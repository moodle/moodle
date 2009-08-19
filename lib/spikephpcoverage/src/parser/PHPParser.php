<?php
    /*
    *  $Id$
    *  
    *  Copyright(c) 2004-2006, SpikeSource Inc. All Rights Reserved.
    *  Licensed under the Open Software License version 2.1
    *  (See http://www.spikesource.com/license.html)
    */
?>
<?php

    if(!defined("__PHPCOVERAGE_HOME")) {
        define("__PHPCOVERAGE_HOME", dirname(dirname(__FILE__)));
    }
    require_once __PHPCOVERAGE_HOME . "/parser/Parser.php";

    /** 
    * Parser for PHP files 
    * 
    * @author Nimish Pachapurkar (npac@spikesource.com)
    * @version $Revision$
    * @package SpikePHPCoverage_Parser
    */
    class PHPParser extends Parser {
        /*{{{ Members */

        private $inHereDoc = false;
        private $inFunction = false;
        private $phpStarters = array('<?php', '<?', '<?=');
        private $phpFinisher = '?>';
        private $inComment = false;
        private $lastLineEndTokenType = "";
        private $fileToken = null;
        private $currFileToken = 0;
        private $lineNb = 0;
        private $multiLineTok = null;
        // If one of these tokens occur as the last token of a line
        // then the next line can be treated as a continuation line
        // depending on how it starts.
        public static $contTypes = array(
            "(",
            ",",
            ".",
            "=",
            T_LOGICAL_XOR,
            T_LOGICAL_AND,
            T_LOGICAL_OR,
            T_PLUS_EQUAL,
            T_MINUS_EQUAL,
            T_MUL_EQUAL,
            T_DIV_EQUAL,
            T_CONCAT_EQUAL,
            T_MOD_EQUAL,
            T_AND_EQUAL,
            T_OR_EQUAL,
            T_XOR_EQUAL,
            T_BOOLEAN_AND,
            T_BOOLEAN_OR,
            T_OBJECT_OPERATOR, 
            T_DOUBLE_ARROW, 
            "[", 
            "]",
            T_LOGICAL_OR, 
            T_LOGICAL_XOR, 
            T_LOGICAL_AND,
            T_STRING
        );
        /*}}}*/

        /*{{{ protected function openFileReadOnly() */

        /** 
        * Opens the file to be parsed in Read-only mode.
        * Overriden to tokenize the whole file.
        * 
        * @return FALSE on failure.
        * @access protected
        */
        protected function openFileReadOnly() {
            $this->fileToken = @token_get_all(file_get_contents($this->filename));
            return parent::openFileReadOnly();
        }

        /*}}}*/
        /*{{{ protected function getNextToken() */

        /** 
        * Gets the next token.
        *
        * The same token can be returned several times for multi-line
        * tokens.
        * 
        * @param $line The current line read from the file.
        * @param &$pos The position of the next token string in the line.
        * @return the next token or null at the end of the line.
        * @access protected
        */
        protected function getNextToken($line, &$pos) {
            $lineLen = strlen($line);
            if($pos >= $lineLen) {
                return null;
            }
            if($this->multiLineTok != null) {
                list($tok, $lnb, $posnl) = $this->multiLineTok;
                if(is_string($tok)) {
                    $str = $tok;
                } else {
                    $str = $tok[1];
                }
                if($posnl >= strlen($str)) {
                    $this->multiLineTok = null;
                } else {
                    if(substr($str, $posnl + 1, $lineLen) == $line) {
                        $pos += $lineLen;
                        $newPosnl = $posnl + $lineLen;
                    } else {
                        $newPosnl = strpos($str, "\n", $posnl + 1);
                        if($newPosnl === false) {
                            $newPosnl = strlen($str);
                        }
                        $len = $newPosnl - $posnl - 1;
                        //if(substr($str, $posnl + 1, $len) != substr($line, $pos, $len)) {
                        //}
                        $pos += $len;
                    }
                    $this->multiLineTok[1]++;
                    $this->multiLineTok[2] = $newPosnl;
                    return $tok;
                }
            }
            if(!isset($this->fileToken[$this->currFileToken])) {
                return null;
            }
            $tok = &$this->fileToken[$this->currFileToken];
            if(is_string($tok)) {
                $str = $tok;
            } else {
                $str = $tok[1];
            }
            $nbnl = substr_count($str, "\n");
            if($nbnl > 0 && ($posnl = strpos($str, "\n")) != strlen($str) - 1) {
                $this->multiLineTok = array($tok, 1, $posnl);
                $str = substr($str, 0, $posnl + 1);
            }
            if(substr($line, $pos, strlen($str)) == $str) {
                $this->currFileToken++;
                $pos += strlen($str);
                return $tok;
            } else {
                return null;
            }
        }

        /*}}}*/
        /*{{{ protected function isMultiLineCont() */

        /** 
        * Determines if the beginning of current line is a
        * continuation of an executable multi-line token.
        *
        * Called at the first token of a line.
        *
        * @return Boolean true if it is a continuation line
        * @access protected
        */
        protected function isMultiLineCont() {
            if($this->multiLineTok == null
               || $this->multiLineTok[1] <= 1) {
                return false;
            }
            switch ($this->getTokenType($this->multiLineTok[0])) {
            case T_COMMENT:
            case T_INLINE_HTML:             // <br/><b>jhsk</b>
                return false;
            }
            return true;
        }

        /*}}}*/
        /*{{{ protected function processLine() */

        /** 
        * Process a line read from the file and determine if it is an
        * executable line or not. 
        *
        * This is the work horse function that does most of the parsing.
        * To parse PHP, get_all_tokens() tokenizer function is used.
        * 
        * @param $line  Line to be parsed.
        * @access protected
        */
        protected function processLine($line) {

            // Default values
            $prevLineType = $this->lineType; 
            $this->lineType = LINE_TYPE_NOEXEC;
            $tokenCnt = 0;
            $this->lineNb++;
            $pos = 0;
            $seeMore = false;
            $seenEnough = false;
            $lastToken = null;
            
            while (($token = $this->getNextToken($line, $pos))) {
                if (!is_string($token)) {
                    $stoken = token_name($token[0]) . ' "'
                        . str_replace(array("\\", "\"", "\n", "\r")
                                      , array("\\\\", "\\\"", "\\n", "\\r")
                                      , $token[1]) . '"';
                    if (isset($token[2])) {
                        $stoken .= '[' . $token[2] . ']';
                        if ($token[2] != $this->lineNb) {
                            $stoken .= ' != [' . $this->lineNb . ']';
                        }
                    }
                } else {
                    $stoken = $token;
                }
                $this->logger->debug("Token $stoken", __FILE__, __LINE__);
                if (!is_string($token) && $token[0] == T_WHITESPACE) {
                    continue;
                }
                $lastToken = $token;
                $tokenCnt ++;
                if ($this->inHereDoc) {
                    $this->lineType = LINE_TYPE_CONT;
                    $this->logger->debug("Here doc Continuation! Token: $token",
                                         __FILE__, __LINE__);
                    
                    if ($this->getTokenType($token) == T_END_HEREDOC) {
                        $this->inHereDoc = false;
                    }
                    continue;
                }
                if ($this->inFunction) {
                    $this->lineType = LINE_TYPE_NOEXEC;
                    $this->logger->debug("Function! Token: $token",
                                         __FILE__, __LINE__);
                    if ($this->getTokenType($token) == '{') {
                        $this->inFunction = false;
                    }
                    continue;
                }
                
                if($tokenCnt == 1 && $prevLineType != LINE_TYPE_NOEXEC
                   && ($this->isMultiLineCont()
                       || $this->isContinuation($token))) {
                    $this->lineType = LINE_TYPE_CONT;
                    $this->logger->debug("Continuation! Token: ".print_r($token, true),
                                         __FILE__, __LINE__);
                    $seenEnough = true;
                    continue;
                }
                if ($seenEnough) {
                    continue;
                }
                if(is_string($token)) {
                    // FIXME: Add more cases, if needed
                    switch($token) {
                        // Any of these things, are non-executable.
                        // And so do not change the status of the line
                    case '{':
                    case '}':
                    case '(':
                    case ')':
                    case ';':
                        break; 
                        
                        // Everything else by default is executable.
                    default:
                        $this->logger->debug("Other string: $token",
                                             __FILE__, __LINE__);
                        if($this->lineType == LINE_TYPE_NOEXEC) {
                            $this->lineType = LINE_TYPE_EXEC;
                        }
                        break;
                    }
                    $this->logger->debug("Status: " . $this->getLineTypeStr($this->lineType) . "\t\tToken: $token",
                                         __FILE__, __LINE__);
                }
                else {
                    // The token is an array
                    list($tokenType, $text) = $token;
                    switch($tokenType) {
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                    case T_WHITESPACE:              // white space
                    case T_OPEN_TAG:                // < ?
                    case T_OPEN_TAG_WITH_ECHO:      // < ? =
                    case T_CURLY_OPEN:              // 
                    case T_INLINE_HTML:             // <br/><b>jhsk</b>
                        //case T_STRING:                  // 
                    case T_EXTENDS:                 // extends
                    case T_STATIC:                  // static
                    case T_STRING_VARNAME:          // string varname?
                    case T_CHARACTER:               // character
                    case T_ELSE:                    // else
                    case T_CONSTANT_ENCAPSED_STRING:   // "some str"
                    case T_ARRAY:                   // array
                        // Any of these things, are non-executable.
                        // And so do not change the status of the line
                        // as the line starts as non-executable.
                        break;
                    case T_START_HEREDOC:
                        $this->inHereDoc = true;
                        break;
                    case T_PRIVATE:                 // private
                    case T_PUBLIC:                  // public
                    case T_PROTECTED:               // protected
                    case T_VAR:                     // var
                    case T_GLOBAL:                  // global
                    case T_ABSTRACT:                // abstract (Moodle added)
                    case T_CLASS:                   // class
                    case T_INTERFACE:               // interface
                    case T_REQUIRE:                 // require
                    case T_REQUIRE_ONCE:            // require_once
                    case T_INCLUDE:                 // include
                    case T_INCLUDE_ONCE:            // include_once
                    case T_SWITCH:                  // switch
                    case T_CONST:                   // const
                    case T_TRY:                     // try
                        $this->lineType = LINE_TYPE_NOEXEC;
                        // No need to see any further
                        $seenEnough = true;
                        break; 
                    case T_FUNCTION:                // function
                        $this->lineType = LINE_TYPE_NOEXEC;
                        $this->inFunction = true;
                        // No need to see any further
                        $seenEnough = true;
                        break; 
                    case T_VARIABLE:                // $foo
                        $seeMore = true;
                        if($this->lineType == LINE_TYPE_NOEXEC) {
                            $this->lineType = LINE_TYPE_EXEC;
                        }
                        break;
                    case T_CLOSE_TAG:
                        if($this->lineType != LINE_TYPE_EXEC) {
                            $this->lineType = LINE_TYPE_NOEXEC;
                        }
                        break;
                    default:
                        $this->logger->debug("Other token: " . token_name($tokenType),
                                             __FILE__, __LINE__);
                        $seeMore = false;
                        if($this->lineType == LINE_TYPE_NOEXEC) {
                            $this->lineType = LINE_TYPE_EXEC;
                        }
                        break;
                    }
                    $this->logger->debug("Status: " . $this->getLineTypeStr($this->lineType) . "\t\tToken type: $tokenType \tText: $text",
                                         __FILE__, __LINE__);
                }
                if(($this->lineType == LINE_TYPE_EXEC && !$seeMore) 
                   || $seenEnough) {
                    // start moodle modification: comment debug line causing notices
                    //$this->logger->debug("Made a decision! Exiting. Token Type: $tokenType & Text: $text",
                    //                     __FILE__, __LINE__);
                    // end moodle modification
                    if($seenEnough) {
                        $this->logger->debug("Seen enough at Token Type: $tokenType & Text: $text",
                                             __FILE__, __LINE__);
                    } else {
                        $seenEnough = true;
                    }
                }
            } // end while
            $this->logger->debug("Line Type: " . $this->getLineTypeStr($this->lineType),
                                 __FILE__, __LINE__);
            $this->lastLineEndTokenType = $this->getTokenType($lastToken);
            $this->logger->debug("Last End Token: " . $this->lastLineEndTokenType,
                                 __FILE__, __LINE__);
        }

        /*}}}*/
        /*{{{ public function getLineType() */

        /** 
        * Returns the type of line just read 
        * 
        * @return Line type
        * @access public
        */
        public function getLineType() {
            return $this->lineType;
        }
        /*}}}*/
        /*{{{ protected function isContinuation() */

        /** 
        * Check if a line is a continuation of the previous line 
        * 
        * @param &$token Second token in a line (after PHP start)
        * @return Boolean True if the line is a continuation; false otherwise
        * @access protected
        */
        protected function isContinuation(&$token) {
            if(is_string($token)) {
                switch($token) {
                case ".":
                case ",";
                case "]":
                case "[":
                case "(":
                case ")":
                case "=":
                    return true;
                }
            }
            else {
                list($tokenType, $text) = $token;               
                switch($tokenType) {
                case T_CONSTANT_ENCAPSED_STRING:
                case T_ARRAY:
                case T_DOUBLE_ARROW:
                case T_OBJECT_OPERATOR:
                case T_LOGICAL_XOR:
                case T_LOGICAL_AND:
                case T_LOGICAL_OR:
                case T_PLUS_EQUAL:
                case T_MINUS_EQUAL:
                case T_MUL_EQUAL:
                case T_DIV_EQUAL:
                case T_CONCAT_EQUAL:
                case T_MOD_EQUAL:
                case T_AND_EQUAL:
                case T_OR_EQUAL:
                case T_XOR_EQUAL:
                case T_BOOLEAN_AND:
                case T_BOOLEAN_OR:
                case T_LNUMBER:
                case T_DNUMBER:
                    return true;

                case T_STRING:
                case T_VARIABLE:
                    return in_array($this->lastLineEndTokenType, PHPParser::$contTypes);
                }
            }

            return false;
        }
        /*}}}*/
        /*{{{ protected function getTokenType() */

        /** 
        * Get the token type of a token (if exists) or
        * the token itself.
        * 
        * @param $token Token
        * @return Token type or token itself
        * @access protected
        */
        protected function getTokenType($token) {
            if(is_string($token)) {
                return $token;
            }
            else {
                list($tokenType, $text) = $token;
                return $tokenType;
            }
        }
        /*}}}*/
        /*
        // Main
        $obj = new PHPParser();
        $obj->parse("test.php");
        while(($line = $obj->getLine()) !== false) {
            echo "#########################\n";
            echo "[" . $line . "] Type: [" . $obj->getLineTypeStr($obj->getLineType()) . "]\n";
            echo "#########################\n";
    }
    */

    }
?>
