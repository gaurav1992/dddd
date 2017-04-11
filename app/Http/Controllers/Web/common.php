<?php

namespace App\Http\Controllers\Web;

use Auth;
use DB;
use Mail;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Crypt;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use Session;
use Validator;
use Redirect;
use Hash;
use Pingpong\Admin\Uploader\ImageUploader;
use dateTime;
use App\User;
use Socialite;
use Services_Twilio;

class common extends Controller {
	/*--functions start for car page--*/
	public function passengerProfile() {
		die('passengerProfile');
	}
}