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
use dateTime;

class driver extends Controller {

	public function __construct()
	{
		//parent::__construct();
		/*REDIRECT USER TO ADMIN PANEL BACK IF HIS ROLE ADMIN OR SUBADMIN START*/	
		$id = Auth::id();
		$user_details = DB::table('dn_users')->select('*','role_user.role_id')
		->Join('role_user', 'dn_users.id', '=', 'role_user.user_id')
		->where('dn_users.id', $id)->get();

		if(@$user_details[0]->role_id == 1 || @$user_details[0]->role_id == 2){

		  return Redirect::intended('/admin');
		}
		/*REDIRECT USER TO ADMIN PANEL BACK IF HIS ROLE ADMIN OR SUBADMIN END*/		
	}
	//function use for driver earning
	public function earning()
	{

		if( Auth::check() )
		{
			$id = Auth::id();
	        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	        $is_driver_approved = $user_detail->is_driver_approved;

		    if($is_driver_approved == '1')
		    {
					
		        $ridelSQL = "SELECT dn_rides.*, dn_users.first_name, dn_payments.amount,dn_payments.amount, dn_payments.driver_earning FROM dn_rides
		    	LEFT JOIN dn_users ON dn_rides.driver_id = dn_users.id
		    	LEFT JOIN dn_payments ON dn_rides.charge_id = dn_payments.id
		    	WHERE dn_rides.driver_id = $id  ORDER BY  dn_rides.id DESC ";

			    $ride_detail = DB::select(DB::raw($ridelSQL));
			    //echo "<pre>"; print_r($ride_detail); die(' here');
			
				

			  	/*foreach($ride_detail as $key=>$value)
			    {
			        $sum_amount+= @$value->driver_earning;
			    }*/
		        if($user_detail)
		        {
					$last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
					if($last_id){
						$createDate = new DateTime($last_id);
						$last_id = $createDate->format('m/d/Y');
					}
					$myData['id'] = $user_detail->id;
		        	$myData['created_at'] = $user_detail->created_at;
					$myData['profile_pic']= $user_detail->profile_pic;
					$myData['first_name']= $user_detail->first_name;
					$myData['email']= $user_detail->email;
					$myData['last_id']= $last_id;
				//print_r($myData); die;
		        }
				$sum_amount=0;
		        foreach($ride_detail as $k=>$ride_passanger_name){
				$driverEarningSQL = "SELECT SUM( driver_earning ) AS sum FROM  `dn_payments` WHERE ride_id = '$ride_passanger_name->id' ";
				$sum_amounts = DB::select(DB::raw($driverEarningSQL));
				
				$sum_amount += $sum_amounts[0]->sum;
			
		       	$passenger_id=$ride_passanger_name->passenger_id; 
				$passenger_detail = DB::table('dn_users')->where('id',$passenger_id)->get();
				//echo "<pre>"; print_r($passenger_detail);
				if(!empty($passenger_detail)) {
		      		 $ride_detail[$k]->passenger_fname=$passenger_detail[0]->first_name;
		      		 $ride_detail[$k]->passenger_lname=$passenger_detail[0]->last_name;
		       		 
				}else{

					$ride_detail[$k]->passenger_fname="N/A";
		       		  $ride_detail[$k]->passenger_lname="N/A";
				}

		        }
				
		         //  echo "<pre>"; print_r($ride_detail); die;
				return View('driver/earning', compact('myData','ride_detail','sum_amount'));

			} else {

				return redirect('passenger/profile');
			}

		} else {

			return redirect('/login');
		}
	}

public function earningReport(REQUEST $request)
	{
		$startDate=$request->input('from');
		$endDate=$request->input('to');
		//$uid=$request->input('id');
		
		if( Auth::check() )
		{

			$id = Auth::id();
			
	        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	        $is_driver_approved = $user_detail->is_driver_approved;

		    if($is_driver_approved == '1')
		    {

		        $ridelSQL = "SELECT dn_rides.*, dn_users.first_name, dn_payments.amount,dn_payments.amount, dn_payments.driver_earning FROM dn_rides
		    	LEFT JOIN dn_users ON dn_rides.driver_id = dn_users.id
		    	LEFT JOIN dn_payments ON dn_rides.charge_id = dn_payments.id
		    	WHERE dn_rides.driver_id = $id  ";
				
				 if(!empty($startDate) &&  !empty($endDate))
				{
					$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
					$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
					$ridelSQL .=" AND  dn_rides.created_at BETWEEN '$startDate' AND '$endDate'";
				} 
				$ridelSQL .= " ORDER BY  dn_rides.id DESC ";
			    $ride_detail = DB::select(DB::raw($ridelSQL));
			    // echo "<pre>"; print_r($ride_detail); die(' here');
			
				$driverEarningSQL = "SELECT SUM( driver_earning ) AS sum FROM  `dn_payments` WHERE user_id =$id";

				$sum_amount = DB::select(DB::raw($driverEarningSQL));
				$sum_amount = $sum_amount[0]->sum;

			  	/*foreach($ride_detail as $key=>$value)
			    {
			        $sum_amount+= @$value->driver_earning;
			    }*/

			  

		        if($user_detail)
		        {
		        	$last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
					if($last_id){
						$createDate = new DateTime($last_id);
						$last_id = $createDate->format('m/d/Y');
					}
		        	$myData['created_at'] = $user_detail->created_at;
					$myData['profile_pic']= $user_detail->profile_pic;
					$myData['first_name']= $user_detail->first_name;
					$myData['email']= $user_detail->email;
					$myData['last_id']= $last_id;
				//print_r($myData); die;
		        }
		        foreach($ride_detail as $k=>$ride_passanger_name){

		       	$passenger_id=$ride_passanger_name->passenger_id; 
				$passenger_detail = DB::table('dn_users')->where('id',$passenger_id)->get();
				//echo "<pre>"; print_r($passenger_detail);
				if(!empty($passenger_detail)) {
		      		 $ride_detail[$k]->passenger_fname=$passenger_detail[0]->first_name;
		      		 $ride_detail[$k]->passenger_lname=$passenger_detail[0]->last_name;
		       		 
				}else{

					$ride_detail[$k]->passenger_fname="N/A";
		       		  $ride_detail[$k]->passenger_lname="N/A";
				}

				}
			}
		}
		$html = "<divstyle='border:1px solid black;background-color:red !important;'>
		
		<center><h4>Driver ride's Details</h4>
		<div style='margin-bottom:20px;'>
					<span class='text-info'>Driver Name: </span><span>".@$myData['first_name'].".&nbsp;&nbsp;</span>
					<span class='text-info'>Driver Email: </span><span>".@$myData['email'].".</span>
					</div>
				<table style='border:1px solid blue; text-align:center;'>
					  <tr>
						<th>Ride ID</th>
						<th>Ride Date</th>
						<th>Passenger Name</th>
						<th>Amount</th>
						<th>Status</th>
					  </tr>";
		if(@$ride_detail && !empty($ride_detail)){
		foreach($ride_detail as $key=>$data)
		{ 
			$rideId=@$data->id? @$data->id:'N/A';
			$passenger_id=@$data->passenger_id?$data->passenger_id:'N/A' ;
			$status=@$data->status? $data->status : 'N/A';
			$created_at=@$data->created_at?$data->created_at : 'N/A';
			$passenger_fnam=@$data->passenger_fnam?$data->passenger_fnam:'N/A';
			$passenger_lname=@$data->passenger_lname?$data->passenger_lname:'N/A';
			$amount=@$data->amount?$data->amount:'N/A';
			if($data->status == 1){ $status_text = "In Progress"; @$status_class='glyphicon glyphicon-refresh'; } 
            else if($data->status == 2){ $status_text = "Completed"; @$status_class='glyphicon glyphicon-ok'; } 
            else if($data->status == 3){ $status_text = "Ride Cancel"; @$status_class='glyphicon glyphicon-remove'; } 
            else if($data->status == 4){ $status_text = "No response"; @$status_class='glyphicon glyphicon-remove'; } 

             
			$html .="<tr>
						<td>" .@$rideId. "</td>
						<td>" .@$created_at. "</td>
						<td>" .@$passenger_fnam.' '.$passenger_lname."</td>
						<td>".@$amount."</td>
						<td><div class='col-sm-6 text-center detail-e-cls'><i class ='".@$status_class."'></i> <span>".@$status_text ."</span></div></td>
						</tr>";
		} 
		$html .="</table></center></div>";
		}else{
			$html ="<center><div><table><tr><td colspan = '5'><h2> NO RIDES </h2></td></tr></table></center></div>";
		}
		return \PDF::loadHTML($html)->download('rideDetails.pdf');	
 }

//function use for driver  single_ride_detail

public function triphistory() {
		if(Auth::check()){
			$id = Auth::id();
	        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();

	        $sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_users.first_name,dn_users.last_name,dn_rides.status,dn_payments.amount from dn_rides ";
			$sql.="LEFT join dn_users on dn_rides.driver_id = dn_users.id ";
			$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
			$sql.=" where dn_rides.driver_id = $id";
			$totalRide=DB::select(DB::raw($sql));

			$sql="Select SUM(dn_payments.amount)  as totalRideamount from dn_rides ";
			$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
			$sql.=" where dn_rides.driver_id = $id";
			$totalRideamount=DB::select(DB::raw($sql));

			//echo "<pre>";
			//print_r($totalRideamount);
			//exit;
	        if($user_detail){
				$last_id = DB::table('dn_rides')->where(['d' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
				if($last_id){
						$createDate = new DateTime($last_id);
						$last_id = $createDate->format('m/d/Y');
					}
	        	$myData['created_at'] = $user_detail->created_at;
				$myData['profile_pic']= $user_detail->profile_pic;
				$myData['first_name']= $user_detail->first_name;
				$myData['email']= $user_detail->email;
				$myData['last_id']= $last_id;
			return View('driver/triphistory', compact('totalRide','myData','totalRideamount','id'));
		}else{
			return redirect('/login');
		}
	}

}

public function getRideAddress(Request $request){

	     if($request->ajax()){
         $id=  $request->get('id');
		// echo $id; die;
         $single_ride_detail = DB::table('dn_rides')->select('*')->where('id', $id)->first();
		 //echo "<pre>", print_r($single_ride_detail);  die;
		 $ride_id = $single_ride_detail->id;
		 $ride_map_image =$single_ride_detail->map_image;

         $pickup_time= $single_ride_detail->pickup_time;
         $droptime=$single_ride_detail->ride_end_time;

         $pick_latitude=$single_ride_detail->pickup_latitude;
         $pick_longitude=$single_ride_detail->pickup_longitude;
         $pickup_locationdata = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$pick_latitude,$pick_longitude&sensor=false");
         $pickup_locationdata = json_decode($pickup_locationdata);
         $pickup_formatted_address = $pickup_locationdata->results[0]->formatted_address;
        //echo  $pickup_formatted_address ; die;


         $destination_latitude=$single_ride_detail->destination_latitude;
         $destination_longitude=$single_ride_detail->destination_longitude;
         $destination_locationdata = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$destination_latitude,$destination_longitude&sensor=false");
         $destination_locationdata = json_decode($destination_locationdata);
         $destination_formatted_address = $pickup_locationdata->results[0]->formatted_address;
           //echo  $destination_formatted_address ; die;
       //for pickup address
        $pick_formatted_address_arr = explode(",", $pickup_formatted_address);
        $pickup_count = count($pick_formatted_address_arr);
        $pickup_country = $pick_formatted_address_arr[$pickup_count - 1];
        $pickup_state = $pick_formatted_address_arr[$pickup_count - 2];
        $pick_num = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $pickup_state = str_replace($pick_num, null, $pickup_state);
        $pick_city = $pick_formatted_address_arr[$pickup_count - 3];
        $pick_city = trim($pick_city);
        $pickup_state = trim($pickup_state);

        //***/
          //for destination address
        $destination_formatted_address_arr = explode(",", $destination_formatted_address);
        $des_count = count($destination_formatted_address_arr);
        $des_country = $destination_formatted_address_arr[$des_count - 1];
        $des_state = $destination_formatted_address_arr[$des_count - 2];
        $des_num = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $des_state = str_replace($des_num, null, $des_state);
        $des_city = $destination_formatted_address_arr[$des_count - 3];
        $des_city = trim($des_city);
        $des_state = trim($des_state);

        //***/

       return response()->json(['pick_city' =>$pick_city , 'pickup_state' => $pickup_state,'pickup_formatted_address' => $pickup_formatted_address,'des_city' =>$des_city , 'des_state' => $des_state,'des_formatted_address' => $destination_formatted_address,'pickup_time' => $pickup_time,'droptime' => $droptime,'ride_map_image'=>$ride_map_image,'ride_id'=>$ride_id]);
}


}
//function use for receipt data
public function getReceiptData(Request $request){

	     if($request->ajax()){
         $id=  $request->get('id');
		// echo $id; die;
         $single_receipt_detail = DB::table('ride_billing_info')->select('*')->where('ride_id', $id)->first();
	     //echo "<pre>", print_r($single_receipt_detail );  die;
         return response()->json($single_receipt_detail);

}

}

//*********end**************
//function for get the selected date based ride data

   public function getSelecteDate(Request $request){
	   if($request->ajax()){
		   $id = Auth::id();
		  $selectfrom = $request->get('slectfrom');
		  $selectto = $request->get('selectTo');
		  $startDate= date('Y-m-d', strtotime($selectfrom));
		  $endDate=date('Y-m-d', strtotime($selectto));

				$select_dn_ride_sql= "SELECT dn_rides . * ,dn_users.first_name, dn_payments.amount
					FROM dn_rides
					LEFT JOIN dn_users ON dn_rides.driver_id = dn_users.id
					LEFT JOIN dn_payments ON dn_rides.payment_id = dn_payments.id
					WHERE dn_rides.driver_id = $id
					AND dn_rides.created_at BETWEEN '".$startDate."' AND '".$endDate."'";

	    $select_dn_ride = DB::select(DB::raw($select_dn_ride_sql));
		//echo "<pre>", print_r($select_dn_ride); die;
		return response()->json($select_dn_ride);

	   }
   }

   //function use for driver  reportFundIssue
public function driverReportAnIssue( Request $request, $rideId = ''){


		  if(Auth::check()){

		  		$data = $request->all();

				$id = Auth::id();
		        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	           	$ride_id=$rideId;

				$catg= DB::table('dn_cancellation_category')->get();

				$subcatg= DB::table('dn_cancellation_subcategory')->get();
				// echo '<pre>',print_r($catg),'</pre>'; die;
		        if($user_detail){
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
					// print_r($myData); die;
		        }

		        if ( @$data['submit'] == 1) {

		        	$ride_id = $data['rideId'];
		        	//echo "<pre>"; print_r($data); die();

		        	$insert_data = array(
			            'user_id'=>Auth::user()->id,
			            'ride_id'=>$data['rideId'],
			            'user_type'=>"driver",
			            'category'=>$data['category'],
			            'sub_category'=>$data['subcategory'],
			            'message'=>$data['message']
			            );

			        $insertGetId = DB::table('dn_report_an_issuse')->insert($insert_data);
                    if($insertGetId){
			        Session::flash('message', 'Issue saved Successfully!');
					Session::flash('alert-class', 'alert-success');
		        	 return redirect('user-driver/earning');
					}else{
						 Session::flash('message', 'Issue  not saved !');
					Session::flash('alert-class', 'alert-success');
					 return redirect('user-driver/driverReportAnIssue');

					}
				  }
                     return View('driver/report-issue', compact('myData','catg','subcatg','ride_id'));

					   } else {

						return redirect('/login');
					}

	}
//function use for driver  reportFundIssue
// public function reportFundIssue(Request $request $rideId = ''){

	  // if(Auth::check()){
			// $id = Auth::id();
	        // $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
			// $catg= DB::table('dn_cancellation_category')->get();
			// $subcatg= DB::table('dn_cancellation_subcategory')->get();
			// $rideId =$rideId ;
			// // echo '<pre>',print_r($catg),'</pre>'; die;
	        // if($user_detail){
	        	// $myData['created_at'] = $user_detail->created_at;
				// $myData['profile_pic']= $user_detail->profile_pic;
				// $myData['first_name']= $user_detail->first_name;
				// $myData['email']= $user_detail->email;
	        // }
			// return View('driver/report-issue', compact('myData','catg','subcatg','ride_id'));
		// }else{
			// return redirect('/login');
		// }

// }

// public function storeissue(Request $request){

	// die('here');

	// $data = $request->all();
	// $category=$data['category'];
	// return;
// }

//function use for driver  savereportFundIssue
// public function savereportFundIssue(Request $request){

	          	// $insert_data = array(
	            // 'category'=>$request->input('category'),
	            // 'sub_category'=>$request->input('subcategory'),
	            // 'message'=>$request->input('message'),
	            // 'user_type'=>"driver",
	            // 'ride_id'=>$request->input('rideId'),
	            // 'user_id'=>Auth::user()->id,
	            // 'created_at' => date('Y-m-d H:i:s')
	            // );

	         // $insertGetId = DB::table('dn_report_an_issuse')->insertGetId($insert_data);
	      // // echo "<pre>"; print_r($insertGetId) ; die;
			 // if( $insertGetId){
                   // Session::flash('message', 'Your issue  saved');
		           // return Redirect::intended('user-driver/report-issue');
			 // }else{
				   // Session::flash('message', 'Your issue  not saved try again');
				   // return Redirect::intended('user-driver/report-issue');
			 // }
// }



//function use for driver  faq
	public function faq() {
		if(Auth::check()){
			$id = Auth::id();
			$user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	        $is_driver_approved = $user_detail->is_driver_approved;

		    if($is_driver_approved == '1')
		    {
		        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();

		        $getAllfaq=DB::table('dn_faq')->get();

		        if($user_detail){
					 $last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
				      if($last_id){
						$createDate = new DateTime($last_id);
						$last_id = $createDate->format('m/d/Y');
					}
		        	$myData['created_at'] = $user_detail->created_at;
					$myData['profile_pic']= $user_detail->profile_pic;
					$myData['first_name']= $user_detail->first_name;
					$myData['email']= $user_detail->email;
					$myData['last_id']= $last_id;
		        }
				return View('driver/faq', compact('myData','getAllfaq'));
			} else {
				return redirect('passenger/profile');
			}
		}else{
			return redirect('/login');
		}
	}
//function use for driver  payments
  public function payments() {
		if(Auth::check()){
			$id = Auth::id();
			$user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	          $is_driver_approved = $user_detail->is_driver_approved;

		    if($is_driver_approved == '1')
		    {
		        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
		        $bank_detail = DB::table('dn_driver_bank_detail')->select('*')->where('user_id', $id)->first();
                  //echo "<pre>", print_r($bank_detail); die;
		        if($user_detail){
					 $last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
				    if($last_id){
						$createDate = new DateTime($last_id);
						$last_id = $createDate->format('m/d/Y');
					}
		        	$myData['created_at'] = $user_detail->created_at;
					$myData['profile_pic']= $user_detail->profile_pic;
					$myData['first_name']= $user_detail->first_name;
					$myData['email']= $user_detail->email;
					$myData['last_id']= $last_id;
		        }
				return View('driver/payments', compact('myData','bank_detail'));
			} else {
					return redirect('passenger/profile');
				}
			}else{
				return redirect('/login');
			}
	}



public function saveEditBankDetail(Request $request){

	if($request->ajax()){
       $id = Auth::id();
	  //echo $id; die;
	 // $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
        $bank_detail = DB::table('dn_driver_bank_detail')->select('*')->where('user_id', $id)->first();
	  $insert_data = array(
	            'bank_name'=>$request->get('bankname'),
	            'acc_number'=>$request->get('account_number'),
	            'routing_number'=>$request->get('routing_number'),
	            'branch'=>$request->get('branch'),
	            'user_id'=>Auth::user()->id,
	            'created_at' => date('Y-m-d H:i:s')
	            );
		if($bank_detail){
			 $bankAcount = DB::table('dn_driver_bank_detail')->where('user_id',$id)->update($insert_data);
			 Session::flash('message', 'Your bank detail  saved Successfully');
	          // return redirect('user-driver/payments');
                 return $bankAcount;
		// 	if( $insertGetId){
          //             Session::flash('message', 'Your bank detail  saved Successfully');
    	 //          return Redirect::intended('user-driver/payments');
  //  		 }else{
  //  				  Session::flash('message', 'Your bank detail  not saved');
  //  				 return Redirect::intended('user-driver/payments');
  //   			 }
		   }else{
			   $insertGetId = DB::table('dn_driver_bank_detail')->insertGetId($insert_data);

		// 	if( $insertGetId){
			   Session::flash('message', 'Your bank detail  updated Successfully');
		 //  return Redirect::intended('user-driver/payments');
		//  }else{
		 //    Session::flash('message', 'Your bank   not updated');
		  	    return $insertGetId;
		//  }

		}
	}
	 //else{

	// $updated = DB::table('dn_driver_bank_detail')->where('user_id',$id)->update($insert_data);

	     //  return  $updated ;
	//}

}

//function use for driver  refers history
	public function referhistory() {

		if(Auth::check()){
			$id = Auth::id();
			$user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	        $is_driver_approved = $user_detail->is_driver_approved;

		    if($is_driver_approved == '1')
		    {
			        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
					//get the river detail
					 $driver_detail_SQL_tiers ="SELECT dn_users_data.tiers_level, dn_users_data.referral_code, dn_driver_tier_data.total_active_hours, dn_driver_tier_data.cancelation_rate, dn_driver_tier_data.acceptance_rate FROM `dn_users_data`
						LEFT JOIN dn_driver_tier_data ON dn_driver_tier_data.driver_id = dn_users_data.user_id
						LEFT JOIN role_user ON role_user.user_id = dn_users_data.user_id
						WHERE dn_users_data.user_id = $id AND role_user.role_id=4 ";
					//echo $id;die;

					//get the river referral completed
				    $driver_detail_SQL_comp ="SELECT dn_user_referrals.user_id, dn_users.first_name
							 FROM dn_user_referrals
							 LEFT JOIN dn_users ON dn_users.id = dn_user_referrals.user_id
							 LEFT JOIN role_user ON role_user.user_id = dn_user_referrals.user_id
							 WHERE dn_user_referrals.referred_by =$id
							 AND dn_user_referrals.status = '1'
							 AND dn_user_referrals.referral_type = 4 AND role_user.role_id=4";
				    //get the driver referral incompleted
				    $driver_detail_SQL_Incomp ="SELECT dn_user_referrals.user_id, dn_users.first_name
							 FROM dn_user_referrals
							 LEFT JOIN dn_users ON dn_users.id = dn_user_referrals.user_id
							 LEFT JOIN role_user ON role_user.user_id = dn_user_referrals.user_id
							 WHERE dn_user_referrals.referred_by =$id
							 AND dn_user_referrals.status = '0'
							 AND dn_user_referrals.referral_type =4 AND role_user.role_id=4 ";

			        //get the driver referral bonus
						$driver_total_bonus_sql	= "SELECT SUM( bonus_amount) AS totalBonus
							FROM `dn_driver_dezibunus`
							WHERE user_id =$id
							AND bonus_type =1";

			        $driver_tiers = DB::select($driver_detail_SQL_tiers);
					$driver_referral_complete = DB::select($driver_detail_SQL_comp);
					//print_r($driver_referral_complete);die;
					foreach($driver_referral_complete as $k=>$v)
					{
						$refrdCmpltdId=$v->user_id;
						$ridesCmpltd[]=DB::table('dn_rides')
										->select(DB::raw("dn_rides.*,CONCAT(dn_users.first_name,' ',dn_users.last_name) as prname"))
										->leftjoin('dn_users', 'dn_users.id', '=', 'dn_rides.passenger_id')
										->where('driver_id',$refrdCmpltdId)
										->where('dn_rides.status','2')
										->get();  
					}
					
					$driver_referral_Incomplete = DB::select($driver_detail_SQL_Incomp);
					
					foreach($driver_referral_Incomplete as $k=>$v)
					{
						$refrdremaingId=$v->user_id;
						$ridesremaind[] = DB::table('dn_rides')
										->select(DB::raw("dn_rides.*,CONCAT(dn_users.first_name,' ',dn_users.last_name) as prname"))
										->leftjoin('dn_users', 'dn_users.id', '=', 'dn_rides.passenger_id')
										->where('driver_id',$refrdremaingId)
										->where('dn_rides.status','2')
										->get();
					} 
					$driver_total_bonus = DB::select($driver_total_bonus_sql);

			     //  echo "<pre>",print_r($driver_tiers);die;

			        if($user_detail){
						$last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
						  if($last_id){
							$createDate = new DateTime($last_id);
							$last_id = $createDate->format('m/d/Y');
						}
						$promo_credit = DB::table('dn_driver_promos')->select('referal_credit_for_5_10','referal_credit_for_20')->where(['is_active' => 1])->first();

			        	$myData['created_at'] = $user_detail->created_at;
						$myData['profile_pic']= $user_detail->profile_pic;
						$myData['first_name']= $user_detail->first_name;
						$myData['email']= $user_detail->email;
						$myData['referal_credit_for_5_10']= $promo_credit->referal_credit_for_5_10;
				        $myData['referal_credit_for_20']= $promo_credit->referal_credit_for_20;
				        $myData['passenger_referral_code']= $user_detail->passenger_referral_code;
						$myData['last_id']= $last_id;
			        }

			        $driver_promos=DB::table("dn_driver_promos")->select('referal_credit_for_20')->orderBy('id','desc')->first();
			       // echo "<pre>"; print_r($driver_promos); die;
					return View('driver/referhistory', compact('myData','driver_tiers','driver_referral_complete','driver_referral_Incomplete','driver_total_bonus','driver_promos','ridesCmpltd','ridesremaind'));
			} else {
					return redirect('passenger/profile');
				}
		}else{
			return redirect('/login');
		}
	}

//function use for driver  tier level
	public function tierlevel() {
		if(Auth::check()){
			$id = Auth::id();
				$user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
		        $is_driver_approved = $user_detail->is_driver_approved;

			    if($is_driver_approved == '1')
			    {
					//get the teir levels
				$driver_tierlevels_SQL ="SELECT dn_users_data.tiers_level, dn_users_data.referral_code,dn_driver_tier_data.total_active_hours, dn_driver_tier_data.cancelation_rate, dn_driver_tier_data.scheduled_hours,dn_driver_tier_data.acceptance_rate FROM `dn_users_data`
						LEFT JOIN dn_driver_tier_data ON dn_driver_tier_data.driver_id = dn_users_data.user_id
						WHERE dn_users_data.user_id = $id";
			    $driver_tier_levels = DB::select($driver_tierlevels_SQL);
			 // echo "<pre>",print_r($getweektiers);die;
			        if($user_detail){
						$last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
						  if($last_id){
							$createDate = new DateTime($last_id);
							$last_id = $createDate->format('m/d/Y');
						}
			        	$myData['created_at'] = $user_detail->created_at;
						$myData['profile_pic']= $user_detail->profile_pic;
						$myData['first_name']= $user_detail->first_name;
						$myData['email']= $user_detail->email;
						$myData['last_id']= $last_id;
			        }
					return View('driver/tierlevel', compact('myData','driver_tier_levels','a'));
			} else {
					return redirect('passenger/profile');
				}
		}else{
			return redirect('/login');
		}
	}

//function use for driver  select tier level week
   public function tierlevelweek(Request $request){
	 if($request->ajax()){
		 $id = Auth::id();
		 $y=date('Y');
		  $getweek = $request->get('slectweek');
		// echo $getweek."<br/>";
		 $week_num = date('W', strtotime($getweek));
	   // echo $week_num; die;
	      $getweektiers_sql="SELECT dn_users_data.tiers_level, dn_driver_tier_data_log.total_active_hours, dn_driver_tier_data_log.scheduled_hours, dn_driver_tier_data_log.cancelation_rate, dn_driver_tier_data_log.acceptance_rate
					FROM `dn_users_data`
					LEFT JOIN dn_driver_tier_data_log ON dn_driver_tier_data_log.driver_id = dn_users_data.user_id
					WHERE dn_driver_tier_data_log.driver_id =$id
					AND dn_driver_tier_data_log.week = $week_num
					AND dn_driver_tier_data_log.year =$y";

	       $getweektiers = DB::select($getweektiers_sql);
		 // echo "<pre>",print_r($getweektiers);die;
		   return response()->json($getweektiers);

	    }

    }


	  public function viewDocument(){
		  if(Auth::check()){
				$id = Auth::id();
		        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
				$user_documents = DB::table('dn_driver_requests')->select('*')->where('user_id', '162')->first();
				$user_documents->license_verification=$user_documents->license_verification;
				$user_documents->proof_of_insurance=$user_documents->proof_of_insurance;
				$user_documents->driver_records=json_decode($user_documents->driver_records,true);


				if($user_documents ==''){
					$user_documents = 'No Data Found';
				}

		         if($user_detail){
					 $last_id = DB::table('dn_rides')->where(['driver_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
						  if($last_id){
							$createDate = new DateTime($last_id);
							$last_id = $createDate->format('m/d/Y');
						}
		        	$myData['created_at'] = $user_detail->created_at;
					$myData['profile_pic']= $user_detail->profile_pic;
					$myData['first_name']= $user_detail->first_name;
					$myData['email']= $user_detail->email;
					$myData['last_id']= $last_id;
				   return View('driver/view-driver', compact('myData','user_documents'));
			     }else{

				return redirect('/login');
			}
	  }
	  }
  }
