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
 * Extra classes needed for HTMLPurifier customisation for Moodle.
 *
 * @package    core
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL 3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Validates RTSP defined by RFC 2326
 */
class HTMLPurifier_URIScheme_rtsp extends HTMLPurifier_URIScheme {

    public $browsable = true;
    public $hierarchical = true;

    public function doValidate(&$uri, $config, $context) {
        $uri->userinfo = null;
        return true;
    }

}


/**
 * Validates RTMP defined by Adobe
 */
class HTMLPurifier_URIScheme_rtmp extends HTMLPurifier_URIScheme {

    public $browsable = false;
    public $hierarchical = true;

    public function doValidate(&$uri, $config, $context) {
        $uri->userinfo = null;
        return true;
    }

}


/**
 * Validates IRC defined by IETF Draft
 */
class HTMLPurifier_URIScheme_irc extends HTMLPurifier_URIScheme {

    public $browsable = true;
    public $hierarchical = true;

    public function doValidate(&$uri, $config, $context) {
        $uri->userinfo = null;
        return true;
    }

}


/**
 * Validates MMS defined by Microsoft
 */
class HTMLPurifier_URIScheme_mms extends HTMLPurifier_URIScheme {

    public $browsable = true;
    public $hierarchical = true;

    public function doValidate(&$uri, $config, $context) {
        $uri->userinfo = null;
        return true;
    }

}


/**
 * Validates Gopher defined by RFC 4266
 */
class HTMLPurifier_URIScheme_gopher extends HTMLPurifier_URIScheme {

    public $browsable = true;
    public $hierarchical = true;

    public function doValidate(&$uri, $config, $context) {
        $uri->userinfo = null;
        return true;
    }

}


/**
 * Validates TeamSpeak defined by TeamSpeak
 */
class HTMLPurifier_URIScheme_teamspeak extends HTMLPurifier_URIScheme {

    public $browsable = true;
    public $hierarchical = true;

    public function doValidate(&$uri, $config, $context) {
        $uri->userinfo = null;
        return true;
    }

}
