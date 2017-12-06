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

/**
 * Filter class
 */
class dataformfilter {

    protected $_instance;
    protected $_attributes;

    protected $_filteredtables = null;
    protected $_searchfields = null;
    protected $_contentfields = null;
    protected $_sortfields = null;
    protected $_joins = null;
    protected $_entriesexcluded = array();

    /**
     * constructor
     */
    public function __construct($data) {
        // Instance.
        $this->_instance = new \stdClass;
        $this->_instance->id = empty($data->id) ? 0 : $data->id;
        $this->_instance->dataid = $data->dataid;
        $this->_instance->name = empty($data->name) ? '' : $data->name;
        $this->_instance->description = empty($data->description) ? '' : $data->description;
        $this->_instance->visible = !isset($data->visible) ? 1 : $data->visible;

        $this->_instance->perpage = empty($data->perpage) ? 0 : $data->perpage;
        $this->_instance->selection = empty($data->selection) ? 0 : $data->selection;
        $this->_instance->groupby = empty($data->groupby) ? '' : $data->groupby;
        $this->_instance->customsort = empty($data->customsort) ? '' : $data->customsort;
        $this->_instance->customsearch = empty($data->customsearch) ? '' : $data->customsearch;
        $this->_instance->search = empty($data->search) ? '' : $data->search;

        // Other attributes.
        $this->_attributes = new \stdClass;
        $this->_attributes->eids = empty($data->eids) ? '' : $data->eids;
        $this->_attributes->users = empty($data->users) ? '' : $data->users;
        $this->_attributes->groups = empty($data->groups) ? '' : $data->groups;
        $this->_attributes->states = empty($data->states) ? '' : $data->states;
        $this->_attributes->page = empty($data->page) ? 0 : $data->page;
        $this->_attributes->pagenum = empty($data->pagenum) ? 0 : $data->pagenum;
        $this->_attributes->contentfields = empty($data->contentfields) ? '' : $data->contentfields;

        // Dataform overrides.
        $df = \mod_dataform_dataform::instance($data->dataid);
        $this->_attributes->grouped = isset($data->grouped) ? $data->grouped : $df->grouped;
        $this->_attributes->individualized = isset($data->individualized) ? $data->individualized : $df->individualized;
        $this->_attributes->groupmode = isset($data->groupmode) ? $data->groupmode : $df->groupmode;
        $this->_attributes->currentgroup = isset($data->currentgroup) ? $data->currentgroup : $df->currentgroup;
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            // Call set method.
            $this->{'set_'.$key}($value);
        } else if (isset($this->_attributes->$key)) {
            // Set non-instance attributes.
            $this->_attributes->$key = $value;
        } else if (isset($this->_instance->$key)) {
            // Set instance attribute.
            $this->_instance->$key = $value;
        }
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        // Call get method.
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        // Return attributes such as eids, users, page, contentfields, pagenum.
        if (isset($this->_attributes->$key)) {
            return $this->_attributes->$key;
        }
        // Return instance attributes.
        if (isset($this->_instance->{$key})) {
            return $this->_instance->{$key};
        }
        return null;
    }

    /**
     * Insert/update filter data in DB.
     */
    public function update() {
        global $DB;

        $df = \mod_dataform_dataform::instance($this->dataid);

        if ($this->id) {
            $DB->update_record('dataform_filters', $this->instance);

            // Trigger event.
            $params = array(
                'objectid' => $this->id,
                'context' => $df->context,
                'other' => array(
                    'filtername' => $this->name,
                    'dataid' => $this->dataid
                )
            );
            $event = \mod_dataform\event\filter_updated::create($params);
            $event->add_record_snapshot('dataform_filters', $this->instance);
            $event->trigger();

        } else {
            $this->id = $DB->insert_record('dataform_filters', $this->instance);

            // Trigger event.
            $params = array(
                'objectid' => $this->id,
                'context' => $df->context,
                'other' => array(
                    'filtername' => $this->name,
                    'dataid' => $this->dataid
                )
            );
            $event = \mod_dataform\event\filter_created::create($params);
            $event->add_record_snapshot('dataform_filters', $this->instance);
            $event->trigger();
        }
    }

    /**
     * Delete filter from DB.
     */
    public function delete() {
        global $DB;

        $df = \mod_dataform_dataform::instance($this->dataid);

        $DB->delete_records('dataform_filters', array('id' => $this->id));

        // Trigger event.
        $params = array(
            'objectid' => $this->id,
            'context' => $df->context,
            'other' => array(
                'filtername' => $this->name,
                'dataid' => $this->dataid
            )
        );
        $event = \mod_dataform\event\filter_deleted::create($params);
        $event->add_record_snapshot('dataform_filters', $this->instance);
        $event->trigger();
    }

    /**
     * Return a deep clone of this filter.
     *
     * @return dataformfilter
     */
    public function get_clone() {
        return unserialize(serialize($this));
    }

    /**
     *
     */
    public function get_instance() {
        return $this->_instance;
    }

    /**
     *
     */
    public function get_custom_search_fields() {
        if (!$customsearch = $this->customsearch) {
            return array();
        }
        return unserialize($customsearch);
    }

    /**
     *
     */
    public function get_custom_sort_fields() {
        if (!$customsort = $this->customsort) {
            return array();
        }
        return unserialize($customsort);
    }

    /**
     *
     */
    public function get_sql() {
        $this->init_filter_sql();

        // Get all fields (see CONTRIB-5225).
        $df = \mod_dataform_dataform::instance($this->dataid);
        $fields = $df->field_manager->get_fields(array('forceget' => true));
        // Get content fields.
        $fieldkeys = $this->contentfields ? array_fill_keys($this->contentfields, null) : null;
        $contentfields = $fieldkeys ? array_intersect_key($fields, $fieldkeys) : array();

        // SEARCH sql.
        $searchsql = $this->get_search_sql($fields);
        list($searchtables, $searchwhere, $searchparams) = $searchsql;
        // SORT sql.
        $sortsql = $this->get_sort_sql($fields);
        list($sorttables, $sortwhere, $sortorder, $sortparams) = $sortsql;
        // CONTENT sql ($dataformcontent is an array of fieldid whose content needs to be fetched).
        $contentsql = $this->get_content_sql($contentfields);
        list($contentwhat, $contenttables, $contentwhere, $contentparams, $dataformcontent) = $contentsql;
        // JOIN sql (does't use params).
        list($joinwhat, $jointables, ) = $this->get_join_sql($fields);

        return array(
            $searchtables,
            $searchwhere,
            $searchparams,
            $sorttables,
            $sortwhere,
            $sortorder,
            $sortparams,
            $contentwhat,
            $contenttables,
            $contentwhere,
            $contentparams,
            $dataformcontent,
            $joinwhat,
            $jointables,
        );
    }

    /**
     *
     */
    public function init_filter_sql() {
        $eaufieldid = \dataformfield_entryauthor_entryauthor::INTERNALID;

        $this->_filteredtables = array($eaufieldid);
        $this->_searchfields = $this->get_custom_search_fields();
        $this->_sortfields = $this->get_custom_sort_fields();
        $this->_joins = array();
    }

    /**
     *
     */
    public function get_search_sql($fields) {
        global $DB;

        $searchfrom = array();
        $searchwhere = array();
        $searchparams = array(); // Named params array.

        $searchfields = $this->_searchfields;
        $simplesearch = $this->search;
        $searchtables = '';

        $whereand = array();
        $whereor = array();

        if ($searchfields) {
            foreach ($searchfields as $fieldid => $searchfield) {
                // If we got this far there must be some actual search values.
                if (empty($fields[$fieldid])) {
                    continue;
                }

                $field = $fields[$fieldid];
                $internalfield = ($field instanceof \mod_dataform\pluginbase\dataformfield_internal);

                // Register join field if applicable.
                $this->register_join_field($field);

                // Add AND search clauses.
                if (!empty($searchfield['AND'])) {
                    foreach ($searchfield['AND'] as $option) {
                        if ($fieldsqloptions = $field->get_search_sql($option)) {
                            list($fieldsql, $fieldparams, $fromcontent) = $fieldsqloptions;
                            $whereand[] = $fieldsql;
                            $searchparams = array_merge($searchparams, $fieldparams);
                            if ($fromcontent) {
                                $searchfrom[$fieldid] = $fieldid;
                            }
                        }
                    }
                }

                // Add OR search clause.
                if (!empty($searchfield['OR'])) {
                    foreach ($searchfield['OR'] as $option) {
                        if ($fieldsqloptions = $field->get_search_sql($option)) {
                            list($fieldsql, $fieldparams, $fromcontent) = $fieldsqloptions;
                            $whereor[] = $fieldsql;
                            $searchparams = array_merge($searchparams, $fieldparams);
                            if ($fromcontent) {
                                $searchfrom[$fieldid] = $fieldid;
                            }
                        }
                    }
                }

            }
        }

        if ($simplesearch) {
            $entryids = array();

            foreach ($fields as $fieldid => $field) {
                // If no search options then no simple search either.
                if (!$field->search_options_menu) {
                    continue;
                }
                foreach ($field->simple_search_elements as $element) {
                    $searchoption = array($element, null, 'LIKE', $simplesearch);
                    if ($fieldsqloptions = $field->get_search_sql($searchoption)) {
                        list($fieldsql, $fieldparams) = $fieldsqloptions;
                        if ($fieldsql) {
                            if ($fieldentryids = $field->get_entry_ids_for_content($fieldsql, $fieldparams)) {
                                $entryids = array_merge($entryids, $fieldentryids);
                            }
                        }
                    }
                }
            }

            if ($entryids) {
                $entryids = array_unique($entryids);
            } else {
                $entryids = array(-999);
            }

            list($ineids, $eidsparams) = $DB->get_in_or_equal($entryids);
            $whereand[] = " e.id $ineids ";
            $searchparams = array_merge($searchparams, $eidsparams);
        }

        // Compile sql for search settings.
        if ($searchfrom) {
            foreach ($searchfrom as $fieldid) {
                // Add only tables which are not already added.
                if (empty($this->_filteredtables) or !in_array($fieldid, $this->_filteredtables)) {
                    $this->_filteredtables[] = $fieldid;
                    $searchtables .= $fields[$fieldid]->get_search_from_sql();
                }
            }
        }

        if ($whereand) {
            $searchwhere[] = implode(' AND ', $whereand);
        }
        if ($whereor) {
            $searchwhere[] = '('. implode(' OR ', $whereor). ')';
        }

        $wheresearch = $searchwhere ? ' AND '. implode(' AND ', $searchwhere) : '';

        // Register referred tables.
        $this->_filteredtables = $searchfrom;
        $searchparams = array_values($searchparams);

        return array($searchtables, $wheresearch, $searchparams);
    }

    /**
     *
     */
    public function get_sort_sql($fields) {
        $sorties = array();
        $orderby = array("e.id ASC");
        $params = array();

        $sortfields = $this->_sortfields;

        if ($sortfields) {
            $orderby = array();
            foreach ($sortfields as $sortelement => $sortdir) {
                list($fieldid, $element) = array_pad(explode(',', $sortelement), 2, null);

                // Fix element dir if needed.
                if (is_array($sortdir)) {
                    list($element, $sortdir) = $sortdir;
                }

                if (!$fieldid) {
                    continue;
                }

                if (empty($fields[$fieldid])) {
                    continue;
                }

                $field = $fields[$fieldid];

                $sortname = $field->get_sort_sql($element);
                // Add non-internal fields to sorties.
                if (!($field instanceof \mod_dataform\pluginbase\dataformfield_internal)) {
                    $sorties[$fieldid] = $sortname;
                }
                $orderby[] = "$sortname ". ($sortdir ? 'DESC' : 'ASC');

                // Register join field if applicable.
                $this->register_join_field($field);
            }
        }

        // Compile sql for sort settings.
        $sorttables = '';
        $wheresort = '';
        $sortorder = '';

        if ($orderby) {
            $sortorder = ' ORDER BY '. implode(', ', $orderby). ' ';
            if ($sorties) {
                $sortfrom = array_keys($sorties);
                foreach ($sortfrom as $fieldid) {
                    // Add only tables which are not already added.
                    if (empty($this->_filteredtables) or !in_array($fieldid, $this->_filteredtables)) {
                        $this->_filteredtables[] = $fieldid;
                        list($fromsql, ) = $fields[$fieldid]->get_sort_from_sql();
                        $sorttables .= $fromsql;
                    }
                }
            }
        }

        return array($sorttables, $wheresort, $sortorder, $params);
    }

    /**
     *
     */
    public function get_content_sql($fields) {

        $dataformcontent = array(); // List of field ids whose content should be fetched separately.
        $whatcontent = ' '; // List of field ids whose content should be fetched in the main query.
        $contenttables = ' '; // List of content tables to include in the main query.
        $wherecontent = '';
        $params = array();

        if (!$contentfields = $this->contentfields) {
            return array($whatcontent, $contenttables, $wherecontent, $params, $dataformcontent);
        }

        $whatcontent = array();
        $contentfrom = array();

        foreach ($contentfields as $fieldid) {
            // Skip non-selectable fields.
            // (some of the internal fields e.g. _user which are included in the select clause by default).
            if (!isset($fields[$fieldid]) or !$selectsql = $fields[$fieldid]->get_select_sql()) {
                continue;
            }

            $field = $fields[$fieldid];

            // Register join field if applicable.
            if ($this->register_join_field($field)) {
                // Processing is done separately.
                continue;
            }

            if ($field->is_dataform_content()) {
                $dataformcontent[] = $fieldid;
            } else {
                $whatcontent[] = $selectsql;
                if ($sortformsql = $field->get_sort_from_sql()) {
                    // Add only tables which are not already added.
                    if (empty($this->_filteredtables) or !in_array($fieldid, $this->_filteredtables)) {
                        list($contentfromfieldid, $fieldparam) = $sortformsql;
                        if ($contentfromfieldid) {
                            $contentfrom[$fieldid] = $contentfromfieldid;
                        }
                        if ($fieldparam !== null) {
                            $params[] = $fieldparam;
                        }
                    }
                }
            }
        }
        $whatcontent = !empty($whatcontent) ? ', '. implode(', ', $whatcontent) : ' ';
        $contenttables = ' '. implode(' ', $contentfrom);
        if ($params) {
            $params = array_map(
                function($fieldid) {
                    return " c$fieldid.fieldid = ? ";
                },
                $params
            );
            $wherecontent = ' AND '. implode(' AND ', $params);
        }
        return array($whatcontent, $contenttables, $wherecontent, $params, $dataformcontent);
    }

    /**
     *
     */
    public function get_join_sql($fields) {

        // List of field ids whose content should be fetched in the main query.
        $whatjoin = ' ';
        // List of content tables to include in the main query.
        $jointables = ' ';

        $params = array();

        // Joins should have been registerec in get_content_sql.
        if (!$this->_joins) {
            return array($whatjoin, $jointables, $params);
        }

        $whatjoin = array();
        $joinfrom = array();

        // Process join fields.
        foreach ($this->_joins as $fieldid) {
            if (empty($fields[$fieldid])) {
                continue;
            }
            $field = $fields[$fieldid];
            $whatjoin[] = $field->get_select_sql();
            list($sqlfrom, $fieldparams) = $field->get_join_sql();
            $joinfrom[$fieldid] = $sqlfrom;
            $params = array_merge($params, $fieldparams);
        }

        $whatjoin = !empty($whatjoin) ? ', '. implode(', ', $whatjoin) : ' ';
        $jointables = ' '. implode(' ', $joinfrom);

        return array($whatjoin, $jointables, $params);
    }

    /**
     * @return bool True if the field is registered, false otherwise
     */
    public function register_join_field($field) {
        if ($field->is_joined()) {
            $fieldid = $field->id;
            $this->_joins[$fieldid] = $fieldid;
            return true;
        }
        return false;
    }

    /**
     * Appends one or more filters.
     *
     * @param array $filters List of dataformfilter objects to append.
     * @return void
     */
    public function append(array $filters) {
        foreach ($filters as $filter) {
            if (!$filter) {
                continue;
            }

            $this->id = $filter->id;

            // Per page - append smaller.
            if ($newperpage = $filter->perpage) {
                if (!$perpage = $this->perpage or $newperpage < $perpage) {
                    $this->perpage = $newperpage;

                    // Set page and page num.
                    $this->page = $filter->page;
                    $this->pagenum = $filter->pagenum;
                }
            }

            // Custom sort.
            if ($newcustomsort = $filter->customsort) {
                if (!$this->customsort) {
                    $this->customsort = $newcustomsort;
                } else {
                    $customsort = unserialize($newcustomsort);
                    $this->append_sort_options($customsort);
                }
            }

            // Custom search.
            if ($newcustomsearch = $filter->customsearch) {
                if (!$this->customsearch) {
                    $this->customsearch = $newcustomsearch;
                } else {
                    $customsearch = unserialize($newcustomsearch);
                    $this->append_search_options($customsearch);
                }
            }

            // Search.
            if ($filter->search and !$this->search) {
                $this->search = $filter->search;
            }

            // Set specific entries.
            if ($eids = $filter->eids) {
                $this->eids = $this->get_unique_list($this->eids, $eids);
            }
            // Set specific users.
            if ($users = $filter->users) {
                $this->users = $this->get_unique_list($this->users, $users);
            }
            // Set specific groups.
            if ($groups = $filter->groups) {
                $this->groups = $this->get_unique_list($this->groups, $groups);
            }
            // Set specific states.
            if ($states = $filter->states) {
                $this->states = $this->get_unique_list($this->states, $states);
            }
        }
    }

    /**
     *
     */
    public function append_sort_options(array $sorties) {
        if ($sorties) {
            $sortoptions = $this->get_custom_sort_fields();
            foreach ($sorties as $fieldid => $sortdir) {
                $sortoptions[$fieldid] = $sortdir;
            }
            $this->customsort = serialize($sortoptions);
        }
    }

    /**
     *
     */
    public function prepend_sort_options(array $sorties) {
        if ($sorties) {
            $sortoptions = $this->get_custom_sort_fields();
            foreach ($sorties as $fieldid => $sortdir) {
                if (array_key_exists($fieldid)) {
                    $sortoptions[$fieldid] = $sortdir;
                    unset($sorties[$fieldid]);
                }
            }
            // Prepend remaining sorties.
            if ($sorties) {
                $sortoptions = $sortoptions + $sorties;
            }
            $this->customsort = serialize($sortoptions);
        }
    }

    /**
     * Appends search options to the filter.
     *
     * @param array $searchies (fieldid => (endor => (element, not, operator, value))).
     * @return void
     */
    public function append_search_options($searchies) {
        if (!$searchies) {
            return;
        }

        if (is_array($searchies)) {
            // Custom search expects an array.
            $searchoptions = $this->get_custom_search_fields();
            foreach ($searchies as $fieldid => $searchy) {
                if (empty($searchoptions[$fieldid])) {
                    $searchoptions[$fieldid] = $searchies[$fieldid];
                } else {
                    $searchoptions[$fieldid] = array_merge_recursive(
                        $searchoptions[$fieldid],
                        $searchies[$fieldid]
                    );
                }
            }
            $this->customsearch = serialize($searchoptions);
        } else {
            // Quick search expects a string.
            $this->search = $searchies;
        }
    }


    /**
     * Generates a unique list from the specified items. The items can be either array
     * lists or comma separated lists.
     *
     * @param string|array $list1
     * @param string|array $list2
     * @param int $sort Sort flag of array_unique; defaults to SORT_NUMERIC.
     * @return array
     */
    private function get_unique_list($items1, $items2, $sort = SORT_NUMERIC) {
        $list1 = is_array($items1) ? $items1 : explode(',', $items1);
        $list2 = is_array($items2) ? $items2 : explode(',', $items2);
        return array_values(array_unique(array_merge($list1, $list2), $sort));
    }

}
