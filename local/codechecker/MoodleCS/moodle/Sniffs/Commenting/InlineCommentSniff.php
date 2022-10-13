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
 * Verifies that inline comments conform to their coding standards.
 *
 * Based on {@see PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\InlineCommentSniff}
 * with some customizations to suit our very personal rules.
 *
 * @package    local_codechecker
 * @copyright  2012 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Checks that there is adequate spacing between comments.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace MoodleHQ\MoodleCS\moodle\Sniffs\Commenting;

// phpcs:disable moodle.NamingConventions

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class InlineCommentSniff implements Sniff {

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
        'JS',
    ];


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return [
            T_COMMENT,
            T_DOC_COMMENT_OPEN_TAG,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        // If this is a function/class/interface doc block comment, skip it.
        // We are only interested in inline doc block comments, which are
        // not allowed.
        if ($tokens[$stackPtr]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            $nextToken = $phpcsFile->findNext(
                Tokens::$emptyTokens,
                ($stackPtr + 1),
                null,
                true
            );

            $ignore = [
                T_CLASS,
                T_INTERFACE,
                T_TRAIT,
                T_FUNCTION,
                T_CLOSURE,
                T_PUBLIC,
                T_PRIVATE,
                T_PROTECTED,
                T_FINAL,
                T_STATIC,
                T_ABSTRACT,
                T_CONST,
                T_PROPERTY,
                T_INCLUDE,
                T_INCLUDE_ONCE,
                T_REQUIRE,
                T_REQUIRE_ONCE,
            ];

            if (in_array($tokens[$nextToken]['code'], $ignore, true) === true) {
                return;
            }

            // Allow phpdoc block before "return new class extends" expressions,
            // we use those anon classes in places like coverage.php files.
            if ($this->is_return_new_class_extends($phpcsFile, $stackPtr)) {
                return;
            }

            // Allow phpdoc before define() token (see CONTRIB-4150).
            if ($tokens[$nextToken]['code'] == T_STRING and $tokens[$nextToken]['content'] == 'define') {
                return;
            }

            if ($phpcsFile->tokenizerType === 'JS') {
                // We allow block comments if a function or object
                // is being assigned to a variable.
                $ignore    = Tokens::$emptyTokens;
                $ignore[]  = T_EQUAL;
                $ignore[]  = T_STRING;
                $ignore[]  = T_OBJECT_OPERATOR;
                $nextToken = $phpcsFile->findNext($ignore, ($nextToken + 1), null, true);
                if ($tokens[$nextToken]['code'] === T_FUNCTION
                    || $tokens[$nextToken]['code'] === T_CLOSURE
                    || $tokens[$nextToken]['code'] === T_OBJECT
                    || $tokens[$nextToken]['code'] === T_PROTOTYPE
                ) {
                    return;
                }
            }

            $prevToken = $phpcsFile->findPrevious(
                Tokens::$emptyTokens,
                ($stackPtr - 1),
                null,
                true
            );

            if ($tokens[$prevToken]['code'] === T_OPEN_TAG) {
                return;
            }

            // Allow @var (type hinting) phpdocs matching beginning of next line.
            $nextToken = $phpcsFile->findNext(
                T_DOC_COMMENT_WHITESPACE,
                ($stackPtr + 1),
                null,
                true
            );
            // Is it a @var tag in the comment?
            if ($tokens[$nextToken]['code'] === T_DOC_COMMENT_TAG &&
                    $tokens[$nextToken]['content'] == '@var') {
                $nextToken = $phpcsFile->findNext(
                    T_DOC_COMMENT_WHITESPACE,
                    ($nextToken + 1),
                    null,
                    true
                );
                // Does the @var comment string end with a variable?
                if ($tokens[$nextToken]['code'] === T_DOC_COMMENT_STRING) {
                    if (preg_match('/\$[^ ]+ *$/', $tokens[$nextToken]['content'], $matches)) {
                        $foundvar = trim($matches[0]);
                        // Does the found variable match any next line beginning with any of:
                        // - a list() statement containing the variable.
                        // - a foreach() statement containing the variable 'as'.
                        // - the variable.
                        $nextToken = $phpcsFile->findNext(
                            Tokens::$emptyTokens,
                            ($nextToken + 1),
                            null,
                            true
                        );
                        if ($tokens[$nextToken]['code'] === T_LIST) {
                            // Let's look within the list for the variable,
                            // calculating its start and end.
                            $liststart = $phpcsFile->findNext(
                                T_OPEN_PARENTHESIS,
                                ($nextToken + 1)
                            );
                            $listend = $phpcsFile->findNext(
                                T_CLOSE_PARENTHESIS,
                                ($liststart + 1)
                            );
                            // Now look for the var within the list used variables.
                            $nextToken = $phpcsFile->findNext(
                                T_VARIABLE,
                                $liststart,
                                $listend,
                                false,
                                $foundvar,
                                true
                            );
                            if (!$nextToken) {
                                // Not valid type-hinting, specialised error.
                                $error = 'Inline doc block type-hinting for \'%s\' does not match next list() variables';
                                $data = array($foundvar);
                                $phpcsFile->addError($error, $stackPtr, 'TypeHintingList', $data);
                            }
                        } else if ($tokens[$nextToken]['code'] === T_FOREACH) {
                            // Let's look within the foreach if the variable appear after the 'as' token.
                            $astoken = $phpcsFile->findNext(
                                T_AS,
                                ($nextToken + 1)
                            );
                            $variabletoken = $phpcsFile->findNext(
                                T_VARIABLE,
                                ($astoken + 1)
                            );
                            if ($tokens[$variabletoken]['content'] !== $foundvar) {
                                // Not valid type-hinting, specialised error.
                                $error = 'Inline doc block type-hinting for \'%s\' does not match next foreach() as variable';
                                $data = array($foundvar, $tokens[$nextToken]['content']);
                                $phpcsFile->addError($error, $stackPtr, 'TypeHintingForeach', $data);
                            }
                        } else if ($tokens[$nextToken]['content'] !== $foundvar) {
                            // Not valid type-hinting, specialised error.
                            $error = 'Inline doc block type-hinting for \'%s\' does not match next code line \'%s...\'';
                            $data = array($foundvar, $tokens[$nextToken]['content']);
                            $phpcsFile->addError($error, $stackPtr, 'TypeHintingMatch', $data);
                        }
                        return; // Have finished.
                    }
                }
            }

            if ($tokens[$stackPtr]['content'] === '/**') {
                $error = 'Inline doc block comments are not allowed; use "// Comment." instead';
                $phpcsFile->addError($error, $stackPtr, 'DocBlock');
            }
        }//end if

        if ($tokens[$stackPtr]['content'][0] === '#') {
            $error = 'Perl-style comments are not allowed; use "// Comment." instead';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'WrongStyle');
            if ($fix === true) {
                $comment = ltrim($tokens[$stackPtr]['content'], "# \t");
                $phpcsFile->fixer->replaceToken($stackPtr, "// $comment");
            }
        }

        // We don't want end of block comments. Check if the last token before the
        // comment is a closing curly brace.
        $previousContent = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($tokens[$previousContent]['line'] === $tokens[$stackPtr]['line']) {
            if ($tokens[$previousContent]['code'] === T_CLOSE_CURLY_BRACKET) {
                return;
            }

            // Special case for JS files.
            if ($tokens[$previousContent]['code'] === T_COMMA
                || $tokens[$previousContent]['code'] === T_SEMICOLON
            ) {
                $lastContent = $phpcsFile->findPrevious(T_WHITESPACE, ($previousContent - 1), null, true);
                if ($tokens[$lastContent]['code'] === T_CLOSE_CURLY_BRACKET) {
                    return;
                }
            }
        }

        // Only want inline comments.
        if (substr($tokens[$stackPtr]['content'], 0, 2) !== '//') {
            return;
        }

        $comment = rtrim($tokens[$stackPtr]['content']);

        // Allow pure comment separators only (look for comments having at least 10 hyphens).
        if (preg_match('!^// (.*?)(-{10})(.*)$!', $comment, $matches)) {
            // It's a comment separator.
            // Verify it's pure.
            $wrongcharsfound = trim(str_replace('-', '', $matches[1] . $matches[3]));
            if ($wrongcharsfound !== '') {
                $error = 'Comment separators are not allowed to contain other chars buy hyphens (-). Found: (%s)';
                // Basic clean dupes for notification.
                $wrongcharsfound = implode(array_keys(array_flip(preg_split('//', $wrongcharsfound, -1, PREG_SPLIT_NO_EMPTY))));
                $data = array($wrongcharsfound);
                $phpcsFile->addWarning($error, $stackPtr, 'IncorrectCommentSeparator', $data);
            }
            // Verify length between 20 and 120.
            $hyphencount = strlen($matches[1] . $matches[2] . $matches[3]);
            if ($hyphencount < 20 or $hyphencount > 120) {
                $error = 'Comment separators length must contain 20-120 chars, %s found';
                $phpcsFile->addWarning($error, $stackPtr, 'WrongCommentSeparatorLength', array($hyphencount));
            }
            // Verify it's the first token in the line.
            $prevToken = $phpcsFile->findPrevious(
                Tokens::$emptyTokens,
                ($stackPtr - 1),
                null,
                true
            );
            if (!empty($prevToken) and $tokens[$prevToken]['line'] == $tokens[$stackPtr]['line']) {
                $error = 'Comment separators must be the unique text in the line, code found before';
                $phpcsFile->addWarning($error, $stackPtr, 'WrongCommentCodeFoundBefore', array());
            }
            // Don't want to continue processing the comment separator.
            return;
        }

        $commentTokens = [$stackPtr];

        $nextComment = $stackPtr;
        $lastComment = $stackPtr;
        while (($nextComment = $phpcsFile->findNext(T_COMMENT, ($nextComment + 1), null, false)) !== false) {
            if ($tokens[$nextComment]['line'] !== ($tokens[$lastComment]['line'] + 1)) {
                break;
            }

            // Only want inline comments.
            if (substr($tokens[$nextComment]['content'], 0, 2) !== '//') {
                break;
            }

            // There is a comment on the very next line. If there is
            // no code between the comments, they are part of the same
            // comment block.
            $prevNonWhitespace = $phpcsFile->findPrevious(T_WHITESPACE, ($nextComment - 1), $lastComment, true);
            if ($prevNonWhitespace !== $lastComment) {
                break;
            }

            $commentTokens[] = $nextComment;
            $lastComment     = $nextComment;
        }//end while

        $commentText = '';
        foreach ($commentTokens as $lastCommentToken) {
            $comment = rtrim($tokens[$lastCommentToken]['content']);

            // Count slashes.
            $slashCount = strlen(preg_replace('!^(/*).*!', '\\1', trim($comment)));

            if ($slashCount > 2) {
                $error = '%s slashes comments are not allowed; use "// Comment." instead';
                $data = array($slashCount);
                $phpcsFile->addError($error, $lastCommentToken, 'WrongStyle', $data);
            }

            if (trim(substr($comment, $slashCount)) === '') {
                continue;
            }

            $spaceCount = 0;
            $tabFound   = false;

            $commentLength = strlen($comment);
            for ($i = $slashCount; $i < $commentLength; $i++) {
                if ($comment[$i] === "\t") {
                    $tabFound = true;
                    break;
                }

                if ($comment[$i] !== ' ') {
                    break;
                }

                $spaceCount++;
            }

            $fix = false;
            if ($tabFound === true) {
                $error = 'Tab found before comment text; expected "// %s" but found "%s"';
                $data  = [
                    ltrim(substr($comment, $slashCount)),
                    $comment,
                ];
                $fix   = $phpcsFile->addFixableError($error, $lastCommentToken, 'TabBefore', $data);
            } else if ($spaceCount === 0) {
                $error = 'No space found before comment text; expected "// %s" but found "%s"';
                $data  = [
                    substr($comment, $slashCount),
                    $comment,
                ];
                $fix   = $phpcsFile->addFixableError($error, $lastCommentToken, 'NoSpaceBefore', $data);
            } else if ($spaceCount > 1) {
                $error = 'Expected 1 space before comment text but found %s; use block comment if you need indentation';
                $data  = [
                    $spaceCount,
                    substr($comment, ($slashCount + $spaceCount)),
                    $comment,
                ];
                $fix   = $phpcsFile->addFixableError($error, $lastCommentToken, 'SpacingBefore', $data);
            }//end if

            if ($fix === true) {
                $newComment = '// '.ltrim($tokens[$lastCommentToken]['content'], "/\t ");
                $phpcsFile->fixer->replaceToken($lastCommentToken, $newComment);
            }

            $commentText .= trim(substr($tokens[$lastCommentToken]['content'], $slashCount));
        }//end foreach

        if ($commentText === '') {
            $error = 'Blank comments are not allowed';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Empty');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken($stackPtr, '');
            }

            return ($lastCommentToken + 1);
        }

        // Respect eslint configuration comments in JS files.
        if ($phpcsFile->tokenizerType === 'JS' && preg_match('!^eslint(-|\s)!', $commentText)) {
            return;
        }

        // Enforce capital letter, digit or 3-dots sequence. Also allow @codeCoverageIgnore
        // for better handling unreachable/uncovered code for coverage purposes.
        if (preg_match('!^([A-Z0-9]|\.{3}|@codeCoverageIgnore)!', $commentText) === 0) {
            $error = 'Inline comments must start with a capital letter, digit or 3-dots sequence';
            $phpcsFile->addWarning($error, $stackPtr, 'NotCapital');
        }

        // Only check the end of comment character if the start of the comment
        // is a letter, indicating that the comment is just standard text.
        if (preg_match('/^\p{L}/u', $commentText) === 1) {
            $commentCloser   = $commentText[(strlen($commentText) - 1)];
            $acceptedClosers = [
                'full-stops'        => '.',
                'exclamation marks' => '!',
                'or question marks' => '?',
            ];

            if (in_array($commentCloser, $acceptedClosers, true) === false) {
                $error = 'Inline comments must end in %s';
                $ender = '';
                foreach ($acceptedClosers as $closerName => $symbol) {
                    $ender .= ' '.$closerName.',';
                }

                $ender = trim($ender, ' ,');
                $data  = [$ender];
                $phpcsFile->addWarning($error, $lastCommentToken, 'InvalidEndChar', $data);
            }
        }

        return ($lastCommentToken + 1);

        // IMPORTANT NOTE FOLLOWS !!!!
        //
        // This code is unreachable. It detects if there are blank lines after comments.
        // As far as there was no consensus, it's not checked.
        // Note that this used to be excluded from ruleset.xml using:
        // <rule ref="moddle.Commenting.InlineComment.SpacingAfter">
        // <severity>0</severity>
        // </rule>
        // But it doesn't work anymore with PHPCS3.x (neither with "moodle" or "MoodleHQ\MoodleCS\moodle"),
        // it seems tha there is a problem if the 2 names don't match.
        // Hence, unreachable. Ugly but ok, so far. Don't add any custom code below.

        // Finally, the line below the last comment cannot be empty if this inline
        // comment is on a line by itself.
        if ($tokens[$previousContent]['line'] < $tokens[$stackPtr]['line']) {
            $next = $phpcsFile->findNext(T_WHITESPACE, ($lastCommentToken + 1), null, true);
            if ($next === false) {
                // Ignore if the comment is the last non-whitespace token in a file.
                return ($lastCommentToken + 1);
            }

            if ($tokens[$next]['code'] === T_DOC_COMMENT_OPEN_TAG) {
                // If this inline comment is followed by a docblock,
                // ignore spacing as docblock/function etc spacing rules
                // are likely to conflict with our rules.
                return ($lastCommentToken + 1);
            }

            $errorCode = 'SpacingAfter';

            if (isset($tokens[$stackPtr]['conditions']) === true) {
                $conditions   = $tokens[$stackPtr]['conditions'];
                $type         = end($conditions);
                $conditionPtr = key($conditions);

                if (($type === T_FUNCTION || $type === T_CLOSURE)
                    && $tokens[$conditionPtr]['scope_closer'] === $next
                ) {
                    $errorCode = 'SpacingAfterAtFunctionEnd';
                }
            }

            for ($i = ($lastCommentToken + 1); $i < $phpcsFile->numTokens; $i++) {
                if ($tokens[$i]['line'] === ($tokens[$lastCommentToken]['line'] + 1)) {
                    if ($tokens[$i]['code'] !== T_WHITESPACE) {
                        return ($lastCommentToken + 1);
                    }
                } else if ($tokens[$i]['line'] > ($tokens[$lastCommentToken]['line'] + 1)) {
                    break;
                }
            }

            $error = 'There must be no blank line following an inline comment';
            $fix   = $phpcsFile->addFixableError($error, $lastCommentToken, $errorCode);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                for ($i = ($lastCommentToken + 1); $i < $next; $i++) {
                    if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                        break;
                    }

                    $phpcsFile->fixer->replaceToken($i, '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        }//end if

        return ($lastCommentToken + 1);

    }//end process()

    /**
     * This looks if there is a valid "return new class extends" expression allowed to have phpdoc block.
     *
     * @param File $file The file being scanned.
     * @param int $pointer The position in the stack.
     * @return bool true if is an allowed to have phpdoc block return new class code.
     */
    protected function is_return_new_class_extends(File $file, $pointer) {

        $ignoredtokens = Tokens::$emptyTokens;

        $tokens = $file->getTokens();

        // Detect 'return'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_RETURN) {
            return false;
        }

        // Detect 'new'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_NEW) {
            return false;
        }

        // Detect 'class'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_ANON_CLASS) {
            return false;
        }

        // Detect 'extends'.
        $pointer = $file->findNext($ignoredtokens, $pointer + 1, null, true);
        if ($tokens[$pointer]['code'] !== T_EXTENDS) {
            return false;
        }

        // Found a valid "return new class extends" expression, phpdoc block allowed.
        return true;
    }// end is_return_new_class_extends()

}//end class
