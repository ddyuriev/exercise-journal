<?php

namespace App\Http\Middleware;

use Closure;
use Detection\MobileDetect;
use Illuminate\Http\Request;

class Device
{

    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('get')) {
            $detect = new MobileDetect();
            $deviceType = $detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer';
            $request->merge(['device_type' => $deviceType]);
        }

        return $next($request);
    }
}
