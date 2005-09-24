<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

    /***
     * This page will deploy an IMS Content Package zip file, 
     * building all the structures and auxiliary files to
     * work inside a Moodle resource.
     */

/// Required stuff
    require_once('../../../../config.php');
    require_once('../../lib.php');
    require_once('resource.class.php');
    require_once('../../../../backup/lib.php');
    require_once('../../../../lib/filelib.php');
    require_once('../../../../lib/xmlize.php');

/// Load request parameters
    $courseid   = required_param ('courseid', PARAM_INT);
    $cmid       = required_param ('cmid', PARAM_INT);
    $file       = required_param ('file', PARAM_PATH);
    $inpopup    = optional_param ('inpopup', 0, PARAM_BOOL);

/// Fetch some records from DB
    $course   = get_record ('course', 'id', $courseid);
    $cm       = get_record ('course_modules', 'id', $cmid);
    $resource = get_record ('resource', 'id', $cm->instance);

/// Get some needed strings
    $strdeploy = get_string('deploy','resource');

/// Instantiate a resource_ims object and modify its navigation
    $resource_obj = new resource_ims ($cmid);
    if ($resource_obj->course->category) {
        $resource_obj->navigation = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/course/view.php?id={$course->id}\">{$course->shortname}</a> -> ".
                            "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/mod/resource/index.php?id={$course->id}\">$resource_obj->strresources</a> -> ";
    } else {
        $resource_obj->navigation = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/mod/resource/index.php?id={$course->id}\">$resource_obj->strresources</a> -> ";
    }

/// Print the header of the page
    $pagetitle = strip_tags($course->shortname.': '.
                     format_string($resource->name)).': '.
                     $strdeploy;

    if ($inpopup) {
        print_header($pagetitle, $course->fullname);
    } else {
        print_header($pagetitle, $course->fullname, 
                     $resource_obj->navigation.format_string($resource->name).' -> '.$strdeploy,
                     '', '', true, 
                     update_module_button($cm->id, $course->id, $resource_obj->strresource));
    }

/// Security Constraints (sesskey and isteacheredit)
    if (!confirm_sesskey()) {
        error(get_string('confirmsesskeybad', 'error'));
    } else if (!isteacheredit($courseid)) {
        error(get_string('onlyeditingteachers', 'error'));
    }

///
/// Main process, where everything is deployed
///

/// Set some variables

/// Create directories
    if (!$resourcedir = make_upload_directory($courseid.'/'.$CFG->moddata.'/resource/'.$resource->id)) {
        error (get_string('errorcreatingdirectory', 'error', $CFG->moddata.'/resource/'.$resource->id));
    }

/// Ensure it's empty
    if (!delete_dir_contents($resourcedir)) {
        error (get_string('errorcleaningdirectory', 'error', $resourcedir));
    }
    
/// Copy files
    $origin = $CFG->dataroot.'/'.$courseid.'/'.$file;
    if (!is_file($origin)) {
        error (get_string('filenotfound' , 'error', $file));
    }
    $mimetype = mimeinfo("type", $file);
    if ($mimetype != "application/zip") {
        error (get_string('invalidfiletype', 'error', $file));
    }
    $resourcefile = $resourcedir.'/'.basename($origin);
    if (!backup_copy_file($origin, $resourcefile)) {
        error (get_string('errorcopyingfiles', 'error'));
    }

/// Unzip files
    if (!unzip_file($resourcefile, '', false)) {
        error (get_string('errorunzippingfiles', 'error'));
    }

/// Check for imsmanifest
    if (!file_exists($resourcedir.'/imsmanifest.xml')) {
        error (get_string('filenotfound', 'error', 'imsmanifest.xml'));
    }

/// Load imsmanifest to memory (instead of using a full parser,
/// we are going to use xmlize intensively (because files aren't too big)
    if (!$imsmanifest = ims_file2var ($resourcedir.'/imsmanifest.xml')) {
        error (get_string ('errorreadingfile', 'error', 'imsmanifest.xml'));
    }

/// Check if the first line is a proper one, because I've seen some
/// packages with some control characters at the beginning.
    $inixml = strpos($imsmanifest, '<?xml ');
    if ($inixml !== false) {
        if ($inixml !== 0) {
            //Strip strange chars before "<?xml "
            $imsmanifest = substr($imsmanifest, $inixml);
        }
    } else {
        error (get_string ('invalidxmlfile', 'error', 'imsmanifest.xml'));
    }

/// xmlize the variable
    $data = xmlize($imsmanifest, 0);

/// Detect if all the manifest share a common xml:base tag
    $manifest_base = $data['manifest']['@']['xml:base'];

/// Parse XML-metadata
    /// Skip this for now (until a proper METADATA container was created in Moodle).

/// Parse XML-content package data
/// First we select an organization an load all the items
    if (!$items = ims_process_organizations($data['manifest']['#']['organizations']['0'])) {
        error (get_string('nonmeaningfulcontent', 'error'));
    }

/// Detect if all the resources share a common xml:base tag
    $resources_base = $data['manifest']['#']['resources']['0']['@']['xml:base'];
  
/// Now, we load all the resources available (keys are identifiers)
    if (!$resources = ims_load_resources($data['manifest']['#']['resources']['0']['#']['resource'], $manifest_base, $resources_base)) {
        error (get_string('nonmeaningfulcontent', 'error'));
    }
///Now we assign to each item, its resource (by identifier)
    foreach ($items as $key=>$item) {
        if (!empty($resources[$item->identifierref])) {
            $items[$key]->href = $resources[$item->identifierref];
        } else {
            $items[$key]->href = '';
        }
    }

