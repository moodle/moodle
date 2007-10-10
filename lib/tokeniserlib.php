<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Original code:                                                        //
//                                                                       //
// Drupal - The copyright of both the Drupal software and the            //
//          "Druplicon" logo belongs to all the original authors,        //
//          though both are licensed under the GPL.                      //
//          http://drupal.org                                            //
//                                                                       //
// Modifications:                                                        //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//           (C) 2001-3001 Antonio Vicent          http://ludens.es      //
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

/// Based on Drupal's search.module version 1.224
/// http://cvs.drupal.org/viewcvs/drupal/drupal/modules/search/search.module?view=markup

/// Usage: $tokens = tokenise_text($text)
/// Returns an array of tokens (key) with their score (value)
/// (see function definition for more info)

/// Some constants

define ('MINIMUM_WORD_SIZE',  3); /// Minimum word size to index and search
define ('MAXIMUM_WORD_SIZE', 50); /// Maximum word size to index and search

define ('START_DELIM',  "\xce\xa9\xc3\x91\xc3\x91"); // Use these (omega) and (ntilde) utf-8 combinations
define ('CENTER_DELIM', "\xc3\x91\xce\xa9\xc3\x91"); // as safe delimiters (not puncuation nor usual) while
define ('END_DELIM',    "\xc3\x91\xc3\x91\xce\xa9"); // processing numbers ($join_numbers = false)

/**
 * Matches Unicode character classes to exclude from the search index.
 *
 * See: http://www.unicode.org/Public/UNIDATA/UCD.html#General_Category_Values
 *
 * The index only contains the following character classes:
 * Lu         Letter, Uppercase
 * Ll         Letter, Lowercase
 * Lt         Letter, Titlecase
 * Lo         Letter, Other
 * Nd         Number, Decimal Digit
 * No         Number, Other
 */
