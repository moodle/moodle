<?php

#
# PHP Markdown Extra  -  A text-to-HTML conversion tool for web writers
#
# Copyright (c) 2004-2005 Michel Fortin  
# <http://www.michelf.com/projects/php-markdown/>
#
# Based on Markdown  
# Copyright (c) 2004-2005 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#


global	$MarkdownPHPVersion, $MarkdownSyntaxVersion,
		$md_empty_element_suffix, $md_tab_width,
		$md_nested_brackets_depth, $md_nested_brackets, 
		$md_escape_table, $md_backslash_escape_table, 
		$md_list_level;

$MarkdownPHPVersion    = 'Extra 1.0.1'; # Fri 9 Dec 2005
$MarkdownSyntaxVersion = '1.0.1';  # Sun 12 Dec 2004


#
# Global default settings:
#
$md_empty_element_suffix = " />";     # Change to ">" for HTML output
$md_tab_width = 4;

#
# WordPress settings:
#
$md_wp_posts    = true;  # Set to false to remove Markdown from posts.
$md_wp_comments = true;  # Set to false to remove Markdown from comments.


# -- WordPress Plugin Interface -----------------------------------------------
/*
Plugin Name: PHP Markdown Extra
Plugin URI: http://www.michelf.com/projects/php-markdown/
Description: <a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://www.michelf.com/projects/php-markdown/">More...</a>
Version: Extra 1.0.1
Author: Michel Fortin
Author URI: http://www.michelf.com/
*/
if (isset($wp_version)) {
	# More details about how it works here:
	# <http://www.michelf.com/weblog/2005/wordpress-text-flow-vs-markdown/>
	
	# Post content and excerpts
	if ($md_wp_posts) {
		remove_filter('the_content',  'wpautop');
		remove_filter('the_excerpt',  'wpautop');
		add_filter('the_content',     'Markdown', 6);
		add_filter('get_the_excerpt', 'Markdown', 6);
		add_filter('get_the_excerpt', 'trim', 7);
		add_filter('the_excerpt',     'md_add_p');
		add_filter('the_excerpt_rss', 'md_strip_p');
		
		remove_filter('content_save_pre',  'balanceTags', 50);
		remove_filter('excerpt_save_pre',  'balanceTags', 50);
		add_filter('the_content',  	  'balanceTags', 50);
		add_filter('get_the_excerpt', 'balanceTags', 9);
		
		function md_add_p($text) {
			if (strlen($text) == 0) return;
			if (strcasecmp(substr($text, -3), '<p>') == 0) return $text;
			return '<p>'.$text.'</p>';
		}
		function md_strip_p($t) { return preg_replace('{</?[pP]>}', '', $t); }
	}
	
	# Comments
	if ($md_wp_comments) {
		remove_filter('comment_text', 'wpautop');
		remove_filter('comment_text', 'make_clickable');
		add_filter('pre_comment_content', 'Markdown', 6);
		add_filter('pre_comment_content', 'md_hide_tags', 8);
		add_filter('pre_comment_content', 'md_show_tags', 12);
		add_filter('get_comment_text',    'Markdown', 6);
		add_filter('get_comment_excerpt', 'Markdown', 6);
		add_filter('get_comment_excerpt', 'md_strip_p', 7);
	
		global $md_hidden_tags;
		$md_hidden_tags = array(
			'<p>'	=> md5('<p>'),		'</p>'	=> md5('</p>'),
			'<pre>'	=> md5('<pre>'),	'</pre>'=> md5('</pre>'),
			'<ol>'	=> md5('<ol>'),		'</ol>'	=> md5('</ol>'),
			'<ul>'	=> md5('<ul>'),		'</ul>'	=> md5('</ul>'),
			'<li>'	=> md5('<li>'),		'</li>'	=> md5('</li>'),
			);
		
		function md_hide_tags($text) {
			global $md_hidden_tags;
			return str_replace(array_keys($md_hidden_tags), 
								array_values($md_hidden_tags), $text);
		}
		function md_show_tags($text) {
			global $md_hidden_tags;
			return str_replace(array_values($md_hidden_tags), 
								array_keys($md_hidden_tags), $text);
		}
	}
}


# -- bBlog Plugin Info --------------------------------------------------------
function identify_modifier_markdown() {
	global $MarkdownPHPVersion;
	return array(
		'name'			=> 'markdown',
		'type'			=> 'modifier',
		'nicename'		=> 'PHP Markdown Extra',
		'description'	=> 'A text-to-HTML conversion tool for web writers',
		'authors'		=> 'Michel Fortin and John Gruber',
		'licence'		=> 'GPL',
		'version'		=> $MarkdownPHPVersion,
		'help'			=> '<a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. <a href="http://www.michelf.com/projects/php-markdown/">More...</a>'
	);
}

# -- Smarty Modifier Interface ------------------------------------------------
function smarty_modifier_markdown($text) {
	return Markdown($text);
}

# -- Textile Compatibility Mode -----------------------------------------------
# Rename this file to "classTextile.php" and it can replace Textile anywhere.
if (strcasecmp(substr(__FILE__, -16), "classTextile.php") == 0) {
	# Try to include PHP SmartyPants. Should be in the same directory.
	@include_once 'smartypants.php';
	# Fake Textile class. It calls Markdown instead.
	class Textile {
		function TextileThis($text, $lite='', $encode='', $noimage='', $strict='') {
			if ($lite == '' && $encode == '')   $text = Markdown($text);
			if (function_exists('SmartyPants')) $text = SmartyPants($text);
			return $text;
		}
	}
}



#
# Globals:
#

