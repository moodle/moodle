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
 * Adaptable theme.
 *
 * @package    theme_adaptable
 * @copyright  2021 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later,
 */

namespace theme_adaptable;

/**
 * Setting that displays markdown files.  Based on admin_setting_description in adminlib.php.
 */
class admin_setting_markdown extends \admin_setting {
    /** @var string Filename */
    private $filename;

    /**
     * Not a setting, just markup.
     *
     * @param string $name Setting name.
     * @param string $visiblename Setting name on the device.
     * @param string $description Setting description on the device.
     * @param string $filename The file to show.
     */
    public function __construct($name, $visiblename, $description, $filename) {
        $this->nosave = true;
        $this->filename = $filename;
        parent::__construct($name, $visiblename, $description, '');
    }

    /**
     * Always returns true.
     *
     * @return bool Always returns true.
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true.
     *
     * @return bool Always returns true.
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     *
     * @param mixed $data Gets converted to str for comparison against yes value.
     * @return string Always returns an empty string.
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Returns an HTML string
     *
     * @param string $data
     * @param string $query
     * @return string Returns an HTML string
     */
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT;

        $context = new \stdClass();
        $context->title = $this->visiblename;
        $context->description = $this->description;

        if (file_exists("{$CFG->dirroot}/theme/adaptable/{$this->filename}")) {
            $filecontents = file_get_contents($CFG->dirroot . '/theme/adaptable/' . $this->filename);
        } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/adaptable/{$this->filename}")) {
            $filecontents = file_get_contents($CFG->themedir . '/adaptable/' . $this->filename);
        } else {
            $filecontents = 'admin_setting_markdown -> file not found: ' . $this->filename;
        }
        $context->markdown = format_text($filecontents, FORMAT_MARKDOWN);

        return $OUTPUT->render_from_template('theme_adaptable/adaptable_admin_setting_markdown', $context);
    }
}
