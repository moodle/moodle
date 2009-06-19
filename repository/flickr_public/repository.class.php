<?php
/**
 * repository_flickr_public class
 * This one is used to create public repository
 * You can set up a public account in admin page, so everyone can
 * access photos in this public account
 *
 * @author Dongsheng Cai <dongsheng@moodle.com>
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

require_once($CFG->libdir.'/flickrlib.php');

/**
 *
 */
class repository_flickr_public extends repository {
    private $flickr;
    public $photos;

    /**
     * save api_key in config table
     * @param array $options
     * @return boolean
     */
    public function set_option($options = array()) {
        if (!empty($options['api_key'])) {
            set_config('api_key', trim($options['api_key']), 'flickr_public');
        }
        unset($options['api_key']);
        $ret = parent::set_option($options);
        return $ret;
    }

    /**
     * get api_key from config table
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '') {
        if ($config==='api_key') {
            return trim(get_config('flickr_public', 'api_key'));
        } else {
            $options['api_key'] = trim(get_config('flickr_public', 'api_key'));
        }
        $options = parent::get_option($config);
        return $options;
    }

    /**
     * is global_search available?
     * @return boolean
     */
    public function global_search() {
        if (empty($this->flickr_account)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @global object $CFG
     * @param int $repositoryid
     * @param int $context
     * @param array $options
     * @param boolean $readonly
     */
    public function __construct($repositoryid, $context = SITEID, $options = array(), $readonly=0) {
        global $CFG, $SESSION;
        parent::__construct($repositoryid, $context, $options,$readonly);
        $this->api_key = $this->get_option('api_key');
        $this->flickr  = new phpFlickr($this->api_key);
        $this->flickr_account = $this->get_option('email_address');

        $account  = optional_param('flickr_account', '', PARAM_RAW);
        $fulltext = optional_param('flickr_fulltext', '', PARAM_RAW);
        if (empty($fulltext)) {
            $fulltext = optional_param('s', '', PARAM_RAW);
        }
        $tag      = optional_param('flickr_tag', '', PARAM_RAW);
        $license  = optional_param('flickr_license', '', PARAM_RAW);

        $this->sess_account = 'flickr_public_'.$this->id.'_account';
        $this->sess_tag     = 'flickr_public_'.$this->id.'_tag';
        $this->sess_text    = 'flickr_public_'.$this->id.'_text';
        $this->sess_license = 'flickr_public_'.$this->id.'_license';

        if (!empty($account) or !empty($fulltext) or !empty($tag) or !empty($license)) {
            $SESSION->{$this->sess_tag}  = $tag;
            $SESSION->{$this->sess_text} = $fulltext;
            $SESSION->{$this->sess_account} = $account;
            $SESSION->{$this->sess_license} = $license;
        }
    }

    /**
     * check flickr account
     * @return boolean
     */
    public function check_login() {
        return !empty($this->flickr_account);
    }

    /**
     *
     * @param boolean $ajax
     * @return array
     */
    public function print_login() {
        if ($this->options['ajax']) {
            $ret = array();
            $fulltext = new stdclass;
            $fulltext->label = get_string('fulltext', 'repository_flickr_public').': ';
            $fulltext->id    = 'el_fulltext';
            $fulltext->type = 'text';
            $fulltext->name = 'flickr_fulltext';

            $tag = new stdclass;
            $tag->label = get_string('tag', 'repository_flickr_public').': ';
            $tag->id    = 'el_tag';
            $tag->type = 'text';
            $tag->name = 'flickr_tag';

            $email_field = new stdclass;
            $email_field->label = get_string('username', 'repository_flickr_public').': ';
            $email_field->id    = 'account';
            $email_field->type = 'text';
            $email_field->name = 'flickr_account';

            $license = new stdclass;
            $license->label = get_string('license', 'repository_flickr_public').': ';
            $license->id    = 'flickr_license_type';
            $license->type  = 'radio';
            $license->name  = 'flickr_license';
            // all -> all licenses
            // cc  -> creative commons
            // ccc -> reeative commons commercial
            $license->value = implode('|', array('all', 1, 2, 3, 4, 5, 6));
            $license->value_label = implode('|', array(
                get_string('all', 'repository_flickr_public'),
                get_string('by-nc-sa', 'repository_flickr_public'),
                get_string('by-nc', 'repository_flickr_public'),
                get_string('by-nc-nd', 'repository_flickr_public'),
                get_string('by', 'repository_flickr_public'),
                get_string('by-sa', 'repository_flickr_public'),
                get_string('by-nd', 'repository_flickr_public')
            ));

            $ret['login'] = array($fulltext, $tag, $email_field, $license);
            $ret['login_btn_label'] = get_string('search');
            $ret['login_btn_action'] = 'search';
            return $ret;
        } else {
            echo '<table>';
            echo '<tr><td><label>'.get_string('fulltext', 'repository_flickr_public').'</label></td>';
            echo '<td><input type="text" name="flickr_fulltext" /></td></tr>';
            echo '<tr><td><label>'.get_string('tag', 'repository_flickr_public').'</label></td>';
            echo '<td><input type="text" name="flickr_tag" /></td></tr>';
            echo '<tr><td><label>'.get_string('username', 'repository_flickr_public').'</label></td>';
            echo '<td><input type="text" name="flickr_account" /></td></tr>';

            echo '<tr><td><label>'.get_string('license', 'repository_flickr_public').'</label></td>';
            echo '<td>';
            echo '<input type="radio" name="flickr_license" value="all" /> '.get_string('all', 'repository_flickr_public');
            echo '<br />';
            echo '<input type="radio" name="flickr_license" value="1" /> '.get_string('by-nc-sa', 'repository_flickr_public');
            echo '<br />';
            echo '<input type="radio" name="flickr_license" value="2" /> '.get_string('by-nc', 'repository_flickr_public');
            echo '<br />';
            echo '<input type="radio" name="flickr_license" value="3" /> '.get_string('by-nc-nd', 'repository_flickr_public');
            echo '<br />';
            echo '<input type="radio" name="flickr_license" value="4" /> '.get_string('by', 'repository_flickr_public');
            echo '<br />';
            echo '<input type="radio" name="flickr_license" value="5" /> '.get_string('by-sa', 'repository_flickr_public');
            echo '<br />';
            echo '<input type="radio" name="flickr_license" value="6" /> '.get_string('by-nd', 'repository_flickr_public');
            echo '</td></tr>';

            echo '</table>';

            echo '<input type="hidden" name="action" value="search" />';
            echo '<input type="submit" value="Search" />';
        }
    }

    /**
     *
     * @return <type>
     */
    public function logout() {
        global $SESSION;
        unset($SESSION->{$this->sess_tag});
        unset($SESSION->{$this->sess_text});
        unset($SESSION->{$this->sess_account});
        return $this->print_login();
    }

    /**
     *
     * @param <type> $search_text
     * @return <type>
     */
    public function search($search_text) {
        global $SESSION;
        if (!empty($this->flickr_account)) {
            $people = $this->flickr->people_findByEmail($this->flickr_account);
            $this->nsid = $people['nsid'];
        }
        if (!empty($SESSION->{$this->sess_account})) {
            $people = $this->flickr->people_findByEmail($SESSION->{$this->sess_account});
            $this->nsid = $people['nsid'];
        }
        if (empty($this->nsid)) {
            $this->nsid = null;
        }
        $is_paging = optional_param('search_paging', '', PARAM_RAW);
        if (!empty($is_paging)) {
            $page = optional_param('p', '', PARAM_INT);
        } else {
            $page = 1;
        }
        if (!empty($SESSION->{$this->sess_tag}) or !empty($SESSION->{$this->sess_text}) 
            or !empty($SESSION->{$this->sess_account}) or !empty($this->nsid))
        {
            if ( empty($SESSION->{$this->sess_license}) or $SESSION->{$this->sess_license}=='all') {
                $photos = $this->flickr->photos_search(array(
                    'tags'=>$SESSION->{$this->sess_tag},
                    'page'=>$page,
                    'per_page'=>24,
                    'user_id'=>$this->nsid,
                    'text'=>$SESSION->{$this->sess_text}
                    )
                );
            } else {
                $photos = $this->flickr->photos_search(array(
                    'tags'=>$SESSION->{$this->sess_tag},
                    'page'=>$page,
                    'per_page'=>24,
                    'user_id'=>$this->nsid,
                    'license'=>$SESSION->{$this->sess_license},
                    'text'=>$SESSION->{$this->sess_text}
                    )
                );
            }
        }
        $ret = array();
        $ret['total'] = $photos['total'];
        $ret['perpage'] = $photos['perpage'];
        if (empty($photos)) {
            $ret['list'] = array();
            return $ret;
        }
        $ret = $this->build_list($photos, $page, $ret);
        $ret['list'] = array_filter($ret['list'], array($this, 'filter'));
        return $ret;
    }

    /**
     *
     * @param string $path
     * @param int $page
     * @return <type>
     */
    public function get_listing($path = '', $page = 1) {
        $people = $this->flickr->people_findByEmail($this->flickr_account);
        $this->nsid = $people['nsid'];
        $photos = $this->flickr->people_getPublicPhotos($people['nsid'], 'original_format', 24, $page);
        $ret = array();

        return $this->build_list($photos, $page, $ret);
    }

    /**
     *
     * @param <type> $photos
     * @param <type> $page
     * @return <type>
     */
    private function build_list($photos, $page = 1, &$ret) {
        if (!empty($this->nsid)) {
            $photos_url = $this->flickr->urls_getUserPhotos($this->nsid);
            $ret['manage'] = $photos_url;
        }
        $ret['list']  = array();
        $ret['pages'] = $photos['pages'];
        if (is_int($page) && $page <= $ret['pages']) {
            $ret['page'] = $page;
        } else {
            $ret['page'] = 1;
        }
        if (!empty($photos['photo'])) {
            foreach ($photos['photo'] as $p) {
                if(empty($p['title'])) {
                    $p['title'] = get_string('notitle', 'repository_flickr');
                }
                if (isset($p['originalformat'])) {
                    $format = $p['originalformat'];
                } else {
                    $format = 'jpg';
                }
                $format = '.'.$format;
                // append extensions to the files
                if (substr($p['title'], strlen($p['title'])-strlen($format)) != $format) {
                    $p['title'] .= $format; 
                }
                $ret['list'][] = array('title'=>$p['title'], 'source'=>$p['id'],
                    'id'=>$p['id'],'thumbnail'=>$this->flickr->buildPhotoURL($p, 'Square'),
                    'date'=>'', 'size'=>'unknown', 'url'=>'http://www.flickr.com/photos/'.$p['owner'].'/'.$p['id']);
            }
        }
        return $ret;
    }

    /**
     *
     * @return <type>
     */
    public function print_search() {
        $str = '';
        $str .= '<input type="hidden" name="repo_id" value="'.$this->id.'" />';
        $str .= '<input type="hidden" name="ctx_id" value="'.$this->context->id.'" />';
        $str .= '<input type="hidden" name="seekey" value="'.sesskey().'" />';
        $str .= '<label>'.get_string('fulltext', 'repository_flickr_public').': </label><br/><input name="s" value="" /><br/>';
        $str .= '<label>'.get_string('tag', 'repository_flickr_public').'</label><br /><input type="text" name="tag" /><br />';
        return $str;
    }

    /**
     *
     * @global object $CFG
     * @param string $photo_id
     * @param string $file
     * @return string
     */
    public function get_file($photo_id, $file = '') {
        global $CFG;
        $result = $this->flickr->photos_getSizes($photo_id);
        $url = '';
        if (!empty($result[4])) {
            $url = $result[4]['source'];
        } elseif(!empty($result[3])) {
            $url = $result[3]['source'];
        } elseif(!empty($result[2])) {
            $url = $result[2]['source'];
        }
        $path = $this->prepare_file($file);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));

        return $path;
    }

