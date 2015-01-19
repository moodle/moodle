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
 * Migration page.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/local/kaltura/migration_form.php');
require_once('locallib.php');
require_once('API/KalturaClient.php');
require_once($CFG->libdir.'/xmldb/xmldb_object.php');
require_once($CFG->libdir.'/xmldb/xmldb_table.php');
require_once('migrationlib.php');

$url = new moodle_url('/local/kaltura/migration.php');
$context = context_system::instance();
$heading = get_string('migration_header', 'local_kaltura');
$site = get_site();

$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('localplugins'));
$PAGE->navbar->add(get_string('pluginname', 'local_kaltura'), new moodle_url('/admin/settings.php', array('section' => 'local_kaltura')));
$PAGE->navbar->add(get_string('migration_header', 'local_kaltura'));

$PAGE->set_url($url);
$PAGE->set_context($context);

$PAGE->set_pagelayout('standard');
$PAGE->set_pagetype('local-kaltura-migration');
$PAGE->set_title($heading);
$PAGE->set_heading($site->fullname);

require_login(null, false);

require_capability('local/kaltura:migrate_data', $context);

$url = new moodle_url('/admin/settings.php', array('section' => 'local_kaltura'));

$form = new local_kaltura_migration_form();
$redirectmessage = '';

if ($data = $form->get_data()) {
    // User hit cancel. Redirect them back to the settings page.
    if (isset($data->cancel)) {
        redirect($url);
    }

    require_sesskey();
    $migrationstats = new local_kaltura_migration_progress();

    // User hit submit button.  Check for records since the configured date.
    if (isset($data->submitbutton)) {
        if(local_kaltura_get_channels_id(local_kaltura_get_kaltura_client(), $data->kafcategory) === false)
        {
            $url = new moodle_url('/admin/settings.php', array('section' => 'local_kaltura'));
            notice("Selected target root category does not have a KAF structure (subcategory '>site>channels' is missing)", $url);
        }
        // Set the migration start time and initialize the KAF root category id.
        if (0 == local_kaltura_migration_progress::get_migrationstarted()) {
            local_kaltura_migration_progress::init_migrationstarted();
            local_kaltura_migration_progress::set_kafcategoryrootid($data->kafcategory);
        }

        // An array mapping of old categories to new categories.
        $cachedcategories = array();

        // Migrate entries that belong to categories under the old rood category.
        list($categoryentries, $cachedcategories) = local_kaltura_move_entries_to_kaf_category_tree($data->kafcategory, 1);
        // Migrate entries that are associated with the old profile id and contain metadata.
        $metadataentries = local_kaltura_move_metadata_entries_to_kaf_category_tree($data->kafcategory, 1);
        $redirectmessage = get_string('migration_has_stopped', 'local_kaltura');

        // Migrate video presentation entries.
        local_kaltura_migrate_video_presentation_entries($data->kafcategory, $cachedcategories);

        // Update the Kaltura activities.
        local_kaltura_update_activities();
        local_kaltura_set_activities_entries_to_categories();

        // If both variables are null, then there is nother more to migrate.
        if (is_null($categoryentries) && is_null($metadataentries)) {
            // Hide migration is needed message on settings page.
            set_config('migration_yes', 0, KALTURA_PLUGIN_NAME);
            $redirectmessage = get_string('migration_complete_redirect', 'local_kaltura');
        }
    } else if (isset($data->startover)) {
        local_kaltura_migration_progress::reset_all();
        $redirectmessage = get_string('migration_start_over_redirect', 'local_kaltura');
    }

    $migrationurl = new moodle_url('/local/kaltura/migration.php');
    redirect($migrationurl, $redirectmessage, 5);
}

local_kaltura_retrieve_repository_settings();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('migration_header', 'local_kaltura'));
$form->display();
echo $OUTPUT->footer();
