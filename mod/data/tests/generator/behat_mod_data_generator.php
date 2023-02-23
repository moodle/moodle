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
 * Behat data generator for mod_data.
 *
 * @package   mod_data
 * @category  test
 * @copyright 2022 Noel De Martin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_data_generator extends behat_generator_base {

    /**
     * Get a list of the entities that Behat can create using the generator step.
     *
     * @return array
     */
    protected function get_creatable_entities(): array {
        return [
            'entries' => [
                'singular' => 'entry',
                'datagenerator' => 'entry',
                'required' => ['database'],
                'switchids' => ['database' => 'databaseid', 'user' => 'userid'],
            ],
            'fields' => [
                'singular' => 'field',
                'datagenerator' => 'field',
                'required' => ['database', 'type', 'name'],
                'switchids' => ['database' => 'databaseid'],
            ],
            'templates' => [
                'singular' => 'template',
                'datagenerator' => 'template',
                'required' => ['database', 'name'],
                'switchids' => ['database' => 'databaseid'],
            ],
            'presets' => [
                'singular' => 'preset',
                'datagenerator' => 'preset',
                'required' => ['database', 'name'],
                'switchids' => ['database' => 'databaseid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Get the database id using an activity idnumber.
     *
     * @param string $idnumber
     * @return int The database id
     */
    protected function get_database_id(string $idnumber): int {
        $cm = $this->get_cm_by_activity_name('data', $idnumber);

        return $cm->instance;
    }

    /**
     * Add an entry.
     *
     * @param array $data Entry data.
     */
    public function process_entry(array $data): void {
        global $DB;

        $database = $DB->get_record('data', ['id' => $data['databaseid']], '*', MUST_EXIST);

        unset($data['databaseid']);
        $userid = 0;
        if (array_key_exists('userid', $data)) {
            $userid = $data['userid'];
            unset($data['userid']);
        }

        $data = array_reduce(array_keys($data), function ($fields, $fieldname) use ($data, $database) {
            global $DB;

            $field = $DB->get_record('data_fields', ['name' => $fieldname, 'dataid' => $database->id], 'id', MUST_EXIST);

            $fields[$field->id] = $data[$fieldname];

            return $fields;
        }, []);

        $this->get_data_generator()->create_entry($database, $data, 0, [], null, $userid);
    }

    /**
     * Add a field.
     *
     * @param array $data Field data.
     */
    public function process_field(array $data): void {
        global $DB;

        $database = $DB->get_record('data', ['id' => $data['databaseid']], '*', MUST_EXIST);

        unset($data['databaseid']);

        $this->get_data_generator()->create_field((object) $data, $database);
    }

    /**
     * Add a template.
     *
     * @param array $data Template data.
     */
    public function process_template(array $data): void {
        global $DB;

        $database = $DB->get_record('data', ['id' => $data['databaseid']], '*', MUST_EXIST);

        if (empty($data['content'])) {
            data_generate_default_template($database, $data['name']);
        } else {
            $newdata = new stdClass();
            $newdata->id = $database->id;
            $newdata->{$data['name']} = $data['content'];
            $DB->update_record('data', $newdata);
        }
    }

    /**
     * Saves a preset.
     *
     * @param array $data Preset data.
     */
    protected function process_preset(array $data): void {
        global $DB;

        $instance = $DB->get_record('data', ['id' => $data['databaseid']], '*', MUST_EXIST);

        $this->get_data_generator()->create_preset($instance, (object) $data);
    }

    /**
     * Get the module data generator.
     *
     * @return mod_data_generator Database data generator.
     */
    protected function get_data_generator(): mod_data_generator {
        return $this->componentdatagenerator;
    }

}
