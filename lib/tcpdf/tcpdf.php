<?php
//============================================================+
// File name   : tcpdf.php
// Begin       : 2002-08-03
// Last Update : 2008-07-29
// Author      : Nicola Asuni - info@tecnick.com - http://www.tcpdf.org
// Version     : 4.0.015
// License     : GNU LGPL (http://www.gnu.org/copyleft/lesser.html)
// 	----------------------------------------------------------------------------
//  Copyright (C) 2002-2008  Nicola Asuni - Tecnick.com S.r.l.
// 	
// 	This program is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU Lesser General Public License as published by
// 	the Free Software Foundation, either version 2.1 of the License, or
// 	(at your option) any later version.
// 	
// 	This program is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU Lesser General Public License for more details.
// 	
// 	You should have received a copy of the GNU Lesser General Public License
// 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 	
// 	See LICENSE.TXT file for more information.
//  ----------------------------------------------------------------------------
//
// Description : This is a PHP class for generating PDF documents without 
//               requiring external extensions.
//
// NOTE:
// This class was originally derived in 2002 from the Public 
// Domain FPDF class by Olivier Plathey (http://www.fpdf.org), 
// but now is almost entirely rewritten.
//
// Main features:
//  * no external libraries are required for the basic functions;
// 	* supports all ISO page formats;
// 	* supports UTF-8 Unicode and Right-To-Left languages;
// 	* supports document encryption;
// 	* includes methods to publish some XHTML code;
// 	* includes graphic (geometric) and transformation methods;
// 	* includes bookmarks;
// 	* includes Javascript and forms support;
// 	* includes a method to print various barcode formats;
// 	* supports TrueTypeUnicode, TrueType, Type1 and CID-0 fonts;
// 	* supports custom page formats, margins and units of measure;
// 	* includes methods for page header and footer management;
// 	* supports automatic page break;
// 	* supports automatic page numbering and page groups;
// 	* supports automatic line break and text justification;
// 	* supports JPEG and PNG images whitout GD library and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;
// 	* supports stroke and clipping mode for text;
// 	* supports clipping masks;
// 	* supports Grayscale, RGB and CMYK colors and transparency;
// 	* supports links;
// 	* supports page compression (requires zlib extension);
// 	* supports PDF user's rights.
//
// -----------------------------------------------------------
// THANKS TO:
// 
// Olivier Plathey (http://www.fpdf.org) for original FPDF.
// Efthimios Mavrogeorgiadis (emavro@yahoo.com) for suggestions on RTL language support.
// Klemen Vodopivec (http://www.fpdf.de/downloads/addons/37/) for Encryption algorithm.
// Warren Sherliker (wsherliker@gmail.com) for better image handling.
// dullus for text Justification.
// Bob Vincent (pillarsdotnet@users.sourceforge.net) for <li> value attribute.
// Patrick Benny for text stretch suggestion on Cell().
// Johannes Güntert for JavaScript support.
// Denis Van Nuffelen for Dynamic Form.
// Jacek Czekaj for multibyte justification
// Anthony Ferrara for the reintroduction of legacy image methods.
// Sourceforge user 1707880 (hucste) for line-trough mode.
// Larry Stanbery for page groups.
// Martin Hall-May for transparency.
// Aaron C. Spike for Polycurve method.
// Mohamad Ali Golkar, Saleh AlMatrafe, Charles Abbott for Arabic and Persian support.
// Moritz Wagner and Andreas Wurmser for graphic functions.
// Andrew Whitehead for core fonts support.
// Anyone that has reported a bug or sent a suggestion.
//============================================================+

/**
 * This is a PHP class for generating PDF documents without requiring external extensions.<br>
 * TCPDF project (http://www.tcpdf.org) was originally derived in 2002 from the Public Domain FPDF class by Olivier Plathey (http://www.fpdf.org), but now is almost entirely rewritten.<br>
 * <h3>TCPDF main features are:</h3>
 * <ul>
 * <li>no external libraries are required for the basic functions;</li>
 * <li>supports all ISO page formats;</li>
 * <li>supports UTF-8 Unicode and Right-To-Left languages;</li>
 * <li>supports document encryption;</li>
 * <li>includes methods to publish some XHTML code;</li>
 * <li>includes graphic (geometric) and transformation methods;</li>
 * <li>includes bookmarks;</li>
 * <li>includes Javascript and forms support;</li>
 * <li>includes a method to print various barcode formats;</li>
 * <li>supports TrueTypeUnicode, TrueType, Type1 and CID-0 fonts;</li>
 * <li>supports custom page formats, margins and units of measure;</li>
 * <li>includes methods for page header and footer management;</li>
 * <li>supports automatic page break;</li>
 * <li>supports automatic page numbering and page groups;</li>
 * <li>supports automatic line break and text justification;
 * <li>supports JPEG and PNG images whitout GD library and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;</li>
 * <li>supports stroke and clipping mode for text;</li>
 * <li>supports clipping masks;</li>
 * <li>supports Grayscale, RGB and CMYK colors and transparency;</li>
 * <li>supports links;</li>
 * <li>supports page compression (requires zlib extension);</li>
 * <li>supports PDF user's rights.</li>
 * </ul>
 * Tools to encode your unicode fonts are on fonts/ttf2ufm directory.</p>
 * @package com.tecnick.tcpdf
 * @abstract Class for generating PDF files on-the-fly without requiring external extensions.
 * @author Nicola Asuni
 * @copyright 2004-2008 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 4.0.015
 */

/**
 * main configuration file
 */
require_once(dirname(__FILE__).'/config/tcpdf_config.php');

// includes some support files

/**
 * unicode data
 */
require_once(dirname(__FILE__).'/unicode_data.php');

/**
 * html colors table
 */
require_once(dirname(__FILE__).'/htmlcolors.php');

/**
 * barcode class
 */
require_once(dirname(__FILE__)."/barcodes.php");


if (!class_exists('TCPDF')) {
	/**
	 * define default PDF document producer
	 */ 
	define('PDF_PRODUCER','TCPDF 4.0.015 (http://www.tcpdf.org)');
	
	/**
	* This is a PHP class for generating PDF documents without requiring external extensions.<br>
	* TCPDF project (http://www.tcpdf.org) has been originally derived in 2002 from the Public Domain FPDF class by Olivier Plathey (http://www.fpdf.org), but now is almost entirely rewritten.<br>
	* @name TCPDF
	* @package com.tecnick.tcpdf
	* @version 4.0.015
	* @author Nicola Asuni - info@tecnick.com
	* @link http://www.tcpdf.org
	* @license http://www.gnu.org/copyleft/lesser.html LGPL
	*/
	class TCPDF {
		
		// protected or Protected properties

		/**
		* @var current page number
		* @access protected
		*/
		protected $page;
		
		/**
		* @var current object number
		* @access protected
		*/
		protected $n;

		/**
		* @var array of object offsets
		* @access protected
		*/
		protected $offsets;

		/**
		* @var buffer holding in-memory PDF
		* @access protected
		*/
		protected $buffer;

		/**
		* @var array containing pages
		* @access protected
		*/
		protected $pages = array();

		/**
		* @var current document state
		* @access protected
		*/
		protected $state;

		/**
		* @var compression flag
		* @access protected
		*/
		protected $compress;
		
		/**
		* @var current page orientation (P = Portrait, L = Landscape)
		* @access protected
		*/
		protected $CurOrientation;

		/**
		* @var array that stores page dimensions.<ul><li>$this->pagedim[$this->page]['w'] => page_width_in_points</li><li>$this->pagedim[$this->page]['h'] => height</li><li>$this->pagedim[$this->page]['tm'] => top_margin</li><li>$this->pagedim[$this->page]['bm'] => bottom_margin</li><li>$this->pagedim[$this->page]['lm'] => left_margin</li><li>$this->pagedim[$this->page]['rm'] => right_margin</li><li>$this->pagedim[$this->page]['pb'] => auto_page_break</li><li>$this->pagedim[$this->page]['or'] => page_orientation</li></ul>
		* @access protected
		*/
		protected $pagedim = array();

		/**
		* @var scale factor (number of points in user unit)
		* @access protected
		*/
		protected $k;

		/**
		* @var width of page format in points
		* @access protected
		*/
		protected $fwPt;

		/**
		* @var height of page format in points
		* @access protected
		*/
		protected $fhPt;

		/**
		* @var current width of page in points
		* @access protected
		*/
		protected $wPt;

		/**
		* @var current height of page in points
		* @access protected
		*/
		protected $hPt;

		/**
		* @var current width of page in user unit
		* @access protected
		*/
		protected $w;

		/**
		* @var current height of page in user unit
		* @access protected
		*/
		protected $h;

		/**
		* @var left margin
		* @access protected
		*/
		protected $lMargin;

		/**
		* @var top margin
		* @access protected
		*/
		protected $tMargin;

		/**
		* @var right margin
		* @access protected
		*/
		protected $rMargin;

		/**
		* @var page break margin
		* @access protected
		*/
		protected $bMargin;

		/**
		* @var cell internal padding
		* @access protected
		*/
		protected $cMargin;
		
		/**
		* @var cell internal padding (previous value)
		* @access protected
		*/
		protected $oldcMargin;

		/**
		* @var current horizontal position in user unit for cell positioning
		* @access protected
		*/
		protected $x;

		/**
		* @var current vertical position in user unit for cell positioning
		* @access protected
		*/
		protected $y;

		/**
		* @var height of last cell printed
		* @access protected
		*/
		protected $lasth;

		/**
		* @var line width in user unit
		* @access protected
		*/
		protected $LineWidth;

		/**
		* @var array of standard font names
		* @access protected
		*/
		protected $CoreFonts;

		/**
		* @var array of used fonts
		* @access protected
		*/
		protected $fonts = array();

		/**
		* @var array of font files
		* @access protected
		*/
		protected $FontFiles = array();

		/**
		* @var array of encoding differences
		* @access protected
		*/
		protected $diffs = array();

		/**
		* @var array of used images
		* @access protected
		*/
		protected $images = array();

		/**
		* @var array of links in pages
		* @access protected
		*/
		protected $PageLinks = array();

		/**
		* @var array of internal links
		* @access protected
		*/
		protected $links = array();

		/**
		* @var current font family
		* @access protected
		*/
		protected $FontFamily;

		/**
		* @var current font style
		* @access protected
		*/
		protected $FontStyle;
		
		/**
		* @var current font ascent (distance between font top and baseline)
		* @access protected
		* @since 2.8.000 (2007-03-29)
		*/
		protected $FontAscent;
		
		/**
		* @var current font descent (distance between font bottom and baseline)
		* @access protected
		* @since 2.8.000 (2007-03-29)
		*/
		protected $FontDescent;

		/**
		* @var underlining flag
		* @access protected
		*/
		protected $underline;

		/**
		* @var current font info
		* @access protected
		*/
		protected $CurrentFont;

		/**
		* @var current font size in points
		* @access protected
		*/
		protected $FontSizePt;

		/**
		* @var current font size in user unit
		* @access protected
		*/
		protected $FontSize;

		/**
		* @var commands for drawing color
		* @access protected
		*/
		protected $DrawColor;

		/**
		* @var commands for filling color
		* @access protected
		*/
		protected $FillColor;

		/**
		* @var commands for text color
		* @access protected
		*/
		protected $TextColor;

		/**
		* @var indicates whether fill and text colors are different
		* @access protected
		*/
		protected $ColorFlag;

		/**
		* @var word spacing
		* @access protected
		*/
		protected $ws;

		/**
		* @var automatic page breaking
		* @access protected
		*/
		protected $AutoPageBreak;

		/**
		* @var threshold used to trigger page breaks
		* @access protected
		*/
		protected $PageBreakTrigger;

		/**
		* @var flag set when processing footer
		* @access protected
		*/
		protected $InFooter;

		/**
		* @var zoom display mode
		* @access protected
		*/
		protected $ZoomMode;

		/**
		* @var layout display mode
		* @access protected
		*/
		protected $LayoutMode;

		/**
		* @var title
		* @access protected
		*/
		protected $title;

		/**
		* @var subject
		* @access protected
		*/
		protected $subject;

		/**
		* @var author
		* @access protected
		*/
		protected $author;

		/**
		* @var keywords
		* @access protected
		*/
		protected $keywords;

		/**
		* @var creator
		* @access protected
		*/
		protected $creator;

		/**
		* @var alias for total number of pages
		* @access protected
		*/
		protected $AliasNbPages;

		/**
		* @var right-bottom corner X coordinate of inserted image
		* @since 2002-07-31
		* @author Nicola Asuni
		* @access protected
		*/
		protected $img_rb_x;

		/**
		* @var right-bottom corner Y coordinate of inserted image
		* @since 2002-07-31
		* @author Nicola Asuni
		* @access protected
		*/
		protected $img_rb_y;

		/**
		* @var image scale factor
		* @since 2004-06-14
		* @author Nicola Asuni
		* @access protected
		*/
		protected $imgscale = 1;

		/**
		* @var boolean set to true when the input text is unicode (require unicode fonts)
		* @since 2005-01-02
		* @author Nicola Asuni
		* @access protected
		*/
		protected $isunicode = false;

		/**
		* @var PDF version
		* @since 1.5.3
		* @access protected
		*/
		protected $PDFVersion = "1.7";
		
		
		// ----------------------
		
		/**
		 * @var Minimum distance between header and top page margin.
		 * @access protected
		 */
		protected $header_margin;
		
		/**
		 * @var Minimum distance between footer and bottom page margin.
		 * @access protected
		 */
		protected $footer_margin;
		
		/**
		 * @var original left margin value
		 * @access protected
		 * @since 1.53.0.TC013
		 */
		protected $original_lMargin;
		
		/**
		 * @var original right margin value
		 * @access protected
		 * @since 1.53.0.TC013
		 */
		protected $original_rMargin;
			
		/**
		 * @var Header font.
		 * @access protected
		 */
		protected $header_font;
		
		/**
		 * @var Footer font.
		 * @access protected
		 */
		protected $footer_font;
		
		/**
		 * @var Language templates.
		 * @access protected
		 */
		protected $l;
		
		/**
		 * @var Barcode to print on page footer (only if set).
		 * @access protected
		 */
		protected $barcode = false;
		
		/**
		 * @var If true prints header
		 * @access protected
		 */
		protected $print_header = true;
		
		/**
		 * @var If true prints footer.
		 * @access protected
		 */
		protected $print_footer = true;
				
		/**
		 * @var Header image logo.
		 * @access protected
		 */
		protected $header_logo = "";
		
		/**
		 * @var Header image logo width in mm.
		 * @access protected
		 */
		protected $header_logo_width = 30;
		
		/**
		 * @var String to print as title on document header.
		 * @access protected
		 */
		protected $header_title = "";
		
		/**
		 * @var String to print on document header.
		 * @access protected
		 */
		protected $header_string = "";
		
		/**
		 * @var Default number of columns for html table.
		 * @access protected
		 */
		protected $default_table_columns = 4;
		
		
		// variables for html parser
		
		/**
		 * @var HTML PARSER: store current link.
		 * @access protected
		 */
		protected $HREF;
		
		/**
		 * @var store available fonts list.
		 * @access protected
		 */
		var $fontlist = array();
		
		/**
		 * @var current foreground color
		 * @access protected
		 */
		protected $fgcolor;
						
		/**
		 * @var HTML PARSER: array of boolean values, true in case of ordered list (OL), false otherwise.
		 * @access protected
		 */
		protected $listordered = array();
		
		/**
		 * @var HTML PARSER: array count list items on nested lists.
		 * @access protected
		 */
		protected $listcount = array();
		
		/**
		 * @var HTML PARSER: current list nesting level.
		 * @access protected
		 */
		protected $listnum = 0;
		
		/**
		 * @var HTML PARSER: indent amount for lists.
		 * @access protected
		 */
		protected $listindent;
		
		/**
		 * @var current background color
		 * @access protected
		 */
		protected $bgcolor;
		
		/**
		 * @var Store temporary font size in points.
		 * @access protected
		 */
		protected $tempfontsize = 10;
		
		/**
		 * @var Bold font style status.
		 * @access protected
		 */
		protected $b;
		
		/**
		 * @var Underlined font style status.
		 * @access protected
		 */
		protected $u;
		
		/**
		 * @var Italic font style status.
		 * @access protected
		 */
		protected $i;
		
		/**
		 * @var Line through font style status.
		 * @access protected
		 * @since 2.8.000 (2008-03-19)
		 */
		protected $d;
		
		/**
		 * @var spacer for LI tags.
		 * @access protected
		 */
		protected $lispacer = "";
		
		/**
		 * @var default encoding
		 * @access protected
		 * @since 1.53.0.TC010
		 */
		protected $encoding = "UTF-8";
		
		/**
		 * @var PHP internal encoding
		 * @access protected
		 * @since 1.53.0.TC016
		 */
		protected $internal_encoding;
		
		/**
		 * @var indicates if the document language is Right-To-Left
		 * @access protected
		 * @since 2.0.000
		 */
		protected $rtl = false;
		
		/**
		 * @var used to force RTL or LTR string inversion
		 * @access protected
		 * @since 2.0.000
		 */
		protected $tmprtl = false;
		
		// --- Variables used for document encryption:
		
		/**
		 * Indicates whether document is protected
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $encrypted;
		
		/**
		 * U entry in pdf document
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $Uvalue;
		
		/**
		 * O entry in pdf document
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $Ovalue;
		
		/**
		 * P entry in pdf document
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $Pvalue;
		
		/**
		 * encryption object id
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $enc_obj_id;
		
		/**
		 * last RC4 key encrypted (cached for optimisation)
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $last_rc4_key;
		
		/**
		 * last RC4 computed key
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $last_rc4_key_c;
		
		// --- bookmark ---
		
		/**
		 * Outlines for bookmark
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $outlines = array();
		
		/**
		 * Outline root for bookmark
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $OutlineRoot;
		
		
		// --- javascript and form ---
		
		/**
		 * javascript code
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $javascript = "";
		
		/**
		 * javascript counter
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $n_js;

		/**
		 * line trough state
		 * @access protected
		 * @since 2.8.000 (2008-03-19)
		 */
		protected $linethrough;

		// --- Variables used for User's Rights ---
		// See PDF reference chapter 8.7 Digital Signatures

		/**
		 * If true enables user's rights on PDF reader
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur;

		/**
		 * Names specifying additional document-wide usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_document;

		/**
		 * Names specifying additional annotation-related usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_annots;

		/**
		 * Names specifying additional form-field-related usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_form;

		/**
		 * Names specifying additional signature-related usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_signature;

		/**
		 * Dot Per Inch Document Resolution (do not change)
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $dpi = 72;
		
		/**
		 * Indicates whether a new page group was requested
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $newpagegroup;
		
		/**
		 * Contains the number of pages of the groups
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $pagegroups;
		
		/**
		 * Contains the alias of the current page group
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $currpagegroup; 
		
		/**
		 * Restrict the rendering of some elements to screen or printout.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $visibility="all";
		
		/**
		 * Print visibility.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $n_ocg_print;
		
		/**
		 * View visibility.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $n_ocg_view;
		
		/**
		 * Array of transparency objects and parameters.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $extgstates;
		
		/**
		 * Set the default JPEG compression quality (1-100)
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $jpeg_quality;
				
		/**
		 * Default cell height ratio.
		 * @access protected
		 * @since 3.0.014 (2008-05-23)
		 */
		protected $cell_height_ratio = K_CELL_HEIGHT_RATIO;
		
		/**
		 * PDF viewer preferences.
		 * @access protected
		 * @since 3.1.000 (2008-06-09)
		 */
		protected $viewer_preferences;
		
		/**
		 * A name object specifying how the document should be displayed when opened.
		 * @access protected
		 * @since 3.1.000 (2008-06-09)
		 */
		protected $PageMode;
		
		/**
		 * Array for storing gradient information.
		 * @access protected
		 * @since 3.1.000 (2008-06-09)
		 */
		protected $gradients = array();
		
		/**
		 * Array used to store positions inside the pages buffer.
		 * keys are the page numbers
		 * @access protected
		 * @since 3.2.000 (2008-06-26)
		 */
		protected $intmrk = array();
		
		/**
		 * Array used to store footer positions of each page.
		 * @access protected
		 * @since 3.2.000 (2008-07-01)
		 */
		protected $footerpos = array();
		
		
		/**
		 * Array used to store footer lenght of each page.
		 * @access protected
		 * @since 4.0.014 (2008-07-29)
		 */
		protected $footerlen = array();
		
		/**
		 * True if a newline is created.
		 * @access protected
		 * @since 3.2.000 (2008-07-01)
		 */
		protected $newline = true;
		
		/**
		 * End position of the latest inserted line
		 * @access protected
		 * @since 3.2.000 (2008-07-01)
		 */
		protected $endlinex = 0;
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleWidth = "";
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleCap = "0 J";
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleJoin = "0 j";
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleDash = "[] 0 d";
		
		/**
		 * True if marked-content sequence is open
		 * @access protected
		 * @since 4.0.013 (2008-07-28)
		 */
		protected $openMarkedContent = false;
		
		//------------------------------------------------------------
		// METHODS
		//------------------------------------------------------------

		/**
		 * This is the class constructor. 
		 * It allows to set up the page format, the orientation and 
		 * the measure unit used in all the methods (except for the font sizes).
		 * @since 1.0
		 * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li></ul>
		 * @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
		 * @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		 * @param boolean $unicode TRUE means that the input text is unicode (default = true)
		 * @param String $encoding charset encoding; default is UTF-8
		 */
		public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding="UTF-8") {
			/* Set internal character encoding to ASCII */
			if (function_exists("mb_internal_encoding") AND mb_internal_encoding()) {
				$this->internal_encoding = mb_internal_encoding();
				mb_internal_encoding("ASCII");
			}
			// set language direction
			$this->rtl = $this->l['a_meta_dir']=='rtl' ? true : false;
			$this->tmprtl = false;
			//Some checks
			$this->_dochecks();
			//Initialization of properties
			$this->isunicode = $unicode;
			$this->page = 0;
			$this->pagedim = array();
			$this->n = 2;
			$this->buffer = '';
			$this->pages = array();
			$this->state = 0;
			$this->fonts = array();
			$this->FontFiles = array();
			$this->diffs = array();
			$this->images = array();
			$this->links = array();
			$this->gradients = array();
			$this->InFooter = false;
			$this->lasth = 0;
			$this->FontFamily = 'helvetica';
			$this->FontStyle = '';
			$this->FontSizePt = 12;
			$this->underline = false;
			$this->linethrough = false;
			$this->DrawColor = '0 G';
			$this->FillColor = '0 g';
			$this->TextColor = '0 g';
			$this->ColorFlag = false;
			$this->ws = 0;
			// encryption values
			$this->encrypted = false;
			$this->last_rc4_key = '';
			$this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
			//Standard Unicode fonts
			$this->CoreFonts = array(
				'courier'=>'Courier',
				'courierB'=>'Courier-Bold',
				'courierI'=>'Courier-Oblique',
				'courierBI'=>'Courier-BoldOblique',
				'helvetica'=>'Helvetica',
				'helveticaB'=>'Helvetica-Bold',
				'helveticaI'=>'Helvetica-Oblique',
				'helveticaBI'=>'Helvetica-BoldOblique',
				'times'=>'Times-Roman',
				'timesB'=>'Times-Bold',
				'timesI'=>'Times-Italic',
				'timesBI'=>'Times-BoldItalic',
				'symbol'=>'Symbol',
				'zapfdingbats'=>'ZapfDingbats'
			);
			//Set scale factor
			$this->setPageUnit($unit);
			// set page format and orientation
			$this->setPageFormat($format, $orientation);
			//Page margins (1 cm)
			$margin = 28.35 / $this->k;
			$this->SetMargins($margin,$margin);
			//Interior cell margin (1 mm)
			$this->cMargin = $margin / 10;
			//Line width (0.2 mm)
			$this->LineWidth = 0.57 / $this->k;
			$this->linestyleWidth = sprintf('%.2f w', ($this->LineWidth * $this->k));
			$this->linestyleCap = "0 J";
			$this->linestyleJoin = "0 j";
			$this->linestyleDash = "[] 0 d";
			//Automatic page break
			$this->SetAutoPageBreak(true, 2*$margin);
			//Full width display mode
			$this->SetDisplayMode('fullwidth');
			//Compression
			$this->SetCompression(true);
			//Set default PDF version number
			$this->PDFVersion = "1.7";
			$this->encoding = $encoding;
			$this->HREF = '';
			$this->getFontsList();
			$this->fgcolor = array('R' => 0, 'G' => 0, 'B' => 0);
			$this->bgcolor = array('R' => 255, 'G' => 255, 'B' => 255);
			$this->extgstates = array();
			// user's rights
			$this->ur = false;
			$this->ur_document = "/FullSave";
			$this->ur_annots = "/Create/Delete/Modify/Copy/Import/Export";
			$this->ur_form = "/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate";
			$this->ur_signature = "/Modify";
			// set default JPEG quality
			$this->jpeg_quality = 75;
			// initialize some settings
			$this->utf8Bidi(array(""));
		}
		
		/**
		 * Default destructor.
		 * @since 1.53.0.TC016
		 */
		public function __destruct() {
			// restore internal encoding
			if (isset($this->internal_encoding) AND !empty($this->internal_encoding)) {
				mb_internal_encoding($this->internal_encoding);
			}
		}
		
		/**
		* Set the units of measure for the document.
		* @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
		* @since 3.0.015 (2008-06-06)
		*/
		public function setPageUnit($unit) {
		//Set scale factor
			switch (strtolower($unit)) {
				// points
				case 'pt': {
					$this->k = 1;
					break;
				}
				// millimeters
				case 'mm': {
					$this->k = $this->dpi / 25.4;
					break;
				}
				// centimeters
				case 'cm': {
					$this->k = $this->dpi / 2.54;
					break;
				}
				// inches
				case 'in': {
					$this->k = $this->dpi;
					break;
				}
				// unsupported unit
				default : {
					$this->Error('Incorrect unit: '.$unit);
					break;
				}
			}
			if (isset($this->CurOrientation)) {
					$this->setPageOrientation($this->CurOrientation);
			}
		}
		
		/**
		* Set the page format
		* @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @since 3.0.015 (2008-06-06)
		*/
		public function setPageFormat($format, $orientation="P") {
			//Page format
			if (is_string($format)) {
				// Page formats (45 standard ISO paper formats and 4 american common formats).
				// Paper cordinates are calculated in this way: (inches * 72) where (1 inch = 2.54 cm)
				switch (strtoupper($format)){
					case '4A0': {$format = array(4767.87,6740.79); break;}
					case '2A0': {$format = array(3370.39,4767.87); break;}
					case 'A0': {$format = array(2383.94,3370.39); break;}
					case 'A1': {$format = array(1683.78,2383.94); break;}
					case 'A2': {$format = array(1190.55,1683.78); break;}
					case 'A3': {$format = array(841.89,1190.55); break;}
					case 'A4': default: {$format = array(595.28,841.89); break;}
					case 'A5': {$format = array(419.53,595.28); break;}
					case 'A6': {$format = array(297.64,419.53); break;}
					case 'A7': {$format = array(209.76,297.64); break;}
					case 'A8': {$format = array(147.40,209.76); break;}
					case 'A9': {$format = array(104.88,147.40); break;}
					case 'A10': {$format = array(73.70,104.88); break;}
					case 'B0': {$format = array(2834.65,4008.19); break;}
					case 'B1': {$format = array(2004.09,2834.65); break;}
					case 'B2': {$format = array(1417.32,2004.09); break;}
					case 'B3': {$format = array(1000.63,1417.32); break;}
					case 'B4': {$format = array(708.66,1000.63); break;}
					case 'B5': {$format = array(498.90,708.66); break;}
					case 'B6': {$format = array(354.33,498.90); break;}
					case 'B7': {$format = array(249.45,354.33); break;}
					case 'B8': {$format = array(175.75,249.45); break;}
					case 'B9': {$format = array(124.72,175.75); break;}
					case 'B10': {$format = array(87.87,124.72); break;}
					case 'C0': {$format = array(2599.37,3676.54); break;}
					case 'C1': {$format = array(1836.85,2599.37); break;}
					case 'C2': {$format = array(1298.27,1836.85); break;}
					case 'C3': {$format = array(918.43,1298.27); break;}
					case 'C4': {$format = array(649.13,918.43); break;}
					case 'C5': {$format = array(459.21,649.13); break;}
					case 'C6': {$format = array(323.15,459.21); break;}
					case 'C7': {$format = array(229.61,323.15); break;}
					case 'C8': {$format = array(161.57,229.61); break;}
					case 'C9': {$format = array(113.39,161.57); break;}
					case 'C10': {$format = array(79.37,113.39); break;}
					case 'RA0': {$format = array(2437.80,3458.27); break;}
					case 'RA1': {$format = array(1729.13,2437.80); break;}
					case 'RA2': {$format = array(1218.90,1729.13); break;}
					case 'RA3': {$format = array(864.57,1218.90); break;}
					case 'RA4': {$format = array(609.45,864.57); break;}
					case 'SRA0': {$format = array(2551.18,3628.35); break;}
					case 'SRA1': {$format = array(1814.17,2551.18); break;}
					case 'SRA2': {$format = array(1275.59,1814.17); break;}
					case 'SRA3': {$format = array(907.09,1275.59); break;}
					case 'SRA4': {$format = array(637.80,907.09); break;}
					case 'LETTER': {$format = array(612.00,792.00); break;}
					case 'LEGAL': {$format = array(612.00,1008.00); break;}
					case 'EXECUTIVE': {$format = array(521.86,756.00); break;}
					case 'FOLIO': {$format = array(612.00,936.00); break;}
				}
				$this->fwPt = $format[0];
				$this->fhPt = $format[1];
			}
			else {
				$this->fwPt = $format[0] * $this->k;
				$this->fhPt = $format[1] * $this->k;
			}
			$this->setPageOrientation($orientation);
		}
		
		
		/**
		* Set page orientation.
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @param boolean $autopagebreak Boolean indicating if auto-page-break mode should be on or off.
		* @param float $bottommargin bottom margin of the page.
		* @since 3.0.015 (2008-06-06)
		*/
		public function setPageOrientation($orientation, $autopagebreak='', $bottommargin='') {
			$orientation = strtoupper($orientation);
			if (($orientation == 'P') OR ($orientation == 'PORTRAIT')) {
				$this->CurOrientation = 'P';
				$this->wPt = $this->fwPt;
				$this->hPt = $this->fhPt;
			} elseif (($orientation == 'L') OR ($orientation == 'LANDSCAPE')) {
				$this->CurOrientation = 'L';
				$this->wPt = $this->fhPt;
				$this->hPt = $this->fwPt;
			}
			else {
				$this->Error('Incorrect orientation: '.$orientation);
			}
			$this->w = $this->wPt / $this->k;
			$this->h = $this->hPt / $this->k;
			if (empty($autopagebreak)) {
				if (isset($this->AutoPageBreak)) {
					$autopagebreak = $this->AutoPageBreak;
				} else {
					$autopagebreak = true;
				}
			}
			if (empty($bottommargin)) {
				if (isset($this->bMargin)) {
					$bottommargin = $this->bMargin;
				} else {
					// default value = 2 cm
					$bottommargin = 2 * 28.35 / $this->k;
				}
			}
			$this->SetAutoPageBreak($autopagebreak, $bottommargin);
			// store page dimensions
			$this->pagedim[$this->page] = array('w' => $this->wPt, 'h' => $this->hPt, 'tm' => $this->tMargin, 'bm' => $bottommargin, 'lm' => $this->lMargin, 'rm' => $this->rMargin, 'pb' => $autopagebreak, 'or' => $this->CurOrientation);
		}
				
		/**
		 * Enable or disable Right-To-Left language mode
		 * @param Boolean $enable if true enable Right-To-Left language mode.
		 * @since 2.0.000 (2008-01-03)
		 */
		public function setRTL($enable) {
			$this->rtl = $enable ? true : false;
			$this->tmprtl = false;
		}
		
		/**
		 * Return the RTL status
		 * @return boolean
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getRTL() {
			return $this->rtl;
		}
		
		/**
		* Force temporary RTL language direction
		* @param mixed $mode can be false, 'L' for LTR or 'R' for RTL
		* @since 2.1.000 (2008-01-09)
		*/
		public function setTempRTL($mode) {
			switch ($mode) {
				case false:
				case 'L':
				case 'R': {
					$this->tmprtl = $mode;
				}
			}
		}
		
		/**
		* Set the last cell height.
		* @param float $h cell height.
		* @author Nicola Asuni
		* @since 1.53.0.TC034
		*/
		public function setLastH($h) {
			$this->lasth=$h;
		}
		
		/**
		* Set the image scale.
		* @param float $scale image scale.
		* @author Nicola Asuni
		* @since 1.5.2
		*/
		public function setImageScale($scale) {
			$this->imgscale=$scale;
		}

		/**
		* Returns the image scale.
		* @return float image scale.
		* @author Nicola Asuni
		* @since 1.5.2
		*/
		public function getImageScale() {
			return $this->imgscale;
		}

		/**
		* Returns the page width in units.
		* @return int page width.
		* @author Nicola Asuni
		* @since 1.5.2
		*/
		public function getPageWidth() {
			return $this->w;
		}

		/**
		* Returns the page height in units.
		* @return int page height.
		* @author Nicola Asuni
		* @since 1.5.2
		*/
		public function getPageHeight() {
			return $this->h;
		}

		/**
		* Returns the page break margin.
		* @return int page break margin.
		* @author Nicola Asuni
		* @since 1.5.2
		*/
		public function getBreakMargin() {
			return $this->bMargin;
		}

		/**
		* Returns the scale factor (number of points in user unit).
		* @return int scale factor.
		* @author Nicola Asuni
		* @since 1.5.2
		*/
		public function getScaleFactor() {
			return $this->k;
		}

		/**
		* Defines the left, top and right margins. By default, they equal 1 cm. Call this method to change them.
		* @param float $left Left margin.
		* @param float $top Top margin.
		* @param float $right Right margin. Default value is the left one.
		* @since 1.0
		* @see SetLeftMargin(), SetTopMargin(), SetRightMargin(), SetAutoPageBreak()
		*/
		public function SetMargins($left, $top, $right=-1) {
			//Set left, top and right margins
			$this->lMargin = $left;
			$this->tMargin = $top;
			if ($right == -1) {
				$right = $left;
			}
			$this->rMargin = $right;
		}

		/**
		* Defines the left margin. The method can be called before creating the first page. If the current abscissa gets out of page, it is brought back to the margin.
		* @param float $margin The margin.
		* @since 1.4
		* @see SetTopMargin(), SetRightMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetLeftMargin($margin) {
			//Set left margin
			$this->lMargin=$margin;
			if (($this->page > 0) AND ($this->x < $margin)) {
				$this->x = $margin;
			}
		}

		/**
		* Defines the top margin. The method can be called before creating the first page.
		* @param float $margin The margin.
		* @since 1.5
		* @see SetLeftMargin(), SetRightMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetTopMargin($margin) {
			//Set top margin
			$this->tMargin=$margin;
			if (($this->page > 0) AND ($this->y < $margin)) {
				$this->y = $margin;
			}
		}

		/**
		* Defines the right margin. The method can be called before creating the first page.
		* @param float $margin The margin.
		* @since 1.5
		* @see SetLeftMargin(), SetTopMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetRightMargin($margin) {
			$this->rMargin=$margin;
			if (($this->page > 0) AND ($this->x > ($this->w - $margin))) {
				$this->x = $this->w - $margin;
			}
		}

		/**
		* Set the internal Cell padding.
		* @param float $pad internal padding.
		* @since 2.1.000 (2008-01-09)
		* @see Cell(), SetLeftMargin(), SetTopMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetCellPadding($pad) {
			$this->cMargin = $pad;
		}

		/**
		* Enables or disables the automatic page breaking mode. When enabling, the second parameter is the distance from the bottom of the page that defines the triggering limit. By default, the mode is on and the margin is 2 cm.
		* @param boolean $auto Boolean indicating if mode should be on or off.
		* @param float $margin Distance from the bottom of the page.
		* @since 1.0
		* @see Cell(), MultiCell(), AcceptPageBreak()
		*/
		public function SetAutoPageBreak($auto, $margin=0) {
			//Set auto page break mode and triggering margin
			$this->AutoPageBreak = $auto;
			$this->bMargin = $margin;
			$this->PageBreakTrigger = $this->h - $margin;
		}

		/**
		* Defines the way the document is to be displayed by the viewer.
		* @param mixed $zoom The zoom to use. It can be one of the following string values or a number indicating the zooming factor to use. <ul><li>fullpage: displays the entire page on screen </li><li>fullwidth: uses maximum width of window</li><li>real: uses real size (equivalent to 100% zoom)</li><li>default: uses viewer default mode</li></ul>
		* @param string $layout The page layout. Possible values are:<ul><li>SinglePage Display one page at a time</li><li>OneColumn Display the pages in one column</li><li>TwoColumnLeft Display the pages in two columns, with odd-numbered pages on the left</li><li>TwoColumnRight Display the pages in two columns, with odd-numbered pages on the right</li><li>TwoPageLeft (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the left</li><li>TwoPageRight (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the right</li></ul>
		* @param string $mode A name object specifying how the document should be displayed when opened:<ul><li>UseNone Neither document outline nor thumbnail images visible</li><li>UseOutlines Document outline visible</li><li>UseThumbs Thumbnail images visible</li><li>FullScreen Full-screen mode, with no menu bar, window controls, or any other window visible</li><li>UseOC (PDF 1.5) Optional content group panel visible</li><li>UseAttachments (PDF 1.6) Attachments panel visible</li></ul>
		* @since 1.2
		*/
		public function SetDisplayMode($zoom, $layout='SinglePage', $mode="UseNone") {
			//Set display mode in viewer
			if (($zoom == 'fullpage') OR ($zoom == 'fullwidth') OR ($zoom == 'real') OR ($zoom == 'default') OR (!is_string($zoom))) {
				$this->ZoomMode = $zoom;
			} else {
				$this->Error('Incorrect zoom display mode: '.$zoom);
			}
			switch ($layout) {
				case "default":
				case "single":
				case "SinglePage": {
					$this->LayoutMode = "SinglePage";
					break;
				}
				case "continuous":
				case "OneColumn": {
					$this->LayoutMode = "OneColumn";
					break;
				}
				case "two":
				case "TwoColumnLeft": {
					$this->LayoutMode = "TwoColumnLeft";
					break;
				}
				case "TwoColumnRight": {
					$this->LayoutMode = "TwoColumnRight";
					break;
				}
				case "TwoPageLeft": {
					$this->LayoutMode = "TwoPageLeft";
					break;
				}
				case "TwoPageRight": {
					$this->LayoutMode = "TwoPageRight";
					break;
				}
				default: {
					$this->LayoutMode = "SinglePage";
				}
			}
			switch ($mode) {
				case "UseNone": {
					$this->PageMode = "UseNone";
					break;
				}
				case "UseOutlines": {
					$this->PageMode = "UseOutlines";
					break;
				}
				case "UseThumbs": {
					$this->PageMode = "UseThumbs";
					break;
				}
				case "FullScreen": {
					$this->PageMode = "FullScreen";
					break;
				}
				case "UseOC": {
					$this->PageMode = "UseOC";
					break;
				}
				case "": {
					$this->PageMode = "UseAttachments";
					break;
				}
				default: {
					$this->PageMode = "UseNone";
				}
			}
		}

		/**
		* Activates or deactivates page compression. When activated, the internal representation of each page is compressed, which leads to a compression ratio of about 2 for the resulting document. Compression is on by default.
		* Note: the Zlib extension is required for this feature. If not present, compression will be turned off.
		* @param boolean $compress Boolean indicating if compression must be enabled.
		* @since 1.4
		*/
		public function SetCompression($compress) {
			//Set page compression
			if (function_exists('gzcompress')) {
				$this->compress = $compress;
			} else {
				$this->compress = false;
			}
		}

		/**
		* Defines the title of the document.
		* @param string $title The title.
		* @since 1.2
		* @see SetAuthor(), SetCreator(), SetKeywords(), SetSubject()
		*/
		public function SetTitle($title) {
			//Title of document
			$this->title = $title;
		}

		/**
		* Defines the subject of the document.
		* @param string $subject The subject.
		* @since 1.2
		* @see SetAuthor(), SetCreator(), SetKeywords(), SetTitle()
		*/
		public function SetSubject($subject) {
			//Subject of document
			$this->subject = $subject;
		}

		/**
		* Defines the author of the document.
		* @param string $author The name of the author.
		* @since 1.2
		* @see SetCreator(), SetKeywords(), SetSubject(), SetTitle()
		*/
		public function SetAuthor($author) {
			//Author of document
			$this->author = $author;
		}

		/**
		* Associates keywords with the document, generally in the form 'keyword1 keyword2 ...'.
		* @param string $keywords The list of keywords.
		* @since 1.2
		* @see SetAuthor(), SetCreator(), SetSubject(), SetTitle()
		*/
		public function SetKeywords($keywords) {
			//Keywords of document
			$this->keywords = $keywords;
		}

		/**
		* Defines the creator of the document. This is typically the name of the application that generates the PDF.
		* @param string $creator The name of the creator.
		* @since 1.2
		* @see SetAuthor(), SetKeywords(), SetSubject(), SetTitle()
		*/
		public function SetCreator($creator) {
			//Creator of document
			$this->creator = $creator;
		}

		/**
		* Defines an alias for the total number of pages. It will be substituted as the document is closed.<br />
		* <b>Example:</b><br />
		* <pre>
		* 		$this->Cell(0,10,'Page '.$pdf->PageNo().'/{nb}',0,0,'C');
		* </pre>
		* @param string $alias The alias. Default value: {nb}.
		* @since 1.4
		* @see PageNo(), Footer()
		*/
		public function AliasNbPages($alias='{nb}') {
			//Define an alias for total number of pages
			$this->AliasNbPages = $this->_escapetext($alias);
		}

		/**
		* This method is automatically called in case of fatal error; it simply outputs the message and halts the execution. An inherited class may override it to customize the error handling but should always halt the script, or the resulting document would probably be invalid.
		* 2004-06-11 :: Nicola Asuni : changed bold tag with strong
		* @param string $msg The error message
		* @since 1.0
		*/
		public function Error($msg) {
			//Fatal error
			die('<strong>TCPDF error: </strong>'.$msg);
		}

		/**
		* This method begins the generation of the PDF document. It is not necessary to call it explicitly because AddPage() does it automatically.
		* Note: no page is created by this method
		* @since 1.0
		* @see AddPage(), Close()
		*/
		public function Open() {
			//Begin document
			$this->state = 1;
		}

		/**
		* Terminates the PDF document. It is not necessary to call this method explicitly because Output() does it automatically. If the document contains no page, AddPage() is called to prevent from getting an invalid document.
		* @since 1.0
		* @see Open(), Output()
		*/
		public function Close() {
			//Terminate document
			if ($this->state == 3) {
				return;
			}
			if ($this->page == 0) {
				$this->AddPage();
			}
			//Page footer
			$this->setFooter();
			//Close page
			$this->_endpage();
			//Close document
			$this->_enddoc();
		}
		
		/**
		* Move pointer at the specified document page and update page dimensions.
		* @param int $pnum page number
		* @param boolean $resetmargins if true reset left, right, top margins and Y position.
		* @since 2.1.000 (2008-01-07)
		* @see getPage(), lastpage(), getNumPages()
		*/
		public function setPage($pnum, $resetmargins=false) {
			if (($pnum > 0) AND ($pnum <= count($this->pages))) {
				$this->page = $pnum;
				$this->wPt = $this->pagedim[$this->page]['w'];
				$this->hPt = $this->pagedim[$this->page]['h'];
				$this->w = $this->wPt / $this->k;
				$this->h = $this->hPt / $this->k;
				$this->tMargin = $this->pagedim[$this->page]['tm'];
				$this->bMargin = $this->pagedim[$this->page]['bm'];
				$this->AutoPageBreak = $this->pagedim[$this->page]['pb'];
				$this->CurOrientation = $this->pagedim[$this->page]['or'];
				$this->SetAutoPageBreak($this->AutoPageBreak, $this->bMargin);
				if ($resetmargins) {
					$this->lMargin = $this->pagedim[$this->page]['lm'];
					$this->rMargin = $this->pagedim[$this->page]['rm'];
					$this->SetY($this->tMargin);
				}
			} else {
				$this->Error('Wrong page number on setPage() function.');
			}
		}
		
		/**
		* Reset pointer to the last document page.
		* @since 2.0.000 (2008-01-04)
		* @see setPage(), getPage(), getNumPages()
		*/
		public function lastPage() {
			$this->setPage($this->getNumPages());
		}
		
		/**
		* Get current document page number.
		* @return int page number
		* @since 2.1.000 (2008-01-07)
		* @see setPage(), lastpage(), getNumPages()
		*/
		public function getPage() {
			return $this->page;
		}
		
		
		/**
		* Get the total number of insered pages.
		* @return int number of pages
		* @since 2.1.000 (2008-01-07)
		* @see setPage(), getPage(), lastpage()
		*/
		public function getNumPages() {
			return count($this->pages);
		}

		/**
		* Adds a new page to the document. If a page is already present, the Footer() method is called first to output the footer. Then the page is added, the current position set to the top-left corner according to the left and top margins, and Header() is called to display the header.
		* The font which was set before calling is automatically restored. There is no need to call SetFont() again if you want to continue with the same font. The same is true for colors and line width.
		* The origin of the coordinate system is at the top-left corner and increasing ordinates go downwards.
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		* @since 1.0
		* @see TCPDF(), Header(), Footer(), SetMargins()
		*/
		public function AddPage($orientation='', $format='') {
			if (!isset($this->original_lMargin)) {
				$this->original_lMargin = $this->lMargin;
			}
			if (!isset($this->original_rMargin)) {
				$this->original_rMargin = $this->rMargin;
			}
			if (count($this->pages) > $this->page) {
				// this page has been already added
				$this->setPage(($this->page + 1));
				return;
			}
			//Start a new page
			if ($this->state == 0) {
				$this->Open();
			}
			// save current settings
			$font_family = $this->FontFamily;
			$font_style = $this->FontStyle.($this->underline ? 'U' : '').($this->linethrough ? 'D' : '');
			$font_size = $this->FontSizePt;
			$prev_rMargin = $this->rMargin;
			$prev_lMargin = $this->lMargin;
			$prev_cMargin = $this->cMargin;
			$prev_linestyleWidth = $this->linestyleWidth;
			$prev_linestyleCap = $this->linestyleCap;
			$prev_linestyleJoin = $this->linestyleJoin;
			$prev_linestyleDash = $this->linestyleDash;
			$prev_DrawColor = $this->DrawColor;
			$prev_FillColor = $this->FillColor;
			$prev_TextColor = $this->TextColor;
			$prev_ColorFlag = $this->ColorFlag;
			if ($this->page > 0) {
				//Page footer
				$this->setFooter();
				//Close page
				$this->_endpage();
			}
			//Start new page
			$this->_beginpage($orientation, $format);
			// restore graphic styles
			$this->_out("".$prev_linestyleWidth." ".$prev_linestyleCap." ".$prev_linestyleJoin." ".$prev_linestyleDash." ".$prev_DrawColor." ".$prev_FillColor."");
			if (!empty($font_family)) {
				$this->SetFont($font_family, $font_style, $font_size);
			}
			//Page header
			$this->setHeader();
			// restore graphic styles
			$this->_out("".$prev_linestyleWidth." ".$prev_linestyleCap." ".$prev_linestyleJoin." ".$prev_linestyleDash." ".$prev_DrawColor." ".$prev_FillColor."");
			if (!empty($font_family)) {
				$this->SetFont($font_family, $font_style, $font_size);
			}
			// restore settings
			$this->FontFamily = $font_family;
			$this->FontStyle = $font_style;
			$this->FontSizePt = $font_size;
			$this->rMargin = $prev_rMargin;
			$this->lMargin = $prev_lMargin;
			$this->cMargin = $prev_cMargin;
			$this->linestyleWidth = $prev_linestyleWidth;
			$this->linestyleCap = $prev_linestyleCap;
			$this->linestyleJoin = $prev_linestyleJoin;
			$this->linestyleDash = $prev_linestyleDash;
			$this->DrawColor = $prev_DrawColor;
			$this->FillColor = $prev_FillColor;
			$this->TextColor = $prev_TextColor;
			$this->ColorFlag = $prev_ColorFlag;
			// mark this point
			$this->intmrk[$this->page] = strlen($this->pages[$this->page]);
		}
		
		/**
	 	 * Set header data.
		 * @param string $ln header image logo
		 * @param string $lw header image logo width in mm
		 * @param string $ht string to print as title on document header
		 * @param string $hs string to print on document header
		*/
		public function setHeaderData($ln="", $lw=0, $ht="", $hs="") {
			$this->header_logo = $ln;
			$this->header_logo_width = $lw;
			$this->header_title = $ht;
			$this->header_string = $hs;
		}
		
		/**
	 	 * Returns header data:
	 	 * <ul><li>$ret['logo'] = logo image</li><li>$ret['logo_width'] = width of the image logo in user units</li><li>$ret['title'] = header title</li><li>$ret['string'] = header description string</li></ul>
		 * @return array()
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getHeaderData() {
			$ret = array();
			$ret['logo'] = $this->header_logo;
			$ret['logo_width'] = $this->header_logo_width;
			$ret['title'] = $this->header_title;
			$ret['string'] = $this->header_string;
			return $ret;
		}
		
		/**
	 	 * Set header margin.
		 * (minimum distance between header and top page margin)
		 * @param int $hm distance in user units
		*/
		public function setHeaderMargin($hm=10) {
			$this->header_margin = $hm;
		}
		
		/**
	 	 * Returns header margin in user units.
		 * @return float
		 * @since 4.0.012 (2008-07-24)
		*/
		public function getHeaderMargin() {
			return $this->header_margin;
		}
		
		/**
	 	 * Set footer margin.
		 * (minimum distance between footer and bottom page margin)
		 * @param int $fm distance in user units
		*/
		public function setFooterMargin($fm=10) {
			$this->footer_margin = $fm;
		}
		
		/**
	 	 * Returns footer margin in user units.
		 * @return float
		 * @since 4.0.012 (2008-07-24)
		*/
		public function getFooterMargin() {
			return $this->footer_margin;
		}
		/**
	 	 * Set a flag to print page header.
		 * @param boolean $val set to true to print the page header (default), false otherwise. 
		 */
		public function setPrintHeader($val=true) {
			$this->print_header = $val;
		}
		
		/**
	 	 * Set a flag to print page footer.
		 * @param boolean $value set to true to print the page footer (default), false otherwise. 
		 */
		public function setPrintFooter($val=true) {
			$this->print_footer = $val;
		}
		
		/**
	 	 * Return the right-bottom (or left-bottom for RTL) corner X coordinate of last inserted image
		 * @return float 
		 */
		public function getImageRBX() {
			return $this->img_rb_x;
		}
		
		/**
	 	 * Return the right-bottom (or left-bottom for RTL) corner Y coordinate of last inserted image
		 * @return float 
		 */
		public function getImageRBY() {
			return $this->img_rb_y;
		}
		
		/**
	 	 * This method is used to render the page header.
	 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
		 */
		public function Header() {
			$ormargins = $this->getOriginalMargins();
			$headerfont = $this->getHeaderFont();
			$headerdata = $this->getHeaderData();
			if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
				$this->Image(K_PATH_IMAGES.$headerdata['logo'], $this->GetX(), $this->getHeaderMargin(), $headerdata['logo_width']);
				$imgy = $this->getImageRBY();
			} else {
				$imgy = $this->GetY();
			}
			$cell_height = round(($this->getCellHeightRatio() * $headerfont[2]) / $this->getScaleFactor(), 2);
			// set starting margin for text data cell
			if ($this->getRTL()) {
				$header_x = $ormargins['right'] + ($headerdata['logo_width'] * 1.1);
			} else {
				$header_x = $ormargins['left'] + ($headerdata['logo_width'] * 1.1);
			}
			$this->SetTextColor(0, 0, 0);
			// header title
			$this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
			$this->SetX($header_x);
			$this->Cell(0, $cell_height, $headerdata['title'], 0, 1, '');
			// header string
			$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
			$this->SetX($header_x);
			$this->MultiCell(0, $cell_height, $headerdata['string'], 0, '', 0, 1, 0, 0, true, 0);
			// print an ending header line
			$this->SetLineStyle(array("width" => 0.85 / $this->getScaleFactor(), "cap" => "butt", "join" => "miter", "dash" => 0, "color" => array(0, 0, 0)));
			$this->SetY(1 + max($imgy, $this->GetY()));
			if ($this->getRTL()) {
				$this->SetX($ormargins['right']);
			} else {
				$this->SetX($ormargins['left']);
			}
			$this->Cell(0, 0, '', 'T', 0, 'C');
		}
		
		/**
	 	 * This method is used to render the page footer. 
	 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
		 */
		public function Footer() {				
			$cur_y = $this->GetY();
			$ormargins = $this->getOriginalMargins();
			$this->SetTextColor(0, 0, 0);			
			//set style for cell border
			$line_width = 0.85 / $this->getScaleFactor();
			$this->SetLineStyle(array("width" => $line_width, "cap" => "butt", "join" => "miter", "dash" => 0, "color" => array(0, 0, 0)));
			//print document barcode
			$barcode = $this->getBarcode();
			if (!empty($barcode)) {
				$this->Ln();
				$barcode_width = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right'])/3);
				$this->write1DBarcode($barcode, "C128B", $this->GetX(), $cur_y + $line_width, $barcode_width, (($this->getFooterMargin() / 3) - $line_width), 0.3, '', '');	
			}
			$pagenumtxt = $this->l['w_page']." ".$this->PageNo().' / {nb}';
			$this->SetY($cur_y);
			//Print page number
			if ($this->getRTL()) {
				$this->SetX($ormargins['right']);
				$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
			} else {
				$this->SetX($ormargins['left']);
				$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'R');
			}
		}
		
		/**
	 	 * This method is used to render the page header. 
	 	 * @access protected
	 	 * @since 4.0.012 (2008-07-24)
		 */
		protected function setHeader() {
			if ($this->print_header) {
				$this->_out("q");
				$this->rMargin = $this->original_rMargin;
				$this->lMargin = $this->original_lMargin;
				//set current position
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $this->header_margin);
				} else {
					$this->SetXY($this->original_lMargin, $this->header_margin);
				}
				$this->SetFont($this->header_font[0], $this->header_font[1], $this->header_font[2]);
				$this->Header();
				//restore position
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $this->tMargin);
				} else {
					$this->SetXY($this->original_lMargin, $this->tMargin);
				}
				$this->_out("Q");
			}
		}
		
		/**
	 	 * This method is used to render the page footer. 
	 	 * @access protected
	 	 * @since 4.0.012 (2008-07-24)
		 */
		protected function setFooter() {
			//Page footer
			$this->InFooter = true;
			// mark this point
			$this->footerpos[$this->page] = strlen($this->pages[$this->page]);
			if ($this->print_footer) {
				$this->_out("q");
				$this->rMargin = $this->original_rMargin;
				$this->lMargin = $this->original_lMargin;
				//set current position
				$footer_y = $this->h - $this->footer_margin;
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $footer_y);
				} else {
					$this->SetXY($this->original_lMargin, $footer_y);
				}
				$this->SetFont($this->footer_font[0], $this->footer_font[1] , $this->footer_font[2]);
				$this->Footer();
				//restore position
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $this->tMargin);
				} else {
					$this->SetXY($this->original_lMargin, $this->tMargin);
				}
				$this->_out("Q");
			}
			$this->footerlen[$this->page] = strlen($this->pages[$this->page]) - $this->footerpos[$this->page];
			$this->InFooter = false;
		}
		
		/**
		* Returns the current page number.
		* @return int page number
		* @since 1.0
		* @see AliasNbPages()
		*/
		public function PageNo() {
			return $this->page;
		}
		
		/**
		* Defines the color used for all drawing operations (lines, rectangles and cell borders). 
		* It can be expressed in RGB components or gray scale. 
		* The method can be called before the first page is created and the value is retained from page to page.
		* @param array $color array of colors
		* @since 3.1.000 (2008-6-11)
		* @see SetDrawColor()
		*/
		public function SetDrawColorArray($color) {
			if (isset($color)) {
				$color = array_values($color);
				$r = isset($color[0]) ? $color[0] : -1;
				$g = isset($color[1]) ? $color[1] : -1;
				$b = isset($color[2]) ? $color[2] : -1;
				$k = isset($color[3]) ? $color[3] : -1;
				if ($r >= 0) {
					$this->SetDrawColor($r, $g, $b, $k);
				}
			}
		}

		/**
		* Defines the color used for all drawing operations (lines, rectangles and cell borders). It can be expressed in RGB components or gray scale. The method can be called before the first page is created and the value is retained from page to page.
		* @param int $col1 Gray level for single color, or Red color for RGB, or Cyan color for CMYK. Value between 0 and 255
		* @param int $col2 Green color for RGB, or Magenta color for CMYK. Value between 0 and 255
		* @param int $col3 Blue color for RGB, or Yellow color for CMYK. Value between 0 and 255
		* @param int $col4 Key (Black) color for CMYK. Value between 0 and 255
		* @since 1.3
		* @see SetDrawColorArray(), SetFillColor(), SetTextColor(), Line(), Rect(), Cell(), MultiCell()
		*/
		public function SetDrawColor($col1=0, $col2=-1, $col3=-1, $col4=-1) {
			// set default values
			if (!is_numeric($col1)) {
				$col1 = 0;
			}
			if (!is_numeric($col2)) {
				$col2 = -1;
			}
			if (!is_numeric($col3)) {
				$col3 = -1;
			}
			if (!is_numeric($col4)) {
				$col4 = -1;
			}
			//Set color for all stroking operations
			if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
				// Grey scale
				$this->DrawColor=sprintf('%.3f G', $col1/255);
			} elseif ($col4 == -1) {
				// RGB
				$this->DrawColor=sprintf('%.3f %.3f %.3f RG', $col1/255, $col2/255, $col3/255);
			} else {
				// CMYK
				$this->DrawColor = sprintf('%.3f %.3f %.3f %.3f K', $col1/100, $col2/100, $col3/100, $col4/100);
			}
			if ($this->page>0) {
				$this->_out($this->DrawColor);
			}
		}
		
		/**
		* Defines the color used for all filling operations (filled rectangles and cell backgrounds). 
		* It can be expressed in RGB components or gray scale. 
		* The method can be called before the first page is created and the value is retained from page to page.
		* @param array $color array of colors
		* @since 3.1.000 (2008-6-11)
		* @see SetFillColor()
		*/
		public function SetFillColorArray($color) {
			if (isset($color)) {
				$color = array_values($color);
				$r = isset($color[0]) ? $color[0] : -1;
				$g = isset($color[1]) ? $color[1] : -1;
				$b = isset($color[2]) ? $color[2] : -1;
				$k = isset($color[3]) ? $color[3] : -1;
				if ($r >= 0) {
					$this->SetFillColor($r, $g, $b, $k);
				}
			}
		}
		
		/**
		* Defines the color used for all filling operations (filled rectangles and cell backgrounds). It can be expressed in RGB components or gray scale. The method can be called before the first page is created and the value is retained from page to page.
		* @param int $col1 Gray level for single color, or Red color for RGB, or Cyan color for CMYK. Value between 0 and 255
		* @param int $col2 Green color for RGB, or Magenta color for CMYK. Value between 0 and 255
		* @param int $col3 Blue color for RGB, or Yellow color for CMYK. Value between 0 and 255
		* @param int $col4 Key (Black) color for CMYK. Value between 0 and 255
		* @since 1.3
		* @see SetFillColorArray(), SetDrawColor(), SetTextColor(), Rect(), Cell(), MultiCell()
		*/
		public function SetFillColor($col1=0, $col2=-1, $col3=-1, $col4=-1) {
			// set default values
			if (!is_numeric($col1)) {
				$col1 = 0;
			}
			if (!is_numeric($col2)) {
				$col2 = -1;
			}
			if (!is_numeric($col3)) {
				$col3 = -1;
			}
			if (!is_numeric($col4)) {
				$col4 = -1;
			}
			//Set color for all filling operations
			if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
				// Grey scale
				$this->FillColor = sprintf('%.3f g', $col1/255);
				$this->bgcolor = array('G' => $col1);
			} elseif ($col4 == -1) {
				// RGB
				$this->FillColor = sprintf('%.3f %.3f %.3f rg', $col1/255, $col2/255, $col3/255);
				$this->bgcolor = array('R' => $col1, 'G' => $col2, 'B' => $col3);
			} else {
				// CMYK
				$this->FillColor = sprintf('%.3f %.3f %.3f %.3f k', $col1/100, $col2/100, $col3/100, $col4/100);
				$this->bgcolor = array('C' => $col1, 'M' => $col2, 'Y' => $col3, 'K' => $col4);
			}
			$this->ColorFlag = ($this->FillColor != $this->TextColor);
			if ($this->page>0) {
				$this->_out($this->FillColor);
			}
		}
		
		/**
		* Defines the color used for text. It can be expressed in RGB components or gray scale. 
		* The method can be called before the first page is created and the value is retained from page to page.
		* @param array $color array of colors
		* @since 3.1.000 (2008-6-11)
		* @see SetFillColor()
		*/
		public function SetTextColorArray($color) {
			if (isset($color)) {
				$color = array_values($color);
				$r = isset($color[0]) ? $color[0] : -1;
				$g = isset($color[1]) ? $color[1] : -1;
				$b = isset($color[2]) ? $color[2] : -1;
				$k = isset($color[3]) ? $color[3] : -1;
				if ($r >= 0) {
					$this->SetTextColor($r, $g, $b, $k);
				}
			}
		}

		/**
		* Defines the color used for text. It can be expressed in RGB components or gray scale. The method can be called before the first page is created and the value is retained from page to page.
		* @param int $col1 Gray level for single color, or Red color for RGB, or Cyan color for CMYK. Value between 0 and 255
		* @param int $col2 Green color for RGB, or Magenta color for CMYK. Value between 0 and 255
		* @param int $col3 Blue color for RGB, or Yellow color for CMYK. Value between 0 and 255
		* @param int $col4 Key (Black) color for CMYK. Value between 0 and 255
		* @since 1.3
		* @see SetTextColorArray(), SetDrawColor(), SetFillColor(), Text(), Cell(), MultiCell()
		*/
		public function SetTextColor($col1=0, $col2=-1, $col3=-1, $col4=-1) {
			// set default values
			if (!is_numeric($col1)) {
				$col1 = 0;
			}
			if (!is_numeric($col2)) {
				$col2 = -1;
			}
			if (!is_numeric($col3)) {
				$col3 = -1;
			}
			if (!is_numeric($col4)) {
				$col4 = -1;
			}
			//Set color for text
			if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
				// Grey scale
				$this->TextColor = sprintf('%.3f g', $col1/255);
				$this->fgcolor = array('G' => $col1);
			} elseif ($col4 == -1) {
				// RGB
				$this->TextColor = sprintf('%.3f %.3f %.3f rg', $col1/255, $col2/255, $col3/255);
				$this->fgcolor = array('R' => $col1, 'G' => $col2, 'B' => $col3);
			} else {
				// CMYK
				$this->TextColor = sprintf('%.3f %.3f %.3f %.3f k', $col1/100, $col2/100, $col3/100, $col4/100);
				$this->fgcolor = array('C' => $col1, 'M' => $col2, 'Y' => $col3, 'K' => $col4);
			}
			$this->ColorFlag = ($this->FillColor != $this->TextColor);
		}

		/**
		* Returns the length of a string in user unit. A font must be selected.<br>
		* @param string $s The string whose length is to be computed
		* @param string $fontname Family font. It can be either a name defined by AddFont() or one of the standard families. It is also possible to pass an empty string, in that case, the current family is retained.
		* @param string $fontstyle Font style. Possible values are (case insensitive):<ul><li>empty string: regular</li><li>B: bold</li><li>I: italic</li><li>U: underline</li><li>D: line trough</li></ul> or any combination. The default value is regular.
		* @param float $fontsize Font size in points. The default value is the current size.
		* @return int string length
		* @author Nicola Asuni
		* @since 1.2
		*/
		public function GetStringWidth($s, $fontname='', $fontstyle='', $fontsize=0) {
			return $this->GetArrStringWidth($this->utf8Bidi($this->UTF8StringToArray($s), $this->tmprtl), $fontname, $fontstyle, $fontsize);
		}
		
		/**
		* Returns the string length of an array of chars in user unit. A font must be selected.<br>
		* @param string $arr The array of chars whose total length is to be computed
		* @param string $fontname Family font. It can be either a name defined by AddFont() or one of the standard families. It is also possible to pass an empty string, in that case, the current family is retained.
		* @param string $fontstyle Font style. Possible values are (case insensitive):<ul><li>empty string: regular</li><li>B: bold</li><li>I: italic</li><li>U: underline</li><li>D: line trough</li></ul> or any combination. The default value is regular.
		* @param float $fontsize Font size in points. The default value is the current size.
		* @return int string length
		* @author Nicola Asuni
		* @since 2.4.000 (2008-03-06)
		*/
		public function GetArrStringWidth($sa, $fontname='', $fontstyle='', $fontsize=0) {
			// store current values
			if (!empty($fontname)) {
				$prev_FontFamily = $this->FontFamily;
				$prev_FontStyle = $this->FontStyle;
				$prev_FontSizePt = $this->FontSizePt;
				$this->SetFont($fontname, $fontstyle, $fontsize);
			}
			$w = 0;
			foreach($sa as $char) {
				$w += $this->GetCharWidth($char);
			}
			// restore previous values
			if (!empty($fontname)) {
				$this->SetFont($prev_FontFamily, $prev_FontStyle, $prev_FontSizePt);
			}
			return $w;
		}
		
		/**
		* Returns the length of the char in user unit for the current font.<br>
		* @param string $char The char whose length is to be returned
		* @return int char width
		* @author Nicola Asuni
		* @since 2.4.000 (2008-03-06)
		*/
		public function GetCharWidth($char) {
			$cw = &$this->CurrentFont['cw'];
			if (isset($cw[$char])) {
				$w = $cw[$char];
				/*
			} elseif (isset($cw[ord($char)])) {
				$w = $cw[ord($char)];
			} elseif (isset($cw[chr($char)])) {
				$w = $cw[chr($char)];
				*/
			} elseif (isset($this->CurrentFont['dw'])) {
				$w = $this->CurrentFont['dw'];
			} elseif (isset($this->CurrentFont['desc']['MissingWidth'])) {
				$w = $this->CurrentFont['desc']['MissingWidth']; // set default size
			} else {
				$w = 500; // default width
			}
			return ($w * $this->FontSize / 1000);
		}
		
		/**
		* Returns the numbero of characters in a string.
		* @param string $s The input string.
		* @return int number of characters
		* @since 2.0.0001 (2008-01-07)
		*/
		public function GetNumChars($s) {
			if ($this->isunicode) {
				return count($this->UTF8StringToArray($s));
			} 
			return strlen($s);
		}
			
		/**
		* Fill the list of available fonts ($this->fontlist).
		* @access protected
		* @since 4.0.013 (2008-07-28)
		*/
		protected function getFontsList() {
			$fontsdir = opendir($this->_getfontpath());
			while (($file = readdir($fontsdir)) !== false) {
				if (substr($file, -4) == ".php") {
						array_push($this->fontlist, strtolower(basename($file, ".php")));
				}
			}
			closedir($fontsdir);
		}
		
		/**
		* Imports a TrueType, Type1, core, or CID0 font and makes it available.
		* It is necessary to generate a font definition file first (read /fonts/ttf2ufm/README.TXT). 
		* The definition file (and the font file itself when embedding) must be present either in the current directory or in the one indicated by K_PATH_FONTS if the constant is defined. If it could not be found, the error "Could not include font definition file" is generated.
		* Changed to support UTF-8 Unicode [Nicola Asuni, 2005-01-02].
		* @param string $family Font family. The name can be chosen arbitrarily. If it is a standard family name, it will override the corresponding font.
		* @param string $style Font style. Possible values are (case insensitive):<ul><li>empty string: regular (default)</li><li>B: bold</li><li>I: italic</li><li>BI or IB: bold italic</li></ul>
		* @param string $file The font definition file. By default, the name is built from the family and style, in lower case with no space.
		* @return array containing the font data, or false in case of error.
		* @since 1.5
		* @see SetFont()
		*/
		public function AddFont($family, $style='', $file='') {
			if (empty($family)) {
				if (!empty($this->FontFamily)) {
					$family = $this->FontFamily;
				} else {
					$this->Error('Empty font family');
				}
			}
			$family = strtolower($family);
			if ((!$this->isunicode) AND ($family == 'arial')) {
				$family = 'helvetica';
			}
			if (($family == "symbol") OR ($family == "zapfdingbats")) {
				$style = '';
			}
			$style = strtoupper($style);
			// underline
			if (strpos($style,'U') !== false) {
				$this->underline = true;
				$style = str_replace('U', '', $style);
			} else {
				$this->underline = false;
			}
			//line through (deleted)
			if (strpos($style,'D') !== false) {
				$this->linethrough = true;
				$style = str_replace('D', '', $style);
			} else {
				$this->linethrough = false;
			}
			if ($style == 'IB') {
				$style = 'BI';
			}
			$fontkey = $family.$style;
			$fontdata = array("fontkey" => $fontkey, "family" => $family, "style" => $style);
			// check if the font has been already added
			if (isset($this->fonts[$fontkey])) {
				return $fontdata;
			}
			if ($file == '') {
				$file = str_replace(' ', '', $family).strtolower($style).'.php';
			}
			if (!file_exists($this->_getfontpath().$file)) {
				// try to load the basic file without styles
				$file = str_replace(' ', '', $family).'.php';
			}
			if (isset($type)) {
				unset($type); 
			}
			if (isset($cw)) {
				unset($cw); 
			}
			include($this->_getfontpath().$file);
			if ((!isset($type)) OR (!isset($cw))) {
				$this->Error('Could not include font definition file');
			}
			$i = count($this->fonts) + 1;
			// register CID font (all styles at once)
			if ($type == 'cidfont0') {
				$styles = array('' => '', 'B' => ',Bold', 'I' => ',Italic', 'BI' => ',BoldItalic');
				foreach ($styles as $skey => $qual) {
					$sname = $name.$qual;
					$sfontkey = $family.$skey;
					$this->fonts[$sfontkey] = array('i' => $i, 'type' => $type, 'name' => $sname, 'desc' => $desc, 'cidinfo' => $cidinfo, 'up' => $up, 'ut' => $ut, 'cw' => $cw, 'dw' => $dw, 'enc' => $enc);
					$i = count($this->fonts) + 1;
				}
				$file = '';
			} elseif ($type == 'core') {
				$def_width = $cw[ord('?')];
				$this->fonts[$fontkey] = array('i' => $i, 'type' => 'core', 'name' => $this->CoreFonts[$fontkey], 'up' => -100, 'ut' => 50, 'cw' => $cw, 'dw' => $def_width);
			} elseif (($type == 'TrueType') OR ($type == 'Type1')) {
				if (!isset($file)) {
					$file = '';
				}
				if (!isset($enc)) {
					$enc = '';
				}
				$this->fonts[$fontkey] = array('i' => $i, 'type' => $type, 'name' => $name, 'up' => $up, 'ut' => $ut, 'cw' => $cw, 'file' => $file, 'enc' => $enc, 'desc' => $desc);
			} elseif ($type == 'TrueTypeUnicode') {
				$this->fonts[$fontkey] = array('i' => $i, 'type' => $type, 'name' => $name, 'desc' => $desc, 'up' => $up, 'ut' => $ut, 'cw' => $cw, 'enc' => $enc, 'file' => $file, 'ctg' => $ctg);
			} else {
				$this->Error('Unknow font type');
			}
			if (isset($diff) AND (!empty($diff))) {
				//Search existing encodings
				$d = 0;
				$nb = count($this->diffs);
				for($i=1; $i <= $nb; $i++) {
					if ($this->diffs[$i] == $diff) {
						$d = $i;
						break;
					}
				}
				if ($d == 0) {
					$d = $nb + 1;
					$this->diffs[$d] = $diff;
				}
				$this->fonts[$fontkey]['diff'] = $d;
			}
			if (!empty($file)) {
				if ((strcasecmp($type,"TrueType") == 0) OR (strcasecmp($type,"TrueTypeUnicode") == 0)) {
					$this->FontFiles[$file] = array('length1' => $originalsize);
				} elseif ($type != 'core') {
					$this->FontFiles[$file] = array('length1' => $size1, 'length2' => $size2);
				}
			}
			return $fontdata;
		}

		/**
		* Sets the font used to print character strings. 
		* The font can be either a standard one or a font added via the AddFont() method. Standard fonts use Windows encoding cp1252 (Western Europe).
		* The method can be called before the first page is created and the font is retained from page to page. 
		* If you just wish to change the current font size, it is simpler to call SetFontSize().
		* Note: for the standard fonts, the font metric files must be accessible. There are three possibilities for this:<ul><li>They are in the current directory (the one where the running script lies)</li><li>They are in one of the directories defined by the include_path parameter</li><li>They are in the directory defined by the K_PATH_FONTS constant</li></ul><br />
		* @param string $family Family font. It can be either a name defined by AddFont() or one of the standard Type1 families (case insensitive):<ul><li>times (Times-Roman)</li><li>timesb (Times-Bold)</li><li>timesi (Times-Italic)</li><li>timesbi (Times-BoldItalic)</li><li>helvetica (Helvetica)</li><li>helveticab (Helvetica-Bold)</li><li>helveticai (Helvetica-Oblique)</li><li>helveticabi (Helvetica-BoldOblique)</li><li>courier (Courier)</li><li>courierb (Courier-Bold)</li><li>courieri (Courier-Oblique)</li><li>courierbi (Courier-BoldOblique)</li><li>symbol (Symbol)</li><li>zapfdingbats (ZapfDingbats)</li></ul> It is also possible to pass an empty string. In that case, the current family is retained.
		* @param string $style Font style. Possible values are (case insensitive):<ul><li>empty string: regular</li><li>B: bold</li><li>I: italic</li><li>U: underline</li><li>D: line trough</li></ul> or any combination. The default value is regular. Bold and italic styles do not apply to Symbol and ZapfDingbats basic fonts or other fonts when not defined.
		* @param float $size Font size in points. The default value is the current size. If no size has been specified since the beginning of the document, the value taken is 12
		* @since 1.0
		* @see AddFont(), SetFontSize()
		*/
		public function SetFont($family, $style='', $size=0) {
			//Select a font; size given in points
			if ($size == 0) {
				$size = $this->FontSizePt;
			}
			// try to add font (if not already added)
			$fontdata =  $this->AddFont($family, $style);
			$this->FontFamily = $fontdata['family'];
			$this->FontStyle = $fontdata['style'];
			$this->CurrentFont = &$this->fonts[$fontdata['fontkey']];
			$this->SetFontSize($size);
		}

		/**
		* Defines the size of the current font.
		* @param float $size The size (in points)
		* @since 1.0
		* @see SetFont()
		*/
		public function SetFontSize($size) {
			//Set font size in points
			$this->FontSizePt = $size;
			$this->FontSize = $size / $this->k;
			if (isset($this->CurrentFont['desc']['Ascent']) AND ($this->CurrentFont['desc']['Ascent'] > 0)) {
				$this->FontAscent = $this->CurrentFont['desc']['Ascent'] * $this->FontSize / 1000;
			} else {
				$this->FontAscent = 0.8 * $this->FontSize;
			}
			if (isset($this->CurrentFont['desc']['Descent']) AND ($this->CurrentFont['desc']['Descent'] > 0)) {
				$this->FontDescent = - $this->CurrentFont['desc']['Descent'] * $this->FontSize / 1000;
			} else {
				$this->FontDescent = 0.2 * $this->FontSize;
			}
			if (($this->page > 0) AND (isset($this->CurrentFont['i']))) {
				$this->_out(sprintf('BT /F%d %.2f Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			}
		}

		/**
		* Creates a new internal link and returns its identifier. An internal link is a clickable area which directs to another place within the document.<br />
		* The identifier can then be passed to Cell(), Write(), Image() or Link(). The destination is defined with SetLink().
		* @since 1.5
		* @see Cell(), Write(), Image(), Link(), SetLink()
		*/
		public function AddLink() {
			//Create a new internal link
			$n = count($this->links) + 1;
			$this->links[$n] = array(0, 0);
			return $n;
		}

		/**
		* Defines the page and position a link points to.
		* @param int $link The link identifier returned by AddLink()
		* @param float $y Ordinate of target position; -1 indicates the current position. The default value is 0 (top of page)
		* @param int $page Number of target page; -1 indicates the current page. This is the default value
		* @since 1.5
		* @see AddLink()
		*/
		public function SetLink($link, $y=0, $page=-1) {
			if ($y == -1) {
				$y=$this->y;
			}
			if ($page == -1) {
				$page = $this->page;
			}
			$this->links[$link] = array($page, $y);
		}

		/**
		* Puts a link on a rectangular area of the page.
		* Text or image links are generally put via Cell(), Write() or Image(), but this method can be useful for instance to define a clickable area inside an image.
		* @param float $x Abscissa of the upper-left corner of the rectangle (or upper-right for RTL languages)
		* @param float $y Ordinate of the upper-left corner of the rectangle (or upper-right for RTL languages)
		* @param float $w Width of the rectangle
		* @param float $h Height of the rectangle
		* @param mixed $link URL or identifier returned by AddLink()
		* @since 1.5
		* @see AddLink(), Cell(), Write(), Image()
		*/
		public function Link($x, $y, $w, $h, $link) {
			$this->PageLinks[$this->page][] = array($x * $this->k, $this->hPt - $y * $this->k, $w * $this->k, $h*$this->k, $link);
		}
		
		/**
		* Prints a character string. The origin is on the left of the first charcter, on the baseline. This method allows to place a string precisely on the page, but it is usually easier to use Cell(), MultiCell() or Write() which are the standard methods to print text.
		* @param float $x Abscissa of the origin
		* @param float $y Ordinate of the origin
		* @param string $txt String to print
		* @param int $stroke outline size in points (0 = disable)
		* @param boolean $clip if true activate clipping mode (you must call StartTransform() before this function and StopTransform() to stop the clipping tranformation).
		* @since 1.0
		* @see SetFont(), SetTextColor(), Cell(), MultiCell(), Write()
		*/
		public function Text($x, $y, $txt, $stroke=0, $clip=false) {
			//Output a string
			if ($this->rtl) {
				// bidirectional algorithm (some chars may be changed affecting the line length)
				$s = $this->utf8Bidi($this->UTF8StringToArray($txt), $this->tmprtl);
				$l = $this->GetArrStringWidth($s);
				$xr = $this->w - $x - $this->GetArrStringWidth($s);
			} else {
				$xr = $x;
			}
			$opt = "";
			if (($stroke > 0) AND (!$clip)) {
				$opt .= "1 Tr ".intval($stroke)." w ";
			} elseif (($stroke > 0) AND $clip) {
				$opt .= "5 Tr ".intval($stroke)." w ";
			} elseif ($clip) {
				$opt .= "7 Tr ";
			}
			$s = sprintf('BT %.2f %.2f Td %s(%s) Tj ET 0 Tr', $xr * $this->k, ($this->h-$y) * $this->k, $opt, $this->_escapetext($txt));
			if ($this->underline AND ($txt!='')) {
				$s .= ' '.$this->_dounderline($xr, $y, $txt);
			}
			if ($this->linethrough AND ($txt!='')) { 
				$s .= ' '.$this->_dolinethrough($xr, $y, $txt); 
			}
			if ($this->ColorFlag AND (!$clip)) {
				$s='q '.$this->TextColor.' '.$s.' Q';
			}
			$this->_out($s);
		}

		/**
		* Whenever a page break condition is met, the method is called, and the break is issued or not depending on the returned value. 
		* The default implementation returns a value according to the mode selected by SetAutoPageBreak().<br />
		* This method is called automatically and should not be called directly by the application.
		* @return boolean
		* @since 1.4
		* @see SetAutoPageBreak()
		*/
		public function AcceptPageBreak() {
			return $this->AutoPageBreak;
		}
		
		/**
		* Add page if needed.
		* @param float $h Cell height. Default value: 0.
		* @since 3.2.000 (2008-07-01)
		* @access protected
		*/
		protected function checkPageBreak($h) {
			if ((($this->y + $h) > $this->PageBreakTrigger) AND (empty($this->InFooter)) AND ($this->AcceptPageBreak())) {
				$rs = "";
				//Automatic page break
				$x = $this->x;
				$ws = $this->ws;
				if ($ws > 0) {
					$this->ws = 0;
					$rs .= '0 Tw';
				}
				$this->AddPage($this->CurOrientation);
				if ($ws > 0) {
					$this->ws = $ws;
					$rs .= sprintf('%.3f Tw', $ws * $k);
				}
				$this->_out($rs);
				$this->y = $this->tMargin;
				$this->x = $x;
			}
		}

		/**
		* Prints a cell (rectangular area) with optional borders, background color and character string. The upper-left corner of the cell corresponds to the current position. The text can be aligned or centered. After the call, the current position moves to the right or to the next line. It is possible to put a link on the text.<br />
		* If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
		* @param float $w Cell width. If 0, the cell extends up to the right margin.
		* @param float $h Cell height. Default value: 0.
		* @param string $txt String to print. Default value: empty string.
		* @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL languages)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>
		Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
		* @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @since 1.0
		* @see SetFont(), SetDrawColor(), SetFillColor(), SetTextColor(), SetLineWidth(), AddLink(), Ln(), MultiCell(), Write(), SetAutoPageBreak()
		*/
		public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0) {
			//$min_cell_height = $this->FontAscent + $this->FontDescent;
			$min_cell_height = $this->FontSize * $this->cell_height_ratio;
			if ($h < $min_cell_height) {
				$h = $min_cell_height;
			}
			$this->checkPageBreak($h);
			$this->_out($this->getCellCode($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch));
		}
		
		/**
		* Returns the PDF string code to print a cell (rectangular area) with optional borders, background color and character string. The upper-left corner of the cell corresponds to the current position. The text can be aligned or centered. After the call, the current position moves to the right or to the next line. It is possible to put a link on the text.<br />
		* If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
		* @param float $w Cell width. If 0, the cell extends up to the right margin.
		* @param float $h Cell height. Default value: 0.
		* @param string $txt String to print. Default value: empty string.
		* @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL languages)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
		* @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @since 1.0
		* @see Cell()
		*/
		protected function getCellCode($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0) {
			$rs = ""; //string to be returned
			$min_cell_height = $this->FontSize * $this->cell_height_ratio;
			if ($h < $min_cell_height) {
				$h = $min_cell_height;
			}
			$k = $this->k;
			if (empty($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $this->x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $this->x;
				}
			}
			$s = '';			
			if (($fill == 1) OR ($border == 1)) {
				if ($fill == 1) {
					$op = ($border == 1) ? 'B' : 'f';
				} else {
					$op = 'S';
				}
				if ($this->rtl) {
					$xk = (($this->x  - $w) * $k);
				} else {
					$xk = ($this->x * $k);
				}
				$s .= sprintf('%.2f %.2f %.2f %.2f re %s ', $xk, (($this->h - $this->y) * $k), ($w * $k), (-$h * $k), $op);
			}
			
			if (is_string($border)) {
				$x = $this->x;
				$y = $this->y;
				if (strpos($border,'L') !== false) {
					if ($this->rtl) {
						$xk = ($x - $w) * $k;
					} else {
						$xk = $x * $k;
					}
					$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $xk, (($this->h - $y) * $k), $xk, (($this->h - ($y + $h)) * $k));
				}
				if (strpos($border,'T') !== false) {
					if ($this->rtl) {
						$xk = ($x - $w) * $k;
						$xwk = $x * $k;
					} else {
						$xk = $x * $k;
						$xwk = ($x + $w) * $k;
					}
					$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $xk, (($this->h - $y) * $k), $xwk, (($this->h - $y) * $k));
				}
				if (strpos($border,'R') !== false) {
					if ($this->rtl) {
						$xk = $x * $k;
					} else {
						$xk = ($x + $w) * $k;
					}
					$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $xk, (($this->h - $y) * $k), $xk, (($this->h - ($y + $h))* $k));
				}
				if (strpos($border,'B') !== false) {
					if ($this->rtl) {
						$xk = ($x - $w) * $k;
						$xwk = $x * $k;
					} else {
						$xk = $x * $k;
						$xwk = ($x + $w) * $k;
					}
					$s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $xk, (($this->h - ($y + $h)) * $k), $xwk, (($this->h - ($y + $h)) * $k));
				}
			}
			if ($txt != '') {
				// text lenght
				$width = $this->GetStringWidth($txt);
				// ratio between cell lenght and text lenght
				$ratio = ($w - (2 * $this->cMargin)) / $width;
				
				// stretch text if required
				if (($stretch > 0) AND (($ratio < 1) OR (($ratio > 1) AND (($stretch % 2) == 0)))) {
					if ($stretch > 2) {
						// spacing
						//Calculate character spacing in points
						$char_space = (($w - $width - (2 * $this->cMargin)) * $this->k) / max($this->GetNumChars($txt)-1,1);
						//Set character spacing
						$rs .= sprintf('BT %.2f Tc ET ', $char_space);
					} else {
						// scaling
						//Calculate horizontal scaling
						$horiz_scale = $ratio * 100.0;
						//Set horizontal scaling
						$rs .= sprintf('BT %.2f Tz ET ', $horiz_scale);
					}
					$align = '';
					$width = $w - (2 * $this->cMargin);
				} else {
					$stretch == 0;
				}
				if ($align == 'L') {
					if ($this->rtl) {
						$dx = $w - $width - $this->cMargin;
					} else {
						$dx = $this->cMargin;
					}
				} elseif ($align == 'R') {
					if ($this->rtl) {
						$dx = $this->cMargin;
					} else {
						$dx = $w - $width - $this->cMargin;
					}
				} elseif ($align == 'C') {
					$dx = ($w - $width) / 2;
				} elseif ($align == 'J') {
					if ($this->rtl) {
						$dx = $w - $width - $this->cMargin;
					} else {
						$dx = $this->cMargin;
					}
				} else {
					$dx = $this->cMargin;
				}
				if ($this->ColorFlag) {
					$s .= 'q '.$this->TextColor.' ';
				}
				$txt2 = $this->_escapetext($txt);
				if ($this->rtl) {
					$xdk = ($this->x - $dx - $width) * $k;
				} else {
					$xdk = ($this->x + $dx) * $k;
				}
				// Justification
				if ($align == 'J') {
					// count number of spaces
					$ns = substr_count($txt, ' ');
					//if ($this->isunicode) {
					if (($this->CurrentFont['type'] == "TrueTypeUnicode") OR ($this->CurrentFont['type'] == "cidfont0")) {
						// get string width without spaces
						$width = $this->GetStringWidth(str_replace(' ', '', $txt));
						// calculate average space width
						$spacewidth = ($w - $width - (2 * $this->cMargin)) / ($ns?$ns:1) / $this->FontSize / $this->k;
						// set word position to be used with TJ operator
						$txt2 = str_replace(chr(0).' ', ') '.(-2830 * $spacewidth).' (', $txt2);
					} else {
						// get string width
						$width = $this->GetStringWidth($txt);
						$spacewidth = (($w - $width - (2 * $this->cMargin)) / ($ns?$ns:1)) * $this->k;
						$rs .= sprintf('BT %.3f Tw ET ', $spacewidth);
					}
				}
				// calculate approximate position of the font base line
				//$basefonty = $this->y + (($h + $this->FontAscent - $this->FontDescent)/2);
				$basefonty = $this->y + ($h/2) + ($this->FontSize/3);
				// print text
				$s .= sprintf('BT %.2f %.2f Td [(%s)] TJ ET', $xdk, (($this->h - $basefonty) * $k), $txt2);
				if ($this->rtl) {
					$xdx = $this->x - $dx - $width;
				} else {
					$xdx = $this->x + $dx;
				}
				if ($this->underline)  {
					$s .= ' '.$this->_dounderline($xdx, $basefonty, $txt);
				}
				if ($this->linethrough) { 
					$s .= ' '.$this->_dolinethrough($xdx, $basefonty, $txt);
				}
				if ($this->ColorFlag) {
					$s .= ' Q';
				}
				if ($link) {
					$this->Link($xdx, $this->y + (($h - $this->FontSize)/2), $width, $this->FontSize, $link);
				}
			}
			// output cell
			if ($s) {
				// output cell
				$rs .= $s;
				// reset text stretching
				if ($stretch > 2) {
					//Reset character horizontal spacing
					$rs .= ' BT 0 Tc ET';
				} elseif ($stretch > 0) {
					//Reset character horizontal scaling
					$rs .= ' BT 100 Tz ET';
				}
			}
			// reset word spacing
			if ((!$this->isunicode) AND ($align == 'J')) {
				$rs .= ' BT 0 Tw ET';
			}
			$this->lasth = $h;
			if ($ln > 0) {
				//Go to the beginning of the next line
				$this->y += $h;
				if ($ln == 1) {
					if ($this->rtl) {
						$this->x = $this->w - $this->rMargin;
					} else {
						$this->x = $this->lMargin;
					}
				}
			} else {
				// go left or right by case
				if ($this->rtl) {
					$this->x -= $w;
				} else {
					$this->x += $w;
				}
			}
			$gstyles = $this->linestyleWidth." ".$this->linestyleCap." ".$this->linestyleJoin." ".$this->linestyleDash." ".$this->DrawColor." ".$this->FillColor."\n";
			$rs = $gstyles.$rs;
			return $rs;
		}

		/**
		* This method allows printing text with line breaks. 
		* They can be automatic (as soon as the text reaches the right border of the cell) or explicit (via the \n character). As many cells as necessary are output, one below the other.<br />
		* Text can be aligned, centered or justified. The cell block can be framed and the background painted.
		* @param float $w Width of cells. If 0, they extend up to the right margin of the page.
		* @param float $h Cell minimum height. The cell extends automatically if needed.
		* @param string $txt String to print
		* @param mixed $border Indicates if borders must be drawn around the cell block. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align</li><li>C: center</li><li>R: right align</li><li>J: justification (default value when $ishtml=false)</li></ul>
		* @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		* @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right</li><li>1: to the beginning of the next line [DEFAULT]</li><li>2: below</li></ul>
		* @param int $x x position in user units
		* @param int $y y position in user units
		* @param boolean $reseth if true reset the last cell height (default true).
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @param boolean $ishtml se to true if $txt is HTML content (default = false).
		* @return int Return the number of cells or 1 for html mode.
		* @since 1.3
		* @see SetFont(), SetDrawColor(), SetFillColor(), SetTextColor(), SetLineWidth(), Cell(), Write(), SetAutoPageBreak()
		*/
		public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false) {	
			if ((empty($this->lasth))OR ($reseth)) {
				//set row height
				$this->lasth = $this->FontSize * $this->cell_height_ratio;
			}
			if (!empty($y)) {
				$this->SetY($y);
			} else {
				$y = $this->GetY();
			}
			// check for page break
			$this->checkPageBreak($h);
			$y = $this->GetY();
			// get current page number
			$startpage = $this->page;
			if (!empty($x)) {
				$this->SetX($x);
			} else {
				$x = $this->GetX();
			}
			if (empty($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $this->x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $this->x;
				}
			}
			// store original margin values
			$lMargin = $this->lMargin;
			$rMargin = $this->rMargin;
			if ($this->rtl) {
				$this->SetRightMargin($this->w - $this->x);
				$this->SetLeftMargin($this->x - $w);
			} else {
				$this->SetLeftMargin($this->x);
				$this->SetRightMargin($this->w - $this->x - $w);
			}
			// calculate remaining vertical space on first page ($startpage)
			$restspace = $this->getPageHeight() - $this->GetY() - $this->getBreakMargin();
			// Adjust internal padding
			if ($this->cMargin < ($this->LineWidth / 2)) {
				$this->cMargin = ($this->LineWidth / 2);
			}
			// Add top space if needed
			if (($this->lasth - $this->FontSize) < $this->LineWidth) {
				$this->y += $this->LineWidth / 2;
			}
			// add top padding
			$this->y += $this->cMargin;
			if ($ishtml) {
				// Write HTML text
				$this->writeHTML($txt, true, 0, $reseth, true, $align);
				$nl = 1;
			} else {
				// Write text
				$nl = $this->Write($this->lasth, $txt, '', 0, $align, true, $stretch, false);
			}
			// add bottom padding
			$this->y += $this->cMargin;
			// Add bottom space if needed
			if (($this->lasth - $this->FontSize) < $this->LineWidth) {
				$this->y += $this->LineWidth / 2;
			}
			// Get end-of-text Y position
			$currentY = $this->GetY();
			// get latest page number
			$endpage = $this->page;
			// check if a new page has been created
			if ($endpage > $startpage) {
				// design borders around HTML cells.
				for ($page=$startpage; $page <= $endpage; $page++) {
					$this->setPage($page);
					if ($page == $startpage) {
						$this->SetY($this->getPageHeight() - $restspace - $this->getBreakMargin());
						$h = $restspace;
					} elseif ($page == $endpage) {
						$this->SetY($this->tMargin); // put cursor at the beginning of text
						$h = $currentY - $this->tMargin;
					} else {
						$this->SetY($this->tMargin); // put cursor at the beginning of text
						$h = $this->getPageHeight() - $this->tMargin - $this->getBreakMargin();
					}
					$this->SetX($x);
					$ccode = $this->getCellCode($w, $h, "", $border, 1, '', $fill);
					if ($border OR $fill) {
						$pstart = substr($this->pages[$this->page], 0, $this->intmrk[$this->page]);
						$pend = substr($this->pages[$this->page], $this->intmrk[$this->page]);
						$this->pages[$this->page] = $pstart.$ccode."\n".$pend;
						$this->intmrk[$this->page] += strlen($ccode."\n");
					}
				}
			} else {
				$h = max($h, ($currentY - $y));
				// put cursor at the beginning of text
				$this->SetY($y); 
				$this->SetX($x);
				$ccode = $this->getCellCode($w, $h, "", $border, 1, '', $fill);
				if ($border OR $fill) {
					// design a cell around the text
					$pstart = substr($this->pages[$this->page], 0, $this->intmrk[$this->page]);
					$pend = substr($this->pages[$this->page], $this->intmrk[$this->page]);
					$this->pages[$this->page] = $pstart.$ccode."\n".$pend;
					$this->intmrk[$this->page] += strlen($ccode."\n");
				}
			}
			// Get end-of-cell Y position
			$currentY = $this->GetY();
			// restore original margin values
			$this->SetLeftMargin($lMargin);
			$this->SetRightMargin($rMargin);
			if ($ln > 0) {
				//Go to the beginning of the next line
				$this->SetY($currentY);
				if ($ln == 2) {
					$this->SetX($x + $w);
				}
			} else {
				// go left or right by case
				$this->setPage($startpage);
				$this->y = $y;
				$this->SetX($x + $w);
			}
			return $nl;
		}
		
		/**
		* This method prints text from the current position.<br />
		* @param float $h Line height
		* @param string $txt String to print
		* @param mixed $link URL or identifier returned by AddLink()
		* @param int $fill Indicates if the background must be painted (1) or transparent (0). Default value: 0.
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
		* @param boolean $ln if true set cursor at the bottom of the line, otherwise set cursor at the top of the line.
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @param boolean $firstline if true prints only the first line and return the remaining string.
		* @return mixed Return the number of cells or the remaining string if $firstline = true.
		* @since 1.5
		*/
		public function Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0, $firstline=false) {
			// remove carriage returns
			$s = str_replace("\r", '', $txt);
			// check if string contains arabic text
			if (preg_match(K_RE_PATTERN_ARABIC, $s)) {
				$arabic = true;
			} else {
				$arabic = false;
			}
			// get array of chars
			$chars = $this->UTF8StringToArray($s);
			// get the number of characters
			$nb = count($chars);
			// handle single space character
			if (($nb == 1) AND preg_match("/[\s]/u", $s)) {
				if ($this->rtl) {
					$this->x -= $this->GetStringWidth($s);
				} else {
					$this->x += $this->GetStringWidth($s);
				}
				return;
			}
			// store current position
			$prevx = $this->x;
			$prevy = $this->y;
			// calculate remaining line width ($w)
			if ($this->rtl) {
				$w = $this->x - $this->lMargin;
			} else {
				$w = $this->w - $this->rMargin - $this->x;
			}
			// max column width
			$wmax = $w - (2 * $this->cMargin);
			$i = 0; // character position
			$j = 0; // current starting position
			$sep = -1; // position of the last blank space
			$l = 0; // current string lenght
			$nl = 0; //number of lines
			$linebreak = false;
			// for each character
			while ($i < $nb) {
				//Get the current character
				$c = $chars[$i];
				if ($c == 10) { // 10 = "\n" = new line
					//Explicit line break
					if ($align == "J") {
						if ($this->rtl) {
							$talign = "R";
						} else {
							$talign = "L";
						}
					} else {
						$talign = $align;
					}
					if ($firstline) {
						$startx = $this->x;
						$linew = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, $i), $this->tmprtl));
						if ($this->rtl) {
							$this->endlinex = $startx - $linew;
						} else {
							$this->endlinex = $startx + $linew;
						}
						$w = $linew;
						$tmpcmargin = $this->cMargin;
						$this->cMargin = 0;
					}
					$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $i), 0, 1, $talign, $fill, $link, $stretch);
					if ($firstline) {
						$this->cMargin = $tmpcmargin;
						return ($this->UTF8ArrSubString($chars, $i));
					}
					$nl++;
					$j = $i + 1;
					$l = 0;
					$sep = -1;
					$w = $this->getRemainingWidth();
					$wmax = $w - (2 * $this->cMargin);
				} else {
					if (preg_match("/[\s]/u", $this->unichr($c))) {
						// update last blank space position
						$sep = $i;
					}
					// update string length
					if (($this->isunicode) AND ($arabic)) {
						// with bidirectional algorithm some chars may be changed affecting the line length
						// *** very slow ***
						$l = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, $i-$j+1), $this->tmprtl));
					} else {
						$l += $this->GetCharWidth($c);
					}
					if ($l > $wmax) {
						// we have reached the end of column
						if ($sep == -1) {
							// check if the line was already started
							if (($this->rtl AND ($this->x < ($this->w - $this->rMargin)))
								OR ((!$this->rtl) AND ($this->x > $this->lMargin))) {
								// print a void cell and go to next line
								$this->Cell($w, $h, "", 0, 1);
								$linebreak = true;
							} else {
								// truncate the word because do not fit on column
								if ($firstline) {
									$startx = $this->x;
									$linew = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, $i), $this->tmprtl));
									if ($this->rtl) {
										$this->endlinex = $startx - $linew;
									} else {
										$this->endlinex = $startx + $linew;
									}
									$w = $linew;
									$tmpcmargin = $this->cMargin;
									$this->cMargin = 0;
								}
								$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $i), 0, 1, $align, $fill, $link, $stretch);
								if ($firstline) {
									$this->cMargin = $tmpcmargin;
									return ($this->UTF8ArrSubString($chars, $i));
								}
								$j = $i;
								$i--;
							}	
						} else {
							// word wrapping
							if ($firstline) {
								$startx = $this->x;
								$linew = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, $sep), $this->tmprtl));
								if ($this->rtl) {
									$this->endlinex = $startx - $linew;
								} else {
									$this->endlinex = $startx + $linew;
								}
								$w = $linew;
								$tmpcmargin = $this->cMargin;
								$this->cMargin = 0;
							}
							$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $sep), 0, 1, $align, $fill, $link, $stretch);
							if ($firstline) {
								$this->cMargin = $tmpcmargin;
								return ($this->UTF8ArrSubString($chars, $sep));
							}
							$i = $sep;
							$sep = -1;
							$j = ($i+1);
						}
						$w = $this->getRemainingWidth();
						$wmax = $w - (2 * $this->cMargin);
						if ($linebreak) {
							$linebreak = false;
						} else {
							$nl++;
							$l = 0;
						}
					}
				}
				$i++;
			} // end while i < nb
			// print last substring (if any)
			if ($l > 0) {
				switch ($align) {
					case "J":
					case "C": {
						$w = $w;
						break;
					}
					case "L": {
						if ($this->rtl) {
							$w = $w;
						} else {
							$w = $l;
						}
						break;
					}
					case "R": {
						if ($this->rtl) {
							$w = $l;
						} else {
							$w = $w;
						}
						break;
					}
					default: {
						$w = $l;
						break;
					}
				}
				if ($firstline) {
					$startx = $this->x;
					$linew = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, $nb), $this->tmprtl));
					if ($this->rtl) {
						$this->endlinex = $startx - $linew;
					} else {
						$this->endlinex = $startx + $linew;
					}
					$w = $linew;
					$tmpcmargin = $this->cMargin;
					$this->cMargin = 0;
				}
				$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $nb), 0, $ln, $align, $fill, $link, $stretch);
				if ($firstline) {
					$this->cMargin = $tmpcmargin;
					return ($this->UTF8ArrSubString($chars, $nb));
				}
				$nl++;
			}	
			return $nl;
		}
				
		/**
		* Returns the remaining width between the current position and margins.
		* @return int Return the remaining width
		* @access protected
		*/
		protected function getRemainingWidth() {
			if ($this->rtl) {
				return ($this->x - $this->lMargin);
			} else {
				return ($this->w - $this->rMargin - $this->x);
			}
		}

	 /**
		* Extract a slice of the $strarr array and return it as string.
		* @param string $strarr The input array of characters.
		* @param int $start the starting element of $strarr.
		* @param int $end first element that will not be returned.
		* @return Return part of a string
		*/
		public function UTF8ArrSubString($strarr, $start='', $end='') {
			if (strlen($start) == 0) {
				$start = 0;
			}
			if (strlen($end) == 0) {
				$end = count($strarr);
			}
			$string = "";
			for ($i=$start; $i < $end; $i++) {
				$string .= $this->unichr($strarr[$i]);
			}
			return $string;
		}
		
		/**
		* Returns the unicode caracter specified by UTF-8 code
		* @param int $c UTF-8 code
		* @return Returns the specified character.
		* @author Miguel Perez, Nicola Asuni
		* @since 2.3.000 (2008-03-05)
		*/
		public function unichr($c) {
			if (!$this->isunicode) {
				return chr($c);
			} elseif ($c <= 0x7F) {
				// one byte
				return chr($c);
			} elseif ($c <= 0x7FF) {
				// two bytes
				return chr(0xC0 | $c >> 6).chr(0x80 | $c & 0x3F);
			} elseif ($c <= 0xFFFF) {
				// three bytes
				return chr(0xE0 | $c >> 12).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
			} elseif ($c <= 0x10FFFF) {
				// four bytes
				return chr(0xF0 | $c >> 18).chr(0x80 | $c >> 12 & 0x3F).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
			} else {
				return "";
			}
		}
		
		/**
		* Puts an image in the page. 
		* The upper-left corner must be given. 
		* The dimensions can be specified in different ways:<ul>
		* <li>explicit width and height (expressed in user unit)</li>
		* <li>one explicit dimension, the other being calculated automatically in order to keep the original proportions</li>
		* <li>no explicit dimension, in which case the image is put at 72 dpi</li></ul>
		* Supported formats are JPEG and PNG images whitout GD library and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;
		* The format can be specified explicitly or inferred from the file extension.<br />
		* It is possible to put a link on the image.<br />
		* Remark: if an image is used several times, only one copy will be embedded in the file.<br />
		* @param string $file Name of the file containing the image.
		* @param float $x Abscissa of the upper-left corner.
		* @param float $y Ordinate of the upper-left corner.
		* @param float $w Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param float $h Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param string $type Image format. Possible values are (case insensitive): JPEG and PNG (whitout GD library) and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;. If not specified, the type is inferred from the file extension.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param string $align Indicates the alignment of the pointer next to image insertion relative to image height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		* @param boolean $resize If true resize (reduce) the image to fit $w and $h (requires GD library).
		* @param int $dpi dot-per-inch resolution used on resize
		* @param string $palign Allows to center or align the image on the current line. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		* @since 1.1
		* @see AddLink()
		*/
		public function Image($file, $x, $y, $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='') {
			// get image size
			list($pixw, $pixh) = getimagesize($file);
			// calculate image width and height on document
			if (($w <= 0) AND ($h <= 0)) {
				// convert image size to document unit
				$w = $pixw / ($this->imgscale * $this->k);
				$h = $pixh / ($this->imgscale * $this->k);
			} elseif ($w <= 0) {
				$w = $h * $pixw / $pixh;
			} elseif ($h <= 0) {
				$h = $w * $pixh / $pixw;
			}
			// calculate new minimum dimensions in pixels
			$neww = round($w * $this->k * $dpi / $this->dpi);
			$newh = round($h * $this->k * $dpi / $this->dpi);
			// check if resize is necessary (resize is used only to reduce the image)
			if (($neww * $newh) >= ($pixw * $pixh)) {
				$resize = false;
			}
			// check if image has been already added on document
			if (!isset($this->images[$file])) {
				//First use of image, get info
				if ($type == '') {
					$fileinfo = pathinfo($file);
					if (isset($fileinfo['extension']) AND (!empty($fileinfo['extension']))) {
						$type = $fileinfo['extension'];
					} else {
						$this->Error('Image file has no extension and no type was specified: '.$file);
					}
				}
				$type = strtolower($type);
				if ($type == "jpg") {
					$type = "jpeg";
				}
				$mqr = get_magic_quotes_runtime();
				set_magic_quotes_runtime(0);
				// Specific image handlers
				$mtd = '_parse'.$type;
				// GD image handler function
				$gdfunction = "imagecreatefrom".$type;
				$info = false;
				if ((method_exists($this,$mtd)) AND (!($resize AND function_exists($gdfunction)))) {
					$info = $this->$mtd($file);
				} 
				if (!$info) {
					if (function_exists($gdfunction)) {
						$img = $gdfunction($file);
						if ($resize) {
							$imgr = imagecreatetruecolor($neww, $newh);
							imagecopyresampled($imgr, $img, 0, 0, 0, 0, $neww, $newh, $pixw, $pixh); 
							$info = $this->_toJPEG($imgr);
						} else {
							$info = $this->_toJPEG($img);
						}
					} else {
						return;
					}
				}
				if ($info === false) {
					//If false, we cannot process image
					return;
				}
				set_magic_quotes_runtime($mqr);
				$info['i'] = count($this->images) + 1;
				// add image to document
				$this->images[$file] = $info;
			} else {
				$info = $this->images[$file];
			}
			// 2007-10-19 Warren Sherliker
			// Check whether we need a new page first as this does not fit
			// Copied from Cell()
			if ((($this->y + $h) > $this->PageBreakTrigger) AND empty($this->InFooter) AND $this->AcceptPageBreak()) {
				// Automatic page break
				$this->AddPage($this->CurOrientation);
				// Reset coordinates to top fo next page
				$x = $this->GetX();
				$y = $this->GetY();
			}
			// 2007-10-19 Warren Sherliker: End Edit
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			// set alignment
			if ($this->rtl) {
				if ($palign == 'L') {
					$ximg = $this->lMargin;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} elseif ($palign == 'C') {
					$ximg = ($this->w - $x - $w) / 2;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} else {
					$ximg = $this->w - $x - $w;
					// set left side coordinate
					$this->img_rb_x = $ximg;
				}
			} else {
				if ($palign == 'R') {
					$ximg = $this->w - $this->rMargin - $w;
					// set left side coordinate
					$this->img_rb_x = $ximg;
				} elseif ($palign == 'C') {
					$ximg = ($this->w - $x - $w) / 2;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} else {
					$ximg = $x;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				}
			}
			$xkimg = $ximg * $this->k;
			$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', ($w * $this->k), ($h * $this->k), $xkimg, (($this->h - ($y + $h)) * $this->k), $info['i']));
			if ($link) {
				$this->Link($ximg, $y, $w, $h, $link);
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T':{
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M':{
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B':{
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N':{
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
			$this->endlinex = $this->img_rb_x;
		}
				
		/**
		* Convert the loaded php image to a JPEG and then return a structure for the PDF creator.
		* This function requires GD library and write access to the directory defined on K_PATH_CACHE constant.
		* @param string $file Image file name.
		* @param image $image Image object.
		* return image JPEG image object.
		* @access protected
		*/
		protected function _toJPEG($image) {
			$tempname = tempnam(K_PATH_CACHE,'jpg');
			imagejpeg($image, $tempname, $this->jpeg_quality);
			imagedestroy($image);
			$retvars = $this->_parsejpeg($tempname);
			// tidy up by removing temporary image
			unlink($tempname);
			return $retvars;
		}
		
		/**
		* Extract info from a JPEG file without using the GD library.
		* @param string $file image file to parse
		* @return array structure containing the image data
		* @access protected
		*/
		protected function _parsejpeg($file) {
			$a = getimagesize($file);
			if (empty($a)) {
				$this->Error('Missing or incorrect image file: '.$file);
			}
			if ($a[2] != 2) {
				$this->Error('Not a JPEG file: '.$file);
			}
			if ((!isset($a['channels'])) OR ($a['channels'] == 3)) {
				$colspace = 'DeviceRGB';
			} elseif ($a['channels'] == 4) {
				$colspace = 'DeviceCMYK';
			}	else {
				$colspace = 'DeviceGray';
			}
			$bpc = isset($a['bits']) ? $a['bits'] : 8;
			$data = file_get_contents($file);
			return array('w' => $a[0], 'h' => $a[1], 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'DCTDecode', 'data' => $data);
		}

		/**
		* Extract info from a PNG file without using the GD library.
		* @param string $file image file to parse
		* @return array structure containing the image data
		* @access protected
		*/
		protected function _parsepng($file) {
			$f = fopen($file,'rb');
			if (empty($f)) {
				$this->Error('Can\'t open image file: '.$file);
			}
			//Check signature
			if (fread($f,8) != chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
				$this->Error('Not a PNG file: '.$file);
			}
			//Read header chunk
			fread($f,4);
			if (fread($f,4) != 'IHDR') {
				$this->Error('Incorrect PNG file: '.$file);
			}
			$w = $this->_freadint($f);
			$h = $this->_freadint($f);
			$bpc = ord(fread($f,1));
			if ($bpc > 8) {
				//$this->Error('16-bit depth not supported: '.$file);
				return false;
			}
			$ct = ord(fread($f,1));
			if ($ct == 0) {
				$colspace = 'DeviceGray';
			} elseif ($ct == 2) {
				$colspace = 'DeviceRGB';
			} elseif ($ct == 3) {
				$colspace = 'Indexed';
			} else {
				//$this->Error('Alpha channel not supported: '.$file);
				return false;
			}
			if (ord(fread($f,1)) != 0) {
				//$this->Error('Unknown compression method: '.$file);
				return false;
			}
			if (ord(fread($f,1)) != 0) {
				//$this->Error('Unknown filter method: '.$file);
				return false;
			}
			if (ord(fread($f,1)) != 0) {
				//$this->Error('Interlacing not supported: '.$file);
				return false;
			}
			fread($f,4);
			$parms = '/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
			//Scan chunks looking for palette, transparency and image data
			$pal = '';
			$trns = '';
			$data = '';
			do {
				$n = $this->_freadint($f);
				$type = fread($f,4);
				if ($type == 'PLTE') {
					//Read palette
					$pal = fread($f,$n);
					fread($f,4);
				} elseif ($type == 'tRNS') {
					//Read transparency info
					$t = fread($f,$n);
					if ($ct == 0) {
						$trns = array(ord(substr($t,1,1)));
					}
					elseif ($ct == 2) {
						$trns = array(ord(substr($t,1,1)), ord(substr($t,3,1)), ord(substr($t,5,1)));
					} else {
						$pos = strpos($t,chr(0));
						if ($pos !== false) {
							$trns = array($pos);
						}
					}
					fread($f, 4);
				} elseif ($type == 'IDAT') {
					//Read image data block
					$data .= fread($f,$n);
					fread($f, 4);
				} elseif ($type == 'IEND') {
					break;
				} else {
					fread($f, $n+4);
				}
			}
			while ($n);
			if (($colspace == 'Indexed') AND (empty($pal))) {
				//$this->Error('Missing palette in '.$file);
				return false;
			}
			fclose($f);
			return array('w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'FlateDecode', 'parms' => $parms, 'pal' => $pal, 'trns' => $trns, 'data' => $data);
		}
		
		/**
		* Performs a line break. 
		* The current abscissa goes back to the left margin and the ordinate increases by the amount passed in parameter.
		* @param float $h The height of the break. By default, the value equals the height of the last printed cell.
		* @param boolean $cell if true add a cMargin to the x coordinate
		* @since 1.0
		* @see Cell()
		*/
		public function Ln($h='', $cell=false) {
			//Line feed; default value is last cell height
			if ($cell) {
				$cellmargin = $this->cMargin;
			} else {
				$cellmargin = 0;
			}
			if ($this->rtl) {
				$this->x = $this->w - $this->rMargin - $cellmargin;
			} else {
				$this->x = $this->lMargin + $cellmargin;
			}
			if (is_string($h)) {
				$this->y += $this->lasth;
			} else {
				$this->y += $h;
			}
			$this->newline = true;
		}

		/**
		* Returns the relative X value of current position.
		* The value is relative to the left border for LTR languages and to the right border for RTL languages.
		* @return float
		* @since 1.2
		* @see SetX(), GetY(), SetY()
		*/
		public function GetX() {
			//Get x position
			if ($this->rtl) {
				return ($this->w - $this->x);
			} else {
				return $this->x;
			}
		}
		
		/**
		* Returns the absolute X value of current position.
		* @return float
		* @since 1.2
		* @see SetX(), GetY(), SetY()
		*/
		public function GetAbsX() {
			return $this->x;
		}
		
		/**
		* Returns the ordinate of the current position.
		* @return float
		* @since 1.0
		* @see SetY(), GetX(), SetX()
		*/
		public function GetY() {
			//Get y position
			return $this->y;
		}
		
		/**
		* Defines the abscissa of the current position. 
		* If the passed value is negative, it is relative to the right of the page (or left if language is RTL).
		* @param float $x The value of the abscissa.
		* @since 1.2
		* @see GetX(), GetY(), SetY(), SetXY()
		*/
		public function SetX($x) {
			//Set x position
			if ($this->rtl) {
				if ($x >= 0) {
					$this->x = $this->w - $x;
				} else {
					$this->x = abs($x);
				}
			} else {
				if ($x >= 0) {
					$this->x = $x;
				} else {
					$this->x = $this->w + $x;
				}
			}
		}
		
		/**
		* Moves the current abscissa back to the left margin and sets the ordinate.
		* If the passed value is negative, it is relative to the bottom of the page.
		* @param float $y The value of the ordinate.
		* @since 1.0
		* @see GetX(), GetY(), SetY(), SetXY()
		*/
		public function SetY($y) {
			//Set y position and reset x
			if ($this->rtl) {
				$this->x = $this->w - $this->rMargin;
			} else {
				$this->x = $this->lMargin;
			}
			if ($y >= 0) {
				$this->y = $y;
			} else {
				$this->y = $this->h + $y;
			}
		}
		
		/**
		* Defines the abscissa and ordinate of the current position. 
		* If the passed values are negative, they are relative respectively to the right and bottom of the page.
		* @param float $x The value of the abscissa
		* @param float $y The value of the ordinate
		* @since 1.2
		* @see SetX(), SetY()
		*/
		public function SetXY($x, $y) {
			//Set x and y positions
			$this->SetY($y);
			$this->SetX($x);
		}

		/**
		* Send the document to a given destination: string, local file or browser. 
		* In the last case, the plug-in may be used (if present) or a download ("Save as" dialog box) may be forced.<br />
		* The method first calls Close() if necessary to terminate the document.
		* @param string $name The name of the file when saved.
		* @param string $dest Destination where to send the document. It can take one of the following values:<ul><li>I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.</li><li>D: send to the browser and force a file download with the name given by name.</li><li>F: save to a local file with the name given by name.</li><li>S: return the document as a string. name is ignored.</li></ul>
		* @since 1.0
		* @see Close()
		*/
		public function Output($name='doc.pdf', $dest='I') {
			//Output PDF to some destination
			//Finish document if necessary
			if ($this->state < 3) {
				$this->Close();
			}
			//Normalize parameters
			if (is_bool($dest)) {
				$dest = $dest ? 'D' : 'F';
			}
			$dest = strtoupper($dest);
			if ($dest != 'F') {
				$name = str_replace("+", "%20", urlencode($name));
				$name = preg_replace('/[\r\n]+\s*/', '' , $name);
			}
			switch($dest) {
				case 'I': {
					//Send to standard output
					if (ob_get_contents()) {
						$this->Error('Some data has already been output, can\'t send PDF file');
					}
					if (php_sapi_name() != 'cli') {
						//We send to a browser
						header('Content-Type: application/pdf');
						if (headers_sent()) {
							$this->Error('Some data has already been output to browser, can\'t send PDF file');
						}
						// Disable caching
						header('Cache-Control: private, must-revalidate');
						header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');	
						header('Content-Length: '.strlen($this->buffer));
						header('Content-Disposition: inline; filename="'.basename($name).'";');
					}
					echo $this->buffer;
					break;
				}
				case 'D': {
					//Download file
					if (ob_get_contents()) {
						$this->Error('Some data has already been output, can\'t send PDF file');
					}
					header('Content-Description: File Transfer');
					if (headers_sent()) {
						$this->Error('Some data has already been output to browser, can\'t send PDF file');
					}
					header('Cache-Control: private, must-revalidate');
					header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					// always modified
					header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
					// force download dialog
					header("Content-Type: application/force-download");
					header("Content-Type: application/octet-stream", false);
					header("Content-Type: application/download", false);
					// use the Content-Disposition header to supply a recommended filename
					header('Content-Disposition: attachment; filename="'.basename($name).'";');
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".strlen($this->buffer));
					echo $this->buffer;
					break;
				}
				case 'F': {
					//Save to local file
					$f = fopen($name, 'wb');
					if (!$f) {
						$this->Error('Unable to create output file: '.$name);
					}
					fwrite($f, $this->buffer,strlen($this->buffer));
					fclose($f);
					break;
				}
				case 'S': {
					//Return as a string
					return $this->buffer;
				}
				default: {
					$this->Error('Incorrect output destination: '.$dest);
				}
			}
			return '';
		}
		
		/**
		* Check for locale-related bug
		* @access protected
		*/
		protected function _dochecks() {
			//Check for locale-related bug
			if (1.1 == 1) {
				$this->Error('Don\'t alter the locale before including class file');
			}
			//Check for decimal separator
			if (sprintf('%.1f', 1.0) != '1.0') {
				setlocale(LC_NUMERIC, 'C');
			}
		}

		/**
		* Return fonts path
		* @return string
		* @access protected
		*/
		protected function _getfontpath() {
			if (!defined('K_PATH_FONTS') AND is_dir(dirname(__FILE__).'/fonts')) {
				define('K_PATH_FONTS', dirname(__FILE__).'/fonts/');
			}
			return defined('K_PATH_FONTS') ? K_PATH_FONTS : '';
		}
		
		/**
		* Output pages.
		* @access protected
		*/
		protected function _putpages() {
			$nb = count($this->pages);	
			if (!empty($this->pagegroups)) {
				// do page number replacement
				foreach ($this->pagegroups as $k => $v) {
					$alias = $this->_escapetext($k);
					$nbstr = $this->UTF8ToUTF16BE($v, false);
					for ($n = 1; $n <= $nb; $n++) {
						$this->pages[$n] = str_replace($alias, $nbstr, $this->pages[$n]);
					}
				}
			}
			if (!empty($this->AliasNbPages)) {
				$nbstr = $this->UTF8ToUTF16BE($nb, false);
				//Replace number of pages
				for($n = 1; $n <= $nb; $n++) {
					$this->pages[$n] = str_replace($this->AliasNbPages, $nbstr, $this->pages[$n]);
				}
			}
			$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
			for($n=1; $n <= $nb; $n++) {
				//Page
				$this->_newobj();
				$this->_out('<</Type /Page');
				$this->_out('/Parent 1 0 R');
				$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]', $this->pagedim[$n]['w'], $this->pagedim[$n]['h']));
				$this->_out('/Resources 2 0 R');
				if (isset($this->PageLinks[$n])) {
					//Links
					$annots = '/Annots [';
					foreach($this->PageLinks[$n] as $pl) {
						$rect = sprintf('%.2f %.2f %.2f %.2f', $pl[0], $pl[1], $pl[0]+$pl[2], $pl[1]-$pl[3]);
						$annots .= '<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
						if (is_string($pl[4])) {
							$annots .= '/A <</S /URI /URI '.$this->_uristring($pl[4]).'>>>>';
						}
						else {
							$l = $this->links[$pl[4]];
							$h = $this->pagedim[$l[0]]['h'];
							$annots .= sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>', 1+2*$l[0], $h-$l[1]*$this->k);
						}
					}
					$this->_out($annots.']');
				}
				$this->_out('/Contents '.($this->n + 1).' 0 R>>');
				$this->_out('endobj');
				//Page content
				$p = ($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
				$this->_newobj();
				$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
				$this->_putstream($p);
				$this->_out('endobj');
			}
			//Pages root
			$this->offsets[1] = strlen($this->buffer);
			$this->_out('1 0 obj');
			$this->_out('<</Type /Pages');
			$kids='/Kids [';
			for($i=0; $i < $nb; $i++) {
				$kids .= (3+2*$i).' 0 R ';
			}
			$this->_out($kids.']');
			$this->_out('/Count '.$nb);
			//$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$this->pagedim[0]['w'],$this->pagedim[0]['h']));
			$this->_out('>>');
			$this->_out('endobj');
		}

		/**
		* Output fonts.
		* _putfonts
		* @access protected
		*/
		protected function _putfonts() {
			$nf = $this->n;
			foreach($this->diffs as $diff) {
				//Encodings
				$this->_newobj();
				$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
				$this->_out('endobj');
			}
			$mqr = get_magic_quotes_runtime();
			set_magic_quotes_runtime(0);
			foreach($this->FontFiles as $file => $info) {
				//Font file embedding
				$this->_newobj();
				$this->FontFiles[$file]['n'] = $this->n;
				$font = file_get_contents($this->_getfontpath().strtolower($file));
				$compressed = (substr($file,-2)=='.z');
				if ((!$compressed) AND (isset($info['length2']))) {
					$header = (ord($font{0}) == 128);
					if ($header) {
						//Strip first binary header
						$font = substr($font,6);
					}
					if ($header AND (ord($font{$info['length1']}) == 128)) {
						//Strip second binary header
						$font = substr($font, 0, $info['length1']).substr($font, $info['length1']+6);
					}
				}
				$this->_out('<</Length '.strlen($font));
				if ($compressed) {
					$this->_out('/Filter /FlateDecode');
				}
				$this->_out('/Length1 '.$info['length1']);
				if (isset($info['length2'])) {
					$this->_out('/Length2 '.$info['length2'].' /Length3 0');
				}
				$this->_out('>>');
				$this->_putstream($font);
				$this->_out('endobj');
			}
			set_magic_quotes_runtime($mqr);
			foreach($this->fonts as $k => $font) {
				//Font objects
				$this->fonts[$k]['n'] = $this->n + 1;
				$type = $font['type'];
				$name = $font['name'];
				if ($type == 'core') {
					//Standard font
					$this->_newobj();
					$this->_out('<</Type /Font');
					$this->_out('/BaseFont /'.$name);
					$this->_out('/Subtype /Type1');
					if (($name != 'symbol') AND ($name != 'zapfdingbats')) {
						$this->_out('/Encoding /WinAnsiEncoding');
					}
					$this->_out('>>');
					$this->_out('endobj');
				} elseif (($type == 'Type1') OR ($type == 'TrueType')) {
					//Additional Type1 or TrueType font
					$this->_newobj();
					$this->_out('<</Type /Font');
					$this->_out('/BaseFont /'.$name);
					$this->_out('/Subtype /'.$type);
					$this->_out('/FirstChar 32 /LastChar 255');
					$this->_out('/Widths '.($this->n + 1).' 0 R');
					$this->_out('/FontDescriptor '.($this->n + 2).' 0 R');
					if ($font['enc']) {
						if (isset($font['diff'])) {
							$this->_out('/Encoding '.($nf + $font['diff']).' 0 R');
						} else {
							$this->_out('/Encoding /WinAnsiEncoding');
						}
					}
					$this->_out('>>');
					$this->_out('endobj');
					//Widths
					$this->_newobj();
					$cw = &$font['cw'];
					$s = '[';
					for($i=32; $i <= 255; $i++) {
						//$s .= $cw[chr($i)].' ';
						$s .= $cw[$i].' ';
					}
					$this->_out($s.']');
					$this->_out('endobj');
					//Descriptor
					$this->_newobj();
					$s = '<</Type /FontDescriptor /FontName /'.$name;
					foreach($font['desc'] as $k => $v) {
						$s .= ' /'.$k.' '.$v;
					}
					$file = $font['file'];
					if ($file) {
						$s .= ' /FontFile'.($type == 'Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
					}
					$this->_out($s.'>>');
					$this->_out('endobj');
				} else {
					//Allow for additional types
					$mtd = '_put'.strtolower($type);
					if (!method_exists($this, $mtd)) {
						$this->Error('Unsupported font type: '.$type);
					}
					$this->$mtd($font);
				}
			}
		}
		
		/**
		 * Output CID-0 fonts.
		 * @param array $font font data
		 * @access protected
		 * @author Andrew Whitehead, Nicola Asuni
		 * @since 3.2.000 (2008-06-23)
		 */
		protected function _putcidfont0($font) {
			$longname = $name = $font['name'];
			$enc = $font['enc'];
			if ($enc) {
				$longname .= "-$enc";
			}
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$longname);
			$this->_out('/Subtype /Type0');
			if ($enc) {
				$this->_out('/Encoding /'.$enc);
			}
			$this->_out('/DescendantFonts ['.($this->n + 1).' 0 R]');
			$this->_out('>>');
			$this->_out('endobj');
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/BaseFont /'.$name);
			$this->_out('/Subtype /CIDFontType0');
			$cidinfo = '/Registry ('.$font['cidinfo']['Registry'].') ';
			$cidinfo .= '/Ordering ('.$font['cidinfo']['Ordering'].') ';
			$cidinfo .= '/Supplement '.$font['cidinfo']['Supplement'];
			$this->_out('/CIDSystemInfo <<'.$cidinfo.'>>');
			$this->_out('/FontDescriptor '.($this->n + 1).' 0 R');
			$codes = array_keys($font['cw']);
			$first = current($codes);
			$last = end($codes);
			$this->_out('/DW '.$font['dw']);
			$w = '/W [';
			$ranges = array();
			$currange = 0;
			for($i = $first; $i <= $last; $i++) {
				if (isset($font['cw'][$i]) AND (!$currange)) {
					$currange = $i - 31;
				} elseif (!isset($font['cw'][$i])) {
					$currange = 0;
				}
				if ($currange) {
					$ranges[$currange][] = $font['cw'][$i];
				}
			}
			foreach($ranges as $k => $ws) {
				$w .= ' '.$k.' [ '.implode(' ', $ws).' ]';
			}
			$w .= ' ]';
			$this->_out($w);
			$this->_out('>>');
			$this->_out('endobj');
			$this->_newobj();
			$s = '<</Type /FontDescriptor /FontName /'.$name;
			foreach($font['desc'] as $k => $v) {
				$s .= ' /'.$k.' '.$v;
			}
			$this->_out($s.'>>');
			$this->_out('endobj');
		}

		/**
		 * Output images.
		 * @access protected
		 */
		protected function _putimages() {
			$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
			reset($this->images);
			while (list($file, $info) = each($this->images)) {
				$this->_newobj();
				$this->images[$file]['n'] = $this->n;
				$this->_out('<</Type /XObject');
				$this->_out('/Subtype /Image');
				$this->_out('/Width '.$info['w']);
				$this->_out('/Height '.$info['h']);
				if (isset($info["masked"])) {
					$this->_out('/SMask '.($this->n-1).' 0 R');
				}
				if ($info['cs'] == 'Indexed') {
					$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal']) / 3 - 1).' '.($this->n + 1).' 0 R]');
				} else {
					$this->_out('/ColorSpace /'.$info['cs']);
					if ($info['cs'] == 'DeviceCMYK') {
						$this->_out('/Decode [1 0 1 0 1 0 1 0]');
					}
				}
				$this->_out('/BitsPerComponent '.$info['bpc']);
				if (isset($info['f'])) {
					$this->_out('/Filter /'.$info['f']);
				}
				if (isset($info['parms'])) {
					$this->_out($info['parms']);
				}
				if (isset($info['trns']) and is_array($info['trns'])) {
					$trns='';
					for($i=0; $i < count($info['trns']); $i++) {
						$trns .= $info['trns'][$i].' '.$info['trns'][$i].' ';
					}
					$this->_out('/Mask ['.$trns.']');
				}
				$this->_out('/Length '.strlen($info['data']).'>>');
				$this->_putstream($info['data']);
				unset($this->images[$file]['data']);
				$this->_out('endobj');
				//Palette
				if ($info['cs'] == 'Indexed') {
					$this->_newobj();
					$pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
					$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
					$this->_putstream($pal);
					$this->_out('endobj');
				}
			}
		}

		/**
		* Output object dictionary for images.
		* @access protected
		*/
		protected function _putxobjectdict() {
			foreach($this->images as $image) {
				$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
			}
		}

		/**
		* Output Resources Dictionary.
		* @access protected
		*/
		protected function _putresourcedict(){
			$this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
			$this->_out('/Font <<');
			foreach($this->fonts as $font) {
				$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
			}
			$this->_out('>>');
			$this->_out('/XObject <<');
			$this->_putxobjectdict();
			$this->_out('>>');
			// visibility
			$this->_out('/Properties <</OC1 '.$this->n_ocg_print.' 0 R /OC2 '.$this->n_ocg_view.' 0 R>>');
			// transparency
			$this->_out('/ExtGState <<');
			foreach($this->extgstates as $k => $extgstate) {
				$this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
			}
			$this->_out('>>');
			// gradients
			if (isset($this->gradients) AND (count($this->gradients) > 0)) {
				$this->_out('/Shading <<');
				foreach($this->gradients as $id => $grad) {
					$this->_out('/Sh'.$id.' '.$grad['id'].' 0 R');
				}
				$this->_out('>>');
			}
		}

		/**
		* Output Resources.
		* @access protected
		*/
		protected function _putresources() {
			$this->_putextgstates();
			$this->_putocg();
			$this->_putfonts();
			$this->_putimages();
			$this->_putshaders();
			//Resource dictionary
			$this->offsets[2] = strlen($this->buffer);
			$this->_out('2 0 obj');
			$this->_out('<<');
			$this->_putresourcedict();
			$this->_out('>>');
			$this->_out('endobj');
			$this->_putjavascript();
			$this->_putbookmarks();
			// encryption
			if ($this->encrypted) {
				$this->_newobj();
				$this->enc_obj_id = $this->n;
				$this->_out('<<');
				$this->_putencryption();
				$this->_out('>>');
				$this->_out('endobj');
			}
		}
		
		/**
		* Adds some Metadata information
		* (see Chapter 10.2 of PDF Reference)
		* @access protected
		*/
		protected function _putinfo() {
			if (!empty($this->title)) {
				$this->_out('/Title '.$this->_textstring($this->title));
			}
			if (!empty($this->author)) {
				$this->_out('/Author '.$this->_textstring($this->author));
			}
			if (!empty($this->subject)) {
				$this->_out('/Subject '.$this->_textstring($this->subject));
			}
			if (!empty($this->keywords)) {
				$this->_out('/Keywords '.$this->_textstring($this->keywords));
			}
			if (!empty($this->creator)) {
				$this->_out('/Creator '.$this->_textstring($this->creator));
			}
			if (defined('PDF_PRODUCER')) {
				$this->_out('/Producer '.$this->_textstring(PDF_PRODUCER));
			}
			$this->_out('/CreationDate '.$this->_datestring('D:'.date('YmdHis')));
			$this->_out('/ModDate '.$this->_datestring('D:'.date('YmdHis')));	
		}
		
		/**
		* Format a date string for meta information
		* @param string $s date string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _datestring($s) {
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			return '('. $this->_escape($s).')';
		}
		
		/**
		* Output Catalog.
		* @access protected
		*/
		protected function _putcatalog() {
			$this->_out('/Type /Catalog');
			$this->_out('/Pages 1 0 R');
			
			if ($this->ZoomMode == 'fullpage') {
				$this->_out('/OpenAction [3 0 R /Fit]');
			} elseif ($this->ZoomMode == 'fullwidth') {
				$this->_out('/OpenAction [3 0 R /FitH null]');
			} elseif ($this->ZoomMode == 'real') {
				$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
			} elseif (!is_string($this->ZoomMode)) {
				$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode / 100).']');
			}
			if (isset($this->LayoutMode) AND (!empty($this->LayoutMode))) {
				$this->_out('/PageLayout /'.$this->LayoutMode.'');
			}
			if (isset($this->PageMode) AND (!empty($this->PageMode))) {
				$this->_out('/PageMode /'.$this->PageMode);
			}
			if (isset($this->l['a_meta_language'])) {
				$this->_out('/Lang /'.$this->l['a_meta_language']);
			}
			if (!empty($this->javascript)) {
				$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
			}
			if (count($this->outlines) > 0) {
				$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
				$this->_out('/PageMode /UseOutlines');
			}
			$this->_putviewerpreferences();
			$p = $this->n_ocg_print.' 0 R';
			$v = $this->n_ocg_view.' 0 R';
			$as = "<</Event /Print /OCGs [".$p." ".$v."] /Category [/Print]>> <</Event /View /OCGs [".$p." ".$v."] /Category [/View]>>";
			$this->_out("/OCProperties <</OCGs [".$p." ".$v."] /D <</ON [".$p."] /OFF [".$v."] /AS [".$as."]>>>>");
			$this->_putuserrights();
		}
		
		/**
		* Output viewer preferences.
		* @author Nicola asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function _putviewerpreferences() {
			$this->_out('/ViewerPreferences<<');
			if ($this->rtl) {
				$this->_out('/Direction /R2L');
			} else {
				$this->_out('/Direction /L2R');
			}
			if (isset($this->viewer_preferences['HideToolbar']) AND ($this->viewer_preferences['HideToolbar'])) {
				$this->_out('/HideToolbar true');
			}
			if (isset($this->viewer_preferences['HideMenubar']) AND ($this->viewer_preferences['HideMenubar'])) {
				$this->_out('/HideMenubar true');
			}
			if (isset($this->viewer_preferences['HideWindowUI']) AND ($this->viewer_preferences['HideWindowUI'])) {
				$this->_out('/HideWindowUI true');
			}
			if (isset($this->viewer_preferences['FitWindow']) AND ($this->viewer_preferences['FitWindow'])) {
				$this->_out('/FitWindow true');
			}
			if (isset($this->viewer_preferences['CenterWindow']) AND ($this->viewer_preferences['CenterWindow'])) {
				$this->_out('/CenterWindow true');
			}
			if (isset($this->viewer_preferences['DisplayDocTitle']) AND ($this->viewer_preferences['DisplayDocTitle'])) {
				$this->_out('/DisplayDocTitle true');
			}
			if (isset($this->viewer_preferences['NonFullScreenPageMode'])) {
				$this->_out('/NonFullScreenPageMode /'.$this->viewer_preferences['NonFullScreenPageMode'].'');
			}
			if (isset($this->viewer_preferences['ViewArea'])) {
				$this->_out('/ViewArea /'.$this->viewer_preferences['ViewArea']);
			}
			if (isset($this->viewer_preferences['ViewClip'])) {
				$this->_out('/ViewClip /'.$this->viewer_preferences['ViewClip']);
			}
			if (isset($this->viewer_preferences['PrintArea'])) {
				$this->_out('/PrintArea /'.$this->viewer_preferences['PrintArea']);
			}
			if (isset($this->viewer_preferences['PrintClip'])) {
				$this->_out('/PrintClip /'.$this->viewer_preferences['PrintClip']);
			}
			if (isset($this->viewer_preferences['PrintScaling'])) {
				$this->_out('/PrintScaling /'.$this->viewer_preferences['PrintScaling']);
			}
			if (isset($this->viewer_preferences['Duplex']) AND (!empty($this->viewer_preferences['Duplex']))) {
				$this->_out('/Duplex /'.$this->viewer_preferences['Duplex']);
			}
			if (isset($this->viewer_preferences['PickTrayByPDFSize'])) {
				if ($this->viewer_preferences['PickTrayByPDFSize']) {
					$this->_out('/PickTrayByPDFSize true');
				} else {
					$this->_out('/PickTrayByPDFSize false');
				}
			}
			if (isset($this->viewer_preferences['PrintPageRange'])) {
				$PrintPageRangeNum = "";
				foreach ($this->viewer_preferences['PrintPageRange'] as $k => $v) {
					$PrintPageRangeNum .= " ".($v-1)."";
				}
				$this->_out('/PrintPageRange ['.substr($PrintPageRangeNum,1).']');
			}
			if (isset($this->viewer_preferences['NumCopies'])) {
				$this->_out('/NumCopies '.intval($this->viewer_preferences['NumCopies']));
			}
			$this->_out('>>');
		}

		/**
		* Output trailer.
		* @access protected
		*/
		protected function _puttrailer() {
			$this->_out('/Size '.($this->n + 1));
			$this->_out('/Root '.$this->n.' 0 R');
			$this->_out('/Info '.($this->n - 1).' 0 R');
			if ($this->encrypted) {
				$this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
				$this->_out('/ID [()()]');
			}
		}

		/**
		* Output PDF header.
		* @access protected
		*/
		protected function _putheader() {
			$this->_out('%PDF-'.$this->PDFVersion);
		}

		/**
		* Output end of document (EOF).
		* @access protected
		*/
		protected function _enddoc() {
			$this->_putheader();
			$this->_putpages();
			$this->_putresources();
			//Info
			$this->_newobj();
			$this->_out('<<');
			$this->_putinfo();
			$this->_out('>>');
			$this->_out('endobj');
			//Catalog
			$this->_newobj();
			$this->_out('<<');
			$this->_putcatalog();
			$this->_out('>>');
			$this->_out('endobj');
			//Cross-ref
			$o = strlen($this->buffer);
			$this->_out('xref');
			$this->_out('0 '.($this->n + 1));
			$this->_out('0000000000 65535 f ');
			for($i=1; $i <= $this->n; $i++) {
				$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
			}
			//Trailer
			$this->_out('trailer');
			$this->_out('<<');
			$this->_puttrailer();
			$this->_out('>>');
			$this->_out('startxref');
			$this->_out($o);
			$this->_out('%%EOF');
			$this->state = 3;
		}

		/**
		* Initialize a new page.
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		* @access protected
		*/
		protected function _beginpage($orientation='', $format='') {
			$this->page++;
			$this->pages[$this->page] = ""; // this mark should be removed before output
			$this->state = 2;
			if (empty($orientation)) {
				if (isset($this->CurOrientation)) {
					$orientation = $this->CurOrientation;
				} else {
					$orientation = 'P';
				}
			}
			if (!empty($format)) {
				$this->setPageFormat($format, $orientation);
			} else {
				$this->setPageOrientation($orientation);
			}
			if ($this->rtl) {
				$this->x = $this->w - $this->rMargin;
			} else {
				$this->x = $this->lMargin;
			}
			$this->y = $this->tMargin;
			if ($this->newpagegroup){
				// start a new group
				$n = sizeof($this->pagegroups) + 1;
				$alias = "{nb".$n."}";
				$this->pagegroups[$alias] = 1;
				$this->currpagegroup = $alias;
				$this->newpagegroup = false;
			} elseif ($this->currpagegroup) {
				$this->pagegroups[$this->currpagegroup]++;
			}
		}

		/**
		* Mark end of page.
		* @access protected
		*/
		protected function _endpage() {
			$this->setVisibility("all");
			$this->state = 1;
		}

		/**
		* Begin a new object.
		* @access protected
		*/
		protected function _newobj() {
			$this->n++;
			$this->offsets[$this->n] = strlen($this->buffer);
			$this->_out($this->n.' 0 obj');
		}

		/**
		* Underline text.
		* @param int $x X coordinate
		* @param int $y Y coordinate
		* @param string $txt text to underline
		* @access protected
		*/
		protected function _dounderline($x, $y, $txt) {
			$up = $this->CurrentFont['up'];
			$ut = $this->CurrentFont['ut'];
			$w = $this->GetStringWidth($txt);
			return sprintf('%.2f %.2f %.2f %.2f re f', $x * $this->k, ($this->h - ($y - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
		}
		
		/**
		* Line through text.
		* @param int $x X coordinate
		* @param int $y Y coordinate
		* @param string $txt text to underline
		* @access protected
		*/
		protected function _dolinethrough($x, $y, $txt) {
			$up = $this->CurrentFont['up'];
			$ut = $this->CurrentFont['ut'];
			$w = $this->GetStringWidth($txt);
			return sprintf('%.2f %.2f %.2f %.2f re f', $x * $this->k, ($this->h - ($y - ($this->FontSize/2) - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
		}
		
		/**
		* Read a 4-byte integer from file.
		* @param string $f file name.
		* @return 4-byte integer
		* @access protected
		*/
		protected function _freadint($f) {
			$a = unpack('Ni', fread($f,4));
			return $a['i'];
		}

		/**
		* Format a text string for meta information
		* @param string $s string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _textstring($s) {
			if ($this->isunicode) {
				if (($this->CurrentFont['type'] == 'core') OR ($this->CurrentFont['type'] == 'TrueType') OR ($this->CurrentFont['type'] == 'Type1')) {
					$s = $this->UTF8ToLatin1($s);
				} else {
					//Convert string to UTF-16BE
					$s = $this->UTF8ToUTF16BE($s, true);
				}
			}
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			return '('. $this->_escape($s).')';
		}
		
		/**
		* Format an URI string
		* @param string $s string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _uristring($s) {
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			return '('.$this->_escape($s).')';
		}
		
		/**
		* Format a text string
		* @param string $s string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _escapetext($s) {
			if ($this->isunicode) {
				if (($this->CurrentFont['type'] == 'core') OR ($this->CurrentFont['type'] == 'TrueType') OR ($this->CurrentFont['type'] == 'Type1')) {
					$s = $this->UTF8ToLatin1($s);
				} else {
					//Convert string to UTF-16BE and reverse RTL language
					$s = $this->utf8StrRev($s, false, $this->tmprtl);
				}
			}
			return $this->_escape($s);
		}

		/**
		* Add "\" before "\", "(" and ")"
		* @param string $s string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _escape($s) {
			// the chr(13) substitution fixes the Bugs item #1421290.
			return strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r'));
		}

		/**
		* Output a stream.
		* @param string $s string to output.
		* @access protected
		*/
		protected function _putstream($s) {
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			$this->_out('stream');
			$this->_out($s);
			$this->_out('endstream');
		}
		
		/**
		* Output a string to the document.
		* @param string $s string to output.
		* @access protected
		*/
		protected function _out($s) {
			if ($this->state == 2) {
				if (isset($this->footerlen[$this->page]) AND ($this->footerlen[$this->page] > 0)) {
					// puts data before page footer
					$page = substr($this->pages[$this->page], 0, -$this->footerlen[$this->page]);
					$footer = substr($this->pages[$this->page], -$this->footerlen[$this->page]);
					$this->pages[$this->page] = $page." ".$s."\n".$footer;
				} else {
					$this->pages[$this->page] .= $s."\n";
				}
			} else {
				$this->buffer .= $s."\n";
			}
		}

		/**
		* Adds unicode fonts.<br>
		* Based on PDF Reference 1.3 (section 5)
		* @access protected
		* @author Nicola Asuni
		* @since 1.52.0.TC005 (2005-01-05)
		*/
		protected function _puttruetypeunicode($font) {
			// Type0 Font
			// A composite font composed of other fonts, organized hierarchically
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /Type0');
			$this->_out('/BaseFont /'.$font['name'].'');
			$this->_out('/Encoding /Identity-H'); //The horizontal identity mapping for 2-byte CIDs; may be used with CIDFonts using any Registry, Ordering, and Supplement values.
			$this->_out('/DescendantFonts ['.($this->n + 1).' 0 R]');
			$this->_out('/ToUnicode '.($this->n + 2).' 0 R');
			$this->_out('>>');
			$this->_out('endobj');
			// CIDFontType2
			// A CIDFont whose glyph descriptions are based on TrueType font technology
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /CIDFontType2');
			$this->_out('/BaseFont /'.$font['name'].'');
			$this->_out('/CIDSystemInfo '.($this->n + 2).' 0 R'); 
			$this->_out('/FontDescriptor '.($this->n + 3).' 0 R');
			if (isset($font['desc']['MissingWidth'])){
				$this->_out('/DW '.$font['desc']['MissingWidth'].''); // The default width for glyphs in the CIDFont MissingWidth
			}
			$w = "";
			foreach ($font['cw'] as $cid => $width) {
				$w .= ''.$cid.' ['.$width.'] '; // define a specific width for each individual CID
			}
			$this->_out('/W ['.$w.']'); // A description of the widths for the glyphs in the CIDFont
			$this->_out('/CIDToGIDMap '.($this->n + 4).' 0 R');
			$this->_out('>>');
			$this->_out('endobj');
			// ToUnicode
			// is a stream object that contains the definition of the CMap
			// (PDF Reference 1.3 chap. 5.9)
			$this->_newobj();
			$this->_out('<</Length 345>>');
			$this->_out('stream');
			$this->_out('/CIDInit /ProcSet findresource begin');
			$this->_out('12 dict begin');
			$this->_out('begincmap');
			$this->_out('/CIDSystemInfo');
			$this->_out('<</Registry (Adobe)');
			$this->_out('/Ordering (UCS)');
			$this->_out('/Supplement 0');
			$this->_out('>> def');
			$this->_out('/CMapName /Adobe-Identity-UCS def');
			$this->_out('/CMapType 2 def');
			$this->_out('1 begincodespacerange');
			$this->_out('<0000> <FFFF>');
			$this->_out('endcodespacerange');
			$this->_out('1 beginbfrange');
			$this->_out('<0000> <FFFF> <0000>');
			$this->_out('endbfrange');
			$this->_out('endcmap');
			$this->_out('CMapName currentdict /CMap defineresource pop');
			$this->_out('end');
			$this->_out('end');
			$this->_out('endstream');
			$this->_out('endobj');
			// CIDSystemInfo dictionary
			// A dictionary containing entries that define the character collection of the CIDFont.
			$this->_newobj();
			$this->_out('<</Registry (Adobe)'); // A string identifying an issuer of character collections
			$this->_out('/Ordering (UCS)'); // A string that uniquely names a character collection issued by a specific registry
			$this->_out('/Supplement 0'); // The supplement number of the character collection.
			$this->_out('>>');
			$this->_out('endobj');
			// Font descriptor
			// A font descriptor describing the CIDFont default metrics other than its glyph widths
			$this->_newobj();
			$this->_out('<</Type /FontDescriptor');
			$this->_out('/FontName /'.$font['name']);
			foreach ($font['desc'] as $key => $value) {
				$this->_out('/'.$key.' '.$value);
			}
			if ($font['file']) {
				// A stream containing a TrueType font program
				$this->_out('/FontFile2 '.$this->FontFiles[$font['file']]['n'].' 0 R');
			}
			$this->_out('>>');
			$this->_out('endobj');
			// Embed CIDToGIDMap
			// A specification of the mapping from CIDs to glyph indices
			$this->_newobj();
			$ctgfile = $this->_getfontpath().strtolower($font['ctg']);
			if (!file_exists($ctgfile)) {
				$this->Error('Font file not found: '.$ctgfile);
			}
			$size = filesize($ctgfile);
			$this->_out('<</Length '.$size.'');
			if (substr($ctgfile, -2) == '.z') { // check file extension
				/* Decompresses data encoded using the public-domain 
				zlib/deflate compression method, reproducing the 
				original text or binary data */
				$this->_out('/Filter /FlateDecode');
			}
			$this->_out('>>');
			$this->_putstream(file_get_contents($ctgfile));
			$this->_out('endobj');
		}
		
		 /**
		 * Converts UTF-8 strings to codepoints array.<br>
		 * Invalid byte sequences will be replaced with 0xFFFD (replacement character)<br>
		 * Based on: http://www.faqs.org/rfcs/rfc3629.html
		 * <pre>
		 * 	  Char. number range  |        UTF-8 octet sequence
		 *       (hexadecimal)    |              (binary)
		 *    --------------------+-----------------------------------------------
		 *    0000 0000-0000 007F | 0xxxxxxx
		 *    0000 0080-0000 07FF | 110xxxxx 10xxxxxx
		 *    0000 0800-0000 FFFF | 1110xxxx 10xxxxxx 10xxxxxx
		 *    0001 0000-0010 FFFF | 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
		 *    ---------------------------------------------------------------------
		 *
		 *   ABFN notation:
		 *   ---------------------------------------------------------------------
		 *   UTF8-octets = *( UTF8-char )
		 *   UTF8-char   = UTF8-1 / UTF8-2 / UTF8-3 / UTF8-4
		 *   UTF8-1      = %x00-7F
		 *   UTF8-2      = %xC2-DF UTF8-tail
		 *
		 *   UTF8-3      = %xE0 %xA0-BF UTF8-tail / %xE1-EC 2( UTF8-tail ) /
		 *                 %xED %x80-9F UTF8-tail / %xEE-EF 2( UTF8-tail )
		 *   UTF8-4      = %xF0 %x90-BF 2( UTF8-tail ) / %xF1-F3 3( UTF8-tail ) /
		 *                 %xF4 %x80-8F 2( UTF8-tail )
		 *   UTF8-tail   = %x80-BF
		 *   ---------------------------------------------------------------------
		 * </pre>
		 * @param string $str string to process.
		 * @return array containing codepoints (UTF-8 characters values)
		 * @access protected
		 * @author Nicola Asuni
		 * @since 1.53.0.TC005 (2005-01-05)
		 */
		protected function UTF8StringToArray($str) {
			if (!$this->isunicode) {
				// split string into array of equivalent codes
				$strarr = array();
				$strlen = strlen($str);
				for($i=0; $i < $strlen; $i++) {
					$strarr[] = ord($str{$i});
				}
				return $strarr;
			}
			$unicode = array(); // array containing unicode values
			$bytes  = array(); // array containing single character byte sequences
			$numbytes  = 1; // number of octetc needed to represent the UTF-8 character
			$str .= ""; // force $str to be a string
			$length = strlen($str);
			for($i = 0; $i < $length; $i++) {
				$char = ord($str{$i}); // get one string character at time
				if (count($bytes) == 0) { // get starting octect
					if ($char <= 0x7F) {
						$unicode[] = $char; // use the character "as is" because is ASCII
						$numbytes = 1;
					} elseif (($char >> 0x05) == 0x06) { // 2 bytes character (0x06 = 110 BIN)
						$bytes[] = ($char - 0xC0) << 0x06; 
						$numbytes = 2;
					} elseif (($char >> 0x04) == 0x0E) { // 3 bytes character (0x0E = 1110 BIN)
						$bytes[] = ($char - 0xE0) << 0x0C; 
						$numbytes = 3;
					} elseif (($char >> 0x03) == 0x1E) { // 4 bytes character (0x1E = 11110 BIN)
						$bytes[] = ($char - 0xF0) << 0x12; 
						$numbytes = 4;
					} else {
						// use replacement character for other invalid sequences
						$unicode[] = 0xFFFD;
						$bytes = array();
						$numbytes = 1;
					}
				} elseif (($char >> 0x06) == 0x02) { // bytes 2, 3 and 4 must start with 0x02 = 10 BIN
					$bytes[] = $char - 0x80;
					if (count($bytes) == $numbytes) {
						// compose UTF-8 bytes to a single unicode value
						$char = $bytes[0];
						for($j = 1; $j < $numbytes; $j++) {
							$char += ($bytes[$j] << (($numbytes - $j - 1) * 0x06));
						}
						if ((($char >= 0xD800) AND ($char <= 0xDFFF)) OR ($char >= 0x10FFFF)) {
							/* The definition of UTF-8 prohibits encoding character numbers between
							U+D800 and U+DFFF, which are reserved for use with the UTF-16
							encoding form (as surrogate pairs) and do not directly represent
							characters. */
							$unicode[] = 0xFFFD; // use replacement character
						} else {
							$unicode[] = $char; // add char to array
						}
						// reset data for next char
						$bytes = array(); 
						$numbytes = 1;
					}
				} else {
					// use replacement character for other invalid sequences
					$unicode[] = 0xFFFD;
					$bytes = array();
					$numbytes = 1;
				}
			}
			return $unicode;
		}
		
		/**
		 * Converts UTF-8 strings to UTF16-BE.<br>
		 * @param string $str string to process.
		 * @param boolean $setbom if true set the Byte Order Mark (BOM = 0xFEFF)
		 * @return string
		 * @access protected
		 * @author Nicola Asuni
		 * @since 1.53.0.TC005 (2005-01-05)
		 * @uses UTF8StringToArray(), arrUTF8ToUTF16BE()
		 */
		protected function UTF8ToUTF16BE($str, $setbom=true) {
			if (!$this->isunicode) {
				return $str; // string is not in unicode
			}
			$unicode = $this->UTF8StringToArray($str); // array containing UTF-8 unicode values
			return $this->arrUTF8ToUTF16BE($unicode, $setbom);
		}
		
		/**
		 * Converts UTF-8 strings to Latin1 when using the standard 14 core fonts.<br>
		 * @param string $str string to process.
		 * @return string
		 * @author Andrew Whitehead, Nicola Asuni
		 * @access protected
		 * @since 3.2.000 (2008-06-23)
		 */
		protected function UTF8ToLatin1($str) {
			if (!$this->isunicode) {
				return $str; // string is not in unicode
			}
			$outstr = ""; // string to be returned
			$unicode = $this->UTF8StringToArray($str); // array containing UTF-8 unicode values
			foreach ($unicode as $char) {
				if ($char == 0xFFFD) {
					// skip
				} elseif ($char == 0x2022) {
					// fix for middot
					$outstr .= chr(183);
				} elseif ($char < 256) {
					$outstr .= chr($char);
				} else {
					$outstr .= '?';
				}
			}
			return $outstr;
		}

		/**
		 * Converts array of UTF-8 characters to UTF16-BE string.<br>
		 * Based on: http://www.faqs.org/rfcs/rfc2781.html
	 	 * <pre>
		 *   Encoding UTF-16:
		 * 
 		 *   Encoding of a single character from an ISO 10646 character value to
		 *    UTF-16 proceeds as follows. Let U be the character number, no greater
		 *    than 0x10FFFF.
		 * 
		 *    1) If U < 0x10000, encode U as a 16-bit unsigned integer and
		 *       terminate.
		 * 
		 *    2) Let U' = U - 0x10000. Because U is less than or equal to 0x10FFFF,
		 *       U' must be less than or equal to 0xFFFFF. That is, U' can be
		 *       represented in 20 bits.
		 * 
		 *    3) Initialize two 16-bit unsigned integers, W1 and W2, to 0xD800 and
		 *       0xDC00, respectively. These integers each have 10 bits free to
		 *       encode the character value, for a total of 20 bits.
		 * 
		 *    4) Assign the 10 high-order bits of the 20-bit U' to the 10 low-order
		 *       bits of W1 and the 10 low-order bits of U' to the 10 low-order
		 *       bits of W2. Terminate.
		 * 
		 *    Graphically, steps 2 through 4 look like:
		 *    U' = yyyyyyyyyyxxxxxxxxxx
		 *    W1 = 110110yyyyyyyyyy
		 *    W2 = 110111xxxxxxxxxx
		 * </pre>
		 * @param array $unicode array containing UTF-8 unicode values
		 * @param boolean $setbom if true set the Byte Order Mark (BOM = 0xFEFF)
		 * @return string
		 * @access protected
		 * @author Nicola Asuni
		 * @since 2.1.000 (2008-01-08)
		 * @see UTF8ToUTF16BE()
		 */
		protected function arrUTF8ToUTF16BE($unicode, $setbom=true) {
			$outstr = ""; // string to be returned
			if ($setbom) {
				$outstr .= "\xFE\xFF"; // Byte Order Mark (BOM)
			}
			foreach($unicode as $char) {
				if ($char == 0xFFFD) {
					$outstr .= "\xFF\xFD"; // replacement character
				} elseif ($char < 0x10000) {
					$outstr .= chr($char >> 0x08);
					$outstr .= chr($char & 0xFF);
				} else {
					$char -= 0x10000;
					$w1 = 0xD800 | ($char >> 0x10);
					$w2 = 0xDC00 | ($char & 0x3FF);	
					$outstr .= chr($w1 >> 0x08);
					$outstr .= chr($w1 & 0xFF);
					$outstr .= chr($w2 >> 0x08);
					$outstr .= chr($w2 & 0xFF);
				}
			}
			return $outstr;
		}
		// ====================================================
		
		/**
	 	 * Set header font.
		 * @param array $font font
		 * @since 1.1
		 */
		public function setHeaderFont($font) {
			$this->header_font = $font;
		}
		
		/**
	 	 * Get header font.
	 	 * @return array()
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getHeaderFont() {
			return $this->header_font;
		}
		
		/**
	 	 * Set footer font.
		 * @param array $font font
		 * @since 1.1
		 */
		public function setFooterFont($font) {
			$this->footer_font = $font;
		}
		
		/**
	 	 * Get Footer font.
	 	 * @return array()
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getFooterFont() {
			return $this->footer_font;
		}
		
		/**
	 	 * Set language array.
		 * @param array $language
		 * @since 1.1
		 */
		public function setLanguageArray($language) {
			$this->l = $language;
			$this->rtl = $this->l['a_meta_dir']=='rtl' ? true : false;
		}
		
		/**
		 * Returns the PDF data.
		 */
		public function getPDFData() {
			if ($this->state < 3) {
				$this->Close();
			}
			return $this->buffer;
		}
		
		/**
		 * Sets font style.
		 * @param string $tag tag name in lowercase. Supported tags are:<ul>
		 * <li>b : bold text</li>
		 * <li>i : italic</li>
		 * <li>u : underlined</li>
		 * <li>lt : line-through</li></ul>
		 * @param boolean $enable
		 * @access protected
		 */
		protected function setStyle($tag, $enable) {
			//Modify style and select corresponding font
			$this->$tag += ($enable ? 1 : -1);
			$style = '';
			foreach(array('b', 'i', 'u', 'd') as $s) {
				if ($this->$s > 0) {
					$style .= $s;
				}
			}
			$this->SetFont('', $style);
		}
		
		/**
		 * Output anchor link.
		 * @param string $url link URL
		 * @param string $name link name
		 * @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		 * @param boolean $firstline if true prints only the first line and return the remaining string.
		 * @return the number of cells used or the remaining text if $firstline = true;
		 * @access public
		 */
		public function addHtmlLink($url, $name, $fill=0, $firstline=false) {
			$prevcolor = $this->fgcolor;
			$this->SetTextColor(0, 0, 255);
			$this->setStyle('u', true);
			$ret = $this->Write($this->lasth, $name, $url, $fill, '', false, 0, $firstline);
			$this->setStyle('u', false);
			$this->SetTextColorArray($prevcolor);
			return $ret;
		}
		
		/**
		 * Returns an associative array (keys: R,G,B) from an html color name or a six-digit or three-digit hexadecimal color representation (i.e. #3FE5AA or #7FF).
		 * @param string $color html color 
		 * @return array
		 * @access protected
		 */		
		protected function convertHTMLColorToDec($color="#000000") {
			global $webcolor;
			// set default color to be returned in case of error
			$returncolor = array ('R' => 0, 'G' => 0, 'B' => 0);
			if (empty($color)) {
				return $returncolor;
			}
			if (substr($color, 0, 1) != "#") {
				// decode color name
				if (isset($webcolor[strtolower($color)])) {
					$color_code = $webcolor[strtolower($color)];
				} else {
					return $returncolor;
				}
			} else {
				$color_code = substr($color, 1);
			}
			switch (strlen($color_code)) {
				case 3: {
					// three-digit hexadecimal representation
					$r = substr($color_code, 0, 1);
					$g = substr($color_code, 1, 1);
					$b = substr($color_code, 2, 1);
					$returncolor['R'] = hexdec($r.$r);
					$returncolor['G'] = hexdec($g.$g);
					$returncolor['B'] = hexdec($b.$b);
					break;
				}
				case 6: {
					// six-digit hexadecimal representation
					$returncolor['R'] = hexdec(substr($color_code, 0, 2));
					$returncolor['G'] = hexdec(substr($color_code, 2, 2));
					$returncolor['B'] = hexdec(substr($color_code, 4, 2));
					break;
				}
			}
			return $returncolor;
		}
		
		/**
		 * Converts pixels to Units.
		 * @param int $px pixels
		 * @return float millimeters
		 * @access public
		 */
		public function pixelsToUnits($px){
			return $px / $this->k;
		}
			
		/**
		 * Reverse function for htmlentities. 
		 * Convert entities in UTF-8.
		 * @param $text_to_convert Text to convert.
		 * @return string converted
		 */
		public function unhtmlentities($text_to_convert) {
			return html_entity_decode($text_to_convert, ENT_QUOTES, $this->encoding);
		}
		
		// ENCRYPTION METHODS ----------------------------------
		// SINCE 2.0.000 (2008-01-02)
		/**
		* Compute encryption key depending on object number where the encrypted data is stored
		* @param int $n object number
		* @since 2.0.000 (2008-01-02)
		*/
		protected function _objectkey($n) {
			return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)),0,10);
		}
		
		/**
		 * Put encryption on PDF document.
		 * @since 2.0.000 (2008-01-02)
		 */
		protected function _putencryption() {
			$this->_out('/Filter /Standard');
			$this->_out('/V 1');
			$this->_out('/R 2');
			$this->_out('/O ('.$this->_escape($this->Ovalue).')');
			$this->_out('/U ('.$this->_escape($this->Uvalue).')');
			$this->_out('/P '.$this->Pvalue);
		}
		
		/**
		* Returns the input text exrypted using RC4 algorithm and the specified key.
		* RC4 is the standard encryption algorithm used in PDF format
		* @param string $key encryption key
		* @param String $text input text to be encrypted
		* @return String encrypted text
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _RC4($key, $text) {
			if ($this->last_rc4_key != $key) {
				$k = str_repeat($key, 256/strlen($key)+1);
				$rc4 = range(0,255);
				$j = 0;
				for ($i=0; $i < 256; $i++) {
					$t = $rc4[$i];
					$j = ($j + $t + ord($k{$i})) % 256;
					$rc4[$i] = $rc4[$j];
					$rc4[$j] = $t;
				}
				$this->last_rc4_key = $key;
				$this->last_rc4_key_c = $rc4;
			} else {
				$rc4 = $this->last_rc4_key_c;
			}
			$len = strlen($text);
			$a = 0;
			$b = 0;
			$out = '';
			for ($i=0; $i < $len; $i++) {
				$a = ($a + 1) % 256;
				$t = $rc4[$a];
				$b = ($b + $t) % 256;
				$rc4[$a] = $rc4[$b];
				$rc4[$b] = $t;
				$k = $rc4[($rc4[$a] + $rc4[$b]) % 256];
				$out .= chr(ord($text{$i}) ^ $k);
			}
			return $out;
		}
		
		/**
		* Encrypts a string using MD5 and returns it's value as a binary string.
		* @param string $str input string
		* @return String MD5 encrypted binary string
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _md5_16($str) {
			return pack('H*',md5($str));
		}
		
		/**
		* Compute O value (used for RC4 encryption)
		* @param String $user_pass user password
		* @param String $owner_pass user password
		* @return String O value
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _Ovalue($user_pass, $owner_pass) {
			$tmp = $this->_md5_16($owner_pass);
			$owner_RC4_key = substr($tmp,0,5);
			return $this->_RC4($owner_RC4_key, $user_pass);
		}
		
		/**
		* Compute U value (used for RC4 encryption)
		* @return String U value
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _Uvalue() {
			return $this->_RC4($this->encryption_key, $this->padding);
		}
		
		/**
		* Compute encryption key
		* @param String $user_pass user password
		* @param String $owner_pass user password
		* @param String $protection protection type
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _generateencryptionkey($user_pass, $owner_pass, $protection) {
			// Pad passwords
			$user_pass = substr($user_pass.$this->padding,0,32);
			$owner_pass = substr($owner_pass.$this->padding,0,32);
			// Compute O value
			$this->Ovalue = $this->_Ovalue($user_pass, $owner_pass);
			// Compute encyption key
			$tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
			$this->encryption_key = substr($tmp,0,5);
			// Compute U value
			$this->Uvalue = $this->_Uvalue();
			// Compute P value
			$this->Pvalue = -(($protection^255)+1);
		}
		
		/**
		* Set document protection
		* The permission array is composed of values taken from the following ones:
		* - copy: copy text and images to the clipboard
		* - print: print the document
		* - modify: modify it (except for annotations and forms)
		* - annot-forms: add annotations and forms 
		* Remark: the protection against modification is for people who have the full Acrobat product.
		* If you don't set any password, the document will open as usual. If you set a user password, the PDF viewer will ask for it before displaying the document. The master password, if different from the user one, can be used to get full access.
		* Note: protecting a document requires to encrypt it, which increases the processing time a lot. This can cause a PHP time-out in some cases, especially if the document contains images or fonts.
		* @param Array $permissions the set of permissions. Empty by default (only viewing is allowed). (print, modify, copy, annot-forms)
		* @param String $user_pass user password. Empty by default.
		* @param String $owner_pass owner password. If not specified, a random value is used.
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		public function SetProtection($permissions=array(), $user_pass='', $owner_pass=null) {
			$options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32);
			$protection = 192;
			foreach($permissions as $permission) {
				if (!isset($options[$permission])) {
					$this->Error('Incorrect permission: '.$permission);
				}
				$protection += $options[$permission];
			}
			if ($owner_pass === null) {
				$owner_pass = uniqid(rand());
			}
			$this->encrypted = true;
			$this->_generateencryptionkey($user_pass, $owner_pass, $protection);
		}
		
		// END OF ENCRYPTION FUNCTIONS -------------------------
		
		// START TRANSFORMATIONS SECTION -----------------------
		// authors: Moritz Wagner, Andreas Wurmser, Nicola Asuni
		
		/**
		* Starts a 2D tranformation saving current graphic state.
		* This function must be called before scaling, mirroring, translation, rotation and skewing.
		* Use StartTransform() before, and StopTransform() after the transformations to restore the normal behavior.
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function StartTransform() {
			$this->_out('q');
		}
		
		/**
		* Stops a 2D tranformation restoring previous graphic state.
		* This function must be called after scaling, mirroring, translation, rotation and skewing.
		* Use StartTransform() before, and StopTransform() after the transformations to restore the normal behavior.
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function StopTransform() {
			$this->_out('Q');
		}
		/**
		* Horizontal Scaling.
		* @param float $s_x scaling factor for width as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function ScaleX($s_x, $x='', $y=''){
			$this->Scale($s_x, 100, $x, $y);
		}
		
		/**
		* Vertical Scaling.
		* @param float $s_y scaling factor for height as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function ScaleY($s_y, $x='', $y=''){
			$this->Scale(100, $s_y, $x, $y);
		}
		
		/**
		* Vertical and horizontal proportional Scaling.
		* @param float $s scaling factor for width and height as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function ScaleXY($s, $x='', $y=''){
			$this->Scale($s, $s, $x, $y);
		}
		
		/**
		* Vertical and horizontal non-proportional Scaling.
		* @param float $s_x scaling factor for width as percent. 0 is not allowed.
		* @param float $s_y scaling factor for height as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Scale($s_x, $s_y, $x='', $y=''){
			if ($x === '') {
				$x=$this->x;
			}
			if ($y === '') {
				$y=$this->y;
			}
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			if (($s_x == 0) OR ($s_y == 0)) {
				$this->Error('Please use values unequal to zero for Scaling');
			}
			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;
			//calculate elements of transformation matrix
			$s_x /= 100;
			$s_y /= 100;
			$tm[0] = $s_x;
			$tm[1] = 0;
			$tm[2] = 0;
			$tm[3] = $s_y;
			$tm[4] = $x * (1 - $s_x);
			$tm[5] = $y * (1 - $s_y);
			//scale the coordinate system
			$this->Transform($tm);
		}
		
		/**
		* Horizontal Mirroring.
		* @param int $x abscissa of the point. Default is current x position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorH($x=''){
			$this->Scale(-100, 100, $x);
		}
		
		/**
		* Verical Mirroring.
		* @param int $y ordinate of the point. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorV($y=''){
			$this->Scale(100, -100, '', $y);
		}
		
		/**
		* Point reflection mirroring.
		* @param int $x abscissa of the point. Default is current x position
		* @param int $y ordinate of the point. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorP($x='',$y=''){
			$this->Scale(-100, -100, $x, $y);
		}
		
		/**
		* Reflection against a straight line through point (x, y) with the gradient angle (angle).
		* @param float $angle gradient angle of the straight line. Default is 0 (horizontal line).
		* @param int $x abscissa of the point. Default is current x position
		* @param int $y ordinate of the point. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorL($angle=0, $x='',$y=''){
			$this->Scale(-100, 100, $x, $y);
			$this->Rotate(-2*($angle-90), $x, $y);
		}
		
		/**
		* Translate graphic object horizontally.
		* @param int $t_x movement to the right (or left for RTL)
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function TranslateX($t_x){
			$this->Translate($t_x, 0);
		}
		
		/**
		* Translate graphic object vertically.
		* @param int $t_y movement to the bottom
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function TranslateY($t_y){
			$this->Translate(0, $t_y);
		}
		
		/**
		* Translate graphic object horizontally and vertically.
		* @param int $t_x movement to the right
		* @param int $t_y movement to the bottom
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Translate($t_x, $t_y){
			if ($this->rtl) {
				$t_x = -$t_x;
			}
			//calculate elements of transformation matrix
			$tm[0] = 1;
			$tm[1] = 0;
			$tm[2] = 0;
			$tm[3] = 1;
			$tm[4] = $t_x * $this->k;
			$tm[5] = -$t_y * $this->k;
			//translate the coordinate system
			$this->Transform($tm);
		}
		
		/**
		* Rotate object.
		* @param float $angle angle in degrees for counter-clockwise rotation
		* @param int $x abscissa of the rotation center. Default is current x position
		* @param int $y ordinate of the rotation center. Default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Rotate($angle, $x='', $y=''){
			if ($x === '') {
				$x=$this->x;
			}
			if ($y === '') {
				$y=$this->y;
			}
			if ($this->rtl) {
				$x = $this->w - $x;
				$angle = -$angle;
			}
			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;
			//calculate elements of transformation matrix
			$tm[0] = cos(deg2rad($angle));
			$tm[1] = sin(deg2rad($angle));
			$tm[2] = -$tm[1];
			$tm[3] = $tm[0];
			$tm[4] = $x + $tm[1] * $y - $tm[0] * $x;
			$tm[5] = $y - $tm[0] * $y - $tm[1] * $x;
			//rotate the coordinate system around ($x,$y)
			$this->Transform($tm);
		}
		
		/**
		* Skew horizontally.
		* @param float $angle_x angle in degrees between -90 (skew to the left) and 90 (skew to the right)
		* @param int $x abscissa of the skewing center. default is current x position
		* @param int $y ordinate of the skewing center. default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function SkewX($angle_x, $x='', $y=''){
			$this->Skew($angle_x, 0, $x, $y);
		}
		
		/**
		* Skew vertically.
		* @param float $angle_y angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
		* @param int $x abscissa of the skewing center. default is current x position
		* @param int $y ordinate of the skewing center. default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function SkewY($angle_y, $x='', $y=''){
			$this->Skew(0, $angle_y, $x, $y);
		}
		
		/**
		* Skew.
		* @param float $angle_x angle in degrees between -90 (skew to the left) and 90 (skew to the right)
		* @param float $angle_y angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
		* @param int $x abscissa of the skewing center. default is current x position
		* @param int $y ordinate of the skewing center. default is current y position
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Skew($angle_x, $angle_y, $x='', $y=''){
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($this->rtl) {
				$x = $this->w - $x;
				$angle_x = -$angle_x;
			}
			if (($angle_x <= -90) OR ($angle_x >= 90) OR ($angle_y <= -90) OR ($angle_y >= 90)) {
				$this->Error('Please use values between -90 and +90 degrees for Skewing.');
			}
			$x *= $this->k;
			$y = ($this->h - $y) * $this->k;
			//calculate elements of transformation matrix
			$tm[0] = 1;
			$tm[1] = tan(deg2rad($angle_y));
			$tm[2] = tan(deg2rad($angle_x));
			$tm[3] = 1;
			$tm[4] = -$tm[2] * $y;
			$tm[5] = -$tm[1] * $x;
			//skew the coordinate system
			$this->Transform($tm);
		}
		
		/**
		* Apply graphic transformations.
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		protected function Transform($tm){
			$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $tm[0], $tm[1], $tm[2], $tm[3], $tm[4], $tm[5]));
		}
		
		// END TRANSFORMATIONS SECTION -------------------------
		
		
		// START GRAPHIC FUNCTIONS SECTION ---------------------
		// The following section is based on the code provided by David Hernandez Sanz
		
		/**
		* Defines the line width. By default, the value equals 0.2 mm. The method can be called before the first page is created and the value is retained from page to page.
		* @param float $width The width.
		* @since 1.0
		* @see Line(), Rect(), Cell(), MultiCell()
		*/
		public function SetLineWidth($width) {
			//Set line width
			$this->LineWidth = $width;
			$this->linestyleWidth = sprintf('%.2f w', ($width * $this->k));
			$this->_out($this->linestyleWidth);
		}
		
		/**
		* Returns the current the line width.
		* @return int Line width 
		* @since 2.1.000 (2008-01-07)
		* @see Line(), SetLineWidth()
		*/
		public function GetLineWidth() {
			return $this->LineWidth;
		}
		
		/**
		* Set line style.
		* @param array $style Line style. Array with keys among the following:
		* <ul>
		*	 <li>width (float): Width of the line in user units.</li>
		*	 <li>cap (string): Type of cap to put on the line. Possible values are:
		* butt, round, square. The difference between "square" and "butt" is that
		* "square" projects a flat end past the end of the line.</li>
		*	 <li>join (string): Type of join. Possible values are: miter, round,
		* bevel.</li>
		*	 <li>dash (mixed): Dash pattern. Is 0 (without dash) or string with
		* series of length values, which are the lengths of the on and off dashes.
		* For example: "2" represents 2 on, 2 off, 2 on, 2 off, ...; "2,1" is 2 on,
		* 1 off, 2 on, 1 off, ...</li>
		*	 <li>phase (integer): Modifier on the dash pattern which is used to shift
		* the point at which the pattern starts.</li>
		*	 <li>color (array): Draw color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K).</li>
		* </ul>
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function SetLineStyle($style) {
			extract($style);
			if (isset($width)) {
				$width_prev = $this->LineWidth;
				$this->SetLineWidth($width);
				$this->LineWidth = $width_prev;
			}
			if (isset($cap)) {
				$ca = array("butt" => 0, "round"=> 1, "square" => 2);
				if (isset($ca[$cap])) {
					$this->linestyleCap = $ca[$cap]." J";
					$this->_out($this->linestyleCap);
				}
			}
			if (isset($join)) {
				$ja = array("miter" => 0, "round" => 1, "bevel" => 2);
				if (isset($ja[$join])) {
					$this->linestyleJoin = $ja[$join]." j";
					$this->_out($this->linestyleJoin);
				}
			}
			if (isset($dash)) {
				$dash_string = "";
				if ($dash) {
					if (ereg("^.+,", $dash)) {
						$tab = explode(",", $dash);
					} else {
						$tab = array($dash);
					}
					$dash_string = "";
					foreach ($tab as $i => $v) {
						if ($i) {
							$dash_string .= " ";
						}
						$dash_string .= sprintf("%.2f", $v);
					}
				}
				if (!isset($phase) OR !$dash) {
					$phase = 0;
				}
				$this->linestyleDash = sprintf("[%s] %.2f d", $dash_string, $phase);
				$this->_out($this->linestyleDash);
			}
			if (isset($color)) {
				$this->SetDrawColorArray($color);
			}
		}
		
		/*
		* Set a draw point.
		* @param float $x Abscissa of point.
		* @param float $y Ordinate of point.
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outPoint($x, $y) {
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			$this->_out(sprintf("%.2f %.2f m", $x * $this->k, ($this->h - $y) * $this->k));
		}
		
		/*
		* Draws a line from last draw point.
		* @param float $x Abscissa of end point.
		* @param float $y Ordinate of end point.
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outLine($x, $y) {
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			$this->_out(sprintf("%.2f %.2f l", $x * $this->k, ($this->h - $y) * $this->k));
		}
		
		/**
		* Draws a rectangle.
		* @param float $x Abscissa of upper-left corner (or upper-right corner for RTL language).
		* @param float $y Ordinate of upper-left corner (or upper-right corner for RTL language).
		* @param float $w Width.
		* @param float $h Height.
		* @param string $op options
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outRect($x, $y, $w, $h, $op) {
			if ($this->rtl) {
				$x = $this->w - $x - $w;
			}
			$this->_out(sprintf('%.2f %.2f %.2f %.2f re %s', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k, $op));
		}
		
		/*
		* Draws a Bezier curve from last draw point.
		* The Bezier curve is a tangent to the line between the control points at either end of the curve.
		* @param float $x1 Abscissa of control point 1.
		* @param float $y1 Ordinate of control point 1.
		* @param float $x2 Abscissa of control point 2.
		* @param float $y2 Ordinate of control point 2.
		* @param float $x3 Abscissa of end point.
		* @param float $y3 Ordinate of end point.
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outCurve($x1, $y1, $x2, $y2, $x3, $y3) {
			if ($this->rtl) {
				$x1 = $this->w - $x1;
				$x2 = $this->w - $x2;
				$x3 = $this->w - $x3;
			}
			$this->_out(sprintf("%.2f %.2f %.2f %.2f %.2f %.2f c", $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
		}
		
		/**
		* Draws a line between two points.
		* @param float $x1 Abscissa of first point.
		* @param float $y1 Ordinate of first point.
		* @param float $x2 Abscissa of second point.
		* @param float $y2 Ordinate of second point.
		* @param array $style Line style. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @access public
		* @since 1.0
		* @see SetLineWidth(), SetDrawColor(), SetLineStyle()
		*/
		public function Line($x1, $y1, $x2, $y2, $style=array()) {
			if ($style) {
				$this->SetLineStyle($style);
			}
			$this->_outPoint($x1, $y1);
			$this->_outLine($x2, $y2);
			$this->_out(" S");
		}
		
		/**
		* Draws a rectangle.
		* @param float $x Abscissa of upper-left corner (or upper-right corner for RTL language).
		* @param float $y Ordinate of upper-left corner (or upper-right corner for RTL language).
		* @param float $w Width.
		* @param float $h Height.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $border_style Border style of rectangle. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all borders. Array like for {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>L, T, R, B or combinations: Line style of left, top, right or bottom border. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the border. Default value: default line style (empty array).
		* @param array $border_style Border style of rectangle. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @since 1.0
		* @see SetLineStyle()
		*/
		public function Rect($x, $y, $w, $h, $style='', $border_style=array(), $fill_color=array()) {
			if (!(false === strpos($style, "F")) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case "F": {
					$op = 'f';
					$border_style = array();
					$this->_outRect($x, $y, $w, $h, $op);
					break;
				}
				case "DF":
				case "FD": {
					if ((!$border_style) OR (isset($border_style["all"]))) {
						$op = 'B';
						if (isset($border_style["all"])) {
							$this->SetLineStyle($border_style["all"]);
							$border_style = array();
						}
					} else {
						$op = 'f';
					}
					$this->_outRect($x, $y, $w, $h, $op);
					break;
				}
				default: {
					$op = 'S';
					if ((!$border_style) OR (isset($border_style["all"]))) {
						if (isset($border_style["all"]) AND $border_style["all"]) {
							$this->SetLineStyle($border_style["all"]);
							$border_style = array();
						}
						$this->_outRect($x, $y, $w, $h, $op);
					}
					break;
				}
			}
			if ($border_style) {
				$border_style2 = array();
				foreach ($border_style as $line => $value) {
					$lenght = strlen($line);
					for ($i = 0; $i < $lenght; $i++) {
						$border_style2[$line[$i]] = $value;
					}
				}
				$border_style = $border_style2;
				if (isset($border_style["L"]) AND $border_style["L"]) {
					$this->Line($x, $y, $x, $y + $h, $border_style["L"]);
				}
				if (isset($border_style["T"]) AND $border_style["T"]) {
					$this->Line($x, $y, $x + $w, $y, $border_style["T"]);
				}
				if (isset($border_style["R"]) AND $border_style["R"]) {
					$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style["R"]);
				}
				if (isset($border_style["B"]) AND $border_style["B"]) {
					$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style["B"]);
				}
			}
		}
		
		
		/**
		* Draws a Bezier curve.
		* The Bezier curve is a tangent to the line between the control points at
		* either end of the curve.
		* @param float $x0 Abscissa of start point.
		* @param float $y0 Ordinate of start point.
		* @param float $x1 Abscissa of control point 1.
		* @param float $y1 Ordinate of control point 1.
		* @param float $x2 Abscissa of control point 2.
		* @param float $y2 Ordinate of control point 2.
		* @param float $x3 Abscissa of end point.
		* @param float $y3 Ordinate of end point.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of curve. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @see SetLineStyle()
		* @since 2.1.000 (2008-01-08)
		*/
		public function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style="", $line_style=array(), $fill_color=array()) {
			if (!(false === strpos($style, "F")) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case "F": {
					$op = "f";
					$line_style = array();
					break;
				}
				case "FD": 
				case "DF": {
					$op = "B";
					break;
				}
				case "CNZ": {
					$op = "W n";
					break;
				}
				case "CEO": {
					$op = "W* n";
					break;
				}
				default: {
					$op = "S";
					break;
				}
			}
			if ($line_style) {
				$this->SetLineStyle($line_style);
			}
			$this->_outPoint($x0, $y0);
			$this->_outCurve($x1, $y1, $x2, $y2, $x3, $y3);
			$this->_out($op);
		}
		
		/**
		* Draws a poly-Bezier curve.
		* Each Bezier curve segment is a tangent to the line between the control points at
		* either end of the curve.
		* @param float $x0 Abscissa of start point.
		* @param float $y0 Ordinate of start point.
		* @param float $segments An array of bezier descriptions. Format: array(x1, y1, x2, y2, x3, y3).
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of curve. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @see SetLineStyle()
		* @since 3.0008 (2008-05-12)
		*/
		public function Polycurve($x0, $y0, $segments, $style="", $line_style=array(), $fill_color=array()) {
			if (!(false === strpos($style, "F")) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case "F": {
					$op = "f";
					$line_style = array();
					break;
				}
				case "FD":
				case "DF": {
					$op = "B";
					break;
				}
				case "CNZ": {
					$op = "W n";
					break;
				}
				case "CEO": {
					$op = "W* n";
					break;
				}
				default: {
					$op = "S";
					break;
				}
			}
			if ($line_style) {
				$this->SetLineStyle($line_style);
			}
			$this->_outPoint($x0, $y0);
			foreach ($segments as $segment) {
				list($x1, $y1, $x2, $y2, $x3, $y3) = $segment;
				$this->_outCurve($x1, $y1, $x2, $y2, $x3, $y3);
			}	
			$this->_out($op);
		}
		
		/**
		* Draws an ellipse.
		* An ellipse is formed from n Bezier curves.
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $rx Horizontal radius.
		* @param float $ry Vertical radius (if ry = 0 then is a circle, see {@link Circle Circle}). Default value: 0.
		* @param float $angle: Angle oriented (anti-clockwise). Default value: 0.
		* @param float $astart: Angle start of draw line. Default value: 0.
		* @param float $afinish: Angle finish of draw line. Default value: 360.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>C: Draw close.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of ellipse. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @param integer $nc Number of curves used in ellipse. Default value: 8.
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function Ellipse($x0, $y0, $rx, $ry=0, $angle=0, $astart=0, $afinish=360, $style="", $line_style=array(), $fill_color=array(), $nc=8) {
			if ($angle) {
				$this->StartTransform();
				$this->Rotate($angle, $x0, $y0);
				$this->Ellipse($x0, $y0, $rx, $ry, 0, $astart, $afinish, $style, $line_style, $fill_color, $nc);
				$this->StopTransform();
				return;
			}
			if ($rx) {
				if (!(false === strpos($style, "F")) AND isset($fill_color)) {
					$this->SetFillColorArray($fill_color);
				}
				switch ($style) {
					case "F": {
						$op = "f";
						$line_style = array();
						break;
					}
					case "FD": 
					case "DF": {
						$op = "B";
						break;
					}
					case "C": {
						$op = "s"; // Small "s" signifies closing the path as well
						break;
					}
					case "CNZ": {
						$op = "W n";
						break;
					}
					case "CEO": {
						$op = "W* n";
						break;
					}
					default: {
						$op = "S";
						break;
					}
				}
				if ($line_style) {
					$this->SetLineStyle($line_style);
				}
				if (!$ry) {
					$ry = $rx;
				}
				$rx *= $this->k;
				$ry *= $this->k;
				if ($nc < 2){
					$nc = 2;
				}
				$astart = deg2rad((float) $astart);
				$afinish = deg2rad((float) $afinish);
				$total_angle = $afinish - $astart;
				$dt = $total_angle / $nc;
				$dtm = $dt / 3;
				$x0 *= $this->k;
				$y0 = ($this->h - $y0) * $this->k;
				$t1 = $astart;
				$a0 = $x0 + ($rx * cos($t1));
				$b0 = $y0 + ($ry * sin($t1));
				$c0 = -$rx * sin($t1);
				$d0 = $ry * cos($t1);
				$this->_outPoint($a0 / $this->k, $this->h - ($b0 / $this->k));
				for ($i = 1; $i <= $nc; $i++) {
					// Draw this bit of the total curve
					$t1 = ($i * $dt) + $astart;
					$a1 = $x0 + ($rx * cos($t1));
					$b1 = $y0 + ($ry * sin($t1));
					$c1 = -$rx * sin($t1);
					$d1 = $ry * cos($t1);
					$this->_outCurve(($a0 + ($c0 * $dtm)) / $this->k, $this->h - (($b0 + ($d0 * $dtm)) / $this->k), ($a1 - ($c1 * $dtm)) / $this->k, $this->h - (($b1 - ($d1 * $dtm)) / $this->k), $a1 / $this->k, $this->h - ($b1 / $this->k));
					$a0 = $a1;
					$b0 = $b1;
					$c0 = $c1;
					$d0 = $d1;
				}
				$this->_out($op);
			}
		}
		
		/**
		* Draws a circle.
		* A circle is formed from n Bezier curves.
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $r Radius.
		* @param float $astart: Angle start of draw line. Default value: 0.
		* @param float $afinish: Angle finish of draw line. Default value: 360.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>C: Draw close.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of circle. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(red, green, blue). Default value: default color (empty array).
		* @param integer $nc Number of curves used in circle. Default value: 8.
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function Circle($x0, $y0, $r, $astart=0, $afinish=360, $style="", $line_style=array(), $fill_color=array(), $nc=8) {
			$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nc);
		}
		
		/**
		* Draws a polygon.
		* @param array $p Points 0 to ($np - 1). Array with values (x0, y0, x1, y1,..., x(np-1), y(np - 1))
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of polygon. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all lines. Array like for {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>0 to ($np - 1): Line style of each line. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the line. Default value is default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function Polygon($p, $style="", $line_style=array(), $fill_color=array()) {
			$np = count($p) / 2;
			if (!(false === strpos($style, "F")) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case "F": {
					$line_style = array();
					$op = "f";
					break;
				}
				case "FD": 
				case "DF": {
					$op = "B";
					break;
				}
				case "CNZ": {
					$op = "W n";
					break;
				}
				case "CEO": {
					$op = "W* n";
					break;
				}				
				default: {
					$op = "S";
					break;
				}
			}
			$draw = true;
			if ($line_style) {
				if (isset($line_style["all"])) {
					$this->SetLineStyle($line_style["all"]);
				} else { // 0 .. (np - 1), op = {B, S}
					$draw = false;
					if ("B" == $op) {
						$op = "f";
						$this->_outPoint($p[0], $p[1]);
						for ($i = 2; $i < ($np * 2); $i = $i + 2) {
							$this->_outLine($p[$i], $p[$i + 1]);
						}
						$this->_outLine($p[0], $p[1]);
						$this->_out($op);
					}
					$p[($np * 2)] = $p[0];
					$p[(($np * 2) + 1)] = $p[1];
					for ($i = 0; $i < $np; $i++) {
						if (isset($line_style[$i]) AND ($line_style[$i] != 0)) {
							$this->Line($p[($i * 2)], $p[(($i * 2) + 1)], $p[(($i * 2) + 2)], $p[(($i * 2) + 3)], $line_style[$i]);
						}
					}
				}
			}
			if ($draw) {
				$this->_outPoint($p[0], $p[1]);
				for ($i = 2; $i < ($np * 2); $i = $i + 2) {
					$this->_outLine($p[$i], $p[$i + 1]);
				}
				$this->_outLine($p[0], $p[1]);
				$this->_out($op);
			}
		}
		
		/**
		* Draws a regular polygon.
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $r: Radius of inscribed circle.
		* @param integer $ns Number of sides.
		* @param float $angle Angle oriented (anti-clockwise). Default value: 0.
		* @param boolean $draw_circle Draw inscribed circle or not. Default value: false.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of polygon sides. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all sides. Array like for {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>0 to ($ns - 1): Line style of each side. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the side. Default value is default line style (empty array).
		* @param array $fill_color Fill color. Format: array(red, green, blue). Default value: default color (empty array).
		* @param string $circle_style Style of rendering of inscribed circle (if draws). Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $circle_outLine_style Line style of inscribed circle (if draws). Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $circle_fill_color Fill color of inscribed circle (if draws). Format: array(red, green, blue). Default value: default color (empty array).
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function RegularPolygon($x0, $y0, $r, $ns, $angle=0, $draw_circle=false, $style="", $line_style=array(), $fill_color=array(), $circle_style="", $circle_outLine_style=array(), $circle_fill_color=array()) {
			if (3 > $ns) {
				$ns = 3;
			}
			if ($draw_circle) {
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
			}
			$p = array();
			for ($i = 0; $i < $ns; $i++) {
				$a = $angle + ($i * 360 / $ns);
				$a_rad = deg2rad((float) $a);
				$p[] = $x0 + ($r * sin($a_rad));
				$p[] = $y0 + ($r * cos($a_rad));
			}
			$this->Polygon($p, $style, $line_style, $fill_color);
		}
		
		/**
		* Draws a star polygon
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $r Radius of inscribed circle.
		* @param integer $nv Number of vertices.
		* @param integer $ng Number of gap (if ($ng % $nv = 1) then is a regular polygon).
		* @param float $angle: Angle oriented (anti-clockwise). Default value: 0.
		* @param boolean $draw_circle: Draw inscribed circle or not. Default value is false.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of polygon sides. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all sides. Array like for
		* {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>0 to (n - 1): Line style of each side. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the side. Default value is default line style (empty array).
		* @param array $fill_color Fill color. Format: array(red, green, blue). Default value: default color (empty array).
		* @param string $circle_style Style of rendering of inscribed circle (if draws). Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $circle_outLine_style Line style of inscribed circle (if draws). Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $circle_fill_color Fill color of inscribed circle (if draws). Format: array(red, green, blue). Default value: default color (empty array).
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function StarPolygon($x0, $y0, $r, $nv, $ng, $angle=0, $draw_circle=false, $style="", $line_style=array(), $fill_color=array(), $circle_style="", $circle_outLine_style=array(), $circle_fill_color=array()) {
			if (2 > $nv) {
				$nv = 2;
			}
			if ($draw_circle) {
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
			}
			$p2 = array();
			$visited = array();
			for ($i = 0; $i < $nv; $i++) {
				$a = $angle + ($i * 360 / $nv);
				$a_rad = deg2rad((float) $a);
				$p2[] = $x0 + ($r * sin($a_rad));
				$p2[] = $y0 + ($r * cos($a_rad));
				$visited[] = false;
			}
			$p = array();
			$i = 0;
			do {
				$p[] = $p2[$i * 2];
				$p[] = $p2[($i * 2) + 1];
				$visited[$i] = true;
				$i += $ng;
				$i %= $nv;
			} while (!$visited[$i]);
			$this->Polygon($p, $style, $line_style, $fill_color);
		}
		
		/**
		* Draws a rounded rectangle.
		* @param float $x Abscissa of upper-left corner.
		* @param float $y Ordinate of upper-left corner.
		* @param float $w Width.
		* @param float $h Height.
		* @param float $r Radius of the rounded corners.
		* @param string $round_corner Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left. Default value: all rounded corner ("1111").
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $border_style Border style of rectangle. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function RoundedRect($x, $y, $w, $h, $r, $round_corner="1111", $style="", $border_style=array(), $fill_color=array()) {
			if ("0000" == $round_corner) { // Not rounded
				$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
			} else { // Rounded
				if (!(false === strpos($style, "F")) AND isset($fill_color)) {
					$this->SetFillColorArray($fill_color);
				}
				switch ($style) {
					case "F": {
						$border_style = array();
						$op = "f";
						break;
					}
					case "FD": 
					case "DF": {
						$op = "B";
						break;
					}
					case "CNZ": {
						$op = "W n";
						break;
					}
					case "CEO": {
						$op = "W* n";
						break;
					}
					default: {
						$op = "S";
						break;
					}
				}
				if ($border_style) {
					$this->SetLineStyle($border_style);
				}
				$MyArc = 4 / 3 * (sqrt(2) - 1);
				$this->_outPoint($x + $r, $y);
				$xc = $x + $w - $r;
				$yc = $y + $r;
				$this->_outLine($xc, $y);
				if ($round_corner[0]) {
					$this->_outCurve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
				} else {
					$this->_outLine($x + $w, $y);
				}
				$xc = $x + $w - $r;
				$yc = $y + $h - $r;
				$this->_outLine($x + $w, $yc);
				if ($round_corner[1]) {
					$this->_outCurve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
				} else {
					$this->_outLine($x + $w, $y + $h);
				}
				$xc = $x + $r;
				$yc = $y + $h - $r;
				$this->_outLine($xc, $y + $h);
				if ($round_corner[2]) {
					$this->_outCurve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
				} else {
					$this->_outLine($x, $y + $h);
				}
				$xc = $x + $r;
				$yc = $y + $r;
				$this->_outLine($x, $yc);
				if ($round_corner[3]) {
					$this->_outCurve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
				} else {
					$this->_outLine($x, $y);
					$this->_outLine($x + $r, $y);
				}
				$this->_out($op);
			}
		}
		
		// END GRAPHIC FUNCTIONS SECTION -----------------------
		
		// BIDIRECTIONAL TEXT SECTION --------------------------
		/**
		 * Reverse the RLT substrings using the Bidirectional Algorithm (http://unicode.org/reports/tr9/).
		 * @param string $str string to manipulate.
		 * @param bool $forcertl if 'R' forces RTL, if 'L' forces LTR
		 * @return string
		 * @author Nicola Asuni
		 * @since 2.1.000 (2008-01-08)
		*/
		protected function utf8StrRev($str, $setbom=false, $forcertl=false) {
			return $this->arrUTF8ToUTF16BE($this->utf8Bidi($this->UTF8StringToArray($str), $forcertl), $setbom);
		}
		
		/**
		 * Reverse the RLT substrings using the Bidirectional Algorithm (http://unicode.org/reports/tr9/).
		 * @param array $ta array of characters composing the string.
		 * @param bool $forcertl if 'R' forces RTL, if 'L' forces LTR
		 * @return string
		 * @author Nicola Asuni
		 * @since 2.4.000 (2008-03-06)
		*/
		protected function utf8Bidi($ta, $forcertl=false) {
			global $unicode, $unicode_mirror, $unicode_arlet, $laa_array, $diacritics;
			// paragraph embedding level
			$pel = 0;
			// max level
			$maxlevel = 0;
			// create string from array
			$str = $this->UTF8ArrSubString($ta);
			// check if string contains arabic text
			if (preg_match(K_RE_PATTERN_ARABIC, $str)) {
				$arabic = true;
			} else {
				$arabic = false;
			}
			// check if string contains RTL text
			if (!($forcertl OR $arabic OR preg_match(K_RE_PATTERN_RTL, $str))) {
				return $ta;
			}
			
			// get number of chars
			$numchars = count($ta);
			
			if ($forcertl == 'R') {
					$pel = 1;
			} elseif ($forcertl == 'L') {
					$pel = 0;
			} else {
				// P2. In each paragraph, find the first character of type L, AL, or R.
				// P3. If a character is found in P2 and it is of type AL or R, then set the paragraph embedding level to one; otherwise, set it to zero.
				for ($i=0; $i < $numchars; $i++) {
					$type = $unicode[$ta[$i]];
					if ($type == 'L') {
						$pel = 0;
						break;
					} elseif (($type == 'AL') OR ($type == 'R')) {
						$pel = 1;
						break;
					}
				}
			}
			
			// Current Embedding Level
			$cel = $pel;
			// directional override status
			$dos = 'N';
			$remember = array();
			// start-of-level-run
			$sor = $pel % 2 ? 'R' : 'L';
			$eor = $sor;
			
			//$levels = array(array('level' => $cel, 'sor' => $sor, 'eor' => '', 'chars' => array()));
			//$current_level = &$levels[count( $levels )-1];
			
			// Array of characters data
			$chardata = Array();
			
			// X1. Begin by setting the current embedding level to the paragraph embedding level. Set the directional override status to neutral. Process each character iteratively, applying rules X2 through X9. Only embedding levels from 0 to 61 are valid in this phase.
			// 	In the resolution of levels in rules I1 and I2, the maximum embedding level of 62 can be reached.
			for ($i=0; $i < $numchars; $i++) {
				if ($ta[$i] == K_RLE) {
					// X2. With each RLE, compute the least greater odd embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to neutral.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + ($cel % 2) + 1;
					if ($next_level < 62) {
						$remember[] = array('num' => K_RLE, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'N';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_LRE) {
					// X3. With each LRE, compute the least greater even embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to neutral.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + 2 - ($cel % 2);
					if ( $next_level < 62 ) {
						$remember[] = array('num' => K_LRE, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'N';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_RLO) {
					// X4. With each RLO, compute the least greater odd embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to right-to-left.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + ($cel % 2) + 1;
					if ($next_level < 62) {
						$remember[] = array('num' => K_RLO, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'R';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_LRO) {
					// X5. With each LRO, compute the least greater even embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to left-to-right.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + 2 - ($cel % 2);
					if ( $next_level < 62 ) {
						$remember[] = array('num' => K_LRO, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'L';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_PDF) {
					// X7. With each PDF, determine the matching embedding or override code. If there was a valid matching code, restore (pop) the last remembered (pushed) embedding level and directional override.
					if (count($remember)) {
						$last = count($remember ) - 1;
						if (($remember[$last]['num'] == K_RLE) OR 
							  ($remember[$last]['num'] == K_LRE) OR 
							  ($remember[$last]['num'] == K_RLO) OR 
							  ($remember[$last]['num'] == K_LRO)) {
							$match = array_pop($remember);
							$cel = $match['cel'];
							$dos = $match['dos'];
							$sor = $eor;
							$eor = ($cel > $match['cel'] ? $cel : $match['cel']) % 2 ? 'R' : 'L';
						}
					}
				} elseif (($ta[$i] != K_RLE) AND
								 ($ta[$i] != K_LRE) AND
								 ($ta[$i] != K_RLO) AND
								 ($ta[$i] != K_LRO) AND
								 ($ta[$i] != K_PDF)) {
					// X6. For all types besides RLE, LRE, RLO, LRO, and PDF:
					//	a. Set the level of the current character to the current embedding level.
					//	b. Whenever the directional override status is not neutral, reset the current character type to the directional override status.
					if ($dos != 'N') {
						$chardir = $dos;
					} else {
						$chardir = $unicode[$ta[$i]];
					}
					// stores string characters and other information
					$chardata[] = array('char' => $ta[$i], 'level' => $cel, 'type' => $chardir, 'sor' => $sor, 'eor' => $eor);
				}
			} // end for each char
			
			// X8. All explicit directional embeddings and overrides are completely terminated at the end of each paragraph. Paragraph separators are not included in the embedding.
			// X9. Remove all RLE, LRE, RLO, LRO, PDF, and BN codes.
			// X10. The remaining rules are applied to each run of characters at the same level. For each run, determine the start-of-level-run (sor) and end-of-level-run (eor) type, either L or R. This depends on the higher of the two levels on either side of the boundary (at the start or end of the paragraph, the level of the “other” run is the base embedding level). If the higher level is odd, the type is R; otherwise, it is L.
			
			// 3.3.3 Resolving Weak Types
			// Weak types are now resolved one level run at a time. At level run boundaries where the type of the character on the other side of the boundary is required, the type assigned to sor or eor is used.
			// Nonspacing marks are now resolved based on the previous characters.
			$numchars = count($chardata);
			
			// W1. Examine each nonspacing mark (NSM) in the level run, and change the type of the NSM to the type of the previous character. If the NSM is at the start of the level run, it will get the type of sor.
			$prevlevel = -1; // track level changes
			$levcount = 0; // counts consecutive chars at the same level
			for ($i=0; $i < $numchars; $i++) {
				if ($chardata[$i]['type'] == 'NSM') {
					if ($levcount) {
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					} elseif ($i > 0) {
						$chardata[$i]['type'] = $chardata[($i-1)]['type'];
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					$levcount++;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W2. Search backward from each instance of a European number until the first strong type (R, L, AL, or sor) is found. If an AL is found, change the type of the European number to Arabic number.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; $i++) {
				if ($chardata[$i]['char'] == 'EN') {
					for ($j=$levcount; $j >= 0; $j--) {
						if ($chardata[$j]['type'] == 'AL') {
							$chardata[$i]['type'] = 'AN';
						} elseif (($chardata[$j]['type'] == 'L') OR ($chardata[$j]['type'] == 'R')) {
							break;
						}
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					$levcount++;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W3. Change all ALs to R.
			for ($i=0; $i < $numchars; $i++) {
				if ($chardata[$i]['type'] == 'AL') {
					$chardata[$i]['type'] = 'R';
				} 
			}
			
			// W4. A single European separator between two European numbers changes to a European number. A single common separator between two numbers of the same type changes to that type.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; $i++) {
				if (($levcount > 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
					if (($chardata[$i]['type'] == 'ES') AND ($chardata[($i-1)]['type'] == 'EN') AND ($chardata[($i+1)]['type'] == 'EN')) {
						$chardata[$i]['type'] = 'EN';
					} elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i-1)]['type'] == 'EN') AND ($chardata[($i+1)]['type'] == 'EN')) {
						$chardata[$i]['type'] = 'EN';
					} elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i-1)]['type'] == 'AN') AND ($chardata[($i+1)]['type'] == 'AN')) {
						$chardata[$i]['type'] = 'AN';
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					$levcount++;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W5. A sequence of European terminators adjacent to European numbers changes to all European numbers.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; $i++) {
				if ($chardata[$i]['type'] == 'ET') {
					if (($levcount > 0) AND ($chardata[($i-1)]['type'] == 'EN')) {
						$chardata[$i]['type'] = 'EN';
					} else {
						$j = $i+1;
						while (($j < $numchars) AND ($chardata[$j]['level'] == $prevlevel)) {
							if ($chardata[$j]['type'] == 'EN') {
								$chardata[$i]['type'] = 'EN';
								break;
							} elseif ($chardata[$j]['type'] != 'ET') {
								break;
							}
							$j++;
						}
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					$levcount++;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W6. Otherwise, separators and terminators change to Other Neutral.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; $i++) {
				if (($chardata[$i]['type'] == 'ET') OR ($chardata[$i]['type'] == 'ES') OR ($chardata[$i]['type'] == 'CS')) {
					$chardata[$i]['type'] = 'ON';
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					$levcount++;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			//W7. Search backward from each instance of a European number until the first strong type (R, L, or sor) is found. If an L is found, then change the type of the European number to L.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; $i++) {
				if ($chardata[$i]['char'] == 'EN') {
					for ($j=$levcount; $j >= 0; $j--) {
						if ($chardata[$j]['type'] == 'L') {
							$chardata[$i]['type'] = 'L';
						} elseif ($chardata[$j]['type'] == 'R') {
							break;
						}
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					$levcount++;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// N1. A sequence of neutrals takes the direction of the surrounding strong text if the text on both sides has the same direction. European and Arabic numbers act as if they were R in terms of their influence on neutrals. Start-of-level-run (sor) and end-of-level-run (eor) are used at level run boundaries.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; $i++) {
				if (($levcount > 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
					if (($chardata[$i]['type'] == 'N') AND ($chardata[($i-1)]['type'] == 'L') AND ($chardata[($i+1)]['type'] == 'L')) {
						$chardata[$i]['type'] = 'L';
					} elseif (($chardata[$i]['type'] == 'N') AND
					 (($chardata[($i-1)]['type'] == 'R') OR ($chardata[($i-1)]['type'] == 'EN') OR ($chardata[($i-1)]['type'] == 'AN')) AND
					 (($chardata[($i+1)]['type'] == 'R') OR ($chardata[($i+1)]['type'] == 'EN') OR ($chardata[($i+1)]['type'] == 'AN'))) {
						$chardata[$i]['type'] = 'R';
					} elseif ($chardata[$i]['type'] == 'N') {
						// N2. Any remaining neutrals take the embedding direction
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					}
				} elseif (($levcount == 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
					// first char
					if (($chardata[$i]['type'] == 'N') AND ($chardata[$i]['sor'] == 'L') AND ($chardata[($i+1)]['type'] == 'L')) {
						$chardata[$i]['type'] = 'L';
					} elseif (($chardata[$i]['type'] == 'N') AND
					 (($chardata[$i]['sor'] == 'R') OR ($chardata[$i]['sor'] == 'EN') OR ($chardata[$i]['sor'] == 'AN')) AND
					 (($chardata[($i+1)]['type'] == 'R') OR ($chardata[($i+1)]['type'] == 'EN') OR ($chardata[($i+1)]['type'] == 'AN'))) {
						$chardata[$i]['type'] = 'R';
					} elseif ($chardata[$i]['type'] == 'N') {
						// N2. Any remaining neutrals take the embedding direction
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					}
				} elseif (($levcount > 0) AND ((($i+1) == $numchars) OR (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] != $prevlevel))) {
					//last char
					if (($chardata[$i]['type'] == 'N') AND ($chardata[($i-1)]['type'] == 'L') AND ($chardata[$i]['eor'] == 'L')) {
						$chardata[$i]['type'] = 'L';
					} elseif (($chardata[$i]['type'] == 'N') AND
					 (($chardata[($i-1)]['type'] == 'R') OR ($chardata[($i-1)]['type'] == 'EN') OR ($chardata[($i-1)]['type'] == 'AN')) AND
					 (($chardata[$i]['eor'] == 'R') OR ($chardata[$i]['eor'] == 'EN') OR ($chardata[$i]['eor'] == 'AN'))) {
						$chardata[$i]['type'] = 'R';
					} elseif ($chardata[$i]['type'] == 'N') {
						// N2. Any remaining neutrals take the embedding direction
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					}
				} elseif ($chardata[$i]['type'] == 'N') {
					// N2. Any remaining neutrals take the embedding direction
					$chardata[$i]['type'] = $chardata[$i]['sor'];
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					$levcount++;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// I1. For all characters with an even (left-to-right) embedding direction, those of type R go up one level and those of type AN or EN go up two levels.
			// I2. For all characters with an odd (right-to-left) embedding direction, those of type L, EN or AN go up one level.
			for ($i=0; $i < $numchars; $i++) {
				$odd = $chardata[$i]['level'] % 2;
				if ($odd) {
					if (($chardata[$i]['type'] == 'L') OR ($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')){
						$chardata[$i]['level'] += 1;
					}
				} else {
					if ($chardata[$i]['type'] == 'R') {
						$chardata[$i]['level'] += 1;
					} elseif (($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')){
						$chardata[$i]['level'] += 2;
					}
				}
				$maxlevel = max($chardata[$i]['level'],$maxlevel);
			}
			
			// L1. On each line, reset the embedding level of the following characters to the paragraph embedding level:
			//	1. Segment separators,
			//	2. Paragraph separators,
			//	3. Any sequence of whitespace characters preceding a segment separator or paragraph separator, and
			//	4. Any sequence of white space characters at the end of the line.
			for ($i=0; $i < $numchars; $i++) {
				if (($chardata[$i]['type'] == 'B') OR ($chardata[$i]['type'] == 'S')) {
					$chardata[$i]['level'] = $pel;
				} elseif ($chardata[$i]['type'] == 'WS') {
					$j = $i+1;
					while ($j < $numchars) {
						if ((($chardata[$j]['type'] == 'B') OR ($chardata[$j]['type'] == 'S')) OR
							(($j == ($numchars-1)) AND ($chardata[$j]['type'] == 'WS'))) {
							$chardata[$i]['level'] = $pel;
							break;
						} elseif ($chardata[$j]['type'] != 'WS') {
							break;
						}
						$j++;
					}
				}
			}
			
			// Arabic Shaping
			// Cursively connected scripts, such as Arabic or Syriac, require the selection of positional character shapes that depend on adjacent characters. Shaping is logically applied after the Bidirectional Algorithm is used and is limited to characters within the same directional run. 
			if ($arabic) {
				$endedletter = array(1569,1570,1571,1572,1573,1575,1577,1583,1584,1585,1586,1608,1688);
				$alfletter = array(1570,1571,1573,1575);
				$chardata2 = $chardata;
				$laaletter = false;
				$charAL = array();
				$x = 0;
				for ($i=0; $i < $numchars; $i++) {
					if (($unicode[$chardata[$i]['char']] == 'AL') OR ($chardata[$i]['char'] == 32) OR ($chardata[$i]['char'] == 8204)) {
						$charAL[$x] = $chardata[$i];
						$charAL[$x]['i'] = $i;
						$chardata[$i]['x'] = $x;
						$x++;
					}
				}
				$numAL = $x;
				for ($i=0; $i < $numchars; $i++) {
					$thischar = $chardata[$i];
					if ($i > 0) {
						$prevchar = $chardata[($i-1)];
					} else {
						$prevchar = false;
					}
					if (($i+1) < $numchars) {
						$nextchar = $chardata[($i+1)];
					} else {
						$nextchar = false;
					}
					if ($unicode[$thischar['char']] == 'AL') {
						$x = $thischar['x'];
						if ($x > 0) {
							$prevchar = $charAL[($x-1)];
						} else {
							$prevchar = false;
						}
						if (($x+1) < $numAL) {
							$nextchar = $charAL[($x+1)];
						} else {
							$nextchar = false;
						}
						// if laa letter
						if (($prevchar !== false) AND ($prevchar['char'] == 1604) AND (in_array($thischar['char'], $alfletter))) {
							$arabicarr = $laa_array;
							$laaletter = true;
							if ($x > 1) {
								$prevchar = $charAL[($x-2)];
							} else {
								$prevchar = false;
							}
						} else {
							$arabicarr = $unicode_arlet;
							$laaletter = false;
						}
						if (($prevchar !== false) AND ($nextchar !== false) AND
							(($unicode[$prevchar['char']] == 'AL') OR ($unicode[$prevchar['char']] == 'NSM')) AND
							(($unicode[$nextchar['char']] == 'AL') OR ($unicode[$nextchar['char']] == 'NSM')) AND
							($prevchar['type'] == $thischar['type']) AND
							($nextchar['type'] == $thischar['type']) AND
							($nextchar['char'] != 1567)) {
							if (in_array($prevchar['char'], $endedletter)) {
								if (isset($arabicarr[$thischar['char']][2])) {
									// initial
									$chardata2[$i]['char'] = $arabicarr[$thischar['char']][2];
								}
							} else {
								if (isset($arabicarr[$thischar['char']][3])) {
									// medial
									$chardata2[$i]['char'] = $arabicarr[$thischar['char']][3];
								}
							}
						} elseif (($nextchar !== false) AND
							(($unicode[$nextchar['char']] == 'AL') OR ($unicode[$nextchar['char']] == 'NSM')) AND
							($nextchar['type'] == $thischar['type']) AND
							($nextchar['char'] != 1567)) {
							if (isset($arabicarr[$chardata[$i]['char']][2])) {
								// initial
								$chardata2[$i]['char'] = $arabicarr[$thischar['char']][2];
							}
						} elseif ((($prevchar !== false) AND
							(($unicode[$prevchar['char']] == 'AL') OR ($unicode[$prevchar['char']] == 'NSM')) AND
							($prevchar['type'] == $thischar['type'])) OR
							(($nextchar !== false) AND ($nextchar['char'] == 1567))) {
							// final
							if (($i > 1) AND ($thischar['char'] == 1607) AND
								($chardata[$i-1]['char'] == 1604) AND
								($chardata[$i-2]['char'] == 1604)) {
								//Allah Word
								// mark characters to delete with false
								$chardata2[$i-2]['char'] = false;
								$chardata2[$i-1]['char'] = false; 
								$chardata2[$i]['char'] = 65010;
							} else {
								if (($prevchar !== false) AND in_array($prevchar['char'], $endedletter)) {
									if (isset($arabicarr[$thischar['char']][0])) {
										// isolated
										$chardata2[$i]['char'] = $arabicarr[$thischar['char']][0];
									}
								} else {
									if (isset($arabicarr[$thischar['char']][1])) {
										// final
										$chardata2[$i]['char'] = $arabicarr[$thischar['char']][1];
									}
								}
							}
						} elseif (isset($arabicarr[$thischar['char']][0])) {
							// isolated
							$chardata2[$i]['char'] = $arabicarr[$thischar['char']][0];
						}
						// if laa letter
						if ($laaletter) {
							// mark characters to delete with false
							$chardata2[($charAL[($x-1)]['i'])]['char'] = false;
						}
					} // end if AL (Arabic Letter)
				} // end for each char
				/* 
				 * Combining characters that can occur with Shadda (0651 HEX, 1617 DEC) are placed in UE586-UE594. 
				 * Putting the combining mark and shadda in the same glyph allows us to avoid the two marks overlapping each other in an illegible manner.
				 */
				$cw = &$this->CurrentFont['cw'];
				for ($i=0; $i < ($numchars-1); $i++) {
					if (($chardata2[$i]['char'] == 1617) AND (isset($diacritics[($chardata2[$i+1]['char'])]))) {
						// check if the subtitution font is defined on current font
						if (isset($cw[($diacritics[($chardata2[$i+1]['char'])])])) {
							$chardata2[$i]['char'] = false;
							$chardata2[$i+1]['char'] = $diacritics[($chardata2[$i+1]['char'])];
						}
					}
				}
				// remove marked characters
				foreach($chardata2 as $key => $value) {
					if ($value['char'] === false) {
						unset($chardata2[$key]);
					}
				}
				$chardata = array_values($chardata2);
				$numchars = count($chardata);
				unset($chardata2);
				unset($arabicarr);
				unset($laaletter);
				unset($charAL);
			}
			
			// L2. From the highest level found in the text to the lowest odd level on each line, including intermediate levels not actually present in the text, reverse any contiguous sequence of characters that are at that level or higher.
			for ($j=$maxlevel; $j > 0; $j--) {
				$ordarray = Array();
				$revarr = Array();
				$onlevel = false;
				for ($i=0; $i < $numchars; $i++) {
					if ($chardata[$i]['level'] >= $j) {
						$onlevel = true;
						if (isset($unicode_mirror[$chardata[$i]['char']])) {
							// L4. A character is depicted by a mirrored glyph if and only if (a) the resolved directionality of that character is R, and (b) the Bidi_Mirrored property value of that character is true.
							$chardata[$i]['char'] = $unicode_mirror[$chardata[$i]['char']];
						}
						$revarr[] = $chardata[$i];
					} else {
						if ($onlevel) {
							$revarr = array_reverse($revarr);
							$ordarray = array_merge($ordarray, $revarr);
							$revarr = Array();
							$onlevel = false;
						}
						$ordarray[] = $chardata[$i];
					}
				}
				if ($onlevel) {
					$revarr = array_reverse($revarr);
					$ordarray = array_merge($ordarray, $revarr);
				}
				$chardata = $ordarray;
			}
			
			$ordarray = array();
			for ($i=0; $i < $numchars; $i++) {
				$ordarray[] = $chardata[$i]['char'];
			}
			
			return $ordarray;
		}
		
		// END OF BIDIRECTIONAL TEXT SECTION -------------------
		
		/*
		* Adds a bookmark.
		* @param string $txt bookmark description.
		* @param int $level bookmark level.
		* @param float $y Ordinate of the boorkmark position (default = -1 = current position).
		* @access public
		* @author Olivier Plathey, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function Bookmark($txt, $level=0, $y=-1) {
			if ($y == -1) {
				$y = $this->GetY();
			}
			$this->outlines[] = array('t' => $txt, 'l' => $level, 'y' => $y, 'p' => $this->PageNo());
		}
		
		/*
		* Create a bookmark PDF string.
		* @access protected
		* @author Olivier Plathey, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _putbookmarks() {
			$nb = count($this->outlines);
			if ($nb == 0) {
				return;
			}
			$lru = array();
			$level = 0;
			foreach($this->outlines as $i => $o) {
				if ($o['l'] > 0) {
					$parent = $lru[($o['l'] - 1)];
					//Set parent and last pointers
					$this->outlines[$i]['parent'] = $parent;
					$this->outlines[$parent]['last'] = $i;
					if ($o['l'] > $level) {
						//Level increasing: set first pointer
						$this->outlines[$parent]['first'] = $i;
					}
				} else {
					$this->outlines[$i]['parent'] = $nb;
				}
				if (($o['l'] <= $level) AND ($i > 0)) {
					//Set prev and next pointers
					$prev = $lru[$o['l']];
					$this->outlines[$prev]['next'] = $i;
					$this->outlines[$i]['prev'] = $prev;
				}
				$lru[$o['l']] = $i;
				$level = $o['l'];
			}
			//Outline items
			$n = $this->n + 1;
			foreach($this->outlines as $i => $o) {
				$this->_newobj();
				$this->_out('<</Title '.$this->_textstring($o['t']));
				$this->_out('/Parent '.($n+$o['parent']).' 0 R');
				if (isset($o['prev']))
				$this->_out('/Prev '.($n+$o['prev']).' 0 R');
				if (isset($o['next']))
				$this->_out('/Next '.($n+$o['next']).' 0 R');
				if (isset($o['first']))
				$this->_out('/First '.($n+$o['first']).' 0 R');
				if (isset($o['last']))
				$this->_out('/Last '.($n+$o['last']).' 0 R');
				$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]', 1+2*$o['p'], ($this->h-$o['y'])*$this->k));
				$this->_out('/Count 0>>');
				$this->_out('endobj');
			}
			//Outline root
			$this->_newobj();
			$this->OutlineRoot=$this->n;
			$this->_out('<</Type /Outlines /First '.$n.' 0 R');
			$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
			$this->_out('endobj');
		}
		
		
		// --- JAVASCRIPT - FORMS ------------------------------
		
		/*
		* Adds a javascript
		* @access public
		* @author Johannes Güntert, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function IncludeJS($script) {
			$this->javascript .= $script;
		}
		
		/*
		* Create a javascript PDF string.
		* @access protected
		* @author Johannes Güntert, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _putjavascript() {
			if (empty($this->javascript)) {
				return;
			}
			$this->_newobj();
			$this->n_js = $this->n;
			$this->_out('<<');
			$this->_out('/Names [(EmbeddedJS) '.($this->n + 1).' 0 R ]');
			$this->_out('>>');
			$this->_out('endobj');
			$this->_newobj();
			$this->_out('<<');
			$this->_out('/S /JavaScript');
			$this->_out('/JS '.$this->_textstring($this->javascript));
			$this->_out('>>');
			$this->_out('endobj');
		}
		
		/*
		* Convert color to javascript color.
		* @param string $color color name or #RRGGBB
		* @access protected
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _JScolor($color) {
			static $aColors = array('transparent', 'black', 'white', 'red', 'green', 'blue', 'cyan', 'magenta', 'yellow', 'dkGray', 'gray', 'ltGray');
			if (substr($color,0,1) == '#') {
				return sprintf("['RGB',%.3f,%.3f,%.3f]", hexdec(substr($color,1,2))/255, hexdec(substr($color,3,2))/255, hexdec(substr($color,5,2))/255);
			}
			if (!in_array($color,$aColors)) {
				$this->Error('Invalid color: '.$color);
			}
			return 'color.'.$color;
		}
		
		/*
		* Adds a javascript form field.
		* @param string $type field type
		* @param string $name field name
		* @param int $x horizontal position
		* @param int $y vertical position
		* @param int $w width
		* @param int $h height
		* @param array $prop array of properties. Possible values are (http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf): <ul><li>rect: Position and size of field on page.</li><li>borderStyle: Rectangle border appearance.</li><li>strokeColor: Color of bounding rectangle.</li><li>lineWidth: Width of the edge of the surrounding rectangle.</li><li>rotation: Rotation of field in 90-degree increments.</li><li>fillColor: Background color of field (gray, transparent, RGB, or CMYK).</li><li>userName: Short description of field that appears on mouse-over.</li><li>readonly: Whether the user may change the field contents.</li><li>doNotScroll: Whether text fields may scroll.</li><li>display: Whether visible or hidden on screen or in print.</li><li>textFont: Text font.</li><li>textColor: Text color.</li><li>textSize: Text size.</li><li>richText: Rich text.</li><li>richValue: Text.</li><li>comb: Text comb format.</li><li>multiline: Text multiline.</li><li>charLimit: Text limit to number of characters.</li><li>fileSelect: Text file selection format.</li><li>password: Text password format.</li><li>alignment: Text layout in text fields.</li><li>buttonAlignX: X alignment of icon on button face.</li><li>buttonAlignY: Y alignment of icon on button face.</li><li>buttonFitBounds: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleHow: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleWhen: Relative scaling of an icon to fit inside a button face.</li><li>highlight: Appearance of a button when pushed.</li><li>style: Glyph style for checkbox and radio buttons.</li><li>numItems: Number of items in a combo box or list box.</li><li>editable: Whether the user can type in a combo box.</li><li>multipleSelection: Whether multiple list box items may be selected.</li></ul>
		* @access protected
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _addfield($type, $name, $x, $y, $w, $h, $prop) {
			$k = $this->k;
			$this->javascript .= sprintf("f".$name."=this.addField('%s','%s',%d,[%.2f,%.2f,%.2f,%.2f]);", $name, $type, $this->PageNo()-1, $x*$k, ($this->h-$y)*$k+1, ($x+$w)*$k, ($this->h-$y-$h)*$k+1)."\n";
			$this->javascript .= "f".$name.".textSize=".$this->FontSizePt.";\n";
			while (list($key, $val) = each($prop)) {
				if (strcmp(substr($key,-5),"Color") == 0) {
					$val = $this->_JScolor($val);
				} else {
					$val = "'".$val."'";
				}
				$this->javascript .= "f".$name.".".$key."=".$val.";\n";
			}
			$this->x += $w;
		}
		
		/*
		* Creates a text field
		* @param string $name field name
		* @param int $w width
		* @param int $h height
		* @param string $prop properties. The value property allows to set the initial value. The multiline property allows to define the field as multiline. Possible values are (http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf): <ul><li>rect: Position and size of field on page.</li><li>borderStyle: Rectangle border appearance.</li><li>strokeColor: Color of bounding rectangle.</li><li>lineWidth: Width of the edge of the surrounding rectangle.</li><li>rotation: Rotation of field in 90-degree increments.</li><li>fillColor: Background color of field (gray, transparent, RGB, or CMYK).</li><li>userName: Short description of field that appears on mouse-over.</li><li>readonly: Whether the user may change the field contents.</li><li>doNotScroll: Whether text fields may scroll.</li><li>display: Whether visible or hidden on screen or in print.</li><li>textFont: Text font.</li><li>textColor: Text color.</li><li>textSize: Text size.</li><li>richText: Rich text.</li><li>richValue: Text.</li><li>comb: Text comb format.</li><li>multiline: Text multiline.</li><li>charLimit: Text limit to number of characters.</li><li>fileSelect: Text file selection format.</li><li>password: Text password format.</li><li>alignment: Text layout in text fields.</li><li>buttonAlignX: X alignment of icon on button face.</li><li>buttonAlignY: Y alignment of icon on button face.</li><li>buttonFitBounds: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleHow: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleWhen: Relative scaling of an icon to fit inside a button face.</li><li>highlight: Appearance of a button when pushed.</li><li>style: Glyph style for checkbox and radio buttons.</li><li>numItems: Number of items in a combo box or list box.</li><li>editable: Whether the user can type in a combo box.</li><li>multipleSelection: Whether multiple list box items may be selected.</li></ul>
		* @access public
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function TextField($name, $w, $h, $prop=array()) {
			$this->_addfield('text', $name, $this->x, $this->y, $w, $h, $prop);
		}
		
		/*
		* Creates a RadioButton field
		* @param string $name field name
		* @param int $w width
		* @param string $prop properties. Possible values are (http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf): <ul><li>rect: Position and size of field on page.</li><li>borderStyle: Rectangle border appearance.</li><li>strokeColor: Color of bounding rectangle.</li><li>lineWidth: Width of the edge of the surrounding rectangle.</li><li>rotation: Rotation of field in 90-degree increments.</li><li>fillColor: Background color of field (gray, transparent, RGB, or CMYK).</li><li>userName: Short description of field that appears on mouse-over.</li><li>readonly: Whether the user may change the field contents.</li><li>doNotScroll: Whether text fields may scroll.</li><li>display: Whether visible or hidden on screen or in print.</li><li>textFont: Text font.</li><li>textColor: Text color.</li><li>textSize: Text size.</li><li>richText: Rich text.</li><li>richValue: Text.</li><li>comb: Text comb format.</li><li>multiline: Text multiline.</li><li>charLimit: Text limit to number of characters.</li><li>fileSelect: Text file selection format.</li><li>password: Text password format.</li><li>alignment: Text layout in text fields.</li><li>buttonAlignX: X alignment of icon on button face.</li><li>buttonAlignY: Y alignment of icon on button face.</li><li>buttonFitBounds: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleHow: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleWhen: Relative scaling of an icon to fit inside a button face.</li><li>highlight: Appearance of a button when pushed.</li><li>style: Glyph style for checkbox and radio buttons.</li><li>numItems: Number of items in a combo box or list box.</li><li>editable: Whether the user can type in a combo box.</li><li>multipleSelection: Whether multiple list box items may be selected.</li></ul>
		* @access public
		* @author Nicola Asuni
		* @since 2.2.003 (2008-03-03)
		*/
		public function RadioButton($name, $w, $prop=array()) {
			if (!isset($prop['strokeColor'])) {
				$prop['strokeColor']='black';
			}
			$this->_addfield('radiobutton', $name, $this->x, $this->y, $w, $w, $prop);
		}
		
		/*
		* Creates a List-box field
		* @param string $name field name
		* @param int $w width
		* @param int $h height
		* @param array $values array containing the list of values.
		* @param string $prop properties. Possible values are (http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf): <ul><li>rect: Position and size of field on page.</li><li>borderStyle: Rectangle border appearance.</li><li>strokeColor: Color of bounding rectangle.</li><li>lineWidth: Width of the edge of the surrounding rectangle.</li><li>rotation: Rotation of field in 90-degree increments.</li><li>fillColor: Background color of field (gray, transparent, RGB, or CMYK).</li><li>userName: Short description of field that appears on mouse-over.</li><li>readonly: Whether the user may change the field contents.</li><li>doNotScroll: Whether text fields may scroll.</li><li>display: Whether visible or hidden on screen or in print.</li><li>textFont: Text font.</li><li>textColor: Text color.</li><li>textSize: Text size.</li><li>richText: Rich text.</li><li>richValue: Text.</li><li>comb: Text comb format.</li><li>multiline: Text multiline.</li><li>charLimit: Text limit to number of characters.</li><li>fileSelect: Text file selection format.</li><li>password: Text password format.</li><li>alignment: Text layout in text fields.</li><li>buttonAlignX: X alignment of icon on button face.</li><li>buttonAlignY: Y alignment of icon on button face.</li><li>buttonFitBounds: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleHow: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleWhen: Relative scaling of an icon to fit inside a button face.</li><li>highlight: Appearance of a button when pushed.</li><li>style: Glyph style for checkbox and radio buttons.</li><li>numItems: Number of items in a combo box or list box.</li><li>editable: Whether the user can type in a combo box.</li><li>multipleSelection: Whether multiple list box items may be selected.</li></ul>
		* @access public
		* @author Nicola Asuni
		* @since 2.2.003 (2008-03-03)
		*/
		public function ListBox($name, $w, $h, $values, $prop=array()) {
			if (!isset($prop['strokeColor'])) {
				$prop['strokeColor'] = 'ltGray';
			}
			$this->_addfield('listbox', $name, $this->x, $this->y, $w, $h, $prop);
			$s = '';
			foreach($values as $value) {
				$s .= "'".addslashes($value)."',";
			}
			$this->javascript .= "f".$name.".setItems([".substr($s,0,-1)."]);\n";
		}
		
		/*
		* Creates a Combo-box field
		* @param string $name field name
		* @param int $w width
		* @param int $h height
		* @param array $values array containing the list of values.
		* @param string $prop properties. Possible values are (http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf): <ul><li>rect: Position and size of field on page.</li><li>borderStyle: Rectangle border appearance.</li><li>strokeColor: Color of bounding rectangle.</li><li>lineWidth: Width of the edge of the surrounding rectangle.</li><li>rotation: Rotation of field in 90-degree increments.</li><li>fillColor: Background color of field (gray, transparent, RGB, or CMYK).</li><li>userName: Short description of field that appears on mouse-over.</li><li>readonly: Whether the user may change the field contents.</li><li>doNotScroll: Whether text fields may scroll.</li><li>display: Whether visible or hidden on screen or in print.</li><li>textFont: Text font.</li><li>textColor: Text color.</li><li>textSize: Text size.</li><li>richText: Rich text.</li><li>richValue: Text.</li><li>comb: Text comb format.</li><li>multiline: Text multiline.</li><li>charLimit: Text limit to number of characters.</li><li>fileSelect: Text file selection format.</li><li>password: Text password format.</li><li>alignment: Text layout in text fields.</li><li>buttonAlignX: X alignment of icon on button face.</li><li>buttonAlignY: Y alignment of icon on button face.</li><li>buttonFitBounds: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleHow: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleWhen: Relative scaling of an icon to fit inside a button face.</li><li>highlight: Appearance of a button when pushed.</li><li>style: Glyph style for checkbox and radio buttons.</li><li>numItems: Number of items in a combo box or list box.</li><li>editable: Whether the user can type in a combo box.</li><li>multipleSelection: Whether multiple list box items may be selected.</li></ul>
		* @access public
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function ComboBox($name, $w, $h, $values, $prop=array()) {
			$this->_addfield('combobox', $name, $this->x, $this->y, $w, $h, $prop);
			$s = '';
			foreach($values as $value) {
				$s .= "'".addslashes($value)."',";
			}
			$this->javascript .= "f".$name.".setItems([".substr($s,0,-1)."]);\n";
		}
		
		/*
		* Creates a CheckBox field
		* @param string $name field name
		* @param int $w width
		* @param boolean $checked define the initial state (default = false).
		* @param string $prop properties. Possible values are (http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf): <ul><li>rect: Position and size of field on page.</li><li>borderStyle: Rectangle border appearance.</li><li>strokeColor: Color of bounding rectangle.</li><li>lineWidth: Width of the edge of the surrounding rectangle.</li><li>rotation: Rotation of field in 90-degree increments.</li><li>fillColor: Background color of field (gray, transparent, RGB, or CMYK).</li><li>userName: Short description of field that appears on mouse-over.</li><li>readonly: Whether the user may change the field contents.</li><li>doNotScroll: Whether text fields may scroll.</li><li>display: Whether visible or hidden on screen or in print.</li><li>textFont: Text font.</li><li>textColor: Text color.</li><li>textSize: Text size.</li><li>richText: Rich text.</li><li>richValue: Text.</li><li>comb: Text comb format.</li><li>multiline: Text multiline.</li><li>charLimit: Text limit to number of characters.</li><li>fileSelect: Text file selection format.</li><li>password: Text password format.</li><li>alignment: Text layout in text fields.</li><li>buttonAlignX: X alignment of icon on button face.</li><li>buttonAlignY: Y alignment of icon on button face.</li><li>buttonFitBounds: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleHow: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleWhen: Relative scaling of an icon to fit inside a button face.</li><li>highlight: Appearance of a button when pushed.</li><li>style: Glyph style for checkbox and radio buttons.</li><li>numItems: Number of items in a combo box or list box.</li><li>editable: Whether the user can type in a combo box.</li><li>multipleSelection: Whether multiple list box items may be selected.</li></ul>
		* @access public
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function CheckBox($name, $w, $checked=false, $prop=array()) {
			$prop['value'] = ($checked ? 'Yes' : 'Off');
			if (!isset($prop['strokeColor'])) {
				$prop['strokeColor'] = 'black';
			}
			$this->_addfield('checkbox', $name, $this->x, $this->y, $w, $w, $prop);
		}
		
		/*
		* Creates a button field
		* @param string $name field name
		* @param int $w width
		* @param int $h height
		* @param string $caption caption.
		* @param string $action action triggered by the button (JavaScript code).
		* @param string $prop properties. Possible values are (http://www.adobe.com/devnet/acrobat/pdfs/js_developer_guide.pdf): <ul><li>rect: Position and size of field on page.</li><li>borderStyle: Rectangle border appearance.</li><li>strokeColor: Color of bounding rectangle.</li><li>lineWidth: Width of the edge of the surrounding rectangle.</li><li>rotation: Rotation of field in 90-degree increments.</li><li>fillColor: Background color of field (gray, transparent, RGB, or CMYK).</li><li>userName: Short description of field that appears on mouse-over.</li><li>readonly: Whether the user may change the field contents.</li><li>doNotScroll: Whether text fields may scroll.</li><li>display: Whether visible or hidden on screen or in print.</li><li>textFont: Text font.</li><li>textColor: Text color.</li><li>textSize: Text size.</li><li>richText: Rich text.</li><li>richValue: Text.</li><li>comb: Text comb format.</li><li>multiline: Text multiline.</li><li>charLimit: Text limit to number of characters.</li><li>fileSelect: Text file selection format.</li><li>password: Text password format.</li><li>alignment: Text layout in text fields.</li><li>buttonAlignX: X alignment of icon on button face.</li><li>buttonAlignY: Y alignment of icon on button face.</li><li>buttonFitBounds: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleHow: Relative scaling of an icon to fit inside a button face.</li><li>buttonScaleWhen: Relative scaling of an icon to fit inside a button face.</li><li>highlight: Appearance of a button when pushed.</li><li>style: Glyph style for checkbox and radio buttons.</li><li>numItems: Number of items in a combo box or list box.</li><li>editable: Whether the user can type in a combo box.</li><li>multipleSelection: Whether multiple list box items may be selected.</li></ul>
		* @access public
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function Button($name, $w, $h, $caption, $action, $prop=array()) {
			if (!isset($prop['strokeColor'])) {
				$prop['strokeColor'] = 'black';
			}
			if (!isset($prop['borderStyle'])) {
				$prop['borderStyle'] = 'beveled';
			}
			$this->_addfield('button', $name, $this->x, $this->y, $w, $h, $prop);
			$this->javascript .= "f".$name.".buttonSetCaption('".addslashes($caption)."');\n";
			$this->javascript .= "f".$name.".setAction('MouseUp','".addslashes($action)."');\n";
			$this->javascript .= "f".$name.".highlight='push';\n";
			$this->javascript .= "f".$name.".print=false;\n";
		}
		
		// END JAVASCRIPT - FORMS ------------------------------
		
		/*
		* Enable Write permissions for PDF Reader.
		* @access protected
		* @author Nicola Asuni
		* @since 2.9.000 (2008-03-26)
		*/
		protected function _putuserrights() {
			if (!$this->ur) {
				return;
			}
			$this->_out('/Perms');
			$this->_out('<<');
			$this->_out('/UR3');
			$this->_out('<<');
			//$this->_out('/SubFilter/adbe.pkcs7.detached/Filter/Adobe.PPKLite/Contents');
			//$this->_out('<0>');
			//$this->_out('/ByteRange[0 3]');
			$this->_out('/M '.$this->_datestring('D:'.date('YmdHis')));
			$this->_out('/Name(TCPDF)');
			$this->_out('/Reference[');
			$this->_out('<<');
			$this->_out('/TransformParams');
			$this->_out('<<');
			$this->_out('/Type/TransformParams');
			$this->_out('/V/2.2');
			if (!empty($this->ur_document)) {
				$this->_out('/Document['.$this->ur_document.']');
			}
			if (!empty($this->ur_annots)) {
				$this->_out('/Annots['.$this->ur_annots.']');
			}
			if (!empty($this->ur_form)) {
				$this->_out('/Form['.$this->ur_form.']');
			}
			if (!empty($this->ur_signature)) {
				$this->_out('/Signature['.$this->ur_signature.']');
			}			
			$this->_out('>>');
			$this->_out('/TransformMethod/UR3');
			$this->_out('/Type/SigRef');
			$this->_out('>>');
			$this->_out(']');
			$this->_out('/Type/Sig');
			$this->_out('>>');
			$this->_out('>>');
		}
		
		/*
		* Set User's Rights for PDF Reader
		* Check the PDF Reference 8.7.1 Transform Methods, 
		* Table 8.105 Entries in the UR transform parameters dictionary
		* @param boolean $enable if true enable user's rights on PDF reader
		* @param string $document Names specifying additional document-wide usage rights for the document. The only defined value is "/FullSave", which permits a user to save the document along with modified form and/or annotation data.
		* @param string $annots Names specifying additional annotation-related usage rights for the document. Valid names in PDF 1.5 and later are /Create/Delete/Modify/Copy/Import/Export, which permit the user to perform the named operation on annotations.
		* @param string $form Names specifying additional form-field-related usage rights for the document. Valid names are: /Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate 
		* @param string $signature Names specifying additional signature-related usage rights for the document. The only defined value is /Modify, which permits a user to apply a digital signature to an existing signature form field or clear a signed signature form field.
		* @access public
		* @author Nicola Asuni
		* @since 2.9.000 (2008-03-26)
		*/
		public function setUserRights(
				$enable=true, 
				$document="/FullSave",
				$annots="/Create/Delete/Modify/Copy/Import/Export",
				$form="/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate",
				$signature="/Modify") {
			$this->ur = $enable;
			$this->ur_document = $document;
			$this->ur_annots = $annots;
			$this->ur_form = $form;
			$this->ur_signature = $signature;
		}
		
		/*
		* Create a new page group.
		* NOTE: call this function before calling AddPage()
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function startPageGroup() {
			$this->newpagegroup = true;
		}
		
		/*
		* Return the current page in the group.
		* @return current page in the group
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function getGroupPageNo() {
			return $this->pagegroups[$this->currpagegroup];
		}
		
		/*
		* Return the alias of the current page group 
		* (will be replaced by the total number of pages in this group).
		* @return alias of the current page group
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function getPageGroupAlias() {
			return $this->currpagegroup;
		}
		
		/*
		* Put visibility settings.
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function _putocg() {
			$this->_newobj();
			$this->n_ocg_print = $this->n;
			$this->_out('<</Type /OCG /Name '.$this->_textstring('print'));
			$this->_out('/Usage <</Print <</PrintState /ON>> /View <</ViewState /OFF>>>>>>');
			$this->_out('endobj');
			$this->_newobj();
			$this->n_ocg_view=$this->n;
			$this->_out('<</Type /OCG /Name '.$this->_textstring('view'));
			$this->_out('/Usage <</Print <</PrintState /OFF>> /View <</ViewState /ON>>>>>>');
			$this->_out('endobj');
		}
		
		/*
		* Set the visibility of the successive elements.
		* This can be useful, for instance, to put a background 
		* image or color that will show on screen but won't print.
		* @param string $v visibility mode. Legal values are: all, print, screen.
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function setVisibility($v) {
			if ($this->openMarkedContent) {
				// close existing open marked-content
				$this->_out('EMC');
				$this->openMarkedContent = false;
			}
			switch($v) {
				case "print": {
					$this->_out('/OC /OC1 BDC');
					$this->openMarkedContent = true;
					break;
				}
				case "screen": {
					$this->_out('/OC /OC2 BDC');
					$this->openMarkedContent = true;
					break;
				}
				case "all": {
					$this->_out('');
					break;
				}
				default: {
					$this->Error('Incorrect visibility: '.$v);
					break;
				}
			}
			$this->visibility = $v;
		}
		
		/*
		* Add transparency parameters to the current extgstate
		* @param array $params parameters
		* @return the number of extgstates
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function addExtGState($parms) {
			$n = count($this->extgstates) + 1;
			$this->extgstates[$n]['parms'] = $parms;
			return $n;
		}
		
		/*
		* Add an extgstate
		* @param array $gs extgstate
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function setExtGState($gs) {
			$this->_out(sprintf('/GS%d gs', $gs));
		}
		
		/*
		* Put extgstates for object transparency
		* @param array $gs extgstate
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function _putextgstates() {
			$ne = count($this->extgstates);
			for ($i = 1; $i <= $ne; $i++) {
				$this->_newobj();
				$this->extgstates[$i]['n'] = $this->n;
				$this->_out('<</Type /ExtGState');
				foreach ($this->extgstates[$i]['parms'] as $k => $v) {
					$this->_out('/'.$k.' '.$v);
				}
				$this->_out('>>');
				$this->_out('endobj');
			}
		}
		
		/*
		* Set alpha for stroking (CA) and non-stroking (ca) operations.
		* @param float $alpha real value from 0 (transparent) to 1 (opaque)
		* @param string $bm blend mode, one of the following: Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn, HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function setAlpha($alpha, $bm='Normal') {
			$gs = $this->addExtGState(array('ca' => $alpha, 'CA' => $alpha, 'BM' => '/'.$bm));
			$this->setExtGState($gs);
		}

		/*
		* Set the default JPEG compression quality (1-100)
		* @param int $quality JPEG quality, integer between 1 and 100
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function setJPEGQuality($quality) {
			if (($quality < 1) OR ($quality > 100)) {
				$quality = 75;
			}
			$this->jpeg_quality = intval($quality);
		}
		
		/*
		* Set the default number of columns in a row for HTML tables.
		* @param int $cols number of columns
		* @access public
		* @since 3.0.014 (2008-06-04)
		*/
		public function setDefaultTableColumns($cols=4) { 
			$this->default_table_columns = intval($cols); 
		}
		
		/*
		* Set the height of cell repect font height.
		* @param int $h cell proportion respect font height (typical value = 1.25).
		* @access public
		* @since 3.0.014 (2008-06-04)
		*/
		public function setCellHeightRatio($h) { 
			$this->cell_height_ratio = $h; 
		}
		
		/*
		* return the height of cell repect font height.
		* @access public
		* @since 4.0.012 (2008-07-24)
		*/
		public function getCellHeightRatio() { 
			return $this->cell_height_ratio; 
		}
		
		/*
		* Set the PDF version (check PDF reference for valid values).
		* Default value is 1.t
		* @access public
		* @since 3.1.000 (2008-06-09)
		*/
		public function setPDFVersion($version="1.7") { 
			$this->PDFVersion = $version;
		}
		
		/*
		* Set the viewer preferences dictionary controlling the way the document is to be presented on the screen or in print.
		* (see Section 8.1 of PDF reference, "Viewer Preferences").
		* <ul>
		* <li>HideToolbar boolean (Optional) A flag specifying whether to hide the viewer application's tool bars when the document is active. Default value: false.</li>
		* <li>HideMenubar boolean (Optional) A flag specifying whether to hide the viewer application's menu bar when the document is active. Default value: false.</li>
		* <li>HideWindowUI boolean (Optional) A flag specifying whether to hide user interface elements in the document's window (such as scroll bars and navigation controls), leaving only the document's contents displayed. Default value: false.</li>
		* <li>FitWindow boolean (Optional) A flag specifying whether to resize the document's window to fit the size of the first displayed page. Default value: false.</li>
		* <li>CenterWindow boolean (Optional) A flag specifying whether to position the document's window in the center of the screen. Default value: false.</li>
		* <li>DisplayDocTitle boolean (Optional; PDF 1.4) A flag specifying whether the window's title bar should display the document title taken from the Title entry of the document information dictionary (see Section 10.2.1, "Document Information Dictionary"). If false, the title bar should instead display the name of the PDF file containing the document. Default value: false.</li>
		* <li>NonFullScreenPageMode name (Optional) The document's page mode, specifying how to display the document on exiting full-screen mode:<ul><li>UseNone Neither document outline nor thumbnail images visible</li><li>UseOutlines Document outline visible</li><li>UseThumbs Thumbnail images visible</li><li>UseOC Optional content group panel visible</li><ul>This entry is meaningful only if the value of the PageMode entry in the catalog dictionary (see Section 3.6.1, "Document Catalog") is FullScreen; it is ignored otherwise. Default value: UseNone.</li>
		* <li>ViewArea name (Optional; PDF 1.4) The name of the page boundary representing the area of a page to be displayed when viewing the document on the screen. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>ViewClip name (Optional; PDF 1.4) The name of the page boundary to which the contents of a page are to be clipped when viewing the document on the screen. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>PrintArea name (Optional; PDF 1.4) The name of the page boundary representing the area of a page to be rendered when printing the document. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>PrintClip name (Optional; PDF 1.4) The name of the page boundary to which the contents of a page are to be clipped when printing the document. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>PrintScaling name (Optional; PDF 1.6) The page scaling option to be selected when a print dialog is displayed for this document. Valid values are: <ul><li>None, which indicates that the print dialog should reflect no page scaling</li><li>AppDefault (default), which indicates that applications should use the current print scaling</li><ul></li>
		* <li>Duplex name (Optional; PDF 1.7) The paper handling option to use when printing the file from the print dialog. The following values are valid:<ul><li>Simplex - Print single-sided</li><li>DuplexFlipShortEdge - Duplex and flip on the short edge of the sheet</li><li>DuplexFlipLongEdge - Duplex and flip on the long edge of the sheet</li></ul>Default value: none</li>
		* <li>PickTrayByPDFSize boolean (Optional; PDF 1.7) A flag specifying whether the PDF page size is used to select the input paper tray. This setting influences only the preset values used to populate the print dialog presented by a PDF viewer application. If PickTrayByPDFSize is true, the check box in the print dialog associated with input paper tray is checked. Note: This setting has no effect on Mac OS systems, which do not provide the ability to pick the input tray by size.</li>
		* <li>PrintPageRange array (Optional; PDF 1.7) The page numbers used to initialize the print dialog box when the file is printed. The first page of the PDF file is denoted by 1. Each pair consists of the first and last pages in the sub-range. An odd number of integers causes this entry to be ignored. Negative numbers cause the entire array to be ignored. Default value: as defined by PDF viewer application</li>
		* <li>NumCopies integer (Optional; PDF 1.7) The number of copies to be printed when the print dialog is opened for this file. Supported values are the integers 2 through 5. Values outside this range are ignored. Default value: as defined by PDF viewer application, but typically 1</li>
		* </ul>
		* @param array $preferences array of options.
		* @author Nicola Asuni
		* @access public
		* @since 3.1.000 (2008-06-09)
		*/
		public function setViewerPreferences($preferences) { 
			$this->viewer_preferences = $preferences;
		}
		
		/**
		* Paints a linear colour gradient.
		* @param float $x abscissa of the top left corner of the rectangle.
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @param array $col1 first color (RGB components).
		* @param array $col2 second color (RGB components).
		* @param array $coords array of the form (x1, y1, x2, y2) which defines the gradient vector (see linear_gradient_coords.jpg). The default value is from left to right (x1=0, y1=0, x2=1, y2=0).
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function LinearGradient($x, $y, $w, $h, $col1=array(), $col2=array(), $coords=array(0,0,1,0)) {
			$this->Clip($x, $y, $w, $h);
			$this->Gradient(2, $col1, $col2, $coords);
		}
		
		/**
		* Paints a radial colour gradient.
		* @param float $x abscissa of the top left corner of the rectangle.
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @param array $col1 first color (RGB components).
		* @param array $col2 second color (RGB components).
		* @param array $coords array of the form (fx, fy, cx, cy, r) where (fx, fy) is the starting point of the gradient with color1, (cx, cy) is the center of the circle with color2, and r is the radius of the circle (see radial_gradient_coords.jpg). (fx, fy) should be inside the circle, otherwise some areas will not be defined.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function RadialGradient($x, $y, $w, $h, $col1=array(), $col2=array(), $coords=array(0.5,0.5,0.5,0.5,1)) {
			$this->Clip($x, $y, $w, $h);
			$this->Gradient(3, $col1, $col2, $coords);
		}
		
		/**
		* Paints a coons patch mesh.
		* @param float $x abscissa of the top left corner of the rectangle.
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @param array $col1 first color (lower left corner) (RGB components).
		* @param array $col2 second color (lower right corner) (RGB components).
		* @param array $col3 third color (upper right corner) (RGB components).
		* @param array $col4 fourth color (upper left corner) (RGB components).
		* @param array $coords <ul><li>for one patch mesh: array(float x1, float y1, .... float x12, float y12): 12 pairs of coordinates (normally from 0 to 1) which specify the Bézier control points that define the patch. First pair is the lower left edge point, next is its right control point (control point 2). Then the other points are defined in the order: control point 1, edge point, control point 2 going counter-clockwise around the patch. Last (x12, y12) is the first edge point's left control point (control point 1).</li><li>for two or more patch meshes: array[number of patches]: arrays with the following keys for each patch: f: where to put that patch (0 = first patch, 1, 2, 3 = right, top and left of precedent patch - I didn't figure this out completely - just try and error ;-) points: 12 pairs of coordinates of the Bézier control points as above for the first patch, 8 pairs of coordinates for the following patches, ignoring the coordinates already defined by the precedent patch (I also didn't figure out the order of these - also: try and see what's happening) colors: must be 4 colors for the first patch, 2 colors for the following patches</li></ul>
		* @param array $coords_min minimum value used by the coordinates. If a coordinate's value is smaller than this it will be cut to coords_min. default: 0
		* @param array $coords_max maximum value used by the coordinates. If a coordinate's value is greater than this it will be cut to coords_max. default: 1
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function CoonsPatchMesh($x, $y, $w, $h, $col1=array(), $col2=array(), $col3=array(), $col4=array(), $coords=array(0.00,0.0,0.33,0.00,0.67,0.00,1.00,0.00,1.00,0.33,1.00,0.67,1.00,1.00,0.67,1.00,0.33,1.00,0.00,1.00,0.00,0.67,0.00,0.33), $coords_min=0, $coords_max=1) {
			$this->Clip($x, $y, $w, $h);        
			$n = count($this->gradients) + 1;
			$this->gradients[$n]['type'] = 6; //coons patch mesh
			//check the coords array if it is the simple array or the multi patch array
			if (!isset($coords[0]['f'])){
				//simple array -> convert to multi patch array
				if (!isset($col1[1])) {
					$col1[1] = $col1[2] = $col1[0];
				}
				if (!isset($col2[1])) {
					$col2[1] = $col2[2] = $col2[0];
				}
				if (!isset($col3[1])) {
					$col3[1] = $col3[2] = $col3[0];
				}
				if (!isset($col4[1])) {
					$col4[1] = $col4[2] = $col4[0];
				}
				$patch_array[0]['f'] = 0;
				$patch_array[0]['points'] = $coords;
				$patch_array[0]['colors'][0]['r'] = $col1[0];
				$patch_array[0]['colors'][0]['g'] = $col1[1];
				$patch_array[0]['colors'][0]['b'] = $col1[2];
				$patch_array[0]['colors'][1]['r'] = $col2[0];
				$patch_array[0]['colors'][1]['g'] = $col2[1];
				$patch_array[0]['colors'][1]['b'] = $col2[2];
				$patch_array[0]['colors'][2]['r'] = $col3[0];
				$patch_array[0]['colors'][2]['g'] = $col3[1];
				$patch_array[0]['colors'][2]['b'] = $col3[2];
				$patch_array[0]['colors'][3]['r'] = $col4[0];
				$patch_array[0]['colors'][3]['g'] = $col4[1];
				$patch_array[0]['colors'][3]['b'] = $col4[2];
			} else {
				//multi patch array
				$patch_array = $coords;
			}
			$bpcd = 65535; //16 BitsPerCoordinate
			//build the data stream
			$this->gradients[$n]['stream'] = "";
			for($i=0; $i < count($patch_array); $i++) {
				$this->gradients[$n]['stream'] .= chr($patch_array[$i]['f']); //start with the edge flag as 8 bit
				for($j=0; $j < count($patch_array[$i]['points']); $j++) {
					//each point as 16 bit
					$patch_array[$i]['points'][$j] = (($patch_array[$i]['points'][$j]-$coords_min)/($coords_max-$coords_min))*$bpcd;
					if ($patch_array[$i]['points'][$j] < 0) {
						$patch_array[$i]['points'][$j] = 0;
					}
					if ($patch_array[$i]['points'][$j] > $bpcd) {
						$patch_array[$i]['points'][$j] = $bpcd;
					}
					$this->gradients[$n]['stream'] .= chr(floor($patch_array[$i]['points'][$j]/256));
					$this->gradients[$n]['stream'] .= chr(floor($patch_array[$i]['points'][$j]%256));
				}
				for($j=0; $j < count($patch_array[$i]['colors']); $j++) {
					//each color component as 8 bit
					$this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['r']);
					$this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['g']);
					$this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['b']);
				}
			}
			//paint the gradient
			$this->_out('/Sh'.$n.' sh');
			//restore previous Graphic State
			$this->_out('Q');
		}
		
		/**
		* Set a rectangular clipping area.
		* @param float $x abscissa of the top left corner of the rectangle (or top right corner for RTL mode).
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function Clip($x, $y, $w, $h){
			if ($this->rtl) {
				$x = $this->w - $x - $w;
			}
			//save current Graphic State
			$s = 'q';
			//set clipping area
			$s .= sprintf(' %.2f %.2f %.2f %.2f re W n', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k);
			//set up transformation matrix for gradient
			$s .= sprintf(' %.3f 0 0 %.3f %.3f %.3f cm', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k);
			$this->_out($s);
		}
				
		/**
		* Output gradient.
		* @param int $type type of gradient.
		* @param array $col1 first color (RGB components).
		* @param array $col2 second color (RGB components).
		* @param array $coords array of coordinates.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function Gradient($type, $col1, $col2, $coords){
			$n = count($this->gradients) + 1;
			$this->gradients[$n]['type'] = $type;
			if (!isset($col1[1])) {
				$col1[1]=$col1[2]=$col1[0];
			}
			$this->gradients[$n]['col1'] = sprintf('%.3f %.3f %.3f', ($col1[0]/255), ($col1[1]/255), ($col1[2]/255));
			if (!isset($col2[1])) {
				$col2[1] = $col2[2] = $col2[0];
			}
			$this->gradients[$n]['col2'] = sprintf('%.3f %.3f %.3f', ($col2[0]/255), ($col2[1]/255), ($col2[2]/255));
			$this->gradients[$n]['coords'] = $coords;
			//paint the gradient
			$this->_out('/Sh'.$n.' sh');
			//restore previous Graphic State
			$this->_out('Q');
		}
		
		/**
		* Output shaders.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		function _putshaders() {
			foreach($this->gradients as $id => $grad) {  
				if (($grad['type'] == 2) OR ($grad['type'] == 3)) {
					$this->_newobj();
					$this->_out('<<');
					$this->_out('/FunctionType 2');
					$this->_out('/Domain [0.0 1.0]');
					$this->_out('/C0 ['.$grad['col1'].']');
					$this->_out('/C1 ['.$grad['col2'].']');
					$this->_out('/N 1');
					$this->_out('>>');
					$this->_out('endobj');
					$f1 = $this->n;
				}
				$this->_newobj();
				$this->_out('<<');
				$this->_out('/ShadingType '.$grad['type']);
				$this->_out('/ColorSpace /DeviceRGB');
				if ($grad['type'] == 2) {
					$this->_out(sprintf('/Coords [%.3f %.3f %.3f %.3f]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3]));
					$this->_out('/Function '.$f1.' 0 R');
					$this->_out('/Extend [true true] ');
					$this->_out('>>');
				} elseif ($grad['type'] == 3) {
					//x0, y0, r0, x1, y1, r1
					//at this this time radius of inner circle is 0
					$this->_out(sprintf('/Coords [%.3f %.3f 0 %.3f %.3f %.3f]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3], $grad['coords'][4]));
					$this->_out('/Function '.$f1.' 0 R');
					$this->_out('/Extend [true true] ');
					$this->_out('>>');
				} elseif ($grad['type'] == 6) {
					$this->_out('/BitsPerCoordinate 16');
					$this->_out('/BitsPerComponent 8');
					$this->_out('/Decode[0 1 0 1 0 1 0 1 0 1]');
					$this->_out('/BitsPerFlag 8');
					$this->_out('/Length '.strlen($grad['stream']));
					$this->_out('>>');
					$this->_putstream($grad['stream']);
				}
				$this->_out('endobj');
				$this->gradients[$id]['id'] = $this->n;
			}
		}

		/**
		* Output an arc
		* @author Maxime Delorme, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function _outarc($x1, $y1, $x2, $y2, $x3, $y3 ) {
			$h = $this->h;
			$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1*$this->k, ($h-$y1)*$this->k, $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
		}
		
		/**
		* Draw the sector of a circle.
		* It can be used for instance to render pie charts.
		* @param float $xc abscissa of the center.
		* @param float $yc ordinate of the center.
		* @param float $r radius.
		* @param float $a start angle (in degrees).
		* @param float $b end angle (in degrees).
		* @param string $style: D, F, FD or DF (draw, fill, fill and draw). Default: FD.
		* @param float $cw: indicates whether to go clockwise (default: true).
		* @param float $o: origin of angles (0 for 3 o'clock, 90 for noon, 180 for 9 o'clock, 270 for 6 o'clock). Default: 90.
		* @author Maxime Delorme, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function PieSector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90) {
			if ($this->rtl) {
				$xc = $this->w - $xc - $w;
			}
			if ($cw) {
				$d = $b;
				$b = $o - $a;
				$a = $o - $d;
			} else {
				$b += $o;
				$a += $o;
			}
			$a = ($a % 360) + 360;
			$b = ($b % 360) + 360;
			if ($a > $b) {
				$b +=360;
			}
			$b = $b / 360 * 2 * M_PI;
			$a = $a / 360 * 2 * M_PI;
			$d = $b - $a;
			if ($d == 0 ) {
				$d = 2 * M_PI;
			}
			$k = $this->k;
			$hp = $this->h;
			if ($style=='F') {
				$op = 'f';
			} elseif ($style=='FD' or $style=='DF') {
				$op = 'b';
			} else {
				$op = 's';
			}
			if (sin($d/2)) {
				$MyArc = 4/3 * (1 - cos($d/2)) / sin($d/2) * $r;
			}
			//first put the center
			$this->_out(sprintf('%.2f %.2f m', ($xc)*$k, ($hp-$yc)*$k));
			//put the first point
			$this->_out(sprintf('%.2f %.2f l', ($xc+$r*cos($a))*$k, (($hp-($yc-$r*sin($a)))*$k)));
			//draw the arc
			if ($d < (M_PI/2)) {
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
			} else {
				$b = $a + $d/4;
				$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
				$a = $b;
				$b = $a + $d/4;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
				$a = $b;
				$b = $a + $d/4;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b) );
				$a = $b;
				$b = $a + $d/4;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
			}
			//terminate drawing
			$this->_out($op);
		}
		
		/**
		* Embed vector-based Adobe Illustrator (AI) or AI-compatible EPS files.
		* Only vector drawing is supported, not text or bitmap. 
		* Although the script was successfully tested with various AI format versions, best results are probably achieved with files that were exported in the AI3 format (tested with Illustrator CS2, Freehand MX and Photoshop CS2).
		* @param string $file Name of the file containing the image.
		* @param float $x Abscissa of the upper-left corner.
		* @param float $y Ordinate of the upper-left corner.
		* @param float $w Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param float $h Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param boolean useBoundingBox specifies whether to position the bounding box (true) or the complete canvas (false) at location (x,y). Default value is true.
		* @param string $align Indicates the alignment of the pointer next to image insertion relative to image height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		* @author Valentin Schmidt, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function ImageEps($file, $x, $y, $w=0, $h=0, $link='', $useBoundingBox=true, $align='') {
			if ($this->rtl) {
				$x = ($this->w - $x - $w);
			}
			$data = file_get_contents($file);
			if ($data === false) {
				$this->Error('EPS file not found: '.$file);
			}
			$regs = array();
			// EPS/AI compatibility check (only checks files created by Adobe Illustrator!)
			preg_match ('/%%Creator:([^\r\n]+)/', $data, $regs); # find Creator
			if (count($regs) > 1) {
				$version_str = trim($regs[1]); # e.g. "Adobe Illustrator(R) 8.0"
				if (strpos($version_str, 'Adobe Illustrator') !== false) {
					$versexp = explode(' ', $version_str);
					$version = (float)array_pop($versexp);
					if ($version >= 9) {
						$this->Error('This version of Adobe Illustrator file is not supported: '.$file);
					}
				}
			}
			// strip binary bytes in front of PS-header
			$start = strpos($data, '%!PS-Adobe');
			if ($start > 0) {
				$data = substr($data, $start);
			}
			// find BoundingBox params
			preg_match ("/%%BoundingBox:([^\r\n]+)/", $data, $regs);
			if (count($regs) > 1) {
				list($x1, $y1, $x2, $y2) = explode(' ', trim($regs[1]));
			} else {
				$this->Error('No BoundingBox found in EPS file: '.$file);
			}
			$start = strpos($data, '%%EndSetup');
			if ($start === false) {
				$start = strpos($data, '%%EndProlog');
			}
			if ($start === false) {
				$start = strpos($data, '%%BoundingBox');
			}
			$data = substr($data, $start);
			$end = strpos($data, '%%PageTrailer');
			if ($end===false) {
				$end = strpos($data, 'showpage');
			}
			if ($end) {
				$data = substr($data, 0, $end);
			}			
			// save the current graphic state
			$this->_out('q');
			$k = $this->k;
			if ($useBoundingBox){
				$dx = $x * $k - $x1;
				$dy = $y * $k - $y1;
			} else {
				$dx = $x * $k;
				$dy = $y * $k;
			}
			// translate
			$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', 1, 0, 0, 1, $dx, $dy+($this->hPt - 2*$y*$k - ($y2-$y1))));
			if ($w > 0) {
				$scale_x = $w/(($x2-$x1)/$k);
				if ($h > 0) {
					$scale_y = $h/(($y2-$y1)/$k);
				} else {
					$scale_y = $scale_x;
					$h = ($y2-$y1)/$k * $scale_y;
				}
			} else {
				if ($h > 0) {
					$scale_y = $h/(($y2-$y1)/$k);
					$scale_x = $scale_y;
					$w = ($x2-$x1)/$k * $scale_x;
				} else {
					$w = ($x2 - $x1) / $k;
					$h = ($y2 - $y1) / $k;
				}
			}
			// scale
			if (isset($scale_x)) {
				$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $scale_x, 0, 0, $scale_y, $x1*(1-$scale_x), $y2*(1-$scale_y)));
			}
			// handle pc/unix/mac line endings
			$lines = split ("\r\n|[\r\n]", $data);
			$u=0;
			$cnt = count($lines);
			for ($i=0; $i < $cnt; $i++) {
				$line = $lines[$i];
				if (($line == '') OR ($line{0} == '%')) {
					continue;
				}
				$len = strlen($line);
				$chunks = explode(' ', $line);
				$cmd = array_pop($chunks);
				// RGB
				if (($cmd == 'Xa') OR ($cmd == 'XA')) {
					$b = array_pop($chunks); 
					$g = array_pop($chunks); 
					$r = array_pop($chunks);
					$this->_out("$r $g $b ". ($cmd=='Xa'?'rg':'RG') ); //substr($line, 0, -2).'rg' -> in EPS (AI8): c m y k r g b rg!
					continue;
				}
				switch ($cmd) {
					case 'm':
					case 'l':
					case 'v':
					case 'y':
					case 'c':
					case 'k':
					case 'K':
					case 'g':
					case 'G':
					case 's':
					case 'S':
					case 'J':
					case 'j':
					case 'w':
					case 'M':
					case 'd':
					case 'n':
					case 'v': {
						$this->_out($line);
						break;
					}
					case 'x': {// custom fill color
						list($c,$m,$y,$k) = $chunks;
						$this->_out("$c $m $y $k k");
						break;
					}
					case 'X': { // custom stroke color
						list($c,$m,$y,$k) = $chunks;
						$this->_out("$c $m $y $k K");
						break;
					}
					case 'Y':
					case 'N':
					case 'V':
					case 'L':
					case 'C': {
						$line{$len-1} = strtolower($cmd);
						$this->_out($line);
						break;
					}
					case 'b':
					case 'B': {
						$this->_out($cmd . '*');
						break;
					}
					case 'f':
					case 'F': {
						if ($u > 0) {
							$isU = false;
							$max = min($i+5, $cnt);
							for ($j=$i+1; $j < $max; $j++)
							  $isU = ($isU OR (($lines[$j] == 'U') OR ($lines[$j] == '*U')));
							if ($isU) {
								$this->_out("f*");
							}
						} else {
							$this->_out("f*");
						}
						break;
					}
					case '*u': {
						$u++;
						break;
					}
					case '*U': {
						$u--;
						break;
					}
				}
			}
			// restore previous graphic state
			$this->_out('Q');
			if ($link) {
				$this->Link($x, $y, $w, $h, $link);
			}
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			if ($this->rtl) {
				// set left side coordinate
				$this->img_rb_x = ($this->w - $x - $w);
			} else {
				// set right side coordinate
				$this->img_rb_x = $x + $w;
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T':{
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M':{
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B':{
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N':{
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
		}
		
		/**
	 	 * Set document barcode.
		 * @param string $bc barcode
		 */
		public function setBarcode($bc="") {
			$this->barcode = $bc;
		}
		
		/**
	 	 * Get current barcode.
		 * @return string
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getBarcode() {
			return $this->barcode;
		}
		
		/**
	 	 * Print Barcode.
	 	 * @param string $code code to print
	 	 * @param string $type type of barcode.
		 * @param int $x x position in user units
		 * @param int $y y position in user units
		 * @param int $w width in user units
		 * @param int $h height position in user units
		 * @param float $xres width of the smallest bar in user units
		 * @param array $style array of options:<ul><li>string $style["position"] barcode position inside the specified width: L = left (default for LTR); C = center; R = right (default for RTL); S = stretch</li><li>boolean $style["border"] if true prints a border around the barcode</li><li>int $style["padding"] padding to leave around the barcode in user units</li><li>array $style["fgcolor"] color array for bars and text</li><li>mixed $style["bgcolor"] color array for background or false for transparent</li><li>boolean $style["text"] boolean if true prints text below the barcode</li><li>string $style["font"] font name for text</li><li>int $style["fontsize"] font size for text</li><li>int $style["stretchtext"]: 0 = disabled; 1 = horizontal scaling only if necessary; 2 = forced horizontal scaling; 3 = character spacing only if necessary; 4 = forced character spacing</li></ul>
		 * @param string $align Indicates the alignment of the pointer next to image insertion relative to image height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		 * @author Nicola Asuni
		 * @since 3.1.000 (2008-06-09)
		 * @access public
		 */
		public function write1DBarcode($code, $type, $x='', $y='', $w='', $h='', $xres=0.4, $style='', $align='') {
			if (empty($code)) {
				return;
			}
			$barcodeobj = new TCPDFbarcode($code, $type);
			$arrcode = $barcodeobj->getBarcodeArray();
			if ($arrcode === false) {
				$this->Error('Error in barcode string');
			}
			// set default values
			if (!isset($style["position"])) {
				if ($this->rtl) {
					$style["position"] = "R";
				} else {
					$style["position"] = "L";
				}
			}
			if (!isset($style["padding"])) {
				$style["padding"] = 0;
			}
			if (!isset($style["fgcolor"])) {
				$style["fgcolor"] = array(0,0,0); // default black
			}
			if (!isset($style["bgcolor"])) {
				$style["bgcolor"] = false; // default transparent
			}
			if (!isset($style["border"])) {
				$style["border"] = false;
			}
			if (!isset($style["text"])) {
				$style["text"] = false;
				$fontsize = 0;
			}
			if ($style["text"] AND isset($style["font"])) {
				$prevFontFamily = $this->FontFamily;
				$prevFontStyle = $this->FontStyle;
				$prevFontSizePt = $this->FontSizePt;
				if (isset($style["fontsize"])) {
					$fontsize = $style["fontsize"];
				} else {
					$fontsize = 0;
				}
				$this->SetFont($style["font"], '', $fontsize);
			}
			if (!isset($style["stretchtext"])) {
				$style["stretchtext"] = 4;
			}
			// set foreground color
			$prevDrawColor = $this->DrawColor;
			$prevTextColor = $this->TextColor;
			$this->SetDrawColorArray($style["fgcolor"]);
			$this->SetTextColorArray($style["fgcolor"]);
			if (empty($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $this->x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $this->x;
				}
			}
			if (empty($x)) {
				$x = $this->GetX();
			}
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			if (empty($y)) {
				$y = $this->GetY();
			}
			if (empty($xres)) {
				$xres = 0.4;
			}
			$fbw = ($arrcode["maxw"] * $xres) + (2 * $style["padding"]);
			$extraspace = ($this->cell_height_ratio * $fontsize / $this->k) + (2 * $style["padding"]);
			if (empty($h)) {
				$h = 10 + $extraspace;
			}
			if ((($y + $h) > $this->PageBreakTrigger) AND (empty($this->InFooter)) AND ($this->AcceptPageBreak())) {
				//Automatic page break
				$x = $this->x;
				$ws = $this->ws;
				if ($ws > 0) {
					$this->ws = 0;
					$this->_out('0 Tw');
				}
				$this->AddPage($this->CurOrientation);
				if ($ws > 0) {
					$this->ws = $ws;
					$this->_out(sprintf('%.3f Tw',$ws * $k));
				}
				$this->x = $x;
				$y = $this->y;
			}
			// maximum bar heigth
			$barh = $h - $extraspace;
			switch ($style["position"]) {
				case "L": { // left
					if ($this->rtl) {
						$xpos = $x - $w;
					} else {
						$xpos = $x;
					}
					break;
				}
				case "C": { // center
					$xdiff = (($w - $fbw) / 2);
					if ($this->rtl) {
						$xpos = $x - $w + $xdiff;
					} else {
						$xpos = $x + $xdiff;
					}
					break;
				}
				case "R": { // right
					if ($this->rtl) {
						$xpos = $x - $fbw;
					} else {
						$xpos = $x + $w - $fbw;
					}
					break;
				}
				case "S": { // stretch
					$fbw = $w;
					$xres = ($w - (2 * $style["padding"])) / $arrcode["maxw"];
					if ($this->rtl) {
						$xpos = $x - $w;
					} else {
						$xpos = $x;
					}
					break;
				}
			}
			$xpos_rect = $xpos;
			$xpos = $xpos_rect + $style["padding"];
			$xpos_text = $xpos;
			// barcode is always printed in LTR direction
			$tempRTL = $this->rtl;
			$this->rtl = false;
			// print background color
			if ($style["bgcolor"]) {
				$this->Rect($xpos_rect, $y, $fbw, $h, 'DF', '', $style["bgcolor"]);
			} elseif ($style["border"]) {
				$this->Rect($xpos_rect, $y, $fbw, $h, 'D');
			}
			// print bars
			if ($arrcode !== false) {
				foreach ($arrcode["bcode"] as $k => $v) {
					$bw = ($v["w"] * $xres);
					if ($v["t"]) {
						// braw a vertical bar
						$ypos = $y + $style["padding"] + ($v["p"] * $barh / $arrcode["maxh"]);
						$this->Rect($xpos, $ypos, $bw, ($v["h"] * $barh  / $arrcode["maxh"]), 'DF', array("L"=>0,"T"=>0,"R"=>0,"B"=>0), $style["fgcolor"]);
					}
					$xpos += $bw;
				}
			}
			// print text
			if ($style["text"]) {
				// print text
				$this->x = $xpos_text;
				$this->y = $y + $style["padding"] + $barh; 
				$this->Cell(($arrcode["maxw"] * $xres), ($this->cell_height_ratio * $fontsize / $this->k), $code, 0, 0, 'C', 0, '', $style["stretchtext"]);
			}
			// restore original direction
			$this->rtl = $tempRTL;
			// restore previous font
			if ($style["text"] AND isset($style["font"])) {
				$this->SetFont($prevFontFamily, $prevFontStyle, $prevFontSizePt);
			}
			// restore colors
			$this->DrawColor = $prevDrawColor;
			$this->TextColor = $prevTextColor;
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			if ($this->rtl) {
				// set left side coordinate
				$this->img_rb_x = ($this->w - $x - $w);
			} else {
				// set right side coordinate
				$this->img_rb_x = $x + $w;
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T':{
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M':{
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B':{
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N':{
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
		}
		
		/**
	 	 * This function is DEPRECATED, please use the new write1DBarcode() function.
		 * @param int $x x position in user units
		 * @param int $y y position in user units
		 * @param int $w width in user units
		 * @param int $h height position in user units
		 * @param string $type type of barcode (I25, C128A, C128B, C128C, C39)
		 * @param string $style barcode style
		 * @param string $font font for text
		 * @param int $xres x resolution
		 * @param string $code code to print
		 * @deprecated deprecated since version 3.1.000 (2008-06-10)
		 * @see write1DBarcode()
		 */
		public function writeBarcode($x, $y, $w, $h, $type, $style, $font, $xres, $code) {
			// convert old settings for the new write1DBarcode() function.
			$xres = 1 / $xres;
			$newstyle = array(
				"position" => "L",
				"border" => false,
				"padding" => 0,
				"fgcolor" => array(0,0,0),
				"bgcolor" => false,
				"text" => true,
				"font" => $font,
				"fontsize" => 8,
				"stretchtext" => 4
			);
			if ($style & 1) {
				$newstyle["border"] = true;
			}
			if ($style & 2) {
				$newstyle["bgcolor"] = false;
			}
			if ($style & 4) {
				$newstyle["position"] = "C";
			} elseif ($style & 8) {
				$newstyle["position"] = "L";
			} elseif ($style & 16) {
				$newstyle["position"] = "R";
			}
			if ($style & 128) {
				$newstyle["text"] = true;
			}
			if ($style & 256) {
				$newstyle["stretchtext"] = 4;
			}
			$this->write1DBarcode($code, $type, $x, $y, $w, $h, $xres, $newstyle, '');
		}
		
		/**
		 * Returns an array containing current margins:
		 * <ul>
				<li>$ret['left'] = left  margin</li>
				<li>$ret['right'] = right margin</li>
				<li>$ret['top'] = top margin</li>
				<li>$ret['bottom'] = bottom margin</li>
				<li>$ret['header'] = header margin</li>
				<li>$ret['footer'] = footer margin</li>
				<li>$ret['cell'] = cell margin</li>
		 * </ul>
		 * @return array containing all margins measures 
		 * @since 3.2.000 (2008-06-23)
		 */
		public function getMargins() {
			$ret = array(
				'left' => $this->lMargin,
				'right' => $this->rMargin,
				'top' => $this->tMargin,
				'bottom' => $this->bMargin,
				'header' => $this->header_margin,
				'footer' => $this->footer_margin,
				'cell' => $this->cMargin,
			);
			return $ret;
		}
		
		/**
		 * Returns an array containing original margins:
		 * <ul>
				<li>$ret['left'] = left  margin</li>
				<li>$ret['right'] = right margin</li>
		 * </ul>
		 * @return array containing all margins measures 
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getOriginalMargins() {
			$ret = array(
				'left' => $this->original_lMargin,
				'right' => $this->original_rMargin
			);
			return $ret;
		}
		
		/**
		 * Returns the current font size.
		 * @return current font size 
		 * @since 3.2.000 (2008-06-23)
		 */
		public function getFontSize() {
			return $this->FontSize;
		}
		
		/**
		 * Returns the current font size in points unit.
		 * @return current font size in points unit 
		 * @since 3.2.000 (2008-06-23)
		 */
		public function getFontSizePt() {
			return $this->FontSizePt;
		}
		
		/**
		 * Prints a cell (rectangular area) with optional borders, background color and html text string. 
		 * The upper-left corner of the cell corresponds to the current position. After the call, the current position moves to the right or to the next line.<br />
		 * If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
		 * @param float $w Cell width. If 0, the cell extends up to the right margin.
		 * @param float $h Cell minimum height. The cell extends automatically if needed.
		 * @param float $x upper-left corner X coordinate
		 * @param float $y upper-left corner Y coordinate
		 * @param string $html html text to print. Default value: empty string.
		 * @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		 * @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL language)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>
	Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
		 * @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		 * @param boolean $reseth if true reset the last cell height (default true).
		 * @param string $align Allows to center or align the text. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		 * @uses MultiCell()
		 * @see Multicell(), writeHTML()
		 */
		public function writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='') {
			return $this->MultiCell($w, $h, $html, $border, $align, $fill, $ln, $x, $y, $reseth, 0, true);
		}
		
		/**
	 	 * Returns the HTML DOM array.
	 	 * <ul><li>$dom[$key]['tag'] = true if tag, false otherwise;</li><li>$dom[$key]['value'] = tag name or text;</li><li>$dom[$key]['opening'] = true if opening tag, false otherwise;</li><li>$dom[$key]['attribute'] = array of attributes (attribute name is the key);</li><li>$dom[$key]['style'] = array of style attributes (attribute name is the key);</li><li>$dom[$key]['parent'] = id of parent element;</li><li>$dom[$key]['fontname'] = font family name;</li><li>$dom[$key]['fontstyle'] = font style;</li><li>$dom[$key]['fontsize'] = font size in points;</li><li>$dom[$key]['bgcolor'] = RGB array of background color;</li><li>$dom[$key]['fgcolor'] = RGB array of foreground color;</li><li>$dom[$key]['width'] = width in pixels;</li><li>$dom[$key]['height'] = height in pixels;</li><li>$dom[$key]['align'] = text alignment;</li><li>$dom[$key]['cols'] = number of colums in table;</li><li>$dom[$key]['rows'] = number of rows in table;</li></ul>
		 * @param string $html html code
		 * @return array
		 * @since 3.2.000 (2008-06-20)
		 */
		protected function getHtmlDomArray($html) {
			// remove all unsupported tags (the line below lists all supported tags)
			$html = strip_tags($html, "<a><b><blockquote><br><br/><dd><del><div><dl><dt><em><font><h1><h2><h3><h4><h5><h6><hr><i><img><li><ol><p><small><span><strong><sub><sup><table><td><th><tr><u><ul>"); 
			//replace carriage returns, newlines and tabs
			$repTable = array("\t" => " ", "\n" => " ", "\r" => " ", "\0" => " ", "\x0B" => " ", "\\" => "\\\\");
			$html = strtr($html, $repTable);
			// remove extra spaces from tables
			$html = preg_replace('/[\s]*<\/table>[\s]*/', '</table>', $html);
			$html = preg_replace('/[\s]*<\/tr>[\s]*/', '</tr>', $html);
			$html = preg_replace('/[\s]*<tr/', '<tr', $html);
			$html = preg_replace('/[\s]*<\/th>[\s]*/', '</th>', $html);
			$html = preg_replace('/[\s]*<th/', '<th', $html);
			$html = preg_replace('/[\s]*<\/td>[\s]*/', '</td>', $html);
			$html = preg_replace('/[\s]*<td/', '<td', $html);
			// pattern for generic tag
			$tagpattern = '/(<[^>]+>)/Uu';
			// explodes the string
			$a = preg_split($tagpattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			// count elements
			$maxel = count($a);
			$key = 0;
			// create an array of elements
			$dom = array();
			$dom[$key] = array();
			// set first void element
			$dom[$key]['tag'] = false;
			$dom[$key]['value'] = "";
			$dom[$key]['parent'] = 0;
			$dom[$key]['fontname'] = $this->FontFamily;
			$dom[$key]['fontstyle'] = $this->FontStyle;
			$dom[$key]['fontsize'] = $this->FontSizePt;
			$dom[$key]['bgcolor'] = false;
			$dom[$key]['fgcolor'] = $this->fgcolor;
			$dom[$key]['align'] = '';
			$key++;
			$level = array();
			array_push($level, 0); // root
			while ($key <= $maxel) {
				if ($key > 0) {
					$dom[$key] = array();
				}
				$element = $a[($key-1)];
				if (preg_match($tagpattern, $element)) {
					// html tag
					$dom[$key]['tag'] = true;
					$element = substr($element, 1, -1);
					// get tag name
					preg_match('/[\/]?([a-zA-Z0-9]*)/', $element, $tag);
					$dom[$key]['value'] = strtolower($tag[1]);
					if ($element{0} == '/') {
						// closing html tag
						$dom[$key]['opening'] = false;
						$dom[$key]['parent'] = end($level);
						array_pop($level);
						$dom[$key]['fontname'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontname'];
						$dom[$key]['fontstyle'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontstyle'];
						$dom[$key]['fontsize'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontsize'];
						$dom[$key]['bgcolor'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['bgcolor'];
						$dom[$key]['fgcolor'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fgcolor'];
						$dom[$key]['align'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['align'];
						// set the number of columns in table tag
						if (($dom[$key]['value'] == "tr") AND (!isset($dom[($dom[($dom[$key]['parent'])]['parent'])]['cols']))) {
							$dom[($dom[($dom[$key]['parent'])]['parent'])]['cols'] = $dom[($dom[$key]['parent'])]['cols'];
						}
						if (($dom[$key]['value'] == "td") OR ($dom[$key]['value'] == "th")) {
							$dom[($dom[$key]['parent'])]['content'] = "";
							for ($i = ($dom[$key]['parent'] + 1); $i < $key; $i++) {
								$dom[($dom[$key]['parent'])]['content'] .= $a[($i-1)];
							}
							$key = $i;
						}
					} else {
						// opening html tag
						$dom[$key]['opening'] = true;
						$dom[$key]['parent'] = end($level);
						if (substr($element, -1, 1) != '/') {
							// not self-closing tag
							array_push($level, $key);
							$dom[$key]['self'] = false;
						} else {
							$dom[$key]['self'] = true;
						}
						// copy some values from parent
						if ($key > 0) {
							$dom[$key]['fontname'] = $dom[($dom[$key]['parent'])]['fontname'];
							$dom[$key]['fontstyle'] = $dom[($dom[$key]['parent'])]['fontstyle'];
							$dom[$key]['fontsize'] = $dom[($dom[$key]['parent'])]['fontsize'];
							$dom[$key]['bgcolor'] = $dom[($dom[$key]['parent'])]['bgcolor'];
							$dom[$key]['fgcolor'] = $dom[($dom[$key]['parent'])]['fgcolor'];
							$dom[$key]['align'] = $dom[($dom[$key]['parent'])]['align'];
						}
						// get attributes
						preg_match_all('/([^=\s]*)=["\']?([^"\']*)["\']?/', $element, $attr_array, PREG_PATTERN_ORDER);
						$dom[$key]['attribute'] = array(); // reset attribute array
						while (list($id, $name) = each($attr_array[1])) {
							$dom[$key]['attribute'][strtolower($name)] = $attr_array[2][$id];
						}
						// split style attributes
						if (isset($dom[$key]['attribute']['style'])) {
							// get style attributes
							preg_match_all('/([^:\s]*):([^;]*)/', $dom[$key]['attribute']['style'], $style_array, PREG_PATTERN_ORDER);
							$dom[$key]['style'] = array(); // reset style attribute array
							while (list($id, $name) = each($style_array[1])) {
								$dom[$key]['style'][strtolower($name)] = $style_array[2][$id];
							}
							// --- get some style attributes ---
							if (isset($dom[$key]['style']['font-family'])) {
								// font family
								if (isset($dom[$key]['style']['font-family'])) {
									$fontslist = split(",", strtolower($dom[$key]['style']['font-family']));
									foreach($fontslist as $font) {
										$font = trim(strtolower($font));
										if (in_array($font, $this->fontlist)){
											$dom[$key]['fontname'] = $font;
											break;
										}
									}
								}
							}
							// font size
							if (isset($dom[$key]['style']['font-size'])) {
								$dom[$key]['fontsize'] = intval($dom[$key]['style']['font-size']);
							}
							// font style
							$dom[$key]['fontstyle'] = "";
							if (isset($dom[$key]['style']['font-weight']) AND (strtolower($dom[$key]['style']['font-weight']{0}) == "b")) {
								$dom[$key]['fontstyle'] .= "B";
							}
							if (isset($dom[$key]['style']['font-style']) AND (strtolower($dom[$key]['style']['font-style']{0}) == "i")) {
								$dom[$key]['fontstyle'] .= "I";
							}
							// check for width attribute
							if (isset($dom[$key]['style']['width'])) {
								$dom[$key]['width'] = intval($dom[$key]['style']['width']);
							}
							// check for height attribute
							if (isset($dom[$key]['style']['height'])) {
								$dom[$key]['height'] = intval($dom[$key]['style']['height']);
							}
							// check for text alignment
							if (isset($dom[$key]['style']['text-align'])) {
								$dom[$key]['align'] = strtoupper($dom[$key]['style']['text-align']{0});
							}
						}
						// check for font tag
						if ($dom[$key]['value'] == "font") {
							// font family
							if (isset($dom[$key]['attribute']['face'])) {
								$fontslist = split(",", strtolower($dom[$key]['attribute']['face']));
								foreach($fontslist as $font) {
									$font = trim(strtolower($font));
									if (in_array($font, $this->fontlist)){
										$dom[$key]['fontname'] = $font;
										break;
									}
								}
							}
							// font size
							if (isset($dom[$key]['attribute']['size'])) {
								if ($key > 0) {
									if ($dom[$key]['attribute']['size']{0} == "+") {
										$dom[$key]['fontsize'] = $dom[($dom[$key]['parent'])]['fontsize'] + intval(substr($dom[$key]['attribute']['size'], 1));
									} elseif ($dom[$key]['attribute']['size']{0} == "-") {
										$dom[$key]['fontsize'] = $dom[($dom[$key]['parent'])]['fontsize'] - intval(substr($dom[$key]['attribute']['size'], 1));
									} else {
										$dom[$key]['fontsize'] = intval($dom[$key]['attribute']['size']);
									}
								} else {
									$dom[$key]['fontsize'] = intval($dom[$key]['attribute']['size']);
								}
							}
						}
						if (($dom[$key]['value'] == "ul") OR ($dom[$key]['value'] == "ol") OR ($dom[$key]['value'] == "dl")) {
							// force natural alignment for lists
							if ($this->rtl) {
								$dom[$key]['align'] = "R";
							} else {
								$dom[$key]['align'] = "L";
							}
						}
						if (($dom[$key]['value'] == "small") OR ($dom[$key]['value'] == "sup") OR ($dom[$key]['value'] == "sub")) {
							$dom[$key]['fontsize'] = $dom[$key]['fontsize'] * K_SMALL_RATIO;
						}
						if (($dom[$key]['value'] == "strong") OR ($dom[$key]['value'] == "b")) {
							$dom[$key]['fontstyle'] .= "B";
						}
						if (($dom[$key]['value'] == "em") OR ($dom[$key]['value'] == "i")) {
							$dom[$key]['fontstyle'] .= "I";
						}
						if (($dom[$key]['value']{0} == "h") AND (intval($dom[$key]['value']{1}) > 0) AND (intval($dom[$key]['value']{1}) < 7)) {
							$headsize = (4 - intval($dom[$key]['value']{1})) * 2;
							$dom[$key]['fontsize'] = $dom[0]['fontsize'] + $headsize;
							$dom[$key]['fontstyle'] .= "B";
						}
						if (($dom[$key]['value'] == "table")) {
							$dom[$key]['rows'] = 0; // number of rows
							$dom[$key]['trids'] = array(); // IDs of TR elements
						}
						if (($dom[$key]['value'] == "tr")) {
							$dom[$key]['cols'] = 0;
							// store the number of rows on table element
							$dom[($dom[$key]['parent'])]['rows']++;
							// store the TR elements IDs on table element
							array_push($dom[($dom[$key]['parent'])]['trids'], $key);
						}
						if (($dom[$key]['value'] == "th") OR ($dom[$key]['value'] == "td")) {
							if (isset($dom[$key]['attribute']['colspan'])) {
								$colspan = intval($dom[$key]['attribute']['colspan']);
							} else {
								$colspan = 1;
							}
							$dom[$key]['attribute']['colspan'] = $colspan;
							$dom[($dom[$key]['parent'])]['cols'] += $colspan;
						}
						// set foreground color attribute
						if (isset($dom[$key]['attribute']['color']) AND (!empty($dom[$key]['attribute']['color']))) {
							$dom[$key]['fgcolor'] = $this->convertHTMLColorToDec($dom[$key]['attribute']['color']);
						}
						// set background color attribute
						if (isset($dom[$key]['attribute']['bgcolor']) AND (!empty($dom[$key]['attribute']['bgcolor']))) {
							$dom[$key]['bgcolor'] = $this->convertHTMLColorToDec($dom[$key]['attribute']['bgcolor']);
						}
						// check for width attribute
						if (isset($dom[$key]['attribute']['width'])) {
							$dom[$key]['width'] = intval($dom[$key]['attribute']['width']);
						}
						// check for height attribute
						if (isset($dom[$key]['attribute']['height'])) {
							$dom[$key]['height'] = intval($dom[$key]['attribute']['height']);
						}
						// check for text alignment
						if (isset($dom[$key]['attribute']['align']) AND (!empty($dom[$key]['attribute']['align'])) AND ($dom[$key]['value'] !== 'img')) {
							$dom[$key]['align'] = strtoupper($dom[$key]['attribute']['align']{0});
						}
					} // end opening tag
				} else {
					// text
					$dom[$key]['tag'] = false;
					$dom[$key]['value'] = stripslashes($this->unhtmlentities($element));
					$dom[$key]['parent'] = end($level);
					// calculate text width
					//$dom[$key]['width'] = $this->GetStringWidth($dom[$key]['value'], $dom[($dom[$key]['parent'])]['fontname'], $dom[($dom[$key]['parent'])]['fontstyle'], $dom[($dom[$key]['parent'])]['fontsize']);	
				}
				$key++;
			}
			return $dom;
		}
		
		/**
		 * Allows to preserve some HTML formatting (limited support).<br />
		 * Supported tags are: a, b, blockquote, br, dd, del, div, dl, dt, em, font, h1, h2, h3, h4, h5, h6, hr, i, img, li, ol, p, small, span, strong, sub, sup, table, td, th, tr, u, ul, 
		 * @param string $html text to display
		 * @param boolean $ln if true add a new line after text (default = true)
		 * @param int $fill Indicates if the background must be painted (true) or transparent (false).
		 * @param boolean $reseth if true reset the last cell height (default false).
		 * @param boolean $cell if true add the default cMargin space to each Write (default false).
		 * @param string $align Allows to center or align the text. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		 */
		public function writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='') {
			// store current values
			$prevlMargin = $this->lMargin;
			$prevrMargin = $this->rMargin;
			$prevcMargin = $this->cMargin;
			$prevFontFamily = $this->FontFamily;
			$prevFontStyle = $this->FontStyle;
			$prevFontSizePt = $this->FontSizePt;
			$curfontname = $prevFontFamily;
			$curfontstyle = $prevFontStyle;
			$curfontsize = $prevFontSizePt;
			$prevbgcolor = $this->bgcolor;
			$prevfgcolor = $this->fgcolor;
			$this->newline = true;
			$startlinepage = $this->page;
			if (isset($this->footerpos[$this->page])) {
				$this->footerpos[$this->page] = strlen($this->pages[$this->page]) - $this->footerlen[$this->page];
				$startlinepos = $this->footerpos[$this->page];
			} else {
				$startlinepos = strlen($this->pages[$this->page]);
			}
			$lalign = $align;
			$plalign = $align;
			if ($this->rtl) {
				$w = $this->x - $this->lMargin;
			} else {
				$w = $this->w - $this->rMargin - $this->x;
			}
			$w -= (2 * $this->cMargin);
			if ($cell) {
				if ($this->rtl) {
					$this->x -= $this->cMargin;
				} else {
					$this->x += $this->cMargin;
				}
			}
			$this->listindent = $this->GetStringWidth("0000");
			$this->listnum = 0;
			if ((empty($this->lasth))OR ($reseth)) {
				//set row height
				$this->lasth = $this->FontSize * $this->cell_height_ratio; 
			}
			$dom = $this->getHtmlDomArray($html);
			$maxel = count($dom);
			$key = 0;
			while ($key < $maxel) {				
				if ($dom[$key]['tag'] OR ($key == 0)) {
					if (isset($dom[$key]['fontname']) OR isset($dom[$key]['fontstyle']) OR isset($dom[$key]['fontsize'])) {
						$fontname = isset($dom[$key]['fontname']) ? $dom[$key]['fontname'] : '';
						$fontstyle = isset($dom[$key]['fontstyle']) ? $dom[$key]['fontstyle'] : '';
						$fontsize = isset($dom[$key]['fontsize']) ? $dom[$key]['fontsize'] : '';
						if (($fontname != $curfontname) OR ($fontstyle != $curfontstyle) OR ($fontsize != $curfontsize)) {
							$this->SetFont($fontname, $fontstyle, $fontsize);
							$this->lasth = $this->FontSize * $this->cell_height_ratio;
							$curfontname = $fontname;
							$curfontstyle = $fontstyle;
							$curfontsize = $fontsize;
						}
					}
					if (isset($dom[$key]['bgcolor']) AND ($dom[$key]['bgcolor'] !== false)) {
						$this->SetFillColorArray($dom[$key]['bgcolor']);
						$wfill = true;
					} else {
						$wfill = $fill | false;
					}
					if (isset($dom[$key]['fgcolor']) AND ($dom[$key]['fgcolor'] !== false)) {
						$this->SetTextColorArray($dom[$key]['fgcolor']);
					}
					if (isset($dom[$key]['align'])) {
						$lalign = $dom[$key]['align'];
					}
					if (empty($lalign)) {
						$lalign = $align;
					}
				}
				// align lines
				if ($this->newline AND (strlen($dom[$key]['value']) > 0) AND ($dom[$key]['value'] != 'td') AND ($dom[$key]['value'] != 'th')) {
					// we are at the beginning of a new line
					if (isset($startlinex)) {
						if (isset($plalign) AND ((($plalign == "C") OR (($plalign == "R") AND (!$this->rtl)) OR (($plalign == "L") AND ($this->rtl))))) {
							// the last line must be shifted to be aligned as requested
							$linew = abs($this->endlinex - $startlinex);
							$pstart = substr($this->pages[$startlinepage], 0, $startlinepos);
							if (isset($opentagpos) AND isset($this->footerpos[$startlinepage])) {
								$this->footerpos[$startlinepage] = strlen($this->pages[$startlinepage]) - $this->footerlen[$startlinepage];
								$midpos = min($opentagpos, $this->footerpos[$startlinepage]);
							} elseif (isset($opentagpos)) {
								$midpos = $opentagpos;
							} elseif (isset($this->footerpos[$startlinepage])) {
								$this->footerpos[$startlinepage] = strlen($this->pages[$startlinepage]) - $this->footerlen[$startlinepage];
								$midpos = $this->footerpos[$startlinepage];
							} else {
								$midpos = 0;
							}
							if ($midpos > 0) {
								$pmid = substr($this->pages[$startlinepage], $startlinepos, ($midpos - $startlinepos));
								$pend = substr($this->pages[$startlinepage], $midpos);
							} else {
								$pmid = substr($this->pages[$startlinepage], $startlinepos);
								$pend = "";
							}
							// calculate shifting amount
							$mdiff = abs($w - $linew);
							if ($plalign == "C") {
								if ($this->rtl) {
									$t_x = -($mdiff / 2);
								} else {
									$t_x = ($mdiff / 2);
								}
							}	elseif (($plalign == "R") AND (!$this->rtl)) {
								// right alignment on LTR document
								$t_x = $mdiff;
							}	elseif (($plalign == "L") AND ($this->rtl)) {
								// left alignment on RTL document
								$t_x = -$mdiff;
							}
							// shift the line
							$trx = sprintf('1 0 0 1 %.3f 0 cm', ($t_x * $this->k));
							$this->pages[$startlinepage] = $pstart."\nq\n".$trx."\n".$pmid."\nQ\n".$pend;
							$endlinepos = strlen($pstart."\nq\n".$trx."\n".$pmid."\nQ\n");
						}
					}
					$this->checkPageBreak($this->lasth);
					$this->SetFont($fontname, $fontstyle, $fontsize);
					if ($wfill) {
						$this->SetFillColorArray($this->bgcolor);
					}
					$startlinex = $this->x;
					$startlinepage = $this->page;
					if (isset($endlinepos)) {
						$startlinepos = $endlinepos;
						unset($endlinepos);
					} else {
						if (isset($this->footerpos[$this->page])) {
							$this->footerpos[$this->page] = strlen($this->pages[$this->page]) - $this->footerlen[$this->page];
							$startlinepos = $this->footerpos[$this->page];
						} else {
							$startlinepos = strlen($this->pages[$this->page]);
						}
					}
					$plalign = $lalign;
					$this->newline = false;
				}
				if (isset($opentagpos)) {
					unset($opentagpos);
				}
				if ($dom[$key]['tag']) {
					if ($dom[$key]['opening']) {	
						// table content is handled in a special way
						if (($dom[$key]['value'] == "td") OR ($dom[$key]['value'] == "th")) {
							$trid = $dom[$key]['parent'];
							$table_el = $dom[$trid]['parent'];
							if (!isset($dom[$table_el]['cols'])) {
								$dom[$table_el]['cols'] = $trid['cols'];
							}
							// calculate cell width
							if (isset($dom[($dom[$key]['parent'])]['width'])) {
								$table_width = $this->pixelsToUnits($dom[($dom[$key]['parent'])]['width']);
							} else {
								$table_width = $w;
							}
							if (isset($dom[($dom[$trid]['parent'])]['attribute']['cellpadding'])) {
								$currentcmargin = $this->pixelsToUnits($dom[($dom[$trid]['parent'])]['attribute']['cellpadding']);
								$this->cMargin = $currentcmargin;
							} else {
								$currentcmargin = 0;
							}
							if (isset($dom[($dom[$trid]['parent'])]['attribute']['cellspacing'])) {
								$cellspacing = $this->pixelsToUnits($dom[($dom[$trid]['parent'])]['attribute']['cellspacing']);
							} else {
								$cellspacing = 0;
							}
							if ($this->rtl) {
								$cellspacingx = -$cellspacing;
							} else {
								$cellspacingx = $cellspacing;
							}
							$colspan = $dom[$key]['attribute']['colspan'];
							if (isset($dom[$key]['width'])) {
								$cellw = $this->pixelsToUnits($dom[$key]['width']);
							} else {
								$cellw = ($colspan * ($table_width / $dom[$table_el]['cols']));
							}
							$cellw -= $cellspacing;
							$cell_content = $dom[$key]['content'];
							$tagtype = $dom[$key]['value'];
							$parentid = $key;
							while (($key < $maxel) AND (!(($dom[$key]['tag']) AND (!$dom[$key]['opening']) AND ($dom[$key]['value'] == $tagtype) AND ($dom[$key]['parent'] == $parentid)))) {
								// move $key index forward
								$key++;
							}
							if (!isset($dom[$trid]['startpage'])) {
								$dom[$trid]['startpage'] = $this->page;
							} else {
								$this->setPage($dom[$trid]['startpage']);
							}
							if (!isset($dom[$trid]['starty'])) {
								$dom[$trid]['starty'] = $this->y;
							} else {
								$this->y = $dom[$trid]['starty'];
							}
							if (!isset($dom[$trid]['startx'])) {
								$dom[$trid]['startx'] = $this->x;
							}
							$this->x += ($cellspacingx / 2);
							if (isset($dom[$parentid]['attribute']['rowspan'])) {
								$rowspan = intval($dom[$parentid]['attribute']['rowspan']);
							}	else {
								$rowspan = 1;
							}
							// skip row-spanned cells started on the previous rows
							if (isset($dom[$table_el]['rowspans'])) {
								foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
									if  (($trwsp['startx'] == $this->x) AND (($trwsp['starty'] < $this->y) OR ($trwsp['startpage'] < $this->page)) AND ($trwsp['rowspan'] > 0)) {
										$this->x = $trwsp['endx'] + $cellspacingx;
									}
								}
							}
							// add rowspan information to table element
							if ($rowspan > 1) {
								if (isset($this->footerpos[$this->page])) {
									$this->footerpos[$this->page] = strlen($this->pages[$this->page]) - $this->footerlen[$this->page];
									$trintmrkpos = $this->footerpos[$this->page];
								} else {
									$trintmrkpos = strlen($this->pages[$this->page]);
								}
								$trsid = array_push($dom[$table_el]['rowspans'], array('rowspan' => $rowspan, 'colspan' => $colspan, 'startpage' => $this->page, 'startx' => $this->x, 'starty' => $this->y, 'intmrkpos' => $trintmrkpos));
							}
							$cellid = array_push($dom[$trid]['cellpos'], array('startx' => $this->x));
							if ($rowspan > 1) {
								$dom[$trid]['cellpos'][($cellid - 1)]['rowspanid'] = ($trsid - 1);
							}
							// push background colors
							if (isset($dom[$parentid]['bgcolor']) AND ($dom[$parentid]['bgcolor'] !== false)) {
								$dom[$trid]['cellpos'][($cellid - 1)]['bgcolor'] = $dom[$parentid]['bgcolor'];
							}
							
							// write the cell content
							$this->MultiCell($cellw, 0, $cell_content, false, $lalign, false, 2, '', '', true, 0, true);
							
							$this->cMargin = $currentcmargin;
							$dom[$trid]['cellpos'][($cellid - 1)]['endx'] = $this->x;
							// update the end of row position
							if (isset($dom[$trid]['endy'])) {
								if ($this->page == $dom[$trid]['endpage']) {
									$dom[$trid]['endy'] = max($this->y, $dom[$trid]['endy']);
								} elseif ($this->page > $dom[$trid]['endpage']) {
									$dom[$trid]['endy'] = $this->y;
								}
							} else {
								$dom[$trid]['endy'] = $this->y;
							}
							if (isset($dom[$trid]['endpage'])) {
								$dom[$trid]['endpage'] = max($this->page, $dom[$trid]['endpage']);
							} else {
								$dom[$trid]['endpage'] = $this->page;
							}
							// account for row-spanned cells
							if ($rowspan > 1) {
								$dom[$table_el]['rowspans'][($trsid - 1)]['endx'] = $this->x;
							}
							if (isset($dom[$table_el]['rowspans'])) {
								foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
									if ($trwsp['rowspan'] > 0) {
										$dom[$table_el]['rowspans'][$k]['endy'] = $dom[$trid]['endy'];
										$dom[$table_el]['rowspans'][$k]['endpage'] = $dom[$trid]['endpage'];
									}
								}
							}
							$this->x += ($cellspacingx / 2);
						} else {
							// opening tag (or self-closing tag)
							if (!isset($opentagpos)) {
								if (isset($this->footerpos[$this->page])) {
									$this->footerpos[$this->page] = strlen($this->pages[$this->page]) - $this->footerlen[$this->page];
									$opentagpos = $this->footerpos[$this->page];
								} else {
									$opentagpos = strlen($this->pages[$this->page]);
								}
							}
							$this->openHTMLTagHandler($dom, $key, $cell);
						}
					} else {
						// closing tag
						$this->closeHTMLTagHandler($dom, $key, $cell);
					}
				} elseif (strlen($dom[$key]['value']) > 0) {
					// text
					if ($this->HREF) {
						// HTML <a> Link
						$strrest = $this->addHtmlLink($this->HREF, $dom[$key]['value'], $wfill, true);
					} else {
						$ctmpmargin = $this->cMargin;
						$this->cMargin = 0;
						// write only the first line and get the rest
						$strrest = $this->Write($this->lasth, $dom[$key]['value'], '', $wfill, "", false, 0, true);
						$this->cMargin = $ctmpmargin;
					}
					if (strlen($strrest) > 0) {
						// store the remaining string on the previous $key position
						$this->newline = true;
						if ($cell) {
							if ($this->rtl) {
								$this->x -= $this->cMargin;
							} else {
								$this->x += $this->cMargin;
							}
						}
						$dom[$key]['value'] = ltrim($strrest);
						$key--;
					}
				}
				$key++;
			} // end for each $key
			// align the last line
			if (isset($startlinex)) {
				if (isset($plalign) AND ((($plalign == "C") OR (($plalign == "R") AND (!$this->rtl)) OR (($plalign == "L") AND ($this->rtl))))) {
					// the last line must be shifted to be aligned as requested
					$linew = abs($this->endlinex - $startlinex);
					$pstart = substr($this->pages[$startlinepage], 0, $startlinepos);
					if (isset($opentagpos) AND isset($this->footerpos[$startlinepage])) {
						$this->footerpos[$startlinepage] = strlen($this->pages[$startlinepage]) - $this->footerlen[$startlinepage];
						$midpos = min($opentagpos, $this->footerpos[$startlinepage]);
					} elseif (isset($opentagpos)) {
						$midpos = $opentagpos;
					} elseif (isset($this->footerpos[$startlinepage])) {
						$this->footerpos[$startlinepage] = strlen($this->pages[$startlinepage]) - $this->footerlen[$startlinepage];
						$midpos = $this->footerpos[$startlinepage];
					} else {
						$midpos = 0;
					}
					if ($midpos > 0) {
						$pmid = substr($this->pages[$startlinepage], $startlinepos, ($midpos - $startlinepos));
						$pend = substr($this->pages[$startlinepage], $midpos);
					} else {
						$pmid = substr($this->pages[$startlinepage], $startlinepos);
						$pend = "";
					}
					// calculate shifting amount
					$mdiff = abs($w - $linew);
					if ($plalign == "C") {
						if ($this->rtl) {
							$t_x = -($mdiff / 2);
						} else {
							$t_x = ($mdiff / 2);
						}
					}	elseif (($plalign == "R") AND (!$this->rtl)) {
						// right alignment on LTR document
						$t_x = $mdiff;
					}	elseif (($plalign == "L") AND ($this->rtl)) {
						// left alignment on RTL document
						$t_x = -$mdiff;
					}
					// shift the line
					$trx = sprintf('1 0 0 1 %.3f 0 cm', ($t_x * $this->k));
					$this->pages[$startlinepage] = $pstart."\nq\n".$trx."\n".$pmid."\nQ\n".$pend;
				}
			}
			if ($ln AND (!($cell AND ($dom[$key-1]['value'] == "table")))) {
				$this->Ln($this->lasth);
			}
			// restore previous values
			$this->SetFont($prevFontFamily, $prevFontStyle, $prevFontSizePt);
			$this->SetFillColorArray($prevbgcolor);
			$this->SetTextColorArray($prevfgcolor);
			$this->lMargin = $prevlMargin;
			$this->rMargin = $prevrMargin;
			$this->cMargin = $prevcMargin;
			unset($dom);
		}
		
		/**
		 * Process opening tags.
		 * @param array $dom html dom array 
		 * @param int $key current element id
		 * @param boolean $cell if true add the default cMargin space to each new line (default false).
		 * @access protected
		 */
		protected function openHTMLTagHandler(&$dom, $key, $cell=false) {
			$tag = $dom[$key];
			$parent = $dom[($dom[$key]['parent'])];
			// check for text direction attribute
			if (isset($tag['attribute']['dir'])) {
				$this->tmprtl = $tag['attribute']['dir'] == 'rtl' ? 'R' : 'L';
			} else {
				$this->tmprtl = false;
			}
			//Opening tag
			switch($tag['value']) {
				case 'table': {
					$dom[$key]['rowspans'] = array();
					if (isset($tag['attribute']['cellpadding'])) {
						$this->oldcMargin = $this->cMargin;
						$this->cMargin = $this->pixelsToUnits($tag['attribute']['cellpadding']);
					}
					break;
				}
				case 'tr': {
					// array of columns positions
					$dom[$key]['cellpos'] = array();
					break;
				}
				case 'td':
				case 'th': {
					break;
				}
				case 'hr': {
					$this->Ln('', $cell);
					if ((isset($tag['attribute']['width'])) AND ($tag['attribute']['width'] != '')) {
						$hrWidth = $this->pixelsToUnits($tag['attribute']['width']);
					} else {
						$hrWidth = $this->w - $this->lMargin - $this->rMargin;
					}
					$x = $this->GetX();
					$y = $this->GetY();
					$prevlinewidth = $this->GetLineWidth();
					$this->Line($x, $y, $x + $hrWidth, $y);
					$this->SetLineWidth($prevlinewidth);
					$this->Ln('', $cell);
					break;
				}
				case 'u': {
					$this->setStyle('u', true);
					break;
				}
				case 'del': {
					$this->setStyle('d', true);
					break;
				}
				case 'a': {
					$this->HREF = $tag['attribute']['href'];
					break;
				}
				case 'img': {
					if (isset($tag['attribute']['src'])) {
						// replace relative path with real server path
						if ($tag['attribute']['src'][0] == '/') {
							$tag['attribute']['src'] = $_SERVER['DOCUMENT_ROOT'].$tag['attribute']['src'];
						}
						$tag['attribute']['src'] = str_replace(K_PATH_URL, K_PATH_MAIN, $tag['attribute']['src']);
						if (!isset($tag['attribute']['width'])) {
							$tag['attribute']['width'] = 0;
						}
						if (!isset($tag['attribute']['height'])) {
							$tag['attribute']['height'] = 0;
						}
						if (!isset($tag['attribute']['align'])) {
							$align = 'N';
						} else {
							switch($tag['attribute']['align']) {
								case 'top':{
									$align = 'T';
									break;
								}
								case 'middle':{
									$align = 'M';
									break;
								}
								case 'bottom':{
									$align = 'B';
									break;
								}
								default:{
									$align = 'N';
									break;
								}
							}
						}
						$fileinfo = pathinfo($tag['attribute']['src']);
						if (isset($fileinfo['extension']) AND (!empty($fileinfo['extension']))) {
							$type = strtolower($fileinfo['extension']);
						}
						if (($type == "eps") OR ($type == "ai")) {
							$this->ImageEps($tag['attribute']['src'], $this->GetX(), $this->GetY(), $this->pixelsToUnits($tag['attribute']['width']), $this->pixelsToUnits($tag['attribute']['height']), '', true, $align);
						} else {
							$this->Image($tag['attribute']['src'], $this->GetX(), $this->GetY(), $this->pixelsToUnits($tag['attribute']['width']), $this->pixelsToUnits($tag['attribute']['height']), '', '', $align);
						}
					}
					break;
				}
				case 'dl': {
					$this->listnum++;
					break;
				}
				case 'dt': {
					$this->Ln('', $cell);
					break;
				}
				case 'dd': {
					if ($this->rtl) {
						$this->rMargin += $this->listindent;
					} else {
						$this->lMargin += $this->listindent;
					}
					$this->Ln('', $cell);
					break;
				}
				case 'ul':
				case 'ol': {
					$this->listnum++;
					if ($tag['value'] == "ol") {
						$this->listordered[$this->listnum] = true;
					} else {
						$this->listordered[$this->listnum] = false;
					}
					$this->listcount[$this->listnum] = 0;
					if ($this->rtl) {
						$this->rMargin += $this->listindent;
					} else {
						$this->lMargin += $this->listindent;
					}
					break;
				}
				case 'li': {
					$this->Ln('', $cell);
					if ($tag['value'] == 'li') {
						if ($this->listordered[$this->listnum]) {
							if (isset($tag['attribute']['value'])) {
								$this->listcount[$this->listnum] = intval($tag['attribute']['value']);
							}
							$this->listcount[$this->listnum]++;
							if ($this->rtl) {
								$this->lispacer = ".".($this->listcount[$this->listnum]);
							} else {
								$this->lispacer = ($this->listcount[$this->listnum]).".";
							}
						} else {
							//unordered list symbol
							$this->lispacer = "-";
						}
					} else {
						$this->lispacer = "";
					}
					$tmpx = $this->x;
					$lspace = $this->GetStringWidth($this->lispacer."  ");
					if ($this->rtl) {
						$this->x += $lspace;
					} else {
						$this->x -= $lspace;
					}
					$this->Write($this->lasth, $this->lispacer, '', false, '', false, 0, false);
					$this->x = $tmpx;
					break;
				}
				case 'blockquote':
				case 'br': {
					$this->Ln('', $cell);
					break;
				}
				case 'p': {
					$this->Ln('', $cell);
					$this->Ln('', $cell);
					break;
				}
				case 'sup': {
					$this->SetXY($this->GetX(), $this->GetY() - (($parent['fontsize'] - $this->FontSizePt) / $this->k));
					break;
				}
				case 'sub': {
					$this->SetXY($this->GetX(), $this->GetY() + (($parent['fontsize'] - (0.5 * $this->FontSizePt)) / $this->k));
					break;
				}
				case 'small': {
					$this->SetXY($this->GetX(), $this->GetY() + (($parent['fontsize'] - $this->FontSizePt)/$this->k));
					break;
				}
				case 'h1': 
				case 'h2': 
				case 'h3': 
				case 'h4': 
				case 'h5': 
				case 'h6': {
					$this->Ln(($tag['fontsize'] * 1.5) / $this->k, $cell);
					break;
				}
				default: {
					break;
				}
			}
		}
		
		/**
		 * Process closing tags.
		 * @param array $dom html dom array 
		 * @param int $key current element id
		 * @param boolean $cell if true add the default cMargin space to each new line (default false).
		 * @access protected
		 */
		protected function closeHTMLTagHandler(&$dom, $key, $cell=false) {
			$tag = $dom[$key];
			$parent = $dom[($dom[$key]['parent'])];
			//Closing tag
			switch($tag['value']) {
				case 'td':
				case 'th': {
					break;
				}
				case 'tr': {
					$table_el = $dom[($dom[$key]['parent'])]['parent'];
					$this->setPage($parent['endpage']);
					$this->y = $parent['endy'];
					if (isset($dom[$table_el]['attribute']['cellspacing'])) {
						$cellspacing = $this->pixelsToUnits($dom[$table_el]['attribute']['cellspacing']);
						$this->y += $cellspacing;
					}				
					$this->Ln(0, $cell);
					$this->x = $parent['startx'];
					// update row-spanned cells
					if (isset($dom[$table_el]['rowspans'])) {
						foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
								$dom[$table_el]['rowspans'][$k]['rowspan'] -= 1;
						}
					}
					break;
				}
				case 'table': {
					// draw borders
					$table_el = $parent;
					if ((isset($table_el['attribute']['border']) AND ($table_el['attribute']['border'] > 0)) 
						OR (isset($table_el['style']['border']) AND ($table_el['style']['border'] > 0))) {
							$border = 1;
					} else {
						$border = 0;
					}
					// for each row
					foreach ($table_el['trids'] as $j => $trkey) {
						$parent = $dom[$trkey];
						$this->setPage($parent['startpage']);
						$this->y = $parent['starty'];
						$restspace = $this->getPageHeight() - $this->y - $this->getBreakMargin();
						$startpage = $parent['startpage'];
						$endpage = $parent['endpage'];
						// for each cell on the row
						foreach ($parent['cellpos'] as $k => $cellpos) {
							if (isset($cellpos['rowspanid'])) {
								$cellpos['startx'] = $table_el['rowspans'][($cellpos['rowspanid'])]['startx'];
								$cellpos['endx'] = $table_el['rowspans'][($cellpos['rowspanid'])]['endx'];
								$endy = $table_el['rowspans'][($cellpos['rowspanid'])]['endy'];
								$startpage = $table_el['rowspans'][($cellpos['rowspanid'])]['startpage'];
								$endpage = $table_el['rowspans'][($cellpos['rowspanid'])]['endpage'];
							} else {
								$endy = $parent['endy'];
							}
							if ($endpage > $startpage) {
								// design borders around HTML cells.
								for ($page=$startpage; $page <= $endpage; $page++) {
									$this->setPage($page);
									if ($page == $startpage) {
										$this->y = $this->getPageHeight() - $restspace - $this->getBreakMargin();
										$ch = $restspace;
									} elseif ($page == $endpage) {
										$this->y = $this->tMargin; // put cursor at the beginning of text
										$ch = $endy - $this->tMargin;
									} else {
										$this->y = $this->tMargin; // put cursor at the beginning of text
										$ch = $this->getPageHeight() - $this->tMargin - $this->getBreakMargin();
									}
	
									if (isset($cellpos['bgcolor']) AND ($cellpos['bgcolor']) !== false) {
										$this->SetFillColorArray($cellpos['bgcolor']);
										$fill = true;
									} else {
										$fill = false;
									}
									$cw = abs($cellpos['endx'] - $cellpos['startx']);
									$this->x = $cellpos['startx'];
									// design a cell around the text
									$ccode = $this->FillColor."\n".$this->getCellCode($cw, $ch, "", $border, 1, '', $fill);
									$pstart = substr($this->pages[$this->page], 0, $this->intmrk[$this->page]);
									$pend = substr($this->pages[$this->page], $this->intmrk[$this->page]);
									$this->pages[$this->page] = $pstart.$ccode."\n".$pend;
									$this->intmrk[$this->page] += strlen($ccode."\n");
								}
							} else {
								$ch = $endy - $parent['starty'];
								if (isset($cellpos['bgcolor']) AND ($cellpos['bgcolor']) !== false) {
									$this->SetFillColorArray($cellpos['bgcolor']);
									$fill = true;
								} else {
									$fill = false;
								}
								$cw = abs($cellpos['endx'] - $cellpos['startx']);
								$this->x = $cellpos['startx'];
								$this->y = $parent['starty'];
								// design a cell around the text
								$ccode = $this->FillColor."\n".$this->getCellCode($cw, $ch, "", $border, 1, '', $fill);
								$pstart = substr($this->pages[$this->page], 0, $this->intmrk[$this->page]);
								$pend = substr($this->pages[$this->page], $this->intmrk[$this->page]);
								$this->pages[$this->page] = $pstart.$ccode."\n".$pend;
								$this->intmrk[$this->page] += strlen($ccode."\n");						
							}
						}					
						if (isset($table_el['attribute']['cellspacing'])) {
							$cellspacing = $this->pixelsToUnits($table_el['attribute']['cellspacing']);
							$this->y += $cellspacing;
						}				
						$this->Ln(0, $cell);
						$this->x = $parent['startx'];
					}
					if (isset($parent['cellpadding'])) {
						$this->cMargin = $this->oldcMargin;
					}
					//set row height
					$this->lasth = $this->FontSize * $this->cell_height_ratio; 
					break;
				}
				case 'u': {
					$this->setStyle('u', false);
					break;
				}
				case 'del': {
					$this->setStyle('d', false);
					break;
				}
				case 'a': {
					$this->HREF = '';
					break;
				}
				case 'sup': {
					$this->SetXY($this->GetX(), $this->GetY() + (($this->FontSizePt - $parent['fontsize'])/$this->k));
					break;
				}
				case 'sub': {
					$this->SetXY($this->GetX(), $this->GetY() - (($this->FontSizePt - (0.5 * $parent['fontsize']))/$this->k));
					break;
				}
				case 'small': {
					$this->SetXY($this->GetX(), $this->GetY() - (($this->FontSizePt - $parent['fontsize'])/$this->k));
					break;
				}
				case 'p': {
					$this->Ln('', $cell);
					$this->Ln('', $cell);
					break;
				}
				case 'dl': {
					$this->listnum--;
					if ($this->listnum <= 0) {
						$this->listnum = 0;
						$this->Ln('', $cell);
						$this->Ln('', $cell);
					}
					break;
				}
				case 'dt': {
					$this->lispacer = "";
					break;
				}
				case 'dd': {
					$this->lispacer = "";
					if ($this->rtl) {
						$this->rMargin -= $this->listindent;
					} else {
						$this->lMargin -= $this->listindent;
					}
					break;
				}
				case 'ul':
				case 'ol': {
					$this->listnum--;
					$this->lispacer = "";
					if ($this->rtl) {
						$this->rMargin -= $this->listindent;
					} else {
						$this->lMargin -= $this->listindent;
					}
					if ($this->listnum <= 0) {
						$this->listnum = 0;
						$this->Ln('', $cell);
						$this->Ln('', $cell);
					}
					$this->lasth = $this->FontSize * $this->cell_height_ratio;
					break;
				}
				case 'li': {
					$this->lispacer = "";
					break;
				}
				case 'h1': 
				case 'h2': 
				case 'h3': 
				case 'h4': 
				case 'h5': 
				case 'h6': {
					$this->Ln(($parent['fontsize'] * 1.5) / $this->k, $cell);
					break;
				}
				default : {
					break;
				}
			}
			$this->tmprtl = false;
		}
	} // END OF TCPDF CLASS
}
//============================================================+
// END OF FILE
//============================================================+
?>