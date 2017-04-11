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
use Services_Twilio;
use Mail;
use Illuminate\Http\Response;
include('/var/www/html/dezinowcom/app/customLib/firebase/firebase.php');

/**
 * @Class for Passenger  activities
 * 
 */
class NotificationsController extends BaseController
{
	
	/**
	 * CRON: Passenger Referal Check 
	 *
	**/

	public function passengerReferralCheck(){
		
		//send prmocodes to users with user_role = 3 and who has birthday today

		$promoData = DB::table('dn_passenger_promo_code')
							->select(array('dn_passenger_promo_code.*'))
							->where('dn_passenger_promo_code.type', 'referral' )
							->first();
	    $referred_credit = $promoData->amount;
	    $credit_type 	= 2; //1=DeziCredit By Admin, 2=referralCredit, 3=PromoCredit
	    $credit_txn_type 	= 'Cr'; //Cr = Credit, Dr = Debit
		//echo "<pre>"; print_r($promoData); echo "</pre>"; die();

		$allReferralUser = DB::table('dn_user_referrals')
							->select(array('dn_user_referrals.*'))
							->where('dn_user_referrals.status', 0 )
							->where('dn_user_referrals.referral_type', 3 )
							->get();

		foreach ($allReferralUser as $user) {
			
			$user_id = $user->user_id;
			$referred_by = $user->referred_by;

			$rideData = DB::table('dn_rides') 
							->select(array('dn_rides.*')) 
							->where( function ($query) use ($user_id)  { 
								$query->where( 'passenger_id', $user_id )
		                      	->where( 'status', 2 );
							})->first();

			if ( !empty($rideData)) {
				
	 			$where_referral = array(
	 				'user_id' => $user_id,
	 				'referred_by' => $referred_by
	 				);

	            $update_referral = DB::table('dn_user_referrals')
				->where( $where_referral )
				->update([
					'ride_taken' => 1,
					'ride_id' => $rideData->id,
					'status' => 1
					]);

				
				//Insert Dezicredit$deziCredit = DB::table('dn_passenger_credits')->where('user_id',$user_id)->orderBy('id', 'desc')->first();		
				$deziCredit = DB::table('dn_passenger_credits')->where('user_id',$referred_by)->orderBy('id', 'desc')->first();	

				if ( !empty($deziCredit)) {

					$credit_balance = $deziCredit->credit_balance + $referred_credit;

					$insertCreditID = DB::table('dn_passenger_credits')
					->insert(
						['user_id' => $referred_by, 
						'credit_type'=> $credit_type,
						'credit_txn_type' => $credit_txn_type,
						'credit_amount'=>$referred_credit,
						'credit_balance'=>$credit_balance]
					);

				} else {

					$insertCreditID = DB::table('dn_passenger_credits')
					->insert(
						['user_id' => $referred_by, 
						'credit_type'=> $credit_type,
						'credit_txn_type' => $credit_txn_type,
						'credit_amount'=>$referred_credit,
						'credit_balance'=>$referred_credit]
					);
				}

			} else {
				# "Referral not completed yet";
			}

		}

	}


