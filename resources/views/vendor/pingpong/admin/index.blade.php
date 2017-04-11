@extends($layout)
@section('customjavascript')
<script>
var payoutUrl = "{!! route('admin.payoutUrl') !!}";
var dynamicPayout = "{!! route('admin.dynamicPayout') !!}";
var DnewPass = "{!! route('admin.DnewPass') !!}";
var DnewDriver = "{!! route('admin.DnewDriver') !!}";
var DnewRide = "{!! route('admin.DnewRide') !!}";
var DrefundUrl = "{!! route('admin.DrefundUrl') !!}";
var DlocationUrl = "{!! route('admin.DlocationUrl') !!}";
var homeUrl= "{!! route('index') !!}";
// alert(payoutUrl);
</script>
<script src="{!! admin_asset('js/dashboard.js') !!}" type="text/javascript"></script>

@stop
@section('content-header')
	<h1 style="text-align:center;">
	 Dashboard
	
	</h1>
<div style="clear:both"></div>
 
@stop

@section('content')

<div style="clear:both;margin-bottom:20px;"></div>

	<div class="row">
        <div class="col-md-12">
          <div class="box">
	 <div class="col-md-6" >
          <div class="box box-solid" style="border:1px solid #ccc;">
            <div style="position:relative" class="box-header with-border">
              <i class="fa fa-text-width"></i>

              <h3 class="box-title">Payouts</h3>
			  {!!  Form::open(array('route' => 'admin.payoutUrl')) !!} 
			
				<div class="form-group wid_45 w100">
				
					<label for="pwd" class="l_blck">Date Range</label>
					<input class="form-control1122 " type="text" name= "startDate" id="startDatedpayout"  readonly='true' placeholder="MM/DD/YYYY">
					<i class="fa fa-calendar" aria-hidden="true"></i> 
				</div>
					<div class="form-group wid_45 w100 " id="drList" >
					<label for="pwd" class="l_blck">&nbsp;</label>
					<input class="form-control1122 " type="text" id="endDatepayout" name= "endDate"  readonly='true' placeholder="MM/DD/YYYY">
					<i class="fa fa-calendar" aria-hidden="true"></i>
				
				</div>
				<div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select class="stateIds form-control1122 " id="payoutState" name="payoutState"> 
					<option value="">---State---</option>
					@foreach($states as $state)
					 <option  value="{{$state->state_code}}"> {{$state->state}}</option>
					@endforeach
				</select>
				</div>
				<div class="form-group wid_45 w100 " id="drList" >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select  name="payoutCity" id="payoutCity" class="cityClass cityTag form-control1122 "> 
					<option value="">---City---</option>
				</select>
			    </div>
				
				<em class="payout_err" style="color:red;wid_45oat: left;position: absolute;left: 10px;bottom: 0px;"></em>
            </div>
			<hr style="border:1px solid #ccc;">
            <!-- /.box-header -->
            <div class="box-body">
			<p class="lead" id="payoutsum">${{$payoutsSum}}</p>
			<p class="text-green dateprint" >{{ $minMaxdate[0]->minDate .' - '. $minMaxdate[0]->maxDate }}  </p>
			<div class="form-group wid_45 w100 " id="drList" >
				<input type="submit" onsubmit="{!! route('admin.payoutUrl')!!}" class="color-blue btn_pad GenerateReportPayouts_po genRepoBtn" id="GenerateReportPayouts" value="Generate Report">
			</div>
			 {!! Form::close() !!} 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
		
		<div class="col-md-6" >
          <div class="box box-solid" style="border:1px solid #ccc;">
            <div style="position:relative"  class="box-header with-border">
              <i class="fa fa-text-width"></i>

              <h3 class="box-title">New Passengers</h3>
			  {!!  Form::open(array('route' => 'admin.newPass')) !!} 
			
				<div class="form-group wid_45 w100">
				<label for="pwd" class="l_blck">Date Range</label>
				<input class="form-control1122 " type="text" name= "startDate"  id="PstartDate" readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i> 
				</div>
				<div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<input type="text" class="form-control1122 "  name= "endDate" id="PendDate" readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i>
				</div>
				<div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select class="stateIds form-control1122 " id="passengerState" name="passengerState"> 
					<option value="">---State---</option>
					@foreach($states as $state)
					 <option  value="{{$state->state_code}}"> {{$state->state}}</option>
					@endforeach
				</select></div>
				<div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select  name="passengerCity" id="passengerCity" class="cityClass cityTag form-control1122 "> 
					<option value="">---City---</option>
				</select>
			    </div>
				<em class="passenger_err" style="color:red;wid_45oat: left;position: absolute;left: 10px;bottom: 0px;"></em>
			
            </div>
			<hr style="border:1px solid #ccc;">
            <!-- /.box-header -->
            <div class="box-body">
			<p class="lead" id="passnew">{{ $passCount }}</p>
			<p class="text-green dateprint2" >{{ $minMaxdatePass[0]->minDate .' - '. $minMaxdatePass[0]->maxDate }}  </p>
			<div class="form-group wid_45 w100 " id="drList" >
				<input type="submit" class="color-blue btn_pad GenerateReportPayouts_npsg genRepoBtn" id="GenerateReportPayouts" value="Generate Report">
			</div>
			 {!! Form::close() !!} 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
		
		<div class="col-md-6" >
          <div class="box box-solid" style="border:1px solid #ccc;">
            <div style="position:relative" class="box-header with-border">
              <i class="fa fa-text-width"></i>

              <h3 class="box-title">New Drivers</h3>
			  {!!  Form::open(array('route' => 'admin.newDriver')) !!} 
			
				<div class="form-group wid_45 w100">
				<label for="pwd" class="l_blck">Date Range</label>
				<input class="form-control1122 " type="text" name= "startDate"  id="DrstartDate" readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i> 
				</div>
				<div class="form-group wid_45 w100 " id="drList" >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<input class="form-control1122 " type="text"  name= "endDate" id="DrendDate" readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i>
				</div>
				
				<div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select class="stateIds form-control1122 " id="driverState"  name="driverState"> 
					<option value="">---State---</option>
					@foreach($states as $state)
					 <option  value="{{$state->state_code}}"> {{$state->state}}</option>
					@endforeach
				</select></div>
				<div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select  name="driverCity" id="driverCity" class="cityClass cityTag form-control1122 "> 
					<option value="">---City---</option>
				</select>
			    </div>
				
				<em class="newdrvr_err" style="color:red;wid_45oat: left;position: absolute;left: 10px;bottom: 0px;"></em>
            </div>
			<hr style="border:1px solid #ccc;">
            <!-- /.box-header -->
            <div class="box-body">
			<p class="lead" id="drivernew">{{ $driverCount }}</p>
			<p class="text-green dateprint3" >{{ $minMaxdatedriver[0]->minDate .' - '. $minMaxdatedriver[0]->maxDate }}  </p>
			<div class="form-group wid_45 w100 " id="drList" >
				<input type="submit" class="color-blue btn_pad GenerateReportPayouts_ndrvr genRepoBtn" id="GenerateReportPayouts" value="Generate Report">
			</div>
			 {!! Form::close() !!} 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
		
		
		<div class="col-md-6" >
          <div class="box box-solid" style="border:1px solid #ccc;">
            <div style="position:relative" class="box-header with-border">
              <i class="fa fa-text-width"></i>

              <h3 class="box-title">Ride Requests</h3>
			  {!!  Form::open(array('route' => 'admin.newRides')) !!} 
			
				<div class="form-group wid_45 w100" >
				<label for="pwd" class="l_blck">Date Range</label>
				<input type="text"  class="form-control1122 " name= "startDate"  id="rdstartDate" readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i> 
				</div>
				<div class="form-group wid_45 w100 " id="drList" >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<input type="text" class="form-control1122 "  name= "endDate" id="rdendDate" readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i>
				</div>
				
				<!--div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select class="stateIds form-control1122 " id="ridereqState" name="ridereqState"> 
					<option value="">---State---</option>
					@foreach($states as $state)
					 <option  value="{{$state->state_code}}"> {{$state->state}}</option>
					@endforeach
				</select></div>
				<div class="form-group wid_45 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select  name="ridereqCity" id="ridereqCity" class="cityClass cityTag form-control1122 "> 
					<option value="">---City---</option>
				</select>
			    </div-->
				<em class="ride_err" style="color:red;wid_45oat: left;position: absolute;left: 10px;bottom: 0px;"></em>
            </div>
			<hr style="border:1px solid #ccc;">
            <!-- /.box-header -->
            <div class="box-body rideBody">

				<div class="form-group wid_45 w100" style="width: 100%; margin-bottom:11px;">

				<?php if (!empty($RideData)) { ?>
					
					<p class="lead marginNone" id="ridesnew1">Most Ride City : {{ $RideData[$maxRideCityIndex]->city_name.'('.$maxRideCount.')'}}</p>
					<p class="lead marginNone" id="ridesnew2">Least Ride City : {{ $RideData[$leastRideCityIndex]->city_name.'('.$leastRideCount.')'}}</p>
					<p class="text-green dateprint4" >{{ $RideData[0]->rideminDate .' - '. $RideData[0]->ridemaxDate }}  </p>

				<?php }else {?>


				<?php } ?>
				</div>

				<div class="form-group wid_45 w100">

				<?php /* if (!empty($RideData)) { ?>

					<p class="lead" id="ridesnew2">{{ $RideData[count($RideData)-1]->counting . ' &nbsp; ' .$RideData[count($RideData)-1]->city_name}}</p>
					<p class="text-green dateprint5" >{{ $RideData[count($RideData)-1]->rideminDate .' - '. $RideData[count($RideData)-1]->ridemaxDate }}  </p>
				
				<?php } */ ?>

				</div>
				
				<div class="form-group wid_45 w100 " id="drList" >
					<input type="submit" class="color-blue btn_pad GenerateReportPayouts_rdrqst genRepoBtn" id="GenerateReportPayouts" value="Generate Report">
				</div>

				 {!! Form::close() !!} 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
		
		
		<div class="col-md-12" >
          <div class="box box-solid" style="border:1px solid #ccc;">
            <div style="position:relative" class="box-header with-border">
              <i class="fa fa-text-width"></i>

              <h3 class="box-title">Refunds</h3>
			  {!!  Form::open(array('route' => 'admin.refundUrl')) !!} 
			
				<div class="form-group wid_23 w100">
				<label for="pwd" class="l_blck">Date Range</label>
				<input class="form-control1122 " type="text" name= "startDate" id="startDaterefund"  readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i> 
				</div>
				<div class="form-group wid_23 w100 " id="drList" >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<input class="form-control1122 " type="text" id="endDatepayoutrefund" name= "endDate"  readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i>
				</div>
				
				<div class="form-group wid_23 w100">
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select class="stateIds form-control1122 " id="refundState"  name="refundState"> 
					<option value="">---State---</option>
					@foreach($states as $state)
					 <option  value="{{$state->state_code}}"> {{$state->state}}</option>
					@endforeach
				</select>
				</div>
				<div class="form-group wid_23 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select  name="refundCity" id="refundCity" class="cityClass cityTag form-control1122 "> 
					<option value="">---City---</option>
				</select>
			    </div>
				<em class="rfnd_err" style="color:red;wid_45oat: left;position: absolute;left: 10px;bottom: 0px;"></em>
            </div>
			<hr style="border:1px solid #ccc;">
            <!-- /.box-header -->
            <div class="box-body">
			<p class="lead" id="refundSum">$ {{$refundData[0]->totalRefund}}</p>
			<p class="text-green dateprint6" >{{ $refundData[0]->minDate .' - '. $refundData[0]->maxDate }}  </p>
			<div class="form-group fl w100 " id="drList" >
				<input type="submit"  class="color-blue btn_pad GenerateReportrefunds_rfnd genRepoBtn" id="GenerateReportrefunds" value="Generate Report">
			</div>
			 {!! Form::close() !!} 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
		
		
		
		<div class="col-md-12" >
          <div class="box box-solid" style="border:1px solid #ccc;">
            <div class="box-header with-border">
              <i class="fa fa-text-width"></i>

              <h3 class="box-title">Locations</h3>
			  {!!  Form::open(array('route' => 'admin.locationUrl')) !!} 
			
				<div class="form-group wid_23 w100">
				<label for="pwd" class="l_blck">Date Range</label>
				<input class="form-control1122 " type="text" name= "startDate" id="startDateloc"  readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i> 
				</div>
				
				<div class="form-group wid_23 w100 " id="drList" >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<input class="form-control1122 " type="text" id="endDatepayoutloc" name= "endDate"  readonly='true' placeholder="MM/DD/YYYY">
				<i class="fa fa-calendar" aria-hidden="true"></i>
				</div>
				
				
				<?php /* <label for="pwd" class="l_blck">&nbsp;</label>
				<?php echo Form::select('city',$cities, null, ['id' => 'cityId','class'=>'cityClass']);  */ ?>
				
				<div class="form-group wid_23 w100 "  >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select id="stateIds" name="state" class="form-control1122 "> 
					<option value="">---State---</option>
					@foreach($states as $state)
					<option  value="{{$state->state_code}}"> {{$state->state}}</option>
					@endforeach
				</select></div>
				<div class="form-group wid_23 w100 " id="drList" >
				<label for="pwd" class="l_blck">&nbsp;</label>
				<select id="cityId" name="city" class="cityClass form-control1122 cityTag"> 
					<option value="">---City---</option>
				</select>
			    </div>
            </div>
			<hr style="border:1px solid #ccc;">
            <!-- /.box-header -->
            <div class="box-body">
			<div class="col-sm-6">
			User Details 
			<p class="lead" id="locationF">Passengers <span style="color:blue;">{{$passengers}}</span>  &nbsp;&nbsp; Drivers <span style="color:blue;">{{ $drivers}}</span>  &nbsp;&nbsp; 
			</p>
			<p class="text-green dateprint7" >{{ $refundData[0]->minDate .' - '. $refundData[0]->maxDate }}  </p>
			</div>
			<div class="col-sm-6">
			Financial Details 
			<p class="lead" id="locationF2">
				Total Revenue <span style="color:blue;">{{'$ '.$tRevenue}}</span>  &nbsp;&nbsp;Payouts <span style="color:blue;">{{'$ '.$payouts}}</span> &nbsp;&nbsp; Profit <span style="color:blue;">{{ '$ '. ($tRevenue - $payouts)}}</span>
			</p>
			</div>
			<div class="form-group wid_45 w100 " id="drList" >
				<input type="submit"  class="color-blue btn_pad genRepoBtn" id="GenerateReportrefunds" value="Generate Report">
			</div>
			 {!! Form::close() !!} 
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
		</div>
        </div>
      </div>	  
@stop

@stop


<style>
.marginNone{margin:0px !important;}
.rideBody {
  padding: 0px 10px !important;}
}
</style>

