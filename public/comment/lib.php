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
 * Functions and classes for commenting
 *
 * @package   core_comment
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 5.1, please use core_comment\manager and core_comment\comment_exception instead.
 * @todo Remove this file in Moodle 6.0 (MDL-86257)
 */

// Nothing to do here, both \comment and \comment_exception will be autoloaded by the legacyclasses autoload system.
// They are fully replaced by core_comment\manager and core_comment\comment_exception respectively.
// However, we cannot add any deprecation message here as this file is autoloaded by
// the before_standard_top_of_body_html_generation hook.
