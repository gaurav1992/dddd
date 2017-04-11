@extends($layout)
@section('title', 'Admin-Subadmin List')
@section('content-header')
	<h1 style="text-align:center;">
		{!! $title or 'Admin-Subadmin List' !!} 
	</h1>
<div style="clear:both"></div>
{!!@$message!!}
@stop
@section('customjavascript')
<script>
var suspendUrl = "{!! route('adminSuspend') !!}";
var indexUrl2= "{!! route('adminSubadminajax') !!}";
var homeUrl= "{!! route('manageAdminSubadmin') !!}";
var suspendAdminUrl = "{!! route('adminSuspend') !!}";

</script>
@stop
@section('content')
<style>
.dataTables_length{display:none;}

.info-box-text{text-align: center; font-size: 17px; padding-top: 8px;}
.info-box-number{text-align: center; font-size: 30px;}
</style>
<div style="clear:both;margin-bottom:20px;"></div>
@if(Session::has('message'))
<p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
@endif
	<div class="row">
        <div class="col-md-12">
          <div class="box">
    <div class="table-responsive add-more-btn ">
		 <!--Subadmin Permission Code Start-->
		<?php 
			$loggedInUserPermission = Session::get('userPermissions'); 
			if(empty($loggedInUserPermission)){
			?>
				<a href="{{ route('addUser') }}"  class="btn btn-success add-more-btn-1">Add users <i class="fa fa-plus-circle"></i> </a>
			<?php 
			}else if(!empty($loggedInUserPermission)){
			?>		
			<?php	
			
				foreach($loggedInUserPermission as $userPermission){
							
					if($userPermission->module_slug=="admin_user" && $userPermission->edit_permission==1){
					?>
						<a href="{{ route('addUser') }}"  class="btn btn-success add-more-btn-1">Add users <i class="fa fa-plus-circle"></i> </a>
					<?php
					}
				}
				?>
				
			<?php 
			} 
		?>
		<!--Subadmin Permission Code Start-->
		   
	<table class="table" id="Passengertable" style="width:100%">
		<thead>
			<th>ID No</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Join Date</th>
			<th>Designation</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Status</th>
			<th>Is Logged In</th>
			<th class="text-center">Action</th>
		</thead>
	</table>
		</div>
          </div>
        </div>
      </div>
@stop


