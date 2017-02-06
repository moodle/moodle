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
            $DB->execute("UPDATE {user} u
                          JOIN {company_users} cu ON cu.userid = u.id
                          JOIN {company} c ON cu.companyid = c.id
                          SET u.institution = c.shortname where u.institution != c.shortname");
        }

        // Are we copying department to department?
        if (!empty($CFG->iomad_sync_department)) {
            mtrace("Copying company department name to user department fields\n");
            $DB->execute("UPDATE {user} u
                          JOIN {company_users} cu ON cu.userid = u.id
                          JOIN {department} d ON cu.departmentid = d.id
                          SET u.department = d.name where u.departname != d.name");
        }
    }
}
