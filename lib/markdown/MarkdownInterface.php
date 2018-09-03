<?php
/**
 * Markdown  -  A text-to-HTML conversion tool for web writers
 *
 * @package   php-markdown
 * @author    Michel Fortin <michel.fortin@michelf.com>
 * @copyright 2004-2016 Michel Fortin <https://michelf.com/projects/php-markdown/>
 * @copyright (Original Markdown) 2004-2006 John Gruber <https://daringfireball.net/projects/markdown/>
 */

namespace Michelf;

/**
 * Markdown Parser Interface 
 */
interface MarkdownInterface {
	/**
	 * Initialize the parser and return the result of its transform method.
	 * This will work fine for derived classes too.
	 *
	 * @api
	 *
	 * @param  string $text
	 * @return string
	 */
	public static function defaultTransform($text);

	/**
	 * Main function. Performs some preprocessing on the input text
	 * and pass it through the document gamut.
	 *
	 * @api
	 *
	 * @param  string $text
	 * @return string
	 */
	public function transform($text);
}
