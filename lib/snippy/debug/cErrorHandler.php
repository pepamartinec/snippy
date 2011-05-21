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

namespace snippy\debug {

	use snippy\debug\screens\cExceptionScreen;
	use snippy\debug\outputItems\cMessage;

	if( defined( 'DEBUG' ) !== true ) {
		define( 'DEBUG', false );
	}

	if( defined( 'ERROR_LEVEL' ) !== true ) {
		define( 'ERROR_LEVEL', E_ALL );
	}

	define( 'E_UNCATCHABLE', E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_STRICT );

	/**
	 * System error handler
	 *
	 */
	class cErrorHandler
	{
		/**
		 * @var cErrorHandler
		 */
		protected static $instance = null;

		/**
		 * @var iOutputWriter
		 */
		protected $debugWriter;
		
		/**
		 * @var iOutputWriter
		 */
		protected $exceptionWriter;

		/**
		 * Contructor
		 *
		 * @param array $conf
		 *
		 * @throws xErrorHandlerException
		 */
		protected function __construct( array $conf )
		{
			// error handler
			if( isset( $conf['debugWriter'] ) === false ) {
				throw new xErrorHandlerException('Error writer conf is missing');
				
			} elseif( $conf['debugWriter'] instanceof iOutputWriter === false ) {
				throw new xErrorHandlerException('Invalid error writer type, \'iOutputWriter\' is expected');
			}
			
			$this->debugWriter = $conf['debugWriter'];
			
			$errorLevel = ( isset( $conf['errorLevel'] ) ) ? $conf['errorLevel'] : E_ALL;
			set_error_handler( array( $this, 'errorCallback' ), $errorLevel );
			register_shutdown_function( array( $this, 'shutdownCallback' ) );

			// exception handler
			if( isset( $conf['exceptionWriter'] ) === false ) {
				throw new xErrorHandlerException('Exception writer conf is missing');
				
			} elseif( $conf['exceptionWriter'] instanceof iOutputWriter === false ) {
				throw new xErrorHandlerException('Invalid exception writer type, \'iOutputWriter\' is expected');
			}
			
			$this->exceptionWriter = $conf['exceptionWriter'];

			set_exception_handler( array( $this, 'exceptionCallback' ) );
			
			
			// override default PHP error reporting and XDebug
			ini_set( 'error_reporting', 0 );
			ini_set( 'display_errors', false );
			ini_set( 'track_errors', 1 );
			if( is_callable( 'xdebug_disable' ) ) {
				xdebug_disable();
			}
		}
		
		/**
		 * Initializes error handler
		 *
		 * @param array $conf
		 *
		 * @throws xErrorHandlerException
		 */
		public static function init( array $conf )
		{
			// check instance
			if( self::$instance !== null ) {
				throw new xErrorHandlerException('Handler has already been initilaized');
			}

			self::$instance = new self( $conf );
		}

		/**
		 * Returns default output writer
		 *
		 * @return iOutputWriter
		 */
		public static function write( iOutputItem $item )
		{
			// check instance
			if( self::$instance === null ) {
				throw new xErrorHandlerException('Handler has not been initialized yet');
			}

			return self::$instance->debugWriter->write( $item );
		}

		/**
		 * Writes catched noticec & warnings to debug writer
		 *
		 * @param integer $type
		 * @param string $message
		 * @param string $file
		 * @param integer $line
		 */
		public function errorCallback( $type, $message, $file, $line, $context )
		{
			if( $type & E_UNCATCHABLE ) {
				$item = new outputItems\cException( new \ErrorException( $message, 0, $type, $file, $line ) );
				$this->exceptionWriter->write( $item );
				
			} else {
				$trace = debug_backtrace();
				$top   = array_shift( $trace );
	
				$item = new outputItems\cSystemMessage( $type, $message, $file, $line, $context, $trace );
				$this->debugWriter->write( $item );
			}
		}

		/**
		 * Writes catched exceptions to exception writer
		 *
		 * @param Exception $exception
		 */
		public function exceptionCallback( \Exception $exception )
		{
			$item = new outputItems\cException( $exception );
			$this->exceptionWriter->write( $item );
		}

		/**
		 * Script shutdown handler
		 *
		 * Workaround for catching fatal errors
		 */
		public function shutdownCallback()
		{

			$err = error_get_last();
			
			if( $err && ( $err['type'] & E_UNCATCHABLE ) ) {
				$item = new outputItems\cException( new \ErrorException( $err['message'], 0, $err['type'], $err['file'], $err['line'] ) );
				$this->exceptionWriter->write( $item );

				// destructors are not called when fatal error occurs
				// workaround for valid logWriter shutdown
				// eg. in case of some caching/lazy-writing mechanism use
				if( is_callable( array( $this->exceptionWriter, '__destruct' ) ) ) {
					$this->exceptionWriter->__destruct();
				}
			}
		}
	}
}

namespace {
	use snippy\debug\cErrorHandler;
	use snippy\debug\outputItems\cMessage;
	use snippy\debug\outputItems\cDump;
	use snippy\debug\outputItems\cTrace;

	/**
	 * Prints trace on default log output
	 */
	function trace()
	{
		$trace = debug_backtrace();
		$top   = array_shift( $trace );

		$item = new cTrace( $trace );
		$item->setInvokePosition( $top['file'], $top['line'] );

		cErrorHandler::write( $item );
	}

	/**
	 * Dumps given varibales on default log output
	 * Accepts custom number of arguments
	 *
	 * @param mixed $variable,...
	 */
	function dump( $variable )
	{
		$item = new cDump( func_get_args() );

		$trace = debug_backtrace();
		$top   = array_shift( $trace );
		$item->setInvokePosition( $top['file'], $top['line'] );
		
		cErrorHandler::write( $item );
	}

	/**
	 * Prints given message on default log output
	 *
	 * @param $message
	 */
	function info( $message )
	{
		$item = new cMessage( cMessage::DEBUG, $message );
		
		$trace = debug_backtrace();
		$top   = array_shift( $trace );
		$item->setInvokePosition( $top['file'], $top['line'] );

		cErrorHandler::write( $item );
	}
}