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
 * Show current cache usage (number of items, size of caches).
 *
 * @package core_cache
 * @copyright 2021 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');

require_once($CFG->dirroot . '/lib/adminlib.php');

admin_externalpage_setup('cacheusage');
$adminhelper = \core_cache\factory::instance()->get_administration_display_helper();
raise_memory_limit(MEMORY_EXTRA);

$samples = optional_param('samples', 50, PARAM_INT);

// Just for safety reasons, stop people choosing a stupid number.
if ($samples > 1000) {
    $samples = 1000;
}

// Get the actual data.
$usage = $adminhelper->get_usage($samples);

// Set up the renderer and organise data to render.
$renderer = $PAGE->get_renderer('core_cache');
[$table, $summarytable] = $renderer->usage_tables($usage);
$form = new \core_cache\output\usage_samples_form();

echo $OUTPUT->header();
echo $renderer->usage_page($table, $summarytable, $form);
echo $OUTPUT->footer();
