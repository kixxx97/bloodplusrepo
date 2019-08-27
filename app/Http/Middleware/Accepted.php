<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Accepted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Auth::guard('web_admin')->user()->institute->status == 'active')
        { 
            return $next($request);
        }
        else
        {
            return redirect('/admin/waitingscreen');
        }
    }
}
