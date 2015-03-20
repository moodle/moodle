<?php
#
# Markdown Extra  -  A text-to-HTML conversion tool for web writers
#
# PHP Markdown Extra
# Copyright (c) 2004-2015 Michel Fortin  
# <https://michelf.ca/projects/php-markdown/>
#
# Original Markdown
# Copyright (c) 2004-2006 John Gruber  
# <http://daringfireball.net/projects/markdown/>
#
namespace Michelf;


#
# Markdown Extra Parser Class
#

class MarkdownExtra extends \Michelf\Markdown {

	### Configuration Variables ###

	# Prefix for footnote ids.
	public $fn_id_prefix = "";
	
	# Optional title attribute for footnote links and backlinks.
	public $fn_link_title = "";
	public $fn_backlink_title = "";
	
	# Optional class attribute for footnote links and backlinks.
	public $fn_link_class = "footnote-ref";
	public $fn_backlink_class = "footnote-backref";

	# Class name for table cell alignment (%% replaced left/center/right)
	# For instance: 'go-%%' becomes 'go-left' or 'go-right' or 'go-center'
	# If empty, the align attribute is used instead of a class name.
	public $table_align_class_tmpl = '';

	# Optional class prefix for fenced code block.
	public $code_class_prefix = "";
	# Class attribute for code blocks goes on the `code` tag;
	# setting this to true will put attributes on the `pre` tag instead.
	public $code_attr_on_pre = false;
	
	# Predefined abbreviations.
	public $predef_abbr = array();

	### Parser Implementation ###

	public function __construct() {
	#
	# Constructor function. Initialize the parser object.
	#
		# Add extra escapable characters before parent constructor 
		# initialize the table.
		$this->escape_chars .= ':|';
		
		# Insert extra document, block, and span transformations. 
		# Parent constructor will do the sorting.
		$this->document_gamut += array(
			"doFencedCodeBlocks" => 5,
			"stripFootnotes"     => 15,
			"stripAbbreviations" => 25,
			"appendFootnotes"    => 50,
			);
		$this->block_gamut += array(
			"doFencedCodeBlocks" => 5,
			"doTables"           => 15,
			"doDefLists"         => 45,
			);
		$this->span_gamut += array(
			"doFootnotes"        => 5,
			"doAbbreviations"    => 70,
			);
		
		$this->enhanced_ordered_list = true;
		parent::__construct();
	}
	
	
	# Extra variables used during extra transformations.
	protected $footnotes = array();
	protected $footnotes_ordered = array();
	protected $footnotes_ref_count = array();
	protected $footnotes_numbers = array();
	protected $abbr_desciptions = array();
	protected $abbr_word_re = '';
	
	# Give the current footnote number.
	protected $footnote_counter = 1;
	
	
	protected function setup() {
	#
	# Setting up Extra-specific variables.
	#
		parent::setup();
		
		$this->footnotes = array();
		$this->footnotes_ordered = array();
		$this->footnotes_ref_count = array();
		$this->footnotes_numbers = array();
		$this->abbr_desciptions = array();
		$this->abbr_word_re = '';
		$this->footnote_counter = 1;
		
		foreach ($this->predef_abbr as $abbr_word => $abbr_desc) {
			if ($this->abbr_word_re)
				$this->abbr_word_re .= '|';
			$this->abbr_word_re .= preg_quote($abbr_word);
			$this->abbr_desciptions[$abbr_word] = trim($abbr_desc);
		}
	}
	
	protected function teardown() {
	#
	# Clearing Extra-specific variables.
	#
		$this->footnotes = array();
		$this->footnotes_ordered = array();
		$this->footnotes_ref_count = array();
		$this->footnotes_numbers = array();
		$this->abbr_desciptions = array();
		$this->abbr_word_re = '';
		
		parent::teardown();
	}
	
	
	### Extra Attribute Parser ###

	# Expression to use to catch attributes (includes the braces)
	protected $id_class_attr_catch_re = '\{((?:[ ]*[#.a-z][-_:a-zA-Z0-9=]+){1,})[ ]*\}';
	# Expression to use when parsing in a context when no capture is desired
	protected $id_class_attr_nocatch_re = '\{(?:[ ]*[#.a-z][-_:a-zA-Z0-9=]+){1,}[ ]*\}';

	protected function doExtraAttributes($tag_name, $attr, $defaultIdValue = null) {
	#
	# Parse attributes caught by the $this->id_class_attr_catch_re expression
	# and return the HTML-formatted list of attributes.
	#
	# Currently supported attributes are .class and #id.
	#
	# In addition, this method also supports supplying a default Id value,
	# which will be used to populate the id attribute in case it was not
	# overridden.
		if (empty($attr) && !$defaultIdValue) return "";
		
		# Split on components
		preg_match_all('/[#.a-z][-_:a-zA-Z0-9=]+/', $attr, $matches);
		$elements = $matches[0];

		# handle classes and ids (only first id taken into account)
		$classes = array();
		$attributes = array();
		$id = false;
		foreach ($elements as $element) {
			if ($element{0} == '.') {
				$classes[] = substr($element, 1);
			} else if ($element{0} == '#') {
				if ($id === false) $id = substr($element, 1);
			} else if (strpos($element, '=') > 0) {
				$parts = explode('=', $element, 2);
				$attributes[] = $parts[0] . '="' . $parts[1] . '"';
			}
		}

		if (!$id) $id = $defaultIdValue;

		# compose attributes as string
		$attr_str = "";
		if (!empty($id)) {
			$attr_str .= ' id="'.$this->encodeAttribute($id) .'"';
		}
		if (!empty($classes)) {
			$attr_str .= ' class="'. implode(" ", $classes) . '"';
		}
		if (!$this->no_markup && !empty($attributes)) {
			$attr_str .= ' '.implode(" ", $attributes);
		}
		return $attr_str;
	}


