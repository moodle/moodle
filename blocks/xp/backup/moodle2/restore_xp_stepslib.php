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
 * Block XP restore steplib.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_xp\di;
use block_xp\local\backup\restore_context;
use block_xp\local\rulefilter\rulefilter_with_update_after_restore;

/**
 * Block XP restore structure step class.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_xp_block_structure_step extends restore_structure_step {

    /**
     * Execution conditions.
     *
     * @return bool
     */
    protected function execute_condition() {
        global $DB;

        // No restore on the front page.
        if ($this->get_courseid() == SITEID) {
            return false;
        }

        return true;
    }

    /**
     * Define structure.
     */
    protected function define_structure() {
        global $DB;

        $paths = [];
        $userinfo = $this->get_setting_value('users');

        // Define each path.
        $paths[] = new restore_path_element('block', '/block');
        $paths[] = new restore_path_element('config', '/block/config');
        $paths[] = new restore_path_element('filter', '/block/filters/filter');
        $paths[] = new restore_path_element('rule', '/block/rules/rule');

        if ($userinfo) {
            $paths[] = new restore_path_element('xp', '/block/xps/xp');
            $paths[] = new restore_path_element('log', '/block/logs/log');
        }

        return $paths;
    }

    /**
     * Process block.
     *
     * @param array $data
     */
    protected function process_block($data) {
        global $DB;

        $target = $this->get_task()->get_target();
        $courseid = $this->get_courseid();
        $coursecontextid = $this->get_task()->get_course_contextid();

        // The backup target expects that all content is first being removed. Since deleting the block
        // instance does not delete the data itself, we must manually delete everything.
        if ($target == backup::TARGET_CURRENT_DELETING || $target == backup::TARGET_EXISTING_DELETING) {
            $this->log('block_xp: deleting all data in target course', backup::LOG_DEBUG);

            // Removing associated data.
            $conditions = ['courseid' => $courseid];
            $DB->delete_records('block_xp', $conditions);
            $DB->delete_records('block_xp_config', $conditions);
            $DB->delete_records('block_xp_filters', $conditions);
            $DB->delete_records('block_xp_log', $conditions);

            // Remove rules in course.
            $DB->delete_records('block_xp_rule', ['contextid' => $coursecontextid]);

            // Removing old preferences.
            $sql = $DB->sql_like('name', ':name');
            $DB->delete_records_select('user_preferences', $sql, [
                'name' => 'block_xp-notice-block_intro_' . $courseid,
            ]);
            $DB->delete_records_select('user_preferences', $sql, [
                'name' => 'block_xp_notify_level_up_' . $courseid,
            ]);
        }
    }

    /**
     * Process config.
     *
     * @param array $data
     */
    protected function process_config($data) {
        global $DB;
        $data['courseid'] = $this->get_courseid();

        // Guarantees that older backups are given the expected legacy value here.
        if (!isset($data['defaultfilters'])) {
            $data['defaultfilters'] = \block_xp\local\config\course_world_config::DEFAULT_FILTERS_STATIC;
        }

        if ($DB->record_exists('block_xp_config', ['courseid' => $data['courseid']])) {
            $this->log('block_xp: config not restored, existing config was found', backup::LOG_DEBUG);
            return;
        }
        $DB->insert_record('block_xp_config', $data);
    }

    /**
     * Process filter.
     *
     * @param array $data
     */
    protected function process_filter($data) {
        global $DB;
        $data['courseid'] = $this->get_courseid();

        // We must never have more than one category grades rule, and it should be a ruleset.
        if (!empty($data['category']) && $data['category'] == block_xp_filter::CATEGORY_GRADES) {

            // If there is only rule, and its empty, then we restore on top of it.
            $records = $DB->get_records('block_xp_filters', ['courseid' => $data['courseid'], 'category' => $data['category']]);
            if (count($records) === 1) {

                $record = reset($records);
                $filter = block_xp_filter::load_from_data($record);
                $rule = $filter->get_rule();

                if ($rule instanceof block_xp_ruleset) {
                    $rules = $rule->get_rules();
                    if (!empty($rules)) {
                        // The ruleset is not empty, there are existing rules, pass.
                        $this->log("block_xp: grades rules not restored, existing grade rules found", backup::LOG_DEBUG);
                        return;
                    }

                    // Update the record.
                    $DB->update_record('block_xp_filters', ['id' => $record->id] + $data);
                    return;

                } else {
                    // It really should be a ruleset, odd, let's just pass.
                    $this->log("block_xp: grades rules not restored, existing grade rules found", backup::LOG_DEBUG);
                    return;
                }

            } else if (count($records) > 1) {
                // It's safer to ignore this.
                $this->log("block_xp: grades rules not restored, multiple existing grade rules found", backup::LOG_DEBUG);
                return;
            }
        }

        $DB->insert_record('block_xp_filters', $data);
    }

    /**
     * Process log.
     *
     * @param array $data The data.
     */
    protected function process_log($data) {
        global $DB;
        $data['courseid'] = $this->get_courseid();
        $data['userid'] = $this->get_mappingid('user', $data['userid']);
        $DB->insert_record('block_xp_log', $data);
    }

    /**
     * Process rule.
     *
     * @param array $data The data.
     */
    protected function process_rule($data) {
        global $DB;

        $oldid = $data['id'];
        $data['contextid'] = $this->get_task()->get_course_contextid();
        $data['childcontextid'] = 0; // Not relevant when restoring courses.
        unset($data['id']);

        $type = di::get('rule_type_resolver')->get_type($data['type']);
        if (!$type) {
            $this->log("block_xp: Rule with unknown type '{$data['type']}' not restored", backup::LOG_DEBUG);
            return;
        }

        $filter = di::get('rule_filter_handler')->get_filter($data['filter']);
        if (!$filter) {
            $this->log("block_xp: Rule with unknown filter '{$data['filter']}' not restored", backup::LOG_DEBUG);
            return;
        }

        if (!in_array(CONTEXT_COURSE, $filter->get_compatible_context_levels())) {
            $this->log("block_xp: Skipping disallowed filter '{$data['filter']}' in context", backup::LOG_DEBUG);
            return;
        }

        if (!$filter->is_multiple_allowed()) {
            $dictator = di::get('rule_dictator');
            $context = $this->get_task()->get_course_context();
            $testoptions = ['type' => $data['type'], 'filter' => $data['filter']];
            if ($dictator->count_rules_in_context($context, null, $testoptions) > 0) {
                $this->log("block_xp: Skipping disallowed multiple rules for '{$data['type']}/{$data['filter']}'",
                    backup::LOG_DEBUG);
                return;
            }
        }

        $newid = $DB->insert_record('block_xp_rule', $data);
        $this->set_mapping('block_xp_rule', $oldid, $newid);
        $this->set_mapping('block_xp_rule_oldid', $newid, $oldid);
    }

    /**
     * Process XP.
     *
     * @param array $data The data.
     */
    protected function process_xp($data) {
        global $DB;
        $data['courseid'] = $this->get_courseid();
        $data['userid'] = $this->get_mappingid('user', $data['userid']);
        $data['lvl'] = 1; // This is no longer used, and is hardcoded to 1.
        if ($DB->record_exists('block_xp', ['courseid' => $data['courseid'], 'userid' => $data['userid']])) {
            $this->log("block_xp: XP of user with id '{$data['userid']}' not restored, existing entry found", backup::LOG_DEBUG);
            return;
        }
        $DB->insert_record('block_xp', $data);
    }

    /**
     * After execute.
     */
    protected function after_execute() {
        $this->add_related_files('block_xp', 'badges', null, $this->task->get_old_course_contextid());
    }

    /**
     * After restore.
     */
    protected function after_restore() {
        global $DB;

        di::reset_container();
        $courseid = $this->get_courseid();
        $restorecontext = restore_context::from_structure_step($this);

        // Update the levels data if needed.
        try {
            $factory = di::get('course_world_factory');
            $world = $factory->get_world($courseid);
            $writer = di::get('levels_info_writer');
            $writer->update_world_after_restore($restorecontext, $world);
        } catch (Exception $e) {
            $this->log("block_xp: Running levels_info_writer::update_world_after_restore did not succeed", backup::LOG_DEBUG);
        }

        // Update the rules after restore.
        try {
            $dictator = di::get('rule_dictator');
            $filterhandler = di::get('rule_filter_handler');

            $rules = $dictator->get_rules_in_context($restorecontext->get_course_context());
            foreach ($rules as $rule) {

                // Check that this was restored just then.
                $oldid = $restorecontext->get_mapping_id('block_xp_rule_oldid', $rule->get_id());
                if ($oldid === null) {
                    continue;
                }

                // Self-handled update.
                $filterconfig = $rule->get_filter_config();
                if (isset($filterconfig->courseid)) {
                    $filterconfig->courseid = $restorecontext->get_mapping_id('course', $filterconfig->courseid);
                }
                if (isset($filterconfig->cmid)) {
                    $filterconfig->cmid = $restorecontext->get_mapping_id('course_module', $filterconfig->cmid);
                }

                // Delegated update for other filters.
                $filter = $filterhandler->get_filter($rule->get_filter_name());
                if ($filter && $filter instanceof rulefilter_with_update_after_restore) {
                    $filterconfig = $filter->update_config_after_restore($restorecontext, $filterconfig);
                }

                // Update the config record.
                $DB->update_record('block_xp_rule', [
                    'id' => $rule->get_id(),
                    'filtercourseid' => $filterconfig->courseid ?? null,
                    'filtercmid' => $filterconfig->cmid ?? null,
                    'filterint1' => $filterconfig->int1 ?? null,
                    'filterchar1' => $filterconfig->char1 ?? null,
                ]);
            }
            // We may eventually need to purge caches here, especially if dictator remembers.
        } catch (Exception $e) {
            $this->log("block_xp: Updating rules after restore failed", backup::LOG_DEBUG);
        }

        // Update each filter (the rules).
        $filters = $DB->get_recordset('block_xp_filters', ['courseid' => $courseid]);
        foreach ($filters as $filter) {
            $filter = block_xp_filter::load_from_data($filter);
            $filter->update_after_restore($this->get_restoreid(), $courseid, $this->get_logger());
        }
        $filters->close();

        // Attempt to purge the filters cache. It should not be needed, but just in case.
        try {
            $factory = di::get('course_world_factory');
            $world = $factory->get_world($courseid);
            $filtermanager = $world->get_filter_manager();
            $filtermanager->invalidate_filters_cache();
        } catch (Exception $e) {
            $this->log("block_xp: Could not invalidate filter cache", backup::LOG_DEBUG);
        }
    }

}
