@extends($layout)
@section('title', 'Rejected driver List')
@section('content-header')
  <h1 style="text-align:center;">
    {!! $title or 'Manage Rejected Driver Applicants' !!} 

  </h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop

@section('customjavascript')
<script>
var driverActionUrl = "{!! route('driverAction') !!}";
var rejectedDriverUrl = "{!! route('rejectedDriverAjax') !!}";
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
  border-bottom: 1px solid #ccc;
  float: left;
  width: 100%;
}
#Passengertable_filter {
    margin: 10px 0;
    text-align: center;
    width: 100%;
}
#rejectedDriverList_filter input{
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
  var driverActionUrl='{{route("driverAction")}}';
  var CSRF_TOKEN='{{csrf_token()}}';
   $(document).on("click", ".rejected_driver_approve", function() {
          var r = confirm("Are you sure, you want to approve this account ?");
        if (r == true) {
            var id = $(this).data('userid');
            var dataAction = $(this).data('action');
            // alert(dataAction);
            $.ajax({
                type: 'post',
                url: driverActionUrl,
                data: 'action=' + dataAction + '&id=' + id + '&_token=' + CSRF_TOKEN,
                beforeSend: function(xhr) {
                $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
				},
                success: function(returnData) {
                   
                }
            });
        }
    });

   
  </script>
<div class="col-lg-12" style="border-bottom:1px solid #ccc;">
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-aqua"><i class="fa fa-map-o" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">New Applicants</span> <span class="info-box-number">{{count($newApplicants)}}</span> <span class="totl_pass">New Applicants</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-red"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">Rejected Applicants</span> <span class="info-box-number">{{count($rejectedApplicants)}}</span> <span class="totl_pass">Rejected Applicants</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->  
        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-green"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">{{{ !empty(@$citiesCount['most']->city)? ucfirst($citiesCount['most']->city) : 'N/A' }}}</span> <span class="info-box-number">{{{ !empty(@$citiesCount['most']->no_of_users)? @$citiesCount['most']->no_of_users:'N/A'}}}   - {{{ !empty(@$citiesCount['most']->state_code)?@$citiesCount['most']->state_code:'N/A' }}}</span> <span class="totl_pass">Most Driver City</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box"> <span class="info-box-icon bg-yellow"><i class="fa fa-location-arrow" aria-hidden="true"></i></span>
            <div class="info-box-content"> <span class="info-box-text">{{{ !empty(@$citiesCount['least']->city)? ucfirst($citiesCount['least']->city) : 'N/A' }}}</span> <span class="info-box-number">{{{ !empty(@$citiesCount['least']->no_of_users)? @$citiesCount['least']->no_of_users:'N/A'}}}   - {{{ !empty(@$citiesCount['least']->state_code)?@$citiesCount['least']->state_code:'N/A' }}}</span> <span class="totl_pass">Least Driver City</span> </div>
            <!-- /.info-box-content --> 
          </div>
          <!-- /.info-box --> 
        </div>
        <!-- /.col --> 
      </div>
      <!-- /.row --> 
</div>


<div class="main-applicant">
  
  <ul class="nav nav-tabs">
    <li class="same-butt"><a href="<?=App::make('url')->to('/admin/driver/new-driver-applicant-list')?>">New Applicants</a></li>
    <li class="active same-butt"><a href="<?=App::make('url')->to('/admin/driver/rejected-driver-applicant-list')?>">Rejected Applicants</a></li>
  </ul>
  
  <div class="tab-content">
    <h1>Filter Generate Report</h1>

    <div id="home" class="tab-pane fade in active">

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
          <label for="pwd" class="l_blck">Join Date Range</label>
          <input class="form-control321" type="text" id="startDate"  readonly='true' placeholder="MM/DD/YYYY">
          <i class="fa fa-calendar" aria-hidden="true"></i>
        </div>

        <div class="form-group fl w100">
          <label for="pwd" class="l_blck">&nbsp;</label>
          <input class="form-control321" type="text" id="endDate"  readonly='true' placeholder="MM/DD/YYYY">
          <i class="fa fa-calendar" aria-hidden="true"></i>
        </div>
        
        <div class="form-group fl sl_widht w100" id="billing">
          <!-- <label for="email"  class="l_blck">Billing Range</label>
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
          </select> -->
        </div>
        <!--button type="submit" class="btn btn-default top_mg">Generate Report</button-->
      </form>
      
    </div>
  </div>

</div>


<div style="clear:both;margin-bottom:20px;"></div> 

  <table class="table" id="rejectedDriverList" width="100%">
  <!-- <table class="table" id="newDriverApplicantList" width="100%"> -->
    <thead>
      
      <th>ID No</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Join Date</th>
      <th>Last Ride</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Status</th>
      <th>Is Logged In</th>
      
      <th class="text-center w96">Action</th>
    </thead>
    
  </table>

@stop


