<?php
namespace snippy\debug\outputItems;

use snippy\debug\cOutputItemTemplate;
use snippy\debug\cHTMLFormater;
use snippy\debug\iOutputItem;

class cDump implements iOutputItem
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

		return $tpl->render( $formater );
	}
}