# Regex to match balanced [brackets].
# Needed to insert a maximum bracked depth while converting to PHP.
$md_nested_brackets_depth = 6;
$md_nested_brackets = 
	str_repeat('(?>[^\[\]]+|\[', $md_nested_brackets_depth).
	str_repeat('\])*', $md_nested_brackets_depth);

# Table of hash values for escaped characters:
$md_escape_table = array(
	"\\" => md5("\\"),
	"`" => md5("`"),
	"*" => md5("*"),
	"_" => md5("_"),
	"{" => md5("{"),
	"}" => md5("}"),
	"[" => md5("["),
	"]" => md5("]"),
	"(" => md5("("),
	")" => md5(")"),
	">" => md5(">"),
	"#" => md5("#"),
	"+" => md5("+"),
	"-" => md5("-"),
	"." => md5("."),
	"!" => md5("!"),
	":" => md5(":"),
	"|" => md5("|"),
);
# Create an identical table but for escaped characters.
$md_backslash_escape_table;
foreach ($md_escape_table as $key => $char)
	$md_backslash_escape_table["\\$key"] = $char;



function Markdown($text) {
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
	global $md_urls, $md_titles, $md_html_blocks, $md_html_hashes;
	$md_urls = array();
	$md_titles = array();
	$md_html_blocks = array();
	$md_html_hashes = array();

	# Standardize line endings:
	#   DOS to Unix and Mac to Unix
	$text = str_replace(array("\r\n", "\r"), "\n", $text);

	# Make sure $text ends with a couple of newlines:
	$text .= "\n\n";

	# Convert all tabs to spaces.
	$text = _Detab($text);

	# Turn block-level HTML blocks into hash entries
	$text = _HashHTMLBlocks($text);

	# Strip any lines consisting only of spaces and tabs.
	# This makes subsequent regexen easier to write, because we can
	# match consecutive blank lines with /\n+/ instead of something
	# contorted like /[ \t]*\n+/ .
	$text = preg_replace('/^[ \t]+$/m', '', $text);

	# Strip link definitions, store in hashes.
	$text = _StripLinkDefinitions($text);

	$text = _RunBlockGamut($text, FALSE);

	$text = _UnescapeSpecialChars($text);

	return $text . "\n";
}


function _StripLinkDefinitions($text) {
#
# Strips link definitions from text, stores the URLs and titles in
# hash references.
#
	global $md_tab_width;
	$less_than_tab = $md_tab_width - 1;

	# Link defs are in the form: ^[id]: url "optional title"
	$text = preg_replace_callback('{
						^[ ]{0,'.$less_than_tab.'}\[(.+)\]:	# id = $1
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
							(.+?)			# title = $3
							[")]
							[ \t]*
						)?	# title is optional
						(?:\n+|\Z)
		}xm',
		'_StripLinkDefinitions_callback',
		$text);
	return $text;
}
function _StripLinkDefinitions_callback($matches) {
	global $md_urls, $md_titles;
	$link_id = strtolower($matches[1]);
	$md_urls[$link_id] = _EncodeAmpsAndAngles($matches[2]);
	if (isset($matches[3]))
		$md_titles[$link_id] = str_replace('"', '&quot;', $matches[3]);
	return ''; # String that will replace the block
}


