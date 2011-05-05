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
 * Provides {@link convert_helper} class
 *
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
    public static function available_converters() {
        global $CFG;

        $converters = array();
        $plugins    = get_list_of_plugins('backup/converter');
        foreach ($plugins as $name) {
            $classfile = "$CFG->dirroot/backup/converter/$name/converter.class.php";
            $classname = "{$name}_converter";

            if (!file_exists($classfile)) {
                throw new coding_exception("Converter factory error: class file not found $classfile");
            }
            require_once($classfile);

            if (!class_exists($classname)) {
                throw new coding_exception("Converter factory error: class not found $classname");
            }

            if (call_user_func($classname .'::is_available')) {
                $converters[] = $name;
            }
        }

        return $converters;
    }

    /**
     * Detects if the given folder contains an unpacked moodle2 backup
     *
     * @param string $tempdir the name of the backup directory
     * @return boolean true if moodle2 format detected, false otherwise
     */
    public static function detect_moodle2_format($tempdir) {
        global $CFG;

        $dirpath    = $CFG->dataroot . '/temp/backup/' . $tempdir;
        $filepath   = $dirpath . '/moodle_backup.xml';

        if (!is_dir($dirpath)) {
            throw new backup_helper_exception('tmp_backup_directory_not_found', $dirpath);
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
     * @throws coding_exception|restore_controller_exception
     * @param string $tempdir The directory to convert
     * @param string $format The current format, if already detected
     * @return void
     */
    public static function to_moodle2_format($tempdir, $format = null) {

        if (is_null($format)) {
            $format = backup_general_helper::detect_backup_format($tempdir);
        }

        // get the supported conversion paths from all available converters
        $converters   = convert_factory::available_converters();
        $descriptions = array();
        foreach ($converters as $name) {
            $classname = "{$name}_converter";
            if (!class_exists($classname)) {
                throw new coding_exception("available_converters() is supposed to load
                    converter classes but class $classname not found");
            }
            $descriptions[$name] = call_user_func($classname .'::description');
        }

        // choose the best conversion path for the given format
        $path = self::choose_conversion_path($format, $descriptions);

        if (empty($path)) {
            // unable to convert
            // todo throwing exception is not a good way to control the flow here
            throw new coding_exception('Unable to find conversion path');
        }

        foreach ($path as $name) {
            $converter = convert_factory::converter($name, $tempdir);
            $converter->convert();
        }

        // make sure we ended with moodle2 format
        if (!self::detect_moodle2_format($tempdir)) {
            throw new coding_exception('Conversion failed');
        }
    }

   /**
    * Inserts an inforef into the conversion temp table
    */
    public static function set_inforef($contextid) {
        global $DB;
    }

    public static function get_inforef($contextid) {
    }

    /**
     * Converts a plain old php object (popo?) into a string...
     * Useful for debuging failed db inserts, or anything like that
     */
    public static function obj_to_readable($obj) {
        $mapper = function($field, $value) { return "$field=$value"; };
        $fields = get_object_vars($obj);

        return implode(", ", array_map($mapper, array_keys($fields), array_values($fields)));
    }

    /**
     * Generate an artificial context ID
     *
     * @static
     * @throws Exception
     * @param int $instance The moodle component instance ID, same value used for get_context_instance()
     * @param string $component The moodle component, like block_html, mod_quiz, etc
     * @param string $converterid The converter ID
     * @return int
     * @todo Add caching?
     * @todo Can we make the lookup faster?  Not taking advantage of indexes
     */
    public static function get_contextid($instance, $component = 'moodle', $converterid = NULL) {
        global $DB;

        // Attempt to retrieve the contextid
        $contextid = $DB->get_field_select('backup_ids_temp', 'id',
                        $DB->sql_compare_text('info', 100).' = ? AND itemid = ? AND itemname = ?',
                        array($component, $instance, 'context')
        );

        if (!empty($contextid)) {
            return $contextid;
        }

        $context = new stdClass;
        $context->itemid   = $instance;
        $context->itemname = 'context';
        $context->info     = $component;

        if (!is_null($converterid)) {
            $context->backupid = $converterid;
        }
        if ($id = $DB->insert_record('backup_ids_temp', $context)) {
            return $id;
        } else {
            $msg = self::obj_to_readable($context);
            throw new Exception(sprintf("Could not insert context record into temp table: %s", $msg));
        }
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
                    throw new coding_exception('Invalid converter description:' . $converter);
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
