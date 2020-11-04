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

namespace block_iomad_company_admin\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use html_writer;

class renderer extends plugin_renderer_base {

    /**
     * Display role templates.
     */
    public function role_templates($templates, $backlink) {
        global $DB;

        // get heading
        $out = '<h3>' . get_string('roletemplates', 'block_iomad_company_admin') . '</h3>';

        $out .= '<a class="btn btn-primary" href="'.$backlink.'">' .
                                           get_string('back') . '</a>';
        $table = new \html_table();
        foreach ($templates as $template) {
            $deletelink = new \moodle_url('/blocks/iomad_company_admin/company_capabilities.php',
                                          array('templateid' => $template->id,
                                                'action' => 'delete',
                                                'sesskey' => sesskey()));
            $editlink = new \moodle_url('/blocks/iomad_company_admin/company_capabilities.php',
                                        array('templateid' => $template->id, 'action' => 'edit'));
            $row = array($template->name, '<a class="btn btn-primary" href="'.$deletelink.'">' .
                                           get_string('deleteroletemplate', 'block_iomad_company_admin') . '</a> ' .
                                           '<a class="btn btn-primary" href="'.$editlink.'">' .
                                           get_string('editroletemplate', 'block_iomad_company_admin') . '</a>');

            $table->data[] = $row;
        }

        $out .= \html_writer::table($table);
        return $out;
    }

