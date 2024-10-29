<?php
/**
 * Admin class
 *
 * @package ajax-for-all
 * @subpackage admin
 * @since 0.2
 */
class AjaxForAllAdmin extends AjaxForAll {
	/**
	 * PHP 4 Style constructor which calls the below PHP5 Style Constructor
	 *
	 * @since 0.2
	 * @return none
	 */
	function AjaxForAllAdmin() {
		$this->__construct();
	}

	/**
	 * Setup backend functionality in WordPress
	 *
	 * @return none
	 * @since 0.2
	 */
	function __construct () {
		AjaxForAll::__construct ();

		// Load localizations if available
		load_plugin_textdomain ( 'ajax-for-all' , false , 'ajax-for-all/translations' );

		// Activation hook
		register_activation_hook ( $this->plugin_file , array ( &$this , 'activate' ) );

		// Whitelist options
		add_action ( 'admin_init' , array ( &$this , 'register_settings' ) );

		// Activate the options page
		add_action ( 'admin_menu' , array ( &$this , 'add_page' ) ) ;

		// Enable ajax handler
		add_action( 'wp_ajax_ajax_for_all', array( &$this, 'ajax' ) );
		add_action( 'wp_ajax_nopriv_ajax_for_all', array( &$this, 'ajax' ) );
	}

	/**
	 * Whitelist the AjaxForAll options
	 *
	 * @since 0.2
	 * @return none
	 */
	function register_settings () {
		register_setting( 'ajax-for-all_options' , 'ajax-for-all' );
	}

	/**
	 * Return plugin default config
	 *
	 * @since 0.2
	 * @return array
	 */
	function defaults () {
		$defaults = array (
				'version'    => '0.5.2',
				'id'         => 'content',
				'domain'     => '',
				'css'        => true,
				'forcesize'  => true,
				'admin_only' => true,
				'homelink'   => true,
				'scrolltop'  => true,
				'scrolltime' => 1500,
				'transition' => 'slide',
				'transtime'  => 2000,
				'nodeeplink' => false
		);
		return $defaults;
	}

	/**
	 * Initialize the default options during plugin activation
	 *
	 * @return none
	 * @since 0.2
	 */
	function activate() {
		if ( version_compare( PHP_VERSION, '5.2.0', '<' ) ) {
			deactivate_plugins( $this->plugin_file ); // Deactivate ourself
			wp_die( "Sorry, but this plugin requires PHP 5.2 or higher." );	
		}

		if ( !get_option ( 'ajax-for-all' ) )
			add_option ( 'ajax-for-all' , $this->defaults() );
		$this->check_upgrade();
	}

	/**
	 * Check if upgrade is necessary
	 *
	 * @since 0.4
	 * @return none
	 */
	function check_upgrade() {
		$defaults = $this->defaults();
		if ( version_compare( $defaults['version'], $this->get_option( 'version' ), '>' ) )
			$this->do_upgrade();
	}

	/**
	 * Upgrade
	 *
	 * @since 0.4
	 * @return none
	 */
	function do_upgrade() {
		$new = $this->defaults();
		$old = get_option( 'ajax-for-all' );
		if ( $old['id'] != 'content' )
			$new['id'] = $old['id'];
		if ( $old['admin_only'] == 'on' )
			$new['admin_only'] = 'on';
		if ( $old['homelink'] != 'on' )
			$new['homelink'] = '';
		update_option( 'ajax-for-all', $new );
	}

	/**
	 * Add the options page
	 *
	 * @return none
	 * @since 0.2
	 */
	function add_page() {
		if ( current_user_can ( 'manage_options' ) && function_exists ( 'add_options_page' ) ) {
			$options_page = add_options_page ( __( 'Ajax For All' , 'ajax-for-all' ) , __( 'Ajax For All' , 'ajax-for-all' ) , 'manage_options' , 'ajax-for-all' , array ( &$this , 'admin_page' ) );
			add_action( 'admin_head-' . $options_page, array( &$this, 'css' ) );
			add_filter( 'ozh_adminmenu_icon_ajax-for-all', array ( &$this , 'icon' ));
		}
	}

	/**
	 * Load admin CSS style
	 *
	 * @since 0.2
	 * @todo isn't there some admin enqueue style function?
	 */
	function css() { ?>
		<link rel="stylesheet" href="<?php echo WP_PLUGIN_URL . '/ajax-for-all/css/admin.css?v=0.2' ?>" type="text/css" media="all" /> <?php
	}

	/**
	 * Return admin menu icon
	 *
	 * @return string path to icon
	 *
	 * @since 0.2
	 */
	function icon() {
		$url = $this->plugin_url();
		$url .= '/images/transmit_blue.png';
		return $url;
	}

