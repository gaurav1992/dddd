<?php //echo $user->id;exit;?>
@extends($layout)
@section('title', 'Bonus Details')
@section('customjavascript')
<script type="text/javascript">
var driverBonusHistory = "{!! route('bonusHistory') !!}";
</script>
<script src="{!! admin_asset('js/bonusHistory.js') !!}" type="text/javascript"></script>
@stop

@section('content')

                <!-- Main content -->
        <section class="content">	
		
            <div class="row">
            	<div class="box">
               	 	<div class="box-body">
                    	<div class="row">
                        	<div style="border-right: 2px solid" class="col-lg-3 m-15 col-md-2 col-sm-2 col-xs-12  ">
                            	<div class="browse-image text-center">
							 <?php if(empty($driverDetails->profile_pic)){ $driverDetails->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
									$driverDetails->profile_pic = $driverDetails->profile_pic;} ?>
							 {!! HTML::image($driverDetails->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
                                
                            	</div>
								<!-- <ul class="profile_dl text-center"> -->
								<ul class="profile_dl">
									<li>
									<p class="rad-heading"> <label> Full Name : </label> {{$driverDetails->first_name .' '. $driverDetails->last_name }} </p>
									</li>
								
									<li>
									<p class="rad-heading"> <label> Email : </label> {{$driverDetails->email}} </p>
									</li>
									
									<li>
									<p class="rad-heading"> <label> Phone : </label> {{$driverDetails->contact_number}}</p>
									</li>												
									
									<li>
									<p class="rad-heading"> <label> DOB : </label> {!!  date('m-d-Y', strtotime($driverDetails->dob)) !!} </p>
									</li>
									
									
									<li>
									<p class="rad-heading"> <label> Anniversary: </label> @if($driverDetails->anniversary=="0000-00-00") {!! $driverDetails->anniversary="N/A" !!} @else {!!  date('m-d-Y', strtotime($driverDetails->anniversary)) !!} @endif</p>
									</li>

									<li>
									<p class="rad-heading"> <label> Gender: </label> {{$driverDetails->gender}}</p>
									</li>	
								
								</ul>
	                        </div>
	                        <div style="border-right: 2px solid" class="col-lg-3 m-15 col-md-2 col-sm-2 col-xs-12  ">
                            	<div class="browse-image text-center">
							 <?php if(empty($referrerDetails->profile_pic)){ $referrerDetails->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
									$referrerDetails->profile_pic = $referrerDetails->profile_pic;} ?>
							 {!! HTML::image($referrerDetails->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
                                
                            	</div>
								<!-- <ul class="profile_dl text-center"> -->
								<ul class="profile_dl">
									<li>
									<p class="rad-heading"> <label> Full Name : </label> {{$referrerDetails->first_name .' '. $referrerDetails->last_name }} </p>
									</li>
								
									<li>
										<?php if(strlen($referrerDetails->email) < 25){
											$size = "";
										}else{
											$size = "font-size: 10px";
										}
											?>
									<p class="rad-heading"> <label> Email : </label><span style="<?php echo $size; ?>">{{$referrerDetails->email}}</span></p>
									</li>
									
									<li>
									<p class="rad-heading"> <label> Phone : </label> {{$referrerDetails->contact_number}}</p>
									</li>												
									
									<li>
									<p class="rad-heading"> <label> DOB : </label> {!!  date('m-d-Y', strtotime($referrerDetails->dob)) !!} </p>
									</li>
									
									
									<li>
									<p class="rad-heading"> <label> Anniversary: </label> @if($referrerDetails->anniversary=="0000-00-00") {!! $referrerDetails->anniversary="N/A" !!} @else {!!  date('m-d-Y', strtotime($referrerDetails->anniversary)) !!} @endif</p>
									</li>

									<li>
									<p class="rad-heading"> <label> Gender: </label> {{$referrerDetails->gender}}</p>
									</li>	
								
								</ul>
	                        </div>
                        	<div class="col-lg-6 m-15 col-md-3 col-sm-3 col-xs-12">
								<div class="row"> 
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<p class="rad-heading text-center"> <label class="label_1">
												<?php echo $bonusDetails->bonus_amount; ?>
											</label> </br> Referral Bonus </p>
										</div>
									</div>
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
										<p class="rad-heading text-center"> <label class="label_1">
											<?php echo date('Y-m-d',strtotime($bonusDetails->last_ride_date));?>
										</label>  </br> Due as of </p>
										</div>
									</div>
										<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
												<p class="rad-heading text-center"> 
													<label class="label_1"><?php echo $ridesCompletedCount; ?></label> </br> Rides Completed </p>
											</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
											<div class="form-group">
											<p class="rad-heading text-center"> <label class="label_1">
												<?php echo $totalDistance; ?></label>  </br> Total Distance Driven </p>
											</div>
									</div>
									<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<p class="rad-heading text-center "> <label class="label_1">
												<?php echo $payments;?></label> </br> Payments Received </p>
										</div>
									</div>
									<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<p class="rad-heading text-center "> <label class="label_1">
												<?php echo $tipAmount; ?></label> </br> Tips Received </p>
										</div>
									</div>
									<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
										<div class="form-group">
											<p class="rad-heading text-center "> <label class="label_1">
												<?php echo $payments; ?></label> </br> Driver earning </p>
										</div>
									</div>
								</div>
                    		</div>
                		</div>
                		<!-- closing here -->
                	</div>
				</div>
        	</div>
</section>
<section class="content">	
		<?php $currentRoute = Route::current();
			  $params = $currentRoute->parameters();
		?>
            <div class="row">
            	<div class="box">
               	 	<div class="box-body">
                    	<div class="row">
                    		<table id="bonusHistory" data-row-id="<?php echo $params['id'];?>">
                    			
                    			<thead>
                    				<th>S.No</th>
                    				<th>Ride Id</th>
                    				<th>Date Time</th>
                    				<th>Passenger Name</th>
                    				<th>Passenger Id</th>
                    				<th>Status</th>
                    				<th>Billing Amount</th>
                    				<th>Earning Amount</th>
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

<script type="text/javascript">
	/*$(document).ready(function(){
		$('#bonusTable').DataTable({

		});
	});*/
</script>
@stop
@stop
