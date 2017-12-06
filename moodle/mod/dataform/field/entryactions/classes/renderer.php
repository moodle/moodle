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
 * @package dataformfield
 * @subpackage entryactions
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_entryactions_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /** @var array Container dataform views menu. */
    protected $_viewsmenu = null;

    /**
     * Overriding {@link dataformfieldrenderer::get_pattern_import_settings()}
     * to return no import settings.
     *
     * @param moodleform $mform
     * @param string $pattern
     * @return array
     */
    public function get_pattern_import_settings(&$mform, $patternname, $header) {
        return array(array(), array());
    }

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $fieldname = $this->_field->name;
        $replacements = array_fill_keys(array_keys($patterns), '');

        // View patterns or editing new entry, so no replacements.
        if ($entry and $entry->id < 0) {
            return $replacements;
        }

        $editing = !empty($options['edit']);
        foreach ($patterns as $pattern) {
            $str = '';
            if (strpos($pattern, "[[$fieldname:more:") === 0 and !$editing) {
                // More for each view.
                list(, , $viewname) = explode(':', trim($pattern, '[]'));
                $str = $this->display_more($entry, array('view' => $viewname));
            } else if (strpos($pattern, "[[$fieldname:moreurl:") === 0) {
                // Moreurl for each view.
                list(, , $viewname) = explode(':', trim($pattern, '[]'));
                $str = $this->display_more($entry, array('view' => $viewname, 'url' => 1));
            } else if (strpos($pattern, "[[$fieldname:edit:") === 0 and !$editing) {
                // Edit for each view.
                list(, , $viewname) = explode(':', trim($pattern, '[]'));
                $str = $this->display_edit($entry, array('view' => $viewname));
            } else {
                switch ($pattern) {
                    // Reference.
                    case "[[$fieldname:more]]":
                        $str = !$editing ? $this->display_more($entry) : '';
                        break;
                    case "[[$fieldname:moreurl]]":
                        $str = $this->display_more($entry, array('url' => 1));
                        break;
                    case "[[$fieldname:anchor]]":
                        $str = html_writer::tag('a', '', array('name' => "entry$entry->id"));
                        break;

                    // Actions.
                    case "[[$fieldname:actionmenu]]":
                        $str = !$editing ? $this->display_action_menu($entry) : '';
                        break;
                    case "[[$fieldname:select]]":
                        $str = !$editing ? $this->display_select($entry) : '';
                        break;
                    case "[[$fieldname:edit]]":
                        $str = !$editing ? $this->display_edit($entry) : '';
                        break;
                    case "[[$fieldname:delete]]":
                        $str = !$editing ? $this->display_delete($entry) : '';
                        break;
                    case "[[$fieldname:export]]":
                        $str = !$editing ? $this->display_export($entry) : '';
                        break;
                    case "[[$fieldname:duplicate]]":
                        $str = !$editing ? $this->display_duplicate($entry) : '';
                        break;

                    // Bulk action.
                    case "[[$fieldname:selectallnone]]":
                        $str = $this->display_select_all_none($options);
                        break;
                    case "[[$fieldname:bulkedit]]":
                        $str = $this->display_bulk_edit($options);
                        break;
                    case "[[$fieldname:bulkduplicate]]":
                        $str = $this->display_bulk_duplicate($options);
                        break;
                    case "[[$fieldname:bulkdelete]]":
                        $str = $this->display_bulk_delete($options);
                        break;
                    case "[[$fieldname:bulkexport]]":
                        $str = $this->display_bulk_export($options);
                        break;

                    default: $str = '';
                }
            }
            $replacements[$pattern] = $str;
        }

        // Remove the selector if no actions.
        if (!$editing and !empty($replacements["[[$fieldname:select]]"])) {
            if (empty($replacements["[[$fieldname:edit]]"])
                    and empty($replacements["[[$fieldname:delete]]"])
                    and empty($replacements["[[$fieldname:export]]"])
                    and empty($replacements["[[$fieldname:duplicate]]"])) {
                $replacements["[[$fieldname:select]]"] = '';
            }
        }
        return $replacements;
    }

    /**
     *
     */
    protected function display_bulk_duplicate($options = null) {
        global $PAGE, $OUTPUT;

        if (!$showentryactions = empty($options['entryactions'])) {
            return '';
        }

        $url = !empty($options['baseurl']) ? $options['baseurl'] : $PAGE->url;
        $str = get_string('multiduplicate', 'dataform');
        $actionlink = new action_menu_link($url, new pix_icon('t/copy', $str), $str, true, array('id' => 'id_entry_bulkaction_duplicate'));
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('entry', 'duplicate', $url->out(false)));
        return $OUTPUT->render($actionlink);
    }

    /**
     *
     */
    protected function display_bulk_edit($options = null) {
        global $PAGE, $OUTPUT;

        if (!$showentryactions = empty($options['entryactions'])) {
            return '';
        }

        $url = !empty($options['baseurl']) ? $options['baseurl'] : $PAGE->url;
        $str = get_string('multiedit', 'dataform');
        $actionlink = new action_menu_link($url, new pix_icon('t/edit', $str), $str, true, array('id' => 'id_entry_bulkaction_editentries'));
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('entry', 'editentries', $url->out(false)));
        return $OUTPUT->render($actionlink);
    }

    /**
     *
     */
    protected function display_bulk_delete($options = null) {
        global $PAGE, $OUTPUT;

        if (!$showentryactions = empty($options['entryactions'])) {
            return '';
        }

        $url = !empty($options['baseurl']) ? $options['baseurl'] : $PAGE->url;
        $str = get_string('multidelete', 'dataform');
        $actionlink = new action_menu_link($url, new pix_icon('t/delete', $str), $str, true, array('id' => 'id_entry_bulkaction_delete'));
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('entry', 'delete', $url->out(false)));
        return $OUTPUT->render($actionlink);
    }

    /**
     *
     */
    protected function display_bulk_export($options = null) {
        global $PAGE, $OUTPUT, $CFG;

        if (!$showentryactions = empty($options['entryactions'])) {
            return '';
        }

        if (empty($CFG->enableportfolios)) {
            return '';
        }

        $url = !empty($options['baseurl']) ? $options['baseurl'] : $PAGE->url;
        if (!empty($format)) {
            $url->param('format', $format);
        }
        $str = get_string('multiexport', 'dataform');
        $actionlink = new action_menu_link($url, new pix_icon('t/portfolioadd', $str), $str, true, array('id' => 'id_entry_bulkaction_export'));
        $PAGE->requires->js_init_call('M.mod_dataform.util.init_bulk_action', array('entry', 'export', $url->out(false)));
        return $OUTPUT->render($actionlink);
    }

    /**
     *
     */
    protected function display_select_all_none($options = null) {
        global $PAGE;

        $PAGE->requires->js_init_call('M.mod_dataform.util.init_select_allnone', array('entry'));
        return html_writer::checkbox('entryselectallnone', null, false, null, array('id' => 'id_entryselectallnone'));
    }

    /**
     *
     */
    protected function display_action_menu($entry, array $options = null) {
        global $OUTPUT;

        $menu = new action_menu();
        $menu->set_menu_trigger($OUTPUT->pix_icon('t/contextmenu', ''));
        // Edit.
        if ($action = $this->display_edit($entry, array('actionmenu' => 1))) {
            $menu->add_secondary_action($action);
        }
        // More.
        if ($action = $this->display_more($entry, array('actionmenu' => 1))) {
            $menu->add_secondary_action($action);
        }

        // Duplicate.
        if ($action = $this->display_duplicate($entry, array('actionmenu' => 1))) {
            $menu->add_secondary_action($action);
        }

        // Export.
        if ($action = $this->display_export($entry, array('actionmenu' => 1))) {
            $menu->add_secondary_action($action);
        }

        // Delete.
        if ($action = $this->display_delete($entry, array('actionmenu' => 1))) {
            $menu->add_secondary_action($action);
        }

        return $OUTPUT->render_action_menu($menu);
    }

    /**
     *
     */
    protected function display_edit($entry, array $options = null) {
        global $OUTPUT;

        $field = $this->_field;
        $params = array(
            'editentries' => $entry->id,
        );
        $url = new moodle_url($entry->baseurl, $params);

        $views = $this->get_views_menu();
        $viewname = null;

        if ($currentview = $field->df->currentview) {
            $viewname = $currentview->name;
        }
        if (!empty($options['view'])) {
            // Designated view from pattern.
            if ($viewid = array_search($options['view'], $views)) {
                $viewname = $options['view'];
                $url->param('view', $viewid);
            } else {
                // View does not exist or cannot be accessed.
                return '';
            }
        } else if (!empty($entry->baseurl)) {
            $viewid = $entry->baseurl->param('view');
        }

        // Check update permissions for target view.
        $accessparams = array('dataformid' => $field->dataid, 'viewid' => $viewid, 'entry' => $entry);
        if (!mod_dataform\access\entry_update::validate($accessparams)) {
            return '';
        }

        $str = get_string('edit');
        $attributes = array('id' => "id_editentry$entry->id");
        $actionlink = new action_menu_link($url, new pix_icon('t/edit', $str), $str, true, $attributes);
        if (!empty($options['actionmenu'])) {
            return $actionlink;
        } else {
            return $OUTPUT->render($actionlink);
        }
    }

    /**
     *
     */
    protected function display_more($entry, array $options = null) {
        global $OUTPUT;

        $field = $this->_field;
        $params = array(
            'eids' => $entry->id
        );
        $url = new moodle_url($entry->baseurl, $params);
        if (!empty($options['view'])) {
            // Designated view from pattern.
            if ($views = $this->get_views_menu()) {
                if ($viewid = array_search($options['view'], $views)) {
                    $url->param('ret', $url->param('view'));
                    $url->param('view', $viewid);
                } else {
                    // View does not exist or cannot be accessed.
                    return '';
                }
            }
        } else if ($targetview = $field->get_target_view('more')) {
            // Designated view from default single.
            $url->param('ret', $url->param('view'));
            $url->param('view', $targetview);
        }

        if (!empty($options['url'])) {
            return $url->out(false);
        }

        $str = get_string('more', 'dataform');
        $attributes = array('id' => "id_moreentry$entry->id");
        $actionlink = new action_menu_link($url, new pix_icon('i/search', $str), $str, true, $attributes);
        if (!empty($options['actionmenu'])) {
            return $actionlink;
        } else {
            return $OUTPUT->render($actionlink);
        }
    }

    /**
     *
     */
    protected function display_duplicate($entry) {
        global $OUTPUT;

        $field = $this->_field;
        $params = array(
            'duplicate' => $entry->id,
            'sesskey' => sesskey()
        );
        $url = new moodle_url($entry->baseurl, $params);
        $str = get_string('copy');
        $attributes = array('id' => "id_duplicateentry$entry->id");
        $actionlink = new action_menu_link($url, new pix_icon('t/copy', $str), $str, true, $attributes);
        if (!empty($options['actionmenu'])) {
            return $actionlink;
        } else {
            return $OUTPUT->render($actionlink);
        }
    }

    /**
     *
     */
    protected function display_delete($entry) {
        global $OUTPUT;

        $field = $this->_field;

        // Check delete permissions for target view.
        $viewid = !empty($entry->baseurl) ? $entry->baseurl->param('view') : 0;
        $accessparams = array('dataformid' => $field->dataid, 'viewid' => $viewid, 'entry' => $entry);
        if (!mod_dataform\access\entry_delete::validate($accessparams)) {
            return '';
        }

        $params = array(
            'delete' => $entry->id,
            'sesskey' => sesskey()
        );
        $url = new moodle_url($entry->baseurl, $params);
        $str = get_string('delete');
        $attributes = array('id' => "id_deleteentry$entry->id");
        $actionlink = new action_menu_link($url, new pix_icon('t/delete', $str), $str, true, $attributes);
        if (!empty($options['actionmenu'])) {
            return $actionlink;
        } else {
            return $OUTPUT->render($actionlink);
        }
    }

    /**
     *
     */
    protected function display_export($entry) {
        global $CFG, $OUTPUT;

        if (!$CFG->enableportfolios) {
            return '';
        }

        $aman = $this->_field->get_df()->get_access_manager();
        if (!$aman->can_export_entry($entry)) {
            return '';
        }

        $field = $this->_field;
        $url = new moodle_url($entry->baseurl, array('export' => $entry->id, 'sesskey' => sesskey()));
        $str = get_string('export', 'dataform'). ' '. get_string('entry', 'dataform'). ' '. $entry->id;
        $attributes = array('id' => "id_exportentry$entry->id");
        $actionlink = new action_menu_link($url, new pix_icon('t/portfolioadd', $str), $str, true, $attributes);
        if (!empty($options['actionmenu'])) {
            return $actionlink;
        } else {
            return $OUTPUT->render($actionlink);
        }
    }

    /**
     *
     */
    protected function display_select($entry, array $options = null) {
        return html_writer::checkbox('entryselector', $entry->id, false, null, array('class' => 'entryselector'));
    }

    /**
     *
     */
    protected function get_views_menu() {
        if ($this->_viewsmenu === null) {
            $field = $this->_field;
            $viewman = mod_dataform_view_manager::instance($this->_field->df->id);
            $this->_viewsmenu = $viewman->views_menu;
        }

        return $this->_viewsmenu;
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;
        $patterns = array();

        $cat = get_string('pluginname', 'dataformfield_entryactions');

        // Actions.
        $patterns["[[$fieldname:actionmenu]]"] = array(true, $cat);
        $patterns["[[$fieldname:edit]]"] = array(true, $cat);
        $patterns["[[$fieldname:delete]]"] = array(true, $cat);
        $patterns["[[$fieldname:select]]"] = array(true, $cat);
        $patterns["[[$fieldname:export]]"] = array(true, $cat);
        $patterns["[[$fieldname:duplicate]]"] = array(true, $cat);

        // Reference.
        $patterns["[[$fieldname:anchor]]"] = array(true, $cat);
        $patterns["[[$fieldname:more]]"] = array(true, $cat);
        $patterns["[[$fieldname:moreurl]]"] = array(true, $cat);

        // Hidden patterns for view designated more and edit.
        if ($views = $this->get_views_menu()) {
            foreach ($views as $viewname) {
                $patterns["[[$fieldname:more:$viewname]]"] = array(false);
                $patterns["[[$fieldname:moreurl:$viewname]]"] = array(false);
                $patterns["[[$fieldname:edit:$viewname]]"] = array(false);
            }
        }

        return $patterns;
    }

    /**
     * Array of patterns this field supports in the view template
     * (that is, outside an entry). These patterns will be listed
     * in the view patterns selector in the view configuration form.
     * These patterns must start with fieldname: and then a specific tag.
     *
     * @return array pattern => array(visible in menu, category)
     */
    protected function view_patterns() {
        $fieldname = $this->_field->name;
        $cat = get_string('pluginname', 'dataformfield_entryactions');

        $patterns = array();

        $patterns["[[$fieldname:selectallnone]]"] = array(true, $cat);
        $patterns["[[$fieldname:bulkduplicate]]"] = array(true, $cat);
        $patterns["[[$fieldname:bulkedit]]"] = array(true, $cat);
        $patterns["[[$fieldname:bulkdelete]]"] = array(true, $cat);
        $patterns["[[$fieldname:bulkexport]]"] = array(true, $cat);

        return $patterns;
    }
}
