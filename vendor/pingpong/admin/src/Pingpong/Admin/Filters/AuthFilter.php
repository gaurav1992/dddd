<?php namespace Pingpong\Admin\Filters;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthFilter extends Filter
{

    /**
     * @return mixed
     */
    public function filter()
    {
		//echo Auth::user()->role; die;
        
        if (! Auth::check() or  Auth::user()->is('user')) {
            Auth::logout();

            return Redirect::route('admin.login.index');
        }
    }
}
