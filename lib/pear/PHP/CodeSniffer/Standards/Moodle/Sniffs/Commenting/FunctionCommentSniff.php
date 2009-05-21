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
 * @package    moodlecore
 * @subpackage lib-pear-php-codesniffer-standards-moodle-sniffs-commenting
 * @copyright  2009 Nicolas Connault
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 *  <li>parameter names represent those in the method.</li>
 *  <li>parameter comments are in the correct order</li>
 *  <li>parameter comments are complete</li>
 *  <li>A space is present before the first and after the last parameter</li>
 *  <li>A return type exists</li>
 *  <li>There must be one blank line between body and headline comments.</li>
 *  <li>Any throw tag must have an exception class.</li>
 * </ul>
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodle_sniffs_commenting_functioncommentsniff implements php_codesniffer_sniff {

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
    protected $commentparser = null;

    /**
     * The current PHP_CodeSniffer_File object we are processing.
     *
     * @var PHP_CodeSniffer_File
     */
    protected $currentfile = null;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register() {
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
    public function process(PHP_CodeSniffer_File $phpcsfile, $stackptr) {
        $find = array(
                 T_COMMENT,
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentend = $phpcsfile->findprevious($find, ($stackptr - 1));

        if ($commentend === false) {
            return;
        }

        $this->currentfile = $phpcsfile;
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
        $prevtoken = $phpcsfile->findprevious($ignore, ($stackptr - 1), null, true);

        if ($prevtoken !== $commentend) {
            $phpcsfile->adderror('Missing function doc comment', $stackptr);
            return;
        }

        $this->_functiontoken = $stackptr;

        foreach ($tokens[$stackptr]['conditions'] as $condptr => $condition) {

            if ($condition === T_CLASS || $condition === T_INTERFACE) {
                $this->_classtoken = $condptr;
                break;
            }
        }

        // If the first T_OPEN_TAG is right before the comment, it is probably
        // a file comment.
        $commentstart = ($phpcsfile->findprevious(T_DOC_COMMENT, ($commentend - 1), null, true) + 1);
        $prevtoken    = $phpcsfile->findprevious(T_WHITESPACE, ($commentstart - 1), null, true);

        if ($tokens[$prevtoken]['code'] === T_OPEN_TAG) {
            // Is this the first open tag?

            if ($stackptr === 0 || $phpcsfile->findprevious(T_OPEN_TAG, ($prevtoken - 1)) === false) {
                $phpcsfile->adderror('Missing function doc comment', $stackptr);
                return;
            }
        }

        $comment           = $phpcsfile->gettokensAsString($commentstart, ($commentend - $commentstart + 1));
        $this->_methodname = $phpcsfile->getDeclarationname($stackptr);

        try {
            $this->commentparser = new PHP_CodeSniffer_CommentParser_FunctionCommentParser($comment, $phpcsfile);
            $this->commentparser->parse();

        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getlinewithinComment() + $commentstart);
            $phpcsfile->adderror($e->getMessage(), $line);
            return;
        }

        $comment = $this->commentparser->getComment();

        if (is_null($comment) === true) {
            $error = 'Function doc comment is empty';
            $phpcsfile->adderror($error, $commentstart);
            return;
        }

        $this->processparams($commentstart);
        $this->processreturn($commentstart, $commentend);
        $this->processthrows($commentstart);

        // No extra newline before short description.
        $short        = $comment->getShortComment();
        $newlinecount = 0;
        $newlinespan  = strspn($short, $phpcsfile->eolChar);

        if ($short !== '' && $newlinespan > 0) {
            $line  = ($newlinespan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before function comment short description";
            $phpcsfile->adderror($error, ($commentstart + 1));
        }

        $newlinecount = (substr_count($short, $phpcsfile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getlongcomment();

        if (empty($long) === false) {
            $between        = $comment->getWhiteSpacebetween();
            $newlinebetween = substr_count($between, $phpcsfile->eolChar);

            if ($newlinebetween !== 2) {
                $error = 'There must be exactly one blank line between descriptions in function comment';
                $phpcsfile->adderror($error, ($commentstart + $newlinecount + 1));
            }

            $newlinecount += $newlinebetween;
        }

        // Exactly one blank line before tags.
        $params = $this->commentparser->gettagOrders();

        if (count($params) > 1) {
            $newlinespan = $comment->getNewlineAfter();

            if ($newlinespan !== 2) {
                $error = 'There must be exactly one blank line before the tags in function comment';

                if ($long !== '') {
                    $newlinecount += (substr_count($long, $phpcsfile->eolChar) - $newlinespan + 1);
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
    protected function processthrows($commentstart) {

        if (count($this->commentparser->getthrows()) === 0) {
            return;
        }

        foreach ($this->commentparser->getthrows() as $throw) {

            $exception = $throw->getvalue();
            $errorpos  = ($commentstart + $throw->getline());

            if ($exception === '') {
                $error = '@throws tag must contain the exception class name';
                $this->currentfile->adderror($error, $errorpos);
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
    protected function processreturn($commentstart, $commentend) {
        // Skip constructor and destructor.
        $classname = '';

        if ($this->_classtoken !== null) {
            $classname = $this->currentfile->getdeclarationname($this->_classtoken);
            $classname = strtolower(ltrim($classname, '_'));
        }

        $methodname      = strtolower(ltrim($this->_methodname, '_'));
        $isspecialmethod = ($this->_methodname === '__construct' || $this->_methodname === '__destruct');

        if ($isspecialmethod === false && $methodname !== $classname) {
            // Report missing return tag.
            if ($this->commentparser->getreturn() === null) {
                $error = 'Missing @return tag in function comment';
                $this->currentfile->adderror($error, $commentend);

            } else if (trim($this->commentparser->getreturn()->getrawcontent()) === '') {
                $error    = '@return tag is empty in function comment';
                $errorpos = ($commentstart + $this->commentparser->getreturn()->getline());
                $this->currentfile->adderror($error, $errorpos);
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
    protected function processparams($commentstart) {
        $realparams = $this->currentfile->getmethodparameters($this->_functiontoken);

        $params      = $this->commentparser->getparams();
        $foundparams = array();

        if (empty($params) === false) {

            $lastparm = (count($params) - 1);

            if (substr_count($params[$lastparm]->getwhitespaceafter(), $this->currentfile->eolChar) !== 2) {
                $error    = 'Last parameter comment requires a blank newline after it';
                $errorpos = ($params[$lastparm]->getline() + $commentstart);
                $this->currentfile->addwarning($error, $errorpos);
            }

            $previousparam      = null;
            $spacebeforevar     = 10000;
            $spacebeforecomment = 10000;
            $longesttype        = 0;
            $longestvar         = 0;

            foreach ($params as $param) {

                $paramcomment = trim($param->getcomment());
                $errorpos     = ($param->getline() + $commentstart);

                // Make sure that there is only one space before the var type.
                if ($param->getwhitespacebeforetype() !== ' ') {
                    $error = 'Expected 1 space before variable type';
                    $this->currentfile->addwarning($error, $errorpos);
                }

                $spacecount = substr_count($param->getwhitespacebeforevarname(), ' ');

                if ($spacecount < $spacebeforevar) {
                    $spacebeforevar = $spacecount;
                    $longesttype    = $errorpos;
                }

                $spacecount = substr_count($param->getwhitespacebeforecomment(), ' ');

                if ($spacecount < $spacebeforecomment && $paramcomment !== '') {
                    $spacebeforecomment = $spacecount;
                    $longestvar         = $errorpos;
                }

                // Make sure they are in the correct order,
                // and have the correct name.
                $pos = $param->getposition();

                $paramname = ($param->getvarname() !== '') ? $param->getvarname() : '[ UNKNOWN ]';

                if ($previousparam !== null) {
                    $previousname = ($previousparam->getvarname() !== '') ? $previousparam->getvarname() : 'UNKNOWN';

                    // Check to see if the parameters align properly.
                    if ($param->alignsvariablewith($previousparam) === false) {
                        $error = 'The variable names for parameters '.$previousname.' ('.($pos - 1).') and '.$paramname.' ('.$pos.') do not align';
                        $this->currentfile->addwarning($error, $errorpos);
                    }

                    if ($param->alignsCommentWith($previousparam) === false) {
                        $error = 'The comments for parameters '.$previousname.' ('.($pos - 1).') and '.$paramname.' ('.$pos.') do not align';
                        $this->currentfile->addwarning($error, $errorpos);
                    }
                }

                // Make sure the names of the parameter comment matches the
                // actual parameter.
                if (isset($realparams[($pos - 1)]) === true) {
                    $realname      = $realparams[($pos - 1)]['name'];
                    $foundparams[] = $realname;
                    // Append ampersand to name if passing by reference.
                    if ($realparams[($pos - 1)]['pass_by_reference'] === true) {
                        $realname = '&'.$realname;
                    }

                    if ($realname !== $param->getvarname()) {
                        $error  = 'Doc comment var "'.$paramname;
                        $error .= '" does not match actual variable name "'.$realname;
                        $error .= '" at position '.$pos;

                        $this->currentfile->adderror($error, $errorpos);
                    }

                } else {
                    // We must have an extra parameter comment.
                    $error = 'Superfluous doc comment at position '.$pos;
                    $this->currentfile->adderror($error, $errorpos);
                }

                if ($param->getvarname() === '') {
                    $error = 'Missing parameter name at position '.$pos;
                     $this->currentfile->adderror($error, $errorpos);
                }

                if ($param->gettype() === '') {
                    $error = 'Missing type at position '.$pos;
                    $this->currentfile->adderror($error, $errorpos);
                }

                if ($paramcomment === '') {
                    $error = 'Missing comment for param "'.$paramname.'" at position '.$pos;
                    $this->currentfile->adderror($error, $errorpos);
                }

                $previousparam = $param;

            }

            if ($spacebeforevar !== 1 && $spacebeforevar !== 10000 && $spacebeforecomment !== 10000) {
                $error = 'Expected 1 space after the longest type';
                $this->currentfile->adderror($error, $longesttype);
            }

            if ($spacebeforecomment !== 1 && $spacebeforecomment !== 10000) {
                $error = 'Expected 1 space after the longest variable name';
                $this->currentfile->adderror($error, $longestvar);
            }

        }

        $realnames = array();

        foreach ($realparams as $realparam) {
            $realnames[] = $realparam['name'];
        }

        // Report and missing comments.
        $diff = array_diff($realnames, $foundparams);

        foreach ($diff as $neededparam) {

            if (count($params) !== 0) {
                $errorpos = ($params[(count($params) - 1)]->getline() + $commentstart);

            } else {
                $errorpos = $commentstart;
            }

            $error = 'Doc comment for "'.$neededparam.'" missing';
            $this->currentfile->adderror($error, $errorpos);
        }
    }
}
