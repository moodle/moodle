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
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\helper;

defined('MOODLE_INTERNAL') || die();

/**
 * Dataform rule form helper
 */
class filterform {

    /**
     *
     */
    public static function filter_selection_definition($mform, $dataformid, $prefix = null) {
        $options = array('' => get_string('choosedots'));
        $fm = \mod_dataform_filter_manager::instance($dataformid);
        if ($filters = $fm->get_filters(null, true, true)) {
            $options = $options + $filters;
        }
        $mform->addElement('select', $prefix. 'filterid', get_string('filter', 'dataform'), $options);
    }

    /**
     *
     */
    public static function get_field_sort_options_menu(array $fields) {
        $menu = array('' => array('' => get_string('field', 'dataform')));
        foreach ($fields as $fieldid => $field) {
            $fieldname = $field->name;
            if ($options = $field->get_sort_options_menu()) {
                $menu[$fieldname] = $options;
            }
        }
        return $menu;
    }

    /**
     *
     */
    public static function custom_sort_definition($mform, $dataformid, $customsort, array $fields = array(), $showlabel = false) {
        if (!$fields and $dataformid) {
            $fieldman = new \mod_dataform_field_manager($dataformid);
            $fields = $fieldman->get_fields();
        }

        $fieldoptions = self::get_field_sort_options_menu($fields);

        $diroptions = array(
            0 => get_string('ascending', 'dataform'),
            1 => get_string('descending', 'dataform')
        );

        $fieldlabel = get_string('filtersortfieldlabel', 'dataform');

        $sortcriteria = array();

        // Add current options.
        if ($customsort) {
            $sortfields = unserialize($customsort);
            foreach ($sortfields as $sortelement => $sortdir) {
                list($fieldid, $element) = array_pad(explode(',', $sortelement), 2, null);

                // Fix element dir if needed.
                if (is_array($sortdir)) {
                    list($element, $sortdir) = $sortdir;
                }

                if (!empty($fields[$fieldid])) {
                    $sortcriteria[] = array($fieldid, $element, $sortdir);
                }
            }
        }

        // Add 2 more.
        $sortcriteria[] = array(null, null, null);
        $sortcriteria[] = array(null, null, null);

        // Add form definitions for sort criteria.
        foreach ($sortcriteria as $i => $criterion) {
            list($fieldid, $element, $sortdir) = $criterion;

            $label = $showlabel ? "$fieldlabel$i" : '';

            $optionsarr = array();
            $optionsarr[] = &$mform->createElement('selectgroups', "sortfield$i", null, $fieldoptions);
            $optionsarr[] = &$mform->createElement('select', "sortdir$i", null, $diroptions);
            $mform->addGroup($optionsarr, "sortoptionarr$i", $label, ' ', false);

            $mform->setDefault("sortfield$i", "$fieldid,$element");
            $mform->setDefault("sortdir$i", $sortdir);

            $mform->disabledIf("sortdir$i", "sortfield$i", 'eq', '');

            if ($i) {
                $prev = $i - 1;
                $mform->disabledIf("sortoptionarr$i", "sortfield$prev", 'eq', '');
            }
            $i++;
        }
    }

    /**
     *
     */
    public static function reload_field_sort_options(&$mform, array $fields) {
        $menu = self::get_field_sort_options_menu($fields);
        $i = 0;
        while ($mform->elementExists("sortfield$i")) {
            $mform->getElement("sortfield$i")->removeOptions();
            $mform->getElement("sortfield$i")->loadArrayOptGroups($menu);
            $i++;
        }
    }

    /**
     *
     */
    public static function get_custom_sort_from_form($formdata) {
        $sortfields = array();
        $i = 0;
        while (isset($formdata->{"sortfield$i"})) {
            if ($sortelement = $formdata->{"sortfield$i"}) {
                $sortfields[$sortelement] = $formdata->{"sortdir$i"};
            }
            $i++;
        }

        if ($sortfields) {
            return $sortfields;
        }
        return null;
    }

    /**
     *
     */
    public static function get_field_search_options_menu(array $fields) {
        $menu = array('' => array('' => get_string('field', 'dataform')));
        foreach ($fields as $fieldid => $field) {
            $fieldname = $field->name;
            if ($options = $field->get_search_options_menu()) {
                $menu[$fieldname] = $options;
            }
        }
        return $menu;
    }

