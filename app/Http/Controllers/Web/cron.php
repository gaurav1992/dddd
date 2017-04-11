<?php

namespace App\Http\Controllers\Web;

use DB;
use Mail;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Crypt;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use Validator;
use Redirect;
use Hash;
use dateTime;
use App\User;
use Socialite;
use Services_Twilio;

class cron extends Controller {
    
    //CRON URL`s crondeleteprofileuncompleteuser
    //http://www.dezinow.com/crondeleteprofileuncompleteuser
    //http://www.dezinow.com/cronPassengerReferralCheck
    //http://www.dezinow.com/cronDriverReferralBonus
    //http://www.dezinow.com/cronTierLevel
    //http://www.dezinow.com/cronDriverLicenseInsuranceExpire
    //http://www.dezinow.com/cronBirthdayAnniversaryPromoCode
    //http://www.dezinow.com/cronSendAdminNotification
    
    public function _sendNotificationEmail( $email, $name, $subject, $notification_message) {

        Mail::send('app.cron.notification-email', ['name' => $name, 'notification_message' => $notification_message], function ($m) use ($email, $name, $subject, $notification_message) {
            $m->from('cron@dezinow.com', 'DeziNow');
            $m->to($email, $name)->subject($subject);
        });
        
        if (count(Mail::failures()) > 0) {
            
            return 0;
        
        } else {

            return 1;
        }
    }

    //CRON
    public function cronSendAdminNotification() {

        echo "<h2 style='color:black'>CRON Admin Notification</h2>";

        $allNotificationInQueue = DB::table('dn_admin_notifications')
            ->where('status', '0' )
            ->get();

        $totalNotification = count($allNotificationInQueue);

        echo "<h4 style='color:blue'>Total Notification in Queue = $totalNotification</h4>";

        //echo "<pre>"; print_r($allNotificationInQueue); echo "</pre>";

        $totalSentnotification = 0;
        $totalNotSentNotification = 0;

        foreach ( $allNotificationInQueue as $data ) {

            //echo "<pre>"; print_r($data); echo "</pre>"; //die(' DATA ');

            $id = $data->id;
            $user_id = $data->user_id;
            $notification_message = $data->message;
            $user_name = $data->user_name;

            $send_via = json_decode($data->via);

            //echo "<pre>"; print_r($send_via); echo "</pre>"; //die(' via ');

            $user_data = DB::table('dn_users')
                ->select('dn_users.email', 'dn_users.country_phone_code', 'dn_users.contact_number', 'dn_users.device_token')
                ->where('id', $user_id )
                ->first();

            if (!empty($user_data)) {
                
                $totalSentnotification++;

                $email = $user_data->email;
                $contact_number = $user_data->country_phone_code.$user_data->contact_number;

                foreach ($send_via as $via) {
                    
                    if ( $via == 'sms' ) {

                        if ( !empty($user_data->country_phone_code) AND !empty($user_data->contact_number) ) {
                            
                            $this->twileo_send( $contact_number, $notification_message );
                            echo "<h4 style='color:green'><u>Mobile</u> Message Sent To = $contact_number</h4>";
                        }

                    } elseif ( $via == 'email' ) {

                        if (!empty($user_data->email)) {

                            $subject = "DeziNow - Notification By Dezi Admin";

                            $this->_sendNotificationEmail( $email, $user_name, $subject, $notification_message);
                            echo "<h4 style='color:green'><u>Email</u> Message Sent To = $email, User name = $user_name</h4>";
                        }

                    } elseif ( $via == 'push_notification' ) {

                        //echo "<h5>Send via push_notification</h5>";
                        $this->sendGoogleCloudMessage($notification_message, $type = 12, $to = $user_id, $from = 0);

                        echo "<h4 style='color:green'><u>Push Notification</u> Sent To = $user_id</h4>";

                    } else {
                        # do nothing...
                    }

                    //update the notification log
                     $where_log = array(
                        'id' => $id
                        );

                    $update_log = DB::table('dn_admin_notifications')
                    ->where( $where_log )
                    ->update(['status' => 1]);
                }

            } else { $totalNotSentNotification++; }

            //echo "<pre>"; print_r($user_data); echo "</pre>"; //die(' USER DATA ');
        }

        echo "</hr>";
        echo "<h4 style='color:green'>Total Sent Notifications = $totalSentnotification</h4>";
        echo "<h4 style='color:red'>Total Not Sent Notifications = $totalNotSentNotification</h4>";
    }


    public function _sendDocxExpireEmailAdmin( $user_id, $admin_email, $email, $name, $subject_admin, $contact_number ) {

        Mail::send('app.cron.expire-email-admin', ['name' => $name, 'subject_admin' => $subject_admin, 'user_id' => $user_id, 'contact_number' => $contact_number, 'email' => $email ], function ($m) use ($user_id, $admin_email, $name, $subject_admin, $contact_number) {

            $m->from('cron@dezinow.com', 'DeziNow');
            $m->to($admin_email, $name)->subject($subject_admin);
        });
        
        if (count(Mail::failures()) > 0) {
            
            return 0;
        
        } else {

            return 1;
        }
    }

    public function _sendDocxExpireEmail($email, $name, $subject) {

        Mail::send('app.cron.expire-email', ['name' => $name, 'subject' => $subject], function ($m) use ($email, $name, $subject) {
            $m->from('cron@dezinow.com', 'DeziNow');
            $m->to($email, $name)->subject($subject);
        });
        
        if (count(Mail::failures()) > 0) {
            
            return 0;
        
        } else {

            return 1;
        }
    }

