@extends($layout)
@section('title', 'Driver Charges')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Manage Charges' !!} 

	</h1>
<link href='http://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold,bolditalic&amp;subset=latin,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

<div style="clear:both"></div>
{!!@$message!!}
@stop

@section('customjavascript')
<script src="{!! admin_asset('js/jquery.noty.packaged.js') !!}" type="text/javascript"></script>
<script src="{!! admin_asset('js/notification_html.js') !!}" type="text/javascript"></script>
<script>
var cityAjax = "{!! route('cityAjax') !!}";
var dayChargesAjax = "{!! route('dayChargesAjax') !!}";
var viewCityChargesURL = "{!! route('driverCharges') !!}";
var driverChargesurl = "{!! route('driverCharges') !!}";
var wkdayChargesUrl = "{!! route('wkdayCharges') !!}";
var driverChargeUpdateURL = "{!! route('driverChargeUpdate') !!}";
var CSRF_TOKEN = "{!! csrf_token() !!}";
var sureFlag=false;
$(document).on("click","#pdfgeneratot",function(){ 
    
    var startDatedpayout = $("#startDatepdf").val();
    
    var endDatepayout = $("#endDatepdf").val();
    
    if(startDatedpayout=='' || endDatepayout==''){
      
      $(".changelogerr").text('Date-range fields can not empty')
      return false;
      
    }else{
      
      $(".changelogerr").text('');
      document.getElementById('pdfform').submit();
      return true;
    }
    
  });
  
  
    function generate(type,text) {
		var n = noty({
                text        : text,
                type        : type,
                dismissQueue: true,
                layout      : 'topLeft',
                closeWith   : ['click'],
                theme       : 'relax',
                maxVisible  : 10,
                animation   : {
                    open  : 'animated bounceInLeft',
                    close : 'animated bounceOutLeft',
                    easing: 'swing',
                    speed : 500
                }
            });
        //console.log('html: ' + n.options.id);
    }
 
   /*  function generateAll() {
		
         generate('alert', 'topCenter');
        generate('information', 'topLeft');
        generate('error', 'topRight');
        generate('warning', 'center');
        generate('notification', 'centerRight');
        generate('success', 'centerLeft'); 
    } */

     
/*
$(document).ready(function(){
    
    if($(".pdfgeneratot").click(function(){
        $("#pdfform").validate({
            rules: {
              from: "required",
              to: "required"
            },
            messages: {
              from:"Please enter start date",
              to:"Please enter End date"
            }
          });
      })){
      
    }
    
  });*/
 
  
</script>
<script src="{!! admin_asset('js/driverList.js') !!}" type="text/javascript"></script>


@stop

@section('content')
<div class="container"  style="line-height: 13px">

    <div id="customContainer"></div>

