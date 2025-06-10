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
 * Class for preparing data for Roles.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\roles;

/**
 * Class for preparing data for Roles.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class role extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'roles';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Role ID.',
                'default' => 0,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'description' => 'Role name.',
                'default' => '',
            ],
            'shortname' => [
                'type' => PARAM_TEXT,
                'description' => 'Role short name.',
                'default' => '',
            ],
        ];
    }

    /**
     * Hook to execute before an export.
     *
     * Please note that at this stage the data has already been validated and therefore
     * any new data being set will not be validated before it is sent to the database.
     *
     * This is only intended to be used by child classes, do not put any logic here!
     *
     * @return void
     */
    protected function before_export() {
        global $CFG;
        require_once($CFG->libdir . '/accesslib.php');

        $role = $this->to_record();
        if (empty($role->name)) {
            $name = role_get_name($role);
            $this->set('name', $name);
        }
    }
}
