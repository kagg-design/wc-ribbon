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
	 * Init class.
	 */
	public function init(): void {
		( new Sale() )->init();
		( new Best() )->init();
	}
}
