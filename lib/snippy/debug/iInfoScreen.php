<?php
namespace snippy\debug;

interface iInfoScreen
{
//	/**
//	 * @var LH\Util\DevConsole\Formater
//	 */
//	protected $formater;
//
//	/**
//	 * @var array
//	 */
//	protected $trace;

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