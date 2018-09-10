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
 * Render an attempt at a HotPot quiz
 * Output format: hp
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/renderer.php');

/**
 * mod_hotpot_attempt_hp_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_hp_renderer extends mod_hotpot_attempt_renderer {

    /** file name of main template e.g. jcloze6.ht_ */
    protected $templatefile = '';

    /** special template strings to be expanded */
    protected $templatestrings = '';

    /** templates folders (relative to Moodle dirroot) */
    protected $templatesfolders = array();

    /** external javascripts required for this format */
    protected $javascripts = array();

    /** type of javascript object to collect quiz results from browser */
    protected $js_object_type = '';

    // the id/name of the of the form which returns results to the browser
    protected $formid = 'store';

    // the name of the score field in the results returned form the browser
    protected $scorefield = 'mark';

    /* results form field that holds the start time of the attempt */
    protected $durationstartfield = 'starttime';

    /* results form field that holds the end time of the attempt */
    protected $durationfinishfield = 'endtime';

    // starttime/endtime are recorded by the client (and may not be trustworthy)
    // resumestart/resumefinish are recorded by the server (but include transfer time to and from client)

    /** the raw html content, either straight from an html file, or generated from an xml file */
    protected $htmlcontent;

    /** names of javascript string variables which can be passed through Glossary autolinking filter, if enabled */
    protected $headcontent_strings = '';

    /** names of javascript array variables which can be passed through Glossary autolinking filter, if enabled */
    protected $headcontent_arrays = '';

    // HP quizzes have a SubmissionTimeout variable, but TexToys do not
    protected $hasSubmissionTimeout = true;

    // basic initialization

    /**
     * init
     *
     * @param xxx $hotpot
     */
    public function init($hotpot)  {
        parent::init($hotpot);
        array_push($this->javascripts, 'mod/hotpot/attempt/hp/hp.js');
        if ($hotpot->studentfeedback) {
            array_push($this->javascripts, 'mod/hotpot/attempt/hp/feedback.js');
        }
    }

    /**
     * set_xmldeclaration
     */
    function set_xmldeclaration()  {
        // for IE6 we must *not* send an xmldeclaration; for other browsers we can
        // see http://moodle.org/mod/forum/discuss.php?d=73309
        if (! isset($this->xmldeclaration)) {

            if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) {
                // do not set xml declaration for IE6 otherwise it goes into quirks mode
                // TODO: check behavior if ($this->page->theme->name()=='custom_corners')
                $this->xmldeclaration = '';
            } else {
                $this->xmldeclaration = '<'.'?xml version="1.0"?'.'>'."\n";
            }
        }
    }

    /**
     * set_htmlcontent
     *
     * @return xxx
     */
    function set_htmlcontent()  {
        if (isset($this->htmlcontent)) {
            // htmlcontent has already been set
            return true;
        }
        $this->htmlcontent = '';

        if (! $this->hotpot->get_source()) {
            // could not access source file
            return false;
        }

        if (! $this->hotpot->source->get_filecontents()) {
            // empty source file - shouldn't happen !!
            return false;
        }

        // html files
        if ($this->hotpot->source->is_html()) {
            $this->htmlcontent = &$this->hotpot->source->filecontents;
            return true;
        }

        // xml files
        if (! $this->hotpot->source->xml_get_filecontents()) {
            // could not create xml tree - shouldn't happen !!
            return false;
        }
        if (! $this->xml_set_htmlcontent()) {
            // could not create html from xml - shouldn't happen !!
            return false;
        }

        return true;
    }

    /**
     * xml_set_htmlcontent
     *
     * @return xxx
     */
    function xml_set_htmlcontent()  {

        // get the xml source
        if (! $this->hotpot->source->xml_get_filecontents()) {
            // could not detect Hot Potatoes quiz type in xml file - shouldn't happen !!
            return false;
        }

        if (empty($this->templatefile)) {
            throw new moodle_exception('missingtemplatefile', 'hotpot', '', get_class($this));
        }

        if (! $this->htmlcontent = $this->expand_template($this->templatefile)) {
            // some problem accessing the main template for this quiz type
            return false;
        }

        if ($this->templatestrings) {
            $this->expand_strings($this->htmlcontent, '/\[('.$this->templatestrings.')\]/is');
        }

        // all done
        return true;
    }

    /**
     * set_headcontent
     */
    function set_headcontent()  {
        global $CFG;

        if (isset($this->headcontent)) {
            return;
        }
        $this->headcontent = '';

        if (! $this->set_htmlcontent()) {
            // could not locate/generate html content
            return;
        }

        // extract contents of first <head> tag
        if (preg_match($this->tagpattern('head'), $this->htmlcontent, $matches)) {
            $this->headcontent = $matches[2];
        }

        if ($this->usemoodletheme) {
            // remove the title from the <head>
            $this->headcontent = preg_replace($this->tagpattern('title'), '', $this->headcontent);
        } else {
            // replace <title> with current name of this quiz
            $title = '<title>'.$this->get_title().'</title>'."\n";
            $this->headcontent = preg_replace($this->tagpattern('title'), $title, $this->headcontent);

            // extract details needed to rebuild page later in $this->view()
            if (preg_match($this->tagpattern('\?xml','',false), $this->htmlcontent, $matches)) {
                $this->xmldeclaration = $matches[0]."\n";
            }
            if (preg_match($this->tagpattern('!DOCTYPE','',false), $this->htmlcontent, $matches)) {
                $this->doctype = $this->single_line($matches[0])."\n";
                // convert short dtd to full dtd (short dtd not allowed in xhtml 1.1)
                $this->doctype = preg_replace('/"xhtml(\d+)(-\w+)?.dtd"/i', '"http://www.w3.org/TR/xhtml$1/DTD/xhtml$1$2.dtd"', $this->doctype, 1);
            }
            if (preg_match($this->tagpattern('html','',false), $this->htmlcontent, $matches)) {
                $this->htmlattributes = ' '.$this->single_line($matches[1])."\n";
            }
            if (preg_match($this->tagpattern('head','',false), $this->htmlcontent, $matches)) {
                $this->headattributes = ' '.$this->single_line($matches[1]);
            }
        }

        // transfer <styles> tags from $this->headcontent to $this->styles
        $this->styles = '';
        if (preg_match_all($this->tagpattern('style'), $this->headcontent, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match) {
                // $match: [0] = matched string, [1] = offset to start of string
                $this->styles = $match[0]."\n".$this->styles;
                $this->headcontent = substr_replace($this->headcontent, '', $match[1], strlen($match[0]));
            }

            // restrict scope of Hot Potatoes styles, so they affect only the quiz's containing element (i.e. the middle column)
            if ($this->usemoodletheme) {
                $search = '/([a-z0-9_\#\.\-\,\: ]+){(.*?)}/is';
                $callback = array($this, 'fix_css_definitions');
                $this->styles = preg_replace_callback($search, $callback, $this->styles);

                // the following is not necessary for standard HP styles, but may required to handle some custom styles
                $this->styles = str_replace('TheBody', $this->themecontainer, $this->styles);
            }

            // remove comments /* ... */
            $this->styles = preg_replace('/[\n\r]*[\t ]*\/\*.*?\*\//s', '', $this->styles);

            // remove blank lines
            $this->styles = $this->remove_blank_lines($this->styles);

        }

        // transfer <script> tags from $this->headcontent to $this->scripts
        $this->scripts = '';
        if (preg_match_all($this->tagpattern('script'), $this->headcontent, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match) {
                // $match: [0] = matched string, [1] = offset to start of string
                $this->scripts = $match[0]."\n".$this->scripts;
                $this->headcontent = substr_replace($this->headcontent, '', $match[1], strlen($match[0]));
            }
            if ($this->usemoodletheme) {
                $this->scripts = str_replace('TheBody', $this->themecontainer, $this->scripts);
            }
            // fix various javascript functions
            $names = $this->get_js_functionnames();
            $this->fix_js_functions($names);

            $this->scripts = preg_replace('/\s*'.'(var )?ResultForm [^;]+?;/s', '', $this->scripts);

            // remove multi-line and single-line comments - except <![CDATA[ + ]]> and  <!-- + -->
            if ($CFG->debug <= DEBUG_DEVELOPER) {
                $this->scripts = $this->fix_js_comment($this->scripts);
            }
            $this->scripts = $this->remove_blank_lines($this->scripts);

            // standardize "} else {" and "} else if" formatting
            $this->scripts = preg_replace('/\}\s*else\s*(\{|if)/s', '} else $1', $this->scripts);

            // standardize indentation to use tabs
            $this->scripts = str_replace('        ', "\t", $this->scripts);
        }

        // remove blank lines
        $this->headcontent = $this->remove_blank_lines($this->headcontent);

        // put each <meta> tag on its own line
        $this->headcontent = preg_replace('/'.'([^\n])'.'(<\w+)'.'/', '$1'."\n".'$2', $this->headcontent);

        // convert self closing tags to explictly closed tags (self-closing not allowed in xhtml 1.1)
        // $this->headcontent = preg_replace('/<((\w+)[^>]*?)\s*\/>/i', '<$1></$2>', $this->headcontent);

        // append styles and scripts to the end of the $this->headcontent
        $this->headcontent .= $this->styles.$this->scripts;

        // do any other fixes for the headcontent
        $this->fix_headcontent_beforeonunload();
        $this->fix_headcontent();
    }

    /**
     * fix_headcontent
     */
    function fix_headcontent()  {
        // this function is a hook that can be used by sub classes to fix up the headcontent
    }

    /**
     * fix_headcontent_beforeonunload
     *
     * @return xxx
     */
    function fix_headcontent_beforeonunload()  {
        // warn user about consequences of navigating away from this page
        switch ($this->can_continue()) {
            case hotpot::CONTINUE_RESUMEQUIZ:
                $onbeforeunload = ''.get_string('canresumehotpot', 'mod_hotpot', format_string($this->hotpot->name));
                break;
            case hotpot::CONTINUE_RESTARTQUIZ:
            case hotpot::CONTINUE_RESTARTUNIT:
                $onbeforeunload = get_string('canrestarthotpot', 'mod_hotpot', format_string($this->hotpot->name));
                break;
            case hotpot::CONTINUE_ABANDONUNIT:
                $onbeforeunload = get_string('abandonhotpot', 'mod_hotpot');
                break;
            default:
                $onbeforeunload = ''; // shouldn't happen !!
        }
        if ($onbeforeunload) {
            $search = "/(\s*)window\.(?:hotpotunload|onunload|HP) = /s";
            $replace = ''
                .'$1'."window.HP_beforeunload = function() {"
                .'$1'."	return '".$this->hotpot->source->js_value_safe($onbeforeunload, true)."';"
                .'$1'."}"
                .'$1'."if (window.opera) {"
                .'$1'."	opera.setOverrideHistoryNavigationMode('compatible');"
                .'$1'."	history.navigationMode = 'compatible';"
                .'$1'."}"
                .'$0'
            ;
            $this->headcontent = preg_replace($search, $replace, $this->headcontent, 1);
        }
    }

    /**
     * fix_headcontent_rottmeier
     *
     * @param xxx $type (optional, default='')
     */
    function fix_headcontent_rottmeier($type='')  {
    }

    /**
     * fix_js_comment
     *
     * @param xxx $str
     * @return xxx
     */
    function fix_js_comment($str)  {
        $out = '';

        // the parse state
        //     0 : javascript code
        //     1 : single-quoted string
        //     2 : double-quoted string
        //     3 : single line comment
        //     4 : multi-line comment
        $state = 0;

        $strlen = strlen($str);
        $i = 0;
        while ($i<$strlen) {
            switch ($state) {
                case 1: // single-quoted string
                    $out .= $str{$i};
                    switch ($str{$i}) {
                        case "\\":
                            $i++; // skip next char
                            $out .= $str{$i};
                            break;
                        case "\n":
                        case "\r":
                        case "'":
                            $state = 0; // end of this string
                            break;
                    }
                    break;

                case 2: // double-quoted string
                    $out .= $str{$i};
                    switch ($str{$i}) {
                        case "\\":
                            $i++; // skip next char
                            $out .= $str{$i};
                            break;
                        case "\n":
                        case "\r":
                        case '"':
                            $state = 0; // end of this string
                            break;
                    }
                    break;

                case 3: // single line comment
                    if ($str{$i}=="\n" || $str{$i}=="\r") {
                        $state = 0; // end of this comment
                        $out .= $str{$i};
                    }
                    break;

                case 4: // multi-line comment
                    if ($str{$i}=='*' && $str{$i+1}=='/') {
                        $state = 0; // end of this comment
                        $i++;
                    }
                    break;

                case 0: // plain old JavaScript code
                default:
                    switch ($str{$i}) {
                        case "'":
                            $out .= $str{$i};
                            $state = 1; // start of single quoted string
                            break;

                        case '"':
                            $out .= $str{$i};
                            $state = 2; // start of double quoted string
                            break;

                        case '/':
                            switch ($str{$i+1}) {
                                case '/':
                                    switch (true) {
                                        // allow certain single line comments
                                        case substr($str, $i+2, 9)=='<![CDATA[':
                                            $out .= substr($str, $i, 11);
                                            $i += 11;
                                            break;
                                        case substr($str, $i+2, 3)==']]>':
                                            $out .= substr($str, $i, 5);
                                            $i += 5;
                                            break;
                                        case substr($str, $i+2, 4)=='<!--':
                                            $out .= substr($str, $i, 6);
                                            $i += 6;
                                            break;
                                        case substr($str, $i+2, 3)=='-->':
                                            $out .= substr($str, $i, 5);
                                            $i += 5;
                                            break;
                                        default:
                                            $state = 3; // start of single line comment
                                            $i++;
                                            break;
                                    }
                                    break;
                                case '*':
                                    $state = 4; // start of multi-line comment
                                    $i++;
                                    break;
                                default:
                                    // a slash - could be start of RegExp ?!
                                    $out .= $str{$i};
                            }
                            break;

                        default:
                            $out .= $str{$i};

                    } // end switch : non-comment char
            } // end switch : status
            $i++;
        } // end while

        return $out;
    }

   /**
    * set_bodycontent
    */
   function set_bodycontent()  {
        if (isset($this->bodycontent)) {
            // content was fetched from cache
            return;
        }

        // otherwise we need to generate new body content

        $this->bodycontent = '';

        if (! $this->set_htmlcontent()) {
            // could not locate/generate html content
            return;
        }

        // extract <body> tag
        if (! preg_match($this->tagpattern('body', 'onload'), $this->htmlcontent, $matches)) {
            return false;
        }
        if ($this->usemoodletheme) {
            $matches[1] = str_replace('id="TheBody"', '', $matches[1]);
        }
        $this->bodyattributes = $this->single_line($matches[1]);
        $onload = $matches[3];
        $this->bodycontent = $this->remove_blank_lines($matches[5]);

       // where necessary, add single space before javascript event handlers to make the syntax compatible with strict XHTML
        $this->bodycontent = preg_replace('/"(on(?:blur|click|focus|mouse(?:down|out|over|up)))/', '" $1', $this->bodycontent);

        // ensure javascript onload routine for quiz is always executed
        // $this->bodyattributes will only be inserted into the <body ...> tag
        // if it is included in the theme/$CFG->theme/header.html,
        // so some old or modified themes may not insert $this->bodyattributes
        $this->bodycontent .= $this->fix_onload($onload, true);

        $this->fix_title();
        $this->fix_TimeLimit();
        $this->fix_SubmissionTimeout();
        $this->fix_relativeurls();
        $this->fix_navigation();
        $this->fix_filters();
        $this->fix_mediafilter();
        $this->fix_feedbackform();
        $this->fix_reviewoptions();
        $this->fix_targets();
        $this->fix_bodycontent();
    }

    /**
     * fix_bodycontent
     */
    function fix_bodycontent()  {
        // this function is a hook that can be used by sub classes to fix up the bodycontent
    }

    /**
     * fix_bodycontent_rottmeier
     *
     * @param xxx $hideclozeform (optional, default=false)
     */
    function fix_bodycontent_rottmeier($hideclozeform=false)  {
    }

    /**
     * fix_js_functions
     *
     * @param xxx $names
     */
    function fix_js_functions($names)  {
        if (is_string($names)) {
            $names = explode(',', $names);
        }
        foreach($names as $name) {
            list($start, $finish) = $this->locate_js_block('function', $name, $this->scripts);
            if (! $finish) {
                // debugging("Could not locate JavaScript function: $name", DEBUG_DEVELOPER);
                continue;
            }
            $methodname = "fix_js_{$name}";
            if (! method_exists($this, $methodname)) {
                // debugging("Could not locate method to fix JavaScript function: $name", DEBUG_DEVELOPER);
                continue;
            }
            $this->$methodname($this->scripts, $start, ($finish - $start));
        }
    }

    /**
     * locate_js_block
     *
     * @param string $type one of "function", "if", "for", "while", "do", "switch"
     * @param string $name unique string to identify this js block (e.g. function-name OR if-condition)
     * @param string $str (passed by reference) javascript code
     * @param boolean $includewhitespace (optional, default=false) TRUE=return leading white space, FALSE otherwise
     * @param boolean $offset (optional, default=0) char position at which to start search
     * @return array($start, $finish) start and finish positions of js block within $str
     */
    function locate_js_block($type, $name, &$str, $includewhitespace=false, $offset=0)  {
        $start = 0;
        $finish = 0;

        // set $search string to locate the start of this block $type
        switch ($type) {

            // these blocks have an leading parenthetical phrase
            case 'if':
            case 'for':
            case 'while':
            case 'switch':
                $search = $type.'\s*\('.preg_quote($name, '/').'\)';
                break;

            // these blocks have a trailing parenthetical phrase
            case 'do':
                $search = $type.'\s*(?=\{)';
                break;

            // functions - this is the original intention of this locate_js_block()
            case 'function':
                $search = 'function\s+'.$name.'\s*\(.*?\)';
                break;
        }
        $search = '/'.($includewhitespace ? '\s*' : '').$search.'/s';

        if (preg_match($search, $str, $match, PREG_OFFSET_CAPTURE, $offset)) {

            // $match[0][0] : matching string
            // $match[0][1] : offset to matching string
            $start = $match[0][1];

            // position of opening curly bracket (or thereabouts)
            $i = $start + strlen($match[0][0]);

            // count how many opening curly brackets we have had so far
            $count = 0;

            // the parse state
            //     0 : javascript code
            //     1 : single-quoted string
            //     2 : double-quoted string
            //     3 : single line comment
            //     4 : multi-line comment
            $state = 0;

            $strlen = strlen($str);
            while ($i<$strlen && ! $finish) {
                switch ($state) {
                    case 1: // single-quoted string
                        switch ($str{$i}) {
                            case "\\":
                                $i++; // skip next char
                                break;
                            case "'":
                                $state = 0; // end of this string
                                break;
                        }
                        break;

                    case 2: // double-quoted string
                        switch ($str{$i}) {
                            case "\\":
                                $i++; // skip next char
                                break;
                            case '"':
                                $state = 0; // end of this string
                                break;
                        }
                        break;

                    case 3: // single line comment
                        if ($str{$i}=="\n" || $str{$i}=="\r") {
                            $state = 0; // end of this comment
                        }
                        break;

                    case 4: // multi-line comment
                        if ($str{$i}=='*' && $str{$i+1}=='/') {
                            $state = 0; // end of this comment
                            $i++;
                        }
                        break;

                    case 0: // plain old JavaScript code
                    default:
                        switch ($str{$i}) {
                            case "'":
                                $state = 1; // start of single quoted string
                                break;

                            case '"':
                                $state = 2; // start of double quoted string
                                break;

                            case '/':
                                switch ($str{$i+1}) {
                                    case '/':
                                        $state = 3; // start of single line comment
                                        $i++;
                                        break;
                                    case '*':
                                        $state = 4; // start of multi-line comment
                                        $i++;
                                        break;
                                }
                                break;

                            case '{':
                                $count++; // start of Javascript code block
                                break;

                            case '}':
                                $count--; // end of Javascript code block

                                // detect trailing blocks or phrases
                                switch ($type) {

                                    // "if" blocks may have trailing "else if" or "else" blocks
                                    case 'if':
                                        $search = '/^\s*else(?:\s+if\s*\(.*?\))?\s*\{/s';
                                        if (preg_match($search, substr($str, $i+1), $match)) {
                                            $i += strlen($match[0]);
                                            $count++; // continue parsing
                                        }
                                        break;

                                    // "do" blocks have a trailing parenthetical phrase
                                    case 'do':
                                        $search = '/^\s*while\s*\('.preg_quote($name, '/').'\);/s';
                                        if (preg_match($search, substr($str, $i+1), $match)) {
                                            $i += strlen($match[0]);
                                        }
                                        break;
                                }

                                // detect trailing semicolon, if any
                                if ($str{$i+1}==';') {
                                    $i++;
                                }
                                if ($count==0) { // end of outer code block (e.g. end of function)
                                    $finish = $i + 1;
                                }
                                break;

                        } // end switch : non-comment char
                } // end switch : status
                $i++;
            } // end while
        } // end if $start

        return array($start, $finish);
    }

    // does this output format allow quiz attempts to be reviewed?

    /**
     * provide_review
     *
     * @return xxx
     */
    public function provide_review()  {
        return true;
    }

    // functions to expand xml templates (and the blocks and strings contained therein)

    /**
     * expand_template
     *
     * @param xxx $filename
     * @return xxx
     */
    public function expand_template($filename)  {
        global $CFG;

        // check that some template folders have been specified to something sensible
        if (! isset($this->templatesfolders)) {
            debugging('templatesfolders is not set', DEBUG_DEVELOPER);
            return '';
        }
        if (! is_array($this->templatesfolders)) {
            debugging('templatesfolders is not an array', DEBUG_DEVELOPER);
            return '';
        }

        // set the path to the template file
        $filepath = '';
        foreach ($this->templatesfolders as $templatesfolder) {
            if (is_file("$CFG->dirroot/$templatesfolder/$filename")) {
                $filepath = "$CFG->dirroot/$templatesfolder/$filename";
                break;
            }
        }

        // check the template was found
        if (! $filepath) {
            debugging('template not found: '.$filename, DEBUG_DEVELOPER);
            return '';
        }
        // check the template is readable
        if (! is_readable($filepath)) {
            debugging('template is not readable: '.$filepath, DEBUG_DEVELOPER);
            return '';
        }

        // try and read the template
        if (! $template = file_get_contents($filepath)) {
            debugging('template is empty: '.$filepath, DEBUG_DEVELOPER);
            return '';
        }

        // expand the blocks and strings in the template
        $this->expand_blocks($template);
        $this->expand_strings($template);

        // return the expanded template
        return $template;
    }

    /**
     * expand_blocks
     *
     * @param xxx $template (passed by reference)
     */
    public function expand_blocks(&$template)  {
        // expand conditional blocks
        //  [1] the full block name (including optional leading 'str' or 'incl')
        //  [2] the short block name (without optional leading 'str' or 'incl')
        //  [3] the content of the block
        $search = '/'.'\[((?:incl|str)?((?:\w|\.)+))\]'.'(.*?)'.'\[\/\\1\]'.'/is';
        $callback = array($this, 'expand_block');
        $template = preg_replace_callback($search, $callback, $template);
    }

    /**
     * expand_block
     *
     * @param xxx $match
     * @return xxx
     */
    public function expand_block($match)  {
        $blockname = $match[2];
        $blockcontent = $match[3];

        // check expand method exists
        $method = 'expand_'.str_replace('.', '', $blockname);
        if (! method_exists($this, $method)) {
            debugging('expand block method not found: '.$method, DEBUG_DEVELOPER);
            return '';
        }

        // if condition is satisfied, return block content; otherwise return empty string
        if ($this->$method()) {
            // expand any (sub) blocks within the block content
            $this->expand_blocks($blockcontent);
            return $blockcontent;
        } else {
            return '';
        }
    }

    /**
     * expand_strings
     *
     * @param xxx $template (passed by reference)
     * @param xxx $search (optional, default='')
     */
    public function expand_strings(&$template, $search='')  {
        if ($search=='') {
            // default search string
            $search = '/\[(?:bool|int|str)(\w+)\]/is';
        }
        $callback = array($this, 'expand_string');
        $template = preg_replace_callback($search, $callback, $template);
    }

    /**
     * expand_string
     *
     * @param xxx $match
     * @return xxx
     */
    public function expand_string($match)  {
        $originalstring = $match[0];
        $stringname = $match[1];

        $method = 'expand_'.$stringname;
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return $originalstring;
        }
    }

    /**
     * expand_halfway_color
     *
     * @param xxx $x
     * @param xxx $y
     * @return xxx
     */
    public function expand_halfway_color($x, $y)  {
        // returns the $color that is half way between $x and $y
        $color = $x; // default
        $rgb = '/^\#?([0-9a-f])([0-9a-f])([0-9a-f])$/i';
        $rrggbb = '/^\#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i';
        if (preg_match($rgb, $x, $x_matches) || preg_match($rrggbb, $x, $x_matches)) {
            if (preg_match($rgb, $y, $y_matches) || preg_match($rrggbb, $y, $y_matches)) {
                $color = '#';
                for ($i=1; $i<=3; $i++) {
                    $x_dec = hexdec($x_matches[$i]);
                    $y_dec = hexdec($y_matches[$i]);
                    $color .= sprintf('%02x', min($x_dec, $y_dec) + abs($x_dec-$y_dec)/2);
                }
            }
        }
        return $color;
    }

    // functions to convert relative urls to absolute URLs

    /**
     * fix_relativeurls
     *
     * @param xxx $str (optional, default=null)
     * @return xxx
     */
    public function fix_relativeurls($str=null)  {
        global $DB;

        if (is_string($str)) {
            // fix relative urls in $str(ing), and return
            return parent::fix_relativeurls($str);
        }

        // do standard fixes relative urls in $this->headcontent and $this->bodycontent
        parent::fix_relativeurls();

        // replace relative URLs in "PreloadImages(...);"
        $search = '/(PreloadImages\()'.'([^)]+?)'.'(\);)/is';
        $callback = array($this, 'convert_urls_preloadimages');
        $this->headcontent = preg_replace_callback($search, $callback, $this->headcontent);
        $this->bodycontent = preg_replace_callback($search, $callback, $this->bodycontent);
    }

    /**
     * convert_urls_preloadimages
     *
     * @param xxx $match
     * @return xxx
     */
    public function convert_urls_preloadimages($match)  {
        $before = $match[1];
        $urls   = $match[2];
        $after  = $match[3];
        $search = '/('.'"'.'|'."'".')'."([^'".'",]+?)'.'('.'"'.'|'."'".')/is';
        $callback = array($this, 'convert_url');
        return $before.preg_replace_callback($search, $callback, $urls).$after;
    }

    /**
     * convert_url_navbutton
     *
     * @param xxx $match
     * @return xxx
     */
    public function convert_url_navbutton($match)  {
        global $CFG, $DB;

        $url = $this->convert_url($match[1]);

        // is this a $url for another HotPot in this course ?
        if (strpos($url, $this->hotpot->source->baseurl.'/')===0) {
            $filepath = substr($url, strlen($this->hotpot->source->baseurl));
            $sourcefile = $this->hotpot->source->xml_locate_file($filepath);
            if ($records = $DB->get_records('hotpot', array('sourcefile' => $sourcefile), '', 'id', 0, 1)) {
                $record = reset($records); // first record - there could be more than one ?!
                $url = new moodle_url('/mod/hotpot/view.php', array('id' => $record->id));
            }
        }

        return $url;
    }

    /**
     * get_send_results_flag
     *
     * @return string
     */
    function get_send_results_event() {
        if ($this->hotpot->delay3==hotpot::TIME_AFTEROK) {
            return 'HP.EVENT_SETVALUES';
        } else {
            return 'HP.EVENT_COMPLETED';
        }
    }
}
