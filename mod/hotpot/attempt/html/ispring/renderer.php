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
 * Output format: html_ispring
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/attempt/html/renderer.php');

/**
 * mod_hotpot_attempt_html_ispring_renderer
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class mod_hotpot_attempt_html_ispring_renderer extends mod_hotpot_attempt_html_renderer {

    /**
     * List of source types which this renderer can handle
     *
     * @return array of strings
     */
    static public function sourcetypes()  {
        return array('html_ispring');
    }

    /**
     * preprocessing
     *
     * @return xxx
     */
    function preprocessing()  {
        if ($this->cache_uptodate) {
            return true;
        }

        if (! $this->hotpot->source->get_filecontents()) {
            // empty source file - shouldn't happen !!
            return false;
        }

        // remove doctype
        $search = '/\s*(?:<!--\s*)?<!DOCTYPE[^>]*>\s*(?:-->\s*)?/s';
        $this->hotpot->source->filecontents = preg_replace($search, '', $this->hotpot->source->filecontents);

        // replace <object> with link and force through filters
        $search = '/<object id="presentation"[^>]*>.*?<param name="movie" value="([^">]*)"[^>]*>.*?<\/object>/is';
        $replace = '<a href="$1?d=800x600">$1</a>';
        $this->hotpot->source->filecontents = preg_replace($search, $replace, $this->hotpot->source->filecontents);

        // remove fixprompt.js
        $search = '/<script[^>]*src="[^">]*fixprompt.js"[^>]*(?:(?:\/>)|(?:<\/script>))\s*/s';
        $this->hotpot->source->filecontents = preg_replace($search, '', $this->hotpot->source->filecontents);

        parent::preprocessing();
    }
}
