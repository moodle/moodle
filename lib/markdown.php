<?php

#
# Markdown  -  A text-to-HTML conversion tool for web writers
#
# Copyright (c) 2004 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#
# Copyright (c) 2004 Michel Fortin - Translation to PHP  
# <http://www.michelf.com/projects/php-markdown/>
#



global  $MarkdownPHPVersion, $MarkdownSyntaxVersion,
		$md_empty_element_suffix, $md_tab_width,
		$md_nested_brackets_depth, $md_nested_brackets, 
		$md_escape_table, $md_backslash_escape_table;


$MarkdownPHPVersion    = '1.0b9'; # Wed 27 Jun 2004
$MarkdownSyntaxVersion = '1.0b9'; # Tue 27 Jun 2004


#
# Global default settings:
#
$md_empty_element_suffix = " />";     # Change to ">" for HTML output
$md_tab_width = 4;


# -- WordPress plugin interface -----------------------------------------------
/*
Plugin Name: Markdown
Plugin URI: http://www.michelf.com/projects/php-markdown/
Description: <a href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a> allows you to write using an easy-to-read, easy-to-write plain text format. This plugin <strong>enables Markdown for your posts and comments</strong>. Based on the original Perl version by <a href="http://daringfireball.net/">John Gruber</a>. Thanks to <a href="http://photomatt.net/">Matt</a> for making the first Markdown WP plugin. If you use this plugin you should disable Textile 1 and 2 because they do not play well with Markdown.
Version: 1.0b9
Author: Michel Fortin
Author URI: http://www.michelf.com/
*/
if (isset($wp_version)) {
	# Remove default WordPress auto-paragraph filter.
    remove_filter('the_content', 'wpautop');
    remove_filter('the_excerpt', 'wpautop');
    remove_filter('comment_text', 'wpautop');
	# Add Markdown filter with priority 6 (same as Textile).
	add_filter('the_content', 'Markdown', 6);
	add_filter('the_excerpt', 'Markdown', 6);
	add_filter('comment_text', 'Markdown', 6);
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
	"#" => md5("#"),
	"." => md5("."),
	"!" => md5("!")
);
# Create an identical table but for escaped characters.
$md_backslash_escape_table;
foreach ($md_escape_table as $key => $char)
	$md_backslash_escape_table["\\$key"] = $char;


function Markdown($text) {
#
# Main function. The order in which other subs are called here is
# essential. Link and image substitutions need to happen before
# _EscapeSpecialChars(), so that any *'s or _'s in the <a>
# and <img> tags get encoded.
#
	# Clear the global hashes. If we don't clear these, you get conflicts
	# from other articles when generating a page which contains more than
	# one article (e.g. an index page that shows the N most recent
	# articles):
	global $md_urls, $md_titles, $md_html_blocks;
	$md_urls = array();
	$md_titles = array();
	$md_html_blocks = array();

	# Standardize line endings:
	#   DOS to Unix and Mac to Unix
	$text = str_replace(array("\r\n", "\r"), "\n", $text);

	# Make sure $text ends with a couple of newlines:
	$text .= "\n\n";
	
	# Convert all tabs to spaces.
	$text = _Detab($text);

	# Strip any lines consisting only of spaces and tabs.
	# This makes subsequent regexen easier to write, because we can
	# match consecutive blank lines with /\n+/ instead of something
	# contorted like /[ \t]*\n+/ .
	$text = preg_replace('/^[ \t]+$/m', '', $text);

	# Turn block-level HTML blocks into hash entries
	$text = _HashHTMLBlocks($text);

	# Strip link definitions, store in hashes.
	$text = _StripLinkDefinitions($text);

	# _EscapeSpecialChars() must be called very early, to get
	# backslash escapes processed.
	$text = _EscapeSpecialChars($text);

	$text = _RunBlockGamut($text);

	$text = _UnescapeSpecialChars($text);

	return $text . "\n";
}


