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

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/sdk/api.class.php');
require_once(__DIR__.'/turnitintooltwo_perflog.class.php');

class turnitintooltwo_comms {

    private $tiiaccountid;
    private $tiiapiurl;
    private $tiisecretkey;
    private $tiiintegrationid;
    private $diagnostic;
    private $langcode;

    public function __construct($accountid = null, $accountshared = null, $url = null) {
        $config = turnitintooltwo_admin_config();

        $tiiapiurl = (substr($config->apiurl, -1) == '/') ? substr($config->apiurl, 0, -1) : $config->apiurl;

        $this->tiiintegrationid = 12;
        $this->tiiaccountid = is_null($accountid) ? $config->accountid : $accountid;
        $this->tiiapiurl = is_null($url) ? $tiiapiurl : $url;
        $this->tiisecretkey = is_null($accountshared) ? $config->secretkey : $accountshared;

        if (empty($this->tiiaccountid) || empty($this->tiiapiurl) || empty($this->tiisecretkey)) {
            turnitintooltwo_print_error( 'configureerror', 'turnitintooltwo' );
        }

        $this->diagnostic = $config->enablediagnostic;
        $this->langcode = $this->get_lang();
    }

    /**
     * Initialise the API object
     *
     * @return object \APITurnitin
     */
    public function initialise_api( $testingconnection = false ) {
        global $CFG, $tiipp;

        $api = new TurnitinAPI($this->tiiaccountid, $this->tiiapiurl, $this->tiisecretkey,
                                $this->tiiintegrationid, $this->langcode);
        // Enable logging if diagnostic mode is turned on.
        if ($this->diagnostic) {
            $api->setLogPath($CFG->tempdir.'/turnitintooltwo/logs/');
        }

        // Use Moodle's proxy settings if specified.
        if (!empty($CFG->proxyhost)) {
            $api->setProxyHost($CFG->proxyhost);
        }

        if (!empty($CFG->proxyport)) {
            $api->setProxyPort($CFG->proxyport);
        }

        if (!empty($CFG->proxyuser)) {
            $api->setProxyUser($CFG->proxyuser);
        }

        if (!empty($CFG->proxypassword)) {
            $api->setProxyPassword($CFG->proxypassword);
        }

        if (!empty($CFG->proxytype)) {
            $api->setProxyType($CFG->proxytype);
        }

        if (!empty($CFG->proxybypass)) {
            $api->setProxyBypass($CFG->proxybypass);
        }

        $pluginversion = turnitintooltwo_get_version();
        $api->setIntegrationVersion($CFG->version);
        $api->setPluginVersion($pluginversion);

        if (is_readable("$CFG->dataroot/moodleorgca.crt")) {
            $certificate = realpath("$CFG->dataroot/moodleorgca.crt");
            $api->setSSLCertificate($certificate);
        }

        // Offline mode provided by Androgogic.
        if (!empty($CFG->tiioffline) && !$testingconnection && empty($tiipp->in_use)) {
            turnitintooltwo_print_error('turnitintoolofflineerror', 'turnitintooltwo');
        }
        $api->setTestingConnection($testingconnection);
        $api->setPerformanceLog(new turnitintooltwo_performancelog());

        return $api;
    }

    /**
     * Log API exceptions and print error to screen if required
     *
     * @param object $e
     * @param string $tterrorstr
     * @param boolean $toscreen
     * @param boolean $embedded
     */
    public static function handle_exceptions($e, $tterrorstr = "", $toscreen = true, $embedded = false) {
        global $CFG;

        $errorstr = "";
        if (!empty($tterrorstr)) {
            $errorstr = get_string($tterrorstr, 'turnitintooltwo')."<br/><br/>";
            if ($embedded == true) {
                $errorstr .= get_string('tii_submission_failure', 'turnitintooltwo')."<br/><br/>";
            }
        }

        // Show full debugging information if developer mode is on.
        if ($CFG->debug == DEBUG_DEVELOPER) {
            if (is_callable(array($e, 'getFaultCode'))) {
                $errorstr .= get_string('faultcode', 'turnitintooltwo').": ".$e->getFaultCode()." | ";
            }

            if (is_callable(array($e, 'getFile'))) {
                $errorstr .= get_string('file').": ".$e->getFile()." | ";
            }

            if (is_callable(array($e, 'getLine'))) {
                $errorstr .= get_string('line', 'turnitintooltwo').": ".$e->getLine()." | ";
            }

            if (is_callable(array($e, 'getMessage'))) {
                $errorstr .= get_string('message', 'turnitintooltwo').": ".$e->getMessage()." | ";
            }

            if (is_callable(array($e, 'getCode'))) {
                $errorstr .= get_string('code', 'turnitintooltwo').": ".$e->getCode();
            }
        } else {
            // Only show message if full debugging is not on.
            if (is_callable(array($e, 'getMessage'))) {
                $errorstr .= get_string('message', 'turnitintooltwo').": ".$e->getMessage();
            }
        }

        turnitintooltwo_activitylog($errorstr, "API_ERROR");
        if ($toscreen) {
            turnitintooltwo_print_error($errorstr, null);
        } else if ($embedded) {
            return $errorstr;
        }
    }

    /**
     * Outputs a language code to use with the Turnitin API
     *
     * @param string $langcode The Moodle language code
     * @return string The cleaned and mapped associated Turnitin lang code
     */
    private function get_lang() {
        $langcode = str_replace("_utf8", "", current_language());
        $langarray = array(
            'en' => 'en_us',
            'en_us' => 'en_us',
            'fr' => 'fr',
            'fr_ca' => 'fr',
            'es' => 'es',
            'es_mx' => 'es',
            'de' => 'de',
            'de_du' => 'de',
            'zh_cn' => 'zh_hans',
            'zh_tw' => 'zh_tw',
            'pt_br' => 'pt_br',
            'th' => 'th',
            'ja' => 'ja',
            'ko' => 'ko',
            'ms' => 'ms',
            'tr' => 'tr',
            'ca' => 'es',
            'sv' => 'sv',
            'nl' => 'nl',
            'fi' => 'fi',
            'ar' => 'ar'
        );
        $langcode = (isset($langarray[$langcode])) ? $langarray[$langcode] : 'en_us';
        return $langcode;
    }

    /**
     * @param int $diagnostic Set diagnostic setting.
     */
    public function set_diagnostic($diagnostic) {
        $this->diagnostic = $diagnostic;
    }
}
