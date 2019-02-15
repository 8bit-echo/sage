@extends('layouts.app')

@section('content')
@include('partials.page-header')
  <ul class="archives-list">
  @while(have_posts()) @php the_post() @endphp
    <li class="archive-post">
      @include('partials.content')
    </li>
  @endwhile
  </ul>

  <nav class="pagination">
    {{ the_posts_pagination() }}
  </nav>
@endsection

