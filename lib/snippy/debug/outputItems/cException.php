<?php
namespace snippy\debug\outputItems;

use snippy\debug\cOutputItemTemplate;
use snippy\debug\cHTMLFormater;
use snippy\debug\iOutputItem;

class cException implements iOutputItem
{
	/**
	 * @var \cException
	 */
	protected $exception;

	/**
	 * Constructor
	 *
	 * @param string $content message content
	 */
	public function __construct( \Exception $exception )
	{
		$this->exception = $exception;
	}

	/**
	 * Returns exception
	 *
	 * @return \cException
	 */
	public function getException()
	{
		return $this->content;
	}

	/**
	 * Renders and returns message content
	 *
	 * @param  HTMLFormater $formater
	 * @return string
	 */
	public function render( cHTMLFormater $formater )
	{
		$tpl = new cOutputItemTemplate( cOutputItemTemplate::C_ERROR );
		$tpl->setTitle( get_class( $this->exception ) )
		    ->setContent( $this->exception->getMessage() )
		    ->setFile( $this->exception->getFile(), $this->exception->getLine() )
		    ->setCollapsers( array(
		    	$formater->createToggleBlock( 'trace', $formater->formatTrace( $this->exception->getTrace() ), false )
		    ) );

		return $tpl->render( $formater );
	}
}