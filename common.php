<?php

function autoload( $className )
{
	$dir       = __DIR__ . '/lib/';
	$nameParts = explode( '\\', $className );
	$filePath  = $dir . implode( '/', $nameParts ) .'.php';

	if( is_file( $filePath ) ) {
		include $filePath;
		
	} else {
		return false;
	}
}

spl_autoload_register( 'autoload' );
