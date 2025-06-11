<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Serve question type files
 *
 * @since      2.0
 * @package    qtype_regexp
 * @copyright  Joseph REZEAU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Expand regexp.
 * @param string $myregexp
 */
function expand_regexp($myregexp) {
    global $regexporiginal;

    // JR 16 DEC 2011 add parentheses if necessary; still need to detect un-parenthesized pipe.
    // JR 04 OCT 2019 removed this feature which caused a bug.

    $regexporiginal = $myregexp;

    $charlist = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    // Change [a-c] to [abc] NOTE: ^ metacharacter is not processed inside [].
    $pattern = '/\\[\w-\w\\]/';     // Find [a-c] in $myregexp.
    while (preg_match($pattern, $myregexp, $matches, PREG_OFFSET_CAPTURE) ) {
        $result = $matches[0][0];
        $offset = $matches[0][1];
        $stringleft = substr($myregexp, 0, $offset + 1);
        $stringright = substr($myregexp, $offset + strlen($result) - 1);
        $c1 = $result[1];
        $c3 = $result[3];
        $rs = '';
        for ($c = strrpos($charlist, $c1); $c < strrpos($charlist, $c3) + 1; $c++) {
            $rs .= $charlist[$c];
        }
        $myregexp = $stringleft.$rs.$stringright;

    }
    // Provisionally replace existing escaped [] before processing the change [abc] to (a|b|c) JR 11-9-2007.
    // See Oleg http://moodle.org/mod/forum/discuss.php?d=38542&parent=354095.
    // See https://moodle.org/mod/forum/discuss.php?d=251510#p1090642
    // Replacement character changed to Unicode character Halloween 2018.

    $pattern = '/\\\\\[/';
    // Replace \[ with Unicode Character 'LEFT WHITE SQUARE BRACKET' (U+301A).
    $replacement = '〚';
    $count = 0;
    $myregexp = preg_replace ($pattern, $replacement, $myregexp, -1, $count);

    $pattern = '/\\\\\]/';
    // Replace \] with Unicode Character 'RIGHT WHITE SQUARE BRACKET' (U+301B).
    $replacement = '〛';
    $myregexp = preg_replace ($pattern, $replacement, $myregexp, -1, $count);

    // Halloween 2018 version: added similar treatment for excaped parentheses.
    $pattern = '/\\\\\(/';
    // Replace \( with Unicode Character 'FULLWIDTH LEFT PARENTHESIS' (U+FF08).
    $replacement = '（';
    $count = 0;
    $myregexp = preg_replace ($pattern, $replacement, $myregexp, -1, $count);

    $pattern = '/\\\\\)/';
    // Replace \) with Unicode Character 'FULLWIDTH RIGHT PARENTHESIS' (U+FF09).
    $replacement = '）';
    $myregexp = preg_replace ($pattern, $replacement, $myregexp, -1, $count);

    // Change [abc] to (a|b|c).
    $pattern = '/\[.*?\]/';     // Find [abc] in $myregexp.
    // Added core_text to deal with utf8 accents etc.
    while (preg_match($pattern, $myregexp, $matches, PREG_OFFSET_CAPTURE) ) {
        $result = $matches[0][0];
        // Fixed utf8 problem in lengths.
        $offset = core_text::strlen(substr($myregexp, 0, $matches[0][1]));
        $stringleft = core_text::substr($myregexp, 0, $offset);
        $stringright = core_text::substr($myregexp, $offset + core_text::strlen($result));
        $rs = core_text::substr($result, 1, core_text::strlen($result) - 2);
        $r = '';
        $l = core_text::strlen($rs);
        for ($i = 0; $i < $l; $i++) {
            $r .= core_text::substr($rs, $i, 1).'|';
        }
        $rs = '('.core_text::substr($r, 0, core_text::strlen($r) - 1).')';
        $myregexp = $stringleft.$rs.$stringright;
    }

    // We can now safely restore the previously replaced escaped square brakets.
    $pattern = '/〚/';
    $replacement = '\[';
    $count = 0;
    $myregexp = preg_replace ($pattern, $replacement, $myregexp, -1, $count);

    $pattern = '/〛/';
    $replacement = '\]';
    $myregexp = preg_replace ($pattern, $replacement, $myregexp, -1, $count);

    // Process ? in regexp (zero or one occurrence of preceding char).
    while (strpos($myregexp, '?')) {
        $c1 = strpos($myregexp, '?');
        $c0 = $myregexp[$c1 - 1];

        // If \? -> escaped ?, treat as literal char (replace with ¬ char temporarily).
        // This ¬ char chosen because non-alphanumeric & rarely used...
        if ($c0 == '\\') {
            $myregexp = substr($myregexp, 0, $c1 - 1 ) .'¬' .substr($myregexp, $c1 + 1);
            continue;
        }
        // If )? -> meta ? action upon parens (), replace with ¤ char temporarily.
        // This ¤ char chosen because non-alphanumeric & rarely used...
        if ($c0 == ')') {
            $myregexp = substr( $myregexp, 0, $c1 - 1 ) .'¤' .substr($myregexp, $c1 + 1);
            continue;
        }
        // If ? metacharacter acts upon an escaped char, put it in $c2.
        if ($myregexp[$c1 - 2] == '\\') {
            $c0 = '\\'.$c0;
        }
        $c2 = '('.$c0.'|)';
        $myregexp = str_replace($c0.'?', $c2, $myregexp);
    }
    // Replaces possible temporary ¬ char with escaped question mark.
    if (strpos( $myregexp, '¬') != - 1) {
        $myregexp = str_replace('¬', '\?', $myregexp);
        $regexporiginal = $myregexp;
    }
    // Replaces possible temporary ¤ char with escaped question mark.
    if (strpos( $myregexp, '¤') != - 1) {
        $myregexp = str_replace('¤', ')?', $myregexp);
    }

    // Process ? metacharacter acting upon a set of parentheses \(.*?\)\?
    $myregexp = str_replace(')?', '|)', $myregexp);

    // Replace escaped characters with their escape code.
    while ($c = strpos($myregexp, '\\')) {
        $s1 = substr($myregexp, $c, 2);
        $s2 = $myregexp[$c + 1];
        $s2 = rawurlencode($s2);

        // Alaphanumeric chars can't be escaped; escape codes useful here are:
        // . = %2e    ; + = %2b ; * = %2a
        // Add any others as needed & modify below accordingly.
        switch ($s2) {
            case '.' : $s2 = '%2e';
            break;
            case '+' : $s2 = '%2b';
            break;
            case '*' : $s2 = '%2a';
            break;
        }
        $myregexp = str_replace($s1, $s2, $myregexp);
    }

    // Remove starting and trailing metacharacters; not used for generation but useful for testing regexp.
    if (strpos($myregexp, '^')) {
        $myregexp = substr($myregexp, 1);
    }
    if (strpos($myregexp, '$') == strlen($myregexp) - 1) {
        $myregexp = substr( $myregexp, 0, strlen($myregexp) - 1);
    }

    $mynewregexp = find_nested_ors($myregexp);     // Check $myregexp for nested parentheses.
    if ($mynewregexp != null) {
        $myregexp = $mynewregexp;
    }

    $result = find_ors($myregexp);     // Expand parenthesis contents.

    // We can now restore any previously replaced escaped parentheses.
    if ( !is_array($result) ) {
        $results[0] = $result;
    } else {
        $results = $result;
    }
    $i = 0;
    foreach ($results as $result) {
        $toreplace = ['（', '）'];
        $replacewith = ['(', ')'];
        $results[$i] = strtr($result, array_combine($toreplace, $replacewith));
        $i++;
    }
    return $results;    // Returns array of alternate strings.
}

