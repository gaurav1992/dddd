@extends($layout)
@section('title', 'New Application')
@section('content-header')

<div style="clear:both"></div>
{!!@$message!!}
@stop

@section('customjavascript')
<script>
var driverActionUrl = "{!! route('driverAction') !!}";
var newDriverUrl = "{!! route('newDriverAjax') !!}";
var homeUrl= "{!! route('index') !!}";
</script>
@stop
<?php 

					if (!empty($driver_data)) {

					  $license_verification = $driver_data->license_verification;
					  $proof_of_insurance   = $driver_data->proof_of_insurance;
					  $driver_records   = $driver_data->driver_records;
					}
					  
					?>	
@section('content')

	<div class="row">
		<div class="col-md-12">
			<div class="main_heading">
                   <h3>New Applicant Details</h3>
			</div>
		</div>
	</div>
     <section class="content">	
		<div class="row">
			<div class="col-md-4">
				
				<!-- Profile picture-->
				<div class="col-md-12 browse-image">
										
				 <?php if(empty($driver_meta_data->driver_profile_pic)){ $driver_meta_data->driver_profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
						$driver_meta_data->driver_profile_pic = $driver_meta_data->driver_profile_pic;} ?>
				 {!! HTML::image($driver_meta_data->driver_profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
					
				</div>
				
				<!-- Basic Detail-->
				<ul class="col-md-12 profile_dl">
				
					<li>
					<p class="rad-heading"> <label> Full Name : </label> {{$user->first_name .' '. $user->last_name }} </p>
					</li>
				
					<li>
					<p class="rad-heading"> <label> Email : </label> {{$user->email}} </p>
					</li>
					
					<li>
					<p class="rad-heading"> <label> Phone : </label> {{$user->contact_number}}</p>
					</li>												
					
					<li>
					<p class="rad-heading"> <label> DOB : </label> {!!  date('m-d-Y', strtotime($user->dob)) !!} </p>
					</li>
					
					<li>
					<p class="rad-heading"> <label> Anniversary: </label> @if($user->anniversary=="0000-00-00") {!! $user->anniversary="N/A" !!} @else {!!  date('m-d-Y', strtotime($user->anniversary)) !!} @endif</p>
					</li>

					<li>
					<p class="rad-heading"> <label> Gender: </label> {{$user->gender}}</p>
					</li>
					<li>
					<p class="rad-heading"> <label> Age: </label> {{@$age}} years</p>
					</li>	
				
				</ul>
					
			</div>
         
			<div class="col-md-4">
				<ul class="col-md-12 profile_dl">
					<li>
					<p class="rad-heading">
						<label> User Type : </label>  Passenger
						<a href="<?php echo url(). "/admin/passenger-detail/".base64_encode(convert_uuencode($user->id));?>"> View Passenger Profile </a>
					</p>
					</li>
					
					<li>
					<p class="rad-heading">
						<label> User ID : </label>  {{$user->unique_code}}
					</p>
					</li>
					<li>
					<p class="rad-heading" >
						<label> Address : </label> {{$user->address_1}},{{$user->address_2}}
					</p>
					</li>



					
					<li>
					<p class="rad-heading">
						<label> City: </label> {{ @$user->city }}
					</p>
					</li>
					
					<li>
					<p class="rad-heading">
						<label> State : </label> {{ @$user->state }}
					</p>
					</li>
					
					<li>
					<p class="rad-heading">
						<label> Zip : </label> {{ @$user->zip_code }}
					</p>
					</li>
									
					<li>
					<p class="rad-heading">
						<label> Join Date : </label> {!!  date('m-d-Y', strtotime($user->created_at)) !!}
					</p>
					</li>

                    <li>
                        <p class="rad-heading">
                            <label>Date Applied:</label>{!!  date('m-d-Y', strtotime(@$driver_data->created_at)) !!}
                        </p>
                    </li>
                    <li>
                        <p class="rad-heading">
                            <label>Licence Number:</label>{{ @$driver_meta_data->license_number }}
                        </p>
                    </li>
                    <li>
                        <p class="rad-heading">
                            <label>Licence Expiry Date:</label>{!!  date('m-d-Y', strtotime(@$user->licence_expiration)) !!}
                        </p>
                    </li>
                    <li>
                        <p class="rad-heading">
                            <label>Insurance Expiry Date:</label>{!!  date('m-d-Y', strtotime(@$user->insurance_expiration)) !!}
                        </p>
                    </li>
                    <li>
                        <p class="rad-heading">
                        		<?php 
								$loggedInUserPermission = Session::get('userPermissions');
								if(empty($loggedInUserPermission))
								{
								?>
                            	<label>SSN/TIN:</label>{{ @$driver_meta_data->ssn }} </p>
                            	<?php }else{

                            		echo '<label>SSN/TIN:</label>'.'#####'.substr(@$driver_meta_data->ssn, -4).' </p>';
                            	} ?>
                        </p>
                    </li>
				</ul>		
								
			</div>
			
			<div class="col-md-4">
								<?php 
				$loggedInUserPermission = Session::get('userPermissions');
				foreach($loggedInUserPermission as $k=>$allModule){
					$allMod[]= $allModule->module_slug;
					$allModPer[$allModule->module_slug]= $allModule;
				}
				?>

				 <?php 
				if(empty($loggedInUserPermission))
				{	
				?>
				<div class="col-sm-12 pad">

					<div class="col-sm-12 suspend-button">
        
						  <?php if ($user->active == 1) { ?>
						  
							<button type="submit" class="btn btn-default btnDriver_suspend" data-action="driver_suspend" data-userid="{{ $user->user_id }}">Suspend User </button>
						
						  <?php } else { ?>
							
							<button type="submit" class="btn btn-default btnDriver_active" data-action="driver_active" data-userid="{{ $user->user_id }}">Activate User</button>
						  
						  <?php } ?>
        
					</div>
		
      
						<div class="col-sm-12 dateofjoining suspend-button">

						@if($user->is_driver_approved==2)
						  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
						@endif

						@if($user->is_driver_approved==1)
						  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
						@endif

						@if($user->is_driver_approved==0)
						  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
						  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
						@endif


						<div style="width:100%;clear:both"></div>

						
					  </div>
				</div>
	<?php 
		}else if(!empty($loggedInUserPermission)){
			foreach($loggedInUserPermission as $userPermission){
			if($userPermission->module_slug=="driver_applicants" && $userPermission->edit_permission==1 ){
				foreach($loggedInUserPermission as $drvrPermission){
					
					if($drvrPermission->module_slug=="driver_applicants"){
						
						if($allModPer[$drvrPermission->module_slug]->edit_permission==1){
						?>
								
								<div class="col-sm-12 pad">

										<div class="col-sm-12 suspend-button">
										
										  <?php if ($user->active == 1) { ?>
										  
											<button type="submit" class="btn btn-default btnDriver_suspend" data-action="driver_suspend" data-userid="{{ $user->user_id }}">Suspend User </button>
										
										  <?php } else { ?>
											
											<button type="submit" class="btn btn-default btnDriver_active" data-action="driver_active" data-userid="{{ $user->user_id }}">Activate User</button>
										  
										  <?php } ?>
										
										</div>
										
									  
									  <div class="col-sm-12 dateofjoining suspend-button">

										@if($user->is_driver_approved==2)
										  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
										@endif

										@if($user->is_driver_approved==1)
										  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
										@endif

										@if($user->is_driver_approved==0)
										  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
										  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
										@endif

										<p style="text-align:left !important;font-size:13px" class="pull-left"><span>Join Date:</span>{!!  date('m-d-Y', strtotime(@$user->driver_requested_on)) !!}</p>
										<p style="text-align:left !important;font-size:13px" class="pull-left"><span>Date Applied:</span>{!!  date('m-d-Y', strtotime(@$user->created_at)) !!}</p>
									  </div>
									</div>
								
								
							<?php
						}
					}
				}
			}
		}	
	}				
		?>	
			</div>
		</div>   
	</section>
	<hr>
<?php /*		     
	<div class="single-driver content-part">
		<div class="col-sm-12 pad seaction1-driver">
			<?php 
				if (!empty($driver_data)) {
					$license_verification = $driver_data->license_verification;
					$proof_of_insurance   = $driver_data->proof_of_insurance;
					$driver_records   = $driver_data->driver_records;
				}
      
			?>

			<div class="col-sm-3 pad new-image"> 
			<?php if(empty($user->profile_pic)){ $user->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
										$user->profile_pic = $user->profile_pic;} ?>
			{!! HTML::image($user->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
				<p><span>Full Name:</span>{{ $user->first_name }} {{ $user->last_name}}</p>
				<p><span>Email:</span>{{ $user->email }}</p>
				<p><span>Phone:</span>{{ $user->contact_number }}</p>
				<p><span>DOB:</span>{{ $user->dob }}</p>
				<p><span>Aniversary:</span>{{ $user->anniversary }}</p>
				<p><span>Age:</span>
				<?php $from = new DateTime( $user->dob );
				$to   = new DateTime('today');
				echo $from->diff($to)->y ?>
				</p>

			</div>
			
			<div class="col-sm-6 pad">
				
				<div class="col-sm-6 pad">
					<p><span>User Type:</span>
			
					<?php   
					if ($user->role_id == 3) { 
						echo "Passanger"; 
					}elseif ( $user->role_id == 4 ) { 
						echo "Driver"; 
					}elseif ( $user->role_id == 2 ) { 
						echo "Sub Admin"; } else { echo "Administrator"; 
					} 
					?>
					<?php $id=base64_encode(convert_uuencode(@$user->user_id));  ?>
					<a href="{{ route('passengerDetail',[$id]) }}">View Passenger Profile</a> 
					</p>
					<p><span>User ID:</span>{{ @$user->user_id }}</p>
					<div class="address-line">
					  <p><span>Address:</span>{{ @$user->address_1 }} {{ @$user->address_2 }}</p>
					  <p><span>City:</span>{{ @$user->city }}</p>
					  .
					  <p><span>State:</span>{{ @$user->state }}</p>
					  
					  <p><span>Zip:</span>{{ @$user->zip_code }}</p>
					  <p><span>Gender:</span>{{ @$user->gender }}</p>
					</div>
					</div>
				</div>

				<?php 
				$loggedInUserPermission = Session::get('userPermissions');
				foreach($loggedInUserPermission as $k=>$allModule){
					$allMod[]= $allModule->module_slug;
					$allModPer[$allModule->module_slug]= $allModule;
				}
				?>

				 <?php 
				if(empty($loggedInUserPermission))
				{	
				?>
				<div class="col-sm-3 pad">

					<div class="col-sm-12 suspend-button">
        
						  <?php if ($user->active == 1) { ?>
						  
							<button type="submit" class="btn btn-default btnDriver_suspend" data-action="driver_suspend" data-userid="{{ $user->user_id }}">Suspend User </button>
						
						  <?php } else { ?>
							
							<button type="submit" class="btn btn-default btnDriver_active" data-action="driver_active" data-userid="{{ $user->user_id }}">Activate User</button>
						  
						  <?php } ?>
        
					</div>
		
      
						<div class="col-sm-12 dateofjoining suspend-button">

						@if($user->is_driver_approved==2)
						  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
						@endif

						@if($user->is_driver_approved==1)
						  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
						@endif

						@if($user->is_driver_approved==0)
						  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
						  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
						@endif

						<p style="text-align:left !important;font-size:13px" class="pull-left"><span>Join Date:</span>{!!  date('m-d-Y', strtotime(@$user->driver_requested_on)) !!}</p>
						<p style="text-align:left !important;font-size:13px" class="pull-left"><span>Date Applied:</span>{!!  date('m-d-Y', strtotime(@$user->created_at)) !!}</p>
						
					  </div>
				</div>
	<?php 
		}else if(!empty($loggedInUserPermission)){
			foreach($loggedInUserPermission as $userPermission){
			if($userPermission->module_slug=="driver_applicants" && $userPermission->edit_permission==1 ){
				foreach($loggedInUserPermission as $drvrPermission){
					
					if($drvrPermission->module_slug=="driver_applicants"){
						
						if($allModPer[$drvrPermission->module_slug]->edit_permission==1){
						?>
								
								<div class="col-sm-5 pad">

										<div class="col-sm-7 suspend-button">
										
										  <?php if ($user->active == 1) { ?>
										  
											<button type="submit" class="btn btn-default btnDriver_suspend" data-action="driver_suspend" data-userid="{{ $user->user_id }}">Suspend User </button>
										
										  <?php } else { ?>
											
											<button type="submit" class="btn btn-default btnDriver_active" data-action="driver_active" data-userid="{{ $user->user_id }}">Activate User</button>
										  
										  <?php } ?>
										
										</div>
										
									  
									  <div class="col-sm-5 dateofjoining suspend-button">

										@if($user->is_driver_approved==2)
										  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
										@endif

										@if($user->is_driver_approved==1)
										  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
										@endif

										@if($user->is_driver_approved==0)
										  <button type="submit" class="btn btn-default btnDriver_approve" data-action="btndriver_approve" data-userid="{{ $user->user_id }}">Approve Driver</button>
										  <button type="submit" class="btn btn-default btnDriver_disapprove" data-action="btndriver_disapprove" data-userid="{{ $user->user_id }}">Disapprove Driver</button>
										@endif

										<p style="text-align:left !important;font-size:13px" class="pull-left"><span>Join Date:</span>{!!  date('m-d-Y', strtotime(@$user->driver_requested_on)) !!}</p>
										<p style="text-align:left !important;font-size:13px" class="pull-left"><span>Date Applied:</span>{!!  date('m-d-Y', strtotime(@$user->created_at)) !!}</p>
									  </div>
									</div>
								
								
							<?php
						}
					}
				}
			}
		}	
	}				
		?>	
		
		
  </div>
  <hr>
  </hr>
  */ ?>
  <div class="col-sm-12 pad seaction1-driver">

    <?php 

    
    if (!empty($driver_records)) {

      $qas = json_decode($driver_records);

      foreach ($qas as $qa) { ?>
        
      <div class="pull-left col-sm-6">
        <p><span>
          <?php echo $qa->question; ?></span>
          
          <?php 
            if ( $qa->answer == '1' ) { 
              echo "YES"; 
            } else if( $qa->answer == '0' ) { 
              echo "NO"; 
            } 
            else { 

              echo '</br>'.
                  '<div class="col-sm-4  seaction1-driver">
                  <p>'.$qa->answer.'</p>
                  </div>
              '; 
          } ?></p>
      
      </div>

    <?php } 
	} ?>


  </div>
  <div class="col-sm-12 pad">
    
   
    <div class="col-sm-8 seaction1-driver">
      
      <ul class="glyp">

        <span class="glyphicon glyphicon-file"></span>
        <li>
          <a href="{!! asset($license_verification) !!}" target="_blank">Driver License</a>
          <a href="{!! asset($license_verification) !!}" download>Download</a>
        </li>
        <span class="glyphicon glyphicon-file"></span>
        <li>
          <a href="{!! asset($proof_of_insurance) !!}" target="_blank">Proof Of Insurance</a>
          <a href="{!! asset( $proof_of_insurance) !!}" download>Download</a>
        </li>
      </ul>

      <!-- <ul class="glyp">
        <span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
        <span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
        <span class="glyphicon glyphicon-file"></span><li>DocumentName<a href="">Download</a></li>
      </ul> -->

    </div>
  </div>
</div>
@stop 

<style type="text/css">
.profile_dl label{text-align:left; width:160px;}
.border{border:1px solid #ff0000;}
.main_heading{width:100%; margin:0px; padding:0px;}
.main_heading h3{background:#238BCC; text-align:center; font-weight:normal; font-size:22px; color:#ffffff; padding:10px 0;}
hr {
  border-top: 2px solid #3c8dbc !important;
}

</style>
