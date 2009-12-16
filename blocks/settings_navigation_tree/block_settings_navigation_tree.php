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
 * The settings navigation tree block class
 *
 * Used to produce the settings navigation block new to Moodle 2.0
 *
 * @package blocks
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_settings_navigation_tree extends block_tree {

    /** @var string */
    public static $navcount;
    public $blockname = null;
    public $id = null;
    /** @var bool */
    protected $contentgenerated = false;
    /** @var bool|null */
    protected $docked = null;

    /**
     * Set the initial properties for the block
     */
    function init() {
        $this->blockname = get_class($this);
        $this->title = get_string('blockname', $this->blockname);
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

    /**
     * Gets the content for this block by grabbing it from $this->page
     */
    function get_content() {
        global $CFG, $OUTPUT;
        // First check if we have already generated, don't waste cycles
        if ($this->contentgenerated === true) {
            return true;
        }
        $this->page->requires->yui2_lib('dom');
        $this->page->requires->js('lib/javascript-navigation.js');
        block_settings_navigation_tree::$navcount++;

        // Check if this block has been docked
        if ($this->docked === null) {
            $this->docked = get_user_preferences('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount, 0);
        }

        // Check if there is a param to change the docked state
        if ($this->docked && optional_param('undock', null, PARAM_INT)==$this->instance->id) {
            unset_user_preference('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount, 0);
            $url = $this->page->url;
            $url->remove_params(array('undock'));
            redirect($url);
        } else if (!$this->docked && optional_param('dock', null, PARAM_INT)==$this->instance->id) {
            set_user_preferences(array('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount=>1));
            $url = $this->page->url;
            $url->remove_params(array('dock'));
            redirect($url);
        }

        $togglesidetabdisplay = get_string('togglesidetabdisplay', $this->blockname);
        $toggleblockdisplay = get_string('toggleblockdisplay', $this->blockname);
        $args = array('instance'=>$this->instance->id);
        $args['togglesidetabdisplay'] = $togglesidetabdisplay;
        $args['toggleblockdisplay'] = $toggleblockdisplay;
        // Give JS some information we will use within the JS tree object
        $this->page->requires->data_for_js('settingsnav'.block_settings_navigation_tree::$navcount, $args);


        $this->id = 'settingsnav'.block_settings_navigation_tree::$navcount;
        $this->page->requires->js_function_call('setup_new_navtree', array($this->id))->on_dom_ready();
        // Grab the children from settings nav, we have more than one root node
        // and we dont want to show the site node
        $this->content->items = $this->page->settingsnav->children;
        // only do search if you have moodle/site:config
        if (count($this->content->items)>0) {
            if (has_capability('moodle/site:config',get_context_instance(CONTEXT_SYSTEM)) ) {
                $searchform = new html_form();
                $searchform->url = new moodle_url("$CFG->wwwroot/$CFG->admin/search.php");
                $searchform->method = 'get';
                $searchform->button->text = get_string('search');
                $searchfield = html_field::make_text('query', optional_param('query', '', PARAM_RAW), '', 50);
                $searchfield->id = 'query';
                $searchfield->style .= 'width: 7em;';
                $searchfield->set_label(get_string('searchinsettings', 'admin'), 'query');
                $searchfield->label->add_class('accesshide');
                $this->content->footer = $OUTPUT->container($OUTPUT->form($searchform, $OUTPUT->field($searchfield)), 'adminsearchform');
            } else {
                $this->content->footer = '';
            }

            $reloadicon = new moodle_action_icon();
            $url = $this->page->url;
            $url->param('regenerate','navigation');
            $reloadicon->link->url = $url;
            $reloadicon->link->add_class('customcommand');
            $reloadicon->image->src = $OUTPUT->old_icon_url('t/reload');
            $reloadicon->alt = get_string('reload');
            $reloadicon->title = get_string('reload');

            $this->content->footer .= $OUTPUT->action_icon($reloadicon);

            if (!empty($this->config->enablesidebarpopout) && $this->config->enablesidebarpopout == 'yes') {
                user_preference_allow_ajax_update('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount, PARAM_INT);

                $moveicon = new moodle_action_icon();
                $moveicon->link->add_classes('moveto customcommand requiresjs');
                $moveicon->link->url = $this->page->url;
                if ($this->docked) {
                    $moveicon->image->src = $OUTPUT->old_icon_url('t/movetoblock');
                    $moveicon->image->alt = $toggleblockdisplay;
                    $moveicon->image->title = $toggleblockdisplay;
                    $moveicon->link->url->param('undock', $this->instance->id);
                } else {
                    $moveicon->image->src = $OUTPUT->old_icon_url('t/movetosidetab');
                    $moveicon->image->alt = $togglesidetabdisplay;
                    $moveicon->image->title = $togglesidetabdisplay;
                    $moveicon->link->url->param('dock', $this->instance->id);
                }
                $this->content->footer .= $OUTPUT->action_icon($moveicon);
            }
        }

        $this->contentgenerated = true;
        return true;
    }

    function html_attributes() {
        $attributes = parent::html_attributes();

        // Check if this block has been docked
        if ($this->docked === null) {
            $this->docked = get_user_preferences('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount, 0);
        }

        if (!empty($this->config->enablehoverexpansion) && $this->config->enablehoverexpansion == 'yes') {
            $attributes['class'] .= ' sideblock_js_expansion';
        }
        if ($this->docked) {
            $attributes['class'] .= ' sideblock_js_sidebarpopout';
        }
        return $attributes;
    }
}
