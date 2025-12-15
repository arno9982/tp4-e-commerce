<?php
// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Ajouté

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Supposons que votre modèle User a un attribut 'is_admin' ou 'role'
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }
        
        // Rediriger ou retourner une erreur si l'utilisateur n'est pas admin
        abort(403, 'Accès non autorisé. Vous devez être administrateur.'); 
    }
}