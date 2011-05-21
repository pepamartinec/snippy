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
use snippy\debug\xOutputWriterException;
use snippy\debug\outputItems\cException;
use snippy\debug\screens\cExceptionScreen;
use snippy\debug\iOutputWriter;

class cExceptionScreenWriter implements iOutputWriter
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
	 * @param iOutputItem $item
	 */
	public function write( iOutputItem $item )
	{
		if( $item instanceof cException === false ) {
			throw new xOutputWriterException( 'Expected cException output item, \''.get_class( $item ).'\' given' );
		}
		
		/* @var $item snippy\debug\outputItems\cException */
		
		$screen = new cExceptionScreen( $this->formater, $item->getException() );
		$screen->display();
	}
}