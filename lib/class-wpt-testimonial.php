<?php
/*
 * WP Testimonial 
 * @define all functions
 * @register_post_type
 * */

/* Create a new WP Testimonial Post Type */
//Define action for register to "WP Testimonial" Post Type
add_action( 'init', 'register_wpt_testimonial_post_type' );

function register_wpt_testimonial_post_type() {
	//define array argument
	register_post_type( 'wpt_testimonial',
		array(
			'labels' => array(
				'name' => __( 'WP Testimonials' ),
				'singular_name' => __( 'WP Testimonial' )
			),
		  'public' => true,
		  'has_archive' => true,
          'supports' => array('title','editor','custom-fields','thumbnail')
		)
	);
}

/*
 * Change the "Featured Image" meta box title
 * 
**/
add_action( 'add_meta_boxes', 'wpt_testimonial_change_featured_meta_boxes_title');

function wpt_testimonial_change_featured_meta_boxes_title() {
 
	if (isset($_GET['post_type']) && $_GET['post_type'] == 'wpt_testimonial' ) {
		//Remove the exist Featured Image Metabox Div
		remove_meta_box( 'postimagediv', 'wpt_testimonial', 'side' );
		//add new metaboxes DIV
		add_meta_box( 'postimagediv', __('Featured Image (author image)'), 'post_thumbnail_meta_box', 'wpt_testimonial', 'side', 'low' );
	}


}


//Define the Action for change title of "Featured Image" to "Author Image"
add_filter('gettext', 'wpt_testimonial_change_add_psot_title', 20, 3);
/*
 * Change the text in the admin for my custom post type
 * 
**/
function wpt_testimonial_change_add_psot_title($newContent,$oldContent) {
  
  if( isset($_GET['post_type']) && $_GET['post_type'] == 'wpt_testimonial')  {
    //make the changes to the text
       if($oldContent=='Add New Post'):
          $newContent = __( 'Add New Testimonial');
        endif;
        
       if($oldContent=='Enter title here'):
          $newContent = __( 'Enter author name here');
        endif;
        //add more items
   }
   return $newContent;
}

/*
 * 
 * Add Some Extra Meta boxes
 * 
*/
 
//Define Action for add to meta boxes