</div>
<div class="blade-3">
<div class="content-part container-part-1">

  @if(Session::has('message'))
  <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
  @endif

  

  <!-- City Default Charges -->
   <?php echo  Form::open(array('url' => 'admin/driver/driverCharges', 'method' => 'post')); ?>

      <?php if (!empty($default_charges)) {
         $cost_per_mile   = $default_charges->cost_per_mile;
         $per_min_charge  = $default_charges->per_min_charge;
         $less_mile_travel_cost     = $default_charges->less_mile_travel_cost;
         $greater_mile_travel_cost  = $default_charges->greater_mile_travel_cost;
         $service_charge  = $default_charges->service_charge;
         $min_charge      = $default_charges->min_charge;
         $cancelation_charge  = $default_charges->cancelation_charge;
      } else {
         $cost_per_mile   = '';
         $per_min_charge  = '';
         $less_mile_travel_cost     = '';
         $greater_mile_travel_cost  = '';
         $service_charge  = '';
         $min_charge      = '';
         $cancelation_charge  = '';
      } ?>
      
      <input type="hidden" name='submit' value='default'>
      <div class="col-sm-12 current-price pad">
        
        <h5 class="text-green lead text-center">Default Reservation Charges For All Cities</h5> 

        <div class="col-sm-8 driver-charge-main">
          <div class="form-group col-sm-6 driver-charge" style="padding-left:15px;">
            <label for="male">Cost Per Mile</label>
            <input name="default_cost_per_mile" value='<?php echo $cost_per_mile; ?>' class="form-control" id="exampleInputEmail1"  type="text" required='required'>
            
          </div>
           <div class="form-group col-sm-6 driver-charge" style="padding-left:15px;">
            <label for="male">Per Minute Charge</label>
            <input name="default_per_min_charge" value='<?php echo $per_min_charge; ?>' class="form-control" id="exampleInputEmail1"  type="text"  required='required'>
          </div>

          <div class="form-group col-sm-6 ">
            <label for="male">Cost For &lt;1.5mi Driver Travel</label>
            <input name="default_less_mile_travel_cost" value='<?php echo $less_mile_travel_cost; ?>' class="form-control" id="exampleInputEmail1" type="text"  required='required'>
            
          </div>
           <div class="form-group col-sm-6">
            <label for="male">Cost For &gt;1.5mi Driver Travel</label>
            <input name='default_greater_mile_travel_cost' value='<?php echo $greater_mile_travel_cost; ?>' class="form-control" id="exampleInputEmail1"  type="text"  required='required'>
          </div>

        </div>

        <div class="col-sm-4" >
          <div class="form-group driver-charge-main col-sm-12">
            <label for="male">Service Charge</label>
            <input name='default_service_charges' value='<?php echo $service_charge; ?>' class="form-control" id="exampleInputEmail1"  type="text" required='required'>
            
          </div>
           <div class="form-group driver-charge-main col-sm-12">
            <label for="male">Min Charge</label>
            <input name='default_min_charge' value='<?php echo $min_charge; ?>' class="form-control" id="exampleInputEmail1"  type="text" required='required'>
            
          </div>
           <div class="form-group driver-charge-main col-sm-12">
            <label for="male">Cancellation Charge</label>
            <input name='default_cancelation_charge' value='<?php echo $cancelation_charge; ?>' class="form-control" id="exampleInputEmail1" type="text" required='required'>
          </div>
        </div>

        <div class="cost_per_mile col-sm-12 trigger-main">
          <button type="submit" class="btn btn-default" style='width: 273px;'>Save Default Charges</button>
        </div>

      </div>
    </form>


  <!-- City Box starts here -->
  <?php echo  Form::open(array('url' => 'admin/driver/driverCharges', 'method' => 'post', 'class' => 'driver-charges')); ?>

      <input type="hidden" name='submit' value='1'>

  <div class="col-sm-12 new-city-box">
    
    <div class="search-box pull-left">
      <span class="fa fa-search"></span>
      <input type="search" name='search_city' id="search_city" placeholder="Search City">
    </div>

    <div class="button-left col-md-offset-8">
	
		<div class="select_state col-md-8">
			<select id="stateCh" class="form-control" name="state"> 
				<option value="">---State---</option>
				@foreach($states as $state)
				<option  value="{{$state->state_code}}"> {{ $state->state }}</option>
				@endforeach
			</select>
		</div>
		<div class="state_btn">
			<a type="button" class="btn btn-default select-all-city" style="background: #00c0ef;color: #fff;">Select All</a>
		</div>
    </div>

    <!-- All City Section -->
    <div class='all-city-section cityblock'>

      


    </div>
    <!-- /All City Section -->
