<?php

/**
 * Copyright (c) 2008, David R. Nadeau, NadeauSoftware.com.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *	* Redistributions of source code must retain the above copyright
 *	  notice, this list of conditions and the following disclaimer.
 *
 *	* Redistributions in binary form must reproduce the above
 *	  copyright notice, this list of conditions and the following
 *	  disclaimer in the documentation and/or other materials provided
 *	  with the distribution.
 *
 *	* Neither the names of David R. Nadeau or NadeauSoftware.com, nor
 *	  the names of its contributors may be used to endorse or promote
 *	  products derived from this software without specific prior
 *	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY
 * WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
 * OF SUCH DAMAGE.
 */

/*
 * This is a BSD License approved by the Open Source Initiative (OSI).
 * See:  http://www.opensource.org/licenses/bsd-license.php
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Combine a base URL and a relative URL to produce a new
 * absolute URL.  The base URL is often the URL of a page,
 * and the relative URL is a URL embedded on that page.
 *
 * This function implements the "absolutize" algorithm from
 * the RFC3986 specification for URLs.
 *
 * This function supports multi-byte characters with the UTF-8 encoding,
 * per the URL specification.
 *
 * Parameters:
 * 	baseUrl		the absolute base URL.
 *
 * 	url		the relative URL to convert.
 *
 * Return values:
 * 	An absolute URL that combines parts of the base and relative
 * 	URLs, or FALSE if the base URL is not absolute or if either
 * 	URL cannot be parsed.
 */
function url_to_absolute( $baseUrl, $relativeUrl )
{
	// If relative URL has a scheme, clean path and return.
	$r = split_url( $relativeUrl );
	if ( $r === FALSE )
		return FALSE;
	if ( !empty( $r['scheme'] ) )
	{
		if ( !empty( $r['path'] ) && $r['path'][0] == '/' )
			$r['path'] = url_remove_dot_segments( $r['path'] );
		return join_url( $r );
	}

	// Make sure the base URL is absolute.
	$b = split_url( $baseUrl );
	if ( $b === FALSE || empty( $b['scheme'] ) || empty( $b['host'] ) )
		return FALSE;
	$r['scheme'] = $b['scheme'];
    if (empty($b['path'])) {
        $b['path'] = '';
    }

	// If relative URL has an authority, clean path and return.
	if ( isset( $r['host'] ) )
	{
		if ( !empty( $r['path'] ) )
			$r['path'] = url_remove_dot_segments( $r['path'] );
		return join_url( $r );
	}
	unset( $r['port'] );
	unset( $r['user'] );
	unset( $r['pass'] );

	// Copy base authority.
	$r['host'] = $b['host'];
	if ( isset( $b['port'] ) ) $r['port'] = $b['port'];
	if ( isset( $b['user'] ) ) $r['user'] = $b['user'];
	if ( isset( $b['pass'] ) ) $r['pass'] = $b['pass'];

	// If relative URL has no path, use base path
	if ( empty( $r['path'] ) )
	{
		if ( !empty( $b['path'] ) )
			$r['path'] = $b['path'];
		if ( !isset( $r['query'] ) && isset( $b['query'] ) )
			$r['query'] = $b['query'];
		return join_url( $r );
	}

	// If relative URL path doesn't start with /, merge with base path.
	if ($r['path'][0] != '/') {
		$base = textlib::strrchr($b['path'], '/', TRUE);
		if ($base === FALSE) {
			$base = '';
		}
		$r['path'] = $base . '/' . $r['path'];
	}
	$r['path'] = url_remove_dot_segments($r['path']);
	return join_url($r);
}

/**
 * Filter out "." and ".." segments from a URL's path and return
 * the result.
 *
 * This function implements the "remove_dot_segments" algorithm from
 * the RFC3986 specification for URLs.
 *
 * This function supports multi-byte characters with the UTF-8 encoding,
 * per the URL specification.
 *
 * Parameters:
 * 	path	the path to filter
 *
 * Return values:
 * 	The filtered path with "." and ".." removed.
 */
