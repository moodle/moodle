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
 * Provides \tool_pluginskel\local\util\index_helper class
 *
 * @package     tool_pluginskel
 * @subpackage  util
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\util;

use Monolog\Logger;
use Monolog\Handler\BrowserConsoleHandler;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class for index.php.
 *
 * @copyright 2016 Alexandru Elisei <alexandru.elisei@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class index_helper {

    /**
     * Returns the number of values for each variable array by examining the recipe.
     *
     * @param string[] $templatevars The template variables.
     * @param string[] $recipe
     * @param string $countprefix The prefix for the variable that will hold the number of values for the variables.
     * @return string[]
     */
    public static function get_array_variable_count_from_recipe($templatevars, $recipe, $countprefix = '') {

        $variablecount = array();

        foreach ($templatevars as $variable) {
            if ($variable['type'] == 'numeric-array') {

                $variablename = $variable['name'];

                if (empty($recipe[$variablename])) {
                    $recipevalues = array();
                    $count = 1;
                } else {
                    $recipevalues = $recipe[$variablename];
                    $count = count($recipevalues);
                }

                if (empty($countprefix)) {
                    $countname = $variablename.'count';
                } else {
                    $countname = $countprefix.'_'.$variablename.'count';
                }

                $variablecount[$countname] = $count;

                if (empty($recipevalues)) {
                    continue;
                }

                foreach ($variable['values'] as $nestedvariable) {
                    if ($nestedvariable['type'] == 'numeric-array') {
                        for ($i = 0; $i < $count; $i += 1) {
                            $nestedvariablecount = self::get_array_variable_count_from_recipe($variable['values'],
                                                                                              $recipevalues[$i],
                                                                                              $countprefix);
                            if (empty($countprefix)) {
                                $nestedcountname = $variablename.'_'.$i.'_'.$nestedvariable['name'].'count';
                            } else {
                                $nestedcountname = $countprefix.'_'.$variablename.'_'.$i.'_'.$nestedvariable['name'].'count';
                            }
                            $variablecount[$nestedcountname] = $nestedvariablecount[$nestedvariable['name'].'count'];
                        }
                    }
                }

            } else if ($variable['type'] === 'associative-array') {
                // Associative arrays can have nested numerically indexed array variables.
                foreach ($variable['values'] as $nestedvariable) {
                    if ($nestedvariable['type'] === 'numeric-array') {

                        if (empty($recipe[$variable['name']][$nestedvariable['name']])) {
                            $count = 1;
                        } else {
                            $count = count($recipe[$variable['name']][$nestedvariable['name']]);
                        }

                        if (empty($countprefix)) {
                            $countname = $variable['name'].'_'.$nestedvariable['name'].'count';
                        } else {
                            $countname = $countprefix.'_'.$variable['name'].'_'.$nestedvariable['name'].'count';
                        }
                        $variablecount[$countname] = $count;
                    }
                }
            }
        }

        return $variablecount;
    }

    /**
     * Returns the number of values for each variable array by examining the form.
     *
     * @param string[] $templatevars The template variables.
     * @param string $countprefix The prefix for the form count variable that has the number of values.
     * @return string[]
     */
    public static function get_array_variable_count_from_form($templatevars, $countprefix = '') {

        $variablecount = array();

        foreach ($templatevars as $variable) {
            if ($variable['type'] === 'numeric-array') {

                $variablename = $variable['name'];

                if (empty($countprefix)) {
                    $countname = $variablename.'count';
                } else {
                    $countname = $countprefix.'_'.$variablename.'count';
                }

                $count = (int) optional_param($countname, 1, PARAM_INT);
                $variablecount[$countname] = $count;

                foreach ($variable['values'] as $nestedvariable) {
                    if ($nestedvariable['type'] === 'numeric-array') {
                        for ($i = 0; $i < $count; $i += 1) {

                            if (empty($parentname)) {
                                $nestedcountname = $variablename.'_'.$i.'_'.$nestedvariable['name'].'count';
                            } else {
                                $nestedcountname = $countprefix.'_'.$variablename.'_'.$i.'_'.$nestedvariable['name'].'count';
                            }

                            $count = (int) optional_param($nestedcountname, 1, PARAM_INT);
                            $variablecount[$nestedcountname] = $count;
                        }
                    }
                }

            } else if ($variable['type'] === 'associative-array') {
                foreach ($variable['values'] as $nestedvariable) {
                    if ($nestedvariable['type'] === 'numeric-array') {

                        if (empty($countprefix)) {
                            $countname = $variable['name'].'_'.$nestedvariable['name'].'count';
                        } else {
                            $countname = $countprefix.'_'.$variable['name'].'_'.$nestedvariable['name'].'count';
                        }

                        $count = (int) optional_param($countname, 1, PARAM_INT);
                        $variablecount[$countname] = $count;
                    }
                }
            }
        }

        return $variablecount;
    }


    /**
     * Generates the download header.
     *
     * @param string $filename
     * @param int $contentlength
     */
    public static function generate_download_header($filename, $contentlength) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.$contentlength);
    }

    /**
     * Downloads the recipe.
     *
     * @param string $recipestring The recipe is a YAML string.
     */
    public static function download_recipe($recipestring) {

        $filename = 'recipe_'.time().'.yaml';
        $contentlength = strlen($recipestring);

        self::generate_download_header($filename, $contentlength);
        echo($recipestring);
    }

    /**
     * Downloads the plugin skeleton as a zip file.
     *
     * @param string[] $recipe
     */
    public static function download_plugin_skeleton($recipe) {

        $logger = new Logger('tool_pluginskel');
        $logger->pushHandler(new BrowserConsoleHandler(Logger::WARNING));

        $manager = manager::instance($logger);
        $manager->load_recipe($recipe);
        $manager->make();

        $tempdir = make_request_directory();
        $targetdir = $tempdir.'/src';
        $manager->write_files($targetdir);

        $generatedfiles = $manager->get_files_content();

        $component = $recipe['component'];
        list($componenttype, $componentname) = \core_component::normalize_component($component);
        $zipfiles = array();
        foreach ($generatedfiles as $filename => $notused) {
            $zipfiles[$componentname.'/'.$filename] = $targetdir.'/'.$filename;
        }

        $packer = get_file_packer('application/zip');
        $archivefile = $tempdir.'/'.$component.'_'.time().'.zip';
        $packer->archive_to_pathname($zipfiles, $archivefile);

        $filename = basename($archivefile);
        $contentlength = filesize($archivefile);

        self::generate_download_header($filename, $contentlength);
        readfile($archivefile);
        exit();
    }
}
