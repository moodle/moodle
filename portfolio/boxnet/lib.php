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
            if (!$created = $this->boxclient->createFolder($newfolder, array('share' => (int)$this->get_export_config('sharefolder')))) {
                throw new portfolio_plugin_exception('foldercreatefailed', 'portfolio_boxnet');
            }
            $this->folders[$created['folder_id']] = $created['folder_name'];
            $this->set_export_config(array('folder' => $created['folder_id']));
        }
        foreach ($this->exporter->get_tempfiles() as $file) {
            $return = $this->boxclient->uploadFile(
                array(
                    'file'      => $file,
                    'folder_id' => $this->get_export_config('folder'),
                    'share'     => $this->get_export_config('sharefile'),
                )
            );
            if (array_key_exists('status', $return) && $return['status'] == 'upload_ok'
                    && array_key_exists('id', $return) && count($return['id']) == 1) {
                $returnid = array_keys($return['id']);
                $this->rename_file($return['id'][array_pop($returnid)], $file->get_filename());
                // if this fails, the file was sent but not renamed - this triggers a warning but is not fatal.
            }
        }
        if ($this->boxclient->isError()) {
            throw new portfolio_plugin_exception('sendfailed', 'portfolio_boxnet', $this->boxclient->getErrorMsg());
        }
    }

    public function get_export_summary() {
        $allfolders = $this->get_folder_list();
        if ($newfolder = $this->get_export_config('newfolder')) {
            $foldername = $newfolder . ' (' . get_string('tobecreated', 'portfolio_boxnet') . ')';
        } elseif ($this->get_export_config('folder')) {
            $foldername = $allfolders[$this->get_export_config('folder')];
        } else {
            $foldername = '';
        }
        return array(
            get_string('targetfolder', 'portfolio_boxnet') => $foldername
        );
    }

    public function get_interactive_continue_url() {
        return 'http://box.net/files#0:f:' . $this->get_export_config('folder');
    }

    public function expected_time($callertime) {
        return $callertime;
    }

    public static function has_admin_config() {
        return true;
    }

    public static function get_allowed_config() {
        return array('apikey');
    }

    public function has_export_config() {
        return true;
    }

    public function get_allowed_user_config() {
        return array('authtoken', 'authtokenctime');
    }

    public function get_allowed_export_config() {
        return array('folder', 'newfolder', 'sharefile', 'sharefolder');
    }

    public function export_config_form(&$mform) {
        $folders = $this->get_folder_list();
        $mform->addElement('checkbox', 'plugin_sharefile', get_string('sharefile', 'portfolio_boxnet'));
        $mform->addElement('text', 'plugin_newfolder', get_string('newfolder', 'portfolio_boxnet'));
        $mform->addElement('checkbox', 'plugin_sharefolder', get_string('sharefolder', 'portfolio_boxnet'));
        $folders[0] = '----';
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

        $mform->addElement('text', 'apikey', get_string('apikey', 'portfolio_boxnet'));
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');
        $a = new stdClass();
        $a->servicesurl = 'http://www.box.net/developers/services';
        $a->callbackurl = $CFG->wwwroot . '/portfolio/add.php?postcontrol=1&type=boxnet';
        $mform->addElement('static', 'setupinfo', get_string('setupinfo', 'portfolio_boxnet'),
            get_string('setupinfodetails', 'portfolio_boxnet', $a));
    }

    public function steal_control($stage) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }
        if ($this->authtoken) {
            return false;
        }
        if (!$this->ensure_ticket()) {
            throw new portfolio_plugin_exception('noticket', 'portfolio_boxnet');
        }
        $token = $this->get_user_config('authtoken', $this->get('user')->id);
        $ctime= $this->get_user_config('authtokenctime', $this->get('user')->id);
        if (!empty($token) && (($ctime + 60*60*20) > time())) {
            $this->authtoken = $token;
            $this->boxclient->auth_token = $token;
            return false;
        }
        return 'http://www.box.net/api/1.0/auth/'.$this->ticket;
    }

    public function post_control($stage, $params) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return;
        }
        if (!array_key_exists('auth_token', $params) || empty($params['auth_token'])) {
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_boxnet');
        }
        $this->authtoken = $params['auth_token'];
        $this->boxclient->auth_token = $this->authtoken;
        $this->set_user_config(array('authtoken' => $this->authtoken, 'authtokenctime' => time()), $this->get('user')->id);
    }

    private function ensure_ticket() {
        if (!empty($this->boxclient)) {
            return true;
        }
        $this->boxclient = new boxclient($this->get_config('apikey'), '');
        $ticket_return = $this->boxclient->getTicket();
        if ($this->boxclient->isError() || empty($ticket_return)) {
            throw new portfolio_plugin_exception('noticket', 'portfolio_boxnet');
        }
        $this->ticket = $ticket_return['ticket'];
        return $this->ticket;
    }

    private function ensure_account_tree() {
        if (!empty($this->accounttree)) {
            return;
        }
        if (empty($this->ticket)
            || empty($this->authtoken)
            || empty($this->boxclient)) {
            // if we don't have these we're pretty much screwed
            throw new portfolio_plugin_exception('folderlistfailed', 'portfolio_boxnet');
            return false;
        }
        $this->accounttree = $this->boxclient->getAccountTree();
        if ($this->boxclient->isError()) {
            throw new portfolio_plugin_exception('folderlistfailed', 'portfolio_boxnet');
        }
        if (!is_array($this->accounttree)) {
            return false;
        }
    }

    private function get_folder_list() {
        if (!empty($this->folders)) {
            return $this->folders;
        }
        $this->ensure_account_tree();
        $folders = array();
        foreach ($this->accounttree['folder_id'] as $key => $id) {
            if (empty($id)) {
                continue;
            }
            $name = $this->accounttree['folder_name'][$key];
            if (!empty($this->accounttree['shared'][$key])) {
                $name .= ' (' . get_string('sharedfolder', 'portfolio_boxnet') . ')';
            }
            $folders[$id] = $name;
        }
        $this->folders = $folders;
        return $folders;
    }

    private function rename_file($fileid, $newname) {
        // look at moving this to the boxnet client class
        $this->ensure_account_tree();
        $count = 1;
        $bits = explode('.', $newname);
        $suffix = '';
        if (count($bits) == 1) {
            $prefix = $newname;
        } else {
            $suffix = '.' . array_pop($bits);
            $prefix = implode('.', $bits);
        }
        while (true) {
            if (!array_key_exists('file_name', $this->accounttree) || !in_array($newname, $this->accounttree['file_name'])) {
                for ($i = 0; $i < 2; $i++) {
                    if ($this->boxclient->renameFile($fileid, $newname)) {
                        return true;
                    }
                }
                debugging("tried three times to rename file and failed");
                return false;
            }
            $newname = $prefix . '(' . $count . ')' . $suffix;
            $count++;
        }
        return false;
    }

    public function instance_sanity_check() {
        if (!$this->get_config('apikey')) {
            return 'err_noapikey';
        }
    //@TODO see if we can verify the api key without actually getting an authentication token
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