    /**
     * Add Instance settings input to Moodle form
     * @param <type> $
     */
    public function instance_config_form(&$mform) {
        $mform->addElement('text', 'email_address', get_string('emailaddress', 'repository_flickr_public'));
        //$mform->addRule('email_address', get_string('required'), 'required', null, 'client');
    }

    /**
     * Names of the instance settings
     * @return <type>
     */
    public static function get_instance_option_names() {
        return array('email_address');
    }

    /**
     * Add Plugin settings input to Moodle form
     * @param <type> $
     */
    public function type_config_form(&$mform) {
        $api_key = get_config('flickr_public', 'api_key');
        if (empty($api_key)) {
            $api_key = '';
        }
        $strrequired = get_string('required');
        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_flickr_public'), array('value'=>$api_key,'size' => '40'));
        $mform->addRule('api_key', $strrequired, 'required', null, 'client');
        $mform->addElement('static', null, '',  get_string('information','repository_flickr_public'));
    }

    /**
     * Names of the plugin settings
     * @return <type>
     */
    public static function get_type_option_names() {
        return array('api_key');
    }

    /**
     * is run when moodle administrator add the plugin
     */
    public static function plugin_init() {
        //here we create a default instance for this type

        $id = repository::static_function('flickr_public','create', 'flickr_public', 0, get_system_context(), array('name' => get_string('repositoryname', 'repository_flickr_public'),'email_address' => null), 1);
        if (empty($id)) {
            return false;
        } else {
            return true;
        }
    }
    public function supported_filetypes() {
        return array('web_image');
    }
}
