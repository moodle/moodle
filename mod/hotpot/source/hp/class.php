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
 * mod/hotpot/source/hp/class.php
 *
 * @package    mod
 * @subpackage hotpot
 * @copyright  2010 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 */

/** Prevent direct access to this script */
defined('MOODLE_INTERNAL') || die();

/** Include required files */
require_once($CFG->dirroot.'/mod/hotpot/source/class.php');

/**
 * hotpot_source_hp
 *
 * @copyright  2010 Gordon Bateson (gordon.bateson@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.0
 * @package    mod
 * @subpackage hotpot
 */
class hotpot_source_hp extends hotpot_source {
    public $xml; // an array containing the xml tree for hp xml files
    public $xml_root; // the array key of the root of the xml tree

    public $hbs_software; // hotpot or textoys
    public $hbs_quiztype; //  jcloze, jcross, jmatch, jmix, jquiz, quandary, rhubarb, sequitur

    // encode a string for javascript
    public $javascript_replace_pairs = array(
        // backslashes and quotes
        '\\'=>'\\\\', "'"=>"\\'", '"'=>'\\"',
        // newlines (win = "\r\n", mac="\r", linux/unix="\n")
        "\r\n"=>'\\n', "\r"=>'\\n', "\n"=>'\\n',
        // other (closing tag is for XHTML compliance)
        "\0"=>'\\0', '</'=>'<\\/'
    );

    // unicode characters can be detected by checking the hex value of a character
    //  00 - 7F : ascii char (roman alphabet + punctuation)
    //  80 - BF : byte 2, 3 or 4 of a unicode char
    //  C0 - DF : 1st byte of 2-byte char
    //  E0 - EF : 1st byte of 3-byte char
    //  F0 - FF : 1st byte of 4-byte char
    // if the string doesn't match any of the above, it might be
    //  80 - FF : single-byte, non-ascii char
    public $search_unicode_chars = '/[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x00-\xff]/';

    // array used to figure what number to decrement from character order value
    // according to number of characters used to map unicode to ascii by utf-8
    public $utf8_decrement = array(
        1 => 0,
        2 => 192,
        3 => 224,
        4 => 240
    );

    // the number of bits to shift each character by
    public $utf8_shift = array(
        1 => array(0=>0),
        2 => array(0=>6,  1=>0),
        3 => array(0=>12, 1=>6,  2=>0),
        4 => array(0=>18, 1=>12, 2=>6, 3=>0)
    );


    /**
     * is_html
     *
     * @return xxx
     */
    function is_html() {
        return preg_match('/\.html?$/', $this->file->get_filename());
    }

    /**
     * get_name
     *
     * @return xxx
     */
    function get_name() {
        if ($this->is_html()) {
            return $this->html_get_name();
        } else {
            return $this->xml_get_name();
        }
    }

    /**
     * get_title
     *
     * @return xxx
     */
    function get_title() {
        if ($this->is_html()) {
            return $this->html_get_name(false);
        } else {
            return $this->xml_get_name(false);
        }
    }

    /**
     * get_entrytext
     *
     * @return xxx
     */
    function get_entrytext() {
        if ($this->is_html()) {
            return $this->html_get_entrytext();
        } else {
            return $this->xml_get_entrytext();
        }
    }

    /**
     * get_nextquiz
     *
     * @return xxx
     */
    function get_nextquiz() {
        if ($this->is_html()) {
            return $this->html_get_nextquiz();
        } else {
            return $this->xml_get_nextquiz();
        }
    }

    // function for html files

