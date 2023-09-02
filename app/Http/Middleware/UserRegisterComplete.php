<?php

namespace App\Http\Middleware;

use App\Models\UserAddress;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRegisterComplete
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $firstUserAddress = UserAddress::where('user_id', \Auth::id())->first();
        if (\Auth::user()->name != null and \Auth::user()->name != '' and filled($firstUserAddress))
            return $next($request);

        if (!filled($firstUserAddress))
            return response()->json([
                'status' => 401,
                'errors' => '',
                'message' => __('messages.register_at_least_one_address'),
            ], 401);

        return response()->json([
            'status' => 401,
            'errors' => '',
            'message' => __('messages.register_not_complete'),
        ], 401);
    }
}
