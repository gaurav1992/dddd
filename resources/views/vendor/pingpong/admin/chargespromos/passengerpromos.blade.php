@extends($layout)
	
	@section('title', 'Passenger Promos')
		@section('content-header')
			<h1 style="text-align:center;">{!! $title or 'Passenger User Referral' !!}</h1>
			<div style="clear:both"></div>
			{!!@$message!!}
		@stop

		@section('customjavascript')
			<script>
				var ajaxpassengerPromos= "{!! route('ajaxpassengerPromos') !!}";
				var deletepassengerpromo="{!! route('deletepassengerpromo') !!}";
				var promo_code_check="{!! route('promo_code_check') !!}";
				var allpromo= "{!! route('passengerpromos') !!}";
				var PROMO_CSRF_TOKEN= "{{ csrf_token() }}";
			</script>
			<script src="{!! admin_asset('js/passenger_promo.js') !!}" type="text/javascript"></script>
		@stop

		@section('content')
			<div class="content-part">
				@if(Session::has('message'))
					<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
				@endif
				
				{!! Form::open(array('class' => 'form-inline','method' => 'POST','id' => 'passengerpromos','name'=>'passengerpromos','files' => true)) !!}
				
				<ul id="errorContainer"></ul>
				<style>
				#errorContainer {
					padding: 15px 30px;
				}
				</style>
                    <?php
                    $referralData = DB::table('dn_passenger_promo_code') ->select(array('*'))->where('dn_passenger_promo_code.type','referral')->orderby('dn_passenger_promo_code.id','DESC') ->first();
                    $anivarsaryData = DB::table('dn_passenger_promo_code') ->select(array('*'))->where('dn_passenger_promo_code.type','ani')->orderby('dn_passenger_promo_code.id','DESC') ->first();
                    $birthdayData = DB::table('dn_passenger_promo_code') ->select(array('*'))->where('dn_passenger_promo_code.type','birthday')->orderby('dn_passenger_promo_code.id','DESC') ->first();
                    $rideData = DB::table('dn_passenger_promo_code') ->select(array('*'))->where('dn_passenger_promo_code.type','new_rider_promotion')->orderby('dn_passenger_promo_code.id','DESC') ->first();

                    ?>
	<div class="col-sm-12">

        <div class="col-sm-2 pad">
			<label>
				<input type="checkbox" <?php  if($referralData->status=='1') { echo "checked"; } else { echo ""; } ?> class="checkbox" name="referal_enable" id="referal_enable" >Enable
			</label>
		</div>

		<div class="col-sm-4 pad">Amount received for referral</div>

		<div class="col-sm-3 pad"></div>

		<div class="col-sm-3 pad">
			<i class="fa fa-usd" aria-hidden="true"></i>
			<input type="text" class="form-control" name="referal_credit" id="referal_credit" placeholder="DeziCredit" value="<?php echo $referralData->amount;?>" style="text-align:right; ">
		</div>

		<tr>

        <div class="col-sm-2 pad">
			<label>
				<input type="checkbox" <?php if($anivarsaryData->status=='1') { echo "checked"; } else { echo ""; } ?> class="checkbox" name="anniversary_promo_enable" id="anniversary_promo_enable">Enable
			</label>
		</div>

		<div class="col-sm-4 pad">Amount for Anniversary:</div>

		<div class="col-sm-3 pad">
			<td><input type="text" class="form-control same-control" value="<?php echo $anivarsaryData->code;?>" name="anniversary_promo_code" id="anniversary_promo_code" placeholder="Promo Code"style="text-align:center;"></td>
		</div>

        <div class="col-sm-3 pad">
			<td><i class="fa fa-usd" aria-hidden="true"></i>
        	<input type="text" class="form-control same-control" value="<?php echo $anivarsaryData->amount;?>" name="anniversary_promo_credit" id="anniversary_promo_credit" placeholder="DeziCredit"style="text-align:right;"></td>
		</div>

		<div class="col-sm-2 pad ">
			<label><input type="checkbox" class="checkbox" <?php if($birthdayData->status=='1') { echo "checked"; } else { echo ""; } ?> name="birthday_promo_enable" id="birthday_promo_enable">Enable</label>
		</div>

		<div class="col-sm-4 pad">Amount for Birthday:</div>
		<div class="col-sm-3 pad">
			<input type="text" class="form-control same-control" value="<?php echo $birthdayData->code;?>" id="birthday_promo_code" name="birthday_promo_code" placeholder="Promo Code"style="text-align:center;">
		</div>

        <div class="col-sm-3 pad">
			<i class="fa fa-usd" aria-hidden="true"></i>
			<input type="text" class="form-control same-control" value="<?php echo $birthdayData->amount;?>" id="birthday_promo_credit" name="birthday_promo_credit" placeholder="DeziCredit"style="text-align:right;">
		</div>

		<div class="col-sm-2 pad">
			<label><input type="checkbox" class="checkbox" <?php if($rideData->status=='1') { echo "checked"; } else { echo ""; } ?> name="new_ride_promo_enable" id="new_ride_promo_enable">Enable</label>
		</div>

		 <div class="col-sm-4 pad">New Rider Promotion:</div>

		<div class="col-sm-3 pad">
			<input type="text" class="form-control same-control" value="<?php echo $rideData->code;?>" name="new_ride_promo_code" id="new_ride_promo_code" placeholder="Promo Code"style="text-align:center;">
		</div>

        <div class="col-sm-3 pad">
			<i class="fa fa-usd" aria-hidden="true"></i>
			<input type="text" class="form-control same-control" value="<?php echo $rideData->amount;?>" name="new_ride_promo_credit" id="new_ride_promo_credit" placeholder="DeziCredit"style="text-align:right;">
		</div>

	</div>

	<div class="col-sm-12">
		<hr class="border-top">
		<h1>Create Promo Code</h1>
	    
	    <div class="col-sm-2 pad">
			<label>
				<input type="checkbox" class="checkbox" <?php if($promoData->status=='1') { echo "checked"; } else { echo ""; } ?> id="promo_enable" name="promo_enable">Enable
			</label>
        </div>
        
		<div class="col-sm-6 pad">
			<input type="text" class="form-control credit same-control" value="" id="promo_code_1" name="promo_code" placeholder="Promo Code"style="text-align:right;">
			<br/><em style="color:red" class="duplicate_promo"></em>
		</div>
		
		<div class="col-sm-4 pad">
			<label><i class="fa fa-usd" aria-hidden="true"></i></label>
			<input type="text" class="form-control same-control" value="" id="promo_credit_1" name="promo_credit" autocomplete="off" placeholder="DeziCredit "style="margin-left:12px;text-align:right;">
			<br/><em style="color:red" class="invalid_pc"></em>
		</div>
		
		<div class="col-sm-4 anii">
			<div class="form-group dis-block">
				<label class="l_blck" for="Anniversary">Till</label>
				<?php
				if($promoData->valid_till=="" || $promoData->valid_till=='0000-00-00 00:00:00'){
					$promo_till_date="";
				} else {
					$promo_till_date=date('m/d/Y',strtotime($promoData->valid_till));
				}
				?>
				<input id="promo_till_date" name="promo_till_date" value=""  placeholder="MM/DD/YYYY" value="" readonly="true" type="text"class="same-control">
				<i aria-hidden="true" class="fa fa-calendar custom_cal"></i> 
			</div>
		</div>
        <div class="col-sm-2 pad">
            <label>
                <input type="checkbox" class="checkbox"   id="promo_multiple" name="promo_multiple">Use multiple time?
            </label>
        </div>
	    
	    <!--<button type="submit" class="btn btn-default">Add New Promo</button>-->
	    <div class="clearfix"></div>
		<hr class="border-top">
		
		<div class="col-sm-12 trigger-main">
			<button type="submit" class="btn btn-default trigger-but1">Save</button>
			<button type="reset" class="btn btn-default trigger-but2">Cancel</button>
		</div>
		
	</div>	
   {!! Form::close() !!}
</div>


	<div style="clear:both;margin-bottom:20px;"></div>
	{!! Form::open( ['files' => true,'route' => 'massbulk' ]) !!}

	<table class="table" id="passengerPromoCode" width="100%">
		<thead>			
			<th>Sr. No</th>
			<th>Promo Code</th>
			<th>Promo Till Date</th>
			<th>Promo Credit</th>
			<th>Status</th>
            <th>Use Multiple</th>
			<th class="text-center">Action</th>
		</thead>
		
	</table>
	{!! Form::close() !!}
@stop


