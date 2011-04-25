<?php
namespace snippy\sysLog\writers;

use snippy\sysLog\iLogWriter;

/**
 * cBlackHole log writer
 *
 * Any log writen trouhg this writer is immediately thrown away
 *
 * @author Josef Martinec
 */
class cBlackHole extends aLogWriter
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
	 * @param int         $level
	 * @param string      $message
	 */
	public function log( $level, $message ) {}
}