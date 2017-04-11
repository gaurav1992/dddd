<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Crypt;
use Hash;
use  DateTime;
use  DateTimeZone;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use Oureastudios\Laravel\BraintreeServiceProvider;
use Services_Twilio;
use Services_Twilio_Capability;
use Services_Twilio_Twiml;
include(app_path().'/customLib/firebase/firebase.php');

class mobile extends Controller
{
    public function   __construct()
    {
        set_error_handler(null);
        set_exception_handler(null);

    }

    //empty check function
    public function check_empty($data, $message = '', $numeric = false)
    {
        $message = (!empty($message)) ? $message : 'Invalid data';
        if (empty($data)) {
            $data = array('status' => 0, 'message' => $message);
            return $data;
        }
        if ($numeric) {
            if (!is_numeric($data)) {
                $data = array('status' => 0, 'message' => 'Invalid data');
                return $data;

            }
        }
    }

    //integer empty check  function
    public function check_integer_empty($data, $message = '')
    {
        $message = (!empty($message)) ? $message : 'Invalid data';

        if (is_numeric($data)) {

        } else {
            $data = array('status' => 0, 'message' => $message);
            return $data;

        }
    }
    private function  driver_data($user_id){

        $driver_data = DB::table('dn_users_data')->where('user_id', $user_id)->first();

        if ($driver_data) {
            return 1;

        }else{
            return 0;
        }

    }

    public function user_check(Request $request)
    {
        //token verification
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $email = $request->input('email');
        if ($data = $this->check_empty($email, 'Please provide email'))
            return response()->json($data, 200);

        $device_token = $request->input('device_token');
        if ($data = $this->check_empty($device_token, 'Please provide device_token'))
            return response()->json($data, 200);

        $referral_code = $request->input('referral_code');

        //check already registered device
        /* $userDeviceCheck = DB::table('dn_users')->select('id')->where(['device_token' => $device_token])->first();
         if ($userDeviceCheck)
             return response()->json(['status' => 0, 'message' => 'You are already signed up on this device'], 200);
         */
        //check already registered Email
        $userEmailCheck = DB::table('dn_users')->select('id')->where('email', $email)->first();
        if ($userEmailCheck)
            return response()->json(['status' => 0, 'message' => 'User exist with this email'], 200);

        //referral_code module
        if (!empty($referral_code)) {
            $code_type = substr($referral_code, 0, 2);
            if($code_type == 'PS'){
                $referralCodeCheck =  DB::table('dn_users')->where('passenger_referral_code', $referral_code)->pluck('id');
            }else if($code_type == 'DR'){

                //$referralCodeCheck =  DB::table('dn_users_data')->where('referral_code', $referral_code)->pluck('user_id');
                return response()->json(['status' => 0, 'message' => 'You can not enter the driver referral code.'], 200);
            }
            if (empty($referralCodeCheck)) {
                return response()->json(['status' => 0, 'message' => 'Referral code doesn\'t exist'], 200);
            }
        }

        return response()->json(['status' => 1, 'message' => 'success'], 200);

    }
    /**
     ***********************************************************
     *  Function Name : register
     *  Functionality : Register user on the application.
     *                  register via the manually or via the social media(facebook etc.).
     *  @access         public
     *  @param        : input
     *  @return       : input data
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function register(Request $request)
    {
        //token verification
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $is_social = $request->input('is_social');
        if ($data = $this->check_integer_empty($is_social, 'Please provide is_social 0 or 1'))
            return response()->json($data, 200);
        $device_token = $request->input('device_token');
        if ($data = $this->check_empty($device_token, 'Please provide device_token'))
            return response()->json($data, 200);
        $contact_number = $request->input('contact_number');
        if ($data = $this->check_empty($contact_number, 'Please provide contact_number'))
            return response()->json($data, 200);
        $country_phone_code = $request->input('country_phone_code');
        if ($data = $this->check_empty($country_phone_code, 'Please provide country_phone_code'))
            return response()->json($data, 200);
        $otp = $request->input('otp');
        if ($data = $this->check_empty($otp, 'Please provide otp'))
            return response()->json($data, 200);
        $latitude = $request->input('latitude');
        if ($data = $this->check_empty($latitude, 'Please provide latitude'))
            return response()->json($data, 200);
        $longitude = $request->input('longitude');
        if ($data = $this->check_empty($longitude, 'Please provide longitude'))
            return response()->json($data, 200);

        $time_zone = $request->input('time_zone');
        if(empty($time_zone)){

            $time_zone = 'America/Los_Angeles';
        }
        $role = 'passenger';

        $referral_code = $request->input('referral_code');

        $role_id = DB::table('roles')->where('slug', $role)->pluck('id');

        //referral_code module
        if (!empty($referral_code)) {
            $code_type = substr($referral_code, 0, 2);
            if($code_type == 'PS'){
                $referral_type ='3';
                $referralCodeCheck =  DB::table('dn_users')->where('passenger_referral_code', $referral_code)->pluck('id');
            }else if($code_type == 'DR'){
                //$referral_type ='4';
                //$referralCodeCheck =  DB::table('dn_users_data')->where('referral_code', $referral_code)->pluck('user_id');
                return response()->json(['status' => 0, 'message' => 'You can not enter the driver referral code.'], 200);
            }
            if (empty($referralCodeCheck)) {
                // return response()->json(['status' => 0, 'message' => 'Referral code doesn\'t exist'], 200);
            } else {
                $referred_by = $referralCodeCheck;
            }

        }

        //check already registered device
        /* $userDeviceCheck = DB::table('dn_users')->select('id')->where(['device_token' => $device_token])->first();
         if ($userDeviceCheck)
             return response()->json(['status' => 0, 'message' => 'You are already signed up on this device'], 200);
         */

        //otp verification module
        $userDevIdCheck = DB::table('dn_user_verification')->select('otp')->where(['contact_number' => $contact_number])->orderBy('id', 'desc')->first();
        if ($userDevIdCheck) {
            if ($otp === $userDevIdCheck->otp) {
                DB::table('dn_user_verification')
                    ->where(['device_token' => $device_token])
                    ->update(['verified' => 1]);
            } else {
                return response()->json(['status' => 0, 'message' => 'Enter valid otp'], 200);
            }
        } else {
            return response()->json(['status' => 0, 'message' => 'Device doesn\'t match '], 200);
        }


        // social logged user
        if ($is_social == 1) {
            $social_id = $request->input('social_id');
            if ($data = $this->check_empty($social_id, 'Please provide social_id'))
                return response()->json($data, 200);

            $first_name = $request->input('first_name');
            $last_name = $request->input('last_name');
            $full_name = $first_name.' '.$last_name;

            $userSocialIdCheck = DB::table('dn_users')->select(array('id', 'social_id', 'email', 'contact_number', 'is_social'))->where('social_id', $social_id)->first();
            if ($userSocialIdCheck)
                return response()->json(['status' => 1, 'user_id' => $userSocialIdCheck->id, 'data' => $userSocialIdCheck], 200);
            else {
                $insert_data = array(
                    'email' => "",
                    'contact_number' => $contact_number,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'time_zone' => $time_zone,
                    'full_name' => $full_name,
                    'country_phone_code' => $country_phone_code,
                    'social_id' => $social_id,
                    'is_social' => $is_social,
                    'device_token' => $device_token,
                    'updated_at' => date('Y-m-d H:i:s'),

                );
            }
        } else {
            // normal register user

            $email = $request->input('email');
            if ($data = $this->check_empty($email, 'Please provide email'))
                return response()->json($data, 200);

            $password = $request->input('password');
            if ($data = $this->check_empty($password, 'Please provide password'))
                return response()->json($data, 200);


            //check already registered Phone
            $userPhoneCheck = DB::table('dn_users')->select('id')->where('contact_number', $contact_number)->first();
            if ($userPhoneCheck)
                return response()->json(['status' => 0, 'message' => 'User exist with this Contact Number'], 200);

            $insert_data = array(
                'email' => $email,
                'contact_number' => $contact_number,
                'country_phone_code' => $country_phone_code,
                'password' => Hash::make($password),
                'time_zone' => $time_zone,
                'social_id' => "",
                'first_name' => "",
                'last_name' => "",
                'is_social' => $is_social,
                'device_token' => $device_token,
                'updated_at' => date('Y-m-d H:i:s')
            );
        }

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

            //referral data insert
            if ($referral_code) {
                DB::table('dn_user_referrals')->insertGetId(['user_id' => $insertGetId, 'referred_by' => $referred_by, 'referral_code' => $referral_code,'referral_type' => $referral_type]);
            }


