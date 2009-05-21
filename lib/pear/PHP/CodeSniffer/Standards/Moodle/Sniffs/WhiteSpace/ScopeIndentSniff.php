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
 * moodle_sniffs_whitespace_scopeindentsniff.
 *
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-whitespace
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * moodle_sniffs_whitespace_scopeindentsniff.
 *
 * Checks that control structures are structured correctly, and their content
 * is indented correctly. This sniff will throw errors if tabs are used
 * for indentation rather than spaces.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_whitespace_scopeindentsniff implements php_codesniffer_sniff {

    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    protected $indent = 4;

    /**
     * Does the indent need to be exactly right.
     *
     * If TRUE, indent needs to be exactly $ident spaces. If FALSE,
     * indent needs to be at least $ident spaces (but can be more).
     *
     * @var bool
     */
    protected $exact = false;

    /**
     * Any scope openers that should not cause an indent.
     *
     * @var array(int)
     */
    protected $nonindentingscopes = array();


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return PHP_CodeSniffer_tokens::$scopeOpeners;
    }


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsfile All the tokens found in the document.
     * @param int                  $stackptr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $tokens = $phpcsfile->gettokens();

        // If this is an inline condition (ie. there is no scope opener), then
        // return, as this is not a new scope.
        if (isset($tokens[$stackptr]['scope_opener']) === false) {
            return;
        }

        if ($tokens[$stackptr]['code'] === T_ELSE) {
            $next = $phpcsfile->findnext(PHP_CodeSniffer_tokens::$emptyTokens, ($stackptr + 1), null, true);
            // We will handle the T_IF token in another call to process.

            if ($tokens[$next]['code'] === T_IF) {
                return;
            }
        }

        // Find the first token on this line.
        $firsttoken = $stackptr;

        for ($i = $stackptr; $i >= 0; $i--) {
            // Record the first code token on the line.
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_tokens::$emptyTokens) === false) {
                $firsttoken = $i;
            }

            // It's the start of the line, so we've found our first php token.
            if ($tokens[$i]['column'] === 1) {
                break;
            }
        }

        // Based on the conditions that surround this token, determine the
        // indent that we expect this current content to be.
        $expectedindent = $this->calculateexpectedindent($tokens, $firsttoken);

        if ($tokens[$firsttoken]['column'] !== $expectedindent) {
            $error  = 'line indented incorrectly; expected ';
            $error .= ($expectedindent - 1).' spaces, found ';
            $error .= ($tokens[$firsttoken]['column'] - 1);
            $phpcsfile->adderror($error, $stackptr);
        }

        $scopeopener = $tokens[$stackptr]['scope_opener'];
        $scopecloser = $tokens[$stackptr]['scope_closer'];

        // Some scopes are expected not to have indents.
        if (in_array($tokens[$firsttoken]['code'], $this->nonindentingscopes) === false) {
            $indent = ($expectedindent + $this->indent);

        } else {
            $indent = $expectedindent;
        }

        $newline     = false;
        $commentopen = false;
        $inheredoc   = false;

        // Only loop over the content beween the opening and closing brace, not
        // the braces themselves.
        for ($i = ($scopeopener + 1); $i < $scopecloser; $i++) {

            // If this token is another scope, skip it as it will be handled by
            // another call to this sniff.
            if (in_array($tokens[$i]['code'], PHP_CodeSniffer_tokens::$scopeOpeners) === true) {

                if (isset($tokens[$i]['scope_opener']) === true) {
                    $i = $tokens[$i]['scope_closer'];

                } else {
                    // If this token does not have a scope_opener indice, then
                    // it's probably an inline scope, so let's skip to the next
                    // semicolon. Inline scopes include inline if's, abstract methods etc.
                    $nexttoken = $phpcsfile->findnext(T_SEMICOLON, $i, $scopecloser);

                    if ($nexttoken !== false) {
                        $i = $nexttoken;
                    }
                }

                continue;
            }

            // If this is a HEREDOC then we need to ignore it as the whitespace
            // before the contents within the HEREDOC are considered part of the content.
            if ($tokens[$i]['code'] === T_START_HEREDOC) {
                $inheredoc = true;
                continue;

            } else if ($inheredoc === true) {

                if ($tokens[$i]['code'] === T_END_HEREDOC) {
                    $inheredoc = false;
                }

                continue;
            }

            if ($tokens[$i]['column'] === 1) {
                // We started a newline.
                $newline = true;
            }

            if ($newline === true && $tokens[$i]['code'] !== T_WHITESPACE) {
                // If we started a newline and we find a token that is not
                // whitespace, then this must be the first token on the line that
                // must be indented.
                $newline    = false;
                $firsttoken = $i;

                $column = $tokens[$firsttoken]['column'];

                // Special case for non-PHP code.
                if ($tokens[$firsttoken]['code'] === T_INLINE_HTML) {
                    $trimmedcontentlength = strlen(ltrim($tokens[$firsttoken]['content']));

                    if ($trimmedcontentlength === 0) {
                        continue;
                    }

                    $contentlength = strlen($tokens[$firsttoken]['content']);
                    $column        = ($contentlength - $trimmedcontentlength + 1);
                }

                // Check to see if this constant string spans multiple lines.
                // If so, then make sure that the strings on lines other than the
                // first line are indented appropriately, based on their whitespace.
                if (in_array($tokens[$firsttoken]['code'], PHP_CodeSniffer_tokens::$stringTokens) === true) {

                    if (in_array($tokens[($firsttoken - 1)]['code'], PHP_CodeSniffer_tokens::$stringTokens) === true) {
                        // If we find a string that directly follows another string
                        // then its just a string that spans multiple lines, so we
                        // don't need to check for indenting.
                        continue;
                    }
                }

                // This is a special condition for T_DOC_COMMENT and C-style
                // comments, which contain whitespace between each line.
                if (in_array($tokens[$firsttoken]['code'], array(T_COMMENT, T_DOC_COMMENT)) === true) {
                    $content = trim($tokens[$firsttoken]['content']);

                    if (preg_match('|^/\*|', $content) !== 0) {
                        // Check to see if the end of the comment is on the same line
                        // as the start of the comment. If it is, then we don't
                        // have to worry about opening a comment.
                        if (preg_match('|\*/$|', $content) === 0) {
                            // We don't have to calculate the column for the start
                            // of the comment as there is a whitespace token before it.
                            $commentopen = true;
                        }

                    } else if ($commentopen === true) {

                        if ($content === '') {
                            // We are in a comment, but this line has nothing on it
                            // so let's skip it.
                            continue;
                        }

                        $contentlength        = strlen($tokens[$firsttoken]['content']);
                        $trimmedcontentlength = strlen(ltrim($tokens[$firsttoken]['content']));
                        $column               = ($contentlength - $trimmedcontentlength + 1);

                        if (preg_match('|\*/$|', $content) !== 0) {
                            $commentopen = false;
                        }
                    }
                }

                // The token at the start of the line, needs to have its' column
                // greater than the relative indent we set above. If it is less,
                // an error should be shown.
                if ($column !== $indent) {

                    if ($this->exact === true || $column < $indent) {
                        $error  = 'line indented incorrectly; expected ';

                        if ($this->exact === false) {
                            $error .= 'at least ';
                        }

                        $error .= ($indent - 1).' spaces, found ';
                        $error .= ($column - 1);
                        $phpcsfile->adderror($error, $firsttoken);
                    }
                }
            }
        }
    }


    /**
     * Calculates the expected indent of a token.
     *
     * @param array $tokens   The stack of tokens for this file.
     * @param int   $stackptr The position of the token to get indent for.
     *
     * @return int
     */
    protected function calculateexpectedindent(array $tokens, $stackptr) {
        $conditionstack = array();

        // Empty conditions array (top level structure).
        if (empty($tokens[$stackptr]['conditions']) === true) {
            return 1;
        }

        $tokenconditions = $tokens[$stackptr]['conditions'];

        foreach ($tokenconditions as $id => $condition) {
            // If it's an indenting scope ie. it's not in our array of
            // scopes that don't indent, add it to our condition stack.
            if (in_array($condition, $this->nonindentingscopes) === false) {
                $conditionstack[$id] = $condition;
            }
        }

        return ((count($conditionstack) * $this->indent) + 1);
    }
}
