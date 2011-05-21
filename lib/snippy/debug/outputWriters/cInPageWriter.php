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

namespace snippy\debug\outputWriters;

use snippy\debug\cHTMLFormater;
use snippy\debug\iOutputItem;
use snippy\debug\iOutputWriter;

class cInPageWriter implements iOutputWriter
{
	/**
	 * @var HTMLFormater
	 */
	protected $formater;

	/**
	 * Constructor
	 *
	 * @param HTMLFormater $formater
	 */
	public function __construct( cHTMLFormater $formater )
	{
		$this->formater = $formater;
	}

	/**
	 * Writes debug item to output
	 *
	 * @param iItem $item
	 */
	public function write( iOutputItem $item )
	{
		echo $item->render( $this->formater );
	}
}