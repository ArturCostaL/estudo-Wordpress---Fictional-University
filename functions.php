<?php

function university_files() {
  
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_script('main-university-js', 'http://localhost:3000/bundled.js', NULL, '1.0', true);

  
}
function university_features() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_image_size('professorLandscape', 400, 260, true);
  add_image_size('professorPortrait', 480, 650, true);
  add_image_size('pageBanner', 1500, 350, true);
}

function university_post_types(){
  
  #CAMPUS POST TYPE
  register_post_type('campus', array(
    
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'excerpt'),
    'has_archive' => true,
    'rewrite' => array('slug' => 'campuses' ),
    'public' => true,
    'labels' => array(
      'name' => 'Campuses',
      'add_new_item' => 'Add New Campus',
      'edit_item' => 'Edit Campus',
      'all_items' => 'All Campuses',
      'singular_name' => 'Campus'
    ),
    'menu_icon' => 'dashicons-location-alt'
  ));
  #EVENT POST TYPE
  register_post_type('event', array(
     
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'excerpt'),
    'has_archive' => true,
    'rewrite' => array('slug' => 'events' ),
    'public' => true,
    'labels' => array(
      'name' => 'Events',
      'add_new_item' => 'Add New Event',
      'edit_item' => 'Edit Event',
      'all_items' => 'All Events',
      'singular_name' => 'Event'
    ),
    'menu_icon' => 'dashicons-calendar-alt'
  ));

  #PROGRAMS POST TYPE

  register_post_type('program', array(
    
    'show_in_rest' => true,
    'supports' => array('title', 'editor'),
    'has_archive' => true,
    'rewrite' => array('slug' => 'programs' ),
    'public' => true,
    'labels' => array(
      'name' => 'Programs',
      'add_new_item' => 'Add New Program',
      'edit_item' => 'Edit Programs',
      'all_items' => 'All Programs',
      'singular_name' => 'Program'
    ),
    'menu_icon' => 'dashicons-awards'
  ));

  #PROFESSOR POST TYPE

  register_post_type('professor', array(
    
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'thumbnail'),
    'public' => true,
    'labels' => array(
      'name' => 'Professors',
      'add_new_item' => 'Add New professor',
      'edit_item' => 'Edit Professors',
      'all_items' => 'All Professors',
      'singular_name' => 'Professor'
    ),
    'menu_icon' => 'dashicons-welcome-learn-more'
  ));


}

function university_adjust_queries($querie){
  if(!is_admin() and is_post_type_archive('event') and is_main_query() ){
    $querie -> set('meta_key', 'event_date');
    $querie -> set('orderby', 'meta_value_num');
    $querie -> set('order', 'ASC');
    $today = date('Ymd');
    $querie -> set('meta_query', array(
      array(
        'key' => 'event_date',
        'compare' => '>=',
        'value' => $today,
        'type' => 'numeric'
        )
    )
  );
}

  if(!is_admin() and is_post_type_archive('program') and is_main_query() ){
    $querie -> set('posts_per_page', -1);
    $querie -> set('orderby', 'title');
    $querie -> set('order', 'ASC');
  }
  
}

function pageBanner($args = NULL){

  if(!$args['title']) {
    $args['title'] = get_the_title();
  }
  if(!$args['subtitle']) {
    $args['subtitle'] = get_field('page_banner_subtitle');
  }
  if(!$args['photo']) {
    if(get_field('page_banner_background')){
      $args['photo'] = get_field('page_banner_background')['sizes']['pageBanner'];
    } else {
      $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    }

  }
  
  ?>

  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle'];?></p>
      </div>
    </div>  
  </div>

  <?php
}

add_action('wp_enqueue_scripts', 'university_files');
add_action('after_setup_theme', 'university_features');
add_action('init', 'university_post_types');
add_action('pre_get_posts', 'university_adjust_queries');