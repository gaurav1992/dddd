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
use Mail;
use Auth;
use Datatables;
use DateTime;
use Redirect;
use Session;

/**
  * @Class for Message  Management
  * 
  */
class ContactmessagesController extends BaseController
{

	
 			
 public function index()
 {		
    return $this->view('contact.index');
 }
 
/**
  * @Function for display messages with ajax
  * @Author : Vaibhav Bharti
  * @Params: '$request' for all Http request
  */
 public function indexAjax(Request $request)
 {
	 /*Initializing all variables */
	$data = $request->all();
	$limit = 10; //records per page
	$draw =   $data['draw'];
	$offset = $data['start'];
	$delBtn='';
	$status='';
	
	$orderfields=array(
					'1'=>'dn_users.unique_code',
					'2'=>'dn_users.first_name',
					'3'=>'dn_users.email',
					'4'=>'dn_contact_messages.source',
					'5'=>'subject',
					'6'=>'dn_contact_messages.status'
	); // array for orderby 
	
	$field='dn_contact_messages.id'; //field for orderby
	$direction='ASC'; //direction  for orderby
	
	/* code for order by data of message*/
	if(!empty($data['order'][0])){
	foreach($orderfields as $key=>$orderfield){
	if($key==$data['order'][0]['column'] )
	{
		$field=$orderfield;
		$direction=$data['order'][0]['dir'];
	}}}
    $i = 1;
	
	/* code for searching of  messages*/
	
	$searchString=$data['search']['value'];
	$sql = "SELECT dn_contact_messages.id FROM `dn_contact_messages`  left  join  `dn_users` on dn_contact_messages.user_id = dn_users.id WHERE dn_contact_messages.status 	In('0','1','3') And  dn_contact_messages.message_type='0'";
	if(@$searchString!='')
	{	
		$search = "%$searchString%";
		if($searchString=='new' or $searchString=='New' or $searchString=='NEW')
		{
			$search=0;
		}elseif($searchString=='old' or $searchString=='Old' or $searchString=='OLD')
		{
			$search=3;
		}
		elseif($searchString=='archive' or $searchString=='Archive' or $searchString=='ARCHIVE')
		{
			$search=1;
		}
		$sql .=" AND  (dn_users.unique_code LIKE '$search' or dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_users.email LIKE '$search' or dn_users.full_name LIKE '$search' or dn_contact_messages.status LIKE '$search') ";
	}
	$sql .= " order by '$field' '$direction'";
	$messagesIds = DB::select(DB::raw($sql));
	
	if(!empty($messagesIds))
	{
		$messageList=array();
		foreach($messagesIds as $value)
		{
			$messageList[]=$value->id;
		}
	}
	
	$messageData = array();
	$totalRecords = 0;
	if(!empty($messageList))
	{
		/* code for fetching data from dn_users table */
		$messageData = DB::table('dn_contact_messages')
					  ->select('dn_contact_messages.*','dn_users.*',DB::raw('dn_contact_messages.id as message_id'))
					  ->leftJoin('dn_users' , 'dn_contact_messages.user_id' , '=' , 'dn_users.id' )
					  ->whereIn('status' , array('0','1','3'))
					  ->whereIn('dn_contact_messages.id',$messageList)
					  ->take($limit)->offset($offset)->orderBy($field,$direction)->get();
				 //print_r($messageData);exit;  
		$totalRecords = DB::table('dn_contact_messages')
					  ->select('dn_contact_messages.*','dn_users.*',DB::raw('dn_contact_messages.id as message_id'))
					  ->leftJoin('dn_users' , 'dn_contact_messages.user_id' , '=' , 'dn_users.id' )
					  ->whereIn('status' , array(0,1,3))
					  ->whereIn('dn_contact_messages.id',$messageList)
					  ->paginate(config('admin.user.perpage'));
		//print_r($totalRecords);exit;
	}
	//echo "<pre>";print_r($messageData);exit;
	foreach($messageData as $user){

		 $first_name = $user->first_name . " ".$user->last_name;
		 $email = $user->email;
		 $unique_id=$user->unique_code;
		 $phone=$user->contact_number;
		 $source=$user->source;
		// $message=$user->message;
		
		if(empty($first_name))
		{
				$first_name="N/A";
		}
	
		if(empty($email))
		{
				$email="N/A";
		}
		
		if(empty($phone))
		{
				$phone="N/A";
		}
		
		if(empty($message))
		{
				$subject="N/A";
		}
		
		if(empty($source))
		{
				$source="Website";
		}
		
		$view= "<span><a href='messageView/".base64_encode(convert_uuencode($user->message_id))."' class='btn btn-primary width-btn'> View </a></span>";
		
		//Subadmin Permission Code Start
		$loggedInUserPermission = Session::get('userPermissions');

		if(empty($loggedInUserPermission)){

			 if($user->status == 0){
				  $status = "<span class='label label-danger'>New</span>";
				  $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;";
			  } else if($user->status == 1) {
				  $status = "<span class='label label-success'>Archieve</span>";
				  $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;";
			
			  } else if($user->status == 3) {
				  $status = "<span class='label label-default'>Old</span>";
				  $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;";
				
			  } 

		}elseif(!empty($loggedInUserPermission)){
			
			foreach($loggedInUserPermission as $userPermission){
				
				if($userPermission->module_slug=="contact_messages" && $userPermission->edit_permission==1){
							
					 if($user->status == 0){
						  $status = "<span class='label label-danger'>New</span>";
						  $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;";
					  } else if($user->status == 1) {
						  $status = "<span class='label label-success'>Archieve</span>";
						  $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;";
					
					  } else if($user->status == 3) {
						  $status = "<span class='label label-default'>Old</span>";
						  $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;";
						
					  } 		
				}
			}	
		}	
		//Subadmin Permission Code End

			 
		  $Data[]= "[".'"'.++$offset. '"'.",
		  ". '"'.$unique_id.'"'.", 
		  ". '"'.$first_name.'"'.",
		  ". '"'.$email.'"' .",
		  ". '"'.$source.'"'.",
		  ".'"'.$status.'"'.",
		  ".'"'.$delBtn.$view.'"'."]";
		  $i++;
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
		  exit;
   }

/**
  * @Function for delete messages with ajax
  * @Author : Vaibhav Bharti
  * @Params: '$request' for all Http request
  */
  public function delete_msg(Request $request)
   {
	$data = $request->all();
	$message_id = $data['id']; 
	$adminId = Auth::id();
	$suspend=DB::table('dn_contact_messages')->where('id', $message_id)->update(['status' =>'2']);     
	if($suspend){
	echo 'deleteSuccess';
	DB::table('dn_users_changed_status_log')->insert(['entity_id' => $message_id,'status_type'=>'message_Delete','added_by'=>$adminId]);}
	exit;
   } // end of delete_msg function

/**
  * @Function for action of message 
  * @Author : Vaibhav Bharti
  * @Params: '$request' for all Http request, '$id' is the id of message to delete or archive
  */
  public function messageAction($action=null,$id=null,Request $request)
   {
	$id = convert_uudecode(base64_decode($id));
	$adminId = Auth::id(); //id of the user (admin or subadmin) currently logged in
	/*  code to check Id exist or not in the Database */
	if(isset($id)){
	$idCheck = DB::table('dn_contact_messages')
			  ->where('id',$id)
			  ->first();
		if(empty($idCheck))
		{
			 Throw new \Exception('Oops!'.$id.' Not Found in database',1001);
		}
	}
    /* code to delete the message  */	
	if(isset($action) and isset($id) and $action=="delete")
	{
		//echo "in";
		$delete=DB::table('dn_contact_messages')->where('id', $id)->update(['status' =>'2']);
		if($delete)
		{
		 DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'message_Delete','added_by'=>$adminId]);	
		 Session::flash('message', 'Message deleted!'); 
		 return Redirect::to('/admin/messagelist');
		}
		else{
			Session::flash('message', 'Message is already deleted!'); 
		    return Redirect::to('/admin/messagelist');
		}
	}
	 /* code to archive the message  */	
	if(isset($action) and isset($id) and $action=="archive")
	{
		$archive=DB::table('dn_contact_messages')->where('id', $id)->update(['status' =>'1']);
		if($archive)
		{
		 DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'message_Archive','added_by'=>$adminId]);
		 Session::flash('message', 'Message archived!'); 
		 return Redirect::to('/admin/messagelist');
		}
		else{
			Session::flash('message', 'Message is already archived!'); 
		 return Redirect::to('/admin/messagelist');
		}
	}

	/* code to delete the message  unregistered user */	
	if(isset($action) and isset($id) and $action=="nrdelete")
	{
		//echo "in";
		$delete=DB::table('dn_contact_messages')->where('id', $id)->update(['status' =>'2']);
		if($delete)
		{
		 DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'message_Delete','added_by'=>$adminId]);	
		 Session::flash('message', 'Message deleted!'); 
		 return Redirect::to('/admin/nrmessagelist');
		}
		else{
			Session::flash('message', 'Message is already deleted!'); 
		    return Redirect::to('/admin/nrmessagelist');
		}
	}

	if(isset($action) and isset($id) and $action=="nrarchive")
	{
		$archive=DB::table('dn_contact_messages')->where('id', $id)->update(['status' =>'1']);
		if($archive)
		{
		 DB::table('dn_users_changed_status_log')->insert(['entity_id' => $id,'status_type'=>'message_Archive','added_by'=>$adminId]);
		 Session::flash('message', 'Message archived!'); 
		 return Redirect::to('/admin/nrmessagelist');
		}
		else{
			Session::flash('message', 'Message is already archived!'); 
		 return Redirect::to('/admin/nrmessagelist');
		}
	}

   } // end of messageAction function
   
