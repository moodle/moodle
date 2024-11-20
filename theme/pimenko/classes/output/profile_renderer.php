<?php
// This file is part of the pimenko theme for Moodle
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
 * Theme pimenko profile renderer file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2019
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_pimenko\output;

use moodle_url;
use stdClass;
use user_picture;
use html_writer;
use preferences_group;
use preferences_groups;
use core_user;
use core_user\output\myprofile\manager;

class profile_renderer extends \renderer_base {

    private $carddetails;
    private $profileblocks;
    private $user;

    public function userprofile($userid) {
        global $DB, $USER;

        $preferences = optional_param(
                'preferences',
                0,
                PARAM_INT
        );

        if ($preferences) {
            $this->page->set_url('/user/preferences.php', ['id' => $userid]);
        }

        if ($user = $DB->get_record(
                'user',
                ['id' => $userid]
        )) {

            $this->user = $user;
            $this->user->msgurl = new moodle_url(
                    '/message/index.php',
                    ['id' => $this->user->id]
            );
            $currentuser = ($user->id == $USER->id);
            $isadmin = is_siteadmin($USER);

            $this->user->seegrades = false;
            if ($currentuser) {
                $this->user->seegrades = true;
            } else {
                $this->user->cansend = true;
            }

            $this->user->seeemail = false;
            if (
                $isadmin
                || $currentuser
                || $user->maildisplay == 1
                || ($user->maildisplay == 2 && enrol_sharing_course($user, $USER))
            ) {
                $this->user->seeemail = true;
            }

            $profile = new stdClass();

            // Extra user attributes.

            $this->user->fullname = fullname($this->user);
            $userpicture = new user_picture($this->user);
            $userpicture->link = false;
            $userpicture->size = 200;
            $this->user->picture = $userpicture->get_url(
                    $this->page
            );

            // Define contactarray from email viewing permission.
            if ($this->user->seeemail) {
                $contactarray = [
                        'email',
                        'city',
                        'country'
                ];
            } else {
                $contactarray = [
                        'city',
                        'country'
                ];
            }
            $this->carddetail(
                    'time',
                    get_string(
                            'profile:joinedon',
                            'theme_pimenko'
                    ) . userdate(
                            $this->user->firstaccess,
                            get_string('strftimemonthyear')
                    )
            );
            $this->carddetail(
                    'time',
                    get_string(
                            'profile:lastaccess',
                            'theme_pimenko'
                    ) . userdate(
                            $this->user->lastaccess,
                            get_string('strftimedatetimeshort')
                    )
            );

            $this->user->carddetails = $this->carddetails;

            $this->profileblock(
                    'person',
                    get_string(
                            'profile:basicinfo',
                            'theme_pimenko'
                    ),
                    format_text(
                            $this->user->description,
                            $this->user->descriptionformat
                    )
            );

            $this->profileblock(
                    'phone',
                    get_string(
                            'profile:contactinfo',
                            'theme_pimenko'
                    ),
                    '',
                    $contactarray
            );

            $tree = manager::build_tree($this->user, $currentuser);
            $this->page->get_renderer('core_user', 'myprofile');
            $this->render_tree($tree);

            if ($preferences) {
                $profile->content = $this->userpreferences($this->user->id);
            } else {
                $profile->content = $this->render_from_template(
                        'theme_pimenko/profiledescription',
                        $this->user
                );
            }

            $profile->user = $user;

            // User enrolments Tab.
            return $this->render_from_template(
                    'theme_pimenko/profile',
                    $profile
            );
        } else {
            return '';
        }
    }

    private function carddetail($icon, $string, $url = null) {
        if (!isset($this->carddetails)) {
            $this->carddetails = [];
        }
        $detail = new stdClass();
        $detail->icon = 'zmdi zmdi-' . $icon;
        $detail->text = $string;
        if ($url) {
            $detail->text = new moodle_url(
                    $url,
                    $detail->text
            );
        }
        if (!empty($detail->text)) {
            $this->carddetails[] = $detail;
        }
    }

    private function profileblock($icon, $name, $content, $properties = []) {
        if (!isset($this->profileblocks)) {
            $this->profileblocks = [];
        }

        $block = new stdClass();
        $block->icon = 'zmdi zmdi-' . $icon;
        switch ($icon) {
            case "badges":
                $block->icon = 'zmdi zmdi-badge-check';
                break;
            case "miscellaneous":
                $block->icon = 'zmdi zmdi-info';
                break;
            case "reports":
                $block->icon = 'zmdi zmdi-collection-text';
                break;
            case "contact":
                $block->icon = 'zmdi zmdi-account';
                break;
            case "privacyandpolicies":
                $block->icon = 'zmdi zmdi-shield-security';
                break;
            case "coursedetails":
                $block->icon = 'zmdi zmdi-graduation-cap';
                break;
        }
        $block->sectionname = $name;
        $block->properties = [];

        foreach ($properties as $property) {
            $block->dttype = true;
            if (isset($this->user->$property)) {
                $userprop = new stdClass();
                $userprop->label = get_string($property);
                $userprop->value = $this->user->$property;
                $block->properties[] = $userprop;
            }
        }

        if (is_array($content)) {
            $block->listtype = true;
            $block->properties = $content;
        } else {
            $block->content = $content;
        }

        if (!empty($content) || (count($block->properties) > 0)) {
            $this->user->profileblocks[] = $block;
        }
    }

