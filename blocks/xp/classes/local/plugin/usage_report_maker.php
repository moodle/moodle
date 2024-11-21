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
 * Usage report maker.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\plugin;

use block_xp\di;
use block_xp\local\config\config;
use block_xp_filter;
use core_component;
use core_plugin_manager;
use moodle_database;

/**
 * Usage report maker class.
 *
 * @package    block_xp
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usage_report_maker {

    /** @var config The global config. */
    protected $config;
    /** @var moodle_database The database. */
    protected $db;

    /**
     * Constructor.
     *
     * @param moodle_database $db The database.
     * @param config $config The config.
     */
    public function __construct(moodle_database $db, config $config) {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Make usage report.
     *
     * @return object Where keys represent usage.
     */
    public function make() {
        global $CFG;
        $pluginman = core_plugin_manager::instance();

        $data = (object) [
            'url' => $CFG->wwwroot,
            'siteidentifier' => get_site_identifier(),
            'moodle_version' => $CFG->version,
            'moodle_release' => $CFG->release,
            'php_version' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION . '.' . PHP_RELEASE_VERSION,
        ];

        $data->moodle_flavour = $this->get_flavour();
        $data->moodle_flavour_version = $this->get_flavour_version($data->moodle_flavour);

        $xpinfo = $pluginman->get_plugin_info('block_xp');
        $data->xp_version = $xpinfo ? $xpinfo->versiondisk : '?';
        $data->xp_release = $xpinfo ? $xpinfo->release : '?';

        $addon = di::get('addon');
        $data->xpplus_autoactivate = addon::is_automatically_activated();
        $data->xpplus_activated = $addon->is_activated();
        $data->xpplus_release = $addon->get_release();
        $xpplusinfo = $pluginman->get_plugin_info('local_xp');
        $data->xpplus_version = $xpplusinfo ? $xpplusinfo->versiondisk : '-';

        $data->xp_context = $this->config->get('context');
        $data->xp_courses = $this->db->count_records('block_xp_config', []);
        $data->xp_users = $this->db->count_records('block_xp', []);
        $data->xp_unique_users = $this->db->count_records_select('block_xp', '', null, 'COUNT(DISTINCT userid)');
        $data->xp_ladders = $this->db->count_records_select('block_xp_config', 'enableladder != ?', [0]);

        $data->xp_rules = $this->db->count_records_select('block_xp_filters', 'courseid > 0');
        $data->xp_rules_usage = $this->get_rules_usage($data->xp_rules > 5000 ? 5000 : 0);

        $data->xp_ruletypes = $this->db->count_records_select('block_xp_rule', 'contextid > 0');
        $data->xp_ruletypes_usage = $this->db->get_records_sql_menu(
            'SELECT type, COUNT(1) AS n FROM {block_xp_rule} WHERE contextid > 0 GROUP BY type');
        $data->xp_rulefilters_usage = $this->db->get_records_sql_menu(
            'SELECT filter, COUNT(1) AS n FROM {block_xp_rule} WHERE contextid > 0 GROUP BY filter');

        $components = ['availability_xp', 'block_stash', 'enrol_xp', 'filter_shortcodes'];
        $data->plugins = array_reduce($components, function($carry, $component) use ($pluginman) {
            $plugininfo = $pluginman->get_plugin_info($component);
            if (!$plugininfo) {
                return $carry;
            }
            return array_merge($carry, [$component => ['r' => $plugininfo->release, 'v' => (string) $plugininfo->versiondisk]]);
        }, []);

        return $data;
    }

    /**
     * Get the flavour.
     *
     * @return string|null
     */
    protected function get_flavour() {
        if (array_key_exists('totara', core_component::get_plugin_types())) {
            return 'totara';
        } else if (array_key_exists('iomad', core_component::get_plugin_list('local'))) {
            return 'iomad';
        } else if (array_key_exists('workplace', core_component::get_plugin_list('theme'))) {
            return 'workplace';
        }
        return null;
    }

    /**
     * Get the flavour's version.
     *
     * @param string|null $flavour The flavour.
     * @return string|int|float|null The version.
     */
    protected function get_flavour_version($flavour) {
        global $CFG;
        if ($flavour !== 'totara') {
            return null;
        }

        // @codingStandardsIgnoreStart
        $TOTARA = new \stdClass();
        include($CFG->dirroot . '/version.php');
        return isset($TOTARA->version) ? $TOTARA->version : null;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get rules usage.
     *
     * @param int $limit How many rules to check.
     */
    public function get_rules_usage($limit = 0) {
        $rulesusage = [
            '_total' => 0,
            '_zero' => 0,
            '_category_' . block_xp_filter::CATEGORY_EVENTS => 0,
            '_category_' . block_xp_filter::CATEGORY_GRADES => 0,
        ];
        $recordset = $this->db->get_recordset_select('block_xp_filters', 'courseid > 0', null, 'id DESC', '*', 0, $limit);
        foreach ($recordset as $record) {
            $ruledata = json_decode($record->ruledata);
            if (!$ruledata) {
                continue;
            }

            // Skip empty recordsets.
            $recordrules = $this->get_rules_from_ruledata($ruledata);
            if (empty($recordrules)) {
                continue;
            }

            // Skip what seems to be the default 0-point rules for assessable, etc.
            if ($record->points == 0
                && !empty($recordrules['ruleset']) && $recordrules['ruleset'] === 1
                && !empty($recordrules['event']) && $recordrules['event'] === 3
                && !empty($recordrules['property']) && $recordrules['property'] === 2
                && !empty($recordrules['event__mod_book__event__course_module_viewed'])
                && !empty($recordrules['event__mod_forum__event__discussion_subscription_created'])
                && !empty($recordrules['event__mod_forum__event__subscription_created'])) {
                    continue;
            }

            $catkey = '_category_' . $record->category;
            $rulesusage['_total']++;
            $rulesusage['_zero'] += !$record->points ? 1 : 0;
            $rulesusage[$catkey] = (isset($rulesusage[$catkey]) ? $rulesusage[$catkey] : 0) + 1;
            $rulesusage = $this->merge_sums([$rulesusage, $recordrules]);
        }
        $recordset->close();
        return $rulesusage;
    }

    /**
     * Get the rules from rule data.
     *
     * @param object $ruledata The rule data.
     * @return array Containing rule data.
     */
    protected function get_rules_from_ruledata($ruledata) {
        $rules = [];

        $class = isset($ruledata->_class) ? $ruledata->_class : null;
        if (!$class) {
            return $rules;
        }

        // Skip rule set that does not have sub rules.
        $hassubrules = !empty($ruledata->rules);
        if ($class === 'block_xp_ruleset' && !$hassubrules) {
            return $rules;
        }

        // Find names of rules.
        $names = $this->get_rule_names($class, $ruledata);
        foreach ($names as $name) {
            $rules[$name] = 1;
        }

        // Browse sub rules.
        if ($hassubrules) {
            $subrules = !is_array($ruledata->rules) ? (array) $ruledata->rules : $ruledata->rules;
            $subrules = array_values(array_map([$this, 'get_rules_from_ruledata'], $subrules));
            $rules = $this->merge_sums(array_merge([$rules], $subrules));
        }

        return $rules;
    }

    /**
     * Compute the names for a given rule.
     *
     * @param string $class The name of the class rule.
     * @param object $ruledata The rule data of that rule.
     */
    protected function get_rule_names($class, $ruledata) {
        $name = $class;
        $othernames = [];

        if ($class === 'block_xp_ruleset') {
            $name = 'ruleset';
        } else if (strpos($class, 'block_xp_rule_') === 0) {
            $name = substr($class, 14);
        } else if (strpos($class, 'local_xp\\') === 0 || strpos($class, 'block_xp\\') === 0) {
            $parts = explode('\\', $class);
            $name = array_pop($parts);
        }

        if ($name === 'property' && !empty($ruledata->property) && $ruledata->property === 'crud') {
            $othernames[] = 'property__crud';
        } if ($name === 'event' && !empty($ruledata->value)) {
            $othernames[] = 'event__' . str_replace('\\', '__', trim($ruledata->value, '\\'));
        }

        return array_merge([$name], $othernames);
    }

    /**
     * Merge arrays summing their values.
     *
     * @param array $arrays A list of array to sum.
     */
    protected function merge_sums($arrays) {
        $total = [];
        foreach ($arrays as $array) {
            foreach ($array as $k => $v) {
                $total[$k] = (isset($total[$k]) ? $total[$k] : 0) + $v;
            }
        }
        return $total;
    }
}