function _HashHTMLBlocks($text) {
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
	global	$block_tags, $context_block_tags, $contain_span_tags, 
			$clean_tags, $auto_close_tags;
	
	# Tags that are always treated as block tags:
	$block_tags = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|'.
					'form|fieldset|iframe|hr|legend';
	
	# Tags treated as block tags only if the opening tag is alone on it's line:
	$context_block_tags = 'script|noscript|math|ins|del';
	
	# Tags where markdown="1" default to span mode:
	$contain_span_tags = 'p|h[1-6]|li|dd|dt|td|th|legend';
	
	# Tags which must not have their contents modified, no matter where 
	# they appear:
	$clean_tags = 'script|math';
	
	# Tags that do not need to be closed.
	$auto_close_tags = 'hr|img';
	
	# Regex to match any tag.
	global $tag_match;
	$tag_match =
		'{
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
				<\?     .*?     \?>	# Processing instruction
			|
				<!\[CDATA\[.*?\]\]>	# CData Block
			)
		}xs';
	
	#
	# Call the HTML-in-Markdown hasher.
	#
	list($text, ) = _HashHTMLBlocks_InMarkdown($text);
	
	return $text;
}
function _HashHTMLBlocks_InMarkdown($text, $indent = 0, 
									$enclosing_tag = '', $md_span = false)
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
# *   If $md_span is true, text inside must treated as span. So any double 
#     newline will be replaced by a single newline so that it does not create 
#     paragraphs.
#
# Returns an array of that form: ( processed text , remaining text )
#
	global	$block_tags, $context_block_tags, $clean_tags, $auto_close_tags,
			$tag_match;
	
	if ($text === '') return array('', '');

	# Regex to check for the presense of newlines around a block tag.
	$newline_match_before = "/(?:^\n?|\n\n) *$/";
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
						'.$block_tags.'			|
						'.$context_block_tags.'	|
						'.$clean_tags.'        	|
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
				<\?     .*?     \?>	# Processing instruction
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
		
		# If in Markdown span mode, replace any multiple newlines that would 
		# trigger a new paragraph.
		if ($md_span) {
			$parts[0] = preg_replace('/\n\n/', "\n", $parts[0]);
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
		else if (preg_match("{^<(?:$block_tags)\b}", $tag) ||
			(	preg_match("{^<(?:$context_block_tags)\b}", $tag) &&
				preg_match($newline_match_before, $parsed) &&
				preg_match($newline_match_after, $text)	)
			)
		{
			# Need to parse tag and following text using the HTML parser.
			list($block_text, $text) = 
				_HashHTMLBlocks_InHTML($tag . $text,
									"_HashHTMLBlocks_HashBlock", TRUE);
			
			# Make sure it stays outside of any paragraph by adding newlines.
			$parsed .= "\n\n$block_text\n\n";
		}
		#
		# Check for: Clean tag (like script, math)
		#            HTML Comments, processing instructions.
		#
		else if (preg_match("{^<(?:$clean_tags)\b}", $tag) ||
			$tag{1} == '!' || $tag{1} == '?')
		{
			# Need to parse tag and following text using the HTML parser.
			# (don't check for markdown attribute)
			list($block_text, $text) = 
				_HashHTMLBlocks_InHTML($tag . $text, 
									"_HashHTMLBlocks_HashClean", FALSE);
			
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
function _HashHTMLBlocks_InHTML($text, $hash_function, $md_attr) {
#
# Parse HTML, calling _HashHTMLBlocks_InMarkdown for block tags.
#
# *   Calls $hash_function to convert any blocks.
# *   Stops when the first opening tag closes.
# *   $md_attr indicate if the use of the `markdown="1"` attribute is allowed.
#     (it is not inside clean tags)
#
# Returns an array of that form: ( processed text , remaining text )
#
	global $auto_close_tags, $contain_span_tags, $tag_match;
	
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
		if (preg_match("{^</?(?:$auto_close_tags)\b}", $tag) ||
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
				preg_match('/^(?:1|block|span)$/', $attr_matches[2]))
			{
				# Remove `markdown` attribute from opening tag.
				$tag = preg_replace($markdown_attr_match, '', $tag);
				
				# Check if text inside this tag must be parsed in span mode.
				$md_mode = $attr_matches[2];
				$span_mode = $md_mode == 'span' || $md_mode != 'block' &&
							preg_match("{^<(?:$contain_span_tags)\b}", $tag);
				
				# Calculate indent before tag.
				preg_match('/(?:^|\n)( *?)(?! ).*?$/', $block_text, $matches);
				$indent = strlen($matches[1]);
				
				# End preceding block with this tag.
				$block_text .= $tag;
				$parsed .= $hash_function($block_text, $span_mode);
				
				# Get enclosing tag name for the ParseMarkdown function.
				preg_match('/^<([\w:$]*)\b/', $tag, $matches);
				$tag_name = $matches[1];
				
				# Parse the content using the HTML-in-Markdown parser.
				list ($block_text, $text)
					= _HashHTMLBlocks_InMarkdown($text, $indent, 
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
	$parsed .= $hash_function($block_text);
	
	return array($parsed, $text);
}
function _HashHTMLBlocks_HashBlock($text) {
	global $md_html_hashes, $md_html_blocks;
	$key = md5($text);
	$md_html_hashes[$key] = $text;
	$md_html_blocks[$key] = $text;
	return $key; # String that will replace the tag.
}
function _HashHTMLBlocks_HashClean($text) {
	global $md_html_hashes;
	$key = md5($text);
	$md_html_hashes[$key] = $text;
	return $key; # String that will replace the clean tag.
}


function _HashBlock($text) {
#
# Called whenever a tag must be hashed. When a function insert a block-level 
# tag in $text, it pass through this function and is automaticaly escaped, 
# which remove the need to call _HashHTMLBlocks at every step.
#
	# Swap back any tag hash found in $text so we do not have to _UnhashTags
	# multiple times at the end. Must do this because of 
	$text = _UnhashTags($text);
	
	# Then hash the block as normal.
	return _HashHTMLBlocks_HashBlock($text);
}


function _RunBlockGamut($text, $hash_html_blocks = TRUE) {
#
# These are all the transformations that form block-level
# tags like paragraphs, headers, and list items.
#
	if ($hash_html_blocks) {
		# We need to escape raw HTML in Markdown source before doing anything 
		# else. This need to be done for each block, and not only at the 
		# begining in the Markdown function since hashed blocks can be part of
		# a list item and could have been indented. Indented blocks would have 
		# been seen as a code block in previous pass of _HashHTMLBlocks.
		$text = _HashHTMLBlocks($text);
	}

	$text = _DoHeaders($text);
	$text = _DoTables($text);

	# Do Horizontal Rules:
	global $md_empty_element_suffix;
	$text = preg_replace(
		array('{^[ ]{0,2}([ ]?\*[ ]?){3,}[ \t]*$}emx',
			  '{^[ ]{0,2}([ ]? -[ ]?){3,}[ \t]*$}emx',
			  '{^[ ]{0,2}([ ]? _[ ]?){3,}[ \t]*$}emx'),
		"_HashBlock('\n<hr$md_empty_element_suffix\n')", 
		$text);

	$text = _DoLists($text);
	$text = _DoDefLists($text);
	$text = _DoCodeBlocks($text);
	$text = _DoBlockQuotes($text);
	$text = _FormParagraphs($text);

	return $text;
}


function _RunSpanGamut($text) {
#
# These are all the transformations that occur *within* block-level
# tags like paragraphs, headers, and list items.
#
	global $md_empty_element_suffix;

	$text = _DoCodeSpans($text);

	$text = _EscapeSpecialChars($text);

	# Process anchor and image tags. Images must come first,
	# because ![foo][f] looks like an anchor.
	$text = _DoImages($text);
	$text = _DoAnchors($text);

	# Make links out of things like `<http://example.com/>`
	# Must come after _DoAnchors(), because you can use < and >
	# delimiters in inline links like [this](<url>).
	$text = _DoAutoLinks($text);
	$text = _EncodeAmpsAndAngles($text);
	$text = _DoItalicsAndBold($text);

	# Do hard breaks:
	$text = preg_replace('/ {2,}\n/', "<br$md_empty_element_suffix\n", $text);

	return $text;
}


function _EscapeSpecialChars($text) {
	global $md_escape_table;
	$tokens = _TokenizeHTML($text);

	$text = '';   # rebuild $text from the tokens
#	$in_pre = 0;  # Keep track of when we're inside <pre> or <code> tags.
#	$tags_to_skip = "!<(/?)(?:pre|code|kbd|script|math)[\s>]!";

	foreach ($tokens as $cur_token) {
		if ($cur_token[0] == 'tag') {
			# Within tags, encode * and _ so they don't conflict
			# with their use in Markdown for italics and strong.
			# We're replacing each such character with its
			# corresponding MD5 checksum value; this is likely
			# overkill, but it should prevent us from colliding
			# with the escape values by accident.
			$cur_token[1] = str_replace(array('*', '_'),
				array($md_escape_table['*'], $md_escape_table['_']),
				$cur_token[1]);
			$text .= $cur_token[1];
		} else {
			$t = $cur_token[1];
			$t = _EncodeBackslashEscapes($t);
			$text .= $t;
		}
	}
	return $text;
}


function _DoAnchors($text) {
#
# Turn Markdown link shortcuts into XHTML <a> tags.
#
	global $md_nested_brackets;
	#
	# First, handle reference-style links: [link text] [id]
	#
	$text = preg_replace_callback("{
		(					# wrap whole match in $1
		  \\[
			($md_nested_brackets)	# link text = $2
		  \\]

		  [ ]?				# one optional space
		  (?:\\n[ ]*)?		# one optional newline followed by spaces

		  \\[
			(.*?)		# id = $3
		  \\]
		)
		}xs",
		'_DoAnchors_reference_callback', $text);

	#
	# Next, inline-style links: [link text](url "optional title")
	#
	$text = preg_replace_callback("{
		(				# wrap whole match in $1
		  \\[
			($md_nested_brackets)	# link text = $2
		  \\]
		  \\(			# literal paren
			[ \\t]*
			<?(.*?)>?	# href = $3
			[ \\t]*
			(			# $4
			  (['\"])	# quote char = $5
			  (.*?)		# Title = $6
			  \\5		# matching quote
			)?			# title is optional
		  \\)
		)
		}xs",
		'_DoAnchors_inline_callback', $text);

	return $text;
}
function _DoAnchors_reference_callback($matches) {
	global $md_urls, $md_titles, $md_escape_table;
	$whole_match = $matches[1];
	$link_text   = $matches[2];
	$link_id     = strtolower($matches[3]);

	if ($link_id == "") {
		$link_id = strtolower($link_text); # for shortcut links like [this][].
	}

	if (isset($md_urls[$link_id])) {
		$url = $md_urls[$link_id];
		# We've got to encode these to avoid conflicting with italics/bold.
		$url = str_replace(array('*', '_'),
						   array($md_escape_table['*'], $md_escape_table['_']),
						   $url);
		$result = "<a href=\"$url\"";
		if ( isset( $md_titles[$link_id] ) ) {
			$title = $md_titles[$link_id];
			$title = str_replace(array('*',     '_'),
								 array($md_escape_table['*'], 
									   $md_escape_table['_']), $title);
			$result .=  " title=\"$title\"";
		}
		$result .= ">$link_text</a>";
	}
	else {
		$result = $whole_match;
	}
	return $result;
}
function _DoAnchors_inline_callback($matches) {
	global $md_escape_table;
	$whole_match	= $matches[1];
	$link_text		= $matches[2];
	$url			= $matches[3];
	$title			=& $matches[6];

	# We've got to encode these to avoid conflicting with italics/bold.
	$url = str_replace(array('*', '_'),
					   array($md_escape_table['*'], $md_escape_table['_']), 
					   $url);
	$result = "<a href=\"$url\"";
	if (isset($title)) {
		$title = str_replace('"', '&quot;', $title);
		$title = str_replace(array('*', '_'),
							 array($md_escape_table['*'], $md_escape_table['_']),
							 $title);
		$result .=  " title=\"$title\"";
	}
	
	$result .= ">$link_text</a>";

	return $result;
}


function _DoImages($text) {
#
# Turn Markdown image shortcuts into <img> tags.
#
	global $md_nested_brackets;

	#
	# First, handle reference-style labeled images: ![alt text][id]
	#
	$text = preg_replace_callback('{
		(				# wrap whole match in $1
		  !\[
			('.$md_nested_brackets.')		# alt text = $2
		  \]

		  [ ]?				# one optional space
		  (?:\n[ ]*)?		# one optional newline followed by spaces

		  \[
			(.*?)		# id = $3
		  \]

		)
		}xs', 
		'_DoImages_reference_callback', $text);

	#
	# Next, handle inline images:  ![alt text](url "optional title")
	# Don't forget: encode * and _

	$text = preg_replace_callback('{
		(				# wrap whole match in $1
		  !\[
			('.$md_nested_brackets.')		# alt text = $2
		  \]
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
		'_DoImages_inline_callback', $text);

	return $text;
}
function _DoImages_reference_callback($matches) {
	global $md_urls, $md_titles, $md_empty_element_suffix, $md_escape_table;
	$whole_match = $matches[1];
	$alt_text    = $matches[2];
	$link_id     = strtolower($matches[3]);

	if ($link_id == "") {
		$link_id = strtolower($alt_text); # for shortcut links like ![this][].
	}

	$alt_text = str_replace('"', '&quot;', $alt_text);
	if (isset($md_urls[$link_id])) {
		$url = $md_urls[$link_id];
		# We've got to encode these to avoid conflicting with italics/bold.
		$url = str_replace(array('*', '_'),
						   array($md_escape_table['*'], $md_escape_table['_']),
						   $url);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($md_titles[$link_id])) {
			$title = $md_titles[$link_id];
			$title = str_replace(array('*', '_'),
								 array($md_escape_table['*'], 
									   $md_escape_table['_']), $title);
			$result .=  " title=\"$title\"";
		}
		$result .= $md_empty_element_suffix;
	}
	else {
		# If there's no such link ID, leave intact:
		$result = $whole_match;
	}

	return $result;
}
function _DoImages_inline_callback($matches) {
	global $md_empty_element_suffix, $md_escape_table;
	$whole_match	= $matches[1];
	$alt_text		= $matches[2];
	$url			= $matches[3];
	$title			= '';
	if (isset($matches[6])) {
		$title		= $matches[6];
	}

	$alt_text = str_replace('"', '&quot;', $alt_text);
	$title    = str_replace('"', '&quot;', $title);
	# We've got to encode these to avoid conflicting with italics/bold.
	$url = str_replace(array('*', '_'),
					   array($md_escape_table['*'], $md_escape_table['_']),
					   $url);
	$result = "<img src=\"$url\" alt=\"$alt_text\"";
	if (isset($title)) {
		$title = str_replace(array('*', '_'),
							 array($md_escape_table['*'], $md_escape_table['_']),
							 $title);
		$result .=  " title=\"$title\""; # $title already quoted
	}
	$result .= $md_empty_element_suffix;

	return $result;
}


function _DoHeaders($text) {
	# Setext-style headers:
	#	  Header 1
	#	  ========
	#  
	#	  Header 2
	#	  --------
	#
	$text = preg_replace(
		array('{ (^.+?) (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})? [ \t]*\n=+[ \t]*\n+ }emx',
			  '{ (^.+?) (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})? [ \t]*\n-+[ \t]*\n+ }emx'),
		array("_HashBlock('<h1'. ('\\2'? ' id=\"'._UnslashQuotes('\\2').'\"':'').
				'>'._RunSpanGamut(_UnslashQuotes('\\1')).'</h1>'
			  ) . '\n\n'",
			  "_HashBlock('<h2'. ('\\2'? ' id=\"'._UnslashQuotes('\\2').'\"':'').
				'>'._RunSpanGamut(_UnslashQuotes('\\1')).'</h2>'
			  ) . '\n\n'"),
		$text);

	# atx-style headers:
	#	# Header 1
	#	## Header 2
	#	## Header 2 with closing hashes ##
	#	...
	#	###### Header 6
	#
	$text = preg_replace('{
			^(\#{1,6})	# $1 = string of #\'s
			[ \t]*
			(.+?)		# $2 = Header text
			[ \t]*
			\#*			# optional closing #\'s (not counted)
			(?:[ ]+\{\#([-_:a-zA-Z0-9]+)\}[ ]*)? # id attribute
			\n+
		}xme',
		"_HashBlock(
			'<h'.strlen('\\1'). ('\\3'? ' id=\"'._UnslashQuotes('\\3').'\"':'').'>'.
			_RunSpanGamut(_UnslashQuotes('\\2')).
			'</h'.strlen('\\1').'>'
		) . '\n\n'",
		$text);

	return $text;
}


function _DoTables($text) {
#
# Form HTML tables.
#
	global $md_tab_width;
	$less_than_tab = $md_tab_width - 1;
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
		'_DoTable_LeadingPipe_callback', $text);
	
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
		'_DoTable_callback', $text);

	return $text;
}
function _DoTable_LeadingPipe_callback($matches) {
	$head		= $matches[1];
	$underline	= $matches[2];
	$content	= $matches[3];
	
	# Remove leading pipe for each row.
	$content	= preg_replace('/^ *[|]/m', '', $content);
	
	return _DoTable_callback(array($matches[0], $head, $underline, $content));
}
function _DoTable_callback($matches) {
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
	$head	= _DoCodeSpans($head);
	$headers	= preg_split('/ *[|] */', $head);
	$col_count	= count($headers);
	
	# Write column headers.
	$text = "<table>\n";
	$text .= "<thead>\n";
	$text .= "<tr>\n";
	foreach ($headers as $n => $header)
		$text .= "  <th$attr[$n]>"._RunSpanGamut(trim($header))."</th>\n";
	$text .= "</tr>\n";
	$text .= "</thead>\n";
	
	# Split content by row.
	$rows = explode("\n", trim($content, "\n"));
	
	$text .= "<tbody>\n";
	foreach ($rows as $row) {
		# Creating code spans before splitting the row is an easy way to 
		# handle a code span containg pipes.
		$row = _DoCodeSpans($row);
		
		# Split row by cell.
		$row_cells = preg_split('/ *[|] */', $row, $col_count);
		$row_cells = array_pad($row_cells, $col_count, '');
		
		$text .= "<tr>\n";
		foreach ($row_cells as $n => $cell)
			$text .= "  <td$attr[$n]>"._RunSpanGamut(trim($cell))."</td>\n";
		$text .= "</tr>\n";
	}
	$text .= "</tbody>\n";
	$text .= "</table>";
	
	return _HashBlock($text) . "\n";
}


function _DoLists($text) {
#
# Form HTML ordered (numbered) and unordered (bulleted) lists.
#
	global $md_tab_width, $md_list_level;
	$less_than_tab = $md_tab_width - 1;

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
	
		if ($md_list_level) {
			$text = preg_replace_callback('{
					^
					'.$whole_list.'
				}mx',
				'_DoLists_callback', $text);
		}
		else {
			$text = preg_replace_callback('{
					(?:(?<=\n\n)|\A\n?)
					'.$whole_list.'
				}mx',
				'_DoLists_callback', $text);
		}
	}

	return $text;
}
function _DoLists_callback($matches) {
	# Re-usable patterns to match list item bullets and number markers:
	$marker_ul  = '[*+-]';
	$marker_ol  = '\d+[.]';
	$marker_any = "(?:$marker_ul|$marker_ol)";
	
	$list = $matches[1];
	$list_type = preg_match("/$marker_ul/", $matches[3]) ? "ul" : "ol";
	
	$marker_any = ( $list_type == "ul" ? $marker_ul : $marker_ol );
	
	# Turn double returns into triple returns, so that we can make a
	# paragraph for the last item in a list, if necessary:
	$list = preg_replace("/\n{2,}/", "\n\n\n", $list);
	$result = _ProcessListItems($list, $marker_any);
	$result = "<$list_type>\n" . $result . "</$list_type>";
	return "\n" . _HashBlock($result) . "\n\n";
}