    /**
     * Is the supplied id in the leaf somewhere?
     * @param array $leaf
     * @param int $id
     * @return boolean
     */
    private function id_in_tree($leaf, $id) {
        if ($leaf->id == $id) {
            return true;
        }
        if (!empty($leaf->children)) {
            foreach ($leaf->children as $child) {
                if (self::id_in_tree($child, $id)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Render one leaf of department select
     * @param array $leaf
     * @param int $depth - how far down the tree
     * @param int $selected - which node is selected (if any)
     * @return html
     */
    private function department_leaf($leaf, $depth, $selected) {
        $haschildren = !empty($leaf->children);
        $expand = self::id_in_tree($leaf, $selected);
        if ($depth == 1 && $leaf->id == $selected) {
            $expand = false;
        }
        $style = 'style="margin-left: ' . $depth * 5 . 'px;"';
        $class = 'tree_item';
        $aria = '';
        if ($haschildren) {
            $class .= ' haschildren';
            if ($expand) {
                $aria = 'aria-expanded="true"';
            } else {
                $aria = 'aria-expanded="false"';
            }
        } else {
            $class .= ' nochildren';
        }
        if ($leaf->id == $selected) {
            $aria_selected = 'aria-selected="true"';
            $name = '<b>' . $leaf->name . ' ' . $leaf->id . ' ' . $selected . '</b>';
        } else {
            $aria_selected = 'aria-selected="false"';
            $name = $leaf->name . ' ' . $leaf->id . ' ' . $selected;
        }
        $data = 'data-id="' . $leaf->id . '"';
        $html = '<div role="treeitem" ' . $aria . ' ' . $aria_selected . ' class="' . $class .'" ' . $style . '>';
        $html .= '<span class="tree_dept_name" ' . $data . '>' . $leaf->name . '</span>';
        if ($haschildren) {
            $html .= '<div role="group">';
            foreach($leaf->children as $child) {
                $html .= $this->department_leaf($child, $depth+1, $selected);
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Create list markup for tree.js department select
     * @param array $tree structure
     * @param int $selected selected id (if any)
     * @return string HTML markup
     */
    public function department_tree($tree, $selected) {
        $html = '';
        $html .= '<div class="dep_tree">';
        $html .= '<div role="tree" id="department_tree">';
        $html .= $this->department_leaf($tree, 1, $selected);
        $html .= '</div></div>';

        return $html;
    }

    /**
     * Render admin block
     * @param adminblock $adminblock
     */
    public function render_adminblock(adminblock $adminblock) {
        return $this->render_from_template('block_iomad_company_admin/adminblock', $adminblock->export_for_template($this));
    }

    /**
     * Render editcompanies page
     * @param editcompanies $editcompanies
     */
    public function render_editcompanies(editcompanies $editcompanies) {
        return $this->render_from_template('block_iomad_company_admin/editcompanies', $editcompanies->export_for_template($this));
    }

    /**
     * Render company capabilities roles page
     * @param capabilitiesroles $capabilitiesroles
     */
    public function render_capabilitiesroles(capabilitiesroles $capabilitiesroles) {
        return $this->render_from_template('block_iomad_company_admin/capabilitiesroles', $capabilitiesroles->export_for_template($this));
    }

    /**
     * Render capabilties page
     * @param capabilitiesroles $capabilities
     */
    public function render_capabilities(capabilities $capabilities) {
        return $this->render_from_template('block_iomad_company_admin/capabilities', $capabilities->export_for_template($this));
    }

    /**
     * Render role templates page
     * @param roletemplates $roletemplates
     */
    public function render_roletemplates(roletemplates $roletemplates) {
        return $this->render_from_template('block_iomad_company_admin/roletemplates', $roletemplates->export_for_template($this));
    }

    public function render_datetime_element($name, $id, $timestamp) {

        // Get the calendar type used - see MDL-18375.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        $this->_elements = array();

        $dateformat = $calendartype->get_date_order();
        // Reverse date element (Day, Month, Year), in RTL mode.
        if (right_to_left()) {
            $dateformat = array_reverse($dateformat);
        }

        if (!empty($timestamp)) {
            $dayvalue = date('d', $timestamp);
            $monvalue = date('n', $timestamp);
            $yearvalue = date('Y', $timestamp);
            $selectarray = array('class' => 'customselect', 'onchange' => "this.form.submit()");
            $checkboxarray = array('type' => 'checkbox', 'name' => $name."[enabled]", 'value' => 1, 'checked' => 'checked', 'class' => 'form-check-input datecontrolswitch checkbox', 'id' => 'id_' . $id . '_calender_enabled');
        } else {
            $dayvalue = date('d', time());
            $monvalue = date('n', time());
            $yearvalue = date('Y', time());
            $selectarray = array('class' => 'customselect', 'disabled' => 'disabled', 'onchange' => "this.form.submit()");
            $checkboxarray = array('type' => 'checkbox', 'name' => $name."[enabled]", 'class' => 'form-check-input datecontrolswitch checkbox', 'id' => 'id_' . $id . '_calender_enabled');
        }

        $html = html_writer::start_tag('span', array('class' => 'fdate_selector d-flex'));
        $html .= html_writer::start_tag('span', array('data-fieldtype' => 'select'));
        $html .= html_writer::start_tag('select', $selectarray + array('name' => $name."[day]", 'id' => $id."_day"));
        foreach ($dateformat['day'] as $key => $value)
        if ($dayvalue == $key) {
            $html .= html_writer::tag('option', $value, array('value' => $key, 'selected' => true));
        } else {
            $html .= html_writer::tag('option', $value, array('value' => $key));
        }
        $html .= html_writer::end_tag('select');
        $html .= html_writer::end_tag('span') . " ";
        $html .= html_writer::start_tag('span', array('data-fieldtype' => 'select'));
        $html .= html_writer::start_tag('select', $selectarray + array('name' => $name."[month]", 'id' => $id."_month"));
        foreach ($dateformat['month'] as $key => $value)
        if ($monvalue == $key) {
            $html .= html_writer::tag('option', $value, array('value' => $key, 'selected' => true));
        } else {
            $html .= html_writer::tag('option', $value, array('value' => $key));
        }
        $html .= html_writer::end_tag('select');
        $html .= html_writer::end_tag('span') . " ";
        $html .= html_writer::start_tag('span', array('data-fieldtype' => 'select'));
        $html .= html_writer::start_tag('select', $selectarray + array('name' => $name."[year]", 'id' => $id."_year"));
        foreach ($dateformat['year'] as $key => $value)
        if ($yearvalue == $key) {
            $html .= html_writer::tag('option', $value, array('value' => $key, 'selected' => true));
        } else {
            $html .= html_writer::tag('option', $value, array('value' => $key));
        }
        $html .= html_writer::end_tag('select');
        $html .= html_writer::end_tag('span') . " ";
        $html .= html_writer::start_tag('a', array('class' => "visibleifjs", 'name' => $name."[calendar]", 'href'=>"#", 'id'=>"id_" . $id ."_calendar"));
        $html .= html_writer::tag('i', '', array('class'=>"icon fa fa-calendar fa-fw ", 'title'=>"Calendar", 'aria-label'=>"Calendar"));
        $html .= html_writer::end_tag('a');
        $html .= html_writer::end_tag('span');
        if (empty($timestamp)) {
            $html .= html_writer::start_tag('label', array('class' => 'form-check fitem'));
            $html .= html_writer::tag('input', '', $checkboxarray);
            $html .= get_string('enable');
            $html .= html_writer::end_tag('label');
        }
        $html .= html_writer::tag('input',
                                       '',
                                       array('name' => 'orig' . $name,
                                             'type' => 'hidden',
                                             'value' => $timestamp,
                                             'id' => 'orig' . $id));

        return $html;
    }
}
