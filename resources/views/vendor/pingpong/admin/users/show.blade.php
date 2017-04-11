
@extends($layout)
@section('title', 'passenger details')
@section('customjavascript')
<script>
  var deleteCarUrl = "{!! route('carDelete') !!}";
  var suspendUrl = "{!! route('suspend') !!}";
  var carUrl = "{!! route('carFunction') !!}";

  var FavPlaceUrl = "{!! route('FavplaceFunction') !!}";
  var Accdetail = "{!! route('AccountDtl') !!}";
  var driverRideList= "{!! route('ridehistory') !!}";
  var isuueHistoryList = "{!! route('driverIssueAjax') !!}";
  var DzCreditUrl = "{!! route('DzCreditAjax') !!}";
  //single passanger view
  var indexUrl= "{!! route('passengerAjax') !!}";
  var homeUrl= "{!! route('index') !!}";
  var testAZ09= "{!! route('testAZ09') !!}";
  var pasangerIssueHistory= "{!! route('pasangerIssueHistory') !!}";
  var pasangerPaymentHistoryURL= "{!! route('pasangerPaymentHistory') !!}";
  var pasangerActionURL= "{!! route('pasangerAction') !!}";
  var userCreditURL= "{!! route('userCredit') !!}";
  var carsEditURL= "{!! route('editCarfunc') !!}";
 
  var userid="{!! $id!!}";
  //Passenger Dezicredit Form
  $('#credit_form').on('submit', function(e) { //use on if jQuery 1.7+
        e.preventDefault();  //prevent form from submitting
        
        var creditData = $("#credit_form").serialize();

        console.log(creditData); //use the console for debugging, F12 in Chrome, not alerts

      jQuery.ajax({
          type : "post",
          dataType : "json",
          url : userCreditURL,
          data : creditData,
           
          //if using the form then use this
          //data : serializedData,

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

  $("#AZ09startDate, #AZ09endDate, #issueStartDate, #issueEndDate, #payHisStartDate, #payHisEndDate").datepicker();
   validateDateRange('AZ09startDate','AZ09endDate');
    $(document).on('change', '#AZ09startDate, #AZ09endDate', function () {
      $(this).datepicker("hide");
       testAZ09Table.ajax.reload();
    });
</script>
<script src="{!! admin_asset('js/passenger.script.js') !!}" type="text/javascript"></script>

@stop
<style>
#DzCreditTable td { text-align: center}

</style>
@section('content')

  <!-- Main content -->
  <section class="content"> 
    
    <div class="row">
      <div class="box">

        <div class="box-body">
          <div class="row">
            <h3 class='text-light-blue text-center'>Single Passenger Details </h3>

              <div class="col-lg-3 m-15 col-md-2 col-sm-2 col-xs-12  ">
                <div class="browse-image text-center">

                 <?php if(empty($user->profile_pic)){ $user->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
                    $user->profile_pic = $user->profile_pic;} ?>
                 {!! HTML::image($user->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
                                  
                </div>

                <ul class="profile_dl ">
                  <li><p class="rad-heading"> <label> Full Name : </label> {{$user->first_name .' '. $user->last_name }} </p></li>                
                  <li><p class="rad-heading"> <label> Email : </label> {{$user->email}} </p></li>                  
                  <li><p class="rad-heading"> <label> Phone : </label> {{$user->contact_number}}</p></li>                                         
                  <li><p class="rad-heading"> <label> DOB : </label>  @if($user->dob==NULL) {!! $user->dob="N/A" !!} @else {!!  date('M j, Y', strtotime($user->dob)) !!} @endif </p></li>
                  <li><p class="rad-heading"> <label> Anniversary: </label> @if($user->anniversary=="0000-00-00") {!! $user->anniversary="N/A" !!} @else {!!  date('M j, Y', strtotime($user->anniversary)) !!} @endif</p></li>
                  
                   @if($user->gender != "") 
                  
					<!--<li><p class="rad-heading"> <label> Gender: </label> {{$user->gender}} &nbsp;&nbsp; {{$age}} years</p></li> -->
                   @endif 
                  
                </ul>
              </div>
              
              <div class="col-lg-9 m-15 col-md-3 col-sm-3 col-xs-12">
  
                <div class="row"> 
                  <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading">
                       
                        <label> User Type : </label> @if($user->role_id=='3') Passenger @else Driver  @endif 
                        @if($driver==true) + Driver <a href="<?php echo url(). "/admin/driver/driver-detail/".base64_encode(convert_uuencode($user->user_id));?>">&nbsp;View Driver Profile </a>@endif 
                      </p>
                    </div>
                  
                    <div class="form-group">
                      <p class="rad-heading"> <label> User ID : </label>{{$user->unique_code}}</p>
                    </div>
                  
                    <div class="form-group">
                      <p class="rad-heading statusClass"> <label> Status : </label> {{$user->active}} </p>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 text-center">
                    
                    <!--Subadmin Permission Code Start-->
                    <?php 
						$loggedInUserPermission = Session::get('userPermissions'); 
						if(empty($loggedInUserPermission)){
						?>
							 <!-- jQuery Action -->
								<div class="form-group">
								@if($user->active=='Suspended')
								<span> <a href='javascript:void(0);' class='btn  btn-success  passenger_Active' data-action= 'passenger_Active' data-userid="{!! $id!!}" >Activate</a></span>	
								@else
								<span><a  href='javascript:void(0);' class='btn btn-danger  driver_suspend' data-action= 'driver_suspend' data-userid="{!! $id!!}">Suspend</a> </span>	
								@endif
								</div>
							<!-- /jQuery Action -->
						<?php 
						}else if(!empty($loggedInUserPermission)){
						?>		
						<?php	
						
							foreach($loggedInUserPermission as $userPermission){
										
								if($userPermission->module_slug=="passengers" && $userPermission->edit_permission==1){
								?>
									 <!-- jQuery Action -->
										<div class="form-group">
										@if($user->active=='Suspended')
										<span> <a href='javascript:void(0);' class='btn  btn-success  passenger_Active' data-action= 'passenger_Active' data-userid="{!! $id!!}" >Activate</a></span>	
										@else
										<span><a  href='javascript:void(0);' class='btn btn-danger  driver_suspend' data-action= 'driver_suspend' data-userid="{!! $id!!}">Suspend</a> </span>	
										@endif
										</div>
									<!-- /jQuery Action -->
								<?php
								}
							}
							?>
							
						<?php 
						} 
					?>
					<!--Subadmin Permission Code Start-->
                                             
                    <div class="form-group">
                      <p class="rad-heading"> <label> Join Date : </label>  {!!  date('m-d-Y', strtotime($user->created_at)) !!}  </p>
                    </div>
                    
                    
                    <div class="form-group az-relative">
                      <p class="rad-heading"> <label> Dezi credit : </label> 
                      <strong>$<?php echo $view_data['deziCredit']; ?> &nbsp; </strong>
						<!--Subadmin Permission Code Start-->
						<?php 
						$loggedInUserPermission = Session::get('userPermissions'); 
						if(empty($loggedInUserPermission)){

						?>
							
              @if($user->active !='Suspended')
                
                 <i class="fa fa-pencil-square-o credit-edit" aria-hidden="true"></i>
                @endif
              
							
						<?php 
						}else if(!empty($loggedInUserPermission)){
						?>		
						<?php	
						
							foreach($loggedInUserPermission as $userPermission){
										
								if($userPermission->module_slug=="dezi_credit" && ($userPermission->edit_permission==1)){
								?>
									  @if($user->active !='Suspended')
                       <i class="fa fa-pencil-square-o credit-edit" aria-hidden="true"></i>
                   @endif
								<?php
								}
							}
							?>
							
						<?php 
						} 
					?>
					<!--Subadmin Permission Code Start-->
                       
                      </p>
                    </div>

                    <div class="callout callout-info credit-edit-callout">
                        <h4>Add Some Dezi Credit To This User</h4>
                        <p>Follow the steps to continue to payment.</p>
                        <span class='close-me'>X</span>

                        {!! Form::open(array('url' => 'foo/bar', 'id'=> 'credit_form', 'class' => 'credit_form')) !!}
                          <div class="box-body">
                              <div class="form-group">
                                  <label for="credit_amount">Enter Credit Amount</label>
                                  <input type="text" name='credit_amount' class="form-control" id="credit_amount" placeholder="Enter The Credit Amount" required='required'>
                              </div>
                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <input type='hidden' name='user_id' value="{!! $id !!}">
                                <input type='hidden' name='submit_credit_form' value='1'>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                  
                  </div>
                </div>
              
                <div class="row"> 
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center"> <label class="label_1" id="ridesTaken"> </label> </br> Rides Taken </p>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                    <p class="rad-heading text-center"> <label class="label_1" id="IssuesRaised"> </label>  </br> Issues Raised </p>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center m-10"> <label>{{ @$lastRideDate->maxDate  }} </label> </br> Last Rides </p>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                      <div class="form-group">
                        <p class="rad-heading text-center"> <label class="label_1"> ${{ @$blCleared  }}</label>  </br> Bill Cleared </p>
                      </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center "> <label class="label_1"> ${{ $penBills }}</label> </br> Pending Bills </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          
          </div>
        </div>
      </div>
    
      <!-- Passanger Test Ride History -->
      <div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
            <div class="table-responsive">
              <h4 class="title-12"><b>Passenger Ride History</b></h4>
                
                <div class="box_search"> 
                  <div class="form-group form_fl">
                    <label> Start Date </label> <br>
                    <input class="form-control321" type="text" Placeholder="Start Date" readonly id="AZ09startDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                
                  <div class="form-group form_fl" id="end">
                    <label>End Date</label> <br>
                    <input class="form-control321" type="text" Placeholder="End Date" readonly id="AZ09endDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                </div>
                <table class="table" id="testAZ09" style="width:100%">
                  <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Ride Id</th>
                        <th>TimeStamp</th>
                        <th>Driver Name</th>
                        <th>Driver ID</th>
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
        <!--  -->

      <!-- Passanger Test Ride History -->
      <div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
            <div class="table-responsive">
              <h4 class="title-12"><b>Passenger Issue History</b></h4>
                
                <div class="box_search"> 
                  <div class="form-group form_fl">
                    <label> Start Date </label> <br>
                    <input class="form-control321" type="text" Placeholder="Start Date" readonly id="issueStartDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                
                  <div class="form-group form_fl" id="end2">
                    <label>End Date</label> <br>
                    <input class="form-control321" type="text" Placeholder="End Date" readonly id="issueEndDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                </div>
                <table class="table" id="pasangerIssueTable" style="width:100%">
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
        <!--  -->


      <!-- Passanger Test Ride History -->
      <div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
            <div class="table-responsive">
              <h4 class="title-12"><b>Passenger Payment History</b></h4>
                
                <div class="box_search"> 
                  <div class="form-group form_fl">
                    <label> Start Date </label> <br>
                    <input class="form-control321" type="text" Placeholder="Start Date" readonly id="payHisStartDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                
                  <div class="form-group form_fl" id="end3">
                    <label>End Date</label> <br>
                    <input class="form-control321" type="text" Placeholder="End Date" readonly id="payHisEndDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                </div>
                <table class="table" id="pasangerPaymentHistoryTable" style="width:100%">
                  <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Ride Id</th>
                        <th>TimeStamp</th>
                        <th>Driver Name</th>
                        <th>Driver ID</th>
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

		
		
      <!-- Passanger Dezi Credit -->
      <div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
            <div class="table-responsive">
              <h4 class="title-12"><b>Passenger Dezi Credit</b></h4>
                
                <div class="box_search" > 
                  <div class="form-group form_fl">
                    <label> Start Date </label> <br>
                    <input class="form-control321" type="text" Placeholder="Start Date" readonly id="DzStartDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
                
                  <div class="form-group form_fl">
                    <label>End Date</label> <br>
                    <input class="form-control321" type="text" Placeholder="End Date" readonly id="DzEndDate"/>
                    <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
                  </div> 
				  <div class="form-group form_fl " id="daterange">
                    <label>Transaction Type</label> <br>
                    <select class="form-control789" name="TrnTYp" id="TrnTYp">
					<option value=''> Select Transaction Type</option>
					<option value="CR"> Credit </option>
					<option value="DR"> Debit </option>
					</select>
                  
                  </div>
                </div>
                <table class="table" id="DzCreditTable" style="width:100%">
                  <thead>
                      <tr>
                        <th>S.No</th>
                        <th>TimeStamp</th>
                        <th>Transfer Type</th>
                        <th>Ride ID</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Admin User</th>
                        
                      </tr>
                  </thead>
                </table>
              </div> 
            </div>
          </div>
        </div>
      <!-- Passanger Car Details -->
      <div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
            <div class="table-responsive">
              <h4 class="title-12"><b>Passenger Cars Detail</b></h4>
                 <div class="box_search" > 
                  <div class="carTable">
                   
                  </div> 
                
				 
                </div>
                <table class="table" id="pasangerCarTable" style="width:100%">
                  <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>License No.</th>
                        <th>Transmission</th>
                        <th>Actions</th>
                      </tr>
                  </thead>      
                </table>
              </div> 
            </div>
          </div>
        </div>

      <!-- Passanger favourite Places -->
      <div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
            <div class="table-responsive">
              <h4 class="title-12"><b>Passenger favorite Places</b></h4>
                <div class="box_search" > 
                  <div class="favplaceTable">
                   
                  </div> 
                
				 
                </div>
                <table class="table" id="PfavPlace" style="width:100%">
                  <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Add date</th>
                        <th>Name</th>
                        <th>Address</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div> 
            </div>
          </div>
        </div>
	
   <!-- Passanger Account detail -->
      <div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
            <div class="table-responsive">
              <h4 class="title-12"><b>Passenger Account Information</b></h4>
                <div class="box_search" > 
                  <div class="AccDtlTable">
                   
                  </div> 
                
         
                </div>
                <table class="table" id="AccDtl" style="width:100%">
                  <thead>
                      <tr>
                        <th>S.No</th>
                        <th>Account Type</th>
                        <th>Card Type</th>
                        <th>Card</th>
                        <th>Expiration</th>
                        <th>Default</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div> 
            </div>
          </div>
        </div>
  
	
	  <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog" >
    <div class="modal-dialog" style="z-index:1081">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Car Details</h4>
        </div>
        <div class="modal-body">
         <form name="carEdit" id="carEdit">
            <div class="form-group">
              <label for="recipient-name" class="form-control-label">Make:</label>
              <input type="text" class="form-control" id="make" name="make">
              <input type="hidden" class="form-control" id="id" name="id">
              <input type="hidden" class="form-control" id="_token" name="_token" value="{!! csrf_token() !!}">
            </div>
            <div class="form-group">
              <label for="message-text" class="form-control-label">Model:</label>
               <input type="text" class="form-control" id="Model" name="Model">
            </div> 
			<div class="form-group">
              <label for="message-text" class="form-control-label">Transmission:</label>
			  <select id="Transmission" name="Transmission"  class="form-control">
			  <option value=""> Select Transmission  </option>
			  <option value="automatic"> Automatic  </option>
			  <option value="manual"> Manual  </option>
			
              
			  </select>
			</div>
         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  <button type="submit" class="btn btn-primary savecarDet">Save</button>
		   </form>
        </div>
      </div>
      
    </div>
  </div>
  

  </section>

@stop

      <!-- Dezi Credit Amount -->
      <!-- /Dezi Credit Amount -->

@stop