/**
  * @Function for display messages with ajax
  * @Author: Vaibhav Bharti
  * @Params: '$request' for all Http request
  */
  public function view_msg($id=null)
  {
	$id = convert_uudecode(base64_decode($id));
	//die($id);
	/*  code to check Id exist or not in the Database */
	if(isset($id)){
	$idCheck = DB::table('dn_contact_messages')
				->where('id', $id)
				->first();
	if(empty($idCheck))
	{
	 Throw new \Exception('Oops!'.$id.' Not Found in database',1001); // throw exception if id doesnot exist in DB
	}			
	}
	/* code to find message with id  */
	$user_message_data = DB::table('dn_contact_messages')
				->select('dn_contact_messages.*','dn_users.*',DB::raw('dn_contact_messages.id as message_id,dn_contact_messages.created_at as message_date'))
				->leftJoin('dn_users' , 'dn_contact_messages.user_id' , '=' , 'dn_users.id' )
				->where('dn_contact_messages.id',$id)
				->first();

	if($user_message_data->status=="3"){
		$user_message_data->status="<span class='label label-default'>Old</span>";

	}elseif($user_message_data->status=="1"){
		$user_message_data->status="<span class='label label-success'>Archive</span>";	
	}elseif($user_message_data->status=="0"){
			
		$user_message_data->status="<span class='label label-danger'>New</span>";
		$where = array(
	        'id' => $id
	        );

 		$update_msg_status=DB::table('dn_contact_messages')
		->where( $where )
		->update(['status' => '3']);
	}

	return $this->View('contact.view_message',compact('user_message_data'));

  }// end of view_msg function

