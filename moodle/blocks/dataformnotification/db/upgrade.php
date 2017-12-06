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
 * This file keeps track of upgrades to the block.
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
 * @package block_dataformnotification
 * @since Moodle 2.8
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade the dataform notification block.
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_dataformnotification_upgrade($oldversion, $block) {
    global $DB;
    $blockname = 'dataformnotification';

    $newversion = 2014111006;
    if ($oldversion < $newversion) {
        // Adjust config.
        block_dataformnotification_config_adjustments_2014111006();

        upgrade_block_savepoint(true, $newversion, $blockname);
    }

    return true;
}

/**
 * Apply config structural changes for 2014111006.
 * @return void
 */
function block_dataformnotification_config_adjustments_2014111006() {
    global $DB;

    $blockname = 'dataformnotification';

    if ($instances = $DB->get_records('block_instances', array('blockname' => $blockname))) {
        foreach ($instances as $instance) {
            if (!$instance->configdata) {
                continue;
            }

            $update = false;

            // Unpack the config data.
            $config = unserialize(base64_decode($instance->configdata));

            // Replace message with contenttext.
            if (isset($config->message)) {
                $config->contenttext = $config->message;
                unset($config->message);
                $update = true;
            }

            // Replace indvidual recipient with an array.
            $recipient = array();
            foreach (array('admin', 'support', 'author', 'role', 'username', 'email') as $recp) {
                if (isset($config->{"recipient$recp"})) {
                    $recipient[$recp] = $config->{"recipient$recp"};
                    unset($config->{"recipient$recp"});
                }
            }
            if ($recipient) {
                $config->recipient = $recipient;
                $update = true;
            }

            // Update the config data.
            if ($update) {
                $instance->configdata = base64_encode(serialize($config));
                $DB->update_record('block_instances', $instance);
            }
        }
    }
}
