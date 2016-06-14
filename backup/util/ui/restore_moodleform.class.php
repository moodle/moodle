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
 * This file contains the forms used by the restore stages
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * An abstract moodleform class specially designed for the restore forms.
 *
 * @abstract Marked abstract here because some idiot forgot to mark it abstract in code!
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_moodleform extends base_moodleform {
    /**
     * Constructor.
     *
     * Overridden just for the purpose of typehinting the first arg.
     *
     * @param restore_ui_stage $uistage
     * @param null $action
     * @param null $customdata
     * @param string $method
     * @param string $target
     * @param null $attributes
     * @param bool $editable
     */
    public function __construct(restore_ui_stage $uistage, $action = null, $customdata = null, $method = 'post',
                                $target = '', $attributes = null, $editable = true) {
        parent::__construct($uistage, $action, $customdata, $method, $target, $attributes, $editable);
    }
}

/**
 * Restore settings form.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_settings_form extends restore_moodleform {}

/**
 * Restore schema review form.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_schema_form extends restore_moodleform {}

/**
 * Restore complete process review form.
 *
 * @package   core_backup
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_review_form extends restore_moodleform {};