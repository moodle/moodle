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
 * moodle_sniffs_controlstructures_inlinecontrolstructuresniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-controlstructures
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_controlstructures_inlinecontrolstructuresniff.
 *
 * Verifies that inline control statements are not present.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_controlstructures_inlinecontrolstructuresniff implements php_codesniffer_sniff {

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedtokenizers = array('PHP', 'JS');

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    protected $error = true;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_IF,
                     T_ELSE,
                     T_FOREACH,
                     T_WHILE,
                     T_DO,
                     T_SWITCH,
                     T_FOR);
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
        $tokens = $phpcsfile->gettokens();

        if (isset($tokens[$stackptr]['scope_opener']) === false) {
            // Ignore the ELSE in ELSE IF. We'll process the IF part later.
            if (($tokens[$stackptr]['code'] === T_ELSE) && ($tokens[($stackptr + 2)]['code'] === T_IF)) {
                return;
            }

            if ($tokens[$stackptr]['code'] === T_WHILE) {
                // This could be from a DO WHILE, which doesn't have an opening brace.
                $lastcontent = $phpcsfile->findprevious(T_WHITESPACE, ($stackptr - 1), null, true);

                if ($tokens[$lastcontent]['code'] === T_CLOSE_CURLY_BRACKET) {
                    $brace = $tokens[$lastcontent];

                    if (isset($brace['scope_condition']) === true) {
                        $condition = $tokens[$brace['scope_condition']];

                        if ($condition['code'] === T_DO) {
                            return;
                        }
                    }
                }
            }

            // This is a control structure without an opening brace,
            // so it is an inline statement.
            if ($this->error === true) {
                $phpcsfile->adderror('Inline control structures are not allowed', $stackptr);
            } else {
                $phpcsfile->addwarning('Inline control structures are discouraged', $stackptr);
            }

            return;
        }
    }
}
