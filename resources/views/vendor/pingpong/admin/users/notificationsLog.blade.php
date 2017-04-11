@extends($layout)
@section('title', 'Notification Logs')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Manage Notifications Log' !!} 
	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('customjavascript')
<script>
var suspendUrl = "{!! route('suspend') !!}";
var indexUrl2= "{!! route('notificationLogAjax') !!}";
var homeUrl= "{!! route('index') !!}";
</script>
@stop
<?php $allActive=0; ?>
@section('content')
<style>
#advanceSearch{padding-top: 30px; display:inline-block;margin-top:30px;}

.fl {
    float: left;
    margin-right: 28px;
    margin-top: 14px;
}
.l_blck {
  display: block;
  font-size: 12.5px;
}

.top_mg {
  background: #3C8DBC none repeat scroll 0 0 !important;
  border: 0 none !important;
  color: #fff !important;
  display: block;
  line-height: 17px;
  margin-top: 35px;
  padding-left: 25px !important;
  padding-right: 25px !important;
}

.top_mg:hover {
  background: #3C8DBC none repeat scroll 0 0 !important;
  border: 0 none !important;
  color: #fff !important;
  display: block;
  line-height: 17px;
  margin-top: 35px;
  padding-left: 25px !important;
  padding-right: 25px !important;
}

.sl_widht{
	width:22%;
}

.form-group.fl.sl_widht > select {
  width: 49%;
   height: 26px;
}

.generate_report {
  border-bottom: 1px solid #1c1c1c;
  float: left;
  width: 100%;
}
#Passengertable_filter {
    margin: 10px 0;
    text-align: center;
    width: 100%;
}
.dataTables_length{display:none;}
</style>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
  <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#startDate,#endDate" ).datepicker().val();
  });
  </script>

	
<form role="form" class="generate_report">

  <!-- <div class="form-group fl sl_widht  w100">
    <label for="email" class="l_blck">Select Location</label>
  	<select id="state" name="state"> 
  		<option value=""> -State- </option>
  		@foreach($states as $state)
  		<option  value="{{$state->state_code}}"> {{$state->state}}</option>
  		@endforeach
  	</select>
  	<select id="city" name="city"> 
  		<option value=""> -City- </option>
  	</select>
  </div> -->

  <div class="form-group fl w100">
    <label for="pwd" class="l_blck">Date Range</label>
    <input type="text" id="startDate"  readonly='true' placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i>
  </div>
  
  <div class="form-group fl w100">
    <label for="pwd" class="l_blck">&nbsp;</label>
    <input type="text" id="endDate"  readonly='true' placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i>
  </div>
  
  <div class="form-group fl sl_widht w100" id="billing" style="display:none;">
    <label for="email"  class="l_blck">Billing Range</label>
    <select >
      <option value="volvo">$From</option>
      <option value="saab">Saab</option>
      <option value="mercedes">Mercedes</option>
      <option value="audi">Audi</option>
    </select>
    <select >
      <option value="volvo">$To</option>
      <option value="saab">Saab</option>
      <option value="mercedes">Mercedes</option>
      <option value="audi">Audi</option>
    </select>
  </div>
  
  <!--button type="submit" class="btn btn-default top_mg">Generate Report</button-->
</form>

<div style="clear:both;margin-bottom:20px;"></div>

	<table class="table" id="Passengertable" width="100%">
		<thead>
			
			<th>Sr No</th>
			<th>To</th>
			<th>Via</th>
			<th>Time Stamp</th>
			<th>Message</th>
			<th>City</th>
			<th>Status</th>
			<th>Admin User</th>

		</thead>
		
	</table>

@stop


