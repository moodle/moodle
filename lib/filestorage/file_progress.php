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
 * Simple interface for receiving progress during long-running file
 * operations.
 *
 * In some cases progress can be reported precisely. In other cases,
 * progress is indeterminate which means that the progress function is called
 * periodically but without information on completion.
 *
 * @package core_files
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface file_progress {
    /**
     * @var int Constant used for indeterminate progress.
     */
    const INDETERMINATE = -1;

    /**
     * Called during a file processing operation that reports progress.
     *
     * This function will be called periodically during the operation, assuming
     * it is successful.
     *
     * If numbers (rather than INDETERMINATE) are provided, then:
     * - The $progress value will either be the same as last call, or increased
     *   by some value (not necessarily 1).
     * - The $progress value will be less than or equal to the $max value.
     *
     * There is no guarantee that this function will be called for every value
     * in the range, or that it will be called with $progress == $max.
     *
     * @param int $progress Current progress, or INDETERMINATE if unknown
     * @param int $max Max progress, or INDETERMINATE if unknown
     */
    public function progress($progress = self::INDETERMINATE, $max = self::INDETERMINATE);
}
