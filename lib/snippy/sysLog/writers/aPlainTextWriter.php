<?php
namespace snippy\sysLog\writers;

use snippy\sysLog\xLogWriterException;

/**
 * Stream log writer
 *
 * @author Josef Martinec
 */
abstract class aPlainTextWriter extends aLogWriter
{
	const DEFAULT_ITEM_DATE = 'Y-m-d H:i:s';
	const DEFAULT_ITEM_MASK = '%d [%l] %m - %c';

	/**
	 * Log item mask
	 *
	 * @var string
	 */
	protected $itemMask;

	/**
	 * Log item date format
	 *
	 * @var string
	 */
	protected $itemMaskDate;

	/**
	 * Set of base placeholders
	 *
	 * @var array
	 */
	protected $basePlaceholders;

	/**
	 * Constructor
	 *
	 * @param string $outputFile
	 */
	public function __construct( $moduleName )
	{
		parent::__construct( $moduleName );

		$this->basePlaceholders = array();
		$this->setItemMask( self::DEFAULT_ITEM_MASK, self::DEFAULT_ITEM_DATE );
	}

	/**
	 * Setups additional placeholders
	 *
	 * @param array $placeholders
	 */
	public function setExternalPlaceholders( array $placeholders )
	{
		$this->basePlaceholders = $placeholders;

		if( isset( $this->basePlaceholders['%%'] ) )
			unset( $this->basePlaceholders['%%'] );
	}

	/**
	 * Setups log message mask
	 *
	 * Available placeholders:
	 *  %i - unique message ID
	 *  %d - date/time field
	 *  %l - severity indicator
	 *  %m - module name
	 *  %c - message content
	 *  %% - percentage char (%)
	 *
	 * @param string      $mask       log item mask
	 * @param string|null $dateFormat format for date/time field
	 */
	public function setItemMask( $mask, $dateFormat = null )
	{
		// date/time
		if( strpos( $mask, '%d' ) === false ) {
			$this->itemMaskDate = null;
			
		} else {
			if( $dateFormat === null ) {
				throw new xLogWriterException( "Missing dateFormat for item mask '{$mask}'" );
			}

			$this->itemMaskDate = $dateFormat;
		}

		$this->itemMask = $mask;
	}

	/**
	 * Logs given message
	 *
	 * @param int    $level
	 * @param string $message
	 *
	 * @throws snippy\sysLog\InvalidLogLevelException
	 * @throws snippy\sysLog\LogWriterException
	 */
	public function log( $level, $message )
	{
		$replace = array(
			'%i' => uniqid(),
			'%d' => date( $this->itemMaskDate ),
			'%l' => self::levelLabel( $level ),
			'%m' => $this->moduleName,
			'%c' => $message,
			'%%' => '%'
		);

		$message = str_replace( array_keys( $this->basePlaceholders ), $this->basePlaceholders, $this->itemMask );
		$message = str_replace( array_keys( $replace ), $replace, $message );

		$this->write( $message );
	}

	/**
	 * Writes given log item to output
	 *
	 * @param string $item
	 *
	 * @throws snippy\sysLog\LogWriterException
	 */
	protected abstract function write( $item );
}