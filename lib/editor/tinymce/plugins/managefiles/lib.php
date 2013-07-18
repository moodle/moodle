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

defined('MOODLE_INTERNAL') || die();

/**
 * Plugin for managing files embedded in the text editor
 *
 * @package   tinymce_managefiles
 * @copyright 2013 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tinymce_managefiles extends editor_tinymce_plugin {
    /** @var array list of buttons defined by this plugin */
    protected $buttons = array('managefiles');

    /**
     * Adjusts TinyMCE init parameters for tinymce_managefiles
     *
     * Adds file area restrictions parameters and actual 'managefiles' button
     *
     * @param array $params TinyMCE init parameters array
     * @param context $context Context where editor is being shown
     * @param array $options Options for this editor
     */
    protected function update_init_params(array &$params, context $context,
            array $options = null) {
        global $USER;

        // Add parameters for filemanager
        $params['managefiles'] = array('usercontext' => context_user::instance($USER->id)->id);
        foreach (array('itemid', 'context', 'areamaxbytes', 'maxbytes', 'subdirs', 'return_types') as $key) {
            if (isset($options[$key])) {
                if ($key === 'context' && is_object($options[$key])) {
                    // Just context id is enough
                    $params['managefiles'][$key] = $options[$key]->id;
                } else {
                    $params['managefiles'][$key] = $options[$key];
                }
            }
        }

        // Add button after moodlemedia button in advancedbuttons3.
        $added = $this->add_button_after($params, 3, 'managefiles', 'moodlemedia', false);

        // So, if no moodlemedia, add after 'image'.
        if (!$added) {
            $this->add_button_after($params, 3, 'managefiles', 'image');
        }

        // Add JS file, which uses default name.
        $this->add_js_plugin($params);
    }

    protected function get_sort_order() {
        return 310;
    }
}
