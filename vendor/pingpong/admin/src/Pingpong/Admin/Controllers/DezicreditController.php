<?php namespace Pingpong\Admin\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Pingpong\Admin\Repositories\Users\UserRepository;
use Pingpong\Admin\Validation\User\Create;
use Pingpong\Admin\Validation\User\Update;
use Illuminate\Http\Request;
use DB;
use Auth;
use Datatables;
use DateTime;
use Redirect;

/**
 * @Class: For Driver Activities
 * 
 * dd(DB::getQueryLog()); DB::enableQueryLog();
 *	
 */

class DezicreditController extends BaseController 
{
   
   	/**
	 * Display a listing of users
	 * Author : Vaibhav Bharti
	 * @return Response
	 */

	public function dezicredit(Request $request) {

		die('dezicredit');
	
	}

	

}
