<?php //$Id$

class block_html extends block_base {

    function init() {
        $this->title = get_string('html', 'block_html');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004123000;
    }

    function specialization() {
        // We allow empty titles
        $this->title = isset($this->config->title) ? $this->config->title : '';
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
        $this->content->text = isset($this->config->text) ? $this->config->text : '';
        $this->content->footer = '';

        return $this->content;
    }
}
?>
