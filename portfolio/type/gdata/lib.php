<?php
require_once($CFG->libdir.'/filelib.php');
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Docs');
Zend_Loader::loadClass('Zend_Http_Client_Adapter_Socket');

class portfolio_plugin_gdata extends portfolio_plugin_push_base {
    public $client;
    public $gdata;
    public $listfeed;
    public $token;
    public $docID;

    public static function get_name() {
        return get_string('pluginname', 'portfolio_gdata');
    }

    public function prepare_package() {

    }

    public function send_package() {
        global $CFG;

        foreach ($this->exporter->get_tempfiles() as $file) {
            // @TODO get max size from gdata
            $filesize = $file->get_filesize();

            // TODO upload method
            $tempfilepath = $CFG->dataroot.'/temp/'.$file->get_pathnamehash();
            $file->copy_content_to($tempfilepath);

            $title = $file->get_filename();

            if ($this->get_export_config('title')) {
                $title = $this->get_export_config('title');
            }

            $return = $this->gdata->uploadFile($tempfilepath, $title, $file->get_mimetype());

            unlink($tempfilepath);

            if (method_exists($return, 'getContent')) {
                $feed_src = $return->getContent()->getSrc();
                if (preg_match('|.*docID=([a-z0-9A-Z_]+)|', $feed_src, $matches)) {
                    $this->docID = $matches[1];
                }

            } else {
                throw new portfolio_plugin_exception('uploadfailed', 'portfolio_gdata', 'Upload not yet implemented');
            }
        }
    }

    public static function allows_multiple() {
        return false;
    }

    public function get_continue_url() {
        $idparam = '';
        if (!empty($this->docID)) {
            $idparam = "/Doc?id=".$this->docID;
        }
        return "http://docs.google.com".$idparam;
    }

    public function expected_time($callertime) {
        return $callertime;
    }

    public static function get_allowed_config() {
        return array();
    }

    public static function has_admin_config() {
        return false;
    }

    public function admin_config_form(&$mform) {

    }

    public function has_export_config() {
        return true;
    }

    public function get_allowed_user_config() {
        return array('authtoken');
    }

    public function steal_control($stage) {
        global $CFG;
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return false;
        }
        if ($this->token) {
            return false;
        }

        $token = $this->get_user_config('authtoken', $this->get('user')->id);

        if (!empty($token)) {
            $this->token = $token;
            $this->client = Zend_Gdata_AuthSub::getHttpClient($token);
            $this->gdata = new Zend_Gdata_Docs($this->client);
            $this->feed = $this->gdata->getDocumentListFeed();
            return false;
        }

        $scope = 'http://docs.google.com/feeds/documents';
        $secure = false;
        $session = true;

        return Zend_Gdata_AuthSub::getAuthSubTokenUri($CFG->wwwroot.'/portfolio/add.php?postcontrol=1', $scope, $secure, $session);
    }

    public function post_control($stage, $params) {
        if ($stage != PORTFOLIO_STAGE_CONFIG) {
            return;
        }
        if (!array_key_exists('token', $params) || empty($params['token'])) {
            throw new portfolio_plugin_exception('noauthtoken', 'portfolio_gdata');
        }

        $this->set_user_config(array('authtoken' => Zend_Gdata_AuthSub::getAuthSubSessionToken($params['token'])), $this->get('user')->id);
    }

    public function export_config_form(&$mform) {
        $mform->addElement('text', 'plugin_title', get_string('title', 'portfolio_gdata'));
    }

    public function get_allowed_export_config() {
        return array('title');
    }

    public function get_export_summary() {
        return array(get_string('title', 'portfolio_gdata') => $this->get_export_config('title'));
    }
}