function url_remove_dot_segments( $path )
{
	// multi-byte character explode
	$inSegs  = preg_split( '!/!u', $path );
	$outSegs = array( );
	foreach ( $inSegs as $seg )
	{
		if ( $seg == '' || $seg == '.')
			continue;
		if ( $seg == '..' )
			array_pop( $outSegs );
		else
			array_push( $outSegs, $seg );
	}
	$outPath = implode( '/', $outSegs );

	if ($path[0] == '/') {
		$outPath = '/' . $outPath;
	}

	// Compare last multi-byte character against '/'.
	if ($outPath != '/' && (textlib::strlen($path) - 1) == textlib::strrpos($path, '/', 'UTF-8')) {
		$outPath .= '/';
	}
	return $outPath;
}

/**
 * This function parses an absolute or relative URL and splits it
 * into individual components.
 *
 * RFC3986 specifies the components of a Uniform Resource Identifier (URI).
 * A portion of the ABNFs are repeated here:
 *
 *	URI-reference	= URI
 *			/ relative-ref
 *
 *	URI		= scheme ":" hier-part [ "?" query ] [ "#" fragment ]
 *
 *	relative-ref	= relative-part [ "?" query ] [ "#" fragment ]
 *
 *	hier-part	= "//" authority path-abempty
 *			/ path-absolute
 *			/ path-rootless
 *			/ path-empty
 *
 *	relative-part	= "//" authority path-abempty
 *			/ path-absolute
 *			/ path-noscheme
 *			/ path-empty
 *
 *	authority	= [ userinfo "@" ] host [ ":" port ]
 *
 * So, a URL has the following major components:
 *
 *	scheme
 *		The name of a method used to interpret the rest of
 *		the URL.  Examples:  "http", "https", "mailto", "file'.
 *
 *	authority
 *		The name of the authority governing the URL's name
 *		space.  Examples:  "example.com", "user@example.com",
 *		"example.com:80", "user:password@example.com:80".
 *
 *		The authority may include a host name, port number,
 *		user name, and password.
 *
 *		The host may be a name, an IPv4 numeric address, or
 *		an IPv6 numeric address.
 *
 *	path
 *		The hierarchical path to the URL's resource.
 *		Examples:  "/index.htm", "/scripts/page.php".
 *
 *	query
 *		The data for a query.  Examples:  "?search=google.com".
 *
 *	fragment
 *		The name of a secondary resource relative to that named
 *		by the path.  Examples:  "#section1", "#header".
 *
 * An "absolute" URL must include a scheme and path.  The authority, query,
 * and fragment components are optional.
 *
 * A "relative" URL does not include a scheme and must include a path.  The
 * authority, query, and fragment components are optional.
 *
 * This function splits the $url argument into the following components
 * and returns them in an associative array.  Keys to that array include:
 *
 *	"scheme"	The scheme, such as "http".
 *	"host"		The host name, IPv4, or IPv6 address.
 *	"port"		The port number.
 *	"user"		The user name.
 *	"pass"		The user password.
 *	"path"		The path, such as a file path for "http".
 *	"query"		The query.
 *	"fragment"	The fragment.
 *
 * One or more of these may not be present, depending upon the URL.
 *
 * Optionally, the "user", "pass", "host" (if a name, not an IP address),
 * "path", "query", and "fragment" may have percent-encoded characters
 * decoded.  The "scheme" and "port" cannot include percent-encoded
 * characters and are never decoded.  Decoding occurs after the URL has
 * been parsed.
 *
 * Parameters:
 * 	url		the URL to parse.
 *
 * 	decode		an optional boolean flag selecting whether
 * 			to decode percent encoding or not.  Default = TRUE.
 *
 * Return values:
 * 	the associative array of URL parts, or FALSE if the URL is
 * 	too malformed to recognize any parts.
 */
