@extends($layout)
@section('title', 'Suspended Users')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Suspended Admin List' !!} 

	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('customjavascript')
<script>
var suspendUrl = "{!! route('suspend') !!}";
var indexUrl2= "{!! route('listSuspendedUserAjax') !!}";
var homeUrl= "{!! route('index') !!}";
var suspendAdminUrl = "{!! route('adminSuspend') !!}";
</script>
@stop
<?php $allActive=0; ?>
@section('content')
<style>
#advanceSearch{padding-top: 30px; display:inline-block;margin-top:30px;}
.suspended_size{
height:26px;
width:175px;

}
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
    text-align: right;
    width: 100%;
}
#Passengertable_filter input{
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
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#startDate,#endDate" ).datepicker().val();
  });
  </script>
<div class="col-lg-12" style="border-bottom:1px solid #ccc;">
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-aqua icon-cust"><i class="fa fa-map-o" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">Total Suspended Accounts</span> <span class="info-box-number">{{count($users)}}</span> <span class="totl_pass">Total Suspended Accounts</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-red icon-cust"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">Highest Suspensions By</span> <span class="info-box-number suspendMax">
             <?php
             
             if($maximumSuspendBy){
               echo $maximumSuspendBy->suspended_by_f_name." ".$maximumSuspendBy->suspended_by_l_name;
             }
             ?> 

            </span> <span class="totl_pass">Highest Suspensions By</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->  <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-green"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text"><?php echo ucfirst(@$citiesCount['most']->city);?></span> <span class="info-box-number">{{ @$citiesCount['most']->no_of_users . ' - ' .@$citiesCount['most']->state_code }}</span> <span class="totl_pass">Most Suspended City</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-yellow"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text"><?php echo ucfirst(@$citiesCount['least']->city);?> </span> <span class="info-box-number">{{ @$citiesCount['least']->no_of_users . ' - ' . @$citiesCount['least']->state_code}}</span> <span class="totl_pass">Least Suspended City</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
       
      </div>
      <!-- /.row -->

</div>
	
<form role="form" class="generate_report">





  <div class="form-group fl sl_widht  w100">
    <label for="email" class="l_blck">Select Location</label>
	<select class="form-control789" id="state" name="state"> 
		<option value="">---State---</option>
		@foreach($states as $state)
		<option  value="{{$state->state_code}}"> {{$state->state}}</option>
		@endforeach
	</select>
	<select class="form-control789" id="city" name="city"> 
		<option value="">---City---</option>
	</select>
  </div>
  <div class="form-group fl w100">
    <label for="pwd" class="l_blck">Select Date</label>
    <input class="form-control321" type="text" id="startDate"  readonly='true' placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i> </div>
  
  <div class="form-group fl w100" id="billing">
    <label for="pwd" class="l_blck">&nbsp;</label>
    <input class="form-control321" type="text" id="endDate"  readonly='true' placeholder="MM/DD/YYYY">
    <i class="fa fa-calendar" aria-hidden="true"></i> </div>
 
   <div class="form-group fl w100">
    <label for="pwd" class="l_blck">Suspended by</label>
    <select class="form-control789" id="revokedBy" name="revokedBy" class="suspended_size"> 
    <option value="">---Select Admin---</option>
   @foreach($adminList as $admin)
    <option  value="{{$admin->id}}"> {{$admin->first_name}} {{$admin->last_name}}</option>
    @endforeach
  </select>
   </div>
  
  
  
  <!--<button type="submit" class="btn btn-default top_mg">Generate Report</button>-->
</form>
<div style="clear:both;margin-bottom:20px;"></div>
{!! Form::open( ['files' => true,'route' => 'massbulk' ]) !!}


	<table class="table" id="Passengertable" style="width:100%">
		<thead>
			
			<th>ID No</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th class="no-sort">Suspension Date</th>
			<th>Last Ride</th>
			<th>Email</th>
			<th>Phone</th>
			<th class="no-sort">Suspended By</th>
		</thead>
		
	</table>
	{!! Form::close() !!}

@stop
