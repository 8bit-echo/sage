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
        $title = '';

        if (WP_ENV === 'development') {
            $title .= 'LOCAL: ';
        }
        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                $title .= get_the_title($home);
            }
            $title .= __('Latest Posts', 'sage');
        }
        if (is_archive()) {
            $title .= get_the_archive_title();
        }
        if (is_search()) {
            $title .= sprintf(__('Search Results for %s', 'sage'), get_search_query());
        }
        if (is_404()) {
            $title .= __('Not Found', 'sage');
        }
        $title .= get_the_title();

        return $title;
    }

    public static function option($key)
    {
        $theme = new AppOption();
        $options = $theme::getInstance();
        return array_key_exists($key, $options) ? $options[$key] : false;
    }
}