//Gaurav edited for non-registered user message

 public function NonRmsg()
 {		
    return $this->view('contact.nrindex');
 }
 

 public function NonRmsgAjax(Request $request)
 {
	 /*Initializing all variables */
	$data = $request->all();
	$limit = 10; //records per page
	$delBtn='';
	$draw =   $data['draw'];
	$offset = $data['start'];
	$orderfields=array('1'=>'first_name','2'=>'email','3'=>'message','4'=>'status'); 
	//array for orderby 
	$field='dn_contact_messages.id'; //field for orderby
	$direction='ASC'; //direction  for orderby
	
	/* code for order by data of message*/
	if(!empty($data['order'][0])){
	foreach($orderfields as $key=>$orderfield){
	if($key==$data['order'][0]['column'] )
	{
		$field=$orderfield;
		$direction=$data['order'][0]['dir'];
	}}}
    $i = 1;
	
	/* code for searching of  messages*/
	
	$searchString=$data['search']['value'];
	
	$sql = "SELECT * FROM `dn_contact_messages` WHERE  dn_contact_messages.message_type='1'";
	
	if(@$searchString!='')
	{	
		$search = "%$searchString%";
		$sql .=" AND  (dn_contact_messages.first_name LIKE '$search' or dn_contact_messages.last_name LIKE '$search' or dn_contact_messages.email LIKE '$search' or dn_contact_messages.message LIKE '$search')";
	}
	//$sql .= " order by '$field' '$direction'";
	$messagesIds = DB::select(DB::raw($sql));
	
	if(!empty($messagesIds))
	{
		$messageList=array();
		foreach($messagesIds as $value)
		{
			$messageList[]=$value->id;
		}
	}
	
	$messageData = array();
	$totalRecords = 0;
	if(!empty($messageList))
	{
		/* code for fetching data from dn_users table */
		$messageData = DB::table('dn_contact_messages')
					  ->select('dn_contact_messages.*',DB::raw('dn_contact_messages.id as message_id'))
					  ->leftJoin('dn_users' , 'dn_contact_messages.user_id' , '=' , 'dn_users.id' )
					  ->whereIn('status' , array('0','1','3'))
					  ->whereIn('dn_contact_messages.id',$messageList)
					  ->take($limit)->offset($offset)->orderBy($field,$direction)->get();
			
		$totalRecords = DB::table('dn_contact_messages')
					  ->select('dn_contact_messages.*','dn_users.*',DB::raw('dn_contact_messages.id as message_id'))
					  ->leftJoin('dn_users' , 'dn_contact_messages.user_id' , '=' , 'dn_users.id' )
					  ->whereIn('status' , array('0','1','3'))
					  ->whereIn('dn_contact_messages.id',$messageList)
					  ->paginate(config('admin.user.perpage'));
		
	}
	
	foreach($messageData as $user){

	
		$email=$user->email;
		$message=$user->message;
	    // $unique_id=$user->unique_code;
		//$phone=$user->contact_number;
		$first_name=$user->first_name." ".$user->last_name;
			
		if(empty($email))
		{
			$email="N/A";
		}
		
		if(empty($first_name))
			{
				$first_name="N/A";
			}
		 $view= "<span><a href='nrmessageView/".base64_encode(convert_uuencode($user->message_id))."' class='btn btn-primary width-btn'> View </a></span>";
		
			  if($user->status == 0){
				  $status = "New";
				 
			  } else if($user->status == 1) {
				  $status = "Archieve";
				 
			  } else if($user->status == 3) {
				  $status = "Old";
				 
			  }
							  
				//Subadmin Permission Code Start
				$loggedInUserPermission = Session::get('userPermissions');

				if(empty($loggedInUserPermission)){
					 $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;"; 
				}elseif(!empty($loggedInUserPermission)){
					
					foreach($loggedInUserPermission as $userPermission){
						
						if($userPermission->module_slug=="contact_messages" && $userPermission->edit_permission==1){
									
							 $delBtn = "<span><a id = 'hiddenUser' href = 'javascript:void(0);' class='btn btn-primary width-btn' data-userid=".$user->message_id.">Delete</a></span>&nbsp;|&nbsp;"; 		
						}
					}	
				}	
				//Subadmin Permission Code End

			 
		  $Data[]= "[".'"'.++$offset. '"'.",
		 
		". '"'.$first_name.'"'.",
		  ". '"'.$email.'"'.",
		  ". '"'.$message.'"'.",
		  ".'"'.$status.'"'.",
		  ".'"'.$delBtn.$view.'"'."]";
		  $i++;
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
		  exit;
   }


	public function generalIssuesList()
	{	
		return $this->view('contact.generalissueslist');
	}
	
	public function generalIssuesAjax(Request $request)
	{
		
 		/* initializing the variables */
		$data = $request->all();
		$limit = 10;
		$action ='';
		$draw = $data['draw'];
					
		$offset = $data['start'];
		$searchString=$data['search']['value'];
				
	    $startDate=isset($data['startDate'])?$data['startDate']:'';
		$endDate=isset($data['endDate'])?$data['endDate']:'';
		
		$orderfields=array('1'=>'dn_report_an_issuse_genral.user_id','2'=>'dn_users.full_name','3'=>'dn_report_an_issuse_genral.category','4'=>'dn_report_an_issuse_genral.status','5'=>'dn_report_an_issuse_genral.created_at');
		
		$field='dn_report_an_issuse_genral.id';
		$direction='DESC';
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

		$sql="Select dn_report_an_issuse_genral.id as issue_id,dn_report_an_issuse_genral.message,dn_report_an_issuse_genral.status,dn_report_an_issuse_genral.created_at,dn_users.first_name,dn_users.last_name,dn_users.full_name as passenger_name,dn_report_an_issuse_genral.user_id as passenger_id, dn_users.id as user_id, dn_cancellation_category.category,dn_cancellation_subcategory.subcategory from dn_report_an_issuse_genral ";
		$sql.="LEFT join dn_users on dn_report_an_issuse_genral.user_id = dn_users.id ";
		$sql.="LEFT join dn_cancellation_category on dn_report_an_issuse_genral.category = dn_cancellation_category.id ";
		$sql.="LEFT join dn_cancellation_subcategory on dn_report_an_issuse_genral.sub_category = dn_cancellation_subcategory.id WHERE 1=1 ";
				
		if(!empty($startDate) &&  !empty($endDate))
		{
			
			
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$sql .=" AND  dn_report_an_issuse_genral.created_at BETWEEN '$startDate' AND '$endDate'";
		}	
		
		if(@$searchString!='')
		{	
			
			$search = "%$searchString%";
			$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_users.full_name  LIKE '$search' or dn_report_an_issuse_genral.id LIKE '$search' or dn_report_an_issuse_genral.message LIKE '$search') ";
		}
		
		$sql .= " order by ".$field." ".$direction;
		
		$totalRideCount=DB::select(DB::raw($sql));
		
		$totalRideCount= count($totalRideCount);
		
		$sql .= " Limit ".$offset." , ".$limit;
		
		$totalIssue=DB::select(DB::raw($sql));
		
		$totalRecords = 0;
		
		$Data="";
		//echo $sql; die;
				
		
		foreach($totalIssue as $issue)
		{
		
			$issue_id=$issue->issue_id;
			$status=$issue->status;
			$passenger_id=$issue->passenger_id;
			$user_id=$issue->user_id;
			$passenger_name =$issue->first_name." ".$issue->last_name;
			$category=$issue->category;
			$subCategory=$issue->subcategory;
			$message=$issue->message;
			$timeStamp=date("m/d/Y",strtotime($issue->created_at));
						
			
			/* With Demo Ids */
			
			//Subadmin Permission Code Start
			$loggedInUserPermission = Session::get('userPermissions');

			if(empty($loggedInUserPermission)){

			$action = "<Select name='changeStatus' data-status='".@$issue->status."' data-id='".@$issue_id."' class='btn btn-primary btn-xs generalIssueStatusChange'><option value=''>Change Status</option><option value='completed'>Completed</option><option value='in_progress'>In Progress</option><option value='open'>Open</option></Select>";

			}elseif(!empty($loggedInUserPermission)){
				
				foreach($loggedInUserPermission as $userPermission){
					
					if($userPermission->module_slug=="contact_messages" && $userPermission->edit_permission==1){
								
						$action = "<button type='button' data-status='".@$issue->status."' data-id='".@$issue_id."' class='btn btn-primary btn-xs generalIssueStatusChange'>Change Status</button>";		
					}
				}	
			}	
			//Subadmin Permission Code End

			
			$v_rply = "<a style='margin-left:5px' href='view_genral_msg/".base64_encode(convert_uuencode($issue_id))."'><button type='button' class='btn btn-success btn-xs'>View</button></a>";
			/* /With Demo Ids */		
			
			if(empty($passenger_id)){continue;}
			if(empty($passenger_id))
			{
				$passenger_id="N/A";
			}else{
				$passenger_id = "<a href='passenger-detail/".base64_encode(convert_uuencode($user_id))."'>".$issue->passenger_id."</a>";
			}
			
			if(empty($status))
			{
				$status="N/A";
			}elseif($status=='in_progress'){$status="In Progress";}
			
			if(empty(trim($passenger_name)))
			{
				$passenger_name="N/A";
			}else{
				
				$passenger_name = "<a href='passenger-detail/".base64_encode(convert_uuencode($user_id))."'>".$issue->first_name." ".$issue->last_name."</a>";
			}
			if(empty($category))
			{
				$category="N/A";
			}
			if(empty($subCategory))
			{
				$subCategory="N/A";
			}
			if(empty($message))
			{
				$message="N/A";
			}
			if(empty($timeStamp))
			{
				$timeStamp="N/A";
			}
			
		
			$Data[]= "[". '"'.++$offset.'"'.",".'"'.$passenger_id .'"' . ",". '"'.$passenger_name.'"' . ",". '"' .$category ."=>".$subCategory .'"'.",". '"'.ucwords($status).'"'.",". '"'.$timeStamp.'"'.",". '"'.$action.$v_rply.'"'."]";
		}

		if(!empty($Data)){
			$newData=implode(',',$Data);	
		
					return '{
			  "draw": '.$draw.',
			  "recordsTotal": '.($totalRideCount).',
			  "recordsFiltered":'.($totalRideCount).',
			  "data": ['.$newData.']
				}';
		} else {
			return '{
			  "draw": '.$draw.',
			  "recordsTotal": 0,
			  "recordsFiltered":0,
			  "data": []
			}';
		}
	
	}

	public function generalIssueStatusChange(Request $request)
	{
		 $data=$request->all();
		 $status=$data['status'];
		  $now = new DateTime();
		 //if($status=='pending'){$status='addressed';}else{$status='pending';}
		 $id=$data['id'];
		 $updates = DB::table('dn_report_an_issuse_genral')
            ->where('id', $id)
            ->update(['status' => $status,'updated_at'=>$now]);
		 die;
		 
	}
	
	

 	public function view_genral_msg($id=null){
 		//$orgid = $id;
		$id = convert_uudecode(base64_decode($id));
		$orgid = $id;
		/*  code to check Id exist or not in the Database */
		if(isset($id)){
			
			$idCheck = DB::table('dn_report_an_issuse_genral')->where('id', $id)->first();
		
			if(empty($idCheck))
			{
				Throw new \Exception('Oops!'.$id.' Not Found in database',1001); // throw exception if id doesnot exist in DB
			}			
		}
		
		/* code to find message with id  */
		$general_message_data = DB::table('dn_report_an_issuse_genral')
			->select('dn_report_an_issuse_genral.*','dn_users.*',DB::raw('dn_report_an_issuse_genral.id as message_id,dn_report_an_issuse_genral.created_at as message_date'))
			->leftJoin('dn_users' , 'dn_report_an_issuse_genral.user_id' , '=' , 'dn_users.id' )
			->where('dn_report_an_issuse_genral.id',$id)
			->first();
							
			
		return $this->View('contact.generalmessageview',compact('general_message_data','orgid'));
		
 	}

 	public function view_general_message(Request $request,$id=null)
	{
		$input = $request->all();
		//print_r($input); die;
		$orgid = $id;
		$finalID =base64_encode(convert_uuencode($id));		


		$submitBtn = isset($input['submit'])?$input['submit']:'';
		$bodyMsg = isset($input['body_message'])?$input['body_message']:'';	

		if($submitBtn !=""){

			$general_message_data = DB::table('dn_report_an_issuse_genral')
			->select('dn_report_an_issuse_genral.*','dn_users.*',DB::raw('dn_report_an_issuse_genral.id as message_id,dn_report_an_issuse_genral.created_at as message_date'))
			->leftJoin('dn_users' , 'dn_report_an_issuse_genral.user_id' , '=' , 'dn_users.id' )
			->where('dn_report_an_issuse_genral.id',$id)
			->first();	

			if($bodyMsg !=''){

				$full_name = $general_message_data->full_name;
				$email = $general_message_data->email;
				$subject = "Dezinow general issue reply";
				$title = "General issue";

				if(empty($full_name)){
					$full_name = "Guest";
				}

				if(empty($email)){
					$email = "noreply@dezinow.com";
				}

				if(empty($bodyMsg)){
					$bodyMsg = "N/A";
				}

				\Mail::send('app.mails.general_issue_reply', ['full_name' =>$full_name,'title' =>$title, 'bodyMessage' => $bodyMsg], function($m) use ($email, $full_name, $subject)
					{
						$m->from('dezinow@example.com', 'DeziNow');
						$m->to($email, $full_name)->subject($subject);
					}
				);	

				if (count(Mail::failures()) > 0) {
					Session::flash('message', "Error: Something went wrong."); 
					Session::flash('alert-class', 'alert-danger');
				}else{
					Session::flash('message', "Success: Message has been sent successfully!"); 
					Session::flash('alert-class', 'alert-success');
				}
			}else{
				Session::flash('message', "Error: Kindly provide the message for reply."); 
				Session::flash('alert-class', 'alert-danger');
			}	
		}
		return Redirect::to('/admin/view_genral_msg/'.$finalID);
		//return $this->View('contact.generalmessageview',compact('general_message_data','orgid'));	
			
	}

    public function nrview_msg($id=null)
  {
	$id = convert_uudecode(base64_decode($id));
	/*  code to check Id exist or not in the Database */
	if(isset($id)){
	$idCheck = DB::table('dn_contact_messages')
				->where('id', $id)
				->first();
	if(empty($idCheck))
	{
	 Throw new \Exception('Oops!'.$id.' Not Found in database',1001); // throw exception if id doesnot exist in DB
	}			
	}
	/* code to find message with id  */
	$user_message_data = DB::table('dn_contact_messages')
				->select('dn_contact_messages.*','dn_users.*',DB::raw('dn_contact_messages.id as message_id,dn_contact_messages.created_at as message_date'))
				->leftJoin('dn_users' , 'dn_contact_messages.user_id' , '=' , 'dn_users.id' )
				->where('dn_contact_messages.id',$id)
				->first();

	if($user_message_data->status=="0"){
		
		$user_message_data->status="New";
		
		DB::table('dn_contact_messages')
				->where('dn_contact_messages.id',$id)
				->update(["dn_contact_messages.status"=>'3']);
		
		//$user_message_data->status="Old";
			
	
	}elseif($user_message_data->status=="1"){
	
		$user_message_data->status="Archive";	
	
	}elseif($user_message_data->status=="3"){
	
		$user_message_data->status="Old";	
	}	
	
	return $this->View('contact.nrview_message',compact('user_message_data'));

  }


}
// end of Class :) 
