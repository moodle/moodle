<?php
#
# Markdown Extra  -  A text-to-HTML conversion tool for web writers
#
# PHP Markdown & Extra
# Copyright (c) 2004-2007 Michel Fortin  
# <http://www.michelf.com/projects/php-markdown/>
#
# Original Markdown
# Copyright (c) 2004-2006 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#


define( 'MARKDOWN_VERSION',       "1.0.1f" ); # Wed 7 Feb 2007
define( 'MARKDOWNEXTRA_VERSION',  "1.1.2" );  # Wed 7 Feb 2007


#
# Global default settings:
#

# Change to ">" for HTML output
define( 'MARKDOWN_EMPTY_ELEMENT_SUFFIX',  " />");

# Define the width of a tab for code blocks.
define( 'MARKDOWN_TAB_WIDTH',     4 );

# Optional title attribute for footnote links and backlinks.
define( 'MARKDOWN_FN_LINK_TITLE',         "" );
define( 'MARKDOWN_FN_BACKLINK_TITLE',     "" );

# Optional class attribute for footnote links and backlinks.
define( 'MARKDOWN_FN_LINK_CLASS',         "" );
define( 'MARKDOWN_FN_BACKLINK_CLASS',     "" );


#
# WordPress settings:
#

# Change to false to remove Markdown from posts and/or comments.
define( 'MARKDOWN_WP_POSTS',      true );
define( 'MARKDOWN_WP_COMMENTS',   true );



### Standard Function Interface ###

define( 'MARKDOWN_PARSER_CLASS',  'MarkdownExtra_Parser' );

function Markdown($text) {
#
# Initialize the parser and return the result of its transform method.
#
	# Setup static parser variable.
	static $parser;
	if (!isset($parser)) {
		$parser_class = MARKDOWN_PARSER_CLASS;
		$parser = new $parser_class;
	}

	# Transform text using parser.
	return $parser->transform($text);
}


### WordPress Plugin Interface ###

/*
Plugin Name: Markdown Extra
Plugin URI: http://www.michelf.com/projects/php-markdown/
Description: <a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://www.michelf.com/projects/php-markdown/">More...</a>
Version: 1.1.2
Author: Michel Fortin
Author URI: http://www.michelf.com/
*/

if (isset($wp_version)) {
	# More details about how it works here:
	# <http://www.michelf.com/weblog/2005/wordpress-text-flow-vs-markdown/>
	
	# Post content and excerpts
	# - Remove WordPress paragraph generator.
	# - Run Markdown on excerpt, then remove all tags.
	# - Add paragraph tag around the excerpt, but remove it for the excerpt rss.
	if (MARKDOWN_WP_POSTS) {
		remove_filter('the_content',  'wpautop');
		remove_filter('the_excerpt',  'wpautop');
		add_filter('the_content',     'Markdown', 6);
		add_filter('get_the_excerpt', 'Markdown', 6);
		add_filter('get_the_excerpt', 'trim', 7);
		add_filter('the_excerpt',     'mdwp_add_p');
		add_filter('the_excerpt_rss', 'mdwp_strip_p');
		
		remove_filter('content_save_pre',  'balanceTags', 50);
		remove_filter('excerpt_save_pre',  'balanceTags', 50);
		add_filter('the_content',  	  'balanceTags', 50);
		add_filter('get_the_excerpt', 'balanceTags', 9);
	}
	
	# Comments
	# - Remove WordPress paragraph generator.
	# - Remove WordPress auto-link generator.
	# - Scramble important tags before passing them to the kses filter.
	# - Run Markdown on excerpt then remove paragraph tags.
	if (MARKDOWN_WP_COMMENTS) {
		remove_filter('comment_text', 'wpautop');
		remove_filter('comment_text', 'make_clickable');
		add_filter('pre_comment_content', 'Markdown', 6);
		add_filter('pre_comment_content', 'mdwp_hide_tags', 8);
		add_filter('pre_comment_content', 'mdwp_show_tags', 12);
		add_filter('get_comment_text',    'Markdown', 6);
		add_filter('get_comment_excerpt', 'Markdown', 6);
		add_filter('get_comment_excerpt', 'mdwp_strip_p', 7);
	
		global $markdown_hidden_tags;
		$markdown_hidden_tags = array(
			'<p>'	=> md5('<p>'),		'</p>'	=> md5('</p>'),
			'<pre>'	=> md5('<pre>'),	'</pre>'=> md5('</pre>'),
			'<ol>'	=> md5('<ol>'),		'</ol>'	=> md5('</ol>'),
			'<ul>'	=> md5('<ul>'),		'</ul>'	=> md5('</ul>'),
			'<li>'	=> md5('<li>'),		'</li>'	=> md5('</li>'),
			);
	}
	
	function mdwp_add_p($text) {
		if (!preg_match('{^$|^<(p|ul|ol|dl|pre|blockquote)>}i', $text)) {
			$text = '<p>'.$text.'</p>';
			$text = preg_replace('{\n{2,}}', "</p>\n\n<p>", $text);
		}
		return $text;
	}
	
	function mdwp_strip_p($t) { return preg_replace('{</?p>}i', '', $t); }

	function mdwp_hide_tags($text) {
		global $markdown_hidden_tags;
		return str_replace(array_keys($markdown_hidden_tags), 
							array_values($markdown_hidden_tags), $text);
	}
	function mdwp_show_tags($text) {
		global $markdown_hidden_tags;
		return str_replace(array_values($markdown_hidden_tags), 
							array_keys($markdown_hidden_tags), $text);
	}
}


### bBlog Plugin Info ###

function identify_modifier_markdown() {
	return array(
		'name' => 'markdown',
		'type' => 'modifier',
		'nicename' => 'PHP Markdown Extra',
		'description' => 'A text-to-HTML conversion tool for web writers',
		'authors' => 'Michel Fortin and John Gruber',
		'licence' => 'GPL',
		'version' => MARKDOWNEXTRA_VERSION,
		'help' => '<a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://www.michelf.com/projects/php-markdown/">More...</a>',
		);
}


### Smarty Modifier Interface ###

function smarty_modifier_markdown($text) {
	return Markdown($text);
}


### Textile Compatibility Mode ###

# Rename this file to "classTextile.php" and it can replace Textile everywhere.

if (strcasecmp(substr(__FILE__, -16), "classTextile.php") == 0) {
	# Try to include PHP SmartyPants. Should be in the same directory.
	@include_once 'smartypants.php';
	# Fake Textile class. It calls Markdown instead.
	class Textile {
		function TextileThis($text, $lite='', $encode='') {
			if ($lite == '' && $encode == '')    $text = Markdown($text);
			if (function_exists('SmartyPants'))  $text = SmartyPants($text);
			return $text;
		}
		# Fake restricted version: restrictions are not supported for now.
		function TextileRestricted($text, $lite='', $noimage='') {
			return $this->TextileThis($text, $lite);
		}
		# Workaround to ensure compatibility with TextPattern 4.0.3.
		function blockLite($text) { return $text; }
	}
}



#
# Markdown Parser Class
#

class Markdown_Parser {

	# Regex to match balanced [brackets].
	# Needed to insert a maximum bracked depth while converting to PHP.
	var $nested_brackets_depth = 6;
	var $nested_brackets;

	# Table of hash values for escaped characters:
	var $escape_chars = '\`*_{}[]()>#+-.!';
	var $escape_table = array();
	var $backslash_escape_table = array();

	# Change to ">" for HTML output.
	var $empty_element_suffix = MARKDOWN_EMPTY_ELEMENT_SUFFIX;
	var $tab_width = MARKDOWN_TAB_WIDTH;


	function Markdown_Parser() {
	#
	# Constructor function. Initialize appropriate member variables.
	#
		$this->_initDetab();
	
		$this->nested_brackets = 
			str_repeat('(?>[^\[\]]+|\[', $this->nested_brackets_depth).
			str_repeat('\])*', $this->nested_brackets_depth);
		
		# Create an identical table but for escaped characters.
		foreach (preg_split('/(?!^|$)/', $this->escape_chars) as $char) {
			$hash = md5($char);
			$this->escape_table[$char] = $hash;
			$this->backslash_escape_table["\\$char"] = $hash;
		}
		
		# Sort document, block, and span gamut in ascendent priority order.
		asort($this->document_gamut);
		asort($this->block_gamut);
		asort($this->span_gamut);
	}


	# Internal hashes used during transformation.
	var $urls = array();
	var $titles = array();
	var $html_blocks = array();
	var $html_hashes = array(); # Contains both blocks and span hashes.


	function transform($text) {
	#
	# Main function. The order in which other subs are called here is
	# essential. Link and image substitutions need to happen before
	# _EscapeSpecialCharsWithinTagAttributes(), so that any *'s or _'s in the <a>
	# and <img> tags get encoded.
	#
		# Clear the global hashes. If we don't clear these, you get conflicts
		# from other articles when generating a page which contains more than
		# one article (e.g. an index page that shows the N most recent
		# articles):
		$this->urls = array();
		$this->titles = array();
		$this->html_blocks = array();
		$this->html_hashes = array();

		# Standardize line endings:
		#   DOS to Unix and Mac to Unix
		$text = str_replace(array("\r\n", "\r"), "\n", $text);

		# Make sure $text ends with a couple of newlines:
		$text .= "\n\n";

		# Convert all tabs to spaces.
		$text = $this->detab($text);

		# Turn block-level HTML blocks into hash entries
		$text = $this->hashHTMLBlocks($text);

		# Strip any lines consisting only of spaces and tabs.
		# This makes subsequent regexen easier to write, because we can
		# match consecutive blank lines with /\n+/ instead of something
		# contorted like /[ \t]*\n+/ .
		$text = preg_replace('/^[ \t]+$/m', '', $text);

		# Run document gamut methods.
		foreach ($this->document_gamut as $method => $priority) {
			$text = $this->$method($text);
		}

		return $text . "\n";
	}
	
	var $document_gamut = array(
		# Strip link definitions, store in hashes.
		"stripLinkDefinitions" => 20,
		
		"runBasicBlockGamut"   => 30,
		"unescapeSpecialChars" => 90,
		);


