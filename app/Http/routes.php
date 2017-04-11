<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function(){
	//return View::make('home.index');
});

//web service routes
Route::post('app/user_check', array('uses' => 'mobile@user_check'));
Route::post('app/register', array('uses' => 'mobile@register'));
Route::post('app/login', array('uses' => 'mobile@login'));
Route::post('app/send_otp', array('uses' => 'mobile@send_otp'));
Route::post('app/driver_profile', array('uses' => 'mobile@driver_profile'));
Route::post('app/get_profile', array('uses' => 'mobile@get_profile'));
Route::post('app/logout', array('uses' => 'mobile@logout'));
Route::post('app/driver_position_update', array('uses' => 'mobile@driver_position_update'));
Route::post('app/get_passenger_ride_info', array('uses' => 'mobile@get_passenger_ride_info'));
Route::post('app/pickup_request', array('uses' => 'mobile@pickup_request'));
Route::post('app/pickup_request_new', array('uses' => 'mobile@pickup_request_new'));
Route::post('app/get_estimated_fare', array('uses' => 'mobile@get_estimated_fare'));
Route::post('app/device_gcm_token', array('uses' => 'mobile@device_gcm_token'));
Route::post('app/toggle_profile', array('uses' => 'mobile@toggle_profile'));
Route::post('app/change_mode', array('uses' => 'mobile@change_mode'));
Route::post('app/check_driver_status', array('uses' => 'mobile@check_driver_status'));
Route::post('app/add_update_car', array('uses' => 'mobile@add_update_car'));
Route::post('app/get_cars', array('uses' => 'mobile@get_cars'));
Route::post('app/delete_car', array('uses' => 'mobile@delete_car'));
Route::post('app/add_update_places', array('uses' => 'mobile@add_update_places'));
Route::post('app/delete_place', array('uses' => 'mobile@delete_place'));
Route::post('app/get_places', array('uses' => 'mobile@get_places'));
Route::post('app/accept_ride', array('uses' => 'mobile@accept_ride'));
Route::post('app/driver_arrive', array('uses' => 'mobile@driver_arrive'));
Route::post('app/ride_status', array('uses' => 'mobile@ride_status'));
Route::post('app/review', array('uses' => 'mobile@review'));
Route::post('app/forgot_password', array('uses' => 'mobile@forgot_password'));
Route::post('app/change_password', array('uses' => 'mobile@change_password'));
Route::post('app/change_phone', array('uses' => 'mobile@change_phone'));
Route::post('app/update_phone', array('uses' => 'mobile@update_phone'));
Route::post('app/default_car', array('uses' => 'mobile@default_car'));
Route::post('app/default_place', array('uses' => 'mobile@default_place'));
Route::post('app/braintree_clint_token', array('uses' => 'mobile@braintree_clint_token'));
Route::post('app/braintree_nonce_save', array('uses' => 'mobile@braintree_nonce_save'));
Route::post('app/get_payment_method', array('uses' => 'mobile@get_payment_method'));
Route::post('app/payment_method_delete', array('uses' => 'mobile@payment_method_delete'));
Route::post('app/braintree_default_payment', array('uses' => 'mobile@braintree_default_payment'));
Route::post('app/one_click_request', array('uses' => 'mobile@one_click_request'));
Route::post('app/nearby_driver', array('uses' => 'mobile@nearby_driver'));
Route::post('app/start_ride', array('uses' => 'mobile@start_ride'));
Route::post('app/end_ride', array('uses' => 'mobile@end_ride'));
Route::post('app/give_rating', array('uses' => 'mobile@give_rating'));
Route::post('app/endride_info', array('uses' => 'mobile@endride_info'));
Route::post('app/driver_total_earning', array('uses' => 'mobile@driver_total_earning'));
Route::post('app/rider_ride_history', array('uses' => 'mobile@rider_ride_history'));
Route::post('app/send_gcm', array('uses' => 'mobile@send_gcm'));
Route::post('app/get_otp_test', array('uses' => 'app@get_otp_test'));
Route::get('app/firebase_get_response', 'mobile@firebase_get_response');
Route::get('app/make_call', 'app@make_call');
Route::post('app/cancellation_category', 'mobile@cancellation_category');
Route::post('app/charge_cancellation_category', 'mobile@charge_cancellation_category');
Route::post('app/cancel_pickup',array('uses' => 'mobile@cancel_pickup'));
Route::post('app/confirm_cancel', 'mobile@confirm_cancel');
Route::post('app/driver_bank_detail', 'mobile@driver_bank_detail');
Route::post('app/driver_earning_detail', 'mobile@driver_earning_detail');
Route::post('app/get_faq', 'mobile@get_faq');
Route::post('app/report_an_issue', 'mobile@report_an_issue');
Route::post('app/get_driver_bank_detail', 'mobile@get_driver_bank_detail');
Route::post('app/get_cityname', 'mobile@get_cityname');
Route::post('app/user_data', 'mobile@user_data');
Route::post('app/pay_bill', 'mobile@pay_bill');
Route::post('app/current_ride_info', 'mobile@current_ride_info');
Route::post('app/driver_reward', 'mobile@driver_reward');
Route::post('app/xml_client',array('uses' => 'mobile@xml_client'));
Route::get('app/status1',array('uses' => 'mobile@status1'));
Route::post('app/twilio_token',array('uses' => 'mobile@twilio_token'));
Route::post('app/distance_calculate',array('uses' => 'mobile@distance_calculate'));
Route::post('app/passenger_promo',array('uses' => 'mobile@passenger_promo'));
Route::post('app/history_detail_android',array('uses' => 'mobile@history_detail_android'));
Route::post('app/cancel_ride_request',array('uses' => 'mobile@cancel_ride_request'));
Route::post('app/transport_mode',array('uses' => 'mobile@transport_mode'));
Route::post('app/driver_request',array('uses' => 'mobile@driver_request'));
Route::post('app/rider_ride_history_new', array('uses' => 'mobile@rider_ride_history_new'));
Route::post('app/become_driver', array('uses' => 'mobile@become_driver'));
Route::post('app/arrived_at_pickup', array('uses' => 'mobile@arrived_at_pickup'));




