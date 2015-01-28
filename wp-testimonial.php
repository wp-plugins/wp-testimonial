<?php
/*
Plugin Name: WP Testimonial
Plugin URI: http://raghunathgurjar.wordpress.com
Description: "WP Testimonial" is a very simple plugins for add to testimonials on your site. 
Version: 1.0
Text Domain: raghunath
Author: Raghunath Gurjar
Author URI: http://www.facebook.com/raghunathprasadgurjar
*/

/* 
* Setup Admin menu item 
*/
//Admin "WP Testimonial" Menu Item
add_action('admin_menu','wpt_testimonials_menu');
function wpt_testimonials_menu(){
	add_options_page('WP Testimonial','WP Testimonial','manage_options','wpt-testimonials-plugin','wpt_testimonials_admin_option_page');
}
//Define Action for register "WP Testimonial" Options
add_action('admin_init','wpt_testimonials_init');
//Register "WP Testimonial" options
function wpt_testimonials_init(){
	register_setting('wpt_testimonial_options','wpt_effect');
	register_setting('wpt_testimonial_options','wpt_speed');
	register_setting('wpt_testimonial_options','wpt_sortby');	
	register_setting('wpt_testimonial_options','wpt_orderby');
	register_setting('wpt_testimonial_options','wpt_viewall');
	register_setting('wpt_testimonial_options','wpt_content_limit');
	register_setting('wpt_testimonial_options','wpt_viewall_page');
} 


add_action('admin_footer','add_wpt_admin_style_script');

if(!function_exists('add_wpt_admin_style_script')):
function add_wpt_admin_style_script()
{
wp_register_style( 'wpt_admin_style', plugins_url( 'css/admin-wpt.css',__FILE__ ) );
wp_enqueue_style( 'wpt_admin_style' );

echo $script='<script type="text/javascript">
	/* WP Testimonial admin js*/
	jQuery(document).ready(function(){
		jQuery(".wpt-tab").hide();
		jQuery("#div-wpt-general").show();
	    jQuery(".wpt-tab-links").click(function(){
		var divid=jQuery(this).attr("id");
		jQuery(".wpt-tab-links").removeClass("active");
		jQuery(".wpt-tab").hide();
		jQuery("#"+divid).addClass("active");
		jQuery("#div-"+divid).fadeIn();
		});
	    
		})
	</script>';

	}
endif;

