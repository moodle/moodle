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

namespace core_badges\local\backpack\ob\v2p0;

/**
 * Class that represents revoked badge assertion to be exported to a backpack.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class revoked_assertion_exporter extends assertion_exporter {
    /**
     * Constructs with issued badge unique hash.
     *
     * @param string $hash Badge unique hash.
     */
    public function __construct(
        /** @var string $hash Badge unique hash. */
        protected string $hash,
    ) {
    }

    #[\Override]
    public function export(
        bool $nested = true,
        bool $usesalt = true,
    ): array {
        // Required fields.
        $data = [
            'id' => $this->get_json_url()->out(false),
            'revoked' => true,
        ];

        return $data;
    }

    #[\Override]
    public function is_revoked(): bool {
        return true;
    }
}
