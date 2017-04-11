@extends('frontend.common')
@section('phonecodecss')
  {!! HTML::style('public/css/libs/prism.css') !!}
  {!! HTML::style('public/css/libs/intlTelInput.css') !!}
  {!! HTML::style('public/css/libs/demo.css') !!}
  {!! HTML::style('public/css/libs/isValidNumber.css') !!}
@endsection

@section('phonecodejs')
  {!! HTML::script('public/js/framework/prism.js'); !!}
  {!! HTML::script('public/js/framework/intlTelInput.js'); !!}
  {!! HTML::script('public/js/framework/isValidNumber.js'); !!}
@endsection

 @section('customjavascript')

  <script type="text/javascript">
    var referralcode = "{!! route('referralcode') !!}";
  </script>

  @stop


@section('content')
<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
    <h3 class="page-heading">Referral History</h3>
	<div class="carousel-caption"> </div>    
</div>
<!--  SECTION-1 -->
<section>
<?php
if($myData){
  $createjoinind_date = new DateTime($myData['created_at']);
  $newjoinind_date = $createjoinind_date->format('m/d/Y');
?>  
  <div class="container mtop-30" id="driverprofileedit">

    @include('frontend.passengersidebar')
    
    <div class="col-md-8 referral-cls">
      @if(Session::has('sendreferral'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('sendreferral') }}</p>
      @endif
      <h2 class="mt-0">Referral History</h2>
      <p>When you refer someone to become a passenger, you(and your friend) will recieve ${{$passenger_promos}} credit after successful payment of first ride(in the first 3 month).</p>
      <div class="refr-code-tag"> You referral code is <span class="refe-code"><?php echo $myData['passenger_referral_code']; ?></span></div>
      <div class="clearfix"></div>
      <ul class="list-inline referral-social">
        <li>
          <a href="#" data-toggle="modal" data-target="#myModalReferal">
            <img src="{!! asset('public/images/icon-text.png') !!}" alt="#" class="text" /><span>Text</span>
          </a>
        </li>
        <li>
          <a target="_blank" href="https://mail.google.com/mail/?view=cm&fs=1&tf=1&su=Dezinow Referral&body=<?php echo $myData['first_name']; ?> has sent you $10 in free DeziNow Credit.Sign up at www.dezinow.com and use code: <?php echo $myData['passenger_referral_code']; ?>">
            <img src="{!! asset('public/images/icon-email.png') !!}" alt="#" class="email" /><span>Email</span>
          </a>
        </li>
        <li>
          <a href="https://api.addthis.com/oexchange/0.8/forward/twitter/offer?url=http%3A%2F%2Fdezinow.com&title=<?php echo $myData['first_name']; ?>%20has%20sent%20you%20$10%20in%20free%20DeziNow%20Credit.Sign%20up%20at%20www.dezinow.com%20and%20use%20code:%20<?php echo $myData['passenger_referral_code']; ?>%20" target="_blank">
            <img src="{!! asset('public/images/icon-twt.png') !!}" alt="#" class="twt" /><span>Twitter</span>
          </a>
      </li>
        <li>
          <a href="https://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=http%3A%2F%2Fdezinow.com&title=<?php echo $myData['first_name']; ?>%20has%20sent%20you%20$10%20in%20free%20DeziNow%20Credit.Sign%20up%20at%20www.dezinow.com%20and%20use%20code:%20<?php echo $myData['passenger_referral_code']; ?>%20" target="_blank">
            <img src="{!! asset('public/images/icon-fb.png') !!}" alt="#" class="fb" /> <span>Facebook</span>
          </a>
      </li>
      </ul>
      <hr/>

      <h3> Referrals Completed </h3>
        <div class="location-data-main">
        
          <?php 
		  $i=0;
            if($myreferralcomplete['complete']){
            foreach ($myreferralcomplete['complete'] as $k=>$complete) {
				$i++;
				?>
			<div class="add-car-cls" onclick="toggle(<?php echo $i;?>)">            
              <div class="col-sm-12 left-cls-div">
                <p  data-toggle="collapse" ><span class="text-primary">Passenger Name:</span>{{@$complete->referralUserName}}   </p>
              </div>
              <div class="clearfix"></div>
             
        		   <div id="<?php echo $i;?>" style="display:none;">
                        <div class="ddd-c-outer">                
                    <h4 class="text-info">Ride Details of {{@$complete->referralUserName }} </h4>
                    <?php
                    foreach($ridesCmpltd[$k] as $key=>$value)
                    { ?>
                            <div class="dd-drop-cont">
                                <div class="add-car-cls">
                          <div class="col-sm-12 left-cls-div left-cls-div-2">
                            <p><span class="text-primary">Ride Id :</span>{{@$value->id}} </p>
                            
                            <p><span class="text-primary">Driver Name :</span>{{ @$value->drname}} </p>
                            <p><span class="text-primary">Ride City :</span>{{@$value->city_name}} </p>
                            <p><span class="text-primary">Ride Status :</span>@if(@$value->status==1) In Process @elseif(@$value->status==2) Complete @elseif(@$value->status==3) cancelled  @elseif(@$value->status==4) No response @elseif(@$value->status==5) cancelled  ride request @endif </p>
                            
                          </div>
                          <div class="clearfix"></div>
                        </div>
                            </div>                      
                        
                    
                <?php }	?> 
             
                </div>
                </div>
             </div>
          <?php } ?>
          <?php }else{ ?>
            <p>No Data Found</p>
          <?php } ?>
        </div>
 
      <h3> Referrals not Completed </h3>
        <div class="location-data-main">
          <?php 
		  $j=1000;
            if($myreferralpending['pending']){
            foreach ($myreferralpending['pending'] as $k=>$pending) {
				$j++;
          ?>
            <div class="add-car-cls" onclick="togglse(<?php echo $j;?>)">
              <div class="col-sm-12 left-cls-div">
                <p><span class="text-primary">Passenger Name:</span>{{@$pending->referralUserName}} </p>
              </div>
              <div class="clearfix"></div>
           
            <div id="<?php echo $j;?>" style="display:none;">
             <div class="ddd-c-outer">                
			<h4 class="text-info">Ride Details of {{@$pending->referralUserName }} </h4>
			<?php if(!empty(@$ridesremaind[$k])){
				foreach($ridesremaind[$k] as $key=>$value)
				{ ?> 
                	      <div class="dd-drop-cont">
						<div class="add-car-cls">
							  <div class="col-sm-12 left-cls-div left-cls-div-2">
						<p><span class="text-primary">Ride Id :</span>{{@$value->id}} </p>
						<p><span class="text-primary">Driver Name :</span>{{ @$value->drname}} </p>
						<p><span class="text-primary">Ride City :</span>{{@$value->city_name}} ></p>
						<p><span class="text-primary">Ride Status :</span>@if(@$value->status==1) In Process @elseif(@$value->status==2) Complete @elseif(@$value->status==3) cancelled  @elseif(@$value->status==4) No response @elseif(@$value->status==5) cancelled  ride request @endif </p>
						
					  </div>
					        <div class="clearfix"></div>
					   </div>
                    </div>
			<?php }	}?> 
            </div>
            </div>
            </div>
          <?php } ?>
          <?php }else{ ?>
            <p>No Data Found</p>
          <?php } ?>
        </div>

      <hr/>
    <table class="table table-referral">
        <tbody>
          <tr>
            <td class="col-sm-9">Referral bonus for 2016</td>
            <td class="col-sm-3">
              <?php 
                if($myBonus['bonus']){
                  $TOTALaMOUNT = $myBonus['bonus'][0]->creditAmount;
                  if($TOTALaMOUNT ==''){
              ?>
                <p>NA</p>  
              <?php }else{ ?>
                <p>$ <?php echo $TOTALaMOUNT; ?></p>
              <?php } } ?>
            </td>
          </tr>
        </tbody>
    </table>
    </div>   
  </div>
<?php }else{ ?>

<?php } ?>  
</section>

