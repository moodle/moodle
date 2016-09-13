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
 * This file keeps track of upgrades to the settings block
 *
 * Sometimes, changes between versions involve alterations to database structures
 * and other major things that may break installations.
 *
 * The upgrade function in this file will attempt to perform all the necessary
 * actions to upgrade your older installation to the current version.
 *
 * If there's something it cannot do itself, it will tell you what you need to do.
 *
 * The commands in here will all be database-neutral, using the methods of
 * database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    contrib
 * @subpackage block_iomad_progress
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @global moodle_database $DB
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_iomad_progress_upgrade($oldversion, $block) {
    global $DB;

    // Fix bad filtering on posted_to values.
    if ($oldversion >= 2013073000 && $oldversion < 2013080500) {
        $configs = $DB->get_records('block_instances', array('blockname' => 'iomad_progress'));
        foreach ($configs as $blockid => $blockrecord) {
            $config = (array)unserialize(base64_decode($blockrecord->configdata));
            foreach ($config as $key => $value) {
                if ($value == 'postedto') {
                    $config[$key] = 'posted_to';
                }
            }
            $configdata = base64_encode(serialize((object)$config));
            $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $blockid));
        }
    }

    return true;
}