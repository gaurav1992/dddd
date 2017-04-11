<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Mail\Mailer;
use Session;
use DB;
use Illuminate\Support\Facades\Redirect;
class AuthController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/
	private $mailer;
	use AuthenticatesAndRegistersUsers;
	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar, Mailer $mailer)
	{
		parent::__construct();
		$this->auth = $auth;
		$this->registrar = $registrar;
        $this->mailer = $mailer;
		$this->middleware('guest', ['except' => 'getLogout']);
	}
		/**
	* Show the application registration form.
	*
	* @return Response
	*/
	public function getRegister()
	{
			return view('auth.register');
	}
	public function postRegister(Request $request)
	{
		$confirmation_code = str_random(30);
		$validator = $this->registrar->validator($request->all());
		if ($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}
		$updateData = $request->all();
		$updateData['confirmation_code'] = $confirmation_code;
		$user = $this->registrar->create($updateData);
		if ($request->get('active')) {
			$this->auth->login($user);
		}
		
		DB::table('dn_role_user')->insert(
					['role_id' => '2', 'user_id' => $user->id,'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
				);
		
		$reciver=$updateData['email'];
		$name=$updateData['fname'];		
		$data=array('confirmation_code'=>$confirmation_code,	'fname'=>$name		);
	
		$this->mailer->send('emails.verify', compact('data'), function($message) use($reciver, $name) {
				$message->to($reciver,$name)
                    ->subject('Flying Chalks - New Registration');
        });
		Session::put('custom_success','You have successfully registered an account! Please check your mailbox for the verification email.');
		return view('auth.login');
	}
	
	public function postLogin(Request $request)
	{
		$this->validate($request, [
			'email' => 'required|email', 'password' => 'required',
		]);

		$credentials = $request->only('email', 'password');
		$verifyCount = DB::table('dn_users')
		->where('confirmed','=',1)
		->where('confirmation_code','=','')
		->where('email','=',$request->email)
		->count();
		
		if( $verifyCount>0){
			if ($this->auth->attempt($credentials, $request->has('remember')))
			{
				
				return Redirect::to('/connect');
			}	
		}else{
			return redirect($this->loginPath())
					->withInput($request->only('email', 'remember'))
					->withErrors([
						'email' => "A verification email has been sent to  " .$request->email.". Please verify to log in.",
					]);
		}
		
		$emailCount = DB::table('dn_users')->where('email','=',$request->email)->count();
		
		if($emailCount==0){
			return redirect($this->loginPath())
					->withInput($request->only('email', 'remember'))
					->withErrors([
						'email' => $this->getFailedLoginMessage(),
					]);
		}else {
			return redirect($this->loginPath())
					->withInput($request->only('email', 'remember'))
					->withErrors([
						'email' => 'Incorrect password. Please try again.',
					]);
		}
		
		
	}


}