	function stripLinkDefinitions($text) {
	#
	# Strips link definitions from text, stores the URLs and titles in
	# hash references.
	#
		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: ^[id]: url "optional title"
		$text = preg_replace_callback('{
							^[ ]{0,'.$less_than_tab.'}\[(.+)\][ ]?:	# id = $1
							  [ \t]*
							  \n?				# maybe *one* newline
							  [ \t]*
							<?(\S+?)>?			# url = $2
							  [ \t]*
							  \n?				# maybe one newline
							  [ \t]*
							(?:
								(?<=\s)			# lookbehind for whitespace
								["(]
								(.*?)			# title = $3
								[")]
								[ \t]*
							)?	# title is optional
							(?:\n+|\Z)
			}xm',
			array(&$this, '_stripLinkDefinitions_callback'),
			$text);
		return $text;
	}
	function _stripLinkDefinitions_callback($matches) {
		$link_id = strtolower($matches[1]);
		$this->urls[$link_id] = $this->encodeAmpsAndAngles($matches[2]);
		if (isset($matches[3]))
			$this->titles[$link_id] = str_replace('"', '&quot;', $matches[3]);
		return ''; # String that will replace the block
	}


	function hashHTMLBlocks($text) {
		$less_than_tab = $this->tab_width - 1;

		# Hashify HTML blocks:
		# We only want to do this for block-level HTML tags, such as headers,
		# lists, and tables. That's because we still want to wrap <p>s around
		# "paragraphs" that are wrapped in non-block-level tags, such as anchors,
		# phrase emphasis, and spans. The list of tags we're looking for is
		# hard-coded:
		$block_tags_a = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|'.
						'script|noscript|form|fieldset|iframe|math|ins|del';
		$block_tags_b = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|'.
						'script|noscript|form|fieldset|iframe|math';

		# Regular expression for the content of a block tag.
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
					(?:
					  />
					|
					  >', $nested_tags_level).	# end of opening tag
					  '.*?'.					# last level nested tag content
			str_repeat('
					  </\2\s*>	# closing nested tag
					)
				  |				
					<(?!/\2\s*>	# other tags with a different name
				  )
				)*',
				$nested_tags_level);

		# First, look for nested blocks, e.g.:
		# 	<div>
		# 		<div>
		# 		tags for inner block must be indented.
		# 		</div>
		# 	</div>
		#
		# The outermost tags must start at the left margin for this to match, and
		# the inner nested divs must be indented.
		# We need to do this before the next, more liberal match, because the next
		# match will start at the first `<div>` and stop at the first `</div>`.
		$text = preg_replace_callback('{
					(						# save in $1
						^					# start of line  (with /m)
						<('.$block_tags_a.')# start tag = $2
						'.$attr.'>\n		# attributes followed by > and \n
						'.$content.'		# content, support nesting
						</\2>				# the matching end tag
						[ \t]*				# trailing spaces/tabs
						(?=\n+|\Z)	# followed by a newline or end of document
					)
			}xm',
			array(&$this, '_hashHTMLBlocks_callback'),
			$text);

		#
		# Match from `\n<tag>` to `</tag>\n`, handling nested tags in between.
		#
		$text = preg_replace_callback('{
					(						# save in $1
						^					# start of line  (with /m)
						<('.$block_tags_b.')# start tag = $2
						'.$attr.'>			# attributes followed by >
						'.$content.'		# content, support nesting
						</\2>				# the matching end tag
						[ \t]*				# trailing spaces/tabs
						(?=\n+|\Z)	# followed by a newline or end of document
					)
			}xm',
			array(&$this, '_hashHTMLBlocks_callback'),
			$text);

		# Special case just for <hr />. It was easier to make a special case than
		# to make the other regex more complicated.
		$text = preg_replace_callback('{
					(?:
						(?<=\n\n)		# Starting after a blank line
						|				# or
						\A\n?			# the beginning of the doc
					)
					(						# save in $1
						[ ]{0,'.$less_than_tab.'}
						<(hr)				# start tag = $2
						\b					# word break
						([^<>])*?			# 
						/?>					# the matching end tag
						[ \t]*
						(?=\n{2,}|\Z)		# followed by a blank line or end of document
					)
			}x',
			array(&$this, '_hashHTMLBlocks_callback'),
			$text);

		# Special case for standalone HTML comments:
		$text = preg_replace_callback('{
				(?:
					(?<=\n\n)		# Starting after a blank line
					|				# or
					\A\n?			# the beginning of the doc
				)
				(						# save in $1
					[ ]{0,'.$less_than_tab.'}
					(?s:
						<!-- .*? -->
					)
					[ \t]*
					(?=\n{2,}|\Z)		# followed by a blank line or end of document
				)
			}x',
			array(&$this, '_hashHTMLBlocks_callback'),
			$text);

		# PHP and ASP-style processor instructions (<? and <%)
		$text = preg_replace_callback('{
				(?:
					(?<=\n\n)		# Starting after a blank line
					|				# or
					\A\n?			# the beginning of the doc
				)
				(						# save in $1
					[ ]{0,'.$less_than_tab.'}
					(?s:
						<([?%])			# $2
						.*?
						\2>
					)
					[ \t]*
					(?=\n{2,}|\Z)		# followed by a blank line or end of document
				)
			}x',
			array(&$this, '_hashHTMLBlocks_callback'),
			$text);

		return $text;
	}
	function _hashHTMLBlocks_callback($matches) {
		$text = $matches[1];
		$key  = $this->hashBlock($text);
		return "\n\n$key\n\n";
	}


	function hashBlock($text) {
	#
	# Called whenever a tag must be hashed when a function insert a block-level 
	# tag in $text, it pass through this function and is automaticaly escaped, 
	# which remove the need to call _HashHTMLBlocks at every step.
	#
		# Swap back any tag hash found in $text so we do not have to `unhash`
		# multiple times at the end.
		$text = $this->unhash($text);
		
		# Then hash the block.
		$key = md5($text);
		$this->html_hashes[$key] = $text;
		$this->html_blocks[$key] = $text;
		return $key; # String that will replace the tag.
	}


	function hashSpan($text) {
	#
	# Called whenever a tag must be hashed when a function insert a span-level 
	# element in $text, it pass through this function and is automaticaly 
	# escaped, blocking invalid nested overlap.
	#
		# Swap back any tag hash found in $text so we do not have to `unhash`
		# multiple times at the end.
		$text = $this->unhash($text);
		
		# Then hash the span.
		$key = md5($text);
		$this->html_hashes[$key] = $text;
		return $key; # String that will replace the span tag.
	}


	var $block_gamut = array(
	#
	# These are all the transformations that form block-level
	# tags like paragraphs, headers, and list items.
	#
		"doHeaders"         => 10,
		"doHorizontalRules" => 20,
		
		"doLists"           => 40,
		"doCodeBlocks"      => 50,
		"doBlockQuotes"     => 60,
		);

	function runBlockGamut($text) {
	#
	# Run block gamut tranformations.
	#
		# We need to escape raw HTML in Markdown source before doing anything 
		# else. This need to be done for each block, and not only at the 
		# begining in the Markdown function since hashed blocks can be part of
		# list items and could have been indented. Indented blocks would have 
		# been seen as a code block in a previous pass of hashHTMLBlocks.
		$text = $this->hashHTMLBlocks($text);
		
		return $this->runBasicBlockGamut($text);
	}
	
	function runBasicBlockGamut($text) {
	#
	# Run block gamut tranformations, without hashing HTML blocks. This is 
	# useful when HTML blocks are known to be already hashed, like in the first
	# whole-document pass.
	#
		foreach ($this->block_gamut as $method => $priority) {
			$text = $this->$method($text);
		}
		
		# Finally form paragraph and restore hashed blocks.
		$text = $this->formParagraphs($text);

		return $text;
	}
	
	
	function doHorizontalRules($text) {
		# Do Horizontal Rules:
		return preg_replace(
			array('{^[ ]{0,2}([ ]?\*[ ]?){3,}[ \t]*$}mx',
				  '{^[ ]{0,2}([ ]? -[ ]?){3,}[ \t]*$}mx',
				  '{^[ ]{0,2}([ ]? _[ ]?){3,}[ \t]*$}mx'),
			"\n".$this->hashBlock("<hr$this->empty_element_suffix")."\n", 
			$text);
	}


	var $span_gamut = array(
	#
	# These are all the transformations that occur *within* block-level
	# tags like paragraphs, headers, and list items.
	#
		"escapeSpecialCharsWithinTagAttributes"	=> -20,
		"doCodeSpans"							=> -10,
		"encodeBackslashEscapes"				=>  -5,

		# Process anchor and image tags. Images must come first,
		# because ![foo][f] looks like an anchor.
		"doImages"            =>  10,
		"doAnchors"           =>  20,
		
		# Make links out of things like `<http://example.com/>`
		# Must come after doAnchors, because you can use < and >
		# delimiters in inline links like [this](<url>).
		"doAutoLinks"         =>  30,
		"encodeAmpsAndAngles" =>  40,

		"doItalicsAndBold"    =>  50,
		"doHardBreaks"        =>  60,
		);

	function runSpanGamut($text) {
	#
	# Run span gamut tranformations.
	#
		foreach ($this->span_gamut as $method => $priority) {
			$text = $this->$method($text);
		}

		return $text;
	}
	
	
	function doHardBreaks($text) {
		# Do hard breaks:
		$br_tag = $this->hashSpan("<br$this->empty_element_suffix\n");
		return preg_replace('/ {2,}\n/', $br_tag, $text);
	}


	function escapeSpecialCharsWithinTagAttributes($text) {
	#
	# Within tags -- meaning between < and > -- encode [\ ` * _] so they
	# don't conflict with their use in Markdown for code, italics and strong.
	# We're replacing each such character with its corresponding MD5 checksum
	# value; this is likely overkill, but it should prevent us from colliding
	# with the escape values by accident.
	#
		$tokens = $this->tokenizeHTML($text);
		$text = '';   # rebuild $text from the tokens

		foreach ($tokens as $cur_token) {
			if ($cur_token[0] == 'tag') {
				$cur_token[1] = str_replace('\\', $this->escape_table['\\'], $cur_token[1]);
				$cur_token[1] = str_replace(array('`'), $this->escape_table['`'], $cur_token[1]);
				$cur_token[1] = str_replace('*', $this->escape_table['*'], $cur_token[1]);
				$cur_token[1] = str_replace('_', $this->escape_table['_'], $cur_token[1]);
			}
			$text .= $cur_token[1];
		}
		return $text;
	}


	function doAnchors($text) {
	#
	# Turn Markdown link shortcuts into XHTML <a> tags.
	#
		#
		# First, handle reference-style links: [link text] [id]
		#
		$text = preg_replace_callback('{
			(					# wrap whole match in $1
			  \[
				('.$this->nested_brackets.')	# link text = $2
			  \]

			  [ ]?				# one optional space
			  (?:\n[ ]*)?		# one optional newline followed by spaces

			  \[
				(.*?)		# id = $3
			  \]
			)
			}xs',
			array(&$this, '_doAnchors_reference_callback'), $text);

		#
		# Next, inline-style links: [link text](url "optional title")
		#
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  \[
				('.$this->nested_brackets.')	# link text = $2
			  \]
			  \(			# literal paren
				[ \t]*
				<?(.*?)>?	# href = $3
				[ \t]*
				(			# $4
				  ([\'"])	# quote char = $5
				  (.*?)		# Title = $6
				  \5		# matching quote
				  [ \t]*	# ignore any spaces/tabs between closing quote and )
				)?			# title is optional
			  \)
			)
			}xs',
			array(&$this, '_DoAnchors_inline_callback'), $text);

		#
		# Last, handle reference-style shortcuts: [link text]
		# These must come last in case you've also got [link test][1]
		# or [link test](/foo)
		#
