<?php 
/*
Plugin Name: Wordpress Pretty Testimonial
Plugin URI: http://raihanb.com/premium/pretty-testimonial-2/
Description: This plugin will enable pretty testimonial in your WordPress site. You can change lot of options from <a href="options-general.php?page=pretty-testimonial-settings">Option Panel</a>
Author:Abu Sayed
Author URI:http://raihanb.com/premium/
Version: 1.0
*/

// This code enable for widget shortcode support
add_filter('widget_text', 'do_shortcode');

/* Adding Latest jQuery from WordPress */
function wp_pretty_testimonial_free_wp_latest_jquery() {
	wp_enqueue_script('jquery');
}
add_action('init', 'wp_pretty_testimonial_free_wp_latest_jquery');


/* Admin tab Active*/
function free_tab_active() {?>
    
<script type="text/javascript">
	jQuery(document).ready(function(){
	   jQuery('#tab-container').easytabs();
	});	
</script> 
 
<?php    
}
add_action('wp_footer', 'free_tab_active');


/* Adding admin tab js */
define('PRETTY_TESTIMONIAL', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );

function admintab_functions() {
	wp_enqueue_script('pretty-testimonial-admin-tab', PRETTY_TESTIMONIAL.'js/admin_tab.js', array('jquery'));
}
add_action('admin_head', 'admintab_functions');


/* Adding pretty testimonial js file*/
function include_free_testimonial_file_js() {

	wp_enqueue_script( 'pretty-testimonial-modernizr', plugins_url( 'js/modernizr.js', __FILE__ ), array('jquery'));	
	wp_enqueue_script( 'pretty-testimonial-jquery', plugins_url( 'js/masonry.pkgd.min.js', __FILE__ ), array('jquery'));	
	wp_enqueue_script( 'pretty-testimonial-flexslider', plugins_url( 'js/jquery.flexslider-min.js', __FILE__ ), array('jquery'));
	wp_enqueue_script( 'pretty-testimonial-main', plugins_url( 'js/main.js', __FILE__ ), array('jquery'));
	
}
add_action('wp_enqueue_scripts', 'include_free_testimonial_file_js');


/* Adding pretty testimonial main css and custom css file*/
function include_free_testimonial_file_css() {

    wp_enqueue_style( 'pretty-testimonial-demo', plugins_url( '/css/demo.css', __FILE__ ));
    wp_enqueue_style( 'pretty-testimonial-style', plugins_url( '/css/style.css', __FILE__ ));
	wp_enqueue_style( 'pretty-testimonial-custom', plugins_url( '/css/custom.css', __FILE__ ));
	wp_enqueue_style( 'pretty-testimonial-tab', plugins_url( '/css/tab.css', __FILE__ ));
  	   
}
add_action('init', 'include_free_testimonial_file_css');


