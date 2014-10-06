<?php
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
 * Kaltura media library file.
 *
 * @package    tinymce_kalturamedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/local/kaltura/locallib.php');

class tinymce_kalturamedia extends editor_tinymce_plugin {
    /** @var array list of buttons defined by this plugin */
    protected $buttons = array('kalturamedia');

    /**
     * Adjusts TinyMCE init parameters for this plugin.
     *
     * Subclasses must implement this function in order to carry out changes
     * to the TinyMCE settings.
     *
     * @param array $params TinyMCE init parameters array
     * @param context $context Context where editor is being shown
     * @param array $options Options for this editor
     */
    protected function update_init_params(array &$params, context $context, array $options = null) {
        $params['lti_launch_context_id'] = $context->id;

        // Add button after 'unlink' in Moodlemedia
        if ($row = $this->find_button($params, 'moodleemoticon')) {
            $this->add_button_after($params, $row, 'kalturamedia', 'moodlemedia', true);
        } else {
            $this->add_button_after($params, 1, 'kalturamedia');
        }

        // Add JS file, which uses default name.
        $this->add_js_plugin($params);
    }

    /**
     * Gets the order in which to run this plugin. Order usually only matters if
     * (a) the place you add your button might depend on another plugin, or
     * (b) you want to make some changes to layout etc. that should happen last.
     * The default order is 100; within that, plugins are sorted alphabetically.
     * Return a lower number if you want this plugin to run earlier, or a higher
     * number if you want it to run later.
     */
    protected function get_sort_order() {
        return 111;
    }
}