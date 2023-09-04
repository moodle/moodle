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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p1;

use DateTime;
use context_user;
use core_course_category;
use core_php_time_limit;
use core_user;
use enrol_oneroster\client as client_base;
use enrol_oneroster\local\converter;

// Client and associated features.
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;

// Entities which represent Moodle objects.
use enrol_oneroster\local\interfaces\course_representation;
use enrol_oneroster\local\interfaces\coursecat_representation;
use enrol_oneroster\local\interfaces\user_representation;
use enrol_oneroster\local\interfaces\enrollment_representation;

use enrol_oneroster\local\collections\orgs as orgs_collection;
use enrol_oneroster\local\collections\schools as schools_collection;
use enrol_oneroster\local\collections\terms as terms_collection;
use enrol_oneroster\local\v1p1\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\entities\org as org_entity;
use enrol_oneroster\local\entities\school as school_entity;
use enrol_oneroster\local\entities\user as user_entity;
use moodle_url;
use progress_trace;
use stdClass;
require_once($CFG->dirroot.'/group/lib.php');

/**
 * One Roster v1p1 client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait oneroster_client {

    /** @var array List of the entries which have been recently modified to reduce churn */
    protected $modifiedentities = [];

    /** @var stdClass[] List of existing enrolment instances for the plugin */
    protected $instances = [];

    /** @var int[] List of user idnumber => userid */
    protected $usermappings = null;

    /** @var stdClass List of mappings from One Roster role name to Moodle Role ID */
    protected $rolemappings = null;

    /** @var array Plugin configuration */
    protected $pluginconfig = null;

    /** @var array List of applicable context levels for each role */
    protected $rolecontextlevels = null;

    /** @var array List of existing role assignmenets */
    protected $existingroleassignments = [];

    /** @var array List of tracking metrics */
    protected $metrics = [];

    /** @var array List of Qubits mapped courses */
    protected $qbs_mcourses = [];

    /** @var array List of Qubits mapped groups */
    protected $qbs_mgroups = [];


    /**
     * Get the Base URL for this One Roster API version.
     *
     * @param string $server The hostname
     * @return moodle_url
     */
    protected function get_base_url(string $server): moodle_url {
        // As per https://www.imsglobal.org/oneroster-v11-final-specification#_Toc480451989
        // The API Root URL MUST be /ims/oneroster.
        //
        // To allow further versions of the specification to exist in a controlled manner, the new version number MUST be '/v1p1'.
        return new moodle_url("{$server}/ims/oneroster/v1p1");
    }

    /**
     * Get all of the scopes required for this OAuth2 Implementation.
     *
     * @return string[]
     */
    protected function get_all_scopes(): array {
        return array_merge(
            rostering_endpoint::get_required_scopes()
        );
    }

    /**
     * Get the rostering endpoint for this version of the API.
     *
     * @return rostering
     */
    public function get_rostering_endpoint(): rostering_endpoint_interface {
        return new rostering_endpoint($this->get_container());
    }

    /**
     * Get the entity factory for this One Roster implementation.
     *
     * @return  container_interface
     */
    public function get_container(): container_interface {
        if ($this->container === null) {
            $this->container = new container($this);
        }

        return $this->container;
    }

    /**
     * Sync the roster.
     *
     * @param   int $onlysincetime
     */
    public function sync_roster(?int $onlysincetime = null): void {
        global $DB;

        // Most systems do not have many organisations in them.
        // Fetch all organisations to add them to the cache.
        $this->fetch_organisation_list();

        $schoolidstosync = explode(',', get_config('enrol_oneroster', 'datasync_schools'));
        $countofschools = count($schoolidstosync);

        $this->get_trace()->output("Processing {$countofschools} schools");

        $onlysince = null;
        if ($onlysincetime) {
            // Only fetch users last modified in the onlysince period.
            $onlysince = new DateTime();
            $onlysince->setTimestamp($onlysincetime);
        }

        // Synchronise all users.
        // One Roster does not provide a way of fetching users relating to a specific school.
        // All users for all supported schools will be created first.
        $this->get_trace()->output("Updating the user roster", 1);

        // Only fetch users last modified in the past day.
        // All timezones in One Roster are Zulu.
        $this->sync_users_in_schools($schoolidstosync, $onlysince);

        // Fetch the details of all enrolment instances before running the sync.
        $this->cache_enrolment_instances();

        $this->fetch_current_enrolment_data();

        // Synchronise all courses, classes, and enrolments.
        // Qubits start
        foreach ($schoolidstosync as $schoolidtosync) {
            $this->get_trace()->output("Fetching school with sourcedId '{$schoolidtosync}'", 2);
            $school = $this->get_container()->get_entity_factory()->fetch_org_by_id($schoolidtosync);
            if ($school instanceof school_entity) {
                $this->get_trace()->output("Synchronising school '{$schoolidtosync}'", 2);
                $this->sync_school($school, $onlysince);
            } else {
                $this->get_trace()->output("Organisation with sourcedId '{$schoolidtosync}' is not a school. Skipping.", 3);
            }
        }


       /* $this->get_trace()->output("Processing unenrolments", 3);
        foreach ($this->existingroleassignments as $instanceid => $ra) {
            $instance = $DB->get_record('enrol', ['id' => $instanceid]);
            if ($instance === null) {
                $this->get_trace()->output("No enrolment instance found with id {$instanceid}");
                continue;
            }

            $context = \context_course::instance($instance->courseid);

            // Unassign roles for this user.
            foreach ($ra as $userid => $roleids) {
                foreach (array_keys($roleids) as $roleid) {
                    if ($roleid) {
                        role_unassign($roleid, $userid, $context->id, 'enrol_oneroster', $instance->id);
                    }
                }
            }

            // Unenrol the user if they have no remaining roles in this enrolment instance.
            // Note: A manual enrolment in the same course is a separate instance.
            $this->get_plugin_instance()->unenrol_user(
                $instance,
                $userid
            );
        } */
        // Qubits end

        $this->get_trace()->output("Completed synchronisation of Rostering information");
        $this->get_trace()->output(sprintf("Entity\t\tCreate\tUpdate\tDelete"), 1);
        foreach ($this->get_metrics() as $thing => $actions) {
            $this->get_trace()->output(
                sprintf(
                    "Entity '%s'\t%d\t%d\t%d",
                    $thing,
                    $actions['create'],
                    $actions['update'],
                    $actions['delete']
                ),
                1
            );
        }
    }

    /**
     * Fetch current enrolment data into memory for later operations.
     */
    protected function fetch_current_enrolment_data(): void {
        global $DB;

        $sql = <<<EOF
      SELECT
            e.id AS enrolid,
            ue.id AS ueid,
            ue.userid AS userid,
            ra.roleid
        FROM {user_enrolments} ue
        JOIN {enrol} e ON ue.enrolid = e.id
   LEFT JOIN {role_assignments} ra ON ra.component = :component AND ra.itemid = e.id
       WHERE e.enrol = :enrol
EOF;

        $rs = $DB->get_recordset_sql($sql, [
            'component' => 'enrol_oneroster',
            'enrol' => 'oneroster',
        ]);

        foreach ($rs as $row) {
            if (!array_key_exists($row->enrolid, $this->existingroleassignments)) {
                $this->existingroleassignments[$row->enrolid] = [];
            }

            if (!array_key_exists($row->userid, $this->existingroleassignments[$row->enrolid])) {
                $this->existingroleassignments[$row->enrolid][$row->userid] = [];
            }

            $this->existingroleassignments[$row->enrolid][$row->userid][$row->roleid] = true;
        }
        $rs->close();
    }

    /**
     * Synchronise all users in the Schools.
     *
     * @param   int[] $schoolids
     * @param   DateTime|null $onlysince Only sync users which have been remotely modified since the specified date
     */
    public function sync_users_in_schools(array $schoolids, ?DateTime $onlysince = null): void {
        $filter = null;
        if ($onlysince) {
            // Only fetch users last modified in the onlysince period.
            $filter = new filter('dateLastModified',  $onlysince->format('o-m-d'), '>');
        }

        // Note: Some Endpoints do not sort properly on Array properties.
        $users = $this->get_container()->get_collection_factory()->get_users(
            [],
            $filter,
            function($data) use ($schoolids) {
                $foundids = array_map(function($orgref) {
                    return $orgref->sourcedId;
                }, $data->get('orgs'));

                return !!count(array_intersect($schoolids, $foundids));
            }
        );

        $usercount = 0;
        foreach ($users as $user) {
            $this->update_or_create_user($user);
            $usercount++;
        }
        $this->get_trace()->output("Finished processing users. Processed {$usercount} users", 3);
    }

    /**
     * Synchronise the entire School.
     *
     * @param   school_entity $school
     * @param   null|DateTime $onlysince
     */
    public function sync_school(school_entity $school, ?DateTime $onlysince = null): void {
        // Updating the category for this school.
        // Qubits Courses Mapping with ClassLink
        $schoolobj = $school->get_data();
        $sourceid = $schoolobj->sourcedId;
        $this->set_qbcourses($sourceid);

        // $this->update_or_create_category($school); // Hide by Qubits

        // Qubits Need to hide the School data
        /* $this->get_trace()->output("Fetching term data", 3);
        foreach ($school->get_terms() as $term) {
            // Nullop to cache terms.
            continue;
        } */

        $classfilter = new filter();
        if ($onlysince) {
            // Only fetch users last modified in the onlysince period.
            $classfilter->add_filter('dateLastModified',  $onlysince->format('o-m-d'), '>');
        } 

        $this->get_trace()->output("Fetching class data", 3);
        $classes = $school->get_classes([], $classfilter);
        foreach ($classes as $class) {
            $this->get_trace()->output(
                sprintf(
                    "Synchronising course '%s' with id %s",
                    $class->get('title'),
                    $class->get('sourcedId')
                ),
                4
            );

           $this->update_or_create_course($class);

            // Note: In an ideal world, enrollments would happen here.
            // However during development we discovered that some services do not work well enough to filter correctly
            // on the sourcedId of the Class entity.
        } 

        $this->get_trace()->output("Fetching enrolments data", 3);
        foreach ($school->get_enrollments() as $enrollment) {
            $this->update_or_create_enrolment($enrollment);
        }
    }

    /**
     * Cache all existing enrolment instances.
     */
    protected function cache_enrolment_instances(): void {
        global $DB;

        // Fetch all of the enrolment instances for this plugin into the cache.
        $enrolinstancesql = <<<EOF
SELECT
    e.*,
    c.idnumber
  FROM {enrol} e
  JOIN {course} c ON c.id = e.courseid
 WHERE e.enrol = :type
EOF;
        $recordset = $DB->get_recordset_sql($enrolinstancesql, ['type' => 'oneroster']);
        foreach ($recordset as $record) {
            $idnumber = $record->idnumber;
            unset($record->idnumber);
            $this->instances[$idnumber] = $record;
        }
        $recordset->close();
    }

    /**
     * Get the Moodle enrolment instance for the specified course representation.
     *
     * @param   course_representation $entity
     * @return  stdClass
     */
    protected function get_course_enrolment_instance(course_representation $entity): ?stdClass {
        $coursedata = $entity->get_course_data();
        if (array_key_exists($coursedata->idnumber, $this->instances)) {
            // The entry already exists in the cache.
            return $this->instances[$coursedata->idnumber];
        }

        return null;
    }

    /**
     * Ensure that the enrolment instance exists for this course.
     *
     * @param   stdClass $course The moodle course to fetch or create the enrolment instance for
     */
    protected function ensure_course_enrolment_instance_exists(stdClass $course): void {
        global $DB;

        if (array_key_exists($course->idnumber, $this->instances)) {
            // The entry already exists in the cache.
            return;
        }

        $instance = $DB->get_record('enrol', [
            'courseid' => $course->id,
            'enrol' => 'oneroster',
        ]);

        if ($instance) {
            // A record exists, add it to the list.
            $this->instances[$course->idnumber] = $instance;

            return;
        }

        $enrolid = $this->get_plugin_instance()->add_instance($course);
        $this->instances[$course->idnumber] = $DB->get_record('enrol', ['id' => $enrolid]);
    }

    /**
     * Fetch the list of organisations that can be syncronised.
     *
     * @return  array
     */
    public function fetch_organisation_list(): Iterable {
        return $this->get_container()->get_collection_factory()->get_orgs();
    }

    /**
     * Update or create a Moodle Course Category based on an entity representing a coursecat.
     *
     * @param   coursecat_representation $entity An entity representing a course category
     * @return  core_course_category
     */
    protected function update_or_create_category(coursecat_representation $entity): core_course_category {
        global $DB;

        // Fetch the course category representation for this entity.
        $remotecategory = $entity->get_course_category_data();

        // Find a matching local course category.
        $localcategoryid = $DB->get_field('course_categories', 'id', [
            'idnumber' => $remotecategory->idnumber,
        ]);

        // Ensure that the entity has not been recently modified.
        // These are only updated once per run.
        if (array_key_exists($entity->get('sourcedId'), $this->modifiedentities)) {
            return core_course_category::get($localcategoryid);
        }
        $this->modifiedentities[$entity->get('sourcedId')] = true;

        // Check for any parents and create/update those too.
        // The alternative is that we fetch all of the ones we need and create them once.
        if ($parent = $entity->get_parent()) {
            $parentcoursecategory = $this->update_or_create_category($parent);
            $remotecategory->parent = $parentcoursecategory->id;
        }

        if ($localcategoryid) {
            $localcategory = core_course_category::get($localcategoryid);

            $remotelastmodified = converter::from_datetime_to_unix($entity->get('dateLastModified'));
            if ($remotelastmodified > $localcategory->timemodified) {
                $localcategory->update($remotecategory);
                $this->add_metric('coursecat', 'update');
            }
        } else {
            $localcategory = core_course_category::create($remotecategory);
            $this->add_metric('coursecat', 'create');
        }

        return $localcategory;
    }

    protected function update_or_create_course(course_representation $entity): void {
        global $CFG, $DB;

        require_once("{$CFG->dirroot}/course/lib.php");

        // Fetch the course representation for this entity.
        $remotecourse = $entity->get_course_data();
        $qbmcoursedata = $this->get_qbit_mdata($remotecourse->idnumber);
        $qbmcourses = $qbmcoursedata["qubitscourses"];
        foreach($qbmcourses as $k => $qbmcourse) {
            $localcourse = $DB->get_record('course', [
                'idnumber' => strtolower($qbmcourse),
            ]);

            $qubitsgroup = $qbmcoursedata["qubitsgroup"][$k];
             // Course Group created or updated
             $groupidnumber = $qubitsgroup;
             $groupid = 0;
             if (!$group = $DB->get_record('groups', array('idnumber'=>$groupidnumber))) {
                 $groupdata = new stdClass;
                 $groupdata->courseid = $localcourse->id;
                 $groupdata->name = $groupidnumber;
                 $groupdata->idnumber = $groupidnumber;
                 $groupdata->description = "Group $groupidnumber";
                 $groupdata->descriptionformat = 1;
                 $groupid = groups_create_group($groupdata);
             }else{
                 $groupid = $group->id;
             }

             $this->ensure_course_enrolment_instance_exists($localcourse);
        }
        

    }

    /**
     * Update or create a Moodle Course based on an entity representing a course.
     *
     * @param   course_representation $entity An entity representing a course category
     * @return  stdClass
     */
    protected function update_or_create_course_old(course_representation $entity): stdClass {
        global $CFG, $DB;

        require_once("{$CFG->dirroot}/course/lib.php");

        // Fetch the course representation for this entity.
        $remotecourse = $entity->get_course_data();
        $qbmcoursedata = $this->get_qbit_mdata($remotecourse->idnumber);
        $qbmcourses = $qbmcoursedata["qubitscourses"];
        foreach($qbmcourses as $qbmcourse) {
            $localcourse = $DB->get_record('course', [
                'shortname' => $qbmcourse,
            ]);
            $this->ensure_course_enrolment_instance_exists($localcourse);
        }

        // Determine the remote parent category.
        //$category = $this->update_or_create_category($entity->get_course_category());
        //$remotecourse->category = $category->id;
        
       
        // Find a matching local course record.
        /*$localcourse = $DB->get_record('course', [
            'idnumber' => $remotecourse->idnumber,
        ]);

        if ($localcourse) {
            $update = false;
            foreach ((array) $remotecourse as $field => $value) {
                if ($localcourse->{$field} != $value) {
                    $update = true;
                    $localcourse->{$field} = $value;
                }
            }

            if ($update) {
                update_course($localcourse);
                $this->add_metric('course', 'update');
            }
        } else {
            $localcourse = create_course($remotecourse);
            $this->add_metric('course', 'create');
        }

        $this->ensure_course_enrolment_instance_exists($localcourse);

        return $localcourse;*/
    }

    /**
     * Update or create a Moodle User based on an entity representing a user.
     *
     * @param   user_representation $entity An entity representing a user category
     * @return  stdClass
     */
    protected function update_or_create_user(user_representation $entity): stdClass {
        global $CFG, $DB;

        // Note: This is _usually_ the responsibility of an authentication plugin but One Roster can work with different
        // authentication sources which do not know anything about One Roster.
        require_once("{$CFG->dirroot}/user/lib.php");

        // Fetch the user representation for this entity.
        $remoteuser = $entity->get_user_data();
        $remoteuser->auth = $this->get_config_setting('newuser_auth');
        $remoteuser->confirmed = true;

        if ($this->get_user_mapping($remoteuser->idnumber)) {
            $localuser = $this->update_existing_user($entity, $remoteuser);
        } else {
            // Create a new uesr.
            $localuser = $this->create_new_user($entity, $remoteuser);
        }

        // See whether this user is an agent for any other user.
        // Note: This is only applied for students as per section 4.1.2 of the specification.
       // $this->sync_user_agents($entity, $localuser);

        return $localuser;
    }

    /**
     * Create a new local user based upon a user representation.
     *
     * @param   user_representation $entity
     * @param   stdClass $remoteuser The user representation for the entity
     * @return  stdClass
     */
    protected function create_new_user(user_representation $entity, stdClass $remoteuser): stdClass {

        
        // Check whether there is an existing user with the same username.
        $user = core_user::get_user_by_username($remoteuser->username);        
        if ($user) { 
            $localuser = \core_user::get_user($user->id);
            $this->create_user_mapping($localuser, $remoteuser->idnumber);

            $this->get_trace()->output(sprintf("Skipping update/create of user %s merged into local user %s",
                $remoteuser->idnumber,
                $localuser->idnumber
            ), 4);

            return $localuser;
        }
        if($remoteuser->status=='tobedeleted') 
        { echo 'TO DE DELETED';
        
            $this->get_trace()->output("No user found for user ");
            return $remoteuser;
        }

        if($remoteuser->status=='active') 
        { echo 'CREATE';
            // No user with the same idnumber, or a mapped idnumber.
            // Create a new user.
            $this->get_trace()->output(sprintf("Creating new user %s (%s)",
            $remoteuser->username,
            $remoteuser->idnumber
        ), 4);
        $remoteuser->mnethostid = 1; // Qubits
        $localuserid = user_create_user($remoteuser);
        $this->add_metric('user', 'create');

        $localuser = \core_user::get_user($localuserid);
        $this->create_user_mapping($localuser, $remoteuser->idnumber);

        return $localuser;
        }
    }

    /**
     * Update an existing user.
     *
     * @param   user_representation $entity
     * @param   stdClass $remoteuser The user representation for the entity
     * @return  stdClass
     */
    protected function update_existing_user(user_representation $entity, stdClass $remoteuser): stdClass {
        global $DB;

        $localuser = $DB->get_record('user', ['idnumber' => $remoteuser->idnumber]);

        // The user exists, user_update_user works on user 'id', so fill that in.
        $remoteuser->id = $localuser->id;

        if ($localuser->timemodified > converter::from_datetime_to_unix($entity->get('dateLastModified'))) {
            $this->get_trace()->output(sprintf("Skipping update of existing user %s with id %s (%s)",
                $remoteuser->username,
                $remoteuser->id,
                $remoteuser->idnumber
            ), 4);

            return $localuser;
        }

        // Update the existing user.
        $this->get_trace()->output(sprintf("Updating existing user %s with id %s (%s) %d < %d",
            $remoteuser->username,
            $remoteuser->id,
            $remoteuser->idnumber,
            $localuser->timemodified,
            converter::from_datetime_to_unix($entity->get('dateLastModified'))
        ), 4);

        user_update_user($remoteuser);
        $this->add_metric('user', 'update');

        return \core_user::get_user($localuser->id);
    }

    /**
     * Synchronise user agents for a user.
     *
     * @param   user_entity $entity The user to sync agents for
     * @param   stdClass $localuser The local record for the user
     */
    protected function sync_user_agents(user_entity $entity, stdClass $localuser): void {
        if ($entity->get('role') !== 'student') {
            // Only applied for students as per section 4.1.2 of the specification.
            return;
        }

        $localusercontext = context_user::instance($localuser->id);

        // Create a mapping of userid => [roleid] for current user agents.
        $localuseragents = [];
        foreach (get_users_roles($localusercontext, [], false) as $userid => $roleassignments) {
            foreach (array_values($roleassignments) as $ra) {
                if ($ra->component === 'enrol_oneroster') {
                    if (!array_key_exists($userid, $localuseragents)) {
                        $localuseragents[$userid] = [];
                    }
                    $localuseragents[$userid][$ra->roleid] = true;
                }
            }
        }

        // Update remote user agents.
        foreach ($entity->get_agent_entities() as $remoteagent) {
            if (!$remoteagent) {
                continue;
            }

            // Ensure that the local user exists.
            $localagent = $this->update_or_create_user($remoteagent);
            if (!$localagent) {
                // Unable to create the local agent.
                $this->get_trace()->output(sprintf(
                    "Unable to assign %s (%s) as a %s of %s (%s). Local user not found.",
                    $remoteagent->get('username'),
                    $remoteagent->get('idnumber'),
                    $remoteagent->get('role'),
                    $entity->get('username'),
                    $entity->get('idnumber')
                ), 4);
                continue;
            }

            // Fetch the local role for the remote agent.
            $roleid = $this->get_role_mapping($remoteagent->get('role'), CONTEXT_USER);
            if (!$roleid) {
                // No local mapping for this role.
                $this->get_trace()->output(sprintf(
                    "Unable to assign %s (%s) as a %s of %s (%s). Role mapping not found.",
                    $remoteagent->get('username'),
                    $remoteagent->get('idnumber'),
                    $remoteagent->get('role'),
                    $entity->get('username'),
                    $entity->get('idnumber')
                ), 4);
                continue;
            }

            $assignrole = !array_key_exists($localagent->id, $localuseragents);
            $assignrole = $assignrole || !array_key_exists($roleid, $localuseragents[$localagent->id]);

            if ($assignrole) {
                // Assign the role.
                role_assign($roleid, $localagent->id, $localusercontext, 'enrol_oneroster');
                $this->get_trace()->output(sprintf(
                    "Assigned %s (%s) as a %s of %s (%s).",
                    $remoteagent->get('username'),
                    $remoteagent->get('idnumber'),
                    $remoteagent->get('role'),
                    $entity->get('username'),
                    $entity->get('idnumber')
                ), 4);
                $this->add_metric('user_mapping', 'create');
            } else {
                // Unset the local agent mapping.
                unset($localuseragents[$localagent->id][$roleid]);
            }

        }

        // Unenrol stale mappings.
        foreach ($localuseragents as $localagentid => $localagentroles) {
            foreach ($localagentroles as $roleid) {
                $this->get_trace()->output(sprintf(
                    "Unasssigned user with id %s from being a %s of %s (%s).",
                    $localagentid,
                    $roleid,
                    $localuser->username,
                    $localuser->idnumber
                ), 4);
                role_unassign($roleid, $localagentid, $localusercontext, 'enrol_oneroster');
                $this->add_metric('user_mapping', 'delete');
            }
        }
    }

    protected function update_or_create_enrolment(enrollment_representation $entity) {
        global $DB;
        
        // Set the Groups by Qubits
        $this->set_course_groups();

        // Fetch the user details for this enrolment.
        $userentity = $entity->get_user_entity();
        if ($userentity === null) {
            $this->get_trace()->output("Unable to fetch user entity for enrollment: " . $entity->get('sourcedId'), 4);
            return;
        }
        $moodleuserid = $this->get_user_mapping_for_user($userentity);
        if ($moodleuserid === null) {
            $this->get_trace()->output("No user found for user " . $userentity->get('identifier'), 4);
            return;
        }

        // Fetch the role mapping for this enrolment.
        $roledata = $entity->get_role_data();
        $moodleroleid = $this->get_role_mapping($roledata->role, (int) CONTEXT_COURSE);
        if ($moodleroleid === null) {
            $this->get_trace()->output("No user found for role '{$roledata->role}'", 4);
            // This role has no mapping in Moodle.
            return;
        }

        // Get the Enrolment instance for this course.
        $scourse = $entity->get_course_representation();
        $cdata = $scourse->get_course_data();
        $mpcdata =  $this->get_qbit_mdata($cdata->idnumber);
        $courses = $mpcdata["qubitscourses"];
        foreach($courses as $k => $cshname){
            $course =  $DB->get_record('course', ["shortname" => $cshname]);
            //$instance = $this->get_course_enrolment_instance($course);
            $instance = $this->instances[$course->idnumber];

            // Fetch Group data
            $grpname = $mpcdata["qubitsgroup"][$k];
            $grpid = $this->qbs_mgroups[$grpname];

            if ($instance === null) {
                $this->get_trace()->output("No enrolment instance could be found or created for course '{$course->idnumber}'", 3);
                return;
            }

            $enroldata = $entity->get_enrolment_data();

            if ($existing = $DB->get_record('user_enrolments', ['userid' => $moodleuserid, 'enrolid' => $instance->id])) {
                $enrolmentkeys = [
                    'status',
                    'timestart',
                    'timeend',
                ];

                // Unset the current mapping to prevent the user from being unenrolled.
                unset($this->existingroleassignments[$instance->id][$moodleuserid][$moodleroleid]);
                if (empty($this->existingroleassignments[$instance->id][$moodleuserid])) {
                    unset($this->existingroleassignments[$instance->id][$moodleuserid]);
                }

                foreach ($enrolmentkeys as $key) {
                    $update = false;
                    if ($existing->{$key} != $enroldata->{$key}) {
                        $update = true;
                    }
                }

                if ($update) {
                    $this->get_trace()->output(
                        "Updating existing enrolment for " .
                        $userentity->get('identifier') .
                        " in {$instance->courseid} from {$enroldata->timestart} to {$enroldata->timeend}",
                        4);
                    $this->get_plugin_instance()->update_user_enrol(
                        $instance,
                        $moodleuserid,
                        $enroldata->status,
                        $enroldata->timestart,
                        $enroldata->timeend
                    );
                    $this->add_metric('enrollment', 'delete');
                }
            } else {
                $this->get_trace()->output(sprintf(
                    "Enroling user %s into %s from %d to %d",
                    $userentity->get('identifier'),
                    $instance->courseid,
                    $enroldata->timestart,
                    $enroldata->timeend
                ), 4);
                $this->get_plugin_instance()->enrol_user(
                    $instance,
                    $moodleuserid,
                    $moodleroleid,
                    $enroldata->timestart,
                    $enroldata->timeend,
                    $enroldata->status,
                    true
                );
                $this->add_metric('enrollment', 'create');
            }
            groups_add_member($grpid, $moodleuserid);

        }
    }

    /**
     * Update or create a Moodle User Enrolment based on an entity representing that enrolment.
     *
     * @param   enrollment_representation $entity An entity representing a enrollment
     * @return  stdClass
     */
    protected function update_or_create_enrolment_old(enrollment_representation $entity) {
        global $DB;

        // Fetch the user details for this enrolment.
        $userentity = $entity->get_user_entity();
        if ($userentity === null) {
            $this->get_trace()->output("Unable to fetch user entity for enrollment: " . $entity->get('sourcedId'), 4);
            return;
        }
        $moodleuserid = $this->get_user_mapping_for_user($userentity);
        if ($moodleuserid === null) {
            $this->get_trace()->output("No user found for user " . $userentity->get('identifier'), 4);
            return;
        }

        // Fetch the role mapping for this enrolment.
        $roledata = $entity->get_role_data();
        $moodleroleid = $this->get_role_mapping($roledata->role, (int) CONTEXT_COURSE);
        if ($moodleroleid === null) {
            $this->get_trace()->output("No user found for role '{$roledata->role}'", 4);
            // This role has no mapping in Moodle.
            return;
        }

        // Get the Enrolment instance for this course.
        $course = $entity->get_course_representation();
        $instance = $this->get_course_enrolment_instance($course);
        if ($instance === null) {
            $this->get_trace()->output("No enrolment instance could be found or created for course '{$course->idnumber}'", 3);
            return;
        }

        $enroldata = $entity->get_enrolment_data();

        if ($existing = $DB->get_record('user_enrolments', ['userid' => $moodleuserid, 'enrolid' => $instance->id])) {
            $enrolmentkeys = [
                'status',
                'timestart',
                'timeend',
            ];

            // Unset the current mapping to prevent the user from being unenrolled.
            unset($this->existingroleassignments[$instance->id][$moodleuserid][$moodleroleid]);
            if (empty($this->existingroleassignments[$instance->id][$moodleuserid])) {
                unset($this->existingroleassignments[$instance->id][$moodleuserid]);
            }

            foreach ($enrolmentkeys as $key) {
                $update = false;
                if ($existing->{$key} != $enroldata->{$key}) {
                    $update = true;
                }
            }

            if ($update) {
                $this->get_trace()->output(
                    "Updating existing enrolment for " .
                    $userentity->get('identifier') .
                    " in {$instance->courseid} from {$enroldata->timestart} to {$enroldata->timeend}",
                    4);
                $this->get_plugin_instance()->update_user_enrol(
                    $instance,
                    $moodleuserid,
                    $enroldata->status,
                    $enroldata->timestart,
                    $enroldata->timeend
                );
                $this->add_metric('enrollment', 'delete');
            }
        } else {
            $this->get_trace()->output(sprintf(
                "Enroling user %s into %s from %d to %d",
                $userentity->get('identifier'),
                $instance->courseid,
                $enroldata->timestart,
                $enroldata->timeend
            ), 4);
            $this->get_plugin_instance()->enrol_user(
                $instance,
                $moodleuserid,
                $moodleroleid,
                $enroldata->timestart,
                $enroldata->timeend,
                $enroldata->status,
                true
            );
            $this->add_metric('enrollment', 'create');
        }
    }

    /**
     * Get the context levels available for the specified role.
     *
     * @param   int $roleid
     * @return  int[] List of context levels suitable for this role
     */
    protected function get_role_contextlevels(int $roleid): array {
        if ($this->rolecontextlevels === null) {
            $this->rolecontextlevels = [];
        }

        if (!array_key_exists($roleid, $this->rolecontextlevels)) {
            $this->rolecontextlevels[$roleid] = get_role_contextlevels($roleid);
        }

        return $this->rolecontextlevels[$roleid];
    }

    /**
     * Whether the specified role is available at the specified context level.
     *
     * @param   int $roleid
     * @param   int $contextlevel
     * @return  bool
     */
    protected function is_role_available_for_contextlevel(int $roleid, int $contextlevel): bool {
        $mappings = $this->get_role_contextlevels($roleid);

        return array_search($contextlevel, $mappings) !== false;
    }

    /**
     * Get the plugin configuration.
     *
     * @param   string $name The plugin name to fetch
     * @return  string|null
     */
    protected function get_config_setting(string $name): ?string {
        if ($this->pluginconfig === null) {
            $this->pluginconfig = get_config('enrol_oneroster');
        }

        return property_exists($this->pluginconfig, $name) ? $this->pluginconfig->{$name} : null;
    }

    /**
     * Get the role mapping for the specified role.
     *
     * @param   string $rolename The One Roster role name
     * @param   int $intendedcontextlevel The context level that this mapping relates to
     * @return  int|null The Moodle Role ID for the mapped role
     */
    protected function get_role_mapping(string $rolename, int $intendedcontextlevel): ?int {
        $roleid = $this->get_config_setting("role_mapping_{$rolename}");

        if (empty($roleid)) {
            // This is user is not configured.
            return null;
        }

        if ($roleid < 0) {
            // This is user is not mapped.
            return null;
        }

        if (!$this->is_role_available_for_contextlevel($roleid, $intendedcontextlevel)) {
            // This role cannot be used in this context level.
            return null;
        }

        return $roleid;
    }

    /**
     * Get the list of user mappings from remote user idnumber to Moodle user ID.
     *
     * One Roster allows multiple people to be represented by multiple Human records in One Roster.
     * Moodle needs to merge those.
     *
     * This is done by mapping a 'Primary' remote sourcedId against the sourcedId of all other users in One Roster with
     * a matching username.
     *
     * @return  array
     */
    protected function get_user_mappings(): array {
        global $DB;

        if ($this->usermappings === null) {
            $sql = <<<EOF
                SELECT eom.mappedid, u.id
                FROM {enrol_oneroster_user_map} eom
                JOIN {user} u ON u.idnumber = eom.parentid
EOF;

            $this->usermappings = $DB->get_records_sql_menu($sql);
        }

        return $this->usermappings;
    }

    /**
     * Get the user mapping for the specified User sourcedId.
     *
     * @param   string $idnumber
     * @return  int|null The user ID of the parent role
     */
    protected function get_user_mapping(string $idnumber): ?int {
        $mappings = $this->get_user_mappings();

        if (array_key_exists($idnumber, $mappings)) {
            return (int) $mappings[$idnumber];
        }

        return null;
    }

    /**
     * Create a user mapping entry.
     *
     * @param   stdClass $user
     * @param   string $mappedid
     */
    protected function create_user_mapping(stdClass $user, string $mappedid): void {
        global $DB;

        $DB->insert_record('enrol_oneroster_user_map', (object) [
            'parentid' => $user->idnumber,
            'mappedid' => $mappedid,
        ]);

        $this->usermappings[$user->idnumber] = $user->id;
    }

    /**
     * Get the user mapping for the specified user representation.
     *
     * @param   user_representation $entity
     * @return  int|null The user ID of the parent role
     */
    protected function get_user_mapping_for_user(user_representation $entity): ?int {
        $userdata = $entity->get_user_data();

        return $this->get_user_mapping($userdata->idnumber);
    }

    /**
     * Add a tracking metric value.
     *
     * @param   string $what The item being tracked
     * @param   string $action The action (create, update, delete)
     * @param   int $count The number of times that the action was performed
     */
    protected function add_metric(string $what, string $action, int $count = 1): void {
        if (!array_key_exists($what, $this->metrics)) {
            $this->metrics[$what] = [
                'create' => 0,
                'update' => 0,
                'delete' => 0,
            ];
        }

        if (!array_key_exists($action, $this->metrics[$what])) {
            return;
        }

        $this->metrics[$what][$action] += $count;
    }

    /**
     * Fetch the recorded tracking metrics.
     *
     * @return  stdClass
     */
    protected function get_metrics(): stdClass {
        return (object) $this->metrics;
    }

    protected function set_qbcourses($sourceid): void{
        global $CFG;
        $fname = $CFG->dirroot.'/clslink/school'.$sourceid.'.json';
        if(file_exists($fname)){
            $qbjson = file_get_contents($fname);
            $qbjson_data = json_decode($qbjson,true);
            $this->qbs_mcourses = $qbjson_data["classes"];
        }
    }

    protected function get_qbit_mdata($class_id): array {
        //$this->get_trace()->output("Class ID  >>>> {$class_id}");
        $ckey = array_search($class_id, array_column($this->qbs_mcourses, 'sourcedId'));
        //$this->get_trace()->output("Key  >>>> {$ckey}");
        $course = [];
        if($ckey!==false){
            $course = $this->qbs_mcourses[$ckey];
        }
        return $course;
    }

    protected function set_course_groups(){
        global $DB;
        $likeidnumber = $DB->sql_like('idnumber', ':idnum');
        $groups = $DB->get_records_sql(
            "SELECT id, idnumber FROM {groups} WHERE {$likeidnumber}",
            [
                'idnum' => 'dns%',
            ]
        );
        
        foreach($groups as $group){
            $this->qbs_mgroups[$group->idnumber] = $group->id;
        }
        
    }

}
