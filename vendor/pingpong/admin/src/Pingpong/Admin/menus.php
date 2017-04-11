<?php

Menu::create('admin-menu', function ($menu) {

    $menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');

    $menu->route('admin.home', trans('admin.menus.dashboard'), [], 0, ['icon' => 'fa fa-dashboard']);
    $menu->route('signleadmin',trans('My Profile'),[], 1, ['icon' => 'fa fa-dashboard']);

    $menu->dropdown('Passengers', function ($sub) 
    {
        $sub->route("admin.passenger.index", 'All Passengers', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("SuspendedPassenger", 'Suspended Passengers', [], 3, ['icon' => 'fa fa-circle-o']);
        
        
    }, 2, [ 'icon' => 'fa fa-male'] );

    $menu->dropdown('Drivers', function ($sub) 
    {
        $sub->route("alldriver", 'All driver list', [], 3, ['icon' => 'fa fa-circle-o']);
        $sub->route("newDriverApplicantList", 'New Applicant List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("rejectedDriverApplicantList", 'Rejected Applicant List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("revokedriver", 'Revoked Driver list', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("newSuspnededList", 'Insurance/DL Suspended list', [], 3, ['icon' => 'fa fa-circle-o']);
	//	$sub->route("suspendeddriver", 'Suspended Driver list', [], 3, ['icon' => 'fa fa-circle-o']);
        
    }, 3, [ 'icon' => 'fa fa-car'] );
   
    $menu->dropdown('Charges & Promos', function ($sub) 
    {
        $sub->route("riderpromos", 'Driver Promos', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route('passengerpromos', 'Passenger Promos', [], 3, ['icon' => 'fa fa-circle-o']);
        $sub->route('riderbonus', 'Tier Bonuses', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route('driverCharges', 'Driver Charges', [], 3, ['icon' => 'fa fa-circle-o']);
        
    },4, [ 'icon' => 'fa fa-bullhorn'] );

	$menu->dropdown('Manage Admin-Subadmin', function ($sub) 
    {
        $sub->route("manageAdminSubadmin", 'Admin-Subadmin List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route('listSuspendedUser', 'Suspended Admin List', [], 3, ['icon' => 'fa fa-circle-o']);
        
    },5,[ 'icon' => 'fa fa-users']);

    $menu->dropdown('Contact Messages', function ($sub) 
    {
        $sub->route("messagelist", 'Messages - Registered User', [], 4, ['icon' => 'fa fa-circle-o']);
        $sub->route("nrmessagelist", 'Messages - Website Visitors', [], 4, ['icon' => 'fa fa-circle-o']);
        $sub->route("generalissueslist", 'Messages - General Issues', [], 4, ['icon' => 'fa fa-circle-o']);
        
        
    }, 6, [ 'icon' => 'fa fa-envelope'] );

	$menu->dropdown('Notifications', function ($sub) 
    {
        $sub->route("manageNotification", 'Manage Notifications', [], 3, ['icon' => 'fa fa-circle-o']);
        $sub->route('manageNotificationLog', 'Manage Notifications Log', [], 3, ['icon' => 'fa fa-circle-o']);
        
    },7, [ 'icon' => 'fa fa-bullhorn']);

});

//Common Menu
Menu::create('common_menu', function ($menu) {

    $menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
    $menu->route('admin.home', trans('admin.menus.dashboard'), [], 0, ['icon' => 'fa fa-dashboard']);
    $menu->route('signleadmin',trans('My Profile'),[], 1, ['icon' => 'fa fa-dashboard']);

});
 
//Passenger Menu
Menu::create('passenger_menu', function ($menu) {
	
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
    
	$menu->dropdown('Passengers', function ($sub) 
    {
        $sub->route("admin.passenger.index", 'All Passengers', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("SuspendedPassenger", 'Suspended Passengers', [], 3, ['icon' => 'fa fa-circle-o']);
           
    }, 2, [ 'icon' => 'fa fa-male'] );    
    
});

//Driver Menu With New Application 
Menu::create('driver_menu', function ($menu) {
	
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
	$menu->dropdown('Drivers', function ($sub) 
    {
        $sub->route("alldriver", 'All driver list', [], 3, ['icon' => 'fa fa-circle-o']);
        $sub->route("newDriverApplicantList", 'New Applicant List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("rejectedDriverApplicantList", 'Rejected Applicant List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("revokedriver", 'Revoked Driver list', [], 3, ['icon' => 'fa fa-circle-o']);
        $sub->route("newSuspnededList", 'Insurance/DL Suspended list', [], 3, ['icon' => 'fa fa-circle-o']);
        
    }, 3, [ 'icon' => 'fa fa-car'] );    

}); 

//Driver Menu Withour New Application 
Menu::create('driver_without_application_menu', function ($menu) {
	
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
	$menu->dropdown('Drivers', function ($sub) 
    {
        
        $sub->route("alldriver", 'All driver list', [], 3, ['icon' => 'fa fa-circle-o']);
        //$sub->route("rejectedDriverApplicantList", 'Rejected Applicant List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("revokedriver", 'Revoked Driver list', [], 3, ['icon' => 'fa fa-circle-o']);
        
    }, 3, [ 'icon' => 'fa fa-car'] );    

});


//Driver Menu Withour New Application 
Menu::create('driver_with_new_menu', function ($menu) {
	
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
	$menu->dropdown('Drivers', function ($sub) 
    {
        
        
        $sub->route("newDriverApplicantList", 'New Applicant List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route("rejectedDriverApplicantList", 'Rejected Applicant List', [], 3, ['icon' => 'fa fa-circle-o']);
        
    }, 3, [ 'icon' => 'fa fa-car'] );    

});

//Charges and Promo Menu
Menu::create('charges_promo_menu', function ($menu) {
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
    
	$menu->dropdown('Charges & Promos', function ($sub) 
    {
        $sub->route("riderpromos", 'Driver Promos', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route('passengerpromos', 'Passenger Promos', [], 3, ['icon' => 'fa fa-circle-o']);
        $sub->route('riderbonus', 'Tier Bonuses', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route('driverCharges', 'Driver Charges', [], 3, ['icon' => 'fa fa-circle-o']);
        
    }, 4, [ 'icon' => 'fa fa-bullhorn'] );
});

//Admin and Submenu Menu
Menu::create('admin_subadmin_menu', function ($menu) {
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
    
	$menu->dropdown('Manage Admin-Subadmin', function ($sub) 
    {
        $sub->route("manageAdminSubadmin", 'Admin-Subadmin List', [], 3, ['icon' => 'fa fa-circle-o']);
		$sub->route('listSuspendedUser', 'Suspended Admin List', [], 3, ['icon' => 'fa fa-circle-o']);
        
    },5,[ 'icon' => 'fa fa-users']);
});

//Contact Message Menu
Menu::create('contact_messages_menu', function ($menu) {
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
	$menu->dropdown('Contact Messages', function ($sub) 
    {
        $sub->route("messagelist", 'Messages - Registered User', [], 4, ['icon' => 'fa fa-circle-o']);
        $sub->route("nrmessagelist", 'Messages - Website Visitors', [], 4, ['icon' => 'fa fa-circle-o']);
        $sub->route("generalissueslist", 'Messages - General Issues', [], 4, ['icon' => 'fa fa-circle-o']);
        
        
    }, 6, [ 'icon' => 'fa fa-envelope'] );
});

//Notification View/Edit Menu
Menu::create('notification_view_edit_menu', function ($menu) {
	
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
	$menu->dropdown('Notifications', function ($sub) 
    {
        $sub->route("manageNotification", 'Manage Notifications', [], 3, ['icon' => 'fa fa-circle-o']);
        $sub->route('manageNotificationLog', 'Manage Notifications Log', [], 3, ['icon' => 'fa fa-circle-o']);
        
    }, 7, [ 'icon' => 'fa fa-bullhorn'] );
});

//Notification View Menu
Menu::create('notification_view_menu', function ($menu) {
	
	$menu->enableOrdering();
    $menu->setPresenter('Pingpong\Admin\Presenters\SidebarMenuPresenter');
	$menu->dropdown('Notifications', function ($sub) 
    {
        $sub->route('manageNotificationLog', 'Manage Notifications Log', [], 3, ['icon' => 'fa fa-circle-o']);
        
    }, 7, [ 'icon' => 'fa fa-bullhorn'] );
});
