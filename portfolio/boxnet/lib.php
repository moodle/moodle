<?php
require_once($CFG->libdir.'/portfolio/plugin.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/boxlib.php');

class portfolio_plugin_boxnet extends portfolio_plugin_push_base {

    public $boxclient;
    private $ticket;
    private $authtoken;
    private $folders;
    private $accounttree;

    public static function get_name() {
        return get_string('pluginname', 'portfolio_boxnet');
    }

    public function prepare_package() {
        // don't do anything for this plugin, we want to send all files as they are.
    }

    public function send_package() {
        // if we need to create the folder, do it now
        if ($newfolder = $this->get_export_config('newfolder')) {
            $created = $this->boxclient->create_folder($newfolder);
            if (empty($created->id)) {
                throw new portfolio_plugin_exception('foldercreatefailed', 'portfolio_boxnet');
            }
            $this->folders[$created->id] = $created->name;
            $this->set_export_config(array('folder' => $created->id));
        }
        foreach ($this->exporter->get_tempfiles() as $file) {
            $return = $this->boxclient->upload_file($file, $this->get_export_config('folder'));
            if (!empty($result->type) && $result->type == 'error') {
                throw new portfolio_plugin_exception('sendfailed', 'portfolio_boxnet', $result->message);
            }
            $createdfile = reset($return->entries);
            if (!empty($createdfile->id)) {
                $result = $this->rename_file($createdfile->id, $file->get_filename());
                // If this fails, the file was sent but not renamed.
            }
        }
    }

    public function get_export_summary() {
        $allfolders = $this->get_folder_list();
        if ($newfolder = $this->get_export_config('newfolder')) {
            $foldername = $newfolder . ' (' . get_string('tobecreated', 'portfolio_boxnet') . ')';
        } else if ($this->get_export_config('folder')) {
            $foldername = $allfolders[$this->get_export_config('folder')];
        } else {
            $foldername = '/';
        }
        return array(
            get_string('targetfolder', 'portfolio_boxnet') => s($foldername)
        );
    }

    public function get_interactive_continue_url() {
        return 'https://app.box.net/files/0/f/' . $this->get_export_config('folder') . '/';
    }

    public function expected_time($callertime) {
        // We're forcing this to be run 'interactively' because the plugin
        // does not support running in cron.
        return PORTFOLIO_TIME_LOW;
    }

    public static function has_admin_config() {
        return true;
    }

    public static function get_allowed_config() {
        return array('clientid', 'clientsecret');
    }

    public function has_export_config() {
        return true;
    }

    public function get_allowed_export_config() {
        return array('folder', 'newfolder');
    }

    public function export_config_form(&$mform) {
        $folders = $this->get_folder_list();
        $mform->addElement('text', 'plugin_newfolder', get_string('newfolder', 'portfolio_boxnet'));
        $mform->setType('plugin_newfolder', PARAM_RAW);
        $folders[0] = '/';
        ksort($folders);
        $mform->addElement('select', 'plugin_folder', get_string('existingfolder', 'portfolio_boxnet'), $folders);
    }

    public function export_config_validation(array $data) {
        $allfolders = $this->get_folder_list();
        if (in_array($data['plugin_newfolder'], $allfolders)) {
            return array('plugin_newfolder' => get_string('folderclash', 'portfolio_boxnet'));
        }
    }

    public static function admin_config_form(&$mform) {
        global $CFG;

        $mform->addElement('text', 'clientid', get_string('clientid', 'portfolio_boxnet'));
        $mform->addRule('clientid', get_string('required'), 'required', null, 'client');
        $mform->setType('clientid', PARAM_RAW_TRIMMED);

        $mform->addElement('text', 'clientsecret', get_string('clientsecret', 'portfolio_boxnet'));
        $mform->addRule('clientsecret', get_string('required'), 'required', null, 'client');
        $mform->setType('clientsecret', PARAM_RAW_TRIMMED);

        $a = new stdClass();
        $a->servicesurl = 'https://app.box.com/developers/services';
        $mform->addElement('static', 'setupinfo', get_string('setupinfo', 'portfolio_boxnet'),
            get_string('setupinfodetails', 'portfolio_boxnet', $a));

        if (!is_https()) {
            $mform->addElement('static', 'warninghttps', '', get_string('warninghttps', 'portfolio_boxnet'));
        }
    }

    public function steal_control($stage) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }
        if (empty($this->boxclient)) {
            $returnurl = new moodle_url('/portfolio/add.php', array('postcontrol' => 1, 'type' => 'boxnet',
                'sesskey' => sesskey()));
            $this->boxclient = new boxnet_client($this->get_config('clientid'), $this->get_config('clientsecret'), $returnurl, '');
        }
        if ($this->boxclient->is_logged_in()) {
            return false;
        }
        return $this->boxclient->get_login_url();
    }

    public function post_control($stage, $params) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return;
        }
        if (!$this->boxclient->is_logged_in()) {
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_boxnet');
        }
    }

    /**
     * Get the folder list.
     *
     * This is limited to the folders in the root folder.
     *
     * @return array of folders.
     */
    protected function get_folder_list() {
        if (empty($this->folders)) {
            $folders = array();
            $result = $this->boxclient->get_folder_items();
            foreach ($result->entries as $item) {
                if ($item->type != 'folder') {
                    continue;
                }
                $folders[$item->id] = $item->name;
                if (!empty($item->shared)) {
                    $folders[$item->id] .= ' (' . get_string('sharedfolder', 'portfolio_boxnet') . ')';
                }
            }
            $this->folders = $folders;
        }
        return $this->folders;
    }

    /**
     * Rename a file.
     *
     * If the name is already taken, we append the current date to the file
     * to prevent name conflicts.
     *
     * @param int $fileid The file ID.
     * @param string $newname The new name.
     * @return bool Whether it succeeded or not.
     */
    protected function rename_file($fileid, $newname) {
        $result = $this->boxclient->rename_file($fileid, $newname);
        if (!empty($result->type) && $result->type == 'error') {
            $bits = explode('.', $newname);
            $suffix = '';
            if (count($bits) == 1) {
                $prefix = $newname;
            } else {
                $suffix = '.' . array_pop($bits);
                $prefix = implode('.', $bits);
            }
            $newname = $prefix . ' (' . date('Y-m-d H-i-s') . ')' . $suffix;
            $result = $this->boxclient->rename_file($fileid, $newname);
            if (empty($result->type) || $result->type != 'error') {
                return true;
            } else {
                // We could not rename the file for some reason...
                debugging('Error while renaming the file on Box.net', DEBUG_DEVELOPER);
            }
        } else {
            return true;
        }
        return false;
    }

    public function instance_sanity_check() {
        global $CFG;
        if (!$this->get_config('clientid') || !$this->get_config('clientsecret')) {
            return 'missingoauthkeys';
        } else if (!is_https()) {
            return 'missinghttps';
        }
    }

    public static function allows_multiple_instances() {
        return false;
    }

    public function supported_formats() {
        return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_RICHHTML);
    }

    /*
     * for now , boxnet doesn't support this,
     * because we can't dynamically construct return urls.
     */
    public static function allows_multiple_exports() {
        return false;
    }
}