/**
 * Find individual $nestedors expressions in $myregexp.
 * @param string $mystring
 *
 */
function is_nested_ors ($mystring) {
    $orsstart = 0; $orsend = 0; $isnested = false; $parens = 0; $result = '';
    for ($i = 0; $i < strlen($mystring); $i++) {
        switch ($mystring[$i]) {
            case '(':
                $parens++;
                if ($parens == 1) {
                    $orsstart = $i;
                }
                if ($parens == 2) {
                    $isnested = true;
                }
                break;
            case ')':
                $parens--;
                if ($parens == 0) {
                    if ($isnested == true) {
                        $orsend = $i + 1;
                        $i = strlen($mystring);
                        break;
                    }
                }
                break;
        }
    }

    if ($isnested == true) {
        $result = substr( $mystring, $orsstart, $orsend - $orsstart);
        return $result;
    }

    return false;
}

/**
 * Find nested parentheses in $myregexp.
 * @param string $myregexp
 */
function is_parents ($myregexp) {
    $finalresult = null;
    $pattern = '/[^(|)]*\\(([^(|)]*\\|[^(|)]*)+\\)[^(|)]*/';
    if (preg_match_all($pattern, $myregexp, $matches, PREG_OFFSET_CAPTURE)) {
        $matches = $matches[0];
        for ($i = 0; $i < count($matches); $i++) {
            $thisresult = $matches[$i][0];
            $leftchar = $thisresult[0];
            $rightchar = $thisresult[strlen($thisresult) - 1];
            $outerchars = $leftchar .$rightchar;
            if ($outerchars !== '()') {
                $finalresult = $thisresult;
                break;
            }
        }
    }

    return $finalresult;
}

/**
 * Find ((a|b)c).
 * @param string $myregexp
 */
