@extends('layouts.app')

@section('title'){{ trans('date.gig_listAttendances_title') }}@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>{{ trans('date.gig_listAttendances_title') }}</h1>

            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-2d">
                        <div class="panel-heading">
                            &nbsp;
                        </div>

                        <div id="attendance-list" class="table-responsive">
                            <table class="table table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10em; min-width: 10em;"></th>

                                        @foreach($gigs as $gig)
                                            <th style="width: 8em; min-width: 8em;">
                                                {{ $gig->title }}
                                                <br>{{ $gig->start }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php //TODO: This should probably go into GigAttendanceController somehow...

                                        foreach($gigs as $gig){
                                            $gigattendances[$gig->id] = $gig->gig_attendances()->get();
                                        }
                                    ?>
                                    @foreach($voices as $voice)
                                        @foreach($voice->children as $sub_voice)
                                            <tr class="subvoice">
                                                <td>
                                                    {{$sub_voice->name}}
                                                    <?php 
                                                        //TODO: This should probably go into GigAttendanceController somehow...
                                                        $users = $sub_voice->users()->currentAndFuture()->get();
                                                        $userIDs = $users->keyBy('id')->keys()->all();
                                                    ?>
                                                    <span class="pull-right">
                                                        <div class="btn btn-2d btn-toggle super-voice-{{ $voice->name }}" data-voice="{{ str_replace(' ', '-', $sub_voice->name) }}" data-status="hidden">
                                                            <i class="fa fa-caret-right"></i>
                                                        </div>
                                                    </span>
                                                </td>
                                                @foreach($gigs as $gig)
                                                    <td>
                                                    <?php 
                                                        //TODO: This should probably go into GigAttendanceController somehow...
                                                        $voiceAttendances = $gigattendances[$gig->id];
                                                        $voiceAttendances = \App\Models\Event::filterAttendancesByUserIDs($voiceAttendances, $userIDs);
                                                        $voiceAttendances = \App\Models\Event::getAttendanceCountNew($voiceAttendances);
                                                        
                                                    ?>
                                                        <span class ="positive overviewnumber">
                                                            {{ $voiceAttendances[\Config::get('enums.attendances')['yes']] }}
                                                            <i class="fa fa-check"></i>
                                                        </span>&nbsp;
                                                        <span class ="maybe overviewnumber">
                                                            {{ $voiceAttendances[\Config::get('enums.attendances')['maybe']] }}
                                                            <i class="fa fa-question"></i>&nbsp;
                                                        </span>
                                                    </td>
                                                @endforeach
                                                
                                            </tr>
                                            @foreach($users as $user)
                                                <tr class="user voice-{{ $voice->name }} voice-{{ str_replace(' ', '-', $sub_voice->name) }}">
                                                    <td>{{ $user->abbreviated_name }}</td>
                                                    @foreach($gigs as $gig)
                                                        <?php switch($gig->isAttending($user)){
                                                            case "yes":
                                                                $tdclass = "attending";
                                                                $iconclass = "fa-check";
                                                                break;
                                                            case "no":
                                                                $tdclass = "not-attending";
                                                                $iconclass = "fa-times";
                                                                break;
                                                            case "maybe":
                                                                $tdclass = "maybe-attending";
                                                                $iconclass = "fa-question";
                                                                break;
                                                            default:
                                                                $tdclass = "unanswered";
                                                                $iconclass = "fa-minus";
                                                                break;
                                                        }?>
                                                        <td class="{{$tdclass}}">
                                                            <i class="fa {{$iconclass}}"></i>
                                                            @if($gig->hasCommented($user))
                                                                <?php $comment = $gig->getComment($user);?>
                                                                &nbsp;
                                                                <i class="far fa-comment comment-toggle" title="{{$comment}}"></i>
                                                                <div class="full-comment" style="display: none"> {{$comment}} </div>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(!is_array($gigs))
                            {{ $gigs->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".btn-toggle").click(function () {
                var voice = $(this).data('voice');

                //TODO: Make this more beautiful.
                if ($(this).data("status") === "hidden") {
                    $(".voice-" + voice).show();
                    $(this).data("status", "display").find("i").removeClass("fa-caret-right").addClass("fa-caret-down");
                    $(".super-voice-" + voice).data("status", "display").find("i").removeClass("fa-caret-right").addClass("fa-caret-down");
                } else {
                    $(".voice-" + voice).hide();
                    $(this).data("status", "hidden").find("i").removeClass("fa-caret-down").addClass("fa-caret-right");
                    $(".super-voice-" + voice).data("status", "hidden").find("i").removeClass("fa-caret-down").addClass("fa-caret-right");
                }
            });
            $(".comment-toggle").click(function(){
                $(this).parent().parent().find(".full-comment").toggle();
            });
            $(".full-comment").click(function(){
                $(this).parent().parent().find(".full-comment").toggle();
            })
        });
    </script>
@endsection