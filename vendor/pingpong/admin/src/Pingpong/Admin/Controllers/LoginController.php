<?php namespace Pingpong\Admin\Controllers;
use DB;
use Illuminate\Http\Request;
use Session;

//session_check();

class LoginController extends BaseController
{

    /**
     * Show login page.
     *
     * @return mixed
     */
    public function index()
    {
        
       // echo "test";
        //exit;
		
        return $this->view('login');
    }
	
	
    /**
     * Login action.
     *
     * @return mixed
     */
    public function store(Request $request)
    {
		
        $credentials = \Input::only('email', 'password');
        $remember = \Input::has('remember');
		
        if (\Auth::attempt($credentials, $remember)) {
           
           $_SESSION['admin'] = \Auth::id();
				
			if($_SESSION['admin'] !=''){
				
				$userIDAndRole = DB::table('dn_users')
						->select(array('dn_users.active','dn_users.id','role_user.role_id'))
						->join('role_user', 'role_user.user_id', '=', 'dn_users.id')		
						->where("dn_users.id",$_SESSION['admin'])						
						->first();
				
				if($userIDAndRole->role_id == 1 || $userIDAndRole->role_id == 2){
					
					if($userIDAndRole->active == 0){
					
						$request->session()->flush();
						
						return \Redirect::to('admin/login')->withFlashMessage("Authentication Failed! Your account has been suspended.")->withFlashType('danger');
						
					}else{
							
							$loggedInStatusUpdate = DB::table('dn_users')
							->where("dn_users.id", $_SESSION['admin'])
							->update(['is_logged' => 'true']);
							
							//GET LOGGEDIN USER PERMISSIONS
							$userPermissions = DB::table('dn_users')
							->select(array('dn_users.id','dn_subadmin_permissions.view_permission','dn_subadmin_permissions.edit_permission','dn_modules.module_slug'))
							->join('dn_subadmin_permissions', 'dn_subadmin_permissions.user_id', '=', 'dn_users.id')		
							->join('dn_modules', 'dn_subadmin_permissions.module_id', '=', 'dn_modules.id')		
							->where("dn_users.id",$_SESSION['admin'])						
							->get();
														
							Session::set('userPermissions', $userPermissions);
							
					} 
					
				}else{	
					$request->session()->flush();
					
					return \Redirect::to('admin/login')->withFlashMessage("Authentication Failed! Permission Denied.")->withFlashType('danger');
				}	
				
			}
		  
		   //\Config::set('permission.module_slag', 'mysinglelue');
		  		   
		   return $this->redirect('home')->withFlashMessage('You have successfully logged in.');
        }

        if (getenv('PINGPONG_ADMIN_TESTING')) {
            return \Redirect::to('admin/login')->withFlashMessage("Authentication Failed! Please try again.")->withFlashType('danger');
        }

        return \Redirect::back()->withFlashMessage("Authentication Failed! Please try again.")->withFlashType('danger');
    }
}
