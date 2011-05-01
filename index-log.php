<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>LazyLog2</title>
	<link href="styles/debug.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
use snippy\sysLog\cLogFactory;

ob_start();

include_once 'common.php';



use snippy\debug\cHTMLFormater;
use snippy\debug\outputWriters\cExceptionScreenWriter;
use snippy\debug\outputWriters\cInPageWriter;
use snippy\debug\cErrorHandler;

$formater = new cHTMLFormater();
cErrorHandler::init( array(
	'debugWriter' => new cInPageWriter( $formater ),
	'errorLevel'  => E_ALL,

	'exceptionWriter' => new cExceptionScreenWriter( $formater ),
) );


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

cLogFactory::init( $conf );

$log = cLogFactory::getLog( 'snippy\sysLog' );
$log->error( 'Ouch!!!!' );
$log->error( 'More Ouch!!!!' );
$log->debug( 'Good :)' );

$log = cLogFactory::getLog( 'LH\Util' );
$log->error( 'Ouch!!!!' );
$log->error( 'More Ouch!!!!' );
$log->debug( 'Good :)' );

$log = cLogFactory::getLog( 'LH' );
$log->error( 'Ouch!!!!' );
$log->error( 'More Ouch!!!!' );
$log->debug( 'Good :)' );

ob_end_flush();

?>
</body>
</html>