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
 * Block Class and Functions
 *
 * This file defines the {@link block_manager} class,
 *
 * @package    core
 * @subpackage block
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**#@+
 * @deprecated since Moodle 2.0. No longer used.
 */
define('BLOCK_MOVE_LEFT',   0x01);
define('BLOCK_MOVE_RIGHT',  0x02);
define('BLOCK_MOVE_UP',     0x04);
define('BLOCK_MOVE_DOWN',   0x08);
define('BLOCK_CONFIGURE',   0x10);
/**#@-*/

/**#@+
 * Default names for the block regions in the standard theme.
 */
define('BLOCK_POS_LEFT',  'side-pre');
define('BLOCK_POS_RIGHT', 'side-post');
/**#@-*/

/**#@+
 * @deprecated since Moodle 2.0. No longer used.
 */
define('BLOCKS_PINNED_TRUE',0);
define('BLOCKS_PINNED_FALSE',1);
define('BLOCKS_PINNED_BOTH',2);
/**#@-*/

/**
 * Exception thrown when someone tried to do something with a block that does
 * not exist on a page.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class block_not_on_page_exception extends moodle_exception {
    /**
     * Constructor
     * @param int $instanceid the block instance id of the block that was looked for.
     * @param object $page the current page.
     */
    public function __construct($instanceid, $page) {
        $a = new stdClass;
        $a->instanceid = $instanceid;
        $a->url = $page->url->out();
        parent::__construct('blockdoesnotexistonpage', '', $page->url->out(), $a);
    }
}

/**
 * This class keeps track of the block that should appear on a moodle_page.
 *
 * The page to work with as passed to the constructor.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class block_manager {
    /**
     * The UI normally only shows block weights between -MAX_WEIGHT and MAX_WEIGHT,
     * although other weights are valid.
     */
    const MAX_WEIGHT = 10;

/// Field declarations =========================================================

    /**
     * the moodle_page we are managing blocks for.
     * @var moodle_page
     */
    protected $page;

    /** @var array region name => 1.*/
    protected $regions = array();

    /** @var string the region where new blocks are added.*/
    protected $defaultregion = null;

    /** @var array will be $DB->get_records('blocks') */
    protected $allblocks = null;

    /**
     * @var array blocks that this user can add to this page. Will be a subset
     * of $allblocks, but with array keys block->name. Access this via the
     * {@link get_addable_blocks()} method to ensure it is lazy-loaded.
     */
    protected $addableblocks = null;

    /**
     * Will be an array region-name => array(db rows loaded in load_blocks);
     * @var array
     */
    protected $birecordsbyregion = null;

    /**
     * array region-name => array(block objects); populated as necessary by
     * the ensure_instances_exist method.
     * @var array
     */
    protected $blockinstances = array();

    /**
     * array region-name => array(block_contents objects) what actually needs to
     * be displayed in each region.
     * @var array
     */
    protected $visibleblockcontent = array();

    /**
     * array region-name => array(block_contents objects) extra block-like things
     * to be displayed in each region, before the real blocks.
     * @var array
     */
    protected $extracontent = array();

    /**
     * Used by the block move id, to track whether a block is currently being moved.
     *
     * When you click on the move icon of a block, first the page needs to reload with
     * extra UI for choosing a new position for a particular block. In that situation
     * this field holds the id of the block being moved.
     *
     * @var integer|null
     */
    protected $movingblock = null;

    /**
     * Show only fake blocks
     */
    protected $fakeblocksonly = false;

/// Constructor ================================================================

    /**
     * Constructor.
     * @param object $page the moodle_page object object we are managing the blocks for,
     * or a reasonable faxilimily. (See the comment at the top of this class
     * and {@link http://en.wikipedia.org/wiki/Duck_typing})
     */
    public function __construct($page) {
        $this->page = $page;
    }