    //CRON
    public function cronDriverLicenseInsuranceExpire() {
		
		$sql_query="SELECT dn_users.id,dn_users.first_name,dn_users.last_name,dn_users.email,dn_users_data.license_number, dn_driver_requests.licence_expiration
		FROM dn_users_data
		LEFT JOIN dn_driver_requests ON dn_driver_requests.user_id = dn_users_data.user_id
		LEFT JOIN dn_users ON dn_driver_requests.user_id = dn_users.id
		WHERE dn_users.is_driver_approved =1 AND dn_driver_requests.licence_expiration 
		BETWEEN
		  date(Now())
		AND 
		  date(date_add(Now(), INTERVAL 1 MONTH)) 
		order by dn_driver_requests.licence_expiration DESC";
		
						
		$driver_records = DB::select(DB::raw($sql_query));	
		//echo "<pre>"; print_r($driver_records); die;
		if(!empty($driver_records)){
			$i=1;
			date_default_timezone_set("Asia/Bangkok");
			$today = date('Y-m-d');
			foreach($driver_records as $drivers){
			/*START  == DOING IS SUSPENDED 2 for display notification on front end that your account will suspend in one month if you do not upload the requirement documents*/
				if($today!=$drivers->licence_expiration){
				
					 $where_referral = array(
						'dn_users.id' => $drivers->id,
					 );

					$update_referral = DB::table('dn_users')
					->where( $where_referral )
					->update([
						'dn_users.is_suspended' => 2,
					]);
				
					$full_name = $drivers->first_name." ".$drivers->last_name;
					//echo $full_name;
					$email = $drivers->email;
					
					$subject = "Dezinow Reminder: Licence Expiration on ".$drivers->licence_expiration;
					
					$title = "Reminder Licence Expiration";
					
					$bodyMsg="It is to addressed you that your licence(".$drivers->license_number.") is going to expire on ".$drivers->licence_expiration.", kindly renew it asap.";
					if(empty($full_name)){
					$full_name = "Guest";
					}	

					if(empty($email)){
						$email = "noreply@dezinow.com";
					}

					if(empty($bodyMsg)){
						$bodyMsg = "N/A";
					}

					\Mail::send('app.mails.licence_expiration_reminder', ['full_name' =>$full_name,'title' =>$title, 'bodyMessage' => $bodyMsg], function($m) use ($email, $full_name, $subject)
						{
							$m->from('noreply@dezinow.com', 'DeziNow');
							/*
							$AdminUsers=array();
							$AdminUsers = DB::table('dn_users')
											->select('dn_users.email','dn_users.full_name')
											->leftjoin('role_user', 'role_user.user_id', '=', 'dn_users.id')		
											->where('role_user.role_id','1')
											->get(); 
							foreach($AdminUsers as $admin_val){
								$m->cc($admin_val->email, $admin_val->full_name);
							}*/
							
							$m->to($email, $full_name)->subject($subject);
							
						}
					);	
					if (count(Mail::failures()) > 0) {
						echo $i.' : Error (Driver License): Something went wrong ! Email not sent to <b>'.$full_name.'('.$email.').</b><br/>';
						
					}else{
						echo $i.' : Success (Driver License): (One month notification)Email delivered to </b>'.$full_name.'('.$email.').</b><br/>';
						//echo $today."and driver dl exp date=".$drivers->licence_expiration.'<br>';

					}
				}
				
			/*END == DOING IS SUSPENDED 2 for display notification on front end that your account will suspend in one month if you do not upload the requirement documents*/	

				if($today==$drivers->licence_expiration){
					
					
					 $where_referral = array(
						'dn_users.id' => $drivers->id,
					 );

					$update_referral = DB::table('dn_users')
					->where( $where_referral )
					->update([
						'dn_users.is_suspended' => 1,
						'dn_users.is_driver_approved' => 0
					]);

					$full_name = $drivers->first_name." ".$drivers->last_name;
					//echo $full_name;
					$email = $drivers->email;
					
					$subject = "Dezinow Reminder: Licence Expiration on ".$drivers->licence_expiration;
					
					$title = "Reminder Licence Expiration";
					
					$bodyMsg="It is to addressed you that your licence(".$drivers->license_number.") is going to expire on ".$drivers->licence_expiration.", kindly renew it asap.";
					if(empty($full_name)){
					$full_name = "Guest";
					}	

					if(empty($email)){
						$email = "noreply@dezinow.com";
					}

					if(empty($bodyMsg)){
						$bodyMsg = "N/A";
					}

					\Mail::send('app.mails.licence_expiration_reminder', ['full_name' =>$full_name,'title' =>$title, 'bodyMessage' => $bodyMsg], function($m) use ($email, $full_name, $subject)
						{
							$m->from('noreply@dezinow.com', 'DeziNow');
							/*
							$AdminUsers=array();
							$AdminUsers = DB::table('dn_users')
											->select('dn_users.email','dn_users.full_name')
											->leftjoin('role_user', 'role_user.user_id', '=', 'dn_users.id')		
											->where('role_user.role_id','1')
											->get(); 
							foreach($AdminUsers as $admin_val){
								$m->cc($admin_val->email, $admin_val->full_name);
							}*/
							
							$m->to($email, $full_name)->subject($subject);
							
						}
					);	
					if (count(Mail::failures()) > 0) {
						echo $i.' : Error (Driver License): Something went wrong ! Email not sent to <b>'.$full_name.'('.$email.').</b><br/>';
						
					}else{
						echo $i.' : Success (Driver License): (Expired)Email delivered to </b>'.$full_name.'('.$email.').</b><br/>';
						//echo $today."and driver dl exp date=".$drivers->licence_expiration.'<br>';

					}
				}
					
				
				$i++;
			}
			
			
		}  
		
       $this->cronInsuranceExpire();
    }
    
    
     //CRON
    public function cronInsuranceExpire() {
		
		$sql_query="SELECT dn_users.id,dn_users.first_name,dn_users.last_name,dn_users.email, dn_driver_requests.insurance_expiration
		FROM dn_users_data
		LEFT JOIN dn_driver_requests ON dn_driver_requests.user_id = dn_users_data.user_id
		LEFT JOIN dn_users ON dn_driver_requests.user_id = dn_users.id
		WHERE dn_users.is_driver_approved =1 AND dn_driver_requests.insurance_expiration
		BETWEEN
		  date(Now())
		AND 
		  date(date_add(Now(), INTERVAL 1 MONTH)) 
		order by dn_driver_requests.insurance_expiration DESC";
		
						
		$driver_records = DB::select(DB::raw($sql_query));	
		
		if(!empty($driver_records)){
			$i=1;
			date_default_timezone_set("Asia/Bangkok");
			$today = date('Y-m-d');
			foreach($driver_records as $drivers){

				/*START  == DOING IS SUSPENDED 2 for display notification on front end that your account will suspend in one month if you do not upload the requirement documents*/
				if($today != $drivers->insurance_expiration){
					 $where_referral = array(
						'dn_users.id' => $drivers->id,
					 );

					$update_referral = DB::table('dn_users')
					->where( $where_referral )
					->update([
						'dn_users.is_suspended' => 2,
					]);
				
					$full_name = $drivers->first_name." ".$drivers->last_name;
					
					$email = $drivers->email;
					
					$subject = "Dezinow Reminder: Insurance Expiration on ".$drivers->insurance_expiration;
					
					$title = "Reminder Licence Expiration";
					
					$bodyMsg="It is to address you that your Insurance is going to expire on ".$drivers->insurance_expiration.", kindly renew it asap.";
					
					if(empty($full_name)){
						$full_name = "Guest";
					}

					if(empty($email)){
						$email = "noreply@dezinow.com";
					}

					if(empty($bodyMsg)){
						$bodyMsg = "N/A";
					}

					\Mail::send('app.mails.licence_expiration_reminder', ['full_name' =>$full_name,'title' =>$title, 'bodyMessage' => $bodyMsg], function($m) use ($email, $full_name, $subject)
						{
							$m->from('noreply@dezinow.com', 'DeziNow');
							/*
							$AdminUsers=array();
							$AdminUsers = DB::table('dn_users')
											->select('dn_users.email','dn_users.full_name')
											->leftjoin('role_user', 'role_user.user_id', '=', 'dn_users.id')		
											->where('role_user.role_id','1')
											->get(); 
							foreach($AdminUsers as $admin_val){
								$m->cc($admin_val->email, $admin_val->full_name);
							}*/
							
							$m->to($email, $full_name)->subject($subject);
							
						}
					);	

					if (count(Mail::failures()) > 0) {
						echo $i.' : Error (Insurance): Something went wrong ! Email not sent to <b>'.$full_name.'('.$email.').</b><br/>';
						
					}else{
						echo $i.' : Success (Insurance): (One month notification)Email delivered to </b>'.$full_name.'('.$email.').</b><br/>';
					}
				}
				/*END == DOING IS SUSPENDED 2 for display notification on front end that your account will suspend in one month if you do not upload the requirement documents*/
				if($today==$drivers->insurance_expiration){
					
					 $where_referral = array(
						'dn_users.id' => $drivers->id,
					 );

					$update_referral = DB::table('dn_users')
					->where( $where_referral )
					->update([
						'dn_users.is_suspended' => 1,
						'dn_users.is_driver_approved' => 0
					]);
					$full_name = $drivers->first_name." ".$drivers->last_name;
					
					$email = $drivers->email;
					
					$subject = "Dezinow Reminder: Insurance Expiration on ".$drivers->insurance_expiration;
					
					$title = "Reminder Licence Expiration";
					
					$bodyMsg="It is to address you that your Insurance is going to expire on ".$drivers->insurance_expiration.", kindly renew it asap.";
					
					if(empty($full_name)){
						$full_name = "Guest";
					}

					if(empty($email)){
						$email = "noreply@dezinow.com";
					}

					if(empty($bodyMsg)){
						$bodyMsg = "N/A";
					}

					\Mail::send('app.mails.licence_expiration_reminder', ['full_name' =>$full_name,'title' =>$title, 'bodyMessage' => $bodyMsg], function($m) use ($email, $full_name, $subject)
						{
							$m->from('noreply@dezinow.com', 'DeziNow');
							/*
							$AdminUsers=array();
							$AdminUsers = DB::table('dn_users')
											->select('dn_users.email','dn_users.full_name')
											->leftjoin('role_user', 'role_user.user_id', '=', 'dn_users.id')		
											->where('role_user.role_id','1')
											->get(); 
							foreach($AdminUsers as $admin_val){
								$m->cc($admin_val->email, $admin_val->full_name);
							}*/
							
							$m->to($email, $full_name)->subject($subject);
							
						}
					);	

					if (count(Mail::failures()) > 0) {
						echo $i.' : Error (Insurance): Something went wrong ! Email not sent to <b>'.$full_name.'('.$email.').</b><br/>';
						
					}else{
						echo $i.' : Success (Insurance): (expired)Email delivered to </b>'.$full_name.'('.$email.').</b><br/>';
					}
				}
				
				$i++;
			}
			
			
		}  
		
    }
    
