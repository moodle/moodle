<?php //$Id$

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir . '/datalib.php');

class user_bulk_form extends moodleform {
    /** 
     * Quickform select object for the available users.
     */
    var $ausers;
    /** 
     * Quickform select object for the selected users.
     */
    var $susers;
    // form definition
    function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'users', get_string('usersinlist', 'bulkusers'));

        $this->ausers =& $mform->createElement('select', 'ausers', get_string('available', 'bulkusers'), null, 'size="15"');
        $this->ausers->setMultiple(true);
        $this->susers =& $mform->createElement('select', 'susers', get_string('selected', 'bulkusers'), null, 'size="15"');
        $this->susers->setMultiple(true);

        $objs = array();
        $objs[] = &$this->ausers;
        $objs[] = &$this->susers;

        $grp =& $mform->addElement('group', 'usersgrp', get_string('users'), $objs, ' ', false);
        $grp->setHelpButton(array('lists','','bulkusers')); 

        $mform->addElement('static', 'comment');

        $objs = array();
        $objs[] =& $mform->createElement('submit', 'addone', get_string('addsel', 'bulkusers'));
        $objs[] =& $mform->createElement('submit', 'addall', get_string('addall', 'bulkusers'));
        $objs[] =& $mform->createElement('submit', 'removesel', get_string('removesel', 'bulkusers'));
        $objs[] =& $mform->createElement('submit', 'removeall', get_string('removeall', 'bulkusers'));
        $objs[] =& $mform->createElement('submit', 'deletesel', get_string('deletesel', 'bulkusers'));
        $objs[] =& $mform->createElement('submit', 'deleteall', get_string('deleteall', 'bulkusers'));
        $grp =& $mform->addElement('group', 'buttonsgrp', get_string('selectedlist', 'bulkusers'), $objs, array(' ', '<br />'), false);
        $grp->setHelpButton(array('selectedlist','','bulkusers')); 
        
        $objs = array();
        $objs[] =& $mform->createElement('select', 'action', get_string('withselected'), @$this->_customdata);
        $objs[] =& $mform->createElement('submit', 'doaction', get_string('go'));;
        $mform->addElement('group', 'actionsgrp', get_string('withselectedusers'), $objs, ' ', false);

        $renderer =& $mform->defaultRenderer();
        $template = '<label class="qflabel" style="vertical-align:top">{label}</label> {element}';
        $renderer->setGroupElementTemplate($template, 'usersgrp');
    }

    function setUserCount($count=-1, $comment=null) {
        global $SESSION;
        if($count < 0) {
            $count = count($SESSION->bulk_ausers);
        }
        $obj =& $this->_form->getElement('comment');
        $obj->setLabel($comment);
        $obj->setText(get_string('usersfound', 'bulkusers', $count) . ' ' .get_string('usersselected', 'bulkusers', count(@$SESSION->bulk_susers)));
    }
    
    function definition_after_data() {
        global $SESSION;
        $this->_updateSelection($this->get_data());
        $this->setSelectedUsers($SESSION->bulk_susers);
        if(empty($SESSION->bulk_susers)) {
            $this->_form->removeElement('actionsgrp');
        }
        //$this->setUserCount();
    }

    /**
     * Updates the user selection based on the data submited to the form.
     * @param object $data object with data received by the form
     */
    function _updateSelection($data) {
        global $SESSION;
        if(!is_array(@$SESSION->bulk_susers)) {
            $SESSION->bulk_susers = array();
        }
        // if the forms was not submited, then quit
        if(is_null($data)){
            return;
        }
        if(@$data->addall) {
            if(!empty($SESSION->bulk_ausers)) {
                $SESSION->bulk_susers = array_merge($SESSION->bulk_susers, $SESSION->bulk_ausers);
            }
        } else if(@$data->addone) {
            if(!empty($data->ausers)) {
                $SESSION->bulk_susers = array_merge($SESSION->bulk_susers, array_values($data->ausers));
            }
        } else if(@$data->removeall) {
            if(!empty($SESSION->bulk_ausers)) {
                $SESSION->bulk_susers = array_diff($SESSION->bulk_susers, $SESSION->bulk_ausers);
            }
        } else if(@$data->removesel) {
            if(!empty($data->ausers)) {
                $SESSION->bulk_susers = array_diff($SESSION->bulk_susers, array_values($data->ausers));
            }
        } else if(@$data->deletesel) {
            if(!empty($data->susers)) {
                $SESSION->bulk_susers = array_diff($SESSION->bulk_susers, array_values($data->susers));
            }
        } else if(@$data->deleteall) {
            $SESSION->bulk_susers = array();
        }
        $SESSION->bulk_susers = array_unique($SESSION->bulk_susers);
    }
    
    /**
     * Sets the available users list, based on their ids
     * @param array $ausers array of user ids
     */
    function setAvailableUsers($ausers) {
        $sqlwhere = null;
        if(!empty($ausers)) {
            $sqlwhere = 'id IN (' . implode(',', $ausers) . ')';
        }
        $this->setAvailableUsersSQL($sqlwhere);
    }
    
    /**
     * Sets the available users list, based on a SQL where condition
     * @param string $sqlwhere filter for the users
     */
    function setAvailableUsersSQL($sqlwhere=null) {
        global $SESSION;
        if(is_null($sqlwhere) || ($users =& $this->getUserData($sqlwhere))===false) {
            $users = array();
        }
        $SESSION->bulk_ausers =& array_keys($users);
        $this->ausers->load($users);
    }
    
    /**
     * Sets the selected users list, based on their ids
     * @param array $ausers array of user ids
     */
    function setSelectedUsers($susers) {
        $sqlwhere = null;
        if(!empty($susers)) {
            $sqlwhere = 'id IN (' . implode(',', $susers) . ')';
        }
        $this->setSelectedUsersSQL($sqlwhere);
    }
    
    /**
     * Sets the selected users list, based on a SQL where condition
     * @param string $sqlwhere filter for the users
     */
    function setSelectedUsersSQL($sqlwhere=null) {
        global $SESSION;
        if(is_null($sqlwhere) || ($users =& $this->getUserData($sqlwhere))===false) {
            $users = array();
        }
        $SESSION->bulk_susers =& array_keys($users);
        $this->susers->load($users);
    }
    
    /**
     * Returns information about the users.
     * @param string $sqlwhere filter for the users
     */
    function getUserData($sqlwhere) {
        return get_records_select_menu('user', $sqlwhere, 'fullname', 'id,' . sql_fullname() . ' AS fullname');
    }

    /**
     * Returns an array of ids of selected users.
     * @return array of selected users' ids
     */
    function getSelectedUsers() {
        global $SESSION;
        return $SESSION->bulk_susers;
    }
    
    /**
     * Returns an int code of the action to be performed.
     * @return int code of the action or false if no action should be performed
     */
    function getAction() {
        $data =& $this->get_data();
        if(!$this->is_submitted() || empty($data->doaction)){
            return false;
        }
        return $data->action;
    }
}