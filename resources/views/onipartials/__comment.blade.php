<?php
    $user = App\User::find(Auth::user()->id);
    $comments_public = null;
    $comments_private = null;
?>
@if(!isset($scope_view) || (isset($scope_view) && $scope_view == App\Comment::TYPE_INTERNAL))
<ul class="nav nav-tabs navs-2">
    <li class="active">
        <a data-toggle="tab" href="#public">KH - NV</a>
    </li>
    <li>
        <a data-toggle="tab" href="#private">NV - NV</a>
    </li>
</ul>
@endif

<div class="tab-content">
    @if(!isset($scope_view) || (isset($scope_view) && $scope_view == App\Comment::TYPE_EXTERNAL))
        <div id="public" class="tab-pane active">
            <?php
            $comments_public = App\Comment::where([
                'object_id' => $object_id,
                'object_type' => $object_type,
                'scope' => App\Comment::TYPE_EXTERNAL
            ])
                ->orderBy('created_at', 'desc')
                ->get();
            ?>
            <div class="sidebar-title">
                <form class="___form" onsubmit="return false;">
                    <input type="hidden" name="action" value="comment">
                    <input type="hidden" name="method" value="post">
                    <input type="hidden" name="url" value="{{ url('comment')  }}">
                    <input type="hidden" name="object_id" value="{{$object_id}}">
                    <input type="hidden" name="object_type" value="{{$object_type}}">
                    <input type="hidden" name="scope_view" value="{{@$scope_view}}">
                    <input type="hidden" name="scope" value="{{ App\Comment::TYPE_EXTERNAL  }}">
                    <input type="hidden" name="is_public_profile" value="1">
                    <input type="hidden" name="anchor" value="#anchor-box-comment">
                    <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                    <input type="hidden" name="response" value="onipartials/__comment">

                    <input
                            name="message"
                            type="text"
                            class="form-control ___input-action"
                            data-key-global="box-chat-external"
                            placeholder="Trao đổi thông tin...">
                </form>
                <small><i class="fa fa-tim"></i> Nhập và ENTER để gửi.</small>
            </div>
            <div class="ibox-content">
                <div class="feed-activity-list">
                @if($comments_public)
                    @foreach($comments_public as $comment_public)
                        <?php
                        if(in_array($comment_public->type_context, [App\Comment::TYPE_CONTEXT_ACTIVITY]))
                            $cmt_class = "cmt_staff";
                        else
                            $cmt_class = "";
                        ?>
                        <div class="feed-element {{$cmt_class}}">
                            @if($comment_public->type_context != App\Comment::TYPE_CONTEXT_LOG)
                                <div class="pull-left">
                                    <img alt="image" class="img-circle" src="{{ asset('images/default-avatar.png') }}">
                                </div>
                            @endif
                            <div class="media-body ">
                                @if($comment_public->type_context != App\Comment::TYPE_CONTEXT_LOG)
                                    <?php
                                    $user_comment = App\User::find($comment_public->user_id);
                                    ?>
                                    <strong>{{$user_comment->name}}</strong>
                                @endif
                                {{$comment_public->message}} <br>
                                <small class="text-muted timeago" datetime="{{$comment_public->created_at}}" title="{{$comment_public->created_at}}">{{ App\Util::formatDate($comment_public->created_at)  }}</small>

                            </div>
                        </div>
                    @endforeach
                @endif
                </div>
            </div>
        </div>
    @endif
    @if(!isset($scope_view) || (isset($scope_view) && $scope_view == App\Comment::TYPE_INTERNAL))
        <div id="private" class="tab-pane">
            <?php
            $comments_private = App\Comment::where([
                'object_id' => $object_id,
                'object_type' => $object_type,
                'scope' => App\Comment::TYPE_INTERNAL
            ])
                ->orderBy('created_at', 'desc')
                ->get();
            ?>
            <div class="sidebar-title">
                <form class="___form" onsubmit="return false;">
                    <input type="hidden" name="action" value="comment">
                    <input type="hidden" name="method" value="post">
                    <input type="hidden" name="url" value="{{ url('comment')  }}">
                    <input type="hidden" name="object_id" value="{{$object_id}}">
                    <input type="hidden" name="object_type" value="{{$object_type}}">
                    <input type="hidden" name="scope_view" value="{{@$scope_view}}">
                    <input type="hidden" name="scope" value="{{ App\Comment::TYPE_INTERNAL  }}">
                    <input type="hidden" name="is_public_profile" value="1">
                    <input type="hidden" name="anchor" value="#anchor-box-comment">
                    <input type="hidden" name="_token" value="{{ csrf_token()  }}">
                    <input type="hidden" name="response" value="onipartials/__comment">
                    <input
                            name="message"
                            type="text"
                            data-key-global="box-chat-internal"
                            class="form-control  ___input-action"
                            placeholder="Chat nội bộ...">
                </form>
                <small><i class="fa fa-tim"></i> Nhập để gửi trao đổi.</small>
            </div>
            <div class="ibox-content">
                <div class="feed-activity-list">
                    @if($comments_private)
                        @foreach($comments_private as $comment_private)
                            <?php
                            if(in_array($comment_private->type_context, [App\Comment::TYPE_CONTEXT_ACTIVITY, App\Comment::TYPE_CONTEXT_LOG]))
                                $cmt_class = "cmt_staff";
                            else
                                $cmt_class = "";
                            ?>
                            <div class="feed-element {{$cmt_class}}">
                                @if($comment_private->type_context != App\Comment::TYPE_CONTEXT_LOG)
                                    <div class="pull-left">
                                        <img alt="image" class="img-circle" src="{{ asset('images/default-avatar.png') }}">
                                    </div>
                                @endif
                                <div class="media-body ">
                                    @if($comment_private->type_context != App\Comment::TYPE_CONTEXT_LOG)
                                        <?php
                                        $user_comment = App\User::find($comment_private->user_id);
                                        ?>
                                        <strong>{{$user_comment->name}}</strong>
                                    @endif
                                    {{$comment_private->message}} <br>
                                    <small class="text-muted">{{ App\Util::formatDate($comment_private->created_at)  }}</small>

                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>