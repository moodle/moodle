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
 * @package mod_dataform
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * This file keeps track of upgrades to
 * the dataform module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 */

function xmldb_dataform_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.1.0 release upgrade line.
    xmldb_dataform_upgrade_2012032100($dbman, $oldversion);
    xmldb_dataform_upgrade_2012040600($dbman, $oldversion);
    xmldb_dataform_upgrade_2012050500($dbman, $oldversion);
    xmldb_dataform_upgrade_2012051600($dbman, $oldversion);
    xmldb_dataform_upgrade_2012053100($dbman, $oldversion);
    xmldb_dataform_upgrade_2012060101($dbman, $oldversion);
    xmldb_dataform_upgrade_2012061700($dbman, $oldversion);
    xmldb_dataform_upgrade_2012070601($dbman, $oldversion);
    xmldb_dataform_upgrade_2012081801($dbman, $oldversion);
    xmldb_dataform_upgrade_2012082600($dbman, $oldversion);
    xmldb_dataform_upgrade_2012082900($dbman, $oldversion);
    xmldb_dataform_upgrade_2012092002($dbman, $oldversion);
    xmldb_dataform_upgrade_2012092207($dbman, $oldversion);
    xmldb_dataform_upgrade_2012121600($dbman, $oldversion);
    xmldb_dataform_upgrade_2012121900($dbman, $oldversion);
    xmldb_dataform_upgrade_2013051101($dbman, $oldversion);
    xmldb_dataform_upgrade_2014041100($dbman, $oldversion);
    xmldb_dataform_upgrade_2014051301($dbman, $oldversion);
    xmldb_dataform_upgrade_2014111000($dbman, $oldversion);
    xmldb_dataform_upgrade_2015051100($dbman, $oldversion);

    return true;
}

