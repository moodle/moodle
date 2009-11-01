<?php
require_once('S3.php');

class repository_s3 extends repository {
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $this->access_key = get_config('s3', 'access_key');
        $this->secret_key = get_config('s3', 'secret_key');
        if (empty($this->access_key)) {
            die(json_encode(array('e'=>get_string('repository_s3', 'needaccesskey'))));
        }
        $this->s = new S3($this->access_key, $this->secret_key);
    }
    public function get_listing($path = '', $page = '') {
        global $CFG, $OUTPUT;
        $list = array();
        $list['list'] = array();
        // the management interface url
        $list['manage'] = false;
        // dynamically loading
        $list['dynload'] = true;
        // the current path of this list.
        // set to true, the login link will be removed
        $list['nologin'] = true;
        // set to true, the search button will be removed
        $list['nosearch'] = true;
        $tree = array();
        if (empty($path)) {
            $buckets = $this->s->listBuckets();
            foreach ($buckets as $bucket) {
                $folder = array(
                    'title' => $bucket,
                    'children' => array(),
                    'thumbnail'=>$OUTPUT->old_icon_url('f/folder-32'),
                    'path'=>$bucket
                    );
                $tree[] = $folder;
            }
        } else {
            $contents = $this->s->getBucket($path);
            foreach ($contents as $file) {
                $info = $this->s->getObjectInfo($path, baseName($file['name']));
                $tree[] = array(
                    'title'=>$file['name'],
                    'size'=>$file['size'],
                    'date'=>userdate($file['time']),
                    'source'=>$path.'/'.$file['name'],
                    'thumbnail' => $OUTPUT->old_icon_url(file_extension_icon($file['name'], 32))
                    );
            }
        }

        $list['list'] = $tree;

        return $list;
    }
    public function get_file($filepath, $file) {
        global $CFG;
        $arr = explode('/', $filepath);
        $bucket   = $arr[0];
        $filename = $arr[1];
        $path = $this->prepare_file($file);
        $this->s->getObject($bucket, $filename, $path);
        return $path;
    }
    // login
    public function check_login() {
        return true;
    }
    public function global_search() {
        return false;
    }
    public static function get_type_option_names() {
        return array('access_key', 'secret_key');
    }
    public function type_config_form(&$mform) {
        $strrequired = get_string('required');
        $mform->addElement('text', 'access_key', get_string('access_key', 'repository_s3'));
        $mform->addElement('text', 'secret_key', get_string('secret_key', 'repository_s3'));
        $mform->addRule('access_key', $strrequired, 'required', null, 'client');
        $mform->addRule('secret_key', $strrequired, 'required', null, 'client');
        return true;
    }
}
