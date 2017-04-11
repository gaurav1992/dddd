<?php namespace Pingpong\Admin\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Routing\Controller;
use DB;
use Session;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
class BaseController extends Controller
{

    /**
     * Show view.
     *
     * @param $view
     * @param array $data
     * @param array $mergeData
     * @return mixed
     */
	 
	 /**
     * @param \User $users
     */

    
    public function __construct()
    {
		
		$loggedinUserId  = \Auth::id(); 
		$currentUrl=Route::getCurrentRoute()->getPath();
		$currentAction = \Route::currentRouteAction();
		list($controller, $method) = explode('@', $currentAction);
	
		$controller = preg_replace('/.*\\\/', '', $controller);
		$loggedInUserPermission = Session::get('userPermissions');
			//echo  $method;
			if(!empty($loggedInUserPermission)){
				foreach($loggedInUserPermission as $userPermission){
					
					if($userPermission->module_slug=="passengers" && $controller =='PassengerController' && $userPermission->view_permission==0){
						//echo $controller;
						// echo url('/permissionErr');die;
						header('Location:'.url("/permissionErr"));
						// return redirect()->away(url("/permissionErr"));  
						die;		
					}
					 if($userPermission->module_slug=="driver_applicants" && $controller =='DriverController' && $userPermission->view_permission==0 && in_array($method,['newDriverApplicantList','newDriverAjax','rejectedDriverApplicantList','rejectedDriverAjax'])){	
						
						header('Location:'.url("/permissionErr"));
								
						 
					} 
					if($userPermission->module_slug=="drivers" && $controller =='DriverController' && $userPermission->view_permission==0 && in_array($method,['alldriver','ridedetail','show'])){

						header('Location:'.url("/permissionErr"));
						die;//die;		
					}
					if($userPermission->module_slug=="admin_user" && $controller =='AdminsubadminController' && $userPermission->view_permission==0 && in_array($method,['admin_subadmin_view','addUser','editUser','ajaxIndex','otherAdmins'])){
						
						header('Location:'.url("/permissionErr"));
						// return redirect()->away(url("/permissionErr"));  
						die;		
					}
					if($userPermission->module_slug=="contact_messages" && $controller =='ContactmessagesController' && $userPermission->view_permission==0){
						
						header('Location:'.url("/permissionErr"));
						// return redirect()->away(url("/permissionErr"));  
						die;		
					}
					if($userPermission->module_slug=="notification" && $controller =='NotificationsController' && $userPermission->view_permission==0){
						
						header('Location:'.url("/permissionErr"));
						// return redirect()->away(url("/permissionErr"));  
						die;		
					}
				}	
			}	
         if(!empty(@$loggedinUserId)){
                
                $userIDAndRole = DB::table('dn_users')
                        ->select(array('dn_users.active','dn_users.id','role_user.role_id'))
                        ->join('role_user', 'role_user.user_id', '=', 'dn_users.id')        
                        ->where("dn_users.id",$loggedinUserId)                       
                        ->first();
					if(!empty($userIDAndRole)){
						
						if(!in_array($userIDAndRole->role_id,array(1,2))){
							 \Auth::logout();
							@$request->session()->flush();
							return $this->redirect(url())->withFlashMessage("Permission Denied! You don't have permission to access this page.")->withFlashType('danger');
							 //return Redirect::to(url().'/login')->withInput(Input::except('password'))->withErrors("");
						}
					}
                                           
            }
    
    }
    
    public function view($view, $data = array(), $mergeData = array())
    {
        return View::make('admin::' . $view, $data, $mergeData);
    }

    /**
     * Redirect to a route.
     *
     * @param $route
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return mixed
     */
    public function redirect($route, $parameters = array(), $status = 302, $headers = array())
    {
        return Redirect::route('admin.' . $route, $parameters, $status, $headers);
    }

    /**
     * Get all input data.
     *
     * @return array
     */
    public function inputAll()
    {
        return Input::all();
    }
}
