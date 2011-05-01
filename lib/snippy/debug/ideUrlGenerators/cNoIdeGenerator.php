<?php
namespace snippy\debug\ideUrlGenerators;

use snippy\debug\iIdeUrlGenerator;

class cNoIdeGenerator implements iIdeUrlGenerator
{
	/**
	 * Generates URL for opening file within IDE
	 *
	 * @param  string      $file
	 * @param  int|null    $line
	 * @return string|null
	 */
	public  function generateUrl( $file, $line = null )
	{
		return null;
	}
}