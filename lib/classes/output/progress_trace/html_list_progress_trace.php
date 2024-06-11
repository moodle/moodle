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
 * HTML List Progress Tree
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class html_list_progress_trace extends progress_trace {
    /** @var int */
    protected $currentdepth = -1;

    /**
     * Echo out the list
     *
     * @param string $message The message to display
     * @param int $depth
     * @return void Output is echoed
     */
    public function output($message, $depth = 0) {
        $samedepth = true;
        while ($this->currentdepth > $depth) {
            echo "</li>\n</ul>\n";
            $this->currentdepth -= 1;
            if ($this->currentdepth == $depth) {
                echo '<li>';
            }
            $samedepth = false;
        }
        while ($this->currentdepth < $depth) {
            echo "<ul>\n<li>";
            $this->currentdepth += 1;
            $samedepth = false;
        }
        if ($samedepth) {
            echo "</li>\n<li>";
        }
        echo htmlspecialchars($message, ENT_COMPAT);
        flush();
    }

    /**
     * Called when the processing is finished.
     */
    public function finished() {
        while ($this->currentdepth >= 0) {
            echo "</li>\n</ul>\n";
            $this->currentdepth -= 1;
        }
    }
}
