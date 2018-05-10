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
 * Gnerate site class
 *
 * @package tool_iomadsite
 * @copyright 2018 Howard Miller
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_iomadsite;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/iomad/lib/company.php');
require_once($CFG->dirroot . '/local/iomad/lib/user.php');
require_once($CFG->dirroot . '/course/lib.php');

class generate {

    protected $companynames = [
        'Acme' => 'Acme Corporation',
        'Globex' => 'Globex Corporation',
        'Soylent' => 'Soylent Corporation',
        'Initech' => 'Initech',
        'Umbrella' => 'Umbrella Corporation',
        'Hooli' => 'Hooli',
        'Vehement' => 'Vehement Capital Partners',
        'Massive' => 'Massive Dynamic',
    ];

    protected $citynames = [
        'Angel Grove',
        'Cabot Cove',
        'Mayberry',
        'Sunnydale',
        'Ambridge',
        'Landmark City',
        'Aberdale',
        'Danville',
        'Elmore',
        'Bedrock',
        'Springfield',
        'Quahog',
        'Castle Rock',
        'Hogsmeade',
        'Los Santos',
        'Waterdeep',
    ];

    protected $coursepre = [
        'Counter',
        'Audiology',
        'Planetary',
        'Foreign',
        'Siege',
        'Alien',
        'Earth',
        'Military',
        'Space',
        'Raid',
        'Self Defence',
        'Enhanced',
        'Small Forces',
        'Speech',
        'Dead Language',
        'Eurythmic',
        'Life',
        'Magic',
        'Physical',
        'Mount',
        'Disaster',
        'Stealth',
        'Ward',
    ];

    protected $coursepost = [
        'Social Sciences',
        'Literature',
        'Religion',
        'Handwriting',
        'Dialects',
        'Disaster Management',
        'Horse Riding',
        'Forensic Science',
        'Pathology',
        'Ethics',
        'Biology',
        'Language History',
        'Language Culture',
        'Arts',
        'Evolutionary Biology',
        'Drama',
        'History',
        'Strategy',
        'Psychology',
        'Finance',
        'Speech',
        'Linguistics',
        'Practice',
        'Healthcare Practice',
        'Creation',
        'Music',
        'Diplomacy',
        'Resource Management',
        'Nutrition',
        'Tactics',
        'Spellcasting',
    ];

    protected $courseextra = [
        'Advanced',
        'Further',
        'Second Year',
        'First Year',
        'Third Year',
        'Begginers',
        'Elementary',
        'An Introduction To',
        'Studies in',
    ];

    protected $firstnames;

    protected $lastnames;

    public function __construct() {
        global $CFG;

        require_once($CFG->dirroot . '/admin/tool/iomadsite/firstnames.php');
        require_once($CFG->dirroot . '/admin/tool/iomadsite/lastnames.php');
        $this->firstnames = $firstnames;
        $this->lastnames = $lastnames;
    }

    /**
     * Make course name
     * @return array(shortname, fullname) 
     */
    protected function invent_coursename() {
 
        if (rand(0,10) < 4) {
            $extra = $this->courseextra[array_rand($this->courseextra,1)] . ' ';
        } else {
            $extra = '';
        }
        $coursepre = $this->coursepre[array_rand($this->coursepre,1)];
        $coursepost = $this->coursepost[array_rand($this->coursepost,1)];
        $fullname = $extra . $coursepre . ' ' . $coursepost;
        $shortname = substr($coursepre, 0, 1) . substr($coursepost, 0, 1) . rand(10000, 99999);

        return [$shortname, $fullname];
    }

    /**
     * Make company category
     * @param string $fullname
     * @return int category id
     */
    protected function company_category($fullname) {
        global $DB;

        $coursecat = new \stdclass();
        $coursecat->name = $fullname;
        $coursecat->sortorder = 999;
        $coursecat->id = $DB->insert_record('course_categories', $coursecat);
        $coursecat->context = \context_coursecat::instance($coursecat->id);
        $categorycontext = $coursecat->context;
        $categorycontext->mark_dirty();
        $DB->update_record('course_categories', $coursecat);
        fix_course_sortorder();

        return $coursecat->id;
    }

