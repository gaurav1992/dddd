
@extends('frontend.common')
<!--slider code start here-->
@section('slider')

  <div id="first-slider">
    <div id="carousel-example-generic" class="carousel slide carousel-fade"> 
      <!-- Indicators -->
      <ol class="carousel-indicators">
        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
      </ol>
      <!-- Wrapper for slides -->
      <div class="carousel-inner" role="listbox"> 
        <!-- Item 1 -->
        
        <div class="item active slide2">
          <div class="row">
            <div class="container">
              <div class="col-md-7 text-left">
                <h3 data-animation="animated bounceInDown">Introducing DeziNow</h3>
                <h4 data-animation="animated bounceInUp">Sign up Now!</h4>
                <a href="#" class="slider-button" data-animation="animated bounceInRight" data-toggle="modal" data-target="#myModal">App Coming Soon</a> 
              </div>
              <!--
              <div class="col-md-5 text-right"> <img   data-animation="animated bounceInRight wow" src="{!! asset('public/images/hand-berry.png') !!}"/> </div>
              -->
            </div>
          </div>
        </div>
        <!-- Item 2 -->
        <div class="item slide3">
          <div class="row">
            <div class="container">
              <div class="col-md-7 text-left">
                <h3 data-animation="animated bounceInDown">Introducing DeziNow </h3>
                <h4 data-animation="animated bounceInUp">Sign up Now!</h4>
        <a href="#" class="slider-button" data-animation="animated bounceInRight" data-toggle="modal" data-target="#myModal">App Coming Soon</a>                
        </div>
            </div>
          </div>
        </div>
        <!-- Item 3 -->
        <div class="item slide4">
          <div class="row">
            <div class="container">
              <div class="col-md-7 text-left">
                <h3 data-animation="animated bounceInDown">Introducing DeziNow</h3>
                <h4 data-animation="animated bounceInUp">Sign up Now!</h4>
                <a href="#" class="slider-button" data-animation="animated bounceInRight" data-toggle="modal" data-target="#myModal">App Coming Soon</a> 
              </div>
              
            </div>
          </div>
        </div>
        <!-- End Item 4 --> 
          
      </div>
      <!-- End Wrapper for slides--> 
      <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev"> <i class="fa fa-angle-left"></i><span class="sr-only">Previous</span> </a> <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next"> <i class="fa fa-angle-right"></i><span class="sr-only">Next</span> </a> </div>
  </div>
@endsection
<!--  SECTION-1 -->
@section('content')
   <section>
  <div class="col-lg-12 page-header text-center">
    <h2>What is DeziNow?</h2>
  </div>
  <div class="container ">
    <div class="row row-bottom-padded-lg" id="about-us">
      <div class="col-md-6 to-animate how-work">
        <p class="abt-title">DeziNow is a On Demand Referral Service platform (app) where we connect customers with drivers.
 </p>
 
 <p class="abt-title">We make it easy. You request for a driver on the app and the driver drives you and your car to your desired location.
</p>

<p class="abt-title">Now signing up drivers for our site.
</p>


<p class="abt-title">You can earn money with DeziNow. We are taking applications now to become a driver on our site. A fun chance to earn money and meet new people.
</p>

<p class="abt-title small-center">
<a href="{!! asset('/earnWithDezi') !!}" class="btn btn-primary green-btn-s pull-left a-outline">Learn More</a> </p>

       <!-- <ul>
          <li> <span> 01 </span>Customer utilizes the app to request a driver.</li>
          <li> <span> 02 </span> Request will go out to nearest drivers until request is accepted.</li>
          <li> <span> 03 </span> Driver will arrive at the location where the customer is waiting. 
            Drivers requested through the DeziNow App can be 
            identified via a DeziNow badge. </li>
          <li> <span> 04 </span>The driver will toke the keys from the customer.</li>
          <li> <span> 05 </span> The driver will carefully and safely drive the customer to the 
            desired location.</li>
          <li> <span> 06 </span> Once the driver arrives at the desired location, the app will 
            automatically calculate the amount for the trip</li>
          <li> <span> 07 </span> No cash needed because all transaction will be done via the app.</li>
        </ul>-->
      </div>
      <div class="col-md-6 wow slideInRight" data-wow-delay="2s"> <img src="{!! asset('public/images/mobiles-screens.jpg') !!}" class="img-responsive img-rounded" alt="Free HTML5 Template"> </div>
    </div>
  </div>
  <div class="container-fluid no-padding">
    <div id="demo">
      <div class=" video-tittle text-center ">
        <h2 class="">The DeziNow Experience</h2>
      </div>
      <div class="container">
        <div class="col-md-8 col-md-offset-2 ">
          <div class="video-container" > 
            <!-- <iframe src="http://player.vimeo.com/video/70984663"></iframe>  -->
            <iframe id="player" width="798" height="411" src="https://player.vimeo.com/video/177882185" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
            <!-- <img src="{!! asset('public/images/girl-vieo.jpg') !!}"/>  -->
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- /container -->
  
  <div class="container ">
    <div class="row row-bottom-padded-lg" id="">
      <div class="col-md-12 section-heading text-center"> </div>
      <div class="col-md-6 animated bounceInLeft wow" data-wow-delay="5s" > <img src="{!! asset('public/images/img-2.png') !!}" class="img-responsive img-rounded" alt="Free HTML5 Template"> </div>
      <div class="col-md-6 to-animate">
        <div class="page-header fqa">
          <h2>Faq’s</h2>
        </div>
        <div class="faqs">
          <div class="question">
            <h4>Q1. Can I use a debit card for DeziNow payments?
</h4>
            <p>Not all debit cards are compatible with the billing system. If you get an error message while trying to add the info, you'll need to find a different option.
 </p>
          </div>
          <div class="question">
            <h4>Q2. Can I request an DeziNow at any time? </h4>
            <p>Yes. DeziNow operates 24/7. That said, more drivers will be online during busy periods. It's not unusual to find no drivers available in the middle of the night in less populated areas.
 </p>
          </div>
          <div class="question">
            <h4>Q3. How does the DeziNow payment method work?
 </h4>
            <p>Visit the main menu of the DeziNow app and select the Payment option or credit card icon to enter your payment information. Once you input and save a credit card, PayPal, or other payment info in the app, all fee payments happen automatically.
 </p>
          </div>
          <div class="question">
            <h4>Q4. How can I call for two or more DeziNow drivers?
 </h4>
            <p>Each DeziNow account can only request one driver. Someone else in your party will need to order the second driver from another DeziNow account.
 </p>
          </div>
          <div class="question">
            <h4>Q5. How can I verify that the person who showed up is my DeziNow driver?
 </h4>
            <p>Ask the driver "Who are you here to pick up?" Your real driver will know the name on your DeziNow account.
You can also check the person against their picture in the driver profile in the app. </p>
          </div>
          <div class="question">
            <h4>Q6. Where do DeziNow credits come from?
 </h4>
            <p>Earn DeziNow credits by inviting friends to join DeziNow using the invite code in your app. Once the credits are added to your account, they are automatically deducted to pay for your fares.
 </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / CONTAINER--> 
</section>
@endsection


<?php/*
@section('form')
<?php
if (Auth::check()) {
      echo $email = Auth::user()->email;
      echo $id = Auth::id();

      ?>
<a href="{!! URL::to('logout') !!}">Logout</a>
<?php     
    }
?>

@endsection


@section('content')
@endsection
*/?>