    /**
     * html_get_name
     *
     * @param xxx $textonly (optional, default=true)
     * @return xxx
     */
    function html_get_name($textonly=true) {
        if (! isset($this->name)) {
            $this->name = '';
            $this->title = '';

            if (! $this->get_filecontents()) {
                // empty file - shouldn't happen !!
                return false;
            }
            if (preg_match('/<h2[^>]*class="ExerciseTitle"[^>]*>(.*?)<\/h2>/is', $this->filecontents, $matches)) {
                $this->name = trim(strip_tags($matches[1]));
                $this->title = trim($matches[1]);
            }
            if (! $this->name) {
                if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $this->filecontents, $matches)) {
                    $this->name = trim(strip_tags($matches[1]));
                    if (! $this->title) {
                        $this->title = trim($matches[1]);
                    }
                }
            }
            $this->name = hotpot_textlib('entities_to_utf8', $this->name, true);
            $this->title = hotpot_textlib('entities_to_utf8', $this->title, true);
        }
        if ($textonly) {
            return $this->name;
        } else {
            return $this->title;
        }
    }

    /**
     * html_get_entrytext
     *
     * @return xxx
     */
    function html_get_entrytext() {
        if (! isset($this->entrytext)) {
            $this->entrytext = '';

            if (! $this->get_filecontents()) {
                // empty file - shouldn't happen !!
                return false;
            }
            if (preg_match('/<h3[^>]*class="ExerciseSubtitle"[^>]*>\s*(.*?)\s*<\/h3>/is', $this->filecontents, $matches)) {
                $this->entrytext .= '<div>'.$matches[1].'</div>';
            }
            if (preg_match('/<div[^>]*id="Instructions"[^>]*>\s*(.*?)\s*<\/div>/is', $this->filecontents, $matches)) {
                $this->entrytext .= '<div>'.$matches[1].'</div>';
            }
        }
        return $this->entrytext;
    }

    /**
     * html_get_nextquiz
     *
     * @return xxx
     */
    function html_get_nextquiz() {
        if (! isset($this->nextquiz)) {
            $this->nextquiz = false;

            if (! $this->get_filecontents()) {
                // empty file - shouldn't happen !!
                return false;
            }
            if (preg_match('/<div[^>]*class="NavButtonBar"[^>]*>(.*?)<\/div>/is', $this->filecontents, $matches)) {

                $navbuttonbar = $matches[1];
                if (preg_match_all('/<button[^>]*onclick="'."location='([^']*)'".'[^"]*"[^>]*>/is', $navbuttonbar, $matches)) {

                    $lastbutton = count($matches[0])-1;
                    $this->nextquiz = $this->xml_locate_file(dirname($this->filepath).'/'.$matches[1][$lastbutton]);
                }
            }
        }
        return $this->nextquiz;
    }

    // functions for xml files

    /**
     * xml_get_name
     *
     * @param xxx $textonly (optional, default=true)
     * @return xxx
     */
    function xml_get_name($textonly=true) {
        if (! isset($this->name)) {
            $this->name = '';
            $this->title = '';

            if (! $this->xml_get_filecontents()) {
                // could not detect Hot Potatoes quiz type - shouldn't happen !!
                return false;
            }
            $this->title = $this->xml_value('data,title');
            $this->title = hotpot_textlib('entities_to_utf8', $this->title, true);
            $this->name = trim(strip_tags($this->title)); // sanitize
        }
        if ($textonly) {
            return $this->name;
        } else {
            return $this->title;
        }
    }

    /**
     * xml_get_entrytext
     *
     * @return xxx
     */
    function xml_get_entrytext() {
        if (! isset($this->entrytext)) {
            $this->entrytext = '';

            if (! $this->xml_get_filecontents()) {
                // could not detect Hot Potatoes quiz type - shouldn't happen !!
                return false;
            }
            if ($intro = $this->xml_value($this->hbs_software.'-config-file,'.$this->hbs_quiztype.',exercise-subtitle')) {
                $this->entrytext .= '<h3>'.$intro.'</h3>';
            }
            if ($intro = $this->xml_value($this->hbs_software.'-config-file,'.$this->hbs_quiztype.',instructions')) {
                $this->entrytext .= '<div>'.$intro.'</div>';
            }
        }
        return $this->entrytext;
    }

    /**
     * xml_get_nextquiz
     *
     * @return xxx
     */
    function xml_get_nextquiz() {
        if (! isset($this->nextquiz)) {
            $this->nextquiz = false;

            if (! $this->xml_get_filecontents()) {
                // could not detect Hot Potatoes quiz type in xml file - shouldn't happen !!
                return false;
            }

            if (! $this->xml_value_int($this->hbs_software.'-config-file,global,include-next-ex')) {
                // next exercise is not enabled for this quiz
                return false;
            }

            if (! $nextquiz = $this->xml_value($this->hbs_software.'-config-file,'.$this->hbs_quiztype.',next-ex-url')) {
                // there is no next URL given for the next quiz
                return false;
            }

            // set the URL of the next quiz
            $this->nextquiz = $this->xml_locate_file(dirname($this->filepath).'/'.$nextquiz);
        }
        return $this->nextquiz;
    }

    /**
     * xml_locate_file
     *
     * @param xxx $file
     * @param xxx $filetypes (optional, default=null)
     * @return xxx
     */
    function xml_locate_file($file, $filetypes=null) {
        if (preg_match('/^https?:\/\//', $file)) {
            return $file;
        }

        $filepath = $this->basepath.'/'.ltrim($file, '/');
        if (file_exists($filepath)) {
            return $file;
        }

        $filename = basename($filepath);
        if (! $pos = strrpos($filename, '.')) {
            return $file;
        }

        $filetype = substr($filename, $pos + 1);
        if ($filetype=='htm' || $filetype=='html') {
            // $file is a local html file that doesn't exist
            // so search for a HP source file with the same name
            $len = strlen($filetype);
            $filepath = substr($filepath, 0, -$len);
            if (is_null($filetypes)) {
                $filetypes = array('jcl', 'jcw', 'jmt', 'jmx', 'jqz'); // 'jbc' for HP 5 ?
            }
            foreach ($filetypes as $filetype) {
                if (file_exists($filepath.$filetype)) {
                    return substr($file, 0, -$len).$filetype;
                }
            }
        }

        // valid $file could not be found :-(
        return '';
    }

    /**
     * xml_get_filecontents
     *
     * @return xxx
     */
    function xml_get_filecontents() {
        if (! isset($this->xml)) {
            $this->xml = false;
            $this->xml_root = '';

            if (! $this->get_filecontents()) {
                // empty file - shouldn't happen !!
                return false;
            }

            $this->compact_filecontents();
            $this->pre_xmlize_filecontents();

            // define root of XML tree
            $this->xml_root = $this->hbs_software.'-'.$this->hbs_quiztype.'-file';

            // convert to XML tree using xmlize()
            if (! $this->xml = xmlize($this->filecontents, 0)) {
                debugging('Could not parse XML file: '.$this->filepath);
            } else if (! array_key_exists($this->xml_root, $this->xml)) {
                debugging('Could not find XML root node: '.$this->xml_root);
            }

            // merge config settings, if necessary
            if (isset($this->config) && $this->config && $this->config->get_filecontents()) {

                $this->config->compact_filecontents(array('header-code'));
                $xml = xmlize($this->config->filecontents, 0);

                $config_file = $this->hbs_software.'-config-file';
                if (isset($xml[$config_file]['#']) && isset($this->xml[$this->xml_root]['#'])) {

                    // make sure the xml tree has the expected structure
                    if (! isset($this->xml[$this->xml_root]['#'][$config_file][0]['#'])) {
                        if (! isset($this->xml[$this->xml_root]['#'][$config_file][0])) {
                            if (! isset($this->xml[$this->xml_root]['#'][$config_file])) {
                                $this->xml[$this->xml_root]['#'][$config_file] = array();
                            }
                            $this->xml[$this->xml_root]['#'][$config_file][0] = array();
                        }
                        $this->xml[$this->xml_root]['#'][$config_file][0]['#'] = array();
                    }

                    // reference to the config values in $this->xml
                    $config = &$this->xml[$this->xml_root]['#'][$config_file][0]['#'];

                    $items = array_keys($xml[$config_file]['#']);
                    foreach ($items as $item) { // 'global', 'jcloze', ... etc ..., 'version'
                        if (is_array($xml[$config_file]['#'][$item][0]['#'])) {
                            $values = array_keys($xml[$config_file]['#'][$item][0]['#']);
                            foreach ($values as $value) {
                                $config[$item][0]['#'][$value] = $xml[$config_file]['#'][$item][0]['#'][$value];
                            }
                        }
                    }
                }
            }
        }
        return $this->xml ? true : false;
    }

    /**
     * pre_xmlize_filecontents
     */
    function pre_xmlize_filecontents() {
        if ($this->filecontents) {
            // encode all ampersands that are not part of HTML entities
            // http://stackoverflow.com/questions/310572/regex-in-php-to-match-that-arent-html-entities
            // Note: we could also use '<![CDATA[&]]>' as the replace string
            $search = '/&(?!(?:[a-zA-Z]+|#[0-9]+|#x[0-9a-fA-F]+);)/';
            $this->filecontents = preg_replace($search, '&amp;', $this->filecontents);

            // unicode characters can be detected by checking the hex value of a character
            //  00 - 7F : ascii char (control chars + roman alphabet + punctuation)
            //  80 - BF : byte 2, 3 or 4 of a unicode char
            //  C0 - DF : 1st byte of 2-byte char
            //  E0 - EF : 1st byte of 3-byte char
            //  F0 - FF : 1st byte of 4-byte char
            // if the string doesn't match the above, it might be
            //  80 - FF : single-byte, non-ascii char
            $search = '/'.'[\xc0-\xdf][\x80-\xbf]{1}'.'|'.
                          '[\xe0-\xef][\x80-\xbf]{2}'.'|'.
                          '[\xf0-\xff][\x80-\xbf]{3}'.'|'.
                          '[\x80-\xff]'.'/';
            $callback = array($this, 'utf8_char_to_html_entity');
            $this->filecontents = preg_replace_callback($search, $callback, $this->filecontents);

            // the following control characters are not allowed in XML
            // and need to be removed because they will break xmlize()
            // basically this is the range 00-1F and the delete key 7F
            // but excluding tab 09, newline 0A and carriage return 0D
            $search = '/[\x00-\x08\x0b-\x0c\x0e-\x1f\x7f]/';
            $this->filecontents = preg_replace($search, '', $this->filecontents);
        }
    }

    function utf8_char_to_html_entity($char, $ampersand='&') {
        // thanks to: http://www.zend.com/codex.php?id=835&single=1
        if (is_array($char)) {
            $char = $char[0];
        }
        $dec = 0;
        $len = strlen($char);
        for ($pos=0; $pos<$len; $pos++) {
            $ord = ord ($char{$pos});
            $ord -= ($pos ? 128 : $this->utf8_decrement[$len]);
            $dec += ($ord << $this->utf8_shift[$len][$pos]);
        }

        return $ampersand.'#x'.sprintf('%04X', $dec).';';
    }

    /**
     * xml_value
     *
     * @uses $CFG
     * @param xxx $tags
     * @param xxx $more_tags (optional, default=null)
     * @param xxx $default (optional, default='')
     * @param xxx $nl2br (optional, default=true)
     * @return xxx
     */
    function xml_value($tags, $more_tags=null, $default='', $nl2br=true) {
        global $CFG;
        static $block_elements = null;

        // set reference to a $value in $this->xml array
        if (isset($this->xml_root)) {
            $all_tags = "['".$this->xml_root."']['#']";
        } else {
            $all_tags = ''; // shouldn't happen
        }
        if ($tags) {
            $all_tags .= "['".str_replace(",", "'][0]['#']['", $tags)."']";
        }
        if ($more_tags===null) {
            $all_tags .= "[0]['#']";
        } else {
            $all_tags .= $more_tags;
        }
        $all_tags = explode('][', str_replace("'", '', substr($all_tags, 1, -1)));

        $value = $this->xml;
        foreach ($all_tags as $tag) {
            if (! is_array($value)) {
                return null;
            }
            if (! array_key_exists($tag, $value)) {
                return null;
            }
            $value = $value[$tag];
        }

        if (is_string($value)) {

            // decode angle brackets
            $value = strtr($value, array('&#x003C;'=>'<', '&#x003E;'=>'>', '&#x0026;'=>'&'));

            // remove white space before and after HTML block elements
            if ($block_elements===null) {
                // set regexp to detect white space around html block elements
                $block_elements = array(
                    //'div','p','pre','blockquote','center',
                    //'h1','h2','h3','h4','h5','h6','hr',
                    'table','caption','colgroup','col','tbody','thead','tfoot','tr','th','td',
                    'ol','ul','dl','li','dt','dd',
                    'applet','embed','object','param',
                    'select','optgroup','option',
                    'fieldset','legend',
                    'frameset','frame'
                );
                $space = '(?:\s|(?:<br[^>]*>))*'; // unwanted white space
                $block_elements = '(?:\/?'.implode(')|(?:\/?', $block_elements).')';
                $block_elements = '/'.$space.'(<(?:'.$block_elements.')[^>]*>)'.$space.'/is';
                //.'(?='.'<)' // followed by the start of another tag
            }
            $value = preg_replace($block_elements, '$1', $value);

            // standardize whitespace within tags
            // $1 : start of tag i.e. "<"
            // $2 : chars in tag (including whitespace and <br />)
            // $3 : end of tag i.e. ">"
            $search = '/(<)([^>]*)(>)/is';
            $callback = array($this, 'single_line');
            $value = preg_replace_callback($search, $callback, $value);

            // replace remaining newlines with <br /> but not in <script> or <style> blocks
            // $1 : chars before open text
            // $2 : text to be converted
            // $3 : chars following text
            if ($nl2br) {
                $search = '/(^|(?:<\/(?:script|style)>\s?))(.*?)((?:\s?<(?:script|style)[^>]*>)|$)/is';
                $callback = array($this, 'xml_value_nl2br');
                $value = preg_replace_callback($search, $callback, $value);
            }

            // encode unicode characters as HTML entities
            // (in particular, accented charaters that have not been encoded by HP)
            $value = hotpot_textlib('utf8_to_entities', $value);
        }
        return $value;
    }

    /**
     * single_line
     *
     * @param xxx $match
     * @return xxx
     */
    function single_line($match) {
        if (is_string($match)) {
            $before = '';
            $text   = $match;
            $after  = '';
        } else {
            $before = $match[1];
            $text   = $match[2];
            $after  = $match[3];
        }
        return $before.preg_replace('/(?:(?:<br[^>]*>)|\s)+/is', ' ', $text).$after;
    }

    /**
     * xml_value_nl2br
     *
     * @param xxx $match
     * @return xxx
     */
    function xml_value_nl2br($match) {
        $before = $match[1];
        $text   = $match[2];
        $after  = $match[3];
        return $before.str_replace("\n", '<br />', $text).$after;
    }

    /**
     * xml_value_bool
     *
     * @param xxx $tags
     * @param xxx $more_tags (optional, default=null)
     * @param xxx $default (optional, default='')
     * @return xxx
     */
    function xml_value_bool($tags, $more_tags=null, $default=false) {
        $value = $this->xml_value($tags, $more_tags, $default, false);
        if (empty($value)) {
            return 'false';
        } else {
            return 'true';
        }
    }

    /**
     * xml_value_int
     *
     * @param xxx $tags
     * @param xxx $more_tags (optional, default=null)
     * @param xxx $default (optional, default=0)
     * @return xxx
     */
    function xml_value_int($tags, $more_tags=null, $default=0) {
        $value = $this->xml_value($tags, $more_tags, $default, false);
        return intval($value);
    }

    /**
     * xml_value_js
     *
     * Note: html entities in captions (e.g. messages and button text)
     * do not need to be converted to javascript "\u" encoding
     * but those in question/answer arrays, generally do
     *
     * @param xxx $tags
     * @param xxx $more_tags (optional, default=null)
     * @param xxx $default (optional, default='')
     * @param xxx $nl2br (optional, default=true)
     * @param xxx $convert_to_unicode (optional, default=true)
     * @return xxx
     */
    function xml_value_js($tags, $more_tags=null, $default='', $nl2br=true, $convert_to_unicode=true) {
        $value = $this->xml_value($tags, $more_tags, $default, $nl2br);
        return $this->js_value_safe($value, $convert_to_unicode);
    }

    /**
     * js_value_safe
     *
     * @param xxx $str
     * @param xxx $convert_to_unicode (optional, default=false)
     * @return xxx
     */
    function js_value_safe($str, $convert_to_unicode=false) {
        global $CFG;

        if ($convert_to_unicode && $CFG->hotpot_enableobfuscate) {
            // CONTRIB-6084 unencode HTML entities
            // before converting to JavaScript unicode
            $str = hotpot_textlib('entities_to_utf8', $str, true);
            // convert ALL chars to Javascript unicode
            $callback = array($this, 'js_unicode_char');
            $str = preg_replace_callback($this->search_unicode_chars, $callback, $str);
        } else {
            // escape backslashes, quotes, etc
            $str = strtr($str, $this->javascript_replace_pairs);
        }

        return $str;
    }

    /**
     * js_unicode_char
     *
     * @param xxx $match
     * @return xxx
     */
    function js_unicode_char($match) {
        $num = $match[0]; // the UTF-8 char
        $num = hotpot_textlib('utf8ord', $num);
        $num = strtoupper(dechex($num));
        return sprintf('\\u%04s', $num);
    }

    /**
     * synchronize_moodle_settings
     *
     * @param xxx $hotpot (passed by reference)
     * @return xxx
     */
    function synchronize_moodle_settings(&$hotpot) {
        $name = $this->get_name();
        if ($name=='' || $name==$hotpot->name) {
            return false;
        } else {
            $hotpot->name = $name;
            return true;
        }
    }
} // end class
