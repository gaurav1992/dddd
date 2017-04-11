@extends('frontend.common')
@section('drivercustom')
{!! HTML::script('public/js/framework/drivercustom.js'); !!}
@stop
@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <div class="carousel-caption"> </div>
    <h3 class="page-heading">TIER LEVELS</h3>
</div>
<!--  SECTION-1 -->
<section>
<?php
if($myData){
  $createjoinind_date = new DateTime($myData['created_at']);
  $newjoinind_date = $createjoinind_date->format('m/d/Y');
?>  
  <div class="container mtop-30" id="driverprofileedit">
    
    @include('frontend.driversidebar')

    <div class="col-md-8">
    <h2 class="mt-0"> Tier Levels</h2>
      <p class="no_bold">Chart your tier levels, and find out about latest tier updates to earn more.</p>
      <div class="row rate-cls-sty">
        <div class="col-sm-6 text-center"> 
			@if($driver_tier_levels[0]->acceptance_rate !='')
			<span class="acceptance_rate">{{ $driver_tier_levels[0]->acceptance_rate }}</span>
			@else
			<span class="acceptance_rate">0%</span>
			@endif  Acceptance Rate 
		</div>
        <div class="col-sm-6 text-center">  @if($driver_tier_levels[0]->cancelation_rate !='')<span class="cancelation_rate">{{ $driver_tier_levels[0]->cancelation_rate }}</span>@else<span class="cancelation_rate">0%</span>@endif Cancellation Rate </div>
      </div>
      
      <div class="select-date-cls">
       <div class="col-sm-4 text-right select-title">Your Tier as on</div>
      <div class=" col-sm-6 col-sm-onset-2">
     <fieldset class="form-group">
              <div class='input-group date boder date-picker-one' id='selectpicker3'>
			   {!! Form::open(array('url' => 'tierlevelsweek')) !!}
                  {!! Form::text('selectweek',date("Y-m-d"),array('id'=>'sweek','class'=>'form-control','placeholder' => 'Select week','required')) !!}
                  <a class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                  </a>
				{!! Form::close() !!}
              </div>
            </fieldset>
      </div>
      
      </div>
      
      <div class="clearfix"></div>
      <hr/>
      <table class="table table-referral">
        <tbody id="chweek">
          <tr>
            <td>Tier Levels Achieved</td>
           @if($driver_tier_levels[0]->tiers_level == '0')
            <td>Normal </td>
		    @elseif($driver_tier_levels[0]->tiers_level == '1')
			 <td>silver </td>
		    @elseif($driver_tier_levels[0]->tiers_level == '2')
			 <td>Gold </td>
		    @elseif($driver_tier_levels[0]->tiers_level == '3')
			 <td>Platinum </td>
		    @elseif($driver_tier_levels[0]->tiers_level == '4')
			 <td>Diamond </td>
		   @else
		   <td>No Level</td>
		@endif
			
          </tr>
          <tr>
            <td>Active Hours</td>
           	@if($driver_tier_levels[0]->total_active_hours !='')
			<td>{{ $driver_tier_levels[0]->total_active_hours }}</td>
            @else 
		   <td>0 Hour</td>
		 @endif
          </tr>
          <tr>
            <td>Active Hours during Rush Hour</td>
			@if($driver_tier_levels[0]->scheduled_hours !='')
			<td>{{ $driver_tier_levels[0]->scheduled_hours }}</td>
            @else 
		   <td>0 Hour</td>
		@endif
          </tr>
          <tr>
            <td>Acceptance Rate</td>
			@if($driver_tier_levels[0]->acceptance_rate !='')
		   <td>{{ $driver_tier_levels[0]->acceptance_rate }}</td>
	        @else
            <td>0% </td>
		@endif
          </tr>
          <tr>
            <td>Cancellattion</td>
		@if($driver_tier_levels[0]->cancelation_rate !='')
            <td>{{ $driver_tier_levels[0]->cancelation_rate }}</td>
		 @else
            <td>0% </td>
		@endif
          </tr>
        </tbody>
      </table>
      <div class="clearfix"></div>
    </div>    
  </div>
<?php }else{ ?>

<?php } ?>  
</section>
@endsection