function find_nested_ors ($myregexp) {
    // Find next nested parentheses in $myregexp.
    while ($nestedors = is_nested_ors ($myregexp)) {
        $nestedorsoriginal = $nestedors;

        // Find what?
        while ($myparent = is_parents ($nestedors)) {
            $leftchar = $nestedors[strpos($nestedors, $myparent) - 1];
            $rightchar = $nestedors[strpos($nestedors, $myparent) + strlen($myparent)];
            $outerchars = $leftchar.$rightchar;
            // Fixed DECEMBER 2018.
            $leftpar = '';
            $rightpar = '';
            // End fix.
            switch ($outerchars) {
                case '||':
                case '()':
                    $leftpar = '';
                    $rightpar = '';
                    break;
                case '((':
                case '))':
                case '(|':
                case '|(':
                case ')|':
                case '|)':
                    $leftpar = '('; $rightpar = ')';
                    break;
                default:
                    break;
            }
            $t1 = find_ors ($myparent);
            $t = implode('|', $t1);
            $myresult = $leftpar.$t.$rightpar;
            $nestedors = str_replace( $myparent, $myresult, $nestedors);

        }
        // Detect sequence of ((*|*)|(*|*)) within parentheses or |) or (| and remove all INSIDE parentheses.
        $pattern = '/(\\(|\\|)\\([^(|)]*\\|[^(|)]*\\)(\\|\\([^(|)]*\\|[^(|)]*\\))*(\\)|\\|)/';
        while (preg_match($pattern, $nestedors, $matches, PREG_OFFSET_CAPTURE)) {
            $plainors = $matches[0][0];
            $leftchar = $plainors[0];
            $rightchar = $plainors[strlen($plainors) - 1];
            // Remove leading & trailing chars.
            $plainors2 = substr($plainors, 1, strlen($plainors) - 2);
            $plainors2 = str_replace(  '(',  '', $plainors2);
            $plainors2 = str_replace(  ')',  '', $plainors2);
            $plainors2 = $leftchar .$plainors2 .$rightchar;
            $nestedors = str_replace(  $plainors,  $plainors2, $nestedors);
            if (is_parents($nestedors)) {
                $myregexp = str_replace( $nestedorsoriginal, $nestedors, $myregexp);
                continue;
            }
        }

        // Any sequence of (|)(|) in $nestedors? process them all.
        $pattern = '/(\\([^(]*?\\|*?\\)){2,99}/';
        while (preg_match($pattern, $nestedors, $matches, PREG_OFFSET_CAPTURE)) {
            $parensseq = $matches[0][0];
            $myresult = find_ors ($parensseq);
            $myresult = implode('|', $myresult);
            $nestedors = str_replace( $parensseq, $myresult, $nestedors);
        }
        // Test if we have reached the singleOrs stage.
        if (is_parents ($nestedors) != null) {
            $myregexp = str_replace( $nestedorsoriginal, $nestedors, $myregexp);
            continue;
        }
        // No parents left in $nestedors, ...
        // Find all single (*|*|*|*) and remove parentheses.
        $patternsingleors = '/\\([^()]*\\)/';
        $patternsingleorstotal = '/^\\([^()]*\\)$/';

        while ($p = preg_match($patternsingleors, $nestedors, $matches, PREG_OFFSET_CAPTURE)) {
            $r = preg_match($patternsingleorstotal, $nestedors, $matches, PREG_OFFSET_CAPTURE);
            if ($r) {
                if ($matches[0][0] == $nestedors) {
                    break;
                } // We have reached top of $nestedors: keep ( )!
            }
            $r = preg_match($patternsingleors, $nestedors, $matches, PREG_OFFSET_CAPTURE);
            $singleparens = $matches[0][0];
            $myresult = substr($singleparens, 1, strlen($singleparens) - 2);
            $nestedors = str_replace( $singleparens, $myresult, $nestedors);
            if (is_parents ($nestedors) != null) {
                $myregexp = str_replace( $nestedorsoriginal, $nestedors, $myregexp);
                continue;
            }

        }
        $myregexp = str_replace( $nestedorsoriginal, $nestedors, $myregexp);

    }
    return $myregexp;
}

/**
 * Find ors.
 * @param string $mystring
 */
function find_ors ($mystring) {
    global $regexporiginal;

    // Add extra space between consecutive parentheses (that extra space will be removed later on).
    $pattern = '/\\(.*?\\|.*?\\)/';
    while (strpos($mystring, ')(')) {
        $mystring = str_replace( ')(', ')µ(', $mystring);
    }
    if (strpos($mystring, ')(')) {
        $mystring = str_replace( ')(', ')£(', $mystring);
    }
    // In $mystring, find the parts outside of parentheses ($plainparts).
    $plainparts = preg_split($pattern, $mystring);
    if ($plainparts) {
        $plainparts = index_plain_parts ($mystring, $plainparts);
    }
    $a = preg_match_all($pattern, $mystring, $matches, PREG_OFFSET_CAPTURE);
    if (!$a) {
        $regexporiginal = stripslashes($regexporiginal);
        return $regexporiginal;
    }
    $plainors = index_ors($mystring, $matches);
    // Send $list of $plainparts and $plainors to expand_ors () function.
    return(expand_ors ($plainparts, $plainors));
}

/**
 * This function expands a chunk of words containing a single set of parenthesized alternatives
 * of the type: <(aaa|bbb)> OR <ccc (aaa|bbb)> OR <ccc (aaa|bbb) ddd> etc.
 * into a LIST of possible alternatives,
 * e.g. <ccc (aaa|bbb|)> -> <ccc aaa>, <ccc bbb>, <ccc>.
 * @param string $plainparts
 * @param string $plainors
 */