	/**
  	 * @Function for manage notification
  	 *
  	**/
	public function manageNotification()
	{

		$adminId = Auth::id();
		
		$data 		= array();
		$data['states']=DB::table('dn_states')->get();

	 	$formData 	= Input::get();
	 	
 		if ( !empty($formData['notify']) )
 		{	
 			$notify_options = json_encode($formData['notify']);

		 	if ( !empty($formData['city_id']) ) 
		 	{
		 		$city_ids = $formData['city_id'];
		 		$message = $formData['message'];

		 		//Notify Passanger
		 		if (!empty( $formData['notify_passanger'] ) ) 
		 		{
		 			foreach ( $city_ids as $myCityID ) 
		 			{

		 				DB::enableQueryLog();// enable query first
			 			//get all new driver applicant users
						$allPassanger = DB::table('role_user') 
										->select(array('dn_users.*')) 
										->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')

										->where( function ($query) use ($myCityID)  { 
											$query->where( 'role_id', '!=', '3' )
					                      	->where( 'dn_users.city', $myCityID );
										})->get();

						if( !empty( $allPassanger ) )
						{
					 		foreach ($allPassanger as $userData) {
					 			//echo "<pre>"; print_r($userData); echo "</pre>"; die();
					 			$user_id = $userData->id;
                                $contact_number = $userData->country_phone_code.$userData->contact_number;
					 			$email 	= $userData->email;
					 			$last_name 	= $userData->first_name;
					 			$first_name 	= $userData->first_name;
					 			$city 	= $userData->city;
					 			/* if (!empty($formData['notify']['email'])) {
					 				$this->sendEmailReminder($email, $first_name, 'test' );
					 			 	echo $first_name;
					 			 	die("here");
					 			 }*/
                                if (in_array("email", $formData['notify'])){
                                    $this->sendEmailReminder($email, $first_name, 'Yoy have mail from DeziNow' );
                                }
                                if (in_array("sms", $formData['notify'])){
                                    $this->twileo_send($message, $contact_number);
                                }
                                if (in_array("push_notification", $formData['notify'])){
                                    $this->sendGoogleCloudMessage($user_id );
                                   }
					 			$insert_data = array(
										'user_name' => $first_name,
										'user_id' => $user_id,
										'via' => $notify_options,
										'city' => $myCityID,
										'message' => $message,
										'admin_user' => $adminId,
						                'time_stamp' => date('Y-m-d H:i:s')
						            );
						           	$insertGetId = DB::table('dn_admin_notifications')->insertGetId($insert_data);
					 			
					 		}
				 		}
				 		else
				 		{
				 			// echo "empty ha <br>";
				 		}
				 	}

		 		}
		 		// die('sfdsaf');
		 		//Notify Driver
		 		if (!empty( $formData['notify_driver'] ) ) 
		 		{
		 			foreach ( $city_ids as $myCityID ) 
		 			{
		 			
			 			//get all new driver applicant users
						$allDriver = DB::table('role_user') 
										->select(array('dn_users.*')) 
										->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')

										->where( function ($query) use ($myCityID)  { 
											$query->where( 'role_id', '!=', '4' )
					                      	->where( 'dn_users.city', /*$myCityID*/740 );
										})->get();

										//->paginate(config('admin.user.perpage'));

						//dd(DB::getQueryLog());
						// dd($allDriver);

				 		if( !empty( $allDriver ) )
						{		
					 		foreach ($allDriver as $userData) {

					 			//echo "<pre>"; print_r($userData); echo "</pre>"; die();
					 			$user_id = $userData->id;
					 			$first_name = $userData->first_name;
					 			$contact_number = $userData->contact_number;
					 			$email 	= $userData->email;
					 			$city 	= $userData->city;

					 			// if (!empty($formData['notify_via_sms'])) {
					 			// 	//$this->twileo_send($phone = '7814009418', $message = 'Messages To Phone devices');
					 				
					 			// }

					 			// if (!empty($formData['notify_via_email'])) {
					 			// 	//$this->sendEmailReminder( $email, $name, $subject );
					 				
					 			// }

					 			// if (!empty($formData['notify_via_push_notification'])) {
					 			// 	//$this->sendGoogleCloudMessage( $message, $type = 7, $to = 7 );
					 				
					 			// }
					 			$insert_data = array(
										'user_id' => $user_id,
										'user_name' => $first_name,
										'via' => $notify_options,
										'city' => $city,
										'message' => $message,
										'admin_user' => $adminId,
						                'time_stamp' => date('Y-m-d H:i:s')
						            );
						           	$insertGetId = DB::table('dn_admin_notifications')->insertGetId($insert_data);
					 			
					 		}
				 		}
				 	}

		 		}
		 	}

		 	session()->flash('message','Success, Message Has Been Saved Successfully');
		}

		return $this->view('users.notifications', $data );
	}
	



