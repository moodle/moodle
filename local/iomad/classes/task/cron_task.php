<?php

namespace local_iomad\task;

class cron_task extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('crontask', 'local_iomad');
    }

    /**
     * Run email cron.
     */
    public function execute() {
        global $DB, $CFG;

        // Are we copying Company to institution?
        if (!empty($CFG->iomad_sync_institution)) {
            mtrace("Copying company shortnames to user institution fields\n");
            // Get the users where it's wrong.
            $users = $DB->get_records_sql("SELECT u.*, c.id as companyid
                                           FROM {user} u
                                           JOIN {company_users} cu ON cu.userid = u.id
                                           JOIN {company} c ON cu.companyid = c.id
                                           WHERE u.institution != c.shortname
                                           AND c.parentid = 0");
            // Get all of the companies.
            $companies = $DB->get_records('company', array(), '', 'id,shortname');
            foreach ($users as $user) {
mtrace("setting user id " . $user->id . " institution to " .  $companies[$user->companyid]->shortname);
                $user->institution = $companies[$user->companyid]->shortname;
                $DB->update_record('user', $user);
            }
            $companies = array();
            $users = array();
        }

        // Are we copying department to department?
        if (!empty($CFG->iomad_sync_department)) {
            mtrace("Copying company department name to user department fields\n");
            // Get the users where it's wrong.
            $users = $DB->get_records_sql("SELECT u.*, d.id as departmentid
                                           FROM {user} u
                                           JOIN {company_users} cu ON cu.userid = u.id
                                           JOIN {company} c ON cu.companyid = c.id
                                           JOIN {department} d ON cu.departmentid = d.id
                                           WHERE u.department != d.name
                                           AND c.parentid = 0");
            // Get all of the companies.
            $departments = $DB->get_records('department', array(), '', 'id,name');
            foreach ($users as $user) {
mtrace("setting user id " . $user->id . " department to " .  $departments[$user->departmentid]->name);
                $user->department = $departments[$user->departmentid]->name;
                $DB->update_record('user', $user);
            }
            $companies = array();
            $users = array();
        }
    }
}
