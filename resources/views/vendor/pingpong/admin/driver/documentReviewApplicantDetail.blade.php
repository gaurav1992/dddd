@extends($layout)
@section('title', 'New Application')
@section('content-header')
<h1 style="text-align:center;"> {!! $title or 'Updated Document Details of driver' !!} </h1>
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

@section('content')
	<div class="single-driver content-part">
		<div class="col-sm-12 pad seaction1-driver">
			<?php 
				if (!empty($driver_data)) {
					$license_verification = $driver_data->license_verification;
					$proof_of_insurance   = $driver_data->proof_of_insurance;
					$driver_records   = $driver_data->driver_records;
				}
      
			?>
 
			<div class="col-sm-3 pad new-image"> <?php if(empty(@$user->profile_pic)){ 
					$profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g"; 
				 } else {
					$profile_pic = $driver_meta_data->driver_profile_pic;
				 } ?>
			    {!! HTML::image( $profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
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
			
			<div class="col-sm-4 pad">
				
				<div class="col-sm-6 pad">
					<p><span>User Type:</span>
			
					<?php   
					if ($user->role_id == 3) { 
						echo "Passenger"; 
					}elseif ( $user->role_id == 4 ) { 
						echo "Driver"; 
					}elseif ( $user->role_id == 2 ) { 
						echo "Sub Admin"; } else { echo "Administrator"; 
					} 
					?>
					</p>
					<p><span>User ID:</span>{{ @$user->user_id }}</p>
					<div class="address-line">
					  <p><span>Address:</span>{{ @$user->address_1 }}</p>
					  <p><span>City:</span>{{ @$user->city }}</p>
					  <p><span>State:</span>{{ @$user->state }}</p>
					  <p><span>Zip:</span>{{ @$user->zip_code }}</p>
					  <p><span>Gender:</span>{{ @$user->gender }}</p>
					</div>
					</div>
					<?php $id=base64_encode(convert_uuencode(@$user->user_id));  ?>
					<div class="col-sm-6 pad"> 
						<a href="{{ route('passengerDetail',[$id]) }}">View Passenger Profile</a> 
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

						<p><span>Join Date:</span>{{ $user->created_at }}</p>
						<p><span>Date Applied:</span>20-10-2015</p>
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

										<p><span>Join Date:</span>{{ $user->created_at }}</p>
										<p><span>Date Applied:</span>20-10-2015</p>
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
  <div class="col-sm-12 pull-left seaction1-driver licence-seaction">
    <div class="col-sm-4">
      <p><span>Licence Number:</span>{{ @$driver_meta_data->license_number }}</p>
      <p><span>Licence Expiry Date:</span>{!!  date('m-d-Y', strtotime(@$driver_data->licence_expiration)) !!}</p>
		<p><span>Insurance Expiry Date:</span>{!!  date('m-d-Y', strtotime(@$driver_data->insurance_expiration)) !!}</p>
	 <!--Subadmin Permission Code Start-->
		<?php 
			$loggedInUserPermission = Session::get('userPermissions'); 
			if(empty($loggedInUserPermission)){
			
			?>
				<!--Subadmin Permission Code Start-->

				  <p><span>SSN/TIN:</span>{{ @$driver_meta_data->ssn }} </p>
			<?php 
			}else if(!empty($loggedInUserPermission)){
			?>		
				 <?php if(!empty($driver_meta_data->ssn)){
							
							$ssn = '#####'.substr($driver_meta_data->ssn, -4);
					  }
				  ?>
				  <p><span>SSN/TIN:</span>{{ @$ssn }} </p>
				
			<?php 
			} 
		?>
		

    </div>
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4"> </div>
  </div>
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