function expand_ors ($plainparts, $plainors) {
    $expandedors = [];
    $expandedors[] = '';
    $slen = count($expandedors);
    $expandedors[$slen - 1] = '';

    if (isset($plainparts[0]) && $plainparts[0] == 0) { // If chunk begins with $plainparts.
        $expandedors[$slen - 1] = $plainparts[1];
        array_splice($plainparts, 0, 2);
    }
    while ((count($plainparts) != 0) || (count($plainors) != 0)) { // Go through sentence $plainparts.
        $l = count($expandedors);
        for ($k = 0; $k < $l; $k++) {
            for ($m = 0; $m < count($plainors[1]); $m++) {
                $expandedors[] = '';
                $slen = count($expandedors) - 1;
                $expandedors[$slen] = $expandedors[0].$plainors[1][$m];
                if (count($plainparts)) {
                    if ($plainparts[1]) {
                        $expandedors[$slen] .= $plainparts[1];
                    }
                }
                $expandedors[$slen] = rawurldecode($expandedors[$slen]);
            }
            array_splice($expandedors, 0, 1);    // Remove current "model" sentence from Sentences.
        }
        array_splice($plainors, 0, 2); // Remove current $plainors.
        array_splice($plainparts, 0, 2); // Remove current $plainparts.
    }
    // Eliminate all extra µ signs which have been placed to replace consecutive parentheses by )µ(.
    $n = count ($expandedors);
    for ($i = 0; $i < $n; $i++) {
        if (is_int(strpos($expandedors[$i], 'µ') ) ) { // Corrects strpos for 1st char of a string found!
            $expandedors[$i] = str_replace('µ', '', $expandedors[$i]);
        }
    }
    return ($expandedors);
}

/**
 * Index plain parts.
 * @param string $mystring
 * @param array $plainparts
 */
function index_plain_parts($mystring, $plainparts) {
    $indexedplainparts = [];
    if (is_array($plainparts) ) {
        foreach ($plainparts as $parts) {
            if ($parts) {
                $index = strpos($mystring, $parts);
                $indexedplainparts[] = $index;
                $indexedplainparts[] = $parts;
            }
        }
    }
    return ($indexedplainparts);
}

/**
 * Index plain ors.
 * @param string $mystring
 * @param array $plainors
 */
function index_ors($mystring, $plainors) {
    $indexedplainors = [];
    foreach ($plainors as $ors) {
        foreach ($ors as $or) {
            $indexedplainors[] = $or[1];
            $o = substr($or[0], 1, strlen($or[0]) - 2);
            $o = explode('|', $o);
            $indexedplainors[] = $o;
        }
    }
    return ($indexedplainors);
}

/**
 * Function adapted from Hot Potatoes.
 * Check beginning
 * @param string $guess
 * @param string $answer
 * @param boolean $ignorecase
 * @return string $outstring
 */
function check_beginning( $guess, $answer, $ignorecase) {
    $outstring = '';
    if ($ignorecase) {
        $guessoriginal = $guess;
        $guess = strtoupper($guess);
        $answer = strtoupper($answer);
    }

    $i1 = core_text::strlen($guess);
    $i2 = core_text::strlen($answer);
    for ($i = 0; ( $i < $i1 && $i < $i2); $i++) {
        if (strlen($answer) < $i ) {
            break;
        }
        if (core_text::substr($guess, $i, 1) == core_text::substr($answer, $i , 1)) {
            $outstring .= core_text::substr($guess, $i, 1);
        } else {
            break;
        }
    }

    if ($ignorecase) {
        $outstring = core_text::substr($guessoriginal, 0, core_text::strlen($outstring));
    }
    return $outstring;
}

/**
 * Function adapted from Hot Potatoes.
 * Get closest.
 * @param string $guess
 * @param string $answers
 * @param boolean $ignorecase
 * @param int $ishint
 * @return array $closest
 */
