<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>LazyLog2</title>
	<link href="lib/snippy/debug/resources/debug.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="lib/snippy/debug/resources/debug.js"></script>
</head>
<body>
<?php

ob_start();

use snippy\debug\ideUrlGenerators\cNoIdeGenerator;
use snippy\debug\ideUrlGenerators\cEclipseGenerator;
use snippy\debug\outputWriters\cExceptionScreenWriter;
use snippy\debug\outputWriters\cBlackHoleWriter;
use snippy\debug\outputItems\cException;
use snippy\debug\cHTMLFormater;
use snippy\debug\outputWriters\cInPageWriter;
use snippy\debug\cErrorHandler;
use snippy\debug\screens\cInfoScreen;
use snippy\debug\screens\cExceptionScreen;

define( 'DEBUG', true );
define( 'ERROR_LEVEL', -1 );

include_once 'common.php';



$formater = new cHTMLFormater( new cEclipseGenerator( 'localhost:34567' ) );
cErrorHandler::init( array(
	'debugWriter' => new cInPageWriter( $formater ),
	'errorLevel'  => E_ALL,

	'exceptionWriter' => new cExceptionScreenWriter( $formater ),
) );




$c = $d + 7;

function a( $aa )
{
//	include 'invalidInclude.php';
	
	$formater = new cHTMLFormater( new cEclipseGenerator( 'localhost:34567' ) );

	$bs = new cInfoScreen( $formater );
	b($formater, $bs);
}

function b( $x, cInfoScreen $y )
{
	trace();
//	throw new Exception('Let me say you something \'Hello\' :)');
	
	//$y->display();
}

session_start();

a( array( 1 => 5, 7 => array( 5,3,4 ) ) );


$conf = array(
	'default' => array(
		'writer'       => 'file',
		'outputFile'   => 'log/global.log',
		'itemMask'     => '%d %m [%l] - %c',
		'itemMaskDate' => 'Y-m-d H:i:s',
	),

	'LH' => array(
		'writer'         => 'file',
		'outputFile'     => 'log/%D_global.log',
		'outputFileDate' => 'Y-m-d',
		'itemMask'       => '%d %m [%l] - %c',
		'itemMaskDate'   => 'Y-m-d H:i:s',
	),

	'snippy\sysLog' => array(
		'writer'         => 'file',
		'outputFile'     => 'log/%D_Log.log',
		'outputFileDate' => 'Y-m-d',
		'itemMask'       => '%d %m [%l] - %c',
		'itemMaskDate'   => 'Y-m-d H:i:s',
	),
);

dump($conf);


info( 'Yes, we are here!!!' );

$c = $x / 0;


ob_end_flush();

?>
</body>
</html>