function split_url( $url, $decode=FALSE)
{
	// Character sets from RFC3986.
	$xunressub     = 'a-zA-Z\d\-._~\!$&\'()*+,;=';
	$xpchar        = $xunressub . ':@% ';

	// Scheme from RFC3986.
	$xscheme        = '([a-zA-Z][a-zA-Z\d+-.]*)';

	// User info (user + password) from RFC3986.
	$xuserinfo     = '((['  . $xunressub . '%]*)' .
	                 '(:([' . $xunressub . ':%]*))?)';

	// IPv4 from RFC3986 (without digit constraints).
	$xipv4         = '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})';

	// IPv6 from RFC2732 (without digit and grouping constraints).
	$xipv6         = '(\[([a-fA-F\d.:]+)\])';

	// Host name from RFC1035.  Technically, must start with a letter.
	// Relax that restriction to better parse URL structure, then
	// leave host name validation to application.
	$xhost_name    = '([a-zA-Z\d-.%]+)';

	// Authority from RFC3986.  Skip IP future.
	$xhost         = '(' . $xhost_name . '|' . $xipv4 . '|' . $xipv6 . ')';
	$xport         = '(\d*)';
	$xauthority    = '((' . $xuserinfo . '@)?' . $xhost .
		         '?(:' . $xport . ')?)';

	// Path from RFC3986.  Blend absolute & relative for efficiency.
	$xslash_seg    = '(/[' . $xpchar . ']*)';
	$xpath_authabs = '((//' . $xauthority . ')((/[' . $xpchar . ']*)*))';
	$xpath_rel     = '([' . $xpchar . ']+' . $xslash_seg . '*)';
	$xpath_abs     = '(/(' . $xpath_rel . ')?)';
	$xapath        = '(' . $xpath_authabs . '|' . $xpath_abs .
			 '|' . $xpath_rel . ')';

	// Query and fragment from RFC3986.
	$xqueryfrag    = '([' . $xpchar . '/?' . ']*)';

	// URL.
	$xurl          = '^(' . $xscheme . ':)?' .  $xapath . '?' .
	                 '(\?' . $xqueryfrag . ')?(#' . $xqueryfrag . ')?$';


	// Split the URL into components.
	if ( !preg_match( '!' . $xurl . '!', $url, $m ) )
		return FALSE;

	if ( !empty($m[2]) )		$parts['scheme']  = strtolower($m[2]);

	if ( !empty($m[7]) ) {
		if ( isset( $m[9] ) )	$parts['user']    = $m[9];
		else			$parts['user']    = '';
	}
	if ( !empty($m[10]) )		$parts['pass']    = $m[11];

	if ( !empty($m[13]) )		$h=$parts['host'] = $m[13];
	else if ( !empty($m[14]) )	$parts['host']    = $m[14];
	else if ( !empty($m[16]) )	$parts['host']    = $m[16];
	else if ( !empty( $m[5] ) )	$parts['host']    = '';
	if ( !empty($m[17]) )		$parts['port']    = $m[18];

	if ( !empty($m[19]) )		$parts['path']    = $m[19];
	else if ( !empty($m[21]) )	$parts['path']    = $m[21];
	else if ( !empty($m[25]) )	$parts['path']    = $m[25];

	if ( !empty($m[27]) )		$parts['query']   = $m[28];
	if ( !empty($m[29]) )		$parts['fragment']= $m[30];

	if ( !$decode )
		return $parts;
	if ( !empty($parts['user']) )
		$parts['user']     = rawurldecode( $parts['user'] );
	if ( !empty($parts['pass']) )
		$parts['pass']     = rawurldecode( $parts['pass'] );
	if ( !empty($parts['path']) )
		$parts['path']     = rawurldecode( $parts['path'] );
	if ( isset($h) )
		$parts['host']     = rawurldecode( $parts['host'] );
	if ( !empty($parts['query']) )
		$parts['query']    = rawurldecode( $parts['query'] );
	if ( !empty($parts['fragment']) )
		$parts['fragment'] = rawurldecode( $parts['fragment'] );
	return $parts;
}

