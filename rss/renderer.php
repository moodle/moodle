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
 * Web service documentation renderer.
 * @package    core_rss
 * @category   rss
 * @copyright  2010 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Web service documentation renderer extending the plugin_renderer_base class.
 * @package    core_rss
 * @category   rss
 * @copyright  2010 Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_rss_renderer extends plugin_renderer_base {
    /**
     * Returns the html for the token reset confirmation box
     * @return string html
     */
    public function user_reset_rss_token_confirmation() {
        global $CFG;
        $managetokenurl = $CFG->wwwroot."/user/managetoken.php?sesskey=" . sesskey();
        $optionsyes = array('action' => 'resetrsstoken', 'confirm' => 1, 'sesskey' => sesskey());
        $optionsno  = array('section' => 'webservicetokens', 'sesskey' => sesskey());
        $formcontinue = new single_button(new moodle_url($managetokenurl, $optionsyes), get_string('reset'));
        $formcancel = new single_button(new moodle_url($managetokenurl, $optionsno), get_string('cancel'), 'get');
        $html = $this->output->confirm(get_string('resettokenconfirmsimple', 'webservice'), $formcontinue, $formcancel);
        return $html;
    }

    /**
     * Display a user token with buttons to reset it
     * @param string $token The token to be displayed
     * @return string html code
     */
    public function user_rss_token_box($token) {
        global $CFG;

        // Display strings.
        $stroperation = get_string('operation', 'webservice');
        $strtoken = get_string('key', 'webservice');

        $return = $this->output->heading(get_string('rss', 'rss'), 3, 'main', true);
        $return .= $this->output->box_start('generalbox webservicestokenui');

        $return .= get_string('rsskeyshelp');

        $table = new html_table();
        $table->head  = array($strtoken, $stroperation);
        $table->align = array('left', 'center');
        $table->width = '100%';
        $table->data  = array();

        if (!empty($token)) {
            $reset = "<a href=\"".$CFG->wwwroot."/user/managetoken.php?sesskey=".sesskey().
                    "&amp;action=resetrsstoken\">".get_string('reset')."</a>";

            $table->data[] = array($token, $reset);

            $return .= html_writer::table($table);
        } else {
            $return .= get_string('notoken', 'webservice');
        }

        $return .= $this->output->box_end();
        return $return;
    }
}
