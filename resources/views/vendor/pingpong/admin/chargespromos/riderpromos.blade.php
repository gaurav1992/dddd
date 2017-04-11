
@extends($layout)
@section('title', 'Ride Promos')

@section('content')
<div class="content-part charge-blad">
<h1>Driver Promos</h1>
 @if(Session::has('message'))
	<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
	@endif
<ul id="errorContainer"></ul>	
 <ul class="nav nav-tabs">
    <li class="active"><a href="{!! route('riderpromos') !!}">Promos</a></li>
    <li><a  href="{!! route('riderbonus') !!}">Tier Bonuses</a></li>
  </ul>



<div class="tab-content main-tab">
    <div id="home" class="tab-pane fade in active col-sm-12 content-page active in">

{!! Form::open(array('class' => 'form-inline','method' => 'POST','id' => 'riderpromos','name'=>'riderpromos','files' => true)) !!} 

  <div class="col-sm-12">
<div class="col-sm-2 next-page">
      <label><input type="checkbox"class="checkbox" <?php if($promoData->referal_enable_for_5_10=='1') { echo "checked"; } else { echo ""; } ?> id="referal_enable_for_5_10" name="referal_enable_for_5_10">Enable</label>
</div>
    <div class="col-sm-4">
       <label>Amount received for referal</label>
		</div>
		<div class="col-sm-3 dezi-lab">
		<i class="fa fa-usd" aria-hidden="true"></i>
        <input type="text" class="form-control  same-control" value="<?php echo $promoData->referal_credit_for_5_10;?>" id="referal_credit_for_5_10" name="referal_credit_for_5_10" placeholder="DeziCredit">
		
      </div>

      <div class="col-sm-3 dezi-lab">
				<span class="text-left" ><label >For 5/10 rides within the 3 months</label></span>
      </div>
</div>
<div class="col-sm-12">
		  <div class="col-sm-2 next-page">
      <label><input type="checkbox" class="checkbox" <?php if($promoData->referal_enable_for_20=='1') { echo "checked"; } else { echo ""; } ?> id="referal_enable_for_20" name="referal_enable_for_20">Enable</label>
</div>
    <div class="col-sm-4 ">
       <label>Amount received for referal</label>
		</div>
		<div class="col-sm-3 dezi-lab">
		<i class="fa fa-usd" aria-hidden="true"></i>
        <input type="text" class="form-control  same-control" value="<?php echo $promoData->referal_credit_for_20;?>" id="referal_credit_for_20" name="referal_credit_for_20" placeholder="DeziCredit">
		
      </div>
      <div class="col-sm-3 dezi-lab">
				<span class="text-left"><label >For 20 rides within the 3 months</label></span>
      </div>
  </div>
	  <div class="col-sm-12 trigger-main">
		<button type="submit" class="btn btn-default trigger-but1">Save</button>
		<button type="reset" class="btn btn-default trigger-but2">Cancel</button>
		</div>
	 {!! Form::close() !!} 	
    </div> 
	
  </div>

</div>        

@stop
@section('style')
<style>
.text-left label{

	font-size:13px !important;
	
}
#referal_credit_for_5_10-error{font-size:14px!important;width:177px;text-align:center;}
</style>
@stop
@stop
