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

/**
 * A custom HTMLPurifier transformation. Adds rel="noreferrer" to all links with target="_blank".
 *
 * @package core
 * @copyright Cameron Ball
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class HTMLPurifier_AttrTransform_Noreferrer extends HTMLPurifier_AttrTransform {
    /** @var HTMLPurifier_URIParser $parser */
    private $parser;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->parser = new HTMLPurifier_URIParser();
    }

    /**
     * Transforms a tags such that when a target attribute is present, rel="noreferrer" is added.
     *
     * Note that this will not respect Attr.AllowedRel
     *
     * @param array $attr Assoc array of attributes, usually from
     *              HTMLPurifier_Token_Tag::$attr
     * @param HTMLPurifier_Config $config Mandatory HTMLPurifier_Config object.
     * @param HTMLPurifier_Context $context Mandatory HTMLPurifier_Context object
     * @return array Processed attribute array.
     */
    public function transform($attr, $config, $context) {
        // Nothing to do If we already have noreferrer in the rel attribute
        if (!empty($attr['rel']) && substr($attr['rel'], 'noreferrer') !== false) {
            return $attr;
        }

        // If _blank target attribute exists, add rel=noreferrer
        if (!empty($attr['target']) && $attr['target'] == '_blank') {
            $attr['rel'] = !empty($attr['rel']) ? $attr['rel'] . ' noreferrer' : 'noreferrer';
        }

        return $attr;
    }
}

/**
 * A custom HTMLPurifier module to add rel="noreferrer" attributes a tags.
 *
 * @package    core
 * @copyright  Cameron Ball
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class HTMLPurifier_HTMLModule_Noreferrer extends HTMLPurifier_HTMLModule {
    /** @var string $name */
    public $name = 'Noreferrer';

    /**
     * Module setup
     *
     * @param HTMLPurifier_Config $config
     */
    public function setup($config) {
        $a = $this->addBlankElement('a');
        $a->attr_transform_post[] = new HTMLPurifier_AttrTransform_Noreferrer();
    }
}
