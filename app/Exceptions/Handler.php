<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {

        if ($request->expectsJson()) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            if ($exception->getCode()) {
                $code = $exception->getCode();
            }

            $message = $exception->getMessage();

            if($exception instanceof ValidationException){
                $message = array_values($exception->errors())[0][0];
            }

            return new Response(compact('code','message'),Response::HTTP_OK);

//            if($exception instanceof UnauthorizedHttpException){
//                $message = $exception->getMessage();
//                return new Response(compact('code','message'),Response::HTTP_OK);
//            }
//
//            if($exception instanceof NotFoundHttpException){
//                $message = '方法不存在';
//                return new Response(compact('message'),Response::HTTP_NOT_FOUND);
//            }

//            if($exception instanceof MethodNotAllowedHttpException){
//                return new Response('',compact('message'),Response::HTTP_METHOD_NOT_ALLOWED);
//            }

        }
        return parent::render($request, $exception);
    }

//return config('app.debug') ? [
//'code' => 0,
//'data' => $e->getMessage(),
//'exception' => get_class($e),
//'file' => $e->getFile(),
//'line' => $e->getLine(),
//'trace' => collect($e->getTrace())->map(function ($trace) {
//    return Arr::except($trace, ['args']);
//})->all(),
//] : [
//'code' => 0,
//'data' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
//];
}