function get_closest( $guess, $answers, $ignorecase, $ishint) {
    $closest[0] = ''; // Closest answer to be displayed as input field value.
    $closest[1] = ''; // Closest answer to be displayed in feedback line.
    $closest[2] = ''; // Hint state :: plus (added 1 letter), minus (removed extra chars & added 1 letter),
                      // Complete (correct response achieved), nil (beginning of sentence).
    $closest[3] = ''; // Student's guess (rest of).
    $closest[4] = ''; // Added letter or word (according to Help mode).
    $closest[5] = ''; // Flag the type of errors: [0]wrong; [1]misplaced.
    $closesta = '';
    $l = core_text::strlen($guess);
    $ignorebegin = '';
    if ($ishint) {
        $closest[2] = 'nil';
    }
    $rightbits = [];
    foreach ($answers as $answer) {
        $rightbits[0][] = $answer;
        $rightbits[1][] = check_beginning($guess, $answer, $ignorecase, $ishint);
    }
    $longestanswerlen = max(array_map('core_text::strlen', $rightbits[1]));
    // Function get_max located at the end of this locallib.
    $indexoflongest = get_max($rightbits[1], 0, 0);
    if ($longestanswerlen) {
        // Var $a = alternative correct answer.
        // Var $g = current best student answer so far.
        $a = $rightbits[0][$indexoflongest];
        $g = $rightbits[1][$indexoflongest];
        $closesta = trim($g);
        $closestahint = '';
        if ($ishint) {
            $closest[2] = 'plus';
            $closestahint = $closesta;
        }
        switch ($ishint) {
            case 1: // Get or buy one character (letter of punctuation mark).
                $closestahint = $closesta;
                $closestahint .= core_text::substr($a, $longestanswerlen, 1);
                $lenguess = core_text::strlen($guess);
                $lenclosestahint = core_text::strlen($closestahint);
                if ($lenguess > $lenclosestahint) {
                    $closest[2] = 'minus';
                }
                if (core_text::substr($a, $longestanswerlen, 1) == ' ') { // If hint letter is a space, add next one.
                    $closestahint .= core_text::substr($a, $longestanswerlen + 1, 1);
                }
                break;
            case 2: // Get or buy one word (including punctuation).
                $pattern = '/\s.*/';
                if (preg_match($pattern, $a, $matches, PREG_OFFSET_CAPTURE, strlen($g) + 1) ) {
                    $closestahint = substr($a, 0, $matches[0][1]);
                } else {
                    $pattern = '/.*$/'; // End of sentence.
                    if (preg_match($pattern, $a, $matches, PREG_OFFSET_CAPTURE, core_text::strlen($g)) ) {
                        $closestahint = $a;
                        $closest[2] = 'complete'; // Hint gives a complete correct answer.
                    }
                }
                break;
            case 3:  // Get or buy one word OR one punctuation mark.
                $pattern = '/(\s|(?<!\w)[\p{P}]|[\p{P}](?!\w))/';
                if (preg_match($pattern, $a, $matches, PREG_OFFSET_CAPTURE, strlen($g) + 1) ) {
                    $index = 0;
                    $pattern = '/\s[\p{P}]/';
                    if (preg_match($pattern, $a, $ma, PREG_OFFSET_CAPTURE, strlen($g)) ) {
                        if ($matches[0][1] == $ma[0][1] + 1) {
                            $index = 1;
                        }
                    }
                    $closestahint = substr($a, 0, $matches[0][1] + $index);
                } else {
                    $pattern = '/.*$/'; // End of sentence.
                    if (preg_match($pattern, $a, $matches, PREG_OFFSET_CAPTURE, core_text::strlen($g)) ) {
                        $closestahint = $a;
                        $closest[2] = 'complete'; // Hint gives a complete correct answer.
                    }
                }
        }

        // JR 13 OCT 2012 to fix potential html format tags inside correct answer.
        $aa = preg_replace("/\//", "\/", $a);
        if ( preg_match('/^'.$aa.'$/'.$ignorecase, $closestahint)) {
            $closest[2] = 'complete'; // Hint gives a complete correct answer.
            $state = new stdClass(); // Instantiate $state explicitely for PHP 5.3 compliance.
            $state->raw_grade = 0;
        }
    }

    // Student clicked the help button with an empty answer.
    $a = $rightbits[0][$indexoflongest];
    if ($closesta == '' && $ishint) {
        $closest[2] = 'plus';
        $answer = $answers[0];
        switch ($ishint) {
            case 1: // Add letter.
                $closestahint = core_text::substr($a, 0, 1);
                break;
            case 2: // Add word.
                $words = explode(' ', $answer);
                $closestahint = $words[0];
                break;
            case 3: // Add word or punctuation.
                // Return fist word OR punctuation sign (e.g. Spanish inverted ? or !).
                $pattern = '/^([\p{P}]|\s*([a-zA-Z0-9]+))/u';
                preg_match ($pattern, $a, $matches, PREG_OFFSET_CAPTURE);
                $closestahint = $matches[0][0];
        }
    }

    // Type of hint state.
    switch ($closest[2]) {
        case 'plus':
            $closest[0] = $closestahint;
            $closest[1] = $guess;
            if ($ignorebegin) {
                $closest[1] = '';
            }
            $closest[4] = substr ($closestahint, strlen($closesta));
            break;
        case 'minus':
        case 'complete':
            $closest[1] = $closesta;
            $closest[4] = substr ($closestahint, strlen($closesta));
        case 'minus':
            $closest[0] = $closestahint;
            break;
        case 'complete':
            $closest[0] = $a;
            break;
        default:
            $closest[0] = $closesta;
            $closest[1] = $closest[0];
    }

    // Search for correct *words* in student's guess, after closest answer has been found
    // and even if closest answer is null JR 26 FEB 2012.
    // JR DEC 2020 If word was bought, response can be complete but with extra text, so we need to look for rest of answer.

    $lenclosesta = strlen($closest[0]) - strlen($closest[4]);
    $closest[1] = substr($closest[0], 0, $lenclosesta);
    $restofanswer = substr($guess, $lenclosesta);
    $restofanswer = implode(' ', explode(' ', $restofanswer));

    // Local function get_max at end of this  l ocallib.
    $indexoflongest = get_max($rightbits[1], 0, 0);
    $restofanswers = $rightbits[0][$indexoflongest];

    if ($restofanswer) {
        unset($array1, $array2);
        // Count punctuation marks as words - except within within words themselves.
        // Does not work for French number format (space separator).
        $pattern = "/(\s|(?<!\w)[\p{P}]|[\p{P}](?!\w))/";
        $flags = PREG_SPLIT_DELIM_CAPTURE;
        $array1 = preg_split($pattern, $restofanswer, - 1, $flags);
        $array2 = preg_split($pattern, $restofanswers, - 1, $flags);
        // Filter arrays to remove empty values.
        $array1 = array_filter(array_map('trim', $array1));
        $array2 = array_filter(array_map('trim', $array2));
        $misplacedwords = array_intersect($array1, $array2);
        // Remove potential duplicate words.
        $misplacedwords = array_unique($misplacedwords);
        foreach ($misplacedwords as $key => $value) {
            $misplacedwords[$key] = '<span class="misplacedword">&nbsp;'.$value.'&nbsp;</span>';
        }
        $wrongwords = array_diff($array1, $array2);
        $closest[5] = (count($misplacedwords) !== 0);
        if (count($wrongwords) !== 0) {
            $closest[5] = $closest[5] + 10;
        }
        foreach ($wrongwords as $key => $value) {
            $wrongwords[$key] = '<span class="wrongword">&nbsp;'.$value.'&nbsp;</span> ';
        }
        unset ($result);
        $result = $misplacedwords + $wrongwords;
        ksort($result);
        $result = implode (' ', $result);
        $closest[3] = $result;
        unset ($result);
    }

    return $closest;
}

