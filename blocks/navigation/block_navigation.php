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
 * This file contains classes used to manage the navigation structures in Moodle
 * and was introduced as part of the changes occuring in Moodle 2.0
 *
 * @since 2.0
 * @package blocks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The global navigation tree block class
 *
 * Used to produce the global navigation block new to Moodle 2.0
 *
 * @package blocks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_navigation extends block_base {

    /** @var int */
    public static $navcount;
    /** @var string */
    public $blockname = null;
    /** @var bool */
    protected $contentgenerated = false;
    /** @var bool|null */
    protected $docked = null;

    /**
     * Set the initial properties for the block
     */
    function init() {
        global $CFG;
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', $this->blockname);
        $this->version = 2009082800;
    }

    /**
     * All multiple instances of this block
     * @return bool Returns true
     */
    function instance_allow_multiple() {
        return false;
    }

    /**
     * Set the applicable formats for this block to all
     * @return array
     */
    function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Allow the user to configure a block instance
     * @return bool Returns true
     */
    function instance_allow_config() {
        return true;
    }

    function instance_can_be_docked() {
        return (parent::instance_can_be_docked() && (empty($this->config->enabledock) || $this->config->enabledock=='yes'));
    }

    function get_required_javascript() {
        global $CFG;
        $this->page->requires->js_module(array('name'=>'core_dock', 'fullpath'=>'/blocks/dock.js', 'requires'=>array('base', 'cookie', 'dom', 'io', 'node', 'event-custom', 'event-mouseenter', 'yui2-container')));
        $this->page->requires->js_module(array('name'=>'block_navigation', 'fullpath'=>'/blocks/navigation/navigation.js', 'requires'=>array('core_dock', 'io', 'node', 'dom', 'event-custom', 'json-parse')));
        user_preference_allow_ajax_update('docked_block_instance_'.$this->instance->id, PARAM_INT);
    }

    /**
     * Gets the content for this block by grabbing it from $this->page
     */
    function get_content() {
        global $CFG, $OUTPUT;
        // First check if we have already generated, don't waste cycles
        if ($this->contentgenerated === true) {
            return $this->content;
        }
        $this->page->requires->yui2_lib('dom');
        // JS for navigation moved to the standard theme, the code will probably have to depend on the actual page structure
        // $this->page->requires->js('/lib/javascript-navigation.js');
        // Navcount is used to allow us to have multiple trees although I dont' know why
        // you would want to trees the same

        block_navigation::$navcount++;

        // Check if this block has been docked
        if ($this->docked === null) {
            $this->docked = get_user_preferences('nav_in_tab_panel_globalnav'.block_navigation::$navcount, 0);
        }

        // Check if there is a param to change the docked state
        if ($this->docked && optional_param('undock', null, PARAM_INT)==$this->instance->id) {
            unset_user_preference('nav_in_tab_panel_globalnav'.block_navigation::$navcount);
            $url = $this->page->url;
            $url->remove_params(array('undock'));
            redirect($url);
        } else if (!$this->docked && optional_param('dock', null, PARAM_INT)==$this->instance->id) {
            set_user_preferences(array('nav_in_tab_panel_globalnav'.block_navigation::$navcount=>1));
            $url = $this->page->url;
            $url->remove_params(array('dock'));
            redirect($url);
        }

        // Initialise (only actually happens if it hasn't already been done yet
        $this->page->navigation->initialise();

        if (!empty($this->config->showmyhistory) && $this->config->showmyhistory=='yes') {
            $this->showmyhistory();
        }

        // Get the expandable items so we can pass them to JS
        $expandable = array();
        $this->page->navigation->find_expandable($expandable);

        // Initialise the JS tree object
        $module = array('name'=>'block_navigation', 'fullpath'=>'/blocks/navigation/navigation.js', 'requires'=>array('core_dock', 'io', 'node', 'dom', 'event-custom', 'json-parse'));
        $arguments = array($this->instance->id, array('expansions'=>$expandable, 'instance'=>$this->instance->id, 'candock'=>$this->instance_can_be_docked()));
        $this->page->requires->js_init_call('M.block_navigation.init_add_tree', $arguments, false, $module);

        // Grab the items to display
        $renderer = $this->page->get_renderer('block_navigation');
        $this->content->text = $renderer->navigation_tree($this->page->navigation);

        $reloadlink = new moodle_url($this->page->url, array('regenerate'=>'navigation'));

        $this->content->footer .= $OUTPUT->action_icon($reloadlink, new pix_icon('t/reload', get_string('reload')), null, array('class'=>'customcommand reloadnavigation'));

        // Set content generated to true so that we know it has been done
        $this->contentgenerated = true;

        return $this->content;
    }

    /**
     * Returns the attributes to set for this block
     *
     * This function returns an array of HTML attributes for this block including
     * the defaults
     * {@link block_tree->html_attributes()} is used to get the default arguments
     * and then we check whether the user has enabled hover expansion and add the
     * appropriate hover class if it has
     *
     * @return array An array of HTML attributes
     */
    public function html_attributes() {
        $attributes = parent::html_attributes();
        if (!empty($this->config->enablehoverexpansion) && $this->config->enablehoverexpansion == 'yes') {
            $attributes['class'] .= ' block_js_expansion';
        }
        return $attributes;
    }

    /**
     * This function maintains a history of the active pages that a user has visited
     * and displays it back to the user as part of the navigation structure
     *
     * @return bool
     */
    protected function showmyhistory() {
        global $USER, $PAGE;

        // Create a navigation cache so that we can store the history
        $cache = new navigation_cache('navigationhistory', 60*60);

        // If the user isn't logged in or is a guest we don't want to display anything
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        // Check the cache to see if we have loaded my courses already
        // there is a very good chance that we have
        if (!$cache->cached('history')) {
            $cache->history = array();
        }
        $history = $cache->history;
        $historycount = count($history);

        // Find the initial active node
        $child = false;
        if ($PAGE->navigation->contains_active_node()) {
            $child = $PAGE->navigation->find_active_node();
        } else if ($PAGE->settingsnav->contains_active_node()) {
            $child = $PAGE->settingsnav->find_active_node();
        }
        // Check that we found an active child node
        if ($child!==false) {
            $properties = array();
            // Check whether this child contains another active child node
            // this can happen if we are looking at a module
            if ($child->contains_active_node()) {
                $titlebits = array();
                // Loop while the child contains active nodes and in each iteration
                // find the next node in the correct direction
                while ($child!==null && $child->contains_active_node()) {
                    if (!empty($child->shorttext)) {
                        $titlebits[] = $child->shorttext;
                    } else {
                        $titlebits[] = $child->text;
                    }
                    foreach ($child->children as $child) {
                        if ($child->contains_active_node() || $child->isactive) {
                            // We have found the active child or one of its parents
                            // so break the foreach so we can proceed in the while
                            break;
                        }
                    }
                }
                if (!empty($child->shorttext)) {
                    $titlebits[] = $child->shorttext;
                } else {
                    $titlebits[] = $child->text;
                }
                $properties['text'] = join(' - ', $titlebits);
                $properties['shorttext'] = join(' - ', $titlebits);
            } else {
                $properties['text'] = $child->text;
                $properties['shorttext'] = $child->shorttext;
            }
            $properties['action'] = $child->action;
            $properties['key'] = $child->key;
            $properties['type'] = $child->type;
            $properties['icon'] = $child->icon;

            // Create a new navigation node object free of the main structure
            // so that it is easily storeable and customised
            $child = new navigation_node($properties);

            // Check that this page isn't already in the history array. If it is
            // we will remove it so that it gets added at the top and we dont get
            // duplicate entries
            foreach ($history as $key=>$node) {
                if ($node->key == $child->key && $node->type == $child->type) {
                    if ($node->action instanceof moodle_url && $child->action instanceof moodle_url && $node->action->compare($child->action)) {
                        unset($history[$key]);
                    } else if ($child->action instanceof moodle_url && $child->action->out_omit_querystring() == $node->action) {
                        unset($history[$key]);
                    } else if ($child->action == $node->action) {
                        unset($history[$key]);
                    }
                }
            }
            // If there is more than 5 elements in the array remove the first one
            // We want a fifo array
            if (count($history) > 5) {
                array_shift($history);
            }
            $child->nodetype = navigation_node::NODETYPE_LEAF;
            $child->children = array();
            // Add the child to the history array
            array_push($history,$child);
        }

        // If we have `more than nothing` in the history display it :D
        if ($historycount > 0) {
            // Add a branch to hold the users history
            $mymoodle = $PAGE->navigation->get('profile', navigation_node::TYPE_USER);
            $myhistorybranch = $mymoodle->add(get_string('showmyhistorytitle', $this->blockname), null, navigation_node::TYPE_CUSTOM, null, 'myhistory');
            foreach (array_reverse($history) as $node) {
                $myhistorybranch->children->add($node);
            }
        }

        // Cache the history (or update the cached history as it is)
        $cache->history = $history;

        return true;
    }
}
