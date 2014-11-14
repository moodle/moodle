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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file defines the global lti administration form
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/*
 * @var admin_settingpage $settings
 */
$modltifolder = new admin_category('modltifolder', new lang_string('pluginname', 'mod_lti'), $module->is_enabled() === false);
$ADMIN->add('modsettings', $modltifolder);
$settings->visiblename = new lang_string('manage_tools', 'mod_lti');
$ADMIN->add('modltifolder', $settings);
$ADMIN->add('modltifolder', new admin_externalpage('ltitoolproxies',
        get_string('manage_tool_proxies', 'lti'),
        new moodle_url('/mod/lti/toolproxies.php')));

foreach (core_plugin_manager::instance()->get_plugins_of_type('ltisource') as $plugin) {
    /*
     * @var \mod_lti\plugininfo\ltisource $plugin
     */
    $plugin->load_settings($ADMIN, 'modltifolder', $hassiteconfig);
}

$toolproxiesurl = new moodle_url('/mod/lti/toolproxies.php');
$toolproxiesurl = $toolproxiesurl->out();

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    $configuredtoolshtml = '';
    $pendingtoolshtml = '';
    $rejectedtoolshtml = '';

    $active = get_string('active', 'lti');
    $pending = get_string('pending', 'lti');
    $rejected = get_string('rejected', 'lti');

    // Gather strings used for labels in the inline JS.
    $PAGE->requires->strings_for_js(
        array(
            'typename',
            'baseurl',
            'action',
            'createdon'
        ),
        'mod_lti'
    );

    $types = lti_filter_get_types(get_site()->id);

    $configuredtools = lti_filter_tool_types($types, LTI_TOOL_STATE_CONFIGURED);

    $configuredtoolshtml = lti_get_tool_table($configuredtools, 'lti_configured');

    $pendingtools = lti_filter_tool_types($types, LTI_TOOL_STATE_PENDING);

    $pendingtoolshtml = lti_get_tool_table($pendingtools, 'lti_pending');

    $rejectedtools = lti_filter_tool_types($types, LTI_TOOL_STATE_REJECTED);

    $rejectedtoolshtml = lti_get_tool_table($rejectedtools, 'lti_rejected');

    $tab = optional_param('tab', '', PARAM_ALPHAEXT);
    $activeselected = '';
    $pendingselected = '';
    $rejectedselected = '';
    switch ($tab) {
        case 'lti_pending':
            $pendingselected = 'class="selected"';
            break;
        case 'lti_rejected':
            $rejectedselected = 'class="selected"';
            break;
        default:
            $activeselected = 'class="selected"';
            break;
    }
    $addtype = get_string('addtype', 'lti');
    $config = get_string('manage_tool_proxies', 'lti');

    $addtypeurl = "{$CFG->wwwroot}/mod/lti/typessettings.php?action=add&amp;sesskey={$USER->sesskey}";

    $template = <<< EOD
<div id="lti_tabs" class="yui-navset">
    <ul id="lti_tab_heading" class="yui-nav" style="display:none">
        <li {$activeselected}>
            <a href="#tab1">
                <em>$active</em>
            </a>
        </li>
        <li {$pendingselected}>
            <a href="#tab2">
                <em>$pending</em>
            </a>
        </li>
        <li {$rejectedselected}>
            <a href="#tab3">
                <em>$rejected</em>
            </a>
        </li>
    </ul>
    <div class="yui-content">
        <div>
            <div><a style="margin-top:.25em" href="{$addtypeurl}">{$addtype}</a></div>
            $configuredtoolshtml
        </div>
        <div>
            $pendingtoolshtml
        </div>
        <div>
            $rejectedtoolshtml
        </div>
    </div>
</div>

<script type="text/javascript">
//<![CDATA[
    YUI().use('yui2-tabview', 'yui2-datatable', function(Y) {
        //If javascript is disabled, they will just see the three tabs one after another
        var lti_tab_heading = document.getElementById('lti_tab_heading');
        lti_tab_heading.style.display = '';

        new Y.YUI2.widget.TabView('lti_tabs');

        var setupTools = function(id, sort){
            var lti_tools = Y.YUI2.util.Dom.get(id + '_tools');

            if(lti_tools){
                var dataSource = new Y.YUI2.util.DataSource(lti_tools);

                var configuredColumns = [
                    {key:'name', label: M.util.get_string('typename', 'mod_lti'), sortable: true},
                    {key:'baseURL', label: M.util.get_string('baseurl', 'mod_lti'), sortable: true},
                    {key:'timecreated', label: M.util.get_string('createdon', 'mod_lti'), sortable: true},
                    {key:'action', label: M.util.get_string('action', 'mod_lti')}
                ];

                dataSource.responseType = Y.YUI2.util.DataSource.TYPE_HTMLTABLE;
                dataSource.responseSchema = {
                    fields: [
                        {key:'name'},
                        {key:'baseURL'},
                        {key:'timecreated'},
                        {key:'action'}
                    ]
                };

                new Y.YUI2.widget.DataTable(id + '_container', configuredColumns, dataSource,
                    {
                        sortedBy: sort
                    }
                );
            }
        };

        setupTools('lti_configured_tools', {key:'name', dir:'asc'});
        setupTools('lti_pending_tools', {key:'timecreated', dir:'desc'});
        setupTools('lti_rejected_tools', {key:'timecreated', dir:'desc'});
    });
//]]
</script>
EOD;
    $settings->add(new admin_setting_heading('lti_types', new lang_string('external_tool_types', 'lti') .
        $OUTPUT->help_icon('main_admin', 'lti'), $template));
}

// Tell core we already added the settings structure.
$settings = null;

