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
 * Edit category form.
 *
 * This file and class have been deprecated, the form has been renamed to core_course_editcategory_form and is not autoloaded when
 * first used. Please update your code to use this new form.
 *
 * @deprecated since 2.6
 * @todo remove in 2.7 MDL-41502
 *
 * @package core_course
 * @copyright 2002 onwards Martin Dougiamas (http://dougiamas.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

debugging('Please update your code to use core_course_editcategory_form (autloaded). This file will be removed in 2.7');

/**
 * Class editcategory_form.
 *
 * This file and class have been deprecated, the form has been renamed to core_course_editcategory_form and is not autoloaded when
 * first used. Please update your code to use this new form.
 *
 * @deprecated since 2.6
 * @todo remove in 2.7 MDL-41502
 * @package core_course
 * @copyright 2002 onwards Martin Dougiamas (http://dougiamas.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editcategory_form extends core_course_editcategory_form {

    /**
     * Constructs the form.
     * @param null $action
     * @param null $customdata
     * @param string $method
     * @param string $target
     * @param null $attributes
     * @param bool $editable
     */
    public function __construct($action = null, $customdata = null, $method = 'post', $target = '', $attributes = null,
                                $editable = true) {
        $customdata['categoryid'] = $customdata['category']->id;
        $customdata['parent'] = $customdata['category']->parent;
        unset($customdata['category']);
        parent::moodleform($action, $customdata, $method, $target, $attributes, $editable);
    }

};