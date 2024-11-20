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
 * @package   block_mycourses
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mycourses\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use core_completion\progress;
use core_course_renderer;
use moodle_url;
use iomad;
use context_system;

require_once($CFG->dirroot . '/blocks/mycourses/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

/**
 * Class containing data for my overview block.
 *
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    /**
     * @var string The tab to display.
     */
    public $tab;

    /**
     * Constructor.
     *
     * @param string $tab The tab to display.
     */
    public function __construct($tab) {
        $this->tab = $tab;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG, $USER, $PAGE;

        // Get the sorting params.
        $sort = optional_param('sort', 'coursefullname', PARAM_CLEAN);
        $dir = optional_param('dir', 'ASC', PARAM_CLEAN);
        $tab = optional_param('tab', 'inprogress#mycourses_inprogress_view', PARAM_CLEAN);
        $view = optional_param('view', $CFG->mycourses_defaultview, PARAM_CLEAN);

        // Get the completion info.
        $mycompletion = mycourses_get_my_completion($sort, $dir);
        $myarchive = mycourses_get_my_archive($sort, $dir);

        $availableview = new available_view($mycompletion);
        $inprogressview = new inprogress_view($mycompletion);
        $completedview = new completed_view($myarchive);

        $downloadcerts = false;
        $downloadcertslink = "";
        if (iomad::has_capability('block/iomad_company_admin:downloadmycertificates', context_system::instance())) {
            $downloadcertslinkurl = new moodle_url('/local/report_completion/index.php', ['certusers' => $USER->id, 'action' => 'downloadcerts', 'sesskey' => sesskey()]);
            $downloadcertslink = $downloadcertslinkurl->out(false);
            $downloadcerts = true;
        }

        // Now, set the tab we are going to be viewing.
        $viewingavailable = false;
        $viewinginprogress = false;
        $viewingcompleted = false;
        if ($this->tab == 'available') {
            $viewingavailable = true;
        } else if ($this->tab == 'completed') {
            $viewingcompleted = true;
        } else {
            $viewinginprogress = true;
        }
        $nocoursesurl = $output->image_url('courses', 'block_mycourses')->out();
        $sortnameurl = new moodle_url($PAGE->url->out(false), ['sort' => 'coursefullname', 'dir' => $dir, 'tab' => $this->tab, 'view' => $view]);
        $sortdateurl = new moodle_url($PAGE->url->out(false), ['sort' => 'timestarted', 'dir' => $dir, 'tab' => $this->tab, 'view' => $view]);
        $sortascurl = new moodle_url($PAGE->url->out(false), ['sort' => $sort, 'dir' => 'ASC', 'tab' => $this->tab, 'view' => $view]);
        $sortdescurl = new moodle_url($PAGE->url->out(false), ['sort' => $sort, 'dir' => 'DESC', 'tab' => $this->tab, 'view' => $view]);
        $listviewurl = new moodle_url($PAGE->url->out(false), ['sort' => $sort, 'dir' => $dir, 'tab' => $this->tab, 'view' => 'list']);
        $cardviewurl = new moodle_url($PAGE->url->out(false), ['sort' => $sort, 'dir' => $dir, 'tab' => $this->tab, 'view' => 'card']);
        $viewlist = false;
        $viewcard = false;
        if ($view == 'list') {
            $viewlist = true;
        }
        if ($view == 'card') {
            $viewcard = true;
        }

        return [
            'midnight' => usergetmidnight(time()),
            'nocourses' => $nocoursesurl,
            'availableview' => $availableview->export_for_template($output),
            'inprogressview' => $inprogressview->export_for_template($output),
            'completedview' => $completedview->export_for_template($output),
            'viewingavailable' => $viewingavailable,
            'viewinginprogress' => $viewinginprogress,
            'viewingcompleted' => $viewingcompleted,
            'sortnameurl' => $sortnameurl->out(false),
            'sortdateurl' => $sortdateurl->out(false),
            'sortascurl' => $sortascurl->out(false),
            'sortdescurl' => $sortdescurl->out(false),
            'listviewurl' => $listviewurl->out(false),
            'cardviewurl' => $cardviewurl->out(false),
            'downloadcertslink' => $downloadcertslink,
            'downloadcerts' => $downloadcerts,
            'viewlist' => $viewlist,
            'viewcard' => $viewcard,
        ];
    }
}
