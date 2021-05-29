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
 * "Business" class for Iomad Learning Paths
 *
 * @package    local_iomadlearninpath
 * @copyright  2021 Derick Turner
 * @author     Derick Turner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iomad_learningpath;

defined('MOODLE_INTERNAL') || die();

use company;

class companypaths {

    protected $context;

    protected $companyid;

    protected $company;

    protected $categories;

    protected $programlicenses;

    public function __construct($companyid, $context) {
        $this->context = $context;
        $this->companyid = $companyid;
        $this->company = new \company($companyid);
    }

    /**
     * Convenience function to return the company
     * @return object
     */
    public function get_company() {
        return $this->company;
    }

    /**
     * Get learning paths for company.
     * @return array
     */
    public function get_paths() {
        global $DB;

        $paths = $DB->get_records('iomad_learningpath', array('company' => $this->companyid));

        return $paths;
    }

    /**
     * Get/check path
     * @param int $id (0 = new/empty)
     * @param bool $create new if does not exist
     * @return object $path
     */
    public function get_path($id, $create = true) {
        global $DB;

        if ($path = $DB->get_record('iomad_learningpath', array('id' => $id))) {
            if ($path->company != $this->companyid) {
                print_error('companymismatch', 'local_iomad_learningpath');
            }

            return $path;
        } else {
            if (!$create) {
                print_error('nopath', 'local_iomad_learningpath');
            }
            $path = new \stdClass;
            $path->company = $this->companyid;
            $path->timecreated = time();
            $path->timeupdated = time();
            $path->name = '';
            $path->description = '';
            $path->active = 0;

            return $path;
        }
    }

    /**
     * Get/check group
     * @param int $pathid
     * @param int $groupid
     * @return object
     */
    public function get_group($pathid, $groupid) {
        global $DB;

        if ($groupid) {

            // Enforce the pathid even though just the id will do.
            $group = $DB->get_record('iomad_learningpathgroup', ['learningpath' => $pathid, 'id' => $groupid], '*', MUST_EXIST);
            return $group;
        } else {
            $group = new \stdClass;
            $group->learningpath = $pathid;
            $group->name = get_string('untitledgroup', 'local_iomad_learningpath');
            $group->sequence = 0;

            return $group;
        }
    }

    /**
     * Check path has at least one group.
     * if not, create a default group and add all the courses
     * @param int $pathid
     */
    public function check_group($pathid) {
        global $DB;

        if (!$DB->count_records('iomad_learningpathgroup', ['learningpath' => $pathid])) {
            $group = $this->get_group($pathid, 0);
            $groupid = $DB->insert_record('iomad_learningpathgroup', $group);
            if ($courses = $DB->get_records('iomad_learningpathcourse', ['path' => $pathid])) {
                foreach ($courses as $course) {
                    $course->groupid = $groupid;
                    $DB->update_record('iomad_learningpathcourse', $course);
                }
            }
        }
    }

    /**
     * Delete group
     * @param int $pathid
     * @param int $groupid
     */
    public function delete_group($pathid, $groupid) {
        global $DB;

        // Remove group courses from LP
        $DB->delete_records('iomad_learningpathcourse', ['path' => $pathid, 'groupid' => $groupid]);

        // Remove group
        $DB->delete_records('iomad_learningpathgroup', ['learningpath' => $pathid, 'id' => $groupid]);
    }

    /**
     * Take image uploaded on learning path form and
     * process for size and thumbnail
     * @param object $context
     * @param int $id learning path id
     */
    public function process_image($context, $id) {
        global $CFG;

        // Get file storage
        $fs = get_file_storage();

        // find the files
        $files = $fs->get_area_files($context->id, 'local_iomad_learningpath', 'picture', $id);
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }

            // Process main picture
            $picture = $file->resize_image(null, 150);

