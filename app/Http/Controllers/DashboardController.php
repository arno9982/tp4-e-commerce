<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $dateStart = Carbon::now()->subDays(30)->startOfDay();

        $stats      = $this->getStatCards($dateStart);
        $chartData  = $this->getChartData($dateStart);
        $recentOrders     = $this->getRecentOrders();
        $lowStockProducts = $this->getLowStockProducts();
        $topCustomers     = $this->getTopCustomers();

        return view('dashboard', compact(
            'stats',
            'chartData',
            'recentOrders',
            'lowStockProducts',
            'topCustomers'
        ));
    }

    private function getStatCards(Carbon $dateStart): array
    {
        $dateEnd = Carbon::now()->endOfDay();
        $datePreviousStart = $dateStart->copy()->subDays(30);

        // Période actuelle (30 derniers jours)
        $current = Order::whereBetween('created_at', [$dateStart, $dateEnd])
            ->whereIn('status', ['completed', 'paid', 'shipped']);

        $currentRevenue = $current->sum('total_amount');
        $currentOrderCount = $current->count();
        $newClients = User::whereBetween('created_at', [$dateStart, $dateEnd])->count();

        // Période précédente (30 jours avant)
        $previous = Order::whereBetween('created_at', [$datePreviousStart, $dateStart])
            ->whereIn('status', ['completed', 'paid', 'shipped']);

        $previousRevenue = $previous->sum('total_amount');
        $previousOrderCount = $previous->count();
        $previousNewClients = User::whereBetween('created_at', [$datePreviousStart, $dateStart])->count();

        // Calculs
        $averageOrderValue = $currentOrderCount > 0 ? $currentRevenue / $currentOrderCount : 0;
        $previousAverageOrderValue = $previousOrderCount > 0 ? $previousRevenue / $previousOrderCount : 0;

        // Taux de réachat réel (clients ayant au moins 2 commandes dans toute l'histoire)
        $totalClientsWithOrders = User::has('orders')->count();
        $repeatClients = User::has('orders', '>=', 2)->count();
        $repeatRate = $totalClientsWithOrders > 0 ? round($repeatClients / $totalClientsWithOrders * 100, 1) : 0;

        return [
            'revenue'       => (int) $currentRevenue,
            'revenueTrend'  => $this->calculateTrend($currentRevenue, $previousRevenue),

            'clients'       => $newClients,
            'clientsTrend'  => $this->calculateTrend($newClients, $previousNewClients),

            'repeat'        => $repeatRate,
            'repeatTrend'   => 0, // Optionnel : tu peux calculer une évolution plus tard

            'panier'        => (int) round($averageOrderValue),
            'panierTrend'   => $this->calculateTrend($averageOrderValue, $previousAverageOrderValue),
        ];
    }

    private function calculateTrend(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function getChartData(Carbon $dateStart): array
    {
        $data = Order::where('status', 'completed')
            ->whereDate('created_at', '>=', $dateStart)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $values = [];
        $currentDate = $dateStart->copy();

        while ($currentDate->lte(now())) {
            $dateStr = $currentDate->toDateString();
            $labels[] = $currentDate->format('d/m');
            $values[] = $data->get($dateStr, 0);

            $currentDate->addDay();
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    private function getRecentOrders()
    {
        return Order::with(['user'])
            ->latest()
            ->limit(8)
            ->get();
    }

    private function getLowStockProducts()
    {
        return Product::where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity')
            ->limit(15)
            ->get(['id', 'name', 'stock_quantity', 'price']);
    }

    private function getTopCustomers()
    {
        return User::withCount('orders as orders_count')
            ->whereHas('orders')
            ->orderByDesc('orders_count')
            ->limit(5)
            ->get(['id', 'name', 'email']);
    }
}