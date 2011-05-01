<?php
namespace snippy\debug;

class cOutputItemTemplate
{
	const C_DEBUG  = 'debug';
	const C_NOTICE = 'notice';
	const C_WARN   = 'warn';
	const C_ERROR  = 'error';

	/**
	 * @var string
	 */
	protected $baseClass;

	/**
	 * @var string|null
	 */
	protected $customClass;

	/**
	 * @var string|null
	 */
	protected $icon;

	/**
	 * @var string|null
	 */
	protected $title;

	/**
	 * @var array|null
	 */
	protected $file;

	/**
	 * @var array
	 */
	protected $collapsers;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * Constructor
	 *
	 * @param string $baseClass
	 */
	public function __construct( $baseClass )
	{
		$this->baseClass   = $baseClass;
		$this->customClass = null;
		$this->icon        = $this->getBaseIcon();
		$this->title       = null;
		$this->file        = null;
		$this->collapsers  = array();
		$this->content     = '';
	}

	/**
	 * Returns icon matching baseClass
	 *
	 * @return string
	 */
	protected function getBaseIcon()
	{
		switch( $this->baseClass ) {
			case self::C_DEBUG:  return null;
			case self::C_NOTICE: return 'images/info.png';
			case self::C_WARN:   return 'images/warning.png';
			case self::C_ERROR:  return 'images/error.png';
		}
	}

	/**
	 * Sets custom class
	 *
	 * @param string $class
	 */
	public function setCustomClass( $class )
	{
		$this->customClass = $class;
		return $this;
	}

	/**
	 * Sets title
	 *
	 * @param string $title
	 */
	public function setTitle( $title )
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Sets file
	 *
	 * @param string   $file
	 * @param int|null $line
	 */
	public function setFile( $file, $line = null )
	{
		$this->file = array( $file, $line );
		return $this;
	}

	/**
	 * Sets collapsers
	 *
	 * @param string $collapsers
	 */
	public function setCollapsers( array $collapsers )
	{
		$this->collapsers = $collapsers;
		return $this;
	}

	/**
	 * Sets content
	 *
	 * @param string $content
	 */
	public function setContent( $content )
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Renders and returns message content
	 *
	 * @param  HTMLFormater $formater
	 * @return string
	 */
	public function render( cHTMLFormater $formater )
	{
		ob_start();

		$class = 'debugItem '.$this->baseClass;
		if( $this->customClass !== null ) {
			$class .= ' '.$this->customClass;
		}

		echo "<div class=\"{$class}\">";
		
		if( $this->title ) {
			echo "<span class=\"title\">{$this->title}</span>";
		}

		$buttons = array();
		$blocks  = array();

		echo '<span class="fileLine">';
		if( $this->file !== null ) {
			$this->collapsers[] = $formater->createToggleBlock( 'source', $formater->formatSourceCode( $this->file[0], $this->file[1] ) );

			echo $formater->formatFileName( $this->file[0], $this->file[1] );
		}
		
		foreach( $this->collapsers as $collapser ) {
			$buttons[] = $collapser['button'];
			$blocks[]  = $collapser['block'];
		}
		
		if( sizeof( $this->collapsers ) > 0 ) {
			echo ' ( '.implode( ' | ', $buttons ).' )';
		}
		echo '</span>';

		echo "<span class=\"content\">{$this->content}</span>";

		echo implode( '', $blocks );

		echo "</div>";

		return ob_get_clean();
	}
}