<?php

/***************************************************************
 * Library to convert HTML into an approximate text equivalent *
 ***************************************************************

  Version: 1.0  (with modifications)
  Copyright 2002 Mark Wilton-Jones
  License: HowToCreate script license with written permission
  URL: http://www.howtocreate.co.uk/php/
  
  For full details about the script and to get the latest version,
  please see the HowToCreate web site above.
  
  This version contains modifications for Moodle.  In each case the 
  original line has been commented out below and a new line has been
  added, so it should be easy to see what has changed.
  
  --------------------------------------------------------------

The reason this library was written was to convert HTML email contents into a text
based email content, where the rendering does not have to be as accurate as with
a text based browser. However, there must be many more uses for it.

This library attempts to deal with non-standard HTML, but may occasionally suffer
from problems with pages that are not properly written - most especially:
Tags written as <tagName attribute=somethingWithA"or'InItButNotSurroundedByQuotes>,
Closing </pre> or </textarea> tags without their corresponding opening tags,
Tags within <textarea> </textarea> tags, which will be rendered, even though they
should not be.

Conversion requires a lot of preg_replace statements, so it can be quite slow with
large HTML files.

******
To use
******

This library requires PHP 4+

To use this library, put the following line in your script before the part that needs it:
require('PATH_TO_THIS_FILE/html2text.php');

To convert HTML/PHP to text:
	$textVersion = html2text( $HTMLversion );

************
Further info
************

For the technically minded, this is the process I use for converting HTML to approx text:

REMOVE php start and end tags
REMOVE <!-- -->
ensure HTML uses entities in the right places (like inside tags) so strip_tags works properly
<STYLE|SCRIPT|OPTION>
carefully remove everything between them
strip_tags except the important ones
replace all \s that are after the start or a </pre> and before <pre> or end with a single space
</TITLE|HR>
\n          --------------------
<H1|H2|H3|H4|H5|H6|DIV|P|PRE>
\n\n
<SUP>
^
<UL|OL|BR|DL|DT|TABLE|CAPTION|TR->(TH|TD)>
\n
<LI>
\n· 
<DD>
\n\t
<TH|TD>
\t
<A|AREA href=(!javascript:&&!#)>
[LINK:hrefWithout#]
<IMG>
[IMG:alt]
<FORM>
[FORM:action]
<INPUT|TEXTAREA|BUTTON|SELECT>
[INPUT]
strip tags again, leaving nothing this time
un-htmlspecialchars
word wrap (this will also affect pre, but as this is intended for email use, I don't care)


*/

