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
 * Process binding username claim tool upload process tracker.
 *
 * @package auth_oidc
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2023 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace auth_oidc;

use html_writer;

/**
 * Class to track the progress of processing username claims uploads.
 */
class upload_process_tracker {
    /** @var array */
    protected $_row;
    /** @var array */
    public $columns = [];
    /** @var array */
    protected $headers = [];

    /**
     * upload_process_tracker constructor.
     */
    public function __construct() {
        $this->headers = [
            'status' => get_string('status'),
            'line' => get_string('csvline', 'auth_oidc'),
            'id' => 'ID',
            'username' => get_string('username'),
            'new_username' => get_string('new_username', 'auth_oidc'),
        ];
        $this->columns = array_keys($this->headers);
    }

    /**
     * Start the tracker.
     *
     * @return void
     */
    public function start() {
        $ci = 0;
        echo html_writer::start_tag('table', ['class' => 'generaltable boxaligncenter flexible-wrap',
            'summary' => get_string('update_username_results', 'auth_oidc')]);
        echo html_writer::start_tag('tr', ['class' => 'heading r0']);
        foreach ($this->headers as $header) {
            echo html_writer::tag('th', $header, ['class' => 'header c' . $ci++, 'scope' => 'col']);
        }
        echo html_writer::end_tag('tr');
        $this->_row = null;
    }

    /**
     * Flush the tracker.
     *
     * @return void
     */
    public function flush() {
        if (empty($this->_row) || empty($this->_row['line']['normal'])) {
            // Nothing to print - each line has to have at least number.
            $this->_row = [];
            foreach ($this->columns as $col) {
                $this->_row[$col] = ['normal' => '', 'info' => '', 'warning' => '', 'error' => ''];
            }

            return;
        }
        $ci = 0;
        $ri = 1;
        echo '<tr class="r' . $ri . '">';
        foreach ($this->_row as $key => $field) {
            foreach ($field as $type => $content) {
                if ($field[$type] !== '') {
                    $field[$type] = '<span class="uu' . $type . '">' . $field[$type] . '</span>';
                } else {
                    unset($field[$type]);
                }
            }
            echo '<td class="cell c' . $ci++ . '">';
            if (!empty($field)) {
                echo implode('<br />', $field);
            } else {
                echo '&nbsp;';
            }
            echo '</td>';
        }
        echo '</tr>';
        foreach ($this->columns as $col) {
            $this->_row[$col] = ['normal' => '', 'info' => '', 'warning' => '', 'error' => ''];
        }
    }

    /**
     * Track a message.
     *
     * @param string $col
     * @param string $msg
     * @param string $level
     * @param bool $merge
     * @return void
     */
    public function track($col, $msg, $level = 'normal', $merge = true) {
        if (empty($this->_row)) {
            $this->flush();
        }
        if (!in_array($col, $this->columns)) {
            debugging('Incorrect column:' . $col);

            return;
        }
        if ($merge) {
            if ($this->_row[$col][$level] != '') {
                $this->_row[$col][$level] .= '<br />';
            }
            $this->_row[$col][$level] .= $msg;
        } else {
            $this->_row[$col][$level] = $msg;
        }
    }

    /**
     * Close the tracker.
     *
     * @return void
     */
    public function close() {
        $this->flush();
        echo html_writer::end_tag('table');
    }
}
