<?php
/**
 * This file is part of snippy.
 *
 * @author Josef Martinec <joker806@gmail.com>
 * @copyright Copyright (c) 2011, Josef Martinec
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace snippy\sysLog\writers;

use snippy\sysLog\xWriterConstructionException;
use snippy\sysLog\xWriteException;
use snippy\sysLog\cLogFactory;

class cLazyConstructWriterProxy extends aLogWriter
{
	/**
	 * @var snippy\sysLog\cLogFactory
	 */
	protected $factory;
	
	/**
	 * @var array
	 */
	protected $writerConf;
	
	/**
	 * Constructor
	 *
	 * @param cLogFactory $factory
	 * @param string      $moduleName
	 * @param array       $writerConf
	 */
	public function __construct( cLogFactory $factory, $moduleName, $writerConf )
	{
		parent::__construct( $moduleName );
		
		$this->factory    = $factory;
		$this->writerConf = $writerConf;
	}

	/**
	 * Creates log writer and logs given message
	 *
	 * @param  int      $level   message level
	 * @param  string   $message message content
	 * @return int|null          unique message ID
	 *
	 * @throws snippy\sysLog\xInvalidLogLevelException
	 * @throws snippy\sysLog\xWriteException
	 */
	public function log( $level, $message )
	{
		// create log writer
		try {
			$writer = $this->factory->createWriter( $this->moduleName, $this->writerConf );
			$this->factory->setWriter( $this->moduleName, $writer );
			
		} catch( xWriterConstructionException $e ) {
			throw new xWriteException( 'Unable to use requested writer', null, $e );
		}
		
		// write given message
		return $writer->log( $level, $message );
	}
}