	/** 
 	 * function start for send mail 
 	**/
    private function sendEmailReminder($email, $name, $subject)
	{

        Mail::send('app.mails.adminNotifymail', ['name' => $name ], function ($m) use ($email, $name, $subject) {
            
            $m->from('dezinow@example.com', 'DeziNow');

            $m->to($email, $name)->subject($subject);
            /*echo $email;
            echo $name;
            echo $subject;
            die("here");*/
        });

        /*if (count(Mail::failures()) > 0) {
            return 0;
           die('0');
        } else {
        	
	        return 1;
	        die('1');
        }*/
	}
	/*--//function end for send mail--*/

	/**
	 * @var \User
	**/
 	protected $users;

	/**
  	 * @Function for manage notification
  	 *
  	**/
	public function manageNotificationLog( Request $request )
	{
		$data = $request->all();
		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')
						->paginate(config('admin.user.perpage'));
			
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
					->where('dn_users.city','!=','')
					->groupBy('dn_users.city')
					->orderBy('no_of_users')
					->get();
					//echo "<pre>"; print_r($citys[0] );die;
		$n=count($citys)-1;
		if($n==0)
		{
			
			$citiesCount=array('least'=>$citys,'most'=>$citys[$n]); 
		}
		@$citiesCount=array('least'=>$citys[0],'most'=>$citys[$n]); 
		return $this->view('users.notificationsLog', compact('users','citiesCount','states'));
	}


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
	                $html .= "<label><input type='checkbox' name='city_id[]' value='".$city->id."' class='city-check'>".$city->city."</label>";
	            $html .= "</div>";
			$html .= "</div>";
		}
			
      	$html .= "</div>";

      	echo $html;

		die();
	}

	/**
	 * @FUNCTION FOR AJAX CALL ON INDEX
	 * @Author : Vaibhav Bharti
	 * @Params : $request
    **/
	public function notificationLogAjax(Request $request)
	{
		/* initializing the variables */
		$data 	= $request->all();
		$limit 	= 10;
		$draw 	= $data['draw'];
		$offset = $data['start'];
		$searchString 	= $data['search']['value'];
		$startDate 		= $data['startDate'];
		$endDate 		= $data['endDate'];
		
		$orderfields 	= array('1'=>'user_name','2'=>'via','3'=>'time_stamp','4'=>'message','5'=>'city','6'=>'status');
		$field='id';
		$direction='ASC';

		/* code for order by data of user*/
		if(!empty($data['order'][0])){

			foreach($orderfields as $key=>$orderfield)
			{
				if($key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		
		/* code for searching of  user*/
		$sql = 'SELECT * FROM dn_admin_notifications WHERE 1=1 ';
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  time_stamp BETWEEN '$startDate' AND '$endDate'";
		}

		/*if($data['state']!='')
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
		}*/
		
		if(@$searchString!='')
		{
					
			$search = "%$searchString%";
			$sql .=" AND  (user_name LIKE '$search' or time_stamp LIKE '$search' or message LIKE '$search') ";
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
			$users = DB::table('dn_admin_notifications')
						->take($limit)->offset($offset)->whereIN('id',$usersList)->orderBy($field,$direction)->get();
						//print_r($users);
			$totalRecords = DB::table('dn_admin_notifications')
						->paginate(config('admin.user.perpage'));
		}
		$Data="";
		foreach($users as $user)
		{
			//echo $user->active;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			
			$view="<span class='label-success label '><a href='passenger-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
			//$view="<span class='label-success label '><a href='#'> View </a></span>";

			if($user->status==1) {
				$active='Active';
				$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
				// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
				
			}else{
				$active='Suspended';
				$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Active</a></span>&nbsp;|&nbsp;".$view;
			} 

			$active = 'Message In Queue';
			$user_via = json_decode($user->via);
			
			// echo "<pre>"; print_r($user_via); 

			$admin_user_id = $user->admin_user;
			
			$admin_user = DB::table('dn_users')->select(array('dn_users.*'))
							->where( function ($query) use ($admin_user_id)  { 
								$query->where( 'dn_users.id', $admin_user_id );
		                      	//->where( 'dn_users.city', 740 );
							})->first();
			
			$user_city_id = $user->city;
			$user_city = DB::table('dn_cities')->select(array('dn_cities.*'))
							->where( function ($query) use ($user_city_id)  { 
								$query->where( 'dn_cities.id', $user_city_id );
		                      	//->where( 'dn_users.city', 740 );
							})->first();
			
			$Data[]= "[". '"'.++$offset .'"' . ",". '"'.$user->user_name .'"'.",". '"'.implode(" , ",$user_via).'"' .",". '"'.date('m/d/Y', strtotime($user->time_stamp)).'"'.",". '"'.$user->message.'"'.",". '"'.$user_city->city.' '.$user_city->state_code.'"'.",". '"'.$active.'"'.",". '"' .$admin_user->first_name. '"'."]";
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
 	

 	/* /Send Notification STARTS HERE */
 	public function adminNotifications( request $request ) 
 	{
 		
 		die('admin/adminNotifications');
 	}


 	//send push notification to google apps user
    private function sendGoogleCloudMessage($to)
    {

        $apiKey = 'AIzaSyB-jqJ2r2acnn2QTpyjnEoRDJWvRf14aqw';   //// firebase server key
        $url = 'https://fcm.googleapis.com/fcm/send';
        $badge = 0;
        $sound = 'default';
        $date_time = date('Y-m-d H:i:s', time());
        $time_zone = date_default_timezone_get();
        $topic =  "'DeziNow$to' in topics";

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );



        $notification_data = array(    //// when application open then post field 'data' parameter work so 'message' and 'body' key should have same text or value
            'message'  => 'The Message From Admin Panel',
            'badge' => $badge,
            'notification_type' => 12,
            'date_time' => $date_time,
            'time_zone' => $time_zone
        );

        $notification = array(       //// when application close then post field 'notification' parameter work
            'body'  => 'The Message From Admin Panel',
            'sound' => $sound,
        );

        $post = array(
            'condition'         => $topic,
            'notification'      => $notification,
            "content_available" => true,
            'priority'          => 'high',
            'data'              => $notification_data
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        $result = curl_exec($ch);
       /* $result= json_decode($result);

        if ($result->message_id) {

             // return 1;
        } else {
            //return 0;
        }*/
        curl_close($ch);
    }
	//send messages on phone
    private function twileo_send($message, $phone)
    {
        //live
        $id = "ACef0bc2ba66b70340468cc67fca14390d";
        $token = "e12d7d9fac857f1b845d19e8e9bde841";

        $url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
        $from = "+16507535036";
        $to = $phone;
        $body = $message;
        $data = array(
            'From' => $from,
            'To' => $to,
            'Body' => $body,
        );
        $post = http_build_query($data);
        $x = curl_init($url);
        curl_setopt($x, CURLOPT_POST, true);
        curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
        curl_setopt($x, CURLOPT_POSTFIELDS, $post);
        $y = curl_exec($x);
        curl_close($x);
        //sms log
    }
	public function twileo_send_old($phone = '7814009418', $message = 'Messages To Phone devices')
    {
        $id = "ACef0bc2ba66b70340468cc67fca14390d";
        $token = "e12d7d9fac857f1b845d19e8e9bde841";

        $url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
        $from = "6507535036";

        $country_code = '+91';

        $to = $country_code.$phone;

        $body = "$message";
        $data = array(
            'From' => $from,
            'To' => $to,
            'Body' => $body
        );

        $post = http_build_query($data);
        $x = curl_init($url);
        curl_setopt($x, CURLOPT_POST, true);
        curl_setopt($x, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($x, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($x, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($x, CURLOPT_USERPWD, "$id:$token");
        curl_setopt($x, CURLOPT_POSTFIELDS, $post);
        $y = curl_exec($x);

        curl_close($x);

        return 1;

        //sms log
    }

	/* /Send Notification ENDS HERE */

	/**
  	 * @Function to send promo codes to users
  	 *
  	**/
	public function sendPromoCode()
	{

		//first check in 'dn_passenger_promos' if birthday promo codes is enabled in latest entry
		$sql="SELECT birthday_promo_code, birthday_promo_enable, birthday_promo_credit, anniversary_promo_enable, anniversary_promo_code, anniversary_promo_credit FROM  `dn_passenger_promos` ORDER BY id DESC LIMIT 1";
		$dn_passenger_promos=DB::select(DB::raw($sql));

		// dd($dn_passenger_promos[0]->birthday_promo_enable);
		// dd($dn_passenger_promos);
		if( !empty($dn_passenger_promos) )
		{

			if( $dn_passenger_promos[0]->birthday_promo_enable == '1' )
			{

				$birthdayPromoCode = $dn_passenger_promos[0]->birthday_promo_code;
				$birthdayPromoCredit = $dn_passenger_promos[0]->birthday_promo_credit;

				//send prmocodes to users with user_role = 3 and who has birthday today
				$sql="SELECT dn_users.id, dn_users.first_name, dn_users.last_name, dn_users.contact_number FROM dn_users ";
				$sql.="LEFT JOIN role_user ON  dn_users.id = role_user.user_id ";
				$sql.="WHERE role_user.role_id = 3 AND dn_users.contact_number != '' AND DATE_FORMAT(dn_users.dob,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d') ";
				// dd($sql);
				$users=DB::select(DB::raw($sql));


				
				//dd($users);
				if( !empty( $users ) )
				{
					foreach ($users as $user)
					{

						//we need to send promo code to user once a year so in 'dn_passenger_promos_check' check for year for this user_id
				
						$sql= "SELECT * FROM `dn_passenger_promos_check` where user_id = $user->id AND DATE_FORMAT(created_at,'%yyyy') = DATE_FORMAT(NOW(),'%yyyy') AND code_type = 'birthday_promo_code' ";
						$dn_passenger_promos_check=DB::select(DB::raw($sql));
						// echo count( $dn_passenger_promos_check );
						// dd($dn_passenger_promos_check);

						if(count( $dn_passenger_promos_check ) == 0 )
						{
							$contact_number = $user->contact_number;
							$message = "--- wishes you Happy Birthday. This is your promo code $birthdayPromoCode. Please use this today.";

							//insert data to 'dn_passenger_promos_check'
							DB::table('dn_passenger_promos_check')->insert(['user_id' => $user->id,'code_type'=>'birthday_promo_code','code'=>$birthdayPromoCode, 'credit' => $birthdayPromoCredit]);

							$this->twileo_send( $contact_number, $message);
							// $this->twileo_send( '8872478404', $message);
						}
					}
				}
			}//Happy Birthday

			if( $dn_passenger_promos[0]->anniversary_promo_enable == '1' )
			{

				$anniversaryPromoCode = $dn_passenger_promos[0]->anniversary_promo_code;
				$anniversaryPromoCredit = $dn_passenger_promos[0]->anniversary_promo_credit;

				$sql="SELECT dn_users.id, dn_users.first_name, dn_users.last_name, dn_users.contact_number FROM dn_users ";
				$sql.="LEFT JOIN role_user ON  dn_users.id = role_user.user_id ";
				$sql.="WHERE role_user.role_id = 3 AND dn_users.contact_number != '' AND DATE_FORMAT(dn_users.anniversary,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d') ";
				
				$users=DB::select(DB::raw($sql));
				
				// dd($users);
				if( !empty( $users ) )
				{
					foreach ($users as $user)
					{
						$sql= "SELECT * FROM `dn_passenger_promos_check` where user_id = $user->id AND DATE_FORMAT(created_at,'%yyyy') = DATE_FORMAT(NOW(),'%yyyy') AND code_type = 'anniversary_promo_code' ";
						$dn_passenger_promos_check=DB::select(DB::raw($sql));
						// echo count( $dn_passenger_promos_check );
						// dd($dn_passenger_promos_check);

						if(count( $dn_passenger_promos_check ) == 0 )
						{


							$contact_number = $user->contact_number;
							$message = "--- wishes you Happy Anniversary. This is your promo code $anniversaryPromoCode. Please use this today.";

							//insert data to 'dn_passenger_promos_check'
							DB::table('dn_passenger_promos_check')->insert(['user_id' => $user->id,'code_type'=>'anniversary_promo_code','code'=>$anniversaryPromoCode, 'credit'=>$anniversaryPromoCredit]);

							$this->twileo_send( $contact_number, $message);
							// $this->twileo_send( '8872478404', $message);
						}
					}
				}
			}//Happy Anniversary
		}
	}



}
