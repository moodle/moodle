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

namespace core\moodlenet;

use backup;
use backup_controller;
use cm_info;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Packager to prepare appropriate backup of an activity to share to MoodleNet.
 *
 * @package   core
 * @copyright 2023 Raquel Ortega <raquel.ortega@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_packager extends resource_packager {

    /**
     * Constructor.
     *
     * @param cm_info $cminfo context module information about the resource being packaged.
     * @param int $userid The ID of the user performing the packaging.
     */
    public function __construct(
        cm_info $cminfo,
        int $userid,
    ) {
        // Check backup/restore support.
        if (!plugin_supports('mod', $cminfo->modname , FEATURE_BACKUP_MOODLE2)) {
            throw new \coding_exception("Cannot backup module $cminfo->modname. This module doesn't support the backup feature.");
        }

        parent::__construct($cminfo, $userid, $cminfo->modname);
    }

    /**
     * Get the backup controller for the activity.
     *
     * @return backup_controller the backup controller for the activity.
     */
    protected function get_backup_controller(): backup_controller {
        return new backup_controller(
            backup::TYPE_1ACTIVITY,
            $this->cminfo->id,
            backup::FORMAT_MOODLE,
            backup::INTERACTIVE_NO,
            backup::MODE_GENERAL,
            $this->userid
        );
    }
}
