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
 * Source type: qedoc
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/source/class.php');

/**
 * hotpot_source_qedoc
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_source_qedoc extends hotpot_source {

    /**
     * is_quizfile
     *
     * @param xxx $sourcefile
     * @return xxx
     */
    static public function is_quizfile($sourcefile)  {
        // e.g. http://www.qedoc.net/library/PLJUB_019.zip
        $search = '/http:\/\/www\.qedoc.(?:com|net)\/library\/\w+\.zip/i';
        return preg_match($search, $sourcefile->get_source());

        // Note: we may want to detect the following as well:
        // http://www.qedoc.net/qqp/jnlp/PLJUB_019.jnlp
    }

    /**
     * get_name
     *
     * @return xxx
     */
    function get_name()  {
        return $this->file->get_filename();
    }

    /**
     * get_title
     *
     * @return xxx
     */
    function get_title()  {
        return $this->file->get_filename();
    }
}
