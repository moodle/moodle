<?php //$Id$

require_once($CFG->libdir.'/formslib.php');
//require_once($CFG->libdir . '/form/submit.php'); // (abautu) is it necesary? i don't think so

class user_filter_form extends moodleform {
    /**
     * array of filter type objects 
     */
    var $_filtersTypes = array();
    
    // form definition
    function definition() {
        global $SESSION;
        // add controls for each filter type in the new filter group
        $mform =& $this->_form;
        $this->_filtersTypes =& $this->_customdata;
        $mform->addElement('header', 'newfilter', get_string('newfilter','filters'));
        foreach($this->_filtersTypes as $ft) {
            $ft->setupForm($mform);
        }
        $objs = array();
        $objs[] = &$mform->createElement('submit', 'add ', get_string('addfilter','filters'));
        $objs[] = &$mform->createElement('submit', 'set', get_string('setfilter','filters'));
        $mform->addElement('group', 'addfilterbar', '', $objs, ' ', false);

        // add controls for each active filter in the active filters group
        $mform->addElement('header', 'actfilterhdr', get_string('actfilterhdr','filters'));
        $objs = array();
        $objs[] = &$mform->createElement('cancel', 'remove', get_string('removeselected','filters'));
        $objs[] = &$mform->createElement('cancel', 'cancel', get_string('removeall','filters'));
        $mform->addElement('group', 'actfiltergrp', '', $objs, ' ', false);
        // insert the controls before the buttons
        if(!empty($SESSION->user_filter_descriptions)) {
            foreach($SESSION->user_filter_descriptions as $k => $f) {
                $obj = &$mform->createElement('checkbox', 'filter['.$k.']', null, $f);
                $mform->insertElementBefore($obj, 'actfiltergrp');
            }
        }
    }

    /**
     * Removes an active filter from the form and filters list.
     * @param int $key id of filter to remove
     */
    function _removeFilter($key) {
        global $SESSION;
        unset($SESSION->user_filter_clauses[$key]);
        unset($SESSION->user_filter_descriptions[$key]);
        $this->_form->removeElement('filter['.$key.']');
    }
    
    /**
     * Removes all active filters from the form and filters list.
     */
    function _removeFilters() {
        global $SESSION;
        if(!empty($SESSION->user_filter_clauses)) {
            foreach($SESSION->user_filter_clauses as $key=>$f) {
                $this->_removeFilter($key);
            }
        }
    }
    
    /**
     * Stores an active filter to the form and filters list.
     * @param int $key id of filter to remove
     * @param string $description human friendly description of the filter
     * @param string $clauses SQL where condition 
     */
    function _insertFilter($key, $description, $clause) {
        global $SESSION;
        $SESSION->user_filter_clauses[$key] = $clause;
        $SESSION->user_filter_descriptions[$key] = $description;
        $mform =& $this->_form;
        $obj = &$mform->createElement('checkbox', 'filter['.$key.']', null, $description);
        $mform->insertElementBefore($obj, 'actfiltergrp');
    }
    
    /**
     * Updates form and filters list based on data received
     * @param object $data object with data received by the form
     */
    function _updateFilters($data) {
        global $SESSION;
        // if the forms was not submited, then quit
        if(is_null($data)){
            return;
        }
        // if cancel was pressed, then remove all filters
        if(!empty($data->cancel)) {
            $this->_removeFilters();
            return;
        }

        // if remove was pressed, then remove selected filters
        if(!empty($data->remove)) {
            if(!empty($data->filter)) {
                foreach($data->filter as $k=>$f) {
                    $this->_removeFilter($k);
                }
            }
            return;
        }

        // if set was pressed, then remove all filters before adding new ones
        if(!empty($data->set)) {
            $this->_removeFilters();
        }
        
        // in any other case, add the selected filter
        // first build the filter out of each active filter type
        $clauses = array();
        $descriptions = array();
        foreach($this->_filtersTypes as $ft) {
            $ft->checkData($data);
            $sqlFilter = $ft->getSQLFilter();
            // ignore disabled filters
            if(!empty($sqlFilter)) {
                $clauses[] = $sqlFilter;
                $descriptions[] = $ft->getDescription();
            }
        }
        // if no filters are active, then quit
        if(empty($clauses)) {
            return;
        }
        
        // join the filter parts and their descriptions together
        $clauses = implode(' AND ', $clauses);
        $descriptions = implode(', ', $descriptions);

        // check if this filter is a duplicate; if so, then quit
        $lastkey = -1;
        if(!empty($SESSION->user_filter_descriptions)) {
            foreach($SESSION->user_filter_descriptions as $k=>$c) {
                if($c == $descriptions) {
                    return;
                }
                $lastkey = $k;
            }
        }
        // append the new filter
        $this->_insertFilter($lastkey + 1, $descriptions, $clauses);
    }

    function definition_after_data() {
        global $SESSION;
        $mform =& $this->_form;
        // update the filters
        $this->_updateFilters($this->get_data());
        // remove the active filters section if no filters are defined
        if(empty($SESSION->user_filter_descriptions)) {
            $mform->removeElement('actfiltergrp');
            $mform->removeElement('actfilterhdr');
        }
    }
    
    /**
     * Returns the complete SQL where condition coresponding to the active filters and the extra conditions
     * @param mixed $extra array of SQL where conditions to be conected by ANDs or a string SQL where condition, which will be connected to the active filters conditions by AND
     * @return string SQL where condition
     */
    function getSQLFilter($extra='') {
        global $SESSION;
        if(is_array($extra)) {
            $extra = implode(' AND ', $extra);
        }
        // join sql filters with ORs and put inside paranteses
        if(!empty($SESSION->user_filter_clauses)) {
            if(!empty($extra)) {
                $extra .= ' AND ';
            }
            $extra .= '((' . implode(') OR (',$SESSION->user_filter_clauses) . '))';
        }
        return $extra;
    }
}
