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
 * File containing the provider interface for plugins needing access to all approved contexts to fill in relevant contextual data.
 *
 * Plugins should implement this if they need access to all approved contexts.
 *
 * @package core_privacy
 * @copyright 2018 Adrian Greeve <adriangreeve.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * The provider interface for plugins which need access to all approved contexts to fill in relevant contextual data.
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface context_aware_provider extends \core_privacy\local\request\core_data_provider {

    /**
     * Give the component a chance to include any contextual information deemed relevant to any child contexts which are
     * exporting personal data.
     *
     * By giving the component access to the full list of contexts being exported across all components, it can determine whether a
     * descendant context is being exported, and decide whether to add relevant contextual information about itself. Having access
     * to the full list of contexts being exported is what makes this component a context aware provider.
     *
     * @param  \core_privacy\local\request\contextlist_collection $contextcollection The collection of approved context lists.
     */
    public static function export_context_data(\core_privacy\local\request\contextlist_collection $contextcollection);
}
