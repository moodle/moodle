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
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-commenting
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * @copyright 2009 Nicolas Connault
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

        $this->currentfile = $phpcsfile;

        $tokens = $phpcsfile->gettokens();
        $type   = strtolower($tokens[$stackptr]['content']);
        $find   = array(
                   T_ABSTRACT,
                   T_WHITESPACE,
                   T_FINAL,
                  );

        // Extract the class comment docblock.
        $commentend = $phpcsfile->findprevious($find, ($stackptr - 1), null, true);

        if ($commentend !== false && $tokens[$commentend]['code'] === T_COMMENT) {
            $phpcsfile->adderror("You must use \"/**\" style comments for a $type comment", $stackptr);
            return;

        } else if ($commentend === false || $tokens[$commentend]['code'] !== T_DOC_COMMENT) {
            $phpcsfile->adderror("Missing $type doc comment", $stackptr);
            return;
        }

        $commentstart = ($phpcsfile->findprevious(T_DOC_COMMENT, ($commentend - 1), null, true) + 1);
        $commentnext  = $phpcsfile->findprevious(T_WHITESPACE, ($commentend + 1), $stackptr, false, $phpcsfile->eolChar);

        // Distinguish file and class comment.
        $prevclasstoken = $phpcsfile->findprevious(T_CLASS, ($stackptr - 1));

        if ($prevclasstoken === false) {
            // This is the first class token in this file, need extra checks.
            $prevnoncomment = $phpcsfile->findprevious(T_DOC_COMMENT, ($commentstart - 1), null, true);

            if ($prevnoncomment !== false) {
                $prevcomment = $phpcsfile->findprevious(T_DOC_COMMENT, ($prevnoncomment - 1));

                if ($prevcomment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlinetoken = $phpcsfile->findnext(T_WHITESPACE, ($commentend + 1), $stackptr, false, $phpcsfile->eolChar);

                    if ($newlinetoken !== false) {
                        $newlinetoken = $phpcsfile->findnext(T_WHITESPACE, ($newlinetoken + 1), $stackptr, false, $phpcsfile->eolChar);

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

        $comment = $phpcsfile->gettokensAsString($commentstart, ($commentend - $commentstart + 1));

        // Parse the class comment.docblock.
        try {
            $this->commentparser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsfile);
            $this->commentparser->parse();

        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getlinewithinComment() + $commentstart);
            $phpcsfile->adderror($e->getMessage(), $line);
            return;
        }

        $comment = $this->commentparser->getComment();

        if (is_null($comment) === true) {
            $error = ucfirst($type).' doc comment is empty';
            $phpcsfile->adderror($error, $commentstart);
            return;
        }

        // No extra newline before short description.
        $short        = $comment->getShortComment();
        $newlinecount = 0;
        $newlinespan  = strspn($short, $phpcsfile->eolChar);

        if ($short !== '' && $newlinespan > 0) {
            $line  = ($newlinespan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before $type comment short description";
            $phpcsfile->adderror($error, ($commentstart + 1));
        }

        $newlinecount = (substr_count($short, $phpcsfile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getlongcomment();

        if (empty($long) === false) {
            $between        = $comment->getWhiteSpacebetween();
            $newlinebetween = substr_count($between, $phpcsfile->eolChar);

            if ($newlinebetween !== 2) {
                $error = "There must be exactly one blank line between descriptions in $type comments";
                $phpcsfile->adderror($error, ($commentstart + $newlinecount + 1));
            }

            $newlinecount += $newlinebetween;
        }

        // Exactly one blank line before tags.
        $tags = $this->commentparser->gettagOrders();

        if (count($tags) > 1) {
            $newlinespan = $comment->getNewlineAfter();

            if ($newlinespan !== 2) {
                $error = "There must be exactly one blank line before the tags in $type comments";

                if ($long !== '') {
                    $newlinecount += (substr_count($long, $phpcsfile->eolChar) - $newlinespan + 1);
                }

                $phpcsfile->addwarning($error, ($commentstart + $newlinecount));
                $short = rtrim($short, $phpcsfile->eolChar.' ');
            }
        }

        // Check each tag.
        $this->processtags($commentstart, $commentend);

    }


    /**
     * Process the version tag.
     *
     * @param int $errorpos The line number where the error occurs.
     *
     * @return void
     */
    protected function processversion($errorpos) {
        $version = $this->commentparser->getVersion();

        if ($version !== null) {
            $content = $version->getcontent();
            $matches = array();

            if (empty($content) === true) {
                $error = 'content missing for @version tag in doc comment';
                $this->currentfile->adderror($error, $errorpos);

            } else if ((strstr($content, 'Release:') === false)) {
                $error = "Invalid version \"$content\" in doc comment; consider \"Release: <package_version>\" instead";
                $this->currentfile->addwarning($error, $errorpos);
            }
        }

    }
}
