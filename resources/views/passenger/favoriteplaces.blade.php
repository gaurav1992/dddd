@extends('frontend.common')

	@section('maplocation')
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src='https://maps.google.com/maps/api/js?sensor=false&libraries=places'></script>
	<!--<script type="text/javascript" src='http://maps.google.com/maps/api/js?key=AIzaSyDXG6rAyP0RS6__od-9Rd_ZM0gXp2wN3Tk&libraries=places'></script>-->
	  {!! HTML::script('public/js/framework/locationpicker.jquery.js'); !!}
	  {!! HTML::script('public/js/framework/mapPicker.js'); !!}
	@endsection

	@section('customjavascript')

	  <script type="text/javascript">
		var addPassengerAddress = "{!! route('addPassengerAddress') !!}";
		var deletePassengerAddress = "{!! route('deletePassengerAddress') !!}";
		var updatePassengerAddress = "{!! route('updatePassengerAddress') !!}";
		$(document).ready(function()
		{
			$('.addAdreesPassengerBtn').on('click',function(){
				var btnText = $(this).text();
			});
			$('.addAdreesPassengerBtn').removeAttr('disabled');
		
		});
	  </script>

	@stop

	@section('content')
	<style type="text/css">
	.address {
		position: static;
		background: transparent;
		color: black;
		padding: 2px 2px 2px 10px;
	}
	</style>
	<div class="container-fluid no-padding" id="inner-header"> <img src="{!! asset('public/images/form-head.jpg') !!}" alt="test" class="img-responsive">
		<div class="carousel-caption"> </div>
		<h3 class="page-heading">FAVORITE PLACES</h3>
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
   
    <div class="col-md-8 referral-cls driver-application favoriteplaces">
            <div id="examples">
              <div id="us2" class="mappicker" style="width:100%;height:270px;"></div>
                {!! Form::open(array('url' => 'addPassengerAddress','class' => 'form-horizontal form-location','id'=>'addPassengerAddress')) !!}  
                  <div class="form-group">
                    <label for="inputPassword" class="col-sm-3 control-label"> <i class="fa fa-map-marker"> </i> New Location</label>
                    <div class="col-sm-3">
                      <select class="form-control place_name" name="place_name">
                        <option value="HOME">HOME</option>
                        <option value="WORK">WORK</option>
                        <option value="OTHER">OTHER</option>
                      </select>
                    </div>
                    
                     <div class="col-sm-6">
                      <input type="text" class="form-control address" name="address" id="us2-address" placeholder="Address Line 1, Address Line 2, City">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-12 col-xs-12  text-center">
                      <input type="hidden" name="latitude" class="latitude" id="us2-lat">
                      <input type="hidden" name="longitude" class="longitude" id="us2-lon">
                      <input type="button" class="btn btn-primary green-btn-s addAdreesPassengerBtn" value="Save">
                    </div>
                  </div>
                {!! Form::close() !!}
            </div>
          <hr/>
  
<?php if(@$homeAddress['homeAddress']){ ?>
   <h4 class="add-h3">Home</h4>
   <div class="location-data-main">
<?php foreach ($homeAddress['homeAddress'] as $home) {
  ?>    
      <div class="add-car-cls">
        <div class="col-sm-6 left-cls-div">
        <?php
          $address =   !empty(@$home->address)?$home->address:'N/A'; 
          //$address = explode(',', @$full_address)[0];
          $city = !empty(@$home->city)?$home->city:'N/A'; 
		  $stateData=DB::table('dn_states')->where('state_code',$home->state)->first();
          $state =  !empty(@$stateData->state)?$stateData->state:'N/A'; 
          $zip =  !empty(@$home->zip)?$home->zip:'N/A'; 
        ?>
          <p><b>Address : </b><?php echo $address; ?></p>
          <p><b>City : </b><?php echo $city; ?></p>
          <p><b>State : </b><?php echo $state; ?></p>
          <p><b>Zip : </b><?php echo $zip; ?></p>
        </div>
        <div class="col-sm-6 right-cls-div ">
            @if($home->is_default == '1')
                <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-ok"></i> </div>
                @else
               <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-remove"></i> </div>
           @endif
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class='deletePassengerAddress' carAction='deletePassengerAddress' addressID='<?php echo $home->id; ?>' href=""><i class="fa fa-trash-o"></i></a></div>
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class="UpdateHomeAddress updateAddress" data-addId="<?php echo $home->id; ?>" data-defaultValue="<?php echo $home->is_default; ?>" data-Address="<?php echo $home->address; ?>" data-latNo="<?php echo $home->latitude; ?>" data-longNo="<?php echo $home->longitude; ?>" data-latID="lat-<?php echo $home->id; ?>" data-longID="long-<?php echo $home->id; ?>" data-lat="#lat-<?php echo $home->id; ?>" data-long="#long-<?php echo $home->id; ?>" data-map="#map_<?php echo $home->id; ?>" data-mapID="map_<?php echo $home->id; ?>" data-location="#location_<?php echo $home->id; ?>" data-locationID="location_<?php echo $home->id; ?>" data-toggle="modal" data-id="myModal_<?php echo $home->id; ?>" data-target="#myModal_<?php echo $home->id; ?>"><i class="fa  fa-edit mt-6"></i></a></div>
        </div>
        <div class="clearfix"></div>
      </div>
<?php } ?>
  </div>
<?php } ?>

