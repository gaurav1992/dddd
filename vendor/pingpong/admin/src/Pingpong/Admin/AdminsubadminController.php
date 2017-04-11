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
use Redirect;
use Datatables;
use Session;
use Hash;
use Crypt;
use Pingpong\Admin\Uploader\ImageUploader;
use Mail;
use DateTime;

use App\User;

class AdminsubadminController extends BaseController
{

    /**
     * @var \User
    */

    protected $users;

    /**
     * CRON FOR CALCULATING DRIVER TIER LEVEL
     *
    **/

    public function _update_deziBonus( $user_id, $referal_credit){

		$sql1 = "SELECT bonus_balance FROM `dn_driver_dezibunus` WHERE last_flag = 1 AND user_id=$user_id";
		$amount = DB::select($sql1);

		$total_balance =0;
		$total_balance += @$amount[0]->bonus_balance + @$referal_credit;
		//print_r($referal_credit_for_20);die;
		$sql2 = "UPDATE `dn_driver_dezibunus` SET  `last_flag`= 0 WHERE last_flag = 1 AND user_id=$user_id";
		DB::statement($sql2);

		$insertBonusSQL = "INSERT INTO dn_driver_dezibunus (user_id, bonus_type, bonus_amount, bonus_balance, bonus_txn_type, bonus_by,last_flag) VALUES ($user_id, '1', ".@$referal_credit .", ".@$total_balance.", 'Cr', '0','1')";

		//DB::table('dn_driver_tier_data')->insert($insert);
		DB::statement($insertBonusSQL);

		echo '0 20 ';
    }

    public function cronDriverReferralBonus()
    {
		
    	//die('cronDriverReferralBonus');
    	$current_datetime = date( 'Y-m-d H:i:s');

    	$bonus_data = DB::table('dn_driver_promos')
					->select(array('dn_driver_promos.*'))
					->where('dn_driver_promos.is_active', '1' )
					->first();

		$referal_credit_for_20 		= $bonus_data->referal_credit_for_20;
		$referal_credit_for_5_10 	= $bonus_data->referal_credit_for_5_10;


		$allReferralDriver = DB::table('dn_user_referrals')
							->select(array('dn_user_referrals.*'))
							->where('dn_user_referrals.status', 0 )
							->where('dn_user_referrals.referral_type', 4 )
							->get();

		foreach ($allReferralDriver as $referralDriver) {
			
			//echo "<pre>"; print_r($referralDriver); die();

			$driver_id 		= $referralDriver->user_id;
			$referred_by 	= $referralDriver->referred_by;

			$driver_bonus_level = $referralDriver->driver_bonus_level; //0=none, 1= completed 5 rides, 2=Completed 10 rides, 3=Completed 20 rides

			$rideCount = DB::table('dn_rides') 
								->select(array('dn_rides.id')) 
								->where( function ($query) use ($driver_id)  { 
									$query->where( 'driver_id', $driver_id )
			                      	->where( 'status', 2 );
								})->count();
			
			if ($driver_bonus_level == 0 && $rideCount >= 5) {
				# do nothing
				$update_bonus_level=DB::table('dn_user_referrals')
				->where('user_id', $driver_id)
				->where('referred_by', $referred_by)
				->update(['driver_bonus_level' => 1, 'updated_at' => $current_datetime]);

				$this->_update_deziBonus( $user_id=$referred_by, $referal_credit_for_5_10);

				
			} elseif ($driver_bonus_level == 1 && $rideCount >= 10) {
				# do nothing
				$update_bonus_level=DB::table('dn_user_referrals')
				->where('user_id', $driver_id)
				->where('referred_by', $referred_by)
				->update(['driver_bonus_level' => 2, 'updated_at' => $current_datetime]);
				
				$this->_update_deziBonus( $user_id=$referred_by, $referal_credit_for_5_10);

			} elseif ($driver_bonus_level == 2 && $rideCount >= 20) {
				# do nothing

				$update_bonus_level=DB::table('dn_user_referrals')
				->where('user_id', $driver_id)
				->where('referred_by', $referred_by)
				->update(['driver_bonus_level' => 3, 'status' => 1, 'ride_taken' => 1, 'updated_at' => $current_datetime]);

				$this->_update_deziBonus( $user_id=$referred_by,  $referal_credit_for_20);

			} else {
				# do nothing
			}

		}

		die('cronDriverReferralBonus');
    }


    /**
     * CRON FOR CALCULATING DRIVER TIER LEVEL
     *
    **/

    public function _check_to_reset_tier_level( $day_number ){
    	
    	$tier_data = DB::table('dn_driver_tier_data')->select(array('dn_driver_tier_data.reset_status'))->first();
    	
    	//echo "<pre>"; print_r($tier_data); die(' _check_to_reset_tier_level');
    	
    	if ( $tier_data->reset_status == 6 && $day_number == 0) {
    		
    		$reset = [ 'reset_status' => $day_number,
    					'total_rides' => 0,
    					'total_active_hours' => 0,
    					'cancelation_rate' => 0,
    					'acceptance_rate' => 0
    					];
			DB::table('dn_driver_tier_data')->update($reset);
    	
    	} else {
    		
    		$update = [ 'reset_status' => $day_number ];
			DB::table('dn_driver_tier_data')->update($update);
    	}
    }