/// Getter methods =============================================================

    /**
     * Get an array of all region names on this page where a block may appear
     *
     * @return array the internal names of the regions on this page where block may appear.
     */
    public function get_regions() {
        if (is_null($this->defaultregion)) {
            $this->page->initialise_theme_and_output();
        }
        return array_keys($this->regions);
    }

    /**
     * Get the region name of the region blocks are added to by default
     *
     * @return string the internal names of the region where new blocks are added
     * by default, and where any blocks from an unrecognised region are shown.
     * (Imagine that blocks were added with one theme selected, then you switched
     * to a theme with different block positions.)
     */
    public function get_default_region() {
        $this->page->initialise_theme_and_output();
        return $this->defaultregion;
    }

    /**
     * The list of block types that may be added to this page.
     *
     * @return array block name => record from block table.
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
                    (block_method_result($block->name, 'instance_allow_multiple') || !$this->is_block_present($block->name)) &&
                    blocks_name_allowed_in_format($block->name, $pageformat) &&
                    block_method_result($block->name, 'user_can_addto', $this->page)) {
                $this->addableblocks[$block->name] = $block;
            }
        }

        return $this->addableblocks;
    }

    /**
     * Given a block name, find out of any of them are currently present in the page

     * @param string $blockname - the basic name of a block (eg "navigation")
     * @return boolean - is there one of these blocks in the current page?
     */
    public function is_block_present($blockname) {
        if (empty($this->blockinstances)) {
            return false;
        }

        foreach ($this->blockinstances as $region) {
            foreach ($region as $instance) {
                if (empty($instance->instance->blockname)) {
                    continue;
                }
                if ($instance->instance->blockname == $blockname) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Find out if a block type is known by the system
     *
     * @param string $blockname the name of the type of block.
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
     * Find out if a region exists on a page
     *
     * @param string $region a region name
     * @return boolean true if this region exists on this page.
     */
    public function is_known_region($region) {
        return array_key_exists($region, $this->regions);
    }

    /**
     * Get an array of all blocks within a given region
     *
     * @param string $region a block region that exists on this page.
     * @return array of block instances.
     */
    public function get_blocks_for_region($region) {
        $this->check_is_loaded();
        $this->ensure_instances_exist($region);
        return $this->blockinstances[$region];
    }

    /**
     * Returns an array of block content objects that exist in a region
     *
     * @param string $region a block region that exists on this page.
     * @return array of block block_contents objects for all the blocks in a region.
     */
    public function get_content_for_region($region, $output) {
        $this->check_is_loaded();
        $this->ensure_content_created($region, $output);
        return $this->visibleblockcontent[$region];
    }

    /**
     * Helper method used by get_content_for_region.
     * @param string $region region name
     * @param float $weight weight. May be fractional, since you may want to move a block
     * between ones with weight 2 and 3, say ($weight would be 2.5).
     * @return string URL for moving block $this->movingblock to this position.
     */
    protected function get_move_target_url($region, $weight) {
        return new moodle_url($this->page->url, array('bui_moveid' => $this->movingblock,
                'bui_newregion' => $region, 'bui_newweight' => $weight, 'sesskey' => sesskey()));
    }

    /**
     * Determine whether a region contains anything. (Either any real blocks, or
     * the add new block UI.)
     *
     * (You may wonder why the $output parameter is required. Unfortunately,
     * because of the way that blocks work, the only reliable way to find out
     * if a block will be visible is to get the content for output, and to
     * get the content, you need a renderer. Fortunately, this is not a
     * performance problem, because we cache the output that is generated, and
     * in almost every case where we call region_has_content, we are about to
     * output the blocks anyway, so we are not doing wasted effort.)
     *
     * @param string $region a block region that exists on this page.
     * @param object $output a core_renderer. normally the global $OUTPUT.
     * @return boolean Whether there is anything in this region.
     */
    public function region_has_content($region, $output) {

        if (!$this->is_known_region($region)) {
            return false;
        }
        $this->check_is_loaded();
        $this->ensure_content_created($region, $output);
        if ($this->page->user_is_editing() && $this->page->user_can_edit_blocks()) {
            // If editing is on, we need all the block regions visible, for the
            // move blocks UI.
            return true;
        }
        return !empty($this->visibleblockcontent[$region]) || !empty($this->extracontent[$region]);
    }

    /**
     * Get an array of all of the installed blocks.
     *
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
     * Add a region to a page
     *
     * @param string $region add a named region where blocks may appear on the
     * current page. This is an internal name, like 'side-pre', not a string to
     * display in the UI.
     */
    public function add_region($region) {
        $this->check_not_yet_loaded();
        $this->regions[$region] = 1;
    }

    /**
     * Add an array of regions
     * @see add_region()
     *
     * @param array $regions this utility method calls add_region for each array element.
     */
    public function add_regions($regions) {
        foreach ($regions as $region) {
            $this->add_region($region);
        }
    }

    /**
     * Set the default region for new blocks on the page
     *
     * @param string $defaultregion the internal names of the region where new
     * blocks should be added by default, and where any blocks from an
     * unrecognised region are shown.
     */
    public function set_default_region($defaultregion) {
        $this->check_not_yet_loaded();
        if ($defaultregion) {
            $this->check_region_is_known($defaultregion);
        }
        $this->defaultregion = $defaultregion;
    }

    /**
     * Add something that looks like a block, but which isn't an actual block_instance,
     * to this page.
     *
     * @param block_contents $bc the content of the block-like thing.
     * @param string $region a block region that exists on this page.
     */
    public function add_fake_block($bc, $region) {
        $this->page->initialise_theme_and_output();
        if (!$this->is_known_region($region)) {
            $region = $this->get_default_region();
        }
        if (array_key_exists($region, $this->visibleblockcontent)) {
            throw new coding_exception('block_manager has already prepared the blocks in region ' .
                    $region . 'for output. It is too late to add a fake block.');
        }
        $this->extracontent[$region][] = $bc;
    }

    /**
     * When the block_manager class was created, the {@link add_fake_block()}
     * was called add_pretend_block, which is inconsisted with
     * {@link show_only_fake_blocks()}. To fix this inconsistency, this method
     * was renamed to add_fake_block. Please update your code.
     * @param block_contents $bc the content of the block-like thing.
     * @param string $region a block region that exists on this page.
     */
    public function add_pretend_block($bc, $region) {
        debugging(DEBUG_DEVELOPER, 'add_pretend_block has been renamed to add_fake_block. Please rename the method call in your code.');
        $this->add_fake_block($bc, $region);
    }

    /**
     * Checks to see whether all of the blocks within the given region are docked
     *
     * @see region_uses_dock
     * @param string $region
     * @return bool True if all of the blocks within that region are docked
     */
    public function region_completely_docked($region, $output) {
        if (!$this->page->theme->enable_dock) {
            return false;
        }
        $this->check_is_loaded();
        $this->ensure_content_created($region, $output);
        foreach($this->visibleblockcontent[$region] as $instance) {
            if (!empty($instance->content) && !get_user_preferences('docked_block_instance_'.$instance->blockinstanceid, 0)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks to see whether any of the blocks within the given regions are docked
     *
     * @see region_completely_docked
     * @param array|string $regions array of regions (or single region)
     * @return bool True if any of the blocks within that region are docked
     */
    public function region_uses_dock($regions, $output) {
        if (!$this->page->theme->enable_dock) {
            return false;
        }
        $this->check_is_loaded();
        foreach((array)$regions as $region) {
            $this->ensure_content_created($region, $output);
            foreach($this->visibleblockcontent[$region] as $instance) {
                if(!empty($instance->content) && get_user_preferences('docked_block_instance_'.$instance->blockinstanceid, 0)) {
                    return true;
                }
            }
        }
        return false;
    }

/// Actions ====================================================================

    /**
     * This method actually loads the blocks for our page from the database.
     *
     * @param boolean|null $includeinvisible
     *      null (default) - load hidden blocks if $this->page->user_is_editing();
     *      true - load hidden blocks.
     *      false - don't load hidden blocks.
     */
    public function load_blocks($includeinvisible = null) {
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

        // Ensure we have been initialised.
        if (is_null($this->defaultregion)) {
            $this->page->initialise_theme_and_output();
            // If there are still no block regions, then there are no blocks on this page.
            if (empty($this->regions)) {
                $this->birecordsbyregion = array();
                return;
            }
        }

        // Check if we need to load normal blocks
        if ($this->fakeblocksonly) {
            $this->birecordsbyregion = $this->prepare_per_region_arrays();
            return;
        }

        if (is_null($includeinvisible)) {
            $includeinvisible = $this->page->user_is_editing();
        }
        if ($includeinvisible) {
            $visiblecheck = '';
        } else {
            $visiblecheck = 'AND (bp.visible = 1 OR bp.visible IS NULL)';
        }

        $context = $this->page->context;
        $contexttest = 'bi.parentcontextid = :contextid2';
        $parentcontextparams = array();
        $parentcontextids = get_parent_contexts($context);
        if ($parentcontextids) {
            list($parentcontexttest, $parentcontextparams) =
                    $DB->get_in_or_equal($parentcontextids, SQL_PARAMS_NAMED, 'parentcontext');
            $contexttest = "($contexttest OR (bi.showinsubcontexts = 1 AND bi.parentcontextid $parentcontexttest))";
        }

        $pagetypepatterns = matching_page_type_patterns($this->page->pagetype);
        list($pagetypepatterntest, $pagetypepatternparams) =
                $DB->get_in_or_equal($pagetypepatterns, SQL_PARAMS_NAMED, 'pagetypepatterntest');

        list($ccselect, $ccjoin) = context_instance_preload_sql('bi.id', CONTEXT_BLOCK, 'ctx');

        $params = array(
            'subpage1' => $this->page->subpage,
            'subpage2' => $this->page->subpage,
            'contextid1' => $context->id,
            'contextid2' => $context->id,
            'pagetype' => $this->page->pagetype,
        );
        $sql = "SELECT
                    bi.id,
                    bp.id AS blockpositionid,
                    bi.blockname,
                    bi.parentcontextid,
                    bi.showinsubcontexts,
                    bi.pagetypepattern,
                    bi.subpagepattern,
                    bi.defaultregion,
                    bi.defaultweight,
                    COALESCE(bp.visible, 1) AS visible,
                    COALESCE(bp.region, bi.defaultregion) AS region,
                    COALESCE(bp.weight, bi.defaultweight) AS weight,
                    bi.configdata
                    $ccselect

                FROM {block_instances} bi
                JOIN {block} b ON bi.blockname = b.name
                LEFT JOIN {block_positions} bp ON bp.blockinstanceid = bi.id
                                                  AND bp.contextid = :contextid1
                                                  AND bp.pagetype = :pagetype
                                                  AND bp.subpage = :subpage1
                $ccjoin

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
            context_instance_preload($bi);
            if ($this->is_known_region($bi->region)) {
                $this->birecordsbyregion[$bi->region][] = $bi;
            } else {
                $unknown[] = $bi;
            }
        }

        // Pages don't necessarily have a defaultregion. The  one time this can
        // happen is when there are no theme block regions, but the script itself
        // has a block region in the main content area.
        if (!empty($this->defaultregion)) {
            $this->birecordsbyregion[$this->defaultregion] =
                    array_merge($this->birecordsbyregion[$this->defaultregion], $unknown);
        }
    }

    /**
     * Add a block to the current page, or related pages. The block is added to
     * context $this->page->contextid. If $pagetypepattern $subpagepattern
     *
     * @param string $blockname The type of block to add.
     * @param string $region the block region on this page to add the block to.
     * @param integer $weight determines the order where this block appears in the region.
     * @param boolean $showinsubcontexts whether this block appears in subcontexts, or just the current context.
     * @param string|null $pagetypepattern which page types this block should appear on. Defaults to just the current page type.
     * @param string|null $subpagepattern which subpage this block should appear on. NULL = any (the default), otherwise only the specified subpage.
     */
    public function add_block($blockname, $region, $weight, $showinsubcontexts, $pagetypepattern = NULL, $subpagepattern = NULL) {
        global $DB;
        // Allow invisible blocks because this is used when adding default page blocks, which
        // might include invisible ones if the user makes some default blocks invisible
        $this->check_known_block_type($blockname, true);
        $this->check_region_is_known($region);

        if (empty($pagetypepattern)) {
            $pagetypepattern = $this->page->pagetype;
        }

        $blockinstance = new stdClass;
        $blockinstance->blockname = $blockname;
        $blockinstance->parentcontextid = $this->page->context->id;
        $blockinstance->showinsubcontexts = !empty($showinsubcontexts);
        $blockinstance->pagetypepattern = $pagetypepattern;
        $blockinstance->subpagepattern = $subpagepattern;
        $blockinstance->defaultregion = $region;
        $blockinstance->defaultweight = $weight;
        $blockinstance->configdata = '';
        $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

        // Ensure the block context is created.
        get_context_instance(CONTEXT_BLOCK, $blockinstance->id);

        // If the new instance was created, allow it to do additional setup
        if ($block = block_instance($blockname, $blockinstance)) {
            $block->instance_create();
        }
    }

    public function add_block_at_end_of_default_region($blockname) {
        $defaulregion = $this->get_default_region();

        $lastcurrentblock = end($this->birecordsbyregion[$defaulregion]);
        if ($lastcurrentblock) {
            $weight = $lastcurrentblock->weight + 1;
        } else {
            $weight = 0;
        }

        if ($this->page->subpage) {
            $subpage = $this->page->subpage;
        } else {
            $subpage = null;
        }

        // Special case. Course view page type include the course format, but we
        // want to add the block non-format-specifically.
        $pagetypepattern = $this->page->pagetype;
        if (strpos($pagetypepattern, 'course-view') === 0) {
            $pagetypepattern = 'course-view-*';
        }

        $this->add_block($blockname, $defaulregion, $weight, false, $pagetypepattern, $subpage);
    }

    /**
     * Convenience method, calls add_block repeatedly for all the blocks in $blocks.
     *
     * @param array $blocks array with array keys the region names, and values an array of block names.
     * @param string $pagetypepattern optional. Passed to @see add_block()
     * @param string $subpagepattern optional. Passed to @see add_block()
     */
    public function add_blocks($blocks, $pagetypepattern = NULL, $subpagepattern = NULL, $showinsubcontexts=false, $weight=0) {
        $this->add_regions(array_keys($blocks));
        foreach ($blocks as $region => $regionblocks) {
            $weight = 0;
            foreach ($regionblocks as $blockname) {
                $this->add_block($blockname, $region, $weight, $showinsubcontexts, $pagetypepattern, $subpagepattern);
                $weight += 1;
            }
        }
    }

    /**
     * Move a block to a new position on this page.
     *
     * If this block cannot appear on any other pages, then we change defaultposition/weight
     * in the block_instances table. Otherwise we just set the position on this page.
     *
     * @param $blockinstanceid the block instance id.
     * @param $newregion the new region name.
     * @param $newweight the new weight.
     */
    public function reposition_block($blockinstanceid, $newregion, $newweight) {
        global $DB;

        $this->check_region_is_known($newregion);
        $inst = $this->find_instance($blockinstanceid);

        $bi = $inst->instance;
        if ($bi->weight == $bi->defaultweight && $bi->region == $bi->defaultregion &&
                !$bi->showinsubcontexts && strpos($bi->pagetypepattern, '*') === false &&
                (!$this->page->subpage || $bi->subpagepattern)) {

            // Set default position
            $newbi = new stdClass;
            $newbi->id = $bi->id;
            $newbi->defaultregion = $newregion;
            $newbi->defaultweight = $newweight;
            $DB->update_record('block_instances', $newbi);

            if ($bi->blockpositionid) {
                $bp = new stdClass;
                $bp->id = $bi->blockpositionid;
                $bp->region = $newregion;
                $bp->weight = $newweight;
                $DB->update_record('block_positions', $bp);
            }

        } else {
            // Just set position on this page.
            $bp = new stdClass;
            $bp->region = $newregion;
            $bp->weight = $newweight;

            if ($bi->blockpositionid) {
                $bp->id = $bi->blockpositionid;
                $DB->update_record('block_positions', $bp);

            } else {
                $bp->blockinstanceid = $bi->id;
                $bp->contextid = $this->page->context->id;
                $bp->pagetype = $this->page->pagetype;
                if ($this->page->subpage) {
                    $bp->subpage = $this->page->subpage;
                } else {
                    $bp->subpage = '';
                }
                $bp->visible = $bi->visible;
                $DB->insert_record('block_positions', $bp);
            }
        }
    }

    /**
     * Find a given block by its instance id
     *
     * @param integer $instanceid
     * @return object
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
     * Check whether the page blocks have been loaded yet
     *
     * @return void Throws coding exception if already loaded
     */
    protected function check_not_yet_loaded() {
        if (!is_null($this->birecordsbyregion)) {
            throw new coding_exception('block_manager has already loaded the blocks, to it is too late to change things that might affect which blocks are visible.');
        }
    }

    /**
     * Check whether the page blocks have been loaded yet
     *
     * Nearly identical to the above function {@link check_not_yet_loaded()} except different message
     *
     * @return void Throws coding exception if already loaded
     */
    protected function check_is_loaded() {
        if (is_null($this->birecordsbyregion)) {
            throw new coding_exception('block_manager has not yet loaded the blocks, to it is too soon to request the information you asked for.');
        }
    }

    /**
     * Check if a block type is known and usable
     *
     * @param string $blockname The block type name to search for
     * @param bool $includeinvisible Include disabled block types in the initial pass
     * @return void Coding Exception thrown if unknown or not enabled
     */
    protected function check_known_block_type($blockname, $includeinvisible = false) {
        if (!$this->is_known_block_type($blockname, $includeinvisible)) {
            if ($this->is_known_block_type($blockname, true)) {
                throw new coding_exception('Unknown block type ' . $blockname);
            } else {
                throw new coding_exception('Block type ' . $blockname . ' has been disabled by the administrator.');
            }
        }
    }

    /**
     * Check if a region is known by its name
     *
     * @param string $region
     * @return void Coding Exception thrown if the region is not known
     */
    protected function check_region_is_known($region) {
        if (!$this->is_known_region($region)) {
            throw new coding_exception('Trying to reference an unknown block region ' . $region);
        }
    }

    /**
     * Returns an array of region names as keys and nested arrays for values
     *
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

    /**
     * Create a set of new block instance from a record array
     *
     * @param array $birecords An array of block instance records
     * @return array An array of instantiated block_instance objects
     */
    protected function create_block_instances($birecords) {
        $results = array();
        foreach ($birecords as $record) {
            if ($blockobject = block_instance($record->blockname, $record, $this->page)) {
                $results[] = $blockobject;
            }
        }
        return $results;
    }

    /**
     * Create all the block instances for all the blocks that were loaded by
     * load_blocks. This is used, for example, to ensure that all blocks get a
     * chance to initialise themselves via the {@link block_base::specialize()}
     * method, before any output is done.
     */
    public function create_all_block_instances() {
        foreach ($this->get_regions() as $region) {
            $this->ensure_instances_exist($region);
        }
    }

    /**
     * Return an array of content objects from a set of block instances
     *
     * @param array $instances An array of block instances
     * @param renderer_base The renderer to use.
     * @param string $region the region name.
     * @return array An array of block_content (and possibly block_move_target) objects.
     */
    protected function create_block_contents($instances, $output, $region) {
        $results = array();

        $lastweight = 0;
        $lastblock = 0;
        if ($this->movingblock) {
            $first = reset($instances);
            if ($first) {
                $lastweight = $first->instance->weight - 2;
            }

            $strmoveblockhere = get_string('moveblockhere', 'block');
        }

        foreach ($instances as $instance) {
            $content = $instance->get_content_for_output($output);
            if (empty($content)) {
                continue;
            }

            if ($this->movingblock && $lastweight != $instance->instance->weight &&
                    $content->blockinstanceid != $this->movingblock && $lastblock != $this->movingblock) {
                $results[] = new block_move_target($strmoveblockhere, $this->get_move_target_url($region, ($lastweight + $instance->instance->weight)/2));
            }

            if ($content->blockinstanceid == $this->movingblock) {
                $content->add_class('beingmoved');
                $content->annotation .= get_string('movingthisblockcancel', 'block',
                        html_writer::link($this->page->url, get_string('cancel')));
            }

            $results[] = $content;
            $lastweight = $instance->instance->weight;
            $lastblock = $instance->instance->id;
        }

        if ($this->movingblock && $lastblock != $this->movingblock) {
            $results[] = new block_move_target($strmoveblockhere, $this->get_move_target_url($region, $lastweight + 1));
        }
        return $results;
    }

    /**
     * Ensure block instances exist for a given region
     *
     * @param string $region Check for bi's with the instance with this name
     */
    protected function ensure_instances_exist($region) {
        $this->check_region_is_known($region);
        if (!array_key_exists($region, $this->blockinstances)) {
            $this->blockinstances[$region] =
                    $this->create_block_instances($this->birecordsbyregion[$region]);
        }
    }

    /**
     * Ensure that there is some content within the given region
     *
     * @param string $region The name of the region to check
     */
    protected function ensure_content_created($region, $output) {
        $this->ensure_instances_exist($region);
        if (!array_key_exists($region, $this->visibleblockcontent)) {
            $contents = array();
            if (array_key_exists($region, $this->extracontent)) {
                $contents = $this->extracontent[$region];
            }
            $contents = array_merge($contents, $this->create_block_contents($this->blockinstances[$region], $output, $region));
            if ($region == $this->defaultregion) {
                $addblockui = block_add_block_ui($this->page, $output);
                if ($addblockui) {
                    $contents[] = $addblockui;
                }
            }
            $this->visibleblockcontent[$region] = $contents;
        }
    }

/// Process actions from the URL ===============================================

    /**
     * Get the appropriate list of editing icons for a block. This is used
     * to set {@link block_contents::$controls} in {@link block_base::get_contents_for_output()}.
     *
     * @param $output The core_renderer to use when generating the output. (Need to get icon paths.)
     * @return an array in the format for {@link block_contents::$controls}
     */
    public function edit_controls($block) {
        global $CFG;

        if (!isset($CFG->undeletableblocktypes) || (!is_array($CFG->undeletableblocktypes) && !is_string($CFG->undeletableblocktypes))) {
            $CFG->undeletableblocktypes = array('navigation','settings');
        } else if (is_string($CFG->undeletableblocktypes)) {
            $CFG->undeletableblocktypes = explode(',', $CFG->undeletableblocktypes);
        }

        $controls = array();
        $actionurl = $this->page->url->out(false, array('sesskey'=> sesskey()));

        // Assign roles icon.
        if (has_capability('moodle/role:assign', $block->context)) {
            //TODO: please note it is sloppy to pass urls through page parameters!!
            //      it is shortened because some web servers (e.g. IIS by default) give
            //      a 'security' error if you try to pass a full URL as a GET parameter in another URL.

            $return = $this->page->url->out(false);
            $return = str_replace($CFG->wwwroot . '/', '', $return);

            $controls[] = array('url' => $CFG->wwwroot . '/' . $CFG->admin .
                    '/roles/assign.php?contextid=' . $block->context->id . '&returnurl=' . urlencode($return),
                    'icon' => 'i/roles', 'caption' => get_string('assignroles', 'role'));
        }

        if ($this->page->user_can_edit_blocks() && $block->instance_can_be_hidden()) {
            // Show/hide icon.
            if ($block->instance->visible) {
                $controls[] = array('url' => $actionurl . '&bui_hideid=' . $block->instance->id,
                        'icon' => 't/hide', 'caption' => get_string('hide'));
            } else {
                $controls[] = array('url' => $actionurl . '&bui_showid=' . $block->instance->id,
                        'icon' => 't/show', 'caption' => get_string('show'));
            }
        }

        if ($this->page->user_can_edit_blocks() || $block->user_can_edit()) {
            // Edit config icon - always show - needed for positioning UI.
            $controls[] = array('url' => $actionurl . '&bui_editid=' . $block->instance->id,
                    'icon' => 't/edit', 'caption' => get_string('configuration'));
        }

        if ($this->page->user_can_edit_blocks() && $block->user_can_edit() && $block->user_can_addto($this->page)) {
            if (!in_array($block->instance->blockname, $CFG->undeletableblocktypes)) {
                // Delete icon.
                $controls[] = array('url' => $actionurl . '&bui_deleteid=' . $block->instance->id,
                        'icon' => 't/delete', 'caption' => get_string('delete'));
            }
        }

        if ($this->page->user_can_edit_blocks()) {
            // Move icon.
            $controls[] = array('url' => $actionurl . '&bui_moveid=' . $block->instance->id,
                    'icon' => 't/move', 'caption' => get_string('move'));
        }

        return $controls;
    }

    /**
     * Process any block actions that were specified in the URL.
     *
     * This can only be done given a valid $page object.
     *
     * @param moodle_page $page the page to add blocks to.
     * @return boolean true if anything was done. False if not.
     */
    public function process_url_actions() {
        if (!$this->page->user_is_editing()) {
            return false;
        }
        return $this->process_url_add() || $this->process_url_delete() ||
            $this->process_url_show_hide() || $this->process_url_edit() ||
            $this->process_url_move();
    }

    /**
     * Handle adding a block.
     * @return boolean true if anything was done. False if not.
     */
    public function process_url_add() {
        $blocktype = optional_param('bui_addblock', null, PARAM_SAFEDIR);
        if (!$blocktype) {
            return false;
        }

        require_sesskey();

        if (!$this->page->user_can_edit_blocks()) {
            throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('addblock'));
        }

        if (!array_key_exists($blocktype, $this->get_addable_blocks())) {
            throw new moodle_exception('cannotaddthisblocktype', '', $this->page->url->out(), $blocktype);
        }

        $this->add_block_at_end_of_default_region($blocktype);

        // If the page URL was a guess, it will contain the bui_... param, so we must make sure it is not there.
        $this->page->ensure_param_not_in_url('bui_addblock');

        return true;
    }

    /**
     * Handle deleting a block.
     * @return boolean true if anything was done. False if not.
     */
    public function process_url_delete() {
        $blockid = optional_param('bui_deleteid', null, PARAM_INTEGER);
        if (!$blockid) {
            return false;
        }

        require_sesskey();

        $block = $this->page->blocks->find_instance($blockid);

        if (!$block->user_can_edit() || !$this->page->user_can_edit_blocks() || !$block->user_can_addto($this->page)) {
            throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('deleteablock'));
        }

        blocks_delete_instance($block->instance);

        // If the page URL was a guess, it will contain the bui_... param, so we must make sure it is not there.
        $this->page->ensure_param_not_in_url('bui_deleteid');

        return true;
    }

    /**
     * Handle showing or hiding a block.
     * @return boolean true if anything was done. False if not.
     */
    public function process_url_show_hide() {
        if ($blockid = optional_param('bui_hideid', null, PARAM_INTEGER)) {
            $newvisibility = 0;
        } else if ($blockid = optional_param('bui_showid', null, PARAM_INTEGER)) {
            $newvisibility = 1;
        } else {
            return false;
        }

        require_sesskey();

        $block = $this->page->blocks->find_instance($blockid);

        if (!$this->page->user_can_edit_blocks()) {
            throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('hideshowblocks'));
        } else if (!$block->instance_can_be_hidden()) {
            return false;
        }

        blocks_set_visibility($block->instance, $this->page, $newvisibility);

        // If the page URL was a guses, it will contain the bui_... param, so we must make sure it is not there.
        $this->page->ensure_param_not_in_url('bui_hideid');
        $this->page->ensure_param_not_in_url('bui_showid');

        return true;
    }

    /**
     * Handle showing/processing the submission from the block editing form.
     * @return boolean true if the form was submitted and the new config saved. Does not
     *      return if the editing form was displayed. False otherwise.
     */
    public function process_url_edit() {
        global $CFG, $DB, $PAGE;

        $blockid = optional_param('bui_editid', null, PARAM_INTEGER);
        if (!$blockid) {
            return false;
        }

        require_sesskey();
        require_once($CFG->dirroot . '/blocks/edit_form.php');

        $block = $this->find_instance($blockid);

        if (!$block->user_can_edit() && !$this->page->user_can_edit_blocks()) {
            throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('editblock'));
        }

        $editpage = new moodle_page();
        $editpage->set_pagelayout('admin');
        $editpage->set_course($this->page->course);
        $editpage->set_context($block->context);
        if ($this->page->cm) {
            $editpage->set_cm($this->page->cm);
        }
        $editurlbase = str_replace($CFG->wwwroot . '/', '/', $this->page->url->out_omit_querystring());
        $editurlparams = $this->page->url->params();
        $editurlparams['bui_editid'] = $blockid;
        $editpage->set_url($editurlbase, $editurlparams);
        $editpage->set_block_actions_done();
        // At this point we are either going to redirect, or display the form, so
        // overwrite global $PAGE ready for this. (Formslib refers to it.)
        $PAGE = $editpage;

        $formfile = $CFG->dirroot . '/blocks/' . $block->name() . '/edit_form.php';
        if (is_readable($formfile)) {
            require_once($formfile);
            $classname = 'block_' . $block->name() . '_edit_form';
            if (!class_exists($classname)) {
                $classname = 'block_edit_form';
            }
        } else {
            $classname = 'block_edit_form';
        }

        $mform = new $classname($editpage->url, $block, $this->page);
        $mform->set_data($block->instance);

        if ($mform->is_cancelled()) {
            redirect($this->page->url);

        } else if ($data = $mform->get_data()) {
            $bi = new stdClass;
            $bi->id = $block->instance->id;
            $bi->pagetypepattern = $data->bui_pagetypepattern;
            if (empty($data->bui_subpagepattern) || $data->bui_subpagepattern == '%@NULL@%') {
                $bi->subpagepattern = null;
            } else {
                $bi->subpagepattern = $data->bui_subpagepattern;
            }

            $parentcontext = get_context_instance_by_id($data->bui_parentcontextid);
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);

            // Updating stickiness and contexts.  See MDL-21375 for details.
            if (has_capability('moodle/site:manageblocks', $parentcontext)) { // Check permissions in destination
                // Explicitly set the context
                $bi->parentcontextid = $parentcontext->id;

                // If the context type is > 0 then we'll explicitly set the block as sticky, otherwise not
                $bi->showinsubcontexts = (int)(!empty($data->bui_contexts));

                // If the block wants to be system-wide, then explicitly set that
                if ($data->bui_contexts == 2) {   // Only possible on a frontpage or system page
                    $bi->parentcontextid = $systemcontext->id;

                } else { // The block doesn't want to be system-wide, so let's ensure that
                    if ($parentcontext->id == $systemcontext->id) {  // We need to move it to the front page
                        $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
                        $bi->parentcontextid = $frontpagecontext->id;
                        $bi->pagetypepattern = '*';  // Just in case
                    }
                }
            }

            $bi->defaultregion = $data->bui_defaultregion;
            $bi->defaultweight = $data->bui_defaultweight;
            $DB->update_record('block_instances', $bi);

            if (!empty($block->config)) {
                $config = clone($block->config);
            } else {
                $config = new stdClass;
            }
            foreach ($data as $configfield => $value) {
                if (strpos($configfield, 'config_') !== 0) {
                    continue;
                }
                $field = substr($configfield, 7);
                $config->$field = $value;
            }
            $block->instance_config_save($config);

            $bp = new stdClass;
            $bp->visible = $data->bui_visible;
            $bp->region = $data->bui_region;
            $bp->weight = $data->bui_weight;
            $needbprecord = !$data->bui_visible || $data->bui_region != $data->bui_defaultregion ||
                    $data->bui_weight != $data->bui_defaultweight;

            if ($block->instance->blockpositionid && !$needbprecord) {
                $DB->delete_records('block_positions', array('id' => $block->instance->blockpositionid));

            } else if ($block->instance->blockpositionid && $needbprecord) {
                $bp->id = $block->instance->blockpositionid;
                $DB->update_record('block_positions', $bp);

            } else if ($needbprecord) {
                $bp->blockinstanceid = $block->instance->id;
                $bp->contextid = $this->page->context->id;
                $bp->pagetype = $this->page->pagetype;
                if ($this->page->subpage) {
                    $bp->subpage = $this->page->subpage;
                } else {
                    $bp->subpage = '';
                }
                $DB->insert_record('block_positions', $bp);
            }

            redirect($this->page->url);

        } else {
            $strheading = get_string('blockconfiga', 'moodle', $block->get_title());
            $editpage->set_title($strheading);
            $editpage->set_heading($strheading);
            $editpage->navbar->add($strheading);
            $output = $editpage->get_renderer('core');
            echo $output->header();
            echo $output->heading($strheading, 2);
            $mform->display();
            echo $output->footer();
            exit;
        }
    }

    /**
     * Handle showing/processing the submission from the block editing form.
     * @return boolean true if the form was submitted and the new config saved. Does not
     *      return if the editing form was displayed. False otherwise.
     */
    public function process_url_move() {
        global $CFG, $DB, $PAGE;

        $blockid = optional_param('bui_moveid', null, PARAM_INTEGER);
        if (!$blockid) {
            return false;
        }

        require_sesskey();

        $block = $this->find_instance($blockid);

        if (!$this->page->user_can_edit_blocks()) {
            throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('editblock'));
        }

        $newregion = optional_param('bui_newregion', '', PARAM_ALPHANUMEXT);
        $newweight = optional_param('bui_newweight', null, PARAM_FLOAT);
        if (!$newregion || is_null($newweight)) {
            // Don't have a valid target position yet, must be just starting the move.
            $this->movingblock = $blockid;
            $this->page->ensure_param_not_in_url('bui_moveid');
            return false;
        }

        if (!$this->is_known_region($newregion)) {
            throw new moodle_exception('unknownblockregion', '', $this->page->url, $newregion);
        }

        // Move this block. This may involve moving other nearby blocks.
        $blocks = $this->birecordsbyregion[$newregion];

        $maxweight = self::MAX_WEIGHT;
        $minweight = -self::MAX_WEIGHT;

        // Initialise the used weights and spareweights array with the default values
        $spareweights = array();
        $usedweights = array();
        for ($i = $minweight; $i <= $maxweight; $i++) {
            $spareweights[$i] = $i;
            $usedweights[$i] = array();
        }

        // Check each block and sort out where we have used weights
        foreach ($blocks as $bi) {
            if ($bi->weight > $maxweight) {
                // If this statement is true then the blocks weight is more than the
                // current maximum. To ensure that we can get the best block position
                // we will initialise elements within the usedweights and spareweights
                // arrays between the blocks weight (which will then be the new max) and
                // the current max
                $parseweight = $bi->weight;
                while (!array_key_exists($parseweight, $usedweights)) {
                    $usedweights[$parseweight] = array();
                    $spareweights[$parseweight] = $parseweight;
                    $parseweight--;
                }
                $maxweight = $bi->weight;
            } else if ($bi->weight < $minweight) {
                // As above except this time the blocks weight is LESS than the
                // the current minimum, so we will initialise the array from the
                // blocks weight (new minimum) to the current minimum
                $parseweight = $bi->weight;
                while (!array_key_exists($parseweight, $usedweights)) {
                    $usedweights[$parseweight] = array();
                    $spareweights[$parseweight] = $parseweight;
                    $parseweight++;
                }
                $minweight = $bi->weight;
            }
            if ($bi->id != $block->instance->id) {
                unset($spareweights[$bi->weight]);
                $usedweights[$bi->weight][] = $bi->id;
            }
        }

        // First we find the nearest gap in the list of weights.
        $bestdistance = max(abs($newweight - self::MAX_WEIGHT), abs($newweight + self::MAX_WEIGHT)) + 1;
        $bestgap = null;
        foreach ($spareweights as $spareweight) {
            if (abs($newweight - $spareweight) < $bestdistance) {
                $bestdistance = abs($newweight - $spareweight);
                $bestgap = $spareweight;
            }
        }

        // If there is no gap, we have to go outside -self::MAX_WEIGHT .. self::MAX_WEIGHT.
        if (is_null($bestgap)) {
            $bestgap = self::MAX_WEIGHT + 1;
            while (!empty($usedweights[$bestgap])) {
                $bestgap++;
            }
        }

        // Now we know the gap we are aiming for, so move all the blocks along.
        if ($bestgap < $newweight) {
            $newweight = floor($newweight);
            for ($weight = $bestgap + 1; $weight <= $newweight; $weight++) {
                foreach ($usedweights[$weight] as $biid) {
                    $this->reposition_block($biid, $newregion, $weight - 1);
                }
            }
            $this->reposition_block($block->instance->id, $newregion, $newweight);
        } else {
            $newweight = ceil($newweight);
            for ($weight = $bestgap - 1; $weight >= $newweight; $weight--) {
                foreach ($usedweights[$weight] as $biid) {
                    $this->reposition_block($biid, $newregion, $weight + 1);
                }
            }
            $this->reposition_block($block->instance->id, $newregion, $newweight);
        }

        $this->page->ensure_param_not_in_url('bui_moveid');
        $this->page->ensure_param_not_in_url('bui_newregion');
        $this->page->ensure_param_not_in_url('bui_newweight');
        return true;
    }

    /**
     * Turns the display of normal blocks either on or off.
     *
     * @param bool $setting
     */
    public function show_only_fake_blocks($setting = true) {
        $this->fakeblocksonly = $setting;
    }
}

