@extends($layout)
@section('title', 'passenger ride details')
@section('customjavascript')
<script>

  //single passanger view
  var indexUrl= "{!! route('passengerAjax') !!}";
  var callLogsUrl= "{!! route('callLogs') !!}";
  var issueStatusUrl= "{!! route('issueStatus') !!}";
  var homeUrl= "{!! route('index') !!}";
  var CSRF_TOKEN = '<?php echo csrf_token(); ?>';
  var ride_id="{!! $ride_id!!}";
  var userrefundURL= "{!! route('refundAjax') !!}";
  var suspendUrl = "{!! route('suspend') !!}";
  //$("#azNotificationModal").modal('show');

</script>
<script src="{!! admin_asset('js/passenger.script.js') !!}" type="text/javascript"></script>
<script src="{!! admin_asset('js/jspdf.min.js') !!}" type="text/javascript"></script>
<script src="http://mrrio.github.io/jsPDF/dist/jspdf.debug.js"></script>
<script type="text/javascript">
    var doc = new jsPDF();
    var specialElementHandlers = {
        '#editor': function (element, renderer) {
            return true;
        }
    };
	$('#payment_receipt_pdf').hide();
    $('#generate-payment-receipt').click(function (e) {
     
	  
	    $("#payment_receipt_pdf").show();
	  $("#payment_receipt_pdf").removeAttr( 'style' );
	  demoFromHTML();
	  $("#payment_receipt_pdf").hide();
       /* 
			e.preventDefault();
        doc.fromHTML($('#payment_receipt_pdf').html(), 15, 15, {
            'width': 170,
                'elementHandlers': specialElementHandlers
        });
        doc.save('payment-receipt-file.pdf'); */
    });
	
	function demoFromHTML() {
    var pdf = new jsPDF('p', 'pt', 'letter');
    // source can be HTML-formatted string, or a reference
    // to an actual DOM element from which the text will be scraped.
    source = $('#customers')[0];

    // we support special element handlers. Register them with jQuery-style 
    // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
    // There is no support for any other type of selectors 
    // (class, of compound) at this time.
    specialElementHandlers = {
        // element with id of "bypass" - jQuery style selector
        '#bypassme': function (element, renderer) {
            // true = "handled elsewhere, bypass text extraction"
            return true
        }
    };
    margins = {
        top: 80,
        bottom: 60,
        left: 40,
        width: 522
    };
    // all coords and widths are in jsPDF instance's declared units
    // 'inches' in this case
    pdf.fromHTML(
    source, // HTML string or DOM elem ref.
    margins.left, // x coord
    margins.top, { // y coord
        'width': margins.width, // max width of content on PDF
        'elementHandlers': specialElementHandlers
    },

    function (dispose) {
        // dispose: object with X, Y of the last line add to the PDF 
        //          this allow the insertion of new lines after html
        pdf.save('Payment Receipt.pdf');
    }, margins);
}
	
	
	$(document).ready(function(){
		$("#addCLF").hide();
		$("#addCallLog").click(function(){
			$("#addCLF").toggle();
		});
	});
	var ride_id= "{!! $ride_id !!}";
	/* testAZ09Table */
		var Calltable=$('#Calltable').DataTable({
		  "language": {
			  "paginate": {
			  "previous": "<<",
			  "next":">>"
			
		  }},
		"processing": true,
		 "columnDefs": [ {
	      "targets": 'no-sort',
	      "orderable": false,
		} ],
	    "serverSide": true,
		"ajax": {
		"url": callLogsUrl,
		"data": function ( d ) {	
				d.ride_id = ride_id;
				}
		 },
		 "fnDrawCallback":function(){
	             if(jQuery('table#Calltable td').hasClass('dataTables_empty')){
	                jQuery('.b1').hide();
	             } else {
	               jQuery('.b1').show();
	             }
	        }
	  	
	});
	
	if($('#Calltable').length){	
	var tableTools = new $.fn.dataTable.Buttons(Calltable,{
    	buttons:[{ extend: 'pdf', title: 'CallLogs', text: 'Generate Report', className:'btn btn-default color-blue b1 genRepoBtn',exportOptions:{columns: [0,1,2,3]}}]
	});
		//$(tableTools.container()).insertAfter('#addCallLog');	
		$(tableTools.container()).insertAfter('.cl_log_btn');	
	}
  </script>

