<?php 

namespace App\Controllers\Partials;
use WP_Query;

trait PostTrait {

  public static function getRelatedByTerms($post, $term) {
    $custom_taxterms = wp_get_object_terms($post->ID, $term, ['fields' => 'ids']);
    $args = [
      'post_type' => get_post_type($post),
      'post_status' => 'publish',
      'posts_per_page' => 3,
      'orderby' => 'term_id',
      'tax_query' => [
        [
          'taxonomy' => $term,
          'field' => 'id',
          'terms' => $custom_taxterms
        ]
      ],
      'post__not_in' => [$post->ID],
    ];
    $related_items = new WP_Query($args);

    if ($related_items->have_posts()) {
      return $related_items->posts;
    } else {
      return [];
    }
  }

  public static function getByMeta($post_type, $key, $value, $limit = -1) {
    $query = new WP_Query([
      'post_type'      => $post_type,
      'post_status'    => 'publish',
      'posts_per_page' => $limit,
      'meta_query'     => [
        [
          'meta_key'   => $key,
          'meta_value' => $value,
          'compare'    => '='
        ]
      ]
    ]);

    if ($query->have_posts()) {
      return $query->posts;
    } else {
      return [];
    }
  }

}