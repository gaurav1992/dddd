@extends('frontend.common')
@section('content')
<div class="container-fluid no-padding" id="inner-header">
    <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">PROFILE</h3>
</div>

<!--  SECTION-1 -->
<?php 
    $id = Auth::id();
    if(Auth::check()) {
        $user_detail = DB::table('dn_users')->select('*')->where('id', $id)->first();
        $driver_detail = DB::table('dn_driver_requests')->select('*')->where('user_id', $id)->first();
        //print_r($user_detail);
        $joinind_date = $user_detail->created_at;
        $email = $user_detail->email;
        $contact_number = $user_detail->contact_number;
        $dob = $user_detail->dob;
        $anniversary = $user_detail->anniversary;
        $first_name = $user_detail->first_name;
        $last_name = $user_detail->last_name;

        $profile_pic = $user_detail->profile_pic;

        $gender = $user_detail->gender;
        $is_driver_approved = $user_detail->is_driver_approved;

        if($dob ==''){
        	$newDob = '';
        }else{
        	$origionalDob = $dob;
	        $arrBob = explode('-', $origionalDob);
	        $newDob = $arrBob[1].'/'.$arrBob[2].'/'.$arrBob[0];
        }
        
        if($anniversary ==''){
        	$newanniversary ='';
        }else{
        	$origionalanniversary = $anniversary;
	        $arranniversary = explode('-', $origionalanniversary);
	        $newanniversary = $arranniversary[1].'/'.$arranniversary[2].'/'.$arranniversary[0];
        }

        $createjoinind_date = new DateTime($joinind_date);
        $newjoinind_date = $createjoinind_date->format('m/d/Y');

        if($driver_detail){
            $car_transmission = $driver_detail->car_transmission;
            //$navigation = $driver_detail->navigation;
        }else{
            $car_transmission = '';
            //$navigation = '';
        }

        $dn_users_data = DB::table('dn_users_data')->select('*')->where('user_id', $id)->first();
        if($dn_users_data){
            $license_number = $dn_users_data->license_number;
            $ssn = $dn_users_data->ssn;
        }else{
            $license_number = '';
            $ssn = '';
        }
?>
<section>
	<div class="container" id="driverprofileedit">
		<div class="mtop-30 row">
			
			@include('frontend.passengersidebar')
			 
			<div class="col-md-8">
				<div class="edit-btnn">
					<?php if($is_driver_approved == '1'){ ?>
						<a href="{!! asset('/editpassenger') !!}" class="btn-grn edit pull-right">EDIT PROFILE</a>
					<?php }else{ ?>
						<a href="{!! asset('/editpassenger') !!}" class="btn-grn edit pull-right">EDIT PROFILE</a>
					<?php } ?>

				</div>
				@if(Session::has('message'))
					<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
				  @endif
				<div class="right-sec">
					<!--<div class="col-sm-12">
						<?php //if($profile_pic !=''){ ?>
							<img src="{!! asset($profile_pic) !!}" class="img-responsive center-block thumbnail profilePic" alt="#"/>
						<?php //}else{ ?>
							<img src="{!! asset('public/images/passanger.png') !!}" class="img-responsive center-block thumbnail profilePic" alt="#"/>
						<?php //} ?>
						<p class="user-profile">{!! $first_name,' ',$last_name !!}</p>       
					</div>-->
					
					<div class="col-sm-7 edit-profile">
						<p><span class="field">Email:</span><span class="pull-right">{!! $email !!}</span></p>
						<p><span class="field">Phone:</span><span class="pull-right">{!! $contact_number !!}</span></p>
						<?php if($newDob !=''){ ?>
							<p><span class="field">Date of Birth :</span><span class="pull-right">{!! $newDob !!}</span></p>
						<?php } ?>
						<?php if($newanniversary !=''){ ?>
							<p><span class="field">Date of Anniversary :</span><span class="pull-right">{!! $newanniversary !!}</span></p>
						<?php } ?>	
						<?php if($is_driver_approved == '1'){ ?>
							<p><span class="field">Gender:</span><span class="pull-right">{!! $gender !!}</span></p>
							<p><span class="field">Car Transmission:</span><span class="pull-right">{!! $car_transmission !!}</span></p>
							<p><span class="field">Driver's License #</span><span class="pull-right">{!! $license_number !!}</span></p>
						<?php } ?>
					</div>
				</div>    
			</div>
		</div>
	</div>
</section>     
<?php }else{ ?>

<?php } ?>
@endsection