    //CRON
    public function crondeleteprofileuncompleteuser() {
       
        DB::table('dn_users')
            ->where(['first_name' => '', 'last_name' => ''])
            ->delete();
        $lower_date = date('Y-m-d', time() - 3600 *24* 1);
        DB::table('dn_user_verification')
            ->where('created_at','<=', $lower_date)
            ->delete();
        DB::table('missed_ride_request')
            ->where('request_time','<=', $lower_date)
            ->delete();
    }
    public function cronPassengerReferralCheck() {
        
        echo "<h2 style='color:black'>CRON Passenger Referral Check</h2>";

        //send prmocodes to users with user_role = 3 and who has birthday today

        $countTotalReferrals = 0;
        $completedCountTotalReferrals = 0;

        $promoData = DB::table('dn_passenger_promo_code')
                            ->select(array('dn_passenger_promo_code.*'))
                            ->where('dn_passenger_promo_code.type', 'referral' )
                            ->first();
        $referred_credit = $promoData->amount;
        $credit_type    = 2; //1=DeziCredit By Admin, 2=referralCredit, 3=PromoCredit
        $credit_txn_type    = 'Cr'; //Cr = Credit, Dr = Debit
        //echo "<pre>"; print_r($promoData); echo "</pre>"; die();

        $allReferralUser = DB::table('dn_user_referrals')
                            ->select(array('dn_user_referrals.*'))
                            ->where('dn_user_referrals.status', 0 )
                            ->where('dn_user_referrals.referral_type', 3 )
                            ->get();

        //echo "<pre>"; print_r($allReferralUser); echo "</pre>"; die();

        foreach ($allReferralUser as $user) {
            
            $countTotalReferrals++;

            $user_id = $user->user_id;
            $referred_by = $user->referred_by;

            $rideData = DB::table('dn_rides') 
                            ->select(array('dn_rides.*')) 
                            ->where( function ($query) use ($user_id)  { 
                                $query->where( 'passenger_id', $user_id )
                                ->where( 'status', 2 );
                            })->first();

            if ( !empty($rideData)) {

                $completedCountTotalReferrals++;
                
                $where_referral = array(
                    'user_id' => $user_id,
                    'referred_by' => $referred_by,
                    'referral_type' => 3,
                    );

                $update_referral = DB::table('dn_user_referrals')
                ->where( $where_referral )
                ->update([
                    'ride_taken' => 1,
                    'ride_id' => $rideData->id,
                    'status' => 1
                    ]);

                
                //Insert Dezicredit to referred_by  
                $deziCredit = DB::table('dn_passenger_credits')->where('user_id',$referred_by)->orderBy('id', 'desc')->first(); 
                
                //referral user
                //$deziCredit = DB::table('dn_passenger_credits')->where('user_id',$user_id)->orderBy('id', 'desc')->first(); 

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

                 //referral user
                $deziCreditreferral = DB::table('dn_passenger_credits')->where('user_id',$user_id)->orderBy('id', 'desc')->first(); 
                 if ( !empty($deziCreditreferral)) {

                    $credit_balance = $deziCreditreferral->credit_balance + $referred_credit;

                    $insertCreditID = DB::table('dn_passenger_credits')
                    ->insert(
                        ['user_id' => $user_id, 
                        'credit_type'=> $credit_type,
                        'credit_txn_type' => $credit_txn_type,
                        'credit_amount'=>$referred_credit,
                        'credit_balance'=>$credit_balance]
                    );

                } else {

                    $insertCreditID = DB::table('dn_passenger_credits')
                    ->insert(
                        ['user_id' => $user_id, 
                        'credit_type'=> $credit_type,
                        'credit_txn_type' => $credit_txn_type,
                        'credit_amount'=>$referred_credit,
                        'credit_balance'=>$referred_credit]
                    );
                }
                echo "<h4 style='color:green'>$user_id Referred By $referred_by = Y </h4>";

            } else {
                # "Referral not completed yet";
                echo "<h4 style='color:red'>$user_id Referred By $referred_by = X </h4>";
            }

        }

        echo "<h4 style='color:blue'>Completed Referral Passengers For This CRON = $completedCountTotalReferrals</h4>";
        echo "<h4 style='color:blue'>Total Referral Passengers = $countTotalReferrals</h4>";
    }
 

