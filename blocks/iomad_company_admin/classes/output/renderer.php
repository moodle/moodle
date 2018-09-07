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

class renderer extends plugin_renderer_base {

    /**
     * Display list of available roles
     * @param array $roles
     */
    public function role_select($roles, $linkurl, $companyid, $templateid) {
        global $DB;

        // get company info for heading
        if (empty($templateid)) {
            $company = $DB->get_record('company', array('id' => $companyid), '*', MUST_EXIST);
            $out = '<h3>' . get_string('restrictcapabilitiesfor', 'block_iomad_company_admin', $company->name) . '</h3>';
        } else {
            $template = $DB->get_record('company_role_templates', array('id' => $templateid), '*', MUST_EXIST);
            $title = get_string('roletemplate', 'block_iomad_company_admin') . ' ' . $template->name;
            $out = '<h3>' . get_string('restrictcapabilitiesfor', 'block_iomad_company_admin', $title) . '</h3>';
        }

        $table = new \html_table();
        $table->head = array(
            get_string('name'),
            get_string('description'),
        );
        foreach ($roles as $role) {
            $linkurl->params(array('roleid' => $role->id));
            $row = array(
                '<b>' . $role->name . '</b>',
                $role->description,
                "<a class=\"btn btn-primary\" href=\"$linkurl\">" . get_string('edit') . "</a>",
            );
            $table->data[] = $row;
        }

        $out .= \html_writer::table($table);
        return $out;
    }

    /**
     * Display capabilities for role
     */
    public function capabilities($capabilities, $roleid, $companyid, $templateid) {
        global $DB;

        // get heading
        if (empty($templateid)) {
            $company = $DB->get_record('company', array('id' => $companyid), '*', MUST_EXIST);
            $out = '<h3>' . get_string('restrictcapabilitiesfor', 'block_iomad_company_admin', $company->name) . '</h3>';
            $prefix = "c." . $companyid;
        } else {
            $template = $DB->get_record('company_role_templates', array('id' => $templateid), '*', MUST_EXIST);
            $title = get_string('roletemplate', 'block_iomad_company_admin') . ' ' . $template->name;
            $out = '<h3>' . get_string('restrictcapabilitiesfor', 'block_iomad_company_admin', $title) . '</h3>';
            $prefix = "t." . $templateid;
        }
        $role = $DB->get_record('role', array('id' => $roleid), '*', MUST_EXIST);
        $out .= '<p><b>' . get_string('rolename', 'block_iomad_company_admin', $role->name) . '</b></p>';
        $out .= '<p>' . get_string('iomadcapabilities_boiler', 'block_iomad_company_admin') . '</p>';

        $table = new \html_table();
        foreach ($capabilities as $capability) {
            $checked = '';
            if (!$capability->iomad_restriction) {
                $checked = 'checked="checked"';
            }
            $value ="{$prefix}.{$roleid}.{$capability->capability}";
            $caplink = '<a href="' . \iomad::documentation_link() . $capability->capability . '">' . get_capability_string($capability->capability) . '</a>';
            $row = array(
                $caplink . '<br /><small>' . $capability->capability . '</small>',
                '<input class="checkbox" type="checkbox" ' . $checked. ' value="' . $value . '" />' . get_string('allow'),
            );
            $table->data[] = $row;
        }

        $out .= \html_writer::table($table);
        return $out;
    }
    
    /**
     * Back to list of roles button
     */
    public function roles_button($link) {
        $out = '<p><a class="btn btn-primary" href="'.$link.'">' . get_string('listroles', 'block_iomad_company_admin') . '</a></p>';
        
        return $out;
    }

    /**
     * Back to list of roles button
     */
    public function templates_buttons($savelink, $managelink, $backlink) {
        $out = '<p><a class="btn btn-primary" href="'.$savelink.'">' . get_string('saveroletemplate', 'block_iomad_company_admin') . '</a> '.
               '<a class="btn btn-primary" href="'.$managelink.'">' . get_string('managetemplates', 'block_iomad_company_admin') . '</a>';
        if (!empty($backlink)) {
            $out .= ' <a class="btn btn-primary" href="'.$backlink.'">' . get_string('backtocompanytemplate', 'block_iomad_company_admin') . '</a>';
        }
        $out .= '</p>';

        return $out;
    }

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
     * @param main $main
     */
    public function render_adminblock(adminblock $adminblock) {
        return $this->render_from_template('block_iomad_company_admin/adminblock', $adminblock->export_for_template($this));
    }

}
