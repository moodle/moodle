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
 * This file keeps track of upgrades to the lightboxgallery module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package   mod_lightboxgallery
 * @copyright 2011 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * xmldb_lightboxgallery_upgrade
 *
 * @param int $oldversion
 * @return bool
 */

function xmldb_lightboxgallery_upgrade($oldversion=0) {

    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2007111400) {
        $table = new xmdbl_table('lightboxgallery');

        // Add perpage field.
        $field = new xmldb_field('perpage', XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add comments field.
        $field = new xmldb_field('comments', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'perpage');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Create new lightboxgallery_comments table.
        $table = new xmldb_table('lightboxgallery_comments');

        if (!$dbman->table_exists($table)) {
            $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->addField($field);

            $field = new xmldb_field('gallery', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->addField($field);

            $field = new xmldb_field('user', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->addField($field);

            $field = new xmldb_field('comment', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
            $table->addField($field);

            $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->addField($field);

            $key = new xmldb_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKey($key);

            $table->add_index('gallery', XMLDB_INDEX_NOTUNIQUE, array('gallery'));

            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2007111400, 'lightboxgallery');
    }

    if ($oldversion < 2007121700) {
        $table = new xmldb_table('lightboxgallery');

        // Insert extinfo field into lightboxgallery.
        $field = new xmldb_field('extinfo', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'comments');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Create lightboxgallery_captions table.
        $table = new xmldb_table('lightboxgallery_captions');

        if (!$dbman->table_exists($table)) {
            $field = new xmldb_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->addField($field);

            $field = new xmldb_field('gallery', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->addField($field);

            $field = new xmldb_field('image', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->addField($field);

            $field = new xmldb_field('caption', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
            $table->addField($field);

            $key = new xmldb_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKey($key);

            $table->add_index('gallery', XMLDB_INDEX_NOTUNIQUE, array('gallery'));

            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2007121700, 'lightboxgallery');
    }

    if ($oldversion < 2008110600) {
        $table = new xmldb_table('lightboxgallery');

        // Insert public, rss, autoresize, resize fields into lightboxgallery.
        $newfields = array('public', 'rss', 'autoresize', 'resize');
        $previousfield = 'comments';
        foreach ($newfields as $newfield) {
            $field = new xmldb_field($newfield, XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', $previousfield);
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
                $previousfield = $newfield;
            }
        }

        $table = new xmldb_table('lightboxgallery_comments');

        // Rename user field to userid in lightboxgallery_comments (for postgres).
        $field = new xmldb_field('user', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'userid');
        }

        $table = new xmldb_table('lightboxgallery_captions');

        // Rename caption field to description and insert metatype field in lightboxgallery_captions.
        $field = new xmldb_field('caption', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, 'image');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'description');
        }

        $field = new xmldb_field('metatype',
                                 XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, array('caption', 'tag'), 'caption', 'image');
        if ($dbman->table_exists($table) && !$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Rename table lightboxgallery_captions to lightboxgallery_image_meta.
        $dbman->rename_table($table, 'lightboxgallery_image_meta');
        upgrade_mod_savepoint(true, 2008110600, 'lightboxgallery');
    }

    if ($oldversion < 2009051200) {
        $table = new xmldb_table('lightboxgallery');

        // Rename public field to ispublic in lightboxgallery (for mssql).
        $field = new xmldb_field('public', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'comments');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'ispublic');
        }
        upgrade_mod_savepoint(true, 2009051200, 'lightboxgallery');
    }

    if ($oldversion < 2011040800) {
        $table = new xmldb_table('lightboxgallery');

        // Add perpage field.
        $field = new xmldb_field('perrow', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '4', 'perpage');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // File migration, add entries in file table that points at the legacy files.
        // Get list of files based on path.
        if ($galleries = $DB->get_records('lightboxgallery')) {
            foreach ($galleries as $gallery) {
                if (!$cm = get_coursemodule_from_instance('lightboxgallery', $gallery->id, $gallery->course, false)) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                $coursecontext = context_course::instance($gallery->course);

                // Add files to lightbox area, iterate over the legacy files.
                $fs = get_file_storage();
                if ($storedfiles = $fs->get_area_files($coursecontext->id, 'course', 'legacy')) {
                    foreach ($storedfiles as $file) {
                        $path = '/'.$gallery->folder;
                        if ($gallery->folder != '') {
                            $path .= '/';
                        }
                        if (substr($file->get_mimetype(), 0, 6) != 'image/' ||
                            substr($file->get_filepath(), -8, 8) == '/_thumb/' ||
                            $file->get_filepath() != $path) {
                            continue;
                        }
                        // Insert as lightbox file.
                        $settings = new stdClass();
                        $settings->contextid = $context->id;
                        $settings->component = 'mod_lightboxgallery';
                        $settings->filearea = 'gallery_images';
                        $settings->filepath = '/';
                        $fs->create_file_from_storedfile($settings, $file);
                    }
                }
            }
        }
        upgrade_mod_savepoint(true, 2011040800, 'lightboxgallery');
    }

    if ($oldversion < 2011071100) {
        // Switch out gallery description field for standard introeditor.
        $table = new xmldb_table('lightboxgallery');

        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'extinfo');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'intro');
        }

        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2011071100, 'lightboxgallery');
    }

    if ($oldversion < 2011111600) {
        $table = new xmldb_table('lightboxgallery');

        $field = new xmldb_field('captionfull', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'extinfo');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, 'extinfo');
        }

        $field = new xmldb_field('captionpos', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'captionfull');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, 'captionfull');
        }

        upgrade_mod_savepoint(true, 2011111600, 'lightboxgallery');
    }

    if ($oldversion < 2013051300) {
        $table = new xmldb_table('lightboxgallery_comments');

        $field = new xmldb_field('comment', XMLDB_TYPE_TEXT, null, null, null, null, null, 'userid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'commenttext');
        }

        upgrade_mod_savepoint(true, 2013051300, 'lightboxgallery');
    }

    if ($oldversion < 2017070700) {
        $table = new xmldb_table('lightboxgallery');
        $field = new xmldb_field('folder');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2017070700, 'lightboxgallery');
    }

    return true;
}