//		$text = preg_replace_callback('{
//			(					# wrap whole match in $1
//			  \[
//				([^\[\]]+)		# link text = $2; can\'t contain [ or ]
//			  \]
//			)
//			}xs',
//			array(&$this, '_doAnchors_reference_callback'), $text);

		return $text;
	}
	function _doAnchors_reference_callback($matches) {
		$whole_match =  $matches[1];
		$link_text   =  $matches[2];
		$link_id     =& $matches[3];

		if ($link_id == "") {
			# for shortcut links like [this][] or [this].
			$link_id = $link_text;
		}
		
		# lower-case and turn embedded newlines into spaces
		$link_id = strtolower($link_id);
		$link_id = preg_replace('{[ ]?\n}', ' ', $link_id);

		if (isset($this->urls[$link_id])) {
			$url = $this->urls[$link_id];
			$url = $this->encodeAmpsAndAngles($url);
			
			$result = "<a href=\"$url\"";
			if ( isset( $this->titles[$link_id] ) ) {
				$title = $this->titles[$link_id];
				$title = $this->encodeAmpsAndAngles($title);
				$result .=  " title=\"$title\"";
			}
		
			$link_text = $this->runSpanGamut($link_text);
			$result .= ">$link_text</a>";
			$result = $this->hashSpan($result);
		}
		else {
			$result = $whole_match;
		}
		return $result;
	}
	function _doAnchors_inline_callback($matches) {
		$whole_match	=  $matches[1];
		$link_text		=  $this->runSpanGamut($matches[2]);
		$url			=  $matches[3];
		$title			=& $matches[6];
		
		$url = $this->encodeAmpsAndAngles($url);

		$result = "<a href=\"$url\"";
		if (isset($title)) {
			$title = str_replace('"', '&quot;', $title);
			$title = $this->encodeAmpsAndAngles($title);
			$result .=  " title=\"$title\"";
		}
		
		$link_text = $this->runSpanGamut($link_text);
		$result .= ">$link_text</a>";

		return $this->hashSpan($result);
	}


	function doImages($text) {
	#
	# Turn Markdown image shortcuts into <img> tags.
	#
		#
		# First, handle reference-style labeled images: ![alt text][id]
		#
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  !\[
				('.$this->nested_brackets.')		# alt text = $2
			  \]

			  [ ]?				# one optional space
			  (?:\n[ ]*)?		# one optional newline followed by spaces

			  \[
				(.*?)		# id = $3
			  \]

			)
			}xs', 
			array(&$this, '_doImages_reference_callback'), $text);

		#
		# Next, handle inline images:  ![alt text](url "optional title")
		# Don't forget: encode * and _
		#
		$text = preg_replace_callback('{
			(				# wrap whole match in $1
			  !\[
				('.$this->nested_brackets.')		# alt text = $2
			  \]
			  \s?			# One optional whitespace character
			  \(			# literal paren
				[ \t]*
				<?(\S+?)>?	# src url = $3
				[ \t]*
				(			# $4
				  ([\'"])	# quote char = $5
				  (.*?)		# title = $6
				  \5		# matching quote
				  [ \t]*
				)?			# title is optional
			  \)
			)
			}xs',
			array(&$this, '_doImages_inline_callback'), $text);

		return $text;
	}
	function _doImages_reference_callback($matches) {
		$whole_match = $matches[1];
		$alt_text    = $matches[2];
		$link_id     = strtolower($matches[3]);

		if ($link_id == "") {
			$link_id = strtolower($alt_text); # for shortcut links like ![this][].
		}

		$alt_text = str_replace('"', '&quot;', $alt_text);
		if (isset($this->urls[$link_id])) {
			$url = $this->urls[$link_id];
			$result = "<img src=\"$url\" alt=\"$alt_text\"";
			if (isset($this->titles[$link_id])) {
				$title = $this->titles[$link_id];
				$result .=  " title=\"$title\"";
			}
			$result .= $this->empty_element_suffix;
			$result = $this->hashSpan($result);
		}
		else {
			# If there's no such link ID, leave intact:
			$result = $whole_match;
		}

		return $result;
	}
	function _doImages_inline_callback($matches) {
		$whole_match	= $matches[1];
		$alt_text		= $matches[2];
		$url			= $matches[3];
		$title			=& $matches[6];

		$alt_text = str_replace('"', '&quot;', $alt_text);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($title)) {
			$title = str_replace('"', '&quot;', $title);
			$result .=  " title=\"$title\""; # $title already quoted
		}
		$result .= $this->empty_element_suffix;

		return $this->hashSpan($result);
	}


	function doHeaders($text) {
		# Setext-style headers:
		#	  Header 1
		#	  ========
		#  
		#	  Header 2
		#	  --------
		#
		$text = preg_replace_callback('{ ^(.+)[ \t]*\n=+[ \t]*\n+ }mx',
			array(&$this, '_doHeaders_callback_setext_h1'), $text);
		$text = preg_replace_callback('{ ^(.+)[ \t]*\n-+[ \t]*\n+ }mx',
			array(&$this, '_doHeaders_callback_setext_h2'), $text);

		# atx-style headers:
		#	# Header 1
		#	## Header 2
		#	## Header 2 with closing hashes ##
		#	...
		#	###### Header 6
		#
		$text = preg_replace_callback('{
				^(\#{1,6})	# $1 = string of #\'s
				[ \t]*
				(.+?)		# $2 = Header text
				[ \t]*
				\#*			# optional closing #\'s (not counted)
				\n+
			}xm',
			array(&$this, '_doHeaders_callback_atx'), $text);

		return $text;
	}
	function _doHeaders_callback_setext_h1($matches) {
		$block = "<h1>".$this->runSpanGamut($matches[1])."</h1>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}
	function _doHeaders_callback_setext_h2($matches) {
		$block = "<h2>".$this->runSpanGamut($matches[1])."</h2>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}
	function _doHeaders_callback_atx($matches) {
		$level = strlen($matches[1]);
		$block = "<h$level>".$this->runSpanGamut($matches[2])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}


	function doLists($text) {
	#
	# Form HTML ordered (numbered) and unordered (bulleted) lists.
	#
		$less_than_tab = $this->tab_width - 1;

		# Re-usable patterns to match list item bullets and number markers:
		$marker_ul  = '[*+-]';
		$marker_ol  = '\d+[.]';
		$marker_any = "(?:$marker_ul|$marker_ol)";

		$markers = array($marker_ul, $marker_ol);

		foreach ($markers as $marker) {
			# Re-usable pattern to match any entirel ul or ol list:
			$whole_list = '
				(								# $1 = whole list
				  (								# $2
					[ ]{0,'.$less_than_tab.'}
					('.$marker.')				# $3 = first list item marker
					[ \t]+
				  )
				  (?s:.+?)
				  (								# $4
					  \z
					|
					  \n{2,}
					  (?=\S)
					  (?!						# Negative lookahead for another list item marker
						[ \t]*
						'.$marker.'[ \t]+
					  )
				  )
				)
			'; // mx
			
			# We use a different prefix before nested lists than top-level lists.
			# See extended comment in _ProcessListItems().
		
			if ($this->list_level) {
				$text = preg_replace_callback('{
						^
						'.$whole_list.'
					}mx',
					array(&$this, '_doLists_callback'), $text);
			}
			else {
				$text = preg_replace_callback('{
						(?:(?<=\n)\n|\A\n?) # Must eat the newline
						'.$whole_list.'
					}mx',
					array(&$this, '_doLists_callback'), $text);
			}
		}

		return $text;
	}
	function _doLists_callback($matches) {
		# Re-usable patterns to match list item bullets and number markers:
		$marker_ul  = '[*+-]';
		$marker_ol  = '\d+[.]';
		$marker_any = "(?:$marker_ul|$marker_ol)";
		
		$list = $matches[1];
		$list_type = preg_match("/$marker_ul/", $matches[3]) ? "ul" : "ol";
		
		$marker_any = ( $list_type == "ul" ? $marker_ul : $marker_ol );
		
		$list .= "\n";
		$result = $this->processListItems($list, $marker_any);
		
		$result = $this->hashBlock("<$list_type>\n" . $result . "</$list_type>");
		return "\n". $result ."\n\n";
	}

	var $list_level = 0;

	function processListItems($list_str, $marker_any) {
	#
	#	Process the contents of a single ordered or unordered list, splitting it
	#	into individual list items.
	#
		# The $this->list_level global keeps track of when we're inside a list.
		# Each time we enter a list, we increment it; when we leave a list,
		# we decrement. If it's zero, we're not in a list anymore.
		#
		# We do this because when we're not inside a list, we want to treat
		# something like this:
		#
		#		I recommend upgrading to version
		#		8. Oops, now this line is treated
		#		as a sub-list.
		#
		# As a single paragraph, despite the fact that the second line starts
		# with a digit-period-space sequence.
		#
		# Whereas when we're inside a list (or sub-list), that line will be
		# treated as the start of a sub-list. What a kludge, huh? This is
		# an aspect of Markdown's syntax that's hard to parse perfectly
		# without resorting to mind-reading. Perhaps the solution is to
		# change the syntax rules such that sub-lists must start with a
		# starting cardinal number; e.g. "1." or "a.".
		
		$this->list_level++;

		# trim trailing blank lines:
		$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

		$list_str = preg_replace_callback('{
			(\n)?							# leading line = $1
			(^[ \t]*)						# leading whitespace = $2
			('.$marker_any.') [ \t]+		# list marker = $3
			((?s:.+?))						# list item text   = $4
			(?:(\n+(?=\n))|\n)				# tailing blank line = $5
			(?= \n* (\z | \2 ('.$marker_any.') [ \t]+))
			}xm',
			array(&$this, '_processListItems_callback'), $list_str);

		$this->list_level--;
		return $list_str;
	}
	function _processListItems_callback($matches) {
		$item = $matches[4];
		$leading_line =& $matches[1];
		$leading_space =& $matches[2];
		$tailing_blank_line =& $matches[5];

		if ($leading_line || $tailing_blank_line || 
			preg_match('/\n{2,}/', $item))
		{
			$item = $this->runBlockGamut($this->outdent($item)."\n");
		}
		else {
			# Recursion for sub-lists:
			$item = $this->doLists($this->outdent($item));
			$item = preg_replace('/\n+$/', '', $item);
			$item = $this->runSpanGamut($item);
		}

		return "<li>" . $item . "</li>\n";
	}


	function doCodeBlocks($text) {
	#
	#	Process Markdown `<pre><code>` blocks.
	#
		$text = preg_replace_callback('{
				(?:\n\n|\A)
				(	            # $1 = the code block -- one or more lines, starting with a space/tab
				  (?:
					(?:[ ]{'.$this->tab_width.'} | \t)  # Lines must start with a tab or a tab-width of spaces
					.*\n+
				  )+
				)
				((?=^[ ]{0,'.$this->tab_width.'}\S)|\Z)	# Lookahead for non-space at line-start, or end of doc
			}xm',
			array(&$this, '_doCodeBlocks_callback'), $text);

		return $text;
	}
	function _doCodeBlocks_callback($matches) {
		$codeblock = $matches[1];

		$codeblock = $this->encodeCode($this->outdent($codeblock));
	//	$codeblock = $this->detab($codeblock);
		# trim leading newlines and trailing whitespace
		$codeblock = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $codeblock);

		$result = "\n\n".$this->hashBlock("<pre><code>" . $codeblock . "\n</code></pre>")."\n\n";

		return $result;
	}


	function doCodeSpans($text) {
	#
	# 	*	Backtick quotes are used for <code></code> spans.
	#
	# 	*	You can use multiple backticks as the delimiters if you want to
	# 		include literal backticks in the code span. So, this input:
	#
	#		  Just type ``foo `bar` baz`` at the prompt.
	#
	#	  	Will translate to:
	#
	#		  <p>Just type <code>foo `bar` baz</code> at the prompt.</p>
	#
	#		There's no arbitrary limit to the number of backticks you
	#		can use as delimters. If you need three consecutive backticks
	#		in your code, use four for delimiters, etc.
	#
	#	*	You can use spaces to get literal backticks at the edges:
	#
	#		  ... type `` `bar` `` ...
	#
	#	  	Turns to:
	#
	#		  ... type <code>`bar`</code> ...
	#
		$text = preg_replace_callback('@
				(?<!\\\)	# Character before opening ` can\'t be a backslash
				(`+)		# $1 = Opening run of `
				(.+?)		# $2 = The code block
				(?<!`)
				\1			# Matching closer
				(?!`)
			@xs',
			array(&$this, '_doCodeSpans_callback'), $text);

		return $text;
	}
	function _doCodeSpans_callback($matches) {
		$c = $matches[2];
		$c = preg_replace('/^[ \t]*/', '', $c); # leading whitespace
		$c = preg_replace('/[ \t]*$/', '', $c); # trailing whitespace
		$c = $this->encodeCode($c);
		return $this->hashSpan("<code>$c</code>");
	}


	function encodeCode($_) {
	#
	# Encode/escape certain characters inside Markdown code runs.
	# The point is that in code, these characters are literals,
	# and lose their special Markdown meanings.
	#
		# Encode all ampersands; HTML entities are not
		# entities within a Markdown code span.
		$_ = str_replace('&', '&amp;', $_);

		# Do the angle bracket song and dance:
		$_ = str_replace(array('<',    '>'), 
						 array('&lt;', '&gt;'), $_);

		# Now, escape characters that are magic in Markdown:
//		$_ = str_replace(array_keys($this->escape_table), 
//						 array_values($this->escape_table), $_);

		return $_;
	}


	function doItalicsAndBold($text) {
		# <strong> must go first:
		$text = preg_replace_callback('{
				(						# $1: Marker
					(?<!\*\*) \* |		#     (not preceded by two chars of
					(?<!__)   _			#      the same marker)
				)
				\1
				(?=\S) 					# Not followed by whitespace 
				(?!\1\1)				#   or two others marker chars.
				(						# $2: Content
					(?:
						[^*_]+?			# Anthing not em markers.
					|
										# Balence any regular emphasis inside.
						\1 (?=\S) .+? (?<=\S) \1
					|
						(?! \1 ) .		# Allow unbalenced * and _.
					)+?
				)
				(?<=\S) \1\1			# End mark not preceded by whitespace.
			}sx',
			array(&$this, '_doItalicAndBold_strong_callback'), $text);
		# Then <em>:
		$text = preg_replace_callback(
			'{ ( (?<!\*)\* | (?<!_)_ ) (?=\S) (?! \1) (.+?) (?<=\S) \1 }sx',
			array(&$this, '_doItalicAndBold_em_callback'), $text);

		return $text;
	}
	function _doItalicAndBold_em_callback($matches) {
		$text = $matches[2];
		$text = $this->runSpanGamut($text);
		return $this->hashSpan("<em>$text</em>");
	}
	function _doItalicAndBold_strong_callback($matches) {
		$text = $matches[2];
		$text = $this->runSpanGamut($text);
		return $this->hashSpan("<strong>$text</strong>");
	}


	function doBlockQuotes($text) {
		$text = preg_replace_callback('/
			  (								# Wrap whole match in $1
				(
				  ^[ \t]*>[ \t]?			# ">" at the start of a line
					.+\n					# rest of the first line
				  (.+\n)*					# subsequent consecutive lines
				  \n*						# blanks
				)+
			  )
			/xm',
			array(&$this, '_doBlockQuotes_callback'), $text);

		return $text;
	}
	function _doBlockQuotes_callback($matches) {
		$bq = $matches[1];
		# trim one level of quoting - trim whitespace-only lines
		$bq = preg_replace(array('/^[ \t]*>[ \t]?/m', '/^[ \t]+$/m'), '', $bq);
		$bq = $this->runBlockGamut($bq);		# recurse

		$bq = preg_replace('/^/m', "  ", $bq);
		# These leading spaces cause problem with <pre> content, 
		# so we need to fix that:
		$bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx', 
			array(&$this, '_DoBlockQuotes_callback2'), $bq);

		return "\n". $this->hashBlock("<blockquote>\n$bq\n</blockquote>")."\n\n";
	}
	function _doBlockQuotes_callback2($matches) {
		$pre = $matches[1];
		$pre = preg_replace('/^  /m', '', $pre);
		return $pre;
	}


	function formParagraphs($text) {
	#
	#	Params:
	#		$text - string to process with html <p> tags
	#
		# Strip leading and trailing lines:
		$text = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $text);

		$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

		#
		# Wrap <p> tags.
		#
		foreach ($grafs as $key => $value) {
			if (!isset( $this->html_blocks[$value] )) {
				$value = $this->runSpanGamut($value);
				$value = preg_replace('/^([ \t]*)/', "<p>", $value);
				$value .= "</p>";
				$grafs[$key] = $this->unhash($value);
			}
		}

		#
		# Unhashify HTML blocks
		#
		foreach ($grafs as $key => $graf) {
			# Modify elements of @grafs in-place...
			if (isset($this->html_blocks[$graf])) {
				$block = $this->html_blocks[$graf];
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
//					# We can't call Markdown(), because that resets the hash;
//					# that initialization code should be pulled into its own sub, though.
//					$div_content = $this->hashHTMLBlocks($div_content);
//					
//					# Run document gamut methods on the content.
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


	function encodeAmpsAndAngles($text) {
	# Smart processing for ampersands and angle brackets that need to be encoded.

		# Ampersand-encoding based entirely on Nat Irons's Amputator MT plugin:
		#   http://bumppo.net/projects/amputator/
		$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/', 
							 '&amp;', $text);;

		# Encode naked <'s
		$text = preg_replace('{<(?![a-z/?\$!%])}i', '&lt;', $text);

		return $text;
	}


	function encodeBackslashEscapes($text) {
	#
	#	Parameter:  String.
	#	Returns:    The string, with after processing the following backslash
	#				escape sequences.
	#
		# Must process escaped backslashes first.
		return str_replace(array_keys($this->backslash_escape_table),
						   array_values($this->backslash_escape_table), $text);
	}


	function doAutoLinks($text) {
		$text = preg_replace_callback('{<((https?|ftp|dict):[^\'">\s]+)>}', 
			array(&$this, '_doAutoLinks_url_callback'), $text);

		# Email addresses: <address@domain.foo>
		$text = preg_replace_callback('{
			<
			(?:mailto:)?
			(
				[-.\w\x80-\xFF]+
				\@
				[-a-z0-9\x80-\xFF]+(\.[-a-z0-9\x80-\xFF]+)*\.[a-z]+
			)
			>
			}xi',
			array(&$this, '_doAutoLinks_email_callback'), $text);

		return $text;
	}
	function _doAutoLinks_url_callback($matches) {
		$url = $this->encodeAmpsAndAngles($matches[1]);
		$link = "<a href=\"$url\">$url</a>";
		return $this->hashSpan($link);
	}
	function _doAutoLinks_email_callback($matches) {
		$address = $matches[1];
		$address = $this->unescapeSpecialChars($address);
		$link = $this->encodeEmailAddress($address);
		return $this->hashSpan($link);
	}


	function encodeEmailAddress($addr) {
	#
	#	Input: an email address, e.g. "foo@example.com"
	#
	#	Output: the email address as a mailto link, with each character
	#		of the address encoded as either a decimal or hex entity, in
	#		the hopes of foiling most address harvesting spam bots. E.g.:
	#
	#	  <p><a href="&#109;&#x61;&#105;&#x6c;&#116;&#x6f;&#58;&#x66;o&#111;
	#        &#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;&#101;&#46;&#x63;&#111;
	#        &#x6d;">&#x66;o&#111;&#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;
	#        &#101;&#46;&#x63;&#111;&#x6d;</a></p>
	#
	#	Based by a filter by Matthew Wickline, posted to BBEdit-Talk.
	#   With some optimizations by Milian Wolff.
	#
		$addr = "mailto:" . $addr;
		$chars = preg_split('/(?<!^)(?!$)/', $addr);
		$seed = (int)abs(crc32($addr) / strlen($addr)); # Deterministic seed.
		
		foreach ($chars as $key => $char) {
			$ord = ord($char);
			# Ignore non-ascii chars.
			if ($ord < 128) {
				$r = ($seed * (1 + $key)) % 100; # Pseudo-random function.
				# roughly 10% raw, 45% hex, 45% dec
				# '@' *must* be encoded. I insist.
				if ($r > 90 && $char != '@') /* do nothing */;
				else if ($r < 45) $chars[$key] = '&#x'.dechex($ord).';';
				else              $chars[$key] = '&#'.$ord.';';
			}
		}
		
		$addr = implode('', $chars);
		$text = implode('', array_slice($chars, 7)); # text without `mailto:`
		$addr = "<a href=\"$addr\">$text</a>";

		return $addr;
	}


	function unescapeSpecialChars($text) {
	#
	# Swap back in all the special characters we've hidden.
	#
		return str_replace(array_values($this->escape_table), 
						   array_keys($this->escape_table), $text);
	}


	function tokenizeHTML($str) {
	#
	#   Parameter:  String containing HTML + Markdown markup.
	#   Returns:    An array of the tokens comprising the input
	#               string. Each token is either a tag or a run of text 
	#               between tags. Each element of the array is a
	#               two-element array; the first is either 'tag' or 'text';
	#               the second is the actual value.
	#   Note:       Markdown code spans are taken into account: no tag token is 
	#               generated within a code span.
	#
		$tokens = array();

		while ($str != "") {
			#
			# Each loop iteration seach for either the next tag or the next 
			# openning code span marker. If a code span marker is found, the 
			# code span is extracted in entierty and will result in an extra
			# text token.
			#
			$parts = preg_split('{
				(
					(?<![`\\\\])
					`+						# code span marker
				|
					<!--    .*?     -->		# comment
				|
					<\?.*?\?> | <%.*?%>		# processing instruction
				|
					<[/!$]?[-a-zA-Z0-9:]+	# regular tags
					(?:
						\s
						(?>[^"\'>]+|"[^"]*"|\'[^\']*\')*
					)?
					>
				)
				}xs', $str, 2, PREG_SPLIT_DELIM_CAPTURE);
			
			# Create token from text preceding tag.
			if ($parts[0] != "") {
				$tokens[] = array('text', $parts[0]);
			}
			
			# Check if we reach the end.
			if (count($parts) < 3) {
				break;
			}
			
			# Create token from tag or code span.
			if ($parts[1]{0} == "`") {
				$tokens[] = array('text', $parts[1]);
				$str = $parts[2];
				
				# Skip the whole code span, pass as text token.
				if (preg_match('/^(.*(?<!`\\\\)'.$parts[1].'(?!`))(.*)$/sm', 
					$str, $matches))
				{
					$tokens[] = array('text', $matches[1]);
					$str = $matches[2];
				}
			} else {
				$tokens[] = array('tag', $parts[1]);
				$str = $parts[2];
			}
		}
		
		return $tokens;
	}


	function outdent($text) {
	#
	# Remove one level of line-leading tabs or spaces
	#
		return preg_replace("/^(\\t|[ ]{1,$this->tab_width})/m", "", $text);
	}


	# String length function for detab. `_initDetab` will create a function to 
	# hanlde UTF-8 if the default function does not exist.
	var $utf8_strlen = 'mb_strlen';
	
	function detab($text) {
	#
	# Replace tabs with the appropriate amount of space.
	#
		# For each line we separate the line in blocks delemited by
		# tab characters. Then we reconstruct every line by adding the 
		# appropriate number of space between each blocks.
		
		$strlen = $this->utf8_strlen; # best strlen function for UTF-8.
		$lines = explode("\n", $text);
		$text = "";
		
		foreach ($lines as $line) {
			# Split in blocks.
			$blocks = explode("\t", $line);
			# Add each blocks to the line.
			$line = $blocks[0];
			unset($blocks[0]); # Do not add first block twice.
			foreach ($blocks as $block) {
				# Calculate amount of space, insert spaces, insert block.
				$amount = $this->tab_width - 
					$strlen($line, 'UTF-8') % $this->tab_width;
				$line .= str_repeat(" ", $amount) . $block;
			}
			$text .= "$line\n";
		}
		return $text;
	}
	function _initDetab() {
	#
	# Check for the availability of the function in the `utf8_strlen` property
	# (probably `mb_strlen`). If the function is not available, create a 
	# function that will loosely count the number of UTF-8 characters with a
	# regular expression.
	#
		if (function_exists($this->utf8_strlen)) return;
		$this->utf8_strlen = 'Markdown_UTF8_strlen';
		
		if (function_exists($this->utf8_strlen)) return;
		function Markdown_UTF8_strlen($text) {
			return preg_match_all('/[\x00-\xBF]|[\xC0-\xFF][\x80-\xBF]*/', 
				$text, $m);
		}
	}


	function unhash($text) {
	#
	# Swap back in all the tags hashed by _HashHTMLBlocks.
	#
		return str_replace(array_keys($this->html_hashes), 
						   array_values($this->html_hashes), $text);
	}

}


#
# Markdown Extra Parser Class
#

class MarkdownExtra_Parser extends Markdown_Parser {

	# Prefix for footnote ids.
	var $fn_id_prefix = "";
	
	# Optional title attribute for footnote links and backlinks.
	var $fn_link_title = MARKDOWN_FN_LINK_TITLE;
	var $fn_backlink_title = MARKDOWN_FN_BACKLINK_TITLE;
	
	# Optional class attribute for footnote links and backlinks.
	var $fn_link_class = MARKDOWN_FN_LINK_CLASS;
	var $fn_backlink_class = MARKDOWN_FN_BACKLINK_CLASS;


	function MarkdownExtra_Parser() {
	#
	# Constructor function. Initialize the parser object.
	#
		# Add extra escapable characters before parent constructor 
		# initialize the table.
		$this->escape_chars .= ':|';
		
		# Insert extra document, block, and span transformations. 
		# Parent constructor will do the sorting.
		$this->document_gamut += array(
			"stripFootnotes"     => 15,
			"stripAbbreviations" => 25,
			"appendFootnotes"    => 50,
			);
		$this->block_gamut += array(
			"doTables"           => 15,
			"doDefLists"         => 45,
			);
		$this->span_gamut += array(
			"doFootnotes"        =>  4,
			"doAbbreviations"    =>  5,
			);
		
		parent::Markdown_Parser();
	}
	
	
	# Extra hashes used during extra transformations.
	var $footnotes = array();
	var $footnotes_ordered = array();
	var $abbr_desciptions = array();
	var $abbr_matches = array();
	var $html_cleans = array();
	
	
	function transform($text) {
	#
	# Added clear to the new $html_hashes, reordered `hashHTMLBlocks` before 
	# blank line stripping and added extra parameter to `runBlockGamut`.
	#
		# Clear the global hashes. If we don't clear these, you get conflicts
		# from other articles when generating a page which contains more than
		# one article (e.g. an index page that shows the N most recent
		# articles):
		$this->footnotes = array();
		$this->footnotes_ordered = array();
		$this->abbr_desciptions = array();
		$this->abbr_matches = array();
		$this->html_cleans = array();

		return parent::transform($text);
	}
	
	
	### HTML Block Parser ###
	
	# Tags that are always treated as block tags:
	var $block_tags = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|form|fieldset|iframe|hr|legend';
	
	# Tags treated as block tags only if the opening tag is alone on it's line:
	var $context_block_tags = 'script|noscript|math|ins|del';
	
	# Tags where markdown="1" default to span mode:
	var $contain_span_tags = 'p|h[1-6]|li|dd|dt|td|th|legend|address';
	
	# Tags which must not have their contents modified, no matter where 
	# they appear:
	var $clean_tags = 'script|math';
	
	# Tags that do not need to be closed.
	var $auto_close_tags = 'hr|img';
	

	function hashHTMLBlocks($text) {
	#
	# Hashify HTML Blocks and "clean tags".
	#
	# We only want to do this for block-level HTML tags, such as headers,
	# lists, and tables. That's because we still want to wrap <p>s around
	# "paragraphs" that are wrapped in non-block-level tags, such as anchors,
	# phrase emphasis, and spans. The list of tags we're looking for is
	# hard-coded.
	#
	# This works by calling _HashHTMLBlocks_InMarkdown, which then calls
	# _HashHTMLBlocks_InHTML when it encounter block tags. When the markdown="1" 
	# attribute is found whitin a tag, _HashHTMLBlocks_InHTML calls back
	#  _HashHTMLBlocks_InMarkdown to handle the Markdown syntax within the tag.
	# These two functions are calling each other. It's recursive!
	#
		#
		# Call the HTML-in-Markdown hasher.
		#
		list($text, ) = $this->_hashHTMLBlocks_inMarkdown($text);
		
		return $text;
	}
	function _hashHTMLBlocks_inMarkdown($text, $indent = 0, 
										$enclosing_tag = '', $span = false)
	{
	#
	# Parse markdown text, calling _HashHTMLBlocks_InHTML for block tags.
	#
	# *   $indent is the number of space to be ignored when checking for code 
	#     blocks. This is important because if we don't take the indent into 
	#     account, something like this (which looks right) won't work as expected:
	#
	#     <div>
	#         <div markdown="1">
	#         Hello World.  <-- Is this a Markdown code block or text?
	#         </div>  <-- Is this a Markdown code block or a real tag?
	#     <div>
	#
	#     If you don't like this, just don't indent the tag on which
	#     you apply the markdown="1" attribute.
	#
	# *   If $enclosing_tag is not empty, stops at the first unmatched closing 
	#     tag with that name. Nested tags supported.
	#
	# *   If $span is true, text inside must treated as span. So any double 
	#     newline will be replaced by a single newline so that it does not create 
	#     paragraphs.
	#
	# Returns an array of that form: ( processed text , remaining text )
	#
		if ($text === '') return array('', '');

		# Regex to check for the presense of newlines around a block tag.
		$newline_match_before = '/(?:^\n?|\n\n)*$/';
		$newline_match_after = 
			'{
				^						# Start of text following the tag.
				(?:[ ]*<!--.*?-->)?		# Optional comment.
				[ ]*\n					# Must be followed by newline.
			}xs';
		
		# Regex to match any tag.
		$block_tag_match =
			'{
				(					# $2: Capture hole tag.
					</?					# Any opening or closing tag.
						(?:				# Tag name.
							'.$this->block_tags.'			|
							'.$this->context_block_tags.'	|
							'.$this->clean_tags.'        	|
							(?!\s)'.$enclosing_tag.'
						)
						\s*				# Whitespace.
						(?:
							".*?"		|	# Double quotes (can contain `>`)
							\'.*?\'   	|	# Single quotes (can contain `>`)
							.+?				# Anything but quotes and `>`.
						)*?
					>					# End of tag.
				|
					<!--    .*?     -->	# HTML Comment
				|
					<\?.*?\?> | <%.*?%>	# Processing instruction
				|
					<!\[CDATA\[.*?\]\]>	# CData Block
				)
			}xs';

		
		$depth = 0;		# Current depth inside the tag tree.
		$parsed = "";	# Parsed text that will be returned.

		#
		# Loop through every tag until we find the closing tag of the parent
		# or loop until reaching the end of text if no parent tag specified.
		#
		do {
			#
			# Split the text using the first $tag_match pattern found.
			# Text before  pattern will be first in the array, text after
			# pattern will be at the end, and between will be any catches made 
			# by the pattern.
			#
			$parts = preg_split($block_tag_match, $text, 2, 
								PREG_SPLIT_DELIM_CAPTURE);
			
			# If in Markdown span mode, add a empty-string span-level hash 
			# after each newline to prevent triggering any block element.
			if ($span) {
				$newline = $this->hashSpan("") . "\n";
				$parts[0] = str_replace("\n", $newline, $parts[0]);
			}
			
			$parsed .= $parts[0]; # Text before current tag.
			
			# If end of $text has been reached. Stop loop.
			if (count($parts) < 3) {
				$text = "";
				break;
			}
			
			$tag  = $parts[1]; # Tag to handle.
			$text = $parts[2]; # Remaining text after current tag.
			
			#
			# Check for: Tag inside code block or span
			#
			if (# Find current paragraph
				preg_match('/(?>^\n?|\n\n)((?>.\n?)+?)$/', $parsed, $matches) &&
				(
				# Then match in it either a code block...
				preg_match('/^ {'.($indent+4).'}.*(?>\n {'.($indent+4).'}.*)*'.
							'(?!\n)$/', $matches[1], $x) ||
				# ...or unbalenced code span markers. (the regex matches balenced)
				!preg_match('/^(?>[^`]+|(`+)(?>[^`]+|(?!\1[^`])`)*?\1(?!`))*$/s',
							 $matches[1])
				))
			{
				# Tag is in code block or span and may not be a tag at all. So we
				# simply skip the first char (should be a `<`).
				$parsed .= $tag{0};
				$text = substr($tag, 1) . $text; # Put back $tag minus first char.
			}
			#
			# Check for: Opening Block level tag or
			#            Opening Content Block tag (like ins and del) 
			#               used as a block tag (tag is alone on it's line).
			#
			else if (preg_match("{^<(?:$this->block_tags)\b}", $tag) ||
				(	preg_match("{^<(?:$this->context_block_tags)\b}", $tag) &&
					preg_match($newline_match_before, $parsed) &&
					preg_match($newline_match_after, $text)	)
				)
			{
				# Need to parse tag and following text using the HTML parser.
				list($block_text, $text) = 
					$this->_hashHTMLBlocks_inHTML($tag . $text, "hashBlock", true);
				
				# Make sure it stays outside of any paragraph by adding newlines.
				$parsed .= "\n\n$block_text\n\n";
			}
			#
			# Check for: Clean tag (like script, math)
			#            HTML Comments, processing instructions.
			#
			else if (preg_match("{^<(?:$this->clean_tags)\b}", $tag) ||
				$tag{1} == '!' || $tag{1} == '?')
			{
				# Need to parse tag and following text using the HTML parser.
				# (don't check for markdown attribute)
				list($block_text, $text) = 
					$this->_hashHTMLBlocks_inHTML($tag . $text, "hashClean", false);
				
				$parsed .= $block_text;
			}
			#
			# Check for: Tag with same name as enclosing tag.
			#
			else if ($enclosing_tag !== '' &&
				# Same name as enclosing tag.
				preg_match("{^</?(?:$enclosing_tag)\b}", $tag))
			{
				#
				# Increase/decrease nested tag count.
				#
				if ($tag{1} == '/')						$depth--;
				else if ($tag{strlen($tag)-2} != '/')	$depth++;

				if ($depth < 0) {
					#
					# Going out of parent element. Clean up and break so we
					# return to the calling function.
					#
					$text = $tag . $text;
					break;
				}
				
				$parsed .= $tag;
			}
			else {
				$parsed .= $tag;
			}
		} while ($depth >= 0);
		
		return array($parsed, $text);
	}
	function _hashHTMLBlocks_inHTML($text, $hash_method, $md_attr) {
	#
	# Parse HTML, calling _HashHTMLBlocks_InMarkdown for block tags.
	#
	# *   Calls $hash_method to convert any blocks.
	# *   Stops when the first opening tag closes.
	# *   $md_attr indicate if the use of the `markdown="1"` attribute is allowed.
	#     (it is not inside clean tags)
	#
	# Returns an array of that form: ( processed text , remaining text )
	#
		if ($text === '') return array('', '');
		
		# Regex to match `markdown` attribute inside of a tag.
		$markdown_attr_match = '
			{
				\s*			# Eat whitespace before the `markdown` attribute
				markdown
				\s*=\s*
				(["\'])		# $1: quote delimiter		
				(.*?)		# $2: attribute value
				\1			# matching delimiter	
			}xs';
		
		# Regex to match any tag.
		$tag_match = '{
				(					# $2: Capture hole tag.
					</?					# Any opening or closing tag.
						[\w:$]+			# Tag name.
						\s*				# Whitespace.
						(?:
							".*?"		|	# Double quotes (can contain `>`)
							\'.*?\'   	|	# Single quotes (can contain `>`)
							.+?				# Anything but quotes and `>`.
						)*?
					>					# End of tag.
				|
					<!--    .*?     -->	# HTML Comment
				|
					<\?.*?\?> | <%.*?%>	# Processing instruction
				|
					<!\[CDATA\[.*?\]\]>	# CData Block
				)
			}xs';
		
		$original_text = $text;		# Save original text in case of faliure.
		
		$depth		= 0;	# Current depth inside the tag tree.
		$block_text	= "";	# Temporary text holder for current text.
		$parsed		= "";	# Parsed text that will be returned.

		#
		# Get the name of the starting tag.
		#
		if (preg_match("/^<([\w:$]*)\b/", $text, $matches))
			$base_tag_name = $matches[1];

		#
		# Loop through every tag until we find the corresponding closing tag.
		#
		do {
			#
			# Split the text using the first $tag_match pattern found.
			# Text before  pattern will be first in the array, text after
			# pattern will be at the end, and between will be any catches made 
			# by the pattern.
			#
			$parts = preg_split($tag_match, $text, 2, PREG_SPLIT_DELIM_CAPTURE);
			
			if (count($parts) < 3) {
				#
				# End of $text reached with unbalenced tag(s).
				# In that case, we return original text unchanged and pass the
				# first character as filtered to prevent an infinite loop in the 
				# parent function.
				#
				return array($original_text{0}, substr($original_text, 1));
			}
			
			$block_text .= $parts[0]; # Text before current tag.
			$tag         = $parts[1]; # Tag to handle.
			$text        = $parts[2]; # Remaining text after current tag.
			
			#
			# Check for: Auto-close tag (like <hr/>)
			#			 Comments and Processing Instructions.
			#
			if (preg_match("{^</?(?:$this->auto_close_tags)\b}", $tag) ||
				$tag{1} == '!' || $tag{1} == '?')
			{
				# Just add the tag to the block as if it was text.
				$block_text .= $tag;
			}
			else {
				#
				# Increase/decrease nested tag count. Only do so if
				# the tag's name match base tag's.
				#
				if (preg_match("{^</?$base_tag_name\b}", $tag)) {
					if ($tag{1} == '/')						$depth--;
					else if ($tag{strlen($tag)-2} != '/')	$depth++;
				}
				
				#
				# Check for `markdown="1"` attribute and handle it.
				#
				if ($md_attr && 
					preg_match($markdown_attr_match, $tag, $attr_matches) &&
					preg_match('/^1|block|span$/', $attr_matches[2]))
				{
					# Remove `markdown` attribute from opening tag.
					$tag = preg_replace($markdown_attr_match, '', $tag);
					
					# Check if text inside this tag must be parsed in span mode.
					$this->mode = $attr_matches[2];
					$span_mode = $this->mode == 'span' || $this->mode != 'block' &&
						preg_match("{^<(?:$this->contain_span_tags)\b}", $tag);
					
					# Calculate indent before tag.
					preg_match('/(?:^|\n)( *?)(?! ).*?$/', $block_text, $matches);
					$indent = strlen($matches[1]);
					
					# End preceding block with this tag.
					$block_text .= $tag;
					$parsed .= $this->$hash_method($block_text);
					
					# Get enclosing tag name for the ParseMarkdown function.
					preg_match('/^<([\w:$]*)\b/', $tag, $matches);
					$tag_name = $matches[1];
					
					# Parse the content using the HTML-in-Markdown parser.
					list ($block_text, $text)
						= $this->_hashHTMLBlocks_inMarkdown($text, $indent, 
														$tag_name, $span_mode);
					
					# Outdent markdown text.
					if ($indent > 0) {
						$block_text = preg_replace("/^[ ]{1,$indent}/m", "", 
													$block_text);
					}
					
					# Append tag content to parsed text.
					if (!$span_mode)	$parsed .= "\n\n$block_text\n\n";
					else				$parsed .= "$block_text";
					
					# Start over a new block.
					$block_text = "";
				}
				else $block_text .= $tag;
			}
			
		} while ($depth > 0);
		
		#
		# Hash last block text that wasn't processed inside the loop.
		#
		$parsed .= $this->$hash_method($block_text);
		
		return array($parsed, $text);
	}


	function hashClean($text) {
	#
	# Called whenever a tag must be hashed when a function insert a "clean" tag
	# in $text, it pass through this function and is automaticaly escaped, 
	# blocking invalid nested overlap.
	#
		# Swap back any tag hash found in $text so we do not have to `unhash`
		# multiple times at the end.
		$text = $this->unhash($text);
		
		# Then hash the tag.
		$key = md5($text);
		$this->html_cleans[$key] = $text;
		$this->html_hashes[$key] = $text;
		return $key; # String that will replace the clean tag.
	}


	function doHeaders($text) {
	#
	# Redefined to add id attribute support.
	#
		# Setext-style headers:
		#	  Header 1  {#header1}
		#	  ========
		#  
		#	  Header 2  {#header2}
		#	  --------
		#
		$text = preg_replace_callback(
			'{ (^.+?) (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})? [ \t]*\n=+[ \t]*\n+ }mx',
			array(&$this, '_doHeaders_callback_setext_h1'), $text);
		$text = preg_replace_callback(
			'{ (^.+?) (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})? [ \t]*\n-+[ \t]*\n+ }mx',
			array(&$this, '_doHeaders_callback_setext_h2'), $text);

		# atx-style headers:
		#	# Header 1        {#header1}
		#	## Header 2       {#header2}
		#	## Header 2 with closing hashes ##  {#header3}
		#	...
		#	###### Header 6   {#header2}
		#
		$text = preg_replace_callback('{
				^(\#{1,6})	# $1 = string of #\'s
				[ \t]*
				(.+?)		# $2 = Header text
				[ \t]*
				\#*			# optional closing #\'s (not counted)
				(?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})? # id attribute
				[ \t]*
				\n+
			}xm',
			array(&$this, '_doHeaders_callback_atx'), $text);

		return $text;
	}
	function _doHeaders_attr($attr) {
		if (empty($attr))  return "";
		return " id=\"$attr\"";
	}
	function _doHeaders_callback_setext_h1($matches) {
		$attr  = $this->_doHeaders_attr($id =& $matches[2]);
		$block = "<h1$attr>".$this->runSpanGamut($matches[1])."</h1>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}
	function _doHeaders_callback_setext_h2($matches) {
		$attr  = $this->_doHeaders_attr($id =& $matches[2]);
		$block = "<h2$attr>".$this->runSpanGamut($matches[1])."</h2>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}
	function _doHeaders_callback_atx($matches) {
		$level = strlen($matches[1]);
		$attr  = $this->_doHeaders_attr($id =& $matches[3]);
		$block = "<h$level$attr>".$this->runSpanGamut($matches[2])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}


	function doTables($text) {
	#
	# Form HTML tables.
	#
		$less_than_tab = $this->tab_width - 1;
		#
		# Find tables with leading pipe.
		#
		#	| Header 1 | Header 2
		#	| -------- | --------
		#	| Cell 1   | Cell 2
		#	| Cell 3   | Cell 4
		#
		$text = preg_replace_callback('
			{
				^							# Start of a line
				[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
				[|]							# Optional leading pipe (present)
				(.+) \n						# $1: Header row (at least one pipe)
				
				[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
				[|] ([ ]*[-:]+[-| :]*) \n	# $2: Header underline
				
				(							# $3: Cells
					(?:
						[ ]*				# Allowed whitespace.
						[|] .* \n			# Row content.
					)*
				)
				(?=\n|\Z)					# Stop at final double newline.
			}xm',
			array(&$this, '_doTable_leadingPipe_callback'), $text);
		
		#
		# Find tables without leading pipe.
		#
		#	Header 1 | Header 2
		#	-------- | --------
		#	Cell 1   | Cell 2
		#	Cell 3   | Cell 4
		#
		$text = preg_replace_callback('
			{
				^							# Start of a line
				[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
				(\S.*[|].*) \n				# $1: Header row (at least one pipe)
				
				[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
				([-:]+[ ]*[|][-| :]*) \n	# $2: Header underline
				
				(							# $3: Cells
					(?:
						.* [|] .* \n		# Row content
					)*
				)
				(?=\n|\Z)					# Stop at final double newline.
			}xm',
			array(&$this, '_DoTable_callback'), $text);

		return $text;
	}
	function _doTable_leadingPipe_callback($matches) {
		$head		= $matches[1];
		$underline	= $matches[2];
		$content	= $matches[3];
		
		# Remove leading pipe for each row.
		$content	= preg_replace('/^ *[|]/m', '', $content);
		
		return $this->_doTable_callback(array($matches[0], $head, $underline, $content));
	}
	function _doTable_callback($matches) {
		$head		= $matches[1];
		$underline	= $matches[2];
		$content	= $matches[3];

		# Remove any tailing pipes for each line.
		$head		= preg_replace('/[|] *$/m', '', $head);
		$underline	= preg_replace('/[|] *$/m', '', $underline);
		$content	= preg_replace('/[|] *$/m', '', $content);
		
		# Reading alignement from header underline.
		$separators	= preg_split('/ *[|] */', $underline);
		foreach ($separators as $n => $s) {
			if (preg_match('/^ *-+: *$/', $s))		$attr[$n] = ' align="right"';
			else if (preg_match('/^ *:-+: *$/', $s))$attr[$n] = ' align="center"';
			else if (preg_match('/^ *:-+ *$/', $s))	$attr[$n] = ' align="left"';
			else									$attr[$n] = '';
		}
		
		# Creating code spans before splitting the row is an easy way to 
		# handle a code span containg pipes.
		$head	= $this->doCodeSpans($head);
		$headers	= preg_split('/ *[|] */', $head);
		$col_count	= count($headers);
		
		# Write column headers.
		$text = "<table>\n";
		$text .= "<thead>\n";
		$text .= "<tr>\n";
		foreach ($headers as $n => $header)
			$text .= "  <th$attr[$n]>".$this->runSpanGamut(trim($header))."</th>\n";
		$text .= "</tr>\n";
		$text .= "</thead>\n";
		
		# Split content by row.
		$rows = explode("\n", trim($content, "\n"));
		
		$text .= "<tbody>\n";
		foreach ($rows as $row) {
			# Creating code spans before splitting the row is an easy way to 
			# handle a code span containg pipes.
			$row = $this->doCodeSpans($row);
			
			# Split row by cell.
			$row_cells = preg_split('/ *[|] */', $row, $col_count);
			$row_cells = array_pad($row_cells, $col_count, '');
			
			$text .= "<tr>\n";
			foreach ($row_cells as $n => $cell)
				$text .= "  <td$attr[$n]>".$this->runSpanGamut(trim($cell))."</td>\n";
			$text .= "</tr>\n";
		}
		$text .= "</tbody>\n";
		$text .= "</table>";
		
		return $this->hashBlock($text) . "\n";
	}

	
	function doDefLists($text) {
	#
	# Form HTML definition lists.
	#
		$less_than_tab = $this->tab_width - 1;

		# Re-usable pattern to match any entire dl list:
		$whole_list = '
			(								# $1 = whole list
			  (								# $2
				[ ]{0,'.$less_than_tab.'}
				((?>.*\S.*\n)+)				# $3 = defined term
				\n?
				[ ]{0,'.$less_than_tab.'}:[ ]+ # colon starting definition
			  )
			  (?s:.+?)
			  (								# $4
				  \z
				|
				  \n{2,}
				  (?=\S)
				  (?!						# Negative lookahead for another term
					[ ]{0,'.$less_than_tab.'}
					(?: \S.*\n )+?			# defined term
					\n?
					[ ]{0,'.$less_than_tab.'}:[ ]+ # colon starting definition
				  )
				  (?!						# Negative lookahead for another definition
					[ ]{0,'.$less_than_tab.'}:[ ]+ # colon starting definition
				  )
			  )
			)
		'; // mx

		$text = preg_replace_callback('{
				(?:(?<=\n\n)|\A\n?)
				'.$whole_list.'
			}mx',
			array(&$this, '_doDefLists_callback'), $text);

		return $text;
	}
	function _doDefLists_callback($matches) {
		# Re-usable patterns to match list item bullets and number markers:
		$list = $matches[1];
		
		# Turn double returns into triple returns, so that we can make a
		# paragraph for the last item in a list, if necessary:
		$result = trim($this->processDefListItems($list));
		$result = "<dl>\n" . $result . "\n</dl>";
		return $this->hashBlock($result) . "\n\n";
	}


	function processDefListItems($list_str) {
	#
	#	Process the contents of a single definition list, splitting it
	#	into individual term and definition list items.
	#
		$less_than_tab = $this->tab_width - 1;
		
		# trim trailing blank lines:
		$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

		# Process definition terms.
		$list_str = preg_replace_callback('{
			(?:\n\n+|\A\n?)					# leading line
			(								# definition terms = $1
				[ ]{0,'.$less_than_tab.'}	# leading whitespace
				(?![:][ ]|[ ])				# negative lookahead for a definition 
											#   mark (colon) or more whitespace.
				(?: \S.* \n)+?				# actual term (not whitespace).	
			)			
			(?=\n?[ ]{0,3}:[ ])				# lookahead for following line feed 
											#   with a definition mark.
			}xm',
			array(&$this, '_processDefListItems_callback_dt'), $list_str);

		# Process actual definitions.
		$list_str = preg_replace_callback('{
			\n(\n+)?						# leading line = $1
			[ ]{0,'.$less_than_tab.'}		# whitespace before colon
			[:][ ]+							# definition mark (colon)
			((?s:.+?))						# definition text = $2
			(?= \n+ 						# stop at next definition mark,
				(?:							# next term or end of text
					[ ]{0,'.$less_than_tab.'} [:][ ]	|
					<dt> | \z
				)						
			)					
			}xm',
			array(&$this, '_processDefListItems_callback_dd'), $list_str);

		return $list_str;
	}
	function _processDefListItems_callback_dt($matches) {
		$terms = explode("\n", trim($matches[1]));
		$text = '';
		foreach ($terms as $term) {
			$term = $this->runSpanGamut(trim($term));
			$text .= "\n<dt>" . $term . "</dt>";
		}
		return $text . "\n";
	}
	function _processDefListItems_callback_dd($matches) {
		$leading_line	= $matches[1];
		$def			= $matches[2];

		if ($leading_line || preg_match('/\n{2,}/', $def)) {
			$def = $this->runBlockGamut($this->outdent($def . "\n\n"));
			$def = "\n". $def ."\n";
		}
		else {
			$def = rtrim($def);
			$def = $this->runSpanGamut($this->outdent($def));
		}

		return "\n<dd>" . $def . "</dd>\n";
	}


	function doItalicsAndBold($text) {
	#
	# Redefined to change emphasis by underscore behaviour so that it does not 
	# work in the middle of a word.
	#
		# <strong> must go first:
		$text = preg_replace_callback(array(
			'{
				(						# $1: Marker
					(?<![a-zA-Z0-9])	# Not preceded by alphanum
					(?<!__)				#	or by two marker chars.
					__
				)
				(?=\S) 					# Not followed by whitespace 
				(?!__)					#   or two others marker chars.
				(						# $2: Content
					(?:
						[^_]+?			# Anthing not em markers.
					|
										# Balence any regular _ emphasis inside.
						(?<![a-zA-Z0-9]) _ (?=\S) (.+?) 
						(?<=\S) _ (?![a-zA-Z0-9])
					|
						___+
					)+?
				)
				(?<=\S) __				# End mark not preceded by whitespace.
				(?![a-zA-Z0-9])			# Not followed by alphanum
				(?!__)					#   or two others marker chars.
			}sx',
			'{
				( (?<!\*\*) \*\* )		# $1: Marker (not preceded by two *)
				(?=\S) 					# Not followed by whitespace 
				(?!\1)					#   or two others marker chars.
				(						# $2: Content
					(?:
						[^*]+?			# Anthing not em markers.
					|
										# Balence any regular * emphasis inside.
						\* (?=\S) (.+?) (?<=\S) \*
					)+?
				)
				(?<=\S) \*\*			# End mark not preceded by whitespace.
			}sx',
			),
			array(&$this, '_doItalicAndBold_strong_callback'), $text);
		# Then <em>:
		$text = preg_replace_callback(array(
			'{ ( (?<![a-zA-Z0-9])(?<!_)_ ) (?=\S) (?! \1) (.+?) (?<=\S) \1(?![a-zA-Z0-9]) }sx',
			'{ ( (?<!\*)\* ) (?=\S) (?! \1) (.+?) (?<=\S) \1 }sx',
			),
			array(&$this, '_doItalicAndBold_em_callback'), $text);

		return $text;
	}


	function formParagraphs($text) {
	#
	#	Params:
	#		$text - string to process with html <p> tags
	#
		# Strip leading and trailing lines:
		$text = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $text);
		
		$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

		#
		# Wrap <p> tags and unhashify HTML blocks
		#
		foreach ($grafs as $key => $value) {
			$value = trim($this->runSpanGamut($value));
			
			# Check if this should be enclosed in a paragraph.
			# Clean tag hashes & block tag hashes are left alone.
			$clean_key = $value;
			$block_key = substr($value, 0, 32);
			
			$is_p = (!isset($this->html_blocks[$block_key]) && 
					 !isset($this->html_cleans[$clean_key]));
			
			if ($is_p) {
				$value = "<p>$value</p>";
			}
			$grafs[$key] = $value;
		}
		
		# Join grafs in one text, then unhash HTML tags. 
		$text = implode("\n\n", $grafs);
		
		# Finish by removing any tag hashes still present in $text.
		$text = $this->unhash($text);
		
		return $text;
	}
	
	
	### Footnotes
	
	function stripFootnotes($text) {
	#
	# Strips link definitions from text, stores the URLs and titles in
	# hash references.
	#
		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: [^id]: url "optional title"
		$text = preg_replace_callback('{
			^[ ]{0,'.$less_than_tab.'}\[\^(.+?)\][ ]?:	# note_id = $1
			  [ \t]*
			  \n?					# maybe *one* newline
			(						# text = $2 (no blank lines allowed)
				(?:					
					.+				# actual text
				|
					\n				# newlines but 
					(?!\[\^.+?\]:\s)# negative lookahead for footnote marker.
					(?!\n+[ ]{0,3}\S)# ensure line is not blank and followed 
									# by non-indented content
				)*
			)		
			}xm',
			array(&$this, '_stripFootnotes_callback'),
			$text);
		return $text;
	}
	function _stripFootnotes_callback($matches) {
		$note_id = $matches[1];
		$this->footnotes[$note_id] = $this->outdent($matches[2]);
		return ''; # String that will replace the block
	}


	function doFootnotes($text) {
	#
	# Replace footnote references in $text [^id] with a special text-token 
	# which will be can be
	#
		$text = preg_replace('{\[\^(.+?)\]}', "a\0fn:\\1\0z", $text);
		return $text;
	}

	
	function appendFootnotes($text) {
	#
	# Append footnote list to text.
	#
		$text = preg_replace_callback('{a\0fn:(.*?)\0z}', 
			array(&$this, '_appendFootnotes_callback'), $text);
	
		if (!empty($this->footnotes_ordered)) {
			$text .= "\n\n";
			$text .= "<div class=\"footnotes\">\n";
			$text .= "<hr". MARKDOWN_EMPTY_ELEMENT_SUFFIX ."\n";
			$text .= "<ol>\n\n";
			
			$attr = " rev=\"footnote\"";
			if ($this->fn_backlink_class != "") {
				$class = $this->fn_backlink_class;
				$class = $this->encodeAmpsAndAngles($class);
				$class = str_replace('"', '&quot;', $class);
				$attr .= " class=\"$class\"";
			}
			if ($this->fn_backlink_title != "") {
				$title = $this->fn_backlink_title;
				$title = $this->encodeAmpsAndAngles($title);
				$title = str_replace('"', '&quot;', $title);
				$attr .= " title=\"$title\"";
			}
			$num = 0;
			
			foreach ($this->footnotes_ordered as $note_id => $footnote) {
				$footnote .= "\n"; # Need to append newline before parsing.
				$footnote = $this->runBlockGamut("$footnote\n");
				
				$attr2 = str_replace("%%", ++$num, $attr);
				
				# Add backlink to last paragraph; create new paragraph if needed.
				$backlink = "<a href=\"#fnref:$note_id\"$attr2>&#8617;</a>";
				if (preg_match('{</p>$}', $footnote)) {
					$footnote = substr($footnote, 0, -4) . "&#160;$backlink</p>";
				} else {
					$footnote .= "\n\n<p>$backlink</p>";
				}
				
				$text .= "<li id=\"fn:$note_id\">\n";
				$text .= $footnote . "\n";
				$text .= "</li>\n\n";
			}
			
			$text .= "</ol>\n";
			$text .= "</div>";
			
			$text = preg_replace('{a\{fn:(.*?)\}z}', '[^\\1]', $text);
		}
		return $text;
	}
	function _appendFootnotes_callback($matches) {
		$node_id = $this->fn_id_prefix . $matches[1];
		
		# Create footnote marker only if it has a corresponding footnote *and*
		# the footnote hasn't been used by another marker.
		if (isset($this->footnotes[$node_id])) {
			# Transfert footnote content to the ordered list.
			$this->footnotes_ordered[$node_id] = $this->footnotes[$node_id];
			unset($this->footnotes[$node_id]);
			
			$num = count($this->footnotes_ordered);
			$attr = " rel=\"footnote\"";
			if ($this->fn_link_class != "") {
				$class = $this->fn_link_class;
				$class = $this->encodeAmpsAndAngles($class);
				$class = str_replace('"', '&quot;', $class);
				$attr .= " class=\"$class\"";
			}
			if ($this->fn_link_title != "") {
				$title = $this->fn_link_title;
				$title = $this->encodeAmpsAndAngles($title);
				$title = str_replace('"', '&quot;', $title);
				$attr .= " title=\"$title\"";
			}
			$attr = str_replace("%%", $num, $attr);
			
			return
				"<sup id=\"fnref:$node_id\">".
				"<a href=\"#fn:$node_id\"$attr>$num</a>".
				"</sup>";
		}
		
		return "[^".$matches[1]."]";
	}
		
	
	### Abbreviations ###
	
	function stripAbbreviations($text) {
	#
	# Strips abbreviations from text, stores the URLs and titles in
	# hash references.
	#
		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: [id]*: url "optional title"
		$text = preg_replace_callback('{
			^[ ]{0,'.$less_than_tab.'}\*\[(.+?)\][ ]?:	# abbr_id = $1
			(.*)					# text = $2 (no blank lines allowed)	
			}xm',
			array(&$this, '_stripAbbreviations_callback'),
			$text);
		return $text;
	}
	function _stripAbbreviations_callback($matches) {
		$abbr_word = $matches[1];
		$abbr_desc = $matches[2];
		$this->abbr_matches[] = preg_quote($abbr_word);
		$this->abbr_desciptions[$abbr_word] = trim($abbr_desc);
		return ''; # String that will replace the block
	}
	
	
	function doAbbreviations($text) {
	#
	# Replace footnote references in $text [^id] with a link to the footnote.
	#
		if ($this->abbr_matches) {
			$regex = '{(?<!\w)(?:'. implode('|', $this->abbr_matches) .')(?!\w)}';
	
			$text = preg_replace_callback($regex, 
				array(&$this, '_doAbbreviations_callback'), $text);
		}
		return $text;
	}
	function _doAbbreviations_callback($matches) {
		$abbr = $matches[0];
		if (isset($this->abbr_desciptions[$abbr])) {
			$desc = $this->abbr_desciptions[$abbr];
			if (empty($desc)) {
				return $this->hashSpan("<abbr>$abbr</abbr>");
			} else {
				$desc = $this->escapeSpecialCharsWithinTagAttributes($desc);
				return $this->hashSpan("<abbr title=\"$desc\">$abbr</abbr>");
			}
		} else {
			return $matches[0];
		}
	}

}


