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
 * Class text_progress_tracker
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_uploaduser\local;

/**
 * Tracks the progress of the user upload and echos it in a text format
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class text_progress_tracker extends \uu_progress_tracker {

    /**
     * Print table header.
     * @return void
     */
    public function start() {
        $this->_row = null;
    }

    /**
     * Output one line (followed by newline)
     * @param string $line
     */
    protected function output_line(string $line): void {
        echo $line . PHP_EOL;
    }

    /**
     * Flush previous line and start a new one.
     * @return void
     */
    public function flush() {
        if (empty($this->_row) or empty($this->_row['line']['normal'])) {
            // Nothing to print - each line has to have at least number.
            $this->_row = array();
            foreach ($this->columns as $col) {
                $this->_row[$col] = ['normal' => '', 'info' => '', 'warning' => '', 'error' => ''];
            }
            return;
        }
        $this->output_line(get_string('linex', 'tool_uploaduser', $this->_row['line']['normal']));
        $prefix = [
            'normal' => '',
            'info' => '',
            'warning' => get_string('warningprefix', 'tool_uploaduser') . ' ',
            'error' => get_string('errorprefix', 'tool_uploaduser') . ' ',
        ];
        foreach ($this->_row['status'] as $type => $content) {
            if (strlen($content)) {
                $this->output_line('  '.$prefix[$type].$content);
            }
        }

        foreach ($this->_row as $key => $field) {
            foreach ($field as $type => $content) {
                if ($key !== 'status' && $type !== 'normal' && strlen($content)) {
                    $this->output_line('  ' . $prefix[$type] . $this->headers[$key] . ': ' .
                        str_replace("\n", "\n".str_repeat(" ", strlen($prefix[$type] . $this->headers[$key]) + 4), $content));
                }
            }
        }
        foreach ($this->columns as $col) {
            $this->_row[$col] = ['normal' => '', 'info' => '', 'warning' => '', 'error' => ''];
        }
    }

    /**
     * Add tracking info
     * @param string $col name of column
     * @param string $msg message
     * @param string $level 'normal', 'warning' or 'error'
     * @param bool $merge true means add as new line, false means override all previous text of the same type
     * @return void
     */
    public function track($col, $msg, $level = 'normal', $merge = true) {
        if (empty($this->_row)) {
            $this->flush();
        }
        if (!in_array($col, $this->columns)) {
            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .= "\n";
            }
            $this->_row[$col][$level] .= $msg;
        } else {
            $this->_row[$col][$level] = $msg;
        }
    }

    /**
     * Print the table end
     * @return void
     */
    public function close() {
        $this->flush();
        $this->output_line(str_repeat('-', 79));
    }
}