    /**
     * CRON FOR CALCULATING DRIVER TIER LEVEL
     *
    **/

    public function _update_deziBonus( $user_id, $referal_credit) {

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

    }


    /**
     * CRON FOR CALCULATING DRIVER TIER LEVEL
     *
    **/
    public function cronDriverReferralBonus() {
        
        echo "<h2 style='color:black'>CRON Driver Referral Bonus</h2>";

        //die('cronDriverReferralBonus');
        $current_datetime = date( 'Y-m-d H:i:s');

        $bonus_data = DB::table('dn_driver_promos')
                    ->select(array('dn_driver_promos.*'))
                    ->where('dn_driver_promos.is_active', '1' )
                    ->first();

        $referal_credit_for_20      = $bonus_data->referal_credit_for_20;
        $referal_credit_for_5_10    = $bonus_data->referal_credit_for_5_10;

        $allReferralDriver = DB::table('dn_user_referrals')
                            ->select(array('dn_user_referrals.*'))
                            //->where('dn_user_referrals.status', 0 )
                            ->where('dn_user_referrals.referral_type', 4 )
                            ->get();

        $countAllReferralDriver = count($allReferralDriver);
        echo "<h4 style='color:blue'>Total Referral Drivers = $countAllReferralDriver</h4>";

        foreach ($allReferralDriver as $referralDriver) {
            
            //Our dates
            //echo "<pre>"; print_r($referralDriver); echo "</pre>";

            $driver_id      = $referralDriver->user_id;
            $referred_by    = $referralDriver->referred_by;

            $driver_bonus_level = $referralDriver->driver_bonus_level; //0=none, 1= completed 5 rides, 2=Completed 10 rides, 3=Completed 20 rides
            
            //if time is with in three months only

            $created_at = $referralDriver->created_at;
            $today_date_time = date("Y-m-d H:i:s");

            //Convert them to timestamps.
            $date1Timestamp = strtotime($created_at);
            $date2Timestamp = strtotime($today_date_time);
             
            //Calculate the difference.
            $difference = $date2Timestamp - $date1Timestamp;
             
            //Convert seconds into days.
            $total_days = floor($difference / (60*60*24) );
            
            if ( $total_days > 90 ) {
                
                # update referral status to TIME exceeds from 90 days
                 $update_bonus_level=DB::table('dn_user_referrals')
                ->where('user_id', $driver_id)
                ->where('referred_by', $referred_by)
                ->where('referral_type', 4)
                ->update(['status' => 2, 'updated_at' => $current_datetime]);
            
                 echo "<h4 style='color:red'><i><u>$driver_id</u></i> Completed 90 days Referred By <i><u>$referred_by</i></u> = 3 Months X </h4>";

            } else {

                $rideCount = DB::table('dn_rides') 
                                ->select(array('dn_rides.id')) 
                                ->where( function ($query) use ($driver_id)  { 
                                    $query->where( 'driver_id', $driver_id )
                                    ->where( 'status', 2 );
                                })->count();
            
                /*if ($driver_bonus_level == 0 && $rideCount >= 5) {
                    # do nothing
                    $update_bonus_level=DB::table('dn_user_referrals')
                    ->where('user_id', $driver_id)
                    ->where('referred_by', $referred_by)
                    ->update(['driver_bonus_level' => 1,'status' => 1, 'updated_at' => $current_datetime]);

                    $this->_update_deziBonus( $user_id=$referred_by, $referal_credit_for_5_10);

                    echo "<h4 style='color:green'><u>$driver_id</u> Completed $rideCount Rides | Referred By <u>$referred_by</u> | Dezi bonus = $referal_credit_for_5_10</h4>";
                    
                } else*/

                if ($driver_bonus_level == 0 && $rideCount >= 10) {
                    # do nothing
                    $update_bonus_level=DB::table('dn_user_referrals')
                    ->where('user_id', $driver_id)
                    ->where('referred_by', $referred_by)
                    ->where('referral_type', 4)
                    ->update(['driver_bonus_level' => 2, 'updated_at' => $current_datetime,'status'=>1]);
                    
                    $this->_update_deziBonus( $user_id=$referred_by, $referal_credit_for_5_10);


                    echo "<h4 style='color:green'><u>$driver_id</u> Completed $rideCount Rides | Referred By <u>$referred_by</u> | Dezi bonus = $referal_credit_for_5_10</h4>";

                } elseif ($driver_bonus_level == 2 && $rideCount >= 20) {
                    # do nothing
                       // echo "test";exit;
                    $update_bonus_level=DB::table('dn_user_referrals')
                    ->where('user_id', $driver_id)
                    ->where('referred_by', $referred_by)
                    ->where('referral_type', 4)
                    ->update(['driver_bonus_level' => 3, 'ride_taken' => 1, 'updated_at' => $current_datetime,'status'=>1]);

                    $this->_update_deziBonus( $user_id=$referred_by,  $referal_credit_for_20);

                    echo "<h4 style='color:green'><u>$driver_id</u> Completed $rideCount Rides | Referred By <u>$referred_by</u> | Dezi bonus = $referal_credit_for_20</h4>";

                } else {

                    echo "<h4 style='color:red'><u>$driver_id</u> Completed $rideCount Rides | Referred By <u>$referred_by</u></h4>";
                }
            }
        }
    }


