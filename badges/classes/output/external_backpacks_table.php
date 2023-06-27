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
 * List of enabled backpacks for the site.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_badges\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/badgeslib.php');

use html_writer;
use moodle_url;
use table_sql;

/**
 * Backpacks table class.
 *
 * @package    core_badges
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_backpacks_table extends table_sql {

    /**
     * Sets up the table.
     */
    public function __construct() {
        parent::__construct('backpacks');

        $this->context = \context_system::instance();
        // This object should not be used without the right permissions.
        require_capability('moodle/badges:manageglobalsettings', $this->context);

        // Define columns in the table.
        $this->define_table_columns();

        // Define configs.
        $this->define_table_configs();
    }

    /**
     * Setup the headers for the table.
     */
    protected function define_table_columns() {
        $cols = [
            'backpackweburl' => get_string('backpackurl', 'core_badges'),
            'sortorder' => '',
        ];

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    /**
     * Define table configs.
     */
    protected function define_table_configs() {
        $this->collapsible(false);
        $this->sortable(false);
        $this->pageable(false);
    }

}
