<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This program is part of Moodle - Modular Object-Oriented Dynamic      //
// Learning Environment - http://moodle.com                              //
//                                                                       //
// $Id$ //
//                                                                       //
// Copyright (C) 2004  Gaëtan Frenoy <gaetan à frenoy.net>               //
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
//                                                                       //
//  To activate this filter, add a line like this to your                //
//  list of filters in your Filter configuration:                        //
//                                                                       //
//       filter/multilang/filter.php                                     //
//                                                                       //
// See README.txt for more information about this module                 //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// These lines are important - the variable must match the name 
/// of the actual function below

$textfilter_function = 'multilang_filter';

if (function_exists($textfilter_function)) {
    return;
}


/// Given XML multilinguage text, return relevant text according to
/// current language.  i.e.=
///   - look for lang sections in the code.
///   - if there exists texts in the currently active language, print them.
///   - else, if there exists texts in the current parent language, print them.
///   - else, if there are English texts, print them
///   - else, print everything.
///
/// $text is raw unmodified user text

function multilang_filter($courseid, $text) {

    global $CFG;

/// Make sure XML is enabled in this PHP
    if (!function_exists('xml_parser_create')) {
        return $text;
    }

/// Do a quick check using stripos to avoid unnecessary work
    if (stripos($text, '<lang') === false) {
        return $text;
    }

/// Flag this text as something not to cache
    $CFG->currenttextiscacheable = false;

/// Get current language
    $currentlanguage = current_language();

/// Strip all spaces between tags
    $text = eregi_replace(">"."[[:space:]]+"."<","><", $text);
    $text = eregi_replace("&", "&amp;", $text);

/// Parse XML multilingual file
    $xml = new XMLParser($text);
    $text = $xml->texts['en'];
    foreach ($xml->texts as $lang => $lang_text) {
        if ($lang == $currentlanguage) $text = $lang_text;
    }

    return $text;
}



/// XML Parser for Multilingual text
/// Search for "<LANG>" tags and stores inner xml into $this->texts[lang]
///
class XMLParser { 
  /// Currently parsed language
  var $current_lang = NULL;
  /// Currently parsed format
  var $current_format = NULL;
  /// Currently parsed text
  var $current_text = NULL;
  /// List of parsed texts so far
  var $texts = NULL;

/// Constructor
  function XMLParser($data) {
    /// Init member variables
    $this->current_lang = NULL;
    $this->current_format = NULL;
    $this->current_text = '';
    $this->texts = array();

    /// Default text for default language is input data
    $this->texts['en'] = $data;

    /// Create parser
    $xml_parser = xml_parser_create(); 
    xml_set_object($xml_parser, &$this); 
    xml_set_element_handler($xml_parser, 'startElement', 'endElement'); 
    xml_set_character_data_handler($xml_parser, 'characterData'); 
    /// Parse date (embed data around dummy tag so parser will receive 
    /// a complete XML document
    if (!xml_parse($xml_parser, '<moodle_xml_text_20040116>'.$data.'</moodle_xml_text_20040116>', true))
    {
      /*
      die(sprintf("XML error: %s at line %d",
		  xml_error_string(xml_get_error_code($xml_parser)),
		  xml_get_current_line_number($xml_parser)));
      */
    }
    /// Free resource
    xml_parser_free($xml_parser); 
  }

/// Callback on start of an XML element
  function startElement($parser, $tag, $attributeList) {
    if ($tag == 'LANG' && is_null($this->current_lang))
    {
      // <LANG> tag found, initialise current member vars
      // default language is 'en'
      $this->current_lang = array_key_exists("LANG", $attributeList)?strtolower($attributeList['LANG']):'en';
      // default format is 'auto'
      $this->current_format = array_key_exists('FORMAT', $attributeList)?strtolower($attributeList['FORMAT']):'auto';
      // init inner xml
      $this->current_text = '';
    }
    elseif (!is_null($this->current_lang))
    {
      // If a language has been found already, process tag and attributes
      // and add it to inner xml for current language
      $this->current_text .= "<{$tag}";
      foreach ($attributeList as $key => $val) {
	$this->current_text .= " {$key}=\"{$val}\"";
      }
      $this->current_text .= ">";
    }
    else {
      // This code is outside any <LANG> tag, text is probably not
      // a valid multilingual format
    }
  } 
  
/// Callback on end of an XML element
  function endElement($parser, $tag) { 
    if ($tag == 'LANG' && !is_null($this->current_lang)) {
      // <LANG> tag found while <LANG> tag was already open, 
      // store inner xml and reset context
      $this->texts[$this->current_lang] = $this->current_text;
      $this->current_text = '';
      $this->current_lang = NULL;
      $this->current_format = NULL;
    }
    elseif (!is_null($this->current_lang)) {
      // If a language has been found already, process tag
      // and add it to inner xml for current language
      $this->current_text .= "</{$tag}>";
    }
    else {
      // This code is outside any <LANG> tag, text is probably not
      // a valid multilingual format
    }
  } 
  
/// Callback on character data
  function characterData($parser, $data) {
    // Process inner text and add it to current inner xml
    $this->current_text .= $data;
  } 
} 

?>
