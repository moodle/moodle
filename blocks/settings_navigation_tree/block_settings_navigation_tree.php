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
    protected $contentgenerated = false;
    public $id = null;

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
        $this->page->requires->js('lib/javascript-navigation.js');
        block_settings_navigation_tree::$navcount++;

        $tooglesidetabdisplay = get_string('tooglesidetabdisplay', $this->blockname);
        $toogleblockdisplay = get_string('toogleblockdisplay', $this->blockname);
        $args = array('instance'=>$this->instance->id);
        $args['tooglesidetabdisplay'] = $tooglesidetabdisplay;
        $args['toogleblockdisplay'] = $toogleblockdisplay;
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
                $this->content->footer =
                        '<div class="adminsearchform">'.
                        '<form action="'.$CFG->wwwroot.'/'.$CFG->admin.'/search.php" method="get"><div>'.
                        '<label for="query" class="accesshide">'.get_string('searchinsettings', 'admin').'</label>'.
                        '<input type="text" name="query" id="query" size="8" value="'.s(optional_param('query', '')).'" />'.
                        '<input type="submit" value="'.get_string('search').'" /></div>'.
                        '</form></div>';
            } else {
                $this->content->footer = '';
            }

            $url = $this->page->url;
            $url->param('regenerate','navigation');
            $reloadstr = get_string('reload');
            $this->content->footer .= '<a href="'.$url->out().'" class="customcommand"><img src="'.$OUTPUT->old_icon_url('t/reload').'" alt="'.$reloadstr.'" title="'.$reloadstr.'" /></a>';
            if (!empty($this->config->enablesidebarpopout) && $this->config->enablesidebarpopout == 'yes') {
                user_preference_allow_ajax_update('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount, PARAM_INT);
                if (get_user_preferences('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount, 0)) {
                    $icon = $OUTPUT->old_icon_url('t/movetoblock');
                    $string = $toogleblockdisplay;
                } else {
                    $icon = $OUTPUT->old_icon_url('t/movetosidetab');
                    $string = $tooglesidetabdisplay;
                }
                $this->content->footer .= '<a class="moveto customcommand requiresjs"><img src="'.$icon.'" alt="'.$string.'" title="'.$string.'"></a>';
            }
        }

        $this->contentgenerated = true;
        return true;
    }

    function html_attributes() {
        $attributes = parent::html_attributes();
        if (!empty($this->config->enablehoverexpansion) && $this->config->enablehoverexpansion == 'yes') {
            $attributes['class'] .= ' sideblock_js_expansion';
        }
        if (get_user_preferences('nav_in_tab_panel_settingsnav'.block_settings_navigation_tree::$navcount, 0)) {
            $attributes['class'] .= ' sideblock_js_sidebarpopout';
        }
        return $attributes;
    }
}