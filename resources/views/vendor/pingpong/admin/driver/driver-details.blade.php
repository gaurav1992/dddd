@extends($layout)
@section('title', 'Driver Details')
@section('customjavascript')
<script>
var driverRideList = "{!! route('ajaxRideList') !!}";
var homeUrl= "{!! route('index') !!}";
</script>
<script src="{!! admin_asset('js/driverList.js') !!}" type="text/javascript"></script>
@stop
@section('content')

                <!-- Main content -->
        <section class="content">	
		                    <div class="row">
                            <div class="box">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-lg-3 m-15 col-md-2 col-sm-2 col-xs-12  ">
                                            <div class="browse-image text-center">
											 <?php if(empty($user->profile_pic)){ $user->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
													$user->profile_pic = "/".$user->profile_pic;} ?>
													{!! HTML::image($user->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
                                            </div>
											<ul class="profile_dl text-center">
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
												<p class="rad-heading"> <label> Gender: </label> {{$user->gender}} &nbsp;&nbsp; {{ (date('Y') - date('Y',strtotime($user->dob))) }} years</p>
												</li>	
											
											</ul>
                                        </div>
                                        <div class="col-lg-9 m-15 col-md-3 col-sm-3 col-xs-12">
					
										<div class="row"> 
                                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                                                <div class="form-group">
													<p class="rad-heading"> <label> User Type : </label> @if($user->role_id=='4') Passenger + Driver  @endif <a href="/admin/driver/new-applicant-detail/{{$user->id}}"> View Drive Profile </a> </p>
												</div>
												
												<div class="form-group">
													<p class="rad-heading"> <label> User ID : </label>  {{$user->unique_code}} </p>
												</div>
												
												<div class="form-group">
													<p class="rad-heading"> <label> Status : </label> @if($user->active=="1") {!! 'Actived' !!} @else 'De-Activated' @endif </p>
												</div>
                                            </div>
                                            <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12 text-center">
                                                <div class="form-group">
																								
													<a href="#" class="btn-default btn"> Suspend User Account</a>
												</div>
												
												<div class="form-group">
													<p class="rad-heading"> <label> Join Date : </label>  {!!  date('m-d-Y', strtotime($user->created_at)) !!}  </p>
												</div>
												
												<div class="form-group">
													<p class="rad-heading"> <label> Dezi credit : </label> $10 &nbsp; <i class="fa fa-pencil-square-o" aria-hidden="true"></i>   </p>
												</div>
                                            </div>

                                        </div> 
										
										
											<div class="row"> 
												<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
													<div class="form-group">
														<p class="rad-heading text-center"> <label class="label_1"> <?php echo $totalRide->total_ride; ?></label> </br> Rides Given </p>
													</div>
												</div>
												<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
													<div class="form-group">
													<p class="rad-heading text-center"> <label class="label_1"> 2 </label>  </br> N/A </p>
													</div>
												</div>
													<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
														<div class="form-group">
															<p class="rad-heading text-center m-10"> <label>02-02-16, 800 hrs: </label> </br> Last Rides </p>
														</div>
												</div>
												<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
														<div class="form-group">
														<p class="rad-heading text-center"> <label class="label_1"> N/A</label>  </br> Bill Cleared </p>
														</div>
												</div>
											   <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
													<div class="form-group">
														<p class="rad-heading text-center "> <label class="label_1"> N/A</label> </br> Pending Bills </p>
													</div>
												</div>
											</div>
									
                                    </div>

                                </div>

                                </div>

                            </div>
                        </div>
						
						
		<!-- favourite place -->
	  		
		<div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
		  
		
            <div class="table-responsive">
			
				
					
					<div class="form-group mt15 pull-right mr30">
						<a class="btn btn-default color-blue" href="/deziNow/admin/driverchangelog/{{$id}}"> Change Log </a>
					</div> 
				
           
			</div> 
          </div>
        </div>
      </div>				

		
	  
	  <!-- favourite place -->
	  		
		<div class="row">
        <div class="col-md-12 pad0">
          <div class="box">
		  
		
            <div class="table-responsive">
			
			
			<h4 class="title-12"><b>Ride History</b></h4>
			  <div class="box_search"> 
					 <input type="hidden" name="id" id="driverid" value="{{$id}}">
					
					<div class="form-group form_fl">
						<label> Search </label> 
						 
						<br>
						 <input type="text" class="m2" id="driver_detail_rideid" Placeholder="Ride ID/Pass Name/Pass ID"/>
					</div> 
					
					<div class="form-group form_fl">
					
						<label> Time Stamp </label> <br>
						 
						 <input type="text"  Placeholder="Ride ID/Driver Name/Driver ID"/>
						 <i aria-hidden="true" class="fa fa-calendar custom_cal"></i>
					</div> 
										
					<div class="form-group mt15 pull-right mr30">
					<button class="btn btn-default color-blue"> Download </button>
					</div> 	
				</div>
              <table class="table" id="rideHistory">
                <thead>
                  <tr>
                    <th>Ride ID</th>
                    <th>Timesstamp</th>
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
        </section>

@stop
@stop
