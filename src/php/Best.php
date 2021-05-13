<?php
/**
 * Best class file.
 *
 * @package kagg/wc-ribbon
 */

namespace KAGG\WCRibbon;

/**
 * Class Best.
 */
class Best extends RibbonBase {

	/**
	 * Sale constructor.
	 */
	public function __construct() {
		$this->meta  = '_ribbon_best';
		$this->class = 'ribbon-best';

		$this->label       = 'Товар-бестселлер';
		$this->description = 'Отметить товар как бестселлер';
	}
}
