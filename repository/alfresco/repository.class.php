<?php
/**
 * repository_alfresco class
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->libdir . '/soaplib.php');
require_once($CFG->libdir . '/alfresco/Service/Repository.php');
require_once($CFG->libdir . '/alfresco/Service/Session.php');
require_once($CFG->libdir . '/alfresco/Service/SpacesStore.php');
require_once($CFG->libdir . '/alfresco/Service/Node.php');

class repository_alfresco extends repository {
    private $repo = null;
    private $ticket = null;
    private $sess = null;
    private $store = null;

    public function __construct($repositoryid, $context = SITEID, $options = array()) {
        global $SESSION, $CFG;
        parent::__construct ($repositoryid, $context, $options);
        $this->repo = new Al_Repository($this->alfresco_url);
        $this->sess_name = 'alfresco_ticket_'.$this->id;
        $this->username   = optional_param('al_username', '', PARAM_RAW);
        $this->password   = optional_param('al_password', '', PARAM_RAW);
        try{
            if ( empty($SESSION->{$this->sess_name}) && !empty($this->username) && !empty($this->password)) {
                $this->ticket = $this->repo->authenticate($this->username, $this->password);
                $SESSION->{$this->sess_name} = $this->ticket;	
            } else {
                $this->ticket = $SESSION->{$this->sess_name}; 	
            }
            $this->sess = $this->repo->createSession($this->ticket);
            $this->store = new SpacesStore($this->sess);
        } catch (Exception $e) {
            $this->logout();
        }
        $this->current_node = null;
    }
    public function print_login() {
        if ($this->options['ajax']) {
            $user_field->label = get_string('username', 'repository_alfresco').': ';
            $user_field->id    = 'alfresco_username';
            $user_field->type  = 'text';
            $user_field->name  = 'al_username';
            
            $passwd_field->label = get_string('password', 'repository_alfresco').': ';
            $passwd_field->id    = 'alfresco_password';
            $passwd_field->type  = 'password';
            $passwd_field->name  = 'al_password';

            $ret = array();
            $ret['login'] = array($user_field, $passwd_field);
            return $ret;
        }
    }

    public function logout() {
        global $SESSION;
        unset($SESSION->{$this->sess_name});
        return $this->print_login();
    }

    public function check_login() {
        global $SESSION;
        return !empty($SESSION->{$this->sess_name});
    }

    private function get_url($node) {
        $result = null;
        if ($node->type == "{http://www.alfresco.org/model/content/1.0}content") {
            $contentData = $node->cm_content;
            if ($contentData != null) {
                $result = $contentData->getUrl();
            }
        } else {
            $result = "index.php?".
                "&uuid=".$node->id.
                "&name=".$node->cm_name.
                "&path=".'Company Home';
        }
        return $result;
    }

    public function get_listing($uuid = '', $search = '') {
        global $CFG;

        $ret = array();
        $ret['dynload'] = true;
        $ret['list'] = array();
        $url = $this->alfresco_url;
        $pattern = '#^(.*)api#';
        preg_match($pattern, $url, $matches);
        $ret['manage'] = $matches[1].'faces/jsp/dashboards/container.jsp';

        $ret['path'] = array(array('name'=>'Root', 'path'=>''));

        if (empty($uuid)) {
            $this->current_node = $this->store->companyHome;
        } else {
            $this->current_node = $this->sess->getNode($this->store, $uuid);
        }
        $folder_filter = "{http://www.alfresco.org/model/content/1.0}folder";
        $file_filter = "{http://www.alfresco.org/model/content/1.0}content";
        foreach ($this->current_node->children as $child)
        {
            if ($child->child->type == $folder_filter)
            {
                $ret['list'][] = array('title'=>$child->child->cm_name,
                    'path'=>$child->child->id,
                    'thumbnail'=>$CFG->pixpath.'/f/folder.gif',
                    'children'=>array());
            } elseif ($child->child->type == $file_filter) {
                $ret['list'][] = array('title'=>$child->child->cm_name,
                    'thumbnail' => $CFG->pixpath .'/f/'. mimeinfo("icon", $child->child->cm_name),
                    'source'=>$child->child->id);
            }
        }
        return $ret;
    }

    public function get_file($uuid, $file = '') {
        global $CFG;
        $node = $this->sess->getNode($this->store, $uuid);
        $url = $this->get_url($node);
        if (!file_exists($CFG->dataroot.'/repository/download')) {
            mkdir($CFG->dataroot.'/repository/download/', 0777, true);
        }
        if(is_dir($CFG->dataroot.'/repository/download')) {
            $dir = $CFG->dataroot.'/repository/download/';
        }

        if (empty($file)) {
            $file = $uuid.'_'.time();
        }
        if (file_exists($dir.$file)) {
            $file = uniqid('al_').$file;
        }
        $fp = fopen($dir.$file, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));
        return $dir.$file;
    }

    public function print_search() {
        parent::print_search();
        echo '<label>Space: </label><br /><select name="space">';
        foreach ($this->sess->stores as $v) {	
            echo '<option ';
            if ($v->__toString() === 'workspace://SpacesStore') {
                echo 'selected ';
            }
            echo 'value="';
            echo $v->__toString().'">';
            echo $v->__toString();
            echo '</option>';
        }
        echo '</select>';
        return true;
    }

    public function search($search_text) {
        global $CFG;
        $space = optional_param('space', 'workspace://SpacesStore', PARAM_RAW);
        $currentStore = $this->sess->getStoreFromString($space);	
        $nodes = $this->sess->query($currentStore, $search_text);
        $ret = array();
        $ret['list'] = array();
        foreach($nodes as $v) {
            $ret['list'][] = array('title'=>$v->cm_name, 'source'=>$v->id);
        }
        return $ret;
    }

    public static function get_instance_option_names() {
        return array('alfresco_url');
    }

    public function instance_config_form(&$mform) {
        $mform->addElement('text', 'alfresco_url', get_string('alfresco_url', 'repository_alfresco'), array('size' => '40'));
        $mform->addRule('alfresco_url', get_string('required'), 'required', null, 'client');
    }
}
?>