            $insert_data['role'] = $role_id;
            return response()->json(['status' => 1, 'user_id' => $insertGetId, 'data' => $insert_data]);
        }

    }
    /**
     ***********************************************************
     *  Function Name : driver_request
     *  Functionality : It gives the response to the driver data.
     *  @access         public
     *  @param        : user_id
     *  @return       : become_driver_request, is_driver_approved
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function driver_request(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $driver_data= DB::table('dn_users')->select('become_driver_request', 'is_driver_approved')->where('id', $user_id)->first();
        if($driver_data){

            return response()->json(['status' => 1, 'data' => $driver_data]);
        }
        else{

            return response()->json(['status' => 0, 'message' => 'No data found about this driver!']);
        }
    }
    /**
     ***********************************************************
     *  Function Name : become_driver
     *  Functionality : In this function we send request to the admin for become a driver.
     *  @access         public
     *  @param        : all the input parameter get from the post method,first check if user is exist or not.
     *  @return       : return the success with the status 1 with success message otherwise status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function become_driver(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $first_name = $request->input('first_name');
        if ($data = $this->check_empty($first_name, 'Please provide first name'))
            return response()->json($data, 200);

        $last_name = $request->input('last_name');
        if ($data = $this->check_empty($last_name, 'Please provide last name'))
            return response()->json($data, 200);
        $full_name = $first_name.' '.$last_name;
        $dob = $request->input('dob');
        if ($data = $this->check_empty($dob, 'Please provide dob'))
            return response()->json($data, 200);
        $gender = $request->input('gender');
        if ($data = $this->check_empty($gender, 'Please provide gender'))
            return response()->json($data, 200);
        $email = $request->input('email');
        if ($data = $this->check_empty($email, 'Please provide email'))
            return response()->json($data, 200);
        $contact_number = $request->input('contact_number');
        if ($data = $this->check_empty($contact_number, 'Please provide contact number'))
            return response()->json($data, 200);
        $city = $request->input('city');
        if ($data = $this->check_empty($city, 'Please provide city'))
            return response()->json($data, 200);
        $state = $request->input('state');
        if ($data = $this->check_empty($state, 'Please provide state'))
            return response()->json($data, 200);
        $zip_code = $request->input('zip_code');
        if ($data = $this->check_empty($zip_code, 'Please provide zip code'))
            return response()->json($data, 200);
        $address_1 = $request->input('address_1');
        if ($data = $this->check_empty($address_1, 'Please provide address line 1'))
            return response()->json($data, 200);
        $address_2 = $request->input('address_2');
        if(empty($address_2)){
            $address_2 = '';
        }
        $anniversary = $request->input('anniversary');
        ///  if ($data = $this->check_empty($anniversary, 'Please provide anniversary'))
        // return response()->json($data, 200);
        $license_verification = Input::file('license_verification');
        if ($data = $this->check_empty($license_verification, 'Please provide license for verification'))
            return response()->json($data, 200);
        $proof_of_insurance = Input::file('proof_of_insurance');
        if ($data = $this->check_empty($proof_of_insurance, 'Please provide proof of insurance'))
            return response()->json($data, 200);

        $license_exp    = $request->input('licence_exp');
        if ($data = $this->check_empty($license_exp, 'Please provide license expiry date'))
            return response()->json($data, 200);
        $insurance_exp  = $request->input('insurance_exp');
        if ($data = $this->check_empty($insurance_exp, 'Please provide insurance expiry date'))
            return response()->json($data, 200);
        // $driver_records = array();

        if($dob !=''){
        }else{
            $dob = '';
        }
        if($anniversary !=''){
        }else{
            $anniversary = '';
        }
        $profile_pic = Input::file('profile_pic');
        if ($data = $this->check_empty($profile_pic, 'Please provide profile picture'))
            return response()->json($data, 200);
        $license_number = $request->input('license_number');
        if ($data = $this->check_empty($license_number, 'Please provide license number'))
            return response()->json($data, 200);
        $ssn    = $request->input('ssn');
        if ($data = $this->check_empty($ssn, 'Please provide ssn number'))
            return response()->json($data, 200);

        // $ssn = \Illuminate\Support\Facades\Crypt::encrypt($encrypted);
        $car_transmission = $request->input('car_transmission');
        if ($data = $this->check_empty($car_transmission, 'Please provide car transmission'))
            return response()->json($data, 200);
        $proof_of_insurance_input =  Input::file('proof_of_insurance');
        if ($data = $this->check_empty($proof_of_insurance_input, 'Please provide proof of insurance'))
            return response()->json($data, 200);
        $license_verification_input =  Input::file('license_verification');
        if ($data = $this->check_empty($license_verification_input, 'Please provide license for verification'))
            return response()->json($data, 200);
        $question = array("Have you had more than one accident in the last three years?","Have you ever had more than two points on your driver’s license?","Have you ever had more than one moving violation in last two years?","Have you been ever arrested for a DUI/OVI?","Have you ever been convicted for a crime?","Have you been driving for less than 2 years?","Are you less than 21 years of age?","Can you drive a manual(stick) transmission?","Do you have a commercial driver`s license?","How did you hear about DeziNow?");
        $answer = array();
        for( $i=0; $i<=9; $i++ )
        {
            ${"driver_records_".$i} = $request->input("driver_records_".$i);
            $answer[] = ${"driver_records_".$i};
        }
        $final_array = [];
        for( $i=0; $i<10; $i++ ){
            $final_array[$i]['question'] = $question[$i];
            $final_array[$i]['answer'] = $answer[$i];

        }

        $driver_records = json_encode($final_array);
        $referral_code = $request->input('referral_code');

        //referral_code module
        if (!empty($referral_code)) {
            $code_type = substr($referral_code, 0, 2);
            if($code_type == 'PS'){
                return response()->json(['status' => 0, 'message' => 'You can not enter the passenger referral code.'], 200);
            }else if($code_type == 'DR'){
                $referral_type ='4';
                $referralCodeCheck =  DB::table('dn_users_data')->where('referral_code', $referral_code)->pluck('user_id');
            }
            if (empty($referralCodeCheck)) {
                return response()->json(['status' => 0, 'message' => 'Referral code doesn\'t exist'], 200);
            } else {
                $referred_by = $referralCodeCheck;
            }
        }
        $contact_number_last_digit = rand(1000, 9999);
        $driver_referral_code = 'DR'.$first_name.$contact_number_last_digit;
        $check_exist= DB::table('dn_users')->select('id')->where('id', $user_id)->first();
        if($check_exist){
            DB::table('dn_users')->where(['id' => $user_id])->update(['first_name' => $first_name,
                'last_name' => $last_name,
                'full_name' => $full_name,
                'email'     => $email,
                'dob'   => $dob,
                'anniversary' => $anniversary,
                'gender'    => $gender,
                'become_driver_request' => '1',
                'address_1' => $address_1,
                'address_2'  => $address_2,
                'city' => $city,
                'contact_number' => $contact_number,
                'state'  => $state,
                'zip_code' =>$zip_code,
                'driver_requested_on' => date('Y-m-d H:i:s')]);
            if ($referral_code) {
                DB::table('dn_user_referrals')->insertGetId(['user_id' => $user_id, 'referred_by' => $referred_by, 'referral_code' => $referral_code,'referral_type' => $referral_type]);
            }

            if ($profile_pic) {
                $destinationPath = 'uploads/profile-img/';
                $filename = md5(microtime() . $profile_pic->getClientOriginalName()) . "." . $profile_pic->getClientOriginalExtension();
                Input::file('profile_pic')->move($destinationPath, $filename);
                $driver_profile_pic_path = $destinationPath . $filename;

            }
            $driver_detail = DB::table('dn_driver_requests')->select('*')->where('user_id', $user_id)->first();
            $driver_license_detail = DB::table('dn_users_data')->select('*')->where('user_id', $user_id)->first();
            if($driver_license_detail){
                DB::table('dn_users_data')->where(['user_id' => $user_id])->update(['license_number' => $license_number,'transmission' => $car_transmission,'terms_conditions' => '1','driver_profile_pic' => $driver_profile_pic_path,'ssn' => $ssn]);
            }else{
                $insertGetId = DB::table('dn_users_data')->insertGetId(['user_id' => $user_id,'license_number' => $license_number,'transmission' => $car_transmission,'terms_conditions' => '1','driver_profile_pic' => $driver_profile_pic_path,'ssn' => $ssn,'referral_code'=>$driver_referral_code]);
            }
            if($driver_detail){
                if ($license_verification) {
                    $destinationPath = 'uploads/drivers-documents/';
                    $filename = md5(microtime() . $license_verification->getClientOriginalName()) . "." . $license_verification->getClientOriginalExtension();
                    Input::file('license_verification')->move($destinationPath, $filename);
                    $license_verification_path = $destinationPath . $filename;
                    DB::table('dn_driver_requests')->where(['user_id' => $user_id])->update(['license_verification' => $license_verification_path]);
                }
                if ($proof_of_insurance) {
                    $destinationPath = 'uploads/drivers-documents/';
                    $filename = md5(microtime() . $proof_of_insurance->getClientOriginalName()) . "." . $proof_of_insurance->getClientOriginalExtension();
                    Input::file('proof_of_insurance')->move($destinationPath, $filename);
                    $proof_of_insurance_path = $destinationPath . $filename;

                    DB::table('dn_driver_requests')->where(['user_id' => $user_id])->update(['proof_of_insurance' => $proof_of_insurance_path]);
                }

                DB::table('dn_driver_requests')->where(['user_id' => $user_id])->update(['car_transmission' => $car_transmission,'licence_expiration' => $license_exp ,'insurance_expiration'=> $insurance_exp,'driver_records' =>$driver_records]);

            } else {


                $insertGetId = DB::table('dn_driver_requests')->insertGetId(['car_transmission' => $car_transmission,'user_id' => $user_id,'driver_records' =>$driver_records,'licence_expiration' => $license_exp ,'insurance_expiration'=> $insurance_exp]);
                if ($insertGetId) {
                    if ($license_verification) {
                        $destinationPath = 'uploads/drivers-documents/';
                        $filename = md5(microtime() . $license_verification->getClientOriginalName()) . "." . $license_verification->getClientOriginalExtension();
                        Input::file('license_verification')->move($destinationPath, $filename);
                        $license_verification_path = $destinationPath . $filename;
                        DB::table('dn_driver_requests')->where(['user_id' => $user_id])->update(['license_verification' => $license_verification_path]);
                    }
                    if ($proof_of_insurance) {
                        $destinationPath = 'uploads/drivers-documents/';
                        $filename = md5(microtime() . $proof_of_insurance->getClientOriginalName()) . "." . $proof_of_insurance->getClientOriginalExtension();
                        Input::file('proof_of_insurance')->move($destinationPath, $filename);
                        $proof_of_insurance_path = $destinationPath . $filename;
                        DB::table('dn_driver_requests')->where(['user_id' => $user_id])->update(['proof_of_insurance' => $proof_of_insurance_path]);
                    }
                }
            }
            return response()->json(['status' => 1, 'message' => 'Your application has been submitted successfully.']);
        }
        else{

            return response()->json(['status' => 0, 'message' => 'User doesn\'t exist!']);
        }
    }
    /**
     ***********************************************************
     *  Function Name : login
     *  Functionality : Login into the application with entering email, password or social media id.
     *  @access         public
     *  @param        : email,password, or social_id.
     *  @return       : if login successfully return the data of user otherwise status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function login(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $is_social = $request->input('is_social');
        if ($data = $this->check_integer_empty($is_social, 'Please provide is_social 0 or 1'))
            return response()->json($data, 200);

        /* $device_token = $request->input('device_token');
         if ($data = $this->check_empty($device_token, 'Please provide device_token'))
             return response()->json($data, 200);*/
        $time_zone = $request->input('time_zone');
        if(empty($time_zone)){

            $time_zone = 'America/Los_Angeles';
        }

        if ($is_social == 1) {
            $social_id = $request->input('social_id');
            if ($data = $this->check_empty($social_id, 'Please provide social_id'))
                return response()->json($data, 200);

            //social login
            $userSocialIdCheck = DB::table('dn_users')->where('social_id', $social_id)->first();
            if ($userSocialIdCheck) {
                //user blocked by admin
                if ($userSocialIdCheck->active == 0) {
                    return response()->json(['status' => 3, 'message' => 'You are blocked by admin.'], 200);
                }
                unset($userSocialIdCheck->password);
                if($userSocialIdCheck->is_driver_approved == 1){

                    $const = $this->firebaseConstant();
                    $DEFAULT_PATH = '/DeziNow';
                    $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
                    // --- reading the stored string ---
                    $deleted = $firebase->delete($DEFAULT_PATH . "/drivers/".$userSocialIdCheck->id);
                    $firebase->delete($DEFAULT_PATH . "/drivers_location/".$userSocialIdCheck->id);

                }
                //if($userSocialIdCheck->is_logged == 'false'){
                // return response()->json(['status' => 0, 'message' => 'Please logged out another device'], 200);
                // }
                $user_role = DB::table('role_user')->where('user_id', $userSocialIdCheck->id)->orderBy('created_at', 'desc')->pluck('role_id');
                if($user_role == 1 OR $user_role == 2){
                    return response()->json(['status' => 0, 'message' => 'You have no permission for login.']);

                }
                //update logged user status in user table

                DB::table('dn_users')->where(['id' => $userSocialIdCheck->id])->update(array('is_logged' => 'true','time_zone'=>$time_zone));


                //previous logged in user log update
                DB::table('dn_user_logs')->where(['user_id' => $userSocialIdCheck->id, 'user_mode' => 'passenger'])->whereNull('logout_time')->orderBy('created_at', 'desc')
                    ->take(1)
                    ->update(['logout_time' => date('Y-m-d H:i:s')]);

                //logged in user log
                DB::table('dn_user_logs')->insertGetId(['user_id' => $userSocialIdCheck->id, 'login_time' => date('Y-m-d H:i:s'), 'user_mode' => 'passenger']);

                return response()->json(['status' => 1, 'user_id' => $userSocialIdCheck->id, 'data' => $userSocialIdCheck], 200);
            } else {
                //user not registered
                return response()->json(['status' => 2, 'message' => 'hit send otp api'], 200);
            }

        } else {
            $email = $request->input('email');
            $contact_number = $request->input('contact_number');
            $password = $request->input('password');

            if ($data = $this->check_empty($password, 'Please provide password'))
                return response()->json($data, 200);

            if ($email) {
                $userEmailCheck = DB::table('dn_users')->where(array('email' => $email))->first();

                if (!$userEmailCheck) {
                    return response()->json(['status' => 0, 'message' => 'User doesn\'t exist with this email'], 200);
                } //empty password means social login, person try normal login
                elseif (!$userEmailCheck->password) {
                    return response()->json(['status' => 0, 'message' => 'Please login via social media.'], 200);
                } //user blocked by admin
                elseif ($userEmailCheck->active == 0) {
                    return response()->json(['status' => 3, 'message' => 'You are blocked by admin.'], 200);
                } elseif (!Hash::check($password, $userEmailCheck->password)) {
                    return response()->json(['status' => 0, 'message' => 'User doesn\'t exist with this email or password'], 200);
                } else {
                    unset($userEmailCheck->password);
                    $userData = $userEmailCheck;
                }

            }
            if ($contact_number) {
                $userPhoneCheck = DB::table('dn_users')->where(array('contact_number' => $contact_number))->first();

                if (!$userPhoneCheck) {
                    return response()->json(['status' => 0, 'message' => 'User doesn\'t exist with this Contact Number'], 200);
                } elseif (!$userPhoneCheck->password) {
                    return response()->json(['status' => 0, 'message' => 'You have to make social login'], 200);
                } elseif ($userPhoneCheck->active == 0) {
                    return response()->json(['status' => 3, 'message' => 'You are blocked'], 200);
                } elseif (!Hash::check($password, $userPhoneCheck->password)) {
                    return response()->json(['status' => 0, 'message' => 'User doesn\'t exist with this contact number or password'], 200);
                } else {

                    unset($userPhoneCheck->password);
                    $userData = $userPhoneCheck;
                }
            }

            if($userData->is_driver_approved == 1){
                $firebase_id =  $userData->id;
                $const = $this->firebaseConstant();
                $DEFAULT_PATH = '/DeziNow';
                $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
                // --- reading the stored string ---
                $deleted = $firebase->delete($DEFAULT_PATH . "/drivers/".$firebase_id);
                $firebase->delete($DEFAULT_PATH . "/drivers_location/".$firebase_id);

            }
            // if($userData->is_logged == 'false'){
            //   return response()->json(['status' => 0, 'message' => 'Please logged out another device'], 200);
            // }
        }
        //select user role

        $user_role = DB::table('role_user')->where('user_id', $userData->id)->orderBy('created_at', 'desc')->pluck('role_id');
        if($user_role == 1 OR $user_role == 2){
            return response()->json(['status' => 0, 'message' => 'You have no permission for login.']);

        }
        //update logged user status in user table

        DB::table('dn_users')
            ->where(['id' => $userData->id])
            ->update(array('is_logged' => 'true','time_zone'=>$time_zone));


        //update role in return array
        $userData->role = $user_role;


        //previous logged in user log update
        DB::table('dn_user_logs')
            ->where(['user_id' => $userData->id, 'user_mode' => 'passenger'])
            ->whereNull('logout_time')
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->update(['logout_time' => date('Y-m-d H:i:s')]);

        //logged in user log
        DB::table('dn_user_logs')->insertGetId(['user_id' => $userData->id, 'login_time' => date('Y-m-d H:i:s'), 'user_mode' => 'passenger']);
        return response()->json(['status' => 1, 'user_id' => $userData->id, 'data' => $userData]);

    }
    /**
     ***********************************************************
     *  Function Name : logout
     *  Functionality : Logout from the application , if log out as driver , its delete from firebase
     *                  and proper maintain log detail in log deatil table in data.
     *  @access         public
     *  @param        : user_id.
     *  @return       : if logout successfully return the status 1 otherwise status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function logout(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide email'))
            return response()->json($data, 200);


        //check registered user
        $userData = DB::table('dn_users')->select('id','is_driver_approved')->where('id', $user_id)->first();
        if (!$userData)
            return response()->json(['status' => 0, 'message' => 'User doesn\'t exist'], 200);

        //update logged user status in user table
        DB::table('dn_users')
            ->where(['id' => $user_id])
            ->update(array('is_logged' => 'false'));

        //logged user log update
        DB::table('dn_user_logs')
            ->where(['user_id' => $userData->id])
            ->whereNull('logout_time')
            ->orderBy('created_at', 'desc')
            ->take(1)
            ->update(['logout_time' => date('Y-m-d H:i:s')]);


        //delete user from firebase
        if($userData->is_driver_approved){

            $const = $this->firebaseConstant();
            $DEFAULT_PATH = '/DeziNow';
            $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
            // --- reading the stored string ---
             $deleted = $firebase->delete($DEFAULT_PATH . "/drivers/$user_id");
             $firebase->delete($DEFAULT_PATH . "/drivers_location/$user_id");
            $firebase->delete($DEFAULT_PATH . "/drivers/");
            DB::table('dn_driver_logs')
                ->where(['user_id' => $user_id]) ->whereNull('logout_time') ->orderBy('id', 'desc')->take(1)->update(['logout_time' => date('Y-m-d H:i:s')]);

        }


        return response()->json(['status' => 1, 'message' => 'Logged out successfully'], 200);
    }
    /**
     ***********************************************************
     *  Function Name : send_otp
     *  Functionality : send the OTP to the user phone number for phone verification on change password, update phone number or user registering.
     *  @access         public
     *  @param        : contact_number.
     *  @return       : if logout successfully return the message "Otp sent successfully" with status 1 otherwise status 0 with valid message;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function send_otp(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $device_id = $request->input('device_token');
        if ($data = $this->check_empty($device_id, 'Please provide device_token'))
            return response()->json($data, 200);
        $contact_number = $request->input('contact_number');
        if ($data = $this->check_empty($contact_number, 'Please provide contact_number'))
            return response()->json($data, 200);
        $country_phone_code = $request->input('country_phone_code');
        if ($data = $this->check_empty($country_phone_code, 'Please provide country_phone_code'))
            return response()->json($data, 200);
        $contact_code = $country_phone_code.$contact_number;
        $is_social = $request->input('is_social');
        if ($data = $this->check_integer_empty($is_social, 'Please provide is_social 0 or 1'))
            return response()->json($data, 200);

        if (!$is_social) {
            $email = $request->input('email');
            if ($data = $this->check_empty($email, 'Please provide email'))
                return response()->json($data, 200);
        }
        $otp = rand(1000, 9999);
        //check already registered device
        /* $userDeviceCheck = DB::table('dn_users')->select('id')->where(['device_token' => $device_id])->first();
         if ($userDeviceCheck)
             return response()->json(['status' => 0, 'message' => 'You are already signed up on this device'], 200);
         */
        //check already registered Phone
        $userPhoneCheck = DB::table('dn_users')->select('id')->where('contact_number', $contact_number)->first();
        if ($userPhoneCheck)
            return response()->json(['status' => 0, 'message' => 'User exist with this Contact Number'], 200);

        //verify device exits
        /*
        $userDeviceCheck = DB::table('dn_user_verification')->where(['device_token' => $device_id, 'contact_number' => $contact_number,'country_phone_code' => $country_phone_code])->first();
        if ($userDeviceCheck) {
            if ($userDeviceCheck->verified == 1) {
                return response()->json(array('status' => 0, 'message' => 'You are verified goto login'), 200);
            }
            else {
                $contact_code = $country_phone_code.$userDeviceCheck->contact_number;
                $is_sent= $this->twileo_send($contact_code, $otp);
                if($is_sent == 1) {
                    DB::table('dn_user_verification')->where(['id' => $userDeviceCheck->id])->update(['otp' => $otp]);
                    return response()->json(['status' => 1, 'otp' => $otp, 'message' => 'Otp sent successfully'], 200);
                }else{
                    return response()->json(['status' => 0, 'message' => 'Phone number is not valid!'], 200);
                }
            }
        }*/
        //send token
        $is_sent =  $this->twileo_send($contact_code, $otp);
        if($is_sent == 1){
            if ($otp) {
                $insert_data = array(
                    'device_token' => $device_id,
                    'contact_number' => $contact_number,
                    'country_phone_code' => $country_phone_code,
                    'otp' => $otp
                );

                $insertGetId = DB::table('dn_user_verification')->insertGetId($insert_data);
                if ($insertGetId)
                    return response()->json(['status' => 1, 'otp' => $otp, 'message' => 'Otp sent successfully'], 200);

                else {
                    return response()->json(['status' => 0, 'message' => 'Phone number is not valid!'], 200);
                }
            }else{

                return response()->json(['status' => 0, 'message' => 'Phone number is not valid!'], 200);
            }

        }else{
            return response()->json(['status' => 0, 'message' => 'Phone number is not valid!'], 200);
        }

    }
    /**
     ***********************************************************
     *  Function Name : driver_profile
     *  Functionality : This function using for updating the profile of user at first time registrations and after including all the basic info.
     *  @access         public
     *  @param        : all the basic info e.g. first name, last name , email.
     *  @return       : if we pass all the parameter proper it gives status 1 and all the input parameter array;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function driver_profile(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }

        $email = $request->input('email');
        if ($data = $this->check_empty($email, 'Please provide email'))
            return response()->json($data, 200);

        //check already registered email
        $userMailCheck = DB::table('dn_users')->select('id')->where(['email' => $email,'social_id' => 0])->first();
        if ($userMailCheck)
            return response()->json(['status' => 0, 'message' => 'User exist with this Email Number'], 200);
        $first_name = $request->input('first_name');
        if ($data = $this->check_empty($first_name, 'Please provide first_name'))
            return response()->json($data, 200);

        $last_name = $request->input('last_name');

        $full_name = $first_name.' '.$last_name;
        $dob = $request->input('dob');
        // $gender = $request->input('gender');
        $anniversary = $request->input('anniversary');
        $transmission = $request->input('transmission');
        $navigation = $request->input('navigation');
        $remove_profile_pic = $request->input('remove_profile_pic');
        $is_driver = $request->input('is_driver');
        $city = $request->input('city');

        $state = $request->input('state');
        if($is_driver != 1){
            if ($data = $this->check_empty($city, 'Please provide city'))
                return response()->json($data, 200);
            if ($data = $this->check_empty($state, 'Please provide state'))
                return response()->json($data, 200);
        }

        // $ssn = $request->input('ssn');
        //$licence_number = $request->input('license');

        $profile_pic = Input::file('profile_pic');

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        //check registered user id
        $userDeviceCheck = DB::table('dn_users')->select(array('id', 'email', 'contact_number', 'is_social', 'profile_pic','passenger_referral_code'))->where('id', $user_id)->first();
        if (!$userDeviceCheck)
            return response()->json(['status' => 7, 'message' => 'Its been too late ,please register again.'], 200);

        //$contact_number_last_digit  = substr($userDeviceCheck->contact_number, -4 );
        $contact_number_last_digit = rand(1000, 9999);
        $passenger_referral_code = 'PS'.$first_name.$contact_number_last_digit;
        if(!empty($userDeviceCheck->passenger_referral_code)){

            $passenger_referral_code = $userDeviceCheck->passenger_referral_code;
        }
        //driver profile update
        if ($is_driver == 1) {

            $role = 'driver';
            $driver_role_id = DB::table('roles')->where('slug', $role)->pluck('id');

            $role = 'passenger';
            $passenger_role_id = DB::table('roles')->where('slug', $role)->pluck('id');

            //select user role
            $user_role = DB::table('role_user')->where('user_id', $user_id)->orderBy('created_at', 'desc')->pluck('role_id');

            if (!$userDeviceCheck->email) {
                //check registered emails on update
                $userEmailCheck = DB::table('dn_users')->select(array('id', 'email'))->where('email', $email)->first();
                if ($userEmailCheck)
                    return response()->json(['status' => 0, 'message' => 'User exist with this email'], 200);
            }

            $insert_data = array(
                'user_id' => $user_id,
                'transmission' => $transmission,
                'navigation_system' => $navigation,
                'modified_at' => date('Y-m-d H:i:s')
            );

            //role passenger means user not exist in user_data(for driver profile) table table
            if ($user_role == $passenger_role_id) {
                $check_first =DB::table('dn_users_data')->where('user_id', $user_id)->first();
                if(empty($check_first)){
                    DB::table('dn_users_data')->insertGetId($insert_data);
                }else{
                    DB::table('dn_users_data')->where(['user_id' => $user_id])->update($insert_data);
                }

            }
            //role driver means user exist in user_data table just update that
            if ($user_role == $driver_role_id) {
                $check_first =DB::table('dn_driver_requests')->where('user_id', $user_id)->first();
                if(empty($check_first)){
                    DB::table('dn_users_data')->insertGetId($insert_data);
                    DB::table('dn_driver_requests')->insertGetId(['car_transmission' => $transmission, 'navigation' => $navigation]);
                }else{
                    DB::table('dn_users_data')->where(['user_id' => $user_id])->update($insert_data);
                    DB::table('dn_driver_requests')->where(['user_id' => $user_id])->update(['car_transmission' => $transmission, 'navigation' => $navigation]);
                }
            }

            if ($profile_pic) {
                $destinationPath = 'uploads/profile-img/';
                $filename = md5(microtime() . $profile_pic->getClientOriginalName()) . "." . $profile_pic->getClientOriginalExtension();
                Input::file('profile_pic')->move($destinationPath, $filename);
                $profile_pic_path = $destinationPath . $filename;

            }
            //inset only first time next time role become 4
            if ($user_role == $passenger_role_id) {
                DB::table('role_user')->insertGetId(['role_id' => $driver_role_id, 'user_id' => $user_id]);
            }


            $update_arr = [
                'email' => $email,
                'first_name' => $first_name,
                'full_name' => $full_name,
                'passenger_referral_code'=>$passenger_referral_code,
                'last_name' => $last_name,
                'dob' => $dob,
                'profile_status' => 1,
                'anniversary' => $anniversary,
            ];

            if ($profile_pic_path) {
                $update_arr['profile_pic'] = $profile_pic_path;
            }

            DB::table('dn_users')
                ->where(['id' => $user_id])
                ->update($update_arr);


            if (!$update_arr['profile_pic']) {
                $update_arr['profile_pic'] = $userDeviceCheck->profile_pic;
            }
            // remove pic
            if ($remove_profile_pic) {
                $update_arr['profile_pic'] = "";

            }

            $return_arr = array_merge($insert_data, $update_arr);

            $return_arr['role'] = $user_role;
        }
        //passenger update
        else {

            //old and new emails are diffrent
            if ($userDeviceCheck->email != $email) {
                //check registered emails on update
                $userEmailCheck = DB::table('dn_users')->select(array('id', 'email'))->where('email', $email)->first();
                if ($userEmailCheck) {
                    return response()->json(['status' => 0, 'message' => 'User exist with this email'], 200);

                }

            }

            $update_arr = [
                'email' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'full_name' => $full_name,
                'dob' => $dob,
                'profile_status' => 1,
                'passenger_referral_code'=>$passenger_referral_code,
                'city' => $city,
                'state' => $state,
                'anniversary' => $anniversary,
            ];

            if ($profile_pic) {
                $destinationPath = 'uploads/profile-img/';
                $filename = md5(microtime() . $profile_pic->getClientOriginalName()) . "." . $profile_pic->getClientOriginalExtension();
                Input::file('profile_pic')->move($destinationPath, $filename);
                $profile_pic_path = $destinationPath . $filename;
            }

            if ($profile_pic_path) {
                $update_arr['profile_pic'] = $profile_pic_path;
            }

            DB::table('dn_users')
                ->where(['id' => $user_id])
                ->update($update_arr);


            if (!$update_arr['profile_pic']) {
                $update_arr['profile_pic'] = $userDeviceCheck->profile_pic;
            }
            // remove pic
            if ($remove_profile_pic) {
                $update_arr['profile_pic'] = "";

            }

            $return_arr = $update_arr;

        }
        $return_arr['contact_number'] = $userDeviceCheck->contact_number;
        return response()->json(['status' => 1, 'user_id' => $user_id, 'data' => $return_arr]);

    }
    /**
     ***********************************************************
     *  Function Name : get_profile
     *  Functionality : This function using for getting user data for user profile page.
     *  @access         public
     *  @param        : user_id.
     *  @return       : it gives user data with status 1 and error with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function get_profile(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }

        $role = 'driver';
        $driver_role_id = DB::table('roles')->where('slug', $role)->pluck('id');

        $role = 'passenger';
        $passenger_role_id = DB::table('roles')->where('slug', $role)->pluck('id');

        //select user role
        $user_role = DB::table('role_user')->where('user_id', $user_id)->orderBy('created_at', 'desc')->pluck('role_id');


        //check registered user
        $userData = DB::table('dn_users')->where('id', $user_id)->first();

        if (!$userData)
            return response()->json(['status' => 7, 'message' => 'Its been too late ,please register again.'], 200);


        //get driver profile with all fields
        if ($user_role == $driver_role_id) {
            if ($user = $this->is_revoked($user_id)) {
                return response()->json($user);
            }

            $driverData = DB::table('dn_users_data')->where('user_id', $user_id)->first();

        }
        //passenger profile with empty fields
        if ($user_role == $passenger_role_id) {
            $driverData = [
                "transmission" => "",
                "navigation_system" => "",
                "terms_conditions" => "",
                "driver_profile_pic" => "",
                "active" => "",
            ];

        }
        $userDataArray = array_merge((array)$driverData, (array)$userData);

        unset($userDataArray['password']);
        unset($userDataArray['id']);
        unset($userDataArray['user_id']);

        unset($userDataArray['name']);
        unset($userDataArray['role']);
        $userDataArray['role'] = $user_role;
        $city_name = DB::table('dn_cities')->where(['id' => $userData->city])->pluck('city');
        $state_name = DB::table('dn_states')->where(['state_code' => $userData->state])->pluck('state');
        $userDataArray['city_name']  = (!empty($city_name)) ? $city_name : '';
        $userDataArray['state_name'] = (!empty($state_name)) ? $state_name : '';
        return response()->json(['status' => 1, 'user_id' => $user_id, 'data' => $userDataArray], 200);

    }
    /**
     ***********************************************************
     *  Function Name : get_passenger_ride_info
     *  Functionality : This function using for getting user personal info, payment info and car detail for ride .
     *  @access         public
     *  @param        : user_id.
     *  @return       : it gives user data with status 1 and error with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function get_passenger_ride_info(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        //check registered user
        $userData = DB::table('dn_users')->where('id', $user_id)->first();

        if (!$userData)
            return response()->json(['status' => 0, 'message' => 'User doesn\'t exist'], 200);

        $user_cars = DB::table('dn_user_cars')->where(['user_id' => $user_id, 'is_delete' => '0', 'is_default' => '1'])->first();

        $user_accounts = DB::table('dn_payment_accounts')->select('id','user_id', 'account_email', 'account_type' ,'card_type','masked_number', 'image_url', 'card_last_4', 'expired', 'is_default' )->where(['user_id' => $user_id, 'is_default' => '1', 'is_delete' => '0'])->first();

        $passenger_ride_info = ['cars' => $user_cars, 'payment_methods' => $user_accounts];


        return response()->json(['status' => 1, 'data' => $passenger_ride_info], 200);

    }
    /**
     ***********************************************************
     *  Function Name : get_estimated_fare
     *  Functionality : This function using for getting fare estimate between two lat , long .
     *  @access         public
     *  @param        : pickup lat,long and destination lat,long.
     *  @return       : it gives fare estimate with status 1 and error with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function get_estimated_fare(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $pickup_latitude = $request->input('pickup_latitude');
        if ($data = $this->check_empty($pickup_latitude, 'Please provide pickup_latitude'))
            return response()->json($data, 200);
        $pickup_longitude = $request->input('pickup_longitude');
        if ($data = $this->check_empty($pickup_longitude, 'Please provide pickup_longitude'))
            return response()->json($data, 200);
        $destination_latitude = $request->input('destination_latitude');
        if ($data = $this->check_empty($destination_latitude, 'Please provide destination'))
            return response()->json($data, 200);
        $destination_longitude = $request->input('destination_longitude');
        if ($data = $this->check_empty($destination_longitude, 'Please provide destination'))
            return response()->json($data, 200);

        //select admin editable charges
        $ETA = $this->get_distance_time($pickup_latitude, $pickup_longitude, $destination_latitude, $destination_longitude);
        $standard_wait_duration = round($ETA[0]->duration->value/30);
        $pickup_time =date('Y-m-d H:i:s');
        $adminCharges = $this->get_cityname($pickup_latitude,$pickup_longitude,$pickup_time,$ride_id = '');
        $service_chg = $adminCharges->service_charge;

        //distance calculation in miles
        $travel_distance = $this->distance_calculate($pickup_latitude, $pickup_longitude, $destination_latitude, $destination_longitude, "M");

        $cost_per_mile = $adminCharges->cost_per_mile;
        $waiting_charge_min = $adminCharges->per_min_charge;
        $min_charge = $adminCharges->min_charge;
        //fair calculation
        $fair = ($travel_distance * $cost_per_mile) + ($waiting_charge_min * $standard_wait_duration) + $service_chg;
        $fair = round($fair);
        if($fair <$min_charge){

            $fair = $min_charge;
        }
        $fair = round($fair,2);
        $est_fair = ['amount' => $fair, 'currency' => '$'];


        return response()->json(['status' => 1, 'message' => 'success', 'data' => $est_fair]);
    }
    /**
     ***********************************************************
     *  Function Name : pickup_request
     *  Functionality : In this passenger request for ride , then searching nearest driver to the user , if he missed the request and go to the next driver and so on upto three driver .
     *  @access         public
     *  @param        : pickup lat,long and destination lat,long, user_id ,car id, payment id.
     *  @return       : it send request to the driver with status 1 and error message 'No found driver in this area' with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function pickup_request(Request $request)
    {

        /**
         *Removing the drivers added before 1 minute in session array
         *
         */

        session_id("driverid");
        session_start();
        if(!empty($_SESSION['allocated_driver'] )){
            foreach($_SESSION['allocated_driver'] as $key=>$value){
                if((time() - $value['TimeStamp']) >=20){
                    // echo "search key value: ";print_r(array_search(@$value['TimeStamp'],$_SESSION['allocated_driver'][$key]['TimeStamp']));
                    // echo "<hr>";
                    if ((@$value['TimeStamp']==$_SESSION['allocated_driver'][$key]['TimeStamp']) && (@$value['drId']==$_SESSION['allocated_driver'][$key]['drId'])) {
                        unset($_SESSION['allocated_driver'][$key]);
                    }
                }/* else{
					
					echo "with in 60";
				} */
            }
        }
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $check_payment = DB::table('dn_rides')->select('id')->where(array('passenger_id' => $user_id,'bill_generated' => '1'))->whereIn('payment_status', array(0, 2))->orderBy('id', 'desc')->get();
        if(!empty($check_payment)){
            $bill_payment  = DB::table('ride_billing_info')->select('total_charges')->where('ride_id', $check_payment[0]->id)->first();
            $bill_payment = $bill_payment->total_charges;
            return response()->json(['status' => 5, 'message' => 'Previous Ride payment was failed due to some reasons, Please Re-Pay by changing your payment method and tap Paybill button to enjoy future rides.','amount' => $bill_payment,'ride_id'=>$check_payment[0]->id]);

        }
        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        $pickup_latitude = $request->input('pickup_latitude');
        if ($data = $this->check_empty($pickup_latitude, 'Please provide pickup_latitude'))
            return response()->json($data, 200);
        $pickup_longitude = $request->input('pickup_longitude');
        if ($data = $this->check_empty($pickup_longitude, 'Please provide pickup_longitude'))
            return response()->json($data, 200);
        $destination_latitude = $request->input('destination_latitude');
        if ($data = $this->check_empty($destination_latitude, 'Please provide destination'))
            return response()->json($data, 200);
        $destination_longitude = $request->input('destination_longitude');
        if ($data = $this->check_empty($destination_longitude, 'Please provide destination'))
            return response()->json($data, 200);

        $city_name = $request->input('city_name');
        if ($data = $this->check_empty($city_name, 'Please provide city_name'))
            $city_name = '';

        $car_id = $request->input('car_id');
        if ($data = $this->check_empty($car_id, 'Please provide car name'))
            return response()->json($data, 200);

        $payment_id = $request->input('payment_id');
        if ($data = $this->check_empty($payment_id, 'You have no payment method'))
            return response()->json($data, 200);

        $ride_id = $request->input('ride_id');

        $payment_detail = DB::table('dn_payment_accounts')->select('account_type', 'card_type', 'account_email', 'image_url', 'masked_number')->where('id', $payment_id)->first();
        if (empty($payment_detail)) {
            return response()->json(['status' => 0, 'message' => 'No payment method found']);
        }

        $car_detail = DB::table('dn_user_cars')->select('make', 'number', 'transmission', 'model')->where('id', $car_id)->first();
        if (empty($car_detail)) {
            return response()->json(['status' => 0, 'message' => 'No car found']);
        }
        $time=time()-15;
        $from = date('Y-m-d H:i:s',$time);
        $timeTo=time();
        $to = date('Y-m-d H:i:s',$timeTo);
        $user_ride_check=DB::table('dn_rides')
            ->select('*')
            ->where('passenger_id', $user_id)
            ->whereBetween('created_at', array($from,$to))
            ->get();
        if(!empty($user_ride_check)){
            return response()->json(['status' => 0, 'message' => 'Your last request is in Process. Please try again.']);
        }



        $transmission = $car_detail->transmission;
        $passenger_info = (array)DB::table('dn_users')->select('first_name', 'last_name', 'profile_pic', DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where('id', $user_id)->first();
        //nearby driver search
        $radius = 8;  //miles radius to search
        $const = $this->firebaseConstant();
        $DEFAULT_PATH = '/DeziNow';
        $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
        // --- reading the stored string ---
        $drivers = $firebase->get($DEFAULT_PATH . '/drivers.json?orderBy="driver_available_is_hired"&equalTo="true_0"&print=pretty');

        if ($drivers) {
            $driver_arr = (array)json_decode($drivers);

            $driver_dist = array();
            foreach ($driver_arr as $driver_id => $driver) {
                $node_count =   count((array)$driver);
                if ($node_count >= 6 && $driver_id != $user_id && ($driver->transmission == 'both' OR $driver->transmission == "$transmission") ) {
                    $driver_rating = DB::table('dn_rating')->select('rating')->where(['passenger_id' => $user_id, 'driver_id' => $driver_id, 'rate_by' => '4'])->orderBy('rating', 'ASC')->first();

                    if (!empty($driver_rating)) {
                        $driver_rating = $driver_rating->rating;
                    } else {
                        $driver_rating = 4; // at first ride assume rating above 2
                    }

                    $passanger_rating = DB::table('dn_rating')->select('rating')->where(['passenger_id' => $user_id, 'driver_id' => $driver_id, 'rate_by' => '3'])->orderBy('rating', 'ASC')->first();
                    if (!empty($passanger_rating)) {
                        $passanger_rating = $passanger_rating->rating;
                    } else {
                        $passanger_rating = 4; // at first ride assume rating above 2
                    }
                    $driver_suspended = DB::table('dn_users')->select('is_suspended')->where(['id' => $driver_id, 'is_suspended' => 0])->first();
                    $driver_revoked = DB::table('role_user')->select('role_id')->where(['user_id' => $driver_id, 'role_id' => 5])->first();
                    if ($driver_revoked == NULL && $driver_suspended->is_suspended==0 && ($passanger_rating > 2 AND $driver_rating > 2)) {
                        $distance = $this->distance_calculate($pickup_latitude, $pickup_longitude, $driver->latitude, $driver->longitude, "M");
                        if (trim($distance) != 'flag') {
                            if ($distance < $radius) {
                                $driver_dist[] = [
                                    'driver_id' => $driver_id,
                                    'latitude' => $driver->latitude,
                                    'longitude' => $driver->longitude,
                                    'driver_dist' => $distance
                                ];
                            }
                        }
                    }
                }
            }
            if (empty($driver_dist)) {

                //no driver found
                return response()->json(['status' => 4, 'message' => 'No driver found']);
            }

            if (!empty($ride_id)) {
                $unanswered_drivers = DB::table('missed_ride_request')->select('driver_id')->where(['ride_id' => $ride_id])->get();
                foreach ($unanswered_drivers as $u_driver) {
                    foreach ($driver_dist as $k => $dr_dist) {
                        if ($dr_dist['driver_id'] == $u_driver->driver_id) {
                            unset($driver_dist[$k]);
                        }
                    }
                }
                if (empty($driver_dist)) {
                    return response()->json(['status' => 4, 'message' => 'No driver found']);
                }
            }
            $driver_dist = $this->aasort($driver_dist, "driver_dist");


            //print_r($driver_dist);exit;
            /*
            Create session id for server to prevent multiple request for same driver
            */

            session_id("driverid");
            $first_near_driver = reset($driver_dist);
            session_start();

            session_id("driverid");


            $first_near_driver = reset($driver_dist);
            session_start();


            $first_near_driver = reset($driver_dist);


            foreach(@$_SESSION['allocated_driver'] as $ky=>$vl){
                if($first_near_driver['driver_id']==@$vl['drId']){
                    return response()->json(['status' => 4, 'message' => 'No driver found']);
                }
            }



            $arr=array (
                'drId'=>$first_near_driver['driver_id'],
                'TimeStamp'=>time()
            );
            $_SESSION['allocated_driver'][]=$arr;




            //calculate distance and time driver takes to reach passenger
            //function parameters: driver latlong,passenger latlong
            $ETA = $this->get_distance_time($first_near_driver['latitude'], $first_near_driver['longitude'], $pickup_latitude, $pickup_longitude);


            //insert ride information
            if(empty($ride_id)) {
                $insert_data = [
                    'driver_id' => $first_near_driver['driver_id'],
                    'passenger_id' => $user_id,
                    'driver_latitude' => $first_near_driver['latitude'],
                    'driver_longitude' => $first_near_driver['longitude'],
                    'pickup_latitude' => $pickup_latitude,
                    'pickup_longitude' => $pickup_longitude,
                    'destination_latitude' => $destination_latitude,
                    'destination_longitude' => $destination_longitude,
                    'pickup_time' => date('Y-m-d H:i:s'),
                    'car_id' => $car_id,
                    'city_name' => $city_name,
                    'payment_id' => $payment_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $ride_id = DB::table('dn_rides')->insertGetId($insert_data);
            }else{

                DB::table('dn_rides')
                    ->where(['id' => $ride_id])
                    ->update(['driver_id' => $first_near_driver['driver_id'],
                        'driver_latitude' => $first_near_driver['latitude'],
                        'driver_longitude' => $first_near_driver['longitude'],
                        'pickup_time' => date('Y-m-d H:i:s')]);
            }
            $to = $first_near_driver['driver_id'];

            DB::table('missed_ride_request')->insertGetId(['ride_id' => $ride_id, 'driver_id' => $first_near_driver['driver_id'],'request_time'=>date('Y-m-d H:i:s')]);
            //$firebase->update($DEFAULT_PATH . '/drivers/' .$to , array('driver_available_is_hired'=>'true_1','is_hired'=>1));
            //device id for gcm
            //passenger data which shown on driver end
            $passenger_data = ['first_name' => $passenger_info['first_name'], 'last_name' => $passenger_info['last_name'], 'profile_pic' => $passenger_info['profile_pic'], 'car_detail' => $car_detail, 'eta' => $ETA, 'pickup_address' => ['latitude' => $pickup_latitude, 'longitude' => $pickup_longitude], 'ride_id' => $ride_id, 'contact_number' => $passenger_info['contact_number']];
            //receiver ids array


            $check_notification = $this->sendGoogleCloudMessage('Passenger Request for ride', '1', $passenger_data, $to, $user_id, $ride_id);
            if (!$check_notification) {
                return response()->json(['status' => 0, 'message' => 'Please try again.']);
            }


            return response()->json(['status' => 1, 'message' => 'success', 'ride_id' => $ride_id, 'driver_id' => $to]);

        } else {
            //no driver found
            return response()->json(['status' => 4, 'message' => 'No driver found']);
        }

    }

    public function pickup_request_new(Request $request)
    {
        /**
         *Removing the drivers added before 1 minute in session array
         *
         */

        session_id("driverid");
        session_start();
        if(!empty($_SESSION['allocated_driver'] )){
            foreach($_SESSION['allocated_driver'] as $key=>$value){
                if((time() - $value['TimeStamp']) >=20){
                    // echo "search key value: ";print_r(array_search(@$value['TimeStamp'],$_SESSION['allocated_driver'][$key]['TimeStamp']));
                    // echo "<hr>";
                    if ((@$value['TimeStamp']==$_SESSION['allocated_driver'][$key]['TimeStamp']) && (@$value['drId']==$_SESSION['allocated_driver'][$key]['drId'])) {
                        unset($_SESSION['allocated_driver'][$key]);
                    }
                }/* else{

					echo "with in 60";
				} */
            }
        }
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $check_payment = DB::table('dn_rides')->select('id')->where(array('passenger_id' => $user_id,'bill_generated' => '1'))->whereIn('payment_status', array(0, 2))->orderBy('id', 'desc')->get();
        if(!empty($check_payment)){
            $bill_payment  = DB::table('ride_billing_info')->select('total_charges')->where('ride_id', $check_payment[0]->id)->first();

            $bill_payment = $bill_payment->total_charges;
            return response()->json(['status' => 5, 'message' => 'Previous Ride payment was failed due to some reasons, Please Re-Pay by changing your payment method and tap Paybill button to enjoy future rides.','amount' => $bill_payment,'ride_id'=>$check_payment[0]->id]);

        }
        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        $pickup_latitude = $request->input('pickup_latitude');
        if ($data = $this->check_empty($pickup_latitude, 'Please provide pickup_latitude'))
            return response()->json($data, 200);
        $pickup_longitude = $request->input('pickup_longitude');
        if ($data = $this->check_empty($pickup_longitude, 'Please provide pickup_longitude'))
            return response()->json($data, 200);
        $destination_latitude = $request->input('destination_latitude');
        if ($data = $this->check_empty($destination_latitude, 'Please provide destination'))
            return response()->json($data, 200);
        $destination_longitude = $request->input('destination_longitude');
        if ($data = $this->check_empty($destination_longitude, 'Please provide destination'))
            return response()->json($data, 200);

        $ride_id = $request->input('ride_id');

        $payment_detail = DB::table('dn_payment_accounts')->select('id','account_type', 'card_type', 'account_email', 'image_url', 'masked_number')->where(['user_id'=>$user_id,'is_delete'=>'0','is_default'=>'1'])->first();
        if (empty($payment_detail)) {
            return response()->json(['status' => 0, 'message' => 'No payment method found']);
        }

        $car_detail = DB::table('dn_user_cars')->select('id','make', 'number', 'transmission', 'model')->where(['user_id'=>$user_id,'is_delete'=>'0','is_default'=>'1'])->first();
        if (empty($car_detail)) {
            return response()->json(['status' => 0, 'message' => 'No car found']);
        }
        $time=time()-15;
        $from = date('Y-m-d H:i:s',$time);
        $timeTo=time();
        $to = date('Y-m-d H:i:s',$timeTo);
        $user_ride_check=DB::table('dn_rides')
            ->select('*')
            ->where('passenger_id', $user_id)
            ->whereBetween('created_at', array($from,$to))
            ->get();
        if(!empty($user_ride_check)){
            return response()->json(['status' => 0, 'message' => 'Your last request is in Process. Please try again.']);
        }



        $transmission = $car_detail->transmission;
        $passenger_info = (array)DB::table('dn_users')->select('first_name', 'last_name', 'profile_pic', DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where('id', $user_id)->first();
        //nearby driver search
        $radius = 8;  //miles radius to search
        $const = $this->firebaseConstant();
        $DEFAULT_PATH = '/DeziNow';
        $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
        // --- reading the stored string ---
        $drivers = $request->input('drivers_info');

        if ($drivers) {
            $driver_arr = (array)json_decode($drivers);
            $driver_dist = array();

            foreach ($driver_arr as $driver_id => $driver) {

                if ($driver_id != $user_id && ($driver->driver_transmission == 'both' OR $driver->driver_transmission == "$transmission") ) {
                    $driver_rating = DB::table('dn_rating')->select('rating')->where(['passenger_id' => $user_id, 'driver_id' => $driver->driver_id, 'rate_by' => '4'])->orderBy('rating', 'ASC')->first();

                    if (!empty($driver_rating)) {
                        $driver_rating = $driver_rating->rating;
                    } else {
                        $driver_rating = 4; // at first ride assume rating above 2
                    }

                    $passanger_rating = DB::table('dn_rating')->select('rating')->where(['passenger_id' => $user_id, 'driver_id' => $driver->driver_id, 'rate_by' => '3'])->orderBy('rating', 'ASC')->first();
                    if (!empty($passanger_rating)) {
                        $passanger_rating = $passanger_rating->rating;
                    } else {
                        $passanger_rating = 4; // at first ride assume rating above 2
                    }
                    $driver_suspended = DB::table('dn_users')->select('is_suspended')->where(['id' => $driver->driver_id, 'is_suspended' => 0])->first();
                    $driver_revoked = DB::table('role_user')->select('role_id')->where(['user_id' => $driver->driver_id, 'role_id' => 5])->first();
                    $const = $this->firebaseConstant();
                    $DEFAULT_PATH = '/DeziNow';
                    $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
                    $driver_available_is_hired = str_replace('"', "", $firebase->get($DEFAULT_PATH . "/drivers/" . $driver->driver_id . '/driver_available_is_hired'));

                    if ($driver_available_is_hired == "true_0" && $driver_revoked == NULL && $driver_suspended->is_suspended==0 && ($passanger_rating > 2 AND $driver_rating > 2)) {

                        $driver_dist[] = [
                            'driver_id' => $driver->driver_id,
                            'latitude' => $driver->driver_latitude,
                            'longitude' => $driver->driver_longitude,
                        ];
                    }
                }
            }

            if (empty($driver_dist)) {

                //no driver found
                return response()->json(['status' => 4, 'message' => 'No driver found']);
            }

            if (!empty($ride_id)) {
                $unanswered_drivers = DB::table('missed_ride_request')->select('driver_id')->where(['ride_id' => $ride_id])->get();
                foreach ($unanswered_drivers as $u_driver) {
                    foreach ($driver_dist as $k => $dr_dist) {
                        if ($dr_dist['driver_id'] == $u_driver->driver_id) {
                            unset($driver_dist[$k]);
                        }
                    }
                }
                if (empty($driver_dist)) {
                    return response()->json(['status' => 4, 'message' => 'No driver found']);
                }
            }
            foreach(@$_SESSION['allocated_driver'] as $ky=>$vl){
                if($driver_dist[0]['driver_id']==@$vl['drId']){
                    return response()->json(['status' => 4, 'message' => 'No driver found']);

                }
            }
            $arr=array (
                'drId'=>$driver_dist[0]['driver_id'],
                'TimeStamp'=>time()
            );
            $_SESSION['allocated_driver'][]=$arr;
            //insert ride information
            if(empty($ride_id)) {
                $insert_data = [
                    'driver_id' => $driver_dist[0]['driver_id'],
                    'passenger_id' => $user_id,
                    'driver_latitude' => $driver_dist[0]['latitude'],
                    'driver_longitude' => $driver_dist[0]['longitude'],
                    'pickup_latitude' => $pickup_latitude,
                    'pickup_longitude' => $pickup_longitude,
                    'destination_latitude' => $destination_latitude,
                    'destination_longitude' => $destination_longitude,
                    'pickup_time' => date('Y-m-d H:i:s'),
                    'car_id' => $car_detail->id,
                    'payment_id' => $payment_detail->id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $ride_id = DB::table('dn_rides')->insertGetId($insert_data);
            }else{

                DB::table('dn_rides')
                    ->where(['id' => $ride_id])
                    ->update(['driver_id' => $driver_dist[0]['driver_id'],
                        'driver_latitude' => $driver_dist[0]['latitude'],
                        'driver_longitude' => $driver_dist[0]['longitude'],
                        'pickup_time' => date('Y-m-d H:i:s')]);
            }
            $to = $driver_dist[0]['driver_id'];
            DB::table('missed_ride_request')->insertGetId(['ride_id' => $ride_id, 'driver_id' => $driver_dist[0]['driver_id'],'request_time'=>date('Y-m-d H:i:s')]);
            $firebase->update($DEFAULT_PATH . '/drivers/' .$to , array('driver_available_is_hired'=>'true_1','is_hired'=>1));
            //device id for gcm
            //passenger data which shown on driver end
            $passenger_data = ['first_name' => $passenger_info['first_name'], 'last_name' => $passenger_info['last_name'], 'profile_pic' => $passenger_info['profile_pic'], 'car_detail' => $car_detail, 'pickup_address' => ['latitude' => $pickup_latitude, 'longitude' => $pickup_longitude], 'ride_id' => $ride_id, 'contact_number' => $passenger_info['contact_number']];
            //receiver ids array


            $check_notification = $this->sendGoogleCloudMessage('Passenger Request for ride', '1', $passenger_data, $to, $user_id, $ride_id);
            if (!$check_notification) {
                return response()->json(['status' => 0, 'message' => 'Please try again.']);
            }


            return response()->json(['status' => 1, 'message' => 'success', 'ride_id' => $ride_id, 'driver_id' => $to]);

        } else {
            //no driver found
            return response()->json(['status' => 4, 'message' => 'No driver found']);
        }

    }



    public function aasort (&$array, $key) {
        $sorter=array();
        $ret=array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii]=$va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii]=$array[$ii];
        }
        return $array=$ret;
    }

    function dist_sort($a, $b) {
        return $b['driver_dist'] - $a['driver_dist'];
    }
    /**
     ***********************************************************
     *  Function Name : one_click_request
     *  Functionality : In this based on user_id return default car, favorite place and default payment detail
     *  @access         public
     *  @param        :user_id.
     *  @return       : it send all default infor of user with status 1 and error message with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function one_click_request(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }

        $palace_detail = DB::table('dn_favorite_places')->where(['user_id' => $user_id, 'is_default' => '1'])->first();
        if(!$palace_detail){
            return response()->json(['status' => 0, 'message' => 'You have no favorite place']);
        }

        $pay_detail  = DB::table('dn_payment_accounts')->select('id', 'account_type' , 'image_url', 'is_default', 'account_email', 'card_type','masked_number', 'card_last_4', 'expired','is_delete' )->where(['user_id' => $user_id , 'is_default' => '1', 'is_delete' => '0'])->first();
        if(!$pay_detail){
            return response()->json(['status' => 0, 'message' => 'You have no payment method']);
        }

        $car_detail = DB::table('dn_user_cars')->select('id','make', 'number', 'transmission', 'model')->where(['user_id' => $user_id, 'is_default' => '1','is_delete' => '0'])->first();
        if(!$car_detail){
            return response()->json(['status' => 0, 'message' => 'You have no car']);
        }


        $default_data=[
            'payment_method' => $pay_detail,
            'car' =>$car_detail,
            'palace_detail' => $palace_detail
        ];


        return response()->json(['status' => 1, 'message' => 'success', 'default_data' => $default_data]);

    }
    /**
     ***********************************************************
     *  Function Name : change_mode
     *  Functionality : In this user change the mode from passenger to driver nad vice - versa.
     *  @access         public
     *  @param        : user_id, mode.
     *  @return       : it change the mode of user with status 1 and error message with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function change_mode(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $mode = $request->input('mode');

        $userData = DB::table('dn_users')->select('id','is_driver_approved','become_driver_request','is_suspended')->where('id', $user_id)->first();

        if ($mode == 'driver') {
            //print_r($userData);exit;
            if ($user = $this->is_revoked($user_id)) {
                return response()->json($user);
            }
            //logged user log
            if($userData->is_driver_approved){
                DB::table('dn_driver_logs')->insertGetId(['user_id' => $user_id, 'login_time' => date('Y-m-d H:i:s')]);
                DB::table('dn_user_logs')->insertGetId(['user_id' => $user_id, 'login_time' => date('Y-m-d H:i:s'), 'user_mode' => 'driver']);
            }
            // $driver_data= DB::table('dn_users_data')->select('transmission','referral_code','navigation_system')->where('user_id', $user_id)->first();
            $driver_data = DB::table('dn_users_data')
                ->select('dn_users_data.transmission', 'dn_users_data.referral_code', 'dn_users_data.navigation_system', 'dn_driver_requests.licence_expiration','dn_driver_requests.insurance_expiration')
                ->leftJoin('dn_driver_requests', 'dn_users_data.user_id', '=', 'dn_driver_requests.user_id')
                ->where(['dn_users_data.user_id' => $user_id])
                ->first();
            if(empty($driver_data)){
                $driver_data = (object) array();
            }
            $driver_data->become_driver_request = $userData->become_driver_request;
            $driver_data->is_driver_approved = $userData->is_driver_approved;
            if($userData->is_driver_approved == '0'){

                return response()->json(['status' => 0, 'message' => 'You are not approved yet!']);
            }
            if($userData->is_driver_approved == 2){

                return response()->json(['status' => 0, 'message' => 'You are rejected by admin']);
            }
            if($userData->is_suspended == 1){

                return response()->json(['status' => 0, 'message' => 'Your driving or insurance license has been expired!']);
            }


        }
        if ($mode == 'passenger') {

            //delete user from firebase
            if($userData->is_driver_approved){

                $const = $this->firebaseConstant();
                $DEFAULT_PATH = '/DeziNow';
                $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
                // --- reading the stored string ---
                $deleted = $firebase->delete($DEFAULT_PATH . "/drivers/$user_id");
                $firebase->delete($DEFAULT_PATH . "/drivers_location/$user_id");
                $driver_data = (object) array();
            }

            //logged user log update
            DB::table('dn_user_logs')
                ->where(['user_id' => $user_id, 'user_mode' => 'driver'])
                ->whereNull('logout_time')
                ->orderBy('created_at', 'desc')
                ->take(1)
                ->update(['logout_time' => date('Y-m-d H:i:s')]);
            DB::table('dn_driver_logs')
                ->where(['user_id' => $user_id]) ->whereNull('logout_time') ->orderBy('id', 'desc')->take(1)->update(['logout_time' => date('Y-m-d H:i:s')]);
            $driver_data = null;

        }

        return response()->json(['status' => 1, 'message' => 'success','driver_data'=>$driver_data]);
    }
    /**
     ***********************************************************
     *  Function Name : toggle_profile
     *  Functionality : in toggle_profile driver toggle profile active - inactive.
     *  @access         public
     *  @param        : user_id, active.
     *  @return       : it change the mode active to inactive and vice-versa with status 1 and error message with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function toggle_profile(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $active = $request->input('active');

        if ($active == '1') {
            //disable driver availability
            DB::table('dn_driver_logs')->insertGetId(['user_id' => $user_id, 'login_time' => date('Y-m-d H:i:s')]);

        }
        if ($active == '0') {
            //disable driver availability
            DB::table('dn_driver_logs')
                ->where(['user_id' => $user_id]) ->whereNull('logout_time') ->orderBy('id', 'desc')->take(1)->update(['logout_time' => date('Y-m-d H:i:s')]);

        }
        return response()->json(['status' => 1, 'message' => 'success']);

    }
    /**
     ***********************************************************
     *  Function Name : check_driver_status
     *  Functionality : check the driver is approved or not.
     *  @access         public
     *  @param        : user_id, active.
     *  @return       : give if user approved it gives message "approved" otherwise give message "You are not approved by driver";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function check_driver_status(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $userDriverCheck = DB::table('dn_users')->select(array('id', 'become_driver_request', 'is_driver_approved'))->where('id', $user_id)->first();
        if (!$userDriverCheck->become_driver_request) {
            return response()->json(['status' => 4, 'message' => 'Fill become a driver form']);
        }
        if (!$userDriverCheck->is_driver_approved) {
            return response()->json(['status' => 5, 'message' => 'You are not approved by driver']);
        }
        return response()->json(['status' => 1, 'message' => 'approved']);

    }
    /**
     ***********************************************************
     *  Function Name : transport_mode
     *  Functionality : if distance if greater than 2 miles then we asks driver for transport mode , it two types public or uber.
     *  @access         public
     *  @param        : ride_id, transport_mode.
     *  @return       : it update the transport mode into the dn_rides table in database if ride not fount it gives the error message with status 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function transport_mode(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $transport_mode = $request->input('transport_mode');
        if ($data = $this->check_empty($transport_mode, 'Please provide transport_mode'))
            return response()->json($data, 200);

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);
        $ride_data = DB::table('dn_rides')->where('id', $ride_id)->first();
        if (!empty($ride_data)) {
            DB::table('dn_rides')->where(['id' => $ride_id])->update([ 'transport_mode' => $transport_mode]);
            return response()->json(['status' => 1, 'message' => '']);
        } else {
            return response()->json(['status' => 0, 'message' => 'The Ride you are requesting is not active!']);
        }

    }
    /**
     ***********************************************************
     *  Function Name : accept_ride
     *  Functionality : driver accept the ride requesting from the passenger.
     *  @access         public
     *  @param        : user_id,driver lat,long,ride_id,transport_mode.
     *  @return       : it accept successfully gives status 1 otherwise gives status 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function accept_ride(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $driver_latitude = $request->input('driver_latitude');
        if ($data = $this->check_empty($driver_latitude, 'Please provide driver_latitude'))
            return response()->json($data, 200);
        $driver_longitude = $request->input('driver_longitude');
        if ($data = $this->check_empty($driver_longitude, 'Please provide driver_longitude'))
            return response()->json($data, 200);
        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $transport_mode = $request->input('transport_mode');
        //if ($data = $this->check_empty($transport_mode, 'Please provide transport_mode'))
        //  return response()->json($data, 200);

        $rating  = $this->rating($user_id);
        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $now_time =  strtotime(date('Y-m-d H:i:s'));

        //check another user working on ride
        $ride_data = DB::table('dn_rides')->where('id', $ride_id)->first();
        if (!empty($ride_data)) {
            //check ride status
            $request_time = DB::table('missed_ride_request')->where(['driver_id' => $user_id, 'ride_id' => $ride_id])->first();
            if(!empty($request_time)) {
                $pickup_time = strtotime($request_time->request_time) + 20;
                if ($pickup_time < $now_time) {
                    return response()->json(['status' => 0, 'message' => 'You got late.Request time has been expired!']);
                }
            }
            if ($ride_data->status == 5) {
                return response()->json(['status' => 0, 'message' => 'Ride request cancelled by passenger!']);

            }
            if ($ride_data->status == 1 || $ride_data->status == 2 || $ride_data->status == 3) {
                return response()->json(['status' => 0, 'message' => 'You got late. Another driver started working on it']);

            }
        } else {
            return response()->json(['status' => 0, 'message' => 'The Ride you are requesting is not active!']);
        }

        $driver_data = (array)DB::table('dn_rides')
            ->select('pickup_latitude', 'pickup_longitude','destination_latitude', 'destination_longitude', 'first_name', 'last_name', 'email', DB::raw('concat( country_phone_code, "", contact_number) as contact_number'), 'driver_id', 'passenger_id','dn_rides.id as ride_id')
            ->join('dn_users', 'dn_users.id', '=', 'dn_rides.driver_id')
            ->where(['dn_rides.id' => $ride_id, 'dn_rides.driver_id' => $user_id])
            ->first();

        $driver_profile_pic =DB::table('dn_users_data')->where(['user_id' => $user_id])->pluck('driver_profile_pic');
        if($driver_profile_pic){
            $driver_data['profile_pic'] = $driver_profile_pic;
        }else{
            $driver_data['profile_pic'] = "";
        }
        //current position of driver
        DB::table('dn_rides') ->where(['id' => $ride_id])->update([
            'driver_latitude' => $driver_latitude,
            'driver_longitude' => $driver_longitude,
            'transport_mode' => $transport_mode,
            'driver_id' => $user_id,
            'status' => '1', // ride in proccess

        ]);
        $ETA = $this->get_distance_time($driver_data['pickup_latitude'], $driver_data['pickup_longitude'], $driver_latitude, $driver_longitude);

        //driver current position for passenger
        $driver_data['latitude'] = $driver_latitude;
        $driver_data['longitude'] = $driver_longitude;
        $driver_data['eta'] = $ETA;
        $driver_data['rating'] = $rating;

        //ride confirmation notification to user
        $to = $driver_data['passenger_id'];

        //set ride data on firebase
        $const = $this->firebaseConstant();
        $DEFAULT_PATH = '/DeziNow';
        $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);

        $user_info = [
            'pickup_latitude' => $driver_data['pickup_latitude'], 'pickup_longitude' => $driver_data['pickup_longitude'],
            'destination_latitude' => $driver_data['destination_latitude'], 'destination_longitude' => $driver_data['destination_longitude'],
            'dist_drivertopick'=>'0','dist_picktodest'=>'0', 'status' => '0', 'ride_message'=>'Driver Coming to you'
        ];

        $firebase->set($DEFAULT_PATH . '/rides/' .$ride_id , $user_info);

        unset($driver_data['pickup_latitude']);
        unset($driver_data['pickup_longitude']);

        $check_notification = $this->sendGoogleCloudMessage('Driver Coming to you', '2', $driver_data, $to, $user_id, $ride_id);
        if ($check_notification) {
            //delete record from missed_ride_request table
            DB::table('missed_ride_request')->where(['driver_id' => $user_id, 'ride_id' => $ride_id])->delete();
            $data = array('passenger_id'=>$to);
            return response()->json(['status' => 1, 'message' => 'approved','data'=>$to]);
        } else {
            return response()->json(['status' => 0, 'message' => 'Please try again']);
        }

    }
    /**
     ***********************************************************
     *  Function Name : arrived_at_pickup
     *  Functionality : it update the datetime and arrived status in dn_rides table in database.
     *  @access         public
     *  @param        : ride_id.
     *  @return       : if update successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function arrived_at_pickup(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);
        $ride_data = DB::table('dn_rides')->where('id', $ride_id)->first();
        if (!empty($ride_data)) {
            DB::table('dn_rides')->where(['id' => $ride_id])->update(array('arrived_status' => 1, 'arrived_time' => date('Y-m-d H:i:s')));
            return response()->json(['status' => 1]);
        }else{

            return response()->json(['status' => 0, 'message' => 'The Ride you are requesting is not active!']);
        }
    }
    /**
     ***********************************************************
     *  Function Name : ride_status
     *  Functionality : it update the datetime and arrived status in dn_rides table in database.
     *  @access         public
     *  @param        : ride_id.
     *  @return       : if update successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function ride_status(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide driver_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $ride_status = $request->input('ride_status');
        if ($data = $this->check_empty($ride_status, 'Please provide ride_id'))
            return response()->json($data, 200);

        $issue_cat = $request->input('issue_cat');
        $issue_query = $request->input('issue_query');
        $issue_text = $request->input('issue_text');

        //get ride info
        $ride_data = DB::table('dn_rides')->where('id', $ride_id)->first();
        if (!empty($ride_data)) {
            $passenger_data  = (array)DB::table('dn_users')->select('first_name', 'last_name', 'profile_pic')->where('id', $ride_data->passenger_id)->first();
            $to = $ride_data->passenger_id;

            //check ride status(status flag check in ride table comments)
            if ($ride_data->status == 1 || $ride_data->status == 2 || $ride_data->status == 3) {
                $data = ['status' => 0, 'message' => 'You got late. Another driver start work on it'];
            } elseif ($ride_status == 1) {
                //start ride
                DB::table('dn_rides')
                    ->where(['id' => $ride_id])
                    ->update(array('status' => 1));

                $check_notification = $this->sendGoogleCloudMessage('ride_start', '3', $passenger_data, $to, $user_id, $ride_id);

                $data = ['status' => 1, 'message' => 'Ride start'];
            } elseif ($ride_status == 2) {
                //complete ride
                DB::table('dn_rides')
                    ->where(['id' => $ride_id])
                    ->update(array('status' => 2));

                //referral status update if user's first ride(pending module)

                $check_notification = $this->sendGoogleCloudMessage('ride_complete', '4', $passenger_data, $to, $user_id, $ride_id);

                $data = ['status' => 1, 'message' => 'Ride complete'];
            } elseif ($ride_status == 3) {
                //cancel ride
                DB::table('dn_rides')
                    ->where(['id' => $ride_id])
                    ->update(array('status' => 3));
                $check_notification = $this->sendGoogleCloudMessage('ride_cancel', '5', $passenger_data, $to, $user_id, $ride_id);
                $data = ['status' => 1, 'message' => 'Ride cancel'];
            } else {
                $data = ['status' => 0, 'message' => 'provide correct ride status'];
            }
        } else {
            $data = ['status' => 0, 'message' => 'The Ride you are requesting is not active'];
        }


        return response()->json($data);

    }
    /**
     ***********************************************************
     *  Function Name : review
     *  Functionality : gives the rating driver to passenger and vice-versa.
     *  @access         public
     *  @param        : driver_id,passenger_id,review_by,review_text,rate,ride_id.
     *  @return       : if review successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function review(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $driver_id = $request->input('driver_id');
        if ($data = $this->check_empty($driver_id, 'Please provide driver_id'))
            return response()->json($data, 200);

        $passenger_id = $request->input('passenger_id');
        if ($data = $this->check_empty($passenger_id, 'Please provide passenger_id'))
            return response()->json($data, 200);

        $review_by = $request->input('review_by');
        if ($data = $this->check_empty($review_by, 'Please provide review_by'))
            return response()->json($data, 200);

        $review_text = $request->input('review_text');
        if ($data = $this->check_empty($review_text, 'Please provide review_text'))
            return response()->json($data, 200);

        $rate = $request->input('rate');
        if ($data = $this->check_empty($rate, 'Please provide rate'))
            return response()->json($data, 200);

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);


        //check blocked user
        if ($user = $this->is_blocked($driver_id)) {
            return response()->json($user);
        }
        //check blocked user
        if ($user = $this->is_blocked($passenger_id)) {
            return response()->json($user);
        }

        //get ride info
        $ride_data = DB::table('dn_rides')->where('ride_id', $ride_id)->first();
        if (!empty($ride_data)) {

            if ($review_by == 'passenger') {
                $device_tokens = DB::table('dn_device_tokens')->where(['user_id' => $driver_id])->pluck('registration_token');
            }
            if ($review_by == 'driver') {
                $device_tokens = DB::table('dn_device_tokens')->where(['user_id' => $passenger_id])->pluck('registration_token');
            }
            $insert = [
                'rate_by' => $this->get_role_id($review_by),
                'driver_id' => $driver_id,
                'passenger_id' => $passenger_id,
                'ride_id' => $ride_id,
                'rating' => $rate,
                'review_text' => $review_text
            ];

            $review_id = DB::table('dn_rate')->insertGetId($insert);
            if ($review_id) {
                $data = ['status' => 1, 'message' => 'Rate successfully'];
            } else {
                $data = ['status' => 0, 'message' => 'Error to rate'];
            }

        } else {
            $data = ['status' => 0, 'message' => 'The Ride you are requesting is not active'];
        }

        return response()->json($data);

    }
    /**
     ***********************************************************
     *  Function Name : add_update_car
     *  Functionality : add and the user car.
     *  @access         public
     *  @param        : user_id,make,transmission,number,model,year,car_id.
     *  @return       : if add successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function add_update_car(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $make = $request->input('make');
        if ($data = $this->check_empty($make, 'Please provide make'))
            return response()->json($data, 200);
        $transmission = $request->input('transmission');
        if ($data = $this->check_empty($transmission, 'Please provide transmission'))
            return response()->json($data, 200);
        $number = $request->input('number');
        /*if ($data = $this->check_empty($number, 'Please provide number'))
            return response()->json($data, 200);*/
        $model = $request->input('model');
        if ($data = $this->check_empty($model, 'Please provide model'))
            return response()->json($data, 200);
        $year = $request->input('year');
        if ($data = $this->check_empty($year, 'Please provide year'))
            return response()->json($data, 200);

        $car_id = $request->input('car_id');

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        $car_data = [
            'user_id' => $user_id,
            'make' => $make,
            'transmission' => $transmission,
            'number' => $number,
            'model' => $model,
            'year' => $year,
        ];
        //add car
        if (!$car_id) {
            DB::table('dn_user_cars')
                ->where(['user_id' => $user_id, 'is_default' => 1])
                ->update(['is_default' => 0]);
            $car_data['is_default'] = 1;
            //make car default if no car
            /*$is_car = DB::table('dn_user_cars')->select('id')->where(['user_id' => $user_id, 'is_delete' => '0'])->first();
            if(!$is_car){
                $car_data['is_default'] = 1;
            }*/

            $car_id = DB::table('dn_user_cars')->insertGetId($car_data);
            if ($car_id) {
                //all cars object
                $cars_obj = DB::table('dn_user_cars')->where(['user_id' => $user_id, 'is_delete' => '0'])->get();

                $data = ['status' => 1, 'message' => 'Car add successfully', 'cars' =>$cars_obj];
            } else {
                $data = ['status' => 0, 'message' => 'Please try again'];
            }

        } //update car
        else {
            DB::table('dn_user_cars')
                ->where(['user_id' => $user_id, 'id' => $car_id])
                ->update($car_data);

            $data = ['status' => 1, 'message' => 'Car updated successfully'];
        }


        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : default_car
     *  Functionality : make the car default added by the user.
     *  @access         public
     *  @param        : user_id,car_id.
     *  @return       : if made default_car successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function default_car(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $car_id = $request->input('car_id');

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        DB::table('dn_user_cars')
            ->where(['user_id' => $user_id, 'is_default' => 1])
            ->update(['is_default' => 0]);

        DB::table('dn_user_cars')
            ->where(['user_id' => $user_id, 'id' => $car_id])
            ->update(['is_default' => 1]);

        $data = ['status' => 1, 'message' => 'Car made default'];

        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : get_cars
     *  Functionality : get all the cars of user.
     *  @access         public
     *  @param        : user_id.
     *  @return       : if made default_car successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function get_cars(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);


        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }

        $cars_obj = DB::table('dn_user_cars')->where(['user_id' => $user_id, 'is_delete' => '0'])->get();

        if (count($cars_obj)) {
            return response()->json(['status' => 1, 'cars' => $cars_obj]);
        } else {
            return response()->json(['status' => 0, 'message' => 'No car found']);
        }

    }
    /**
     ***********************************************************
     *  Function Name : delete_car
     *  Functionality : delete the user car.
     *  @access         public
     *  @param        : user_id,car_id.
     *  @return       : if delete successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function delete_car(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $car_id = $request->input('car_id');
        if ($data = $this->check_empty($car_id, 'Please provide car_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }

        //disable car
        DB::table('dn_user_cars')
            ->where(['user_id' => $user_id, 'id' => $car_id])
            ->update(array('is_delete' => 1));

        return response()->json(['status' => 1, 'message' => 'Car deleted successfully']);
    }
    /**
     ***********************************************************
     *  Function Name : add_update_places
     *  Functionality : add or update the places of the user.
     *  @access         public
     *  @param        : user_id,place_name,address,city,state,zip,latitude,longitude,address_id.
     *  @return       : if add place successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function add_update_places(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $place_name = $request->input('place_name');
        if ($data = $this->check_empty($place_name, 'Please provide place_name'))
            return response()->json($data, 200);
        trim($place_name);
        $address = $request->input('address');
        if ($data = $this->check_empty($address, 'Please provide address'))
            return response()->json($data, 200);

        $city = $request->input('city');

        $state = $request->input('state');

        $zip = $request->input('zip');

        //$address_id = $request->input('address_id');

        $latitude = $request->input('latitude');

        $longitude = $request->input('longitude');

        $address_id = $request->input('address_id');

        $updated_at = date('Y-m-d H:i:s');


        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }

        $address_data = [
            'user_id' => $user_id,
            'place_name' => $place_name,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'updated_at' => $updated_at

        ];
        //add address
        if (!$address_id) {
            //make place default if no place
            $is_place = DB::table('dn_favorite_places')->where(['user_id' => $user_id])->pluck('id');
            if(!$is_place){
                $address_data['is_default'] = 1;

            }
            //var_dump($address_data); die;
            $address_id = DB::table('dn_favorite_places')->insertGetId($address_data);
            if ($address_id) {
                $data = ['status' => 1, 'message' => 'Address added successfully'];
            } else {
                $data = ['status' => 0, 'message' => 'Please try again'];
            }
        } //update address
        else {
            DB::table('dn_favorite_places')
                ->where(['user_id' => $user_id, 'id' => $address_id])
                ->update($address_data);
            $data = ['status' => 1, 'message' => 'Address updated successfully'];
        }

        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : get_places
     *  Functionality : get the all places of user.
     *  @access         public
     *  @param        : user_id.
     *  @return       : if get place successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function get_places(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);


        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }

        $home = DB::table('dn_favorite_places')
            ->where(['user_id' => $user_id,'place_name' => 'home'])
            ->orderBy('id','desc')
            ->get();

        $work = DB::table('dn_favorite_places')
            ->where(['user_id' => $user_id,'place_name' => 'work'])
            ->orderBy('id','desc')
            ->get();

        $other = DB::table('dn_favorite_places')
            ->where(['user_id' => $user_id,'place_name' => 'other'])
            ->orderBy('id','desc')
            ->get();

        $places_arr['home'] = $home;
        $places_arr['work'] = $work;
        $places_arr['other'] = $other;

        if ($places_arr) {
            return response()->json(['status' => 1, 'places' => $places_arr]);
        } else {
            return response()->json(['status' => 0, 'message' => 'No place found']);
        }


    }
    /**
     ***********************************************************
     *  Function Name : delete_place
     *  Functionality : delete  place of user.
     *  @access         public
     *  @param        : user_id,address_id.
     *  @return       : if delete place successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function delete_place(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $address_id = $request->input('address_id');
        if ($data = $this->check_empty($address_id, 'Please provide address_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        //delete place
        DB::table('dn_favorite_places')
            ->where(['user_id' => $user_id, 'id' => $address_id])
            ->delete();

        return response()->json(['status' => 1, 'message' => 'Place deleted successfully']);
    }
    /**
     ***********************************************************
     *  Function Name : default_place
     *  Functionality : make the user place the default.
     *  @access         public
     *  @param        : user_id,address_id.
     *  @return       : if made place successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function default_place(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $place_id = $request->input('place_id');

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }

        DB::table('dn_favorite_places')
            ->where(['user_id' => $user_id, 'is_default' => 1])
            ->update(['is_default' => 0]);

        DB::table('dn_favorite_places')
            ->where(['user_id' => $user_id, 'id' => $place_id])
            ->update(['is_default' => 1]);

        $data = ['status' => 1, 'message' => 'Place made default'];

        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : get_queries
     *  Functionality : get all the queries raised bt the users.
     *  @access         public
     *  @param        : token.
     *  @return       : if get queries successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function get_queries(Request $request)
    {
        //get issues for cancel ride
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $cancel_ride_queies = DB::table('dn_cancel_ride_issues')->get();

        if ($cancel_ride_queies) {
            return response()->json(['status' => 1, 'issues' => $cancel_ride_queies]);
        } else {
            return response()->json(['status' => 0, 'message' => 'No Issue found']);
        }

    }
    /**
     ***********************************************************
     *  Function Name : forgot_password
     *  Functionality : get the old or new password by the user on email.
     *  @access         public
     *  @param        : token,email.
     *  @return       : if  successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function forgot_password(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $email = $request->input('email');
        if ($data = $this->check_empty($email, 'Please provide email'))
            return response()->json($data, 200);


        $user_email_check = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'email', 'is_social')->where('email', $email)->first();

        //check blocked user
        if ($user = $this->is_blocked($user_email_check->id)) {
            return response()->json($user);
        }

        if (!empty($user_email_check)) {
            if($user_email_check->is_social == '0') {

                //remember_token
                $remember_token = rand(10000, 99999);;
                //send email with token
                $is_sent = $this->sendEmailReminder($user_email_check->email, $user_email_check->first_name, $remember_token, 'Forgot password DeziNow');

                if ($is_sent) {
                    DB::table('dn_users')
                        ->where(['id' => $user_email_check->id])
                        ->update(['password_token' => $remember_token]);
                    //token sent to mobile. compare token done at mobile end
                    $data = ['status' => 1, 'message' => 'Email sent successfully', 'user_id' => $user_email_check->id, 'remember_token' => $remember_token];

                } else {

                    $data = ['status' => 0, 'message' => 'There is technical error mail doesn\t send'];
                }
            }
            else {

                $data = ['status' => 0, 'message' => 'This email lined with social login.'];
            }
        }
        else {

            $data = ['status' => 0, 'message' => 'No email found'];
        }

        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : change_password
     *  Functionality : user can change password .
     *  @access         public
     *  @param        : token,user_id,password,old_password.
     *  @return       : if password changed successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function change_password(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $password = $request->input('password');
        if ($data = $this->check_empty($password, 'Please provide password'))
            return response()->json($data, 200);


        $is_change_password = $request->input('is_change_password');

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $user_id_check = DB::table('dn_users')->select('id', 'password')->where('id', $user_id)->first();

        if (!empty($user_id_check)) {

            // for change password only
            if ($is_change_password == 1) {
                $old_password = $request->input('old_password');
                if ($data = $this->check_empty($old_password, 'Please provide old_password'))
                    return response()->json($data, 200);


                if (!Hash::check($old_password, $user_id_check->password)) {

                    return response()->json(['status' => 0, 'message' => 'Old password doesn\'t match']);
                }
            }

            $enc_pass =  Hash::make($password);

            DB::table('dn_users')
                ->where(['id' => $user_id_check->id])
                ->update(['password' => $enc_pass]);

            $data = ['status' => 1, 'message' => 'Password changed successfully'];
        } else {

            $data = ['status' => 0, 'message' => 'No user found'];
        }

        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : change_phone
     *  Functionality : user can change phone number .
     *  @access         public
     *  @param        : token,user_id,contact_number,country_phone_code.
     *  @return       : if phone changed successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function change_phone(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $contact_number = $request->input('contact_number');
        if ($data = $this->check_empty($contact_number, 'Please provide contact_number'))
            return response()->json($data, 200);
        $country_phone_code = $request->input('country_phone_code');
        if ($data = $this->check_empty($country_phone_code, 'Please provide country_phone_code'))
            return response()->json($data, 200);

        //check already registered Phone
        $userPhoneCheck = DB::table('dn_users')->where('contact_number', $contact_number)->pluck('contact_number');
        if ($userPhoneCheck) {
            return response()->json(['status' => 0, 'message' => 'User already exist with this Contact Number'], 200);
        }

        //verify User exits
        $userCheck = DB::table('dn_users')->select('id','contact_number','country_phone_code')->where(['id' => $user_id])->first();
        if(!$userCheck){
            $data = ['status' => 0, 'message' => 'User doesn\'t exist'];
        }
        elseif($userCheck->contact_number == $contact_number){
            $data = ['status' => 0, 'message' => 'User doesn\'t exist with this Contact Number'];
        }
        else{
            //send token
            $otp = rand(1000, 9999);
            $contact_code = $country_phone_code.$contact_number;
            $is_sent = $this->twileo_send($contact_code, $otp);

            $update = DB::table('dn_users')
                ->where(['id' => $user_id])
                ->update(['password_token' => $otp]);
            if ($is_sent == 1) {
                $data = ['status' => 1, 'otp' => $otp, 'message' => 'Otp sent successfully'];
            }
            else {
                $data = ['status' => 0, 'message' => 'Phone number is not valid!'];
            }

        }
        return response()->json($data, 200);

    }
    /**
     ***********************************************************
     *  Function Name : update_phone
     *  Functionality : user can update phone number .
     *  @access         public
     *  @param        : token,user_id,contact_number,country_phone_code,otp.
     *  @return       : if phone update successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function update_phone(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $contact_number = $request->input('contact_number');
        if ($data = $this->check_empty($contact_number, 'Please provide contact_number'))
            return response()->json($data, 200);
        $country_phone_code = $request->input('country_phone_code');
        if ($data = $this->check_empty($country_phone_code, 'Please provide country_phone_code'))
            return response()->json($data, 200);

        $otp = $request->input('otp');
        if ($data = $this->check_empty($otp, 'Please provide otp'))
            return response()->json($data, 200);


        $userCheck = DB::table('dn_users')->select('id','contact_number','password_token')->where(['id' => $user_id])->first();
        if(!$userCheck){
            $data = ['status' => 0, 'message' => 'User doesn\'t exist'];
        }
        elseif($userCheck->password_token != $otp){
            $data = ['status' => 0, 'message' => 'Invalid Otp. Please try again'];
        }
        else{
            $update = DB::table('dn_users')
                ->where(['id' => $user_id])
                ->update(['contact_number' => $contact_number,'country_phone_code' => $country_phone_code]);

            $data = ['status' => 1, 'message' => 'Phone number changed successfully'];

        }

        return response()->json($data, 200);

    }

    /**
     ***********************************************************
     *  Function Name : braintree_clint_token
     *  Functionality : generate the braintree token.
     *  @access         public
     *  @param        : token.
     *  @return       : if generate successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    //braintree services
    public function braintree_clint_token(Request $request){
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $clientToken = \Braintree_ClientToken::generate();
        if($clientToken){
            $data = ['status' => 1, 'message' => 'Client token generated successfully','token' => $clientToken];
        }
        else{
            $data = ['status' => 0, 'message' => 'no token found'];
        }

        return response()->json($data, 200);
    }
    /**
     ***********************************************************
     *  Function Name : braintree_nonce_save
     *  Functionality : save the braintree nonce.
     *  @access         public
     *  @param        : token,user_id,account_type,nonce_token.
     *  @return       : if generate successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function braintree_nonce_save(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $account_type = $request->input('account_type');
        if ($data = $this->check_empty($account_type, 'Please provide account_type'))
            return response()->json($data, 200);



        $nonce_token = $request->input('nonce_token');
        if ($data = $this->check_empty($nonce_token, 'Please provide nonce_token'))
            return response()->json($data, 200);

        $userCheck = DB::table('dn_users')->select('id','first_name','last_name','email','contact_number')->where(['id' => $user_id])->first();
        if ($userCheck) {

            //check user payment method
            $userPaymentCheck = DB::table('dn_payment_accounts')->where(['user_id' => $user_id, 'is_delete' => '0'])->first();
            if(!$userPaymentCheck) {
                //create user on braintree and add card
                $result = \Braintree_Customer::create([
                    'id' => $user_id,
                    'firstName' => $userCheck->first_name,
                    'lastName' => $userCheck->last_name,
                    'email' => $userCheck->email,
                    'phone' => $userCheck->contact_number,
                    'paymentMethodNonce' => $nonce_token,

                    'creditCard' => [
                        'options' => [
                            // 'failOnDuplicatePaymentMethod' => true,
                            'verifyCard' => true
                        ]
                    ]

                ]);

                if($result->success){
                    //var_dump($result);die;
                    if($account_type == 'card'){
                        $data_arr =  [
                            'user_id' => $user_id,
                            'account_type' => $account_type,
                            'card_type' => $result->customer->creditCards[0]->cardType,
                            'card_identifier' => $result->customer->creditCards[0]->uniqueNumberIdentifier,
                            'masked_number' => $result->customer->creditCards[0]->maskedNumber,
                            'payment_token' => $result->customer->creditCards[0]->token,
                            'expiration_date' => $result->customer->creditCards[0]->expirationDate,
                            'image_url' => $result->customer->creditCards[0]->imageUrl,
                            'card_last_4' => $result->customer->creditCards[0]->last4,
                            'expired' => $result->customer->creditCards[0]->expired,
                            'payroll' => $result->customer->creditCards[0]->payroll,
                            // 'is_default' => $result->customer->creditCards[0]->default,
                            'is_default' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    elseif($account_type == 'paypal'){
                        $data_arr =  [
                            'user_id' => $user_id,
                            'account_type' => $account_type,
                            'payment_token' => $result->customer->paypalAccounts[0]->token,
                            'image_url' => $result->customer->paypalAccounts[0]->imageUrl,
                            //'is_default' => $result->customer->paypalAccounts[0]->default,
                            'is_default' => 1,
                            'account_email' =>  $result->customer->paypalAccounts[0]->email,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    else{
                        $data_error = ['status' => 0, 'message' => 'Provide correct payment type.'];
                        return response()->json($data_error, 200);
                    }

                    $inserted = DB::table('dn_payment_accounts')->insertGetId($data_arr);

                    $data = ['status' => 1, 'message' => 'Payment method saved successfully'];

                }
                else{
                    $data = ['status' => 0, 'message' => 'Payment Method not saved', 'message_braintree' => 'Invalid card details.'];
                }
            }
            else{
                //add card to existing user on braintree
                $result = \Braintree_PaymentMethod::create([
                    'customerId' => $user_id,
                    'paymentMethodNonce' => $nonce_token,
                    'options' => [
//                        'failOnDuplicatePaymentMethod' =>true,
                        'verifyCard' => true
                    ]

                ]);

                if($result->success){

                    if($account_type == 'card'){

                        $data_arr =  [
                            'user_id' => $user_id,
                            'account_type' => $account_type,
                            'card_type' => $result->paymentMethod->cardType,
                            'card_identifier' => $result->paymentMethod->uniqueNumberIdentifier,
                            'masked_number' => $result->paymentMethod->maskedNumber,
                            'payment_token' => $result->paymentMethod->token,
                            'expiration_date' => $result->paymentMethod->expirationDate,
                            'image_url' => $result->paymentMethod->imageUrl,
                            'card_last_4' => $result->paymentMethod->last4,
                            'expired' => $result->paymentMethod->expired,
                            'payroll' => $result->paymentMethod->payroll,
                            //'is_default' => $result->paymentMethod->default,
                            'is_default' => 1,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    elseif($account_type == 'paypal'){

                        $data_arr =  [
                            'user_id' => $user_id,
                            'account_type' => $account_type,
                            'payment_token' => $result->paymentMethod->token,
                            'image_url' => $result->paymentMethod->imageUrl,
                            //'is_default' => $result->paymentMethod->default,
                            'is_default' => 1,
                            'account_email' =>  $result->paymentMethod->email,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    else{
                        $data_error = ['status' => 0, 'message' => 'Provide correct payment type.'];
                        return response()->json($data_error, 200);
                    }
                    DB::table('dn_payment_accounts') ->where(['user_id' => $user_id]) ->update(['is_default' => 0]);
                    $inserted = DB::table('dn_payment_accounts')->insertGetId($data_arr);

                    $data = ['status' => 1, 'message' => 'Payment method saved successfully'];

                }
                else{
                    $data = ['status' => 0, 'message' => 'Payment Method not saved', 'message_braintree' => 'Invalid card details.'];
                }
            }

        }
        else{
            $data = ['status' => 0, 'message' => 'No user found'];
        }

        return response()->json($data, 200);

    }
    /**
     ***********************************************************
     *  Function Name : get_payment_method
     *  Functionality : get the payment method of the user.
     *  @access         public
     *  @param        : token,user_id.
     *  @return       : if get successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function get_payment_method(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $userCheck = DB::table('dn_users')->select('id')->where(['id' => $user_id])->first();
        if($userCheck) {

            $userPaymentMethodsCards = DB::table('dn_payment_accounts')->select('id','user_id', 'account_type' ,'card_type','masked_number', 'image_url', 'card_last_4', 'expired', 'is_default' )->where(['user_id' => $user_id, 'account_type' => 'card', 'is_delete' => '0'])->get();

            $userPaymentMethodsPaypal = DB::table('dn_payment_accounts')->select('id','user_id', 'account_type' , 'image_url', 'is_default', 'account_email' )->where(['user_id' => $user_id, 'account_type' => 'paypal', 'is_delete' => '0'])->get();

            $userPaymentMethods['cards'] = $userPaymentMethodsCards;
            $userPaymentMethods['paypal'] = $userPaymentMethodsPaypal;
            $dezi_credit = DB::table('dn_passenger_credits')->select('credit_balance')->where(['user_id' => $user_id])->orderBy('id', 'desc')->first();
            if($dezi_credit){

                $dezi_credit = number_format((float)$dezi_credit->credit_balance, 2, '.', '');
            }else{
                $dezi_credit = 0.00;
            }

            $data = ['status' => 1, 'payment_methods' => $userPaymentMethods,'dezi_credit'=>$dezi_credit];
        }
        else{
            $data = ['status' => 0, 'message' => 'No user found'];
        }

        return response()->json($data, 200);
    }
    /**
     ***********************************************************
     *  Function Name : payment_method_delete
     *  Functionality : delete the user payment method.
     *  @access         public
     *  @param        : token,user_id,card_id.
     *  @return       : if delete successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function payment_method_delete(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $card_id = $request->input('card_id');
        if ($data = $this->check_empty($card_id, 'Please provide card_id'))
            return response()->json($data, 200);

        $userCheck = DB::table('dn_users')->select('id')->where(['id' => $user_id])->first();
        if($userCheck) {
            $card_info = DB::table('dn_payment_accounts')->select('payment_token')->where(['id' =>  $card_id, 'user_id' => $user_id])->first();
            if($card_info) {
                $card_count = DB::table('dn_payment_accounts')->select('payment_token')->where(['user_id' => $user_id ,'is_delete' => 0])->count();
                //if user have last card delete user from braintree
                if($card_count == 1){
                    //delete user
                    $result = \Braintree_Customer::delete($user_id);
                }
                else{
                    //delete card only
                    $result = \Braintree_PaymentMethod::delete($card_info->payment_token);
                }

                if($result->success) {
                    DB::table('dn_payment_accounts')
                        ->where(['id' => $card_id, 'user_id' => $user_id])
                        ->update(array('is_delete' => 1));
                    $data = ['status' => 1, 'message' => 'Payment method deleted successfully'];
                }
                else{
                    $data = ['status' => 0, 'message' => 'Error to delete payment method'];
                }
            }
            else{
                $data = ['status' => 0, 'message' => 'No payment method found'];
            }
        }
        else{

            $data = ['status' => 0, 'message' => 'No user found'];
        }

        return response()->json($data, 200);
    }
    /**
     ***********************************************************
     *  Function Name : braintree_default_payment
     *  Functionality : make the default user payment method.
     *  @access         public
     *  @param        : token,user_id,card_id.
     *  @return       : if make default successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function braintree_default_payment(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $card_id = $request->input('card_id');
        if ($data = $this->check_empty($card_id, 'Please provide card_id'))
            return response()->json($data, 200);

        $userCheck = DB::table('dn_users')->select('id')->where(['id' => $user_id])->first();
        if($userCheck) {

            $cardCheck = DB::table('dn_payment_accounts')->select('id','payment_token')->where(['id' => $card_id])->first();
            if($cardCheck){

                $result =  \Braintree_PaymentMethod::update(
                    $cardCheck->payment_token,
                    [
                        'options' => [
                            'makeDefault' => true
                        ]

                    ]);

                if($result->success) {

                    DB::table('dn_payment_accounts')
                        ->where(['user_id' => $user_id])
                        ->update(['is_default' => 0]);

                    DB::table('dn_payment_accounts')
                        ->where(['id' => $card_id, 'user_id' => $user_id])
                        ->update(['is_default' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                    $data = ['status' => 1, 'message' => 'Card made default'];
                }
                else{
                    $data = ['status' => 0, 'message' => 'Please try again'];
                }
            }
            else{
                $data = ['status' => 0, 'message' => 'No card found'];
            }
        }
        else{

            $data = ['status' => 0, 'message' => 'No user found'];
        }

        return response()->json($data, 200);
    }
    public function firebase_get_response(){

        $const = $this->firebaseConstant();
        $DEFAULT_PATH = '/DeziNow';
        $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
        // --- reading the stored string ---
        $name = $firebase->get($DEFAULT_PATH . '/Drivers');

        //
        //
        //
        // var_dump($name);
    }
    /**
     ***********************************************************
     *  Function Name : give_rating
     *  Functionality : give rating by the driver to the passenger and vice-versa.
     *  @access         public
     *  @param        : token,ride_id,rating,rate_by.
     *  @return       : if  successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function give_rating(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $rating = $request->input('rating');
        if ($data = $this->check_empty($rating, 'Please provide rating'))
            return response()->json($data, 200);

        $rate_by = $request->input('rate_by');
        if ($data = $this->check_empty($rate_by, 'Please provide rate_by'))
            return response()->json($data, 200);
        $ride_data = DB::table('dn_rides')->select('id','passenger_id','driver_id')->where(['id' => $ride_id])->first();
        if($ride_data){

            $driver_id =  $ride_data->driver_id;
            $passenger_id =  $ride_data->passenger_id;
        }else{
            return response()->json(['status' => 0, 'message' => 'Ride_id does not exist!']);
        }
        $insert_data =[
            'driver_id' => $driver_id,
            'passenger_id' => $passenger_id,
            'ride_id' => $ride_id,
            'rating' => $rating,
            'rate_by' => $rate_by
        ];

        $is_rating = DB::table('dn_rating')->insertGetId($insert_data);

        if($is_rating){

            return response()->json(['status' => 1, 'message' => 'Thank you for rate!']);
        }else{

            return response()->json(['status' => 0, 'message' => 'You can not rate!']);
        }
    }
    /**
     ***********************************************************
     *  Function Name : start_ride
     *  Functionality : start the ride by the driver.
     *  @access         public
     *  @param        : token,ride_id,user_id.
     *  @return       : if  successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function start_ride(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('driver_id');
        if ($data = $this->check_empty($user_id, 'Please provide driver_id'))
            return response()->json($data, 200);

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        /* check cancelled*/
        $check_canceled  = DB::table('dn_cancel_rides')->select('id')->where(['ride_id' => $ride_id])->first();
        if(!empty($check_canceled)){

            return response()->json(array('status' => 0, 'message' => 'This ride already canceled!'), 200);
        }
        //check another user working on ride
        $ride_data = DB::table('dn_rides')->select('id','status','passenger_id','driver_id')->where('id', $ride_id)->first();
        if (!empty($ride_data)) {
            //check ride status
            /* if ($ride_data->status != 0) {
                 return response()->json(['status' => 0, 'message' => 'You got late. Another driver start work on it']);

             }*/
        } else {
            return response()->json(['status' => 0, 'message' => 'The Ride you are requesting is not active']);
        }

        $driver_level =DB::table('dn_users_data')->select('tiers_level')->where('user_id', $ride_data->driver_id)->first();
        //current position of driver
        $is_update = DB::table('dn_rides')->where(['id' => $ride_id])->update(['ride_start_time' => date('Y-m-d H:i:s'),'driver_level' => $driver_level->tiers_level]);

        $passanger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'profile_pic')->where(['id' => $ride_data->passenger_id])->first();
        $data = array('status' => 1, 'message' => 'ride start');
        $check_notification = $this->sendGoogleCloudMessage('Ride has been started', '9', $passanger_info, $ride_data->passenger_id, $ride_data->driver_id, $ride_id);
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : end_ride
     *  Functionality : ride end by the driver.
     *  @access         public
     *  @param        : token,driver_id,ride_id,destination_latitude,destination_longitude,dist_picktodest,dist_drivertopick.
     *  @return       : if end successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function end_ride(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $driver_id = $request->input('driver_id');
        if ($data = $this->check_empty($driver_id, 'Please provide driver_id'))
            return response()->json($data, 200);

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $destination_latitude = $request->input('destination_latitude');
        if ($data = $this->check_empty($destination_latitude, 'Please provide destination'))
            return response()->json($data, 200);
        $destination_longitude = $request->input('destination_longitude');
        if ($data = $this->check_empty($destination_longitude, 'Please provide destination'))
            return response()->json($data, 200);
        $dist_picktodest = $request->input('dist_picktodest');
        if ($data = $this->check_integer_empty($dist_picktodest, 'Please provide dist_picktodest'))
            return response()->json($data, 200);

        $dist_drivertopick = $request->input('dist_drivertopick');
        if ($data = $this->check_integer_empty($dist_drivertopick, 'Please provide dist_drivertopick'))
            return response()->json($data, 200);

        $ride_data = DB::table('dn_rides')->select('id','status','ride_start_time','passenger_id', 'pickup_time','payment_id','pickup_longitude','pickup_latitude','driver_id','transport_mode','driver_latitude','driver_longitude','driver_level')->where(['id' => $ride_id])->first();
        $tier_level = $ride_data->driver_level;
        $tier_level_bonus = DB::table('dn_driver_bonus')->select('*')->orderBy('id', 'desc')->first();
        if($tier_level == 0){

            $dezi_commission = $tier_level_bonus->commission_silver/100;
        }else if($tier_level == 1){

            $dezi_commission = $tier_level_bonus->commission_silver/100;
        }else if($tier_level == 2){

            $dezi_commission = $tier_level_bonus->commission_gold/100;
        }else if($tier_level == 3){

            $dezi_commission = $tier_level_bonus->commission_platinum/100;
        }else if($tier_level == 4){

            $dezi_commission = $tier_level_bonus->commission_diamond/100;
        }

        if($ride_data){
            $start_longitude =$ride_data->pickup_longitude;
            $start_latitude = $ride_data->pickup_latitude;
            $driver_lat = $ride_data->driver_latitude;
            $driver_long = $ride_data->driver_longitude;
            $passenger_info = (array)DB::table('dn_users')->select('first_name', 'last_name', 'profile_pic',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where('id', $ride_data->passenger_id)->first();
            $ride_end_time = date('Y-m-d H:i:s');

            //fare calculation  -----------------------------------------
            $to_time = strtotime($ride_end_time);
            $from_time = strtotime($ride_data->ride_start_time);
            $ride_duration = round(abs($to_time - $from_time) / 60,2);
            $adminCharges = $this->get_cityname($start_latitude,$start_longitude,$ride_data->pickup_time,$ride_id);
            $service_chg = $adminCharges->service_charge;

            //distance calculation in miles
            $travel_distance = $dist_picktodest;

            $passenger_to_driver = $dist_drivertopick;

            if ($passenger_to_driver <= 2) {
                $passenger_to_driver_charges = $adminCharges->less_mile_travel_cost;
            } else {
                if($ride_data->transport_mode == 'public'){
                    $passenger_to_driver_charges = $adminCharges->greater_mile_travel_cost;
                }else {
                    $passenger_to_driver_charges = file_get_contents("https://api.uber.com/v1/estimates/price?server_token=ZPJytLhhzA7xykRCqLiNRNOC79YSYMGn3F6Q0bKo&start_longitude=$driver_long&end_longitude=$start_longitude&start_latitude=$driver_lat&end_latitude=$start_latitude");
                    $passenger_to_driver_charges = json_decode($passenger_to_driver_charges);
                    $passenger_to_driver_charges = $passenger_to_driver_charges->prices[0]->high_estimate;//meters
                }
            }
            $cost_per_mile = $adminCharges->cost_per_mile;
            $waiting_charge_min = $adminCharges->per_min_charge;

            $standard_wait_duration = $ride_duration;

            //fair calculation
            $travel_distance_chagres = $travel_distance * $cost_per_mile;
            $standard_wait_duration_chagres = $waiting_charge_min * $standard_wait_duration;
            $fair = $travel_distance_chagres + $standard_wait_duration_chagres + $passenger_to_driver_charges + $service_chg;
            $fair = round($fair,2);
            $min_charge = $adminCharges->min_charge;
            if($fair > $min_charge){

                $min_charge_charge = 0.00;
                $amount_for_trip = $travel_distance_chagres + $standard_wait_duration_chagres;
                $dezinow_fee = $amount_for_trip * $dezi_commission;
                $sub_total = $amount_for_trip - $dezinow_fee;
                $driver_earning = $sub_total + $passenger_to_driver_charges;
            }else if($fair < $min_charge){

                $min_charge_charge = $min_charge - $fair;
                if(empty($min_charge_charge)){
                    $min_charge_charge = 0.00;
                }
                $amount_for_trip = $min_charge - $service_chg - $passenger_to_driver_charges;
                $dezinow_fee = $amount_for_trip * $dezi_commission;
                $sub_total = $amount_for_trip - $dezinow_fee;
                $driver_earning = $sub_total + $passenger_to_driver_charges;
                $fair = round($min_charge,2);
            }

            $billing_data = [
                'ride_id' => $ride_id,
                'miles' => $travel_distance,
                'miles_charges' => $travel_distance_chagres ? $travel_distance_chagres : 0.00,
                'duration' => $standard_wait_duration ? $standard_wait_duration : 0.00,
                'duration_charges' => $standard_wait_duration_chagres ? $standard_wait_duration_chagres : 0.00,
                'subtotal' => $sub_total ? $sub_total : 0.00,
                'subtotal_passenger' => $standard_wait_duration_chagres + $travel_distance_chagres,
                'service_fee' => $service_chg ? $service_chg : 0.00,
                'pickup_fee' => $passenger_to_driver_charges ? $passenger_to_driver_charges : 0.00,
                'minimum_charge' => $min_charge ? $min_charge : 0.00,
                'driver_earnings' => $driver_earning ? $driver_earning : 0.00,
                'dezinow_earning' => $dezinow_fee ? $dezinow_fee : 0.00,
                'min_charge' => $min_charge_charge ? $min_charge_charge : 0.00,
                'total_charges' => $fair ? $fair : 0.00,
            ];

            $map_image = "https://maps.googleapis.com/maps/api/staticmap?center=$start_latitude,$start_longitude&size=500x250&maptype=roadmap&markers=icon:http://www.dezinow.com/uploads/icons/pickup.png%7C$start_latitude,$start_longitude&markers=icon:http://www.dezinow.com/uploads/icons/dropoff.png%7C$destination_latitude,$destination_longitude";
            DB::table('dn_rides')
                ->where(['driver_id' => $driver_id, 'id' => $ride_id])
                ->update([
                    'destination_latitude' => $destination_latitude,
                    'destination_longitude' => $destination_longitude,
                    'ride_end_time' => $ride_end_time,
                    'status' => '2',
                    'bill_generated' => '1',
                    'map_image' => $map_image,
                ]);

            DB::table('ride_billing_info')->insertGetId($billing_data);
            $data = ['status' => 1];
            $check_notification = $this->sendGoogleCloudMessage('Ride has been completed', '6', $passenger_info, $ride_data->passenger_id, $ride_data->driver_id, $ride_id);
        }
        else{
            $data = ['status' => 0, 'message' => 'The Ride you are requesting is not active!'];
        }

        return response()->json($data);

    }
    /**
     *
     ***********************************************************
     *  Function Name : endride_info
     *  Functionality : get the single ride info to the user.
     *  @access         public
     *  @param        : token,ride_id.
     *  @return       : if successfully gives status 1 and amount,driver_info,payment_status,time otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function endride_info(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $ride_data = DB::table('dn_rides')->select('id','status','ride_start_time','passenger_id', 'payment_id','ride_end_time','charge_id','driver_id')->where(['id' => $ride_id])->first();

        if($ride_data) {
            //check blocked user
            if ($user = $this->is_blocked($ride_data->passenger_id)) {
                return response()->json($user);
            }
            $driver_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'profile_pic')->where(['id' => $ride_data->driver_id])->first();
            $to_time = strtotime($ride_data->ride_end_time);
            $from_time = strtotime($ride_data->ride_start_time);

            $ride_duration = round(abs($to_time - $from_time) / 60, 2);
            $payment_detail  = DB::table('dn_payments')->select('amount')->where(['id' => $ride_data->charge_id])->first();
            if($payment_detail->status = 'Approved'){
                $payment_status = 1;
            }else{

                $payment_status = 0;
            }
            $fair = round($payment_detail->amount,2);
            $est_fair = ['amount' => $fair, 'currency' => '$'];
            $data = ['status' => 1,'amount' => $est_fair, 'driver_info' => $driver_info, 'payment_status' => $payment_status, 'time' => $ride_duration];
        }else{

            $data = ['status' => 0, 'message' => 'The Ride you are requesting is not active'];
        }

        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : driver_total_earning
     *  Functionality : get the single driver total earning of that day.
     *  @access         public
     *  @param        : token,driver_id.
     *  @return       : if successfully gives status 1 and net_amount otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function driver_total_earning(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $driver_id = $request->input('driver_id');
        if ($data = $this->check_empty($driver_id, 'Please provide driver_id'))
            return response()->json($data, 200);
        if ($user = $this->is_blocked($driver_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($driver_id)) {
            return response()->json($user);
        }
        $driver_exist = DB::table('dn_users')->select('id')->where(['id' => $driver_id])->first();
        $today = date('Y-m-d').' 00:00:00';
        $end_today = date('Y-m-d').' 23:59:59';
        if($driver_exist) {
            $ride_ids = (array)DB::table('dn_rides')->select(DB::raw("id"))->where(array('driver_id' => $driver_id)) ->whereBetween('created_at', array($today, $end_today))->orderBy('id', 'desc')->get();
            foreach($ride_ids as &$ride_id)
            {
                $ride_id1 = $ride_id->id;
                $amount = DB::table('dn_payments')->select('driver_earning')->where(['ride_id' => $ride_id1])->first();
                if($amount){
                    $total_amount[] =  ( !empty( $amount->driver_earning ) ) ? $amount->driver_earning : '0.00';
                }
            }
            $net_amount = array_sum($total_amount);
            $net_amount =  number_format((float)$net_amount, 2, '.', '');
            $data = ['status' => 1,'net_amount' => $net_amount];
        }else{

            $data = ['status' => 0, 'message' => 'Driver not found!'];
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : rider_ride_history
     *  Functionality : get the trip history of user.
     *  @access         public
     *  @param        : token,driver_id,from_date,end_date,offset.
     *  @return       : if successfully gives status 1 and all trip history in data object otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function rider_ride_history(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $currunt_timezone = config('app.timezone');
        $user_timezone = $this->get_user_timezone($user_id);
        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        $from_date = $request->input('from_date');
        if ($data = $this->check_empty($from_date, 'Please provide from_date'))
            return response()->json($data, 200);
        $from_date = $this->time_translate($user_timezone,$currunt_timezone, $from_date);

        $end_date = $request->input('end_date');
        if ($data = $this->check_empty($end_date, 'Please provide end_date'))
            return response()->json($data, 200);
        $end_date = $this->time_translate($user_timezone,$currunt_timezone, $end_date);

        $offset = $request->input('offset');
        if ($data = $this->check_integer_empty($offset, 'Please provide offset'))
            return response()->json($data, 200);
        $limit = 10;
        if($offset !=0){
            $offset = ($offset*$limit)+1;
        }
        $exists = DB::table('dn_users')->where('id', $user_id)->first();
        if($exists){

            $ride_ids = (array)DB::table('dn_rides')->select(DB::raw("*"))->where(array('passenger_id' => $user_id)) ->whereBetween('ride_end_time', array($from_date, $end_date))->whereIn('status', array(2, 3, 6))->orderBy('id', 'desc')->limit($limit)->offset($offset)->get();

            if($ride_ids) {
                foreach ($ride_ids as &$ride_id) {
                    $ride_id->pickup_time = $this->time_translate($currunt_timezone, $user_timezone, $ride_id->pickup_time);
                    $ride_id->ride_start_time = $this->time_translate($currunt_timezone, $user_timezone, $ride_id->ride_start_time);
                    $ride_id->ride_end_time = $this->time_translate($currunt_timezone, $user_timezone,$ride_id->ride_end_time);
                    $ride_id1 = $ride_id->id;
                    $driver_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_id->driver_id])->first();
                    $driver_profile_pic =DB::table('dn_users_data')->where(['user_id' => $ride_id->driver_id])->pluck('driver_profile_pic');
                    if($driver_profile_pic){
                        $driver_info->profile_pic = $driver_profile_pic;
                    }else{
                        $driver_info->profile_pic = "";
                    }
                    $ride_id->driver_info = $driver_info;
                    $billing_info = DB::table('ride_billing_info')->select('*')->where(['ride_id' => $ride_id1])->first();
                    $account_info = DB::table('dn_payment_accounts')->select('account_type', 'account_email', 'masked_number', 'image_url')->where(['id' => $ride_id->payment_id])->first();

                    if($billing_info){
                        $billing_info->account_type = $account_info->account_type;
                        $billing_info->account_email = $account_info->account_email;
                        $billing_info->masked_number = $account_info->masked_number;
                        $billing_info->image_url = $account_info->image_url;
                        $ride_id->billing_info = $billing_info;
                    }
                    else{
                        $ride_id->billing_info = NUlL;
                    }
                }

                $data = array('status' => 1, 'data' => $ride_ids);
            }else{
                $data = array('status' => 1, 'message' => 'No history found!');
            }
        }else{

            $data = array('status' => 0, 'message' => 'This user does not exist!');
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : cancel_pickup
     *  Functionality : cancel the ride before start the ride or during the ride.
     *  @access         public
     *  @param        : token,user_id,ride_id,type,category,subcategory,message,cancel_by.
     *  @return       : if cancel successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function cancel_pickup(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $type = $request->input('type');
        if ($data = $this->check_empty($type, 'Please provide type'))
            return response()->json($data, 200);

        $category = $request->input('category');
        if ($data = $this->check_empty($category, 'Please provide category'))
            return response()->json($data, 200);

        $subcategory = $request->input('subcategory');

        $message = $request->input('message');

        $cancel_by = $request->input('cancel_by');
        if ($data = $this->check_empty($cancel_by, 'Please provide cancel_by'))
            return response()->json($data, 200);

        $check_canceled  = DB::table('dn_cancel_rides')->select('id')->where(['ride_id' => $ride_id])->first();
        if(!empty($check_canceled)){

            //return response()->json(array('status' => 8, 'message' => 'This ride already canceled!'), 200);
        }
        $now_time = date('Y-m-d H:i:s');
        $current_time = strtotime(date('Y-m-d H:i:s'));


        $const = $this->firebaseConstant();
        $DEFAULT_PATH = '/DeziNow';
        $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
        $destination_latitude = str_replace('"', "", $firebase->get($DEFAULT_PATH . "/rides/" . $ride_id . '/destination_latitude'));
        $destination_longitude = str_replace('"', "", $firebase->get($DEFAULT_PATH . "/rides/" . $ride_id . '/destination_longitude'));
        $start_latitude = str_replace('"', "", $firebase->get($DEFAULT_PATH . "/rides/" . $ride_id . '/pickup_latitude'));
        $start_longitude = str_replace('"', "", $firebase->get($DEFAULT_PATH . "/rides/" . $ride_id . '/pickup_longitude'));
        $dist_drivertopick = str_replace('"', "", $firebase->get($DEFAULT_PATH . "/rides/" . $ride_id . '/dist_drivertopick'));
        $dist_picktodest = str_replace('"', "", $firebase->get($DEFAULT_PATH . "/rides/" . $ride_id . '/dist_picktodest'));

        $map_image = "https://maps.googleapis.com/maps/api/staticmap?center=$start_latitude,$start_longitude&size=500x250&maptype=roadmap&markers=icon:http://www.dezinow.com/uploads/icons/pickup.png%7C$start_latitude,$start_longitude&markers=icon:http://www.dezinow.com/uploads/icons/dropoff.png%7C$destination_latitude,$destination_longitude";

        $ride_data = DB::table('dn_rides')->select('id','status','ride_start_time','passenger_id','pickup_time','ride_end_time','charge_id','driver_id','arrived_time','arrived_status','driver_latitude','driver_longitude','driver_level')->where(['id' => $ride_id])->first();
        $tier_level = $ride_data->driver_level;
        $tier_level_bonus = DB::table('dn_driver_bonus')->select('*')->orderBy('id', 'desc')->first();
        if($tier_level == 0){

            $dezi_commission = $tier_level_bonus->commission_silver/100;
        }else if($tier_level == 1){

            $dezi_commission = $tier_level_bonus->commission_silver/100;
        }else if($tier_level == 2){

            $dezi_commission = $tier_level_bonus->commission_gold/100;
        }else if($tier_level == 3){

            $dezi_commission = $tier_level_bonus->commission_platinum/100;
        }else if($tier_level == 4){

            $dezi_commission = $tier_level_bonus->commission_diamond/100;
        }
        $adminCharges = $this->get_cityname($start_latitude,$start_longitude,$ride_data->pickup_time,$ride_id);
        $min_charge = $adminCharges->min_charge;
        $service_chg = $adminCharges->service_charge;
        $driver_latitude = $ride_data->driver_latitude;
        $driver_longitude = $ride_data->driver_longitude;
        if($cancel_by == 'driver'){

            $to = $ride_data->passenger_id;
            $from = $ride_data->driver_id;
            $passenger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->driver_id])->first();
            // $sql_query="SELECT id,first_name,last_name FROM dn_users WHERE id = $from UNION ALL SELECT id FROM dn_users WHERE id = $to";
            // $passenger_info = DB::select(DB::raw($sql_query));
            $driver_profile_pic =DB::table('dn_users_data')->where(['user_id' => $ride_data->driver_id])->pluck('driver_profile_pic');
            if($driver_profile_pic){
                $passenger_info->profile_pic = $driver_profile_pic;
            }else{
                $passenger_info->profile_pic = "";
            }
        }else{
            $to = $ride_data->driver_id;
            $from = $ride_data->passenger_id;
            $passenger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'profile_pic',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->passenger_id])->first();
        }
        $insert_data = ['ride_id' => $ride_id,'cancel_by' => $cancel_by,'user_id' => $user_id,'category' => $category,'subcategory' => $subcategory,'message' => $message];

        if($ride_data){
            $driver_id= $ride_data->driver_id;
            DB::table('ride_billing_info')->where(['ride_id' => $ride_id])->delete();
            if($type == 'pickup') {

                $passenger_to_driver_charges = file_get_contents("https://api.uber.com/v1/estimates/price?server_token=ZPJytLhhzA7xykRCqLiNRNOC79YSYMGn3F6Q0bKo&start_longitude=$driver_longitude&end_longitude=$start_longitude&start_latitude=$driver_latitude&end_latitude=$start_latitude");
                $passenger_to_driver_charges = json_decode($passenger_to_driver_charges);
                $passenger_to_driver_charges = $passenger_to_driver_charges->prices[0]->high_estimate;//meters
                $passenger_to_driver_charges = round($passenger_to_driver_charges, 2);
                $ride_time = strtotime($ride_data->pickup_time);
                $ride_time = $ride_time + (60 * 2);

                if ($cancel_by == 'passenger') {
                    $contact_number = DB::table('dn_users')->select(DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->driver_id])->first();
                    if ($ride_time > $current_time OR in_array($subcategory, array("9", "13", "14", "16", "17"))) {
                        DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 6, 'map_image' => $map_image, 'bill_generated' => '0', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);
                        $data = array('status' => 1, 'message' => 'The ride is cancelled successfully!');
                        $firebase->update($DEFAULT_PATH . '/rides/' .$ride_id , ['status' => '-1', 'ride_message'=>'Ride has been cancelled']);
                        $firebase->update($DEFAULT_PATH . '/drivers/' .$driver_id , ['is_hired' => 0,'driver_available_is_hired' => 'true_0','driver_available' => true]);
                        $check_notification = $this->sendGoogleCloudMessage('Ride has been cancelled', '8', $passenger_info, $to, $from, $ride_id);
                        DB::table('dn_cancel_rides')->insertGetId($insert_data);
                        // $is_sent= $this->twileo_send($contact_number->contact_number,'passenger');

                    } else if (in_array($subcategory, array("10", "11", "12", "18"))) {

                        $total_charges = $passenger_to_driver_charges;
                        $total_charges = round($total_charges, 2);
                        $billing_data = [
                            'ride_id' => $ride_id,
                            'pickup_fee' => $passenger_to_driver_charges,
                            'minimum_charge' => $min_charge,
                            'driver_earnings' => $passenger_to_driver_charges,
                            'total_charges' => $total_charges
                        ];
                        DB::table('ride_billing_info')->insertGetId($billing_data);
                        DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 3, 'map_image' => $map_image, 'bill_generated' => '1', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);

                        $data = array('status' => 2, 'cancellation_data' => $insert_data, 'amount' => (string)$total_charges);
                    } else if ($subcategory == 15) {

                        $total_charges = $passenger_to_driver_charges+$service_chg;
                        if ($total_charges >= $min_charge) {
                            $min_charge_charge = 0.00;
                            $amount_for_trip = $passenger_to_driver_charges;
                            $dezinow_fee = $amount_for_trip * $dezi_commission;
                            $sub_total = $amount_for_trip - $dezinow_fee;
                            $driver_earning = $sub_total + $passenger_to_driver_charges;
                        } else  {
                            $total_charges = round($total_charges, 2);
                            $min_charge_charge = $min_charge - $total_charges;
                            if(empty($min_charge_charge)){
                                $min_charge_charge = 0.00;
                            }
                            $amount_for_trip = $min_charge - $service_chg - $passenger_to_driver_charges;
                            $dezinow_fee = $amount_for_trip * $dezi_commission;
                            $sub_total = $amount_for_trip - $dezinow_fee;
                            $driver_earning = $sub_total + $passenger_to_driver_charges;
                            $total_charges = $min_charge;
                        }
                        $total_charges = round($total_charges, 2);
                        $billing_data = [
                            'ride_id' => $ride_id,
                            'service_fee' => $service_chg ? $service_chg : 0.00,
                            'min_charge' => $min_charge_charge ? $min_charge_charge : 0.00,
                            'minimum_charge' => $min_charge ? $min_charge : 0.00,
                            'pickup_fee' => $passenger_to_driver_charges ? $passenger_to_driver_charges : 0.00,
                            'driver_earnings' => $driver_earning ? $driver_earning : 0.00,
                            'dezinow_earning' => $dezinow_fee ? $dezinow_fee : 0.00,
                            'total_charges' => $total_charges ? $total_charges : 0.00,
                        ];
                        DB::table('ride_billing_info')->insertGetId($billing_data);
                        DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 3, 'map_image' => $map_image, 'bill_generated' => '1', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);

                        $data = array('status' => 2, 'cancellation_data' => $insert_data, 'amount' => (string)$total_charges);

                    }  else{

                        DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 6, 'map_image' => $map_image, 'bill_generated' => '0', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);
                        $data = array('status' => 1, 'message' => 'The ride is cancelled successfully!');
                        $firebase->update($DEFAULT_PATH . '/rides/' .$ride_id , ['status' => '-1', 'ride_message'=>'Ride has been cancelled']);
                        $firebase->update($DEFAULT_PATH . '/drivers/' .$driver_id , ['is_hired' => 0,'driver_available_is_hired' => 'true_0','driver_available' => true]);
                        $check_notification = $this->sendGoogleCloudMessage('Ride has been cancelled', '8', $passenger_info, $to, $from, $ride_id);
                        DB::table('dn_cancel_rides')->insertGetId($insert_data);
                    }

                } else if ($cancel_by == 'driver') {
                    $contact_number = DB::table('dn_users')->select(DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->passenger_id])->first();
                    $arrived_time = strtotime($ride_data->arrived_time);
                    $arrived_time = $arrived_time + (60 * 2);
                    $arrived_time5minute = $arrived_time + (60 * 5);
                    if ($ride_data->arrived_status == 0 ) {
                        DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 6, 'map_image' => $map_image, 'bill_generated' => '0', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);
                        $data = array('status' => 1, 'message' => 'The ride is cancelled successfully!');
                        $firebase->update($DEFAULT_PATH . '/rides/' .$ride_id , ['status' => '-1', 'ride_message'=>'Ride has been cancelled']);
                        $firebase->update($DEFAULT_PATH . '/drivers/' .$driver_id , ['is_hired' => 0,'driver_available_is_hired' => 'true_0','driver_available' => true]);
                        $check_notification = $this->sendGoogleCloudMessage('Ride has been cancelled', '8', $passenger_info, $to, $from, $ride_id);
                        DB::table('dn_cancel_rides')->insertGetId($insert_data);
                        // $is_sent= $this->twileo_send($contact_number->contact_number,'driver');
                    } else {

                        if (( $subcategory != 4 AND $subcategory != 6 AND $subcategory != 7 AND $subcategory != 40) OR in_array($subcategory, array("1", "2", "3", "5", "8"))) {
                            DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 6, 'map_image' => $map_image, 'bill_generated' => '0', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);
                            $firebase->update($DEFAULT_PATH . '/rides/' .$ride_id , ['status' => '-1', 'ride_message'=>'Ride has been cancelled']);
                            $firebase->update($DEFAULT_PATH . '/drivers/' .$driver_id , ['is_hired' => 0,'driver_available_is_hired' => 'true_0','driver_available' => true]);
                            $check_notification = $this->sendGoogleCloudMessage('Ride has been cancelled', '8', $passenger_info, $to, $from, $ride_id);
                            DB::table('dn_cancel_rides')->insertGetId($insert_data);
                            $data = array('status' => 1, 'message' => 'The ride is cancelled successfully!');
                        } else if (($subcategory == 4 AND $arrived_time5minute < $current_time) OR $subcategory == 6) {
                            // Took more than 5 minutes after tapping Arrived At PickUp before cancelling
                            $total_charges = $passenger_to_driver_charges + $service_chg;
                            if ($total_charges >= $min_charge) {
                                $min_charge_charge = 0.00;
                                $amount_for_trip = 0;
                                $dezinow_fee =0;
                                $sub_total = 0;
                                $driver_earning = $passenger_to_driver_charges;
                            } else  {
                                $total_charges = round($total_charges, 2);
                                $min_charge_charge = $min_charge - $total_charges;
                                if(empty($min_charge_charge)){
                                    $min_charge_charge = 0.00;
                                }
                                $amount_for_trip = $min_charge - $service_chg - $passenger_to_driver_charges;
                                $dezinow_fee = $amount_for_trip * $dezi_commission;
                                $sub_total = $amount_for_trip - $dezinow_fee;
                                $driver_earning = $sub_total + $passenger_to_driver_charges;
                                $total_charges = $min_charge;
                            }
                            $total_charges = round($total_charges, 2);
                            $billing_data = [
                                'ride_id' => $ride_id,
                                'service_fee' => $service_chg ? $service_chg : 0.00,
                                'min_charge' => $min_charge_charge ? $min_charge_charge : 0.00,
                                'minimum_charge' => $min_charge ? $min_charge : 0.00,
                                'pickup_fee' => $passenger_to_driver_charges ? $passenger_to_driver_charges : 0.00,
                                'driver_earnings' => $driver_earning ? $driver_earning : 0.00,
                                'dezinow_earning' => $dezinow_fee ? $dezinow_fee : 0.00,
                                'total_charges' => $total_charges ? $total_charges : 0.00,
                            ];
                            DB::table('ride_billing_info')->insertGetId($billing_data);
                            DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 3, 'map_image' => $map_image, 'bill_generated' => '1', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);

                            $data = array('status' => 2, 'cancellation_data' => $insert_data, 'amount' => (string)$total_charges);
                        } elseif (($subcategory == 4 AND $arrived_time5minute > $current_time) OR $subcategory == 7 OR $subcategory == 40) {

                            $total_charges = $passenger_to_driver_charges;
                            $total_charges = round($total_charges, 2);
                            $billing_data = [
                                'ride_id' => $ride_id,
                                'pickup_fee' => $passenger_to_driver_charges ? $passenger_to_driver_charges : 0.00,
                                'minimum_charge' => $min_charge ? $min_charge : 0.00,
                                'driver_earnings' => $passenger_to_driver_charges ? $passenger_to_driver_charges : 0.00,
                                'total_charges' => $total_charges ? $total_charges : 0.00,
                            ];
                            DB::table('ride_billing_info')->insertGetId($billing_data);
                            DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude, 'destination_longitude' => $destination_longitude, 'status' => 3, 'map_image' => $map_image, 'bill_generated' => '1', 'ride_start_time' => $now_time, 'ride_end_time' => $now_time]);

                            $data = array('status' => 2, 'cancellation_data' => $insert_data, 'amount' => (string)$total_charges);
                        }
                    }
                }
            }else if($type =='ride' ){
                $to_time = strtotime($now_time);
                $from_time = strtotime($ride_data->ride_start_time);
                $ride_duration = round(abs($to_time - $from_time) / 60, 2);
                //distance calculation in miles
                $travel_distance = $dist_picktodest;

                $passenger_to_driver = $dist_drivertopick;

                if ($passenger_to_driver <= 2) {
                    $passenger_to_driver_charges = $adminCharges->less_mile_travel_cost;
                } else {
                    if($ride_data->transport_mode == 'public'){
                        $passenger_to_driver_charges = $adminCharges->greater_mile_travel_cost;
                    }else {
                        $passenger_to_driver_charges = file_get_contents("https://api.uber.com/v1/estimates/price?server_token=ZPJytLhhzA7xykRCqLiNRNOC79YSYMGn3F6Q0bKo&start_longitude=$driver_longitude&end_longitude=$start_longitude&start_latitude=$driver_latitude&end_latitude=$start_latitude");
                        $passenger_to_driver_charges = json_decode($passenger_to_driver_charges);
                        $passenger_to_driver_charges = $passenger_to_driver_charges->prices[0]->high_estimate;//meters
                    }
                }
                $cost_per_mile = $adminCharges->cost_per_mile;
                $waiting_charge_min = $adminCharges->per_min_charge;

                $standard_wait_duration = $ride_duration;

                //fair calculation
                $travel_distance_chagres = $travel_distance * $cost_per_mile;
                $standard_wait_duration_chagres = $waiting_charge_min * $standard_wait_duration;
                $fair = $travel_distance_chagres + $standard_wait_duration_chagres + $passenger_to_driver_charges + $service_chg;
                $fair = round($fair,2);
                if($fair > $min_charge){
                    $total_charges = $fair;
                    $min_charge_charge = 0.00;
                    $amount_for_trip = $travel_distance_chagres + $passenger_to_driver_charges;
                    $dezinow_fee = $amount_for_trip * $dezi_commission;
                    $sub_total = $amount_for_trip - $dezinow_fee;
                    $driver_earning = $sub_total + $passenger_to_driver_charges;
                }else if($fair < $min_charge){
                    $fair = round($fair,2);
                    $min_charge_charge = $min_charge -$fair;
                    if(empty($min_charge_charge)){
                        $min_charge_charge = 0.00;
                    }
                    $amount_for_trip = $min_charge - $service_chg - $passenger_to_driver_charges;
                    $dezinow_fee = $amount_for_trip * $dezi_commission;
                    $sub_total = $amount_for_trip - $dezinow_fee;
                    $driver_earning = $sub_total + $passenger_to_driver_charges;
                    $total_charges = $min_charge;
                }
                $total_charges = round($total_charges,2);
                $billing_data =[
                    'ride_id' => $ride_id,
                    'miles' => $travel_distance,
                    'miles_charges' => $travel_distance_chagres ? $travel_distance_chagres : 0.00,
                    'duration' => $standard_wait_duration,
                    'duration_charges' => $standard_wait_duration_chagres ? $standard_wait_duration_chagres : 0.00,
                    'subtotal' => $sub_total ? $sub_total : 0.00,
                    'subtotal_passenger' => $standard_wait_duration_chagres + $travel_distance_chagres,
                    'service_fee'  => $service_chg ? $service_chg : 0.00,
                    'cancelation_charge'  => 0.00,
                    'min_charge'  => $min_charge_charge ? $min_charge_charge : 0.00,
                    'minimum_charge' => $min_charge ? $min_charge : 0.00,
                    'driver_earnings' => $driver_earning ? $driver_earning : 0.00,
                    'dezinow_earning' => $dezinow_fee ? $dezinow_fee : 0.00,
                    'pickup_fee' => $passenger_to_driver_charges ? $passenger_to_driver_charges : 0.00,
                    'total_charges' => $total_charges ? $total_charges : 0.00,
                ];
                DB::table('ride_billing_info')->insertGetId($billing_data);
                DB::table('dn_rides')->where('id', $ride_id)->update(['destination_latitude' => $destination_latitude,'destination_longitude'=>$destination_longitude,'status' => 3,'map_image'=>$map_image,'bill_generated' => '1','ride_end_time'=>$now_time]);
                $data = array('status' => 2,'cancellation_data' =>$insert_data,'amount'=>(string)$total_charges);
            }
        }else{
            $data = array('status' => 0, 'message' => 'The Ride you are requesting is not active!');
        }
        /*
        Remove Driver id from session so that he can ride again.
        */
        /*session_id("driverid");
        session_start();
         if (($key = array_search(@$ride_data->driver_id,$_SESSION['allocated_driver'])) !== false) {
            unset($_SESSION['allocated_driver'][$key]);
        } */
        return response()->json($data);

    }
    /**
     ***********************************************************
     *  Function Name : cancel_ride_request
     *  Functionality : cancel the ride before accept the ride.
     *  @access         public
     *  @param        : token,ride_id.
     *  @return       : if cancel successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function cancel_ride_request(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $ride_id_exist = DB::table('dn_rides')->where('id', $ride_id)->first();
        if(!empty($ride_id_exist)){

            DB::table('dn_rides') ->where(['id' => $ride_id])->update(array('status' => '5'));
            $data = array('status' => 1, 'message' => 'Cancelled successfully!');
        }else{

            $data = array('status' => 0, 'message' => 'This ride does not exist!');
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : cancellation_category
     *  Functionality : get the category and sub-category for report an issues.
     *  @access         public
     *  @param        : token,type,issue_type.
     *  @return       : if get successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function cancellation_category(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $type = $request->input('type');
        if ($data = $this->check_empty($type, 'Please provide category type'))
            return response()->json($data, 200);
        $issue_type = $request->input('issue_type');
        $cancellation_data = DB::table('dn_cancellation_category')->select('id','category')->where(['type' => $type ,'issue_type'=>$issue_type])->get();
        if($cancellation_data){

            foreach ($cancellation_data as &$cancellation_id) {

                $subcancellation_data = DB::table('dn_cancellation_subcategory')->select('id','subcategory')->where(['category_id' => $cancellation_id->id])->get();


                if($subcancellation_data){
                    $cancellation_id->subcategory_detail = $subcancellation_data;
                }
                else{
                    $cancellation_id->subcategory_detail = array();
                }
            }
            $data = array('status' => 1, 'data' => $cancellation_data);
        }
        else{

            $data = array('status' => 0, 'message' => 'No category found!');
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : charge_cancellation_category
     *  Functionality : get the category and sub-category for cancellation reason.
     *  @access         public
     *  @param        : token,type,cancel_type.
     *  @return       : if get successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function charge_cancellation_category(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $type = $request->input('type');
        if ($data = $this->check_empty($type, 'Please provide category type'))
            return response()->json($data, 200);
        $cancel_type = $request->input('cancel_type');
        if ($data = $this->check_empty($cancel_type, 'Please provide category cancel_type'))
            return response()->json($data, 200);
        $cancellation_data = DB::table('dn_cancellation_charge_category')->select('id','category')->where(['type' => $type ,'cancel_type'=>$cancel_type])->get();
        if($cancellation_data){

            foreach ($cancellation_data as &$cancellation_id) {

                $subcancellation_data = DB::table('dn_cancellation_charge_subcategory')->select('id','subcategory','charge')->where(['category_id' => $cancellation_id->id])->get();


                if($subcancellation_data){
                    $cancellation_id->subcategory_detail = $subcancellation_data;
                }
                else{
                    $cancellation_id->subcategory_detail = array();
                }
            }
            $data = array('status' => 1, 'data' => $cancellation_data);
        }
        else{

            $data = array('status' => 0, 'message' => 'No category found!');
        }
        return response()->json($data);
    }
    private function key($key)
    {
        $db_key_arr = DB::table('dn_app_secret')->where('slug', 'app_security')->first();
        $db_key = $db_key_arr->app_secret;
        if ($db_key !== $key) {
            return 1;

        }
    }
    /**
     ***********************************************************
     *  Function Name : generateRandomString
     *  Functionality : used for generate the random string.
     *  @access         private
     *  @param        : lenth.
     *  @return       : random string;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    /**
     ***********************************************************
     *  Function Name : twileo_send
     *  Functionality : used for send the text message using twileo services.
     *  @access         private
     *  @param        : phone number, otp.
     *  @return       : return 1 if send message successfully otherwise return 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function twileo_send( $phone,$otp)
    {
        //live
        $id = "ACef0bc2ba66b70340468cc67fca14390d";
        $token = "e12d7d9fac857f1b845d19e8e9bde841";


        // $id = "ACfddf87b2b59408f9148f91a941134934";
        // $token = "615636c3a66c920408df28510727eb09";


        $url = "https://api.twilio.com/2010-04-01/Accounts/$id/SMS/Messages";
        $from = "6507535036";
        //$from = "+1443-840-7757";   //live
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
        //var_dump($y); die;
        $xml=simplexml_load_string($y);
        if(empty($xml->SMSMessage->Sid)){
            return 0;
        }else{
            return 1;
        }
        curl_close($x);
        //sms log
    }
    /**
     ***********************************************************
     *  Function Name : driver_bank_detail
     *  Functionality : add and update the bank detail of driver.
     *  @access         public
     *  @param        : token,user_id,acc_numer,routing_number,bank_name,branch.
     *  @return       : if add/update successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function driver_bank_detail(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $acc_numer = $request->input('acc_numer');
        if ($data = $this->check_empty($acc_numer, 'Please provide acc_number'))
            return response()->json($data, 200);

        $routing_number = $request->input('routing_number');
        if ($data = $this->check_empty($routing_number, 'Please provide routing_number'))
            return response()->json($data, 200);

        $bank_name = $request->input('bank_name');
        if ($data = $this->check_empty($bank_name, 'Please provide bank_name'))
            return response()->json($data, 200);

        $branch = $request->input('branch');
        if ($data = $this->check_empty($branch, 'Please provide branch'))
            return response()->json($data, 200);

        $check_existing = DB::table('dn_driver_bank_detail')->where('user_id', $user_id)->first();

        if(empty($check_existing)){
            $inserted = DB::table('dn_driver_bank_detail')->insertGetId(['user_id' => $user_id, 'bank_name' => $bank_name, 'branch' => $branch, 'acc_number' => $acc_numer, 'routing_number' => $routing_number]);
            if ($inserted) {
                $data = array('status' => 1, 'message' => 'Added successfully');
            } else {
                $data = array('status' => 0, 'message' => 'Unable to Added');
            }

        }else{

            DB::table('dn_driver_bank_detail')
                ->where(['user_id' => $user_id])
                ->update(['user_id' => $user_id, 'bank_name' => $bank_name, 'branch' => $branch, 'acc_number' => $acc_numer, 'routing_number' => $routing_number]);
            $data = array('status' => 1, 'message' => 'Updated successfully');
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : get_driver_bank_detail
     *  Functionality : get the bank detail of driver.
     *  @access         public
     *  @param        : token,user_id.
     *  @return       : if get successfully gives status 1 and bank data otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function get_driver_bank_detail(Request $request)
    {

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $check_existing = DB::table('dn_driver_bank_detail')->where('user_id', $user_id)->first();
        if($check_existing){

            $data = array('status' => 1, 'data' => $check_existing);
        }else{

            $data = array('status' => 0, 'message' => 'You have no bank information yet!');
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : get_driver_bank_detail
     *  Functionality : get the bank detail of driver.
     *  @access         public
     *  @param        : token,user_id.
     *  @return       : if get successfully gives status 1 and bank data otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function driver_earning_detail(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $currunt_timezone = config('app.timezone');

        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $user_timezone = $this->get_user_timezone($user_id);
        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $from_date = $request->input('from_date');
        if ($data = $this->check_empty($from_date, 'Please provide from_date'))
            return response()->json($data, 200);
        $from_date = $this->time_translate($user_timezone,$currunt_timezone, $from_date);
        $end_date = $request->input('end_date');
        if ($data = $this->check_empty($end_date, 'Please provide end_date'))
            return response()->json($data, 200);
        $end_date = $this->time_translate($user_timezone,$currunt_timezone, $end_date);


        $driver_exist = DB::table('dn_users')->select('id')->where(['id' => $user_id])->first();

        if($driver_exist) {
            //  \DB::connection()->enableQueryLog();
            $ride_ids = (array)DB::table('dn_rides')->select(DB::raw("*"))->where(array('driver_id' => $user_id)) ->whereBetween('ride_end_time', array($from_date, $end_date))->whereIn('status', array(2,3,6))->orderBy('id', 'desc')->get();

            // $query = \DB::getQueryLog();
            // $lastQuery = end($query);
            // var_dump($lastQuery);
            //  die;

            if($ride_ids) {
                foreach ($ride_ids as &$ride_id) {
                    $ride_id->pickup_time = $this->time_translate($currunt_timezone, $user_timezone, $ride_id->pickup_time);
                    $ride_id->ride_start_time = $this->time_translate($currunt_timezone, $user_timezone, $ride_id->ride_start_time);
                    $ride_id->ride_end_time = $this->time_translate($currunt_timezone, $user_timezone,$ride_id->ride_end_time);

                    $amount = DB::table('dn_payments')->select('driver_earning','payment_id')->where(['ride_id' => $ride_id->id])->first();

                    $total_amount[] = (!empty($amount->driver_earning)) ? $amount->driver_earning : 0.00;


                    $passanger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'profile_pic',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_id->passenger_id])->first();
                    $ride_id->driver_info = $passanger_info;
                    $billing_info = DB::table('ride_billing_info')->select('*')->where(['ride_id' => $ride_id->id])->first();

                    if ($billing_info) {
                        $billing_info->driver_earning = (!empty($amount->driver_earning)) ? $amount->driver_earning : 0.00;
                        $ride_id->billing_info = $billing_info;
                    } else {
                        $ride_id->billing_info = NUlL;
                    }
                }

                $net_amount = array_sum($total_amount);
                $net_amount =  number_format((float)$net_amount, 2, '.', '');

                $data = ['status' => 1, 'net_amount' => $net_amount, 'ride_data' => $ride_ids];

            }else{
                $data = ['status' => 1, 'net_amount' => "0.00", 'ride_data' => $ride_ids,'message' =>'No ride found in this week'];

            }
        }else{

            $data = ['status' => 0, 'message' => 'Driver not found!'];
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : get_faq
     *  Functionality : get all the faq from the database.
     *  @access         public
     *  @param        : token.
     *  @return       : if get successfully gives status 1 and faq data otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function get_faq(Request $request){

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $get_faq = DB::table('dn_faq')->get();
        if($get_faq){

            $data = ['status' => 1, 'data' => $get_faq];
        }else{

            $data = ['status' => 0, 'message' => 'No faq found!'];
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : report_an_issue
     *  Functionality : report an issue by the user.
     *  @access         public
     *  @param        : token,user_id,ride_id,category,sub_category,message,.
     *  @return       : if report successfully gives status 1 otherwise 0";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function report_an_issue(Request $request){

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        //check blocked user
        if ($user = $this->is_blocked($user_id)) {
            return response()->json($user);
        }
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }
        $ride_id = $request->input('ride_id');

        $user_type = $request->input('user_type');
        if ($data = $this->check_empty($user_type, 'Please provide user_type'))
            return response()->json($data, 200);

        $category = $request->input('category');
        if ($data = $this->check_empty($category, 'Please provide category'))
            return response()->json($data, 200);

        $sub_category = $request->input('sub_category');

        $message = $request->input('message');
        if(!empty($ride_id)){
            $insert_data = [
                'user_id' => $user_id,
                'ride_id' => $ride_id,
                'user_type' => $user_type,
                'category' => $category,
                'sub_category' => $sub_category,
                'message' => $message,
            ];
            $insert_id = DB::table('dn_report_an_issuse')->insertGetId($insert_data);
        }else{

            $insert_data = [
                'user_id' => $user_id,
                'user_type' => $user_type,
                'category' => $category,
                'sub_category' => $sub_category,
                'message' => $message,
            ];
            $insert_id = DB::table('dn_report_an_issuse_genral')->insertGetId($insert_data);
        }


        if($insert_id){
            $data = ['status' => 1, 'message' => 'Report successfully!'];

        }else{

            $data = ['status' => 0, 'message' => 'You can not report this!'];
        }
        return response()->json($data);
    }
    /**
     ***********************************************************
     *  Function Name : is_blocked
     *  Functionality : check if user is block or not.
     *  @access         public
     *  @param        : user_id.
     *  @return       : status 3 with message"You are blocked by admin";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function is_blocked($user_id)
    {
        $user_blocked_id = DB::table('dn_users')->where(['active' => '0', 'id' => $user_id])->pluck('id');
        if ($user_blocked_id) {
            return ['status' => 3, 'message' => 'You are blocked by admin'];

        }
    }
    /**
     ***********************************************************
     *  Function Name : is_revoked
     *  Functionality : check if user is revoked or not.
     *  @access         public
     *  @param        : user_id.
     *  @return       : status 10 with message"You are revoked by admin";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function is_revoked($user_id)
    {
        $user_revoked_id = DB::table('role_user')->where(['user_id' => $user_id, 'role_id' => 5])->orderBy('created_at', 'desc')->pluck('id');
        if ($user_revoked_id) {
            return ['status' => 10, 'message' => 'You are revoked by admin'];

        }
    }
    /**
     ***********************************************************
     *  Function Name : pay_bill
     *  Functionality : pay the bill if come on from the cancellation process then need the cancellation data.  .
     *  @access         public
     *  @param        : token,tip,cancellation_data,ride_id.
     *  @return       : return status 1 if success";
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function pay_bill(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $tip = $request->input('tip');
        if ($data = $this->check_integer_empty($tip, 'Please provide tip'))
            return response()->json($data, 200);

        $cancellation_data = $request->input('cancellation_data');
        $cancellation_data = json_decode($cancellation_data);
        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $ride_data = DB::table('dn_rides')->select('id','status','ride_start_time','passenger_id', 'pickup_time','ride_end_time','charge_id','driver_id','arrived_time','arrived_status','driver_latitude','driver_longitude','driver_id')->where(['id' => $ride_id])->first();


        if(!empty($cancellation_data)) {
            $check_exist = DB::table('dn_cancel_rides')->select('id')->where(['ride_id' => $ride_id, 'cancel_by' => $cancellation_data->cancel_by])->first();
            if (empty($check_exist)) {
                $cancellation_insert_data = [
                    'ride_id' => $ride_id,
                    'cancel_by' => $cancellation_data->cancel_by,
                    'user_id' => $cancellation_data->user_id,
                    'category' => $cancellation_data->category,
                    'subcategory' => $cancellation_data->subcategory,
                    'message' => $cancellation_data->message,
                ];
                DB::table('dn_cancel_rides')->insertGetId($cancellation_insert_data);
            }
            if($cancellation_data->cancel_by == 'driver'){
                $contact_number = DB::table('dn_users')->select(DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->passenger_id])->first();
                // $is_sent= $this->twileo_send($contact_number->contact_number,'driver');
                $to = $ride_data->passenger_id;
                $from = $ride_data->driver_id;
                $passenger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->driver_id])->first();
                $driver_profile_pic =DB::table('dn_users_data')->where(['user_id' => $ride_data->driver_id])->pluck('driver_profile_pic');
                if($driver_profile_pic){
                    $passenger_info->profile_pic = $driver_profile_pic;
                }else{
                    $passenger_info->profile_pic = "";
                }
            }else{
                $contact_number = DB::table('dn_users')->select(DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->driver_id])->first();
                // $is_sent= $this->twileo_send($contact_number->contact_number,'passenger');
                $to = $ride_data->driver_id;
                $from = $ride_data->passenger_id;
                $passenger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'profile_pic',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->passenger_id])->first();
            }
            $const = $this->firebaseConstant();
            $DEFAULT_PATH = '/DeziNow';
            $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);
            $firebase->update($DEFAULT_PATH . '/rides/' .$ride_id , ['status' => '-1', 'ride_message'=>'Ride has been cancelled']);
            $firebase->update($DEFAULT_PATH . '/drivers/' .$ride_data->driver_id , ['is_hired' => 0,'driver_available_is_hired' => 'true_0','driver_available' => true]);
            $check_notification = $this->sendGoogleCloudMessage('Ride has been cancelled', '8', $passenger_info, $to, $from, $ride_id);
        }

        $this->pay_amount($ride_id,$tip);

    }
    /**
     ***********************************************************
     *  Function Name : pay_amount
     *  Functionality : pay the bill  .
     *  @access         public
     *  @param        : tip,ride_id.
     *  @return       : return nothing if success otherwise status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function pay_amount($ride_id,$tip)
    {


        $billing_info = DB::table('ride_billing_info')->select('*')->where(['ride_id' => $ride_id])->orderBy('id', 'desc')->first();
        $ride_data = DB::table('dn_rides')->select('id','status','ride_start_time','passenger_id', 'payment_id','pickup_longitude','pickup_latitude','payment_status','driver_level')->where(['id' => $ride_id])->first();
        $payment_token = DB::table('dn_payment_accounts')->where(['user_id' => $ride_data->passenger_id,'is_default' => 1,'is_delete' => 0])->pluck('payment_token');
        if(empty($payment_token)){
            if ($data = $this->check_empty($payment_token, 'Please select the default payment method'))
                return response()->json($data, 200);
        }
        $fair =  $billing_info->total_charges+$tip;

        $dezi_credit = DB::table('dn_passenger_credits')->select('credit_balance')->where(['user_id' => $ride_data->passenger_id])->orderBy('id', 'desc')->first();
        if($dezi_credit){

            $dezi_credit = $dezi_credit->credit_balance;
        }else{
            $dezi_credit = 0.00;
        }

        $driver_earning = $billing_info->driver_earnings + $tip;
        if($ride_data->payment_status != 1) {
            if ($dezi_credit <= $fair) {
                //braintree payment
                $payble_amount = $fair-$dezi_credit;
                $pay = \Braintree_Transaction::sale([
                    'amount' => $payble_amount,
                    'paymentMethodToken' => $payment_token

                ]);
                if ($pay->success == FALSE) {

                    $data = ['status' => 1, 'payment_status' => 0,'message'=>'your payment has been failed due to some reason ,Please add new payment method or contact your service provider','message_braintree'=>$pay->transaction->processorResponseText];
                    DB::table('dn_rides') ->where(['id' => $ride_id])->update([ 'payment_status' => 2 ]);
                    $user_check = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'email', 'is_social')->where('id', $ride_data->passenger_id)->first();
                    $this->payment_failed_mail($user_check->email, $user_check->first_name,'Payment failure');


                } else {

                    if ($pay->transaction->paymentInstrumentType == 'paypal_account') {
                        $insert_data = [
                            'user_id' => $ride_data->passenger_id,
                            'ride_id' => $ride_id,
                            'amount' => $pay->transaction->amount,
                            'tip_percentage' => $tip,
                            'dezicredit' => $dezi_credit,
                            'driver_earning' => $driver_earning,
                            'payment_id' => $pay->transaction->paypal['paymentId'],
                            'payment_type' => 'paypal_account',
                            'merchantAccountId' => $pay->transaction->merchantAccountId,
                            'status' => $pay->transaction->processorResponseText,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    } else {
                        // echo $pay->transaction->amount; die;
                        $insert_data = [
                            'user_id' => $ride_data->passenger_id,
                            'ride_id' => $ride_id,
                            'amount' => $pay->transaction->amount,
                            'tip_percentage' => $tip,
                            'dezicredit' => $dezi_credit,
                            'driver_earning' => $driver_earning,
                            'payment_id' => $pay->transaction->creditCard['uniqueNumberIdentifier'],
                            'payment_type' => 'credit_card',
                            'merchantAccountId' => $pay->transaction->merchantAccountId,
                            'status' => $pay->transaction->processorResponseText,
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }

                    $is_payment = DB::table('dn_payments')->insertGetId($insert_data);

                    //current position of driver

                    DB::table('dn_rides')->where(['id' => $ride_id])->update([ 'charge_id' => $is_payment,'payment_status' => 1 ]);
                    DB::table('ride_billing_info')->where(['id' => $billing_info->id])->update([ 'tip' => $tip,'total_charges' => $fair,'driver_earnings'=>$driver_earning]);
                    DB::table('dn_passenger_credits')->insertGetId($insert_data = ['user_id' => $ride_data->passenger_id, 'debit_amount'=>$dezi_credit,'credit_balance'=>'0','credit_txn_type'=>'Dr' ]);
                    $data = ['status' => 1, 'payment_status' => 1];
                }
            } else if ($dezi_credit > $fair) {
                $left_dezi_credit = $dezi_credit-$fair;
                $insert_data = [
                    'user_id' => $ride_data->passenger_id,
                    'ride_id' => $ride_id,
                    'amount' => 0,
                    'tip_percentage' => $tip,
                    'dezicredit' => $fair,
                    'driver_earning' => $driver_earning,
                    'payment_id' => 0,
                    'payment_type' => 'dezicredit',
                    'merchantAccountId' => 0,
                    'status' => 'Approved',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $is_payment = DB::table('dn_payments')->insertGetId($insert_data);

                //current position of driver

                DB::table('dn_rides')
                    ->where(['id' => $ride_id])
                    ->update([ 'charge_id' => $is_payment,'payment_status' => 1 ]);
                DB::table('ride_billing_info')->where(['id' => $billing_info->id])->update([ 'tip' => $tip,'total_charges' => $fair,'driver_earnings'=>$driver_earning ]);
                DB::table('dn_passenger_credits')->insertGetId($insert_data = ['user_id' => $ride_data->passenger_id, 'debit_amount'=>$fair,'credit_balance'=>$left_dezi_credit,'credit_txn_type'=>'Dr' ]);
                $data = ['status' => 1, 'payment_status' => 1];

            }
        }else{
            $data = ['status' => 1, 'payment_status' => 1,'message' => 'Payment already done!'];

        }
        echo json_encode($data);
    }
    /**
     ***********************************************************
     *  Function Name : user_data
     *  Functionality : return the user data belongs to that specific ride.
     *  @access         public
     *  @param        : token,tip,ride_id.
     *  @return       : return user data  if success otherwise status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function user_data(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $ride_data = DB::table('dn_rides')->select('id','passenger_id','driver_id')->where(['id' => $ride_id])->first();
        if ($ride_data) {
            $passanger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name','profile_pic')->where(['id' => $ride_data->passenger_id])->first();
            $driver_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'profile_pic')->where(['id' => $ride_data->driver_id])->first();
            $driver_profile_pic =DB::table('dn_users_data')->where(['user_id' => $ride_data->driver_id])->pluck('driver_profile_pic');
            if($driver_profile_pic){
                $driver_info->profile_pic = $driver_profile_pic;
            }else{
                $driver_info->profile_pic = "";
            }
            $billing_info = DB::table('ride_billing_info')->select('*')->where(['ride_id' => $ride_id])->first();
            $data = ['status' => 1, 'passanger_info' => $passanger_info,'driver_info' => $driver_info,'billing_info' => $billing_info];

            return response()->json($data);

        }
    }
    /**
     ***********************************************************
     *  Function Name : distance_calculate
     *  Functionality : calculate the distance nad time between to lat,long.
     *  @access         public
     *  @param        : $lat1, $lon1, $lat2, $lon2, $unit.
     *  @return       : return $dis_miles  if success otherwise status flag;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/
    public function distance_calculate($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $data_str = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=$lat1,$lon1&destinations=$lat2,$lon2");
        $data_arr = json_decode($data_str);
        $zero_result = $data_arr->rows[0]->elements[0]->status;//meters
        if($zero_result == 'ZERO_RESULTS'){
            return 'flag';

        }else if($zero_result == 'OK'){
            $distance_meter = $data_arr->rows[0]->elements[0]->distance->value; //meters
            $dis_miles = ($distance_meter / 1609.344);
            return $dis_miles;
        }
    }
    /**
     ***********************************************************
     *  Function Name : sendGoogleCloudMessage
     *  Functionality : send the push notification on mobile device.
     *  @access         public
     *  @param        : $title, $type, $data, $to, $from, $ride_id = ''.
     *  @return       : return 1  if success otherwise 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function sendGoogleCloudMessage($title, $type, $data, $to, $from, $ride_id = '')
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
            'message'  => $title,
            'ride_id' => $ride_id,
            'badge' => $badge,
            'notification_type' => $type,
            'rider_info' => $data,
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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        $result = curl_exec($ch);
        //  var_dump($result); die;
        $result= json_decode($result);
        if ($result->message_id) {
            //insert notification in db
            $insert_data = [
                'sender_id' => $from,
                'receiver_id' => $to,
                'notification_type' => $type,
                'ride_id' => $ride_id,
                'alert' => $title,
                'is_read' => '0'

            ];

            DB::table('dn_notifications')->insertGetId($insert_data);

            return 1;
        } else {
            return 0;
        }
        curl_close($ch);
    }
    /**
     ***********************************************************
     *  Function Name : get_distance_time
     *  Functionality : send the push notification on mobile device.
     *  @access         private
     *  @param        : $originLat, $originLong, $desLat, $desLong.
     *  @return       : return the data from google api;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function get_distance_time($originLat, $originLong, $desLat, $desLong)
    {
        $data_str = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=$originLat,$originLong&destinations=$desLat,$desLong");
        $data_arr = json_decode($data_str);
        return $data_arr->rows[0]->elements;
    }

    private function get_role_id($role)
    {

        $driver_role_id = DB::table('roles')->where('slug', $role)->pluck('id');
    }
    /**
     ***********************************************************
     *  Function Name : sendEmailReminder
     *  Functionality : send the email to the user.
     *  @access         private
     *  @param        : $email, $name, $token, $subject.
     *  @return       : return 1 id success otherwise 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function sendEmailReminder($email, $name, $token, $subject)
    {
        Mail::send('app.mails.forgot-password', ['name' => $name, 'token' => $token], function ($m) use ($email, $name, $token, $subject) {
            $m->from('dezinow@example.com', 'DeziNow');

            $m->to($email, $name)->subject($subject);

        });

        if (count(Mail::failures()) > 0) {
            return 0;
        }

        return 1;
    }
    private function payment_failed_mail($email, $name, $subject)
    {
        Mail::send('app.mails.payment_failed', ['name' => $name], function ($m) use ($email, $name, $subject) {
            $m->from('dezinow@example.com', 'DeziNow');

            $m->to($email, $name)->subject($subject);
        });

        if (count(Mail::failures()) > 0) {
            return 0;
        }

        return 1;
    }
    /**
     ***********************************************************
     *  Function Name : firebaseConstant
     *  Functionality : define the firebase contant.
     *  @access         private
     *  @param        : none.
     *  @return       : return firebase contant;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function firebaseConstant(){

        $DEFAULT_URL = 'https://dezinow-b9118.firebaseio.com/';
        $DEFAULT_TOKEN = 'Pmz0GmyhiG5zadghI04Hf9CnuDfB3CZtMzSwVtq3';
        return ['DEFAULT_URL' => $DEFAULT_URL, 'DEFAULT_TOKEN' => $DEFAULT_TOKEN];
    }
    /**
     ***********************************************************
     *  Function Name : rating
     *  Functionality : get the user rating based on the user_id.
     *  @access         private
     *  @param        : user_id.
     *  @return       : return user rating if success otherwise return 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    private function rating ($user_id){

        $total_rating  = DB::table('dn_rating')->select('rating')->where(['driver_id' => $user_id])->get();
        if($total_rating){

            $sumArray = 0;
            $count = 0;
            foreach ($total_rating as $k=>$subArray) {
                foreach ($subArray as $id=>$value) {
                    $sumArray += $value;
                    $count += count($k);
                }
            }
            $rating  = $sumArray/$count;
            return round($rating);
        }else{
            return 0;
        }
    }
    /**
     ***********************************************************
     *  Function Name : get_cityname
     *  Functionality : get the city name basis on the lat and long.
     *  @access         public
     *  @param        : user_id.
     *  @return       : return admin charges basis on the city;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function get_cityname ($lat,$long,$pickup_time,$ride_id){
        $geolocation = $lat.','.$long;
        $request = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false';
        $file_contents = file_get_contents($request);
        $json_decode = json_decode($file_contents);
        if(isset($json_decode->results[0])) {
            $response = array();
            foreach($json_decode->results[0]->address_components as $addressComponet) {
                if(in_array('political', $addressComponet->types)) {
                    $response[] = $addressComponet->long_name;
                }
            }

        }
        $city=$response[0];
        $state=$response[2];
        if(!empty($city)){
            $get_city_id = DB::table('dn_cities')->select('*')->where(['city' => $city])->orderBy('id', 'desc')->pluck('id');
            if(!empty($ride_id)){
                DB::table('dn_rides')->where(['id' => $ride_id])->update(array('city_name' => $city,'state_name'=>$state));
            }
            if($get_city_id){
                $dayID=date('N', strtotime($pickup_time));
                $dayID++;
                if($dayID==8){$dayID=1;}

                $adminCharges_array = DB::table('dn_driver_charges')->select('*')->where(['city_id' => $get_city_id])->orderBy('id', 'desc')->get();

                if(empty($adminCharges_array)){
                    $adminCharges = DB::table('dn_driver_charges')->select('*')->where(['id' => 1])->orderBy('id', 'desc')->first();
                }else{

                    foreach ($adminCharges_array as $charges) {
                        if($charges->day_number == $dayID){

                            $pickup_timestrtotime = strtotime($pickup_time);
                            $from_time = str_replace(' ', '',$charges->from_time);
                            $from_time1 =   date("H:i:s", strtotime($from_time));
                            $from_time2 =  date('Y-m-d').' '.$from_time1;
                            $from_time3 =  strtotime($from_time2);
                            $to_time = str_replace(' ', '',$charges->to_time);
                            $to_time1 =   date("H:i:s", strtotime($to_time));
                            $to_time2 =  date('Y-m-d').' '.$to_time1;
                            $to_time3 =  strtotime($to_time2);
                            if(($from_time3 <= $pickup_timestrtotime) && ($to_time3 >= $pickup_timestrtotime)){
                                $adminCharges = $charges;
                                break;
                            }

                        }
                    }

                    if(empty($adminCharges)){

                        $adminCharges = DB::table('dn_driver_charges')->select('*')->where(['city_id' => $get_city_id,'day_number'=> 0])->orderBy('id', 'desc')->first();
                    }
                }

            }else{

                $adminCharges = DB::table('dn_driver_charges')->select('*')->where(['id' => 1])->orderBy('id', 'desc')->first();
            }
        }else{
            $adminCharges =  DB::table('dn_driver_charges')->select('*')->where(['id' => 1])->orderBy('id', 'desc')->first();;

        }
        return $adminCharges;
    }
    /**
     ***********************************************************
     *  Function Name : current_ride_info
     *  Functionality : get current ride info based on the ride_id.
     *  @access         public
     *  @param        : token,ride_id.
     *  @return       : return ride info if success otherwise gives error with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function current_ride_info(Request $request){

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_integer_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $ride_data = DB::table('dn_rides')->select('*')->where(['id' => $ride_id])->first();
        if($ride_data){
            $driver_info = DB::table('dn_users')->select('id', 'first_name', 'last_name',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->driver_id])->first();
            $driver_profile_pic =DB::table('dn_users_data')->where(['user_id' => $ride_data->driver_id])->pluck('driver_profile_pic');
            if($driver_profile_pic){
                $driver_info->profile_pic = $driver_profile_pic;
            }else{
                $driver_info->profile_pic = "";
            }
            $driver_rating =   DB::table('dn_rating')->selectRaw('sum(rating) as sum')->where('driver_id', $ride_data->driver_id)->get();
            $driver_rating =  $driver_rating[0]->sum;
            $row_count = DB::table('dn_rating')->select('id')->where(['driver_id' => $ride_data->driver_id])->count();
            $net_rating = (string)round($driver_rating/$row_count);
            $passenger_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', 'profile_pic',DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $ride_data->passenger_id])->first();
            $driver_info->rating = $net_rating;
            $ride_data->passenger_info = $passenger_info;
            $ride_data->driver_info = $driver_info;
            $car_info = DB::table('dn_user_cars')->select('make', 'transmission', 'number', 'model')->where(['user_id' => $ride_data->passenger_id])->first();
            $ride_data->car_info = $car_info;
            return response()->json(['status' => 1, 'data' =>$ride_data ]);
        }else{

            return response()->json(['status' => 0, 'message' => 'The Ride you are requesting is not active']);
        }

    }
    /**
     ***********************************************************
     *  Function Name : passenger_promo
     *  Functionality : apply the promo code and credit goes to the user account.
     *  @access         public
     *  @param        : token,user_id,promo_code.
     *  @return       : return ride info if success otherwise gives error with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function passenger_promo(Request $request){
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $user_id = $request->input('user_id');
        if ($data = $this->check_integer_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        $promo_code = $request->input('promo_code');
        if ($data = $this->check_empty($promo_code, 'Please provide promo_code'))
            return response()->json($data, 200);

        $dezi_credit = DB::table('dn_passenger_credits')->select('credit_balance')->where(['user_id' => $user_id])->orderBy('id', 'desc')->first();
        if($dezi_credit){
            $dezi_credit = $dezi_credit->credit_balance;
        }else{
            $dezi_credit = 0;
        }
        $code_validate = DB::table('dn_passenger_promo_code')->select('*')->where(['code' => $promo_code,'status' => '1'])->orderBy('id', 'desc')->first();
        if($code_validate){

            $used_check = DB::table('dn_passenger_promo_code_uses')->select('id')->where(['promo_code_id' => $code_validate->id,'user_id' => $user_id])->orderBy('id', 'desc')->first();
            if($code_validate->promo_multiple == 1){
                $used_check = null;
            }
            if(is_null($used_check)){
                $credit_balance = $dezi_credit + $code_validate->amount;
                $today_date = strtotime(date('Y-m-d'));
                $till_validate = strtotime($code_validate->valid_till);
                $add_days = 15;
                $sub_days = 15;
                if($code_validate->type == 'birthday'){
                    $get_birthdate =DB::table('dn_users')->where(['id' => $user_id])->pluck('dob');
                    $upper_date = date('Y-m-d',strtotime($get_birthdate) + (24*3600*$add_days));
                    $upper_date = explode('-', $upper_date);
                    unset($upper_date[0]);
                    array_unshift($upper_date, date('Y'));
                    $upper_date =implode("-",$upper_date);
                    $upper_date = strtotime($upper_date);
                    $lower_date = date('Y-m-d',strtotime($get_birthdate) - (24*3600*$sub_days));
                    $lower_date = explode('-', $lower_date);
                    unset($lower_date[0]);
                    array_unshift($lower_date, date('Y'));
                    $lower_date =implode("-",$lower_date);
                    if($lower_date[1] == 12){
                        $lower_date = date('Y-m-d', strtotime('-1 year'));
                    }
                    $lower_date = strtotime($lower_date);
                    if($today_date > $lower_date AND $today_date < $upper_date){
                    }else{
                        return response()->json(['status' => 0, 'message' => 'Your birthday promo is expired!']);
                    }
                }
                if($code_validate->type == 'ani'){
                    $get_anidate =DB::table('dn_users')->where(['id' => $user_id])->pluck('anniversary');
                    $upper_date = date('Y-m-d',strtotime($get_anidate) + (24*3600*$add_days));
                    $upper_date = explode('-', $upper_date);
                    unset($upper_date[0]);
                    array_unshift($upper_date, date('Y'));
                    $upper_date =implode("-",$upper_date);
                    $upper_date = strtotime($upper_date);
                    $lower_date = date('Y-m-d',strtotime($get_anidate) - (24*3600*$sub_days));
                    $lower_date = explode('-', $lower_date);
                    unset($lower_date[0]);
                    array_unshift($lower_date, date('Y'));
                    $lower_date =implode("-",$lower_date);
                    if($lower_date[1] == 12){
                        $lower_date = date('Y-m-d', strtotime('-1 year'));
                    }
                    $lower_date = strtotime($lower_date);
                    if($today_date > $lower_date AND $today_date < $upper_date){
                    }else{
                        return response()->json(['status' => 0, 'message' => 'Your anniversary promo is expired!']);
                    }
                }
                if($code_validate->type == 'normal'){
                    if($today_date >$till_validate){
                        return response()->json(['status' => 0, 'message' => 'Your promo code is expired!']);

                    }

                }
                if($code_validate->type == 'new_rider_promotion'){
                    $get_ride =DB::table('dn_rides')->where(['passenger_id' => $user_id])->pluck('id');
                    if(!empty($get_ride)){
                        return response()->json(['status' => 0, 'message' => 'Your promo code is expired!']);
                    }
                }
                $inserted = DB::table('dn_passenger_credits')->insertGetId(['user_id' => $user_id, 'credit_type' => '3', 'credit_amount' => $code_validate->amount, 'credit_txn_type' => 'DR', 'credit_balance' => $credit_balance]);
                if ($inserted) {
                    DB::table('dn_passenger_promo_code_uses')->insertGetId(['user_id' => $user_id, 'promo_code_id' => $code_validate->id]);
                    return response()->json(['status' => 1, 'message' => 'Code applied successfully!','dezi_amount'=>number_format((float)$credit_balance, 2, '.', '')]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'You can not apply this code!']);
                }
            }else{
                return response()->json(['status' => 0, 'message' => 'You have already applied this code!']);
            }

        }else{

            return response()->json(['status' => 0, 'message' => 'Code is wrong/expired!']);
        }

    }
    /**
     ***********************************************************
     *  Function Name : driver_reward
     *  Functionality : get the hours, peak hour , tier level of driver and return to the app.
     *  @access         public
     *  @param        : token,user_id,user_id,.
     *  @return       : return the hours, peak hour , tier level of driver  if success otherwise gives error with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function driver_reward(Request $request){
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $user_id = $request->input('user_id');
        if ($data = $this->check_integer_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);
        if ($user = $this->is_revoked($user_id)) {
            return response()->json($user);
        }

        $is_driver = $request->input('is_driver');
        if ($data = $this->check_integer_empty($is_driver, 'Please provide is_driver'))
            return response()->json($data, 200);

        $driver_exist  = DB::table('dn_users')->select('id')->where(['id' => $user_id])->first();
        if($driver_exist){
            if($is_driver == 1) {

                $reward = DB::table('dn_users_data')->select('dn_driver_tier_data.total_active_hours as hour','dn_driver_tier_data.scheduled_hours as pick_hours','dn_driver_tier_data.cancelation_rate as cancellation_rate','dn_driver_tier_data.acceptance_rate as acceptance_level','dn_users_data.tiers_level')->leftJoin('dn_driver_tier_data', 'dn_users_data.user_id', '=', 'dn_driver_tier_data.driver_id')
                    ->Where(['dn_users_data.user_id' => $user_id])
                    ->first();
                if(is_null($reward->hour))
                {
                    $reward->hour = '';
                }
                if(is_null($reward->cancellation_rate))
                {
                    $reward->cancellation_rate = '';
                }
                if(is_null($reward->acceptance_level))
                {
                    $reward->acceptance_level = '';
                }
                $referral_bonus =DB::table('dn_driver_dezibunus')->where(['user_id' => $user_id,'bonus_type' => '1'])->orderBy('id', 'desc')->pluck('bonus_balance');
                if(is_null($referral_bonus)){

                    $referral_bonus = "0.00";
                }
                $reward->referral_bonus = $referral_bonus;
                $referrals = DB::table('dn_user_referrals')->select('dn_user_referrals.status', 'dn_user_referrals.user_id', 'dn_users.first_name', 'dn_users.last_name')
                    ->leftJoin('dn_users', 'dn_user_referrals.user_id', '=', 'dn_users.id')
                    ->where(['dn_user_referrals.referred_by' => $user_id, 'dn_user_referrals.referral_type' => '4'])
                    ->get();
                $reward->referrals = $referrals;
            }else{
                $reward = array();
                $referral_bonus = DB::table('dn_passenger_credits')->select( DB::raw('SUM(credit_amount) as total_sales')) ->where(['user_id' => $user_id,'credit_type'=>'2'])
                    ->pluck('total_sales');;

                if(empty($referral_bonus)){

                    $referral_bonus = "0.00";
                }
                $reward['referral_bonus'] = $referral_bonus;

                $referrals = DB::table('dn_user_referrals')->select('dn_user_referrals.status', 'dn_user_referrals.user_id', 'dn_users.first_name', 'dn_users.last_name')
                    ->leftJoin('dn_users', 'dn_user_referrals.user_id', '=', 'dn_users.id')
                    ->where(['dn_user_referrals.referred_by' => $user_id, 'dn_user_referrals.referral_type' => '3'])
                    ->get();
                $reward['referrals'] = $referrals;


            }

            return response()->json(['status' => 1, 'data' =>$reward ]);
        }else{

            return response()->json(['status' => 0, 'message' => 'Driver does not exist!']);
        }
    }
    /*------------------------------------------------------------------------
  Function name : twilio_token()
  Author        : Rubiya
  Description   : This Api is required to generate the token for the application so that users or driver can connect to twilio services.
                  Max time for the token is 24 hours.
  CTM           : Please update Twilio sid, token and appsid if using another twilio account
  --------------------------------------------------------------------------*/

    public function twilio_token(Request $request)
    {
        include(app_path().'/customLib/twilio/Services/Twilio.php');
        include(app_path().'/customLib/twilio/Services/Twilio/Capability.php');

        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $account_sid = 'ACef0bc2ba66b70340468cc67fca14390d';
        $auth_token = 'e12d7d9fac857f1b845d19e8e9bde841';
        $appSid = 'APa414a9e28dff7d3a0962b782ef9ce053';

        $user_id = $request->input('user_id');
        if ($data = $this->check_integer_empty($user_id, 'Please provide user_id'))
            return response()->json($data, 200);

        $capability = new Services_Twilio_Capability($account_sid, $auth_token);
        $capability->allowClientOutgoing($appSid);
        $capability->allowClientIncoming($user_id."_twilio");
        $token = $capability->generateToken(18000);

        if ($token) {
            return response()->json(['status' => 1, 'message' => 'Token generated successfully', 'token' => $token], 200);
        }else{
            return response()->json(['status' => 0, 'message' => 'Something went wrong! Unable to generate Token.'], 200);
        }

    }


    /*------------------------------------------------------------------------
    Function name : xml_client()
    Author        : Rubiya
    Description   : This Api is required to generate get the dial call response.
    CTM           : While project is moving to client server, please check is the project under the parent directory. If not then not concatinate the $parent varibale with $url
                    Also in logfile.php, establish the client database connection
    --------------------------------------------------------------------------*/

    public function xml_client() {
        $dir = $_SERVER['SERVER_NAME'];
        // $parent = basename(dirname($_SERVER['PHP_SELF']));
        $url =  $dir."/" ;

        include(app_path().'/customLib/twilio/Services/Twilio.php');
        include(app_path().'/customLib/twilio/Services/Twilio/Twiml.php');
        $k = print_r( $_POST, true );
        $myFile = "testFile.txt";

        $fh = fopen($myFile, 'a') or die("can't open file");
        $stringData = $k;
        fwrite($fh, $stringData);

        $response = new Services_Twilio_Twiml;

        $dial = $response->dial('', array( 'callerId' => $_POST['From'], 'action' => 'https://'.$url.'logs/logfile.php?fromNo='.$_POST['From'].'&toNo='.$_POST['To']) );

        $to  = $_POST['To'];
        $dial->Number( $to );

        fwrite($fh, $response);

        $account_sid = 'ACef0bc2ba66b70340468cc67fca14390d';

        $auth_token = 'e12d7d9fac857f1b845d19e8e9bde841';

        $client = new Services_Twilio($account_sid, $auth_token);

        $call = $client->account->calls->get($_POST['CallSid']);

        fwrite($fh, $call->status);
        return $response;
    }
    /**
     ***********************************************************
     *  Function Name : history_detail_android
     *  Functionality : this method used only for the android not for IOS, in this we get the detailed for ride.
     *  @access         public
     *  @param        : token,ride_id,user_type.
     *  @return       : return ride info if success otherwise gives error with status 0;
     *  Author        : Manjeet Boora
     ***********************************************************
     **/

    public function history_detail_android(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }

        $ride_id = $request->input('ride_id');
        if ($data = $this->check_empty($ride_id, 'Please provide ride_id'))
            return response()->json($data, 200);

        $user_type = $request->input('user_type');
        $user_id = $request->input('user_id');

        //if ($data = $this->check_empty($user_type, 'Please provide user_type'))
        //ssss  return response()->json($data, 200);

        $ride_data = DB::table('dn_rides')->select('*')->where(['id' => $ride_id])->first();
        if($ride_data) {
            $currunt_timezone = config('app.timezone');
            $user_timezone = $this->get_user_timezone($user_id);
            $ride_data->pickup_time = $this->time_translate($currunt_timezone, $user_timezone, $ride_data->pickup_time);
            $ride_data->ride_start_time = $this->time_translate($currunt_timezone, $user_timezone, $ride_data->ride_start_time);
            $ride_data->ride_end_time = $this->time_translate($currunt_timezone, $user_timezone,$ride_data->ride_end_time);

            if($user_type == 'driver'){

                $user_id = $ride_data->passenger_id;
                $user_info = DB::table('dn_users')->select('id', 'first_name', 'last_name','profile_pic', DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $user_id])->first();

            }else{
                $user_id = $ride_data->driver_id;
                $user_info = DB::table('dn_users')->select('id', 'first_name', 'last_name', DB::raw('concat( country_phone_code, "", contact_number) as contact_number'))->where(['id' => $user_id])->first();
                $driver_profile_pic =DB::table('dn_users_data')->where(['user_id' => $user_id])->pluck('driver_profile_pic');
                if($driver_profile_pic){
                    $user_info->profile_pic = $driver_profile_pic;
                }else{
                    $user_info->profile_pic = "";
                }
            }
            $ride_data->driver_info = $user_info;
            $billing_info = DB::table('ride_billing_info')->select('*')->where(['ride_id' => $ride_id])->first();
            $account_info = DB::table('dn_payment_accounts')->select('account_type', 'account_email', 'masked_number', 'image_url')->where(['id' => $ride_data->payment_id])->first();

            if($billing_info){
                $billing_info->account_type = $account_info->account_type;
                $billing_info->account_email = $account_info->account_email;
                $billing_info->masked_number = $account_info->masked_number;
                $billing_info->image_url = $account_info->image_url;
                $ride_data->billing_info = $billing_info;
            }
            else{
                $ride_data->billing_info = NUlL;
            }
            $data = array('status' => 1, 'data' => $ride_data);
        }else{
            $data = array('status' => 0, 'message' => 'No history found!');
        }

        return response()->json($data);
    }
    public function city_json(Request $request)
    {
        if ($error = $this->key($request->input('token'))) {
            return response()->json(['not valid key'], 200);
        }
        $ride_data = DB::table('dn_states')->get();
        foreach($ride_data as &$ride_id)
        {

            $city = (array)DB::table('dn_cities')->select('*')->where(['state_code' => $ride_id->state_code])->get();
            $ride_id->city = $city;
        }
        return response()->json($ride_data);
    }

    public function get_user_timezone($user_id)
    {
        $user_time_zone = DB::table('dn_users')->where(['id' => $user_id])->pluck('time_zone');
        if (empty($user_time_zone)) {
            $user_time_zone = "America/Los_Angeles";
        }
        return $user_time_zone;
    }
    function time_translate($tz_from, $tz_to, $time_str = 'now', $format = 'Y-m-d H:i:s')
    {
        $dt = new DateTime($time_str, new DateTimezone($tz_from));
        $timestamp = $dt->getTimestamp();
        return $dt->setTimezone(new DateTimezone($tz_to))->setTimestamp($timestamp)->format($format);
    }

    public function queryDriver(){
        $count=1;
        for ($x = 0; $x <= 20; $x++) {

            $email='manjeet1'.$count.'@yopmail.com';
            $unique_code=(300000+$count);
            $role_id=4;
            $passenger_referral_code='PSNEW'.(909+$count);

            $id=DB::table('dn_users')->insertGetId(
                [
                    'unique_code' => $unique_code,
                    'profile_status' => 1,
                    'first_name' => 'Gaurav',
                    'last_name' => 'RIDER',
                    'full_name' => 'Gaurav RIDER',
                    'dob' => '1990-02-17',
                    'gender' => 'male',
                    'profile_pic' => 'uploads/profile-img/bd8d1a7e09a996fc08521e97693643c8.jpg',
                    'anniversary' => '1990-02-17',
                    'state' => 'AK',
                    'city' => 78,
                    'address_1' => 'g',
                    'address_2' =>'g',
                    'zip_code' => '133145',
                    'time_zone' => 'Asia/Kolkata',
                    'country_code' => '',
                    'contact_number' => '8046435285',
                    'country_phone_code' => '+1',
                    'is_social' => '0',
                    'last_ip' => '',
                    'last_login' => '',
                    'remember_token' => '',
                    'access_token' => '',
                    'password_token' => '',
                    'password_token_expired' => 0,
                    'social_id' => '',
                    'name' => '',
                    'email' => $email,
                    'password' => '$2y$10$XMSpciu.PdQz.JSGHScCXuDq0U3eye9/0ML.g0ruFdcVTdYL2TPGK',
                    'device_token' => '5bf2bb2de39761ae',
                    'active' => '1',
                    'is_suspended' => 0,
                    'become_driver_request' => '1',
                    'driver_requested_on' => '2017-01-26 21:49:37',
                    'is_driver_approved' => '1',
                    'driver_approved_on' => '2017-01-26 21:50:20',
                    'driver_verified_by' => 1,
                    'passenger_referral_code' => $passenger_referral_code,
                    'anniversary_promo_check' => 0,
                    'birthday_promo_check' => 0,
                    'is_logged' => 'true',
                    'created_at' => '2017-01-17 12:04:05',
                    'updated_at' => '2017-01-17 04:04:05'
                ]
            );
            $dn_users_data=DB::table('dn_users_data')->insertGetId(
                [
                    'user_id' => $id,
                    'transmission' => 'both',
                    'navigation_system' => 'google_map',
                    'active' => '1',
                    'tiers_level' => '1',
                    'license_number' => '3214697',
                    'ssn' => '653219874',
                    'referral_code' => 'DR32568',
                ]
            );
            $dn_driver_requests_id=DB::table('dn_driver_requests')->insertGetId(
                [

                    'user_id' => $id,
                    'license_verification' => 'uploads/drivers-documents/ecaede1c8b47c028a568c6fad3b86a33.png',
                    'proof_of_insurance' => 'uploads/drivers-documents/cf1c65559e10a33ce51e4c32847f0b7f.png',
                    'licence_expiration' => '2017-04-28',
                    'insurance_expiration' => '2017-02-28',
                    'car_transmission' => 'both',
                    'navigation' => '',
                    'driver_records' => '[{"question":"Have you had more than one accident in the last
                    three years?","answer":"1"},{"question":"Have you ever had more than two
                    points on your driver\\u2019s license?","answer":"1"},{"question":"Have you
                    ever had more than one moving violation in last two
                    years?","answer":"1"},{"question":"Have you been ever arrested for a
                    DUI\\/OVI?","answer":"1"},{"question":"Have you ever been convicted for a
                    crime?","answer":"1"},{"question":"Have you been driving for less than 2
                    years?","answer":"1"},{"question":"Are you less than 21 years of
                    age?","answer":"1"},{"question":"Can you drive a manual(stick)
                    transmission?","answer":"1"},{"question":"Do you have a commercial driver`s
                    license?","answer":"1"},{"question":"How did you hear about
                    DeziNow?","answer":"Hhh"}]',
                    'approved_by' => '0',
                    'created_at' => '2017-01-27 05:49:37',
                ]
            );

            DB::table('role_user')->insertGetId(['role_id' => $role_id, 'user_id' => $id]);

            $const = $this->firebaseConstant();
            $DEFAULT_PATH = '/DeziNow';
            $firebase = new \Firebase\FirebaseLib($const['DEFAULT_URL'], $const['DEFAULT_TOKEN']);

            $user_info = [
                'driver_available' => true,
                'driver_available_is_hired' => 'true_0',
                'is_hired' => 0,
                'latitude' => 30.7086432,
                'longitude'=>76.7020541,
                'transmission'=>'both',
                'updated_at' => 'Driver build from query'
            ];
            $firebase->set($DEFAULT_PATH . '/drivers/' .$id , $user_info);
            $count++;

        } //Loop close 
        echo "25000 driver added successfully with its profile detail.";
    }

    function queryPassenger(){
        $count=1;
        for ($x = 0; $x <= 65000; $x++) {

            $email='neha'.$count.'@yopmail.com';
            $unique_code=(400000+$count);
            $role_id=3;
            $passenger_referral_code='PSNEW'.(606+$count);

            $id=DB::table('dn_users')->insertGetId(
                [
                    'unique_code' => $unique_code,
                    'profile_status' => 1,
                    'first_name' => 'neha',
                    'last_name' => 'RIDER',
                    'full_name' => 'neha RIDER',
                    'dob' => '1986-02-17',
                    'gender' => 'female',
                    'profile_pic' => 'uploads/profile-img/bd8d1a7e09a996fc08521e97693643c8.jpg',
                    'anniversary' => '1986-02-17',
                    'state' => 'AK',
                    'city' => 78,
                    'address_1' => 'g',
                    'address_2' =>'g',
                    'zip_code' => '133145',
                    'time_zone' => 'Asia/Kolkata',
                    'country_code' => '',
                    'contact_number' => '8046435285',
                    'country_phone_code' => '+1',
                    'is_social' => '0',
                    'last_ip' => '',
                    'last_login' => '',
                    'remember_token' => '',
                    'access_token' => '',
                    'password_token' => '',
                    'password_token_expired' => 0,
                    'social_id' => '',
                    'name' => '',
                    'email' => $email,
                    'password' => '$2y$10$XMSpciu.PdQz.JSGHScCXuDq0U3eye9/0ML.g0ruFdcVTdYL2TPGK',
                    'device_token' => '5bf2bb2de39761ae',
                    'active' => '1',
                    'is_suspended' => 0,
                    'become_driver_request' => '0',
                    'driver_requested_on' => '0000-00-00 00:00:00',
                    'is_driver_approved' => '0',
                    'driver_approved_on' => '0000-00-00 00:00:00',
                    'driver_verified_by' => 0,
                    'passenger_referral_code' => $passenger_referral_code,
                    'anniversary_promo_check' => 0,
                    'birthday_promo_check' => 0,
                    'is_logged' => 'true',
                    'created_at' => '2017-01-17 12:04:05',
                    'updated_at' => '2017-01-17 04:04:05'
                ]
            );

            $dn_favorite_places_id=DB::table('dn_favorite_places')->insertGetId(
                [
                    'user_id' => $id,
                    'place_name' => 'HOME',
                    'address' => 'E-37, Phase 8, Industrial Area, Sahibzada Ajit Singh Nagar, Punjab 160071, India',
                    'city' => 'Sahibzada Ajit Singh Nagar',
                    'state' => 'Punjab',
                    'zip' => '160071',
                    'latitude' => '30.707771482368333',
                    'longitude' => '76.70294762543926',
                    'is_default' => '1',
                    'created_at' => '2017-02-16 03:06:27',
                    'updated_at' => '0000-00-00 00:00:00'

                ]
            );



            $dn_user_cars_id=DB::table('dn_user_cars')->insertGetId(
                [
                    'user_id' => $id,
                    'make' => 'honda',
                    'transmission' => 'automatic',
                    'number' => 'HR AB40 42020',
                    'model' => 'city',
                    'year' => '2017',
                    'is_default' => '1',
                    'is_delete' => '0',
                    'added_at' => '2017-02-16 03:07:27'

                ]
            );

            $dn_payment_accounts_id=DB::table('dn_payment_accounts')->insertGetId(
                [
                    'user_id' => $id,
                    'account_type' => 'paypal',
                    'card_type' => '',
                    'account_email' => 'bt_buyer_us@paypal.com',
                    'card_identifier' => '',
                    'masked_number' => '',
                    'payment_token' => 'k7mbg4',
                    'expiration_date' => '',
                    'image_url' => 'https://assets.braintreegateway.com/payment_method_logo/paypal.png?environment=sandbox',
                    'card_last_4' => '',
                    'expired' => '',
                    'payroll' => '',
                    'is_default' => '1',
                    'is_delete' => '0',
                    'created_at' => '2017-02-16 03:07:27',
                    'updated_at' => '2017-02-16 03:07:27'

                ]
            );

            DB::table('role_user')->insertGetId(['role_id' => $role_id,
                'user_id' => $id]);

            $count++;


        } //Loop close
        echo "25000 passenger added successfully with its fav place, car, payment and profile detail.";
    }
    function queryPassenger2(){
        $count=1;
        for ($x = 0; $x <= 100000; $x++) {

            $email='taran'.$count.'@yopmail.com';
            $unique_code=(400000+$count);
            $role_id=3;
            $passenger_referral_code='PSNEW'.(606+$count);

            $id=DB::table('dn_users')->insertGetId(
                [
                    'unique_code' => $unique_code,
                    'profile_status' => 1,
                    'first_name' => 'taran',
                    'last_name' => 'RIDER',
                    'full_name' => 'taran RIDER',
                    'dob' => '1989-02-17',
                    'gender' => 'female',
                    'profile_pic' => 'uploads/profile-img/bd8d1a7e09a996fc08521e97693643c8.jpg',
                    'anniversary' => '1989-02-17',
                    'state' => 'AK',
                    'city' => 78,
                    'address_1' => 'g',
                    'address_2' =>'g',
                    'zip_code' => '133145',
                    'time_zone' => 'Asia/Kolkata',
                    'country_code' => '',
                    'contact_number' => '8046435285',
                    'country_phone_code' => '+1',
                    'is_social' => '0',
                    'last_ip' => '',
                    'last_login' => '',
                    'remember_token' => '',
                    'access_token' => '',
                    'password_token' => '',
                    'password_token_expired' => 0,
                    'social_id' => '',
                    'name' => '',
                    'email' => $email,
                    'password' => '$2y$10$XMSpciu.PdQz.JSGHScCXuDq0U3eye9/0ML.g0ruFdcVTdYL2TPGK',
                    'device_token' => '5bf2bb2de39761ae',
                    'active' => '1',
                    'is_suspended' => 0,
                    'become_driver_request' => '0',
                    'driver_requested_on' => '0000-00-00 00:00:00',
                    'is_driver_approved' => '0',
                    'driver_approved_on' => '0000-00-00 00:00:00',
                    'driver_verified_by' => 0,
                    'passenger_referral_code' => $passenger_referral_code,
                    'anniversary_promo_check' => 0,
                    'birthday_promo_check' => 0,
                    'is_logged' => 'true',
                    'created_at' => '2017-01-17 12:04:05',
                    'updated_at' => '2017-01-17 04:04:05'
                ]
            );

            $dn_favorite_places_id=DB::table('dn_favorite_places')->insertGetId(
                [
                    'user_id' => $id,
                    'place_name' => 'HOME',
                    'address' => 'E-37, Phase 8, Industrial Area, Sahibzada Ajit Singh Nagar, Punjab 160071, India',
                    'city' => 'Sahibzada Ajit Singh Nagar',
                    'state' => 'Punjab',
                    'zip' => '160071',
                    'latitude' => '30.707771482368333',
                    'longitude' => '76.70294762543926',
                    'is_default' => '1',
                    'created_at' => '2017-02-16 03:06:27',
                    'updated_at' => '0000-00-00 00:00:00'

                ]
            );



            $dn_user_cars_id=DB::table('dn_user_cars')->insertGetId(
                [
                    'user_id' => $id,
                    'make' => 'honda',
                    'transmission' => 'automatic',
                    'number' => 'HR AB40 42020',
                    'model' => 'city',
                    'year' => '2017',
                    'is_default' => '1',
                    'is_delete' => '0',
                    'added_at' => '2017-02-16 03:07:27'

                ]
            );

            $dn_payment_accounts_id=DB::table('dn_payment_accounts')->insertGetId(
                [
                    'user_id' => $id,
                    'account_type' => 'paypal',
                    'card_type' => '',
                    'account_email' => 'bt_buyer_us@paypal.com',
                    'card_identifier' => '',
                    'masked_number' => '',
                    'payment_token' => 'k7mbg4',
                    'expiration_date' => '',
                    'image_url' => 'https://assets.braintreegateway.com/payment_method_logo/paypal.png?environment=sandbox',
                    'card_last_4' => '',
                    'expired' => '',
                    'payroll' => '',
                    'is_default' => '1',
                    'is_delete' => '0',
                    'created_at' => '2017-02-16 03:07:27',
                    'updated_at' => '2017-02-16 03:07:27'

                ]
            );

            DB::table('role_user')->insertGetId(['role_id' => $role_id,
                'user_id' => $id]);

            $count++;
        } //Loop close
        echo "25000 passenger added successfully with its fav place, car, payment and profile detail.";
    }
}