function _ProcessListItems($list_str, $marker_any) {
#
#	Process the contents of a single ordered or unordered list, splitting it
#	into individual list items.
#
	global $md_list_level;
	
	# The $md_list_level global keeps track of when we're inside a list.
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
	
	$md_list_level++;

	# trim trailing blank lines:
	$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

	$list_str = preg_replace_callback('{
		(\n)?							# leading line = $1
		(^[ \t]*)						# leading whitespace = $2
		('.$marker_any.') [ \t]+		# list marker = $3
		((?s:.+?)						# list item text   = $4
		(\n{1,2}))
		(?= \n* (\z | \2 ('.$marker_any.') [ \t]+))
		}xm',
		'_ProcessListItems_callback', $list_str);

	$md_list_level--;
	return $list_str;
}
function _ProcessListItems_callback($matches) {
	$item = $matches[4];
	$leading_line =& $matches[1];
	$leading_space =& $matches[2];

	if ($leading_line || preg_match('/\n{2,}/', $item)) {
		$item = _RunBlockGamut(_Outdent($item));
	}
	else {
		# Recursion for sub-lists:
		$item = _DoLists(_Outdent($item));
		$item = preg_replace('/\n+$/', '', $item);
		$item = _RunSpanGamut($item);
	}

	return "<li>" . $item . "</li>\n";
}


