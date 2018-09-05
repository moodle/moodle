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
 * mod/hotpot/mediafilter/hotpot/class.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get the parent class (=hotpot_mediafilter)
require_once($CFG->dirroot.'/mod/hotpot/mediafilter/class.php');

/**
 * hotpot_mediafilter_hotpot
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_mediafilter_hotpot extends hotpot_mediafilter {

    /**
     * mediaplugin_filter
     *
     * @param xxx $hotpot
     * @param xxx $text
     * @param xxx $options (optional, default=array)
     * @return xxx
     */
    function mediaplugin_filter($hotpot, $text, $options=array())  {
        global $CFG, $PAGE;

        // Keep track of the id of the current quiz
        // so that eolas_fix.js is only included once in each quiz
        // Note: the cron script calls this method for multiple quizzes
        static $eolas_fix_applied = 0;

        if (! is_string($text)) {
            // non string data can not be filtered anyway
            return $text;
        }
        $newtext = $text; // fullclone is slow and not needed here

        foreach (array_keys($this->media_filetypes) as $filetype) {

            // set $adminsetting, the name of the $CFG setting, if any, which enables/disables filtering of this file type
            $adminsetting = '';
            if (preg_match('/^[a-z]+$/', $filetype)) {
                $hotpot_enable = 'hotpot_enable'.$filetype;
                $filter_mediaplugin_enable = 'filter_mediaplugin_enable_'.$filetype;

                if (isset($CFG->$hotpot_enable)) {
                    $adminsetting = $hotpot_enable;
                } else if (isset($CFG->$filter_mediaplugin_enable)) {
                    $adminsetting = $filter_mediaplugin_enable;
                }
            }

            // set $search and $replace strings
            $search = '/<a.*?href="([^"?>]*\.'.$filetype.'[^">]*)"[^>]*>.*?<\/a>/is';
            if ($adminsetting=='' || $CFG->$adminsetting) {
                // filtering of this file type is allowed
                $callback = array($this, 'hotpot_mediaplugin_filter');
                $callback = partial($callback, $filetype, $options);
                $newtext = preg_replace_callback($search, $callback, $newtext, -1, $count);
            } else {
                // filtering of this file type is disabled
                $replace = '$1<br />'.get_string('error_disabledfilter', 'mod_hotpot', $adminsetting);
                $newtext = preg_replace($search, $replace, $newtext, -1, $count);
            }

            if ($count>0) {
                break;
            }
        }

        if (is_null($newtext) || $newtext==$text) {
            // error or not filtered
            return $text;
        }

        if ($eolas_fix_applied==$hotpot->id) {
            // do nothing - the external javascripts have already been included for this quiz
        } else {
            $PAGE->requires->js('/mod/hotpot/mediafilter/ufo.js', true);
            $PAGE->requires->js('/mod/hotpot/mediafilter/eolas_fix.js');
            //$newtext .= "\n".'<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/hotpot/mediafilter/ufo.js"></script>';
            //$newtext .= "\n".'<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/hotpot/mediafilter/eolas_fix.js" defer="defer"></script>';
            $eolas_fix_applied = $hotpot->id;
        }

        return $newtext;
    }

    /**
     * hotpot_mediaplugin_filter
     *
     * @param xxx $match
     * @param xxx $filetype
     * @param xxx $options
     * @return xxx
     */
    function hotpot_mediaplugin_filter($filetype, $options, $match)  {
        $link = $match[0];
        $mediaurl = $match[1];

        // get a valid $player name
        if (isset($options['player'])) {
            $player = $options['player'];
        } else {
            $player = '';
        }
        if ($player=='') {
            $player = $this->defaultplayer;
        } else if (! array_key_exists($player, $this->players)) {
            debugging('Invalid media player requested: '.$player);
            $player = $this->defaultplayer;
        }

        // merge player options
        if ($player==$this->defaultplayer) {
            $options = array_merge($this->players[$player]->options, $options);
        } else {
            $options = array_merge($this->players[$this->defaultplayer]->options, $this->players[$player]->options, $options);
        }

        // generate content for required player
        $content = $this->players[$player]->generate($filetype, $link, $mediaurl, $options);

        return $content;
    }
}
