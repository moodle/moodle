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
 * Classes for displaying and editing a nested list of items.
 *
 * Handles functionality for :
 *
 *    Construction of nested list from db records with some key pointing to a parent id.
 *    Display of list with or without editing icons with optional pagination.
 *    Reordering of items works across pages.
 *    Processing of editing actions on list.
 *
 * @package    core
 * @subpackage lib
 * @copyright  Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Clues to reading this code:
 *
 * The functions that move things around the tree structure just update the
 * database - they don't update the in-memory structure, instead they trigger a
 * page reload so everything is rebuilt from scratch.
 *
 * @package moodlecore
 * @copyright Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class moodle_list {
    public $attributes;
    public $listitemclassname = 'list_item';

    /** @var array of $listitemclassname objects. */
    public $items = array();

    /** @var string 'ol' or 'ul'. */
    public $type;

    /** @var list_item or derived class. */
    public $parentitem = null;
    public $table;
    public $fieldnamesparent = 'parent';

    /** @var array Records from db, only used in top level list. */
    public $records = array();

    public $editable;

    /** @var array keys are child ids, values are parents. */
    public $childparent;

//------------------------------------------------------
//vars used for pagination.
    public $page = 0; // 0 means no pagination
    public $firstitem = 1;
    public $lastitem = 999999;
    public $pagecount;
    public $paged = false;
    public $offset = 0;
//------------------------------------------------------
    public $pageurl;
    public $pageparamname;

    /**
     * Constructor.
     *
     * @param string $type
     * @param string $attributes
     * @param boolean $editable
     * @param moodle_url $pageurl url for this page
     * @param integer $page if 0 no pagination. (These three params only used in top level list.)
     * @param string $pageparamname name of url param that is used for passing page no
     * @param integer $itemsperpage no of top level items.
     */
    public function __construct($type='ul', $attributes='', $editable = false, $pageurl=null, $page = 0, $pageparamname = 'page', $itemsperpage = 20) {
        global $PAGE;

        $this->editable = $editable;
        $this->attributes = $attributes;
        $this->type = $type;
        $this->page = $page;
        $this->pageparamname = $pageparamname;
        $this->itemsperpage = $itemsperpage;
        if ($pageurl === null) {
            $this->pageurl = new moodle_url($PAGE->url);
            $this->pageurl->params(array($this->pageparamname => $this->page));
        } else {
            $this->pageurl = $pageurl;
        }
    }

    /**
     * Returns html string.
     *
     * @param integer $indent depth of indentation.
     */
    public function to_html($indent=0, $extraargs=array()) {
        if (count($this->items)) {
            $tabs = str_repeat("\t", $indent);
            $first = true;
            $itemiter = 1;
            $lastitem = '';
            $html = '';

            foreach ($this->items as $item) {
                $last = (count($this->items) == $itemiter);
                if ($this->editable) {
                    $item->set_icon_html($first, $last, $lastitem);
                }
                if ($itemhtml = $item->to_html($indent+1, $extraargs)) {
                    $html .= "$tabs\t<li".((!empty($item->attributes))?(' '.$item->attributes):'').">";
                    $html .= $itemhtml;
                    $html .= "</li>\n";
                }
                $first = false;
                $lastitem = $item;
                $itemiter++;
            }
        } else {
            $html = '';
        }
        if ($html) { //if there are list items to display then wrap them in ul / ol tag.
            $tabs = str_repeat("\t", $indent);
            $html = $tabs.'<'.$this->type.((!empty($this->attributes))?(' '.$this->attributes):'').">\n".$html;
            $html .= $tabs."</".$this->type.">\n";
        } else {
            $html ='';
        }
        return $html;
    }

    /**
     * Recurse down the tree and find an item by it's id.
     *
     * @param integer $id
     * @param boolean $suppresserror error if not item found?
     * @return list_item *copy* or null if item is not found
     */
    public function find_item($id, $suppresserror = false) {
        if (isset($this->items)) {
            foreach ($this->items as $key => $child) {
                if ($child->id == $id) {
                    return $this->items[$key];
                }
            }
            foreach (array_keys($this->items) as $key) {
                $thischild = $this->items[$key];
                $ref = $thischild->children->find_item($id, true);//error always reported at top level
                if ($ref !== null) {
                    return $ref;
                }
            }
        }

        if (!$suppresserror) {
            print_error('listnoitem');
        }
        return null;
    }

    public function add_item($item) {
        $this->items[] = $item;
    }

    public function set_parent($parent) {
        $this->parentitem = $parent;
    }

    /**
     * Produces a hierarchical tree of list items from a flat array of records.
     * 'parent' field is expected to point to a parent record.
     * records are already sorted.
     * If the parent field doesn't point to another record in the array then this is
     * a top level list
     *
     * @param integer $offset how many list toplevel items are there in lists before this one
     * @return array(boolean, integer) whether there is more than one page, $offset + how many toplevel items where there in this list.
     *
     */
    public function list_from_records($paged = false, $offset = 0) {
        $this->paged = $paged;
        $this->offset = $offset;
        $this->get_records();
        $records = $this->records;
        $page = $this->page;
        if (!empty($page)) {
            $this->firstitem = ($page - 1) * $this->itemsperpage;
            $this->lastitem = $this->firstitem + $this->itemsperpage - 1;
        }
        $itemiter = $offset;
        //make a simple array which is easier to search
        $this->childparent = array();
        foreach ($records as $record) {
            $this->childparent[$record->id] = $record->parent;
        }

        //create top level list items and they're responsible for creating their children
        foreach ($records as $record) {
            if (array_key_exists($record->parent, $this->childparent)) {
                // This record is a child of another record, so it will be dealt
                // with by a call to list_item::create_children, not here.
                continue;
            }

            $inpage = $itemiter >= $this->firstitem && $itemiter <= $this->lastitem;

            // Make list item for top level for all items
            // we need the info about the top level items for reordering peers.
            if ($this->parentitem !== null) {
                $newattributes = $this->parentitem->attributes;
            } else {
                $newattributes = '';
            }

            $this->items[$itemiter] = new $this->listitemclassname($record, $this, $newattributes, $inpage);

            if ($inpage) {
                $this->items[$itemiter]->create_children($records, $this->childparent, $record->id);
            } else {
                // Don't recurse down the tree for items that are not on this page
                $this->paged = true;
            }

            $itemiter++;
        }
        return array($this->paged, $itemiter);
    }

    /**
     * Should be overriden to return an array of records of list items.
     */
    public abstract function get_records();

    /**
     * display list of page numbers for navigation
     */
    public function display_page_numbers() {
        $html = '';
        $topcount = count($this->items);
        $this->pagecount = (integer) ceil(($topcount + $this->offset)/ QUESTION_PAGE_LENGTH );
        if (!empty($this->page) && ($this->paged)) {
            $html = "<div class=\"paging\">".get_string('page').":\n";
            foreach (range(1,$this->pagecount) as $currentpage) {
                if ($this->page == $currentpage) {
                    $html .= " $currentpage \n";
                }
                else {
                    $html .= "<a href=\"".$this->pageurl->out(true, array($this->pageparamname => $currentpage))."\">";
                    $html .= " $currentpage </a>\n";
                }
            }
            $html .= "</div>";
        }
        return $html;
    }

    /**
     * Returns an array of ids of peers of an item.
     *
     * @param    int itemid - if given, restrict records to those with this parent id.
     * @return   array peer ids
     */
    public function get_items_peers($itemid) {
        $itemref = $this->find_item($itemid);
        $peerids = $itemref->parentlist->get_child_ids();
        return $peerids;
    }

    /**
     * Returns an array of ids of child items.
     *
     * @return   array peer ids
     */
    public function get_child_ids() {
        $childids = array();
        foreach ($this->items as $child) {
           $childids[] = $child->id;
        }
        return $childids;
    }

    /**
     * Returns the value to be used as the parent for the $item when it goes to the top level.
     * Override if needed.
     *
     * @param list_item $item The item which its top level parent is going to be returned.
     * @return int
     */
    public function get_top_level_parent_id($item) {
        return 0; // Top level items have no parent.
    }

    /**
     * Move a record up or down
     *
     * @param string $direction up / down
     * @param integer $id
     */
    public function move_item_up_down($direction, $id) {
        $peers = $this->get_items_peers($id);
        $itemkey = array_search($id, $peers);
        switch ($direction) {
            case 'down' :
                if (isset($peers[$itemkey+1])) {
                    $olditem = $peers[$itemkey+1];
                    $peers[$itemkey+1] = $id;
                    $peers[$itemkey] = $olditem;
                } else {
                    print_error('listcantmoveup');
                }
                break;

            case 'up' :
                if (isset($peers[$itemkey-1])) {
                    $olditem = $peers[$itemkey-1];
                    $peers[$itemkey-1] = $id;
                    $peers[$itemkey] = $olditem;
                } else {
                    print_error('listcantmovedown');
                }
                break;
        }
        $this->reorder_peers($peers);
    }

    public function reorder_peers($peers) {
        global $DB;
        foreach ($peers as $key => $peer) {
            $DB->set_field($this->table, "sortorder", $key, array("id"=>$peer));
        }
    }

    /**
     * Moves the item one step up in the tree.
     *
     * @param int $id an item index.
     * @return list_item the item that used to be the parent of the item moved.
     */
    public function move_item_left($id) {
        global $DB;

        $item = $this->find_item($id);
        if (!isset($item->parentlist->parentitem->parentlist)) {
            print_error('listcantmoveleft');
        } else {
            $newpeers = $this->get_items_peers($item->parentlist->parentitem->id);
            if (isset($item->parentlist->parentitem->parentlist->parentitem)) {
                $newparent = $item->parentlist->parentitem->parentlist->parentitem->id;
            } else {
                $newparent = $this->get_top_level_parent_id($item);
            }
            $DB->set_field($this->table, "parent", $newparent, array("id"=>$item->id));
            $oldparentkey = array_search($item->parentlist->parentitem->id, $newpeers);
            $neworder = array_merge(array_slice($newpeers, 0, $oldparentkey+1), array($item->id), array_slice($newpeers, $oldparentkey+1));
            $this->reorder_peers($neworder);
        }
        return $item->parentlist->parentitem;
    }

    /**
     * Make item with id $id the child of the peer that is just above it in the sort order.
     *
     * @param integer $id
     */
    public function move_item_right($id) {
        global $DB;

        $peers = $this->get_items_peers($id);
        $itemkey = array_search($id, $peers);
        if (!isset($peers[$itemkey-1])) {
            print_error('listcantmoveright');
        } else {
            $DB->set_field($this->table, "parent", $peers[$itemkey-1], array("id"=>$peers[$itemkey]));
            $newparent = $this->find_item($peers[$itemkey-1]);
            if (isset($newparent->children)) {
                $newpeers = $newparent->children->get_child_ids();
            }
            if ($newpeers) {
                $newpeers[] = $peers[$itemkey];
                $this->reorder_peers($newpeers);
            }
        }
    }

    /**
     * process any actions.
     *
     * @param integer $left id of item to move left
     * @param integer $right id of item to move right
     * @param integer $moveup id of item to move up
     * @param integer $movedown id of item to move down
     * @return unknown
     */
    public function process_actions($left, $right, $moveup, $movedown) {
        //should this action be processed by this list object?
        if (!(array_key_exists($left, $this->records) || array_key_exists($right, $this->records) || array_key_exists($moveup, $this->records) || array_key_exists($movedown, $this->records))) {
            return false;
        }
        if (!empty($left)) {
            $oldparentitem = $this->move_item_left($left);
            if ($this->item_is_last_on_page($oldparentitem->id)) {
                // Item has jumped onto the next page, change page when we redirect.
                $this->page ++;
                $this->pageurl->params(array($this->pageparamname => $this->page));
            }
        } else if (!empty($right)) {
            $this->move_item_right($right);
            if ($this->item_is_first_on_page($right)) {
                // Item has jumped onto the previous page, change page when we redirect.
                $this->page --;
                $this->pageurl->params(array($this->pageparamname => $this->page));
            }
        } else if (!empty($moveup)) {
            $this->move_item_up_down('up', $moveup);
            if ($this->item_is_first_on_page($moveup)) {
                // Item has jumped onto the previous page, change page when we redirect.
                $this->page --;
                $this->pageurl->params(array($this->pageparamname => $this->page));
            }
        } else if (!empty($movedown)) {
            $this->move_item_up_down('down', $movedown);
            if ($this->item_is_last_on_page($movedown)) {
                // Item has jumped onto the next page, change page when we redirect.
                $this->page ++;
                $this->pageurl->params(array($this->pageparamname => $this->page));
            }
        } else {
            return false;
        }

        redirect($this->pageurl);
    }

    /**
     * @param integer $itemid an item id.
     * @return boolean Is the item with the given id the first top-level item on
     * the current page?
     */
    public function item_is_first_on_page($itemid) {
        return $this->page && isset($this->items[$this->firstitem]) &&
                $itemid == $this->items[$this->firstitem]->id;
    }

    /**
     * @param integer $itemid an item id.
     * @return boolean Is the item with the given id the last top-level item on
     * the current page?
     */
    public function item_is_last_on_page($itemid) {
        return $this->page && isset($this->items[$this->lastitem]) &&
                $itemid == $this->items[$this->lastitem]->id;
    }
}