    /**
     * CRON FOR CALCULATING DRIVER TIER LEVEL
     *
    **/
    public function _check_to_reset_tier_level( $day_number ) {
        
        $tier_data = DB::table('dn_driver_tier_data')->select(array('dn_driver_tier_data.reset_status'))->first();
        
        //echo "<pre>"; print_r($tier_data); die(' _check_to_reset_tier_level');

        $dayName = date('l');
        
        if (!empty($tier_data)) {
            
            //if ( $tier_data->reset_status == 6 && $day_number == 0) {
            if ($day_number == 0) {
                
                $reset = [ 'reset_status' => $day_number,
							//'total_rides' => 0,
                            'total_active_hours' => 0,
                            'scheduled_hours' => 0
                            //'cancelation_rate' => 0,
                            //'acceptance_rate' => 0
                            ];
                DB::table('dn_driver_tier_data')->update($reset);
            
                echo "<h4 style='color:RED'>Its $dayName today | <u>TIER LEVELS RESET FOR ALL DRIVERS</u> = Y</h4>";

            }/* else {
                
                $update = [ 'reset_status' => $day_number ];
                DB::table('dn_driver_tier_data')->update($update);

                echo "<h4 style='color:blue'>Its $dayName Today | <u>TIER LEVELS RESET</u> = $day_number</h4>";
            } */  
        }

    }

   


