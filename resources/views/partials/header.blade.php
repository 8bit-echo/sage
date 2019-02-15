@php
    use \App\Classes\MegaNav;
@endphp
<header class="banner">
  <div class="container">
    {{the_custom_logo()}}
    
    <nav class="nav-primary">
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class'     => 'nav',
          'walker'         => new MegaNav(),
        ]) !!}
      @endif
      
    </nav>
  </div>
</header>
