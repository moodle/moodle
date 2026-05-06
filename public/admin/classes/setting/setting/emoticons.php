<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\setting\setting;

/**
 * Administration interface for emoticon_manager settings.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class emoticons extends \core_admin\setting {
    /**
     * Calls parent::__construct with specific args
     */
    public function __construct() {
        global $CFG;

        $manager = get_emoticon_manager();
        $defaults = $this->prepare_form_data($manager->default_emoticons());
        parent::__construct('emoticons', get_string('emoticons', 'admin'), get_string('emoticons_desc', 'admin'), $defaults);
    }

    /**
     * Return the current setting(s)
     *
     * @return ?array Current settings array
     */
    public function get_setting() {
        global $CFG;

        $manager = get_emoticon_manager();

        $config = $this->config_read($this->name);
        if (is_null($config)) {
            return null;
        }

        $config = $manager->decode_stored_config($config);
        if (is_null($config)) {
            return null;
        }

        return $this->prepare_form_data($config);
    }

    /**
     * Save selected settings
     *
     * @param array $data Array of settings to save
     * @return string error message or empty string on success
     */
    public function write_setting($data) {

        $manager = get_emoticon_manager();
        $emoticons = $this->process_form_data($data);

        if ($emoticons === false) {
            return false;
        }

        if ($this->config_write($this->name, $manager->encode_stored_config($emoticons))) {
            return ''; // Success.
        } else {
            return get_string('errorsetting', 'admin') . $this->visiblename . \html_writer::empty_tag('br');
        }
    }

    #[\Override]
    public function output_html($data, $query = '') {
        global $OUTPUT;

        $context = (object) [
            'name' => $this->get_full_name(),
            'emoticons' => [],
            'forceltr' => true,
        ];

        $i = 0;
        foreach ($data as $field => $value) {
            // When $i == 0: text.
            // When $i == 1: imagename.
            // When $i == 2: imagecomponent.
            // When $i == 3: altidentifier.
            // When $i == 4: altcomponent.
            $fields[$i] = (object) [
                'field' => $field,
                'value' => $value,
                'index' => $i,
            ];
            $i++;

            if ($i > 4) {
                $icon = null;
                if (!empty($fields[1]->value)) {
                    if (get_string_manager()->string_exists($fields[3]->value, $fields[4]->value)) {
                        $alt = get_string($fields[3]->value, $fields[4]->value);
                    } else {
                        $alt = $fields[0]->value;
                    }
                    $icon = new \pix_emoticon($fields[1]->value, $alt, $fields[2]->value);
                }
                $context->emoticons[] = [
                    'fields' => $fields,
                    'icon' => $icon ? $icon->export_for_template($OUTPUT) : null,
                ];
                $fields = [];
                $i = 0;
            }
        }

        $context->reseturl = new \moodle_url('/admin/resetemoticons.php');
        $element = $OUTPUT->render_from_template('core_admin/setting_emoticons', $context);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', null, $query);
    }

    /**
     * Converts the array of emoticon objects provided by {@see emoticon_manager} into admin settings form data
     *
     * @see self::process_form_data()
     * @param array $emoticons array of emoticon objects as returned by {@see emoticon_manager}
     * @return array of form fields and their values
     */
    protected function prepare_form_data(array $emoticons) {

        $form = [];
        $i = 0;
        foreach ($emoticons as $emoticon) {
            $form['text' . $i]            = $emoticon->text;
            $form['imagename' . $i]       = $emoticon->imagename;
            $form['imagecomponent' . $i]  = $emoticon->imagecomponent;
            $form['altidentifier' . $i]   = $emoticon->altidentifier;
            $form['altcomponent' . $i]    = $emoticon->altcomponent;
            $i++;
        }
        // Add one more blank field set for new object.
        $form['text' . $i]            = '';
        $form['imagename' . $i]       = '';
        $form['imagecomponent' . $i]  = '';
        $form['altidentifier' . $i]   = '';
        $form['altcomponent' . $i]    = '';

        return $form;
    }

    /**
     * Converts the data from admin settings form into an array of emoticon objects
     *
     * @see self::prepare_form_data()
     * @param array $form array of admin form fields and values
     * @return false|array of emoticon objects
     */
    protected function process_form_data(array $form) {

        $count = count($form); // Number of form field values.

        if ($count % 5) {
            // We must get five fields per emoticon object.
            return false;
        }

        $emoticons = [];
        for ($i = 0; $i < $count / 5; $i++) {
            $emoticon                   = new \stdClass();
            $emoticon->text             = clean_param(trim($form['text' . $i]), PARAM_NOTAGS);
            $emoticon->imagename        = clean_param(trim($form['imagename' . $i]), PARAM_PATH);
            $emoticon->imagecomponent   = clean_param(trim($form['imagecomponent' . $i]), PARAM_COMPONENT);
            $emoticon->altidentifier    = clean_param(trim($form['altidentifier' . $i]), PARAM_STRINGID);
            $emoticon->altcomponent     = clean_param(trim($form['altcomponent' . $i]), PARAM_COMPONENT);

            if (strpos($emoticon->text, ':/') !== false || strpos($emoticon->text, '//') !== false) {
                // Prevent from breaking http://url.addresses by accident.
                $emoticon->text = '';
            }

            if (strlen($emoticon->text) < 2) {
                // Do not allow single character emoticons.
                $emoticon->text = '';
            }

            if (preg_match('/^[a-zA-Z]+[a-zA-Z0-9]*$/', $emoticon->text)) {
                // Emoticon text must contain some non-alphanumeric character to prevent
                // Breaking HTML tags.
                $emoticon->text = '';
            }

            if ($emoticon->text !== '' && $emoticon->imagename !== '' && $emoticon->imagecomponent !== '') {
                $emoticons[] = $emoticon;
            }
        }
        return $emoticons;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(emoticons::class, \admin_setting_emoticons::class);
