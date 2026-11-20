<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class RoleMiddleware {
    public function handle(Request $r, Closure $next, string ...$roles) {
        if (!$r->user() || !in_array($r->user()->role, $roles)) abort(403, 'Akses ditolak.');
        return $next($r);
    }
}
