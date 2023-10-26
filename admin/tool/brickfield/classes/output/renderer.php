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

namespace tool_brickfield\output;

use tool_brickfield\accessibility;
use plugin_renderer_base;
use moodle_url;
use tabobject;
use tabtree;
use html_writer;
use tool_brickfield\analysis;
use tool_brickfield\local\tool\filter;
use tool_brickfield\local\tool\tool;
use tool_brickfield\manager;
use tool_brickfield\scheduler;

/**
 * tool_brickfield renderer
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @author     Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render the page containing the tool report.
     *
     * @param \stdClass $data Report data.
     * @param filter $filter Display filters.
     * @return String HTML showing charts.
     */
    public function display(\stdClass $data, filter $filter): string {
        $component = 'tool_brickfield';
        $subtype = $filter->tab;
        $toolrenderer = $this->page->get_renderer($component, $subtype);
        if (!empty($toolrenderer)) {
            return $toolrenderer->display($data, $filter);
        }
    }

    /**
     * Render the valid tabs.
     *
     * @param filter $filter
     * @param array $tools
     * @return string
     * @throws \moodle_exception
     */
    public function tabs(filter $filter, array $tools): string {
        $idprefix = 'tab_';
        $tabs = [];
        foreach ($tools as $toolname => $tool) {
            $link = new moodle_url(
                accessibility::get_plugin_url(),
                array_merge(['tab' => $toolname, ], $tool->toplevel_arguments($filter))
            );
            if (isset($altlabel[$toolname])) {
                $label = $altlabel[$toolname];
            } else {
                $label = $tool->get_toolshortname();
            }
            $tab = new tabobject($idprefix . $toolname, $link, $label);
            $tab->extraclass = isset($extraclass[$toolname]) ? $extraclass[$toolname] : null;
            $tabs[] = $tab;
        }
        return $this->render(new tabtree($tabs, $idprefix . $filter->tab));
    }

    /**
     * Renders tabtree
     *
     * @param tabtree $tabtree
     * @return string
     * @throws \moodle_exception
     */
    protected function render_tabtree(tabtree $tabtree): string {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $data = $tabtree->export_for_template($this);
        foreach ($data->tabs as $idx => $tab) {
            if (isset($tabtree->subtree[$idx]->extraclass)) {
                $data->tabs[$idx]->extraclass = $tabtree->subtree[$idx]->extraclass;
            }
        }
        return $this->render_from_template(manager::PLUGINNAME . '/tabtree', $data);
    }

    /**
     * Render the cache alert message.
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function cachealert(): string {
        $html = '';
        if (!analysis::is_enabled()) {
            $html = \html_writer::div(get_string('analysistypedisabled', manager::PLUGINNAME),
                '', ['class' => 'alert alert-primary']);
        }
        return $html;
    }

    /**
     * This function assumes that 'scheduler::is_analysed' has already failed.
     * @param int $courseid
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function analysisalert(int $courseid): string {
        $siteorcourse = ($courseid == SITEID) ? 'site' : '';
        if (scheduler::is_course_in_schedule($courseid)) {
            $html = \html_writer::div(get_string('schedule:' . $siteorcourse . 'scheduled', manager::PLUGINNAME),
                '', ['class' => 'alert alert-primary']);
        } else {
            $html = \html_writer::div(
                get_string('schedule:' . $siteorcourse . 'notscheduled', manager::PLUGINNAME, manager::get_helpurl()),
                '', ['class' => 'alert alert-primary']
            );
            $html .= $this->analysisbutton($courseid);
        }
        return $html;
    }

    /**
     * Render the "not validated" alert message.
     *
     * @return string
     * @throws \coding_exception
     */
    public function notvalidatedalert(): string {
        return \html_writer::div(get_string('notvalidated', manager::PLUGINNAME), '', ['class' => 'alert alert-primary']);
    }

    /**
     * Render the analysis request button.
     *
     * @param int $courseid
     * @return string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function analysisbutton(int $courseid) : string {
        $link = new moodle_url(accessibility::get_plugin_url(), [
            'action' => 'requestanalysis',
            'courseid' => $courseid
        ]);

        $classname = manager::PLUGINNAME . '_analysisbutton';

        $button = new \single_button(
            $link,
            get_string('schedule:requestanalysis', manager::PLUGINNAME),
            'post',
            true,
            ['class' => $classname]
        );

        return html_writer::tag('div', $this->render($button), ['class' => $classname]);
    }
}
