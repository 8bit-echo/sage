<?php
// put ONLY the functions for THIS site in this file. 
use function App\asset_path;
// put ONLY the functions for THIS site in this file. 

/**
 * Add custom color swatches to TinyMCE editor.
 */ 
function sage_cutom_wysiwyg_colors($init) {

  $custom_colours = '
      "004e4d", "Green",
  ';

  // build colour grid default+custom colors
  $init['textcolor_map'] = '['.$custom_colours.']';

  // change the number of rows in the grid if the number of colors changes
  // 8 swatches per row
  $init['textcolor_rows'] = 1;

  return $init;
}
add_filter('tiny_mce_before_init', 'sage_cutom_wysiwyg_colors');


/**
 * define custom blocks in ACF
 */ 
function register_acf_gutenberg_blocks() {
  // if (function_exists('acf_register_block')) {
  //   acf_register_block([
  //     'name'              => '',
  //     'title'             => '',
  //     'description'       => '',
  //     'render_callback'   => '',
  //     'icon'              => '',
  //     'keywords'          => [],
  //     'supports'          => [
  //       'align' => ['wide'], 
  //     ], 
  //   ]);
  // }
}
add_action('acf/init', 'register_acf_gutenberg_blocks');