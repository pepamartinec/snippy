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

namespace snippy\debug;

class cHTMLFormater
{
	const ID_PREFIX = 'debugFormater_';

	/**
	 * @var array
	 */
	protected $sourceCodeCache;

	/**
	 * @var int
	 */
	protected $elIDsCounter;

	/**
	 * @var int
	 */
	protected $dumpMaxDepth;

	/**
	 * @var int
	 */
	protected $dumpMaxLength;
	
	/**
	 * @var iIdeUrlGenerator
	 */
	protected $ideUrlGenerator;
	
	/**
	 * @var string
	 */
	protected $recursionMarker;

	/**
	 * Constructor
	 *
	 */
	public function __construct( iIdeUrlGenerator $urlGenerator )
	{
		$this->sourceCodeCache = array();
		$this->elIDsCounter    = 0;
		$this->dumpMaxDepth    = 5;
		$this->dumpMaxLength   = 10;
		$this->ideUrlGenerator = $urlGenerator;
		$this->recursionMarker = uniqid( "\x00" );
	}

	/**
	 * Returns formated stack trace
	 *
	 * @param  array  $trace
	 * @return string
	 */
	public function formatTrace( array $trace )
	{
		$trace = array_reverse( $trace );
		array_unshift( $trace, array(
			'function' => '{main}',
			'file'     => isset( $trace[0]['file'] ) ? $trace[0]['file'] : null,
			'line'     => isset( $trace[0]['line'] ) ? $trace[0]['line'] : null,
			'args'     => null
		));

		ob_start();

		echo '<table class="trace">';
		echo '<tr>';
			echo '<th class="level">#</th>';
			echo '<th class="funciton">Function</th>';
			echo '<th class="file">File</th>';
			echo '<th class="buttons"></th>';
		echo '</tr>';

		foreach( $trace as $n => $item ) {
			$itemInfo = array();
			
			// arguments
			try {
				$refl = array_key_exists( 'class', $item ) ?
					new \ReflectionMethod( $item['class'], $item['function'] ) :
					new \ReflectionFunction( $item['function'] );

				$params = $refl->getParameters();
				$argsList = array();

				foreach( $item['args'] as $m => $arg ) {
					$argsList[ $params[$m]->name ] = $arg;
				}

			} catch( \ReflectionException $e ) {
				$argsList = $item['args'];
			}
			
			$id = $this->getNextBlockID();
			$itemInfo[] = array(
				'button' => $this->getToggleButton( 'args', $id ),
				'block'  => "<tr class=\"arguments collapsed\" id=\"{$id}\"><td colspan=\"4\">{$this->formatListHorizontal( $argsList )}</td></tr>"
			);
			
			// source code
			$fileName = isset( $item['file'] ) ? $item['file'] : null;
			$fileLine = isset( $item['line'] ) ? $item['line'] : null;
			
			if( $fileName !== null ) {
				$id = $this->getNextBlockID();
				$itemInfo[] = array(
					'button' => $this->getToggleButton( 'source', $id ),
					'block'  => "<tr class=\"code collapsed\" id=\"{$id}\"><td colspan=\"4\">{$this->formatSourceCode( $fileName, $fileLine )}</td></tr>"
				);
			}
			
			// trace item
			echo '<tr class="title">';
				echo '<td>'.( $n+1 ).'</td>';
				echo '<td>'.( isset( $item['class'] ) ? $item['class'].$item['type'] : '' ).$item['function'].'()</td>';
				echo '<td>'.$this->formatFileName( $fileName, $fileLine ).'</td>';
				echo '<td>';
					foreach( $itemInfo as $infoItem ) {
						echo $infoItem['button'].' ';
					}
				echo '</td>';
			echo '</tr>';

			foreach( $itemInfo as $infoItem ) {
				echo $infoItem['block'];
			}

		}
		echo '</table>';

		return ob_get_clean();
	}

