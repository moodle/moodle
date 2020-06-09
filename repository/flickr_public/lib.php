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
 * This plugin is used to access flickr pictures
 *
 * @since Moodle 2.0
 * @package    repository_flickr_public
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir.'/flickrlib.php');
require_once(__DIR__ . '/image.php');

/**
 * repository_flickr_public class
 * This one is used to create public repository
 * You can set up a public account in admin page, so everyone can access
 * flickr photos from this plugin
 *
 * @since Moodle 2.0
 * @package    repository_flickr_public
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_flickr_public extends repository {
    private $flickr;
    public $photos;

    /**
     * Stores sizes of images to prevent multiple API call
     */
    static private $sizes = array();

    /**
     * constructor method
     *
     * @global object $CFG
     * @global object $SESSION
     * @param int $repositoryid
     * @param int $context
     * @param array $options
     * @param boolean $readonly
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array(), $readonly=0) {
        global $CFG, $SESSION;
        parent::__construct($repositoryid, $context, $options,$readonly);
        $this->api_key = $this->get_option('api_key');
        $this->flickr  = new phpFlickr($this->api_key);
        $this->flickr_account = $this->get_option('email_address');
        $this->usewatermarks = $this->get_option('usewatermarks');

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

        if (!empty($account) or !empty($fulltext) or !empty($tag) or !empty($license)) {
            $SESSION->{$this->sess_tag}  = $tag;
            $SESSION->{$this->sess_text} = $fulltext;
            $SESSION->{$this->sess_account} = $account;
        }
    }

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
        return parent::set_option($options);
    }

    /**
     * get api_key from config table
     *
     * @param string $config
     * @return mixed
     */
    public function get_option($config = '') {
        if ($config==='api_key') {
            return trim(get_config('flickr_public', 'api_key'));
        } else {
            $options['api_key'] = trim(get_config('flickr_public', 'api_key'));
        }
        return parent::get_option($config);
    }

    /**
     * is global_search available?
     *
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
     * check if flickr account
     * @return boolean
     */
    public function check_login() {
        return !empty($this->flickr_account);
    }

    /**
     * construct login form
     *
     * @param boolean $ajax
     * @return array
     */
    public function print_login() {
        if ($this->options['ajax']) {
            $ret = array();
            $fulltext = new stdClass();
            $fulltext->label = get_string('fulltext', 'repository_flickr_public').': ';
            $fulltext->id    = 'el_fulltext';
            $fulltext->type = 'text';
            $fulltext->name = 'flickr_fulltext';

            $tag = new stdClass();
            $tag->label = get_string('tag', 'repository_flickr_public').': ';
            $tag->id    = 'el_tag';
            $tag->type = 'text';
            $tag->name = 'flickr_tag';

            $email_field = new stdClass();
            $email_field->label = get_string('username', 'repository_flickr_public').': ';
            $email_field->id    = 'account';
            $email_field->type = 'text';
            $email_field->name = 'flickr_account';

            $commercial = new stdClass();
            $commercial->label = get_string('commercialuse', 'repository_flickr_public').': ';
            $commercial->id    = 'flickr_commercial_id';
            $commercial->type  = 'checkbox';
            $commercial->name  = 'flickr_commercial';
            $commercial->value = 'yes';

            $modification = new stdClass();
            $modification->label = get_string('modification', 'repository_flickr_public').': ';
            $modification->id    = 'flickr_modification_id';
            $modification->type  = 'checkbox';
            $modification->name  = 'flickr_modification';
            $modification->value = 'yes';

            $ret['login'] = array($fulltext, $tag, $email_field, $commercial, $modification);
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

            echo '<tr><td><label>'.get_string('commercialuse', 'repository_flickr_public').'</label></td>';
            echo '<td>';
            echo '<input type="checkbox" name="flickr_commercial" value="yes" />';
            echo '</td></tr>';

            echo '<tr><td><label>'.get_string('modification', 'repository_flickr_public').'</label></td>';
            echo '<td>';
            echo '<input type="checkbox" name="flickr_modification" value="yes" />';
            echo '</td></tr>';

            echo '</table>';

            echo '<input type="hidden" name="action" value="search" />';
            echo '<input type="submit" value="'.get_string('search', 'repository').'" />';
        }
    }

    /**
     * destroy session
     *
     * @return object
     */
    public function logout() {
        global $SESSION;
        unset($SESSION->{$this->sess_tag});
        unset($SESSION->{$this->sess_text});
        unset($SESSION->{$this->sess_account});
        return $this->print_login();
    }

    public function license4moodle ($license_id) {
        $license = array(
            '0' => 'allrightsreserved',
            '1' => 'cc-nc-sa',
            '2' => 'cc-nc',
            '3' => 'cc-nc-nd',
            '4' => 'cc',
            '5' => 'cc-sa',
            '6' => 'cc-nd',
            '7' => 'other'
            );
        return $license[$license_id];
    }

    /**
     * search images on flickr
     *
     * @param string $search_text
     * @return array
     */
    public function search($search_text, $page = 0) {
        global $SESSION;
        $ret = array();
        if (empty($page)) {
            $page = 1;
        }

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
            // user specify a flickr account, but it is not valid
            if (!empty($this->flickr_account) or !empty($SESSION->{$this->sess_account})) {
                $ret['e'] = get_string('invalidemail', 'repository_flickr_public');
                return $ret;
            }
        }

        // including all licenses by default
        $licenses = array(1=>1, 2, 3, 4, 5, 6, 7);

        $commercial   = optional_param('flickr_commercial', '', PARAM_RAW);
        $modification = optional_param('flickr_modification', '', PARAM_RAW);

        if ($commercial == 'yes') {
            // including
            // 4: Attribution License
            // 5: Attribution ShareAlike
            // 6: Attribution NoDerives
            // 7: unknown license
            unset($licenses[1], $licenses[2], $licenses[3]);
        }
        if ($modification == 'yes') {
            // including
            // 1: Attribution NonCommercial ShareAlike
            // 2: Attribution NonCommercial
            // 4: Attribution License
            // 5: Attribution ShareAlike
            // 7: unknown license
            unset($licenses[3], $licenses[6]);
        }
        //if ($modification == 'sharealike') {
            // including
            // 1: Attribution NonCommercial ShareAlike
            // 5: Attribution ShareAlike
            //unset($licenses[2], $licenses[3], $licenses[4], $licenses[6], $licenses[7]);
        //}

        $licenses = implode(',', $licenses);

        $tag  = !empty($SESSION->{$this->sess_tag})  ? $SESSION->{$this->sess_tag}  : null;
        $text = !empty($SESSION->{$this->sess_text}) ? $SESSION->{$this->sess_text} : null;
        $nsid = !empty($this->nsid) ? $this->nsid : null;

        $photos = $this->flickr->photos_search(array(
            'tags'=>$tag,
            'page'=>$page,
            'per_page'=>24,
            'user_id'=>$nsid,
            'license'=>$licenses,
            'text'=>$text
            )
        );
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
     * return an image list
     *
     * @param string $path
     * @param int $page
     * @return array
     */
    public function get_listing($path = '', $page = 1) {
        $people = $this->flickr->people_findByEmail($this->flickr_account);
        $this->nsid = $people['nsid'];
        $photos = $this->flickr->people_getPublicPhotos($people['nsid'], 'original_format', 24, $page);
        $ret = array();

        return $this->build_list($photos, $page, $ret);
    }

    /**
     * build an image list
     *
     * @param array $photos
     * @param int $page
     * @return array
     */
    private function build_list($photos, $page = 1, &$ret) {
        global $OUTPUT;

        if (!empty($this->nsid)) {
            $photos_url = $this->flickr->urls_getUserPhotos($this->nsid);
            $ret['manage'] = $photos_url;
        }
        $ret['list']  = array();
        $ret['nosearch'] = true;
        $ret['norefresh'] = true;
        $ret['logouttext'] = get_string('backtosearch', 'repository_flickr_public');
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
                if (substr($p['title'], strlen($p['title'])-strlen($format)) != $format) {
                    // append author id
                    // $p['title'] .= '-'.$p['owner'];
                    // append file extension
                    $p['title'] .= $format;
                }
                // Get the thumbnail source URL.
                $thumbnailsource = $this->flickr->buildPhotoURL($p, 'Square');
                if (!@getimagesize($thumbnailsource)) {
                    // Use the file extension icon as a thumbnail if the original thumbnail does not exist to avoid
                    // displaying broken thumbnails in the repository.
                    $thumbnailsource = $OUTPUT->image_url(file_extension_icon($p['title'], 90))->out(false);
                }
                $ret['list'][] = array(
                    'title' => $p['title'],
                    'source' => $p['id'],
                    'id' => $p['id'],
                    'thumbnail' => $thumbnailsource,
                    'date' => '',
                    'size' => 'unknown',
                    'url' => 'http://www.flickr.com/photos/' . $p['owner'] . '/' . $p['id'],
                    'haslicense' => true,
                    'hasauthor' => true
                );
            }
        }
        return $ret;
    }

    /**
     * Print a search form
     *
     * @return string
     */
    public function print_search() {
        $str = '';
        $str .= '<input type="hidden" name="repo_id" value="'.$this->id.'" />';
        $str .= '<input type="hidden" name="ctx_id" value="'.$this->context->id.'" />';
        $str .= '<input type="hidden" name="seekey" value="'.sesskey().'" />';
        $str .= '<label>'.get_string('fulltext', 'repository_flickr_public').'</label><br/><input name="s" value="" /><br/>';
        $str .= '<label>'.get_string('tag', 'repository_flickr_public').'</label><br /><input type="text" name="flickr_tag" /><br />';
        return $str;
    }

    /**
     * Return photo url by given photo id
     * @param string $photoid
     * @return string
     */
    private function build_photo_url($photoid) {
        $bestsize = $this->get_best_size($photoid);
        if (!isset($bestsize['source'])) {
            throw new repository_exception('cannotdownload', 'repository');
        }
        return $bestsize['source'];
    }

    /**
     * Returns the best size for a photo
     *
     * @param string $photoid the photo identifier
     * @return array of information provided by the API
     */
    protected function get_best_size($photoid) {
        if (!isset(self::$sizes[$photoid])) {
            // Sizes are returned from smallest to greatest.
            self::$sizes[$photoid] = $this->flickr->photos_getSizes($photoid);
        }
        $sizes = self::$sizes[$photoid];
        $bestsize = array();
        if (is_array($sizes)) {
            while ($bestsize = array_pop($sizes)) {
                // Make sure the source is set. Exit the loop if found.
                if (isset($bestsize['source'])) {
                    break;
                }
            }
        }
        return $bestsize;
    }

    public function get_link($photoid) {
        return $this->build_photo_url($photoid);
    }

    /**
     *
     * @global object $CFG
     * @param string $photoid
     * @param string $file
     * @return string
     */
    public function get_file($photoid, $file = '') {
        global $CFG;

        $info = $this->flickr->photos_getInfo($photoid);

        // If we can read the original secret, it means that we have access to the original picture.
        if (isset($info['originalsecret'])) {
            $source = $this->flickr->buildPhotoURL($info, 'original');
        } else {
            $source = $this->build_photo_url($photoid);
        }
        // Make sure the source image exists.
        if (!@getimagesize($source)) {
            throw new moodle_exception('cannotdownload', 'repository');
        }

        if ($info['owner']['realname']) {
            $author = $info['owner']['realname'];
        } else {
            $author = $info['owner']['username'];
        }
        $copyright = get_string('author', 'repository') . ': ' . $author;

        $result = parent::get_file($source, $file);
        $path = $result['path'];

        if (!empty($this->usewatermarks)) {
            $img = new moodle_image($path);
            $img->watermark($copyright, array(10,10), array('ttf'=>true, 'fontsize'=>12))->saveas($path);
        }

        return array('path'=>$path, 'author'=>$info['owner']['realname'], 'license'=>$this->license4moodle($info['license']));
    }

    /**
     * Add Instance settings input to Moodle form
     * @param object $mform
     */
    public static function instance_config_form($mform) {
        $mform->addElement('text', 'email_address', get_string('emailaddress', 'repository_flickr_public'));
        $mform->setType('email_address', PARAM_RAW_TRIMMED); // This is for sending to flickr. Not our job to validate it.
        $mform->addElement('checkbox', 'usewatermarks', get_string('watermark', 'repository_flickr_public'));
        $mform->setDefault('usewatermarks', 0);
    }

    /**
     * Names of the instance settings
     * @return array
     */
    public static function get_instance_option_names() {
        return array('email_address', 'usewatermarks');
    }

    /**
     * Add Plugin settings input to Moodle form
     * @param object $mform
     */
    public static function type_config_form($mform, $classname = 'repository') {
        $api_key = get_config('flickr_public', 'api_key');
        if (empty($api_key)) {
            $api_key = '';
        }
        $strrequired = get_string('required');

        $mform->addElement('text', 'api_key', get_string('apikey', 'repository_flickr_public'), array('value'=>$api_key,'size' => '40'));
        $mform->setType('api_key', PARAM_RAW_TRIMMED);
        $mform->addRule('api_key', $strrequired, 'required', null, 'client');

        $mform->addElement('static', null, '',  get_string('information','repository_flickr_public'));
    }

    /**
     * Names of the plugin settings
     * @return array
     */
    public static function get_type_option_names() {
        return array('api_key', 'pluginname');
    }

    /**
     * is run when moodle administrator add the plugin
     */
    public static function plugin_init() {
        //here we create a default instance for this type

        $id = repository::static_function('flickr_public','create', 'flickr_public', 0, context_system::instance(), array('name'=>'', 'email_address' => null, 'usewatermarks' => false), 0);
        if (empty($id)) {
            return false;
        } else {
            return true;
        }
    }
    public function supported_filetypes() {
        return array('web_image');
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    /**
     * Return the source information
     *
     * @param string $photoid photo id
     * @return string|null
     */
    public function get_file_source_info($photoid) {
        return $this->build_photo_url($photoid);
    }

    /**
     * Is this repository accessing private data?
     *
     * @return bool
     */
    public function contains_private_data() {
        return false;
    }
}
