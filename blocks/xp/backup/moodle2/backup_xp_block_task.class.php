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
 * Block XP backup task.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_xp\di;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/xp/backup/moodle2/backup_xp_stepslib.php');

/**
 * Block XP backup task class.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_xp_block_task extends backup_block_task {

    /**
     * Define settings.
     */
    protected function define_my_settings() {
    }

    /**
     * Define steps.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_xp_block_structure_step('xp', 'xp.xml'));
    }

    /**
     * File areas.
     *
     * @return array
     */
    public function get_fileareas() {
        return [];
    }

    /**
     * Config data encoded attributes.
     */
    public function get_configdata_encoded_attributes() {
    }

    /**
     * Encore content links.
     *
     * @param string $content The content.
     * @return string
     */
    public static function encode_content_links($content) {
        $manager = di::get('backup_content_manager');
        return $manager->encode_content_links($content);
    }
}
