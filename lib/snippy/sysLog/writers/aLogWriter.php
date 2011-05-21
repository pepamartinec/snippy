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

namespace snippy\sysLog\writers;

use snippy\sysLog\xInvalidLogLevelException;
use snippy\sysLog\iLogWriter;

abstract class aLogWriter implements iLogWriter
{
	const DEBUG = 1;
	const WARN  = 2;
	const ERROR = 3;
	const NONE  = 4;

	const DEFAULT_LEVEL = self::DEBUG;

	/**
	 * Module name
	 *
	 * @var string
	 */
	protected $moduleName;

	/**
	 * Log level
	 *
	 * @var int
	 */
	protected $logLevel;

	/**
	 * Constructor
	 *
	 * @param iLogWriter $writer
	 */
	public function __construct( $moduleName )
	{
		$this->moduleName = $moduleName;
		$this->logLevel   = self::DEFAULT_LEVEL;
	}

	/**
	 * Returns label for given log level
	 *
	 * @param  int    $level
	 * @return string
	 *
	 * @throws xInvalidLogLevelException
	 */
	public static function levelLabel( $level )
	{
		switch( $level ) {
			case self::DEBUG: return 'DEBUG';
			case self::WARN:  return 'WARN';
			case self::ERROR: return 'ERROR';
			case self::NONE:  return 'NONE';
			default:          throw new xInvalidLogLevelException( $level );
		}
	}

	/**
	 * Returns module name
	 *
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->moduleName;
	}

	/**
	 * Setups log level
	 *
	 * @param int $logLevel
	 *
	 * @throws xInvalidLogLevelException
	 */
	public function setLogLevel( $logLevel )
	{
		$this->logLevel = $logLevel;
	}

	/**
	 * Returns current log level
	 *
	 * @return int
	 */
	public function getLogLevel()
	{
		return $this->logLevel;
	}

	/**
	 * Logs given message at DEBUG level
	 *
	 * @param string $message
	 *
	 * @throws snippy\sysLog\xWriteException
	 */
	public function debug( $message )
	{
		if( $this->logLevel > self::DEBUG ) {
			return null;
		}

		return $this->log( self::DEBUG, $message );
	}

	/**
	 * Logs given message at WARN level
	 *
	 * @param string $message
	 *
	 * @throws snippy\sysLog\xWriteException
	 */
	public function warn( $message )
	{
		if( $this->logLevel > self::WARN ) {
			return null;
		}

		return $this->log( self::WARN, $message );
	}

	/**
	 * Logs given message at ERROR level
	 *
	 * @param string $message
	 *
	 * @throws snippy\sysLog\xWriteException
	 */
	public function error( $message )
	{
		if( $this->logLevel > self::ERROR ) {
			return null;
		}
		
		return $this->log( self::ERROR, $message );
	}
}