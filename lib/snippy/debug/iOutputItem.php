<?php
namespace snippy\debug;

interface iOutputItem
{
	/**
	 * Renders and returns message content
	 *
	 * @param  HTMLFormater $formater
	 * @return string
	 */
	public function render( cHTMLFormater $formater );
}