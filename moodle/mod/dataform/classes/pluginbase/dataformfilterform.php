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
 * @category filter
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

defined('MOODLE_INTERNAL') or die;

require_once("$CFG->libdir/formslib.php");

/*
 *
 */
abstract class dataformfilterform extends \moodleform {
    protected $_filter = null;

    /*
     *
     */
    public function __construct($filter, $action = null, $customdata = null, $method = 'post', $target = '', $attributes = null, $editable = true) {
        $this->_filter = $filter;

        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }

    /*
     *
     */
    public function definition_general() {

        $mform = &$this->_form;
        $filter = $this->_filter;
        $name = empty($filter->name) ? get_string('filternew', 'dataform') : $filter->name;
        $description = empty($filter->description) ? '' : $filter->description;
        $visible = !isset($filter->visible) ? 1 : $filter->visible;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Name.
        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', $name);

        // Description.
        $mform->addElement('textarea', 'description', get_string('description'));
        $mform->setType('description', PARAM_TEXT);
        $mform->setDefault('description', $description);

        // Visibility.
        $mform->addElement('selectyesno', 'visible', get_string('visible'));
        $mform->setDefault('visible', 1);

        // Entries per page.
        $mform->addElement('text', 'perpage', get_string('viewperpage', 'dataform'));
        $mform->setType('perpage', PARAM_INT);
        $mform->addRule('perpage', null, 'numeric', null, 'client');
        $mform->setDefault('perpage', $filter->perpage);

        // Selection method
        // $options = array(0 => get_string('filterbypage', 'dataform'), 1 => get_string('random', 'dataform'));
        // $mform->addElement('select', 'selection', get_string('filterselection', 'dataform'), $options);
        // $mform->setDefault('selection', $filter->selection);
        // $mform->disabledIf('selection', 'perpage', 'eq', '0');.

        // Group by
        // $mform->addElement('select', 'groupby', get_string('filtergroupby', 'dataform'), $fieldoptions);
        // $mform->setDefault('groupby', $filter->groupby);.

    }

    /*
     *
     */
    public function validation($data, $files) {
        if (!$errors = parent::validation($data, $files)) {

            // Per page cannot be negative.
            if (!empty($data['perpage'])) {
                if (is_numeric($data['perpage'])) {
                    $perpage = (int) $data['perpage'];
                } else {
                    $perpage = -1;
                }
                if ($perpage < 0) {
                    $errors['perpage'] = get_string('error:cannotbenegative', 'dataform');
                }
            }
        }

        return $errors;
    }

    /*
     *
     */
    protected function custom_sort_definition($customsort, $fields, $showlabel = false) {
        $mform = &$this->_form;
        $dataformid = $this->_filter->dataid;

        $mform->addElement('header', 'customsorthdr', get_string('filtercustomsort', 'dataform'));
        $mform->setExpanded('customsorthdr');

        \mod_dataform\helper\filterform::custom_sort_definition($mform, $dataformid, $customsort, $fields, $showlabel);
    }

    /*
     *
     */
    protected function custom_search_definition($customsearch, $fields, $showlabel = false) {
        $mform = &$this->_form;
        $filter = $this->_filter;
        $dataformid = $filter->dataid;

        $mform->addElement('header', 'customsearchhdr', get_string('filtercustomsearch', 'dataform'));
        $mform->setExpanded('customsearchhdr');

        // General search.
        $mform->addElement('text', 'search', get_string('search'));
        $mform->setType('search', PARAM_TEXT);
        $mform->setDefault('search', $filter->search);

        // Custom search.
        \mod_dataform\helper\filterform::custom_search_definition($mform, $dataformid, $customsearch, $fields, $showlabel);
    }

    /*
     *
     */
    protected function get_url_query($fields) {
        global $OUTPUT;

        $filter = $this->_filter;

        // Parse custom settings.
        $sorturlquery = '';
        $searchurlquery = '';

        if ($filter->customsort or $filter->customsearch) {
            // CUSTOM SORT.
            if ($filter->customsort) {
                if ($sortfields = unserialize($filter->customsort)) {
                    $sorturlarr = array();
                    foreach ($sortfields as $sortelement => $sortdir) {
                        list($fieldid, $element) = array_pad(explode(',', $sortelement), 2, null);
                        // Fix element dir if needed.
                        if (is_array($sortdir)) {
                            list($element, $sortdir) = $sortdir;
                        }

                        if (empty($fields[$fieldid])) {
                            continue;
                        }

                        // Sort url query.
                        $sorturlarr[] = "$sortelement,$sortdir";
                    }
                    if ($sorturlarr) {
                        $sorturlquery = '&usort='. urlencode(implode(',', $sorturlarr));
                    }
                }
            }

            // CUSTOM SEARCH.
            if ($filter->customsearch) {
                if ($searchfields = unserialize($filter->customsearch)) {
                    $searchurlarr = array();
                    foreach ($searchfields as $fieldid => $searchfield) {
                        if (empty($fields[$fieldid])) {
                            continue;
                        }
                        $fieldoptions = array();
                        if (!empty($searchfield['AND'])) {
                            $options = array();
                            foreach ($searchfield['AND'] as $option) {
                                if ($option) {
                                    $options[] = $fields[$fieldid]->format_search_value($option);
                                }
                            }
                            $fieldoptions[] = '<b>'. $fields[$fieldid]->name. '</b>:'. implode(' <b>and</b> ', $options);
                        }
                        if (!empty($searchfield['OR'])) {
                            $options = array();
                            foreach ($searchfield['OR'] as $option) {
                                if ($option) {
                                    $options[] = $fields[$fieldid]->format_search_value($option);
                                }
                            }
                            $fieldoptions[] = '<b>'. $fields[$fieldid]->name. '</b> '. implode(' <b>or</b> ', $options);
                        }
                        if ($fieldoptions) {
                            $searcharr[] = implode('<br />', $fieldoptions);
                        }
                    }
                    if (!empty($searcharr)) {
                        $searchurlquery = '&ucsearch='. \mod_dataform_filter_manager::get_search_url_query($searchfields);
                    }
                } else if ($filter->search) {
                    $searchurlquery = '&usearch='. urlendcode($filter->search);
                }
            }
        }

        return $sorturlquery. $searchurlquery;
    }
}
