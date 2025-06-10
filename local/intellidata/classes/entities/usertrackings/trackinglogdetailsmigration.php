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
 * Class for migration Tracking Log Details.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2021 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\usertrackings;

/**
 * Class for migration Tracking Log Details.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2021 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class trackinglogdetailsmigration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\usertrackings\trackinglogdetail';
    /** @var string */
    public $table = 'local_intellidata_trdetails';
}
