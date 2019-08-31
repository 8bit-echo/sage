<?php
  /* https://raw.githubusercontent.com/Alecaddd/awps/master/inc/Core/WalkerNav.php */

namespace App\Classes;
use Walker_Nav_Menu;

class MegaNav extends Walker_Nav_Menu
{

  protected $top_level_item_img_desc;

  /**
   * "Start Element". Generally, this method is used to add the opening HTML tag for a single tree item (such as <li>, <span>, or <a>) to $output.
   */
  public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
  {
    $output .= "<!--start_el: " . $depth . "-->";
    if ($depth === 0) {
      $classes = [];
      $classes[] = in_array('current-menu-item', $item->classes) ? 'active' : null;
      $classes[] = $args->walker->has_children ? 'has-children' : '';
      $output .= '<li class="menu-item ' . implode(' ', $classes) . '"><a href="' . $item->url . '">' . $item->title . '</a>';

        // we'll need the image and the parent description later, but we can't use it where we need to yet. 
      $this->top_level_item_img_desc = '
          <img src="' . get_the_post_thumbnail_url($item->object_id) . '" alt="' . $item->title . '">
          <div class="description-wrapper">
             <p class="description">' . $item->description . '</p>' .
        '<div class="cta-wrapper">
                 <a class="cta btn-simple" href="' . $item->url . '">Explore</a>
               </div>
           </div>
        ';
    }


    if ($depth === 1) {

      $classes = [];
      $classes[] = $args->walker->has_children ? 'has-children' : null;
      $classes[] = $item->post_status == 'publish' ? null : 'hidden';
      $output .= '<li class="menu-item ' . implode(' ', $classes) . '"><a href="' . $item->url . '">' . $item->title . '</a>';
    }

    if ($depth === 2) {
      $output .= '<li class="menu-item grandchild"><a href="' . $item->url . '">' . $item->title . '</a>';
    }
  }


  /**
   * "Start Level". This method is run when the walker reaches the start of a new "branch" in the tree structure. Generally, this method is used to add the opening tag of a container HTML element (such as <ol>, <ul>, or <div>) to $output.
   */
  public function start_lvl(&$output, $depth = 0, $args = [])
  {
    $output .= "<!--start_lvl: " . $depth . "-->";
    if ($depth === 0) {
      $output .=
        '<div class="dropdown-wrapper">
        <div class="dropdown-inner">
          
            <div class="left">
               <!-- left-content -->
            </div>

            <div class="right">
                 <!-- right-content -->
            <div class="submenu-wrapper">
              <ul class="submenu">
           ';
        // these elements get closed in end_lvl();
    }

    if ($depth == 1) {
      $output .= '
          <ul class="grandchildren">
        ';
    }

  }

  /**
   * "End Element" . Generally, this method is used to add any closing HTML tag for a single tree item(such as < / li >, < / span >, or < / a >) to $output . Note that elements are not ended until after all of their children have been added .
   */
  public function end_el(&$output, $object, $depth = 0, $args = [])
  {
    $output .= '</li>';
    if ($depth === 0) {

        // this is the part where we inject the featured image and description into the output.
      $output = str_replace('<!-- left-content -->', $this->top_level_item_img_desc, $output);
    }

    $output .= "<!--end_el: " . $depth . "-->";
  }

  /**
   * "End Level" . This method is run when the walker reaches the end of a "branch" in the tree structure . Generally, this method is used to add the closing tag of a container HTML element (such as < / ol >, < / ul >, or < / div >) to $output .
   */
  public function end_lvl(&$output, $depth = 0, $args = [])
  {
    if ($depth === 0) {
        // close the child submenu, submenu-wrapper, div.right, dropdown-inner & dropdown-wrapper
      $output .= "
                  </ul> <!-- /.submenu -->
        </div><!-- /.submenu-wrapper -->
            </div><!-- /.right -->
          </div><!-- /.dropdown-inner -->
        </div><!-- /.dropdown-wrapper -->
        ";
    }

    if ($depth === 1) {
      $output .= '
          </ul> <!-- /.grandchildren -->';
    }
    $output .= "<!--end_lvl: " . $depth . "-->";

  }

}