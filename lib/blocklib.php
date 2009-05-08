<?php //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
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
 * This library includes all the necessary stuff to use blocks on pages in Moodle.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package pages
 */

define('BLOCK_MOVE_LEFT',   0x01);
define('BLOCK_MOVE_RIGHT',  0x02);
define('BLOCK_MOVE_UP',     0x04);
define('BLOCK_MOVE_DOWN',   0x08);
define('BLOCK_CONFIGURE',   0x10);

define('BLOCK_POS_LEFT',  'side-pre');
define('BLOCK_POS_RIGHT', 'side-post');

define('BLOCKS_PINNED_TRUE',0);
define('BLOCKS_PINNED_FALSE',1);
define('BLOCKS_PINNED_BOTH',2);

require_once($CFG->libdir.'/pagelib.php');

class block_not_on_page_exception extends moodle_exception {
    public function __construct($instanceid, $page) {
        $a = new stdClass;
        $a->instanceid = $instanceid;
        $a->url = $page->url;
        parent::__construct('blockdoesnotexistonpage', '', $page->url, $a);
    }
}

/**
 * This class keeps track of the block that should appear on a moodle_page.
 * The page to work with as passed to the constructor.
 * The only fields of moodle_page that is uses are ->context, ->pagetype and
 * ->subpage, so instead of passing a full moodle_page object, you may also
 * pass a stdClass object with those three fields. These field values are read
 * only at the point that the load_blocks() method is called. It is the caller's
 * responsibility to ensure that those fields do not subsequently change.
 *
 *
 * Note about the weird 'implements ArrayAccess' part of the declaration:
 *
 * ArrayAccess is a magic PHP5 thing. If your class implements the ArrayAccess
 * interface, then other code can use the $object[$index] syntax, and it will
 * call the offsetGet method of the object.
 * See http://php.net/manual/en/class.arrayaccess.php
 *
 * So, why do we do this here? Basically, some of the deprecated blocks methods
 * like blocks_setup used to return an array of blocks on the page, with array
 * keys BLOCK_POS_LEFT, BLOCK_POS_RIGHT. We can keep legacy code that calls those
 * deprecated functions mostly working by changing blocks_setup to return the
 * block_manger object, and then use 'implements ArrayAccess' so that the right
 * thing happens when legacy code does something like $pageblocks[BLOCK_POS_LEFT].
 */
class block_manager implements ArrayAccess {

/// Field declarations =========================================================

    protected $page;

    protected $regions = array();

    protected $defaultregion;

    protected $allblocks = null; // Will be get_records('blocks');

    protected $addableblocks = null; // Will be a subset of $allblocks.

    /**
     * Will be an array region-name => array(db rows loaded in load_blocks);
     */
    protected $birecordsbyregion = null;

    /**
     * array region-name => array(block objects); populated as necessary by
     * the ensure_instances_exist method.
     */
    protected $blockinstances = array();

    /**
     * array region-name => array(block_content objects) what acutally needs to
     * be displayed in each region.
     */
    protected $visibleblockcontent = array();

/// Constructor ================================================================

    /**
     * Constructor.
     * @param object $page the moodle_page object object we are managing the blocks for,
     * or a reasonable faxilimily. (See the comment at the top of this classe
     * and http://en.wikipedia.org/wiki/Duck_typing)
     */
    public function __construct($page) {
        $this->page = $page;
    }

/// Getter methods =============================================================

    /**
     * @return array the internal names of the regions on this page where block may appear.
     */
    public function get_regions() {
        return array_keys($this->regions);
    }

    /**
     * @return string the internal names of the region where new blocks are added
     * by default, and where any blocks from an unrecognised region are shown.
     * (Imagine that blocks were added with one theme selected, then you switched
     * to a theme with different block positions.)
     */
    public function get_default_region() {
        return $this->defaultregion;
    }

    /**
     * The list of block types that may be added to this page.
     * @return array block id => record from block table.
     */
    public function get_addable_blocks() {
        $this->check_is_loaded();

        if (!is_null($this->addableblocks)) {
            return $this->addableblocks;
        }

        // Lazy load.
        $this->addableblocks = array();

        $allblocks = blocks_get_record();
        if (empty($allblocks)) {
            return $this->addableblocks;
        }

        $pageformat = $this->page->pagetype;
        foreach($allblocks as $block) {
            if ($block->visible &&
                    (block_method_result($block->name, 'instance_allow_multiple') || !$this->is_block_present($block->id)) &&
                    blocks_name_allowed_in_format($block->name, $pageformat)) {
                $this->addableblocks[$block->id] = $block;
            }
        }

        return $this->addableblocks;
    }

    public function is_block_present($blocktypeid) {
        // TODO
    }

