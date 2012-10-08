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
 * Plugin for Moodle emoticons.
 *
 * @package   tinymce_moodleemoticon
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tinymce_moodleemoticon extends editor_tinymce_plugin {
    /** @var array list of buttons defined by this plugin */
    protected $buttons = array('moodleemoticon');

    protected function update_init_params(array &$params, context $context,
            array $options = null) {
        global $OUTPUT;

        if ($this->get_config('requireemoticon', 1)) {
            // If emoticon filter is disabled, do not add button.
            $filters = filter_get_active_in_context($context);
            if (!array_key_exists('filter/emoticon', $filters)) {
                return;
            }
        }

        // Add button after 'image' in advancedbuttons3.
        $this->add_button_after($params, 3, 'moodleemoticon', 'image');

        // Add JS file, which uses default name.
        $this->add_js_plugin($params);

        // Extra params specifically for emoticon plugin.
        $manager = get_emoticon_manager();
        $emoticons = $manager->get_emoticons();
        $imgs = array();
        // See the TinyMCE plugin moodleemoticon for how the emoticon index is (ab)used.
        $index = 0;
        foreach ($emoticons as $emoticon) {
            $imgs[$emoticon->text] = $OUTPUT->render($manager->prepare_renderable_emoticon(
                    $emoticon, array('class' => 'emoticon emoticon-index-'.$index++)));
        }
        $params['moodleemoticon_emoticons'] = json_encode($imgs);
    }
}
