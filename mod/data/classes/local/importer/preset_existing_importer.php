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

namespace mod_data\local\importer;

use mod_data\manager;

/**
 * Data preset importer for existing presets
 * @package    mod_data
 * @copyright  2022 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class preset_existing_importer extends preset_importer {

    /** @var int user id. */
    protected $userid;

    /** @var string fullname of the preset. */
    private $fullname;

    /**
     * Constructor
     *
     * @param manager $manager
     * @param string $fullname
     */
    public function __construct(manager $manager, string $fullname) {
        global $USER;

        list($userid, $shortname) = explode('/', $fullname, 2);
        $context = $manager->get_context();
        if ($userid &&
            ($userid != $USER->id) &&
            !has_capability('mod/data:manageuserpresets', $context) &&
            !has_capability('mod/data:viewalluserpresets', $context)
        ) {
            throw new \coding_exception('Invalid preset provided');
        }

        $this->userid = intval($userid);
        $this->fullname = $fullname;
        $cm = $manager->get_coursemodule();
        $course = $cm->get_course();
        $filepath = data_preset_path($course, $userid, $shortname);
        parent::__construct($manager, $filepath);
    }

    /**
     * Returns user ID
     *
     * @return int userid
     */
    public function get_userid(): int {
        return $this->userid;
    }

    /**
     * Returns the information we need to build the importer selector.
     *
     * @return array Value and name for the preset importer selector
     */
    public function get_preset_selector(): array {
        return ['name' => 'fullname', 'value' => $this->get_userid().'/'.$this->get_directory()];
    }
}
