<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		'Symfony\Component\HttpKernel\Exception\HttpException'
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		return parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e)
	{
	    if ($e instanceof ModelNotFoundException) {
	        $e = new NotFoundHttpException($e->getMessage(), $e);
	    }
		
		 //insert this snippet
		if ($this->isHttpException($e)) 
		{
			$statusCode = $e->getStatusCode();
			
			switch ($statusCode) 
			{
				case '404': return response()->view('errors.404');
				case '403': return response()->view('errors.403');
			}
		}

		
	    if ($e instanceof TokenMismatchException) {            
	        return redirect($request->fullUrl())->withErrors(['token_error' => 'Sorry, your session seems to have expired. Please try again.']);
	    }

	    if ($e instanceof FatalErrorException) {            
	        return redirect('/');
	    }

	    if ($e instanceof NotFoundHttpException) {            
	        return redirect('/');
	    }

	    return parent::render($request, $e);
	}

}
