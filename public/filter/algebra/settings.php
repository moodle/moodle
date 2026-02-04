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
 * Algebra filter settings
 *
 * @package    filter_algebra
 * @copyright  2025 Yusuf Wibisono <yusuf.wibisono@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot . '/filter/algebra/lib.php');

    $items = [];
    $items[] = new admin_setting_heading('filter_algebra/latexheading', get_string('latexsettings', 'filter_algebra'), '');
    $items[] = new admin_setting_configtextarea(
        'filter_algebra/latexpreamble',
        get_string('latexpreamble', 'filter_algebra'),
        '',
        "\\usepackage[latin1]{inputenc}\n\\usepackage{amsmath}\n" .
        "\\usepackage{amsfonts}\n\\RequirePackage{amsmath,amssymb,latexsym}\n"
    );
    $items[] = new admin_setting_configcolourpicker(
        'filter_algebra/latexbackground',
        get_string('backgroundcolour', 'admin'),
        '',
        '#FFFFFF'
    );
    $items[] = new admin_setting_configtext('filter_algebra/density', get_string('density', 'admin'), '', '120', PARAM_INT);

    $defaultfilteralgebrapathlatex   = '';
    $defaultfilteralgebrapathdvips   = '';
    $defaultfilteralgebrapathdvisvgm = '';
    $defaultfilteralgebrapathconvert = '';
    if (PHP_OS == 'Linux') {
        $defaultfilteralgebrapathlatex   = "/usr/bin/latex";
        $defaultfilteralgebrapathdvips   = "/usr/bin/dvips";
        $defaultfilteralgebrapathdvisvgm = "/usr/bin/dvisvgm";
        $defaultfilteralgebrapathconvert = "/usr/bin/convert";
    } else if (PHP_OS == 'Darwin') {
        // Most likely needs a fink install (fink.sf.net).
        $defaultfilteralgebrapathlatex   = "/sw/bin/latex";
        $defaultfilteralgebrapathdvips   = "/sw/bin/dvips";
        $defaultfilteralgebrapathdvisvgm = "/usr/bin/dvisvgm";
        $defaultfilteralgebrapathconvert = "/sw/bin/convert";
    } else if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32' || PHP_OS == 'Windows') {
        // Note: you need Ghostscript installed (standard), miktex (standard)
        // and ImageMagick (install at c:\ImageMagick).
        $defaultfilteralgebrapathlatex   = "c:\\texmf\\miktex\\bin\\latex.exe";
        $defaultfilteralgebrapathdvips   = "c:\\texmf\\miktex\\bin\\dvips.exe";
        $defaultfilteralgebrapathdvisvgm   = "c:\\texmf\\miktex\\bin\\dvisvgm.exe";
        $defaultfilteralgebrapathconvert = "c:\\imagemagick\\convert.exe";
    }

    $pathlatex = get_config('filter_algebra', 'pathlatex');
    $pathdvips = get_config('filter_algebra', 'pathdvips');
    $pathconvert = get_config('filter_algebra', 'pathconvert');
    $pathdvisvgm = get_config('filter_algebra', 'pathdvisvgm');
    if (
        strrpos($pathlatex . $pathdvips . $pathconvert . $pathdvisvgm, '"') ||
        strrpos($pathlatex . $pathdvips . $pathconvert . $pathdvisvgm, "'")
    ) {
        set_config('pathlatex', trim($pathlatex, " '\""), 'filter_algebra');
        set_config('pathdvips', trim($pathdvips, " '\""), 'filter_algebra');
        set_config('pathconvert', trim($pathconvert, " '\""), 'filter_algebra');
        set_config('pathdvisvgm', trim($pathdvisvgm, " '\""), 'filter_algebra');
    }

    $items[] = new admin_setting_configexecutable(
        'filter_algebra/pathlatex',
        get_string('pathlatex', 'filter_algebra'),
        '',
        $defaultfilteralgebrapathlatex
    );
    $items[] = new admin_setting_configexecutable(
        'filter_algebra/pathdvips',
        get_string('pathdvips', 'filter_algebra'),
        '',
        $defaultfilteralgebrapathdvips
    );
    $items[] = new admin_setting_configexecutable(
        'filter_algebra/pathconvert',
        get_string('pathconvert', 'filter_algebra'),
        '',
        $defaultfilteralgebrapathconvert
    );
    $items[] = new admin_setting_configexecutable(
        'filter_algebra/pathdvisvgm',
        get_string('pathdvisvgm', 'filter_algebra'),
        '',
        $defaultfilteralgebrapathdvisvgm
    );

    // The update callback checks whether required paths actually point to executables.
    // If they don't, we force the setting to PNG as the default fallback format.
    $formats = ['png' => 'PNG', 'gif' => 'GIF', 'svg' => 'SVG'];
    $items[] = new admin_setting_configselect(
        'filter_algebra/convertformat',
        get_string('convertformat', 'filter_algebra'),
        get_string('configconvertformat', 'filter_algebra'),
        'png',
        $formats
    );

    foreach ($items as $item) {
        $item->set_updatedcallback('filter_algebra_updatedcallback');
        $settings->add($item);
    }
}
