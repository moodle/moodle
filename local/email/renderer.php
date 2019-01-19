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

class local_email_renderer extends plugin_renderer_base {

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
    public function templateset_buttons($savelink, $managelink, $backlink) {
        $out = '<p><a class="btn btn-primary" href="'.$savelink.'">' . get_string('savetemplateset', 'local_email') . '</a> '.
               '<a class="btn btn-primary" href="'.$managelink.'">' . get_string('managetemplatesets', 'local_email') . '</a>';
        if (!empty($backlink)) {
            $out .= ' <a class="btn btn-primary" href="'.$backlink.'">' . get_string('backtocompanytemplates', 'local_email') . '</a>';
        }
        $out .= '</p>';

        return $out;
    }

    /**
     * Display role templates.
     */
    public function email_templates($templates, $configtemplates, $lang, $prefix, $templatesetid) {
        global $DB, $company;

        $ntemplates = count($configtemplates);
        $context = context_system::instance();
        $out ="";

        if (iomad::has_capability('local/email:edit', $context)) {
            $stredit = get_string('edit');
            $enable = true;
            $strdisable = get_string('disable');
        } else {
            $stredit = null;
            $enable = false;
            $strdisable = null;
        }
        if (iomad::has_capability('local/email:add', $context)) {
            $stradd = get_string('add_template_button', 'local_email');
        } else {
            $stradd = null;
        }
        if (iomad::has_capability('local/email:delete', $context)) {
            $strdelete = get_string('delete_template_button', 'local_email');
        } else {
            $strdelete = null;
        }
        if (iomad::has_capability('local/email:send', $context)) {
            $strsend = get_string('send_button', 'local_email');
        } else {
            $strsend = null;
        }
        $stroverride = get_string('custom', 'local_email');
        $strdefault = get_string('default', 'local_email');

        $table = new html_table();
        $table->id = 'ReportTable';
        $table->head = array (get_string('emailtemplatename', 'local_email'),
                              get_string('enable'),
                              get_string('enable_manager', 'local_email'),
                              get_string('enable_supervisor', 'local_email'),
                              get_string('controls', 'local_email'));
        $table->align = array ("left", "center", "center", "center", "center", "center", "center", "center");

        $i = 0;

        foreach ($templates as $template) {
            while ($i < $ntemplates && $configtemplates[$i] < $template->name) {
                $table->data[] = local_email::create_default_template_row($configtemplates[$i], $strdefault,
                                                             $stradd, $strsend, $enable, $lang, $prefix);
                $i++;
            }
            $templatename = $configtemplates[$i];
            $row = new html_table_row();
            $row->cells[] = $templatename;
            if ($enable) {
                if ($template->disabled) {
                    $checked = "";
                } else {
                    $checked = "checked";
                }
                $value ="{$prefix}.e.{$templatename}";
                $enablebutton = '<label class="switch"><input class="checkbox" type="checkbox" ' . $checked. ' value="' . $value . '" />' .
                                "<span class='slider round'></span></label>";
                $cell = new html_table_cell($enablebutton);
                $row->cells[] = $cell;
                if ($template->disabledmanager) {
                    $checked = '';
                } else {
                    $checked = 'checked';
                }
                $value ="{$prefix}.em.{$templatename}";
                $enablemanagerbutton = '<label class="switch"><input class="checkbox " type="checkbox" ' . $checked. ' value="' . $value . '" />' .
                                       "<span class='slider round'></span></label>";
                $cell = new html_table_cell($enablemanagerbutton);
                $row->cells[] = $cell;
                if ($template->disabledsupervisor) {
                    $checked = '';
                } else {
                    $checked = 'checked';
                }
                $value ="{$prefix}.es.{$templatename}";
                $enablesupervisorbutton = '<label class="switch"><input class="checkbox" type="checkbox" ' . $checked. ' value="' . $value . '" />' .
                                          "<span class='slider round'></span></label>";
                $cell = new html_table_cell($enablesupervisorbutton);
                $row->cells[] = $cell;
            }
            if ($strdelete) {
                $deletebutton = "<a class='btn' href='" . new moodle_url('template_list.php',
                              array("delete" => $template->id, 'lang' => $lang, 'sesskey' => sesskey())) ."'>$strdelete</a>";
            } else {
                $deletebutton = "";
            }

            if ($stredit) {
                $editbutton = "<a class='btn' href='" . new moodle_url('template_edit_form.php',
                              array("templateid" => $template->id, 'lang' => $lang)) . "'>$stredit</a>";
            } else {
                $editbutton = "";
            }

            if ($strsend && local_email::allow_sending_to_template($templatename)) {
                $sendbutton = "<a class='btn' href='" . new moodle_url('template_send_form.php',
                              array("templateid" => $template->id, 'lang' => $lang)) . "'>$strsend</a>";
            } else {
                $sendbutton = "";
            }
            $rowform = new email_template_edit_form(new moodle_url('template_edit_form.php'), $company->id, $templatename, $templatesetid);
            $rowform->set_data(array('templatename' => $templatename));
            $cell = new html_table_cell($rowform->render());
            $row->cells[] = $cell;
            $table->data[] = $row;

            // Need to increase the counter to skip the default template.
            $i++;
        }

        while ($i < $ntemplates) {
            $table->data[] = local_email::create_default_template_row($configtemplates[$i],
                              $strdefault, $stradd, $strsend, $enable, $lang, $prefix, $templatesetid);
            $i++;
        }

        if (!empty($table)) {
            $out .= html_writer::table($table);
        }

        return $out;
    }

    /**
     * Display role templates.
     */
    public function email_templatesets($templates, $backlink) {
        global $DB;

        // get heading
        $out = '<h3>' . get_string('emailtemplatesets', 'local_email') . '</h3>';

        $out .= '<a class="btn btn-primary" href="'.$backlink.'">' .
                                           get_string('back') . '</a>';
        $table = new html_table();
        foreach ($templates as $template) {
            $deletelink = new moodle_url('/local/email/template_list.php',
                                          array('templatesetid' => $template->id,
                                                'action' => 'delete',
                                                'sesskey' => sesskey()));
            $editlink = new moodle_url('/local/email/template_list.php',
                                        array('templatesetid' => $template->id, 'action' => 'edit'));
            $row = array($template->templatesetname, '<a class="btn btn-primary" href="'.$deletelink.'">' .
                                           get_string('deletetemplateset', 'local_email') . '</a> ' .
                                           '<a class="btn btn-primary" href="'.$editlink.'">' .
                                           get_string('edittemplateset', 'local_email') . '</a>');

            $table->data[] = $row;
        }

        $out .= html_writer::table($table);
        return $out;
    }
}