/* Display the Options form for Custom Tweets */
function wpt_testimonials_admin_option_page(){ ?>
	<div> 
	<h2>WP Testimonial Settings</h2>
<!-- Start Options Form -->
	<form action="options.php" method="post" id="wpt-testimonial-admin-form">
		
	<div id="wpt-tab-menu"><a id="wpt-general" class="wpt-tab-links active" >General Settings</a> <a  id="wpt-advance" class="wpt-tab-links">Advance Settings</a> <a  id="wpt-shortcode" class="wpt-tab-links">Shortcode</a> <a  id="wpt-support" class="wpt-tab-links">Support</a> </div>
	
	<div class="wpt-setting">
	<!-- General Setting -->	
	<div class="first wpt-tab" id="div-wpt-general">
	<h2>General Settings</h2>
	<table class="wp-testimonial">
	<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<th>Choose Effect</th>
				<td >
				<select id="wpt_effect" name="wpt_effect">
				<option value="fade" <?php if(get_option('wpt_effect')=='fade'){echo 'selected="selected"';}?>>fade</option>
				<option value="scrollUp" <?php if(get_option('wpt_effect')=='scrollUp'){echo 'selected="selected"';}?>>scrollUp</option>
				<option value="scrollDown" <?php if(get_option('wpt_effect')=='scrollDown'){echo 'selected="selected"';}?>>scrollDown</option>
				<option value="scrollRight" <?php if(get_option('wpt_effect')=='scrollRight'){echo 'selected="selected"';}?>>scrollRight</option>
				
				<option value="scrollLeft" <?php if(get_option('wpt_effect')=='scrollLeft'){echo 'selected="selected"';}?>>scrollLeft</option>
				
				<option value="shuffle" <?php if(get_option('wpt_effect')=='shuffle'){echo 'selected="selected"';}?>>shuffle</option>
				</select>
				</td>
				
			</tr>	
			<tr>
				<th><?php echo 'Speed:';?></th>
				<td>
					<input type="text" id="wpt_speed" name="wpt_speed" value="<?php echo esc_attr(get_option('wpt_speed')); ?>" size="5" placeholder="5000"/>ms 
				</td>
			</tr>
			
			<tr>
				<th><?php echo 'View All:';?></th>
				<td>
					<input type="checkbox" id="wpt_viewall" name="wpt_viewall" <?php if(get_option('wpt_viewall')!=''):echo 'checked="checked"';endif; ?> size="5" value="1"/>Show the "View All" links in testiomonial sidebar
					<?php  if(get_option('wpt_viewall')!=''):?><br>
						<input type="text" id="wpt_viewall_page" name="wpt_viewall_page" value="<?php echo esc_attr(get_option('wpt_viewall_page')); ?>" size="25" placeholder="Enter testiomonals list page url"/><br>
					<?php endif;?>
				</td>
			</tr>
<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
		</table>
	</div>
	
	<!-- Advance Setting -->	
	<div class="first wpt-tab" id="div-wpt-advance">
	<h2>Advance Settings</h2>
		<table>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr>
				<th><?php echo 'Sort By:';?></th>
				<td>
				<select id="wpt_sortby" name="wpt_sortby" >
				<option value="title" <?php if(get_option('wpt_sortby')=='title'){echo 'selected="selected"';}?>>Title</option>
				<option value="date" <?php if(get_option('wpt_sortby')=='date'){echo 'selected="selected"';}?>>Date</option>
				</select>
				</td>
			</tr>
			<tr>
				<th><?php echo 'Order By:';?></th>
				<td>
				<select id="wpt_orderby" name="wpt_orderby" >
				<option value="ASC" <?php if(get_option('wpt_orderby')=='ASC'){echo 'selected="selected"';}?>>ASC</option>
				<option value="DESC" <?php if(get_option('wpt_orderby')=='DESC'){echo 'selected="selected"';}?>>DESC</option>
				</select>
				</td>
			</tr>
			<tr>
				<td><?php echo 'Content Limit (Rotator):';?></td>
				<td>
					<input type="text" id="wpt_content_limit" name="wpt_content_limit" value="<?php echo esc_attr(get_option('wpt_content_limit')); ?>" size="30" placeholder="400"/>
				</td>
			</tr>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>	
		</table>
	</div>
	
	<!-- Shortcode Setting -->	
	<div class="first wpt-tab" id="div-wpt-shortcode">
	<h2>Shortcode</h2>
		<table>
			<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			<tr><td colspan="3">[wpt_random] for add to testimonial rotator on any page/post</td></tr>
			<tr><td colspan="3">[wpt_testimonials] for publish all testimonials on a single page</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>		
		</table>
	</div>
	
	<!-- Support Setting -->	
	<div class="first wpt-tab" id="div-wpt-support">
		
	<h2>Plugin Support</h2>
	
	<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WN785E5V492L4" target="_blank" style="font-size: 17px; font-weight: bold;"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" title="Donate for this plugin"></a></p>
	
	<p><strong>Plugin Author:</strong><br><img src="<?php echo  plugins_url( 'images/raghu.jpg' , __FILE__ );?>" width="75" height="75"><br><a href="http://raghunathgurjar.wordpress.com" target="_blank">Raghunath Gurjar</a></p>
	<p><a href="mailto:raghunath.0087@gmail.com" target="_blank" class="contact-author">Contact Author</a></p>
	<p><strong>My Other Plugins:</strong><br>
	<ul>
		<li><a href="https://wordpress.org/plugins/protect-wp-admin/" target="_blank">Protect WP-Admin</a></li>
		<li><a href="https://wordpress.org/plugins/custom-share-buttons-with-floating-sidebar/" target="_blank">Custom Share Buttons With Floting Sidebar</a></li>
		<li><a href="https://wordpress.org/plugins/wp-easy-recipe/" target="_blank">WP Easy Recipe</a></li>
		<li><a href="https://wordpress.org/plugins/wp-social-buttons/" target="_blank">WP Social Buttons</a></li>
		<li><a href="https://wordpress.org/plugins/wp-youtube-gallery/" target="_blank">WP Youtube Gallery</a></li>
		</ul></p>
	</div>

	</div>
	</div>
	<?php echo get_submit_button('Save Settings','button-primary','submit','','');?>
    <?php settings_fields('wpt_testimonial_options'); ?>
	</form>
<!-- End Options Form -->
	</div>

<?php
}
// return the WP Testimonial Settings
	function get_wpt_testimonials_options() {
		global $wpdb;
		$ctOptions = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'wpt_%'");								
		foreach ($ctOptions as $option) {
			$ctOptions[$option->option_name] =  $option->option_value;
		}
		return $ctOptions;	
	}
	
/*
-----------------------------------------------------------------------------------------------
                        WP Testimonials Posts                              
-----------------------------------------------------------------------------------------------
*/


//Include Post files
include dirname( __FILE__ ) .'/lib/class-wpt-testimonial.php';



/*
-----------------------------------------------------------------------------------------------
                              WP Testimonials Rutator Widget  
-----------------------------------------------------------------------------------------------
*/

//Include Widget files
include dirname( __FILE__ ) .'/lib/wpt-testimonial-wdget.php';

/* *Delete the options during disable the plugins */
if( function_exists('register_uninstall_hook') )
	register_uninstall_hook(__FILE__,'wpt_testimonial_uninstall');   
//Delete all Custom Tweets options after delete the plugin from admin
function wpt_testimonial_uninstall(){
	delete_option('wpt_effect');
	delete_option('wpt_speed');
	delete_option('wpt_sortby');
	delete_option('wpt_orderby');
	delete_option('wpt_viewall');
	delete_option('wpt_viewall_page');
} 
?>
