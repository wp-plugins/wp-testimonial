<?php
/**
 * 
 * Register WP Testimonial Rotator 
 */

//check widget active or not

add_action('wp_head','wpt_load_inline_js');

class wpt_testimonials_widget extends WP_Widget {
        function wpt_testimonials_widget() {
            $widget_ops = array('description' => __('Display auto rutate testimonials in your sidebar', 'WP Testimonial Rotator'));
            $this->WP_Widget('wpt_testimonials', __('WP Testimonial'), $widget_ops);
        }
       
        // Display Widget
        function widget($args, $instance) {
            extract($args);
            $title = esc_attr($instance['title']);

            echo $before_widget.$before_title.$title.$after_title;

                get_wpt_testimonials_content();

            echo $after_widget;
        }

        // When Widget Control Form Is Posted
        function update($new_instance, $old_instance) {
            if (!isset($new_instance['submit'])) {
                return false;
            }
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            return $instance;
        }

        // DIsplay Widget Control Form
        function form($instance) {
            global $wpdb;
            $instance = wp_parse_args((array) $instance, array('title' => __('wpt_testimonials', 'wpt_testimonials')));
            $title = esc_attr($instance['title']);
    ?>

    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'Simple Testimonial Widget'); ?>
    <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label>

    <input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
    <?php
        }
    }

### Function: Init Simple Testiomonial Rotator Widget
add_action('widgets_init', 'wpt_testiomonials_init');
function wpt_testiomonials_init() {
        register_widget('wpt_testimonials_widget');
    }

/*
 * Load jQuery code in header   
 */

function wpt_load_inline_js()
{
	$getOptions =get_wpt_testimonials_options();
	$delayTimeVal=$getOptions['wpt_speed'];
	$delayTimeVal=5000;
	if($delayTimeVal!=''){$delayTime=$delayTimeVal;}else{$delayTime=5000; };
	
	$jscnt ="<script>
		jQuery(function($) {
			jQuery('#wpt_testimonial > div:gt(0)').hide();
			setInterval(function() { 
			  jQuery('#wpt_testimonial > div:first')
			    .fadeOut(1000)
			    .next()
			    .fadeIn(1000)
			    .end()
			    .appendTo('#wpt_testimonial')
			},  ".$delayTimeVal.")
		})
	</script>";
	
	echo $jscnt;
	}	   

function get_wpt_testimonials_content() {
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

if($getOptions['wpt_content_limit']!=''):$content_limit=$getOptions['wpt_content_limit'];else:$content_limit="400";endif;

 // Restore global post data stomped by the_post().
$script="<script type='text/javascript'>
jQuery(document).ready(function($) {
    jQuery('#wptTestimonialsWidget').cycle({
        fx: '".$effect."', // choose your transition type, ex: fade, scrollUp, scrollRight, shuffle
        speed:".$delayTime.", 
		delay:0,
		/*fit:true,*/
		
     });
});
</script>"; 
 
$wptContent='<div id="wptWidget" class="wptWidget">'; 
$wptContent.=$script;
$wptContent.='<div id="wptTestimonialsWidget" class="wptTestimonial">';
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
echo $wptContent;
}
?>
