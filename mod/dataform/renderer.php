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
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the dataform module.
 *
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_dataform_renderer extends plugin_renderer_base {

    /* @var dataform The dataform object for this output instance. */
    private $_dataformid;

    /**
     * Set the dataform instance id for this renderer.
     * This is required for {@link render_groups_menu()},
     * {@link render_intro()}, {@link render_tabs()}.
     *
     * @param dataform
     */
    public function set_df($dataformid) {
        $this->_dataformid = $dataformid;
    }

    /**
     * prints the header of the current dataform page
     *
     * @param array $params
     */
    public function header($params = null) {
        $params = (object) $params;
        $o = '';

        $o .= $this->output->header();

        // Print heading.
        if (!empty($params->heading)) {
            $o .= $this->output->heading($params->heading);
        }

        // Print intro.
        if (!empty($params->intro) and $params->intro) {
            $o .= $this->render_intro();
        }

        // Print the tabs.
        if (!empty($params->tab)) {
            $o .= $this->render_tabs($params->tab);
        }

        // Print groups menu if needed.
        if (!empty($params->groups)) {
            $filterid = !empty($params->urlparams->filter) ? $params->urlparams->filter : null;
            $o .= $this->render_groups_menu($params->urlparams->view, $filterid);
        }

        // Print any notices.
        if (empty($params->nonotifications)) {
            $o .= $this->render_notifications();
        }
        return $o;
    }

    /**
     * Prints the footer of the current dataform page
     *
     * @param array $params
     */
    public function footer($params = null) {
        return $this->output->footer();
    }

    /**
     * Prints a dataform subplugin selector
     *
     * @param string $dir subplugin directory name
     * @param array $options
     * @param bool $asort Whether to sort the list alphbetically
     * @return string HTML fragment
     */
    public function subplugin_select($dir, array $options = null, $asort = true) {
        if (!$this->_dataformid) {
            return null;
        }

        $subplugintype = "dataform$dir";
        $plugininfo = '\\mod_dataform\\plugininfo\\'. $subplugintype;

        if (!$enabled = $plugininfo::get_enabled_plugins()) {
            return null;
        }

        $context = \mod_dataform_dataform::instance($this->_dataformid)->context;
        if (!$instantiable = $plugininfo::get_instantiable_plugins($context)) {
            return null;
        }

        $menu = array();
        foreach ($enabled as $name) {
            // Must be instantiable.
            if (!array_key_exists($name, $instantiable)) {
                continue;
            }

            // Must not be excluded.
            if (!empty($options['exclude']) and in_array($name, $options['exclude'])) {
                continue;
            }

            // Add to list.
            $menu[$name] = get_string('pluginname', "{$subplugintype}_$name");
        }

        $params = array('d' => $this->_dataformid, 'sesskey' => sesskey());
        $url = new moodle_url("/mod/dataform/$dir/edit.php", $params);
        $select = new single_select($url, 'type', $menu, null, array('' => 'choosedots'), $subplugintype.'form');
        $select->set_label(get_string($dir. 'add', 'dataform'));
        return html_writer::tag('div', $this->output->render($select), array('class' => 'subplugin-selector mdl-align'));
    }

    /**
     * Returns html for admin style list of views
     *
     * @param string $heading Title of list
     * @param array $fields List of available views
     * @return string HTML fragment of html_table
     */
    public function views_admin_list($heading, $views) {
        if (!$this->_dataformid) {
            return null;
        }

        $df = mod_dataform_dataform::instance($this->_dataformid);

        $viewbaseurl = new moodle_url('/mod/dataform/view.php', array('d' => $this->_dataformid));
        $editbaseurl = new moodle_url('/mod/dataform/view/edit.php', array('d' => $this->_dataformid));
        $actionbaseurl = new moodle_url('/mod/dataform/view/index.php', array('d' => $this->_dataformid));
        $sessparam = array('sesskey' => sesskey());

        // Table headings.
        $strname = get_string('name');
        $strdescription = get_string('description');
        $strtype = get_string('type', 'dataform');
        $strdefault = get_string('default');
        $strvisible = get_string('visible');
        $strfilter = get_string('filter', 'dataform');
        $stredit = get_string('edit');
        $strview = get_string('view', 'dataform');
        $strdelete = get_string('delete');
        $strduplicate = get_string('duplicate');
        $strchoose = get_string('choose');
        $strhide = get_string('hide');
        $strshow = get_string('show');
        $strreset = get_string('reset');
        $strpermissions = get_string('permissions', 'role');
        $strnotifications = get_string('notifications');
        $strpatternbroken = get_string('patternbroken', 'dataform');
        $strpatternsuspect = get_string('patternsuspect', 'dataform');
        $strpatternvalid = get_string('patternvalid', 'dataform');
        $strpatterncleanup = get_string('patterncleanup', 'dataform');
        $strnorules = get_string('rulesnone', 'dataform');

        // Action icons.
        $editicon = $this->output->pix_icon('t/edit', $stredit);
        $browseicon = $this->output->pix_icon('i/search', $strview);
        $duplicateicon = $this->output->pix_icon('t/copy', $strduplicate);
        $deleteicon = $this->output->pix_icon('t/delete', $strdelete);
        $reseticon = $this->output->pix_icon('t/reload', $strreset);
        $defaulticon = $this->output->pix_icon('t/check', $strdefault);
        $nodefaulticon = $this->output->pix_icon('i/completion-auto-n', $strchoose);
        $hideicon = $this->output->pix_icon('t/hide', $strhide);
        $showicon = $this->output->pix_icon('t/show', $strshow);
        $patternvalidicon = $this->output->pix_icon('patternvalid', $strpatternvalid, 'dataform');
        $patternsuspecticon = $this->output->pix_icon('patternsuspect', $strpatternsuspect, 'dataform');
        $patterbrokenicon = $this->output->pix_icon('patternbroken', $strpatternbroken, 'dataform');
        $cleanupicon = $this->output->pix_icon('cleanup', $strpatterncleanup, 'dataform');
        $notificationicon = $this->output->pix_icon('notification', $strnotifications, 'dataform');
        $nonotificationicon = $this->output->pix_icon('nonotification', $strnotifications, 'dataform');
        $accessicon = $this->output->pix_icon('access', $strpermissions, 'dataform');
        $noaccessicon = $this->output->pix_icon('noaccess', $strpermissions, 'dataform');

        $selectallnone = html_writer::checkbox('viewselectallnone', null, false, null, array('id' => 'id_viewselectallnone'));
        $this->page->requires->js_init_call('M.mod_dataform.util.init_select_allnone', array('view'));

        $multiactionurl = new moodle_url($actionbaseurl, $sessparam);

        // Bulk delete.
        $icon = new pix_icon('t/delete', get_string('multidelete', 'dataform'));
        $multidelete = $this->output->action_icon($actionbaseurl, $icon, null, array('id' => 'id_view_bulkaction_delete'));
        $this->page->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('view', 'delete', $multiactionurl->out(false)));

        // Bulk duplicate.
        $icon = new pix_icon('t/copy', get_string('multiduplicate', 'dataform'));
        $multiduplicate = $this->output->action_icon($actionbaseurl, $icon, null, array('id' => 'id_view_bulkaction_duplicate'));
        $this->page->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('view', 'duplicate', $multiactionurl->out(false)));

        // Filters menu.
        $filtersmenu = mod_dataform_filter_manager::instance($this->_dataformid)->get_filters(null, true);

        // Access manager.
        $aman = mod_dataform_access_manager::instance($this->_dataformid);

        // Notification manager.
        $nman = mod_dataform_notification_manager::instance($this->_dataformid);

        // Table headers.
        $headers = array(
            'browse' => array(null, 'center', false),
            'name' => array($strname, 'left', false),
            'type' => array($strtype, 'left', false),
            'description' => array($strdescription, 'left', false),
            'default' => array($strdefault, 'center', false),
            'visible' => array($strvisible, 'center', false),
            'filter' => array($strfilter, 'center', false),
            'info' => array(null, 'left', false),
            'actions' => array("$multiduplicate $multidelete &nbsp;$selectallnone", 'right', false),
        );

        if (!$views) {
            $views = array();
        }

        $rows = array();
        foreach ($views as $viewid => $view) {

            $editurl = new moodle_url($editbaseurl, $sessparam + array('vedit' => $viewid));
            $viewname = html_writer::link($editurl, $view->name);

            $viewtype = $view->get_typename();

            $viewdescription = format_text($view->description, FORMAT_PLAIN);

            // Visibility.
            $visibility = $view::get_visibility_modes();
            $selecturl = new moodle_url($actionbaseurl, $sessparam + array('visible' => $viewid));
            $select = new single_select($selecturl, 'visibility', $visibility, $view->visible, null);
            $selectedclass = !$view->visible ? 'disabled' : ($view->visible == $view::VISIBILITY_HIDDEN ? 'hidden' : 'visible');
            $viewvisible = html_writer::tag('div', $this->output->render($select), array('class' => "viewvisibilityselector view$selectedclass"));

            // Default view.
            if ($viewid == $df->defaultview) {
                $viewdefault = $defaulticon;
            } else {
                $defaulturl = new moodle_url($actionbaseurl, $sessparam + array('default' => $viewid));
                $idsetdefault = 'id_'. str_replace(' ', '_', $view->name). '_set_default';
                $viewdefault = html_writer::link($defaulturl, $nodefaulticon, array('id' => $idsetdefault));
            }

            // View filter.
            if (!empty($filtersmenu)) {
                $viewfilterid = $view->filterid;
                if ($viewfilterid and !in_array($viewfilterid, array_keys($filtersmenu))) {
                    $url = new moodle_url($actionbaseurl, $sessparam + array('setfilter' => $viewid, 'fid' => -1));
                    $viewfilter = html_writer::link($url, $this->output->pix_icon('i/risk_xss', $strreset));

                } else {
                    if ($viewfilterid) {
                        $selected = $viewfilterid;
                        $options = array(-1 => '* '. get_string('reset')) + $filtersmenu;
                    } else {
                        $selected = '';
                        $options = $filtersmenu;
                    }

                    $selecturl = new moodle_url($actionbaseurl, $sessparam + array('setfilter' => $viewid));
                    $viewselect = new single_select($selecturl, 'fid', $options, $selected, array('' => 'choosedots'));

                    $viewfilter = $this->output->render($viewselect);
                }
            } else {
                $viewfilter = get_string('filtersnonedefined', 'dataform');
            }

            // INFO
            // Patterns validation.
            if ($updates = $view->patterns_check()) {
                $showcleanup = true;
                if (!empty($updates['view']) or !empty($updates['field'])) {
                    $icon = $patternbrokenicon;
                } else {
                    $icon = $patternsuspecticon;
                }
                $url = new moodle_url($actionbaseurl, array('patternscleanup' => $viewid));
                $viewpatterncheck = html_writer::link($url, $icon);

            } else {
                $viewpatterncheck = $patternvalidicon;
            }

            // Permission rules.
            if ($rulenames = $aman->get_view_rules($view->name)) {
                $viewpermissions = $accessicon;
                // Html_writer::alist($rulenames);.
            } else {
                $viewpermissions = $noaccessicon;
            }

            // Notification rules.
            if ($rulenames = $nman->get_view_rules($view->name)) {
                $viewnotifications = $notificationicon;
                // Html_writer::alist($rulenames);.
            } else {
                $viewnotifications = $nonotificationicon;
            }

            $viewinfo = implode('&nbsp;&nbsp;', array($viewpatterncheck, $viewpermissions, $viewnotifications));

            // ACTIONS.
            $url = new moodle_url($viewbaseurl, array('view' => $viewid));
            $linkparams = array('id' => "id_browseview$viewid", 'title' => "$strview $view->name");
            $viewbrowse = html_writer::link($url, $browseicon, $linkparams);

            $url = new moodle_url($editbaseurl, $sessparam + array('vedit' => $viewid));
            $linkparams = array('id' => "id_editview$viewid", 'title' => "$stredit $view->name");
            $viewedit = html_writer::link($url, $editicon, $linkparams);

            $url = new moodle_url($actionbaseurl, $sessparam + array('duplicate' => $viewid));
            $linkparams = array('id' => "id_duplicateview$viewid", 'title' => "$strduplicate $view->name");
            $viewduplicate = html_writer::link($url, $duplicateicon, $linkparams);

            $url = new moodle_url($actionbaseurl, $sessparam + array('reset' => $viewid));
            $linkparams = array('id' => "id_resetview$viewid", 'title' => "$strreset $view->name");
            $viewreset = html_writer::link($url, $reseticon, $linkparams);

            $url = new moodle_url($actionbaseurl, $sessparam + array('delete' => $viewid));
            $linkparams = array('id' => "id_deleteview$viewid", 'title' => "$strdelete $view->name");
            $viewdelete = html_writer::link($url, $deleteicon, $linkparams);

            $attributes = array('id' => "id_viewselector$viewid", 'class' => 'viewselector');
            $viewselector = html_writer::checkbox("viewselector", $viewid, false, null, $attributes);
            $viewactions = implode('&nbsp;&nbsp;&nbsp;', array($viewreset, $viewedit, $viewduplicate, $viewdelete, $viewselector));

            $data = array();
            foreach (array_keys($headers) as $key) {
                $data[] = ${"view$key"};
            }

            $rows[] = $data;
        }

        // Add cleanup link to Info header if needed.
        if (!empty($showcleanup)) {
            $url = new moodle_url($actionbaseurl, array('patternscleanup' => -1));
            $cleanuplink = html_writer::link($url, $cleanupicon);
            $headers['info'] = array($cleanuplink, 'left', false);
        }

        // Generate the table.
        $table = new html_table();
        foreach ($headers as $header) {
            list($table->head[], $table->align[], $table->wrap[]) = $header;
        }
        $table->data = $rows;

        $title = $heading ? html_writer::tag('h3', $heading) : null;
        return $title. html_writer::table($table);
    }

    /**
     * Returns html for admin style list of fields.
     *
     * @param string $extorint Subset type external|internal
     * @param string $heading Title of list
     * @param array $fields List of available fields
     * @return string HTML fragment of html_table
     */
    public function fields_admin_list($extorint, $heading, $fields) {
        if (!$this->_dataformid) {
            return null;
        }

        $df = mod_dataform_dataform::instance($this->_dataformid);

        // External or internal.
        $external = ($extorint == 'external');

        $editbaseurl = new moodle_url('/mod/dataform/field/edit.php', array('d' => $this->_dataformid));
        $actionbaseurl = new moodle_url('/mod/dataform/field/index.php', array('d' => $this->_dataformid));
        $sessparam = array('sesskey' => sesskey());

        $strname = get_string('name');
        $strdescription = get_string('description');
        $strtype = get_string('type', 'dataform');
        $stredit = get_string('edit');
        $strduplicate = get_string('duplicate');
        $strdelete = get_string('delete');
        $strhide = get_string('hide');
        $strshow = get_string('show');
        $strlock = get_string('lock', 'dataform');
        $strunlock = get_string('unlock', 'dataform');
        $strpermissions = get_string('permissions', 'role');
        $strnotifications = get_string('notifications');

        // Icons.
        $editicon = $this->output->pix_icon('t/edit', $stredit);
        $duplicateicon = $this->output->pix_icon('t/copy', $strduplicate);
        $deleteicon = $this->output->pix_icon('t/delete', $strdelete);
        $notificationicon = $this->output->pix_icon('notification', $strnotifications, 'dataform');
        $nonotificationicon = $this->output->pix_icon('nonotification', $strnotifications, 'dataform');
        $accessicon = $this->output->pix_icon('access', $strpermissions, 'dataform');
        $noaccessicon = $this->output->pix_icon('noaccess', $strpermissions, 'dataform');

        // The default value of the type attr of a button is submit, so set it to button so that
        // it doesn't submit the form.
        $selectallnone = html_writer::checkbox('fieldselectallnone', null, false, null, array('id' => 'id_fieldselectallnone'));
        $this->page->requires->js_init_call('M.mod_dataform.util.init_select_allnone', array('field'));

        $multiactionurl = new moodle_url($actionbaseurl, $sessparam);

        // Bulk delete.
        $icon = new pix_icon('t/delete', get_string('multidelete', 'dataform'));
        $multidelete = $this->output->action_icon($actionbaseurl, $icon, null, array('id' => 'id_field_bulkaction_delete'));
        $this->page->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('field', 'delete', $multiactionurl->out(false)));

        // Bulk duplicate.
        $icon = new pix_icon('t/copy', get_string('multiduplicate', 'dataform'));
        $multiduplicate = $this->output->action_icon($actionbaseurl, $icon, null, array('id' => 'id_field_bulkaction_duplicate'));
        $this->page->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('field', 'duplicate', $multiactionurl->out(false)));

        // Table headers.
        $headers = array(
            'name' => array($strname, 'left', false),
            'type' => array($strtype, 'left', false),
            'description' => array($strdescription, 'left', false),
            'visible' => array(get_string('visible'), 'center', false),
            'editable' => array(get_string('fieldeditable', 'dataform'), 'center', false),
            'info' => array(null, 'left', false),
            'actions' => array("$multiduplicate $multidelete &nbsp;$selectallnone", 'right', false),
        );

        if (!$external) {
            unset($headers['actions']);
            unset($headers['visible']);
            unset($headers['editable']);
        }

        // Access manager.
        $aman = mod_dataform_access_manager::instance($this->_dataformid);

        // Notification manager.
        $nman = mod_dataform_notification_manager::instance($this->_dataformid);

        if (!$fields) {
            $fields = array();
        }

        $rows = array();
        foreach ($fields as $fieldid => $field) {
            if (!$field) {
                continue;
            }

            $fieldformclass = "dataformfield_{$field->type}_form";

            // Name.
            if (class_exists($fieldformclass)) {
                $fieldname = html_writer::link(new moodle_url($editbaseurl, $sessparam + array('fid' => $fieldid)), $field->name);
            } else {
                $fieldname = $field->name;
            }
            // Type.
            $fieldtype = $field->image. '&nbsp;'. $field->typename;
            // Description.
            $fielddescription = shorten_text($field->description, 30);
            // Visible.
            if ($visible = $field->visible) {
                $visibleicon = $this->output->pix_icon('t/hide', $strhide);
                $visibleicon = ($visible == 1 ? "($visibleicon)" : $visibleicon);
            } else {
                $visibleicon = $this->output->pix_icon('t/show', $strshow);
            }
            $fieldvisible = html_writer::link(new moodle_url($actionbaseurl, $sessparam + array('visible' => $fieldid)), $visibleicon);
            // Editable.
            if ($editable = $field->editable) {
                $editableicon = $this->output->pix_icon('t/lock', $strlock);
            } else {
                $editableicon = $this->output->pix_icon('t/unlock', $strunlock);
            }
            $fieldeditable = html_writer::link(new moodle_url($actionbaseurl, $sessparam + array('editable' => $fieldid)), $editableicon);

            // INFO
            // Access rules.
            if ($rulenames = $aman->get_field_rules($field->name)) {
                $fieldaccess = $accessicon;
                // Html_writer::alist($rulenames);.
            } else {
                $fieldaccess = $noaccessicon;
            }

            // Notification rules.
            if ($rulenames = $nman->get_field_rules($field->name)) {
                $fieldnotifications = $notificationicon;
                // Html_writer::alist($rulenames);.
            } else {
                $fieldnotifications = $nonotificationicon;
            }
            $fieldinfo = implode('&nbsp;&nbsp;', array($fieldaccess, $fieldnotifications));

            // ACTIONS.
            if ($field instanceof \mod_dataform\pluginbase\dataformfield_internal) {
                $fieldactions = null;
            } else {
                $url = new moodle_url($editbaseurl, $sessparam + array('fid' => $fieldid));
                $linkparams = array('id' => "id_editfield$fieldid", 'title' => "$stredit $field->name");
                $fieldedit = html_writer::link($url, $editicon, $linkparams);

                $url = new moodle_url($actionbaseurl, $sessparam + array('duplicate' => $fieldid));
                $linkparams = array('id' => "id_duplicatefield$fieldid", 'title' => "$strduplicate $field->name");
                $fieldduplicate = html_writer::link($url, $duplicateicon, $linkparams);

                $url = new moodle_url($actionbaseurl, $sessparam + array('delete' => $fieldid));
                $linkparams = array('id' => "id_deletefield$fieldid", 'title' => "$strdelete $field->name");
                $fielddelete = html_writer::link($url, $deleteicon, $linkparams);

                $fieldselector = html_writer::checkbox("fieldselector", $fieldid, false, null, array('class' => 'fieldselector'));

                $fieldactions = implode('&nbsp;&nbsp;&nbsp;', array($fieldedit, $fieldduplicate, $fielddelete, $fieldselector));
            }

            $data = array();
            foreach (array_keys($headers) as $key) {
                $data[] = ${"field$key"};
            }

            $rows[] = $data;
        }

        // Generate the table.
        $table = new html_table();
        foreach ($headers as $header) {
            list($table->head[], $table->align[], $table->wrap[]) = $header;
        }

        $table->data = $rows;

        $title = $heading ? html_writer::tag('h3', $heading) : null;
        return $title. html_writer::table($table);
    }

    /**
     * Returns html for admin style list of filters.
     *
     * @param string $extorint Subset type external|internal
     * @param string $heading Title of list
     * @param array $fields List of available fields
     * @return string HTML fragment of html_table
     */
    public function filters_admin_list() {
        global $PAGE;

        if (!$this->_dataformid) {
            return null;
        }
        $df = mod_dataform_dataform::instance($this->_dataformid);
        $fm = $df->filter_manager;

        $editbaseurl = new moodle_url('/mod/dataform/filter/edit.php', array('d' => $this->_dataformid));
        $actionbaseurl = new moodle_url('/mod/dataform/filter/index.php', array('d' => $this->_dataformid));
        $sessparam = array('sesskey' => sesskey());

        // Strings.
        $strfilters = get_string('name');
        $strdescription = get_string('description');
        $strperpage = get_string('filterperpage', 'dataform');
        $strcustomsort = get_string('filtercustomsort', 'dataform');
        $strcustomsearch = get_string('filtercustomsearch', 'dataform');
        $strurlquery = get_string('filterurlquery', 'dataform');
        $strvisible = get_string('visible');
        $strhide = get_string('hide');
        $strshow = get_string('show');
        $stredit = get_string('edit');
        $strdelete = get_string('delete');
        $strduplicate = get_string('duplicate');
        $strdefault = get_string('default');
        $strchoose = get_string('choose');

        // Action icons.
        $editicon = $this->output->pix_icon('t/edit', $stredit);
        $duplicateicon = $this->output->pix_icon('t/copy', $strduplicate);
        $deleteicon = $this->output->pix_icon('t/delete', $strdelete);
        $defaulticon = $this->output->pix_icon('t/check', $strdefault);
        $nodefaulticon = $this->output->pix_icon('i/completion-auto-n', $strchoose);
        $hideicon = $this->output->pix_icon('t/hide', $strhide);
        $showicon = $this->output->pix_icon('t/show', $strshow);

        $selectallnone = html_writer::checkbox('filterselectallnone', null, false, null, array('id' => 'id_filterselectallnone'));
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_select_allnone', array('filter'));

        $icon = new pix_icon('t/delete', get_string('multidelete', 'dataform'));
        $multidelete = $this->output->action_icon(null, $icon, null, array('id' => 'id_filter_bulkaction_delete'));
        $deleteurl = new moodle_url($actionbaseurl, $sessparam);
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('filter', 'delete', $deleteurl->out(false)));

        $headers = array(
            array($strfilters, 'left', false),
            array($strdescription, 'left', false),
            array($strvisible, 'center', false),
            array($strdefault, 'center', false),
            array("$multidelete &nbsp;$selectallnone", 'right', true),
        );

        $rows = array();
        foreach ($fm->get_filters() as $filterid => $filter) {
            $filtername = html_writer::link(new moodle_url($editbaseurl, array('fid' => $filterid)), $filter->name);
            $filterdescription = format_text($filter->description, FORMAT_PLAIN);

            // Actions.
            $url = new moodle_url($editbaseurl, array('fid' => $filterid));
            $linkparams = array('id' => "id_editfilter$filterid", 'title' => "$stredit $filter->name");
            $filteredit = html_writer::link($url, $editicon, $linkparams);

            $url = new moodle_url($actionbaseurl, $sessparam + array('duplicate' => $filterid));
            $linkparams = array('id' => "id_duplicatefilter$filterid", 'title' => "$strduplicate $filter->name");
            $filterduplicate = html_writer::link($url, $duplicateicon, $linkparams);

            $url = new moodle_url($actionbaseurl, $sessparam + array('delete' => $filterid));
            $linkparams = array('id' => "id_deletefilter$filterid", 'title' => "$strdelete $filter->name");
            $filterdelete = html_writer::link($url, $deleteicon, $linkparams);

            $filterselector = html_writer::checkbox("filterselector", $filterid, false, null, array('class' => 'filterselector'));

            $filteractions = implode('&nbsp;&nbsp;&nbsp;', array($filteredit, $filterduplicate, $filterdelete, $filterselector));

            // Visible.
            $icon = $filter->visible ? $hideicon : $showicon;
            $filtervisible = html_writer::link(new moodle_url($actionbaseurl, $sessparam + array('showhide' => $filterid)), $icon);

            // Default filter.
            if ($filterid == $df->defaultfilter) {
                $unseturl = new moodle_url($actionbaseurl, $sessparam + array('default' => -1));
                $idunsetdefault = str_replace(' ', '_', $filter->name). '_unset_default';
                $filterdefault = html_writer::link($unseturl, $defaulticon, array('id' => $idunsetdefault));
            } else {
                $seturl = new moodle_url($actionbaseurl, $sessparam + array('default' => $filterid));
                $idsetdefault = str_replace(' ', '_', $filter->name). '_set_default';
                $filterdefault = html_writer::link($seturl, $nodefaulticon, array('id' => $idsetdefault));
            }

            $rows[] = array(
                $filtername,
                $filterdescription,
                $filtervisible,
                $filterdefault,
                $filteractions,
            );
        }

        $table = new html_table();
        $table->head = array();
        $table->align = array();
        $table->wrap = array();

        foreach ($headers as $header) {
            list($table->head[], $table->align[], $table->wrap[]) = $header;
        }
        $table->attributes['align'] = 'center';
        $table->data = $rows;

        echo html_writer::table($table);
    }

    /**
     *
     */
    public function add_filter_link() {
        if (!$this->_dataformid) {
            return null;
        }

        echo html_writer::empty_tag('br');
        echo html_writer::start_tag('div', array('class' => 'fieldadd mdl-align'));
        echo html_writer::link(new moodle_url('/mod/dataform/filter/edit.php', array('d' => $this->_dataformid)), get_string('filteradd', 'dataform'));
        // Echo $OUTPUT->help_icon('filteradd', 'dataform');.
        echo html_writer::end_tag('div');
        echo html_writer::empty_tag('br');
    }

    /**
     *
     */
    public function rules_admin_list($cat, $ruletypename, $blocktype, $rules) {
        if (!$this->_dataformid) {
            return null;
        }

        $baseurl = "/mod/dataform/$cat/index.php";
        $ruletype = str_replace("dataform$cat", '', $blocktype);

        // Add icon.
        $params = array(
            'd' => $this->_dataformid,
            'bui_addblock' => $blocktype,
            'edit' => 1,
            'sesskey' => sesskey(),
        );
        $url = new moodle_url($baseurl, $params);
        $pix = $this->output->pix_icon('t/add', get_string('ruleadd', 'dataform'));
        $linkparams = array('id' => "id_add_{$ruletype}_{$cat}_rule");
        $addlink = html_writer::link($url, $pix, $linkparams);

        echo html_writer::tag('h3', $ruletypename. "  $addlink");

        // Table headings.
        $strname = get_string('name');
        $strdescription = get_string('description');
        $strpermissions = get_string('permissions', 'role');
        $strtimefrom = get_string('from');
        $strtimeto = get_string('to');
        $strapplyto = get_string('views', 'dataform');
        $stredit = get_string('edit');
        $strdelete = get_string('delete');
        $strhide = get_string('hide');
        $strshow = get_string('show');

        $headers = array(
            array($strname, 'left', false),
            array($strdescription, 'left', false),
            array("$strtimefrom .. $strtimeto", 'left', false),
            array($strapplyto, 'left', false),
            array('', 'center', false),
        );

        $table = new html_table();
        foreach ($headers as $header) {
            list($table->head[], $table->align[], $table->wrap[]) = $header;
        }

        $count = 0;
        foreach ($rules as $rule) {
            $block = $rule->get_block();
            $blockid = $block->instance->id;
            $data = $rule->get_data();
            $idforaction = $cat. $rule->type. ++$count;

            // Name.

            // From to.
            $timeformat = '%a, %Y-%m-%d %H:%M';
            $timefrom = $data->timefrom ? userdate($data->timefrom, $timeformat) : '';
            $timeto = $data->timeto ? userdate($data->timeto, $timeformat) : '';

            // Applicable views.
            $applicableviews = '';
            if ($views = $rule->get_applicable_views()) {
                $applicableviews = \html_writer::alist($views);
            }

            // Show/hide.
            if (!empty($data->enabled)) {
                $showhide = 'hide';
                $able = 'disable';
            } else {
                $showhide = 'show';
                $able = 'enable';
            }
            $params = array(
                'd' => $this->_dataformid,
                'type' => $rule->type,
                $able => $block->instance->id,
                'sesskey' => sesskey()
            );
            $url = new moodle_url($baseurl, $params);
            $pix = $this->output->pix_icon("t/$showhide", get_string($showhide));
            $linkparams = array('id' => "id_showhide$idforaction");
            $showhidelink = html_writer::link($url, $pix, $linkparams);

            // Edit settings.
            $params = array(
                'd' => $this->_dataformid,
                'bui_editid' => $block->instance->id,
                'edit' => 1,
                'sesskey' => sesskey()
            );
            $url = new moodle_url($baseurl, $params);
            $pix = $this->output->pix_icon('t/edit', '');
            $linkparams = array('id' => "id_edit$idforaction");
            $editlink = html_writer::link($url, $pix, $linkparams);

            // Edit permissions.
            $params = array(
                'd' => $this->_dataformid,
                'contextid' => $block->context->id,
            );
            $url = new moodle_url('/admin/roles/permissions.php', $params);
            $pix = $this->output->pix_icon('i/edit', get_string('edit'));
            $linkparams = array('id' => "id_editperm$idforaction");
            $editpermlink = html_writer::link($url, $pix, $linkparams);

            // Delete.
            $params = array(
                'd' => $this->_dataformid,
                'type' => $rule->type,
                'delete' => $block->instance->id,
                'sesskey' => sesskey()
            );
            $url = new moodle_url($baseurl, $params);
            $pix = $this->output->pix_icon('t/delete', get_string('delete'));
            $linkparams = array('id' => "id_delete$idforaction");
            $deletelink = html_writer::link($url, $pix, $linkparams);

            $table->data[] = array(
                $data->name,
                $data->description,
                "$timefrom .. $timeto",
                $applicableviews,
                "$showhidelink $editlink $editpermlink $deletelink",
            );
        }

        echo html_writer::tag('div', html_writer::table($table), array('class' => 'itemslist'));
    }

    /**
     *
     * @return string HTML fragment
     */
    protected function render_notifications() {
        if (!$this->_dataformid) {
            return null;
        }

        $df = mod_dataform_dataform::instance($this->_dataformid);
        $o = '';

        if (!$df->notifications) {
            return null;
        }

        foreach ($df->notifications as $goodorbad => $notes) {
            if (!empty($notes)) {
                foreach ($notes as $notification) {
                    if (!empty($notification)) {
                        if ($goodorbad == 'success') {
                            $o .= $this->output->notification($notification, 'notifysuccess');    // good (usually green)
                        } else {
                            $o .= $this->output->notification($notification);    // bad (usually red)
                        }
                    }
                }
            }
        }

        return $o;
    }

    /**
     * Renders the groups menu.
     *
     * @param int $view View id
     * @param int $filter Filter id
     * @return string HTML fragment
     */
    protected function render_groups_menu($view, $filter) {
        if (!$this->_dataformid) {
            return null;
        }

        $df = mod_dataform_dataform::instance($this->_dataformid);
        if (!$df->groupmode) {
            return null;
        }

        $params = array(
            'd' => $this->_dataformid,
            'view' => $view
        );
        if ($filter) {
            $params['filter'] = $filter;
        }
        $pagefile = $df->pagefile;
        $returnurl = new moodle_url("/mod/dataform/$pagefile.php", $params);
        return groups_print_activity_menu($df->cm, $returnurl.'&amp;', true);
    }

    /**
     * Renders the dataform intro.
     *
     * @return string HTML fragment
     */
    protected function render_intro() {
        if (!$this->_dataformid) {
            return null;
        }

        $df = mod_dataform_dataform::instance($this->_dataformid);
        if ($df->intro) {
            $options = new stdClass();
            $options->noclean = true;
            return $this->output->box(format_module_intro('dataform', $df->data, $df->cm->id), 'generalbox', 'intro');
        }
    }

    /**
     * Prints the Dataform tabs
     */
    protected function render_tabs($currenttab) {
        global $PAGE;

        if (!$this->_dataformid) {
            return null;
        }

        $df = mod_dataform_dataform::instance($this->_dataformid);
        $dfid = $this->_dataformid;

        // Must be in an active dataform instance.
        if (empty($currenttab) or !$df->data or !$df->course) {
            throw new moodle_exception('emptytab', 'dataform');
        }

        // Tabs are displayed only for managers.
        if (!isloggedin() or !$manager = $df->user_manage_permissions) {
            return null;
        }

        // Don't display if browsing and not editing.
        if ($currenttab == 'browse' and !$PAGE->user_is_editing()) {
            return null;
        }

        $manageurl = new moodle_url('/mod/dataform/view/index.php', array('d' => $dfid));
        $browseurl = new moodle_url('/mod/dataform/view.php', array('d' => $dfid));

        // Main level.
        $browse = new tabobject('browse', $browseurl, get_string('browse', 'dataform'));
        $manage = new tabobject('manage', $manageurl, get_string('manage', 'dataform'));

        $maintabs = array($browse, $manage);
        // Add view edit tab.
        if ($currenttab == 'browse' and $manager['views'] and $currentview = $df->currentview and $currentview->id) {
            $params = array('d' => $dfid, 'sesskey' => sesskey(), 'vedit' => $currentview->id);
            $editviewurl = new moodle_url('/mod/dataform/view/edit.php', $params);
            $pix = $this->output->pix_icon('t/edit', get_string('vieweditthis', 'dataform'));
            $maintabs[] = new tabobject('editview', $editviewurl, $pix, get_string('vieweditthis', 'dataform'));
        }

        if ($currenttab != 'browse') {
            $inactive = $manage->inactive = true;

            // Management tabs.
            $tabs = array();
            // Views.
            if ($manager['views']) {
                $url = new moodle_url('/mod/dataform/view/index.php', array('d' => $dfid));
                $tabs[] = new tabobject('views', $url, get_string('views', 'dataform'));
            }
            // Fields.
            if ($manager['fields']) {
                $url = new moodle_url('/mod/dataform/field/index.php', array('d' => $dfid));
                $tabs[] = new tabobject('fields', $url, get_string('fields', 'dataform'));
            }
            // Filters.
            if ($manager['filters']) {
                $url = new moodle_url('/mod/dataform/filter/index.php', array('d' => $dfid));
                $tabs[] = new tabobject('filters', $url, get_string('filters', 'dataform'));
            }
            // Access.
            if ($manager['access']) {
                $url = new moodle_url('/mod/dataform/access/index.php', array('d' => $dfid));
                $tabs[] = new tabobject('access', $url, get_string('access', 'dataform'));
            }
            // Notifications.
            if ($manager['notifications']) {
                $url = new moodle_url('/mod/dataform/notification/index.php', array('d' => $dfid));
                $tabs[] = new tabobject('notification', $url, get_string('notifications'));
            }
            // Css.
            if ($manager['css']) {
                $url = new moodle_url('/mod/dataform/css.php', array('d' => $dfid, 'cssedit' => 1));
                $tabs[] = new tabobject('css', $url, get_string('cssinclude', 'dataform'));
            }
            // JS.
            if ($manager['js']) {
                $url = new moodle_url('/mod/dataform/js.php', array('d' => $dfid, 'jsedit' => 1));
                $tabs[] = new tabobject('js', $url, get_string('jsinclude', 'dataform'));
            }
            // Tools.
            if ($manager['tools']) {
                $url = new moodle_url('/mod/dataform/tool/index.php', array('d' => $dfid));
                $tabs[] = new tabobject('tools', $url, get_string('tools', 'dataform'));
            }
            // Preses.
            if ($manager['presets']) {
                $url = new moodle_url('/mod/dataform/preset/index.php', array('d' => $dfid));
                $tabs[] = new tabobject('presets', $url, get_string('presets', 'dataform'));
            }

            $manage->subtree = $tabs;
        }

        return $this->output->tabtree($maintabs, $currenttab);
    }

    /**
     * Overriding paging bar rendering to add more css selectors,
     * and remove the label.
     *
     * @param paging_bar $pagingbar
     * @return string
     */
    protected function render_paging_bar(paging_bar $pagingbar) {
        $output = '';
        $pagingbar = clone($pagingbar);
        $pagingbar->prepare($this, $this->page, $this->target);

        if ($pagingbar->totalcount > $pagingbar->perpage) {
            if (!empty($pagingbar->previouslink)) {
                $output .= html_writer::tag('div', $pagingbar->previouslink, array('class' => 'previous'));
            }

            if (!empty($pagingbar->firstlink)) {
                $output .= html_writer::tag('div', $pagingbar->firstlink, array('class' => 'first'));
            }

            foreach ($pagingbar->pagelinks as $key => $link) {
                if ($key == $pagingbar->page) {
                    $output .= html_writer::tag('div', $link, array('class' => 'current'));
                } else {
                    $output .= html_writer::tag('div', $link, array('class' => "link page$key"));
                }
            }

            if (!empty($pagingbar->lastlink)) {
                $output .= html_writer::tag('div', $pagingbar->lastlink, array('class' => 'last'));
            }

            if (!empty($pagingbar->nextlink)) {
                $output .= html_writer::tag('div', $pagingbar->nextlink, array('class' => 'next'));
            }
            $output .= html_writer::tag('div', null, array('class' => 'clearfix'));
        }

        return html_writer::tag('div', $output, array('class' => 'paging'));
    }
}

