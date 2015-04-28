<?php
/**
 * Plugin Name: WordPress Option Page
 * Plugin URI: 
 * Description: Une page d'options dans la partie réglages de WordPress.
 * Version: 1.0
 * Author: JR
 * Author URI: https://github.com/jeanremy/
 * License: GPL2
 */


class WordPressOptionPage {

    public function __construct() {
        add_action('admin_init', array($this, 'wpop_options_init' ));
        add_action('admin_menu', array($this, 'wpop_options_add_page'));
        add_action('admin_enqueue_scripts', array($this, 'wpop_option_script'));
    }


	/****************
     * OPTIONS PAGE *
     ****************/
    public function wpop_option_script() {
        wp_enqueue_script('wpop_option_script', plugins_url( 'js/wpop-fileupload.js', __FILE__ ), array('jquery', 'plupload-all'),'', true);
    }

    // Init plugin options to white list our options
    public function wpop_options_init(){
        register_setting( 'wpop_theme_options', '_wpop_theme_options', array($this, 'wpop_theme_options_validate') );
    }

    // Add menu page
    public function wpop_options_add_page() {
        add_options_page('Options du site', 'Options du site', 'manage_options', 'wpop_theme_options', array($this, 'wpop_theme_options_page'));
    }

    // HTML output for theme options
    public function wpop_theme_options_page() {  
        wp_enqueue_style('wpop_admin_css', plugins_url( 'css/wpop-admin.css', __FILE__ ), array(),'', false);
        ?>

        <div class="wrap">
            <h2>Options du site</h2>
            <form method="post" action="options.php" enctype="multipart/form-data">
                <?php settings_fields('wpop_theme_options'); ?>
                <?php $options = get_option('_wpop_theme_options');?>
                <table class="form-table">

                    <tr valign="top">
                        <th scope="row">Pré-adresse</th>
                        <td><input type="text" class="widefat" id="pre-address" name="_wpop_theme_options[pre-address]" value="<?php echo $options['pre-address'] ? $options['pre-address']:''; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Adresse</th>
                        <td><input type="text" class="widefat" id="address" name="_wpop_theme_options[address]" value="<?php echo $options['address'] ? $options['address']:''; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Code Postal</th>
                        <td><input type="text" class="widefat" id="zipcode" name="_wpop_theme_options[zipcode]" value="<?php echo $options['zipcode'] ? $options['zipcode']:''; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Ville</th>
                        <td><input type="text" class="widefat" id="city" name="_wpop_theme_options[city]" value="<?php echo $options['city'] ? $options['city']:''; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Téléphone</th>
                        <td><input type="text" class="widefat" id="phone" name="_wpop_theme_options[phone]" value="<?php echo $options['phone'] ? $options['phone']:''; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Email</th>
                        <td><input type="email" class="widefat" id="email" name="_wpop_theme_options[email]" value="<?php echo $options['email'] ? $options['email']:''; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">URL de la page Linkedin</th>
                        <td><input type="text" class="widefat" id="linkedin" name="_wpop_theme_options[linkedin]" value="<?php echo $options['linkedin'] ? $options['linkedin']:''; ?>" /></td>
                    </tr>

                    <tr valign="top">
                        <th scope="row">Fichier attaché</th>
                        <td>
                            <input type="text" class="highlight-path" id="highlight" name="highlight" value="<?php //echo $options['highlight']; ?>" /><span class="dashicons dashicons-no-alt"></span>
                            <input type="file" id="highlight-upload" name="highlight-upload"/>
                        </td>
                    </tr>


                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'wpop') ?>" />
                </p>
            </form>
        </div>

        <?php   
    }

    // Sanitize and validate input. Accepts an array, return a sanitized array.
    public function wpop_theme_options_validate($input) {
        $input['pre-address'] =  strip_tags($input['pre-address']);
        $input['address'] =  wp_filter_nohtml_kses($input['address']);
        $input['zipcode'] =  wp_filter_nohtml_kses($input['zipcode']);
        $input['city'] =  wp_filter_nohtml_kses($input['city']);
        $input['phone'] =  wp_filter_nohtml_kses($input['phone']);
        $input['email'] =  wp_filter_nohtml_kses($input['email']);
        $input['linkedin'] =  wp_filter_nohtml_kses($input['linkedin']);
        $input['lat'] = wp_filter_nohtml_kses($input['lat']);
        $input['long'] = wp_filter_nohtml_kses($input['long']);

        Catalogue upload
        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

        if($_FILES['catalogue-upload']['size']  != 0) {
            $uploadedfile = $_FILES['catalogue-upload'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile ) {
                // Ote le chemin absolu de l'url, afin de ne pas stocker l'url en bdd, et evite les soucis pour site de tests, etc;
                // a récupérer, et à utiliser avec $upload['baseurl'] dans le thème
                $upload = wp_upload_dir();
                $file = str_replace($upload['baseurl'], '', $movefile['url']);
                $input['catalogue'] =   $file;
                
            } else {
                $input['catalogue'] =   'erreur d\'upload';
            }
        } else {
            $input['catalogue'] =   $_POST['catalogue'];
        }
        
        
        return $input;
    }


}

new WordPressOptionPage();