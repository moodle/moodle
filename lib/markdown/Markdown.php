<?php
/**
 * Markdown  -  A text-to-HTML conversion tool for web writers
 *
 * @package   php-markdown
 * @author    Michel Fortin <michel.fortin@michelf.com>
 * @copyright 2004-2018 Michel Fortin <https://michelf.com/projects/php-markdown/>
 * @copyright (Original Markdown) 2004-2006 John Gruber <https://daringfireball.net/projects/markdown/>
 */

namespace Michelf;

/**
 * Markdown Parser Class
 */
class Markdown implements MarkdownInterface {
	/**
	 * Define the package version
	 * @var string
	 */
	const MARKDOWNLIB_VERSION = "1.8.0";

	/**
	 * Simple function interface - Initialize the parser and return the result
	 * of its transform method. This will work fine for derived classes too.
	 *
	 * @api
	 *
	 * @param  string $text
	 * @return string
	 */
	public static function defaultTransform($text) {
		// Take parser class on which this function was called.
		$parser_class = \get_called_class();

		// Try to take parser from the static parser list
		static $parser_list;
		$parser =& $parser_list[$parser_class];

		// Create the parser it not already set
		if (!$parser) {
			$parser = new $parser_class;
		}

		// Transform text using parser.
		return $parser->transform($text);
	}

	/**
	 * Configuration variables
	 */

	/**
	 * Change to ">" for HTML output.
	 * @var string
	 */
	public $empty_element_suffix = " />";

	/**
	 * The width of indentation of the output markup
	 * @var int
	 */
	public $tab_width = 4;

	/**
	 * Change to `true` to disallow markup or entities.
	 * @var boolean
	 */
	public $no_markup   = false;
	public $no_entities = false;


	/**
	 * Change to `true` to enable line breaks on \n without two trailling spaces
	 * @var boolean
	 */
	public $hard_wrap = false;

	/**
	 * Predefined URLs and titles for reference links and images.
	 * @var array
	 */
	public $predef_urls   = array();
	public $predef_titles = array();

	/**
	 * Optional filter function for URLs
	 * @var callable
	 */
	public $url_filter_func = null;

	/**
	 * Optional header id="" generation callback function.
	 * @var callable
	 */
	public $header_id_func = null;

	/**
	 * Optional function for converting code block content to HTML
	 * @var callable
	 */
	public $code_block_content_func = null;

	/**
	 * Optional function for converting code span content to HTML.
	 * @var callable
	 */
	public $code_span_content_func = null;

	/**
	 * Class attribute to toggle "enhanced ordered list" behaviour
	 * setting this to true will allow ordered lists to start from the index
	 * number that is defined first.
	 *
	 * For example:
	 * 2. List item two
	 * 3. List item three
	 *
	 * Becomes:
	 * <ol start="2">
	 * <li>List item two</li>
	 * <li>List item three</li>
	 * </ol>
	 *
	 * @var bool
	 */
	public $enhanced_ordered_list = false;

	/**
	 * Parser implementation
	 */

	/**
	 * Regex to match balanced [brackets].
	 * Needed to insert a maximum bracked depth while converting to PHP.
	 * @var int
	 */
	protected $nested_brackets_depth = 6;
	protected $nested_brackets_re;

	protected $nested_url_parenthesis_depth = 4;
	protected $nested_url_parenthesis_re;

	/**
	 * Table of hash values for escaped characters:
	 * @var string
	 */
	protected $escape_chars = '\`*_{}[]()>#+-.!';
	protected $escape_chars_re;

	/**
	 * Constructor function. Initialize appropriate member variables.
	 * @return void
	 */
	public function __construct() {
		$this->_initDetab();
		$this->prepareItalicsAndBold();

		$this->nested_brackets_re =
			str_repeat('(?>[^\[\]]+|\[', $this->nested_brackets_depth).
			str_repeat('\])*', $this->nested_brackets_depth);

		$this->nested_url_parenthesis_re =
			str_repeat('(?>[^()\s]+|\(', $this->nested_url_parenthesis_depth).
			str_repeat('(?>\)))*', $this->nested_url_parenthesis_depth);

		$this->escape_chars_re = '['.preg_quote($this->escape_chars).']';

		// Sort document, block, and span gamut in ascendent priority order.
		asort($this->document_gamut);
		asort($this->block_gamut);
		asort($this->span_gamut);
	}


	/**
	 * Internal hashes used during transformation.
	 * @var array
	 */
	protected $urls        = array();
	protected $titles      = array();
	protected $html_hashes = array();

	/**
	 * Status flag to avoid invalid nesting.
	 * @var boolean
	 */
	protected $in_anchor = false;

	/**
	 * Status flag to avoid invalid nesting.
	 * @var boolean
	 */
	protected $in_emphasis_processing = false;

	/**
	 * Called before the transformation process starts to setup parser states.
	 * @return void
	 */
	protected function setup() {
		// Clear global hashes.
		$this->urls        = $this->predef_urls;
		$this->titles      = $this->predef_titles;
		$this->html_hashes = array();
		$this->in_anchor   = false;
		$this->in_emphasis_processing = false;
	}

	/**
	 * Called after the transformation process to clear any variable which may
	 * be taking up memory unnecessarly.
	 * @return void
	 */
	protected function teardown() {
		$this->urls        = array();
		$this->titles      = array();
		$this->html_hashes = array();
	}

	/**
	 * Main function. Performs some preprocessing on the input text and pass
	 * it through the document gamut.
	 *
	 * @api
	 *
	 * @param  string $text
	 * @return string
	 */
	public function transform($text) {
		$this->setup();

		# Remove UTF-8 BOM and marker character in input, if present.
		$text = preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $text);

		# Standardize line endings:
		#   DOS to Unix and Mac to Unix
		$text = preg_replace('{\r\n?}', "\n", $text);

		# Make sure $text ends with a couple of newlines:
		$text .= "\n\n";

		# Convert all tabs to spaces.
		$text = $this->detab($text);

		# Turn block-level HTML blocks into hash entries
		$text = $this->hashHTMLBlocks($text);

		# Strip any lines consisting only of spaces and tabs.
		# This makes subsequent regexen easier to write, because we can
		# match consecutive blank lines with /\n+/ instead of something
		# contorted like /[ ]*\n+/ .
		$text = preg_replace('/^[ ]+$/m', '', $text);

		# Run document gamut methods.
		foreach ($this->document_gamut as $method => $priority) {
			$text = $this->$method($text);
		}

		$this->teardown();

