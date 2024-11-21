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
 * Default course world config.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\config;

/**
 * Default course world config.
 *
 * The default settings of a typical course world.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class default_course_world_config extends immutable_config {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(new static_config([
            'enabled' => false,
            'enablecheatguard' => true,   // Enable cheat guard.
            'enableladder' => true,       // Enable the ladder.
            'enableinfos' => true,        // Enable the infos page.
            'levels' => 0,                // Not used any more.
            'levelsdata' => '',           // JSON encoded value of the levels data.
            'enablelevelupnotif' => true, // Enable the level up notification.
            // This used to flag whether we use the custom badges, or not. We changed it to be a flag describing whether
            // we need to copy admin badge, or not, or if we are in the legacy state of not using custom badges.
            'enablecustomlevelbadges' => course_world_config::CUSTOM_BADGES_MISSING,
            'maxactionspertime' => 10,           // Max actions during timepermaxactions.
            'timeformaxactions' => 60,           // Time during which max actions cannot be reached.
            'timebetweensameactions' => 180,     // Time between similar actions.
            'identitymode' => course_world_config::IDENTITY_ON, // Identity mode.
            'rankmode' => course_world_config::RANK_ON,         // Rank mode.
            'neighbours' => 0,                                  // Number of neighbours to show on ladder, 0 means everyone.
            'defaultfilters' => course_world_config::DEFAULT_FILTERS_MISSING,  // Flag about the default filters.
            'laddercols' => 'xp,progress',      // Addditional columns to be displayed on the ladder.
            'instructions' => '',                   // Instructions to display on the info page.
            'instructions_format' => FORMAT_HTML,   // Instructions format.
        ]));
    }

}
