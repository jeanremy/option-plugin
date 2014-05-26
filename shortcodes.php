<?php 

/****************************
		SHORTCODES
****************************/

 // src: http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/

/* Creating shortcodes */
function shortcode_columns_full($atts, $content = null) {
    $html = '<div class="col-12"><p>';
    $html .= $content;
    $html .= '</p></div>';
    return $html;
}

function shortcode_columns_onehalf($atts, $content = null) {
    $html = '<div class="col-6"><p>';
    $html .= $content;
    $html .= '</p></div>';
    return $html;
}

function shortcode_columns_onethird($atts, $content = null) {
    $html = '<div class="col-4"><p>';
    $html .= $content;
    $html .= '</p></div>';
    return $html;
}
function shortcode_columns_twothirds($atts, $content = null) {
    $html = '<div class="col-8"><p>';
    $html .= $content;
    $html .= '</p></div>';
    return $html;
}
function shortcode_columns_onequarter($atts, $content = null) {
    $html = '<div class="col-3"><p>';
    $html .= $content;
    $html .= '</p></div>';
    return $html;
}
function shortcode_columns_threequarters($atts, $content = null) {
    $html = '<div class="col-9"><p>';
    $html .= $content;
    $html .= '</p></div>';
    return $html;
}

function register_shortcodes(){
  add_shortcode('full', 'shortcode_columns_full');
   add_shortcode('one-half', 'shortcode_columns_onehalf');
   add_shortcode('one-third', 'shortcode_columns_onethird');
   add_shortcode('two-thirds', 'shortcode_columns_twothirds');
   add_shortcode('one-quarter', 'shortcode_columns_onequarter');
   add_shortcode('three-quarters', 'shortcode_columns_threequarters');
}

add_action( 'init', 'register_shortcodes');

// MCE PART

function register_button( $buttons ) {
   array_push( $buttons,"columns" );
   return $buttons;
}

function add_plugin( $plugin_array ) {
   $plugin_array['columns'] = plugins_url('js/shortcode_plugin.js', __FILE__ );
   return $plugin_array;
}

function columns_button() {

   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
      return;
   }

   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'add_plugin' );
      add_filter( 'mce_buttons', 'register_button' );
   }

}

add_action('init', 'columns_button');


?>
