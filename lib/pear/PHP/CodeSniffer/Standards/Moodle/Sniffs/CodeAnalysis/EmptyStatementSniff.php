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
 * File containing the moodle_sniffs_codeanalysis_emptystatementsniff Class
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-codeanalysis
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This sniff class detected empty statement.
 *
 * This sniff implements the common algorithm for empty statement body detection.
 * A body is considered as empty if it is completely empty or it only contains
 * whitespace characters and|or comments.
 *
 * <code>
 * stmt {
 *   // foo
 * }
 * stmt (conditions) {
 *   // foo
 * }
 * </code>
 *
 * Statements covered by this sniff are <b>catch</b>, <b>do</b>, <b>else</b>,
 * <b>elsif</b>, <b>for</b>, <b>foreach<b>, <b>if</b>, <b>switch</b>, <b>try</b>
 * and <b>while</b>.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_codeanalysis_emptystatementsniff implements php_codesniffer_sniff {

    /**
     * List of block tokens that this sniff covers.
     *
     * The key of this hash identifies the required token while the boolean
     * value says mark an error or mark a warning.
     *
     * @type array<boolean>
     * @var array(integer=>boolean) $_tokens
     */
    private $_tokens = array(
                        T_CATCH   => true,
                        T_DO      => false,
                        T_ELSE    => false,
                        T_ELSEIF  => false,
                        T_FOR     => false,
                        T_FOREACH => false,
                        T_IF      => false,
                        T_SWITCH  => false,
                        T_TRY     => false,
                        T_WHILE   => false,
                       );


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return array(integer)
     */
    public function register() {
        return array_keys($this->_tokens);
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile The file being scanned.
     * @param int                  $stackptr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();
        $token  = $tokens[$stackptr];

        // Skip for-statements without body.
        if (isset($token['scope_opener']) === false) {
            return;
        }

        $next = ++$token['scope_opener'];
        $end  = --$token['scope_closer'];

        $emptybody = true;

        for (; $next <= $end; ++$next) {

            if (in_array($tokens[$next]['code'], PHP_CodeSniffer_tokens::$emptyTokens) === false) {
                $emptybody = false;
                break;
            }
        }

        if ($emptybody === true) {
            // Get token identifier.
            $name  = $phpcsfile->gettokensAsString($stackptr, 1);
            $error = sprintf('Empty %s statement detected', strtoupper($name));

            if ($this->_tokens[$token['code']] === true) {
                $phpcsfile->adderror($error, $stackptr);

            } else {
                $phpcsfile->addwarning($error, $stackptr);
            }
        }
    }
}
