<?php 

namespace App\Classes;

class API
{
  private $post_types;

  function __construct() {
    $this->post_types = self::getPostTypes();
    $this->addTermsToRESTAPI();
  }

  public static function getPostTypes() {
    return array_filter(get_post_types(), function ($post_type) {
      return !in_array($post_type, self::getExtraneousPostTypes());
    });
  }

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

  function addTermsToRESTAPI() {
    foreach (self::getPostTypes() as $post_type) {
      register_rest_field($post_type, 'taxonomies', [
        'get_callback' => function ($post) use ($post_type) {
          return self::getTermSchema($post_type, $post);
        },
      ]);
    }
  }

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
}

