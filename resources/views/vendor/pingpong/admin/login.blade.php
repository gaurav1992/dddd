<?php //echo "test";exit;?>
<!DOCTYPE html>
<html class="">
    <head>
        <meta charset="UTF-8">
        <title>Administrator | Login</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="{!! admin_asset('components/bootstrap/dist/css/bootstrap.min.css') !!}" rel="stylesheet" type="text/css"/>
        <link href="{!! admin_asset('components/fontawesome/css/font-awesome.min.css') !!}" rel="stylesheet"
type="text/css"/>
        <!-- Theme style -->
        <link href="{!! admin_asset('adminlte/css/AdminLTE.css') !!}" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="{!! admin_asset('adminlte/plugins/iCheck/flat/blue.css') !!}">
		  <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition login-page">
		<div class="login-box">
      <div class="login-logo">
        <a href="#"><b>Dezi</b>Now</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        {!! Form::open(['route' => 'admin.login.store','id'=>'AdminLogin']) !!}
        
                <div class="body">
                    @if(Session::has('flash_message'))
                        <p class="login-flash-text text-danger">
                            {{ Session::get('flash_message') }}
                        </p>
                    @endif
                    <div class="form-group has-feedback">
                        <input type="text" name="email" class="form-control required email" placeholder="Email" required/>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="password" name="password" class="form-control required" placeholder="Password" required/>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <!--<div class="form-group checkbox" style="margin-left:20px;">
                        <input type="checkbox" name="remember" value="1" /> Remember me
                    </div>-->
                </div>
                <div class="footer">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign me in</button>
                    <!--
                    <p><a href="#">I forgot my password</a></p>

                    <a href="register.html" class="text-center">Register a new membership</a> -->
                </div>
            {!! Form::close() !!}


      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->




        <script src="{!! admin_asset('components/jquery/dist/jquery.min.js') !!}"></script>
         <script src="{!! admin_asset('adminlte/plugins/iCheck/icheck.min.js') !!}"></script>
        <script src="{!! admin_asset('components/bootstrap/dist/js/bootstrap.min.js') !!}" type="text/javascript"></script>
		<script src="{!! admin_asset('components/jquery/jquery.validate.js') !!}" type="text/javascript"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				//$('#AdminLogin').validate();
			});
            $(function () {
                $('input').iCheck({
                  checkboxClass: 'icheckbox_square-blue',
                  radioClass: 'iradio_square-blue',
                  increaseArea: '20%' // optional
                });
              });
		</script>

    </body>
</html>