define('PREG_CLASS_SEARCH_EXCLUDE',
'\x{0}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}-\x{bf}\x{d7}\x{f7}\x{2b0}-'.
'\x{385}\x{387}\x{3f6}\x{482}-\x{489}\x{559}-\x{55f}\x{589}-\x{5c7}\x{5f3}-'.
'\x{61f}\x{640}\x{64b}-\x{65e}\x{66a}-\x{66d}\x{670}\x{6d4}\x{6d6}-\x{6ed}'.
'\x{6fd}\x{6fe}\x{700}-\x{70f}\x{711}\x{730}-\x{74a}\x{7a6}-\x{7b0}\x{901}-'.
'\x{903}\x{93c}\x{93e}-\x{94d}\x{951}-\x{954}\x{962}-\x{965}\x{970}\x{981}-'.
'\x{983}\x{9bc}\x{9be}-\x{9cd}\x{9d7}\x{9e2}\x{9e3}\x{9f2}-\x{a03}\x{a3c}-'.
'\x{a4d}\x{a70}\x{a71}\x{a81}-\x{a83}\x{abc}\x{abe}-\x{acd}\x{ae2}\x{ae3}'.
'\x{af1}-\x{b03}\x{b3c}\x{b3e}-\x{b57}\x{b70}\x{b82}\x{bbe}-\x{bd7}\x{bf0}-'.
'\x{c03}\x{c3e}-\x{c56}\x{c82}\x{c83}\x{cbc}\x{cbe}-\x{cd6}\x{d02}\x{d03}'.
'\x{d3e}-\x{d57}\x{d82}\x{d83}\x{dca}-\x{df4}\x{e31}\x{e34}-\x{e3f}\x{e46}-'.
'\x{e4f}\x{e5a}\x{e5b}\x{eb1}\x{eb4}-\x{ebc}\x{ec6}-\x{ecd}\x{f01}-\x{f1f}'.
'\x{f2a}-\x{f3f}\x{f71}-\x{f87}\x{f90}-\x{fd1}\x{102c}-\x{1039}\x{104a}-'.
'\x{104f}\x{1056}-\x{1059}\x{10fb}\x{10fc}\x{135f}-\x{137c}\x{1390}-\x{1399}'.
'\x{166d}\x{166e}\x{1680}\x{169b}\x{169c}\x{16eb}-\x{16f0}\x{1712}-\x{1714}'.
'\x{1732}-\x{1736}\x{1752}\x{1753}\x{1772}\x{1773}\x{17b4}-\x{17db}\x{17dd}'.
'\x{17f0}-\x{180e}\x{1843}\x{18a9}\x{1920}-\x{1945}\x{19b0}-\x{19c0}\x{19c8}'.
'\x{19c9}\x{19de}-\x{19ff}\x{1a17}-\x{1a1f}\x{1d2c}-\x{1d61}\x{1d78}\x{1d9b}-'.
'\x{1dc3}\x{1fbd}\x{1fbf}-\x{1fc1}\x{1fcd}-\x{1fcf}\x{1fdd}-\x{1fdf}\x{1fed}-'.
'\x{1fef}\x{1ffd}-\x{2070}\x{2074}-\x{207e}\x{2080}-\x{2101}\x{2103}-\x{2106}'.
'\x{2108}\x{2109}\x{2114}\x{2116}-\x{2118}\x{211e}-\x{2123}\x{2125}\x{2127}'.
'\x{2129}\x{212e}\x{2132}\x{213a}\x{213b}\x{2140}-\x{2144}\x{214a}-\x{2b13}'.
'\x{2ce5}-\x{2cff}\x{2d6f}\x{2e00}-\x{3005}\x{3007}-\x{303b}\x{303d}-\x{303f}'.
'\x{3099}-\x{309e}\x{30a0}\x{30fb}-\x{30fe}\x{3190}-\x{319f}\x{31c0}-\x{31cf}'.
'\x{3200}-\x{33ff}\x{4dc0}-\x{4dff}\x{a015}\x{a490}-\x{a716}\x{a802}\x{a806}'.
'\x{a80b}\x{a823}-\x{a82b}\x{d800}-\x{f8ff}\x{fb1e}\x{fb29}\x{fd3e}\x{fd3f}'.
'\x{fdfc}-\x{fe6b}\x{feff}-\x{ff0f}\x{ff1a}-\x{ff20}\x{ff3b}-\x{ff40}\x{ff5b}-'.
'\x{ff65}\x{ff70}\x{ff9e}\x{ff9f}\x{ffe0}-\x{fffd}');

/**
 * Matches all 'N' Unicode character classes (numbers)
 */
define('PREG_CLASS_NUMBERS',
'\x{30}-\x{39}\x{b2}\x{b3}\x{b9}\x{bc}-\x{be}\x{660}-\x{669}\x{6f0}-\x{6f9}'.
'\x{966}-\x{96f}\x{9e6}-\x{9ef}\x{9f4}-\x{9f9}\x{a66}-\x{a6f}\x{ae6}-\x{aef}'.
'\x{b66}-\x{b6f}\x{be7}-\x{bf2}\x{c66}-\x{c6f}\x{ce6}-\x{cef}\x{d66}-\x{d6f}'.
'\x{e50}-\x{e59}\x{ed0}-\x{ed9}\x{f20}-\x{f33}\x{1040}-\x{1049}\x{1369}-'.
'\x{137c}\x{16ee}-\x{16f0}\x{17e0}-\x{17e9}\x{17f0}-\x{17f9}\x{1810}-\x{1819}'.
'\x{1946}-\x{194f}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{2153}-\x{2183}'.
'\x{2460}-\x{249b}\x{24ea}-\x{24ff}\x{2776}-\x{2793}\x{3007}\x{3021}-\x{3029}'.
'\x{3038}-\x{303a}\x{3192}-\x{3195}\x{3220}-\x{3229}\x{3251}-\x{325f}\x{3280}-'.
'\x{3289}\x{32b1}-\x{32bf}\x{ff10}-\x{ff19}');

/**
 * Matches all 'P' Unicode character classes (punctuation)
 */
