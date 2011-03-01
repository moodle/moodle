<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * repository_alfresco class
 * This is a class used to browse files from alfresco
 *
 * @since      2.0
 * @package    repository
 * @subpackage alfresco
 * @copyright  2009 Dongsheng Cai
 * @author     Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class repository_alfresco extends repository {
    private $ticket = null;
    private $user_session = null;
    private $store = null;
    private $alfresco;

    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        global $SESSION, $CFG;
        parent::__construct($repositoryid, $context, $options);
        $this->sessname = 'alfresco_ticket_'.$this->id;
        if (class_exists('SoapClient')) {
            require_once($CFG->libdir . '/alfresco/Service/Repository.php');
            require_once($CFG->libdir . '/alfresco/Service/Session.php');
            require_once($CFG->libdir . '/alfresco/Service/SpacesStore.php');
            require_once($CFG->libdir . '/alfresco/Service/Node.php');
            // setup alfresco
            $server_url = '';
            if (!empty($this->options['alfresco_url'])) {
                $server_url = $this->options['alfresco_url'];
            } else {
                return;
            }
            $this->alfresco = new Alfresco_Repository($this->options['alfresco_url']);
            $this->username   = optional_param('al_username', '', PARAM_RAW);
            $this->password   = optional_param('al_password', '', PARAM_RAW);
            try{
                // deal with user logging in
                if (empty($SESSION->{$this->sessname}) && !empty($this->username) && !empty($this->password)) {
                    $this->ticket = $this->alfresco->authenticate($this->username, $this->password);
                    $SESSION->{$this->sessname} = $this->ticket;
                } else {
                    if (!empty($SESSION->{$this->sessname})) {
                        $this->ticket = $SESSION->{$this->sessname};
                    }
                }
                $this->user_session = $this->alfresco->createSession($this->ticket);
                $this->store = new SpacesStore($this->user_session);
            } catch (Exception $e) {
                $this->logout();
            }
            $this->current_node = null;
        } else {
            $this->disabled = true;
        }
    }

    public function print_login() {
        if ($this->options['ajax']) {
            $user_field = new stdClass();
            $user_field->label = get_string('username', 'repository_alfresco').': ';
            $user_field->id    = 'alfresco_username';
            $user_field->type  = 'text';
            $user_field->name  = 'al_username';

            $passwd_field = new stdClass();
            $passwd_field->label = get_string('password', 'repository_alfresco').': ';
            $passwd_field->id    = 'alfresco_password';
            $passwd_field->type  = 'password';
            $passwd_field->name  = 'al_password';

            $ret = array();
            $ret['login'] = array($user_field, $passwd_field);
            return $ret;
        } else {
            echo '<table>';
            echo '<tr><td><label>'.get_string('username', 'repository_alfresco').'</label></td>';
            echo '<td><input type="text" name="al_username" /></td></tr>';
            echo '<tr><td><label>'.get_string('password', 'repository_alfresco').'</label></td>';
            echo '<td><input type="password" name="al_password" /></td></tr>';
            echo '</table>';
            echo '<input type="submit" value="Enter" />';
        }
    }

    public function logout() {
        global $SESSION;
        unset($SESSION->{$this->sessname});
        return $this->print_login();
    }

    public function check_login() {
        global $SESSION;
        return !empty($SESSION->{$this->sessname});
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

    /**
     * Get a file list from alfresco
     *
     * @param string $uuid a unique id of directory in alfresco
     * @param string $path path to a directory
     * @return array
     */
    public function get_listing($uuid = '', $path = '') {
        global $CFG, $SESSION, $OUTPUT;
        $ret = array();
        $ret['dynload'] = true;
        $ret['list'] = array();
        $server_url = $this->options['alfresco_url'];
        $pattern = '#^(.*)api#';
        if ($return = preg_match($pattern, $server_url, $matches)) {
            $ret['manage'] = $matches[1].'faces/jsp/dashboards/container.jsp';
        }

        $ret['path'] = array(array('name'=>get_string('pluginname', 'repository_alfresco'), 'path'=>''));

        try {
            if (empty($uuid)) {
                $this->current_node = $this->store->companyHome;
            } else {
                $this->current_node = $this->user_session->getNode($this->store, $uuid);
            }

            $folder_filter = "{http://www.alfresco.org/model/content/1.0}folder";
            $file_filter = "{http://www.alfresco.org/model/content/1.0}content";

            // top level sites folder
            $sites_filter = "{http://www.alfresco.org/model/content/1.0}sites";
            // individual site
            $site_filter = "{http://www.alfresco.org/model/content/1.0}site";

            foreach ($this->current_node->children as $child)
            {
                if ($child->child->type == $folder_filter or
                    $child->child->type == $sites_filter or
                    $child->child->type == $site_filter)
                {
                    $ret['list'][] = array('title'=>$child->child->cm_name,
                        'path'=>$child->child->id,
                        'thumbnail'=>$OUTPUT->pix_url('f/folder-32') . '',
                        'children'=>array());
                } elseif ($child->child->type == $file_filter) {
                    $ret['list'][] = array('title'=>$child->child->cm_name,
                        'thumbnail' => $OUTPUT->pix_url(file_extension_icon($child->child->cm_name, 32))->out(false),
                        'source'=>$child->child->id);
                }
            }
        } catch (Exception $e) {
            unset($SESSION->{$this->sessname});
            $ret = $this->print_login();
        }
        return $ret;
    }

    /**
     * Download a file from alfresco
     *
     * @param string $uuid a unique id of directory in alfresco
     * @param string $path path to a directory
     * @return array
     */
    public function get_file($uuid, $file = '') {
        $node = $this->user_session->getNode($this->store, $uuid);
        $url = $this->get_url($node);
        $path = $this->prepare_file($file);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));
        return array('path'=>$path, 'url'=>$url);
    }

    /**
     * Return file URL
     *
     * @param string $url the url of file
     * @return string
     */
    public function get_link($uuid) {
        $node = $this->user_session->getNode($this->store, $uuid);
        $url = $this->get_url($node);
        return $url;
    }

    public function print_search() {
        $str = parent::print_search();
        $str .= '<label>Space: </label><br /><select name="space">';
        foreach ($this->user_session->stores as $v) {
            $str .= '<option ';
            if ($v->__toString() === 'workspace://SpacesStore') {
                $str .= 'selected ';
            }
            $str .= 'value="';
            $str .= $v->__toString().'">';
            $str .= $v->__toString();
            $str .= '</option>';
        }
        $str .= '</select>';
        return $str;
    }

    /**
     * Look for a file
     *
     * @param string $search_text
     * @return array
     */
    public function search($search_text) {
        $space = optional_param('space', 'workspace://SpacesStore', PARAM_RAW);
        $currentStore = $this->user_session->getStoreFromString($space);
        $nodes = $this->user_session->query($currentStore, $search_text);
        $ret = array();
        $ret['list'] = array();
        foreach($nodes as $v) {
            $ret['list'][] = array('title'=>$v->cm_name, 'source'=>$v->id);
        }
        return $ret;
    }

    /**
     * Enable mulit-instance
     *
     * @return array
     */
    public static function get_instance_option_names() {
        return array('alfresco_url');
    }

    /**
     * define a configuration form
     *
     * @return bool
     */
    public function instance_config_form($mform) {
        if (!class_exists('SoapClient')) {
            $mform->addElement('static', null, get_string('notice'), get_string('soapmustbeenabled', 'repository_alfresco'));
            return false;
        }
        $mform->addElement('text', 'alfresco_url', get_string('alfresco_url', 'repository_alfresco'), array('size' => '40'));
        $mform->addElement('static', 'alfreco_url_intro', '', get_string('alfrescourltext', 'repository_alfresco'));
        $mform->addRule('alfresco_url', get_string('required'), 'required', null, 'client');
        return true;
    }

    /**
     * Check if SOAP extension enabled
     *
     * @return bool
     */
    public static function plugin_init() {
        if (!class_exists('SoapClient')) {
            print_error('soapmustbeenabled', 'repository_alfresco');
            return false;
        } else {
            return true;
        }
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
}

