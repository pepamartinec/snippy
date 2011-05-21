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

class cMessage extends aBaseItem
{
	const DEBUG = 'debug';
	const WARN  = 'warn';
	const ERROR = 'error';

	/**
	 * @var int
	 */
	protected $level;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * Constructor
	 *
	 * @param string $content message content
	 */
	public function __construct( $level, $content )
	{
		$this->level   = $level;
		$this->content = $content;
	}

	/**
	 * Returns message severity
	 *
	 * @return int
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * Returns message content
	 *
	 * @return string
	 */
	public function getContent()
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
		$tpl = new cOutputItemTemplate( $this->level );
		$tpl->setContent( $this->content );
		
		$this->applyInvokePositionToTemplate( $tpl );

		return $tpl->render( $formater );
	}
}