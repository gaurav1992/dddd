@extends('frontend.common')
@section('drivercustom')
  
{!! HTML::script('public/js/framework/drivercustom.js'); !!}

@stop
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">EARNING</h3>
</div>
<!--  SECTION-1 -->
<section>

<?php

if($myData){
  $createjoinind_date = new DateTime($myData['created_at']);
  $newjoinind_date = $createjoinind_date->format('m/d/Y');
?>  
  <div class="container mtop-30" id="driverprofileedit">
    
    @include('frontend.driversidebar')
   
    <div class="col-md-8">
  
     <h2 class="mt-0"> Earning</h2>
    <div id="editor"></div>
    
      <div class="clearfix"></div>
      <div class="text-center date-earn">

<?php  $uid=$myData['id']; ?>

	<div class="col-xs-12 col-md-6">
		<fieldset class="form-group">
				<div class='input-group date boder date-picker-one' id='frompicker'>
				{!! Form::open(array('url' => route('earningReport'))) !!}
				{!! Form::hidden('id',@ $uid,array('id'=>'id','class'=>'form-control','required','readonly')) !!}
				{!! Form::text('from','',array('id'=>'from','class'=>'form-control','placeholder' => 'DD/MM/YYYY','required','readonly')) !!}
				<a class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
				</a>


				  </div>
		</fieldset>
    </div>
	<div class="col-xs-12 col-md-6">
        <fieldset class="form-group">
              <div class='input-group date boder date-picker-one' id='topicker'>
        
				{!! Form::text('to','',array('id'=>'to','class'=>'form-control','placeholder' => 'DD/MM/YYYY','required','readonly')) !!}
				<a class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
				</a>
				
              </div>
			  
        </fieldset>
		<input  type="submit" class="btn btn-primary green-btn-s edit pull-right" value="Download Weekly Report">
    </div> 
	
				{!! Form::close() !!}
      <span class="span1">Total</span> <span class="span2"> ${{ $sum_amount }}</span>
    </div>
   @if(Session::has('message'))
              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            @endif
     @if($ride_detail)
    <div class="earning-data-table" id="ridechange">

    @foreach ($ride_detail as $ride)
      <div class="detail-earn-cls" >
          <div class="col-sm-7 left-cls-div">
 <?php if($ride->map_image !=''){ ?>
          <img src="{!! asset($ride->map_image) !!}" style="float: left;height: 90px; margin-right: 20px;width: 140px;" class="img-responsive center-block thumbnail" alt="#"/>
      <?php }else{ ?>
          <img src="{!! asset('public/images/passanger.png') !!}"  style="float: left;height: 90px; margin-right: 20px;width: 140px;" style="float: left;height: 90px; margin-right: 20px;width: 140px;"class="img-responsive center-block thumbnail" alt="#"/>
      <?php } ?>			<div style="overflow: hidden">
            <p><b>Ride Id:</b>{{$ride->id}}</p>
            <p><b>Timestamp:</b>{{date_format(date_create(@$ride->created_at),"d/m/Y H:i:s")}}</p>
			<p><b>Passenger Name:</b> {{$ride->passenger_fname}} </p>
            <p><a href="javascript:void(0)" id="{{ @$ride->id }}" class="rideaddress" >View Details </a> </p>
		
            </div>
          </div>
         
          <div class="col-sm-5 right-cls-div ">
            <div class="col-sm-6 text-center detail-e-cls">$<?php if (!empty($ride->amount)) { echo $ride->amount; } else { echo "0.00"; } ?> <span>Amount Paid </span> </div>

            <?php if($ride->status == 1){ $status_text = "In Progress"; @$status_class='glyphicon glyphicon-refresh'; } 
            else if($ride->status == 2){ $status_text = "Completed"; @$status_class='glyphicon glyphicon-ok'; } 
            else if($ride->status == 3){ $status_text = "Ride Cancel"; @$status_class='glyphicon glyphicon-remove'; } 
            else if($ride->status == 4){ $status_text = "No response"; @$status_class='glyphicon glyphicon-remove'; } ?>

              <div class="col-sm-6 text-center detail-e-cls"> <i class="{!! @$status_class !!}"></i> <span>{{ @$status_text }}</span></div>

            <!-- <div class="col-sm-6 text-center detail-e-cls">
              <i class="glyphicon glyphicon-ok"></i> 
              <span><?php if(@$ride->status == 1){ echo "In Progress"; } 
			  else if(@$ride->status == 2){ echo "Completed"; } 
			  else if(@$ride->status == 3){ echo "Ride Cancel"; } 
			  else if(@$ride->status == 4){ echo "No Response"; } ?>
			  </span>
            </div> -->

          </div> 
          <div class="clearfix"></div>
        </div>
          @endforeach
      </div>
    @else
      <div class="earning-data-table">
          <h1 style="text-align: center;">No Record found</h1>
    </div>
     @endif
      <div class="download-invoice-cls address_pdf" id="address">
        <div class="right-sec">
         <a class="btn btn-primary green-btn-s edit pull-right" id="dwnInvoice" href="javascript:weeklyReport();">Download Invoice</a>
       </div>
      <div class="clearfix"></div>
    <div id="ridechange">
    <div id="rideimage">
       <img src="" style="width:100%;" height="270" id="mapFrame"> 
    </div>
      <p id="noMap" style="display:inline;text-align:center;"> </p>
	<div id="genrateRepo">
      <table class="table table-referral pik_up  table-striped" >
	     <colgroup>
            <col width="40%">
                <col width="40%">
                   
        </colgroup>
        <thead>
          <tr>
            <th align="center">Pick Up </th>
            <th align="center">Drop Off</th>
          </tr>
        </thead>
        <tbody id="ride">
         <tr>
            <td class="col-sm-6" >Address Line 1</td>
            <td class="col-sm-6">Address Line 2 </td>
          </tr>
          <tr>
            <td class="col-sm-6">Address Line 2</td>
            <td class="col-sm-6"> Address Line 2</td>
          </tr>
          <tr>
            <td class="col-sm-6">City, State</td>
            <td class="col-sm-6">City, State </td>
          </tr>
          <tr>
            <td class="col-sm-6">DD/MM/YYYY</td>
            <td class="col-sm-6">DD/MM/YYYY</td>
          </tr>
             <tr> 
            <td colspan="2"><hr/><hr/> </td>
          </tr>
        </tbody>
      </table>
      
    
       
       <table class="table table-referral genrep table-striped">
	   <colgroup>
            <col width="40%">
                
                   
        </colgroup>
        <thead>
          <tr>
            <th align="center" colspan="2">Receipt</th>
        
          </tr>
        </thead>
        <tbody id="receipt">
          <tr>
            <td class="col-sm-6">23 Miles</td>
            <td class="col-sm-6">$XX.XX</td>
          </tr>
          <tr>
            <td class="col-sm-6">120 Minutes</td>
            <td class="col-sm-6">$XX.XX</td>
          </tr>
          
            <tr>
            <td></td>
            <td></td>
          </tr>
          
          
          <tr>
            <td class="col-sm-6">Subtotal</td>
            <td class="col-sm-6">$XX.XX </td>
          </tr>
          <tr>
            <td class="col-sm-6">Dezi Fee</td>
            <td class="col-sm-6">$XX.XX</td>
          </tr>
          
           <tr>
            <td class="col-sm-6">Pick up Free</td>
            <td class="col-sm-6">$XX.XX</td>
          </tr>
           <tr>
            <td></td>
            <td></td>
          </tr>
          
            <tr>
            <td class="col-sm-6">Total Bill </td>
            <td class="col-sm-6">$XX.XX</td>
          </tr>
          
         
        </tbody>
      </table>
      </div>
      </div>
      <div class="btn-cls-down" id="receipt-report">
      
      <hr/>
      <!----
      <div class="col-sm-6 text-center"><a class="btn green-btn-s" href="#" role="button">Report Found Item</a></div>
        
        <div class="col-sm-6 text-center"><a class="btn green-btn-s" href= '{{ url("user-driver/report-issue") }}' role="button">Report An Issue</a></div>
        -->
      
      <div class="clearfix"></div>
      </div>
      
       <div class="clearfix"></div>
      </div>
      
      
    </div> 
<div id="editor" class="bypass"></div>	
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
                <td id="subtotal"></td>
               
            </tr> 
			<tr>
                <td>Dezi Fee</td>
                <td id="deziFee"></td>
               
            </tr>
			<tr>
                <td>Pick up Free</td>
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
