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

class home extends Controller {

	
	
	public function __construct()
    {
        
        $authcheck = Session::get('userid');
       
    }
	
	public function  getPage()
    {
        
       return View('errors.404');
       
    }
    public function  driversterm()
    {
        
       return View('driversterms');
       
    }

   

	/*--function for passenger profile page start--*/
		public function passengerprofile() {
			return View('passengerprofile');
		}
	/*--//function for passenger profile page end--*/

		/*--function start for generate random number--*/
		    private function generateRandomString($length = 6)
			    {
			        $characters = '0123456789';
			        $charactersLength = strlen($characters);
			        $randomString = '';
			        for ($i = 0; $i < $length; $i++) {
			            $randomString .= $characters[rand(0, $charactersLength - 1)];
			        }
			        return $randomString;
			    }
			    private function generateRandomStrings($length = 4)
			    {
			        $characters = '0123456789';
			        $charactersLength = strlen($characters);
			        $randomString = '';
			        for ($i = 0; $i < $length; $i++) {
			            $randomString .= $characters[rand(0, $charactersLength - 1)];
			        }
			        return $randomString;
			    }
			    private function generateRandomReferral($length = 7)
				    {
				        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				        $charactersLength = strlen($characters);
				        $randomString = '';
				        for ($i = 0; $i < $length; $i++) {
				            $randomString .= $characters[rand(0, $charactersLength - 1)];
				        }
				        return $randomString;
				    }
		/*--//function end for generate random number--*/	    
		
		/*--function start for send mail--*/	
		    private function sendEmailReminder($email, $name, $token, $subject)
			    {
			        Mail::send('app.mails.confirm-email', ['name' => $name, 'token' => $token], function ($m) use ($email, $name, $token, $subject) {
			            $m->from('dezinow@example.com', 'DeziNow');
			            
			            $m->to($email, $name)->subject($subject);
			        });
			        if (count(Mail::failures()) > 0) {
			            return 0;
			        }

			        return 1;
			    }

			private function sendEmailnewRegister($email, $name, $password, $subject)
			    {
			        Mail::send('app.mails.newuser-website', ['name' => $name, 'email' => $email,'password' => $password], function ($m) use ($email, $name, $password, $subject) {
			            $m->from('dezinow@example.com', 'DeziNow');
			            
			            $m->to($email, $name)->subject($subject);
			           /* echo $email.'<br/>';
			            echo $name.'<br/>';
			            echo $subject.'<br/>';
			            echo $password.'<br/>';
			            die("here");*/

			        });
			        if (count(Mail::failures()) > 0) {
			            return 0;
			        }

			        return 1;
			    }
		/*--//function end for send mail--*/

		/*--function start for send otp--*/
			private function twileo_send($phone,$otp)
			    {
			        $id = "ACef0bc2ba66b70340468cc67fca14390d";
			        $token = "e12d7d9fac857f1b845d19e8e9bde841";


			        $url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
			        $from = "6507535036";
			        //$from = "+14438407757";   //live
			        //$phone ="+919991281944";
			        $to = $phone; 
			        $body = "Verification code for DeziNow: $otp";
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
			        /*echo '<pre>';
			        print_r($y);
			        echo '</pre>';
			        die;*/
			        curl_close($x);
			        //sms log
			    }
		/*--//function end for send otp--*/


