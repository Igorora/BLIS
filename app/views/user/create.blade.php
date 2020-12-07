@extends("layout")
@section("content")
	<div>
		<ol class="breadcrumb">
		  <li><a href="{{{URL::route('user.home')}}}">{{ trans('messages.home') }}</a></li>
		  <li><a href="{{ URL::route('user.index') }}">{{ Lang::choice('messages.user', 1) }}</a></li>
		  <li class="active">{{ trans('messages.create-user') }}</li>
		</ol>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading ">
			<span class="glyphicon glyphicon-user"></span>
			{{ trans('messages.create-user') }}
		</div>
		<div class="panel-body">
		<!-- if there are creation errors, they will show here -->
			
			@if($errors->all())
				<div class="alert alert-danger">
					{{ HTML::ul($errors->all()) }}
				</div>
			@endif

			{{ Form::open(array('route' => array('user.index'), 'id' => 'form-create-user', 'files' => true)) }}

				<div class="form-group">
					{{ Form::label('username', trans('messages.username')) }}
					{{ Form::text('username', Input::old('username'), ["placeholder" => "Username",
						'class' => 'form-control']) }}
				</div>
				<div class="form-group">
					{{ Form::label('password', Lang::choice('messages.password',1)) }}
					{{ Form::password('password', ["placeholder" => "Password", 'class' => 'form-control']) }}
				</div>
				<div class="form-group">
					{{ Form::label('password_confirmation', trans('messages.repeat-password')) }}
					{{ Form::password('password_confirmation', ["placeholder" => "Retype Password",'class' => 'form-control']) }}
				</div>
				<div class="form-group">
					{{ Form::label('full_name', trans('messages.full-name')) }}
					{{ Form::text('full_name', Input::old('full_name'), ["placeholder" => "Name", 
						'class' => 'form-control']) }}
				</div>
				<div class="form-group">
					{{ Form::label('phone', trans('messages.phone-address')) }}
					{{ Form::text('phone', Input::old('phone'), ["placeholder" => "Phone number", 
						'class' => 'form-control']) }}
				</div>
				<div class="form-group">
					{{ Form::label('email', trans('messages.email-address')) }}
					{{ Form::email('email', Input::old('email'), ["placeholder" => "email@chub.com", 
						'class' => 'form-control']) }}
				</div>
				<div class="form-group">
					{{ Form::label('designation', trans('messages.designation')) }}
					{{ Form::text('designation', Input::old('designation'), ["placeholder" => "Lab Technologist", 
						'class' => 'form-control']) }}
				</div>
                <div class="form-group">
                    {{ Form::label('gender', trans('messages.gender')) }}
                    <div>{{ Form::radio('gender', Patient::MALE, true) }}
                    	<span class='input-tag'>{{trans('messages.male')}}</span></div>
                    <div>{{ Form::radio("gender", Patient::FEMALE, false) }}
                    	<span class='input-tag'>{{trans('messages.female')}}</span></div>
                </div>
                <div class="form-group">
                	{{ Form::label('image', trans('messages.photo')) }}
                    {{ Form::file("image") }}
                </div>
				<div class="form-group actions-row">
					{{ Form::button('<span class="glyphicon glyphicon-save"></span> '.trans('messages.save'), 
						['class' => 'btn btn-primary', 'onclick' => 'submit()']
					) }}
				</div>

			{{ Form::close() }}
		</div>
	</div>
@stop