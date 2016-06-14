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
 *  Censorship filtering
 *
 *  This very simple example of a Text Filter will parse
 *  printed text, blacking out words perceived to be bad
 *
 * @package    filter
 * @subpackage censor
 * @copyright  2004 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//////////////////////////////////////////////////////////////
//  Censorship filtering
//
//  This very simple example of a Text Filter will parse
//  printed text, blacking out words perceived to be bad
//
//  The list of words is in the lang/xx/moodle.php
//
//////////////////////////////////////////////////////////////

class filter_censor extends moodle_text_filter {
    private function _canseecensor() {
        return is_siteadmin(); //TODO: add proper access control
    }

    function hash(){
        $cap = "mod/filter:censor";
        if (is_siteadmin()) {  //TODO: add proper access control
            $cap = "mod/filter:seecensor";
        }
        return $cap;
    }

    function filter($text, array $options = array()){
        static $words;
        global $CFG;

        if (!isset($CFG->filter_censor_badwords)) {
            set_config( 'filter_censor_badwords','' );
        }

        if (empty($words)) {
            $words = array();
            if (empty($CFG->filter_censor_badwords)) {
                $badwords = explode(',',get_string('badwords', 'filter_censor'));
            }
            else {
                $badwords = explode(',', $CFG->filter_censor_badwords);
            }
            foreach ($badwords as $badword) {
                $badword = trim($badword);
                if($this->_canseecensor()){
                    $words[] = new filterobject($badword, '<span class="censoredtexthighlight" title="'.$badword.'">', '</span>',
                        false, false, $badword);
                } else {
                    $words[] = new filterobject($badword, '<span class="censoredtext" title="'.$badword.'">',
                        '</span>', false, false, str_pad('',strlen($badword),'*'));
                }
            }
        }
        return filter_phrases($text, $words);
    }
}


