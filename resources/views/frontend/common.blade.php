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
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MCJDSP');</script>
<!-- End Google Tag Manager -->
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
.EditPhoneNumberDiv{
    width: 96%;
    margin: 0px auto;
    margin-left: 2%;
    background: #EEEEEE;
}
.updatepassengerprofilebtn{width: 50%;}
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
  /*--//css end for input fields--*/
</style>


</head>

<body>
  <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MCJDSP"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) —>
<input type="hidden" id="refresh" value="no">
<!-- HEADER -->



<header>
  <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container"> 
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="fa fa-bars fa-lg"></span> </button>
        <a class="navbar-brand" href="{!! asset('/') !!}"> <img src="{!! asset('public/images/logo.png') !!}" alt="" class="logo"> </a> </div>
      
      <!-- Collect the nav links, forms, and other content for toggling -->
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
        <?php if(Auth::check() ){ 

          $id = Auth::id();
            $user_detail = $users = DB::table('role_user')
              ->select(array('dn_users.*'))
              ->leftjoin('dn_users', 'role_user.user_id', '=', 'dn_users.id')  
              ->where('dn_users.id' ,$id) 
              ->where('role_user.role_id' ,'4')
              ->get();
             if(empty($user_detail)) {
          ?>
              <a href="{!! asset('/login/becomedriver') !!}"><button class="btn green-btn-s pull-right becomedriver">Become a Driver</button></a>

        <?php }} else  { ?>
          
            
              <a href="{!! asset('/login/becomedriver') !!}"><button class="btn green-btn-s pull-right becomedriver">Become a Driver</button></a>

        <?php  }?>

        <br/>

        <ul class="nav navbar-nav navbar-right">
          <li class="{!! Request::is('about') ? 'active' : '' !!}"><a href="{!! asset('/about') !!}">about us</a></li> 
          <!-- <li class="{!! Request::is('features') ? 'active' : '' !!}"><a href="#features">pricing</a></li>
          <li class="{!! Request::is('faqs') ? 'active' : '' !!}"><a href="{!! asset('/faqs') !!}">faqs</a></li> -->
         
          <li class="{!! Request::is('faq') ? 'active' : '' !!}"><a href="{!! asset('/faq') !!}">Faq</a></li>
          <li class="{!! Request::is('howItWorks') ? 'active' : '' !!}"><a href="{!! asset('/howItWorks') !!}">How it Works</a></li>
           <li class="{!! Request::is('contact') ? 'active' : '' !!}"><a href="{!! asset('/contact') !!}">contact us</a></li>
       <!--   <li class="{!! Request::is('earnWithDezi') ? 'active' : '' !!}"><a href="{!! asset('/earnWithDezi') !!}">Earn with DeziNow</a></li>-->
          <?php if(Auth::check()){ ?>
            <li class="{!! Request::is('login') ? 'active ' : '' !!} nav-sep"><a href="{{ URL::to('logout') }}">Logout</a></li>
          <?php }else{ ?>
            <li class="{!! Request::is('login') ? 'active' : '' !!} nav-sep loginuser"><a href="{!! asset('/login') !!}">Login</a> </li>
          <?php } ?>

          <?php if(Auth::check()){ ?>
            <li class="{!! Request::is('/editpassenger') ? 'active' : '' !!}"><a class="getApp" href="{!! asset('/editpassenger') !!}">My Page</a></li>
          <?php }else{ ?>
            <li class="{!! Request::is('signup') ? 'active' : '' !!}"><a class="getApp" href="{!! asset('/signup') !!}">signup</a></li>
          <?php } ?>         

        </ul>
      </div>
      <!-- /.navbar-collapse --> 
    </div>
    <!-- /.container--> 
  </nav>
  @yield('slider')
</header>
<!-- / HEADER --> 

<!--  SECTION-1 -->
@yield('content')


