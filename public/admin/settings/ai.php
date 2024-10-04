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
 * Adds settings links to admin tree.
 *
 * AI gets top billing in general because it's the future.
 *
 * @package   core_admin
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Add settings page for AI provider settings.
    $providers = new admin_settingpage('aiprovider', new lang_string('aiproviders', 'ai'));
    $providers->add(new admin_setting_heading('availableproviders',
        get_string('availableproviders', 'core_ai'),
        get_string('availableproviders_desc', 'core_ai')));
    // Add call to action to add a new provider.
    $providers->add(new \core_admin\admin\admin_setting_template_render(
        name: 'addnewprovider',
        templatename: 'core_ai/admin_add_provider',
        context: ['addnewproviderurl' => new moodle_url('/ai/configure.php')]
    ));

    $providers->add(new \core_ai\admin\admin_setting_provider_manager(
            'aiprovider',
            \core_ai\table\aiprovider_management_table::class,
            'manageaiproviders',
            new lang_string('manageaiproviders', 'core_ai'),
    ));
    $ADMIN->add('ai', $providers);

    // Add settings page for AI placement settings.
    $placements = new admin_settingpage('aiplacement', new lang_string('aiplacements', 'ai'));
    $placements->add(new admin_setting_heading('availableplacements',
            get_string('availableplacements', 'core_ai'),
            get_string('availableplacements_desc', 'core_ai')));
    $placements->add(new \core_admin\admin\admin_setting_plugin_manager(
            'aiplacement',
            \core_ai\table\aiplacement_management_table::class,
            'manageaiplacements',
            new lang_string('manageaiplacements', 'core_ai'),
    ));
    $ADMIN->add('ai', $placements);

    // Load settings for all placements.
    $plugins = core_plugin_manager::instance()->get_plugins_of_type('aiplacement');
    foreach ($plugins as $plugin) {
        /** @var \core\plugininfo\aiprovider $plugin */
        $plugin->load_settings($ADMIN, 'ai', $hassiteconfig);
    }
}

// AI reports category.
$ADMIN->add('reports', new admin_category('aireports', get_string('aireports', 'core_ai')));
// Add AI policy acceptance report.
$aipolicyacceptance = new admin_externalpage(
    'aipolicyacceptancereport',
    get_string('aipolicyacceptance', 'core_ai'),
    new moodle_url('/ai/policy_acceptance_report.php'),
    'moodle/ai:viewaipolicyacceptancereport'
);
$ADMIN->add('aireports', $aipolicyacceptance);
// Add AI usage report.
$aiusage = new admin_externalpage(
    'aiusagereport',
    get_string('aiusage', 'core_ai'),
    new moodle_url('/ai/usage_report.php'),
    'moodle/ai:viewaiusagereport',
);
$ADMIN->add('aireports', $aiusage);
