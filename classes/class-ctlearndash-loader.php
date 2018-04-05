<?php
/**
 * Custom Template for LearnDash - Loader.
 *
 * @package Custom Template for LearnDash
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'CTLearnDash_Loader' ) ) {

	/**
	 * Loader Class for CTLearnDash
	 */
	class CTLearnDash_Loader {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 * Initiator
		 */
		public static function get_instance() {

			if ( ! class_exists( 'SFWD_LMS' ) ) {
				return false;
			}

			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		private function __construct() {
			$this->includes();
		}

		/**
		 * Include required files.
		 *
		 * @return void
		 */
		private function includes() {

			// Load the metabbox class only in admin.
			require_once CTLEARNDASH_DIR . 'admin/class-ctlearndash-admin.php';
			require_once CTLEARNDASH_DIR . 'classes/class-ctlearndash.php';
		}
	}
}

add_action( 'plugins_loaded', 'CTLearnDash_Loader::get_instance' );
