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

namespace core_course;

/**
 * Class that is the return value for the _get_coursemodule_info module API function.
 *
 * Note: For backward compatibility, you can also return a stdclass object from that function.
 * The difference is that the stdclass object may contain an 'extra' field (deprecated,
 * use extraclasses and onclick instead). The stdclass object may not contain
 * the new fields defined here (content, extraclasses, customdata).
 *
 * @package     core_course
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   Sam Marshall
 */
class cached_cm_info {
    /**
     * Name (text of link) for this activity; Leave unset to accept default name
     * @var string
     */
    public $name;

    /**
     * @var string Name of icon for this activity.
     *
     * Normally, this should be used together with $iconcomponent to define the icon, as per image_url function.
     *
     * For backward compatibility, if this value is of the form 'mod/forum/icon' then an icon
     * within that module will be used.
     *
     * @see cm_info::get_icon_url()
     * @see \core\output\renderer_base::image_url()
     */
    public $icon;

    /**
     * @var string Component for icon for this activity, as per image_url; leave blank to use default 'moodle' component
     * @see \core\output\renderer_base::image_url()
     */
    public $iconcomponent;

    /** @var string HTML content to be displayed on the main page below the link (if any) for this course-module */
    public $content;

    /**
     * Custom data to be stored in modinfo for this activity; useful if there are cases when
     * internal information for this activity type needs to be accessible from elsewhere on the
     * course without making database queries. May be of any type but should be short.
     * @var mixed
     */
    public $customdata;

    /**
     * Extra CSS class or classes to be added when this activity is displayed on the main page;
     * space-separated string
     * @var string
     */
    public $extraclasses;

    /**
     * External URL image to be used by activity as icon, useful for some external-tool modules
     * like lti. If set, takes precedence over $icon and $iconcomponent
     * @var $moodle_url
     */
    public $iconurl;

    /**
     * Content of onclick JavaScript; escaped HTML to be inserted as attribute value
     * @var string
     */
    public $onclick;
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cached_cm_info::class, \cached_cm_info::class);