		/*--function start for login--*/
			public function login() {
			    // Getting all post data
			    $data = Input::all();
			    $checkuser = Input::get('check');
			    //echo $checkuser = Session::get('checkuser');
			    //$checkuser = '<script>document.write(localStorage.getItem("checkuser"));</script>';
			    //dd($checkuser);
			    // Applying validation rules.
			    $rules = array(
					'email' => 'required|email',
					'password' => 'required|min:6',
				     );
			    $validator = Validator::make($data, $rules);
			    /*print_r($validator);
			      die('here');*/
			    if ($validator->fails()){
			      // If validation falis redirect back to login.
			      return Redirect::to('/login')->withInput(Input::except('password'))->withErrors($validator);
			    }
			    else {
			    	//$password = Hash::make(input::get('password'));
			    	//$password = bcrypt(input::get('password'));
			        $userdata = array(
					    'email' => Input::get('email'),
					    'password' => input::get('password')
					  );
			      //if (Hash::check('flyingbeta', $password))
			      /*if (Hash::check(input::get('password'), $password))
						{
						    // The passwords match...
						    echo 'yes';
						    print_r($userdata);
			        	    echo $id = Auth::id();
						}else{
							echo 'no';
						}*/
			     /*print_r($userdata);*/
			      //die("userdata");
			      // doing login.
			      if (Auth::validate($userdata)) {
			        if (Auth::attempt($userdata)) {
			        	  
			        	  	$id = Auth::id();
			        	  	//die("here");
			      			$roleID = DB::table('role_user')->where('user_id', $id)->where('role_id','!=','5')->pluck('role_id');
			      			
			      			$roleName = DB::table('roles')->where('id', $roleID)->pluck('name');
			      			$userStatus = DB::table('dn_users')->where('id', $id)->pluck('active');
			      			$is_driver_approved = DB::table('dn_users')->where('id', $id)->pluck('is_driver_approved');
			      			
			      			if(($roleName == 'Passenger' || $roleName == 'Driver') && $userStatus == '1'){
			      				Session::put('userid', $id);
			      				
			      				//$is_driver_approved = DB::table('dn_users')->where('id', $id)->pluck('is_driver_approved');
			      				$change_login_status =  DB::table('dn_users')->where('id', $id)->update(['is_logged' => 'true']);
			      				//dd($checkuser);
			      				if($checkuser == '1'){
			      					/*echo $checkuser;
			      					die("if");*/
			      					if($is_driver_approved == '1'){
			      						return Redirect::intended('/editdriver');
			      					}else{
			      						return Redirect::intended('/becomedriver');
			      						 //Session::forget('becomedriver');
			      					}
			      				}else{
			      					/*echo $checkuser;
			      					die("else");*/
			      					return Redirect::intended('/editpassenger');
			      				}
			      			}else if($userStatus == '0'){
			      				Auth::logout();
			      				Session::flush();
			      				//Session::put('email', Input::get('email'));
			      				Session::flash('verify', 'You Account Is Not Active');
			      				return Redirect::intended('/login');
			      			}else{
			      				Auth::logout();
			      				Session::flush();
			      				Session::flash('message', 'LOGIN AND PASSWORD DO NOT MATCH. PLEASE TRY AGAIN.');
			      				return Redirect::intended('/login');
			      			}
			        }
			      } 
			      else {
			        // if any error send back with message.
			        Session::flash('error', 'LOGIN AND PASSWORD DO NOT MATCH. PLEASE TRY AGAIN.');
			        return Redirect::to('login');
			      }
			    }
			}
		/*--//function end for login--*/

		  	/*--function start for logout--*/
			  	public function logout() {
				$id = Auth::id();
				 $change_login_status =  DB::table('dn_users')->where('id', $id)->update(['is_logged' => 'false']);	
				  Auth::logout(); // logout user
				  Session::flush();
				  return  Redirect::to('passenger/profile'); //redirect back to login
				}
			/*--//function end for logout--*/

			/*--function start for forgot password request--*/
			public function forgot_password(Request $request) {
				$forgotpassword = $request->input('forgotpassword');
				if($forgotpassword == '0'){

					$email = $request->input('email');
			        $user_email_check = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'email', 'is_social')->where('email', $email)->first();

					if (!empty($user_email_check)) {
		            	if($user_email_check->is_social == '0') {
		            		$remember_token = $this->generateRandomString(5);
		            		$is_sent = $this->sendEmailReminder($user_email_check->email, $user_email_check->first_name, $remember_token, 'Forgot password DeziNow');
		            		if ($is_sent) {
			                    DB::table('dn_users')->where(['id' => $user_email_check->id])->update(['password_token' => $remember_token,'password_token_expired'=>0]);
			                    Session::flash('message', 'Email sent successfully');
			                    return Redirect::intended('/forgot');
			                } else {
			                	Session::flash('message', 'There is technical error mail doesn\t send');
			                	return Redirect::intended('/forgot');
			                }
			            }else{
			            	Session::flash('message', 'EMAIL ADDRESS DOES NOT EXIST.');
			            	return Redirect::intended('/forgot');
			            }
			        }else{
			        	Session::flash('message', 'EMAIL ADDRESS DOES NOT EXIST.');
			            return Redirect::intended('/forgot');
			        }
				}else{

					$password_token = $request->input('password_token');

					if(!empty($password_token)){
						$user_passtoken_check = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'email', 'is_social', 'password_token','password_token_expired')->where('password_token', $password_token)->first();

						if (!empty($user_passtoken_check)) {
							if($user_passtoken_check->password_token == $password_token) {
								if($user_passtoken_check->password_token_expired == 1){
									Session::flash('error', 'Verification Code is expired Please Try Again');
								    return Redirect::intended('/forgot');
								}
								Session::flash('message', 'Verification Success');
								Session::put('password_token', $user_passtoken_check->password_token);
								DB::table('dn_users')->where('password_token', $password_token)->update(['password_token_expired' => 1]);
								return Redirect::intended('/resetpassword');
							}else{
								Session::flash('error', 'Verification Code Not Match Please Try Again');
								return Redirect::intended('/forgot');
							}
						}else{
							Session::flash('error', 'Verification Code Not Match Please Try Again');
							return Redirect::intended('/forgot');
						}
					}else{
						Session::flash('error', 'Enter Verification Code');
						return Redirect::intended('/forgot');
					}

