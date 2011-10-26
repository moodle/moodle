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
* Manifest management
*
* @package    backup-convert
* @subpackage cc-library
* @copyright  2011 Darko Miletic <dmiletic@moodlerooms.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/


require_once 'cc_utils.php';
require_once 'xmlbase.php';
require_once 'cc_resources.php';
require_once 'cc_version_base.php';
require_once 'gral_lib/pathutils.php';


/**
 * Manifest Class
 *
 */
class cc_manifest extends XMLGenericDocument implements cc_i_manifest {
    private $ccversion              = null;
    private $ccobj                  = null;
    private $rootmanifest           = null;
    private $activemanifest         = null;
    private $parentmanifest         = null;
    private $parentparentmanifest   = null;
    private $ares                   = array();
    private $mainidentifier         = null;

    public function __construct($ccver = cc_version::v1,$activemanifest=null,
                        $parentmanifest=null,$parentparentmanifest=null) {

        if (is_int($ccver)){
            $this->ccversion=$ccver;
            $classname = "cc_version{$ccver}";
            $this->ccobj = new $classname;
            parent::__construct('UTF-8',true);
        } else
        if (is_object($ccver) && (get_class($ccver)=='cc_manifest')){
            $this->doc = $ccver->doc;
            $this->rootmanifest = $ccver->rootmanifest;
            $this->activemanifest = $activemanifest;
            $this->parentmanifest = $parentmanifest;
            $this->parentparentmanifest = $parentparentmanifest;
            $this->ccversion = $ccver->ccversion;
            $this->ccobj = $ccver;
            $this->register_namespaces_for_xpath();
        }
    }

    public function __destruct() {
        parent::__destruct();
    }


    /**
     * Register Namespace for use XPATH
     *
     */
    public function register_namespaces_for_xpath(){
        $scnam = $this->activemanifest->get_cc_namespaces();
        foreach ($scnam as $key => $value){
            $this->registerNS($key,$value);
        }
    }

    /**
     * TODO - implement this method - critical
     * Enter description here ...
     */
    private function fill_manifest() {

    }

    /**
     * Add Metadata For Manifest
     *
     * @param cc_i_metadata_manifest $met
     */
    public function add_metadata_manifest (cc_i_metadata_manifest $met){
        $metanode = $this->node("//imscc:manifest[@identifier='".
                                $this->activemanifest->manifestID().
                                "']/imscc:metadata");
        $nmeta = $this->activemanifest->create_metadata_node($met,$this->doc,$metanode);
        $metanode->appendChild($nmeta);
    }


    /**
     * Add Metadata For Resource
     *
     * @param cc_i_metadata_resource $met
     * @param string $identifier
     */
    public function add_metadata_resource (cc_i_metadata_resource $met,$identifier){
       $metanode = $this->node("//imscc:resource".
                     "[@identifier='".
                     $identifier.
                     "']");
       $metanode2 = $this->node("//imscc:resource".
                     "[@identifier='".
                     $identifier.
                     "']/imscc:file");
       $dnode  = $this->doc->createElementNS($this->ccnamespaces['imscc'], "metadata");

       $metanode->insertBefore($dnode,$metanode2);

       $this->activemanifest->create_metadata_resource_node($met,$this->doc,$dnode);
    }


    /**
     * Add Metadata For File
     *
     * @param cc_i_metadata_file $met
     * @param string $identifier
     * @param string $filename
     */
    public function add_metadata_file (cc_i_metadata_file $met,$identifier,$filename){

        if (empty($met) || empty($identifier) || empty($filename)){
            throw new Exception('Try to add a metadata file with nulls values given!');
        }

        $metanode = $this->node("//imscc:resource".
                     "[@identifier='".
                     $identifier.
                     "']/imscc:file".
                     "[@href='".
                     $filename.
                     "']");

       $dnode  = $this->doc->createElementNS($this->ccnamespaces['imscc'], "metadata");

       $metanode->appendChild($dnode);

       $this->activemanifest->create_metadata_file_node($met,$this->doc,$dnode);
    }


    public function on_create (){
        $this->activemanifest = cc_builder_creator::factory($this->ccversion);
        $this->rootmanifest = $this->activemanifest;
        $result = $this->activemanifest->create_manifest($this->doc);
        $this->register_namespaces_for_xpath();
        return $result;

    }


    public function get_relative_base_path() {return $this->activemanifest->base();}
    public function parent_manifest () {return new cc_manifest($this,$this->parentmanifest,$this->parentparentmanifest);}
    public function root_manifest   () {return new cc_manifest($this,$this->rootmanifest);}
    public function manifestID     () {return $this->activemanifest->manifestID();}
    public function get_manifest_namespaces() {return $this->rootmanifest->get_cc_namespaces(); }



    /**
     * Add a new organization
     *
     * @param cc_i_organization $org
     */
    public function add_new_organization(cc_i_organization &$org) {
        $norg = $this->activemanifest->create_organization_node($org,$this->doc);
        $orgnode = $this->node("//imscc:manifest[@identifier='".
                                $this->activemanifest->manifestID().
                                "']/imscc:organizations");
        $orgnode->appendChild($norg);
    }



