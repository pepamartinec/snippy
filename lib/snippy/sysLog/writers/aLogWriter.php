<?php
namespace snippy\sysLog\writers;

use snippy\sysLog\xInvalidLogLevelException;
use snippy\sysLog\iLogWriter;

abstract class aLogWriter implements iLogWriter
{
	const DEBUG = 1;
	const INFO  = 2;
	const WARN  = 3;
	const ERROR = 4;

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
		$this->moduleName   = $moduleName;
		$this->logLevel = self::DEFAULT_LEVEL;
	}

	/**
	 * Returns label for given log level
	 *
	 * @param  int    $level
	 * @return string
	 *
	 * @throws InvalidLogLevelException
	 */
	public static function levelLabel( $level )
	{
		switch( $level ) {
			case self::DEBUG: return 'DEBUG';
			case self::INFO:  return 'INFO';
			case self::WARN:  return 'WARN';
			case self::ERROR: return 'ERROR';
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
	 * @throws InvalidLogLevelException
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
	 * @throws InvalidLogLevelException
	 */
	public function debug( $message )
	{
		if( $this->logLevel > self::DEBUG )
			return;

		$this->log( self::DEBUG, $message );
	}

	/**
	 * Logs given message at INFO level
	 *
	 * @param string $message
	 *
	 * @throws InvalidLogLevelException
	 */
	public function info( $message )
	{
		if( $this->logLevel > self::INFO )
			return;

		$this->log( self::INFO, $message );
	}

	/**
	 * Logs given message at WARN level
	 *
	 * @param string $message
	 *
	 * @throws InvalidLogLevelException
	 */
	public function warn( $message )
	{
		if( $this->logLevel > self::WARN )
			return;

		$this->log( self::WARN, $message );
	}

	/**
	 * Logs given message at ERROR level
	 *
	 * @param string $message
	 *
	 * @throws InvalidLogLevelException
	 */
	public function error( $message )
	{
		$this->log( self::ERROR, $message );
	}
}