            // store mainpicture
            if ($oldfile = $fs->get_file($context->id, 'local_iomad_learningpath', 'mainpicture', $id, '/', 'picture.png')) {
                $oldfile->delete();
            }
            $fileinfo = [
                'contextid' => $context->id,
                'component' => 'local_iomad_learningpath',
                'filearea' => 'mainpicture',
                'itemid' => $id,
                'filepath' => '/',
                'filename' => 'picture.png',
            ];
            $fs->create_file_from_string($fileinfo, $picture);

            // Process thumbnail
            $thumb = $file->resize_image(null, 50);

            // store thumbnail
            if ($oldfile = $fs->get_file($context->id, 'local_iomad_learningpath', 'thumbnail', $id, '/', 'thumbnail.png')) {
                $oldfile->delete();
            }
            $fileinfo = [
                'contextid' => $context->id,
                'component' => 'local_iomad_learningpath',
                'filearea' => 'thumbnail',
                'itemid' => $id,
                'filepath' => '/',
                'filename' => 'thumbnail.png',
            ];
            $fs->create_file_from_string($fileinfo, $thumb);
        }
    }

    /**
     * Set breadcrumb correctly for learning paths admin
     * @param string $linktext (optional) final link
     * @param moodle_url $linkurl (optional) final link
     */
    public function breadcrumb($linktext = '', $linkurl = null) {
        global $PAGE;

        $PAGE->navbar->ignore_active();
        $PAGE->navbar->add(get_string('administrationsite'));
        $PAGE->navbar->add(get_string('dashboard', 'block_iomad_company_admin'), new \moodle_url('/my'));
        $PAGE->navbar->add(get_string('managetitle', 'local_iomad_learningpath'), new \moodle_url('/local/iomad_learningpath/manage.php'));
        if ($linktext) {
            $PAGE->navbar->add($linktext, $linkurl);
        }
    }

    /**
     * Get course image url
     * @param int $courseid
     * @return mixed url or false if no image
     */
    public function get_course_image_url($courseid) {
        global $OUTPUT;

        $fs = get_file_storage();

        $context = \context_course::instance($courseid);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0);
        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                return \moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    null, $file->get_filepath(), $file->get_filename())->out();
            }
        }

        // No image defined, so...
        return $OUTPUT->image_url('courseimage', 'block_iomad_learningpath')->out();
    }

    /**
     * Get course list for given path
     * @param int $pathid
     * @param int $groupid (0 = all)
     * @param bool $idonly just return course ids if set
     * @return array
     */
    public function get_courselist($pathid, $groupid = 0, $idonly = false) {
        global $DB;

        $sql = 'SELECT c.id courseid, c.shortname shortname, c.fullname fullname, lpc.*
            FROM {iomad_learningpathcourse} lpc JOIN {course} c ON lpc.course = c.id
            WHERE lpc.path = :pathid ';
        $params = ['pathid' => $pathid];
        if ($groupid) {
            $sql .= 'AND lpc.groupid = :groupid ';
            $params['groupid'] = $groupid;
        }
        $sql .= 'ORDER BY lpc.groupid, lpc.sequence';

        $courses = $DB->get_records_sql($sql, $params);

        // ID only?
        if ($idonly) {
            return array_keys($courses);
        }

        // Add images and groupid
        foreach ($courses as $course) {
            $course->image = $this->get_course_image_url($course->courseid);
            $course->groupid = $groupid;
        }

        return $courses;
    }

    /**
     * Get display courselist
     * List of groups (if there are any) and their courses
     * @param int $pathid
     * @return array
     */
    public function get_display_courselist($pathid) {
        global $DB;

        $groups = $DB->get_records('iomad_learningpathgroup', ['learningpath' => $pathid]);
        foreach ($groups as $group) {
            if ($group->sequence) {
                $group->name = get_string('groupnamesequential', 'local_iomad_learningpath', $group->name);
            }
            $group->courses = $this->get_courselist($pathid, $group->id);
        }

        return $groups;
    }

    /**
     * Get prospective course list for company
     * @param int $pathid
     * @param string $filter
     * @param int $category (course category)
     * @return array of courses
     */
    public function get_prospective_courses($pathid, $filter = '', $category = 0, $programlicenseid = 0) {
        global $DB;

        // Get currently selected courses
        $selectedcourses = $this->get_courselist($pathid, 0, true);

        $topdepartment = company::get_company_parentnode($this->companyid);
        $depcourses = company::get_recursive_department_courses($topdepartment->id);

        $courses = array();
        $categories = array();
        foreach ($depcourses as $depcourse) {

            // Get full course object
            if (!$course = $DB->get_record('course', ['id' => $depcourse->courseid])) {
                throw new \coding_exception('No course record found for courseid = ' . $depcourse->courseid);
            }

            // Collect categories regardless of selection
            $categories[$course->category] = $course->category;

            // Do not include courses already selected
            if (in_array($depcourse->courseid, $selectedcourses)) {
                continue;
            }

            // Do not include courses NOT in the selected category
            if ($category) {
                if ($course->category != $category) {
                    continue;
                }
            }

            // Do not include courses NOT in selected license.
            if ($programlicenseid) {
                if (!$DB->get_record('companylicense_courses', array('id' => $programlicenseid, 'courseid' => $course->id))) {
                    continue;
                }
            }

            // Apply filter (if specified).
            if ($filter && (stripos($course->fullname, $filter) === false)) {
                continue;
            }

            $course->image = $this->get_course_image_url($course->id);
            $courses[$course->id] = $course;
        }
        $this->categories = $categories;

        return $courses;
    }

    /**
     * Return course categories used
     * @param int $pathid
     * @return array
     */
    public function get_categories($pathid) {
        global $DB;

        // Check if categories have been collected
        if (!$this->categories) {
            $this->get_prospective_courses($pathid);
        }

        // loop over categories and get full(er) information.
        $cat0 = (object)['id' => 0, 'name' => get_string('all')];
        $cats = [0 => $cat0];
        foreach ($this->categories as $categoryid) {
            $cat = new \stdClass;
            $coursecategory = $DB->get_record('course_categories', ['id' => $categoryid], '*', MUST_EXIST);
            $cat->id = $coursecategory->id;
            $cat->name = $coursecategory->name;
            $cats[$categoryid] = $cat;
        }

        return $cats;
    }

    /**
     * Return company program licenses.
     * @param int $pathid
     * @return array
     */
    public function get_programlicenses($pathid) {
        global $DB;

        $programlicenses = $DB->get_records('companylicense', array('companyid' => $this->companyid, 'program' => 1));
        $path = $DB->get_record('iomad_learningpath', array('id' => $pathid));

        // loop over licenses and get full(er) information.
        $program0 = (object)['id' => 0, 'name' => get_string('none')];
        if (empty($path->licenseid)) {
            $program0->selected = "selected";
        } else {
            $program0->selected = "";
        }
        $programs = [0 => $program0];
        foreach ($programlicenses as $programlicense) {
            $license = new \stdClass;
            $license->id = $programlicense->id;
            $license->name = $programlicense->name;
            if ($path->licenseid == $programlicense->id) {
                $license->selected = "selected";
            } else {
                $license->selected = "";
            }
            $programs[$license->id] = $license;
        }

        return $programs;
    }

    /**
     * Add courses to path
     * @param int pathid
     * @param array $courseids
     * @param int $groupid (0 = add to first group)
     */
    public function add_courses($pathid, $courseids, $groupid = 0) {
        global $DB;

        // Make sure we only add courses in the prospective list.
        $allcourses = $this->get_prospective_courses($pathid);

        // Get existing list
        $count = $DB->count_records('iomad_learningpathcourse', ['path' => $pathid]);

        // Check/get groupid
        if ($groupid) {
            $group = $DB->get_record('iomad_learningpathgroup', ['learningpath' => $pathid, 'id' => $groupid], '*', MUST_EXIST);
        } else {
            $groups = $DB->get_records('iomad_learningpathgroup', ['learningpath' => $pathid]);
            if (!$group = reset($groups)) {
                throw new \Exception('No groups for learning path id = ' . $pathid);
            }
        }

        // Work through courses.
        foreach ($courseids as $courseid) {

            // Double clicking can try to add the same course twice.
            if (!array_key_exists($courseid, $allcourses)) {
                continue;
            }

            // If course already in the list then just skip it
            if ($course = $DB->get_record('iomad_learningpathcourse', ['path' => $pathid, 'course' => $courseid])) {
                continue;
            }

            // Add at the end
            $count++;
            $course = new \stdClass;
            $course->path = $pathid;
            $course->course = $courseid;
            $course->sequence = $count;
            $course->groupid = $group->id;
            $DB->insert_record('iomad_learningpathcourse', $course);
        }
    }

    /**
     * Remove courses from path
     * @param int $pathid
     * @param array $courseids
     */
    public function remove_courses($pathid, $courseids) {
        global $DB;

        // Work through courses.
        foreach ($courseids as $courseid) {
            $DB->delete_records('iomad_learningpathcourse', ['course' => $courseid, 'path' => $pathid]);
        }

        // Fix the sequence
        $this->fix_sequence($pathid);
    }

    /**
     * Fixup the sequence values in path
     * Used if one (or more) has been deleted
     * @param int $pathid
     */
    public function fix_sequence($pathid) {
        global $DB;

        $courses = $DB->get_records('iomad_learningpathcourse', ['path' => $pathid], 'sequence ASC');
        $count = 1;
        foreach ($courses as $course) {
            $course->sequence = $count;
            $DB->update_record('iomad_learningpathcourse', $course);
            $count++;
        }
    }

    /**
     * Delete a path
     * @param int $pathid
     */
    public function deletepath($pathid) {
        global $DB;

        // Delete the users.
        $users = $this->get_users($pathid, true);
        if (!empty($users)) {
            $this->delete_users($pathid, $users);
        }

        // Delete courses from path
        $DB->delete_records('iomad_learningpathcourse', ['path' => $pathid]);

        // Delete image from path (if any)
        $fs = get_file_storage();
        if ($oldfile = $fs->get_file($this->context->id, 'local_iomad_learningpath', 'mainpicture', $pathid, '/', 'picture.png')) {
            $oldfile->delete();
        }
        if ($oldfile = $fs->get_file($this->context->id, 'local_iomad_learningpath', 'thumbnail', $pathid, '/', 'thumbnail.png')) {
            $oldfile->delete();
        }

        // Delete path itself
        $DB->delete_records('iomad_learningpath', ['id' => $pathid]);
    }

    /**
     * Copy a path
     * @param int $pathid
     */
    public function copypath($pathid) {
        global $DB;

        // Get original path
        $path = $DB->get_record('iomad_learningpath', ['id' => $pathid], '*', MUST_EXIST);

        // work out what new name will be
        $count = 1;
        while ($DB->get_record('iomad_learningpath', ['name' => $path->name . " Copy $count"])) {
            $count++;
            if ($count >= 9999) {
                throw new \coding_exception('countlimit', 'Failed to find new name for path');
            }
        }
        $newname = $path->name . " Copy $count";

        // Create new path
        $newpath = new \stdClass;
        $newpath->company = $path->company;
        $newpath->name = $newname;
        $newpath->description = $path->description;
        $newpath->active = false;
        $newpath->timecreated = time();
        $newpath->timeupdated = time();
        $newpathid = $DB->insert_record('iomad_learningpath', $newpath);

        // Copy images
        $fs = get_file_storage();
        if ($picture = $fs->get_file($this->context->id, 'local_iomad_learningpath', 'mainpicture', $pathid, '/', 'picture.png')) {
            $fileinfo = [
                'contextid' => $this->context->id,
                'component' => 'local_iomad_learningpath',
                'filearea' => 'mainpicture',
                'itemid' => $newpathid,
                'filepath' => '/',
                'filename' => 'picture.png',
            ];
            $fs->create_file_from_storedfile($fileinfo, $picture);
        }
        if ($thumbnail = $fs->get_file($this->context->id, 'local_iomad_learningpath', 'thumbnail', $pathid, '/', 'thumbnail.png')) {
            $fileinfo = [
                'contextid' => $this->context->id,
                'component' => 'local_iomad_learningpath',
                'filearea' => 'thumbnail',
                'itemid' => $newpathid,
                'filepath' => '/',
                'filename' => 'thumbnail.png',
            ];
            $fs->create_file_from_storedfile($fileinfo, $thumbnail);
        }

        // Copy groups.
        $groups = $DB->get_records('iomad_learningpathgroup', ['learningpath' => $pathid]);
        foreach ($groups as $group) {
            $group->learningpath = $newpathid;
            $group->newid = $DB->insert_record('iomad_learningpathgroup', $group);
        }

        // Copy courses
        $courses = $DB->get_records('iomad_learningpathcourse', ['path' => $pathid]);
        foreach ($courses as $course) {
            $course->path = $newpathid;
            $course->groupid = $groups[$course->groupid]->newid;
            $DB->insert_record('iomad_learningpathcourse', $course);
        }

        // Copy students over
        $pathusers = $DB->get_records('iomad_learningpathuser', ['pathid' => $pathid]);
        foreach ($pathusers as $pathuser) {
            $pathuser->pathid = $newpathid;
            $DB->insert_record('iomad_learningpathuser', $pathuser);
        }
    }

    /**
     * Get students assigned to a path
     * @param int $pathid
     * @param bool idonly just give us the ids
     * @return array
     */
    public function get_users($pathid, $idonly = false) {
        global $DB;

        $sql = "SELECT u.*
            FROM {user} u JOIN {iomad_learningpathuser} lpu ON lpu.userid = u.id
            WHERE u.deleted = 0
            AND u.suspended = 0
            AND lpu.pathid = :pathid
            ORDER BY u.lastname, u.firstname ASC";
        $users = $DB->get_records_sql($sql, ['pathid' => $pathid]);
        if ($idonly) {
            return array_keys($users);
        }

        // Adjust for fullname
        foreach ($users as $user) {
            $user->fullname = fullname($user);
        }

        return $users;
    }

    /**
     * Get prospective users
     * @param string $filter
     * @param array $excludeids
     * @return array of objects
     */
    public function get_prospective_users($pathid, $filter, $profilefieldid = 0) {
        global $DB;

        // Set up some defaults for the SQL.
        $companyprofjoin = "";
        $sqlparams = array('companyid' => $this->companyid);

        // Did we get passed anything to filter?
        if (!empty($filter)) {
            if (!empty($profilefieldid)) {
                $companyprofjoin = "LEFT JOIN {user_info_data} uid ON (u.id = uid.userid AND uid.fieldid = :profilefieldid)";
                $filtersql = " AND " . $DB->sql_like("uid.data", ':profsearch', false, false);
                $sqlparams['profilefieldid'] = $profilefieldid;
                $sqlparams['profsearch'] = "%".$filter."%"; 
            } else {
                $filtersql = " AND (
                             " . $DB->sql_like("u.firstname", ':firstname', false, false) . "
                              OR " . $DB->sql_like("u.lastname", ':lastname', false, false) . "
                              OR " . $DB->sql_like("u.email", ':email', false, false) . "
                              )";
            $sqlparams['firstname'] = "%" . $filter . "%";
            $sqlparams['lastname'] = "%" . $filter . "%";
            $sqlparams['email'] = "%" . $filter . "%";

            }
        } else {
            $filtersql = "";
        }

        // Get any users who are already assigned to the learning path.
        $excludeids = $this->get_users($pathid, true);
        if (!empty($excludeids)) {
            // Add SQL to remove them from the list.
            $excludesql = " AND u.id NOT IN (" . implode(',', array_keys($excludeids)) . ")";

        } else {
            $excludesql = "";
        }



        // Build the SQL.
        $sql = "SELECT DISTINCT u.*
            FROM {user} u JOIN {company_users} cu ON cu.userid = u.id
            $companyprofjoin
            WHERE u.deleted = 0
            AND u.suspended = 0
            AND cu.companyid = :companyid
            $excludesql
            $filtersql
            ORDER BY u.lastname, u.firstname ASC";

        // Get the users.
        $allusers = $DB->get_records_sql($sql, $sqlparams);

        // Build the return array.
        $users = [];
        foreach ($allusers as $user) {
            $user->fullname = fullname($user);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Add users to path
     * @param int $pathid
     * @param array $userids
     */
    public function add_users($pathid, $userids) {
        global $DB;

        foreach ($userids as $userid) {

            // Check userid is really in this company
            if (!$companyuser = $DB->get_record('company_users', ['companyid' => $this->companyid, 'userid' => $userid])) {
                throw new \coding_exception('invaliduserid', 'User is not a member of current company - id = ' . $userid);
            }

            // Is the userid already in the path
            if ($user = $DB->get_record('iomad_learningpathuser', ['pathid' => $pathid, 'userid' => $userid])) {
                continue;
            }

            // Add a new record
            $user = new \stdClass;
            $user->pathid = $pathid;
            $user->userid = $userid;
            $DB->insert_record('iomad_learningpathuser', $user);
        }

        return true;
    }

    /**
     * Delete users from path
     * @param int $pathid
     * @param array $userids
     */
    public function delete_users($pathid, $userids) {
        global $DB;

        foreach ($userids as $userid) {

            // Check userid is really in this company
            if (!$companyuser = $DB->get_record('company_users', ['companyid' => $this->companyid, 'userid' => $userid])) {
                throw new \coding_exception('invaliduserid', 'User is not a member of current company - id = ' . $userid);
            }

            $DB->delete_records('iomad_learningpathuser', ['pathid' => $pathid, 'userid' => $userid]);
        }

        return true;
    }

    /**
     * Assign license to plan.
     * @param int $pathid
     * @param int $licenseid
     * @return boolean.
     */
    public function assign_license_to_plan($pathid, $licenseid) {
        global $DB;

        $path = $DB->get_record('iomad_learningpath', array('id' => $pathid));

        // If we are removing a license 
        if (($licenseid == 0 && !empty($path->licenseid)) || $path->licenseid != $licenseid) {
            // Remove the courses from the learning path.
            if ($courses = $DB->get_records('iomad_learningpathcourse', array('path' => $pathid), 'course', 'course')) {
                self::remove_courses($pathid, array_keys($courses));
            }
        }

        // Are we adding a license?
        if (!empty($licenseid) && $path->licenseid != $licenseid) {
            // Get the license courses.
            if ($newcourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), 'courseid', 'courseid')) {
                self::add_courses($pathid, array_keys($newcourses));
            }
        }

        // Update the path.
        $DB->set_field('iomad_learningpath', 'licenseid', $licenseid, array('id' => $pathid));
    }

    /** Events **/

    /**
     * Triggered via company_license_deleted event.
     *
     * @param \block_iomad_company_user\event\company_license_deleted $event
     * @return bool true on success.
     */
    public static function company_license_deleted(\block_iomad_company_admin\event\company_license_deleted $event) {
        global $DB, $CFG;

        $licenseid = $event->other['licenseid'];

        if (!$licenserec = $DB->get_record('companylicense', array('id' => $licenseid))) {
            // Do nothing.
            return;
        }

        if (!$company = $DB->get_record('company', array('id' => $licenserec->companyid))) {
            // Do nothing.
            return;
        }

        // Check if this license is tied to a path.
        if (!$path = $DB->get_record('iomad_learningpath', array('licenseid' => $licenseid))) {
            return;
        }

        $companypath = new companypaths($company->id, \context_system::instance());

        $companypath->deletepath($path->id);
        return true;
    }

    /**
     * Triggered via company_license_updated event.
     *
     * @param \block_iomad_company_user\event\company_license_updated $event
     * @return bool true on success.
     */
    public static function company_license_updated(\block_iomad_company_admin\event\company_license_updated $event) {
        global $DB, $CFG;

        $licenseid = $event->other['licenseid'];

        if (!$licenserec = $DB->get_record('companylicense', array('id' => $licenseid))) {
            // Do nothing.
            return;
        }

        if (!$company = $DB->get_record('company', array('id' => $licenserec->companyid))) {
            // Do nothing.
            return;
        }

        // Check if this license is tied to a path.
        if (!$path = $DB->get_record('iomad_learningpath', array('licenseid' => $licenseid))) {
            return;
        }

        $companypath = new companypaths($company->id, \context_system::instance());

        if (!empty($licenserecord->program)) {
            // This is a program of courses.
            // If it's been updated we need to deal with any course changes.
            $currentcourses = $DB->get_records('companylicense_courses', array('licenseid' => $licenseid), null, 'courseid');
            $oldcourses = (array) json_decode($event->other['oldcourses'], true);

            // Check for courses which have been removed.
            foreach ($oldcourses as $oldcourse) {
                $oldcourseid = $oldcourse['courseid'];
                if (empty($currentcourses[$oldcourseid])) {
                    $companypath->remove_courses($pathid, array($oldcourseid));
                }
            }

            // Check for new courses added.
            foreach ($currentcourses as $currentcourse) {
                $currcourseid = $currentcourse->courseid;
                if (empty($oldcourses[$currcourseid])) {
                    $companypath->add_courses($pathid, array($currentcourseid));
                }
            }
        }

        return true;
    }

    /**
     * Triggered via user_license_assigned event.
     *
     * @param \block_iomad_company_user\event\user_license_assigned $event
     * @return bool true on success.
     */
    public static function user_license_assigned(\block_iomad_company_admin\event\user_license_assigned $event) {
        global $DB, $CFG;

        $userid = $event->userid;
        $userlicid = $event->objectid;
        $licenseid = $event->other['licenseid'];

        if (!$licenserec = $DB->get_record('companylicense', array('id' => $licenseid))) {
            // Do nothing.
            return;
        }

        if (!$company = $DB->get_record('company', array('id' => $licenserec->companyid))) {
            // Do nothing.
            return;
        }

        // Check if this license is tied to a path.
        if (!$path = $DB->get_record('iomad_learningpath', array('licenseid' => $licenseid))) {
            return;
        }

        $companypath = new companypaths($company->id, \context_system::instance());

        // If so, add this user to the path.
        $companypath->add_users($path->id, array($userid));

        return true;
    }

    /**
     * Triggered via user_license_unassigned event.
     *
     * @param \block_iomad_company_user\event\user_license_unassigned $event
     * @return bool true on success.
     */
    public static function user_license_unassigned(\block_iomad_company_admin\event\user_license_unassigned $event) {
        global $DB, $CFG;

        $userid = $event->userid;
        $userlicid = $event->objectid;
        $licenseid = $event->other['licenseid'];

        if (!$licenserec = $DB->get_record('companylicense', array('id' => $licenseid))) {
            // Do nothing.
            return;
        }

        if (!$company = $DB->get_record('company', array('id' => $licenserec->companyid))) {
            // Do nothing.
            return;
        }

        // Check if this license is tied to a path.
        if (!$path = $DB->get_record('iomad_learningpath', array('licenseid' => $licenseid))) {
            return;
        }

        $companypath = new companypaths($company->id, \context_system::instance());

        // If so, remove this user from the path.
        $companypath->delete_users($path->id, array($userid));

        return true;
    }

}
