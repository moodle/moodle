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
 * Renderer.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing the renderer functions for displaying file types.
 *
 * @package tool_filetypes
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_filetypes_renderer extends plugin_renderer_base {

    /**
     * Renderer for displaying the file type edit table.
     *
     * @param array $filetypes An array of file type objects (from get_mimetypes_array)
     * @param array $deleted An array of deleted file types
     * @param bool $restricted If true, cannot be edited because set in config.php.
     * @return string HTML code
     */
    public function edit_table(array $filetypes, array $deleted, $restricted) {
        // Get combined array of all types, with deleted marker.
        $combined = array_merge($filetypes, $deleted);
        foreach ($deleted as $ext => $value) {
            $combined[$ext]['deleted'] = true;
        }
        ksort($combined);

        $out = $this->heading(get_string('pluginname', 'tool_filetypes'));
        if ($restricted) {
            $out .= html_writer::div(
                    html_writer::div(get_string('configoverride', 'admin'), 'alert alert-info'),
                    '', array('id' => 'adminsettings'));
        }
        if (count($combined) > 1) {
            // Display the file type table if any file types exist (other than 'xxx').
            $table = new html_table();
            $headings = new html_table_row();
            $headings->cells = array();
            $headings->cells[] = new html_table_cell(get_string('extension', 'tool_filetypes'));
            if (!$restricted) {
                $headings->cells[] =
                        new html_table_cell(html_writer::span(get_string('edit'), 'accesshide'));
            }
            $headings->cells[] = new html_table_cell(get_string('source', 'tool_filetypes'));
            $headings->cells[] = new html_table_cell(get_string('mimetype', 'tool_filetypes'));
            $headings->cells[] = new html_table_cell(get_string('groups', 'tool_filetypes'));
            $headings->cells[] = new html_table_cell(get_string('displaydescription', 'tool_filetypes'));
            foreach ($headings->cells as $cell) {
                $cell->header = true;
            }
            $table->data = array($headings);
            foreach ($combined as $extension => $filetype) {
                if ($extension === 'xxx') {
                    continue;
                }
                $row = new html_table_row();
                $row->cells = array();

                // First cell has icon and extension.
                $icon = $this->pix_icon('f/' . $filetype['icon'], '');
                $iconcell = new html_table_cell($icon . ' ' . html_writer::span(s($extension)));
                $iconcell->attributes['class'] = 'icon-size-5';
                $row->cells[] = $iconcell;

                // Reset URL and button if needed.
                $reverturl = new \moodle_url('/admin/tool/filetypes/revert.php',
                        array('extension' => $extension));
                $revertbutton = html_writer::link($reverturl, $this->pix_icon('t/restore',
                        get_string('revert', 'tool_filetypes', s($extension))));
                if ($restricted) {
                    $revertbutton = '';
                }

                // Rest is different for deleted items.
                if (!empty($filetype['deleted'])) {
                    // Show deleted standard types differently.
                    if (!$restricted) {
                        $row->cells[] = new html_table_cell('');
                    }
                    $source = new html_table_cell(get_string('source_deleted', 'tool_filetypes') .
                            ' ' . $revertbutton);
                    $source->attributes = array('class' => 'nonstandard');
                    $row->cells[] = $source;

                    // Other cells are blank.
                    $row->cells[] = new html_table_cell('');
                    $row->cells[] = new html_table_cell('');
                    $row->cells[] = new html_table_cell('');
                    $row->attributes = array('class' => 'deleted');
                } else {
                    if (!$restricted) {
                        // Edit icons. For accessibility, the name of these links should
                        // be different for each row, so we have to include the extension.
                        $editurl = new \moodle_url('/admin/tool/filetypes/edit.php',
                                array('oldextension' => $extension));
                        $editbutton = html_writer::link($editurl, $this->pix_icon('t/edit',
                                get_string('edita', 'moodle', s($extension))));
                        $deleteurl = new \moodle_url('/admin/tool/filetypes/delete.php',
                                array('extension' => $extension));
                        $deletebutton = html_writer::link($deleteurl, $this->pix_icon('t/delete',
                                get_string('deletea', 'tool_filetypes', s($extension))));
                        $row->cells[] = new html_table_cell($editbutton . '&nbsp;' . $deletebutton);
                    }

                    // Source.
                    $sourcestring = 'source_';
                    if (!empty($filetype['custom'])) {
                        $sourcestring .= 'custom';
                    } else if (!empty($filetype['modified'])) {
                        $sourcestring .= 'modified';
                    } else {
                        $sourcestring .= 'standard';
                    }
                    $source = new html_table_cell(get_string($sourcestring, 'tool_filetypes') .
                            ($sourcestring === 'source_modified' ? ' ' . $revertbutton : ''));
                    if ($sourcestring !== 'source_standard') {
                        $source->attributes = array('class' => 'nonstandard');
                    }
                    $row->cells[] = $source;

                    // MIME type.
                    $mimetype = html_writer::div(s($filetype['type']), 'mimetype');
                    if (!empty($filetype['defaulticon'])) {
                        // Include the 'default for MIME type' info in the MIME type cell.
                        $mimetype .= html_writer::div(html_writer::tag('i',
                                get_string('defaulticon', 'tool_filetypes')));
                    }
                    $row->cells[] = new html_table_cell($mimetype);

                    // Groups.
                    $groups = !empty($filetype['groups']) ? implode(', ', $filetype['groups']) : '';
                    $row->cells[] = new html_table_cell(s($groups));

                    // Description.
                    $description = get_mimetype_description(array('filename' => 'a.' . $extension));
                    // Don't show the description if it's just a copy of the MIME type,
                    // it makes the table ugly with the long duplicate text; leave blank instead.
                    if ($description === $filetype['type']) {
                        $description = '';
                    }
                    $row->cells[] = new html_table_cell($description);
                }

                $table->data[] = $row;
            }
            $out .= html_writer::table($table);
        } else {
            $out .= html_writer::tag('div', get_string('emptylist', 'tool_filetypes'));
        }
        // Displaying the 'Add' button.
        if (!$restricted) {
            $out .= $this->single_button(new moodle_url('/admin/tool/filetypes/edit.php',
                    array('name' => 'add')), get_string('addfiletypes', 'tool_filetypes'), 'get');
        }
        return $out;
    }
}