    /**
     * @param string $blockname the name of ta type of block.
     * @param boolean $includeinvisible if false (default) only check 'visible' blocks, that is, blocks enabled by the admin.
     * @return boolean true if this block in installed.
     */
    public function is_known_block_type($blockname, $includeinvisible = false) {
        $blocks = $this->get_installed_blocks();
        foreach ($blocks as $block) {
            if ($block->name == $blockname && ($includeinvisible || $block->visible)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $region a region name
     * @return boolean true if this retion exists on this page.
     */
    public function is_known_region($region) {
        return array_key_exists($region, $this->regions);
    }

    /**
     * @param $region a block region that exists on this page.
     * @return array of block instances.
     */
    public function get_blocks_for_region($region) {
        $this->check_is_loaded();
        $this->ensure_instances_exist($region);
        return $this->blockinstances[$region];
    }

    /**
     * @param $region a block region that exists on this page.
     * @return array of block block_content objects for all the blocks in a region.
     */
    public function get_content_for_region($region) {
        $this->check_is_loaded();
        $this->ensure_content_created($region);
        return $this->visibleblockcontent[$region];
    }

    /**
     * Get the list of all installed blocks.
     * @return array contents of the block table.
     */
    public function get_installed_blocks() {
        global $DB;
        if (is_null($this->allblocks)) {
            $this->allblocks = $DB->get_records('block');
        }
        return $this->allblocks;
    }

/// Setter methods =============================================================

    /**
     * @param string $region add a named region where blocks may appear on the
     * current page. This is an internal name, like 'side-pre', not a string to
     * display in the UI.
     */
    public function add_region($region) {
        $this->check_not_yet_loaded();
        $this->regions[$region] = 1;
    }

    /**
     * @param array $regions this utility method calls add_region for each array element.
     */
    public function add_regions($regions) {
        foreach ($regions as $region) {
            $this->add_region($region);
        }
    }

    /**
     * @param string $defaultregion the internal names of the region where new
     * blocks should be added by default, and where any blocks from an
     * unrecognised region are shown.
     */
    public function set_default_region($defaultregion) {
        $this->check_not_yet_loaded();
        $this->check_region_is_known($defaultregion);
        $this->defaultregion = $defaultregion;
    }

/// Actions ====================================================================

    /**
     * This method actually loads the blocks for our page from the database.
     */
    public function load_blocks($includeinvisible = NULL) {
        global $DB, $CFG;
        if (!is_null($this->birecordsbyregion)) {
            // Already done.
            return;
        }

        if ($CFG->version < 2009050619) {
            // Upgrade/install not complete. Don't try too show any blocks.
            $this->birecordsbyregion = array();
            return;
        }

        if (is_null($includeinvisible)) {
            $includeinvisible = $this->page->user_is_editing();
        }
        if ($includeinvisible) {
            $visiblecheck = 'AND (bp.visible = 1 OR bp.visible IS NULL)';
        } else {
            $visiblecheck = '';
        }

        $context = $this->page->context;
        $contexttest = 'bi.contextid = :contextid2';
        $parentcontextparams = array();
        $parentcontextids = get_parent_contexts($context);
        if ($parentcontextids) {
            list($parentcontexttest, $parentcontextparams) =
                    $DB->get_in_or_equal($parentcontextids, SQL_PARAMS_NAMED, 'parentcontext0000');
            $contexttest = "($contexttest OR (bi.showinsubcontexts = 1 AND bi.contextid $parentcontexttest))";
        }

        $pagetypepatterns = $this->matching_page_type_patterns($this->page->pagetype);
        list($pagetypepatterntest, $pagetypepatternparams) =
                $DB->get_in_or_equal($pagetypepatterns, SQL_PARAMS_NAMED, 'pagetypepatterntest0000');

        $params = array(
            'subpage1' => $this->page->subpage,
            'subpage2' => $this->page->subpage,
            'contextid1' => $context->id,
            'contextid2' => $context->id,
            'pagetype' => $this->page->pagetype,
        );
        $sql = "SELECT
                    bi.id,
                    bi.blockname,
                    bi.contextid,
                    bi.showinsubcontexts,
                    bi.pagetypepattern,
                    bi.subpagepattern,
                    COALESCE(bp.visible, 1) AS visible,
                    COALESCE(bp.region, bi.defaultregion) AS region,
                    COALESCE(bp.weight, bi.defaultweight) AS weight,
                    bi.configdata

                FROM {block_instances} bi
                JOIN {block} b ON bi.blockname = b.name
                LEFT JOIN {block_positions} bp ON bp.blockinstanceid = bi.id
                                                  AND bp.contextid = :contextid1
                                                  AND bp.pagetype = :pagetype
                                                  AND bp.subpage = :subpage1

                WHERE
                $contexttest
                AND bi.pagetypepattern $pagetypepatterntest
                AND (bi.subpagepattern IS NULL OR bi.subpagepattern = :subpage2)
                $visiblecheck
                AND b.visible = 1

                ORDER BY
                    COALESCE(bp.region, bi.defaultregion),
                    COALESCE(bp.weight, bi.defaultweight),
                    bi.id";
        $blockinstances = $DB->get_recordset_sql($sql, $params + $parentcontextparams + $pagetypepatternparams);

        $this->birecordsbyregion = $this->prepare_per_region_arrays();
        $unknown = array();
        foreach ($blockinstances as $bi) {
            if ($this->is_known_region($bi->region)) {
                $this->birecordsbyregion[$bi->region][] = $bi;
            } else {
                $unknown[] = $bi;
            }
        }
        $this->birecordsbyregion[$this->defaultregion] = array_merge($this->birecordsbyregion[$this->defaultregion], $unknown);
    }

    /**
     * Add a block to the current page, or related pages. The block is added to
     * context $this->page->contextid. If $pagetypepattern $subpagepattern
     * @param string $blockname The type of block to add.
     * @param string $region the block region on this page to add the block to.
     * @param integer $weight determines the order where this block appears in the region.
     * @param boolean $showinsubcontexts whether this block appears in subcontexts, or just the current context.
     * @param string|null $pagetypepattern which page types this block should appear on. Defaults to just the current page type.
     * @param string|null $subpagepattern which subpage this block should appear on. NULL = any (the default), otherwise only the specified subpage.
     */
    public function add_block($blockname, $region, $weight, $showinsubcontexts, $pagetypepattern = NULL, $subpagepattern = NULL) {
        global $DB;
        $this->check_known_block_type($blockname);
        $this->check_region_is_known($region);

        if (empty($pagetypepattern)) {
            $pagetypepattern = $this->page->pagetype;
        }

        $blockinstance = new stdClass;
        $blockinstance->blockname = $blockname;
        $blockinstance->contextid = $this->page->context->id;
        $blockinstance->showinsubcontexts = !empty($showinsubcontexts);
        $blockinstance->pagetypepattern = $pagetypepattern;
        $blockinstance->subpagepattern = $subpagepattern;
        $blockinstance->defaultregion = $region;
        $blockinstance->defaultweight = $weight;
        $blockinstance->configdata = '';
        $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

        // If the new instance was created, allow it to do additional setup
        if($block = block_instance($blockname, $blockinstance)) {
            $block->instance_create();
        }
    }

    /**
     * Convenience method, calls add_block repeatedly for all the blocks in $blocks.
     * @param array $blocks array with arrray keys the region names, and values an array of block names.
     * @param string $pagetypepattern optional. Passed to @see add_block()
     * @param string $subpagepattern optional. Passed to @see add_block()
     */
    public function add_blocks($blocks, $pagetypepattern = NULL, $subpagepattern = NULL) {
        $this->add_regions(array_keys($blocks));
        foreach ($blocks as $region => $regionblocks) {
            $weight = 0;
            foreach ($regionblocks as $blockname) {
                $this->add_block($blockname, $region, $weight, false, $pagetypepattern, $subpagepattern);
                $weight += 1;
            }
        }
    }

    /**
     * 
     * @param integer $instanceid
     * @return unknown_type
     */
    public function find_instance($instanceid) {
        foreach ($this->regions as $region => $notused) {
            $this->ensure_instances_exist($region);
            foreach($this->blockinstances[$region] as $instance) {
                if ($instance->instance->id == $instanceid) {
                    return $instance;
                }
            }
        }
        throw new block_not_on_page_exception($instanceid, $this->page);
    }

/// Inner workings =============================================================

    /**
     * Given a specific page type, return all the page type patterns that might
     * match it.
     * @param string $pagetype for example 'course-view-weeks' or 'mod-quiz-view'.
     * @return array an array of all the page type patterns that might match this page type.
     */
    protected function matching_page_type_patterns($pagetype) {
        $patterns = array($pagetype, '*');
        $bits = explode('-', $pagetype);
        if (count($bits) == 3 && $bits[0] == 'mod') {
            if ($bits[2] == 'view') {
                $patterns[] = 'mod-*-view';
            } else if ($bits[2] == 'index') {
                $patterns[] = 'mod-*-index';
            }
        }
        while (count($bits) > 0) {
            $patterns[] = implode('-', $bits) . '-*';
            array_pop($bits);
        }
        return $patterns;
    }

    protected function check_not_yet_loaded() {
        if (!is_null($this->birecordsbyregion)) {
            throw new coding_exception('block_manager has already loaded the blocks, to it is too late to change things that might affect which blocks are visible.');
        }
    }

    protected function check_is_loaded() {
        if (is_null($this->birecordsbyregion)) {
            throw new coding_exception('block_manager has not yet loaded the blocks, to it is too soon to request the information you asked for.');
        }
    }

    protected function check_known_block_type($blockname, $includeinvisible = false) {
        if (!$this->is_known_block_type($blockname, $includeinvisible)) {
            if ($this->is_known_block_type($blockname, true)) {
                throw new coding_exception('Unknown block type ' . $blockname);
            } else {
                throw new coding_exception('Block type ' . $blockname . ' has been disabled by the administrator.');
            }
        }
    }

    protected function check_region_is_known($region) {
        if (!$this->is_known_region($region)) {
            throw new coding_exception('Trying to reference an unknown block region ' . $region);
        }
    }

    /**
     * @return array an array where the array keys are the region names, and the array
     * values are empty arrays.
     */
    protected function prepare_per_region_arrays() {
        $result = array();
        foreach ($this->regions as $region => $notused) {
            $result[$region] = array();
        }
        return $result;
    }

    protected function create_block_instances($birecords) {
        $results = array();
        foreach ($birecords as $record) {
            $results[] = block_instance($record->blockname, $record, $this->page);
        }
        return $results;
    }

    protected function create_block_content($instances) {
        $results = array();
        foreach ($instances as $instance) {
            if ($instance->is_empty()) {
                continue;
            }

            $content = $instance->get_content();
            if (!empty($content)) {
                $results[] = $content;
            }
        }
        return $results;
    }

    protected function ensure_instances_exist($region) {
        $this->check_region_is_known($region);
        if (!array_key_exists($region, $this->blockinstances)) {
            $this->blockinstances[$region] =
                    $this->create_block_instances($this->birecordsbyregion[$region]);
        }
    }

    protected function ensure_content_created($region) {
        $this->ensure_instances_exist($region);
        if (!array_key_exists($region, $this->visibleblockcontent)) {
            $this->visibleblockcontent[$region] =
                    $this->create_block_content($this->blockinstances[$region]);
        }
    }

/// Deprecated stuff for backwards compatibility ===============================

    public function offsetSet($offset, $value) {
    }
    public function offsetExists($offset) {
        return $this->is_known_region($offset);
    }
    public function offsetUnset($offset) {
    }
    public function offsetGet($offset) {
        return $this->get_blocks_for_region($offset);
    }
}

/**
 * This class holds all the information required to view a block.
 */
abstract class block_content {
    /** Id used to uniquely identify this block in the HTML. */
    public $id = null;
    /** Class names to add to this block's container in the HTML. */
    public $classes = array();
    /** The content that appears in the title bar at the top of the block (HTML). */
    public $heading = null;
    /** Plain text name of this block instance, used in the skip links. */
    public $title = null;
    /**
     * A (possibly empty) array of editing controls. Array keys should be a
     * short string, e.g. 'edit', 'move' and the values are the HTML of the icons.
     */
    public $editingcontrols = array();
    /** The content that appears within the block, as HTML. */
    public $content = null;
    /** The content that appears at the end of the block. */
    public $footer = null;
    /**
     * Any small print that should appear under the block to explain to the
     * teacher about the block, for example 'This is a sticky block that was
     * added in the system context.'
     */
    public $annotation = null;
    /** The result of the preferred_width method, which the theme may choose to use, or ignore. */
    public $preferredwidth = null;
    public abstract function get_content();
}

/// Helper functions for working with block classes ============================

/**
 * Call a class method (one that does not requrie a block instance) on a block class.
 * @param string $blockname the name of the block.
 * @param string $method the method name.
 * @param array $param parameters to pass to the method.
 * @return mixed whatever the method returns.
 */
function block_method_result($blockname, $method, $param = NULL) {
    if(!block_load_class($blockname)) {
        return NULL;
    }
    return call_user_func(array('block_'.$blockname, $method), $param);
}

/**
 * Creates a new object of the specified block class.
 * @param string $blockname the name of the block.
 * @param $instance block_instances DB table row (optional).
 * @param moodle_page $page the page this block is appearing on.
 * @return block_base the requested block instance.
 */
function block_instance($blockname, $instance = NULL, $page = NULL) {
    if(!block_load_class($blockname)) {
        return false;
    }
    $classname = 'block_'.$blockname;
    $retval = new $classname;
    if($instance !== NULL) {
        if (is_null($page)) {
            global $PAGE;
            $page = $PAGE;
        }
        $retval->_load_instance($instance, $page);
    }
    return $retval;
}

/**
 * Load the block class for a particular type of block.
 * @param string $blockname the name of the block.
 * @return boolean success or failure.
 */
function block_load_class($blockname) {
    global $CFG;

    if(empty($blockname)) {
        return false;
    }

    $classname = 'block_'.$blockname;

    if(class_exists($classname)) {
        return true;
    }

    require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
    @include_once($CFG->dirroot.'/blocks/'.$blockname.'/block_'.$blockname.'.php'); // do not throw errors if block code not present

    return class_exists($classname);
}

/// Functions that have been deprecated by block_manager =======================

/**
 * @deprecated since Moodle 2.0 - use $page->blocks->get
 * This function returns an array with the IDs of any blocks that you can add to your page.
 * Parameters are passed by reference for speed; they are not modified at all.
 * @param $page the page object.
 * @param $blockmanager Not used.
 * @return array of block type ids.
 */
function blocks_get_missing(&$page, &$blockmanager) {
    return array_keys($page->blocks->get_addable_blocks());
}

/**
 * Actually delete from the database any blocks that are currently on this page,
 * but which should not be there according to blocks_name_allowed_in_format.
 * @param $page a page object.
 */
function blocks_remove_inappropriate($page) {
    // TODO
    return;
    $blockmanager = blocks_get_by_page($page);

    if(empty($blockmanager)) {
        return;
    }

    if(($pageformat = $page->pagetype) == NULL) {
        return;
    }

    foreach($blockmanager as $region) {
        foreach($region as $instance) {
            $block = blocks_get_record($instance->blockid);
            if(!blocks_name_allowed_in_format($block->name, $pageformat)) {
               blocks_delete_instance($instance);
            }
        }
    }
}

function blocks_name_allowed_in_format($name, $pageformat) {
    $accept = NULL;
    $maxdepth = -1;
    $formats = block_method_result($name, 'applicable_formats');
    if (!$formats) {
        $formats = array();
    }
    foreach ($formats as $format => $allowed) {
        $formatregex = '/^'.str_replace('*', '[^-]*', $format).'.*$/';
        $depth = substr_count($format, '-');
        if (preg_match($formatregex, $pageformat) && $depth > $maxdepth) {
            $maxdepth = $depth;
            $accept = $allowed;
        }
    }
    if ($accept === NULL) {
        $accept = !empty($formats['all']);
    }
    return $accept;
}

/**
 * Delete a block, and associated data.
 * @param object $instance a row from the block_instances table
 * @param $nolongerused legacy parameter. Not used, but kept for bacwards compatibility.
 * @param $skipblockstables for internal use only. Makes @see blocks_delete_all_for_context() more efficient.
 */
function blocks_delete_instance($instance, $nolongerused = false, $skipblockstables = false) {
    global $DB;

    if ($block = block_instance($instance->blockname, $instance)) {
        $block->instance_delete();
    }
    delete_context(CONTEXT_BLOCK, $instance->id);

    if (!$skipblockstables) {
        $DB->delete_records('block_positions', array('blockinstanceid' => $instance->id));
        $DB->delete_records('block_instances', array('id' => $instance->id));
    }
}

/**
 * @deprecated since 2.0
 * Delete all the blocks from a particular page.
 *
 * @param string $pagetype the page type.
 * @param integer $pageid the page id.
 * @return success of failure.
 */
function blocks_delete_all_on_page($pagetype, $pageid) {
    global $DB;

    debugging('Call to deprecated function blocks_repopulate_page. ' .
            'This function cannot work any more. Doing nothing. ' .
            'Please update your code to use another method.', DEBUG_DEVELOPER);
    return false;
}

/**
 * Delete all the blocks that belong to a particular context.
 * @param $contextid the context id.
 */
function blocks_delete_all_for_context($contextid) {
    global $DB;
    $instances = $DB->get_recordset('block_instances', array('contextid' => $contextid));
    foreach ($instances as $instance) {
        blocks_delete_instance($instance, true);
    }
    $instances->close();
    $DB->delete_records('block_instances', array('contextid' => $contextid));
    $DB->delete_records('block_positions', array('contextid' => $contextid));
}

// Accepts an array of block instances and checks to see if any of them have content to display
// (causing them to calculate their content in the process). Returns true or false. Parameter passed
// by reference for speed; the array is actually not modified.
function blocks_have_content(&$blockmanager, $region) {
    // TODO deprecate
    $content = $blockmanager->get_content_for_region($region);
    return !empty($content);
}

// This function prints one group of blocks in a page
function blocks_print_group($page, $blockmanager, $region) {
    global $COURSE, $CFG, $USER;

    $isediting = $page->user_is_editing();
    $groupblocks = $blockmanager->get_blocks_for_region($region);

    foreach($groupblocks as $instance) {
        if (($isediting && empty($instance->pinned))) {
            $options = 0;
            // The block can be moved up if it's NOT the first one in its position. If it is, we look at the OR clause:
            // the first block might still be able to move up if the page says so (i.e., it will change position)
// TODO            $options |= BLOCK_MOVE_UP    * ($instance->weight != 0          || ($page->blocks_move_position($instance, BLOCK_MOVE_UP)   != $instance->position));
            // Same thing for downward movement
// TODO            $options |= BLOCK_MOVE_DOWN  * ($instance->weight != $maxweight || ($page->blocks_move_position($instance, BLOCK_MOVE_DOWN) != $instance->position));
            // For left and right movements, it's up to the page to tell us whether they are allowed
// TODO            $options |= BLOCK_MOVE_RIGHT * ($page->blocks_move_position($instance, BLOCK_MOVE_RIGHT) != $instance->position);
// TODO            $options |= BLOCK_MOVE_LEFT  * ($page->blocks_move_position($instance, BLOCK_MOVE_LEFT ) != $instance->position);
            // Finally, the block can be configured if the block class either allows multiple instances, or if it specifically
            // allows instance configuration (multiple instances override that one). It doesn't have anything to do with what the
            // administrator has allowed for this block in the site admin options.
            $options |= BLOCK_CONFIGURE * ( $instance->instance_allow_multiple() || $instance->instance_allow_config() );
            $instance->_add_edit_controls($options);
        }

        if (false /* TODO */&& !$instance->visible && empty($COURSE->javascriptportal)) {
            if ($isediting) {
                $instance->_print_shadow();
            }
        } else {
            global $COURSE;
            if(!empty($COURSE->javascriptportal)) {
                 $COURSE->javascriptportal->currentblocksection = $region;
            }
            $instance->_print_block();
        }
        if (!empty($COURSE->javascriptportal)
                    && (empty($instance->pinned) || !$instance->pinned)) {
            $COURSE->javascriptportal->block_add('inst'.$instance->id, !$instance->visible);
        }
    } // End foreach

    if ($page->blocks->get_default_region() == $region &&
            $page->user_is_editing() && $page->user_can_edit_blocks()) {
        blocks_print_adminblock($page, $blockmanager);
    }
}

// This iterates over an array of blocks and calculates the preferred width
// Parameter passed by reference for speed; it's not modified.
function blocks_preferred_width($instances) {
    $width = 210;
}

/**
 * Get the block record for a particulr blockid.
 * @param $blockid block type id. If null, an array of all block types is returned.
 * @param $notusedanymore No longer used.
 * @return array|object row from block table, or all rows.
 */
function blocks_get_record($blockid = NULL, $notusedanymore = false) {
    global $PAGE;
    $blocks = $PAGE->blocks->get_installed_blocks();
    if ($blockid === NULL) {
        return $blocks;
    } else if (isset($blocks[$blockid])) {
        return $blocks[$blockid];
    } else {
        return false;
    }
}

function blocks_find_block($blockid, $blocksarray) {
    if (empty($blocksarray)) {
        return false;
    }
    foreach($blocksarray as $blockgroup) {
        if (empty($blockgroup)) {
            continue;
        }
        foreach($blockgroup as $instance) {
            if($instance->blockid == $blockid) {
                return $instance;
            }
        }
    }
    return false;
}

// Simple entry point for anyone that wants to use blocks
function blocks_setup(&$page, $pinned = BLOCKS_PINNED_FALSE) {
    $page->blocks->load_blocks();
    blocks_execute_url_action($page, $page->blocks);
    return $page->blocks;
}

function blocks_execute_action($page, &$blockmanager, $blockaction, $instanceorid, $pinned=false, $redirect=true) {
    global $CFG, $USER, $DB;

    if (!in_array($blockaction, array('config', 'add', 'delete'))) {
        throw new moodle_exception('Sorry, blocks editing is currently broken. Will be fixed. See MDL-19010.');
    }

    if (is_int($instanceorid)) {
        $blockid = $instanceorid;
    } else if (is_object($instanceorid)) {
        $instance = $instanceorid;
    }

    switch($blockaction) {
        case 'config':
            $block = blocks_get_record($instance->blockid);
            // Hacky hacky tricky stuff to get the original human readable block title,
            // even if the block has configured its title to be something else.
            // Create the object WITHOUT instance data.
            $blockobject = block_instance($block->name);
            if ($blockobject === false) {
                break;
            }

            // First of all check to see if the block wants to be edited
            if(!$blockobject->user_can_edit()) {
                break;
            }

            // Now get the title and AFTER that load up the instance
            $blocktitle = $blockobject->get_title();
            $blockobject->_load_instance($instance);

            // Define the data we're going to silently include in the instance config form here,
            // so we can strip them from the submitted data BEFORE serializing it.
            $hiddendata = array(
                'sesskey' => sesskey(),
                'instanceid' => $instance->id,
                'blockaction' => 'config'
            );

            // To this data, add anything the page itself needs to display
            $hiddendata = $page->url->params($hiddendata);

            if ($data = data_submitted()) {
                $remove = array_keys($hiddendata);
                foreach($remove as $item) {
                    unset($data->$item);
                }
                if(!$blockobject->instance_config_save($data, $pinned)) {
                    print_error('cannotsaveblock');
                }
                // And nothing more, continue with displaying the page
            }
            else {
                // We need to show the config screen, so we highjack the display logic and then die
                $strheading = get_string('blockconfiga', 'moodle', $blocktitle);
                $page->print_header(get_string('pageheaderconfigablock', 'moodle'), array($strheading => ''));

                echo '<div class="block-config" id="'.$block->name.'">';   /// Make CSS easier

                print_heading($strheading);
                echo '<form method="post" name="block-config" action="'. $page->url->out(false) .'">';
                echo '<p>';
                foreach($hiddendata as $name => $val) {
                    echo '<input type="hidden" name="'. $name .'" value="'. $val .'" />';
                }
                echo '</p>';
                $blockobject->instance_config_print();
                echo '</form>';

                echo '</div>';
                $PAGE->set_pagetype('blocks-' . $block->name);
                print_footer();
                die(); // Do not go on with the other page-related stuff
            }
        break;
        case 'toggle':
            if(empty($instance))  {
                print_error('invalidblockinstance', '', '', $blockaction);
            }
            $instance->visible = ($instance->visible) ? 0 : 1;
            if (!empty($pinned)) {
                $DB->update_record('block_pinned_old', $instance);
            } else {
                $DB->update_record('block_instance_old', $instance);
            }
        break;
        case 'delete':
            if(empty($instance))  {
                print_error('invalidblockinstance', '', '', $blockaction);
            }
            blocks_delete_instance($instance->instance, $pinned);
        break;
        case 'moveup':
            if(empty($instance))  {
                print_error('invalidblockinstance', '', '', $blockaction);
            }

            if($instance->weight == 0) {
                // The block is the first one, so a move "up" probably means it changes position
                // Where is the instance going to be moved?
                $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_UP);
                $newweight = (empty($blockmanager[$newpos]) ? 0 : max(array_keys($blockmanager[$newpos])) + 1);

                blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
            }
            else {
                // The block is just moving upwards in the same position.
                // This configuration will make sure that even if somehow the weights
                // become not continuous, block move operations will eventually bring
                // the situation back to normal without printing any warnings.
                if(!empty($blockmanager[$instance->position][$instance->weight - 1])) {
                    $other = $blockmanager[$instance->position][$instance->weight - 1];
                }
                if(!empty($other)) {
                    ++$other->weight;
                    if (!empty($pinned)) {
                        $DB->update_record('block_pinned_old', $other);
                    } else {
                        $DB->update_record('block_instance_old', $other);
                    }
                }
                --$instance->weight;
                if (!empty($pinned)) {
                    $DB->update_record('block_pinned_old', $instance);
                } else {
                    $DB->update_record('block_instance_old', $instance);
                }
            }
        break;
        case 'movedown':
            if(empty($instance))  {
                print_error('invalidblockinstance', '', '', $blockaction);
            }

            if($instance->weight == max(array_keys($blockmanager[$instance->position]))) {
                // The block is the last one, so a move "down" probably means it changes position
                // Where is the instance going to be moved?
                $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_DOWN);
                $newweight = (empty($blockmanager[$newpos]) ? 0 : max(array_keys($blockmanager[$newpos])) + 1);

                blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
            }
            else {
                // The block is just moving downwards in the same position.
                // This configuration will make sure that even if somehow the weights
                // become not continuous, block move operations will eventually bring
                // the situation back to normal without printing any warnings.
                if(!empty($blockmanager[$instance->position][$instance->weight + 1])) {
                    $other = $blockmanager[$instance->position][$instance->weight + 1];
                }
                if(!empty($other)) {
                    --$other->weight;
                    if (!empty($pinned)) {
                        $DB->update_record('block_pinned_old', $other);
                    } else {
                        $DB->update_record('block_instance_old', $other);
                    }
                }
                ++$instance->weight;
                if (!empty($pinned)) {
                    $DB->update_record('block_pinned_old', $instance);
                } else {
                    $DB->update_record('block_instance_old', $instance);
                }
            }
        break;
        case 'moveleft':
            if(empty($instance))  {
                print_error('invalidblockinstance', '', '', $blockaction);
            }

            // Where is the instance going to be moved?
            $newpos = $page->blocks_move_position($instance, BLOCK_MOVE_LEFT);
            $newweight = (empty($blockmanager[$newpos]) ? 0 : max(array_keys($blockmanager[$newpos])) + 1);

            blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
        break;
        case 'moveright':
            if(empty($instance))  {
                print_error('invalidblockinstance', '', '', $blockaction);
            }

            // Where is the instance going to be moved?
            $newpos    = $page->blocks_move_position($instance, BLOCK_MOVE_RIGHT);
            $newweight = (empty($blockmanager[$newpos]) ? 0 : max(array_keys($blockmanager[$newpos])) + 1);

            blocks_execute_repositioning($instance, $newpos, $newweight, $pinned);
        break;
        case 'add':
            // Add a new instance of this block, if allowed
            $block = blocks_get_record($blockid);

            if (empty($block) || !$block->visible) {
                // Only allow adding if the block exists and is enabled
                break;
            }

            if (!$block->multiple && blocks_find_block($blockid, $blockmanager) !== false) {
                // If no multiples are allowed and we already have one, return now
                break;
            }

            if (!block_method_result($block->name, 'user_can_addto', $page)) {
                // If the block doesn't want to be added...
                break;
            }

            $region = $page->blocks->get_default_region();
            $weight = $DB->get_field_sql("SELECT MAX(defaultweight) FROM {block_instances} 
                    WHERE contextid = ? AND defaultregion = ?", array($page->context->id, $region));
            $pagetypepattern = $page->pagetype;
            if (strpos($pagetypepattern, 'course-view') === 0) {
                $pagetypepattern = 'course-view-*';
            }
            $page->blocks->add_block($block->name, $region, $weight, false, $pagetypepattern);
        break;
    }

    if ($redirect) {
        // In order to prevent accidental duplicate actions, redirect to a page with a clean url
        redirect($page->url->out());
    }
}

// You can use this to get the blocks to respond to URL actions without much hassle
function blocks_execute_url_action(&$PAGE, &$blockmanager,$pinned=false) {
    $blockaction = optional_param('blockaction', '', PARAM_ALPHA);

    if (empty($blockaction) || !$PAGE->user_allowed_editing() || !confirm_sesskey()) {
        return;
    }

    $instanceid  = optional_param('instanceid', 0, PARAM_INT);
    $blockid     = optional_param('blockid',    0, PARAM_INT);

    if (!empty($blockid)) {
        blocks_execute_action($PAGE, $blockmanager, strtolower($blockaction), $blockid, $pinned);
    } else if (!empty($instanceid)) {
        $instance = $blockmanager->find_instance($instanceid);
        blocks_execute_action($PAGE, $blockmanager, strtolower($blockaction), $instance, $pinned);
    }
}

// This shouldn't be used externally at all, it's here for use by blocks_execute_action()
// in order to reduce code repetition.
function blocks_execute_repositioning(&$instance, $newpos, $newweight, $pinned=false) {
    global $DB;

    throw new moodle_exception('Sorry, blocks editing is currently broken. Will be fixed. See MDL-19010.');

    // If it's staying where it is, don't do anything, unless overridden
    if ($newpos == $instance->position) {
        return;
    }

    // Close the weight gap we 'll leave behind
    if (!empty($pinned)) {
        $sql = "UPDATE {block_instance_old}
                   SET weight = weight - 1
                 WHERE pagetype = ? AND position = ? AND weight > ?";
        $params = array($instance->pagetype, $instance->position, $instance->weight);

    } else {
        $sql = "UPDATE {block_instance_old}
                   SET weight = weight - 1
                 WHERE pagetype = ? AND pageid = ?
                       AND position = ? AND weight > ?";
        $params = array($instance->pagetype, $instance->pageid, $instance->position, $instance->weight);
    }
    $DB->execute($sql, $params);

    $instance->position = $newpos;
    $instance->weight   = $newweight;

    if (!empty($pinned)) {
        $DB->update_record('block_pinned_old', $instance);
    } else {
        $DB->update_record('block_instance_old', $instance);
    }
}


/**
 * Moves a block to the new position (column) and weight (sort order).
 * @param $instance - The block instance to be moved.
 * @param $destpos - BLOCK_POS_LEFT or BLOCK_POS_RIGHT. The destination column.
 * @param $destweight - The destination sort order. If NULL, we add to the end
 *                      of the destination column.
 * @param $pinned - Are we moving pinned blocks? We can only move pinned blocks
 *                  to a new position withing the pinned list. Likewise, we
 *                  can only moved non-pinned blocks to a new position within
 *                  the non-pinned list.
 * @return boolean (success or failure).
 */
function blocks_move_block($page, &$instance, $destpos, $destweight=NULL, $pinned=false) {
    global $CFG, $DB;

    throw new moodle_exception('Sorry, blocks editing is currently broken. Will be fixed. See MDL-19010.');

    if ($pinned) {
        $blocklist = blocks_get_pinned($page);
    } else {
        $blocklist = blocks_get_by_page($page);
    }

    if ($blocklist[$instance->position][$instance->weight]->id != $instance->id) {
        // The source block instance is not where we think it is.
        return false;
    }

    // First we close the gap that will be left behind when we take out the
    // block from it's current column.
    if ($pinned) {
        $closegapsql = "UPDATE {block_instance_old}
                           SET weight = weight - 1
                         WHERE weight > ? AND position = ? AND pagetype = ?";
        $params = array($instance->weight, $instance->position, $instance->pagetype);
    } else {
        $closegapsql = "UPDATE {block_instance_old}
                           SET weight = weight - 1
                         WHERE weight > ? AND position = ?
                               AND pagetype = ? AND pageid = ?";
        $params = array($instance->weight, $instance->position, $instance->pagetype, $instance->pageid);
    }
    if (!$DB->execute($closegapsql, $params)) {
        return false;
    }

    // Now let's make space for the block being moved.
    if ($pinned) {
        $opengapsql = "UPDATE {block_instance_old}
                           SET weight = weight + 1
                         WHERE weight >= ? AND position = ? AND pagetype = ?";
        $params = array($destweight, $destpos, $instance->pagetype);
    } else {
        $opengapsql = "UPDATE {block_instance_old}
                          SET weight = weight + 1
                        WHERE weight >= ? AND position = ?
                              AND pagetype = ? AND pageid = ?";
        $params = array($destweight, $destpos, $instance->pagetype, $instance->pageid);
    }
    if (!$DB->execute($opengapsql, $params)) {
        return false;
    }

    // Move the block.
    $instance->position = $destpos;
    $instance->weight   = $destweight;

    if ($pinned) {
        $table = 'block_pinned_old';
    } else {
        $table = 'block_instance_old';
    }
    return $DB->update_record($table, $instance);
}


/**
 * Returns an array consisting of 2 arrays:
 * 1) Array of pinned blocks for position BLOCK_POS_LEFT
 * 2) Array of pinned blocks for position BLOCK_POS_RIGHT
 */
function blocks_get_pinned($page) {
    global $DB;

    $visible = true;
    $select = "pagetype = ?";
    $params = array($page->pagetype);

     if ($visible) {
        $select .= " AND visible = 1";
     }

    $blocks = $DB->get_records_select('block_pinned_old', $select, $params, 'position, weight');

    $regions = $page->blocks->get_regions();
    $arr = array();

    foreach($regions as $key => $region) {
        $arr[$region] = array();
    }

    if(empty($blocks)) {
        return $arr;
    }

    foreach($blocks as $block) {
        $block->pinned = true; // so we know we can't move it.
        // make up an instanceid if we can..
        $block->pageid = $page->get_id();
        $arr[$block->position][$block->weight] = $block;
    }

    return $arr;
}


/**
 * Similar to blocks_get_by_page(), except that, the array returned includes
 * pinned blocks as well. Pinned blocks are always appended before normal
 * block instances.
 */
function blocks_get_by_page_pinned($page) {
    $pinned = blocks_get_pinned($page);
    $user = blocks_get_by_page($page);

    $weights = array();

    foreach ($pinned as $pos => $arr) {
        $weights[$pos] = count($arr);
    }

    foreach ($user as $pos => $blocks) {
        if (!array_key_exists($pos,$pinned)) {
             $pinned[$pos] = array();
        }
        if (!array_key_exists($pos,$weights)) {
            $weights[$pos] = 0;
        }
        foreach ($blocks as $block) {
            $pinned[$pos][$weights[$pos]] = $block;
            $weights[$pos]++;
        }
    }
    return $pinned;
}


/**
 * Returns an array of blocks for the page. Pinned blocks are excluded.
 */
function blocks_get_by_page($page) {
    global $DB;

    // TODO check the backwards compatibility hack.
    return $page->blocks;
}


//This function prints the block to admin blocks as necessary
function blocks_print_adminblock($page, $blockmanager) {
    global $USER;

    $missingblocks = array_keys($page->blocks->get_addable_blocks());

    if (!empty($missingblocks)) {
        $strblocks = '<div class="title"><h2>';
        $strblocks .= get_string('blocks');
        $strblocks .= '</h2></div>';

        $stradd    = get_string('add');
        foreach ($missingblocks as $blockid) {
            $block = blocks_get_record($blockid);
            $blockobject = block_instance($block->name);
            if ($blockobject !== false && $blockobject->user_can_addto($page)) {
                $menu[$block->id] = $blockobject->get_title();
            }
        }
        asort($menu, SORT_LOCALE_STRING);

        $target = $page->url->out(false, array('sesskey' => sesskey(), 'blockaction' => 'add'));
        $content = popup_form($target.'&amp;blockid=', $menu, 'add_block', '', $stradd .'...', '', '', true);
        print_side_block($strblocks, $content, NULL, NULL, NULL, array('class' => 'block_adminblock'));
    }
}

/**
 * Parse a list of default blocks. See config-dist for a description of the format.
 * @param string $blocksstr
 * @return array
 */
function blocks_parse_default_blocks_list($blocksstr) {
    $blocks = array();
    $bits = explode(':', $blocksstr);
    if (!empty($bits)) {
        $blocks[BLOCK_POS_LEFT] = explode(',', array_shift($bits));
    }
    if (!empty($bits)) {
        $blocks[BLOCK_POS_RIGHT] = explode(',', array_shift($bits));
    }
    return $blocks;
}

/**
 * @return array the blocks that should be added to the site course by default.
 */
function blocks_get_default_site_course_blocks() {
    global $CFG;

    if (!empty($CFG->defaultblocks_site)) {
        return blocks_parse_default_blocks_list($CFG->defaultblocks_site);
    } else {
        return array(
            BLOCK_POS_LEFT => array('site_main_menu', 'admin_tree'),
            BLOCK_POS_RIGHT => array('course_summary', 'calendar_month')
        );
    }
}

/**
 * Add the default blocks to a course.
 * @param object $course a course object.
 */
function blocks_add_default_course_blocks($course) {
    global $CFG;

    if (!empty($CFG->defaultblocks_override)) {
        $blocknames = blocks_parse_default_blocks_list($CFG->defaultblocks_override);

    } else if ($course->id == SITEID) {
        $blocknames = blocks_get_default_site_course_blocks();

    } else {
        $defaultblocks = 'defaultblocks_' . $course->format;
        if (!empty($CFG->$defaultblocks)) {
            $blocknames = blocks_parse_default_blocks_list($CFG->$defaultblocks);

        } else {
            $formatconfig = $CFG->dirroot.'/course/format/'.$course->format.'/config.php';
            if (is_readable($formatconfig)) {
                require($formatconfig);
            }
            if (!empty($format['defaultblocks'])) {
                $blocknames = blocks_parse_default_blocks_list($format['defaultblocks']);

            } else if (!empty($CFG->defaultblocks)){
                $blocknames = blocks_parse_default_blocks_list($CFG->defaultblocks);

            } else {
                $blocknames = array(
                    BLOCK_POS_LEFT => array('participants', 'activity_modules', 'search_forums', 'admin', 'course_list'),
                    BLOCK_POS_RIGHT => array('news_items', 'calendar_upcoming', 'recent_activity')
                );
            }
        }
    }

    if ($course->id == SITEID) {
        $pagetypepattern = 'site-index';
    } else {
        $pagetypepattern = 'course-view-*';
    }

    $page = new moodle_page();
    $page->set_course($course);
    $page->blocks->add_blocks($blocknames, $pagetypepattern);
}

/**
 * Add the default system-context blocks. E.g. the admin tree.
 */
function blocks_add_default_system_blocks() {
    $page = new moodle_page();
    $page->set_context(get_context_instance(CONTEXT_SYSTEM));
    $page->blocks->add_blocks(array(BLOCK_POS_LEFT => array('admin_tree', 'admin_bookmarks')), 'admin-*');
}

/**
 * @deprecated since 2.0
 * Dispite what this function is called, it seems to be mostly used to populate
 * the default blocks when a new course (or whatever) is created.
 * @param object $page the page to add default blocks to.
 * @return boolean success or failure.
 */
function blocks_repopulate_page($page) {
    global $CFG;

    debugging('Call to deprecated function blocks_repopulate_page. ' .
            'Use a more specific method like blocks_add_default_course_blocks, ' .
            'or just call $PAGE->blocks->add_blocks()', DEBUG_DEVELOPER);

    /// If the site override has been defined, it is the only valid one.
    if (!empty($CFG->defaultblocks_override)) {
        $blocknames = $CFG->defaultblocks_override;
    } else {
        $blocknames = $page->blocks_get_default();
    }

    $blocks = blocks_parse_default_blocks_list($blocknames);
    $page->blocks->add_blocks($blocks);

    return true;
}

?>
