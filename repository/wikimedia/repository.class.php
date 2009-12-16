<?php
require_once('wikimedia.php');

class repository_wikimedia extends repository {
    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        parent::__construct($repositoryid, $context, $options);
        $this->keyword = optional_param('wikimedia_keyword', '', PARAM_RAW);
    }
    public function get_listing($path = '', $page = '') {
        global $OUTPUT;
        $client = new wikimedia;
        $result = $client->search_images($this->keyword);
        $list = array();
        $list['list'] = array();
        foreach ($result as $title=>$url) {
            $list['list'][] = array(
                'title'=>substr($title, 5),
                'thumbnail'=>$OUTPUT->pix_url(file_extension_icon('xx.jpg', 32)),
                // plugin-dependent unique path to the file (id, url, path, etc.)
                'source'=>$url,
                // the accessible url of the file
                'url'=>$url
            );
        }
        return $list;
    }
    // login
    public function check_login() {
        return !empty($this->keyword);
    }
    // if check_login returns false,
    // this function will be called to print a login form.
    public function print_login() {
        $keyword->label = get_string('keyword', 'repository_wikimedia').': ';
        $keyword->id    = 'input_text_keyword';
        $keyword->type  = 'text';
        $keyword->name  = 'wikimedia_keyword';
        $keyword->value = '';

        $form = array();
        $form['login'] = array($keyword);
        return $form;
    }
    //search
    // if this plugin support global search, if this function return
    // true, search function will be called when global searching working
    public function global_search() {
        return false;
    }
    public function search($text) {
        $search_result = array();
        $search_result['list'] = array();
        return $search_result;
    }
    // when logout button on file picker is clicked, this function will be
    // called.
    public function logout() {
        return true;
    }
    public static function get_type_option_names() {
        return null;
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
}
