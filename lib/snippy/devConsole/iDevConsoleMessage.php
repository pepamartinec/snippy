<?php
namespace snippy\devConsole;

interface iDevConsoleMessage
{
	/**
	 * Renders and returns message content
	 *
	 * @return string
	 */
	public function render();
}