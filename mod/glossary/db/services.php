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
 * Glossary module external functions.
 *
 * @package    mod_glossary
 * @category   external
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(

    'mod_glossary_get_glossaries_by_courses' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_glossaries_by_courses',
        'description'   => 'Retrieve a list of glossaries from several courses.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_view_glossary' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'view_glossary',
        'description'   => 'Notify the glossary as being viewed.',
        'type'          => 'write',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_view_entry' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'view_entry',
        'description'   => 'Notify a glossary entry as being viewed.',
        'type'          => 'write',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_by_letter' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_by_letter',
        'description'   => 'Browse entries by letter.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_by_date' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_by_date',
        'description'   => 'Browse entries by date.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_categories' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_categories',
        'description'   => 'Get the categories.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_by_category' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_by_category',
        'description'   => 'Browse entries by category.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_authors' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_authors',
        'description'   => 'Get the authors.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_by_author' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_by_author',
        'description'   => 'Browse entries by author.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_by_author_id' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_by_author_id',
        'description'   => 'Browse entries by author ID.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_by_search' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_by_search',
        'description'   => 'Browse entries by search query.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_by_term' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_by_term',
        'description'   => 'Browse entries by term (concept or alias).',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entries_to_approve' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entries_to_approve',
        'description'   => 'Browse entries to be approved.',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:approve',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_get_entry_by_id' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'get_entry_by_id',
        'description'   => 'Get an entry by ID',
        'type'          => 'read',
        'capabilities'  => 'mod/glossary:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_add_entry' => array(
        'classname'     => 'mod_glossary_external',
        'methodname'    => 'add_entry',
        'description'   => 'Add a new entry to a given glossary',
        'type'          => 'write',
        'capabilities'  => 'mod/glossary:write',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_glossary_delete_entry' => [
        'classname'     => 'mod_glossary\external\delete_entry',
        'methodname'    => 'execute',
        'classpath'     => '',
        'description'   => 'Delete the given entry from the glossary.',
        'type'          => 'write',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ],

    'mod_glossary_update_entry' => [
        'classname'     => 'mod_glossary\external\update_entry',
        'methodname'    => 'execute',
        'classpath'     => '',
        'description'   => 'Updates the given glossary entry.',
        'type'          => 'write',
        'services'      => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ],
);
