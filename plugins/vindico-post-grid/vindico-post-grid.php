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


function wpcat_postsbycategory($atts, $content = null, $tag) {
  
  extract( shortcode_atts( array(
    'limit' => '10',
    'position' => 'na',
    'category' => '',
    'gridclass' => 'gridposts',
    'postclass' => 'articleclass'    
  ), $atts ) );
  
  if ($category !== null) { 
    $the_query = new WP_Query( array( 'category_name' => $category, 'posts_per_page' => $limit ) ); 
    
    if ( $the_query->have_posts() ) {

      if ($limit > 1) { 
        $output .= '<div class="'. $gridclass .'">';
      }
      $count = 0;
      $countType = '';
      while ( $the_query->have_posts() ) {
        $the_query->the_post();
        $count++;
        if($count % 2 == 0){ 
          $countType = 'postitem-even';  
        } else { 
          $countType = 'postitem-odd';  
        } 
        $postCategory = strtoupper($category);
          $postTitle = get_the_title();     
          $postMonth = strtoupper(get_the_date(__('M')));
          $postDay = strtoupper(get_the_date(__('d')));
          $postYear = strtoupper(get_the_date(__('Y')));
          $postLink = get_the_permalink();
          $postImage = wp_get_attachment_image_src(get_post_thumbnail_id(), $size, true )[0];
          $output .= '<div onclick="window.location.href = \''. $postLink .'\'" class="gridpostitem ' . $postclass . ' postitem-' . $count . ' ' . $countType . '" style="background: url(' . $postImage .'); background-size: cover; background-repeat: no-repeat; background-position: center;">';
          $output .= '  <div class="articlecategory"><div class="categorypadding bodyfont">' . $postCategory . '</div></div>';
          $output .= '  <div class="articledate">';
          $output .= '    <div class="articlemonth bodyfont">' . $postMonth . '</div>';
          $output .= '    <div class="articleday titlefont">' . $postDay . '</div>';
          $output .= '    <div class="articleyear bodyfont">' . $postYear . '</div>';  
          $output .= '  </div>';
          $output .= '  <div class="articletitle"><a href="' . $postLink .'" rel="bookmark"><div class="titlepadding bodyfont">' . $postTitle .'</div></a></div>';
          $output .= '</div>';      
      }
      if ($limit > 1) { 
        $output .= '</div>';
      }

    } else {
      // $output = '<pre style="font-size:15px">NO POSTS FOUND</pre>';
    }
  } else {
    $output = 'CATEGORY NOT SPECIFIED';
    // the query
  }
    /* Restore original Post Data */
    wp_reset_postdata();
  return $output; //$string;

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