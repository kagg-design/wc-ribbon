<?php
/**
 * Sale class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

/**
 * Class Sale.
 */
class Sale extends RibbonBase {

	/**
	 * Sale constructor.
	 */
	public function __construct() {
		$this->meta  = '_ribbon_sale';
		$this->class = 'ribbon-sale';

		$this->label       = 'Товар по акции';
		$this->description = 'Отметить товар, продающийся по акции';
	}
}
