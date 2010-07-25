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
 * Defines the base class form used by blocks/edit.php to edit block instance configuration.
 *
 * It works with the {@link block_edit_form} class, or rather the particular
 * subclass defined by this block, to do the editing.
 *
 * @package    core
 * @subpackage block
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

/**
 * The base class form used by blocks/edit.php to edit block instance configuration.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_edit_form extends moodleform {
    /**
     * The block instance we are editing.
     * @var block_base
     */
    public $block;
    /**
     * The page we are editing this block in association with.
     * @var moodle_page
     */
    public $page;

    function __construct($actionurl, $block, $page) {
        global $CFG;
        $this->block = $block;
        $this->page = $page;
        parent::moodleform($actionurl);
    }

    function definition() {
        $mform =& $this->_form;

        // First show fields specific to this type of block.
        $this->specific_definition($mform);

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'whereheader', get_string('wherethisblockappears', 'block'));

        // If the current weight of the block is out-of-range, add that option in.
        $blockweight = $this->block->instance->weight;
        $weightoptions = array();
        if ($blockweight < -block_manager::MAX_WEIGHT) {
            $weightoptions[$blockweight] = $blockweight;
        }
        for ($i = -block_manager::MAX_WEIGHT; $i <= block_manager::MAX_WEIGHT; $i++) {
            $weightoptions[$i] = $i;
        }
        if ($blockweight > block_manager::MAX_WEIGHT) {
            $weightoptions[$blockweight] = $blockweight;
        }
        $first = reset($weightoptions);
        $weightoptions[$first] = get_string('bracketfirst', 'block', $first);
        $last = end($weightoptions);
        $weightoptions[$last] = get_string('bracketlast', 'block', $last);

        $regionoptions = $this->page->theme->get_all_block_regions();

        $parentcontext = get_context_instance_by_id($this->block->instance->parentcontextid);
        $mform->addElement('hidden', 'bui_parentcontextid', $parentcontext->id);

        $contextoptions = array();
        if ( ($parentcontext->contextlevel == CONTEXT_COURSE && $parentcontext->instanceid == SITEID) ||
             ($parentcontext->contextlevel == CONTEXT_SYSTEM)) {        // Home page
            $contextoptions[0] = get_string('showonfrontpageonly', 'block');
            $contextoptions[1] = get_string('showonfrontpageandsubs', 'block');
            $contextoptions[2] = get_string('showonentiresite', 'block');
        } else {
            $parentcontextname = print_context_name($parentcontext);
            $contextoptions[0] = get_string('showoncontextonly', 'block', $parentcontextname);
            $contextoptions[1] = get_string('showoncontextandsubs', 'block', $parentcontextname);
        }
        $mform->addElement('select', 'bui_contexts', get_string('contexts', 'block'), $contextoptions);

        if ($this->page->pagetype == 'site-index') {   // No need for pagetype list on home page
            $pagetypelist = array('*');
        } else {
            $pagetypelist = matching_page_type_patterns($this->page->pagetype);
        }
        $pagetypeoptions = array();
        foreach ($pagetypelist as $pagetype) {         // Find human-readable names for the pagetypes
            $pagetypeoptions[$pagetype] = $pagetype;
            $pagetypestringname = 'page-'.str_replace('*', 'x',$pagetype);  // Better names MDL-21375
            if (get_string_manager()->string_exists($pagetypestringname, 'pagetype')) {
                $pagetypeoptions[$pagetype] .= ' (' . get_string($pagetypestringname, 'pagetype') . ')';
            }
        }
        $mform->addElement('select', 'bui_pagetypepattern', get_string('restrictpagetypes', 'block'), $pagetypeoptions);

        if ($this->page->subpage) {
            $subpageoptions = array(
                '%@NULL@%' => get_string('anypagematchingtheabove', 'block'),
                $this->page->subpage => get_string('thisspecificpage', 'block', $this->page->subpage),
            );
            $mform->addElement('select', 'bui_subpagepattern', get_string('subpages', 'block'), $subpageoptions);
        }

        $defaultregionoptions = $regionoptions;
        $defaultregion = $this->block->instance->defaultregion;
        if (!array_key_exists($defaultregion, $defaultregionoptions)) {
            $defaultregionoptions[$defaultregion] = $defaultregion;
        }
        $mform->addElement('select', 'bui_defaultregion', get_string('defaultregion', 'block'), $defaultregionoptions);

        $mform->addElement('select', 'bui_defaultweight', get_string('defaultweight', 'block'), $weightoptions);

        // Where this block is positioned on this page.
        $mform->addElement('header', 'whereheader', get_string('onthispage', 'block'));

        $mform->addElement('selectyesno', 'bui_visible', get_string('visible', 'block'));

        $blockregion = $this->block->instance->region;
        if (!array_key_exists($blockregion, $regionoptions)) {
            $regionoptions[$blockregion] = $blockregion;
        }
        $mform->addElement('select', 'bui_region', get_string('region', 'block'), $regionoptions);

        $mform->addElement('select', 'bui_weight', get_string('weight', 'block'), $weightoptions);

        $pagefields = array('bui_visible', 'bui_region', 'bui_weight');
        if (!$this->block->user_can_edit()) {
            $mform->hardFreezeAllVisibleExcept($pagefields);
        }
        if (!$this->page->user_can_edit_blocks()) {
            $mform->hardFreeze($pagefields);
        }

        $this->add_action_buttons();
    }

    function set_data($defaults) {
        // Prefix bui_ on all the core field names.
        $blockfields = array('showinsubcontexts', 'pagetypepattern', 'subpagepattern', 'parentcontextid',
                'defaultregion', 'defaultweight', 'visible', 'region', 'weight');
        foreach ($blockfields as $field) {
            $newname = 'bui_' . $field;
            $defaults->$newname = $defaults->$field;
        }

        // Copy block config into config_ fields.
        if (!empty($this->block->config)) {
            foreach ($this->block->config as $field => $value) {
                $configfield = 'config_' . $field;
                $defaults->$configfield = $value;
            }
        }

        // Munge ->subpagepattern becuase HTML selects don't play nicely with NULLs.
        if (empty($defaults->bui_subpagepattern)) {
            $defaults->bui_subpagepattern = '%@NULL@%';
        }

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        if ($defaults->parentcontextid == $systemcontext->id) {
            $defaults->bui_contexts = 2; // System-wide and sticky
        } else {
            $defaults->bui_contexts = $defaults->bui_showinsubcontexts;
        }

        parent::set_data($defaults);
    }

    /**
     * Override this to create any form fields specific to this type of block.
     * @param object $mform the form being built.
     */
    protected function specific_definition($mform) {
        // By default, do nothing.
    }
}
