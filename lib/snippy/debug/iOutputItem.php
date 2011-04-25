<?php
namespace snippy\debug;

interface iOutputItem
{
	/**
	 * Sets position of message invokation
	 *
	 * @param string $file
	 * @param int    $line
	 */
	public function setInvokePosition( $file, $line );
	
	/**
	 * Renders and returns message content
	 *
	 * @param  HTMLFormater $formater
	 * @return string
	 */
	public function render( cHTMLFormater $formater );
}