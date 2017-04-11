<?php
namespace App\Http\Controllers\Web;

use Auth;
use DB;
use Mail;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Crypt;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use Session;
use Validator;
use Redirect;
use Hash;
use Pingpong\Admin\Uploader\ImageUploader;

use App\User;
use Socialite;
use Services_Twilio;

class apppage extends Controller 
{

	public function driver(Request $request, $id=null) 
	{
		
		$formData 	= Input::get();
		$token 	= Input::get('_token');
		if (!empty($token)) {
		  $id   = Input::get('userId');
          $driver_profile_pic  = Input::file('profile_pic');
          $first_name   = Input::get('first_name');
          $last_name  = Input::get('last_name');
          $email  = Input::get('email');
          $dob  = Input::get('dob');
          $anniversary  = Input::get('anniversary');
          if($dob !=''){
          }else{
            $dob = '';
          }
          if($anniversary !=''){
          }else{
            $anniversary = '';
          }

          $terms  = Input::get('terms');

          $license_number = Input::get('license_number');
          //$ssn  = Input::get('ssn');

          $gender = Input::get('gender');
          $license_exp_input  = Input::get('licence_exp');
          $insurance_exp_input  = Input::get('insurance_exp');

          $license_exp = date("Y-m-d", strtotime($license_exp_input));
          $insurance_exp = date("Y-m-d", strtotime($insurance_exp_input));

            $address_1  = Input::get('address_1');
            $address_2  = Input::get('address_2');
            $city   = Input::get('city');
            $state  = Input::get('state');
            $zip_code   = Input::get('zip_code');
          //echo  $license_exp; die;
          $car_transmission = Input::get('car_transmission');
          //$navigation = Input::get('navigation');

          $license_verification = Input::file('license_verification');
          $proof_of_insurance = Input::file('proof_of_insurance');

            $question = array("Have you had more than one accident in the last three years?","Have you ever had more than two points on your driver’s license?","Have you ever had more than one moving violation in last two years?","Have you been ever arrested for a DUI/OVI?","Have you ever been convicted for a crime?","Have you been driving for less than 2 years?","Are you less than 21 years of age?","Can you drive a manual(stick) transmission?","Do you have a commercial driver`s license?","How did you hear about DeziNow?");
            $answer = array();
            for( $i=0; $i<=9; $i++ )
            {
              ${"driver_records_".$i} = Input::get("driver_records_".$i);
              $answer[] = ${"driver_records_".$i};
            }
            $final_array = [];
            for( $i=0; $i<10; $i++ ){
              $final_array[$i]['question'] = $question[$i];
              $final_array[$i]['answer'] = $answer[$i];
              
            }

            $driver_records = json_encode($final_array);
          
            $driver_detail = DB::table('dn_driver_requests')->select('*')->where('user_id', $id)->first();
            $driver_license_detail = DB::table('dn_users_data')->select('*')->where('user_id', $id)->first();

            $userDeviceCheck = DB::table('dn_users')->select(array('id', 'email', 'contact_number', 'is_social', 'profile_pic','is_driver_approved'))->where('id', $id)->first();
            $is_driver_approved = $userDeviceCheck->is_driver_approved;
              /*--code start for change date formet --*/
                /*$origionalDob = $dob;
                    $arrBob = explode('/', $origionalDob);
                    $newDob = $arrBob[2].'-'.$arrBob[1].'-'.$arrBob[0];
                    $origionalanniversary = $anniversary;
                    $arranniversary = explode('/', $origionalanniversary);
                    $newanniversary = $arranniversary[2].'-'.$arranniversary[1].'-'.$arranniversary[0];*/
                    $origionalDob = $dob;
                    $newDob = date("Y-m-d", strtotime($origionalDob));

                    $origionalanniversary = $anniversary;
                    $newanniversary = date("Y-m-d", strtotime($origionalanniversary));
                    /*--code end for change date formet --*/
                    
                    $dateString=$newDob;
                    $years = round((time()-strtotime($dateString))/(3600*24*365.25));
              if($years < '18' || $dob == ""){

                Session::flash('message', 'You are less then 18 year old');
              return Redirect::intended('/driver/'.$id);
            
            } else {

                  $destinationPath = 'uploads/profile-img/';
                  $filename = md5(microtime() . $driver_profile_pic->getClientOriginalName()) . "." . $driver_profile_pic->getClientOriginalExtension();
                  Input::file('profile_pic')->move($destinationPath, $filename);
                  $driver_profile_pic_path = $destinationPath . $filename;

                if($driver_license_detail){
                        DB::table('dn_users_data')->where(['user_id' => $id])->update(['license_number' => $license_number,'transmission' => $car_transmission,'terms_conditions' => '1','driver_profile_pic'=>$driver_profile_pic_path]);
                      }else{
                        $insertGetId = DB::table('dn_users_data')->insertGetId(['user_id' => $id,'license_number' => $license_number,'transmission' => $car_transmission,'terms_conditions' => '1','driver_profile_pic'=>$driver_profile_pic_path]);
                      }

                DB::table('dn_users')->where(['id' => $id])->update(['first_name' => $first_name,
                  'last_name' => $last_name,
                  'email'   => $email,
                  'dob'   => $newDob,
                  'anniversary' => $newanniversary,
                  'gender'  => $gender,
                  'become_driver_request' => '1',
                  'address_1' => $address_1,
                  'address_2'  => $address_2,
                  'city' => $city,
                  'state'  => $state,
                  'zip_code' =>$zip_code,
                  'driver_requested_on' => date('Y-m-d H:i:s')]);
              
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
                              
                              DB::table('dn_driver_requests')->where(['user_id' => $id])->update(['proof_of_insurance' => $proof_of_insurance_path]);
                            }

                            DB::table('dn_driver_requests')->where(['user_id' => $id])->update(['car_transmission' => $car_transmission,'licence_expiration' => $license_exp ,'insurance_expiration'=> $insurance_exp,'driver_records' =>$driver_records]);

                    } else {
                          $insertGetId = DB::table('dn_driver_requests')->insertGetId(['car_transmission' => $car_transmission,'user_id' => $id,'driver_records' =>$driver_records,'licence_expiration' => $license_exp ,'insurance_expiration'=> $insurance_exp]);
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
                    Session::flash('message', 'YOUR APPLICATION HAS BEEN SUBMITTED');
              		  return redirect('/driver/success/'.$id);
                    //return Redirect::intended('/driver/'.$id);
            }
		}else{

			$myData['id'] = "$id";
			$user_detail = DB::table('dn_users')->select('*')->where('id', $myData['id'])->first();
			$dn_users_data = DB::table('dn_users_data')->select('*')->where('user_id', $myData['id'])->first();
			$driver_detail = DB::table('dn_driver_requests')->select('*')->where('user_id', $myData['id'])->first();

        //echo "<pre>"; print_r($user_detail); die(' user_detail HERE');

			$myData['created_at'] = @$user_detail->created_at;
			$myData['email'] = @$user_detail->email;
			$myData['contact_number'] = @$user_detail->contact_number;
			$myData['dob'] = @$user_detail->dob;
			$myData['anniversary'] = @$user_detail->anniversary;
			$myData['first_name'] = @$user_detail->first_name;
			$myData['last_name'] = @$user_detail->last_name;
			$myData['profile_pic'] = @$user_detail->profile_pic;
			$myData['is_driver_approved'] = @$user_detail->is_driver_approved;
			$myData['become_driver_request'] = @$user_detail->become_driver_request;
			$myData['gender'] = @$user_detail->gender;
			$myData['address_1'] = @$user_detail->address_1;
			$myData['address_2'] = @$user_detail->address_2;
			$myData['city'] = @$user_detail->city;
			$myData['state'] = @$user_detail->state;
			$myData['zip_code'] = @$user_detail->zip_code;

	        if($dn_users_data){
	            $myData['license_number'] = $dn_users_data->license_number;
              $myData['ssn'] = $dn_users_data->ssn;
	            $myData['driver_profile_pic'] = $dn_users_data->driver_profile_pic;
	        }else{
	            $myData['license_number'] = '';
              $myData['ssn'] = '';
	            $myData['driver_profile_pic'] = '';
	        }

	        if($driver_detail){
              $myData['car_transmission'] = $driver_detail->car_transmission;

	            $driver_recordsdata = $driver_detail->driver_records;
	            $myData['driver_records'] = json_decode($driver_recordsdata);

              $myData['licence_expirationDb'] = $driver_detail->licence_expiration;
              
              $myData['insurance_expirationdb'] = $driver_detail->insurance_expiration;

	        }else{
              $myData['car_transmission'] = '';
              $myData['licence_expirationDb'] = '';
	            $myData['insurance_expirationdb'] = '';
	            $driver_recordsdata ='[{"question":"Have you had more than one accident in the last three years?","answer":"1"},{"question":"Have you ever had more than two points on your driver’s license?","answer":"1"},{"question":"Have you ever had more than one moving violation in last two years?","answer":"1"},{"question":"Have you been ever arrested for a DUI/OVI?","answer":"1"},{"question":"Have you ever been convicted for a crime?","answer":"1"},{"question":"Have you been driving for less than 2 years?","answer":"1"},{"question":"Are you less than 21 years of age?","answer":"1"},{"question":"Can you drive a manual(stick) transmission?","answer":"1"},{"question":"Do you have a commercial driver`s license?","answer":"1"},{"question":"How did you hear about DeziNow?","answer":""}]';;
	            $myData['driver_records'] = json_decode($driver_recordsdata);
	        }

	        $origionalDob = @$user_detail->dob;
            if($origionalDob !=''){
              $arrBob = explode('-', $origionalDob);
              $myData['dob'] = $arrBob[1].'/'.$arrBob[2].'/'.$arrBob[0];
            }else{
              $myData['dob'] = '';
          }
          $origionalanniversary = @$user_detail->anniversary;
            if($origionalanniversary !=''){
              $arranniversary = explode('-', $origionalanniversary);
              $myData['anniversary'] = $arranniversary[1].'/'.$arranniversary[2].'/'.$arranniversary[0];
            }else{
              $myData['anniversary'] = '';
            }
	        
		}
        //print_r($myData);die;
        return View('becomedriver_app', compact('myData'));		
	}

  public function driversuccess(Request $request, $id=null){
      $myData['id'] = "$id ";
      $user_detail = DB::table('dn_users')->select('*')->where('id', $myData['id'])->first();
      $dn_users_data = DB::table('dn_users_data')->select('*')->where('user_id', $myData['id'])->first();
      $driver_detail = DB::table('dn_driver_requests')->select('*')->where('user_id', $myData['id'])->first();

        //echo "<pre>"; print_r($user_detail); die(' user_detail HERE');

          $myData['created_at'] = @$user_detail->created_at;
          $myData['email'] = @$user_detail->email;
          $myData['contact_number'] = @$user_detail->contact_number;
          $myData['dob'] = @$user_detail->dob;
          $myData['anniversary'] = @$user_detail->anniversary;
          $myData['first_name'] = @$user_detail->first_name;
          $myData['last_name'] = @$user_detail->last_name;
          $myData['profile_pic'] = @$user_detail->profile_pic;
          $myData['is_driver_approved'] = @$user_detail->is_driver_approved;
          $myData['become_driver_request'] = @$user_detail->become_driver_request;
          $myData['gender'] = @$user_detail->gender;
          $myData['address_1'] = @$user_detail->address_1;
          $myData['address_2'] = @$user_detail->address_2;
          $myData['city'] = @$user_detail->city;
          $myData['state'] = @$user_detail->state;
          $myData['zip_code'] = @$user_detail->zip_code;

          if($dn_users_data){
              $myData['license_number'] = $dn_users_data->license_number;
              $myData['ssn'] = $dn_users_data->ssn;
              $myData['driver_profile_pic'] = $dn_users_data->driver_profile_pic;
          }else{
              $myData['license_number'] = '';
              $myData['ssn'] = '';
              $myData['driver_profile_pic'] = '';
          }

          if($driver_detail){
              $myData['car_transmission'] = $driver_detail->car_transmission;

              $driver_recordsdata = $driver_detail->driver_records;
              $myData['driver_records'] = json_decode($driver_recordsdata);

              $myData['licence_expirationDb'] = $driver_detail->licence_expiration;
              
              $myData['insurance_expirationdb'] = $driver_detail->insurance_expiration;

          }else{
              $myData['car_transmission'] = '';
              $myData['licence_expirationDb'] = '';
              $myData['insurance_expirationdb'] = '';
              $driver_recordsdata ='[{"question":"Have you had more than one accident in the last three years?","answer":"1"},{"question":"Have you ever had more than two points on your driver’s license?","answer":"1"},{"question":"Have you ever had more than one moving violation in last two years?","answer":"1"},{"question":"Have you been ever arrested for a DUI/OVI?","answer":"1"},{"question":"Have you ever been convicted for a crime?","answer":"1"},{"question":"Have you been driving for less than 2 years?","answer":"1"},{"question":"Are you less than 21 years of age?","answer":"1"},{"question":"Can you drive a manual(stick) transmission?","answer":"1"},{"question":"Do you have a commercial driver`s license?","answer":"1"},{"question":"How did you hear about DeziNow?","answer":""}]';;
              $myData['driver_records'] = json_decode($driver_recordsdata);
          }

          $origionalDob = @$user_detail->dob;
            if($origionalDob !=''){
              $arrBob = explode('-', $origionalDob);
              $myData['dob'] = $arrBob[1].'/'.$arrBob[2].'/'.$arrBob[0];
            }else{
              $myData['dob'] = '';
          }
          $origionalanniversary = @$user_detail->anniversary;
            if($origionalanniversary !=''){
              $arranniversary = explode('-', $origionalanniversary);
              $myData['anniversary'] = $arranniversary[1].'/'.$arranniversary[2].'/'.$arranniversary[0];
            }else{
              $myData['anniversary'] = '';
            }
     Session::flash('message', 'YOUR APPLICATION HAS BEEN SUBMITTED');
     return View('becomedriver_app', compact('myData'));
  }
}