/// Helper functions for working with block classes ============================

/**
 * Call a class method (one that does not require a block instance) on a block class.
 *
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
 * Creates a new instance of the specified block class.
 *
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
 *
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

    $blockpath = $CFG->dirroot.'/blocks/'.$blockname.'/block_'.$blockname.'.php';

    if (file_exists($blockpath)) {
        require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
        include_once($blockpath);
    }else{
        //debugging("$blockname code does not exist in $blockpath", DEBUG_DEVELOPER);
        return false;
    }

    return class_exists($classname);
}

/**
 * Given a specific page type, return all the page type patterns that might
 * match it.
 *
 * @param string $pagetype for example 'course-view-weeks' or 'mod-quiz-view'.
 * @return array an array of all the page type patterns that might match this page type.
 */
function matching_page_type_patterns($pagetype) {
    $patterns = array($pagetype);
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
    $patterns[] = '*';
    return $patterns;
}

/// Functions update the blocks if required by the request parameters ==========

/**
 * Return a {@link block_contents} representing the add a new block UI, if
 * this user is allowed to see it.
 *
 * @return block_contents an appropriate block_contents, or null if the user
 * cannot add any blocks here.
 */
function block_add_block_ui($page, $output) {
    global $CFG, $OUTPUT;
    if (!$page->user_is_editing() || !$page->user_can_edit_blocks()) {
        return null;
    }

    $bc = new block_contents();
    $bc->title = get_string('addblock');
    $bc->add_class('block_adminblock');

    $missingblocks = $page->blocks->get_addable_blocks();
    if (empty($missingblocks)) {
        $bc->content = get_string('noblockstoaddhere');
        return $bc;
    }

    $menu = array();
    foreach ($missingblocks as $block) {
        $blockobject = block_instance($block->name);
        if ($blockobject !== false && $blockobject->user_can_addto($page)) {
            $menu[$block->name] = $blockobject->get_title();
        }
    }
    textlib_get_instance()->asort($menu);

    $actionurl = new moodle_url($page->url, array('sesskey'=>sesskey()));
    $select = new single_select($actionurl, 'bui_addblock', $menu, null, array(''=>get_string('adddots')), 'add_block');
    $bc->content = $OUTPUT->render($select);
    return $bc;
}

