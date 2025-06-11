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
 * This plugin is used to access files by providing an url
 *
 * @since Moodle 2.0
 * @package    repository_url
 * @copyright  2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . '/repository/lib.php');
require_once(__DIR__.'/locallib.php');

/**
 * repository_url class
 * A subclass of repository, which is used to download a file from a specific url
 *
 * @since Moodle 2.0
 * @package    repository_url
 * @copyright  2009 Dongsheng Cai {@link http://dongsheng.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_url extends repository {
    /** @var int Maximum time of recursion. */
    const MAX_RECURSION_TIME = 5;
    /** @var int Maximum number of CSS imports. */
    protected const MAX_CSS_IMPORTS = 10;
    /** @var int CSS import counter. */
    protected int $cssimportcounter = 0;
    var $processedfiles = array();
    /** @var int Recursion counter. */
    var $recursioncounter = 0;
    /** @var string file URL. */
    public $file_url;

    /**
     * @param int $repositoryid
     * @param object $context
     * @param array $options
     */
    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()){
        global $CFG;
        parent::__construct($repositoryid, $context, $options);
        $this->file_url = optional_param('file', '', PARAM_RAW);
        $this->file_url = $this->escape_url($this->file_url);
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
            $ret['allowcaching'] = true; // indicates that login form can be cached in filepicker.js
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
        $ret = array();
        $ret['list'] = array();
        $ret['nosearch'] = true;
        $ret['norefresh'] = true;
        $ret['nologin'] = true;

        $this->file_url = clean_param($this->file_url, PARAM_URL);
        if (empty($this->file_url)) {
            throw new repository_exception('validfiletype', 'repository_url');
        }

        $this->parse_file(null, $this->file_url, $ret, true);
        return $ret;
    }

    /**
     * Parses one file (either html or css)
     *
     * @param string $baseurl (optional) URL of the file where link to this file was found
     * @param string $relativeurl relative or absolute link to the file
     * @param array $list
     * @param bool $mainfile true only for main HTML false and false for all embedded/linked files
     */
    protected function parse_file($baseurl, $relativeurl, &$list, $mainfile = false) {
        if (preg_match('/([\'"])(.*)\1/', $relativeurl, $matches)) {
            $relativeurl = $matches[2];
        }
        if (empty($baseurl)) {
            $url = $relativeurl;
        } else {
            $url = htmlspecialchars_decode(url_to_absolute($baseurl, $relativeurl), ENT_COMPAT);
        }
        if (in_array($url, $this->processedfiles)) {
            // Avoid endless recursion for the same URL with same parameters.
            return;
        }
        // Remove the query string and anchors before check.
        $recursioncheckurl = (new moodle_url($url))->out_omit_querystring();
        if (in_array($recursioncheckurl, $this->processedfiles)) {
            $this->recursioncounter++;
        }
        if ($this->recursioncounter >= self::MAX_RECURSION_TIME) {
            // Avoid endless recursion for the same URL with different parameters.
            return;
        }
        $this->processedfiles[] = $url;
        $curl = new curl;
        $curl->setopt(array('CURLOPT_FOLLOWLOCATION' => true, 'CURLOPT_MAXREDIRS' => 3));
        $msg = $curl->head($url);
        $info = $curl->get_info();
        if ($info['http_code'] != 200) {
            if ($mainfile) {
                $list['error'] = $msg;
            }
        } else {
            $csstoanalyze = '';
            if ($mainfile && (strstr($info['content_type'], 'text/html') || empty($info['content_type']))) {
                // parse as html
                $htmlcontent = $curl->get($info['url']);
                $ddoc = new DOMDocument();
                @$ddoc->loadHTML($htmlcontent);
                // extract <img>
                $tags = $ddoc->getElementsByTagName('img');
                foreach ($tags as $tag) {
                    $url = $tag->getAttribute('src');
                    $this->add_image_to_list($info['url'], $url, $list);
                }
                // analyse embedded css (<style>)
                $tags = $ddoc->getElementsByTagName('style');
                foreach ($tags as $tag) {
                    if ($tag->getAttribute('type') == 'text/css') {
                        $csstoanalyze .= $tag->textContent."\n";
                    }
                }
                // analyse links to css (<link type='text/css' href='...'>)
                $tags = $ddoc->getElementsByTagName('link');
                foreach ($tags as $tag) {
                    if ($tag->getAttribute('type') == 'text/css' && strlen($tag->getAttribute('href'))) {
                        $this->parse_file($info['url'], $tag->getAttribute('href'), $list);
                    }
                }
            } else if (strstr($info['content_type'], 'css')) {
                // parse as css
                $csscontent = $curl->get($info['url']);
                $csstoanalyze .= $csscontent."\n";
            } else if (strstr($info['content_type'], 'image/')) {
                // download this file
                $this->add_image_to_list($info['url'], $info['url'], $list);
            } else {
                $list['error'] = get_string('validfiletype', 'repository_url');
            }

            // parse all found css styles
            if (strlen($csstoanalyze)) {
                $urls = extract_css_urls($csstoanalyze);
                if (!empty($urls['property'])) {
                    foreach ($urls['property'] as $url) {
                        $this->add_image_to_list($info['url'], $url, $list);
                    }
                }
                if (!empty($urls['import'])) {
                    foreach ($urls['import'] as $cssurl) {
                        // Limit the number of CSS imports to avoid infinite imports.
                        if ($this->cssimportcounter >= self::MAX_CSS_IMPORTS) {
                            return;
                        }
                        $this->cssimportcounter++;
                        $this->parse_file($info['url'], $cssurl, $list);
                    }
                }
            }
        }
    }
    protected function add_image_to_list($baseurl, $url, &$list) {
        if (empty($list['list'])) {
            $list['list'] = array();
        }
        $src = url_to_absolute($baseurl, htmlspecialchars_decode($url, ENT_COMPAT));
        foreach ($list['list'] as $image) {
            if ($image['source'] == $src) {
                return;
            }
        }
        $list['list'][] = array(
            'title'=>$this->guess_filename($url, ''),
            'source'=>$src,
            'thumbnail'=>$src,
            'thumbnail_height'=>84,
            'thumbnail_width'=>84
        );
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

    /**
     * Escapes a url by replacing spaces with %20.
     *
     * Note: In general moodle does not automatically escape urls, but for the purposes of making this plugin more user friendly
     * and make it consistent with some other areas in moodle (such as mod_url), urls will automatically be escaped.
     *
     * If moodle_url or PARAM_URL is changed to clean characters that need to be escaped, then this function can be removed
     *
     * @param string $url An unescaped url.
     * @return string The escaped url
     */
    protected function escape_url($url) {
        $url = str_replace('"', '%22', $url);
        $url = str_replace('\'', '%27', $url);
        $url = str_replace(' ', '%20', $url);
        $url = str_replace('<', '%3C', $url);
        $url = str_replace('>', '%3E', $url);
        return $url;
    }

    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }

    /**
     * Return the source information
     *
     * @param stdClass $url
     * @return string|null
     */
    public function get_file_source_info($url) {
        return $url;
    }

    /**
     * file types supported by url downloader plugin
     *
     * @return array
     */
    public function supported_filetypes() {
        return array('web_image');
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
