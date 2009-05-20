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
 * Parses and verifies the doc comments for files.
 *
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-commenting
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found');
}

/**
 * Parses and verifies the doc comments for files.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>A PHP version is specified.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class moodle_sniffs_commenting_filecommentsniff implements php_codesniffer_sniff {

    /**
     * The header comment parser for the current file.
     *
     * @var PHP_CodeSniffer_Comment_Parser_ClassCommentParser
     */
    protected $commentparser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentfile = null;

    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = array(
           'category'   => array(
                            'required'       => false,
                            'allow_multiple' => false,
                            'order_text'     => 'precedes @package',
                           ),
           'package'    => array(
                            'required'       => true,
                            'allow_multiple' => false,
                            'order_text'     => 'follows @category',
                           ),
           'subpackage' => array(
                            'required'       => false,
                            'allow_multiple' => false,
                            'order_text'     => 'follows @package',
                           ),
           'author'     => array(
                            'required'       => false,
                            'allow_multiple' => true,
                            'order_text'     => 'follows @subpackage (if used) or @package',
                           ),
           'copyright'  => array(
                            'required'       => true,
                            'allow_multiple' => true,
                            'order_text'     => 'follows @author',
                           ),
           'license'    => array(
                            'required'       => true,
                            'allow_multiple' => false,
                            'order_text'     => 'follows @copyright (if used) or @author',
                           ),
           'version'    => array(
                            'required'       => false,
                            'allow_multiple' => false,
                            'order_text'     => 'follows @licence',
                           ),
           'link'       => array(
                            'required'       => false,
                            'allow_multiple' => true,
                            'order_text'     => 'follows @version',
                           ),
           'see'        => array(
                            'required'       => false,
                            'allow_multiple' => true,
                            'order_text'     => 'follows @link',
                           ),
           'since'      => array(
                            'required'       => false,
                            'allow_multiple' => false,
                            'order_text'     => 'follows @see (if used) or @link',
                           ),
           'deprecated' => array(
                            'required'       => false,
                            'allow_multiple' => false,
                            'order_text'     => 'follows @since (if used) or @see (if used) or @link',
                           ),
                );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

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
        $this->currentfile = $phpcsfile;

        // We are only interested if this is the first open tag.
        if ($stackptr !== 0) {
            if ($phpcsfile->findPrevious(T_OPEN_TAG, ($stackptr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsfile->gettokens();

        // Find the next non whitespace token.
        $commentStart = $phpcsfile->findnext(T_WHITESPACE, ($stackptr + 1), null, true);

        // Look for $Id$ and boilerplate
        if ($tokens[$commentStart]['code'] != T_COMMENT) {
            $phpcsfile->adderror('File must begin with License boilerplate', ($stackptr + 1));
            return;
        } else if (preg_match('|\$Id\$|i', $tokens[$commentStart]['content'])) {
            $phpcsfile->addwarning('$Id$ tag is no longer required, please remove.', ($stackptr + 1));
            return;
        }

        // now look for boilerplate, must be immediately after the first line
        $boilerplate = '// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.';

        $nexttoken = $phpcsfile->findnext(T_WHITESPACE, ($stackptr + 1), null, true);
        $boilerplate_lines = preg_split('/[\n\r]+/', $boilerplate);

        if (rtrim($tokens[$nexttoken]['content']) != $boilerplate_lines[0]) {
            $phpcsfile->adderror('You must include the Moodle boilerplate at the top of the file', ($nexttoken));
            return;
        }

        $boilerplate_index = 0;

        foreach ($boilerplate_lines as $line) {
            $nexttoken = $phpcsfile->findnext(T_COMMENT, ($nexttoken));

            if (rtrim($tokens[$nexttoken]['content']) != $boilerplate_lines[$boilerplate_index]) {
                $phpcsfile->adderror('Badly formatted boilerplate. Please copy-paste exactly', ($nexttoken));
                return;
            }
            $nexttoken++;
            $boilerplate_index++;
        }

        $filedoctoken = $phpcsfile->findnext(T_WHITESPACE, ($nexttoken + 1), null, true);

        if ($tokens[$filedoctoken]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($tokens[$filedoctoken]['code'] === T_COMMENT) {
            $phpcsfile->adderror('You must use "/**" style comments for a file comment', ($filedoctoken + 1));
            return;
        } else if ($filedoctoken === false || $tokens[$filedoctoken]['code'] !== T_DOC_COMMENT) {
            $phpcsfile->adderror('Missing file doc comment', ($filedoctoken + 1));
            return;
        } else {

            // Extract the header comment docblock.
            $commentEnd = ($phpcsfile->findnext(T_DOC_COMMENT, ($filedoctoken + 1), null, true) - 1);

            // Check if there is only 1 doc comment between the open tag and class token.
            $nexttoken   = array(
                            T_ABSTRACT,
                            T_CLASS,
                            T_FUNCTION,
                            T_DOC_COMMENT,
                           );
            $commentNext = $phpcsfile->findnext($nexttoken, ($commentEnd + 1));
            if ($commentNext !== false && $tokens[$commentNext]['code'] !== T_DOC_COMMENT) {
                // Found a class token right after comment doc block.
                $newlinetoken = $phpcsfile->findnext(T_WHITESPACE, ($commentEnd + 1), $commentNext, false, $phpcsfile->eolChar);
                if ($newlinetoken !== false) {
                    $newlinetoken = $phpcsfile->findnext(T_WHITESPACE, ($newlinetoken + 1), $commentNext, false, $phpcsfile->eolChar);
                    if ($newlinetoken === false) {
                        // No blank line between the class token and the doc block.
                        // The doc block is most likely a class comment.
                        $phpcsfile->adderror('Missing file doc comment', ($stackptr + 1));
                        return;
                    }
                }
            }

            $comment = $phpcsfile->gettokensAsString($filedoctoken, ($commentEnd - $filedoctoken + 1));

            // Parse the header comment docblock.
            try {
                $this->commentparser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsfile);
                $this->commentparser->parse();
            } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
                $line = ($e->getlinewithinComment() + $filedoctoken);
                $phpcsfile->adderror($e->getMessage(), $line);
                return;
            }

            $comment = $this->commentparser->getComment();
            if (is_null($comment) === true) {
                $error = 'File doc comment is empty';
                $phpcsfile->adderror($error, $filedoctoken);
                return;
            }

            // No extra newline before short description.
            $short        = $comment->getShortComment();
            $newlineCount = 0;
            $newlineSpan  = strspn($short, $phpcsfile->eolChar);
            if ($short !== '' && $newlineSpan > 0) {
                $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
                $error = "Extra $line found before file comment short description";
                $phpcsfile->adderror($error, ($filedoctoken + 1));
            }

            $newlineCount = (substr_count($short, $phpcsfile->eolChar) + 1);

            // Exactly one blank line between short and long description.
            $long = $comment->getlongcomment();
            if (empty($long) === false) {
                $between        = $comment->getWhiteSpaceBetween();
                $newlineBetween = substr_count($between, $phpcsfile->eolChar);
                if ($newlineBetween !== 2) {
                    $error = 'There should be exactly one blank line between descriptions in file comment';
                    $phpcsfile->addwarning($error, ($filedoctoken + $newlineCount + 1));
                }

                $newlineCount += $newlineBetween;
            }

            // Exactly one blank line before tags.
            $tags = $this->commentparser->getTagOrders();
            if (count($tags) > 1) {
                $newlineSpan = $comment->getNewlineAfter();
                if ($newlineSpan !== 2) {
                    $error = 'There must be exactly one blank line before the tags in file comment';
                    if ($long !== '') {
                        $newlineCount += (substr_count($long, $phpcsfile->eolChar) - $newlineSpan + 1);
                    }

                    $phpcsfile->addwarning($error, ($filedoctoken + $newlineCount));
                    $short = rtrim($short, $phpcsfile->eolChar.' ');
                }
            }

            // Check the PHP Version.
            /*
            if (strstr(strtolower($long), 'php version') === false) {
                $error = 'PHP version not specified';
                $phpcsfile->addwarning($error, $commentEnd);
            }
            */

            // Check each tag.
            $this->processTags($filedoctoken, $commentEnd);
        }

    }


    /**
     * Processes each required or optional tag.
     *
     * @param int $commentStart The position in the stack where the comment started.
     * @param int $commentEnd   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processtags($commentStart, $commentEnd) {
        $docBlock    = (get_class($this) === 'moodle_sniffs_commenting_filecommentsniff') ? 'file' : 'class';
        $foundTags   = $this->commentparser->getTagOrders();
        $orderIndex  = 0;
        $indentation = array();
        $longestTag  = 0;
        $errorPos    = 0;

        foreach ($this->tags as $tag => $info) {

            // Required tag missing.
            if ($info['required'] === true && in_array($tag, $foundTags) === false) {
                $error = "Missing @$tag tag in $docBlock comment";
                $this->currentfile->adderror($error, $commentEnd);
                continue;
            }

             // Get the line number for current tag.
            $tagName = ucfirst($tag);
            if ($info['allow_multiple'] === true) {
                $tagName .= 's';
            }

            $getMethod  = 'get'.$tagName;
            $tagElement = $this->commentparser->$getMethod();
            if (is_null($tagElement) === true || empty($tagElement) === true) {
                continue;
            }

            $errorPos = $commentStart;
            if (is_array($tagElement) === false) {
                $errorPos = ($commentStart + $tagElement->getline());
            }

            // Get the tag order.
            $foundIndexes = array_keys($foundTags, $tag);

            if (count($foundIndexes) > 1) {
                // Multiple occurance not allowed.
                if ($info['allow_multiple'] === false) {
                    $error = "Only 1 @$tag tag is allowed in a $docBlock comment";
                    $this->currentfile->adderror($error, $errorPos);
                } else {
                    // Make sure same tags are grouped together.
                    $i     = 0;
                    $count = $foundIndexes[0];
                    foreach ($foundIndexes as $index) {
                        if ($index !== $count) {
                            $errorPosIndex = ($errorPos + $tagElement[$i]->getline());
                            $error         = "@$tag tags must be grouped together";
                            $this->currentfile->adderror($error, $errorPosIndex);
                        }

                        $i++;
                        $count++;
                    }
                }
            }

            // Check tag order.
            if ($foundIndexes[0] > $orderIndex) {
                $orderIndex = $foundIndexes[0];
            } else {
                if (is_array($tagElement) === true && empty($tagElement) === false) {
                    $errorPos += $tagElement[0]->getline();
                }

                $orderText = $info['order_text'];
                $error     = "The @$tag tag is in the wrong order; the tag $orderText";
                $this->currentfile->adderror($error, $errorPos);
            }

            // Store the indentation for checking.
            $len = strlen($tag);
            if ($len > $longestTag) {
                $longestTag = $len;
            }

            if (is_array($tagElement) === true) {
                foreach ($tagElement as $key => $element) {
                    $indentation[] = array(
                                      'tag'   => $tag,
                                      'space' => $this->getIndentation($tag, $element),
                                      'line'  => $element->getline(),
                                     );
                }
            } else {
                $indentation[] = array(
                                  'tag'   => $tag,
                                  'space' => $this->getIndentation($tag, $tagElement),
                                 );
            }

            $method = 'process'.$tagName;
            if (method_exists($this, $method) === true) {
                // Process each tag if a method is defined.
                call_user_func(array($this, $method), $errorPos);
            } else {
                if (is_array($tagElement) === true) {
                    foreach ($tagElement as $key => $element) {
                        $element->process($this->currentfile, $commentStart, $docBlock);
                    }
                } else {
                     $tagElement->process($this->currentfile, $commentStart, $docBlock);
                }
            }
        }

        foreach ($indentation as $indentInfo) {
            if ($indentInfo['space'] !== 0 && $indentInfo['space'] !== ($longestTag + 1)) {
                $expected     = (($longestTag - strlen($indentInfo['tag'])) + 1);
                $space        = ($indentInfo['space'] - strlen($indentInfo['tag']));
                $error        = "@$indentInfo[tag] tag comment indented incorrectly. ";
                $error       .= "Expected $expected spaces but found $space.";
                $getTagMethod = 'get'.ucfirst($indentInfo['tag']);
                if ($this->tags[$indentInfo['tag']]['allow_multiple'] === true) {
                    $line = $indentInfo['line'];
                } else {
                    $tagElem = $this->commentparser->$getTagMethod();
                    $line    = $tagElem->getline();
                }

                $this->currentfile->addwarning($error, ($commentStart + $line));
            }
        }

    }


    /**
     * Get the indentation information of each tag.
     *
     * @param string                                   $tagName    The name of the doc comment element.
     * @param PHP_CodeSniffer_CommentParser_DocElement $tagElement The doc comment element.
     *
     * @return void
     */
    protected function getindentation($tagName, $tagElement)
    {
        if ($tagElement instanceof PHP_CodeSniffer_CommentParser_SingleElement) {
            if ($tagElement->getcontent() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforecontent(), ' '));
            }
        } else if ($tagElement instanceof PHP_CodeSniffer_CommentParser_PairElement) {
            if ($tagElement->getValue() !== '') {
                return (strlen($tagName) + substr_count($tagElement->getWhitespaceBeforeValue(), ' '));
            }
        }

        return 0;

    }


    /**
     * Process the category tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processcategory($errorPos)
    {
        $category = $this->commentparser->getCategory();
        if ($category !== null) {
            $content = $category->getcontent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newcontent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newcontent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = ucfirst($firstBit).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= ucfirst($bit).'_';
                    }

                    $validName = trim($newName, '_');
                    $error     = "Category name \"$content\" is not valid; consider \"$validName\" instead";
                    // $this->currentfile->adderror($error, $errorPos);
                }
            } else {
                $error = '@category tag must contain a name';
                $this->currentfile->adderror($error, $errorPos);
            }
        }

    }


    /**
     * Process the package tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processpackage($errorPos) {
        global $CFG;
        $package = $this->commentparser->getPackage();
        $filename = str_replace($CFG->dirroot, '', $this->currentfile->getfilename());

        // Replace slashes or backslashes in file path with dashes
        $expected_package = strtolower(str_replace('/', '-', $filename));

        if (strpos($expected_package, '-')) {
            $expected_package = strtolower(str_replace('\\', '-', $filename));
        }

        // Strip off last part: the name of the searched file
        $expected_package = substr($expected_package, 0, strrpos($expected_package, '-'));

        // Remove first dash if present
        $expected_package = ltrim($expected_package, '-');
        if ($package !== null) {
            $content = $package->getcontent();

            if ($content !== $expected_package) {
                $error = "Package name \"$content\" is not valid; \"$expected_package\" expected.";
                $this->currentfile->adderror($error, $errorPos);

            } else if ($content !== '') {
                if (!preg_match('/^[a-z\-]*$/', $content)) {
                    $error = "Package name \"$content\" is not valid; must be lower-case with optional hyphens.";
                    $this->currentfile->adderror($error, $errorPos);
                }
            } else {
                $error = '@package tag must contain a name';
                $this->currentfile->adderror($error, $errorPos);
            }
        }

    }


    /**
     * Process the subpackage tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processsubpackage($errorPos)
    {
        $package = $this->commentparser->getSubpackage();
        if ($package !== null) {
            $content = $package->getcontent();
            if ($content !== '') {
                if (PHP_CodeSniffer::isUnderscoreName($content) !== true) {
                    $newcontent = str_replace(' ', '_', $content);
                    $nameBits   = explode('_', $newcontent);
                    $firstBit   = array_shift($nameBits);
                    $newName    = strtoupper($firstBit{0}).substr($firstBit, 1).'_';
                    foreach ($nameBits as $bit) {
                        $newName .= strtoupper($bit{0}).substr($bit, 1).'_';
                    }

                    $validName = trim($newName, '_');
                    $error     = "Subpackage name \"$content\" is not valid; consider \"$validName\" instead";
                    $this->currentfile->adderror($error, $errorPos);
                }
            } else {
                $error = '@subpackage tag must contain a name';
                $this->currentfile->adderror($error, $errorPos);
            }
        }

    }


    /**
     * Process the author tag(s) that this header comment has.
     *
     * This function is different from other _process functions
     * as $authors is an array of SingleElements, so we work out
     * the errorPos for each element separately
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     *
     * @return void
     */
    protected function processauthors($commentStart)
    {
         $authors = $this->commentparser->getAuthors();
        // Report missing return.
        if (empty($authors) === false) {
            foreach ($authors as $author) {
                $errorPos = ($commentStart + $author->getline());
                $content  = $author->getcontent();
                if ($content !== '') {
                    $local = '\da-zA-Z-_+';
                    // Dot character cannot be the first or last character in the local-part.
                    $localMiddle = $local.'.\w';
                    if (preg_match('/^([^<]*)\s+<(['.$local.']['.$localMiddle.']*['.$local.']@[\da-zA-Z][-.\w]*[\da-zA-Z]\.[a-zA-Z]{2,7})>$/', $content) === 0) {
                        $error = 'content of the @author tag must be in the form "Display Name <username@example.com>"';
                        $this->currentfile->adderror($error, $errorPos);
                    }
                } else {
                    $docBlock = (get_class($this) === 'moodle_sniffs_commenting_filecommentsniff') ? 'file' : 'class';
                    $error    = "content missing for @author tag in $docBlock comment";
                    $this->currentfile->adderror($error, $errorPos);
                }
            }
        }

    }


    /**
     * Process the copyright tags.
     *
     * @param int $commentStart The position in the stack where
     *                          the comment started.
     *
     * @return void
     */
    protected function processcopyrights($commentStart)
    {
        $copyrights = $this->commentparser->getCopyrights();
        foreach ($copyrights as $copyright) {
            $errorPos = ($commentStart + $copyright->getline());
            $content  = $copyright->getcontent();
            if ($content !== '') {
                $matches = array();
                if (preg_match('/^([0-9]{4})((.{1})([0-9]{4}))? (.+)$/', $content, $matches) !== 0) {
                    // Check earliest-latest year order.
                    if ($matches[3] !== '') {
                        if ($matches[3] !== '-') {
                            $error = 'A hyphen must be used between the earliest and latest year';
                            $this->currentfile->adderror($error, $errorPos);
                        }

                        if ($matches[4] !== '' && $matches[4] < $matches[1]) {
                            $error = "Invalid year span \"$matches[1]$matches[3]$matches[4]\" found; consider \"$matches[4]-$matches[1]\" instead";
                            $this->currentfile->addwarning($error, $errorPos);
                        }
                    }
                } else {
                    $error = '@copyright tag must contain a year and the name of the copyright holder';
                    $this->currentfile->adderror($error, $errorPos);
                }
            } else {
                $error = '@copyright tag must contain a year and the name of the copyright holder';
                $this->currentfile->adderror($error, $errorPos);
            }
        }

    }


    /**
     * Process the license tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processlicense($errorPos)
    {
        $license = $this->commentparser->getLicense();
        if ($license !== null) {
            $value   = $license->getValue();
            $comment = $license->getComment();
            if ($value === '' || $comment === '') {
                $error = '@license tag must contain a URL and a license name';
                $this->currentfile->adderror($error, $errorPos);
            }
            if ($comment != 'GNU GPL v3 or later') {
                $this->currentfile->adderror('License must be "GNU GPL v3 or later", found "'.$comment.'"', $errorPos);
            }
            if ($value != 'http://www.gnu.org/copyleft/gpl.html') {
                $this->currentfile->adderror('License must be "GNU GPL v3 or later"', $errorPos);
            }
        }

    }


    /**
     * Process the version tag.
     *
     * @param int $errorPos The line number where the error occurs.
     *
     * @return void
     */
    protected function processversion($errorPos)
    {
        $version = $this->commentparser->getVersion();
        if ($version !== null) {
            $content = $version->getcontent();
            $matches = array();
            if (empty($content) === true) {
                $error = 'content missing for @version tag in file comment';
                $this->currentfile->adderror($error, $errorPos);
            } else if (strstr($content, 'CVS:') === false && strstr($content, 'SVN:') === false) {
                $error = "Invalid version \"$content\" in file comment; consider \"CVS: <cvs_id>\" or \"SVN: <svn_id>\" instead";
                $this->currentfile->addwarning($error, $errorPos);
            }
        }

    }


}

?>
