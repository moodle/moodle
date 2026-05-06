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
 * Administration setting to define a list of file types.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filetypes extends \core_admin\setting\setting\configtext {
    /** @var array Allow selection from these file types only. */
    protected $onlytypes = [];

    /** @var bool Allow selection of 'All file types' (will be stored as '*'). */
    protected $allowall = true;

    /** @var core_form\filetypes_util instance to use as a helper. */
    protected $util = null;

    /**
     * Constructor.
     *
     * @param string $name Unique ascii name like 'mycoresetting' or 'myplugin/mysetting'
     * @param string $visiblename Localised label of the setting
     * @param string $description Localised description of the setting
     * @param string $defaultsetting Default setting value.
     * @param array $options Setting widget options, an array with optional keys:
     *   'onlytypes' => array Allow selection from these file types only; for example ['onlytypes' => ['web_image']].
     *   'allowall' => bool Allow to select 'All file types', defaults to true. Does not apply if onlytypes are set.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting = '', array $options = []) {

        parent::__construct($name, $visiblename, $description, $defaultsetting, PARAM_RAW);

        if (array_key_exists('onlytypes', $options) && is_array($options['onlytypes'])) {
            $this->onlytypes = $options['onlytypes'];
        }

        if (!$this->onlytypes && array_key_exists('allowall', $options)) {
            $this->allowall = (bool)$options['allowall'];
        }

        $this->util = new \core_form\filetypes_util();
    }

    /**
     * Normalize the user's input and write it to the database as comma separated list.
     *
     * Comma separated list as a text representation of the array was chosen to
     * make this compatible with how the $CFG->courseoverviewfilesext values are stored.
     *
     * @param string $data Value submitted by the admin.
     * @return string Epty string if all good, error message otherwise.
     */
    public function write_setting($data) {
        return parent::write_setting(implode(',', $this->util->normalize_file_types($data)));
    }

    /**
     * Validate data before storage
     *
     * @param string $data The setting values provided by the admin
     * @return bool|string True if ok, the string if error found
     */
    public function validate($data) {
        $parentcheck = parent::validate($data);
        if ($parentcheck !== true) {
            return $parentcheck;
        }

        // Check for unknown file types.
        if ($unknown = $this->util->get_unknown_file_types($data)) {
            return get_string('filetypesunknown', 'core_form', implode(', ', $unknown));
        }

        // Check for disallowed file types.
        if ($notlisted = $this->util->get_not_listed($data, $this->onlytypes)) {
            return get_string('filetypesnotallowed', 'core_form', implode(', ', $notlisted));
        }

        return true;
    }

    /**
     * Return an HTML string for the setting element.
     *
     * @param string $data The current setting value
     * @param string $query Admin search query to be highlighted
     * @return string HTML to be displayed
     */
    public function output_html($data, $query = '') {
        global $OUTPUT, $PAGE;

        $default = $this->get_defaultsetting();
        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'descriptions' => $this->util->describe_file_types($data),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_filetypes', $context);

        $PAGE->requires->js_call_amd('core_form/filetypes', 'init', [
            $this->get_id(),
            $this->visiblename->out(),
            $this->onlytypes,
            $this->allowall,
        ]);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $default, $query);
    }

    /**
     * Should the values be always displayed in LTR mode?
     *
     * We always return true here because these values are not RTL compatible.
     *
     * @return bool True because these values are not RTL compatible.
     */
    public function get_force_ltr() {
        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(filetypes::class, \admin_setting_filetypes::class);
