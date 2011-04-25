<?php
namespace snippy\debug\screens;

use snippy\debug\cHTMLFormater;

use snippy\debug\iInfoScreen;

abstract class aInfoScreen implements iInfoScreen
{
	/**
	 * @var HTMLFormater
	 */
	protected $formater;
	
	/**
	 * Path to images, JS & CSS files
	 *
	 * @var string
	 */
	protected $resourcesPath;
	
	/**
	 * @var array
	 */
	protected $items;
	
	/**
	 * Constructor
	 *
	 */
	public function __construct( cHTMLFormater $formater )
	{
		$this->formater      = $formater;
		$this->resourcesPath = $this->intersectFilenames( $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'], __DIR__.'/../resources/' );
		$this->items = array();
	}
	
	/**
	 * Displays info screen and terminates script
	 */
	public function display()
	{
		while( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		echo $this->render();
		exit;
	}

	/**
	 * Saves infoScreen into file
	 *
	 * @param string $fileName
	 */
	public function save( $fileName )
	{
		file_put_contents( $fileName, $this->render() );
	}
	
	/**
	 * Prints dynamic content of a screen
	 *
	 * Used internally within templates
	 */
	public function printContent()
	{
		foreach( $this->items as $item ) {
			echo $item->render( $this->formater );
		}
	}

	/**
	 * Adds item as content of screen
	 *
	 * @param iOutputItem $message
	 */
	public function addItem( iOutputItem $item )
	{
		$this->items[] = $item;
	}
	
	/**
	 * Renders screen content
	 *
	 * @return string
	 */
	protected abstract function render();
	
	/**
	 * Calculates relative path from one file to another
	 *
	 * @param  string $baseFilename    referenced file name
	 * @param  string $relatedFilename referencing file name
	 * @return string
	 */
	protected function intersectFilenames( $baseFilename, $relatedFilename )
	{
		$baseParts    = explode( '/', $baseFilename );
		$relatedParts = explode( '/', $relatedFilename );

		$minL = min( sizeof( $baseParts ) - 1, sizeof( $relatedParts ) ) + 1;
		for( $i = 0; $i < $minL; ++$i ) {
			if( $baseParts[$i] != $relatedParts[$i] ) {
				break;
			}
		};

		return str_repeat( '../', sizeof( $baseParts ) - $i - 1 ) .
		       implode( '/', array_slice( $relatedParts, $i ) );
	}
}