/**
 * This function joins together URL components to form a complete URL.
 *
 * RFC3986 specifies the components of a Uniform Resource Identifier (URI).
 * This function implements the specification's "component recomposition"
 * algorithm for combining URI components into a full URI string.
 *
 * The $parts argument is an associative array containing zero or
 * more of the following:
 *
 *	"scheme"	The scheme, such as "http".
 *	"host"		The host name, IPv4, or IPv6 address.
 *	"port"		The port number.
 *	"user"		The user name.
 *	"pass"		The user password.
 *	"path"		The path, such as a file path for "http".
 *	"query"		The query.
 *	"fragment"	The fragment.
 *
 * The "port", "user", and "pass" values are only used when a "host"
 * is present.
 *
 * The optional $encode argument indicates if appropriate URL components
 * should be percent-encoded as they are assembled into the URL.  Encoding
 * is only applied to the "user", "pass", "host" (if a host name, not an
 * IP address), "path", "query", and "fragment" components.  The "scheme"
 * and "port" are never encoded.  When a "scheme" and "host" are both
 * present, the "path" is presumed to be hierarchical and encoding
 * processes each segment of the hierarchy separately (i.e., the slashes
 * are left alone).
 *
 * The assembled URL string is returned.
 *
 * Parameters:
 * 	parts		an associative array of strings containing the
 * 			individual parts of a URL.
 *
 * 	encode		an optional boolean flag selecting whether
 * 			to do percent encoding or not.  Default = true.
 *
 * Return values:
 * 	Returns the assembled URL string.  The string is an absolute
 * 	URL if a scheme is supplied, and a relative URL if not.  An
 * 	empty string is returned if the $parts array does not contain
 * 	any of the needed values.
 */
function join_url( $parts, $encode=FALSE)
{
	if ( $encode )
	{
		if ( isset( $parts['user'] ) )
			$parts['user']     = rawurlencode( $parts['user'] );
		if ( isset( $parts['pass'] ) )
			$parts['pass']     = rawurlencode( $parts['pass'] );
		if ( isset( $parts['host'] ) &&
			!preg_match( '!^(\[[\da-f.:]+\]])|([\da-f.:]+)$!ui', $parts['host'] ) )
			$parts['host']     = rawurlencode( $parts['host'] );
		if ( !empty( $parts['path'] ) )
			$parts['path']     = preg_replace( '!%2F!ui', '/',
				rawurlencode( $parts['path'] ) );
		if ( isset( $parts['query'] ) )
			$parts['query']    = rawurlencode( $parts['query'] );
		if ( isset( $parts['fragment'] ) )
			$parts['fragment'] = rawurlencode( $parts['fragment'] );
	}

	$url = '';
	if ( !empty( $parts['scheme'] ) )
		$url .= $parts['scheme'] . ':';
	if ( isset( $parts['host'] ) )
	{
		$url .= '//';
		if ( isset( $parts['user'] ) )
		{
			$url .= $parts['user'];
			if ( isset( $parts['pass'] ) )
				$url .= ':' . $parts['pass'];
			$url .= '@';
		}
		if ( preg_match( '!^[\da-f]*:[\da-f.:]+$!ui', $parts['host'] ) )
			$url .= '[' . $parts['host'] . ']';	// IPv6
		else
			$url .= $parts['host'];			// IPv4 or name
		if ( isset( $parts['port'] ) )
			$url .= ':' . $parts['port'];
		if ( !empty( $parts['path'] ) && $parts['path'][0] != '/' )
			$url .= '/';
	}
	if ( !empty( $parts['path'] ) )
		$url .= $parts['path'];
	if ( isset( $parts['query'] ) )
		$url .= '?' . $parts['query'];
	if ( isset( $parts['fragment'] ) )
		$url .= '#' . $parts['fragment'];
	return $url;
}

/**
 * This function encodes URL to form a URL which is properly
 * percent encoded to replace disallowed characters.
 *
 * RFC3986 specifies the allowed characters in the URL as well as
 * reserved characters in the URL. This function replaces all the
 * disallowed characters in the URL with their repective percent
 * encodings. Already encoded characters are not encoded again,
 * such as '%20' is not encoded to '%2520'.
 *
 * Parameters:
 * 	url		the url to encode.
 *
 * Return values:
 * 	Returns the encoded URL string.
 */
