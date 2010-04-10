<?PHP // $Id$ 
      // enrol_ldap.php - created with Moodle 1.7 beta + (2006101003)


$string['description'] = '<p>You can use an LDAP server to control your enrolments.  
                          It is assumed your LDAP tree contains groups that map to 
                          the courses, and that each of thouse groups/courses will 
                          have membership entries to map to students.</p>
                          <p>It is assumed that courses are defined as groups in 
                          LDAP, with each group having multiple membership fields 
                          (<em>member</em> or <em>memberUid</em>) that contain a unique
                          identification of the user.</p>
                          <p>To use LDAP enrolment, your users <strong>must</strong> 
                          to have a valid  idnumber field. The LDAP groups must have 
                          that idnumber in the member fields for a user to be enrolled 
                          in the course.
                          This will usually work well if you are already using LDAP 
                          Authentication.</p>
                          <p>Enrolments will be updated when the user logs in. You
                           can also run a script to keep enrolments in synch. Look in 
                          <em>enrol/ldap/enrol_ldap_sync.php</em>.</p>
                          <p>This plugin can also be set to automatically create new 
                          courses when new groups appear in LDAP.</p>';
$string['enrol_ldap_autocreate'] = 'Courses can be created automatically if there are
                                    enrolments to a course  that doesn\'t yet exist 
                                    in Moodle.';
$string['enrol_ldap_autocreation_settings'] = 'Automatic course creation settings';
$string['enrol_ldap_bind_dn'] = 'If you want to use bind-user to search users, 
                                 specify it here. Someting like 
                                 \'cn=ldapuser,ou=public,o=org\'';
$string['enrol_ldap_bind_pw'] = 'Password for bind-user.';
$string['enrol_ldap_category'] = 'The category for auto-created courses.';
$string['enrol_ldap_contexts'] = 'LDAP contexts';
$string['enrol_ldap_course_fullname'] = 'Optional: LDAP field to get the full name from.';
$string['enrol_ldap_course_idnumber'] = 'Map to the unique identifier in LDAP, usually
                                         <em>cn</em> or <em>uid</em>. It is 
                                         recommended to lock the value if you are using 
                                         automatic course creation.';
$string['enrol_ldap_course_settings'] = 'Course enrolment settings';
$string['enrol_ldap_course_shortname'] = 'Optional: LDAP field to get the shortname from.';
$string['enrol_ldap_course_summary'] = 'Optional: LDAP field to get the summary from.';
$string['enrol_ldap_editlock'] = 'Lock value';
$string['enrol_ldap_general_options'] = 'General Options';
$string['enrol_ldap_host_url'] = 'Specify LDAP host in URL-form like 
                                  \'ldap://ldap.myorg.com/\' 
                                  or \'ldaps://ldap.myorg.com/\'';
$string['enrol_ldap_memberattribute'] = 'LDAP member attribute';
$string['enrol_ldap_objectclass'] = 'objectClass used to search courses. Usually
                                     \'posixGroup\'.';
$string['enrol_ldap_roles'] = 'Role mapping';
$string['enrol_ldap_search_sub'] = 'Search group memberships from subcontexts.';
$string['enrol_ldap_server_settings'] = 'LDAP Server Settings';
$string['enrol_ldap_student_contexts'] = 'List of contexts where groups with student
                                          enrolments are located. Separate different 
                                          contexts with \';\'. For example: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_student_memberattribute'] = 'Member attribute, when users belongs
                                          (is enrolled) to a group. Usually \'member\'
                                          or \'memberUid\'.';
$string['enrol_ldap_student_settings'] = 'Student enrolment settings';
$string['enrol_ldap_teacher_contexts'] = 'List of contexts where groups with teacher
                                          enrolments are located. Separate different 
                                          contexts with \';\'. For example: 
                                          \'ou=courses,o=org; ou=others,o=org\'';
$string['enrol_ldap_teacher_memberattribute'] = 'Member attribute, when users belongs
                                          (is enrolled) to a group. Usually \'member\'
                                          or \'memberUid\'.';
$string['enrol_ldap_teacher_settings'] = 'Teacher enrolment settings';
$string['enrol_ldap_template'] = 'Optional: auto-created courses can copy 
                                  their settings from a template course.';
$string['enrol_ldap_updatelocal'] = 'Update local data';
$string['enrol_ldap_version'] = 'The version of the LDAP protocol your server is using.';
$string['enrolname'] = 'LDAP';

?>