	public function _update_tier_level( $driver_id, $current_tier_level, $total_active_hours, $scheduled_hours, $cancelation_rate, $acceptance_rate)
	{

	   $bonus_data = DB::table('dn_driver_bonus')->select(array('*'))->where('is_active','1')->first();

	   //echo "<pre>"; print_r($bonus_data); echo "</pre>";

	   if ($current_tier_level == 0) {
	   		
	   		$total_hrs 	= $bonus_data->total_hrs_silver;
	   		$total_hrs_schedule = $bonus_data->total_hrs_schedule_silver;
	   		$cancellation 	= $bonus_data->cancellation_silver;
	   		$acceptance 		= $bonus_data->acceptance_silver;

	   		if ( $total_active_hours >= $total_hrs && $scheduled_hours >= $total_hrs_schedule && $cancelation_rate <= $cancellation && $acceptance_rate >= $acceptance) {
	   			
	   			$update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 1]);
	   			
	   			echo 'DRIVER : '.$driver_id.' '; die('SILVER');
	   		}

	   } elseif ($current_tier_level == 1) {
	   		
	   		$total_hrs = $bonus_data->total_hrs_gold;
	   		$total_hrs_schedule = $bonus_data->total_hrs_schedule_gold;
	   		$cancellation = $bonus_data->cancellation_gold;
	   		$acceptance = $bonus_data->acceptance_gold;

	   		if ( $total_active_hours >= $total_hrs && $scheduled_hours >= $total_hrs_schedule && $cancelation_rate <= $cancellation && $acceptance_rate >= $acceptance) {
	   			
	   			$update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 2]);
	   			echo 'DRIVER : '.$driver_id.' '; die('GOLD');
	   		}

	   } elseif ($current_tier_level == 2) {
	   		
	   		$total_hrs = $bonus_data->total_hrs_platinum;
	   		$total_hrs_schedule = $bonus_data->total_hrs_schedule_platinum;
	   		$cancellation = $bonus_data->cancellation_platinum;
	   		$acceptance = $bonus_data->acceptance_platinum;

	   		if ( $total_active_hours >= $total_hrs && $scheduled_hours >= $total_hrs_schedule && $cancelation_rate <= $cancellation && $acceptance_rate >= $acceptance) {
	   			
	   			$update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 3]);
	   			echo 'DRIVER : '.$driver_id.' '; die('PLATINUM');
	   		}

	   } elseif ($current_tier_level == 3) {
	   		
	   		$total_hrs = $bonus_data->total_hrs_diamond;
	   		$total_hrs_schedule = $bonus_data->total_hrs_schedule_diamond;
	   		$cancellation = $bonus_data->cancellation_diamond;
	   		$acceptance = $bonus_data->acceptance_diamond;

	   		if ( $total_active_hours >= $total_hrs && $scheduled_hours >= $total_hrs_schedule && $cancelation_rate <= $cancellation && $acceptance_rate >= $acceptance) {
	   			
	   			$update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 4]);
	   			echo 'DRIVER : '.$driver_id.' '; die('DIAMOND');
	   		}

	   }
	}

    public function cronTierLevel() {

    	//$sunday = date( 'Y-m-d H:i:s', strtotime( 'sunday previous week' ) );

    	//today is
    	$day_number = 0;

    	$today_is = strtolower( date("l") );
    	$weekNumber = date("W");
    	$year = date("Y");

		switch ($today_is) {
		    case "sunday":
		        $day_number = 0;
		        break;
		    case "monday":
		        $day_number = 1;
		        break;
		    case "tuesday":
		        $day_number = 2;
		        break;
		    case "wednesday":
		        $day_number = 3;
		        break;
		    case "thursday":
		        $day_number = 4;
		        break;
		    case "friday":
		        $day_number = 5;
		        break;
		    case "saturday":
		        $day_number = 6;
		        break;
		    default:
		        $day_number = 0;
		}

		//check to reset the tier level
		$this->_check_to_reset_tier_level( $day_number );

    	//today
    	$today = date( 'Y-m-d', strtotime( 'today' ) );

    	//This weak
    	$sunday = date( 'Y-m-d H:i:s', strtotime( 'sunday previous week' ) );
    	$saturday = date( 'Y-m-d H:i:s', strtotime( 'saturday this week' ) );

    	$current_datetime = date( 'Y-m-d H:i:s');

    	//All days of this week
    	$this_sunday = date( 'Y-m-d', strtotime( 'sunday previous week' ) );
    	$this_monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
    	$this_tuesday = date( 'Y-m-d', strtotime( 'tuesday this week' ) );
    	$this_wednesday = date( 'Y-m-d', strtotime( 'wednesday this week' ) );
    	$this_thursday = date( 'Y-m-d', strtotime( 'thursday this week' ) );
    	$this_friday = date( 'Y-m-d', strtotime( 'friday this week' ) );
    	$this_saturday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

    	//All bonus data for the driver
    	$bonusData = DB::table('dn_driver_bonus')->select(array('*'))->where('is_active','1')->first();
    	//echo "<pre>"; print_r($bonusData); echo "</pre>";

    	//Scheduled time for each day
    	$scheduled_time = json_decode($bonusData->scheduled_time);

    	$driverDataSQL = "SELECT dn_users.id AS ID, dn_users_data.tiers_level AS tierLevel FROM dn_users 
    	LEFT JOIN role_user ON dn_users.id = role_user.user_id 
    	LEFT JOIN dn_users_data ON dn_users.id = dn_users_data.user_id 
    	WHERE role_user.role_id = 4 ORDER BY  dn_users.id DESC ";    	
    	$driverData = DB::select(DB::raw($driverDataSQL));

    	//echo "<pre>"; print_r($driverData); echo "</pre>"; die('driverData');

    	
    	//check and update tier level for each driver
    	foreach ($driverData as $driver) {
    		
    		$driver_id = $driver->ID;
    		//$driver_id = 208;

    		$tierLevel = $driver->tierLevel;

    		$hourlogsSQL = "SELECT date(login_time) AS date, time(login_time) AS login_time,time(logout_time) AS  logout_time, TIMEDIFF(logout_time, login_time) AS duration FROM dn_user_logs  WHERE user_id=$driver_id AND created_at >= '".$sunday."' AND created_at <= '".$saturday."' ";
			$hourlogs = DB::select(DB::raw($hourlogsSQL));

			
			$total_hours = 0;
			foreach ($hourlogs as $logs){

				if(!empty($logs->duration)){
					
					$hourdiff = abs( round((strtotime($logs->logout_time) - strtotime($logs->login_time))/3600, 4) );
					$total_hours += $hourdiff;
				}
			}
			
			$total_active_hours = $total_hours; //1. total_active_hours

	    	$scheduled_hours = 0;
	    	$total_scheduled_hours = 0;

	    	foreach ($hourlogs as $sLogs) {

	    		//echo "<pre>"; print_r($scheduled_time); die();

	    		if ($sLogs->date == $today) {

		    		$start_time = '';
		    		$end_time = '';
		    		
		    		if (!empty($sLogs->login_time)) {
		    			
		    			if ($sLogs->login_time <= $scheduled_time->to_time[$day_number]) {
		    				$start_time = $scheduled_time->to_time[$day_number];
		    			} else {
		    				$start_time = $sLogs->login_time;
		    			}


			    		if (!empty($sLogs->logout_time)) {
			    			
			    			if ($sLogs->logout_time >= $scheduled_time->from_time[$day_number]) {
			    				$end_time = $sLogs->logout_time;
			    			} else {
			    				$end_time = $scheduled_time->from_time[$day_number];
			    			}

			    		}
		    		}

		    		if ($start_time != '' AND $end_time != '') {
		    			$hourdiff_scheduled = abs( round((strtotime($logs->logout_time) - strtotime($logs->login_time))/3600, 4) );
						$total_scheduled_hours += $hourdiff_scheduled;	
		    		}
	    		
	    		}
	    	}

		    //calculate scheduled time
	    	$scheduled_hours = $total_scheduled_hours; //2. total_active_hours


	    	//calculating total rides rate
	    	$totalRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->count();
	    	
	    	if ($totalRides != 0) {

	    		//calculating total canceled rides rate
		    	$totalCanceledRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->where('status','3')->count();
		    	$totalAcceptedRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->where('status','!=','0')->count();

		    	//echo "$totalCanceledRides".' '.$totalRides.'<br>';

		    	$cancelation_rate = ceil( ( $totalCanceledRides/$totalRides ) * 100 );
		    	$acceptance_rate = ceil( ( $totalAcceptedRides/$totalRides ) * 100 );

		    	//get and update tier level    	   
		    	
		    	//echo "<pre>"; print_r($bonusData); echo "</pre>";

		    	/*echo 'total_active_hours = '.$total_active_hours.'<br>';
		    	echo 'scheduled_hours = '.$scheduled_hours.'<br>';
		    	echo 'totalRides = '.$totalRides.'<br>';
		    	echo 'totalCanceledRides = '.$totalCanceledRides.'<br>';
		    	echo 'cancelation_rate = '.$cancelation_rate.'<br>';
		    	echo 'totalAcceptedRides = '.$totalAcceptedRides.'<br>';
		    	echo 'acceptance_rate = '.$acceptance_rate.'<br>';
		    	echo '---------- <br>';*/

		    	$current_tier_level = $tierLevel;

		    	//update tier data if driver exists or insert if not exists
		   		$tier_data_exists = DB::table('dn_driver_tier_data')->select(array('dn_driver_tier_data.driver_id'))->where('driver_id',$driver_id)->first();
		   		
		   		if ($tier_data_exists === NULL) {


		   			$insert = ['driver_id' => $driver_id,
		   					'total_rides' => $totalRides,
		   					'total_active_hours' => $total_active_hours,
		   					'scheduled_hours' => $scheduled_hours,
		   					'cancelation_rate' => $cancelation_rate,
		   					'acceptance_rate' => $acceptance_rate ];

					DB::table('dn_driver_tier_data')->insert($insert);
		   		
		   		} else {

		   			$updateSQL = "UPDATE  dn_driver_tier_data SET total_rides = $totalRides, scheduled_hours = scheduled_hours + $scheduled_hours, total_active_hours = total_active_hours + $total_active_hours, cancelation_rate = $cancelation_rate, acceptance_rate = $acceptance_rate, 
		   					updated_at = '".$current_datetime."' WHERE driver_id =$driver_id";

					DB::statement($updateSQL);
		   		}

				$this->_update_tier_level( $driver_id, $current_tier_level, $total_active_hours, $scheduled_hours, $cancelation_rate, $acceptance_rate);	
	    		
	    		//update tear log
		   		$tier_log = DB::table('dn_driver_tier_data_log')->select(array('dn_driver_tier_data_log.driver_id'))
		   		->where('driver_id',$driver_id)
		   		->where('week',$weekNumber)
		   		->where('year',$year)
		   		->first();

		   		if ($tier_log === NULL) {
		   			$insert = ['driver_id' => $driver_id,
		   					'total_rides' => $totalRides,
		   					'total_active_hours' => $total_active_hours,
		   					'scheduled_hours' => $scheduled_hours,
		   					'cancelation_rate' => $cancelation_rate,
		   					'acceptance_rate' => $acceptance_rate,
		   					'week' => $weekNumber,
		   					'year' => $year ];

					DB::table('dn_driver_tier_data_log')->insert($insert);

		   		} else {

		   			$updateSQL = "UPDATE  dn_driver_tier_data_log SET total_rides = $totalRides, scheduled_hours = scheduled_hours + $scheduled_hours, total_active_hours = total_active_hours + $total_active_hours, cancelation_rate = $cancelation_rate, acceptance_rate = $acceptance_rate, 
		   					updated_at = '".$current_datetime."' WHERE driver_id =$driver_id AND week = $weekNumber AND year = $year";

					DB::statement($updateSQL);
		   		}


	    	}

    	}

		die('cronTierLevel');
    
    }
    //TIER LEVEL ENDS HERE


    /**
     * @param \User $users
     */
    public function __construct(UserRepository $repository,User $userModel, ImageUploader $uploader)
    {
		parent::__construct();
        $this->repository = $repository;
		$this->userModel = $userModel;
		$this->uploader = $uploader;	

    }

    /**
     * Redirect not found.
     *
     * @return Response
     */
    protected function redirectNotFound()
    {
        return $this->redirect('manageAdminSubadmin');
    }

    /**
     * Display a listing of users
     * Author : Vaibhav Bharti
     * @return Response
     */
    public function admin_subadmin_view(Request $request)
    {
		$adminId = Auth::id();
		$data = $request->all();
		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('dn_users.id', '!=',$adminId )
						->whereIn('role_id', [1, 2])
						->paginate(config('admin.user.perpage'));
        return $this->view('users.admin_subadmin_view', compact('users'));
    }
	
	/**
	 * FUNCTION FOR AJAX CALL ON INDEX
	 * Author : Vaibhav Bharti
	 **/
	public function ajaxIndex(Request $request)
	{

		
		$data = $request->all();
		
		$limit = 10;
		$action='';
	    $adminId = Auth::id();
		$draw = $data['draw'];
		$offset = $data['start'];
		$searchString=$data['search']['value'];
		$sql = 'SELECT dn_users.id FROM role_user left join dn_users on role_user.user_id = dn_users.id  WHERE role_user.role_id IN(1,2) AND  dn_users.id != "'.$adminId.'"';
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_users.email LIKE '$search')";
		}
				
		$usersIds=DB::select(DB::raw($sql ));
		//print_r($usersIds);exit;
		if(!empty($usersIds))
		{
		$usersList=array();
		foreach($usersIds as $value)
		{
			$usersList[]=$value->id;
		
		}}
		
		$users = array();
		$totalRecords = 0;
		if(!empty($usersList))
		{
			
			
			/*Code for sorting Start*/
				$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','4'=>'role_user.role_id','5'=>'email','6'=>'contact_number','7'=>'active','8'=>'is_logged');
				
				$field='id';
				$direction='ASC';
				
				if(!empty($data['order'][0])){
					foreach($orderfields as $key=>$orderfield){
						if($key==$data['order'][0]['column'] )
						{
							$field=$orderfield;
							$direction=$data['order'][0]['dir'];
						}
					}
				}
			/*Code for sorting End*/
			
			$users = DB::table('role_user')
						->select(array('dn_users.*','role_user.role_id'))
						->leftjoin('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->whereIn('role_user.user_id',$usersList)
						->whereIn('role_user.role_id', [1, 2])
						->take($limit)
						->offset($offset)
						->orderBy('id','DESC')
						->get();
						//print_r($users);
			$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->leftjoin('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						->whereIn('role_user.user_id',$usersList)
						->whereIn('role_user.role_id', [1, 2])
						->paginate(config('admin.user.perpage'));
		}
		$Data="";
		//print_r($users);exit;
		foreach($users as $user)
		{
			//echo $user->active;
			
			$first_name =$user->first_name;
			$last_name =$user->last_name;
			$email=$user->email;
			$phone=$user->contact_number;
			$state=$user->state;
			$city=$user->city;
			$roleId=$user->role_id;
			if(	$roleId==2) {
				$designation="SubAdmin";
			}elseif($roleId==1) {
				$designation="SuperAdmin";
			}

			$edit="<span class='label-success label '><a href='edituser/".(($user->id))."'> Edit </a></span>";
			$view="<span class='label-success label '><a href=".route('otherAdmins',$user->id)."> View  </a></span>";
			
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');

			if(empty($loggedInUserPermission)){
				if($user->active==1) {

					$action= "<span class='label-info label'> <a href='javascript:void(0);' data-action='subadmin_suspend'  class='subadmin_suspend label-info label' data-userid=".$user->id." > Suspend </a></span>&nbsp;|&nbsp;".$edit."&nbsp;|&nbsp;";
				}else {

					$action= "<span class='label-info label'> <a href='javascript:void(0);'  data-action='subadmin_Active' class='subadmin_Active label-info label' data-userid=".$user->id." > Activate </a></span>&nbsp;|&nbsp;".$edit."&nbsp;|&nbsp;";
				} 						

			}elseif(!empty($loggedInUserPermission)){
				
				foreach($loggedInUserPermission as $userPermission){
					
					if($userPermission->module_slug=="admin_user" && $userPermission->edit_permission==1){
						if($user->active==1) {
						
							$action= "<span class='label-info label'> <a href='javascript:void(0);' data-action='subadmin_suspend'  class='subadmin_suspend label-info label' data-userid=".$user->id." > Suspend </a></span>&nbsp;|&nbsp;".$edit."&nbsp;|&nbsp;";
						}else {
						
							$action= "<span class='label-info label'> <a href='javascript:void(0);'  data-action='subadmin_Active' class='subadmin_Active label-info label' data-userid=".$user->id." > Activate </a></span>&nbsp;|&nbsp;".$edit."&nbsp;|&nbsp;";
						} 		
								
					}
				}	
			}	
			//Subadmin Permission Code End
			
			if($user->active==1) {
				$active='Active';
				
			}else {
				$active='Suspended';
			} 
			
			if($user->is_logged == 'true') {
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
			

			$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"'.$designation.'"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.$view.'"'."]";
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
	
	public function suspend(Request $request)
	{
		$data = $request->all();
		 $id = $data['id']; 
		$actionType=$data['action'];
		$adminId = Auth::id();
		if($actionType=='subadmin_suspend')
		{
			$suspend=DB::table('dn_users')
            ->where('id', $id)
            ->update(['active' => 0]);
			//print_r($suspend);
			


			if($suspend)
			{
				echo "suspendSuccess";
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id, 'status_type'=>'user_suspend','added_by'=>$adminId]);
			}
			else{
				echo "suspendFail";
			}


		}
		if($actionType=='subadmin_Active')
		{
			$active=DB::table('dn_users')
            ->where('id', $id)
            ->update(['active' => 1]);
			//print_r($suspend);
			if($active)
			{
				echo "activeSuccess";
				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id, 'status_type'=>'user_active','added_by'=>$adminId]);
			}
			else{
				echo "activeFail";
			}
		}
	
		exit;
	}

	public function adduser(Request $request){
	$input=$request->all(); //all http requests
	$newMainArr=array();
	$modules = DB::table('dn_modules')->get(); //get all modules from dn_modules table.
	if(!empty($input))
	 {	
		if(array_key_exists('Edit', $input) or array_key_exists('View', $input))
		{
			$newMainArr = $this->viewEditPermission($input);
		}

		$emailcheck=DB::table('dn_users')->where('email', $input['Email'])->get();
		$phonecheck=DB::table('dn_users')->where('contact_number', $input['Phone'])->get();
		
		if($input['userType']=="superAdmin"){
				$role=1;
			} else {
				$role=2;	
			}
		if(empty($input['Anniversary'])){$input['Anniversary']='0000-00-00';}
		if(empty($emailcheck) And empty($phonecheck))
		{
			
			$validExt = array("image/png","image/jpg","image/jpeg","image/gif","image/bmp");
			
			if (!empty($input['image'])){
				if($_FILES['image']['type'] !=''){
					if(!in_array(strtolower($_FILES['image']['type']),$validExt)){
						 Session::flash('message', 'opps!! Image format isn\'t valid! Try again.'); 
						 Session::flash('alert-class', 'alert-danger');
						return back()->withInput();
					}
				}
				$this->uploader->upload('image')->save('uploads/profile-img','admin'); //uploading image.
				$imageName = $this->uploader->getFilename();
			}else {
				$imageName="";
			}
			$hashed=Hash::make($input['pwd']); //hasing the password.
			$email=$input['Email']; //hasing the password.
			$state=$input['state']; //hasing the password.
			$city=$input['city']; //hasing the password.
			/* inserting data in dn_users for admin and subadmin */
			DB::table('dn_users')->insert(['first_name'=>$input['fname'],'last_name'=>$input['lname'],'gender'=>$input['gender'],'profile_pic'=>$imageName,'email' =>$input['Email'],'contact_number'=>$input['Phone'],'password'=>$hashed,'state'=>$state,'city'=>$city,'dob'=>date('Y/m/d', strtotime($input['DOB'])),'anniversary'=>date('Y/m/d', strtotime($input['Anniversary']))]);
			/* getting Id of last insert user. */
			$id = DB::table('dn_users')->where('email',$input['Email'] )->pluck('id');
			DB::table('dn_users')->where('id', $id)->update(['unique_code' =>200000+$id]);
			DB::table('role_user')->insert(['role_id'=>$role,'user_id'=>$id]);
			if($role==2)
			{
				foreach($newMainArr as $key=>$val)
				{
					if($val['edit']==1){
						$val['view'] = 1;
					}
			DB::table('dn_subadmin_permissions')->insert(['user_id'=>$id,'module_id'=>$val['key'],'view_permission'=>$val['view'],'edit_permission' =>$val['edit']]);
				}
				/* statement to give dashboard permission   */
			DB::table('dn_subadmin_permissions')->insert(['user_id'=>$id,'module_id'=>'7','view_permission'=>'1']);
				
			}
			  Mail::Send('app.mails.newuseremail', ['name' => $input['fname'], 'password' => $input['pwd'], 'email' => $input['Email']], function ($message) use ($email) {
					$message->from('dezinow@example.com', 'DeziNow');
					//$message->from('us@example.com', 'DeziNow');
					
					$message->to( $email )->subject('Your Account is updated');
				});
			  Session::flash('message', 'User Added Successfully!'); 
		      return Redirect::to('/admin/manageusers');
			
		}else{
			 Session::flash('Errmessage', '  opps!! User Not Added ! Either Email or phone registerd earlier!  Try again.'); 
			 return back()->withInput();
		}
	}
	return $this->view('users.add_user',compact('modules'));
 } // End of addUser

	public function adduser1(Request $request){
		//echo "test";
		
		$input=$request->all();
		// echo "<pre>";
		$modules = DB::table('dn_modules')->get();
		// print_r($modules);
		if(!empty($input))
		{
			//cho count($input['Edit']);
			if(array_key_exists("Edit", $input)){
				$Edit=$input['Edit'];
			} else {
				$Edit=array();
			}

			if(array_key_exists("View", $input)){
				$View=$input['View'];
			} else {
				$View=array();
			}

			//die;

			////$Edit=$input['Edit'];
			//$View=$input['View'];
			$mainArray=array();
			$EditCount=count($Edit);
			$ViewCount=count($View);
			//echo "<pre>";
			// print_r($result);
			//print_r($input);
			$perArr = array();
			$i = 0;
			foreach($Edit as $key => $ed) {
				$perArr[$i]["key"] = $key;
				$perArr[$i]["edit"] = "1";
				$i++;
			}
			foreach($View as $key => $ed) {
				$perArr[$i]["key"] = $key;
				$perArr[$i]["view"] = "1";
				$i++;
			}
			//echo "<hr>";
			$newMainArr = array();
			foreach ($modules as $modKey => $modVal) {
				$newMainArr[$modKey]["key"] = $modVal->id;
				$newMainArr[$modKey]["edit"] = "0";
				$newMainArr[$modKey]["view"] = "0";
				foreach ($perArr as $perKey => $valPer){	
					if($modVal->id == $valPer["key"] && isset($valPer["view"]) ) {
						$newMainArr[$modKey]["view"] = $valPer["view"];	
					}
					if($modVal->id == $valPer["key"] && isset($valPer["edit"]) ) {
						$newMainArr[$modKey]["edit"] = $valPer["edit"];
					}
				}
			}
			// print_r(($newMainArr));
			// exit;
			$emailcheck=DB::table('dn_users')->where('email', $input['Email'])->get();
			if($input['userType']=="superAdmin"){
					$role=1;
					
					//print_r($id);exit;
				} else {
					$role=2;
					
				}
			//print_R($input);
			if(empty($emailcheck))
			{	
				DB::table('dn_users')->insert(['first_name'=>$input['fname'],'last_name'=>$input['lname'],'gender'=>$input['gender'],'email' =>$input['Email'],'contact_number'=>$input['Phone'],'password'=>$input['pwd'],'dob'=>$input['DOB'],'anniversary'=>$input['Anniversary']]);
				$id = DB::table('dn_users')->where('email',$input['Email'] )->pluck('id');
				DB::table('role_user')->insert(['role_id'=>$role,'user_id'=>$id]);
				if($role==2)
				{
					foreach($newMainArr as $key=>$val)
					{
						DB::table('dn_subadmin_permissions')->insert(['user_id'=>$id,'module_id'=>$val['key'],'view_permission'=>$val['view'],'edit_permission' =>$val['edit']]);
					}				
				}

				$suspend=DB::table('dn_users')->where('id', $id)->update(['unique_code' => 200000+$id]);

				
				return Redirect::to('admin/adduser')->with('message', 'User Added');
			}else{
				return Redirect::to('admin/adduser')->with('message', 'Email already exist try another one');
			}
		}	
		return $this->view('users.add_user',compact('modules'));
	}


	/**
  * @FUNCTION FOR EDIT USER
  * @Author : Vaibhav Bharti
  * @Params :$id is the id of user to edit , $request is the array of  all http request
  * @Return : '$user' for user data, '$permission' for users permission 
  **/
