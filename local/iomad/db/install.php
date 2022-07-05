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
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_local_iomad_install() {
    global $CFG, $DB;

    $systemcontext = context_system::instance();

    // Even worse - change the theme.
    $theme = theme_config::load('iomadboost');
    set_config('theme', $theme->name);
    set_config('allowuserthemes', 1);

    // Enable completion tracking.
    set_config('enablecompletion', 1);

    // Set the default blocks in courses.
    $defblocks = '';
    set_config('defaultblocks_topics', $defblocks);
    set_config('defaultblocks_weeks', $defblocks);

    // Change the default settings for extended username chars to be true.
    $DB->execute("update {config} set value='1' where name='extendedusernamechars'");

    // Set up the new roles for IOMAD.
    // Create the Company Manager role.
    if (!$companymanager = $DB->get_record('role', array('shortname' => 'companymanager'))) {
        $companymanagerid = create_role('Company Manager',
                                        'companymanager',
                                        '(IOMAD) Manages individual companies - can upload users etc.',
                                        'companymanager'
                                        );

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companymanagerid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companymanagerid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('companymanager');
    }

    // Create new Company Department Manager role.
    if (!$companydepartmentmanager = $DB->get_record('role',
                                     array('shortname' => 'companydepartmentmanager'))) {
        $companydepartmentmanagerid = create_role('Company Department Manager',
                                                  'companydepartmentmanager',
                                                  '(IOMAD) Manages departments within companies - can upload users etc.',
                                                  'companydepartmentmanager'
                                                  );

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companydepartmentmanagerid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companydepartmentmanagerid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('companydepartmentmanager');
    }

    // Create the Company Course Editor.
    if (!$companycourseeditor = $DB->get_record('role',
                                                array('shortname' => 'companycourseeditor'))) {
        $companycourseeditorid = create_role('Company Course Editor',
                                             'companycourseeditor',
                                             '(IOMAD) Teacher style role for Company manager provided to them when they create their own course.',
                                             'companycourseeditor'
                                             );

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companycourseeditorid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companycourseeditorid;
            $level->contextlevel = CONTEXT_COURSE;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('companycourseeditor');
    }

    // Create new Company Course Non Editor role.
    if (!$companycoursenoneditor = $DB->get_record('role',
                                        array('shortname' => 'companycoursenoneditor'))) {
        $companycoursenoneditorid = create_role('Company Course Non Editor',
                                                'companycoursenoneditor',
                                                '(IOMAD) Non editing teacher style role form Company and department managers',
                                                'companycoursenoneditor'
                                                );

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companycoursenoneditorid );
        if (empty($levels)) {
            $level = null;
            $level->roleid = $companycoursenoneditorid;
            $level->contextlevel = CONTEXT_COURSE;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('companycoursenoneditor');
    }

    // Create new Company reporter role.
    if (!$companyreporter = $DB->get_record( 'role', array( 'shortname' => 'companyreporter') )) {
        $companyreporterid = create_role('Company Report Only',
                                         'companyreporter',
                                         '(IOMAD) Access to company reports only..',
                                         'companyreporter'
                                         );

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $companyreporterid );
        if (empty($levels)) {
            $level = new stdClass;
            $level->roleid = $companyreporterid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('companyreporter');
    }

    // Create new Client reporter role.
    if (!$clientreporter = $DB->get_record( 'role', array( 'shortname' => 'clientreporter') )) {
        $clientreporterid = create_role('Company Report Only',
                                        'clientreporter',
                                        '(IOMAD) Client access to all company reports only..',
                                        'clientreporter'
                                        );

        // If not done already, allow assignment at system context.
        $levels = get_role_contextlevels( $clientreporterid );
        if (empty($levels)) {
            $level = new stdClass;
            $level->roleid = $clientreporterid;
            $level->contextlevel = CONTEXT_SYSTEM;
            $DB->insert_record( 'role_context_levels', $level );
        }

        update_capabilities('clientreporter');
    }
}
