<?php
/**
 * Main class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

/**
 * Class Main.
 */
class Main {

	/**
	 * Render class instance.
	 *
	 * @var Render
	 */
	public Render $render;

	/**
	 * Get instance.
	 *
	 * @return Main
	 */
	public static function get_instance(): Main {
		static $instance;

		if ( ! $instance ) {
			$instance = new self();

			$instance->init();
		}

		return $instance;
	}

	/**
	 * Init class.
	 */
	public function init(): void {
		( new Sale() )->init();
		( new Best() )->init();

		$this->render = new Render();
		$this->render->init();

		add_action( 'woocommerce_init', [ $this, 'init_hooks' ] );
	}

	/**
	 * Init hooks.
	 */
	public function init_hooks(): void {
		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );
	}

	/**
	 * Enqueue Scripts.
	 */
	public function wp_enqueue_scripts(): void {
		wp_enqueue_style(
			'wc-ribbon',
			KAGG_WC_RIBBON_URL . '/assets/css/wc-ribbon.css',
			[],
			KAGG_WC_RIBBON_VERSION
		);
	}
}
