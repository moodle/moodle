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
 * @package dataformview
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_dataform\pluginbase;
defined('MOODLE_INTERNAL') or die;
/**
 * Base class for view patterns
 */
class dataformviewpatterns {
    const PATTERN_SHOW_IN_MENU = 0;
    const PATTERN_CATEGORY = 1;
    protected $_view = null;
    protected $_patterns;
    /**
     * Constructor
     */
    public function __construct(&$view) {
        $this->_view = $view;
    }
    /**
     * Search and collate view patterns that occur in given text
     *
     * @param string Text that may contain view patterns
     * @param array Optional list of patterns to search in text
     * @return array View patterns found in the text
     */
    public function search($text, array $patterns = null) {
        $viewid = $this->_view->id;
        if (!$patterns and !$patterns = array_keys($this->patterns())) {
            return array();
        }
        $found = array();
        // Prepare for regexp patterns.
        $regexppatterns = array();
        foreach ($this->patterns() as $tag => $attrs) {
            if (!empty($attrs[2])) {
                $regexppatterns[$tag] = $tag;
            }
        }
        foreach ($patterns as $pattern) {
            if (in_array($pattern, $regexppatterns)) {
                // Regexp pattern.
                if (preg_match_all("%$pattern%", $text, $matches)) {
                    foreach ($matches[0] as $match) {
                        $found[$match] = $match;
                    }
                }
            } else {
                // Fixed string pattern.
                if (strpos($text, $pattern) !== false) {
                    $found[$pattern] = $pattern;
                }
            }
        }
        return $found;
    }
    /**
     * Returns a flat list of the view patterns.
     *
     * @return array
     */
    public final function get_list($showall = false) {
        // All.
        if ($showall) {
            return array_keys($this->patterns());
        }
        // Only visible.
        $patternsmenu = array();
        foreach ($this->patterns() as $tag => $pattern) {
            if ($pattern[self::PATTERN_SHOW_IN_MENU]) {
                // Which category.
                if (!empty($pattern[self::PATTERN_CATEGORY])) {
                    $cat = $pattern[self::PATTERN_CATEGORY];
                } else {
                    $cat = get_string('views', 'dataform');
                }
                // Prepare array.
                if (!isset($patternsmenu[$cat])) {
                    $patternsmenu[$cat] = array();
                }
                // Add tag.
                $patternsmenu[$cat][$tag] = $tag;
            }
        }
        return $patternsmenu;
    }
    /**
     * Returns a categorized list of the view patterns.
     *
     * @return array Associative array of associative arrays
     */
    public final function get_menu($showall = false) {
        // The default menu category for views.
        $patternsmenu = array();
        foreach ($this->patterns() as $tag => $pattern) {
            if ($showall or $pattern[self::PATTERN_SHOW_IN_MENU]) {
                // Which category.
                if (!empty($pattern[self::PATTERN_CATEGORY])) {
                    $cat = $pattern[self::PATTERN_CATEGORY];
                } else {
                    $cat = get_string('views', 'dataform');
                }
                // Prepare array.
                if (!isset($patternsmenu[$cat])) {
                    $patternsmenu[$cat] = array();
                }
                // Add tag.
                $patternsmenu[$cat][$tag] = $tag;
            }
        }
        return $patternsmenu;
    }
    /**
     *
     */
    public function get_replacements(array $patterns, $entry = null, array $options = array()) {
        global $CFG, $OUTPUT;
        $view = $this->_view;
        $viewname = $view->name;
        $info = array_keys($this->info_patterns());
        $ref = array_keys($this->ref_patterns());
        $userpref = array_keys($this->userpref_patterns());
        $actions = array_keys($this->action_patterns());
        $paging = array_keys($this->paging_patterns());
        $fieldview = array_keys($this->fieldview_patterns());
        $options['filter'] = $view->get_filter();
        $options['baseurl'] = new \moodle_url($view->get_baseurl(), array('sesskey' => sesskey()));
        $edit = !empty($options['edit']) ? $options['edit'] : false;
        // ##entries## ##editentry## ##newentry## should be processed first.
        $unshiftpatterns = array();
        foreach (array('##entries##', '##editentry##', '##newentry##') as $unshiftpattern) {
            if ($key = array_search($unshiftpattern, $patterns) or $key === 0) {
                $unshiftpatterns[] = $unshiftpattern;
                unset($patterns[$key]);
            }
        }
        if ($unshiftpatterns) {
            $patterns = array_merge($unshiftpatterns, $patterns);
        }
        $replacements = array_fill_keys($patterns, '');
        foreach ($patterns as $pattern) {
            if (in_array($pattern, $info)) {
                $replacements[$pattern] = $this->get_info_replacement($pattern, $entry, $options);
            } else if (in_array($pattern, $userpref)) {
                $replacements[$pattern] = $this->get_userpref_replacement($pattern, $entry, $options);
            } else if (in_array($pattern, $actions)) {
                $replacements[$pattern] = $this->get_action_replacement($pattern, $entry, $options);
            } else if (in_array($pattern, $paging)) {
                $replacements[$pattern] = $this->get_paging_replacement($pattern, $entry, $options);
            } else if (in_array($pattern, $fieldview)) {
                $replacements[$pattern] = $this->get_fieldview_replacement($pattern, $entry, $options);
            } else if (in_array($pattern, $ref) or $this->is_regexp_pattern($pattern, $this->ref_patterns())) {
                $replacements[$pattern] = $this->get_ref_replacement($pattern, $entry, $options);
            }
        }
        return $replacements;
    }
    /**
     *
     */
    protected function get_info_replacement($tag, $entry = null, array $options = null) {
        global $OUTPUT;
        $view = $this->_view;
        $entryman = $view->entry_manager;
        $replacement = '';
        switch ($tag) {
            // Print notifications.
            case '##notifications##':
                if ($notes = $view->df->get_notifications()) {
                    $notifications = array();
                    foreach ($notes as $class => $items) {
                        foreach ($items as $note) {
                            $notifications[] = $OUTPUT->notification($note, 'notify'.$class);
                        }
                    }
                    $replacement = implode('', $notifications);
                }
                break;
            case '##numentriestotal##':
                $replacement = $entryman->get_count();
                break;
            case '##numentriesviewable##':
                $replacement = $entryman->get_count(\mod_dataform_entry_manager::COUNT_VIEWABLE);
                break;
            case '##numentriesfiltered##':
                $replacement = $entryman->get_count(\mod_dataform_entry_manager::COUNT_FILTERED);
                break;
            case '##numentriesdisplayed##':
                $replacement = $entryman->get_count(\mod_dataform_entry_manager::COUNT_DISPLAYED);
                break;
            case '##newentry##':
                // Get the form.
                $mform = $view->get_new_entry_form();
                $replacement = $mform->render();
                break;
            case '##editentry##':
                if (!$view->editentries) {
                    $entryid = -1;
                }
                if ($view->editentries) {
                    $entryids = explode(',', $view->editentries);
                    $entryid = reset($entryids);
                    $entryid = ($entryid < 0 ? -1 : $entryid);
                }
                if ($entryid == -1) {
                    $mform = $view->get_new_entry_form();
                    $replacement = $mform->render();
                } else if ($entryid > 0) {
                    $view->editentries = $entryid;
                    $replacement = $view->get_entries_display($options);
                }
                // Notify something.
                break;
            case '##entries##':
                $replacement = $view->get_entries_display($options);
                break;
        }
        return $replacement;
    }
    /**
     *
     */
    protected function get_ref_replacement($tag, $entry = null, array $options = null) {
        global $PAGE;
        $output = $PAGE->get_renderer('mod_dataform', 'dataformview');
        if ($tag == '##viewsmenu##') {
            return $output->render_views_menu($this->_view);
        }
        if ($tag == '##filtersmenu##') {
            return $output->render_filters_menu($this->_view);
        }
        // This view url.
        if ($tag == '##viewurl##') {
            return $this->get_viewurl_replacement($this->_view->name);
        }
        // Named view url.
        if (strpos($tag, '##viewurl:') === 0) {
            list(, $viewname) = explode(':', trim($tag, '#'));
            return $this->get_viewurl_replacement($viewname);
        }
        // Named view link.
        if (strpos($tag, '##viewlink:') === 0) {
            list(, $args) = explode(':', trim($tag, '#'), 2);
            return $this->get_viewlink_replacement($args);
        }
        // Named view session link.
        if (strpos($tag, '##viewsesslink:') === 0) {
            list(, $args) = explode(':', trim($tag, '#'), 2);
            return $this->get_viewlink_replacement($args, true);
        }
        // Named view content.
        if (strpos($tag, '##viewcontent:') === 0) {
            list(, $viewname) = explode(':', trim($tag, '#'));
            return $this->get_viewcontent_replacement($viewname);
        }
        return '';
    }
    /**
     *
     */
    protected function get_userpref_replacement($tag, $entry = null, array $options = null) {
        global $PAGE;
        $view = $this->_view;
        $filter = $view->get_filter();
        $entryman = $view->entry_manager;
        $entriescount = $entryman->entries ? $entryman->get_count(\mod_dataform_entry_manager::COUNT_VIEWABLE) : 0;
        if ($filter->id or $entriescount) {
            $output = $PAGE->get_renderer('mod_dataform', 'dataformview');
            switch ($tag) {
                // Deprecate (at some point).
                case '##advancedfilter##':
                    return $output->render_advanced_filter($view);
                case '##quicksearch##':
                    return $output->render_quick_search($view);
                case '##quickperpage##':
                    return $output->render_quick_perpage($view);
            }
        }
        return '';
    }
    /**
     *
     */
    protected function get_action_replacement($tag, $entry = null, array $options = null) {
        global $CFG, $OUTPUT, $PAGE;
        $replacement = '';
        $view = $this->_view;
        $df = $view->df;
        $filter = $view->get_filter();
        $baseurl = new \moodle_url($view->get_baseurl(), array('sesskey' => sesskey()));
        // Add entries.
        if ($tag == '##addnewentry##' or $tag == '##addnewentries##') {
            $baseurl = new \moodle_url($view->get_baseurl());
            // Can this user registered or anonymous add entries.
            if ( !$view->allows_submission()) {
                return '';
            }
            $accessparams = array('dataformid' => $view->dataid, 'viewid' => $view->id);
            if (!\mod_dataform\access\entry_add::validate($accessparams)) {
                return '';
            }
            if ($tag == '##addnewentry##') {
                $baseurl->param('editentries', -1);
                $label = \html_writer::tag('span', get_string('entryaddnew', 'dataform'));
                return \html_writer::link($baseurl, $label, array('class' => 'addnewentry'));
            } else {
                $options = array_combine(range(-1, -20), range(1, 20));
                $select = new \single_select(new \moodle_url($baseurl), 'editentries', $options, null, array(0 => get_string('dots', 'dataform')), 'newentries_jump');
                $select->set_label(get_string('entryaddmultinew', 'dataform'). '&nbsp;');
                return $OUTPUT->render($select);
            }
        }
        return '';
    }
    /**
     *
     */
    protected function get_paging_replacement($tag, $entry = null, array $options = null) {
        global $PAGE;
        $output = $PAGE->get_renderer('mod_dataform', 'dataformview');
        if ($tag == '##paging:bar##') {
            return $output->render_pagingbar($this->_view);
        }
        return '';
    }
    /**
     *
     */
    protected function get_fieldview_replacement($tag, $entry = null, array $options = null) {
        $view = $this->_view;
        // Get the field name.
        list($fieldname) = explode(':', trim($tag, '#[]'));
        // Get the field.
        if ($field = $view->df->field_manager->get_field_by_name($fieldname)) {
            if ($replacements = $field->renderer->get_replacements(array($tag), $entry, $options)) {
                $replacement = reset($replacements);
                return $replacement;
            }
        }
        return null;
    }
    /**
     *
     */
    protected function get_viewurl_replacement($viewname) {
        $thisview = $this->_view;
        // Return this view's url.
        if ($viewname == $thisview->name) {
            return $thisview->get_baseurl()->out(false);
        }
        $df = $thisview->get_df();
        static $views = null;
        if (empty($views[$viewname])) {
            if ($view = $thisview->df->view_manager->get_view_by_name($viewname)) {
                $views[$view->name] = $view;
                return $view->get_baseurl()->out(false);
            }
        } else {
            return $views[$viewname]->get_baseurl()->out(false);
        }
        return '';
    }
    /**
     *
     */
    protected function get_viewlink_replacement($args, $sess = false) {
        global $OUTPUT;
        $thisview = $this->_view;
        $view = null;
        static $views = array();
        list($viewname, $linktext, $urlquery, ) = array_merge(explode(';', $args), array(null, null, null));
        // Return this view's url.
        if ($viewname == $thisview->name) {
            $view = $thisview;
        } else {
            if (empty($views[$viewname])) {
                if ($view = $thisview->df->view_manager->get_view_by_name($viewname)) {
                    $views[$view->name] = $view;
                }
            } else {
                $view = $views[$viewname];
            }
        }
        if ($view) {
            $linkparams = array();
            // Link text.
            if ($linktext) {
                if (strpos($linktext, '_pixicon:') === 0) {
                    list(, $icon, $titletext) = explode(':', $linktext);
                    $linktext = $OUTPUT->pix_icon($icon, $titletext);
                }
            } else {
                $linktext = $view->name;
            }
            // Link query.
            if ($urlquery) {
                foreach (explode('|', $urlquery) as $urlparam) {
                    list($key, $value) = explode('=', $urlparam, 2);
                    $linkparams[$key] = $value;
                }
            }
            if ($sess) {
                $linkparams['sesskey'] = sesskey();
            } else {
                unset($linkparams['sesskey']);
            }
            unset($linkparams['d']);
            unset($linkparams['view']);
            $viewlink = new \moodle_url($view->baseurl, $linkparams);
            return \html_writer::link($viewlink, $linktext);
        }
        return '';
    }
    /**
     *
     */
    protected function get_viewcontent_replacement($viewname) {
        $thisview = $this->_view;
        // Cannot display current view or else infinite loop.
        if ($viewname == $thisview->name) {
            return '';
        }
        static $views = null;
        if (empty($views[$viewname])) {
            if ($view = $thisview->df->view_manager->get_view_by_name($viewname)) {
                $views[$viewname] = $view;
                return $view->display();
            }
        } else {
            return $views[$viewname]->display();
        }
        return '';
    }
    /**
     *
     */
    protected function patterns() {
        if (!$this->_patterns) {
            $this->_patterns = array_merge(
                $this->info_patterns(),
                $this->ref_patterns(),
                $this->userpref_patterns(),
                $this->action_patterns(),
                $this->paging_patterns(),
                $this->fieldview_patterns()
            );
        }
        return $this->_patterns;
    }
    /**
     *
     */
    protected function info_patterns() {
        $cat = get_string('entries', 'dataform');
        $patterns = array(
            '##notifications##' => array(true, $cat),
            '##numentriestotal##' => array(true, $cat),
            '##numentriesviewable##' => array(true, $cat),
            '##numentriesfiltered##' => array(true, $cat),
            '##numentriesdisplayed##' => array(true, $cat),
            '##newentry##' => array(true, $cat),
            '##editentry##' => array(true, $cat),
            '##entries##' => array(true, $cat),
        );
        return $patterns;
    }
    /**
     *
     */
    protected function ref_patterns() {
        $cat = get_string('reference', 'dataform');
        $patterns = array(
            '##viewurl##' => array(true, $cat),
            '##viewsmenu##' => array(true, $cat),
            '##filtersmenu##' => array(true, $cat),
        );
        $viewman = $this->_view->df->view_manager;
        if ($views = $viewman->views_menu) {
            foreach ($views as $viewname) {
                $patterns["##viewurl:$viewname##"] = array(false);
                $patterns["##viewcontent:$viewname##"] = array(false);
                $patterns["##viewlink:$viewname##"] = array(false);
                // Third arg: Regexp pattern identifier.
                $patterns["##viewlink:$viewname;[^;]*;[^;]*;##"] = array(false, $cat, true);
                $patterns["##viewsesslink:$viewname;[^;]*;[^;]*;##"] = array(false, $cat, true);
            }
        }
        return $patterns;
    }
    /**
     *
     */
    protected function userpref_patterns() {
        $cat = get_string('userpref', 'dataform');
        $patterns = array(
            '##quicksearch##' => array(true, $cat),
            '##quickperpage##' => array(true, $cat),
            '##advancedfilter##' => array(true, $cat),
        );
        return $patterns;
    }
    /**
     *
     */
    protected function action_patterns() {
        $cat = get_string('generalactions', 'dataform');
        $patterns = array(
            '##addnewentry##' => array(true, $cat),
            '##addnewentries##' => array(true, $cat),
        );
        return $patterns;
    }
    /**
     *
     */
    protected function paging_patterns() {
        $cat = get_string('pagingbar', 'dataform');
        $patterns = array(
            '##paging:bar##' => array(true, $cat),
            '##paging:next##' => array(true, $cat),
            '##paging:previous##' => array(true, $cat),
            '##paging:last##' => array(true, $cat),
            '##paging:first##' => array(true, $cat),
        );
        return $patterns;
    }
    /**
     *
     */
    protected function fieldview_patterns() {
        $view = $this->_view;
        $patterns = array();
        if ($fields = $view->df->field_manager->get_fields()) {
            foreach ($fields as $field) {
                if ($fvpatterns = $field->renderer->get_view_patterns()) {
                    $patterns = array_merge($patterns, $fvpatterns);
                }
            }
        }
        return $patterns;
    }
    /**
     * Returns true if the specified pattern is a regexp pattern of the specified patterns group.
     *
     * @param string $pattern
     * @retrun array $group Patterns group
     * @retrun bool
     */
    protected function is_regexp_pattern($pattern, array $group) {
        foreach ($group as $tag => $attrs) {
            if (empty($attrs[2])) {
                continue;
            }
            if (preg_match("%^$tag%", $pattern)) {
                return true;
            }
        }
        return false;
    }
}
