<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Seffeng\Cryptlib\Crypt;
use Seffeng\LaravelRSA\RSA;

class Secret
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
        $crypt = new Crypt();

//        $detext = RSA::decrypt($entext);

        $response = $next($request);

        return $response;
    }
}
