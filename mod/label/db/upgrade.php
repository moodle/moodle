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
 * Label module upgrade
 *
 * @package mod_label
 * @copyright  2006 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This file keeps track of upgrades to
// the label module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die;

function xmldb_label_upgrade($oldversion) {
    global $CFG, $DB;

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022101300) {
        $module = $DB->get_field('modules', 'id', ['name' => 'label']);
        $DB->execute('
            UPDATE {course_modules}
               SET visible = 0, visibleoncoursepage = 1
             WHERE module = :module
                   AND visible = 1
                   AND visibleoncoursepage = 0',
            ['module' => $module]
        );
        upgrade_mod_savepoint(true, 2022101300, 'label');
    }

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2022112801) {
        $prevlang = force_current_language($CFG->lang);

        $select = $DB->sql_like('name', ':tofind');
        $params = ['tofind' => '%@@PLUGINFILE@@%'];
        $total = $DB->count_records_select('label', $select, $params);
        if ($total > 0) {
            $labels = $DB->get_recordset_select('label', $select, $params, '', 'id, name, intro');

            // Show a progress bar.
            $pbar = new progress_bar('upgrademodlabelpluginfile', 500, true);
            $current = 0;

            $defaultname = get_string('modulename', 'label');
            foreach ($labels as $label) {
                $originalname = $label->name;
                // Make sure that all labels have now the same name according to the new convention.
                // Note this is the same (and duplicated) code as in get_label_name as we cannot call any API function
                // during upgrade.
                $name = html_to_text(format_string($label->intro, true));
                $name = preg_replace('/@@PLUGINFILE@@\/[[:^space:]]+/i', '', $name);
                // Remove double space and also nbsp; characters.
                $name = preg_replace('/\s+/u', ' ', $name);
                $name = trim($name);
                if (core_text::strlen($name) > LABEL_MAX_NAME_LENGTH) {
                    $name = core_text::substr($name, 0, LABEL_MAX_NAME_LENGTH) . "...";
                }
                if (empty($name)) {
                    $name = $defaultname;
                }
                $label->name = $name;
                if ($originalname !== $name) {
                    $DB->update_record('label', $label);
                }
                $current++;
                $pbar->update($current, $total, "Upgrading label activity names - $current/$total.");
            }
            $labels->close();
        }
        force_current_language($prevlang);
        upgrade_mod_savepoint(true, 2022112801, 'label');
    }

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
