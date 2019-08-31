<?php
use App\Classes\AppOption;
use App\Classes\API;

new AppOption();
new API();

/*========================*/
/*   Define Functions     */
/*========================*/
/** Remove non-essential items from the WP Admin bar  */
function clean_admin_bar()
{
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu('wp-logo');
    // $wp_admin_bar->remove_menu('customize');
  $wp_admin_bar->remove_menu('updates');
  $wp_admin_bar->remove_menu('comments');
  $wp_admin_bar->remove_menu('itsec_admin_bar_menu');
  $wp_admin_bar->remove_menu('wpseo-menu');
}

/** Global custom stylesheet for WP back-end. */
function get_sage_admin_styles()
{
  wp_register_style('sage-admin-styles', get_theme_file_uri() . '/resources/sage-admin.css');
  wp_enqueue_style('sage-admin-styles');
}

/** Hide pages for CPTUI and ACF if the user isn't privileged. */
function remove_menu_items_from_admin()
{
  remove_menu_page('cptui_main_menu');
  remove_menu_page('edit.php?post_type=acf-field-group');
}

/** Browser detection function for Last 3 Versions of IE */
function is_ie()
{
  return boolval(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/') !== false);
}

// Fix ACF Previews
function add_field_debug_preview($fields){
   $fields["debug_preview"] = "debug_preview";
   return $fields;
}

function add_input_debug_preview() {
   echo '<input type="hidden" name="debug_preview" value="debug_preview">';
}

/**
 * Change all image src from editor to be compatible with blazy lazy-loader
 */ 
function lazy_load_editor_images($content) {
	//-- Change src/srcset to data attributes.
	$content = preg_replace("/<img(.*?)(src=|srcset=)(.*?)>/i", '<img$1data-$2$3>', $content);

	//-- Add .lazy-load class to each image that already has a class.
	$content = preg_replace('/<img(.*?)class=\"(.*?)\"(.*?)>/i', '<img$1class="$2 lazy"$3>', $content);

	//-- Add .lazy-load class to each image that doesn't already have a class.
	$content = preg_replace('/<img((.(?!class=))*)\/?>/i', '<img class="lazy"$1>', $content);
	
  return $content;
}

add_theme_support('align-wide');
add_theme_support('disable-custom-colors');
add_theme_support('editor-color-palette', [ 
  [
    'name'    => 'White', 
    'slug'    => 'white', 
    'color'   => '##fff',
  ],

  [
    'name'    => 'Grey', 
    'slug'    => 'grey-light', 
    'color'   => '#fefefe',
  ],
]);

// debugging messages
function registerWhoops() {
  $whoops = new \Whoops\Run;
  $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
  $whoops->register();
}
if( WP_DEBUG && env('WP_ENV') === 'development' ) registerWhoops();

function remove_jquery_from_frontend() {
  if (!is_admin()) {
    wp_deregister_script('jquery'); // De-Register jQuery
  }
}


/*============================*/
/*      Admin Functions       */
/*============================*/
if (is_admin()) {
  $current_user = wp_get_current_user();
  add_action('admin_head', 'get_sage_admin_styles');

  // User is not an admin
  if (!in_array('administrator', $current_user->roles)) {
    add_action('admin_init', 'remove_menu_items_from_admin');
  }
}

/*===========================*/
/*          Actions          */
/*===========================*/
add_action('wp_before_admin_bar_render', 'clean_admin_bar');
add_action( 'edit_form_after_title', 'add_input_debug_preview' );


/*===========================*/
/*          Filters          */
/*===========================*/
add_filter('the_content' , 'lazy_load_editor_images');
add_filter('_wp_post_revision_fields', 'add_field_debug_preview');
