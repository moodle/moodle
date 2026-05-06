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

use core_admin\admin_search;

/**
 * Special class for web service protocol administration.
 *
 * @author Petr Skoda (skodak)
 */
class admin_setting_managewebserviceprotocols extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('webservicesui', get_string('manageprotocols', 'webservice'), '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @return string Always returns ''
     */
    public function write_setting($data) {
    // do not write any setting
        return '';
    }

    /**
     * Checks if $query is one of the available webservices
     *
     * @param string $query The string to search for
     * @return bool Returns true if found, false if not
     */
    public function is_related($query) {
        if (parent::is_related($query)) {
            return true;
        }

        $protocols = core_component::get_plugin_list('webservice');
        foreach ($protocols as $protocol=>$location) {
            if (strpos($protocol, $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_SHORT_NAME;
                return true;
            }
            $protocolstr = get_string('pluginname', 'webservice_'.$protocol);
            if (strpos(core_text::strtolower($protocolstr), $query) !== false) {
                $this->searchmatchtype = admin_search::SEARCH_MATCH_SETTING_DISPLAY_NAME;
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        // display strings
        $stradministration = get_string('administration');
        $strsettings = get_string('settings');
        $stredit = get_string('edit');
        $strprotocol = get_string('protocol', 'webservice');
        $strenable = get_string('enable');
        $strdisable = get_string('disable');
        $strversion = get_string('version');

        $protocols_available = core_component::get_plugin_list('webservice');
        $activeprotocols = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
        ksort($protocols_available);

        foreach ($activeprotocols as $key => $protocol) {
            if (empty($protocols_available[$protocol])) {
                unset($activeprotocols[$key]);
            }
        }

        $return = $OUTPUT->heading(get_string('actwebserviceshhdr', 'webservice'), 3, 'main');
        if (in_array('xmlrpc', $activeprotocols)) {
            $notify = new \core\output\notification(get_string('xmlrpcwebserviceenabled', 'admin'),
                \core\output\notification::NOTIFY_WARNING);
            $return .= $OUTPUT->render($notify);
        }
        $return .= $OUTPUT->box_start('generalbox webservicesui');

        $table = new html_table();
        $table->head  = array($strprotocol, $strversion, $strenable, $strsettings);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign', 'centeralign', 'centeralign');
        $table->id = 'webserviceprotocols';
        $table->attributes['class'] = 'admintable table generaltable table-hover';
        $table->data  = array();

        // iterate through auth plugins and add to the display table
        $url = "$CFG->wwwroot/$CFG->admin/webservice/protocols.php?sesskey=" . sesskey();
        foreach ($protocols_available as $protocol => $location) {
            $name = get_string('pluginname', 'webservice_'.$protocol);

            $plugin = new stdClass();
            if (file_exists($CFG->dirroot.'/webservice/'.$protocol.'/version.php')) {
                include($CFG->dirroot.'/webservice/'.$protocol.'/version.php');
            }
            $version = isset($plugin->version) ? $plugin->version : '';

            // hide/show link
            if (in_array($protocol, $activeprotocols)) {
                $hideshow = "<a href=\"$url&amp;action=disable&amp;webservice=$protocol\">";
                $hideshow .= $OUTPUT->pix_icon('t/hide', $strdisable) . '</a>';
                $displayname = "<span>$name</span>";
            } else {
                $hideshow = "<a href=\"$url&amp;action=enable&amp;webservice=$protocol\">";
                $hideshow .= $OUTPUT->pix_icon('t/show', $strenable) . '</a>';
                $displayname = "<span class=\"dimmed_text\">$name</span>";
            }

            // settings link
            if (file_exists($CFG->dirroot.'/webservice/'.$protocol.'/settings.php')) {
                $settings = "<a href=\"settings.php?section=webservicesetting$protocol\">$strsettings</a>";
            } else {
                $settings = '';
            }

            // add a row to the table
            $table->data[] = array($displayname, $version, $hideshow, $settings);
        }
        $return .= html_writer::table($table);
        $return .= get_string('configwebserviceplugins', 'webservice');
        $return .= $OUTPUT->box_end();

        return highlight($query, $return);
    }
}
