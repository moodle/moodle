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

defined('MOODLE_INTERNAL') || die();

interface post_grades_return {
    public function is_ready();
}

interface post_grades_return_header {
    public function get_explanation();
}

interface post_grades_return_process
    extends post_grades_return, post_grades_return_header {

    public function process();

    public function get_url($processed);
}

class post_grades_good_return implements post_grades_return {
    public function is_ready() {
        return true;
    }
}

abstract class post_grades_delegating_return implements post_grades_return_process {
    public function __construct($base_return) {
        $this->base_return = $base_return;
    }

    public function get_explanation() {
        return $this->base_return->get_explanation();
    }

    public function get_url($processed) {
        if (empty($processed)) {
            $processed = $this->base_return->process();
        }
        return $this->base_return->get_url($processed);
    }

}
