<?php
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
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file defines the global basiclti administration form
 *
 * @package lti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $PAGE, $CFG;

require_once($CFG->dirroot.'/mod/lti/locallib.php');

function blti_get_tool_table($tools, $id){
    global $CFG, $USER;
    $html = '';
    
    $typename = get_string('typename', 'lti');
    $baseurl = get_string('baseurl', 'lti');
    $action = get_string('action', 'lti');
    $createdon = get_string('createdon', 'lti');
    
    if (!empty($tools)) {
        if($id == 'lti_configured'){
            $html .= '<a style="margin-top:.25em" href="'.$CFG->wwwroot.'/mod/lti/typessettings.php?action=add&amp;sesskey='.$USER->sesskey.'">'.get_string('addtype', 'lti').'</a>';
        }
        
        $html .= <<<HTML
        <div id="{$id}_container" style="margin-top:.5em;margin-bottom:.5em">
            <table id="{$id}_tools">
                <thead>
                    <tr>
                        <th>$typename</th>
                        <th>$baseurl</th>
                        <th>$createdon</th>
                        <th>$action</th>
                    </tr>
                </thead>
HTML;
        
        foreach ($tools as $type) {
            $date = userdate($type->timecreated);
            $accept = get_string('accept', 'lti');
            $update = get_string('update', 'lti');
            $delete = get_string('delete', 'lti');
            
            $accepthtml = <<<HTML
                <a class="editing_accept" href="{$CFG->wwwroot}/mod/lti/typessettings.php?action=accept&amp;id={$type->id}&amp;sesskey={$USER->sesskey}&amp;tab={$id}" title="{$accept}">
                    <img class="iconsmall" alt="{$accept}" src="{$CFG->wwwroot}/pix/t/clear.gif"/>
                </a>
HTML;

            $deleteaction = 'delete';
                    
            if($type->state == LTI_TOOL_STATE_CONFIGURED){
                $accepthtml = '';
            }
            
            if($type->state != LTI_TOOL_STATE_REJECTED) {
                $deleteaction = 'reject';
                $delete = get_string('reject', 'lti');
            }
                    
            $html .= <<<HTML
            <tr>
                <td>
                    {$type->name}
                </td>
                <td>
                    {$type->baseurl}
                </td>
                <td>
                    {$date}
                </td>
                <td align="center">
                    {$accepthtml}
                    <a class="editing_update" href="{$CFG->wwwroot}/mod/lti/typessettings.php?action=update&amp;id={$type->id}&amp;sesskey={$USER->sesskey}&amp;tab={$id}" title="{$update}">
                        <img class="iconsmall" alt="{$update}" src="{$CFG->wwwroot}/pix/t/edit.gif"/>
                    </a>
                    <a class="editing_delete" href="{$CFG->wwwroot}/mod/lti/typessettings.php?action={$deleteaction}&amp;id={$type->id}&amp;sesskey={$USER->sesskey}&amp;tab={$id}" title="{$delete}">
                        <img class="iconsmall" alt="{$delete}" src="{$CFG->wwwroot}/pix/t/delete.gif"/>
                    </a>
                </td>
            </tr>
HTML;
        }
        $html .= '</table></div>';
    } else {
        $html .= get_string('no_' . $id, 'lti');
    }
    
    return $html;
}

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/lti/locallib.php');

    $configuredtoolshtml = '';
    $pendingtoolshtml = '';
    $rejectedtoolshtml = '';

    $active = get_string('active', 'lti');
    $pending = get_string('pending', 'lti');
    $rejected = get_string('rejected', 'lti');
    $typename = get_string('typename', 'lti');
    $baseurl = get_string('baseurl', 'lti');
    $action = get_string('action', 'lti');
    $createdon = get_string('createdon', 'lti');
    
    $types = lti_filter_get_types();
    
    $configuredtools = array_filter($types, function($value){
        return $value->state == LTI_TOOL_STATE_CONFIGURED;
    });
    
    $configuredtoolshtml = blti_get_tool_table($configuredtools, 'lti_configured');

    $pendingtools = array_filter($types, function($value){
        return $value->state == LTI_TOOL_STATE_PENDING;
    });
    
    $pendingtoolshtml = blti_get_tool_table($pendingtools, 'lti_pending');
    
    $rejectedtools = array_filter($types, function($value){
        return $value->state == LTI_TOOL_STATE_REJECTED;
    });
    
    $rejectedtoolshtml = blti_get_tool_table($rejectedtools, 'lti_rejected');
    
    $tab = optional_param('tab', '', PARAM_ALPHAEXT);
    $activeselected = '';
    $pendingselected = '';
    $rejectedselected = '';
    switch($tab){
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
    
    $template = <<<HTML
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

<script type='text/javascript'>
//<![CDATA[
    (function(){
        //If javascript is disabled, they will just see the three tabs one after another
        var lti_tab_heading = document.getElementById('lti_tab_heading');
        lti_tab_heading.style.display = '';

        new YAHOO.widget.TabView('lti_tabs');
    
        var setupTools = function(id, sort){
            var lti_tools = YAHOO.util.Dom.get(id + "_tools");
    
            if(lti_tools){
                var dataSource = new YAHOO.util.DataSource(lti_tools);
    
                var configuredColumns = [
                    {key:"name", label:"$typename", sortable:true},
                    {key:"baseURL", label:"$baseurl", sortable:true},
                    {key:"timecreated", label:"$createdon", sortable:true, formatter:YAHOO.widget.DataTable.formatDate},
                    {key:"action", label:"$action"}
                ];

                dataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
                dataSource.responseSchema = {
                    fields: [
                        {key:"name"},
                        {key:"baseURL"},
                        {key:"timecreated", parser:"date"},
                        {key:"action"}
                    ]
                };

                new YAHOO.widget.DataTable(id + "_container", configuredColumns, dataSource,
                    {
                        sortedBy: sort
                    }
                );
            }
        };

        setupTools('lti_configured', {key:"name", dir:"asc"});
        setupTools('lti_pending', {key:"timecreated", dir:"desc"});
        setupTools('lti_rejected', {key:"timecreated", dir:"desc"});
    })();
//]]
</script>
HTML;
    
    $PAGE->requires->yui2_lib('tabview');
    $PAGE->requires->yui2_lib('datatable');
   
    $settings->add(new admin_setting_heading('lti_types', get_string('external_tool_types', 'lti'), $template /*  $str*/));
}
