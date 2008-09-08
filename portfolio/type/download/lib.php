<?php

require_once($CFG->libdir . '/portfoliolib.php');
require_once($CFG->libdir . '/packer/zip_packer.php');

class portfolio_plugin_download extends portfolio_plugin_pull_base {

    protected $exportconfig;

    public static function get_name() {
        return get_string('pluginname', 'portfolio_download');
    }

    public static function allows_multiple() {
        return false;
    }

    public function expected_time($callertime) {
        return PORTFOLIO_TIME_LOW;
    }

    public function prepare_package() {

        $files = $this->exporter->get_tempfiles();
        $zipper = new zip_packer();

        $filename = 'portfolio-export.zip';
        list ($contextid, $filearea, $itemid) = array_values($this->get('exporter')->get_base_filearea());
        if ($newfile = $zipper->archive_to_storage($files, $contextid, $filearea, $itemid, '/final/', $filename, $this->user->id)) {
            $this->set('file', $newfile);
            return true;
        }
        return false;
    }

    public function send_package() {
        return true;
    }

    public function get_extra_finish_options() {
        global $CFG;
        return array($CFG->wwwroot . '/portfolio/file.php?id=' . $this->exporter->get('id') => get_string('downloadfile', 'portfolio_download'));
    }

    public function verify_file_request_params($params) {
        // for download plugin the only thing we need to verify is that
        // the logged in user is the same as the exporting user
        global $USER;
        if ($USER->id  != $this->user->id) {
            return false;
        }
        return true;
    }

    public function get_continue_url() {
        return false;
    }
}

?>
