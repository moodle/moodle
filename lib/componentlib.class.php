<?php  //$Id$

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

// This library includes all the necessary stuff to use the one-click
// download and install feature of Moodle, used to keep updated some
// items like languages, pear, enviroment... i.e, components.
// 
// It has been developed harcoding some important limits that are 
// explained below:
//    - It only can check, download and install items under moodledata.
//    - Every downloadeable item must be one zip file.
//    - The zip file root content must be 1 directory, i.e, everything
//      stored under 1 directory.
//    - Zip file name and root directory must have the same name (but 
//      the .zip, of course).
//    - Every .zip file must be defined in one .md5 file that will be
//      stored in the same remote directory that the .zip file.
//    - The name of such .md5 file is free, although it's recommended
//      to use the same name than the .zip (that's the default 
//      assumption if no specified).
//    - Every .md5 file will be a comma separated (CVS) file where each
//      line will follow this formar:
//        - Field 1: name of the zip file (without extension). Mandatory.
//        - Field 2: md5 of the zip file. Mandatory.
//        - Field 3: whatever you want. Optional.
// With all these details present, the process will perform this tasks:
//    - Perform security checks. Only admins are allowed to use this.
//    - Perform server checks. fopen must allow to open remote URLs.
//    - Read the .md5 file from source (1).
//    - Extract the correct line for the .zip being requested.
//    - Compare it with the local .md5 file (2).
//    - If different:
//        - Download the newer .zip file from source.
//        - Calculate its md5 (3).
//        - Compare (1) and (3).
//        - If equal:
//            - Delete old directory.
//            - Uunzip the newer .zip file.
//            - Create the new local .md5 file.
//            - Delete the .zip file.
//        - If different:
//            - ERROR. Old package won't be modified. We shouldn't
//              reach here ever.
//    - If fopen is not available, a message text about how to do
//      the process manually will be generated.
// That's all!

/**
 * This class is used to chack, download and install items from
 * download.moodle.org to the moodledata directory. It always
 * return true/false in all their public methods to say if
 * execution has ended succesfuly or not. If there is any problem
 * its getError() method can be called, returning one error string
 * to be used with the standard get/print_string() functions.
 */
class component_installer {

    var $sourcebase;   /// Full http URL, base for downloadeable items
    var $zippath;      /// Relative path (from sourcebase) where the 
                       /// downloadeable item resides.
    var $zipfilename;  /// Name of the .zip file to be downloaded
    var $md5filename;  /// Name of the .md5 file to be read
    var $componentname;/// Name of the component. Must be the zip name without
                       /// the extension. And it defines a lot of things:
                       /// the md5 line to search for, the default m5 file name
                       /// and the name of the root dir stored inside the zip file
    var $destpath;     /// Relative path (from moodledata) where the .zip
                       /// file will be expanded.
    var $errorstring;  /// Latest error produced. It will contain one lang string key.
    var $extramd5info; /// Contents of the optional third field in the .md5 file.
    var $requisitesok; /// Flag to see if requisites check has been passed ok.

    /**
     * Standard constructor of the class. It will initialize all attributes.
     * without performing any check at all.
     *
     * @param string Full http URL, base for downloadeable items
     * @param string Relative path (from sourcebase) where the 
     *               downloadeable item resides
     * @param string Name of the .zip file to be downloaded
     * @param string Name of the .md5 file to be read (default '' = same 
     *               than zipfilename)
     * @param string Relative path (from moodledata) where the .zip file will 
     *               be expanded (default='' = moodledataitself)
     * @return object
     */
    function component_installer ($sourcebase, $zippath, $zipfilename, $md5filename='', $destpath='') {

        $this->sourcebase   = $sourcebase;
        $this->zippath      = $zippath;
        $this->zipfilename  = $zipfilename;
        $this->md5filename  = $md5filename;
        $this->componentname= '';
        $this->destpath     = $destpath;
        $this->errorstring  = '';
        $this->extramd5info = '';
        $this->requisitesok = false;
    }

    /**
     * This function will check if everything is properly set to begin
     * one installation. It'll check for fopen wrappers enabled and
     * admin privileges. Also, it will check for required settings
     * and will fill everything as needed.
     *
     * @return boolean true/false (plus detailed error in errorstring)
     */
    function check_requisites() {

    /// Check for admin (this will be out in the future)
        if (!isadmin()) {
            $this->errorstring='onlyadmicaninstallcomponents';
            return false;
        } else {
        /// Check for fopen remote enabled
            if (!ini_get('allow_url_fopen')) {
                $this->errorstring='remotedownloadnotallowed';
                return false;
            }
        }
    /// Check that everything we need is present
        if (empty($this->sourcebase) || empty($this->zippath) || empty($this->zipfilename)) {
            $this->errorstring='missingrequiredfield';
            return false;
        }
    /// Check for correct sourcebase (this will be out in the future)
        if ($this->sourcebase != 'http://download.moodle.org') {
            $this->errorstring='wrongsourcebase';
            return false;
        }
    /// Check the zip file is a correct one (by extension)
        if (stripos($this->zipfilename, '.zip') === false) {
            $this->errorstring='wrongzipfilename';
            return false;
        }
    /// Calculate the componentnamea
        $pos = stripos($this->zipfilename, '.zip');
        $this->componentname = substr($this->zipfilename, 0, $pos+1);
    /// Calculate md5filename if it's empty
        if (empty($this->md5filename)) {
            $this->md5filename = $this->componentname.'md5';
        }
    /// Set the requisites passed flag
        $this->requisitesok = true;
        return true;
    }

    /**
     * This function will download the specified md5 file, looking for the
     * current componentname, returning its md5 field and storing extramd5info
     * if present
     *
     * @return mixed md5 present in server (or false if error)
     */
    function get_remote_md5() {

    /// Check requisites are passed
        if (!$this->requisitesok) {
            $this->errorstring='requisitesnotpassed';
            return false;
        }

    /// Define and retrieve the full md5 file
        $source = $this->sourcebase.'/'.$this->zippath.'/'.$this->md5filename;
        $availablecomponents = array();
        if ($fp = fopen($source, 'r')) {
        /// Read from URL, each line will be one component
            while(!feof ($fp)) {
                $availablecomponents[] = split(',', fgets($fp,1024));
            }
            fclose($fp);
        /// If no components have been found, return error
            if (empty($availablecomponents)) {
                $this->errorstring='cannotdownloadmd5file';
                return false;
            }
        /// Build an associative array of components, storing it in the 
        /// Look for our expected componentname
            
            print_object($availablecomponents);
            
        } else {
        /// Return error
            $this->errorstring='cannotdownloadcomponent';
            return false;
        }

    }

    /**
     * This function returns the errorstring
     *
     * @return string the error string
     */
    function get_error_string() {
        return $this->errorstring;
    }


} /// End of component_installer class

?>