	/**
	 * Returns formated source code part around given line
	 *
	 * @param  string $file   file name
	 * @param  int    $lineNo number of line to highlight
	 * @return string
	 */
	public function formatSourceCode( $file, $lineNo )
	{
		// load and format source file
		if( isset( $this->sourceCodeCache[ $file ] ) === false ) {
			$content = highlight_file( $file, true );
			$lines = explode( "\n", $content );
			$lines = explode( "<br />", $lines[1] ); // explode highlighted code
			array_unshift( $lines, '' ); // insert dummy line (start lines from 1, not 0)

			$spans = array();
			foreach( $lines as $n => &$line ) {
				// prepend prev line opened spans
				if( sizeof( $spans ) > 0 ) {
					$line = implode( '', $spans ).$line;
				}

				// count spans
				$spans = array();
				preg_match_all('#</?span[^>]*>#', $line, $out );

				if( $out[0] !== null ) {
					// save opened spans
					foreach( $out[0] as $span ) {
						if( substr( $span, 0, 2 ) !== '</' ) {
							array_push( $spans, $span );
						} else {
							array_pop( $spans );
						}
					}

					// close opened spans
					if( sizeof( $spans ) > 0 ) {
						$line .= str_repeat( '</span>', sizeof( $spans ) );
					}
				}
			}

			$this->sourceCodeCache[ $file ] = $lines;
		}

		$source = $this->sourceCodeCache[ $file ];

		$start  = max( 0, $lineNo - 15 );
		$length = 20;
		$end    = min( $start + $length, sizeof( $source ) );

		$lineDigits = strlen( (string)( $start + $length ) );

		$code = '';
		for( $n = $start; $n < $end; ++$n ) {
			$code .= sprintf("<div class=\"line%s\"><span class=\"lineNumber\">%-{$lineDigits}d: </span>%s</div>", $n == $lineNo ? ' selected' : '', $n, $source[ $n ] );
		}
		
		return "<div class=\"sourceCode\">{$this->formatFileName( $file, $lineNo )}<pre>{$code}</pre></div>";
	}

	/**
	 * Prints formated variable
	 *
	 * @param  mixed $var         variable to format
	 * @param  bool  $displayType if true, variable type will be appended
	 */
	public function formatVariable( $var, $displayType = true )
	{
		return '<div class="varDump">'.
		           $this->formatVariableHepler( $var, $displayType, 0 ).
		       '</div>';
	}

