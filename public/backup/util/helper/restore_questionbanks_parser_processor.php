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

defined('MOODLE_INTERNAL' || die());

require_once($CFG->dirroot . '/backup/util/xml/parser/processors/grouped_parser_processor.class.php');

/**
 * Parse and store activity data for activities that publish questions.
 *
 * @package   core
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_questionbanks_parser_processor extends grouped_parser_processor {
    /**
     * Store the restore ID and register paths.
     *
     * @param string $restoreid ID of the backup being restored.
     */
    public function __construct(
        /** @var string ID of the backup being restored */
        protected string $restoreid,
    ) {
        parent::__construct();
        $this->add_path('/activity');
    }

    #[\Override]
    protected function dispatch_chunk($data): void {
        // Recieved one chunk, store the context ID as that's what we will match question categories against.
        $itemid = $data['tags']['contextid'];
        restore_dbops::set_backup_ids_record($this->restoreid, 'questionbank', $itemid);
    }

    #[\Override]
    protected function notify_path_start($path) {
        // Nothing to do.
    }

    #[\Override]
    protected function notify_path_end($path) {
        // Nothing to do.
    }
}
