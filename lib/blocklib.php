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
 * Default names for the block regions in the standard theme.
 */
define('BLOCK_POS_LEFT',  'side-pre');
define('BLOCK_POS_RIGHT', 'side-post');
/**#@-*/

define('BUI_CONTEXTS_FRONTPAGE_ONLY', 0);
define('BUI_CONTEXTS_FRONTPAGE_SUBS', 1);
define('BUI_CONTEXTS_ENTIRE_SITE', 2);

define('BUI_CONTEXTS_CURRENT', 0);
define('BUI_CONTEXTS_CURRENT_SUBS', 1);

// Position of "Add block" control, to be used in theme config as a value for $THEME->addblockposition:
// - default: as a fake block that is displayed in editing mode
// - flatnav: "Add block" item in the flat navigation drawer in editing mode
// - custom: none of the above, theme will take care of displaying the control.
define('BLOCK_ADDBLOCK_POSITION_DEFAULT', 0);
define('BLOCK_ADDBLOCK_POSITION_FLATNAV', 1);
define('BLOCK_ADDBLOCK_POSITION_CUSTOM', -1);

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

        $unaddableblocks = self::get_undeletable_block_types();
        $requiredbythemeblocks = $this->get_required_by_theme_block_types();
        $pageformat = $this->page->pagetype;
        foreach($allblocks as $block) {
            if (!$bi = block_instance($block->name)) {
                continue;
            }
            if ($block->visible && !in_array($block->name, $unaddableblocks) &&
                    !in_array($block->name, $requiredbythemeblocks) &&
                    ($bi->instance_allow_multiple() || !$this->is_block_present($block->name)) &&
                    blocks_name_allowed_in_format($block->name, $pageformat) &&
                    $bi->user_can_addto($this->page)) {
                $block->title = $bi->get_title();
                $this->addableblocks[$block->name] = $block;
            }
        }

        core_collator::asort_objects_by_property($this->addableblocks, 'title');
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

        $requiredbythemeblocks = $this->get_required_by_theme_block_types();
        foreach ($this->blockinstances as $region) {
            foreach ($region as $instance) {
                if (empty($instance->instance->blockname)) {
                    continue;
                }
                if ($instance->instance->blockname == $blockname) {
                    if ($instance->instance->requiredbytheme) {
                        if (!in_array($blockname, $requiredbythemeblocks)) {
                            continue;
                        }
                    }
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
        if (empty($region)) {
            return false;
        }
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
     * Returns an array of block content objects for all the existings regions
     *
     * @param renderer_base $output the rendered to use
     * @return array of block block_contents objects for all the blocks in all regions.
     * @since  Moodle 3.3
     */
    public function get_content_for_all_regions($output) {
        $contents = array();
        $this->check_is_loaded();

        foreach ($this->regions as $region => $val) {
            $this->ensure_content_created($region, $output);
            $contents[$region] = $this->visibleblockcontent[$region];
        }
        return $contents;
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
     * @param core_renderer $output a core_renderer. normally the global $OUTPUT.
     * @return boolean Whether there is anything in this region.
     */
    public function region_has_content($region, $output) {

        if (!$this->is_known_region($region)) {
            return false;
        }
        $this->check_is_loaded();
        $this->ensure_content_created($region, $output);
        // if ($this->page->user_is_editing() && $this->page->user_can_edit_blocks()) {
        // Mark Nielsen's patch - part 1
        if ($this->page->user_is_editing() && $this->page->user_can_edit_blocks() && $this->movingblock) {
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

    /**
     * @return array names of block types that must exist on every page with this theme.
     */
    public function get_required_by_theme_block_types() {
        $requiredbythemeblocks = false;
        if (isset($this->page->theme->requiredblocks)) {
            $requiredbythemeblocks = $this->page->theme->requiredblocks;
        }

        if ($requiredbythemeblocks === false) {
            return array('navigation', 'settings');
        } else if ($requiredbythemeblocks === '') {
            return array();
        } else if (is_string($requiredbythemeblocks)) {
            return explode(',', $requiredbythemeblocks);
        } else {
            return $requiredbythemeblocks;
        }
    }

    /**
     * Make this block type undeletable and unaddable.
     *
     * @param mixed $blockidorname string or int
     */
    public static function protect_block($blockidorname) {
        global $DB;

        $syscontext = context_system::instance();

        require_capability('moodle/site:config', $syscontext);

        $block = false;
        if (is_int($blockidorname)) {
            $block = $DB->get_record('block', array('id' => $blockidorname), 'id, name', MUST_EXIST);
        } else {
            $block = $DB->get_record('block', array('name' => $blockidorname), 'id, name', MUST_EXIST);
        }
        $undeletableblocktypes = self::get_undeletable_block_types();
        if (!in_array($block->name, $undeletableblocktypes)) {
            $undeletableblocktypes[] = $block->name;
            set_config('undeletableblocktypes', implode(',', $undeletableblocktypes));
            add_to_config_log('block_protect', "0", "1", $block->name);
        }
    }

    /**
     * Make this block type deletable and addable.
     *
     * @param mixed $blockidorname string or int
     */
    public static function unprotect_block($blockidorname) {
        global $DB;

        $syscontext = context_system::instance();

        require_capability('moodle/site:config', $syscontext);

        $block = false;
        if (is_int($blockidorname)) {
            $block = $DB->get_record('block', array('id' => $blockidorname), 'id, name', MUST_EXIST);
        } else {
            $block = $DB->get_record('block', array('name' => $blockidorname), 'id, name', MUST_EXIST);
        }
        $undeletableblocktypes = self::get_undeletable_block_types();
        if (in_array($block->name, $undeletableblocktypes)) {
            $undeletableblocktypes = array_diff($undeletableblocktypes, array($block->name));
            set_config('undeletableblocktypes', implode(',', $undeletableblocktypes));
            add_to_config_log('block_protect', "1", "0", $block->name);
        }

    }

    /**
     * Get the list of "protected" blocks via admin block manager ui.
     *
     * @return array names of block types that cannot be added or deleted. E.g. array('navigation','settings').
     */
    public static function get_undeletable_block_types() {
        global $CFG;
        $undeletableblocks = false;
        if (isset($CFG->undeletableblocktypes)) {
            $undeletableblocks = $CFG->undeletableblocktypes;
        }

        if (empty($undeletableblocks)) {
            return array();
        } else if (is_string($undeletableblocks)) {
            return explode(',', $undeletableblocks);
        } else {
            return $undeletableblocks;
        }
    }

/// Setter methods =============================================================

    /**
     * Add a region to a page
     *
     * @param string $region add a named region where blocks may appear on the current page.
     *      This is an internal name, like 'side-pre', not a string to display in the UI.
     * @param bool $custom True if this is a custom block region, being added by the page rather than the theme layout.
     */
    public function add_region($region, $custom = true) {
        global $SESSION;
        $this->check_not_yet_loaded();
        if ($custom) {
            if (array_key_exists($region, $this->regions)) {
                // This here is EXACTLY why we should not be adding block regions into a page. It should
                // ALWAYS be done in a theme layout.
                debugging('A custom region conflicts with a block region in the theme.', DEBUG_DEVELOPER);
            }
            // We need to register this custom region against the page type being used.
            // This allows us to check, when performing block actions, that unrecognised regions can be worked with.
            $type = $this->page->pagetype;
            if (!isset($SESSION->custom_block_regions)) {
                $SESSION->custom_block_regions = array($type => array($region));
            } else if (!isset($SESSION->custom_block_regions[$type])) {
                $SESSION->custom_block_regions[$type] = array($region);
            } else if (!in_array($region, $SESSION->custom_block_regions[$type])) {
                $SESSION->custom_block_regions[$type][] = $region;
            }
        }
        $this->regions[$region] = 1;
    }

    /**
     * Add an array of regions
     * @see add_region()
     *
     * @param array $regions this utility method calls add_region for each array element.
     */
    public function add_regions($regions, $custom = true) {
        foreach ($regions as $region) {
            $this->add_region($region, $custom);
        }
    }

    /**
     * Finds custom block regions associated with a page type and registers them with this block manager.
     *
     * @param string $pagetype
     */
    public function add_custom_regions_for_pagetype($pagetype) {
        global $SESSION;
        if (isset($SESSION->custom_block_regions[$pagetype])) {
            foreach ($SESSION->custom_block_regions[$pagetype] as $customregion) {
                $this->add_region($customregion, false);
            }
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
        if (!isset($bc->attributes['data-block'])) {
            $bc->attributes['data-block'] = '_fake';
        }
        $bc->attributes['class'] .= ' block_fake';
        $this->extracontent[$region][] = $bc;
    }

    /**
     * Checks to see whether all of the blocks within the given region are docked
     *
     * @see region_uses_dock
     * @param string $region
     * @return bool True if all of the blocks within that region are docked
     */
    public function region_completely_docked($region, $output) {
        global $CFG;
        // If theme doesn't allow docking or allowblockstodock is not set, then return.
        if (!$this->page->theme->enable_dock || empty($CFG->allowblockstodock)) {
            return false;
        }

        // Do not dock the region when the user attemps to move a block.
        if ($this->movingblock) {
            return false;
        }

        // Block regions should not be docked during editing when all the blocks are hidden.
        if ($this->page->user_is_editing() && $this->page->user_can_edit_blocks()) {
            return false;
        }

        $this->check_is_loaded();
        $this->ensure_content_created($region, $output);
        if (!$this->region_has_content($region, $output)) {
            // If the region has no content then nothing is docked at all of course.
            return false;
        }
        foreach ($this->visibleblockcontent[$region] as $instance) {
            if (!get_user_preferences('docked_block_instance_'.$instance->blockinstanceid, 0)) {
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

        // Exclude auto created blocks if they are not undeletable in this theme.
        $requiredbytheme = $this->get_required_by_theme_block_types();
        $requiredbythemecheck = '';
        $requiredbythemeparams = array();
        $requiredbythemenotparams = array();
        if (!empty($requiredbytheme)) {
            list($testsql, $requiredbythemeparams) = $DB->get_in_or_equal($requiredbytheme, SQL_PARAMS_NAMED, 'requiredbytheme');
            list($testnotsql, $requiredbythemenotparams) = $DB->get_in_or_equal($requiredbytheme, SQL_PARAMS_NAMED,
                                                                                'notrequiredbytheme', false);
            $requiredbythemecheck = 'AND ((bi.blockname ' . $testsql . ' AND bi.requiredbytheme = 1) OR ' .
                                ' (bi.blockname ' . $testnotsql . ' AND bi.requiredbytheme = 0))';
        } else {
            $requiredbythemecheck = 'AND (bi.requiredbytheme = 0)';
        }

        if (is_null($includeinvisible)) {
            $includeinvisible = $this->page->user_is_editing();
        }
        if ($includeinvisible) {
            $visiblecheck = '';
        } else {
            $visiblecheck = 'AND (bp.visible = 1 OR bp.visible IS NULL) AND (bs.visible = 1 OR bs.visible IS NULL)';
        }

        $context = $this->page->context;
        $contexttest = 'bi.parentcontextid IN (:contextid2, :contextid3)';
        $parentcontextparams = array();
        $parentcontextids = $context->get_parent_context_ids();
        if ($parentcontextids) {
            list($parentcontexttest, $parentcontextparams) =
                    $DB->get_in_or_equal($parentcontextids, SQL_PARAMS_NAMED, 'parentcontext');
            $contexttest = "($contexttest OR (bi.showinsubcontexts = 1 AND bi.parentcontextid $parentcontexttest))";
        }

        $pagetypepatterns = matching_page_type_patterns($this->page->pagetype);
        list($pagetypepatterntest, $pagetypepatternparams) =
                $DB->get_in_or_equal($pagetypepatterns, SQL_PARAMS_NAMED, 'pagetypepatterntest');

        $ccselect = ', ' . context_helper::get_preload_record_columns_sql('ctx');
        $ccjoin = "LEFT JOIN {context} ctx ON (ctx.instanceid = bi.id AND ctx.contextlevel = :contextlevel)";

        $systemcontext = context_system::instance();
        $params = array(
            'contextlevel' => CONTEXT_BLOCK,
            'subpage1' => $this->page->subpage,
            'subpage2' => $this->page->subpage,
            'subpage3' => $this->page->subpage,
            'contextid1' => $context->id,
            'contextid2' => $context->id,
            'contextid3' => $systemcontext->id,
            'contextid4' => $systemcontext->id,
            'pagetype' => $this->page->pagetype,
            'pagetype2' => $this->page->pagetype,
        );
        if ($this->page->subpage === '') {
            $params['subpage1'] = '';
            $params['subpage2'] = '';
            $params['subpage3'] = '';
        }
        $sql = "SELECT
                    bi.id,
                    COALESCE(bp.id, bs.id) AS blockpositionid,
                    bi.blockname,
                    bi.parentcontextid,
                    bi.showinsubcontexts,
                    bi.pagetypepattern,
                    bi.requiredbytheme,
                    bi.subpagepattern,
                    bi.defaultregion,
                    bi.defaultweight,
                    COALESCE(bp.visible, bs.visible, 1) AS visible,
                    COALESCE(bp.region, bs.region, bi.defaultregion) AS region,
                    COALESCE(bp.weight, bs.weight, bi.defaultweight) AS weight,
                    bi.configdata
                    $ccselect

                FROM {block_instances} bi
                JOIN {block} b ON bi.blockname = b.name
                LEFT JOIN {block_positions} bp ON bp.blockinstanceid = bi.id
                                                  AND bp.contextid = :contextid1
                                                  AND bp.pagetype = :pagetype
                                                  AND bp.subpage = :subpage1
                LEFT JOIN {block_positions} bs ON bs.blockinstanceid = bi.id
                                                  AND bs.contextid = :contextid4
                                                  AND bs.pagetype = :pagetype2
                                                  AND bs.subpage = :subpage3
                $ccjoin

                WHERE
                $contexttest
                AND bi.pagetypepattern $pagetypepatterntest
                AND (bi.subpagepattern IS NULL OR bi.subpagepattern = :subpage2)
                $visiblecheck
                AND b.visible = 1
                $requiredbythemecheck

                ORDER BY
                    COALESCE(bp.region, bs.region, bi.defaultregion),
                    COALESCE(bp.weight, bs.weight, bi.defaultweight),
                    bi.id";

        $allparams = $params + $parentcontextparams + $pagetypepatternparams + $requiredbythemeparams + $requiredbythemenotparams;
        $blockinstances = $DB->get_recordset_sql($sql, $allparams);

        $this->birecordsbyregion = $this->prepare_per_region_arrays();
        $unknown = array();
        foreach ($blockinstances as $bi) {
            context_helper::preload_from_record($bi);
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
        $blockinstance->timecreated = time();
        $blockinstance->timemodified = $blockinstance->timecreated;
        $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

        // Ensure the block context is created.
        context_block::instance($blockinstance->id);

        // If the new instance was created, allow it to do additional setup
        if ($block = block_instance($blockname, $blockinstance)) {
            $block->instance_create();
        }
    }

    public function add_block_at_end_of_default_region($blockname) {
        if (empty($this->birecordsbyregion)) {
            // No blocks or block regions exist yet.
            return;
        }
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

        // We should end using this for ALL the blocks, making always the 1st option
        // the default one to be used. Until then, this is one hack to avoid the
        // 'pagetypewarning' message on blocks initial edition (MDL-27829) caused by
        // non-existing $pagetypepattern set. This way at least we guarantee one "valid"
        // (the FIRST $pagetypepattern will be set)

        // We are applying it to all blocks created in mod pages for now and only if the
        // default pagetype is not one of the available options
        if (preg_match('/^mod-.*-/', $pagetypepattern)) {
            $pagetypelist = generate_page_type_patterns($this->page->pagetype, null, $this->page->context);
            // Only go for the first if the pagetype is not a valid option
            if (is_array($pagetypelist) && !array_key_exists($pagetypepattern, $pagetypelist)) {
                $pagetypepattern = key($pagetypelist);
            }
        }
        // Surely other pages like course-report will need this too, they just are not important
        // enough now. This will be decided in the coming days. (MDL-27829, MDL-28150)

        $this->add_block($blockname, $defaulregion, $weight, false, $pagetypepattern, $subpage);
    }

    /**
     * Convenience method, calls add_block repeatedly for all the blocks in $blocks. Optionally, a starting weight
     * can be used to decide the starting point that blocks are added in the region, the weight is passed to {@link add_block}
     * and incremented by the position of the block in the $blocks array
     *
     * @param array $blocks array with array keys the region names, and values an array of block names.
     * @param string $pagetypepattern optional. Passed to {@link add_block()}
     * @param string $subpagepattern optional. Passed to {@link add_block()}
     * @param boolean $showinsubcontexts optional. Passed to {@link add_block()}
     * @param integer $weight optional. Determines the starting point that the blocks are added in the region.
     */
    public function add_blocks($blocks, $pagetypepattern = NULL, $subpagepattern = NULL, $showinsubcontexts=false, $weight=0) {
        $initialweight = $weight;
        $this->add_regions(array_keys($blocks), false);
        foreach ($blocks as $region => $regionblocks) {
            foreach ($regionblocks as $offset => $blockname) {
                $weight = $initialweight + $offset;
                $this->add_block($blockname, $region, $weight, $showinsubcontexts, $pagetypepattern, $subpagepattern);
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
            $newbi->timemodified = time();
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
     * @return block_base
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
     *
     * It is also used to create any blocks that are "requiredbytheme" by the current theme.
     * These blocks that are auto-created have requiredbytheme set on the block instance
     * so they are only visible on themes that require them.
     */
    public function create_all_block_instances() {
        $missing = false;

        // If there are any un-removable blocks that were not created - force them.
        $requiredbytheme = $this->get_required_by_theme_block_types();
        if (!$this->fakeblocksonly) {
            foreach ($requiredbytheme as $forced) {
                if (empty($forced)) {
                    continue;
                }
                $found = false;
                foreach ($this->get_regions() as $region) {
                    foreach($this->birecordsbyregion[$region] as $instance) {
                        if ($instance->blockname == $forced) {
                            $found = true;
                        }
                    }
                }
                if (!$found) {
                    $this->add_block_required_by_theme($forced);
                    $missing = true;
                }
            }
        }

        if ($missing) {
            // Some blocks were missing. Lets do it again.
            $this->birecordsbyregion = null;
            $this->load_blocks();
        }
        foreach ($this->get_regions() as $region) {
            $this->ensure_instances_exist($region);
        }

    }

    /**
     * Add a block that is required by the current theme but has not been
     * created yet. This is a special type of block that only shows in themes that
     * require it (by listing it in undeletable_block_types).
     *
     * @param string $blockname the name of the block type.
     */
    protected function add_block_required_by_theme($blockname) {
        global $DB;

        if (empty($this->birecordsbyregion)) {
            // No blocks or block regions exist yet.
            return;
        }

        // Never auto create blocks when we are showing fake blocks only.
        if ($this->fakeblocksonly) {
            return;
        }

        // Never add a duplicate block required by theme.
        if ($DB->record_exists('block_instances', array('blockname' => $blockname, 'requiredbytheme' => 1))) {
            return;
        }

        $systemcontext = context_system::instance();
        $defaultregion = $this->get_default_region();
        // Add a special system wide block instance only for themes that require it.
        $blockinstance = new stdClass;
        $blockinstance->blockname = $blockname;
        $blockinstance->parentcontextid = $systemcontext->id;
        $blockinstance->showinsubcontexts = true;
        $blockinstance->requiredbytheme = true;
        $blockinstance->pagetypepattern = '*';
        $blockinstance->subpagepattern = null;
        $blockinstance->defaultregion = $defaultregion;
        $blockinstance->defaultweight = 0;
        $blockinstance->configdata = '';
        $blockinstance->timecreated = time();
        $blockinstance->timemodified = $blockinstance->timecreated;
        $blockinstance->id = $DB->insert_record('block_instances', $blockinstance);

        // Ensure the block context is created.
        context_block::instance($blockinstance->id);

        // If the new instance was created, allow it to do additional setup.
        if ($block = block_instance($blockname, $blockinstance)) {
            $block->instance_create();
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
        }

        foreach ($instances as $instance) {
            $content = $instance->get_content_for_output($output);
            if (empty($content)) {
                continue;
            }

            if ($this->movingblock && $lastweight != $instance->instance->weight &&
                    $content->blockinstanceid != $this->movingblock && $lastblock != $this->movingblock) {
                $results[] = new block_move_target($this->get_move_target_url($region, ($lastweight + $instance->instance->weight)/2));
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
            $results[] = new block_move_target($this->get_move_target_url($region, $lastweight + 1));
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
    public function ensure_content_created($region, $output) {
        $this->ensure_instances_exist($region);
        if (!array_key_exists($region, $this->visibleblockcontent)) {
            $contents = array();
            if (array_key_exists($region, $this->extracontent)) {
                $contents = $this->extracontent[$region];
            }
            $contents = array_merge($contents, $this->create_block_contents($this->blockinstances[$region], $output, $region));
            if (($region == $this->defaultregion) && (!isset($this->page->theme->addblockposition) ||
                    $this->page->theme->addblockposition == BLOCK_ADDBLOCK_POSITION_DEFAULT)) {
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

        $controls = array();
        $actionurl = $this->page->url->out(false, array('sesskey'=> sesskey()));
        $blocktitle = $block->title;
        if (empty($blocktitle)) {
            $blocktitle = $block->arialabel;
        }

        if ($this->page->user_can_edit_blocks()) {
            // Move icon.
            $str = new lang_string('moveblock', 'block', $blocktitle);
            $controls[] = new action_menu_link_primary(
                new moodle_url($actionurl, array('bui_moveid' => $block->instance->id)),
                new pix_icon('t/move', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str,
                array('class' => 'editing_move')
            );

        }

        if ($this->page->user_can_edit_blocks() || $block->user_can_edit()) {
            // Edit config icon - always show - needed for positioning UI.
            $str = new lang_string('configureblock', 'block', $blocktitle);
            $controls[] = new action_menu_link_secondary(
                new moodle_url($actionurl, array('bui_editid' => $block->instance->id)),
                new pix_icon('t/edit', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str,
                array('class' => 'editing_edit')
            );

        }

        if ($this->page->user_can_edit_blocks() && $block->instance_can_be_hidden()) {
            // Show/hide icon.
            if ($block->instance->visible) {
                $str = new lang_string('hideblock', 'block', $blocktitle);
                $url = new moodle_url($actionurl, array('bui_hideid' => $block->instance->id));
                $icon = new pix_icon('t/hide', $str, 'moodle', array('class' => 'iconsmall', 'title' => ''));
                $attributes = array('class' => 'editing_hide');
            } else {
                $str = new lang_string('showblock', 'block', $blocktitle);
                $url = new moodle_url($actionurl, array('bui_showid' => $block->instance->id));
                $icon = new pix_icon('t/show', $str, 'moodle', array('class' => 'iconsmall', 'title' => ''));
                $attributes = array('class' => 'editing_show');
            }
            $controls[] = new action_menu_link_secondary($url, $icon, $str, $attributes);
        }

        // Assign roles.
        if (get_assignable_roles($block->context, ROLENAME_SHORT)) {
            $rolesurl = new moodle_url('/admin/roles/assign.php', array('contextid' => $block->context->id,
                'returnurl' => $this->page->url->out_as_local_url()));
            $str = new lang_string('assignrolesinblock', 'block', $blocktitle);
            $controls[] = new action_menu_link_secondary(
                $rolesurl,
                new pix_icon('i/assignroles', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str, array('class' => 'editing_assignroles')
            );
        }

        // Permissions.
        if (has_capability('moodle/role:review', $block->context) or get_overridable_roles($block->context)) {
            $rolesurl = new moodle_url('/admin/roles/permissions.php', array('contextid' => $block->context->id,
                'returnurl' => $this->page->url->out_as_local_url()));
            $str = get_string('permissions', 'role');
            $controls[] = new action_menu_link_secondary(
                $rolesurl,
                new pix_icon('i/permissions', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str, array('class' => 'editing_permissions')
            );
        }

        // Change permissions.
        if (has_any_capability(array('moodle/role:safeoverride', 'moodle/role:override', 'moodle/role:assign'), $block->context)) {
            $rolesurl = new moodle_url('/admin/roles/check.php', array('contextid' => $block->context->id,
                'returnurl' => $this->page->url->out_as_local_url()));
            $str = get_string('checkpermissions', 'role');
            $controls[] = new action_menu_link_secondary(
                $rolesurl,
                new pix_icon('i/checkpermissions', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str, array('class' => 'editing_checkroles')
            );
        }

        if ($this->user_can_delete_block($block)) {
            // Delete icon.
            $str = new lang_string('deleteblock', 'block', $blocktitle);
            $controls[] = new action_menu_link_secondary(
                new moodle_url($actionurl, array('bui_deleteid' => $block->instance->id)),
                new pix_icon('t/delete', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str,
                array('class' => 'editing_delete')
            );
        }

        return $controls;
    }

    /**
     * @param block_base $block a block that appears on this page.
     * @return boolean boolean whether the currently logged in user is allowed to delete this block.
     */
    protected function user_can_delete_block($block) {
        return $this->page->user_can_edit_blocks() && $block->user_can_edit() &&
                $block->user_can_addto($this->page) &&
                !in_array($block->instance->blockname, self::get_undeletable_block_types()) &&
                !in_array($block->instance->blockname, $this->get_required_by_theme_block_types());
    }

    /**
     * Process any block actions that were specified in the URL.
     *
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
        global $CFG, $PAGE, $OUTPUT;

        $blocktype = optional_param('bui_addblock', null, PARAM_PLUGIN);
        if ($blocktype === null) {
            return false;
        }

        require_sesskey();

        if (!$this->page->user_can_edit_blocks()) {
            throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('addblock'));
        }

        $addableblocks = $this->get_addable_blocks();

        if ($blocktype === '') {
            // Display add block selection.
            $addpage = new moodle_page();
            $addpage->set_pagelayout('admin');
            $addpage->blocks->show_only_fake_blocks(true);
            $addpage->set_course($this->page->course);
            $addpage->set_context($this->page->context);
            if ($this->page->cm) {
                $addpage->set_cm($this->page->cm);
            }

            $addpagebase = str_replace($CFG->wwwroot . '/', '/', $this->page->url->out_omit_querystring());
            $addpageparams = $this->page->url->params();
            $addpage->set_url($addpagebase, $addpageparams);
            $addpage->set_block_actions_done();
            // At this point we are going to display the block selector, overwrite global $PAGE ready for this.
            $PAGE = $addpage;
            // Some functions use $OUTPUT so we need to replace that too.
            $OUTPUT = $addpage->get_renderer('core');

            $site = get_site();
            $straddblock = get_string('addblock');

            $PAGE->navbar->add($straddblock);
            $PAGE->set_title($straddblock);
            $PAGE->set_heading($site->fullname);
            echo $OUTPUT->header();
            echo $OUTPUT->heading($straddblock);

            if (!$addableblocks) {
                echo $OUTPUT->box(get_string('noblockstoaddhere'));
                echo $OUTPUT->container($OUTPUT->action_link($addpage->url, get_string('back')), 'm-x-3 m-b-1');
            } else {
                $url = new moodle_url($addpage->url, array('sesskey' => sesskey()));
                echo $OUTPUT->render_from_template('core/add_block_body',
                    ['blocks' => array_values($addableblocks),
                     'url' => '?' . $url->get_query_string(false)]);
                echo $OUTPUT->container($OUTPUT->action_link($addpage->url, get_string('cancel')), 'm-x-3 m-b-1');
            }

            echo $OUTPUT->footer();
            // Make sure that nothing else happens after we have displayed this form.
            exit;
        }

        if (!array_key_exists($blocktype, $addableblocks)) {
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
        global $CFG, $PAGE, $OUTPUT;

        $blockid = optional_param('bui_deleteid', null, PARAM_INT);
        $confirmdelete = optional_param('bui_confirm', null, PARAM_INT);

        if (!$blockid) {
            return false;
        }

        require_sesskey();
        $block = $this->page->blocks->find_instance($blockid);
        if (!$this->user_can_delete_block($block)) {
            throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('deleteablock'));
        }

        if (!$confirmdelete) {
            $deletepage = new moodle_page();
            $deletepage->set_pagelayout('admin');
            $deletepage->blocks->show_only_fake_blocks(true);
            $deletepage->set_course($this->page->course);
            $deletepage->set_context($this->page->context);
            if ($this->page->cm) {
                $deletepage->set_cm($this->page->cm);
            }

            $deleteurlbase = str_replace($CFG->wwwroot . '/', '/', $this->page->url->out_omit_querystring());
            $deleteurlparams = $this->page->url->params();
            $deletepage->set_url($deleteurlbase, $deleteurlparams);
            $deletepage->set_block_actions_done();
            // At this point we are either going to redirect, or display the form, so
            // overwrite global $PAGE ready for this. (Formslib refers to it.)
            $PAGE = $deletepage;
            //some functions like MoodleQuickForm::addHelpButton use $OUTPUT so we need to replace that too
            $output = $deletepage->get_renderer('core');
            $OUTPUT = $output;

            $site = get_site();
            $blocktitle = $block->get_title();
            $strdeletecheck = get_string('deletecheck', 'block', $blocktitle);
            $message = get_string('deleteblockcheck', 'block', $blocktitle);

            // If the block is being shown in sub contexts display a warning.
            if ($block->instance->showinsubcontexts == 1) {
                $parentcontext = context::instance_by_id($block->instance->parentcontextid);
                $systemcontext = context_system::instance();
                $messagestring = new stdClass();
                $messagestring->location = $parentcontext->get_context_name();

                // Checking for blocks that may have visibility on the front page and pages added on that.
                if ($parentcontext->id != $systemcontext->id && is_inside_frontpage($parentcontext)) {
                    $messagestring->pagetype = get_string('showonfrontpageandsubs', 'block');
                } else {
                    $pagetypes = generate_page_type_patterns($this->page->pagetype, $parentcontext);
                    $messagestring->pagetype = $block->instance->pagetypepattern;
                    if (isset($pagetypes[$block->instance->pagetypepattern])) {
                        $messagestring->pagetype = $pagetypes[$block->instance->pagetypepattern];
                    }
                }

                $message = get_string('deleteblockwarning', 'block', $messagestring);
            }

            $PAGE->navbar->add($strdeletecheck);
            $PAGE->set_title($blocktitle . ': ' . $strdeletecheck);
            $PAGE->set_heading($site->fullname);
            echo $OUTPUT->header();
            $confirmurl = new moodle_url($deletepage->url, array('sesskey' => sesskey(), 'bui_deleteid' => $block->instance->id, 'bui_confirm' => 1));
            $cancelurl = new moodle_url($deletepage->url);
            $yesbutton = new single_button($confirmurl, get_string('yes'));
            $nobutton = new single_button($cancelurl, get_string('no'));
            echo $OUTPUT->confirm($message, $yesbutton, $nobutton);
            echo $OUTPUT->footer();
            // Make sure that nothing else happens after we have displayed this form.
            exit;
        } else {
            blocks_delete_instance($block->instance);
            // bui_deleteid and bui_confirm should not be in the PAGE url.
            $this->page->ensure_param_not_in_url('bui_deleteid');
            $this->page->ensure_param_not_in_url('bui_confirm');
            return true;
        }
    }

    /**
     * Handle showing or hiding a block.
     * @return boolean true if anything was done. False if not.
     */
    public function process_url_show_hide() {
        if ($blockid = optional_param('bui_hideid', null, PARAM_INT)) {
            $newvisibility = 0;
        } else if ($blockid = optional_param('bui_showid', null, PARAM_INT)) {
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
        global $CFG, $DB, $PAGE, $OUTPUT;

        $blockid = optional_param('bui_editid', null, PARAM_INT);
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
        $editpage->blocks->show_only_fake_blocks(true);
        $editpage->set_course($this->page->course);
        //$editpage->set_context($block->context);
        $editpage->set_context($this->page->context);
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
        //some functions like MoodleQuickForm::addHelpButton use $OUTPUT so we need to replace that to
        $output = $editpage->get_renderer('core');
        $OUTPUT = $output;

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

            // This may get overwritten by the special case handling below.
            $bi->pagetypepattern = $data->bui_pagetypepattern;
            $bi->showinsubcontexts = (bool) $data->bui_contexts;
            if (empty($data->bui_subpagepattern) || $data->bui_subpagepattern == '%@NULL@%') {
                $bi->subpagepattern = null;
            } else {
                $bi->subpagepattern = $data->bui_subpagepattern;
            }

            $systemcontext = context_system::instance();
            $frontpagecontext = context_course::instance(SITEID);
            $parentcontext = context::instance_by_id($data->bui_parentcontextid);

            // Updating stickiness and contexts.  See MDL-21375 for details.
            if (has_capability('moodle/site:manageblocks', $parentcontext)) { // Check permissions in destination

                // Explicitly set the default context
                $bi->parentcontextid = $parentcontext->id;

                if ($data->bui_editingatfrontpage) {   // The block is being edited on the front page

                    // The interface here is a special case because the pagetype pattern is
                    // totally derived from the context menu.  Here are the excpetions.   MDL-30340

                    switch ($data->bui_contexts) {
                        case BUI_CONTEXTS_ENTIRE_SITE:
                            // The user wants to show the block across the entire site
                            $bi->parentcontextid = $systemcontext->id;
                            $bi->showinsubcontexts = true;
                            $bi->pagetypepattern  = '*';
                            break;
                        case BUI_CONTEXTS_FRONTPAGE_SUBS:
                            // The user wants the block shown on the front page and all subcontexts
                            $bi->parentcontextid = $frontpagecontext->id;
                            $bi->showinsubcontexts = true;
                            $bi->pagetypepattern  = '*';
                            break;
                        case BUI_CONTEXTS_FRONTPAGE_ONLY:
                            // The user want to show the front page on the frontpage only
                            $bi->parentcontextid = $frontpagecontext->id;
                            $bi->showinsubcontexts = false;
                            $bi->pagetypepattern  = 'site-index';
                            // This is the only relevant page type anyway but we'll set it explicitly just
                            // in case the front page grows site-index-* subpages of its own later
                            break;
                    }
                }
            }

            $bits = explode('-', $bi->pagetypepattern);
            // hacks for some contexts
            if (($parentcontext->contextlevel == CONTEXT_COURSE) && ($parentcontext->instanceid != SITEID)) {
                // For course context
                // is page type pattern is mod-*, change showinsubcontext to 1
                if ($bits[0] == 'mod' || $bi->pagetypepattern == '*') {
                    $bi->showinsubcontexts = 1;
                } else {
                    $bi->showinsubcontexts = 0;
                }
            } else  if ($parentcontext->contextlevel == CONTEXT_USER) {
                // for user context
                // subpagepattern should be null
                if ($bits[0] == 'user' or $bits[0] == 'my') {
                    // we don't need subpagepattern in usercontext
                    $bi->subpagepattern = null;
                }
            }

            $bi->defaultregion = $data->bui_defaultregion;
            $bi->defaultweight = $data->bui_defaultweight;
            $bi->timemodified = time();
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
            $bits = explode('-', $this->page->pagetype);
            if ($bits[0] == 'tag' && !empty($this->page->subpage)) {
                // better navbar for tag pages
                $editpage->navbar->add(get_string('tags'), new moodle_url('/tag/'));
                $tag = core_tag_tag::get($this->page->subpage);
                // tag search page doesn't have subpageid
                if ($tag) {
                    $editpage->navbar->add($tag->get_display_name(), $tag->get_view_url());
                }
            }
            $editpage->navbar->add($block->get_title());
            $editpage->navbar->add(get_string('configuration'));
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

        $blockid = optional_param('bui_moveid', null, PARAM_INT);
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
                if (array_key_exists($weight, $usedweights)) {
                    foreach ($usedweights[$weight] as $biid) {
                        $this->reposition_block($biid, $newregion, $weight - 1);
                    }
                }
            }
            $this->reposition_block($block->instance->id, $newregion, $newweight);
        } else {
            $newweight = ceil($newweight);
            for ($weight = $bestgap - 1; $weight >= $newweight; $weight--) {
                if (array_key_exists($weight, $usedweights)) {
                    foreach ($usedweights[$weight] as $biid) {
                        $this->reposition_block($biid, $newregion, $weight + 1);
                    }
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
 * Returns a new instance of the specified block instance id.
 *
 * @param int $blockinstanceid
 * @return block_base the requested block instance.
 */
function block_instance_by_id($blockinstanceid) {
    global $DB;

    $blockinstance = $DB->get_record('block_instances', ['id' => $blockinstanceid]);
    $instance = block_instance($blockinstance->blockname, $blockinstance);
    return $instance;
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

/**
 * Give an specific pattern, return all the page type patterns that would also match it.
 *
 * @param  string $pattern the pattern, e.g. 'mod-forum-*' or 'mod-quiz-view'.
 * @return array of all the page type patterns matching.
 */
function matching_page_type_patterns_from_pattern($pattern) {
    $patterns = array($pattern);
    if ($pattern === '*') {
        return $patterns;
    }

    // Only keep the part before the star because we will append -* to all the bits.
    $star = strpos($pattern, '-*');
    if ($star !== false) {
        $pattern = substr($pattern, 0, $star);
    }

    $patterns = array_merge($patterns, matching_page_type_patterns($pattern));
    $patterns = array_unique($patterns);

    return $patterns;
}

/**
 * Given a specific page type, parent context and currect context, return all the page type patterns
 * that might be used by this block.
 *
 * @param string $pagetype for example 'course-view-weeks' or 'mod-quiz-view'.
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array an array of all the page type patterns that might match this page type.
 */
function generate_page_type_patterns($pagetype, $parentcontext = null, $currentcontext = null) {
    global $CFG; // Required for includes bellow.

    $bits = explode('-', $pagetype);

    $core = core_component::get_core_subsystems();
    $plugins = core_component::get_plugin_types();

    //progressively strip pieces off the page type looking for a match
    $componentarray = null;
    for ($i = count($bits); $i > 0; $i--) {
        $possiblecomponentarray = array_slice($bits, 0, $i);
        $possiblecomponent = implode('', $possiblecomponentarray);

        // Check to see if the component is a core component
        if (array_key_exists($possiblecomponent, $core) && !empty($core[$possiblecomponent])) {
            $libfile = $core[$possiblecomponent].'/lib.php';
            if (file_exists($libfile)) {
                require_once($libfile);
                $function = $possiblecomponent.'_page_type_list';
                if (function_exists($function)) {
                    if ($patterns = $function($pagetype, $parentcontext, $currentcontext)) {
                        break;
                    }
                }
            }
        }

        //check the plugin directory and look for a callback
        if (array_key_exists($possiblecomponent, $plugins) && !empty($plugins[$possiblecomponent])) {

            //We've found a plugin type. Look for a plugin name by getting the next section of page type
            if (count($bits) > $i) {
                $pluginname = $bits[$i];
                $directory = core_component::get_plugin_directory($possiblecomponent, $pluginname);
                if (!empty($directory)){
                    $libfile = $directory.'/lib.php';
                    if (file_exists($libfile)) {
                        require_once($libfile);
                        $function = $possiblecomponent.'_'.$pluginname.'_page_type_list';
                        if (!function_exists($function)) {
                            $function = $pluginname.'_page_type_list';
                        }
                        if (function_exists($function)) {
                            if ($patterns = $function($pagetype, $parentcontext, $currentcontext)) {
                                break;
                            }
                        }
                    }
                }
            }

            //we'll only get to here if we still don't have any patterns
            //the plugin type may have a callback
            $directory = $plugins[$possiblecomponent];
            $libfile = $directory.'/lib.php';
            if (file_exists($libfile)) {
                require_once($libfile);
                $function = $possiblecomponent.'_page_type_list';
                if (function_exists($function)) {
                    if ($patterns = $function($pagetype, $parentcontext, $currentcontext)) {
                        break;
                    }
                }
            }
        }
    }

    if (empty($patterns)) {
        $patterns = default_page_type_list($pagetype, $parentcontext, $currentcontext);
    }

    // Ensure that the * pattern is always available if editing block 'at distance', so
    // we always can 'bring back' it to the original context. MDL-30340
    if ((!isset($currentcontext) or !isset($parentcontext) or $currentcontext->id != $parentcontext->id) && !isset($patterns['*'])) {
        // TODO: We could change the string here, showing its 'bring back' meaning
        $patterns['*'] = get_string('page-x', 'pagetype');
    }

    return $patterns;
}

/**
 * Generates a default page type list when a more appropriate callback cannot be decided upon.
 *
 * @param string $pagetype
 * @param stdClass $parentcontext
 * @param stdClass $currentcontext
 * @return array
 */
function default_page_type_list($pagetype, $parentcontext = null, $currentcontext = null) {
    // Generate page type patterns based on current page type if
    // callbacks haven't been defined
    $patterns = array($pagetype => $pagetype);
    $bits = explode('-', $pagetype);
    while (count($bits) > 0) {
        $pattern = implode('-', $bits) . '-*';
        $pagetypestringname = 'page-'.str_replace('*', 'x', $pattern);
        // guessing page type description
        if (get_string_manager()->string_exists($pagetypestringname, 'pagetype')) {
            $patterns[$pattern] = get_string($pagetypestringname, 'pagetype');
        } else {
            $patterns[$pattern] = $pattern;
        }
        array_pop($bits);
    }
    $patterns['*'] = get_string('page-x', 'pagetype');
    return $patterns;
}

/**
 * Generates the page type list for the my moodle page
 *
 * @param string $pagetype
 * @param stdClass $parentcontext
 * @param stdClass $currentcontext
 * @return array
 */
function my_page_type_list($pagetype, $parentcontext = null, $currentcontext = null) {
    return array('my-index' => get_string('page-my-index', 'pagetype'));
}

/**
 * Generates the page type list for a module by either locating and using the modules callback
 * or by generating a default list.
 *
 * @param string $pagetype
 * @param stdClass $parentcontext
 * @param stdClass $currentcontext
 * @return array
 */
function mod_page_type_list($pagetype, $parentcontext = null, $currentcontext = null) {
    $patterns = plugin_page_type_list($pagetype, $parentcontext, $currentcontext);
    if (empty($patterns)) {
        // if modules don't have callbacks
        // generate two default page type patterns for modules only
        $bits = explode('-', $pagetype);
        $patterns = array($pagetype => $pagetype);
        if ($bits[2] == 'view') {
            $patterns['mod-*-view'] = get_string('page-mod-x-view', 'pagetype');
        } else if ($bits[2] == 'index') {
            $patterns['mod-*-index'] = get_string('page-mod-x-index', 'pagetype');
        }
    }
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
    $bc->attributes['data-block'] = 'adminblock';

    $missingblocks = $page->blocks->get_addable_blocks();
    if (empty($missingblocks)) {
        $bc->content = get_string('noblockstoaddhere');
        return $bc;
    }

    $menu = array();
    foreach ($missingblocks as $block) {
        $menu[$block->name] = $block->title;
    }

    $actionurl = new moodle_url($page->url, array('sesskey'=>sesskey()));
    $select = new single_select($actionurl, 'bui_addblock', $menu, null, array(''=>get_string('adddots')), 'add_block');
    $select->set_label(get_string('addblock'), array('class'=>'accesshide'));
    $bc->content = $OUTPUT->render($select);
    return $bc;
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
    if (!$bi = block_instance($name)) {
        return false;
    }

    $formats = $bi->applicable_formats();
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

    // Allow plugins to use this block before we completely delete it.
    if ($pluginsfunction = get_plugins_with_function('pre_block_delete')) {
        foreach ($pluginsfunction as $plugintype => $plugins) {
            foreach ($plugins as $pluginfunction) {
                $pluginfunction($instance);
            }
        }
    }

    if ($block = block_instance($instance->blockname, $instance)) {
        $block->instance_delete();
    }
    context_helper::delete_instance(CONTEXT_BLOCK, $instance->id);

    if (!$skipblockstables) {
        $DB->delete_records('block_positions', array('blockinstanceid' => $instance->id));
        $DB->delete_records('block_instances', array('id' => $instance->id));
        $DB->delete_records_list('user_preferences', 'name', array('block'.$instance->id.'hidden','docked_block_instance_'.$instance->id));
    }
}

/**
 * Delete multiple blocks at once.
 *
 * @param array $instanceids A list of block instance ID.
 */
function blocks_delete_instances($instanceids) {
    global $DB;

    $limit = 1000;
    $count = count($instanceids);
    $chunks = [$instanceids];
    if ($count > $limit) {
        $chunks = array_chunk($instanceids, $limit);
    }

    // Perform deletion for each chunk.
    foreach ($chunks as $chunk) {
        $instances = $DB->get_recordset_list('block_instances', 'id', $chunk);
        foreach ($instances as $instance) {
            blocks_delete_instance($instance, false, true);
        }
        $instances->close();

        $DB->delete_records_list('block_positions', 'blockinstanceid', $chunk);
        $DB->delete_records_list('block_instances', 'id', $chunk);

        $preferences = array();
        foreach ($chunk as $instanceid) {
            $preferences[] = 'block' . $instanceid . 'hidden';
            $preferences[] = 'docked_block_instance_' . $instanceid;
        }
        $DB->delete_records_list('user_preferences', 'name', $preferences);
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
  * @param string $blocksstr Determines the starting point that the blocks are added in the region.
  * @return array the parsed list of default blocks
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
        $rightbits = trim(array_shift($bits));
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

    if (isset($CFG->defaultblocks_site)) {
        return blocks_parse_default_blocks_list($CFG->defaultblocks_site);
    } else {
        return array(
            BLOCK_POS_LEFT => array(),
            BLOCK_POS_RIGHT => array()
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

    if (isset($CFG->defaultblocks_override)) {
        $blocknames = blocks_parse_default_blocks_list($CFG->defaultblocks_override);

    } else if ($course->id == SITEID) {
        $blocknames = blocks_get_default_site_course_blocks();

    } else if (isset($CFG->{'defaultblocks_' . $course->format})) {
        $blocknames = blocks_parse_default_blocks_list($CFG->{'defaultblocks_' . $course->format});

    } else {
        require_once($CFG->dirroot. '/course/lib.php');
        $blocknames = course_get_format($course)->get_default_blocks();

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
    $page->set_context(context_system::instance());
    // We don't add blocks required by the theme, they will be auto-created.
    $page->blocks->add_blocks(array(BLOCK_POS_LEFT => array('admin_bookmarks')), 'admin-*', null, null, 2);

    if ($defaultmypage = $DB->get_record('my_pages', array('userid' => null, 'name' => '__default', 'private' => 1))) {
        $subpagepattern = $defaultmypage->id;
    } else {
        $subpagepattern = null;
    }

    $newblocks = array('private_files', 'online_users', 'badges', 'calendar_month', 'calendar_upcoming');
    $newcontent = array('lp', 'myoverview');
    $page->blocks->add_blocks(array(BLOCK_POS_RIGHT => $newblocks, 'content' => $newcontent), 'my-index', $subpagepattern);
}
