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

namespace auth_iomadsaml2\testing;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use coding_exception;

/**
 * Class \auth_iomadsaml2\testing\generator, required for Totara
 *
 * @package   auth_iomadsaml2
 * @author    Noemie Ariste <noemie.ariste@catalyst.net.nz>
 * @copyright Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class generator extends \core\testing\component_generator {

    use tests_generator;

    /**
     * Number of entities created
     * @var int
     */
    protected $entitiescount = 0;

}
