<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
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
 * Classes for displaying and editing a nested list of items.
 *
 * Handles functionality for :
 *
 *    Construction of nested list from db records with some key pointing to a parent id.
 *    Display of list with or without editing icons with optional pagination.
 *    Reordering of items works across pages.
 *    Processing of editing actions on list.
 *
 * @author Jamie Pratt
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

/**
 * Clues to reading this code:
 *
 * The functions that move things around the tree structure just update the
 * database - they don't update the in-memory structure, instead they trigger a
 * page reload so everything is rebuilt from scratch.
 */
class moodle_list {
    var $attributes;
    var $listitemclassname = 'list_item';
    /**
     * An array of $listitemclassname objects.
     * @var array
     */
    var $items = array();
    /**
     * ol / ul
     * @var string
     */
    var $type;
    /**
     * @var list_item or derived class
     */
    var $parentitem = null;
    var $table;
    var $fieldnamesparent = 'parent';
    var $sortby = 'parent, sortorder, name';
    /**
     * Records from db, only used in top level list.
     * @var array
     */
    var $records = array();

    var $editable;

    /**
     * Key is child id, value is parent.
     * @var array
     */
    var $childparent;

//------------------------------------------------------
//vars used for pagination.
    var $page = 0;// 0 means no pagination
    var $firstitem = 1;
    var $lastitem = 999999;
    var $pagecount;
    var $paged = false;
    var $offset = 0;
//------------------------------------------------------
    var $pageurl;
    var $pageparamname;