	protected function stripLinkDefinitions($text) {
	#
	# Strips link definitions from text, stores the URLs and titles in
	# hash references.
	#
		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: ^[id]: url "optional title"
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
					(?:[ ]* '.$this->id_class_attr_catch_re.' )?  # $5 = extra id & class attr
							(?:\n+|\Z)
			}xm',
			array($this, '_stripLinkDefinitions_callback'),
			$text);
		return $text;
	}
	protected function _stripLinkDefinitions_callback($matches) {
		$link_id = strtolower($matches[1]);
		$url = $matches[2] == '' ? $matches[3] : $matches[2];
		$this->urls[$link_id] = $url;
		$this->titles[$link_id] =& $matches[4];
		$this->ref_attr[$link_id] = $this->doExtraAttributes("", $dummy =& $matches[5]);
		return ''; # String that will replace the block
	}


	### HTML Block Parser ###
	
	# Tags that are always treated as block tags:
	protected $block_tags_re = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|form|fieldset|iframe|hr|legend|article|section|nav|aside|hgroup|header|footer|figcaption|figure';
						   
	# Tags treated as block tags only if the opening tag is alone on its line:
	protected $context_block_tags_re = 'script|noscript|style|ins|del|iframe|object|source|track|param|math|svg|canvas|audio|video';
	
	# Tags where markdown="1" default to span mode:
	protected $contain_span_tags_re = 'p|h[1-6]|li|dd|dt|td|th|legend|address';
	
	# Tags which must not have their contents modified, no matter where 
	# they appear:
	protected $clean_tags_re = 'script|style|math|svg';
	
	# Tags that do not need to be closed.
	protected $auto_close_tags_re = 'hr|img|param|source|track';
	

	protected function hashHTMLBlocks($text) {
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
	# attribute is found within a tag, _HashHTMLBlocks_InHTML calls back
	#  _HashHTMLBlocks_InMarkdown to handle the Markdown syntax within the tag.
	# These two functions are calling each other. It's recursive!
	#
		if ($this->no_markup)  return $text;

		#
		# Call the HTML-in-Markdown hasher.
		#
		list($text, ) = $this->_hashHTMLBlocks_inMarkdown($text);
		
		return $text;
	}
	protected function _hashHTMLBlocks_inMarkdown($text, $indent = 0,
										$enclosing_tag_re = '', $span = false)
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
	# *   If $enclosing_tag_re is not empty, stops at the first unmatched closing 
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
		$newline_before_re = '/(?:^\n?|\n\n)*$/';
		$newline_after_re = 
			'{
				^						# Start of text following the tag.
				(?>[ ]*<!--.*?-->)?		# Optional comment.
				[ ]*\n					# Must be followed by newline.
			}xs';
		
		# Regex to match any tag.
		$block_tag_re =
			'{
				(					# $2: Capture whole tag.
					</?					# Any opening or closing tag.
						(?>				# Tag name.
							'.$this->block_tags_re.'			|
							'.$this->context_block_tags_re.'	|
							'.$this->clean_tags_re.'        	|
							(?!\s)'.$enclosing_tag_re.'
						)
						(?:
							(?=[\s"\'/a-zA-Z0-9])	# Allowed characters after tag name.
							(?>
								".*?"		|	# Double quotes (can contain `>`)
								\'.*?\'   	|	# Single quotes (can contain `>`)
								.+?				# Anything but quotes and `>`.
							)*?
						)?
					>					# End of tag.
				|
					<!--    .*?     -->	# HTML Comment
				|
					<\?.*?\?> | <%.*?%>	# Processing instruction
				|
					<!\[CDATA\[.*?\]\]>	# CData Block
				'. ( !$span ? ' # If not in span.
				|
					# Indented code block
					(?: ^[ ]*\n | ^ | \n[ ]*\n )
					[ ]{'.($indent+4).'}[^\n]* \n
					(?>
						(?: [ ]{'.($indent+4).'}[^\n]* | [ ]* ) \n
					)*
				|
					# Fenced code block marker
					(?<= ^ | \n )
					[ ]{0,'.($indent+3).'}(?:~{3,}|`{3,})
									[ ]*
					(?:
					\.?[-_:a-zA-Z0-9]+ # standalone class name
					|
						'.$this->id_class_attr_nocatch_re.' # extra attributes
					)?
					[ ]*
					(?= \n )
				' : '' ). ' # End (if not is span).
				|
					# Code span marker
					# Note, this regex needs to go after backtick fenced
					# code blocks but it should also be kept outside of the
					# "if not in span" condition adding backticks to the parser
					`+
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
			$parts = preg_split($block_tag_re, $text, 2, 
								PREG_SPLIT_DELIM_CAPTURE);
			
			# If in Markdown span mode, add a empty-string span-level hash 
			# after each newline to prevent triggering any block element.
			if ($span) {
				$void = $this->hashPart("", ':');
				$newline = "$void\n";
				$parts[0] = $void . str_replace("\n", $newline, $parts[0]) . $void;
			}
			
			$parsed .= $parts[0]; # Text before current tag.
			
			# If end of $text has been reached. Stop loop.
			if (count($parts) < 3) {
				$text = "";
				break;
			}
			
			$tag  = $parts[1]; # Tag to handle.
			$text = $parts[2]; # Remaining text after current tag.
			$tag_re = preg_quote($tag); # For use in a regular expression.
			
			#
			# Check for: Fenced code block marker.
			# Note: need to recheck the whole tag to disambiguate backtick
			# fences from code spans
			#
			if (preg_match('{^\n?([ ]{0,'.($indent+3).'})(~{3,}|`{3,})[ ]*(?:\.?[-_:a-zA-Z0-9]+|'.$this->id_class_attr_nocatch_re.')?[ ]*\n?$}', $tag, $capture)) {
				# Fenced code block marker: find matching end marker.
				$fence_indent = strlen($capture[1]); # use captured indent in re
				$fence_re = $capture[2]; # use captured fence in re
				if (preg_match('{^(?>.*\n)*?[ ]{'.($fence_indent).'}'.$fence_re.'[ ]*(?:\n|$)}', $text,
					$matches)) 
				{
					# End marker found: pass text unchanged until marker.
					$parsed .= $tag . $matches[0];
					$text = substr($text, strlen($matches[0]));
				}
				else {
					# No end marker: just skip it.
					$parsed .= $tag;
				}
			}
			#
			# Check for: Indented code block.
			#
			else if ($tag{0} == "\n" || $tag{0} == " ") {
				# Indented code block: pass it unchanged, will be handled 
				# later.
				$parsed .= $tag;
			}
			#
			# Check for: Code span marker
			# Note: need to check this after backtick fenced code blocks
			#
			else if ($tag{0} == "`") {
				# Find corresponding end marker.
				$tag_re = preg_quote($tag);
				if (preg_match('{^(?>.+?|\n(?!\n))*?(?<!`)'.$tag_re.'(?!`)}',
					$text, $matches))
				{
					# End marker found: pass text unchanged until marker.
					$parsed .= $tag . $matches[0];
					$text = substr($text, strlen($matches[0]));
				}
				else {
					# Unmatched marker: just skip it.
					$parsed .= $tag;
				}
			}
			#
			# Check for: Opening Block level tag or
			#            Opening Context Block tag (like ins and del) 
			#               used as a block tag (tag is alone on it's line).
			#
			else if (preg_match('{^<(?:'.$this->block_tags_re.')\b}', $tag) ||
				(	preg_match('{^<(?:'.$this->context_block_tags_re.')\b}', $tag) &&
					preg_match($newline_before_re, $parsed) &&
					preg_match($newline_after_re, $text)	)
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
			else if (preg_match('{^<(?:'.$this->clean_tags_re.')\b}', $tag) ||
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
			else if ($enclosing_tag_re !== '' &&
				# Same name as enclosing tag.
				preg_match('{^</?(?:'.$enclosing_tag_re.')\b}', $tag))
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
	protected function _hashHTMLBlocks_inHTML($text, $hash_method, $md_attr) {
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
		$markdown_attr_re = '
			{
				\s*			# Eat whitespace before the `markdown` attribute
				markdown
				\s*=\s*
				(?>
					(["\'])		# $1: quote delimiter		
					(.*?)		# $2: attribute value
					\1			# matching delimiter	
				|
					([^\s>]*)	# $3: unquoted attribute value
				)
				()				# $4: make $3 always defined (avoid warnings)
			}xs';
		
		# Regex to match any tag.
		$tag_re = '{
				(					# $2: Capture whole tag.
					</?					# Any opening or closing tag.
						[\w:$]+			# Tag name.
						(?:
							(?=[\s"\'/a-zA-Z0-9])	# Allowed characters after tag name.
							(?>
								".*?"		|	# Double quotes (can contain `>`)
								\'.*?\'   	|	# Single quotes (can contain `>`)
								.+?				# Anything but quotes and `>`.
							)*?
						)?
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
		# (This pattern makes $base_tag_name_re safe without quoting.)
		#
		if (preg_match('/^<([\w:$]*)\b/', $text, $matches))
			$base_tag_name_re = $matches[1];

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
			$parts = preg_split($tag_re, $text, 2, PREG_SPLIT_DELIM_CAPTURE);
			
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
			if (preg_match('{^</?(?:'.$this->auto_close_tags_re.')\b}', $tag) ||
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
				if (preg_match('{^</?'.$base_tag_name_re.'\b}', $tag)) {
					if ($tag{1} == '/')						$depth--;
					else if ($tag{strlen($tag)-2} != '/')	$depth++;
				}
				
				#
				# Check for `markdown="1"` attribute and handle it.
				#
				if ($md_attr && 
					preg_match($markdown_attr_re, $tag, $attr_m) &&
					preg_match('/^1|block|span$/', $attr_m[2] . $attr_m[3]))
				{
					# Remove `markdown` attribute from opening tag.
					$tag = preg_replace($markdown_attr_re, '', $tag);
					
					# Check if text inside this tag must be parsed in span mode.
					$this->mode = $attr_m[2] . $attr_m[3];
					$span_mode = $this->mode == 'span' || $this->mode != 'block' &&
						preg_match('{^<(?:'.$this->contain_span_tags_re.')\b}', $tag);
					
					# Calculate indent before tag.
					if (preg_match('/(?:^|\n)( *?)(?! ).*?$/', $block_text, $matches)) {
						$strlen = $this->utf8_strlen;
						$indent = $strlen($matches[1], 'UTF-8');
					} else {
						$indent = 0;
					}
					
					# End preceding block with this tag.
					$block_text .= $tag;
					$parsed .= $this->$hash_method($block_text);
					
					# Get enclosing tag name for the ParseMarkdown function.
					# (This pattern makes $tag_name_re safe without quoting.)
					preg_match('/^<([\w:$]*)\b/', $tag, $matches);
					$tag_name_re = $matches[1];
					
					# Parse the content using the HTML-in-Markdown parser.
					list ($block_text, $text)
						= $this->_hashHTMLBlocks_inMarkdown($text, $indent, 
							$tag_name_re, $span_mode);
					
					# Outdent markdown text.
					if ($indent > 0) {
						$block_text = preg_replace("/^[ ]{1,$indent}/m", "", 
													$block_text);
					}
					
					# Append tag content to parsed text.
					if (!$span_mode)	$parsed .= "\n\n$block_text\n\n";
					else				$parsed .= "$block_text";
					
					# Start over with a new block.
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


	protected function hashClean($text) {
	#
	# Called whenever a tag must be hashed when a function inserts a "clean" tag
	# in $text, it passes through this function and is automaticaly escaped, 
	# blocking invalid nested overlap.
	#
		return $this->hashPart($text, 'C');
	}


	protected function doAnchors($text) {
	#
	# Turn Markdown link shortcuts into XHTML <a> tags.
	#
		if ($this->in_anchor) return $text;
		$this->in_anchor = true;
		
		#
		# First, handle reference-style links: [link text] [id]
		#
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

		#
		# Next, inline-style links: [link text](url "optional title")
		#
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
			  (?:[ ]? '.$this->id_class_attr_catch_re.' )?	 # $8 = id/class attributes
			)
			}xs',
			array($this, '_doAnchors_inline_callback'), $text);

		#
		# Last, handle reference-style shortcuts: [link text]
		# These must come last in case you've also got [link text][1]
		# or [link text](/foo)
		#
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
	protected function _doAnchors_reference_callback($matches) {
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
			$url = $this->encodeURLAttribute($url);
			
			$result = "<a href=\"$url\"";
			if ( isset( $this->titles[$link_id] ) ) {
				$title = $this->titles[$link_id];
				$title = $this->encodeAttribute($title);
				$result .=  " title=\"$title\"";
			}
			if (isset($this->ref_attr[$link_id]))
				$result .= $this->ref_attr[$link_id];
		
			$link_text = $this->runSpanGamut($link_text);
			$result .= ">$link_text</a>";
			$result = $this->hashPart($result);
		}
		else {
			$result = $whole_match;
		}
		return $result;
	}
	protected function _doAnchors_inline_callback($matches) {
		$whole_match	=  $matches[1];
		$link_text		=  $this->runSpanGamut($matches[2]);
		$url			=  $matches[3] == '' ? $matches[4] : $matches[3];
		$title			=& $matches[7];
		$attr  = $this->doExtraAttributes("a", $dummy =& $matches[8]);

		// if the URL was of the form <s p a c e s> it got caught by the HTML
		// tag parser and hashed. Need to reverse the process before using the URL.
		$unhashed = $this->unhash($url);
		if ($unhashed != $url)
			$url = preg_replace('/^<(.*)>$/', '\1', $unhashed);

		$url = $this->encodeURLAttribute($url);

		$result = "<a href=\"$url\"";
		if (isset($title)) {
			$title = $this->encodeAttribute($title);
			$result .=  " title=\"$title\"";
		}
		$result .= $attr;
		
		$link_text = $this->runSpanGamut($link_text);
		$result .= ">$link_text</a>";

		return $this->hashPart($result);
	}


	protected function doImages($text) {
	#
	# Turn Markdown image shortcuts into <img> tags.
	#
		#
		# First, handle reference-style labeled images: ![alt text][id]
		#
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

		#
		# Next, handle inline images:  ![alt text](url "optional title")
		# Don't forget: encode * and _
		#
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
			  (?:[ ]? '.$this->id_class_attr_catch_re.' )?	 # $8 = id/class attributes
			)
			}xs',
			array($this, '_doImages_inline_callback'), $text);

		return $text;
	}
	protected function _doImages_reference_callback($matches) {
		$whole_match = $matches[1];
		$alt_text    = $matches[2];
		$link_id     = strtolower($matches[3]);

		if ($link_id == "") {
			$link_id = strtolower($alt_text); # for shortcut links like ![this][].
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
			if (isset($this->ref_attr[$link_id]))
				$result .= $this->ref_attr[$link_id];
			$result .= $this->empty_element_suffix;
			$result = $this->hashPart($result);
		}
		else {
			# If there's no such link ID, leave intact:
			$result = $whole_match;
		}

		return $result;
	}
	protected function _doImages_inline_callback($matches) {
		$whole_match	= $matches[1];
		$alt_text		= $matches[2];
		$url			= $matches[3] == '' ? $matches[4] : $matches[3];
		$title			=& $matches[7];
		$attr  = $this->doExtraAttributes("img", $dummy =& $matches[8]);

		$alt_text = $this->encodeAttribute($alt_text);
		$url = $this->encodeURLAttribute($url);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($title)) {
			$title = $this->encodeAttribute($title);
			$result .=  " title=\"$title\""; # $title already quoted
		}
		$result .= $attr;
		$result .= $this->empty_element_suffix;

		return $this->hashPart($result);
	}


	protected function doHeaders($text) {
	#
	# Redefined to add id and class attribute support.
	#
		# Setext-style headers:
		#	  Header 1  {#header1}
		#	  ========
		#  
		#	  Header 2  {#header2 .class1 .class2}
		#	  --------
		#
		$text = preg_replace_callback(
			'{
				(^.+?)								# $1: Header text
				(?:[ ]+ '.$this->id_class_attr_catch_re.' )?	 # $3 = id/class attributes
				[ ]*\n(=+|-+)[ ]*\n+				# $3: Header footer
			}mx',
			array($this, '_doHeaders_callback_setext'), $text);

		# atx-style headers:
		#	# Header 1        {#header1}
		#	## Header 2       {#header2}
		#	## Header 2 with closing hashes ##  {#header3.class1.class2}
		#	...
		#	###### Header 6   {.class2}
		#
		$text = preg_replace_callback('{
				^(\#{1,6})	# $1 = string of #\'s
				[ ]*
				(.+?)		# $2 = Header text
				[ ]*
				\#*			# optional closing #\'s (not counted)
				(?:[ ]+ '.$this->id_class_attr_catch_re.' )?	 # $3 = id/class attributes
				[ ]*
				\n+
			}xm',
			array($this, '_doHeaders_callback_atx'), $text);

		return $text;
	}
	protected function _doHeaders_callback_setext($matches) {
		if ($matches[3] == '-' && preg_match('{^- }', $matches[1]))
			return $matches[0];

		$level = $matches[3]{0} == '=' ? 1 : 2;

		$defaultId = is_callable($this->header_id_func) ? call_user_func($this->header_id_func, $matches[1]) : null;

		$attr  = $this->doExtraAttributes("h$level", $dummy =& $matches[2], $defaultId);
		$block = "<h$level$attr>".$this->runSpanGamut($matches[1])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}
	protected function _doHeaders_callback_atx($matches) {
		$level = strlen($matches[1]);

		$defaultId = is_callable($this->header_id_func) ? call_user_func($this->header_id_func, $matches[2]) : null;
		$attr  = $this->doExtraAttributes("h$level", $dummy =& $matches[3], $defaultId);
		$block = "<h$level$attr>".$this->runSpanGamut($matches[2])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}


	protected function doTables($text) {
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
					(?>
						[ ]*				# Allowed whitespace.
						[|] .* \n			# Row content.
					)*
				)
				(?=\n|\Z)					# Stop at final double newline.
			}xm',
			array($this, '_doTable_leadingPipe_callback'), $text);
		
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
					(?>
						.* [|] .* \n		# Row content
					)*
				)
				(?=\n|\Z)					# Stop at final double newline.
			}xm',
			array($this, '_DoTable_callback'), $text);

		return $text;
	}
	protected function _doTable_leadingPipe_callback($matches) {
		$head		= $matches[1];
		$underline	= $matches[2];
		$content	= $matches[3];
		
		# Remove leading pipe for each row.
		$content	= preg_replace('/^ *[|]/m', '', $content);
		
		return $this->_doTable_callback(array($matches[0], $head, $underline, $content));
	}
	protected function _doTable_makeAlignAttr($alignname)
	{
		if (empty($this->table_align_class_tmpl))
			return " align=\"$alignname\"";

		$classname = str_replace('%%', $alignname, $this->table_align_class_tmpl);
		return " class=\"$classname\"";
	}
	protected function _doTable_callback($matches) {
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
			if (preg_match('/^ *-+: *$/', $s))
				$attr[$n] = $this->_doTable_makeAlignAttr('right');
			else if (preg_match('/^ *:-+: *$/', $s))
				$attr[$n] = $this->_doTable_makeAlignAttr('center');
			else if (preg_match('/^ *:-+ *$/', $s))
				$attr[$n] = $this->_doTable_makeAlignAttr('left');
			else
				$attr[$n] = '';
		}
		
		# Parsing span elements, including code spans, character escapes, 
		# and inline HTML tags, so that pipes inside those gets ignored.
		$head		= $this->parseSpan($head);
		$headers	= preg_split('/ *[|] */', $head);
		$col_count	= count($headers);
		$attr       = array_pad($attr, $col_count, '');
		
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
			# Parsing span elements, including code spans, character escapes, 
			# and inline HTML tags, so that pipes inside those gets ignored.
			$row = $this->parseSpan($row);
			
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

	
	protected function doDefLists($text) {
	#
	# Form HTML definition lists.
	#
		$less_than_tab = $this->tab_width - 1;

		# Re-usable pattern to match any entire dl list:
		$whole_list_re = '(?>
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
		)'; // mx

		$text = preg_replace_callback('{
				(?>\A\n?|(?<=\n\n))
				'.$whole_list_re.'
			}mx',
			array($this, '_doDefLists_callback'), $text);

		return $text;
	}
	protected function _doDefLists_callback($matches) {
		# Re-usable patterns to match list item bullets and number markers:
		$list = $matches[1];
		
		# Turn double returns into triple returns, so that we can make a
		# paragraph for the last item in a list, if necessary:
		$result = trim($this->processDefListItems($list));
		$result = "<dl>\n" . $result . "\n</dl>";
		return $this->hashBlock($result) . "\n\n";
	}


	protected function processDefListItems($list_str) {
	#
	#	Process the contents of a single definition list, splitting it
	#	into individual term and definition list items.
	#
		$less_than_tab = $this->tab_width - 1;
		
		# trim trailing blank lines:
		$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

		# Process definition terms.
		$list_str = preg_replace_callback('{
			(?>\A\n?|\n\n+)					# leading line
			(								# definition terms = $1
				[ ]{0,'.$less_than_tab.'}	# leading whitespace
				(?!\:[ ]|[ ])				# negative lookahead for a definition
											#   mark (colon) or more whitespace.
				(?> \S.* \n)+?				# actual term (not whitespace).	
			)			
			(?=\n?[ ]{0,3}:[ ])				# lookahead for following line feed 
											#   with a definition mark.
			}xm',
			array($this, '_processDefListItems_callback_dt'), $list_str);

		# Process actual definitions.
		$list_str = preg_replace_callback('{
			\n(\n+)?						# leading line = $1
			(								# marker space = $2
				[ ]{0,'.$less_than_tab.'}	# whitespace before colon
				\:[ ]+						# definition mark (colon)
			)
			((?s:.+?))						# definition text = $3
			(?= \n+ 						# stop at next definition mark,
				(?:							# next term or end of text
					[ ]{0,'.$less_than_tab.'} \:[ ]	|
					<dt> | \z
				)						
			)					
			}xm',
			array($this, '_processDefListItems_callback_dd'), $list_str);

		return $list_str;
	}
	protected function _processDefListItems_callback_dt($matches) {
		$terms = explode("\n", trim($matches[1]));
		$text = '';
		foreach ($terms as $term) {
			$term = $this->runSpanGamut(trim($term));
			$text .= "\n<dt>" . $term . "</dt>";
		}
		return $text . "\n";
	}
	protected function _processDefListItems_callback_dd($matches) {
		$leading_line	= $matches[1];
		$marker_space	= $matches[2];
		$def			= $matches[3];

		if ($leading_line || preg_match('/\n{2,}/', $def)) {
			# Replace marker with the appropriate whitespace indentation
			$def = str_repeat(' ', strlen($marker_space)) . $def;
			$def = $this->runBlockGamut($this->outdent($def . "\n\n"));
			$def = "\n". $def ."\n";
		}
		else {
			$def = rtrim($def);
			$def = $this->runSpanGamut($this->outdent($def));
		}

		return "\n<dd>" . $def . "</dd>\n";
	}


	protected function doFencedCodeBlocks($text) {
	#
	# Adding the fenced code block syntax to regular Markdown:
	#
	# ~~~
	# Code block
	# ~~~
	#
		$less_than_tab = $this->tab_width;
		
		$text = preg_replace_callback('{
				(?:\n|\A)
				# 1: Opening marker
				(
					(?:~{3,}|`{3,}) # 3 or more tildes/backticks.
				)
				[ ]*
				(?:
					\.?([-_:a-zA-Z0-9]+) # 2: standalone class name
				|
					'.$this->id_class_attr_catch_re.' # 3: Extra attributes
				)?
				[ ]* \n # Whitespace and newline following marker.
				
				# 4: Content
				(
					(?>
						(?!\1 [ ]* \n)	# Not a closing marker.
						.*\n+
					)+
				)
				
				# Closing marker.
				\1 [ ]* (?= \n )
			}xm',
			array($this, '_doFencedCodeBlocks_callback'), $text);

		return $text;
	}
	protected function _doFencedCodeBlocks_callback($matches) {
		$classname =& $matches[2];
		$attrs     =& $matches[3];
		$codeblock = $matches[4];
		$codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);
		$codeblock = preg_replace_callback('/^\n+/',
			array($this, '_doFencedCodeBlocks_newlines'), $codeblock);

		if ($classname != "") {
			if ($classname{0} == '.')
				$classname = substr($classname, 1);
			$attr_str = ' class="'.$this->code_class_prefix.$classname.'"';
		} else {
			$attr_str = $this->doExtraAttributes($this->code_attr_on_pre ? "pre" : "code", $attrs);
		}
		$pre_attr_str  = $this->code_attr_on_pre ? $attr_str : '';
		$code_attr_str = $this->code_attr_on_pre ? '' : $attr_str;
		$codeblock  = "<pre$pre_attr_str><code$code_attr_str>$codeblock</code></pre>";
		
		return "\n\n".$this->hashBlock($codeblock)."\n\n";
	}
	protected function _doFencedCodeBlocks_newlines($matches) {
		return str_repeat("<br$this->empty_element_suffix", 
			strlen($matches[0]));
	}


	#
	# Redefining emphasis markers so that emphasis by underscore does not
	# work in the middle of a word.
	#
	protected $em_relist = array(
		''  => '(?:(?<!\*)\*(?!\*)|(?<![a-zA-Z0-9_])_(?!_))(?![\.,:;]?\s)',
		'*' => '(?<![\s*])\*(?!\*)',
		'_' => '(?<![\s_])_(?![a-zA-Z0-9_])',
		);
	protected $strong_relist = array(
		''   => '(?:(?<!\*)\*\*(?!\*)|(?<![a-zA-Z0-9_])__(?!_))(?![\.,:;]?\s)',
		'**' => '(?<![\s*])\*\*(?!\*)',
		'__' => '(?<![\s_])__(?![a-zA-Z0-9_])',
		);
	protected $em_strong_relist = array(
		''    => '(?:(?<!\*)\*\*\*(?!\*)|(?<![a-zA-Z0-9_])___(?!_))(?![\.,:;]?\s)',
		'***' => '(?<![\s*])\*\*\*(?!\*)',
		'___' => '(?<![\s_])___(?![a-zA-Z0-9_])',
		);


	protected function formParagraphs($text) {
	#
	#	Params:
	#		$text - string to process with html <p> tags
	#
		# Strip leading and trailing lines:
		$text = preg_replace('/\A\n+|\n+\z/', '', $text);
		
		$grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

		#
		# Wrap <p> tags and unhashify HTML blocks
		#
		foreach ($grafs as $key => $value) {
			$value = trim($this->runSpanGamut($value));
			
			# Check if this should be enclosed in a paragraph.
			# Clean tag hashes & block tag hashes are left alone.
			$is_p = !preg_match('/^B\x1A[0-9]+B|^C\x1A[0-9]+C$/', $value);
			
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
	
	protected function stripFootnotes($text) {
	#
	# Strips link definitions from text, stores the URLs and titles in
	# hash references.
	#
		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: [^id]: url "optional title"
		$text = preg_replace_callback('{
			^[ ]{0,'.$less_than_tab.'}\[\^(.+?)\][ ]?:	# note_id = $1
			  [ ]*
			  \n?					# maybe *one* newline
			(						# text = $2 (no blank lines allowed)
				(?:					
					.+				# actual text
				|
					\n				# newlines but 
					(?!\[.+?\][ ]?:\s)# negative lookahead for footnote or link definition marker.
					(?!\n+[ ]{0,3}\S)# ensure line is not blank and followed 
									# by non-indented content
				)*
			)		
			}xm',
			array($this, '_stripFootnotes_callback'),
			$text);
		return $text;
	}
	protected function _stripFootnotes_callback($matches) {
		$note_id = $this->fn_id_prefix . $matches[1];
		$this->footnotes[$note_id] = $this->outdent($matches[2]);
		return ''; # String that will replace the block
	}


	protected function doFootnotes($text) {
	#
	# Replace footnote references in $text [^id] with a special text-token 
	# which will be replaced by the actual footnote marker in appendFootnotes.
	#
		if (!$this->in_anchor) {
			$text = preg_replace('{\[\^(.+?)\]}', "F\x1Afn:\\1\x1A:", $text);
		}
		return $text;
	}

	
	protected function appendFootnotes($text) {
	#
	# Append footnote list to text.
	#
		$text = preg_replace_callback('{F\x1Afn:(.*?)\x1A:}', 
			array($this, '_appendFootnotes_callback'), $text);
	
		if (!empty($this->footnotes_ordered)) {
			$text .= "\n\n";
			$text .= "<div class=\"footnotes\">\n";
			$text .= "<hr". $this->empty_element_suffix ."\n";
			$text .= "<ol>\n\n";

			$attr = "";
			if ($this->fn_backlink_class != "") {
				$class = $this->fn_backlink_class;
				$class = $this->encodeAttribute($class);
				$attr .= " class=\"$class\"";
			}
			if ($this->fn_backlink_title != "") {
				$title = $this->fn_backlink_title;
				$title = $this->encodeAttribute($title);
				$attr .= " title=\"$title\"";
			}
			$num = 0;
			
			while (!empty($this->footnotes_ordered)) {
				$footnote = reset($this->footnotes_ordered);
				$note_id = key($this->footnotes_ordered);
				unset($this->footnotes_ordered[$note_id]);
				$ref_count = $this->footnotes_ref_count[$note_id];
				unset($this->footnotes_ref_count[$note_id]);
				unset($this->footnotes[$note_id]);
				
				$footnote .= "\n"; # Need to append newline before parsing.
				$footnote = $this->runBlockGamut("$footnote\n");				
				$footnote = preg_replace_callback('{F\x1Afn:(.*?)\x1A:}', 
					array($this, '_appendFootnotes_callback'), $footnote);
				
				$attr = str_replace("%%", ++$num, $attr);
				$note_id = $this->encodeAttribute($note_id);

				# Prepare backlink, multiple backlinks if multiple references
				$backlink = "<a href=\"#fnref:$note_id\"$attr>&#8617;</a>";
				for ($ref_num = 2; $ref_num <= $ref_count; ++$ref_num) {
					$backlink .= " <a href=\"#fnref$ref_num:$note_id\"$attr>&#8617;</a>";
				}
				# Add backlink to last paragraph; create new paragraph if needed.
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
		}
		return $text;
	}
	protected function _appendFootnotes_callback($matches) {
		$node_id = $this->fn_id_prefix . $matches[1];
		
		# Create footnote marker only if it has a corresponding footnote *and*
		# the footnote hasn't been used by another marker.
		if (isset($this->footnotes[$node_id])) {
			$num =& $this->footnotes_numbers[$node_id];
			if (!isset($num)) {
				# Transfer footnote content to the ordered list and give it its
				# number
				$this->footnotes_ordered[$node_id] = $this->footnotes[$node_id];
				$this->footnotes_ref_count[$node_id] = 1;
				$num = $this->footnote_counter++;
				$ref_count_mark = '';
			} else {
				$ref_count_mark = $this->footnotes_ref_count[$node_id] += 1;
			}

			$attr = "";
			if ($this->fn_link_class != "") {
				$class = $this->fn_link_class;
				$class = $this->encodeAttribute($class);
				$attr .= " class=\"$class\"";
			}
			if ($this->fn_link_title != "") {
				$title = $this->fn_link_title;
				$title = $this->encodeAttribute($title);
				$attr .= " title=\"$title\"";
			}
			
			$attr = str_replace("%%", $num, $attr);
			$node_id = $this->encodeAttribute($node_id);
			
			return
				"<sup id=\"fnref$ref_count_mark:$node_id\">".
				"<a href=\"#fn:$node_id\"$attr>$num</a>".
				"</sup>";
		}
		
		return "[^".$matches[1]."]";
	}
		
	
	### Abbreviations ###
	
	protected function stripAbbreviations($text) {
	#
	# Strips abbreviations from text, stores titles in hash references.
	#
		$less_than_tab = $this->tab_width - 1;

		# Link defs are in the form: [id]*: url "optional title"
		$text = preg_replace_callback('{
			^[ ]{0,'.$less_than_tab.'}\*\[(.+?)\][ ]?:	# abbr_id = $1
			(.*)					# text = $2 (no blank lines allowed)	
			}xm',
			array($this, '_stripAbbreviations_callback'),
			$text);
		return $text;
	}
	protected function _stripAbbreviations_callback($matches) {
		$abbr_word = $matches[1];
		$abbr_desc = $matches[2];
		if ($this->abbr_word_re)
			$this->abbr_word_re .= '|';
		$this->abbr_word_re .= preg_quote($abbr_word);
		$this->abbr_desciptions[$abbr_word] = trim($abbr_desc);
		return ''; # String that will replace the block
	}
	
	
	protected function doAbbreviations($text) {
	#
	# Find defined abbreviations in text and wrap them in <abbr> elements.
	#
		if ($this->abbr_word_re) {
			// cannot use the /x modifier because abbr_word_re may 
			// contain significant spaces:
			$text = preg_replace_callback('{'.
				'(?<![\w\x1A])'.
				'(?:'.$this->abbr_word_re.')'.
				'(?![\w\x1A])'.
				'}', 
				array($this, '_doAbbreviations_callback'), $text);
		}
		return $text;
	}
	protected function _doAbbreviations_callback($matches) {
		$abbr = $matches[0];
		if (isset($this->abbr_desciptions[$abbr])) {
			$desc = $this->abbr_desciptions[$abbr];
			if (empty($desc)) {
				return $this->hashPart("<abbr>$abbr</abbr>");
			} else {
				$desc = $this->encodeAttribute($desc);
				return $this->hashPart("<abbr title=\"$desc\">$abbr</abbr>");
			}
		} else {
			return $matches[0];
		}
	}
}
