<?php
/**
 * Custom Template for LearnDash - Admin.
 *
 * @package Custom Template for LearnDash
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'CTLearnDash_Admin' ) ) {

	/**
	 * LearnDash Custom Template Initialization
	 *
	 * @since 1.0.0
	 */
	class CTLearnDash_Admin {


		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Activation hook.
			add_action( 'admin_init', array( $this, 'ctlearndash_initialize' ) );

			add_action( 'parent_file', array( $this, 'remove_unwanted_tabs' ) );
			add_action( 'init', array( $this, 'learndash_course_landing_page_post_type' ) );
			add_filter( 'post_updated_messages', array( $this, 'custom_post_type_post_update_messages' ) );

			add_action( 'admin_menu', array( $this, 'display_admin_menu' ) );
			add_action( 'parent_file', array( $this, 'active_admin_menu' ) );

			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );

			// Actions.
			add_filter( 'fl_builder_post_types', array( $this, 'bb_builder_compatibility' ), 10, 1 );
			add_filter( 'learndash_post_args', array( $this, 'course_settings_fields' ) );
			add_action( 'save_post', array( $this, 'save_course_landing_page' ) );
		}

		/**
		 * Remove LearnDash Tabs.
		 *
		 * @return void
		 */
		public function remove_unwanted_tabs() {

			global $parent_file, $current_screen, $submenu_file, $pagenow;
			if ( ( ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) && 'ld-custom-template' === $current_screen->post_type ) || 'edit-ld-custom-template' === $current_screen->id ) {
				remove_all_actions( 'learndash_admin_tabs_set' );
			}
		}

		/**
		 * Register Meta Boxes.
		 *
		 * @since  1.0.2
		 * @return void
		 */
		public function register_meta_boxes() {

			add_meta_box(
				'ctlearndash-help',
				__( 'Help', 'custom-template-learndash' ),
				array( $this, 'help_meta_box_callback' ),
				'ld-custom-template',
				'side'
			);
		}

		/**
		 * Help meta box markup.
		 *
		 * @since  1.0.2
		 * @return void
		 */
		public function help_meta_box_callback() {

			echo sprintf(
				/* translators: 1: anchor start, 2: anchor close */
				esc_html__( '%1$sSee list of all LearnDash shortcodes%2$s that you can add in the custom template.', 'custom-template-learndash' ),
				'<a href="https://www.learndash.com/support/docs/core/shortcodes-blocks/" target="_blank" rel="noopner" >',
				'</a>'
			);

			echo '<br/><br/>';

			echo sprintf(
				/* translators: 1: anchor start, 2: anchor close */
				esc_html__( 'How to %1$sget URL of the checkout pages%2$s to link it from custom template.', 'custom-template-learndash' ),
				'<a href="https://learndash.com/docs/link-learndash-checkout-flow-buy-button-landing-page-not-made-learndash/" target="_blank" rel="noopner" >',
				'</a>'
			);
		}


		/**
		 * Reset write rules when plugin is activated.
		 *
		 * @since 1.0.2
		 * @return void
		 */
		public function ctlearndash_initialize() {
			if ( is_admin() && get_option( 'ctlearndash_activation' ) === 'is-activated' ) {
					delete_option( 'ctlearndash_activation' );
					flush_rewrite_rules();
			}
		}

		/**
		 * Register custom landing page tab with the LLMS Course metabox
		 *
		 * @param    array $fields  existing fields.
		 * @return   array
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public function course_settings_fields( $fields ) {

			global $post;

			$all_posts = array(
				'none' => __( 'None', 'custom-template-learndash' ),
			);

			$atts = array(
				'post_type'      => 'ld-custom-template',
				'posts_per_page' => 500, //phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
				'fields'         => 'ids',
				'post_status'    => 'publish',
			);

			$posts = new WP_Query( $atts );

			if ( isset( $posts->posts ) ) {
				foreach ( $posts->posts as $key => $id ) {
					$all_posts[ $id ] = get_the_title( $id );
				}
			}

			$selected    = get_post_meta( get_the_ID(), 'learndash_course_template', true );
			$description = sprintf(
				/* translators: 1: anchor start, 2: anchor close */
				__( 'The selected custom template will replace default LearnDash course template for non-enrolled students. <br> If you have not done already, add new custom templates from %1$shere%2$s.', 'custom-template-learndash' ),
				'<a href="' . esc_url( admin_url( 'post-new.php?post_type=ld-custom-template' ) ) . '">',
				'</a>'
			);

			$fields['sfwd-courses']['fields']['learndash_course_template'] = array(
				'name'            => __( 'Select Custom Template for this Course', 'custom-template-learndash' ),
				'type'            => 'select',
				'initial_options' => $all_posts,
				'default'         => 'none',
				'help_text'       => $description,
				'show_in_rest'    => true,
				'rest_args'       => array(
					'schema' => array(
						'type' => 'string',
					),
				),
			);

			return $fields;
		}

		/**
		 * Admin Menu.
		 *
		 * @return void
		 */
		public function display_admin_menu() {

			add_submenu_page(
				'learndash-lms',
				__( 'Custom Templates', 'custom-template-learndash' ),
				__( 'Custom Templates', 'custom-template-learndash' ),
				'manage_options',
				'edit.php?post_type=ld-custom-template'
			);
		}

		/**
		 * Set Active Admin menu
		 *
		 * @return string
		 */
		public function active_admin_menu() {

			global $parent_file, $current_screen, $submenu_file, $pagenow;

			if ( ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) && 'ld-custom-template' === $current_screen->post_type ) :
				$submenu_file = 'edit.php?post_type=ld-custom-template'; // phpcs:ignore OVERRIDE OK.
				$parent_file  = 'learndash-lms'; // phpcs:ignore OVERRIDE OK.
			endif;

			return $parent_file;
		}

		/**
		 * Create LearnDash Custom Template custom post type
		 *
		 * @return void
		 */
		public function learndash_course_landing_page_post_type() {

			$labels = array(
				'name'          => esc_html_x( 'LearnDash Custom Templates', 'learndash course landing page general name', 'custom-template-learndash' ),
				'singular_name' => esc_html_x( 'LearnDash Custom Template', 'learndash course landing page singular name', 'custom-template-learndash' ),
				'search_items'  => esc_html__( 'Search LearnDash Custom Templates', 'custom-template-learndash' ),
				'all_items'     => esc_html__( 'All LearnDash Custom Templates', 'custom-template-learndash' ),
				'edit_item'     => esc_html__( 'Edit LearnDash Custom Template', 'custom-template-learndash' ),
				'view_item'     => esc_html__( 'View LearnDash Custom Template', 'custom-template-learndash' ),
				'add_new'       => esc_html__( 'Add New', 'custom-template-learndash' ),
				'update_item'   => esc_html__( 'Update LearnDash Custom Template', 'custom-template-learndash' ),
				'add_new_item'  => esc_html__( 'Add New', 'custom-template-learndash' ),
				'new_item_name' => esc_html__( 'New LearnDash Custom Template Name', 'custom-template-learndash' ),
			);
			$args   = array(
				'labels'              => $labels,
				'public'              => true,
				'show_ui'             => true,
				'query_var'           => true,
				'can_export'          => true,
				'show_in_menu'        => false,
				'show_in_admin_bar'   => true,
				'exclude_from_search' => true,
				'supports'            => apply_filters( 'learndash_course_landing_supports', array( 'title', 'editor', 'elementor' ) ),
			);

			register_post_type( 'ld-custom-template', apply_filters( 'learndash_course_landing_post_type_args', $args ) );
		}

		/**
		 * Add Update messages for any custom post type
		 *
		 * @param array $messages Array of default messages.
		 * @return array
		 */
		public function custom_post_type_post_update_messages( $messages ) {

			$custom_post_type = get_post_type( get_the_ID() );

			if ( 'ld-custom-template' === $custom_post_type ) {

				$obj                           = get_post_type_object( $custom_post_type );
				$singular_name                 = $obj->labels->singular_name;
				$messages[ $custom_post_type ] = array(
					0  => '', // Unused. Messages start at index 1.
					/* translators: %s: singular custom post type name */
					1  => sprintf( __( '%s updated.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					2  => sprintf( __( 'Custom %s updated.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					3  => sprintf( __( 'Custom %s deleted.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					4  => sprintf( __( '%s updated.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %1$s: singular custom post type name ,%2$s: date and time of the revision */
					5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'custom-template-learndash' ), $singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					/* translators: %s: singular custom post type name */
					6  => sprintf( __( '%s published.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					7  => sprintf( __( '%s saved.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					8  => sprintf( __( '%s submitted.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					9  => sprintf( __( '%s scheduled for.', 'custom-template-learndash' ), $singular_name ),
					/* translators: %s: singular custom post type name */
					10 => sprintf( __( '%s draft updated.', 'custom-template-learndash' ), $singular_name ),
				);
			}

			return $messages;
		}

		/**
		 * Add page builder support to Advanced hook.
		 *
		 * @param array $value Array of post types.
		 * @return array
		 */
		public function bb_builder_compatibility( $value ) {

			$value[] = 'ld-custom-template';

			return $value;
		}

		/**
		 * Save Course Landing Page Id.
		 *
		 * @param  int $post_id Current Post id.
		 * @return void
		 */
		public function save_course_landing_page( $post_id ) {

			$landing_page_id = ( isset( $_POST['course_template'] ) ) ? absint( $_POST['course_template'] ) : ''; //phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification

			update_post_meta( $post_id, 'course_template', $landing_page_id );
		}
	}
}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
CTLearnDash_Admin::get_instance();
