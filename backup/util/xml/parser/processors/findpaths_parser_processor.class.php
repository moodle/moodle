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
 * Find paths progressive_parser_processor that will search for all the paths present in
 * the chunks being returned. Useful to know the overal structure of the XML file.
 */
class findpaths_parser_processor extends progressive_parser_processor {

   protected $foundpaths; // array of paths foudn in the chunks received from the parser

   public function __construct() {
       parent::__construct();
       $this->foundpaths = array();
   }

   public function process_chunk($data) {
       if (isset($data['tags'])) {
           foreach ($data['tags'] as $tag) {
               $tagpath = $data['path'] . '/' . $tag['name'];
               if (!array_key_exists($tagpath, $this->foundpaths)) {
                   $this->foundpaths[$tagpath] = 1;
               } else {
                   $this->foundpaths[$tagpath]++;
               }
           }
       }
   }

   public function debug_info() {
       $debug = array();
       foreach($this->foundpaths as $path => $chunks) {
           $debug['paths'][$path] = $chunks;
       }
       return array_merge($debug, parent::debug_info());
   }
}
