<?php 
namespace Pingpong\Admin\Controllers;
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
use Session;

/**
 * @Class for Passenger  activities
 * 
 */
class PassengerController extends BaseController {

	
	/**
  	 * @param \User $users
    **/
    public function __construct(UserRepository $repository)
    {
        parent::__construct();
		

    }
	/**
	 * @var \User
	**/
 	protected $users;
 
 	public function refundAjax( Request $request ) {
 		
 		$formData = $request->all();

 		$driver_level = $formData['driver_level'];
 		$ride_id = $formData['ride_id'];
 		$total_amount = $formData['total_amount'];

 		$expChargeID = $formData['expChargeID'];

 		$expRefundAmount = $formData['expRefundAmount'];
 		$expRefundTip = $formData['expRefundTip'];

 		if ($expRefundTip == '') { $expRefundTip = 0.00; }
 		if ($expRefundAmount == '') { $expRefundAmount = 0.00; }

 		$deziRefundAmount = $expRefundAmount + $expRefundTip;

 		$refund_user_id = DB::table('dn_rides')->select('dn_rides.passenger_id')->where('id', $ride_id)->first();
 		$refund_user_id = $refund_user_id->passenger_id;

 		$ride_data['dn_driver_bonus'] = DB::table('dn_driver_bonus')->where('is_active', 1)->first();

 		//echo "<pre>"; print_r($ride_data['dn_driver_bonus']); echo "</pre>"; die();

 		if ($driver_level == 1) { $commission = $ride_data['dn_driver_bonus']->commission_silver; }
 		else if ($driver_level == 2) { $commission = $ride_data['dn_driver_bonus']->commission_gold; }
 		else if ($driver_level == 3) { $commission = $ride_data['dn_driver_bonus']->commission_platinum; }
 		else if ($driver_level == 4) { $commission = $ride_data['dn_driver_bonus']->commission_diamond; }
 		else { $commission = $ride_data['dn_driver_bonus']->commission_silver; }

 		$driver_earning = number_format((float)( $total_amount/100 ) * $commission, 2, '.', '');

 		$where = array(
	        //'id' => $expChargeID,
	        'ride_id' => $ride_id
	        );
		$now = new \DateTime();
 		$update_refund=DB::table('dn_payments')
		->where( $where )
		->update(['tip_refund' => $expRefundTip, 'refund_amount' => $expRefundAmount, 'driver_earning' => $driver_earning,'updated_at'=>$now]);

		if ($update_refund) {
			
			$credit_type    = 4; //1=DeziCredit By Admin, 2=referralCredit, 3=PromoCredit, 4=RefundCredit
			$deziCredit = DB::table('dn_passenger_credits')->where('user_id',$refund_user_id)->orderBy('id', 'desc')->first(); 

            if ( !empty($deziCredit)) {

                $credit_balance = $deziCredit->credit_balance + $deziRefundAmount;
                $credit_txn_type = 'Cr';
                
                $insertCreditID = DB::table('dn_passenger_credits')

                ->insert(
                    ['user_id' 	=> $refund_user_id, 
                    'credit_type' 	=> $credit_type,
                    'credit_txn_type' 	=> $credit_txn_type,
                    'credit_amount' 	=>$deziRefundAmount,
                    'credit_balance' 	=>$credit_balance]
                );

            } else {

                $insertCreditID = DB::table('dn_passenger_credits')
                ->insert(
                    ['user_id' => $refund_user_id, 
                    'credit_type' 	=> $credit_type,
                    'credit_txn_type' 	=> $credit_txn_type,
                    'credit_amount'		=>$deziRefundAmount,
                    'credit_balance'	=>$deziRefundAmount]
                );
            }

	 		$data['azStatus'] = 'success';
	 		$data['azMessage'] = "Success: Refund updated successfully on passenger dezi credit.";
	 		$data['azTitle'] = "SUCCESS : Refund Updated.";
		
		} else {

	 		$data['azStatus'] = 'error';
	 		$data['azMessage'] = "Error: An error occured while updating the refund, Please try again later.";
	 		$data['azTitle'] = 'ERROR : Refund Update.';
		}

 		echo json_encode($data); die();
 	}
		
		/*  function to add call logs for ride  */
		
	/*public function addCL(Request $request)
	{
		$input = $request->input();
		$pquery=$input['pquery'];
		$Qdesc=$input['Qdesc'];
		$rideId=$input['rideId'];
		$Pid=$input['Pid'];
		$values = array('passenger_id' => $Pid,'ride_id' => $rideId,'passenger_query'=>$pquery,'description'=>$Qdesc);
		DB::table('dn_ride_call_details')->insert($values);
		return back();
		
	}*/
	
	/*
 *RIDE CALL DETAILS 
 */
	
 	/*public function callLogs( Request $request ){
		//echo "ytryt";die;

		/* initializing the variables 
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$ride_id = $data['ride_id'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);

		$orderfields=array('0'=>'id','1'=>'passenger_query','2'=>'description','3'=>'created_at');
	   
		
		//print_r($data['order'][0]);
		$field='id';
		$direction='ASC';
		//code for order by data of user
		if(!empty($data['order'][0])){
			foreach($orderfields as $key=>$orderfield){
				if($key==$data['order'][0]['column'] )
				{
					$field=$orderfield;
					$direction=$data['order'][0]['dir'];
				}
			}
		}
		$sql = "SELECT * FROM dn_ride_call_details WHERE ride_id = $ride_id ";
		//$sql="Select * from dn_rides where driver_id = 253";
		
		
		if(@$searchString!='')
		{	
			 $search = "%$searchString%";
			
			$sql .=" WHERE (id LIKE '$search' or passenger_query LIKE '$search' or description LIKE '$search') ";
			
		}
		$sql .= " order by ".$field." ".$direction;
		$totalCallCount=DB::select(DB::raw($sql));
		$totalCallCount= count($totalCallCount);
		//echo $totalCallCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalCall=DB::select(DB::raw($sql));
		$totalRecords = 0;		
		$Data="";
		


		foreach($totalCall as $call)
		{
			//echo $user->active;
			$callId=$call->rideId;
			$passengerQuery=$call->rideId;
			$calldetails=$call->rideId;
			$timeStamp=$call->rideId;
			
					
			if(empty($callId))
			{
				$callId="N/A";
			}if(empty($passengerQuery))
			{
				$passengerQuery="N/A";
			}if(empty($calldetails))
			{
				$calldetails="N/A";
			}if(empty($timeStamp))
			{
				$timeStamp="N/A";
			}
			
			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.","
					.'"'.$callId .'"' . ","
					. '"'.$passengerQuery.'"' . ","
					. '"'.$calldetails .'"'.","
					. '"'.$timeStamp.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalCallCount).',
			  "recordsFiltered":'.($totalCallCount).',
			  "data": ['.$newData.']
		}';} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
 	}*/


