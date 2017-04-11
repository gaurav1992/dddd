<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="cache-control" content="no-store" />
<meta http-equiv="cache-control" content="must-revalidate" />
<meta http-equiv="expires"       content="0" />
<meta http-equiv="expires"       content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma"        content="no-cache" />
<title>DeziNow</title>
<link rel="shortcut icon" type="image/png" href="{!! asset('public/images/ms-icon.png') !!}"/>
<!-- Bootstrap -->

{!! HTML::style('public/css/bootstrap.min.css') !!}
{!! HTML::style('public/css/main.css') !!}
{!! HTML::style('public/css/font-awesome.css') !!}
{!! HTML::style('public/css/libs/animate.css') !!}
{!! HTML::style('public/css/bootstrap-datepicker.css') !!}
@yield('phonecodecss')
@yield('creditcss')
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
     
    <![endif]-->
<style type="text/css">
.datePicker .my-error-class {
    color: red;
    clear: both;
    display: table-footer-group;
}
.lodingDiv img {
    display: inline-block;
}
.uploadBtn{
    background: none;
    color: #000;
    border: 2px solid #95d60a;
    border-radius: 35px;
    padding: 0px 25px;
    line-height: 2;
    font-family: andadabold_italic;
    text-transform: capitalize;
    font-size: 15px;
    float: right;
    margin-right: 4%;
    z-index: 9999;
}
.hiddenInput{
  position: initial !important;
  z-index: -1 !important;
}
.img-upload{
  font-size: 25px !important;
}
body div, a, button, input, img{outline:none;}
  .btn-grn {
      float: right;
      background: #95d60a;
      border: none;
      border-radius: 35px;
      padding: 9px 30px;
      margin: 10px 0 0 0;
  }
  .uploadProfilePic input{display: none;}
  .profilePic{
      border-radius: 50%;
      width: 175px;
      height: 175px;
    }
  a.img-upload {
    cursor: pointer;
  }
  /*.browsefile .license_verification,.browsefile .proof_of_insurance{display: none;}*/
  .center-aline .col-sm-6:nth-child(2){margin-top: -50px !important;}
  .intl-tel-input.allow-dropdown {
      width: 100%;
  }
  .my-error-class{color: red;}
  .my-valid-class{color: green;}


  /*--css start for input fields--*/
        .center-block {
            float: none;
            margin-left: auto;
            margin-right: auto;
        }

        .input-group .icon-addon .form-control {
            border-radius: 0;
        }

        .icon-addon {
            position: relative;
            color: #555;
            display: block;
        }

        .icon-addon:after,
        .icon-addon:before {
            display: table;
            content: " ";
        }

        .icon-addon:after {
            clear: both;
        }

        .icon-addon.addon-md .glyphicon,
        .icon-addon .glyphicon, 
        .icon-addon.addon-md .fa,
        .icon-addon .fa {
            position: absolute;
            z-index: 2;
            left: 10px;
            font-size: 14px;
            width: 20px;
            margin-left: -2.5px;
            text-align: center;
            padding: 10px 0;
            top: 1px
        }

        .icon-addon.addon-lg .form-control {
            line-height: 1.33;
            height: 46px;
            font-size: 18px;
            padding: 10px 16px 10px 40px;
        }

        .icon-addon.addon-sm .form-control {
            height: 30px;
            padding: 5px 10px 5px 28px;
            font-size: 12px;
            line-height: 1.5;
        }

        .icon-addon.addon-lg .fa,
        .icon-addon.addon-lg .glyphicon {
            font-size: 18px;
            margin-left: 0;
            left: 11px;
            top: 4px;
        }

        .icon-addon.addon-md .form-control,
        .icon-addon .form-control {
            padding-left: 43px !important;
            float: left;
            font-weight: normal;
        }

        .icon-addon.addon-sm .fa,
        .icon-addon.addon-sm .glyphicon {
            margin-left: 0;
            font-size: 12px;
            left: 5px;
            top: -1px
        }

        .icon-addon .form-control:focus + .glyphicon,
        .icon-addon:hover .glyphicon,
        .icon-addon .form-control:focus + .fa,
        .icon-addon:hover .fa {
            color: #2580db;
        }
        .confmsg {
            text-align: center;
        }
        #termCondition label {
            float: right;
        }
        #myBar {
          position: absolute;
          display: none;
          width: 100%;
          height: 1%;
          background-color: #4CAF50;
        }
        #inner-header {
          margin-top: 0px;
        }
  /*--//css end for input fields--*/
</style>


</head>

<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-MCJDSP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MCJDSP');</script>
<!-- End Google Tag Manager -->

<body> 
<!-- HEADER -->
<!--  SECTION-1 -->
@yield('content')
</section>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 

{!! HTML::script('public/js/jquery-2.1.4.min.js'); !!}
{!! HTML::script('public/js/wow.min.js'); !!}
{!! HTML::script('public/js/bootstrap.js'); !!}
{!! HTML::script('public/js/bootstrap-datepicker.js'); !!}
{!! HTML::script('public/js/framework/jquery.validate.min.js'); !!}
@yield('creditjs')
@yield('maplocation')
@yield('drivercustom')
@yield('resendotp')
{!! HTML::script('public/js/framework/custom.js'); !!}
{!! HTML::script('public/js/framework/ajaxform.js'); !!}
@yield('phonecodejs')
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
 <!--script start for get current location-->
<script type="text/javascript">
$.get("http://ipinfo.io", function (response) {
  $(".location").text(response.loc);

var longlat = response.loc;
var breaklonglat = longlat.split(',');
var longlatHtml = '<div class="longlat">';
var count="1";
jQuery.each(breaklonglat, function( index, value ) {
  if(count == '1'){ var locname = 'latitude' }else{ var locname = 'longitude' };
  longlatHtml += '<input type="hidden" name="'+locname+'" class="'+locname+'" value="'+value+'"></input>';
  count++;
});
longlatHtml += '</div>';
jQuery(".loactionadata").append(longlatHtml);


    /*$("#ip").html("IP: " + response.ip);
    $("#address").html("Location: " + response.city + ", " + response.region);
    $("#details").html(JSON.stringify(response, null, 4));*/
}, "jsonp");
</script>
<script type="text/javascript">
    jQuery(".country-list li").on('click',function(){
      var phonecode = jQuery(this).find(".dial-code").text();
      jQuery("#phonecode").attr("value",phonecode);
      jQuery("#sendreferrelcode #phonecode1").attr("value",phonecode);
    });
</script>

<script>
jQuery('iframe#player').attr('src', function() {
    return this.src + '?title=0&byline=0&portrait=0&color=ffffff'
  });
</script>

 
@yield('customjavascript')
</body>
</html>