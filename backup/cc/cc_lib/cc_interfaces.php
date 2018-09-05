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
 * CC Manifest Interface
 */
interface cc_i_manifest {

    public function on_create ();
    public function on_load ();
    public function on_save ();
    public function add_new_organization (cc_i_organization &$org);
    public function get_resources ();
    public function get_resource_list ();
    public function add_resource (cc_i_resource $res, $identifier=null, $type='webcontent');
    public function add_metadata_manifest(cc_i_metadata_manifest $met);
    public function add_metadata_resource (cc_i_metadata_resource $met,$identifier);
    public function add_metadata_file (cc_i_metadata_file $met,$identifier,$filename);
    public function put_nodes ();

}



/**
 * CC Organization Interface
 */
interface cc_i_organization {

    public function add_item (cc_i_item &$item);
    public function has_items ();
    public function attr_value (&$nod, $name, $ns=null);
    public function process_organization (&$node,&$doc);

}



/**
 * CC Item Interface
 */
interface cc_i_item {

    public function add_child_item (cc_i_item &$item);
    public function attach_resource ($res);     // can be object or value
    public function has_child_items ();
    public function attr_value (&$nod, $name, $ns=null);
    public function process_item (&$node,&$doc);

}


/**
 * CC Resource Interface
 */
interface cc_i_resource {

    public function get_attr_value (&$nod, $name, $ns=null);
    public function add_resource ($fname, $location='');
    public function import_resource (DOMElement &$node, cc_i_manifest &$doc);
    public function process_resource ($manifestroot, &$fname,$folder);

}



/**
 * CC Metadata Manifest Interface
 */
interface cc_i_metadata_manifest {

    public function add_metadata_general($obj);
    public function add_metadata_technical($obj);
    public function add_metadata_rights($obj);
    public function add_metadata_lifecycle($obj);

}


/**
 * CC Metadata Resource Interface
 */
interface cc_i_metadata_resource {

    public function add_metadata_resource_educational($obj);

}

/**
 * CC Metadata File Interface
 */
interface cc_i_metadata_file {

    public function add_metadata_file_educational($obj);

}

