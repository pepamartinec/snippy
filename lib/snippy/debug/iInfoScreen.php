<?php
namespace snippy\debug;

interface iInfoScreen
{
	/**
	 * Displays info screen
	 */
	public function display();

	/**
	 * Saves infoScreen into file
	 *
	 * @param string $fileName
	 */
	public function save( $fileName );

	/**
	 * Prints dynamic content of a screen
	 *
	 * Used internally within templates
	 */
	public function printContent();
}