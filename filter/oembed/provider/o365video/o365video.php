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
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace filter_oembed\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * oEmbed provider implementation for Docs.com
 */
class o365video extends provider {

    /**
     * Constructor.
     * @param $data JSON decoded array or a data object containing all provider data.
     */
    public function __construct($data = null) {
        if ($data === null) {
            $data = [
                'providername' => 'Office365 Video',
                'providerurl' => '',
                'endpoints' => [],
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

        $newtext = '';
        $odburl = get_config('local_o365', 'odburl');
        if (!empty($odburl)) {
            $odburl = preg_replace('/^https?:\/\//', '', $odburl);
            $odburl = preg_replace('/\/.*/', '', $odburl);
            $trimedurl = preg_replace("/-my/", "", $odburl);
            $search = '/(https?:\/\/)('.$odburl.'|'.$trimedurl.')\/(.*)/is';
            $newtext = preg_replace_callback($search, [$this, 'get_replacement'], $text);
        }
        return (empty($newtext) || ($newtext == $text)) ? false : $newtext;
    }

    /**
     * Get the replacement oembed HTML.
     *
     * @param array $matched Matched URL.
     * @return string The replacement text/HTML.
     */
    public function get_replacement($matched) {

        if (empty($matched[3])) {
            return $matched[0];
        }
        $matched[3] = preg_replace("/&amp;/", "&", $matched[3]);
        $values = array();
        parse_str($matched[3], $values);
        if (empty($values['chid']) || empty($values['vid'])) {
            return $matched[0];
        }
        if (!\local_o365\rest\sharepoint::is_configured()) {
            \local_o365\utils::debug('filter_oembed share point is not configured', 'filter_oembed_o365videocallback');
            return $matched[0];
        }
        try {
            $spresource = \local_o365\rest\sharepoint::get_resource();
            if (!empty($spresource)) {
                $httpclient = new \local_o365\httpclient();
                $clientdata = \local_o365\oauth2\clientdata::instance_from_oidc();
                $sptoken = \local_o365\oauth2\systemtoken::instance(null, $spresource, $clientdata, $httpclient);
                if (!empty($sptoken)) {
                    $sharepoint = new \local_o365\rest\sharepoint($sptoken, $httpclient);
                    // Retrieve api url for video service.
                    $url = $sharepoint->videoservice_discover();
                    if (!empty($url)) {
                        $sharepoint->override_resource($url);
                        $width = 640;
                        if (!empty($values['width'])) {
                            $width = $values['width'];
                        }
                        $height = 360;
                        if (!empty($values['height'])) {
                            $height = $values['height'];
                        }
                        // Retrieve embed code.
                        return $sharepoint->get_video_embed_code($values['chid'], $values['vid'], $width, $height);
                    }
                }
            }
        } catch (\Exception $e) {
            \local_o365\utils::debug('filter_oembed share point execption: '.$e->getMessage(),
                'filter_oembed_o365videocallback', $e);
        }
        return $matched[0];
    }
}