@stop

@section('content')

<?php $driver_id = $ride_data['ride_data']->driver_id ?>

  <!-- Main content -->
  <section class="content"> 
    <style>
 .dt-buttons{
    width: 98%;
    text-align: right;
    /* margin-right: 50px !important; */
    float: left !important;
    margin: 10px 0 0 0;
}
.dt-buttons2{
    width: 97.6%;
    text-align: right;
    /* margin-right: 50px !important; */
    float: left !important;
    margin-top: 10px !important;
    margin-bottom: 10px !important;
}
</style>
    <div class="row">
      <div class="box">

        <div class="box-body">
          <div class="row">
            <h3 class='text-light-blue text-center'>Single Ride Details</h3>

              <div class="col-lg-3 m-15 col-md-2 col-sm-2 col-xs-12  ">
                <div class="browse-image text-center">
                 <?php if(empty($user->profile_pic)){ $user->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
                    $user->profile_pic = $user->profile_pic;} ?>
                 {!! HTML::image($user->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}                            
                </div>

                <ul class="profile_dl text-center">
                  <li><p class="rad-heading"> <label> Passenger Name : </label> {{$user->first_name .' '. $user->last_name }} </p></li>                
                  <li><p class="rad-heading"> <label> Email : </label> {{$user->email}} </p></li>                  
                  <li><p class="rad-heading"> <label> Phone : </label> {{$user->contact_number}}</p></li>                                         
                  <li><p class="rad-heading"> <label> DOB : </label> {!!  date('m-d-Y', strtotime($user->dob)) !!} </p></li>
                  <li><p class="rad-heading"> <label> Anniversary: </label> @if($user->anniversary=="0000-00-00") {!! $user->anniversary="N/A" !!} @else {!!  date('m-d-Y', strtotime($user->anniversary)) !!} @endif</p></li>
                  <li><p class="rad-heading"> <label> Gender: </label> {{$user->gender}} &nbsp;&nbsp; {{$age}} years</p></li> 
                </ul>
              </div>
              
              <div class="col-lg-9 m-15 col-md-3 col-sm-3 col-xs-12">
  
                <div class="row"> 
                  <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">

                    <div class="form-group">
                      <p class="rad-heading">
                        <label> User Type : </label> @if($user->role_id=='3') Passenger @else Driver  @endif <a href="#"> View Drive Profile </a>
                      </p>
                    </div>
                 
                    <div class="form-group">
                      <p class="rad-heading"> <label> User ID : </label> {{$user->unique_code}}</p>
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
							<span> <a href='javascript:void(0);' class='btn  btn-success  passenger_Active' data-action= 'passenger_Active' data-userid="{!! $user->user_id !!}" >Activate</a></span>	
							@else
							<span><a  href='javascript:void(0);' class='btn btn-danger  driver_suspend' data-action= 'driver_suspend' data-userid="{!! $user->user_id !!}">Suspend</a> </span>	
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
										<span> <a href='javascript:void(0);' class='btn  btn-success  passenger_Active' data-action= 'passenger_Active' data-userid="{!! $user->user_id !!}" >Activate</a></span>	
										@else
										<span><a  href='javascript:void(0);' class='btn btn-danger  driver_suspend' data-action= 'driver_suspend' data-userid="{!! $user->user_id !!}">Suspend</a> </span>	
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
                        <strong>$<?php echo @$view_data['deziCredit']; ?> &nbsp; </strong>
                      </p>
                    </div>

   
                  </div>
                </div>
              
                <div class="row"> 
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center"> <label class="label_1"> {!! @$ride_data['total_ride_count'] !!}</label> </br> Rides Taken </p>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                    <p class="rad-heading text-center"> <label class="label_1"> @if(!empty(@$ride_data['total_reported_issue'])){!! @$ride_data['total_reported_issue'] - 1 !!} @else 0 @endif </label>  </br> Issues Raised </p>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center m-10"> <label>{!! @$ride_data['last_ride'] !!} </label> </br> Last Rides </p>
                    </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                      <div class="form-group">
                        <p class="rad-heading text-center"> <label class="label_1"> ${!! @$ride_data['bill_cleared'] !!}</label>  </br> Bill Cleared </p>
                      </div>
                  </div>
                  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center "> <label class="label_1"> ${!! @$ride_data['pending_bill'] !!}</label> </br> Pending Bills </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          
          </div>
        </div>
      </div>
    
      <!-- Passanger Test Ride History -->
      <div class="row" id='payment_receipt'>
        
        <!-- ./col -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <i class="fa fa-text-width"></i>
              <h3 class="box-title">Rating And Ride Detail</h3>
              <a href="javascript:void(0)" id='generate-payment-receipt' class="btn-danger btn pull-right genRepoBtn"> Generate Payment Receipt</a> 
            </div>

            <!-- /.box-header -->
            <div class="box-body">
              
              
              <div class="col-md-2 text-center">
                <h4 class="text-light-blue">{!! @$ride_id !!}</h4>
                <h4 class="text-muted">Ride ID</h4><br>
              </div>

              <div class="col-md-3 text-center">
                <h4 class="text-light-blue">{!! @$ride_data['ride_data']->created_at !!}</h4>
                <h4 class="text-muted">Ride Timestamp</h4><br>
              </div>

              <div class="col-md-2 text-center">
                <h4 class="text-light-blue">{!! @$ride_data['ride_data']->driver_id !!}</h4>
                <h4 class="text-muted">Driver Id</h4><br>
              </div>
              <div class="col-md-3 text-center">
                <h4 class="text-light-blue">
                  
                  <?php $driver_name = DB::table('dn_users')->select('dn_users.first_name', 'dn_users.last_name')->where('id', $driver_id)->first(); 
                    echo @$driver_name->first_name.' '.@$driver_name->last_name; ?>

                </h4>
                
                <h4 class="text-muted">Driver Name</h4>
              </div>
              <div class="col-md-2 text-center">
                <h4 class="text-light-blue">
                  @if (@$ride_data['ride_data']->status == 1)In Process
                       <h4 class="text-muted">Ride Status</h4>
                  @elseif (@$ride_data['ride_data']->status == 2)
                      Complete
                       <h4 class="text-muted">Ride Status</h4>
                  @elseif (@$ride_data['ride_data']->status == 3)
                      Ride cancel
                       <h4 class="text-muted">Ride Status</h4>
                  @else
                      No response
                       <h4 class="text-muted">Ride Status</h4>
                  @endif

                </h4>
                
              </div>
            
            </div>

            <div class="box-body">
              @if(empty($ride_data['rating_received']))
                   <div class="col-md-3 text-center">
                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>
                      <h6 class="text-muted">Not Yet Rated</h6><br>
                    <p class="text-muted">Passenger rating Received</p>
                  </div> 
                    <div class="col-md-3 text-center">

                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>
                      <i class="fa fa-fw fa-star-o"></i>

                     <h6 class="text-muted">Not Yet Rated</h6><br>
                   <p class="text-muted">Driver rating Received</p>
                     </div> 
                     
                @endif
              
              @foreach ($ride_data['rating_received'] as $rating)


                  @if ($rating->rate_by == 3)
                      <div class="col-md-3 text-center">

                    @if( $rating->rating == 1 )
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 2)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 3)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 4)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 5)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @else
                      Not Yet Rated
                    @endif
                    <p class="text-muted">Passenger rating Received</p>
                  </div>

                  @elseif ($rating->rate_by == 4)
                     <div class="col-md-3 text-center">
                    @if( $rating->rating == 1 )
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 2)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 3)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 4)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @elseif($rating->rating == 5)
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                      <i class="fa fa-fw fa-star"></i>
                    @else
                      Not Yet Rated
                    @endif
                       <p class="text-muted">Driver rating Received</p>
                     </div>
                
                  @endif

              @endforeach

              <div class="col-md-3">
                <h4 class="text-light-blue">Pick Up Address</h4>
                <h4 class="text-light-muted">
                    <?php 

                      $lat = $ride_data['ride_data']->pickup_latitude;
                      $lng = $ride_data['ride_data']->pickup_longitude;

                      $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
                      $json = @file_get_contents($url);
                      $data=json_decode($json);
                      $status = @$data->status;
                      if(@$status=="OK")
                      {echo $data->results[0]->formatted_address;}
                      else
                      {echo "No Address Location";}
                    ?>
                </h4>
              </div>

              <div class="col-md-3">
                <h4 class="text-light-blue">Drop Address</h4>
                <h4 class="text-light-muted">
                    <?php 

                      $lat = $ride_data['ride_data']->destination_latitude;
                      $lng = $ride_data['ride_data']->destination_longitude;

                      $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
                      $json = @file_get_contents($url);
                      $data=json_decode($json);
                      $status = @$data->status;
                      if($status=="OK")
                      echo $data->results[0]->formatted_address;
                      else
                      echo "No Address Location";
                    ?>
                </h4>
              </div>


              <!-- refund -->
              <div class="row refund-section">
                <?php /*echo "<pre>"; print_r($ride_data['collected_ride_data']); echo "</pre>";*/ ?>
                <div class="col-sm-2 text-center">
                  
                  <h4 class="text-light-blue">Payment Status</h4>
                  <h4 class="text-muted">

                    <?php 
                      if ( @$ride_data['collected_ride_data'][0]->payment_status == 1) { echo "Success"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->payment_status == 0) { echo "No Payment"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->payment_status == 2) { echo "Payment Failed"; } 
                    ?>
                  </h4>
                  
                </div>
                
                <div class="col-sm-3 text-center">
                  <h4 class="text-light-blue">Mode Of Payment</h4>
                  <h4 class="text-muted">
                    <?php 
                      if ( @$ride_data['collected_ride_data'][0]->ride_payment_type == 'paypal_account') 
                      { echo "PayPal"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->ride_payment_type == 'credit_card') 
                      { echo $ride_data['collected_ride_data'][0]->masked_number; }
                      else if ( @$ride_data['collected_ride_data'][0]->ride_payment_type == 'dezicredit') 
                      { echo "Dezi Credit"; }
                      else{
						echo "N/A";
					  }
                    ?>
                  </h4>
                </div>
                
                <div class="col-sm-2 text-center">
                  <h4 class="text-light-blue">Transaction ID</h4>
                  <h4 style='word-wrap:break-word' class="text-muted"><?php if(@$ride_data['collected_ride_data'][0]->TXN_ID !=""){echo @$ride_data['collected_ride_data'][0]->TXN_ID; }else{"N/A";}; ?>
                  </h4>
                </div>
					
				 <!--Subadmin Permission Code Start-->
				<?php 
					$loggedInUserPermission = Session::get('userPermissions'); 
					if(empty($loggedInUserPermission)){
					?>
							
						<div class="col-sm-5">
						  
						  <!-- Mathematical Expression Starts Here -->
						  <div class="az-exp">
							
							<?php 
							  $exp_ride_id = @$ride_data['collected_ride_data'][0]->rideId; 
							  $exp_charge_id = @$ride_data['collected_ride_data'][0]->charge_id; 
							  $driver_level = @$ride_data['collected_ride_data'][0]->driver_level; 
							
							  $expAmount = @$ride_data['collected_ride_data'][0]->amount;
							  $expRefundAmount = @$ride_data['collected_ride_data'][0]->refund_amount;
							  $expRefundTip = @$ride_data['collected_ride_data'][0]->tip_refund;

							  $expTotalAmont = $expAmount-$expRefundAmount-$expRefundTip;
							?>

							<span id='expRideStatus' expRideStatus='<?php echo @$ride_data['collected_ride_data'][0]->payment_status ?>'></span>
							<span id='expRideID' expRideID='<?php echo $exp_ride_id ?>'></span>
							<span id='expChargeID' expChargeID='<?php echo $exp_charge_id ?>'></span>
							<span id='expDriverLevel' expDriverLevel='<?php echo $driver_level ?>'></span>

							<span class="az-exp-row">
							  <span class="az-exp-left">Ride Amount</span>
							  <span expAmount='{{ $expAmount }}' class="expAmount az-exp-right">${{ $expAmount }}</span>
							</span>

							<span class="az-exp-row">
							  <span class="az-exp-left">Add Refund To Ride</span>
							  <span class="az-exp-right"><input type='number' name='exp_refund_amount' value='{{ $expRefundAmount }}' class='expRefundAmount'></span>
							</span>

							<span class="az-exp-row exp-info">
							  <span class="az-exp-left">Dezi Credit Used</span>
							  <span expDeziCredit='<?php echo @$ride_data['collected_ride_data'][0]->deziCredit ?>' class="expDeziCredit az-exp-right">$<?php echo @$ride_data['collected_ride_data'][0]->deziCredit ?></span>
							</span>

							<span class="az-exp-row">
							  <span class="az-exp-left">Tip</span>
							  <span expTip='<?php echo @$ride_data['collected_ride_data'][0]->tip ?>' class="expTip az-exp-right">$<?php echo @$ride_data['collected_ride_data'][0]->tip ?></span>
							</span>

							<span class="az-exp-row">
							  <span class="az-exp-left">Add Refund To Tip</span>
							  <span class="az-exp-right"><input type='number' name='exp_refund_tip_amount' value='{{ $expRefundTip }}' class='expRefundTip'></span>
							</span>

							<span class="az-exp-row exp-result">
							  <span class="az-exp-left"><button type="button" class="expCalTotal text-bold btn btn-block btn-success btn-sm">Grant</button></span>
							  <span class="az-exp-right"><button type="button" class="expShowTotal text-bold btn btn-block btn-default btn-sm">${{ $expTotalAmont }}</button></span>
							  <div class="my-loader"></div>
							</span>

						  </div>
						  <!-- /Mathematical Expression Ends Here -->

						</div>

					<?php 
					}else if(!empty($loggedInUserPermission)){
					?>		
					<?php	
					
						foreach($loggedInUserPermission as $userPermission){
									
							if($userPermission->module_slug=="passengers" && $userPermission->edit_permission==1){
							?>
							<div class="col-sm-5">
						  
								  <!-- Mathematical Expression Starts Here -->
								  <div class="az-exp">
									
									<?php 
									  $exp_ride_id = @$ride_data['collected_ride_data'][0]->rideId; 
									  $exp_charge_id = @$ride_data['collected_ride_data'][0]->charge_id; 
									  $driver_level = @$ride_data['collected_ride_data'][0]->driver_level; 
									
									  $expAmount = @$ride_data['collected_ride_data'][0]->amount;
									  $expRefundAmount = @$ride_data['collected_ride_data'][0]->refund_amount;
									  $expRefundTip = @$ride_data['collected_ride_data'][0]->tip_refund;

									  $expTotalAmont = $expAmount-$expRefundAmount-$expRefundTip;
									?>

									<span id='expRideStatus' expRideStatus='<?php echo @$ride_data['collected_ride_data'][0]->payment_status ?>'></span>
									<span id='expRideID' expRideID='<?php echo $exp_ride_id ?>'></span>
									<span id='expChargeID' expChargeID='<?php echo $exp_charge_id ?>'></span>
									<span id='expDriverLevel' expDriverLevel='<?php echo $driver_level ?>'></span>

									<span class="az-exp-row">
									  <span class="az-exp-left">Ride Amount</span>
									  <span expAmount='{{ $expAmount }}' class="expAmount az-exp-right">${{ $expAmount }}</span>
									</span>

									<span class="az-exp-row">
									  <span class="az-exp-left">Add Refund To Ride</span>
									  <span class="az-exp-right"><input type='number' name='exp_refund_amount' value='{{ $expRefundAmount }}' class='expRefundAmount'></span>
									</span>

									<span class="az-exp-row exp-info">
									  <span class="az-exp-left">Dezi Credit Used</span>
									  <span expDeziCredit='<?php echo @$ride_data['collected_ride_data'][0]->deziCredit ?>' class="expDeziCredit az-exp-right">$<?php echo @$ride_data['collected_ride_data'][0]->deziCredit ?></span>
									</span>

									<span class="az-exp-row">
									  <span class="az-exp-left">Tip</span>
									  <span expTip='<?php echo @$ride_data['collected_ride_data'][0]->tip ?>' class="expTip az-exp-right">$<?php echo @$ride_data['collected_ride_data'][0]->tip ?></span>
									</span>

									<span class="az-exp-row">
									  <span class="az-exp-left">Add Refund To Tip</span>
									  <span class="az-exp-right"><input type='number' name='exp_refund_tip_amount' value='{{ $expRefundTip }}' class='expRefundTip'></span>
									</span>

									<span class="az-exp-row exp-result">
									  <span class="az-exp-left"><button type="button" class="expCalTotal text-bold btn btn-block btn-success btn-sm">Grant</button></span>
									  <span class="az-exp-right"><button type="button" class="expShowTotal text-bold btn btn-block btn-default btn-sm">${{ $expTotalAmont }}</button></span>
									  <div class="my-loader"></div>
									</span>

								  </div>
								  <!-- /Mathematical Expression Ends Here -->

								</div>
	
							<?php
							}
						}
						?>
						
					<?php 
					} 
				?>
				<!--Subadmin Permission Code Start-->
				
              </div>
              <!-- refund -->

            </div>

            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>

        <div id="editor"></div>

        <!-- ./col -->
        
      </div>
        <!--  -->
	
	<!-- CODE START FOR DOC PRINT -->	
	      <!-- Passanger Test Ride History -->
			  <div class="row" id='payment_receipt_pdf'>
				
				<div id="customers">
				<table id="tab_customers" class="table table-striped">
					<colgroup>
						<col width="40%">
							<col width="40%">
								<!--col width="20%">
									<col width="20%"-->
					</colgroup>
					<thead>
						<tr class='warning'>
							<th>Title</th>
							<th>Value</th>
							
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Ride Id</td>
							<td>{!! @$ride_id !!}</td>
							
						</tr>
						<tr>
							<td>Ride Timestamp</td>
							<td>{!! @$ride_data['ride_data']->created_at !!}</td>
							
						</tr>
						<tr>
							<td>Driver Id</td>
							<td>{!! @$ride_data['ride_data']->driver_id !!}</td>
							
						</tr>
						<tr>
							<td>Driver Name</td>
							<td><?php $driver_name = DB::table('dn_users')->select('dn_users.first_name', 'dn_users.last_name')->where('id', $driver_id)->first(); 
                    echo @$driver_name->first_name.' '.@$driver_name->last_name; ?></td>
							
						</tr>
						<tr>
							<td>Ride Status</td>
						<td>@if(@$ride_data['ride_data']->status == 1)In Process @elseif (@$ride_data['ride_data']->status == 2)Complete @elseif (@$ride_data['ride_data']->status == 3)Ride cancel @else No response @endif
						</td>
							
						</tr>
						
						
					<tr>
					<td>Driver rating Received</td>
					<td> @if(empty($ride_data['rating_received']))
						   <div class="col-md-3 text-center">
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <h6 class="text-muted">Not Yet Rated</h6><br>
							
						   </div> 
						@else
						@foreach ($ride_data['rating_received'] as $rating)
					@if ($rating->rate_by == 4)
						<div class="col-md-3 text-center">
						@if( $rating->rating == 1 )
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 2)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 3)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 4)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 5)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						 </div>
                    
						@endif
					@endif
                    @endforeach
					@endif
			 </td>
			</tr>
			
			
			<tr>
					<td>Passenger rating Received</td>
					<td> @if(empty($ride_data['rating_received']))
						   <div class="col-md-3 text-center">
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <i class="fa fa-fw fa-star-o"></i>
							  <h6 class="text-muted">Not Yet Rated</h6><br>
							
						   </div> 
						@else
						@foreach ($ride_data['rating_received'] as $rating)
						@if ($rating->rate_by == 3)
						 <div class="col-md-3 text-center">
						@if( $rating->rating == 1 )
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 2)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 3)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 4)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						@elseif($rating->rating == 5)
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						  <i class="fa fa-fw fa-star"></i>
						
						@endif
						</div>
						@endif
                      
						
						@endforeach
						@endif
			 </td>
			</tr>
			
						<tr>
							<td>Pick Up Address</td>
							<td> <?php 

                      $lat = $ride_data['ride_data']->pickup_latitude;
                      $lng = $ride_data['ride_data']->pickup_longitude;

                      $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
                      $json = @file_get_contents($url);
                      $data=json_decode($json);
                      $status = @$data->status;
                      if(@$status=="OK")
                      {echo $data->results[0]->formatted_address;}
                      else
                      {echo "No Address Location";}
                    ?></td>
							
						</tr>
			
						<tr>
							<td>Drop Address</td>
							<td><?php 

                      $lat = $ride_data['ride_data']->destination_latitude;
                      $lng = $ride_data['ride_data']->destination_longitude;

                      $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
                      $json = @file_get_contents($url);
                      $data=json_decode($json);
                      $status = @$data->status;
                      if($status=="OK")
                      echo $data->results[0]->formatted_address;
                      else
                      echo "No Address Location";
                    ?></td>
							
						</tr>
						
						<tr>
							<td>Payment Status</td>
							<td> <?php 
                      if ( @$ride_data['collected_ride_data'][0]->payment_status == 1) { echo "Success"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->payment_status == 0) { echo "No Payment"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->payment_status == 2) { echo "Payment Failed"; } 
                    ?></td>
							
						</tr>
						
						<tr>
							<td>Mode Of Payment</td>
							<td> <?php 
                      if ( @$ride_data['collected_ride_data'][0]->account_type == 'paypal') { echo "PayPal"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->account_type == 'card') { echo $ride_data['collected_ride_data'][0]->masked_number; }
                    ?></td>
							
						</tr>
						
						<tr>
							<td>Transaction ID</td>
							<td><?php echo @$ride_data['collected_ride_data'][0]->TXN_ID; ?></td>
							
						</tr>
						
			
					</tbody>
				</table>
