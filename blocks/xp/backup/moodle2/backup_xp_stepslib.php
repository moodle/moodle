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
 * Block XP backup steplib.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Block XP backup structure step class.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_xp_block_structure_step extends backup_block_structure_step {

    /**
     * Define structure.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('users');
        $coursecontextid = context_course::instance($this->get_courseid())->id;

        // Define each element separated.
        $xpconfig = new backup_nested_element('config', ['courseid'], [
            'enabled', 'levels', 'lastlogpurge', 'enableladder', 'enableinfos', 'levelsdata',
            'enablelevelupnotif', 'enablecustomlevelbadges', 'maxactionspertime', 'timeformaxactions', 'timebetweensameactions',
            'identitymode', 'rankmode', 'neighbours', 'enablecheatguard', 'defaultfilters', 'laddercols', 'instructions',
            'instructions_format', 'blocktitle', 'blockdescription', 'blockrecentactivity', 'blockrankingsnapshot',
        ]);
        $xpfilters = new backup_nested_element('filters');
        $xpfilter = new backup_nested_element('filter', ['courseid'], ['ruledata', 'points', 'sortorder', 'category']);
        $xprules = new backup_nested_element('rules');
        $xprule = new backup_nested_element('rule', ['id'], [ 'points', 'type', 'filter', 'filtercourseid', 'filtercmid',
            'filterint1', 'filterchar1']);
        $xplevels = new backup_nested_element('xps');
        $xplevel = new backup_nested_element('xp', ['courseid'], ['userid', 'xp']);
        $xplogs = new backup_nested_element('logs');
        $xplog = new backup_nested_element('log', ['courseid'], ['userid', 'eventname', 'xp', 'time']);

        // Prepare the structure.
        $xp = $this->prepare_block_structure($xpconfig);

        $xpfilters->add_child($xpfilter);
        $xp->add_child($xpfilters);
        $xprules->add_child($xprule);
        $xp->add_child($xprules);

        if ($userinfo) {
            $xplevels->add_child($xplevel);
            $xp->add_child($xplevels);

            $xplogs->add_child($xplog);
            $xp->add_child($xplogs);
        }

        // Define sources.
        $xpconfig->set_source_table('block_xp_config', ['courseid' => backup::VAR_COURSEID]);
        $xpfilter->set_source_table('block_xp_filters', ['courseid' => backup::VAR_COURSEID]);
        $xplevel->set_source_table('block_xp', ['courseid' => backup::VAR_COURSEID]);
        $xprule->set_source_sql('SELECT * FROM {block_xp_rule} WHERE contextid = ?', [['sqlparam' => $coursecontextid]]);
        $xplog->set_source_table('block_xp_log', ['courseid' => backup::VAR_COURSEID]);

        // Annotations.
        $xplevel->annotate_ids('user', 'userid');
        $xplog->annotate_ids('user', 'userid');
        $xp->annotate_files('block_xp', 'badges', null, $coursecontextid);

        // Return the root element.
        return $xp;
    }
}
