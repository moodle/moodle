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
 * Render HTML elements for tertiary nav for scorm.
 *
 * @package mod_scorm
 * @copyright 2021 Sujith Haridasan <sujith@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class actionbar implements renderable, templatable {
    /** @var int */
    private $id;

    /** @var string */
    private $download;

    /** @var int */
    private $attemptsmode;

    /**
     * actionbar constructor.
     *
     * @param int $id The course module id.
     * @param bool $download Show download button or not.
     * @param int $attemptsmode attempts mode for scorm.
     */
    public function __construct(int $id, bool $download = false, int $attemptsmode = 0) {
        $this->id = $id;
        $this->download = $download;
        $this->attemptsmode = $attemptsmode;
    }

    /**
     * Provide data for the template
     *
     * @param renderer_base $output renderer_base object.
     * @return array data for the template
     */
    public function export_for_template(renderer_base $output): array {
        global $PAGE;

        $basicreportlink = new moodle_url('/mod/scorm/report.php', ['id' => $this->id, 'mode' => 'basic']);
        $graphreportlink = new moodle_url('/mod/scorm/report.php', ['id' => $this->id, 'mode' => 'graphs']);
        $interactionreportlink = new moodle_url('/mod/scorm/report.php', ['id' => $this->id, 'mode' => 'interactions']);
        $objectivesreportlink = new moodle_url('/mod/scorm/report.php', ['id' => $this->id, 'mode' => 'objectives']);

        $reportmenu = [
            $basicreportlink->out(false) => get_string('pluginname', 'scormreport_basic'),
            $graphreportlink->out(false) => get_string('pluginname', 'scormreport_graphs'),
            $interactionreportlink->out(false) => get_string('pluginname', 'scormreport_interactions'),
            $objectivesreportlink->out(false) => get_string('pluginname', 'scormreport_objectives'),
        ];

        $sesskey = sesskey();
        if ($this->download) {
            $mode = $PAGE->url->get_param('mode');
            if ($mode === 'basic') {
                $options = [
                    'id' => $this->id, 'mode' => $mode,
                    'attemptsmode' => $this->attemptsmode, 'sesskey' => $sesskey
                ];
            } else if ($mode === 'interactions') {
                $options = [
                    'id' => $this->id, 'mode' => $mode, 'qtext' => '0',
                    'resp' => '1', 'right' => '0', 'result' => '0',
                    'attemptsmode' => $this->attemptsmode, 'sesskey' => $sesskey
                ];
            } else if ($mode === 'objectives') {
                $options = [
                    'id' => $this->id, 'mode' => $mode,
                    'attemptsmode' => $this->attemptsmode, 'objectivescore' => '0',
                    'sesskey' => $sesskey
                ];
            }

            $options['download'] = 'ODS';
            $downloadodslink = new moodle_url($PAGE->url, $options);

            $options['download'] = 'Excel';
            $downloadexcellink = new moodle_url($PAGE->url, $options);

            $options['download'] = 'CSV';
            $downloadtextlink = new moodle_url($PAGE->url, $options);
        }

        $url = new moodle_url('/mod/scorm/report.php', $PAGE->url->remove_params('attemptsmode'));
        $urlselect = new url_select($reportmenu, $url->out(false), null, 'selectscormreports');
        $heading = $reportmenu[$url->out(false)] ?? null;

        $data = [
            'heading' => $heading,
            'scormreports' => $urlselect->export_for_template($output),
            'candownload' => $this->download,
            'downloadods' => ($this->download) ? $downloadodslink->out(false) : '',
            'downloadexcel' => ($this->download) ? $downloadexcellink->out(false) : '',
            'downloadtext' => ($this->download) ? $downloadtextlink->out(false) : ''
        ];
        return $data;
    }
}
