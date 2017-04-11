@extends($layout)
@section('title', 'Revoke Driver List')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Revoked Drivers' !!} 

	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('customjavascript')
<script>
var revokeDriverList= "{!! route('alldriverRevokelist') !!}";
var revokeURL = "{!! route('revoke') !!}";
var revokedriverUrl = "{!! route('revokedriver') !!}";
var alldriver= "{!! route('revokedriver') !!}";
var homeUrl= "{!! route('index') !!}";
</script>
<script src="{!! admin_asset('js/driverList.js') !!}" type="text/javascript"></script>
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
  border-bottom: 1px solid #ccc;
  float: left;
  width: 100%;
}
#Passengertable_filter {
    margin: 10px 0;
    text-align: center;
    width: 100%;
}
#RevokeDriverList_filter input{
	height: 34px;
	padding: 6px 12px;
	font-size: 14px;
	line-height: 1.42857143;
	color: #555;
	background-color: #fff;
	background-image: none;
	border: 1px solid #ccc;
	border-radius: 4px;
	-webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
	box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
	-webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
	-o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
	transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;	

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

<div class="col-lg-12" style="border-bottom:1px solid #ccc;">
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-aqua"><i class="fa fa-map-o" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">Total Drivers</span> <span class="info-box-number">{{count($users)}}</span> <span class="totl_pass">Total revoked Drivers </span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-red"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">Active Drivers</span> <span class="info-box-number"><?php $allActive=0;
			//print_r(json_decode($Users['data'],true)); die;
				foreach ($users as $user){
					if($user->active == '1'){
						$allActive = $allActive+1;	
					}
				}
				echo $allActive;
			?></span> <span class="totl_pass">Total Active of the Month</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
		<?php 
		//echo '<pre>';
		//print_r($citiesCount); die;
		?>
        <!-- /.col -->  
        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
         <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-green"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">{{{ !empty(@$citiesCount['most']->city)? ucfirst($citiesCount['most']->city) : 'N/A' }}}</span> <span class="info-box-number">{{{ !empty(@$citiesCount['most']->no_of_users)? @$citiesCount['most']->no_of_users:'N/A'}}}   - {{{ !empty(@$citiesCount['most']->state_code)?@$citiesCount['most']->state_code:'N/A' }}}</span> <span class="totl_pass">Most Drivers City</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-yellow"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text"> {{{ !empty(@$citiesCount['least']->city)? ucfirst($citiesCount['least']->city) : 'N/A' }}} </span> <span class="info-box-number">{{{ !empty(@$citiesCount['least']->no_of_users)? @$citiesCount['least']->no_of_users:'N/A'}}}   - {{{ !empty(@$citiesCount['least']->state_code)?@$citiesCount['least']->state_code:'N/A' }}}</span> <span class="totl_pass">Least Drivers City</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col --> 
      </div>
      <!-- /.row -->

</div>
	
<form role="form" class="generate_report">





  <div class="form-group fl sl_widht  w100">
    <label for="email" class="l_blck">Select Location</label>
	<select class="form-control789" id="stateDriver" name="state" class="Restate"> 
		<option value="">---State---</option>
		@foreach($states as $state)
		<option  value="{{$state->state_code}}"> {{$state->state}}</option>
		@endforeach
	</select>
	<select class="form-control789" id="cityDriver" name="city" class="Recity"> 
		<option value="">---City---</option>
	</select>
  </div>
  <div class="form-group fl w100">
    <label for="pwd" class="l_blck">Join Date Range</label>
    <input class="form-control321" type="text" id="startDate"  readonly='true' placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i> </div>
  <div class="form-group fl w100">
    <label for="pwd" class="l_blck">&nbsp;</label>
    <input class="form-control321" type="text" id="endDate"  readonly='true' placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i> </div>
  <div class="form-group fl sl_widht w100" id="driverlist">
    <label for="email"  class="l_blck">Earning Range</label>
    <select class="form-control789" name="earningStart" id="earningStart">
      <option value="0">$0</option>
	  <option value="0">$100</option>
	  <option value="0">$200</option>
	  <option value="0">$300</option>
	  <option value="0">$400</option>
	  <option value="0">$500</option>
    
    </select>
    <select class="form-control789" name="earningEnd" id="earningEnd">
	  <option value="0">$0</option>
	  <option value="0">$100</option>
	  <option value="0">$200</option>
	  <option value="0">$300</option>
	  <option value="0">$400</option>
	  <option value="0">$500</option>
    </select>
  </div>
  
  
  
  
  <!--button type="submit" class="btn btn-default top_mg">Generate Report</button-->
</form>
<div style="clear:both;margin-bottom:20px;"></div>
{!! Form::open( ['files' => true,'route' => 'massbulk' ]) !!}


	<table class="table" id="RevokeDriverList" width="100%">
		<thead>			
			<th>ID No</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Join Date</th>
			<th>Last Ride</th>
			<th>Email</th>
			<th>Phone</th>		
			<th class="text-center w96">Action</th>
		</thead>
		
	</table>
	{!! Form::close() !!}

@stop