<!-- FOOTER -->
<section class="footer">
  <div class="container-fulid socialize">
  <div class="container">
  <div class="col-sm-6 center-dix">
    <div class="footer-menu">
      <ul>
        <li><a href="{!! asset('/about') !!}">about us</a></li>
        
          <li><a href="{!! asset('/faq') !!}">Faq</a></li>
          <li><a href="{!! asset('/howItWorks') !!}">How it Works</a></li>
        
        <li><a href="{!! asset('/contact') !!}">contact us</a></li>
      <div class="social-icons">
        <li class="no-border"> <a href="https://www.facebook.com/profile.php?id=100012778486984"><img src="{!! asset('public/images/facebook-icon.png') !!}"/></a> </li>
        <li class="no-border"> <a href="https://twitter.com/@dezinow1"> <img src="{!! asset('public/images/twitter-icon.png') !!}"/> </a> </li>
        <li class="no-border"><a href="https://www.instagram.com/dezinow1/"> <img src="{!! asset('public/images/instagram.png') !!}"/>  </a></li>
        </div>
      </ul>
    </div>
    </div>
    <div class="col-sm-4">
     <ul class="appshare">
     <li class="app-sh-li"> <a href="#" data-toggle="modal" data-target="#appModal"><img class="img-responsive" src="{!! asset('public/images/app-storeicon.png') !!}"/> </a> </li>
    <li> <a href="#" data-toggle="modal" data-target="#appModal"> <img class="img-responsive" src="{!! asset('public/images/google-play-icon.png') !!}"/> </a></li>
    </ul> 
 
  
    </div>
    </div>
    
  </div>
<div class="container"> </div>
<footer class="text-center">

      <div class="copyright">
      <ul> <li> Copyright@2016. All Right Reserved </li>   <li><a href="{!! asset('public/customer-terms-service-agreement.pdf') !!}" target="_blank" title="TERMS OF SERVICE AGREEMENT">Customer Terms of Service Agreement</a></li>
        <li><a href="{!! asset('public/driver-terms-service-agreement.pdf') !!}" target="_blank" title="TERMS OF SERVICE AGREEMENT">Driver Terms of Service Agreement</a></li> <li>  <a href="{{url('privacy-policy')}}">Privacy Policy</a></p> </ul> </li>
      </div>
  
</footer>
</section>
<!-- / FOOTER --> 
<div id="myModal" class="modal fade" role="dialog" style="display: none;">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title ">Download from</h4>
      </div>
      <div class="modal-body">
        <li> <a href="#"> <img class="img-responsive" src="{!! asset('public/images/app-storeicon.png') !!}"/> </a>  </li>
        <li> <a href="#"> <img class="img-responsive" src="{!! asset('public/images/google-play-icon.png') !!}"/> </a> </li>
      </div>       
      <div class="clearfix"></div>
    </div>
  </div>
</div> 



<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 

{!! HTML::script('public/js/jquery-2.1.4.min.js'); !!}
{!! HTML::script('public/js/wow.min.js'); !!}
{!! HTML::script('public/js/bootstrap.js'); !!}
{!! HTML::script('public/js/bootstrap-datepicker.js'); !!}
{!! HTML::script('public/js/framework/jquery.validate.min.js'); !!}
 {!! HTML::script('public/js/framework/intlTelInput.js'); !!}
  {!! HTML::script('public/js/framework/isValidNumber.js'); !!}
@yield('creditjs')
@yield('maplocation')
@yield('drivercustom')
@yield('resendotp')
{!! HTML::script('public/js/framework/custom.js'); !!}
{!! HTML::script('public/js/framework/ajaxform.js'); !!}
<script src="http://html2canvas.hertzen.com/build/html2canvas.js"></script>

    <!-- Code editor -->
<script src="https://cdn.jsdelivr.net/ace/1.1.01/noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
{!! HTML::script('https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js'); !!} 
 
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
var nomapurl="<?php echo url('/').'/public/images/map/no-map.png'; ?>";
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

    <script src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
  <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
  <script>
  var tripHisURl = "{!! route('triphistory') !!}";
  var CSRF_TOKEN="{!! csrf_token() !!}";
       $('body').on("click", ".applyBtn.btn.btn-sm.btn-success",function(){
        
          var daterange = $("#datefilter").val();
      
         
      $.ajax({
        type: "POST",
        url: tripHisURl,
        data: {daterange:daterange,_token:CSRF_TOKEN},
        beforeSend:function() {
          $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
        },
        success: function(response){
          $('#divLoading').remove();
        if ($.trim(response) ==''){   
          
          $('.earning-data-table').html("<h1>NO RIDES</h1>");
          $('#invoice').hide();
        }
          else{
            $('.earning-data-table').html(response);
            $('#invoice').hide();
          }
          
          //console.log(response);
          }
        
      });
  
    });</script>
    
    
