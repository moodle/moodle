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
 * Configure provider instance order settings.
 *
 * @package     core_ai
 * @copyright   Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '../../config.php');

require_login(autologinguest: false);
require_capability('moodle/site:config', context_system::instance());

require_sesskey();

$PAGE->set_url('/ai/configure_providers.php');

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', '', PARAM_INT);

$manager = \core\di::get(\core_ai\manager::class);
$providerrecord = $manager->get_provider_records(filter: ['id' => $id]);

$returnurl = new moodle_url('/admin/settings.php?section=aiprovider');

if (empty($providerrecord) || !$providerrecord) {
    throw new moodle_exception('error:providernotfound', 'core_ai', $returnurl);
}

if (empty($action) || !in_array($action, \core\plugininfo\aiprovider::get_provider_actions())) {
    throw new moodle_exception('error:actionnotfound', 'core_ai', $returnurl, $action);
}

switch ($action) {
    case \core\plugininfo\aiprovider::UP:
        $manager->change_provider_order($id, \core\plugininfo\aiprovider::MOVE_UP);
        break;
    case \core\plugininfo\aiprovider::DOWN:
        $manager->change_provider_order($id, \core\plugininfo\aiprovider::MOVE_DOWN);
        break;
}

redirect($returnurl);
