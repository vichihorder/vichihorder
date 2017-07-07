@extends('layouts/support')

@section('content')
    <div style="background: #fff;margin: 0 20px; width: 100%; display: inline-block; padding: 5px 20px;">
        <!-- Page Heading -->
        <h1 class="my-4">{{ $post->post_title  }}</h1>

        @if($can_edit_post)
            <a href="{{ url('post', $post->id)  }}">Sửa bài viết</a>
    @endif

    <!-- Project Four -->
        <div class="row">
            <div class="col-md-12">
                <p>
                    {{ $post->post_excerpt  }}
                </p>

                <br>
                <div class="post-content">
                    {!! $post->post_content !!}
                </div>

            </div>
        </div>
        <!-- /.row -->

        <hr>
    </div>

@endsection

@section('sidebar')
    <div class="row">
        <div class="col-md-12">
            <h3 class="my-4">Bài viết gần đây</h3>
            <ul style="padding-left: 18px;">
                @foreach($posts_newest as $posts_newest_item)
                <li>
                    <a href="{{ url('ho-tro', $posts_newest_item->id)  }}" title="{{$posts_newest_item->post_title}}">
                        {{$posts_newest_item->post_title}}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection