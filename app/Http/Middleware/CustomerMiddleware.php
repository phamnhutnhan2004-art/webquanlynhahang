<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors(['login' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        if (Auth::user()->isLocked()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['login' => 'Tài khoản của bạn đang bị khóa.']);
        }

        if (! Auth::user()->isCustomer() && ! Auth::user()->isAdmin()) {
            abort(403, 'Bạn không có quyền truy cập khu vực khách hàng.');
        }

        return $next($request);
    }
}
