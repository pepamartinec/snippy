<?php
namespace snippy\debug\outputWriters;

use snippy\debug\iOutputItem;
use snippy\debug\iOutputWriter;

class cBlackHoleWriter implements iOutputWriter
{
	/**
	 * Writes debug item to output
	 *
	 * @param iItem $item
	 */
	public function write( iOutputItem $item ) {}
}