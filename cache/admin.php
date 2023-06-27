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
 * The administration and management interface for the cache setup and configuration.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->dirroot.'/lib/adminlib.php');
require_once($CFG->dirroot.'/cache/locallib.php');
require_once($CFG->dirroot.'/cache/forms.php');

// The first time the user visits this page we are going to reparse the definitions.
// Just ensures that everything is up to date.
// We flag is session so that this only happens once as people are likely to hit
// this page several times if making changes.
if (empty($SESSION->cacheadminreparsedefinitions)) {
    cache_helper::update_definitions();
    $SESSION->cacheadminreparsedefinitions = true;
}

$action = optional_param('action', null, PARAM_ALPHA);

admin_externalpage_setup('cacheconfig');
$adminhelper = cache_factory::instance()->get_administration_display_helper();

$notifications = array();
// Empty array to hold any form information returned from actions.
$forminfo = [];

// Handle page actions in admin helper class.
if (!empty($action) && confirm_sesskey()) {
    $forminfo = $adminhelper->perform_cache_actions($action, $forminfo);
}

// Add cache store warnings to the list of notifications.
// Obviously as these are warnings they are show as failures.
foreach (cache_helper::warnings(core_cache\administration_helper::get_store_instance_summaries()) as $warning) {
    $notifications[] = array($warning, false);
}

// Decide on display mode based on returned forminfo.
$mform = array_key_exists('form', $forminfo) ? $forminfo['form'] : null;
$title = array_key_exists('title', $forminfo) ? $forminfo['title'] : new lang_string('cacheadmin', 'cache');

$PAGE->set_title($title);
$PAGE->set_heading($SITE->fullname);
/* @var core_cache_renderer $renderer */
$renderer = $PAGE->get_renderer('core_cache');

echo $renderer->header();
echo $renderer->heading($title);
echo $renderer->notifications($notifications);

if ($mform instanceof moodleform) {
    $mform->display();
} else {
    // Handle main page definition in admin helper class.
    echo $adminhelper->generate_admin_page($renderer);
}

echo $renderer->footer();
