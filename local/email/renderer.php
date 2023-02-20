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
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
        if (!empty($backlink)) {
            $out = ' <a class="btn btn-primary" href="'.$backlink.'">' . get_string('backtocompanytemplates', 'local_email') . '</a>';
        } else {
            $out = '<p><a class="btn btn-primary" href="'.$savelink.'">' . get_string('savetemplateset', 'local_email') . '</a> ';
            $out .= ' <a class="btn btn-primary" href="'.$managelink.'">' . get_string('managetemplatesets', 'local_email') . '</a>';
}
        $out .= '</p>';

        return $out;
    }

    /**
     * Display role templates.
     */
    public function email_templates($templates, $configtemplates, $lang, $prefix, $templatesetid, $page = 0, $perpage = 30) {
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
        $strdefault = get_string('default');

        // Deal with header sliders.
        $sliced = array_slice($configtemplates, $page * $perpage, $perpage, true);
        $echecked = " checked ";
        $eschecked = " checked ";
        $emchecked = " checked ";
        $ecount = 0;
        $emcount = 0;
        $escount =0;

        foreach ($sliced as $test) {
            foreach ($templates as $templateid => $template) {
                if ($template->name == $test) {
                    if ($template->disabled) {
                        $ecount++;
                    }
                    if ($template->disabledmanager) {
                        $emcount++;
                    }
                    if ($template->disabledsupervisor) {
                        $escount++;
                    }
                }
            }
        }
        if ($ecount == count($sliced)) {
            $echecked = "";
        }
        if ($emcount == count($sliced)) {
            $emchecked = "";
        }
        if ($escount == count($sliced)) {
            $eschecked = "";
        }
        $table = new html_table();
        $table->id = 'ReportTable';
        $head = array();
        $head[] = get_string('emailtemplatename', 'local_email');
        $head[] = get_string('enable') . '</br><label class="switch"><input class="checkbox enableallall" type="checkbox" ' . $echecked. ' value="' . "{$prefix}.e.{$page}" . '" />' .
                                    "<span class='slider round'></span></label>";
        $head[] = get_string('enable_manager', 'local_email') . '</br><label class="switch"><input class="checkbox enableallmanager" type="checkbox" ' . $emchecked. ' value="' . "{$prefix}.em.{$page}" . '" />' .
                                    "<span class='slider round'></span></label>";
        $head[] = get_string('enable_supervisor', 'local_email') . '</br><label class="switch"><input class="checkbox enableallsupervisor" type="checkbox" ' . $eschecked. ' value="' . "{$prefix}.es.{$page}" . '" />' .
                                    "<span class='slider round'></span></label>";
        $head[] = get_string('controls', 'local_email');
        $table->head = $head;
        $table->align = array ("left", "center", "center", "center", "center", "center", "center", "center");

        $i = $page * $perpage;
        $max = ($page + 1) * $perpage;

        while ($i < $max && $i < $ntemplates) {
            $found = false;
            foreach ($templates as $templateid => $template) {
                if ($template->name == $configtemplates[$i]) {
                    $found = true;
                    $templatename = $configtemplates[$i];
                    unset($templates[$templateid]);
                    break;
                }
            }
            if (!$found) {
                $table->data[] = local_email::create_default_template_row($configtemplates[$i],
                                                                          $strdefault,
                                                                          $stradd,
                                                                          $strsend,
                                                                          $enable,
                                                                          $lang,
                                                                          $prefix,
                                                                          $templatesetid);
            } else {
                $row = new html_table_row();
                $row->cells[] = get_string($templatename.'_name', 'local_email') . $this->help_icon($templatename.'_name', 'local_email');
                if ($enable) {
                    if ($template->disabled) {
                        $checked = "";
                    } else {
                        $checked = "checked";
                    }
                    $value ="{$prefix}.e.{$templatename}";
                    $enablebutton = '<label class="switch"><input class="checkbox enableall" type="checkbox" ' . $checked. ' value="' . $value . '" />' .
                                    "<span class='slider round'></span></label>";
                    $cell = new html_table_cell($enablebutton);
                    $row->cells[] = $cell;
                    if ($template->disabledmanager) {
                        $checked = '';
                    } else {
                        $checked = 'checked';
                    }
                    $value ="{$prefix}.em.{$templatename}";
                    $enablemanagerbutton = '<label class="switch"><input class="checkbox enablemanager" type="checkbox" ' . $checked. ' value="' . $value . '" />' .
                                           "<span class='slider round'></span></label>";
                    $cell = new html_table_cell($enablemanagerbutton);
                    $row->cells[] = $cell;
                    if ($template->disabledsupervisor) {
                        $checked = '';
                    } else {
                        $checked = 'checked';
                    }
                    $value ="{$prefix}.es.{$templatename}";
                    $enablesupervisorbutton = '<label class="switch"><input class="checkbox enablesupervisor" type="checkbox" ' . $checked. ' value="' . $value . '" />' .
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
                $rowform->set_data(array('templatename' => $templatename, 'lang' => $lang));
                $cell = new html_table_cell($rowform->render());
                $row->cells[] = $cell;
                $table->data[] = $row;
            }

            // Need to increase the counter to skip the default template.
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

        $out = '<a class="btn btn-primary" href="'.$backlink.'">' .
                                           get_string('back') . '</a>';
        $table = new html_table();
        foreach ($templates as $template) {
            $deletelink = new moodle_url('/local/email/template_list.php',
                                          array('templatesetid' => $template->id,
                                                'action' => 'delete',
                                                'sesskey' => sesskey()));
            $editlink = new moodle_url('/local/email/template_list.php',
                                        array('templatesetid' => $template->id, 'action' => 'edit'));
            $applylink = new moodle_url('/local/email/template_apply_form.php',
                                        array('templatesetid' => $template->id, 'action' => 'apply'));
            $row = array($template->templatesetname, '<a class="btn btn-primary" href="'.$deletelink.'">' .
                                           get_string('deletetemplateset', 'local_email') . '</a> ' .
                                           '<a class="btn btn-primary" href="'.$editlink.'">' .
                                           get_string('edittemplateset', 'local_email') . '</a> ' .
                                           '<a class="btn btn-primary" href="'.$applylink.'">' .
                                           get_string('applytemplateset', 'local_email', $template->templatesetname) . '</a>');

            $table->data[] = $row;
        }

        $out .= html_writer::table($table);
        return $out;
    }
}
