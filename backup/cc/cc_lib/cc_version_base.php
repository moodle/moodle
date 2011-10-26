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

require_once 'cc_organization.php';


/**
 * Abstract Version Base class
 *
 */
abstract class cc_version_base {
    protected $_generator           = null;
    protected $ccnamespaces         = array();
    protected $isrootmanifest       = false;
    protected $manifestID           = null;
    protected $organizationid       = null;
    public    $resources            = null;
    protected $metadata             = null;
    public    $organizations        = null;
    protected $base                 = null;
    public    $ccversion            = null;
    public    $camversion           = null;


    abstract protected function on_create(DOMDocument &$doc,$rootmanifestnode=null,$nmanifestID=null);

    abstract protected function create_metadata_manifest (cc_i_metadata_manifest $met,DOMDocument &$doc,$xmlnode=null);

    abstract protected function create_metadata_resource (cc_i_metadata_resource $met,DOMDocument &$doc,$xmlnode=null);

    abstract protected function create_metadata_file (cc_i_metadata_file $met,DOMDocument &$doc,$xmlnode=null);

    abstract protected function create_resource(cc_i_resource &$res, DOMDocument &$doc, $xmlnode=null);

    abstract protected function create_organization(cc_i_organization &$org, DOMDocument &$doc, $xmlnode=null);

    public function get_cc_namespaces(){
        return $this->ccnamespaces;
    }

    public function create_manifest(DOMDocument &$doc,$rootmanifestnode=null){
        return $this->on_create($doc,$rootmanifestnode);
    }

    public function create_resource_node(cc_i_resource &$res, DOMDocument &$doc, $xmlnode = null) {
        return $this->create_resource($res,$doc,$xmlnode);
    }


    public function create_metadata_node (&$met, DOMDocument &$doc, $xmlnode = null){
        return $this->create_metadata_manifest($met,$doc,$xmlnode);
    }

    public function create_metadata_resource_node (&$met, DOMDocument &$doc, $xmlnode = null){
        return $this->create_metadata_resource($met,$doc,$xmlnode);
    }

    public function create_metadata_file_node (&$met, DOMDocument &$doc, $xmlnode = null){
        return $this->create_metadata_file($met,$doc,$xmlnode);
    }

    public function create_organization_node(cc_i_organization &$org, DOMDocument &$doc, $xmlnode = null) {
        return $this->create_organization($org,$doc,$xmlnode);
    }

    public function manifestID(){
        return $this->manifestID;
    }

    public function set_manifestID($id){
        $this->manifestID = $id;
    }

    public function get_base(){
        return $this->base;
    }

    public function set_base($baseval){
        $this->base = $baseval;
    }

    public function import_resources(DOMElement &$node, cc_i_manifest &$doc) {
        if (is_null($this->resources)){
            $this->resources = array();
        }
        $nlist = $node->getElementsByTagNameNS($this->ccnamespaces['imscc'],'resource');
        if (is_object($nlist)) {
            foreach ($nlist as $nd) {
                $sc = new cc_resource($doc,$nd);
                $this->resources[$sc->identifier]=$sc;
            }
        }
    }

    public function import_organization_items(DOMElement &$node, cc_i_manifest &$doc) {
        if (is_null($this->organizations)) {
            $this->organizations = array();
        }
        $nlist = $node->getElementsByTagNameNS($this->ccnamespaces['imscc'],'organization');
        if (is_object($nlist)) {
            foreach ($nlist as $nd) {
                $sc = new cc_organization($nd,$doc);
                $this->organizations[$sc->identifier]=$sc;
            }
        }
    }

    public function set_generator($value) {
        $this->_generator = $value;
    }
}