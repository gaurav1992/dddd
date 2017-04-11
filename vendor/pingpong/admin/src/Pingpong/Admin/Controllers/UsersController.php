<?php namespace Pingpong\Admin\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Pingpong\Admin\Repositories\Users\UserRepository;
use Pingpong\Admin\Validation\User\Create;
use Pingpong\Admin\Validation\User\Update;
use Illuminate\Http\Request;
use DB;
use Datatables;
use App\ExchangeStudent;
use App\University;
use App\City;
use App\Country;
use App\UserType;
use Auth;

class UsersController extends BaseController
{

    /**
     * @var \User
     */
	 private $exchangeStudent;
    protected $users;

    /**
     * @param \User $users
     */
    public function __construct(UserRepository $repository,ExchangeStudent $exchangeStudent)
    {
        $this->repository = $repository;
		$this->exchangeStudent = $exchangeStudent;
		
		
    }

    public function testuser(){
    	echo 'sdfs';
    	die;
    }

    /**
     * Redirect not found.
     *
     * @return Response
     */
    protected function redirectNotFound()
    {
        return $this->redirect('users.index');
    }

    /**
     * Display a listing of users
     *
     * @return Response
     */
    public function index(Request $request)
    {
        /*$users = $this->repository->allOrSearch(Input::get('q'));
        $no = $users->firstItem();*/
		 
		$data = $request->all();
	
		/* } */
		$users = DB::table('role_user')
						->select(array('dn_users.*'))

						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')	
							
						->where('role_id','3')
						->paginate(config('admin.user.perpage'));
		$no = $users->firstItem();
		// *********  Code to find least and most city's passenger ******** //
		$citys = DB::table('dn_users')
					->select(array('city', DB::raw('COUNT(city) as no_of_users')))
                    ->groupBy('city')
					->where('city','!=','')    
                    ->get();
					
		
		$n=count($citys)-1;
		$cities=array('least'=>$citys[0],'most'=>$citys[$n]); //echo '<pre>'; print_r($cities);die;
		//echo '<pre>';print_r($users);die; 
        return $this->view('users.index', compact('no','cities'));
    }
	
