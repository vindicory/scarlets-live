<?php
/**
 * Plugin Name: Vindico Youtube Grid Plugin
 * Plugin URI: https://www.vindico.net
 * Description: Display's Latest Youtube Videos from Channel using Youtube API
 * Version: 0.1
 * Text Domain: vindico-youtube-grid
 * Author: Vindico
 * Author URI: https://www.vindico.net
 * Example:   [youtube-grid youtubeapikey="AIzaSyDQ0sqhPW_KM_n3mDyBFVu_KZusHwKZQrE" youtubechannel="UCSpQ51CzUYp_ambKRD7fDCg" limit="4" /]
 */


function vindico_youtube_grid($atts, $content = null, $tag) {
  extract( shortcode_atts( array(
    'limit' => '4',
    'position' => 'na',
    'order' => 'date',
    'type' => 'video',
    'category' => '',
    'gridclass' => 'youtubegrid',
    'postclass' => 'youtubepost',
    'youtubeapikey' => '',
    'youtubechannel' => '',
  ), $atts ) );

  $apiURL = 'https://www.googleapis.com/youtube/v3/search?part=snippet'; // main apiURL
  $apiURL .= '&channelId=' . $youtubechannel; 
  $apiURL .= '&maxResults=' . $limit;
  $apiURL .= '&order=' . $order;
  $apiURL .= '&type=' . $type;
  $apiURL .= '&key=' . $youtubeapikey;
  
  //$request = wp_remote_get($apiURL, array('sslverify' => false) );
  $sampleData = plugin_dir_path( __FILE__ ) . 'sampledata.json';
  $json = json_decode(file_get_contents($sampleData));
   
  
  //$body = wp_remote_retrieve_body( $request );
  
  
  
//  $json = json_decode($json);
  $items = $json->items; // extract data tree
  
//  $items = explode(',', $json);

$count = 0;
$countType = '';
if ($limit > 1) { 
  $output .= '<div class="youtubegrid">';
}

  foreach ($items as $item) {
    $count++;
    if($count % 2 == 0){ 
      $countType = 'videoitem-even';  
    } else { 
      $countType = 'videoitem-odd';  
    } 
//    $output .= $item->snippet->title . '<br />';
    $postCategory = 'YOUTUBE'; //strtoupper($category);
    $postTitle = $item->snippet->title;
//    2020-04-23T10:27:05.000Z",
    $postDate = date_create($item->snippet->publishTime);    
    $postMonth = date_format($postDate, 'M');
    $postDay = date_format($postDate, 'd'); // strtoupper(get_the_date(__('d')));
    $postYear = date_format($postDate, 'Y');; //strtoupper(get_the_date(__('Y')));
    $postVideoId = $item->id->videoId;
    $postLink = 'https://www.youtube.com/embed/' . $postVideoId . '?wmode=transparent&autoplay=1'; //get_the_permalink();
    $postImage = $item->snippet->thumbnails->high->url; //'https://images.wallpaperscraft.com/image/fireplace_example_interior_living_room_sofas_80288_1280x720.jpg'; //wp_get_attachment_image_src(get_post_thumbnail_id(), $size, true )[0];
    $output .= '<div onclick="openModal(\''.$postLink.'\');" class="gridpostitem ' . $postclass . ' postitem-' . $count . ' ' . $countType . '" style="background: url(' . $postImage .'); background-size: cover; background-repeat: no-repeat; background-position: center;">';
    $output .= '  <div class="youtubecategory"><div class="categorypadding">' . $postCategory . '</div></div>';
    $output .= '  <div class="articledate">';
    $output .= '    <div class="articlemonth">' . $postMonth . '</div>';
    $output .= '    <div class="articleday">' . $postDay . '</div>';
    $output .= '    <div class="articleyear">' . $postYear . '</div>';  
    $output .= '  </div>';
    $output .= '  <div class="articletitle"><a href="' . $postLink .'" rel="bookmark"><div class="titlepadding">' . $postTitle .'</div></a></div>';
    $output .= '</div>';
    //    $output .= '<iframe title="TEST TEST TEST" allowtransparency="true" class="youtubeiframe" src="' . $postLink . '"></iframe></div>';
  }
  if ($limit > 1) { 
    $output .= '</div>';
  }

  //$output = $apiURL;
  //return 'Path is ' . $sampleData;
  //return 'var_dump - ' . var_dump($json);
  //return 'JSON - <pre>' . print_r($json->items, true) . '</pre>';
  return $output; //$string;

}
// Add a shortcode
add_shortcode('youtube-grid', 'vindico_youtube_grid');
?>