					//print_r($user_passtoken_check);
					//die("here");
				}
			}
			/*--//function end for forgot password request--*/


			/*--function start for reset password--*/
			public function resetpassword(Request $request) {
				$password_tokn = $request->input('password_tokn');
				//$email = $request->input('email');
				$password = $request->input('password');
				$password_confirmation = $request->input('password_confirmation');

				$resetPassword =  Hash::make($password);
				if($password_tokn !=''){
					if($password !='' || $password_confirmation !=''){
						if($password == $password_confirmation){
							$user_detail_check = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'email', 'is_social', 'password_token')->where('password_token', $password_tokn)->first();
								$userid = $user_detail_check->id;
								$useremail = $user_detail_check->email;
								if($user_detail_check){
									Session::forget('password_token');
									DB::table('dn_users')->where(['id' => $userid])->update(['password' => $resetPassword]);
									Session::flash('resetpassword', 'Password reset successfully');
									return Redirect::intended('/login');
								}else{
									Session::flash('message', 'User not exists');
									return Redirect::intended('/resetpassword');
								}
						}else{
							Session::flash('message', 'Password Not Match Please Try Again');
							return Redirect::intended('/resetpassword');
						}
					}else{
						Session::flash('message', 'Please Enter All The Field');
						return Redirect::intended('/resetpassword');
					}
				}else{
					Session::flash('verify', 'Please Verify Your Email Address');
					return Redirect::intended('/forgot');
				}
			}
			/*--//function end for reset password--*/

			/*--function start here for social login signup--*/
			    protected $redirectPath = '/profile';
				    /**
				     * Redirect the user to the Facebook authentication page.
				     *
				     * @return Response
				     */
				    public function redirectToProvider()
				    {
				        return Socialite::driver('facebook')->redirect();
				    }
			    /**
			     * Obtain the user information from Facebook.
			     *
			     * @return Response
			     */
			    public function handleProviderCallback()
			    {
			        try {
			            $user = Socialite::driver('facebook')->user();
			        } catch (Exception $e) {
			            return redirect('login/facebook');
			        }
			        //$checkuser = '<script>document.write(localStorage.getItem("checkuser"));</script>';
			        $authUser = $this->findorCreateUser($user);
			        if($authUser){
			        	Auth::login($authUser, true);
			        	return redirect()->route('profile');
			        	/*if($checkuser =='1'){
			        		return redirect()->route('becomedriver');
			        	}else{
			        		return redirect()->route('profile');
			        	}*/
			        }
			        return Redirect::intended('/phoneverification');

			    }
			    /**
			     * Return user if exists; create and return if doesn't
			     *
			     * @param $facebookUser
			     * @return User
			     */
			    private function findorCreateUser($facebookUser)
			    {
			        $authUser = User::where('email', $facebookUser->email)->first();
			        if ($authUser){
			            return $authUser;
			            DB::table('dn_users')->where(['id' => $authUser->id])->update(['profile_pic' => $facebookUser->avatar]);
			        }
			        Session::put('email', $facebookUser->email);
			        //$passenger_referral_code = 'PS'.$this->generateRandomReferral(7);
			       //Session::put('passenger_referral_code', $passenger_referral_code); 						
			    }

		    /*--//function end here for social login signup--*/




			/*--function for signup page start--*/
				public function signup(Request $request) {
					Session::forget('phone_code');
					Session::forget('phone_num');
					$email = $request->input('email');
					$password = $request->input('password');
					$password_confirmation = $request->input('password_confirmation');
					$latitude = $request->input('latitude');
        			$longitude = $request->input('longitude');
        			$referral_code = $request->input('referral_code');

					$checkEmail = DB::table('dn_users')->select('email')->where('email', $email)->first();

					if (!empty($referral_code)) {
						$code_type = substr($referral_code, 0, 2);
						if($code_type == 'PS'){
							$referral_type ='3';
							Session::put('referral_type', $referral_type);
							$referralCodeCheck =  DB::table('dn_users')->where('passenger_referral_code', $referral_code)->pluck('id');
						}else if($code_type == 'DR'){
							Session::flash('message', 'Please enter passenger referral code only.');
							return Redirect::intended('/signup');
							//$referral_type ='4';
							//Session::put('referral_type', $referral_type);
							//$referralCodeCheck =  DB::table('dn_users_data')->where('referral_code', $referral_code)->pluck('user_id');
						}
						if (empty($referralCodeCheck)) {
							
							Session::flash('message', 'Referral code does not exists.');
							return Redirect::intended('/signup');
						} else {
							$referred_by = $referralCodeCheck;
							Session::put('referred_by', $referred_by);
						}
					}
					if($email !='' && $password !='' && $password_confirmation !=''){
						if($password == $password_confirmation){
							if(!$checkEmail){
								Session::put('email', $email);
								Session::put('password', $password);
								Session::put('latitude', $latitude);
								Session::put('longitude', $longitude);
								Session::put('referral_code', $referral_code);
								        Session::flash('registerConfirm', 'Please Verify Your Number For Complete Registration');
										return Redirect::intended('/phoneverification');
								}else{
									Session::flash('message', 'You are already registered');
									return Redirect::intended('/signup');
								}
						}else{
							Session::flash('message', 'Password Not Match');
							return Redirect::intended('/signup');
						}
					}else{
						Session::flash('message', 'Please Fill All The Fields');
						return Redirect::intended('/signup');
					}
				}
			/*--//function for signup page end--*/

			/*--function start for update phone numbere--*/
			public function editPhone(Request $request) {
				if ($request->ajax()) {
					$phone_number = $request->get('phone_number');
					$phonecode = $request->get('phonecode');
					$UerID = $request->get('UerID');

				$user_phone_check = DB::table('dn_users')->select('contact_number')->where('contact_number', $phone_number)->get();
				$checkotp = DB::table('dn_user_verification')->select('contact_number')->where(['contact_number'=>$phone_number])->get();
				$checkotpAlready = DB::table('dn_user_verification')->select('contact_number','verified')->where(['contact_number'=>$phone_number,'verified'=>'1'])->first();
				$otp = $this->generateRandomString(4);

				$numberWithCode = $phonecode.$phone_number;
			        $insert_data = array(
		                'country_phone_code' => $phonecode,
		                'contact_number' => $phone_number,
		                'otp' => $otp
		            );

				    if($checkotpAlready){
				        	Session::put('phonecode', $phonecode);
							Session::put('phone_number', $phone_number);

			           		$alreadythere = '11';
			           		if($alreadythere){
					        	Session::flash('verificationMessage', 'Already Exist');
								return $alreadythere;
					        }
				    }else{
				        if(!$checkotp){
				        	Session::put('phonecode', $phonecode);
							Session::put('phone_number', $phone_number);

			           		$insertGetId = DB::table('dn_user_verification')->insertGetId($insert_data);
			           		$this->twileo_send($numberWithCode, $otp);
			           		if($insertGetId){
					        	Session::flash('verificationMessage', 'Verification code sent');
								return $insertGetId;
					        }
				        }else{
				        	Session::put('phonecode', $phonecode);
							Session::put('phone_number', $phone_number);

			           		$insertGetId = DB::table('dn_user_verification')->where(['contact_number' => $phone_number])->update(['otp' => $otp]);
			           		$this->twileo_send($numberWithCode, $otp);
			           		if($insertGetId){
					        	Session::flash('verificationMessage', 'Verification code sent');
								return $insertGetId;
					        }
				        }
				    }
				}
			}
			public function confirmOTP(Request $request) {
				if ($request->ajax()) {
					$id = Auth::id();
					$phone_number = Session::get('phone_number');
					$phonecode = Session::get('phonecode');
					$otp = $request->get('otp');
					$get_otp = DB::table('dn_user_verification')->select('otp','verified')->where('contact_number', $phone_number)->first();
					if($otp == $get_otp->otp){
						$verifiedOtp = DB::table('dn_user_verification')->where(['contact_number' => $phone_number])->update(['verified' => '1']);
						if($verifiedOtp){
							$updateOtp = DB::table('dn_users')->where(['id' => $id])->update(['contact_number' => $phone_number,'country_phone_code' => $phonecode]);
							if($updateOtp){
					        	Session::flash('confirmUpdateContact', 'Your Contact Number Updated Successfully');
								return $updateOtp;
					        }
						}
					}else{
						$otpWrong = '11';
						if($otpWrong){
				        	Session::flash('confirmUpdateContact', 'OTP Not Match Please Try Again');
							return $otpWrong;
				        }
					}
				}
			}
			/*--//function end for update phone numbere--*/

			/*--function for phone verification page start--*/
				public function phoneverification(Request $request) {
					$latitude = Session::get('latitude');
					$longitude = Session::get('longitude');
					$password = Session::get('password');

					if($password ==''){
						$password = $this->generateRandomString(6);
					}
					if($latitude ==''){
						$latitude = $request->input('latitude');
					}
					if($longitude ==''){
						$longitude = $request->input('longitude');
					}

					$email = Session::get('email');
					$phoneverification = $request->input('phoneverification');
					$phone_code = $request->input('phonecode');
					$phone_num = $request->input('phone_number');
					$phone_number = $phone_code.$phone_num;
					/*echo $phone_number;
					die("here");*/
					$otp = $this->generateRandomString(4);
					
					$user_email_check = DB::table('dn_users')->select('email')->where('email', $email)->get();
					$user_phone_check = DB::table('dn_users')->select('contact_number')->where('contact_number', $phone_num)->get();
					$user_phone_email_check = DB::table('dn_users')->select('email','contact_number')->where('email', $email)->first();
					$checkotp = DB::table('dn_user_verification')->select('contact_number')->where('contact_number', $phone_num)->get();

					if($phoneverification == '1'){

						/*--code for get otp start--*/
						if($phone_num != ''){
									if ($otp) {
										if(!$user_phone_check){
								            $insert_data = array(
								                'country_phone_code' => $phone_code,
								                'contact_number' => $phone_num,
								                'otp' => $otp
								            );
								           	if(!$checkotp){
								           		Session::put('phone_code', $phone_code);
												Session::put('phone_num', $phone_num);

								           		$insertGetId = DB::table('dn_user_verification')->insertGetId($insert_data);
								           		$this->twileo_send($phone_number, $otp);
								           		Session::put('contact_number', $phone_num);
								           		Session::flash('message', 'Verification code sent');
												return Redirect::intended('/phoneverification');
								           	}else{
								           		Session::put('phone_code', $phone_code);
												Session::put('phone_num', $phone_num);

								           		DB::table('dn_user_verification')->where(['contact_number' => $phone_num])->update(['otp' => $otp]);
								           		$this->twileo_send($phone_number, $otp);
								           		Session::put('contact_number', $phone_num);
								           		Session::flash('message', 'Verification code sent');
												return Redirect::intended('/phoneverification');
								           	}
								        }else{
											Session::forget('phone_code');
											Session::forget('phone_num');
								        	Session::flash('message', 'Phone no is already in use.');
											return Redirect::intended('/phoneverification');
								        }


									}else {
							            	Session::flash('message', 'Please Tyr Again');
											return Redirect::intended('/phoneverification');
							               // return response()->json(['status' => 0, 'message' => 'Error: otp not sent'], 200);
							            }
						        }else{
						        	Session::flash('message', 'Please Enter A Phone Number');
									return Redirect::intended('/phoneverification');
						        }  
						/*--//code for get otp end--*/
					}else{
						/*--function for confirm otp start--*/
							$phone_code = Session::get('phone_code');
							$contact_number = Session::get('contact_number');
							$email = Session::get('email');

							//$passenger_referral_code = 'PS'.$this->generateRandomReferral(7);
							//$passenger_referral_code = Session::get('passenger_referral_code');

							$opt = $request->input('opt');

							$get_otp = DB::table('dn_user_verification')->select('otp','verified')->where('contact_number', $contact_number)->first();
								if($opt !=''){
									if($opt == $get_otp->otp){
										$verifiedOtp = DB::table('dn_user_verification')->where(['contact_number' => $contact_number])->update(['verified' => '1']);
										if($verifiedOtp){
											$role = 'passenger';
	        								$role_id = DB::table('roles')->where('slug', $role)->pluck('id');
			        						$insert_data = array(
								                'email' => $email,
								                'active' => '1',
								                'password' => Hash::make($password),
								                'social_id' => "",
								                'updated_at' => date('Y-m-d H:i:s'),
								                //'passenger_referral_code' => $passenger_referral_code,
								                'contact_number' => $contact_number,
								                'country_phone_code' => $phone_code
								            );

			        						//get city and state of user
									        if ($latitude && $longitude) {
									            $address = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=false");
									            $address = json_decode($address);
									            if (!empty($address)) {
									                $formatted_address = $address->results[0]->formatted_address;

									                $formatted_address_arr = explode(",", $formatted_address);

									                $count = count($formatted_address_arr);
									                $country = $formatted_address_arr[$count - 1];
									                $state = $formatted_address_arr[$count - 2];
									                $num = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

									                $state = str_replace($num, null, $state);
									                $city = $formatted_address_arr[$count - 3];

									                $city = trim($city);
									                $state = trim($state);

									                $city_state = DB::table('dn_cities')
									                    ->leftJoin('dn_states', 'dn_cities.state_code', '=', 'dn_states.state_code')
									                    ->where(['dn_cities.city' => $city, 'dn_cities.state_code' => $state])
									                    ->first();

									                $insert_data['city'] = !empty($city_state->id) ? $city_state->id : "";
									                $insert_data['state'] = !empty($city_state->state_code) ? $city_state->state_code : "";
									            } else {
									                $insert_data['city'] = "";
									                $insert_data['state'] = "";
									            }
									        }
											$insertGetId = DB::table('dn_users')->insertGetId($insert_data);
											if ($insertGetId) {
									            unset($insert_data['password']);
									            //insert data role_user table
									            DB::table('role_user')->insertGetId(['role_id' => $role_id, 'user_id' => $insertGetId]);
									            //emp code update
									            $emp_code = 200000 + $insertGetId;
									            DB::table('dn_users')
									                ->where(['id' => $insertGetId])
									                ->update(['unique_code' => $emp_code, 'is_logged' => 'true']);
									            //logged user log
									            DB::table('dn_user_logs')->insertGetId(['user_id' => $insertGetId, 'login_time' => date('Y-m-d H:i:s'), 'user_mode' => 'passenger']);
									            $insert_data['role'] = $role_id;
									            	$is_sent = $this->sendEmailnewRegister($email, 'Passanger', $password, 'Your password DeziNow');  
										            //$checkuser = '<script>document.write(localStorage.getItem("checkuser"));</script>';
										            $userdata = array(
													    'email' => $email,
													    'password' => $password
													  );
										            
										            	$referral_code=Session::get('referral_code');
										            	$referred_by=Session::get('referred_by');
										            	$referral_type=Session::get('referral_type');
														if ($referral_code) {
															DB::table('dn_user_referrals')->insertGetId(
																[
																'user_id' => $insertGetId,
																'referred_by' => $referred_by,
																'referral_code' => $referral_code,
																'referral_type' => $referral_type
																]
															);
														}
													

												    if (Auth::attempt($userdata)) {
												    	
											//$becomedriver= Session::get('becomedriver'); echo $becomedriver;exit;
												            Session::flash('conOpt', 'You Profile Is Active ! Password send on your registered email address');
													 	return redirect('/editpassenger');
													 	
												    }
									        }
										}else{
											Session::flash('message', 'Please Enter A Valid One Time Password');
											return Redirect::intended('/phoneverification');
										}
									}else{
										Session::flash('message', 'Please Enter A Valid One Time Password');
										return Redirect::intended('/phoneverification');
									}	
								}else{
									Session::flash('message', 'Please Enter One Time Password');
									return Redirect::intended('/phoneverification');
								}
						/*--//function for confirm otp end--*/
					}
				}
			/*--//function for phone verification page end--*/

			/*--function start for edit passenger profile--*/
				public function editpassenger(Request $request){
					$id 	= Auth::id();
					$profile_pic = Input::file('profile_pic');
					$first_name = Input::get('first_name');
					//print_r(Input::get());die; 
					$last_name = Input::get('last_name');
					$dob = Input::get('dob');
					$becomeD = Input::get('becomeD');
					$state=Input::get('payoutState');
					$city=Input::get('payoutCity');
					$email=Input::get('email');
					$emailCheck = DB::table('dn_users')->select('*')
					->where('email', $email)
					->Where('id', '!=' ,$id)->first();
					if(!empty($emailCheck)){
						 Session::flash('error', 'Error!! An account with this email already exists.');
						 return Redirect::back()->withInput(Input::all());
					}
					$full_name = Input::get('first_name')." ".Input::get('last_name');


					
					//passenger_referral_code
					if($dob !=''){
						$dob = Input::get('dob');
						//echo $dob;die;
						$origionalDob = explode('/', $dob);
					    $newDob = $origionalDob[2].'-'.$origionalDob[0].'-'.$origionalDob[1];
					}else{
						$newDob = NULL;
					}
					//print_r(Input::get());die;

					$anniversary = Input::get('anniversary');

					if($anniversary !=''){
						$anniversary = Input::get('anniversary');
						/*$origionalanniversary = $anniversary;
						$dateani = str_replace('/', '-', $origionalanniversary);*/
						$arranniversary = explode('/', $anniversary);
					    $newanniversary = $arranniversary[2].'-'.$arranniversary[0].'-'.$arranniversary[1];
					}else{
						$newanniversary = NULL;
					}

					//echo $anniversary.' '.$newanniversary; die(' IN');
					//$id = Auth::id();
					$userDeviceCheck = DB::table('dn_users')->select(array('id', 'email', 'contact_number', 'is_social', 'profile_pic'))->where('id', $id)->first();
					$contactNumber = $userDeviceCheck->contact_number;
					$Digit= $this->generateRandomStrings(4);
					$passenger_referral_code = 'PS'.$first_name.$Digit;
					//if($first_name !='' && $last_name !='' && $email !='' && $dob !='' && $anniversary !=''){	
						
						/*--code start for change date formet --*/
							/*$origionalDob = $dob;
					        $arrBob = explode('/', $origionalDob);
					        $newDob = $arrBob[2].'-'.$arrBob[1].'-'.$arrBob[0];*/

					        /*$origionalanniversary = $anniversary;
					        $arranniversary = explode('/', $origionalanniversary);
					        $newanniversary = $arranniversary[2].'-'.$arranniversary[1].'-'.$arranniversary[0];*/
				        /*--code end for change date formet --*/
				        if ($profile_pic) {
			                $destinationPath = 'uploads/profile-img/';
			                $filename = md5(microtime() . $profile_pic->getClientOriginalName()) . "." . $profile_pic->getClientOriginalExtension();
			                Input::file('profile_pic')->move($destinationPath, $filename);
			                $profile_pic_path = $destinationPath . $filename;
			                DB::table('dn_users')->where(['id' => $id])->update(['profile_pic' => $profile_pic_path]);
			            }
						//echo $newDob;die;
			            DB::table('dn_users')->where(['id' => $id])->update(['full_name' => $full_name,'first_name' => $first_name,'last_name' => $last_name,'email'=>$email,'dob' => $newDob,'anniversary' => $newanniversary,'profile_status' => '1','passenger_referral_code' => $passenger_referral_code,'state'=>$state,'city'=>$city]);
						//die("here");
						
								/*
						      $allUsersRecords = DB::table('dn_users')->select(array('id', 'first_name', 'last_name'))->get();		
						      if(!empty($allUsersRecords)){
								foreach($allUsersRecords as $Data_val){
									$full_name = $Data_val->first_name." ".$Data_val->last_name;
									$updated_id = $Data_val->id;
									DB::table('dn_users')->where(['id' => $updated_id])->update(['full_name' => $full_name]);	
								}
							  }*/
				            //echo "<pre>"; print_r($allUsersRecords); die;
							if($becomeD=='becomedriver'){
								return Redirect::intended('/becomedriver');
								Session::forget('becomedriver');
							}else{
								Session::flash('message', 'Profile Edit Successfully !!');
								return Redirect::intended('/editpassenger');
							}
							
						/*}else{
						
						Session::flash('message', 'Please Fill All The Fields');
						return Redirect::intended('editpassenger');
					}*/
				}
			/*--//function end for edit passenger profile--*/
		public function getcity(request $request)
		{
			$data= $request->all();
			$stateCode=$data["stateCode"];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}exit;
			
		}


			/*--function start for become a deriver profile edit--*/
				public function becomedriver(Request $request) 
				{
					$id 	= Auth::id();
					$driver_profile_pic = Input::file('profile_pic');
					$first_name 	= Input::get('first_name');
					$last_name 	= Input::get('last_name');
					$email 	= Input::get('email');
					$dob 	= Input::get('dob');
					
					$anniversary 	= Input::get('anniversary');
					
					if($anniversary !=''){
					}else{
						$anniversary = '';
					}
					if($dob !=''){
					}else{
						$dob = '';
					}

					$terms 	= Input::get('terms');

					$license_number = Input::get('license_number');
					$ssn	= Input::get('SSN');
					
					//$ssn = \Illuminate\Support\Facades\Crypt::encrypt($encrypted);
					$gender = Input::get('gender');
                    $license_exp_input 	= Input::get('licence_exp');
					$insurance_exp_input 	= Input::get('insurance_exp');
					$license_exp = date("Y-m-d", strtotime($license_exp_input));
					$insurance_exp = date("Y-m-d", strtotime($insurance_exp_input));

				    $address_1 	= Input::get('address_1');
					$address_2 	= Input::get('address_2');
					$city 	= Input::get('city');
					$state 	= Input::get('state');
					$zip_code 	= Input::get('zip_code');
					//echo  $license_exp; die;
					$car_transmission = Input::get('car_transmission');
					$referral_code = Input::get('referral_code');

					if (!empty($referral_code)) {
						$code_type = substr($referral_code, 0, 2);
						if($code_type == 'PS'){
							Session::flash('message', 'Please enter driver referral code only.');
							return Redirect::intended('/becomedriver');
						}else if($code_type == 'DR'){

							$referral_type ='4';
							Session::put('referral_type', $referral_type);
							$referralCodeCheck =  DB::table('dn_users_data')->where('referral_code', $referral_code)->pluck('user_id');
						}
						if (empty($referralCodeCheck)) {
							
							Session::flash('message', 'Referral code does not exist.');
							return Redirect::intended('/becomedriver');
						} else {
							

							DB::table('dn_user_referrals')
							->insertGetId(
								[
								'user_id' => $id,
								'referred_by' => $referralCodeCheck,
								'referral_code' => $referral_code,
								'referral_type' => $referral_type
								]
							);
						}
					}

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
							//echo $years;exit;
						if($years <= 18 ){

							Session::flash('message', 'You are less then 18 year old');
							return Redirect::intended('/becomedriver');
							
						} else {
							/*--upload driver profile image--*/
							$destinationPath = 'uploads/profile-img/';
						    $filename = md5(microtime() . $driver_profile_pic->getClientOriginalName()) . "." . $driver_profile_pic->getClientOriginalExtension();
						    Input::file('profile_pic')->move($destinationPath, $filename);
						    $driver_profile_pic_path = $destinationPath . $filename;
						    /*--//upload driver profile image--*/
							//echo $ssn; exit;
						    if($driver_license_detail){
			                	DB::table('dn_users_data')->where(['user_id' => $id])->update(['license_number' => $license_number,'transmission' => $car_transmission,'terms_conditions' => '1','driver_profile_pic' => $driver_profile_pic_path,'ssn' => $ssn]);
			                }else{
			                	$insertGetId = DB::table('dn_users_data')->insertGetId(['user_id' => $id,'license_number' => $license_number,'transmission' => $car_transmission,'terms_conditions' => '1','driver_profile_pic' => $driver_profile_pic_path,'ssn' => $ssn]);
			                }

						    DB::table('dn_users')->where(['id' => $id])->update(['first_name' => $first_name,
						    	'last_name' => $last_name,
						    	'email' 	=> $email,
						    	//'dob' 	=> $newDob,
						    	//'anniversary' => $newanniversary,
						    	'gender' 	=> $gender,
						    	'become_driver_request'	=> '1',
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
			        					
			        					DB::table('dn_driver_requests')
			        					->where(['user_id' => $id])
			        					->update(['proof_of_insurance' => $proof_of_insurance_path]);
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
							return Redirect::intended('/becomedriver');
						}
				}
			/*--//function end for become a deriver profile edit--*/

			/*--public function start for edit driverprofile--*/ 
 public function editdriver(Request $request){
					$id 	= Auth::id();
					$driver_profile_pic = Input::file('profile_pic');
					$anniversary 	= Input::get('anniversary');
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
				        	/*--code end for change date formet --*/
						    if($driver_license_detail){
								if(!empty($driver_profile_pic)){
										/*--upload driver profile image--*/
										$destinationPath = 'uploads/profile-img/';
										$filename = md5(microtime() . $driver_profile_pic->getClientOriginalName()) . "." . $driver_profile_pic->getClientOriginalExtension();
										Input::file('profile_pic')->move($destinationPath, $filename);
										$driver_profile_pic_path = $destinationPath . $filename;
										DB::table('dn_users_data')->where(['user_id' => $id])->update(['driver_profile_pic'=>$driver_profile_pic_path]);
										/*--//upload driver profile image--*/
								}
								
			                	DB::table('dn_users_data')->where(['user_id' => $id])->update(['license_number' => $license_number,'transmission' => $car_transmission]);
			                }else{
			                	$insertGetId = DB::table('dn_users_data')->insertGetId(['user_id' => $id,'license_number' => $license_number,'transmission' => $car_transmission]);
			                }

						    DB::table('dn_users')->where(['id' => $id])->update(['full_name' => $full_name,'anniversary' => $newanniversary,'address_1' => $address_1,'address_2'  => $address_2,'city' => $city,'state'  => $state,'zip_code' =>$zip_code,'email'=>$email,'become_driver_request'=>2,'is_suspended'=>3,'is_driver_approved'=>0]); 

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
							return Redirect::intended('/editdriver');
				}
			/*--//public function end for edit driverprofile--*/ 
    
		    /*--function for contact page start--*/
				public function contact(Request $request) {
					if(Auth::check()){
						$id = Auth::id();
						$message_type = '0';
					}else{
						$id = '0';
						$message_type = '1';
					}
					$first_name=Input::get('first_name');
					$last_name=Input::get('last_name');
					$email = Input::get('email');
					$message = Input::get('message');

					//$userCheck = DB::table('dn_users')->select(array('id', 'email'))->where('id', $id)->first();

					if($email !='' || $message !='' || $first_name !=''  || $last_name !='' ){
						$insert_data = array(
			                'message' => $message,
			                'user_id' => $id,
			                'message_type' => $message_type,
			                'email' => $email,
							 'first_name' => $first_name,
							 'last_name' => $last_name,
			                'created_at' => date('Y-m-d H:i:s')
			            );
			           	$insertGetId = DB::table('dn_contact_messages')->insertGetId($insert_data);
			           	Session::flash('message', 'Your Message Is Submitted Successfully !!');
				        return Redirect::intended('/contact');
					}else{
						Session::flash('message', 'Please Fill All The Fields !!');
				        return Redirect::intended('/contact');
					}
				}
			/*--//function for contact page end--*/


			/*--function start for send referral code--*/
			/*--function start for send otp--*/
			private function twileo_send_referral($phone,$otp)
			    {
			        $id = "ACef0bc2ba66b70340468cc67fca14390d";
			        $token = "e12d7d9fac857f1b845d19e8e9bde841";
			        $url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
			        $from = "6507535036";
			        //$from = "+14438407757";   //live
			        //$phone ="+919991281944";
			        $to = $phone; 
			        $body = "$otp";
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
			        /*echo '<pre>';
			        print_r($y);
			        echo '</pre>';
			        die;*/
			        curl_close($x);
			        return 1;
			        //sms log
			    }
		/*--//function end for send otp--*/

			public function referralcode( Request $request ) {
				if ($request->ajax()) {
					$phonecode = $request->get('phoneCode');
					$phonenumber = $request->get('phoneNumber');

					$contactNumber = $phonecode.$phonenumber;

					$referralCode = $request->get('referralCode');

					$this->twileo_send_referral($contactNumber, $referralCode);
					
					Session::flash('sendreferral', 'Your Referral Code send successfully');
					return 1;

				}
			}
			/*--//function end for send referral code--*/


}
