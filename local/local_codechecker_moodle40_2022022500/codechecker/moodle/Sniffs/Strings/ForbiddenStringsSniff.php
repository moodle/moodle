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
 * Inspect some strings that may lead to incorrect uses of Moodle/PHP APIs.
 *
 * @package    local_codechecker
 * @copyright  2014 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace MoodleCodeSniffer\moodle\Sniffs\Strings;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ForbiddenStringsSniff implements Sniff {

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array tokens this sniff will handle.
     */
    public function register() {
        // We are going to handle strings here.
        return array(T_CONSTANT_ENCAPSED_STRING);
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr) {
        // Delegate the processing to specialised methods.
        $this->process_sql_as_keyword($phpcsFile, $stackPtr);
        $this->process_regexp_separator_e($phpcsFile, $stackPtr);
        $this->process_string_with_backticks($phpcsFile, $stackPtr);
    }

    /**
     * Detect strings using the AS keyword to aliase tables.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    protected function process_sql_as_keyword(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];
        $text = trim($token['content'], "'\"");
        if (preg_match('~\b(FROM|JOIN)\b.*\{\w+\}\s*\bAS\b~im', $text)) {
            $error = 'The use of the AS keyword to alias tables is bad for cross-db';
            $phpcsFile->addError($error, $stackPtr, 'Found');
        }
    }

    /**
     * Detect strings being regexp and using the "e" modifier.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    protected function process_regexp_separator_e(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];
        $text = trim($token['content'], " '\"\t\n");
        if (@preg_match($text, '') !== false) {
            // Regular expression found. Look for used separator (1st char).
            $separator = substr($text, 0, 1);
            // Get rid of separator.
            $text = trim($text, $separator);
            $parts = preg_split('~' . preg_quote($separator, '~') . '~', $text);
            // Need exactly 2 parts.
            if (isset($parts[1]) && !isset($parts[2])) {
                $modifiers = $parts[1];
                if (preg_match('~^[imsxeADSUXJu]+$~', $modifiers)) { // Only when modifiers are valid.
                    if (strpos($modifiers, 'e') !== false) {
                        $error = 'The use of the /e modifier in regular expressions is forbidden';
                        $phpcsFile->addError($error, $stackPtr, 'Found');
                    }
                }
            }
        }
    }

    /**
     * Detect strings using backticks.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token in the stack passed in $tokens.
     *
     * @return void
     */
    protected function process_string_with_backticks(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];
        $text = trim($token['content'], "'\"");
        if (strpos($text, '`') !== false) { //phpcs:ignore moodle.Strings.ForbiddenStrings.Found

            // Exception. lang strings ending with _desc or _help can
            // contain backticks as they are correct Markdown formatting.
            // Look for previous string.
            $prevString = $phpcsFile->findPrevious(
                T_CONSTANT_ENCAPSED_STRING,
                ($stackPtr - 1));
            if ($prevString) {
                $prevtext = trim($tokens[$prevString]['content'], "'\"");
                // Verify it matches _desc|_help.
                if (preg_match('/(_desc|_help)$/', $prevtext)) {
                    // Verify it's an $string array element.
                    $prevToken = $phpcsFile->findPrevious(
                        T_OPEN_SQUARE_BRACKET,
                        ($prevString - 1),
                        null,
                        true);
                    if ($prevToken) {
                        if ($tokens[$prevToken]['code'] === T_VARIABLE && $tokens[$prevToken]['content'] === '$string') {
                            // Confirmed we are in a valid lang string using Markdown, skip any warning.
                            return;
                        }
                    }
                }
            }

            $error = 'The use of backticks in strings is not recommended';
            $phpcsFile->addWarning($error, $stackPtr, 'Found');
        }
    }
}