function _StripLinkDefinitions($text) {
#
# Strips link definitions from text, stores the URLs and titles in
# hash references.
#
	# Link defs are in the form: ^[id]: url "optional title"
	$text = preg_replace_callback('{
						^[ \t]*\[(.+)\]:	# id = $1
						  [ \t]*
						  \n?				# maybe *one* newline
						  [ \t]*
						<?(\S+?)>?			# url = $2
						  [ \t]*
						  \n?				# maybe one newline
						  [ \t]*
						(?:
							# Todo: Titles are delimited by "quotes" or (parens).
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
		$md_titles[$link_id] = htmlentities($matches[3]);
	return ''; # String that will replace the block
}


function _HashHTMLBlocks($text) {
	# Hashify HTML blocks:
	# We only want to do this for block-level HTML tags, such as headers,
	# lists, and tables. That's because we still want to wrap <p>s around
	# "paragraphs" that are wrapped in non-block-level tags, such as anchors,
	# phrase emphasis, and spans. The list of tags we're looking for is
	# hard-coded:
	$block_tags_a = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|math|ins|del';
	$block_tags_b = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|script|math';
	
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
	$text = preg_replace_callback("{
				(						# save in $1
					^					# start of line  (with /m)
					<($block_tags_a)	# start tag = $2
					\\b					# word break
					(.*\\n)*?			# any number of lines, minimally matching
					</\\2>				# the matching end tag
					[ \\t]*				# trailing spaces/tabs
					(?=\\n+|\\Z)	# followed by a newline or end of document
				)
		}xm",
		'_HashHTMLBlocks_callback',
		$text);

	#
	# Now match more liberally, simply from `\n<tag>` to `</tag>\n`
	#
	$text = preg_replace_callback("{
				(						# save in $1
					^					# start of line  (with /m)
					<($block_tags_b)	# start tag = $2
					\\b					# word break
					(.*\\n)*?			# any number of lines, minimally matching
					.*</\\2>				# the matching end tag
					[ \\t]*				# trailing spaces/tabs
					(?=\\n+|\\Z)	# followed by a newline or end of document
				)
		}xm",
		'_HashHTMLBlocks_callback',
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
					[ \t]*
					<(hr)				# start tag = $2
					\b					# word break
					([^<>])*?			# 
					/?>					# the matching end tag
					(?=\n{2,}|\Z)		# followed by a blank line or end of document
				)
		}x',
		'_HashHTMLBlocks_callback',
		$text);

	return $text;
}
function _HashHTMLBlocks_callback($matches) {
	global $md_html_blocks;
	$text = $matches[1];
	$key = md5($text);
	$md_html_blocks[$key] = $text;
	return "\n\n$key\n\n"; # String that will replace the block
}


