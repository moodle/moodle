<?php

/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage lib
 * @author     Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 *
 * Customised version of SimplePie for Moodle
 */

require_once($CFG->libdir.'/filelib.php');

// PLEASE NOTE: we use the simplepie class _unmodified_
// through the joys of OO. Distros are free to use their stock
// version of this file.
require_once($CFG->libdir.'/simplepie/simplepie.inc');

/**
 * Moodle Customised version of the SimplePie class
 *
 * This class extends the stock SimplePie class
 * in order to make sensible configuration choices,
 * such as using the Moodle cache directory and
 * curl functions/proxy config  for making http
 * requests.
 */
class moodle_simplepie extends SimplePie
{
    function __construct($feed_url = null){
        global $CFG;

        // Use the Moodle class for http requests
        $this->file_class = 'moodle_simplepie_file';

        // Use sensible cache directory
        $cachedir = $CFG->dataroot.'/cache/simplepie/';
        if (!file_exists($cachedir)){
            mkdir($cachedir, 0777, true);
        }

        parent::__construct($feed_url, $cachedir);
        parent::set_output_encoding('UTF-8');
    }
}

/**
 * Moodle Customised version of the SimplePie_File class
 *
 * This class extends the stock SimplePie class
 * in order to utilise Moodles own curl class for making
 * http requests. By using the moodle curl class
 * we ensure that the correct proxy configuration is used.
 */
class moodle_simplepie_file extends SimplePie_File
{

    /**
     * The contructor is a copy of the stock simplepie File class which has
     * been modifed to add in use the Moodle curl class rather than php curl
     * functions.
     */
    function moodle_simplepie_file($url, $timeout = 10, $redirects = 5, $headers = null, $useragent = null, $force_fsockopen = false)
    {
        $this->url = $url;
        $this->method = SIMPLEPIE_FILE_SOURCE_REMOTE | SIMPLEPIE_FILE_SOURCE_CURL;

        $curl = new curl();
        $curl->setopt(array('CURLOPT_HEADER'=>true));

        try{
            $this->headers = $curl->get($url);
        }catch(moodle_exception $e){
            $this->error = 'cURL Error: '.$curl->error;
            $this->success = false;
            return false;
        }

        $parser =& new SimplePie_HTTP_Parser($this->headers);

        if ($parser->parse()){
            $this->headers = $parser->headers;
            $this->body = $parser->body;
            $this->status_code = $parser->status_code;


            if (($this->status_code == 300 || $this->status_code == 301 || $this->status_code == 302 || $this->status_code == 303 || $this->status_code == 307 || $this->status_code > 307 && $this->status_code < 400) && isset($this->headers['location']) && $this->redirects < $redirects)
            {
                $this->redirects++;
                $location = SimplePie_Misc::absolutize_url($this->headers['location'], $url);
                return $this->SimplePie_File($location, $timeout, $redirects, $headers);
            }
        }
    }
}
