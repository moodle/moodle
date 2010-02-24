<?php

class block_mentees extends block_base {

    function init() {
        $this->title = get_string('blockname', 'block_mentees');
        $this->version = 2007101509;
    }

    function applicable_formats() {
        return array('all' => true, 'tag' => false);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? $this->config->title : get_string('newmenteesblock', 'block_mentees');
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        
        global $CFG, $USER;
        if ($this->content !== NULL) {
            return $this->content;
        }

        // get all the mentees, i.e. users you have a direct assignment to
        if ($usercontexts = get_records_sql("SELECT c.instanceid, c.instanceid, u.firstname, u.lastname
                                         FROM {$CFG->prefix}role_assignments ra,
                                              {$CFG->prefix}context c,
                                              {$CFG->prefix}user u
                                         WHERE ra.userid = $USER->id
                                         AND   ra.contextid = c.id
                                         AND   c.instanceid = u.id
                                         AND   c.contextlevel = ".CONTEXT_USER)) {
                                           
            $this->content->text = '<ul>';
            foreach ($usercontexts as $usercontext) {
                $this->content->text .= '<li><a href="'.$CFG->wwwroot.'/user/view.php?id='.$usercontext->instanceid.'&amp;course='.SITEID.'">'.fullname($usercontext).'</a></li>';
            }
            $this->content->text .= '</ul>';                    
        }
                                
        
        $this->content->footer = '';

        return $this->content;
    }
}
?>
