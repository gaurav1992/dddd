<?php namespace App\Services;use App\User;use Validator;use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
class Registrar implements RegistrarContract {	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		return Validator::make($data, [
			'fname' => 'required|max:250',			
			'lname' => 'required|max:250',			
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
		]);
	}
	/**
	 * Create a new user instance after a valid registration.
	 * @param  array  $data
	 * @return User
	 */	public function create(array $data)	{		
			$user_created=User::create([
			'fname' => $data['fname'],
			'lname' => $data['lname'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			'confirmation_code' =>$data['confirmation_code'] 
			]);
			return $user_created;
			}}
