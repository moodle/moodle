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
 * Event report renderer.
 *
 * @package    report_eventlist
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Renderer for event report.
 *
 * @package    report_eventlist
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_eventlist_renderer extends plugin_renderer_base {

    /**
     * Renders the event list page with filter form and datatable.
     *
     * @param eventfilter_form $form Event filter form.
     * @param array $tabledata An array of event data to be used by the datatable.
     * @return string HTML to be displayed.
     */
    public function render_event_list($form, $tabledata) {

        $title = get_string('pluginname', 'report_eventlist');

        // Header.
        $html = $this->output->header();
        $html .= $this->output->heading($title);

        // Form.
        ob_start();
        $form->display();
        $html .= ob_get_contents();
        ob_end_clean();

        $this->page->requires->yui_module('moodle-report_eventlist-eventfilter', 'Y.M.report_eventlist.EventFilter.init',
                array(array('tabledata' => $tabledata)));
        $this->page->requires->strings_for_js(array(
            'eventname',
            'component',
            'action',
            'crud',
            'edulevel',
            'affectedtable',
            'dname',
            'legacyevent',
            'since'
            ), 'report_eventlist');
        $html .= html_writer::start_div('report-eventlist-data-table', array('id' => 'report-eventlist-table'));
        $html .= html_writer::end_div();

        $html .= $this->output->footer();
        return $html;
    }

    /**
     * Event detail renderer.
     *
     * @param array $observerlist A list of observers that consume this event.
     * @param array $eventinformation A list of information about the event.
     * @return string HTML to be displayed.
     */
    public function render_event_detail($observerlist, $eventinformation) {

        $titlehtml = $this->output->header();
        $titlehtml .= $this->output->heading($eventinformation['title']);

        $html = html_writer::start_tag('dl', array('class' => 'list'));

        $explanation = nl2br($eventinformation['explanation']);
        $html .= html_writer::tag('dt', get_string('eventexplanation', 'report_eventlist'));
        $html .= html_writer::tag('dd', $explanation);

        if (isset($eventinformation['crud'])) {
            $html .= html_writer::tag('dt', get_string('crud', 'report_eventlist'));
            $html .= html_writer::tag('dd', $eventinformation['crud']);
        }

        if (isset($eventinformation['edulevel'])) {
            $html .= html_writer::tag('dt', get_string('edulevel', 'report_eventlist'));
            $html .= html_writer::tag('dd', $eventinformation['edulevel']);
        }

        if (isset($eventinformation['objecttable'])) {
            $html .= html_writer::tag('dt', get_string('affectedtable', 'report_eventlist'));
            $html .= html_writer::tag('dd', $eventinformation['objecttable']);
        }

        if (isset($eventinformation['legacyevent'])) {
            $html .= html_writer::tag('dt', get_string('legacyevent', 'report_eventlist'));
            $html .= html_writer::tag('dd', $eventinformation['legacyevent']);
        }

        if (isset($eventinformation['parentclass'])) {
            $url = new moodle_url('eventdetail.php', array('eventname' => $eventinformation['parentclass']));
            $html .= html_writer::tag('dt', get_string('parentevent', 'report_eventlist'));
            $html .= html_writer::tag('dd', html_writer::link($url, $eventinformation['parentclass']));
        }

        if (isset($eventinformation['abstract'])) {
            $html .= html_writer::tag('dt', get_string('abstractclass', 'report_eventlist'));
            $html .= html_writer::tag('dd', get_string('yes', 'report_eventlist'));
        }

        if (isset($eventinformation['typeparameter'])) {
            $html .= html_writer::tag('dt', get_string('typedeclaration', 'report_eventlist'));
            foreach ($eventinformation['typeparameter'] as $typeparameter) {
                $html .= html_writer::tag('dd', $typeparameter);
            }
        }

        if (isset($eventinformation['otherparameter'])) {
            $html .= html_writer::tag('dt', get_string('othereventparameters', 'report_eventlist'));
            foreach ($eventinformation['otherparameter'] as $otherparameter) {
                $html .= html_writer::tag('dd', $otherparameter);
            }
        }

        // List observers consuming this event if there are any.
        if (!empty($observerlist)) {
            $html .= html_writer::tag('dt', get_string('relatedobservers', 'report_eventlist'));
            foreach ($observerlist as $observer) {
                if ($observer->plugin == 'core') {
                    $html .= html_writer::tag('dd', $observer->plugin);
                } else {
                    $manager = get_string_manager();
                    $pluginstring = $observer->plugintype . '_' . $observer->plugin;
                    if ($manager->string_exists('pluginname', $pluginstring)) {
                        if (!empty($observer->parentplugin)) {
                            $string = get_string('pluginname', $pluginstring) . ' (' . $observer->parentplugin
                                    . ' ' . $pluginstring . ')';
                        } else {
                            $string = get_string('pluginname', $pluginstring) . ' (' . $pluginstring . ')';
                        }
                    } else {
                        $string = $observer->plugintype . ' ' . $observer->plugin;
                    }
                    $html .= html_writer::tag('dd', $string);
                }
            }
        }
        $html .= html_writer::end_div();
        $html .= html_writer::end_tag('dl');

        $pagecontent = new html_table();
        $pagecontent->data = array(array($html));
        $pagehtml = $titlehtml . html_writer::table($pagecontent);
        $pagehtml .= $this->output->footer();

        return $pagehtml;
    }
}
