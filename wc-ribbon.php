<?php
/*
Plugin Name: WooCommerce Add Ribbon to Product
Plugin URI: mailto:ovsyannikov.ivan@gmail.com
Description: WooCommerce Add Ribbon to Product
Version: 1.0 Beta
Author: Ivan Ovsyannikov
Author URI: mailto:ovsyannikov.ivan@gmail.com
*/

add_action('plugins_loaded', 'woocommerce_ribbon_init', 0);

function woocommerce_ribbon_init() {
	if (!class_exists('WC_Payment_Gateway')) return;
	$woocommerce_ribbon_sale = new WC_Ribbon_Sale();
}

class WC_Ribbon_Sale {

	public function __construct() {
		add_action('woocommerce_product_options_general_product_data', array($this, 'ribbon_admin_sale'));
		add_action('woocommerce_process_product_meta', array($this, 'ribbon_admin_sale_save'));
		add_filter('manage_edit-product_columns', array($this, 'ribbon_column_into_product_list'));
		add_action('manage_product_posts_custom_column', array($this, 'ribbon_rows_into_product_list'), 10, 3);
		add_action('quick_edit_custom_box', array($this, 'ribbon_quickedit_checkbox'), 10, 3);
		add_action('admin_footer-edit.php', array($this, 'ribbon_admin_footer_js_script'), 11);
		add_action('save_post', array($this, 'ribbon_admin_sale_save'), 10, 3);
	}

	function ribbon_admin_sale() {
		global $post;
		$product_meta = (object) get_post_meta($post->ID);
		$value = (isset($product_meta->_ribbon_sale) && $product_meta->_ribbon_sale[0] == 'yes') ? 'yes' : '';
		echo '<div class="options_group">';
		woocommerce_wp_checkbox(array(
			'id' => '_ribbon_sale',
			'value' => $value,
			'cbvalue' => 'yes',
			'label' => 'Товар по акции',
			'description' => 'Отметить товар, продающийся по акции'
		));
		echo '</div>';
	}

	function ribbon_admin_sale_save($post_id) {
		$ribbon_sale = isset($_POST['_ribbon_sale']) ? $_POST['_ribbon_sale'] : '';
		update_post_meta($post_id, '_ribbon_sale', $ribbon_sale);
	}

	function ribbon_column_into_product_list($defaults) {
		$defaults['_ribbon_sale'] = 'Товар по акции';
		return $defaults;
	}

	function ribbon_rows_into_product_list($column, $post_id) {
		switch ($column) {
			case '_ribbon_sale':
				$arr_value = get_post_meta($post_id, '_ribbon_sale', false);
				$value = (isset($arr_value[0]) && $arr_value[0] == 'yes') ? '<span class="ribbon-sale" data-status="yes" style="color: #e1360c;">Товар по акции</span>' : '<span class="ribbon-sale" data-status="no">&mdash;</span>';
				echo $value;
				break;
		}
	}

	function ribbon_quickedit_checkbox($col, $type) {
		if ($type != 'product') return;
		if ($col == '_ribbon_sale') {
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
	}

	function ribbon_admin_footer_js_script() {
		$slug = 'product';
		if ((isset($_GET['page']) && $_GET['page'] == $slug) || (isset($_GET['post_type']) && $_GET['post_type'] == $slug)) {
			?>
			<script type="text/javascript">
			(function($) {
				var _wp_inline_edit = inlineEditPost.edit;
				inlineEditPost.edit = function(id) {
					_wp_inline_edit.apply(this, arguments);
					var _post_id = 0;
					if (typeof(id) == 'object') _post_id = parseInt(this.getId(id));
					if (_post_id > 0) {
						var _post_slug = $('#post-' + _post_id),
						_edit_slug = $('#edit-' + _post_id),
						_payment_available_checkbox = $('.ribbon-sale-checkbox', _edit_slug),
						_payment_available_status = $('td._ribbon_sale > span', _post_slug).attr('data-status');
						if (_payment_available_status == 'yes') _payment_available_checkbox.prop('checked', true);
						else _payment_available_checkbox.prop('checked', false);
					}
				}
			})(jQuery);
			</script>
			<?php
		}
	}
}