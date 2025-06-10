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

// TODO namespace.

/**
 * Class plugin_base
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class plugin_base {

    /**
     * @var string
     */
    public string $fullname = '';

    /**
     * @var string
     */
    public $type = '';

    /**
     * @var false|mixed|stdClass|null
     */
    public $report = null;

    /**
     * @var bool
     */
    public bool $form = false;

    /**
     * @var array
     */
    public $cache = [];

    /**
     * @var bool
     */
    public bool $unique = false;

    /**
     * @var array
     */
    public array $reporttypes = [];

    /**
     * @var true
     */
    public bool $ordering;

    /**
     * __construct
     *
     * @param int|object $report
     */
    public function __construct($report) {
        global $DB;

        if (is_numeric($report)) {
            $this->report = $DB->get_record('block_configurable_reports', ['id' => $report]);
        } else {
            $this->report = $report;
        }
        $this->init();
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return '';
    }

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        throw new coding_exception('init method not implemented');
    }

    /**
     * colformat
     *
     * @param object|null $data
     * @return string[]
     */
    public function colformat(?object $data): array {
        $align = $data->align ?? '';
        $size = $data->size ?? '';
        $wrap = $data->wrap ?? '';

        return [$align, $size, $wrap];
    }

    /**
     * print_filter
     *
     * @param MoodleQuickForm $mform
     * @param bool|object $formdata
     * @return mixed
     */
    public function print_filter(MoodleQuickForm $mform, $formdata = false): void {
        throw new coding_exception('print_filter method not implemented');
    }

}