// Functions that have been deprecated by block_manager =======================

/**
 * @deprecated since Moodle 2.0 - use $page->blocks->get_addable_blocks();
 *
 * This function returns an array with the IDs of any blocks that you can add to your page.
 * Parameters are passed by reference for speed; they are not modified at all.
 *
 * @param $page the page object.
 * @param $blockmanager Not used.
 * @return array of block type ids.
 */
function blocks_get_missing(&$page, &$blockmanager) {
    debugging('blocks_get_missing is deprecated. Please use $page->blocks->get_addable_blocks() instead.', DEBUG_DEVELOPER);
    $blocks = $page->blocks->get_addable_blocks();
    $ids = array();
    foreach ($blocks as $block) {
        $ids[] = $block->id;
    }
    return $ids;
}

/**
 * Actually delete from the database any blocks that are currently on this page,
 * but which should not be there according to blocks_name_allowed_in_format.
 *
 * @todo Write/Fix this function. Currently returns immediately
 * @param $course
 */
function blocks_remove_inappropriate($course) {
    // TODO
    return;
    /*
    $blockmanager = blocks_get_by_page($page);

    if (empty($blockmanager)) {
        return;
    }

    if (($pageformat = $page->pagetype) == NULL) {
        return;
    }

    foreach($blockmanager as $region) {
        foreach($region as $instance) {
            $block = blocks_get_record($instance->blockid);
            if(!blocks_name_allowed_in_format($block->name, $pageformat)) {
               blocks_delete_instance($instance->instance);
            }
        }
    }*/
}

