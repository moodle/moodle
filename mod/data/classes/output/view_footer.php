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

namespace mod_data\output;

use action_link;
use core\output\sticky_footer;
use html_writer;
use mod_data\manager;
use mod_data\template;
use moodle_url;
use renderer_base;

/**
 * Renderable class for sticky footer in the view pages of the database activity.
 *
 * @package    mod_data
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_footer extends sticky_footer {

    /** @var int $totalcount the total records count. */
    private $totalcount;

    /** @var int $currentpage the current page */
    private $currentpage;

    /** @var int $nowperpage the number of elements per page */
    private $nowperpage;

    /** @var moodle_url $baseurl the page base url */
    private $baseurl;

    /** @var template $parser the template name */
    private $parser;

    /** @var manager $manager if the user can manage capabilities or not */
    private $manager;

    /**
     * The class constructor.
     *
     * @param manager $manager the activity manager
     * @param int $totalcount the total records count
     * @param int $currentpage the current page
     * @param int $nowperpage the number of elements per page
     * @param moodle_url $baseurl the page base url
     * @param template $parser the current template name
     */
    public function __construct(
        manager $manager,
        int $totalcount,
        int $currentpage,
        int $nowperpage,
        moodle_url $baseurl,
        template $parser
    ) {
        $this->manager = $manager;
        $this->totalcount = $totalcount;
        $this->currentpage = $currentpage;
        $this->nowperpage = $nowperpage;
        $this->baseurl = $baseurl;
        $this->parser = $parser;
    }

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(renderer_base $output) {
        $this->set_content(
            $this->get_footer_output($output)
        );
        return parent::export_for_template($output);
    }

    /**
     * Generate the pre-rendered footer content.
     *
     * @param \renderer_base $output The renderer to be used to render the action bar elements.
     * @return string the rendered content
     */
    public function get_footer_output(renderer_base $output): string {
        $data = [];

        $cm = $this->manager->get_coursemodule();
        $instance = $this->manager->get_instance();
        $currentgroup = groups_get_activity_group($cm);
        $groupmode = groups_get_activity_groupmode($cm);
        $context = $this->manager->get_context();
        $canmanageentries = has_capability('mod/data:manageentries', $context);
        $parser = $this->parser;

        // Sticky footer content.
        $data['pagination'] = $output->paging_bar(
            $this->totalcount,
            $this->currentpage,
            $this->nowperpage,
            $this->baseurl
        );

        if ($parser->get_template_name() != 'singletemplate' && $parser->has_tag('delcheck') && $canmanageentries) {
            // Build the select/deselect all control.
            $selectallid = 'selectall-listview-entries';
            $togglegroup = 'listview-entries';
            $mastercheckbox = new \core\output\checkbox_toggleall($togglegroup, true, [
                'id' => $selectallid,
                'name' => $selectallid,
                'value' => 1,
                'label' => get_string('selectall'),
                'classes' => 'btn-secondary mx-1',
            ], true);
            $data['selectall'] = $output->render($mastercheckbox);

            $data['deleteselected'] = html_writer::empty_tag('input', [
                'class' => 'btn btn-secondary mx-1',
                'type' => 'submit',
                'value' => get_string('deleteselected'),
                'disabled' => true,
                'data-action' => 'toggle',
                'data-togglegroup' => $togglegroup,
                'data-toggle' => 'action',
            ]);
        }
        if (data_user_can_add_entry($instance, $currentgroup, $groupmode, $context)) {
            $addentrylink = new moodle_url(
                '/mod/data/edit.php',
                ['id' => $cm->id, 'backto' => $this->baseurl]
            );
            $addentrybutton = new action_link(
                $addentrylink,
                get_string('add', 'mod_data'),
                null,
                ['class' => 'btn btn-primary mx-1', 'role' => 'button']
            );
            $data['addentrybutton'] = $addentrybutton->export_for_template($output);
        }
        return $output->render_from_template('mod_data/view_footer', $data);
    }
}
