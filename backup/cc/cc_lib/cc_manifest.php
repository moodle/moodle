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

require_once('cc_utils.php');
require_once('xmlbase.php');
require_once('cc_resources.php');
require_once('cc_version_base.php');
require_once('gral_lib/pathutils.php');

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

    public function __construct($ccver = cc_version::v1, $activemanifest=null,
                        $parentmanifest=null, $parentparentmanifest=null) {

        if (is_int($ccver)) {
            $this->ccversion=$ccver;
            $classname = "cc_version{$ccver}";
            $this->ccobj = new $classname;
            parent::__construct('UTF-8', true);
        } else if (is_object($ccver) && (get_class($ccver)=='cc_manifest')) {
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

    /**
     * Register Namespace for use XPATH
     *
     */
    public function register_namespaces_for_xpath() {
        $scnam = $this->activemanifest->get_cc_namespaces();
        foreach ($scnam as $key => $value) {
            $this->registerNS($key, $value);
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
    public function add_metadata_manifest(cc_i_metadata_manifest $met) {
        $metanode = $this->node("//imscc:manifest[@identifier='".
                                $this->activemanifest->manifestID().
                                "']/imscc:metadata");
        $nmeta = $this->activemanifest->create_metadata_node($met, $this->doc, $metanode);
        $metanode->appendChild($nmeta);
    }


    /**
     * Add Metadata For Resource
     *
     * @param cc_i_metadata_resource $met
     * @param string $identifier
     */
    public function add_metadata_resource(cc_i_metadata_resource $met, $identifier) {
        $metanode  = $this->node("//imscc:resource".
            "[@identifier='".
            $identifier.
            "']");
        $metanode2 = $this->node("//imscc:resource".
            "[@identifier='".
            $identifier.
            "']/imscc:file");
        $nspaces   = $this->activemanifest->get_cc_namespaces();
        $dnode     = $this->append_new_element_ns($metanode2, $nspaces['imscc'], 'metadata');
        $this->activemanifest->create_metadata_resource_node($met, $this->doc, $dnode);
    }


    /**
     * Add Metadata For File
     *
     * @param cc_i_metadata_file $met
     * @param string $identifier
     * @param string $filename
     */
    public function add_metadata_file(cc_i_metadata_file $met, $identifier, $filename) {

        if (empty($met) || empty($identifier) || empty($filename)) {
            throw new Exception('Try to add a metadata file with nulls values given!');
        }

        $metanode = $this->node("//imscc:resource".
            "[@identifier='".
            $identifier.
            "']/imscc:file".
            "[@href='".
            $filename.
            "']");

        $nspaces = $this->activemanifest->get_cc_namespaces();
        $dnode   = $this->doc->createElementNS($nspaces['imscc'], "metadata");

        $metanode->appendChild($dnode);

        $this->activemanifest->create_metadata_file_node($met, $this->doc, $dnode);
    }


    public function on_create() {
        $this->activemanifest = cc_builder_creator::factory($this->ccversion);
        $this->rootmanifest = $this->activemanifest;
        $result = $this->activemanifest->create_manifest($this->doc);
        $this->register_namespaces_for_xpath();
        return $result;
    }

    public function get_relative_base_path() {
        return $this->activemanifest->base();
    }

    public function parent_manifest() {
        return new cc_manifest($this, $this->parentmanifest, $this->parentparentmanifest);
    }

    public function root_manifest() {
        return new cc_manifest($this, $this->rootmanifest);
    }

    public function manifestID() {
        return $this->activemanifest->manifestID();
    }

    public function get_manifest_namespaces() {
        return $this->rootmanifest->get_cc_namespaces();
    }

    /**
     * Add a new organization
     *
     * @param cc_i_organization $org
     */
    public function add_new_organization(cc_i_organization &$org) {
        $norg    = $this->activemanifest->create_organization_node($org, $this->doc);
        $orgnode = $this->node("//imscc:manifest[@identifier='".
            $this->activemanifest->manifestID().
            "']/imscc:organizations");
        $orgnode->appendChild($norg);
    }

    public function get_resources($searchspecific='') {
        $reslist = $this->get_resource_list($searchspecific);
        $resourcelist = array();
        foreach ($reslist as $resourceitem) {
            $resourcelist[] = new cc_resource($this, $resourceitem);
        }
        return $resourcelist;
    }

    public function get_cc_namespace_path($nsname) {
        if (is_string($nsname) && (!empty($nsname))) {
            $scnam = $this->activemanifest->get_cc_namespaces();
            return $scnam[$nsname];
        }
        return null;
    }


    public function get_resource_list($searchspecific = '') {
        return $this->nodeList("//imscc:manifest[@identifier='".
                            $this->activemanifest->manifestID().
                            "']/imscc:resources/imscc:resource".$searchspecific);
    }

    public function on_load() {
        $this->register_namespaces_for_xpath();
        $this->fill_manifest();
        return true;
    }

    public function on_save() {
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
    public function add_resource(cc_i_resource $res, $identifier = null, $type = 'webcontent') {

        if (!$this->ccobj->valid($type)) {
            throw new Exception("Type invalid...");
        }

        if ($res == null) {
            throw new Exception('Invalid Resource or dont give it');
        }
        $rst = $res;

        // TODO: This has to be reviewed since it does not handle multiple files properly.
        // Dependencies.
        if (is_object($identifier)) {
            $this->activemanifest->create_resource_node($rst, $this->doc, $identifier);
        } else {
            $nresnode   = null;

            $rst->type = $type;
            if (!cc_helpers::is_html($rst->filename)) {
                $rst->href = null;
            }

            $this->activemanifest->create_resource_node($rst, $this->doc, $nresnode);
            foreach ($rst->files as $file) {
                $ident = $this->get_identifier_by_filename($file);
                if ($ident == null) {
                    $newres = new cc_resource($rst->manifestroot, $file);
                    if (!cc_helpers::is_html($file)) {
                         $newres->href = null;
                    }
                    $newres->type = 'webcontent';
                    $this->activemanifest->create_resource_node($newres, $this->doc, $nresnode);
                }
            }
        }

        $tmparray = array($rst->identifier, $rst->files[0]);
        return $tmparray;
    }

    private function check_if_exist_in_other($name, $identifier) {
        $status = array();
        foreach ($this->activemanifest->resources as $value) {
            if (($value->identifier != $identifier) && isset($value->files[$name])) {
                $status[] = $value->identifier;
            }
        }
        return $status;
    }

    private function replace_file_x_dependency($depen, $name) {
        foreach ($depen as $key => $value) {
            ($key);
            $ident                                          = $this->get_identifier_by_filename($name);
            $this->activemanifest->resources[$value]->files =
                $this->array_remove_by_value($this->activemanifest->resources[$value]->files, $name);
            if (!in_array($ident, $this->activemanifest->resources[$value]->dependency)) {
                array_push($this->activemanifest->resources[$value]->dependency, $ident);
            }

        }
        return true;
    }

    private function get_identifier_by_filename($name) {
        $result = null;
        if (isset($this->activemanifest->resources_ind[$name])) {
            $result = $this->activemanifest->resources_ind[$name];
        }
        return $result;
    }

    private function array_remove_by_value($arr, $value) {
        return array_values(array_diff($arr, array($value)));
    }

    private function array_remove_by_key($arr, $key) {
        return array_values(array_diff_key($arr, array($key)));
    }

    public function update_instructoronly($identifier, $value = false) {
        if (isset($this->activemanifest->resources[$identifier])) {
            $resource = $this->activemanifest->resources[$identifier];
            $resource->instructoronly = $value;
        }
    }

    /**
     * Append the resources nodes in the Manifest
     *
     * @return DOMNode
     */
    public function put_nodes() {

        $resnodestr = "//imscc:manifest[@identifier='".$this->activemanifest->manifestID().
            "']/imscc:resources";
        $resnode    = $this->node($resnodestr);

        foreach ($this->activemanifest->resources as $k => $v) {
            ($k);
            $depen = $this->check_if_exist_in_other($v->files[0], $v->identifier);
            if (!empty($depen)) {
                $this->replace_file_x_dependency($depen, $v->files[0]);
                $v->type = 'webcontent';
            }
        }

        foreach ($this->activemanifest->resources as $node) {
            $rnode = $this->activemanifest->create_resource_node($node, $this->doc, null);
            $resnode->appendChild($rnode);
            if ($node->instructoronly) {
                $metafileceduc = new cc_metadata_resouce_educational();
                $metafileceduc->set_value(intended_user_role::INSTRUCTOR);
                $metafile = new cc_metadata_resouce();
                $metafile->add_metadata_resource_educational($metafileceduc);
                $this->activemanifest->create_metadata_educational($metafile, $this->doc, $rnode);
            }
        }

        return $resnode;
    }
}


