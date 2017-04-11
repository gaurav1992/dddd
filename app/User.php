<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'dn_users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['first_name','last_name', 'email','gender','contact_number','dob','anniversary', 'password','confirmation_code','provider','provider_id','confirmed','avatar','contact'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
	
	public static function getTotalUsers() {
	
		$users = DB::table('role_user')
						->leftJoin('users', 'role_user.user_id', '=', 'users.id')	
						->where('role_id','2')
						->count();
    	
		return $users+70; 
	}
	
	public function exchange()
    {
		return $this->hasOne('App\ExchangeStudent');
    }

}
