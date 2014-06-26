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

function filter_tex_get_executable($debug=false) {
    global $CFG;

    $error_message1 = "Your system is not configured to run mimeTeX. You need to download the appropriate<br />"
                     ."executable for you ".PHP_OS." platform from <a href=\"http://moodle.org/download/mimetex/\">"
                     ."http://moodle.org/download/mimetex/</a>, or obtain the C source<br /> "
                     ."from <a href=\"http://www.forkosh.com/mimetex.zip\">"
                     ."http://www.forkosh.com/mimetex.zip</a>, compile it and "
                     ."put the executable into your<br /> moodle/filter/tex/ directory.";

    $error_message2 = "Custom mimetex is not executable!<br /><br />";

    if ((PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows")) {
        return "$CFG->dirroot/filter/tex/mimetex.exe";
    }

    $custom_commandpath = "$CFG->dirroot/filter/tex/mimetex";
    if (file_exists($custom_commandpath)) {
        if (is_executable($custom_commandpath)) {
            return $custom_commandpath;
        } else {
            print_error('mimetexnotexecutable', 'error');
        }
    }

    switch (PHP_OS) {
        case "Linux":   return "$CFG->dirroot/filter/tex/mimetex.linux";
        case "Darwin":  return "$CFG->dirroot/filter/tex/mimetex.darwin";
        case "FreeBSD": return "$CFG->dirroot/filter/tex/mimetex.freebsd";
    }

    print_error('mimetexisnotexist', 'error');
}

function filter_tex_sanitize_formula($texexp) {
    /// Check $texexp against blacklist (whitelisting could be more complete but also harder to maintain)
    $tex_blacklist = array(
        'include','command','loop','repeat','open','toks','output',
        'input','catcode','name','^^',
        '\def','\edef','\gdef','\xdef',
        '\every','\errhelp','\errorstopmode','\scrollmode','\nonstopmode',
        '\batchmode','\read','\write','csname','\newhelp','\uppercase',
        '\lowercase','\relax','\aftergroup',
        '\afterassignment','\expandafter','\noexpand','\special',
        '\let', '\futurelet','\else','\fi','\chardef','\makeatletter','\afterground',
        '\noexpand','\line','\mathcode','\item','\section','\mbox','\declarerobustcommand'
    );

    return  str_ireplace($tex_blacklist, 'forbiddenkeyword', $texexp);
}

function filter_tex_get_cmd($pathname, $texexp) {
    $texexp = filter_tex_sanitize_formula($texexp);
    $texexp = escapeshellarg($texexp);
    $executable = filter_tex_get_executable(false);

    if ((PHP_OS == "WINNT") || (PHP_OS == "WIN32") || (PHP_OS == "Windows")) {
        $executable = str_replace(' ', '^ ', $executable);
        return "$executable ++ -e  \"$pathname\" -- $texexp";

    } else {
        return "\"$executable\" -e \"$pathname\" -- $texexp";
    }
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
    if (file_exists("$CFG->dataroot/filter/algebra")) {
        remove_dir("$CFG->dataroot/filter/algebra");
    }
    if (file_exists("$CFG->tempdir/latex")) {
        remove_dir("$CFG->tempdir/latex");
    }

    $DB->delete_records('cache_filters', array('filter'=>'tex'));
    $DB->delete_records('cache_filters', array('filter'=>'algebra'));

    if (!isset($CFG->filter_tex_pathlatex)) {
        // detailed settings not present yet
        return;
    }

    if (!(is_file($CFG->filter_tex_pathlatex) && is_executable($CFG->filter_tex_pathlatex) &&
          is_file($CFG->filter_tex_pathdvips) && is_executable($CFG->filter_tex_pathdvips) &&
          is_file($CFG->filter_tex_pathconvert) && is_executable($CFG->filter_tex_pathconvert))) {
        // LaTeX, dvips or convert are not available, and mimetex can only produce GIFs so...
        set_config('filter_tex_convertformat', 'gif');
    }
}


