@extends($layout)
@section('title', 'Notifications')
@section('content-header')
  <h1 style="text-align:center;">
    {!! $title or 'Manage Notifications' !!} 

  </h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('style')
<style>
.col-sm-12.new-city-box {
  max-height: 500px;
  overflow: auto;
}

</style>
@stop
@section('customjavascript')
<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<script>


$(".sendNotification").click(function () {
    
  
    if (!$('input[name^=city_id]').is(":checked")) {
        console.log("first");
        alert("No city selected");
        return false;
    }
    else if (!$('#notify_passanger').is(":checked") && !$('#notify_driver').is(":checked")) {
       console.log("second");
       alert("Please select to Passanger/Driver option");
        return false;
    }
    else if  (!$('.test input[name^=notify]').is(":checked")) {
        console.log("third");
        alert("Please select via msg/email/push notification");
        return false;
    }else{
      console.log("else");
      return true;
    }


  

});


var cityAjax = "{!! route('cityAjax') !!}";

 // Setup form validation on the #register-form element
    $("#manage_charges_form").validate({
    
        // Specify the validation rules
        rules: {
            message: {
                required: true
            }
        },
        
        // Specify the validation error messages
        messages: {
            message: {
                required: "Please enter your message here."
            }
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });
	
	/*FOR CALLING ALL CITIES
	*/
	var driverChargesurl = "{!! route('driverCharges') !!}";
$( document ).on( "change", "#stateCh", function(){
		var stateCode= $(this).val();
		//console.log(stateCode);
		$.ajax({
			 type:'get',
			 url:driverChargesurl,
			 data:'stateCode='+stateCode+'&_token='+CSRF_TOKEN,
			 beforeSend: function( xhr ) {
				$('body').append('<div id="loadering"><img src="'+loadingImage+'"></div>');
				//return false;
			},
			 success:function(returnData)
			 {
				$('#loadering').remove();
				 //console.log(returnData);
				// var parsedJson=$.parseJSON(returnData);
				$( ".cityblock" ).html(returnData);
				
			 }
		});
	});	
</script>


@stop

@section('content')
<div class="blade-3">
<div class="content-part container-part-1">

  @if(Session::has('message'))
  <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
  @endif

  <!-- City Box starts here -->
  <?php echo  Form::open(array('url' => '#', 'method' => 'post', 'id' => 'manage_charges_form')); ?>

      <input type="hidden" name='submit' value='1'>

  <div class="col-sm-12 new-city-box">
    
    <div class="search-box pull-left">
      <input type="search" name='search_city' id="search_city">
    </div>

    
	 <div class="button-left pull-right">
	
	
	<select id="stateCh" class="form-control" name="state"> 
		<option value="">---State---</option>
		@foreach($states as $state)
		<option  value="{{$state->state_code}}"> {{ $state->state }}</option>
		@endforeach
	</select>
    
    	<a type="button" class="btn btn-default select-all-city">Select All</a>
    </div>

    <!-- All City Section -->
    <div class='all-city-section cityblock'>

      


    </div>
    </div>
    <!-- /All City Section -->
      



  <div class="col-sm-12 current-price pad">

    <div class="row no-margin">
      <div class="col-sm-3"><strong>To : </strong></div>
      <div class="col-sm-3">
        <div class="checkbox">
          <label><input type="checkbox" name='notify_passanger' id="notify_passanger" value='1' class='city-check'>Passanger</label>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="checkbox">
          <label><input type="checkbox" name='notify_driver' id='notify_driver' value='1' class='city-check'>Driver</label>
        </div>
      </div>
    </div>

    <hr>
    <div style="clear:both;"></div>
    <div class="row no-margin test">
      <div class="col-sm-3"><strong>Via : </strong></div>
      <div class="col-sm-3">
        <div class="checkbox">
          <label><input type="checkbox" name='notify[]' value='sms' class='city-check'>SMS</label>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="checkbox">
          <label><input type="checkbox" name='notify[]' value='email' class='city-check'>Email</label>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="checkbox">
          <label><input type="checkbox" name='notify[]' value='push_notification' class='city-check' >Push Notification</label>
        </div>
      </div>
    </div>

    <hr>

    <div class="row no-margin">
      <div class="form-group col-sm-12">
          <label>Textarea</label>
          <textarea name="message" class="form-control" rows="3" placeholder="Enter ..." required='required'></textarea>
      </div>
    </div>

  </div>

</div>

  <div class="col-sm-12 trigger-main">
    <button type="submit" class="sendNotification btn btn-default trigger-but1">Send</button>
    <button type="submit" class="btn btn-default trigger-but2">Cancel</button>
  </div>

<?php echo Form::close(); ?>

@stop