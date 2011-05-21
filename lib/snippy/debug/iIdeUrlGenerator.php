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

interface iIdeUrlGenerator
{
	/**
	 * Generates URL for opening file within IDE
	 *
	 * @param  string      $file
	 * @param  int|null    $line
	 * @return string|null
	 */
	public function generateUrl( $file, $line = null );
}