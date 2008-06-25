<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2008 onwards  Moodle Pty Ltd   http://moodle.com        //
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

/**
 * Base repository class
 */
abstract class repository {

    public $plugin;        // plugin name - must be filled in by subclasses!
    public $repositoryid;  // The id of this repository instance in the repository table
    public $context;       // The current context of the user using this repository right now


    /**
     * Constructor should set up all the private variables
     * @access public
     * @param object $context
     */
    public function repository($context) {
    }


/// The following methods are not final!

    /**
     * Given a file path on this repository, get the file, store it via File API and return 
     * the file object describing the file
     * @access public
     * @param string $fullpath
     * @return object $file
     */
    public function get_file($fullpath) {
    }


    /**
     * Given a path, return a listing object of files and directories within that path
     * @access public
     * @param string $parent
     * @param string $search
     * @return object $listing
     */
    public function get_listing($parent='/', $search='') {
    }


    /**
     * Given a listing object from get_listing, print the output to the screen
     * @access public
     * @param object $listing
     * @return boolean $success
     */
    public function print_listing($listing) {
    }


    /**
     * Prints a search box, with an optional default value 
     * @access public
     * @param string $default
     * @return boolean $success
     */
    public function print_search($default='') {
    }


    /**
     * This function will get run by cron on a regular basis
     * @access public
     * @return boolean $success
     */
    public function cron() {
    }

}


/**
 * Listing object describing a listing of files and directories
 */

abstract class repository_listing {
}

?>
