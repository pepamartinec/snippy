<?php

use snippy\sysLog\writers\cFileWriter;

class cFileWriterTest extends PHPUnit_Framework_TestCase
{
	public function testModuleNameGetter()
	{
		$writer = new cFileWriter( 'testModule', 'test.log' );
	
		$this->assertTrue( $writer->getModuleName() === 'testModule' );
	}
	
	public function testFileCreation()
	{
		$base = __DIR__ .'/../../../..';
		
		$writer = new cFileWriter('testModule',  $base.'/test.log' );
		$writer->debug( 'test' );
		
		$this->assertFileExists( './test.log' );
	}
}