<?php //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This program is part of Moodle - Modular Object-Oriented Dynamic      //
// Learning Environment - http://moodle.org                              //
//                                                                       //
// Copyright (C) 2004  Gaetan Frenoy <gaetan@frenoy.net>                 //
//                     Eloy Lafuente <stronk7@moodle.org>                //
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

// Given XML multilinguage text, return relevant text according to
// current language:
//   - look for multilang blocks in the text.
//   - if there exists texts in the currently active language, print them.
//   - else, if there exists texts in the current parent language, print them.
//   - else, print the first language in the text.
// Please note that English texts are not used as default anymore!
//
// This version is based on original multilang filter by Gaetan Frenoy,
// rewritten by Eloy and skodak.
//
// Following new syntax is not compatible with old one:
//   <span lang="XX" class="multilang">one lang</span><span lang="YY" class="multilang">another language</span>

function multilang_filter($courseid, $text) {
    global $CFG;

    // [pj] I don't know about you but I find this new implementation funny :P
    // [skodak] I was laughing while rewriting it ;-)
    // [nicolasconnault] Should support inverted attributes: <span class="multilang" lang="en"> (Doesn't work curently)
    // [skodak] it supports it now, though it is slower - any better idea? 

    if (empty($text) or is_numeric($text)) {
        return $text;
    }

    if (empty($CFG->filter_multilang_force_old) and !empty($CFG->filter_multilang_converted)) {
        // new syntax
        $search = '/(<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>.*?<\/span>)(\s*<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>.*?<\/span>)+/is';
    } else {
        // old syntax
        $search = '/(<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.*?<\/(?:lang|span)>)(\s*<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.*?<\/(?:lang|span)>)+/is';
    }

    $result = preg_replace_callback($search, 'multilang_filter_impl', $text);

    if (is_null($result)) {
        return $text; //error during regex processing (too many nested spans?)
    } else {
        return $result;
    }
}

function multilang_filter_impl($langblock) {
    global $CFG;

    $mylang = str_replace('_utf8', '', current_language());
    static $parentcache;
    if (!isset($parentcache)) {
        $parentcache = array();
    }
    if (!array_key_exists($mylang, $parentcache)) {
        $parentlang = str_replace('_utf8', '', get_string('parentlanguage'));
        $parentcache[$mylang] = $parentlang;
    } else {
        $parentlang = $parentcache[$mylang];
    }

    $searchtosplit = '/<(?:lang|span)[^>]+lang="([a-zA-Z0-9_-]+)"[^>]*>(.*?)<\/(?:lang|span)>/is';

    if (!preg_match_all($searchtosplit, $langblock[0], $rawlanglist)) {
        //skip malformed blocks
        return $langblock[0];
    }

    $langlist = array();
    foreach ($rawlanglist[1] as $index=>$lang) {
        $lang = str_replace('_utf8', '', str_replace('-','_',strtolower($lang))); // normalize languages
        $langlist[$lang] = $rawlanglist[2][$index];
    }

    if (array_key_exists($mylang, $langlist)) {
        return $langlist[$mylang];
    } else if (array_key_exists($parentlang, $langlist)) {
        return $langlist[$parentlang];
    } else {
        $first = array_shift($langlist);
        return $first;
    }
}

?>
