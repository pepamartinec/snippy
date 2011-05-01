<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>LazyLog2</title>
	<link href="styles/debug.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
use snippy\devConsole\panels\cDebugPanel;
use snippy\devConsole\cDevConsole;
use snippy\debug\ideUrlGenerators\cEclipseGenerator;
use snippy\sysLog\cLogFactory;

ob_start();

include_once 'common.php';



use snippy\debug\cHTMLFormater;
use snippy\debug\outputWriters\cExceptionScreenWriter;
use snippy\debug\outputWriters\cInPageWriter;
use snippy\debug\cErrorHandler;

$formater = new cHTMLFormater( new cEclipseGenerator( 'localhost:34567' ) );
$debugWriter = new cDebugPanel( $formater );
cErrorHandler::init( array(
	'debugWriter' => $debugWriter,
	'errorLevel'  => E_ALL,

	'exceptionWriter' => new cExceptionScreenWriter( $formater ),
) );


$dc = cDevConsole::getInstance();
$dc->addPanel( $debugWriter );

$c = $d + 7;

$dc->render();

ob_end_flush();

?>
</body>
</html>