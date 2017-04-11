@if(isset($model))
{!! Form::model($model, ['method' => 'PUT', 'files' => true, 'route' => ['admin.users.update', $model->id]]) !!}
@else
{!! Form::open(['files' => true, 'route' => 'admin.users.store']) !!}
@endif
	<div class="form-group">
		{!! Form::label('fname', 'First Name:') !!}
		{!! Form::text('fname', null, ['class' => 'form-control']) !!}
		{!! $errors->first('fname', '<div class="text-danger">:message</div>') !!}
	</div>
	<div class="form-group">
		{!! Form::label('lname', 'Last Name:') !!}
		{!! Form::text('lname', null, ['class' => 'form-control']) !!}
		{!! $errors->first('lname', '<div class="text-danger">:message</div>') !!}
	</div>
	<div class="form-group">
		{!! Form::label('email', 'Email:') !!}
		{!! Form::email('email', null, ['class' => 'form-control']) !!}
		{!! $errors->first('email', '<div class="text-danger">:message</div>') !!}
	</div>
	<div class="form-group">
		{!! Form::label('password', 'Password:') !!}
		{!! Form::password('password', ['class' => 'form-control']) !!}
		{!! $errors->first('password', '<div class="text-danger">:message</div>') !!}
	</div>
	<?php if(@$countryList){ ?>
		<div class="form-group">
			{!! Form::label('TypeofUser', 'Type of user:') !!}
			{!! Form::select('type', array(""=>"Please select") + $userTypes,Input::old('type')!=''?Input::old('type'):(@$exchangeDetail->type!=''?@$exchangeDetail->type:''), array('id' =>
											'type','class'=>'form-control selectMenu', 'autocomplete'=>'off')) !!}
			{!! $errors->first('type', '<div class="text-danger">:message</div>') !!}
		</div>
		
		<div class="form-group">
			{!! Form::label('Home University', 'Home University:') !!}
			   {!! Form::select('homeUniversityID', array(""=>"Please select") + $homeUnivList,Input::old('homeUniversityID')!=''?Input::old('homeUniversityID'):(@$exchangeDetail->homeUniversity->id!=''?@$exchangeDetail->homeUniversity->id:''), array('id' =>
											'homeUniversityID','class'=>'form-control selectMenu', 'autocomplete'=>'off')) !!}
			{!! $errors->first('homeUniversityID', '<div class="text-danger">:message</div>') !!}
		</div>
		<div class="form-group" id="newHomeUniversityDiv" style="display:{{ Input::old('homeUniversityID')=='1'?'block':'none'}};">
			{!! Form::label('Country', 'Country:') !!}
			  {!! Form::select('homecountry', array(""=>"Please select") + $countryList,Input::old('homecountry'), array('id' =>
															'homecountry','class'=>'form-control selectMenu', 'autocomplete'=>'off')) !!}
			{!! $errors->first('homecountry', '<div class="text-danger">:message</div>') !!}

			{!! Form::label('City', 'City:') !!}
			   {!! Form::text('homecity',Input::old('homecity'),array('id'=>'homecity','class'=>'form-control')) !!}
			{!! $errors->first('homecity', '<div class="text-danger">:message</div>') !!}

			{!! Form::label('University Name', 'University Name:') !!}
			    {!! 	Form::text('homeuniversityName',Input::old('homeuniversityName'),array('id'=>'homeuniversityName','class'=>'form-control')) !!}
			{!! $errors->first('homeuniversityName', '<div class="text-danger">:message</div>') !!}
		</div>
		
		<div class="form-group">
			{!! Form::label('Matriculation Year', 'Matriculation Year (format: YYYY):') !!}
			   {!! Form::text('matriculationYear',Input::old('matriculationYear')!=''?Input::old('matriculationYear'):(@$exchangeDetail->matriculationYear!=''?@$exchangeDetail->matriculationYear:''),array('id'=>'matriculationYear','class'=>'form-control')) !!}
			{!! $errors->first('matriculationYear', '<div class="text-danger">:message</div>') !!}
		</div>
		
		<div class="form-group type3-4" {{ Input::old('type')=='3' || Input::old('type')=='4' || @$exchangeDetail->type=='3' || @$exchangeDetail->type=='4'?'style=display:none;':''}}>
			{!! Form::label('Host University', 'Host University:') !!}
			     {!! Form::select('hostUniversityID', array(""=>"Please select") + $hostUnivList,Input::old('hostUniversityID')!=''?Input::old('hostUniversityID'):(@$exchangeDetail->hostUniversity->id!=''?@$exchangeDetail->hostUniversity->id:''), array('id' =>
											'hostUniversityID','class'=>'form-control selectMenu', 'autocomplete'=>'off')) !!}
			{!! $errors->first('hostUniversityID', '<div class="text-danger">:message</div>') !!}
		</div>
		
		<div class="form-group type3-4 hostCountryBlk" {{ Input::old('type')=='3' || Input::old('type')=='4' || @$exchangeDetail->type=='3' || @$exchangeDetail->type=='4'?'style=display:none;':''}}>
			{!! Form::label('Host Country', 'Host Country:') !!}
			      {!! Form::text('hostCountry',Input::old('hostCountry')!=''?Input::old('hostCountry'):(@$exchangeDetail->hostUniversity->city->country->countryName!=''?@$exchangeDetail->hostUniversity->city->country->countryName:''),array('id'=>'hostCountry','class'=>'form-control', 'readonly'=>'readonly')) !!}
			{!! $errors->first('hostCountry', '<div class="text-danger">:message</div>') !!}
		</div>
		<div class="form-group type3-4" id="newHostUniversityDiv" style="display:{{ Input::old('hostUniversityID')=='1'?'block':'none'}};">
			<div class="form-group">
				{!! Form::label('Country', 'Country:') !!}
			       {!! Form::select('hostNewcountry', array(""=>"Please select") + $countryList,Input::old('hostNewcountry'), array('id' =>
															'hostNewcountry','class'=>'form-control selectMenu', 'autocomplete'=>'off')) !!}
				{!! $errors->first('hostNewcountry', '<div class="text-danger">:message</div>') !!}
			</div>
			<div class="form-group">
				{!! Form::label('City', 'City:') !!}
			        {!! Form::text('hostcity',Input::old('hostcity'),array('id'=>'hostcity','class'=>'form-control')) !!}
				{!! $errors->first('hostcity', '<div class="text-danger">:message</div>') !!}
			</div>
			<div class="form-group">
				{!! Form::label('University Name', 'University Name:') !!}
			        {!! 	Form::text('hostuniversityName',Input::old('hostuniversityName'),array('id'=>'hostuniversityName','class'=>'form-control')) !!}
				{!! $errors->first('hostuniversityName', '<div class="text-danger">:message</div>') !!}
			</div>
		</div>
		<div class="form-group type3-4" {{ Input::old('type')=='3' || Input::old('type')=='4' || @$exchangeDetail->type=='3' || @$exchangeDetail->type=='4' ?'style=display:none;':''}}>
			{!! Form::label('Student Term', 'Student Term:') !!}
			         {!! Form::select('exchangeTerm', array(""=>"Please select", "Spring 2017"=>"Spring 2017", "Summer 2017"=>"Summer 2017", "Fall 2017"=>"Fall 2017", "Winter 2017"=>"Winter 2017", "Spring 2016"=>"Spring 2016", "Summer 2016"=>"Summer 2016", "Fall 2016"=>"Fall 2016", "Winter 2016"=>"Winter 2016", "Spring 2015"=>"Spring 2015", "Summer 2015"=>"Summer 2015", "Fall 2015"=>"Fall 2015", "Winter 2015"=>"Winter 2015", "Spring 2014"=>"Spring 2014", "Summer 2014"=>"Summer 2014", "Fall 2014"=>"Fall 2014", "Winter 2014"=>"Winter 2014", "Spring 2013"=>"Spring 2013", "Summer 2013"=>"Summer 2013", "Fall 2013"=>"Fall 2013", "Winter 2013"=>"Winter 2013", "Spring 2012"=>"Spring 2012", "Summer 2012"=>"Summer 2012", "Fall 2012"=>"Fall 2012", "Winter 2012"=>"Winter 2012"),Input::old('exchangeTerm')!=''?Input::old('exchangeTerm'):(@$exchangeDetail->exchangeTerm!=''?@$exchangeDetail->exchangeTerm:''), array('id' =>
											'exchangeTerm','class'=>'form-control selectMenu', 'autocomplete'=>'off')) !!}
				{!! $errors->first('exchangeTerm', '<div class="text-danger">:message</div>') !!}
		
		</div>
	<?php } ?>
	
	
	<?php if(is_array(@$role) && in_array('1',@$role)){ ?>
	<div class="form-group" style="display:none;">
		{!! Form::label('role', 'Role:') !!}
		{!! Form::select('role', $roles, isset($role) ? $role : null, ['class' => 'form-control']) !!}
		{!! $errors->first('role', '<div class="text-danger">:message</div>') !!}
	</div>
	
	<?php }else { ?>
	<div class="form-group" style="display:none;">
		{!! Form::text('role', '2', ['class' => 'form-control']) !!}
	</div>
	<?php } ?>
	
	
	<div class="form-group">
		{!! Form::submit(isset($model) ? 'Update' : 'Save', ['class' => 'btn btn-primary']) !!}
	</div>
{!! Form::close() !!}
