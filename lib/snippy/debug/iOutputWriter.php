<?php
namespace snippy\debug;

interface iOutputWriter
{
	/**
	 * Writes debug item to output
	 *
	 * @param iItem $item
	 */
	public function write( iOutputItem $item );
}