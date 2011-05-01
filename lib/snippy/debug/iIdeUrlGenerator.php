<?php
namespace snippy\debug;

interface iIdeUrlGenerator
{
	/**
	 * Generates URL for opening file within IDE
	 *
	 * @param  string      $file
	 * @param  int|null    $line
	 * @return string|null
	 */
	public function generateUrl( $file, $line = null );
}