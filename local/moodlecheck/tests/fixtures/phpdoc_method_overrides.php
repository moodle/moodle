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

interface dummy_top_level {
    // Should be an error.
    public function undocumented();
}

class dummy_implements_something implements dummy_top_level {
    // Should be a warning.
    public function undocumented() {}
}

class dummy_extends_something extends dummy_implements_something {
    // Should be a warning.
    public function undocumented() {}
}

class dummy_extends_and_implements_something extends dummy_implements_something implements dummy_top_level {
    // Should be a warning.
    public function undocumented() {}
}
