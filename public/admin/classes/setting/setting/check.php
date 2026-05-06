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
 * Displays the result of a check via AJAX.
 *
 * @package    core_admin
 * @copyright  2024 onwards Moodle Pty Ltd {@link https://moodle.com}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class check extends \core_admin\setting {
    /** @var \core\check\check $check the check to use **/
    private $check;

    /** @var bool $includedetails if the details of result are included. **/
    private $includedetails;

    /**
     * Creates check setting.
     *
     * @param string $name name of setting
     * @param \core\check\check $check The check linked to this setting.
     * @param bool $includedetails if the details of the result are included
     */
    public function __construct(string $name, \core\check\check $check, bool $includedetails = false) {
        $this->check = $check;
        $this->includedetails = $includedetails;
        $heading = $check->get_name();

        parent::__construct($name, $heading, '', '');
    }

    /**
     * Returns the check linked to this setting.
     *
     * @return \core\check\check
     */
    public function get_check() {
        return $this->check;
    }

    /**
     * Returns setting (unused)
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Writes the setting (unused)
     *
     * @param mixed $data
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Outputs the admin setting HTML to be rendered.
     *
     * @param mixed $data
     * @param string $query
     * @return string html
     */
    public function output_html($data, $query = '') {
        global $PAGE, $OUTPUT;

        $domref = uniqid($this->check->get_ref());

        // The actual result is obtained via ajax,
        // Since its likely somewhat slow to obtain.
        $context = [
            'domselector' => '[data-check-reference="' . $domref . '"]',
            'admintreeid' => $this->get_id(),
            'settingname' => $this->name,
            'includedetails' => $this->includedetails,
        ];
        $PAGE->requires->js_call_amd('core/check/check_result', 'getAndRender', $context);

        // Render a generic loading icon while waiting for ajax.
        $loadingstr = get_string('checkloading', '', $this->check->get_name());
        $loadingicon = $OUTPUT->pix_icon('i/loading', $loadingstr);

        // Wrap it in a notification so we reduce style changes when loading is finished.
        $output = $OUTPUT->notification($loadingicon . $loadingstr, \core\output\notification::NOTIFY_INFO, false);

        // Add the action link.
        if ($actionlink = $this->check->get_action_link()) {
            $output .= $OUTPUT->render($actionlink);
        }

        // Wrap in a div with a reference. The JS getAndRender will replace this with the response from the webservice.
        $statusdiv = \html_writer::div($output, '', ['data-check-reference' => $domref]);

        return format_admin_setting($this, $this->visiblename, '', $statusdiv);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(check::class, \admin_setting_check::class);
