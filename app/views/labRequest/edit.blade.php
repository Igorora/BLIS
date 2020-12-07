@extends("layout")
@section("content")

<div>
    <ol class="breadcrumb">
        <li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
        <li>
            <a href="{{ URL::route('test.index') }}">{{ Lang::choice('messages.test',2) }}</a>
        </li>
        <li class="active">{{trans('messages.new-test')}}</li>
    </ol>
</div>
<div class="panel panel-primary">
    <div class="panel-heading ">
        <div class="container-fluid">
            <div class="row less-gutter">
                <div class="col-md-11">
                    <span class="glyphicon glyphicon-adjust"></span>{{trans('messages.new-test')}}
                </div>
                <div class="col-md-1">
                    <a class="btn btn-sm btn-primary pull-right" href="#" onclick="window.history.back();return false;"
                       alt="{{trans('messages.back')}}" title="{{trans('messages.back')}}">
                        <span class="glyphicon glyphicon-backward"></span></a>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <!-- if there are creation errors, they will show here -->
        @if($errors->all())
        <div class="alert alert-danger">
            {{ HTML::ul($errors->all()) }}
        </div>
        @endif

        {{ Form::open(array('route' => 'test.saveNewTest', 'id' => 'form-new-test')) }}
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{trans("messages.patient-details")}}</h3>
                        </div>
                        <div class="panel-body inline-display-details">
                            <span><strong>{{trans("messages.patient-number")}}</strong> {{ $patient->patient_number }}</span>
                            <span><strong>{{ Lang::choice('messages.name',1) }}</strong> {{ $patient->name }}</span>
                            <span><strong>{{trans("messages.age")}}</strong> {{ $patient->getAge() }}</span>
                            <span><strong>{{trans("messages.gender")}}</strong>
                                {{ $patient->gender==0?trans("messages.male"):trans("messages.female") }}</span>
                        </div>
                    </div>
                    <div class="form-group">

                        {{ Form::label('department', trans("messages.department")) }}
                        {{ Form::select('department', [' ' => '--- Select department ---','0' => 'IM','1' => 'GO', '2'=>'Ped', '3'=>'ENT/ORL', '4'=>'Dermato', '5'=>'Stomato', '6'=>'Ophtalmo', '7'=>'Surg', '8'=>'Emergency', '9'=>'ICU', '10'=>'Dialysis','11'=>'ARV Ped','12'=>'ARV IM', '13'=>'Hospital outside'], $requestDprtmt,
                                     array('id' => 'department','class' => 'form-control')) }}
                    </div>
                    <div class="form-group hospital">
                        {{ Form::label('hospital', 'Hospital') }}
                        {{Form::text('hospital', $requestDprtmt, array('id'=>'hospital','placeholder'=>'Reffering hospital','class' => 'form-control'))}}
                    </div>
                    <div class="form-group">
                        {{ Form::hidden('patient_id', $patient->id) }}
                        {{ Form::hidden('request_id', $requestId) }}
                        {{ Form::label('visit_type', trans("messages.visit-type"),['class'=>'visit_type']) }}
                        {{ Form::select('visit_type', ['' => '--- Select visit type ---','0' => trans("messages.out-patient"),'1' => trans("messages.in-patient")], $requestType,
                                     array('id'=>'visitType','class' => 'form-control visit_type')) }}
                    </div>
                    <div class="form-group wardBed" >
                        {{ Form::label('ward', 'Ward') }}
                        {{ Form::select('ward', ['' => '--- Select ward ---',$requestWard=>$requestWard], $requestWard,
                                     array('id'=>'selectWard','class' => 'form-control')) }}

                    </div>
                    <div class="form-group wardBed">
                        {{ Form::label('bed', 'Bed') }}
                        {{Form::text('bed', $requestBed, array('id'=>'bed','placeholder'=>'Bed','class' => 'form-control'))}}
                    </div>
                    <div class="form-group">
                        {{ Form::label('visit_urgency', 'Visit urgency') }}
                        <label class="radio-inline">{{ Form::radio('visit_urgency', 'urgent', $requestUrgency) }} <strong>Urgent!!</strong> </label>
                        <label class="radio-inline">{{ Form::radio('visit_urgency', 'not_urgent', $requestUrgency)}} <strong>Not urgent</strong></label>
                        
                    </div>
                    <div class="form-group">
                        {{ Form::label('physician', trans("messages.physician")) }}
                        {{-- {{Form::text('physician', $requestby, array('placeholder'=>'Requesting Dr','class' => 'form-control'))}} --}}
                        {{ Form::select('physician', ['' => '--- Select Clinician ---','Not readable' => 'Not readable','Not listed' => 'Not listed']+$clinicians, $requestby,
                                     array('id'=>'physician','class' => 'form-control')) }}
                    </div>
                    <div class="form-group nonListedClinician">
                        {{ Form::label('nonListedClinician', 'Non listed Clinician') }}
                        {{Form::text('nonListedClinician', $requestby, array('id'=>'nonListedClinician','placeholder'=>'Type the name','class' => 'form-control'))}}
                    </div>

                    <div class="form-group">
                        {{ Form::label('clinicinfo', trans("messages.clinicinfo")) }}
                        {{Form::textarea('clinicinfo', $requestClinicInfo, array('class' => 'form-control'))}}
                    </div>
                    <div class="form-group">
                        {{ Form::label('tests', trans("messages.select-tests")) }}
                        <div class="form-pane">

                            <table id='testlist' class="table table-striped table-hover table-condensed ">
                                <thead>
                                    <tr>
                                        <th>{{ Lang::choice('messages.test',2) }}</th>
                                        <th>{{ trans('messages.actions') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($testtypes as $key => $value)
                                    <tr>
                                        <td>{{ $value->name }}</td>
                                        <td><label  class="editor-active">
                                                {{Form::checkbox('testtypes[]', $value->id, array_key_exists($value->id,$requestedTestIds)?true:Input::old('testtypes[]'))}}
                                                <!--<input type="checkbox" name="testtypes[]" value="{{-- {{ $value->id}} --}}" />-->
                                            </label>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="form-group actions-row">
                                {{ Form::button("<span class='glyphicon glyphicon-save'></span> ".trans('messages.save-test'),
                                    array('class' => 'btn btn-primary', 'id' => 'submitRequest', 'alt' => 'save_new_test')) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
        @stop