PHP Markdown
============

PHP Markdown Lib 1.7.0 - 29 Oct 2016

by Michel Fortin  
<https://michelf.ca/>

based on Markdown by John Gruber  
<https://daringfireball.net/>


Introduction
------------

This is a library package that includes the PHP Markdown parser and its 
sibling PHP Markdown Extra with additional features.

Markdown is a text-to-HTML conversion tool for web writers. Markdown
allows you to write using an easy-to-read, easy-to-write plain text
format, then convert it to structurally valid XHTML (or HTML).

"Markdown" is actually two things: a plain text markup syntax, and a 
software tool, originally written in Perl, that converts the plain text 
markup to HTML. PHP Markdown is a port to PHP of the original Markdown 
program by John Gruber.

*	[Full documentation of the Markdown syntax](<https://daringfireball.net/projects/markdown/>)  
	— Daring Fireball (John Gruber)
*	[Markdown Extra syntax additions](<https://michelf.ca/projects/php-markdown/extra/>)  
	— Michel Fortin


Requirement
-----------

This library package requires PHP 5.3 or later.

Note: The older plugin/library hybrid package for PHP Markdown and
PHP Markdown Extra is still maintained and will work with PHP 4.0.5 and later.

Before PHP 5.3.7, pcre.backtrack_limit defaults to 100 000, which is too small
in many situations. You might need to set it to higher values. Later PHP 
releases defaults to 1 000 000, which is usually fine.


Usage
-----

This library package is meant to be used with class autoloading. For autoloading 
to work, your project needs have setup a PSR-0-compatible autoloader. See the 
included Readme.php file for a minimal autoloader setup. (If you cannot use 
autoloading, see below.)

With class autoloading in place, putting the 'Michelf' folder in your 
include path should be enough for this to work:

	use \Michelf\Markdown;
	$my_html = Markdown::defaultTransform($my_text);

Markdown Extra syntax is also available the same way:

	use \Michelf\MarkdownExtra;
	$my_html = MarkdownExtra::defaultTransform($my_text);

If you wish to use PHP Markdown with another text filter function 
built to parse HTML, you should filter the text *after* the `transform`
function call. This is an example with [PHP SmartyPants][psp]:

	use \Michelf\Markdown, \Michelf\SmartyPants;
	$my_html = Markdown::defaultTransform($my_text);
	$my_html = SmartyPants::defaultTransform($my_html);

All these examples are using the static `defaultTransform` static function 
found inside the parser class. If you want to customize the parser 
configuration, you can also instantiate it directly and change some 
configuration variables:

	use \Michelf\MarkdownExtra;
	$parser = new MarkdownExtra;
	$parser->fn_id_prefix = "post22-";
	$my_html = $parser->transform($my_text);

To learn more, see the full list of [configuration variables].

 [configuration variables]: https://michelf.ca/projects/php-markdown/configuration/


### Usage without an autoloader

If you cannot use class autoloading, you can still use `include` or `require` 
to access the parser. To load the `\Michelf\Markdown` parser, do it this way:

	require_once 'Michelf/Markdown.inc.php';

Or, if you need the `\Michelf\MarkdownExtra` parser:

	require_once 'Michelf/MarkdownExtra.inc.php';

While the plain `.php` files depend on autoloading to work correctly, using the
`.inc.php` files instead will eagerly load the dependencies that would be 
loaded on demand if you were using autoloading.


Public API and Versioning Policy
---------------------------------

Version numbers are of the form *major*.*minor*.*patch*.

The public API of PHP Markdown consist of the two parser classes `Markdown`
and `MarkdownExtra`, their constructors, the `transform` and `defaultTransform`
functions and their configuration variables. The public API is stable for
a given major version number. It might get additions when the minor version
number increments.

**Protected members are not considered public API.** This is unconventional 
and deserves an explanation. Incrementing the major version number every time 
the underlying implementation of something changes is going to give
nonessential version numbers for the vast majority of people who just use the
parser.  Protected members are meant to create parser subclasses that behave in
different ways. Very few people create parser subclasses. I don't want to 
discourage it by making everything private, but at the same time I can't 
guarantee any stable hook between versions if you use protected members.

**Syntax changes** will increment the minor number for new features, and the 
patch number for small corrections. A *new feature* is something that needs a 
change in the syntax documentation. Note that since PHP Markdown Lib includes
two parsers, a syntax change for either of them will increment the minor 
number. Also note that there is nothing perfectly backward-compatible with the
Markdown syntax: all inputs are always valid, so new features always replace
something that was previously legal, although generally nonsensical to do.


Bugs
----

To file bug reports please send email to:
<michel.fortin@michelf.ca>

Please include with your report: (1) the example input; (2) the output you
expected; (3) the output PHP Markdown actually produced.

If you have a problem where Markdown gives you an empty result, first check 
that the backtrack limit is not too low by running `php --info | grep pcre`.
See Installation and Requirement above for details.


Development and Testing
-----------------------

Pull requests for fixing bugs are welcome. Proposed new features are
going to be meticulously reviewed -- taking into account backward compatibility, 
potential side effects, and future extensibility -- before deciding on
acceptance or rejection.

If you make a pull request that includes changes to the parser please add 
tests for what is being changed to [MDTest][] and make a pull request there 
too.

 [MDTest]: https://github.com/michelf/mdtest/


Donations
---------

If you wish to make a donation that will help me devote more time to 
PHP Markdown, please visit [michelf.ca/donate] or send Bitcoin to
[1HiuX34czvVPPdhXbUAsAu7pZcesniDCGH].

 [michelf.ca/donate]: https://michelf.ca/donate/#!Thanks%20for%20PHP%20Markdown
 [1HiuX34czvVPPdhXbUAsAu7pZcesniDCGH]: bitcoin:1HiuX34czvVPPdhXbUAsAu7pZcesniDCGH


Version History
---------------

PHP Markdown Lib 1.7.0 (29 Oct 2016)

*	Added a `hard_wrap` configuration variable to make all newline characters 
	in the text become `<br>` tags in the HTML output. By default, according 
	to the standard Markdown syntax these newlines are ignored unless they a 
	preceded by two spaces. Thanks to Jonathan Cohlmeyer for the implementation.

*	Improved the parsing of list items to fix problematic cases that came to 
	light with the addition of `hard_wrap`. This should have no effect on the 
	output except span-level list items that ended with two spaces (and thus 
	ended with a line break).

*	Added a `code_span_content_func` configuration variable which takes a 
	function that will convert the content of the code span to HTML. This can
	be useful to implement syntax highlighting. Although contrary to its 
	code block equivalent, there is no syntax for specifying a language. 
	Credits to styxit for the implementation.

*	Fixed a Markdwon Extra issue where two-space-at-end-of-line hard breaks 
	wouldn't work inside of HTML block elements such as `<p markdown="1">` 
	where the element expects only span-level content.

*	In the parser code, switched to PHPDoc comment format. Thanks to 
	Robbie Averill for the help.


PHP Markdown Lib 1.6.0 (23 Dec 2015)  

Note: this version was incorrectly released as 1.5.1 on Dec 22, a number 
that contradicted the versioning policy.

*	For fenced code blocks in Markdown Extra, can now set a class name for the 
	code block's language before the special attribute block. Previously, this 
	class name was only allowed in the absence of the special attribute block.

*	Added a `code_block_content_func` configuration variable which takes a 
	function that will convert the content of the code block to HTML. This is 
	most useful for syntax highlighting. For fenced code blocks in Markdown 
	Extra, the function has access to the language class name (the one outside 
	of the special attribute block). Credits to Mario Konrad for providing the 
	implementation.

*	The curled arrow character for the backlink in footnotes is now followed
	by a Unicode variant selector to prevent it from being displayed in emoji
	form on iOS.

	Note that in older browsers the variant selector is often interpreted as a 
	separate character, making it visible after the arrow. So there is now a 
	also a `fn_backlink_html` configuration variable that can be used to set 
	the link text to something else. Credits to Dana for providing the 
	implementation.

*	Fixed an issue in MarkdownExtra where long header lines followed by a
	special attribute block would hit the backtrack limit an cause an empty
	string to be returned.


PHP Markdown Lib 1.5.0 (1 Mar 2015)

*	Added the ability start ordered lists with a number different from 1 and
	and have that reflected in the HTML output. This can be enabled with
	the `enhanced_ordered_lists` configuration variable for the Markdown 
	parser; it is enabled by default for Markdown Extra.
	Credits to Matt Gorle for providing the implementation.

*	Added the ability to insert custom HTML attributes with simple values 
	everywhere an extra attribute block is allowed (links, images, headers).
	The value must be unquoted, cannot contains spaces and is limited to 
	alphanumeric ASCII characters.
	Credits to Peter Droogmans for providing the implementation.

*	Added a `header_id_func` configuration variable which takes a function
	that can generate an `id` attribute value from the header text.
	Credits to Evert Pot for providing the implementation.

*	Added a `url_filter_func` configuration variable which takes a function
	that can rewrite any link or image URL to something different.


PHP Markdown Lib 1.4.1 (4 May 2014)

*	The HTML block parser will now treat `<figure>` as a block-level element
	(as it should) and no longer wrap it in `<p>` or parse it's content with 
	the as Markdown syntax (although with Extra you can use `markdown="1"`
	if you wish to use the Markdown syntax inside it).

*	The content of `<style>` elements will now be left alone, its content
	won't be interpreted as Markdown.

*	Corrected an bug where some inline links with spaces in them would not
	work even when surounded with angle brackets:
	
		[link](<s p a c e s>)

*	Fixed an issue where email addresses with quotes in them would not always
	have the quotes escaped in the link attribute, causing broken links (and
	invalid HTML).

*	Fixed the case were a link definition following a footnote definition would
	be swallowed by the footnote unless it was separated by a blank line.


PHP Markdown Lib 1.4.0 (29 Nov 2013)

*	Added support for the `tel:` URL scheme in automatic links.

		<tel:+1-111-111-1111>
	
	It gets converted to this (note the `tel:` prefix becomes invisible):
	
		<a href="tel:+1-111-111-1111">+1-111-111-1111</a>

*	Added backtick fenced code blocks to MarkdownExtra, originally from
	Github-Flavored Markdown.

*	Added an interface called MarkdownInterface implemented by both
	the Markdown and MarkdownExtra parsers. You can use the interface if
	you want to create a mockup parser object for unit testing.

*	For those of you who cannot use class autoloading, you can now
	include `Michelf/Markdown.inc.php` or `Michelf/MarkdownExtra.inc.php` (note 
	the 	`.inc.php` extension) to automatically include other files required
	by the parser.


PHP Markdown Lib 1.3 (11 Apr 2013)

This is the first release of PHP Markdown Lib. This package requires PHP 
version 5.3 or later and is designed to work with PSR-0 autoloading and, 
optionally with Composer. Here is a list of the changes since 
PHP Markdown Extra 1.2.6:

*	Plugin interface for WordPress and other systems is no longer present in
	the Lib package. The classic package is still available if you need it:
	<https://michelf.ca/projects/php-markdown/classic/>

*	Added `public` and `protected` protection attributes, plus a section about
	what is "public API" and what isn't in the Readme file.

*	Changed HTML output for footnotes: now instead of adding `rel` and `rev`
	attributes, footnotes links have the class name `footnote-ref` and
	backlinks `footnote-backref`.

*	Fixed some regular expressions to make PCRE not shout warnings about POSIX
	collation classes (dependent on your version of PCRE).

*	Added optional class and id attributes to images and links using the same
	syntax as for headers:

		[link](url){#id .class}  
		![img](url){#id .class}
	
	It work too for reference-style links and images. In this case you need
	to put those attributes at the reference definition:

		[link][linkref] or [linkref]  
		![img][linkref]
		
		[linkref]: url "optional title" {#id .class}

*	Fixed a PHP notice message triggered when some table column separator 
	markers are missing on the separator line below column headers.

*	Fixed a small mistake that could cause the parser to retain an invalid
	state related to parsing links across multiple runs. This was never 
	observed (that I know of), but it's still worth fixing.


Copyright and License
---------------------

PHP Markdown Lib
Copyright (c) 2004-2016 Michel Fortin
<https://michelf.ca/>  
All rights reserved.

Based on Markdown  
Copyright (c) 2003-2005 John Gruber   
<https://daringfireball.net/>   
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

*   Redistributions of source code must retain the above copyright 
    notice, this list of conditions and the following disclaimer.

*   Redistributions in binary form must reproduce the above copyright
    notice, this list of conditions and the following disclaimer in the
    documentation and/or other materials provided with the 
    distribution.

*   Neither the name "Markdown" nor the names of its contributors may
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
