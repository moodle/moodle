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
 * oEmbed provider implementation for ISSUU
 */
class issuu extends provider {

    /**
     * Constructor.
     * @param $data JSON decoded array or a data object containing all provider data.
     */
    public function __construct($data = null) {
        if ($data === null) {
            $data = [
                'providername' => 'ISSUU',
                'providerurl' => 'https://issuu.com',
                'endpoints' => [
                    ['schemes' => ['https://issuu.com/*'],
                    'url' => 'https://issuu.com/oembed'],
                ],
            ];
        }
        parent::__construct($data);
    }
}
