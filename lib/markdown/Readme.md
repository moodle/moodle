PHP Markdown
============

PHP Markdown Lib 1.3 - 11 Apr 2013

by Michel Fortin  
<http://michelf.ca/>

based on Markdown by John Gruber  
<http://daringfireball.net/>


Introduction
------------

This is a library package that includes the PHP Markdown parser and its 
sibling PHP Markdown Extra which additional features.

Markdown is a text-to-HTML conversion tool for web writers. Markdown
allows you to write using an easy-to-read, easy-to-write plain text
format, then convert it to structurally valid XHTML (or HTML).

"Markdown" is two things: a plain text markup syntax, and a software 
tool, written in Perl, that converts the plain text markup to HTML. 
PHP Markdown is a port to PHP of the original Markdown program by 
John Gruber.

PHP Markdown can work as a plug-in for WordPress, as a modifier for
the Smarty templating engine, or as a replacement for Textile
formatting in any software that supports Textile.

Full documentation of Markdown's syntax is available on John's 
Markdown page: <http://daringfireball.net/projects/markdown/>


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
included Readme.php file for a minimal autoloader setup. (If you don't want to 
use autoloading you can do a classic `require_once` to manually include the 
files prior use instead.)

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


Usage
-----

This library package is meant to be used with class autoloading. For autoloading 
to work, your project needs have setup a PSR-0-compatible autoloader. See the 
included Readme.php file for a minimal autoloader setup. (If you don't want to 
use autoloading you can do a classic `require_once` to manually include the 
files prior use instead.)

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

 [configuration variables]: http://michelf.ca/project/php-markdown/configuration/


Public API and Versionning Policy
---------------------------------

Version numbers are of the form *major*.*minor*.*patch*.

The public API of PHP Markdown consist of the two parser classes `Markdown`
and `MarkdownExtra`, their constructors, the `transform` and `defaultTransform`
functions and their configuration variables. The public API is stable for
a given major version number. It might get additions when the minor version
number increments.

**Protected members are not considered public API.** This is unconventionnal 
and deserves an explanation. Incrementing the major version number every time 
the underlying implementation of something changes is going to give nonsential 
version numbers for the vast majority of people who just use the parser. 
Protected members are meant to create parser subclasses that behave in 
different ways. Very few people create parser subclasses. I don't want to 
discourage it by making everything private, but at the same time I can't 
guarenty any stable hook between versions if you use protected members.

**Syntax changes** will increment the minor number for new features, and the 
patch number for small corrections. A *new feature* is something that needs a 
change in the syntax documentation. Note that since PHP Markdown Lib includes
two parsers, a syntax change for either of them will increment the minor 
number. Also note that there is nothigng perfectly backward-compatible with the
Markdown syntax: all inputs are always valid, so new features always replace
something that was previously legal, although generally non-sensial to do.


Bugs
----

To file bug reports please send email to:
<michel.fortin@michelf.ca>

Please include with your report: (1) the example input; (2) the output you
expected; (3) the output PHP Markdown actually produced.

If you have a problem where Markdown gives you an empty result, first check 
that the backtrack limit is not too low by running `php --info | grep pcre`.
See Installation and Requirement above for details.


Version History
---------------

PHP Markdown Lib 1.3 (11 Apr 2013):

This is the first release of PHP Markdown Lib. This package requires PHP 
version 4.3 or later and is designed to work with PSR-0 autoloading and, 
optionally with Composer. Here is a list of the changes since 
PHP Markdown Extra 1.2.6:

*	Plugin interface for Wordpress and other systems is no longer present in
	the Lib package. The classic package is still available if you need it:
	<http://michelf.ca/projects/php-markdown/classic/>

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
Copyright (c) 2004-2013 Michel Fortin  
<http://michelf.ca/>  
All rights reserved.

Based on Markdown  
Copyright (c) 2003-2005 John Gruber   
<http://daringfireball.net/>   
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
