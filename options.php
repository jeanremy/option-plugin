<?php

/*
	HOW TO USE IN FUNCTIONS

	$options = get_option('_theme_options');
	define('FB_ID', $options['fb_id']);
	define('FB_API', $options['fb_app_id']);
	define('TWITTER_ID', $options['tw_id']);
	etc.
 */

/*
Based on 
http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
*/
function script_enqueue() {
	wp_enqueue_script('options_plugin_admin_js',plugins_url( 'js/options_plugin.js', __FILE__ ), array('jquery', 'plupload-all'),'', true);
}

add_action( 'admin_enqueue_scripts', 'script_enqueue' );

add_action('admin_init', 'options_plugin_init' );
add_action('admin_menu', 'options_plugin_add_page');

// Init plugin options to white list our options
function options_plugin_init(){
	register_setting( 'options_plugin', '_options_plugin', 'options_plugin_validate' );
}

// Add menu page
function options_plugin_add_page() {
	add_options_page('Options du site', 'Options du site', 'manage_options', 'options_plugin', 'options_plugin_page');

}


// Draw the menu page itself
function options_plugin_page() {	
	wp_enqueue_style('options_plugin_admin_css', plugins_url( 'css/options_plugin.css', __FILE__ ), array(),'', false);
	wp_enqueue_script('options_plugin_admin_js', plugins_url( 'js/options_plugin.js', __FILE__ ), array('jquery'),'', true);
	?>

	<div class="wrap">
		<h2>Options du site</h2>
		<form method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields('options_plugin'); ?>
			<?php $options = get_option('_options_plugin');?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Clé Api Facebook</th>
					<td><input type="text" class="widefat" id="fb_app_id" name="_options_plugin[fb_app_id]" value="<?php echo $options['fb_app_id']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">URL du Profil Facebook</th>
					<td><input type="text" class="widefat" id="fb_id" name="_options_plugin[fb_id]" value="<?php echo $options['fb_id']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Nom d'utilisateur Twitter</th>
					<td><input type="text" class="widefat" id="tw_id" name="_options_plugin[tw_id]" value="<?php echo $options['tw_id']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Page Pinterest</th>
					<td><input type="text" class="widefat" id="pin_id" name="_options_plugin[pin_id]" value="<?php echo $options['pin_id']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Clé Google Analytics</th>
					<td><input type="text" class="widefat" id="ga_id" name="_options_plugin[ga_id]" value="<?php echo $options['ga_id']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Téléphone (contact)</th>
					<td><input type="text" class="widefat" id="phone" name="_options_plugin[phone]" value="<?php echo $options['phone']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Adresse (contact)</th>
					<td><input type="text" class="widefat" id="adress" name="_options_plugin[adress]" value="<?php echo $options['adress']; ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Catalogue</th>
					<td>
						<?php if ($options['file_uploaded']): ?>
						<p class="file-uploaded"><?php echo $options['file_uploaded']; ?><span class="dashicons dashicons-yes"></span></p>
						<?php endif; ?>
							<input type="file" class="widefat" id="catalogue" name="catalogue"/>
					</td>
				</tr>


			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>

	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function options_plugin_validate($input) {
	
	// Say our second option must be safe text with no HTML tags
	$input['fb_app_id'] =  wp_filter_nohtml_kses($input['fb_app_id']);

	// Say our second option must be safe text with no HTML tags
	$input['fb_id'] =  wp_filter_nohtml_kses($input['fb_id']);

	// Say our second option must be safe text with no HTML tags
	$input['tw_id'] =  wp_filter_nohtml_kses($input['tw_id']);

	// Say our second option must be safe text with no HTML tags
	$input['pin_id'] =  wp_filter_nohtml_kses($input['pin_id']);

	// Say our second option must be safe text with no HTML tags
	$input['ga_id'] =  wp_filter_nohtml_kses($input['ga_id']);

	// Say our second option must be safe text with no HTML tags
	$input['phone'] =  wp_filter_nohtml_kses($input['phone']);
	// Say our second option must be safe text with no HTML tags
	$input['adress'] =  wp_filter_nohtml_kses($input['adress']);

	//Catalogue upload
	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

	$uploadedfile = $_FILES['catalogue'];
	$upload_overrides = array( 'test_form' => false );
	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
	if ( $movefile ) {
	    // Ote le chemin absolu de l'url, afin de ne pas stocker l'url en bdd, et evite les soucis pour site de tests, etc;
		// a récupérer, et à utiliser avec $upload['baseurl'] dans le thème. Définir $upload = $upload = wp_upload_dir();
		$upload = wp_upload_dir();
		$file = str_replace($upload['baseurl'], '', $movefile['url']);
	    $input['catalogue'] =   $file;
	    
	} else {
	    $input['file_uploaded'] =   'erreur d\'upload';
	}
	return $input;
}


/* PLUPLOAD */
// File Upload

add_action('wp_ajax_call_catalogue_upload_attach_file', 'catalogue_upload_attach_file');
add_action('wp_ajax_nopriv_call_catalogue_upload_attach_file', 'catalogue_upload_attach_file');
function catalogue_upload_attach_file(){
    $html = '';
    $errors = false;
    $messages = false;
    $check_ajax_referer = check_ajax_referer('_wpnonce', false);
    do {
        if(!$check_ajax_referer){
            $errors['AJAX_FAIL'] = __('Ajax fail...', 'theme');
            break;
        }
        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $wp_handle_upload = wp_handle_upload($_FILES['catalogue']);
        if (isset($wp_handle_upload['catalogue'])){
            $filetype = wp_check_filetype( $wp_handle_upload['catalogue']);
            if (in_array($filetype['ext'], array('pdf'))){
                //$messages['WP_HANDLE_UPLOAD'] = $wp_handle_upload['url'];
                $html = $wp_handle_upload['url'];
                break;
            } else {
                $errors['WP_HANDLE_UPLOAD_EXT'] = __('Problème avec l\'extension de votre fichier', 'theme');
            }
        } else {
            if(isset($wp_handle_upload['error'])){
                $errors['WP_HANDLE_UPLOAD_ERROR'] = $wp_handle_upload['error'];
            }else{
                foreach($wp_handle_upload as $key => $value){
                    $errors['WP_HANDLE_UPLOAD_ERROR_'.$key] = $key.' : '.$value;
                }
            }
        }
    
    } while(false);
    
    
    if($errors){
        $html.='<div class="alert alert-error">';
        $html.='<button class="close" data-dismiss="alert" type="button">×</button>';
        foreach($errors as $key => $value){
            $html.= '<p>'.$value.'</p>';
        }
        $html.='</div>';
    }
    echo $html;
    die();
}



