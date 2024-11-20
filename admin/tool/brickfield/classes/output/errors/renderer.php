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

namespace tool_brickfield\output\errors;

use tool_brickfield\accessibility;
use tool_brickfield\local\areas\module_area_base;
use tool_brickfield\local\tool\filter;
use tool_brickfield\local\tool\tool;
use tool_brickfield\manager;

/**
 * tool_brickfield/errors renderer
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \tool_brickfield\output\renderer {
    /**
     * Render the page containing the errors tool.
     *
     * @param \stdClass $data Report data.
     * @param filter $filter Display filters.
     * @return String HTML showing charts.
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function display(\stdClass $data, filter $filter): string {
        $templatedata = new \stdClass();

        // Need a URL for the paging bar.
        $pageurl = new \moodle_url(
            accessibility::get_plugin_url(),
            [
                'courseid' => $filter->courseid,
                'categoryid' => $filter->categoryid,
                'tab' => $filter->tab,
                'perpage' => $filter->perpage,
            ]
        );

        // Set up a table of data for the template.
        $templatedata->pagetitle = accessibility::get_title($filter, $data->countdata);

        if (count($data->errordata) == 0) {
            $templatedata->noerrorsfound = get_string('noerrorsfound', manager::PLUGINNAME);
            return $this->render_from_template(manager::PLUGINNAME . '/norecords', $templatedata);
        }

        $templatedata->tableheading1 = get_string('tbltarget', manager::PLUGINNAME);
        $templatedata->tableheading2 = get_string('tblcheck', manager::PLUGINNAME);
        $templatedata->tableheading3 = get_string('tblhtmlcode', manager::PLUGINNAME);
        $templatedata->tableheading4 = get_string('tblline', manager::PLUGINNAME);
        $templatedata->tableheading5 = get_string('tbledit', manager::PLUGINNAME);

        $templatedata->tabledata = [];
        foreach ($data->errordata as $err) {
            $row = new \stdClass();
            $row->activity = ucfirst(tool::get_instance_name($err->component, $err->tablename, $err->cmid,
                $err->courseid, $err->categoryid));
            $row->check = $err->checkdesc;
            $row->html = $err->htmlcode;
            $row->line = $err->errline;
            $row->edit = $this->get_link($err, $row->activity);
            $templatedata->tabledata[] = $row;
        }

        $bar = new \paging_bar($data->errortotal, $filter->page, $filter->perpage, $pageurl->out());
        $templatedata->pagenavigation = $this->render($bar);

        return $this->render_from_template(manager::PLUGINNAME . '/errors', $templatedata);
    }

    /**
     * Return a link to edit the appropriate content for the error.
     *
     * @param \stdClass $err
     * @param string $titlestr
     * @return string
     * @throws \coding_exception
     */
    public function get_link(\stdClass $err, string $titlestr): string {
        $out = '';

        $areaclass = '\tool_brickfield\local\areas\\' . $err->component . '\base';
        if (class_exists($areaclass)) {
            $link = $areaclass::get_edit_url($err);
        } else {
            $link = module_area_base::get_edit_url($err);
        }

        $title = get_string('errorlink', manager::PLUGINNAME, $titlestr);

        if (!isset($link)) {
            debugging($err->component . ' ' . $err->tablename);
        }
        $out .= \html_writer::link($link, get_string('edit'), ['title' => $title]);

        return $out;
    }
}
