<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/permissionErr',function(){
	 return view('errors.permissionERR');
});
/*--route for home page start--*/
Route::get('/', array('as' => '/', 'uses' => function(){
  
  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }

  return view('index');

}));
/*--//route for home page end--*/

/*--route for about us start--*/
Route::get('/about', array('as' => '/about', 'uses' => function(){
  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }		
  return view('about');
}));
/*--//route for about us end--*/

/*--route for contact us start--*/
Route::get('/contact', array('as' => '/contact', 'uses' => function(){

  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }	
  return view('contact');
}));
Route::post('contact', 'home@contact');
/*--//route for contact us end--*/

Route::get('howitworks', array('as' => '/howItWorks', 'uses' => function(){
  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }	
  return view('about');
}));

Route::get('privacy-policy', array('as' => '/privacy-policy', 'uses' => function(){
  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }	
  return view('privacy');
}));

/*--route for login start--*/
Route::get('login', function() {
  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }	
  return View::make('login');
});
Route::post('login', 'home@login');
/*--//route for login end--*/

/*--route for logout start--*/
Route::get('logout', array('uses' => 'home@logout'));
/*--//route for logout end--*/

/*--route for signup start--*/
Route::get('signup', function() {
	  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }
  return View::make('signup');
});
Route::post('signup', array('uses' => 'home@signup'));
/*--//route for signup end--*/

/*--route for forgot password request start--*/
Route::get('forgot', function() {
  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }	
  return View::make('forgot');
});
Route::post('forgot', array('uses' => 'home@forgot_password'));
/*--//route for forgot password request end--*/

/*--route for reset password start--*/
Route::get('resetpassword', function() {
  $id = Auth::id();
  
  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
  
  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

      return Redirect::intended('/admin');
  }	
  return View::make('resetpassword');
});
Route::post('resetpassword', array('uses' => 'home@resetpassword'));
/*--//route for reset password end--*/


/*--route for passenger profile start--*/
//Route::get('profile', array('uses' => 'home@passengerprofile'));
/*--//route for passenger profile end--*/


/*--route for reset password start--*/
Route::get('phoneverification', function() {
	
  return View::make('phoneverification');
});
Route::post('phoneverification', array('uses' => 'home@phoneverification'));
/*--//route for reset password end--*/

/*--route for edit passanger profile start--*/
Route::get('editpassenger', function() {
  if(Auth::check()){
    $id = Auth::id();
    $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
    if($user_detail){
		$last_id = DB::table('dn_rides')->where(['passenger_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
		if($last_id){
					$createDate = new DateTime($last_id);
                    $last_id = $createDate->format('m/d/Y');
				}
        $myData['created_at'] = $user_detail->created_at;
        $myData['profile_pic']= $user_detail->profile_pic;
        $myData['first_name']= $user_detail->first_name;
		    $myData['last_name']= $user_detail->last_name;
        $myData['email']= $user_detail->email;
        $myData['dob']= $user_detail->dob;
		    $myData['last_id']= $last_id;
        $myData['profile_status']= $user_detail->profile_status;

      //print_r($myData); die;
    }
    $myData['states'] = DB::table('dn_states')->get();
    
      $id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
    return View::make('editpassenger',compact('myData'));
  }else{
  return redirect('/login');
  }
});
Route::post('editpassenger', array('uses' => 'home@editpassenger'));
/*--//route for edit passanger profile end--*/

/*--route for edit passanger profile start--*/
Route::get('becomedriver', function() {
  if(Auth::check()){
	  $id = Auth::id();
   $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
   $states=DB::table('dn_states')->get();
   if($user_detail){
	        	$myData['created_at'] = $user_detail->created_at;
    				$myData['profile_pic']= $user_detail->profile_pic;
    				$myData['first_name']= $user_detail->first_name;
    				$myData['last_name']= $user_detail->last_name;
            $myData['email']= $user_detail->email;
            $myData['dob']= $user_detail->dob;
            $myData['profile_status']= $user_detail->profile_status;
            $id = Auth::id();
  
			  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
			   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
			   ->where('dn_users.id', $id)->get();
			  
			  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

				  return Redirect::intended('/admin');
			  }
            
              if($myData['profile_status'] == '0'){
                Session::flash('updateProfile', 'Please Update Your Profile First');
                return redirect('/editpassenger');
              }
			//print_r($myData); die;
	        }
    return View::make('becomedriver',compact('myData','states'));
  }else{
    return redirect('/login')->with('becomedriver', ['becomedriver']);;
  }
});
Route::post('becomedriver', array('uses' => 'home@becomedriver'));
Route::post('getcity', array('as'=>'getcity','uses' => 'home@getcity'));
/*--//route for edit passanger profile end--*/
/*--route for edit driver profile start--*/
Route::get('editdriver', function() {

   if(Auth::check()){

    $id = Auth::id();
   $user_details = DB::table('dn_users')->select('*','role_user.role_id')
   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
   ->where('dn_users.id', $id)->get();
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
   foreach ($user_details as $key => $user_detail) {
     # code...
          if($user_detail->role_id=='4'){
			  $last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
				if($last_id){
					$createDate = new DateTime($last_id);
                    $last_id = $createDate->format('m/d/Y');
				}
                  $myData['created_at'] = $user_detail->created_at;
                  $myData['profile_pic']= $user_detail->profile_pic;
                  $myData['first_name']= $user_detail->first_name;
                  $myData['last_name']= $user_detail->last_name;
                  $myData['email']= $user_detail->email;
				  $myData['last_id']= $last_id;
                  $myData['profile_status']= $user_detail->profile_status;
                  $myData['is_suspended']=$user_detail->is_suspended;

      
                    if($myData['profile_status'] == '0'){
                      Session::flash('updateProfile', 'Please Update Your Profile First');
                      return redirect('/editdriver');
                    }
                    return View::make('/driver/editdriver',compact('myData'));
            
          }else if($user_detail->role_id =='5'){
              Session::flash('message', 'You have been revoked by admin.');
              return redirect('/editpassenger');
          }
   }
  }else{
    return redirect('/login');
  }
});

Route::any('/earningReport', array('as' => 'earningReport', 'uses'=>'driver@earningReport'));
Route::post('/editdriver', array('uses' => 'home@editdriver'));
/*--//route for edit driver profile end--*/

/*--route for login and signup with facebook start--*/
Route::get('login/facebook', 'home@redirectToProvider');
Route::get('login/facebook/callback', 'home@handleProviderCallback');

Route::get('passenger/profile', array('as' => 'profile', 'uses' => function(){
  if(Auth::check()){
	 $id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
   $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
  if($user_detail){
	        	$myData['created_at'] = $user_detail->created_at;
    				$myData['profile_pic']= $user_detail->profile_pic;
    				$myData['first_name']= $user_detail->first_name;
    			  $myData['last_name']= $user_detail->last_name;
            $myData['email']= $user_detail->email;
    				$myData['profile_status']= $user_detail->profile_status;
              if($myData['profile_status'] == '0'){
                Session::flash('updateProfile', 'Please Update Your Profile First');
                return redirect('/editpassenger');
              }
			//print_r($myData); die;
	        }
    return view('passengerprofile',compact('myData'));
  }else{
    return redirect('/login');
  }
}));


Route::get('login/becomedriver', array('as' => 'login', 'uses' => function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
   if(Auth::check()){
      return redirect('/');
    }else{
      Session::set('becomedriver', 'becomedriver');

      return view('login'); 

    }
}));
Route::get('login', array('as' => 'login', 'uses' => function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
   if(Auth::check()){
      return redirect('/');
    }else{
      return view('login');
    }
}));
/*--//route for login with facebook end--*/


