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
 * @author Aashay Zajriya<aashay@introp.net>
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2016 onwards Microsoft Open Technologies, Inc. (http://msopentech.com/)
 */

namespace filter_oembed\provider;

defined('MOODLE_INTERNAL') || die();

/**
 * oEmbed provider implementation for Microsoft Forms
 */
class officeforms extends provider {

    /**
     * Constructor.
     * @param $data JSON decoded array or a data object containing all provider data.
     */
    public function __construct($data = null) {
        if ($data === null) {
            $data = [
                'providername' => 'Office Forms',
                'providerurl' => 'https://forms.office.com/',
                'endpoints' => [
                    ['schemes' => ['https://forms.office.com/Pages/ResponsePage.aspx?id=*',
                                   'https://www.forms.office.com/Pages/ResponsePage.aspx?id=*'],
                     'url' => 'https://forms.office.com/Pages/ResponsePage.aspx?id=*&embed=true',
                     'formats' => ['json']
                    ]
                ]
            ];
        }
        parent::__construct($data);
    }

    /**
     * Get the replacement oembed HTML.
     *
     * @param array $matched Matched URL.
     * @return string The replacement text/HTML.
     */
    public function get_replacement($matched) {
        if (!empty($matched) && !empty($matched[1])) {
            $url = 'https://forms.office.com/Pages/ResponsePage.aspx?id='.$matched[1].'&embed=true';
            $embedhtml = $this->getembedhtml($url);
            return $embedhtml;
        }
        return $matched[0];
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
        $search = '/(?:https?:\/\/(?:www\.)?)(?:forms\.office\.com)\/(?:.+?)\/(?:DesignPage\.aspx)#FormId=(.+)/is';
        $newtext = preg_replace_callback($search, [$this, 'get_replacement'], $text);
        return (empty($newtext) || ($newtext == $text)) ? false : $newtext;
    }

    /**
     * Return the HTML content to be embedded.
     *
     * @param string $embedurl Additional parameters to include in the embed URL.
     * @return string The HTML content to be embedded in the page.
     */
    private function getembedhtml($embedurl) {
        $iframeattrs = [
            'src' => $embedurl,
            'height' => '768px',
            'width' => '99%',
            'frameborder' => '0',
            'marginwidth' => '0',
            'marginheight' => '0',
            'style' => 'border: none; max-width: 100%; max-height: 100vh',
            'allowfullscreen' => 'true',
            'webkitallowfullscreen' => 'true',
            'mozallowfullscreen' => 'true',
            'msallowfullscreen' => 'true',
        ];
        return \html_writer::tag('iframe', ' ', $iframeattrs);
    }
}
