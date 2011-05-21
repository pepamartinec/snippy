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

class cTrace extends aBaseItem
{
	/**
	 * @var string
	 */
	protected $trace;

	/**
	 * Constructor
	 *
	 * @param array $trace
	 */
	public function __construct( array $trace )
	{
		$this->trace = $trace;
	}

	/**
	 * Returns items
	 *
	 * @return array
	 */
	public function getTrace()
	{
		return $this->trace;
	}

	/**
	 * Renders and returns message content
	 *
	 * @param  HTMLFormater $formater
	 * @return string
	 */
	public function render( cHTMLFormater $formater )
	{
		$tpl = new cOutputItemTemplate( cOutputItemTemplate::C_DEBUG );
		$tpl->setContent( $formater->formatTrace( $this->trace ) );

		$this->applyInvokePositionToTemplate( $tpl );
		
		return $tpl->render( $formater );
	}
}