<?php if($publicAddress['publicAddress']){ ?>
   <h4 class="add-h3">Public</h4>
   <div class="location-data-main">
<?php foreach ($publicAddress['publicAddress'] as $public) {
  ?>    
      <div class="add-car-cls">
        <div class="col-sm-6 left-cls-div">
          <?php
            
		  $address = !empty(@$public->address)?$public->address:'N/A'; 
          //$address = explode(',', @$full_address)[0];
          $city =!empty(@$public->city)?$public->city:'N/A';
		  $stateData=DB::table('dn_states')->where('state_code',$public->state)->first();
          $state = !empty(@$stateData->state)?$stateData->state:'N/A';
          $zip = !empty(@$public->zip)?$public->zip:'N/A';
          ?>
            <p><b>Address : </b><?php echo $address; ?></p>
            <p><b>City : </b><?php echo $city; ?></p>
            <p><b>State : </b><?php echo $state; ?></p>
            <p><b>Zip : </b><?php echo $zip; ?></p>
        </div>
        <div class="col-sm-6 right-cls-div ">
            @if($public->is_default == '1')
                <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-ok"></i> </div>
                @else
               <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-remove"></i> </div>
            @endif
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class='deletePassengerAddress' carAction='deletePassengerAddress' addressID='<?php echo $public->id; ?>' href=""><i class="fa fa-trash-o"></i></a></div>
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class="UpdateHomeAddress updateAddress" data-addId="<?php echo $public->id; ?>" data-Address="<?php echo $public->address; ?>" data-latNo="<?php echo $public->latitude; ?>" data-longNo="<?php echo $public->longitude; ?>" data-defaultValue="<?php echo $public->is_default; ?>" data-latID="lat-<?php echo $public->id; ?>" data-longID="long-<?php echo $public->id; ?>" data-lat="#lat-<?php echo $public->id; ?>" data-long="#long-<?php echo $public->id; ?>" data-map="#map_<?php echo $public->id; ?>" data-mapID="map_<?php echo $public->id; ?>" data-location="#location_<?php echo $public->id; ?>" data-locationID="location_<?php echo $public->id; ?>" data-toggle="modal" data-id="myModal_<?php echo $public->id; ?>" data-target="#myModal_<?php echo $public->id; ?>"><i class="fa  fa-edit mt-6"></i></a></div>
        </div>
        <div class="clearfix"></div>
      </div>
<?php } ?>
  </div>
<?php } ?>

<?php if(!empty(@$workAddress['workAddress'])){?>
   <h4 class="add-h3">Work</h4>
   <div class="location-data-main">
<?php foreach ($workAddress['workAddress'] as $work) {
  ?>    
      <div class="add-car-cls">
        <div class="col-sm-6 left-cls-div">
          <?php
              $address = !empty(@$work->address)?$work->address:'N/A';
              $city =  !empty(@$work->city)?$work->city:'N/A';
			  $stateData=DB::table('dn_states')->where('state_code',@$work->state)->first();
			  $state = !empty(@$stateData->state)?$stateData->state:'N/A';;
			   print_r($state);
			  $zip = !empty(@$work->zip)?$work->zip:'N/A';
          ?>
            <p><b>Address : </b><?php echo $address; ?></p>
            <p><b>City : </b><?php echo $city; ?></p>
            <p><b>State : </b><?php echo $state; ?></p>
            <p><b>Zip : </b><?php echo $zip; ?></p>
        </div> 
        <div class="col-sm-6 right-cls-div ">
            @if($work->is_default == '1')
                <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-ok"></i> </div>
                @else
               <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-remove"></i> </div>
            @endif
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class='deletePassengerAddress' carAction='deletePassengerAddress' addressID='<?php echo $work->id; ?>' href=""><i class="fa fa-trash-o"></i></a></div>
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class="UpdateHomeAddress updateAddress" data-addId="<?php echo $work->id; ?>" data-Address="<?php echo $work->address; ?>" data-latNo="<?php echo $work->latitude; ?>" data-longNo="<?php echo $work->longitude; ?>" data-defaultValue="<?php echo $work->is_default; ?>" data-latID="lat-<?php echo $work->id; ?>" data-longID="long-<?php echo $work->id; ?>" data-lat="#lat-<?php echo $work->id; ?>" data-long="#long-<?php echo $work->id; ?>" data-map="#map_<?php echo $work->id; ?>" data-mapID="map_<?php echo $work->id; ?>" data-location="#location_<?php echo $work->id; ?>" data-locationID="location_<?php echo $work->id; ?>" data-toggle="modal" data-id="myModal_<?php echo $work->id; ?>" data-target="#myModal_<?php echo $work->id; ?>"><i class="fa  fa-edit mt-6"></i></a></div>
        </div>
        <div class="clearfix"></div>
      </div>
<?php } ?>
  </div>
<?php } ?>