/**
 * Find whether student's response matches at least the beginning of one of the correct answers.
 * @param array $question
 * @param string $currentanswer
 * @param boolean $correctresponse
 * @param boolean $hintadded
 * @return array $closest
 */
function find_closest($question, $currentanswer, $correctresponse = false, $hintadded = false) {
    global $CFG;
    // JR dec 2011 moved get alternate answers to new function.
    $alternateanswers = get_alternateanswers($question);
    $alternatecorrectanswers = [];
    // JR jan 2012 changed contents of alternateanswers.
    if (isset($question->id)) {
        $qid = $question->id;
        if (!isset ($SESSION->qtype_regexp_question->alternatecorrectanswers[$qid])) {
            foreach ($alternateanswers as $key => $alternateanswer) {
                foreach ($alternateanswer['answers'] as $alternate) {
                    $alternatecorrectanswers[] = $alternate;
                }
            }
        }
    }
    // Testing ignorecase.
    $ignorecase = 'i';
    if ($question->usecase) {
        $ignorecase = '';
    };
    // Only use ishint value if hint button has been clicked.
    $ishint = $question->usehint * $hintadded;

    // Find closest answer matching student response.
    if (!isset($currentanswer) && !$correctresponse) {
        return null;
    }
    if ($correctresponse) {
        return $alternatecorrectanswers;
    }
    $closest = get_closest( $currentanswer, $alternatecorrectanswers, $ignorecase, $ishint);
    if ($closest[2] == 'complete') {
        return $closest;
    }

    return $closest;
}

/**
 * Remove extra blank spaces from student's response.
 * @param string $text
 * @return string $text
 */
function remove_blanks($text) {
    // Finds 2 successive spaces (note: \s does not work with French 'à' character!
    $pattern = "/  /";
    // Added (string) before $text for PHP 8.1 compatibility.
    while ($w = preg_match($pattern, (string) $text, $matches, PREG_OFFSET_CAPTURE) ) {
        $text = substr($text, 0, $matches[0][1]) .substr($text, $matches[0][1] + 1);
    }
    // Remove potential final extra blank. 31 AUG 2024.
    $text = $text !== null ? trim($text) : '';
    return $text;
}

/**
 * Check that parentheses and square brackets are balanced, including nested ones.
 * @param string $myregexp
 * @param string $markedline
 * @return string
 */
function check_my_parens($myregexp, $markedline) {
    $parens = [];
    $sqbrackets = [];

    // Walk the $myregexp string to find parentheses and square brackets.
    for ($i = 0; $i < strlen($myregexp); $i++) {
        $escaped = false;
        if ($i > 0 && $myregexp[$i - 1] == "\\") {
            $escaped = true;
        }
        if (!$escaped) {
            switch ($myregexp[$i]) {
                case '(': $parens[$i] = 'open';
                break;
                case ')': $parens[$i] = 'close';
                break;
                case '[': $sqbrackets[$i] = 'open';
                break;
                case ']': $sqbrackets[$i] = 'close';
                break;
                default:
                break;
            }
        }
    }
    // Check for parentheses.
    $tags['open'] = '(';
    $tags['close'] = ')';
    $markedline2 = check_balanced($parens, $tags, $markedline);

    // Check for square brackets.
    $tags['open'] = '[';
    $tags['close'] = ']';
    $markedline2 = check_balanced($sqbrackets, $tags, $markedline2);
    if ($markedline2 != $markedline) {
        return $markedline2;
    } else {
        return;
    }
}

