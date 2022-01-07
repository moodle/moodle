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

namespace mod_scorm\output;

use renderable;
use renderer_base;
use templatable;
use moodle_url;
use url_select;

/**
 * Render HTML elements for reports page on tertiary nav.
 *
 * @package mod_scorm
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userreportsactionbar implements renderable, templatable {
    /** @var int */
    private $id;

    /** @var int */
    private $userid;

    /** @var int */
    private $attempt;

    /** @var string */
    private $reporttype;

    /** @var string */
    private $mode;

    /** @var int */
    private $scoid;

    /**
     * userreportsactionbar constructor
     *
     * @param int $id Course module id.
     * @param int $userid User id.
     * @param int $attempt Number of attempts.
     * @param string $reporttype The report type can be either learning/interact.
     * @param string $mode The mode view to set the back button.
     * @param int|null $scoid The scorm id.
     */
    public function __construct(int $id, int $userid, int $attempt, string $reporttype, string $mode, ?int $scoid = null) {
        $this->id = $id;
        $this->userid = $userid;
        $this->attempt = $attempt;
        $this->reporttype = $reporttype;
        $this->mode = $mode;
        $this->scoid = $scoid;
    }

    /**
     * Provide data for the template
     *
     * @param renderer_base $output renderer_base object.
     * @return array data for the template.
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'backurl' => (new moodle_url('/mod/scorm/report.php', ['id' => $this->id, 'mode' => $this->mode]))->out(false)
        ];

        if (!$this->scoid) {
            $learnobjects = new moodle_url('/mod/scorm/report/userreport.php',
                    ['id' => $this->id, 'user' => $this->userid, 'attempt' => $this->attempt, 'mode' => $this->mode]);
            $interactions = new moodle_url('/mod/scorm/report/userreportinteractions.php',
                    ['id' => $this->id, 'user' => $this->userid, 'attempt' => $this->attempt, 'mode' => $this->mode]);

            $reportmenu[$learnobjects->out(false)] = get_string('scoes', 'scorm');
            $reportmenu[$interactions->out(false)] = get_string('interactions', 'scorm');

            if ($this->reporttype === 'learning') {
                $userreporturl = $learnobjects->out(false);
            } else {
                $userreporturl = $interactions->out(false);
            }
            $urlselect = new url_select($reportmenu, $userreporturl, [], 'userscormreport');
            $data ['userreport'] = $urlselect->export_for_template($output);
            $data ['heading'] = $reportmenu[$userreporturl] ?? null;
        }

        return $data;
    }
}
