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
	 * @throws snippy\sysLog\xInvalidLogLevelException
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
	 * @param  string   $message message content
	 * @return int|null          unique message ID
	 *
	 * @throws snippy\sysLog\xWriteException
	 */
	public function debug( $message );

	/**
	 * Logs given message at WARN level
	 *
	 * @param  string   $message message content
	 * @return int|null          unique message ID
	 *
	 * @throws snippy\sysLog\xWriteException
	 */
	public function warn( $message );

	/**
	 * Logs given message at ERROR level
	 *
	 * @param  string   $message message content
	 * @return int|null          unique message ID
	 *
	 * @throws snippy\sysLog\xWriteException
	 */
	public function error( $message );

	/**
	 * Logs given message
	 *
	 * @param  int      $level   message level
	 * @param  string   $message message content
	 * @return int|null          unique message ID
	 *
	 * @throws snippy\sysLog\xInvalidLogLevelException
	 * @throws snippy\sysLog\xWriteException
	 */
	public function log( $level, $message );
}