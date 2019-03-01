@php
    use \App\Classes\MegaNav;
@endphp
<header class="site-header">
  <div class="site-header__container">
    {{the_custom_logo()}}
    
    <nav class="site-navigation">
      @if (has_nav_menu('utility_navigation'))
            {!! wp_nav_menu([ 
              'theme_location' => 'utility_navigation', 
              'menu_class'     => 'utility-nav',
              'container'        => false,
            ])!!}
      @endif
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu([
          'theme_location' => 'primary_navigation',
          'menu_class'     => 'primary-nav',
          'container'        => false,
          'walker'         => new MegaNav(),
        ]) !!}
      @endif
    </nav>
  </div>
</header>
