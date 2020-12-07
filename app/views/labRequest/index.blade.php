@extends("layout")
@section("content")
    <div>
        <ol class="breadcrumb">
          <li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
          <li class="active">Requests</li>
        </ol>
    </div>
    @if (Session::has('message'))
        @if(isset(Session::get('message')->danger))
            <div class="alert alert-danger">{{ trans(Session::get('message')->danger) }}</div>
        @elseif(isset(Session::get('message')->info))
            <div class="alert alert-info">{{ trans(Session::get('message')->info) }}</div>
        @else
								<div class="alert alert-success">{{ trans(Session::get('message')) }}</div>
								@endif
    @endif

    <div class='container-fluid'>
        {{ Form::open(array('route' => array('labRequest.index'))) }}
            <div class='row'>
                <div class='col-md-3'>
                        {{ Form::label('search', trans('messages.search'), array('class' => 'sr-only')) }}
                        {{ Form::text('search', Input::get('search'),
                            array('class' => 'form-control', 'placeholder' => 'Search')) }}
                </div>

                <div class='col-md-4'>
                    <div class='col-md-3'>
                        {{ Form::label('date_from', trans('messages.from')) }}
                    </div>
                    <div class='col-md-9'>
                        {{ Form::text('date_from', Input::get('date_from'), 
                            array('class' => 'form-control standard-datepicker')) }}
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class='col-md-3'>
                        {{ Form::label('date_to', trans('messages.to')) }}
                    </div>
                    <div class='col-md-9'>
                        {{ Form::text('date_to', Input::get('date_to'), 
                            array('class' => 'form-control standard-datepicker')) }}
                    </div>
                </div>
                <div class='col-md-1'>
                        {{ Form::submit(trans('messages.search'), array('class'=>'btn btn-primary')) }}
                </div>
            </div>
        {{ Form::close() }}
    </div>

    <br>

    <div class="panel panel-primary tests-log">
        <div class="panel-heading ">
            <div class="container-fluid">
                <div class="row less-gutter">
                    <div class="col-md-11">
                        <span class="glyphicon glyphicon-filter"></span>Lab requests list
                        @if(Auth::user()->can('request_test'))
                        <div class="panel-btn">
                            <a class="btn btn-sm btn-info" href="javascript:void(0)"
                                data-toggle="modal" data-target="#new-test-modal">
                                <span class="glyphicon glyphicon-plus-sign"></span>
                                {{trans('messages.new-test')}}
                            </a>
                        </div>
                        @endif
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
            <table class="table search-table table-striped table-hover table-condensed">
                <thead>
                    <tr>
                    	<th>Request No.</th>
                        <th>{{trans('messages.date-ordered')}}</th>
                        
                        
                        <th class="col-md-2">{{trans('messages.patient-name')}}</th>
                       
                        <th class="col-md-3">{{ Lang::choice('messages.test',2) }}</th>
                        <th>{{trans('messages.visit-type')}}</th>
                        <th>Request status</th>
                        <th class="col-md-3">Action on request</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($testSet as $key => $test)
                    <tr class="
                        @if(Session::has('activeRequest'))
                            {{ in_array($test->visit_id, Session::get('activeRequest'))?'info':''}}
                        @endif
                        {{$test->visit->visit_urgency=='urgent'?'danger':''}}

                        "
                        >
                        <td>{{ empty($test->visit->visit_number)?
                                $test->visit_id:
                                $test->visit->visit_number
                            }}</td> <!--Visit Number -->
                        <td>{{ date('d-m-Y H:i', strtotime($test->time_created));}}</td>  <!--Date Ordered-->

                        
                        <td>{{ $test->visit->patient->name.' ('.($test->visit->patient->getGender(true)).',
                            '.$test->visit->patient->getAge('Y'). ')'}}</td> <!--Patient Name -->
                   
                        <td><?php $testTypes=explode(", ",$test->testTpe); ?> @foreach (TestType::wherein('id', $testTypes)->get() as $testType) {{ $testType->name }}; @endforeach</td> <!--Test-->
                        <td>{{ $test->visit->visit_type }}</br>{{ $test->visit->department }}</td> <!--Visit Type -->
                        <td id="test-status-{{$test->id}}" class='test-status'>
                            <!-- Test Statuses -->
                            <div class="container-fluid">
                            
                                <div class="row">

                                    <div class="col-md-12">
                                        @if(is_null($test->visit->visit_amount))
                                            
                                            <span class='label label-info'>
                                                Request not yet recieved </span>
                                        @else																				<span class='label label-success'>
                                                Request recieved </span>
                                        @endif
                                    </div>
    
                                </div>
                                @if($test->visit->areCompletedTestsVerified())
                                <div class="row">

                                    <div class="col-md-12">
                                        <span class='label label-info'>
                                                Completed tests verified </span>
                                    </div>                            
                                
                                </div>
                                @else
                                <div class="row">

                                    <div class="col-md-12">
                                        <span class='label label-warning'>
                                                Some tests not verified </span>
                                    </div>                            
                                
                                </div>
                                @endif
                            </div>
                        </td>
                        <!-- ACTION BUTTONS -->
                        <td>
                            @if(Entrust::can('recieve_request'))
                            <a class="btn btn-sm btn-success"
                                href="{{ URL::route('labRequest.viewDetails', $test->visit_id) }}"
                                id="view-details-{{$test->visit_id}}-link" 
                                title="{{trans('messages.view-details-title')}}">
                                {{trans('Recieve resquest')}}

                            </a>
                            <a class="btn btn-sm btn-primary"
                                data-toggle="modal"
								href="#result-transmission-register"
                                data-url="{{ URL::route('result.transmission.register', $test->visit_id) }}" 
                                title="View transmission details">
                                <span class="glyphicon glyphicon-transfer"></span>
                                Transmitted
                                <span class="badge">{{$test->visit->numPrintedTests().' of '.$test->visit->numOfMeasures()}}</span>
                            </a>
                                @if(is_null($test->visit->visit_amount) )
                                <a class="btn btn-sm btn-info" 
                                    href="{{ URL::to("test/update/" . $test->visit_id ) }}" >
                                <span class="glyphicon glyphicon-edit"></span>
                                {{trans('messages.edit')}}
                                </a>
                                <button class="btn btn-sm btn-danger delete-item-link"
                                data-toggle="modal" data-target=".confirm-delete-modal" 
                                data-id='{{ URL::to("/test/delete/". $test->visit_id) }}'>
                                <span class="glyphicon glyphicon-trash"></span>
                                {{trans('messages.delete')}}
                                </button>
                                @endif
                            @endif
                            @if(Entrust::can('view_result'))
                            <a class="btn btn-sm btn-default" href="{{ URL::to('patientreport/'.$test->visit->patient->id.'/'.$test->visit->id) }}">
                                <span class="glyphicon glyphicon-eye-open"></span>
                                {{trans('View report')}}
                                <span class="badge">{{$test->visit->numResMeasures().' of '.$test->visit->numOfMeasures()}}</span>
                            </a>
                           
                            @endif

                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            
           {{ $testSet->links() }}
        {{ Session::put('SOURCE_URL', URL::full()) }}
        {{ Session::put('TESTS_FILTER_INPUT', Input::except('_token')); }}
        
        </div>
    </div>

    <!-- MODALS -->
    <div class="modal fade" id="new-test-modal">
      <div class="modal-dialog">
        <div class="modal-content">
        {{ Form::open(array('route' => 'test.create')) }}
          <input type="hidden" id="patient_id" name="patient_id" value="0" />
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('messages.close')}}</span>
            </button>
            <h4 class="modal-title">{{trans('messages.create-new-test')}}</h4>
          </div>
          <div class="modal-body">
            <h4>{{ trans('messages.first-select-patient') }}</h4>
            <div class="row">
              <div class="col-lg-12">
                <div class="input-group">
                  <input type="text" class="form-control search-text" 
                    placeholder="{{ trans('messages.search-patient-placeholder') }}">
                  <span class="input-group-btn">
                    <button class="btn btn-default search-patient" type="button">
                        {{ trans('messages.patient-search-button') }}</button>
                  </span>
                </div><!-- /input-group -->
                <div class="patient-search-result form-group">
                    <table class="table table-condensed table-striped table-bordered table-hover hide">
                      <thead>
                        <th> </th>
                        <th>{{ trans('messages.patient-id') }}</th>
                        <th>{{ Lang::choice('messages.name',2) }}</th>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                </div>
              </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                {{trans('messages.close')}}</button>
            <button type="button" class="btn btn-primary next" onclick="submit();" disabled>
                {{trans('messages.next')}}</button>
          </div>
        {{ Form::close() }}
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
	
<div class="modal fade" id="result-transmission-register">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">{{trans('messages.close')}}</span>
            </button>
            <h4 class="modal-title">
                <span class="glyphicon glyphicon-transfer"></span>
                Result transmission registrer</h4>
          </div>
          <div class="modal-body">
          </div>
          <div class="modal-footer">
           
            <button type="button" class="btn btn-default" data-dismiss="modal">
                {{trans('messages.close')}}</button>
          </div>
       
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
</div><!-- /.modal /#change-specimen-modal-->
   

    
@stop