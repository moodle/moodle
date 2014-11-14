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
 * pdflib.php - Moodle PDF library
 *
 * We currently use the TCPDF library by Nicola Asuni.
 *
 * The default location for fonts that are included with TCPDF is
 * lib/tcpdf/fonts/. If PDF_CUSTOM_FONT_PATH exists, this directory
 * will be used instead of lib/tcpdf/fonts/, the default location is
 * $CFG->dataroot.'/fonts/'.
 *
 * You should always copy all fonts from lib/tcpdf/fonts/ to your
 * PDF_CUSTOM_FONT_PATH and then add extra fonts. Alternatively
 * you may download all TCPDF fonts from http://www.tcpdf.org/download.php
 * and extract them to PDF_CUSTOM_FONT_PATH directory.
 *
 * You can specify your own default font family in config.php
 * by defining PDF_DEFAULT_FONT constant there.
 *
 * If you want to add True Type fonts such as "Arial Unicode MS",
 * you need to create a simple script and then execute it, it should add
 * new file to your fonts directory:
 * <code>
 *   <?php
 *   require('config.php');
 *   require_once($CFG->libdir . '/pdflib.php');
 *   TCPDF_FONTS::addTTFfont('/full_path_to/ARIALUNI.TTF', 'TrueTypeUnicode');
 * </code>
 * This script will convert the TTF file to format compatible with TCPDF.
 *
 * Please note you need to have appropriate license to use the font files on your server!
 *
 * Example usage:
 * <code>
 *    $doc = new pdf;
 *    $doc->setPrintHeader(false);
 *    $doc->setPrintFooter(false);
 *    $doc->AddPage();
 *    $doc->Write(5, 'Hello World!');
 *    $doc->Output();
 * </code>
 *
 * @package     moodlecore
 * @copyright   Vy-Shane Sin Fat
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!defined('PDF_CUSTOM_FONT_PATH')) {
    /** Defines the site-specific location of fonts. */
    define('PDF_CUSTOM_FONT_PATH', $CFG->dataroot.'/fonts/');
}

if (!defined('PDF_DEFAULT_FONT')) {
    /** Default font to be used. */
    define('PDF_DEFAULT_FONT', 'FreeSerif');
}

/** tell tcpdf it is configured here instead of in its own config file */
define('K_TCPDF_EXTERNAL_CONFIG', 1);

// The configuration constants needed by tcpdf follow

/**
 * Init K_PATH_FONTS and PDF_FONT_NAME_MAIN constant.
 *
 * Unfortunately this hack is necessary because the constants need
 * to be defined before inclusion of the tcpdf.php file.
 */
function tcpdf_init_k_font_path() {
    global $CFG;

    $defaultfonts = $CFG->dirroot.'/lib/tcpdf/fonts/';

    if (!defined('K_PATH_FONTS')) {
        if (is_dir(PDF_CUSTOM_FONT_PATH)) {
            // NOTE:
            //   There used to be an option to have just one file and having it set as default
            //   but that does not make sense any more because add-ons using standard fonts
            //   would fail very badly, also font families consist of multiple php files for
            //   regular, bold, italic, etc.

            // Check for some standard font files if present and if not do not use the custom path.
            $somestandardfiles = array('courier',  'helvetica', 'times', 'symbol', 'zapfdingbats', 'freeserif', 'freesans');
            $missing = false;
            foreach ($somestandardfiles as $file) {
                if (!file_exists(PDF_CUSTOM_FONT_PATH . $file . '.php')) {
                    $missing = true;
                    break;
                }
            }
            if ($missing) {
                define('K_PATH_FONTS', $defaultfonts);
            } else {
                define('K_PATH_FONTS', PDF_CUSTOM_FONT_PATH);
            }
        } else {
            define('K_PATH_FONTS', $defaultfonts);
        }
    }

    if (!defined('PDF_FONT_NAME_MAIN')) {
        define('PDF_FONT_NAME_MAIN', strtolower(PDF_DEFAULT_FONT));
    }
}
tcpdf_init_k_font_path();

/** tcpdf installation path */
define('K_PATH_MAIN', $CFG->dirroot.'/lib/tcpdf/');

