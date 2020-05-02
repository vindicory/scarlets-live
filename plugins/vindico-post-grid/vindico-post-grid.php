<?php
/**
 * Plugin Name: Vindico Post Grid Plugin
 * Plugin URI: https://www.vindico.net
 * Description: Display Rugby Matches Data using a shortcode in a page or post
 * Version: 0.1
 * Text Domain: vindico-post-grid
 * Author: Vindico
 * Author URI: https://www.vindico.net
 */


function wpcat_postsbycategory() {
  // the query
  $the_query = new WP_Query( array( 'News' => 'news', 'posts_per_page' => 3 ) ); 
   
  // The Loop
  if ( $the_query->have_posts() ) {
    $string .= '<div class="gridposts">';
    while ( $the_query->have_posts() ) {
      $the_query->the_post();
        $string .= '<a href="' . get_the_permalink() .'" rel="bookmark">';
        $featureImgArr = '../../wp-content/uploads/2020/04/Scarlets-Ultimate-XV-v2-1.jpg'; // . wp_get_attachment_image_src(get_post_thumbnail_id(), $size, true );
        $string .= '<div class="gridpostitem" style="background: url(' . $featureImgArr .'); background-size: cover; background-repeat: no-repeat; background-position: center;">';
        $string .= '  <div class="articlecategory"><div class="categorypadding bodyfont">NEWS</div></div>';
        $string .= '   <div class="articledate">';
        $string .= '     <div class="articlemonth bodyfont">MAR</div>';
        $string .= '     <div class="articleday titlefont">15</div>';
        $string .= '     <div class="articleyear bodyfont">2020</div>';  
        $string .= '   </div>';
        
        if ( has_post_thumbnail() ) {
          //$string .= '<a href="' . get_the_permalink() .'" rel="bookmark">' . get_the_post_thumbnail($post_id, array( 50, 50) ) . get_the_title() .'</a></div>';
          $string .= '<div class="articletitle"><div class="titlepadding bodyfont">'. get_the_title() .'</div></div>' ;
        } else { 
          // if no featured image is found
          $string .= '<div><a href="' . get_the_permalink() .'" rel="bookmark">' . get_the_title() .'</a></div>';
        }
        $string .= '</a>';
        $string .= '</div>';
    }
    $string .= '</div>';

  } else {
        // no posts found
  }
   
  return $string;
   
  /* Restore original Post Data */
  wp_reset_postdata();
  }
  // Add a shortcode
  add_shortcode('categoryposts', 'wpcat_postsbycategory');
   
  // Enable shortcodes in text widgets
  add_filter('widget_text', 'do_shortcode');
  
  //Register scripts to use
  function func_load_vuescripts() {
    wp_register_script('wpvue_vuejs', 'https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.min.js', array(), '2.6.11', true);
    wp_register_script('axios', 'https://cdn.jsdelivr.net/npm/axios@0.19.2/dist/axios.min.js', true);
  
    wp_register_script('my_vuecode', 'https://cdn.jsdelivr.net/gh/vindicory/scarlets-live/scripts/vindico-post-grid/vindico-post-grid-vue.min.js', 'wpvue_vuejs', true );
  }
  //Tell WordPress to register the scripts 
  add_action('wp_enqueue_scripts', 'func_load_vuescripts');

  //Return string for shortcode
  function func_wp_vue(){
    wp_enqueue_script('wpvue_vuejs');	
    wp_enqueue_script('axios');
  
    wp_enqueue_script('my_vuecode');

    //Build String
    $str= "<div id='divWpVue'><post-grid /></div>";

    //Return to display
    return $str;
  } // end function

add_action('rest_api_init', 'register_rest_images' );

function register_rest_images(){
    register_rest_field( array('post'),
        'fimg_url',
        array(
            'get_callback'    => 'get_rest_featured_image',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

function get_rest_featured_image( $object, $field_name, $request ) {
    if( $object['featured_media'] ){
        $img = wp_get_attachment_image_src( $object['featured_media'], 'app-thumb' );
        return $img[0];
    }
    return false;
}

//Add shortcode to WordPress
add_shortcode( 'wpvue', 'func_wp_vue' );
?>