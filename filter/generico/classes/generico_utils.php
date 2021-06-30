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
 * @package    filter
 * @subpackage generico
 * @copyright  2014 Justin Hunt <poodllsupport@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_generico;

defined('MOODLE_INTERNAL') || die;

class generico_utils {
    const FILTER_GENERICO_TEMPLATE_COUNT = 20;

    const CLOUDPOODLL_VAL_BY_REGCODE = 1;
    const CLOUDPOODLL_VAL_BY_APICREDS = 2;
    const CLOUDPOODLL_IS_REGISTERED = 1;
    const CLOUDPOODLL_IS_UNREGISTERED = 0;
    const CLOUDPOODLL_IS_EXPIRED = 2;

    public static function fetch_emptyproparray() {
        $proparray = array();
        $proparray['AUTOID'] = '';
        $proparray['CSSLINK'] = '';
        return $proparray;
    }

    /**
     * Return an array of variable names
     *
     * @param string template containing @@variable@@ variables
     * @return array of variable names parsed from template string
     */
    public static function fetch_variables($template) {
        $matches = array();
        $t = preg_match_all('/@@(.*?)@@/s', $template, $matches);
        if (count($matches) > 1) {
            return ($matches[1]);
        } else {
            return array();
        }
    }

    public static function fetch_filter_properties($filterstring) {
        //lets do a general clean of all input here
        //see: https://github.com/justinhunt/moodle-filter_generico/issues/7
        $filterstring = clean_param($filterstring, PARAM_TEXT);

        //this just removes the {GENERICO: .. }
        $rawproperties = explode("{GENERICO:", $filterstring);
        //here we remove any html tags we find. They should not be in here
        $rawproperties = $rawproperties[1];
        $rawproperties = explode("}", $rawproperties);
        //here we remove any html tags we find. They should not be in here
        //and we return the guts of the filter string for parsing
        $rawproperties = strip_tags($rawproperties[0]);

        //Now we just have our properties string
        //Lets run our regular expression over them
        //string should be property=value,property=value
        //got this regexp from http://stackoverflow.com/questions/168171/regular-expression-for-parsing-name-value-pairs
        $regexpression = '/([^=,]*)=("[^"]*"|[^,"]*)/';
        $matches = array();

        //here we match the filter string and split into name array (matches[1]) and value array (matches[2])
        //we then add those to a name value array.
        $itemprops = array();
        if (preg_match_all($regexpression, $rawproperties, $matches, PREG_PATTERN_ORDER)) {
            $propscount = count($matches[1]);
            for ($cnt = 0; $cnt < $propscount; $cnt++) {
                // echo $matches[1][$cnt] . "=" . $matches[2][$cnt] . " ";
                $newvalue = $matches[2][$cnt];
                //this could be done better, I am sure. WE are removing the quotes from start and end
                //this wil however remove multiple quotes id they exist at start and end. NG really
                $newvalue = trim($newvalue, '"');
                $itemprops[trim($matches[1][$cnt])] = $newvalue;
            }
        }
        return $itemprops;
    }

    /**
     * Returns URL to the stored file via pluginfile.php.
     *
     * theme revision is used instead of the itemid.
     *
     * @param string $setting
     * @param string $filearea
     * @return string protocol relative URL or null if not present
     */
    public static function setting_file_url($filepath, $filearea) {
        global $CFG;

        $component = 'filter_generico';
        $itemid = 0;
        $syscontext = \context_system::instance();

        $url = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                "/$syscontext->id/$component/$filearea/$itemid" . $filepath);

