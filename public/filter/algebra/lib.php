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
 * Algebra filter library functions.
 *
 * @package    filter_algebra
 * @copyright  2025 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Purge all caches when settings changed.
 *
 * @param string $name The name of the setting that was changed.
 */
function filter_algebra_updatedcallback($name) {
    global $CFG, $DB;
    reset_text_filters_cache();

    if (file_exists("$CFG->dataroot/filter/algebra")) {
        remove_dir("$CFG->dataroot/filter/algebra");
    }
    if (file_exists("$CFG->tempdir/latex")) {
        remove_dir("$CFG->tempdir/latex");
    }

    $DB->delete_records('cache_filters', ['filter' => 'algebra']);

    $pathlatex = get_config('filter_algebra', 'pathlatex');
    if ($pathlatex === false) {
        return;
    }

    $pathlatex = trim($pathlatex, " '\"");
    $pathdvips = trim(get_config('filter_algebra', 'pathdvips'), " '\"");
    $pathconvert = trim(get_config('filter_algebra', 'pathconvert'), " '\"");
    $pathdvisvgm = trim(get_config('filter_algebra', 'pathdvisvgm'), " '\"");

    $supportedformats = [];
    if (
        (is_file($pathlatex) && is_executable($pathlatex)) &&
        (is_file($pathdvips) && is_executable($pathdvips))
    ) {
        if (is_file($pathconvert) && is_executable($pathconvert)) {
             $supportedformats[] = 'png';
             $supportedformats[] = 'gif';
        }
        if (is_file($pathdvisvgm) && is_executable($pathdvisvgm)) {
             $supportedformats[] = 'svg';
        }
    }
    // If no formats are supported, default to PNG (even if tools aren't available, admin can configure later).
    if (empty($supportedformats)) {
        $supportedformats[] = 'png';
    }
    if (!in_array(get_config('filter_algebra', 'convertformat'), $supportedformats)) {
        set_config('convertformat', $supportedformats[0], 'filter_algebra');
    }
}
