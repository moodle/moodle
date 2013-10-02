<?php

//Included from lib.php

require_once(dirname(__FILE__) . '/teammode_settings_form.php');

class workshop_teammode_random_allocator extends workshop_random_allocator {
    
    protected $form_class = 'workshop_teammode_random_allocator_form';
    
    protected function get_options_from_settings($settings) {
        $options                     = array();
        $options['numofreviews']     = $settings->numofreviews;
        $options['numper']           = $settings->numper;
        $options['excludesamegroup'] = true;
        return $options;
    }
    
    protected function get_group_mode() {
        //teammode always spoofs visible groups mode
        return VISIBLEGROUPS;
    }
    
    protected function get_authors() {
        global $DB;
        $rslt = $this->workshop->get_submissions_grouped();
        //now we have to do some magic to turn these back into "authors"
        $ret = array();
        $users = array();
        
        //loop 1: get user ids
        foreach ($rslt as $r) {
            $users[] = $r->authorid;
        }
        $fields = user_picture::fields();
        $users = $DB->get_records_list('user','id',$users,'',$fields);
        //loop 2: apply users to submissions 
        $ret[0] = array();
        foreach ($rslt as $r){
            $ret[$r->group->id] = array( $r->authorid => $users[$r->authorid] );
            $ret[0][$r->authorid] = $users[$r->authorid];
        }

        return $ret;
    }
    
    protected function self_allocation($authors=array(), $reviewers=array(), $assessments=array()) {
        if (!isset($authors[0]) || !isset($reviewers[0])) {
            // no authors or no reviewers
            return array();
        }
        $alreadyallocated = array();
        foreach ($assessments as $assessment) {
            if ($assessment->authorid == $assessment->reviewerid) {
                $alreadyallocated[$assessment->authorid] = 1;
            }
        }
        $add = array(); // list of new allocations to be created
        foreach (array_slice($authors,1,null,true) as $groupid => $a) {
            // for all authors in all groups
            $authorid = key($a);
            
            $groupmembers = groups_get_members($groupid,'u.id');
            foreach($groupmembers as $memberid => $member) {
                if (isset($reviewers[0][$memberid])) {
                    // if the author can be reviewer
                    if (!isset($alreadyallocated[$memberid])) {
                        // and the allocation does not exist yet, then
                        $add[] = array($memberid => $authorid);
                    }
                }
            }
        }
        return $add;
    }
    
}
