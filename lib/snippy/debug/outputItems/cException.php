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

namespace snippy\debug\outputItems;

use snippy\debug\cOutputItemTemplate;
use snippy\debug\cHTMLFormater;

class cException extends aBaseItem
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
		return $this->exception;
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