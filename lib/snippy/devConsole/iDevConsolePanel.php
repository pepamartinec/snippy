<?php
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