//web service routes testing
Route::post('app_test/user_check', array('uses' => 'app_test@user_check'));
Route::post('app_test/register', array('uses' => 'app_test@register'));
Route::post('app_test/login', array('uses' => 'app_test@login'));
Route::post('app_test/send_otp', array('uses' => 'app_test@send_otp'));
Route::post('app_test/driver_profile', array('uses' => 'app_test@driver_profile'));
Route::post('app_test/get_profile', array('uses' => 'app_test@get_profile'));
Route::post('app_test/logout', array('uses' => 'app_test@logout'));
Route::post('app_test/driver_position_update', array('uses' => 'app_test@driver_position_update'));
Route::post('app_test/get_passenger_ride_info', array('uses' => 'app_test@get_passenger_ride_info'));
Route::post('app_test/pickup_request', array('uses' => 'app_test@pickup_request'));
Route::post('app_test/get_estimated_fare', array('uses' => 'app_test@get_estimated_fare'));
Route::post('app_test/device_gcm_token', array('uses' => 'app_test@device_gcm_token'));
Route::post('app_test/toggle_profile', array('uses' => 'app_test@toggle_profile'));
Route::post('app_test/change_mode', array('uses' => 'app_test@change_mode'));
Route::post('app_test/check_driver_status', array('uses' => 'app_test@check_driver_status'));
Route::post('app_test/add_update_car', array('uses' => 'app_test@add_update_car'));
Route::post('app_test/get_cars', array('uses' => 'app_test@get_cars'));
Route::post('app_test/delete_car', array('uses' => 'app_test@delete_car'));
Route::post('app_test/add_update_places', array('uses' => 'app_test@add_update_places'));
Route::post('app_test/delete_place', array('uses' => 'app_test@delete_place'));
Route::post('app_test/get_places', array('uses' => 'app_test@get_places'));
Route::post('app_test/accept_ride', array('uses' => 'app_test@accept_ride'));
Route::post('app_test/driver_arrive', array('uses' => 'app_test@driver_arrive'));
Route::post('app_test/ride_status', array('uses' => 'app_test@ride_status'));
Route::post('app_test/review', array('uses' => 'app_test@review'));
Route::post('app_test/forgot_password', array('uses' => 'app_test@forgot_password'));
Route::post('app_test/change_password', array('uses' => 'app_test@change_password'));
Route::post('app_test/change_phone', array('uses' => 'app_test@change_phone'));
Route::post('app_test/update_phone', array('uses' => 'app_test@update_phone'));
Route::post('app_test/default_car', array('uses' => 'app_test@default_car'));
Route::post('app_test/default_place', array('uses' => 'app_test@default_place'));
Route::post('app_test/braintree_clint_token', array('uses' => 'app_test@braintree_clint_token'));
Route::post('app_test/braintree_nonce_save', array('uses' => 'app_test@braintree_nonce_save'));
Route::post('app_test/get_payment_method', array('uses' => 'app_test@get_payment_method'));
Route::post('app_test/payment_method_delete', array('uses' => 'app_test@payment_method_delete'));
Route::post('app_test/braintree_default_payment', array('uses' => 'app_test@braintree_default_payment'));
Route::post('app_test/one_click_request', array('uses' => 'app_test@one_click_request'));
Route::post('app_test/give_rating', array('uses' => 'app_test@give_rating'));
Route::post('app_test/endride_info', array('uses' => 'app_test@endride_info'));
Route::post('app_test/driver_total_earning', array('uses' => 'app_test@driver_total_earning'));
Route::post('app_test/rider_ride_history', array('uses' => 'app_test@rider_ride_history'));
Route::get('app_test/firebase_get_response', 'app_test@firebase_get_response');
Route::post('app_test/send_gcm', array('uses' => 'app_test@send_gcm'));
Route::post('app_test/get_otp_test', array('uses' => 'app_test@get_otp_test'));
Route::post('app_test/start_ride', array('uses' => 'app_test@start_ride'));
Route::post('app_test/end_ride', array('uses' => 'app_test@end_ride'));
Route::post('app_test/nearby_driver', array('uses' => 'app_test@nearby_driver'));
Route::get('app_test/make_call', 'app_test@make_call');
Route::post('app_test/cancellation_category', 'app_test@cancellation_category');
Route::post('app_test/charge_cancellation_category', 'app_test@charge_cancellation_category');
Route::post('app_test/cancel_pickup', 'app_test@cancel_pickup');
Route::post('app_test/confirm_cancel', 'app_test@confirm_cancel');
Route::post('app_test/driver_bank_detail', 'app_test@driver_bank_detail');
Route::post('app_test/driver_earning_detail', 'app_test@driver_earning_detail');
Route::post('app_test/get_faq', 'app_test@get_faq');
Route::post('app_test/report_an_issue', 'app_test@report_an_issue');
Route::post('app_test/get_driver_bank_detail', 'app_test@get_driver_bank_detail');
Route::post('app_test/user_data', 'app_test@user_data');
Route::post('app_test/pay_bill', 'app_test@pay_bill');
Route::post('app_test/current_ride_info', 'app_test@current_ride_info');
Route::post('app_test/driver_reward', 'app_test@driver_reward');
Route::post('app_test/passenger_promo',array('uses' => 'app_test@passenger_promo'));
Route::post('app_test/history_detail_android',array('uses' => 'app_test@history_detail_android'));
Route::post('app_test/cancel_ride_request',array('uses' => 'app_test@cancel_ride_request'));\
Route::post('app_test/transport_mode',array('uses' => 'app_test@transport_mode'));
Route::post('app_test/driver_request',array('uses' => 'app_test@driver_request'));
Route::post('app_test/arrived_at_pickup', array('uses' => 'app_test@arrived_at_pickup'));
Route::post('app_test/become_driver', array('uses' => 'app_test@become_driver'));
Route::post('app_test/city_json', array('uses' => 'app_test@city_json'));


/*---work done by Rubiya----*/
//Route::post('app_test_new/twilio_token','app_test_new@twilio_token');
Route::post('app_test/twilio_token',array('uses' => 'app_test@twilio_token'));
Route::post('app_test/xml_client',array('uses' => 'app_test@xml_client'));
Route::get('app_test/status1',array('uses' => 'app_test@status1'));
