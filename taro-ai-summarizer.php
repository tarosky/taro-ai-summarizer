<?php
/*
Plugin Name: Taro AI Summarizer
Plugin URI: https://wordpress.org/plugin/taro-ai-summarizer
Description: Summarize contents via ChatGPT.
Author: Tarosky INC.
Author URI: https://tarosky.co.jp
Text Domain: taroai
Domain Path: /languages/
License: GPL v3 or later.
Version: 1.0.0
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || die();


add_action( 'plugins_loaded', 'taro_ai_init' );

/**
 * Bootstrap function.
 *
 * @package taroai
 * @since 1.0.0
 * @access private
 */
function taro_ai_init() {
	load_plugin_textdomain( 'taroai', false, basename( dirname( __FILE__ ) ) . '/languages' );
	require_once __DIR__ . '/includes/functions.php';
	require_once __DIR__ . '/includes/setting.php';
	// Register command if this is CLI.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		require_once __DIR__ . '/includes/class-taro-ai-command.php';
		WP_CLI::add_command( 'taroai', 'Tarosky\TaroAI\Commands' );
	}
}