/**
 * Check that a given name is in a permittable format
 *
 * @param string $name
 * @param string $pageformat
 * @return bool
 */
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
 *
 * @param object $instance a row from the block_instances table
 * @param bool $nolongerused legacy parameter. Not used, but kept for backwards compatibility.
 * @param bool $skipblockstables for internal use only. Makes @see blocks_delete_all_for_context() more efficient.
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
        $DB->delete_records_list('user_preferences', 'name', array('block'.$instance->id.'hidden','docked_block_instance_'.$instance->id));
    }
}

/**
 * Delete all the blocks that belong to a particular context.
 *
 * @param int $contextid the context id.
 */
function blocks_delete_all_for_context($contextid) {
    global $DB;
    $instances = $DB->get_recordset('block_instances', array('parentcontextid' => $contextid));
    foreach ($instances as $instance) {
        blocks_delete_instance($instance, true);
    }
    $instances->close();
    $DB->delete_records('block_instances', array('parentcontextid' => $contextid));
    $DB->delete_records('block_positions', array('contextid' => $contextid));
}

/**
 * Set a block to be visible or hidden on a particular page.
 *
 * @param object $instance a row from the block_instances, preferably LEFT JOINed with the
 *      block_positions table as return by block_manager.
 * @param moodle_page $page the back to set the visibility with respect to.
 * @param integer $newvisibility 1 for visible, 0 for hidden.
 */
