<?php
/**
 * RibbonBase class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

/**
 * Class RibbonBase.
 */
abstract class RibbonBase {

	/**
	 * Product post type.
	 */
	protected const PRODUCT = 'product';

	/**
	 * Name of the meta to store ribbon value.
	 *
	 * @var string
	 */
	protected string $meta = '';

	/**
	 * Label of the ribbon.
	 *
	 * @var string
	 */
	protected string $label = '';

	/**
	 * Description of the ribbon.
	 *
	 * @var string
	 */
	protected string $description = '';

	/**
	 * Name of the ribbon class.
	 *
	 * @var string
	 */
	protected string $class = '';

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
		add_action( 'woocommerce_product_options_general_product_data', [ $this, 'ribbon_admin' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'ribbon_admin_save' ] );
		add_filter( 'manage_edit-product_columns', [ $this, 'ribbon_column_into_product_list' ] );
		add_action( 'manage_product_posts_custom_column', [ $this, 'ribbon_rows_into_product_list' ], 10, 3 );
		add_action( 'quick_edit_custom_box', [ $this, 'ribbon_quick_edit_checkbox' ], 10, 3 );
		add_action( 'admin_footer-edit.php', [ $this, 'ribbon_admin_footer_script' ], 11 );
		add_action( 'save_post', [ $this, 'ribbon_admin_save' ], 10, 3 );
	}

	/**
	 * Add product data.
	 */
	public function ribbon_admin(): void {
		global $post;

		$meta         = $this->meta;
		$product_meta = (object) get_post_meta( $post->ID );
		$value        = ( isset( $product_meta->$meta ) && 'yes' === $product_meta->$meta[0] ) ? 'yes' : '';

		echo '<div class="options_group">';
		woocommerce_wp_checkbox(
			[
				'id'          => $this->meta,
				'value'       => $value,
				'cbvalue'     => 'yes',
				'label'       => $this->label,
				'description' => $this->description,
			]
		);
		echo '</div>';
	}

	/**
	 * Save flag.
	 *
	 * @param int $post_id Post id.
	 */
	public function ribbon_admin_save( int $post_id ): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$status = isset( $_POST[ $this->meta ] ) ?
			filter_input( INPUT_POST, $this->meta, FILTER_SANITIZE_STRING ) :
			'';
		update_post_meta( $post_id, $this->meta, $status );
	}

	/**
	 * Add column to products list.
	 *
	 * @param array $defaults Defaults.
	 *
	 * @return array
	 */
	public function ribbon_column_into_product_list( array $defaults ): array {
		$defaults[ $this->meta ] = $this->label;

		return $defaults;
	}

	/**
	 * Output column.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post id.
	 */
	public function ribbon_rows_into_product_list( string $column, int $post_id ): void {
		if ( $this->meta !== $column ) {
			return;
		}

		$arr_value = get_post_meta( $post_id, $this->meta, false );
		$value     = ( isset( $arr_value[0] ) && 'yes' === $arr_value[0] ) ?
			'<span class="' . $this->class . '" data-status="yes" style="color: #e1360c;">' . $this->label . '</span>' :
			'<span class="' . $this->class . '" data-status="no">&mdash;</span>';
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
		if ( self::PRODUCT !== $type || $this->meta !== $col ) {
			return;
		}

		?>
		<fieldset class="inline-edit-col-left clear">
			<div class="inline-edit-col">
				<label class="alignleft">
					<input
							type="checkbox" class="<?php echo esc_attr( $this->class ); ?>-checkbox"
							name="<?php echo esc_attr( $this->meta ); ?>" value="yes">
					<span class="checkbox-title"><?php echo esc_html( $this->description ); ?></span>
				</label>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Admin footer script.
	 */
	public function ribbon_admin_footer_script(): void {
		if (
			( filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING ) === self::PRODUCT ) ||
			( filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING ) === self::PRODUCT )
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
								_checkbox = $( '.<?php echo esc_attr( $this->class ); ?>-checkbox', _edit_slug ),
								_status = $( 'td.<?php echo esc_attr( $this->meta ); ?> > span', _post_slug ).attr( 'data-status' );
							if ( _status === 'yes' ) _checkbox.prop( 'checked', true );
							else _checkbox.prop( 'checked', false );
						}
					};
				} )( jQuery );
			</script>
			<?php
		}
	}
}
