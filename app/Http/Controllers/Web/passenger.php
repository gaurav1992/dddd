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
use dateTime;
use App\User;
use Socialite;
use Services_Twilio;
use Oureastudios\Laravel\BraintreeServiceProvider;


class passenger extends Controller {
	/*--functions start for car page--*/
	public function yourcars() {
		if(Auth::check()){
			$id = Auth::id();
	        $user_detail = DB::table('dn_users')->select('*')->where(['id' => $id])->first();
			$last_id = DB::table('dn_rides')->where(['passenger_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
	        $user_car_detail = DB::table('dn_user_cars')->select('*')->where(['user_id' => $id])->get();
	        $total_user_cars = count($user_car_detail);
			if($last_id){
					$createDate = new DateTime($last_id);
                    $last_id = $createDate->format('m/d/Y');
				}
	        if($user_detail){
	        	$myData['created_at'] = $user_detail->created_at;
	        	$myData['profile_pic']= $user_detail->profile_pic;
				$myData['first_name']= $user_detail->first_name;
			    $myData['last_name']= $user_detail->last_name;
				$myData['email']= $user_detail->email;
				$myData['last_id']= $last_id;
				$myData['profile_status']= $user_detail->profile_status;
	        }
			
	        if($user_car_detail){
	        	$myCars['cardetails'] = $user_car_detail;
	        }else{
	        	$myCars['cardetails'] = '';
	        }
			return View('passenger/yourcars', compact('myData','myCars','total_user_cars'));
		}else{
			return redirect('/login');
		}
	}

	public function addCarDetail(Request $request){
	
		$id = Auth::id();
		$make = Input::get('make');
		$model = Input::get('model');
		$year = Input::get('year');
		$number = Input::get('number');
		$transmission = Input::get('transmission');
		DB::table('dn_user_cars')->update(['is_default' => '0']);
		$rules = array(
        'make' => 'Required',
        'model'     => 'Required',
		 'year' => 'Required'
        );
		$insert_data = array(
            'user_id' => $id,
            'make' => $make,
            'model' => $model,
            'year' => $year,
            'number' => $number,
            'is_default' => '1',
            'transmission' => $transmission,
            'is_delete' => '0',
            'added_at' => date('Y-m-d H:i:s')
        );
		$v = Validator::make($insert_data, $rules);
		if( $v->fails()) {
		Session::flash('message', 'Please enter the all required fields [ make,model,and year ]');
		return Redirect::intended('passenger/yourcars');
		}else{ 
			$insertGetId = DB::table('dn_user_cars')->insertGetId($insert_data);
			if($insertGetId){
			Session::flash('message',  'Your Car Is Register Successfully');
			return Redirect::intended('passenger/yourcars');
			}else{
				Session::flash('message', 'Your Data is not submitted please try again');
				return Redirect::intended('passenger/yourcars');
			}
		}
       
	}
  /**
	* Delete the car
	*
	* @param  $request
	* @return Response
	*/
	public function deleteCar( Request $request ) {
		if ($request->ajax()) {
			$id = Auth::id();
			
			$carDeleteId = $request->get('carID');
			$carDetalDelete = DB::table('dn_user_cars')->where(['id' => $carDeleteId])->delete();
			if($carDetalDelete){
				$user_car_detail = DB::table('dn_user_cars')->select('*')->where(['user_id' => $id])->get();
		    	$total_user_cars = count($user_car_detail);
				if($total_user_cars == 1){
			    	DB::table('dn_user_cars')->update(['is_default' => '1']);
			    	return redirect('passenger/yourcars');
			    }
	        	Session::flash('message', 'Your Car Is Deleted Successfully');
				return $carDetalDelete;
	        }
			//die("here");
			
			//echo "<pre>"; print_r($request->ajax()); echo "</pre>";
			//echo "<pre>"; print_r($request->get('carID'));

			//$data['name'] = 'ASDF';
			//echo json_encode($data); die();
		}
	}

	/**
	* update the car
	*
	* @param  $request
	* @return Response
	*/
	public function updateCar( Request $request ) {
		if ($request->ajax()) {
			$id = Auth::id();
			$user_car_detail = DB::table('dn_user_cars')->select('*')->where(['user_id' => $id])->get();
		    $total_user_cars = count($user_car_detail);
		    
		    if($total_user_cars == 1){
		    	$is_default = '1';
		    }else{
		    	$is_default = $request->get('default_check');
		    	DB::table('dn_user_cars')->update(['is_default' => '0']);
		    }

			$carDetailId = $request->get('updatedcarId');
			$update_car_data = array(
                'make' => $request->get('make'),
                'model' => $request->get('model'),
                'year' => $request->get('year'),
                'number' => $request->get('number'),
                'transmission' => $request->get('transmission'),
				'is_default' => $is_default
				
            );
			$CarDetailUpdate = DB::table('dn_user_cars')->where(['id' => $carDetailId])->update($update_car_data);
			if($CarDetailUpdate){
	        	Session::flash('message', 'Your Car Detail Updated Successfully');
				return $CarDetailUpdate;
	        }
		}
	}
/*--//functions end for car page--*/


/*--functions start for add favorite places--*/

	public function favoriteplaces() {
		if(Auth::check()){
			$id = Auth::id();
	        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	        $passengerHomeAddress = DB::table('dn_favorite_places')->select('*')->where(['user_id'=>$id,'place_name'=>'HOME'])->get();
	        $passengerpublicAddress = DB::table('dn_favorite_places')->select('*')->where(['user_id'=>$id,'place_name'=>'public'])->get();
	        $passengerWORKAddress = DB::table('dn_favorite_places')->select('*')->where(['user_id'=>$id,'place_name'=>'WORK'])->get();
			$last_id = DB::table('dn_rides')->where(['passenger_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
	        $passengerotherAddress = DB::table('dn_favorite_places')->select('*')->where(['user_id'=>$id,'place_name'=>'other'])->get();
	        if($user_detail){
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
	        }
	        if($passengerHomeAddress){
	        	$homeAddress['homeAddress'] = $passengerHomeAddress;
	        }else{
	        	$homeAddress['homeAddress'] = '';
	        }
	        if($passengerpublicAddress){
	        	$publicAddress['publicAddress'] = $passengerpublicAddress;
	        }else{
	        	$publicAddress['publicAddress'] = '';
	        }
	        if($passengerWORKAddress){
	        	$workAddress['workAddress'] = $passengerWORKAddress;
	        }else{
	        	$workAddress['workAddress'] = '';
	        }
	        if($passengerotherAddress){
	        	$otherAddress['otherAddress'] = $passengerotherAddress;
	        }else{
	        	$otherAddress['otherAddress'] = '';
	        }


			return View('passenger/favoriteplaces', compact('myData','homeAddress','publicAddress','workAddress','otherAddress'));
		}else{
			return redirect('/login');
		}	
	}
	public function addPassengerAddress( Request $request ) {
		if ($request->ajax()) {
			$place_name = $request->get('place_name');
			$address = $request->get('address');
			$latitude = $request->get('latitude');
			$longitude = $request->get('longitude');
            $zip = explode(' ', trim(explode(',', $address)[2]))[1];

			$locationdata = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=false");
            $locationdata = json_decode($locationdata);
                $formatted_address = $locationdata->results[0]->formatted_address;

                $formatted_address_arr = explode(",", $formatted_address);
                $count = count($formatted_address_arr);
                $country = $formatted_address_arr[$count - 1];
                $state = $formatted_address_arr[$count - 2];
                $num = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

                $state = str_replace($num, null, $state);
                $city = $formatted_address_arr[$count - 3];

                $city = trim($city);
                $state = trim($state);

                DB::table('dn_favorite_places')->update(['is_default' => '0']);

            $insert_data = array(
                'user_id' => Auth::id(),
                'place_name' => $place_name,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'is_default' => '1',
                'created_at' => date('Y-m-d H:i:s')
            );
			$insertGetId = DB::table('dn_favorite_places')->insertGetId($insert_data);
			if($insertGetId){
	        	Session::flash('message', 'Your Address Detail Addes Successfully');
				return $insertGetId;
	        }
		}
	}
	public function deletePassengerAddress( Request $request ) {
		if ($request->ajax()) {
			$addressID = $request->get('addressID');
			$addressDetailDelete = DB::table('dn_favorite_places')->where(['id' => $addressID])->delete();
			if($addressDetailDelete){
	        	Session::flash('message', 'Your Address Is Deleted Successfully');
				return $addressDetailDelete;
	        }
		}
	}
	public function updatePassengerAddress( Request $request ) {
		if ($request->ajax()) {
			$addId = $request->get('addId');
			$latitude = $request->get('latitude');
			$longitude = $request->get('longitude');
			$address = $request->get('address');
			$zip = explode(' ', trim(explode(',', $address)[2]))[1];
			$default_check = $request->get('default_check');
				$locationdata = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=false");
	            $locationdata = json_decode($locationdata);
	                $formatted_address = $locationdata->results[0]->formatted_address;

	                $formatted_address_arr = explode(",", $formatted_address);
	                $count = count($formatted_address_arr);
	                $country = $formatted_address_arr[$count - 1];
	                $state = $formatted_address_arr[$count - 2];
	                $num = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);

	                $state = str_replace($num, null, $state);
	                $city = $formatted_address_arr[$count - 3];

	                $city = trim($city);
	                $state = trim($state);

	        if($default_check =='1'){
	        	DB::table('dn_favorite_places')->update(['is_default' => '0']);
	        }       
	        $update_address_data = array(
                'user_id' => Auth::id(),
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'is_default' => $default_check,
                'updated_at' => date('Y-m-d H:i:s')
            );
            $addressDetailUpdate = DB::table('dn_favorite_places')->where(['id' => $addId])->update($update_address_data);
			if($addressDetailUpdate){
	        	Session::flash('message', 'Your Address Is Updated Successfully');
				return $addressDetailUpdate;
	        }
		}
	}
/*--//functions end for add favorite places--*/	


	public function triphistory(REQUEST $request) {
		$data= $request->all();
		if(@$data['daterange']){
			$dateRange=$data['daterange'];
			$dates=explode('-',$dateRange);
			$startDate= date('Y-m-d H:i:s', strtotime($dates[0]));
			$endDate=date('Y-m-d H:i:s', strtotime($dates[1]));
			
			if(Auth::check()){
			$id = Auth::id();
			$sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_users.first_name,dn_users.last_name,dn_rides.status,dn_payments.amount from dn_rides ";
			$sql.="LEFT join dn_users on dn_rides.driver_id = dn_users.id ";
			$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
			$sql.=" where dn_rides.passenger_id = $id";
			if(!empty($startDate) &&  !empty($endDate))
			{
				
				$sql .=" AND dn_rides.created_at BETWEEN '$startDate' AND '$endDate'";
			}
			$totalRide=DB::select(DB::raw($sql));
		//	echo "<pre>"; print_r($totalRide); die;

			$html='';
			foreach($totalRide as $ride)
			{
				$html .='
						<div class="detail-earn-cls">
						<div class="col-sm-6 left-cls-div">';
				if(@$ride->map_image && $ride->map_image!=''){
				$html .='<img src="{!! $ride->map_image !!}" style="float: left;height: 90px; margin-right: 20px;width: 140px;">';  }
		  else{
		  $html .='<figure> <img src="http://www.startupbangkok.com/wp-content/uploads/2014/12/icon128-2x.png"> </figure>';}
		  
						
						$html .='<p>'.@$ride->rideId.'</p>
						<p>'. @$ride->timeStamp.' </p>
						<p>'.@$ride->first_name .' '. @$ride->last_name.'</p>
						<p><a href="javascript:void(0)" id='. @$ride->rideId.' class="viewDe">view Details</a></p>
						
					  </div>
					  <div class="col-sm-6 right-cls-div ">
						<div class="col-sm-6 text-center detail-e-cls"> $'. @$ride->amount .' <span>Amount Paid </span> </div>';
						  if(@$ride->status== "0"){
						$html .= ' <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-remove"></i> <span>
						  No response'; }
						  elseif(@$ride->status== "1"){
						$html .='  <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-ok"></i> <span>
						  In progress ';}
						  elseif(@$ride->status== "2"){
						 $html .=' <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-ok"></i> <span>
						Completed';}
						  elseif(@$ride->status== "3"){
						  $html .='<div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-remove"></i> <span>
						  Ride cancel';}
						  else{
						$html .='  <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-remove"></i> <span>
						  No response';}
						 $html .='
						</span> </div>
					  </div>
					  <div class="clearfix"></div>
					</div>';
			}
				return $html;
			}
			}
		else{
		if(Auth::check()){
			$id = Auth::id();
	        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();

	        $sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_rides.map_image,dn_users.first_name,dn_users.last_name,dn_rides.status,dn_payments.amount from dn_rides ";
			$sql.="LEFT join dn_users on dn_rides.driver_id = dn_users.id ";
			$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
			$sql.=" where dn_rides.passenger_id = $id";
			$totalRide=DB::select(DB::raw($sql));

			$sql="Select SUM(dn_payments.amount)  as totalRideamount from dn_rides ";
			$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
			$sql.=" where dn_rides.passenger_id = $id";
			$totalRideamount=DB::select(DB::raw($sql));
			$last_id = DB::table('dn_rides')->where(['passenger_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
			if($last_id){
					$createDate = new DateTime($last_id);
                    $last_id = $createDate->format('m/d/Y');
				}
	        if($user_detail){
	        	$myData['created_at'] = $user_detail->created_at;
	        	$myData['profile_pic']= $user_detail->profile_pic;
				$myData['first_name']= $user_detail->first_name;
				$myData['last_name']= $user_detail->last_name;
				$myData['email']= $user_detail->email;
				$myData['last_id']= $last_id;
				$myData['profile_status']= $user_detail->profile_status;
	        }
			
			return View('passenger/triphistory', compact('totalRide','myData','totalRideamount','id'));
		}else{
			return redirect('/login');
	}	}
	}

	public function passengerSubCat(Request $request){
		if(Auth::check()){
			$data = $request->all();
			if(!empty($data)){
				$subcatg= DB::table('dn_cancellation_subcategory')
				->where('category_id',$data['id'])
				->get();
				return $subcatg; 
			}
		}
	}
	
	//function to generate report of trips history--*/
	
	public function generateReportTrip(){
		
		if(Auth::check()){
			$id = Auth::id();
	        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();

	        $sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_rides.map_image,dn_users.first_name,dn_users.last_name,dn_rides.status,dn_payments.amount from dn_rides ";
			$sql.="LEFT join dn_users on dn_rides.driver_id = dn_users.id ";
			$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
			$sql.=" where dn_rides.passenger_id = $id";
			$totalRide=DB::select(DB::raw($sql));
			$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>Ride Id</th>
						<th>Timestamp</th>
						<th>Driver Name</th>
						<th>Amount Paid</th>
						<th>Status</th>
					  </tr>
				';
			 foreach($totalRide as $k=>$ride){
				if(empty($ride->status)){$status='N/A';}else{$status=$ride->status;}
				if(empty($ride->amount)){$amount='N/A';}else{$amount=$ride->amount;}
					if($status==1){
						$status="In Process";}
					elseif($status==2){
						$status="Complete";
						}
					elseif($status==3){
						$status="Ride Cancel";
						}
					elseif($status==0){
						$status="No response";
						} 
					elseif($status==5){
						$status="Cancel Ride Request";
						}
					elseif($status==6){
						$status="Ride cancel but no bill";
						} 
					$html .= "
					<tr>
						<td>".++$k."</td>
						<td>".$ride->rideId."</td>
						<td>".$ride->timeStamp."</td>
						<td>".$ride->first_name.' '.$ride->last_name."</td>
						<td>".$amount."</td>
						<td>".$status."</td>
				    </tr>
		
			"; 
	
			}
			$html .= "</table>";
			
			return \Excel::create('triphistoryReport', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html){
                $sheet->loadView('excel.export')->with("html", $html);
				});
			})->export('xls'); 
		}
		
		
	}
	//function use for driver  reportFundIssue
	public function passengerReportAnIssue( Request $request, $rideId = ''){
		
		  
		  if(Auth::check()){

		  		$data = $request->all();

				$id = Auth::id();
		        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
	           	$ride_id=$rideId;
			    $last_id = DB::table('dn_rides')->where(['passenger_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');

				$catg= DB::table('dn_cancellation_category')->get();
				if($last_id){
					$createDate = new DateTime($last_id);
                    $last_id = $createDate->format('m/d/Y');
				}
				
		        if($user_detail){

		        	$myData['created_at'] = $user_detail->created_at;
		        	$myData['profile_pic']= $user_detail->profile_pic;
					$myData['first_name']= $user_detail->first_name;
					$myData['last_name']= $user_detail->last_name;
					$myData['email']= $user_detail->email;
					$myData['last_id']= $last_id;
					$myData['profile_status']= $user_detail->profile_status;
					// print_r($myData); die;
		        }

		        if ( @$data['submit'] == 1) {
		        	
		        	$ride_id = $data['rideId'];
		        	//echo "<pre>"; print_r($data); die();

		        	$insert_data = array(
			            'user_id'=>Auth::user()->id,
			            'ride_id'=>$data['rideId'],
			            'user_type'=>"passenger",
			            'category'=>$data['category'],
			            'sub_category'=>$data['subcategory'],
			            'message'=>$data['message']
			            );

			        $insertGetId = DB::table('dn_report_an_issuse')->insert($insert_data);

			        Session::flash('message', 'Issue saved Successfully!'); 
					Session::flash('alert-class', 'alert-success'); 
		        	
		        	return redirect('passenger/triphistory');
		        }

				return View("passenger/issue-as-passenger", compact('myData','catg','ride_id'));
			
			} else {

				return redirect('/login');
			}
		
	}

	public function viewDetails( Request $request )
	{
		$data=$request->all();
		$rideId=$data['rideId'];
		$rideData = DB::table('dn_rides')->where(['id' => $rideId])->first();
		
		$pass_id =$rideData->id;
		//echo "<pre>" ,print_r($pass_id); die;
		$reciptData = DB::table('ride_billing_info')->where(['ride_id' => $rideId])->first();
		//echo "<pre>" ,print_r($reciptData); die;
		$pick_lat=@$rideData->pickup_latitude;
		$pick_long=@$rideData->pickup_longitude;
		$dest_lat=@$rideData->destination_latitude;
		$dest_long=@$rideData->destination_longitude;
		$address1 = $this->getaddress(@$pick_lat,$pick_long);
		$address2 = $this->getaddress(@$dest_lat,$dest_long);
		$startTime = date_format(new dateTime(@$rideData->ride_start_time),"d/m/Y H:i:s");
		$endTime = date_format(new dateTime(@$rideData->ride_end_time),"d/m/Y H:i:s");
		$map_image =@$rideData->map_image;
		
		$miles = @$reciptData->miles;
		empty(@$miles) ? $miles=0:'';
		$miles_charges = @$reciptData->miles_charges;
		empty(@$miles_charges) ? $miles_charges=0:'';
		$duration = @$reciptData->duration;
		empty(@$duration) ? $duration=0:'';
		$duration_charges = @$reciptData->duration_charges;
		empty(@$duration_charges) ? $duration_charges=0:'';
		$sub_total = @$reciptData->sub_total;
		empty(@$sub_total) ? $sub_total=0:'';
		$pick_upFee=@$reciptData->pickup_fee;
		empty(@$pick_upFee) ? $pick_upFee=0:'';
		$deziFee=@$reciptData->service_fee;
		empty(@$deziFee) ? $deziFee=0:'';
		$total_charges=@$reciptData->total_charges;
		empty(@$total_charges) ? $total_charges=0:'';
		
		return json_encode(array("pass_id" => $pass_id ,"address1"=>$address1,"address2"=>$address2,"startTime" =>$startTime,"endTime"=>$endTime,"map_image"=>@$map_image,"miles"=>$miles,"miles_charges"=>$miles_charges,"duration"=>$duration,"duration_charges"=>$duration_charges,"sub_total"=>$sub_total,"deziFee"=>$deziFee,"pick_upFee"=>$pick_upFee,"total_charges"=>$total_charges ),true);
	}
	public function getaddress($lat,$lng)
	{
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
		$json = @file_get_contents($url);
		$data=json_decode($json);
		$status = $data->status;
		if($status=="OK")
		return $data->results[0]->formatted_address;
		else
		return false;
	}
	
	/*functions start for payment page*/
	public function paymentpassenger(Request $request) {

		if(Auth::check()) {
			
			$id = Auth::id();
	        
	        $user_detail = DB::table('dn_users')->select('*')->where(['id' => $id])->first();
	        $passenger_dezicredit = DB::table('dn_passenger_credits')->select('*')->where(['user_id' => $id])->orderBy('id', 'desc')->first();

	        //echo "<pre>"; print_r($passenger_dezicredit); die('dezicredit');

			$user_credit_sql ="SELECT SUM( dn_passenger_credits.credit_amount ) AS totalCredit,(
								SELECT credit_balance
								FROM dn_passenger_credits
								WHERE user_id = $id
								ORDER BY id DESC 
								LIMIT 1
								) AS creditBalance
								FROM  dn_passenger_credits 
								WHERE  user_id = $id ";

			$user_credit = DB::select(DB::raw($user_credit_sql));
			$paypalAccount=DB::table('dn_payment_accounts')->select('id','user_id', 'account_type' , 'image_url', 'is_default', 'account_email' )->where(['user_id' => $id, 'account_type' => 'paypal', 'is_delete' => '0'])->get();
			$card=DB::table('dn_payment_accounts')->select('id','user_id', 'account_type' ,'card_type','masked_number', 'image_url', 'card_last_4', 'expired', 'is_default' )->where(['user_id' => $id, 'account_type' => 'card', 'is_delete' => '0'])->get();
			$last_id = DB::table('dn_rides')->where(['passenger_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
	          if($last_id){
					$createDate = new DateTime($last_id);
                    $last_id = $createDate->format('m/d/Y');
				}
	        if($user_detail) {
	        	$myData['created_at'] = $user_detail->created_at;
	        	$myData['profile_pic']= $user_detail->profile_pic;
				$myData['first_name']= $user_detail->first_name;
				$myData['last_name']= $user_detail->last_name;
				$myData['email']= $user_detail->email;
				$myData['last_id']= $last_id;
				$myData['profile_status']= $user_detail->profile_status;
	        }

			return View('passenger/paymentpassenger', compact('myData','user_credit','paypalAccount','card'));

		} else { return redirect('/login'); }
	}


	public function savecarddetail( Request $request ) {

		$data=$request->all();
		$user_id = Auth::id();

		if (!empty($data['ps_promo_code'])) {
			
			$ps_promo_code = $data['ps_promo_code'];

			Session::put('ps_promo_code', $ps_promo_code);
			$validator_time = date('Y-m-d H:i:s');
            $dezi_credit = DB::table('dn_passenger_credits')->select('credit_balance')->where(['user_id' => $user_id])->orderBy('id', 'desc')->first();
            if($dezi_credit){
                $dezi_credit = $dezi_credit->credit_balance;
            }else{
                $dezi_credit = 0;
            }
			$promoCodeCheck = DB::table('dn_passenger_promo_code')->select('*')->where(['code' => $ps_promo_code, 'status' => 1])->whereNotIn('type', ['referral'])->first();

			if (!empty($promoCodeCheck)) {

                $used_check = DB::table('dn_passenger_promo_code_uses')->select('id')->where(['promo_code_id' => $promoCodeCheck->id,'user_id' => $user_id])->orderBy('id', 'desc')->first();
                if($promoCodeCheck->promo_multiple == 1){

                    $used_check = null;
                }
                if(is_null($used_check)){
                    $credit_balance = $dezi_credit + $promoCodeCheck->amount;
                    $today_date = strtotime(date('Y-m-d'));
                    $till_validate = strtotime($promoCodeCheck->valid_till);
                    $add_days = 15;
                    $sub_days = 15;
                    if($promoCodeCheck->type == 'birthday'){
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
                        $lower_date = strtotime($lower_date);
                        if($today_date > $lower_date AND $today_date < $upper_date){
                        }else{
                            $request->session()->flash('status', 'Your anniversary promo is expired!');
                            return redirect('passenger/paymentpassenger');
                        }
                    }
                    if($promoCodeCheck->type == 'ani'){
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
                        $lower_date = strtotime($lower_date);
                        if($today_date > $lower_date AND $today_date < $upper_date){
                        }else{
                            $request->session()->flash('status', 'Your anniversary promo is expired!');
                            return redirect('passenger/paymentpassenger');
                        }
                    }
                    if($promoCodeCheck->type == 'normal'){
                        if($today_date >$till_validate){
                            $request->session()->flash('status', 'Your promo code is expired!');
                            return redirect('passenger/paymentpassenger');

                        }

                    }
                    if($promoCodeCheck->type == 'new_rider_promotion'){
                        $get_ride =DB::table('dn_rides')->where(['passenger_id' => $user_id])->pluck('id');
                        if(!empty($get_ride)){

                            $request->session()->flash('status', 'Your promo code is expired!');
                            return redirect('passenger/paymentpassenger');
                        }
                    }
                    $inserted = DB::table('dn_passenger_credits')->insertGetId(['user_id' => $user_id, 'credit_type' => '3', 'credit_amount' => $promoCodeCheck->amount, 'credit_txn_type' => 'DR', 'credit_balance' => $credit_balance]);
                    if ($inserted) {
                        DB::table('dn_passenger_promo_code_uses')->insertGetId(['user_id' => $user_id, 'promo_code_id' => $promoCodeCheck->id]);
                        $request->session()->flash('status', 'Code applied successfully!');
                        return redirect('passenger/paymentpassenger');
                    } else {
                        return response()->json(['status' => 0, 'message' => 'You can not apply this code!']);
                    }
                }else{

                    $request->session()->flash('status', 'You have already applied this code!');
                    return redirect('passenger/paymentpassenger');
                }
			} else {

				$request->session()->flash('status', 'Invalid Promo Code.');
            	return redirect('passenger/paymentpassenger');
			}
			
		} //ps_promo_code
			

		$nonce_token = $data['payment_method_nonce'];
		$account_type = $data['account_type'];

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
		                            'failOnDuplicatePaymentMethod' => true,
		                            'verifyCard' => true
		                        ]
		                    ]

		                ]);

		               
		                //print_r($result);exit;

		                if($result->success){
		                    //var_dump($result);die('test');
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
		                            'is_default' => $result->customer->creditCards[0]->default,
		                            'updated_at' => date('Y-m-d H:i:s')
		                        ];

		                        	$inserted = DB::table('dn_payment_accounts')->insertGetId($data_arr);

				                    $request->session()->flash('status', 'Payment method saved successfully');
				                    return redirect('passenger/paymentpassenger');
		                    }
		                    elseif($account_type == 'paypal'){
		                        $data_arr =  [
		                            'user_id' => $user_id,
		                            'account_type' => $account_type,
		                            'payment_token' => $result->customer->paypalAccounts[0]->token,
		                            'image_url' => $result->customer->paypalAccounts[0]->imageUrl,
		                            'is_default' => $result->customer->paypalAccounts[0]->default,
		                            'account_email' =>  $result->customer->paypalAccounts[0]->email,
		                            'updated_at' => date('Y-m-d H:i:s')
		                        ];

									$inserted = DB::table('dn_payment_accounts')->insertGetId($data_arr);
									$data = ['status' => 1, 'message' => 'Payment method saved successfully'];
									return $data;

		                    }
		                    else{
		                        $data_error = ['status' => 0, 'message' => 'Provide correct payment type.'];
		                        $request->session()->flash('status', 'Provide correct payment type.');
		                        return redirect('passenger/paymentpassenger');
		                    }

		                    

		                }
		                else{
		                    $data = ['status' => 0, 'message' => 'Payment Method not saved', 'message_braintree' => $result->message];
		                   	if($account_type=='card'){
		                   		$request->session()->flash('status', $result->message);
		             			return redirect('passenger/paymentpassenger');
		                   	}else if($account_type=='paypal'){
		             			$data = ['status' => 1, 'message' => $result->message];
								return $data;
		                   	}
		                   	
		                } 
		            }
		            else{	
		                //add card to existing user on braintree
		                $result = \Braintree_PaymentMethod::create([
		                    'customerId' => $user_id,
		                    'paymentMethodNonce' => $nonce_token,
		                    'options' => [
		                        'failOnDuplicatePaymentMethod' =>true,
		                        'verifyCard' => true
		                    ]

		                ]);
		                //print_r($result->success);
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
		                            'is_default' => $result->paymentMethod->default,
		                            'updated_at' => date('Y-m-d H:i:s')
		                        ];

		                        $inserted = DB::table('dn_payment_accounts')->insertGetId($data_arr);
			                    $request->session()->flash('status', 'Payment method saved successfully.');
			                    return redirect('passenger/paymentpassenger');
		                    }
		                    elseif($account_type == 'paypal'){
		                        //var_dump($result);die;
		                        $data_arr =  [
		                            'user_id' => $user_id,
		                            'account_type' => $account_type,
		                            'payment_token' => $result->paymentMethod->token,
		                            'image_url' => $result->paymentMethod->imageUrl,
		                            'is_default' => $result->paymentMethod->default,
		                            'account_email' =>  $result->paymentMethod->email,
		                            'updated_at' => date('Y-m-d H:i:s')
		                        ];
		                        $inserted = DB::table('dn_payment_accounts')->insertGetId($data_arr);
			                    $data = ['status' => 1, 'message' => 'Payment method saved successfully'];
			                    return $data;
		                    }
		                    else{
		                    		if($account_type=='card'){
				                   		 $request->session()->flash('status', 'Provide correct payment type.');
				             			return redirect('passenger/paymentpassenger');
				                   	}else if($account_type=='paypal'){
				             			$data_error = ['status' => 0, 'message' => 'Provide correct payment type.'];
										return $data;
				                   	}
		                    }

		                }
		                else{
		                    $data = ['status' => 0, 'message' => 'Payment Method not saved', 'message_braintree' => $result->message];
		                     $request->session()->flash('status', $result->message);
		                     return redirect('passenger/paymentpassenger');
		                }
		            }

        }
        else{
            $data = ['status' => 0, 'message' => 'No user found'];
             
        }

        	/*
			$add_card_data = array(
                'masked_number' => $request->get('number'),
                'card_type' => $request->get('cardType'),
                'account_type' => $request->get('account_type'),
                'expiration_date' => $request->get('expiration_date'),
                'card_last_4' => $request->get('last_4_no'),
                'created_at' => date('Y-m-d H:i:s')
            );
            //print_r($add_card_data);exit;
			$AddCardData = DB::table('dn_payment_accounts')->where(['user_id' => $id])->update($add_card_data);
			if($AddCardData){
				print_r($AddCardData);
				die("hfij");
	        	Session::flash('message', 'Your Card Detail Added Successfully');
				return $AddCardData;
	        }*/
		
	}


	public function payment_method_delete(Request $request)
    {
    // print_r($request->all());exit;  

        $user_id = $request['user_id'];
       
        $card_id = $request['id'];
       

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
                    return $data;
                }
                else{
                    $data = ['status' => 0, 'message' => 'Error to delete payment method'];
                    return $data;
                }
            }
            else{
                $data = ['status' => 0, 'message' => 'No payment method found'];
                return $data;
            }
        }
        else{

            $data = ['status' => 0, 'message' => 'No user found'];
            return $data;
        }

    }
	 //braintree services
   

