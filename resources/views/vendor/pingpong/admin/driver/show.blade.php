<?php //echo  $user->id;exit;?>
@extends($layout)
@section('title', 'Driver profile')
@section('customjavascript')
<script>
var deleteCarUrl = "{!! route('deleteCar') !!}";
var suspendUrl = "{!! route('suspend') !!}";
var delImageUrl = "{!! route('deleteImage') !!}";
var revokeURL = "{!! route('revoke') !!}";
var indexUrl= "{!! route('passengerAjax') !!}";
var homeUrl= "{!! route('index') !!}";
var bankdetailsUrl= "{!! route('bankdetails') !!}";
var driverRideList= "{!! route('ridehistory') !!}";
/*avinash thakur 29th july getting data for a rider's bonus history*/
var driverBonusHistory = "{!! route('ridebonus') !!}";
/*added driverBonusHistory route variable*/
var isuueHistoryList = "{!! route('driverIssueAjax') !!}";
var userid="{!! $user->id!!}";
var EarningHisotryList= "{!! route('driverearningAjax') !!}";
var hourLog= "{!! route('hourlog') !!}";
var documentAjax= "{!! route('documentAjax') !!}";
var alldriver= "{!! route('alldriver') !!}";
var drID="{!! @$user->first_name .' '. @$user->last_name .'|('.@$user->unique_code .')'!!} ";
var userCreditURL= "{!! route('driverCredit') !!}";
var userSSNURL= "{!! route('userSSN') !!}";
var drivername="{{$user->first_name .' '. $user->last_name }}";
</script>
<script src="{!! admin_asset('js/driverList.js') !!}" type="text/javascript"></script>
<script type="text/javascript">

	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
		//Edit Credit Callout
		$( document ).on( "click", ".credit-edit", function( e ){
			e.preventDefault();
			$('.credit-edit-callout').show(400);
		});
		$( document ).on( "click", ".ssnEdit", function( e ){
			e.preventDefault();
			$('.ssnEditformdiv').show(400);
		});

		$( document ).on( "click", ".close-me", function( e ){
			
			e.preventDefault();
			$(this).parent().hide(400);
		});
		//Driver Dezi Credit
		$('#credit_form').on('submit', function(e) { //use on if jQuery 1.7+
        e.preventDefault();  //prevent form from submitting
        
        var creditData = $("#credit_form").serialize();
		var credit_amount =$("#credit_amount").val();
		
			var numeric = "/^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/";  
			if($.trim(credit_amount)==''){
				$("#errorcredit").html("Enter valid Credit amount.")
				$("#errorcredit").show();
				return false;
			}else if(/\D/g.test(credit_amount)){
				$("#errorcredit").html("Please input numeric number..")
				$("#errorcredit").show();
			return false;}
        console.log(creditData); //use the console for debugging, F12 in Chrome, not alerts

	    jQuery.ajax({
	        type : "post",
	        dataType : "json",
	        url : userCreditURL,
	        data : creditData,
	        success: function(response) {
	            
	            if (response.azStatus == 'success') {
	            	alert(response.azMessage);
	            	location.reload();
	            }

	            else { alert(response.azMessage); }
	        },
	        failure: function(errMsg) {
		        alert(errMsg);
		    }
	    });

    });

	
	/*START UPDATE SSN*/
		$('#ssnUpdate').on('submit', function(e) { 
			 e.preventDefault();  //prevent form from submitting
			var ssn =$("#ssnno").val();
		
			var numeric = "/^[+-]?\d+(\.\d+)?([eE][+-]?\d+)?$/";  
			if($.trim(ssn)==''){
				$("#errorssn").html("Enter valid SSN Nuber.")
				$("#errorssn").show();
				return false;
			}else if(/\D/g.test(ssn)){
				$("#errorssn").html("Please input numeric number..")
				$("#errorssn").show();
				return false;
			}else if(ssn.length !=9){
				$("#errorssn").html("Please input valid 9 Digit ssn number.")
				$("#errorssn").show();
				return false;
			}{  
				
		//use on if jQuery 1.7+
       
       
        var updateData = $("#ssnUpdate").serialize();

       	jQuery.ajax({
	        type : "post",
	        dataType : "json",
	        url : userSSNURL,
	        data : updateData,
	        success: function(response) {
	            
	            if (response.azStatus == 'success') {
	            	alert(response.azMessage);
	            	location.reload();
	            }

	            else { alert(response.azMessage); }
	        },
	        failure: function(errMsg) {
		        alert(errMsg);
		    }
	    });
	}
    });

	/*END UPDATE SSN*/
	});
