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
 * Default admin config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

/**
 * Default admin config.
 *
 * This class holds the default admin config values.
 *
 * What you should know: The admin config, and course config share a
 * special bond. Although this is not a particularily good design, there
 * are times when we will assume that there if an admin config was set
 * it takes precedence over a default course setting. For instance, we
 * may be doing this:
 *
 * new config_stack([
 *     new default_admin_config(),
 *     new default_course_world_config()
 * ]);
 *
 * That might become difficult to debug if we do not pay attention
 * when adding new admin configs, so keep that in mind when you
 * add more values to this class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_admin_config extends immutable_config {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(new static_config([
            'context' => CONTEXT_COURSE,
            'adminscanearnxp' => 0,
            'enablepromoincourses' => 1,
            'keeplogs' => 3,
            'navbardisplay' => 0,

            'enablecheatguard' => 1,
            'enableinfos' => 1,
            'enableladder' => 1,
            'enablelevelupnotif' => 1,
            'identitymode' => course_world_config::IDENTITY_ON,
            'laddercols' => 'xp,progress',
            'levelsdata' => '',
            'maxactionspertime' => 10,
            'neighbours' => 0,
            'rankmode' => course_world_config::RANK_ON,
            'timebetweensameactions' => 180,
            'timeformaxactions' => 60,

            'blocktitle' => get_string('levelup', 'block_xp'),
            'blockdescription' => get_string('participatetolevelup', 'block_xp'),
            'blockrecentactivity' => 3,
            'blockrankingsnapshot' => 1,

            'adminnotices' => 1,
            'lastoutofsyncnoticekey' => '',

            'apiroot' => 'https://backend.levelup.plus/api',
            'usagereport' => 1,
            'lastusagereport' => 0,
            'usagereportid' => '',
        ]));
    }

}
