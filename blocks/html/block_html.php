<?php //$Id$

class block_html extends block_base {

    function init() {
        $this->title = get_string('html', 'block_html');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004123000;
    }

    function specialization() {
        // Does not check if $this->config or $this->config->title
        // are empty because if they are then the user wishes the title
        // of the block to be hidden anyway
        $this->title = $this->config->title;
    }

    function instance_allow_multiple() {
        return true;
    }

    function get_content() {
        global $CFG;
        require_once($CFG->dirroot .'/lib/weblib.php');
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = $this->config->text;
        $this->content->footer = '';

        return $this->content;
    }
}
?>