add_action( 'add_meta_boxes', 'wpt_testimonial_add_extra_meta_box');
function wpt_testimonial_add_extra_meta_box() {
		add_meta_box(
			'wpt_testimonial',
			__( 'WP Testimonial Extra Information', 'wpt_testimonial' ),
			'wpt_testimonial_meta_box_callback','wpt_testimonial');
}
/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function wpt_testimonial_meta_box_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'wpt_testimonial_meta_box', 'wpt_testimonial_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$author = get_post_meta( $post->ID, '_wpt_testimonial_author', true );
	$designation = get_post_meta( $post->ID, '_wpt_testimonial_designation', true );
	$url = get_post_meta( $post->ID, '_wpt_testimonial_url', true );
    $wpt_html='<table><tr><th>&nbsp;</th><td>&nbsp;</td></tr>';
	$wpt_html .='<tr><th align="left"><label for="wpt_testimonial_designation">Author Role:</label></th><td><input type="text" id="wpt_testimonial_designation" name="wpt_testimonial_designation" placeholder="enter author role" value="' . esc_attr( $designation ) . '" size="50" /></td></tr>';
	
	$wpt_html.='<tr><th align="left"><label for="wpt_testimonial_url">URL:</label></th><td><input type="text" id="wpt_testimonial_url" name="wpt_testimonial_url" value="' . esc_attr( $url ) . '" size="50" placeholder="http://"/></td></tr>';
    
    $wpt_html.='<tr><th>&nbsp;</th><td>&nbsp;</td></tr></table>';
    
    echo $wpt_html;
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wpt_testimonial_save_meta_box_data( $post_id ) {
	// Check if our nonce is set.
	if ( ! isset( $_POST['wpt_testimonial_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['wpt_testimonial_meta_box_nonce'], 'wpt_testimonial_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'wpt_testimonial' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, its safe for us to save the data now. */
	
	
	// Make sure that it is set.
	if ( ! isset( $_POST['wpt_testimonial_designation'] ) ) {
		return;
	}

	// Sanitize designation input.
	$my_data = sanitize_text_field( $_POST['wpt_testimonial_designation'] );

	// Update the designation in the database.
	update_post_meta( $post_id, '_wpt_testimonial_designation', $my_data );
	
	// Make sure that it is set.
	if ( ! isset( $_POST['wpt_testimonial_url'] ) ) {
		return;
	}
	// Sanitize url input.
	$my_data = sanitize_text_field( $_POST['wpt_testimonial_url'] );
	// Update the url meta field in the database.
	update_post_meta( $post_id, '_wpt_testimonial_url', $my_data );
}
add_action( 'save_post', 'wpt_testimonial_save_meta_box_data' );


/*
 * wpt Sortcode
 * 
 */

add_shortcode('wpt_testimonials','get_all_testimonials');


function get_all_testimonials() {
//get the post
$getOptions =get_wpt_testimonials_options();
if($getOptions['wpt_sortby']!=''):$wpt_sortBy=$getOptions['wpt_sortby']; else: $wpt_sortBy='title'; endif;
if($getOptions['wpt_orderby']!=''):$wpt_orderby=$getOptions['wpt_orderby']; else: $wpt_orderby='ASC'; endif;
$wpt_query = new WP_Query('post_type=wpt_testimonial&post_status=publish&orderby='.$wpt_sortBy.'&order='.$wpt_orderby);
 // Restore global post data stomped by the_post().

?>
<div id="wpt_testimonial_list">
<?php
if( $wpt_query->have_posts() ) {
  while ($wpt_query->have_posts()) : $wpt_query->the_post(); ?>
	    <div id="wpt-<?php echo get_the_ID();?>">
	    <blockquote class="style1">
			<div class="content"><span class="laquo">&nbsp;</span><?php echo strip_tags(get_the_content());?><span class="raquo">&nbsp;</span></div>
			
			<div  class="wpt_author">
			<?php //get the author image
			echo get_the_post_thumbnail(get_the_ID(), array(50,40) );?>  
			
			<span>
			<?php
			if(get_post_meta(get_the_ID(), '_wpt_testimonial_url', true)==''): 
			 //get author title
			 the_title();
			 else:
			 echo '<a href="'.get_post_meta(get_the_ID(), '_wpt_testimonial_url', true).'" target="_blank">'.get_the_title().'</a>';
			 endif;
			  ?>
			   <?php if(get_post_meta(get_the_ID(), '_wpt_testimonial_designation', true)!=''): echo '<span class="authorRole">'.get_post_meta(get_the_ID(), '_wpt_testimonial_designation', true).'</span>';endif; ?>
			 </span>
			 
			 </div>
	   </blockquote></div>
	    
<?php
endwhile;
} 
wp_reset_query();
?>
</div>
<?php 
} // End wpt testiomonial content part

/*
 * Check wpt_testimonials shortcode exist or not
 */
 
if(shortcode_exists( 'wpt_testimonials' )):
add_action( 'wp_enqueue_scripts', 'wpt_testimonials_style' );
endif;

//register list page style files
function wpt_testimonials_style() {
wp_enqueue_script( 'jquery' ); // wordpress jQuery
wp_register_style( 'wpt_style', plugins_url('wp-testimonial/css/wpt-style.css'));
wp_register_script( 'wpt_js', plugins_url( 'wp-testimonial/js/jquery.cycle.all.lat.js'), array('jquery') );
wp_enqueue_style( 'wpt_style' );
wp_enqueue_script( 'wpt_js');
}

function get_wpt_random_testimonial() {
/** Get Testimonial Content*/

$getOptions =get_wpt_testimonials_options();
if($getOptions['wpt_sortby']!=''):$wpt_sortBy=$getOptions['wpt_sortby']; else: $wpt_sortBy='title'; endif;
if($getOptions['wpt_orderby']!=''):$wpt_orderby=$getOptions['wpt_orderby']; else: $wpt_orderby='ASC'; endif;
$wpt_query = new WP_Query('post_type=wpt_testimonial&post_status=publish&orderby='.$wpt_sortBy.'&order='.$wpt_orderby);

$effect=$getOptions['wpt_effect'];

if($effect==''){$effect='fade';}

$delayTimeVal=$getOptions['wpt_speed'];
$delayTimeVal=5000;
if($delayTimeVal!=''){$delayTime=$delayTimeVal;}else{$delayTime=5000; };

if($getOptions['wpt_content_limit']!=''):$content_limit=$getOptions['wpt_content_limit'];else:$content_limit="300";endif;


 // Restore global post data stomped by the_post().
$script="<script type='text/javascript'>
jQuery(document).ready(function() {
    jQuery('#wptTestimonials').cycle({
        fx: '".$effect."', // choose your transition type, ex: fade, scrollUp, scrollRight, shuffle
        speed:".$delayTime.", 
		delay:0,
		/*fit:true,*/
		
     });
});
</script>"; 
 
 
$wptContent='<div id="wptRandom">'; 
$wptContent.=$script;
$wptContent.='<div id="wptTestimonials" class="wptTestimonial">';
if( $wpt_query->have_posts() ) {
  while ($wpt_query->have_posts()) : $wpt_query->the_post(); 
  
if(strlen(strip_tags(get_the_content())) > $content_limit){ $moreContent='...';}else{$moreContent='';}
  
  if(get_post_meta(get_the_ID(), '_wpt_testimonial_url', true)==''): 
			 //get author title
			 $authorName=get_the_title();
			 else:
			$authorName='<a href="'.get_post_meta(get_the_ID(), '_wpt_testimonial_url', true).'" target="_blank">'.get_the_title().'</a>';
			 endif;
		
 if(get_post_meta(get_the_ID(), '_wpt_testimonial_designation', true)!=''): 
 $authorDesignation='<span class="designation">'.get_post_meta(get_the_ID(), '_wpt_testimonial_designation', true).'</span>';
 else:
 $authorDesignation='';
 endif; 
 	 
  $wptContent.='<blockquote>';
    
  $wptContent.='<p><span class="laquo">&nbsp;</span>'.substr(strip_tags(get_the_content()),0,$content_limit).$moreContent.'<span class="raquo">&nbsp;</span></p>';

  $wptContent.='<cite>- '.$authorName.$authorDesignation.'</cite>';
			  
  $wptContent.='</blockquote>';
  
  endwhile;
} 
wp_reset_query();
$wptContent.='</div>';

if($getOptions['wpt_viewall']==1): 
$wptContent.='<div class="view-all"><a href="'.$getOptions['wpt_viewall_page'].'">View All</a></div>';
endif; 
$wptContent.='</div>';
return $wptContent;

}
/* Shortcode for display the testimonial rutator*/
add_shortcode('wpt_random','get_wpt_random_testimonial');
?>
