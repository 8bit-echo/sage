<?php 

namespace App\Classes;

class API
{

  function __construct() {
    $this->addTaxonomies();
    $this->addFeaturedMedia();
  }

  /**
   * Gets a list of sane post types excluding those that are built into WP. 
   */ 
  public static function getPostTypes() {
    return array_filter(get_post_types(), function ($post_type) {
      return !in_array($post_type, self::getExtraneousPostTypes());
    });
  }

  /**
   * Get a list of all the post types in WP that are not commonly used or visible.
   */ 
  public static function getExtraneousPostTypes() {
    return [
      'attachment',
      'revision',
      'nav_menu_item',
      'custom_css',
      'customize_changeset',
      'oembed_cache',
      'user_request',
      'wp_block',
      'acf-field-group',
      'acf-field',
    ];
  }

  /**
   * Register Taxonomies and terms to the WP REST API post response.
   */ 
  function addTaxonomies() {
    foreach (self::getPostTypes() as $post_type) {
      register_rest_field($post_type, 'taxonomies', [
        'get_callback' => function ($post) use ($post_type) {
          return self::getTermSchema($post_type, $post);
        },
      ]);
    }
  }

  /**
   * Return the data structure of taxonomies, labels and terms in the REST response.
   */ 
  private static function getTermSchema($post_type, $post) {
    $schema = [];
    $taxonomies = get_object_taxonomies($post_type, 'objects');
    foreach ($taxonomies as $tax) {
      $schema[$tax->name] = [
        'label' => $tax->label,
        'terms' => []
      ];

      $schema[$tax->name]['terms'] = array_map(
        function ($term) {
          return $term->name;
        },
        get_the_terms($post->ID, $tax->name)
      );
    }
    return $schema;
  }

  /**
   * Add featured media URL to REST API response
   */ 
  private static function addFeaturedMedia() {
    foreach (self::getPostTypes() as $post_type) {
      register_rest_field($post_type, 'featured_media_url', [
        'get_callback' => function ($post) use ($post_type) {
          return get_the_post_thumbnail_url($post->ID);
        },
      ]);
    }
  }
}

