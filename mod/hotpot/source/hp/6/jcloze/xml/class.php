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
 * Class to represent the source of a HotPot quiz
 * Source type: hp_6_jcloze_xml
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/source/hp/6/jcloze/class.php');

/**
 * hotpot_source_hp_6_jcloze_xml
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_source_hp_6_jcloze_xml extends hotpot_source_hp_6_jcloze {

    /**
     * is_quizfile
     *
     * @param xxx $sourcefile
     * @return xxx
     */
    static public function is_quizfile($sourcefile)  {
        return preg_match('/\.jcl$/', $sourcefile->get_filename());
    }

    /**
     * compact_filecontents
     */
    function compact_filecontents($tags=null) {
        // remove white space within tags
        parent::compact_filecontents($tags);

        // fix white space and html entities in open text

        // Note: when testing this code, be aware that
        // xmlize() behaves differently in PHP4 and PHP5

        $search = '/(?<=<gap-fill)'.'>.*?<'.'(?=\/gap-fill>)/s';
        if (preg_match($search, $this->filecontents, $matches, PREG_OFFSET_CAPTURE)) {
            $match = $matches[0][0];
            $start = $matches[0][1];
            $length = strlen($match);

            // convert newlines to html line breaks
            $newlines = array(
                "\r\n" => '&lt;br /&gt;',
                "\r"   => '&lt;br /&gt;',
                "\n"   => '&lt;br /&gt;',
            );
            $match = strtr($match, $newlines);

            // make sure there is at least one space between the gaps
            $search = '/(?<=<\/question-record>)(?=<question-record>)/';
            $match = preg_replace($search, ' ', $match);

            // add <open-text> tags around open text
            $search = array(
                '/(^>)([^<>]+?)(<question-record>)/', // start text
                '/(<\/question-record>)([^<>]+?)(<question-record>)/',
                '/(<\/question-record>)([^<>]+?)(<$)/', // end text
            );
            $match = preg_replace($search, '$1<open-text>$2</open-text>$3', $match);

            // surround ampersands in open text by CDATA start and end tags
            $search = '/(?<=>)([^<]*)(?=<)/s';
            $callback = array($this, 'compact_filecontents_opentext');
            $match = preg_replace_callback($search, $callback, $match);

            $this->filecontents = substr_replace($this->filecontents, $match, $start, $length);
        }
    }

    /**
     * compact_filecontents_opentext
     *
     * @param xxx $match
     * @return xxx
     */
    function compact_filecontents_opentext($match)  {
        $search = '/&[a-zA-Z0-9#;]*;/';
        $callback = array($this, 'compact_filecontents_entities');
        return preg_replace_callback($search, $callback, $match[0]);
    }

    /**
     * compact_filecontents_entities
     *
     * @param xxx $match
     * @return xxx
     */
    function compact_filecontents_entities($match)  {
        // these html entities are coverted back to plain text
        static $html_entities = array(
            '&apos;' => "'",
            '&quot;' => '"',
            '&lt;'   => '<',
            '&gt;'   => '>',
            '&amp;'  => '&'
        );
        return '<![CDATA['.strtr($match[0], $html_entities).']]>';
    }
}