    /**
     * Make profile for this company
     * @param string $shortname
     * @return int profile id
     */
    protected function company_profile($shortname) {
        global $DB;

        $catdata = new \stdclass();
        $catdata->sortorder = $DB->count_records('user_info_category') + 1;
        $catdata->name = $shortname;
        $profileid = $DB->insert_record('user_info_category', $catdata, false);

        return $profileid;
    }

    /**
     * Create company record
     * @param string $shortname
     * @param string $fullname 
     * @return object record
     */
    protected function company_record($shortname, $fullname) {
        global $DB;

        $company = new \stdClass();
        $company->name = $fullname;
        $company->shortname = $shortname;
        $company->city = $this->citynames[array_rand($this->citynames,1)];
        $company->country = 'GB';
        $company->maildisplay = 0;
        $company->mailformat = 1;
        $company->maildigest = 0;
        $company->autosubscribe = 1;
        $company->trackforums = 0;
        $company->htmleditor = 1;
        $company->screenreader = 0;
        $company->timezone = 99;
        $company->lang = 'en';
        $company->theme = 'iomadboost';
        $company->category = $this->company_category($fullname);
        $company->profileid = $this->company_profile($shortname);
        $company->suspended = 0;
        $company->emailprofileid = 0;
        $company->supervisorprofileid = 0;
        $company->managernotify = 0;
        $company->parentid = 0;
        $company->ecommerce = 0;
        $company->managerdigestday = 0;
        $company->previousroletemplateid = 0;

        $companyid = $DB->insert_record('company', $company);
        $company = $DB->get_record('company', ['id' => $companyid]);

        \company::initialise_departments($companyid);

        echo "<p>Created company '$fullname'</p>\n";

        return $company;
    }

    /**
     * Add random courses to a company
     * @param object $company
     */
    public function courses($company) {

        // Iomad company object.
        $comp = new \company($company->id);
        
        // Add a random number of courses
        $howmany = rand(10, 25);
        for ($i=0; $i < $howmany; $i++) {
            list($shortname, $fullname) = $this->invent_coursename();
            $data = new \stdClass();
            $data->fullname = $fullname;
            $data->shortname = $shortname;
            $data->category = $company->category;
            $course = create_course($data);
            $comp->add_course($course, 0, true);
           
            echo "<p>Created course '$fullname'</p>\n";

            // Add some users
            $this->users($company, $shortname);
        }
    }

    /**
     * Create random user
     * @param int $companyid
     * @param int $courseid;
     */
    protected function create_user($companyid, $courseid) {
        $firstname = $this->firstnames[array_rand($this->firstnames, 1)];
        $lastname = $this->lastnames[array_rand($this->lastnames, 1)];
        $email = $firstname . '.' . $lastname . '.' . rand(1000,9999) . '@example.com';
        
        // data object for user details
        $data = new \stdClass;
        $data->firstname = $firstname;
        $data->lastname = $lastname;
        $data->email = $email;
        $data->use_email_as_username = 0;
        $data->sendnewpasswordemails = 0;
        $data->preference_auth_forcepasswordchange = 0;
        $data->newpassword = 'Aa*12345678';
        $data->companyid = $companyid;
        $data->selectedcourses = [];
        \company_user::create($data);
    }

    /**
     * Create users for course
     * @param object $company
     * @param string $shortname (of course)
     */
    public function users($company, $shortname) {
        global $DB;

        $course = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
        $howmany = rand(10, 40);
        for ($i=1; $i < $howmany; $i++) {
            $this->create_user($company->id, $course->id);
        }
    }

    /**
     * Create the companies
     */
    public function companies() {
        global $DB;

        foreach ($this->companynames as $shortname => $fullname) {

            // Make sure it doesn't already exist.
            if (!$company = $DB->get_record('company', ['shortname' => $shortname])) {
                $company = $this->company_record($shortname, $fullname);
            }
            $this->courses($company);
        }
    }

}
