<?php
namespace Tarosky\TaroAI;

use cli\Table;

/**
 * Command function.
 */
class Commands extends \WP_CLI_Command {
	
	public function connection() {
	
	}
	
	/**
	 * Display models.
	 *
	 * @return void
	 */
	public function model() {
		$engines = get_engines();
		if ( is_wp_error( $engines ) ) {
			\WP_CLI::error( $engines->get_error_message() );
		}
		$table = new Table();
		$table->setHeaders( [ 'ID', 'Owner', 'Created' ] );
		foreach ( $engines as $engine ) {
			$table->addRow( [ $engine['id'], $engine['owned_by'], date_i18n( 'Y-m-d', $engine['created'], true ) ] );
		}
		$table->display();
	}
	
	/**
	 * Summarize contents.
	 *
	 * @synopsis <post_id> [--engine=<engine>]
	 * @param array $args       Arguments.
	 * @param array $assoc_args Options.
	 */
	public function summarize( $args, $assoc_args ) {
		$engine          = $assoc_args['engine'] ?? get_option( 'taroai_engine_id', '' );
		list( $post_id ) = $args;
		$post = get_post( $post_id );
		if ( ! $post ) {
			\WP_CLI::error( __( 'No post found.', 'taroai' ) );
		}
		// Create prompt.
		$prompt = sprintf( 'Please summarize a blog post "%s" in 80 words.', get_the_title( $post ) );
		\WP_CLI::line( $prompt );
		$prompt = [
			$prompt,
			'',
		];
		$prompt = explode( "\n", strip_text( $post->post_content, 2000 ) );
		$prompt = implode( "\n", $prompt );
		$response = get_completion( $prompt, $engine );
		if ( is_wp_error( $response ) ) {
			\WP_CLI::error( $response->get_error_message() );
		}
		foreach ( $response['choices'] as $choice) {
			\WP_CLI::line( $choice['message']['content'] );
		}
		\WP_CLI::success( 'Done!' );
	}
}
