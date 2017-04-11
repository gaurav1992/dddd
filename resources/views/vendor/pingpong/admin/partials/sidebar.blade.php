<!-- Left side column. contains the logo and sidebar  -->
<aside class="main-sidebar" >
    
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        
        <!-- Sidebar user panel -->
        <div class="user-panel">
            
            <div class="pull-left image">
				<?php 
					if(empty(Auth::user()->profile_pic)){ 
						$profile_pic="http://www.gravatar.com/avatar/283d34811820f8566680a63ccac4050b?s=60&d=mm&r=g"; 
					}else{
                    //$profile_pic = "/public/img/memberImages/".Auth::user()->profile_pic;
                    $profile_pic = asset('').'/'.Auth::user()->profile_pic;
					} 
				?>
                {!! HTML::image( $profile_pic, 'a picture', array('class' => 'img-circle')) !!}
            </div>
            
            <div class="pull-left info">
                <p style="font-size:14px;fornt-weight:normal">Hi, {!! Auth::user()->first_name.' '.Auth::user()->last_name !!} </p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
            
        </div>
       <?php $loggedInUserPermission = Session::get('userPermissions');
       if(!empty($loggedInUserPermission)){
				foreach($loggedInUserPermission as $k=>$allModule){
					$allMod[]= $allModule->module_slug;
					$allModPer[$allModule->module_slug]= $allModule;
				}
		
		}
				
        ?>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        
        <?php 
			if(empty($loggedInUserPermission)){?>
				<ul class="sidebar-menu">
				{!! Menu::get('admin-menu') !!}
				</ul>
		<?php 
			}else if(!empty($loggedInUserPermission)){
		?>		
		<ul class="sidebar-menu">
				{!! Menu::get('common_menu') !!}
		<?php	
				
				
				foreach($loggedInUserPermission as $userPermission){
					
					if($userPermission->module_slug=="passengers" && $userPermission->view_permission==1){
					?>
						{!! Menu::get('passenger_menu') !!}
					<?php
					}
					
					if($userPermission->module_slug=="drivers" && $userPermission->view_permission==1 ){
						foreach($loggedInUserPermission as $drvrPermission){
							if($drvrPermission->module_slug=="driver_applicants"){
								
								if($allModPer[$drvrPermission->module_slug]->view_permission==1){
								?>
									{!! Menu::get('driver_menu') !!}
								<?php	
								}else{
								?>
									{!! Menu::get('driver_without_application_menu') !!}
								<?php	
								
								}
							}
						}
					?>
						
					<?php
					}
					
					if($userPermission->module_slug=="drivers" && $userPermission->view_permission==0 ){
						foreach($loggedInUserPermission as $drvrPermission){
							if($drvrPermission->module_slug=="driver_applicants" && $allModPer[$drvrPermission->module_slug]->view_permission==1){
							?>
									{!! Menu::get('driver_with_new_menu') !!}
							<?php	
											
							}
						}
					}	
					
					if($userPermission->module_slug=="admin_user" && $userPermission->view_permission==1){
					?>
						{!! Menu::get('admin_subadmin_menu') !!}
					<?php
					}
					
					if($userPermission->module_slug=="contact_messages" && $userPermission->view_permission==1){
					?>
						{!! Menu::get('contact_messages_menu') !!}
					<?php
					}
					
					if($userPermission->module_slug=="notification" && ($userPermission->view_permission==1 && $userPermission->edit_permission==1)){
					?>
						{!! Menu::get('notification_view_edit_menu') !!}
					<?php
					}else if($userPermission->module_slug=="notification" && ($userPermission->view_permission==1 && $userPermission->edit_permission==0)){
					?>
						{!! Menu::get('notification_view_menu') !!}
					<?php
					}
				}
		?>
			</ul>
		<?php 
			} ?>
        
    </section>
    <!-- /.sidebar -->
</aside>
<style>
.skin-blue .sidebar-menu>li>.treeview-menu {
        margin: 0 1px;
        background: #2c3b41;
        width: 229px!important;
}
.treeview span {
  width: 229px !important;
}
</style>
