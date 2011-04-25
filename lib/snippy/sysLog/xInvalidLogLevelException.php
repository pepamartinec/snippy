<?php
namespace snippy\sysLog;

class xInvalidLogLevelException extends xLogException
{
	public function __construct( $level )
	{
		parent::__construct( "'{$level}' is not valid log level" );
	}
}