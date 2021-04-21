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
 * Output rendering for the plugin.
 *
 * @package     tool_oauth2
 * @copyright   2017 Damyon Wiese
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_oauth2\output;

use plugin_renderer_base;
use html_table;
use html_table_cell;
use html_table_row;
use html_writer;
use core\oauth2\issuer;
use core\oauth2\api;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Implements the plugin renderer
 *
 * @copyright 2017 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * This function will render one beautiful table with all the issuers.
     *
     * @param \core\oauth2\issuer[] $issuers - list of all issuers.
     * @return string HTML to output.
     */
    public function issuers_table($issuers) {
        global $CFG;

        $table = new html_table();
        $table->head  = [
            get_string('name'),
            get_string('issuerusedforlogin', 'tool_oauth2'),
            get_string('logindisplay', 'tool_oauth2'),
            get_string('issuerusedforinternal', 'tool_oauth2'),
            get_string('discoverystatus', 'tool_oauth2') . ' ' . $this->help_icon('discovered', 'tool_oauth2'),
            get_string('systemauthstatus', 'tool_oauth2') . ' ' . $this->help_icon('systemaccountconnected', 'tool_oauth2'),
            get_string('edit'),
        ];
        $table->attributes['class'] = 'admintable generaltable';
        $data = [];

        $index = 0;

        foreach ($issuers as $issuer) {
            // We need to handle the first and last ones specially.
            $first = false;
            if ($index == 0) {
                $first = true;
            }
            $last = false;
            if ($index == count($issuers) - 1) {
                $last = true;
            }

            // Name.
            $name = $issuer->get('name');
            $image = $issuer->get('image');
            if ($image) {
                $name = '<img width="24" height="24" alt="" src="' . s($image) . '"> ' . s($name);
            }
            $namecell = new html_table_cell($name);
            $namecell->header = true;

            // Login issuer.
            if ((int)$issuer->get('showonloginpage') == issuer::SERVICEONLY) {
                $loginissuer = $this->pix_icon('no', get_string('notloginissuer', 'tool_oauth2'), 'tool_oauth2');
                $logindisplayas = '';
            } else {
                $logindisplayas = s($issuer->get_display_name());
                if ($issuer->get('id') && $issuer->is_configured() && !empty($issuer->get_endpoint_url('userinfo'))) {
                    $loginissuer = $this->pix_icon('yes', get_string('loginissuer', 'tool_oauth2'), 'tool_oauth2');
                } else {
                    $loginissuer = $this->pix_icon('notconfigured', get_string('notconfigured', 'tool_oauth2'), 'tool_oauth2');
                }
            }
            $loginissuerstatuscell = new html_table_cell($loginissuer);

            // Internal services issuer.
            if ((int)$issuer->get('showonloginpage') == issuer::LOGINONLY) {
                $serviceissuer = $this->pix_icon('no', get_string('issuersservicesnotallow', 'tool_oauth2'), 'tool_oauth2');
            } else if ($issuer->get('id') && $issuer->is_configured()) {
                $serviceissuer = $this->pix_icon('yes', get_string('issuersservicesallow', 'tool_oauth2'), 'tool_oauth2');
            } else {
                $serviceissuer = $this->pix_icon('notconfigured', get_string('notconfigured', 'tool_oauth2'), 'tool_oauth2');
            }
            $internalissuerstatuscell = new html_table_cell($serviceissuer);

            // Discovered.
            if (!empty($issuer->get('scopessupported'))) {
                $discovered = $this->pix_icon('yes', get_string('discovered', 'tool_oauth2'), 'tool_oauth2');
            } else {
                if (!empty($issuer->get_endpoint_url('discovery'))) {
                    $discovered = $this->pix_icon('no', get_string('notdiscovered', 'tool_oauth2'), 'tool_oauth2');
                } else {
                    $discovered = '-';
                }
            }

            $discoverystatuscell = new html_table_cell($discovered);

            // Connected.
            if ($issuer->is_system_account_connected()) {
                $systemaccount = \core\oauth2\api::get_system_account($issuer);
                $systemauth = s($systemaccount->get('email')) . ' (' . s($systemaccount->get('username')). ') ';
                $systemauth .= $this->pix_icon('yes', get_string('systemaccountconnected', 'tool_oauth2'), 'tool_oauth2');
            } else {
                $systemauth = $this->pix_icon('no', get_string('systemaccountnotconnected', 'tool_oauth2'), 'tool_oauth2');
            }

            $params = ['id' => $issuer->get('id'), 'action' => 'auth'];
            $authurl = new moodle_url('/admin/tool/oauth2/issuers.php', $params);
            $icon = $this->pix_icon('auth', get_string('connectsystemaccount', 'tool_oauth2'), 'tool_oauth2');
            $authlink = html_writer::link($authurl, $icon);
            $systemauth .= ' ' . $authlink;

            $systemauthstatuscell = new html_table_cell($systemauth);

            $links = '';
            // Action links.
            $editurl = new moodle_url('/admin/tool/oauth2/issuers.php', ['id' => $issuer->get('id'), 'action' => 'edit']);
            $editlink = html_writer::link($editurl, $this->pix_icon('t/edit', get_string('edit')));
            $links .= ' ' . $editlink;

            // Endpoints.
            $editendpointsurl = new moodle_url('/admin/tool/oauth2/endpoints.php', ['issuerid' => $issuer->get('id')]);
            $str = get_string('editendpoints', 'tool_oauth2');
            $editendpointlink = html_writer::link($editendpointsurl, $this->pix_icon('t/viewdetails', $str));
            $links .= ' ' . $editendpointlink;

            // User field mapping.
            $params = ['issuerid' => $issuer->get('id')];
            $edituserfieldmappingsurl = new moodle_url('/admin/tool/oauth2/userfieldmappings.php', $params);
            $str = get_string('edituserfieldmappings', 'tool_oauth2');
            $edituserfieldmappinglink = html_writer::link($edituserfieldmappingsurl, $this->pix_icon('t/user', $str));
            $links .= ' ' . $edituserfieldmappinglink;

            // Delete.
            $deleteurl = new moodle_url('/admin/tool/oauth2/issuers.php', ['id' => $issuer->get('id'), 'action' => 'delete']);
            $deletelink = html_writer::link($deleteurl, $this->pix_icon('t/delete', get_string('delete')));
            $links .= ' ' . $deletelink;
            // Enable / Disable.
            if ($issuer->get('enabled')) {
                // Disable.
                $disableparams = ['id' => $issuer->get('id'), 'sesskey' => sesskey(), 'action' => 'disable'];
                $disableurl = new moodle_url('/admin/tool/oauth2/issuers.php', $disableparams);
                $disablelink = html_writer::link($disableurl, $this->pix_icon('t/hide', get_string('disable')));
                $links .= ' ' . $disablelink;
            } else {
                // Enable.
                $enableparams = ['id' => $issuer->get('id'), 'sesskey' => sesskey(), 'action' => 'enable'];
                $enableurl = new moodle_url('/admin/tool/oauth2/issuers.php', $enableparams);
                $enablelink = html_writer::link($enableurl, $this->pix_icon('t/show', get_string('enable')));
                $links .= ' ' . $enablelink;
            }
            if (!$last) {
                // Move down.
                $params = ['id' => $issuer->get('id'), 'action' => 'movedown', 'sesskey' => sesskey()];
                $movedownurl = new moodle_url('/admin/tool/oauth2/issuers.php', $params);
                $movedownlink = html_writer::link($movedownurl, $this->pix_icon('t/down', get_string('movedown')));
                $links .= ' ' . $movedownlink;
            }
            if (!$first) {
                // Move up.
                $params = ['id' => $issuer->get('id'), 'action' => 'moveup', 'sesskey' => sesskey()];
                $moveupurl = new moodle_url('/admin/tool/oauth2/issuers.php', $params);
                $moveuplink = html_writer::link($moveupurl, $this->pix_icon('t/up', get_string('moveup')));
                $links .= ' ' . $moveuplink;
            }

            $editcell = new html_table_cell($links);

            $row = new html_table_row([
                $namecell,
                $loginissuerstatuscell,
                $logindisplayas,
                $internalissuerstatuscell,
                $discoverystatuscell,
                $systemauthstatuscell,
                $editcell,
            ]);

            if (!$issuer->get('enabled')) {
                $row->attributes['class'] = 'dimmed_text';
            }

            $data[] = $row;
            $index++;
        }
        $table->data = $data;
        return html_writer::table($table);
    }

    /**
     * This function will render one beautiful table with all the endpoints.
     *
     * @param \core\oauth2\endpoint[] $endpoints - list of all endpoints.
     * @param int $issuerid
     * @return string HTML to output.
     */
    public function endpoints_table($endpoints, $issuerid) {
        global $CFG;

        $table = new html_table();
        $table->head  = [
            get_string('name'),
            get_string('url'),
            get_string('edit'),
        ];
        $table->attributes['class'] = 'admintable generaltable';
        $data = [];

        $index = 0;

        foreach ($endpoints as $endpoint) {
            // Name.
            $name = $endpoint->get('name');
            $namecell = new html_table_cell(s($name));
            $namecell->header = true;

            // Url.
            $url = $endpoint->get('url');
            $urlcell = new html_table_cell(s($url));

            $links = '';
            // Action links.
            $editparams = ['issuerid' => $issuerid, 'endpointid' => $endpoint->get('id'), 'action' => 'edit'];
            $editurl = new moodle_url('/admin/tool/oauth2/endpoints.php', $editparams);
            $editlink = html_writer::link($editurl, $this->pix_icon('t/edit', get_string('edit')));
            $links .= ' ' . $editlink;

            // Delete.
            $deleteparams = ['issuerid' => $issuerid, 'endpointid' => $endpoint->get('id'), 'action' => 'delete'];
            $deleteurl = new moodle_url('/admin/tool/oauth2/endpoints.php', $deleteparams);
            $deletelink = html_writer::link($deleteurl, $this->pix_icon('t/delete', get_string('delete')));
            $links .= ' ' . $deletelink;

            $editcell = new html_table_cell($links);

            $row = new html_table_row([
                $namecell,
                $urlcell,
                $editcell,
            ]);

            $data[] = $row;
            $index++;
        }
        $table->data = $data;
        return html_writer::table($table);
    }

    /**
     * This function will render one beautiful table with all the user_field_mappings.
     *
     * @param \core\oauth2\user_field_mapping[] $userfieldmappings - list of all user_field_mappings.
     * @param int $issuerid
     * @return string HTML to output.
     */
    public function user_field_mappings_table($userfieldmappings, $issuerid) {
        global $CFG;

        $table = new html_table();
        $table->head  = [
            get_string('userfieldexternalfield', 'tool_oauth2'),
            get_string('userfieldinternalfield', 'tool_oauth2'),
            get_string('edit'),
        ];
        $table->attributes['class'] = 'admintable generaltable';
        $data = [];

        $index = 0;

        foreach ($userfieldmappings as $userfieldmapping) {
            // External field.
            $externalfield = $userfieldmapping->get('externalfield');
            $externalfieldcell = new html_table_cell(s($externalfield));

            // Internal field.
            $internalfield = $userfieldmapping->get('internalfield');
            $internalfieldcell = new html_table_cell(s($internalfield));

            $links = '';
            // Action links.
            $editparams = ['issuerid' => $issuerid, 'userfieldmappingid' => $userfieldmapping->get('id'), 'action' => 'edit'];
            $editurl = new moodle_url('/admin/tool/oauth2/userfieldmappings.php', $editparams);
            $editlink = html_writer::link($editurl, $this->pix_icon('t/edit', get_string('edit')));
            $links .= ' ' . $editlink;

            // Delete.
            $deleteparams = ['issuerid' => $issuerid, 'userfieldmappingid' => $userfieldmapping->get('id'), 'action' => 'delete'];
            $deleteurl = new moodle_url('/admin/tool/oauth2/userfieldmappings.php', $deleteparams);
            $deletelink = html_writer::link($deleteurl, $this->pix_icon('t/delete', get_string('delete')));
            $links .= ' ' . $deletelink;

            $editcell = new html_table_cell($links);

            $row = new html_table_row([
                $externalfieldcell,
                $internalfieldcell,
                $editcell,
            ]);

            $data[] = $row;
            $index++;
        }
        $table->data = $data;
        return html_writer::table($table);
    }
}
