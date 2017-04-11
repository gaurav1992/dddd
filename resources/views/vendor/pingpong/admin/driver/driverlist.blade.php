@extends($layout)
@section('title', 'Driver List')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Manage Drivers' !!} 
	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('customjavascript')
<script>
var suspendUrl = "{!! route('suspend') !!}";
var activeDriverList= "{!! route('alldriverlist') !!}";
var homeUrl= "{!! route('index') !!}";
var revokeURL = "{!! route('revoke') !!}";
var dltDrive="{!! route('delete') !!}";
var alldriver= "{!! route('alldriver') !!}";
var CSRF_TOKEN_LIST = '<?php echo csrf_token(); ?>';
var allDriverListReport= "{!! route('allDriverListReport') !!}";

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
  border-bottom: 1px solid #1c1c1c;
  float: left;
  width: 100%;
}
#Passengertable_filter {
    margin: 10px 0;
    text-align: center;
    width: 100%;
}
.dataTables_filter input {
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
form.generate_report > .dt-buttons {
    margin: 30px;
}
.genRepoDL{
float: right;
margin-top: 35px;
margin-right: 10px;
}
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
					@if(Session::has('message'))
						<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
					@endif
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-aqua"><i class="fa fa-map-o" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">Total Drivers</span> <span class="info-box-number totalDR"></span> <span class="totl_pass">Total Drivers </span> </div>
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
		
		
        <!-- /.col -->  
        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-green"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text" style="text-align:center;">{{{ !empty(@$citiesCount['most']->city)? ucfirst($citiesCount['most']->city) : 'N/A' }}}</span> 
			
			
			<span class="info-box-number">{{{ !empty(@$citiesCount['most']->no_of_users)? @$citiesCount['most']->no_of_users:'N/A'}}}   - {{{ !empty(@$citiesCount['most']->state_code)?@$citiesCount['most']->state_code:'N/A' }}}</span> <span class="totl_pass">Most Driver City</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-yellow"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">{{{ !empty(@$citiesCount['least']->city)? ucfirst($citiesCount['least']->city) : 'N/A' }}} </span> <span class="info-box-number">{{{ !empty(@$citiesCount['least']->no_of_users)? @$citiesCount['least']->no_of_users:'N/A'}}}   - {{{ !empty(@$citiesCount['least']->state_code)?@$citiesCount['least']->state_code:'N/A' }}}</span> <span class="totl_pass">Least Drivers City</span> </div>
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
    
        <!-- /.col --> 
      </div>
      <!-- /.row -->

</div>
	
	<!--<form role="form" class="generate_report">-->
		 {!!  Form::open(array('route' => 'allDriverListReport')) !!} 
		<div style="border-bottom:1px solid #ccc; width:100%; float:left">
		<div class="form-group fl sl_widht  w100">
			<label for="email" class="l_blck">Select Location</label>
			<select id="stateDriver" name="state" class="state form-control789" > 
				<option value="">---State---</option>
				@foreach(@$states as $state)
				<option  value="{{$state->state_code}}"> {{@$state->state}}</option>
				@endforeach
			</select>
			<select id="cityDriver" name="city" class="city form-control789"> 
				<option value="">---City---</option>
			</select>
		</div>
		
		<div class="form-group fl w100">
			<label for="pwd" class="l_blck">Join Date Range</label>
			<input class="form-control321" type="text" id="startDatedriverlist" name="startDatedriverlist"  readonly='true' placeholder="MM/DD/YYYY">
			<i class="fa fa-calendar" aria-hidden="true"></i> 
		</div>
		
		<div class="form-group fl w100 " id="drList" >
			<label for="pwd" class="l_blck">&nbsp;</label>
			<input class="form-control321" type="text" id="endDatedriverlist" name="endDatedriverlist"  readonly='true' placeholder="MM/DD/YYYY">
			<i class="fa fa-calendar" aria-hidden="true"></i> 
		</div>
			<!--button type="submit" class="btn btn-default top_mg">Generate Report</button-->
			<?php $loggedInUserPermission = Session::get('userPermissions'); 
			     
			      foreach($loggedInUserPermission as $k=>$allModule){
					$allMod[]= $allModule->module_slug;
					$allModPer[$allModule->module_slug]= $allModule;
				}
			
			
			if(!empty(@$allModPer['reports']->view_permission!=0)||empty($loggedInUserPermission)){?>
			<div class="form-group genRepoDL w100 ">
				<input type="submit" class="btn btn-primary" id="getAllDriverListReport" value="Generate Report">
			</div>
			<?php }?>
            </div>
		 {!! Form::close() !!} 
		<div style="clear:both;margin-bottom:20px;"></div>
		{!! Form::open( ['files' => true,'route' => 'massbulk' ]) !!}
		<table class="table table-responsive" id="activeDriverList" width="100%">
			<thead>			
				<th>Sr. No</th>
				<th>ID No</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Join Date</th>
				<th class="no-sort">Last Ride</th>
				<th>Email</th>
				<th>Phone</th>
				<th>DOB</th>
				<th>City</th>
				<th>State</th>
				<th>Anniversary date</th>
				<th>Zip Code</th>
				<th>Age</th>
				<th>Referral Code</th>
				<th>License No.</th>
				<th>License Exp.</th>
			
				<th>Insurance Exp.</th>
				<th>Date of Approval</th>
				<th>Car transmission</th>
				<th>Status</th>	
				<th class="text-center w96">Action</th>
				<th>Records</th>
			</thead>
			
		</table>
		{!! Form::close() !!}

@stop


