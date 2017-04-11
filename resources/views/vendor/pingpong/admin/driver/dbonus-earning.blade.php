
@extends($layout)
@section('title', 'Driver Bonuses')
@section('customjavascript')
<script>

  var dzbnsEarningAjax= "{!! route('dzbnsEarningAjax') !!}";
  var driverId= "{!! @$uid !!}";
 
  //$("#azNotificationModal").modal('show');

</script>
<script src="{!! admin_asset('js/driverList.js') !!}" type="text/javascript"></script>

@stop

@section('content')

            
        <section class="content-header">
            <h1 style="text-align:center;">Single Driver Dezibonus Details</h1>
			<div style="clear:both"></div>

        </section>
            <!-- Main content -->
        <section class="content">	
		
            <div class="row">
            	<div class="box">
               	 	<div class="box-body">
                    	<div class="row">
                        	<div class="col-lg-4 m-15 col-md-2 col-sm-2 col-xs-12  ">
							
                            	<div class="browse-image text-center">
							 <?php if(empty($referredByData->profile_pic)){ $referredByData->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
									$referredByData->profile_pic = $referredByData->profile_pic;} ?>
							 {!! HTML::image($referredByData->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
							
                                
                            	</div>
								<ul class="profile_dl text-center">
									<li>
									<p class="rad-heading"> <label> Referred  Id : </label> {!! @$referredByData->unique_code !!}</p>
									</li>
									<li>
									<p class="rad-heading"> <label> Driver Name : </label> {!! $referredByData->fullname !!}</p>
									</li>
								
									<li>
									<p class="rad-heading"> <label> Email : </label> {!! $referredByData->email !!} </p>
									</li>
									
									<li>
									<p class="rad-heading"> <label> Phone : </label>{!! $referredByData->contact_number !!}</p>
									</li>												
									
									<li>
									<p class="rad-heading"> <label> DOB : </label> {!! date('m/d/Y', strtotime($referredByData->dob)) !!} </p>
									</li>
									
									
									<li>
									<p class="rad-heading"> <label> Anniversary: </label>{!! date('m/d/Y', strtotime($referredByData->anniversary)) !!} </p>
									</li>

									<li>
									<p class="rad-heading"> <label> Gender: </label> {!! $referredByData->gender !!}</p>
									</li>	
								
								</ul>
	                        </div>
	                           <div class="col-lg-4 m-15 col-md-2 col-sm-2 col-xs-12  ">
							   
                            	<div class="browse-image text-center">
							 <?php if(empty($DrByData->profile_pic)){ $DrByData->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
									$DrByData->profile_pic = $DrByData->profile_pic;} ?>
							 {!! HTML::image($DrByData->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
							
                                
                            	</div>
								<ul class="profile_dl text-center">
									<li>
									<p class="rad-heading"> <label> Driver ID : </label> {!! @$DrByData->unique_code !!}</p>
									</li>
								
									<li>
									<p class="rad-heading"> <label> Full Name : </label> {!! $DrByData->fullname !!}</p>
									</li>
								
									<li>
									<p class="rad-heading"> <label> Email : </label>  {!! $DrByData->email !!}</p>
									</li>
									
									<li>
									<p class="rad-heading"> <label> Phone : </label> {!! $DrByData->contact_number !!}</p>
									</li>												
									
									<li>
									<p class="rad-heading"> <label> DOB : </label>  {!! date('m/d/Y', strtotime($DrByData->dob)) !!}</p>
									</li>
									
									
									<li>
									<p class="rad-heading"> <label> Anniversary: </label> {!! date('m/d/Y', strtotime($DrByData->anniversary)) !!} </p>
									</li>

									<li>
									<p class="rad-heading"> <label> Gender: </label> {!! $DrByData->gender !!}</p>
									</li>	
								
								</ul>
	                        </div>
                        	<div class="col-lg-4 m-15 col-md-3 col-sm-3 col-xs-12">

								<div class="row"> 
									
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
										<div class="form-group text-center">
											<p class="rad-heading"> <label class="label_1">${!! @$refBonus->amount !!}
											</label> </br>  Referral Bonus</p>
										</div>
									</div>
									
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
										<div class="form-group text-center">
											<p class="rad-heading"> <label class="label_1">${!! @$refBonus->amount !!}
											</label> </br> Due as of</p>
										</div>
									</div>
									
									<div style="margin-bottom:60px"></div>
									
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
										<div class="form-group text-center">
										<p class="rad-heading"> <label class="label_1"> {!! @$rideCompleted !!} </label>  </br> Rides Completed </p>
										</div>
									</div>
									
									<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
											<div class="form-group text-center">
											<p class="rad-heading"> <label class="label_1" style=->{!! @$othersData[0]->distance!!} m  </label>  </br> Total Distance Driven</p>
											</div>
									</div>
									
									<div style="margin-bottom:60px"></div>
									
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group text-center">
											<p class="rad-heading text-center "> <label class="label_1"> -- </label> </br> Payment Recieved</p>
										</div>
									</div>
								   
								   <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group text-center">
											<p class="rad-heading text-center "> <label class="label_1"> -- </label> </br> Tip Recieved</p>
										</div>
									</div>
									
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group text-center">
											<p class="rad-heading text-center "> <label class="label_1"> -- </label> </br> Driver Earning </p>
										</div>
									</div>
								</div>
                    		</div>
                		</div>
                	</div>
				</div>
        	</div>
					<div class="row">
        	<div class="col-md-12 pad0">
         	 	<div class="box">
            		<div class="table-responsive">
						<h4 class="title-12"><b>Referred Driver Details</b></h4>
			  			<div class="box_search"> 
					 									
						</div>
		              	<table class="table" id="refDrDetails">
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



</section>

@stop
@stop
