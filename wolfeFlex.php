<?php
/*
	Plugin Name: WolfeFlex
	Plugin URI: http://github.com/alexiswolfish/Flex
	Description: A simple FlexSlider Wordpress plugin
	Version: 1.0
	Author: Alex Wolfe
	Author URI: http://alexkwolfe.com		WordPress plugin that hacks the gallery[id="blah blah blah"] shortcode to display a 	clean basic flexslider instead of the default static thumbnails. No custom classes	or extra posts necessary, just use the normal add media button and the nice gallery	editor already available. 
*/

//define constants for Plugin details
define('WF_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
//add include files using Wordpress enqueue functions
wp_enqueue_script('flexslider', WF_PATH.'jquery.flexslider-min.js', array('jquery')); 
wp_enqueue_style('flexslider_css', WF_PATH.'flexslider.css');  
//hook flexslider js into the header of the wp-theme
function wfs_addScript(){
echo '<script type="text/javascript" charset="utf-8">
  jQuery(window).load(function() {
    jQuery(\'.flexslider\').flexslider();
  });
</script>';
}	
add_action('wp_head', 'wfs_addScript');
//use reg expressions to get the post IDs from gallery shortcode
function wfs_getGalleryIDs(){	$post_content = get_the_content();
	$hasGallery = preg_match('/\[gallery.*ids=.(.*).\]/', $post_content, $ids);
	$flexHtml = ' ';		//create html list for the slider
	if( $hasGallery = 1){
		$array_id = explode(",", $ids[1]);
		$flexHtml .= "
					<div class=\"flexslider\">\n";
		$flexHtml .= "<ul class=\"slides\">\n";
			foreach ($array_id as $id){			$caption =  get_post($id);						$flexHtml .= "<li>";			$flexHtml .= wp_get_attachment_image( $id,'medium');			if( !empty($caption->post_excerpt)){				$flexHtml .= "<p class=\"flex-caption\">";				$flexHtml .= $caption->post_excerpt;				$flexHtml .= "</p>";			}
			$flexHtml .= "</li>\n";
		}
		$flexHtml .= "</ul></div>";
	}
	else{
		$flexHtml = false;
	}
	return $flexHtml;
}
//filter that replaces Gallery shortcode with the newly generated flexslider html
function wfs_replaceGallery($content){
	global $post;
	$new_content = get_the_content();
		if(is_singular()){ //make sure that the gallery is on a page/single post
			$newGalleryHtml = wfs_getGalleryIDs();
			if($newGalleryHtml != false){
				$new_content = preg_replace('/\[gallery.*ids=.(.*).\]/', $newGalleryHtml, $content);
			}
		}
	return $new_content;
	}
add_filter( 'the_content', 'wfs_replaceGallery' );
?>