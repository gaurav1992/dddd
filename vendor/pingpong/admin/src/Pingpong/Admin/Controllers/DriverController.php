<?php namespace Pingpong\Admin\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Pingpong\Admin\Repositories\Users\UserRepository;
use Pingpong\Admin\Validation\User\Create;
use Pingpong\Admin\Validation\User\Update;
use Illuminate\Http\Request;
use Session;
use DB;
use Auth;
use Datatables;
use DateTime;
use Redirect;
use Mail;
/**
 * @Class: For Driver Activities
 * 
 * dd(DB::getQueryLog()); DB::enableQueryLog();
 *	
 */
session_check();

class DriverController extends BaseController 
{
   /**
	* @var \User
	*/

	protected $users;	

   	/**
	 * Display a listing of users
	 * Author : Vaibhav Bharti
	 * @return Response
	 */

	public function applicantlist(Request $request){

		$data = $request->all();

		//DB::enableQueryLog();

		//get all new driver applicant users
		$newApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4') 

						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})

						->paginate(config('admin.user.perpage'));

		//dd(DB::getQueryLog()); 

		$countNewApplicants = count($newApplicants);
		
		//get all driver users
		$rejectedApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
			
						//->where('role_id','4') 

						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 2 );
						})

						->paginate(config('admin.user.perpage'));
		
		$countRejectedApplicants = count($rejectedApplicants);
		

		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})

						->paginate(config('admin.user.perpage'));
						//echo "<pre>"; print_r($newApplicants); echo "</pre>"; die('newApplicants');

		$states = DB::table('dn_states')->get();
		
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
		}


		$citys = DB::table('dn_users')
				->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.city) as no_of_users')))
				->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
				
				//->where('dn_users.city','!=','')

				->where( function ($query) {
					$query->where( 'dn_users.city','!=','' );
                  	//->where( 'role_id', '4' );
				})
				
				->groupBy('dn_users.city')
				->orderBy('no_of_users')
				->get();

		//$this->preDie( $citys ); @Harish#1

		//DB::enableQueryLog();

		$n=count($citys)-1;
		if($n==0)
		{
			
			$citiesCount=array('least'=>$citys,'most'=>$citys[$n]); 
		}
		@$citiesCount=array('least'=>$citys[0],'most'=>$citys[$n]); 
		return $this->view('driver.driverApplicantList', compact( 'newApplicants', 'rejectedApplicants', 'users','citiesCount','states'));
	}

	/**
	 * @FUNCTION FOR AJAX CALL ON INDEX for deriver list
	 * @Author : Vaibhav Bharti
	 * @Params : $request
	 **/

 	public function ajaxIndex(Request $request) 
 	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$searchString=$data['search']['value'];
		$startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','5'=>'email','6'=>'contact_number','8'=>'is_logged');
		//print_r($data['order'][0]); die();
		
		$field='id';
		$direction='ASC';
		
		/* code for order by data of user*/
		if(!empty($data['order'][0])) 
		{
			foreach($orderfields as $key=>$orderfield)
			{
				if( $key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		/* code for searching of  user*/
		$sql = 'SELECT id FROM dn_users WHERE 1=1 ';

		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
		}
		if($data['state']!='')
		{
			$state = $data['state'];
			$sql .=" AND  state= '$state'";
		}
		if($data['city']!='')
		{
			 $city = $data['city'];
			 $sql .=" AND  city= '$city'";
		}
		if($data['state']!='' && $data['city']!='')
		{
			$state = $data['state'];
			$city = $data['city'];
			$sql .="AND  state= '$state' AND  city= '$city'";
		}
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			
			$sql .=" AND  (first_name LIKE '$search' or last_name LIKE '$search' or email LIKE '$search') ";
		}
		
		$sql .= " order by ".$field." ".$direction;
		$usersIds=DB::select(DB::raw($sql));
		if(!empty($usersIds))
		{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
		}
		$users = array();
		$totalRecords = 0;

		if(!empty($usersList))
		{
			/* code for fetching data from dn_users table */

			$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->take($limit)->offset($offset) ->orderBy($field,$direction)->get();
			
			$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->paginate(config('admin.user.perpage'));
		}
		$Data="";
		foreach($users as $user)
		{
			//echo $user->active;
			$first_name =$user->first_name;
			$last_name =$user->last_name;
			$email=$user->email;
			$phone=$user->contact_number;
			$state=$user->state;
			$city=$user->city;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			
			//$view="<span class='label-success label '><a href='driver-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
			$view="<span class='label-success label '><a href='/deziNow/admin/driver/new-applicant-detail/".$user->id."'> View </a></span>";

			/*if($user->active==1) {
				$active='Active';
				$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
				// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
				
			} else{
				$active='Suspended';
				$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Active</a></span>&nbsp;|&nbsp;".$view;
			}*/

			$active='Disapprove';
			$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Disapprove</a></span>&nbsp;|&nbsp;".$view;
			 
			
			if($user->is_logged=='true') {
				$is_logged='Yes';
			}else{
				$is_logged='No';
			} 
			if(empty($first_name))
			{
				$first_name="N/A";
			}
			if(empty($last_name))
			{
				$last_name="N/A";
			}
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($phone))
			{
				$phone="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($city))
			{
				$city="N/A";
			}
			$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data))
		{
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.count($totalRecords).',
			  "recordsFiltered":'.count($totalRecords).',
			  "data": ['.$newData.']
			}';
		} 
		else 
		{
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
					
	}


	/**
	 * Display a listing of New driver Users
	 * Author : Harish Chauhan
	 * @return Response
	 */

	public function newDriverApplicantList(Request $request){

		$data = $request->all();
		//DB::enableQueryLog();
		//get all new driver applicant users
		$newApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})

						->paginate(config('admin.user.perpage'));

		//dd(DB::getQueryLog()); 

		$countNewApplicants = count($newApplicants);
		
		//get all driver users
		$rejectedApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
			
						//->where('role_id','4') 

						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 2 ); // is_driver_approved = 
						})

						->paginate(config('admin.user.perpage'));
		
		$countRejectedApplicants = count($rejectedApplicants);
		

		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
							->where( 'dn_users.active', 1 )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})
						->paginate(config('admin.user.perpage'));
						//echo "<pre>"; print_r($newApplicants); echo "</pre>"; die('newApplicants');

		$states = DB::table('dn_states')->get();
		
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
		}


		$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.city) as no_of_users')))
					->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					
					//->where('dn_users.city','!=','')

					->where( function ($query) {
						$query->where( 'dn_users.city','!=','' );
                      	//->where( 'role_id', '4' );
					})
					
					->groupBy('dn_users.city')
					->orderBy('no_of_users')
					->get();

		//$this->preDie( $citys ); @Harish#1

		//DB::enableQueryLog();


		$n=count($citys)-1;
		if($n==0)
		{
			
			$citiesCount=array('least'=>$citys,'most'=>$citys[$n]); 
		}
		@$citiesCount=array('least'=>$citys[0],'most'=>$citys[$n]); 
		return $this->view('driver.newDriverApplicantList', compact( 'newApplicants', 'rejectedApplicants', 'users','citiesCount','states'));
	}

	/**
	 * @FUNCTION FOR AJAX CALL FOR NEW DRIVER AJAX
	 * @Author : Harish Chander 
	 * @Params : $request
	 **/

 	public function newDriverAjax(Request $request) 
 	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$searchString=$data['search']['value'];
		$startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','5'=>'email','6'=>'contact_number','8'=>'is_logged');
		//print_r($data['order'][0]); die();
		
		$field='id';
		$direction='ASC';
		
		/* code for order by data of user*/
		if(!empty($data['order'][0])) 
		{
			foreach($orderfields as $key=>$orderfield)
			{
				if( $key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		/* code for searching of  user*/
		$sql = 'SELECT id FROM dn_users WHERE 1=1 ';

		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
		}
		if($data['state']!='')
		{
			$state = $data['state'];
			$sql .=" AND  state= '$state'";
		}
		if($data['city']!='')
		{
			 $city = $data['city'];
			 $sql .=" AND  city= '$city'";
		}
		if($data['state']!='' && $data['city']!='')
		{
			$state = $data['state'];
			$city = $data['city'];
			$sql .="AND  state= '$state' AND  city= '$city'";
		}
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sql .=" AND  (first_name LIKE '$search' or last_name LIKE '$search' or full_name LIKE '$search' or email LIKE '$search'  or contact_number LIKE '$search') ";
		}
		
		$sql .= " order by ".$field." ".$direction;
		$usersIds=DB::select(DB::raw($sql));
		if(!empty($usersIds))
		{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
		}
		$users = array();
		$totalRecords = 0;

		if(!empty($usersList))
		{
			/* code for fetching data from dn_users table */

			$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						//->where('role_id','4')
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->take($limit)->offset($offset) ->orderBy($field,$direction)->get();
						//print_r($users);
			
			$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->orderBy($field,$direction)->get();
		}
		$Data="";
		foreach($users as $user)
		{
			//echo $user->active;
			$first_name =$user->first_name;
			$last_name =$user->last_name;
			$email=$user->email;
			$phone=$user->contact_number;
			$state=$user->state;
			$city=$user->city;
		

			$active 	= '';
			$view 		= "<span class='label-info label'><a href='".route("driverNewApplicantDetail",$user->id)."'> View </a></span>";	
			$actionDEF = 'N/A';
			$action = '';
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');
			//echo "<pre>"; print_r($loggedInUserPermission); die;
			foreach($loggedInUserPermission as $k=>$allModule){
				$allMod[]= $allModule->module_slug;
				$allModPer[$allModule->module_slug]= $allModule;
			}
			if(empty($loggedInUserPermission)){
				
				$action 	= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data-action='btndriver_approve' data-userid=".$user->id." >Approve</a></span></br>";
				$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_disapprove' data-action='btndriver_disapprove' data-userid=".$user->id." >Disapprove</a></span></br>";
				$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_dlt' data-action='btndriver_dlt' data-userid=".$user->id." >Delete</a></span></br>".$view;

			}elseif(!empty($loggedInUserPermission)){
			foreach($loggedInUserPermission as $userPermission){
				
				if($userPermission->module_slug=="driver_applicants" && $userPermission->edit_permission==1){
								$action 	= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data-action='btndriver_approve' data-userid=".$user->id." >Approve</a></span></br>";
								$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_disapprove' data-action='btndriver_disapprove' data-userid=".$user->id." >Disapprove</a></span></br>";
								$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_dlt' data-action='btndriver_dlt' data-userid=".$user->id." >Delete</a></span></br>$view";
							
						
						}else
					
					/*Inner condition end*/	
					if($userPermission->module_slug=="driver_applicants" && $userPermission->view_permission==1){
					$action .= $view ;	
						
					}
				}
			} else{
				$action = "N/A";
			} 
			 
			//Subadmin Permission Code End
			
			
			
			/*if($user->active==1) {
				$active='Active';
				$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
				// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
				
			}else{
				$active='Suspended';
				$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Active</a></span>&nbsp;|&nbsp;".$view;
			}*/ 
			
			if($user->is_logged=='true') {
				$is_logged='Yes';
			}else{
				$is_logged='No';
			} 
			if(empty($first_name))
			{
				$first_name="N/A";
			}
			if(empty($last_name))
			{
				$last_name="N/A";
			}
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($phone))
			{
				$phone="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($city))
			{
				$city="N/A";
			}
			$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data))
		{
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.count($totalRecords).',
			  "recordsFiltered":'.count($totalRecords).',
			  "data": ['.$newData.']
			}';
		} 
		else 
		{
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
					
	}

	/**
	 * Display all rejected applicant drivers
	 * Author : Vaibhav Bharti
	 * @return Response
	 */

	public function rejectedDriverApplicantList(Request $request) {

		$data = $request->all();

		//DB::enableQueryLog();

		//get all new driver applicant users
		$newApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
			
						//->where('role_id','4') 

						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})

						->paginate(config('admin.user.perpage'));

		//dd(DB::getQueryLog()); 

		$countNewApplicants = count($newApplicants);
		
		//get all driver users
		$rejectedApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
			
						//->where('role_id','4') 

						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 ) 
	                      	->where( 'dn_users.is_driver_approved', 2 );
						})
						
						->paginate(config('admin.user.perpage'));
		
		$countRejectedApplicants = count($rejectedApplicants);
		

		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', 1 ) 
	                      	->where( 'dn_users.is_driver_approved', 2 );
						})
						->paginate(config('admin.user.perpage'));
						//echo "<pre>"; print_r($newApplicants); echo "</pre>"; die('newApplicants');

		$states = DB::table('dn_states')->get();
		
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
		}

		$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.id) as no_of_users,dn_users.id as pid')))
					->leftjoin('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->leftjoin('role_user', 'dn_users.id', '=', 'role_user.user_id')
					->where('dn_users.city','!=',0)
					->where('role_user.role_id',4)
					->where('dn_users.city','!=','')
					->groupBy('dn_users.city')
					->orderBy('no_of_users')
					->get();
					
		//print_r($citys);die;
		
		if(@$citys and !empty($citys)){
		$n=count($citys)-1;
		if($n>=0)
		{
			$citiesCount=array('least'=>@$citys[0],'most'=>@$citys[$n]); }
		}else{
			$cty=(object)array('city'=>'N/A','no_of_users'=>'N/A');
			@$citiesCount=array('least'=>@$cty,'most'=>$cty);}
		return $this->view('driver.rejectedDriverApplicantList', compact( 'newApplicants', 'rejectedApplicants', 'users','citiesCount','states'));
	}
 	

    /**
	 * @FUNCTION FOR AJAX CALL ON INDEX
	 * @Author : Vaibhav Bharti
	 * @Params : $request
	 **/

 	public function rejectedDriverAjax(Request $request) 
 	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$searchString=$data['search']['value'];
		$startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','5'=>'email','6'=>'contact_number','8'=>'is_logged');
		//print_r($data['order'][0]); die();
		
		$field='id';
		$direction='ASC';
		
		/* code for order by data of user*/
		if(!empty($data['order'][0])) 
		{
			foreach($orderfields as $key=>$orderfield)
			{
				if( $key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		/* code for searching of  user*/
		$sql = 'SELECT id FROM dn_users WHERE 1=1 ';

		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
		}
		if($data['state']!='')
		{
			$state = $data['state'];
			$sql .=" AND  state= '$state'";
		}
		if($data['city']!='')
		{
			 $city = $data['city'];
			 $sql .=" AND  city= '$city'";
		}
		if($data['state']!='' && $data['city']!='')
		{
			$state = $data['state'];
			$city = $data['city'];
			$sql .="AND  state= '$state' AND  city= '$city'";
		}
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sql .=" AND  (first_name LIKE '$search' or last_name LIKE '$search' or full_name LIKE '$search' or email LIKE '$search' or contact_number LIKE '$search') ";
		}
		
		$sql .= " order by ".$field." ".$direction;
		$usersIds=DB::select(DB::raw($sql));
		if(!empty($usersIds))
		{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
		}
		$users = array();
		$totalRecords = 0;

		if(!empty($usersList))
		{
			/* code for fetching data from dn_users table */

			$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','3')
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', [1,2] )
	                      	->where( 'dn_users.is_driver_approved', 2 );
						})
						->whereIn('role_user.user_id',$usersList)
						->paginate(config('admin.user.perpage'));
						//print_r($users);
			
			$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						//->where('role_id','3')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.become_driver_request', [1,2] )
	                      	->where( 'dn_users.is_driver_approved', 2 );
						})
						->whereIn('role_user.user_id',$usersList)
						->paginate(config('admin.user.perpage'));
		}
		$Data="";
		foreach($users as $user)
		{
			//echo $user->active;
			$first_name =$user->first_name;
			$last_name =$user->last_name;
			$email=$user->email;
			$phone=$user->contact_number;
			$state=$user->state;
			$city=$user->city;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			
			//$view="<span class='label-success label '><a href='driver-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
			
			$active = '';
			$view = "<span class='label-success label'><a href=".route("driverRejectedApplicantDetail",$user->id)."> View </a></span>";
			
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');

			if(empty($loggedInUserPermission)){
				$action = "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data-action='driver_approve' data-userid=".$user->id." >Approve</a></span>&nbsp;|&nbsp;".$view;

			}elseif(!empty($loggedInUserPermission)){
				
				foreach($loggedInUserPermission as $userPermission){
					if($userPermission->module_slug=="driver_applicants" && $userPermission->edit_permission==1){
						$action = "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data-action='driver_approve' data-userid=".$user->id." >Approve</a></span>&nbsp;|&nbsp;".$view;
					}else
					if($userPermission->module_slug=="driver_applicants" && $userPermission->view_permission==1){
						$action = "".$view;
					}
					
				} 
			}else{
				$action = "N/A";
			} 
			//Subadmin Permission Code End

			/*if($user->active==1) {
				$active='Active';
				$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
				// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
			}else{
				$active='Suspended';
				$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Active</a></span>&nbsp;|&nbsp;".$view;
			} */
			

			if($user->is_logged=='true') {
				$is_logged='Yes';
			}else{
				$is_logged='No';
			} 
			if(empty($first_name))
			{
				$first_name="N/A";
			}
			if(empty($last_name))
			{
				$last_name="N/A";
			}
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($phone))
			{
				$phone="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($city))
			{
				$city="N/A";
			}
			$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data))
		{
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.count($totalRecords).',
			  "recordsFiltered":'.count($totalRecords).',
			  "data": ['.$newData.']
			}';
		} 
		else 
		{
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
					
	}

	/**
	 * @FUNCTION FOR ALL DRIVERS
	 * @Author : Gaurav Kumar
	 * @Params : $request is used to handle all Http request
	**/
	public function alldriver(Request $request) {

		$data = $request->all();

		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '4' )
							->where( 'dn_users.active', 1 )
	                      	->where( 'dn_users.become_driver_request', 1 )
	                      	->where( 'dn_users.is_driver_approved', 1 );
						})
						->get();
						//echo "<pre>"; print_r($users); echo "</pre>";exit;
		
		$states = DB::table('dn_states')->get();
		
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
		}
		$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.id) as no_of_users,dn_users.id as pid')))
					->leftjoin('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->leftjoin('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->leftjoin('role_user', 'dn_users.id', '=', 'role_user.user_id')
					->where('dn_users.city','!=',0)
					->where('role_user.role_id',4)
					->where('dn_users.city','!=','')
					->groupBy('dn_cities.id')
					->orderBy('no_of_users')
					->get();


		
		if(@$citys and !empty($citys)){
		$n=count($citys)-1;
		if($n>=0)
		{
			$citiesCount=array('least'=>@$citys[0],'most'=>@$citys[$n]); }
		}else{
			$cty=(object)array('city'=>'N/A','no_of_users'=>'N/A','state_code'=>'N/A');
			@$citiesCount=array('least'=>@$cty,'most'=>$cty);}
			 //print_r($citiesCount);die;
		return $this->view('driver.driverlist', compact( 'users','citiesCount','states'));
	}

	public function alldriverlist(Request $request){
		/* initializing the variables */
			$data = $request->all();
			$limit = 10;
			$draw = $data['draw'];
			$offset = $data['start'];
			$searchString=$data['search']['value'];
		    $startDate=$data['startDate'];
			$endDate=$data['endDate'];
			$orderfields=array('0'=>'dn_users.id','1'=>'dn_users.unique_code','2'=>'dn_users.first_name','3'=>'dn_users.last_name','4'=>'dn_users.created_at','5'=>'dn_users.lstRd','6'=>'dn_users.email','7'=>'dn_users.contact_number','8'=>'dn_users.active','9'=>'dn_users.anniversary','10'=>'dn_users.zip_code','11'=>'dn_users.dob','12'=>'dn_users_data.referral_code','13'=>'dn_users_data.license_number','14'=>'dn_driver_requests.licence_expiration','15'=>'dn_driver_requests.insurance_expiration','16'=>'dn_users.driver_approved_on','17'=>'dn_users_data.transmission','18'=>'dn_users.status');
			//print_r($data['order'][0]);
			$field='id';
			$direction='ASC';
			/* code for order by data of user*/
			if(!empty($data['order'][0])){
				foreach($orderfields as $key=>$orderfield){
					if($key==$data['order'][0]['column'])
					{
						$field=$orderfield;
						$direction=$data['order'][0]['dir'];
					}
				}
			}
			
			/* code for searching of  user*/
			$sql = 'SELECT dn_users.id FROM dn_users left join dn_driver_requests on dn_driver_requests.user_id= dn_users.id left join dn_users_data on dn_users_data.user_id=dn_users.id WHERE 1=1 ';
				
			// $sql = 'LEFT JOIN dn_rides ON dn_rides.driver_id = dn_users.id ';
			if(!empty($startDate) &&  !empty($endDate))
			{
				$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
				$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				$sql .=" AND  dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
			}
			if($data['state']!='')
			{
				$state = $data['state'];
				$sql .=" AND  dn_users.state= '$state'";
			}
			if($data['city']!='')
			{
				 $city = $data['city'];
				 $sql .=" AND  dn_users.city= '$city'";
			}
			if($data['state']!='' && $data['city']!='')
			{
				$state = $data['state'];
				$city = $data['city'];
				$sql .="AND  dn_users.state= '$state' AND  dn_users.city= '$city'";
			}
			if(@$searchString!='') 
			{	
				$search = "%$searchString%";
				$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_users.full_name LIKE '$search'  or dn_users.contact_number LIKE '$search' or dn_users.unique_code LIKE '$search' or dn_users.email LIKE '$search') ";
			}
			
			$sql .= " order by ".$field." ".$direction;
			//echo $sql;die;
			$usersIds=DB::select(DB::raw($sql));
			if(!empty($usersIds))
			{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
			}
			$users = array();
			$totalRecords = 0;
			if(!empty($usersList))
			{
				/* code for fetching data from dn_users table */
				$users = DB::table('role_user')
							->select(array('dn_users.*','dn_states.state','dn_cities.city','dn_driver_requests.licence_expiration','dn_driver_requests.insurance_expiration','dn_driver_requests.driver_records','dn_users_data.license_number','dn_users_data.referral_code','dn_users_data.transmission'))
							->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
							->leftjoin('dn_driver_requests', 'dn_driver_requests.user_id', '=', 'dn_users.id')	
							->leftjoin('dn_users_data', 'dn_users_data.user_id', '=', 'dn_users.id')	
							->leftjoin('dn_states', 'dn_users.state', '=', 'dn_states.state_code')	
							->leftjoin('dn_cities', 'dn_users.city', '=', 'dn_cities.id')	
							//->leftjoin('dn_rides', 'dn_rides.driver_id', '=', 'dn_users.id')					
							->where('role_id','4')
							->where('dn_users.is_driver_approved','1')
							->whereIn('role_user.user_id',$usersList)
							->take($limit)->offset($offset) ->orderBy($field,$direction)->get();
							//print_r($users);die;
				$totalRecords = DB::table('role_user')
							->select(array('dn_users.*'))
							->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
							->leftjoin('dn_driver_requests', 'dn_driver_requests.user_id', '=', 'dn_users.id')	
							->leftjoin('dn_users_data', 'dn_users_data.user_id', '=', 'dn_users.id')	
							->where('role_id','4')
							->where('dn_users.is_driver_approved','1')
							->whereIn('role_user.user_id',$usersList)
							->paginate(config('admin.user.perpage'));
			}
			$Data="";
			foreach($users as $user)
			{
				//echo $user->id."</br>";
				$first_name =$user->first_name;
				$action='';
				$driverId =$user->id;
				$last_name =$user->last_name;
				$email=$user->email;
				$phone=$user->contact_number;
				$state=$user->state;
				$dob=$user->dob;
				$zip_code=$user->zip_code;
				$anniversary=$user->anniversary;
				$referral_code=$user->referral_code;
				$license_number=$user->license_number;
				$driver_records=$user->driver_records;
				$licence_expiration=$user->licence_expiration;
				$insurance_expiration=$user->insurance_expiration;
				$driver_approved_on=$user->driver_approved_on;
				$transmission=$user->transmission;
				if(empty($license_number))
				{
					$license_number="N/A";
				}
				if(empty($transmission))
				{
					$transmission="N/A";
				}
				if(empty($licence_expiration))
				{
					$licence_expiration="N/A";
				}else{
					$licence_expiration=date('m/d/Y',strtotime($licence_expiration));
				}
				if(empty($insurance_expiration))
				{
					$insurance_expiration="N/A";
				}else{
					$insurance_expiration=date('m/d/Y',strtotime($insurance_expiration));
				}
				if(empty($driver_approved_on))
				{
					$driver_approved_on="N/A";
				}else{
					$driver_approved_on=date('m/d/Y',strtotime($driver_approved_on));
				}
				if(empty($dob))
				{
					$dob="N/A";
					$age="N/A";
				}else{
					$age=$this->ageCalculator($dob);
					$dob=date('m/d/Y',strtotime($dob));
				}
				if(empty($anniversary))
				{
					$anniversary="N/A";
				}else{
					$anniversary=date('m/d/Y',strtotime($anniversary));
				}
				if(!empty(@$user->driver_records)){
			    	$driver_records=$user->driver_records;
					$driver_records=json_decode($driver_records);
					$recordData='<table><thead><tr><th>Sr. No.</th> <th>Question</th> <th>Answer</th></tr>';
					
					foreach($driver_records as $key=> $records)
					{
						if(@$records->answer=='1')
						{
							$answer="Yes";
						}elseif(@$records->answer=='0')
						{
							$answer="No";
						}else{
							$answer=trim($records->answer);
						}
						$recordData .= "<tr style='text-align:left;'><td>".trim(++$key)."</td><td><b>".trim($records->question)." </b></td><td>".trim($answer)."</td></tr>";
						
					}
					$recordData.="</table>";
			   }else{$recordData="N/A";}
				
				$city=$user->city;
				$lastRide= DB::table('dn_rides')->select('dn_rides.created_at')->where('driver_id',$driverId)->orderby('created_at','desc')->first();
				if(@$lastRide && !empty($lastRide))
				{
					$lstRd=$lastRide->created_at;
				}else{
					$lstRd="N/A";
				}
				//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
				$view ="<span class='label-success label '><a href='driver-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
				$Edit ="<span class='label-info label '><a href='driver-edit/".base64_encode(convert_uuencode($user->id))."'> Edit </a></span>";
				$delete="<span class='btn btn-danger width-btn driver_dlt' data-action='btndriver_dlt' data-userid=".$user->id.">Delete </span>";
				$revoke="<span class=''><a href='javascript:void(0);' class='btn btn-primary width-btn driver_revoke' data-action= 'driver_revoke' data-userid=".$user->id."> Revoke</a></span>";
				
				
				//Subadmin Permission Code Start
				$loggedInUserPermission = Session::get('userPermissions');

				if(empty($loggedInUserPermission)){
					if($user->active==1) {
					$active='Active';
					$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn suspendDriver' data-action = 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$revoke."&nbsp;|&nbsp;".$delete."&nbsp|&nbsp;".$Edit."&nbsp;| ";
					// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
					
				}else{
					$active='Suspended';
					$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn activateDriver' data-action= 'passenger_Active' data-userid=".$user->id." >Activate</a></span>&nbsp;|&nbsp;".$revoke."&nbsp;|&nbsp;".$delete."&nbsp|&nbsp;".$Edit."&nbsp;| ";
				} 
				
				
				}elseif(!empty($loggedInUserPermission)){
					
				foreach($loggedInUserPermission as $userPermission){
				if($userPermission->module_slug=="drivers" && $userPermission->edit_permission==1){
					if($user->active==1) {
					$active='Active';
					$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$revoke."&nbsp;|&nbsp;".$delete."&nbsp|&nbsp;";
					// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";	
					}else{
						$active='Suspended';
						$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Activate</a></span>&nbsp;|&nbsp;".$revoke."&nbsp;|&nbsp;".$delete."&nbsp|&nbsp;";
					}
				}else{
					
					if($user->active==1) {
					$active='Active';				
					}else{
						$active='Suspended';	
					}
					
				}
				} 
				}  
				//Subadmin Permission Code End
				//$action.="&nbsp;".$view."&nbsp";
				if($user->is_logged=='true') {
					$is_logged='Yes';
				}else{
					$is_logged='No';
				} 
				if(empty($first_name))
				{
					$first_name="N/A";
				}
				if(empty($last_name))
				{
					$last_name="N/A";
				}
				if(empty($email))
				{
					$email="N/A";
				}
				if(empty($phone))
				{
					$phone="N/A";
				}
				if(empty($state))
				{
					$state="N/A";
				}
				if(empty($city))
				{
					$city="N/A";
				}
				if(empty($zip_code))
				{
					$zip_code="N/A";
				}
				
				
				if(empty($referral_code))
				{
					$referral_code="N/A";
				}		
				$Data['data'][]=array(++$offset,$user->unique_code,$first_name,$last_name,date('m/d/Y', strtotime($user->created_at)),$lstRd,$email,$phone,$dob,$city, $state,$anniversary,$zip_code,$age,$referral_code,$license_number,$licence_expiration,$insurance_expiration,$driver_approved_on,$transmission,$active,$action.' '.$view,$recordData);
				
			}
			
			if(!empty($Data)){	
			$Data['draw']=$draw;
			$Data['recordsTotal']=count($totalRecords);
			$Data['recordsFiltered']=count($totalRecords);
			return(json_encode($Data));
			} else {
				return '{
				  "draw": '.$draw.',
				  "recordsTotal": 0,
				  "recordsFiltered":0,
				  "data": []
				}';
			}
	}
	
	public function editDriver($id=null,Request $request){
		$input=$request->all();
		
		if(!empty($input)){
			
					$id = $input['id'];
					$driver_profile_pic = Input::file('profile_pic');
					$anniversary 	= Input::get('anniversary');
					$dob 	= Input::get('dob');
					if($anniversary !=''){
					}else{
						$anniversary = '';
					}
					$ssn	= Input::get('ssn');
					$email	= Input::get('email');
					$emailCheck = DB::table('dn_users')->select('*')
					->where('email', $email)
					->Where('id', '!=' ,$id)->first();
					if(!empty($emailCheck)){
						 Session::flash('error', 'Error!! An account with this email already exists.');
						 return Redirect::back()->withInput(Input::all());
					}
					
					
					$license_number = Input::get('license_number');
                    $license_exp_input 	= Input::get('licence_exp');
                    $insurance_exp_input 	= Input::get('insurance_exp');
					$license_exp = date("Y-m-d", strtotime($license_exp_input));
					$insurance_exp = date("Y-m-d", strtotime($insurance_exp_input));
				    $address_1 	= Input::get('address_1');
					$address_2 	= Input::get('address_2');
					$city 	= Input::get('city');
					$state 	= Input::get('state');
					$zip_code 	= Input::get('zip_code');
					$full_name = Input::get('first_name')." ".Input::get('last_name');
					$first_name = Input::get('first_name');
					$last_name = Input::get('last_name'); 
					//echo  $license_exp; die;
					$car_transmission = Input::get('car_transmission');
					
					//$navigation = Input::get('navigation');

					$license_verification = Input::file('license_verification');
					$proof_of_insurance = Input::file('proof_of_insurance');
					$LFLAG=0;$IFLAG=0;
					if(!empty($license_verification)){
						$LFLAG=1;
					}
					if(!empty($proof_of_insurance)){
						$IFLAG=1;
					}
					$driver_detail = DB::table('dn_driver_requests')->select('*')->where('user_id', $id)->first();
					$driver_license_detail = DB::table('dn_users_data')->select('*')->where('user_id', $id)->first();	
					$userDeviceCheck = DB::table('dn_users')->select(array('id', 'first_name','email', 'contact_number', 'is_social', 'profile_pic','is_driver_approved'))->where('id', $id)->first();
					$is_driver_approved = $userDeviceCheck->is_driver_approved;
						/*--code start for change date formet --*/
							$origionalanniversary = $anniversary;
							$newanniversary = date("Y-m-d", strtotime($origionalanniversary));
							$dob = date("Y-m-d", strtotime($dob));
				        	/*--code end for change date formet --*/
						    if($driver_license_detail){
								/*--upload driver profile image--*/
							$destinationPath = 'uploads/profile-img/';
							//die('4');
							//echo $driver_profile_pic->getClientOriginalName();die;
							if(!empty($driver_profile_pic)){ $filename = md5(microtime() . $driver_profile_pic->getClientOriginalName()) . "." . $driver_profile_pic->getClientOriginalExtension();
							Input::file('profile_pic')->move($destinationPath,$filename);
						    $driver_profile_pic_path = $destinationPath . $filename;
							DB::table('dn_users_data')->where(['user_id' => $id])->update(['driver_profile_pic'=>$driver_profile_pic_path]);
							}
							//die('5');
						    /*--//upload driver profile image--*/
			                	DB::table('dn_users_data')->where(['user_id' => $id])->update(['license_number' => $license_number,'transmission' => $car_transmission,'ssn'=>$ssn]);
			                }else{
			                	$insertGetId = DB::table('dn_users_data')->insertGetId(['user_id' => $id,'license_number' => $license_number,'transmission' => $car_transmission,'ssn'=>$ssn]);
			                }
							
						    DB::table('dn_users')->where(['id' => $id])->update(['first_name'=>$first_name,'last_name'=>$last_name,'full_name' => $full_name,'anniversary' => $newanniversary,'address_1' => $address_1,'address_2'  => $address_2,'city' => $city,'state'  => $state,'zip_code' =>$zip_code,'email'=>$email,'become_driver_request'=>2]); 

							if($driver_detail){
				            	if ($license_verification) {
					                $destinationPath = 'uploads/drivers-documents/';
					                $filename = md5(microtime() . $license_verification->getClientOriginalName()) . "." . $license_verification->getClientOriginalExtension();
					                Input::file('license_verification')->move($destinationPath, $filename);
					                $license_verification_path = $destinationPath . $filename;
			        					DB::table('dn_driver_requests')->where(['user_id' => $id])->update(['license_verification' => $license_verification_path]);
					            }
								
					            if ($proof_of_insurance) {
					                $destinationPath = 'uploads/drivers-documents/';
					                $filename = md5(microtime() . $proof_of_insurance->getClientOriginalName()) . "." . $proof_of_insurance->getClientOriginalExtension();
					                Input::file('proof_of_insurance')->move($destinationPath, $filename);
					                $proof_of_insurance_path = $destinationPath . $filename;
			        					
			        					DB::table('dn_driver_requests')
			        					->where(['user_id' => $id])
			        					->update(['proof_of_insurance' => $proof_of_insurance_path]);
					            }
					            DB::table('dn_driver_requests')->where(['user_id' => $id])->update(['car_transmission' => $car_transmission,'licence_expiration' => $license_exp ,'insurance_expiration'=> $insurance_exp]);

				            } else {
				           			$insertGetId = DB::table('dn_driver_requests')->insertGetId(['car_transmission' => $car_transmission,'user_id' => $id,'licence_expiration' => $license_exp ,'insurance_expiration'=> $insurance_exp]);
						                    if ($insertGetId) {
						                        if ($license_verification) {
						                            $destinationPath = 'uploads/drivers-documents/';
						                            $filename = md5(microtime() . $license_verification->getClientOriginalName()) . "." . $license_verification->getClientOriginalExtension();
						                            Input::file('license_verification')->move($destinationPath, $filename);
						                            $license_verification_path = $destinationPath . $filename;
						                          DB::table('dn_driver_requests')->where(['user_id' => $id])->update(['license_verification' => $license_verification_path]);
						                        }
						                        if ($proof_of_insurance) {
						                            $destinationPath = 'uploads/drivers-documents/';
						                            $filename = md5(microtime() . $proof_of_insurance->getClientOriginalName()) . "." . $proof_of_insurance->getClientOriginalExtension();
						                            Input::file('proof_of_insurance')->move($destinationPath, $filename);
						                            $proof_of_insurance_path = $destinationPath . $filename;
						                          DB::table('dn_driver_requests')->where(['user_id' => $id])->update(['proof_of_insurance' => $proof_of_insurance_path]);
						                        }
											}
				            		}

					if($LFLAG==1 or $IFLAG==1){
								$AdminUsers = DB::table('dn_users')
										->select('dn_users.email','dn_users.full_name')
										->leftjoin('role_user', 'role_user.user_id', '=', 'dn_users.id')		
										->where('role_user.role_id','1')
										->get();
								  //PRINT_R($users);die;
								$subject = "Successfully updated Your Dezi Profile";
								$title = "Alert Mail";
							foreach($AdminUsers as $k=>$v)
								{
									$Admin_full_name = $v->full_name;
									$Admin_email = $v->email;
									if($LFLAG==1){
										$bodyMsg="$full_name has updated his Driver License.";
										}
									
									if($IFLAG==1){
										$bodyMsg="$full_name has updated his Proof Of Insurance.";
										}
									if(empty($full_name)){
										$full_name = "Guest";
									}
									if(empty($Admin_email)){
										$Admin_email = "noreply@dezinow.com";
									}
									if(empty($bodyMsg)){
										$bodyMsg = "N/A";
									}
								\Mail::send('app.mails.general_issue_reply', ['full_name' =>$Admin_full_name,'title' =>$title, 'bodyMessage' => $bodyMsg], function($m) use ($Admin_email, $Admin_full_name, $subject)
									{
										$m->from('dezinow@example.com', 'DeziNow');
										$m->to($Admin_email, $Admin_full_name)->subject($subject);
									}
								);	
								}	
							}	
							
				            Session::flash('message', 'Profile Updated Successfully !!');
							return redirect()->route('alldriver');
			
		}else{
			$id = convert_uudecode(base64_decode($id));
			$usersData=DB::table('dn_users')->where('id',$id)->first();
			$dn_users_data=DB::table('dn_users_data')->where('user_id',$id)->first();
			//print_r($dn_users_data);die;
			$states = DB::table('dn_states')->get();
			return $this->view('driver.edit', compact( 'usersData','dn_users_data','id','states'));
			
		}
	}
	
	
	public function revoke(Request $request)
	{
		$data = $request->all();
		$id = $data['id']; 
		$adminId = Auth::id();
		$actionType=$data['action'];
		if($actionType=='driver_revoke')
		{
			$suspend=DB::table('role_user')
			->where('user_id', $id)
			->where('role_id', 4)
			->update(['role_id' => 5]);
			if($suspend){
				echo "RevokedSuccess";
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id, 'status_type'=>'user_revoked','added_by'=>$adminId]);
			} else {
				echo "RevokedFail";
			}
			
		}
		if($actionType=='driver_unrevoke')
		{
			$active=DB::table('role_user')
			->where('user_id', $id)
			->where('role_id', 5)
			->update(['role_id' => 4]);
			if($active){
				echo "unrevokedsuccess";
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'user_active','added_by'=>$adminId]);
			}else{
				echo "unrevokedfail";
			}
			
		}exit;
	}

	/**
	 * @FUNCTION FOR Revoke Driver
	 * @Author : Gaurav Kumar
	 * @Params : $request is used to handle all Http request
	**/
	public function revokedriver(Request $request) {

		$data = $request->all();

		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '5' );
						})
						->get();
						//echo "<pre>"; print_r($users); echo "</pre>";exit;


		$states = DB::table('dn_states')->get();
		
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
		}


		$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.city) as no_of_users')))
					->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->join('role_user', 'dn_users.id', '=', 'role_user.user_id')
					
					//->where('dn_users.city','!=','')

					->where( function ($query) {
						$query->where( 'dn_users.city','!=','' )
                      	->where( 'role_user.role_id', '5' );
					})
					
					->groupBy('dn_users.city')
					->orderBy('no_of_users')
					->get();

		//$this->preDie( $citys ); 
		//DB::enableQueryLog();


		$n=count($citys)-1;
		
		if($n==0)
		{
			
			$citiesCount=array('least'=>$citys,'most'=>$citys[$n]); 
		}
			//$citiesCount=array('least'=>$citys[0],'most'=>$citys[$n]); 
		@$citiesCount=array('least'=>'N/A','most'=>'N/A'); 
		
		return $this->view('driver.revokeddriverlist', compact( 'users','citiesCount','states'));
	}

	public function alldriverRevokelist(Request $request){
		/* initializing the variables */
			$data = $request->all();
			$limit = 10;
			$draw = $data['draw'];
			$offset = $data['start'];
			$searchString=$data['search']['value'];
		    $startDate=$data['startDate'];
			$endDate=$data['endDate'];
			$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','5'=>'email','6'=>'contact_number','8'=>'is_logged');
			//print_r($data['order'][0]);
			$field='id';
			$direction='ASC';
			//echo $data['state'];die;
			/* code for order by data of user*/
			if(!empty($data['order'][0])){
				foreach($orderfields as $key=>$orderfield){
					if($key==$data['order'][0]['column'] )
					{
						$field=$orderfield;
						$direction=$data['order'][0]['dir'];
					}
				}
			}
			
			/* code for searching of  user*/
			$sql = 'SELECT id FROM dn_users WHERE 1=1';
			if(!empty($startDate) &&  !empty($endDate))
			{
				$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
				$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
			}
			if($data['state']!='')
			{
				$state = $data['state'];
				$sql .=" AND  state= '$state'";
			}
			if($data['city']!='')
			{
				 $city = $data['city'];
				 $sql .=" AND  city= '$city'";
			}
			if($data['state']!='' && $data['city']!='')
			{
				$state = $data['state'];
				$city = $data['city'];
				$sql .="AND  state= '$state' AND  city= '$city'";
			}
			if(@$searchString!='')
			{	
				$search = "%$searchString%";
				$sql .=" AND  (first_name LIKE '$search' or last_name LIKE '$search' or full_name LIKE '$search' or email LIKE '$search'  or contact_number LIKE '$search' ) "; 
			}
			
			$sql .= " order by ".$field." ".$direction;
			$usersIds=DB::select(DB::raw($sql));
			if(!empty($usersIds))
			{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
			}
			$users = array();
			$totalRecords = 0;
			if(!empty($usersList))
			{
				/* code for fetching data from dn_users table */
				$users = DB::table('role_user')
							->select(array('dn_users.*'))
							->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
							//->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')					
							->where('role_id','5')
							->whereIn('role_user.user_id',$usersList)
							->take($limit)->offset($offset) ->orderBy($field,$direction)->get();
							//print_r($users);
				$totalRecords = DB::table('role_user')
							->select(array('dn_users.*'))
							->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
							->where('role_id','5')
							->whereIn('role_user.user_id',$usersList)
							->paginate(config('admin.user.perpage'));
			}
			$Data="";
			foreach($users as $user)
			{
				//echo $user->active;
				$first_name =$user->first_name;
				$last_name =$user->last_name;
				$email=$user->email;
				$phone=$user->contact_number;
				$state=$user->state;
				$city=$user->city;
				//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
				$action='N/A';
				//Subadmin Permission Code Start
				$loggedInUserPermission = Session::get('userPermissions');

				if(empty($loggedInUserPermission)){
					$action="<span class=''><a href='javascript:void(0);' class='btn btn-success width-btn driver_unrevoke' data-action= 'driver_unrevoke' data-userid=".$user->id.">Re-Allow</a></span>";

				}elseif(!empty($loggedInUserPermission)){
				foreach($loggedInUserPermission as $userPermission){
				if($userPermission->module_slug=="drivers" && $userPermission->edit_permission==1){
					$action="<span class=''><a href='javascript:void(0);' class='btn btn-success width-btn driver_unrevoke' data-action= 'driver_unrevoke' data-userid=".$user->id.">Re-Allow</a></span>";
				}
				} 
				} 
				//Subadmin Permission Code End
				
				
				
				if($user->is_logged=='true') {
					$is_logged='Yes';
				}else{
					$is_logged='No';
				} 
				if(empty($first_name))
				{
					$first_name="N/A";
				}
				if(empty($last_name))
				{
					$last_name="N/A";
				}
				if(empty($email))
				{
					$email="N/A";
				}
				if(empty($phone))
				{
					$phone="N/A";
				}
				if(empty($state))
				{
					$state="N/A";
				}
				if(empty($city))
				{
					$city="N/A";
				}
				$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$action.'"'."]";
			}
			if(!empty($Data)){
				$newData=implode(',',$Data);	
				//echo '<pre>';print_r($newData);die;
						return '{
				  "draw": '.$draw.',
				  "recordsTotal": '.count($totalRecords).',
				  "recordsFiltered":'.count($totalRecords).',
				  "data": ['.$newData.']
			}';} else {
				return '{
				  "draw": '.$draw.',
				  "recordsTotal": 0,
				  "recordsFiltered":0,
				  "data": []
				}';
			}
	}

	
	public function dzbnsEarning($uid=null,$did=null)
	{	
		//print_r(57346546);die;
		$referredByData = DB::table('dn_users')->select(DB::raw("CONCAT(first_name,' ',last_name) as fullname"),'email','contact_number','anniversary','dob','gender','unique_code','profile_pic')->where('id',@$uid)->first(); 
		$DrByData = DB::table('dn_users')->select(DB::raw("CONCAT(first_name,' ',last_name) as fullname"),'email','contact_number','anniversary','dob','gender','unique_code','profile_pic')->where('id',@$did)->first(); 
		
		$refBonus= DB::table('dn_user_referrals')->select('amount')->where('referred_by',@$did)->where('user_id',@$uid)->first(); 
		$rideCompleted= DB::table('dn_rides')->where('driver_id',@$uid)->where('status',2)->count(); 
		
		
		/* code for searching of  user*/
				
		
		$othersData= DB::table('dn_rides')->select(DB::raw('Sum(miles) as distance'))
					    ->leftJoin('ride_billing_info', 'dn_rides.id', '=', 'ride_billing_info.ride_id')
					    ->where('driver_id',@$uid)->get(); 
		
		//echo "<pre>"; print_r($referredByData); echo "----------------";die;
			
		return $this->view('driver.dbonus-earning',compact('uid','referredByData','DrByData','refBonus','rideCompleted','othersData','did'));
	}
	public function dzbnsEarningAjax(Request $request)
	{
		
		/* initializing the variables */
			$data = $request->all();
			$limit = 10;
			$draw = @$data['draw'];
			$offset = @$data['start'];
			
			$driverId = @$data['driverId'];
			$orderfields=array('0'=>'dn_rides.id','1'=>'dn_rides.id','2'=>'dn_rides.created_at','3'=>'dn_rides.passenger_id','4'=>'dn_rides.passenger_id','5'=>'dn_rides.status','6'=>'ride_billing_info.total_charges');
			//print_r($data['order'][0]);
			$field='dn_rides.id';
			$direction='ASC';
			//echo $data['state'];die;
			/* code for order by data of user*/
			if(!empty($data['order'][0])){
				foreach($orderfields as $key=>$orderfield){
					if($key==$data['order'][0]['column'] )
					{
						$field=$orderfield;
						$direction=$data['order'][0]['dir'];
					}
				}
			}
			
			/* code for searching of  user*/
			$sql = "SELECT dn_rides.id as rideId,
					dn_rides.created_at as timestamp,
					dn_rides.passenger_id,dn_rides.status,ride_billing_info.total_charges,
					((2/100) * dn_payments.amount *(ride_billing_info.miles_charges + ride_billing_info.duration_charges - dn_payments.refund_amount)
					+ride_billing_info.tip + ride_billing_info.pickup_fee) AS earning FROM dn_rides 
					LEFT JOIN ride_billing_info  ON dn_rides.id = ride_billing_info.ride_id
					LEFT join dn_payments on dn_rides.id = dn_payments.ride_id
					WHERE dn_rides.driver_id = $driverId ";
			
			
			$totalRecords=0;
			$totalRecords=DB::select(DB::raw($sql));
			$sql .= " order by ".$field." ".$direction;
			$sql .= " Limit ".$offset." , ".$limit;
			$rideData=DB::select(DB::raw($sql));
			
			
			
			$Data="";
			foreach($rideData as $data)
			{
				//echo $user->active;
				$rideId =$data->rideId;
				$timestamp = $data->timestamp;
				$passenger_id = $data->passenger_id;
				$passenger_name = DB::table('dn_users')->select(DB::raw("CONCAT(first_name,' ',last_name) as passenger_name"))->where('id',@$data->passenger_id)->first();
				
				$status=$data->status;
				$total_charges=$data->total_charges;
				$earning=$data->earning;

				if(empty($rideId))
				{
					$rideId="N/A";
				}
				if(empty($timestamp))
				{
					$timestamp="N/A";
				}
				if(empty($passenger_id))
				{
					$passenger_id="N/A";
				}
				if(empty($status))
				{
					$status="N/A";
				}
				if(empty($total_charges))
				{
					$total_charges="N/A";
				}
				if(empty($passenger_name->passenger_name))
				{
					$pass_name="N/A";
				}else{$pass_name=$passenger_name->passenger_name;}
				if(empty($earning))
				{
					$earning="N/A";
				}if(empty($total_charges))
				{
					$total_charges="N/A";
				}
				if(empty($adminName->adminname))
				{
					$adminName="N/A";
				}else{
					$adminName=$adminName->adminname;
				}
				if(!empty($status) && $status==1)
				{
					$status="In Process";
				}elseif(!empty($status) && $status==2)
				{
					$status="Complete";
				}elseif(!empty($status) && $status==3)
				{
					$status="Ride Cancel";
				}elseif(!empty($status) && $status==4)
				{
					$status="No Response";
				}elseif(!empty($status) && $status==5)
				{
					$status="No Response";
				}
				$Data[]= "[". 
							'"'.++$offset .'"' . ",". 
							'"'.$rideId .'"' . ",". 
							'"'.$timestamp .'"'.",". 
							'"'.$passenger_id.'"' .",". 
							'"'.$pass_name.'"'.",". 
							'"'.$status.'"'.",". 
							'"'.$total_charges.'"'.",". 
							'"'.$earning.'"'."]";
			}
			if(!empty($Data)){
				$newData=implode(',',$Data);	
				//echo '<pre>';print_r($newData);die;
						return '{
				  "draw": '.$draw.',
				  "recordsTotal": '.count($totalRecords).',
				  "recordsFiltered":'.count($totalRecords).',
				  "data": ['.$newData.']
			}';} else {
				return '{
				  "draw": '.$draw.',
				  "recordsTotal": 0,
				  "recordsFiltered":0,
				  "data": []
				}';
			}
		
	}
	/*Avinash thakur 29 july add the controller method ridebonus*/
	/*this method gets the ride bonus data for a driver from the database*/
	public function ridebonus(Request $request){
		
		//echo "hi";die;
		/* initializing the variables */
			$data = $request->all();
			$limit = 10;
			$draw = @$data['draw'];
			$offset = @$data['start'];
			$searchString=$data['search']['value'];
		    $startDate=@$data['startDate'];
			$endDate=@$data['endDate'];
			$driverId = @$data['useriddriver'];
			$orderfields=array('0'=>'dn_user_referrals.id','1'=>'dn_user_referrals.user_id','2'=>'dn_user_referrals.created_at','3'=>'dn_driver_dezibunus.bonus_txn_type','4'=>'dn_user_referrals.ride_id','5'=>'dn_user_referrals.amount','6'=>'dn_driver_dezibunus.bonus_type','7'=>'dn_driver_dezibunus.bonus_by');
			//print_r($data['order'][0]);
			$field='dn_driver_dezibunus.id';
			$direction='ASC';
			//echo $data['state'];die;
			/* code for order by data of user*/
			if(!empty($data['order'][0])){
				foreach($orderfields as $key=>$orderfield){
					if($key==$data['order'][0]['column'] )
					{
						$field=$orderfield;
						$direction=$data['order'][0]['dir'];
					}
				}
			}
			
			/* code for searching of  user*/
			$sql = "SELECT dn_user_referrals.user_id as referredId,dn_user_referrals.referred_by as did,dn_user_referrals.created_at as timestamp,dn_driver_dezibunus.bonus_txn_type, dn_user_referrals.ride_id as veriRideId ,dn_user_referrals.amount,dn_driver_dezibunus.bonus_type,dn_driver_dezibunus.bonus_by FROM dn_driver_dezibunus LEFT JOIN dn_user_referrals on dn_driver_dezibunus.user_id =  dn_user_referrals.referred_by  WHERE dn_driver_dezibunus.user_id = $driverId AND dn_driver_dezibunus.last_flag=1 ";
			if(!empty($startDate) &&  !empty($endDate))
			{
				$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
				$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				$sql .=" AND  dn_user_referrals.created_at BETWEEN '$startDate' AND '$endDate'";
			}
			
			if(@$searchString!='')
			{	
				$search = "%$searchString%";
				$sql .=" AND  (dn_user_referrals.user_id LIKE '$search' or dn_user_referrals.created_at LIKE '$search' or dn_driver_dezibunus.bonus_txn_type LIKE '$search' or dn_user_referrals.ride_id LIKE '$search'  or dn_user_referrals.amount LIKE '$search') ";
			}
			
			$totalRecords=0;
			$totalRecords=DB::select(DB::raw($sql));
			$sql .= " order by ".$field." ".$direction;
			$sql .= " Limit ".$offset." , ".$limit;
			
			$deziBonusData=DB::select(DB::raw($sql));
			
			
			$Data="";
			//print_r($deziBonusData);die;
			foreach($deziBonusData as $DezBns)
			{
				//echo $user->active;
				$referredId =$DezBns->referredId;
				$did =$DezBns->did;
				$timestamp =$DezBns->timestamp;
				$bonus_txn_type=(strtolower($DezBns->bonus_txn_type)=="cr")?"Credit":"Debit";
				$veriRideId=$DezBns->veriRideId;
				$amount=$DezBns->amount;
				$bonus_type=$DezBns->bonus_type;
				$bonus_by=$DezBns->bonus_by;
				
				// $view='<span class="label-info label">'.link_to_route('dzbnsEarning','View',$referredId ).'</span>';
				//$view="<span class='label-info label'><a href='".url("admin/dzbnsEarning/".$referredId.'/'.$did)."'>View</a></span>";
				$adminName=DB::table('dn_users')->select(DB::raw("CONCAT(first_name,' ',last_name) as adminname"))->where('id',$bonus_by)->first();
				
				
				if(empty($referredId))
				{
					$referredId="N/A";
				}
				if(empty($timestamp))
				{
					$timestamp="N/A";
				}
				if(empty($bonus_txn_type))
				{
					$bonus_txn_type="N/A";
				}
				if(empty($veriRideId))
				{
					$veriRideId="N/A";
				}
				if(empty($amount))
				{
					$amount="N/A";
				}
				if(empty($adminName->adminname))
				{
					$adminName="N/A";
				}else{
					$adminName=$adminName->adminname;
				}
				if(empty($bonus_type) && $bonus_type==1)
				{
					$bonus_type="Referral Bonus";
				}else{
					$bonus_type="Not-Assigned";
				}
				$Data[]= "[". 
							'"'.++$offset .'"' . ",". 
							'"'.$referredId .'"' . ",". 
							'"'.$timestamp .'"'.",". 
							'"'.$bonus_txn_type.'"' .",". 
							'"'.$amount.'"'.",". 
							'"'.$adminName.'"'."]";
			}
			if(!empty($Data)){
				$newData=implode(',',$Data);	
				//echo '<pre>';print_r($newData);die;
						return '{
				  "draw": '.$draw.',
				  "recordsTotal": '.count($totalRecords).',
				  "recordsFiltered":'.count($totalRecords).',
				  "data": ['.$newData.']
			}';} else {
				return '{
				  "draw": '.$draw.',
				  "recordsTotal": 0,
				  "recordsFiltered":0,
				  "data": []
				}';
			}
		
	}

	/*ridebonus end*/

	public function bonus($row_id=null){
		//$data = $request->all();
		//$id = convert_uudecode(base64_decode($id));
		/*the id variable will contain the row id from the bonus table*/
		if(!$row_id){
			/*redirect the user to another page*/
			return redirect()->back()->with('error','kindly provide the id parameter');
		}
		
		$bonusDetails = DB::table('dn_driver_referrals')->where('id',$row_id)->first();

		if(!empty($bonusDetails)){
			$driverDetails = $this->getUserDetails($bonusDetails->driver_id);
			$referrerDetails = $this->getUserDetails($bonusDetails->user_id);
			
			$ridesCompleted = DB::table('dn_rides')
							  ->join('dn_users','dn_rides.passenger_id','=','dn_users.id')
							  ->join('dn_payments','dn_rides.id', '=', 'dn_payments.ride_id')
							  ->where('dn_rides.driver_id',$bonusDetails->driver_id)
							  ->select('dn_rides.*' , 'dn_users.first_name', 'dn_users.last_name','dn_payments.amount','dn_payments.driver_earning')
							  ->get();

			$payments = DB::table('dn_payments')
						->join('dn_rides', 'dn_payments.ride_id', '=', 'dn_rides.id')
						->where('dn_rides.payment_status',1)
						->where('dn_rides.driver_id',$bonusDetails->driver_id)
						->sum('dn_payments.amount');
						
			
			$ridesCompletedCount = count($ridesCompleted);
			/*$commission = 'commission';
			if($ridesCompletedCount >= 20){
				$commission .= '_platinum';
			}elseif ($ridesCompletedCount >= 10 && $ridesCompletedCount < 20) {
				$commission .= '_gold';
			}elseif ($ridesCompletedCount >= 5 && $ridesCompletedCount < 10) {
				$commission .= '_silver';
			}else{
				return redirect()->back()->with('error','You need to complete more rides to get Bonus');
			}*/

			/*$day_number = date('N', strtotime(date('Y-m-d')));

			$commissionPercentage = DB::table('dn_driver_bonus')
									->where('day_number',$day_number)
									->where('is_active',1)
									->select($commission)
									->first();
			$referralBonus = 0;
			if(count($commissionPercentage) !== 0){		
				$referralBonus =  ($commissionPercentage->$commission / 100) * $payments;
			}*/
			$totalDistance = DB::table('ride_billing_info')
							 ->join('dn_rides', 'ride_billing_info.ride_id', '=','dn_rides.id')
							 ->where('dn_rides.driver_id',$bonusDetails->driver_id)
							 ->sum('ride_billing_info.miles');
			$tipAmount = DB::table('ride_billing_info')
							 ->join('dn_rides', 'ride_billing_info.ride_id', '=','dn_rides.id')
							 ->where('dn_rides.driver_id',$bonusDetails->driver_id)
							 ->sum('ride_billing_info.tip');
		}else{
			/*if the $bonusDetails variable is empty*/
			/*this means that there is no entry in the driver referral table*/
			return redirect()->back()->with('error','driver referral data empty');
		}
		

		return $this->view('driver.bonus',
			compact('driverDetails','referrerDetails','ridesCompleted'
				,'bonusDetails','ridesCompletedCount'
				,'totalDistance','payments','tipAmount'));
	}

	/*this is an AJAX request method which is used to get all the 
	  rides for a user's bonus level*/
	public function bonusHistory(Request $request){
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$row_id = $data['row_id'];
		if(!$row_id){
			/*redirect the user to another page*/
			return redirect()->back()->with('error','kindly provide the id parameter');
		}

		$bonusDetails = DB::table('dn_driver_referrals')->where('id',$row_id)->first();

		$ridesCompleted = DB::table('dn_rides')
					  ->join('dn_users','dn_rides.passenger_id','=','dn_users.id')
					  ->join('dn_payments','dn_rides.id', '=', 'dn_payments.ride_id')
					  ->where('dn_rides.driver_id',$bonusDetails->driver_id)
					  ->select('dn_rides.*' , 'dn_users.first_name', 'dn_users.last_name','dn_payments.amount','dn_payments.driver_earning')
					  ->get();
		$Data = [];
		$count = 1;
		
		foreach ($ridesCompleted as $rides) {
			$ride_id 	= $rides->id;
			$dateTime 	= $rides->ride_start_time;
			$passengerName = $rides->first_name.' '.$rides->last_name;
			$passengerId = $rides->passenger_id;
			$status = "Success";
			$billingAmount = $rides->amount;
			$earningAmount = $rides->driver_earning;

		$Data[] = "[".'"'.$count.'"'. ",". '"'.$ride_id .'"'.",". '"'.$dateTime.'"' .",". '"'.$passengerName.'"'.",".'"'.$passengerId.'"' .",". '"'.$status.'"'.",". '"'.$billingAmount.'"'.",". '"'.$earningAmount.'"'."]";
		$count++;
		}

		if(!empty($Data)){
				$newData=implode(',',$Data);
						return '{
				  "draw": '.$draw.',
				  "recordsTotal": '.$count.',
				  "recordsFiltered":'.$count.',
				  "data": ['.$newData.']
			}';} else {
				return '{
				  "draw": '.$draw.',
				  "recordsTotal": 0,
				  "recordsFiltered":0,
				  "data": []
				}';
			}

	}
	
	public function ridehistory(Request $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$driverId = $data['useriddriver'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
		$orderfields=array('0'=>'dn_rides.id','1'=>'dn_rides.id','2'=>'dn_rides.created_at','3'=>'dn_users.first_name','4'=>'dn_users.unique_code','8'=>'dn_payments.amount');
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$rideStatus=$data['rideStatus'];
		
		//print_r($data['order'][0]);
		$field='dn_rides.id';
		$direction='ASC';
		/* code for order by data of user*/
		if(!empty($data['order'][0])){
			foreach($orderfields as $key=>$orderfield){
				if($key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}

		$sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_users.first_name,dn_users.last_name,dn_users.unique_code as passenger_id,dn_report_an_issuse.message,dn_rides.status,dn_payments.amount from dn_rides ";
		$sql.="LEFT join dn_users on dn_rides.passenger_id = dn_users.id ";
		$sql.="LEFT join dn_report_an_issuse on dn_rides.id = dn_report_an_issuse.ride_id ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.=" where dn_rides.driver_id = $driverId ";
		//echo $sql;exit;
		//$sql="Select * from dn_rides where driver_id = 253";
		if(!empty($startDate) &&  !empty($endDate))
		{
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_rides.created_at BETWEEN '$startDate' AND '$endDate'";
		}
		if(!empty($rideStatus))
		{
			$sql .=" AND  dn_rides.status = '$rideStatus' ";
		}
		if(@$searchString!='')
		{	
			
			$search = "%$searchString%";
			$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_rides.id LIKE '$search' or dn_rides.passenger_id LIKE '$search' ) ";
		}
		$sql .= " order by dn_rides.id DESC";
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalRide=DB::select(DB::raw($sql));
		
		//print_r($totalRide);exit;
		
		/* $totalRide = DB::table('dn_rides')
					->select(array('*'))
					->join('dn_users', 'dn_rides.driver_id', '=', 'dn_users.id')
					->where('dn_rides.driver_id',$driverId)
					->take($limit)->offset($offset) ->orderBy($field,$direction)
					->get(); */
		//$users = array();
		$totalRecords = 0;
		
		
		$Data="";
		foreach($totalRide as $ride)
		{
			//echo $user->active;
			$rideId=$ride->rideId;
			$timeStamp=$ride->timeStamp;
			$passenger_name =$ride->first_name.' '.$ride->last_name;
			
			$passenger_id=$ride->passenger_id;
			
			$reportedIssue=count(DB::table('dn_report_an_issuse')->where("ride_id",$rideId)->get());
			$billingAmount=$ride->amount;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			$action = "<span class='label-success label '><a href=".route('ridedetail',base64_encode(convert_uuencode($rideId)))." > View </a></span>";
					
			
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			
			if(empty($billingAmount))
			{
				$billingAmount="N/A";
			}else{
				$billingAmount="$".$billingAmount;
			}
			if(empty($passenger_name))
			{
				$passenger_name="N/A";
			}
			if(empty($unique_code))
			{
				$unique_code="N/A";
			}
			if(empty($passenger_id))
			{
				$passenger_id="N/A";
			}
			if(empty($reportedIssue))
			{
				$reportedIssue="0";
			}
			
			$rideStatus=$ride->status;
			
			if($rideStatus=='1')
			{
				$rideStatus="In process";
				
			} 
			else if($rideStatus=='2')
			{
				$rideStatus="Complete";
				
			}
			else if($rideStatus=='3')
			{
				$rideStatus="Ride Cancelled";
				
			}else if($rideStatus=='4')
			{
				$rideStatus="No Response";
				
			}else if($rideStatus=='5')
			{
				$rideStatus="Cancel Ride Request";
				
			}else if($rideStatus=='6')
			{
				$rideStatus="Ride Cancel But No Bill";
				
			}
			else 
			{
				$rideStatus="N/A";
				
			}
			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$rideId .'"' . ",". '"'.$timeStamp.'"' . ",". '"'.$passenger_name .'"'.",". '"'.$passenger_id.'"'.",". '"' .$reportedIssue. '"'.",". '"'.$rideStatus.'"'.",". '"'.$billingAmount.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalRideCount).',
			  "recordsFiltered":'.($totalRideCount).',
			  "data": ['.$newData.']
		}';} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	}
	
	public function driverIssueAjax(Request $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		
			
		$offset = $data['start'];
		$driverId = $data['useriddriver'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
		
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'dn_rides.id','1'=>'dn_rides.id','2'=>'dn_report_an_issuse.created_at','3'=>'dn_users.first_name','4'=>'dn_users.unique_code','7'=>'dn_payments.amount');
		//print_r($data['order'][0]);
		$field='dn_report_an_issuse.id';
		$direction='ASC';
		/* code for order by data of user*/
		if(!empty($data['order'][0])){
			foreach($orderfields as $key=>$orderfield){
				if($key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		$sql="Select dn_report_an_issuse.ride_id as rideId,dn_report_an_issuse.created_at as timeStamp,dn_users.first_name,dn_users.last_name,dn_users.unique_code as passenger_id,dn_cancellation_category.category,dn_cancellation_subcategory.subcategory,dn_rides.status,dn_payments.amount from dn_report_an_issuse ";
		$sql.="LEFT join dn_users on dn_report_an_issuse.user_id = dn_users.id ";
		$sql.="LEFT join dn_rides on dn_rides.id = dn_report_an_issuse.ride_id ";
		$sql.="LEFT join dn_payments on dn_report_an_issuse.ride_id = dn_payments.ride_id ";
		$sql.="LEFT join dn_cancellation_category on dn_report_an_issuse.category = dn_cancellation_category.id ";
		$sql.="LEFT join dn_cancellation_subcategory on dn_report_an_issuse.sub_category = dn_cancellation_subcategory.id ";
		$sql.=" where dn_rides.driver_id = $driverId ";
		//AND dn_report_an_issuse.user_type='passenger'";
		if(!empty($startDate) &&  !empty($endDate))
		{
			
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_report_an_issuse.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		if(@$searchString!='')
		{	
			
			$search = "%$searchString%";
			$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_rides.id LIKE '$search') ";
		}
		$sql .= " order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalIssue=DB::select(DB::raw($sql));
		
		//print_r($totalIssue);exit;
		
		/* $totalRide = DB::table('dn_rides')
					->select(array('*'))
					->join('dn_users', 'dn_rides.driver_id', '=', 'dn_users.id')
					->where('dn_rides.driver_id',$driverId)
					->take($limit)->offset($offset) ->orderBy($field,$direction)
					->get(); */
		
		
		
		//print_r($totalRide);
		
		//$users = array();
		$totalRecords = 0;
		
		$Data="";
		foreach($totalIssue as $issue)
		{
			//echo $user->active;
			$rideId=$issue->rideId;
			$timeStamp=$issue->timeStamp;
			$passenger_name =$issue->first_name.' '.$issue->last_name;
			$category=$issue->category;
			$subCategory=$issue->subcategory;
			$passenger_id=$issue->passenger_id;
			
			$billingAmount=$issue->amount;
			$encryptedID=base64_encode(convert_uuencode($rideId));
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			$action = "<span class='label-success label '><a href='".route("ridedetail",$encryptedID)."'> View </a></span>";
					
			
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			if(empty($billingAmount))
			{
				$billingAmount="N/A";
			}else{
				$billingAmount="$".$billingAmount;
			}
			if(empty($passenger_name))
			{
				$passenger_name="N/A";
			}
			if(empty($unique_code))
			{
				$unique_code="N/A";
			}
			if(empty($passenger_id))
			{
				$passenger_id="N/A";
			}
			if(empty($reportedIssue))
			{
				$reportedIssue="N/A";
			}
			if(empty($category))
			{
				$category="N/A";
			}
			if(empty($subCategory))
			{
				$subCategory="N/A";
			}
			$rideStatus=$issue->status;
			
			if($rideStatus=='0')
			{
				$rideStatus="In process";
				
			} 
			else if($rideStatus=='2')
			{
				$rideStatus="Complete";
				
			}
			else if($rideStatus=='3')
			{
				$rideStatus="cancel";
				
			}
			else 
			{
				$rideStatus="No Responce";
				
			}
			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$rideId .'"' . ",". '"'.$timeStamp.'"' . ",". '"'.$passenger_name .'"'.",". '"'.$unique_code.'"'.",". '"' .$category ."=>".$subCategory .'"'.",". '"'.$rideStatus.'"'.",". '"'.$billingAmount.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalRideCount).',
			  "recordsFiltered":'.($totalRideCount).',
			  "data": ['.$newData.']
		}';} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	}

	public function driverearningAjax(Request $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$driverId = $data['useriddriver'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
		$orderfields=array('0'=>'dn_rides.id','1'=>'dn_rides.id','2'=>'dn_rides.created_at','3'=>'dn_users.first_name','4'=>'dn_users.unique_code','8'=>'dn_payments.amount');
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		
		//print_r($data['order'][0]);
		$field='dn_rides.id';
		$direction='ASC';
		/* code for order by data of user*/
		if(!empty($data['order'][0])){
			foreach($orderfields as $key=>$orderfield){
				if($key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		$sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_users.first_name as firstname,
		dn_users.last_name as lastname,dn_users.unique_code as passenger_id,dn_rides.status,dn_payments.amount as Billingamount,
		dn_payments.refund_amount as RefundAmount,ride_billing_info.miles_charges as MilesCharges,
		ride_billing_info.duration_charges as DurationCharges,ride_billing_info.pickup_fee as PickupFee,ride_billing_info.tip as tip from dn_rides ";
		$sql.="LEFT join dn_users on dn_rides.passenger_id = dn_users.id ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.="LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id ";
		$sql.=" where dn_rides.driver_id = $driverId";

		//$sql.="Select greater_mile_travel_cost from dn_rides where driver_id = 253";
		if(!empty($startDate) &&  !empty($endDate))
		{
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_rides.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_rides.id LIKE '$search') ";
		}
		$sql .= " order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalRide=DB::select(DB::raw($sql));
		//print_r($totalRide);exit;
		
		
		/* $totalRide = DB::table('dn_rides')
					->select(array('*'))
					->join('dn_users', 'dn_rides.driver_id', '=', 'dn_users.id')
					->where('dn_rides.driver_id',$driverId)
					->take($limit)->offset($offset) ->orderBy($field,$direction)
					->get(); */
		//$users = array();
		$totalRecords = 0;
		
		$Data="";
		foreach($totalRide as $ride)
		{
			//echo $user->active;
			$rideId=$ride->rideId;
			$timeStamp=$ride->timeStamp;
			$passenger_name =$ride->firstname.' '.$ride->lastname;
			
			$passenger_id=$ride->passenger_id;
			$rideStatus=$ride->status;
			$billingAmount=$ride->Billingamount;
			
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";  
			$action = "<span class='label-success label '><a href='".url('admin/driver/paymentetail')."/".base64_encode(convert_uuencode($rideId))."'> View </a></span>";
			$commisionPercentage=(2 / 100) * $billingAmount;
			$earningamount=$commisionPercentage*($ride->MilesCharges+$ride->DurationCharges-$ride->RefundAmount)+$ride->PickupFee+$ride->tip;		
			
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			
			if(empty($billingAmount))
			{
				$billingAmount="N/A";
			}else{
				$billingAmount="$".$billingAmount;
			}
			if(empty($earningamount))
			{
				$earningamount="N/A";
			}else{
				$earningamount="$".$earningamount;
			}
			if(empty($passenger_name))
			{
				$passenger_name="N/A";
			}
			if(empty($unique_code))
			{
				$unique_code="N/A";
			}
			if(empty($passenger_id))
			{
				$passenger_id="N/A";
			}
			
			
			$rideStatus=$ride->status;
			
			if($rideStatus=='1')
			{
				$rideStatus="In process";
				
			} 
			else if($rideStatus=='2')
			{
				$rideStatus="Complete";
				
			}
			else if($rideStatus=='3')
			{
				$rideStatus="cancel";
				
			}
			else 
			{
				$rideStatus="No Responce";
				
			}


			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$rideId .'"' . ",". '"'.$timeStamp.'"' . ",". '"'.$passenger_name .'"'.",". '"'.$passenger_id.'"'.",". '"' .$rideStatus. '"'.",". '"' .$billingAmount. '"'.",". '"'.$earningamount.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalRideCount).',
			  "recordsFiltered":'.($totalRideCount).',
			  "data": ['.$newData.']
		}';
		} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	}
	
	
	
	public function driverHourAjax(Request $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$driverId = $data['useriddriver'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
		$orderfields=array('0'=>'dn_user_logs.id','2'=>'dn_user_logs.login_time','3'=>'dn_user_logs.logout_time');
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		
		//print_r($data['order'][0]);
		$field='dn_user_logs.id';
		$direction='DESC';
		/* code for order by data of user*/
		if(!empty($data['order'][0])){
			foreach($orderfields as $key=>$orderfield){
				if($key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		$sql="Select date(login_time) as date, time(login_time) as login_time,time(logout_time) as  logout_time, SEC_TO_TIME(TIMEDIFF(logout_time,login_time)) as duration from dn_user_logs  where user_id=$driverId ";
		//$sql.="Select greater_mile_travel_cost from dn_rides where driver_id = 253";
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sql .=" AND  (login_time LIKE '$search' or logout_time LIKE '$search') ";
		}
		$sql .= " order by dn_user_logs.id DESC ";
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		$sql .= " Limit ".$offset." , ".$limit;
		$hourlogs=DB::select(DB::raw($sql));
		$totalRecords = 0;
		$Data="";
		foreach($hourlogs as $logs)
		{
			//echo $user->active;
			//$rideId=$logs->rideId;
			$date=date("m-d-Y",strtotime($logs->date));
			$login_time  = $logs->login_time;
			$logout_time = $logs->logout_time;
			$duration=$logs->duration;
			if(empty($date))
			{
				$date="N/A";
			}
			
			if(empty($login_time))
			{
				$login_time="N/A";
			}
			if(empty($logout_time))
			{
				$logout_time="N/A";
			}
			if(empty($duration))
			{
				$duration="N/A";
			}/* else if($duration > 60){
				$hour    = $duration/60;
				$minute  = $duration%60;
				$duration = $hour . ":" . $minute. " Hr";
			}else{
				$duration = $duration. " Minute";
			} */
			
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$date .'"' . ",". '"'.$login_time.'"' . ",". '"'.$logout_time .'"'.",". '"'.$duration.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalRideCount).',
			  "recordsFiltered":'.($totalRideCount).',
			  "data": ['.$newData.']
		}';} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	}
	
	
	
	
	public function documentAjax(Request $request)
	{
		/* initializing the variables */
		
		
		$data = $request->all();
		//print_r($data);exit;
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$driverId = $data['useriddriver'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
		$orderfields=array('0'=>'id','1'=>'make','2'=>'model','4'=>'transmission');
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		
		//print_r($data['order'][0]);
		$field='id';
		$direction='ASC';
		/* code for order by data of user*/
		if(!empty($data['order'][0])){
			foreach($orderfields as $key=>$orderfield){
				if($key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		$sql2="select dn_users_data.license_number, dn_driver_requests.* from dn_users_data LEFT JOIN dn_driver_requests  ON dn_users_data.user_id = dn_driver_requests.user_id where dn_users_data.user_id = $driverId";
		$licenseNo = DB::select(DB::raw($sql2));
		$totalRideCount=DB::select(DB::raw($sql2));
		$totalRideCount= count($totalRideCount);
		foreach($licenseNo as $license)
		{
			$licenseNumber='';
			$downloadLicense='';
			$proofOfInsurance='';
			$downloadInsurance='';

			if(@$license->license_number && !empty($license->license_number))
			{
				$licenseNumber=@$license->license_number;
			}
			if(@$license->license_verification){
				$licenseVerification=@$license->license_verification;
				$downloadLicense="<a download='' href = '".url($licenseVerification)."' class='btn btn-large pull-left'><i class='icon-download-alt'> </i> Download Documents </a>";
				
			}else {
				$downloadLicense="N/A";

			}if(@$license->proof_of_insurance){
				$proofOfInsurance=@$license->proof_of_insurance;
				$downloadInsurance="<a download='' href='".url($proofOfInsurance)."'  class='btn btn-large pull-left'><i class='icon-download-alt'> </i> Download Documents </a>";
			}else{
				$downloadInsurance="N/A";
			}

			$Data[]= "[". '"'.++$offset.'"'.",".
					'"'.$licenseNumber .'"'.",". 
					'"'.$downloadLicense.'"'.",".
					'"'.$downloadInsurance.'"'."]";	
		}
		
		
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalRideCount).',
			  "recordsFiltered":'.($totalRideCount).',
			  "data": ['.$newData.']
		}';} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	}
	/**
	 * @FUNCTION FOR APPROVE DRIVERS
	 * @Author : HARISH CHAUHAN
	 * @Params : $request is used to handle all Http request
	**/

	public function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
	 public function driverAction(Request $request)
	  {
		  /* initializing variables */
		  
		$data = $request->all();
		$id = $data['id']; 
		$adminId = Auth::id();
		$actionType=$data['action'];

		if($actionType=='driver_approve' || $actionType=='btndriver_approve')
		{
			$adminDetail = DB::table('dn_users')
							  ->select('dn_users.first_name', 'dn_users.contact_number')
							  ->where('id',$id)
							  ->first();
			
			if(!empty($adminDetail->first_name)){
				
				$newstring = rand(0000,9999);				  
				$randomstring=$this->generateRandomString();
				$randomstring="DR".$adminDetail->first_name.$newstring;
				
				$date = date('Y-m-d H:i:s');
				$sessiondata = Session::all();
				
				$update = ['is_driver_approved' => 1,'is_suspended'=>0,'driver_approved_on' => $date,'driver_verified_by' =>$sessiondata['login_82e5d2c56bdd0811318f0cf078b78bfc']];
				DB::table('dn_users')
				->where('id', $id)
				->update($update);
				$exists=DB::table('role_user')->where('user_id',$id)->Where('role_id',4)->first();
				if(!empty($exists)){
						
					$active=true;
				}else{
					$active=DB::table('role_user')->insert([
						'user_id'=>$id,
						'role_id'=>4,
					]);
				}
				
			}
			if($active){
				if ($actionType=='btndriver_approve') { echo "btnapproveSuccess"; }
				else{ echo "approveSuccess"; }

				$update = ['referral_code' => $randomstring];

				DB::table('dn_users_data')
				->where('user_id', $id)
				->update($update);

				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'user_active','added_by'=>$adminId]);
			
			}else{
				echo "approveFail";
			}
			
		}

		if($actionType=='driver_disapprove' || $actionType=='btndriver_disapprove')
		{

			$active=DB::table('dn_users')
			->where('id', $id)
			->update(['is_driver_approved' => 2]);
			if($active){
				if ($actionType=='btndriver_disapprove') { echo "btndisapproveSuccess"; }
				else{ echo "disapproveSuccess"; }
				
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'user_active','added_by'=>$adminId]);
			}else{
				echo "disapproveFail";
			}
			
		}

		//TRASH CODE STARTS
		if($actionType=='driver_suspend')
		{
			$suspend=DB::table('dn_users')
			->where('id', $id)
			->update(['active' => 0]);
			if($suspend){
				echo "suspendSuccess";
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id, 'status_type'=>'user_suspend','added_by'=>$adminId]);
			}else{
				echo "suspendFail";
			}
			
		}

		if($actionType=='driver_active')
		{
			$suspend=DB::table('dn_users')
			->where('id', $id)
			->update(['active' => 1]);
			if($suspend){
				echo "btnactiveSuccess";
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id, 'status_type'=>'user_suspend','added_by'=>$adminId]);
			}else{
				echo "activeFail";
			}
			
		}

		exit;
	 }

	/**
	  * @Function for age calculator
	  * @Params: '$dob' is the date of birth 
	  */

	 public function ageCalculator($dob=null)
	 {
		if(!empty($dob)){
			$birthdate = new DateTime($dob);
			$today   = new DateTime('today');
			$age = $birthdate->diff($today)->y;
			return $age;
		} else {
			return 0;
		}
	}

	/**
	  * @Function for users details
	  * @Params: '$id' is the user id
	  */
	 public function getUserDetails($id=null)
	 {
		if(!empty($id))
		{
			$user = DB::table('role_user')
					->select(array('dn_users.*','role_user.role_id','role_user.user_id'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$id)->first(); // get data from 'dn_users' table.
			
		return $user;
		}else{
			return 0;
		}
			
	 }

	 public function deleteImage(Request $request){
	 	$data=$request->all();
	 	//print_r($data['id']);die;
	 	$delimage = DB::table('dn_users_data')
			->where( 'user_id',$data['id'])
			->update([
				'driver_profile_pic' => ''
				]);
				
		DB::table('dn_users')
			->where( 'id',$data['id'])
			->update([
				'is_suspended' => 1,'is_driver_approved'=>0
				]);
		if($delimage){echo "deleted";}

	 }
	 /**
	  * @FUNCTION FOR View PASSENGER AND HIS OTHER DETAILS
	  * @Author : 
	  * @return variable '$user' to display user , '$userCars' for all car details for that user.
	  **/
	 public function show($id=null)
	  {
		  
		$id = convert_uudecode(base64_decode($id));
		$userRoles=DB::table('role_user')->where('user_id',$id)->get(); 
		$driver=0;
		$revoke=0;
		foreach($userRoles as $role)
		{
				if( $role->role_id == 4 || $role->role_id == 5  )
				{
					$driver=1;
				}
				if($role->role_id==5)
				{
					$revoke=1;
				}
		}
		$changeLog= DB::table('dn_driver_change_log')
					->select(array('dn_driver_change_log.*','dn_users.first_name','dn_users.last_name'))
					->leftjoin('dn_users', 'dn_users.id', '=', 'dn_driver_change_log.added_by')
					->where('user_id',$id)->get();
		
		$user=$this->getUserDetails($id); //get user personal details
		$userCars=DB::table('dn_user_cars')->where('user_id',$id)->where('is_delete',0)->get(); // get his car details
		$userapprovedById = DB::table('dn_users')->select('driver_verified_by','created_at')->where('id',$id)->first();
		$uAID=@$userapprovedById->driver_verified_by;
		
		$userapprovedBy = DB::table('dn_users')->select('first_name')->where('id',$uAID)->first();
		 
		if(empty($userapprovedBy))
		{
			$ApName="N/A";
		}else{
			$ApName= @$userapprovedBy->first_name;
		}
		$dob = $user->dob;
		if($user->active==1)
		{
			$user->active='Active';
		}else{$user->active='Suspended';}
		$age= $this->ageCalculator($dob);
		$_SESSION['countRide']=0;
		$lastRide=DB::table('dn_rides')->select('created_at')->where('driver_id',$id)->groupBy('created_at')->orderBy('created_at','DESC')->first();
		//print_r($lastRide);die;
		$lastRide=@$lastRide->created_at;
		
		$sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_users.first_name as firstname,
		dn_users.last_name as lastname,dn_users.unique_code as passenger_id,dn_rides.status,dn_payments.amount as Billingamount,
		dn_payments.refund_amount as RefundAmount,dn_payments.driver_earning,ride_billing_info.miles_charges as MilesCharges,
		ride_billing_info.duration_charges as DurationCharges,ride_billing_info.pickup_fee as PickupFee,ride_billing_info.tip as tip from dn_rides ";
		$sql.="LEFT join dn_users on dn_rides.passenger_id = dn_users.id ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.="LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id ";
		$sql.=" where dn_rides.driver_id = $id";
		//echo $sql;exit;
		//$sql .= " order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		//$sql .= " Limit ".$offset." , ".$limit;
		$totalRide=DB::select(DB::raw($sql));
		//print_r($totalRide);exit;
		$earningamount='';
		$billingAmount='';
		foreach($totalRide as $ride)
		{
			//echo $user->active;
			$rideId=$ride->rideId;
			$timeStamp=$ride->timeStamp;
			//echo $ride->Billingamount;exit;
			$billingAmount=$billingAmount+$ride->Billingamount;
			$billingAmount=$billingAmount+$ride->driver_earning;
	
		}
		
		$bonus_balance = DB::table('dn_driver_dezibunus')->select('bonus_balance')->where('user_id',$id)->where('last_flag',1)->orderBy('id','desc')->first();
	
		$deziBonus = @$bonus_balance->bonus_balance;
		
		//echo "earningamount=".$earningamount."</br> billingAmount=".$billingAmount."</br> totalRideCount=".$totalRideCount;exit;
		//print_r($user);die;
		$data_user = DB::table('role_user')
					->select(array('dn_users.*','role_user.*','dn_cities.city','dn_states.state','dn_driver_requests.licence_expiration','dn_driver_requests.licence_expiration'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->join('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->join('dn_driver_requests', 'dn_driver_requests.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$id)->first(); // get data from 'dn_users' table.
					
					
		
		$driver_data = DB::table('dn_driver_requests')->where('user_id', $id)->first();
		$driver_meta_data=DB::table('dn_users_data')->where('user_id', $id)->first();
		//echo "<pre>"; print_r($driver_meta_data); die;
		return $this->view('driver.show',compact('user','userCars','age','totalRideCount','billingAmount','earningamount','deziBonus','lastRide','driver','revoke','ApName','userapprovedById','changeLog','data_user','driver_data','driver_meta_data'));
		
		
		
		
	 }

	 /**
	  * @Function for age calculator
	  * @Params: '$dob' is the date of birth 
	  */
	 public function driverNewApplicantDetail( $id=null )
	 {
		
		//$data['user'] = DB::table('dn_users')->where('id', $id)->first();
		

		$data['user'] = DB::table('role_user')
					->select(array('dn_users.*','role_user.user_id','dn_cities.city','dn_states.state','dn_driver_requests.licence_expiration','dn_driver_requests.licence_expiration','dn_driver_requests.insurance_expiration'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->join('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->join('dn_driver_requests', 'dn_driver_requests.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$id)->first(); // get data from 'dn_users' table.
					
		
		$data['driver_data'] = DB::table('dn_driver_requests')->where('user_id', $id)->first();
		$data['driver_meta_data']=DB::table('dn_users_data')->where('user_id', $id)->first();
		
		
		$dob = isset($data['user']->dob)?$data['user']->dob:"";
		
		if($dob !=""){
			$age= $this->ageCalculator($dob);
		}else{
			$age= 0;
		}
		$data['age'] = $age;
		//echo "<pre>";print_r($data); die;
		return $this->view( 'driver.newapplicant',$data);
	 }
	 
	  /**
	  * @Function for age calculator
	  * @Params: '$dob' is the date of birth 
	  */
	 public function suspendedApplicantDetail( $id=null )
	 {
		
		//$data['user'] = DB::table('dn_users')->where('id', $id)->first();


		$data['user'] = DB::table('role_user')
					->select(array('dn_users.*','role_user.*','dn_cities.city','dn_states.state','dn_driver_requests.licence_expiration','dn_driver_requests.licence_expiration'))
					->leftjoin('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->leftjoin('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->leftjoin('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->leftjoin('dn_driver_requests', 'dn_driver_requests.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$id)->first(); // get data from 'dn_users' table.
		//print_r($data['user']);die;
		$data['driver_data'] = DB::table('dn_driver_requests')->where('user_id', $id)->first();
		//print_r($data['driver_data'] );die;
		$data['driver_meta_data']=DB::table('dn_users_data')->where('user_id', $id)->first();
		

		return $this->view( 'driver.suspendedApplicantDetail', $data );
	 }
	 
	  /**
	  * @Function for age calculator
	  * @Params: '$dob' is the date of birth 
	  */
	 public function documentReviewApplicantDetail( $id=null )
	 {
		
		//$data['user'] = DB::table('dn_users')->where('id', $id)->first();


		$data['user'] = DB::table('role_user')
					->select(array('dn_users.*','role_user.*','dn_cities.city','dn_states.state','dn_driver_requests.licence_expiration','dn_driver_requests.licence_expiration'))
					->leftjoin('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->leftjoin('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->leftjoin('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->leftjoin('dn_driver_requests', 'dn_driver_requests.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$id)->first(); // get data from 'dn_users' table.

		$data['driver_data'] = DB::table('dn_driver_requests')->where('user_id', $id)->first();
		$data['driver_meta_data']=DB::table('dn_users_data')->where('user_id', $id)->first();
		

		return $this->view( 'driver.documentReviewApplicantDetail', $data );
	 }
	 
	 
	 /**
	  * @Function for age calculator
	  * @Params: '$dob' is the date of birth 
	  */
	 public function driverRejectedApplicantDetail( $id=null )
	 {
		
		//$data['user'] = DB::table('dn_users')->where('id', $id)->first();


		$data['user'] = DB::table('role_user')
					->select(array('dn_users.*','role_user.*','dn_cities.city','dn_states.state','dn_driver_requests.licence_expiration','dn_driver_requests.licence_expiration'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->join('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->join('dn_driver_requests', 'dn_driver_requests.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$id)->first(); // get data from 'dn_users' table.

		$data['driver_data'] = DB::table('dn_driver_requests')->where('user_id', $id)->first();
		$data['driver_meta_data']=DB::table('dn_users_data')->where('user_id', $id)->first();
		//echo "<pre>"; print_r($data); die; 
		//$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get(); 
		
		
		return $this->view( 'driver.driverRejectedApplicantDetail', $data );
	 }

 public function driverCharges(request $request)
	 {

	 	$adminId = Auth::id();
		$info=$request->all();
	 	$data = array();
		$data['states'] = DB::table('dn_states')->get();
		if(isset($info['stateCode']))
		{
			$stateCode=$info['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			// echo "<option value=''>---City---</option>";
		
			$htmlD = '<div class="row"><div class="col-sm-12">

		
		
        <!-- a -->
        <ul class="list-unstyled"><li><strong>A</strong>';

          if (!empty($cities)) { 
				
              foreach ($cities as $city) {if(ucfirst($city->city[0])=="A"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>
			  ';

			   }} } 
			   
			   $htmlD .='</li><li><strong>B</strong>';
			    if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="B"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			 }} } 
			 
			  $htmlD .='</li><li><strong>C</strong>';
			  if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="C"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
			   
				  $htmlD .='</li><li><strong>D</strong>';
			    if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="D"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
			   
 
  			$htmlD .='</li><li><strong>E</strong>';
            if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="E"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 
			  
			    $htmlD .='</li><li><strong>F</strong>';
			   if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="F"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 
			    $htmlD .='</li><li><strong>G</strong>';
			    if (!empty($cities)) { 
				
              foreach ($cities as $city) {if(ucfirst($city->city[0])=="G"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
			   $htmlD .='</li><li>';
			     $htmlD .='<strong>H</strong>';
			   if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="H"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			 }} } 
        
	  
          
            
        
            $htmlD .='</li><li><strong>I</strong>';
           if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="I"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
			   $htmlD .='</li><li><strong>J</strong>';
			    if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="J"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div><';

			   }} } 
			   
			   $htmlD .='</li><li><strong>K</strong>';
		 if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="K"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 
			  $htmlD .='</li><li><strong>L</strong>';
			  if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="L"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 

      
       $htmlD .='</li><li><strong>M</strong>';
          if (!empty($cities)) { 
				
              foreach ($cities as $city) {if(ucfirst($city->city[0])=="M"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
             $htmlD .='</li><li><strong>N</strong>';
			 if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="N"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			 }} } 
			  $htmlD .='</li><li><strong>O</strong>';
			 if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="O"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
			    $htmlD .='</li><li><strong>P</strong>';
			    if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="P"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
        $htmlD .= '</li><li><strong>Q</strong>';
            
         
            if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="Q"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 
			  
			   $htmlD .='</li><li><strong>R</strong>';
			   if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="R"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 
			   $htmlD .='</li><li><strong>S</strong>';
			   if (!empty($cities)) { 
				
              foreach ($cities as $city) {if(ucfirst($city->city[0])=="S"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
			    $htmlD .='</li><li><strong>T</strong>';
			  if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="T"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			 }} } 
            
			  
       
         
        $htmlD .= '</li><li><strong>U</strong>';
            
           if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="U"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
			    $htmlD .='</li><li><strong>V</strong>';
			    if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="V"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} }
			    $htmlD .='</li><li><strong>W</strong>';
			 if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="W"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 
			  
			   $htmlD .='</li><li><strong>X</strong><div class="clearfix"> </div>';
			  if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="X"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			  }} } 
			   $htmlD .='</li><li><strong>Y</strong>';
			   if (!empty($cities)) { 
				
              foreach ($cities as $city) {if(ucfirst($city->city[0])=="Y"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			   }} } 
            
       $htmlD .='</li><li><strong>Z</strong>';
            
          if (!empty($cities)) { 

              foreach ($cities as $city) { if(ucfirst($city->city[0])=="Z"){ 
            $htmlD .= '
              <div class="checkbox">
                <label><input type="checkbox" name="city_id[]" value="'.$city->id.'" class="city-check">'.$city->city.'</label>
              </div>';

			 }} } 

        $htmlD .='</li></ul></div>';
			echo $htmlD;exit;
		}
	 	if( !empty(Input::get('getCityCharges')) ){

	 		$view_city_id = Input::get('getCityCharges');

	 		$data['view_city_charges'] = DB::table('dn_driver_charges')	 			
	 			->where( function ($query) use ($view_city_id)  { 
					$query->where( 'city_id', $view_city_id )
                  	->where( 'is_active', 1 );
				})
	 			->first();

	 		if (!empty($data['view_city_charges'])) {
	 			
	 			$data['azStatus'] = 'success';

	 			echo json_encode($data);
	 			die();

	 		} else {
	 			$data['azStatus'] = 'error';
	 			$data['azMessage'] = 'No Charges Settled For This City';

	 			echo json_encode($data);
	 			die();
	 		}
	 		//echo "<pre>"; print($data['view_city_charges']); die();
	 	}

	 	if(Input::get('submit') == 'default'){

	 		$formData 	= Input::get();

	 		//update the default record for all the cities 
 			$cherge_where = array('id' => 1 );

            $update_default = DB::table('dn_driver_charges')
			->where( $cherge_where )
			->update([
				'cost_per_mile' => $formData['default_cost_per_mile'],
				'per_min_charge' => $formData['default_per_min_charge'],
				'less_mile_travel_cost' => $formData['default_less_mile_travel_cost'],
				'greater_mile_travel_cost' => $formData['default_greater_mile_travel_cost'],
				'service_charge' => $formData['default_service_charges'],
				'min_charge' => $formData['default_min_charge'],
				'cancelation_charge' => $formData['default_cancelation_charge']
				]);

			if ($update_default) {
				Session::flash('message', "Success: Charges has been saved successfully!"); 
				Session::flash('alert-class', 'alert-success');
			} else {
				Session::flash('message', "Error: On Saving Default Charges."); 
				Session::flash('alert-class', 'alert-danger');
			}
	 	}

	 	if(Input::get('submit') == 1){

		 	$formData 	= Input::get();
		 	//echo "<pre>"; print_r($formData);die;
	 		$is_schedule_charges 	= Input::get('is_schedule_charges');

	 		$city_ids 	= Input::get('city_id');
	 		$num_cities = count($city_ids); 

		 	
		 	if ( !empty($city_ids) ) {

		 		foreach ($city_ids as $city_id) {
		 			
			 		$default_cost_per_mile  	= Input::get('default_cost_per_mile');
			 		$default_per_min_charge  	= Input::get('default_per_min_charge');
			 		$default_less_mile_travel_cost  	= Input::get('default_less_mile_travel_cost');
			 		$default_greater_mile_travel_cost  	= Input::get('default_greater_mile_travel_cost');
			 		$default_service_charges  	= Input::get('default_service_charges');
			 		$default_min_charge  		= Input::get('default_min_charge');
			 		$default_cancelation_charge = Input::get('default_cancelation_charge');
			 		
			 		$day_id = Input::get('day_id');

			 		$day_start_time = Input::get('day_start_time');
			 		$day_end_time 	= Input::get('day_end_time');

			 		$cost_per_mile   = Input::get('cost_per_mile');
			 		$service_charge  = Input::get('service_charge');
			 		$per_min_charge  = Input::get('per_min_charge');
			 		$less_mile_travel_cost		= Input::get('less_mile_travel_cost');
			 		$greater_mile_travel_cost 	= Input::get('greater_mile_travel_cost');
			 		$service_charge = Input::get('service_charge');
			 		$min_charge 	= Input::get('min_charge');
			 		$cancelation_charge = Input::get('cancelation_charge');
			 		$is_active 	= 1;

			 			//update the previous record for this city id 
			 			$cherge_where = array('city_id' => $city_id,
			 				'is_active' => 1
			 			);

		                $update_log=DB::table('dn_driver_charges')
						->where( $cherge_where )
						->update(['is_active' => 0]);


						$insert =	DB::table('dn_driver_charges')
							->insert( [ 'city_id' 	=> $city_id,
								'day_number'		=> 0,
								'cost_per_mile'		=> $default_cost_per_mile,
								'per_min_charge'	=> $default_per_min_charge,
								'less_mile_travel_cost'		=> $default_less_mile_travel_cost,
								'greater_mile_travel_cost'	=> $default_greater_mile_travel_cost,
								'service_charge'=> $default_service_charges,
								'min_charge'	=> $default_min_charge,
								'cancelation_charge'	=> $default_cancelation_charge,
								'from_time'	=> '',
								'to_time'	=> '',
								'is_active'	=> $is_active
								]
							);

						//update the previous record for this city_id and day_id
						if ( $is_schedule_charges == 1) {

				 			$cherge_where = array('city_id' => $city_id,
					 				'day_number' => $day_id,
					 				'is_active' => 1
					 			);

				                $update_log=DB::table('dn_driver_charges')
								->where( $cherge_where )
								->update(['is_active' => 0]);

								$insert =	DB::table('dn_driver_charges')
									->insert( [ 'city_id' 	=> $city_id,
										'day_number'		=> $day_id,
										'cost_per_mile'		=> $cost_per_mile,
										'per_min_charge'	=> $per_min_charge,
										'less_mile_travel_cost'		=> $less_mile_travel_cost,
										'greater_mile_travel_cost'	=> $greater_mile_travel_cost,
										'service_charge'=> $service_charge,
										'min_charge'	=> $min_charge,
										'cancelation_charge'	=> $cancelation_charge,
										'from_time'	=> $day_start_time,
										'to_time'	=> $day_end_time,
										'is_active'	=> $is_active
										]
									);

				 		}				
		 		}

		 		if ($insert) {
	 				Session::flash('message', "Success: Charges has been saved successfully for $num_cities Cities!"); 
					Session::flash('alert-class', 'alert-success');
		 		} else {
		 			Session::flash('message', 'Error: There was an error while performing this operation!'); 
					Session::flash('alert-class', 'alert-danger');
		 		}

		 	} else {
		 		Session::flash('message', "Error: Please Select at least one city from the list!"); 
				Session::flash('alert-class', 'alert-danger');
		 	}

	 		
	 		//echo "<pre>"; print_r($name); echo "</pre>";
	 	}

	 	$data['cities']['a'] = DB::table('dn_cities')->take(10)->where('city', 'like', 'a%')->get();
	 	$data['cities']['b'] = DB::table('dn_cities')->take(10)->where('city', 'like', 'b%')->get();
	 	$data['cities']['c'] = DB::table('dn_cities')->take(10)->where('city', 'like', 'c%')->get();
	 	$data['cities']['d'] = DB::table('dn_cities')->take(10)->where('city', 'like', 'd%')->get();
	 	$data['cities']['e'] = DB::table('dn_cities')->take(10)->where('city', 'like', 'e%')->get();
	 	$data['cities']['f'] = DB::table('dn_cities')->take(10)->where('city', 'like', 'f%')->get();

	 	$where_day = array( 'is_active' => 1,
	 	'day_number' => 1 );

	 	//$data['day_charges'] = DB::table('dn_driver_charges')->where( $where_day )->get();
	 	$data['day_charges'] = array();
	 	$data['default_charges'] = DB::table('dn_driver_charges')->where('id', '1')->first();

	 	//$this->preDie($data['day_charges']);

		return $this->view('driver.driverCharges', $data );
	}
	/**
	 * Get all the list of searched cities
	 *
	 * @param : string
	 * @return Get all the list of searched cities
	 * 
	 * Author : Harish Chauhan
	 */

	public function cityAjax()
	{
		$cityNameLike  = trim( Input::get('cityNameLike') );

		$data['cities'] = DB::table('dn_cities')->take(60)->where('city', 'like', $cityNameLike.'%')->get();
		
		$html = "<div class='row'>";
        	$html .= "<div class='col-sm-12'><strong>City Names Search For City Names Like $cityNameLike</strong></div>";
      	$html .= "</div>";
		
		$html .= "<div class='row'>";

		foreach ($data['cities'] as $city) {
			$html .= "<div class='col-sm-2'>";
				$html .= "<div class='checkbox'>";
	               
					
					$html .= "<label><input type='checkbox' name='city_id[]' value='".$city->id."' class='city-check'>".$city->city." (".$city->state_code.")</label>";
	            $html .= "</div>";
			$html .= "</div>";
		}
			
      	$html .= "</div>";

      	echo $html;

		die();
	}

	/**
	 * Get all the Week charges
	 *
	 * @param : string
	 * @return Get all the list of searched week charges
	 * 
	 * Author : Harish Chauhan
	 */

	public function dayChargesAjax()
	{
		$data = array();

		$dayID  = trim( Input::get('dayID') );

      	$where_day = array( 'is_active' => 1,
	 	'day_number' => $dayID );

	 	$data['day_charges'] = DB::table('dn_driver_charges')->where( $where_day )->get();

      	echo json_encode($data);

		die();
	}
	
	public function driverChargeUpdate(REQUEST $request)
	{
		$data= $request->all();
		$request=(array)json_decode($data['request']);
		
		print_r($request);
		if(!empty($request['id'])){
			$updates=DB::table('dn_driver_charges')
			->where('id',$request['id'])
			->where('day_number', '!=' , 0)
			->update($request);
		}else{
			  unset($request['id']);
				DB::table('dn_driver_charges')
			    ->insert($request);
		}
		
		return ;
	}


	public function wkdayCharges(REQUEST $request)
	{
			$data= $request->all();
			$day_id=$data['dayid'];
			$city_id=$data['CityId'];
			$day_charges = DB::table('dn_driver_charges')->where('city_id',$city_id )->where('day_number',$day_id )->orderBy('created_on','DESC')->first();

      	//echo json_encode($data);
		print_r(json_encode((array)$day_charges)); die;
		 //console.log($day_charges);die;
	}
	/**
	 * Print An Array And Die The Array
	 *
	 * @param : array
	 * @return Print Array In Pre Format
	 * 
	 * Author : Harish Chauhan
	 */
	public function preDie($value='')
	{
		echo "<pre>"; print_r($value); die();
	}

	/**
	 * Print An Array The Array
	 *
	 * @param : array
	 * @return Print Array In Pre Format
	 * 
	 * Author : Harish Chauhan
	 */
	public function pre($value='')
	{
		echo "<pre>"; print_r($value); echo "</pre>";
	}

	public function driverChargespdf (Request $request)
	{
		  /* initializing variables */
		
		$data = $request->all();
		//print_r($data);
		//echo "</pre>";exit;
		if(!empty(@$data['from']) && !empty(@$data['to'])){
				$time=strtotime($data['from']);
				$from = date('Y-m-d H:i:s',$time);
				$timeTo=strtotime($data['to']);
				$to = date('Y-m-d H:i:s',$timeTo);
				$response = DB::table('dn_driver_charges')
							->whereBetween('created_on', array( $from , $to))
							->get();
			}else{
				$dates = DB::table('dn_driver_charges')
							->select(DB::raw('MAX(created_on) as maxdate,MIN(created_on) as mindate'))
							->first();	
				$response = DB::table('dn_driver_charges')->get();
				$from=$dates->mindate;
				$to=$dates->maxdate;
			}
		$html= ' <tr><th colspan="9" style="text-align:center"><p> FROM &nbsp;<small>&nbsp;'.$from.'&nbsp;</small> &nbsp;TO &nbsp;<small>&nbsp;'.$to.'&nbsp;</small>&nbsp; </p></th></tr>';
		
		$html.='<table style="border 1px solid blue ">
			  <tr>
			    <th>City Name</th>
			    <th>Cost per Mile</th>
			    <th>Per Minute charges</th>
			    <th>Less than 2 mile travel cost</th>
			    <th>Greater than 2 mile travel cost</th>
			    <th>Service charge</th>
			    <th>Minimum charge</th>
			    <th>Cancellation charge</th>
			    <th>TimeStamp</th>
			

			  </tr>';
		foreach ($response as $key => $Resdata) {
			$cityName=DB::table('dn_cities')->select('city','state_code')->where('id',@$Resdata->city_id)->first();
			//print_r($Resdata);
			$html= $html."
					 <tr>
				    <td>".@$cityName->city.' [ '.@$cityName->state_code.' ] '."</td>
				    <td>".@$Resdata->cost_per_mile."</td>
				    <td>".@$Resdata->per_min_charge."</td>
				    <td>".@$Resdata->less_mile_travel_cost."</td>
				    <td>".@$Resdata->greater_mile_travel_cost."</td>
				    <td>".@$Resdata->service_charge."</td>
				    <td>".@$Resdata->min_charge."</td>
				    <td>".@$Resdata->cancelation_charge."</td>
				    <td>".@$Resdata->created_on."</td>
				    </tr>
		
			";
		}

		$html=$html."</table>";
		
		/*return \Excel::create('newdriver', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');*/
 		return \PDF::loadHTML($html)->download('Drivercharges.pdf');
 		//PDF::loadHTML($html)->setPaper('a4')->setOrientation('landscape')->setOption('margin-bottom', 0)->save('Drivercharges.pdf')
 		//return redirect('driver/driverCharges');
 	}

 	
 	public function ridedetail($id=null) {

	
		
		$ride_id = convert_uudecode(base64_decode($id));
		
		$totalRecords['ridedetail']= DB::table('dn_rides')
							->select(array('dn_rides.*'))
							->where('id',$ride_id)
							->first();
		
		/* NEW DRIVER EARNING CONCEPT   */
		
			$driverEarning=DB::table('dn_rides')
									->leftjoin('dn_payments', 'dn_payments.ride_id', '=', 'dn_rides.id')
									->where('dn_rides.id',$ride_id)
									->sum('dn_payments.driver_earning');
									
									
	
		
		
		
		/*    */
		
		
		
		/*Payment Refund Section*/
		$refund_driver_id = $totalRecords['ridedetail']->driver_id;

		$sql="Select dn_rides.id as rideId, dn_rides.payment_status, dn_rides.charge_id, dn_rides.status, dn_rides.driver_level, dn_payments.amount, dn_payments.dezicredit as deziCredit, dn_payments.payment_id as TXN_ID, dn_payments.tip_percentage as tip, dn_payments.tip_refund, dn_payments.refund_amount, dn_payment_accounts.account_type, dn_payment_accounts.masked_number from dn_rides ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.="LEFT join dn_payment_accounts on dn_rides.payment_id = dn_payment_accounts.id ";
		
		$dataSQL = $sql." where dn_rides.driver_id = $refund_driver_id AND dn_rides.id = $ride_id LIMIT 1";
		$ride_data['collected_ride_data'] = DB::select(DB::raw($dataSQL));

		//echo "<pre>"; print_r($ride_data['collected_ride_data']); die();
		/* /Payment Refund Section*/
					
		$totalRecords['driverdetail']= DB::table('dn_users')
							->select(array('dn_users.unique_code','dn_users.first_name','dn_users.last_name','dn_users.gender','dn_users.dob','dn_users.profile_pic','dn_users.anniversary','dn_users.email','dn_users.contact_number'))
							->where('id',$totalRecords['ridedetail']->driver_id)
							->first();	
		$totalRecords['passengerdetail']= DB::table('dn_users')
							->select(array('dn_users.unique_code','dn_users.first_name','dn_users.last_name','dn_users.gender','dn_users.dob','dn_users.contact_number','dn_users.profile_pic','dn_users.anniversary','dn_users.email'))
							->where('id',$totalRecords['ridedetail']->passenger_id)
							->first();	
		$totalRecords['paymentdetail']= DB::table('dn_payments')
							->select(array('dn_payments.*'))
							->where('ride_id',$totalRecords['ridedetail']->id)
							->first();
		$totalRecords['billingInfo']= DB::table('ride_billing_info')
							->select(array('ride_billing_info.miles','ride_billing_info.duration','ride_billing_info.miles_charges','ride_billing_info.duration_charges','ride_billing_info.tip','ride_billing_info.pickup_fee'))
							->where('ride_id',$totalRecords['ridedetail']->id)
							->first();
		
		$ride_data['ride_issues'] = DB::table('dn_report_an_issuse')
						->select('dn_report_an_issuse.*', 'dn_cancellation_category.category as main_category', 'dn_cancellation_subcategory.subcategory as sub_category')
						
						->leftJoin('dn_cancellation_category', 'dn_cancellation_category.id', '=', 'dn_report_an_issuse.category')	
						->leftJoin('dn_cancellation_subcategory', 'dn_cancellation_subcategory.id', '=', 'dn_report_an_issuse.sub_category')
						->where('dn_report_an_issuse.ride_id',$ride_id)
						->get();

		//echo "<pre>"; print_r($ride_data['ride_issues']); die();

		$ride_data['rating_received'] = DB::table('dn_rating')	
						->select('dn_rating.rate_by', 'dn_rating.rating')
						->where('ride_id',$ride_id)
						->get();
					
		return $this->view('driver.driverRideDetail',compact('totalRecords','ride_data','ride_id','driverEarning'));
 	}


  	public function delete(Request $request)
	{
		/* initializing variables */
		  
		$data = $request->all();
		$id = $data['id']; 
		$adminId = Auth::id();
		$actionType=$data['action'];

		if($actionType=='dltSuccess')
		{

			$user_data = DB::table('dn_users')	
				->select('dn_users.contact_number')
				->where('id',$id)
				->first();

			$contact_number = $user_data->contact_number;

			$user_delete=DB::table('dn_users')
			->where('id', $id)
			->delete();

			$user_role_delete=DB::table('role_user')
			->where('user_id', $id)
			->delete();

			$active=DB::table('dn_user_verification')
			->where('contact_number', $contact_number)
			->delete();
					
			
			if($user_delete){
				if ($actionType=='dltSuccess') { echo "dltSuccess"; }
			
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'Driver_Deleted','added_by'=>$adminId]);
			
			}else{
				echo "error";
			}
			
		}

		exit;
	 }		

	public function bankdetails(Request $request)
	{
		
		/* initializing the variables */
			$data = $request->all();
			$limit = 10;
			$draw = @$data['draw'];
			$offset = @$data['start'];
			$searchString=$data['search']['value'];
			$driverId = @$data['driverId'];
			$orderfields=array('0'=>'id','1'=>'bank_name','2'=>'acc_number','3'=>'routing_number','4'=>'branch');
			
			$field='id';
			$direction='ASC';
			
			/* code for order by data of user*/
			if(!empty($data['order'][0])){
				foreach($orderfields as $key=>$orderfield){
					if($key==$data['order'][0]['column'] )
					{
						$field=$orderfield;
						$direction=$data['order'][0]['dir'];
					}
				}
			}
			
			/* code for searching of  user*/
			$sql = "Select * from dn_driver_bank_detail where user_id = $driverId ";
			
			if(@$searchString!='')
			{	
				$search = "%$searchString%";
				$sql .=" AND  (bank_name LIKE '$search' or acc_number LIKE '$search' or routing_number LIKE '$search' or branch LIKE '$search') ";
			}
			$totalRecords=0;
			$totalRecords=DB::select(DB::raw($sql));
			$sql .= " order by ".$field." ".$direction;
			$sql .= " Limit ".$offset." , ".$limit;
			$bankData=DB::select(DB::raw($sql));
			
			
			
			$Data="";
			foreach($bankData as $data)
			{
				//echo $user->active;
				$bank_name =$data->bank_name;
				$acc_number = $data->acc_number;
				$routing_number = $data->routing_number;
				$branch=$data->branch;

				if(empty($bank_name))
				{
					$bank_name="N/A";
				}
				if(empty($acc_number))
				{
					$acc_number="N/A";
				}
				if(empty($routing_number))
				{
					$routing_number="N/A";
				}
				if(empty($branch))
				{
					$branch="N/A";
				}
				
				$Data[]= "[". 
							'"'.++$offset .'"' . ",". 
							'"'.$bank_name .'"' . ",". 
							'"'.$acc_number .'"'.",". 
							'"'.$routing_number.'"' .",". 
							'"'.$branch.'"'."]";
			}
			if(!empty($Data)){
				$newData=implode(',',$Data);	
				//echo '<pre>';print_r($newData);die;
						return '{
				  "draw": '.$draw.',
				  "recordsTotal": '.count($totalRecords).',
				  "recordsFiltered":'.count($totalRecords).',
				  "data": ['.$newData.']
			}';} else {
				return '{
				  "draw": '.$draw.',
				  "recordsTotal": 0,
				  "recordsFiltered":0,
				  "data": []
				}';
			}
		
	}	 
	/* ------------------------+------- */

	/**
	 * @FUNCTION FOR Driver deziBonus
	 * @Author : Gaurav Kumar
	 *
	**/	
	public function DriverCredit(Request $request) {

		$data = $request->all();
		
		$user_id = $data['user_id'];
		$credit_type 	= 1; //1=DeziCredit By Admin, 2=referralCredit, 2=PromoCredit
		$credit_amount = $data['credit_amount'];
		$credit_by 	= Auth::id();
		$credit_txn_type 	= 'Cr'; //Cr = Credit, Dr = Debit

		$deziCredit = DB::table('dn_driver_dezibunus')->where('user_id',$user_id)->orderBy('id', 'desc')->first();		

		if ( !empty($deziCredit)) {

			$credit_balance = $deziCredit->bonus_balance + $credit_amount;

			$insertCreditID = DB::table('dn_driver_dezibunus')
			->insert(
				['user_id' => $user_id, 
				'bonus_type'=> $credit_type,
				'bonus_by' => $credit_by,
				'bonus_txn_type' => $credit_txn_type,
				'bonus_amount'=>$credit_amount,
				'bonus_balance'=>$credit_balance,
				'last_flag'=>'1']
			);

		} else {

			$insertCreditID = DB::table('dn_driver_dezibunus')
			->insert(
				['user_id' => $user_id, 
				'bonus_type'=> $credit_type,
				'bonus_by' => $credit_by,
				'bonus_txn_type' => $credit_txn_type,
				'bonus_amount'=>$credit_amount,
				'bonus_balance'=>$credit_amount,
				'last_flag'=>'1']
			);
		}
		
		$data['azStatus']	= 'success';
		$data['azMessage']	= 'The DeziCredit is successfully added to user account.';

		echo json_encode($data);
		die();
	}
	/**/
	
	
	public function userSSN(Request $request) {
		$data=$request->all();
		$ssn=$data['ssn'];
		$user_id=$data['user_id'];
		DB::table("dn_users_data")->where('user_id',$user_id)->update(['ssn'=>$ssn]);
		$data['azStatus']	= 'success';
		$data['azMessage']	= 'The SSN is successfully added to user account.';
		echo json_encode($data);
		die();
		
	}
	/*  Price Check     */
	
	/* ------------------------+------- */
	/**
	 * Display a listing of New driver Users
	 * Author : Harish Chauhan
	 * @return Response
	 */

	public function newSuspnededList(Request $request){

		$data = $request->all();
		//DB::enableQueryLog();
		//get all new driver applicant users
		$newApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.is_suspended', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})

						->paginate(config('admin.user.perpage'));

		//dd(DB::getQueryLog()); 

		$countNewApplicants = count($newApplicants);
		
		//get all driver users
		$rejectedApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
			
						//->where('role_id','4') 

						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.is_suspended', 3 );
						})

						->paginate(config('admin.user.perpage'));
		
		$countRejectedApplicants = count($rejectedApplicants);
		

		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.is_suspended', 2 );
						})
						->paginate(config('admin.user.perpage'));
						//echo "<pre>"; print_r($newApplicants); echo "</pre>"; die('newApplicants');

		$states = DB::table('dn_states')->get();
		
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
		}


		$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.city) as no_of_users')))
					->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					
					//->where('dn_users.city','!=','')

					->where( function ($query) {
						$query->where( 'dn_users.city','!=','' );
                      	//->where( 'role_id', '4' );
					})
					
					->groupBy('dn_users.city')
					->orderBy('no_of_users')
					->get();

		//$this->preDie( $citys ); @Harish#1

		//DB::enableQueryLog();


		$n=count($citys)-1;
		if($n==0)
		{
			
			$citiesCount=array('least'=>$citys,'most'=>$citys[$n]); 
		}
		@$citiesCount=array('least'=>$citys[0],'most'=>$citys[$n]); 
		return $this->view('driver.newSuspnededList', compact( 'newApplicants', 'rejectedApplicants', 'users','citiesCount','states'));
	}	
	
	/**
	 * @FUNCTION FOR AJAX CALL FOR NEW DRIVER AJAX
	 * @Author : Harish Chander 
	 * @Params : $request
	 **/

 	public function newSuspendedListAjax(Request $request) 
 	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$searchString=$data['search']['value'];
		$startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','5'=>'email','6'=>'contact_number','8'=>'is_logged');
		//print_r($data['order'][0]); die();
		
		$field='id';
		$direction='ASC';
		
		/* code for order by data of user*/
		if(!empty($data['order'][0])) 
		{
			foreach($orderfields as $key=>$orderfield)
			{
				if( $key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		/* code for searching of  user*/
		$sql = 'SELECT id FROM dn_users WHERE 1=1 ';

		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
		}
		if($data['state']!='')
		{
			$state = $data['state'];
			$sql .=" AND  state= '$state'";
		}
		if($data['city']!='')
		{
			 $city = $data['city'];
			 $sql .=" AND  city= '$city'";
		}
		if($data['state']!='' && $data['city']!='')
		{
			$state = $data['state'];
			$city = $data['city'];
			$sql .="AND  state= '$state' AND  city= '$city'";
		}
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sql .=" AND  (first_name LIKE '$search' or last_name LIKE '$search' or full_name LIKE '$search' or email LIKE '$search'  or contact_number LIKE '$search') ";
		}
		
		$sql .= " order by ".$field." ".$direction;
		$usersIds=DB::select(DB::raw($sql));
		if(!empty($usersIds))
		{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
		}
		$users = array();
		$totalRecords = 0;

		if(!empty($usersList))
		{
			/* code for fetching data from dn_users table */

			$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
							->where( 'dn_users.is_suspended', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->take($limit)->offset($offset) ->orderBy($field,$direction)->get();

			$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
							->where( 'dn_users.is_suspended', 1 )
	                      	->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->paginate(config('admin.user.perpage'));
		}
		$Data="";
		foreach($users as $user)
		{
			//echo $user->active;
			$first_name =$user->first_name;
			$last_name =$user->last_name;
			$email=$user->email;
			$phone=$user->contact_number;
			$state=$user->state;
			$city=$user->city;
		

			$active 	= '';
			$view 		= "<span class='label-info label'><a href='".route("suspendedApplicantDetail",$user->id)."'> View </a></span>";	
			$actionDEF = 'N/A';
			$action = '';
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');
			//echo "<pre>"; print_r($loggedInUserPermission); die;
			foreach($loggedInUserPermission as $k=>$allModule){
				$allMod[]= $allModule->module_slug;
				$allModPer[$allModule->module_slug]= $allModule;
			}
			if(empty($loggedInUserPermission)){
				
				/*$action 	= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data-action='btndriver_approve' data-userid=".$user->id." >Approve</a></span></br>";
				$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_disapprove' data-action='btndriver_disapprove' data-userid=".$user->id." >Disapprove</a></span></br>".$view;*/
				// $action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_dlt' data-action='btndriver_dlt' data-userid=".$user->id." >Delete</a></span></br>".$view;
				$action=''.$view; 
			}elseif(!empty($loggedInUserPermission)){
			foreach($loggedInUserPermission as $userPermission){
				
				if($userPermission->module_slug=="driver_applicants" && $userPermission->edit_permission==1){
								/*$action 	= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data- action='btndriver_approve' data-userid=".$user->id." >Approve</a></span></br>";
								$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_disapprove' data-action='btndriver_disapprove' data-userid=".$user->id." >Disapprove</a></span></br>".$view;*/
								// $action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_dlt' data-action='btndriver_dlt' data-userid=".$user->id." >Delete</a></span></br>$view";
								$action=''.$view; 
						}else
					
					/*Inner condition end*/	
					if($userPermission->module_slug=="driver_applicants" && $userPermission->view_permission==1){
					$action .= $view ;	
						
					}
				}
			} else{
				$action = "N/A";
			} 
			 
			//Subadmin Permission Code End
		
			if($user->is_logged=='true') {
				$is_logged='Yes';
			}else{
				$is_logged='No';
			} 
			if(empty($first_name))
			{
				$first_name="N/A";
			}
			if(empty($last_name))
			{
				$last_name="N/A";
			}
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($phone))
			{
				$phone="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($city))
			{
				$city="N/A";
			}
			$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data))
		{
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.count($totalRecords).',
			  "recordsFiltered":'.count($totalRecords).',
			  "data": ['.$newData.']
			}';
		} 
		else 
		{
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
					
	}
	
	
	public function newdocumentReviewList(Request $request){

		$data = $request->all();
		//DB::enableQueryLog();
		//get all new driver applicant users
		$newApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.is_suspended',3 )
							->where( 'dn_users.is_driver_approved', 0 );
						})

						->paginate(config('admin.user.perpage'));

		//dd(DB::getQueryLog()); 

		$countNewApplicants = count($newApplicants);
		
		//get all driver users
		$rejectedApplicants = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4') 
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.is_suspended',3 )
							->where( 'dn_users.is_driver_approved', 0 );
						})
						->paginate(config('admin.user.perpage'));
		
		$countRejectedApplicants = count($rejectedApplicants);
		

		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.is_suspended',3 )
							->where( 'dn_users.is_driver_approved', 0 );
						})
						->paginate(config('admin.user.perpage'));
						//echo "<pre>"; print_r($newApplicants); echo "</pre>"; die('newApplicants');

		$states = DB::table('dn_states')->get();
		
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
		}
		$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.city) as no_of_users')))
					->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					//->where('dn_users.city','!=','')
					->where( function ($query) {
						$query->where( 'dn_users.city','!=','' );
                      	//->where( 'role_id', '4' );
					})
					->groupBy('dn_users.city')
					->orderBy('no_of_users')
					->get();

		//$this->preDie( $citys ); @Harish#1

		//DB::enableQueryLog();


		$n=count($citys)-1;
		if($n==0)
		{
			
			$citiesCount=array('least'=>$citys,'most'=>$citys[$n]); 
		}
		@$citiesCount=array('least'=>$citys[0],'most'=>$citys[$n]); 
		return $this->view('driver.newdocumentReviewList', compact( 'newApplicants', 'rejectedApplicants', 'users','citiesCount','states'));
	}	
	
	/**
	 * @FUNCTION FOR AJAX CALL FOR NEW DRIVER AJAX
	 * @Author : Harish Chander 
	 * @Params : $request
	 **/

 	public function newdocumentReviewListAjax(Request $request) 
 	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$searchString=$data['search']['value'];
		$startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','5'=>'email','6'=>'contact_number','8'=>'is_logged');
		//print_r($data['order'][0]); die();
		
		$field='id';
		$direction='ASC';
		
		/* code for order by data of user*/
		if(!empty($data['order'][0])) 
		{
			foreach($orderfields as $key=>$orderfield)
			{
				if( $key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		/* code for searching of  user*/
		$sql = 'SELECT id FROM dn_users WHERE 1=1 ';

		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
		}
		if($data['state']!='')
		{
			$state = $data['state'];
			$sql .=" AND  state= '$state'";
		}
		if($data['city']!='')
		{
			 $city = $data['city'];
			 $sql .=" AND  city= '$city'";
		}
		if($data['state']!='' && $data['city']!='')
		{
			$state = $data['state'];
			$city = $data['city'];
			$sql .="AND  state= '$state' AND  city= '$city'";
		}
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sql .=" AND  (first_name LIKE '$search' or last_name LIKE '$search' or full_name LIKE '$search' or email LIKE '$search'  or contact_number LIKE '$search') ";
		}
		
		$sql .= " order by ".$field." ".$direction;
		$usersIds=DB::select(DB::raw($sql));
		if(!empty($usersIds))
		{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
		}
		$users = array();
		$totalRecords = 0;

		if(!empty($usersList))
		{
			/* code for fetching data from dn_users table */

			$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						//->where('role_id','4')
						//driver users role_id = 4 = Driver
						->where( function ($query) { 
							$query->whereIn('role_id', [3,4])
	                      	->where( 'dn_users.is_suspended',3 )
							->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->take($limit)->offset($offset) ->orderBy($field,$direction)->distinct()->get();
						//print_r($users);
			
			$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						//->where('role_id','4')
						->where( function ($query) { 
							$query->where( 'role_id', '3' )
	                      	->where( 'dn_users.is_suspended',3 )
							->where( 'dn_users.is_driver_approved', 0 );
						})
						->whereIn('role_user.user_id',$usersList)
						->orderBy($field,$direction)->distinct()->get();
		}
		$Data="";
		foreach($users as $user)
		{
			//echo $user->active;
			$first_name =$user->first_name;
			$last_name =$user->last_name;
			$email=$user->email;
			$phone=$user->contact_number;
			$state=$user->state;
			$city=$user->city;
		

			$active 	= '';
			$view 		= "<span class='label-info label'><a href='".route("documentReviewApplicantDetail",$user->id)."'> View </a></span>";	
			$actionDEF = 'N/A';
			$action = '';
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');
			//echo "<pre>"; print_r($loggedInUserPermission); die;
			foreach($loggedInUserPermission as $k=>$allModule){
				$allMod[]= $allModule->module_slug;
				$allModPer[$allModule->module_slug]= $allModule;
			}
			if(empty($loggedInUserPermission)){
				
				$action 	= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data-action='btndriver_approve' data-userid=".$user->id." >Approve</a></span></br>";
				$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_disapprove' data-action='btndriver_disapprove' data-userid=".$user->id." >Disapprove</a></span></br>".$view;;
				// $action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_dlt' data-action='btndriver_dlt' data-userid=".$user->id." >Delete</a></span></br>".$view;

			}elseif(!empty($loggedInUserPermission)){
			foreach($loggedInUserPermission as $userPermission){
				
				if($userPermission->module_slug=="driver_applicants" && $userPermission->edit_permission==1){
								$action 	= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn driver_approve' data-action='btndriver_approve' data-userid=".$user->id." >Approve</a></span></br>";
								$action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_disapprove' data-action='btndriver_disapprove' data-userid=".$user->id." >Disapprove</a></span></br>".$view;;
								// $action 	.= "<span> <a href='javascript:void(0);' class='btn btn-danger width-btn driver_dlt' data-action='btndriver_dlt' data-userid=".$user->id." >Delete</a></span></br>$view";
							
						
						}else
					
					/*Inner condition end*/	
					if($userPermission->module_slug=="driver_applicants" && $userPermission->view_permission==1){
					$action .= $view ;	
						
					}
				}
			} else{
				$action = "N/A";
			} 
			 
			//Subadmin Permission Code End
			
			
			
			/*if($user->active==1) {
				$active='Active';
				$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
				// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
				
			}else{
				$active='Suspended';
				$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Active</a></span>&nbsp;|&nbsp;".$view;
			}*/ 
			
			if($user->is_logged=='true') {
				$is_logged='Yes';
			}else{
				$is_logged='No';
			} 
			if(empty($first_name))
			{
				$first_name="N/A";
			}
			if(empty($last_name))
			{
				$last_name="N/A";
			}
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($phone))
			{
				$phone="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($city))
			{
				$city="N/A";
			}
			$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.'"'."]";
		}
		if(!empty($Data))
		{
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.count($totalRecords).',
			  "recordsFiltered":'.count($totalRecords).',
			  "data": ['.$newData.']
			}';
		} 
		else 
		{
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
					
	}
	
}

