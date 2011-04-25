<?php
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