	private function formatVariableHepler( $var, $displayType, $depth )
	{
		// object/array dump depth limitation
		if( $depth > $this->dumpMaxDepth ) {
			return "<span class=\"maxDepth\">** Max nesting depth ({$this->dumpMaxDepth}) reached **</span>";
		}
		

		ob_start();

		$typeName = gettype( $var );
		echo "<span class=\"{$typeName}\">";

		if( $displayType && ( is_null( $var ) || is_object( $var ) || is_array( $var ) ) === false  ) {
			echo '<span class="varType">('.substr( $typeName, 0, 3 ).')</span>';
		}

		// ===== NULL =====
		if( is_null( $var ) ) {
			echo 'null';

		// ===== BOOL =====
		} elseif( is_bool( $var ) ) {
			echo $var ? 'true' : 'false';

		// ===== INT =====
		} elseif( is_int( $var ) ) {
			echo $var;

		// ===== FLOAT =====
		} elseif( is_float( $var ) ) {
			echo $var;

		// ===== STRING =====
		} elseif( is_string( $var ) ) {
			echo $var == '' ?
				'<span class="empty">empty</span>' :
				$this->cutString( htmlspecialchars( $var ) );

		// ===== ARRAY =====
		} elseif( is_array( $var ) ) {
			if( isset( $var[ $this->recursionMarker ] ) ) {
				echo '<span class="cyclicRef">** Circular reference detected (array) **</span>';
				
			} else {
				echo 'array('.sizeof( $var ).') [';
	
				if( sizeof( $var ) === 0 ) {
					echo ']';
	
				} else {
					$var[ $this->recursionMarker ] = true;
					$itemsCounter = 0;
					
					ob_start();
									
					foreach( $var as $k => $v ) {
						if( $k === $this->recursionMarker ) {
							continue;
						}
						
						echo '<br />';
	
						if( ++$itemsCounter > $this->dumpMaxLength ) {
							$hiddenItemsNo = sizeof( $var ) - $itemsCounter;
							echo "<span class=\"maxLength\">** ... and {$hiddenItemsNo} more items **</span>";
							break;
						}
	
						echo '<span class="key">'.( is_string($k) ? "'$k'" : $k ).'</span>';
						echo '&nbsp;=>&nbsp';
	
						// GLOBALS cyclic reference workaround
						if( $k === 'GLOBALS' && is_array( $v ) && array_key_exists( 'GLOBALS', $v ) ) {
							echo '<span class="cyclicRef">** Circular reference detected (GLOBALS) **</span>';
						} else {
							echo $this->formatVariableHepler( $v, $displayType, $depth + 1 );
						}
					}
	
					echo $this->indentBlock( ob_get_clean(), 1 );
					echo '<br />]';
					
					unset( $var[ $this->recursionMarker ] );
				}
			}

		// ===== OBJECT =====
		} elseif( is_object( $var ) ) {
			if( isset( $var->{$this->recursionMarker} ) ) {
				echo '<span class="cyclicRef">** Circular reference detected ('.get_class( $var ).') **</span>';
				
			} else {
				$refl = new \ReflectionObject( $var );
				echo '<span class="name">'.$refl->getName().'</span>&nbsp;{';
	
				$properties = $refl->getProperties();
				if( sizeof( $properties ) == 0 ) {
					echo '}';
	
				} else {
					$var->{$this->recursionMarker} = true;
					$itemsCounter = 0;
					
					ob_start();
					
					foreach( $refl->getProperties() as $prop ) {
						echo '<br />';
	
						if( ++$itemsCounter > $this->dumpMaxLength ) {
							$hiddenItemsNo = sizeof( $properties ) - $itemsCounter;
							echo "<span class=\"maxLength\">** ... and {$hiddenItemsNo} more items **</span>";
							break;
						}
	
						echo '<span class="modifiers">'.
								( $prop->isStatic() ? '*' : '' ).
								( $prop->isPublic() ? 'pub' : ( $prop->isProtected() ? 'pro' : 'pri' ) ).
						     '</span>&nbsp;<span class="key">'.$prop->getName().'</span>&nbsp;=&gt;&nbsp;';
	
						$prop->setAccessible( true );
						echo $this->formatVariableHepler( $prop->getValue( $var ), $displayType, $depth + 1 );
						$prop->setAccessible( !( $prop->isProtected() || $prop->isPrivate() ) );
					}
	
					echo $this->indentBlock( ob_get_clean(), 1 );
					echo '<br />}';
					
					unset( $var->{$this->recursionMarker} );
				}
			}

		// ===== RESOURCE =====
		} elseif( is_resource( $var ) ) {
			echo get_resource_type( $var );
		}

		echo '</span>'.PHP_EOL;
		return ob_get_clean();
	}

	/**
	 * Formats array as vertical table
	 *
	 * @param  array $items
	 * @return string
	 */
	public function formatListVertical( array $varList )
	{
		if( sizeof( $varList ) == 0 ) {
			return '<div class="list empty">empty</div>';
		}

		ob_start();

		echo '<table class="list vertical">';
		foreach( $varList as $k => $v ) {
			echo '<tr>';
				echo '<th>'.$k.'</th>';
				echo '<td>'.$this->formatVariable( $v ).'</td>';
			echo '</tr>';
		}
		echo '</table>';

		return ob_get_clean();
	}

	/**
	 * Formats array as horizontal table
	 *
	 * @param  array $items
	 * @return string
	 */
	public function formatListHorizontal( $items )
	{
		if( sizeof( $items ) == 0 ) {
			return '<div class="list empty">empty</div>';
		}

		ob_start();

		echo '<table class="list horizontal">';

		// print header (array keys)
		echo '<tr>';
		foreach( $items as $key => $item ) {
			echo '<th>'.$key.'</th>';
		}
		echo '</tr>';

		// print content
		echo '<tr>';
		foreach( $items as $key => $item ) {
			echo '<td>'.$this->formatVariable( $item ).'</td>';
		}
		echo '</tr>';

		echo '</table>';

		return ob_get_clean();
	}