	public function ajaxIndex()
	{
		//echo '<pre>';
		
		$limit = 10;
		$draw = $_REQUEST['draw'];
		$offset = $_REQUEST['start'];
		//$offset = ($start-1)*$limit;
		//print_r($_REQUEST);
		//$offset = 
			$searchString= $trim($_REQUEST['search']['value']);
			$startDate=$_REQUEST['startDate'];
			$endDate=$_REQUEST['endDate'];
			//print_r($_REQUEST['city']);
			//print_r($_REQUEST['state']);exit;
		if(!empty($startDate) &&  !empty($endDate))
			{
			$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
			$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
			$usersList = DB::table('dn_users')
							->whereBetween('created_at', [$startDate, $endDate])
							->lists('id');
			//print_r($usersList);exit;
			}
		if($_REQUEST['state']!='')
			{
			 $search = $_REQUEST['state'];
			 $usersList = DB::table('dn_users')
				->Where('state', $search)
				->lists('id');
				//print_r( $usersList);
			}
		if($_REQUEST['city']!='')
			{
			 $search = $_REQUEST['city'];
			 $usersList = DB::table('dn_users')
					->Where('city', $search)
					->lists('id');
					//print_r( $usersList);
			}
		
		if($_REQUEST['state']!='')
			{
			 $search = $_REQUEST['state'];
			 $usersList = DB::table('dn_users')
					->Where('state', $search)
					->lists('id');
					//print_r( $usersList);
			}
		if(@$searchString!=''){
			//$search = $data['search'];	
			 $search = "%$searchString%";
			 //echo $search;
			 $usersList = DB::table('dn_users')
						->orWhere('first_name', 'like', $search)
						->orWhere('last_name', 'like', $search)
						->orWhere('email', 'like', $search)
						->orWhere('unique_code', 'like', $search)
						->lists('id');
			}
			//print_r($usersList);die;
		if(!empty($usersList))
			{
				$users = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')
						->whereIn('role_user.user_id',$usersList)
						->take($limit)->offset($offset)->get();
						//print_r($users);
				$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')
						->where('role_id','3')
						->whereIn('role_user.user_id',$usersList)
						->paginate(config('admin.user.perpage'));
			}else{
				$users = 	DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')
						->take($limit)->offset($offset)->get();
				$totalRecords = DB::table('role_user')
						->select(array('dn_users.*'))
						->join('dn_users', 'role_user.user_id', '=', 'dn_users.id')		
						->where('role_id','3')->paginate(config('admin.user.perpage'));				
		    }
		//print_r(count($totalRecords));
		// $dataArray['data'] = $users['data'];
		// $dataArray['draw'] = $users['current_page'];
		// $dataArray['recordsTotal'] = $users['total'];
		// $dataArray['recordsFiltered'] = $users['total'];
		//return json_encode($dataArray,true);
		//echo '<pre>';print_r(json_encode($users,true));die;
		$Data="";
			foreach($users as $user)
			{
				//echo $user->active;
				$action= "<span> <a href='javascript:void(0);' class='driver_suspend' data-userid=".$user->id." > Suspend </a></span>";
				if($user->active==1) {
					$active='Active';
				}else {
					$active='Suspended';
				} 
					
				if($user->is_logged==1) {
					$is_logged='Yes';
				}else{
					$is_logged='No';
				} 
					$Data[]= "[". '"'.$user->unique_code .'"' . ",". '"'.$user->first_name .'"'.",". '"'.$user->last_name.'"' .",". '"'.date('m/d/Y', strtotime($user->created_at)).'"'.",". '"N/A"'.",". '"'.$user->email.'"'.",". '"'.$user->contact_number.'"'.",". '"'.$user->state.'"'.",". '"'.$user->city.'"'.",". '"'.$active.'"'.",". '"'.$is_logged.'"'.",". '"'.$action.'"'."]";
			}
					//$Data=$Data.','.$Data;
				$newData=implode(',',$Data);	
				//echo '<pre>';print_r($newData);die;
							return '{
					  "draw": '.$draw.',
					  "recordsTotal": '.count($totalRecords).',
					  "recordsFiltered":'.count($totalRecords).',
					  "data": ['.$newData.']
					}';
	}
					
	
	
	 public function edit($id=null)
    {
		echo $id;die;
	}
	
	public function suspend()
	{
		$id = $_REQUEST['id']; 
		$suspend=DB::table('dn_users')
            ->where('id', $id)
            ->update(['active' => 0]);
			print_r($suspend);
			if($suspend)
			{
				echo "success";
			}
			else{
				echo "fail";
			}
		die;
	}
	
	public function charges()
	{
		//die 'hello';
		return $this->view('users.charges');
	}

	public function suspendedUserList(Request $request){

	$userData =array();	
	$data = $request->all();
	$adminId = Auth::id();
	$citiesCount=array(); 
	$states=array();
	$maximumSuspendBy=array();
	$adminList=DB::table('dn_users')->select('dn_users.id','first_name','last_name')->leftjoin('role_user', 'role_user.user_id', '=', 'dn_users.id')->whereIn('role_id', [1, 2])->get();
	//echo "<pre>"; print_r($adminList);die;

	$users = DB::table('role_user')
					->select(array('dn_users.*'))
					->leftjoin('dn_users', 'role_user.user_id', '=', 'dn_users.id')			
					->where('dn_users.id', '!=',$adminId )
					->where('dn_users.active','0' )
					->whereIn('role_id', [1, 2])
					->paginate(config('admin.user.perpage'));
					
	$sql = 'SELECT dn_users.id FROM dn_users left join  role_user on dn_users.id = role_user.user_id WHERE dn_users.id != "'.$adminId.'" AND dn_users.active = "0" AND role_user.role_id IN ("1","2")';
	$userIds=DB::select(DB::raw($sql));

	//print_r( $userIds ); die;

	if(!empty($userIds)){




	if(isset($userIds))
	{
	
	$usersList=array();
	foreach($userIds as $value)
	{
		$usersList[]=$value->id;
	}
	}	
	$userLog=DB::table('dn_users_changed_status_log')
						->select(array('dn_users_changed_status_log.*'))
						->whereIn('entity_id',$usersList)
						->where('status_type','user_suspend')
						->groupby('entity_id')
						->orderBy('created_at','DESC')
						->distinct()->get();
	//echo "<pre>"; print_r($userLog); die;
	foreach($userLog as $key=>$value)
	{
		$userData[]=(array)$value;
	}

	 $counts = array_count_values(array_column($userData, 'added_by'));

	 //echo  $counts;
	if($counts){
		$max = max($counts); // $max == 7
	} else {
		$max = "";
	}
	 
	//echo $max;
	 
     $maximumSuspendById=array_keys($counts, $max);

     //echo $maximumSuspendById[0]->first();
	 if($max){
	 	 $maximumSuspendBy=DB::table('dn_users')->select(array(DB::raw('first_name as suspended_by_f_name,last_name as suspended_by_l_name')))->where('id',$maximumSuspendById[0])->first();
	 	} else {

	 		$maximumSuspendBy=array();
	 	}
	
	//echo "<pre>"; print_r($maximumSuspendBy);exit;
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
}
	$citys=array();
	$citys = DB::table('dn_users')
					->select(array('dn_users.city','dn_cities.*', DB::raw('COUNT(dn_users.id) as no_of_users,dn_users.id as pid')))
					->leftjoin('dn_cities', 'dn_users.city', '=', 'dn_cities.id')
					->leftjoin('dn_states', 'dn_users.state', '=', 'dn_states.state_code')
					->leftjoin('role_user', 'dn_users.id', '=', 'role_user.user_id')
					->where('dn_users.city','!=',0)
					->where('dn_users.active',0)
					->whereIN('role_user.role_id',[1,2])
					
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
			$cty=(object)array('city'=>'N/A','no_of_users'=>'N/A','state_code'=>'N/A');
			@$citiesCount=array('least'=>@$cty,'most'=>$cty);}
	



	//print_r($citiesCount);die;
	return $this->view('users.suspendedUser', compact('users','adminList','citiesCount','states','maximumSuspendBy'));
	
	//return $this->view('users.suspendedUser', compact('users','citiesCount','states','maximumSuspendBy'));
}