/*--//functions end for car page--*/


/*--function start for referral history--*/
	public function referhistorypassenger() { 
		if(Auth::check()){
			$id = Auth::id();
	        $user_detail = DB::table('dn_users')->select('*')->where(['id' => $id])->first();
	        $referralsCode = DB::table('dn_user_referrals')->select('*')->where(['user_id' => $id])->first();
	       // print_r($id);die;
				$referralsCompleted = "SELECT dn_user_referrals . * , dn_users.first_name AS referralUserName FROM `dn_user_referrals` LEFT JOIN dn_users ON dn_user_referrals.user_id = dn_users.id WHERE dn_user_referrals.referred_by ='".$id."' AND dn_user_referrals.status = '1' AND  dn_user_referrals.referral_type = '3'";
				$referralsPending = "SELECT dn_user_referrals . * , dn_users.first_name AS referralUserName FROM `dn_user_referrals` LEFT JOIN dn_users ON dn_user_referrals.user_id = dn_users.id WHERE dn_user_referrals.referred_by ='".$id."' AND dn_user_referrals.status = '0' AND  dn_user_referrals.referral_type = '3'";
				$PassengerBonus = "SELECT SUM( credit_amount ) AS creditAmount FROM dn_passenger_credits WHERE `user_id` = '".$id."' AND credit_type='2' AND credit_txn_type = 'Cr' ";
				

				$referralsComplete = DB::select(DB::raw($referralsCompleted));
				//print_r($referralsComplete);die;
				foreach($referralsComplete as $k=>$v)
				{
					$refrdCmpltdId=$v->user_id;
					$ridesCmpltd[]=DB::table('dn_rides')
									->select(DB::raw("dn_rides.*,CONCAT(dn_users.first_name,' ',dn_users.last_name) as drname"))
									->leftjoin('dn_users', 'dn_users.id', '=', 'dn_rides.driver_id')->where('passenger_id',$refrdCmpltdId)->get();  
				}
				$referralsRemaining = DB::select(DB::raw($referralsPending));
				
				foreach($referralsComplete as $k=>$v)
				{
					$refrdremaingId=$v->user_id;
					$ridesremaind[] = DB::table('dn_rides')
									->select(DB::raw("dn_rides.*,CONCAT(dn_users.first_name,' ',dn_users.last_name) as drname"))
									->leftjoin('dn_users', 'dn_users.id', '=', 'dn_rides.driver_id')
									->where('passenger_id',$refrdremaingId)->get();
				} 
				
				 
				$completebonus = DB::select(DB::raw($PassengerBonus));
				$passenger_promos=DB::table("dn_passenger_promo_code")->select('amount')->where(['type'=>'referral'])->orderBy('id','desc')->first();
			    $passengerPromoData =explode('.',$passenger_promos->amount);
			    $passenger_promos=$passengerPromoData[0];
				$last_id = DB::table('dn_rides')->where(['passenger_id' => $id])->orderBy('id', 'desc')->whereIn('status', array(2,3,6))->pluck('ride_end_time');
				if($last_id){
					$createDate = new DateTime($last_id);
                    $last_id = $createDate->format('m/d/Y');
				}
            if($user_detail){
	        	$myData['created_at'] = $user_detail->created_at;
	        	$myData['profile_pic']= $user_detail->profile_pic;
				$myData['first_name']= $user_detail->first_name;
				$myData['last_name']= $user_detail->last_name;
				$myData['email']= $user_detail->email;
				$myData['last_id']= $last_id;
				$myData['passenger_referral_code']= $user_detail->passenger_referral_code;
				$myData['profile_status']= $user_detail->profile_status;
	        }
	        if($referralsComplete){
	        	$myreferralcomplete['complete'] = $referralsComplete;
	        }else{
	        	$myreferralcomplete['complete'] = '';
	        } 
	        if($completebonus){
	        	$myBonus['bonus'] = $completebonus;
	        }else{ 
	        	$myBonus['bonus'] = '';
	        }
			
	        if($referralsRemaining){
	        	$myreferralpending['pending'] = $referralsRemaining;
	        }else{
	        	$myreferralpending['pending'] = '';
	        }
	        if($referralsCode){
	        	$myreferral['referral_code'] = $referralsCode->referral_code;
	        }else{
	        	$myreferral['referral_code'] = '';
	        }

			return View('passenger/referhistorypassenger', compact('myData','passenger_promos','myreferralcomplete','myreferralpending','myreferral','myBonus','ridesCmpltd','ridesremaind'));
		}else{
			return redirect('/login');
		}	
	}
/*--//function end for referral history--*/	



}