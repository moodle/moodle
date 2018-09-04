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
 * mod/hotpot/mediafilter/hotpot/image/class.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * hotpot_mediaplayer_image
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_mediaplayer_image extends hotpot_mediaplayer {
    public $aliases = array('img');
    public $media_filetypes = array('gif','jpg','png');
    public $options = array(
        'width' => 0, 'height' => 0, 'build' => 0,
        'quality' => '', 'majorversion' => '', 'flashvars' => ''
    );
    public $spantext = '';
    public $removelink = false;

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
        $img = '<img src="'.$mediaurl.'"';
        if (array_key_exists('player', $options)) {
            unset($options['player']);
        }
        if (! array_key_exists('alt', $options)) {
            $options['alt'] = basename($mediaurl);
        }
        foreach ($options as $name => $value) {
            if ($value) {
                $img .= ' '.$name.'="'.$value.'"';
            }
        }
        $img .= ' />';
        return $img;
    }
}