/**
  * @FUNCTION FOR DISPLAY SUSPENDED USER WITH AJAX
  * @Author : Vaibhav Bharti
  * @Params : $request is the array of  all http request
  * @Return : '$user' for user data, '$permission' for users permission 
  **/
 public function displaySuspendedUserAjax(Request $request)
  {
	  /* initializing the variables */
	$data = $request->all();
	$limit = 10;
	$adminId = Auth::id();
	$draw = $data['draw'];
	$offset = $data['start'];
	$searchString=$data['search']['value'];
	$startDate=$data['startDate'];
	$endDate=$data['endDate'];
	$revokedBy=$data['revokedBy'];
	/* Array to sort details  */
	$orderfields=array('0'=>'unique_code','1'=>'first_name','2'=>'last_name','3'=>'created_at','5'=>'email','6'=>'contact_number','7'=>'first_name');
	$field='id';
	$direction='ASC';
	/* code for order by data of user*/
	if(!empty($data['order'][0])){
	foreach($orderfields as $key=>$orderfield){
	if($key==$data['order'][0]['column'] and $key !='3')
	{
		$field=$orderfield;
		$direction=$data['order'][0]['dir'];
	}
    elseif($key==$data['order'][0]['column'] and $key =='3'){
		 $fieldForSuspensionDate=$orderfield;
	     $directionForSuspensionDate=$data['order'][0]['dir']; 
	}}}
	
	/* code for searching of  user*/
	$sql = 'SELECT dn_users.id FROM dn_users left join  role_user on dn_users.id = role_user.user_id WHERE dn_users.id != "'.$adminId.'" AND dn_users.active = "0" AND role_user.role_id IN ("1","2")';
	if(!empty($startDate) &&  !empty($endDate))
	{
		$startDate=$date = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $startDate)));
		$endDate=date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $endDate)));
		$sql .=" AND  dn_users.created_at BETWEEN '$startDate' AND '$endDate'";
	}
	if($data['state']!='')
	{
		$state = $data['state'];
		$sql .=" AND  dn_users.state= '$state'";
	}
	if($data['city']!='')
	{
		 $city = $data['city'];
		 $sql .=" AND  dn_users.city= '$city'";
	}
	if($data['state']!='' && $data['city']!='')
	{
		$state = $data['state'];
		$city = $data['city'];
		$sql .="AND  dn_users.state= '$state' AND  city= '$city'";
	}
	if(@$searchString!='')
	{	
		$search = "%$searchString%";
		$sql .=" AND  (dn_users.first_name LIKE '$search' or dn_users.last_name LIKE '$search' or dn_users.email LIKE '$search') ";
	}
	
	$sql .= " order by $field $direction ";
	$usersIds=DB::select(DB::raw($sql));

	//print_r($usersIds);

	
	//print_r($sql);
	if(!empty($usersIds))
	{
	$usersList=array();
	foreach($usersIds as $value)
	{
		$usersList[]=$value->id;
		//$usersList_2[]="'".$value->id."'";
	
	}
	
	if(isset($fieldForSuspensionDate)){
		
		$maxIds=DB::select(DB::raw("SELECT  * FROM `dn_users_changed_status_log`  WHERE `id` IN (
									SELECT MAX(`id`) FROM `dn_users_changed_status_log` where entity_id in(".implode(',',$usersList).") GROUP BY `entity_id`  ) and `status_type` = 'user_suspend'
									order by `created_at` desc"));

		
		
		
		$usersList = array();

		foreach($maxIds AS $value){
		
			 //$usersList[]=$value[0]->entity_id;
			array_push($usersList,$value->entity_id);
		}	
	}
	
	if(!empty(@$revokedBy))
		{

		$uIds = DB::select(DB::raw("SELECT  distinct * FROM `dn_users_changed_status_log`  WHERE `id` IN (SELECT MAX(`id`) FROM `dn_users_changed_status_log` where entity_id in(".implode(',',$usersList).") GROUP BY `entity_id`  ) and `status_type` = 'user_suspend' and `added_by` = '".$revokedBy."' order by `created_at` desc"));
		unset($usersList); 
		$usersList=array();
			foreach($uIds AS $value){
			 //$usersList[]=$value[0]->entity_id;
			array_push($usersList,$value->entity_id);
		}	
		}
	
	}

	$users = array();
	$full_user_array = array();
	$totalRecords = 0;
	$initializer = 0;

	if(isset($usersList)){

	foreach($usersList AS $key=>$value){
			// echo $key.'=>'.$value. '<br/>';
			$users_1 = DB::table('dn_users')
					->select(array('dn_users.*'))	
					->where('id',$value)
					->take($limit)->offset($offset)->orderBy($field,$direction)->first();
			$user_id_1 = $users_1->id;
			
			$userLog=DB::table('dn_users_changed_status_log')
						->select(array('dn_users_changed_status_log.*'))
						->where('entity_id',$value)
						->where('status_type','user_suspend')
						->groupby('created_at')
						->orderBy('created_at','Desc')
						->distinct()->first();
						//print_r($userLog);
			$suspended_user_id_1 = $userLog->entity_id;
			$suspendedby_user_id_1 = $userLog->added_by;
			$suspendedby_user_details_1 = DB::table('dn_users')
					->select(array('dn_users.*'))	
					->where('id',$suspendedby_user_id_1)
					->take($limit)->offset($offset)->orderBy($field,$direction)->first();
			
			$full_user_array[$initializer]['userdetails'] = $users_1;
			$full_user_array[$initializer]['suspendedbyuserdetails'] = $suspendedby_user_details_1->first_name;
			$first_name = $users_1->first_name;
			$last_name = $users_1->last_name;
			$email= $users_1->email;
			$phone= $users_1->contact_number;
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
			$Data[]= "[". '"'.$users_1->unique_code .'"' . ",". '"'.$first_name .'"'.",". '"'.$last_name.'"' .",". '"'.date('m/d/Y', strtotime($userLog->created_at)).'"'.",". '"N/A"'.",". '"'.$email.'"'.",". '"'.$users_1->contact_number.'"'.",". '"'.$full_user_array[$initializer]['suspendedbyuserdetails'].'"'."]";
			$initializer++;
	}}
	

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
	  
  }
    
   

}
