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
* Metadata managing
*
* @package    backup-convert
* @subpackage cc-library
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/



/**
 * Metadata Resource Educational Type
 *
 */
class cc_metadata_resouce_educational{

    public $value   = array();


    public function set_value ($value){
        $arr = array($value);
        $this->value[] = $arr;
    }

}

/**
 * Metadata Resource
 *
 */
class cc_metadata_resouce implements cc_i_metadata_resource {

    public $arrayeducational  = array();

    public function add_metadata_resource_educational($obj){
        if (empty($obj)){
            throw new Exception('Medatada Object given is invalid or null!');
        }
         !is_null($obj->value)? $this->arrayeducational['value']=$obj->value:null;
    }


}