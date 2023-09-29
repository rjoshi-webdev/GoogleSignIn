<?php
/**
 * Interface Module.
 *
 * Every module inside src/Modules/ should implement
 * to this interface.
 *
 * @package RjoshiWebdev\GoogleSignIn
 * @since 1.0.0
 */

namespace RjoshiWebdev\GoogleSignIn\Interfaces;

/**
 * Interface Module
 *
 * @package WpGuruDev\OrderExport
 */
interface Module {

	/**
	 * Initialization of module.
	 *
	 * @return void
	 */
	public function init(): void;

	/**
	 * Return module name.
	 *
	 * @return string
	 */
	public function name(): string;
}