/*--route for about us page for app start--*/
Route::get('aboutus', array('as' => 'aboutus', 'uses' => function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
    return view('aboutapp');
}));
/*--//route for about us page for app end--*/
Route::get('driver/{id}', 'apppage@driver');
Route::any('driver', array('uses' => 'apppage@driver'));
Route::any('driver/success/{id}', 'apppage@driversuccess');
/*--//route for become driver page for app end--*/
Route::get('faq', array('as' => 'faq', 'uses' => function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
    return view('FAQ');
}));

Route::get('faq-mobile', array('as' => '/faq-mobile', 'uses' => function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
  return view('faqmobile');
}));

Route::get('howItWorks', array('as' => 'howItWorks', 'uses' => function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
    return view('works');
}));
Route::get('earnWithDezi', array('as' => 'earnWithDezi', 'uses' => function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
    return view('earn');
}));
Route::post('/editPhone', ['as' => 'editPhone', 'uses' => 'home@editPhone']);
Route::post('/confirmOTP', ['as' => 'confirmOTP', 'uses' => 'home@confirmOTP']);


/*--passenger routes start here--*/
Route::post('/referralcode', ['as' => 'referralcode', 'uses' => 'home@referralcode']);

Route::group(array('prefix'=>'passenger'),function(){
	$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  } 
Route::get('/yourcars',array('uses' => 'passenger@yourcars'));
Route::post('/yourcars',array('uses' => 'passenger@addCarDetail'));
Route::get('/deleteCar', ['as' => 'deleteCar', 'uses' => 'passenger@deleteCar']);
Route::post('/updateCar', ['as' => 'updateCar', 'uses' => 'passenger@updateCar']);
Route::get('/favoriteplaces',array('uses' => 'passenger@favoriteplaces'));
Route::any('/addPassengerAddress', ['as' => 'addPassengerAddress', 'uses' => 'passenger@addPassengerAddress']);
Route::get('/deletePassengerAddress', ['as' => 'deletePassengerAddress', 'uses' => 'passenger@deletePassengerAddress']);
Route::get('/updatePassengerAddress', ['as' => 'updatePassengerAddress', 'uses' => 'passenger@updatePassengerAddress']);
Route::any('/triphistory',array('as'=>'triphistory','uses' => 'passenger@triphistory'));
Route::any('/viewDetails',array('uses' => 'passenger@viewDetails'));
Route::get('/paymentpassenger',array('uses' => 'passenger@paymentpassenger'));
Route::POST('/deltecard', ['as' => 'deltecard', 'uses' => 'passenger@payment_method_delete']);  

Route::POST('/savecarddetail', ['as' => 'savecarddetail', 'uses' => 'passenger@savecarddetail']);
Route::get('/referhistorypassenger',array('uses' => 'passenger@referhistorypassenger'));
Route::any('/passengerReportAnIssue/{rideid}',array('uses' => 'passenger@passengerReportAnIssue'));
Route::post('/passengerReportAnIssue',array('as'=> 'passengerReportAnIssue','uses' => 'passenger@passengerReportAnIssue'));
});
Route::post('/passengerSubCat',array('as'=> 'passengerSubCat','uses' => 'passenger@passengerSubCat'));
Route::any('/generateReportTrip',array('as'=> 'generateReportTrip','uses' => 'passenger@generateReportTrip'));