function _RunBlockGamut($text) {
#
# These are all the transformations that form block-level
# tags like paragraphs, headers, and list items.
#
	global $md_empty_element_suffix;
	
	$text = _DoHeaders($text);

	# Do Horizontal Rules:
	$text = preg_replace(
		array('/^( ?\* ?){3,}$/m',
			  '/^( ?- ?){3,}$/m',
			  '/^( ?_ ?){3,}$/m'),
		"\n<hr$md_empty_element_suffix\n", 
		$text);

	$text = _DoLists($text);

	$text = _DoCodeBlocks($text);

	$text = _DoBlockQuotes($text);

	# Make links out of things like `<http://example.com/>`
	$text = _DoAutoLinks($text);

	# We already ran _HashHTMLBlocks() before, in Markdown(), but that
	# was to escape raw HTML in the original Markdown source. This time,
	# we're escaping the markup we've just created, so that we don't wrap
	# <p> tags around block-level tags.
	$text = _HashHTMLBlocks($text);

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

	# Fix unencoded ampersands and <'s:
	$text = _EncodeAmpsAndAngles($text);

	# Process anchor and image tags. Images must come first,
	# because ![foo][f] looks like an anchor.
	$text = _DoImages($text);
	$text = _DoAnchors($text);


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
			<?(.+?)>?	# href = $3
			[ \\t]*
			(			# title = $4
			  (['\"])	# quote char = $5
			  .*?
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
	$whole_match = $matches[1];
	$link_text   = $matches[2];
	$url	  		= $matches[3];
	$title		= isset($matches[4]) ? $matches[4] : '';    // Moodle mod

	# We've got to encode these to avoid conflicting with italics/bold.
	$url = str_replace(array('*', '_'),
					   array($md_escape_table['*'], $md_escape_table['_']), 
					   $url);
	$result = "<a href=\"$url\"";
	if ($title) {
		$title = str_replace(array('*', '_'),
						     array($md_escape_table['*'], $md_escape_table['_']),
						     $title);
		$result .=  " title=$title";
	}
	$result .= ">$link_text</a>";

	return $result;
}


function _DoImages($text) {
#
# Turn Markdown image shortcuts into <img> tags.
#
	#
	# First, handle reference-style labeled images: ![alt text][id]
	#
	$text = preg_replace_callback('{
		(				# wrap whole match in $1
		  !\[
		    (.*?)		# alt text = $2
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

	$text = preg_replace_callback("{
		(				# wrap whole match in $1
		  !\\[
			(.*?)		# alt text = $2
		  \\]
		  \\(			# literal paren
			[ \\t]*
			<?(\S+?)>?	# src url = $3
			[ \\t]*
			(			# $4
			  (['\"])	# quote char = $5
			  (.*?)		# title = $6
			  \\5		# matching quote
			  [ \\t]*
			)?			# title is optional
		  \\)
		)
		}xs",
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
	$whole_match = $matches[1];
	$alt_text    = $matches[2];
	$url	  		= $matches[3];
	$title		= $matches[6];

    $alt_text = str_replace('"', '&quot;', $alt_text);
    $title = str_replace('"', '&quot;', $title);
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
		array("/(.+)[ \t]*\n=+[ \t]*\n+/e",
			  "/(.+)[ \t]*\n-+[ \t]*\n+/e"),
		array("'<h1>'._RunSpanGamut(_UnslashQuotes('\\1')).'</h1>\n\n'",
			  "'<h2>'._RunSpanGamut(_UnslashQuotes('\\1')).'</h2>\n\n'"),
		$text);

	# atx-style headers:
	#	# Header 1
	#	## Header 2
	#	## Header 2 with closing hashes ##
	#	...
	#	###### Header 6
	#
	$text = preg_replace("{
			^(\\#{1,6})	# $1 = string of #'s
			[ \\t]*
			(.+?)		# $2 = Header text
			[ \\t]*
			\\#*			# optional closing #'s (not counted)
			\\n+
		}xme",
		"'<h'.strlen('\\1').'>'._RunSpanGamut(_UnslashQuotes('\\2')).'</h'.strlen('\\1').'>\n\n'",
		$text);

	return $text;
}


function _DoLists($text) {
#
# Form HTML ordered (numbered) and unordered (bulleted) lists.
#
	global $md_tab_width;
	$less_than_tab = $md_tab_width - 1;

	$text = preg_replace_callback("{
			(
			  (
			    ^[ ]{0,$less_than_tab}
			    (\\*|\\d+[.])
			    [ \\t]+
			  )
			  (?s:.+?)
			  (
			      \\z
			    |
				  \\n{2,}
				  (?=\\S)
				  (?![ \\t]* (\\*|\\d+[.]) [ \\t]+)
			  )
			)
		}xm",
		'_DoLists_callback', $text);

	return $text;
}
function _DoLists_callback($matches) {
	$list_type = ($matches[3] == "*") ? "ul" : "ol";
	$list = $matches[1];
	# Turn double returns into triple returns, so that we can make a
	# paragraph for the last item in a list, if necessary:
	$list = preg_replace("/\n{2,}/", "\n\n\n", $list);
	$result = _ProcessListItems($list);
	$result = "<$list_type>\n" . $result . "</$list_type>\n";
	return $result;
}


function _ProcessListItems($list_str) {
	# trim trailing blank lines:
	$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

	$list_str = preg_replace_callback('{
		(\n)?							# leading line = $1
		(^[ \t]*)						# leading whitespace = $2
		(\*|\d+[.]) [ \t]+				# list marker = $3
		((?s:.+?)						# list item text   = $4
		(\n{1,2}))
		(?= \n* (\z | \2 (\*|\d+[.]) [ \t]+))
		}xm',
		'_ProcessListItems_callback', $list_str);

	return $list_str;
}
function _ProcessListItems_callback($matches) {
	$item = $matches[4];
	$leading_line = $matches[1];
	$leading_space = $matches[2];

	if ($leading_line || preg_match('/\n{2,}/', $item)) {
		$item = _RunBlockGamut(_Outdent($item));
		#$item =~ s/\n+/\n/g;
	}
	else {
		# Recursion for sub-lists:
		$item = _DoLists(_Outdent($item));
		$item = rtrim($item, "\n");
		$item = _RunSpanGamut($item);
	}

	return "<li>" . $item . "</li>\n";
}


function _DoCodeBlocks($text) {
#
#	Process Markdown `<pre><code>` blocks.
#	
	global $md_tab_width;
	$text = preg_replace_callback("{
			(.?)			# $1 = preceding character
			(:)				# $2 = colon delimiter
			(\\n+)			# $3 = newlines after colon
			(	            # $4 = the code block -- one or more lines, starting with a space/tab
			  (?:
			    (?:[ ]\{$md_tab_width} | \\t)  # Lines must start with a tab or a tab-width of spaces
			    .*\\n+
			  )+
			)
			((?=^[ ]{0,$md_tab_width}\\S)|\\Z)	# Lookahead for non-space at line-start, or end of doc
		}xm",
		'_DoCodeBlocks_callback', $text);

	return $text;
}
function _DoCodeBlocks_callback($matches) {
	$prevchar  = $matches[1];
	$newlines  = $matches[2];
	$codeblock = $matches[4];
	
	#
	# Check the preceding character before the ":". If it's not
	# whitespace, then the ":" remains; if it is whitespace,
	# the ":" disappears completely, along with the space character.
	#
	$prefix = "";
	if (!(preg_match('/\s/', $prevchar) || ($prevchar == ""))) {
			$prefix = "$prevchar:";
	}
	$codeblock = _EncodeCode(_Outdent($codeblock));
	$codeblock = _Detab($codeblock);
	# trim leading newlines and trailing whitespace
	$codeblock = preg_replace(array('/\A\n+/', '/\s+\z/'), '', $codeblock);
	
	$result = $prefix . "\n\n<pre><code>" . $codeblock . "\n</code></pre>\n\n";

	return $result;
}


function _DoCodeSpans($text) {
#
# 	*	Backtick quotes are used for <code></code> spans.
# 
# 	*	You can use multiple backticks as the delimiters if you want to
# 		include literal backticks in the code span. So, this input:
#     
#         Just type ``foo `bar` baz`` at the prompt.
#     
#     	Will translate to:
#     
#         <p>Just type <code>foo `bar` baz</code> at the prompt.</p>
#     
#		There's no arbitrary limit to the number of backticks you
#		can use as delimters. If you need three consecutive backticks
#		in your code, use four for delimiters, etc.
#
#	*	You can use spaces to get literal backticks at the edges:
#     
#         ... type `` `bar` `` ...
#     
#     	Turns to:
#     
#         ... type <code>`bar`</code> ...
#
	$text = preg_replace_callback("@
			(`+)		# Opening run of `
			(.+?)		# the code block
			(?<!`)
			\\1
			(?!`)
		@xs",
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
	$text = preg_replace('{ (\*\*|__) (?=\S) (.+?) (?<=\S) \1 }sx',
		'<strong>\2</strong>', $text);
	# Then <em>:
	$text = preg_replace('{ (\*|_) (?=\S) (.+?) (?<=\S) \1 }sx',
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
	$bq = preg_replace('/^/m', "\t", $bq);
	
	return "<blockquote>\n$bq\n</blockquote>\n\n";
}


function _FormParagraphs($text) {
#
#	Params:
#		$text - string to process with html <p> tags
#
	global $md_html_blocks;

	# Strip leading and trailing lines:
	$text = preg_replace(array('/\A\n+/', '/\n+\z/'), '', $text);
	
	$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);
	$count = count($grafs);

	#
	# Wrap <p> tags.
	#
	foreach ($grafs as $key => $value) {
		if (!isset( $md_html_blocks[$value] )) {
			$value = _RunSpanGamut($value);
			$value = preg_replace('/^([ \t]*)/', '<p>', $value);
			$value .= "</p>";
			$grafs[$key] = $value;
		}
	}

	#
	# Unhashify HTML blocks
	#
	foreach ($grafs as $key => $value) {
		if (isset( $md_html_blocks[$value] )) {
			$grafs[$key] = $md_html_blocks[$value];
		}
	}

	return implode("\n\n", $grafs);
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
#   Parameter:  String.
#   Returns:    The string, with after processing the following backslash
#               escape sequences.
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
#       x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;">&#102;&#111;&#111;
#       &#64;&#101;x&#x61;&#109;&#x70;&#108;&#x65;&#x2E;&#99;&#111;&#109;</a>
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


# Tokenize_HTML is shared between PHP Markdown and PHP SmartyPants.
# We only define it if it is not already defined.
if (!function_exists('_TokenizeHTML')) {
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

		$depth = 6;
		$nested_tags = str_repeat('(?:<[a-z\/!$](?:[^<>]|',$depth)
					   .str_repeat(')*>)', $depth);
		$match = "(?s:<!(?:--.*?--\s*)+>)|".  # comment
				 "(?s:<\?.*?\?>)|".         # processing instruction
				 "$nested_tags";            # nested tags
                 
        $parts = preg_split("/($match)/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        foreach ($parts as $part) {
            if (++$index % 2 && $part != '') 
                array_push($tokens, array('text', $part));
            else
                array_push($tokens, array('tag', $part));
        }
        
		return $tokens;
	}
}

function _Outdent($text) {
#
# Remove one level of line-leading tabs or spaces
#
	global $md_tab_width;
	return preg_replace("/^(\\t|[ ]{1,$md_tab_width})/m", "", $text);
}


function _Detab($text) {
#
# Inspired from a post by Bart Lateur:
# <http://www.nntp.perl.org/group/perl.macperl.anyperl/154>
#
	global $md_tab_width;
	$text = preg_replace(
		"/(.*?)\t/e",
		"'\\1'.str_repeat(' ', $md_tab_width - strlen('\\1') % $md_tab_width)",
		$text);
	return $text;
}


function _UnslashQuotes($text) {
#
#   This function is useful to remove automaticaly slashed double quotes
#   when using preg_replace and evaluating an expression.
#   Parameter:  String.
#   Returns:    The string with any slash-double-quote (\") sequence replaced
#               by a single double quote.
#
	return str_replace('\"', '"', $text);
}


/*

PHP Markdown
============

Description
-----------

This is a PHP translation of the original Markdown formatter written in
Perl by John Gruber.

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

1.0b9: Sun 27 Jun 2004

*	Replacing `"` with `&quot;` to fix literal quotes within img alt 
    attributes.


1.0b8: Wed 23 Jun 2004

*   In WordPress, solved a bug where PHP Markdown did not deactivate 
    the paragraph filter, converting all returns to a line break.
    The "texturize" filter was being disabled instead.

*	Added 'math' tags to block-level tag patterns in `_HashHTMLBlocks()`.
    Please disregard all the 'math'-tag related items in 1.0b7.

*	Commented out some vestigial code in `_EscapeSpecialChars()`


1.0b7: Sat 12 Jun 2004

*   Added 'math' to `$tags_to_skip` pattern, for MathML users.

*   Tweaked regex for identifying HTML entities in
    `_EncodeAmpsAndAngles()`, so as to allow for the very long entity
    names used by MathML. (Thanks to Jacques Distler for the patch.)

*   Changed the internals of `_TokenizeHTML` to lower the PHP version
    requirement to PHP 4.0.5.


1.0b6: Sun 6 Jun 2004

*   Added a WordPress plugin interface. This means that you can 
	directly put the "markdown.php" file into the "wp-content/plugins" 
	directory and then activate it from the administrative interface.
    
*   Added a Textile compatibility interface. Rename this file to 
    "classTextile.php" and it can replace Textile anywhere.

*   The title attribute of reference-style links were ignored. 
	This is now fixed.

*   Changed internal variables names so that they begin with `md_` 
    instead of `g_`. This should reduce the risk of name collision with 
    other programs.


1.0b5: Sun 2 May 2004
	
*	Workaround for supporting `<ins>` and `<del>` as block-level tags.
	This only works if the start and end tags are on lines by
	themselves.

*	Three or more underscores can now be used for horizontal rules.

*	Lines containing only whitespace are trimmed from blockquotes.

*	You can now optionally wrap URLs with angle brackets -- like so:
	`<http://example.com>` -- in link definitions and inline links and
	images.

*	`_` and `*` characters in links and images are no longer escaped
	as HTML entities. Instead, we use the ridiculous but effective MD5
	hashing trick that's used to hide these characters elsewhere. The
	end result is that the HTML output uses the literal `*` and `_`
	characters, rather than the ugly entities.

*	Passing an empty string to the Markdown function no longer creates 
	an empty paragraph.
	
*	Added a global declaration at the beginning of the file. This
	means you can now `include 'markdown.php'` from inside a function.


1.0b4.1: Sun 4 Apr 2004

*	Fixed a bug where image tags did not close.

*	Fixed a bug where brakets `[]` inside a link caused the link to be
	ignored. PHP Markdown support only 6 (!) level of brakets inside a link
	(while John's original version of Markdown in Perl support much more).


1.0b4: Sat 27 Mar 2004
	
*	First release of PHP Markdown, based on the 1.0b4 release.


Author & Contributors
---------------------

Original version by John Gruber  
<http://daringfireball.net/>

PHP translation by Michel Fortin  
<http://www.michelf.com/>

First WordPress plugin interface written by Matt Mullenweg  
<http://photomatt.net/>


Copyright and License
---------------------

Copyright (c) 2003-2004 John Gruber  
<http://daringfireball.net/>  
All rights reserved.

Copyright (c) 2004 Michel Fortin  
<http://www.michelf.com/>

Markdown is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.

Markdown is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
for more details.

*/
?>
