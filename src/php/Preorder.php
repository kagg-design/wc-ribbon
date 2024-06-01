<?php
/**
 * Preorder class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

/**
 * Class Preorder.
 */
class Preorder extends RibbonBase {

	/**
	 * Sale constructor.
	 */
	public function __construct() {
		$this->meta  = '_ribbon_preorder';
		$this->class = 'ribbon-preorder';

		$this->label       = 'Предзаказ из Европы';
		$this->description = 'Отметить товар по предзаказу из Европы';
	}
}