function blocks_set_visibility($instance, $page, $newvisibility) {
    global $DB;
    if (!empty($instance->blockpositionid)) {
        // Already have local information on this page.
        $DB->set_field('block_positions', 'visible', $newvisibility, array('id' => $instance->blockpositionid));
        return;
    }

    // Create a new block_positions record.
    $bp = new stdClass;
    $bp->blockinstanceid = $instance->id;
    $bp->contextid = $page->context->id;
    $bp->pagetype = $page->pagetype;
    if ($page->subpage) {
        $bp->subpage = $page->subpage;
    }
    $bp->visible = $newvisibility;
    $bp->region = $instance->defaultregion;
    $bp->weight = $instance->defaultweight;
    $DB->insert_record('block_positions', $bp);
}

/**
 * @deprecated since 2.0
 * Delete all the blocks from a particular page.
 *
 * @param string $pagetype the page type.
 * @param integer $pageid the page id.
 * @return bool success or failure.
 */
function blocks_delete_all_on_page($pagetype, $pageid) {
    global $DB;

    debugging('Call to deprecated function blocks_delete_all_on_page. ' .
            'This function cannot work any more. Doing nothing. ' .
            'Please update your code to use a block_manager method $PAGE->blocks->....', DEBUG_DEVELOPER);
    return false;
}