</div>
			  </div>

				
				<!--  -->
	<!-- CODE START FOR DOC PRINT -->	
    
    <div class="row">
        
        <!-- ./col -->
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <i class="fa fa-text-width"></i>
              <h3 class="box-title">Ride Issue details</h3>
            </div>

            <!-- /.box-header -->
            
            <?php 

              if (!empty($ride_data['ride_issues'])) {
                
                $srNum = 1;

                foreach ($ride_data['ride_issues'] as $issue) { ?>
                  

                    <div class="box-body">

                      <div class="col-md-1 text-center">
                        <h1 class="text-light-blue">{!! $srNum !!}.</h1>
                      </div>

                        <div class="col-md-3 text-center">
                        <h4 class="text-light-blue">Issue Timestamp</h4>
                        <h4 class="text-muted"><?php echo $issue->created_at; ?></h4>
						@if(@$issue->status==0)
					    <label class="btn btn-danger btn-xs stLabel">Pending</label>
						@else 
						<label class="btn btn-success btn-xs stLabel">Addressed </label>
						@endif
						 <!--Subadmin Permission Code Start-->
						<?php 
							$loggedInUserPermission = Session::get('userPermissions'); 
							if(empty($loggedInUserPermission)){
							?>
								<button type="button" data-status="{!! @$issue->status !!}" data-id="{!! @$issue->id !!}" class="btn btn-primary btn-xs statusC">Change Status</button>
							<?php 
							}else if(!empty($loggedInUserPermission)){
							?>		
							<?php	
							
								foreach($loggedInUserPermission as $userPermission){
											
									if($userPermission->module_slug=="passengers" && $userPermission->edit_permission==1){
									?>
										<button type="button" data-status="{!! @$issue->status !!}" data-id="{!! @$issue->id !!}" class="btn btn-primary btn-xs statusC">Change Status</button>
									<?php
									}
								}
								?>
								
							<?php 
							} 
						?>
						<!--Subadmin Permission Code Start-->
						

                      </div>

                      <div class="col-md-3 text-center">
                        <h4 class="text-light-blue">Category</h4>
                        <h4 class="text-muted"><?php echo $issue->main_category; ?></h4>
                         <h4 class="text-light-blue">Addressed Date</h4>
                        <h4 class="text-muted issueDate"><?php echo $issue->updated_at; ?></h4>
                      </div>

                      <div class="col-md-3 text-center">
                        <h4 class="text-light-blue">Sub category</h4>
                        <h4 class="text-muted"><?php echo $issue->sub_category; ?></h4>
                         <h4 class="text-light-blue">Reported By</h4>
                        <h4 class="text-muted"><?php echo $issue->user_type; ?></h4>
                      </div>
                      <div class="col-md-2 text-center">
                        <h4 class="text-light-blue">Message</h4>
                        <h4 class="text-muted"><?php echo $issue->message; ?></h4>
                      </div>
                    
                    </div>
            
            <?php  $srNum++; }

              }   ?>


            <!-- /.box-body -->
          </div>

          <!-- /.box -->
        </div>

        <!-- ./col -->
      </div>

		<!--Call log-->
	<div class="row">
        
        
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border" >
              <i class="fa fa-text-width"></i>
              <h3 class="box-title">Call Logs</h3>
            </div>
			<div class="fl sl_widht w100 col-md-12">
        <div class="dt-buttons2">
			 <!--Subadmin Permission Code Start-->
				<?php 
					$loggedInUserPermission = Session::get('userPermissions'); 
					if(empty($loggedInUserPermission)){
					?>
						<button type="button" class="btn btn-primary"  id="addCallLog">Add more</button>
					<?php 
					}else if(!empty($loggedInUserPermission)){
					?>		
					<?php	
					
						foreach($loggedInUserPermission as $userPermission){
									
							if($userPermission->module_slug=="passengers" && $userPermission->edit_permission==1){
							?>
								<button type="button" class="btn btn-primary"  id="addCallLog">Add more</button>
							<?php
							}
						}
						?>
						
					<?php 
					} 
				?>  </div>
				<em class="cl_log_btn"></em>
				<!--Subadmin Permission Code Start-->	
		
           <div class="clearfix"></div>
			<div class="col-xs-6 blue-back">
				{!! Form::open(array('route' => 'adcalllog','id'=>'addCLF')) !!}
				<div class="form-group">
				{!! Form::hidden('rideId', @$ride_id) !!}
				{!! Form::hidden('Pid', @$user_id) !!}
				{!! Form::label('Passenger Query') !!}
				
				{!! Form::text('pquery', null, 
					array('required', 
						  'class'=>'form-control', 
						  'placeholder'=>'Passenger Query')) !!}
				</div>
				<div class="form-group">
					{!! Form::label('Query Description') !!}
					{!! Form::text('Qdesc', null, 
						array('required', 
							  'class'=>'form-control', 
							  'placeholder'=>'Query Description')) !!}
				</div>
				
			{!! Form::submit('Save') !!}
			{!! Form::close() !!}
			</div>
			</div>
            <div style="clear:both;margin-bottom:20px;"></div>

	<table class="table" id="Calltable" width="100%">
		<thead>
			
			<th>Sr. No</th>
			<th>Call Id</th>
			<th>Passenger Query</th>
			<th>Call Details</th>
			<th>Admin</th>
			<th>Timestamp</th>
			
		</thead>
		
	</table>

           
          </div>

         
        </div>

</div>
	

      </section>

@stop
@stop
