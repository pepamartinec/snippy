<?php
namespace snippy\debug\screens;

use snippy\debug\cHTMLFormater;

class cExceptionScreen extends aInfoScreen
{
	/**
	 * @var \Exception
	 */
	protected $exception;

	/**
	 * Constructor
	 *
	 * @param cHTMLFormater $formater
	 * @param \Exception    $exception
	 */
	public function __construct( cHTMLFormater $formater, \Exception $exception )
	{
		parent::__construct( $formater );
		
		$this->exception = $exception;
	}
	
	/**
	 * Renders and returns screen content
	 *
	 * @return string
	 */
	protected function render()
	{
		ob_start();
		include __DIR__.'/templates/exceptionScreen.php';
		return ob_get_clean();
	}
}