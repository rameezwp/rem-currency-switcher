<?php
/**
 * Plugin Name: REM - Currency Switcher
 * Plugin URI: https://webcodingplace.com/rem-currency-switcher/
 * Description: Live currency rates conversion
 * Version: 1.0
 * Author: WebCodingPlace
 * Author URI: https://webcodingplace.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rem-currency-switcher
 * Domain Path: /languages
 */

/* 
**========== Direct access not allowed =========== 
*/
if( ! defined('ABSPATH' ) ){ exit; }

require_once('plugin.class.php');
define('REM_CS_PATH', untrailingslashit(plugin_dir_path( __FILE__ )) );
define('REM_CS_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );

require_once( REM_CS_PATH.'/inc/currency-widget.php');

if( class_exists('REM_Currency_Switcher') && defined('REM_URL')){
    $rem_currency_switcher = new REM_Currency_Switcher;
	if (defined('REM_PATH')) {
		require_once REM_PATH.'/inc/update/wp-package-updater/class-wp-package-updater.php';
		$rem_currency_switcher_updater = new WP_Package_Updater(
			'https://kb.webcodingplace.com/',
			wp_normalize_path( __FILE__ ),
			wp_normalize_path( plugin_dir_path( __FILE__ ) )
		);
	}
}

?>