/**
 * Dispite what this function is called, it seems to be mostly used to populate
 * the default blocks when a new course (or whatever) is created.
 *
 * @deprecated since 2.0
 *
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

/**
 * Get the block record for a particular blockid - that is, a particular type os block.
 *
 * @param $int blockid block type id. If null, an array of all block types is returned.
 * @param bool $notusedanymore No longer used.
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

/**
 * Find a given block by its blockid within a provide array
 *
 * @param int $blockid
 * @param array $blocksarray
 * @return bool|object Instance if found else false
 */
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

// Functions for programatically adding default blocks to pages ================

/**
 * Parse a list of default blocks. See config-dist for a description of the format.
 *
 * @param string $blocksstr
 * @return array
 */
function blocks_parse_default_blocks_list($blocksstr) {
    $blocks = array();
    $bits = explode(':', $blocksstr);
    if (!empty($bits)) {
        $leftbits = trim(array_shift($bits));
        if ($leftbits != '') {
            $blocks[BLOCK_POS_LEFT] = explode(',', $leftbits);
        }
    }
    if (!empty($bits)) {
        $rightbits =trim(array_shift($bits));
        if ($rightbits != '') {
            $blocks[BLOCK_POS_RIGHT] = explode(',', $rightbits);
        }
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
            BLOCK_POS_LEFT => array('site_main_menu'),
            BLOCK_POS_RIGHT => array('course_summary', 'calendar_month')
        );
    }
}

