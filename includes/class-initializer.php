<?php
/**
 * Newspack Network Node plugin initialization.
 *
 * @package Newspack
 */

namespace Newspack_Network_Node;

/**
 * Class to handle the plugin initialization
 */
class Initializer {

	/**
	 * Runs the initialization.
	 */
	public static function init() {
		Admin::init();
		Settings::init();
		Webhook::init();
		Data_Listeners::init();
	}

}
