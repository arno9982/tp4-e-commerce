{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord - EAZYSHOP')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-indigo-50 to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <!-- Titre + Date -->
        <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900">Tableau de bord</h1>
                <p class="mt-2 text-lg text-gray-600">Bienvenue, voici l’état de votre boutique en temps réel</p>
            </div>
            <div class="mt-4 sm:mt-0 text-sm text-gray-500">
                {{ now()->translatedFormat('l d F Y') }} à {{ now()->format('H:i') }}
            </div>
        </div>

        <!-- === CARTES DE STATISTIQUES === -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Chiffre d'affaires -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-6 text-white shadow-lg transform hover:scale-105 hover:shadow-2xl transition-all duration-300">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                <p class="text-emerald-100 text-sm font-medium">Chiffre d'affaires (30j)</p>
                <p class="text-4xl font-bold mt-3">{{ number_format($stats['revenue']) }} FCFA</p>
                <p class="mt-2 text-emerald-100 text-sm">
                    <span class="{{ $stats['revenueTrend'] >= 0 ? 'text-white' : 'text-red-200' }} font-bold">
                        {{ $stats['revenueTrend'] >= 0 ? '↑' : '↓' }} {{ abs($stats['revenueTrend']) }}%
                    </span> vs mois dernier
                </p>
            </div>

            <!-- Nouveaux clients -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 p-6 text-white shadow-lg transform hover:scale-105 hover:shadow-2xl transition-all duration-300">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                <p class="text-indigo-100 text-sm font-medium">Nouveaux clients</p>
                <p class="text-4xl font-bold mt-3">{{ $stats['clients'] }}</p>
                <p class="mt-2 text-indigo-100 text-sm">
                    <span class="{{ $stats['clientsTrend'] >= 0 ? 'text-white' : 'text-red-200' }} font-bold">
                        {{ $stats['clientsTrend'] >= 0 ? '↑' : '↓' }} {{ abs($stats['clientsTrend']) }}%
                    </span>
                </p>
            </div>

            <!-- Taux de réachat -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 p-6 text-white shadow-lg transform hover:scale-105 hover:shadow-2xl transition-all duration-300">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                <p class="text-pink-100 text-sm font-medium">Taux de réachat</p>
                <p class="text-4xl font-bold mt-3">{{ $stats['repeat'] }}%</p>
                <p class="mt-2 text-pink-100 text-sm">
                    <span class="{{ $stats['repeatTrend'] >= 0 ? 'text-white' : 'text-red-200' }} font-bold">
                        {{ $stats['repeatTrend'] >= 0 ? '↑' : '↓' }} {{ abs($stats['repeatTrend']) }}%
                    </span>
                </p>
            </div>

            <!-- Panier moyen -->
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white shadow-lg transform hover:scale-105 hover:shadow-2xl transition-all duration-300">
                <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                <p class="text-amber-100 text-sm font-medium">Panier moyen</p>
                <p class="text-4xl font-bold mt-3">{{ number_format($stats['panier']) }} FCFA</p>
                <p class="mt-2 text-amber-100 text-sm">
                    <span class="{{ $stats['panierTrend'] >= 0 ? 'text-white' : 'text-red-200' }} font-bold">
                        {{ $stats['panierTrend'] >= 0 ? '↑' : '↓' }} {{ abs($stats['panierTrend']) }}%
                    </span>
                </p>
            </div>
        </div>

        <!-- === SECTION GRAPHIQUE + STOCKS === -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Graphique CA -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4">
                    <h3 class="text-xl font-bold">Chiffre d'affaires – 30 derniers jours</h3>
                </div>
                <div class="p-6">
                    <canvas id="revenueChart" class="w-full" height="120"></canvas>
                </div>
            </div>

            <!-- Stocks faibles -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold">Stock faible / rupture</h3>
                    <a href="{{ route('admin.products.create') }}"
                       class="bg-white text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition">
                        + Ajouter
                    </a>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-200">
                            @forelse($lowStockProducts as $product)
                                <tr class="hover:bg-red-50 transition">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                            {{ $product->stock_quantity == 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $product->stock_quantity }} en stock
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Modifier</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                        <p class="text-lg">Tous les stocks sont au top !</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

        <!-- === COMMANDES RÉCENTES + TOP CLIENTS === -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Commandes récentes -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-6 py-4">
                    <h3 class="text-xl font-bold">Dernières commandes</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">N°</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Client</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Articles</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Montant</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($recentOrders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $order->user->name ?? 'Invité' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $order->orderItems->count() }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-indigo-600">
                                        {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' :
                                               ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ ucfirst(trans("order.status.{$order->status}")) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        Aucune commande récente
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top clients -->
            <div class="bg-gradient-to-br from-purple-600 to-indigo-700 text-white rounded-2xl shadow-xl p-6">
                <h3 class="text-2xl font-bold mb-6">Top 5 clients</h3>
                <ol class="space-y-5">
                    @forelse($topCustomers as $i => $customer)
                        <li class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white/20 backdrop-blur rounded-full flex items-center justify-center font-bold text-lg">
                                    {{ $i + 1 }}
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $customer->name }}</p>
                                    <p class="text-sm opacity-90">{{ $customer->orders_count }} commandes</p>
                                </div>
                            </div>
                            <div class="text-3xl font-bold opacity-30">{{ $i + 1 }}</div>
                        </li>
                    @empty
                        <li class="text-center py-8 opacity-75">Aucun client encore</li>
                    @endforelse
                </ol>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Chiffre d\'affaires',
                    data: @json($chartData['values']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => value.toLocaleString() + ' FCFA' }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection