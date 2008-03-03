<?PHP //$Id$

class block_tags extends block_base {
    function init() {
        $this->version = 2007101509;
        $this->title = get_string('blocktagstitle', 'tag');
    }

    function instance_allow_multiple() {
        return true;
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function instance_allow_config() {
        return true;
    }

    function specialization() {

        // load userdefined title and make sure it's never empty
        if (empty($this->config->title)) {
            $this->title = get_string('blocktagstitle','tag');
        } else {
            $this->title = $this->config->title;
        }
    }

    function get_content() {

        global $CFG, $SITE, $COURSE, $USER;

        if (empty($CFG->usetags)) {
            $this->content->text = '';
            return $this->content;
        }

        if (empty($this->config->numberoftags)) {
            $this->config->numberoftags = 80;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';

        /// Get a list of tags

        require_once($CFG->dirroot.'/tag/lib.php');

        $this->content->text = tag_print_cloud($this->config->numberoftags, true);

        return $this->content;
    }

    function instance_config_print() {
        global $CFG;

    /// set up the numberoftags select field
        $numberoftags = array();
        for($i=1;$i<=200;$i++) $numberoftags[$i] = $i;

        if (is_file($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html')) {
            print_simple_box_start('center', '', '', 5, 'blockconfigglobal');
            include($CFG->dirroot .'/blocks/'. $this->name() .'/config_instance.html');
            print_simple_box_end();
        } else {
            notice(get_string('blockconfigbad'), str_replace('blockaction=', 'dummy=', qualified_me()));
        }
    }
}

?>