function _DoDefLists($text) {
#
# Form HTML definition lists.
#
	global $md_tab_width;
	$less_than_tab = $md_tab_width - 1;

	# Re-usable patterns to match list item bullets and number markers:

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
		'_DoDefLists_callback', $text);

	return $text;
}
function _DoDefLists_callback($matches) {
	# Re-usable patterns to match list item bullets and number markers:
	$list = $matches[1];
	
	# Turn double returns into triple returns, so that we can make a
	# paragraph for the last item in a list, if necessary:
	$result = trim(_ProcessDefListItems($list));
	$result = "<dl>\n" . $result . "\n</dl>";
	return _HashBlock($result) . "\n\n";
}


function _ProcessDefListItems($list_str) {
#
#	Process the contents of a single ordered or unordered list, splitting it
#	into individual list items.
#
	global $md_tab_width;
	$less_than_tab = $md_tab_width - 1;
	
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
		'_ProcessDefListItems_callback_dt', $list_str);

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
		'_ProcessDefListItems_callback_dd', $list_str);

	return $list_str;
}
function _ProcessDefListItems_callback_dt($matches) {
	$terms = explode("\n", trim($matches[1]));
	$text = '';
	foreach ($terms as $term) {
		$term = _RunSpanGamut(trim($term));
		$text .= "\n<dt>" . $term . "</dt>";
	}
	return $text . "\n";
}
function _ProcessDefListItems_callback_dd($matches) {
	$leading_line	= $matches[1];
	$def			= $matches[2];

	if ($leading_line || preg_match('/\n{2,}/', $def)) {
		$def = _RunBlockGamut(_Outdent($def . "\n\n"));
		$def = "\n". $def ."\n";
	}
	else {
		$def = rtrim($def);
		$def = _RunSpanGamut(_Outdent($def));
	}

	return "\n<dd>" . $def . "</dd>\n";
}


