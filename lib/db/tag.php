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
 * Tag area definitions
 *
 * File db/tag.php lists all available tag areas in core or a plugin.
 *
 * Each tag area may have the following attributes:
 *   - itemtype (required) - what is tagged. Must be name of the existing DB table
 *   - component - component responsible for tagging, if the tag area is inside a
 *     plugin the component must be the full frankenstyle name of the plugin
 *   - collection - name of the custom tag collection that will be used to store
 *     tags in this area. If specified aministrator will be able to neither add
 *     any other tag areas to this collection nor move this tag area elsewhere
 *   - searchable (only if collection is specified) - wether the tag collection
 *     should be searchable on /tag/search.php
 *   - showstandard - default value for the "Standard tags" attribute of the area,
 *     this is only respected when new tag area is added and ignored during upgrade
 *   - customurl (only if collection is specified) - custom url to use instead of
 *     /tag/search.php to display information about one tag
 *   - callback - name of the function that returns items tagged with this tag,
 *     see core_tag_tag::get_tag_index() and existing callbacks for more details,
 *     callback should return instance of core_tag\output\tagindex
 *   - callbackfile - file where callback is located (if not an autoloaded location)
 *
 * Language file must contain the human-readable names of the tag areas and
 * collections (either in plugin language file or in component language file or
 * lang/en/tag.php in case of core):
 * - for item type "user":
 *     $string['tagarea_user'] = 'Users';
 * - for tag collection "mycollection":
 *     $string['tagcollection_mycollection'] = 'My tag collection';
 *
 * @package   core
 * @copyright 2015 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$tagareas = array(
    array(
        'itemtype' => 'user', // Users.
        'component' => 'core',
        'callback' => 'user_get_tagged_users',
        'callbackfile' => '/user/lib.php',
        'showstandard' => core_tag_tag::HIDE_STANDARD,
    ),
    array(
        'itemtype' => 'course', // Courses.
        'component' => 'core',
        'callback' => 'course_get_tagged_courses',
        'callbackfile' => '/course/lib.php',
    ),
    array(
        'itemtype' => 'question', // Questions.
        'component' => 'core_question',
    ),
    array(
        'itemtype' => 'post', // Blog posts.
        'component' => 'core',
        'callback' => 'blog_get_tagged_posts',
        'callbackfile' => '/blog/lib.php',
    ),
    array(
        'itemtype' => 'blog_external', // External blogs.
        'component' => 'core',
    ),
    array(
        'itemtype' => 'course_modules', // Course modules.
        'component' => 'core',
        'callback' => 'course_get_tagged_course_modules',
        'callbackfile' => '/course/lib.php',
    ),
);
