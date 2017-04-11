<?php namespace Pingpong\Admin\Entities;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Pingpong\Trusty\Traits\TrustyTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{

    use Authenticatable, CanResetPassword, TrustyTrait;

    protected $table = 'dn_users';
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

    /**
     * The fillable property.
     *
     * @var array
     */
    //protected $fillable = array('first_name','lname', 'username', 'email', 'password', 'status');
   // protected $guarded = array('first_name','last_name');

    /**
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }

    /**
     * Get gravatar url.
     *
     * @param  integer $size
     * @param  string  $default
     * @param  string  $rating
     * @return string
     */
    public function gravatar($size = 60, $default = 'mm', $rating = 'g')
    {
        $email = $this->email;
        
        return 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . "?s={$size}&d={$default}&r={$rating}";
    }

  
	
	/*public function roles()
	{
		return $this->belongsToMany('Role','role_user');
	}*/
}
