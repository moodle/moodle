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
 * Logger that stores HTML log data in memory, ready for later display.
 *
 * @package core_backup
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_backup_html_logger extends base_logger {
    /**
     * @var string HTML output
     */
    protected $html = '';

    protected function action($message, $level, $options = null) {
        $prefix = $this->get_prefix($level, $options);
        $depth = isset($options['depth']) ? $options['depth'] : 0;
        $this->html .= $prefix . str_repeat('&nbsp;&nbsp;', $depth) .
                s($message) . '<br/>' . PHP_EOL;
        return true;
    }

    /**
     * Gets the full HTML content of the log.
     *
     * @return string HTML content of log
     */
    public function get_html() {
        return $this->html;
    }
}
