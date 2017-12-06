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
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Field manager class
 */
class mod_dataform_field_manager {

    /** @var int The id of the Dataform this manager works for. */
    protected $_dataformid;
    /** @var array The list of records of managed items. */
    protected $_items;

    /**
     * Returns and caches (for the current script) if not already, a fields manager for the specified Dataform.
     *
     * @param int Dataform id
     * @return mod_dataform_field_manager
     */
    public static function instance($dataformid) {
        if (!$instance = \mod_dataform_instance_store::instance($dataformid, 'field_manager')) {
            $instance = new mod_dataform_field_manager($dataformid);
            \mod_dataform_instance_store::register($dataformid, 'field_manager', $instance);
        }

        return $instance;
    }

    /**
     * constructor
     */
    public function __construct($dataformid) {
        $this->_dataformid = $dataformid;
        $this->_items = array();
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        return null;
    }

    /**
     * Initialize if needed and return the internal field types.
     */
    public static function get_internal_field_types() {
        static $types;
        if (!isset($types)) {
            $types = array();
            foreach (array_keys(core_component::get_plugin_list('dataformfield')) as $subpluginname) {
                $fieldclass = "dataformfield_{$subpluginname}_$subpluginname";
                if (is_subclass_of($fieldclass, '\mod_dataform\pluginbase\dataformfield_internal')) {
                    $types[$fieldclass::INTERNALID] = $subpluginname;
                }
            }
        }
        return $types;
    }

    /**
     * Initialize if needed and return the internal field names.
     */
    public static function get_internal_fields($dataformid) {
        static $fieldw;
        if (!isset($fields)) {
            $fields = array();
            foreach (array_keys(core_component::get_plugin_list('dataformfield')) as $subpluginname) {
                $fieldclass = "dataformfield_{$subpluginname}_$subpluginname";
                if (is_subclass_of($fieldclass, '\mod_dataform\pluginbase\dataformfield_internal')) {
                    $fielddata = $fieldclass::get_default_data($dataformid);
                    $fields[$fieldclass::INTERNALID] = $fielddata;
                }
            }
        }
        return $fields;
    }

    /**
     *
     */
    protected function get_field_records($forceget = false, $options = null, $sort = '') {
        global $DB, $CFG;

        if (empty($this->_items) or $forceget) {
            $this->_items = array();
            $params = array('dataid' => $this->_dataformid);
            if (!empty($options['type'])) {
                $params['type'] = $options['type'];
            }
            if ($fields = $DB->get_records('dataform_fields', $params, $sort)) {
                $this->_items = $fields;
            }
            // Add internal fields without DB record.
            foreach (self::get_internal_fields($this->_dataformid) as $fieldid => $field) {
                $this->_items[$fieldid] = $field;
            }
        }
        return $this->_items;
    }

