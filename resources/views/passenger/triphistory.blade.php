@extends('frontend.common')

@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">TRIP HISTORY</h3>
</div>
<!--  SECTION-1 -->
<section>
<?php
if($myData){
  $createjoinind_date = new DateTime($myData['created_at']);
  $newjoinind_date = $createjoinind_date->format('m/d/Y');
?>  
  <div class="container mtop-30" id="driverprofileedit">
    
    @include('frontend.passengersidebar')
   
    <div class="col-md-8  earning-cls-div">
      <h2 class="mt-0"> Trip History</h2>
      <!--<button class="btn-grn edit pull-right">Download Weekly Report</button>-->
      <div class="clearfix"></div>
      <div class="text-center date-earn">
   
    <input type="text" name="datefilter" placeholder="Select Date" id="datefilter" readonly value="" />
   
@section('customjavascript')

  <script type="text/javascript">
    $(function() {
      $('input[name="datefilter"]').daterangepicker({autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
      });
       $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
      $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    });
    
    
  </script>
 @stop 
 <style>
  #datefilter{border-radius:10px; box-shadow: 10px 10px 5px #888888;}
 </style>
 
       
        <!--<span class="span1">Total</span> <span  class="span2"> ${{ $totalRideamount[0]->totalRideamount}}</span> </div>-->
	 <div class="col-md-12 marg_bt_tp"><a href="{!! route('generateReportTrip')!!}" class="btn btn-primary green-btn-s edit pull-right ">Report</a></div>
      <div class="earning-data-table" style="border:1px solid black;">
        <p style="text-align: center;">@if(empty($totalRide)) <b> No Records Found </b> @endif</p>
        @foreach ($totalRide as $ride)
            <div class="detail-earn-cls">
          <div class="col-sm-6 left-cls-div">
             <?php if($ride->map_image !=''){ ?>
          <img src="{!! asset($ride->map_image) !!}" style="float: left;height: 90px; margin-right: 20px;width: 140px;" class="img-responsive center-block thumbnail" alt="#"/>
      <?php }else{ ?>
          <img src="{!! asset('public/images/passanger.png') !!}"  style="float: left;height: 90px; margin-right: 20px;width: 140px;" style="float: left;height: 90px; margin-right: 20px;width: 140px;"class="img-responsive center-block thumbnail" alt="#"/>
      <?php } ?> 
     <!-- @if(@$ride->map_image && $ride->map_image!='')
            <img src="{!!$ride->map_image!!}&key=AIzaSyBbCIxyHRspKBFepvSzR1nnNrqUXBZijcs" style="float: left;height: 90px; margin-right: 20px;width: 140px;">  
    
      @else
      <figure> <img src="http://www.startupbangkok.com/wp-content/uploads/2014/12/icon128-2x.png"> </figure>
      @endif-->
            <p> @if(!empty($ride->rideId)) <b> Ride id : </b> {{ @$ride->rideId }} @endif</p>
            <p>@if(!empty($ride->timeStamp)) <b> Timestamp : </b> {{ @$ride->timeStamp }} @endif </p>
            <p>@if(!empty($ride->first_name)) <b> Driver Name: </b> {{ @$ride->first_name }} {{ @$ride->last_name }}  @endif </p>
            <p><a href="javascript:void(0)" id="{{ @$ride->rideId }}" class="viewDe">view Details</a></p>
      
          </div>
          <div class="col-sm-6 right-cls-div ">
            <div class="col-sm-6 text-center detail-e-cls"> ${{ @$ride->amount }} <span>Amount Paid </span> </div>
              @if(@$ride->status== '0')
              <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-remove"></i> <span>
                  No response
              @elseif(@$ride->status== '1')
              <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-ok"></i> <span>
                In progress
              @elseif(@$ride->status== '2')
              <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-ok"></i> <span>
                Completed
              @elseif(@$ride->status== '3')
              <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-remove"></i> <span>
                Ride cancel
              @else
              <div class="col-sm-6 text-center detail-e-cls"> <i class="glyphicon glyphicon-remove"></i> <span>
                  No response
              @endif

            </span> </div>
          </div>
          <div class="clearfix"></div>
        </div>
        @endforeach

       
      </div>
      <div style="width:100%; height:10px; padding-top:30px;"></style>
      <div class="download-invoice-cls" id="invoice">
      
      <div class="right-sec">
      <a href="javascript:rideReportPdf();" class="btn btn-primary green-btn-s edit pull-right" id="dnldbtn">Download Invoice</a></div>
      <!--img id="mapFrame" src="https://www.google.com/maps/embed?pb=!1m22!1m8!1m3!1d219342.99272739235!2d76.7026366!3d30.7960642!3m2!1i1024!2i768!4f13.1!4m11!3e6!4m3!3m2!1d30.769254699999998!2d76.8143436!4m5!1s0x390ff2a7fd40c72b%3A0x9db3b04557e4cde0!2sOpp.+Kansal+Forest+Range%2C+Village%3A+Kansal%2C+Chandigarh%2C+160103!3m2!1d30.769126!2d76.814346!5e0!3m2!1sen!2sin!4v1470363163592" width="686" height="270" frameborder="0" style="border:0; width:100%;" allowfullscreen-->
	   <img   src="" style="width:100%;" height="270" id="mapFrame"> 
      <div class="clearfix"></div>
    
    
      <p id="noMap"> </p>
      
      <table class="table table-referral">
        <thead>
          <tr>
            <th>Pick Up </th>
            <th>Drop Off</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="col-sm-6" id="address1"></td>
            <td class="col-sm-6" id="address2"></td>
          </tr>
          <tr>
            <td class="col-sm-6" id="rStartTime">DD/MM/YYYY</td>
            <td class="col-sm-6" id="rEndTime">DD/MM/YYYY</td>
          </tr>
             <tr>
            <td colspan="2"><hr/><hr/> </td>
          </tr>
        </tbody>
      </table>
      
    
       
       <table class="table table-referral">
        <thead>
          <tr>
            <th colspan="2">Receipt</th>
        
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="col-sm-6" id="miles"></td>
            <td class="col-sm-6" id="milesCharges"></td>
          </tr>
          <tr>
            <td class="col-sm-6" id="durations"></td>
            <td class="col-sm-6" id="durationCharges"></td>
          </tr>
          
            <tr>
            <td></td>
            <td></td>
          </tr>
          
         
          
          <tr>
            <td class="col-sm-6">Subtotal</td>
            <td class="col-sm-6" id="subtotal"> </td>
          </tr>
          <tr>
            <td class="col-sm-6">Dezi Fee</td>
            <td class="col-sm-6" id="deziFee"></td>
          </tr>
          
           <tr>
            <td class="col-sm-6">Pick up Free</td>
            <td class="col-sm-6" id="pick_up"></td>
          </tr>
           <tr>
            <td></td>
            <td></td>
          </tr>
          
            <tr>
            <td class="col-sm-6">Total Bill </td>
            <td class="col-sm-6" id="total"></td>
          </tr>
          
         
        </tbody>
      </table>
      
      <div class="btn-cls-down" id="pass_Id">
      <hr/>
      <div class="col-sm-6 text-center"><a class="btn green-btn-s" href="#" role="button">Report Found Item</a></div>
        
         <div class="col-sm-6 text-center" ><a class="btn green-btn-s" href='{{ url("passenger/passengerReportAnIssue/1479") }}' role="button">Report An Issue</a></div>
      
      <div class="clearfix"></div>
      </div>
      
       <div class="clearfix"></div>
      </div>
    </div>    
  </div>
  
    <!-- REPORT PDF   -->
  
  <div id="customers">

    <table id="tab_customers" class="table table-striped">
        <colgroup>
            <col width="30%">
                <col width="50%">
                   
        </colgroup>
        <thead>
            <tr class='warning'>
                <th>Title</th>
                <th>Value</th> 
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pickup Address</td>
                <td id="pickupAdd"></td>
            </tr>
            <tr>
                <td>Drop off Address</td>
                <td id="dropOff"></td>
                
            </tr>
            <tr>
                <td id="distance">Distance</td>
                <td id="distanCharges"></td>
                
            </tr>
            <tr>
                <td id="time">Time</td>
                <td id="timeCharges"></td>
               
            </tr>
            <tr>
                <td>Subtotal</td>
                <td id="subtotal2"></td>
               
            </tr> 
			<tr>
                <td>Dezi Fee</td>
                <td id="deziFee2"></td>
               
            </tr>
			<tr>
                <td>Pick up Fee</td>
                <td id="pickupFee"></td>
               
            </tr>
			<tr>
                <td>Total Bill </td>
                <td id="TotalEarning"></td>
               
            </tr>
        </tbody>
    </table>
</div>
<!----- IMAGE CODE START--->
<div class="output form-horizontal" style="visibility:hidden"> 
  <hr>
  <h2>Output</h2>
  <div>
    <strong class="col-sm-2 text-right">Converted via:</strong>
    <div class="col-sm-10">
      <span class="convertType"></span>
    </div>
  </div>
  <div>
    <strong  class="col-sm-2 text-right">Size:</strong>
    <div class="col-sm-10">
      <span class="size"></span>
    </div>
  </div>
  <div>
    <strong class="col-sm-2 text-right">Text:</strong>
    <div class="col-sm-10">
      <textarea class="form-control data_img_url textbox"></textarea>
	 
    </div>
  </div>
 </div>
<?php }else{ ?>

<?php } ?>  
</section>
@endsection

@stop

