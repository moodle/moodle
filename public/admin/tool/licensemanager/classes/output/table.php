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
 * Renderable for display of license manager table.
 *
 * @package   tool_licensemanager
 * @copyright 2020 Tom Dickman <tomdickman@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_licensemanager\output;

use html_table;
use html_table_cell;
use html_table_row;
use html_writer;
use license_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Renderable for display of license manager table.
 *
 * @package   tool_licensemanager
 * @copyright 2020 Tom Dickman <tomdickman@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class table implements \renderable {

    /**
     * 'Create License' link.
     *
     * @return string HTML string.
     */
    public function create_license_link() {
        $link = html_writer::link(\tool_licensemanager\helper::get_create_license_url(),
            get_string('createlicensebuttontext', 'tool_licensemanager'),
            ['class' => 'btn btn-secondary mb-3']);

        return $link;
    }

    /**
     * Create the HTML table for license management.
     *
     * @param array $licenses
     * @param \renderer_base $output
     *
     * @return string HTML for license manager table.
     */
    public function create_license_manager_table(array $licenses, \renderer_base $output) {
        $table = new html_table();
        $table->head  = [
            get_string('enable'),
            get_string('license', 'tool_licensemanager'),
            get_string('version'),
            get_string('order'),
            get_string('edit'),
            get_string('delete'),
        ];
        $table->colclasses = [
            'text-center',
            'text-start',
            'text-start',
            'text-center',
            'text-center',
            'text-center',
        ];
        $table->id = 'manage-licenses';
        $table->attributes['class'] = 'admintable table generaltable table-hover';
        $table->data  = [];

        $rownumber = 0;
        $rowcount = count($licenses);

        foreach ($licenses as $key => $value) {
            $canmoveup = $rownumber > 0;
            $canmovedown = $rownumber < $rowcount - 1;
            $table->data[] = $this->get_license_table_row_data($value, $canmoveup, $canmovedown, $output);
            $rownumber++;
        }

        $html = html_writer::table($table);

        return $html;
    }

    /**
     * Get table row data for a license.
     *
     * @param object $license the license to populate row data for.
     * @param bool $canmoveup can this row move up.
     * @param bool $canmovedown can this row move down.
     * @param \renderer_base $output the renderer
     *
     * @return \html_table_row of columns values for row.
     */
    protected function get_license_table_row_data($license, bool $canmoveup, bool $canmovedown, \renderer_base $output) {
        global $CFG;

        $summary = $license->fullname . ' ('. $license->shortname . ')';
        if (!empty($license->source)) {
            $summary .= html_writer::empty_tag('br');
            $summary .= html_writer::link($license->source, $license->source, ['target' => '_blank']);
        }
        $summarycell = new html_table_cell($summary);
        $summarycell->attributes['class'] = 'license-summary';
        $versioncell = new html_table_cell($license->version);
        $versioncell->attributes['class'] = 'license-version';

        $deletelicense = '';
        if ($license->shortname == $CFG->sitedefaultlicense) {
            $hideshow = $output->pix_icon('t/locked', get_string('sitedefaultlicenselock', 'tool_licensemanager'));
        } else {
            if ($license->enabled == license_manager::LICENSE_ENABLED) {
                $hideshow = html_writer::link(\tool_licensemanager\helper::get_disable_license_url($license->shortname),
                    $output->pix_icon('t/hide', get_string('disablelicensename', 'tool_licensemanager', $license->fullname)));
            } else {
                $hideshow = html_writer::link(\tool_licensemanager\helper::get_enable_license_url($license->shortname),
                    $output->pix_icon('t/show', get_string('enablelicensename', 'tool_licensemanager', $license->fullname)));
            }

            if ($license->custom == license_manager::CUSTOM_LICENSE) {
                $deletelink = new \moodle_url('/admin/tool/licensemanager/index.php', [
                    'action' => 'delete',
                    'license' => $license->shortname,
                    'sesskey' => sesskey(),
                ]);
                $deletelicense = html_writer::link(
                    url: '#',
                    text: $output->pix_icon('i/trash', get_string('deletelicensename', 'tool_licensemanager', $license->fullname)),
                    attributes: [
                        'class' => 'delete-license',
                        'data-modal' => 'confirmation',
                        'data-modal-title-str' => json_encode(['deletelicense', 'tool_licensemanager']),
                        'data-modal-content-str' => json_encode(['deletelicenseconfirmmessage', 'tool_licensemanager']),
                        'data-modal-destination' => $deletelink->out(false),
                    ],
                );
            }
        }
        $hideshowcell = new html_table_cell($hideshow);
        $hideshowcell->attributes['class'] = 'license-status';

        if ($license->custom == license_manager::CUSTOM_LICENSE) {
            $editlicense = html_writer::link(\tool_licensemanager\helper::get_update_license_url($license->shortname),
                $output->pix_icon('t/editinline', get_string('editlicensename', 'tool_licensemanager', $license->fullname)),
                ['class' => 'edit-license']);
        } else {
            $editlicense = '';
        }
        $editlicensecell = new html_table_cell($editlicense);
        $editlicensecell->attributes['class'] = 'edit-license';

        $spacer = $output->pix_icon('spacer', '', 'moodle', ['class' => 'iconsmall']);
        $updown = '';
        if ($canmoveup) {
            $updown .= html_writer::link(\tool_licensemanager\helper::get_moveup_license_url($license->shortname),
                    $output->pix_icon('t/up', get_string('movelicenseupname', 'tool_licensemanager', $license->fullname),
                        'moodle', ['class' => 'iconsmall']),
                    ['class' => 'move-up']) . '';
        } else {
            $updown .= $spacer;
        }

        if ($canmovedown) {
            $updown .= '&nbsp;'.html_writer::link(\tool_licensemanager\helper::get_movedown_license_url($license->shortname),
                    $output->pix_icon('t/down', get_string('movelicensedownname', 'tool_licensemanager', $license->fullname),
                        'moodle', ['class' => 'iconsmall']),
                    ['class' => 'move-down']);
        } else {
            $updown .= $spacer;
        }
        $updowncell = new html_table_cell($updown);
        $updowncell->attributes['class'] = 'license-order';

        $row = new html_table_row([$hideshowcell, $summarycell, $versioncell, $updowncell, $editlicensecell, $deletelicense]);
        $row->attributes['data-license'] = $license->shortname;
        $row->attributes['class'] = strtolower(get_string('license', 'tool_licensemanager'));

        return $row;
    }
}
