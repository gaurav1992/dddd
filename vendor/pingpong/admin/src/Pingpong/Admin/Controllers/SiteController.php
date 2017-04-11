<?php namespace Pingpong\Admin\Controllers;

session_check();
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Pingpong\Admin\Entities\Article;
use Pingpong\Admin\Entities\Option;
use Datatables;
use Illuminate\Http\Request;
use DateTime;
class SiteController extends BaseController
{

	 
    /**
     * @Admin dashboard.
     * @Dashboard contains the gist of whole website
	 * @Modules coverd In this function : Payouts, New Passengers,New Drivers,Ride Requests,Refunds,Locations and other  
	 *  details of admin.
     * @return \Response
     */
    public function index()
    {
		$adminUsers = DB::table('role_user')
						->leftJoin('dn_users', 'role_user.user_id', '=', 'dn_users.id')	
						->where('role_id','1')
						->get();
		
		$hello="hello world";
		$states = DB::table('dn_states')->get();
		/*  Payouts   */
		$minMaxdate = DB::table('dn_rides')
						->select(DB::raw('DATE_FORMAT(max(date(dn_rides.created_at)),"%m/%d/%Y") as maxDate,DATE_FORMAT(min(date(dn_rides.created_at)),"%m/%d/%Y") as minDate'))
						->leftJoin('dn_payments', 'dn_rides.id', '=', 'dn_payments.ride_id')	
						//->whereIn('dn_rides.payment_status',[0,2,1])
						->get();
						//print_r($minMaxdate);exit;
		
		$payoutsSum = DB::table('dn_rides')
						->distinct()
						->select(DB::raw("SUM(driver_earning) AS payouts"))
						->leftJoin('ride_billing_info', 'dn_rides.id', '=', 'ride_billing_info.ride_id')	
						->leftJoin('dn_payments', 'dn_rides.id', '=', 'dn_payments.ride_id')	
						->where('dn_rides.status','2')
						->where('dn_rides.payment_status','1')->get();
						//->sum('dn_payments.amount');
		//print_r($payoutsSum);die;		
		
		
		$payoutsSum=$payoutsSum[0]->payouts;
		
		/*  Payouts   */
		
		/*  New Passenger  */
		$minMaxdatePass = DB::table('dn_users')
						->select(DB::raw('DATE_FORMAT(max(date(dn_users.created_at)),"%m/%d/%Y") as maxDate,DATE_FORMAT(min(date(dn_users.created_at)),"%m/%d/%Y") as minDate'))
						->leftJoin('role_user', 'role_user.user_id', '=', 'dn_users.id')	
						->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')	
						->where('role_user.role_id',3)
						->get();
		$passCount = DB::table('dn_users')
						->leftJoin('role_user', 'dn_users.id', '=', 'role_user.user_id')	
						->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')	
						->where('role_user.role_id',3)
						->count();
						
		
		/*  New Passenger  */
		
		/*  New Driver  */
		$minMaxdatedriver = DB::table('dn_users')
						->select(DB::raw('DATE_FORMAT(max(date(dn_users.created_at)),"%m/%d/%Y") as maxDate,DATE_FORMAT(min(date(dn_users.created_at)),"%m/%d/%Y") as minDate'))
						->leftJoin('role_user', 'role_user.user_id', '=', 'dn_users.id')	
						->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')	
						->where('role_user.role_id',4)
						->get();
		$driverCount = DB::table('dn_users')
						->leftJoin('role_user', 'dn_users.id', '=', 'role_user.user_id')	
						->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')	
						->where('role_user.role_id',4)
						->count();
		/*  New Driver  */
		
		/*  Ride Requests  */
		$RideData = DB::table('dn_rides')
			->select(DB::raw('city_name,count(city_name) as counting,DATE_FORMAT(max(date(dn_rides.created_at)),"%m/%d/%Y") as ridemaxDate,DATE_FORMAT(min(date(dn_rides.created_at)),"%m/%d/%Y") as rideminDate'))
						->where('city_name','!=','')
						->orderBy('city_name', 'ASc')
						->groupBy('city_name')
						->get();

		
	   /*MOST-LEAST RIDE CODE START*/
		$max = -9999999; //will hold max val
		$found_item = null; //will hold item with max val;
		$least = 9999999; //will hold max val
		$found_item_least = null; //will hold item with max val;
		foreach($RideData as $k=>$v) 
		{
			if($v->counting>$max)
			{
			   $max = $v->counting;
			   $found_item = $k;
			}
		}
		
		foreach($RideData as $lk=>$lv) 
		{
			if($lv->counting < $least)
			{
			   $least = $lv->counting;
			   $found_item_least = $lk;
			}
		}
		
		$maxRideCityIndex = $found_item;
		$maxRideCount = $max;
		$leastRideCityIndex = $found_item_least;
		$leastRideCount = $least;
		/*MOST-LEAST RIDE CODE END*/
		/*  Ride Requests    */
		/*  Refunds    */
		$refundData= DB::table('dn_rides')
						->select(DB::raw('(SUM(dn_payments.refund_amount)+SUM(dn_payments.tip_refund)) as totalRefund,DATE_FORMAT(max(date(dn_rides.created_at)),"%m/%d/%Y") as maxDate,DATE_FORMAT(min(date(dn_rides.created_at)),"%m/%d/%Y") as minDate'))
						->leftJoin('dn_payments', 'dn_rides.id', '=', 'dn_payments.ride_id')	
						// ->whereIn('dn_rides.payment_status',[0,2])
						->get();
		//print_r($refundData);die;
		/*  Refunds    */
		/*  Locations    */
		$sql  = $this->locationQueries(null,null);
		
		//echo $sql;exit; 


		$locData = DB::select(DB::raw($sql));
		//echo "<pre>";print_r($locData);die;	
		$passengers=0;
		$drivers=0;
		$tRevenue=0;
		$payouts=0;
		$profit=0;
		foreach($locData as $k=>$v)
		{	
			$passengers+=$v->passCount;
			$drivers+=$v->driverCount;
			$tRevenue+=$v->Trevenue;
			$payouts+=$v->payouts;
			$profit+=$v->profit;	
		}
		//echo $passenger; die;

		$cities= DB::table('dn_cities')
				->orderby('city','ASC')->lists('city','id');
		$cities = array(0=>'All Cities') + $cities;		
		/*  Locations    */
        return $this->view('index',compact('adminUsers','passengers','cities','drivers','tRevenue','payouts','profit','payoutsSum','minMaxdate','minMaxdatePass','passCount','minMaxdatedriver','driverCount','RideData','refundData','states','maxRideCityIndex','maxRideCount','leastRideCityIndex','leastRideCount'));
    }
	
	
	
	/**
     * @getCity function to retrive the cityName based on Latitude and longitude
	 *  
     * @return \city
     */
	public function getcity($long,$lat)
	{
		$deal_lat=$long;
		$deal_long=$lat;
		$geocode=file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng='.$deal_lat.','.$deal_long.'&sensor=false');
        $output= json_decode($geocode);
		for($j=0;$j<count($output->results[0]->address_components);$j++){
               $cn=array($output->results[0]->address_components[$j]->types[0]);
           if(in_array("locality", $cn))
           {
            $city= $output->results[0]->address_components[$j]->long_name;
           }
        }
            return $city;
	} //End of getcity function

