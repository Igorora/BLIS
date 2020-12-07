@extends("layout")
@section("content")
<div>
    <ol class="breadcrumb">
        <li><a href="{{{URL::route('user.home')}}}">{{ trans('messages.home') }}</a></li>
        <li class="active"><a href="{{ URL::route('reports.patient.index') }}">{{ Lang::choice('messages.report', 2) }}</a></li>
        <li class="active">{{ trans('messages.patient-report') }}</li>
    </ol>
</div>
<div class='container-fluid'>
    {{ Form::open(array('url' => 'patientreport/'.$patient->id, 'class' => 'form-inline', 'id' => 'form-patientreport-filter', 'method'=>'POST')) }}
    {{ Form::hidden('patient', $patient->id, array('id' => 'patient')) }}
    <div class="row">
        <div class="col-sm-3">
            <label class="checkbox-inline">
                {{ Form::checkbox('pending', "1", isset($pending)) }}{{trans('messages.include-pending-tests')}}
            </label>
        </div>
        <div class="col-sm-3">
            <div class="row">
                <div class="col-sm-2">
                    {{ Form::label('start', trans("messages.from")) }}</div><div class="col-sm-1">
                    {{ Form::text('start', isset($input['start'])?$input['start']:null,
			                array('class' => 'form-control standard-datepicker')) }}
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="row">
                <div class="col-sm-2">
                    {{ Form::label('end', trans("messages.to")) }}
                </div>
                <div class="col-sm-1">
                    {{ Form::text('end', isset($input['end'])?$input['end']:null,
		                    array('class' => 'form-control standard-datepicker')) }}
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="row">
                <div class="col-sm-3 text-center">
                    {{ Form::button("<span class='glyphicon glyphicon-filter'></span> ".trans('messages.view'),
			                    array('class' => 'btn btn-primary', 'id' => 'filter', 'type' => 'submit')) }}
                </div>
                
                <div class="col-sm-6 text-center" id="printbutton">

                <button class="btn btn-success @if(!$allCompleteVerified) hidden @endif" id="pdfVerified" type="button" data-toggle="modal" data-target="#result-transmission"><span class="glyphicon glyphicon-download-alt" ></span> Print</button>
                </div>
                

                {{-- @if(count($tests)&& (count($verified) == count($tests))) --}}
                <!-- <div class="col-sm-6">
				{{-- {{ Form::submit(trans('messages.export-to-word'), array('class' => 'btn btn-success', 
				'id' => 'word', 'name' => 'word')) }}--}}
                </div> -->
                {{-- @endif --}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                {{ Form::label('testCategory', Lang::choice('messages.test-category',1)) }}
                {{ Form::select('testCategory', array(0 => '...Select service...')+$labSections,
                    Input::old('testCategory'), array('class' => 'form-control')) }}
            </div>
        </div>
    </div>
    {{ Form::hidden('visit_id', $visit, array('id'=>'visit_id')) }}
    {{ Form::close() }}
</div>
<br />
<div class="panel panel-primary" id="patientReport" data-report="{{str_replace(" ", "_", $patient->name).'_' . $patient->patient_number . '_'.date("Ymdhi") }}">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-user"></span>
        {{ trans('messages.patient-report') }}
    </div>
    <div class="panel-body">
        @if($error!='')
        <!-- if there are search errors, they will show here -->
        <div class="alert alert-info">{{ $error }}</div>
        @else

        <div id="report_content">
            @include("reportHeader")
            <strong>
                <p>
                    {{trans('messages.patient-report').' - '.date('d-m-Y')}}
                </p>
            </strong>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>{{ trans('messages.patient-name')}}</th>
                        @if(Entrust::can('view_names'))
                        <td>{{ $patient->name }}</td>
                        @else
                        <td>N/A</td>
                        @endif
                        <th>{{ trans('messages.gender')}}</th>
                        <td>{{ $patient->getGender(false) }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('messages.patient-id')}}</th>
                        <td>{{ $patient->patient_number}}</td>
                        <th>{{ trans('messages.age')}}</th>
                        <td>{{ $patient->getAge()}}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('messages.patient-lab-number')}}</th>
                        <td>{{ $visit? $visit : $patient->id }}</td>
                        <th>{{ trans('messages.requesting-facility-department')}}</th>
                        <td>{{$department.' '.$visitType.' '.$ward.' '.$bed.' '.$requestedBY}}</td>
                    </tr>
					<tr>
                        <th>Registered by</th>
                        <td>{{$registeredBy." <span class='label label-success'>" . $registeredByPhone."</span> "}}</td>
                        <th>Registration time</th>
                        <td>{{$requestDate}}</td>
                    </tr>
                    <tr>
                        <th>Clinical info </th>
                        
                        <td colspan="3">{{$clinicInfo}}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <tbody>
                    
                    <tr>
                        <th class="col-md-1">{{Lang::choice('messages.test-type', 1)}}</th>
                        <th class="col-md-7">
                            <div class="row">
                                <span class="col-md-3">Measure name</span>
                                <span class="col-md-4">Result</span>
                                <span class="col-md-3">Reference</span>
                                <span class="col-md-2">Unit</span>                            
                            </div>
                        </th>

                        <th class="col-md-4">Test details</th>


                    </tr>
                    @forelse($tests as $test)
                    <?php $isResultAbnormal=[] ?>
                    <tr>
                        <td >{{ $test->testType->name }}</td>
                        <td>
                          <table class="table  borderless ">
                            
                              
                                @foreach($test->testResults as $result)
                                <?php 
                                $measure=Measure::find($result->measure_id);
                                $measureType=$measure->measure_type_id;
                                $measureResult=$result->result;
                                ?>
                                 @if($measureType==1)
                                    <?php
                                    $measureRanges= explode('-',trim(Measure::getRange($test->visit->patient, $result->measure_id, $result->time_entered ),'()'));                                  
                                    $rangeMin=$measureRanges[0];
                                    $rangeMax=$measureRanges[1];
                                    $isResultAbnormal[$result->id]=(Float)$measureResult < (Float)$rangeMin || (Float)$measureResult > (Float)$rangeMax;
                                    ?>
                                  <tr class="row {{$isResultAbnormal[$result->id] ? ' danger' : ''  }}">
                                      <td class="col-md-3"><strong>{{ $measure->name }} </strong></td>
                                      <td class="col-md-4">{{ $measureResult }}</td>
                                      <td class="col-md-3">{{'('.$rangeMin.'-'.$rangeMax.')'}}</td>
                                      <td class="col-md-2">{{ $measure->unit }}</td>
                                  </tr>
                                  @else
                                  <tr class="row">
                                      <td class="col-md-3"><strong>{{ $measure->name }} </strong></td>
                                      <td colspan="3" class="col-md-9">{{ $measureResult }}</td>
                                      
                                  </tr>
                                  @endif
                                @endforeach
                              
                          </table>  
                            
							        @if(count($test->testType->organisms)>0)
        <div class="panel panel-danger">  <!-- Patient Details -->
            <div class="panel-heading">
                <h3 class="panel-title">{{trans("messages.culture-worksheet")}}</h3>
            </div>
            <div class="panel-body">
                <p><strong>{{trans("messages.culture-work-up")}}</strong></p>
                <table class="table table-bordered">
                    <thead>

                    </thead>
                    <tbody id="tbbody">
                        <tr>
                            <th width="15%">{{ trans('messages.date')}}</th>
                            <th width="10%">{{ trans('messages.tech-initials')}}</th>
                            <th>{{ trans('messages.observations-and-work-up')}}</th>
                        </tr>
                        @if(($observations = $test->culture) != null)
                        @foreach($observations as $observation)
                        <tr>
                            <td>{{ $observation->created_at }}</td>
                            <td>{{ User::find($observation->user_id)->name }}</td>
                            <td>{{ $observation->observation }}</td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="3">{{ trans('messages.no-data-found') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <p><strong>{{trans("messages.susceptibility-test-results")}}</strong></p>
                <div class="row">
                    @if(count($test->susceptibility)>0)
                    @foreach($test->organisms->unique('id') as $organism)
                    <div class="row">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th colspan="3">{{ $organism->name }}</th>
                                </tr>
                                <tr>
                                    <th width="50%">{{ Lang::choice('messages.drug',1) }}</th>
                                    <th>{{ trans('messages.zone-size')}}</th>
                                    <th>{{ trans('messages.interp')}}</th>
                                </tr>
                                @foreach($organism->drugs as $drug)
                                @if($drugSusceptibility = Susceptibility::getDrugSusceptibility($test->id, $organism->id, $drug->id))
                                @if($drugSusceptibility->interpretation)
                                <tr>
                                    <td>{{ $drug->name }}</td>
                                    <td>{{ $drugSusceptibility->zone!=null?$drugSusceptibility->zone:'' }}</td>
                                    <td>{{ $drugSusceptibility->interpretation!=null?$drugSusceptibility->interpretation:'' }}</td>
                                </tr>
                                @endif
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div> <!-- ./ panel-body -->
        @endif
						 @if(in_array(true, $isResultAbnormal, true))
                         <p><strong>Remarks : </strong>
                         	@if($test->interpretation == '')
                         	{{"This test show out of range result or was not measured on at least one of its numeric measure."}}
                         	@else
                         	{{ "This test show out of range result or was not measured on at least one of its numeric measure. ".$test->interpretation}}

                         	@endif
						</p>
						 @else
						 <p><strong>Remarks : </strong>{{ $test->interpretation == '' ? '' : $test->interpretation }}</p>
						 @endif
						</td>

                        <td>
                            @if($test->test_status_id == Test::VERIFIED || $test->test_status_id == Test::COMPLETED)
                            <p><strong>{{trans('messages.tested-by')}}: </strong> {{ $test->testedBy->name}} {{ $test->testedBy->phone ? "<span class='label label-success'>" . $test->testedBy->phone."</span> " : "<span class='label label-success'>" . $test->testType->testCategory->phone.  "</span> " }}</p>
                            <p><strong>{{trans('messages.results-entry-date')}}: </strong>{{ $test->time_completed }} </p>
                            @else
                            <p><strong>Specimen status: </strong> {{$test->specimen->specimenStatus->name.' and test is '.$test->testStatus->name}}<p>
                                @endif
                            
                            
                            @if($test->test_status_id == Test::VERIFIED)
                            <p>      <strong>{{trans('messages.verified-by')}}: </strong> {{ $test->verifiedBy->name }} {{ $test->verifiedBy->phone ? "<span class='label label-success'>" . $test->verifiedBy->phone."</span> " : "<span class='label label-success'>" . $test->testType->testCategory->phone.  "</span> " }}</p>
                            <p><strong>{{trans('messages.date-verified')}}: </strong> {{ $test->time_verified }}</p>
                            @else
                                 <p class="hidden bg-success ajaxVerificationByOther"><strong>Test was verified by some one else</strong> </p>
                                 <p class="hidden ajaxVerification verifBy">      <strong>{{trans('messages.verified-by')}}: </strong> {{ Auth::user()->name }} {{ "<span class='label label-success'>" . Auth::user()->phone."</span> " }}</p>
                                <p class="hidden ajaxVerification verifDate"><strong>{{trans('messages.date-verified')}}: </strong> </p>
								<p class="bg-info verifStatus"><strong>Test verification pending</strong> </p>
                                @if(Auth::user()->can('verify_test_results') && (Auth::user()->id != $test->tested_by || Auth::user()->can('verify_own')) && $test->test_status_id == Test::COMPLETED)
                                    
                                    <a class="btn btn-sm btn-success verify-test" href="javascript:void(0)" data-test-id="{{$test->id}}" data-ajax-verify="ajaxVerify" data-url="{{ URL::route('test.verify', array($test->id)) }}">
                                        <span class="glyphicon glyphicon-thumbs-up"></span>
                                        {{trans('messages.verify')}}
                                    </a>
                                @endif 
                            @endif
                            
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8">{{trans("messages.no-records-found")}}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if(Entrust::can('view_specimen_details'))
            <table class="table table-bordered" id="specimen_details">
                <tbody>
                    <tr>
                        <th colspan="7">{{trans('messages.specimen')}}</th>
                    </tr>
                    <tr>
                        <th>{{ Lang::choice('messages.specimen-type', 1)}}</th>
                        <th>{{ Lang::choice('messages.test', 2)}}</th>
                        <th>{{ trans('messages.date-ordered') }}</th>
                        <th>{{ Lang::choice('messages.test-category', 2)}}</th>
                        <th>{{ trans('messages.specimen-status')}}</th>
                        <th>{{ trans('messages.collected-by')."/".trans('messages.rejected-by')}}</th>
                        <th>{{ trans('messages.date-checked')}}</th>
                    </tr>
                    @forelse($tests as $test)
                    <tr>
                        <td>{{ $test->specimen->specimenType->name }}</td>
                        <td>{{ $test->testType->name }}</td>
                        <td>{{ $test->isExternal()?$test->external()->request_date:$test->time_created }}</td>
                        <td>{{ $test->testType->testCategory->name }}</td>
                        @if($test->specimen->specimen_status_id == Specimen::NOT_COLLECTED)
                        <td>{{trans('messages.specimen-not-collected')}}</td>
                        <td></td>
                        <td></td>
                        @elseif($test->specimen->specimen_status_id == Specimen::ACCEPTED)
                        <td>{{trans('messages.specimen-accepted')}}</td>
                        <td>{{$test->specimen->acceptedBy->name}}</td>
                        <td>{{$test->specimen->time_accepted}}</td>
                        @elseif($test->specimen->specimen_status_id == Specimen::REJECTED)
                        <td>{{trans('messages.specimen-rejected')}}</td>
                        <td>{{$test->specimen->rejectedBy->name}}</td>
                        <td>{{$test->specimen->time_rejected}}</td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">{{trans("messages.no-records-found")}}</td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
            @endif
        </div>

        @endif
    </div>
</div>

<div class="modal fade" id="result-transmission">
      <div class="modal-dialog">
        <div class="modal-content">
        {{ Form::open(array('route' => 'transmit.result'),['id' => 'result-transmission-form']) }}
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('messages.close')}}</span>
            </button>
            <h4 class="modal-title">
                <span class="glyphicon glyphicon-transfer"></span>
                Result transmission form</h4>
          </div>
          <div class="modal-body" >
            
            <div class="form-group">
                    {{ Form::hidden('testids', implode(';',$tests->lists('id')),['id' => 'testids']) }}
                    {{ Form::label('transmittedto', 'Transmitted to') }}
                    {{ Form::text('transmittedto', Input::old('transmittedto'), ['placeholder' => "Transmited to",'class' => 'form-control' ,'id' => 'transmittedto']) }}
            </div>
            <div class="form-group">

                    {{ Form::label('designation', 'Designation') }}
                    {{ Form::select('designation', ['' => '--- Select designation ---','Medical student' => 'Medical student','Nurse' => 'Nurse', 'Patient self'=>'Patient self', 'Patient relative'=>'Patient relative','Redident Dr'=>'Resident Dr', 'Support staff'=>'Support staff', 'Treating Dr'=>'Treating Dr'], null,
                                     array('class' => 'form-control','id' => 'designation')) }}
            </div>
          </div>
          <div class="modal-footer">
            {{ Form::button("<span class='glyphicon glyphicon-transfer'></span> ".'Transmit',
                array('class' => 'btn btn-primary ', 'data-dismiss' => 'modal', 'id' => 'submitbutton','disabled'=>'disabled', 'type' => 'submit')) }}
            <button type="button" class="btn btn-default" data-dismiss="modal">
                {{trans('messages.close')}}</button>
          </div>
        {{ Form::close() }}
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
</div>

</div>
@stop