</script>
@stop

@section('content')

<!-- Main content -->
	<div class="row">
		<div class="col-md-12">
			<div class="main_heading">
                   <h3>Single Driver Details</h3>
			</div>
		</div>
	</div>
                
    <section class="content">	
		<div class="row">
			<div class="col-md-4">
				
				<!-- Profile picture-->
				<div class="col-md-12 browse-image">
										
				 <?php 
				 if(empty($driver_meta_data->driver_profile_pic)){ 
				 $imgEmpty=true;
				 $driver_meta_data->driver_profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
					 $imgEmpty=false;
						$driver_meta_data->driver_profile_pic = $driver_meta_data->driver_profile_pic;} ?>
				 													
				 {!! HTML::image($driver_meta_data->driver_profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
					@if($imgEmpty==false)
					<a href='javascript:void(0);' style="float:left; width:100%; text-align:center; margin-bottom:10px" data-toggle="tooltip" data-placement="bottom" title="Delete Image" data-driverid="{!! $user->id!!}" class="delImage">
							<span class="glyphicon glyphicon-trash " style="color:red;">
									
							</span>
							Delete Image
						</a>
						 @endif
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
					<p class="rad-heading"> <label> Age: </label> {{$age}} years</p>
					</li>	
				
				</ul>
					
			</div>
         
			<div class="col-md-4">
				<ul class="col-md-12 profile_dl">
					<li>
					<p class="rad-heading">
						<label> User Type : </label>  Driver + @if(@$driver==1)  Passenger  @endif 
						<a href="<?php echo url(). "/admin/passenger-detail/".base64_encode(convert_uuencode($user->id));?>"> View Passenger Profile </a>
					</p>
					</li>
					
					<li>
					<p class="rad-heading">
						<label> User ID : </label>  {{$user->unique_code}}
					</p>
					</li>
					
					<li>
					<p class="rad-heading">
						<label> Status : </label> {{$user->active}}
					</p>
					</li>
					
					<li>
					<p class="rad-heading">
						 <label> Approved By : </label> {{ @$ApName }} 
					</p>
					</li>
					<li>
					<p class="rad-heading" style=" float: left;width: 100%;">
						<label> Address : </label> {{$user->address_1}},{{$user->address_2}}
					</p>
					</li>

					<li>
					<p class="rad-heading">
						<label> City: </label> {{ @$data_user->city }}
					</p>
					</li>
					
					<li>
					<p class="rad-heading">
						<label> State : </label> {{ @$data_user->state }}
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
						<label> Approval Date : </label> {!!  date('m-d-Y', strtotime(@$user->driver_approved_on)) !!}
					</p>
					</li>
				</ul>		
								
			</div>
			
			<div class="col-md-4">
				<?php $loggedInUserPermission = Session::get('userPermissions'); ?>
					@if(empty($loggedInUserPermission))
						 <div class="form-group">
							@if($user->active=='Suspended')
								<span> <a href='javascript:void(0);' class='passangerAzAction btn  btn-success  passenger_Active' data-action= 'passenger_Active' data-userid="{!! $user->id!!}" >Activate</a></span>	
							@else
								<span><a  href='javascript:void(0);' class='passangerAzAction btn btn-danger  driver_suspend' data-action= 'driver_suspend' data-userid="{!! $user->id!!}">Suspend</a> </span>	
							@endif
						
							@if(@$revoke=='1')
								<span class='dfg'><a href='javascript:void(0);' class='btn btn-success  driver_unrevoke' data-action= 'driver_unrevoke' data-userid="{!! $user->id!!}">Re-Allow</a></span>	
							@else
								<span><a class="btn btn-primary  driver_revoke" data-userid="{!! $user->id!!}" data-action="driver_revoke" href="javascript:void(0);"> Revoke</a></span>
							@endif	
						 
						</div>
						
		          @elseif(!empty($loggedInUserPermission))
										
						@foreach($loggedInUserPermission as $userPermission)	
								@if($userPermission->module_slug=="drivers" && $userPermission->edit_permission==1)
								 <div class="form-group">
									@if($user->active=='Suspended')
										<span> <a href='javascript:void(0);' class='passangerAzAction btn  btn-success  passenger_Active' data-action= 'passenger_Active' data-userid="{!! $user->id!!}" >Activate</a></span>	
									@else
										<span><a  href='javascript:void(0);' class='passangerAzAction btn btn-danger  driver_suspend' data-action= 'driver_suspend' data-userid="{!! $user->id!!}">Suspend</a> </span>	
									@endif
									
									@if(@$revoke=='1')
											<span class='dfg'><a href='javascript:void(0);' class='btn btn-success  driver_unrevoke' data-action= 'driver_unrevoke' data-userid="{!! $user->id!!}">Re-Allow</a></span>	
									@else
											<span><a class="btn btn-primary  driver_revoke" data-userid="{!! $user->id!!}" data-action="driver_revoke" href="javascript:void(0);"> Revoke</a></span>
									@endif	
								</div>
								@endif
						@endforeach
					@endif


					<!--Subadmin Permission Code Start-->
					<?php 
					if (!empty($driver_data)) {

					  $license_verification = $driver_data->license_verification;
					  $proof_of_insurance   = $driver_data->proof_of_insurance;
					  $driver_records   = $driver_data->driver_records;
					}
					  
					?>	
					<div class="form-group">
						<p class="rad-heading"> <label> Licence Number: </label>
						{{ @$driver_meta_data->license_number }}
						</p>
					</div>
					
					<div class="form-group">
						<p class="rad-heading"> <label> Licence Expiry Date:</label>
						{!!  date('m-d-Y', strtotime(@$driver_data->licence_expiration)) !!}
						</p>
					</div>
					
					<div class="form-group">
						<p class="rad-heading"> <label> Insurance Expiry Date: </label>
						{!!  date('m-d-Y', strtotime(@$driver_data->insurance_expiration)) !!}
						</p>
					</div>
								
					 <!--Subadmin Permission Code Start-->
					<?php 
						$loggedInUserPermission = Session::get('userPermissions'); 
						if(!empty($driver_meta_data->ssn)){
							$ssn=$driver_meta_data->ssn;
						}else{
							$ssn="####.........";
						}
						
						$ssnEdit = '<div class="form-group"><p>
						<label> SSN/TIN&nbsp;:&nbsp;</label>'.@$ssn.'&nbsp;<i class="fa fa-pencil-square-o ssnEdit" aria-hidden="true"></i></p></div>';
						$ssnViewonly= '<div class="form-group"><p><label><span>SSN/TIN:</span></label>'.'#####'.substr($ssn, -4).'</p></div>';

						if(empty($loggedInUserPermission)){
						?>	
						
							{!! $ssnEdit !!}
						
						<div class="callout callout-info ssnEditformdiv">
							<h4>Add SSN of this User</h4>
							<p>Follow the steps to continue.</p>
							<span class="close-me">X</span>

							{!! Form::open(array("id"=> "ssnUpdate", "class" => "ssnUpdate")) !!}
								<div class="box-body">
								  <div class="form-group">
									  <label for="credit_amount">Enter SSN : </label>
									
									  <input type="password" name="ssn" class="form-control" id="ssnno" placeholder="Enter The SSN" >
									  <span style="display:none;" id="errorssn"></span>
								  </div>
								</div><!-- /.box-body --> 

								<div class="box-footer">
									<input type="hidden" name="user_id" value="{!! $user->id !!}">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</form>
						</div>		 
						
						<?php 
						}else if(!empty($loggedInUserPermission)){
							echo $ssnViewonly;
						} 
						?>
						
						<div class="form-group">
							
							<?php
							
							$editbutton= '<label> Dezi Bonus&nbsp;:&nbsp;</label> 0$'. @$deziBonus.'&nbsp;<i class="fa fa-pencil-square-o credit-edit" aria-hidden="true"></i>';
							$viewonly= '<label> Dezi Bonus : 0$'. @$deziBonus.'';
							 ?>
							@if(empty($loggedInUserPermission))
									{!!$editbutton!!}
								@elseif(!empty($loggedInUserPermission))
								
								@foreach($loggedInUserPermission as $userPermission)
																	
									@if($userPermission->module_slug=="dezi_credit" && $userPermission->edit_permission==1)
										{!!$editbutton!!}	
									@elseif($userPermission->module_slug=="dezi_credit" && $userPermission->view_permission==1)
										{!!$viewonly!!}
									@endif
								@endforeach
							@endif
						</div><!-- Close Form Group-->	
								
						<div class="callout callout-info credit-edit-callout">
							<h4>Add Some Dezi Credit To This User</h4>
							<p>Follow the steps to continue to payment.</p>
							<span class="close-me">X</span>

							{!! Form::open(array("url" => "foo/bar", "id"=> "credit_form", "class" => "credit_form")) !!}
							  <div class="box-body">
								  <div class="form-group">
									  <label for="credit_amount">Enter Credit Amount</label>
									  <input type="text" name="credit_amount" class="form-control" id="credit_amount" placeholder="Enter The Credit Amount" required="required">
									<span style="display:none;" id="errorcredit"></span>
								  </div>
								</div><!-- /.box-body -->

								<div class="box-footer">
									<input type="hidden" name="user_id" value="{!! $user->id !!}">
									<input type="hidden" name="submit_credit_form" value="1">
									<button type="submit" class="btn btn-primary">Submit</button>
								</div>
							</form>
						</div>
						
					</div><!-- COL-MD-4 CLOSE -->
					
		</div>
        
        
        <div class="clearfix"></div>
	
		<div class="row">
			<hr/>
			 <div class="clearfix"></div>
			<div class="row">
				<div class="col-md-6">
				<?php 
					if (!empty($driver_records)) {
						$qas = json_decode($driver_records);
						  $i=1;
						  foreach ($qas as $k=>$qa) {
							 
							   ?>
							 <div class="col-md-12"> 
								<label>Question : <?php echo $qa->question; ?></label>
							 </div>	
							<div class="col-md-12"> 
							  <?php 
								if($qa->answer == '1'){ 
									echo "Answer : YES"; 
								}else if( $qa->answer == '0' ){ 
									echo "Answer : NO"; 
								}else{ 
									echo 'Answer : '.$qa->answer; 
								} ?>
							</div>
						  
						  

						<?php  if($i%2==0){echo '</div><div class="col-md-6">';} $i++;
						} 
					} ?>
					
					</div>
					<!--<ul class="glyp">

						<span class="glyphicon glyphicon-file"></span>
						<li>
						  <a href="{!! asset(@$license_verification) !!}" target="_blank">Driver License</a>
						  <a href="{!! asset(@$license_verification) !!}" download>Download</a>
						</li>
						<span class="glyphicon glyphicon-file"></span>
						<li>
						  <a href="{!! asset(@$proof_of_insurance) !!}" target="_blank">Proof Of Insurance</a>
						  <a href="{!! asset(@$proof_of_insurance) !!}" download>Download</a>
						</li> 
				  </ul>-->
				  
			</div>
			<br/>
			 <div class="clearfix"></div>
		</div> 
		<!--ALL TABLES START--->
		<!-- User History START-->
	  		
		<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>Ride History</b></h4>
			  			<div class="box_search"> 
					 		<!--div class="form-group form_fl">
								<label> Search </label> 
							 	<br>
						 		<input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
							</div--> 
					
							<div class="form-group form_fl">
								<label> Start Date </label> <br>
								<input type="text" Placeholder="Start Date" id="rideStartDate"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<div class="form-group form_fl" id="end">
								<label>End Date</label> <br>
								<input type="text" Placeholder="End Date"  id="rideEndDate"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							<div class="form-group form_fl" id="end">
								<label>Ride Status</label> <br>
								<select name="rideStatus" class="form-control" id="rideStatus">
									<option value="">--Select Status--</option>
									<option value="1">In process</option>
									<option value="2">Complete</option>
									<option value="3">Ride Cancelled</option>
									<option value="4">No Response</option>
									<option value="5">Cancel Ride Request</option>
									<option value="6">Ride Cancel But No Bill</option>
								</select>
								
							</div> 
							
							<!--div class="form-group mt15 pull-right mr30">
								<button class="btn btn-default color-blue"> Download </button>
							</div--> 	
						</div>
		              	<table class="table" id="driverRideHisotry" style="width:100%">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <th>Ride Id</th>
				                    <th>TimeStamp</th>
				                    <th>Passenger Name</th>
				                    <th>Passenger ID</th>
				                    <th>Reported Issues</th>
				                    <th>Status</th>
				                    <th>Billing Amount</th>
				                    <th>Action</th>
			                  	</tr>
			                </thead>
			               
		              	</table>
					</div> 
          		</div>
        	</div>
      	</div>
	
		<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>Issues History</b></h4>
			  			<div class="box_search"> 
					 		<!--div class="form-group form_fl">
								<label> Search </label> 
							 	<br>
						 		<input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
							</div--> 
					
							<div class="form-group form_fl">
								<label> Start Date </label> <br>
								<input type="text" Placeholder="Start Date" id="issueStartDate"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<div class="form-group form_fl" id="end2">
								<label>End Date</label> <br>
								<input type="text" Placeholder="End Date" id="issueEndDate">
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<!--div class="form-group mt15 pull-right mr30">
								<button class="btn btn-default color-blue"> Download </button>
							</div--> 	
						</div>
		              	<table class="table" id="IssuesHisotry" style="width:100%">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <th>Ride Id</th>
				                    <th>TimeStamp</th>
				                    <th>Passenger Name</th>
				                    <th>Passenger ID</th>
				                    <th>Issues Type</th>
				                    <th>Status</th>
				                    <th>Billing Amount</th>
				                    <th>Action</th>
			                  	</tr>
			                </thead>
			               
		              	</table>
					</div> 
          		</div>
        	</div>
        </div>
      	
		<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>Earning History</b></h4>
			  			<div class="box_search"> 
					 		<!--div class="form-group form_fl">
								<label> Search </label> 
							 	<br>
						 		<input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
							</div--> 
					
							<div class="form-group form_fl">
								<label> Start Date </label> <br>
								<input type="text" Placeholder="Start Date" id="earningStartDate"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<div class="form-group form_fl" id="end3">
								<label>End Date</label> <br>
								<input type="text" Placeholder="End Date" id="earningEndDate">
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<!--div class="form-group mt15 pull-right mr30">
								<button class="btn btn-default color-blue"> Download </button>
							</div--> 	
						</div>
		              	<table class="table" id="EarningHisotry" style="width:100%">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <th>Ride Id</th>
				                    <th>TimeStamp</th>
				                    <th>Passenger Name</th>
				                    <th>Passenger ID</th>
				                    <th>Status</th>
				                    <th>Billing Amount</th>
				                    <th>Earning Amount</th>
				                    <th>Action</th>
			                  	</tr>
			                </thead>
			               
		              	</table>
					</div> 
          		</div>
        	</div>
        	</div>
    		
		<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>Dezi Bonus</b></h4>
			  			<div class="box_search"> 
					 		<!--div class="form-group form_fl">
								<label> Search </label> 
							 	<br>
						 		<input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
							</div--> 
					
							<div class="form-group form_fl">
								<label> Start Date </label> <br>
								<input type="text" Placeholder="Start Date" id="bonusStartDate"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<div class="form-group form_fl" id="dezibns">
								<label>End Date</label> <br>
								<input type="text" Placeholder="End Date" id="bonusEndDate">
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div>  
							
								
						</div>
		              	<table class="table" id="BonusHistory" style="width:100%">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <th>Referred User Id</th>
				                    <th>TimeStamp</th>
				                    <th>Transfer Type</th>
				                   
				                    <th>Amount</th>
				                
				                    <th>Admin User</th>
				                   
			                  	</tr>
			                </thead>
			               <tbody>
			               	<tr>
			               		<td></td>
			               	</tr>
			               </tbody>
		              	</table>
					</div> 
          		</div>
        	</div>
        	</div>
			
		<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>Hour Log</b></h4>
			  			<div class="box_search"> 
					 		<!--div class="form-group form_fl">
								<label> Search </label> 
							 	<br>
						 		<input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
							</div--> 
					
							<div class="form-group form_fl">
								<label> Start Date </label> <br>
								<input type="text" Placeholder="Start Date" id="hourlogStartDate"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<div class="form-group form_fl" id="end5">
								<label>End Date</label> <br>
								<input type="text" Placeholder="End Date" id="hourlogEndDate">
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<!--div class="form-group mt15 pull-right mr30">
								<button class="btn btn-default color-blue"> Download </button>
							</div--> 	
						</div>
		              	<table class="table" id="HourLogTable" style="width:100%">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <th>Date</th>
				                    <th>Login Time</th>
				                    <th>Logout Time</th>
				                    <th>Duration</th>
			                  	</tr>
			                </thead>
			               
		              	</table>
					</div> 
          		</div>
        	</div>
        	</div>
			
		<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>Documents</b></h4>
			  			<div class="box_search"> 
					 		<!--div class="form-group form_fl">
								<label> Search </label> 
							 	<br>
						 		<input type="text" class="m2" Placeholder="Ride ID/Driver Name/Driver ID"/>
							</div--> 
					
							<div class="form-group form_fl">
								<label> Start Date </label> <br>
								<input type="text" Placeholder="Start Date" id="documentStartDate"/>
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<div class="form-group form_fl" id="end6">
								<label>End Date</label> <br>
								<input type="text" Placeholder="End Date" id="documentEndDate">
								<i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
							</div> 
							
							<!--div class="form-group mt15 pull-right mr30">
								<button class="btn btn-default color-blue"> Download </button>
							</div--> 	
						</div>
		              	<table class="table" id="documentTable" style="width:100%;text-align:left;">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <!--<th>Make</th>
				                    <th>Model</th>-->
				                    <th  class="no-sort" >License No.</th>
				                    <!--<th>Transmission</th>-->
				                    <th  class="no-sort" >License</th>
				                    <th  class="no-sort" >Insurance</th>
				                    <!--<th>Actions</th>-->
			                  	</tr>
			                </thead>
			               
		              	</table>
					</div> 
          		</div>
        	</div>
        	</div>
			
		<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12" id="bankbtn"><b>Bank Details</b></h4>
			  			
		              	<table class="table" id="bankdetTable" style="width:100%">
			                <thead>
			                  	<tr>
				                    <th>S.No</th>
				                    <th>Bank Name</th>
				                    <th>Account No.</th>
				                    <th>Routing No.</th>
				                    <th>Branch</th>
				                    
			                  	</tr>
			                </thead>
			               
		              	</table>
					</div> 
          		</div>
        	</div>
        	</div>
     <!-- User History END -->
		<!--ALL TABLES END--->
	</section>

@stop
@stop
<style type="text/css">
.profile_dl label{text-align:left; width:110px;}
.border{border:1px solid #ff0000;}
.main_heading{width:100%; margin:0px; padding:0px;}
.main_heading h3{background:#238BCC; text-align:center; font-weight:normal; font-size:22px; color:#ffffff; padding:10px 0;}
hr {
  border-top: 2px solid #3c8dbc !important;
}

</style>