/**
 * Check that parentheses and square brackets are balanced.
 * @param array $bracketstype
 * @param array $tags
 * @param string $markedline
 * @return string
 */
function check_balanced ($bracketstype, $tags, $markedline) {
    $open = [];
    foreach ($bracketstype as $key => $value) {
        switch ($value) {
            case 'open':
                $open[] = $key;
                break;
            case 'close':
                if ($open) {
                    $index = array_pop ($open);
                    $bracketstype[$index] = null;
                    $bracketstype[$key] = null;
                }
            break;
        }
    }
    foreach ($bracketstype as $key => $value) {
        if ($value) {
            if ($value == 'open') {
                $mark = $tags['open'];
            }
            if ($value == 'close') {
                $mark = $tags['close'];
            }
            $markedline[$key] = $mark;
        }
    }
    return $markedline;
}

/**
 * Detect un-escaped metacharacters.
 * Full list of metacharacters used in regular expressions syntax.
 * ALL these characters can be used as metacharacters in INCORRECT Answers (grade = None)
 * . ^ $ * ( ) [ ] + ? | { } \ *
 * Characters which can NOT be used as metacharacters in an accepted Answer (grade > 0)
 * and MUST be escaped if used for their LITERAL value: . ^ $ * + { } \
 * Characters which CAN be used as metacharacters in an accepted Answer (grade > 0)
 * and must be escaped IF used for their LITERAL value: use of those characters
 * must lead to alternative CORRECT answers ( ) [ ] | ?
 * @param string $myregexp
 * @param string $markedline
 * @return string
 */
function check_unescaped_metachars ($myregexp, $markedline) {
    $markedline2 = $markedline;
    // All metacharacters must be escaped.
    // Check for unescaped metacharacters, except for backslash itself.
    $unescapedregex = '/(?<!\\\\)[\.\^\$\*\+\{\}]/';
    // 1 (?<!\\\\) NO backslash preceding (this is a negative lookahead assertion)
    // 2 [\.\^\$\*\+\{\}] list of metacharacters which can NOT be used in context of accepted Answer (grade > 0).

    $unescapedmetachars = preg_match_all($unescapedregex, $myregexp, $matches, PREG_OFFSET_CAPTURE);
    if ($unescapedmetachars) {
        foreach ($matches as $v1) {
            // In marked line, replace blank spaces with the unescaped metacharacter.
            foreach ($v1 as $v2) {
                $markedline2[$v2[1]] = $v2[0];
            }
        }
    }

    // Now check for unescaped backslashes.
    $unescapedregex = '/(^|[^\\\])\\\[^\.|\*|\(|\\[\]\{\}\/)\+\?\^\|\$\.]/';
    /* First part of regexp: beginning of sentence OR no backslash.
     Second part of regexp: followed by backslash.
     This is the third part of regexp: NOT followed by a metacharacter.
     */

    $unescapedmetachars = preg_match_all($unescapedregex, $myregexp, $matches, PREG_OFFSET_CAPTURE);
    if ($unescapedmetachars) {
        $foundbackslash = substr($matches[0][0][0], 1, 3);
        // We must skip a valid escaped backslash.
        if ($foundbackslash != "\\\\") {
            foreach ($matches as $v1) {
                // In marked line, replace blank spaces with the unescaped backslash \.
                foreach ($v1 as $v2) {
                    $markedline2[$v2[1] + 1] = '\\';
                }
            }
        }
    }
    if ($markedline2 != $markedline) {
        return $markedline2;
    } else {
        return;
    }
}

/**
 * When displaying unescaped_metachars or unbalanced brackets, too long strings need to be cut up into chunks.
 * Change $maxlen if necessary (e.g. to fit smaller width screens).
 * @param string $longstring
 * @param int $maxlen
 */
function splitstring ($longstring, $maxlen = 75) {
    $len = core_text::strlen($longstring);
    $stringchunks = [];
    if ($len < $maxlen) {
        $stringchunks[] = $longstring;
    } else {
        for ($i = 0; $i < $len; $i += $maxlen) {
            $stringchunks[] = core_text::substr($longstring, $i, $maxlen);
        }
    }
    return $stringchunks;
}

/**
 * Expand regexp.
 * @param array $question
 */