<!-- 
    <div class="col-sm-12 new-city-butt">
      <button type="submit" class="btn btn-default">Done</button>
    </div> -->
  </div>

  <div class="col-sm-12 current-price pad">
    <div class="col-sm-12">
      <div class="col-sm-8 pad">
        <p>Current Price Set Time 12-10-16;1400 hrs</p>
        <p>Last Price Change Time 12-10-16;1400 hrs</p>
      </div>

      <div class="schdule pad  pull-right">
		<a type="button" class="btn btn-default view-charges" style="background: #00c0ef;color: #fff;">View Charges</a>
        <button type="button" class="btn btn-default" id='price_schdule' style="background: #00c0ef;color: #fff;">Price Schedule</button>
      </div>
    </div>


    <div class="col-sm-8 driver-charge-main">
      <p>Administrative Charges</p>
      <div class="form-group col-sm-6 driver-charge">
        <label for="male">Cost Per Mile</label>
        <input name="default_cost_per_mile" class="form-control" id="default_cost_per_mile"  type="text" required='required'>
      	
      </div>
       <div class="form-group col-sm-6 driver-charge">
        <label for="male">Per Minute Charge</label>
        <input name="default_per_min_charge" class="form-control" id="default_per_min_charge"  type="text"  required='required'>
      </div>

			<div class="form-group col-sm-6 driver-charge">
        <label for="male">Cost For &lt;1.5mi Driver Travel</label>
        <input name="default_less_mile_travel_cost" class="form-control" id="default_less_mile_travel_cost"  type="text"  required='required'>
			 
      </div>
      <div class="form-group col-sm-6 driver-charge">
        <label for="male">Cost For &gt;1.5mi Driver Travel</label>
        <input name='default_greater_mile_travel_cost' class="form-control" id="default_greater_mile_travel_cost"  type="text"  required='required'>
      </div>

    </div>

    <div class="col-sm-4" style="padding-top:28px;">
   
      <div class="form-group driver-charge-main col-sm-12">
         <label for="male">Service Charge</label>
        <input name='default_service_charges' class="form-control" id="default_service_charges"  type="text" required='required'>
			  </div>
      <div class="form-group driver-charge-main col-sm-12">
         <label for="male">Min Charge</label>
        <input name='default_min_charge' class="form-control" id="default_min_charge" type="text" required='required'>
      </div>
      <div class="form-group driver-charge-main col-sm-12">
         <label for="male">Cancellation Charge</label>
        <input name='default_cancelation_charge' class="form-control" id="default_cancelation_charge"  type="text" required='required'>
      </div>
    </div>

    <!-- <div class="cost_per_mile col-sm-12 trigger-main">
      <button type="submit" class="btn btn-default trigger-but1">Save Changes</button>
    </div> -->

  </div>

</div>

