<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>LazyLog2</title>
	<link href="lib/snippy/debug/resources/debug.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="lib/snippy/debug/resources/debug.js"></script>
	
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<link href="lib/snippy/devConsole/resources/devConsole.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.js"></script>
	<script type="text/javascript" src="lib/snippy/devConsole/resources/devConsole.js"></script>
	
	<script type="text/javascript">

	$(document).ready(function() {
		var devConsole = $( "#devConsole" );
		
		devConsole.tabs({
			collapsible : true
		});
		$( "#devConsole, #devConsole .ui-tabs-nav, #devConsole .ui-tabs-nav > *" )
			.removeClass( "ui-corner-all ui-corner-bottom ui-corner-top" );

		devConsole.resizable({
			handles : 'nw',
			containment : 'document',
			minWidth : 600,
			minHeight : 150
		});
		devConsole.resizable('enable');
	});
	</script>
</head>
<body>
<?php
use snippy\devConsole\panels\cEnviromentPanel;
use snippy\devConsole\panels\cDebugPanel;
use snippy\devConsole\cDevConsole;
use snippy\debug\ideUrlGenerators\cEclipseGenerator;
use snippy\sysLog\cLogFactory;


include_once 'common.php';



use snippy\debug\cHTMLFormater;
use snippy\debug\outputWriters\cExceptionScreenWriter;
use snippy\debug\outputWriters\cInPageWriter;
use snippy\debug\cErrorHandler;

ob_start();

$formater = new cHTMLFormater( new cEclipseGenerator( 'localhost:34567' ) );
$debugWriter = new cDebugPanel( $formater );
cErrorHandler::init( array(
	'debugWriter' => $debugWriter,
	'errorLevel'  => E_ALL,

	'exceptionWriter' => new cExceptionScreenWriter( $formater ),
) );


$dc = cDevConsole::getInstance();
$dc->addPanel( $debugWriter );
$dc->addPanel( new cEnviromentPanel( $formater ) );

$c = $d + 7;

$dc->render();


ob_end_flush();

?>
</body>
</html>