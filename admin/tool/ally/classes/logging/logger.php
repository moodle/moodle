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

namespace tool_ally\logging;

class logger {

    /**
     * Get the logger instance.
     * This could be adapted to return a different logger type instance depending on a config.
     */
    public static function get() {
        static $instance = null;
        if ($instance === null) {
            $instance = new loggerdb();
            $instance->setlevelrange();
        }
        return $instance;
    }
}