	/**
	 * Highlights baseName part of file name
	 *
	 * @param  string   $fileName
	 * @param  int|null $line
	 * @return string
	 */
	public function formatFileName( $fileName, $line = null, $clickable = true )
	{
		if( $fileName == '' ) {
			return '';
		}
		
		// get file realPath
		$fileName = $this->intersectFilenames( $_SERVER['DOCUMENT_ROOT'].'/', $fileName );

		$parts = explode( '/', $fileName );

		$fileName = array_pop( $parts );

		array_push( $parts, null );
		$filePath = implode( '/', $parts );

		$formated = "<span class=\"filePath\">{$filePath}</span><span class=\"fileName\">{$fileName}</span>";

		if( $line !== null ) {
			$formated .= ":<span class=\"fileLine\">{$line}</span>";
		}
		
		if( $clickable === true ) {
			$url = $this->ideUrlGenerator->generateUrl( $filePath . $fileName, $line );
			
			if( $url !== null ) {
				$formated = "<a href=\"javascript:snippy_openFileInEditor('{$url}')\">{$formated}</a>";
			}
		}

		return "<span class=\"fileNameBlock\">{$formated}</span>";
	}

	/**
	 * Wraps given content into collapsable block and creates associated toggle button
	 *
	 * @param  string $btnLabel  toggle button label
	 * @param  string $content   block content
	 * @param  bool   $collapsed if true, block will be printed as collapsed
	 * @return array             array( button => $toggleButton, block => $wrappedContent )
	 */
	public function createToggleBlock( $btnLabel, $content, $collapsed = true )
	{
		$id    = $this->getNextBlockID();
		$class = $collapsed ? 'collapsed' : '';

		return array(
			'button' => $this->getToggleButton( $btnLabel, $id ),
			'block'  => "<div id=\"{$id}\" class=\"{$class}\">{$content}</div>"
		);
	}

	/**
	 * Returns next available unique ID
	 *
	 * @return int
	 */
	protected function getNextBlockID()
	{
		return self::ID_PREFIX . ++$this->elIDsCounter;
	}

	/**
	 * Shortens long strings
	 *
	 * @param string $string
	 * @return string
	 */
	protected function cutString( $string )
	{
		$maxLen = 100;

		return strlen( $string ) > $maxLen ?
			substr( $string, 0, $maxLen - 3 ).'...' :
			$string;
	}

	/**
	 * Calculates relative path from one file to another
	 *
	 * @param string $baseFilename    referenced file name
	 * @param string $relatedFilename referencing file name
	 * @return string
	 */
	private function intersectFilenames( $baseFilename, $relatedFilename )
	{
		$baseParts    = explode( '/', $baseFilename );
		$relatedParts = explode( '/', $relatedFilename );

		$minL = min( sizeof( $baseParts ) - 1, sizeof( $relatedParts ) ) + 1;
		for( $i = 0; $i < $minL; ++$i ) {
			if( $baseParts[$i] != $relatedParts[$i] ) {
				break;
			}
		};

		return str_repeat( '../', sizeof( $baseParts ) - $i - 1 ) .
		       implode( '/', array_slice( $relatedParts, $i ) );
	}

	/**
	 * Indents text blok
	 *
	 * @param string  $text
	 * @param int     $indentSize
	 * @return string
	 */
	protected function indentBlock( $text, $indentSize )
	{
		$indent = str_repeat( '&nbsp;&nbsp;', $indentSize );

		return $indent.str_replace( '<br />', '<br />'.$indent, $text );
	}

	/**
	 * Prints javascript for toggling element display
	 *
	 * @param string $id target element ID
	 */
	public function getToggleButton( $title, $id )
	{
		return "<span class=\"button\" onclick=\"var el=document.getElementById('{$id}');if( el.className.match(/\bcollapsed\b/) == null ) el.className += ' collapsed'; else el.className = el.className.replace(/\bcollapsed\b/, ' ');\">{$title}</span>";
	}
}