function encode_url($url) {
  $reserved = array(
    ":" => '!%3A!ui',
    "/" => '!%2F!ui',
    "?" => '!%3F!ui',
    "#" => '!%23!ui',
    "[" => '!%5B!ui',
    "]" => '!%5D!ui',
    "@" => '!%40!ui',
    "!" => '!%21!ui',
    "$" => '!%24!ui',
    "&" => '!%26!ui',
    "'" => '!%27!ui',
    "(" => '!%28!ui',
    ")" => '!%29!ui',
    "*" => '!%2A!ui',
    "+" => '!%2B!ui',
    "," => '!%2C!ui',
    ";" => '!%3B!ui',
    "=" => '!%3D!ui',
    "%" => '!%25!ui',
  );

  $url = rawurlencode($url);
  $url = preg_replace(array_values($reserved), array_keys($reserved), $url);
  return $url;
}

/**
 * Extract URLs from a web page.
 *
 * URLs are extracted from a long list of tags and attributes as defined
 * by the HTML 2.0, HTML 3.2, HTML 4.01, and draft HTML 5.0 specifications.
 * URLs are also extracted from tags and attributes that are common
 * extensions of HTML, from the draft Forms 2.0 specification, from XHTML,
 * and from WML 1.3 and 2.0.
 *
 * The function returns an associative array of associative arrays of
 * arrays of URLs.  The outermost array's keys are the tag (element) name,
 * such as "a" for <a> or "img" for <img>.  The values for these entries
 * are associative arrays where the keys are attribute names for those
 * tags, such as "href" for <a href="...">.  Finally, the values for
 * those arrays are URLs found in those tags and attributes throughout
 * the text.
 *
 * Parameters:
 * 	text		the UTF-8 text to scan
 *
 * Return values:
 * 	an associative array where keys are tags and values are an
 * 	associative array where keys are attributes and values are
 * 	an array of URLs.
 *
 * See:
 * 	http://nadeausoftware.com/articles/2008/01/php_tip_how_extract_urls_web_page
 */
