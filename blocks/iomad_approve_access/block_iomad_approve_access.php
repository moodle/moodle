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
 * block approve access
 *
 * @package    Block Iomad Approve Access
 * @copyright  2021 Derick Turner
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_iomad_approve_access extends block_base {
    public function init() {
        $this->title = get_string('title', 'block_iomad_approve_access' );
    }

    public function hide_header() {
        return false;
    }

    public function has_config() {
        return false;
    }

    public function get_content() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/blocks/iomad_approve_access/lib.php');
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;

        if (iomad_approve_access::has_users()) {
            $this->content->text   = '<a href="'.new moodle_url('/blocks/iomad_approve_access/approve.php').'">'.
                                      get_string('userstoapprove', 'block_iomad_approve_access').'</a>';
        } else {
            $this->content->text = get_string('noonetoapprove', 'block_iomad_approve_access');
        }
        return $this->content;
    }
}

