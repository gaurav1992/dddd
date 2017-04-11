@extends($layout)
@section('title', 'Ride Bonus')

@section('customjavascript')
<script>

var driverBonusAjax = "{!! route('driverBonusAjax') !!}";
</script>
<script src="{!! admin_asset('js/charges-bonus.js') !!}" type="text/javascript"></script>
@stop


@section('content')
<div class="content-part charge-blad">
<h1>Tier bonuses</h1>
 @if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
	@endif
<ul id="errorContainer"></ul>	
 <ul class="nav nav-tabs">
    <li ><a  href="{!! route('riderpromos') !!}">Promos</a></li>
    <li class="active"><a  href="{!! route('riderbonus') !!}">Tier Bonuses</a></li>
  </ul>

<div class="tab-content main-tab">   
  
    <div id="menu1" class="tab-pane fade active in">
      <h1>Schedule(Tiers to auto reset on every Monday at 12 pm)</h1>
      {!! Form::open(array('method' => 'POST','id' => 'driverbonus','name'=>'driverbonus','files' => true)) !!} 
      <div class="col-sm-12">

	    <div class="form-group">
		
            <label class="col-sm-2 control-label day-label" for="IsSmallBusiness">Day</label>

			<div class="col-sm-10 check-butt">
				<div class="btn-group" data-toggle="buttons">
					<label class="readOnly btn btn-primary active circle">
						<input type="checkbox" name="day[]" class="scheduleDay" autocomplete="off" value="1" checked onclick="return false"> <span>S</span>
					</label>
					<label class="readOnly btn btn-primary active circle">
						<input type="checkbox" name="day[]" class="scheduleDay" autocomplete="off" value="2" checked readonly><span> M</span>
					</label>
					<label class="readOnly btn btn-primary active circle">
						<input type="checkbox" name="day[]" class="scheduleDay" autocomplete="off" value="3" checked><span> T</span>
					</label>
					<label class="readOnly btn btn-primary active circle">
						<input type="checkbox" name="day[]" class="scheduleDay" autocomplete="off" value="4" checked> <span>W</span>
					</label>
					<label class="readOnly btn btn-primary active circle">
						<input type="checkbox" name="day[]" class="scheduleDay" autocomplete="off" value="5" checked><span> T</span>
					</label>
					<label class="readOnly btn btn-primary active circle">
						<input type="checkbox" name="day[]" class="scheduleDay" autocomplete="off" value="6" checked><span> F</span>
					</label>
					<label class="readOnly btn btn-primary active circle">
						<input type="checkbox" name="day[]" class="scheduleDay" autocomplete="off" value="7" checked><span> S</span>
					</label>
				</div>
			</div>

			<label class="col-sm-2 control-label day-label" for="IsSmallBusiness">From</label>
			<div class="col-sm-10 check-butt time-circle">

				<?php $scheduled_time = json_decode( $bonusData->scheduled_time ); ?>

				<div class="circle1">
					<input type="text" class="form-control" id="from_time" readonly value="<?php echo $scheduled_time->from_time[0] ?>" name="from_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" id="from_time" readonly value="<?php echo $scheduled_time->from_time[1] ?>" name="from_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" id="from_time" readonly value="<?php echo $scheduled_time->from_time[2] ?>" name="from_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" id="from_time" readonly value="<?php echo $scheduled_time->from_time[3] ?>" name="from_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" id="from_time" readonly value="<?php echo $scheduled_time->from_time[4] ?>" name="from_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" id="from_time" readonly value="<?php echo $scheduled_time->from_time[5] ?>" name="from_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" id="from_time" readonly value="<?php echo $scheduled_time->from_time[6] ?>" name="from_time[]" placeholder="">
				</div>
			</div>
			<label class="col-sm-2 control-label day-label" for="IsSmallBusiness">TO</label>
			
			<div class="col-sm-10 check-butt time-circle">
				<div class="circle1">
					<input type="text" class="form-control" readonly id="to_time" value="<?php echo $scheduled_time->to_time[0] ?>" name="to_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" readonly id="to_time" value="<?php echo $scheduled_time->to_time[1] ?>" name="to_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" readonly id="to_time" value="<?php echo $scheduled_time->to_time[2] ?>" name="to_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" readonly id="to_time" value="<?php echo $scheduled_time->to_time[3] ?>" name="to_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" readonly id="to_time" value="<?php echo $scheduled_time->to_time[4] ?>" name="to_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" readonly id="to_time" value="<?php echo $scheduled_time->to_time[5] ?>" name="to_time[]" placeholder="">
				</div>
				<div class="circle1">
					<input type="text" class="form-control" readonly id="to_time" value="<?php echo $scheduled_time->to_time[6] ?>" name="to_time[]" placeholder="">
				</div>
			</div>

	  </div>
    </div>
	<hr></hr>
	<div class="col-sm-12 jewellary">
    <div class="row">
	<div class="col-sm-2">
	<label></label>
	</div>
    <div class="col-sm-2">
	<label>Silver</label>
	</div>
	<div class="col-sm-2">
	<label>Gold</label>
	</div>
	<div class="col-sm-2">
	<label>Platinum</label>
	</div>
	<div class="col-sm-2">
	<label>Diamond</label>
	</div>
    <div class="col-sm-2">
	<label></label>
	</div>
	</div>
  
    
    
     <div class="row">
    <div class="col-sm-2 text-right"><label for="male">Commission %</label></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="commission_silver" name="commission_silver" value="{!! $bonusData->commission_silver !!}" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="commission_gold" name="commission_gold" value="{{ $bonusData->commission_gold }}" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="commission_platinum" name="commission_platinum" value="{{ $bonusData->commission_platinum }}"  ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="commission_diamond" name="commission_diamond" value="{{ $bonusData->commission_diamond }}" ></div>
    <div class="col-sm-2"><span>(weekly)</span></div>
    </div>
    <br>

     <div class="row">
    
      <div class="col-sm-2 text-right"><label for="male">Total active hrs</label></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_silver" value="{{ $bonusData->total_hrs_silver }}" name="total_hrs_silver" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_gold" value="{{ $bonusData->total_hrs_gold }}" name="total_hrs_gold" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_platinum" value="{{ $bonusData->total_hrs_platinum }}" name="total_hrs_platinum" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_diamond" value="{{ $bonusData->total_hrs_diamond }}" name="total_hrs_diamond" ></div>
    <div class="col-sm-2"><span>(weekly)</span></div>
    </div>
    
    <br>

      <div class="row">
    
      <div class="col-sm-2 text-right"><label for="male">Total active hrs (Schedule)</label></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_schedule_silver" value="{{ $bonusData->total_hrs_schedule_silver }}" name="total_hrs_schedule_silver" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_schedule_gold" value="{{ $bonusData->total_hrs_schedule_gold }}" name="total_hrs_schedule_gold" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_schedule_platinum" value="{{ $bonusData->total_hrs_schedule_platinum }}" name="total_hrs_schedule_platinum"  ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="total_hrs_schedule_diamond" value="{{ $bonusData->total_hrs_schedule_diamond }}" name="total_hrs_schedule_diamond" ></div>
    <div class="col-sm-2"><span>(weekly)</span></div>
    </div>

