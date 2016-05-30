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
 * JS and CSS compression.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Collection of JS and CSS compression methods.
 */
class core_minify {
    /**
     * Minify JS code.
     *
     * @param string $content
     * @return string minified JS code
     */
    public static function js($content) {
        global $CFG;
        require_once("$CFG->libdir/minify/lib/JSMinPlus.php");

        try {
            ob_start(); // JSMinPlus just echos errors, weird...
            $compressed = JSMinPlus::minify($content);
            if ($compressed !== false) {
                ob_end_clean();
                return $compressed;
            }
            $error = ob_get_clean();

        } catch (Exception $e) {
            ob_end_clean();
            $error = $e->getMessage();
        }

        $return = <<<EOD

try {console.log('Error: Minimisation of JavaScript failed!');} catch (e) {}

// Error: $error
// Problem detected during JavaScript minimisation, please review the following code
// =================================================================================


EOD;

        return $return.$content;
    }

    /**
     * Minify JS files.
     *
     * @param array $files
     * @return string minified JS code
     */
    public static function js_files(array $files) {
        if (empty($files)) {
            return '';
        }

        $compressed = array();
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                $compressed[] = "\n\n// Cannot read JS file ".basename(dirname(dirname($file))).'/'.basename(dirname($file)).'/'.basename($file)."\n\n";
                continue;
            }
            $compressed[] = self::js($content);
        }

        return implode(";\n", $compressed);
    }

    /**
     * Minify CSS code.
     *
     * @param string $content
     * @return string minified CSS
     */
    public static function css($content) {
        global $CFG;
        require_once("$CFG->libdir/minify/lib/Minify/CSS/Compressor.php");

        $error = 'unknown';
        try {
            $compressed = Minify_CSS_Compressor::process($content);
            if ($compressed !== false) {
                return $compressed;
            }

        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $return = <<<EOD

/* Error: $error */
/* Problem detected during CSS minimisation, please review the following code */
/* ========================================================================== */


EOD;

        return $return.$content;
    }

    /**
     * Minify CSS files.
     *
     * @param array $files
     * @return string minified CSS code
     */
    public static function css_files(array $files) {
        if (empty($files)) {
            return '';
        }

        $compressed = array();
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                $compressed[] = "\n\n/* Cannot read CSS file ".basename(dirname(dirname($file))).'/'.basename(dirname($file)).'/'.basename($file)."*/\n\n";
                continue;
            }
            $compressed[] = self::css($content);
        }

        return implode("\n", $compressed);
    }
}