// Testimonial Shortcode
function free_testimonial_shortcode($atts){
global $pretty_testimonial_options; $pretty_testimonial_settings = get_option( 'pretty_testimonial_options', $pretty_testimonial_options );
	extract( shortcode_atts( array(

		 'id' =>'', 
		 'category' =>'', 
		 'posts_per_page' => $pretty_testimonial_settings['posts_per_page'],
		 'bg_color' =>$pretty_testimonial_settings['bg_color'],
		 'content_font_size' =>$pretty_testimonial_settings['content_font_size'],		 
		 'content_color' =>$pretty_testimonial_settings['content_color'],
		 'client_content_none' =>$pretty_testimonial_settings['client_content_none'],
		 'client_title_font_size' => $pretty_testimonial_settings['client_title_font_size'],		 
		 'client_title_color' => $pretty_testimonial_settings['client_title_color'],
		 'direction' => $pretty_testimonial_settings['direction'],
		
	), $atts, 'testimonial' ) );
	
    $q = new WP_Query(
       array( 'posts_per_page' =>$posts_per_page, 'post_type' =>'testimonial-items', 'testimonial_cat' =>$category, 'meta_key' => 'testimonial_order','orderby' => 'meta_value','order' => 'ASC')
        );		
			
	$list = '	
	
	<script type="text/javascript">
		jQuery(document).ready(function($){

				jQuery("#testimonial'.$id.'").flexslider({
					selector: ".cd-testimonials > li",
					animation: "slide",
					easing: "swing",
					direction: "'.$direction.'",
				});
		}); 	
	</script>
	
	<div style="background-color:'.$bg_color.'" id="testimonial'.$id.'" class="cd-testimonials-wrapper cd-container">
	<ul class="cd-testimonials">
	
	
	';
	
	while($q->have_posts()) : $q->the_post();
        $post_thumbnail = get_the_post_thumbnail ( get_the_ID(), 'post_thumbnail' );
		$list .= '

		<li>
		
			
			<div class="cd-author">
				'.$post_thumbnail.'
			</div>
			<p style="display:'.$client_content_none.';color:'.$content_color.';font-size:'.$content_font_size.'px">'.get_the_content().'</p>
				<ul class="cd-author-info">
					<li style="color:'.$client_title_color.';font-size:'.$client_title_font_size.'px;">'.get_the_title().'</li>
				</ul>
		</li>
		
		 
		';        
	endwhile;
	$list.= ' </ul><a href="#0" class="cd-see-all">Show All</a></div>  
 <div class="cd-testimonials-all"> <div class="cd-testimonials-all-wrapper"> <ul> 


	';
	
	
	while($q->have_posts()) : $q->the_post();
		$post_thumbnail = get_the_post_thumbnail ( get_the_ID(), 'post_thumbnail' );		
		$list .= '
				
			<li class="cd-testimonials-item">
				<p style="color:'.$content_color.'">'.get_the_content().'</p>
				
				<div class="cd-author after_view_author">
					'.$post_thumbnail.'
					<ul class="cd-author-info">
						<li style="color:'.$client_title_color.';font-size:'.$client_title_font_size.'px;">'.get_the_title().'</li>
					</ul>
				</div>				
			</li>			
			<a href="#0" class="close-btn">Close</a>	
		';        
	endwhile;
	$list.= '
		</ul></div></div>
	';
	wp_reset_query();
	return $list;
}
add_shortcode('testimonial', 'free_testimonial_shortcode');



//add options framework
function add_free_free_pretty_testimonial_options_framework()  
{  
	add_options_page('Pretty Testimonial Options', 'Pretty Testimonial Options', 'manage_options', 'pretty-testimonial-settings','free_pretty_testimonial_options_framework');  
}  
add_action('admin_menu', 'add_free_free_pretty_testimonial_options_framework');

