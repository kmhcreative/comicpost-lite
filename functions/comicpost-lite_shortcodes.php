<?php
/*	ComicPost Lite Shortcodes
	Note that these can be used with the ComicPost plugin enabled
	but the plugin has better shortcodes for these with more options
*/

add_shortcode('comicpost-chapter-list', 'comicpost_lite_chapter_list');
add_shortcode('comicpost-archive-dropdown','comicpost_lite_archive_dropdown');
add_shortcode('comicpost-latest-comic', 'comicpost_lite_show_latest_comic');
/* 	Utility function
	This normalizes term names or slugs to term_ids
*/
function comicpost_lite_get_term_ids( $list, $tax ){
	$include_list  = explode(',', $list);
	$includes = array(); // holding pen
	foreach( $include_list as $included ){
		$add = $included;
		if (!is_numeric($included)){ // not a number
			if (get_term_by('name', $included, $tax)){ // check if by name
				$add = get_term_by('name', $included, $tax)->term_id;
			} else if (get_term_by('slug', $included, $tax)){ // check if by slug
				$add = get_term_by('slug', $included, $tax)->term_id;
			} else { // not a valid term for tax
				$add = null;
			}
		}
		if (!empty($add)){
			$includes[] = $add; // add it to holding pen
		}
	}
	// cast array back to string
	$list = implode(',', $includes);
	// return it
	return $list;
}
/*	Add a simple list of Comic Chapters anywhere
	Example usage: 	[comicpost-lite-chapter-list] // list with ALL Chapters and sub-chapters in default style
					[comicpost-lite-chapter-list exclude="124,142,143,168"] // list excluding 4 Chapters and all their sub-chapters
*/
function comicpost_lite_chapter_list( $atts, $content='' ){
	extract( shortcode_atts( array(
	    'include' => '',
		'exclude' => '',
		'emptychaps' => 'hide',
		'order' => 'ASC',
		'orderby' => 'name',
		'postdate' => 'last',
		'title' => 'Chapters',
		'thumbnails' => 'none',
		'postcount'  => false,
		'liststyle'  => 'flat'
	), $atts) );
	// PASSED AS STRINGS NOT BOOLEANS!!
	if ($include != 'all'){
		$include = comicpost_lite_get_term_ids( $include, 'chapters');
	}
		$exclude = comicpost_lite_get_term_ids( $exclude, 'chapters');
	// set show/hide empties
	if ($emptychaps == 'hide'){
		$hide_empty = 1;
		$hide_if_empty = true;
	} else {
		$hide_empty = 0;
		$hide_if_empty = false;
	}
	// allow multiples to be on one page
	$uid = wp_unique_id();
	// custom walker to get thumbnails
	$my_walker = new ChapterThumbnail_Walker();	
	// Build arguments for drop-down
	$args = array(
	    'include'	    => $include,
		'exclude' 		=> $exclude,
		'exclude_tree' 	=> $exclude,
		'hierarchical'  => 1,
		'depth'			=> 0,
		'hide_empty'    => $hide_empty,
		'hide_if_empty' => $hide_if_empty,
		'walker'		=> $my_walker,
		'taxonomy'		=> 'chapters',
		  'order'		=> $order,
		  'orderby'     => $orderby,
		  'postdate'    => $postdate,
		'title_li'		=> $title,
		'echo'			=> 0,
		'thumbnails'    => $thumbnails,
		'show_count'	=> $postcount,
		'liststyle'     => $liststyle
	);
	$output = '<ul id="chapters_list_'.$uid.'" class="chapters-list">';
	// get chapter terms	
//	$terms = get_terms( $args );
	$output .= wp_list_categories( $args ).'</ul>';

	return $output;
}

function comicpost_lite_chapter_thumbnail($chapter,$firstlast = 'first',$size = 'thumbnail'){
	if ( $firstlast == 'first' ){
		$order = 'ASC';
	} else if ( $firstlast == 'last' ){
		$order = 'DESC';
	} else {
		return;
	}
	$args = array(
		'showposts' => 1,
		'post_type' => 'comic',
		'tax_query' => array(
			array(
				'taxonomy' => 'chapters',
				'terms'    => $chapter->term_id
			)
		),
		'posts_per_page' => '1',
		'order' => $order
	);
	$firstlast_post = null;
	$image = array();
	// get first post
	$firstlast_post_query = new WP_Query( $args );
	$posts = $firstlast_post_query->get_posts();

	if( !empty( $posts )){
		$firstlast_post = array_shift( $posts );
	}
	if ($firstlast_post){
		if (has_post_thumbnail( $firstlast_post->ID )){
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $firstlast_post->ID), $size );
		}
	}
	return $image;
}
class ChapterThumbnail_Walker extends Walker_Category {

