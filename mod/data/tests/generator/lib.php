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
 * Data generator class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_data\manager;
use mod_data\preset;

defined('MOODLE_INTERNAL') || die();


/**
 * Data generator class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_data_generator extends testing_module_generator {

    /**
     * @var int keep track of how many database fields have been created.
     */
    protected $databasefieldcount = 0;

    /**
     * @var int keep track of how many database records have been created.
     */
    protected $databaserecordcount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->databasefieldcount = 0;
        $this->databaserecordcount = 0;

        parent::reset();
    }

    /**
     * Creates a mod_data instance
     *
     * @param array $record
     * @param array $options
     * @return StdClass
     */
    public function create_instance($record = null, array $options = null) {
        // Note, the parent class does not type $record to cast to array and then to object.
        $record = (object) (array) $record;

        if (!isset($record->assessed)) {
            $record->assessed = 0;
        }
        if (!isset($record->scale)) {
            $record->scale = 0;
        }

        return parent::create_instance((array) $record, $options);
    }

    /**
     * Creates a field for a mod_data instance.
     *
     * @param StdClass $record
     * @param mod_data $data
     * @return data_field_{type}
     */
    public function create_field(stdClass $record = null, $data = null) {
        $record = (array) $record;

        $this->databasefieldcount++;

        if (!isset($data->course)) {
            throw new coding_exception('course must be present in phpunit_util::create_field() $data');
        }

        if (!isset($data->id)) {
            throw new coding_exception('dataid must be present in phpunit_util::create_field() $data');
        } else {
            $record['dataid'] = $data->id;
        }

        if (!isset($record['type'])) {
            throw new coding_exception('type must be present in phpunit_util::create_field() $record');
        }

        if (!isset($record['required'])) {
            $record['required'] = 0;
        }

        if (!isset($record['name'])) {
            $record['name'] = "testField - " . $this->databasefieldcount;
        }

        if (!isset($record['description'])) {
            $record['description'] = " This is testField - " . $this->databasefieldcount;
        }

        if (isset($record['param1']) && !empty($record['param1'])) {
            // Some fields have multiline entries.
            $record['param1'] = str_replace('\n', "\n", $record['param1']);
        }

        if (!isset($record['param1'])) {
            if ($record['type'] == 'checkbox') {
                $record['param1'] = implode("\n", array('opt1', 'opt2', 'opt3', 'opt4'));
            } else if ($record['type'] == 'radiobutton') {
                $record['param1'] = implode("\n", array('radioopt1', 'radioopt2', 'radioopt3', 'radioopt4'));
            } else if ($record['type'] == 'menu') {
                $record['param1'] = implode("\n", array('menu1', 'menu2', 'menu3', 'menu4'));
            } else if ($record['type'] == 'multimenu') {
                $record['param1'] = implode("\n", array('multimenu1', 'multimenu2', 'multimenu3', 'multimenu4'));
            } else if (($record['type'] === 'text') || ($record['type'] === 'url')) {
                $record['param1'] = 1;
            } else if ($record['type'] == 'latlong') {
                $record['param1'] = 'Google Maps';
            } else {
                $record['param1'] = '';
            }
        }

        if (!isset($record['param2'])) {

            if ($record['type'] === 'textarea') {
                $record['param2'] = 60;
            } else if ($record['type'] == 'latlong') {
                $record['param2'] = -1;
            } else {
                $record['param2'] = '';
            }
        }

        if (!isset($record['param3'])) {

            if (($record['type'] === 'textarea')) {
                $record['param3'] = 35;
            } else if ($record['type'] == 'picture' || $record['type'] == 'file') {
                $record['param3'] = 0;
            } else {
                $record['param3'] = '';
            }
        }

        if (!isset($record['param4'])) {

            if (($record['type'] === 'textarea')) {
                $record['param4'] = 1;
            }
        }

        if (!isset($record['param5'])) {
            if (($record['type'] === 'textarea')) {
                $record['param5'] = 0;
            }
        }

        $record = (object) $record;

        $field = data_get_field($record, $data);
        $field->insert_field();

        return $field;
    }

    /**
     * Creates a field for a mod_data instance.
     * Keep in mind the default data field params created in create_field() function!
     * ...if you haven't provided your own custom data field parameters there.
     * The developers using the generator must adhere to the following format :
     *
     *   Syntax : $contents[ fieldid ] = fieldvalue
     *   $contents['checkbox'] = array('val1', 'val2', 'val3' .....)
     *   $contents['data'] = 'dd-mm-yyyy'
     *   $contents['menu'] = 'value';
     *   $contents['multimenu'] =  array('val1', 'val2', 'val3' .....)
     *   $contents['number'] = 'numeric value'
     *   $contents['radiobuton'] = 'value'
     *   $contents['text'] = 'text'
     *   $contents['textarea'] = 'text'
     *   $contents['url'] = 'example.url' or array('example.url', 'urlname')
     *   $contents['latlong'] = array('value for lattitude', 'value for longitude')
     *   $contents['file'] = 'filename or draftitemid'
     *   $contents['picture'] = array('filename or draftitemid', 'alternative text')
     *
     * @param stdClass $data record from table {data}
     * @param array $contents
     * @param int $groupid
     * @param array $tags
     * @param array $options
     * @param int $userid if defined, it will be the author of the entry
     * @return int id of the generated record in table {data_records}
     */
    public function create_entry($data, array $contents, $groupid = 0, $tags = [], array $options = null, int $userid = 0) {
        global $DB, $USER, $CFG;

        // Set current user if defined.
        if (!empty($userid)) {
            $currentuser = $USER;
            $user = \core_user::get_user($userid);
            $this->set_user($user);
        }

        $this->databaserecordcount++;

        $recordid = data_add_record($data, $groupid);

        if (isset($options['approved'])) {
            data_approve_entry($recordid, !empty($options['approved']));
        } else {
            $approved = null;
        }

        $fields = $DB->get_records('data_fields', array('dataid' => $data->id));

        // Validating whether required field are filled.
        foreach ($fields as $field) {
            $fieldhascontent = true;

            $field = data_get_field($field, $data);

            $fieldid = $field->field->id;

            if ($field->type === 'date') {
                $values = array();

                $temp = explode('-', $contents[$fieldid], 3);

                $values['field_' . $fieldid . '_day'] = (int)trim($temp[0]);
                $values['field_' . $fieldid . '_month'] = (int)trim($temp[1]);
                $values['field_' . $fieldid . '_year'] = (int)trim($temp[2]);

                // Year should be less than 2038, so it can be handled by 32 bit windows.
                if ($values['field_' . $fieldid . '_year'] > 2038) {
                    throw new coding_exception('DateTime::getTimestamp resturns false on 32 bit win for year beyond ' .
                        '2038. Please use year less than 2038.');
                }

                $contents[$fieldid] = $values;

                foreach ($values as $fieldname => $value) {
                    if (!$field->notemptyfield($value, $fieldname)) {
                        $fieldhascontent = false;
                    }
                }
            } else if ($field->type === 'textarea') {
                $values = array();

                $values['field_' . $fieldid] = $contents[$fieldid];
                $values['field_' . $fieldid . '_content1'] = 1;

                $contents[$fieldid] = $values;

                $fieldname = 'field_' . $fieldid;
                if (!$field->notemptyfield($values[$fieldname], $fieldname)) {
                    $fieldhascontent = false;
                }

            } else if ($field->type === 'url') {
                $values = array();

                if (is_array($contents[$fieldid])) {
                    foreach ($contents[$fieldid] as $key => $value) {
                        $values['field_' . $fieldid . '_' . $key] = $value;
                    }
                } else {
                    $values['field_' . $fieldid . '_0'] = $contents[$fieldid];
                }

                $contents[$fieldid] = $values;
                $fieldname = 'field_' . $fieldid . '_0';
                if (!$field->notemptyfield($values[$fieldname], $fieldname)) {
                    $fieldhascontent = false;
                }

            } else if ($field->type === 'latlong') {
                $values = array();

                foreach ($contents[$fieldid] as $key => $value) {
                    $values['field_' . $fieldid . '_' . $key] = $value;
                }

                $contents[$fieldid] = $values;
                $fieldname = 'field_' . $fieldid . '_0';
                if (!$field->notemptyfield($values[$fieldname], $fieldname)) {
                    $fieldhascontent = false;
                }

            } else if ($field->type === 'file' || $field->type === 'picture') {
                if (is_array($contents[$fieldid])) {
                    list($itemid, $alttext) = $contents[$fieldid];
                } else {
                    $itemid = $contents[$fieldid];
                    $alttext = '';
                }

                if (strlen($itemid) && !is_numeric($itemid)) {
                    // We expect draftarea item id here but it can also be a filename, in this case provider will generate file.
                    $filename = $itemid;
                    $usercontext = context_user::instance($USER->id);
                    $itemid = file_get_unused_draft_itemid();
                    get_file_storage()->create_file_from_string(['component' => 'user', 'filearea' => 'draft',
                        'contextid' => $usercontext->id, 'itemid' => $itemid, 'filepath' => '/',
                        'filename' => $filename],
                        file_get_contents($CFG->dirroot.'/mod/data/pix/monologo.png'));
                }

                $fieldname = 'field_' . $fieldid . '_file';
                if ($field->type === 'file') {
                    $contents[$fieldid] = $itemid;
                } else {
                    $contents[$fieldid] = [
                        $fieldname => $itemid,
                        'field_' . $fieldid . '_alttext' => $alttext
                    ];
                }

                if (!$field->notemptyfield($itemid, $fieldname)) {
                    $fieldhascontent = false;
                }

            } else {
                if ($field->notemptyfield($contents[$fieldid], 'field_' . $fieldid . '_0')) {
                    continue;
                }
            }

            if ($field->field->required && !$fieldhascontent) {
                return false;
            }
        }

        foreach ($contents as $fieldid => $content) {
            $field = data_get_field_from_id($fieldid, $data);

            if (is_array($content) and in_array($field->type, array('date', 'textarea', 'url', 'picture', 'latlong'))) {

                foreach ($content as $fieldname => $value) {
                    $field->update_content($recordid, $value, $fieldname);
                }

            } else {
                $field->update_content($recordid, $content);
            }
        }

        if (!empty($tags)) {
            $cm = get_coursemodule_from_instance('data', $data->id);
            core_tag_tag::set_item_tags('mod_data', 'data_records', $recordid,
                context_module::instance($cm->id), $tags);
        }

        if (isset($currentuser)) {
            $this->set_user($currentuser);
        }

        return $recordid;
    }

    /**
     * Creates a preset from a mod_data instance.
     *
     * @param stdClass $instance The mod_data instance.
     * @param stdClass|null $record The preset information, like 'name'.
     * @return preset The preset that has been created.
     */
    public function create_preset(stdClass $instance, stdClass $record = null): preset {
        global $USER;

        if (is_null($record)) {
            $record = new stdClass();
        }

        // Set current user if defined.
        if (isset($record->userid) && $record->userid != $USER->id) {
            $currentuser = $USER;
            $user = \core_user::get_user($record->userid);
            $this->set_user($user);
        }

        // Fill in optional values if not specified.
        $presetname = 'New preset ' . microtime();
        if (isset($record->name)) {
            $presetname = $record->name;
        }
        $presetdescription = null;
        if (isset($record->description)) {
            $presetdescription = $record->description;
        }

        $manager = manager::create_from_instance($instance);
        $preset = preset::create_from_instance($manager, $presetname, $presetdescription);
        $preset->save();

        if (isset($currentuser)) {
            $this->set_user($currentuser);
        }

        return $preset;
    }
}
