<?php
namespace snippy\sysLog\writers;

use snippy\sysLog\xLogWriterException;

/**
 * Stream log writer
 *
 * @author Josef Martinec
 */
class cFileWriter extends aPlainTextWriter
{
	/**
	 * Output fileName mask
	 *
	 * @var string
	 */
	protected $outputFile;

	/**
	 * Constructor
	 *
	 * @param string $outputFile
	 *
	 * @throws snippy\sysLog\xLogWriterException
	 */
	public function __construct( $moduleName, $outputFile )
	{
		parent::__construct( $moduleName );

		$this->outputFile = $outputFile;
		
		// create output dir
		$dirname = dirname( $outputFile );
		if( is_dir( $dirname ) === false && mkdir( $dirname, 0777, true ) === false ) {
			throw new xLogWriterException( error_get_last() );
		}
	}

	/**
	 * Writes given log item to output
	 *
	 * @param string $item
	 *
	 * @throws snippy\sysLog\xLogWriterException
	 */
	protected function write( $item )
	{
		// open log-file
		if( ( $out = fopen( $this->outputFile, 'a' ) ) === false ) {
			$error = error_get_last();
			throw new xLogWriterException( $error['message'] );
		}

		// write message
		if( fwrite( $out, $item . PHP_EOL ) === false ) {
			$error = error_get_last();
			throw new xLogWriterException( $error['message'] );
		}

		// close log-file
		if( fclose( $out ) === false ) {
			$error = error_get_last();
			throw new xLogWriterException( $error['message'] );
		}
	}
}