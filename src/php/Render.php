<?php
/**
 * Render class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

use WC_Product;

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
		add_action(
			'woocommerce_product_get_image',
			[ $this, 'woocommerce_product_get_image' ],
			10,
			5
		);

		add_filter(
			'woocommerce_single_product_image_thumbnail_html',
			[ $this, 'woocommerce_single_product_image_thumbnail_html' ],
			10,
			2
		);
	}

	/**
	 * Add ribbon to the image on the main page and category page.
	 *
	 * @param string     $image       Image html.
	 * @param WC_Product $product     Product.
	 * @param string     $size        Size.
	 * @param array      $attr        Attributes.
	 * @param bool       $placeholder Placeholder.
	 *
	 * @return string
	 * @noinspection PhpMissingParamTypeInspection
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function woocommerce_product_get_image( $image, $product, $size, $attr, $placeholder ): string {
		$ribbon = $this->get_ribbons();

		if ( $ribbon ) {
			$image .= $ribbon;
		}

		return $image;
	}

	/**
	 * Add ribbon to the image on the product page.
	 *
	 * @param string $html    Html of the thumbnail.
	 * @param int    $post_id Post id.
	 *
	 * @return string
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function woocommerce_single_product_image_thumbnail_html( string $html, int $post_id ): string {
		$ribbon = $this->get_ribbons();

		if ( $ribbon ) {
			$html = str_replace( '</a></div>', $ribbon . '</a></div>', $html );
		}

		return $html;
	}

	/**
	 * Get ribbons html.
	 *
	 * @return string
	 */
	private function get_ribbons(): string {
		global $product;

		if ( ! $product ) {
			return '';
		}

		$ribbons = [
			[
				'meta'     => '_ribbon_best',
				'class'	   => 'ribbon-best',
				'position' => 'left',
				'color'    => 'red',
				'text'     => 'Топ продаж!',
			],
			[
				'meta'     => '_ribbon_preorder',
				'class'	   => 'ribbon-preorder',
				'position' => 'left',
				'color'    => 'red',
				'text'     => 'Предзаказ из Европы!',
			],
			[
				'meta'     => '_ribbon_sale',
				'class'	   => 'ribbon-sale',
				'position' => 'right',
				'color'    => 'yellow',
				'text'     => 'Акция!',
			],
		];

		$output = '';

		foreach ( $ribbons as $ribbon ) {
			$status = get_post_meta( $product->get_id(), $ribbon['meta'] );

			if ( ! isset( $status[0] ) || 'yes' !== $status[0] ) {
				continue;
			}

			$output .=
				'<div class="ribbon-wrapper ' . $ribbon['position'] . '">' .
				'<div class="ribbon ' . $ribbon['class'] . ' ' . $ribbon['color'] . '">' . $ribbon['text'] . '</div>' .
				'</div>';
		}

		return $output;
	}
}
