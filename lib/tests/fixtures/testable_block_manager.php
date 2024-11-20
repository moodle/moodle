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
 * Test-specific subclass to make some protected things public.
 *
 * @package   core
 * @category  test
 * @copyright 2022 onwards Eloy Lafuente (stronk7) {@link https://stronk7.com}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_block_manager extends block_manager {
    /**
     * Resets the caches in the block manager.
     * This allows blocks to be reloaded correctly.
     */
    public function reset_caches() {
        $this->birecordsbyregion = null;
        $this->blockinstances = array();
        $this->visibleblockcontent = array();
    }
    public function mark_loaded() {
        $this->birecordsbyregion = array();
    }
    public function get_loaded_blocks() {
        return $this->birecordsbyregion;
    }
}
