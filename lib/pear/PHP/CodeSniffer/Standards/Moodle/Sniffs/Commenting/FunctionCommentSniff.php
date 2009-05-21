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
 * Parses and verifies the doc comments for functions.
 *
 * @package   lib-pear-php-codesniffer-standards-moodle-sniffs-commenting
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (class_exists('PHP_CodeSniffer_CommentParser_FunctionCommentParser', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_CommentParser_FunctionCommentParser not found');
}

/**
 * Parses and verifies the doc comments for functions.
 *
 * Verifies that :
 * <ul>
 *  <li>A comment exists</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Parameter names represent those in the method.</li>
 *  <li>Parameter comments are in the correct order</li>
 *  <li>Parameter comments are complete</li>
 *  <li>A space is present before the first and after the last parameter</li>
 *  <li>A return type exists</li>
 *  <li>There must be one blank line between body and headline comments.</li>
 *  <li>Any throw tag must have an exception class.</li>
 * </ul>
 *
 * @copyright 2008 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_commenting_functioncommentsniff implements php_codesniffer_sniff
{

    /**
     * The name of the method that we are currently processing.
     *
     * @var string
     */
    private $_methodname = '';

    /**
     * The position in the stack where the fucntion token was found.
     *
     * @var int
     */
    private $_functiontoken = null;

    /**
     * The position in the stack where the class token was found.
     *
     * @var int
     */
    private $_classtoken = null;

    /**
     * The function comment parser for the current method.
     *
     * @var PHP_CodeSniffer_Comment_Parser_FunctionCommentParser
     */
    protected $commentParser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentFile = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FUNCTION);

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
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr)
    {
        $find = array(
                 T_COMMENT,
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentend = $phpcsfile->findPrevious($find, ($stackptr - 1));

        if ($commentend === false) {
            return;
        }

        $this->currentFile = $phpcsfile;
        $tokens            = $phpcsfile->gettokens();

        // If the token that we found was a class or a function, then this
        // function has no doc comment.
        $code = $tokens[$commentend]['code'];

        if ($code === T_COMMENT) {
            $error = 'You must use "/**" style comments for a function comment';
            $phpcsfile->adderror($error, $stackptr);
            return;
        } else if ($code !== T_DOC_COMMENT) {
            $phpcsfile->adderror('Missing function doc comment', $stackptr);
            return;
        }

        // If there is any code between the function keyword and the doc block
        // then the doc block is not for us.
        $ignore    = PHP_CodeSniffer_tokens::$scopeModifiers;
        $ignore[]  = T_STATIC;
        $ignore[]  = T_WHITESPACE;
        $ignore[]  = T_ABSTRACT;
        $ignore[]  = T_FINAL;
        $prevtoken = $phpcsfile->findPrevious($ignore, ($stackptr - 1), null, true);
        if ($prevtoken !== $commentend) {
            $phpcsfile->adderror('Missing function doc comment', $stackptr);
            return;
        }

        $this->_functiontoken = $stackptr;

        foreach ($tokens[$stackptr]['conditions'] as $condPtr => $condition) {
            if ($condition === T_CLASS || $condition === T_INTERFACE) {
                $this->_classtoken = $condPtr;
                break;
            }
        }

        // If the first T_OPEN_TAG is right before the comment, it is probably
        // a file comment.
        $commentstart = ($phpcsfile->findPrevious(T_DOC_COMMENT, ($commentend - 1), null, true) + 1);
        $prevtoken    = $phpcsfile->findPrevious(T_WHITESPACE, ($commentstart - 1), null, true);
        if ($tokens[$prevtoken]['code'] === T_OPEN_TAG) {
            // Is this the first open tag?
            if ($stackptr === 0 || $phpcsfile->findPrevious(T_OPEN_TAG, ($prevtoken - 1)) === false) {
                $phpcsfile->adderror('Missing function doc comment', $stackptr);
                return;
            }
        }

        $comment           = $phpcsfile->gettokensAsString($commentstart, ($commentend - $commentstart + 1));
        $this->_methodname = $phpcsfile->getDeclarationname($stackptr);

        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment, $phpcsfile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getlinewithinComment() + $commentstart);
            $phpcsfile->adderror($e->getMessage(), $line);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = 'Function doc comment is empty';
            $phpcsfile->adderror($error, $commentstart);
            return;
        }

        $this->processParams($commentstart);
        $this->processReturn($commentstart, $commentend);
        $this->processThrows($commentstart);

        // No extra newline before short description.
        $short        = $comment->getShortComment();
        $newlinecount = 0;
        $newlineSpan  = strspn($short, $phpcsfile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before function comment short description";
            $phpcsfile->adderror($error, ($commentstart + 1));
        }

        $newlinecount = (substr_count($short, $phpcsfile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getlongcomment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsfile->eolChar);
            if ($newlineBetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in function comment';
                $phpcsfile->adderror($error, ($commentstart + $newlinecount + 1));
            }

            $newlinecount += $newlineBetween;
        }

        // Exactly one blank line before tags.
        $params = $this->commentParser->gettagOrders();
        if (count($params) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = 'There must be exactly one blank line before the tags in function comment';
                if ($long !== '') {
                    $newlinecount += (substr_count($long, $phpcsfile->eolChar) - $newlineSpan + 1);
                }

                $phpcsfile->addwarning($error, ($commentstart + $newlinecount));
                $short = rtrim($short, $phpcsfile->eolChar.' ');
            }
        }

    }


    /**
     * Process any throw tags that this function comment has.
     *
     * @param int $commentstart The position in the stack where the
     *                          comment started.
     *
     * @return void
     */
    protected function processThrows($commentstart)
    {
        if (count($this->commentParser->getThrows()) === 0) {
            return;
        }

        foreach ($this->commentParser->getThrows() as $throw) {

            $exception = $throw->getValue();
            $errorpos  = ($commentstart + $throw->getline());

            if ($exception === '') {
                $error = '@throws tag must contain the exception class name';
                $this->currentFile->adderror($error, $errorpos);
            }
        }

    }


    /**
     * Process the return comment of this function comment.
     *
     * @param int $commentstart The position in the stack where the comment started.
     * @param int $commentend   The position in the stack where the comment ended.
     *
     * @return void
     */
    protected function processReturn($commentstart, $commentend)
    {
        // Skip constructor and destructor.
        $classname = '';
        if ($this->_classtoken !== null) {
            $classname = $this->currentFile->getDeclarationname($this->_classtoken);
            $classname = strtolower(ltrim($classname, '_'));
        }

        $methodname      = strtolower(ltrim($this->_methodname, '_'));
        $isspecialmethod = ($this->_methodname === '__construct' || $this->_methodname === '__destruct');

        if ($isspecialmethod === false && $methodname !== $classname) {
            // Report missing return tag.
            if ($this->commentParser->getReturn() === null) {
                $error = 'Missing @return tag in function comment';
                $this->currentFile->adderror($error, $commentend);
            } else if (trim($this->commentParser->getReturn()->getRawcontent()) === '') {
                $error    = '@return tag is empty in function comment';
                $errorpos = ($commentstart + $this->commentParser->getReturn()->getline());
                $this->currentFile->adderror($error, $errorpos);
            }
        }

    }


    /**
     * Process the function parameter comments.
     *
     * @param int $commentstart The position in the stack where
     *                          the comment started.
     *
     * @return void
     */
    protected function processParams($commentstart)
    {
        $realParams = $this->currentFile->getmethodParameters($this->_functiontoken);

        $params      = $this->commentParser->getParams();
        $foundParams = array();

        if (empty($params) === false) {

            $lastParm = (count($params) - 1);
            if (substr_count($params[$lastParm]->getWhitespaceAfter(), $this->currentFile->eolChar) !== 2) {
                $error    = 'Last parameter comment requires a blank newline after it';
                $errorpos = ($params[$lastParm]->getline() + $commentstart);
                $this->currentFile->addwarning($error, $errorpos);
            }

            // Parameters must appear immediately after the comment.
            if ($params[0]->getOrder() !== 2) {
                $error    = 'Parameters must appear immediately after the comment';
                $errorpos = ($params[0]->getline() + $commentstart);
                $this->currentFile->adderror($error, $errorpos);
            }

            $previousParam      = null;
            $spaceBeforeVar     = 10000;
            $spaceBeforeComment = 10000;
            $longestType        = 0;
            $longestVar         = 0;

            foreach ($params as $param) {

                $paramComment = trim($param->getComment());
                $errorpos     = ($param->getline() + $commentstart);

                // Make sure that there is only one space before the var type.
                if ($param->getWhitespaceBeforeType() !== ' ') {
                    $error = 'Expected 1 space before variable type';
                    $this->currentFile->addwarning($error, $errorpos);
                }

                $spacecount = substr_count($param->getWhitespaceBeforeVarname(), ' ');
                if ($spacecount < $spaceBeforeVar) {
                    $spaceBeforeVar = $spacecount;
                    $longestType    = $errorpos;
                }

                $spacecount = substr_count($param->getWhitespaceBeforeComment(), ' ');

                if ($spacecount < $spaceBeforeComment && $paramComment !== '') {
                    $spaceBeforeComment = $spacecount;
                    $longestVar         = $errorpos;
                }

                // Make sure they are in the correct order,
                // and have the correct name.
                $pos = $param->getposition();

                $paramname = ($param->getVarname() !== '') ? $param->getVarname() : '[ UNKNOWN ]';

                if ($previousParam !== null) {
                    $previousname = ($previousParam->getVarname() !== '') ? $previousParam->getVarname() : 'UNKNOWN';

                    // Check to see if the parameters align properly.
                    if ($param->alignsVariableWith($previousParam) === false) {
                        $error = 'The variable names for parameters '.$previousname.' ('.($pos - 1).') and '.$paramname.' ('.$pos.') do not align';
                        $this->currentFile->addwarning($error, $errorpos);
                    }

                    if ($param->alignsCommentWith($previousParam) === false) {
                        $error = 'The comments for parameters '.$previousname.' ('.($pos - 1).') and '.$paramname.' ('.$pos.') do not align';
                        $this->currentFile->addwarning($error, $errorpos);
                    }
                }

                // Make sure the names of the parameter comment matches the
                // actual parameter.
                if (isset($realParams[($pos - 1)]) === true) {
                    $realname      = $realParams[($pos - 1)]['name'];
                    $foundParams[] = $realname;
                    // Append ampersand to name if passing by reference.
                    if ($realParams[($pos - 1)]['pass_by_reference'] === true) {
                        $realname = '&'.$realname;
                    }

                    if ($realname !== $param->getVarname()) {
                        $error  = 'Doc comment var "'.$paramname;
                        $error .= '" does not match actual variable name "'.$realname;
                        $error .= '" at position '.$pos;

                        $this->currentFile->adderror($error, $errorpos);
                    }
                } else {
                    // We must have an extra parameter comment.
                    $error = 'Superfluous doc comment at position '.$pos;
                    $this->currentFile->adderror($error, $errorpos);
                }

                if ($param->getVarname() === '') {
                    $error = 'Missing parameter name at position '.$pos;
                     $this->currentFile->adderror($error, $errorpos);
                }

                if ($param->getType() === '') {
                    $error = 'Missing type at position '.$pos;
                    $this->currentFile->adderror($error, $errorpos);
                }

                if ($paramComment === '') {
                    $error = 'Missing comment for param "'.$paramname.'" at position '.$pos;
                    $this->currentFile->adderror($error, $errorpos);
                }

                $previousParam = $param;

            }

            if ($spaceBeforeVar !== 1 && $spaceBeforeVar !== 10000 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest type';
                $this->currentFile->adderror($error, $longestType);
            }

            if ($spaceBeforeComment !== 1 && $spaceBeforeComment !== 10000) {
                $error = 'Expected 1 space after the longest variable name';
                $this->currentFile->adderror($error, $longestVar);
            }

        }

        $realnames = array();
        foreach ($realParams as $realParam) {
            $realnames[] = $realParam['name'];
        }

        // Report and missing comments.
        $diff = array_diff($realnames, $foundParams);
        foreach ($diff as $neededParam) {
            if (count($params) !== 0) {
                $errorpos = ($params[(count($params) - 1)]->getline() + $commentstart);
            } else {
                $errorpos = $commentstart;
            }

            $error = 'Doc comment for "'.$neededParam.'" missing';
            $this->currentFile->adderror($error, $errorpos);
        }

    }


}

?>
