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
* @package    backup-convert
* @subpackage cc-library
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

/**
 * Factory pattern class
 * Create the version class to use
 *
 */
class cc_builder_creator {

   public static function factory($version){
       if (is_null($version)) {
           throw new Exception("Version is null!");
       }
       if (include_once 'cc_version' . $version . '.php') {
           $classname = 'cc_version' . $version;
           return new $classname;
       } else {
           throw new Exception ("Dont find cc version class!");
       }
   }
}