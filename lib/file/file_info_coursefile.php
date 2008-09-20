<?php  //$Id$

class file_info_coursefile extends file_info_stored {
    public function __construct($browser, $context, $storedfile) {
        global $CFG;
        $urlbase = $CFG->wwwroot.'/file.php';
        parent::__construct($browser, $context, $storedfile, $urlbase, get_string('coursefiles'), false, true, true, false);
    }

    public function get_url($forcedownload=false, $https=false) {
        global $CFG;

        if (!$this->is_readable()) {
            return null;
        }

        if ($this->lf->is_directory()) {
            return null;
        }

        $filepath = $this->lf->get_filepath();
        $filename = $this->lf->get_filename();
        $courseid = $this->context->instanceid;

        $path = '/'.$courseid.$filepath.$filename;

        return $this->browser->encodepath($this->urlbase, $path, $forcedownload, $https);
    }

    public function get_children() {
        if (!$this->lf->is_directory()) {
            return array();
        }
        return $this->browser->build_coursefile_children($this->context, $this->lf->get_filepath());
    }


}