function extract_html_urls( $text )
{
	$match_elements = array(
		// HTML
		array('element'=>'a',		'attribute'=>'href'),		// 2.0
		array('element'=>'a',		'attribute'=>'urn'),		// 2.0
		array('element'=>'base',	'attribute'=>'href'),		// 2.0
		array('element'=>'form',	'attribute'=>'action'),		// 2.0
		array('element'=>'img',		'attribute'=>'src'),		// 2.0
		array('element'=>'link',	'attribute'=>'href'),		// 2.0

		array('element'=>'applet',	'attribute'=>'code'),		// 3.2
		array('element'=>'applet',	'attribute'=>'codebase'),	// 3.2
		array('element'=>'area',	'attribute'=>'href'),		// 3.2
		array('element'=>'body',	'attribute'=>'background'),	// 3.2
		array('element'=>'img',		'attribute'=>'usemap'),		// 3.2
		array('element'=>'input',	'attribute'=>'src'),		// 3.2

		array('element'=>'applet',	'attribute'=>'archive'),	// 4.01
		array('element'=>'applet',	'attribute'=>'object'),		// 4.01
		array('element'=>'blockquote',	'attribute'=>'cite'),		// 4.01
		array('element'=>'del',		'attribute'=>'cite'),		// 4.01
		array('element'=>'frame',	'attribute'=>'longdesc'),	// 4.01
		array('element'=>'frame',	'attribute'=>'src'),		// 4.01
		array('element'=>'head',	'attribute'=>'profile'),	// 4.01
		array('element'=>'iframe',	'attribute'=>'longdesc'),	// 4.01
		array('element'=>'iframe',	'attribute'=>'src'),		// 4.01
		array('element'=>'img',		'attribute'=>'longdesc'),	// 4.01
		array('element'=>'input',	'attribute'=>'usemap'),		// 4.01
		array('element'=>'ins',		'attribute'=>'cite'),		// 4.01
		array('element'=>'object',	'attribute'=>'archive'),	// 4.01
		array('element'=>'object',	'attribute'=>'classid'),	// 4.01
		array('element'=>'object',	'attribute'=>'codebase'),	// 4.01
		array('element'=>'object',	'attribute'=>'data'),		// 4.01
		array('element'=>'object',	'attribute'=>'usemap'),		// 4.01
		array('element'=>'q',		'attribute'=>'cite'),		// 4.01
		array('element'=>'script',	'attribute'=>'src'),		// 4.01

		array('element'=>'audio',	'attribute'=>'src'),		// 5.0
		array('element'=>'command',	'attribute'=>'icon'),		// 5.0
		array('element'=>'embed',	'attribute'=>'src'),		// 5.0
		array('element'=>'event-source','attribute'=>'src'),		// 5.0
		array('element'=>'html',	'attribute'=>'manifest'),	// 5.0
		array('element'=>'source',	'attribute'=>'src'),		// 5.0
		array('element'=>'video',	'attribute'=>'src'),		// 5.0
		array('element'=>'video',	'attribute'=>'poster'),		// 5.0

		array('element'=>'bgsound',	'attribute'=>'src'),		// Extension
		array('element'=>'body',	'attribute'=>'credits'),	// Extension
		array('element'=>'body',	'attribute'=>'instructions'),	// Extension
		array('element'=>'body',	'attribute'=>'logo'),		// Extension
		array('element'=>'div',		'attribute'=>'href'),		// Extension
		array('element'=>'div',		'attribute'=>'src'),		// Extension
		array('element'=>'embed',	'attribute'=>'code'),		// Extension
		array('element'=>'embed',	'attribute'=>'pluginspage'),	// Extension
		array('element'=>'html',	'attribute'=>'background'),	// Extension
		array('element'=>'ilayer',	'attribute'=>'src'),		// Extension
		array('element'=>'img',		'attribute'=>'dynsrc'),		// Extension
		array('element'=>'img',		'attribute'=>'lowsrc'),		// Extension
		array('element'=>'input',	'attribute'=>'dynsrc'),		// Extension
		array('element'=>'input',	'attribute'=>'lowsrc'),		// Extension
		array('element'=>'table',	'attribute'=>'background'),	// Extension
		array('element'=>'td',		'attribute'=>'background'),	// Extension
		array('element'=>'th',		'attribute'=>'background'),	// Extension
		array('element'=>'layer',	'attribute'=>'src'),		// Extension
		array('element'=>'xml',		'attribute'=>'src'),		// Extension

		array('element'=>'button',	'attribute'=>'action'),		// Forms 2.0
		array('element'=>'datalist',	'attribute'=>'data'),		// Forms 2.0
		array('element'=>'form',	'attribute'=>'data'),		// Forms 2.0
		array('element'=>'input',	'attribute'=>'action'),		// Forms 2.0
		array('element'=>'select',	'attribute'=>'data'),		// Forms 2.0

		// XHTML
		array('element'=>'html',	'attribute'=>'xmlns'),

		// WML
		array('element'=>'access',	'attribute'=>'path'),		// 1.3
		array('element'=>'card',	'attribute'=>'onenterforward'),	// 1.3
		array('element'=>'card',	'attribute'=>'onenterbackward'),// 1.3
		array('element'=>'card',	'attribute'=>'ontimer'),	// 1.3
		array('element'=>'go',		'attribute'=>'href'),		// 1.3
		array('element'=>'option',	'attribute'=>'onpick'),		// 1.3
		array('element'=>'template',	'attribute'=>'onenterforward'),	// 1.3
		array('element'=>'template',	'attribute'=>'onenterbackward'),// 1.3
		array('element'=>'template',	'attribute'=>'ontimer'),	// 1.3
		array('element'=>'wml',		'attribute'=>'xmlns'),		// 2.0
	);

	$match_metas = array(
		'content-base',
		'content-location',
		'referer',
		'location',
		'refresh',
	);

	// Extract all elements
	if ( !preg_match_all( '/<([a-z][^>]*)>/iu', $text, $matches ) )
		return array( );
	$elements = $matches[1];
	$value_pattern = '=(("([^"]*)")|([^\s]*))';

	// Match elements and attributes
	foreach ( $match_elements as $match_element )
	{
		$name = $match_element['element'];
		$attr = $match_element['attribute'];
		$pattern = '/^' . $name . '\s.*' . $attr . $value_pattern . '/iu';
		if ( $name == 'object' )
			$split_pattern = '/\s*/u';	// Space-separated URL list
		else if ( $name == 'archive' )
			$split_pattern = '/,\s*/u';	// Comma-separated URL list
		else
			unset( $split_pattern );	// Single URL
		foreach ( $elements as $element )
		{
			if ( !preg_match( $pattern, $element, $match ) )
				continue;
			$m = empty($match[3]) ? (!empty($match[4])?$match[4]:'') : $match[3];
			if ( !isset( $split_pattern ) )
				$urls[$name][$attr][] = $m;
			else
			{
				$msplit = preg_split( $split_pattern, $m );
				foreach ( $msplit as $ms )
					$urls[$name][$attr][] = $ms;
			}
		}
	}

	// Match meta http-equiv elements
	foreach ( $match_metas as $match_meta )
	{
		$attr_pattern    = '/http-equiv="?' . $match_meta . '"?/iu';
		$content_pattern = '/content'  . $value_pattern . '/iu';
		$refresh_pattern = '/\d*;\s*(url=)?(.*)$/iu';
		foreach ( $elements as $element )
		{
			if ( !preg_match( '/^meta/iu', $element ) ||
				!preg_match( $attr_pattern, $element ) ||
				!preg_match( $content_pattern, $element, $match ) )
				continue;
			$m = empty($match[3]) ? $match[4] : $match[3];
			if ( $match_meta != 'refresh' )
				$urls['meta']['http-equiv'][] = $m;
			else if ( preg_match( $refresh_pattern, $m, $match ) )
				$urls['meta']['http-equiv'][] = $match[2];
		}
	}

	// Match style attributes
	$urls['style'] = array( );
	$style_pattern = '/style' . $value_pattern . '/iu';
	foreach ( $elements as $element )
	{
		if ( !preg_match( $style_pattern, $element, $match ) )
			continue;
		$m = empty($match[3]) ? $match[4] : $match[3];
		$style_urls = extract_css_urls( $m );
		if ( !empty( $style_urls ) )
			$urls['style'] = array_merge_recursive(
				$urls['style'], $style_urls );
	}

	// Match style bodies
	if ( preg_match_all( '/<style[^>]*>(.*?)<\/style>/siu', $text, $style_bodies ) )
	{
		foreach ( $style_bodies[1] as $style_body )
		{
			$style_urls = extract_css_urls( $style_body );
			if ( !empty( $style_urls ) )
				$urls['style'] = array_merge_recursive(
					$urls['style'], $style_urls );
		}
	}
	if ( empty($urls['style']) )
		unset( $urls['style'] );

	return $urls;
}
/**
 * Extract URLs from UTF-8 CSS text.
 *
 * URLs within @import statements and url() property functions are extracted
 * and returned in an associative array of arrays.  Array keys indicate
 * the use context for the URL, including:
 *
 * 	"import"
 * 	"property"
 *
 * Each value in the associative array is an array of URLs.
 *
 * Parameters:
 * 	text		the UTF-8 text to scan
 *
 * Return values:
 * 	an associative array of arrays of URLs.
 *
 * See:
 * 	http://nadeausoftware.com/articles/2008/01/php_tip_how_extract_urls_css_file
 */
function extract_css_urls( $text )
{
	$urls = array( );

	$url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
	$urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
	$pattern         = '/(' .
		 '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
		'|(@import\s*'      . $urlfunc_pattern . ')'      .
		'|('                . $urlfunc_pattern . ')'      .  ')/iu';
	if ( !preg_match_all( $pattern, $text, $matches ) )
		return $urls;

	// @import '...'
	// @import "..."
	foreach ( $matches[3] as $match )
		if ( !empty($match) )
			$urls['import'][] =
				preg_replace( '/\\\\(.)/u', '\\1', $match );

	// @import url(...)
	// @import url('...')
	// @import url("...")
	foreach ( $matches[7] as $match )
		if ( !empty($match) )
			$urls['import'][] =
				preg_replace( '/\\\\(.)/u', '\\1', $match );

	// url(...)
	// url('...')
	// url("...")
	foreach ( $matches[11] as $match )
		if ( !empty($match) )
			$urls['property'][] =
				preg_replace( '/\\\\(.)/u', '\\1', $match );

	return $urls;
}
