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
 * Welcome to the future of Moodle plugin!
 *
 * Just kidding, but isn't it much nicer to have routing? I am not
 * pretending to have designed the best API. In fact I did not try,
 * there really is not point trying to do better than many of the
 * existing libraries. But copying a whole library in this plugin
 * is just not worth it.
 *
 * Moodle core should support routing and other niceties for any
 * developer to use. Oh, and when I say 'support', I don't mean
 * rewriting a new routing engine...
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
require(__DIR__ . '/../../config.php');

\block_xp\di::get('router')->dispatch();
