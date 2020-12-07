@extends("layout")
@section("content")
<div>
    <ol class="breadcrumb">
        <li><a href="{{{URL::route('user.home')}}}">{{trans('messages.home')}}</a></li>
        <li><a href="{{ URL::route('labRequest.index') }}">{{ Lang::choice('messages.test',2) }}</a></li>
        <li class="active">{{trans('messages.test-details')}}</li>
    </ol>
</div>
@if (Session::has('message'))
<div class="alert alert-info">{{ trans(Session::get('message')) }}</div>
@endif
@if($errors->all())
			<div class="alert alert-danger">
				{{ HTML::ul($errors->all()) }}
			</div>
		@endif
<div class="row">
    <div class="col-sm-2 col-sm-offset-10 text-center">
        {{ Form::button("<span class='glyphicon glyphicon-download-alt'></span> PDF",
                    array('class' => 'btn btn-success', 'id' => 'pdf')) }}
    </div>
</div>
<br />
<div class="panel panel-primary" id="patientReport" data-report="{{str_replace(" ", "_", $visit->patient->name).'_' . $requestID . '_'.date("Ymdhi") }}">
    <div class="panel-heading ">
        <div class="container-fluid">
            <div class="row less-gutter">
                 <div class="col-md-11">
                    <span class="glyphicon  glyphicon-user"></span>Lab Request   
                     @if(is_null($visit->visit_amount) )
                    <div class="panel-btn">
                        <a class="btn btn-sm btn-info" 
                            href="{{ URL::to("test/update/" . $visit->id ) }}" >
                        <span class="glyphicon glyphicon-edit"></span>
                        {{trans('messages.edit')}}
                        </a>
                        <button class="btn btn-sm btn-danger delete-item-link"
                        data-toggle="modal" data-target=".confirm-delete-modal" 
                        data-id='{{ URL::to("/test/delete/". $visit->id) }}'>
                        <span class="glyphicon glyphicon-trash"></span>
                        {{trans('messages.delete')}}
                        </button> 
                    </div> 
                    @endif         
                
                </div>
                
            </div>
        </div>
    </div> <!-- ./ panel-heading -->
    <div class="panel-body">
        <div class="container-fluid">
            <table class=" table borderless ">
                
                <tr class="row">
                    <td class="col-md-6">
                        <div class="display-details ">
                        <h2 class="view">                                                                  <span class="col-md-6"><strong>Request ID</strong></span>
                            <span class="col-md-6">{{  $requestID }}</span>
                        </h2>
                        <h3 class="view">                                                                  <span class="col-md-6"><strong>Tests</strong></span>
                           <span class="col-md-6">@if(Entrust::can('recieve_request') && is_null($visit->visit_amount) )<strong>Check payment</strong>  @else <strong>Tests status</strong> @endif</span>
                        </h3>
                        {{ Form::open(array('route'=>'labRequest.receive'))}}
                        @foreach($requestedTests as $requestedTest )
                        <p class="view">                                                                
                        <span class="col-md-6"><strong>{{$requestedTest->testType->name }}</strong></span>
                        <span class="col-md-6">                                                             @if(!is_null($visit->visit_amount) && is_null($requestedTest->paid_amount))
                            <span class="col-md-12">Not sent for testing because was not paid   </span>
                            @elseif(!is_null($visit->visit_amount) && !is_null($requestedTest->paid_amount))
                            <span class="col-md-12">Sent for testing  </span>
                            @else
                             @if(Entrust::can('recieve_request'))
                                <span class="col-md-6"><label>{{ Form::radio($requestedTest->id, '1', Input::old($requestedTest->id)) }} Yes</label></span>
                                <span class="col-md-6"><label>{{ Form::radio($requestedTest->id, '0', Input::old($requestedTest->id)) }} No</label></span>  
                                {{ Form::hidden('request_ID', $requestID) }}
                                                                                                                        
                             @else
                                <span class="col-md-12">Test is still awaited to show payment proof </span>
                             @endif
                            @endif  
                        </span>                                 
                        </p>
                        @endforeach
                                                                                                
                        </br></br>
                        @if(Entrust::can('recieve_request') && is_null($visit->visit_amount) )
                        <label  class="col-md-6"> Select the type of insurance</label>
                        <span class="col-md-6">
                            <select class="form-control" name="insurance">
                                <option value=''>...Select insurance...</option>
                                <option value='tarif_A'>Community Health Insurance</option>
                                <option value='tarif_C'>RSSB/RAMA</option>      
                                <option value='tarif_B'>MMI/MS_UR/ Other institutes</option>
                                <option value='tarif_D'>Commircial and private companies</option>
                                <option value='tarif_E'>Private</option>        
                            </select>
                        </span>
                        </br></br></br></br>
                        <div class="form-group actions-row">
                        {{ Form::button('<span class="glyphicon glyphicon-save"></span> '.trans('messages.save'),['class' => 'btn btn-primary', 'onclick' => 'submit()']) }}
                        {{ Form::button(trans('messages.cancel'),['class' => 'btn btn-default', 'onclick' => 'javascript:history.go(-1)']) }}
                        </div>
                        @endif
                        {{Form::close()}}
                        </div>
                    </td>
                    <td class="col-md-6">
                                            <div class="panel panel-info">  <!-- Patient Details -->
                        <div class="panel-heading">
                            <h3 class="panel-title">{{trans("messages.patient-details")}}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>{{trans("messages.patient-number")}}</strong></p></div>
                                    <div class="col-md-9">
                                        {{$visit->patient->patient_number}}</div></div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>{{ Lang::choice('messages.name',1) }}</strong></p></div>
                                    <div class="col-md-9">
                                        {{$visit->patient->name}}</div></div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>{{trans("messages.age")}}</strong></p></div>
                                    <div class="col-md-9">
                                        {{$visit->patient->getAge()}}</div></div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>{{trans("messages.gender")}}</strong></p></div>
                                    <div class="col-md-9">
                                        {{$visit->patient->gender==0?trans("messages.male"):trans("messages.female")}}
                                    </div></div>                                                    
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Request by</strong></p></div>
                                    <div class="col-md-9">
                                        {{$requestedBY}}
                                    </div></div>                                                        
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Request on</strong></p></div>
                                    <div class="col-md-9">
                                        {{$requestedOn}}
                                    </div></div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Department</strong></p></div>
                                    <div class="col-md-9">
                                        {{$department}}
                                    </div></div>                                                        
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Visit Type</strong></p></div>
                                    <div class="col-md-9">
                                        {{$visitType}}
                                    </div></div>                                                                                                                            
                                @if($ward)                                                                                                                                      
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Ward</strong></p></div>
                                    <div class="col-md-9">
                                        {{$ward}}
                                    </div></div>                                                                                                                        
                                @endif

                                @if($room)
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Room</strong></p></div>
                                    <div class="col-md-9">
                                        {{$room}}
                                    </div></div>
                                @endif
                                @if($recievedBy)
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Ricieved by</strong></p></div>
                                    <div class="col-md-9">
                                        {{$recievedBy->name}}
                                        {{ $recievedBy->phone ? "<span class='label label-success'>" . $recievedBy->phone."</span> " : "<span class='label label-success'>0788575656</span> " }}
                                    </div></div>
                                @endif
                                @if($visitAmount)
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><strong>Paid amount</strong></p></div>
                                    <div class="col-md-9">
                                        {{$visitAmount}}
                                    </div></div>
                                 @endif
                            </div>
                        </div> <!-- ./ panel-body -->
                    </div>
                    </td>
                </tr>
                
            </table>
            
        </div> <!-- ./ container-fluid -->

    </div> <!-- ./ panel-body -->
</div> <!-- ./ panel -->
@stop