public function editUser($id=null,Request $request){
	$input=$request->all();
	$newMainArr=array();
	/*  fetching admin or subadmin userData from dn_users table */
	$user = DB::table('dn_users')
					->select(array('dn_users.*','role_user.*','dn_users.id as userId',DB::raw('role_user.id as roleindex_id')))
					->where('dn_users.id',$id)
					->leftjoin('role_user', 'dn_users.id', '=', 'role_user.user_id')
					->first();
					
	$password="";
	if($user){
		$password = 	$user->password;
	}				
				
	if( ($user->anniversary=='0000-00-00') || empty($user->anniversary) ){$user->anniversary="";}
	
	/*  fetching admin or subadmin userPermissions from dn_subadmin_permissions table */
	$permissions=DB::table('dn_modules')
					->select(array('dn_modules.*','dn_subadmin_permissions.*'))
					->leftjoin('dn_subadmin_permissions', 'dn_modules.id', '=', 'dn_subadmin_permissions.module_id')
					->where('dn_subadmin_permissions.user_id',$id)
					->where('dn_subadmin_permissions.module_id','!=','7')
					->get();
	if(!empty($input))
	 {	
		$adminid=Auth::id();
		if(array_key_exists('Edit', $input) or array_key_exists('View', $input))
		{
			$newMainArr = $this->viewEditPermission($input);
		}
		$idCheck=DB::table('dn_users')->where('id', $input['user_id'])->first();
		$emailCheck=DB::table('dn_users')->where('email', $input['Email'])->where('id','!=',$input['user_id'])->first();
		// print_r($input['user_id']);
		// print_r($emailCheck);die;
		if($input['userType']=="superAdmin"){
				$role=1;
			} else {
				$role=2;	
			}
		if(empty($input['Anniversary'])){$input['Anniversary']='0000-00-00';}
		if(!empty($idCheck) && empty($emailCheck))
		{
			if (!empty($input['image'])){	
			if(!empty($user->profile_pic) and !empty(base_path().'uploads/profile-img'.$user->profile_pic)){
				
				if(file_exists(base_path().'uploads/profile-img'.$user->profile_pic)){
					unlink(base_path().'uploads/profile-img'.$user->profile_pic);
				}
				
				
				
				}
				
				$this->uploader->upload('image')->save('uploads/profile-img','admin');
				$imageName = 'uploads/profile-img/'.$this->uploader->getFilename();	
			}else {
			$imageName="";
			}
			/*  Updating data for user */
			if($input['pwd']){
				DB::table('dn_users')->where('id',$idCheck->id)->Update(['first_name'=>$input['fname'],'last_name'=>$input['lname'],'gender'=>$input['gender'],'profile_pic'=>$imageName,'email' =>$input['Email'],'state' =>$input['state'],'city' =>$input['city'],'contact_number'=>$input['Phone'],'password'=>Hash::make($input['pwd']),'dob'=>date('Y-m-d', strtotime($input['DOB'])),'anniversary'=>date('Y-m-d', strtotime($input['Anniversary']))]);
			//print_r($input);
			$email =  $input['Email'];
				
				$res = \Mail::Send('app.mails.newuseremail', ['name' => $input['fname'], 'password' => $input['pwd'], 'email' => $input['Email']], function ($message) use ($email) {
					$message->from('dezinow@example.com', 'DeziNow');
					//$message->from('us@example.com', 'DeziNow');
					$message->to($email)->subject('Your Account is updated');;
				});
				
				/*
				$res = \Mail::send('app.mails.newuseremail', ['name' => $input['fname'], 'password' => $input['pwd'], 'email' => $input['Email']], function ($message) use ($email,$input['fname'])
					{
						$message->from('dezinow@example.com', 'DeziNow');
						$m->to($email, $input['fname'])->subject("Account updated");
						
					});*/
				

			} else {
				
				DB::table('dn_users')->where('id',$idCheck->id)->Update(['first_name'=>$input['fname'],'last_name'=>$input['lname'],'gender'=>$input['gender'],'profile_pic'=>$imageName,'email' =>$input['Email'],'contact_number'=>$input['Phone'],'dob'=>date('Y-m-d', strtotime($input['DOB'])),'anniversary'=>date('Y-m-d', strtotime($input['Anniversary']))]);
			
			}
			
			DB::table('role_user')->where('user_id',$idCheck->id)->Update(['role_id'=>$role]);
			if($role==2)
			{
				DB::table('dn_subadmin_permissions')->where('user_id', '=', $idCheck->id)->delete();
				
				foreach($newMainArr as $key=>$val)
				{
					if($val['edit']==1){
						$val['view'] = 1;
					}
					DB::table('dn_subadmin_permissions')->insert(['user_id'=>$idCheck->id,'module_id'=>$val['key'],'view_permission'=>$val['view'],'edit_permission' =>$val['edit'],'created_by'=>$adminid]);
				}
					DB::table('dn_subadmin_permissions')->insert(['user_id'=>$idCheck->id,'module_id'=>'7','view_permission'=>'1','created_by'=>$adminid]);
				
			}
			 $request->session()->flash('message', 'User Updated Successfully!');
			 return back();
		}else{
			 $request->session()->flash('Errmessage', 'User Not Updated ! Email already  exists with some other users ');
			 return back()->withInput();
			 }
	}
	return $this->view('users.edit_user',compact('user','permissions'));
	} // End of editUser 


	/**
  * @FUNCTION FOR VIEW EDIT PERMISSION
  * @Author : Vaibhav Bharti
  * @Params : '$input' is the array of inputs via form
  **/	
 public function viewEditPermission($input=null){
	$mainArray=array();
	$modules = DB::table('dn_modules')->get(); //get all modules from dn_modules table.
		$perArr = array();
		$i = 0;
		if (array_key_exists('Edit', $input)){
		$Edit=$input['Edit'];	 // $Edit contains all edit permission.
		$EditCount=count($Edit); // $EditCount contains the no. of permissions for edit ,given in form submission.
			foreach($Edit as $key => $ed) {
			$perArr[$i]["key"] = $key;
			$perArr[$i]["edit"] = "1";
			$i++;
		}}
		if(array_key_exists('View', $input)){
		$View=$input['View'];     // $View contains all edit permission.
		$ViewCount=count($View); // $ViewCount contains the no. of permissions for view ,given in form submission.
		foreach($View as $key => $ed) {
			$perArr[$i]["key"] = $key;
			$perArr[$i]["view"] = "1";
			$i++;


		}}
		$newMainArr = array();
		foreach ($modules as $modKey => $modVal) {
			$newMainArr[$modKey]["key"] = $modVal->id;
			$newMainArr[$modKey]["edit"] = "0";
			$newMainArr[$modKey]["view"] = "0";
			foreach ($perArr as $perKey => $valPer){	
				if($modVal->id == $valPer["key"] && isset($valPer["view"]) ) {
					$newMainArr[$modKey]["view"] = $valPer["view"];	
				}
				if($modVal->id == $valPer["key"] && isset($valPer["edit"]) ) {
					$newMainArr[$modKey]["edit"] = $valPer["edit"];
				}
			}
		}
	return $newMainArr;
	} // End of viewEditPermission  


	public function passengerpromos(Request $request){
		
		$input = $request->all();

		$promoData = DB::table('dn_passenger_promo_code')
					->select(array('*'))
					->where('dn_passenger_promo_code.status','1')
					->orderby('dn_passenger_promo_code.id','DESC')
					->first();

		if(empty($promoData)){
		
			$promoData =  (object) array('referal_enable'=>'','referal_credit'=>'','anniversary_promo_enable'=>'','anniversary_promo_code'=>'','anniversary_promo_credit'=>'','birthday_promo_enable'=>'','birthday_promo_code'=>'','birthday_promo_credit'=>'','new_ride_promo_enable'=>'','new_ride_promo_code'=>'','new_ride_promo_credit'=>'','promo_enable'=>'','promo_code'=>'','promo_credit'=>'','promo_till_date'=>'');
		}			
		//print_r($user);			
		//print_r($input);
		if(!empty($input))	 
		{
				
			if(trim($input['promo_code'])!=''){

                if (empty($input['promo_credit'])) {

                    Session::flash('message', "Error: Promo credit can not be empty.");
                    Session::flash('alert-class', 'alert-danger');
                    return Redirect::to('/admin/passengerpromos');

                }
                if (empty($input['promo_till_date'])) {

                    Session::flash('message', "Error: Promo Till Date can not be empty.");
                    Session::flash('alert-class', 'alert-danger');
                    return Redirect::to('/admin/passengerpromos');

                }
				if (!preg_match('/^[0-9]*$/', $input['promo_credit'])) {
					
					Session::flash('message', "Error: Promo credit should be numeric."); 
					Session::flash('alert-class', 'alert-danger');
					return Redirect::to('/admin/passengerpromos');
				
				}
				$existPromoData = DB::table('dn_passenger_promo_code')
						->select(array('*'))
						->where('dn_passenger_promo_code.code',$input['promo_code'])
						->first();
						
				if(!empty($existPromoData)){
					
					Session::flash('message', "Error: Promo code already exists."); 
					Session::flash('alert-class', 'alert-danger');
					return Redirect::to('/admin/passengerpromos');
					
				}else{

					if($input['promo_till_date']==""){
						$promo_till_date = $input['promo_till_date']="";
					} else {
						$promo_till_date = date('Y-m-d H:i:s',strtotime($input['promo_till_date']));
					}
					/* Entry In dn_passenger_promo_code START */
					$promo_status = !isset($input['promo_enable']) ? "0" : "1";
                    $promo_multiple = !isset($input['promo_multiple']) ? "0" : "1";
					$update_code = ['type'=>'normal','code' => $input['promo_code'], 'amount' => $input['promo_credit'], 'valid_till' => $promo_till_date, 'status' => $promo_status,'promo_multiple'=>$promo_multiple];
					DB::table('dn_passenger_promo_code')->insert($update_code);
					Session::flash('message', 'Promos are added successfully.'); 
					return Redirect::to('/admin/passengerpromos');
					//DB::table('dn_passenger_promo_code')->where('type', 'normal')->update($update_code);

                  
					/* /Entry In dn_passenger_promo_code END */

					
				}		
	
			}
		 	
            if($input['referal_credit'] !='') {
	            $promo_status = !isset($input['referal_enable']) ? "0" : "1";
	            $update_code = ['amount' => $input['referal_credit'], 'status' => $promo_status];
	            DB::table('dn_passenger_promo_code')->where('type', 'referral')->update($update_code);
            }
            if($input['anniversary_promo_code'] !='') {
                 $promo_status = !isset($input['anniversary_promo_enable']) ? "0" : "1";
                 $update_code = ['code' => $input['anniversary_promo_code'], 'amount' => $input['anniversary_promo_credit'], 'status' => $promo_status];
                 DB::table('dn_passenger_promo_code')->where('type', 'ani')->update($update_code);
            }
            if($input['birthday_promo_code'] !='') {
                $promo_status = !isset($input['birthday_promo_enable']) ? "0" : "1";
                $update_code = ['code' => $input['birthday_promo_code'], 'amount' => $input['birthday_promo_credit'], 'status' => $promo_status];
                DB::table('dn_passenger_promo_code')->where('type', 'birthday')->update($update_code);
            	
            }
            if($input['new_ride_promo_code'] !='') {
                $promo_status = !isset($input['new_ride_promo_enable']) ? "0" : "1";
                $update_code = ['code' => $input['new_ride_promo_code'], 'amount' => $input['new_ride_promo_credit'], 'status' => $promo_status];
                DB::table('dn_passenger_promo_code')->where('type', 'new_rider_promotion')->update($update_code);
            	
            }

			
		}
		
		return $this->view('chargespromos.passengerpromos',compact('promoData'));
	}
	
	/**
  * @FUNCTION FOR AJAX CALL ON INDEX
  * @Author : Vaibhav Bharti
  * @Params : $request
  **/
	 public function ajaxpassengerPromos(Request $request)
	 {
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		//$driverId = $data['id'];
		$searchString=$data['search']['value'];
		
		$orderfields=array('0'=>'id','1'=>'code','2'=>'type','3'=>'valid_till','4'=>'status');
		
		$field='id';
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
		$type = 'referral';
		/* code for searching of  user*/
		$sql = "Select dn_passenger_promo_code.* from dn_passenger_promo_code WHERE dn_passenger_promo_code.type = 'normal'";
		
		if($searchString != ""){
			$sql .= ' AND (dn_passenger_promo_code.code like "%'.$searchString.'%" OR dn_passenger_promo_code.valid_till like "%'.$searchString.'%" OR dn_passenger_promo_code.amount like "%'.$searchString.'%")';
		}
		$sql .= " order by id DESC";
		//$sql .= " order by ".$field." ".$direction;
		//echo $sql;exit;
		$totalPromocount=DB::select(DB::raw($sql));
		$totalPromoCount= count($totalPromocount);
		
		$sql .= " Limit ".$offset." , ".$limit;
		
		$totalPromo=DB::select(DB::raw($sql));
		
		
		//echo "<pre>"; print_r($totalPromo);die;
		
		
		$totalRecords = 0;
		
		$Data="";
		foreach($totalPromo as $promo)
		{
			//echo $user->active;
			$promo_code = 		$promo->code;
			$promo_till_date =	date("m/d/Y",strtotime($promo->valid_till));
			$promo_credit=		$promo->amount;
			$promo_enable=		$promo->status;
            $promo_multiple= $promo->promo_multiple;
			
			$action ="<span> <a href='javascript:void(0);' class='btn btn-danger width-btn promo_dlt' data-action='btnpromo_dlt' data-promoid=".$promo->id." >Delete</a></span>";
					
			
			if(empty($promo_code))
			{
				$promo_code="N/A";
			}
            if($promo_multiple == 1)
            {
                $promo_multiple="Yes";
            }else{
                $promo_multiple="No";
            }
			if(empty($promo_till_date))
			{
				$promo_till_date="N/A";
			}
			if(empty($promo_credit))
			{
				$promo_credit="N/A";
			}
			
			if($promo_enable==1){
				$promo_enable="Enabled";
			
			}else{
				$promo_enable="Disabled";
			}
			
			
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$promo_code .'"'.",". '"'.$promo_till_date.'"' .",".'"'.$promo_credit.'"'.",". '"'.$promo_enable.'"'.",". '"'.$promo_multiple.'"' .",". '"'.$action.'"'."]";
		}
		
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalPromoCount).',
			  "recordsFiltered":'.($totalPromoCount).',
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
	
	public function deletepassengerpromo(Request $request)
	{
		/* initializing variables */
		  
		$data = $request->all();
		$id = $data['id']; 
		$adminId = Auth::id();
		$actionType=$data['action'];

		if($actionType=='btnpromo_dlt')
		{
			
			$promo_delete = DB::table('dn_passenger_promo_code')
			->where('id', $id)
			->delete();
		
			if($promo_delete){
				
				if ($actionType=='btnpromo_dlt') { echo "btnpromo_dlt"; }
			
					DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'Promo_Deleted','added_by'=>$adminId]);
			
			}else{
				
				echo "error";
			
			}
			
		}

		exit;
	 }
	 
	public function promo_code_check(Request $request)
	{
		/* initializing variables */
		  
		$data = $request->all();
		$promo_name = $data['promo_code_name']; 
		$adminId = Auth::id();
		$actionType=$data['action'];

		if($actionType=='check_promo_code')
		{

			$promoData = DB::table('dn_passenger_promo_code')
				->select(array('id'))
				->where('dn_passenger_promo_code.code',$promo_name)
				->first();
		
			if(empty($promoData)){
				
				echo 0;	
			
			}else{
				
				echo 1;
			
			}
			
		}

		exit;
	 }
	 		
	public function riderpromos(Request $request){
		$input = $request->all();

		//$input = $request->all();
		$promoData = DB::table('dn_driver_promos')
					->select(array('*'))
					->where('dn_driver_promos.is_active','1')					
					->first();
//print_r($promoData);
		if(empty($promoData)){
		
			$promoData =  (object) array('referal_enable_for_5_10'=>'','referal_credit_for_5_10'=>'','referal_enable_for_20'=>'','referal_credit_for_20'=>'');
		}			
//print_r($promoData);
//die;

		if(!empty($input))	 
		{
			//print_r($input);
			/** update old all **/
			$update=DB::table('dn_driver_promos')
            ->where('is_active','1')
            ->update(['is_active' => '0']);
            
			$data = array(
			'referal_enable_for_5_10' => (!isset($input['referal_enable_for_5_10'])) ? "0" : "1",
			'referal_credit_for_5_10' =>$input['referal_credit_for_5_10'],

			'referal_enable_for_20' => (!isset($input['referal_enable_for_20'])) ? "0" : "1",
			'referal_credit_for_20' =>$input['referal_credit_for_20'],
			

			'added_on' =>date('Y-m-d H:i:s'),
			'added_by' =>Auth::id()
			);

			DB::table('dn_driver_promos')->insert($data);
			//echo '<pre>';
			//print_r($data);
			//die;
			Session::flash('message', 'Promos are added successfully.'); 
		    return Redirect::to('/admin/riderpromos');
		}			

		
		return $this->view('chargespromos.riderpromos',compact('promoData'));
	}

	public function charges(Request $request){
		$input = $request->all();
		
		return $this->view('chargespromos.chargesall');
	}


	public function riderbonus(Request $request){

		$input = $request->all();
		
		$bonusData = DB::table('dn_driver_bonus')
		->select(array('*'))
		->where('is_active','1')
		->first();

		if(empty($bonusData)){
		

			$bonusData = (object) array(
			'day_number' => '',

			'commission_silver' =>'',
			'total_hrs_silver' =>'',
			'total_hrs_schedule_silver' =>'',
			'acceptance_silver' =>'',
			'cancellation_silver' =>'',

			'commission_gold' =>'',
			'total_hrs_gold' =>'',
			'total_hrs_schedule_gold' =>'',
			'acceptance_gold' =>'',
			'cancellation_gold' =>'',


			'commission_platinum' =>'',
			'total_hrs_platinum' =>'',
			'total_hrs_schedule_platinum' =>'',
			'acceptance_platinum' =>'',
			'cancellation_platinum' =>'',

			'commission_diamond' =>'',
			'total_hrs_diamond' =>'',
			'total_hrs_schedule_diamond' =>'',
			'acceptance_diamond' =>'',
			'cancellation_diamond' =>'',

			'from_time'=> '',
			'to_time'=> '',

			'scheduled_time'=> '',

			);

		}			

		
		if(!empty($input))	 
		{

			for ($schedule_count=0; $schedule_count < 7; $schedule_count++) { 
				
				$strToTime 	= $input['to_time'][$schedule_count];
		        $withoutSpaceToTime  = str_replace(' ', '', $strToTime);
				$to_time  	= date("H:i:s", strtotime($withoutSpaceToTime));

				$strFromDate 	= $input['from_time'][$schedule_count];
	            $withoutSpaceFromDate  = str_replace(' ', '', $strFromDate);
				$from_time  	= date("H:i:s", strtotime($withoutSpaceFromDate));

				$schedule_time['day'][$schedule_count] 	= $input['day'][$schedule_count];
				$schedule_time['from_time'][$schedule_count] 	= $from_time;
				$schedule_time['to_time'][$schedule_count] 	= $to_time;

			}

			$schedule_time = json_encode($schedule_time);


			/** update old all **/
			$update=DB::table('dn_driver_bonus')
            ->where('is_active','1')
            ->update(['is_active' => '0']);
           
			$data = array(
			'day_number' => '1',

			'commission_silver' =>$input['commission_silver'],
			'total_hrs_silver' =>$input['total_hrs_silver'],
			'total_hrs_schedule_silver' =>$input['total_hrs_schedule_silver'],
			'acceptance_silver' =>$input['acceptance_silver'],
			'cancellation_silver' =>$input['cancellation_silver'],

			'commission_gold' =>$input['commission_gold'],
			'total_hrs_gold' =>$input['total_hrs_gold'],
			'total_hrs_schedule_gold' =>$input['total_hrs_schedule_gold'],
			'acceptance_gold' =>$input['acceptance_gold'],
			'cancellation_gold' =>$input['cancellation_gold'],


			'commission_platinum' =>$input['commission_platinum'],
			'total_hrs_platinum' =>$input['total_hrs_platinum'],
			'total_hrs_schedule_platinum' =>$input['total_hrs_schedule_platinum'],
			'acceptance_platinum' =>$input['acceptance_platinum'],
			'cancellation_platinum' =>$input['cancellation_platinum'],

			'commission_diamond' =>$input['commission_diamond'],
			'total_hrs_diamond' =>$input['total_hrs_diamond'],
			'total_hrs_schedule_diamond' =>$input['total_hrs_schedule_diamond'],
			'acceptance_diamond' =>$input['acceptance_diamond'],
			'cancellation_diamond' =>$input['cancellation_diamond'],

			'from_time'	=> '00:00:00',
			'to_time'	=> '00:00:00',

			'scheduled_time'	=> $schedule_time,

			'added_on' =>date('Y-m-d H:i:s'),
			'added_by' =>Auth::id()

			);

			DB::table('dn_driver_bonus')->insert($data);
			
			Session::flash('message', 'Bonus are added successfully.'); 
		    return Redirect::to('/admin/riderbonus');
		}

		return $this->view('chargespromos.riderbonus',compact('bonusData'));

	}
	
	
	/**
	 * Ajax request function for get driver bonus
	 */
	function driverBonusAjax( Request $request ){
		
		$input = $request->all();

		$dayId = ($input['dayId']); 
		
					
		$nextMondy =  date('Y-m-d 23:59:59',strtotime( "next sunday" ));
		$thisWeekMonday = date('Y-m-d H:i:s',strtotime( "Monday this week" ));
		$bonusData = DB::table('dn_driver_bonus')
		->select(array('*'))
		->where('is_active','1')
		->where('day_number',$dayId)
		->whereBetween('added_on', [$thisWeekMonday, $nextMondy])					
		->first();			
					
		if(empty($bonusData)){
		

				$bonusData = (object) array(
			'day_number' => '',

			'commission_silver' =>'',
			'total_hrs_silver' =>'',
			'total_hrs_schedule_silver' =>'',
			'acceptance_silver' =>'',
			'cancellation_silver' =>'',

			'commission_gold' =>'',
			'total_hrs_gold' =>'',
			'total_hrs_schedule_gold' =>'',
			'acceptance_gold' =>'',
			'cancellation_gold' =>'',


			'commission_platinum' =>'',
			'total_hrs_platinum' =>'',
			'total_hrs_schedule_platinum' =>'',
			'acceptance_platinum' =>'',
			'cancellation_platinum' =>'',

			'commission_diamond' =>'',
			'total_hrs_diamond' =>'',
			'total_hrs_schedule_diamond' =>'',
			'acceptance_diamond' =>'',
			'cancellation_diamond' =>'',

			'from_time'=> '',
			'to_time'=> '',

			);

		}			
		return json_encode($bonusData);
		exit;			
	}


	function driverchangelog ( $id, Request $request ){
		
		
		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('dn_users.id', $id )
						->whereIn('role_id', [4])
						->first();
		if($users){
			
			/** get user logs data here **/
			$logs = DB::table('dn_driver_change_log')
					->select(array('*'))
					->join('dn_users', 'dn_driver_change_log.added_by', '=', 'dn_users.id')		
					->where('dn_driver_change_log.user_id', $id )
					->orderBy('dn_driver_change_log.added_on', 'DESC')
					->get();		
			
				
			$adminId = Auth::id();				
				
			$input = $request->all();	
			if($input){
				
				if(trim($input['changeText'])!=""){
					
					$data = array('user_id'=>$id,'text'=>$input['changeText'],'added_by'=>$adminId,'added_on'=>date('Y-m-d H:i:s'));
					DB::table('dn_driver_change_log')->insert($data);
					Session::flash('message', 'Logs are added successfully.'); 
					
				} else {
					
					Session::flash('error', 'OOPS! Somting Went Wrong. Please Try Again.');
				}
				
				return Redirect::to('/admin/driverchangelog/'.$id);
			}
			
		}				
			
		
		return $this->view('driver.driverlogs',compact('users','logs'));

	}
	
	function driverlist(){
		
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
		
		
		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','4')
						//->toSql();
						//print_r($users); die;
						->paginate(config('admin.user.perpage'));
						

		$citys = DB::table('dn_users')
				->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.city) as no_of_users')))
				->join('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
				->join('role_user', 'role_user.user_id', '=', 'dn_users.id')
				

				->where( function ($query) {
					$query->where( 'dn_users.city','!=','' );
					$query->where( 'role_user.role_id','4' );
                  	//->where( 'role_id', '4' );
				})
				
				->groupBy('dn_users.city')
				->orderBy('no_of_users')
				->get();
				//->toSql();
		//print_r($citys);		
		$n=count($citys)-1;
		
		if($citys){
			
			if($n==0)
			{

			$citiesCount=array('least'=>$citys,'most'=>$citys[$n]); 
			}
			$citiesCount=array('least'=>$citys[0],'most'=>$citys[$n]);
			
		} else {
			$leastArray = (object) array('city'=>'N/A','no_of_users'=>0);
			$mostArray = (object) array('city'=>'N/A','no_of_users'=>0);
			$citiesCount= array('least'=>$leastArray,'most'=>$mostArray);
		}
		 
		
		return $this->view('driver.driverlist',compact( 'users','citiesCount','states'));
	}
	
	
	
	 