	/**
     * @Genrate report of payout data on dashboard.
     * @return \excel for the payouts genearted between the given date.
     */
	public function payout(Request $request)
	{
		/* initializing the variables */
		$data = $request->all();
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$payoutCity=$data['payoutCity'];
		$payoutState=$data['payoutState'];
		$field='dn_rides.id';
		$direction='ASC';
		$sql="Select DISTINCT dn_rides.id as rideId,date(dn_rides.created_at) as rideDate ,dn_rides.driver_id,dn_rides.passenger_id,ride_billing_info.pickup_fee,ride_billing_info.miles as distance,ride_billing_info.miles_charges,ride_billing_info.duration_charges,dn_payments.refund_amount,ride_billing_info.duration,dn_payments.amount,ride_billing_info.tip,driver_earning as payouts from dn_rides ";
		$sql.="LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id ";
		$sql.="LEFT join dn_cities on dn_cities.city = dn_rides.city_name ";
		$sql.="LEFT join dn_states on dn_states.state_code = dn_rides.state_code ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		//$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.=" where dn_rides.payment_status = '1' AND dn_rides.status = '2' ";
		//$sql="Select * from dn_rides where driver_id = 253";
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_payments.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		
		if(!empty($payoutCity))
		{
			
				$city=DB::table('dn_cities')->where('id',$payoutCity)->first();
			$sql .=" AND  dn_rides.city_name = '$city->city'";
		}
		
		if(!empty($payoutState))
		{
			$sql .=" AND  dn_rides.state_code = '$payoutState'";
		}
		//$sql .= "order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		//$sql .= " Limit ".$offset." , ".$limit;
		//echo $sql;die;
		$totalRide=DB::select(DB::raw($sql));
		//print_r($totalRide);die;
		$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>Ride ID</th>
						<th>Ride Date</th>
						<th>Driver ID</th>
						<th>Passenger ID</th>
						<th>Distance</th>
						<th>Minutes</th>
						<th>Billing Amount</th>
						<th>tip</th>
						<th>Driver Earning</th>
					  </tr>
				';
				$sum=0;
		foreach($totalRide as $k=>$ride)
		{
			//echo $user->active;
			$rideId=$ride->rideId;
			$rideDate=$ride->rideDate;
			$driver_id=$ride->driver_id;
			$passenger_id =$ride->passenger_id;
			$distance=$ride->distance;
			$duration=$ride->duration;
			$Totalamount=$ride->amount;
			$payouts=$ride->payouts;
			$tip=$ride->tip;
			
			$sum+=(float)$payouts;
			if(!empty($payouts)){
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			if(empty($rideDate))
			{
				$rideDate="N/A";
			}
			if(empty($passenger_id))
			{
				$passenger_id="N/A";
			}
			if(empty($driver_id))
			{
				$driver_id="N/A";
			}
			if(empty($distance))
			{
				$distance="0";
			}
			if(empty($duration))
			{
				$duration="0";
			}
			if(empty($Totalamount))
			{
				$Totalamount="NA";
			}
			if(empty($tip))
			{
				$tip="NA";
			}
			if(empty($payouts)){
				if($payouts==0){
					continue;
				}
			}
			$html .= "
					<tr>
				    <td>".++$k.".</td>
				    <td>".$rideId."</td>
				    <td>".$rideDate."</td>
				    <td>".$driver_id."</td>
				    <td>".$passenger_id."</td>
				    <td>".$distance."</td>
				    <td>".$duration."</td>
				    <td>".$Totalamount."</td>
				    <td>".$tip."</td>
				    <td>".$payouts."</td>
				    </tr>
		
			";	}
		}
		$html .= "<tr><td colspan='9'> Total : </td><td>$ ".$sum."</td></tr></table>";
		return \Excel::create('payouts', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html); 
            });
        })->export('xls');
		
		//return \PDF::loadHTML($html)->download('payOuts.pdf');	
	}
	/**
     * @Genrate report of new passenger data on dashboard.
     * @return \excel for the new passenger genearted between the given date.
     */
	public function newPass(Request $request)
	{
		/* initializing the variables */
		//die("new fun");
		//echo "bfghghg";die;
		$data = $request->all();
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$field='dn_users.id';
		$direction='ASC';
		
		$passengerCity=$data['passengerCity'];
		$passengerState=$data['passengerState'];
		
		//print_r($data);die;
		$sql="Select dn_users.unique_code as userId,date(dn_users.created_at) as joinedDate ,dn_users.first_name,dn_users.last_name,dn_users.contact_number,dn_users.email,dn_cities.city as location,dn_users.dob from dn_users ";
		$sql.=" LEFT join role_user on role_user.user_id = dn_users.id ";
		$sql.=" LEFT join dn_cities on dn_cities.id = dn_users.city ";
		$sql.=" LEFT join roles on roles.id = role_user.role_id ";
		//$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.=" where role_user.role_id = 3 ";
		//$sql="Select * from dn_rides where driver_id = 253";
		if(!empty($startDate) &&  !empty($endDate))
		{
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
		}
		
		if(!empty($passengerCity))
		{
			
			
			$sql .=" AND  dn_users.city = '$passengerCity'";
		}
		
		if(!empty($passengerState))
		{
			$sql .=" AND  dn_users.state = '$passengerState'";
		}	
		
		
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		
		$totalUser=DB::select(DB::raw($sql));
		$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>User Id</th>
						<th>Full Name</th>
						<th>Joining Date</th>
						<th>Phone Number</th>
						<th>Email</th>
						<th>Location</th>
						<th>Dob</th>
						<th>Age</th>
					  </tr>
				';
		foreach($totalUser as $k=>$user)
		{
			//echo $user->active;
			$userId=$user->userId;
			$joinedDate=$user->joinedDate;
			$fullname=$user->first_name. ' '.$user->last_name;
			$contact_number =$user->contact_number;
			$email=$user->email;
			$location=$user->location;
			$dob = $user->dob;
			$age = $this->ageCalculator($dob);
			if(empty($userId))
			{
				$userId="N/A";
			}
			
			if(empty($joinedDate))
			{
				$joinedDate="N/A";
			}
			if(empty($fullname))
			{
				$fullname="N/A";
			}
			if(empty($contact_number))
			{
				$contact_number="N/A";
			}
			
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($location))
			{
				$location="N/A";
			}
			if(empty($dob))
			{
				$dob="NA";
			}
			if(empty($age))
			{
				$age="NA";
			}
			$html .= "
					<tr>
				    <td>".++$k."</td>
				    <td>".$userId."</td>
				    <td>".$fullname."</td>
				    <td>".$joinedDate."</td>
				    <td>".$contact_number."</td>
				    <td>".$email."</td>
				    <td>".$location."</td>
				    <td>".$dob."</td>
				    <td>".$age."</td>
				    </tr>
		
			";
	
			
		}
		$html .= "</table>";
		//print_r($html); die;
		return \Excel::create('NewPassengers', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');
		
		//return \PDF::loadHTML($html)->download('newpassenger.pdf');	
	}
	
	/**
     * @dynamically change payout data on dashboard according to date change.
     * @return \payout.
     */
	 public function dynamicPayout(Request $request)
		{
			$data = $request->all();
			$time=$data['startDate'];
			$payoutCity=$data['payoutCity'];
			$payoutState=$data['payoutState'];
			$timeto=$data['endDate'];
			
			/* $payoutsSum = DB::table('dn_rides')
						->leftJoin('dn_payments', 'dn_rides.id', '=', 'dn_payments.ride_id')	
						->where('dn_rides.payment_status','1')
						->where('dn_rides.status','2')
						->whereBetween('dn_rides.created_at', array($from,$enxd))
						->sum('dn_payments.amount'); */
						
		$sql="Select driver_earning as payouts from dn_rides ";
		$sql.="LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id ";
		$sql.="LEFT join dn_cities on dn_cities.city = dn_rides.city_name ";
		$sql.="LEFT join dn_states on dn_states.state_code = dn_rides.state_code ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		//$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.=" where dn_rides.payment_status = '1' AND dn_rides.status = '2' ";
		//$sql="Select * from dn_rides where driver_id = 253";
		if(!empty($time) &&  !empty($timeto))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $time)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $timeto)));
			$sql .=" AND  dn_payments.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		
		if(!empty($payoutCity))
		{
			
			$city=DB::table('dn_cities')->where('id',$payoutCity)->first();
			$sql .=" AND  dn_rides.city_name = '$city->city'";
		}
		
		if(!empty($payoutState))
		{
			$sql .=" AND  dn_rides.state_code = '$payoutState'";
		}
		
			$payoutsSum=DB::select(DB::raw($sql));
		$sum=0;	
	foreach($payoutsSum as $k=>$ride)
		{
			$totalPayouts=$ride->payouts;
			$sum+=(float)$totalPayouts;
			

		}
			return !empty($sum)?$sum:0;
	}
		
	/**
     * @dynamically change passenger data on dashboard according to date change.
     * @return \passenger counting
     */	
	 public function DnewPass(Request $request)
		{
			$data = $request->all();
			$passengerState=$data['passengerState'];
			$passengerCity=$data['passengerCity'];
			$time=strtotime($data['startDate']);
			
			$timeto=strtotime($data['endDate']);
			
			
			
			/* $passCount = DB::table('dn_users')
						->leftJoin('role_user', 'dn_users.id', '=', 'role_user.user_id')	
						->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')	
						->where('role_user.role_id',3)
						->where('dn_users.state',"@$passengerState")
						->where('dn_users.city',"@$passengerCity")
						->whereBetween('dn_users.created_at', array($from,$enxd))
						->count(); */
				$sql=" SELECT COUNT(*) as countpass FROM dn_users ";		
				$sql.=" LEFT join role_user on role_user.user_id = dn_users.id ";
				$sql.=" LEFT join roles on role_user.role_id = roles.id ";
				$sql.=" WHERE role_user.role_id = '3' ";
				
				if(!empty($time) && !empty($timeto))
				{
					$from = date('Y-m-d H:i:s',$time);
					$enxd = date('Y-m-d H:i:s',$timeto);
					$sql .=" AND  dn_users.created_at BETWEEN '$from' AND '$enxd'";
				}
				
				if(!empty($passengerState))
				{
					$sql .=" AND  dn_users.state = '$passengerState'";
				}
				
				if(!empty($passengerCity))
				{
					$sql .=" AND  dn_users.city = '$passengerCity'";
				}
			$passCount=DB::select(DB::raw($sql));	
			
			 /*  if(!empty($passengerState)){
				
			    }
			if(!empty($passengerCity)){
				
			    }
			if(!empty($from) && !empty($enxd)){
							       
				}			
			 $passCount->count();   */
			
			 
			return @$passCount[0]->countpass;
		}
	/**
     * @Genrate report of driver data on dashboard.
     * @return \excel for the driver genearted between the given date.
     */
	
	public function newDriver(Request $request)
	{
		/* initializing the variables */
		//die("new fun");
		//echo "bfghghg";die;
		$data = $request->all();
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		
		$driverCity=$data['driverCity'];
		$driverState=$data['driverState'];
		$field=' dn_users.created_at ';
		$direction=' DESC ';

		$sql ="Select dn_users.unique_code as userId,dn_users.Anniversary,dn_users.driver_approved_on as approvaldate,dn_users.zip_code as zip,dn_states.state,dn_users.id as drID,date(dn_users.created_at) as joinedDate ,dn_users.first_name,dn_users.last_name,dn_users.contact_number,dn_users.email,dn_cities.city as location,dn_users.dob,dn_users.become_driver_request as application,dn_users.is_driver_approved from dn_users ";
		$sql.=" LEFT join role_user on role_user.user_id = dn_users.id ";
		$sql.=" LEFT join dn_cities on dn_cities.id = dn_users.city ";
		$sql.=" LEFT join roles on roles.id = role_user.role_id ";
		$sql.=" LEFT join dn_states on dn_states.state_code = dn_users.state ";
		//$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.=" where role_user.role_id = 4 ";
		//$sql="Select * from dn_rides where driver_id = 253";
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
		}


		if(!empty(@$driverCity))
		{
			
			
			$sql .=" AND  dn_users.city = '$driverCity'";
		}
		
		if(!empty(@$driverState))
		{
			$sql .=" AND  dn_users.state = '$driverState'";
		}			
		$sql .=  "order by $field  $direction ";
		
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		
		$totalUser = DB::select(DB::raw($sql));
		$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>User Id</th>
						<th>Driver Name</th>
						<th>Joining Date</th>
						<th>Phone Number</th>
						<th>Email</th>
						<th>State</th>
						<th>City</th>
						<th>Zip Code</th>
						<th>Dob</th>
						<th>Anniversary date</th>
						<th>Age</th>
						<th>Referral Code</th>
						<th>License No.</th>
						<th>License Exp.</th>
						<th>Insurance Exp.</th>
						<th>Date of Approval</th>
						<th>Car Transmission</th>
						<th>Driver Records</th>
					  </tr>
				';
		if(!empty($totalUser)){
		foreach($totalUser as $k=>$user)
		{
			//echo $user->active;
			$userId=$user->userId;
			$drID=$user->drID;
			$joinedDate=date('m/d/Y',strtotime($user->joinedDate));
			$fullname=$user->first_name. ' '.$user->last_name;
			$contact_number =$user->contact_number;
			$email=$user->email;
			$city=$user->location;
			$state=$user->state;
			$dob = date('m/d/Y',strtotime($user->dob));
			$zip = $user->zip;
			$age = $this->ageCalculator($dob);
			$approvaldate = date('m/d/Y',strtotime($user->approvaldate));
			$Anniversary = date('m/d/Y',strtotime($user->Anniversary));

			if(empty($userId))
			{
				$userId="N/A";
			}else{
				//echo "<pre>";
				$dn_users_data=DB::table('dn_users_data')->where('user_id',$drID)->first();
				$dn_driver_requests=DB::table('dn_driver_requests')->where('user_id',$drID)->first();
				//print_r($dn_users_data);
				
				//print_r($dn_driver_requests);
								//echo "<hr>";
				if(!empty(@$dn_users_data->referral_code)){
			    	$referral_code=$dn_users_data->referral_code;
			   }else{$referral_code="N/A";}
			   
			   if(!empty(@$dn_users_data->license_number)){
			    	$license_number=$dn_users_data->license_number;
			   }else{$license_number="N/A";}
			   
			   if(!empty(@$dn_driver_requests->licence_expiration)){
				   
			    	$licence_expiration=date('m/d/Y H:i:s',strtotime($dn_driver_requests->licence_expiration));
			   }else{$licence_expiration="N/A";}
			   
			   if(!empty(@$dn_driver_requests->insurance_expiration)){
			    	$insurance_expiration=date('m/d/Y H:i:s',strtotime($dn_driver_requests->insurance_expiration));
			   }else{$insurance_expiration="N/A";} 
			   
			   if(!empty(@$dn_driver_requests->car_transmission)){
			    	$car_transmission=$dn_driver_requests->car_transmission;
			   }else{$car_transmission="N/A";}
			   
			   if(!empty(@$dn_driver_requests->driver_records)){
			    	$driver_records=$dn_driver_requests->driver_records;
					$driver_records=json_decode($driver_records);
					$recordData='<table>
								<thead>
								<tr><th>Sr. No.</th> <th>Question</th> <th>Answer</th></tr>';
					
					foreach($driver_records as $key=> $records)
					{
						if(@$records->answer=='1')
						{
							$answer="Yes";
						}elseif(@$records->answer=='0')
						{
							$answer="No";
						}else{
							$answer=$records->answer;
						}
						$recordData .= "<tr style='text-align:left;'><td>".++$key."</td><td><b>$records->question </b></td><td>$answer</td></tr>";
						
					}
					$recordData.="</table>";
			   }else{$recordData="N/A";}
			}
			
			
			if(empty($joinedDate))
			{
				$joinedDate="N/A";
			}
			if(empty($fullname))
			{
				$fullname="N/A";
			}
			if(empty($contact_number))
			{
				$contact_number="N/A";
			}
			
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($Anniversary))
			{
				$Anniversary="N/A";
			}
			if(empty($approvaldate))
			{
				$approvaldate="N/A";
			}
			if(empty($city))
			{
				$city="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($zip))
			{
				$zip="N/A";
			}
			if(empty($dob))
			{
				$dob="NA";
			}
			if(empty($age))
			{
				$age="NA";
			}
			$html .= "
					<tr>
				    <td>".++$k."</td>
				    <td>".$userId."</td>
				    <td>".$fullname."</td>
				    <td>".$joinedDate."</td>
				    <td>".$contact_number."</td>
				    <td>".$email."</td>
				    <td>".$state."</td>
				    <td>".$city."</td>
				    <td>".$zip."</td>
				    <td>".$dob."</td>
				    <td>".$Anniversary."</td>
				    <td>".$age."</td>
				    <td>".$referral_code."</td>
				    <td>".$license_number."</td>
				    <td>".$licence_expiration."</td>
				    <td>".$insurance_expiration."</td>
				     <td>".$approvaldate."</td>
				    <td>".$car_transmission."</td>
				    <td>".$recordData."</td>
				   
				    </tr>
		
			";
	
			
		}}else{
			$html.="<tr text-align='center'><td colspan='8' >No Data</td></tr>";
		}
		$html .= "</table>";
		//print_r($html); 
		
		return \Excel::create('newdriver', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');
		//return \PDF::loadHTML($html)->download('newpassenger.pdf');	
	}
	
	public function DnewDriver(Request $request)
		{
			$data = $request->all();
			$time=strtotime($data['startDate']);
			$driverState=$data['driverState']; 
			$driverCity=$data['driverCity'];
			
			$timeto=strtotime($data['endDate']);
			
			/* $driverCount = DB::table('dn_users')
						->leftJoin('role_user', 'dn_users.id', '=', 'role_user.user_id')	
						->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')	
						->where('role_user.role_id',4)
						->whereBetween('dn_users.created_at', array($from,$enxd))
						->count(); */
			$sql=" SELECT COUNT(*) as countdr FROM dn_users ";		
				$sql.=" LEFT join role_user on role_user.user_id = dn_users.id ";
				$sql.=" LEFT join roles on role_user.role_id = roles.id ";
				$sql.=" WHERE role_user.role_id = '4' ";
				
				if(!empty($time) && !empty($timeto))
				{
					$from = date('Y-m-d H:i:s',$time);
					$enxd = date('Y-m-d H:i:s',$timeto);
					$sql .=" AND  dn_users.created_at BETWEEN '$from' AND '$enxd'";
				}
				
				if(!empty($driverState))
				{
					$sql .=" AND  dn_users.state = '".$driverState."'";
				}
				
				if(!empty($driverCity))
				{
					$sql .=" AND  dn_users.city = '$driverCity'";
				}
				//echo $sql;die;
			$driverCount=DB::select(DB::raw($sql));	
			
			 /*  if(!empty($passengerState)){
				
			    }
			if(!empty($passengerCity)){
				
			    }
			if(!empty($from) && !empty($enxd)){
							       
				}			
			 $passCount->count();   */
			
			 
			return @$driverCount[0]->countdr;
		}
		
   /**
	 *
	 *
	 */
	public function newRides(Request $request)
	{
		/* initializing the variables */
		//die("new fun");
		//echo "bfghghg";die;
		$data = $request->all();
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		//$ridereqState=$data['ridereqState'];
		//$ridereqCity=$data['ridereqCity'];
		$field='dn_rides.id';
		$direction='ASC';

		$sql = "SELECT 
			 dn_rides.city_name as cname,count(*) as total_rides,
			(SELECT count(dn_rides.status) FROM `dn_rides` WHERE dn_rides.status = 3 AND `city_name` = cname) as cancel_count, 
			dn_rides.status,sum(dn_payments.amount) as amtPdFrmCty,sum(dn_payments.refund_amount) as amtRefndInCty,

			(SELECT
			SUM((2/100) * dn_payments.amount *(ride_billing_info.miles_charges + ride_billing_info.duration_charges - dn_payments.refund_amount)
			+ride_billing_info.tip + ride_billing_info.pickup_fee) AS earning
			FROM dn_rides
			LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id
			LEFT join dn_payments on dn_rides.id = dn_payments.ride_id
			WHERE `city_name`=`cname` 
			group by dn_rides.driver_id
			order by earning DESC
			limit 0,1) as most_driver_earning,

			(SELECT  dn_rides.driver_id 
			FROM dn_rides
			LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id
			LEFT join dn_payments on dn_rides.id = dn_payments.ride_id
			WHERE `city_name`=`cname`
			group by driver_id
			order by SUM((2/100) * dn_payments.amount *(ride_billing_info.miles_charges + ride_billing_info.duration_charges - dn_payments.refund_amount)
			+ride_billing_info.tip + ride_billing_info.pickup_fee) DESC
			limit 0,1 ) as most_earning_Did,

			(SELECT
			sum(dn_payments.amount) AS paying
			FROM dn_rides
			LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id
			LEFT join dn_payments on dn_rides.id = dn_payments.ride_id
			WHERE `city_name`=`cname`
			group by dn_rides.passenger_id
			order by paying DESC
			limit 0,1 ) as most_payng_user,

			(SELECT dn_rides.passenger_id 
			FROM dn_rides
			LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id
			LEFT join dn_payments on dn_rides.id = dn_payments.ride_id
			WHERE `city_name`=`cname` 
			group by dn_rides.passenger_id
			order by sum(dn_payments.amount) DESC
			limit 0,1 ) as most_payng_user_id
			
			FROM `dn_rides` 
			left join dn_payments 
			on dn_rides.id=dn_payments.ride_id  
			Where dn_rides.city_name != '' 
			";
		
		if(!empty($startDate) or  !empty($endDate))
		{
			
			 $time=strtotime($startDate);
			 $from = date('Y-m-d H:i:s',$time);
			 $timeto=strtotime($endDate);
			 $enxd = date('Y-m-d H:i:s',$timeto);
			//echo "<script>alert('jfsdjhgfsh');</script>";
			//$startDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			//$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND (dn_rides.created_at BETWEEN '$from' AND '$enxd') ";
			
		}	
		
		$sql .=" group by city_name order by count(city_name) ASC ";
		
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		
		$totalUser=DB::select(DB::raw($sql));
		$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>City Name</th>
						<th>Ride Requests</th>
						<th>Cancelletion</th>
						<th>Amount Billed From City</th>
						<th>Amount Refunded in City</th>
						<th>Most Earning Driver</th>
						<th>Most Earning DriverID</th>
						<th>Most Paying User</th>
						<th>Most Paying UserID</th>
					  </tr>
				';
				
		foreach($totalUser as $k=>$user)
		{
			//echo $user->active;
			$cname=$user->cname;
			$total_rides=$user->total_rides;
			$cancel_count =$user->cancel_count ;
			$amtPdFrmCty  =$user->amtPdFrmCty ;
			$amtRefndInCty=$user->amtRefndInCty;
			$most_driver_earning =$user->most_driver_earning ;
			$most_earning_Did  = $user->most_earning_Did ;
			$most_payng_user   = $user->most_payng_user  ;
			$most_payng_user_id    = $user->most_payng_user_id   ;
			$drName= DB::table('dn_users')->select(DB::raw("CONCAT(first_name,' ',last_name) as drname"))->where('id',@$most_earning_Did)->first();
			$prName= DB::table('dn_users')->select(DB::raw("CONCAT(first_name,' ',last_name) as prname"))->where('id',@$most_payng_user_id)->first();
			$driverName=@$drName->drname;
			$passengerName=@$prName->prname;
			if(empty($driverName) || empty($passengerName)){
				continue;
			}
			if(empty($cname))
			{
				$cname="N/A";
			}if(empty($driverName))
			{
				$driverName="N/A";
			}if(empty($passengerName))
			{
				$passengerName="N/A";
			}
			
			if(empty($total_rides))
			{
				$total_rides="N/A";
			}
			if(empty($cancel_count))
			{
				$cancel_count="N/A";
			}
			if(empty($amtPdFrmCty))
			{
				$amtPdFrmCty="N/A";
			}
			
			if(empty($amtRefndInCty))
			{
				$amtRefndInCty="N/A";
			}
			if(empty($most_driver_earning))
			{
				$most_driver_earning="N/A";
			}
			if(empty($most_earning_Did))
			{
				$most_earning_Did="NA";
			}
			if(empty($most_payng_user))
			{
				$most_payng_user="NA";
			}
			if(empty($most_payng_user_id))
			{
				$most_payng_user_id="NA";
			}
			$html .= "
					<tr>
				    <td>".++$k."</td>
				    <td>".$cname."</td>
				    <td>".$total_rides."</td>
				    <td>".$cancel_count."</td>
				    <td>".$amtPdFrmCty."</td>
				    <td>".$amtRefndInCty."</td>
				    <td>".$driverName."</td>
				    <td>".$most_earning_Did."</td>
				    <td>".$passengerName."</td>
				    <td>".$most_payng_user_id."</td>
				    </tr>
		
			";
	
			
		}
		$html .= "</table>";
		//print_r($html); die;
		return \Excel::create('newrideRequests', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');
		//return \PDF::loadHTML($html)->download('newrideRequests.pdf');	
	}
	
	public function DnewRide(Request $request)
		{
			$data = $request->all();
			$time=strtotime($data['startDate']);
			$from = date('Y-m-d H:i:s',$time);
			$timeto=strtotime($data['endDate']);
			$enxd = date('Y-m-d H:i:s',$timeto);
			
			/* $driverCount = DB::table('dn_users')
						->leftJoin('role_user', 'dn_users.id', '=', 'role_user.user_id')	
						->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')	
						->where('role_user.role_id',4)
						->whereBetween('dn_users.created_at', array($from,$enxd))
						->count(); */
			
				$RideData = DB::table('dn_rides')
				->select(DB::raw('city_name,count(city_name) as counting,DATE_FORMAT(max(date(dn_rides.created_at)),"%m/%d/%Y") as ridemaxDate,DATE_FORMAT(min(date(dn_rides.created_at)),"%m/%d/%Y") as rideminDate'))
							->whereBetween('dn_rides.created_at', array($from,$enxd))
							->where('city_name','!=','')
							->orderBy('city_name', 'ASc')
							->groupBy('city_name')
							->get();
				/*MOST-LEAST RIDE CODE START*/
				$max = -9999999; //will hold max val
				$found_item = null; //will hold item with max val;
				
				$least = 9999999; //will hold max val
				$found_item_least = null; //will hold item with max val;
				foreach($RideData as $k=>$v) 
				{
					if($v->counting>$max)
					{
					   $max = $v->counting;
					   $found_item = $k;
					}
				}
				
				foreach($RideData as $lk=>$lv) 
				{
					if($lv->counting < $least)
					{
					   $least = $lv->counting;
					   $found_item_least = $lk;
					}
				}
				
				$maxRideCityIndex = $found_item;
				$leastRideCityIndex = $found_item_least;
				
				$RideData['maxRideCityIndex']=$maxRideCityIndex;
				$RideData['leastRideCityIndex']=$leastRideCityIndex;
				
				/*MOST-LEAST RIDE CODE END*/		
				return json_encode($RideData);
			
			
		}
		
		public function refund(Request $request)
		{
			$data = $request->all();
			$refundState=$data['refundState'];
			$refundCity=$data['refundCity'];
			if(@$data['startDate'] && @$data['endDate'])
			{
				$startDate=$data['startDate'];
				$endDate=$data['endDate'];
			}
			
			$field='dn_rides.id';
			$direction='ASC'; 
			$sql= "SELECT dn_rides.id as rideId,date(dn_rides.created_at) as rideDate,(dn_payments.refund_amount+dn_payments.tip_refund) as refund_amount,dn_rides.driver_id,dn_rides.passenger_id FROM dn_rides ";
			$sql.= "LEFT JOIN dn_payments ON dn_payments.ride_id=dn_rides.id";
			
			if(!empty($startDate) &&  !empty($endDate))
			{	
				$startDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
				$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				$sql .=" WHERE (dn_payments.updated_at BETWEEN '$startDate' AND '$endDate') ";
				
			}
			if(!empty($refundState))
			{
				$sql .=" AND dn_rides.state_code = '$refundState' ";
			}
			if(!empty($refundCity))
			{
				$city=DB::table('dn_cities')->where('id',$refundCity)->first();
			    $sql .=" AND  dn_rides.city_name = '$city->city'";
			}
		

		$totalRefund=DB::select(DB::raw($sql));
		$html='<table> <tr><th colspan="6">From '.date('m/d/Y', strtotime($startDate)).'  To '.date('m/d/Y', strtotime($endDate)).' </th></tr>
					  <tr>
						<th>Sr. No.</th>
						<th>Ride ID</th>
						<th>Ride Date</th>
						<th>Driver ID</th>
						<th>Passenger Id</th>
						<th>Refund Amount</th>
						
						
					  </tr>
				';
	if(!empty($totalRefund)){
		foreach($totalRefund as $k=>$refund)
		{ 
			//echo $user->active;
			if(!empty($refund->refund_amount) && $refund->refund_amount !=0)
			{
			$rideId=$refund->rideId;
			$rideDate=$refund->rideDate;
			$driver_id =$refund->driver_id ;
			$passenger_id  =$refund->passenger_id ;
			$refund_amount=$refund->refund_amount;
			
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			
			if(empty($rideDate))
			{
				$rideDate="N/A";
			}
			if(empty($driver_id))
			{
				$driver_id="N/A";
			}
			if(empty($passenger_id))
			{
				$passenger_id="N/A";
			}
			
			
			
			$html .= "
					<tr>
				    <td>".++$k."</td>
				    <td>".$rideId."</td>
				    <td>".$rideDate."</td>
				    <td>".$driver_id."</td>
				    <td>".$passenger_id."</td>
				    <td>".$refund_amount."</td>
				   
				    </tr>
		
			";
	
			
	}}}else{
		$html .="<tr><td colspan='6'>No DATA AVAILABLE</td></tr>";
		
	}
		$html .= "</table>";
		//print_r($html); die;
		return \Excel::create('Refund', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');
		//return \PDF::loadHTML($html)->download('newrideRequests.pdf');	
		}
	
	
	public function Drefund(Request $request)
		{
			$data = $request->all();
			$time=strtotime($data['startDate']);
			$refundState=$data['refundState'];
			$refundCity=$data['refundCity'];
			//$from = date('Y-m-d H:i:s',$time);
			$timeto=strtotime($data['endDate']);
			// $enxd = date('Y-m-d H:i:s',$timeto);
			/* $refundData= DB::table('dn_rides')
						->select(DB::raw('SUM(dn_payments.refund_amount) as totalRefund'))
						->leftJoin('dn_payments', 'dn_rides.id', '=', 'dn_payments.ride_id')	
						->whereBetween('dn_rides.created_at', array($from,$enxd))
						->get(); */
			$sql= "SELECT  (SUM(dn_payments.refund_amount)+SUM(dn_payments.tip_refund)) as totalRefund FROM dn_rides ";
			$sql.= "LEFT JOIN dn_payments ON dn_payments.ride_id=dn_rides.id";
			if(!empty($time) &&  !empty($timeto))
			{	
				$startDate=date('Y-m-d H:i:s',$time);
				$endDate=date('Y-m-d H:i:s',$timeto);
				$sql .=" WHERE (dn_payments.updated_at BETWEEN '$startDate' AND '$endDate') ";
				
			}
			if(!empty($refundState))
			{
				$sql .=" AND dn_rides.state_code = '$refundState' ";
			}
			if(!empty($refundCity))
			{
				$city=DB::table('dn_cities')->where('id',$refundCity)->first();
			    $sql .=" AND  dn_rides.city_name = '$city->city'";
			}
			
		$totalRefund=DB::select(DB::raw($sql));
		
			if(!empty($totalRefund[0]->totalRefund))
			{
				return $totalRefund[0]->totalRefund;
			}else{return 0;}
		}
		
		public function locationQueries($startDate=null,$endDate=null){
			/**
			  * SUBQUERY TO FIND PASSENGERS COUNT
			  **/
			$subquery1="(SELECT COUNT(*) FROM dn_users LEFT JOIN dn_cities ON dn_users.city = dn_cities.id ";
			$subquery1.=" LEFT join role_user on role_user.user_id = dn_users.id ";
			$subquery1.=" LEFT join roles on roles.id = role_user.role_id ";
			$subquery1.=" WHERE role_user.role_id = '3'  AND dn_users.city = cityId";
			/**
			  * SUBQUERY TO FIND DRIVERS COUNT
			  **/
			$subquery2="(SELECT COUNT(*) FROM dn_users LEFT JOIN dn_cities ON dn_users.city = dn_cities.id ";
			$subquery2.=" LEFT join role_user on role_user.user_id = dn_users.id ";
			$subquery2.=" LEFT join roles on roles.id = role_user.role_id ";
			$subquery2.=" WHERE role_user.role_id = '4' AND dn_users.city = cityId";
			
			
			/**
			  * SUBQUERY TO FIND TOTAL REVENUE
			  **/
			$subquery3 = "(SELECT SUM(dn_payments.amount) FROM dn_rides ";
			$subquery3 .= " LEFT join dn_payments ON dn_rides.id = dn_payments.ride_id";
			$subquery3 .= " LEFT join dn_cities ON dn_cities.city = dn_rides.city_name";
			$subquery3 .= " WHERE dn_rides.city_name=cityName  ";
			
			/**
			  * SUBQUERY TO FIND PAYOUTS
			  **/
			$subquery4= " (SELECT
			SUM(driver_earning) AS earning
			FROM dn_rides
			LEFT join ride_billing_info on dn_rides.id = ride_billing_info.ride_id
			LEFT join dn_payments on dn_rides.id = dn_payments.ride_id
			WHERE `city_name`=`cityName` ";
			if(!empty($startDate) &&  !empty($endDate))
			{
				$startDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
				$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				$subquery3 .= " AND dn_payments.created_at BETWEEN '$startDate' AND '$endDate'";
				$subquery4 .= " AND dn_payments.created_at BETWEEN '$startDate' AND '$endDate'";
			}
			$subquery3 .= " ) as Trevenue";
			$subquery4 .= " ) as payouts ";
			$subqueries5 = "(SELECT(Trevenue - payouts)) as profit";
			/**
			  * CONDITION TO CHECK IF ANY CITY IS SELECTED
			  **/
			  //print_r($data);exit;
			if(@$data['city'] && !empty($data['city']) && $data['city'] != '0')
			{
				
				$city = $data['city'];
				$subquery1 .=" AND dn_users.city = $city  GROUP BY dn_users.city) as passCount";
				$subquery2 .=" AND dn_users.city = $city  GROUP BY dn_users.city) as driverCount";
			}else{
				$subquery1 .=" ) as passCount";
				$subquery2 .=" ) as driverCount";
			}
			
			/**
			  * MAIN QUERY TO FIND ALL FIELDS (MERGING ALL SUBQUERIES HERE)
			  **/
			  //echo $subquery1;exit;
			$sql = "SELECT dn_cities.id as cityId,dn_cities.city as cityName,$subquery1,$subquery2,$subquery3,$subquery4,$subqueries5  FROM dn_cities where dn_cities.id in (select distinct city from dn_users)";
			//echo $sql;exit;
			return $sql ;}
			
			
		public function location(Request $request)
		{
			$data = $request->all();
			
			$sql=$this->locationQueries($data['startDate'],$data['endDate']);
			//echo $sql;die;
			$conditions = array();
			if(@$data['city'] && !empty($data['city']) && $data['city'] != '0')
			{
				
				$city = $data['city'];
				//$sql .= "WHERE dn_users.city = $city ";
				$conditions = array_merge($conditions,array("dn_users.city = $city") );
				//$sql .= "GROUP BY dn_cities.id";
				
			}else{
				//$sql .= "GROUP BY dn_cities.id";
			}
			
			if(@$data['startDate'] && !empty($data['startDate'] && @$data['endDate'] && !empty($data['endDate'])))
			{
				$startDate=$data['startDate'];
				$endDate=$data['endDate'];
				$startDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
				$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				//$sql .=" WHERE (dn_users.created_at BETWEEN '$startDate' AND '$endDate') ";
				$conditions = array_merge($conditions,array("dn_users.created_at BETWEEN '$startDate' AND '$endDate'"));	
			}
			$where='';
			if(!empty($data['startDate']) && !empty($data['endDate']) && !empty($data['city']) && $data['city'] != '0'){
				$finalCondition = implode(" AND ",$conditions);
				$where = "WHERE ".$finalCondition;
				
			}else if(!empty($data['startDate']) && !empty($data['endDate']) or (!empty(@$data['city'])))
			{
				$finalCondition = " WHERE ".implode("  ",$conditions);
				$sql.=$finalCondition ;
			}
			
			if($where != ""){
				$sql = $sql.$where;
			}
			$sql .= " GROUP BY dn_cities.id ";
			// echo $sql;
			// echo "<pre>";
			// echo $sql;
			$locData = DB::select(DB::raw($sql));
			//print_r($locData);die;
			$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>City Name</th>
						<th>No. Of Passengers In City</th>
						<th>No. Of Drivers In City</th>
						<th>Total Revenues</th>
						<th>Total Payouts</th>
						<th>Total profits</th>
					  </tr>
				';
		
		//print_r($locData);
		$allpasngr=0;$alldrvr=0;
		$totalrevenue=0;$totalpayout=0;
		foreach($locData as $k=>$loc)
		{
			//echo $user->active;
			$cityName = $loc->cityName;
			$passCount = $loc->passCount;
			$driverCount  = $loc->driverCount ;
			$Trevenue  = $loc->Trevenue ;
			$payouts = $loc->payouts;
			$profit = $loc->profit;
			if(@$loc->cityId && !empty($loc->cityId))
			{
				if(empty($cityName))
				{
					$cityName="N/A";
				}
			
			 if(empty($passCount))
				{
					$passCount="0";
				}
			if(empty($driverCount))
				{
					$driverCount="0";
				}
			if(empty($Trevenue))
				{
					$Trevenue="0";
				}
			
			if(empty($payouts))
				{
					$payouts="0";
				}
			if(empty($profit))
				{
					$profit="0";
				} 
			if($driverCount!=0 || $passCount!=0 || $Trevenue!=0 || $payouts!=0 || $profit!=0){
			$html .= "
					<tr>
				    <td>".++$k."</td>
				    <td>".$cityName."</td>
				    <td>".$passCount."</td>
				    <td>".$driverCount."</td>
				    <td>".$Trevenue."</td>
				    <td>".$payouts."</td>
				    <td>".$profit."</td>
				    </tr>
		
			";
			}
			$allpasngr+=$passCount;
			$alldrvr+=$driverCount;
			$totalrevenue+=$Trevenue;
			$totalpayout+=$payouts;
			}
		}
		$html .= "<tr><td></td><td>TOTAL</td><td>".$allpasngr."</td><td>".$alldrvr."</td><td>".$totalrevenue."</td><td>".$totalpayout."</td><td>".($totalrevenue-$totalpayout)."</td></tr></table>";
		//print_r($html); die;
		return \Excel::create('Location', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');
		//return \PDF::loadHTML($html)->download('newrideRequests.pdf');		
		}
		
	 public function Dlocation(Request $request)
		{
			$data = $request->all();
			
			if(@$data['city'] != 0)
			{
				
				$cityId=@$data['city'];
				$cityName=@$data['cityName'];
			}
			if(@$data['startDate'] && @$data['endDate']){
					$startDate=@$data['startDate'];
					$endDate=@$data['endDate'];
					$sql=$this->locationQueries($startDate,$endDate);
					$startDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
					$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				}else{
					$sql=$this->locationQueries('','');
				}
		
		/* $sql1= "Select Count(*) as passengers FROM dn_users LEFT JOIN role_user on role_user.user_id = dn_users.id LEFT JOIN roles ON roles.id = role_user.role_id WHERE role_user.role_id=3 ";
		
		$sql2= "Select Count(*) as drivers FROM dn_users LEFT JOIN role_user on role_user.user_id = dn_users.id LEFT JOIN roles ON roles.id = role_user.role_id WHERE role_user.role_id=4  "; */
		
		/* $drivers=DB::table('dn_users')
					->leftJoin('role_user', 'role_user.user_id', '=', 'dn_users.id')		
					->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
					->where('role_user.role_id','4')
					->orwhere('dn_users.city',@$cityId)
					->whereBetween('dn_users.created_at', array($startDate,$endDate))
					->count(); 
		$totalRevenue= DB::table('dn_payments')
					->select(DB::raw('SUM(amount)  as total'))
					->whereBetween('dn_payments.created_at', array($startDate,$endDate))
					->get();
		$payouts = DB::table('dn_rides')
					->Select(DB::raw('SUM((2/100) * dn_payments.amount *(ride_billing_info.miles_charges + ride_billing_info.duration_charges - dn_payments.refund_amount)
					+ride_billing_info.tip + ride_billing_info.pickup_fee) AS payouts'))
					->LeftJoin('ride_billing_info','dn_rides.id','=','ride_billing_info.ride_id')
					->LeftJoin('dn_payments','dn_rides.id','=','dn_payments.ride_id')
					->whereBetween('dn_rides.created_at', array($startDate,$endDate))
					->get();*/
		/* $sql3= "SELECT SUM(dn_payments.amount)  as total FROM  dn_payments LEFT JOIN dn_rides ON dn_rides.id = dn_payments.ride_id ";
		$sql4 = "SELECT SUM((2/100) * dn_payments.amount *(ride_billing_info.miles_charges + ride_billing_info.duration_charges - dn_payments.refund_amount)
					+ride_billing_info.tip + ride_billing_info.pickup_fee) AS payouts  FROM dn_rides  ";
		$sql4 .= " LEFT JOIN ride_billing_info ON dn_rides.id = ride_billing_info.ride_id";
		$sql4 .= " LEFT JOIN dn_payments ON dn_rides.id = dn_payments.ride_id";
		if(@$startDate && @$endDate)
		{
			$sql1 .= " AND dn_users.created_at BETWEEN '$startDate' AND '$endDate' ";
			$sql2 .= " AND dn_users.created_at BETWEEN '$startDate' AND '$endDate' ";
			$sql3 .= " AND dn_payments.created_at BETWEEN '$startDate' AND '$endDate' ";
			$sql4 .= " AND dn_rides.created_at BETWEEN '$startDate' AND '$endDate' ";
		} */
		
		/* if(@$cityId)
		{
			$sql1 .= " AND dn_users.city = $cityId";
			$sql2 .= " AND dn_users.city = $cityId";
			$sql3 .= " AND dn_rides.city_name = '$cityName'";
			$sql4 .= " AND dn_rides.city_name = '$cityName'";
		}
		$passengers=DB::select(DB::raw($sql1));
		$drivers=DB::select(DB::raw($sql2));
		$totalRevenue=DB::select(DB::raw($sql3));
		$payouts=DB::select(DB::raw($sql4));
		$tRevenue=$totalRevenue[0]->total;
		
		if(empty($tRevenue)){$tRevenue=0;}
		$payouts = $payouts[0]->payouts;
		$tRevenue=number_format((float)$tRevenue, 2, '.', '');
		$payouts=number_format((float)$payouts, 2, '.', '');
		//print_r($passengers);
		if(empty($payouts)){$payouts=0;} */
		
		
		$conditions = array();
			if(@$data['city'] && !empty($data['city']) && $data['city'] != 0)
			{
				
				  $city = $data['city']; 
				//$sql .= "WHERE dn_users.city = $city ";
				$conditions = array_merge($conditions,array("dn_users.city = $city") );
				//$sql .= "GROUP BY dn_cities.id";
				
			}else{
				// $sql .= "GROUP BY dn_cities.id";
			}
			
			if(@$startDate && @$endDate)
			{
				//$sql .=" WHERE (dn_users.created_at BETWEEN '$startDate' AND '$endDate') ";
				$conditions = array_merge($conditions,array("dn_users.created_at BETWEEN '$startDate' AND '$endDate'"));
				
			}
			$where='';
			if((!empty($data['startDate']) && !empty($data['endDate'])) or (!empty($data['city']) && $data['city'] != 0)){
				
				
				$finalCondition = implode(" AND ",$conditions);
				$where = "WHERE ".$finalCondition;
				
				
			}else if(!empty($data['startDate']) && !empty($data['endDate']))
			{
				$finalCondition = ' where '.implode("  ",$conditions);
				$sql.=$finalCondition ;
			}
			
			if($where != ""){
				$sql = $sql.$where;
			}
			$sql .= " GROUP BY dn_cities.id";
			//echo $sql;exit;
			//echo "<pre>";
			// echo $sql;die;
			$locData = DB::select(DB::raw($sql));
			$passengers=0;
			$drivers=0;
			$tRevenue=0;
			$payouts=0;
			$profit=0;
			foreach($locData as $k=>$v)
			{
				
				$passengers+=$v->passCount;
				$drivers+=$v->driverCount;
				$tRevenue+=$v->Trevenue;
				$payouts+=$v->payouts;
				$profit+=$v->profit;	
			}
		return $dataAll=json_encode(array("passengers"=>$passengers,"drivers"=>$drivers,"tRevenue"=>$tRevenue,"payouts"=>$payouts));
		}		
		
    /**
     * Logout.
     *
     * @return \Response
     */
    public function logout()
    {

       
         \Auth::logout();
		/*$loggedInStatusUpdate = DB::table('dn_users')
		->where("dn_users.id", $_SESSION['admin'])
		->update(['is_logged' => 'false']);
		
		unset($_SESSION['admin']);*/
		
        return $this->redirect('login.index');
    }

    /**
     * Settings Page.
     *
     * @return \Response
     */
    public function settings()
    {
        if (! defined('STDIN')) {
            $stdin = fopen("php://stdin", "r");
        }

        return $this->view('settings');
    }

    /**
     * Reinstall the application.
     *
     * @return mixed
     */
    public function reinstall()
    {
        \Artisan::call('migrate:refresh');

        \Artisan::call('db:seed');

        return $this->redirect('settings')->withFlashMessage('Reinstalled success!');
    }

    /**
     * Clear the application cache.
     *
     * @return mixed
     */
    public function clearCache()
    {
        \Artisan::call('cache:clear');

        return $this->redirect('settings')->withFlashMessage('Application cache cleared!');
    }

    /**
     * Update the settings.
     *
     * @return mixed
     */
    public function updateSettings()
    {
        $settings = \Input::all();

        foreach ($settings as $key => $value) {
            $option = str_replace('_', '.', $key);
		
            Option::findByKey($option)->update([
                'value' => $value
            ]);
        }

        return \Redirect::back()->withFlashMessage('Settings has been successfully updated!');
    }

    /**
     * Show article.
     *
     * @param  int $id
     * @return mixed
     */
    public function showArticle($id)
    {
        try {
        	return true;
            
        } catch (ModelNotFoundException $e) {
            return \App::abort(404);
        }
    }
	
	
	 public function ageCalculator($dob=null)
 {
	if(!empty($dob)){
		$birthdate = new DateTime($dob);
		$today   = new DateTime('today');
		$age = $birthdate->diff($today)->y;
		return $age;
	}else{
		return 0;
	}
 }
 
	public function allDriverListReport(Request $request)
	{
		/* initializing the variables */
		//die("new fun");
		//echo "bfghghg";die;
		$data = $request->all();
	    $startDate=isset($data['startDatedriverlist'])?$data['startDatedriverlist']:'';
		$endDate=isset($data['endDatedriverlist'])?$data['endDatedriverlist']:'';
		
		$driverCity=isset($data['city'])?$data['city']:'';
		$driverState=isset($data['state'])?$data['state']:'';
		$field=' dn_users.created_at ';
		$direction=' DESC ';

		$sql ="Select dn_users.unique_code as userId,dn_users.Anniversary,dn_users.driver_approved_on as approvaldate,dn_users.zip_code as zip,dn_states.state,dn_users.id as drID,date(dn_users.created_at) as joinedDate ,dn_users.first_name,dn_users.last_name,dn_users.contact_number,dn_users.email,dn_cities.city as location,dn_users.dob,dn_users.become_driver_request as application,dn_users.is_driver_approved from dn_users ";
		$sql.=" LEFT join role_user on role_user.user_id = dn_users.id ";
		$sql.=" LEFT join dn_cities on dn_cities.id = dn_users.city ";
		$sql.=" LEFT join roles on roles.id = role_user.role_id ";
		$sql.=" LEFT join dn_states on dn_states.state_code = dn_users.state ";
		
		$sql.=" where role_user.role_id = 4 ";
		
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
		}


		if(!empty(@$driverCity))
		{
			
			
			$sql .=" AND  dn_users.city = '$driverCity'";
		}
		
		if(!empty(@$driverState))
		{
			$sql .=" AND  dn_users.state = '$driverState'";
		}			
		$sql .=  "order by $field  $direction ";
		
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		
		$totalUser = DB::select(DB::raw($sql));
		$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>User Id</th>
						<th>Driver Name</th>
						<th>Joining Date</th>
						<th>Phone Number</th>
						<th>Email</th>
						<th>State</th>
						<th>City</th>
						<th>Zip Code</th>
						<th>Dob</th>
						<th>Anniversary date</th>
						<th>Age</th>
						<th>Referral Code</th>
						<th>License No.</th>
						<th>License Exp.</th>
						<th>Insurance Exp.</th>
						<th>Date of Approval</th>
						<th>Car Transmission</th>
						<th>Driver Records</th>
					  </tr>
				';
		if(!empty($totalUser)){
		//	print_r($totalUser); die;
		foreach($totalUser as $k=>$user)
		{
			//echo $user->active;
			$userId=$user->userId;
			$drID=$user->drID;
			$joinedDate=date('m/d/Y',strtotime($user->joinedDate));
			$fullname=$user->first_name. ' '.$user->last_name;
			$contact_number =$user->contact_number;
			$email=$user->email;
			$city=$user->location;
			$state=$user->state;
			$dob = date('m/d/Y',strtotime($user->dob));
			$zip = $user->zip;
			$age = $this->ageCalculator($dob);
			$approvaldate = date('m/d/Y',strtotime($user->approvaldate));
			$Anniversary = date('m/d/Y',strtotime($user->Anniversary));

			if(empty($userId))
			{
				$userId="N/A";
			}else{
				//echo "<pre>";
				$dn_users_data=DB::table('dn_users_data')->where('user_id',$drID)->first();
				$dn_driver_requests=DB::table('dn_driver_requests')->where('user_id',$drID)->first();
				//print_r($dn_users_data);
				
				//print_r($dn_driver_requests);
								//echo "<hr>";
				if(!empty(@$dn_users_data->referral_code)){
			    	$referral_code=$dn_users_data->referral_code;
			   }else{$referral_code="N/A";}
			   
			   if(!empty(@$dn_users_data->license_number)){
			    	$license_number=$dn_users_data->license_number;
			   }else{$license_number="N/A";}
			   
			   if(!empty(@$dn_driver_requests->licence_expiration)){
				   
			    	$licence_expiration=date('m/d/Y H:i:s',strtotime($dn_driver_requests->licence_expiration));
			   }else{$licence_expiration="N/A";}
			   
			   if(!empty(@$dn_driver_requests->insurance_expiration)){
			    	$insurance_expiration=date('m/d/Y H:i:s',strtotime($dn_driver_requests->insurance_expiration));
			   }else{$insurance_expiration="N/A";} 
			   
			   if(!empty(@$dn_driver_requests->car_transmission)){
			    	$car_transmission=$dn_driver_requests->car_transmission;
			   }else{$car_transmission="N/A";}
			   
			   if(!empty(@$dn_driver_requests->driver_records)){
			    	$driver_records=$dn_driver_requests->driver_records;
					$driver_records=json_decode($driver_records);
					$recordData='<table>
								<thead>
								<tr><th>Sr. No.</th> <th>Question</th> <th>Answer</th></tr>';
					
					foreach($driver_records as $key=> $records)
					{
						if(@$records->answer=='1')
						{
							$answer="Yes";
						}elseif(@$records->answer=='0')
						{
							$answer="No";
						}else{
							$answer=$records->answer;
						}
						$recordData .= "<tr style='text-align:left;'><td>".++$key."</td><td><b>$records->question </b></td><td>$answer</td></tr>";
						
					}
					$recordData.="</table>";
			   }else{$recordData="N/A";}
			}
			
			
			if(empty($joinedDate))
			{
				$joinedDate="N/A";
			}
			if(empty($fullname))
			{
				$fullname="N/A";
			}
			if(empty($contact_number))
			{
				$contact_number="N/A";
			}
			
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($Anniversary))
			{
				$Anniversary="N/A";
			}
			if(empty($approvaldate))
			{
				$approvaldate="N/A";
			}
			if(empty($city))
			{
				$city="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($zip))
			{
				$zip="N/A";
			}
			if(empty($dob))
			{
				$dob="NA";
			}
			if(empty($age))
			{
				$age="NA";
			}
			$html .= "
					<tr>
				    <td>".++$k."</td>
				    <td>".$userId."</td>
				    <td>".$fullname."</td>
				    <td>".$joinedDate."</td>
				    <td>".$contact_number."</td>
				    <td>".$email."</td>
				    <td>".$state."</td>
				    <td>".$city."</td>
				    <td>".$zip."</td>
				    <td>".$dob."</td>
				    <td>".$Anniversary."</td>
				    <td>".$age."</td>
				    <td>".$referral_code."</td>
				    <td>".$license_number."</td>
				    <td>".$licence_expiration."</td>
				    <td>".$insurance_expiration."</td>
				     <td>".$approvaldate."</td>
				    <td>".$car_transmission."</td>
				    <td>".$recordData."</td>
				   
				    </tr>
		
			";
	
			
		}}else{
			$html.="<tr text-align='center'><td colspan='8' >No Data</td></tr>";
		}
		$html .= "</table>";
		//print_r($html); 
		
		return \Excel::create('all_driver_list_report', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');
		//return \PDF::loadHTML($html)->download('newpassenger.pdf');	
	}
	
	
	public function allNewDriverListReport(Request $request)
	{
		/* initializing the variables */
		//die("new fun");
		//echo "bfghghg";die;
	
		$data = $request->all();
		//	print_r($data); die;
	    $startDate=isset($data['startDatedriverlist'])?$data['startDatedriverlist']:'';
		$endDate=isset($data['endDatedriverlist'])?$data['endDatedriverlist']:'';
		
		$driverCity=isset($data['city'])?$data['city']:'';
		$driverState=isset($data['state'])?$data['state']:'';
		$field=' dn_users.created_at ';
		$direction=' DESC ';

		$sql ="Select 
				dn_users.unique_code as userId,
				dn_users.Anniversary, 
				dn_users.become_driver_request,
				dn_users.zip_code as zip,
				dn_states.state,dn_users.id as drID,
				date(dn_users.created_at) as joinedDate,
				dn_users.first_name,
				dn_users.last_name,
				dn_users.contact_number,
				dn_users.email,
				dn_users.address_1,
				dn_users.address_2,
				dn_cities.city as location,
				dn_users.dob,
				dn_users.become_driver_request as application,
				dn_users.is_driver_approved 
				
				From dn_users 
				
				LEFT join role_user 
				ON role_user.user_id = dn_users.id 
				
				LEFT JOIN dn_cities 
				ON dn_cities.id = dn_users.city 
				
				LEFT JOIN roles 
				ON roles.id = role_user.role_id 
				
				LEFT JOIN dn_states 
				ON dn_states.state_code = dn_users.state 
				
				WHERE 
				(role_user.role_id ='3' AND dn_users.become_driver_request='1' AND dn_users.is_driver_approved ='0')";
		
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
		}


		if(!empty(@$driverCity))
		{
			
			
			$sql .=" AND  dn_users.city = '$driverCity'";
		}
		
		if(!empty(@$driverState))
		{
			$sql .=" AND  dn_users.state = '$driverState'";
		}			
		$sql .=  " order by $field  $direction ";
		
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		
		$totalUser = DB::select(DB::raw($sql));
		$html='<table>
					  <tr>
						<th>Sr. No.</th>
						<th>User Id</th>
						<th>Driver Name</th>
						<th>Joining Date</th>
						<th>Phone Number</th>
						<th>Email</th>
						<th>Address</th>
						<th>State</th>
						<th>City</th>
						<th>Zip Code</th>
						<th>Dob</th>
						<th>Anniversary date</th>
						<th>Age</th>
						<th>Referral Code</th>
						<th>License No.</th>
						<th>License Exp.</th>
						<th>Insurance Exp.</th>
						<th>Car Transmission</th>
						<th>Driver Records</th>
					  </tr>
				';
		if(!empty($totalUser)){
			
		foreach($totalUser as $k=>$user)
		{
			//echo $user->active;
			$userId=$user->userId;
			$drID=$user->drID;
			$joinedDate=date('m/d/Y',strtotime($user->joinedDate));
			$fullname=$user->first_name. ' '.$user->last_name;
			$contact_number =$user->contact_number;
			$email=$user->email;
			$city=$user->location;
			$address=@$user->address_1." ".@$user->address_2;
			$state=$user->state;
			$dob = date('m/d/Y',strtotime($user->dob));
			$zip = $user->zip;
			$age = $this->ageCalculator($dob);
			$Anniversary = date('m/d/Y',strtotime($user->Anniversary));

			if(empty($userId))
			{
				$userId="N/A";
			}else{
				//echo "<pre>";
				$dn_users_data=DB::table('dn_users_data')->where('user_id',$drID)->first();
				$dn_driver_requests=DB::table('dn_driver_requests')->where('user_id',$drID)->first();
				//print_r($dn_users_data);
				
				//print_r($dn_driver_requests);
								//echo "<hr>";
				if(!empty(@$dn_users_data->referral_code)){
			    	$referral_code=$dn_users_data->referral_code;
			   }else{$referral_code="N/A";}
			   
			   if(!empty(@$dn_users_data->license_number)){
			    	$license_number=$dn_users_data->license_number;
			   }else{$license_number="N/A";}
			   
			   if(!empty(@$dn_driver_requests->licence_expiration)){
				   
			    	$licence_expiration=date('m/d/Y H:i:s',strtotime($dn_driver_requests->licence_expiration));
			   }else{$licence_expiration="N/A";}
			   
			   if(!empty(@$dn_driver_requests->insurance_expiration)){
			    	$insurance_expiration=date('m/d/Y H:i:s',strtotime($dn_driver_requests->insurance_expiration));
			   }else{$insurance_expiration="N/A";} 
			   
			   if(!empty(@$dn_driver_requests->car_transmission)){
			    	$car_transmission=$dn_driver_requests->car_transmission;
			   }else{$car_transmission="N/A";}
			   
			   if(!empty(@$dn_driver_requests->driver_records)){
			    	$driver_records=$dn_driver_requests->driver_records;
					$driver_records=json_decode($driver_records);
					$recordData='<table>
								<thead>
								<tr><th>Sr. No.</th> <th>Question</th> <th>Answer</th></tr>';
					
					foreach($driver_records as $key=> $records)
					{
						if(@$records->answer=='1')
						{
							$answer="Yes";
						}elseif(@$records->answer=='0')
						{
							$answer="No";
						}else{
							$answer=$records->answer;
						}
						$recordData .= "<tr style='text-align:left;'><td>".++$key."</td><td><b>$records->question </b></td><td>$answer</td></tr>";
						
					}
					$recordData.="</table>";
			   }else{$recordData="N/A";}
			}
			
			
			if(empty($joinedDate))
			{
				$joinedDate="N/A";
			}
			if(empty($fullname))
			{
				$fullname="N/A";
			}
			if(empty($contact_number))
			{
				$contact_number="N/A";
			}
			
			if(empty($email))
			{
				$email="N/A";
			}
			if(empty($Anniversary))
			{
				$Anniversary="N/A";
			}
			
			if(empty($city))
			{
				$city="N/A";
			}
			if(empty($address) && $address !=' ')
			{
				$address="N/A";
			}
			if(empty($state))
			{
				$state="N/A";
			}
			if(empty($zip))
			{
				$zip="N/A";
			}
			if(empty($dob))
			{
				$dob="NA";
			}
			if(empty($age))
			{
				$age="NA";
			}
			$html .= "
					<tr>
				    <td>".++$k."</td>
				    <td>".$userId."</td>
				    <td>".$fullname."</td>
				    <td>".$joinedDate."</td>
				    <td>".$contact_number."</td>
				    <td>".$email."</td>
				    <td>".$address."</td>
				    <td>".$state."</td>
				    <td>".$city."</td>
				    <td>".$zip."</td>
				    <td>".$dob."</td>
				    <td>".$Anniversary."</td>
				    <td>".$age."</td>
				    <td>".$referral_code."</td>
				    <td>".$license_number."</td>
				    <td>".$licence_expiration."</td>
				    <td>".$insurance_expiration."</td>
				    
				    <td>".$car_transmission."</td>
				    <td>".$recordData."</td>
				   
				    </tr>
		
			";
	
			
		}}else{
			$html.="<tr text-align='center'><td colspan='8' >No Data</td></tr>";
		}
		$html .= "</table>";
		//print_r($html); 
		
		return \Excel::create('new_applicants_list', function($excel) use ($html) {
            $excel->sheet('Excel', function($sheet) use ($html) {
                $sheet->loadView('excel.export')->with("html", $html);
            });
        })->export('xls');
		//return \PDF::loadHTML($html)->download('newpassenger.pdf');	
	}
	
}
