<?php
/*
	ComicPost Lite Admin Functions
	This file has all the functions that only work
	on the Admin back-end
*/

function comicpost_lite_set_custom_comic_columns( $columns ){
	if ( !comicpost_lite_other_comic_plugin_enabled() ){
		$columns['comic_image'] = __( 'Comic Image', 'comicpost' );
	}
	return $columns;
}

function comicpost_lite_custom_comic_column( $column, $post_id ){
	if ( !comicpost_lite_other_comic_plugin_enabled() ){
		if ($column == 'comic_image' ){
			echo get_the_post_thumbnail( $post_id, 'thumbnail' );
		// make sure image will fit:
	?><style type="text/css">
		.column-comic_image{
			width: 120px;
		}
			.column-comic_image img {
				width: 100%;
				height: auto;
			}
	  </style>
	<?php
		}
	}
}
add_filter( 'manage_comic_posts_columns', 'comicpost_lite_set_custom_comic_columns' );
add_filter( 'manage_comic_posts_custom_column', 'comicpost_lite_custom_comic_column', 10, 2);

/* Adds Drop-down list of Chapters to Filter Comic Post Management List */
function add_comicpost_lite_taxonomy_filters() {
	if (!comicpost_lite_other_comic_plugin_enabled() ){
		global $typenow;
	 
		// an array of all the taxonomyies you want to display. Use the taxonomy name or slug
		$taxonomies = array('chapters');
	 
		// must set this to the post type you want the filter(s) displayed on
		if( $typenow == 'comic'){
	
			foreach ($taxonomies as $tax_slug) {
				$tax_obj = get_taxonomy($tax_slug);
				$tax_name = $tax_obj->labels->name;
				$terms = get_terms($tax_slug);
				if(count($terms) > 0) {
					echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
					echo "<option value=''>Show All $tax_name</option>";
					foreach ($terms as $term) {
						echo '<option value='. $term->slug, isset($_GET[$tax_slug]) && $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>'; 
					}
					echo "</select>";
				}
			}
		}
	}
}
add_action( 'restrict_manage_posts', 'add_comicpost_lite_taxonomy_filters' );


 function comicpost_lite_admin_notice(){
    global $typenow;
    if ( $typenow == 'comic' ) {
         echo '<div class="notice notice-info is-dismissible">
         			<img src="'.comicpost_lite_pluginfo('plugin_url').'images/comicpost_logo.png" height="48" width="48" style="float:left;margin-right:10px;height:48px;width:48px;"/>
             		<p>You are using the <strong>COMICPOST LITE</strong> plugin.  There is a <strong>FREE</strong> <em>full feature</em> version available 
             		which adds content restrictions, watermarking, social media sharing, and anti-AI tools in addition to comics management. You can 
             		download it here: https://www.github.com/kmhcreative/comicpost</p>
         	   </div>';
    }
}
add_action('admin_notices', 'comicpost_lite_admin_notice');