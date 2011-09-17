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
 * Post installation and migration code.
 *
 * @package    tool
 * @subpackage bloglevelupgrade
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_tool_bloglevelupgrade_install() {
    global $CFG, $OUTPUT;

    // this is a hack - admins were long ago instructed to upgrade blog levels,
    // the problem is that blog is not supposed to be course level activity!!

    if (!empty($CFG->bloglevel_upgrade_complete)) {
        // somebody already upgrades, we do not need this any more
        unset_config('bloglevel_upgrade_complete');
        return;
    }

    if (!isset($CFG->bloglevel)) {
        // fresh install?
        return;
    }

    if (($CFG->bloglevel == BLOG_COURSE_LEVEL || $CFG->bloglevel == BLOG_GROUP_LEVEL)) {
        // inform admins that some settings require attention after upgrade
        $site = get_site();

        $a = new StdClass;
        $a->sitename = $site->fullname;
        $a->fixurl   = "$CFG->wwwroot/$CFG->admin/tool/bloglevelupgrade/index.php";

        $subject = get_string('bloglevelupgrade', 'tool_bloglevelupgrade');
        $description = get_string('bloglevelupgradedescription', 'tool_bloglevelupgrade', $a);

        // can not use messaging here because it is not configured yet!
        upgrade_log(UPGRADE_LOG_NOTICE, null, $subject, $description);
        set_config('tool_bloglevelupgrade_pending', 1);

        echo $OUTPUT->notification($description);
    }
}


