<?php
/**
 * repository_url class
 * A subclass of repository, which is used to download a file from a specific url
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

class repository_url extends repository {

    /**
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SITEID, $options = array()){
        global $CFG;
        parent::__construct($repositoryid, $context, $options);
        if (!empty($options['client_id'])) {
            // will be used to construct download form
            $this->client_id = $options['client_id'];
        }
        $this->file_url = optional_param('download_from', '', PARAM_RAW);
    }

    public function get_file($url, $file = '') {
        global $CFG;
        $path = $this->prepare_file($file);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));
        return $path;
    }

    public function check_login() {
        if (!empty($this->file_url)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @return mixed
     */
    public function print_login() {
        $strdownload = get_string('download', 'repository');
        $strname     = get_string('rename', 'repository_url');
        $strurl      = get_string('url', 'repository_url');
        if ($this->options['ajax']) {
            $url = new stdclass;
            $url->label = $strurl.': ';
            $url->id   = 'fileurl-'.$this->client_id;
            $url->type = 'text';
            $url->name = 'file';

            $title = new stdclass;
            $title->label = $strname.': ';
            $title->id    = 'newname-'.$this->client_id;
            $title->type = 'text';
            $title->name = 'title';

            $ret['login'] = array($url, $title);
            $ret['login_btn_label'] = get_string('download', 'repository_url');
            $ret['login_btn_action'] = 'download';
            return $ret;
        } else {
            echo <<<EOD
<table>
<tr>
<td>{$strurl}: </td><td><input name="file" type="text" /></td>
</tr>
<tr>
<td>{$strname}: </td><td><input name="title" type="text" /></td>
</tr>
</table>
<input type="hidden" name="action" value="download" />
<input type="submit" value="{$strdownload}" />
EOD;

        }
    }

    /**
     * @param mixed $path
     * @param string $search
     * @return array
     */
    public function get_listing($path='', $page='') {
        $this->print_login();
    }

    public function get_name(){
        return get_string('repositoryname', 'repository_url');;
    }
}
?>
