<?php
/**
 * This file is part of snippy.
 *
 * @author Josef Martinec <joker806@gmail.com>
 * @copyright Copyright (c) 2011, Josef Martinec
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace snippy\devConsole;

interface iDevConsolePanel
{
	/**
	 * Returns panel unique ID
	 */
	public function getID();

	/**
	 * Returns panel title
	 */
	public function getTitle();

	/**
	 * Renders panel
	 *
	 * @return string
	 */
	public function render();

	/**
	 * Includes panel from previous code call
	 *
	 * @param aLogBarPanel $panel
	 */
	//public abstract function restorePrevious( aLogBarPanel $panel );
}