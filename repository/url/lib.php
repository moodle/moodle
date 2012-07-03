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
 * repository_url class
 * A subclass of repository, which is used to download a file from a specific url
 *
 * @since 2.0
 * @package    repository
 * @subpackage url
 * @copyright  2009 Dongsheng Cai
 * @author     Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/locallib.php');

class repository_url extends repository {

    /**
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()){
        global $CFG;
        parent::__construct($repositoryid, $context, $options);
        $this->file_url = optional_param('file', '', PARAM_RAW);
    }

    public function get_file($url, $file = '') {
        global $CFG;
        //$CFG->repository_no_delete = true;
        $path = $this->prepare_file($file);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));
        return array('path'=>$path, 'url'=>$url);
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
            $url = new stdClass();
            $url->label = $strurl.': ';
            $url->id   = 'fileurl';
            $url->type = 'text';
            $url->name = 'file';

            $ret['login'] = array($url);
            $ret['login_btn_label'] = get_string('download', 'repository_url');
            return $ret;
        } else {
            echo <<<EOD
<table>
<tr>
<td>{$strurl}: </td><td><input name="file" type="text" /></td>
</tr>
</table>
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
        global $CFG, $OUTPUT;
        $ret = array();
        $curl = new curl;
        $curl->setopt(array('CURLOPT_FOLLOWLOCATION' => true, 'CURLOPT_MAXREDIRS' => 3));
        $msg = $curl->head($this->file_url);
        $info = $curl->get_info();
        if ($info['http_code'] != 200) {
            $ret['e'] = $msg;
        } else {
            $ret['list'] = array();
            $ret['nosearch'] = true;
            $ret['nologin'] = true;
            $filename = $this->guess_filename($info['url'], $info['content_type']);
            if (strstr($info['content_type'], 'text/html') || empty($info['content_type'])) {
                // analysis this web page, general file list
                $ret['list'] = array();
                $content = $curl->get($info['url']);
                $this->analyse_page($info['url'], $content, $ret);
            } else {
                // download this file
                $ret['list'][] = array(
                    'title'=>$filename,
                    'source'=>$this->file_url,
                    'thumbnail' => $OUTPUT->pix_url(file_extension_icon($filename, 32))->out(false)
                    );
            }
        }
        return $ret;
    }
    public function analyse_page($baseurl, $content, &$list) {
        global $CFG, $OUTPUT;
        $urls = extract_html_urls($content);
        $images = $urls['img']['src'];
        $pattern = '#img(.+)src="?\'?([[:alnum:]:?=&@/._+-]+)"?\'?#i';
        if (!empty($images)) {
            foreach($images as $url) {
                $list['list'][] = array(
                    'title'=>$this->guess_filename($url, ''),
                    'source'=>url_to_absolute($baseurl, $url),
                    'thumbnail'=>url_to_absolute($baseurl, $url),
                    'thumbnail_height'=>84,
                    'thumbnail_width'=>84
                );
            }
        }
    }
    public function guess_filename($url, $type) {
        $pattern = '#\/([\w_\?\-.]+)$#';
        $matches = null;
        preg_match($pattern, $url, $matches);
        if (empty($matches[1])) {
            return $url;
        } else {
            return $matches[1];
        }
    }

    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
}

