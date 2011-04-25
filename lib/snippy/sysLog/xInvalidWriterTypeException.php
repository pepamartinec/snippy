<?php
namespace snippy\sysLog;

class xInvalidWriterTypeException extends xLogFactoryException
{
	public function __construct( $writerType )
	{
		parent::__construct( "'{$writerType}' is not a valid log writer type" );
	}
}