    /**
     * calculate and update cron tier level
     * 
     * 
    **/
    public function cronTierLevel() {
    	
        
        echo "<h2 style='color:black'>CRON driver Tier Level</h2>";
  
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
        //print_r($scheduled_time);exit;
        $driverDataSQL = "SELECT dn_users.id AS ID, dn_users_data.tiers_level AS tierLevel FROM dn_users 
        LEFT JOIN role_user ON dn_users.id = role_user.user_id 
        LEFT JOIN dn_users_data ON dn_users.id = dn_users_data.user_id 
        WHERE role_user.role_id = 4 ORDER BY  dn_users.id DESC ";       
        $driverData = DB::select(DB::raw($driverDataSQL));
       // echo "<pre>"; print_r($driverData); echo "</pre>"; die('driverData');

        $totalCronedDriver = count($driverData);
        $totalEligibleDriver = 0;
        
        
        //check and update tier level for each driver
        foreach ($driverData as $driver) {
            
            $driver_id = $driver->ID;

            //$driver_id = 439;

            $tierLevel = $driver->tierLevel;

            /*$hourlogsSQL = "SELECT date(login_time) AS date, time(login_time) AS login_time, time(logout_time) AS  logout_time, TIMEDIFF(logout_time, login_time) AS duration FROM dn_user_logs  WHERE user_id=$driver_id AND created_at >= '".$sunday."' AND created_at <= '".$saturday."' ";*/
            
            $hourlogsSQL = "SELECT user_id AS driverID, date(login_time) AS date, time(login_time) AS login_time, time(logout_time) AS  logout_time, TIMEDIFF(logout_time, login_time) AS duration FROM dn_driver_logs  WHERE user_id=$driver_id AND status=0 AND login_time >= '".$sunday."' AND login_time <= '".$saturday."' ";
            $hourlogs = DB::select( DB::raw($hourlogsSQL) );
            $total_active_hours = 0;
            $_total_active_hours=0;
            $total_hours = 0;
            $peekLoop = 0;
            $finalPeekTime =0;
           
            $scheduled_hours = 0;
            foreach ($hourlogs as $logs){				
				$total_scheduled_hours = 0;	                
                if(!empty($logs->duration)){                    
                    $hourdiff = abs( round((strtotime($logs->logout_time) - strtotime($logs->login_time))/3600, 4) ); 
                    $total_hours += $hourdiff;
                }
                
                if ($logs->date == $today) {
					if(!empty($logs->logout_time)){
						if($logs->logout_time !='0000-00-00 00:00:00'){
							if($peekLoop==0){
								$level_from_time = $logs->date.' '.$scheduled_time->from_time[$day_number];
								$level_to_time = $logs->date.' '.$scheduled_time->to_time[$day_number];

								$peakHoursSQL = "SELECT SEC_TO_TIME( SUM( TIME_TO_SEC( TIMEDIFF(logout_time, login_time)  ) ) ) AS peekhrs  FROM `dn_driver_logs` WHERE status = 0 AND `login_time` >= '".$level_from_time."' AND `logout_time` <= '".$level_to_time."' AND user_id=".$logs->driverID;
								//echo $peakHoursSQL;exit;

								$phourlogs = DB::select( DB::raw($peakHoursSQL) );
								 
								if($phourlogs[0]->peekhrs !=''){
									$peekHoursJob = $phourlogs[0]->peekhrs;
								}else{
									$peekHoursJob = '00:00:00';
								}
								 
								$seconds = strtotime("1970-01-01 $peekHoursJob UTC");
								//echo $seconds; echo "</pre>"; die;
								$scheduled_hours = abs( round($seconds/3600,2));
								
							}
							$peekLoop = 1;	
						}
					}
				}	
            }
          
			
            $total_active_hours = number_format((float)$total_hours, 2, '.', ''); //1. total_active_hours
			//echo "<pre>"; print_r($scheduled_hours); echo "</pre>"; die;

            //calculate scheduled time    
            
            //calculating total rides rate
            $totalRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->count();
            
            if ($totalRides != 0) {

                //calculating total canceled rides rate
                /*
                $totalCanceledRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->where('status','3')->count();
                $totalAcceptedRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->where('status','!=','0')->count();
				*/
				$totalCanceledRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->whereIn('status',array('3','6'))->count();
				
				$totalMissedRides = $totalRides = DB::table('missed_ride_request')->select(array('missed_ride_request.*'))->where('driver_id', $driver_id)->count();
                
                $totalAcceptedRides = DB::table('dn_rides')->select(array('dn_rides.*'))->where('driver_id', $driver_id)->whereIn('status',array('2'))->count();
                
                
				if($totalMissedRides >= 0){
					$totalCanceledRides = $totalCanceledRides+$totalMissedRides;
					$totalRides = $totalCanceledRides+$totalAcceptedRides;
				}
				
				//echo "$totalRides".' => '.$totalAcceptedRides.'=>'.$totalCanceledRides.'<br>'; die;
				if($totalRides > 0){
					$cancelation_rate = round( (( $totalCanceledRides/$totalRides ) * 100),2 );
					$acceptance_rate = round( (( $totalAcceptedRides/$totalRides ) * 100),2 );
				}else{
					$cancelation_rate=0;
					$acceptance_rate=0;
				}              

                //get and update tier level        
                //echo "<pre>"; print_r($bonusData); echo "</pre>";
                $current_tier_level = $tierLevel;

                //update tier data if driver exists or insert if not exists
                $tier_data_exists = DB::table('dn_driver_tier_data')->select(array('dn_driver_tier_data.driver_id'))->where('driver_id',$driver_id)->first();
                
                if($tier_data_exists === NULL) {


                    $insert = ['driver_id' => $driver_id,
                            'total_rides' => $totalRides,
                            'total_active_hours' => $total_active_hours,
                            'scheduled_hours' => $scheduled_hours,
                            'cancelation_rate' => $cancelation_rate,
                            'acceptance_rate' => $acceptance_rate ];

                    DB::table('dn_driver_tier_data')->insert($insert);
                
                } else {

                    $get_driver_tier_data = DB::table('dn_driver_tier_data')->select('*')
                    ->where('driver_id',$driver_id)
                    ->first();

                    $last_scheduled_hours = $get_driver_tier_data->scheduled_hours;
                    $_scheduled_hours = number_format((float)($last_scheduled_hours+$scheduled_hours), 2, '.', '');

                    $last_total_active_hours = $get_driver_tier_data->total_active_hours;
                    $_total_active_hours = number_format((float)($last_total_active_hours+$total_active_hours), 2, '.', '');

                    $updateSQL = "UPDATE  dn_driver_tier_data SET total_rides = $totalRides, scheduled_hours = $_scheduled_hours, total_active_hours = $_total_active_hours, cancelation_rate = $cancelation_rate, acceptance_rate = $acceptance_rate, updated_at = '".$current_datetime."' WHERE driver_id =$driver_id";

                    DB::statement($updateSQL);
                }
                // echo "incron=".$_total_active_hours."done</br>";
                $this->_update_tier_level( $driver_id, $current_tier_level, @$_total_active_hours, $_scheduled_hours, $cancelation_rate, $acceptance_rate);    
                $totalEligibleDriver++;

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

                    $get_driver_tier_data = DB::table('dn_driver_tier_data')->select('*')
                    ->where('driver_id',$driver_id)
                    ->first();

                    $last_scheduled_hours = $get_driver_tier_data->scheduled_hours;
                    $_scheduled_hours = number_format((float)($scheduled_hours), 2, '.', '');

                    $last_total_active_hours = $get_driver_tier_data->total_active_hours;
                    $_total_active_hours = number_format((float)($total_active_hours), 2, '.', '');

                    $updateSQL = "UPDATE  dn_driver_tier_data_log SET total_rides = $totalRides, scheduled_hours = $_scheduled_hours, total_active_hours = $_total_active_hours, cancelation_rate = $cancelation_rate, acceptance_rate = $acceptance_rate, 
                            updated_at = '".$current_datetime."' WHERE driver_id =$driver_id AND week = $weekNumber AND year = $year";

                    DB::statement($updateSQL);
                }

            } 
            $driverLogUpdate = "UPDATE dn_driver_logs SET status = 1 WHERE user_id = $driver_id AND logout_time != '0000-00-00 00:00:00'  AND login_time >= '".$sunday."' AND login_time <= '".$saturday."'";
            
        //echo $driverLogUpdate;exit;
            DB::statement($driverLogUpdate);       
            
        }

