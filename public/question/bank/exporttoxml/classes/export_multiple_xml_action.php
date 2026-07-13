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

namespace qbank_exporttoxml;

use core_question\local\bank\bulk_action_base;
use Override;

/**
 * Class to add the bulk action menu item
 *
 * @package    qbank_exporttoxml
 * @copyright  2026 MoodleMoot DACH
 * @author     Andreas Steiger, Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_multiple_xml_action extends bulk_action_base {
    #[\Override]
    public function get_bulk_action_title(): string {
        return get_string('exporttoxml', 'qbank_exporttoxml');
    }

    #[\Override]
    public function get_key(): string {
        return 'exportselected';
    }

    #[\Override]
    public function get_bulk_action_url(): \moodle_url {
        return new \moodle_url('/question/bank/exporttoxml/exportmany.php');
    }

    #[\Override]
    public function get_bulk_action_classes(): string {
        return '';
    }

    #[\Override]
    public function get_bulk_action_capabilities(): ?array {
        return [
            'moodle/question:viewall',
        ];
    }
}
