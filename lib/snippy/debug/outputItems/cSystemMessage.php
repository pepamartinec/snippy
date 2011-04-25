<?php
namespace snippy\debug\outputItems;

use snippy\debug\cOutputItemTemplate;
use snippy\debug\cHTMLFormater;

class cSystemMessage extends aBaseItem
{
	/**
	 * @var int
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $file;

	/**
	 * @var int
	 */
	protected $line;

	/**
	 * @var array|null
	 */
	protected $context;

	/**
	 * @var array|null
	 */
	protected $trace;

	/**
	 * Constructor
	 *
	 * @param string $content message content
	 */
	public function __construct( $type, $message, $file, $line, array $context = null, array $trace = null )
	{
		$this->type    = $type;
		$this->message = $message;
		$this->file    = $file;
		$this->line    = $line;
		$this->context = $context;
		$this->trace   = $trace;
	}

	/**
	 * Returns message class
	 *
	 * @param  int $type
	 * @return string
	 */
	protected static function getClass( $type )
	{
		switch( $type ) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
			default:
				return cOutputItemTemplate::C_ERROR;


			case E_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
			case E_USER_WARNING:
			case E_STRICT:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				return cOutputItemTemplate::C_WARN;


			case E_NOTICE:
			case E_USER_NOTICE:
				return cOutputItemTemplate::C_INFO;
		}
	}

	/**
	 * Returns human-readable type
	 *
	 * @param  int $type
	 * @return string
	 */
	protected static function getTypeName( $type )
	{
		switch( $type ) {
			case E_ERROR:
				return 'Fatal Error';

			case E_WARNING:
				return 'System Warning';

			case E_PARSE:
				return 'Parse Error';

			case E_NOTICE:
				return 'System Notice';

			case E_CORE_ERROR:
				return 'Core Error';

			case E_CORE_WARNING:
				return 'Core Warning';

			case E_COMPILE_ERROR:
				return 'Compile Error';

			case E_COMPILE_WARNING:
				return 'Compile Warning';

			case E_USER_ERROR:
				return 'User Error';

			case E_USER_WARNING:
				return 'User Warning';

			case E_USER_NOTICE:
				return 'User Notice';

			case E_STRICT:
				return 'Strict Warning';

			case E_RECOVERABLE_ERROR:
				return 'Recoverable Error';

			case E_DEPRECATED:
				return 'System Deprecated';

			case E_USER_DEPRECATED:
				return 'User Deprecated';

			default:
				return 'Unknown Error';
		}
	}

	/**
	 * Renders and returns message content
	 *
	 * @param  HTMLFormater $formater
	 * @return string
	 */
	public function render( cHTMLFormater $formater )
	{
		$baseClass = self::getClass( $this->type );

		$collapsers = array();
		if( $this->context !== null ) {
			$collapsers[] = $formater->createToggleBlock( 'context', $formater->formatListHorizontal( $this->context ) );
		}
		
		if( $this->trace !== null ) {
			$formater->createToggleBlock( 'trace', $formater->formatTrace( $this->trace ), $baseClass === cOutputItemTemplate::C_INFO );
		}

		$tpl = new cOutputItemTemplate( $baseClass );
		$tpl->setTitle( self::getTypeName( $this->type ) )
		    ->setContent( $this->message )
		    ->setFile( $this->file, $this->line )
		    ->setCollapsers( $collapsers );

		return $tpl->render( $formater );
	}
}