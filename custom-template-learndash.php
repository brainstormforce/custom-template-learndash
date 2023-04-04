<?php
/**
 * Plugin Name:     Custom Template for LearnDash
 * Plugin URI:      https://github.com/brainstormforce/custom-template-learndash
 * Description:     This plugin will help you replace default LearnDash course template for non-enrolled students with a custom template. You can design the custom template with any page builder of your choice.
 * Author:          Pratik Chaskar
 * Author URI:      https://pratikchaskar.com/
 * Text Domain:     custom-template-learndash
 * Domain Path:     /languages
 * Version:         1.0.6
 *
 * @package         Custom Template for LearnDash
 */

// Set Option to flush the rewrite rule after activation of plugin.
register_activation_hook( __FILE__, 'ctlearndash_activation' );

/**
 * Set option that states plugin is activated.
 *
 * @since 1.0.2
 * @return void
 */
function ctlearndash_activation() {
	add_option( 'ctlearndash_activation', 'is-activated' );
}

define( 'CTLEARNDASH_VER', '1.0.6' );
define( 'CTLEARNDASH_FILE', __FILE__ );
define( 'CTLEARNDASH_DIR', plugin_dir_path( __FILE__ ) );
define( 'CTLEARNDASH_URL', plugins_url( '/', __FILE__ ) );
define( 'CTLEARNDASH_PATH', plugin_basename( __FILE__ ) );

require_once CTLEARNDASH_DIR . 'classes/class-ctlearndash-loader.php';
