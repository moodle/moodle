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
 * Class for loading/storing data categories from the DB.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

/**
 * Class for loading/storing data categories from the DB.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category extends \core\persistent {

    /**
     * Database table.
     */
    const TABLE = 'tool_dataprivacy_category';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'name' => array(
                'type' => PARAM_TEXT,
                'description' => 'The category name.',
            ),
            'description' => array(
                'type' => PARAM_RAW,
                'description' => 'The category description.',
                'null' => NULL_ALLOWED,
                'default' => '',
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
        );
    }

    /**
     * Is this category used?.
     *
     * @return null
     */
    public function is_used() {

        if (\tool_dataprivacy\contextlevel::is_category_used($this->get('id')) ||
                \tool_dataprivacy\context_instance::is_category_used($this->get('id'))) {
            return true;
        }

        $pluginconfig = get_config('tool_dataprivacy');
        $levels = \context_helper::get_all_levels();
        foreach ($levels as $level => $classname) {

            list($unused, $categoryvar) = \tool_dataprivacy\data_registry::var_names_from_context($classname);
            if (!empty($pluginconfig->{$categoryvar}) && $pluginconfig->{$categoryvar} == $this->get('id')) {
                return true;
            }
        }

        return false;
    }
}
