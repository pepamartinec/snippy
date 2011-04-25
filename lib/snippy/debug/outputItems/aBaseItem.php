<?php
namespace snippy\debug\outputItems;

use snippy\debug\cOutputItemTemplate;

use snippy\debug\iOutputItem;

abstract class aBaseItem implements iOutputItem
{
	/**
	 * @var array array( $file, $line )
	 */
	protected $invokePosition = array();
	
	/**
	 * Sets position of message invokation
	 *
	 * @param string $file
	 * @param int    $line
	 */
	public function setInvokePosition( $file, $line )
	{
		$this->invokePosition = array( $file, $line );
	}
	
	/**
	 * Returns position of message invokation
	 *
	 * @return array array( $file, $line )
	 */
	public function getInvokePosition()
	{
		return $this->invokePosition;
	}
	
	/**
	 * Applies invoke position as file setting to output template
	 *
	 * @param cOutputItemTemplate $tpl
	 */
	protected function applyInvokePositionToTemplate( cOutputItemTemplate $tpl )
	{
		$file = isset( $this->invokePosition[0] ) ? $this->invokePosition[0] : null;
		$line = isset( $this->invokePosition[1] ) ? $this->invokePosition[1] : null;
		
		if( $file !== null ) {
			$tpl->setFile( $file, $line );
		}
	}
}