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
 * mod/hotpot/mediafilter/hotpot/matbury_mp3_player/class.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * hotpot_mediaplayer_matbury_mp3_player
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_mediaplayer_matbury_mp3_player extends hotpot_mediaplayer {
    public $aliases = array('matbury');
    public $playerurl = 'matbury_mp3_player/matbury_mp3_player.swf';
    public $flashvars_paramname = 'mp3url';
    public $more_options = array(
        'width' => 200, 'height' => 18, 'majorversion' => 9, 'build' => 115,
        'timesToPlay' => 1, 'showPlay' => 'true', 'waitToPlay' => 'true'
    );
    public $flashvars = array(
        'timesToPlay' => PARAM_INT, 'showPlay' => PARAM_ALPHANUM, 'waitToPlay' => PARAM_ALPHANUM
    );
}
