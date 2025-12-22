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
 * TeX filter library functions.
 *
 * @package    filter
 * @subpackage tex
 * @copyright  2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu
 *             Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Check the formula expression against the list of denied keywords.
 *
 * List of allowed could be more complete but also harder to maintain.
 *
 * @param string $texexp Formula expression to check.
 * @return string Formula expression with denied keywords replaced with 'forbiddenkeyword'.
 */
function filter_tex_sanitize_formula(string $texexp): string {

    $denylist = [
        'include', 'command', 'loop', 'repeat', 'open', 'toks', 'output',
        'input', 'catcode', 'name', '^^',
        '\def', '\edef', '\gdef', '\xdef',
        '\every', '\errhelp', '\errorstopmode', '\scrollmode', '\nonstopmode',
        '\batchmode', '\read', '\write', 'csname', '\newhelp', '\uppercase',
        '\lowercase', '\relax', '\aftergroup',
        '\afterassignment', '\expandafter', '\noexpand', '\special',
        '\let', '\futurelet', '\else', '\fi', '\chardef', '\makeatletter', '\afterground',
        '\noexpand', '\line', '\mathcode', '\item', '\section', '\mbox', '\declarerobustcommand',
        '\ExplSyntaxOn', '\pdffiledump', '\mathtex',
    ];

    $allowlist = ['inputenc'];

    // Add encoded backslash (&#92;) versions of backslashed items to deny list.
    $encodedslashdenylist = array_map(function($value) {
        $encoded = str_replace('\\', '&#92;', $value);
        // Return an encoded slash version if a slash is found, otherwise null so we can filter it off.
        return $encoded != $value ? $encoded : null;
    }, $denylist);
    $encodedslashdenylist = array_filter($encodedslashdenylist);
    $denylist = array_merge($denylist, $encodedslashdenylist);

    // Prepare the denylist for regular expression.
    $denylist = array_map(function($value){
        return '/' . preg_quote($value, '/') . '/i';
    }, $denylist);

    // Prepare the allowlist for regular expression.
    $allowlist = array_map(function($value){
        return '/\bforbiddenkeyword_(' . preg_quote($value, '/') . ')\b/i';
    }, $allowlist);

    // First, mangle all denied words.
    $texexp = preg_replace_callback($denylist,
        function($matches) {
            // Remove backslashes to make commands impotent.
            $noslashes = str_replace('\\', '', $matches[0]);
            return 'forbiddenkeyword_' . $noslashes;
        },
        $texexp
    );

    // Then, change back the allowed words.
    $texexp = preg_replace_callback($allowlist,
        function($matches) {
            return $matches[1];
        },
        $texexp
    );

    return $texexp;
}

/**
 * Purge all caches when settings changed.
 */
function filter_tex_updatedcallback($name) {
    global $CFG, $DB;
    reset_text_filters_cache();

    if (file_exists("$CFG->dataroot/filter/tex")) {
        remove_dir("$CFG->dataroot/filter/tex");
    }
    if (file_exists("$CFG->tempdir/latex")) {
        remove_dir("$CFG->tempdir/latex");
    }

    $DB->delete_records('cache_filters', array('filter'=>'tex'));

    $pathlatex = get_config('filter_tex', 'pathlatex');
    if ($pathlatex === false) {
        // detailed settings not present yet
        return;
    }

    $pathlatex = trim($pathlatex, " '\"");
    $pathdvips = trim(get_config('filter_tex', 'pathdvips'), " '\"");
    $pathconvert = trim(get_config('filter_tex', 'pathconvert'), " '\"");
    $pathdvisvgm = trim(get_config('filter_tex', 'pathdvisvgm'), " '\"");

    $supportedformats = [];
    if ((is_file($pathlatex) && is_executable($pathlatex)) &&
            (is_file($pathdvips) && is_executable($pathdvips))) {
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
    if (!in_array(get_config('filter_tex', 'convertformat'), $supportedformats)) {
        set_config('convertformat', $supportedformats[0], 'filter_tex');
    }

}