<?php if(!empty(@$otherAddress['otherAddress'])){ ?>
   <h4 class="add-h3">Other</h4>
   <div class="location-data-main">
<?php foreach ($otherAddress['otherAddress'] as $other) {
  ?>    
      <div class="add-car-cls">
        <div class="col-sm-6 left-cls-div">
          <?php
			
			$address = !empty(@$other->address)?$other->address:'N/A';
			$city =  !empty(@$other->city)?$other->city:'N/A';
			$stateData=DB::table('dn_states')->where('state_code',@$other->state)->first();
			$state = !empty(@$stateData->state)?$stateData->state:'N/A';
			$zip = !empty(@$other->zip)?$other->zip:'N/A';
          ?>
            <p><b>Address : </b><?php echo $address; ?></p>
            <p><b>City : </b><?php echo $city; ?></p>
            <p><b>State : </b><?php echo $state; ?></p>
            <p><b>Zip : </b><?php echo $zip; ?></p>
        </div>
        <div class="col-sm-6 right-cls-div ">
            @if($other->is_default == '1')
                <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-ok"></i> </div>
                @else
               <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><i class="glyphicon glyphicon-remove"></i> </div>
            @endif
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class='deletePassengerAddress' carAction='deletePassengerAddress' addressID='<?php echo $other->id; ?>' href=""><i class="fa fa-trash-o"></i></a></div>
          <div class="col-xs-4 col-sm-4 text-center location-icon-sty"><a class="UpdateHomeAddress updateAddress" data-addId="<?php echo $other->id; ?>" data-Address="<?php echo $other->address; ?>" data-latNo="<?php echo $other->latitude; ?>" data-longNo="<?php echo $other->longitude; ?>" data-defaultValue="<?php echo $other->is_default; ?>" data-latID="lat-<?php echo $other->id; ?>" data-longID="long-<?php echo $other->id; ?>" data-lat="#lat-<?php echo $other->id; ?>" data-long="#long-<?php echo $other->id; ?>" data-map="#map_<?php echo $other->id; ?>" data-mapID="map_<?php echo $other->id; ?>" data-location="#location_<?php echo $other->id; ?>" data-locationID="location_<?php echo $other->id; ?>" data-toggle="modal" data-id="myModal_<?php echo $other->id; ?>" data-target="#myModal_<?php echo $other->id; ?>"><i class="fa  fa-edit mt-6"></i></a></div>
        </div>
        <div class="clearfix"></div>
      </div>
<?php } ?>
  </div>
<?php } ?>

  </div>    
</div>
<?php }else{ ?>

<?php } ?>

<!-- Modal start for update home address-->
  <div id="" class="modal fade updateaddressmodel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close MapCloseBtn" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
			<h2 class="mt-0"> Edit Favorite Place </h2>
          <div class="lodingDiv" style="display:none;">
            <img src="{!! asset('public/img/loader.gif') !!}" alt="test" class="img-responsive lodingImg">
          </div>
          <div id="" class="mapDiv" style="width: 100%; height: 270px;"></div>
          {!! Form::open(array('url' => 'updatePassengerAddress','class' => 'form-horizontal form-location','id'=>'updatePassengerAddress')) !!}
            <div class="form-group">
              <label for="inputPassword" class="col-sm-3 control-label"> <i class="fa fa-map-marker"> </i> New Location</label>
               <div class="col-sm-9">
                <input type="text" class="form-control AddressForupdate address" value="" name="address" id="" placeholder="Address Line 1, Address Line 2, City">
              </div>
            </div>
                   <div class="form-group">
                      <label class="col-sm-3 col-xs-12 control-label">Favorite Place*</label>
                        <div class="col-sm-9 col-xs-12">
                        	<div class="fp-yn">
                           		<label class="radio-inline">
                                  <input type="radio" name="default_address" id="inlineRadio3" value="1"> <span> Yes</span>
                                </label>  
                            	<label class="radio-inline">
                              <input type="radio" name="default_address" id="inlineRadio4" value="0"> <span>No</span>
                            </label>
                            </div>
                      </div>
                  </div>
            <div class="form-group">
              <div class="col-sm-12 col-xs-12  text-center">
                <input type="hidden" name="latitude" class="latitude" id="">
                <input type="hidden" name="longitude" class="longitude" id="">
                <button class="btn btn-primary green-btn-s UpdateAdreesPassengerBtn" addId="">Update</button>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default MapCloseBtn" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
<!--//model end for update home address-->
</section>
@endsection
