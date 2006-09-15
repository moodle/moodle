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
// current language.  i.e.=
//   - look for lang sections in the code.
//   - if there exists texts in the currently active language, print them.
//   - else, if there exists texts in the current parent language, print them.
//   - else, print the first language in the text.
// Please note that English texts are not used as default anymore!
//
// This is an improved version of the original multilang filter by Gaetan Frenoy. 
// It should be 100% compatible with the original one. Some new features are:
//   - Supports a new "short" syntax to make things easier. Simply use:
//         <span lang="XX">
//   - Needs less resources and executes faster.
//   - Allows any type of content to be used. No restrictions at all!

function multilang_filter($courseid, $text) {

    // [pj] I don't know about you but I find this new implementation funny :P
    // [skodak] I was laughing while rewriting it ;-)
    $search = '/(<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.+?<\/(?:lang|span)>\s*)+/is';
    return preg_replace_callback($search, 'multilang_filter_impl', $text);
}

function multilang_filter_impl($langblock) {
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

    $searchtosplit = '/<(?:lang|span) lang="([a-zA-Z0-9_-]*)".*?>(.+?)<\/(?:lang|span)>/is';
    preg_match_all($searchtosplit, $langblock[0], $rawlanglist);

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
