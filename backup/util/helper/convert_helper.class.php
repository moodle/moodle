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
 * Provides {@link convert_helper} and {@link convert_helper_exception} classes
 *
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/includes/convert_includes.php');

/**
 * Provides various functionality via its static methods
 */
abstract class convert_helper {

    /**
     * @param string $entropy
     * @return string random identifier
     */
    public static function generate_id($entropy) {
        return md5(time() . '-' . $entropy . '-' . random_string(20));
    }

    /**
     * Returns the list of all available converters and loads their classes
     *
     * Converter must be installed as a directory in backup/converter/ and its
     * method is_available() must return true to get to the list.
     *
     * @see base_converter::is_available()
     * @return array of strings
     */
    public static function available_converters($restore=true) {
        global $CFG;

        $converters = array();

        $plugins    = get_list_of_plugins('backup/converter');
        foreach ($plugins as $name) {
            $filename = $restore ? 'lib.php' : 'backuplib.php';
            $classuf  = $restore ? '_converter' : '_export_converter';
            $classfile = "{$CFG->dirroot}/backup/converter/{$name}/{$filename}";
            $classname = "{$name}{$classuf}";
            $zip_contents      = "{$name}_zip_contents";
            $store_backup_file = "{$name}_store_backup_file";
            $convert           = "{$name}_backup_convert";

            if (!file_exists($classfile)) {
                throw new convert_helper_exception('converter_classfile_not_found', $classfile);
            }

            require_once($classfile);

            if (!class_exists($classname)) {
                throw new convert_helper_exception('converter_classname_not_found', $classname);
            }

            if (call_user_func($classname .'::is_available')) {
                if (!$restore) {
                    if (!class_exists($zip_contents)) {
                        throw new convert_helper_exception('converter_classname_not_found', $zip_contents);
                    }
                    if (!class_exists($store_backup_file)) {
                        throw new convert_helper_exception('converter_classname_not_found', $store_backup_file);
                    }
                    if (!class_exists($convert)) {
                        throw new convert_helper_exception('converter_classname_not_found', $convert);
                    }
                }

                $converters[] = $name;
            }

        }

        return $converters;
    }

    public static function export_converter_dependencies($converter, $dependency) {
        global $CFG;

        $result = array();
        $filename = 'backuplib.php';
        $classuf  = '_export_converter';
        $classfile = "{$CFG->dirroot}/backup/converter/{$converter}/{$filename}";
        $classname = "{$converter}{$classuf}";

        if (!file_exists($classfile)) {
            throw new convert_helper_exception('converter_classfile_not_found', $classfile);
        }
        require_once($classfile);

        if (!class_exists($classname)) {
            throw new convert_helper_exception('converter_classname_not_found', $classname);
        }

        if (call_user_func($classname .'::is_available')) {
            $deps = call_user_func($classname .'::get_deps');
            if (array_key_exists($dependency, $deps)) {
                $result = $deps[$dependency];
            }
        }

        return $result;
    }

    /**
     * Detects if the given folder contains an unpacked moodle2 backup
     *
     * @param string $tempdir the name of the backup directory
     * @return boolean true if moodle2 format detected, false otherwise
     */
    public static function detect_moodle2_format($tempdir) {
        global $CFG;

        $dirpath    = $CFG->tempdir . '/backup/' . $tempdir;
        $filepath   = $dirpath . '/moodle_backup.xml';

        if (!is_dir($dirpath)) {
            throw new convert_helper_exception('tmp_backup_directory_not_found', $dirpath);
        }

        if (!file_exists($filepath)) {
            return false;
        }

        $handle     = fopen($filepath, 'r');
        $firstchars = fread($handle, 200);
        $status     = fclose($handle);

        if (strpos($firstchars,'<?xml version="1.0" encoding="UTF-8"?>') !== false and
            strpos($firstchars,'<moodle_backup>') !== false and
            strpos($firstchars,'<information>') !== false) {
                return true;
        }

        return false;
    }

    /**
     * Converts the given directory with the backup into moodle2 format
     *
     * @param string $tempdir The directory to convert
     * @param string $format The current format, if already detected
     * @param base_logger|null if the conversion should be logged, use this logger
     * @throws convert_helper_exception
     * @return bool false if unable to find the conversion path, true otherwise
     */
    public static function to_moodle2_format($tempdir, $format = null, $logger = null) {

        if (is_null($format)) {
            $format = backup_general_helper::detect_backup_format($tempdir);
        }

        // get the supported conversion paths from all available converters
        $converters   = self::available_converters();
        $descriptions = array();
        foreach ($converters as $name) {
            $classname = "{$name}_converter";
            if (!class_exists($classname)) {
                throw new convert_helper_exception('class_not_loaded', $classname);
            }
            if ($logger instanceof base_logger) {
                backup_helper::log('available converter', backup::LOG_DEBUG, $classname, 1, false, $logger);
            }
            $descriptions[$name] = call_user_func($classname .'::description');
        }

        // choose the best conversion path for the given format
        $path = self::choose_conversion_path($format, $descriptions);

        if (empty($path)) {
            if ($logger instanceof base_logger) {
                backup_helper::log('unable to find the conversion path', backup::LOG_ERROR, null, 0, false, $logger);
            }
            return false;
        }

        if ($logger instanceof base_logger) {
            backup_helper::log('conversion path established', backup::LOG_INFO,
                implode(' => ', array_merge($path, array('moodle2'))), 0, false, $logger);
        }

        foreach ($path as $name) {
            if ($logger instanceof base_logger) {
                backup_helper::log('running converter', backup::LOG_INFO, $name, 0, false, $logger);
            }
            $converter = convert_factory::get_converter($name, $tempdir, $logger);
            $converter->convert();
        }

        // make sure we ended with moodle2 format
        if (!self::detect_moodle2_format($tempdir)) {
            throw new convert_helper_exception('conversion_failed');
        }

        return true;
    }

