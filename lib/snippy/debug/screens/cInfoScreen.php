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

namespace snippy\debug\screens;

use snippy\debug\iOutputItem;

use snippy\debug\cHTMLFormater;

class cInfoScreen extends aInfoScreen
{
	/**
	 * @var HTMLFormater
	 */
	protected $formater;

	/**
	 * @var array
	 */
	protected $trace;

	/**
	 * Constructor
	 *
	 * @param Formater $formater
	 */
	public function __construct( cHTMLFormater $formater )
	{
		parent::__construct( $formater );
		
		$this->trace = debug_backtrace();
		array_shift( $this->trace ); // remove '__construct' call
	}

	/**
	 * Renders and returns screen content
	 *
	 * @return string
	 */
	protected function render()
	{
		ob_start();
		include __DIR__.'/templates/infoScreen.php';
		return ob_get_clean();
	}
}