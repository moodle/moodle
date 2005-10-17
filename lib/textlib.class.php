<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// Required files
    require_once('typo3/class.t3lib_cs.php');
    require_once('typo3/class.t3lib_div.php');

/// If ICONV is available, lets Typo3 library use it for convert
    if (extension_loaded('iconv')) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = 'iconv';
    /// Else if mbstring is available, lets Typo3 library use it
    } else if (extension_loaded('mbstring')) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = 'mbstring';
    } else {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_convMethod'] = '';
    }

/// If mbstring is available, lets Typo3 library use it for functions
    if (extension_loaded('mbstring')) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_utils'] = 'mbstring';
    } else {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['t3lib_cs_utils'] = '';
    /// And this directoy must exist to allow Typo to cache conversion 
    /// tables when using internal functions
        make_upload_directory('temp/typo3temp/cs');
    }

/// Default mask for Typo
    $GLOBALS['TYPO3_CONF_VARS']['BE']['fileCreateMask'] = $CFG->directorypermissions;

/// This full path constants must be defined too, transforming backslashes
/// to forward slashed beacuse Typo3 requires it.
    define ('PATH_t3lib', str_replace('\\','/',$CFG->dirroot.'/testutf/typo3/'));
    define ('PATH_typo3', str_replace('\\','/',$CFG->dirroot.'/testutf/typo3/'));
    define ('PATH_site', str_replace('\\','/',$CFG->dataroot.'/temp/'));
    define ('TYPO3_OS', stristr(PHP_OS,'win')&&!stristr(PHP_OS,'darwin')?'WIN':'');


/// As we implement the singleton pattern to use this class (only one instance
/// is shared globally), we need this helper function

function textlib_get_instance () {
    static $instance;
    if (!is_object($instance)) {
        $instance = new textlib();
    }
    return $instance;
}

/**
* This class is used to manipulate strings under Moodle 1.6 an later. As
* utf-8 text become mandatory a pool of safe functions under this encoding
* become necessary. All of them are static methods that will be called from
* Moodle code with the :: syntax. The name of the methods is exactly the
* same than their PHP originals.
* 
* A big part of this class acts as a wrapper over the Typo3 charset library,
* really a cool group of utilities to handle texts and encoding conversion.
*
* Take a look to its own copyright and license details.
*/
class textlib {

    var $typo3cs;

    /* Standard constructor of the class. All it does is to instantiate
     * a new t3lib_cs object to have all their functions ready.
     *
     * Instead of istantiating a lot of objects of this class everytime
     * some of their functions is going to be used, you can invoke the:
     * textlib_get_instance() function, avoiding the creation of them
     * (following the singleton pattern)
     */
    function textlib() {
        /// Instantiate a conversor object some of the methods in typo3
        /// reference to $this and cannot be executed in a static context
        $this->typo3cs = new t3lib_cs();
    }

    /* Converts the text between different encodings. It will use iconv, mbstring
    * or internal (typo3) methods to try such conversion. Returns false if fails.
    */
    function convert($text, $fromCS, $toCS='utf-8') {
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 conv() function. It will do all the work
        $result = $this->typo3cs->conv($text, $fromCS, $toCS);
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /* Multibyte safe substr() function, uses mbstring if available. */
    function substr($text, $start, $len=null, $charset='utf-8') {
    /// Call Typo3 substr() function. It will do all the work
        return $this->typo3cs->substr($charset,$text,$start,$len);
    }

    /* Multibyte safe strlen() function, uses mbstring if available. */
    function strlen($text, $charset='utf-8') {
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 strlen() function. It will do all the work
        $result = $this->typo3cs->strlen($charset,$text);
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /* Multibyte safe strtolower() function, uses mbstring if available. */
    function strtolower($text, $charset='utf-8') {
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 conv_case() function. It will do all the work
        $result = $this->typo3cs->conv_case($charset,$text,'toLower');
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /* Multibyte safe strtoupper() function, uses mbstring if available. */
    function strtoupper($text, $charset='utf-8') {
    /// Avoid some notices from Typo3 code
        $oldlevel = error_reporting(E_PARSE);
    /// Call Typo3 conv_case() function. It will do all the work
        $result = $this->typo3cs->conv_case($charset,$text,'toUpper');
    /// Restore original debug level
        error_reporting($oldlevel);
        return $result;
    }

    /* UTF-8 ONLY safe strpos() function, uses mbstring if available. */
    function strpos($haystack,$needle,$offset=0) {
    /// Call Typo3 utf8_strpos() function. It will do all the work
        return $this->typo3cs->utf8_strpos($haystack,$needle,$offset);
    }

    /* UTF-8 ONLY safe strrpos() function, uses mbstring if available. */
    function strrpos($haystack,$needle) {
    /// Call Typo3 utf8_strrpos() function. It will do all the work
        return $this->typo3cs->utf8_strrpos($haystack,$needle);
    }

}
?>
