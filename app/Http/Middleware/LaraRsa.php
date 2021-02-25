<?php

namespace App\Http\Middleware;

use Closure;

class LaraRsa
{

    protected $timeOut = 30;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = $request->input('data',null);
        $result = \LaraRsa\LaraRsa::decrypt($data);
        if (empty($result)){
            throw new \Exception('params empty.');
        }
        $result = json_decode($result,true);
        if (isset($result['millis']) && time() - $result['millis'] >= $this->timeOut){
            throw new \Exception('timeout error.');
        }
        $request->replace($result);
        return $next($request);
    }
}
