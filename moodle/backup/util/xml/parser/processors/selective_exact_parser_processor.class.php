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
 * @subpackage xml
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/backup/util/xml/parser/processors/progressive_parser_processor.class.php');

/**
 * Selective progressive_parser_processor that will send chunks straight
 * to output but only for chunks matching (in an exact way) some defined paths
 */
class selective_exact_parser_processor extends progressive_parser_processor {

   protected $paths; // array of paths we are interested on

   public function __construct(array $paths) {
       parent::__construct();
       $this->paths = $paths;
   }

   public function process_chunk($data) {
       if ($this->path_is_selected($data['path'])) {
           print_r($data); // Simply output chunk, for testing purposes
       } else {
           $this->chunks--; // Chunk skipped
       }
   }

// Protected API starts here

   protected function path_is_selected($path) {
       return in_array($path, $this->paths);
   }
}