function xmldb_dataform_upgrade_2012032100($dbman, $oldversion) {
    if ($oldversion < 2012032100) {
        // Add field selection to dataform_filters.
        $table = new xmldb_table('dataform_filters');
        $field = new xmldb_field('selection', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'perpage');

        // Launch add field selection.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012032100, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012040600($dbman, $oldversion) {
    if ($oldversion < 2012040600) {
        // Add field edits to dataform_fields.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('edits', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL, null, '-1', 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012040600, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012050500($dbman, $oldversion) {
    if ($oldversion < 2012050500) {
        // Drop field comments from dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('comments');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        // Drop field locks.
        $field = new xmldb_field('locks');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        // Add field rules.
        $field = new xmldb_field('rules', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'rating');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012050500, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012051600($dbman, $oldversion) {
    if ($oldversion < 2012051600) {
        // Drop field grading from entries.
        $table = new xmldb_table('dataform_entries');
        $field = new xmldb_field('grading');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012051600, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012053100($dbman, $oldversion) {
    if ($oldversion < 2012053100) {
        $table = new xmldb_table('dataform');

        // Add field cssincludes.
        $field = new xmldb_field('cssincludes', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'css');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add field jsincludes.
        $field = new xmldb_field('jsincludes', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'js');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012053100, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012060101($dbman, $oldversion) {
    global $DB;

    if ($oldversion < 2012060101) {
        // Changed stored content of view editors from serialized to formatted string.
        // Assumed at this point that serialized content in param fields in the
        // view table is editor content which needs to be unserialized to
        // $text, $format, $trust and restored as "ft:{$format}tr:{$trust}ct:$text".

        // Get all views.
        if ($views = $DB->get_records('dataform_views')) {
            foreach ($views as $view) {
                $update = false;
                // Section field.
                if (!empty($view->section)) {
                    $editordata = @unserialize($view->section);
                    if ($editordata !== false) {
                        list($text, $format, $trust) = $editordata;
                        $view->section = "ft:{$format}tr:{$trust}ct:$text";
                        $update = true;
                    }
                }
                // Ten param fields.
                for ($i = 1; $i <= 10; ++$i) {
                    $param = "param$i";
                    if (!empty($view->$param)) {
                        $editordata = @unserialize($view->$param);
                        if ($editordata !== false) {
                            list($text, $format, $trust) = $editordata;
                            $view->$param = "ft:{$format}tr:{$trust}ct:$text";
                            $update = true;
                        }
                    }
                }
                if ($update) {
                    $DB->update_record('dataform_views', $view);
                }
            }
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012060101, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012061700($dbman, $oldversion) {
    global $DB;

    if ($oldversion < 2012061700) {
        // Remove version record of dataform views and fields from config_plugin.
        $DB->delete_records_select('config_plugins', $DB->sql_like('plugin', '?'), array('dataform%'));
        // Change type of view block/blockext to matrix/matrixext.
        $DB->set_field('dataform_views', 'type', 'matrix', array('type' => 'block'));
        $DB->set_field('dataform_views', 'type', 'matrixext', array('type' => 'blockext'));

        // Move content of matrixext param1 -> param4 and param3 -> param5.
        if ($views = $DB->get_records('dataform_views', array('type' => 'matrixext'))) {
            foreach ($views as $view) {
                if (!empty($view->param1) or !empty($view->param3)) {
                    $view->param4 = $view->param1;
                    $view->param5 = $view->param3;
                    $view->param1 = null;
                    $view->param3 = null;
                    $DB->update_record('dataform_views', $view);
                }
            }
        }

        // Move content of editon param3 -> param7.
        if ($views = $DB->get_records('dataform_views', array('type' => 'editon'))) {
            foreach ($views as $view) {
                if (!empty($view->param3)) {
                    $view->param7 = $view->param3;
                    $view->param1 = null;
                    $view->param3 = null;
                    $DB->update_record('dataform_views', $view);
                }
            }
        }

        // Move content of tabular param1 -> param3.
        if ($views = $DB->get_records('dataform_views', array('type' => 'tabular'))) {
            foreach ($views as $view) {
                $view->param3 = $view->param1;
                $view->param1 = null;
                $DB->update_record('dataform_views', $view);
            }
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012061700, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012070601($dbman, $oldversion) {
    global $DB;

    if ($oldversion < 2012070601) {
        // Add field default filter to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('defaultfilter', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'defaultview');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Move content of dataform->defaultsort to a new default filter.
        if ($dataforms = $DB->get_records('dataform')) {
            $strdefault = get_string('default');
            foreach ($dataforms as $dfid => $dataform) {
                if (!empty($dataform->defaultsort)) {
                    // Add a new 'Default filter' filter.
                    $filter = new \stdClass;
                    $filter->dataid = $dfid;
                    $filter->name = $strdefault. '_0';
                    $filter->description = '';
                    $filter->visible = 0;
                    $filter->customsort = $dataform->defaultsort;

                    if ($filterid = $DB->insert_record('dataform_filters', $filter)) {
                        $DB->set_field('dataform', 'defaultfilter', $filterid, array('id' => $dfid));
                    }
                }
            }
        }

        // Drop dataform field defaultsort.
        $field = new xmldb_field('defaultsort');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012070601, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012081801($dbman, $oldversion) {
    if ($oldversion < 2012081801) {
        // Add field visible to dataform_fields.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('visible', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '2', 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012081801, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012082600($dbman, $oldversion) {
    if ($oldversion < 2012082600) {
        // Change timelimit field to signed, default -1.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('timelimit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1', 'maxentries');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_unsigned($table, $field);
            $dbman->change_field_default($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012082600, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012082900($dbman, $oldversion) {
    global $DB;

    if ($oldversion < 2012082900) {
        $fs = get_file_storage();
        // Move presets from course_packages to course_presets.
        if ($dataforms = $DB->get_records('dataform')) {
            foreach ($dataforms as $df) {
                $context = context_course::instance($df->course);
                if ($presets = $fs->get_area_files($context->id, 'mod_dataform', 'course_packages')) {

                    $filerecord = new \stdClass;
                    $filerecord->contextid = $context->id;
                    $filerecord->component = 'mod_dataform';
                    $filerecord->filearea = 'course_presets';
                    $filerecord->filepath = '/';

                    foreach ($presets as $preset) {
                        if (!$preset->is_directory()) {
                            $fs->create_file_from_storedfile($filerecord, $preset);
                        }
                    }
                    $fs->delete_area_files($context->id, 'mod_dataform', 'course_packages');
                }
            }
        }

        // Move presets from site_packages to site_presets.
        $filerecord = new \stdClass;
        $filerecord->contextid = SYSCONTEXTID;
        $filerecord->component = 'mod_dataform';
        $filerecord->filearea = 'site_presets';
        $filerecord->filepath = '/';

        if ($presets = $fs->get_area_files(SYSCONTEXTID, 'mod_dataform', 'course_packages')) {
            foreach ($presets as $preset) {
                if (!$preset->is_directory()) {
                    $fs->create_file_from_storedfile($filerecord, $preset);
                }
            }
        }
        $fs->delete_area_files(SYSCONTEXTID, 'mod_dataform', 'site_packages');

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012082900, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012092002($dbman, $oldversion) {
    global $CFG, $DB;
    if ($oldversion < 2012092002) {
        // Add rules table.
        $table = new xmldb_table('dataform_rules');
        if (!$dbman->table_exists($table)) {
            $filepath = "$CFG->dirroot/mod/dataform/db/install.xml";
            $dbman->install_one_table_from_xmldb_file($filepath, 'dataform_rules');
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012092002, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012092207($dbman, $oldversion) {
    global $DB;
    if ($oldversion < 2012092207) {
        // Change type of view matrix/matrixext to grid/gridext.
        $DB->set_field('dataform_views', 'type', 'grid', array('type' => 'matrix'));
        $DB->set_field('dataform_views', 'type', 'gridext', array('type' => 'matrixext'));

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012092207, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012121600($dbman, $oldversion) {
    global $DB;
    if ($oldversion < 2012121600) {
        // Convert internal field ids whereever they are cached or referenced.
        $newfieldids = array(
            -1 => 'entry',
            -2 => 'timecreated',
            -3 => 'timemodified',
            -4 => 'approve',
            -5 => 'group',
            -6 => 'userid',
            -7 => 'username',
            -8 => 'userfirstname',
            -9 => 'userlastname',
            -10 => 'userusername',
            -11 => 'useridnumber',
            -12 => 'userpicture',
            -13 => 'comment',
            -14 => 'rating',
            -141 => 'ratingavg',
            -142 => 'ratingcount',
            -143 => 'ratingmax',
            -144 => 'ratingmin',
            -145 => 'ratingsum',
        );

        // View patterns.
        if ($views = $DB->get_records('dataform_views')) {
            foreach ($views as $view) {
                $update = false;
                if ($view->patterns) {
                    $patterns = unserialize($view->patterns);
                    $newpatterns = array('view' => $patterns['view'], 'field' => array());
                    foreach ($patterns['field'] as $fieldid => $tags) {
                        if ($fieldid < 0 and !empty($newfieldids[$fieldid])) {
                            $newpatterns['field'][$newfieldids[$fieldid]] = $tags;
                            $update = true;
                        } else {
                            $newpatterns['field'][$fieldid] = $tags;
                        }
                    }
                    $view->patterns = serialize($newpatterns);
                }
                if ($update) {
                    $DB->update_record('dataform_views', $view);
                }
            }
        }
        // Filter customsort and customsearch.
        if ($filters = $DB->get_records('dataform_filters')) {
            foreach ($filters as $filter) {
                $update = false;

                // Adjust customsort field ids.
                if ($filter->customsort) {
                    $customsort = unserialize($filter->customsort);
                    $sortfields = array();
                    foreach ($customsort as $fieldid => $sortdir) {
                        if ($fieldid < 0 and !empty($newfieldids[$fieldid])) {
                            $sortfields[$newfieldids[$fieldid]] = $sortdir;
                            $update = true;
                        } else {
                            $sortfields[$fieldid] = $sortdir;
                        }
                    }
                    $filter->customsort = serialize($sortfields);
                }

                // Adjust customsearch field ids.
                if ($filter->customsearch) {
                    $customsearch = unserialize($filter->customsearch);
                    $searchfields = array();
                    foreach ($customsearch as $fieldid => $options) {
                        if ($fieldid < 0 and !empty($newfieldids[$fieldid])) {
                            $searchfields[$newfieldids[$fieldid]] = $options;
                            $update = true;
                        } else {
                            $searchfields[$fieldid] = $options;
                        }
                    }
                    $filter->customsearch = serialize($searchfields);
                }
                if ($update) {
                    $DB->update_record('dataform_filters', $filter);
                }
            }
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012121600, 'dataform');
    }
}

function xmldb_dataform_upgrade_2012121900($dbman, $oldversion) {
    global $DB;
    if ($oldversion < 2012121900) {

        // Changing type of field groupby on table dataform_views to char.
        $table = new xmldb_table('dataform_views');
        $field = new xmldb_field('groupby', XMLDB_TYPE_CHAR, '64', null, null, null, '', 'perpage');
        $dbman->change_field_type($table, $field);

        // Changing type of field groupby on table dataform_filters to char.
        $table = new xmldb_table('dataform_filters');
        $field = new xmldb_field('groupby', XMLDB_TYPE_CHAR, '64', null, null, null, '', 'selection');
        $dbman->change_field_type($table, $field);

        // Change groupby 0 to null in existing views and filters.
        $DB->set_field('dataform_views', 'groupby', null, array('groupby' => 0));
        $DB->set_field('dataform_filters', 'groupby', null, array('groupby' => 0));

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2012121900, 'dataform');
    }
}

function xmldb_dataform_upgrade_2013051101($dbman, $oldversion) {
    global $DB;
    if ($oldversion < 2013051101) {
        // Add notification format column to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('notificationformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'notification');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add label column to dataform fields.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('label', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'edits');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, 2013051101, 'dataform');
    }
}

function xmldb_dataform_upgrade_2014041100($dbman, $oldversion) {
    global $CFG, $DB;

    $newversion = 2014041100;
    if ($oldversion < $newversion) {
        // Drop available-from column to dataform views.
        $table = new xmldb_table('dataform_views');
        $field = new xmldb_field('availablefrom', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'description');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop available-to column to dataform views.
        $table = new xmldb_table('dataform_views');
        $field = new xmldb_field('availableto', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'availablefrom');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop set-as-default column to dataform views.
        $table = new xmldb_table('dataform_views');
        $field = new xmldb_field('setasdefault', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'availableto');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Add inline view column to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('inlineview', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'introformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add embedded view column to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('embedded', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'inlineview');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Change field description column to default null.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'name');
        $dbman->change_field_type($table, $field);

        // Transfer comments from internal comments field to mdlcomments.
        if ($dataforms = $DB->get_records('dataform')) {
            foreach (array_keys($dataforms) as $dataformid) {
                $df = mod_dataform_dataform::instance($dataformid);
                // For each Dataform instance get context.
                $context = $df->context;
                if ($comments = $DB->get_records('comments', array('contextid' => $context->id, 'commentarea' => 'entry'))) {
                    // Add an mdlcomments field to the Dataform instance.
                    $commentmdl = (object) array(
                        'dataid' => $dataformid,
                        'type' => 'commentmdl',
                        'name' => 'mdlcomments'
                    );
                    $commentmdlid = $DB->insert_record('dataform_fields', $commentmdl);
                    // Update the commentarea to mdlomments.
                    foreach ($comments as $comment) {
                        $DB->set_field('comments', 'commentarea', 'mdlcomments', array('id' => $comment->id));
                    }

                    // Replace internal comment patterns with the corresponding commentmdl patterns.
                    $replacements = array();
                    $replacements['##comments##'] = '[[mdlcomments]]';
                    $replacements['##comments:count##'] = '[[mdlcomments:count]]';
                    $replacements['##comments:inline##'] = '[[mdlcomments:inline]]';
                    $replacements['##comments:add##'] = '[[mdlcomments:add]]';
                    $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
                }
            }
        }

        // Transfer entry ratings from internal ratings field to ratingmdl.
        if ($dataforms = $DB->get_records('dataform')) {
            foreach (array_keys($dataforms) as $dataformid) {
                $df = new mod_dataform_dataform($dataformid);
                // For each Dataform instance get context.
                $context = $df->context;
                if ($ratings = $DB->get_records('rating', array('contextid' => $context->id, 'ratingarea' => 'entry'))) {
                    // Add an mdlcomments field to the Dataform instance.
                    $ratingmdl = (object) array(
                        'dataid' => $dataformid,
                        'type' => 'ratingmdl',
                        'name' => 'mdlratings',
                    );
                    $ratingmdlid = $DB->insert_record('dataform_fields', $ratingmdl);
                    $scaleidupdated = false;
                    foreach ($ratings as $rating) {
                        // Update the ratingarea to mdlratings.
                        $DB->set_field('rating', 'ratingarea', 'mdlratings', array('id' => $rating->id));
                        // Update ratingmdl scaleid once.
                        if (!$scaleidupdated) {
                            $DB->set_field('dataform_fields', 'param1', $rating->scaleid, array('id' => $ratingmdlid));
                            $scaleidupdated = true;
                        }
                    }

                    // Replace internal comment patterns with the corresponding commentmdl patterns.
                    $replacements = array();
                    $replacements['##ratings##'] = '[[mdlratings]]';
                    $replacements['##ratings:rate##'] = '[[mdlratings:rate]]';
                    $replacements['##ratings:view##'] = '[[mdlratings:view]]';
                    $replacements['##ratings:viewurl##'] = '[[mdlratings:viewurl]]';
                    $replacements['##ratings:viewinline##'] = '[[mdlratings:viewinline]]';
                    $replacements['##ratings:avg##'] = '[[mdlratings:avg]]';
                    $replacements['##ratings:avg:bar##'] = '[[mdlratings:avg:bar]]';
                    $replacements['##ratings:avg:star##'] = '[[mdlratings:avg:star]]';
                    $replacements['##ratings:count##'] = '[[mdlratings:count]]';
                    $replacements['##ratings:max##'] = '[[mdlratings:max]]';
                    $replacements['##ratings:min##'] = '[[mdlratings:min]]';
                    $replacements['##ratings:sum##'] = '[[mdlratings:sum]]';

                    $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
                }
            }
        }

        // Add grade calc column to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('gradecalc', XMLDB_TYPE_TEXT, 'long', null, null, null, null, 'grademethod');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add formula for existing grade methods.
        $grademethods = array(
            1 => '[[mdlratings:avg]]',
            2 => '[[mdlratings:count]]',
            3 => '[[mdlratings:max]]',
            4 => '[[mdlratings:min]]',
            5 => '[[mdlratings:sum]]'
        );
        if ($dataforms = $DB->get_records('dataform')) {
            foreach ($dataforms as $dataform) {
                if (!empty($dataform->grademethod) and !empty($grademethods[$dataform->grademethod])) {
                    $formula = '='. $grademethods[$dataform->grademethod];
                    $DB->set_field('dataform', 'gradecalc', $formula, array('id' => $dataform->id));
                }
            }
        }

        // Entries can now be displayed only by adding the ##entries## tag to the view template.
        // Add ##entries## tag to view templates.
        if ($views = $DB->get_records('dataform_views')) {
            foreach ($views as $view) {
                // If there are no patterns there is no entry template.
                // Otherwise do nothing.
                if (empty($view->patterns)) {
                    continue;
                }

                if (strpos($view->section, '##entries##') === false) {
                    $section = $view->section. html_writer::tag('div', '##entries##', array('class' => ''));
                    // Adjust the patterns.
                    $patterns = unserialize($view->patterns);
                    if (empty($patterns['view'])) {
                        $patterns['view'] = array('##entries##');
                    } else if (!in_array('##entries##', $patterns['view'])) {
                        $patterns['view'][] = '##entries##';
                    }
                    $patterns = serialize($patterns);
                    $DB->set_field('dataform_views', 'patterns', $patterns, array('id' => $view->id));
                }
            }
        }

        // Remame view filter column to filterid.
        $table = new xmldb_table('dataform_views');
        $field = new xmldb_field('filter', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'groupby');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'filterid');
        }

        // Rename field edits column to editable.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('edits', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL, null, '-1', 'visible');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'editable');
        }

        // Drop sectionpos column.
        $table = new xmldb_table('dataform_views');
        $field = new xmldb_field('sectionpos', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'section');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop grade method column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('grademethod', XMLDB_TYPE_CHAR, '255', null, null, null, '0', 'grade');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop allow late column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('allowlate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'intervalcount');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop notifcation format column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('notificationformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'notification');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop notifcation column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('notification', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'anonymous');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop entries to view column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('entriestoview', XMLDB_TYPE_INTEGER, '8', null, XMLDB_NOTNULL, null, '0', 'entriesrequired');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop approval column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('approval', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'timelimit');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop rating column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('rating', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'grouped');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop rss column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('rss', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'rss');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop rss articles column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('rssarticles', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'singleview');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop singleview column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('singleview', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'singleedit');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop singleedit column.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('singleedit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'grouped');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Drop rules table.
        $table = new xmldb_table('dataform_rules');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Rename entry approved column to state.
        $table = new xmldb_table('dataform_entries');
        $field = new xmldb_field('approved', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'state');
        }

        // Change entry state column precision to 4.
        $table = new xmldb_table('dataform_entries');
        $field = new xmldb_field('state', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }

        // Transfer entrystate from internal field to entrystates.
        if ($dataforms = $DB->get_records('dataform')) {
            foreach (array_keys($dataforms) as $dataformid) {
                $df = mod_dataform_dataform::instance($dataformid);
                if (!$views = $df->view_manager->get_views()) {
                    continue;
                }

                // The Dataform will require update if patterns for fieldid -5 exist.
                $requireupdate = false;
                foreach ($views as $view) {
                    if (!$patterns = $view->patterns) {
                        continue;
                    }

                    // Check both -5 and approve in case upgrading an old version where internal
                    // field ids are verbose.
                    // Note that 'approve' field id will not be replaced.
                    if (!empty($patterns['field'][-5]) or !empty($patterns['field']['approve'])) {
                        $requireupdate = true;
                        break;
                    }
                }

                // Update.
                if ($requireupdate) {
                    // Add an entrystate field to the Dataform instance.
                    $entrystates = (object) array(
                        'dataid' => $dataformid,
                        'type' => 'entrystate',
                        'name' => 'entrystates'
                    );
                    $fieldid = $DB->insert_record('dataform_fields', $entrystates);

                    // Replace internal approve patterns with the corresponding entrystates patterns.
                    $replacements = array();
                    $replacements['##approve##'] = '[[entrystates]]';
                    $replacements['##approved##'] = '[[entrystates:state]]';
                    $replacements['##multiapprove##'] = '[[entrystates:bulkinstate]]';
                    $replacements['##multiapprove:icon##'] = '[[entrystates:bulkinstate]]';
                    $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
                }
            }
        }

        // Replace fieldview action patterns.
        if ($dataforms = $DB->get_records('dataform')) {
            $replacements = array(
                '##selectallnone##' => '##selectallnone##',
                '##multiduplicate##' => '##bulkduplicate##',
                '##multiduplicate:icon##' => '##bulkduplicate##',
                '##multiedit##' => '##bulkedit##',
                '##multiedit:icon##' => '##bulkedit##',
                '##multidelete##' => '##bulkdelete##',
                '##multidelete:icon##' => '##bulkdelete##',
                '##multiexport##' => '##bulkexport##',
                '##multiexport:icon##' => '##bulkexport##',
                '##multiapprove##' => '',
                '##multiapprove:icon##' => '',
                '##bulkinstate##' => '',
            );
            foreach (array_keys($dataforms) as $dataformid) {
                $df = mod_dataform_dataform::instance($dataformid);
                if (!$views = $df->view_manager->get_views()) {
                    continue;
                }
                $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
            }
        }

        // MOVE SEPARATE PARTICIPANTS FROM GROUP MODE TO SETTING.
        // Add individualized column to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('individualized', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'entriesrequired');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update dataform course modules and dataforms.
        $moduleid = $DB->get_field('modules', 'id', array('name' => 'dataform'));
        if ($cms = $DB->get_records('course_modules', array('module' => $moduleid, 'groupmode' => -1))) {
            foreach ($cms as $cmid => $cm) {
                $DB->set_field('course_modules', 'groupmode', 0, array('id' => $cmid));
                $DB->set_field('dataform', 'individualized', 1, array('id' => $cm->instance));
            }
        }

        // Change multiselect field type to selectmulti.
        $DB->set_field('dataform_fields', 'type', 'selectmulti', array('type' => 'multiselect'));

        // Add submission (settings) field to dataform view.
        $table = new xmldb_table('dataform_views');
        $field = new xmldb_field('submission', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'patterns');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Move all appearance settings of picture fields from param4-10 into param4
        // as base64_encode(serialize((object) $appearance)).
        if ($picturefields = $DB->get_records('dataform_fields', array('type' => 'picture'))) {
            $changes = array(
                'param4' => 'dispw',
                'param5' => 'disph',
                'param6' => 'dispu',
                'param7' => 'maxw',
                'param8' => 'maxh',
                'param9' => 'thumbw',
                'param10' => 'thumbh',
            );

            foreach ($picturefields as $field) {
                $appearance = array();

                if ($field->param6 == 'px') {
                    $field->param6 = null;
                }

                foreach ($changes as $param => $var) {
                    if ($field->$param) {
                        $appearance[$var] = $field->$param;
                        $field->$param = null;
                    }
                }
                if ($appearance) {
                    $field->param4 = base64_encode(serialize((object) $appearance));
                    $DB->update_record('dataform_fields', $field);
                }
            }
        }

        // Replace pagingbar pattern to paging:bar.
        if ($dataforms = $DB->get_records('dataform')) {
            $replacements = array(
                '##pagingbar##' => '##paging:bar##',
            );
            foreach (array_keys($dataforms) as $dataformid) {
                $df = mod_dataform_dataform::instance($dataformid);
                if (!$views = $df->view_manager->get_views()) {
                    continue;
                }
                $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
            }
        }

        // Enclose content of selectmulti and checkbox fields with #...#.
        // This is done to allow the search filter to work on these fields.
        list($intype, $params) = $DB->get_in_or_equal(array('selectmulti', 'checkbox'));
        if ($fields = $DB->get_records_select('dataform_fields', " type $intype ", $params)) {
            // Get the content fields.
            list($inids, $params) = $DB->get_in_or_equal(array_keys($fields));
            if ($contents = $DB->get_records_select('dataform_contents', " fieldid $inids ", $params)) {
                foreach ($contents as $content) {
                    if (empty($content->content)) {
                        // Shouldn't be but just in case.
                        $DB->delete_records('dataform_contents', array('id' => $content->id));
                    } else {
                        $content->content = '#'. $content->content. '#';
                        $DB->update_record('dataform_contents', $content);
                    }
                }
            }
        }

        // Replace entrytime, entrygroup, entryauthor, entryactions and entryid patterns.
        if ($dataforms = $DB->get_records('dataform')) {
            $replacements = array();

            // Entry id.
            $replacements["##entryid##"] = "[[entryid]]";

            // Entry actions.
            $fieldname = get_string('fieldname', 'dataformfield_entryactions');
            $pvars = array('actionmenu', 'edit', 'delete', 'select', 'export', 'duplicate');
            foreach ($pvars as $pvar) {
                $replacements["##$pvar##"] = "[[$fieldname:$pvar]]";
            }
            $pvars = array('anchor', 'more', 'moreurl');
            foreach ($pvars as $pvar) {
                $replacements["##$pvar##"] = "[[$fieldname:$pvar]]";
            }

            // Hidden patterns for view designated more and edit.
            if ($views = $DB->get_records('dataform_views')) {
                foreach ($views as $view) {
                    $viewname = $view->name;
                    $replacements["##more:$viewname##"] = "[[$fieldname:more:$viewname]]";
                    $replacements["##moreurl:$viewname##"] = "[[$fieldname:moreurl:$viewname]]";
                    $replacements["##edit:$viewname##"] = "[[$fieldname:edit:$viewname]]";
                }
            }

            // Entry author.
            $fieldname = get_string('fieldname', 'dataformfield_entryauthor');
            foreach (explode(',', user_picture::fields()) as $internalname) {
                $replacements["##author:{$internalname}##"] = "[[$fieldname:$internalname]]";
            }
            foreach (array('username', 'name', 'edit', 'picturelarge') as $pvar) {
                $patterns["##author:$pvar##"] = "[[$fieldname:$pvar]]";
            }

            // Entry group.
            $fieldname = get_string('fieldname', 'dataformfield_entrygroup');
            foreach (array('id', 'name', 'picture', 'picturelarge', 'edit') as $item) {
                $replacements["##group:$item##"] = "[[$fieldname:$item]]";
            }

            // Entry time.
            $fieldname = get_string('fieldname', 'dataformfield_entrytime');
            foreach (array('timecreate', 'timemodified') as $timevar) {
                $replacements["##$timevar##"] = "[[$fieldname:$timevar]]";
                $replacements["##$timevar:date##"] = "[[$fieldname:$timevar:date]]";
                $replacements["##$timevar:minute##"] = "[[$fieldname:$timevar:minute]]";
                $replacements["##$timevar:hour##"] = "[[$fieldname:$timevar:hour]]";
                $replacements["##$timevar:day##"] = "[[$fieldname:$timevar:day]]";
                $replacements["##$timevar:d##"] = "[[$fieldname:$timevar:d]]";
                $replacements["##$timevar:week##"] = "[[$fieldname:$timevar:week]]";
                $replacements["##$timevar:month##"] = "[[$fieldname:$timevar:month]]";
                $replacements["##$timevar:m##"] = "[[$fieldname:$timevar:m]]";
                $replacements["##$timevar:year##"] = "[[$fieldname:$timevar:year]]";
                $replacements["##$timevar:Y##"] = "[[$fieldname:$timevar:Y]]";
            }

            // Csv export patterns.
            $replacements["##export:all##"] = "##exportall##";
            $replacements["##export:page##"] = "##exportpage##";

            foreach (array_keys($dataforms) as $dataformid) {
                $df = mod_dataform_dataform::instance($dataformid);
                if (!$views = $df->view_manager->get_views()) {
                    continue;
                }
                $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
            }
        }

        // Dataformview->visible changed to 0|1, so adjust any instance
        // that is not 0 to 1.
        if ($views = $DB->get_records('dataform_views')) {
            foreach ($views as $view) {
                if ($view->visible and $view->visible != 1) {
                    $DB->set_field('dataform_views', 'visible', 1, array('id' => $view->id));
                }
            }
        }

        // Add completionentries column to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('completionentries', XMLDB_TYPE_INTEGER, '9', null, XMLDB_NOTNULL, null, '0', 'defaultfilter');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, $newversion, 'dataform');
    }

    return true;
}

function xmldb_dataform_upgrade_2014051301($dbman, $oldversion) {
    global $CFG, $DB;

    $newversion = 2014051301;
    if ($oldversion < $newversion) {
        // Add completionspecificgrade column to dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field(
            'completionspecificgrade',
            XMLDB_TYPE_INTEGER,
            '9',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'completionentries'
        );
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, $newversion, 'dataform');
    }

    return true;
}

function xmldb_dataform_upgrade_2014111000($dbman, $oldversion) {
    global $CFG, $DB;

    $newversion = 2014111000;
    if ($oldversion < $newversion) {
        // Replace field template pattern from [[fieldname@]] to [[T@fieldname]].
        if ($dataforms = $DB->get_records('dataform')) {
            foreach (array_keys($dataforms) as $dataformid) {
                $sqlparams = array('dataid' => $dataformid);

                // Get field names of the dataform fields.
                if (!$fieldnames = $DB->get_records_menu('dataform_fields', $sqlparams, '', 'id,name')) {
                    continue;
                }

                // Must have views to continue.
                if (!$DB->record_exists('dataform_views', $sqlparams)) {
                    continue;
                }

                $df = mod_dataform_dataform::instance($dataformid);
                $replacements = array();

                foreach ($fieldnames as $fieldname) {
                    $replacements["[[$fieldname@]]"] = "[[T@$fieldname]]";
                }

                $df->view_manager->replace_patterns_in_views(array_keys($replacements), $replacements);
            }
        }

        // Enable existing field plugins.
        $type = 'dataformfield';
        $enabled = array_keys(core_component::get_plugin_list($type));
        set_config("enabled_$type", implode(',', $enabled), 'mod_dataform');

        // Enable existing view plugins.
        $type = 'dataformview';
        $enabled = array_keys(core_component::get_plugin_list($type));
        set_config("enabled_$type", implode(',', $enabled), 'mod_dataform');

        // Add defaultcontentmode column to dataform_fields.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('defaultcontentmode', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0', 'label');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add defaultcontent column to dataform_fields.
        $table = new xmldb_table('dataform_fields');
        $field = new xmldb_field('defaultcontent', XMLDB_TYPE_TEXT, null, null, null, null, null, 'defaultcontentmode');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, $newversion, 'dataform');
    }

    return true;
}

function xmldb_dataform_upgrade_2015051100($dbman, $oldversion, $t = '') {
    global $CFG, $DB;

    list(, , , $newversion) = explode('_', __FUNCTION__);
    $newversion = $t ? (double) ("$newversion.$t") : $newversion;
    if ($oldversion < $newversion) {
        // Change gradecalc column to gradeitems dataform.
        $table = new xmldb_table('dataform');
        $field = new xmldb_field('gradecalc', XMLDB_TYPE_TEXT, null, null, null, null, null, 'grade');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'gradeitems');
        }

        // Convert gradecalc content to new content.
        if ($dataforms = $DB->get_records('dataform')) {
            foreach ($dataforms as $dataform) {
                if (empty($dataform->gradeitems)) {
                    continue;
                }
                if (@unserialize($dataform->gradeitems) !== false) {
                    continue;
                }

                // We have an old style calc.
                $calc = $dataform->gradeitems;
                // Convert to new style.
                $dataform->gradeitems = serialize(array(0 => array('ca' => $calc)));

                $DB->update_record('dataform', $dataform);
            }
        }

        // Dataform savepoint reached.
        upgrade_mod_savepoint(true, $newversion, 'dataform');
    }

    return true;
}
