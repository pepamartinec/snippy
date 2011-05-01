<?php
namespace snippy\devConsole\panels;

use snippy\devConsole\iDevConsolePanel;
use snippy\debug\iOutputWriter;

class cSysLogPanel implements iDevConsolePanel, iOutputWriter
{
	const TITLE = 'System Log';

	/**
	 * @var array
	 */
	protected $items;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var \snippy\sysLog\iLogWriter
	 */
	protected $writer;

	/**
	 * Constructor
	 *
	 * @param string $title
	 */
	public function __construct( iLogWriter $writer )
	{
		$this->items  = array();
		$this->writer = $writer;
	}

	/**
	 * Returns panel unique ID
	 */
	public function getID()
	{
		return 'msgPanel_'.preg_replace( '/\s/', '_', self::TITLE );
	}

	/**
	 * Returns name of panel
	 */
	public function getTitle()
	{
		return self::TITLE;
	}

	/**
	 * Renders panel content
	 *
	 * @return string
	 */
	public function render()
	{
		ob_start();

		foreach( $this->items as $item )
			echo $item->render( $this->formater );

		return ob_get_clean();
	}

	/**
	 * Writes debug item to output
	 *
	 * @param iItem $item
	 */
	public function write( iOutputItem $item )
	{
		$this->items[] = $item;
	}
}