function _DoCodeBlocks($text) {
#
#	Process Markdown `<pre><code>` blocks.
#
	global $md_tab_width;
	$text = preg_replace_callback('{
			(?:\n\n|\A)
			(	            # $1 = the code block -- one or more lines, starting with a space/tab
			  (?:
				(?:[ ]{'.$md_tab_width.'} | \t)  # Lines must start with a tab or a tab-width of spaces
				.*\n+
			  )+
			)
			((?=^[ ]{0,'.$md_tab_width.'}\S)|\Z)	# Lookahead for non-space at line-start, or end of doc
		}xm',
		'_DoCodeBlocks_callback', $text);

	return $text;
}
function _DoCodeBlocks_callback($matches) {
	$codeblock = $matches[1];

	$codeblock = _EncodeCode(_Outdent($codeblock));
//	$codeblock = _Detab($codeblock);
	# trim leading newlines and trailing whitespace
	$codeblock = preg_replace(array('/\A\n+/', '/\s+\z/'), '', $codeblock);

	$result = "<pre><code>" . $codeblock . "\n</code></pre>";

	return "\n\n" . _HashBlock($result) . "\n\n";
}


function _DoCodeSpans($text) {
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
		'_DoCodeSpans_callback', $text);

	return $text;
}
function _DoCodeSpans_callback($matches) {
	$c = $matches[2];
	$c = preg_replace('/^[ \t]*/', '', $c); # leading whitespace
	$c = preg_replace('/[ \t]*$/', '', $c); # trailing whitespace
	$c = _EncodeCode($c);
	return "<code>$c</code>";
}