@section('customjavascript')
<script type="text/javascript">

      
jQuery(document).ready(function(){

  $("#invoice").hide();
  $('body').on('click', '.viewDe', function (){
    var rideId=$(this).prop('id');
    var CSRF_TOKEN= "<?php echo csrf_token(); ?>";
    
    $.ajax({
          type:'post',
          url:'<?php echo url("passenger/viewDetails"); ?>',
          data:'rideId='+rideId+'&_token='+CSRF_TOKEN,
          beforeSend:function() {
            $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
  
          },
          success:function(returnData)
          {
            
            var parseData = JSON.parse(returnData);
            console.log(parseData);
          
                      $('#pass_Id').html('<div class="btn-cls-down"> <hr/><div class="col-sm-6 text-center"><a class="btn green-btn-s" href="#" role="button">Report Found Item</a></div>\
                     <div class="col-sm-6 text-center"><a class="btn green-btn-s" href="passengerReportAnIssue/' + parseData['pass_id'] + '" role="button">Report An Issue</a></div>\
                      <div class="clearfix"></div> </div>');
            var address1=parseData['address1'];
      
            var address2=parseData['address2'];
            var startTime=parseData['startTime'];
            var endTime=parseData['endTime'];
            
            
            $('#pickupAdd').html(address1+' , '+startTime);
            $('#dropOff').html(address2+' , '+endTime);
            var map_image=parseData['map_image'];
        
            $('#address1').html(address1);
            $('#address2').html(address2);
            $('#rStartTime').html(startTime);
            $('#rEndTime').html(endTime);
            console.log($.trim(map_image));
            if($.trim(map_image) == "")
            {
              $("#mapFrame").show();
              $( "#noMap" ).html( " ");
              $('#mapFrame').attr('src',nomapurl);

            }
            else{
              $("#mapFrame").show();
              $( "#noMap" ).html( " ");
              $('#mapFrame').attr('src',map_image+'&key=AIzaSyBuc_EFYfMcW3dn_jnpbcsMvV9HjehWiaI');
              
            }
            
            $('#miles,#distance').html(parseData['miles']+" miles");
            $('#milesCharges,#distanCharges').html( "$" +parseData['miles_charges'] );
            $('#durations,#time').html(parseData['duration'] + "Minutes");
            $('#durationCharges,#timeCharges').html("$" +parseData['duration_charges']);
            $('#subtotal,#subtotal2').html("$" +parseData['sub_total']);
            $('#deziFee,#deziFee2').html("$" +parseData['deziFee']);
            $('#pick_up,#pickupFee').html("$" +parseData['pick_upFee']);
            $('#total,#TotalEarning').html("$" +parseData['total_charges']);
            
            $("#invoice").show();
            $('#divLoading').remove();
          }
        });
  });
  $('#customers').hide();
});


function rideReportPdf() {
  
  $('body').append('<div id="divLoading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;"><p style="position: absolute; color: White; top: 50%; left: 45%;">Loading, please wait...<img src="http://pulse.sindlab.com.pk//images/ajax-loading.gif"></p></div>');
  $('#customers').show();
  var doc = new jsPDF();
  var specialElementHandlers = {
    '#editor': function (element, renderer) {
        return true;
    }
  };
  
  var pik_up=$('.pik_up').text();
  doc.text(15, 25, "Ride Details"); 
  var imageUrl = $('#mapFrame').attr('src');
  
    var convertType = 'Canvas';
  convertImgToDataURLviaCanvas(imageUrl, function(base64Img) {
    $('.output').find('.textbox').val(base64Img)
      .end()
      .find('.link')
      .attr('href', base64Img)
      .text(base64Img)
      .end()
      .find('.img')
      .attr('src', base64Img)
      .end()
      .find('.size')
      .text(base64Img.length)
      .end()
      .find('.convertType')
      .text(convertType)
      .end()
      .hide()
  });
  //console.log('yha tak');
  
  //var a = $('[active_contact][serial=1] '+img)[0];
  //console.log(getBase64Image(a));
  setTimeout(function(){ 
  
  var data_img_url = $.trim($(".data_img_url").val());
  doc.addImage(data_img_url, 'PNG', 15, 40, 155, 80); 
  
  doc.fromHTML($('#customers').get(0), 15, 120, {
        'width': 180,
            'elementHandlers': specialElementHandlers
    });
  
  
  $('#customers').hide();
  $('#divLoading').remove();
  doc.save('invoice');
  }, 1000);
}

function convertImgToDataURLviaCanvas(url, callback, outputFormat) {
  var img = new Image();
  img.crossOrigin = 'Anonymous';
  img.onload = function() {
    var canvas = document.createElement('CANVAS');
    var ctx = canvas.getContext('2d');
    var dataURL;
    canvas.height = this.height;
    canvas.width = this.width;
    ctx.drawImage(this, 0, 0);
    dataURL = canvas.toDataURL(outputFormat);
    callback(dataURL);
    canvas = null;
  };
  img.src = url;
}



</script>
  
@yield('customjavascript')
</body>
</html>
