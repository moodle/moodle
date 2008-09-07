<?php  //$Id$

class file_info_user extends file_info {
    protected $user;

    public function __construct($browser, $context) {
        global $DB, $USER;

        parent::__construct($browser, $context);

        $userid = $context->instanceid;

        if ($userid == $USER->id) {
            $this->user = $USER;
        } else {
            // if context exists user record should exist too ;-)
            $this->user = $DB->get_record('user', array('id'=>$userid));
        }
    }

    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'filearea' =>null,
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    public function get_visible_name() {
        return fullname($this->user, true);
    }

    public function is_writable() {
        return false;
    }

    public function is_directory() {
        return true;
    }

    public function get_children() {
        global $USER, $CFG;

        // only current user for now
        return array($this->browser->get_file_info(get_context_instance(CONTEXT_USER, $USER->id), 'user_private', 0));
    }

    public function get_parent() {
        return $this->browser->get_file_info(get_context_instance(CONTEXT_SYSTEM));
    }
}