// add color picker
function free_pretty_testimonial_color_picker_function( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('js/color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'free_pretty_testimonial_color_picker_function' );


// Default options values
$pretty_testimonial_options = array(	
	'plugin_active_deactive' => 'block',
	'posts_per_page' => -1,
	'bg_color' => '#222222',
	'client_title_font_size' => '16',
	'client_title_color' => '#6b6b70',
	'content_font_size' => '17',
	'content_color' => '#6b6b70',
	'client_content_none' => 'block',
	'client_image_none' => '',
	'view_all_bg_none' => '',
	'cross_color' => '#6b6b70',
	'view_content_arrow_style' => 'style_one',
	'direction' => '',




);


if ( is_admin() ) : // Load only if we are viewing an admin page

function free_pretty_testimonial_register_settings() {
	// Register settings and call sanitation functions
	register_setting( 'pretty_testimonial_p_options', 'pretty_testimonial_options', 'free_pretty_testimonial_validate_options' );
}

add_action( 'admin_init', 'free_pretty_testimonial_register_settings' );


// After view client image and image top arrow
$view_content_arrow_style = array(
	'testimonial_style_one' => array(
		'value' => 'style_one',
		'label' => 'Client image right side'
	)
);


// Client Image Show or hide
$client_image_none = array(
	'client_image_block' => array(
		'value' => 'client_image_enable_disable',
		'label' => 'Hide Client Image'
	)
);

// Content Show or hide
$client_content_none = array(
	'client_content_yes' => array(
		'value' => 'client_content_enable_disable',
		'label' => 'Hide Client Content'
	)
);

// View all background Show or hide
$view_all_bg_none = array(
	'view_all_bg_none_yes' => array(
		'value' => 'view_all_bg_enable_disable',
		'label' => 'Hide View all'
	)
);


// Function to generate options page
function free_pretty_testimonial_options_framework() {
	global $pretty_testimonial_options, $view_content_arrow_style,$client_image_none,$client_content_none, $view_all_bg_none;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

<div class="wrap admin_panel">
	
<h1 style="margin-bottom:25px;font-size:20px;font-style:italic;text-decoration:underline">If you want this plugin with lot of options and features please go to <a style="font-size:28px" href="http://demo.plugime.com/pretty-testimonial"> this link </a> </h1>

<h1 style="margin-bottom:25px;font-size:20px;font-style:italic">Please go our plugins site there have lot more good plugins <a href="http://plugime.com">Click Here</a></h1>

<h4 style="font-style:italic">Where you want pretty testimonial ? just copy the shortcode and paste where you want the pretty testimonial &nbsp;&nbsp; [testimonial id="1" category="home" ] &nbsp;( atfirst you must create a category. Please go to pretty testimonial custom post and create category name then put your category name like  category="category name" then you give a unique id in shortcode like id="id number" )</h4>
	
	
	<h2>WordPress Pretty Testimonial</h2>

	<?php if ( false !== $_REQUEST['updated'] ) : ?>
	<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
	<?php endif; // If the form has just been submitted, this shows the notification ?>

	<form method="post" action="options.php">

	<?php $settings = get_option( 'pretty_testimonial_options', $pretty_testimonial_options ); ?>
	
	<?php settings_fields( 'pretty_testimonial_p_options' );
	/* This function outputs some hidden fields required by the form,
	including a nonce, a unique number used to ensure the form has been submitted from the admin page
	and not somewhere else, very important for security */ ?>


	
<div id="tab-container" class='tab-container rasel_option_panel'>
 <ul class='etabs'>
   <li class='tab'><a href="#basic_settings">Front Page Settings</a></li>
   <li class='tab'><a href="#view_settings">After View Settings</a></li>
 </ul>
 <div class='panel-container'>
  <div id="basic_settings">
   <h2>Front Page Settings</h2>	
	
	
	<table class="form-table margin_top"><!-- Grab a hot cup of coffee, yes we're using tables! -->
	
		<tr>
			<td align="center"><input type="submit" class="button-secondary default_settings_button" name="pretty_testimonial_options[default_settings]" value="Default settings" /><p class="font_size">If you want to default settings of plugin just click default settings button.</p></td>
			<td colspan="2"><input type="submit" class="button-primary" value="Save Options" /></td>
		</tr>		
	
		<tr valign="top">
			<th scope="row"><label for="posts_per_page">Number of post</label></th>
			<td>
				<input id="posts_per_page" type="number" style="width:80px;height:24px;padding-left:3px" name="pretty_testimonial_options[posts_per_page]" value="<?php echo isset($settings['posts_per_page']) ? stripslashes($settings['posts_per_page']) : ''; ?>" /><p class="description">If you put 4 the testimonial will show 4, If you put 5 the testimonial will show 5. Default post is -1 ( -1 mean unlimited post ).</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="direction">Testimonial Direction</label></th>
				<td>
				
				<?php
				global $pretty_testimonial_options; $pretty_testimonial_settings = get_option( 'pretty_testimonial_options', $pretty_testimonial_options );
				?>
				<select id="direction" name="pretty_testimonial_options[direction]">
				<?php
					// storing drop down value in a array 
					$direction = array ('horizontal','vertical');
					foreach( $direction as $item ):?>
					<option value="<?php echo $item; ?>" <?php if($pretty_testimonial_settings['direction'] == $item){ echo 'selected="selected"'; } ?>><?php echo $item; ?></option>
				<?php endforeach; ?>	
				</select>
				<p class="description">Select testimonial direction. Here is two types direction 1.Horizontal, 2.Vertical. If you select "horizontal" the testimonial slide left and right, If you select "vertical" the testimonial slide top and bottom. Default Testimonial Direction is "horizontal".</p>
			</td>
		</tr>		
		
		<tr valign="top">
			<th scope="row"><label for="bg_color">Background color</label></th>
			<td>
				<input id="bg_color" type="text" class="my-color-field" name="pretty_testimonial_options[bg_color]" value="<?php echo isset($settings['bg_color']) ? stripslashes($settings['bg_color']) : ''; ?>" /><p class="description">Choose testimonial Background color. Default color is #222222.</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="client_title_font_size">Title Font Size</label></th>
			<td>
				<input id="client_title_font_size" type="number" style="width:80px;height:24px;padding-left:3px" name="pretty_testimonial_options[client_title_font_size]" value="<?php echo isset($settings['client_title_font_size']) ? stripslashes($settings['client_title_font_size']) : ''; ?>" /><span style="padding-left:3px">px</span><p class="description">Put testimonial title font size. Default font size is 16px.</p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="client_title_color">Title color</label></th>
			<td>
				<input id="client_title_color" type="text" class="my-color-field" name="pretty_testimonial_options[client_title_color]" value="<?php echo isset($settings['client_title_color']) ? stripslashes($settings['client_title_color']) : ''; ?>" /><p class="description">Choose testimonial title color. Default color is #6b6b70.</p>
			</td>
		</tr>			

		<tr valign="top">
			<th scope="row"><label for="content_font_size">Content Font Size</label></th>
			<td>
				<input id="content_font_size" type="number" style="width:80px;height:24px;padding-left:3px" name="pretty_testimonial_options[content_font_size]" value="<?php echo isset($settings['content_font_size']) ? stripslashes($settings['content_font_size']) : ''; ?>" /><span style="padding-left:3px">px</span><p class="description">Put testimonial content font size. Default font size is 17px.</p>
			</td>
		</tr>
						
		<tr valign="top">
			<th scope="row"><label for="content_color"> Content color</label></th>
			<td>
				<input id="content_color" type="text" class="my-color-field" name="pretty_testimonial_options[content_color]" value="<?php echo isset($settings['content_color']) ? stripslashes($settings['content_color']) : ''; ?>" /><p class="description">Choose testimonial content color. Default color is #6b6b70.</p>
			</td>
		</tr>	
		
		<tr valign="top">
			<th scope="row"><label for="client_content_none"> Hide content</label></th>
			<td>
				<?php foreach( $client_content_none as $activate ) : ?>
				<input type="checkbox" id="<?php echo $activate['value']; ?>" name="pretty_testimonial_options[client_content_none]" 
				value="<?php esc_attr_e( $activate['value'] ); ?>" <?php checked( $settings['client_content_none'], $activate['value'] ); ?> />
				<label for="<?php echo $activate['value']; ?>"><?php echo $activate['label']; ?></label><br />
				<?php endforeach; ?>
			<p class="description">Hide or Show content. If you select then save it testimonial content will hide. If you deselect it testimonial content will show.</p>
			</td>
		</tr>
		
		<tr valign="top">
			<th scope="row"><label for="client_image_none"> Hide Client image</label></th>
			<td> 
				<?php foreach( $client_image_none as $activate ) : ?>
				<input type="checkbox" id="<?php echo $activate['value']; ?>" name="pretty_testimonial_options[client_image_none]" 
				value="<?php esc_attr_e( $activate['value'] ); ?>" <?php checked( $settings['client_image_none'], $activate['value'] ); ?> />
				<label for="<?php echo $activate['value']; ?>"><?php echo $activate['label']; ?></label><br />
				<?php endforeach; ?>
			<p class="description">Show or Hide Client image.If you select then save it testimonial client image will hide, If you deselect it testimonial client image will show.</p>
			</td>
		</tr>	

		<tr valign="top">
			<th scope="row"><label for="view_all_bg_none"> Hide View all</label></th>
			<td> 
				<?php foreach( $view_all_bg_none as $activate ) : ?>
				<input type="checkbox" id="<?php echo $activate['value']; ?>" name="pretty_testimonial_options[view_all_bg_none]" 
				value="<?php esc_attr_e( $activate['value'] ); ?>" <?php checked( $settings['view_all_bg_none'], $activate['value'] ); ?> />
				<label for="<?php echo $activate['value']; ?>"><?php echo $activate['label']; ?></label><br />
				<?php endforeach; ?>
			<p class="description">Show or Hide view all.If you select then save it testimonial view all will hide, If you deselect it testimonial view all will show.</p>
			</td>
		</tr>			
			

	</table>
  </div>		

  
  
  <!-- After View Settings -->
  <div id="view_settings">
	
	<table class="form-table margin_top">
		<h2>After View Settings</h2> 
		
		<tr>
			<td align="center"><input type="submit" class="button-secondary default_settings_button" name="pretty_testimonial_options[default_settings]" value="Default settings" /><p class="font_size">If you want to default settings of plugin just click default settings button.</p></td>
			<td colspan="2"><input type="submit" class="button-primary" value="Save Options" /></td>
		</tr>			

		<tr valign="top">
			<th scope="row"><label for="cross_color">Cross color</label></th>
			<td>
				<input id="cross_color" type="text" class="my-color-field" name="pretty_testimonial_options[cross_color]" value="<?php echo isset($settings['cross_color']) ? stripslashes($settings['cross_color']) : ''; ?>" /><p class="description">Choose cross color (after viewing the cross color). Default color is #6b6b70.</p>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="view_content_arrow_style"> Client image right side</label></th>
			<td>
				<?php foreach( $view_content_arrow_style as $activate ) : ?>
				<input type="checkbox" id="<?php echo $activate['value']; ?>" name="pretty_testimonial_options[view_content_arrow_style]" 
				value="<?php esc_attr_e( $activate['value'] ); ?>" <?php checked( $settings['view_content_arrow_style'], $activate['value'] ); ?> />
				<label for="<?php echo $activate['value']; ?>"><?php echo $activate['label']; ?></label><br />
				<?php endforeach; ?>
			<p class="description">If you select it after view client image will show right side,If you deselect it after view client image will show left side.</p>
			</td>
		</tr>		
			
		
	</table>

  </div>
  
  
 </div>
</div>
		
	<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>			

	</form>

</div>

	<?php
}

function free_pretty_testimonial_validate_options( $input ) {
	global $pretty_testimonial_options, $view_content_arrow_style, $client_image_none,  $client_content_none, $view_all_bg_none;

	$settings = get_option( 'pretty_testimonial_options', $pretty_testimonial_options );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS

	$input['posts_per_page'] = isset( $input['default_settings'] ) ? -1 : wp_filter_post_kses( $input['posts_per_page'] );
	$input['bg_color'] = isset( $input['default_settings'] ) ? '#222222' : wp_filter_post_kses( $input['bg_color'] );
	$input['client_title_color'] = isset( $input['default_settings'] ) ? '#6b6b70' : wp_filter_post_kses( $input['client_title_color'] );
	$input['client_title_font_size'] = isset( $input['default_settings'] ) ? '16' : wp_filter_post_kses( $input['client_title_font_size'] );
	$input['content_font_size'] = isset( $input['default_settings'] ) ? '17' : wp_filter_post_kses( $input['content_font_size'] );
	$input['content_color'] = isset( $input['default_settings'] ) ? '#6b6b70' : wp_filter_post_kses( $input['content_color'] );
	$input['client_content_none'] = isset( $input['default_settings'] ) ? 'block' : wp_filter_post_kses( $input['client_content_none'] );
	$input['client_image_none'] = isset( $input['default_settings'] ) ? '' : wp_filter_post_kses( $input['client_image_none'] );
	$input['view_all_bg_none'] = isset( $input['default_settings'] ) ? '' : wp_filter_post_kses( $input['view_all_bg_none'] );
	$input['cross_color'] = isset( $input['default_settings'] ) ? '#6b6b70' : wp_filter_post_kses( $input['cross_color'] );
	$input['view_content_arrow_style'] = isset( $input['default_settings'] ) ? 'style_one' : wp_filter_post_kses( $input['view_content_arrow_style'] );
	

	// We select the previous value of the field, to restore it in case an invalid entry has been given
	$prev = $settings['layout_only'];
	// We verify if the given value exists in the layouts array
	if ( !array_key_exists( $input['layout_only'], $view_content_arrow_style ) )
		$input['layout_only'] = $prev;
		
	// We select the previous value of the field, to restore it in case an invalid entry has been given
	$prev = $settings['layout_only'];
	// We verify if the given value exists in the layouts array
	if ( !array_key_exists( $input['layout_only'], $client_image_none ) )
		$input['layout_only'] = $prev;

	// We select the previous value of the field, to restore it in case an invalid entry has been given
	$prev = $settings['layout_only'];
	// We verify if the given value exists in the layouts array
	if ( !array_key_exists( $input['layout_only'], $client_content_none ) )
		$input['layout_only'] = $prev;		
			
	// We select the previous value of the field, to restore it in case an invalid entry has been given
	$prev = $settings['layout_only'];
	// We verify if the given value exists in the layouts array
	if ( !array_key_exists( $input['layout_only'], $view_all_bg_none ) )
		$input['layout_only'] = $prev;

		
	return $input;
}

endif;  // EndIf is_admin()




	//  Custom post title name
			
function change_free_testiminial_title( $title ){
     $screen = get_current_screen();
     if  ( 'testimonial-items' == $screen->post_type ) {
          $title = 'Enter Your Pretty Testimonial Title';
     }
     return $title;
}
add_filter( 'enter_title_here', 'change_free_testiminial_title' );

    //   Pretty testimonial custom post
            add_action( 'init', 'free_testimonial_custom_post' );
            function free_testimonial_custom_post() {
                    register_post_type( 'testimonial-items',
                            array(
                                    'labels' => array(
                                            'name' => __( 'Pretty Testimonial' ),
                                            'singular_name' => __( 'Pretty Testimonial' ),
                                            'add_new' => __( 'Add New Testimonial' ),
                                            'add_new_item' => __( 'Add New Pretty Testimonial' ),
                                            'edit_item' => __( 'Edit Pretty Testimonial' ),
                                            'new_item' => __( 'New Pretty Testimonial' ),
                                            'view_item' => __( 'View Pretty Testimonial' ),
                                            'not_found' => __( 'Sorry, we couldn\'t find the Pretty Testimonial you are looking for.' )
                                    ),
                            'public' => true,
                            'publicly_queryable' => false,
                            'show_in_admin_bar' => true,
                            'exclude_from_search' => true,
                            'menu_position' => 15,
                            'has_archive' => false,
                            'hierarchical' => false,
                            'capability_type' => 'page',
                            'rewrite' => array( 'slug' => 'testimonial' ),
                            'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields')
                            )
                    );
            }



			
	  //   Testimonial custom taxonomy
	 function free_pretty_testimonial_taxonomy() {
                    register_taxonomy('testimonial_cat', //the name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
	         'testimonial-items',                   //post type name
                           array(
                           'hierarchical'                =>true,
                           'label'                          =>'Testimonial Category',   //Display name
                           'query_var'                   =>true,
                           'show_admin_column'   =>true,
                           'rewrite'                        =>array(
                           'slug'                           =>'testimonial-category',  // This controls the base slug that will display before each term
						    
                          'with_front'                    =>false   // Don't display the category base before
                         )
                  )
           );

     }
   add_action(  'init', 'free_pretty_testimonial_taxonomy' );	 	


	
// Pretty testimonial some css
function free_pretty_testimonial_css() {?>

<?php global $pretty_testimonial_options; $pretty_testimonial_settings = get_option( 'pretty_testimonial_options', $pretty_testimonial_options ); ?>

<style type="text/css">

		<!-- After view client image and image top arrow -->
		<?php if ( $pretty_testimonial_settings['view_content_arrow_style'] =='style_one' ) : ?>
			<?php wp_enqueue_style( 'pretty-testimonial-client-image-right-side', plugins_url( 'css/client-image-arrow-right-side.css', __FILE__ ));  ?>
		<?php endif; ?>			

		<!-- Hide Client image -->
		<?php if ( $pretty_testimonial_settings['client_image_none'] =='client_image_enable_disable' ) : ?>
			<?php wp_enqueue_style( 'pretty-testimonial-client-image-hide', plugins_url( 'css/client-image-hide.css', __FILE__ ));  ?>
		<?php endif; ?>
		
		<!--  Hide Content -->
		<?php if ( $pretty_testimonial_settings['client_content_none'] =='client_content_enable_disable' ) : ?>
			<?php wp_enqueue_style( 'pretty-testimonial-client-image-hide', plugins_url( 'css/client-content-hide.css', __FILE__ ));  ?>
		<?php endif; ?>	

		<!--  Hide View all background -->
		<?php if ( $pretty_testimonial_settings['view_all_bg_none'] =='view_all_bg_enable_disable' ) : ?>
			<?php wp_enqueue_style( 'pretty-testimonial-view-all-hide', plugins_url( 'css/view-all-bg-hide.css', __FILE__ ));  ?>
		<?php endif; ?>	
	
		.flex-control-nav.flex-control-paging a{color:#fff;}	
		.flex-control-nav.flex-control-paging a {color:#6b6b70;}
		.flex-pauseplay {background:#252527;}
		.flex-pause, .flex-play {color:#6b6b70}
		.flex-control-nav.flex-control-paging > li {background-color:#252527;}
		
		.flex-direction-nav li{background-color:#252527}
		.flex-prev:hover{background-color:#1e1e21;}
		.flex-next:hover{background-color:#1e1e21;}
		.cd-testimonials-wrapper:after {color:#6b6b70;}
		.close-btn:before, .close-btn:after{background-color:<?php echo $pretty_testimonial_settings['cross_color']; ?>}
		.cd-see-all {background: <?php echo $pretty_testimonial_settings['view_all_bg_none']; ?>!important;}
		.cd-author-info {margin-top: 52px !important;}

</style> 

<?php
}
add_action('wp_head', 'free_pretty_testimonial_css');	





?>