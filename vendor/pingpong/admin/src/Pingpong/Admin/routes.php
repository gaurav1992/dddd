<?php

Route::group(['prefix' => config('admin.prefix', 'admin'), 'namespace' => 'Pingpong\Admin\Controllers'], function () {
    
    Route::group(['before' => config('admin.filter.guest')], function () {
       
        Route::resource('login', 'LoginController', [
            'only' => ['index', 'store'],
            'names' => [
                'index' => 'admin.login.index',
                'store' => 'admin.login.store'
            ]
        ]);
    });
	Route::any('/getDownload', ['as' => 'getDownload','uses' =>'DriverController@getDownload']);
	 Route::any('/allDriverListReport', ['as' => 'allDriverListReport', 'uses' => 'SiteController@allDriverListReport']);
	 Route::any('/allNewDriverListReport', ['as' => 'allNewDriverListReport', 'uses' => 'SiteController@allNewDriverListReport']);
     Route::get('/singleadmin' , [ 'as' => 'signleadmin' , 'uses' => 'AdminsubadminController@singleAdmin' ]);
    Route::get('/otherAdmins/{id}' , [ 'as' => 'otherAdmins' , 'uses' => 'AdminsubadminController@otherAdmins' ]);
    Route::get('/admindetail/{id}' , [ 'as' => 'admindetail' , 'uses' => 'AdminsubadminController@admindetail' ]);
    Route::get('loghistory' , [ 'as' => 'loghistory' , 'uses' => 'AdminsubadminController@loghistory' ]);
	Route::group(['before' => config('admin.filter.auth')], function () {
    Route::get('/', ['as' => 'admin.home', 'uses' => 'SiteController@index']);
    Route::get('/logout', ['as' => 'admin.logout', 'uses' => 'SiteController@logout']);
    Route::any('/payout', ['as' => 'admin.payoutUrl', 'uses' => 'SiteController@payout']);
    
    Route::any('/dynamicPayout', ['as' => 'admin.dynamicPayout', 'uses' => 'SiteController@dynamicPayout']);
    Route::any('/newPass', ['as' => 'admin.newPass', 'uses' => 'SiteController@newPass']);
    Route::any('/DnewPass', ['as' => 'admin.DnewPass', 'uses' => 'SiteController@DnewPass']);
    Route::any('/DnewDriver', ['as' => 'admin.DnewDriver', 'uses' => 'SiteController@DnewDriver']);
    Route::any('/DnewRide', ['as' => 'admin.DnewRide', 'uses' => 'SiteController@DnewRide']);
    Route::any('/newDriver', ['as' => 'admin.newDriver', 'uses' => 'SiteController@newDriver']);
    Route::any('/newRides', ['as' => 'admin.newRides', 'uses' => 'SiteController@newRides']);
    Route::any('/refund', ['as' => 'admin.refundUrl', 'uses' => 'SiteController@refund']);
	Route::any('/Drefund', ['as' => 'admin.DrefundUrl', 'uses' => 'SiteController@Drefund']);
	Route::any('/location', ['as' => 'admin.locationUrl', 'uses' => 'SiteController@location']);
	Route::any('/Dlocation', ['as' => 'admin.DlocationUrl', 'uses' => 'SiteController@Dlocation']);
    // settings
    Route::get('settings', ['as' => 'admin.settings', 'uses' => 'SiteController@settings']);
    Route::post('settings', ['as' => 'admin.settings.update', 'uses' => 'SiteController@updateSettings']);

	//Routing for Admin panel
	Route::post('users/suspend', ['as' => 'suspend', 'uses' => 'PassengerController@suspend']); 
	Route::any('/DzCreditAjax', ['as' => 'DzCreditAjax', 'uses' => 'PassengerController@DzCreditAjax']); 
	Route::any('/carFunction', ['as' => 'carFunction', 'uses' => 'PassengerController@carFunction']); 
	Route::any('/FavplaceFunction', ['as' => 'FavplaceFunction', 'uses' => 'PassengerController@FavplaceFunction']);
    Route::any('/AccountDtl', ['as' => 'AccountDtl', 'uses' => 'PassengerController@AccountDtl']);  
	Route::any('/editCarfunc', ['as' => 'editCarfunc', 'uses' => 'PassengerController@editCarfunc']); 
	Route::any('/carDelete', ['as' => 'carDelete', 'uses' => 'PassengerController@carDelete']); 
	
	Route::post('admin/ggg', ['as' => 'ggg', 'uses' => 'PassengerController@addCLl']);
	Route::post('users/adminsubadminsuspend', ['as' => 'adminSuspend', 'uses' => 'AdminsubadminController@suspend']); 
	Route::get('chargesandpromos', ['as' => 'chargePromos', 'uses' => 'UsersController@chargePromos']); 
    Route::get('/passengerajax', ['as' => 'passengerAjax', 'uses' => 'PassengerController@ajaxIndex']); 

    Route::get('/driverajax', ['as' => 'driverAjax', 'uses' => 'DriverController@ajaxIndex']); 
    Route::any('/driverChargeUpdate', ['as' => 'driverChargeUpdate', 'uses' => 'DriverController@driverChargeUpdate']); 

    Route::any('/wkdayCharges', ['as' => 'wkdayCharges', 'uses' => 'DriverController@wkdayCharges']); 
    Route::get('/driverisuuehistory', ['as' => 'driverIssueAjax', 'uses' => 'DriverController@driverIssueAjax']);
    Route::get('/driverearninghistory', ['as' => 'driverearningAjax', 'uses' => 'DriverController@driverearningAjax']);
     
    Route::get('/newDriverAjax', ['as' => 'newDriverAjax', 'uses' => 'DriverController@newDriverAjax']); 
	Route::get('/rejectedDriverAjax', ['as' => 'rejectedDriverAjax', 'uses' => 'DriverController@rejectedDriverAjax']); 
	Route::get('/passenger-detail/{id}', ['as' => 'passengerDetail', 'uses' => 'PassengerController@show']); 
	
    Route::get('/edit-new-driver/{id}', ['as' => 'editDriver', 'uses' => 'DriverController@editDriver']); 
    Route::any('/dzbnsEarning/{uid}/{did}', ['as' => 'dzbnsEarning', 'uses' => 'DriverController@dzbnsEarning']); 
    Route::any('/dzbnsEarningAjax', ['as' => 'dzbnsEarningAjax', 'uses' => 'DriverController@dzbnsEarningAjax']); 
	Route::get('/edituser/{id}', ['as' => 'edituser', 'uses' => 'AdminsubadminController@editUser']); 
	Route::post('/edituser/{id}', ['as' => 'edituser', 'uses' => 'AdminsubadminController@editUser']);
    Route::get('/driverBonusAjax', ['as' => 'driverBonusAjax', 'uses' => 'AdminsubadminController@driverBonusAjax']);  
    //driverBonusAjax
	//Route::get('listsuspendedusers/', ['as' => 'listSuspendedUser', 'uses' => 'AdminsubadminController@displaySuspendedUser']); 

       
	//Route::post('/list-suspended-users/', ['as' => 'listSuspendedUser', 'uses' => 'AdminsubadminController@displaySuspendedUser']); 
	//Route::post('/list-suspended-users-ajax/', ['as' => 'listSuspendedUserAjax', 'uses' => 'AdminsubadminController@displaySuspendedUserAjax']);
	
	Route::get('/adminsubadminajax', ['as' => 'adminSubadminajax', 'uses' => 'AdminsubadminController@ajaxIndex']); 
	Route::post('/index', ['as' => 'index', 'uses' => 'PassengerController@index']);
	Route::any('/SuspendedPassenger', ['as' => 'SuspendedPassenger', 'uses' => 'PassengerController@SuspendedPassenger']);
	Route::any('/SuspendedPassengerAjax', ['as' => 'SuspendedPassengerAjax', 'uses' => 'PassengerController@SuspendedPassengerAjax']);
	
	Route::get('/manageusers', ['as' => 'manageAdminSubadmin', 'uses' => 'AdminsubadminController@admin_subadmin_view']); 
	Route::get('/addnewuser', ['as' => 'addUser', 'uses' => 'AdminsubadminController@adduserview']); 

    //Route::get( '/addnewuser', 'AdminsubadminController@adduserview');
    Route::any('/adduser', ['as' => 'addUser', 'uses' => 'AdminsubadminController@addUser']);
    //Route::post('/adduser', ['as' => 'addUser', 'uses' => 'AdminsubadminController@addUser']);  
    Route::get('/messagelist', ['as' => 'messagelist', 'uses' => 'ContactmessagesController@index']); 
    Route::get('/messagelistajax', ['as' => 'messagelistajax', 'uses' => 'ContactmessagesController@indexAjax']); 
    Route::get('/message-del/{id}' , [ 'as' => 'messageDel' , 'uses' => 'ContactmessagesController@delete_msg' ]);
    Route::get('/messageView/{id}' , [ 'as' => 'viewMessage' , 'uses' => 'ContactmessagesController@view_msg' ]);
    Route::get('/message-action/{action}/{id}' , [ 'as' => 'messageAction' , 'uses' => 'ContactmessagesController@messageAction' ]);
    //Non registered user message

    Route::get('/nrmessagelist', ['as' => 'nrmessagelist', 'uses' => 'ContactmessagesController@NonRmsg']); 
    Route::get('/nrmessagelistajax', ['as' => 'nrmessagelistajax', 'uses' => 'ContactmessagesController@NonRmsgAjax']);
    Route::get('/nrmessageView/{id}' , [ 'as' => 'nrviewMessage' , 'uses' => 'ContactmessagesController@nrview_msg' ]);
	
	//ROUTING FOR GENERAL ISSUE CODE START
	Route::get('/generalissueslist', ['as' => 'generalissueslist', 'uses' => 'ContactmessagesController@generalIssuesList']); 
    Route::get('/generalIssuesAjax', ['as' => 'generalIssuesAjax', 'uses' => 'ContactmessagesController@generalIssuesAjax']);
    
    Route::any('/generalIssueStatusChange', ['as' => 'generalIssueStatusChange', 'uses' => 'ContactmessagesController@generalIssueStatusChange']); 
    
    Route::get('/view_genral_msg/{id}' , [ 'as' => 'view_genral_msg' , 'uses' => 'ContactmessagesController@view_genral_msg' ]);
    Route::any('/view_general_message/{id}' , [ 'as' => 'view_general_message' , 'uses' => 'ContactmessagesController@view_general_message' ]);
	//ROUTING FOR GENERAL ISSUE CODE END 

    Route::resource('passenger', 'PassengerController', [
        'except' => 'show',
        'names' => [
            'index' => 'admin.passenger.index',
            'create' => 'admin.passenger.create',
            'store' => 'admin.passenger.store',
            'show' => 'admin.passenger.show',
            'update' => 'admin.passenger.update',
            'edit' => 'admin.passenger.edit',
            'destroy' => 'admin.passenger.destroy'
        ]
    ]);

    // backup & reset
    Route::get('backup/reset', ['as' => 'admin.reset', 'uses' => 'SiteController@reset']);
    Route::get('app/reinstall', ['as' => 'admin.reinstall', 'uses' => 'SiteController@reinstall']);
    Route::get('cache/clear', ['as' => 'admin.cache.clear', 'uses' => 'SiteController@clearCache']);
    Route::any('massbulk', ['as' => 'massbulk', 'uses' => 'BulkController@store']);
    Route::get('charges', ['as' => 'charges', 'uses' => 'AdminsubadminController@charges']); 

    Route::any('riderpromos', ['as' => 'riderpromos', 'uses' => 'AdminsubadminController@riderpromos']);
   // Route::post('riderpromos', ['as' => 'riderpromos', 'uses' => 'AdminsubadminController@riderpromos']);
    
    //Passanger Routes
    Route::any('passanger/passengerRideHistory', ['as' => 'passengerRideHistory', 'uses' => 'PassengerController@passengerRideHistory']);
    
    
    //testAZ09

    Route::any('passanger/userCredit', ['as' => 'userCredit', 'uses' => 'PassengerController@userCredit']);
     Route::any('driver/userCredit', ['as' => 'driverCredit', 'uses' => 'DriverController@DriverCredit']);
     Route::any('driver/userSSN', ['as' => 'userSSN', 'uses' => 'DriverController@userSSN']);
	
    Route::any('passanger/testAZ09', ['as' => 'testAZ09', 'uses' => 'PassengerController@testAZ09']);
    Route::any('passanger/pasangerIssueHistory', ['as' => 'pasangerIssueHistory', 'uses' => 'PassengerController@pasangerIssueHistory']);
    Route::any('passanger/pasangerPaymentHistory', ['as' => 'pasangerPaymentHistory', 'uses' => 'PassengerController@pasangerPaymentHistory']);
    
    Route::any('passanger/pasangerAction', ['as' => 'pasangerAction', 'uses' => 'PassengerController@pasangerAction']);
    Route::any('passenger-detail/passengerRideDetail/{userid}/{rideid}', ['as' => 'passengerRideDetail', 'uses' => 'PassengerController@passengerRideDetail']);
    Route::any('passenger-detail/passengerIssueDetail/{userid}/{rideid}', ['as' => 'passengerRideDetail', 'uses' => 'PassengerController@passengerRideDetail']);
    Route::any('passenger-detail/passengerPaymentDetail/{userid}/{rideid}', ['as' => 'passengerRideDetail', 'uses' => 'PassengerController@passengerRideDetail']);
    //testAZ09
	Route::any('/issueStatus', ['as' => 'issueStatus', 'uses' => 'PassengerController@issueStatus']);
    Route::any('/passengerpromos', ['as' => 'passengerpromos', 'uses' => 'AdminsubadminController@passengerpromos']);
    
    Route::any('/listSuspendedUser', [
        'as' => 'listSuspendedUser', 'uses' => 'UsersController@suspendedUserList' 
    ]);

    Route::get('/list-suspended-users-ajax/', ['as' => 'listSuspendedUserAjax', 'uses' => 'UsersController@displaySuspendedUserAjax']);

    Route::get('riderbonus', ['as' => 'riderbonus', 'uses' => 'AdminsubadminController@riderbonus']);
    Route::post('riderbonus', ['as' => 'riderbonus', 'uses' => 'AdminsubadminController@riderbonus']);

    /*CRON AZ*/
    Route::any('cronTierLevel', ['as' => 'cronTierLevel', 'uses' => 'AdminsubadminController@cronTierLevel']);
    Route::any('cronDriverReferralBonus', ['as' => 'cronDriverReferralBonus', 'uses' => 'AdminsubadminController@cronDriverReferralBonus']);

    Route::any('driver/driverCharges', ['as' => 'driverCharges', 'uses' => 'DriverController@driverCharges']);
    //Route::post('driver/driverCharges', ['as' => 'driverCharges', 'uses' => 'DriverController@driverCharges']);

    //Driver Charges
    Route::post('driver/cityAjax', ['as' => 'cityAjax', 'uses' => 'DriverController@cityAjax']);
    Route::post('driver/dayChargesAjax', ['as' => 'dayChargesAjax', 'uses' => 'DriverController@dayChargesAjax']);

    Route::post('driver/driver-action', ['as' => 'driverAction', 'uses' => 'DriverController@driverAction']); 

    Route::get('driver/new-applicant-detail/{id}', ['as' => 'driverNewApplicantDetail', 'uses' => 'DriverController@driverNewApplicantDetail']);
    Route::get('driver/suspended-applicant-detail/{id}', ['as' => 'suspendedApplicantDetail', 'uses' => 'DriverController@suspendedApplicantDetail']);
    Route::get('driver/document-review-applicant-detail/{id}', ['as' => 'documentReviewApplicantDetail', 'uses' => 'DriverController@documentReviewApplicantDetail']);
    
//	Route::any('driver/driveraction', ['as' => 'driverAction', 'uses' => 'DriverController@driverAction']);
	
    Route::get('driver/rejected-applicant-detail/{id}', ['as' => 'driverRejectedApplicantDetail', 'uses' => 'DriverController@driverRejectedApplicantDetail']);
    
    Route::get('driver/applicantlist', ['as' => 'driverApplicantlist', 'uses' => 'DriverController@applicantlist']);
    
    Route::get('driver/new-driver-applicant-list', ['as' => 'newDriverApplicantList', 'uses' => 'DriverController@newDriverApplicantList']);
    
    Route::get('driver/newSuspnededList', ['as' => 'newSuspnededList', 'uses' => 'DriverController@newSuspnededList']);
    Route::get('driver/newSuspendedListAjax', ['as' => 'newSuspendedListAjax', 'uses' => 'DriverController@newSuspendedListAjax']);
    
    Route::get('driver/newdocumentReviewList', ['as' => 'newdocumentReviewList', 'uses' => 'DriverController@newdocumentReviewList']);
    Route::get('driver/newdocumentReviewListAjax', ['as' => 'newdocumentReviewListAjax', 'uses' => 'DriverController@newdocumentReviewListAjax']);
    
    Route::get('driver/rejected-driver-applicant-list', ['as' => 'rejectedDriverApplicantList', 'uses' => 'DriverController@rejectedDriverApplicantList']);
    Route::get('driver/alldriver', ['as' => 'alldriver', 'uses' => 'DriverController@alldriver']);
    Route::any('driver/deleteImage', ['as' => 'deleteImage', 'uses' => 'DriverController@deleteImage']);
    Route::get('/alldriverlist', ['as' => 'alldriverlist', 'uses' => 'DriverController@alldriverlist']);
    Route::get('driver/driver-detail/{id}', ['as' => 'driverDetail', 'uses' => 'DriverController@show']); 
    Route::any('driver/driver-edit/{id}', ['as' => 'editDriver', 'uses' => 'DriverController@editDriver']); 
    Route::post('/revoke', ['as' => 'revoke', 'uses' => 'DriverController@revoke']);
    Route::post('/delete', ['as' => 'delete', 'uses' => 'DriverController@delete']);
    Route::any('/hourlog', ['as' => 'hourlog', 'uses' => 'DriverController@driverHourAjax']);
    Route::any('/documentAjax', ['as' => 'documentAjax', 'uses' => 'DriverController@documentAjax']);
    Route::post('driver/driverChargespdf', ['as' => 'driver/driverChargespdf', 'uses' => 'DriverController@driverChargespdf']);
	
    /*  ride history ajax work   */
    Route::any('driver/ridehistory', ['as' => 'ridehistory', 'uses' => 'DriverController@ridehistory']);
    /*Avinash thakur 29 july added the route for getting driver's bonus history*/
    Route::any('driver/ridebonus',['as' => 'ridebonus', 'uses' => 'DriverController@ridebonus']);
    Route::any('driver/bonusHistory',['as' => 'bonusHistory', 'uses' => 'DriverController@bonusHistory']);
    Route::any('driver/bonus/{id}',['as' => 'bonus', 'uses' => 'DriverController@bonus']);
    /*ridebonus route end*/
    Route::any('driver/revokedriver', ['as' => 'revokedriver', 'uses' => 'DriverController@revokedriver']);
    //Route::any('driver/suspendeddriver', ['as' => 'revokedriver', 'uses' => 'DriverController@suspendeddriver']);
    
    Route::get('/alldriverRevokelist', ['as' => 'alldriverRevokelist', 'uses' => 'DriverController@alldriverRevokelist']);
    Route::any('driver/ridedetail/{id}', ['as' => 'ridedetail', 'uses' => 'DriverController@ridedetail']);
   
    Route::any('driver/paymentetail/{id}', ['as' => 'ridedetail', 'uses' => 'DriverController@ridedetail']);
    //Route::get('/driverajax', ['as' => 'driverAjax', 'uses' => 'DriverController@ajaxIndex']); 
    Route::get('/driverchangelog/{id}', ['as' => 'notification', 'uses' => 'AdminsubadminController@driverchangelog']);
	Route::post('/driverchangelog/{id}', ['as' => 'notificationpost', 'uses' => 'AdminsubadminController@driverchangelog']);
	Route::get('/driverlist', ['as' => 'driverlist', 'uses' => 'AdminsubadminController@driverlist']);
	Route::get('/ajaxRideList', ['as' => 'ajaxRideList', 'uses' => 'AdminsubadminController@ajaxRideList']);

	Route::get('/ajaxpassengerPromos', ['as' => 'ajaxpassengerPromos', 'uses' => 'AdminsubadminController@ajaxpassengerPromos']);

    Route::any('/deletepassengerpromo', ['as' => 'deletepassengerpromo', 'uses' => 'AdminsubadminController@deletepassengerpromo']);
    
    Route::any('promo_code_check', ['as' => 'promo_code_check', 'uses' => 'AdminsubadminController@promo_code_check']);
    
    Route::any('/bankdetails', ['as' => 'bankdetails', 'uses' => 'DriverController@bankdetails']); 
    Route::get('/dezicredit', ['as' => 'dezicredit', 'uses' => 'DezicreditController@dezicredit']);
    Route::any('/refundAjax', ['as' => 'refundAjax', 'uses' => 'PassengerController@refundAjax']);

    /* Notification Routes*/
    Route::get('/notification', ['as' => 'manageNotification', 'uses' => 'NotificationsController@manageNotification']);
    Route::post('/notification', ['as' => 'manageNotification', 'uses' => 'NotificationsController@manageNotification']);
    
    Route::get('/notification/sendPromoCode', ['as' => 'sendPromoCode', 'uses' => 'NotificationsController@sendPromoCode']);
    Route::get('/notification/passengerReferralCheck', ['as' => 'passengerReferralCheck', 'uses' => 'NotificationsController@passengerReferralCheck']);

    Route::get('/notification-log', ['as' => 'manageNotificationLog', 'uses' => 'NotificationsController@manageNotificationLog']);
    Route::get('/notificationLogAjax', ['as' => 'notificationLogAjax', 'uses' => 'NotificationsController@notificationLogAjax']); 
    Route::post('/adminNotifications', ['as' => 'adminNotifications', 'uses' => 'NotificationsController@adminNotifications']);
    Route::any('/cron/driverbonus', ['as' => 'driverbonus', 'uses' => 'CronController@driverbonus']);
    Route::any('/callLogs', ['as' => 'callLogs', 'uses' => 'PassengerController@callLogs']);
	Route::any('/adcalllog', ['as' => 'adcalllog', 'uses' => 'PassengerController@adcalllog']);
	
	//Route::any('/DocExpirationNotification', ['as' => 'DocExpirationNotification', 'uses' => 'DriverController@DocExpirationNotification']);

    //CRON JOB CONTROLLER
	
    });
});
