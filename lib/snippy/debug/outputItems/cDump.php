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

class cDump extends aBaseItem
{
	/**
	 * @var string
	 */
	protected $items;

	/**
	 * Constructor
	 *
	 * @param array $variable
	 */
	public function __construct( array $items )
	{
		$this->items = $items;
	}

	/**
	 * Returns items
	 *
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}

	/**
	 * Renders and returns message content
	 *
	 * @param  HTMLFormater $formater
	 * @return string
	 */
	public function render( cHTMLFormater $formater )
	{
		$content = '';
		foreach( $this->items as $item ) {
			$content .= $formater->formatVariable( $item, false );
		}

		$tpl = new cOutputItemTemplate( cOutputItemTemplate::C_DEBUG );
		$tpl->setContent( $content );
		
		$this->applyInvokePositionToTemplate( $tpl );

		return $tpl->render( $formater );
	}
}