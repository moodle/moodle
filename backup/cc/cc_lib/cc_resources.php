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

require_once 'cc_interfaces.php';
require_once 'xmlbase.php';
require_once 'gral_lib/pathutils.php';
require_once 'gral_lib/ccdependencyparser.php';
require_once 'cc_version_base.php';
require_once 'cc_version1.php';
require_once 'cc_manifest.php';

/**
 * Common Cartridge Version
 *
 */
class cc_version{
  const v1  = 1;
  const v11 = 11;
}


class cc1_resource_type {
    const   webcontent          = 'webcontent';
    const   questionbank        = 'imsqti_xmlv1p2/imscc_xmlv1p0/question-bank';
    const   assessment          = 'imsqti_xmlv1p2/imscc_xmlv1p0/assessment';
    const   associatedcontent   = 'associatedcontent/imscc_xmlv1p0/learning-application-resource';
    const   discussiontopic     = 'imsdt_xmlv1p0';
    const   weblink             = 'imswl_xmlv1p0';

    public static $checker = array(self::webcontent,
                                    self::assessment,
                                    self::associatedcontent,
                                    self::discussiontopic,
                                    self::questionbank,
                                    self::weblink);

}

class cc11_resource_type {
    const webcontent         = 'webcontent';
    const questionbank       = 'imsqti_xmlv1p2/imscc_xmlv1p1/question-bank';
    const assessment         = 'imsqti_xmlv1p2/imscc_xmlv1p1/assessment';
    const associatedcontent  = 'associatedcontent/imscc_xmlv1p1/learning-application-resource';
    const discussiontopic    = 'imsdt_xmlv1p1';
    const weblink            = 'imswl_xmlv1p1';
    const basiclti           = 'imsbasiclti_xmlv1p0';

    public static $checker = array(self::webcontent,
                                    self::assessment,
                                    self::associatedcontent,
                                    self::discussiontopic,
                                    self::questionbank,
                                    self::weblink,
                                    self::basiclti);

}


/**
 * Resource Class
 *
 */
class cc_resource implements cc_i_resource {

    public  $identifier     = null;
    public  $type           = null;
    public  $dependency     = array();
    public  $identifierref  = null;
    public  $href           = null;
    public  $base           = null;
    public  $persiststate   = null;
    public  $filename       = null;
    public  $files          = array();
    public  $isempty        = null;
    public  $manifestroot   = null;
    public  $folder         = null;

    private $throwonerror   = true;

    public function __construct($manifest, $file, $folder='', $throwonerror = true) {
        $this->throwonerror = $throwonerror;
        if (is_string($manifest)) {
            $this->folder = $folder;
            $this->process_resource($manifest, $file,$folder);
            $this->manifestroot = $manifest;
        } else if (is_object($manifest)) {
            $this->import_resource($file,$manifest.$folder);
        }
    }

    /**
     * Add resource
     *
     * @param string $fname
     * @param string $location
     */
    public function add_resource ($fname, $location =''){
        $this->process_resource($fname,$location);

    }


    /**
     * Import a resource
     *
     * @param DOMElement $node
     * @param cc_i_manifest $doc
     */
    public function import_resource(DOMElement &$node, cc_i_manifest &$doc) {

        $searchstr = "//imscc:manifest[@identifier='".$doc->manifestID().
                     "']/imscc:resources/imscc:resource";
        $this->identifier   = $this->get_attr_value($node,"identifier");
        $this->type         = $this->get_attr_value($node,"type");
        $this->href         = $this->get_attr_value($node,"href");
        $this->base         = $this->get_attr_value($node,"base");
        $this->persiststate = null;
        $nodo               = $doc->nodeList($searchstr."[@identifier='".
                              $this->identifier."']/metadata/@href");
        $this->metadata     = $nodo->nodeValue;
        $this->filename     = $this->href;
        $nlist              = $doc->nodeList($searchstr."[@identifier='".
                              $this->identifier."']/imscc:file/@href");
        $this->files        = array();
        foreach ($nlist as $file) {
            $this->files[]  = $file->nodeValue;
        }
        $nlist              = $doc->nodeList($searchstr."[@identifier='".
                              $this->identifier."']/imscc:dependency/@identifierref");
        $this->dependency   = array();
        foreach ($nlist as $dependency) {
            $this->dependency[]  = $dependency->nodeValue;
        }
        $this->isempty      = false;
    }


    /**
     * Get a attribute value
     *
     * @param DOMElement $nod
     * @param string $name
     * @param string $ns
     * @return string
     */
    public function get_attr_value(&$nod, $name, $ns=null) {
      return is_null($ns) ?
             ($nod->hasAttribute($name) ? $nod->getAttribute($name) : null) :
             ($nod->hasAttributeNS($ns, $name) ? $nod->getAttributeNS($ns, $name) : null);
    }


    /**
     * Process a resource
     *
     * @param string $manifestroot
     * @param string $fname
     * @param string $folder
     */
    public function process_resource($manifestroot, &$fname, $folder) {
        $file = empty($folder) ? $manifestroot.'/'.$fname : $manifestroot.'/'.$folder.'/'.$fname;
        if (!file_exists($file) && $this->throwonerror){
            throw new Exception('The file doesnt exist!');
        }

        GetDepFiles($manifestroot, $fname, $this->folder, $this->files);
        array_unshift($this->files,$folder.$fname);
        $this->init_empty_new();
        $this->href             = $folder.$fname;
        $this->identifierref    = $folder.$fname;
        $this->filename         = $fname;
        $this->isempty          = false;
        $this->folder           = $folder;
    }

    public function adjust_path($mroot, $fname) {
        $result = null;
        if (file_exists($fname->filename)) {
            $result = pathDiff($fname->filename,$mroot);

        } else if (file_exists($mroot.$fname->filename) || file_exists($mroot.DIRECTORY_SEPARATOR.$fname->filename)) {
            $result = trim(toUrlPath($fname->filename),"/");
        }
        return $result;
    }



    public function init_clean() {
        $this->identifier       =   null;
        $this->type             =   null;
        $this->href             =   null;
        $this->base             =   null;
        $this->metadata         =   array();
        $this->dependency       =   array();
        $this->identifierref    =   null;
        $this->persiststate     =   null;
        $this->filename         =   '';
        $this->files            =   array();
        $this->isempty          =   true;
    }


    public function init_empty_new() {
        $this->identifier       =   cc_helpers::uuidgen('I_', '_R');
        $this->type             =   null;
        $this->href             =   null;
        $this->persiststate     =   null;
        $this->filename         =   null;
        $this->isempty          =   false;
        $this->identifierref    =   null;
    }

    public function get_manifestroot(){
        return $this->manifestroot;
    }

}