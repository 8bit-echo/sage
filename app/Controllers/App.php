<?php

namespace App\Controllers;

use Sober\Controller\Controller;
use \App\Classes\AppOption;

class App extends Controller
{
    public function siteName()
    {
        return get_bloginfo('name');
    }

    public static function title()
    {
        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }
            return __('Latest Posts', 'sage');
        }
        if (is_archive()) {
            return get_the_archive_title();
        }
        if (is_search()) {
            return sprintf(__('Search Results for %s', 'sage'), get_search_query());
        }
        if (is_404()) {
            return __('Not Found', 'sage');
        }
        return get_the_title();
    }

    public static function option($key)
    {
        $theme = new AppOption();
        $options = $theme::getInstance();
        return array_key_exists($key, $options) ? $options[$key] : false;
    }

    public static function copyrightYears() {
        // est. 2019
        $year_string = '2019';
        $year_string .= date('Y') > 2019 ? '-' . date('Y') : null;
    
        return $year_string;
      }
    
      public static function paginationArgs() {
        return [
          'mid_size' => 3,
          'prev_text' => '&laquo;',
          'next_text' => '&raquo;'
        ];
      }
}
