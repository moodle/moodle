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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class component_base
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class component_base {

    /**
     * @var bool
     */
    public $plugins = false;

    /**
     * @var bool
     */
    public $ordering = false;

    /**
     * @var bool
     */
    public $form = false;

    /**
     * @var string
     */
    public string $help = '';

    /**
     * @var object
     */
    protected object $config;

    /**
     * __construct
     *
     * @param int|object $report
     */
    public function __construct($report) {
        global $DB;

        if (is_numeric($report)) {
            $this->config = $DB->get_record('block_configurable_reports', ['id' => $report], '*', MUST_EXIST);
        } else {
            $this->config = $report;
        }
        $this->init();
    }

    /**
     * add_form_elements
     *
     * @param MoodleQuickForm $mform
     * @param string|object $components
     * @return void
     */
    public function add_form_elements(MoodleQuickForm $mform, $components): void {

    }

}
