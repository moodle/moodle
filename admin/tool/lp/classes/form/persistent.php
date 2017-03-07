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
 * Persistent form abstract.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp\form;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use moodleform;
use stdClass;

require_once($CFG->libdir.'/formslib.php');

/**
 * Persistent form abstract class.
 *
 * This provides some shortcuts to validate objects based on the persistent model.
 *
 * Note that all mandatory fields (non-optional) of your model should be included in the
 * form definition. Mandatory fields which are not editable by the user should be
 * as hidden and constant.
 *
 *    $mform->addElement('hidden', 'userid');
 *    $mform->setType('userid', PARAM_INT);
 *    $mform->setConstant('userid', $this->_customdata['userid']);
 *
 * You may exclude some fields from the validation should your form include other
 * properties such as files. To do so use the $foreignfields property.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class persistent extends moodleform {

    /** @var string The fully qualified classname. */
    protected static $persistentclass = null;

    /** @var array Fields to remove when getting the final data. */
    protected static $fieldstoremove = array('submitbutton');

    /** @var array Fields to remove from the persistent validation. */
    protected static $foreignfields = array();

    /** @var \tool_lp\peristent Reference to the persistent. */
    private $persistent = null;

    /**
     * Constructor.
     *
     * The 'persistent' has to be passed as custom data when 'editing'.
     *
     * Note that in order for your persistent to be reloaded after form submission you should
     * either override the URL to include the ID to your resource, or add the ID to the form
     * fields.
     *
     * @param mixed $action
     * @param mixed $customdata
     * @param string $method
     * @param string $target
     * @param mixed $attributes
     * @param bool $editable
     */
    public function __construct($action = null, $customdata = null, $method = 'post', $target = '',
                                $attributes = null, $editable = true) {
        if (empty(static::$persistentclass)) {
            throw new coding_exception('Static property $persistentclass must be set.');
        } else if (!is_subclass_of(static::$persistentclass, 'core_competency\\persistent')) {
            throw new coding_exception('Static property $persistentclass is not valid.');
        } else if (!array_key_exists('persistent', $customdata)) {
            throw new coding_exception('The custom data \'persistent\' key must be set, even if it is null.');
        }

        // Make a copy of the persistent passed, this ensures validation and object reference issues.
        $persistendata = new stdClass();
        $persistent = isset($customdata['persistent']) ? $customdata['persistent'] : null;
        if ($persistent) {
            if (!($persistent instanceof static::$persistentclass)) {
                throw new coding_exception('Invalid persistent');
            }
            $persistendata = $persistent->to_record();
            unset($persistent);
        }

        $this->persistent = new static::$persistentclass();
        $this->persistent->from_record($persistendata);

        unset($customdata['persistent']);
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);

        // Load the defaults.
        $this->set_data($this->get_default_data());
    }

    /**
     * Convert some fields.
     *
     * @param  stdClass $data The whole data set.
     * @return stdClass The amended data set.
     */
    protected static function convert_fields(stdClass $data) {
        $class = static::$persistentclass;
        $properties = $class::get_formatted_properties();

        foreach ($data as $field => $value) {
            // Replace formatted properties.
            if (isset($properties[$field])) {
                $formatfield = $properties[$field];
                $data->$formatfield = $data->{$field}['format'];
                $data->$field = $data->{$field}['text'];
            }
        }

        return $data;
    }

    /**
     * Define extra validation mechanims.
     *
     * The data here:
     * - does not include {@link self::$fieldstoremove}.
     * - does include {@link self::$foreignfields}.
     * - was converted to map persistent-like data, e.g. array $description to string $description + int $descriptionformat.
     *
     * You can modify the $errors parameter in order to remove some validation errors should you
     * need to. However, the best practice is to return new or overriden errors. Only modify the
     * errors passed by reference when you have no other option.
     *
     * Do not add any logic here, it is only intended to be used by child classes.
     *
     * @param  stdClass $data Data to validate.
     * @param  array $files Array of files.
     * @param  array $errors Currently reported errors.
     * @return array of additional errors, or overridden errors.
     */
    protected function extra_validation($data, $files, array &$errors) {
        return array();
    }

    /**
     * Filter out the foreign fields of the persistent.
     *
     * This can be overridden to filter out more complex fields.
     *
     * @param stdClass $data The data to filter the fields out of.
     * @return stdClass.
     */
    protected function filter_data_for_persistent($data) {
        return (object) array_diff_key((array) $data, array_flip((array) static::$foreignfields));
    }

    /**
     * Get the default data.
     *
     * This is the data that is prepopulated in the form at it loads, we automatically
     * fetch all the properties of the persistent however some needs to be converted
     * to map the form structure.
     *
     * Extend this class if you need to add more conversion.
     *
     * @return stdClass
     */
    protected function get_default_data() {
        $data = $this->get_persistent()->to_record();
        $class = static::$persistentclass;
        $properties = $class::get_formatted_properties();
        $allproperties = $class::properties_definition();

        foreach ($data as $field => $value) {
            // Clean data if it is to be displayed in a form.
            if (isset($allproperties[$field]['type'])) {
                $data->$field = clean_param($data->$field, $allproperties[$field]['type']);
            }

            // Convert formatted properties.
            if (isset($properties[$field])) {
                $data->$field = array(
                    'text' => $data->$field,
                    'format' => $data->{$properties[$field]}
                );
                unset($data->{$properties[$field]});
            }
        }

        return $data;
    }

    /**
     * Get form data.
     *
     * Conveniently removes non-desired properties and add the ID property.
     *
     * @return object|null
     */
    public function get_data() {
        $data = parent::get_data();
        if (is_object($data)) {
            foreach (static::$fieldstoremove as $field) {
                unset($data->{$field});
            }
            $data = static::convert_fields($data);

            // Ensure that the ID is set.
            $data->id = $this->persistent->get_id();
        }
        return $data;
    }

    /**
     * Return the persistent object associated with this form instance.
     *
     * @return tool_lp\persistent
     */
    final protected function get_persistent() {
        return $this->persistent;
    }

    /**
     * Get the submitted form data.
     *
     * Conveniently removes non-desired properties.
     *
     * @return object|null
     */
    public function get_submitted_data() {
        $data = parent::get_submitted_data();
        if (is_object($data)) {
            foreach (static::$fieldstoremove as $field) {
                unset($data->{$field});
            }
            $data = static::convert_fields($data);
        }
        return $data;
    }

    /**
     * Form validation.
     *
     * If you need extra validation, use {@link self::extra_validation()}.
     *
     * @param  array $data
     * @param  array $files
     * @return array
     */
    public final function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $data = $this->get_submitted_data();

        // Only validate compatible fields.
        $persistentdata = $this->filter_data_for_persistent($data);
        $persistent = $this->get_persistent();
        $persistent->from_record((object) $persistentdata);
        $errors = array_merge($errors, $persistent->get_errors());

        // Apply extra validation.
        $extraerrors = $this->extra_validation($data, $files, $errors);
        $errors = array_merge($errors, (array) $extraerrors);

        return $errors;
    }
}
