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

namespace mod_forum\h5p;

/**
 * Class to check if the H5P content can be edited for this plugin.
 *
 * @package   mod_forum
 * @copyright 2021 Sara Arjona (sara@moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class canedit {

    /**
     * Check if the user can edit an H5P file. In that case, this method will return true if the file belongs to mod_forum
     * filearea is post and the user can edit the post where the H5P is.
     *
     * @param \stored_file $file The H5P file to check.
     *
     * @return boolean Whether the user can edit or not the given file.
     * @since Moodle 4.0
     */
    public static function can_edit_content(\stored_file $file): bool {
        global $USER;

        list($type, $component) = \core_component::normalize_component($file->get_component());

        if ($type === 'mod' && $component === 'forum') {
            // For mod_forum files in posts, check if the user can edit the post where the H5P is.
            if ($file->get_filearea() === 'post') {
                // Check if the user can edit the forum post.
                $vaultfactory = \mod_forum\local\container::get_vault_factory();
                $forumvault = $vaultfactory->get_forum_vault();
                $discussionvault = $vaultfactory->get_discussion_vault();
                $postvault = $vaultfactory->get_post_vault();
                $postid = $file->get_itemid();
                $postentity = $postvault->get_from_id($postid);
                if (!empty($postentity)) {
                    $discussionentity = $discussionvault->get_from_id($postentity->get_discussion_id());
                    $managerfactory = \mod_forum\local\container::get_manager_factory();
                    $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
                    $capabilitymanager = $managerfactory->get_capability_manager($forumentity);
                    if ($capabilitymanager->can_edit_post($USER, $discussionentity, $postentity)) {
                        return true;
                    }
                }
            } else {
                // For any other fileare, check whether the user can add/edit them.
                $context = \context::instance_by_id($file->get_contextid());
                $plugins = \core_component::get_plugin_list($type);
                $isvalid = array_key_exists($component, $plugins);
                if ($isvalid && has_capability("$type/$component:addinstance", $context)) {
                    // The user can edit the content because she has the capability for creating instances where the file belongs.
                    return true;
                }
            }
        }

        return false;
    }
}