	/**
	 * The main ajax handler. Parse the ajax request, fetch remote content, return
	 *
	 * @param none
	 *
	 * @return mixed array with misc. info
	 *
	 * @since 0.1
	 *
	 * @todo pass request, cookie etc parameters
	 * @todo fail if POST, can we pass that on?
	 */
	function ajax() {
		$success	= false;
		$jump		= false;
		$jumpto		= false;
		$url		= filter_var( $_REQUEST['href'] );
		$user		= filter_var( $_REQUEST['user'] );
		$nonce		= filter_var( $_REQUEST['nonce'] );
		if ( // @todo functions
			strpos( $url, get_bloginfo( 'url' ) ) === 0
			&& strpos( $url, '/wp-admin/' ) === false
		) {
			$success	= true;
			$content	= $this->fetch_url( $url, $user, $nonce );
			$content	= $this->extract_id( $content, $this->get_option( 'id' ) );
			$parsed		= parse_url( $url );
			if ( $parsed['fragment'] ) {
				$jump	= true;
				$jumpto	= $parsed['fragment'];
			}
			if ( !$content )
				$success = false;
		}
		$return		= json_encode(
			array(
				'success'	=> $success,
				'content'	=> $content,
				'href'		=> $url,
				'jump'		=> $jump,
				'jumpto'	=> $jumpto,
			)
		);
		die( $return );
	}
	
