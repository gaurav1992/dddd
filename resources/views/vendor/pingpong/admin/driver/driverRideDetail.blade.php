
@extends($layout)
@section('title', 'Ride Details')
@section('customjavascript')
<script>
  var userrefundURL  = "{!! route('refundAjax') !!}";
  var issueStatusUrl = "{!! route('issueStatus') !!}";
   var callLogsUrl= "{!! route('callLogs') !!}";
  //$("#azNotificationModal").modal('show');
</script>
<script src="http://html2canvas.hertzen.com/build/html2canvas.js"></script>

		<!-- Code editor -->
<script src="https://cdn.jsdelivr.net/ace/1.1.01/noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="{!! admin_asset('js/passenger.script.js') !!}" type="text/javascript"></script>
<script src="{!! admin_asset('js/jspdf.min.js') !!}" type="text/javascript"></script>
<script src="http://mrrio.github.io/jsPDF/dist/jspdf.debug.js"></script>
<script type="text/javascript">
    

    $('#payment_receipt_pdf').hide();
    $('#generate-payment-receipt').click(function (e) {
	  $("#payment_receipt_pdf").show();
	  Report();
	  $("#payment_receipt_pdf").hide();
      
    });
	
	function Report() {
	$('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
	
	var doc = new jsPDF();
	var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
	};
	doc.text(15, 25, "Ride Details"); 
	doc.fromHTML($('#payment_receipt_pdf').get(0), 15, 30, {
        'width': 180,
            'elementHandlers': specialElementHandlers
    });
	$('#divLoading').remove();
	doc.save('invoice');
	
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
    	buttons:[{ extend: 'pdfFlash', text: 'Generate Report', className:'btn btn-default color-blue b1 genRepoBtn',exportOptions:{columns: [0,1,2,3]}}]
	});
		$( tableTools.container()).insertAfter('#cl_genrepo');	
	}


  </script>

@stop

