<?php
namespace snippy\devConsole;

class cDevConsole
{
	const ELEMENT_ID  = 'devConsole';
	const SESSION_KEY = '__devConsole__';

	const IMG_DATE = 'images/reqDate.png';
	const IMG_TIME = 'images/reqTime.png';
	const IMG_MEM ='images/reqMem.png';

	const STYLESHEET = 'css/LogBar.css';
	const JAVASCRIPT = 'js/LogBar.js';

	/**
	 * @var array
	 */
	protected $panels;

	/**
	 * @var float
	 */
	protected $startTime;

	/**
	 * @var cLogBar
	 */
	private static $instance = null;

	/**
	 * Private constructor
	 */
	private function __construct()
	{
		$this->panels    = array();
		$this->startTime = microtime( true );
	}

	private function __clone() {}

	/**
	 * Returns instance of cLogBar
	 *
	 * @return cDevConsole
	 */
	public static final function getInstance()
	{
		if( self::$instance === null ) {
			self::$instance = new self();
			
			// restore any non-rendered bar
		}

		return self::$instance;
	}

	/**
	 * Restores bar from previous script
	 */
//	private function restorePrevious()
//	{
//		session_start();
//
//		if( isset( $_SESSION[ self::SESSION_KEY ] ) ) {
//			$prevBars = $_SESSION[ self::SESSION_KEY ];
//
//			foreach( $prevBars as $prevBar ) {
//				foreach( $prevBar as $panelStr ) {
//					$panel = unserialize( $panelStr );
//
//					if( !isset( $this->panels[$panel] ) )
//						$this->addPanel( new get_class( $panel ) );
//
//					// restore old panel
//					$this->panels[ $panel->getID() ]->restorePrevious( $panel );
//				}
//			}
//		}
//	}

	/**
	 * Adds panel on bar
	 *
	 * @param aLogBarPanel $panel
	 */
	public function addPanel( iDevConsolePanel $panel )
	{
		$this->panels[ $panel->getID() ] = $panel;
	}

	/**
	 * Renders panel content
	 */
	public function render()
	{
		$elementID = self::ELEMENT_ID;

		$time = number_format( ( microtime( true ) - $this->startTime ) * 1000, 1, '.', ' ' );
		$mem  = number_format( memory_get_peak_usage( true ) / 1000, 1, '.', ' ' );
		$date = date_format( new \DateTime( 't'.date('H.i.s.', $this->startTime) ), 'H:i:s.u' );
		
		echo "<div id=\"{$elementID}\">";
		
			echo '<ul>';
			foreach( $this->panels as $panel ) {
				echo "<li><a href=\"#{$elementID}_{$panel->getID()}\">{$panel->getTitle()}</a></li>";
			}
			echo '</ul>';
		
			foreach( $this->panels as $panel ) {
				echo "<div id=\"{$elementID}_{$panel->getID()}\">{$panel->render()}</div>";
			}

//
//			echo "<div class=\"header\">";
//			echo "<h2>LazyBar</h2><img src=\"".self::IMG_DATE."\" /> {$date} <img src=\"".self::IMG_TIME."\" /> {$time}ms <img src=\"".self::IMG_MEM."\" /> {$mem}kB";
//			echo "</div>";

		echo "</div>";
	}

}