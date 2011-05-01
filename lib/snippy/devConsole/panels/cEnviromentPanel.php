<?php
namespace snippy\devConsole\panels;

use snippy\devConsole\iDevConsolePanel;

class cEnviromentPanel implements iDevConsolePanel
{
	/**
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param cLogBar|null $logBar
	 */
	public function __construct()
	{
		$constants = get_defined_constants( true );

		$this->data['$_SESSION'] = $_SESSION;
		$this->data['$_SERVER']  = $_SERVER;
		$this->data['$_POST']    = $_POST;
		$this->data['$_GET']     = $_GET;
		$this->data['$_COOKIE']  = $_COOKIE;
	}

	/**
	 * Returns panel unique ID
	 */
	public function getID()
	{
		return 'env';
	}

	/**
	 * Returns name of panel
	 */
	public function getTitle()
	{
		return 'Enviroment';
	}

	/**
	 * Renders panel content
	 */
	public function render()
	{
		$requestHeaders = array();
		foreach( $this->data['$_SERVER'] as $k => $v ) {
			if( substr( $k, 0, 5 ) != 'HTTP_' )
				continue;

			$name = substr( $k, 5 );
			$name = str_replace( '_', ' ', $name );
			$name = ucwords( strtolower( $name ) );
			$name = str_replace( ' ', '-', $name );

			$requestHeaders[$name] = $v;
		}

		$responseHeaders = headers_list();

		$constants = get_defined_constants( true );
		$settings  = $constants['user'];



		$columnStyle = 'vertical-align: top; padding: 3px 7px; '.iLogAsHTML::COLOR_GRAY;

		echo "<fieldset><legend>Request</legend>";
		$this->printVars( array(
			'$_POST'   => $this->data['$_POST'],
			'$_GET'    => $this->data['$_GET'],
			'$_COOKIE' => $this->data['$_COOKIE'],
			'Request headers' => $requestHeaders
		));
		echo '</fieldset>';

		echo "<fieldset><legend>Response</legend>";
		$this->printVars( array(
			'Response headers' => $responseHeaders
		));
		echo '</fieldset>';

		echo "<fieldset><legend>Enviroment</legend>";
		$this->printVars( array(
			'$_SESSION' => $this->data['$_SESSION'],
			'$_SERVER'  => $this->data['$_SERVER'],
			'Settings'  => $settings
		));
		echo '</fieldset>';
	}

	protected function printVars( $data )
	{
		echo '<table><tr>';
		foreach( $data as $title => $values  )
			if( $values !== null )
				echo "<th>{$title}</th>";

		echo '</tr><tr>';

		foreach( $data as $title => $values  )
			if( $values !== null )
				echo '<td>'.cLogWriterHTML::listVertical( $values ).'</td>';

		echo '</tr></table>';
	}
}