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

namespace mod_subsection\courseformat;

use action_menu;
use core_courseformat\base as course_format;
use core_courseformat\output\local\content\section\controlmenu;
use core_courseformat\sectiondelegatemodule;
use mod_subsection\manager;
use renderer_base;

/**
 * Subsection plugin section delegate class.
 *
 * This class implements all the integrations needed to delegate core section logic to
 * the plugin. For a basic subsection plugin, all methods are inherited from the
 * sectiondelegatemodule class.
 *
 * @package    mod_subsection
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sectiondelegate extends sectiondelegatemodule {
}
