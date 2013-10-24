<?php
/**
 * Plugin Name: WordPress Starter
 * Plugin URI: http://wordpress.org/extend/plugins/wordpress-starter/
 * Description: TBD
 * Version: 0.0.1
 * Author: Michael Cannon
 * Author URI: http://aihr.us/about-aihrus/michael-cannon-resume/
 * License: GPLv2 or later
 */


/**
 * Copyright 2013 Michael Cannon (email: mc@aihr.us)
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
class WordPress_Starter {
	const ID          = 'wordpress-starter';
	const PLUGIN_FILE = 'wordpress-starter/wordpress-starter.php';
	const VERSION     = '0.0.1';

	private static $base;
	private static $post_types;

	public static $donate_button;
	public static $menu_id;
	public static $settings_link;


	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_shortcode( 'wordpress_starter_shortcode', array( $this, 'wordpress_starter_shortcode' ) );
		self::$base = plugin_basename( __FILE__ );
	}


	public function admin_init() {
		$this->update();
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		self::$donate_button = <<<EOD
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="WM4F995W9LHXE">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
EOD;

		self::$settings_link = '<a href="' . get_admin_url() . 'options-general.php?page=' . WordPress_Starter_Settings::ID . '">' . __( 'Settings', 'wordpress-starter' ) . '</a>';
	}


	public function admin_menu() {
		self::$menu_id = add_management_page( esc_html__( 'WordPress Starter Processer', 'wordpress-starter' ), esc_html__( 'WordPress Starter Processer', 'wordpress-starter' ), 'manage_options', self::ID, array( $this, 'user_interface' ) );

		add_action( 'admin_print_scripts-' . self::$menu_id, array( $this, 'scripts' ) );
		add_action( 'admin_print_styles-' . self::$menu_id, array( $this, 'styles' ) );

		add_screen_meta_link(
			'wps_settings_link',
			esc_html__( 'WordPress Starter Settings', 'wordpress-starter' ),
			admin_url( 'options-general.php?page=' . WordPress_Starter_Settings::ID ),
			self::$menu_id,
			array( 'style' => 'font-weight: bold;' )
		);
	}


	public function init() {
		add_action( 'wp_ajax_ajax_process_post', array( $this, 'ajax_process_post' ) );
		load_plugin_textdomain( self::ID, false, 'wordpress-starter/languages' );
		self::set_post_types();
	}


	public function plugin_action_links( $links, $file ) {
		if ( $file == self::$base ) {
			array_unshift( $links, self::$settings_link );

			$link = '<a href="' . get_admin_url() . 'tools.php?page=' . self::ID . '">' . esc_html__( 'Process', 'wordpress-starter' ) . '</a>';
			array_unshift( $links, $link );
		}

		return $links;
	}


	public function activation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	public function deactivation() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
	}


	public function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		global $wpdb;

		require_once 'lib/class-wordpress-starter-settings.php';
		$delete_data = wps_get_option( 'delete_data', false );
		if ( $delete_data ) {
			delete_option( WordPress_Starter_Settings::ID );
			$wpdb->query( 'OPTIMIZE TABLE `' . $wpdb->options . '`' );
		}
	}


	public static function plugin_row_meta( $input, $file ) {
		if ( $file != self::$base )
			return $input;

		$disable_donate = wps_get_option( 'disable_donate' );
		if ( $disable_donate )
			return $input;

		$links = array(
			'<a href="http://aihr.us/about-aihrus/donate/"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" alt="PayPal - The safer, easier way to pay online!" /></a>',
			'<a href="http://aihr.us/downloads/wordpress-starter-premium-wordpress-plugin/">Purchase WordPress Starter Premium</a>',
		);

		$input = array_merge( $input, $links );

		return $input;
	}


	public static function set_post_types() {
		$post_types       = get_post_types( array( 'public' => true ), 'names' );
		self::$post_types = array();
		foreach ( $post_types as $post_type )
			self::$post_types[] = $post_type;
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function user_interface() {
		// Capability check
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( $this->post_id, esc_html__( "Your user account doesn't have permission to access this.", 'wordpress-starter' ) );

?>

<div id="message" class="updated fade" style="display:none"></div>

<div class="wrap wpsposts">
	<div class="icon32" id="icon-tools"></div>
	<h2><?php _e( 'WordPress Starter Processer', 'wordpress-starter' ); ?></h2>

<?php
		if ( wps_get_option( 'debug_mode' ) ) {
			$posts_to_import = wps_get_option( 'posts_to_import' );
			$posts_to_import = explode( ',', $posts_to_import );
			foreach ( $posts_to_import as $post_id ) {
				$this->post_id = $post_id;
				$this->ajax_process_post();
			}

			exit( __LINE__ . ':' . basename( __FILE__ ) . " DONE<br />\n" );
		}

		// If the button was clicked
		if ( ! empty( $_POST[ self::ID ] ) || ! empty( $_REQUEST['posts'] ) ) {
			// Form nonce check
			check_admin_referer( self::ID );

			// Create the list of image IDs
			if ( ! empty( $_REQUEST['posts'] ) ) {
				$posts = explode( ',', trim( $_REQUEST['posts'], ',' ) );
				$posts = array_map( 'intval', $posts );
			} else {
				$posts = self::get_posts_to_process();
			}

			$count = count( $posts );
			if ( ! $count ) {
				echo '	<p>' . _e( 'All done. No posts needing processing found.', 'wordpress-starter' ) . '</p></div>';
				return;
			}

			$posts = implode( ',', $posts );
			$this->show_status( $count, $posts );
		} else {
			// No button click? Display the form.
			$this->show_greeting();
		}
?>
	</div>
<?php
	}


	public static function get_posts_to_process() {
		global $wpdb;

		$query = array(
			'post_status' => array( 'publish', 'private' ),
			'post_type' => self::$post_types,
			'orderby' => 'post_modified',
			'order' => 'DESC',
		);

		$include_ids = wps_get_option( 'posts_to_import' );
		if ( $include_ids ) {
			$query[ 'post__in' ] = str_getcsv( $include_ids );
		} else {
			$query['posts_per_page'] = 1;
			$query['meta_query']     = array(
				array(
					'key' => 'TBD',
					'value' => '',
					'compare' => '!=',
				),
			);
			unset( $query['meta_query'] );
		}

		$skip_ids = wps_get_option( 'skip_importing_post_ids' );
		if ( $skip_ids )
			$query[ 'post__not_in' ] = str_getcsv( $skip_ids );

		$results  = new WP_Query( $query );
		$query_wp = $results->request;

		$limit = wps_get_option( 'limit' );
		if ( $limit )
			$query_wp = preg_replace( '#\bLIMIT 0,.*#', 'LIMIT 0,' . $limit, $query_wp );
		else
			$query_wp = preg_replace( '#\bLIMIT 0,.*#', '', $query_wp );

		$posts = $wpdb->get_col( $query_wp );

		return $posts;
	}


	public function show_greeting() {
?>
	<form method="post" action="">
<?php wp_nonce_field( self::ID ); ?>

	<p><?php _e( 'Use this tool to process posts for TBD.', 'wordpress-starter' ); ?></p>

	<p><?php _e( 'This processing is not reversible. Backup your database beforehand or be prepared to revert each transformmed post manually.', 'wordpress-starter' ); ?></p>

	<p><?php printf( esc_html__( 'Please review your %s before proceeding.', 'wordpress-starter' ), self::$settings_link ); ?></p>

	<p><?php _e( 'To begin, just press the button below.', 'wordpress-starter' ); ?></p>

	<p><input type="submit" class="button hide-if-no-js" name="<?php echo self::ID; ?>" id="<?php echo self::ID; ?>" value="<?php _e( 'Process WordPress Starter', 'wordpress-starter' ) ?>" /></p>

	<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'wordpress-starter' ) ?></em></p></noscript>

	</form>
<?php
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function show_status( $count, $posts ) {
		echo '<p>' . esc_html__( 'Please be patient while this script run. This can take a while, up to a minute per post. Do not navigate away from this page until this script is done or the import will not be completed. You will be notified via this page when the import is completed.', 'wordpress-starter' ) . '</p>';

		echo '<p>' . sprintf( esc_html__( 'Estimated time required to import is %1$s minutes.', 'wordpress-starter' ), ( $count * 1 ) ) . '</p>';

		$text_goback = ( ! empty( $_GET['goback'] ) ) ? sprintf( __( 'To go back to the previous page, <a href="%s">click here</a>.', 'wordpress-starter' ), 'javascript:history.go(-1)' ) : '';

		$text_failures = sprintf( __( 'All done! %1$s posts were successfully processed in %2$s seconds and there were %3$s failures. To try importing the failed posts again, <a href="%4$s">click here</a>. %5$s', 'wordpress-starter' ), "' + rt_successes + '", "' + rt_totaltime + '", "' + rt_errors + '", esc_url( wp_nonce_url( admin_url( 'tools.php?page=' . self::ID . '&goback=1' ) ) . '&posts=' ) . "' + rt_failedlist + '", $text_goback );

		$text_nofailures = sprintf( esc_html__( 'All done! %1$s posts were successfully processed in %2$s seconds and there were no failures. %3$s', 'wordpress-starter' ), "' + rt_successes + '", "' + rt_totaltime + '", $text_goback );
?>

	<noscript><p><em><?php _e( 'You must enable Javascript in order to proceed!', 'wordpress-starter' ) ?></em></p></noscript>

	<div id="wpsposts-bar" style="position:relative;height:25px;">
		<div id="wpsposts-bar-percent" style="position:absolute;left:50%;top:50%;width:300px;margin-left:-150px;height:25px;margin-top:-9px;font-weight:bold;text-align:center;"></div>
	</div>

	<p><input type="button" class="button hide-if-no-js" name="wpsposts-stop" id="wpsposts-stop" value="<?php _e( 'Abort Processing Posts', 'wordpress-starter' ) ?>" /></p>

	<h3 class="title"><?php _e( 'Debugging Information', 'wordpress-starter' ) ?></h3>

	<p>
		<?php printf( esc_html__( 'Total Postss: %s', 'wordpress-starter' ), $count ); ?><br />
		<?php printf( esc_html__( 'Posts Processed: %s', 'wordpress-starter' ), '<span id="wpsposts-debug-successcount">0</span>' ); ?><br />
		<?php printf( esc_html__( 'Process Failures: %s', 'wordpress-starter' ), '<span id="wpsposts-debug-failurecount">0</span>' ); ?>
	</p>

	<ol id="wpsposts-debuglist">
		<li style="display:none"></li>
	</ol>

	<script type="text/javascript">
	// <![CDATA[
		jQuery(document).ready(function($){
			var i;
			var rt_posts = [<?php echo esc_attr( $posts ); ?>];
			var rt_total = rt_posts.length;
			var rt_count = 1;
			var rt_percent = 0;
			var rt_successes = 0;
			var rt_errors = 0;
			var rt_failedlist = '';
			var rt_resulttext = '';
			var rt_timestart = new Date().getTime();
			var rt_timeend = 0;
			var rt_totaltime = 0;
			var rt_continue = true;

			// Create the progress bar
			$( "#wpsposts-bar" ).progressbar();
			$( "#wpsposts-bar-percent" ).html( "0%" );

			// Stop button
			$( "#wpsposts-stop" ).click(function() {
				rt_continue = false;
				$( '#wpsposts-stop' ).val( "<?php echo esc_html__( 'Stopping, please wait a moment.', 'wordpress-starter' ); ?>" );
			});

			// Clear out the empty list element that's there for HTML validation purposes
			$( "#wpsposts-debuglist li" ).remove();

			// Called after each import. Updates debug information and the progress bar.
			function WPSPostsUpdateStatus( id, success, response ) {
				$( "#wpsposts-bar" ).progressbar( "value", ( rt_count / rt_total ) * 100 );
				$( "#wpsposts-bar-percent" ).html( Math.round( ( rt_count / rt_total ) * 1000 ) / 10 + "%" );
				rt_count = rt_count + 1;

				if ( success ) {
					rt_successes = rt_successes + 1;
					$( "#wpsposts-debug-successcount" ).html(rt_successes);
					$( "#wpsposts-debuglist" ).append( "<li>" + response.success + "</li>" );
				}
				else {
					rt_errors = rt_errors + 1;
					rt_failedlist = rt_failedlist + ',' + id;
					$( "#wpsposts-debug-failurecount" ).html(rt_errors);
					$( "#wpsposts-debuglist" ).append( "<li>" + response.error + "</li>" );
				}
			}

			// Called when all posts have been processed. Shows the results and cleans up.
			function WPSPostsFinishUp() {
				rt_timeend = new Date().getTime();
				rt_totaltime = Math.round( ( rt_timeend - rt_timestart ) / 1000 );

				$( '#wpsposts-stop' ).hide();

				if ( rt_errors > 0 ) {
					rt_resulttext = '<?php echo $text_failures; ?>';
				} else {
					rt_resulttext = '<?php echo $text_nofailures; ?>';
				}

				$( "#message" ).html( "<p><strong>" + rt_resulttext + "</strong></p>" );
				$( "#message" ).show();
			}

			// Regenerate a specified image via AJAX
			function WPSPosts( id ) {
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {
						action: "ajax_process_post",
						id: id
					},
					success: function( response ) {
						if ( response.success ) {
							WPSPostsUpdateStatus( id, true, response );
						}
						else {
							WPSPostsUpdateStatus( id, false, response );
						}

						if ( rt_posts.length && rt_continue ) {
							WPSPosts( rt_posts.shift() );
						}
						else {
							WPSPostsFinishUp();
						}
					},
					error: function( response ) {
						WPSPostsUpdateStatus( id, false, response );

						if ( rt_posts.length && rt_continue ) {
							WPSPosts( rt_posts.shift() );
						}
						else {
							WPSPostsFinishUp();
						}
					}
				});
			}

			WPSPosts( rt_posts.shift() );
		});
	// ]]>
	</script>
<?php
	}


	/**
	 * Process a single post ID (this is an AJAX handler)
	 *
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 * @SuppressWarnings(PHPMD.Superglobals)
	 */
	public function ajax_process_post() {
		if ( ! wps_get_option( 'debug_mode' ) ) {
			error_reporting( 0 ); // Don't break the JSON result
			header( 'Content-type: application/json' );
			$this->post_id = intval( $_REQUEST['id'] );
		}

		$post = get_post( $this->post_id );
		if ( ! $post || ! in_array( $post->post_type, self::$post_types )  )
			die( json_encode( array( 'error' => sprintf( esc_html__( 'Failed Processing: %s is incorrect post type.', 'wordpress-starter' ), esc_html( $this->post_id ) ) ) ) );

		$this->do_something( $this->post_id, $post );

		die( json_encode( array( 'success' => sprintf( __( '&quot;<a href="%1$s" target="_blank">%2$s</a>&quot; Post ID %3$s was successfully processed in %4$s seconds.', 'wordpress-starter' ), get_permalink( $this->post_id ), esc_html( get_the_title( $this->post_id ) ), $this->post_id, timer_stop() ) ) ) );
	}


	/**
	 *
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function do_something( $post_id, $post ) {
		// do something there with the post
		// use error_log to track happenings
	}


	public function admin_notices_0_0_1() {
		$content  = '<div class="updated fade"><p>';
		$content .= sprintf( __( 'If your WordPress Starter display has gone to funky town, please <a href="%s">read the FAQ</a> about possible CSS fixes.', 'wordpress-starter' ), 'https://aihrus.zendesk.com/entries/23722573-Major-Changes-Since-2-10-0' );
		$content .= '</p></div>';

		echo $content;
	}


	public function admin_notices_donate() {
		$content  = '<div class="updated fade"><p>';
		$content .= sprintf( __( 'Please donate $5 towards development and support of this WordPress Starter plugin. %s', 'wordpress-starter' ), self::$donate_button );
		$content .= '</p></div>';

		echo $content;
	}


	public function update() {
		$prior_version = wps_get_option( 'admin_notices' );
		if ( $prior_version ) {
			if ( $prior_version < '0.0.1' )
				add_action( 'admin_notices', array( $this, 'admin_notices_0_0_1' ) );

			if ( $prior_version < self::VERSION )
				do_action( 'wps_update' );

			wps_set_option( 'admin_notices' );
		}

		// display donate on major/minor version release
		$donate_version = wps_get_option( 'donate_version', false );
		if ( ! $donate_version || ( $donate_version != self::VERSION && preg_match( '#\.0$#', self::VERSION ) ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notices_donate' ) );
			wps_set_option( 'donate_version', self::VERSION );
		}
	}


	public static function scripts() {
		if ( is_admin() ) {
			wp_enqueue_script( 'jquery' );

			wp_register_script( 'jquery-ui-progressbar', plugins_url( 'js/jquery.ui.progressbar.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' ), '1.10.3' );
			wp_enqueue_script( 'jquery-ui-progressbar' );
		}

		do_action( 'wps_scripts' );
	}


	public static function styles() {
		if ( is_admin() ) {
			wp_register_style( 'jquery-ui-progressbar', plugins_url( 'css/redmond/jquery-ui-1.10.3.custom.min.css', __FILE__ ), false, '1.10.3' );
			wp_enqueue_style( 'jquery-ui-progressbar' );
		} else {
			wp_register_style( __CLASS__, plugins_url( 'wordpress-starter.css', __FILE__ ) );
			wp_enqueue_style( __CLASS__ );
		}

		do_action( 'wps_styles' );
	}


	public static function wordpress_starter_shortcode( $atts ) {
		self::scripts();
		self::styles();

		return __CLASS__ . ' shortcode';
	}


}


register_activation_hook( __FILE__, array( 'WordPress_Starter', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'WordPress_Starter', 'deactivation' ) );
register_uninstall_hook( __FILE__, array( 'WordPress_Starter', 'uninstall' ) );


add_action( 'plugins_loaded', 'wordpress_starter_init', 99 );


/**
 *
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
function wordpress_starter_init() {
	if ( ! is_admin() )
		return;

	if ( ! function_exists( 'add_screen_meta_link' ) )
		require_once 'lib/screen-meta-links.php';

	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( WordPress_Starter::PLUGIN_FILE ) ) {
		require_once 'lib/class-wordpress-starter-settings.php';

		global $WordPress_Starter;
		if ( is_null( $WordPress_Starter ) )
			$WordPress_Starter = new WordPress_Starter();

		global $WordPress_Starter_Settings;
		if ( is_null( $WordPress_Starter_Settings ) )
			$WordPress_Starter_Settings = new WordPress_Starter_Settings();
	}
}


?>
