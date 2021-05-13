<?php
/**
 * Sale class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

/**
 * Class Sale
 */
class Sale {

	/**
	 * Init class.
	 */
	public function init(): void {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		add_action( 'plugins_loaded', [ $this, 'init_hooks' ], 0 );
	}

	/**
	 * Init hooks.
	 */
	public function init_hooks(): void {
		add_action( 'woocommerce_product_options_general_product_data', [ $this, 'ribbon_admin_sale' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'ribbon_admin_sale_save' ] );
		add_filter( 'manage_edit-product_columns', [ $this, 'ribbon_column_into_product_list' ] );
		add_action( 'manage_product_posts_custom_column', [ $this, 'ribbon_rows_into_product_list' ], 10, 3 );
		add_action( 'quick_edit_custom_box', [ $this, 'ribbon_quick_edit_checkbox' ], 10, 3 );
		add_action( 'admin_footer-edit.php', [ $this, 'ribbon_admin_footer_js_script' ], 11 );
		add_action( 'save_post', [ $this, 'ribbon_admin_sale_save' ], 10, 3 );
	}

	/**
	 * Add product data.
	 */
	public function ribbon_admin_sale(): void {
		global $post;
		$product_meta = (object) get_post_meta( $post->ID );
		$value        = ( isset( $product_meta->_ribbon_sale ) && 'yes' === $product_meta->_ribbon_sale[0] ) ? 'yes' : '';
		echo '<div class="options_group">';
		woocommerce_wp_checkbox(
			[
				'id'          => '_ribbon_sale',
				'value'       => $value,
				'cbvalue'     => 'yes',
				'label'       => 'Товар по акции',
				'description' => 'Отметить товар, продающийся по акции',
			]
		);
		echo '</div>';
	}

	/**
	 * Save flag.
	 *
	 * @param int $post_id Post id.
	 */
	public function ribbon_admin_sale_save( int $post_id ): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$ribbon_sale = isset( $_POST['_ribbon_sale'] ) ?
			filter_input( INPUT_POST, '_ribbon_sale', FILTER_VALIDATE_INT ) :
			'';
		update_post_meta( $post_id, '_ribbon_sale', $ribbon_sale );
	}

	/**
	 * Add column to products list.
	 *
	 * @param array $defaults Defaults.
	 *
	 * @return array
	 */
	public function ribbon_column_into_product_list( array $defaults ): array {
		$defaults['_ribbon_sale'] = 'Товар по акции';

		return $defaults;
	}

	/**
	 * Output column.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post id.
	 */
	public function ribbon_rows_into_product_list( string $column, int $post_id ): void {
		if ( '_ribbon_sale' !== $column ) {
			return;
		}

		$arr_value = get_post_meta( $post_id, '_ribbon_sale', false );
		$value     = ( isset( $arr_value[0] ) && 'yes' === $arr_value[0] ) ?
			'<span class="ribbon-sale" data-status="yes" style="color: #e1360c;">Товар по акции</span>' :
			'<span class="ribbon-sale" data-status="no">&mdash;</span>';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $value;
	}

	/**
	 * Output quick edit checkbox.
	 *
	 * @param string $col  Column name.
	 * @param string $type Post type.
	 */
	public function ribbon_quick_edit_checkbox( string $col, string $type ): void {
		if ( 'product' !== $type || '_ribbon_sale' !== $col ) {
			return;
		}

		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<h4>Товар по акции</h4>
				<label class="alignleft">
					<input type="checkbox" class="ribbon-sale-checkbox" name="_ribbon_sale" value="yes">
					<span class="checkbox-title">Отметить товар, продающийся по акции</span>
				</label>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Admin footer script.
	 */
	public function ribbon_admin_footer_js_script(): void {
		$slug = 'product';

		if (
			( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) === $slug ) ||
			( filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING ) === $slug )
		) {
			?>
			<script type="text/javascript">
				( function( $ ) {
					var _wp_inline_edit = inlineEditPost.edit;
					inlineEditPost.edit = function( id ) {
						_wp_inline_edit.apply( this, arguments );
						var _post_id = 0;
						if ( typeof ( id ) == 'object' ) _post_id = parseInt( this.getId( id ) );
						if ( _post_id > 0 ) {
							var _post_slug = $( '#post-' + _post_id ),
								_edit_slug = $( '#edit-' + _post_id ),
								_payment_available_checkbox = $( '.ribbon-sale-checkbox', _edit_slug ),
								_payment_available_status = $( 'td._ribbon_sale > span', _post_slug ).attr( 'data-status' );
							if ( _payment_available_status === 'yes' ) _payment_available_checkbox.prop( 'checked', true );
							else _payment_available_checkbox.prop( 'checked', false );
						}
					};
				} )( jQuery );
			</script>
			<?php
		}
	}
}
