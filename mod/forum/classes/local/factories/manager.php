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
 * Managers factory.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\factories;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/rating/lib.php');

use mod_forum\local\entities\forum as forum_entity;
use mod_forum\local\managers\capability as capability_manager;
use rating_manager;

/**
 * Managers factory.
 *
 * See:
 * https://designpatternsphp.readthedocs.io/en/latest/Creational/SimpleFactory/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** @var legacy_data_mapper $legacydatamapperfactory Legacy data mapper factory */
    private $legacydatamapperfactory;

    /**
     * Constructor.
     *
     * @param legacy_data_mapper $legacydatamapperfactory Legacy data mapper factory
     */
    public function __construct(legacy_data_mapper $legacydatamapperfactory) {
        $this->legacydatamapperfactory = $legacydatamapperfactory;
    }

    /**
     * Create a capability manager for the given forum.
     *
     * @param forum_entity $forum The forum to manage capabilities for
     * @return capability_manager
     */
    public function get_capability_manager(forum_entity $forum) {
        return new capability_manager(
            $forum,
            $this->legacydatamapperfactory->get_forum_data_mapper(),
            $this->legacydatamapperfactory->get_discussion_data_mapper(),
            $this->legacydatamapperfactory->get_post_data_mapper()
        );
    }

    /**
     * Create a rating manager.
     *
     * @return rating_manager
     */
    public function get_rating_manager(): rating_manager {
        return new rating_manager();
    }
}
