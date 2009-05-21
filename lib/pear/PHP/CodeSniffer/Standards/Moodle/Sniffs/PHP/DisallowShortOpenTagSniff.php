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
 * moodle_sniffs_php_disallowshortopentagsniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-php
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_php_disallowshortopentagsniff.
 *
 * Makes sure that shorthand PHP open tags are not used.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_php_disallowshortopentagsniff implements php_codesniffer_sniff {


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        // If short open tags are off, then any short open tags will be converted
        // to inline_html tags so we can just ignore them.
        // If its on, then we want to ban the use of them.
        $option = ini_get('short_open_tag');

        // Ini_get returns a string "0" if short open tags is off.
        if ($option === '0') {
            return;
        }

        $tokens  = $phpcsfile->gettokens();
        $opentag = $tokens[$stackptr];

        if ($opentag['content'] === '<?') {
            $error = 'Short PHP opening tag used. Found "'.$opentag['content'].'" Expected "<?php".';
            $phpcsfile->adderror($error, $stackptr);
        }

        if ($opentag['code'] === T_OPEN_TAG_WITH_ECHO) {
            $nextvar = $tokens[$phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($stackptr + 1), null, true)];
            $error   = 'Short PHP opening tag used with echo. Found "';
            $error  .= $opentag['content'].' '.$nextvar['content'].' ..." but expected "<?php echo '.
                       $nextvar['content'].' ...".';
            $phpcsfile->adderror($error, $stackptr);
        }
    }
}
