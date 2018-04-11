<?php
/**
 * @package Discourage
 * Plugin Name: Discourage - Malicious Bots
 * Plugin URI: #discourage
 * Description: Stop serving contents to scrappers and mis-behaving bots. <strong>Side effect warning:</strong> Probably, it will also discourage SEO on your site - do not activate this plugin in such case.
 * Version: 1.0.0
 * Author: Discourage
 * Author URI: #
 * Donation Link: #
 * License: Not licensed
 * Text Domain: discourage
 */
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Adapted from:
 * https://silvermapleweb.com/using-the-php-session-in-wordpress/
 */
function myStartSession() {
    if(!session_id()) {
        session_start();
		if(empty($_SESSION["discourage"]))
		{
			$_SESSION["discourage"] = strtoupper("D".md5(microtime(true)));
		}
	}
}
function myEndSession() {
    session_destroy();
}
add_action("init", "myStartSession", 0);
add_action("wp_logout", "myEndSession");
add_action("wp_login", "myEndSession");

function discourage()
{
	/**
	 * Normally activate in frontend pages only
	 */
	if(!is_admin())
	{
		if(empty($_SERVER["SERVER_NAME"]))
		{
			// actually, no need to serve in this case, it may be a CLI.
			$_SERVER["SERVER_NAME"] = "localhost";
		}

		if(empty($_COOKIE[$_SESSION["discourage"]]))
		{
			/**
			 * A visitor should review your website within 20 minutes.
			 * It will still show the contents after that period, but the page will reload once in between.
			 */
			setcookie($_SESSION["discourage"], "discourage", time()+1200, "/", $_SERVER["SERVER_NAME"], false, true);

			//global $wp;
			//$current_url = home_url(add_query_arg(array(), $wp->request));
			//header(sprintf("Refresh: 1;url=%s", $current_url));
			header("Refresh: 1;");

			/**
			 * Page will refresh and content will be served in that round
			 * Page may experience a tiny moment of white space, which is still ok.
			 */
			die(""); // Discouraged!
		}
	}
}
add_action("init", "discourage", 1);