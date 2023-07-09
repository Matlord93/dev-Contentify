<?php

namespace App\Exceptions;

use ErrorException;
use Config;
use Response;
use View;
use MsgException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
	
	    public function render($request, Throwable $e)
    {
        // Laravel wraps any exceptions thrown in views in an error exception so we have to unwrap it
        // @see https://github.com/laravel/ideas/issues/956
        if ($e instanceof ErrorException and
            $e->getPrevious() and $e->getPrevious() instanceof MsgException) {
            /* @var $innerException MsgException */
            $innerException = $e->getPrevious();
            return $innerException->render($request);
        }

        if (! Config::get('app.debug')) { // If we are in debug mode we do not want to override Laravel's error output
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return Response::make(View::make('error_not_found'), 404);
            }

            return Response::make(View::make('error'), 500);
        }

        return parent::render($request, $e);
    }
}
