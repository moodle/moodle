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

    global $CFG;

/// Do a quick check using stripos to avoid unnecessary work
    if (stripos($text, '<lang') === false) {
        return $text;
    }

/// Flag this text as something not to cache
    $CFG->currenttextiscacheable = false;

/// Get current language
    $currentlang = current_language();

/// Get parent language
    $langfile = "$CFG->dirroot/lang/$currentlang/moodle.php";
    if ($result = get_string_from_file("parentlanguage", "$langfile", "\$parentlang")) {
        eval($result);
    }

/// Create an array of preffered languages
    $preflangs = array();
    $preflangs[] = $currentlang;     /// First, the current lang
    if (!empty($parentlang)) {
        $preflangs[] = $parentlang; /// Then, if available, the parent lang
    }
    if ($currentlang != 'en') {
        $preflangs[] = 'en';        /// Finally, if not used, add the en lang
    }

/// Break the text into lang sections
    $search = '/<lang lang="([a-zA-Z_]*)".*?>(.+?)<\/lang>/is';
    preg_match_all($search,$text,$list_of_langs);

/// Get the existing sections langs
    $minpref   = count($preflangs);
    $bestkey   = 0;
    //Iterate
    foreach ($list_of_langs[1] as $key => $lang) {
        $foundkey = array_search($lang, $preflangs);
        if ($foundkey !== false && $foundkey !== NULL && $foundkey < $minpref) {
            $minpref = $foundkey;
            $bestkey = $key;
            if ($minpref == 0) {
                continue;        //The best has been found. Leave iteration.
            }
        }
    }

    $text = trim($list_of_langs[2][$bestkey]);

    return $text;
}

?>
