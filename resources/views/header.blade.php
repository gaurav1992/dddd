<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Index</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap-theme.css') }}" rel="stylesheet">
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link href="{{ asset('/css/full-slider.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/developer.css') }}" rel="stylesheet">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


 



  </head> 
  <body>
  @if (Session::has('custom_error') )
	<div class="alert alert-danger" style="text-align: center;" id="custom_error">
		<strong>Whoops!</strong> There were some problems with your input.<br><br>
		<ul>
			
			<li>{{ session('custom_error') }}</li>
			{{Session::forget('custom_error')}}
		</ul>
	</div>
	@endif	
	@if (Session::has('custom_success'))
			<div class="alert alert-success" style="text-align: center;" id="custom_success">
				{{ session('custom_success') }}
			</div>
			{{Session::forget('custom_success')}}
	@endif	

<?php if($routeController == 'HomeController' && $routeAction == 'index') {?>	
<header id="myCarousel" class="carousel slide home-header">
        <!-- Indicators -->
        <!-- <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol> -->
        <div class="top-bar">
          <div clas="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
            <a href="{{ url('/') }}"><img src="img/newlogo.png"></a>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
            <ul class="pull-right">
				@if (Auth::guest())
              <li class="h-about"><a href="#">About</a></li>
              <li><a href="{{ url('/auth/register') }}">Sign Up</a></li>
              <li><a href="{{ url('/auth/login') }}">Log In</a></li>
				@else 
				 <li class="h-about"><a href="#">About</a></li>
			 <li class=""><a href="{{ url('/create') }}">My page</a></li>
			 <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
				@endif
            </ul>
            </div>
          </div>
        </div><!-- end .top-bar -->

        <!-- Wrapper for Slides -->
        <div class="carousel-inner">
            <div class="item active">
                <!-- Set the first background image using inline CSS below. -->
                <div class="fill" style="background-image:url('img/img1132.png');"></div>
                <div class="carousel-caption">
                    <h2>GOING FOR OVERSEAS STUDIES?</h2>
                    <span>We have more than ### students who are going or have returned.</span>
                    <a href="#">Connect with  Students Now</a>
                </div>
            </div>
            <div class="item">
                <!-- Set the second background image using inline CSS below. -->
                <div class="fill" style="background-image:url('img/img1122.png');"></div>
                <div class="carousel-caption">
                    <h2>GOING FOR OVERSEAS STUDIES?</h2>
                    <span>We have more than ### students who are going or have returned.</span>
                    <a href="#">Connect with  Students Now</a>
                </div>
            </div>
            <div class="item">
                <!-- Set the third background image using inline CSS below. banner3-->
                <div class="fill" style="background-image:url('img/img1112.png');"></div>
                <div class="carousel-caption">
                    <h2>GOING FOR OVERSEAS STUDIES?</h2>
                    <span>We have more than ### students who are going or have returned.</span>
                    <a href="#">Connect with  Students Now</a>
                </div>
            </div>
        </div>



        <!-- Controls -->
        <a class="left carousel-control" href="#myCarousel" data-slide="prev">
            <span class="icon-prev"><img src="{{ asset('/img/left-arw.png') }}"></span>
        </a>
        <a class="right carousel-control" href="#myCarousel" data-slide="next">
            <span class="icon-next"><img src="{{ asset('/img/right-arw.png') }}"></span>
        </a>

        <div class="header-bottom">
       <div class="dropdown">
			  <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Check Out our <strong>University Guides!</strong>
			 <!--  <span class="caret"></span> --></button>
			  <ul class="dropdown-menu">
				<li><a href="#">Option 1</a></li>
				<li><a href="#">Option 2</a></li>
				<li><a href="#">Option 3</a></li>
			  </ul>
			</div>
        </div>

    </header>
<?php }else{ ?>

<div class="inner-Pageheader">
  <div clas="row">
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 inner-logo">
	<a href="{{ url('/') }}"><img src="{{ asset('/img/inner_logo.png') }}"></a>
	<div class="arrow-down"><img src="{{ asset('/img/dwn-arw.png') }}"></div>
	</div>
	<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
	<ul class="pull-right">
	 @if (Auth::guest())
	  <li class="active"><a href="{{ url('/auth/login') }}">Log In</a></li>
	<li><a href="{{ url('/auth/register') }}">Sign Up</a></li>
		@else
			 <li><a href="{{ url('/auth/logout') }}">Logout</a></li>

		@endif

	</ul>
	</div>
  </div>
</div><!-- end .top-bar -->
<?php } ?>
