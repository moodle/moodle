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
 * TeX filter settings
 *
 * @package    filter
 * @subpackage tex
 * @copyright  2007 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot.'/filter/tex/lib.php');

    $items = array();
    $items[] = new admin_setting_heading('filter_tex/latexheading', get_string('latexsettings', 'filter_tex'), '');
    $items[] = new admin_setting_configtextarea('filter_tex/latexpreamble', get_string('latexpreamble','filter_tex'),
                   '', "\\usepackage[latin1]{inputenc}\n\\usepackage{amsmath}\n\\usepackage{amsfonts}\n\\RequirePackage{amsmath,amssymb,latexsym}\n");
    $items[] = new admin_setting_configtext('filter_tex/latexbackground', get_string('backgroundcolour', 'admin'), '', '#FFFFFF');
    $items[] = new admin_setting_configtext('filter_tex/density', get_string('density', 'admin'), '', '120', PARAM_INT);

    if (PHP_OS=='Linux') {
        $default_filter_tex_pathlatex   = "/usr/bin/latex";
        $default_filter_tex_pathdvips   = "/usr/bin/dvips";
        $default_filter_tex_pathconvert = "/usr/bin/convert";

    } else if (PHP_OS=='Darwin') {
        // most likely needs a fink install (fink.sf.net)
        $default_filter_tex_pathlatex   = "/sw/bin/latex";
        $default_filter_tex_pathdvips   = "/sw/bin/dvips";
        $default_filter_tex_pathconvert = "/sw/bin/convert";

    } else if (PHP_OS=='WINNT' or PHP_OS=='WIN32' or PHP_OS=='Windows') {
        // note: you need Ghostscript installed (standard), miktex (standard)
        // and ImageMagick (install at c:\ImageMagick)
        $default_filter_tex_pathlatex   = "\"c:\\texmf\\miktex\\bin\\latex.exe\" ";
        $default_filter_tex_pathdvips   = "\"c:\\texmf\\miktex\\bin\\dvips.exe\" ";
        $default_filter_tex_pathconvert = "\"c:\\imagemagick\\convert.exe\" ";

    } else {
        $default_filter_tex_pathlatex   = '';
        $default_filter_tex_pathdvips   = '';
        $default_filter_tex_pathconvert = '';
    }

    $items[] = new admin_setting_configexecutable('filter_tex/pathlatex', get_string('pathlatex', 'filter_tex'), '', $default_filter_tex_pathlatex);
    $items[] = new admin_setting_configexecutable('filter_tex/pathdvips', get_string('pathdvips', 'filter_tex'), '', $default_filter_tex_pathdvips);
    $items[] = new admin_setting_configexecutable('filter_tex/pathconvert', get_string('pathconvert', 'filter_tex'), '', $default_filter_tex_pathconvert);
    $items[] = new admin_setting_configexecutable('filter_tex/pathmimetex', get_string('pathmimetex', 'filter_tex'), get_string('pathmimetexdesc', 'filter_tex'), '');

    // Even if we offer GIF and PNG formats here, in the update callback we check whether
    // all the paths actually point to executables. If they don't, we force the setting
    // to GIF, as that's the only format mimeTeX can produce.
    $formats = array('gif' => 'GIF', 'png' => 'PNG');
    $items[] = new admin_setting_configselect('filter_tex/convertformat', get_string('convertformat', 'filter_tex'), get_string('configconvertformat', 'filter_tex'), 'gif', $formats);

    foreach ($items as $item) {
        $item->set_updatedcallback('filter_tex_updatedcallback');
        $settings->add($item);
    }
}