        echo "<hr>";
        echo "<h4 style='color:blue'>Total Croned Driver = $totalCronedDriver</h4>";
        echo "<h4 style='color:blue'>Total Eligible Driver = $totalEligibleDriver</h4>";



        //old tier code done
    }

	
	 public function _update_tier_level( $driver_id, $current_tier_level, $total_active_hours, $scheduled_hours, $cancelation_rate, $acceptance_rate) {

			$bonus_data = DB::table('dn_driver_bonus')->select(array('*'))->where('is_active','1')->first();
		
			$total_hrs = $bonus_data->total_hrs_gold;
			$total_hrs_schedule = $bonus_data->total_hrs_schedule_gold;
			$cancellation = $bonus_data->cancellation_gold;
			$acceptance = $bonus_data->acceptance_gold;
			
		//GOLD CHECK
		 if ( $total_active_hours >= $total_hrs && $scheduled_hours >= $total_hrs_schedule && $cancelation_rate <= $cancellation && $acceptance_rate >= $acceptance) {
				
				$total_hrs = $bonus_data->total_hrs_platinum;
				$total_hrs_schedule = $bonus_data->total_hrs_schedule_platinum;
				$cancellation = $bonus_data->cancellation_platinum;
				$acceptance = $bonus_data->acceptance_platinum;
				
				$update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 2]);
				$msg = "<h4 style='color:#F75528'>DRIVER <u>$driver_id</u> UPGRADED to tier level <u>GOLD</u></h4>";
				
				//Platinum CHECK	
				 if ( $total_active_hours >= $total_hrs && $scheduled_hours >= $total_hrs_schedule && $cancelation_rate <= $cancellation && $acceptance_rate >= $acceptance) {
					
						$total_hrs = $bonus_data->total_hrs_diamond;
						$total_hrs_schedule = $bonus_data->total_hrs_schedule_diamond;
						$cancellation = $bonus_data->cancellation_diamond;
						$acceptance = $bonus_data->acceptance_diamond;
						
						$update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 3]);
				
				        $msg = "<h4 style='color:#A4A234'>DRIVER <u>$driver_id</u> UPGRADED to tier level <u>PLATINUM</u></h4>";
							
						//Diamond CHECK		
						if ( $total_active_hours >= $total_hrs && $scheduled_hours >= $total_hrs_schedule && $cancelation_rate <= $cancellation && $acceptance_rate >= $acceptance) {
								 $update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 4]);

								 $msg = "<h4 style='color:#E4FA8D'>DRIVER <u>$driver_id</u> upgrated to tier level <u>DIAMOND</u></h4>";
						}
			 }
		}else{
			$update_tier=DB::table('dn_users_data')->where('user_id', $driver_id) ->update(['tiers_level' => 1]);
            $msg = "<h4 style='color:#C0C0C0'>DRIVER <u>$driver_id</u> UPGRADED to tier level <u>SILVER</u></h4>";
		}
		echo $msg;
	}	
    /**
     * @Function to send promo codes to users
     *
    **/
    public function cronBirthdayAnniversaryPromoCode() {

        echo "<h2 style='color:black'>CRON Birthday Anniversary Promo Code</h2>";

        //first check in 'dn_passenger_promos' if birthday promo codes is enabled in latest entry
        $sql="SELECT * FROM  `dn_passenger_promo_code`  WHERE dn_passenger_promo_code.type = 'birthday' ORDER BY id DESC LIMIT 1";
        $dn_passenger_promos=DB::select(DB::raw($sql));

        if( !empty($dn_passenger_promos) ) {
        	
            if($dn_passenger_promos[0]->status=='1') {

                $birthdayPromoCode = $dn_passenger_promos[0]->code;
                $birthdayPromoCredit = $dn_passenger_promos[0]->amount;
               
                //send prmocodes to users with user_role = 3 and who has birthday within this month
                $sql="SELECT dn_users.id, dn_users.first_name, dn_users.last_name,dn_users.full_name,dn_users.email, dn_users.country_phone_code, dn_users.contact_number, dn_users.email FROM dn_users ";
                $sql.="LEFT JOIN role_user ON  dn_users.id = role_user.user_id ";
                $sql.="WHERE dn_users.active = '1' AND role_user.role_id = 3 AND dn_users.	contact_number != '' AND  DAYOFYEAR(dn_users.dob) BETWEEN 
    				DAYOFYEAR('2011-01-07' - INTERVAL 15 DAY) AND (DAYOFYEAR('2011-01-07' - INTERVAL 15 DAY) + 21) OR DAYOFYEAR(dn_users.dob) BETWEEN 
    				(DAYOFYEAR('2011-01-07' + INTERVAL 15 DAY) - 21) AND DAYOFYEAR('2011-01-07' + INTERVAL 15 DAY)";
               	//echo $sql;die;
                $users=DB::select(DB::raw($sql));
                //print_r($dn_passenger_promos);
                //print_r($users);die;
                $totalBirthdays = count($users);
                echo "<h4 style='color:blue'>Total Birthdays = $totalBirthdays</h4>";

                //dd($sql);

                //echo "<pre>"; print_r($users); echo "</pre>"; die('users');
                
                //dd($users);
                if( !empty( $users ) ) {

                    foreach ($users as $user) {

                        //Check to send promo code once in a year, check in 'dn_passenger_promos_check' if promo code already sent to this user_id
            
                        $sql= "SELECT * FROM `dn_passenger_promos_check` where user_id = $user->id AND DATE_FORMAT(created_at,'%yyyy') = DATE_FORMAT(NOW(),'%yyyy') AND code_type = 'birthday_promo_code' ";
                        $dn_passenger_promos_check=DB::select(DB::raw($sql));
                        
                        // echo count( $dn_passenger_promos_check );
                        // dd($dn_passenger_promos_check);

                        $contact_number = $user->country_phone_code.$user->contact_number;

                        if(count( $dn_passenger_promos_check ) == 0 ) {

                            $message = "DeziNow wishes you Happy Birthday and a promo balance of $".$birthdayPromoCredit." Please use '".$birthdayPromoCode."' to use the promo balance.";

                            //insert data to 'dn_passenger_promos_check'
                            DB::table('dn_passenger_promos_check')->insert(['user_id' => $user->id,'code_type'=>'birthday_promo_code','code'=>$birthdayPromoCode, 'credit' => $birthdayPromoCredit]);

                            $this->twileo_send( $contact_number, $message);
                            $this->sendGoogleCloudMessage($message, $type = 14, $to = $user->id, $from = 1);
                            $this->_sendNotificationEmail( $user->email, $user->full_name, 'DeziNow wishes you Happy Birthday', $message);

                            echo "<h4 style='color:green'>Birthday PROMO CODE Sent To = $contact_number</h4>";
                        } else {
                            echo "<h4 style='color:green'>Birthday PROMO CODE Already Sent To = $contact_number</h4>";
                        }
                    }
                }
            } //Happy Birthday
            $sql2="SELECT * FROM  `dn_passenger_promo_code`  WHERE dn_passenger_promo_code.type = 'ani' ORDER BY id DESC LIMIT 1";
            $dn_passenger_promos2=DB::select(DB::raw($sql2));

            if( @$dn_passenger_promos2[0]->status == '1' ) {

                $anniversaryPromoCode = $dn_passenger_promos2[0]->code;
                $anniversaryPromoCredit = $dn_passenger_promos2[0]->amount;

                $sql="SELECT dn_users.id, dn_users.first_name, dn_users.last_name,dn_users.full_name,dn_users.email, dn_users.contact_number, dn_users.country_phone_code FROM dn_users ";
                $sql.="LEFT JOIN role_user ON  dn_users.id = role_user.user_id ";
                $sql.="WHERE dn_users.active = '1' AND role_user.role_id = 3 AND dn_users.contact_number != '' AND  DAYOFYEAR(dn_users.anniversary) BETWEEN 
    				DAYOFYEAR('2011-01-07' - INTERVAL 15 DAY) AND (DAYOFYEAR('2011-01-07' - INTERVAL 15 DAY) + 21) OR DAYOFYEAR(dn_users.anniversary) BETWEEN 
    				(DAYOFYEAR('2011-01-07' + INTERVAL 15 DAY) - 21) AND DAYOFYEAR('2011-01-07' + INTERVAL 15 DAY) "; 
                
                $users=DB::select(DB::raw($sql));
                
                $totalAnniversary = count($users);
                echo "<h4 style='color:blue'>Total Anniversary = $totalAnniversary</h4>";

                // dd($users);
                if( !empty( $users ) ) {

                    foreach ($users as $user) {

                        $sql= "SELECT * FROM `dn_passenger_promos_check` where user_id = $user->id AND DATE_FORMAT(created_at,'%yyyy') = DATE_FORMAT(NOW(),'%yyyy') AND code_type = 'anniversary_promo_code' ";
                        $dn_passenger_promos_check=DB::select(DB::raw($sql));
                        // echo count( $dn_passenger_promos_check );
                        // dd($dn_passenger_promos_check);

                        $contact_number = $user->country_phone_code.$user->contact_number;

                        if(count( $dn_passenger_promos_check ) == 0 ) {

                            $message = "DeziNow wishes you Happy Anniversary and a promo balance of $".$anniversaryPromoCredit." Please use  '".$anniversaryPromoCode."' to use the promo balance.";

                            //insert data to 'dn_passenger_promos_check'
                            DB::table('dn_passenger_promos_check')->insert(['user_id' => $user->id,'code_type'=>'anniversary_promo_code','code'=>$anniversaryPromoCode, 'credit'=>$anniversaryPromoCredit]);

                            $this->twileo_send( $contact_number, $message);
                            $this->sendGoogleCloudMessage($message, $type = 13, $to = $user->id, $from = 1);
                            $this->_sendNotificationEmail( $user->email, $user->full_name, 'DeziNow wishes you Happy Anniversary', $message);

                            echo "<h4 style='color:green'>Anniversary PROMO CODE Sent To = $contact_number</h4>";
                        } else {
                            echo "<h4 style='color:green'>Anniversary PROMO CODE Already Sent To = $contact_number</h4>";
                        }
                    }
                }
            }//Happy Anniversary

        }
    }


    //send messages on phone
    public function twileo_send($phone = '', $message = '') {

        $id = "ACef0bc2ba66b70340468cc67fca14390d";
        $token = "e12d7d9fac857f1b845d19e8e9bde841";

        $url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
        $from = "6507535036";
         
        $to = $phone;

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

        //echo "<pre>"; print_r($y); echo "</pre>";

        return 1;
    }

	function hoursToMinutes($hours) 
	{ 
		$minutes = 0; 
		if (strpos($hours, ':') !== false) 
		{ 
			// Split hours and minutes. 
			list($hours, $minutes) = explode(':', $hours); 
		} 
		return $hours * 60 + $minutes; 
	} 
	
    public function sendGoogleCloudMessage($title, $type = 12, $to, $from) {

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
            'message'  => $title,
            'badge' => $badge,
            'notification_type' => $type,
            'sender_id' => $from,
            'receiver_id' => $to,
            'date_time' => $date_time,
            'time_zone' => $time_zone
        );

        $notification = array(       //// when application close then post field 'notification' parameter work
            'body'  => $title,
            'sound' => $sound,
        );

        $post = array(
            'condition'         => $topic,
            'notification'      => $notification,
            "content_available" => true,
            'priority'          => 'high',
            'data'              => $notification_data
        );
        //var_dump($post); die;
        // return ['status' => 3, 'message' => $post];

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        $result = curl_exec($ch);
        //  var_dump($result); die;

        $result= json_decode($result);

        if ($result->message_id)
        {
            //insert notification in db
            $insert_data = [
                'sender_id' => 1,
                'receiver_id' => $to,
                'notification_type' => $type,
                'alert' => $title,
                'is_read' => '0'
            ];
            DB::table('dn_notifications')->insertGetId($insert_data);
 }

        curl_close($ch);
    }

}
