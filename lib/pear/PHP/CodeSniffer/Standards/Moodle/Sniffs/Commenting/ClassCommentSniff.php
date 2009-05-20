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
 * Parses and verifies the doc comments for classes.
 *
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-commenting
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found';
    throw new PHP_CodeSniffer_Exception($error);
}

if (class_exists('moodle_sniffs_commenting_filecommentsniff', true) === false) {
    $error = 'Class moodle_sniffs_commenting_filecommentsniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses and verifies the doc comments for classes.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_commenting_classcommentsniff extends moodle_sniffs_commenting_filecommentsniff {

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
        return array(T_CLASS, T_INTERFACE);
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
        // Modify array of required tags
        $this->tags['package']['required'] = false;
        $this->tags['copyright']['required'] = false;
        $this->tags['author']['required'] = false;

        $this->currentFile = $phpcsfile;

        $tokens = $phpcsfile->gettokens();
        $type   = strtolower($tokens[$stackptr]['content']);
        $find   = array(
                   T_ABSTRACT,
                   T_WHITESPACE,
                   T_FINAL,
                  );

        // Extract the class comment docblock.
        $commentEnd = $phpcsfile->findPrevious($find, ($stackptr - 1), null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsfile->adderror("You must use \"/**\" style comments for a $type comment", $stackptr);
            return;
        } else if ($commentEnd === false || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT) {
            $phpcsfile->adderror("Missing $type doc comment", $stackptr);
            return;
        }

        $commentStart = ($phpcsfile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $commentNext  = $phpcsfile->findPrevious(T_WHITESPACE, ($commentEnd + 1), $stackptr, false, $phpcsfile->eolChar);

        // Distinguish file and class comment.
        $prevClasstoken = $phpcsfile->findPrevious(T_CLASS, ($stackptr - 1));
        if ($prevClasstoken === false) {
            // This is the first class token in this file, need extra checks.
            $prevNonComment = $phpcsfile->findPrevious(T_DOC_COMMENT, ($commentStart - 1), null, true);
            if ($prevNonComment !== false) {
                $prevComment = $phpcsfile->findPrevious(T_DOC_COMMENT, ($prevNonComment - 1));
                if ($prevComment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlinetoken = $phpcsfile->findNext(T_WHITESPACE, ($commentEnd + 1), $stackptr, false, $phpcsfile->eolChar);
                    if ($newlinetoken !== false) {
                        $newlinetoken = $phpcsfile->findNext(T_WHITESPACE, ($newlinetoken + 1), $stackptr, false, $phpcsfile->eolChar);
                        if ($newlinetoken !== false) {
                            // Blank line between the class and the doc block.
                            // The doc block is most likely a file comment.
                            $phpcsfile->adderror("Missing $type doc comment", ($stackptr + 1));
                            return;
                        }
                    }
                }
            }
        }

        $comment = $phpcsfile->gettokensAsString($commentStart, ($commentEnd - $commentStart + 1));

        // Parse the class comment.docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsfile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getlinewithinComment() + $commentStart);
            $phpcsfile->adderror($e->getMessage(), $line);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = ucfirst($type).' doc comment is empty';
            $phpcsfile->adderror($error, $commentStart);
            return;
        }

        // No extra newline before short description.
        $short        = $comment->getShortComment();
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsfile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before $type comment short description";
            $phpcsfile->adderror($error, ($commentStart + 1));
        }

        $newlineCount = (substr_count($short, $phpcsfile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getlongcomment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsfile->eolChar);
            if ($newlineBetween !== 2) {
                $error = "There must be exactly one blank line between descriptions in $type comments";
                $phpcsfile->adderror($error, ($commentStart + $newlineCount + 1));
            }

            $newlineCount += $newlineBetween;
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = "There must be exactly one blank line before the tags in $type comments";
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsfile->eolChar) - $newlineSpan + 1);
                }

                $phpcsfile->addwarning($error, ($commentStart + $newlineCount));
                $short = rtrim($short, $phpcsfile->eolChar.' ');
            }
        }

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);

    }


    /**
     * Process the version tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processVersion($errorPos)
    {
        $version = $this->commentParser->getVersion();
        if ($version !== null) {
            $content = $version->getcontent();
            $matches = array();
            if (empty($content) === true) {
                $error = 'content missing for @version tag in doc comment';
                $this->currentFile->adderror($error, $errorPos);
            } else if ((strstr($content, 'Release:') === false)) {
                $error = "Invalid version \"$content\" in doc comment; consider \"Release: <package_version>\" instead";
                $this->currentFile->addwarning($error, $errorPos);
            }
        }

    }
}

?>
