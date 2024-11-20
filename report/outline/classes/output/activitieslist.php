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

namespace report_outline\output;

use core_report\output\coursestructure;
use course_modinfo;

/**
 * Activities list page.
 *
 * @package    report_outline
 * @copyright  2024 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activitieslist extends coursestructure {
    /**
     * Constructor
     *
     * @param course_modinfo $modinfo
     * @param array $views Views information for activity and users.
     * @param bool $showlastaccess Whether the last access should be shown or not.
     * @param int $minlog The minimum log time is computed.
     * @param bool $showblogs Whether related blog entries should be shown or not.
     */
    public function __construct(
            course_modinfo $modinfo,
            /** @var array $views Views information for activity and users. */
            protected array $views,
            /** @var bool $showlastaccess Whether the last access should be shown or not. */
            protected bool $showlastaccess = true,
            /** @var int $minlog The minimum log time is computed. */
            protected int $minlog = 0,
            /** @var bool $showblogs Whether related blog entries should be shown or not. */
            protected bool $showblogs = true,
    ) {
        $this->modinfo = $modinfo;
    }

    /**
     * Exports the data.
     *
     * @param \renderer_base $output
     * @return array|\stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $table = parent::export_for_template($output);
        $table['id'] = 'outlinereport';
        $data = [
            'minlog' => userdate($this->minlog),
            'table' => $table,
        ];

        return $data;
    }

    /**
     * Exports the headers for report table.
     *
     * @param \renderer_base $output
     * @return array
     */
    protected function export_headers(\renderer_base $output): array {
        $headers = parent::export_headers($output);
        $headers[] = get_string('views');

        if ($this->showblogs) {
            $headers[] = get_string('relatedblogentries', 'blog');
        }

        if ($this->showlastaccess) {
            $headers[] = get_string('lastaccess');
        }

        return $headers;
    }

    /**
     * Exports the data for a single activity.
     *
     * @param \renderer_base $output
     * @param \cm_info $cm
     * @param bool $indelegated Whether the activity is part of a delegated section or not.
     * @return array
     */
    public function export_activity_data(\renderer_base $output, \cm_info $cm, bool $indelegated = false): array {
        global $CFG;

        $data = parent::export_activity_data($output, $cm, $indelegated);
        if (empty($data) || !in_array('cells', $data)) {
            return [];
        }

        if (!empty($this->views[$cm->id]->numviews)) {
            $numviewscell = get_string('numviews', 'report_outline', $this->views[$cm->id]);
        } else {
            $numviewscell = '-';
        }

        $data['cells'][] = [
            'activityclass' => 'numviews',
            'text' => $numviewscell,
        ];

        if ($this->showblogs) {
            $cell = ['activityclass' => 'blog'];
            require_once($CFG->dirroot.'/blog/lib.php');
            if ($blogcount = blog_get_associated_count($cm->get_course()->id, $cm->id)) {
                $blogurl = new \moodle_url('/blog/index.php', ['modid' => $cm->id]);
                $cell['link']  = new \action_link($blogurl, $blogcount);
            } else {
                $cell['text'] = '-';
            }
            $data['cells'][] = $cell;
        }

        if ($this->showlastaccess) {
            if (isset($this->views[$cm->id]->lasttime)) {
                $timeago = format_time(time() - $this->views[$cm->id]->lasttime);
                $lastaccesscell = userdate($this->views[$cm->id]->lasttime)." ($timeago)";
            } else {
                $lastaccesscell = '';
            }
            $data['cells'][] = [
                    'activityclass' => 'lastaccess',
                    'text' => $lastaccesscell,
            ];
        }

        return $data;
    }
}
