@section ("header")
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand">{{ Config::get('kblis.name') }} {{ Config::get('kblis.version') }}</a>
            </div>
            <div class="grid-2  user-profile">
                @if (Auth::check())
                    <ul class="nav navbar-nav navbar-right">
                        <li class="user-link">
                            <a href="javascript:void(0);">
                                <strong>{{Auth::user()->name }}</strong>
                            </a>
                        </li>
                    </ul>
                    <div class="user-settings">
                        <div>
                            <span class="glyphicon glyphicon-edit"></span>
                            <a href='{{ URL::to("user/".Auth::user()->id."/edit") }}'>{{trans('messages.edit-profile')}}</a>
                        </div>
                        <div>
                            <span class="glyphicon glyphicon-log-out"></span>
                            <a href="{{ URL::route("user.logout") }}">{{trans('messages.logout')}}</a>
                        </div>
                    </div>
                @endif
                <?php
                    $labSectionIp=Request::ip();
                ?>
                @if(Request::path()=='test')
                <ul class="nav navbar-nav navbar-right" id='testStatusNotification' >
                    <li class="">                        
                        <h4><span class=" label label-danger">Above half TAT
                            <span class="badge halfTaT" >{{ Ip::TestCountByStatus($labSectionIp,null)}}</span>
                        </span></h4>
                    </li>
                    <li class="">                        
                        <h4><span class=" label label-default">To recieve
                            <span class="badge tocollect" >{{ Ip::TestCountByStatus($labSectionIp,1)}}</span>
                        </span></h4>
                    </li>
                    <li class="">                        
                        <h4><span class=" label label-info">To collect or start
                            <span class="badge tostart" >{{ Ip::TestCountByStatus($labSectionIp,2)}}</span>
                        </span></h4>
                    </li>
                    <li class="">                        
                        <h4><span class=" label label-warning">To complete
                            <span class="badge tocomplete" >{{ Ip::TestCountByStatus($labSectionIp,3)}}</span>
                        </span></h4>
                    </li>
                    <li class="">                        
                        <h4><span class=" label label-primary">To verify
                            <span class="badge toverify" >{{ Ip::TestCountByStatus($labSectionIp,4)}}</span>
                        </span></h4>
                    </li>
                    <li class="">                        
                        <h4><span class=" label label-success" >Verified
                            <span class="badge verified">{{ Ip::TestCountByStatus($labSectionIp,5)}}</span>
                        </span></h4>
                    </li>
                </ul>
                @endif
            </div>
        </div>
    </div>
@show