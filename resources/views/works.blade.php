@extends('frontend.common')
@section('content')
<!--  SECTION-1 -->

<div id="inner-header" class="container-fluid no-padding"> <img class="img-responsive" alt="test" src="{!! asset('public/images/bg-work.jpg') !!}">
	<div class="carousel-caption"> </div>
	<h3 class="page-heading">HOW IT WORKS</h3>
</div>

<section>
  <div class="container" id="about-us">
  <div class="page-header text-center info-text">
    <h2>DeziNow</h2>
    <p class="mtop-30">DeziNow is an On Demand Designated driver service that allows you to request drivers to your location through the IOS and Android devices.  The application utilizes technology to reach out to the closest driver available to your location to provide you with a cashless solution to your transportation needs.  The following is a step by step on the process of utilizing DeziNow to request a driver.
 </p>
</div>
<div class="tittle info-text"> 



<ul>
<li> Visit the DeziNow.com website or download the DeziNow application.  DeziNow registerstration and service requests are done via the app while only registration and other non service request functions can be on the website. ( drivers independent contractors or employees)
</li>
<li> To be connected to a driver you have to first register with DeziNow. Registering to become a member can be done both on the website or via the app.  To register you need your name, email , phone number, and billing information. Promo codes can be inputted also to receive a credit towards your ride. DeziNow accepts credit cards and paypal as valid forms of payment.
</li>
<li> Read and accept the terms and conditions. Please make sure to read the terms and conditions and understand them fully before continuing. </li>
<li> Click on the signup button. Once you sign up, you will receive a verification code via text. Enter the verification code in the next screen to continue with the sign up process.
</li>
<li> Fill in the information in the profile screen. There are information to fill out in the profile page that must be filled out in order to continue with the service.
</li>
<li> Set pickup location. The main page of the app is the screen where you set the pickup location or the location where you would like to meet the driver. You can type in an address in the pick up location box or moving the pin around. You will see the available drivers in the area. The eta inside the location pin shows you the eta of the closest available drivers.
 </li>
 
 <li> Set Destination location.  Choose your car. You can choose what car you drive on the screen by tapping the car or going into the menu and choosing “Your Cars”. Add a car by putting in the make and model and year and license plate number and whether it is an automatic or manual. Then click on the payment or choosing “payments” in the menu to add a payment method. You can choose the destination address by moving the pin or entering a location in the destination location box. Once all information has been entered, click on the “confirm pickup” button.</li>
  <li> Wait in front for your driver. You will be given an eta of your driver. You will receive a notification when your driver gets close to your location and then another notification when your driver has arrived. Please do not keep your driver waiting as the driver may leave if they cannot locate you.
  
  <ul> <li>
  
  If no drivers are available, please try again in a few minutes as drivers become available as they finish their jobs. 

  </li>
  <li>
  
  You will also be able to contact your driver via text or phone if you cannot locate your driver. 


  </li>
  
  <li> Cancellation of the request after 2 minutes after acceptance will result in a cancellation fee of $15. 
</li>
   <li> Eta’s are only a estimation and arrival times of the drivers may vary by city, time, and amount of requests.
</li>
  
  
  </ul>
  
  
</li>
   <li>
   On your way. Once the driver arrives, verify the driver and then inspect the vehicle once together to ensure there is no damage or to note existing damage to the vehicle. Verify that the driver matches the driver shown in the app. Then hand over the keys and get into the vehicle. 
   
   
   <ul>
   <li>Charges are calculated by time and distance. There is also a small minimum service fee and pick up fee that is added. Fares are different based on time of day and city, please check the app or website for a breakdown of the exact fares during that time.
</li>
   
   
   </ul>
   
   </li> <li>Cashless payments. Once the ride is over, the system will calculate the price by the criteria mentioned before. If the price comes out to less than the minimum amount, then the minimum amount of $15 will be charged. The amount will show after the ride is complete, and you have the option of adding a tip. Tips are appreciated but not required. You will also have the option of rating your driver. The driver rating is between 1 to 5 stars. 2 stars or lower will ensure that you will never be matched up again with that driver.  Cash is not required and should not be used. All transactions are done through the payment system through the application. 
</li> 
 
 
</ul>
<br>

 <p >Interested in driving with DeziNow? Click on the “Become a Driver” button at the top of the page or click on the “Earn with DeziNow” below to learn more.
 </p>

<p>
<button onclick="window.location.href='{!! asset('/earnWithDezi') !!}'" class="btn btn-primary green-btn-s pull-left">Earn with DeziNow</button> </p>

</div>
  </div>
</section>
@endsection




<script> (function( $ ) {

    //Function to animate slider captions 
	function doAnimations( elems ) {
		//Cache the animationend event in a variable
		var animEndEv = 'webkitAnimationEnd animationend';
		
		elems.each(function () {
			var $this = $(this),
				$animationType = $this.data('animation');
			$this.addClass($animationType).one(animEndEv, function () {
				$this.removeClass($animationType);
			});
		});
	}
	
	//Variables on page load 
	var $myCarousel = $('#carousel-example-generic'),
		$firstAnimatingElems = $myCarousel.find('.item:first').find("[data-animation ^= 'animated']");
		
	//Initialize carousel 
	$myCarousel.carousel();
	
	//Animate captions in first slide on page load 
	doAnimations($firstAnimatingElems);
	
	//Pause carousel  
	$myCarousel.carousel('pause');
	
	
	//Other slides to be animated on carousel slide event 
	$myCarousel.on('slide.bs.carousel', function (e) {
		var $animatingElems = $(e.relatedTarget).find("[data-animation ^= 'animated']");
		doAnimations($animatingElems);
	});  
    $('#carousel-example-generic').carousel({
        interval:3000,
        pause: "false"
    });
	
})(jQuery);	
</script>