<?php
namespace snippy\sysLog;

interface iLogWriter
{
	/**
	 * Returns module name
	 *
	 * @return string
	 */
	public function getModuleName();

	/**
	 * Setups log level
	 *
	 * @param int $logLevel
	 *
	 * @throws InvalidLogLevelException
	 */
	public function setLogLevel( $logLevel );

	/**
	 * Returns current log level
	 *
	 * @return int
	 */
	public function getLogLevel();

	/**
	 * Logs given message at DEBUG level
	 *
	 * @param string $message
	 *
	 * @throws InvalidLogLevelException
	 */
	public function debug( $message );

	/**
	 * Logs given message at INFO level
	 *
	 * @param string $message
	 *
	 * @throws InvalidLogLevelException
	 */
	public function info( $message );

	/**
	 * Logs given message at WARN level
	 *
	 * @param string $message
	 *
	 * @throws InvalidLogLevelException
	 */
	public function warn( $message );

	/**
	 * Logs given message at ERROR level
	 *
	 * @param string $message
	 *
	 * @throws InvalidLogLevelException
	 */
	public function error( $message );

	/**
	 * Logs given message
	 *
	 * @param int    $level
	 * @param string $message
	 *
	 * @throws snippy\sysLog\InvalidLogLevelException
	 * @throws snippy\sysLog\LogWriterException
	 */
	public function log( $level, $message );
}