   /**
    * Inserts an inforef into the conversion temp table
    */
    public static function set_inforef($contextid) {
        global $DB;
    }

    public static function get_inforef($contextid) {
    }

    /// end of public API //////////////////////////////////////////////////////

    /**
     * Choose the best conversion path for the given format
     *
     * Given the source format and the list of available converters and their properties,
     * this methods picks the most effective way how to convert the source format into
     * the target moodle2 format. The method returns a list of converters that should be
     * called, in order.
     *
     * This implementation uses Dijkstra's algorithm to find the shortest way through
     * the oriented graph.
     *
     * @see http://en.wikipedia.org/wiki/Dijkstra's_algorithm
     * @author David Mudrak <david@moodle.com>
     * @param string $format the source backup format, one of backup::FORMAT_xxx
     * @param array $descriptions list of {@link base_converter::description()} indexed by the converter name
     * @return array ordered list of converter names to call (may be empty if not reachable)
     */
    protected static function choose_conversion_path($format, array $descriptions) {

        // construct an oriented graph of conversion paths. backup formats are nodes
        // and the the converters are edges of the graph.
        $paths = array();   // [fromnode][tonode] => converter
        foreach ($descriptions as $converter => $description) {
            $from   = $description['from'];
            $to     = $description['to'];
            $cost   = $description['cost'];

            if (is_null($from) or $from === backup::FORMAT_UNKNOWN or
                is_null($to) or $to === backup::FORMAT_UNKNOWN or
                is_null($cost) or $cost <= 0) {
                    throw new convert_helper_exception('invalid_converter_description', $converter);
            }

            if (!isset($paths[$from][$to])) {
                $paths[$from][$to] = $converter;
            } else {
                // if there are two converters available for the same conversion
                // path, choose the one with the lowest cost. if there are more
                // available converters with the same cost, the chosen one is
                // undefined (depends on the order of processing)
                if ($descriptions[$paths[$from][$to]]['cost'] > $cost) {
                    $paths[$from][$to] = $converter;
                }
            }
        }

        if (empty($paths)) {
            // no conversion paths available
            return array();
        }

        // now use Dijkstra's algorithm and find the shortest conversion path

        $dist = array(); // list of nodes and their distances from the source format
        $prev = array(); // list of previous nodes in optimal path from the source format
        foreach ($paths as $fromnode => $tonodes) {
            $dist[$fromnode] = null; // infinitive distance, can't be reached
            $prev[$fromnode] = null; // unknown
            foreach ($tonodes as $tonode => $converter) {
                $dist[$tonode] = null; // infinitive distance, can't be reached
                $prev[$tonode] = null; // unknown
            }
        }

        if (!array_key_exists($format, $dist)) {
            return array();
        } else {
            $dist[$format] = 0;
        }

        $queue = array_flip(array_keys($dist));
        while (!empty($queue)) {
            // find the node with the smallest distance from the source in the queue
            // in the first iteration, this will find the original format node itself
            $closest = null;
            foreach ($queue as $node => $undefined) {
                if (is_null($dist[$node])) {
                    continue;
                }
                if (is_null($closest) or ($dist[$node] < $dist[$closest])) {
                    $closest = $node;
                }
            }

            if (is_null($closest) or is_null($dist[$closest])) {
                // all remaining nodes are inaccessible from source
                break;
            }

            if ($closest === backup::FORMAT_MOODLE) {
                // bingo we can break now
                break;
            }

            unset($queue[$closest]);

            // visit all neighbors and update distances to them eventually

            if (!isset($paths[$closest])) {
                continue;
            }
            $neighbors = array_keys($paths[$closest]);
            // keep just neighbors that are in the queue yet
            foreach ($neighbors as $ix => $neighbor) {
                if (!array_key_exists($neighbor, $queue)) {
                    unset($neighbors[$ix]);
                }
            }

            foreach ($neighbors as $neighbor) {
                // the alternative distance to the neighbor if we went thru the
                // current $closest node
                $alt = $dist[$closest] + $descriptions[$paths[$closest][$neighbor]]['cost'];

                if (is_null($dist[$neighbor]) or $alt < $dist[$neighbor]) {
                    // we found a shorter way to the $neighbor, remember it
                    $dist[$neighbor] = $alt;
                    $prev[$neighbor] = $closest;
                }
            }
        }

        if (is_null($dist[backup::FORMAT_MOODLE])) {
            // unable to find a conversion path, the target format not reachable
            return array();
        }

        // reconstruct the optimal path from the source format to the target one
        $conversionpath = array();
        $target         = backup::FORMAT_MOODLE;
        while (isset($prev[$target])) {
            array_unshift($conversionpath, $paths[$prev[$target]][$target]);
            $target = $prev[$target];
        }

        return $conversionpath;
    }
}

/**
 * General convert_helper related exception
 *
 * @author David Mudrak <david@moodle.com>
 */
class convert_helper_exception extends moodle_exception {

    /**
     * Constructor
     *
     * @param string $errorcode key for the corresponding error string
     * @param object $a extra words and phrases that might be required in the error string
     * @param string $debuginfo optional debugging information
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}
