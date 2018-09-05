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


function xmldb_local_iomad_install() {
    global $CFG, $DB;

    $systemcontext = context_system::instance();

    // Create new Company Manager role.
    if (!$companymanager = $DB->get_record( 'role', array( 'shortname' => 'companymanager') )) {
        $companymanagerid = create_role( 'Company Manager', 'companymanager',
        '(Iomad) Manages individual companies - can upload users etc.');
    } else {
        $companymanagerid = $companymanager->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $companymanagerid );
    if (empty($levels)) {
        $level = new stdClass;
        $level->roleid = $companymanagerid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Company Department Manager role.
    if (!$companydepartmentmanager = $DB->get_record('role',
                                     array( 'shortname' => 'companydepartmentmanager'))) {
        $companydepartmentmanagerid = create_role('Company Department Manager',
        'companydepartmentmanager',
        'Iomad Manages departments within companies - can upload users etc.');
    } else {
        $companydepartmentmanagerid = $companydepartmentmanager->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $companydepartmentmanagerid );
    if (empty($levels)) {
        $level = new stdclass;
        $level->roleid = $companydepartmentmanagerid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Client Administrator role.
    if (!$clientadministrator = $DB->get_record('role',
                                                 array('shortname' => 'clientadministrator'))) {
        $clientadministratorid = create_role( 'Client Administrator', 'clientadministrator',
        '(Iomad) Manages site - can create new companies and add managers etc.');
    } else {
        $clientadministratorid = $clientadministrator->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $clientadministratorid );
    if (empty($levels)) {
        $level = new stdclass();
        $level->roleid = $clientadministratorid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Client Course Editor role.
    if (!$companycourseeditor = $DB->get_record('role',
                                                 array('shortname' => 'companycourseeditor'))) {
        $companycourseeditorid = create_role( 'Client Course Editor', 'companycourseeditor',
        'Iomad Client Course Editor - can edit course content; add, delete, modify etc..');
    } else {
        $companycourseeditorid = $companycourseeditor->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels( $companycourseeditorid );
    if (empty($levels)) {
        $level = new stdclass;
        $level->roleid = $companycourseeditorid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Create new Client Course Access role.
    if (!$companycoursenoneditor = $DB->get_record('role',
                                                   array('shortname' => 'companycoursenoneditor'))) {
        $companycoursenoneditorid = create_role('Client Course Access', 'companycoursenoneditor',
        'Iomad Client Course Access - similar to the non-editing teacher role for client admin');
    } else {
        $companycoursenoneditorid = $companycoursenoneditor->id;
    }

    // If not done already, allow assignment at system context.
    $levels = get_role_contextlevels($clientadministratorid);
    if (empty($levels)) {
        $level = null;
        $level->roleid = $clientadministratorid;
        $level->contextlevel = CONTEXT_SYSTEM;
        $DB->insert_record( 'role_context_levels', $level );
    }

    // Add capabilities to above.
    $clientadministratorcaps = array(
        'block/iomad_company_admin:assign_company_manager',
        'block/iomad_company_admin:assign_department_manager',
        'block/iomad_company_admin:company_add',
        'block/iomad_company_admin:company_course_users',
        'block/iomad_company_admin:company_delete',
        'block/iomad_company_admin:company_edit',
        'block/iomad_company_admin:company_manager',
        'block/iomad_company_admin:company_user',
        'block/iomad_company_admin:createcourse',
        'block/iomad_company_admin:user_create',
        'block/iomad_company_admin:user_upload',
        'block/iomad_company_admin:restrict_capabilities',
        'block/iomad_link:view',

        'block/iomad_onlineusers:viewlist',
        'block/iomad_reports:view',
        'local/report_attendance:view',
        'local/report_completion:view',
        'local/report_users:view',
        'local/report_scorm_overview:view',
    );
    foreach ($clientadministratorcaps as $cap) {
        assign_capability( $cap, CAP_ALLOW, $clientadministratorid, $systemcontext->id);
    }
    $companydepartmentmanagercaps = array(
        'block/iomad_reports:view',
        'block/iomad_onlineusers:viewlist',
        'block/iomad_link:view',
        'block/iomad_company_admin:view_licenses',
        'block/iomad_company_admin:user_upload',
        'block/iomad_company_admin:user_create',
        'block/iomad_company_admin:editusers',
        'block/iomad_company_admin:edit_departments',
        'block/iomad_company_admin:companymanagement_view',
        'block/iomad_company_admin:company_course_users',
        'block/iomad_company_admin:company_license_users',
        'block/iomad_company_admin:assign_department_manager',
        'block/iomad_company_admin:allocate_licenses',
        'block/iomad_approve_access:approve',
        'block/iomad_company_admin:coursemanagement_view',
        'block/iomad_company_admin:licensemanagement_view',
        'block/iomad_company_admin:usermanagement_view',
        'block/iomad_company_admin:viewsuspendedusers',
        'mod/iomadcertificate:viewother',
        'block/iomad_reports:view',
        'local/report_attendance:view',
        'local/report_completion:view',
        'local/report_users:view',
        'local/report_scorm_overview:view',
        'local/report_license:view',
        'moodle/competency:plancomment',
        'moodle/competency:planmanage',
        'moodle/competency:planmanageowndraft',
        'moodle/competency:planreview',
        'moodle/competency:planview',
        'moodle/competency:usercompetencycomment',
        'moodle/competency:usercompetencyreview',
        'moodle/competency:usercompetencyview',
        'moodle/competency:userevidencemanage',
        'moodle/competency:userevidenceview',
        'moodle/competency:competencymanage',
        'moodle/competency:competencyview',
        'moodle/competency:templatemanage',
        'moodle/competency:templateview',
        'block/iomad_company_admin:competencymanagement_view',
        'block/iomad_company_admin:templateview',
    );

    foreach ($companydepartmentmanagercaps as $cap) {
        assign_capability( $cap, CAP_ALLOW, $companydepartmentmanagerid, $systemcontext->id );
    }

    $companymanagercaps = array(
        'block/iomad_onlineusers:viewlist',
        'block/iomad_link:view',
        'block/iomad_company_admin:view_licenses',
        'block/iomad_company_admin:user_upload',
        'block/iomad_company_admin:user_create',
        'block/iomad_company_admin:editusers',
        'block/iomad_company_admin:edit_departments',
        'block/iomad_company_admin:companymanagement_view',
        'block/iomad_company_admin:company_course_users',
        'block/iomad_company_admin:company_license_users',
        'block/iomad_company_admin:assign_department_manager',
        'block/iomad_company_admin:allocate_licenses',
        'block/iomad_company_admin:assign_company_manager',
        'block/iomad_company_admin:classrooms',
        'block/iomad_company_admin:classrooms_add',
        'block/iomad_company_admin:classrooms_delete',
        'block/iomad_company_admin:classrooms_edit',
        'block/iomad_company_admin:company_edit',
        'block/iomad_company_admin:company_course_unenrol',
        'block/iomad_company_admin:company_manager',
        'block/iomad_company_admin:company_user_profiles',
        'block/iomad_company_admin:createcourse',
        'block/iomad_company_admin:coursemanagement_view',
        'block/iomad_company_admin:licensemanagement_view',
        'block/iomad_company_admin:usermanagement_view',
        'block/iomad_company_admin:viewsuspendedusers',
        'mod/iomadcertificate:viewother',
        'block/iomad_reports:view',
        'local/report_attendance:view',
        'local/report_completion:view',
        'local/report_users:view',
        'local/report_scorm_overview:view',
        'local/report_license:view',
        'block/iomad_approve_access:approve',
        'moodle/competency:plancomment',
        'moodle/competency:planmanage',
        'moodle/competency:planmanageowndraft',
        'moodle/competency:planreview',
        'moodle/competency:planview',
        'moodle/competency:usercompetencycomment',
        'moodle/competency:usercompetencyreview',
        'moodle/competency:usercompetencyview',
        'moodle/competency:userevidencemanage',
        'moodle/competency:userevidenceview',
        'moodle/competency:competencymanage',
        'moodle/competency:competencyview',
        'moodle/competency:templatemanage',
        'moodle/competency:templateview',
        'block/iomad_company_admin:competencymanagement_view',
        'block/iomad_company_admin:competencyview',
        'block/iomad_company_admin:templateview',

    );

    $companycoursenoneditorcaps = array(
        'gradereport/grader:view',
        'gradereport/user:view',
        'mod/assignment:view',
        'mod/book:read',
        'mod/choice:readresponses',
        'mod/feedback:view',
        'mod/forum:addquestion',
        'mod/forum:createattachment',
        'mod/forum:deleteownpost',
        'mod/forum:replypost',
        'mod/forum:startdiscussion',
        'mod/forum:viewdiscussion',
        'mod/forum:viewqandawithoutposting',
        'mod/page:view',
        'mod/quiz:attempt',
        'mod/quiz:view',
        'mod/resource:view',
        'mod/survey:participate',
        'mod/trainingevent:add',
        'mod/trainingevent:grade',
        'mod/trainingevent:viewattendees',
        'moodle/block:view',
        'moodle/grade:viewall',
        'moodle/site:viewfullnames',
        'moodle/site:viewuseridentity',
        'moodle/competency:coursecompetencyview',
    );

    $companycourseeditorcaps = array(
        'booktool/importhtml:import',
        'booktool/print:print',
        'enrol/license:manage',
        'enrol/license:unenrol',
        'enrol/manual:enrol',
        'enrol/manual:unenrol',
        'gradereport/grader:view',
        'gradereport/overview:view',
        'gradereport/user:view',
        'mod/assignment:exportownsubmission',
        'mod/assignment:grade',
        'mod/assignment:view',
        'mod/book:edit',
        'mod/book:read',
        'mod/book:viewhiddenchapters',
        'mod/choice:choose',
        'mod/choice:deleteresponses',
        'mod/choice:downloadresponses',
        'mod/choice:readresponses',
        'mod/trainingevent:add',
        'mod/trainingevent:grade',
        'mod/trainingevent:invite',
        'mod/trainingevent:viewattendees',
        'mod/forum:addnews',
        'mod/forum:addquestion',
        'mod/forum:createattachment',
        'mod/forum:deleteanypost',
        'mod/forum:deleteownpost',
        'mod/forum:editanypost',
        'mod/forum:exportdiscussion',
        'mod/forum:exportownpost',
        'mod/forum:exportpost',
        'mod/forum:managesubscriptions',
        'mod/forum:movediscussions',
        'mod/forum:postwithoutthrottling',
        'mod/forum:rate',
        'mod/forum:replynews',
        'mod/forum:replypost',
        'mod/forum:splitdiscussions',
        'mod/forum:startdiscussion',
        'mod/forum:viewallratings',
        'mod/forum:viewanyrating',
        'mod/forum:viewdiscussion',
        'mod/forum:viewhiddentimedposts',
        'mod/forum:viewqandawithoutposting',
        'mod/forum:viewrating',
        'mod/forum:viewsubscribers',
        'mod/page:view',
        'mod/resource:view',
        'mod/scorm:deleteresponses',
        'mod/scorm:savetrack',
        'mod/scorm:viewreport',
        'mod/scorm:viewscores',
        'mod/url:view',
        'moodle/block:edit',
        'moodle/block:view',
        'moodle/calendar:manageentries',
        'moodle/calendar:managegroupentries',
        'moodle/calendar:manageownentries',
        'moodle/course:activityvisibility',
        'moodle/course:changefullname',
        'moodle/course:changesummary',
        'moodle/course:manageactivities',
        'moodle/course:managefiles',
        'moodle/course:managegroups',
        'moodle/course:markcomplete',
        'moodle/course:reset',
        'moodle/course:sectionvisibility',
        'moodle/course:setcurrentsection',
        'moodle/course:update',
        'moodle/course:viewhiddenactivities',
        'moodle/course:viewhiddensections',
        'moodle/course:viewparticipants',
        'moodle/grade:edit',
        'moodle/grade:hide',
        'moodle/grade:lock',
        'moodle/grade:manage',
        'moodle/grade:managegradingforms',
        'moodle/grade:manageletters',
        'moodle/grade:manageoutcomes',
        'moodle/grade:unlock',
        'moodle/grade:viewall',
        'moodle/grade:viewhidden',
        'moodle/notes:manage',
        'moodle/notes:view',
        'moodle/rating:rate',
        'moodle/rating:view',
        'moodle/rating:viewall',
        'moodle/rating:viewany',
        'moodle/role:switchroles',
        'moodle/site:accessallgroups',
        'moodle/site:manageblocks',
        'moodle/site:trustcontent',
        'moodle/site:viewfullnames',
        'moodle/site:viewreports',
        'moodle/site:viewuseridentity',
        'moodle/user:viewdetails',
        'report/courseoverview:view',
        'report/log:view',
        'report/log:viewtoday',
        'report/loglive:view',
        'report/outline:view',
        'report/participation:view',
        'report/progress:view',
        'moodle/competency:competencygrade',
        'moodle/competency:coursecompetencymanage',
        'moodle/competency:coursecompetencyview',
        'moodle/competency:coursecompetencyconfigure',

    );

    foreach ($companymanagercaps as $cap) {
        assign_capability( $cap, CAP_ALLOW, $companymanagerid, $systemcontext->id );
    }

    foreach ($companycourseeditorcaps as $rolecapability) {
        // Assign_capability will update rather than insert if capability exists.
        assign_capability($rolecapability, CAP_ALLOW, $companycourseeditorid,
                          $systemcontext->id);
    }

    foreach ($companycoursenoneditorcaps as $rolecapability) {
        // Assign_capability will update rather than insert if capability exists.
        assign_capability($rolecapability, CAP_ALLOW, $companycoursenoneditorid,
                          $systemcontext->id);
    }

    // Even worse - change the theme.
    $theme = theme_config::load('iomadboost');
    set_config('theme', $theme->name);
    set_config('allowuserthemes', 1);

    // Enable completion tracking.
    set_config('enablecompletion', 1);

    // Set the default blocks in courses.
    $defblocks = ':iomad_link,iomad_company_selector,iomad_onlineusers,completionstatus';
    set_config('defaultblocks_topics', $defblocks);
    set_config('defaultblocks_weeks', $defblocks);

    // Change the default settings for extended username chars to be true.
    $DB->execute("update {config} set value=1 where name='extendedusernamechars'");

}
