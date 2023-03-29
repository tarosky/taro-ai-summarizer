<?php
namespace Tarosky\TaroAI;

/**
 * Utility functions for Taro AI Summarizer.
 *
 * @package taroai
 * @since 1.0.0
 */


/**
 * Get AI.
 *
 * @return string
 */
function get_api_key():string {
	return get_option( 'taroai_api_key', '' );
}

function get_request_headers( $headers = [] ) {
	return array_merge_recursive( [
		'headers' => [
			'Authorization' => 'Bearer ' . get_api_key(),
			'Content-Type'  => 'application/json',
		 ],
	], $headers );
}

/**
 * Get engines.
 *
 * @return array|\WP_Error
 */
function get_engines():mixed {
	$engine_cache = get_transient( 'taro_ai_models' );
	if ( false !== $engine_cache ) {
		// Cache found.
		return $engine_cache;
	}
	$response = wp_remote_get( 'https://api.openai.com/v1/models', get_request_headers() );
	if ( is_wp_error( $response ) ) {
		return $response;
	}
	$models  = json_decode( wp_remote_retrieve_body( $response ), true );
	$engines = [];
	if ( ! empty( $models['data'] ) ) {
		$engines = $models['data'];
	}
	set_transient( 'taro_ai_models', $engines, 60 * 60 * 24 );
	return $engines;
}

/**
 * Get completion.
 *
 * @param string $text  Text to complete.
 * @param string $model Model ID.
 * @return array\WP_Error
 */
function get_completion( $text, $model = '' ) {
	$model = $model ?: get_option( 'taroai_engine_id', '' );
	$response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', get_request_headers( [
		'timeout' => 45,
		'body'    => json_encode( [
			'messages' => [
				[
					'role' => 'user',
					'content' => $text,
				],
			],
			'model'  => $model,
		] ),
	] ) );
	if ( is_wp_error( $response ) ) {
		return $response;
	}
	return json_decode( wp_remote_retrieve_body( $response ), true );
}

/**
 * Strip text and concat it to specified length.
 *
 * @param string $html_text HTML text.
 * @param int    $max_chars Max chars.
 * @return string
 */
function strip_text( string $html_text, int $max_chars = 1000 ):string {
	$text = html_entity_decode( wp_strip_all_tags( strip_shortcodes( $html_text ) ), ENT_HTML5 );
	$text = preg_replace( '/[\r\n|\r|\n]+/usm', "\n", $text );
	return mb_substr( $text, 0, $max_chars );
}
