<?php
/**
 * Frontend class
 *
 * @package ajax-for-all
 * @subpackage frontend
 * @since 0.2
 */

class AjaxForAllFrontend extends AjaxForAll {

	/**
	 * Full path to the directory for the custom avatars
	 *
	 * @return string
	 * @since 0.2
	 */

	var $avatars_dir;

	/**
	 * PHP 4 Style constructor which calls the below PHP5 Style Constructor
	 *
	 * @since 0.2
	 * @return none
	 */
	function AjaxForAllFrontend() {
		$this->__construct();
	}

	/**
	 * Hook into init
	 *
	 * @return none
	 * @since 0.2
	 */
	function __construct () {
		AjaxForAll::__construct ();

		add_action( 'init', array( &$this, 'init' ) );
	}

	/**
	 * Setup frontend 
	 *
	 * @return none
	 * @since 0.2
	 */
	function init() {
		/*
		$domain			= parse_url( $_SERVER['SERVER_NAME'] );
		$option_domain	= $this->get_option( 'domain' );
		if ( $option_domain === '' || $option_domain === $domain['path'] ) {
		*/
		if ( !$this->get_option( 'admin_only' ) || 
			( $this->get_option( 'admin_only' ) && current_user_can( 'activate_plugins' ) )
		) {
			$this->auto_login();
			$this->styles();
			$this->scripts();
			add_action( 'wp_footer', array( $this, 'homelink' ) );
		}
	}

	/**
	 * Auto login for verfied CURL requests
	 * This is necessary because the server who requests the content obviously
	 * isn't logged in.
	 *
	 * Yeah, we could do everything in JavaScript...
	 *
	 * @return none
	 * @since 0.2
	 */
	function auto_login() {
		if ( isset( $_GET['ajax_for_all_curl_user'] ) ) {
			$username	= $_GET['ajax_for_all_curl_user'];
			// log in automatically
			if ( !is_user_logged_in() ) {
				$user = get_userdatabylogin( $username );
				$user_id = $user->ID;
				wp_set_current_user( $user_id, $user_login );
				wp_set_auth_cookie( $user_id );
				do_action( 'wp_login', $user_login );
			}
			// die if nonce incorrect
			$nonce		= $_GET['ajax_for_all_curl_nonce'];
			if ( !wp_verify_nonce( $nonce, 'ajax_for_all_curl_nonce') ) {
				die( 'Security check' );
			}
		}
	}

	/**
	 * Add CSS styles
	 *
	 * @return none
	 * @since 0.2
	 */
	function styles() {
		if ( $this->get_option( 'css' ) ) {
			wp_register_style( 'ajax-for-all', $this->plugin_url() . '/css/ajax-for-all.css', false, '0.4' );
			wp_enqueue_style( 'ajax-for-all' );
		}
	}

	/**
	 * Add JavaScript and enable ajax handlers
	 *
	 * @return none
	 * @since 0.2
	 */
	function scripts() {
		wp_register_script(
			'ajax-for-all-bbq',
			$this->plugin_url() . '/js/jquery.ba-bbq.min.js',
			array( 'jquery' ),
			'0.5',
			true
		);
		wp_register_script(
			'ajax-for-all',
			$this->plugin_url() . '/js/ajax-for-all.js',
			array( 'jquery', 'ajax-for-all-bbq' ),
			'0.5',
			true
		);
		wp_enqueue_script( 'ajax-for-all' );
		add_action( 'wp_print_scripts', array( &$this, 'ajax_url' ) );
	}

	/**
	 * Add a link to the plugin homepage in the footer
	 *
	 * @return none
	 * @since 0.2
	 */
	function homelink() {
		if ( $this->get_option( 'homelink' ) && !$this->get_option('admin_only') ) { ?>
			<a href="http://www.nkuttler.de/2010/07/22/automatic-ajax-for-wordpress-plugin/">Ajax For All</a> <?php
		}
	}

	/**
	 * Print the admin-ajax.php url and more
	 * Set up curl auth info if user is logged in. We should really get the
	 * page through JS i guess...
	 *
	 * @since 0.1
	 */
	function ajax_url( ) {
		$force = 'false';
		if ( $this->get_option( 'forcesize' ) )
			$force = 'true';
		$scrolltop = 'false';
		if ( $this->get_option( 'scrolltop' ) )
			$scrolltop = 'true'; ?>
		<script type="text/javascript">
		<!--
			var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
			var afa_id = "<?php echo $this->get_option( 'id' ) ?>";
			var afa_nodeeplink = "<?php echo $this->get_option( 'nodeeplink' ) ?>";
			var afa_root = "<?php bloginfo( 'url' ); ?>/";
			var afa_preserve_size = <?php echo $force ?>;
			<?php
			if ( is_user_logged_in() ) {
				get_currentuserinfo();
				global $user_login;
				$nonce= wp_create_nonce( 'ajax_for_all_curl_nonce' );
			} ?>

			var ajax_for_all_curl_user = "<?php echo $user_login ?>";
			var ajax_for_all_curl_nonce = "<?php echo $nonce ?>";
			var ajax_for_all_transition = '<?php echo $this->get_option( 'transition' ) ?>';
			var ajax_for_all_transtime = <?php echo $this->get_option( 'transtime' ) ?>;
			var ajax_for_all_scrolltop = <?php echo $scrolltop ?>;
			var ajax_for_all_scrolltime = <?php echo $this->get_option( 'scrolltime' ) ?>;
		//-->
		</script> <?php
	}

}
