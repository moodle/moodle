<?php

require_once($CFG->libdir . '/portfoliolib.php');

class portfolio_plugin_download extends portfolio_plugin_base {

    protected $zipfile;
    protected $exportconfig;

    public static function allows_multiple() {
        return false;
    }

    public function expected_time($callertime) {
        return PORTFOLIO_TIME_LOW;
    }

    public function prepare_package($tempdir) {
        // just zip up whatever files the caller has created for us
        // and move them to the user's temporary area.
        $userdir = temp_portfolio_usertemp_directory($this->get('user')->id);

        $newfile = 'portfolio_export_' . time() . '.zip';
        $files = get_directory_list($tempdir);
        foreach ($files as $key => $file) {
            $files[$key] = $tempdir . '/' . $file;
        }

        zip_files($files, $userdir . '/' . $newfile);
        $this->set('zipfile', $newfile);

        return true;
    }

    public function send_package() {
        return true;
    }

    public function get_extra_finish_options() {
        global $CFG;
        return array(
            // @todo this will go through files api later, this is a (nonworking) hack for now.
            $CFG->wwwroot . '/file.php?file=' . $this->zipfile => get_string('downloadfile', 'portfolio_download'),
        );
    }

    public function get_continue_url() {
        return false;
    }
}

?>
