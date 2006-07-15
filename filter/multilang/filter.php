<?php //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This program is part of Moodle - Modular Object-Oriented Dynamic      //
// Learning Environment - http://moodle.org                              //
//                                                                       //
// Copyright (C) 2004  Gaëtan Frenoy <gaetan à frenoy.net>               //
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
//   - else, if there are English texts, print them
//   - else, print the first language in the text.
//
// This is an improved version of the original multilang filter by Gaëtan Frenoy. 
// It should be 100% compatible with the original one. Some new features are:
//   - Supports a new "short" syntax to make things easier. Simply use:
//         <lang lang="XX">
//   - Needs less resources and executes faster.
//   - Allows any type of content to be used. No restrictions at all!

function multilang_filter($courseid, $text) {

/*
/// Do a quick check using stripos to avoid unnecessary work
    if (stripos($text, '<lang') === false && stripos($text, '<span lang') === false) {
        return $text;
    }
*/

    // [pj] I don't know about you but I find this new implementation funny :P
    $search = '/(<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.+?<\/(?:lang|span)>\s*)+/is';
    return preg_replace_callback($search, 'multilang_filter_impl', $text);
}

function multilang_filter_impl($langblock) {

    global $CFG;
    // This callbackis going to get called multiple times, so cache $preflangs
    static $preflangs = NULL;

    /// Flag this text as something not to cache
    $CFG->currenttextiscacheable = false;

    if(empty($preflangs)) {
        /// Get current languages (utf and simple)
        $currentlang = current_language();
        $currentlangsimple = '';
        /// If it's a utf8 lang, use the basic one too
        if (strstr($currentlang, '_utf8') !== false) {
            $currentlangsimple = str_replace('_utf8', '', $currentlang);
        }
    
        /// Get parent language of $currentlang (utf and simple)
        $parentlang = get_string('parentlanguage');
        $parentlangsimple = '';
        if (substr($parentlang, 0, 1) == '[') {
            $parentlang = '';
        }
        /// If it's a utf8 lang, use the basic one too
        if (strstr($parentlang, '_utf8') !== false) {
            $parentlangsimple = str_replace('_utf8', '', $parentlang);
        }

        /// Fill in the array of preffered languages
        $preflangs = array();
        $preflangs[] = $currentlang;     /// First, the current lang
        if (!empty($currentlangsimple)) {
            $preflangs[] = $currentlangsimple; /// The simple (non utf8) lang
        }
        if (!empty($parentlang)) {
            $preflangs[] = $parentlang; /// Then, if available, the parent lang
        }
        if (!empty($parentlangsimple)) {
            $preflangs[] = $parentlangsimple; /// And the simple (non utf8) parent lang
        }
        if ($currentlang != 'en' && $currentlang != 'en_utf8') {
            $preflangs[] = 'en_utf8';        /// Finally, if not used, add the en langs
            $preflangs[] = 'en';
        }
    }
    // Setup is done, now do multilang replacement on the match we 've been called for

    $searchtosplit = '/<(?:lang|span) lang="([a-zA-Z0-9_-]*)".*?>(.+?)<\/(?:lang|span)>/is';
    preg_match_all($searchtosplit, $langblock[0], $langlist);
    /// Get the existing sections langs
    $lang      = '';
    $minpref   = count($preflangs);
    $bestkey   = 0;
    // Define the preference that will be enough if found
    $stoppref = empty($CFG->unicodedb) ? 0 : 1;
    //Iterate
    foreach ($langlist[1] as $key => $lang) {
        //Normalize: Moodle's langs are always lowercase and they use the underscore
        //Should we be stricter?
        $lang = strtolower(str_replace('-','_',$lang));
        $foundkey = array_search($lang, $preflangs);
        if ($foundkey !== false && $foundkey !== NULL && $foundkey < $minpref) {
            $minpref = $foundkey;
            $bestkey = $key;
            if ($minpref <= $stoppref) {
                break;        //The best has been found. Leave iteration.
            }
        }
    }

    return trim($langlist[2][$bestkey]);
}

?>