/** URL path to tcpdf installation folder */
define('K_PATH_URL', $CFG->wwwroot . '/lib/tcpdf/');

/** cache directory for temporary files (full path) */
define('K_PATH_CACHE', $CFG->cachedir . '/tcpdf/');

/** images directory */
define('K_PATH_IMAGES', $CFG->dirroot . '/');

/** blank image */
define('K_BLANK_IMAGE', K_PATH_IMAGES . 'pix/spacer.gif');

/** height of cell repect font height */
define('K_CELL_HEIGHT_RATIO', 1.25);

/** reduction factor for small font */
define('K_SMALL_RATIO', 2/3);

/** Throw exceptions from errors so they can be caught and recovered from. */
define('K_TCPDF_THROW_EXCEPTION_ERROR', true);

require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');

/**
 * Wrapper class that extends TCPDF (lib/tcpdf/tcpdf.php).
 * Moodle customisations are done here.
 *
 * @package     moodlecore
 * @copyright   Vy-Shane Sin Fat
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdf extends TCPDF {

    /**
     * Class constructor
     *
     * See the parent class documentation for the parameters info.
     */
    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8') {
        make_cache_directory('tcpdf');

        parent::__construct($orientation, $unit, $format, $unicode, $encoding);

        // theses replace the tcpdf's config/lang/ definitions
        $this->l['w_page']          = get_string('page');
        $this->l['a_meta_language'] = current_language();
        $this->l['a_meta_charset']  = 'UTF-8';
        $this->l['a_meta_dir']      = get_string('thisdirection', 'langconfig');
    }

    /**
     * Send the document to a given destination: string, local file or browser.
     * In the last case, the plug-in may be used (if present) or a download ("Save as" dialog box) may be forced.<br />
     * The method first calls Close() if necessary to terminate the document.
     * @param $name (string) The name of the file when saved. Note that special characters are removed and blanks characters are replaced with the underscore character.
     * @param $dest (string) Destination where to send the document. It can take one of the following values:<ul><li>I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.</li><li>D: send to the browser and force a file download with the name given by name.</li><li>F: save to a local server file with the name given by name.</li><li>S: return the document as a string (name is ignored).</li><li>FI: equivalent to F + I option</li><li>FD: equivalent to F + D option</li><li>E: return the document as base64 mime multi-part email attachment (RFC 2045)</li></ul>
     * @public
     * @since Moodle 1.0
     * @see Close()
     */
    public function Output($name='doc.pdf', $dest='I') {
        $olddebug = error_reporting(0);
        $result  = parent::output($name, $dest);
        error_reporting($olddebug);
        return $result;
    }

    /**
     * Is this font family one of core fonts?
     * @param string $fontfamily
     * @return bool
     */
    public function is_core_font_family($fontfamily) {
        return isset($this->CoreFonts[$fontfamily]);
    }

    /**
     * Returns list of font families and types of fonts.
     *
     * @return array multidimensional array with font families as keys and B, I, BI and N as values.
     */
    public function get_font_families() {
        $families = array();
        foreach ($this->fontlist as $font) {
            if (strpos($font, 'uni2cid') === 0) {
                // This is not an font file.
                continue;
            }
            if (strpos($font, 'cid0') === 0) {
                // These do not seem to work with utf-8, better ignore them for now.
                continue;
            }
            if (substr($font, -2) === 'bi') {
                $family = substr($font, 0, -2);
                if (in_array($family, $this->fontlist)) {
                    $families[$family]['BI'] = 'BI';
                    continue;
                }
            }
            if (substr($font, -1) === 'i') {
                $family = substr($font, 0, -1);
                if (in_array($family, $this->fontlist)) {
                    $families[$family]['I'] = 'I';
                    continue;
                }
            }
            if (substr($font, -1) === 'b') {
                $family = substr($font, 0, -1);
                if (in_array($family, $this->fontlist)) {
                    $families[$family]['B'] = 'B';
                    continue;
                }
            }
            // This must be a Family or incomplete set of fonts present.
            $families[$font]['R'] = 'R';
        }

        // Sort everything consistently.
        ksort($families);
        foreach ($families as $k => $v) {
            krsort($families[$k]);
        }

        return $families;
    }
}
