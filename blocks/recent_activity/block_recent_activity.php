<?PHP //$Id$

class block_recent_activity extends block_base {
    function init() {
        $this->title = get_string('recentactivity');
        $this->version = 2007101509;
    }

    function get_content() {
        global $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        // Slightly hacky way to do it but...
        ob_start();
        print_recent_activity($COURSE);
        $this->content->text = ob_get_contents();
        ob_end_clean();

        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'my' => false, 'tag' => false);
    }
}
?>
