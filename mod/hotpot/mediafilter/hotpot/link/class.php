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
 * mod/hotpot/mediafilter/hotpot/link/class.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * hotpot_mediaplayer_link
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_mediaplayer_link extends hotpot_mediaplayer {
    public $aliases = array('a');
    public $options = array(
        'width' => 0, 'height' => 0, 'build' => 0,
        'quality' => '', 'majorversion' => '', 'flashvars' => ''
    );
    public $spantext = '';
    public $removelink = false;
    public $media_filetypes = array('...'); // 'htm','html','pdf'

    /**
     * generate
     *
     * @param xxx $filetype
     * @param xxx $link
     * @param xxx $mediaurl
     * @param xxx $options
     * @return xxx
     */
    function generate($filetype, $link, $mediaurl, $options)  {
        $a = '<a href="'.$mediaurl.'"';
        if (array_key_exists('player', $options)) {
            unset($options['player']);
        }
        if (array_key_exists('onclick', $options)) {
            $a .= ' onclick="'.$options['onclick'].'"';
            unset($options['onclick']);
        } else {
            $a .= ' target="_blank"';
        }
        if (array_key_exists('text', $options)) {
            $text = $options['text'];
            unset($options['text']);
        } else {
            $text = $mediaurl;
        }
        foreach ($options as $name => $value) {
            if ($value) {
                $a .= ' '.$name.'="'.$value.'"';
            }
        }
        return $a.'>'.$text.'</a>';
    }
}