    /**
     * Constructor function
     *
     * @param string $type
     * @param string $attributes
     * @param boolean $editable
     * @param moodle_url $pageurl url for this page
     * @param integer $page if 0 no pagination. (These three params only used in top level list.)
     * @param string $pageparamname name of url param that is used for passing page no
     * @param integer $itemsperpage no of top level items.
     * @return moodle_list
     */
    function moodle_list($type='ul', $attributes='', $editable = false, $pageurl=null, $page = 0, $pageparamname = 'page', $itemsperpage = 20) {
        $this->editable = $editable;
        $this->attributes = $attributes;
        $this->type = $type;
        $this->page = $page;
        $this->pageparamname = $pageparamname;
        $this->itemsperpage = $itemsperpage;
        if ($pageurl === null) {
            $this->pageurl = new moodle_url();
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
    function to_html($indent=0, $extraargs=array()) {
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
    function find_item($id, $suppresserror = false) {
        if (isset($this->items)) {
            foreach ($this->items as $key => $child) {
                if ($child->id == $id) {
                    return $this->items[$key];
                }
            }
            foreach (array_keys($this->items) as $key) {
                $thischild =& $this->items[$key];
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

    function add_item(&$item) {
        $this->items[] =& $item;
    }

    function set_parent(&$parent) {
        $this->parentitem =& $parent;
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
    function list_from_records($paged = false, $offset = 0) {
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
            if (!array_key_exists($record->parent, $this->childparent)) {
                //if this record is not a child of another record then

                $inpage = ($itemiter >= $this->firstitem && $itemiter <= $this->lastitem);
                //make list item for top level for all items
                //we need the info about the top level items for reordering peers.
                if ($this->parentitem!==null) {
                    $newattributes = $this->parentitem->attributes;
                } else {
                    $newattributes = '';

                }
                $this->items[$itemiter] =& new $this->listitemclassname($record, $this, $newattributes, $inpage);
                if ($inpage) {
                    $this->items[$itemiter]->create_children($records, $this->childparent, $record->id);
                } else {
                    //don't recurse down the tree for items that are not on this page
                    $this->paged = true;
                }
                $itemiter++;
            }
        }
        return array($this->paged, $itemiter);
    }

    /**
     * Should be overriden to return an array of records of list items.
     *
     */
    function get_records() {
    }

    /**
     * display list of page numbers for navigation
     */
    function display_page_numbers() {
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
                    $html .= "<a href=\"".$this->pageurl->out(false, array($this->pageparamname => $currentpage))."\">";
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
    function get_items_peers($itemid) {
        $itemref = $this->find_item($itemid);
        $peerids = $itemref->parentlist->get_child_ids();
        return $peerids;
    }

    /**
     * Returns an array of ids of child items.
     *
     * @return   array peer ids
     */
    function get_child_ids() {
        $childids = array();
        foreach ($this->items as $child) {
           $childids[] = $child->id;
        }
        return $childids;
    }

    /**
     * Move a record up or down
     *
     * @param string $direction up / down
     * @param integer $id
     */
    function move_item_up_down($direction, $id) {
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

    function reorder_peers($peers) {
        foreach ($peers as $key => $peer) {
            if (!set_field("{$this->table}", "sortorder", $key, "id", $peer)) {
                print_error('listupdatefail');
            }
        }
    }

    /**
     * @param integer $id an item index.
     * @return object the item that used to be the parent of the item moved.
     */
    function move_item_left($id) {
        $item = $this->find_item($id);
        if (!isset($item->parentlist->parentitem->parentlist)) {
            print_error('listcantmoveleft');
        } else {
            $newpeers = $this->get_items_peers($item->parentlist->parentitem->id);
            if (isset($item->parentlist->parentitem->parentlist->parentitem)) {
                $newparent = $item->parentlist->parentitem->parentlist->parentitem->id;
            } else {
                $newparent = 0; // top level item
            }
            if (!set_field("{$this->table}", "parent", $newparent, "id", $item->id)) {
                print_error('listupdatefail');
            } else {
                $oldparentkey = array_search($item->parentlist->parentitem->id, $newpeers);
                $neworder = array_merge(array_slice($newpeers, 0, $oldparentkey+1), array($item->id), array_slice($newpeers, $oldparentkey+1));
                $this->reorder_peers($neworder);
            }
        }
        return $item->parentlist->parentitem;
    }

    /**
     * Make item with id $id the child of the peer that is just above it in the sort order.
     *
     * @param integer $id
     */
    function move_item_right($id) {
        $peers = $this->get_items_peers($id);
        $itemkey = array_search($id, $peers);
        if (!isset($peers[$itemkey-1])) {
            print_error('listcantmoveright');
        } else {
            if (!set_field("{$this->table}", "parent", $peers[$itemkey-1], "id", $peers[$itemkey])) {
                print_error('listupdatefail');
            } else {
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
    function process_actions($left, $right, $moveup, $movedown) {
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

        redirect($this->pageurl->out());
    }

    /**
     * @param integer $itemid an item id.
     * @return boolean Is the item with the given id the first top-level item on
     * the current page?
     */
    function item_is_first_on_page($itemid) {
        return $this->page && isset($this->items[$this->firstitem]) &&
                $itemid == $this->items[$this->firstitem]->id;
    }

    /**
     * @param integer $itemid an item id.
     * @return boolean Is the item with the given id the last top-level item on
     * the current page?
     */
    function item_is_last_on_page($itemid) {
        return $this->page && isset($this->items[$this->lastitem]) &&
                $itemid == $this->items[$this->lastitem]->id;
    }
}

class list_item {
    /**
     * id of record, used if list is editable
     * @var integer
     */
    var $id;
    /**
     * name of this item, used if list is editable
     * @var string
     */
    var $name;
    /**
     * The object or string representing this item.
     * @var mixed
     */
    var $item;
    var $fieldnamesname = 'name';
    var $attributes;
    var $display;
    var $icons = array();
    /**
     * @var moodle_list
     */
    var $parentlist;
    /**
     * Set if there are any children of this listitem.
     * @var moodle_list
     */
    var $children;

    /**
     * Constructor
     * @param mixed $item fragment of html for list item or record
     * @param object &$parent reference to parent of this item
     * @param string $attributes attributes for li tag
     * @param boolean $display whether this item is displayed. Some items may be loaded so we have a complete
     *                              structure in memory to work with for actions but are not displayed.
     * @return list_item
     */
    function list_item($item, &$parent, $attributes='', $display = true) {
        $this->item = $item;
        if (is_object($this->item)) {
            $this->id = $this->item->id;
            $this->name = $this->item->{$this->fieldnamesname};
        }
        $this->set_parent($parent);
        $this->attributes = $attributes;
        $parentlistclass = get_class($parent);
        $this->children =& new $parentlistclass($parent->type, $parent->attributes, $parent->editable, $parent->pageurl, 0);
        $this->children->set_parent($this);
        $this->display = $display;
    }

    /**
     * Output the html just for this item. Called by to_html which adds html for children.
     *
     */
    function item_html($extraargs = array()) {
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
    function to_html($indent=0, $extraargs = array()) {
        if (!$this->display) {
            return '';
        }
        $tabs = str_repeat("\t", $indent);

        if (isset($this->children)) {
            $childrenhtml = $this->children->to_html($indent+1, $extraargs);
        } else {
            $childrenhtml = '';
        }
        return $this->item_html($extraargs).'&nbsp;'.(join($this->icons, '')).(($childrenhtml !='')?("\n".$childrenhtml):'');
    }

    function set_icon_html($first, $last, &$lastitem) {
        global $CFG;
        $strmoveup = get_string('moveup');
        $strmovedown = get_string('movedown');
        $strmoveleft = get_string('maketoplevelitem', 'question');
        $pixpath = $CFG->pixpath;

        if (isset($this->parentlist->parentitem)) {
            $parentitem =& $this->parentlist->parentitem;
            if (isset($parentitem->parentlist->parentitem)) {
                $action = get_string('makechildof', 'question', $parentitem->parentlist->parentitem->name);
            } else {
                $action = $strmoveleft;
            }
            $this->icons['left'] = $this->image_icon($action, $this->parentlist->pageurl->out_action(array('left'=>$this->id)), 'left');
        } else {
            $this->icons['left'] =  $this->image_spacer();
        }

        if (!$first) {
            $this->icons['up'] = $this->image_icon($strmoveup, $this->parentlist->pageurl->out_action(array('moveup'=>$this->id)), 'up');
        } else {
            $this->icons['up'] =  $this->image_spacer();
        }

        if (!$last) {
            $this->icons['down'] = $this->image_icon($strmovedown, $this->parentlist->pageurl->out_action(array('movedown'=>$this->id)), 'down');
        } else {
            $this->icons['down'] =  $this->image_spacer();
        }

        if (!empty($lastitem)) {
            $makechildof = get_string('makechildof', 'question', $lastitem->name);
            $this->icons['right'] = $this->image_icon($makechildof, $this->parentlist->pageurl->out_action(array('right'=>$this->id)), 'right');
        } else {
            $this->icons['right'] =  $this->image_spacer();
        }
    }

    function image_icon($action, $url, $icon) {
        global $CFG;
        $pixpath = $CFG->pixpath;
        return '<a title="' . $action .'" href="'.$url.'">
                <img src="' . $pixpath . '/t/'.$icon.'.gif" class="iconsmall" alt="' . $action. '" /></a> ';
    }

    function image_spacer() {
        global $CFG;
        $pixpath = $CFG->pixpath;
        return '<img src="' . $pixpath . '/spacer.gif" class="iconsmall" alt="" />';
    }

    /**
     * Recurse down tree creating list_items, called from moodle_list::list_from_records
     *
     * @param array $records
     * @param array $children
     * @param integer $thisrecordid
     */
    function create_children(&$records, &$children, $thisrecordid) {
        //keys where value is $thisrecordid
        $thischildren = array_keys($children, $thisrecordid);
        if (count($thischildren)) {
            foreach ($thischildren as $child) {
                $thisclass = get_class($this);
                $newlistitem =& new $thisclass($records[$child], $this->children, $this->attributes);
                $this->children->add_item($newlistitem);
                $newlistitem->create_children($records, $children, $records[$child]->id);
            }
        }
    }

    function set_parent(&$parent) {
        $this->parentlist =& $parent;
    }
}
?>