    /**
     * Returns true if max fields as set by admin has been reached.
     *
     * @return bool
     */
    public function is_at_max_fields() {
        global $DB, $CFG;

        if ($CFG->dataform_maxfields) {
            if ($count = $DB->count_records('dataform_fields', array('dataid' => $this->_dataformid))) {
                if ($count >= $CFG->dataform_maxfields) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Adds a field of the given type with default settings.
     *
     * @param string $type Field type
     * @return dataformfield_type_type field object
     */
    public function add_field($type) {
        if ($field = $this->get_field($type)) {
            $field->create($field->data);
            $this->_items[$field->id] = $field->data;
        }
        return $field;
    }

    /**
     * Given a field id returns the field object or false if not found.
     *
     * @return dataformfield|bool Field object or false if not found.
     */
    public function get_field_by_id($fieldid, $forceget = false) {
        global $DB, $CFG;

        if (!empty($this->_items[$fieldid]) and !$forceget) {
            return $this->get_field($this->_items[$fieldid]);
        }
        if (empty($this->_items[$fieldid]) or $forceget) {
            $internalfieldtypes = self::get_internal_field_types();
            if (!empty($internalfieldtypes[$fieldid])) {
                $fieldtype = $internalfieldtypes[$fieldid];
                $fieldclass = "dataformfield_{$fieldtype}_$fieldtype";
                $field = $fieldclass::get_default_data($this->_dataformid);
            } else if ($fieldid > 0) {
                // Try from DB.
                $field = $DB->get_record('dataform_fields', array('id' => $fieldid));
            } else {
                return false;
            }

            $this->_items[$fieldid] = $field;
            return $this->get_field($field);
        }

        return false;
    }

    /**
     * Given an array of field ids return the field objects
     *
     */
    public function get_fields_by_id(array $fieldids, $forceget = false) {
        $fields = array();
        foreach ($fieldids as $fieldid) {
            if ($field = $this->get_field_by_id($fieldid, $forceget)) {
                $fields[$fieldid] = $field;
            }
        }
        return $fields;
    }

    /**
     * Given a field name return the field object
     *
     */
    public function get_field_by_name($fieldname, $forceget = false) {
        global $DB;

        // Try first internal field.
        foreach (self::get_internal_fields($this->_dataformid) as $field) {
            if ($fieldname == $field->name) {
                return $this->get_field($field);
            }
        }

        if (!$forceget) {
            foreach ($this->_items as $field) {
                if ($field->name == $fieldname) {
                    return $this->get_field($field);
                }
            }
        }
        // Either no field or forceget so get the field from DB.
        if ($field = $DB->get_record('dataform_fields', array('dataid' => $this->_dataformid, 'name' => $fieldname))) {
            $this->_items[$field->id] = $field;
            return $this->get_field($field);
        }

        return false;
    }

    /**
     * Given a field pattern, returns the field object.
     * Applicable to user fields only ([[...]]).
     *
     * @param string $fieldpattern
     * @param bool $forceget
     * @return bool|dataformfield The dataformfield or false if not found.
     */
    public function get_field_by_pattern($fieldpattern, $forceget = false) {
        if (strpos($fieldpattern, '[[') === 0) {
            list($fieldname, ) = explode(':', trim($fieldpattern, '[]')) + array(null);
        }

        if (!empty($fieldname)) {
            return $this->get_field_by_name($fieldname, $forceget);
        }
        return false;
    }

    /**
     * given a field type returns the field object from get_fields
     * Initializes get_fields if necessary
     */
    public function get_fields_by_type($type, $forceget = false, $menu = false) {
        if (!$fields = $this->get_field_records($forceget, array('type' => $type))) {
            return false;
        }

        $typefields = array();
        foreach ($fields as $fieldid => $field) {
            if ($menu) {
                $typefields[$fieldid] = $field->name;
            } else {
                $typefields[$fieldid] = $this->get_field($field);
            }
        }
        return $typefields;
    }

    /**
     * returns a subclass field object given a record of the field
     * used to invoke plugin methods
     * input: $param $field record from db, or field type
     */
    public function get_field($objortype) {
        if ($objortype) {
            if (is_object($objortype)) {
                $type = $objortype->type;
                $obj = $objortype;
            } else {
                $type = $objortype;
                $obj = new stdClass;
                $obj->type = $type;
                $obj->dataid = $this->_dataformid;
            }
            $fieldclass = "dataformfield_{$type}_$type";
            $field = new $fieldclass($obj);
            return $field;
        } else {
            return false;
        }
    }

    /**
     *
     */
    public function get_fields(array $options = null) {
        $forceget = !empty($options['forceget']) ? $options['forceget'] : null;
        $sort = !empty($options['sort']) ? $options['sort'] : '';

        $this->get_field_records($forceget, null, $sort);

        $fields = array();
        $exclude = !empty($options['exclude']) ? $options['exclude'] : null;
        foreach ($this->_items as $fieldid => $field) {
            if (!empty($exclude) and in_array($fieldid, $exclude)) {
                continue;
            }
            $fields[$fieldid] = $this->get_field($field);
        }
        return $fields;
    }

    /**
     *
     */
    public function get_fields_menu(array $options = null) {
        $sort = !empty($options['sort']) ? $options['sort'] : null;
        $forceget = !empty($options['forceget']) ? $options['forceget'] : null;

        $this->get_field_records($forceget, null, $sort);
        // $this->get_field_records_internal();.
        if (!$fieldrecs = $this->_items) {
            return array();
        }

        $fields = array();
        $exclude = !empty($options['exclude']) ? $options['exclude'] : null;
        foreach ($fieldrecs as $fieldid => $field) {
            if (!empty($exclude) and in_array($fieldid, $exclude)) {
                continue;
            }
            $fields[$fieldid] = $field->name;
        }

        return $fields;
    }

    /**
     * Processes field crud requests and returns a list of processed field ids.
     *
     * @param string $action
     * @param string|array $fids Field ids to process
     * @param bool $confirmed
     * @return bool/array
     * @throws \required_capability_exception on mod/dataform:manageviews
     */
    public function process_fields($action, $fids, $confirmed = false) {
        global $OUTPUT, $DB;

        $df = mod_dataform_dataform::instance($this->_dataformid);
        require_capability('mod/dataform:managefields', $df->context);

        // Get array of ids.
        if (!is_array($fids)) {
            $fids = explode(',', $fids);
        }

        $dffields = $this->get_fields();
        $fields = array();
        // Collate the fields for processing.
        foreach ($fids as $fieldid) {
            if ($fieldid > 0 and isset($dffields[$fieldid])) {
                $fields[$fieldid] = $dffields[$fieldid];
            }
        }

        $processedfids = array();
        $strnotify = '';

        if (empty($fields) and $action != 'add') {
            $df->notifications = array('problem' => array('fieldnoneforaction' => get_string('fieldnoneforaction', 'dataform')));
            return false;
        } else {
            if (!$confirmed) {
                $output = $df->get_renderer();
                echo $output->header('fields');

                // Print a confirmation page.
                echo $output->confirm(get_string("fieldsconfirm$action", 'dataform', count($fields)),
                        new moodle_url('/mod/dataform/field/index.php', array('d' => $this->_dataformid,
                                                                        $action => implode(',', array_keys($fields)),
                                                                        'sesskey' => sesskey(),
                                                                        'confirmed' => 1)),
                        new moodle_url('/mod/dataform/field/index.php', array('d' => $this->_dataformid)));

                echo $output->footer();
                exit;

            } else {
                // Go ahead and perform the requested action.
                switch ($action) {
                    case 'visible':
                        foreach ($fields as $fid => $field) {
                            // Hide = 0; (show to owner) = 1; show to everyone = 2.
                            $field->visible = (($field->visible + 1) % 3);
                            $field->update($field->data);

                            $processedfids[] = $fid;
                        }

                        $strnotify = '';
                        break;

                    case 'editable':
                        foreach ($fields as $fid => $field) {
                            // Lock = 0; unlock = -1;.
                            $field->editable = $field->editable ? 0 : -1;
                            $field->update($field->data);

                            $processedfids[] = $fid;
                        }

                        $strnotify = '';
                        break;

                    case 'duplicate':
                        foreach ($fields as $field) {
                            if ($this->is_at_max_fields()) {
                                break;
                            }
                            // Set new name.
                            while ($df->name_exists('fields', $field->name)) {
                                $field->name .= '_1';
                            }
                            $field->create($field->data);
                            $processedfids[] = $field->id;
                        }
                        $strnotify = 'fieldsadded';
                        break;

                    case 'delete':
                        foreach ($fields as $fieldid => $field) {
                            $deletepatterns = $field->renderer->get_patterns();
                            $field->delete();
                            unset($this->_items[$fieldid]);
                            $processedfids[] = $fieldid;
                            // Update views.
                            $df->view_manager->replace_patterns_in_views($deletepatterns, '');
                        }
                        $strnotify = 'fieldsdeleted';
                        break;

                    default:
                        break;
                }

                if ($strnotify) {
                    $fieldsprocessed = $processedfids ? count($processedfids) : 'No';
                    $df->notifications = array('success' => array('' => get_string($strnotify, 'dataform', $fieldsprocessed)));
                }
                return $processedfids;
            }
        }
    }

    /**
     *
     */
    public function delete_fields() {
        if ($fields = $this->get_fields_menu()) {
            $fieldids = array_keys($fields);
            $this->process_fields('delete', $fieldids, true);
        }
    }

    /**
     * Generates field patterns menu for group selector.
     * Used in view template forms for adding field patterns to template.
     *
     * @return array Associative array of associative arrays
     */
    public function get_field_patterns_menu() {
        $patterns = array();
        if ($fields = $this->get_fields()) {
            foreach ($fields as $field) {
                if ($fieldpatterns = $field->renderer->get_menu()) {
                    $patterns = array_merge_recursive($patterns, $fieldpatterns);
                }
            }
        }
        return $patterns;
    }

}