/**/
//Route::any('/routeurl', ['as' => 'routkey', 'uses' => 'passenger@asdctrl']); 
/**/

/*--//passenger routes end here--*/



/*--driver routes start here--*/
Route::group(array('prefix'=>'user-driver'),function(){
$id = Auth::id();
  
	  $user_details = DB::table('dn_users')->select('*','role_user.role_id')
	   ->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
	   ->where('dn_users.id', $id)->get();
	  
	  if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
	  }
  Route::get('/earning',array('as'=> 'earning','uses' => 'driver@earning'));
  //Route::group(array('prefix'=>'driver'),function(){
  Route::get('viewdocument',array('uses' => 'driver@viewDocument'));
  //});
  Route::any('/rideAddres' ,array('uses' => 'driver@getRideAddress'));
  Route::post('/receipt' ,array('uses' => 'driver@getReceiptData'));
  Route::post('/selectedDate' ,array('uses' => 'driver@getSelecteDate'));
  
   Route::any('/driverReportAnIssue/{rideid}',array('as' => 'driverReportAnIssue', 'uses' => 'driver@driverReportAnIssue'));
  Route::post('/driverReportAnIssue',array('as'=> 'driverReportAnIssue','uses' => 'driver@driverReportAnIssue'));
  /* date: 8/8/2016 by vaibhav  */
 // Route::post('/storeissue',array('uses' => 'driver@storeissue'));
  /* /date: 8/8/2016 by vaibhav  */
  Route::get('/faqd',array('uses' => 'driver@faq'));
  Route::get('/payments',array('uses' => 'driver@payments'));
  Route::put('/payments',array('uses' => 'driver@saveEditBankDetail'));
  Route::get('/referhistory',array('uses' => 'driver@referhistory'));
  Route::get('/tierlevel',array('uses' => 'driver@tierlevel'));
  Route::post('/tierlevelsweek',array('uses' => 'driver@tierlevelweek'));

  Route::get('/profile', array('as' => 'driver-profile', 'uses' => function(){

	Route::any('/{page?}',function(){
		echo "asdasd"; die;
	  return View::make('errors.404');
	})->where('page','.*');

    if(Auth::check()) {

        $id = Auth::id();
        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
        $is_driver_approved = $user_detail->is_driver_approved;

        if($is_driver_approved == '1')
        {
            if($user_detail)
            {
                $myData['created_at'] = $user_detail->created_at;
                $myData['profile_pic']= $user_detail->profile_pic;
                $myData['first_name']= $user_detail->first_name;
                $myData['email']= $user_detail->email;
                $myData['profile_status']= $user_detail->profile_status;
            }
            return view('driver/driver-profile',compact('myData'));

        } else {

          return redirect('passenger/profile');
        }  

    } else {

      return redirect('/login');
    }

  }));

});

/*--//driver routes end here--*/


//CRON JOBS
Route::any('/crondeleteprofileuncompleteuser', ['as' => 'crondeleteprofileuncompleteuser', 'uses' => 'cron@crondeleteprofileuncompleteuser']);
Route::any('/cronPassengerReferralCheck', ['as' => 'cronPassengerReferralCheck', 'uses' => 'cron@cronPassengerReferralCheck']);

Route::any('/cronTierLevel', ['as' => 'cronTierLevel', 'uses' => 'cron@cronTierLevel']);

Route::any('/getPeekTimeDutyDone', ['as' => 'getPeekTimeDutyDone', 'uses' => 'cron@getPeekTimeDutyDone']);

Route::any('/cronDriverReferralBonus', ['as' => 'cronDriverReferralBonus', 'uses' => 'cron@cronDriverReferralBonus']);
Route::any('/cronDriverLicenseInsuranceExpire', ['as' => 'cronDriverLicenseInsuranceExpire', 'uses' => 'cron@cronDriverLicenseInsuranceExpire']);
Route::any('/cronBirthdayAnniversaryPromoCode', ['as' => 'cronBirthdayAnniversaryPromoCode', 'uses' => 'cron@cronBirthdayAnniversaryPromoCode']);
Route::any('/cronSendAdminNotification', ['as' => 'cronSendAdminNotification', 'uses' => 'cron@cronSendAdminNotification']);
Route::any('/driver-terms-conditions', ['as' => 'driver-terms-conditions', 'uses' => 'home@driversterm']);

Route::get('/{page}','home@getPage');


