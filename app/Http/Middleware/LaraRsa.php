<?php

namespace App\Http\Middleware;

use Closure;

class LaraRsa
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
        $data = $request->input('data');
        if (empty($data)) {
            throw new \Exception('params empty.');
        }
        $result = \LaraRsa\LaraRsa::decrypt($data);
        $request->replace(json_decode($result,true));
        return $next($request);
    }
}