		return $text . "\n";
	}

	/**
	 * Define the document gamut
	 * @var array
	 */
	protected $document_gamut = array(
		// Strip link definitions, store in hashes.
		"stripLinkDefinitions" => 20,
		"runBasicBlockGamut"   => 30,
	);

	/**
	 * Strips link definitions from text, stores the URLs and titles in
	 * hash references
	 * @param  string $text
	 * @return string
	 */
	protected function stripLinkDefinitions($text) {

		$less_than_tab = $this->tab_width - 1;

		// Link defs are in the form: ^[id]: url "optional title"
		$text = preg_replace_callback('{
							^[ ]{0,'.$less_than_tab.'}\[(.+)\][ ]?:	# id = $1
							  [ ]*
							  \n?				# maybe *one* newline
							  [ ]*
							(?:
							  <(.+?)>			# url = $2
							|
							  (\S+?)			# url = $3
							)
							  [ ]*
							  \n?				# maybe one newline
							  [ ]*
							(?:
								(?<=\s)			# lookbehind for whitespace
								["(]
								(.*?)			# title = $4
								[")]
								[ ]*
							)?	# title is optional
							(?:\n+|\Z)
			}xm',
			array($this, '_stripLinkDefinitions_callback'),
			$text
		);
		return $text;
	}

	/**
	 * The callback to strip link definitions
	 * @param  array $matches
	 * @return string
	 */
	protected function _stripLinkDefinitions_callback($matches) {
		$link_id = strtolower($matches[1]);
		$url = $matches[2] == '' ? $matches[3] : $matches[2];
		$this->urls[$link_id] = $url;
		$this->titles[$link_id] =& $matches[4];
		return ''; // String that will replace the block
	}

	/**
	 * Hashify HTML blocks
	 * @param  string $text
	 * @return string
	 */
	protected function hashHTMLBlocks($text) {
		if ($this->no_markup) {
			return $text;
		}

		$less_than_tab = $this->tab_width - 1;

		/**
		 * Hashify HTML blocks:
		 *
		 * We only want to do this for block-level HTML tags, such as headers,
		 * lists, and tables. That's because we still want to wrap <p>s around
		 * "paragraphs" that are wrapped in non-block-level tags, such as
		 * anchors, phrase emphasis, and spans. The list of tags we're looking
		 * for is hard-coded:
		 *
		 * *  List "a" is made of tags which can be both inline or block-level.
		 *    These will be treated block-level when the start tag is alone on
		 *    its line, otherwise they're not matched here and will be taken as
		 *    inline later.
		 * *  List "b" is made of tags which are always block-level;
		 */
		$block_tags_a_re = 'ins|del';
		$block_tags_b_re = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|'.
						   'script|noscript|style|form|fieldset|iframe|math|svg|'.
						   'article|section|nav|aside|hgroup|header|footer|'.
						   'figure';

		// Regular expression for the content of a block tag.
		$nested_tags_level = 4;
		$attr = '
			(?>				# optional tag attributes
			  \s			# starts with whitespace
			  (?>
				[^>"/]+		# text outside quotes
			  |
				/+(?!>)		# slash not followed by ">"
			  |
				"[^"]*"		# text inside double quotes (tolerate ">")
			  |
				\'[^\']*\'	# text inside single quotes (tolerate ">")
			  )*
			)?
			';
		$content =
			str_repeat('
				(?>
				  [^<]+			# content without tag
				|
				  <\2			# nested opening tag
					'.$attr.'	# attributes
					(?>
					  />
					|
					  >', $nested_tags_level).	// end of opening tag
					  '.*?'.					// last level nested tag content
			str_repeat('
					  </\2\s*>	# closing nested tag
					)
				  |
					<(?!/\2\s*>	# other tags with a different name
				  )
				)*',
				$nested_tags_level);
		$content2 = str_replace('\2', '\3', $content);

		/**
		 * First, look for nested blocks, e.g.:
		 * 	<div>
		 * 		<div>
		 * 		tags for inner block must be indented.
		 * 		</div>
		 * 	</div>
		 *
		 * The outermost tags must start at the left margin for this to match,
		 * and the inner nested divs must be indented.
		 * We need to do this before the next, more liberal match, because the
		 * next match will start at the first `<div>` and stop at the
		 * first `</div>`.
		 */
		$text = preg_replace_callback('{(?>
			(?>
				(?<=\n)			# Starting on its own line
				|				# or
				\A\n?			# the at beginning of the doc
			)
			(						# save in $1

			  # Match from `\n<tag>` to `</tag>\n`, handling nested tags
			  # in between.

						[ ]{0,'.$less_than_tab.'}
						<('.$block_tags_b_re.')# start tag = $2
						'.$attr.'>			# attributes followed by > and \n
						'.$content.'		# content, support nesting
						</\2>				# the matching end tag
						[ ]*				# trailing spaces/tabs
						(?=\n+|\Z)	# followed by a newline or end of document

			| # Special version for tags of group a.

						[ ]{0,'.$less_than_tab.'}
						<('.$block_tags_a_re.')# start tag = $3
						'.$attr.'>[ ]*\n	# attributes followed by >
						'.$content2.'		# content, support nesting
						</\3>				# the matching end tag
						[ ]*				# trailing spaces/tabs
						(?=\n+|\Z)	# followed by a newline or end of document

			| # Special case just for <hr />. It was easier to make a special
			  # case than to make the other regex more complicated.

						[ ]{0,'.$less_than_tab.'}
						<(hr)				# start tag = $2
						'.$attr.'			# attributes
						/?>					# the matching end tag
						[ ]*
						(?=\n{2,}|\Z)		# followed by a blank line or end of document

			| # Special case for standalone HTML comments:

					[ ]{0,'.$less_than_tab.'}
					(?s:
						<!-- .*? -->
					)
					[ ]*
					(?=\n{2,}|\Z)		# followed by a blank line or end of document

			| # PHP and ASP-style processor instructions (<? and <%)

					[ ]{0,'.$less_than_tab.'}
					(?s:
						<([?%])			# $2
						.*?
						\2>
					)
					[ ]*
					(?=\n{2,}|\Z)		# followed by a blank line or end of document

			)
			)}Sxmi',
			array($this, '_hashHTMLBlocks_callback'),
			$text
		);

		return $text;
	}

	/**
	 * The callback for hashing HTML blocks
	 * @param  string $matches
	 * @return string
	 */
	protected function _hashHTMLBlocks_callback($matches) {
		$text = $matches[1];
		$key  = $this->hashBlock($text);
		return "\n\n$key\n\n";
	}

	/**
	 * Called whenever a tag must be hashed when a function insert an atomic
	 * element in the text stream. Passing $text to through this function gives
	 * a unique text-token which will be reverted back when calling unhash.
	 *
	 * The $boundary argument specify what character should be used to surround
	 * the token. By convension, "B" is used for block elements that needs not
	 * to be wrapped into paragraph tags at the end, ":" is used for elements
	 * that are word separators and "X" is used in the general case.
	 *
	 * @param  string $text
	 * @param  string $boundary
	 * @return string
	 */
	protected function hashPart($text, $boundary = 'X') {
		// Swap back any tag hash found in $text so we do not have to `unhash`
		// multiple times at the end.
		$text = $this->unhash($text);

		// Then hash the block.
		static $i = 0;
		$key = "$boundary\x1A" . ++$i . $boundary;
		$this->html_hashes[$key] = $text;
		return $key; // String that will replace the tag.
	}

	/**
	 * Shortcut function for hashPart with block-level boundaries.
	 * @param  string $text
	 * @return string
	 */
	protected function hashBlock($text) {
		return $this->hashPart($text, 'B');
	}

	/**
	 * Define the block gamut - these are all the transformations that form
	 * block-level tags like paragraphs, headers, and list items.
	 * @var array
	 */
	protected $block_gamut = array(
		"doHeaders"         => 10,
		"doHorizontalRules" => 20,
		"doLists"           => 40,
		"doCodeBlocks"      => 50,
		"doBlockQuotes"     => 60,
	);

	/**
	 * Run block gamut tranformations.
	 *
	 * We need to escape raw HTML in Markdown source before doing anything
	 * else. This need to be done for each block, and not only at the
	 * begining in the Markdown function since hashed blocks can be part of
	 * list items and could have been indented. Indented blocks would have
	 * been seen as a code block in a previous pass of hashHTMLBlocks.
	 *
	 * @param  string $text
	 * @return string
	 */
	protected function runBlockGamut($text) {
		$text = $this->hashHTMLBlocks($text);
		return $this->runBasicBlockGamut($text);
	}

	/**
	 * Run block gamut tranformations, without hashing HTML blocks. This is
	 * useful when HTML blocks are known to be already hashed, like in the first
	 * whole-document pass.
	 *
	 * @param  string $text
	 * @return string
	 */
	protected function runBasicBlockGamut($text) {

		foreach ($this->block_gamut as $method => $priority) {
			$text = $this->$method($text);
		}

		// Finally form paragraph and restore hashed blocks.
		$text = $this->formParagraphs($text);

		return $text;
	}

	/**
	 * Convert horizontal rules
	 * @param  string $text
	 * @return string
	 */
	protected function doHorizontalRules($text) {
		return preg_replace(
			'{
				^[ ]{0,3}	# Leading space
				([-*_])		# $1: First marker
				(?>			# Repeated marker group
					[ ]{0,2}	# Zero, one, or two spaces.
					\1			# Marker character
				){2,}		# Group repeated at least twice
				[ ]*		# Tailing spaces
				$			# End of line.
			}mx',
			"\n".$this->hashBlock("<hr$this->empty_element_suffix")."\n",
			$text
		);
	}

	/**
	 * These are all the transformations that occur *within* block-level
	 * tags like paragraphs, headers, and list items.
	 * @var array
	 */
	protected $span_gamut = array(
		// Process character escapes, code spans, and inline HTML
		// in one shot.
		"parseSpan"           => -30,
		// Process anchor and image tags. Images must come first,
		// because ![foo][f] looks like an anchor.
		"doImages"            =>  10,
		"doAnchors"           =>  20,
		// Make links out of things like `<https://example.com/>`
		// Must come after doAnchors, because you can use < and >
		// delimiters in inline links like [this](<url>).
		"doAutoLinks"         =>  30,
		"encodeAmpsAndAngles" =>  40,
		"doItalicsAndBold"    =>  50,
		"doHardBreaks"        =>  60,
	);

	/**
	 * Run span gamut transformations
	 * @param  string $text
	 * @return string
	 */
	protected function runSpanGamut($text) {
		foreach ($this->span_gamut as $method => $priority) {
			$text = $this->$method($text);
		}

		return $text;
	}

	/**
	 * Do hard breaks
	 * @param  string $text
	 * @return string
	 */
	protected function doHardBreaks($text) {
		if ($this->hard_wrap) {
			return preg_replace_callback('/ *\n/',
				array($this, '_doHardBreaks_callback'), $text);
		} else {
			return preg_replace_callback('/ {2,}\n/',
				array($this, '_doHardBreaks_callback'), $text);
		}
	}

	/**
	 * Trigger part hashing for the hard break (callback method)
	 * @param  array $matches
	 * @return string
	 */
	protected function _doHardBreaks_callback($matches) {
		return $this->hashPart("<br$this->empty_element_suffix\n");
	}

	/**
	 * Turn Markdown link shortcuts into XHTML <a> tags.
	 * @param  string $text
	 * @return string
	 */
	protected function doAnchors($text) {
		if ($this->in_anchor) {
			return $text;
		}
		$this->in_anchor = true;

		// First, handle reference-style links: [link text] [id]
		$text = preg_replace_callback('{
			(					# wrap whole match in $1
			  \[
				('.$this->nested_brackets_re.')	# link text = $2
			  \]

			  [ ]?				# one optional space
			  (?:\n[ ]*)?		# one optional newline followed by spaces

			  \[
				(.*?)		# id = $3
			  \]
			)
			}xs',
			array($this, '_doAnchors_reference_callback'), $text);

		// Next, inline-style links: [link text](url "optional title")
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  \[
				('.$this->nested_brackets_re.')	# link text = $2
			  \]
			  \(			# literal paren
				[ \n]*
				(?:
					<(.+?)>	# href = $3
				|
					('.$this->nested_url_parenthesis_re.')	# href = $4
				)
				[ \n]*
				(			# $5
				  ([\'"])	# quote char = $6
				  (.*?)		# Title = $7
				  \6		# matching quote
				  [ \n]*	# ignore any spaces/tabs between closing quote and )
				)?			# title is optional
			  \)
			)
			}xs',
			array($this, '_doAnchors_inline_callback'), $text);

		// Last, handle reference-style shortcuts: [link text]
		// These must come last in case you've also got [link text][1]
		// or [link text](/foo)
		$text = preg_replace_callback('{
			(					# wrap whole match in $1
			  \[
				([^\[\]]+)		# link text = $2; can\'t contain [ or ]
			  \]
			)
			}xs',
			array($this, '_doAnchors_reference_callback'), $text);

		$this->in_anchor = false;
		return $text;
	}

	/**
	 * Callback method to parse referenced anchors
	 * @param  string $matches
	 * @return string
	 */
	protected function _doAnchors_reference_callback($matches) {
		$whole_match =  $matches[1];
		$link_text   =  $matches[2];
		$link_id     =& $matches[3];

		if ($link_id == "") {
			// for shortcut links like [this][] or [this].
			$link_id = $link_text;
		}

		// lower-case and turn embedded newlines into spaces
		$link_id = strtolower($link_id);
		$link_id = preg_replace('{[ ]?\n}', ' ', $link_id);

		if (isset($this->urls[$link_id])) {
			$url = $this->urls[$link_id];
			$url = $this->encodeURLAttribute($url);

			$result = "<a href=\"$url\"";
			if ( isset( $this->titles[$link_id] ) ) {
				$title = $this->titles[$link_id];
				$title = $this->encodeAttribute($title);
				$result .=  " title=\"$title\"";
			}

			$link_text = $this->runSpanGamut($link_text);
			$result .= ">$link_text</a>";
			$result = $this->hashPart($result);
		} else {
			$result = $whole_match;
		}
		return $result;
	}

	/**
	 * Callback method to parse inline anchors
	 * @param  string $matches
	 * @return string
	 */
	protected function _doAnchors_inline_callback($matches) {
		$whole_match	=  $matches[1];
		$link_text		=  $this->runSpanGamut($matches[2]);
		$url			=  $matches[3] == '' ? $matches[4] : $matches[3];
		$title			=& $matches[7];

		// If the URL was of the form <s p a c e s> it got caught by the HTML
		// tag parser and hashed. Need to reverse the process before using
		// the URL.
		$unhashed = $this->unhash($url);
		if ($unhashed != $url)
			$url = preg_replace('/^<(.*)>$/', '\1', $unhashed);

		$url = $this->encodeURLAttribute($url);

		$result = "<a href=\"$url\"";
		if (isset($title)) {
			$title = $this->encodeAttribute($title);
			$result .=  " title=\"$title\"";
		}

		$link_text = $this->runSpanGamut($link_text);
		$result .= ">$link_text</a>";

		return $this->hashPart($result);
	}

	/**
	 * Turn Markdown image shortcuts into <img> tags.
	 * @param  string $text
	 * @return string
	 */
	protected function doImages($text) {
		// First, handle reference-style labeled images: ![alt text][id]
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  !\[
				('.$this->nested_brackets_re.')		# alt text = $2
			  \]

			  [ ]?				# one optional space
			  (?:\n[ ]*)?		# one optional newline followed by spaces

			  \[
				(.*?)		# id = $3
			  \]

			)
			}xs',
			array($this, '_doImages_reference_callback'), $text);

		// Next, handle inline images:  ![alt text](url "optional title")
		// Don't forget: encode * and _
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  !\[
				('.$this->nested_brackets_re.')		# alt text = $2
			  \]
			  \s?			# One optional whitespace character
			  \(			# literal paren
				[ \n]*
				(?:
					<(\S*)>	# src url = $3
				|
					('.$this->nested_url_parenthesis_re.')	# src url = $4
				)
				[ \n]*
				(			# $5
				  ([\'"])	# quote char = $6
				  (.*?)		# title = $7
				  \6		# matching quote
				  [ \n]*
				)?			# title is optional
			  \)
			)
			}xs',
			array($this, '_doImages_inline_callback'), $text);

		return $text;
	}

	/**
	 * Callback to parse references image tags
	 * @param  array $matches
	 * @return string
	 */
	protected function _doImages_reference_callback($matches) {
		$whole_match = $matches[1];
		$alt_text    = $matches[2];
		$link_id     = strtolower($matches[3]);

		if ($link_id == "") {
			$link_id = strtolower($alt_text); // for shortcut links like ![this][].
		}

		$alt_text = $this->encodeAttribute($alt_text);
		if (isset($this->urls[$link_id])) {
			$url = $this->encodeURLAttribute($this->urls[$link_id]);
			$result = "<img src=\"$url\" alt=\"$alt_text\"";
			if (isset($this->titles[$link_id])) {
				$title = $this->titles[$link_id];
				$title = $this->encodeAttribute($title);
				$result .=  " title=\"$title\"";
			}
			$result .= $this->empty_element_suffix;
			$result = $this->hashPart($result);
		} else {
			// If there's no such link ID, leave intact:
			$result = $whole_match;
		}

		return $result;
	}

	/**
	 * Callback to parse inline image tags
	 * @param  array $matches
	 * @return string
	 */
	protected function _doImages_inline_callback($matches) {
		$whole_match	= $matches[1];
		$alt_text		= $matches[2];
		$url			= $matches[3] == '' ? $matches[4] : $matches[3];
		$title			=& $matches[7];

		$alt_text = $this->encodeAttribute($alt_text);
		$url = $this->encodeURLAttribute($url);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($title)) {
			$title = $this->encodeAttribute($title);
			$result .=  " title=\"$title\""; // $title already quoted
		}
		$result .= $this->empty_element_suffix;

		return $this->hashPart($result);
	}

	/**
	 * Parse Markdown heading elements to HTML
	 * @param  string $text
	 * @return string
	 */
	protected function doHeaders($text) {
		/**
		 * Setext-style headers:
		 *	  Header 1
		 *	  ========
		 *
		 *	  Header 2
		 *	  --------
		 */
		$text = preg_replace_callback('{ ^(.+?)[ ]*\n(=+|-+)[ ]*\n+ }mx',
			array($this, '_doHeaders_callback_setext'), $text);

		/**
		 * atx-style headers:
		 *   # Header 1
		 *   ## Header 2
		 *   ## Header 2 with closing hashes ##
		 *   ...
		 *   ###### Header 6
		 */
		$text = preg_replace_callback('{
				^(\#{1,6})	# $1 = string of #\'s
				[ ]*
				(.+?)		# $2 = Header text
				[ ]*
				\#*			# optional closing #\'s (not counted)
				\n+
			}xm',
			array($this, '_doHeaders_callback_atx'), $text);

		return $text;
	}

	/**
	 * Setext header parsing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doHeaders_callback_setext($matches) {
		// Terrible hack to check we haven't found an empty list item.
		if ($matches[2] == '-' && preg_match('{^-(?: |$)}', $matches[1])) {
			return $matches[0];
		}

		$level = $matches[2]{0} == '=' ? 1 : 2;

		// ID attribute generation
		$idAtt = $this->_generateIdFromHeaderValue($matches[1]);

		$block = "<h$level$idAtt>".$this->runSpanGamut($matches[1])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}

	/**
	 * ATX header parsing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doHeaders_callback_atx($matches) {
		// ID attribute generation
		$idAtt = $this->_generateIdFromHeaderValue($matches[2]);

		$level = strlen($matches[1]);
		$block = "<h$level$idAtt>".$this->runSpanGamut($matches[2])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}

	/**
	 * If a header_id_func property is set, we can use it to automatically
	 * generate an id attribute.
	 *
	 * This method returns a string in the form id="foo", or an empty string
	 * otherwise.
	 * @param  string $headerValue
	 * @return string
	 */
	protected function _generateIdFromHeaderValue($headerValue) {
		if (!is_callable($this->header_id_func)) {
			return "";
		}

		$idValue = call_user_func($this->header_id_func, $headerValue);
		if (!$idValue) {
			return "";
		}

		return ' id="' . $this->encodeAttribute($idValue) . '"';
	}

	/**
	 * Form HTML ordered (numbered) and unordered (bulleted) lists.
	 * @param  string $text
	 * @return string
	 */
	protected function doLists($text) {
		$less_than_tab = $this->tab_width - 1;

		// Re-usable patterns to match list item bullets and number markers:
		$marker_ul_re  = '[*+-]';
		$marker_ol_re  = '\d+[\.]';

		$markers_relist = array(
			$marker_ul_re => $marker_ol_re,
			$marker_ol_re => $marker_ul_re,
			);

		foreach ($markers_relist as $marker_re => $other_marker_re) {
			// Re-usable pattern to match any entirel ul or ol list:
			$whole_list_re = '
				(								# $1 = whole list
				  (								# $2
					([ ]{0,'.$less_than_tab.'})	# $3 = number of spaces
					('.$marker_re.')			# $4 = first list item marker
					[ ]+
				  )
				  (?s:.+?)
				  (								# $5
					  \z
					|
					  \n{2,}
					  (?=\S)
					  (?!						# Negative lookahead for another list item marker
						[ ]*
						'.$marker_re.'[ ]+
					  )
					|
					  (?=						# Lookahead for another kind of list
					    \n
						\3						# Must have the same indentation
						'.$other_marker_re.'[ ]+
					  )
				  )
				)
			'; // mx

			// We use a different prefix before nested lists than top-level lists.
			//See extended comment in _ProcessListItems().

			if ($this->list_level) {
				$text = preg_replace_callback('{
						^
						'.$whole_list_re.'
					}mx',
					array($this, '_doLists_callback'), $text);
			} else {
				$text = preg_replace_callback('{
						(?:(?<=\n)\n|\A\n?) # Must eat the newline
						'.$whole_list_re.'
					}mx',
					array($this, '_doLists_callback'), $text);
			}
		}

		return $text;
	}

	/**
	 * List parsing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doLists_callback($matches) {
		// Re-usable patterns to match list item bullets and number markers:
		$marker_ul_re  = '[*+-]';
		$marker_ol_re  = '\d+[\.]';
		$marker_any_re = "(?:$marker_ul_re|$marker_ol_re)";
		$marker_ol_start_re = '[0-9]+';

		$list = $matches[1];
		$list_type = preg_match("/$marker_ul_re/", $matches[4]) ? "ul" : "ol";

		$marker_any_re = ( $list_type == "ul" ? $marker_ul_re : $marker_ol_re );

		$list .= "\n";
		$result = $this->processListItems($list, $marker_any_re);

		$ol_start = 1;
		if ($this->enhanced_ordered_list) {
			// Get the start number for ordered list.
			if ($list_type == 'ol') {
				$ol_start_array = array();
				$ol_start_check = preg_match("/$marker_ol_start_re/", $matches[4], $ol_start_array);
				if ($ol_start_check){
					$ol_start = $ol_start_array[0];
				}
			}
		}

		if ($ol_start > 1 && $list_type == 'ol'){
			$result = $this->hashBlock("<$list_type start=\"$ol_start\">\n" . $result . "</$list_type>");
		} else {
			$result = $this->hashBlock("<$list_type>\n" . $result . "</$list_type>");
		}
		return "\n". $result ."\n\n";
	}

	/**
	 * Nesting tracker for list levels
	 * @var integer
	 */
	protected $list_level = 0;

	/**
	 * Process the contents of a single ordered or unordered list, splitting it
	 * into individual list items.
	 * @param  string $list_str
	 * @param  string $marker_any_re
	 * @return string
	 */
	protected function processListItems($list_str, $marker_any_re) {
		/**
		 * The $this->list_level global keeps track of when we're inside a list.
		 * Each time we enter a list, we increment it; when we leave a list,
		 * we decrement. If it's zero, we're not in a list anymore.
		 *
		 * We do this because when we're not inside a list, we want to treat
		 * something like this:
		 *
		 *		I recommend upgrading to version
		 *		8. Oops, now this line is treated
		 *		as a sub-list.
		 *
		 * As a single paragraph, despite the fact that the second line starts
		 * with a digit-period-space sequence.
		 *
		 * Whereas when we're inside a list (or sub-list), that line will be
		 * treated as the start of a sub-list. What a kludge, huh? This is
		 * an aspect of Markdown's syntax that's hard to parse perfectly
		 * without resorting to mind-reading. Perhaps the solution is to
		 * change the syntax rules such that sub-lists must start with a
		 * starting cardinal number; e.g. "1." or "a.".
		 */
		$this->list_level++;

		// Trim trailing blank lines:
		$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

		$list_str = preg_replace_callback('{
			(\n)?							# leading line = $1
			(^[ ]*)							# leading whitespace = $2
			('.$marker_any_re.'				# list marker and space = $3
				(?:[ ]+|(?=\n))	# space only required if item is not empty
			)
			((?s:.*?))						# list item text   = $4
			(?:(\n+(?=\n))|\n)				# tailing blank line = $5
			(?= \n* (\z | \2 ('.$marker_any_re.') (?:[ ]+|(?=\n))))
			}xm',
			array($this, '_processListItems_callback'), $list_str);

		$this->list_level--;
		return $list_str;
	}

	/**
	 * List item parsing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _processListItems_callback($matches) {
		$item = $matches[4];
		$leading_line =& $matches[1];
		$leading_space =& $matches[2];
		$marker_space = $matches[3];
		$tailing_blank_line =& $matches[5];

		if ($leading_line || $tailing_blank_line ||
			preg_match('/\n{2,}/', $item))
		{
			// Replace marker with the appropriate whitespace indentation
			$item = $leading_space . str_repeat(' ', strlen($marker_space)) . $item;
			$item = $this->runBlockGamut($this->outdent($item)."\n");
		} else {
			// Recursion for sub-lists:
			$item = $this->doLists($this->outdent($item));
			$item = $this->formParagraphs($item, false);
		}

		return "<li>" . $item . "</li>\n";
	}

	/**
	 * Process Markdown `<pre><code>` blocks.
	 * @param  string $text
	 * @return string
	 */
	protected function doCodeBlocks($text) {
		$text = preg_replace_callback('{
				(?:\n\n|\A\n?)
				(	            # $1 = the code block -- one or more lines, starting with a space/tab
				  (?>
					[ ]{'.$this->tab_width.'}  # Lines must start with a tab or a tab-width of spaces
					.*\n+
				  )+
				)
				((?=^[ ]{0,'.$this->tab_width.'}\S)|\Z)	# Lookahead for non-space at line-start, or end of doc
			}xm',
			array($this, '_doCodeBlocks_callback'), $text);

		return $text;
	}

	/**
	 * Code block parsing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doCodeBlocks_callback($matches) {
		$codeblock = $matches[1];

		$codeblock = $this->outdent($codeblock);
		if ($this->code_block_content_func) {
			$codeblock = call_user_func($this->code_block_content_func, $codeblock, "");
		} else {
			$codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);
		}

		# trim leading newlines and trailing newlines
		$codeblock = preg_replace('/\A\n+|\n+\z/', '', $codeblock);

		$codeblock = "<pre><code>$codeblock\n</code></pre>";
		return "\n\n" . $this->hashBlock($codeblock) . "\n\n";
	}

	/**
	 * Create a code span markup for $code. Called from handleSpanToken.
	 * @param  string $code
	 * @return string
	 */
	protected function makeCodeSpan($code) {
		if ($this->code_span_content_func) {
			$code = call_user_func($this->code_span_content_func, $code);
		} else {
			$code = htmlspecialchars(trim($code), ENT_NOQUOTES);
		}
		return $this->hashPart("<code>$code</code>");
	}

	/**
	 * Define the emphasis operators with their regex matches
	 * @var array
	 */
	protected $em_relist = array(
		''  => '(?:(?<!\*)\*(?!\*)|(?<!_)_(?!_))(?![\.,:;]?\s)',
		'*' => '(?<![\s*])\*(?!\*)',
		'_' => '(?<![\s_])_(?!_)',
	);

	/**
	 * Define the strong operators with their regex matches
	 * @var array
	 */
	protected $strong_relist = array(
		''   => '(?:(?<!\*)\*\*(?!\*)|(?<!_)__(?!_))(?![\.,:;]?\s)',
		'**' => '(?<![\s*])\*\*(?!\*)',
		'__' => '(?<![\s_])__(?!_)',
	);

	/**
	 * Define the emphasis + strong operators with their regex matches
	 * @var array
	 */
	protected $em_strong_relist = array(
		''    => '(?:(?<!\*)\*\*\*(?!\*)|(?<!_)___(?!_))(?![\.,:;]?\s)',
		'***' => '(?<![\s*])\*\*\*(?!\*)',
		'___' => '(?<![\s_])___(?!_)',
	);

	/**
	 * Container for prepared regular expressions
	 * @var array
	 */
	protected $em_strong_prepared_relist;

	/**
	 * Prepare regular expressions for searching emphasis tokens in any
	 * context.
	 * @return void
	 */
	protected function prepareItalicsAndBold() {
		foreach ($this->em_relist as $em => $em_re) {
			foreach ($this->strong_relist as $strong => $strong_re) {
				// Construct list of allowed token expressions.
				$token_relist = array();
				if (isset($this->em_strong_relist["$em$strong"])) {
					$token_relist[] = $this->em_strong_relist["$em$strong"];
				}
				$token_relist[] = $em_re;
				$token_relist[] = $strong_re;

				// Construct master expression from list.
				$token_re = '{(' . implode('|', $token_relist) . ')}';
				$this->em_strong_prepared_relist["$em$strong"] = $token_re;
			}
		}
	}

	/**
	 * Convert Markdown italics (emphasis) and bold (strong) to HTML
	 * @param  string $text
	 * @return string
	 */
	protected function doItalicsAndBold($text) {
		if ($this->in_emphasis_processing) {
			return $text; // avoid reentrency
		}
		$this->in_emphasis_processing = true;

		$token_stack = array('');
		$text_stack = array('');
		$em = '';
		$strong = '';
		$tree_char_em = false;

		while (1) {
			// Get prepared regular expression for seraching emphasis tokens
			// in current context.
			$token_re = $this->em_strong_prepared_relist["$em$strong"];

			// Each loop iteration search for the next emphasis token.
			// Each token is then passed to handleSpanToken.
			$parts = preg_split($token_re, $text, 2, PREG_SPLIT_DELIM_CAPTURE);
			$text_stack[0] .= $parts[0];
			$token =& $parts[1];
			$text =& $parts[2];

			if (empty($token)) {
				// Reached end of text span: empty stack without emitting.
				// any more emphasis.
				while ($token_stack[0]) {
					$text_stack[1] .= array_shift($token_stack);
					$text_stack[0] .= array_shift($text_stack);
				}
				break;
			}

			$token_len = strlen($token);
			if ($tree_char_em) {
				// Reached closing marker while inside a three-char emphasis.
				if ($token_len == 3) {
					// Three-char closing marker, close em and strong.
					array_shift($token_stack);
					$span = array_shift($text_stack);
					$span = $this->runSpanGamut($span);
					$span = "<strong><em>$span</em></strong>";
					$text_stack[0] .= $this->hashPart($span);
					$em = '';
					$strong = '';
				} else {
					// Other closing marker: close one em or strong and
					// change current token state to match the other
					$token_stack[0] = str_repeat($token{0}, 3-$token_len);
					$tag = $token_len == 2 ? "strong" : "em";
					$span = $text_stack[0];
					$span = $this->runSpanGamut($span);
					$span = "<$tag>$span</$tag>";
					$text_stack[0] = $this->hashPart($span);
					$$tag = ''; // $$tag stands for $em or $strong
				}
				$tree_char_em = false;
			} else if ($token_len == 3) {
				if ($em) {
					// Reached closing marker for both em and strong.
					// Closing strong marker:
					for ($i = 0; $i < 2; ++$i) {
						$shifted_token = array_shift($token_stack);
						$tag = strlen($shifted_token) == 2 ? "strong" : "em";
						$span = array_shift($text_stack);
						$span = $this->runSpanGamut($span);
						$span = "<$tag>$span</$tag>";
						$text_stack[0] .= $this->hashPart($span);
						$$tag = ''; // $$tag stands for $em or $strong
					}
				} else {
					// Reached opening three-char emphasis marker. Push on token
					// stack; will be handled by the special condition above.
					$em = $token{0};
					$strong = "$em$em";
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$tree_char_em = true;
				}
			} else if ($token_len == 2) {
				if ($strong) {
					// Unwind any dangling emphasis marker:
					if (strlen($token_stack[0]) == 1) {
						$text_stack[1] .= array_shift($token_stack);
						$text_stack[0] .= array_shift($text_stack);
						$em = '';
					}
					// Closing strong marker:
					array_shift($token_stack);
					$span = array_shift($text_stack);
					$span = $this->runSpanGamut($span);
					$span = "<strong>$span</strong>";
					$text_stack[0] .= $this->hashPart($span);
					$strong = '';
				} else {
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$strong = $token;
				}
			} else {
				// Here $token_len == 1
				if ($em) {
					if (strlen($token_stack[0]) == 1) {
						// Closing emphasis marker:
						array_shift($token_stack);
						$span = array_shift($text_stack);
						$span = $this->runSpanGamut($span);
						$span = "<em>$span</em>";
						$text_stack[0] .= $this->hashPart($span);
						$em = '';
					} else {
						$text_stack[0] .= $token;
					}
				} else {
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$em = $token;
				}
			}
		}
		$this->in_emphasis_processing = false;
		return $text_stack[0];
	}

	/**
	 * Parse Markdown blockquotes to HTML
	 * @param  string $text
	 * @return string
	 */
	protected function doBlockQuotes($text) {
		$text = preg_replace_callback('/
			  (								# Wrap whole match in $1
				(?>
				  ^[ ]*>[ ]?			# ">" at the start of a line
					.+\n					# rest of the first line
				  (.+\n)*					# subsequent consecutive lines
				  \n*						# blanks
				)+
			  )
			/xm',
			array($this, '_doBlockQuotes_callback'), $text);

		return $text;
	}

	/**
	 * Blockquote parsing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doBlockQuotes_callback($matches) {
		$bq = $matches[1];
		// trim one level of quoting - trim whitespace-only lines
		$bq = preg_replace('/^[ ]*>[ ]?|^[ ]+$/m', '', $bq);
		$bq = $this->runBlockGamut($bq); // recurse

		$bq = preg_replace('/^/m', "  ", $bq);
		// These leading spaces cause problem with <pre> content,
		// so we need to fix that:
		$bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx',
			array($this, '_doBlockQuotes_callback2'), $bq);

		return "\n" . $this->hashBlock("<blockquote>\n$bq\n</blockquote>") . "\n\n";
	}

	/**
	 * Blockquote parsing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doBlockQuotes_callback2($matches) {
		$pre = $matches[1];
		$pre = preg_replace('/^  /m', '', $pre);
		return $pre;
	}

	/**
	 * Parse paragraphs
	 *
	 * @param  string $text String to process in paragraphs
	 * @param  boolean $wrap_in_p Whether paragraphs should be wrapped in <p> tags
	 * @return string
	 */
	protected function formParagraphs($text, $wrap_in_p = true) {
		// Strip leading and trailing lines:
		$text = preg_replace('/\A\n+|\n+\z/', '', $text);

		$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

		// Wrap <p> tags and unhashify HTML blocks
		foreach ($grafs as $key => $value) {
			if (!preg_match('/^B\x1A[0-9]+B$/', $value)) {
				// Is a paragraph.
				$value = $this->runSpanGamut($value);
				if ($wrap_in_p) {
					$value = preg_replace('/^([ ]*)/', "<p>", $value);
					$value .= "</p>";
				}
				$grafs[$key] = $this->unhash($value);
			} else {
				// Is a block.
				// Modify elements of @grafs in-place...
				$graf = $value;
				$block = $this->html_hashes[$graf];
				$graf = $block;
//				if (preg_match('{
//					\A
//					(							# $1 = <div> tag
//					  <div  \s+
//					  [^>]*
//					  \b
//					  markdown\s*=\s*  ([\'"])	#	$2 = attr quote char
//					  1
//					  \2
//					  [^>]*
//					  >
//					)
//					(							# $3 = contents
//					.*
//					)
//					(</div>)					# $4 = closing tag
//					\z
//					}xs', $block, $matches))
//				{
//					list(, $div_open, , $div_content, $div_close) = $matches;
//
//					// We can't call Markdown(), because that resets the hash;
//					// that initialization code should be pulled into its own sub, though.
//					$div_content = $this->hashHTMLBlocks($div_content);
//
//					// Run document gamut methods on the content.
//					foreach ($this->document_gamut as $method => $priority) {
//						$div_content = $this->$method($div_content);
//					}
//
//					$div_open = preg_replace(
//						'{\smarkdown\s*=\s*([\'"]).+?\1}', '', $div_open);
//
//					$graf = $div_open . "\n" . $div_content . "\n" . $div_close;
//				}
				$grafs[$key] = $graf;
			}
		}

		return implode("\n\n", $grafs);
	}

	/**
	 * Encode text for a double-quoted HTML attribute. This function
	 * is *not* suitable for attributes enclosed in single quotes.
	 * @param  string $text
	 * @return string
	 */
	protected function encodeAttribute($text) {
		$text = $this->encodeAmpsAndAngles($text);
		$text = str_replace('"', '&quot;', $text);
		return $text;
	}

	/**
	 * Encode text for a double-quoted HTML attribute containing a URL,
	 * applying the URL filter if set. Also generates the textual
	 * representation for the URL (removing mailto: or tel:) storing it in $text.
	 * This function is *not* suitable for attributes enclosed in single quotes.
	 *
	 * @param  string $url
	 * @param  string &$text Passed by reference
	 * @return string        URL
	 */
	protected function encodeURLAttribute($url, &$text = null) {
		if ($this->url_filter_func) {
			$url = call_user_func($this->url_filter_func, $url);
		}

		if (preg_match('{^mailto:}i', $url)) {
			$url = $this->encodeEntityObfuscatedAttribute($url, $text, 7);
		} else if (preg_match('{^tel:}i', $url)) {
			$url = $this->encodeAttribute($url);
			$text = substr($url, 4);
		} else {
			$url = $this->encodeAttribute($url);
			$text = $url;
		}

		return $url;
	}

	/**
	 * Smart processing for ampersands and angle brackets that need to
	 * be encoded. Valid character entities are left alone unless the
	 * no-entities mode is set.
	 * @param  string $text
	 * @return string
	 */
	protected function encodeAmpsAndAngles($text) {
		if ($this->no_entities) {
			$text = str_replace('&', '&amp;', $text);
		} else {
			// Ampersand-encoding based entirely on Nat Irons's Amputator
			// MT plugin: <http://bumppo.net/projects/amputator/>
			$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/',
								'&amp;', $text);
		}
		// Encode remaining <'s
		$text = str_replace('<', '&lt;', $text);

		return $text;
	}

	/**
	 * Parse Markdown automatic links to anchor HTML tags
	 * @param  string $text
	 * @return string
	 */
	protected function doAutoLinks($text) {
		$text = preg_replace_callback('{<((https?|ftp|dict|tel):[^\'">\s]+)>}i',
			array($this, '_doAutoLinks_url_callback'), $text);

		// Email addresses: <address@domain.foo>
		$text = preg_replace_callback('{
			<
			(?:mailto:)?
			(
				(?:
					[-!#$%&\'*+/=?^_`.{|}~\w\x80-\xFF]+
				|
					".*?"
				)
				\@
				(?:
					[-a-z0-9\x80-\xFF]+(\.[-a-z0-9\x80-\xFF]+)*\.[a-z]+
				|
					\[[\d.a-fA-F:]+\]	# IPv4 & IPv6
				)
			)
			>
			}xi',
			array($this, '_doAutoLinks_email_callback'), $text);

		return $text;
	}

	/**
	 * Parse URL callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doAutoLinks_url_callback($matches) {
		$url = $this->encodeURLAttribute($matches[1], $text);
		$link = "<a href=\"$url\">$text</a>";
		return $this->hashPart($link);
	}

	/**
	 * Parse email address callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _doAutoLinks_email_callback($matches) {
		$addr = $matches[1];
		$url = $this->encodeURLAttribute("mailto:$addr", $text);
		$link = "<a href=\"$url\">$text</a>";
		return $this->hashPart($link);
	}

	/**
	 * Input: some text to obfuscate, e.g. "mailto:foo@example.com"
	 *
	 * Output: the same text but with most characters encoded as either a
	 *         decimal or hex entity, in the hopes of foiling most address
	 *         harvesting spam bots. E.g.:
	 *
	 *        &#109;&#x61;&#105;&#x6c;&#116;&#x6f;&#58;&#x66;o&#111;
	 *        &#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;&#101;&#46;&#x63;&#111;
	 *        &#x6d;
	 *
	 * Note: the additional output $tail is assigned the same value as the
	 * ouput, minus the number of characters specified by $head_length.
	 *
	 * Based by a filter by Matthew Wickline, posted to BBEdit-Talk.
	 * With some optimizations by Milian Wolff. Forced encoding of HTML
	 * attribute special characters by Allan Odgaard.
	 *
	 * @param  string  $text
	 * @param  string  &$tail
	 * @param  integer $head_length
	 * @return string
	 */
	protected function encodeEntityObfuscatedAttribute($text, &$tail = null, $head_length = 0) {
		if ($text == "") {
			return $tail = "";
		}

		$chars = preg_split('/(?<!^)(?!$)/', $text);
		$seed = (int)abs(crc32($text) / strlen($text)); // Deterministic seed.

		foreach ($chars as $key => $char) {
			$ord = ord($char);
			// Ignore non-ascii chars.
			if ($ord < 128) {
				$r = ($seed * (1 + $key)) % 100; // Pseudo-random function.
				// roughly 10% raw, 45% hex, 45% dec
				// '@' *must* be encoded. I insist.
				// '"' and '>' have to be encoded inside the attribute
				if ($r > 90 && strpos('@"&>', $char) === false) {
					/* do nothing */
				} else if ($r < 45) {
					$chars[$key] = '&#x'.dechex($ord).';';
				} else {
					$chars[$key] = '&#'.$ord.';';
				}
			}
		}

		$text = implode('', $chars);
		$tail = $head_length ? implode('', array_slice($chars, $head_length)) : $text;

		return $text;
	}

	/**
	 * Take the string $str and parse it into tokens, hashing embeded HTML,
	 * escaped characters and handling code spans.
	 * @param  string $str
	 * @return string
	 */
	protected function parseSpan($str) {
		$output = '';

		$span_re = '{
				(
					\\\\'.$this->escape_chars_re.'
				|
					(?<![`\\\\])
					`+						# code span marker
			'.( $this->no_markup ? '' : '
				|
					<!--    .*?     -->		# comment
				|
					<\?.*?\?> | <%.*?%>		# processing instruction
				|
					<[!$]?[-a-zA-Z0-9:_]+	# regular tags
					(?>
						\s
						(?>[^"\'>]+|"[^"]*"|\'[^\']*\')*
					)?
					>
				|
					<[-a-zA-Z0-9:_]+\s*/> # xml-style empty tag
				|
					</[-a-zA-Z0-9:_]+\s*> # closing tag
			').'
				)
				}xs';

		while (1) {
			// Each loop iteration seach for either the next tag, the next
			// openning code span marker, or the next escaped character.
			// Each token is then passed to handleSpanToken.
			$parts = preg_split($span_re, $str, 2, PREG_SPLIT_DELIM_CAPTURE);

			// Create token from text preceding tag.
			if ($parts[0] != "") {
				$output .= $parts[0];
			}

			// Check if we reach the end.
			if (isset($parts[1])) {
				$output .= $this->handleSpanToken($parts[1], $parts[2]);
				$str = $parts[2];
			} else {
				break;
			}
		}

		return $output;
	}

	/**
	 * Handle $token provided by parseSpan by determining its nature and
	 * returning the corresponding value that should replace it.
	 * @param  string $token
	 * @param  string &$str
	 * @return string
	 */
	protected function handleSpanToken($token, &$str) {
		switch ($token{0}) {
			case "\\":
				return $this->hashPart("&#". ord($token{1}). ";");
			case "`":
				// Search for end marker in remaining text.
				if (preg_match('/^(.*?[^`])'.preg_quote($token).'(?!`)(.*)$/sm',
					$str, $matches))
				{
					$str = $matches[2];
					$codespan = $this->makeCodeSpan($matches[1]);
					return $this->hashPart($codespan);
				}
				return $token; // Return as text since no ending marker found.
			default:
				return $this->hashPart($token);
		}
	}

	/**
	 * Remove one level of line-leading tabs or spaces
	 * @param  string $text
	 * @return string
	 */
	protected function outdent($text) {
		return preg_replace('/^(\t|[ ]{1,' . $this->tab_width . '})/m', '', $text);
	}


	/**
	 * String length function for detab. `_initDetab` will create a function to
	 * handle UTF-8 if the default function does not exist.
	 * @var string
	 */
	protected $utf8_strlen = 'mb_strlen';

	/**
	 * Replace tabs with the appropriate amount of spaces.
	 *
	 * For each line we separate the line in blocks delemited by tab characters.
	 * Then we reconstruct every line by adding the  appropriate number of space
	 * between each blocks.
	 *
	 * @param  string $text
	 * @return string
	 */
	protected function detab($text) {
		$text = preg_replace_callback('/^.*\t.*$/m',
			array($this, '_detab_callback'), $text);

		return $text;
	}

	/**
	 * Replace tabs callback
	 * @param  string $matches
	 * @return string
	 */
	protected function _detab_callback($matches) {
		$line = $matches[0];
		$strlen = $this->utf8_strlen; // strlen function for UTF-8.

		// Split in blocks.
		$blocks = explode("\t", $line);
		// Add each blocks to the line.
		$line = $blocks[0];
		unset($blocks[0]); // Do not add first block twice.
		foreach ($blocks as $block) {
			// Calculate amount of space, insert spaces, insert block.
			$amount = $this->tab_width -
				$strlen($line, 'UTF-8') % $this->tab_width;
			$line .= str_repeat(" ", $amount) . $block;
		}
		return $line;
	}

	/**
	 * Check for the availability of the function in the `utf8_strlen` property
	 * (initially `mb_strlen`). If the function is not available, create a
	 * function that will loosely count the number of UTF-8 characters with a
	 * regular expression.
	 * @return void
	 */
	protected function _initDetab() {

		if (function_exists($this->utf8_strlen)) {
			return;
		}

		$this->utf8_strlen = function($text) {
			return preg_match_all('/[\x00-\xBF]|[\xC0-\xFF][\x80-\xBF]*/', $text, $m);
		};
	}

	/**
	 * Swap back in all the tags hashed by _HashHTMLBlocks.
	 * @param  string $text
	 * @return string
	 */
	protected function unhash($text) {
		return preg_replace_callback('/(.)\x1A[0-9]+\1/',
			array($this, '_unhash_callback'), $text);
	}

	/**
	 * Unhashing callback
	 * @param  array $matches
	 * @return string
	 */
	protected function _unhash_callback($matches) {
		return $this->html_hashes[$matches[0]];
	}
}