	/**
	 * Fetch remote content
	 *
	 * @param string $url the link to fetch
	 *
	 * @return string HTML content
	 *
	 * @since 0.1
	 *
	 * @todo request, cookie etc
	 */
	function fetch_url( $url, $user, $nonce ) {
		// add user / nonce parameters
		$parsed_url = parse_url( $url );
		$sep = '?';
		if ( $parsed_url['query'] )
			$sep = '&amp;';
		if ( $user )
			$url .= $sep . "ajax_for_all_curl_user=$user&ajax_for_all_curl_nonce=$nonce";

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		// This is for my encrypted and password-protected dev server
		if ( defined( 'AFA_HTTP_CREDENTIALS' ) ) {
			curl_setopt( $ch, CURLOPT_USERPWD, AFA_HTTP_CREDENTIALS );
			curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		$result = curl_exec ( $ch );
		$response = curl_getinfo( $ch );
		curl_close ( $ch );
		return $result;
	}
	
	/**
	 * Extract an id from an HTML document
	 *
	 * @param string $content website's HTML content
	 *
	 * @return string one id's HTML content
	 *
	 * @since 0.1
	 */
	function extract_id( $content, $id ) {
		// thanks http://codjng.blogspot.com/2009/10/unicode-problem-when-using-domdocument.html
		if ( function_exists( 'mb_convert_encoding' ) )
			$content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'); // require mb_string
		$dom= new DOMDocument();
		error_reporting( 0 );
		$dom->loadHTML( $content );
		error_reporting( 1 );
		$dom->preserveWhiteSpace = false;
	
		$element = $dom->getElementById( $id );
		$innerHTML = $this->innerHTML( $element );
		return( $innerHTML ); 
	}
	
	/**
	 * Helper, returns the inner HTML of an element
	 *
	 * @param object DOMElement (i think)
	 *
	 * @return string one id's HTML content
	 *
	 * @since 0.1
	 */
	function innerHTML( $contentdiv ) {
		$els = $contentdiv->childNodes;
		foreach( $els as $el ) { 
			if ( $el->nodeType == XML_TEXT_NODE ) {
				$text = $el->nodeValue;
				$text = str_replace( '<', '&lt;', $text );
				$r .= $text;
			}
			// FIXME we should return comments
			elseif ( $el->nodeType == XML_COMMENT_NODE ) {
				$r .= '';
			}
			else {
				$r .= '<';
				$r .= $el->nodeName;
				if ( $el->hasAttributes() ) { 
					$atts = $el->attributes;
					foreach ( $atts as $att )
						$r .= " {$att->nodeName}='{$att->nodeValue}'" ;
				}	
				$r .= '>';
				$r .= $this->innerHTML( $el );
				$r .= "</{$el->nodeName}>";
			}	
		}
		return $r;
	}

	/**
	 * Output the options page
	 *
	 * @return none
	 * @since 0.2
	 */
	function admin_page () { ?>
		<div id="nkuttler" class="wrap" >
			<h2><?php _e( 'Ajax For All', 'ajax-for-all' ) ?></h2> <?php
			require_once( 'nkuttler.php' );
			nkuttler0_2_4_links( 'ajax-for-all', 'http://www.nkuttler.de/wordpress-plugin/automatic-ajax-for-wordpress-plugin/' ) ?>

			<form method="post" action="options.php"> <?php
				settings_fields( 'ajax-for-all_options' ); ?>
				<input type="hidden" name="ajax-for-all[version]" value="<?php echo $this->get_option( 'version' ) ?>" />
				<table class="form-table form-table-clearnone" >

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Admin only mode", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="checkbox" name="ajax-for-all[admin_only]" <?php
								if ( $this->options['admin_only'] == true )
									echo ' checked="checked"'; ?>
							/> <?php _e( "The admin only mode is the default so that you can test if the plugin works with your site. Disable it so that your visitors can use the Ajax feature. You might want to disable the link to the plugin's page in your site's footer before doing this.", 'ajax-for-all' ) ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "ID to replace", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="text" name="ajax-for-all[id]" value="<?php echo $this->options['id']; ?>" size="15" />
							<?php _e( "The default 'content' should work with most themes.", 'ajax-for-all' ) ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Which transition?", 'ajax-for-all' ) ?>
						</th>
						<td> <?php
							$choices = array(
								'none'	=> __( 'No transition', 'ajax-for-all' ),
								'slide'	=> __( 'Slide up/down', 'ajax-for-all' ),
								'fade'	=> __( 'Fade out/in', 'ajax-for-all' ),
							);
							echo '<select name="ajax-for-all[transition]">';
							foreach ( $choices as $choice => $label ) {
								$selected = '';
								if ( $choice == $this->get_option( 'transition' ) )
									$selected = ' selected="selected" ';
								echo "<option value=\"$choice\" $selected>$label</option>";
							}
							echo '</select>';
							_e( 'Pick one of the available transitions', 'ajax-for-all' ); ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Transition time?", 'ajax-for-all' ) ?>
						</th>
						<td> <?php
							$choices = array(
								500,
								1000,
								1500,
								2000,
								2500,
							);
							echo '<select name="ajax-for-all[transtime]">';
							foreach ( $choices as $choice ) {
								$selected = '';
								if ( $choice == $this->get_option( 'transtime' ) )
									$selected = ' selected="selected" ';
								echo "<option value=\"$choice\" $selected>$choice</option>";
							}
							echo '</select>'; ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Scroll time?", 'ajax-for-all' ) ?>
						</th>
						<td> <?php
							$choices = array(
								0,
								500,
								1000,
								1500,
								2000,
								2500,
							);
							echo '<select name="ajax-for-all[scrolltime]">';
							foreach ( $choices as $choice ) {
								$selected = '';
								if ( $choice == $this->get_option( 'scrolltime' ) )
									$selected = ' selected="selected" ';
								echo "<option value=\"$choice\" $selected>$choice</option>";
							}
							echo '</select>';
							_e( 'Time to scroll up/towards anchors', 'ajax-for-all' ) ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Scroll to the top when a link is clicked?", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="checkbox" name="ajax-for-all[scrolltop]" <?php
								if ( $this->options['scrolltop'] == true )
									echo ' checked="checked"'; ?>
							size="25" /> <?php
							_e( 'If you disable this setting make sure that long pages work properly. You probably want to re-style the spinner.', 'ajax-for-all' ) ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Disable jumps to deep links?", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="checkbox" name="ajax-for-all[nodeeplink]" <?php
								if ( $this->options['nodeeplink'] == true )
									echo ' checked="checked"'; ?>
							size="25" /> <?php
							_e( 'You might want to disable jumping to deep links if this conflicts with some other plugin you\'re using.', 'ajax-for-all' ) ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Use plugin CSS", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="checkbox" name="ajax-for-all[css]" <?php
								if ( $this->options['css'] == true )
									echo ' checked="checked"'; ?>
							size="25" />
							<?php _e( 'Disable this if your layout breaks. But you will need to style the spinner yourself then.', 'ajax-for-all' ) ?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Use width/height-fixed placeholder?", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="checkbox" name="ajax-for-all[forcesize]" <?php
								if ( $this->options['forcesize'] == true )
									echo ' checked="checked"'; ?>
							size="25" />
							<?php _e('This prevents sidebars/footers etc from jumping around too much because of the transitions. Disable if you use no transitions.', 'ajax-for-all') ?>
						</td>
					</tr>

					<!--
					<tr valign="top">
						<th scope="row"> <?php
							_e( "Limit to domain", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="text" name="ajax-for-all[domain]" value="<?php echo $this->options['domain']; ?>" size="25" /> Do not enter anything here unless you know what you are doing.
						</td>
					</tr>
					-->

					<tr valign="top">
						<th scope="row"> <?php
							_e( "Link to the plugin page in your footer?", 'ajax-for-all' ) ?>
						</th>
						<td>
							<input type="checkbox" name="ajax-for-all[homelink]" <?php
								if ( $this->options['homelink'] == true )
									echo ' checked="checked"'; ?>
							/> Please consider blogging about this plugin or making a donation if you don't want the link.
						</td>
					</tr>

				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>

		</div> <?php
	}
}