define('PREG_CLASS_PUNCTUATION',
'\x{21}-\x{23}\x{25}-\x{2a}\x{2c}-\x{2f}\x{3a}\x{3b}\x{3f}\x{40}\x{5b}-\x{5d}'.
'\x{5f}\x{7b}\x{7d}\x{a1}\x{ab}\x{b7}\x{bb}\x{bf}\x{37e}\x{387}\x{55a}-\x{55f}'.
'\x{589}\x{58a}\x{5be}\x{5c0}\x{5c3}\x{5f3}\x{5f4}\x{60c}\x{60d}\x{61b}\x{61f}'.
'\x{66a}-\x{66d}\x{6d4}\x{700}-\x{70d}\x{964}\x{965}\x{970}\x{df4}\x{e4f}'.
'\x{e5a}\x{e5b}\x{f04}-\x{f12}\x{f3a}-\x{f3d}\x{f85}\x{104a}-\x{104f}\x{10fb}'.
'\x{1361}-\x{1368}\x{166d}\x{166e}\x{169b}\x{169c}\x{16eb}-\x{16ed}\x{1735}'.
'\x{1736}\x{17d4}-\x{17d6}\x{17d8}-\x{17da}\x{1800}-\x{180a}\x{1944}\x{1945}'.
'\x{2010}-\x{2027}\x{2030}-\x{2043}\x{2045}-\x{2051}\x{2053}\x{2054}\x{2057}'.
'\x{207d}\x{207e}\x{208d}\x{208e}\x{2329}\x{232a}\x{23b4}-\x{23b6}\x{2768}-'.
'\x{2775}\x{27e6}-\x{27eb}\x{2983}-\x{2998}\x{29d8}-\x{29db}\x{29fc}\x{29fd}'.
'\x{3001}-\x{3003}\x{3008}-\x{3011}\x{3014}-\x{301f}\x{3030}\x{303d}\x{30a0}'.
'\x{30fb}\x{fd3e}\x{fd3f}\x{fe30}-\x{fe52}\x{fe54}-\x{fe61}\x{fe63}\x{fe68}'.
'\x{fe6a}\x{fe6b}\x{ff01}-\x{ff03}\x{ff05}-\x{ff0a}\x{ff0c}-\x{ff0f}\x{ff1a}'.
'\x{ff1b}\x{ff1f}\x{ff20}\x{ff3b}-\x{ff3d}\x{ff3f}\x{ff5b}\x{ff5d}\x{ff5f}-'.
'\x{ff65}');

/**
 * Matches all CJK characters that are candidates for auto-splitting
 * (Chinese, Japanese, Korean).
 * Contains kana and BMP ideographs.
 */
define('PREG_CLASS_CJK', '\x{3041}-\x{30ff}\x{31f0}-\x{31ff}\x{3400}-\x{4db5}'.
'\x{4e00}-\x{9fbb}\x{f900}-\x{fad9}');


/**
 * This function process the text passed at input, extracting all the tokens
 * and scoring each one based in their number of ocurrences and relation with
 * some well-known html tags
 * 
 * @param string  $text the text to be tokenised.
 * @param array   $stop_words array of utf-8 words than can be ignored in 
 *                the text being processed. There are some cool lists of
 *                stop words at http://snowball.tartarus.org/
 * @param boolean $overlap_cjk option to split CJK text into some overlapping
 *                tokens is order to allow them to be searched. Useful to build
 *                indexes and search systems.
 * @param boolean $join_numbers option to join in one unique token sequences of numbers
 *                separated by puntuaction chars. Useful to build indexes and
 *                search systems.
 * @return array one sorted array of tokens, with tokens being the keys and scores in the values.
 */
