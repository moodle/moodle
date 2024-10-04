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
 * Generator for mod_qbank.
 * Required by the module generator but intentionally blank until we need to extend
 *
 * @package    mod_qbank
 * @copyright  2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Simon Adams <simon.adams@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_qbank_generator extends testing_module_generator {

    #[\Override]
    public function create_instance($record = null, ?array $options = null) {
        $record = (object)(array)$record;

        if (empty($record->type)) {
            $record->type = core_question\local\bank\question_bank_helper::TYPE_STANDARD;
        }
        if (isset($options['section']) && (int) $options['section'] !== 0) {
            throw new \core\exception\coding_exception(
                "Attempted to create a mod_qbank instance in section {$options['section']}. " .
                    "Question banks can only be created in section 0.",
            );
        }
        return parent::create_instance($record, $options);
    }
}
