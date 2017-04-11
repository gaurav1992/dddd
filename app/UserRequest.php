<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class UserRequest extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_requests";
    protected $primaryKey = 'id';
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
		'to_id',
		'type',
    ];
	public static function counttotalbf(){
		$users = DB::table('user_requests')
					->where('type','1')
                    ->count();
					return $users;
					
	}
	public static function counttotalsa(){
		$users = DB::table('user_requests')
					->where('type','2')
                    ->count();
					return $users;
					
	}
	
}
