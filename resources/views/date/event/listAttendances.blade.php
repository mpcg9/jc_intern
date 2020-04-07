@extends('layouts.app')

@section('title'){{ $title }}@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <h1>{{ $title }}</h1>

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

                                        @foreach($events as $event)
                                            <th style="width: 8em; min-width: 8em;">
                                                {{ $event->title }}
                                                <br>{{ $event->start }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($voices as $voice)
                                        @foreach($voice->children as $sub_voice)
                                            <tr class="subvoice">
                                                <td>
                                                    {{$sub_voice->name}}
                                                    <span class="pull-right">
                                                        <div class="btn btn-2d btn-toggle super-voice-{{ $voice->name }}" data-voice="{{ str_replace(' ', '-', $sub_voice->name) }}" data-status="hidden">
                                                            <i class="fa fa-caret-right"></i>
                                                        </div>
                                                    </span>
                                                </td>
                                                @foreach($events as $event)
                                                    <td>
                                                        <span class ="positive overviewnumber">
                                                            {{ $attendanceCounts[$event->id][$sub_voice->id][\Config::get('enums.attendances')['yes']] }}
                                                            <i class="fa fa-check"></i>
                                                        </span>&nbsp;
                                                        <?php // @if(null === $event->binary_answer) 
                                                              // binary_answer and $event->hasBinaryAnswer() are not working :(  ?>
                                                        @if( $attendanceCounts[$event->id][$sub_voice->id][\Config::get('enums.attendances')['maybe']] > 0)
                                                        <span class ="maybe overviewnumber">
                                                            {{ $attendanceCounts[$event->id][$sub_voice->id][\Config::get('enums.attendances')['maybe']] }}
                                                            <i class="fa fa-question"></i>&nbsp;
                                                        </span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                
                                            </tr>
                                            @foreach($users[$sub_voice->id] as $user)
                                                <tr class="user voice-{{ $voice->name }} voice-{{ str_replace(' ', '-', $sub_voice->name) }}">
                                                    <td>{{ $user->abbreviated_name }}</td>
                                                    @foreach($events as $event)
                                                        <?php switch($event->isAttending($user)){
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
                                                            @if($event->hasCommented($user))
                                                                <?php $comment = $event->getComment($user);?>
                                                                &nbsp;
                                                                <a class="btn btn-2d btn-toggle comment-toggle">
                                                                    <i class="far fa-comment" title="{{$comment}}"></i>
                                                                </a>
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
                        @if(!is_array($events))
                            {{ $events->links() }}
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
            // open and close subvoices
            $(".subvoice").click(function (){
                var togglebutton = $(this).find(".btn-toggle");
                var voice = togglebutton.data('voice');

                $(this).nextAll(".voice-"+voice).toggle();
                if("hidden" === togglebutton.data("status")){
                    togglebutton.data("status","display").children("i").removeClass("fa-caret-right").addClass("fa-caret-down");
                }
                else{
                    togglebutton.data("status", "hidden").children("i").removeClass("fa-caret-down").addClass("fa-caret-right");
                }
            });
            // open and close comments for one user
            $("td").has(".comment-toggle").click(function(){
                $(this).siblings().addBack().find(".full-comment").toggle();
            });
        });
    </script>
@endsection