function _EncodeCode($_) {
#
# Encode/escape certain characters inside Markdown code runs.
# The point is that in code, these characters are literals,
# and lose their special Markdown meanings.
#
	global $md_escape_table;

	# Encode all ampersands; HTML entities are not
	# entities within a Markdown code span.
	$_ = str_replace('&', '&amp;', $_);

	# Do the angle bracket song and dance:
	$_ = str_replace(array('<',    '>'), 
					 array('&lt;', '&gt;'), $_);

	# Now, escape characters that are magic in Markdown:
	$_ = str_replace(array_keys($md_escape_table), 
					 array_values($md_escape_table), $_);

	return $_;
}


function _DoItalicsAndBold($text) {
	# <strong> must go first:
	$text = preg_replace(array(
		'{
			( (?<!\w) __ )			# $1: Marker (not preceded by alphanum)
			(?=\S) 					# Not followed by whitespace 
			(?!__)					#   or two others marker chars.
			(						# $2: Content
				(?>
					[^_]+?			# Anthing not em markers.
				|
									# Balence any regular _ emphasis inside.
					(?<![a-zA-Z0-9])_ (?=\S) (?! _) (.+?) 
					(?<=\S) _ (?![a-zA-Z0-9])
				)+?
			)
			(?<=\S) __				# End mark not preceded by whitespace.
			(?!\w)					# Not followed by alphanum.
		}sx',
		'{
			( (?<!\*\*) \*\* )		# $1: Marker (not preceded by two *)
			(?=\S) 					# Not followed by whitespace 
			(?!\1)					#   or two others marker chars.
			(						# $2: Content
				(?>
					[^*]+?			# Anthing not em markers.
				|
									# Balence any regular * emphasis inside.
					\* (?=\S) (?! \*) (.+?) (?<=\S) \*
				)+?
			)
			(?<=\S) \*\*			# End mark not preceded by whitespace.
		}sx',
		),
		'<strong>\2</strong>', $text);
	# Then <em>:
	$text = preg_replace(array(
		'{ ( (?<!\w) _ ) (?=\S) (?! _)  (.+?) (?<=\S) _ (?!\w) }sx',
		'{ ( (?<!\*)\* ) (?=\S) (?! \*) (.+?) (?<=\S) \* }sx',
		),
		'<em>\2</em>', $text);

	return $text;
}


function _DoBlockQuotes($text) {
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
		'_DoBlockQuotes_callback', $text);

	return $text;
}
function _DoBlockQuotes_callback($matches) {
	$bq = $matches[1];
	# trim one level of quoting - trim whitespace-only lines
	$bq = preg_replace(array('/^[ \t]*>[ \t]?/m', '/^[ \t]+$/m'), '', $bq);
	$bq = _RunBlockGamut($bq);		# recurse

	$bq = preg_replace('/^/m', "  ", $bq);
	# These leading spaces screw with <pre> content, so we need to fix that:
	$bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx', 
								'_DoBlockQuotes_callback2', $bq);

	return _HashBlock("<blockquote>\n$bq\n</blockquote>") . "\n\n";
}
function _DoBlockQuotes_callback2($matches) {
	$pre = $matches[1];
	$pre = preg_replace('/^  /m', '', $pre);
	return $pre;
}


function _FormParagraphs($text) {
#
#	Params:
#		$text - string to process with html <p> tags
#
	global $md_html_blocks, $md_html_hashes;

	# Strip leading and trailing lines:
	$text = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $text);
	
	$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

	#
	# Wrap <p> tags and unhashify HTML blocks
	#
	foreach ($grafs as $key => $value) {
		$value = trim(_RunSpanGamut($value));
		
		# Check if this should be enclosed in a paragraph.
		# Text equaling to a clean tag hash are not enclosed.
		# Text starting with a block tag hash are not either.
		$clean_key = $value;
		$block_key = substr($value, 0, 32);
		
		$is_p = (!isset($md_html_blocks[$block_key]) && 
				 !isset($md_html_hashes[$clean_key]));
		
		if ($is_p) {
			$value = "<p>$value</p>";
		}
		$grafs[$key] = $value;
	}
	
	# Join grafs in one text, then unhash HTML tags. 
	$text = implode("\n\n", $grafs);
	
	# Finish by removing any tag hashes still present in $text.
	$text = _UnhashTags($text);
	
	return $text;
}


function _EncodeAmpsAndAngles($text) {
# Smart processing for ampersands and angle brackets that need to be encoded.

	# Ampersand-encoding based entirely on Nat Irons's Amputator MT plugin:
	#   http://bumppo.net/projects/amputator/
	$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/', 
						 '&amp;', $text);;

	# Encode naked <'s
	$text = preg_replace('{<(?![a-z/?\$!])}i', '&lt;', $text);

	return $text;
}


function _EncodeBackslashEscapes($text) {
#
#	Parameter:  String.
#	Returns:    The string, with after processing the following backslash
#				escape sequences.
#
	global $md_escape_table, $md_backslash_escape_table;
	# Must process escaped backslashes first.
	return str_replace(array_keys($md_backslash_escape_table),
					   array_values($md_backslash_escape_table), $text);
}