/*

PHP Markdown Extra
==================

Description
-----------

This is a PHP port of the original Markdown formatter written in Perl 
by John Gruber. This special "Extra" version of PHP Markdown features 
further enhancements to the syntax for making additional constructs 
such as tables and definition list.

Markdown is a text-to-HTML filter; it translates an easy-to-read /
easy-to-write structured text format into HTML. Markdown's text format
is most similar to that of plain text email, and supports features such
as headers, *emphasis*, code blocks, blockquotes, and links.

Markdown's syntax is designed not as a generic markup language, but
specifically to serve as a front-end to (X)HTML. You can use span-level
HTML tags anywhere in a Markdown document, and you can use block level
HTML tags (like <div> and <table> as well).

For more information about Markdown's syntax, see:

<http://daringfireball.net/projects/markdown/>


Bugs
----

To file bug reports please send email to:

<michel.fortin@michelf.com>

Please include with your report: (1) the example input; (2) the output you
expected; (3) the output Markdown actually produced.


Version History
---------------

See Readme file for details.

Extra 1.1.2 (7 Feb 2007)

Extra 1.1.1 (28 Dec 2006)

Extra 1.1 (1 Dec 2006)

Extra 1.0.1 (9 Dec 2005)

Extra 1.0 (5 Sep 2005)


Copyright and License
---------------------

PHP Markdown & Extra
Copyright (c) 2004-2007 Michel Fortin  
<http://www.michelf.com/>  
All rights reserved.

Based on Markdown  
Copyright (c) 2003-2006 John Gruber   
<http://daringfireball.net/>   
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

*	Redistributions of source code must retain the above copyright notice,
	this list of conditions and the following disclaimer.

*	Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.

*	Neither the name "Markdown" nor the names of its contributors may
	be used to endorse or promote products derived from this software
	without specific prior written permission.

This software is provided by the copyright holders and contributors "as
is" and any express or implied warranties, including, but not limited
to, the implied warranties of merchantability and fitness for a
particular purpose are disclaimed. In no event shall the copyright owner
or contributors be liable for any direct, indirect, incidental, special,
exemplary, or consequential damages (including, but not limited to,
procurement of substitute goods or services; loss of use, data, or
profits; or business interruption) however caused and on any theory of
liability, whether in contract, strict liability, or tort (including
negligence or otherwise) arising in any way out of the use of this
software, even if advised of the possibility of such damage.

*/
?>