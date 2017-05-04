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

class block_iomad_company_admin_renderer extends plugin_renderer_base {

    /**
     * Display list of available roles
     * @param array $roles
     */
    public function role_select($roles, $linkurl, $companyid) {
        global $DB;

        // get company info for heading
        $company = $DB->get_record('company', array('id' => $companyid), '*', MUST_EXIST);
        $out = '<h3>' . get_string('restrictcapabilitiesfor', 'block_iomad_company_admin', $company->name) . '</h3>';

        $table = new html_table();
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

        $out .= html_writer::table($table);
        return $out;
    }

    /**
     * Display capabilities for role
     */
    public function capabilities($capabilities, $roleid, $companyid) {
        global $DB;

        // get heading
        $company = $DB->get_record('company', array('id' => $companyid), '*', MUST_EXIST);
        $role = $DB->get_record('role', array('id' => $roleid), '*', MUST_EXIST);
        $out = '<h3>' . get_string('restrictcapabilitiesfor', 'block_iomad_company_admin', $company->name) . '</h3>';
        $out .= '<p><b>' . get_string('rolename', 'block_iomad_company_admin', $role->name) . '</b></p>';
        $out .= '<p>' . get_string('iomadcapabilities_boiler', 'block_iomad_company_admin') . '</p>';

        $table = new html_table();
        foreach ($capabilities as $capability) {
            $checked = '';
            if (!$capability->iomad_restriction) {
                $checked = 'checked="checked"';
            }
            $value ="{$companyid}.{$roleid}.{$capability->capability}";
            $caplink = '<a href="' .iomad::documentation_link() . $capability->capability . '">' . get_capability_string($capability->capability) . '</a>';
            $row = array(
                $caplink . '<br /><small>' . $capability->capability . '</small>',
                '<input class="checkbox" type="checkbox" ' . $checked. ' value="' . $value . '" />' . get_string('allow'),
            );
            $table->data[] = $row;
        }

        $out .= html_writer::table($table);
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
     * Render one leaf of department select
     * @param array $leaf
     * @param int $depth - how far down the tree
     * @return html
     */
    private function department_leaf($leaf, $depth=0) {
        $haschildren = !empty($leaf->children);
        $style = 'style="padding-left=' . $depth*
        $html = '<div role="treeitem" aria-expanded="true">' . $leaf->name;
        if ($haschildren) {
            $html .= '<div role="group">';
            foreach($leaf->children as $child) {
                $html .= $this->department_leaf($child, $depth+1);
            }
            $html .= '</div>';
        }
        $html .= '</div>';
   
        return $html;
    }

    /**
     * Create list markup for tree.js department select
     * @param array tree structure
     * @return string HTML markup
     */
    public function department_tree($tree) {
//echo "<pre>"; var_dump($tree); die;
        $html = '';
        $html .= '<div role="tree" id="department_tree">';
        $html .= $this->department_leaf($tree);
        $html .= '</div>';

        return $html;
    }

}
