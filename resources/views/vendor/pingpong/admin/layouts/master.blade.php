<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administrator | @yield('title', 'DeziNow')</title>
    
    @include('admin::partials.style')
    @yield('style')
	<script>
	var loadingImage="<?php echo URL::to('/'); ?>/public/packages/pingpong/admin/images/ajax-loader.gif";

	</script>
	
</head>

<body class="skin-blue sidebar-mini">
<div class="wrapper">
    @include('admin::partials.header')
        @include('admin::partials.sidebar')

        <!-- Right side column. Contains the navbar and content of the page -->
        <aside class="right-side">
            <!-- Content Header (Page header) -->

            <section class="content-header">
                @yield('content-header')
            </section>

            <!-- Main content -->
            <section class="content">
                @include('admin::partials.flashes')
                @include('admin::partials.azmodal')
                @yield('content')
            </section>
            
        </aside>
        <!-- /.right-side -->
   
    <!-- ./wrapper -->
</div>	

    <!-- add new calendar event modal -->
	
    @include('admin::partials.script')
	@yield('customjavascript')
    @yield('script')
	
	<script>
	var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
	</script>

    <!-- Button trigger notification modal -->
    <!-- Modal -->
    <!-- Button trigger modal -->

</body>
</html>
<?php $loggedInUserPermission = Session::get('userPermissions');?>	
	@if(!empty($loggedInUserPermission))
		@foreach($loggedInUserPermission as $userPermission)
		
			@if($userPermission->module_slug=="reports" && $userPermission->view_permission==0)
				Hello:{{$userPermission->module_slug.$userPermission->view_permission}}	
					<script>
					$(document).ready(function(){
						
						$('.genRepoBtn').remove();
					});
					</script>
			@endif
		@endforeach
	@endif