<?php

/**
 * PHP lexer code snarfed from the CVS tree for the lamplib project at
 * http://sourceforge.net/projects/lamplib
 * This project is administered by Markus Baker, Harry Fuecks and Matt
 * Mitchell, and the project  code is in the public domain.
 * 
 * Thanks, guys!
 *
 * @package   moodlecore
 * @copyright Markus Baker, Harry Fuecks and Matt Mitchell
 * @license   Public Domain {@link http://sourceforge.net/projects/lamplib}
 */

    /** LEXER_ENTER = 1 */
    define("LEXER_ENTER", 1);
    /** LEXER_MATCHED = 2 */
    define("LEXER_MATCHED", 2);
    /** LEXER_UNMATCHED = 3 */
    define("LEXER_UNMATCHED", 3);
    /** LEXER_EXIT = 4 */
    define("LEXER_EXIT", 4);
    /** LEXER_SPECIAL = 5 */
    define("LEXER_SPECIAL", 5);
    
    /**
     * Compounded regular expression. Any of
     * the contained patterns could match and
     * when one does it's label is returned.
     * @package   moodlecore
     * @copyright Markus Baker, Harry Fuecks and Matt Mitchell
     * @license   Public Domain {@link http://sourceforge.net/projects/lamplib}
     */
    class ParallelRegex {
        var $_patterns;
        var $_labels;
        var $_regex;
        var $_case;
        
        /**
         *    Constructor. Starts with no patterns.
         *    @param bool $case    True for case sensitive, false
         *                    for insensitive.
         *    @access public
         */
        public function __construct($case) {
            $this->_case = $case;
            $this->_patterns = array();
            $this->_labels = array();
            $this->_regex = null;
        }

        /**
         * Old syntax of class constructor. Deprecated in PHP7.
         *
         * @deprecated since Moodle 3.1
         */
        public function ParallelRegex($case) {
            debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
            self::__construct($case);
        }

        /**
         *    Adds a pattern with an optional label.
         *    @param string $pattern      Perl style regex, but ( and )
         *                         lose the usual meaning.
         *    @param string $label        Label of regex to be returned
         *                         on a match.
         *    @access public
         */
        function addPattern($pattern, $label = true) {
            $count = count($this->_patterns);
            $this->_patterns[$count] = $pattern;
            $this->_labels[$count] = $label;
            $this->_regex = null;
        }
        
        /**
         *    Attempts to match all patterns at once against
         *    a string.
         *    @param string $subject      String to match against.
         *    @param string $match        First matched portion of
         *                         subject.
         *    @return bool             True on success.
         *    @access public
         */
        function match($subject, &$match) {
            if (count($this->_patterns) == 0) {
                return false;
            }
            if (!preg_match($this->_getCompoundedRegex(), $subject, $matches)) {
                $match = "";
                return false;
            }
            $match = $matches[0];
            for ($i = 1; $i < count($matches); $i++) {
                if ($matches[$i]) {
                    return $this->_labels[$i - 1];
                }
            }
            return true;
        }
        
        /**
         *    Compounds the patterns into a single
         *    regular expression separated with the
         *    "or" operator. Caches the regex.
         *    Will automatically escape (, ) and / tokens.
         *    @access private
         */
        function _getCompoundedRegex() {
            if ($this->_regex == null) {
                for ($i = 0; $i < count($this->_patterns); $i++) {
                    $this->_patterns[$i] = '(' . str_replace(
                            array('/', '(', ')'),
                            array('\/', '\(', '\)'),
                            $this->_patterns[$i]) . ')';
                }
                $this->_regex = "/" . implode("|", $this->_patterns) . "/" . $this->_getPerlMatchingFlags();
            }
            return $this->_regex;
        }
        
        /**
         *    Accessor for perl regex mode flags to use.
         *    @return string       Flags as string.
         *    @access private
         */
        function _getPerlMatchingFlags() {
            return ($this->_case ? "msS" : "msSi");
        }
    }
    
    /**
     * States for a stack machine.
     *
     * @package   moodlecore
     * @copyright Markus Baker, Harry Fuecks and Matt Mitchell
     * @license   Public Domain {@link http://sourceforge.net/projects/lamplib}
     */
    class StateStack {
        var $_stack;
        
        /**
         *    Constructor. Starts in named state.
         *    @param string $start        Starting state name.
         *    @access public
         */
        public function __construct($start) {
            $this->_stack = array($start);
        }

        /**
         * Old syntax of class constructor. Deprecated in PHP7.
         *
         * @deprecated since Moodle 3.1
         */
        public function StateStack($start) {
            debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
            self::__construct($start);
        }

        /**
         *    Accessor for current state.
         *    @return string State as string.
         *    @access public
         */
        function getCurrent() {
            return $this->_stack[count($this->_stack) - 1];
        }
        
        /**
         *    Adds a state to the stack and sets it
         *    to be the current state.
         *    @param string $state        New state.
         *    @access public
         */
        function enter($state) {
            array_push($this->_stack, $state);
        }
        
        /**
         *    Leaves the current state and reverts
         *    to the previous one.
         *    @return bool     False if we drop off
         *                the bottom of the list.
         *    @access public
         */
        function leave() {
            if (count($this->_stack) == 1) {
                return false;
            }
            array_pop($this->_stack);
            return true;
        }
    }
    
    /**
     * Accepts text and breaks it into tokens.
     * Some optimisation to make the sure the
     * content is only scanned by the PHP regex
     * parser once. Lexer modes must not start
     * with leading underscores.
     *
     * @package   moodlecore
     * @copyright Markus Baker, Harry Fuecks and Matt Mitchell
     * @license   Public Domain {@link http://sourceforge.net/projects/lamplib}
     */
    class Lexer {
        var $_regexes;
        var $_parser;
        var $_mode;
        var $_mode_handlers;
        var $_case;
        
        /**
         *    Sets up the lexer in case insensitive matching
         *    by default.
         *    @param object $parser     Handling strategy by
         *                       reference.
         *    @param string $start      Starting handler.
         *    @param bool $case       True for case sensitive.
         *    @access public
         */
        public function __construct(&$parser, $start = "accept", $case = false) {
            $this->_case = $case;
            $this->_regexes = array();
            $this->_parser = &$parser;
            $this->_mode = new StateStack($start);
            $this->_mode_handlers = array();
        }

        /**
         * Old syntax of class constructor. Deprecated in PHP7.
         *
         * @deprecated since Moodle 3.1
         */
        public function Lexer(&$parser, $start = "accept", $case = false) {
            debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
            self::__construct($parser, $start, $case);
        }
        
        /**
         *    Adds a token search pattern for a particular
         *    parsing mode. The pattern does not change the
         *    current mode.
         *    @param string $pattern      Perl style regex, but ( and )
         *                         lose the usual meaning.
         *    @param string $mode         Should only apply this
         *                         pattern when dealing with
         *                         this type of input.
         *    @access public
         */
        function addPattern($pattern, $mode = "accept") {
            if (!isset($this->_regexes[$mode])) {
                $this->_regexes[$mode] = new ParallelRegex($this->_case);
            }
            $this->_regexes[$mode]->addPattern($pattern);
        }
        
        /**
         *    Adds a pattern that will enter a new parsing
         *    mode. Useful for entering parenthesis, strings,
         *    tags, etc.
         *    @param string $pattern      Perl style regex, but ( and )
         *                         lose the usual meaning.
         *    @param string $mode         Should only apply this
         *                         pattern when dealing with
         *                         this type of input.
         *    @param string $new_mode     Change parsing to this new
         *                         nested mode.
         *    @access public
         */
        function addEntryPattern($pattern, $mode, $new_mode) {
            if (!isset($this->_regexes[$mode])) {
                $this->_regexes[$mode] = new ParallelRegex($this->_case);
            }
            $this->_regexes[$mode]->addPattern($pattern, $new_mode);
        }
        
        /**
         *    Adds a pattern that will exit the current mode
         *    and re-enter the previous one.
         *    @param string $pattern      Perl style regex, but ( and )
         *                         lose the usual meaning.
         *    @param string $mode         Mode to leave.
         *    @access public
         */
        function addExitPattern($pattern, $mode) {
            if (!isset($this->_regexes[$mode])) {
                $this->_regexes[$mode] = new ParallelRegex($this->_case);
            }
            $this->_regexes[$mode]->addPattern($pattern, "__exit");
        }
        
        /**
         *    Adds a pattern that has a special mode.
         *    Acts as an entry and exit pattern in one go.
         *    @param string $pattern      Perl style regex, but ( and )
         *                         lose the usual meaning.
         *    @param string $mode         Should only apply this
         *                         pattern when dealing with
         *                         this type of input.
         *    @param string $special      Use this mode for this one token.
         *    @access public
         */
        function addSpecialPattern($pattern, $mode, $special) {
            if (!isset($this->_regexes[$mode])) {
                $this->_regexes[$mode] = new ParallelRegex($this->_case);
            }
            $this->_regexes[$mode]->addPattern($pattern, "_$special");
        }
        
        /**
         *    Adds a mapping from a mode to another handler.
         *    @param string $mode        Mode to be remapped.
         *    @param string $handler     New target handler.
         *    @access public
         */
        function mapHandler($mode, $handler) {
            $this->_mode_handlers[$mode] = $handler;
        }
        
        /**
         *    Splits the page text into tokens. Will fail
         *    if the handlers report an error or if no
         *    content is consumed. If successful then each
         *    unparsed and parsed token invokes a call to the
         *    held listener.
         *    @param string $raw        Raw HTML text.
         *    @return bool           True on success, else false.
         *    @access public
         */
        function parse($raw) {
            if (!isset($this->_parser)) {
                return false;
            }
            $length = strlen($raw);
            while (is_array($parsed = $this->_reduce($raw))) {
                list($unmatched, $matched, $mode) = $parsed;
                if (!$this->_dispatchTokens($unmatched, $matched, $mode)) {
                    return false;
                }
                if (strlen($raw) == $length) {
                    return false;
                }
                $length = strlen($raw);
            }
            if (!$parsed) {
                return false;
            }
            return $this->_invokeParser($raw, LEXER_UNMATCHED);
        }
        
        /**
         *    Sends the matched token and any leading unmatched
         *    text to the parser changing the lexer to a new
         *    mode if one is listed.
         *    @param string $unmatched    Unmatched leading portion.
         *    @param string $matched      Actual token match.
         *    @param string $mode         Mode after match. The "_exit"
         *                         mode causes a stack pop. An
         *                         false mode causes no change.
         *    @return bool              False if there was any error
         *                         from the parser.
         *    @access private
         */
        function _dispatchTokens($unmatched, $matched, $mode = false) {
            if (!$this->_invokeParser($unmatched, LEXER_UNMATCHED)) {
                return false;
            }
            if ($mode === "__exit") {
                if (!$this->_invokeParser($matched, LEXER_EXIT)) {
                    return false;
                }
                return $this->_mode->leave();
            }
            if (strncmp($mode, "_", 1) == 0) {
                $mode = substr($mode, 1);
                $this->_mode->enter($mode);
                if (!$this->_invokeParser($matched, LEXER_SPECIAL)) {
                    return false;
                }
                return $this->_mode->leave();
            }
            if (is_string($mode)) {
                $this->_mode->enter($mode);
                return $this->_invokeParser($matched, LEXER_ENTER);
            }
            return $this->_invokeParser($matched, LEXER_MATCHED);
        }
        
        /**
         *    Calls the parser method named after the current
         *    mode. Empty content will be ignored.
         *    @param string $content        Text parsed.
         *    @param string $is_match       Token is recognised rather
         *                           than unparsed data.
         *    @access private
         */
        function _invokeParser($content, $is_match) {
            if (($content === "") || ($content === false)) {
                return true;
            }
            $handler = $this->_mode->getCurrent();
            if (isset($this->_mode_handlers[$handler])) {
                $handler = $this->_mode_handlers[$handler];
            }
            return $this->_parser->$handler($content, $is_match);
        }
        
        /**
         *    Tries to match a chunk of text and if successful
         *    removes the recognised chunk and any leading
         *    unparsed data. Empty strings will not be matched.
         *    @param string $raw  The subject to parse. This is the
         *                        content that will be eaten.
         *    @return bool|array  Three item list of unparsed
         *                        content followed by the
         *                        recognised token and finally the
         *                        action the parser is to take.
         *                        True if no match, false if there
         *                        is a parsing error.
         *    @access private
         */
        function _reduce(&$raw) {
            if (!isset($this->_regexes[$this->_mode->getCurrent()])) {
                return false;
            }
            if ($raw === "") {
                return true;
            }
            if ($action = $this->_regexes[$this->_mode->getCurrent()]->match($raw, $match)) {
                $count = strpos($raw, $match);
                $unparsed = substr($raw, 0, $count);
                $raw = substr($raw, $count + strlen($match));
                return array($unparsed, $match, $action);
            }
            return true;
        }
    }
?>