<div class="content-part charge-blad rajat-form-group price_schdule">
  <span class="close-me">X</span>
  <div class="col-sm-12 blade-all-main">
  	<div class="form-group week-form">
      <div class="check-butt">
		<!--span id="weekCal" class="selectday"></span-->
		<!--input type="hidden"  name="jsonData"  id="jsonData" >
          <label class="btn btn-primary  circle day_id" data-dayID='1'>
            <input type="checkbox" class="selectday" name="day_id" value='1' id="option1"><span>S</span>
          </label>
          <label class="btn btn-primary circle day_id" data-dayID='2'>
            <input type="checkbox" class="selectday" name="day_id" value='2' id="option2" ><span> M</span>
          </label>
          <label class="btn btn-primary circle day_id" data-dayID='3'>
            <input type="checkbox" class="selectday" name="day_id" value='3' id="option3" ><span> T</span>
          </label>
            <label class="btn btn-primary circle day_id" data-dayID='4'>
            <input type="checkbox" class="selectday" name="day_id" value='4' id="option4" > <span>W</span>
          </label>
          <label class="btn btn-primary circle day_id" data-dayID='5'>
            <input type="checkbox" class="selectday" name="day_id" value='5' id="option5" ><span> T</span>
          </label>
          <label class="btn btn-primary circle day_id" data-dayID='6'>
            <input type="checkbox" class="selectday" name="day_id" value='6' id="option6" ><span> F</span>
          </label>
            <label class="btn btn-primary circle day_id" data-dayID='7'>
            <input type="checkbox" class="selectday" name="day_id" value='7' id="option7" ><span> S</span>
          </label-->
  <ul class="nav nav-tabs">
    <li class="active listTab"><a data-toggle="tab" id="tab1"  class="dayTab" data-value="1" href="#SUN">SUN</a></li>
    <li class="listTab"><a data-toggle="tab" class="dayTab" id="tab2" data-value="2" href="#MON">MON</a></li>
    <li class="listTab"><a data-toggle="tab" class="dayTab" id="tab3" data-value="3" href="#TUE">TUE</a></li>
    <li class="listTab"><a data-toggle="tab" class="dayTab" id="tab4" data-value="4" href="#WED">WED</a></li>
    <li class="listTab"><a data-toggle="tab" class="dayTab" id="tab5" data-value="5" href="#THU">THU</a></li>
    <li class="listTab"><a data-toggle="tab" class="dayTab" id="tab6" data-value="6" href="#FRI">FRI</a></li>
    <li class="listTab"><a data-toggle="tab" class="dayTab" id="tab7" data-value="7" href="#SAT">SAT</a></li>
  </ul>
      </div>
    </div>

   <?php if (!empty($day_charges)) {
       // echo "<pre>"; print_R($day_charges); die;
        foreach ($day_charges as $charges) {
          
          $day_number     = $charges->day_number;
          $cost_per_mile  = $charges->cost_per_mile;
          $per_min_charge = $charges->per_min_charge;
          $less_mile_travel_cost    = $charges->less_mile_travel_cost;
          $greater_mile_travel_cost = $charges->greater_mile_travel_cost;
          $service_charge = $charges->service_charge;
          $min_charge     = $charges->min_charge;
          $cancelation_charge = $charges->cancelation_charge;
          $from_time  = $charges->from_time;
          $to_time    = $charges->to_time;
          $created_on = $charges->created_on;

        }

      } ?> 
 <div class="tab-content" >
    <div id="SUN" class="tab-pane fade in active">
    </div>   
	
	<div id="MON" class="tab-pane fade">
    </div>   
	
	<div id="TUE" class="tab-pane fade">
    </div>   
	
	<div id="WED" class="tab-pane fade">
    </div> 

	<div id="THU" class="tab-pane fade">
    </div>  

	<div id="FRI" class="tab-pane fade">
    </div>  
	
	<div id="SAT" class="tab-pane fade">
    </div>
    </div>
  </div>
 </div>

	<div class="col-sm-12 trigger-main">
    <input type='hidden' name='is_schedule_charges' value='0'>
  	<button type="submit" class="btn btn-default trigger-but1 updateCharges">Update</button>
  	<button type="reset" class="btn btn-default trigger-but2 undo ">Undo Changes</button>
    
	</div>
    <div class="content-part container-part-1">
<div class="col-sm-12 current-price">
<?php echo Form::close(); ?>


<?php echo  Form::open(array('url' => 'admin/driver/driverChargespdf', 'method' => 'post','id'=>'pdfform')); ?>

    <div class="col-sm-12 date-label">
      <div class="form-group fl w100">
        <label>From</label>
        <input type="text" name="from" id="startDatepdf" readonly placeholder="MM/DD/YYYY">
        <input type="hidden" id="tokenpdf" name="_token" value="{{ csrf_token() }}">
        <i class="fa fa-calendar" aria-hidden="true"></i> 
      </div>

      <div class="form-group fl w100">
        <label for="pwd" class="l_blck">TO</label>
        <input type="text" name="to" id="endDatepdf" readonly placeholder="MM/DD/YYYY">
        <i class="fa fa-calendar" aria-hidden="true"></i>
      </div>
      
      <div class="dt-buttons" style="margin-top:20px">
        <button class="dt-button buttons-pdf buttons-html5 btn btn-default color-blue" id="pdfgeneratot" tabindex="0" aria-controls="Passengertable" style=" width:150px; text-align:center">
          <span>Download Change Log</span>
        </button>
      </div>
      
    </div>
    <em class="changelogerr">    </em>
<?php echo Form::close(); ?>
</div>
</div>
</div>
@stop

@section('style')
<style>
.col-sm-12.new-city-box {
  max-height: 500px;
  overflow: auto;
}
.search-box input {
    
    height: 38px !important;
    
}
.changelogerr{
  color:red;
  margin-left:20px;
}
.search-box { position: relative; }
.search-box input { text-indent: 30px;}
.search-box .fa-search { 
  position: absolute;
  top: 13px;
  right: 14px;
  font-size: 15px;
}
</style>
@stop

@section('customjavascript')
<script>
$('.undo').click(function(){
	alert(7);
$( ":input" ).val()='';
});
</script>


@stop