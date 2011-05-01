<?php
namespace snippy\sysLog;

use snippy\sysLog\writers\cLazyConstructWriterProxy;

use snippy\sysLog\writers\cBlackHoleWriter;
use snippy\sysLog\writers\cFileWriter;

/**
 * Log factory
 *
 * @singleton
 * @author Josef Martinec
 */
class cLogFactory
{
	const LOG_GLOBAL = 'GLOBAL';
	
	/**
	 * Configuration
	 *
	 * @var array
	 */
	protected $conf;

	/**
	 * Existing log writers
	 *
	 * @var array
	 */
	protected $writers;

	/**
	 * Unique request ID
	 *
	 * @var string
	 */
	protected $sessionID;

	/**
	 * Unique request ID
	 *
	 * @var string
	 */
	protected $requestID;

	/**
	 * Current request time
	 *
	 * @var float
	 */
	protected $requestTime;

	/**
	 * Singleton instance
	 *
	 * @var cLogFactory
	 */
	private static $instance = null;

	/**
	 * Private constructor
	 * (public construction disabled)
	 */
	private function __construct( array $conf )
	{
		if( session_id() === '' ) {
			session_start();
		}

		$this->conf        = $conf;
		$this->writers     = array();
		$this->sessionID   = session_id();
		$this->requestID   = uniqid();
		$this->requestTime = time( true );
	}

	/**
	 * Private copy-contructor
	 * (copy-construction disabled)
	 */
	private function __clone() {}

	/**
	 * cLogFactory static initialization
	 *
	 * @param array $conf
	 *
	 * @throws snippy\sysLog\xLogFactoryException
	 */
	public static function init( array $conf )
	{
		if( self::$instance !== null ) {
			throw new xLogFactoryException( 'cLogFactory has already been initialized' );
		}

		$conf['default'] = array(
			'writer'         => 'stream',
			'outputFile'     => 'log/%D-global.log',
			'outputFileDate' => 'Y-m-d'
		);

		self::$instance = new self( $conf );
	}

	/**
	 * Returns log writer for given module
	 *
	 * @param  string     $moduleName
	 * @return iLogWriter
	 *
	 * @throws snippy\sysLog\xLogFactoryException
	 * @throws snippy\sysLog\xWriterConstructionException
	 */
	public static function getLog( $moduleName )
	{
		if( self::$instance === null ) {
			throw new xLogFactoryException( 'cLogFactory has to be initialized first' );
		}
		
		$factory = self::$instance;

		if( isset( $factory->writers[ $moduleName ] ) === false ) {
			// get module conf
			$confName = $moduleName;
			while( isset( $factory->conf[ $confName ] ) === false &&
				( $localNameLen = strrpos( $confName, '\\' ) ) !== false ) {
	
				$confName = substr( $confName, 0, $localNameLen );
			}
	
			$conf = $factory->conf[ $confName ] ?: $factory->conf['default'];
			
			$factory->writers[ $moduleName ] = new cLazyConstructWriterProxy( $factory, $moduleName, $conf );
		}

		return $factory->writers[ $moduleName ];
	}
	
	/**
	 * Replaces module logWriter with new one
	 *
	 * @param string     $moduleName
	 * @param iLogWriter $writer
	 */
	public function setWriter( $moduleName, iLogWriter $writer )
	{
		$this->writers[ $moduleName ] = $writer;
	}

	/**
	 * Returns log writer for given module
	 *
	 * @param string $moduleName
	 * @param array  $conf
	 *
	 * @throws snippy\sysLog\xWriterConstructionException
	 */
	public function createWriter( $moduleName, array $conf )
	{
		// create writer
		if( !isset( $conf['writer'] ) ) {
			throw new xInvalidConfException( "'writer' configuration is missing for module '{$moduleName}'" );
		}

		switch( $conf['writer'] ) {
			case 'file':
				$writer = $this->createFileWriter( $moduleName, $conf );
				break;

			case 'null':
			case 'blackHole':
				$writer = $this->createBlackHoleWriter();
				break;

			default:
				throw new xInvalidConfException( "Invalid writer type '{$conf['writer']}'" );
		}

		return $writer;
	}

	/**
	 * Creates new stream writer based on given configuration
	 *
	 * @param  string $moduleName
	 * @param  array  $conf
	 * @return Stream
	 *
	 * @throws snippy\sysLog\xWriterConstructionException
	 */
	protected function createFileWriter( $moduleName, array $conf )
	{
		if( !isset( $conf['outputFile'] ) ) {
			throw new xInvalidConfException( "'output' configuration missing" );
		}

		// format output fileName
		$outFormat = $conf['outputFile'];
		$outDate   = $conf['outputFileDate'] ?: null;

		if( strpos( $outFormat, '%D' ) !== false && $outDate === null ) {
			throw new xInvalidConfException( "Missing dateFormat definition for output file" );
		}

		$outReplace = array(
			'%S' => $this->sessionID,
			'%R' => $this->requestID,
			'%D' => $outDate ? date( $outDate, $this->requestTime ) : null,
		);
		$outFile = str_replace( array_keys( $outReplace ), $outReplace, $outFormat );

		// create & setup writer
		$writer = new cFileWriter( $moduleName, $outFile );
		$writer->setExternalPlaceholders( $outReplace );

		if( isset( $conf['itemMask'] ) ) {
			$writer->setItemMask( $conf['itemMask'], $conf['itemMaskDate'] ?: null );
		}

		return $writer;
	}

	/**
	 * Returns instace of BlackHole log writer
	 *
	 * Internaly, just one writer instance is created, because
	 * it can be safely shared between all clients
	 *
	 * @return cBlackHoleWriter
	 */
	protected function createBlackHoleWriter()
	{
		// writer can by shared
		static $writer = null;
		
		if( $writer === null ) {
			$writer = new cBlackHoleWriter();
		}

		return $writer;
	}
}