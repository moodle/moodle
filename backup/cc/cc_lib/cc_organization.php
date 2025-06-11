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

require_once 'cc_utils.php';
require_once 'cc_version_base.php';
require_once 'cc_resources.php';
require_once 'cc_manifest.php';


/**
 * Organization Class
 *
 */

class cc_organization implements cc_i_organization {


    public $title         = null;
    public $identifier    = null;
    public $structure     = null;
    public $itemlist      = null;
    private $metadata      = null;
    private $sequencing    = null;

    /** @var bool true if empty, otherwise false. */
    protected $isempty;

    public function __construct($node=null, $doc=null) {
        if (is_object($node) && is_object($doc)) {
            $this->process_organization($node,$doc);
        } else {
            $this->init_new();
        }
    }

    /**
     * Add one Item into the Organization
     *
     * @param cc_i_item $item
     */
    public function add_item(cc_i_item &$item) {
        if (is_null($this->itemlist)) {
            $this->itemlist = array();
        }
        $this->itemlist[$item->identifier] = $item;
    }

    /**
     * Add new Item into the Organization
     *
     * @param string $title
     * @return cc_i_item
     */
    public function add_new_item($title='') {
        $nitem = new cc_item();
        $nitem->title = $title;
        $this->add_item($nitem);
        return $nitem;
    }


    public function has_items() {
        return is_array($this->itemlist) && (count($this->itemlist) > 0);
    }

    public function attr_value(&$nod, $name, $ns=null) {
      return is_null($ns) ?
             ($nod->hasAttribute($name) ? $nod->getAttribute($name) : null) :
             ($nod->hasAttributeNS($ns, $name) ? $nod->getAttributeNS($ns, $name) : null);
    }


    public function process_organization(&$node,&$doc) {
        $this->identifier   = $this->attr_value($node,"identifier");
        $this->structure    = $this->attr_value($node,"structure");
        $this->title        = '';
        $nlist              = $node->getElementsByTagName('title');
        if (is_object($nlist) && ($nlist->length > 0) ) {
            $this->title = $nlist->item(0)->nodeValue;
        }
        $nlist = $doc->nodeList("//imscc:organization[@identifier='".$this->identifier."']/imscc:item");
        $this->itemlist=array();
        foreach ($nlist as $item) {
            $this->itemlist[$item->getAttribute("identifier")] = new cc_item($item,$doc);
        }
        $this->isempty=false;
    }

    public function init_new() {
        $this->title        = null;
        $this->identifier   = cc_helpers::uuidgen('O_');
        $this->structure    = 'rooted-hierarchy';
        $this->itemlist     = null;
        $this->metadata     = null;
        $this->sequencing   = null;

    }

    public function uuidgen() {
        $uuid = sprintf('%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535));
        return strtoupper(trim($uuid));
    }


}


/**
 * Item Class
 *
 */
class cc_item implements cc_i_item  {


    public $identifier     = null;
    public $identifierref  = null;
    public $isvisible      = null;
    public $title          = null;
    public $parameters     = null;
    public $childitems     = null;
    private $parentItem     = null;
    private $isempty        = true;

    /** @var mixed node structure. */
    public $structure;

    public function __construct($node=null,$doc=null) {
        if (is_object($node)) {
            $clname = get_class($node);
            if ($clname =='cc_resource') {
                $this->init_new_item();
                $this->identifierref = $node->identifier;
                $this->title = is_string($doc) && (!empty($doc)) ? $doc : 'item';
            } else if ($clname =='cc_manifest') {
                $this->init_new_item();
                $this->identifierref = $node->manifestID();
                $this->title = is_string($doc) && (!empty($doc)) ? $doc : 'item';
            } else if ( is_object($doc)){
                $this->process_item($node,$doc);
            } else {
                $this->init_new_item();
            }
        } else {
            $this->init_new_item();
        }
    }



    public function attr_value(&$nod, $name, $ns=null) {
      return is_null($ns) ?
             ($nod->hasAttribute($name) ? $nod->getAttribute($name) : null) :
             ($nod->hasAttributeNS($ns, $name) ? $nod->getAttributeNS($ns, $name) : null);
    }


    public function process_item(&$node,&$doc) {
        $this->identifier       = $this->attr_value($node,"identifier");
        $this->structure        = $this->attr_value($node,"structure");
        $this->identifierref    = $this->attr_value($node,"identifierref");
        $atr = $this->attr_value($node,"isvisible");
        $this->isvisible = is_null($atr) ? true : $atr;
        $nlist = $node->getElementsByTagName('title');
        if (is_object($nlist) && ($nlist->length > 0) ) {
            $this->title = $nlist->item(0)->nodeValue;
        }
        $nlist = $doc->nodeList("//imscc:item[@identifier='".$this->identifier."']/imscc:item");
        if ($nlist->length > 0) {
            $this->childitems=array();
            foreach ($nlist as $item) {
                $key=$this->attr_value($item,"identifier");
                $this->childitems[$key] = new cc_item($item,$doc);
            }
        }
        $this->isempty = false;
    }

    /**
     * Add one Child Item
     *
     * @param cc_i_item $item
     */
    public function add_child_item(cc_i_item &$item) {
        if (is_null($this->childitems)) {
            $this->childitems = array();
        }
        $this->childitems[$item->identifier] = $item;
    }


    /**
     * Add new child Item
     *
     * @param string $title
     * @return cc_i_item
     */
    public function add_new_child_item($title='') {
        $sc         = new cc_item();
        $sc->title  = $title;
        $this->add_child_item($sc);
        return $sc;
    }



    public function attach_resource($resource) {

        if ($this->has_child_items()) {
            throw new Exception("Can not attach resource to item that contains other items!");
        }
        $resident = null;
        if (is_string($resource)) {
            $resident = $resource;
        } else if (is_object($resource)) {
            $clname = get_class($resource);
            if ($clname == 'cc_resource') {
                $resident = $resource->identifier;
            } else
            if ($clname == 'cc_manifest') {
                $resident = $resource->manifestID();
            } else {
                throw new Exception("Unable to attach resource. Invalid object.");
            }
        }
        if (is_null($resident) || (empty($resident))) {
            throw new Exception("Resource must have valid identifier!");
        }
        $this->identifierref = $resident;
    }

    public function has_child_items() {
        return is_array($this->childitems) && (count($this->childitems) > 0);
    }

    public function child_item($identifier) {
        return $this->has_child_items() ? $this->childitems[$identifier] : null;
    }


    public function init_clean() {
            $this->identifier   = null;
            $this->isvisible    = null;
            $this->title        = null;
            $this->parameters   = null;
            $this->childitems   = null;
            $this->parentItem   = null;
            $this->isempty      = true;
    }

    public function init_new_item() {
            $this->identifier   = cc_helpers::uuidgen('I_');
            $this->isvisible    = true; //default is true
            $this->title        = null;
            $this->parameters   = null;
            $this->childitems   = null;
            $this->parentItem   = null;
            $this->isempty      = false;
    }

}