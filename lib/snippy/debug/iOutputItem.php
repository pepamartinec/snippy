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