function get_alternateanswers($question) {
    global $CFG, $SESSION;
    $qid = '';

    if (isset($question->id)) {
        $qid = $question->id;
        if (isset ($SESSION->qtype_regexp_question->alternateanswers[$qid])) {
            return $SESSION->qtype_regexp_question->alternateanswers[$qid];
        }
    }
    $alternateanswers = [];
    $i = 1;
    foreach ($question->answers as $answer) {
        if ($answer->fraction != 0) {
            // This is Answer 1 :: do not process as regular expression.
            if ($i == 1) {
                $alternateanswers[$i]['fraction'] = ($answer->fraction * 100).'%';
                $alternateanswers[$i]['regexp'] = $answer->answer;
                $alternateanswers[$i]['answers'][] = $answer->answer;
            } else {
                // JR added permutations OCT 2012.
                $answer->answer = has_permutations($answer->answer);
                // End permutations.
                $r = expand_regexp($answer->answer);
                if ($r) {
                    $alternateanswers[$i]['fraction'] = ($answer->fraction * 100).'%';
                    $alternateanswers[$i]['regexp'] = $answer->answer;
                    if (is_array($r)) {
                        $alternateanswers[$i]['answers'] = $r; // Normal alternateanswers (expanded).
                    } else {
                        $alternateanswers[$i]['answers'][] = $r; // Regexp was not expanded.
                    }
                }
            }
        }
        $i++;
    }
    // Store alternate answers in SESSION for caching effect DEC 2011.
    // Added isset check DEC 2020 to avoid error message (strict syntax).
    if (isset($SESSION->qtype_regexp_question->alternateanswers)) {
        $SESSION->qtype_regexp_question->alternateanswers[$qid] = $alternateanswers;
        $SESSION->qtype_regexp_question->alternatecorrectanswers[$qid] = '';
    }
    return $alternateanswers;
}

/**
 * Check permutations.
 * @param string $ans
 */
function check_permutations($ans) {
    $p = preg_match_all("/\[\[(.*)\]\]/U", $ans, $matches);
    if ($p === 0) {
        return;
    }
    if ($p > 2) {
        return get_string("regexperrortoomanypermutations", "qtype_regexp");
    }
    $nbpermuted = count($matches[1]);
    for ($i = 0; $i < $nbpermuted; $i++) {
        $ans = $matches[1][$i];
        $p = preg_match_all("/(.*)_(.*)_.*/U", $ans, $matchesp);
        if ($p === 0) {
            return get_string("regexperrornopermutations", "qtype_regexp");
        }
        $p = preg_match_all("/_/", $ans, $matchesp);
        $n = count($matchesp[0]);
        if ($odd = $n % 2) {
            return get_string("regexperroroddunderscores", "qtype_regexp").' '.$n;
        }
    }
}

/**
 * Check if $ans has permutations.
 * @param string $ans
 */
function has_permutations($ans) {
    require_once('combinations/YourCombinations.php');
    $staticparts = [];
    $p = preg_match_all("/\[\[(.*)\]\]/U", $ans, $matches);
    if ($p === 0) {
        return $ans;
    }
    $nbpermuted = count($matches[1]);
    $p = preg_match_all("/(.*)\[\[(.*)\]\](.*)/", $ans, $nonpermuted);
    if ($nbpermuted > 1) {
        $p = preg_match_all("/(.*)\[\[(.*)\]\](.*)/", $nonpermuted[1][0], $nonpermuted2);
        $beginning2 = $nonpermuted2[1][0];
        $staticparts[0] = $nonpermuted2[1][0];
        $staticparts[1] = $nonpermuted2[3][0];
        $staticparts[2] = $nonpermuted[3][0];
    } else {
        $staticparts[0] = $nonpermuted[1][0];
        $staticparts[1] = $nonpermuted[3][0];
    }
    $nbstaticparts = count($staticparts);
    $res = [];
    for ($i = 0; $i < $nbpermuted; $i++) {
        $res[$i] = '(';
        $ans = $matches[1][$i];
        $p = preg_match_all("/(.*)_(.*)_.*/U", $ans, $matchesp);
        $combinations = new YourCombinations($matchesp[2]);
        $nb = count($matchesp[2]);
        $p = preg_match_all("/_.*_(.*)/", $ans, $matchesr);
        $rightelement = '';
        if ($p) {
            $rightelement = $matchesr[1][0];
        }
        foreach ($combinations->Permutations($nb, false) as $permutation) {
            for ($j = 0; $j < $nb; $j++) {
                $res[$i] .= $matchesp[1][$j] .$permutation[$j];
            }
            $res[$i] .= $rightelement.'|';
        }
        $res[$i] = rtrim($res[$i], '|');
        $res[$i] .= ')';
    }
    $result = '';
    for ($i = 0; $i < $nbstaticparts - 1; $i++) {
        $result .= $staticparts[$i].$res[$i];
    }
    $result .= $staticparts[$i];
    return $result;
}

/**
 * See https://stackoverflow.com/questions/1762191/how-to-get-the-length-of-longest-string-in-an-array#1762216.
 * @param array $array
 * @param int $cur
 * @param int $curmax
 */
function get_max($array, $cur, $curmax) {
    return $cur >= count($array) ? $curmax :
        get_max($array, $cur + 1, strlen($array[$cur]) > strlen($array[$curmax]) ? $cur : $curmax);
}
