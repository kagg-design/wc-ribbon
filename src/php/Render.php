<?php
/**
 * Render class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

/**
 * Class Render.
 */
class Render {

	/**
	 * Init class.
	 */
	public function init(): void {
		add_action( 'woocommerce_init', [ $this, 'init_hooks' ] );
	}

	/**
	 * Init hooks.
	 */
	public function init_hooks(): void {
		add_filter(
			'woocommerce_single_product_image_thumbnail_html',
			[ $this, 'woocommerce_single_product_image_thumbnail_html' ],
			10,
			2
		);
	}

	/**
	 * Get ribbons html.
	 *
	 * @return string
	 */
	private function get_ribbons(): string {
		global $product;

		$ribbons = [
			[
				'meta'     => '_ribbon_best',
				'position' => 'left',
				'color'    => 'red',
				'text'     => 'Топ продаж!',
			],
			[
				'meta'     => '_ribbon_sale',
				'position' => 'right',
				'color'    => 'yellow',
				'text'     => 'Акция!',
			],
		];

		$output = '';

		foreach ( $ribbons as $ribbon ) {
			$status = get_post_meta( $product->get_id(), $ribbon['meta'], false );

			if ( ! isset( $status[0] ) || 'yes' !== $status[0] ) {
				continue;
			}

			$output .=
				'<div class="ribbon-wrapper ' . $ribbon['position'] . '"><div class="ribbon ' . $ribbon['color'] . '">' .
				$ribbon['text'] . '</div></div>';
		}

		return $output;
	}

	/**
	 * Add filter for product thumbnails on product page to display ribbons.
	 *
	 * @param string $html    Html of the thumbnail.
	 * @param int    $post_id Post id.
	 *
	 * @return string
	 */
	public function woocommerce_single_product_image_thumbnail_html( string $html, int $post_id ): string {
		$ribbon = $this->get_ribbons();
		if ( $ribbon ) {
			$html = str_replace( '</a></div>', $ribbon . '</a></div>', $html );
		}

		return $html;
	}

	/**
	 * Override WooCommerce internal function to add ribbons.
	 */
	public function woocommerce_template_loop_product_thumbnail(): void {
		$image  = woocommerce_get_product_thumbnail();
		$ribbon = $this->get_ribbons();

		if ( $ribbon ) {
			$image .= $ribbon;
		}

		echo wp_kses_post( $image );
	}
}
