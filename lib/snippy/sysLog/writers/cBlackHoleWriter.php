<?php
namespace snippy\sysLog\writers;

use snippy\sysLog\iLogWriter;

/**
 * cBlackHoleWriter log writer
 *
 * Any log writen trouhg this writer is immediately thrown away
 *
 * @author Josef Martinec
 */
class cBlackHoleWriter extends aLogWriter
{
	/**
	 * Constructor
	 *
	 * Empty constructor, so parent constructor won't be called
	 */
	public function __construct() {}

	/**
	 * Logs given message
	 *
	 * @param  int    $level   message level
	 * @param  string $message message content
	 * @return int             unique message ID
	 */
	public function log( $level, $message )
	{
		return null;
	}
}