<!-- Modal -->
<div id="myModalReferal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>You referral code is : - <?php echo $myData['passenger_referral_code']; ?></p>
              <div class="panel-body">                              
                {!! Form::open(array('url' => 'referralcode','class' => 'form','id'=>'sendreferrelcode')) !!}
                  <fieldset>
                    <div class="form-group">
                      <div id="result">
                          <input id="phone" type="text" name="phone_number" placeholder="Mobile Number" class="form-control span6 referralContact">
                      </div>
                    </div>
                    <div class="form-group">
                      <input id="phonecode1" name="phonecode" type="hidden" value="+1">
                      <input id="referralCode" name="referralCode" type="hidden" value="<?php echo $myData['first_name']; ?> has sent you $10 in free DeziNow Credit.Sign up at www.dezinow.com and use code: <?php echo $myData['passenger_referral_code']; ?>">
                      {!! Form::submit('Send referral code', array('class'=>'btn btn-lg btn-primary btn-block GRN sendreferrelcode')) !!}
                    </div>
                  </fieldset>
                {!! Form::close() !!} 
              </div>
          <div class="lodingDiv" style="display:none;">
            <img src="{!! asset('public/img/loader.gif') !!}" alt="test" class="img-responsive lodingImg">
          </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

<script>
	function toggle(id){		
		$("#"+id).toggle("slow");
	}
	function togglse(id){
		
		$("#"+id).toggle("slow");
	}				
</script>