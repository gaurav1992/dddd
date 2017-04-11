<?php namespace Pingpong\Admin\Validation\University;

use Pingpong\Admin\Validation\Validator;
use Illuminate\Support\Facades\Request;

class Update extends Validator
{

    public function rules()
    {
        return [
            'tab'=>'required',
			'universityName' => 'required_if:tab,1',
            'country' => 'required_if:tab,1',
            'Overview' =>'required_if:tab,1',
            'Academics' => 'required_if:tab,1',
            'MyCampus' => 'required_if:tab,1',
            'Studentlife' =>'required_if:tab,1',
            'Surrounding' => 'required_if:tab,1',
            'Accessibility' => 'required_if:tab,1',
            'Transportation' => 'required_if:tab,2',
            'BankingServices' => 'required_if:tab,2',
            'postoffice' => 'required_if:tab,2',
            'medicalservices' => 'required_if:tab,2',
            'Telecommunications' => 'required_if:tab,2',
            'SurvivalGuide' => 'required_if:tab,2',
            'Consolidated' => 'required_if:tab,3',

			
			
        ];
    }
}
