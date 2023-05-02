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
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace filter_oembed\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * oEmbed provider implementation for Docs.com
 */
class docsdotcom extends provider {

    /**
     * Constructor.
     * @param $data JSON decoded array or a data object containing all provider data.
     */
    public function __construct($data = null) {
        if ($data === null) {
            $data = [
                'providername' => 'Docs',
                'providerurl' => 'https://docs.com',
                'endpoints' => [
                    ['schemes' => ['https://docs.com/*', 'https://www.docs.com/*'],
                     'url' => 'https:\/\/docs.com\/api\/oembed',
                     'formats' => ['json']
                    ]
                ]
            ];
        }
        parent::__construct($data);
    }

    /**
     * If a matching endpoint scheme is found in the passed text, return a consumer request URL.
     *
     * @param string $text The text to look for an URL resource using provider's schemes.
     * @return string Consumer request URL.
     */
    public function get_oembed_request($text) {
        $requesturl = '';
        // Get the regex arrauy to look for matching schemes.
        $regex = $this->endpoints_regex(new endpoint());
        if (preg_match($regex, $text, $matched)) {
            $params = [
                'url' => $matched[1]. $matched[3] . '/' . $matched[4] . '/' . $matched[5] . '/' . $matched[6],
                'format' => 'json',
                'maxwidth' => '600',
                'maxheight' => '400',
            ];
            $oembedurl = new \moodle_url('https://docs.com/api/oembed', $params);
            $requesturl = $oembedurl->out(false);
        }
        return $requesturl;
    }

    /**
     * Return a regular expression that can be used to search text for an endpoint's schemes.
     *
     * @param endpoint $endpoint
     * @return array Array of regular expressions matching all endpoints and schemes.
     */
    protected function endpoints_regex(endpoint $endpoint) {
        return '/(https?:\/\/(www\.)?)(docs\.com)\/(.+?)\/(.+?)\/(.+?)/is';
    }
}
