<?php  // $Id$
//
// Capability definitions for Moodle core.
//
// The capabilities are loaded into the database table when the module is
// installed or updated. Whenever the capability definitions are updated,
// the module version number should be bumped up.
//
// The system has four possible values for a capability:
// CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
//
//
// CAPABILITY NAMING CONVENTION
//
// It is important that capability names are unique. The naming convention
// for capabilities that are specific to modules and blocks is as follows:
//   [mod/block]/<component_name>:<capabilityname>
//
// component_name should be the same as the directory name of the mod or block.
//
// Core moodle capabilities are defined thus:
//    moodle/<capabilityclass>:<capabilityname>
//
// Examples: mod/forum:viewpost
//           block/recent_activity:view
//           moodle/site:deleteuser
//
// The variable name for the capability definitions array follows the format
//   $<componenttype>_<component_name>_capabilities
//
// For the core capabilities, the variable is $moodle_capabilities.


$moodle_capabilities = array(

    'moodle/site:doanything' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG | RISK_DATALOSS,

        'captype' => 'admin',
        'contextlevel' => CONTEXT_SYSTEM
    ),

    'moodle/legacy:guest' => array(

        'captype' => 'legacy',
        'contextlevel' => CONTEXT_SYSTEM
    ),

    'moodle/legacy:user' => array(

        'captype' => 'legacy',
        'contextlevel' => CONTEXT_SYSTEM
    ),

    'moodle/legacy:student' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'legacy',
        'contextlevel' => CONTEXT_SYSTEM
    ),


    'moodle/legacy:teacher' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL,

        'captype' => 'legacy',
        'contextlevel' => CONTEXT_SYSTEM
    ),


    'moodle/legacy:editingteacher' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'legacy',
        'contextlevel' => CONTEXT_SYSTEM
    ),


    'moodle/legacy:coursecreator' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'legacy',
        'contextlevel' => CONTEXT_SYSTEM
    ),


    'moodle/legacy:admin' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG | RISK_DATALOSS,

        'captype' => 'legacy',
        'contextlevel' => CONTEXT_SYSTEM
    ),


    'moodle/site:config' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG | RISK_DATALOSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:readallmessages' => array(

        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),

    'moodle/site:sendmessage' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW,
            'user' => CAP_ALLOW
        )
    ),

    'moodle/site:approvecourse' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:import' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:backup' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/backup:userinfo' => array(

        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:restore' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/restore:userinfo' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/restore:rolldates' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'coursecreator' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:manageblocks' => array(

        'riskbitmask' => RISK_SPAM | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:accessallgroups' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:viewfullnames' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:viewreports' => array(

        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:trustcontent' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:uploadusers' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:langeditmaster' => array(

        'riskbitmask' => RISK_CONFIG | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_PREVENT
        )
    ),

    'moodle/site:langeditlocal' => array(

        'riskbitmask' => RISK_CONFIG | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),


    'moodle/user:create' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:delete' => array(

        'riskbitmask' => RISK_PERSONAL, RISK_DATALOSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:update' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:viewdetails' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'guest' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:viewhiddendetails' => array(

        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:loginas' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS | RISK_CONFIG,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/role:assign' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/role:override' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/role:safeoverride' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW
        )
    ),

    'moodle/role:manage' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL | RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/role:unassignself' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'student' => (empty($CFG->allowunenrol)) ? CAP_INHERIT : CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/role:viewhiddenassigns' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/role:switchroles' => array(

        'riskbitmask' => RISK_XSS | RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    // Create, update and delete course categories. (Deleting a course category
    // does not let you delete the courses it contains, unless you also have
    // moodle/course: delete.) Creating and deleting requires this permission in
    // the parent category.
    'moodle/category:manage' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'legacy' => array(
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/category:update'
    ),

    'moodle/category:viewhiddencategories' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSECAT,
        'legacy' => array(
            'coursecreator' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/category:visibility'
    ),

    'moodle/course:create' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSECAT,
        'legacy' => array(
            'coursecreator' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:request' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'user' => CAP_ALLOW,
        )
    ),

    'moodle/course:delete' => array(

        'riskbitmask' => RISK_DATALOSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:update' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'guest' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW
        )
    ),

    'moodle/course:bulkmessaging' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:viewhiddenuserfields' => array(

        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:viewhiddencourses' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:visibility' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:managefiles' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:manageactivities' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:managemetacourse' => array(

        'riskbitmask' => RISK_XSS | RISK_PERSONAL,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:activityvisibility' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:viewhiddenactivities' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:viewparticipants' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:changefullname' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:update'
    ),

    'moodle/course:changeshortname' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:update'
    ),

    'moodle/course:changeidnumber' => array(

        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:update'
    ),

    'moodle/course:changecategory' => array(
        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:update'
    ),

    'moodle/course:changesummary' => array(
        'riskbitmask' => RISK_XSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:update'
    ),

    'moodle/site:viewparticipants' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:viewscales' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:managescales' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:managegroups' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:reset' => array(

        'riskbitmask' => RISK_DATALOSS,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/blog:view' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'guest' => CAP_ALLOW,
            'user' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/blog:create' => array( // works in CONTEXT_SYSTEM only

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'user' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/blog:manageentries' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/calendar:manageownentries' => array( // works in CONTEXT_SYSTEM only

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'user' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/calendar:managegroupentries' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/calendar:manageentries' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:editprofile' => array(

        'riskbitmask' => RISK_SPAM | RISK_PERSONAL,

        'captype' => 'write',
        'contextlevel' => CONTEXT_USER,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:editownprofile' => array(

        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'user' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:changeownpassword' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'guest' => CAP_PROHIBIT,
            'user' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    // The next 3 might make no sense for some roles, e.g teacher, etc.
    // since the next level up is site. These are more for the parent role
    'moodle/user:readuserposts' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/user:readuserblogs' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    // designed for parent role - not used in legacy roles
    'moodle/user:viewuseractivitiesreport' => array(
        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_USER,
        'legacy' => array(
        )
    ),

    'moodle/question:managecategory' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    //new in moodle 1.9
    'moodle/question:add' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:editmine' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:editall' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:viewmine' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:viewall' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:usemine' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:useall' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:movemine' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    'moodle/question:moveall' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' =>  'moodle/question:manage'
    ),
    //END new in moodle 1.9

    // Configure the installed question types.
    'moodle/question:config' => array(
        'riskbitmask' => RISK_CONFIG,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:doclinks' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:sectionvisibility' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:useremail' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:viewhiddensections' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/course:setcurrentsection' => array(

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/site:mnetlogintoremote' => array(

        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/grade:viewall' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE, // and CONTEXT_USER
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:viewcoursegrades'
    ),

    'moodle/grade:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'student' => CAP_ALLOW
        )
    ),

    'moodle/grade:viewhidden' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:viewcoursegrades'
    ),

    'moodle/grade:import' => array(
        'riskbitmask' => RISK_PERSONAL | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:managegrades'
    ),

    'moodle/grade:export' => array(
        'riskbitmask' => RISK_PERSONAL,
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:managegrades'
    ),

    'moodle/grade:manage' => array(
        'riskbitmask' => RISK_PERSONAL | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:managegrades'
    ),

    'moodle/grade:edit' => array(
        'riskbitmask' => RISK_PERSONAL | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:managegrades'
    ),

    'moodle/grade:manageoutcomes' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:managegrades'
    ),

    'moodle/grade:manageletters' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/course:managegrades'
    ),

    'moodle/grade:hide' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/grade:lock' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/grade:unlock' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/my:manageblocks' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'user' => CAP_ALLOW
        )
    ),

    'moodle/notes:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/notes:manage' => array(
        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/tag:manage' => array(
        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/tag:create' => array(
        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW,
            'user' => CAP_ALLOW
        )
    ),

    'moodle/tag:edit' => array(
        'riskbitmask' => RISK_SPAM,

        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'admin' => CAP_ALLOW,
            'user' => CAP_ALLOW
        )
    ),

    'moodle/tag:editblocks' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'legacy' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'admin' => CAP_ALLOW
        )
    ),

    'moodle/block:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'legacy' => array(
            'guest' => CAP_ALLOW,
            'user' => CAP_ALLOW,
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW
        )
    )
);


?>