/**
  * @FUNCTION FOR AJAX CALL ON INDEX
  * @Author : Vaibhav Bharti
  * @Params : $request
  **/
 public function ajaxDriverIndex(Request $request)
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
					//->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')					
					->where('role_id','4')
					->whereIn('role_user.user_id',$usersList)
					->take($limit)->offset($offset) ->orderBy($field,$direction)->get();
					//print_r($users);
		$totalRecords = DB::table('role_user')
					->select(array('dn_users.*'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->where('role_id','4')
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
		$view="<span class='label-success label '><a href='driver-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
		if($user->active==1) {
			$active='Active';
			$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
			// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
			
		}else{
			$active='Suspended';
			$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Active</a></span>&nbsp;|&nbsp;".$view;
		} 
		
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
		$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$action.'"'."]";
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
 
 function driverDetails($id){
	 $id = convert_uudecode(base64_decode($id));
	 //$user=$this->getUserDetails($id);
	 $user = DB::table('role_user')
				->select(array('dn_users.*','role_user.*'))
				->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
				->where('dn_users.id',$id)->first();
	

	 $totalRide = DB::table('dn_rides')
				->select(array(DB::raw('COUNT(*) as total_ride')))
				->join('dn_users', 'dn_rides.driver_id', '=', 'dn_users.id')
				->where('dn_rides.driver_id',$id)->first();
	//print_r($ride);				
			
	return $this->view('driver.driver-details',compact('user','totalRide','id'));			
	 die;
 }
 
 /**
  * @FUNCTION FOR AJAX CALL ON INDEX
  * @Author : Vaibhav Bharti
  * @Params : $request
  **/
 public function ajaxRideList(Request $request)
 {
	/* initializing the variables */
	$data = $request->all();
	$limit = 10;
	$draw = $data['draw'];
	$offset = $data['start'];
	$driverId = $data['id'];
	$searchString=$data['search']['value'];
	$driver_detail_rideid= trim( $data['driver_detail_rideid'] );
	
    //$startDate=$data['startDate'];
	//$endDate=$data['endDate'];
	$orderfields=array('0'=>'id');
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
	
	/* code for searching of  user*/
	$sql = 'Select (dn_rides.id + 5000000) as driver_unique_code, dn_rides.* , dn_users.unique_code,dn_users.contact_number, dn_users.state, dn_users.city, dn_users.first_name ,dn_users.last_name,dn_users.email from dn_rides ';
	$sql .= 'inner join dn_users on dn_rides.passenger_id = dn_users.id where 1= 1 AND ';
	$sql .= 'dn_rides.driver_id = "'.$driverId.'"';
	
	if($driver_detail_rideid!=""){
		$sql .= ' AND ( dn_users.unique_code like "%'.$driver_detail_rideid.'%" OR dn_users.first_name like "%'.$driver_detail_rideid.'%")';
	}
		
	$sql .= " order by ".$field." ".$direction;
	$totalRideCount=DB::select(DB::raw($sql));
	$totalRideCount= count($totalRideCount);
	//echo $totalRideCount; die;
	$sql .= " Limit ".$offset." , ".$limit;
	$totalRide=DB::select(DB::raw($sql));
	
	
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
	foreach($totalRide as $user)
	{
		//echo $user->active;
		$first_name =$user->first_name.' '.$user->last_name;
		$unique_code =$user->unique_code;
		$email=$user->email;
		$phone=$user->contact_number;
		$state=$user->state;
		$city=$user->city;
		//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
		$action="<span class='label-success label '><a href='driver-ride-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
				
		
		if(empty($first_name))
		{
			$first_name="N/A";
		}
		if(empty($unique_code))
		{
			$unique_code="N/A";
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
		$rideStatus=$user->status;
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
		
		$rideAmount = $this->getRideAmount($user->id);
		//die('asdfasd');
		$Data[]= "[". '"'.$user->driver_unique_code .'"' . ",". '"'.$user->created_at .'"' . ",". '"'.$first_name .'"'.",". '"'.$unique_code.'"' .",". '"0"'.",". '"'.$rideStatus.'"'.",". '"$ '.$rideAmount.'"'.",". '"'.$action.'"'."]";
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
 
 function getRideAmount( $rideId ){
	 
	 $ride = DB::table('dn_payments')
				->select(array('dn_payments.*'))				
				->where('ride_id',$rideId)->first();
				//echo "<hr>";
				//return "ddd".$ride->amount;
		//echo '<pre>';
		//print_r($ride->amount);
		//echo $ride->amount
		//die;
		//echo $ride->amount;
		//die;
//echo($ride['amount']; 		
	//print_r($ride->amount); 
	//echo '<br>';
	//die;			
	 if(!empty($ride)){
		return $ride->amount;
		exit; 
	 }	else {
		 return 0;
		 exit;
	 }		
	 
 }

public function singleAdmin(){    
	$data=$this->admindetail(0);
	
	return $this->view('singleadmin',compact('data'));
 }
 
 public function otherAdmins($id=null)
 {
	 $data=$this->admindetail($id);
	 return $this->view('singleadmin',compact('data'));
 }
 
  public function admindetail($id=null)
 {
	 //global $id;
	// echo $id; die;
		if(@$id==0){
 		$id = Session::get('login_82e5d2c56bdd0811318f0cf078b78bfc');
 			if(empty($id)){
 			 return redirect('admin/login');
 			 
		}}
   		
 		$data['user'] = DB::table('role_user')
					->select(array('dn_users.*','roles.Name as type'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->join('roles', 'role_user.role_id', '=', 'roles.id')
					->where('dn_users.id',$id)->first();
		
		$data['message_Archive'] = DB::table('dn_users_changed_status_log')
					->where('status_type','message_Archive')
					->Where('added_by',$id)
					->count();
					
		$data['lastLoginData']=DB::table('dn_users')->select('last_login')->where('id',$id)->first();
		$data['lastLogin'] = @$lastLoginData->last_login;
		//print_r($lastLoginData);die;
	return $data;
		
 		
 }
 
 public function loghistory(Request $request)
 {
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$id=$data['adminId'];
		//global $id;
		//$id = Session::get('login_82e5d2c56bdd0811318f0cf078b78bfc');
		// $id  = $data['id'][0];
		
		$field='created_at';
		$direction='DESC';
		$orderfields=array('0'=>'id','1'=>'status_type','2'=>'entity_id','3'=>'entity_id','4'=>'created_at');
		//print_r($data['order'][0]);
		
		
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
		
		$sql="Select id, entity_id,status_type,created_at,added_by from dn_users_changed_status_log";
		$sql.="  where added_by = $id ";

		//$sql.="Select greater_mile_travel_cost from dn_rides where driver_id = 253";
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		$sql .= " order by ".$field." ".$direction;
		
		
		$totallogCount=DB::select(DB::raw($sql));
		$totallogCount= count($totallogCount);
		$sql .= " Limit ".$offset." , ".$limit;
		$logdetail=DB::select(DB::raw($sql));
		$Data="";
		
		foreach($logdetail as $log)
		{
			
			if(@$log->status_type=='user_active')
			{
				$status="Activated the user.";
				
			}elseif(@$log->status_type=='user_suspend')
			{
				$status="Suspended the user.";
				
			}elseif(@$log->status_type=='message_Delete')
			{
				$status="Deleted the message.";
				
			}elseif(@$log->status_type=='message_Archive')
			{
				$status="Archived the message.";

			}elseif(@$log->status_type=='user_revoked')
			{
				$status="Revoked the user.";

			}elseif(@$log->status_type=='dezi_credit_added')
			{
				$status="Dezi Credit Given.";

			}else{
				$status="N/A.";
			}
			
			$userid=$log->entity_id;

			$user = DB::table('role_user')
					->select(array('dn_users.first_name','dn_users.last_name','dn_users.unique_code','role_user.role_id'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$userid)->first();
			//echo $user->role_id;exit;

			if(@$user->role_id == 3)
			{
				@$username="Passenger name :".@$user->first_name.' '.@$user->last_name;
			}else if(@$user->role_id==4){
				@$username="Driver name :".@$user->first_name.' '.@$user->last_name;
			}else if(@$user->role_id==1){
				@$username="Admin :".@$user->first_name.' '.@$user->last_name;
				
			}else{
				@$username="N/A";
			}
			
			@$userUnique= (@$user->unique_code !='')?@$user->unique_code:"N/A";
			@$time=date("m/d/Y",strtotime(@$log->created_at));
			
			
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$status .'"' . ",". '"'.$username.'"' . ",". '"'.$userUnique .'"'.",". '"'.$time.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totallogCount).',
			  "recordsFiltered":'.($totallogCount).',
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
	
	
	
}
