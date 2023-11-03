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

defined('MOODLE_INTERNAL') || die();

use block_reportdashboard\local;
use block_learnerscript\local\ls as ls;

/**
 * Block Report Dashboard renderer.
 * @package   block_reportdashboard
 */
class block_reportdashboard_renderer extends plugin_renderer_base {

    public function render_widgetheader($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_reportdashboard/widgetheader', $data);
    }
    public function render_reportarea($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_reportdashboard/reportarea', $data);
    }
    public function render_dashboardheader($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_reportdashboard/dashboardheader', $data);
    }
    /**
     * List of role for current user
     * @return [string] [list of roles]
     */
    public function switch_role() {
        global $USER, $DB, $OUTPUT, $COURSE, $CFG;
        $actions = '';
        if (!empty($_SESSION['role'])) {
            $currentrole = $_SESSION['role'];
        } else {
            $currentrole = 'Switch Role';
        }
        $actions .= html_writer::start_tag("span", array("class" => "dropdown", "id" => "switchrole_dropdwn"));
        $actions .= html_writer::tag("button", $currentrole, array("class" => "dropbtn",
                "onclick" => "(function(e){ require('block_learnerscript/helper').dropdown('switchrole_menu')
                })(event)"));
        $actions .= html_writer::start_tag("ul", array("id" => "switchrole_menu", "class" => "dropdown-content"));
        $systemcontext = context_system::instance();
        if (!is_siteadmin()) {
            $roles = (new ls)->get_currentuser_roles();
        } else {
            $roles = get_switchable_roles($systemcontext);
        }
        $actions .= html_writer::start_tag("li", array("role" => "presentation"))
        . html_writer::link($CFG->wwwroot.'/blocks/reportdashboard/dashboard.php', 'Switch Role', array())
        . html_writer::end_tag("li");
        foreach ($roles as $key => $value) {
            $roleshortname = $DB->get_field('role', 'shortname', array('id' => $key));
            $roleurl = new moodle_url('/blocks/reportdashboard/dashboard.php',
                array('role' => $roleshortname));
            $actions .= html_writer::start_tag("li", array("role" => "presentation"))
            . html_writer::link($roleurl, $value, array())
            . html_writer::end_tag("li");
        }

        $actions .= html_writer::end_tag("ul");
        $actions .= html_writer::end_tag("span");
        return $actions;
    }
}