    /**
     *
     */
    public static function custom_search_definition($mform, $dataformid, $customsearch, array $fields = array(), $showlabel = false) {
        if (!$fields and $dataformid) {
            $fieldman = new \mod_dataform_field_manager($dataformid);
            $fields = $fieldman->get_fields();
        }

        $andoroptions = array(
            '' => get_string('andor', 'dataform'),
            'AND' => get_string('and', 'dataform'),
            'OR' => get_string('or', 'dataform'),
        );

        $fieldoptions = self::get_field_search_options_menu($fields);

        $isnotoptions = array(
            '' => get_string('is', 'dataform'),
            'NOT' => get_string('not', 'dataform'),
        );
        $operatoroptions = array(
            '' => get_string('empty', 'dataform'),
            '=' => get_string('equal', 'dataform'),
            '>' => get_string('greaterthan', 'dataform'),
            '<' => get_string('lessthan', 'dataform'),
            '>=' => get_string('greaterorequal', 'dataform'),
            '<=' => get_string('lessorequal', 'dataform'),
            'BETWEEN' => get_string('between', 'dataform'),
            'LIKE' => get_string('contains', 'dataform'),
            'IN' => get_string('in', 'dataform'),
        );

        // Add current options.
        $searchcriteria = array();
        if ($customsearch) {
            if (!is_array($customsearch)) {
                $searchfields = unserialize($customsearch);
            } else {
                $searchfields = $customsearch;
            }

            foreach ($searchfields as $fieldid => $searchfield) {
                if (empty($fields[$fieldid])) {
                    continue;
                }

                foreach ($searchfield as $andor => $searchoptions) {
                    foreach ($searchoptions as $searchoption) {
                        if (!is_array($searchoption) or count($searchoption) != 4) {
                            continue;
                        }
                        list($element, $not, $operator, $value) = $searchoption;
                        $searchcriteria[] = array("$fieldid,$element", $andor, $not, $operator, $value);
                    }
                }
            }
        }

        // Add 2 more empty options.
        $searchcriteria[] = array(null, null, null, null, null);
        $searchcriteria[] = array(null, null, null, null, null);

        // Add form definition for each existing criterion.
        $fieldlabel = get_string('filtersearchfieldlabel', 'dataform');

        foreach ($searchcriteria as $i => $searchcriterion) {
            if (count($searchcriterion) != 5) {
                continue;
            }

            $label = $showlabel ? "$fieldlabel$i" : '';

            list($fieldid, $andor, $not, $operator, $value) = $searchcriterion;

            $arr = array();
            $arr[] = &$mform->createElement('select', "searchandor$i", null, $andoroptions);
            $arr[] = &$mform->createElement('selectgroups', "searchfield$i", null, $fieldoptions);
            $arr[] = &$mform->createElement('select', "searchnot$i", null, $isnotoptions);
            $arr[] = &$mform->createElement('select', "searchoperator$i", '', $operatoroptions);
            $arr[] = &$mform->createElement('text', "searchvalue$i", '');
            $mform->addGroup($arr, "customsearcharr$i", $label, ' ', false);

            $mform->setType("searchvalue$i", PARAM_TEXT);

            $mform->setDefault("searchandor$i", $andor);
            $mform->setDefault("searchfield$i", $fieldid);
            $mform->setDefault("searchnot$i", $not);
            $mform->setDefault("searchoperator$i", $operator);
            $mform->setDefault("searchvalue$i", $value);

            $mform->disabledIf("searchfield$i", "searchandor$i", 'eq', '');
            $mform->disabledIf("searchnot$i", "searchfield$i", 'eq', '');
            $mform->disabledIf("searchoperator$i", "searchfield$i", 'eq', '');
            $mform->disabledIf("searchvalue$i", "searchoperator$i", 'eq', '');

            if ($i) {
                $prev = $i - 1;
                $mform->disabledIf("customsearcharr$i", "searchfield$prev", 'eq', '');
            }

            $i++;
        }
    }

    /**
     *
     */
    public static function reload_field_search_options(&$mform, array $fields) {
        $menu = self::get_field_search_options_menu($fields);
        $i = 0;
        while ($mform->elementExists("searchfield$i")) {
            $mform->getElement("searchfield$i")->removeOptions();
            $mform->getElement("searchfield$i")->loadArrayOptGroups($menu);
            $i++;
        }
    }

    /**
     *
     */
    public static function get_custom_search_from_form($formdata, $dataformid) {
        $df = \mod_dataform_dataform::instance($dataformid);
        if ($fields = $df->field_manager->get_fields()) {
            $searchfields = array();
            foreach ($formdata as $var => $unused) {
                if (strpos($var, 'searchandor') !== 0) {
                    continue;
                }

                $i = (int) str_replace('searchandor', '', $var);
                // Check if trying to define a search criterion.
                if ($searchandor = $formdata->{"searchandor$i"}) {
                    if ($searchelement = $formdata->{"searchfield$i"}) {
                        list($fieldid, $element) = explode(',', $searchelement);
                        $not = !empty($formdata->{"searchnot$i"}) ? $formdata->{"searchnot$i"} : '';
                        $operator = isset($formdata->{"searchoperator$i"}) ? $formdata->{"searchoperator$i"} : '';
                        $value = isset($formdata->{"searchvalue$i"}) ? $formdata->{"searchvalue$i"} : '';

                        // Don't add empty criteria on cleanup (unless operator is Empty and thus doesn't need search value).
                        if ($operator and !$value) {
                            continue;
                        }

                        // Aggregate by fieldid and searchandor,.
                        if (!isset($searchfields[$fieldid])) {
                            $searchfields[$fieldid] = array();
                        }
                        if (!isset($searchfields[$fieldid][$searchandor])) {
                            $searchfields[$fieldid][$searchandor] = array();
                        }
                        $searchfields[$fieldid][$searchandor][] = array($element, $not, $operator, $value);
                    }
                }
            }
            if ($searchfields) {
                return $searchfields;
            }
        }
        return null;
    }

}
