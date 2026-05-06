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
 * Administration interface for emoticon_manager settings.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class emoticons extends \admin_setting {

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
            return ''; // success
        } else {
            return get_string('errorsetting', 'admin') . $this->visiblename . \html_writer::empty_tag('br');
        }
    }

    /**
     * Return XHTML field(s) for options
     *
     * @param array $data Array of options to set in HTML
     * @return string XHTML string for the fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
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
                'index' => $i
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
                    'icon' => $icon ? $icon->export_for_template($OUTPUT) : null
                ];
                $fields = [];
                $i = 0;
            }
        }

        $context->reseturl = new \moodle_url('/admin/resetemoticons.php');
        $element = $OUTPUT->render_from_template('core_admin/setting_emoticons', $context);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, false, '', NULL, $query);
    }

    /**
     * Converts the array of emoticon objects provided by {@see emoticon_manager} into admin settings form data
     *
     * @see self::process_form_data()
     * @param array $emoticons array of emoticon objects as returned by {@see emoticon_manager}
     * @return array of form fields and their values
     */
    protected function prepare_form_data(array $emoticons) {

        $form = array();
        $i = 0;
        foreach ($emoticons as $emoticon) {
            $form['text'.$i]            = $emoticon->text;
            $form['imagename'.$i]       = $emoticon->imagename;
            $form['imagecomponent'.$i]  = $emoticon->imagecomponent;
            $form['altidentifier'.$i]   = $emoticon->altidentifier;
            $form['altcomponent'.$i]    = $emoticon->altcomponent;
            $i++;
        }
        // add one more blank field set for new object
        $form['text'.$i]            = '';
        $form['imagename'.$i]       = '';
        $form['imagecomponent'.$i]  = '';
        $form['altidentifier'.$i]   = '';
        $form['altcomponent'.$i]    = '';

        return $form;
    }

    /**
     * Converts the data from admin settings form into an array of emoticon objects
     *
     * @see self::prepare_form_data()
     * @param array $data array of admin form fields and values
     * @return false|array of emoticon objects
     */
    protected function process_form_data(array $form) {

        $count = count($form); // number of form field values

        if ($count % 5) {
            // we must get five fields per emoticon object
            return false;
        }

        $emoticons = array();
        for ($i = 0; $i < $count / 5; $i++) {
            $emoticon                   = new \stdClass();
            $emoticon->text             = clean_param(trim($form['text'.$i]), PARAM_NOTAGS);
            $emoticon->imagename        = clean_param(trim($form['imagename'.$i]), PARAM_PATH);
            $emoticon->imagecomponent   = clean_param(trim($form['imagecomponent'.$i]), PARAM_COMPONENT);
            $emoticon->altidentifier    = clean_param(trim($form['altidentifier'.$i]), PARAM_STRINGID);
            $emoticon->altcomponent     = clean_param(trim($form['altcomponent'.$i]), PARAM_COMPONENT);

            if (strpos($emoticon->text, ':/') !== false or strpos($emoticon->text, '//') !== false) {
                // prevent from breaking http://url.addresses by accident
                $emoticon->text = '';
            }

            if (strlen($emoticon->text) < 2) {
                // do not allow single character emoticons
                $emoticon->text = '';
            }

            if (preg_match('/^[a-zA-Z]+[a-zA-Z0-9]*$/', $emoticon->text)) {
                // emoticon text must contain some non-alphanumeric character to prevent
                // breaking HTML tags
                $emoticon->text = '';
            }

            if ($emoticon->text !== '' and $emoticon->imagename !== '' and $emoticon->imagecomponent !== '') {
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