    private function render_tree($tree) {
        $categories = $tree->categories;

        foreach ($categories as $category) {
            if ($category->name == 'loginactivity') {
                continue;
            }
            $this->render($category);
        }
    }

    public function userpreferences($userid) {
        global $USER, $CFG;

        require_once($CFG->libdir . '/navigationlib.php');
        $currentuser = $userid == $USER->id;

        // Check that the user is a valid user.
        $user = core_user::get_user($userid);

        if (!$currentuser) {
            $this->page->navigation->extend_for_user($USER);
            // Need to check that settings exist.
            if ($settings = $this->page->settingsnav->find(
                    'userviewingsettings' . $USER->id,
                    null
            )) {
                $settings->make_active();
            }
            // Show an error if there are no preferences that this user has access to.
            if (!$this->page->settingsnav->can_view_user_preferences($userid)) {
                throw new moodle_exception(
                        'cannotedituserpreferences',
                        'error'
                );
            }
        } else {
            // Shutdown the users node in the navigation menu.
            $usernode = $this->page->navigation->find(
                    'users',
                    null
            );
            $usernode->make_inactive();

            $settings = $this->page->settingsnav->find(
                    'usercurrentsettings',
                    null
            );
            $settings->make_active();
        }

        // Identifying the nodes.
        $groups = [];
        $orphans = [];

        foreach ($settings->children as $setting) {
            if ($setting->has_children()) {
                $icon = $this->pref_icon($setting->key);
                $groups[] = new preferences_group(
                        $icon . $setting->get_content(),
                        $setting->children
                );
            } else {
                $orphans[] = $setting;
            }
        }
        if (!empty($orphans)) {
            $groups[] = new preferences_group(
                    get_string('miscellaneous'),
                    $orphans
            );
        }

        $preferences = new preferences_groups($groups);

        return $this->render_preferences_groups($preferences);
    }

    public function pref_icon($key) {
        $settingicons = [
                'useraccount' => 'zmdi-info',
                'blogs' => 'zmdi-globe-alt',
                'badges' => 'zmdi-badge-check',
                '1' => 'zmdi-male'
        ];
        $icon = html_writer::tag(
                'i',
                '',
                ['class' => 'zmdi m-r-5 zmdi-miscellaneous ' . $key]
        );
        if (array_key_exists(
                $key,
                $settingicons
        )) {
            $icontype = $settingicons[$key];
            $icon = html_writer::tag(
                    'i',
                    '',
                    ['class' => 'zmdi m-r-5 ' . $icontype]
            );
        }
        return $icon;
    }

    /**
     * Renders preferences groups.
     *
     * @param preferences_groups $renderable The renderable
     *
     * @return string The output.
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function render_preferences_groups(preferences_groups $renderable) {

        foreach ($renderable->groups as $group) {
            foreach ($group->nodes as $node) {
                if ($node->has_children()) {
                    debugging(
                            'Preferences nodes do not support children',
                            DEBUG_DEVELOPER
                    );
                }
                if ($node->text == get_string('editorpreferences')) {
                    continue;
                }
                $group->shownodes[] = $this->render_pref_node($node);
            }
        }

        return $this->render_from_template(
                'theme_pimenko/preferenceblocks',
                $renderable
        );
    }

    /**
     * Render a node.
     *
     * @param node $node
     *
     * @return string
     */
    public function render_pref_node($node) {
        $nodetemplate = new stdClass();
        $nodetemplate->url = $node->action;
        $nodetemplate->title = $node->text;
        $nodetemplate->classes = $node->classes;
        return $nodetemplate;
    }

    /**
     * Render a category.
     *
     * @param category $category
     *
     * @return string
     * @throws \coding_exception
     */
    public function render_category($category) {

        $nodes = $category->nodes;
        if (empty($nodes)) {
            return '';
        }

        // Standard available.
        // Blogs - notes - forumposts - forumdiscussions.
        // Learningplans - todayslogs - alllogs.
        // Outline - complete - usersessions - grade.
        $hiddenitems = [
                'forumposts',
                'forumdiscussions'
        ];
        $content = [];
        foreach ($nodes as $key => $node) {
            if (in_array(
                    $key,
                    $hiddenitems
            )) {
                continue;
            }
            $content[] = $this->render($node);
        }
        $this->profileblock(
                $category->name,
                $category->title,
                $content
        );
    }

    /**
     * Render a node.
     *
     * @param node $node
     *
     * @return string
     */
    public function render_node($node) {
        $nodetemplate = new stdClass();
        $nodetemplate->url = $node->url;
        $nodetemplate->title = $node->title;
        $nodetemplate->content = $node->content;
        return $nodetemplate;
    }
}
