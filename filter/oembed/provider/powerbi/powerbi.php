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
 * @package filter_oembed
 * @author Sushant Gawali <sushant@introp.net>
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace filter_oembed\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * oEmbed provider implementation for Docs.com
 */
class powerbi extends provider {

    /**
     * Constructor.
     * @param $data JSON decoded array or a data object containing all provider data.
     */
    public function __construct($data = null) {
        if ($data === null) {
            $data = [
                'providername' => 'Power BI',
                'providerurl' => '',
                'endpoints' => [
                    ['schemes' => ['https://powerbi.com/*/*/*/*/*',
                                   'https://app.powerbi.com/*/*/*/*/*'],
                     'url' => '',
                     'formats' => ['json']
                    ]
                ]
            ];
        }
        parent::__construct($data);
    }

    /**
     * Main filter function. This should only be used by subplugins, and it is preferable
     * to not use it even then. Ideally, a provider plugin should provide a JSON oembed provider
     * response (http://oembed.com/#section2.3) and let the main filter handle the HTML. Use this
     * only if the HTML must be determined by the plugin. If implemented, ensure FALSE is returned
     * if no filtering occurred.
     *
     * @param string $text Incoming text.
     * @return string Filtered text, or false for no changes.
     */
    public function filter($text) {
        // PowerBI depends on 'local_o365' installed. If it isn't, return false.
        if (\core_plugin_manager::instance()->get_plugin_info('local_o365') == null) {
            return false;
        }

        $search = '/(https?:\/\/(app\.)?)(powerbi\.com)\/(.+?)\/(.+?)\/(.+?)\/(.+?)\/(.+?)/is';
        $newtext = preg_replace_callback($search, [$this, 'get_replacement'], $text);
        return (empty($newtext) || ($newtext == $text)) ? false : $newtext;
    }

    /**
     * Get the replacement oembed HTML.
     *
     * @param array $matched Matched URL.
     * @return string The replacement text/HTML.
     */
    public function get_replacement($matched) {
        global $CFG;
        require_once($CFG->dirroot . '/filter/oembed/provider/powerbi/rest/powerbi.php');

        $httpclient = new \local_o365\httpclient();
        try {
            $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc();
            $resource = \filter_oembed\provider\powerbi\rest\powerbi::get_resource();
            $token = \local_o365\oauth2\systemtoken::instance(null, $resource, $clientdata, $httpclient);
            if (!empty($token)) {
                $powerbi = new \filter_oembed\provider\powerbi\rest\powerbi($token, $httpclient);
                if ($matched[6] == 'reports') {
                    $reportsdata = $powerbi->apicall('get', 'reports');
                    $embedurl = $powerbi->getreportoembedurl($matched[7], $reportsdata);
                    $embedhtml = $this->getembedhtml($embedurl);
                    $embedhtml .= '<input type="hidden" class="token" value="' . $token->get_token(). '">';
                    return $embedhtml;
                }
            }
        } catch (\Exception $e) {
            \local_o365\utils::debug('filter_oembed oauth2 exeception: '.$e->getMessage(), 'filter_oembed_powerbicallback', $e);
        }
        return $matched[0];
    }

    private function getembedhtml($embedurl) {
        return '<iframe class="powerbi_iframe" src="'. $embedurl . '" '
                . 'height="768px" width="99%" frameborder="0" seamless></iframe>';
    }
}