/**
 * Add the default blocks to a course.
 *
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
            $format = array(); // initialize array in external file
            if (is_readable($formatconfig)) {
                include($formatconfig);
            }
            if (!empty($format['defaultblocks'])) {
                $blocknames = blocks_parse_default_blocks_list($format['defaultblocks']);

            } else if (!empty($CFG->defaultblocks)){
                $blocknames = blocks_parse_default_blocks_list($CFG->defaultblocks);

            } else {
                $blocknames = array(
                    BLOCK_POS_LEFT => array(),
                    BLOCK_POS_RIGHT => array('search_forums', 'news_items', 'calendar_upcoming', 'recent_activity')
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
    global $DB;

    $page = new moodle_page();
    $page->set_context(get_context_instance(CONTEXT_SYSTEM));
    $page->blocks->add_blocks(array(BLOCK_POS_LEFT => array('navigation', 'settings')), '*', null, true);
    $page->blocks->add_blocks(array(BLOCK_POS_LEFT => array('admin_bookmarks')), 'admin-*', null, null, 2);

    if ($defaultmypage = $DB->get_record('my_pages', array('userid'=>null, 'name'=>'__default', 'private'=>1))) {
        $subpagepattern = $defaultmypage->id;
    } else {
        $subpagepattern = null;
    }

    $page->blocks->add_blocks(array(BLOCK_POS_RIGHT => array('private_files', 'online_users'), 'content' => array('course_overview')), 'my-index', $subpagepattern, false);
}