<?php
/* $earningamount='';
if(!empty($totalRecords['paymentdetail'])){
$commisionPercentage=(2 / 100) * $totalRecords['paymentdetail']->amount;
 
$earningamount=round($commisionPercentage*($totalRecords['billingInfo']->miles_charges+$totalRecords['billingInfo']->duration_charges-$totalRecords['paymentdetail']->refund_amount)+$totalRecords['billingInfo']->pickup_fee+$totalRecords['billingInfo']->tip,2);
//echo $earningamount; exit;
}
else{
  $earningamount=0;
} */
?>
@section('content')
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
            
        <section class="content-header">
            <h1 style="text-align:center;">Single Ride Details -Driver View </h1>
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
                  <?php if(empty(@$totalRecords['passengerdetail']->profile_pic)){$profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
                  $profile_pic = $totalRecords['passengerdetail']->profile_pic;} ?>
               {!! HTML::image($profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
                                
                              </div>
                <ul class="profile_dl text-center">
                  <li>
                  <p class="rad-heading"> <label> Passenger Name : </label>{{ @$totalRecords['passengerdetail']->first_name .' '. @$totalRecords['passengerdetail']->last_name }}
					@if(empty(@$totalRecords['passengerdetail']->first_name) && empty(@$totalRecords['passengerdetail']->last_name))
						N/A 
					@endif</p>
                  </li>
                
                  <li>
                  <p class="rad-heading"> <label> Email : </label> @if(empty(@$totalRecords['passengerdetail']->email))
					  N/A
				  @else
				  {!! @$totalRecords['passengerdetail']->email  !!}
				  @endif</p>
                  </li>
                  
                  <li>
                  <p class="rad-heading"> <label> Phone : </label>@if(!empty(@$totalRecords['passengerdetail']->contact_number)) 
					   {!! @$totalRecords['passengerdetail']->contact_number !!}
				  @else
					  N/A
				  @endif</p>
                  </li>                       
                  
                  <li>
                  <p class="rad-heading"> <label> DOB : </label> @if(!empty(@$totalRecords['passengerdetail']->dob)) 
					   {!!  date('m-d-Y', strtotime(@$totalRecords['passengerdetail']->dob)) !!}
				  @else
					  N/A
				  @endif</p>
                  </li>
                  
                  
                  <li>
                  <p class="rad-heading"> <label> Anniversary: </label> @if(@$totalRecords['passengerdetail']->anniversary=="0000-00-00" or empty(@$totalRecords['passengerdetail']->anniversary) ) {!! @$totalRecords['passengerdetail']->anniversary="N/A" !!} @else {!!  date('m-d-Y', strtotime(@$totalRecords['passengerdetail']->anniversary)) !!} @endif</p>
                  </li>

                  <li>
                  <p class="rad-heading"> <label> Gender: </label> {{@$totalRecords['passengerdetail']->gender}} </p>
                  </li> 
                
                </ul>
                          </div>
                             <div class="col-lg-4 m-15 col-md-2 col-sm-2 col-xs-12  ">
                              <div class="browse-image text-center">
               <?php if(empty($totalRecords['driverdetail']->profile_pic)){ $totalRecords['driverdetail']->profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g";} else{
                  $totalRecords['driverdetail']->profile_pic = $totalRecords['driverdetail']->profile_pic;} ?>
               {!! HTML::image(@$totalRecords['driverdetail']->profile_pic, 'a picture', array('class' => 'img-responsive','id'=>'profile_pic')) !!}
                                
                              </div>
                <ul class="profile_dl text-center">
                  <li>
                  <p class="rad-heading"> <label> Driver Name : </label> {{@$totalRecords['driverdetail']->first_name .' '. @$totalRecords['driverdetail']->last_name }} </p>
                  </li>
                
                  <li>
                  <p class="rad-heading"> <label> Email : </label> {{@$totalRecords['driverdetail']->email}} </p>
                  </li>
                  
                  <li>
                  <p class="rad-heading"> <label> Phone : </label>{{@$totalRecords['driverdetail']->contact_number}}</p>
                  </li>                       
                  
                  <li>
                  <p class="rad-heading"> <label> DOB : </label> {!!  date('m-d-Y', strtotime(@$totalRecords['driverdetail']->dob)) !!} </p>
                  </li>
                  
                  
                  <li>
                  <p class="rad-heading"> <label> Anniversary: </label> @if($totalRecords['driverdetail']->anniversary=="0000-00-00") {!! $totalRecords['driverdetail']->anniversary="N/A" !!} @else {!!  date('m-d-Y', strtotime($totalRecords['driverdetail']->anniversary)) !!} @endif</p>
                  </li>

                  <li>
                  <p class="rad-heading"> <label> Gender: </label> {{$totalRecords['driverdetail']->gender}}</p>
                  </li> 
                
                </ul>
                          </div>
                          <div class="col-lg-4 m-15 col-md-3 col-sm-3 col-xs-12">

                <div class="row"> 
                  <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center"> <label class="label_1"> <?php if(!empty($totalRecords['billingInfo'])){echo round($totalRecords['billingInfo']->miles,2);}else{echo "N/A";}?></label> </br> Rides Distance </p>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                    <p class="rad-heading text-center"> <label class="label_1"> {{@$totalRecords['billingInfo']->duration}} </label>  </br> Ride Duration </p>
                    </div>
                  </div>
                  <div style="margin-bottom:60px"></div>
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                      <div class="form-group">
                      <p class="rad-heading text-center"> <label class="label_1"> {{@$totalRecords['paymentdetail']->amount}}</label>  </br> Bill Cleared </p>
                      </div>
                  </div>
                   <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center "> <label class="label_1"> {{@$totalRecords['billingInfo']->tip}}</label> </br> Tip </p>
                    </div>
                  </div>
                  
                  <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <div class="form-group">
                      <p class="rad-heading text-center "> <label class="label_1">  {{$driverEarning}}</label> </br> Driver Earning </p>
                    </div>
                  </div>
                </div>
                        </div>
                    </div>
                  </div>
        </div>
          </div>

<?php //print_r($ride_data);exit;?>
          <!--Driver Ride detail -->
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
                <h4 class="text-light-blue">{!! $ride_id !!}</h4>
                <h4 class="text-muted">Ride ID</h4><br>
              </div>

              <div class="col-md-3 text-center">
                <h4 class="text-light-blue">{!! $totalRecords['ridedetail']->created_at !!}</h4>
                <h4 class="text-muted">Ride Timestamp</h4><br>
              </div>

              <div class="col-md-2 text-center">
                <h4 class="text-light-blue">{!! $totalRecords['ridedetail']->driver_id !!}</h4>
                <h4 class="text-muted">Driver Id</h4><br>
              </div>
              <div class="col-md-3 text-center">
                <h4 class="text-light-blue">
                  
                  <?php $driver_name = DB::table('dn_users')->select('dn_users.first_name', 'dn_users.last_name')->where('id', $totalRecords['ridedetail']->driver_id)->first(); 
                    echo $driver_name->first_name.' '.$driver_name->last_name; ?>

                </h4>
                
                <h4 class="text-muted">Driver Name</h4>
              
              </div>
              <div class="col-md-2 text-center">
                <h4 class="text-light-blue">

                  @if ($totalRecords['ridedetail']->status == 1)
                      In process
                      <h4 class="text-muted">Ride Status</h4>
                  @elseif ($totalRecords['ridedetail']->status == 2)
                      Complete
                      <h4 class="text-muted">Ride Status</h4>
                  @elseif ($totalRecords['ridedetail']->status == 3)
                      Ride Cancel
                      <h4 class="text-muted">Ride Status</h4>
                  @elseif ($totalRecords['ridedetail']->status == 4)
                      No Responce
                      <h4 class="text-muted">Ride Status</h4>
                  @elseif ($totalRecords['ridedetail']->status == 5)
                      Cancel ride request
                      <h4 class="text-muted">Ride Status</h4>
                  @endif

                </h4>
                
              </div>
            
            </div>

            <div class="box-body">
              @if(empty($ride_data['rating_received']))
                   <div class="col-md-3 text-center">
                     <h6 class="text-muted">Not Yet Rated</h6><br>
                    <p class="text-muted">Passenger rating Received</p>
                  </div> 
                    <div class="col-md-3 text-center">

                     <h6 class="text-muted">Not Yet Rated</h6><br>
                   <p class="text-muted">Driver rating Received</p>
                     </div> 
                     
                @endif
              
              @foreach ($ride_data['rating_received'] as $rating)


                  @if ($rating->rate_by == 3)
                      <div class="col-md-3 text-center">
                     <h4 class="text-light-blue">{!! $ride_id !!}</h4>
                     <h4 class="text-muted">Ride ID</h4><br>
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
                     <h4 class="text-light-blue">{!! $ride_id !!}</h4>
                     <h4 class="text-muted">Ride ID</h4><br>
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

                      $lat = $totalRecords['ridedetail']->pickup_latitude;
                      $lng = $totalRecords['ridedetail']->pickup_longitude;

                      $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
                      $json = @file_get_contents($url);
                      $data=json_decode($json);
                      $status = $data->status;
                      if($status=="OK")
                      echo $data->results[0]->formatted_address;
                      else
                      echo "No Address Location";
                    ?>
                </h4>
              </div>

              <div class="col-md-3">
                <h4 class="text-light-blue">Drop Address</h4>
                <h4 class="text-light-muted">
                    <?php 

                      $lat = $totalRecords['ridedetail']->destination_latitude;
                      $lng = $totalRecords['ridedetail']->destination_longitude;

                      $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
                      $json = @file_get_contents($url);
                      $data=json_decode($json);
                      $status = $data->status;
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

                  <h4 class="text-light-blue">
                    <?php 
                      if ( @$ride_data['collected_ride_data'][0]->payment_status == 1) { echo "Success"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->payment_status == 0) { echo "No Payment"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->payment_status == 2) { echo "Payment Failed"; } 
                    ?>
                  </h4>
                  <h4 class="text-muted">Payment Status</h4>
                </div>
                
                <div class="col-sm-3 text-center">
                  <h4 class="text-light-blue">
                    <?php 
                      if ( @$ride_data['collected_ride_data'][0]->account_type == 'paypal') { echo "PayPal"; } 
                      else if ( @$ride_data['collected_ride_data'][0]->account_type == 'card') { echo $ride_data['collected_ride_data'][0]->masked_number; }
                    ?>
                  </h4>
                  <h4 class="text-muted">Mode Of Payment</h4>
                </div>
                
                <div class="col-sm-2 text-center">
                  <h4 style='word-wrap:break-word' class="text-light-blue"><?php echo @$ride_data['collected_ride_data'][0]->TXN_ID; ?>
                  </h4>
                  <h4 class="text-muted">Transaction ID</h4>
                </div>

               <?php 
				$exp_ride_id = @$ride_data["collected_ride_data"][0]->rideId; 
				$exp_charge_id = @$ride_data["collected_ride_data"][0]->charge_id; 
				$driver_level = @$ride_data["collected_ride_data"][0]->driver_level; 

				$expAmount = @$ride_data["collected_ride_data"][0]->amount;
				$expRefundAmount = @$ride_data["collected_ride_data"][0]->refund_amount;
				$expRefundTip = @$ride_data["collected_ride_data"][0]->tip_refund;

				$expTotalAmont = $expAmount-$expRefundAmount-$expRefundTip;
			   
			   
			   
			   $html='<div class="col-sm-5">
                  <!-- Mathematical Expression Starts Here -->
                  <div class="az-exp">
                    <span id="expRideStatus" expRideStatus='. @$ride_data['collected_ride_data'][0]->payment_status .'></span>
                    <span id="expRideID" expRideID='.$exp_ride_id .'></span>
                     <span id="rideamount" rideamount='.$expAmount .'></span>
                    <span id="expChargeID" expChargeID='. $exp_charge_id.'></span>
                    <span id="expDriverLevel" expDriverLevel='.$driver_level .'></span>

                    <span class="az-exp-row">
                      <span class="az-exp-left">Ride Amount</span>
                      <span expAmount='.$expAmount .' class="expAmount az-exp-right">'.$expAmount .'</span>
                    </span>

                    <span class="az-exp-row">
                      <span class="az-exp-left">Add Refund To Ride</span>
                      <span class="az-exp-right"><input type="number" name="exp_refund_amount" value='.$expRefundAmount .' class="expRefundAmount"></span>
                    </span>

                    <span class="az-exp-row exp-info">
                      <span class="az-exp-left">Dezi Credit Used</span>
                      <span expDeziCredit='.@$ride_data['collected_ride_data'][0]->deziCredit .' class="expDeziCredit az-exp-right">$'.@$ride_data["collected_ride_data"][0]->deziCredit .'</span>
                    </span>

                    <span class="az-exp-row">
                      <span class="az-exp-left">Tip</span>
                      <span expTip='. @$ride_data['collected_ride_data'][0]->tip .' class="expTip az-exp-right">$'. @$ride_data["collected_ride_data"][0]->tip .'</span>
                    </span>

                    <span class="az-exp-row">
                      <span class="az-exp-left">Add Refund To Tip</span>
                      <span class="az-exp-right"><input type="number" name="exp_refund_tip_amount" value='.$expRefundTip .' class="expRefundTip"></span>
                    </span>

                    <span class="az-exp-row exp-result">
                      <span class="az-exp-left"><button type="button" class="expCalTotal text-bold btn btn-block btn-success btn-sm">Grant</button></span>
                      <span class="az-exp-right"><button type="button" class="expShowTotal text-bold btn btn-block btn-default btn-sm">$'.$expTotalAmont .'</button></span>
                      <div class="my-loader"></div>
                    </span>

                  </div>
                  <!-- /Mathematical Expression Ends Here -->
                </div>' ; ?>
				
				<?php $loggedInUserPermission = Session::get('userPermissions');?> 
				
				@if(empty($loggedInUserPermission))
					{!!$html!!}
				@elseif(!empty($loggedInUserPermission))
					@foreach($loggedInUserPermission as $userPermission)								
						@if($userPermission->module_slug=="drivers" && $userPermission->edit_permission==1)
							{!!$html!!}	
						@endif
					@endforeach
			    @endif

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
 <div class="row" id='payment_receipt_pdf' >
			<table id="tab_customers" class="table table-striped">
				<colgroup>
					<col width="40%">
						<col width="40%">
							<!--col width="20%">
								<col width="20%"-->
				</colgroup>
				<thead>
					<tr class='warning'>
						
						
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Ride Id</td>
						<td>{!! @$ride_id !!}</td>
						
					</tr>
					<tr>
						<td>Ride Timestamp</td>
						<td>{!! @$totalRecords['ridedetail']->created_at !!}</td>
						
					</tr>
					<tr>
						<td>Driver Id</td>
						<td>{!! @$totalRecords['ridedetail']->driver_id !!}</td>
						
					</tr>
					<tr>
						<td>Driver Name</td>
						
						<td><?php $driver_name = DB::table('dn_users')->select('dn_users.first_name', 'dn_users.last_name')->where('id', $totalRecords['ridedetail']->driver_id)->first();  
				echo @$driver_name->first_name.' '.@$driver_name->last_name; ?></td>
						
					</tr>

		<tr>
		  <td>Ride Status</td>
		  <td><span style="float:left; margin-left:-50px;"><?php if (@$totalRecords['ridedetail']->status == 1)echo "In Process";elseif (@$totalRecords['ridedetail']->status == 2)echo "Complete";elseif (@$totalRecords['ridedetail']->status == 3)echo "Ride cancel";else echo "No response"; 
		  ?>
		  </span>
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
		  1 rating

					@elseif($rating->rating == 2)
					  2 rating

					@elseif($rating->rating == 3)
					  3 rating

					@elseif($rating->rating == 4)
					  4 rating

					@elseif($rating->rating == 5)
					  5 rating
		  
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
						 
						  <h6 class="text-muted">Not Yet Rated</h6><br>
						
					   </div> 
					@else
					@foreach ($ride_data['rating_received'] as $rating)
					@if ($rating->rate_by == 3)
					 <div class="col-md-3 text-center">
					@if( $rating->rating == 1 )
					  1 rating

					@elseif($rating->rating == 2)
					 2 rating

					@elseif($rating->rating == 3)
					  3 rating

					@elseif($rating->rating == 4)
					  4 rating

					@elseif($rating->rating == 5)
					  5 rating

					
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

				  $lat = $totalRecords['ridedetail']->pickup_latitude;
				  $lng = $totalRecords['ridedetail']->pickup_longitude;

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

				  $lat = $totalRecords['ridedetail']->destination_latitude;
				  $lng = $totalRecords['ridedetail']->destination_longitude;

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
			  

    <!--  -->
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
                        <h4 class="text-light-blue"><?php echo $issue->created_at; ?></h4>
                        <h4 class="text-muted">Issue Timestamp</h4>
						@if(@$issue->status==0)
					    <label class="btn btn-danger btn-xs stLabel">Pending</label>
						@else 
						<label class="btn btn-success btn-xs stLabel">Addressed </label>
						@endif
						
						<!--Subadmin Permission Code Start-->
						@if(empty($loggedInUserPermission))
							<button type="button" data-status="{!! @$issue->status !!}" data-id="{!! @$issue->id !!}" class="btn btn-primary btn-xs statusC">Change Status</button>
						@elseif(!empty($loggedInUserPermission))
							@foreach($loggedInUserPermission as $userPermission)								
								@if($userPermission->module_slug=="drivers" && $userPermission->edit_permission==1)
									<button type="button" data-status="{!! @$issue->status !!}" data-id="{!! @$issue->id !!}" class="btn btn-primary btn-xs statusC">Change Status</button>
								@endif
							@endforeach
						@endif
						<!--Subadmin Permission Code Start-->

                      </div>

                      <div class="col-md-3 text-center">
                        <h4 class="text-light-blue">Category</h4>
                        <h4 class="text-muted"><?php echo $issue->main_category; ?></h4>
                        <h4 class="text-light-blue issueDate"><?php echo $issue->updated_at; ?></h4>
                        <h4 class="text-muted">Addressed Date</h4>
                      </div>

                      <div class="col-md-3 text-center">
                        <h4 class="text-light-blue">Sub category</h4>
                        <h4 class="text-muted"><?php echo $issue->sub_category; ?></h4>
                         <h4 class="text-light-blue"><?php echo $issue->user_type; ?></h4>
                        <h4 class="text-muted">Reported By</h4>
                      </div>
                      <div class="col-md-2 text-center">
                        <h4 class="text-light-blue">Message</h4>
                        <h4 class="text-muted"><?php echo $issue->message; ?></h4>
                      </div>
                    
                    </div>
            
            <?php  $srNum++; 
            }

              }   ?>


            <!-- /.box-body -->
          </div>

          <!-- /.box -->
        </div>

        <!-- ./col -->
      </div>
    <!--  -->

	
		<!--Call log-->
	<div class="row">
        
        
        <div class="col-md-12">
          <div class="box box-solid">
            <div class="box-header with-border" >
              <i class="fa fa-text-width"></i>
              <h3 class="box-title">Call Logs</h3>
            </div>
			<div class="fl sl_widht w100 col-md-12" style="margin-top:20px"  >
        <div class="dt-buttons2">
			@if(empty($loggedInUserPermission))
					<button type="button" class="btn btn-primary"  id="addCallLog">Add more</button>
				@elseif(!empty($loggedInUserPermission))
					@foreach($loggedInUserPermission as $userPermission)								
						@if($userPermission->module_slug=="drivers" && $userPermission->edit_permission==1)
							<button type="button" class="btn btn-primary" id="addCallLog">Add more</button>	
						@endif
					@endforeach
			    @endif
        </div>
			<em id="cl_genrepo"> </em>
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



