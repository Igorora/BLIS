@extends("layout")
@section("content")
	<div>
		<ol class="breadcrumb">
		  <li><a href="{{{URL::route('user.home')}}}">{{ trans('messages.home') }}</a></li>
		  <li><a href="{{ URL::route('patient.index') }}">{{ Lang::choice('messages.patient',2) }}</a></li>
		  <li class="active">{{trans('messages.create-patient')}}</li>
		</ol>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading ">
			<span class="glyphicon glyphicon-user"></span>
			{{trans('messages.create-patient')}}
		</div>
		<div class="panel-body">
		<!-- if there are creation errors, they will show here -->
			
			@if($errors->all())
				<div class="alert alert-danger">
					{{ HTML::ul($errors->all()) }}
				</div>
			@endif
			{{ Form::open(array('url' => 'patient', 'id' => 'form-create-patient')) }}
				<div class="col-md-4">
					<div class="form-group">
						{{ Form::label('patient_number', trans('messages.patient-number')) }}
						{{ Form::text('patient_number',Input::old('patient_number'),
							array('class' => 'form-control')) }} 
					</div>
					<div class="form-group">
						{{ Form::label('name', trans('messages.names')) }}
						{{ Form::text('name', Input::old('name'), array('class' => 'form-control')) }}
					</div> 
					<div class="">
						{{ Form::label('ageselector', 'Age Input Selector') }}
						
						
						<div class="form-group  col-md-12">
							<div class="col-md-6">{{ Form::radio('ageselector', '0', true) }}
							<span class="input-tag">Age</span></div>
							<div  class="col-md-6">{{ Form::radio("ageselector", '1', false) }}
							<span class="input-tag">Date Picker</span></div>
						</div>
						
						
					</div>
					<div class="form-group" id="age">
						{{ Form::label('dob', 'Age in years') }}
						{{ Form::text('age', Input::old('dob'), 
							array('class' => 'form-control', 'placeholder'=>'Enter the age in years')) }}
					</div>
					<div class="form-group" id="date-picker">
						{{ Form::label('dob', trans('messages.date-of-birth')) }}
						{{ Form::text('dob', Input::old('dob'), 
							array('class' => 'form-control standard-datepicker' ,  'placeholder'=>'Click and select a date')) }}
					</div>
					<div class="form-group">
						{{ Form::label('gender', trans('messages.gender')) }}
						<div>{{ Form::radio('gender', '0', true) }}
						<span class="input-tag">{{trans('messages.male')}}</span></div>
						<div>{{ Form::radio("gender", '1', false) }}
						<span class="input-tag">{{trans('messages.female')}}</span></div>
					</div>
					
					<div class="form-group">
						{{ Form::label('phone_number', trans('messages.phone-number')) }}
						{{ Form::text('phone_number', Input::old('phone_number'), array('class' => 'form-control')) }}
					</div>
					<div class="form-group">
						{{ Form::label('email', trans('messages.email-address')) }}
						{{ Form::email('email', Input::old('email'), array('class' => 'form-control')) }}
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Country</label>
						<select name="country" class="form-control select-single" id="country" >
								{{$countr_options}}
						</select>
					</div>
					<div class="form-group">
						<label>Province</label>
						<select name="province" class="form-control" id="province">
							<option value="">Select Province</option>
							<option value="Not provided">Not provided</option>
								
						</select>
					</div>
						<div class="form-group">
						<label>District</label>
						<select name="district" class="form-control" id="district">
								<option value="">Select District</option>
							<option value="Not provided">Not provided</option>
						</select>
					</div>
						<div class="form-group">
						<label>Sector</label>
						<select name="sector" class="form-control" id="sector">
								<option value="">Select Sector</option>
							<option value="Not provided">Not provided</option>
						</select>
					</div>
					<div class="form-group">
						<label>Cell</label>
						<select name="cell" class="form-control" id="cell">
								<option value="">Select Cell</option>
							<option value="Not provided">Not provided</option>
						</select>
					</div>
					<div class="form-group">
						<label>Village</label>
						<select name="village" class="form-control" id="village">
							<option value="">Select Village</option>
							<option value="Not provided">Not provided</option>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Marital Status </label>
						<select name="marital_Status" class="form-control" id="marital_Status">
							<option value="">Seelect status</option>
							<option value="Not provided">Not provided</option>
							<option value="single">Single</option>
							<option value="married">Married</option>
							<option value="divorced">Divorced</option>
							<option value="unkown">Unkown</option>
						</select>
					</div>
					<div class="form-group">
						<label>Living arrangement </label>
						<select name="living_arrangement" class="form-control" id="living_arrangement">
							<option value="">Seelect all apply</option>
							<option value="Not provided">Not provided</option>
							<option value="with_familly">With familly</option>
							<option value="alone">alone</option>
							<option value="rural">rural</option>
							<option value="urban">urban</option>
							<option value="with_mother">With mother</option>
							<option value="unkown">unkown</option>
						</select>
					</div>
					<div class="form-group">
						<label>Occupation </label>
						<select name="occupation" class="form-control" id="occupation">
							<option value="">Seelect all apply</option>
							<option value="Not provided">Not provided</option>
							<option value="none">none</option>
							<option value="own_busness">Own busness</option>
							<option value="others_busness">works for others</option>
						</select>
					</div>
					<div class="form-group">
						<label>Religion </label>
						<select name="religion" class="form-control" id="religion">
							<option value="">Seelect one</option>
							<option value="Not provided">Not provided</option>
							<option value="islam">islam</option>
							<option value="catholic">Catholic</option>
							<option value="jehova_witness">Protestant</option>
							<option value="traditional">Traditional</option>
							<option value="none">None</option>
						</select>
					</div>
					<div class="form-group">
						<label>Highest education level </label>
						<select name="education" class="form-control" id="education">
							<option value="">Seelect one</option>
							<option value="Not provided">Not provided</option>
							<option value="nursery">Nursery</option>
							<option value="primary">Primary</option>
							<option value="secondary">Secondary</option>
							<option value="undergraduate">Undergraduate</option>
							<option value="postgraduate">Postgraduate</option>
						</select>
					</div>

				</div>
					
				<div class="form-group col-md-12 text-center">
					{{ Form::button('<span class="glyphicon glyphicon-save"></span> '.trans('messages.save'), 
						['class' => 'btn btn-primary', 'onclick' => 'submit()']) }}
				</div>
		
			{{ Form::close() }}
		</div>
	</div>
@stop	