function _DoAutoLinks($text) {
	$text = preg_replace("!<((https?|ftp):[^'\">\\s]+)>!", 
						 '<a href="\1">\1</a>', $text);

	# Email addresses: <address@domain.foo>
	$text = preg_replace('{
		<
        (?:mailto:)?
		(
			[-.\w]+
			\@
			[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]+
		)
		>
		}exi',
		"_EncodeEmailAddress(_UnescapeSpecialChars(_UnslashQuotes('\\1')))",
		$text);

	return $text;
}


function _EncodeEmailAddress($addr) {
#
#	Input: an email address, e.g. "foo@example.com"
#
#	Output: the email address as a mailto link, with each character
#		of the address encoded as either a decimal or hex entity, in
#		the hopes of foiling most address harvesting spam bots. E.g.:
#
#	  <a href="&#x6D;&#97;&#105;&#108;&#x74;&#111;:&#102;&#111;&#111;&#64;&#101;
#		x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;">&#102;&#111;&#111;
#		&#64;&#101;x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;</a>
#
#	Based by a filter by Matthew Wickline, posted to the BBEdit-Talk
#	mailing list: <http://tinyurl.com/yu7ue>
#
	$addr = "mailto:" . $addr;
	$length = strlen($addr);

	# leave ':' alone (to spot mailto: later)
	$addr = preg_replace_callback('/([^\:])/', 
								  '_EncodeEmailAddress_callback', $addr);

	$addr = "<a href=\"$addr\">$addr</a>";
	# strip the mailto: from the visible part
	$addr = preg_replace('/">.+?:/', '">', $addr);

	return $addr;
}
function _EncodeEmailAddress_callback($matches) {
	$char = $matches[1];
	$r = rand(0, 100);
	# roughly 10% raw, 45% hex, 45% dec
	# '@' *must* be encoded. I insist.
	if ($r > 90 && $char != '@') return $char;
	if ($r < 45) return '&#x'.dechex(ord($char)).';';
	return '&#'.ord($char).';';
}


function _UnescapeSpecialChars($text) {
#
# Swap back in all the special characters we've hidden.
#
	global $md_escape_table;
	return str_replace(array_values($md_escape_table), 
					   array_keys($md_escape_table), $text);
}


function _UnhashTags($text) {
#
# Swap back in all the tags hashed by _HashHTMLBlocks.
#
	global $md_html_hashes;
	return str_replace(array_keys($md_html_hashes), 
					   array_values($md_html_hashes), $text);
}


# _TokenizeHTML is shared between PHP Markdown and PHP SmartyPants.
# We only define it if it is not already defined.
if (!function_exists('_TokenizeHTML')) :
function _TokenizeHTML($str) {
#
#   Parameter:  String containing HTML markup.
#   Returns:    An array of the tokens comprising the input
#               string. Each token is either a tag (possibly with nested,
#               tags contained therein, such as <a href="<MTFoo>">, or a
#               run of text between tags. Each element of the array is a
#               two-element array; the first is either 'tag' or 'text';
#               the second is the actual value.
#
#
#   Regular expression derived from the _tokenize() subroutine in 
#   Brad Choate's MTRegex plugin.
#   <http://www.bradchoate.com/past/mtregex.php>
#
	$index = 0;
	$tokens = array();

	$match = '(?s:<!(?:--.*?--\s*)+>)|'.	# comment
			 '(?s:<\?.*?\?>)|'.				# processing instruction
			 								# regular tags
			 '(?:<[/!$]?[-a-zA-Z0-9:]+\b(?>[^"\'>]+|"[^"]*"|\'[^\']*\')*>)'; 

	$parts = preg_split("{($match)}", $str, -1, PREG_SPLIT_DELIM_CAPTURE);

	foreach ($parts as $part) {
		if (++$index % 2 && $part != '') 
			$tokens[] = array('text', $part);
		else
			$tokens[] = array('tag', $part);
	}

	return $tokens;
}
endif;


function _Outdent($text) {
#
# Remove one level of line-leading tabs or spaces
#
	global $md_tab_width;
	return preg_replace("/^(\\t|[ ]{1,$md_tab_width})/m", "", $text);
}


function _Detab($text) {
#
# Replace tabs with the appropriate amount of space.
#
	global $md_tab_width;

	# For each line we separate the line in blocks delemited by
	# tab characters. Then we reconstruct every line by adding the 
	# appropriate number of space between each blocks.
	
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
			$amount = $md_tab_width - strlen($line) % $md_tab_width;
			$line .= str_repeat(" ", $amount) . $block;
		}
		$text .= "$line\n";
	}
	return $text;
}


function _UnslashQuotes($text) {
#
#	This function is useful to remove automaticaly slashed double quotes
#	when using preg_replace and evaluating an expression.
#	Parameter:  String.
#	Returns:    The string with any slash-double-quote (\") sequence replaced
#				by a single double quote.
#
	return str_replace('\"', '"', $text);
}


/*

PHP Markdown Extra
==================

Description
-----------

This is a PHP translation of the original Markdown formatter written in
Perl by John Gruber. This special version of PHP Markdown also include 
syntax additions by myself.

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

Extra 1.0.1 - 9 December 2005

Extra 1.0 - 5 September 2005

Extra 1.0b4 - 1 August 2005

Extra 1.0b3 - 29 July 2005

Extra 1.0b2 - 26 July 2005

Extra 1.0b1 - 25 July 2005


Author & Contributors
---------------------

Original Markdown in Perl by John Gruber  
<http://daringfireball.net/>

PHP port and extras by Michel Fortin  
<http://www.michelf.com/>


Copyright and License
---------------------

Copyright (c) 2004-2005 Michel Fortin  
<http://www.michelf.com/>  
All rights reserved.

Based on Markdown  
Copyright (c) 2003-2004 John Gruber   
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