/**
 * @package moodlecore
 * @copyright Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class list_item {
    /** @var integer id of record, used if list is editable. */
    public $id;

    /** @var string name of this item, used if list is editable. */
    public $name;

    /** @var mixed The object or string representing this item. */
    public $item;
    public $fieldnamesname = 'name';
    public $attributes;
    public $display;
    public $icons = array();

    /** @var moodle_list */
    public $parentlist;

    /** @var moodle_list Set if there are any children of this listitem. */
    public $children;

    /**
     * Constructor
     * @param mixed $item fragment of html for list item or record
     * @param object $parent reference to parent of this item
     * @param string $attributes attributes for li tag
     * @param boolean $display whether this item is displayed. Some items may be loaded so we have a complete
     *                              structure in memory to work with for actions but are not displayed.
     * @return list_item
     */
    public function __construct($item, $parent, $attributes = '', $display = true) {
        $this->item = $item;
        if (is_object($this->item)) {
            $this->id = $this->item->id;
            $this->name = $this->item->{$this->fieldnamesname};
        }
        $this->set_parent($parent);
        $this->attributes = $attributes;
        $parentlistclass = get_class($parent);
        $this->children = new $parentlistclass($parent->type, $parent->attributes, $parent->editable, $parent->pageurl, 0);
        $this->children->set_parent($this);
        $this->display = $display;
    }

    /**
     * Output the html just for this item. Called by to_html which adds html for children.
     *
     */
    public function item_html($extraargs = array()) {
        if (is_string($this->item)) {
            $html = $this->item;
        } elseif (is_object($this->item)) {
            //for debug purposes only. You should create a sub class to
            //properly handle the record
            $html = join(', ', (array)$this->item);
        }
        return $html;
    }

    /**
     * Returns html
     *
     * @param integer $indent
     * @param array $extraargs any extra data that is needed to print the list item
     *                            may be used by sub class.
     * @return string html
     */
    public function to_html($indent = 0, $extraargs = array()) {
        if (!$this->display) {
            return '';
        }
        $tabs = str_repeat("\t", $indent);

        if (isset($this->children)) {
            $childrenhtml = $this->children->to_html($indent+1, $extraargs);
        } else {
            $childrenhtml = '';
        }
        return $this->item_html($extraargs).'&nbsp;'.(join('', $this->icons)).(($childrenhtml !='')?("\n".$childrenhtml):'');
    }

    public function set_icon_html($first, $last, $lastitem) {
        global $CFG;
        $strmoveup = get_string('moveup');
        $strmovedown = get_string('movedown');
        $strmoveleft = get_string('maketoplevelitem', 'question');

        if (right_to_left()) {   // Exchange arrows on RTL
            $rightarrow = 'left';
            $leftarrow  = 'right';
        } else {
            $rightarrow = 'right';
            $leftarrow  = 'left';
        }

        if (isset($this->parentlist->parentitem)) {
            $parentitem = $this->parentlist->parentitem;
            if (isset($parentitem->parentlist->parentitem)) {
                $action = get_string('makechildof', 'question', $parentitem->parentlist->parentitem->name);
            } else {
                $action = $strmoveleft;
            }
            $url = new moodle_url($this->parentlist->pageurl, (array('sesskey'=>sesskey(), 'left'=>$this->id)));
            $this->icons['left'] = $this->image_icon($action, $url, $leftarrow);
        } else {
            $this->icons['left'] =  $this->image_spacer();
        }

        if (!$first) {
            $url = new moodle_url($this->parentlist->pageurl, (array('sesskey'=>sesskey(), 'moveup'=>$this->id)));
            $this->icons['up'] = $this->image_icon($strmoveup, $url, 'up');
        } else {
            $this->icons['up'] =  $this->image_spacer();
        }

        if (!$last) {
            $url = new moodle_url($this->parentlist->pageurl, (array('sesskey'=>sesskey(), 'movedown'=>$this->id)));
            $this->icons['down'] = $this->image_icon($strmovedown, $url, 'down');
        } else {
            $this->icons['down'] =  $this->image_spacer();
        }

        if (!empty($lastitem)) {
            $makechildof = get_string('makechildof', 'question', $lastitem->name);
            $url = new moodle_url($this->parentlist->pageurl, (array('sesskey'=>sesskey(), 'right'=>$this->id)));
            $this->icons['right'] = $this->image_icon($makechildof, $url, $rightarrow);
        } else {
            $this->icons['right'] =  $this->image_spacer();
        }
    }

    public function image_icon($action, $url, $icon) {
        global $OUTPUT;
        return '<a title="' . s($action) .'" href="'.$url.'">' .
                $OUTPUT->pix_icon('t/' . $icon, $action) . '</a> ';
    }

    public function image_spacer() {
        global $OUTPUT;
        return $OUTPUT->spacer();
    }

    /**
     * Recurse down tree creating list_items, called from moodle_list::list_from_records
     *
     * @param array $records
     * @param array $children
     * @param integer $thisrecordid
     */
    public function create_children(&$records, &$children, $thisrecordid) {
        //keys where value is $thisrecordid
        $thischildren = array_keys($children, $thisrecordid);
        foreach ($thischildren as $child) {
            $thisclass = get_class($this);
            $newlistitem = new $thisclass($records[$child], $this->children, $this->attributes);
            $this->children->add_item($newlistitem);
            $newlistitem->create_children($records, $children, $records[$child]->id);
        }
    }

    public function set_parent($parent) {
        $this->parentlist = $parent;
    }
}