    public function get_resources($searchspecific='') {
        $reslist = $this->get_resource_list($searchspecific);
        $resourcelist = array();
        foreach ($reslist as $resourceitem) {
            $resourcelist[]=new cc_resource($this, $resourceitem);
        }
        return $resourcelist;
    }



    public function get_cc_namespace_path($nsname) {
        if (is_string($nsname) && (!empty($nsname))){
            $scnam = $this->activemanifest->get_cc_namespaces();
            return $scnam[$nsname];
        }
        return null;
    }


    public function get_resource_list($searchspecific=''){
        return $this->nodeList("//imscc:manifest[@identifier='".
                            $this->activemanifest->manifestID().
                            "']/imscc:resources/imscc:resource".$searchspecific);
    }


    public function on_load (){
        $this->register_namespaces_for_xpath();
        $this->fill_manifest();
        return true;
    }

    public function on_save (){
        return true;
    }


    /**
     * Add a resource to the manifest
     *
     * @param cc_i_resource $res
     * @param string $identifier
     * @param string $type
     * @return array
     */
    public function add_resource (cc_i_resource $res, $identifier=null, $type='webcontent'){

        if (!$this->ccobj->valid($type)){
             throw new Exception("Type invalid...");
        }

        if (is_null($res)){
            throw new Exception('Invalid Resource or dont give it');
        }
        $rst = null;

        if (is_string($res)){
            $rst = new cc_resource($this->filePath(), $res);
            if (is_string($identifier)){
                $rst->identifier = $identifier;
            }
        } else {
            $rst = $res;
        }

        //TODO: This has to be reviewed since it does not handle properly mutiple file
        //      dependencies
        if (is_object($identifier)) {
            $this->activemanifest->create_resource_node($rst,$this->doc,$identifier);
        } else {
            $nresnode   = null;

            $rst->type = $type;
            if (!cc_helpers::is_html($rst->filename)) {
                $rst->href = null;
            }
            $this->activemanifest->create_resource_node($rst,$this->doc,$nresnode);


            for ($i = 1 ; $i < count ($rst->files); $i++){
                $ident = $this->get_identifier_by_filename($rst->files[$i]);
                if(empty($ident)){
                    $newres = new cc_resource($rst->manifestroot,$rst->files[$i],false);
                    if (!empty($newres)) {
                        if (!cc_helpers::is_html($rst->files[$i])) {
                             $newres->href = null;
                        }
                        $newres->type = 'webcontent';
                        $this->activemanifest->create_resource_node($newres,$this->doc,$nresnode);
                    }
                }

            }
            foreach ($this->activemanifest->resources as $k => $v){
                ($k);
                $depen = $this->check_if_exist_in_other($v->files[0]);
                if (!empty($depen)){
                    $this->replace_file_x_dependency($depen,$v->files[0]);
                    // coloca aca como type = webcontent porque son archivos dependientes
                    // quizas aqui habria q ver de que type es el que vino y segun eso, ponerlo
                    // en associatedcontent o en webcontent
                    $v->type = 'webcontent';
                }
            }
        }

        $tmparray = array($rst->identifier,$rst->files[0]);
        return $tmparray;
    }



    private function check_if_exist_in_other($name){
        $status = array();
        foreach ($this->activemanifest->resources as $key => $value){
            ($key);
            for ($i=1; $i< count($value->files); $i++){
                if ($name == $value->files[$i]){
                    array_push($status,$value->identifier);
                }
            }
        }
        return $status;
    }


    private function replace_file_x_dependency($depen,$name){
        foreach ($depen as $key => $value){
            ($key);
            $ident = $this->get_identifier_by_filename($name);
            $this->activemanifest->resources[$value]->files =
                $this->array_remove_by_value($this->activemanifest->resources[$value]->files,$name);
            if (!in_array($ident,$this->activemanifest->resources[$value]->dependency)){
                array_push($this->activemanifest->resources[$value]->dependency,$ident);
            }

        }
        return true;
    }


    private function get_identifier_by_filename($name){
        $result = null;
        foreach ($this->activemanifest->resources as $key => $value) {
                if ($name == $value->files[0]){
                    $result = $key;
                    break;
                }
        }
        return $result;
    }



    private function array_remove_by_value($arr,$value) {
        return array_values(array_diff($arr,array($value)));

    }

    private function array_remove_by_key($arr,$key) {
        return array_values(array_diff_key($arr,array($key)));

    }


    /**
     * Append the resources nodes in the Manifest
     *
     * @return DOMNode
     */
    public function put_nodes (){

        $resnodestr = "//imscc:manifest[@identifier='".$this->activemanifest->manifestID().
                          "']/imscc:resources";
        $resnode    = $this->node($resnodestr);

        foreach ($this->activemanifest->resources as $key => $node) {
            ($key);
            $resnode->appendChild($this->activemanifest->create_resource_node($node,$this->doc,null));
        }
        return $resnode;

    }
}