	function start_el(&$output, $item, $depth=0, $args=array(), $current_object_id = 0){
        $image = comicpost_lite_chapter_thumbnail($item,$args['thumbnails']);
			if (!empty($image)){
				$thumbnail = '<img class="comicpost-chapter-thumbnail" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" alt="Chapter thumbnail image for'.esc_attr($item->name).'."/>';
			} else {
				$thumbnail = '';
			}
		if ( $args['show_count']){
			$show_count = ' <span class="comicpost-post-count">('.$item->count.')</span>';
		} else {
			$show_count = '';
		}
		if ( $args['liststyle'] == 'indent' ){
			$list_style = " comicpost-list-indent";
		} else {
			$list_style = "";
		}
		$output .= '<li class="comicpost-chapter-list-item'.$list_style.'"><a href="'.get_category_link( $item ).'" class="chapter-list-item-link">'.$thumbnail.'<span class="chapter-title">'.esc_attr($item->name).'</span>'.$show_count.'</a>';
    }
	function end_el(&$output, $item, $depth=0, $args=array() ){
		$output .= "</li>\n";
	}
}

/*	Add a drop-down list of Comic Chapters anywhere
	Example usage: 	[comicpost_lite-archive-dropdown] // drop-down with ALL Chapters and sub-chapters
					[comicpost_lite-archive-dropdown exclude="124,142,143,168"] // drop-down excluding 4 Chapters and all their sub-chapters
*/
function comicpost_lite_archive_dropdown( $atts, $content='' ){
	extract( shortcode_atts( array(
	    'include' => '',
		'exclude' => '',
		'emptychaps' => true,
		'title' => 'Select Chapter'
	), $atts) );
	// PASSED AS STRINGS NOT BOOLEANS!!
	if ($include != 'all'){
		$include = comicpost_lite_get_term_ids( $include, 'chapters');
	}
		$exclude = comicpost_lite_get_term_ids( $exclude, 'chapters');
	// set show/hide empties
	if ($emptychaps == 'hide'){
		$hide_empty = 1;
		$hide_if_empty = true;
	} else {
		$hide_empty = 0;
		$hide_if_empty = false;
	}
	// allow multiples to be on one page
	$uid = wp_unique_id();
	// figure out if we need slug or term_id based on permalink structure

	// Build arguments for drop-down
	$args = array(
	    'include'	    => $include,
		'exclude' 		=> $exclude,
		'exclude_tree' 	=> $exclude,
		'hierarchical'  => 1,
		'depth'			=> 0,
		'hide_empty'    => $hide_empty,
		'hide_if_empty' => $hide_if_empty,
		'show_option_none' => $title,
		'id' 			=> 'comicpost_chapter_drop'.$uid.'',
		'name'			=> 'comicpost_chapter_drop'.$uid.'',
		'taxonomy'		=> 'chapters',
		'selected'		=> 'chapters',
		'value_field'	=> 'slug',
		'echo'			=> 0
	);
	$select  = wp_dropdown_categories( $args );
	// get chapter terms	
	$terms = get_terms( 'chapters' );
	if (empty($terms)){
		// if there are no terms dropdown would be empty, so bail...
		return;
	} else {
		// if permalink structure is empty URL ends in ?chapters=slug
		if (empty(get_option('permalink_structure'))){
			$linkfront = explode('=', get_term_link( $terms[0] ));
			$linkfront = $linkfront[0].'=';
		} else {
			if (!empty($terms[0])){
				$linkfront = dirname( get_term_link( $terms[0] ) ).'/'; // its an object, no 2nd param needed
			} else {
				$linkfront = get_option('home').'/chapters/';
			}
		}
	}
    $replace = "<select$1 onchange=\"location.href='".$linkfront."'+this.options[this.selectedIndex].value\">";
    $select  = preg_replace( '#<select([^>]*)>#', $replace, $select );

	return $select;
}
/* Simple function to show image of latest comic from a chapter */
function comicpost_lite_show_latest_comic( $atts, $content='' ){
	extract( shortcode_atts( array(
			'chapter' => '',
			'size' => 'large',
			'link'    => true
		), $atts) );
		if ( !empty($chapter) ){
			$chapter = comicpost_lite_get_term_ids( $chapter, 'chapters' );
		} else {
			return;
		}
		$args = array(
			'showposts' => 1,
			'post_type' => 'comic',
			'tax_query' => array(
				array(
					'taxonomy' => 'chapters',
					'terms'    => $chapter
				)
			),
			'posts_per_page' => '1',
			'order' => 'DESC'
		);
	$latest_post = null;
	$image = array();
	// get first post
	$latest_post_query = new WP_Query( $args );
	$posts = $latest_post_query->get_posts();
	if( !empty( $posts )){
		$latest_post = array_shift( $posts );
	}
	if ($latest_post){
		if (has_post_thumbnail( $latest_post->ID )){
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $latest_post->ID), $size );
		}
	}
	if (!empty($image)){
		$content = '<div class="comicpost-wrap">';
		if ($link) {
			$content .= '<a href="'.get_permalink( $latest_post->ID ).'" title="Go to latest post from '.get_term($chapter)->name.'">';
		}
		$content .= '<img class="comic" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" alt="Latest image for '.esc_attr(get_term($chapter)->name).'."/>';
		if ($link) {
			$content .= '</a>';
		}
		$content .= '</div>';
		return $content;
	} else {
		return;
	}
}

if (!function_exists('wp_unique_id')){
	function wp_unique_id( $prefix = '' ) {
		static $id_counter = 0;
		return $prefix . (string) ++$id_counter;
	};
}