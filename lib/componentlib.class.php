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
 * This library includes all the necessary stuff to use the one-click
 * download and install feature of Moodle, used to keep updated some
 * items like languages, pear, enviroment... i.e, components.
 *
 * It has been developed harcoding some important limits that are
 * explained below:
 *    - It only can check, download and install items under moodledata.
 *    - Every downloadeable item must be one zip file.
 *    - The zip file root content must be 1 directory, i.e, everything
 *      is stored under 1 directory.
 *    - Zip file name and root directory must have the same name (but
 *      the .zip extension, of course).
 *    - Every .zip file must be defined in one .md5 file that will be
 *      stored in the same remote directory than the .zip file.
 *    - The name of such .md5 file is free, although it's recommended
 *      to use the same name than the .zip (that's the default
 *      assumption if no specified).
 *    - Every remote .md5 file will be a comma separated (CVS) file where each
 *      line will follow this format:
 *        - Field 1: name of the zip file (without extension). Mandatory.
 *        - Field 2: md5 of the zip file. Mandatory.
 *        - Field 3: whatever you want (or need). Optional.
 *    -Every local .md5 file will:
 *        - Have the zip file name (without the extension) plus -md5
 *        - Will reside inside the expanded zip file dir
 *        - Will contain the md5 od the latest installed component
 * With all these details present, the process will perform this tasks:
 *    - Perform security checks. Only admins are allowed to use this for now.
 *    - Read the .md5 file from source (1).
 *    - Extract the correct line for the .zip being requested.
 *    - Compare it with the local .md5 file (2).
 *    - If different:
 *        - Download the newer .zip file from source.
 *        - Calculate its md5 (3).
 *        - Compare (1) and (3).
 *        - If equal:
 *            - Delete old directory.
 *            - Uunzip the newer .zip file.
 *            - Create the new local .md5 file.
 *            - Delete the .zip file.
 *        - If different:
 *            - ERROR. Old package won't be modified. We shouldn't
 *              reach here ever.
 *    - If component download is not possible, a message text about how to do
 *      the process manually (remotedownloaderror) must be displayed to explain it.
 *
 * General Usage:
 *
 * To install one component:
 * <code>
 *     require_once($CFG->libdir.'/componentlib.class.php');
 *     if ($cd = new component_installer('http://download.moodle.org', 'langpack/2.0',
 *                                       'es.zip', 'languages.md5', 'lang')) {
 *         $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)
 *         switch ($status) {
 *             case COMPONENT_ERROR:
 *                 if ($cd->get_error() == 'remotedownloaderror') {
 *                     $a = new stdClass();
 *                     $a->url = 'http://download.moodle.org/langpack/2.0/es.zip';
 *                     $a->dest= $CFG->dataroot.'/lang';
 *                     print_error($cd->get_error(), 'error', '', $a);
 *                 } else {
 *                     print_error($cd->get_error(), 'error');
 *                 }
 *                 break;
 *             case COMPONENT_UPTODATE:
 *                 //Print error string or whatever you want to do
 *                 break;
 *             case COMPONENT_INSTALLED:
 *                 //Print/do whatever you want
 *                 break;
 *             default:
 *                 //We shouldn't reach this point
 *         }
 *     } else {
 *         //We shouldn't reach this point
 *     }
 * </code>
 *
 * To switch of component (maintaining the rest of settings):
 * <code>
 *     $status = $cd->change_zip_file('en.zip'); //returns boolean false on error
 * </code>
 *
 * To retrieve all the components in one remote md5 file
 * <code>
 *     $components = $cd->get_all_components_md5();  //returns boolean false on error, array instead
 * </code>
 *
 * To check if current component needs to be updated
 * <code>
 *     $status = $cd->need_upgrade();  //returns COMPONENT_(ERROR | UPTODATE | NEEDUPDATE)
 * </code>
 *
 * To get the 3rd field of the md5 file (optional)
 * <code>
 *     $field = $cd->get_extra_md5_field();  //returns string (empty if not exists)
 * </code>
 *
 * For all the error situations the $cd->get_error() method should return always the key of the
 * error to be retrieved by one standard get_string() call against the error.php lang file.
 *
 * That's all!
 *
 * @package   core
 * @copyright (C) 2001-3001 Eloy Lafuente (stronk7) {@link http://contiento.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

 /**
  * @global object $CFG
  * @name $CFG
  */
global $CFG;
require_once($CFG->libdir.'/filelib.php');

// Some needed constants
define('COMPONENT_ERROR',           0);
define('COMPONENT_UPTODATE',        1);
define('COMPONENT_NEEDUPDATE',      2);
define('COMPONENT_INSTALLED',       3);

/**
 * This class is used to check, download and install items from
 * download.moodle.org to the moodledata directory.
 *
 * It always return true/false in all their public methods to say if
 * execution has ended succesfuly or not. If there is any problem
 * its getError() method can be called, returning one error string
 * to be used with the standard get/print_string() functions.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package moodlecore
 */
class component_installer {
    /**
     * @var string
     */
    var $sourcebase;   /// Full http URL, base for downloadable items
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
     * @var array
     */
    var $cachedmd5components; /// Array of cached components to avoid to
                              /// download the same md5 file more than once per request.

    /**
     * Standard constructor of the class. It will initialize all attributes.
     * without performing any check at all.
     *
     * @param string $sourcebase Full http URL, base for downloadeable items
     * @param string $zippath Relative path (from sourcebase) where the
     *               downloadeable item resides
     * @param string $zipfilename Name of the .zip file to be downloaded
     * @param string $md5filename Name of the .md5 file to be read (default '' = same
     *               than zipfilename)
     * @param string $destpath Relative path (from moodledata) where the .zip file will
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
        $this->cachedmd5components = array();

        $this->check_requisites();
    }

    /**
     * This function will check if everything is properly set to begin
     * one installation. Also, it will check for required settings
     * and will fill everything as needed.
     *
     * @global object
     * @return boolean true/false (plus detailed error in errorstring)
     */
    function check_requisites() {
        global $CFG;

        $this->requisitesok = false;

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
    /// Check that exists under dataroot
        if (!empty($this->destpath)) {
            if (!file_exists($CFG->dataroot.'/'.$this->destpath)) {
                $this->errorstring='wrongdestpath';
                return false;
            }
        }
    /// Calculate the componentname
        $pos = stripos($this->zipfilename, '.zip');
        $this->componentname = substr($this->zipfilename, 0, $pos);
    /// Calculate md5filename if it's empty
        if (empty($this->md5filename)) {
            $this->md5filename = $this->componentname.'.md5';
        }
    /// Set the requisites passed flag
        $this->requisitesok = true;
        return true;
    }

    /**
     * This function will perform the full installation if needed, i.e.
     * compare md5 values, download, unzip, install and regenerate
     * local md5 file
     *
     * @global object
     * @uses COMPONENT_ERROR
     * @uses COMPONENT_UPTODATE
     * @uses COMPONENT_ERROR
     * @uses COMPONENT_INSTALLED
     * @return int COMPONENT_(ERROR | UPTODATE | INSTALLED)
     */
    function install() {

        global $CFG;

    /// Check requisites are passed
        if (!$this->requisitesok) {
            return COMPONENT_ERROR;
        }
    /// Confirm we need upgrade
        if ($this->need_upgrade() === COMPONENT_ERROR) {
            return COMPONENT_ERROR;
        } else if ($this->need_upgrade() === COMPONENT_UPTODATE) {
            $this->errorstring='componentisuptodate';
            return COMPONENT_UPTODATE;
        }
    /// Create temp directory if necesary
        if (!make_upload_directory('temp', false)) {
             $this->errorstring='cannotcreatetempdir';
             return COMPONENT_ERROR;
        }
    /// Download zip file and save it to temp
        $source = $this->sourcebase.'/'.$this->zippath.'/'.$this->zipfilename;
        $zipfile= $CFG->dataroot.'/temp/'.$this->zipfilename;

        if($contents = download_file_content($source)) {
            if ($file = fopen($zipfile, 'w')) {
                if (!fwrite($file, $contents)) {
                    fclose($file);
                    $this->errorstring='cannotsavezipfile';
                    return COMPONENT_ERROR;
                }
            } else {
                $this->errorstring='cannotsavezipfile';
                return COMPONENT_ERROR;
            }
            fclose($file);
        } else {
            $this->errorstring='cannotdownloadzipfile';
            return COMPONENT_ERROR;
        }
    /// Calculate its md5
        $new_md5 = md5($contents);
    /// Compare it with the remote md5 to check if we have the correct zip file
        if (!$remote_md5 = $this->get_component_md5()) {
            return COMPONENT_ERROR;
        }
        if ($new_md5 != $remote_md5) {
            $this->errorstring='downloadedfilecheckfailed';
            return COMPONENT_ERROR;
        }
    /// Move current revision to a safe place
        $destinationdir = $CFG->dataroot.'/'.$this->destpath;
        $destinationcomponent = $destinationdir.'/'.$this->componentname;
        @remove_dir($destinationcomponent.'_old');     //Deleting possible old components before
        @rename ($destinationcomponent, $destinationcomponent.'_old');  //Moving to a safe place
    /// Unzip new version
        if (!unzip_file($zipfile, $destinationdir, false)) {
        /// Error so, go back to the older
            @remove_dir($destinationcomponent);
            @rename ($destinationcomponent.'_old', $destinationcomponent);
            $this->errorstring='cannotunzipfile';
            return COMPONENT_ERROR;
        }
    /// Delete old component version
        @remove_dir($destinationcomponent.'_old');
    /// Create local md5
        if ($file = fopen($destinationcomponent.'/'.$this->componentname.'.md5', 'w')) {
            if (!fwrite($file, $new_md5)) {
                fclose($file);
                $this->errorstring='cannotsavemd5file';
                return COMPONENT_ERROR;
            }
        } else  {
            $this->errorstring='cannotsavemd5file';
            return COMPONENT_ERROR;
        }
        fclose($file);
    /// Delete temp zip file
        @unlink($zipfile);

        return COMPONENT_INSTALLED;
    }

    /**
     * This function will detect if remote component needs to be installed
     * because it's different from the local one
     *
     * @uses COMPONENT_ERROR
     * @uses COMPONENT_UPTODATE
     * @uses COMPONENT_NEEDUPDATE
     * @return int COMPONENT_(ERROR | UPTODATE | NEEDUPDATE)
     */
    function need_upgrade() {

    /// Check requisites are passed
        if (!$this->requisitesok) {
            return COMPONENT_ERROR;
        }
    /// Get local md5
        $local_md5 = $this->get_local_md5();
    /// Get remote md5
        if (!$remote_md5 = $this->get_component_md5()) {
            return COMPONENT_ERROR;
        }
    /// Return result
       if ($local_md5 == $remote_md5) {
           return COMPONENT_UPTODATE;
       } else {
           return COMPONENT_NEEDUPDATE;
       }
    }

    /**
     * This function will change the zip file to install on the fly
     * to allow the class to process different components of the
     * same md5 file without intantiating more objects.
     *
     * @param string $newzipfilename New zip filename to process
     * @return boolean true/false
     */
    function change_zip_file($newzipfilename) {

        $this->zipfilename = $newzipfilename;
        return $this->check_requisites();
    }

    /**
     * This function will get the local md5 value of the installed
     * component.
     *
     * @global object
     * @return bool|string md5 of the local component (false on error)
     */
    function get_local_md5() {
        global $CFG;

    /// Check requisites are passed
        if (!$this->requisitesok) {
            return false;
        }

        $return_value = 'needtobeinstalled';   /// Fake value to force new installation

    /// Calculate source to read
       $source = $CFG->dataroot.'/'.$this->destpath.'/'.$this->componentname.'/'.$this->componentname.'.md5';
    /// Read md5 value stored (if exists)
       if (file_exists($source)) {
           if ($temp = file_get_contents($source)) {
               $return_value = $temp;
           }
        }
        return $return_value;
    }

    /**
     * This function will download the specified md5 file, looking for the
     * current componentname, returning its md5 field and storing extramd5info
     * if present. Also it caches results to cachedmd5components for better
     * performance in the same request.
     *
     * @return mixed md5 present in server (or false if error)
     */
    function get_component_md5() {

    /// Check requisites are passed
        if (!$this->requisitesok) {
            return false;
        }
    /// Get all components of md5 file
        if (!$comp_arr = $this->get_all_components_md5()) {
            if (empty($this->errorstring)) {
                $this->errorstring='cannotdownloadcomponents';
            }
            return false;
        }
    /// Search for the componentname component
        if (empty($comp_arr[$this->componentname]) || !$component = $comp_arr[$this->componentname]) {
             $this->errorstring='cannotfindcomponent';
             return false;
        }
    /// Check we have a valid md5
        if (empty($component[1]) || strlen($component[1]) != 32) {
            $this->errorstring='invalidmd5';
            return false;
        }
    /// Set the extramd5info field
        if (!empty($component[2])) {
            $this->extramd5info = $component[2];
        }
        return $component[1];
    }

    /**
     * This function allows you to retrieve the complete array of components found in
     * the md5filename
     *
     * @return bool|array array of components in md5 file or false if error
     */
    function get_all_components_md5() {

    /// Check requisites are passed
        if (!$this->requisitesok) {
            return false;
        }

    /// Initialize components array
        $comp_arr = array();

    /// Define and retrieve the full md5 file
        $source = $this->sourcebase.'/'.$this->zippath.'/'.$this->md5filename;

    /// Check if we have downloaded the md5 file before (per request cache)
        if (!empty($this->cachedmd5components[$source])) {
            $comp_arr = $this->cachedmd5components[$source];
        } else {
        /// Not downloaded, let's do it now
            $availablecomponents = array();

            if ($contents = download_file_content($source)) {
            /// Split text into lines
                $lines=preg_split('/\r?\n/',$contents);
            /// Each line will be one component
                foreach($lines as $line) {
                    $availablecomponents[] = explode(',', $line);
                }
            /// If no components have been found, return error
                if (empty($availablecomponents)) {
                    $this->errorstring='cannotdownloadcomponents';
                    return false;
                }
            /// Build an associative array of components for easily search
            /// applying trim to avoid linefeeds and other...
                $comp_arr = array();
                foreach ($availablecomponents as $component) {
                /// Avoid sometimes empty lines
                    if (empty($component[0])) {
                        continue;
                    }
                    $component[0]=trim($component[0]);
                    if (!empty($component[1])) {
                        $component[1]=trim($component[1]);
                    }
                    if (!empty($component[2])) {
                        $component[2]=trim($component[2]);
                    }
                    $comp_arr[$component[0]] = $component;
                }
            /// Cache components
                $this->cachedmd5components[$source] = $comp_arr;
            } else {
            /// Return error
                $this->errorstring='remotedownloaderror';
                return false;
            }
        }
    /// If there is no commponents or erros found, error
        if (!empty($this->errorstring)) {
             return false;

        } else if (empty($comp_arr)) {
             $this->errorstring='cannotdownloadcomponents';
             return false;
        }
        return $comp_arr;
    }

    /**
     * This function returns the errorstring
     *
     * @return string the error string
     */
    function get_error() {
        return $this->errorstring;
    }

    /** This function returns the extramd5 field (optional in md5 file)
     *
     * @return string the extramd5 field
     */
    function get_extra_md5_field() {
        return $this->extramd5info;
    }

} /// End of component_installer class


/**
 * Language packs installer
 *
 * This class wraps the functionality provided by {@link component_installer}
 * and adds support for installing a set of language packs.
 *
 * Given an array of required language packs, this class fetches them all
 * and installs them. It detects eventual dependencies and installs
 * all parent languages, too.
 *
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang_installer {

    /** lang pack was successfully downloaded and deployed */
    const RESULT_INSTALLED      = 'installed';
    /** lang pack was up-to-date so no download was needed */
    const RESULT_UPTODATE       = 'uptodate';
    /** there was a problem with downloading the lang pack */
    const RESULT_DOWNLOADERROR  = 'downloaderror';

    /** @var array of languages to install */
    protected $queue = array();
    /** @var string the code of language being currently installed */
    protected $current;
    /** @var array of languages already installed by this instance */
    protected $done = array();
    /** @var string this Moodle major version */
    protected $version;

    /**
     * Prepare the installer
     *
     * @todo Moodle major version is hardcoded here, should be obtained from version.php or so
     * @param string|array $langcode a code of the language to install
     */
    public function __construct($langcode = '') {
        global $CFG;

        $this->set_queue($langcode);
        $this->version = '2.1';

        if (!empty($CFG->langotherroot) and $CFG->langotherroot !== $CFG->dataroot . '/lang') {
            debugging('The in-built language pack installer does not support alternative location ' .
                'of languages root directory. You are supposed to install and update your language '.
                'packs on your own.');
        }
    }

    /**
     * Sets the queue of language packs to be installed
     *
     * @param string|array $langcodes language code like 'cs' or a list of them
     */
    public function set_queue($langcodes) {
        if (is_array($langcodes)) {
            $this->queue = $langcodes;
        } else if (!empty($langcodes)) {
            $this->queue = array($langcodes);
        }
    }

    /**
     * Runs the installer
     *
     * This method calls {@link self::install_language_pack} for every language in the
     * queue. If a dependency is detected, the parent language is added to the queue.
     *
     * @return array results, array of self::RESULT_xxx constants indexed by language code
     */
    public function run() {

        $results = array();

        while ($this->current = array_shift($this->queue)) {

            if ($this->was_processed($this->current)) {
                // do not repeat yourself
                continue;
            }

            if ($this->current === 'en') {
                $this->mark_processed($this->current);
                continue;
            }

            $results[$this->current] = $this->install_language_pack($this->current);

            if (in_array($results[$this->current], array(self::RESULT_INSTALLED, self::RESULT_UPTODATE))) {
                if ($parentlang = $this->get_parent_language($this->current)) {
                    if (!$this->is_queued($parentlang) and !$this->was_processed($parentlang)) {
                        $this->add_to_queue($parentlang);
                    }
                }
            }

            $this->mark_processed($this->current);
        }

        return $results;
    }

    /**
     * Returns the URL where a given language pack can be downloaded
     *
     * Alternatively, if the parameter is empty, returns URL of the page with the
     * list of all available language packs.
     *
     * @param string $langcode language code like 'cs' or empty for unknown
     * @return string URL
     */
    public function lang_pack_url($langcode = '') {

        if (empty($langcode)) {
            return 'http://download.moodle.org/langpack/'.$this->version.'/';
        } else {
            return 'http://download.moodle.org/download.php/langpack/'.$this->version.'/'.$langcode.'.zip';
        }
    }

    /**
     * Returns the list of available language packs from download.moodle.org
     *
     * @return array|bool false if can not download
     */
    public function get_remote_list_of_languages() {
        $source = 'http://download.moodle.org/langpack/' . $this->version . '/languages.md5';
        $availablelangs = array();

        if ($content = download_file_content($source)) {
            $alllines = explode("\n", $content);
            foreach($alllines as $line) {
                if (!empty($line)){
                    $availablelangs[] = explode(',', $line);
                }
            }
            return $availablelangs;

        } else {
            return false;
        }
    }

    // Internal implementation /////////////////////////////////////////////////

    /**
     * Adds a language pack (or a list of them) to the queue
     *
     * @param string|array $langcodes code of the language to install or a list of them
     */
    protected function add_to_queue($langcodes) {
        if (is_array($langcodes)) {
            $this->queue = array_merge($this->queue, $langcodes);
        } else if (!empty($langcodes)) {
            $this->queue[] = $langcodes;
        }
    }

    /**
     * Checks if the given language is queued or if the queue is empty
     *
     * @example $installer->is_queued('es');    // is Spanish going to be installed?
     * @example $installer->is_queued();        // is there a language queued?
     *
     * @param string $langcode language code or empty string for "any"
     * @return boolean
     */
    protected function is_queued($langcode = '') {

        if (empty($langcode)) {
            return !empty($this->queue);

        } else {
            return in_array($langcode, $this->queue);
        }
    }

    /**
     * Checks if the given language has already been processed by this instance
     *
     * @see self::mark_processed()
     * @param string $langcode
     * @return boolean
     */
    protected function was_processed($langcode) {
        return isset($this->done[$langcode]);
    }

    /**
     * Mark the given language pack as processed
     *
     * @see self::was_processed()
     * @param string $langcode
     */
    protected function mark_processed($langcode) {
        $this->done[$langcode] = 1;
    }

    /**
     * Returns a parent language of the given installed language
     *
     * @param string $langcode
     * @return string parent language's code
     */
    protected function get_parent_language($langcode) {
        return get_parent_language($langcode);
    }

    /**
     * Perform the actual language pack installation
     *
     * @uses component_installer
     * @param string $langcode
     * @return int return status
     */
    protected function install_language_pack($langcode) {

        // initialise new component installer to process this language
        $installer = new component_installer('http://download.moodle.org', 'download.php/direct/langpack/' . $this->version,
            $langcode . '.zip', 'languages.md5', 'lang');

        if (!$installer->requisitesok) {
            throw new lang_installer_exception('installer_requisites_check_failed');
        }

        $status = $installer->install();

        if ($status == COMPONENT_ERROR) {
            if ($installer->get_error() === 'remotedownloaderror') {
                return self::RESULT_DOWNLOADERROR;
            } else {
                throw new lang_installer_exception($installer->get_error(), $langcode);
            }

        } else if ($status == COMPONENT_UPTODATE) {
            return self::RESULT_UPTODATE;

        } else if ($status == COMPONENT_INSTALLED) {
            return self::RESULT_INSTALLED;

        } else {
            throw new lang_installer_exception('unexpected_installer_result', $status);
        }
    }
}


/**
 * Exception thrown by {@link lang_installer}
 *
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang_installer_exception extends moodle_exception {

    public function __construct($errorcode, $debuginfo = null) {
        parent::__construct($errorcode, 'error', '', null, $debuginfo);
    }
}
