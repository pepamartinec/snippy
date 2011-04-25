<?php
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
		const EX_STANDARD = 'standard';
		const EX_SCREEN   = 'screen';
		
		/**
		 * @var cErrorHandler
		 */
		protected static $instance = null;

		/**
		 * @var iOutputWriter
		 */
		protected $writer;

		/**
		 * Contructor
		 *
		 * @param array $conf
		 *
		 * @throws xErrorHandlerException
		 */
		protected function __construct( array $conf )
		{
			// writer
			if( isset( $conf['writer'] ) === false ) {
				throw new xErrorHandlerException('Writer conf is missing');
				
			} elseif( $conf['writer'] instanceof iOutputWriter === false ) {
				throw new xErrorHandlerException('Invalid writer type, \'iOutputWriter\' is expected');
			}
			
			$this->writer = $conf['writer'];

			// register handlers
			$errorLevel = ( isset( $conf['errorLevel'] ) ) ? $conf['errorLevel'] : -1;
			set_error_handler( array( $this, 'errorWriteItem' ), $errorLevel );
			
			switch( $conf['exceptionWriter'] ) {
				default:
				case self::EX_SCREEN:
					set_exception_handler( array( $this, 'exceptionDisplayScreen' ) );
					break;
					
				case self::EX_STANDARD:
					set_exception_handler( array( $this, 'exceptionWriteItem' ) );
					break;
			}
			
			register_shutdown_function( array( $this, 'shutdownHandle' ) );
			
			// override default PHP error reporting and XDebug
//			ini_set( 'error_reporting', 0 );
//			ini_set( 'display_errors', false );
//			ini_set( 'track_errors', 1 );
//			if( is_callable( 'xdebug_disable' ) ) {
//				xdebug_disable();
//			}
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
				throw new \Exception('handler not initilaized yet'); //TODO custom exception
			}

			return self::$instance->writer->write( $item );
		}

		/**
		 * Writes catched error to default writer
		 *
		 * @param integer $type
		 * @param string $message
		 * @param string $file
		 * @param integer $line
		 */
		public function errorWriteItem( $type, $message, $file, $line, $context )
		{
			$trace = debug_backtrace();
			$top   = array_shift( $trace );

			$item = new outputItems\cSystemMessage( $type, $message, $file, $line, $context, $trace );
			$this->writer->write( $item );
		}

		/**
		 * Writes catched exceptions to default writer
		 *
		 * @param Exception $exception
		 */
		public function exceptionWriteItem( \Exception $exception )
		{
			$item = new outputItems\cException( $exception );
			$this->writer->write( $item );
		}
		
		/**
		 * Displays catched exception on ExceptionScreen
		 *
		 * @param Exception $exception
		 */
		public function exceptionDisplayScreen( \Exception $exception )
		{
			$screen = new cExceptionScreen( new cHTMLFormater(), $exception );
			$screen->display();
		}

		/**
		 * Script shutdown handler
		 *
		 * Workaround for catching fatal errors
		 */
		public function shutdownHandle()
		{
			$err = error_get_last();

			if( $err && ( $err['type'] & E_UNCATCHABLE ) ) {
				$item = new outputItems\cSystemMessage( $err['type'], $err['message'], $err['file'], $err['line'] );
				$this->writer->write( $item );

				// destructors are not called when fatal error occurs
				// workaround for valid logWriter shutdown
				// eg. in case of some caching/lazy-writing mechanism use
				if( is_callable( array( $this->writer, '__destruct' ) ) ) {
					$this->writer->__destruct();
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

		cErrorHandler::write( $item );
	}
}