        // Now this is tricky because the we can not hardcode http or https here, lets use the relative link.
        // Note: unfortunately moodle_url does not support //urls yet.
        // $url = preg_replace('|^https?://|i', '//', $url->out(false));
        return $url;
    }

    public static function setting_file_serve($filearea, $args, $forcedownload, $options) {
        global $CFG;
        require_once("$CFG->libdir/filelib.php");

        $syscontext = \context_system::instance();
        $component = 'filter_generico';

        $revision = array_shift($args);
        if ($revision < 0) {
            $lifetime = 0;
        } else {
            $lifetime = 60 * 60 * 24 * 60;
        }

        $fs = get_file_storage();
        $relativepath = implode('/', $args);

        $fullpath = "/{$syscontext->id}/{$component}/{$filearea}/0/{$relativepath}";
        $fullpath = rtrim($fullpath, '/');
        if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
            send_stored_file($file, $lifetime, 0, $forcedownload, $options);
            return true;
        } else {
            send_file_not_found();
        }
    }

    public static function update_revision() {
        set_config('revision', time(), 'filter_generico');
    }

    protected function check_registered_url($theurl, $wildcardok = true) {
        global $CFG;

        //get arrays of the wwwroot and registered url
        //just in case, lowercase'ify them
        $thewwwroot = strtolower($CFG->wwwroot);
        $theregisteredurl = strtolower($theurl);
        $theregisteredurl = trim($theregisteredurl);

        //add http:// or https:// to URLs that do not have it
        if (strpos($theregisteredurl, 'https://') !== 0 &&
                strpos($theregisteredurl, 'http://') !== 0) {
            $theregisteredurl = 'https://' . $theregisteredurl;
        }

        //if neither parsed successfully, thats a no straight up
        $wwwroot_bits = parse_url($thewwwroot);
        $registered_bits = parse_url($theregisteredurl);
        if (!$wwwroot_bits || !$registered_bits) {
            return self::CLOUDPOODLL_IS_UNREGISTERED;
        }

        //get the subdomain widlcard address, ie *.a.b.c.d.com
        $wildcard_subdomain_wwwroot = '';
        if (array_key_exists('host', $wwwroot_bits)) {
            $wildcardparts = explode('.', $wwwroot_bits['host']);
            $wildcardparts[0] = '*';
            $wildcard_subdomain_wwwroot = implode('.', $wildcardparts);
        } else {
            return self::CLOUDPOODLL_IS_UNREGISTERED;
        }

        //match either the exact domain or the wildcard domain or fail
        if (array_key_exists('host', $registered_bits)) {
            //this will cover exact matches and path matches
            if ($registered_bits['host'] === $wwwroot_bits['host']) {
                $this->validated = true;
                return self::CLOUDPOODLL_IS_REGISTERED;
                //this will cover subdomain matches but only for institution bigdog and enterprise license
            } else if (($registered_bits['host'] === $wildcard_subdomain_wwwroot) && $wildcardok) {
                //yay we are registered!!!!
                return self::CLOUDPOODLL_IS_REGISTERED;
            } else {
                return self::CLOUDPOODLL_IS_UNREGISTERED;
            }
        } else {
            return self::CLOUDPOODLL_IS_UNREGISTERED;
        }
    }

    //This is called from the settings page and we do not want to make calls out to cloud.poodll.com on settings
    //page load, for performance and stability issues. So if the cache is empty and/or no token, we just show a
    //"refresh token" link
    public function fetch_token_for_display($apiuser, $apisecret) {
        global $CFG;

        //First check that we have an API id and secret
        //refresh token
        $refresh = \html_writer::link($CFG->wwwroot . '/filter/generico/refreshtoken.php',
                        get_string('refreshtoken', constants::MOD_FRANKY)) . '<br>';

        $message = '';
        $apiuser = trim($apiuser);
        $apisecret = trim($apisecret);
        if (empty($apiuser)) {
            $message .= get_string('noapiuser', constants::MOD_FRANKY) . '<br>';
        }
        if (empty($apisecret)) {
            $message .= get_string('noapisecret', constants::MOD_FRANKY);
        }

        if (!empty($message)) {
            return $refresh . $message;
        }

        //Fetch from cache and process the results and display
        $cache = \cache::make_from_params(\cache_store::MODE_APPLICATION, constants::MOD_FRANKY, 'token');
        $tokenobject = $cache->get('recentpoodlltoken');

        //if we have no token object the creds were wrong ... or something
        if (!($tokenobject)) {
            $message = get_string('notokenincache', constants::MOD_FRANKY);
            //if we have an object but its no good, creds werer wrong ..or something
        } else if (!property_exists($tokenobject, 'token') || empty($tokenobject->token)) {
            $message = get_string('credentialsinvalid', constants::MOD_FRANKY);
            //if we do not have subs, then we are on a very old token or something is wrong, just get out of here.
        } else if (!property_exists($tokenobject, 'subs')) {
            $message = 'No subscriptions found at all';
        }
        if (!empty($message)) {
            return $refresh . $message;
        }

        //we have enough info to display a report. Lets go.
        foreach ($tokenobject->subs as $sub) {
            $sub->expiredate = date('d/m/Y', $sub->expiredate);
            $message .= get_string('displaysubs', constants::MOD_FRANKY, $sub) . '<br>';
        }
        //Is site authorised
        $haveauthsite = false;
        foreach ($tokenobject->sites as $site) {
            if ($this->check_registered_url($site) == self::CLOUDPOODLL_IS_REGISTERED) {
                $haveauthsite = true;
                break;
            }
        }
        if (!$haveauthsite) {
            $message .= get_string('appnotauthorised', constants::MOD_FRANKY) . '<br>';
        } else {

            //Is app authorised
            if (in_array(constants::MOD_FRANKY, $tokenobject->apps)) {
                $message .= get_string('appauthorised', constants::MOD_FRANKY) . '<br>';
            } else {
                $message .= get_string('appnotauthorised', constants::MOD_FRANKY) . '<br>';
            }
        }
        return $refresh . $message;
    }

    //We need a Poodll token to make cloudpoodll happen
    public static function fetch_token($apiuser, $apisecret, $force = false) {
        $cache = \cache::make_from_params(\cache_store::MODE_APPLICATION, constants::MOD_FRANKY, 'token');
        $tokenobject = $cache->get('recentpoodlltoken');
        $tokenuser = $cache->get('recentpoodlluser');
        $apiuser = trim($apiuser);
        $apisecret = trim($apisecret);

        //if we got a token and its less than expiry time
        // use the cached one
        if ($tokenobject && $tokenuser && $tokenuser == $apiuser && !$force) {
            if ($tokenobject->validuntil == 0 || $tokenobject->validuntil > time()) {
                return $tokenobject->token;
            }
        }

        // Send the request & save response to $resp
        $token_url = "https://cloud.poodll.com/local/cpapi/poodlltoken.php";
        $postdata = array(
                'username' => $apiuser,
                'password' => $apisecret,
                'service' => 'cloud_poodll');
        $token_response = self::curl_fetch($token_url, $postdata);
        if ($token_response) {
            $resp_object = json_decode($token_response);
            if ($resp_object && property_exists($resp_object, 'token')) {
                $token = $resp_object->token;
                //store the expiry timestamp and adjust it for diffs between our server times
                if ($resp_object->validuntil) {
                    $validuntil = $resp_object->validuntil - ($resp_object->poodlltime - time());
                    //we refresh one hour out, to prevent any overlap
                    $validuntil = $validuntil - (1 * HOURSECS);
                } else {
                    $validuntil = 0;
                }

                //make sure the token has all the bits in it we expect before caching it
                $tokenobject = $resp_object;//new \stdClass();
                $tokenobject->validuntil = $validuntil;
                if (!property_exists($tokenobject, 'subs')) {
                    $tokenobject->subs = false;
                }
                if (!property_exists($tokenobject, 'apps')) {
                    $tokenobject->apps = false;
                }
                if (!property_exists($tokenobject, 'sites')) {
                    $tokenobject->sites = false;
                }
                $cache->set('recentpoodlltoken', $tokenobject);
                $cache->set('recentpoodlluser', $apiuser);

            } else {
                $token = false;
                if ($resp_object && property_exists($resp_object, 'error')) {
                    //ERROR = $resp_object->error
                }
            }
        } else {
            $token = false;
        }
        return $token;
    }

    //we use curl to fetch transcripts from AWS and Tokens from cloudpoodll
    //this is our helper
    public static function curl_fetch($url, $postdata) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        $curl = new \curl();

        $result = $curl->get($url, $postdata);
        return $result;
    }
}