<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class ExchangeStudentFormRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'type'=>'required',
            'homeUniversityID' => 'required',
            // matriculation year is not required, but will validate if something is entered
            'matriculationYear' => 'required|digits:4|date_format:"Y"',
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
			'fname' => 'required',
			'lname' => 'required',
			'email' => 'required|email',
        ];
    }
    public function messages()
    {
        $current_year = date('Y');
        return [
		
			'contact.required' => 'This field is required',
			'email.email' => 'Please enter valid email address',
            'homeUniversityID.required' => 'This field is required.',
			'matriculationYear.required' => 'This field is required.',
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
			'fname.required' => 'This field is required',
			'lname.required' => 'This field is requ ired',
			'email.required' => 'This field is required',
			'contact.required' => 'This field is required',
			'email.email' => 'Please enter valid email address',
            
        ];
    }

}