/**
 * A custom renderer class that extends the plugin_renderer_base and is used by the dataform module.
 *
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_dataform_dataformview_renderer extends plugin_renderer_base {

    /* @var dataform The dataform object for this output instance. */
    private $_dataformid;

    /**
     * Add a dataform instance to the renderer.
     * This is required for {@link render_groups_menu()},
     * {@link render_intro()}, {@link render_tabs()}.
     *
     * @param dataform
     */
    public function set_df($dataformid) {
        $this->_dataformid = $dataformid;
    }

    /**
     *
     */
    public function render_views_menu($view) {
        $viewman = mod_dataform_view_manager::instance($view->dataid);
        $filter = $view->get_filter();
        $baseurl = $view->get_baseurl();

        $viewjump = '';

        if ($menuviews = $viewman->views_navigation_menu) {
            if (count($menuviews) == 1) {
                $viewjump = reset($menuviews);
            } else {
                // Display the view form jump list.
                $baseurl = $baseurl->out_omit_querystring();
                $baseurlparams = array('d' => $view->dataid,
                                        'filter' => $filter->id);
                $select = new single_select(new moodle_url($baseurl, $baseurlparams), 'view', $menuviews, $view->id, null, 'viewbrowse_jump');
                $select->attributes = array('id' => 'id_viewsmenu');
                $viewjump = $this->render($select);
            }
        }

        return $viewjump;
    }

    /**
     *
     */
    public function render_filters_menu($view) {
        $df = $view->get_df();
        $filter = $view->filter;
        $baseurl = $view->baseurl;

        $filterjump = '';

        if ($filter->id or $view->entry_manager->get_count(mod_dataform_entry_manager::COUNT_VIEWABLE)) {
            $fm = mod_dataform_filter_manager::instance($df->id);
            if (!$menufilters = $fm->get_filters(null, true)) {
                $menufilters = array();
            }
            if ($userfilters = $fm->get_user_filters_menu($view->id)) {
                // Quick filter.
                if (array_key_exists($fm::USER_FILTER_QUICK, $userfilters)) {
                    $quickfilter = array(
                        $fm::USER_FILTER_QUICK => $userfilters[$fm::USER_FILTER_QUICK],
                        $fm::USER_FILTER_QUICK_RESET => $userfilters[$fm::USER_FILTER_QUICK_RESET],
                    );
                    $menufilters[] = array(get_string('filterquick', 'dataform') => $quickfilter);
                    unset($userfilters[$fm::USER_FILTER_QUICK]);
                    unset($userfilters[$fm::USER_FILTER_QUICK_RESET]);
                }
                $menufilters[] = array(get_string('filtersaved', 'dataform') => $userfilters);
            }

            $baseurl = $baseurl->out_omit_querystring();
            $baseurlparams = array('d' => $df->id,
                                    'view' => $view->id);

            // Display the filter form jump list.
            $select = new single_select(new moodle_url($baseurl, $baseurlparams), 'filter', $menufilters, $filter->id, array('' => 'choosedots'), 'filterbrowse_jump');
            $select->attributes = array('id' => 'id_filtersmenu');
            $filterjump = $this->output->render($select);
        }

        return $filterjump;
    }

    /**
     *
     */
    public function render_quick_search($view) {

        $df = $view->get_df();
        $filter = $view->get_filter();
        $baseurl = $view->get_baseurl();

        $quicksearchjump = '';

        $baseurl = $baseurl->out_omit_querystring();
        $baseurlparams = array('d' => $df->id,
                                'sesskey' => sesskey(),
                                'view' => $view->id,
                                'filter' => mod_dataform_filter_manager::USER_FILTER_QUICK);

        if ($filter->id < 0 and $filter->search) {
            $searchvalue = $filter->search;
        } else {
            $searchvalue = '';
        }

        // Display the quick search form.
        $inputfield = html_writer::empty_tag('input', array('type' => 'text',
                                                            'name' => 'usearch',
                                                            'value' => $searchvalue,
                                                            'size' => 20));

        $formparams = '';
        foreach ($baseurlparams as $var => $val) {
            $formparams .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $var, 'value' => $val));
        }

        $attributes = array('method' => 'post', 'action' => new moodle_url($baseurl));
        $qsform = html_writer::tag('form', "$formparams&nbsp;$inputfield", $attributes);

        // And finally one more wrapper with class.
        $quicksearchjump = html_writer::tag('div', $qsform, array('class' => 'singleselect'));

        return $quicksearchjump;
    }

    /**
     *
     */
    public function render_quick_perpage($view) {
        $df = $view->get_df();
        $filter = $view->get_filter();
        $baseurl = $view->get_baseurl();

        $perpagejump = '';

        $baseurl = $baseurl->out_omit_querystring();
        $baseurlparams = array(
            'd' => $df->id,
            'view' => $view->id,
            'filter' => mod_dataform_filter_manager::USER_FILTER_QUICK,
        );

        if ($filter->id < 0 and $filter->perpage) {
            $perpagevalue = $filter->perpage;
        } else {
            $perpagevalue = 0;
        }

        $options = array(
            1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10, 15 => 15,
            20 => 20, 30 => 30, 40 => 40, 50 => 50,
            100 => 100, 200 => 200, 300 => 300, 400 => 400, 500 => 500, 1000 => 1000
        );
        // Display the view form jump list.
        $select = new single_select(new moodle_url($baseurl, $baseurlparams), 'uperpage', $options, $perpagevalue, array('' => 'choosedots'), 'perpage_jump');

        return $this->output->render($select);
    }

    /**
     *
     */
    public function render_pagingbar($view) {
        $df = mod_dataform_dataform::instance($view->dataid);
        $output = $df->get_renderer();

        $filter = $view->get_filter();
        $baseurl = $view->get_baseurl();

        // Typical groupby, one group per page case. show paging bar as per number of groups.
        if ($filter->pagenum) {
            $pagingbar = new paging_bar($filter->pagenum,
                                        $filter->page,
                                        1,
                                        $baseurl. '&amp;',
                                        'page',
                                        '',
                                        true);
            return $output->render($pagingbar);
        }

        // Standard paging bar case.
        if ($filter->perpage) {

            $entryman = $view->entry_manager;
            $filteredcount = $entryman->entries ? $entryman->get_count(mod_dataform_entry_manager::COUNT_FILTERED) : 0;
            $displayedcount = $entryman->entries ? $entryman->get_count(mod_dataform_entry_manager::COUNT_DISPLAYED) : 0;

            // Adjust filter page if needed.
            // This may be needed if redirecting from entry form to paged view.
            if ($filter->eids and !$filter->page) {
                if ($entryid = (is_array($filter->eids) ? $filter->eids[0] : $filter->eids) and $entryid > 0) {
                    $filter->page = $entryman->get_entry_position($entryid, $filter);
                }
            }

            if ($filteredcount and $displayedcount and $filteredcount != $displayedcount) {
                $url = new moodle_url($baseurl, array('filter' => $filter->id));

                $pagingbar = new paging_bar(
                    $filteredcount,
                    $filter->page,
                    $filter->perpage,
                    $url. '&amp;',
                    'page',
                    '',
                    true
                );
                return $output->render($pagingbar);
            }
        }
        return null;
    }

    /**
     *
     */
    public function render_advanced_filter($view) {
        $df = $view->df;
        $urlparams = array(
            'd' => $df->id,
            'view' => $view->id,
            'pagefile' => $df->pagefile,
        );
        if ($filterid = $view->filter->id and $filterid <= mod_dataform_filter_manager::USER_FILTER_ID_START) {
            $urlparams['fid'] = $filterid;
        }

        $url = new moodle_url('/mod/dataform/filter/editadvanced.php', $urlparams);
        $label = html_writer::tag('span', get_string('filteradvanced', 'dataform'));
        return html_writer::link($url, $label, array('class' => ''));
    }

}
