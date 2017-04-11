
@extends($layout)
@section('title', 'driverLog')

@section('content')
<div class="single-driver content-part manage-notification">
  <div class="col-sm-12 pad seaction1-driver">
    <div class="col-sm-3 pad new-image"> <img alt="a picture" id="profile_pic" class="img-thumbnail" src="http://www.mobilytedev.com/deziNow/public/img/memberImages/4b31504ab172931b912d44cbe3e976de6a35c54b.png">
      <p><span>Full Name:</span>{!! $users->first_name !!} {!! $users->last_name !!}</p>
      <p><span>Email:</span>{!! $users->email !!}</p>
      <p><span>Phone:</span>{!! $users->contact_number !!}</p>
      <p><span>DOB:</span>{{ date('d-m-Y', strtotime($users->dob)) }}</p>
      <p><span>Aniversary:</span>{{ date('d-m-Y', strtotime($users->anniversary)) }}</p>
      <p><span>{{ ucfirst($users->gender) }}:</span>{{ (date('Y') - date('Y',strtotime($users->dob))) }}</p>
    </div>
    <div class="col-sm-9 pad">
      <div class="col-sm-6 pad new-manage">
        <p><span>User Type:</span>Passanger  <a href="">View Passanger Profile</a> </p>
        <div class="address-line">
          <p><span>User ID:</span>{{ ucfirst($users->unique_code) }}</p>
		  <p><span>Approved by:</span>Pallav</p>
        </div>
	  </div>
	     <div class="col-sm-6 pad">
      <div class="col-sm-7 suspend-button">
        <button type="submit" class="btn btn-default">Suspend User Account</button>
		    <div class="address-line">
          <p><span>Join Date:</span>10-10-2016</p>
		   <p><span>Status:</span>Active</p>
        </div>
      </div>
      <div class="col-sm-5 dateofjoining suspend-button">
        <button type="submit" class="btn btn-default">Revoke Driver</button>
		    <div class="address-line">
           <p><span>Approved Date:</span>10-10-2015</p>
        <p><span>DeziBonus:</span>N/A<span class="glyphicon glyphicon-edit"></span></p>
        </div>
   
      </div>
	  </div>
	   <div class="col-sm-2 pad manage-span">
	    <p><span>N/A:</span>Rides Given</p>
		</div>
		 <div class="col-sm-2 pad manage-span">
	    <p><span>N/A:</span>Issues Raised</p>
		</div>
		 <div class="col-sm-2 pad manage-span">
	    <p><span>N/A:</span>Last Ride</p>
		</div>
		 <div class="col-sm-2 pad manage-span">
	    <p><span>N/A:</span>Bill Cleared</p>
		</div>
		 <div class="col-sm-2 pad manage-span">
	    <p><span>N/A:</span>Earnings</p>
		</div>
			 <div class="col-sm-2 pad manage-span">

		</div>
    </div>
  </div>
  <hr>
  </hr>
  <div class="next-seaction">
   @if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
	@endif
	@if(Session::has('error'))
	<p class="alert alert-error">{{ Session::get('message') }}</p>
	@endif
  <h3>Change Log</h3>

<div id="changeLogs">
@foreach ($logs as $log)
    <div class="sub-seaction1">
		<div class="pull-left seaction-left">
		<p>{{ $log->text }}</p>
		</div>
		<div class="pull-right seaction-right">
		<p><span>Time-stamp:</span>{{ date('d-m-Y h:i A', strtotime($log->added_on)) }}</p>
		<p><span>Admin:</span>{{ $log->first_name }}</p>
		</div>
	</div>
@endforeach
	
</div> 
 {!! Form::open(array('method' => 'POST','id' => 'driverchangelog','name'=>'driverchangelog','files' => true)) !!}  
	<textarea rows="4" col="50" class="text-lorem" name="changeText"></textarea>
	<div class="pull-right manage-last">
	<button type="cancel" class="btn btn-default manage-can">Cancel</button>
	<button type="submit" class="btn btn-default">Submit</button>

	</div>
 {!! Form::close() !!} 
  </div>
  </div>

@stop
@stop
