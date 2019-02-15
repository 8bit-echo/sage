@extends('layouts.app')

@section('content')
  <p class="remove">See front-page.blade.php</p>
  @while(have_posts()) @php the_post() @endphp
    @include('partials.page-header')
    @include('partials.content-page')
  @endwhile
@endsection