<?php
/** Shared editorial collection configuration. Missing published guides are omitted. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
function stc_get_site_collection( $collection ) {
$collections = array();
$collections['city-guides'] = [
	[ 'name' => 'Beijing', 'copy' => 'History & culture', 'class' => 'beijing', 'image' => 'card-beijing-hd.webp' ],
	[ 'name' => 'Shanghai', 'copy' => 'Modern & vibrant', 'class' => 'shanghai', 'image' => 'card-shanghai-hd.webp' ],
	[ 'name' => 'Guangzhou', 'copy' => 'Business & shopping', 'class' => 'guangzhou', 'image' => 'card-guangzhou-hd.webp' ],
	[ 'name' => 'Chengdu', 'copy' => 'Pandas & laid-back', 'class' => 'chengdu', 'image' => 'card-chengdu-hd.webp' ],
	[ 'name' => 'Chongqing', 'copy' => 'Mountains & rivers', 'class' => 'chongqing', 'image' => 'card-chongqing-hd.webp' ],
	[ 'name' => "Xi'an", 'copy' => 'Ancient capital', 'class' => 'xian', 'image' => 'card-xian-hd.webp' ],
	[ 'name' => 'Hangzhou', 'copy' => 'Natural beauty', 'class' => 'hangzhou', 'image' => 'card-hangzhou-hd.webp' ],
	[ 'name' => 'Zhangjiajie', 'copy' => 'Otherworldly peaks', 'class' => 'zhangjiajie', 'image' => 'card-zhangjiajie-city-hd.webp' ],
];

$collections['attraction-guides'] = [
	[ 'name' => 'Forbidden City', 'city' => 'Beijing', 'tag' => 'Booking required', 'class' => 'forbidden-city', 'image' => 'card-forbidden-city-hd.webp' ],
	[ 'name' => 'Great Wall', 'city' => 'Beijing', 'tag' => 'Best time: Apr-Oct', 'class' => 'great-wall', 'image' => 'card-great-wall-hd.webp' ],
	[ 'name' => 'Terracotta Warriors', 'city' => "Xi'an", 'tag' => 'Passport', 'class' => 'terracotta', 'image' => 'card-terracotta-hd.webp' ],
	[ 'name' => 'Zhangjiajie', 'city' => 'Hunan', 'tag' => 'Best time: Apr-Nov', 'class' => 'zhangjiajie', 'image' => 'card-zhangjiajie-attraction-hd.webp' ],
	[ 'name' => 'West Lake', 'city' => 'Hangzhou', 'tag' => 'Best time: Mar-May', 'class' => 'west-lake', 'image' => 'card-west-lake-hd.webp' ],
	[ 'name' => 'Shanghai Disney Resort', 'city' => 'Shanghai', 'tag' => 'Booking required', 'class' => 'disney', 'image' => 'card-disney-hd.webp' ],
];

    $items = isset( $collections[$collection] ) ? $collections[$collection] : array();
    $items = apply_filters( 'stc_site_collection', $items, $collection );
    $result = array();
    foreach ( $items as $item ) {
        $key = isset( $item['entity_key'] ) ? $item['entity_key'] : $item['class'];
        $type = 'city-guides' === $collection ? 'city-guide' : 'attraction-guide';
        $fallback = array( 'beijing' => 'beijing-first-time-city-guide', 'forbidden-city' => 'forbidden-city-first-time-visitor-guide' );
        $guide = stc_resolve_entity_guide( $key, $type, isset( $fallback[$key] ) ? $fallback[$key] : '' );
        if ( ! $guide ) { continue; }
        $item['url'] = $guide['url'];
        $item['title'] = $item['name'];
        $item['copy'] = isset( $item['copy'] ) ? $item['copy'] : $item['city'];
        unset( $item['tag'] ); // Time-sensitive claims belong to reviewed guide content.
        $result[] = $item;
    }
    return $result;
}

/** A small editorial row, emitted only for real published recommendations. */
function stc_render_home_latest_guides() {
 $posts=get_posts(array('post_type'=>'post','post_status'=>'publish','has_password'=>false,'numberposts'=>3,'orderby'=>'date','order'=>'DESC'));
 if(!$posts){return;}
 echo '<section class="stc-section stc-home-latest" aria-labelledby="home-latest-title"><h2 id="home-latest-title">Fresh practical guides</h2><div class="stc-home-latest__links">';
 foreach($posts as $post){echo '<a href="'.esc_url(get_permalink($post)).'"><strong>'.esc_html(get_the_title($post)).'</strong><span>'.esc_html(wp_trim_words(get_the_excerpt($post),18)).'</span></a>';}
 echo '</div></section>';
}
