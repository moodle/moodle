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
 * @package moodlecore
 * @subpackage backup-xml
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This abstract class implements one progressive_parser_processor
 *
 * Processor that will receive chunks of data from the @progressive_parser
 * and will perform all sort of operations with them (join, split, invoke
 * other methods, output, whatever...
 *
 * You will need to extend this class to get the expected functionality
 * by implementing the @process_chunk() method to handle different
 * chunks of information and, optionally, the @process_cdata() to
 * process each cdata piece individually before being "published" to
 * the chunk processor.
 *
 * The "propietary array format" that the parser publishes to the @progressive_parser_procesor
 * is this:
 *    array (
 *        'path' => path where the tags belong to,
 *        'level'=> level (1-based) of the tags
 *        'tags  => array (
 *            'name' => name of the tag,
 *            'attrs'=> array( name of the attr => value of the attr),
 *            'cdata => cdata of the tag
 *        )
 *    )
 *
 * TODO: Finish phpdocs
 */
abstract class progressive_parser_processor {

    protected $inittime; // Initial microtime
    protected $chunks;   // Number of chunks processed

    public function __construct() {
        $this->inittime= microtime(true);
        $this->chunks   = 0;
    }

    /**
     * Receive one chunk of information from the parser
     */
    abstract public function process_chunk($data);

    /**
     * The parser fires this each time one path is going to be parsed
     */
    public function before_path($path) { }

    /**
     * The parser fires this each time one path has been parsed
     */
    public function after_path($path) { }

    /**
     * Perform custom transformations in the processed cdata
     */
    public function process_cdata($cdata) {
        return $cdata;
    }

    public function debug_info() {
        return array('memory' => memory_get_peak_usage(true),
                     'time'   => microtime(true) - $this->inittime,
                     'chunks' => $this->chunks);
    }

    public function receive_chunk($data) {
        $this->chunks++;
        $this->process_chunk($data);
    }

}
