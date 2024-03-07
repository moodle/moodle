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
 * An adhoc task for local Iomad
 *
 * @package    local_iomad
 * @copyright  2024 E-Learn Design https://www.e-learndesign.co.uk
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_iomad\task;

defined('MOODLE_INTERNAL') || die();

use core\task\adhoc_task;
use company;
use company_user;
use iomad;
use core_course_category;

class deletecompanytask extends adhoc_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('deletecompany', 'block_iomad_company_admin');
    }

    /**
     * Run fixtracklicensestask
     */
    public function execute() {
        global $DB, $CFG;

        $customdata = $this->get_custom_data();

        if (!$companyrec  = $DB->get_record('company', ['id' => $customdata->companyid])) {
            // Company doesn't exist.
            return;
        }

        mtrace("deleting company $companyrec->name");

        // Delete the certificates
        mtrace("deleting all stored certificates");
        $tracrecs = $DB->get_records_sql("SELECT lit.id
                                          FROM {local_iomad_track} lit
                                          JOIN {local_iomad_track_certs} litc
                                          ON (lit.id = litc.trackid)
                                          WHERE lit.companyid = :companyid",
                                          ['companyid' => $companyrec->id]);

        foreach ($tracrecs as $tracrec) {
            require_once($CFG->libdir . "/../local/iomad_track/lib.php");
            \local_iomad_track_delete_entry($tracrec->id, true);
        }

        mtrace("dealing with all completion reports");
        $DB->delete_records('local_iomad_track', ['companyid' => $companyrec->id]);

        mtrace("dealing with all license allocation reports");
        $licenses = $DB->get_records('companylicense', ['companyid' => $companyrec->id]);
        foreach ($licenses as $license) {
            $DB->delete_records('local_report_user_lic_allocs', ['licenseid' => $license->id]);
        }

        mtrace("dealing with all licenses");
        $companylicenses = $DB->get_records('companylicense', ['companyid' => $companyrec->id]);
        foreach ($companylicenses as $companylicense) {
            $DB->delete_records('companylicense_users', ['licenseid' => $companylicense->id]);
            $DB->delete_records('companylicense_courses', ['licenseid' => $companylicense->id]);
            $DB->delete_records('companylicense', ['id' => $companylicense->id]);
        }

        mtrace("dealing with frameworks and templates");
        $DB->delete_records('company_comp_frameworks', ['companyid' => $companyrec->id]);
        $DB->delete_records('company_shared_frameworks', ['companyid' => $companyrec->id]);
        $DB->delete_records('company_comp_templates', ['companyid' => $companyrec->id]);
        $DB->delete_records('company_shared_templates', ['companyid' => $companyrec->id]);

        mtrace("dealing with roles");
        $DB->delete_records('company_role_restriction', ['companyid' => $companyrec->id]);
        $DB->delete_records('company_role_templates_ass', ['companyid' => $companyrec->id]);

        mtrace("dealing with email templates");
        $DB->delete_records('email_template', ['companyid' => $companyrec->id]);

        mtrace("dealing with users");
        $users = $DB->get_records_sql("SELECT DISTINCT userid
                                       FROM {company_users}
                                       WHERE companyid = :companyid",
                                       ['companyid' => $companyrec->id]);
        foreach ($users as $user) {
            company_user::delete($user->userid, $companyrec->id);
        }
        // Blanket deletion.
        $DB->delete_records('company_users', ['companyid' => $companyrec->id]);

        mtrace("dealing with courses");
        $DB->delete_records('company_course_groups', ['companyid' => $companyrec->id]);
        $DB->delete_records('company_course_autoenrol', ['companyid' => $companyrec->id]);

        // Get courses which are just allocated to this company and not shared.
        $companycourses = $DB->get_records_sql("SELECT cc.courseid
                                                FROM {company_course} cc
                                                JOIN {iomad_courses} ic ON (cc.courseid = ic.courseid)
                                                JOIN {course} c ON (cc.courseid = c.id AND ic.courseid = c.id)
                                                WHERE ic.shared = 0
                                                AND cc.companyid = :companyid",
                                               ['companyid' => $companyrec->id]);
        foreach ($companycourses as $companycourse) {
            mtrace("deleting course ID $companycourse->courseid");
            delete_course($companycourse->courseid, false);
        }                                                
        $DB->delete_records('company_course', ['companyid' => $companyrec->id]);
        $DB->delete_records('company_created_courses', ['companyid' => $companyrec->id]);
        $DB->delete_records('company_shared_courses', ['companyid' => $companyrec->id]);

        // Deal with company course category
        mtrace("deleting company course category");
        if ($DB->get_record('course_categories', ['id' => $companyrec->category])) {
            $category = core_course_category::get($companyrec->category);
            if (!$category->has_courses() && !$category->has_children()) {
                $category->delete_full();
            } else {
                mtrace("Could not do this as not empty");
            }
        }

        // Deal with company profile fields
        mtrace("deleting company profile field category");
        $profilefields = $DB->get_records('user_info_field', ['categoryid' => $companyrec->profileid]);
        foreach ($profilefields as $profilefield) {
            $DB->delete_records('user_info_data', ['fieldid' => $profilefield->id]);
            $DB->delete_records('user_info_field', ['id' => $profilefield->id]);
        }
        $DB->delete_records('user_info_category', ['id' => $companyrec->profileid]);

        mtrace("dealing with departments");
        $DB->delete_records('department', ['company' => $companyrec->id]);

        mtrace("dealing with the company");
        if (!empty($companyrec->parentid)) {
            $DB->set_field('company', 'parentid', $companyrec->parentid, ['parentid' => $companyrec->id]);
        } else {
            $DB->set_field('company', 'parentid', 0, ['parentid' => $companyrec->id]);
        }
        $DB->delete_records('department', ['company' => $companyrec->id]);
        $DB->delete_records('company', ['id' => $companyrec->id]);

        mtrace("clearing up any config");
        $DB->delete_records_select('config', $DB->sql_like('name', ":name"), ['name' => '%' . $DB->sql_like_escape($companyrec->id)]);
        $DB->delete_records_select('config_plugins', $DB->sql_like('name', ":name"), ['name' => '%' . $DB->sql_like_escape($companyrec->id)]);
        
        if ($files = $DB->get_records_select('files',
                                             "component = 'core_admin' AND " . $DB->sql_like('filearea', ':filearea') . " AND filename !='.'",
                                             ['filearea' => "%" .  $DB->sql_like_escape($companyrec->id)])) {
            $fs =   get_file_storage();
            foreach ($files as $filerec) {
                $file = $fs->get_file($filerec->contextid, $filerec->component, $filerec->filearea, $filerec->itemid, $filerec->filepath, $filerec->filename);
                $file->delete();
            } 
        }
    }

    /**
     * Queues the task.
     *
     */
    public static function queue_task() {

        // Let's set up the adhoc task.
        $task = new \local_iomad\task\deletecompanytask();
        \core\task\manager::queue_adhoc_task($task, true);
    }
}
