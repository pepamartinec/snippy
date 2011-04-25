<?php
namespace snippy\debug;

interface iOutputWriter
{
	/**
	 * Writes debug item to output
	 *
	 * @param iOutputItem $item
	 */
	public function write( iOutputItem $item );
}