function html2text( $badStr ) {
	//remove PHP if it exists
	if( $andPHP ) { while( substr_count( $badStr, '<'.'?' ) && substr_count( $badStr, '?'.'>' ) && strpos( $badStr, '?'.'>' ) > strpos( $badStr, '<'.'?' ) ) {
		$badStr = substr( $badStr, 0, strpos( $badStr, '<'.'?' ) ) . substr( $badStr, strpos( $badStr, '?'.'>' ) + 2 ); } }
	//remove comments
	while( substr_count( $badStr, '<!--' ) && substr_count( $badStr, '-->' ) && strpos( $badStr, '-->' ) > strpos( $badStr, '<!--' ) ) {
		$badStr = substr( $badStr, 0, strpos( $badStr, '<!--' ) ) . substr( $badStr, strpos( $badStr, '-->' ) + 3 ); }
	//now make sure all HTML tags are correctly written (> not in between quotes)
	for( $x = 0, $goodStr = '', $is_open_tb = false, $is_open_sq = false, $is_open_sq = false; strlen( $chr = $badStr{$x} ); $x++ ) {
		//take each letter in turn and check if that character is permitted there
		switch( $chr ) {
			case '<':
				if( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 5 ) ) == 'style' ) {
					$badStr = substr( $badStr, 0, $x ) . substr( $badStr, strpos( strtolower( $badStr ), '</style>' ) + 7 ); $chr = '';
				} elseif( !$is_open_tb && strtolower( substr( $badStr, $x + 1, 6 ) ) == 'script' ) {
					$badStr = substr( $badStr, 0, $x ) . substr( $badStr, strpos( strtolower( $badStr ), '</script>' ) + 8 ); $chr = '';
				} elseif( !$is_open_tb ) { $is_open_tb = true; } else { $chr = '&lt;'; }
				break;
			case '>':
				if( !$is_open_tb || $is_open_dq || $is_open_sq ) { $chr = '&gt;'; } else { $is_open_tb = false; }
				break;
			case '"':
				if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_dq = true; }
				elseif( $is_open_tb && $is_open_dq && !$is_open_sq ) { $is_open_dq = false; }
				else { $chr = '&quot;'; }
				break;
			case "'":
				if( $is_open_tb && !$is_open_dq && !$is_open_sq ) { $is_open_sq = true; }
				elseif( $is_open_tb && !$is_open_dq && $is_open_sq ) { $is_open_sq = false; }
		} $goodStr .= $chr;
	}
	//now that the page is valid (I hope) for strip_tags, strip all unwanted tags
	$goodStr = strip_tags( $goodStr, '<title><hr><h1><h2><h3><h4><h5><h6><div><p><pre><sup><ul><ol><br><dl><dt><table><caption><tr><li><dd><th><td><a><area><img><form><input><textarea><button><select><option>' );
	//strip extra whitespace except between <pre> and <textarea> tags
	$badStr = preg_split( "/<\/?pre[^>]*>/i", $goodStr );
	for( $x = 0; is_string( $badStr[$x] ); $x++ ) {
		if( $x % 2 ) { $badStr[$x] = '<pre>'.$badStr[$x].'</pre>'; } else {
			$goodStr = preg_split( "/<\/?textarea[^>]*>/i", $badStr[$x] );
			for( $z = 0; is_string( $goodStr[$z] ); $z++ ) {
				if( $z % 2 ) { $goodStr[$z] = '<textarea>'.$goodStr[$z].'</textarea>'; } else {
					$goodStr[$z] = preg_replace( "/\s+/", ' ', $goodStr[$z] );
			} }
			$badStr[$x] = implode('',$goodStr);
	} }
	$goodStr = implode('',$badStr);
	//remove all options from select inputs
	$goodStr = preg_replace( "/<option[^>]*>[^<]*/i", '', $goodStr );
	//replace all tags with their text equivalents
	$goodStr = preg_replace( "/<(\/title|hr)[^>]*>/i", "\n          --------------------\n", $goodStr );
	$goodStr = preg_replace( "/<(h|div|p)[^>]*>/i", "\n\n", $goodStr );
	$goodStr = preg_replace( "/<sup[^>]*>/i", '^', $goodStr );
	$goodStr = preg_replace( "/<(ul|ol|br|dl|dt|table|caption|\/textarea|tr[^>]*>\s*<(td|th))[^>]*>/i", "\n", $goodStr );
	$goodStr = preg_replace( "/<li[^>]*>/i", "\n· ", $goodStr );
	$goodStr = preg_replace( "/<dd[^>]*>/i", "\n\t", $goodStr );
	$goodStr = preg_replace( "/<(th|td)[^>]*>/i", "\t", $goodStr );
	///$goodStr = preg_replace( "/<a[^>]* href=(\"((?!\"|#|javascript:)[^\"#]*)(\"|#)|'((?!'|#|javascript:)[^'#]*)('|#)|((?!'|\"|>|#|javascript:)[^#\"'> ]*))[^>]*>/i", "[LINK: $2$4$6] ", $goodStr ); /// Moodle
	$goodStr = preg_replace( "/<a[^>]* href=(\"((?!\"|#|javascript:)[^\"#]*)(\"|#)|'((?!'|#|javascript:)[^'#]*)('|#)|((?!'|\"|>|#|javascript:)[^#\"'> ]*))[^>]*>/i", "[$2$4$6] ", $goodStr );
	/// $goodStr = preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "[IMAGE: $2$3$4] ", $goodStr );/// Moodle
	$goodStr = preg_replace( "/<img[^>]* alt=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "{$2$3$4} ", $goodStr );
	$goodStr = preg_replace( "/<form[^>]* action=(\"([^\"]+)\"|'([^']+)'|([^\"'> ]+))[^>]*>/i", "\n[FORM: $2$3$4] ", $goodStr );
	$goodStr = preg_replace( "/<(input|textarea|button|select)[^>]*>/i", "[INPUT] ", $goodStr );
	//strip all remaining tags (mostly closing tags)
	$goodStr = strip_tags( $goodStr );
	//convert HTML entities
	$goodStr = strtr( $goodStr, array_flip( get_html_translation_table( HTML_ENTITIES ) ) );
	preg_replace( "/&#(\d+);/me", "chr('$1')", $goodStr );
	//wordwrap
	///$goodStr = wordwrap($goodStr);    /// Moodle
	$goodStr = wordwrap($goodStr, 70);
	//make sure there are no more than 3 linebreaks in a row and trim whitespace
	return preg_replace( "/^\n*|\n*$/", '', preg_replace( "/[ \t]+(\n|$)/", "$1", preg_replace( "/\n(\s*\n){2}/", "\n\n\n", preg_replace( "/\r\n?|\f/", "\n", str_replace( chr(160), ' ', $goodStr ) ) ) ) );
}

?>
