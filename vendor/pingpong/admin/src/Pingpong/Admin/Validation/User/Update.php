<?php namespace Pingpong\Admin\Validation\User;

use Pingpong\Admin\Validation\Validator;
use Illuminate\Support\Facades\Request;

class Update extends Validator
{

    public function rules()
    {
        $id = Request::segment(3);

        $rules = [
            'fname' => 'required',
			'lname' => 'required',
            'email' => 'required|unique:users,email,' . $id,
			 'type' => 'required|in:1,2,3,4',
            'homeUniversityID' => 'required',
            // matriculation year is not required, but will validate if something is entered
            'matriculationYear' => 'digits:4|date_format:"Y"',
            // exchange term and host university id is only required if type is 1
            'exchangeTerm' => 'required_if:type,1|required_if:type,2|required_with:hostUniversityID',
            'hostUniversityID' => 'required_if:type,1|required_if:type,2',
			'homecountry' => 'required_if:homeUniversityID,1|required_with:city, universityName',
            'homecity' => 'required_if:homeUniversityID,1',
            'homeuniversityName' => 'required_if:homeUniversityID,1',
            // country, city and university name is required only if host university id is 1 (Others)
            'hostNewcountry' => 'required_if:hostUniversityID,1|required_with:city, universityName',
            'hostcity' => 'required_if:hostUniversityID,1',
            'hostuniversityName' => 'required_if:hostUniversityID,1',
        ];

        if ($this->has('password')) {
            $rules['password'] = 'required|min:6|max:20';
        }

        return $rules;
    }
	    public function messages()
    {
        $current_year = date('Y');
        return [
            'homeUniversityID.required' => 'This field is required.',
            'matriculationYear.digits' => 'The matriculation year must be 4 digits. (eg. '.$current_year.')',
            'hostUniversityID.required' => 'This field is required.',
            'exchangeTerm.required_if' => 'This field is required.',
            'hostUniversityID.required_if' => 'This field is required.',
			'homecountry.required_if' => 'This field is required.',
            'homecity.required_if' => 'This field is required.',
            'homeuniversityName.required_if' => 'This field is required.',
            'hostNewcountry.required_if' => 'This field is required.',
            'hostcity.required_if' => 'This field is required.',
            'hostuniversityName.required_if' => 'This field is required.',
            
        ];
    }
}
