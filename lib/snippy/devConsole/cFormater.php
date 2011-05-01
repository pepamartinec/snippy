<?php
namespace snippy\devConsole;

class cFormater
{
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
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$this->sourceCodeCache = array();
		$this->elIDsCounter    = 0;
		$this->dumpMaxDepth    = 5;
		$this->dumpMaxLength   = 10;
	}

	/**
	 * Prints formated stack trace
	 *
	 * @param array $trace
	 */
	public function formatTrace( array $trace )
	{
		$trace = array_reverse( $trace );

		echo '<table class="trace">';
		echo '<tr>';
			echo '<th class="level">#</th>';
			echo '<th class="funciton">Function</th>';
			echo '<th class="file">File</th>';
			echo '<th class="buttons"></th>';
		echo '</tr>';

		echo '<tr class="title">';
			echo '<td>0</td>';
			echo '<td>{main}()</td>';
			echo '<td>'.$this->formatFileName( $trace[0]['file'] ).'</td>';
			echo '<td></td>';
		echo '</tr>';

		foreach( $trace as $n => $item ) {
			$argsID = $this->getNextBlockID();
			$codeID = $this->getNextBlockID();

			echo '<tr class="title">';
				echo '<td>'.( $n+1 ).'</td>';
				echo '<td>'.( $item['class'] ? $item['class'].$item['type'] : '' ).$item['function'].'()</td>';
				echo '<td>'.$this->formatFileName( $item['file'] ).'&nbsp;:&nbsp;'.$item['line'].'</td>';
				echo '<td>'.$this->getToggleButton( 'args', $argsID ).'&nbsp;|&nbsp;'.$this->getToggleButton( 'source', $codeID ).'</td>';
			echo '</tr>';

			echo "<tr class=\"arguments collapsed\" id=\"{$argsID}\"><td colspan=\"4\">";
				try {
					$refl = array_key_exists( 'class', $item ) ?
						new \ReflectionMethod( $item['class'], $item['function'] ) :
						new \ReflectionFunction( $item['function'] );

					$params = $refl->getParameters();
					$args = array();

					foreach( $item['args'] as $m => $arg )
						$args[ $params[$m]->name ] = $arg;

				} catch( \ReflectionException $e ) {
					$args = $item['args'];
				}

				echo $this->formatListHorizontal( $args );
			echo '</td></tr>';

			echo "<tr class=\"code collapsed\" id=\"{$codeID}\"><td colspan=\"4\">".$this->formatSourceCode( $item['file'], $item['line'] )."</td></tr>";
		}
		echo '</table>';
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
				if( sizeof( $spans ) > 0 )
				$line = implode( '', $spans ).$line;

				// count spans
				$spans = array();
				preg_match_all('#</?span[^>]*>#', $line, $out );

				if( $out[0] !== null ) {
					// save opened spans
					foreach( $out[0] as $span ) {
						if( substr( $span, 0, 2 ) !== '</' )
						array_push( $spans, $span );
						else
						array_pop( $spans );
					}

					// close opened spans
					if( sizeof( $spans ) > 0 )
					$line .= str_repeat( '</span>', sizeof( $spans ) );
				}
			}

			$this->sourceCodeCache[ $file ] = $lines;
		}

		$source = $this->sourceCodeCache[ $file ];

		$start  = max( 0, $lineNo - 15 );
		$length = 20;
		$end    = min( $start + $length, sizeof( $source ) );

		$code = '<pre class="sourceCode">';
		$lineDigits = strlen( (string)( $start + $length ) );

		for( $n = $start; $n < $end; ++$n )
			$code .= sprintf("<div class=\"line%s\"><span class=\"lineNumber\">%-{$lineDigits}d: </span>%s</div>", $n == $lineNo ? ' selected' : '', $n, $source[ $n ] );

		return $code.'</pre>';
	}

	/**
	 * Prints formated variable
	 *
	 * @param  mixed $var         variable to format
	 * @param  bool  $displayType if true, variable type will be appended
	 */
	public function formatVariable( $var, $displayType = true )
	{
		$stack = array();

		return '<div class="varDump">'.
		           $this->formatVariableHepler( $var, $displayType, $stack ).
		       '</div>';
	}

	private function formatVariableHepler( $var, $displayType, &$stack )
	{
		// cyclic reference setection
		if( in_array( $var, $stack, true ) )
			return '<span class="cyclicRef">** Cyclic reference detected ('.get_class($var).') **</span>';

		// object/array dump depth limitation
		if( sizeof( $stack ) > $this->dumpMaxDepth )
			return "<span class=\"maxDepth\">** Max nesting depth ({$this->dumpMaxDepth}) reached **</span>";


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
			echo 'array('.sizeof( $var ).') [';

			if( sizeof( $var ) === 0 ) {
				echo ']';

			} else {
				ob_start();
				array_push( $stack, $var );
				$itemsCounter = 0;
				foreach( $var as $k => $v ) {
					echo '<br />';

					if( ++$itemsCounter > $this->dumpMaxLength ) {
						$hiddenItemsNo = sizeof( $var ) - $itemsCounter;
						echo "<span class=\"maxLength\">** ... and {$hiddenItemsNo} more items **</span>";
						break;
					}

					echo '<span class="key">'.( is_string($k) ? "'$k'" : $k ).'</span>';
					echo '&nbsp;=>&nbsp';

					// GLOBALS cyclic reference workaround
					if( $k === 'GLOBALS' && is_array( $v ) && array_key_exists( 'GLOBALS', $v ) )
						echo '<span class="cyclicRef">** Cyclic reference detected (GLOBALS) **</span>';
					elseif( in_array( $v, $stack, true ) )
						echo '<span class="cyclicRef">** Cyclic reference detected ('.$v.') **</span>';
					else
						echo $this->formatVariableHepler( $v, $displayType, $stack );
				}
				array_pop( $stack );

				echo $this->indentBlock( ob_get_clean(), 1 );
				echo '<br />]';
			}

		// ===== OBJECT =====
		} elseif( is_object( $var ) ) {

			$refl = new \ReflectionObject( $var );
			echo '<span class="name">'.$refl->getName().'</span>&nbsp;{';

			$properties = $refl->getProperties();
			if( sizeof( $properties ) == 0 ) {
				echo '}';

			} else {
				ob_start();
				array_push( $stack, $var );
				$itemsCounter = 0;
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
					echo $this->formatVariableHepler( $prop->getValue( $var ), $displayType, $stack );
					$prop->setAccessible( !( $prop->isProtected() || $prop->isPrivate() ) );
				}
				array_pop( $stack );

				echo $this->indentBlock( ob_get_clean(), 1 );
				echo '<br />}';
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
		if( sizeof( $varList ) == 0 )
			return '<div class="list empty">empty</div>';

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
		if( sizeof( $items ) == 0 )
			return '<div class="list empty">empty</div>';

		ob_start();

		echo '<table class="list horizontal">';

		// print header (array keys)
		echo '<tr>';
		foreach( $items as $key => $item )
			echo '<th>'.$key.'</th>';
		echo '</tr>';

		// print content
		echo '<tr>';
		foreach( $items as $key => $item )
			echo '<td>'.$this->formatVariable( $item ).'</td>';
		echo '</tr>';

		echo '</table>';

		return ob_get_clean();
	}

	/**
	 * Returns next available unique ID
	 *
	 * @return int
	 */
	protected function getNextBlockID()
	{
		return ++$this->elIDsCounter;
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
	 * Highlights baseName part of file name
	 *
	 * @param string $fileName
	 * @return string
	 */
	protected function formatFileName( $fileName )
	{
		// get file realPath
		$fileName = $this->intersectFilenames( $_SERVER['DOCUMENT_ROOT'], $fileName );

		$parts = explode( '/', $fileName );

		$fileName = array_pop( $parts );

		array_push( $parts, null );
		$filePath = implode( '/', $parts );

		return "<span class=\"filePath\">{$filePath}</span><span class=\"fileName\">{$fileName}</span>";
	}

	/**
	 * Calculates relative path from one file to another
	 *
	 * @param string $baseFilename referenced file name
	 * @param string $relatedFilename referencing file name
	 * @return string
	 */
	private function intersectFilenames( $baseFilename, $relatedFilename )
	{
		$baseParts = explode( '/', $baseFilename );
		$relatedParts = explode( '/', $relatedFilename );

		$minL = min( sizeof( $baseParts ) - 1, sizeof( $relatedParts ) );
		for( $i = 0; $i < $minL; ++$i ) {
			if( $baseParts[$i] != $relatedParts[$i] )
			break;
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
	protected function getToggleButton( $title, $id )
	{
		return "<span class=\"button\" onclick=\"var el=document.getElementById('{$id}');if( el.className.match(/\bcollapsed\b/) == null ) el.className += ' collapsed'; else el.className = el.className.replace(/\bcollapsed\b/, ' ');\">{$title}</span>";
	}
}