<article @php post_class() @endphp>
  <header>
    <h2 class="entry-title">
      <a href="{{ get_permalink() }}">{{ the_post_thumbnail('thumbnail') }}<br />{{ get_the_title() }}</a>
    </h2>
    @include('partials/entry-meta')
  </header>

  <div class="entry-summary">
    @php the_excerpt() @endphp
  </div>
</article>
