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
 * Provides {@link tool_policy\output\renderer} class.
 *
 * @package     tool_policy
 * @category    output
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy;

use coding_exception;
use context_helper;
use context_system;
use context_user;
use core\session\manager;
use stdClass;
use tool_policy\event\acceptance_created;
use tool_policy\event\acceptance_updated;
use user_picture;

defined('MOODLE_INTERNAL') || die();

/**
 * Provides the API of the policies plugin.
 *
 * @copyright 2018 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Return current (active) policies versions.
     *
     * @param array $audience If defined, filter against the given audience (AUDIENCE_ALL always included)
     * @return array of stdClass - exported {@link tool_policy\policy_version_exporter} instances
     */
    public static function list_current_versions($audience = null) {

        $current = [];

        foreach (static::list_policies() as $policy) {
            if (empty($policy->currentversion)) {
                continue;
            }
            if ($audience && !in_array($policy->currentversion->audience, [policy_version::AUDIENCE_ALL, $audience])) {
                continue;
            }
            $current[] = $policy->currentversion;
        }

        return $current;
    }

    /**
     * Checks if there are any current policies defined and returns their ids only
     *
     * @param array $audience If defined, filter against the given audience (AUDIENCE_ALL always included)
     * @return array of version ids indexed by policies ids
     */
    public static function get_current_versions_ids($audience = null) {
        global $DB;
        $sql = "SELECT v.policyid, v.id
             FROM {tool_policy} d
             LEFT JOIN {tool_policy_versions} v ON v.policyid = d.id
             WHERE d.currentversionid = v.id";
        $params = [];
        if ($audience) {
            $sql .= " AND v.audience IN (?, ?)";
            $params = [$audience, policy_version::AUDIENCE_ALL];
        }
        return $DB->get_records_sql_menu($sql . " ORDER BY d.sortorder", $params);
    }

    /**
     * Returns a list of all policy documents and their versions.
     *
     * @param array|int|null $ids Load only the given policies, defaults to all.
     * @param int $countacceptances return number of user acceptances for each version
     * @return array of stdClass - exported {@link tool_policy\policy_exporter} instances
     */
    public static function list_policies($ids = null, $countacceptances = false) {
        global $DB, $PAGE;

        $versionfields = policy_version::get_sql_fields('v', 'v_');

        $sql = "SELECT d.id, d.currentversionid, d.sortorder, $versionfields ";

        if ($countacceptances) {
            $sql .= ", COALESCE(ua.acceptancescount, 0) AS acceptancescount ";
        }

        $sql .= " FROM {tool_policy} d
             LEFT JOIN {tool_policy_versions} v ON v.policyid = d.id ";

        if ($countacceptances) {
            $sql .= " LEFT JOIN (
                            SELECT policyversionid, COUNT(*) AS acceptancescount
                            FROM {tool_policy_acceptances}
                            GROUP BY policyversionid
                        ) ua ON ua.policyversionid = v.id ";
        }

        $sql .= " WHERE v.id IS NOT NULL ";

        $params = [];

        if ($ids) {
            list($idsql, $idparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
            $sql .= " AND d.id $idsql";
            $params = array_merge($params, $idparams);
        }

        $sql .= " ORDER BY d.sortorder ASC, v.timecreated DESC";

        $policies = [];
        $versions = [];
        $optcache = \cache::make('tool_policy', 'policy_optional');

        $rs = $DB->get_recordset_sql($sql, $params);

        foreach ($rs as $r) {
            if (!isset($policies[$r->id])) {
                $policies[$r->id] = (object) [
                    'id' => $r->id,
                    'currentversionid' => $r->currentversionid,
                    'sortorder' => $r->sortorder,
                ];
            }

            $versiondata = policy_version::extract_record($r, 'v_');

            if ($countacceptances && $versiondata->audience != policy_version::AUDIENCE_GUESTS) {
                $versiondata->acceptancescount = $r->acceptancescount;
            }

            $versions[$r->id][$versiondata->id] = $versiondata;

            $optcache->set($versiondata->id, $versiondata->optional);
        }

        $rs->close();

        foreach (array_keys($policies) as $policyid) {
            static::fix_revision_values($versions[$policyid]);
        }

        $return = [];
        $context = context_system::instance();
        $output = $PAGE->get_renderer('tool_policy');

        foreach ($policies as $policyid => $policydata) {
            $versionexporters = [];
            foreach ($versions[$policyid] as $versiondata) {
                if ($policydata->currentversionid == $versiondata->id) {
                    $versiondata->status = policy_version::STATUS_ACTIVE;
                } else if ($versiondata->archived) {
                    $versiondata->status = policy_version::STATUS_ARCHIVED;
                } else {
                    $versiondata->status = policy_version::STATUS_DRAFT;
                }
                $versionexporters[] = new policy_version_exporter($versiondata, [
                    'context' => $context,
                ]);
            }
            $policyexporter = new policy_exporter($policydata, [
                'versions' => $versionexporters,
            ]);
            $return[] = $policyexporter->export($output);
        }

        return $return;
    }

    /**
     * Returns total number of users who are expected to accept site policy
     *
     * @return int|null
     */
    public static function count_total_users() {
        global $DB, $CFG;
        static $cached = null;
        if ($cached === null) {
            $cached = $DB->count_records_select('user', 'deleted = 0 AND id <> ?', [$CFG->siteguest]);
        }
        return $cached;
    }

    /**
     * Load a particular policy document version.
     *
     * @param int $versionid ID of the policy document version.
     * @param array $policies cached result of self::list_policies() in case this function needs to be called in a loop
     * @return stdClass - exported {@link tool_policy\policy_exporter} instance
     */
    public static function get_policy_version($versionid, $policies = null) {
        if ($policies === null) {
            $policies = self::list_policies();
        }
        foreach ($policies as $policy) {
            if ($policy->currentversionid == $versionid) {
                return $policy->currentversion;

            } else {
                foreach ($policy->draftversions as $draft) {
                    if ($draft->id == $versionid) {
                        return $draft;
                    }
                }

                foreach ($policy->archivedversions as $archived) {
                    if ($archived->id == $versionid) {
                        return $archived;
                    }
                }
            }
        }

        throw new \moodle_exception('errorpolicyversionnotfound', 'tool_policy');
    }

    /**
     * Make sure that each version has a unique revision value.
     *
     * Empty value are replaced with a timecreated date. Duplicates are suffixed with v1, v2, v3, ... etc.
     *
     * @param array $versions List of objects with id, timecreated and revision properties
     */
    public static function fix_revision_values(array $versions) {

        $byrev = [];

        foreach ($versions as $version) {
            if ($version->revision === '') {
                $version->revision = userdate($version->timecreated, get_string('strftimedate', 'core_langconfig'));
            }
            $byrev[$version->revision][$version->id] = true;
        }

        foreach ($byrev as $origrevision => $versionids) {
            $cnt = count($byrev[$origrevision]);
            if ($cnt > 1) {
                foreach ($versionids as $versionid => $unused) {
                    foreach ($versions as $version) {
                        if ($version->id == $versionid) {
                            $version->revision = $version->revision.' - v'.$cnt;
                            $cnt--;
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Can the user view the given policy version document?
     *
     * @param stdClass $policy - exported {@link tool_policy\policy_exporter} instance
     * @param int $behalfid The id of user on whose behalf the user is viewing the policy
     * @param int $userid The user whom access is evaluated, defaults to the current one
     * @return bool
     */
    public static function can_user_view_policy_version($policy, $behalfid = null, $userid = null) {
        global $USER;

        if ($policy->status == policy_version::STATUS_ACTIVE) {
            return true;
        }

        if (empty($userid)) {
            $userid = $USER->id;
        }

        // Check if the user is viewing the policy on someone else's behalf.
        // Typical scenario is a parent viewing the policy on behalf of her child.
        if ($behalfid > 0) {
            $behalfcontext = context_user::instance($behalfid);

            if ($behalfid != $userid && !has_capability('tool/policy:acceptbehalf', $behalfcontext, $userid)) {
                return false;
            }

            // Check that the other user (e.g. the child) has access to the policy.
            // Pass a negative third parameter to avoid eventual endless loop.
            // We do not support grand-parent relations.
            return static::can_user_view_policy_version($policy, -1, $behalfid);
        }

        // Users who can manage policies, can see all versions.
        if (has_capability('tool/policy:managedocs', context_system::instance(), $userid)) {
            return true;
        }

        // User who can see all acceptances, must be also allowed to see what was accepted.
        if (has_capability('tool/policy:viewacceptances', context_system::instance(), $userid)) {
            return true;
        }

        // Users have access to all the policies they have ever accepted/declined.
        if (static::is_user_version_accepted($userid, $policy->id) !== null) {
            return true;
        }

        // Check if the user could get access through some of her minors.
        if ($behalfid === null) {
            foreach (static::get_user_minors($userid) as $minor) {
                if (static::can_user_view_policy_version($policy, $minor->id, $userid)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return the user's minors - other users on which behalf we can accept policies.
     *
     * Returned objects contain all the standard user name and picture fields as well as the context instanceid.
     *
     * @param int $userid The id if the user with parental responsibility
     * @param array $extrafields Extra fields to be included in result
     * @return array of objects
     */
    public static function get_user_minors($userid, array $extrafields = null) {
        global $DB;

        $ctxfields = context_helper::get_preload_record_columns_sql('c');
        $namefields = get_all_user_name_fields(true, 'u');
        $pixfields = user_picture::fields('u', $extrafields);

        $sql = "SELECT $ctxfields, $namefields, $pixfields
                  FROM {role_assignments} ra
                  JOIN {context} c ON c.contextlevel = ".CONTEXT_USER." AND ra.contextid = c.id
                  JOIN {user} u ON c.instanceid = u.id
                 WHERE ra.userid = ?
              ORDER BY u.lastname ASC, u.firstname ASC";

        $rs = $DB->get_recordset_sql($sql, [$userid]);

        $minors = [];

        foreach ($rs as $record) {
            context_helper::preload_from_record($record);
            $childcontext = context_user::instance($record->id);
            if (has_capability('tool/policy:acceptbehalf', $childcontext, $userid)) {
                $minors[$record->id] = $record;
            }
        }

        $rs->close();

        return $minors;
    }

    /**
     * Prepare data for the {@link \tool_policy\form\policydoc} form.
     *
     * @param \tool_policy\policy_version $version persistent representing the version.
     * @return stdClass form data
     */
    public static function form_policydoc_data(policy_version $version) {

        $data = $version->to_record();
        $summaryfieldoptions = static::policy_summary_field_options();
        $contentfieldoptions = static::policy_content_field_options();

        if (empty($data->id)) {
            // Adding a new version of a policy document.
            $data = file_prepare_standard_editor($data, 'summary', $summaryfieldoptions, $summaryfieldoptions['context']);
            $data = file_prepare_standard_editor($data, 'content', $contentfieldoptions, $contentfieldoptions['context']);

        } else {
            // Editing an existing policy document version.
            $data = file_prepare_standard_editor($data, 'summary', $summaryfieldoptions, $summaryfieldoptions['context'],
                'tool_policy', 'policydocumentsummary', $data->id);
            $data = file_prepare_standard_editor($data, 'content', $contentfieldoptions, $contentfieldoptions['context'],
                'tool_policy', 'policydocumentcontent', $data->id);
        }

        return $data;
    }

    /**
     * Save the data from the policydoc form as a new policy document.
     *
     * @param stdClass $form data submitted from the {@link \tool_policy\form\policydoc} form.
     * @return \tool_policy\policy_version persistent
     */
    public static function form_policydoc_add(stdClass $form) {
        global $DB;

        $form = clone($form);

        $form->policyid = $DB->insert_record('tool_policy', (object) [
            'sortorder' => 999,
        ]);

        static::distribute_policy_document_sortorder();

        return static::form_policydoc_update_new($form);
    }

    /**
     * Save the data from the policydoc form as a new policy document version.
     *
     * @param stdClass $form data submitted from the {@link \tool_policy\form\policydoc} form.
     * @return \tool_policy\policy_version persistent
     */
    public static function form_policydoc_update_new(stdClass $form) {
        global $DB;

        if (empty($form->policyid)) {
            throw new coding_exception('Invalid policy document ID');
        }

        $form = clone($form);

        $form->id = $DB->insert_record('tool_policy_versions', (new policy_version(0, (object) [
            'timecreated' => time(),
            'policyid' => $form->policyid,
        ]))->to_record());

        return static::form_policydoc_update_overwrite($form);
    }


    /**
     * Save the data from the policydoc form, overwriting the existing policy document version.
     *
     * @param stdClass $form data submitted from the {@link \tool_policy\form\policydoc} form.
     * @return \tool_policy\policy_version persistent
     */
    public static function form_policydoc_update_overwrite(stdClass $form) {

        $form = clone($form);
        unset($form->timecreated);

        $summaryfieldoptions = static::policy_summary_field_options();
        $form = file_postupdate_standard_editor($form, 'summary', $summaryfieldoptions, $summaryfieldoptions['context'],
            'tool_policy', 'policydocumentsummary', $form->id);
        unset($form->summary_editor);
        unset($form->summarytrust);

        $contentfieldoptions = static::policy_content_field_options();
        $form = file_postupdate_standard_editor($form, 'content', $contentfieldoptions, $contentfieldoptions['context'],
            'tool_policy', 'policydocumentcontent', $form->id);
        unset($form->content_editor);
        unset($form->contenttrust);

        unset($form->status);
        unset($form->save);
        unset($form->saveasdraft);
        unset($form->minorchange);

        $policyversion = new policy_version($form->id, $form);
        $policyversion->update();

        return $policyversion;
    }

    /**
     * Make the given version the current active one.
     *
     * @param int $versionid
     */
    public static function make_current($versionid) {
        global $DB, $USER;

        $policyversion = new policy_version($versionid);
        if (! $policyversion->get('id') || $policyversion->get('archived')) {
            throw new coding_exception('Version not found or is archived');
        }

        // Archive current version of this policy.
        if ($currentversionid = $DB->get_field('tool_policy', 'currentversionid', ['id' => $policyversion->get('policyid')])) {
            if ($currentversionid == $versionid) {
                // Already current, do not change anything.
                return;
            }
            $DB->set_field('tool_policy_versions', 'archived', 1, ['id' => $currentversionid]);
        }

        // Set given version as current.
        $DB->set_field('tool_policy', 'currentversionid', $policyversion->get('id'), ['id' => $policyversion->get('policyid')]);

        // Reset the policyagreed flag to force everybody re-accept the policies.
        $DB->set_field('user', 'policyagreed', 0);

        // Make sure that the current user is not immediately redirected to the policy acceptance page.
        if (isloggedin() && !isguestuser()) {
            $USER->policyagreed = 1;
        }
    }

    /**
     * Inactivate the policy document - no version marked as current and the document does not apply.
     *
     * @param int $policyid
     */
    public static function inactivate($policyid) {
        global $DB;

        if ($currentversionid = $DB->get_field('tool_policy', 'currentversionid', ['id' => $policyid])) {
            // Archive the current version.
            $DB->set_field('tool_policy_versions', 'archived', 1, ['id' => $currentversionid]);
            // Unset current version for the policy.
            $DB->set_field('tool_policy', 'currentversionid', null, ['id' => $policyid]);
        }
    }

    /**
     * Create a new draft policy document from an archived version.
     *
     * @param int $versionid
     * @return \tool_policy\policy_version persistent
     */
    public static function revert_to_draft($versionid) {
        $policyversion = new policy_version($versionid);
        if (!$policyversion->get('id') || !$policyversion->get('archived')) {
            throw new coding_exception('Version not found or is not archived');
        }

        $formdata = static::form_policydoc_data($policyversion);
        // Unarchived the new version.
        $formdata->archived = 0;
        return static::form_policydoc_update_new($formdata);
    }

    /**
     * Can the current version be deleted
     *
     * @param stdClass $version object describing version, contains fields policyid, id, status, archived, audience, ...
     */
    public static function can_delete_version($version) {
        // TODO MDL-61900 allow to delete not only draft versions.
        return has_capability('tool/policy:managedocs', context_system::instance()) &&
                $version->status == policy_version::STATUS_DRAFT;
    }

    /**
     * Delete the given version (if it is a draft). Also delete policy if this is the only version.
     *
     * @param int $versionid
     */
    public static function delete($versionid) {
        global $DB;

        $version = static::get_policy_version($versionid);
        if (!self::can_delete_version($version)) {
            // Current version can not be deleted.
            return;
        }

        $DB->delete_records('tool_policy_versions', ['id' => $versionid]);

        if (!$DB->record_exists('tool_policy_versions', ['policyid' => $version->policyid])) {
            // This is a single version in a policy. Delete the policy.
            $DB->delete_records('tool_policy', ['id' => $version->policyid]);
        }
    }

    /**
     * Editor field options for the policy summary text.
     *
     * @return array
     */
    public static function policy_summary_field_options() {
        global $CFG;
        require_once($CFG->libdir.'/formslib.php');

        return [
            'subdirs' => false,
            'maxfiles' => -1,
            'context' => context_system::instance(),
        ];
    }

    /**
     * Editor field options for the policy content text.
     *
     * @return array
     */
    public static function policy_content_field_options() {
        global $CFG;
        require_once($CFG->libdir.'/formslib.php');

        return [
            'subdirs' => false,
            'maxfiles' => -1,
            'context' => context_system::instance(),
        ];
    }

    /**
     * Re-sets the sortorder field of the policy documents to even values.
     */
    protected static function distribute_policy_document_sortorder() {
        global $DB;

        $sql = "SELECT p.id, p.sortorder, MAX(v.timecreated) AS timerecentcreated
                  FROM {tool_policy} p
             LEFT JOIN {tool_policy_versions} v ON v.policyid = p.id
              GROUP BY p.id, p.sortorder
              ORDER BY p.sortorder ASC, timerecentcreated ASC";

        $rs = $DB->get_recordset_sql($sql);
        $sortorder = 10;

        foreach ($rs as $record) {
            if ($record->sortorder != $sortorder) {
                $DB->set_field('tool_policy', 'sortorder', $sortorder, ['id' => $record->id]);
            }
            $sortorder = $sortorder + 2;
        }

        $rs->close();
    }

    /**
     * Change the policy document's sortorder.
     *
     * @param int $policyid
     * @param int $step
     */
    protected static function move_policy_document($policyid, $step) {
        global $DB;

        $sortorder = $DB->get_field('tool_policy', 'sortorder', ['id' => $policyid], MUST_EXIST);
        $DB->set_field('tool_policy', 'sortorder', $sortorder + $step, ['id' => $policyid]);
        static::distribute_policy_document_sortorder();
    }

    /**
     * Move the given policy document up in the list.
     *
     * @param id $policyid
     */
    public static function move_up($policyid) {
        static::move_policy_document($policyid, -3);
    }

    /**
     * Move the given policy document down in the list.
     *
     * @param id $policyid
     */
    public static function move_down($policyid) {
        static::move_policy_document($policyid, 3);
    }

    /**
     * Returns list of acceptances for this user.
     *
     * @param int $userid id of a user.
     * @param int|array $versions list of policy versions.
     * @return array list of acceptances indexed by versionid.
     */
    public static function get_user_acceptances($userid, $versions = null) {
        global $DB;

        list($vsql, $vparams) = ['', []];
        if (!empty($versions)) {
            list($vsql, $vparams) = $DB->get_in_or_equal($versions, SQL_PARAMS_NAMED, 'ver');
            $vsql = ' AND a.policyversionid ' . $vsql;
        }

        $userfieldsmod = get_all_user_name_fields(true, 'm', null, 'mod');
        $sql = "SELECT u.id AS mainuserid, a.policyversionid, a.status, a.lang, a.timemodified, a.usermodified, a.note,
                  u.policyagreed, $userfieldsmod
                  FROM {user} u
                  INNER JOIN {tool_policy_acceptances} a ON a.userid = u.id AND a.userid = :userid $vsql
                  LEFT JOIN {user} m ON m.id = a.usermodified";
        $params = ['userid' => $userid];
        $result = $DB->get_recordset_sql($sql, $params + $vparams);

        $acceptances = [];
        foreach ($result as $row) {
            if (!empty($row->policyversionid)) {
                $acceptances[$row->policyversionid] = $row;
            }
        }
        $result->close();

        return $acceptances;
    }

    /**
     * Returns version acceptance for this user.
     *
     * @param int $userid User identifier.
     * @param int $versionid Policy version identifier.
     * @param array|null $acceptances List of policy version acceptances indexed by versionid.
     * @return stdClass|null Acceptance object if the user has ever accepted this version or null if not.
     */
    public static function get_user_version_acceptance($userid, $versionid, $acceptances = null) {
        if (empty($acceptances)) {
            $acceptances = static::get_user_acceptances($userid, $versionid);
        }
        if (array_key_exists($versionid, $acceptances)) {
            // The policy version has ever been accepted.
            return $acceptances[$versionid];
        }

        return null;
    }

    /**
     * Did the user accept the given policy version?
     *
     * @param int $userid User identifier.
     * @param int $versionid Policy version identifier.
     * @param array|null $acceptances Pre-loaded list of policy version acceptances indexed by versionid.
     * @return bool|null True/false if this user accepted/declined the policy; null otherwise.
     */
    public static function is_user_version_accepted($userid, $versionid, $acceptances = null) {

        $acceptance = static::get_user_version_acceptance($userid, $versionid, $acceptances);

        if (!empty($acceptance)) {
            return (bool) $acceptance->status;
        }

        return null;
    }

    /**
     * Get the list of policies and versions that current user is able to see and the respective acceptance records for
     * the selected user.
     *
     * @param int $userid
     * @return array array with the same structure that list_policies() returns with additional attribute acceptance for versions
     */
    public static function get_policies_with_acceptances($userid) {
        // Get the list of policies and versions that current user is able to see
        // and the respective acceptance records for the selected user.
        $policies = static::list_policies();
        $acceptances = static::get_user_acceptances($userid);
        $ret = [];
        foreach ($policies as $policy) {
            $versions = [];
            if ($policy->currentversion && $policy->currentversion->audience != policy_version::AUDIENCE_GUESTS) {
                if (isset($acceptances[$policy->currentversion->id])) {
                    $policy->currentversion->acceptance = $acceptances[$policy->currentversion->id];
                } else {
                    $policy->currentversion->acceptance = null;
                }
                $versions[] = $policy->currentversion;
            }
            foreach ($policy->archivedversions as $version) {
                if ($version->audience != policy_version::AUDIENCE_GUESTS
                        && static::can_user_view_policy_version($version, $userid)) {
                    $version->acceptance = isset($acceptances[$version->id]) ? $acceptances[$version->id] : null;
                    $versions[] = $version;
                }
            }
            if ($versions) {
                $ret[] = (object)['id' => $policy->id, 'versions' => $versions];
            }
        }

        return $ret;
    }

    /**
     * Check if given policies can be accepted by the current user (eventually on behalf of the other user)
     *
     * Currently, the version ids are not relevant and the check is based on permissions only. In the future, additional
     * conditions can be added (such as policies applying to certain users only).
     *
     * @param array $versionids int[] List of policy version ids to check
     * @param int $userid Accepting policies on this user's behalf (defaults to accepting on self)
     * @param bool $throwexception Throw exception instead of returning false
     * @return bool
     */
    public static function can_accept_policies(array $versionids, $userid = null, $throwexception = false) {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            if ($throwexception) {
                throw new \moodle_exception('noguest');
            } else {
                return false;
            }
        }

        if (!$userid) {
            $userid = $USER->id;
        }

        if ($userid == $USER->id && !manager::is_loggedinas()) {
            if ($throwexception) {
                require_capability('tool/policy:accept', context_system::instance());
                return;
            } else {
                return has_capability('tool/policy:accept', context_system::instance());
            }
        }

        // Check capability to accept on behalf as the real user.
        $realuser = manager::get_realuser();
        $usercontext = \context_user::instance($userid);
        if ($throwexception) {
            require_capability('tool/policy:acceptbehalf', $usercontext, $realuser);
            return;
        } else {
            return has_capability('tool/policy:acceptbehalf', $usercontext, $realuser);
        }
    }

    /**
     * Check if given policies can be declined by the current user (eventually on behalf of the other user)
     *
     * Only optional policies can be declined. Otherwise, the permissions are same as for accepting policies.
     *
     * @param array $versionids int[] List of policy version ids to check
     * @param int $userid Declining policies on this user's behalf (defaults to declining by self)
     * @param bool $throwexception Throw exception instead of returning false
     * @return bool
     */
    public static function can_decline_policies(array $versionids, $userid = null, $throwexception = false) {

        foreach ($versionids as $versionid) {
            if (static::get_agreement_optional($versionid) == policy_version::AGREEMENT_COMPULSORY) {
                // Compulsory policies can't be declined (that is what makes them compulsory).
                if ($throwexception) {
                    throw new \moodle_exception('errorpolicyversioncompulsory', 'tool_policy');
                } else {
                    return false;
                }
            }
        }

        return static::can_accept_policies($versionids, $userid, $throwexception);
    }

    /**
     * Check if acceptances to given policies can be revoked by the current user (eventually on behalf of the other user)
     *
     * Revoking optional policies is controlled by the same rules as declining them. Compulsory policies can be revoked
     * only by users with the permission to accept policies on other's behalf. The reasoning behind this is to make sure
     * the user communicates with the site's privacy officer and is well aware of all consequences of the decision (such
     * as losing right to access the site).
     *
     * @param array $versionids int[] List of policy version ids to check
     * @param int $userid Revoking policies on this user's behalf (defaults to revoking by self)
     * @param bool $throwexception Throw exception instead of returning false
     * @return bool
     */
    public static function can_revoke_policies(array $versionids, $userid = null, $throwexception = false) {
        global $USER;

        // Guests' acceptance is not stored so there is nothing to revoke.
        if (!isloggedin() || isguestuser()) {
            if ($throwexception) {
                throw new \moodle_exception('noguest');
            } else {
                return false;
            }
        }

        // Sort policies into two sets according the optional flag.
        $compulsory = [];
        $optional = [];

        foreach ($versionids as $versionid) {
            $agreementoptional = static::get_agreement_optional($versionid);
            if ($agreementoptional == policy_version::AGREEMENT_COMPULSORY) {
                $compulsory[] = $versionid;
            } else if ($agreementoptional == policy_version::AGREEMENT_OPTIONAL) {
                $optional[] = $versionid;
            } else {
                throw new \coding_exception('Unexpected optional flag value');
            }
        }

        // Check if the user can revoke the optional policies from the list.
        if ($optional) {
            if (!static::can_decline_policies($optional, $userid, $throwexception)) {
                return false;
            }
        }

        // Check if the user can revoke the compulsory policies from the list.
        if ($compulsory) {
            if (!$userid) {
                $userid = $USER->id;
            }

            $realuser = manager::get_realuser();
            $usercontext = \context_user::instance($userid);
            if ($throwexception) {
                require_capability('tool/policy:acceptbehalf', $usercontext, $realuser);
                return;
            } else {
                return has_capability('tool/policy:acceptbehalf', $usercontext, $realuser);
            }
        }

        return true;
    }

    /**
     * Mark the given policy versions as accepted by the user.
     *
     * @param array|int $policyversionid Policy version id(s) to set acceptance status for.
     * @param int|null $userid Id of the user accepting the policy version, defaults to the current one.
     * @param string|null $note Note to be recorded.
     * @param string|null $lang Language in which the policy was shown, defaults to the current one.
     */
    public static function accept_policies($policyversionid, $userid = null, $note = null, $lang = null) {
        static::set_acceptances_status($policyversionid, $userid, $note, $lang, 1);
    }

    /**
     * Mark the given policy versions as declined by the user.
     *
     * @param array|int $policyversionid Policy version id(s) to set acceptance status for.
     * @param int|null $userid Id of the user accepting the policy version, defaults to the current one.
     * @param string|null $note Note to be recorded.
     * @param string|null $lang Language in which the policy was shown, defaults to the current one.
     */
    public static function decline_policies($policyversionid, $userid = null, $note = null, $lang = null) {
        static::set_acceptances_status($policyversionid, $userid, $note, $lang, 0);
    }

    /**
     * Mark the given policy versions as accepted or declined by the user.
     *
     * @param array|int $policyversionid Policy version id(s) to set acceptance status for.
     * @param int|null $userid Id of the user accepting the policy version, defaults to the current one.
     * @param string|null $note Note to be recorded.
     * @param string|null $lang Language in which the policy was shown, defaults to the current one.
     * @param int $status The acceptance status, defaults to 1 = accepted
     */
    protected static function set_acceptances_status($policyversionid, $userid = null, $note = null, $lang = null, $status = 1) {
        global $DB, $USER;

        // Validate arguments and capabilities.
        if (empty($policyversionid)) {
            return;
        } else if (!is_array($policyversionid)) {
            $policyversionid = [$policyversionid];
        }
        if (!$userid) {
            $userid = $USER->id;
        }
        self::can_accept_policies([$policyversionid], $userid, true);

        // Retrieve the list of policy versions that need agreement (do not update existing agreements).
        list($sql, $params) = $DB->get_in_or_equal($policyversionid, SQL_PARAMS_NAMED);
        $sql = "SELECT v.id AS versionid, a.*
                  FROM {tool_policy_versions} v
             LEFT JOIN {tool_policy_acceptances} a ON a.userid = :userid AND a.policyversionid = v.id
                 WHERE v.id $sql AND (a.id IS NULL OR a.status <> :status)";

        $needacceptance = $DB->get_records_sql($sql, $params + [
            'userid' => $userid,
            'status' => $status,
        ]);

        $realuser = manager::get_realuser();
        $updatedata = ['status' => $status, 'lang' => $lang ?: current_language(),
            'timemodified' => time(), 'usermodified' => $realuser->id, 'note' => $note];
        foreach ($needacceptance as $versionid => $currentacceptance) {
            unset($currentacceptance->versionid);
            if ($currentacceptance->id) {
                $updatedata['id'] = $currentacceptance->id;
                $DB->update_record('tool_policy_acceptances', $updatedata);
                acceptance_updated::create_from_record((object)($updatedata + (array)$currentacceptance))->trigger();
            } else {
                $updatedata['timecreated'] = $updatedata['timemodified'];
                $updatedata['policyversionid'] = $versionid;
                $updatedata['userid'] = $userid;
                $updatedata['id'] = $DB->insert_record('tool_policy_acceptances', $updatedata);
                acceptance_created::create_from_record((object)($updatedata + (array)$currentacceptance))->trigger();
            }
        }

        static::update_policyagreed($userid);
    }

    /**
     * Make sure that $user->policyagreed matches the agreement to the policies
     *
     * @param int|stdClass|null $user user to check (null for current user)
     */
    public static function update_policyagreed($user = null) {
        global $DB, $USER, $CFG;
        require_once($CFG->dirroot.'/user/lib.php');

        if (!$user || (is_numeric($user) && $user == $USER->id)) {
            $user = $USER;
        } else if (!is_object($user)) {
            $user = $DB->get_record('user', ['id' => $user], 'id, policyagreed');
        }

        $sql = "SELECT d.id, v.optional, a.status
                  FROM {tool_policy} d
            INNER JOIN {tool_policy_versions} v ON v.policyid = d.id AND v.id = d.currentversionid
             LEFT JOIN {tool_policy_acceptances} a ON a.userid = :userid AND a.policyversionid = v.id
                 WHERE (v.audience = :audience OR v.audience = :audienceall)";

        $params = [
            'audience' => policy_version::AUDIENCE_LOGGEDIN,
            'audienceall' => policy_version::AUDIENCE_ALL,
            'userid' => $user->id
        ];

        $allresponded = true;
        foreach ($DB->get_records_sql($sql, $params) as $policyacceptance) {
            if ($policyacceptance->optional == policy_version::AGREEMENT_COMPULSORY && empty($policyacceptance->status)) {
                $allresponded = false;
            } else if ($policyacceptance->optional == policy_version::AGREEMENT_OPTIONAL && $policyacceptance->status === null) {
                $allresponded = false;
            }
        }

        if ($user->policyagreed != $allresponded) {
            $user->policyagreed = $allresponded;
            $DB->set_field('user', 'policyagreed', $allresponded, ['id' => $user->id]);
        }
    }

    /**
     * May be used to revert accidentally granted acceptance for another user
     *
     * @param int $policyversionid
     * @param int $userid
     * @param null $note
     */
    public static function revoke_acceptance($policyversionid, $userid, $note = null) {
        global $DB, $USER;
        if (!$userid) {
            $userid = $USER->id;
        }
        self::can_accept_policies([$policyversionid], $userid, true);

        if ($currentacceptance = $DB->get_record('tool_policy_acceptances',
                ['policyversionid' => $policyversionid, 'userid' => $userid])) {
            $realuser = manager::get_realuser();
            $updatedata = ['id' => $currentacceptance->id, 'status' => 0, 'timemodified' => time(),
                'usermodified' => $realuser->id, 'note' => $note];
            $DB->update_record('tool_policy_acceptances', $updatedata);
            acceptance_updated::create_from_record((object)($updatedata + (array)$currentacceptance))->trigger();
        }

        static::update_policyagreed($userid);
    }

    /**
     * Create user policy acceptances when the user is created.
     *
     * @param \core\event\user_created $event
     */
    public static function create_acceptances_user_created(\core\event\user_created $event) {
        global $USER, $CFG, $DB;

        // Do nothing if not set as the site policies handler.
        if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_policy') {
            return;
        }

        $userid = $event->objectid;
        $lang = current_language();
        $user = $event->get_record_snapshot('user', $userid);
        // Do nothing if the user has not accepted the current policies.
        if (!$user->policyagreed) {
            return;
        }

        // Cleanup our bits in the presignup cache (we can not rely on them at this stage any more anyway).
        $cache = \cache::make('core', 'presignup');
        $cache->delete('tool_policy_userpolicyagreed');
        $cache->delete('tool_policy_viewedpolicies');
        $cache->delete('tool_policy_policyversionidsagreed');

        // Mark all compulsory policies as implicitly accepted during the signup.
        if ($policyversions = static::list_current_versions(policy_version::AUDIENCE_LOGGEDIN)) {
            $acceptances = array();
            $now = time();
            foreach ($policyversions as $policyversion) {
                if ($policyversion->optional == policy_version::AGREEMENT_OPTIONAL) {
                    continue;
                }
                $acceptances[] = array(
                    'policyversionid' => $policyversion->id,
                    'userid' => $userid,
                    'status' => 1,
                    'lang' => $lang,
                    'usermodified' => isset($USER->id) ? $USER->id : 0,
                    'timecreated' => $now,
                    'timemodified' => $now,
                );
            }
            $DB->insert_records('tool_policy_acceptances', $acceptances);
        }

        static::update_policyagreed($userid);
    }

    /**
     * Returns the value of the optional flag for the given policy version.
     *
     * Optimised for being called multiple times by making use of a request cache. The cache is normally populated as a
     * side effect of calling {@link self::list_policies()} and in most cases should be warm enough for hits.
     *
     * @param int $versionid
     * @return int policy_version::AGREEMENT_COMPULSORY | policy_version::AGREEMENT_OPTIONAL
     */
    public static function get_agreement_optional($versionid) {
        global $DB;

        $optcache = \cache::make('tool_policy', 'policy_optional');

        $hit = $optcache->get($versionid);

        if ($hit === false) {
            $flags = $DB->get_records_menu('tool_policy_versions', null, '', 'id, optional');
            $optcache->set_many($flags);
            $hit = $flags[$versionid];
        }

        return $hit;
    }
}
