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
 * moodle_sniffs_namingconventions_uppercaseconstantnamesniff.
 *
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-namingconventions
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_namingconventions_uppercaseconstantnamesniff.
 *
 * Ensures that constant names are all uppercase.
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_namingconventions_uppercaseconstantnamesniff implements php_codesniffer_sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_STRING);

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
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr)
    {
        $tokens    = $phpcsfile->gettokens();
        $constname = $tokens[$stackptr]['content'];

        // If this token is in a heredoc, ignore it.
        if ($phpcsfile->hasCondition($stackptr, T_START_HEREDOC) === true) {
            return;
        }

        // If the next non-whitespace token after this token
        // is not an opening parenthesis then it is not a function call.
        $openBracket = $phpcsfile->findNext(array(T_WHITESPACE), ($stackptr + 1), null, true);
        if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
            $functionKeyword = $phpcsfile->findPrevious(array(T_WHITESPACE, T_COMMA, T_COMMENT, T_STRING), ($stackptr - 1), null, true);

            $declarations = array(
                             T_FUNCTION,
                             T_CLASS,
                             T_INTERFACE,
                             T_IMPLEMENTS,
                             T_EXTENDS,
                             T_INSTANCEOF,
                             T_NEW,
                            );
            if (in_array($tokens[$functionKeyword]['code'], $declarations) === true) {
                // This is just a declaration; no constants here.
                return;
            }

            if ($tokens[$functionKeyword]['code'] === T_CONST) {
                // This is a class constant.
                if (strtoupper($constname) !== $constname) {
                    $error = 'Class constants must be uppercase; expected '.strtoupper($constname)." but found $constname";
                    $phpcsfile->adderror($error, $stackptr);
                }

                return;
            }

            // Is this a class name?
            $nextPtr = $phpcsfile->findNext(array(T_WHITESPACE), ($stackptr + 1), null, true);
            if ($tokens[$nextPtr]['code'] === T_DOUBLE_COLON) {
                return;
            }

            // Is this a type hint?
            if ($tokens[$nextPtr]['code'] === T_VARIABLE) {
                return;
            } else if ($phpcsfile->isReference($nextPtr) === true) {
                return;
            }

            // Is this a member var name?
            $prevPtr = $phpcsfile->findPrevious(array(T_WHITESPACE), ($stackptr - 1), null, true);
            if ($tokens[$prevPtr]['code'] === T_OBJECT_OPERATOR) {
                return;
            }

            // Is this an instance of declare()
            $prevPtr = $phpcsfile->findPrevious(array(T_WHITESPACE, T_OPEN_PARENTHESIS), ($stackptr - 1), null, true);
            if ($tokens[$prevPtr]['code'] === T_DECLARE) {
                return;
            }

            // This is a real constant.
            if (strtoupper($constname) !== $constname) {
                $error = 'Constants must be uppercase; expected '.strtoupper($constname)." but found $constname";
                $phpcsfile->adderror($error, $stackptr);
            }

        } else if (strtolower($constname) === 'define' || strtolower($constname) === 'constant') {

            /*
                This may be a "define" or "constant" function call.
            */

            // The next non-whitespace token must be the constant name.
            $constPtr = $phpcsfile->findNext(array(T_WHITESPACE), ($openBracket + 1), null, true);
            if ($tokens[$constPtr]['code'] !== T_CONSTANT_ENCAPSED_STRING) {
                return;
            }

            $constname = $tokens[$constPtr]['content'];
            if (strtoupper($constname) !== $constname) {
                $error = 'Constants must be uppercase; expected '.strtoupper($constname)." but found $constname";
                $phpcsfile->adderror($error, $stackptr);
            }
        }

    }


}

?>