	/**
	 * @FUNCTION: Single ride detail  
	 * @Author : Harish Chauhan
	 *
	**/
 	public function passengerRideDetail( $user_id = null, $ride_id = null ) {
 		$rideData = [];
		$user=$this->getUserDetails($user_id); //get user personal details

		//echo "<pre>"; print_r($user); die();

		$dob = $user->dob;
		if($user->active==1) { $user->active='Active'; } else { $user->active='Suspended'; }
		$age= $this->ageCalculator($dob);

		$ride_data['ride_data'] = DB::table('dn_rides')->where('id',$ride_id)->first();

		$sql="Select dn_rides.id as rideId, dn_rides.payment_status, dn_rides.created_at as timeStamp, dn_rides.charge_id, dn_users.first_name,dn_users.last_name,dn_users.unique_code as passenger_id,dn_report_an_issuse.message,dn_rides.status,dn_payments.amount, dn_payments.dezicredit as deziCredit, dn_payments.payment_type as ride_payment_type, dn_payments.payment_id as TXN_ID, dn_payments.tip_percentage as tip, dn_payments.tip_refund, dn_payments.refund_amount, dn_payment_accounts.account_type, dn_payment_accounts.masked_number from dn_rides ";
		$sql.="LEFT join dn_users on dn_rides.passenger_id = dn_users.id ";
		$sql.="LEFT join dn_report_an_issuse on dn_rides.id = dn_report_an_issuse.ride_id ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.="LEFT join dn_payment_accounts on dn_rides.payment_id = dn_payment_accounts.id ";
		
		$countSQL = $sql." where dn_rides.passenger_id = $user_id";
		
		$dataSQL = $sql." where dn_rides.passenger_id = $user_id AND dn_rides.id = $ride_id LIMIT 1";
		
		$totalRideCount=DB::select(DB::raw($countSQL));

		$ride_data['collected_ride_data'] = DB::select(DB::raw($dataSQL));

		//echo "<pre>"; print_r($collectedrideData); die();

		$ride_data['total_ride_count'] 		= count($totalRideCount);

		$ride_data['ride_issues'] = DB::table('dn_report_an_issuse')
						->select('dn_report_an_issuse.*', 'dn_cancellation_category.category as main_category', 'dn_cancellation_subcategory.subcategory as sub_category')
						
						->leftJoin('dn_cancellation_category', 'dn_cancellation_category.id', '=', 'dn_report_an_issuse.category')	
						->leftJoin('dn_cancellation_subcategory', 'dn_cancellation_subcategory.id', '=', 'dn_report_an_issuse.sub_category')
						->where('dn_report_an_issuse.ride_id',$ride_id)
						->get();

		//echo "<pre>"; print_r($ride_data['ride_issues']); die();

		$ride_data['rating_received'] = DB::table('dn_rating')	
						->select('dn_rating.rate_by', 'dn_rating.rating')
						->where('ride_id',$ride_id)
						->get();

		
		$ride_data['total_reported_issue']	= count(DB::table('dn_report_an_issuse')->where("user_id",$user_id)->get());
		
		$last_ride_data = DB::table('dn_rides')->select('created_at')->where("passenger_id",$user_id)->orderBy('created_at', 'desc')->first();
		$ride_data['last_ride'] = $last_ride_data->created_at;

		$ride_data['bill_cleared'] = DB::table('dn_rides')
						->leftJoin('dn_payments', 'dn_rides.payment_id', '=', 'dn_payments.id')	
						->where('dn_rides.passenger_id',$user_id)
						->where('dn_rides.payment_status',1)
						->sum( 'dn_payments.amount' );

		$ride_data['pending_bill'] = DB::table('dn_rides')
						->leftJoin('dn_payments', 'dn_rides.payment_id', '=', 'dn_payments.id')	
						->where('dn_rides.passenger_id',$user_id)
						->where('dn_rides.payment_status',0)
						->where('dn_rides.bill_generated',1)
						->sum( 'dn_payments.amount' );
		//echo "<pre>"; print_r($ride_data); die;
		return $this->view('users.ride_detail',compact('user','age','user_id', 'ride_id', 'ride_data' ));
 	}

	 
 	public function testAZ09( Request $request ){

		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$driverId = $data['useriddriver'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);

		$orderfields=array('0'=>'dn_rides.id','1'=>'dn_rides.id','2'=>'dn_rides.created_at','3'=>'Dfullname','4'=>'DrId','6'=>'dn_rides.status','7'=>'dn_payments.amount');
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		
		//print_r($data['order'][0]);
		$field='dn_rides.id';
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
		$sql = "Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,
				dn_report_an_issuse.message,dn_rides.status
				,dn_payments.amount,dn_rides.driver_id as DID, 
				CONCAT(DriverDetail.first_name,' ',DriverDetail.last_name) as Dfullname,
				DriverDetail.unique_code as DrId from dn_rides 

				LEFT join dn_users on dn_rides.passenger_id = dn_users.id 
				LEFT join dn_users as DriverDetail  on DriverDetail.id = dn_rides.driver_id 
				LEFT join dn_report_an_issuse on dn_rides.id = dn_report_an_issuse.ride_id 
				LEFT join dn_payments on dn_rides.id = dn_payments.ride_id 
				where dn_rides.passenger_id = $driverId ";
		//$sql="Select * from dn_rides where driver_id = 253";
		
		if(!empty($startDate) &&  !empty($endDate))
		{	
			$startDate= date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_rides.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		if(@$searchString!='')
		{	
			 $search = "%$searchString%";
			/* 
			
			if($searchString=="In process" or $searchString=="In p%" or $searchString=="in process" or $searchString=="in p%")
			{
				$search = 0;
			
			}
			if($searchString == "complete" or $searchString=="Complete" or $searchString=="com" or $searchString=="Com")
			{
				$search =2;
			}
			if($searchString == "cancel" or $searchString=="Cancel" or $searchString=="can%" or $searchString=="%Can%")
			{
				$search =3;
			} */
			$sql .=" HAVING (Dfullname LIKE '$search' or DrId LIKE '$search' or dn_rides.id LIKE '$search') ";
			//$sql .=" AND  (dn_rides.driver_id LIKE '$search' or DfullnameCname LIKE '$search' or dn_rides.status LIKE '$search' or dn_rides.id LIKE '$search' or dn_users.unique_code LIKE '$search') ";
		}
		$sql .= " order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		
		
		$totalRide=DB::select(DB::raw($sql));
		
		//print_r($totalRide);exit;
		
		/* $totalRide = DB::table('dn_rides')
					->select(array('*'))
					->join('dn_users', 'dn_rides.driver_id', '=', 'dn_users.id')
					->where('dn_rides.driver_id',$driverId)
					->take($limit)->offset($offset) ->orderBy($field,$direction)
					->get(); */
		//$users = array();
		
		$totalRecords = 0;		
		$Data="";
		$data['rides_taken'] = 0;


		foreach($totalRide as $ride)
		{
			//echo $user->active;
			$rideId=$ride->rideId;
			$drivername=$ride->Dfullname;
			
			$timeStamp=$ride->timeStamp;
			
			
			$DrId=$ride->DrId;
			
			$reportedIssue=count(DB::table('dn_report_an_issuse')->where("ride_id",$rideId)->get());
			$billingAmount=$ride->amount;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			
			//$action = "<span class='label-success label '><a href='passengerRideDetail/".base64_encode(convert_uuencode($rideId))."'> View </a></span>";
			$action = "<span class='label-success label '><a href='passengerRideDetail/".$driverId."/".$rideId."'> View </a></span>";
					
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			
			if(empty($billingAmount))
			{
				$billingAmount="N/A";
			}else{
				$billingAmount="$".$billingAmount;
			}
			if(empty($drivername))
			{
				$drivername="N/A";
			}
			if(empty($unique_code))
			{
				$unique_code="N/A";
			}
			if(empty($DrId))
			{
				$DrId="N/A";
			}
			if(empty($reportedIssue))
			{
				$reportedIssue="0";
			}
			
			$rideStatus=$ride->status;
			
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
				$rideStatus="Ride Cancel";
				
			}
			else if($rideStatus=='4')
			{
				$rideStatus="No Response";
				
			}
			else if($rideStatus=='5')
			{
				$rideStatus="Cancel ride request";
				
			}
			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$rideId .'"' . ",". '"'.$timeStamp.'"' . ",". '"'.$drivername .'"'.",". '"'.$DrId.'"'.",". '"' .$reportedIssue. '"'.",". '"'.$rideStatus.'"'.",". '"'.$billingAmount.'"'.",". '"'.$action.'"'."]";
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

	public function issueStatus(Request $request)
	{
		 $data=$request->all();
		 $status=$data['status'];
		  $now = new DateTime();
		 if($status==0){$status=1;}else{$status=0;}
		 $id=$data['id'];
		 $updates = DB::table('dn_report_an_issuse')
            ->where('id', $id)
            ->update(['status' => $status,'updated_at'=>$now]);
		 $stat=DB::table('dn_report_an_issuse')
		    ->select('status')
            ->where('id',$id)
			->first();
		 print_r((int)$stat->status);
		 
		 die;
		 
	 }
 	
 	public function pasangerPaymentHistory(Request $request)
 	{
 		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$passengerId = $data['useriddriver'];
		//print_r($driverId);die;
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
		$orderfields=array('0'=>'dn_rides.id','1'=>'dn_rides.id','2'=>'dn_rides.created_at','3'=>'dn_users.first_name','4'=>'dn_users.id','8'=>'dn_payments.amount');
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		
		//print_r($data['order'][0]);
		$field='dn_rides.id';
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
		
		//	$_SESSION['countRide'] = $offset;
		
		$subquery1="(SELECT  CONCAT(dn_users.first_name,' ',dn_users.last_name) AS full_name  from dn_users where dn_users.id = DID)  as fullname";
		
		$sql="Select dn_rides.id as rideId,dn_rides.created_at as timeStamp,dn_rides.driver_id as DID,$subquery1, dn_users.first_name,dn_users.last_name,dn_users.unique_code as passenger_id, dn_users.id as user_id, dn_rides.payment_status,dn_payments.amount from dn_rides ";
		$sql.="LEFT join dn_users on dn_rides.passenger_id = dn_users.id ";
		$sql.="LEFT join dn_payments on dn_rides.id = dn_payments.ride_id ";
		$sql.=" where dn_payments.user_id = $passengerId";
		//$sql="Select * from dn_rides where driver_id = 253";

		if(!empty($startDate) &&  !empty($endDate))
		{
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_rides.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		if(@$searchString!='')
		{	
			
			$search = "%$searchString%";
			$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_rides.id LIKE '$search') ";
		}
		$sql .= " order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalRide=DB::select(DB::raw($sql));
		
		//print_r($totalRide);exit;
		
		/* $totalRide = DB::table('dn_rides')
					->select(array('*'))
					->join('dn_users', 'dn_rides.driver_id', '=', 'dn_users.id')
					->where('dn_rides.driver_id',$passengerId)
					->take($limit)->offset($offset) ->orderBy($field,$direction)
					->get(); */
		//$users = array();
		$totalRecords = 0;
		
		$Data="";
		foreach($totalRide as $ride)
		{

			//echo "<pre>"; print_r($ride); die();

			//echo $user->active;
			$rideId=$ride->rideId;

			$payment_status=$ride->payment_status;

			if ($payment_status == 0) { $payment_status = 'No Payment'; }
			else if ($payment_status == 1) { $payment_status = 'Payment Success'; }
			else if ($payment_status == 2) { $payment_status = 'Payment Fail'; }
			else { $payment_status = 'N/A'; }

			$timeStamp=$ride->timeStamp;
			$driverName =$ride->fullname;
			
			$DID=$ride->DID;
			
			$reportedIssue=count(DB::table('dn_report_an_issuse')->where("ride_id",$rideId)->get());
			$billingAmount=$ride->amount;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			
			//$action = "<span class='label-success label '><a href='driver-ride-detail/".base64_encode(convert_uuencode($rideId))."'> View </a></span>";

			/* With Demo Ids */
			$demo_rideId 	= $rideId;
			$demo_driverId 	= $ride->user_id;
			$action = "<span class='label-success label '><a href='passengerPaymentDetail/".$demo_driverId."/".$demo_rideId."'> View </a></span>";
			/* /With Demo Ids */
					
			
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			
			if(empty($billingAmount))
			{
				$billingAmount="N/A";
			}else{
				$billingAmount="$".$billingAmount;
			}
			if(empty($driverName))
			{
				$driverName="N/A";
			}
			if(empty($unique_code))
			{
				$unique_code="N/A";
			}
			if(empty($DID))
			{
				$DID="N/A";
			}
			if(empty($reportedIssue))
			{
				$reportedIssue="0";
			}
			
			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$rideId .'"' . ",". '"'.$timeStamp.'"' . ",". '"'.$driverName .'"'.",". '"'.$DID.'"'.",". '"'.$payment_status.'"'.",". '"'.$billingAmount.'"'.",". '"'.$action.'"'."]";
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

 	public function pasangerIssueHistory(Request $request)
 	{
 		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		
			
		$offset = $data['start'];
		$driverId = $data['useriddriver'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
		
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'dn_rides.id','1'=>'dn_rides.id','2'=>'dn_report_an_issuse.created_at','3'=>'dn_users.first_name','4'=>'dn_users.unique_code','7'=>'dn_payments.amount');
		//print_r($data['order'][0]);
		$field='dn_report_an_issuse.id';
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

		$sql="Select dn_report_an_issuse.ride_id as rideId,dn_report_an_issuse.created_at as timeStamp,dn_users.first_name,dn_users.last_name,dn_users.unique_code as passenger_id, dn_users.id as user_id, dn_cancellation_category.category,dn_cancellation_subcategory.subcategory,dn_rides.status,dn_payments.amount from dn_report_an_issuse ";
		$sql.="LEFT join dn_users on dn_report_an_issuse.user_id = dn_users.id ";
		$sql.="LEFT join dn_rides on dn_rides.id = dn_report_an_issuse.ride_id ";
		$sql.="LEFT join dn_payments on dn_report_an_issuse.ride_id = dn_payments.ride_id ";
		$sql.="LEFT join dn_cancellation_category on dn_report_an_issuse.category = dn_cancellation_category.id ";
		$sql.="LEFT join dn_cancellation_subcategory on dn_report_an_issuse.sub_category = dn_cancellation_subcategory.id ";
		$sql.=" where dn_rides.passenger_id = $driverId ";
		//AND dn_report_an_issuse.user_type='passenger'";
		if(!empty($startDate) &&  !empty($endDate))
		{
			
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_report_an_issuse.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		if(@$searchString!='')
		{	
			
			$search = "%$searchString%";
			$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_rides.id LIKE '$search') ";
		}
		$sql .= " order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalIssue=DB::select(DB::raw($sql));
		
		//print_r($totalIssue);exit;
		
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
		foreach($totalIssue as $issue)
		{
			//echo "<pre>"; print_r($issue); echo "</pre>"; die();

			$rideId=$issue->rideId;
			$timeStamp=$issue->timeStamp;
			$passenger_name =$issue->first_name.' '.$issue->last_name;
			$category=$issue->category;
			$subCategory=$issue->subcategory;
			$passenger_id=$issue->passenger_id;
			
			$billingAmount=$issue->amount;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			
			//$action = "<span class='label-success label '><a href='driver-ride-detail/".base64_encode(convert_uuencode($rideId))."'> View </a></span>";
			/* With Demo Ids */
			$demo_driverId = $issue->user_id;
			$demo_rideId = $rideId;
			$action = "<span class='label-success label '><a href='passengerIssueDetail/".$demo_driverId."/".$demo_rideId."'> View </a></span>";
			/* /With Demo Ids */		
			
			if(empty($rideId))
			{
				$rideId="N/A";
			}
			if(empty($billingAmount))
			{
				$billingAmount="N/A";
			}else{
				$billingAmount="$".$billingAmount;
			}
			if(empty($passenger_name))
			{
				$passenger_name="N/A";
			}
			if(empty($unique_code))
			{
				$unique_code="N/A";
			}
			if(empty($passenger_id))
			{
				$passenger_id="N/A";
			}
			if(empty($reportedIssue))
			{
				$reportedIssue="N/A";
			}
			if(empty($category))
			{
				$category="N/A";
			}
			if(empty($subCategory))
			{
				$subCategory="N/A";
			}
			$rideStatus=$issue->status;
			
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
			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$rideId .'"' . ",". '"'.$timeStamp.'"' . ",". '"'.$passenger_name .'"'.",". '"'.$unique_code.'"'.",". '"' .$category ."=>".$subCategory .'"'.",". '"'.$rideStatus.'"'.",". '"'.$billingAmount.'"'.",". '"'.$action.'"'."]";
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


	/**
  	 * @Function for age calculator
  	 * @Params: '$dob' is the date of birth 
  	**/
	
	public function ageCalculator($dob=null)
	{
		if(!empty($dob)){

			$birthdate = new DateTime($dob);
			$today   = new DateTime('today');
			$age = $birthdate->diff($today)->y;
			return $age;
		} else {
			return 0;
		}
	}
 
	/**
	 * @Function for users details
	 * @Params: '$id' is the user id
	**/	
 	
 	public function getUserDetails($id=null)
 	{
		if(!empty($id)) {

			$user = DB::table('role_user')
					->select(array('dn_users.*','role_user.*'))
					->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
					->where('dn_users.id',$id)->first(); // get data from 'dn_users' table.
			
		return $user;
		} else {
			return 0;
		}
	}



	/**
	 * Redirect not found.
	 *
	 * @return Response
	**/
    protected function redirectNotFound()
    {
        return $this->redirect('users.index');
    }

	/**
	 * Display a listing of users
	 * Author : Vaibhav Bharti
	 * @return Response
	**/

	public function index(Request $request)
	{
		
		$data = $request->all();

		$data['usersCount'] = DB::table('role_user')
						//->select(array('dn_users.id'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')
						//->take("10") 
						->count();
						//->paginate(config('admin.user.perpage'));
		//print_r($users );	die;
		$data['activeUsersCount'] = DB::table('role_user')
						//->select(array('dn_users.id'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')
						->where('dn_users.active','1')
						//->take("10") 
						->count();
		//print_r($ActiveUsersCount);die;
		$states = DB::table('dn_states')->get();
		if(isset($data['stateCode']))
		{
			$stateCode=$data['stateCode'];
			$cities = DB::table('dn_cities')->where('state_code',$stateCode)->get();
			echo "<option value=''>---City---</option>";
			foreach($cities as $city)
			{
				echo "<option class='append' value='$city->id'>".$city->city."</option>";
			}
		}
		$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.id) as no_of_users,dn_users.id as pid')))
					->leftjoin('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->leftjoin('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->leftjoin('role_user', 'dn_users.id', '=', 'role_user.user_id')
					->where('dn_users.city','!=',0)
					->where('role_user.role_id',3)
					->where('dn_users.city','!=','')
					->groupBy('dn_users.city')
					->orderBy('no_of_users')
					->get();
		if(@$citys and !empty($citys)){
		$n=count($citys)-1;
		if($n>=0)
		{
			$citiesCount=array('least'=>@$citys[0],'most'=>@$citys[$n]); }
		}else{
			$cty=(object)array('city'=>'N/A','no_of_users'=>'N/A');
			@$citiesCount=array('least'=>@$cty,'most'=>$cty);}
	//echo "fhdhfj";die;
		return $this->view('users.index', compact('data','citiesCount','states'));
	}
 
	/**
	 * @FUNCTION FOR AJAX CALL ON INDEX
	 * @Author : Vaibhav Bharti
	 * @Params : $request
    **/
	public function ajaxIndex(Request $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$searchString=$data['search']['value'];
		$startDate=$data['startDate'];
		$endDate=$data['endDate'];
		
		$billingval1=$data['billingval1'];
		$billingval2=$data['billingval2'];
		
		$orderfields=array('0'=>'dn_users.id','1'=>'unique_code','2'=>'first_name','3'=>'last_name','4'=>'dn_users.created_at','6'=>'email','7'=>'contact_number','8'=>'active','9'=>'is_logged');
		//print_r($data['order'][0]); 
		$field='dn_users.id'; 
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
		$sql = 'SELECT distinct dn_users.id FROM dn_users ';
		$sql1 = 'SELECT  count(distinct dn_users.id) as totalcount FROM dn_users ';
		
		$sqlQuery = " LEFT JOIN dn_payments on dn_payments.user_id = dn_users.id ";
		$sqlQuery .= " LEFT JOIN role_user on role_user.user_id = dn_users.id ";
		$sqlQuery.= "WHERE 1=1 AND role_user.role_id='3' AND role_user.user_id = dn_users.id";
				
		if(!empty($startDate) &&  !empty($endDate))
		{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sqlQuery .=" AND dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
		}
		
		if(!empty($billingval1) &&  !empty($billingval2))
		{
			
			$sqlQuery .=" AND dn_payments.amount BETWEEN '$billingval1' AND '$billingval2'";
		
		}else if(!empty($billingval1) &&  empty($billingval2)){
			
			$sqlQuery .=" AND dn_payments.amount > '$billingval1'";
		
		}else if(empty($billingval1) &&  !empty($billingval2)){
			
			$sqlQuery .=" AND dn_payments.amount < '$billingval2'";
		
		}
		
		if($data['state']!='')
		{
			$state = $data['state'];
			$sqlQuery .=" AND  state= '$state'";
		}
		if($data['city']!='')
		{
			 $city = $data['city'];
			 $sqlQuery .=" AND  city= '$city'";
		}
		if($data['state']!='' && $data['city']!='')
		{
			$state = $data['state'];
			$city = $data['city'];
			$sqlQuery .="AND  state= '$state' AND  city= '$city'";
		}
		if(@$searchString!='')
		{	
			$search = "%$searchString%";
			$sqlQuery .=" AND  (unique_code LIKE '$search' or  first_name LIKE '$search' or full_name LIKE '$search' or last_name LIKE '$search' or email LIKE '$search' or contact_number LIKE '$search') ";
		}
		
		$sqlQuery .= " order by ".$field." ".$direction;
		$totalRecords = 0;
		$sqlcount = $sql1.$sqlQuery;
		//echo $sqlcount;
		$totalRecords=DB::select(DB::raw($sqlcount));
		//print_r($totalRecords);die;
		$sqlQuery .= " limit $offset, $limit";
		$sqldata=$sql.$sqlQuery; 
		//echo $sqldata;die;
		$usersIds=DB::select(DB::raw($sqldata));
		//print_r($usersIds);
		if(!empty($usersIds))
		{
		$usersList=array();
		foreach($usersIds as $value)
		{
			$usersList[]=$value->id;
		//print_R($usersList);
		}//print_R($usersList);
		}
		$users = array();
		if(!empty($usersList))
		{
			/* code for fetching data from dn_users table */
			 $users = DB::table('dn_users')
						->select(array('dn_users.*'))
						->whereIn('id',$usersList)
						->orderBy($field,$direction)//->tosql();
						->get();
						//print_r($users);
		//echo $users;
		}
		$Data="";
		//echo "<pre>"; print_r($users); die;
		foreach($users as $user)
		{
			//echo $user->active;
			$action='';
			$deleteUser='';
			$first_name =$user->first_name;
			$last_name =$user->last_name;
			$email=$user->email;
			$phone=$user->contact_number;
			$state=$user->state;
			$city=$user->city;
			$userId=$user->id;
			$user_active=$user->active;
			//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
			$lastrideData= DB::table('dn_rides')->where('passenger_id',$userId)->orderBy('created_at', 'desc')->first();
			$lastride=@$lastrideData->created_at;
			
			
			$view="<span class='label-success label '><a href='passenger-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
			//$view="<span class='label-success label '><a href='#'> View </a></span>";
			
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');
			
			if(empty($loggedInUserPermission)){

				if($user->active==1) {
				
					$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;";
										
				}else{
					
					$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Activate</a></span>&nbsp;";
				} 
				
				$deleteUser = "<span> <a class='deletePassenger btn btn-danger width-btn passenger_dlt' data-action= 'deletePassenger' data-userid=".$user->id." >Delete</a></span>";

			}elseif(!empty($loggedInUserPermission)){
				
				foreach($loggedInUserPermission as $userPermission){
					
					if($userPermission->module_slug=="passengers" && $userPermission->edit_permission==1){
						
						if($user->active==1) {
				
							$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;";
												
						}else{
							
							$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Activate</a></span>&nbsp;";
						} 
						
						$deleteUser = "<span> <a class='deletePassenger btn btn-danger width-btn passenger_dlt' data-action= 'deletePassenger' data-userid=".$user->id." >Delete</a></span>"; 
								
					}
				}	
			}	
			
			//Subadmin Permission Code End
			
			
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
			}if(empty($lastride))
			{
				$lastride="N/A";
			}
			
			if($user_active==1) {
				$active='Active';				
			}else{
				$active='Suspended';
			}
			
			$Data[]= "[". '"'.++$offset.'"' . ",".'"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"'.$lastride.'"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.$view.$deleteUser.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.$totalRecords[0]->totalcount.',
			  "recordsFiltered":'.$totalRecords[0]->totalcount.',
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

	/**
	  * @FUNCTION FOR EDIT PASSENGER
	  * @Author : 
	  *
	  **/
	 public function edit($id=null)
	  {
		 echo $id;die;
	  }
  
	 /**
	  * @FUNCTION FOR View PASSENGER AND HIS OTHER DETAILS
	  * @Author : 
	  * @return variable '$user' to display user , '$userCars' for all car details for that user.
	  **/
	public function show( Request $request, $id=null)
	{
		//$ride_data = 
		$id = convert_uudecode(base64_decode($id));
		
		//$ride_data['total_reported_issue']	= count(DB::table('dn_report_an_issuse')->where("user_id",$user_id)->get());

		$user=$this->getUserDetails($id); 
		//echo "<pre>"; print_R($user); die;
		/*
		if($user->gender==""){
			
			$gen = 'N/A';
			
		}else{
			
			$gen = $user->gender;
		}
		echo $gen; die;*/
		//get user personal details
		$userRoles=DB::table('role_user')->where('user_id',$id)->get(); 
		$driver=false;
		foreach($userRoles as $role)
		{
				if( $role->role_id==4 || $role->role_id==5 )
				{
					$driver=true;
				}
		}
		$userCars=DB::table('dn_user_cars')->where('user_id',$id)->where('is_delete',0)->get(); // get his car details
		$userFavPlaces=DB::table('dn_favorite_places')->where('user_id',$id)->get(); // get his car details
		//echo "<pre>"; print_r($userCars); die();

		$deziCredit = DB::table('dn_passenger_credits')->where('user_id',$id)->orderBy('id', 'desc')->first();	
		$lastRideDate = DB::table('dn_rides')->select(DB::raw('DATE_FORMAT(max(date(dn_rides.created_at)),"%d/%m/%Y %H:%i:%s") as maxDate'))->where('passenger_id',$id)->first();
		$paymentIdList= DB::table('dn_rides')->where('passenger_id',$id)->where('payment_status',1)->lists('id');
		$billCleared = DB::table('dn_payments')->select(DB::raw('SUM(amount) as billCleared'))->whereIN('ride_id',[implode(',',$paymentIdList)])->get();
		// print_r($billCleared);die;
		if(empty($billCleared[0]->billCleared))
		{$blCleared = 0 ;}else
		{
			$blCleared=$billCleared[0]->billCleared;
			
		}
		
		$pendingBillList= DB::table('dn_rides')->where('passenger_id',$id)->where('payment_status',0)->lists('id');
		$pendingBill = DB::table('dn_payments')->select(DB::raw('SUM(amount) as pendBill'))->whereIN('ride_id',[implode(',',$pendingBillList)])->get();
		
		if(empty($pendingBill[0]->pendBill))
		{$penBills = 0 ;}else
		{
			
			$penBills=$pendingBill[0]->pendBill;
			
		}
		
		if (!empty($deziCredit)) {
			$view_data['deziCredit'] = $deziCredit->credit_balance;
		} else {
			$view_data['deziCredit'] = 0.00;
		}

		$dob = $user->dob;
		
		if(empty($user->anniversary)){
		$user->anniversary="0000-00-00";
		}
		// print_R($user);die;
		if($user->active==1)
		{
			$user->active='Active';
		}else{$user->active='Suspended';}
		$age= $this->ageCalculator($dob);
		$_SESSION['countRide']=0;
		
		return $this->view('users.show',compact('user','driver','userCars','age','id', 'view_data', 'userFavPlaces','lastRideDate','blCleared','penBills','dob'));

	}

	public function passengerRideHistory(Request $request)
	{
		echo "string";
	}

	

/**
  * @FUNCTION FOR SUSPEND USERS
  * @Author : Vaibhav Bharti
  * @Params : $request is used to handle all Http request
  **/
 public function suspend(Request $request)
  {
	  /* initializing variables */
	$data = $request->all();
	$id = $data['id']; 
	$adminId = Auth::id();
	$actionType=$data['action'];
	
	if($actionType=='driver_suspend')
	{
		$suspend=DB::table('dn_users')
		->where('id', $id)
		->update(['active' => 0]);

		if($suspend){
			echo "suspendSuccess";
			DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id, 'status_type'=>'user_suspend','added_by'=>$adminId]);
		}else{
			echo "suspendFail";
		}
		
	}
	if($actionType=='passenger_Active')
	{
		$active=DB::table('dn_users')
		->where('id', $id)
		->update(['active' => 1]);
		if($active){
			echo "activeSuccess";
			DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'user_active','added_by'=>$adminId]);
		}else{
			echo "activeFail";
		}
		
	}exit;
 }

 /**
  * @FUNCTION FOR 
  * @Author : 
  *
  **/
	public function chargePromos() {
		//die 'hello';
		return $this->view('users.charges_promos');
	}

	
	/* ------------------------+------- */

	/**
	 * @FUNCTION FOR User deziCredit
	 * @Author : Harish Chauhan
	 *
	**/	
	public function userCredit(Request $request) {

		$data 		= $request->all();
		
		$user_id = $data['user_id'];
		$credit_type 	= 1; //1=DeziCredit By Admin, 2=referralCredit, 2=PromoCredit
		$credit_amount = $data['credit_amount'];
		$credit_by 	= Auth::id();
		$credit_txn_type 	= 'Cr'; //Cr = Credit, Dr = Debit

		$deziCredit = DB::table('dn_passenger_credits')->where('user_id',$user_id)->orderBy('id', 'desc')->first();		

		if ( !empty($deziCredit)) {

			$credit_balance = $deziCredit->credit_balance + $credit_amount;

			$insertCreditID = DB::table('dn_passenger_credits')
			->insert(
				['user_id' => $user_id, 
				'credit_type'=> $credit_type,
				'credit_by' => $credit_by,
				'credit_txn_type' => $credit_txn_type,
				'credit_amount'=>$credit_amount,
				'credit_balance'=>$credit_balance]
			);

		} else {

			$insertCreditID = DB::table('dn_passenger_credits')
			->insert(
				['user_id' => $user_id, 
				'credit_type'=> $credit_type,
				'credit_by' => $credit_by,
				'credit_txn_type' => $credit_txn_type,
				'credit_amount'=>$credit_amount,
				'credit_balance'=>$credit_amount]
			);
		}
		
		$data['azStatus']	= 'success';
		$data['azMessage']	= 'The DeziCredit is successfully added to user account.';
		
		DB::table('dn_users_changed_status_log')->insert(['entity_id' => $user_id, 'status_type'=>'dezi_credit_added','added_by'=>$credit_by]);
		
		echo json_encode($data);
		die();
	}

	public function pasangerAction(Request $request) {

		$adminId 	= Auth::id();
		$data 		= $request->all();

		$azId 		= $data['azId']; 
		$azAction 	= $data['azAction'];
		
		if($azAction=='suspend') {

			$suspend=DB::table('dn_users')
			->where('id', $azId)
			->update(['active' => 0]);
			if($suspend){ $data['azStatus'] = "success";

				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $azId, 'status_type'=>'user_suspend','added_by'=>$adminId]);
			
			} else { $data['azStatus'] = "error"; }
			
		}

		if($azAction=='activate') {

			$active=DB::table('dn_users')
			->where('id', $id)
			->update(['active' => 1]);

			if($active) { $data['azStatus'] = "success";

				DB::table('dn_users_changed_status_log')->insert(['entity_id' => $azId,'status_type'=>'user_active','added_by'=>$adminId]);
			
			} else { $data['azStatus'] = "error"; }
			
		}

		echo json_encode($data);
		die();
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

	/* ------------------------+------- */
	public function SuspendedPassenger()
	{
		$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')
						->paginate(config('admin.user.perpage'));
		$activeUsers = DB::table('role_user')
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')->where('active','1')->count();	
						
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
		//return $this->view('users.index', compact('users','citiesCount','states'));
		return $this->view('users.suspendedPassengerList', compact('users','citiesCount','states','activeUsers'));
	}
	
	public function SuspendedPassengerAjax(Request $request)
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
			//echo $data['state'];die;
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
			//$sql = 'SELECT id FROM dn_users WHERE 1=1';
			$sql1 = 'SELECT count(distinct dn_users.id) as totalrecords FROM dn_users ';
			$sql2 = 'SELECT distinct dn_users.id  FROM dn_users ';
			$sqlQuery ='';
			$sqlQuery = " LEFT JOIN role_user on role_user.user_id = dn_users.id ";
			$sqlQuery.= "WHERE 1=1 AND role_user.role_id='3' AND role_user.user_id = dn_users.id AND dn_users.active='0'";
			if(!empty($startDate) &&  !empty($endDate))
			{
				$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
				$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
				$sqlQuery .=" AND  dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
			}
			if($data['state']!='')
			{
				$state = $data['state'];
				$sqlQuery .=" AND  state= '$state'";
			}
			if($data['city']!='')
			{
				 $city = $data['city'];
				 $sqlQuery .=" AND  city= '$city'";
			}
			if($data['state']!='' && $data['city']!='')
			{
				$state = $data['state'];
				$city = $data['city'];
				$sqlQuery .="AND  state= '$state' AND  city= '$city'";
			}
			if(@$searchString!='')
			{	
				$search = "%$searchString%";
				$sqlQuery .=" AND  (unique_code LIKE '$search' or first_name LIKE '$search' or last_name LIKE '$search' or full_name LIKE '$search' or email LIKE '$search' or contact_number LIKE '$search') ";
			}
			
			$sqlQuery  .= " order by ".$field." ".$direction;
			
			$totalRecords = 0;
			$sqlcount = $sql1.$sqlQuery;
			$totalRecords=DB::select(DB::raw($sqlcount));
			$sqlQuery .= " limit $offset, $limit";
			$sqldata=$sql2.$sqlQuery; 
			$usersIds=DB::select(DB::raw($sqldata));
		
			if(!empty($usersIds))
			{
			$usersList=array();
			foreach($usersIds as $value)
			{
				$usersList[]=$value->id;
			
			}
			}
			$users = array();
			
			if(!empty($usersList))
			{
				/* code for fetching data from dn_users table */
				$users = DB::table('dn_users')
							->select(array('dn_users.*'))
							->whereIn('dn_users.id',$usersList)
							->orderBy($field,$direction)->get();
							//print_r($users);
				
			}
			$Data="";
			foreach($users as $user)
			{
				//echo $user->active;
				//print_r($user);exit;
				$first_name =$user->first_name;
				$last_name =$user->last_name;
				$email=$user->email;
				$phone=$user->contact_number;
				$state=$user->state;
				$city=$user->city;
				$lastride= DB::table('dn_rides')->where('passenger_id',$user->id)->orderBy('created_at', 'desc')->first();
				$lastride=@$lastride->created_at;
				 
				//$view="<span class='label-info label'>".link_to_route("passengerDetail","View")."</span>";
				
				//$action="<span class=''><a href='javascript:void(0);' class='btn btn-success width-btn driver_unrevoke' data-action= 'driver_unrevoke' data-userid=".$user->id.">Re-Allow</a></span>";
				
				
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
				if(empty($lastride))
				{
					$lastride="N/A";
				}
				if(empty($city))
				{
					$city="N/A";
				}
				$view="<span class='label-success label '><a href='passenger-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
				$action="".$view;
				

			$loggedInUserPermission = Session::get('userPermissions');
			
			if(empty($loggedInUserPermission)){
				// $view="<span class='label-success label '><a href='passenger-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
				if($user->active==1) {
				$active='Active';
				$action = "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
				// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
				}else{
				$active='Suspended';
				$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Activate</a></span>&nbsp;|&nbsp;".$view;
				}

				

			}elseif(!empty($loggedInUserPermission)){
			foreach($loggedInUserPermission as $userPermission){
				if($userPermission->module_slug=="passengers" && $userPermission->edit_permission==1){
					
					// $view="<span class='label-success label '><a href='passenger-detail/".base64_encode(convert_uuencode($user->id))."'> View </a></span>";
				if($user->active==1) {
				$active='Active';
				$action= "<span><a  href='javascript:void(0);' class='btn btn-primary width-btn driver_suspend' data-action= 'driver_suspend' data-userid=".$user->id.">Suspend</a> </span>&nbsp;|&nbsp;".$view;
				// $action= "<a href='javascript:void(0);' class='driver_suspend ' data-userid=".$user->id." > Suspend </a>";
				}else{
				$active='Suspended';
				$action= "<span> <a href='javascript:void(0);' class='btn btn-success width-btn passenger_Active' data-action= 'passenger_Active' data-userid=".$user->id." >Activate</a></span>&nbsp;|&nbsp;".$view;
				}

				}
			} 
			} 
				$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"'.$lastride.'"'.",". '"'.$email.'"'.",". '"'.$phone.'"'.",". '"'.$action.'"'."]";
			}
			if(!empty($Data)){
				$newData=implode(',',$Data);	
				//echo '<pre>';print_r($newData);die;
						return '{
				  "draw": '.$draw.',
				  "recordsTotal": '.$totalRecords[0]->totalrecords.',
				  "recordsFiltered":'.$totalRecords[0]->totalrecords.',
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

	public function DzCreditAjax(Request $request)
	{
		
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$Pid = $data['Pid'];
		$TrnTYp = @$data['trnTyp'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);
	    $startDate=$data['startDate'];
		$endDate=$data['endDate'];
		$orderfields=array('0'=>'dn_passenger_credits.id','1'=>'dn_passenger_credits.created_on','2'=>'dn_passenger_credits.credit_type','4'=>'dn_passenger_credits.credit_amount');
		//print_r($data['order'][0]);
		$field='dn_passenger_credits.id';
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
		
		$sql="Select * FROM dn_passenger_credits WHERE user_id=$Pid ";
		
		//AND dn_report_an_issuse.user_type='passenger'";
		if(!empty($startDate) &&  !empty($endDate))
		{
			
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_passenger_credits.created_on BETWEEN '$startDate' AND '$endDate'";
		}	
		if(@$TrnTYp && !empty($TrnTYp))
		{
			
			$sql .= " AND  dn_passenger_credits.credit_txn_type = '$TrnTYp'";
		}
		if(@$searchString!='')
		{		
		$search = "%$searchString%";
		$sql .=" AND  (dn_passenger_credits.created_on LIKE '$search' or dn_passenger_credits.credit_amount LIKE '$search')";
		}
		$sql .= " order by ".$field." ".$direction;
		$totalRideCount=DB::select(DB::raw($sql));
		$totalRideCount= count($totalRideCount);
		//echo $totalRideCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalDzCredit=DB::select(DB::raw($sql));
		
		$totalRecords = 0;
		
		$Data="";
		//echo "<pre>"; print_r($totalDzCredit);
		foreach($totalDzCredit as $dzcredit)
		{
			
			$timeStamp=$dzcredit->created_on;
			$transTyp=$dzcredit->credit_txn_type;
			$rideId="N/A";
			$mode="N/A";
			$credit_byId=$dzcredit->credit_by;
			$credit_amount=$dzcredit->credit_amount;
			$creditByName = DB::table('dn_users')->select(DB::raw('CONCAT(first_name," ",last_name) as fullNAme'))
							->where('id', @$credit_byId)
							->first();
	
			$creditByName =@$creditByName->fullNAme ?  $creditByName->fullNAme : 'N/A';
			
			if($transTyp=="Cr")
			{
				$transTyp="Credit";
			}elseif($transTyp=="Dr"){
				$credit_amount = $dzcredit->debit_amount;
				$transTyp="Debit";}
			//print_r($creditByName);
			$Data[]= "[". 
						'"'.++$offset.'"'.",".
						'"'.@$timeStamp .'"' . ",". 
						'"'.@$transTyp.'"' . ",". 
						'"'.@$rideId .'"'.",". 
						'"'.@$credit_amount.'"'.",". 
						'"' .@$mode.'"'.",". 
						'"'.@$creditByName.'"'."]";
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
	public function carFunction(REQUEST $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$Actions='N/A';
		$draw = $data['draw'];
		$offset = $data['start'];
		$Pid = $data['Pid'];
		$totalusersData =0;
		$searchString=$data['search']['value'];

		$orderfields=array('0'=>'id','1'=>'make','2'=>'model','4'=>'transmission');
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
	    $uCarDetails = DB::table('dn_user_cars')->select(DB::raw('id,make,model,transmission'))
							->where('user_id', @$Pid)
							->where('is_delete', 0)
							->take($limit)->offset($offset)->orderBy($field,$direction)
							->get();
		$totalusersData = DB::table('dn_user_cars')->select()->where('user_id', @$Pid)->where('is_delete', 0)
						  ->count();
		
		$licenseNo  = DB::table('dn_users_data')->select(DB::raw('license_number'))
							->where('user_id', @$Pid)
							->first();
		if(empty($licenseN->license_number))
			{
				$licenseNum ="N/A";
			}else{
				$licenseNum =$licenseN->license_number;
			}
		
	foreach($uCarDetails as $car)
		{
			//print_r($dzcredit);
			$make=$car->make;
			$model=$car->model;
			$transmission=$car->transmission;
			if(empty($make))
			{
				$make="N/A";
			}
			if(empty($model))
			{
				$model="N/A";
			}
			if(empty($transmission))
			{
				$transmission="N/A";
			}
			//print_r($car);die;
			$Edit ="<span class='label-success label '><a href='javascript:void(0)' id='".@$car->id."' class='editcarDtl' data-toggle='modal' data-target='#myModal'> Edit </a></span>";
			$Delete ="<span class='label-success label '><a href='javascript:void(0)' id='".@$car->id."' class='deleteCar'  > Delete </a></span>";
			
			
			
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');
			
			if(empty($loggedInUserPermission)){

				$Actions=$Edit .'  | ' .$Delete;

			}elseif(!empty($loggedInUserPermission)){
				
				foreach($loggedInUserPermission as $userPermission){
					
					if($userPermission->module_slug=="passengers" && $userPermission->edit_permission==1){
						
						$Actions=$Edit .'  | ' .$Delete;
								
					}
				}	
			}	
			$Data['data'][]=array(++$offset,@$make,@$model,@$licenseNum,@$transmission,@$Actions);
		}
	if(!empty($Data)){

			$Data['draw']=$draw;
			$Data['recordsTotal']=count($totalusersData);
			$Data['recordsFiltered']=count($totalusersData);
			return(json_encode($Data));
			} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	}
	
	public function FavplaceFunction(REQUEST $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$Pid = $data['Pid'];
		$totalusersData =0;
		$searchString=$data['search']['value'];

		$orderfields=array('0'=>'id','1'=>'created_at','2'=>'place_name','3'=>'address');
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
	    $FplacesDetails = DB::table('dn_favorite_places')->select(DB::raw('created_at,place_name,address'))
							->where('user_id', @$Pid)
							->take($limit)->offset($offset)->orderBy($field,$direction)
							->get();
		$totalusersData = DB::table('dn_favorite_places')->select()->where('user_id', @$Pid)
						  ->count();
		
		//print_r($FplacesDetails);exit;
		
		$Actions="jfhdg";
	foreach($FplacesDetails as $fplace)
		{
			
			$created_at=$fplace->created_at;
			$place_name=$fplace->place_name;
			$address=$fplace->address;
			if(empty($created_at))
			{
				$created_at="N/A";
			}
			if(empty($place_name))
			{
				$place_name="N/A";
			}
			if(empty($address))
			{
				$address="N/A";
			}
			//print_r($creditByName);
			$Data['data'][]=array(++$offset,@$created_at,@$place_name,@$address);
		}
	if(!empty($Data)){

			$Data['draw']=$draw;
			$Data['recordsTotal']=count($totalusersData);
			$Data['recordsFiltered']=count($totalusersData);
			return(json_encode($Data));
			} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	}
	//Passenger payment detail 
	public function AccountDtl(REQUEST $request)
	{
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$Pid = $data['Pid'];
		$totalusersData =0;
		$searchString=$data['search']['value'];

		$orderfields=array('0'=>'id','1'=>'created_at','2'=>'place_name','3'=>'address');
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
	    $FplacesDetails = DB::table('dn_payment_accounts')->select(DB::raw('account_type,card_type,expiration_date,masked_number,is_default,account_email'))
							->where('user_id', @$Pid)
							->where('is_delete','0')
							->take($limit)->offset($offset)->orderBy($field,$direction)
							->get();
		$totalusersData = DB::table('dn_payment_accounts')->select()->where('user_id', @$Pid)
						  ->count();
		
		
		
		$Actions="jfhdg";
	foreach($FplacesDetails as $fplace)
		{
			//print_r($dzcredit);
			$account_type=$fplace->account_type;
			$card_type=$fplace->card_type;
			$expiration_date=$fplace->expiration_date;
			$masked_number=$fplace->masked_number;
			$is_default=$fplace->is_default;
			$account_email=$fplace->account_email;
			if(empty($account_type))
			{
				$account_type="N/A";
			}
			if(empty($card_type))
			{
				$card_type="Paypal";
			}
			if(empty($expiration_date))
			{
				$expiration_date="N/A";
			}
			if(empty($masked_number) || !empty($account_email))
			{
				$masked_number=$account_email;
			}
			if(!empty($is_default))
			{
				$is_default="Yes";
			}else{$is_default="";}
			
			//print_r($creditByName);
			$Data[]= "[". 
						'"'.++$offset.'"'.",".
						'"'.@$account_type .'"' . ",". 
						'"'.@$card_type.'"' . ",".
						'"'.@$masked_number.'"' . ",".
						'"'.@$expiration_date.'"' . ",". 
						'"'.@$is_default.'"'."]";
		}
	if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalusersData).',
			  "recordsFiltered":'.($totalusersData).',
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

	public function editCarfunc(REQUEST $request)
	{
		$data=$request->all();
		if(@$data['carid'] && !empty($data['carid']))
		{
			$carData= DB::table('dn_user_cars')->select(DB::raw('id,make,model,transmission'))
							->where('id', @$data['carid'])
							->first(); 
			return json_encode($carData);
		}else{
			  $active=DB::table('dn_user_cars')
					->where('id', @$data['id'])
					->update(['make' => @$data['make'],'model'=>@$data['Model'],'transmission'=>@$data['Transmission']]);
			return 'success';
		}
	}
	
/**
  * @FUNCTION FOR DELETE CARS
  * @Author : Vaibhav Bharti
  * @Params : $request is used to handle all Http request
  **/
 public function carDelete(Request $request)
  {	
  
	$data = $request->all();
	$id = $data['id']; 
	$adminId = Auth::id();
	if(!empty($id))
	{
		$delete=DB::table('dn_user_cars')->where('id', $id) ->update(['is_delete' => 1]);	//deleting cars from table dn_user_cars
	}
	
	if($delete)
	{
		echo "deleted";
		DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'Car_Delete','added_by'=>$adminId]);
	}exit;
 }
 public function callLogs( Request $request ){
		
		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$draw = $data['draw'];
		$offset = $data['start'];
		$ride_id = $data['ride_id'];
		$searchString=$data['search']['value'];
		//$driver_detail_rideid= trim($data['driver_detail_rideid']);

		$orderfields=array('0'=>'id','1'=>'passenger_query','2'=>'description','3'=>'created_at');
	   
		
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
		$sql = "SELECT * FROM dn_ride_call_details WHERE ride_id = $ride_id ";
		//$sql="Select * from dn_rides where driver_id = 253";
		
		
		if(@$searchString!='')
		{	
			 $search = "%$searchString%";
			
			$sql .=" AND (id LIKE '$search' or passenger_query LIKE '$search' or description LIKE '$search') ";
			
		}
		$sql .= " order by ".$field." ".$direction;
		$totalCallCount=DB::select(DB::raw($sql));
		$totalCallCount= count($totalCallCount);
		//echo $totalCallCount; die;
		$sql .= " Limit ".$offset." , ".$limit;
		$totalCall=DB::select(DB::raw($sql));
		$totalRecords = 0;		
		$Data="";
		


		foreach($totalCall as $call)
		{
			//echo $user->active;
			$callId = $call->call_id;
			$admin=$call->admin;
			$passengerQuery=$call->passenger_query;
			$calldetails=$call->description;
			$timeStamp=$call->created_at;
			
					
			if(empty($passengerQuery))
			{
				$passengerQuery="N/A";
			}if(empty($calldetails))
			{
				$calldetails="N/A";
			}if(empty($timeStamp))
			{
				$timeStamp="N/A";
			}
			
			//die('asdfasd');
			$Data[]= "[". '"'.++$offset.'"'.","
					. '"'.$callId .'"'.","
					. '"'.$passengerQuery.'"' . ","
					. '"'.$calldetails .'"'.","
					. '"'.$admin .'"'.","
					. '"'.$timeStamp.'"'."]";
		}
		if(!empty($Data)){
			$newData=implode(',',$Data);	
			//echo '<pre>';print_r($newData);die;
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalCallCount).',
			  "recordsFiltered":'.($totalCallCount).',
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
 	public function adcalllog(Request $request){
 		$input = $request->input();
		$pquery=$input['pquery'];
		$Qdesc=$input['Qdesc'];
		$rideId=$input['rideId'];
		$Pid=$input['Pid'];
		$a = mt_rand(0,999); 
		$data = $request->session()->all();
		$admin = DB::table('role_user')->where('user_id', '=',$data['login_82e5d2c56bdd0811318f0cf078b78bfc'])
		  ->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
		  ->first();
		
		if($admin->role_id == 1){
			$adminFullName=$admin->first_name .' '.$admin->last_name;
		}else{
			$adminFullName='';
		}
		for ($i = 0; $i<3; $i++) 
		{
		    $a .= mt_rand(0,9);
		}
		$values = array('passenger_id' => $Pid,'ride_id' => $rideId,'admin'=>@$adminFullName,'call_id'=>$a,'passenger_query'=>$pquery,'description'=>$Qdesc);
		DB::table('dn_ride_call_details')->insert($values);
		return back();
		
 	}
	
}