/// Create the INDEX (moodle_inx.ser - where the order of the pages are stored serialized) file
    if (!ims_save_serialized_file($resourcedir.'/moodle_inx.ser', $items)) {
        error (get_string('errorcreatingfile', 'error', 'moodle_inx.ser'));
    }

/// Create the HASH file (moodle_hash.ser - where the hash of the ims is stored serialized) file
    $hash = $resource_obj->calculatefilehash($resourcefile);
    if (!ims_save_serialized_file($resourcedir.'/moodle_hash.ser', $hash)) {
        error (get_string('errorcreatingfile', 'error', 'moodle_hash.ser'));
    }

/// End button (go to view mode)
    echo '<center>';
    print_simple_box(get_string('imspackageloaded', 'resource'), 'center');
    $link = $CFG->wwwroot.'/mod/resource/view.php';
    $options['r'] = $resource->id;
    $label = get_string('viewims', 'resource');
    $method = 'post';
    print_single_button($link, $options, $label, $method);
    echo '</center>';

///
/// End of main process, where everything is deployed
///

/// Print the footer of the page
    print_footer();

///
/// Common and useful functions used by the body of the script
///

    /*** This function will return an ordered and nested array of items
     *   that is a perfect representation of the prefered organization
     */
    function ims_process_organizations($data) {

        global $CFG;

    /// Get the default organization
        $default_organization = $data['@']['default'];
        if ($CFG->debug) print_object('default_organization: '.$default_organization);

    /// Iterate (reverse) over organizations until we find the default one
        $count_organizations = count($data['#']['organization']);
        if ($CFG->debug) print_object('count_organizations: '.$count_organizations);

        $current_organization = $count_organizations - 1;
        while ($current_organization >= 0) {
        /// Load organization and check it
            $organization = $data['#']['organization'][$current_organization];
            if ($organization['@']['identifier'] == $default_organization) {
                    $current_organization = -1;   //Match, so exit.
            }
            $current_organization--;
        }

    /// At this point we MUST have the final organization
        if ($CFG->debug) print_object('final organization: '.$organization['#']['title'][0]['#']);
        if (empty($organization)) {
            return false;    //Error, no organization found
        }

    /// Extract items map from organization
        $items = $organization['#']['item'];
        if (!$itemmap = ims_process_items($items)) {
            return false;    //Error, no items found
        }
        return $itemmap;
    }

    /*** This function gets the xmlized representation of the items
     *   and returns an array of items, ordered, with level and info
     */
    function ims_process_items($items, $level = 1, $id = 1, $parent = 0) {
        global $CFG;

        $itemmap = array();

    /// Iterate over items from start to end
        $count_items = count($items);
        if ($CFG->debug) print_object('level '.$level.'-count_items: '.$count_items);

        $current_item = 0;
        while ($current_item < $count_items) {
        /// Load item 
            $item = $items[$current_item];
            $obj_item = new stdClass;
            $obj_item->title         = $item['#']['title'][0]['#'];
            $obj_item->identifier    = $item['@']['identifier'];
            $obj_item->identifierref = $item['@']['identifierref'];
            $obj_item->id            = $id;
            $obj_item->level         = $level;
            $obj_item->parent        = $parent;
        /// Only if the item has everything
            if (!empty($obj_item->title) && 
                !empty($obj_item->identifier)) {
            /// Add to itemmap
                $itemmap[$id] = $obj_item;
                if ($CFG->debug) print_object('level '.$level.'-id '.$id.'-parent '.$parent.'-'.$obj_item->title);
            /// Counters go up
                $id++;
            /// Check for subitems recursively
                $subitems = $item['#']['item'];
                if (count($subitems)) {
                /// Recursive call
                    $subitemmap = ims_process_items($subitems, $level+1, $id, $obj_item->id);
                /// Add at the end and counters if necessary
                    if ($count_subitems = count($subitemmap)) {
                        foreach ($subitemmap as $subitem) {
                        /// Add the subitem to the main items array
                            $itemmap[$subitem->id] = $subitem;
                        /// Counters go up
                            $id++;
                        }
                    }
                }
            }
            $current_item++;
        }
        return $itemmap;
    }

    /*** This function will load an array of resources to be used later. 
     *   Keys are identifiers
     */
    function ims_load_resources($data, $manifest_base, $resources_base) {
        global $CFG;

        $resources = array();

        $count_resources = count($data);
        if ($CFG->debug) print_object('count_resources: '.$count_resources);

        $current_resource = 0;
        while ($current_resource < $count_resources) {
        /// Load resource 
            $resource = $data[$current_resource];

        /// Create a new object resource
            $obj_resource = new stdClass;
            $obj_resource->identifier = $resource['@']['identifier'];
            $obj_resource->resource_base = $resource['@']['xml:base'];
            $obj_resource->href = $resource['@']['href'];
            if (empty($obj_resource->href)) {
                $obj_resource->href = $resource['#']['file']['0']['@']['href'];
            }

        /// Only if the resource has everything
            if (!empty($obj_resource->identifier) &&
                !empty($obj_resource->href)) {
            /// Add to resources (identifier as key)
            /// Depending of $manifest_base, $resources_base and the particular
            /// $resource_base variable, concatenate them to build the correct href
                $href_base = '';
                if (!empty($manifest_base)) {
                    $href_base = $manifest_base;
                }
                if (!empty($resources_base)) {
                    $href_base .= $resources_base;
                }
                if (!empty($obj_resource->resource_base)) {
                    $href_base .= $obj_resource->resource_base;
                }
                $resources[$obj_resource->identifier] = $href_base.$obj_resource->href;
            }
        /// Counters go up
            $current_resource++;
        }
        return $resources;
    }

?>
