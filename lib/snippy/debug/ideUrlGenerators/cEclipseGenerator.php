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

namespace snippy\debug\ideUrlGenerators;

use snippy\debug\iIdeUrlGenerator;

class cEclipseGenerator implements iIdeUrlGenerator
{
	/**
	 * @var string
	 */
	protected $host;
	
	/**
	 * Constructor
	 *
	 * @param string $host IDE host name
	 */
	public function __construct( $host )
	{
		$this->host = $host;
	}
	
	/**
	 * Generates URL for opening file within IDE
	 *
	 * @param  string      $file
	 * @param  int|null    $line
	 * @return string|null
	 */
	public  function generateUrl( $file, $line = null )
	{
		$file   = urlencode( $_SERVER['DOCUMENT_ROOT'] .'/'. $file );
		$line   = max( 1, $line === null ? 0 : $line - 1 );
			
		return "http://{$this->host}/?command=org.eclipse.soc.ewi.examples.commands.openfile&path={$file}&line={$line}";
	}
}
