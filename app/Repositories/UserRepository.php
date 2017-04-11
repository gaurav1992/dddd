<?php namespace App\Repositories;

use App\User;
use DB;
use Auth;
use Session;
use Illuminate\Support\Facades\Redirect;
class UserRepository {

    public function findByUserNameOrCreate($userData) {
			
		$user = User::where('provider_id', '=', $userData->id)->first();
		
		
		if(!$user) {
			if(Auth::check()) {
				DB::table('users')
				 				->where('id',Auth::user()->id)
								->update(array('provider_id'=>$userData->id,'avatar'=>$userData->avatar));
				$user = User::where('provider_id', '=', $userData->id)->first();				
			}else{
				
				if(!empty($userData->email)){
					
					$user = User::create([
						'provider_id' => $userData->id,
						'fname' => $userData->user['first_name'],
						'lname' => $userData->user['last_name'],
						'username' => $userData->nickname,
						'email' => $userData->email,
						'avatar' => $userData->avatar,
						'confirmed' => 1,
					]);
					
					DB::table('role_user')->insert(
						['role_id' => '2', 'user_id' => $user->id,'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
					);
				}
			}
           
        }
	
        //$this->checkIfUserNeedsUpdating($userData, $user);
        return $user;
    }

    public function checkIfUserNeedsUpdating($userData, $user) {

        $socialData = [
            'avatar' => $userData->avatar,
            'email' => $userData->email,
            'fname' => $userData->user['first_name'],
            'lname' => $userData->user['last_name'],
            'username' => $userData->nickname,
        ];
        $dbData = [
            'avatar' => $user->avatar,
            'email' => $user->email,
            'fname' => $user->fname,
            'lname' => $user->lname,
            'username' => $user->username,
        ];
		
		/*echo '<pre>';print_r($socialData).'<br />';
		echo '<pre>';print_r($dbData);die;
		*/
        if (array_diff($socialData, $dbData)) {
            $user->avatar = $userData->avatar;
            //$user->email = $userData->email;
            //$user->fname = $userData->user['first_name'];
           // $user->lname = $userData->user['last_name'];
           // $user->username = $userData->nickname;
            $user->save();
        }
		
		return true;
    }
}