<br>

<div class="row">
    
      <div class="col-sm-2 text-right"><label for="male">Total acceptance rate</label></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="acceptance_silver" value="{{ $bonusData->acceptance_silver }}" name="acceptance_silver" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="acceptance_gold" value="{{ $bonusData->acceptance_gold }}" name="acceptance_gold" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="acceptance_platinum" value="{{ $bonusData->acceptance_platinum }}" name="acceptance_platinum" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="acceptance_diamond" value="{{ $bonusData->acceptance_diamond }}" name="acceptance_diamond" ></div>
    <div class="col-sm-2"><span>(weekly)</span></div>
    </div>
<br>

<div class="row">
    
      <div class="col-sm-2 text-right"><label for="male">Total cancellation rate</label></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="cancellation_silver" value="{{ $bonusData->cancellation_silver }}" name="cancellation_silver" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="cancellation_gold" value="{{ $bonusData->cancellation_gold }}" name="cancellation_gold" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="cancellation_platinum" value="{{ $bonusData->cancellation_platinum }}" name="cancellation_platinum" ></div>
    <div class="col-sm-2"><input type="text" class="form-control" id="cancellation_diamond" value="{{ $bonusData->cancellation_diamond }}" name="cancellation_diamond" ></div>
    <div class="col-sm-2"><span>(weekly)</span></div>
    </div>



  </div>

    
    
    
	<!--<div class="col-sm-12 commision">
    
   
	<div class="col-sm-3">
		<label for="male">Commission %</label>
	<input type="text" class="form-control" id="commission_silver" name="commission_silver" value="{!! $bonusData->commission_silver !!}" >
	</div>
	<div class="col-sm-3">
		<label for="male">Commission %</label>
	<input type="text" class="form-control" id="commission_gold" name="commission_gold" value="{{ $bonusData->commission_gold }}" >
	</div>
	<div class="col-sm-3">
		<label for="male">Commission %</label>
	<input type="text" class="form-control" id="commission_platinum" name="commission_platinum" value="{{ $bonusData->commission_platinum }}"  >
	</div>
	<div class="col-sm-3">
		<label for="male">Commission %</label>
	<input type="text" class="form-control" id="commission_diamond" name="commission_diamond" value="{{ $bonusData->commission_diamond }}" >
	<span>(weekly)</span>
	</div>

    
    
    
	<div class="col-sm-3">
		<label for="male">Total active hrs</label>
	<input type="text" class="form-control" id="total_hrs_silver" value="{{ $bonusData->total_hrs_silver }}" name="total_hrs_silver" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total active hrs</label>
	<input type="text" class="form-control" id="total_hrs_gold" value="{{ $bonusData->total_hrs_gold }}" name="total_hrs_gold" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total active hrs</label>
	<input type="text" class="form-control" id="total_hrs_platinum" value="{{ $bonusData->total_hrs_platinum }}" name="total_hrs_platinum" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total active hrs</label>
	<input type="text" class="form-control" id="total_hrs_diamond" value="{{ $bonusData->total_hrs_diamond }}" name="total_hrs_diamond" >
	<span>(weekly)</span>
	</div>
    
    
    
    
    
	<div class="col-sm-3">
		<label for="male">Total active hrs(Schedule)</label>
	<input type="text" class="form-control" id="total_hrs_schedule_silver" value="{{ $bonusData->total_hrs_schedule_silver }}" name="total_hrs_schedule_silver" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total active hrs(Schedule)</label>
	<input type="text" class="form-control" id="total_hrs_schedule_gold" value="{{ $bonusData->total_hrs_schedule_gold }}" name="total_hrs_schedule_gold" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total active hrs(Schedule)</label>
	<input type="text" class="form-control" id="total_hrs_schedule_platinum" value="{{ $bonusData->total_hrs_schedule_platinum }}" name="total_hrs_schedule_platinum"  >
	</div>
	<div class="col-sm-3">
		<label for="male">Total active hrs(Schedule)</label>
	<input type="text" class="form-control" id="total_hrs_schedule_diamond" value="{{ $bonusData->total_hrs_schedule_diamond }}" name="total_hrs_schedule_diamond" >
	<span>(weekly)</span>
	</div>
    
    
    

	<div class="col-sm-3">
		<label for="male">Total acceptance rate</label>
	<input type="text" class="form-control" id="acceptance_silver" value="{{ $bonusData->acceptance_silver }}" name="acceptance_silver" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total acceptance rate</label>
	<input type="text" class="form-control" id="acceptance_gold" value="{{ $bonusData->acceptance_gold }}" name="acceptance_gold" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total acceptance rate</label>
	<input type="text" class="form-control" id="acceptance_platinum" value="{{ $bonusData->acceptance_platinum }}" name="acceptance_platinum" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total acceptance rate</label>
	<input type="text" class="form-control" id="acceptance_diamond" value="{{ $bonusData->acceptance_diamond }}" name="acceptance_diamond" >
	<span>(weekly)</span>
	</div>


	<div class="col-sm-3">
		<label for="male">Total cancellation rate</label>
	<input type="text" class="form-control" id="cancellation_silver" value="{{ $bonusData->cancellation_silver }}" name="cancellation_silver" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total cancellation rate</label>
	<input type="text" class="form-control" id="cancellation_gold" value="{{ $bonusData->cancellation_gold }}" name="cancellation_gold" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total cancellation rate</label>
	<input type="text" class="form-control" id="cancellation_platinum" value="{{ $bonusData->cancellation_platinum }}" name="cancellation_platinum" >
	</div>
	<div class="col-sm-3">
		<label for="male">Total cancellation rate</label>
	<input type="text" class="form-control" id="cancellation_diamond" value="{{ $bonusData->cancellation_diamond }}" name="cancellation_diamond" >
	<span>(weekly)</span>
	</div>
 
	</div>-->
    
    
    
	<div class="col-sm-12 trigger-main">
		<button type="submit" class="btn btn-default trigger-but1">Update</button>
		<!--<button type="reset" class="btn btn-default trigger-but2">Undo Changes</button>-->
		</div>
	</div>
	
  </div>
 {!! Form::close() !!} 	
</div>        

@stop
@stop