function tokenise_text($text, $stop_words = array(), $overlap_cjk = false, $join_numbers = false) {

    $textlib = textlib_get_instance();

    // Multipliers for scores of words inside certain HTML tags.
    // Note: 'a' must be included for link ranking to work.
    $tags = array('h1' => 25,
                  'h2' => 18,
                  'h3' => 15,
                  'h4' => 12,
                  'h5' => 9,
                  'h6' => 6,
                  'u' => 3,
                  'b' => 3,
                  'i' => 3,
                  'strong' => 3,
                  'em' => 3,
                  'a' => 10);

    // Strip off all ignored tags to speed up processing, but insert space before/after
    // them to keep word boundaries.
    $text = str_replace(array('<', '>'), array(' <', '> '), $text);
    $text = strip_tags($text, '<'. implode('><', array_keys($tags)) .'>');

    // Split HTML tags from plain text.
    $split = preg_split('/\s*<([^>]+?)>\s*/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    // Note: PHP ensures the array consists of alternating delimiters and literals
    // and begins and ends with a literal (inserting $null as required).

    $tag = FALSE; // Odd/even counter. Tag or no tag.
    $score = 1; // Starting score per word
    $accum = ' '; // Accumulator for cleaned up data
    $tagstack = array(); // Stack with open tags
    $tagwords = 0; // Counter for consecutive words
    $focus = 1; // Focus state

    $results = array(0 => array()); // Accumulator for words for index

    foreach ($split as $value) {
        if ($tag) {
            // Increase or decrease score per word based on tag
            list($tagname) = explode(' ', $value, 2);
            $tagname = $textlib->strtolower($tagname);
            // Closing or opening tag?
            if ($tagname[0] == '/') {
                $tagname = substr($tagname, 1);
                // If we encounter unexpected tags, reset score to avoid incorrect boosting.
                if (!count($tagstack) || $tagstack[0] != $tagname) {
                    $tagstack = array();
                    $score = 1;
                }
                else {
                    // Remove from tag stack and decrement score
                    $score = max(1, $score - $tags[array_shift($tagstack)]);
                }
            }
            else {
                if (isset($tagstack[0]) && $tagstack[0] == $tagname) {
                    // None of the tags we look for make sense when nested identically.
                    // If they are, it's probably broken HTML.
                    $tagstack = array();
                    $score = 1;
                }
                else {
                    // Add to open tag stack and increment score
                    array_unshift($tagstack, $tagname);
                    $score += $tags[$tagname];
                }
            }
            // A tag change occurred, reset counter.
            $tagwords = 0;
        }
        else {
            // Note: use of PREG_SPLIT_DELIM_CAPTURE above will introduce empty values
            if ($value != '') {
                $words = tokenise_split($value, $stop_words, $overlap_cjk, $join_numbers);
                foreach ($words as $word) {
                    // Add word to accumulator
                    $accum .= $word .' ';
                    $num = is_numeric($word);
                    // Check word length
                    if ($num || $textlib->strlen($word) >= MINIMUM_WORD_SIZE) {
                        // Normalize numbers
                        if ($num && $join_numbers) {
                            $word = (int)ltrim($word, '-0');
                        }

                        if (!isset($results[0][$word])) {
                            $results[0][$word] = 0;
                        }

                        $results[0][$word] += $score * $focus;
                        // Focus is a decaying value in terms of the amount of unique words up to this point.
                        // From 100 words and more, it decays, to e.g. 0.5 at 500 words and 0.3 at 1000 words.
                        $focus = min(1, .01 + 3.5 / (2 + count($results[0]) * .015));
                    }
                    $tagwords++;
                    // Too many words inside a single tag probably mean a tag was accidentally left open.
                    if (count($tagstack) && $tagwords >= 15) {
                        $tagstack = array();
                        $score = 1;
                    }
                }
            }
        }
        $tag = !$tag;
    }

    $res = array();

    if (isset($results[0]) && count($results[0]) > 0) {
        $res = $results[0];
        arsort($res, SORT_NUMERIC);
    }

    return $res;
}

///
/// Some helper functions (should be considered private)
///

/**
 * Splits a string into tokens
 */
function tokenise_split($text, $stop_words, $overlap_cjk, $join_numbers) {
    static $last = NULL;
    static $lastsplit = NULL;

    if ($last == $text) {
        return $lastsplit;
    }
    // Process words
    $text = tokenise_simplify($text, $overlap_cjk, $join_numbers);
    $words = explode(' ', $text);
    // Limit word length to 50
    array_walk($words, 'tokenise_truncate_word');

    // We have stop words, apply them
    if (is_array($stop_words) && !empty($stop_words)) {
        // Normalise them
        $simp_stop_words = explode(' ', tokenise_simplify(implode(' ', $stop_words), $overlap_cjk, $join_numbers));
        // Extract from the list
        $words = array_diff($words, $simp_stop_words);
    }
    // Save last keyword result
    $last = $text;
    $lastsplit = $words;

    return $words;
}

/**
 * Simplifies a string according to indexing rules.
 */
function tokenise_simplify($text, $overlap_cjk, $join_numbers) {

    $textlib = textlib_get_instance();

    // Decode entities to UTF-8
    $text = $textlib->entities_to_utf8($text, true);

    // Lowercase
    $text = $textlib->strtolower($text);

    // Simple CJK handling
    if ($overlap_cjk) {
        $text = preg_replace_callback('/['. PREG_CLASS_CJK .']+/u', 'tokenise_expand_cjk', $text);
    }

    // To improve searching for numerical data such as dates, IP addresses
    // or version numbers, we consider a group of numerical characters
    // separated only by punctuation characters to be one piece.
    // This also means that searching for e.g. '20/03/1984' also returns
    // results with '20-03-1984' in them.
    // Readable regexp: ([number]+)[punctuation]+(?=[number])
    if ($join_numbers) {
        $text = preg_replace('/(['. PREG_CLASS_NUMBERS .']+)['. PREG_CLASS_PUNCTUATION .']+(?=['. PREG_CLASS_NUMBERS .'])/u', '\1', $text);
    } else {
    // Keep all the detected numbers+punctuation in a safe place in order to restore them later
        preg_match_all('/['. PREG_CLASS_NUMBERS .']+['. PREG_CLASS_PUNCTUATION . PREG_CLASS_NUMBERS .']+/u', $text, $foundseqs);
        $storedseqs = array();
        foreach (array_unique($foundseqs[0]) as $ntkey => $value) {
            $prefix = (string)(count($storedseqs) + 1);
            $storedseqs[START_DELIM.$prefix.CENTER_DELIM.$ntkey.END_DELIM] = $value;
        }
        if (!empty($storedseqs)) {
            $text = str_replace($storedseqs, array_keys($storedseqs), $text);
        }
    }

    // The dot, underscore and dash are simply removed. This allows meaningful
    // search behaviour with acronyms and URLs.
    $text = preg_replace('/[._-]+/', '', $text);

    // With the exception of the rules above, we consider all punctuation,
    // marks, spacers, etc, to be a word boundary.
    $text = preg_replace('/['. PREG_CLASS_SEARCH_EXCLUDE .']+/u', ' ', $text);

    // Restore, if not joining numbers, recover the original strings
    if (!$join_numbers) {
        if (!empty($storedseqs)) {
            $text = str_replace(array_keys($storedseqs), $storedseqs, $text);
        }
    }

    return $text;
}

/**
 * Basic CJK tokeniser. Simply splits a string into consecutive, overlapping
 * sequences of characters (MINIMUM_WORD_SIZE long).
 */
function tokenise_expand_cjk($matches) {

    $textlib = textlib_get_instance();

    $str = $matches[0];
    $l = $textlib->strlen($str);
    // Passthrough short words
    if ($l <= MINIMUM_WORD_SIZE) {
        return ' '. $str .' ';
    }
    $tokens = ' ';
    // FIFO queue of characters
    $chars = array();
    // Begin loop
    for ($i = 0; $i < $l; ++$i) {
        // Grab next character
        $current = $textlib->substr($str, 0, 1);
        $str = substr($str, strlen($current));
        $chars[] = $current;
        if ($i >= MINIMUM_WORD_SIZE - 1) {
            $tokens .= implode('', $chars) .' ';
            array_shift($chars);
        }
    }
    return $tokens;
}

/**
 * Helper function for array_walk in search_index_split.
 * Truncates one string (token) to MAXIMUM_WORD_SIZE
 */
function tokenise_truncate_word(&$text) {

    $textlib = textlib_get_instance();
    $text = $textlib->substr($text, 0, MAXIMUM_WORD_SIZE);
}

?>
