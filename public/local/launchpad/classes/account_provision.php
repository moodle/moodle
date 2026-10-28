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

namespace local_launchpad;

/**
 * Staff-controlled LaunchPad account provisioning.
 *
 * Fellowship/Mentor: manual users + manual enrolment + course roles on lp-*.
 * Employee: system cohort membership only (no Fellowship/Mentor/Admin rights).
 * Does not change login/signup/auth config. Does not grant Admin.
 * Persona labels are never taken from the client — callers pass validated
 * Moodle ids after server-side authorization checks.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class account_provision {

    /** Capability required at system context to create users (or be site admin). */
    public const CREATE_USER_CAPABILITY = 'moodle/user:create';

    /** Capability required in the course to enrol via this workflow (or site admin). */
    public const ENROL_CAPABILITY = 'enrol/manual:enrol';

    /** Capability to manage the Employee cohort (or site admin). */
    public const COHORT_CAPABILITY = 'moodle/cohort:assign';

    /** Default mentor Moodle role shortname. */
    public const DEFAULT_MENTOR_ROLE = 'editingteacher';

    /**
     * Grants this workflow may perform (never admin).
     *
     * @return list<string>
     */
    public static function allowed_grants(): array {
        return [account_persona::FELLOWSHIP, account_persona::MENTOR, account_persona::EMPLOYEE];
    }

    /**
     * Mentor role shortnames permitted in LaunchPad courses.
     *
     * @return list<string>
     */
    public static function mentor_role_shortnames(): array {
        return ['editingteacher', 'teacher'];
    }

    /**
     * Pure policy: is this grant type allowed by the workflow?
     */
    public static function is_allowed_grant(string $grant): bool {
        $grant = strtolower(trim($grant));
        return in_array($grant, self::allowed_grants(), true);
    }

    /**
     * Pure policy: reject Admin (and unknown) grants with a coded error.
     *
     * @throws account_provision_exception
     */
    public static function assert_grant_allowed(string $grant): void {
        $grant = strtolower(trim($grant));
        if ($grant === account_persona::ADMIN) {
            throw new account_provision_exception(
                'forbidden_grant_admin',
                'Admin must be granted only via Moodle site administration'
            );
        }
        if (!self::is_allowed_grant($grant)) {
            throw new account_provision_exception('forbidden_grant', 'Grant type not allowed: ' . $grant);
        }
    }

    /**
     * Pure policy: LaunchPad course idnumber shape (lp-*).
     */
    public static function is_launchpad_idnumber(string $idnumber): bool {
        return str_starts_with(strtolower(trim($idnumber)), account_persona::COURSE_IDNUMBER_PREFIX);
    }

    /**
     * Pure policy: mentor role shortname is editingteacher or teacher.
     */
    public static function is_allowed_mentor_role(string $shortname): bool {
        return in_array(strtolower(trim($shortname)), self::mentor_role_shortnames(), true);
    }

    /**
     * True when actor may create Moodle accounts via this helper.
     *
     * @param \stdClass|null $actor Full user record or object with id
     */
    public static function actor_may_create_users(?\stdClass $actor): bool {
        if ($actor === null || empty($actor->id)) {
            return false;
        }
        $userid = (int) $actor->id;
        if (is_siteadmin($userid)) {
            return true;
        }
        return has_capability(self::CREATE_USER_CAPABILITY, \context_system::instance(), $userid);
    }

    /**
     * True when actor may manually enrol in this course.
     *
     * @param \stdClass|null $actor
     * @param int $courseid
     */
    public static function actor_may_enrol(?\stdClass $actor, int $courseid): bool {
        if ($actor === null || empty($actor->id) || $courseid <= 0) {
            return false;
        }
        $userid = (int) $actor->id;
        if (is_siteadmin($userid)) {
            return true;
        }
        return has_capability(self::ENROL_CAPABILITY, \context_course::instance($courseid), $userid);
    }

    /**
     * Require create-user authorization or throw.
     *
     * @param \stdClass|null $actor
     * @throws account_provision_exception
     */
    public static function require_can_create_users(?\stdClass $actor): void {
        if (!self::actor_may_create_users($actor)) {
            throw new account_provision_exception('forbidden', 'Not authorized to create users');
        }
    }

    /**
     * Require enrol authorization for the course or throw.
     *
     * @param \stdClass|null $actor
     * @param int $courseid
     * @throws account_provision_exception
     */
    public static function require_can_enrol(?\stdClass $actor, int $courseid): void {
        if (!self::actor_may_enrol($actor, $courseid)) {
            throw new account_provision_exception('forbidden', 'Not authorized to enrol in this course');
        }
    }

    /**
     * Load and validate a LaunchPad course by id.
     *
     * @throws account_provision_exception
     */
    public static function require_launchpad_course(int $courseid): \stdClass {
        global $DB;

        if ($courseid <= 0) {
            throw new account_provision_exception('invalid_course', 'Course id must be positive');
        }
        $course = $DB->get_record('course', ['id' => $courseid], '*', IGNORE_MISSING);
        if (!$course) {
            throw new account_provision_exception('invalid_course', 'Course not found');
        }
        if (!self::is_launchpad_idnumber((string) $course->idnumber)) {
            throw new account_provision_exception(
                'invalid_course',
                'Course is not a LaunchPad (lp-*) course'
            );
        }
        return $course;
    }

    /**
     * Resolve course by idnumber (must be lp-*).
     *
     * @throws account_provision_exception
     */
    public static function require_launchpad_course_by_idnumber(string $idnumber): \stdClass {
        global $DB;

        $idnumber = trim($idnumber);
        if (!self::is_launchpad_idnumber($idnumber)) {
            throw new account_provision_exception('invalid_course', 'Course idnumber must start with lp-');
        }
        $course = $DB->get_record('course', ['idnumber' => $idnumber], '*', IGNORE_MISSING);
        if (!$course) {
            throw new account_provision_exception('invalid_course', 'LaunchPad course not found: ' . $idnumber);
        }
        return $course;
    }

    /**
     * Load an existing non-deleted user.
     *
     * @throws account_provision_exception
     */
    public static function require_user(int $userid): \stdClass {
        global $DB;

        if ($userid <= 0) {
            throw new account_provision_exception('invalid_user', 'User id must be positive');
        }
        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', IGNORE_MISSING);
        if (!$user) {
            throw new account_provision_exception('invalid_user', 'User not found');
        }
        return $user;
    }

    /**
     * Create a manual-auth Moodle account (staff only). Does not enrol or assign roles.
     *
     * @param \stdClass $actor Acting staff user
     * @param array{username:string,email:string,firstname:string,lastname:string,password?:string} $data
     * @return array{status:string,userid:int} status create|exists
     * @throws account_provision_exception
     */
    public static function create_manual_user(\stdClass $actor, array $data): array {
        global $CFG, $DB;

        self::require_can_create_users($actor);

        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->libdir . '/authlib.php');

        $username = \core_text::strtolower(trim((string) ($data['username'] ?? '')));
        $email = trim((string) ($data['email'] ?? ''));
        $firstname = trim((string) ($data['firstname'] ?? ''));
        $lastname = trim((string) ($data['lastname'] ?? ''));

        if ($username === '' || $email === '' || $firstname === '' || $lastname === '') {
            throw new account_provision_exception('invalid_user', 'username, email, firstname, lastname required');
        }
        if (!validate_email($email)) {
            throw new account_provision_exception('invalid_user', 'Invalid email');
        }

        $existing = $DB->get_record('user', [
            'username' => $username,
            'mnethostid' => $CFG->mnet_localhost_id,
            'deleted' => 0,
        ]);
        if ($existing) {
            return ['status' => 'exists', 'userid' => (int) $existing->id];
        }

        $user = (object) [
            'auth' => 'manual',
            'username' => $username,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'confirmed' => 1,
            'mnethostid' => $CFG->mnet_localhost_id,
        ];
        if (!empty($data['password'])) {
            $user->password = (string) $data['password'];
        } else {
            // Temporary password; staff should force reset via Moodle tools as needed.
            $user->password = generate_password(12);
        }

        $userid = user_create_user($user, true, false);
        return ['status' => 'created', 'userid' => (int) $userid];
    }

    /**
     * Enrol user as student (Fellowship) on a LaunchPad course.
     *
     * @param \stdClass $actor
     * @param int $userid
     * @param int $courseid
     * @return array{status:string,userid:int,courseid:int} status enrolled|already_enrolled
     * @throws account_provision_exception
     */
    public static function enrol_fellow(\stdClass $actor, int $userid, int $courseid): array {
        self::assert_grant_allowed(account_persona::FELLOWSHIP);
        self::require_can_enrol($actor, $courseid);
        self::require_user($userid);
        self::require_launchpad_course($courseid);

        return self::enrol_with_role($userid, $courseid, 'student');
    }

    /**
     * Assign mentor (teacher/editingteacher) on a LaunchPad course only.
     *
     * @param \stdClass $actor
     * @param int $userid
     * @param int $courseid
     * @param string $roleshortname editingteacher|teacher
     * @return array{status:string,userid:int,courseid:int,role:string}
     * @throws account_provision_exception
     */
    public static function assign_mentor(
        \stdClass $actor,
        int $userid,
        int $courseid,
        string $roleshortname = self::DEFAULT_MENTOR_ROLE
    ): array {
        self::assert_grant_allowed(account_persona::MENTOR);
        self::require_can_enrol($actor, $courseid);
        self::require_user($userid);
        self::require_launchpad_course($courseid);

        $roleshortname = strtolower(trim($roleshortname));
        if (!self::is_allowed_mentor_role($roleshortname)) {
            throw new account_provision_exception(
                'invalid_role',
                'Mentor role must be editingteacher or teacher'
            );
        }

        $result = self::enrol_with_role($userid, $courseid, $roleshortname);
        $result['role'] = $roleshortname;
        return $result;
    }

    /**
     * Explicitly refuse Admin grants through this workflow.
     *
     * @throws account_provision_exception
     */
    public static function grant_admin(\stdClass $actor, int $userid): void {
        unset($actor, $userid);
        self::assert_grant_allowed(account_persona::ADMIN);
    }

    /**
     * True when actor may assign the Employee cohort.
     *
     * @param \stdClass|null $actor
     */
    public static function actor_may_manage_employees(?\stdClass $actor): bool {
        if ($actor === null || empty($actor->id)) {
            return false;
        }
        $userid = (int) $actor->id;
        if (is_siteadmin($userid)) {
            return true;
        }
        return has_capability(self::COHORT_CAPABILITY, \context_system::instance(), $userid);
    }

    /**
     * Ensure the system Employee cohort exists (idnumber {@see account_persona::EMPLOYEE_COHORT_IDNUMBER}).
     *
     * @return int cohort id
     */
    public static function ensure_employee_cohort(): int {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/cohort/lib.php');

        $idnumber = account_persona::EMPLOYEE_COHORT_IDNUMBER;
        $existing = $DB->get_record('cohort', [
            'idnumber' => $idnumber,
            'contextid' => \context_system::instance()->id,
        ]);
        if ($existing) {
            return (int) $existing->id;
        }

        $cohort = (object) [
            'contextid' => \context_system::instance()->id,
            'name' => get_string('employee_cohort_name', 'local_launchpad'),
            'idnumber' => $idnumber,
            'description' => 'Staff-provisioned employee access for ToBa Learn (not Fellowship).',
            'descriptionformat' => FORMAT_PLAIN,
            'visible' => 1,
        ];
        return (int) cohort_add_cohort($cohort);
    }

    /**
     * Grant Employee access via cohort membership only (no course roles / no admin).
     *
     * @param \stdClass $actor
     * @param int $userid
     * @return array{status:string,userid:int,cohortid:int} status added|already_member
     * @throws account_provision_exception
     */
    public static function grant_employee(\stdClass $actor, int $userid): array {
        global $CFG;

        self::assert_grant_allowed(account_persona::EMPLOYEE);
        if (!self::actor_may_manage_employees($actor)) {
            throw new account_provision_exception('forbidden', 'Not authorized to provision employees');
        }
        self::require_user($userid);

        require_once($CFG->dirroot . '/cohort/lib.php');

        $cohortid = self::ensure_employee_cohort();
        if (cohort_is_member($cohortid, $userid)) {
            return ['status' => 'already_member', 'userid' => $userid, 'cohortid' => $cohortid];
        }
        cohort_add_member($cohortid, $userid);
        return ['status' => 'added', 'userid' => $userid, 'cohortid' => $cohortid];
    }

    /**
     * Snapshot of auth/signup settings this workflow must not change.
     *
     * @return array{registerauth:string,auth:string,alternateloginurl:string}
     */
    public static function auth_config_snapshot(): array {
        global $CFG;

        return [
            'registerauth' => (string) ($CFG->registerauth ?? ''),
            'auth' => (string) ($CFG->auth ?? ''),
            'alternateloginurl' => (string) ($CFG->alternateloginurl ?? ''),
        ];
    }

    /**
     * Manual enrol + course role. Idempotent for duplicates.
     *
     * @return array{status:string,userid:int,courseid:int}
     * @throws account_provision_exception
     */
    private static function enrol_with_role(int $userid, int $courseid, string $roleshortname): array {
        global $CFG, $DB;

        require_once($CFG->libdir . '/enrollib.php');

        $role = $DB->get_record('role', ['shortname' => $roleshortname], '*', IGNORE_MISSING);
        if (!$role) {
            throw new account_provision_exception('invalid_role', 'Role not found: ' . $roleshortname);
        }

        $plugin = enrol_get_plugin('manual');
        if (!$plugin) {
            throw new account_provision_exception('enrol_unavailable', 'Manual enrolment plugin missing');
        }

        $instance = $DB->get_record('enrol', [
            'enrol' => 'manual',
            'courseid' => $courseid,
            'status' => ENROL_INSTANCE_ENABLED,
        ], '*', IGNORE_MISSING);
        if (!$instance) {
            throw new account_provision_exception(
                'enrol_unavailable',
                'No enabled manual enrolment on course ' . $courseid
            );
        }

        $context = \context_course::instance($courseid);
        $already = is_enrolled($context, $userid, '', true)
            && user_has_role_assignment($userid, (int) $role->id, $context->id);

        $plugin->enrol_user($instance, $userid, (int) $role->id);

        return [
            'status' => $already ? 'already_enrolled' : 'enrolled',
            'userid' => $userid,
            'courseid' => $courseid,
        ];
    }
}
