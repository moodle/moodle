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
 * Table log for generating data in ajax mode.
 *
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Table log class for generating data in ajax mode.
 *
 * @since      Moodle 2.7
 * @package    report_loglive
 * @copyright  2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_loglive_table_log_ajax extends report_loglive_table_log {

    /**
     * Convenience method to call a number of methods for you to display the
     * table.
     *
     * @param int $pagesize pagesize
     * @param bool $useinitialsbar Not used, present only for compatibility with parent.
     * @param string $downloadhelpbutton Not used, present only for compatibility with parent.
     *
     * @return string json encoded data containing html of new rows.
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        $this->query_db($pagesize, false);
        $html = '';
        if ($this->rawdata && $this->columns) {
            foreach ($this->rawdata as $row) {
                $formatedrow = $this->format_row($row);
                $formatedrow = $this->get_row_from_keyed($formatedrow);
                $html .= $this->get_row_html($formatedrow, 'newrow');
            }
        }
        $result = array('logs' => $html, 'until' => $this->get_until());
        return json_encode($result);
    }

    /**
     * Popup actions do not function when they are rendered in response to an AJAX request, encode within the link itself
     *
     * @param moodle_url $url
     * @param string $text
     * @param string $name
     * @return string
     */
    protected function action_link(moodle_url $url, $text, $name = 'popup') {
        global $OUTPUT;

        $link = new action_link($url, $text, null, [
            'data-action' => 'action-popup',
            'data-popup-action' => json_encode(
                new popup_action('click', $url, $name, ['height' => 440, 'width' => 700])
            ),
        ]);

        return $OUTPUT->render($link);
    }
}
