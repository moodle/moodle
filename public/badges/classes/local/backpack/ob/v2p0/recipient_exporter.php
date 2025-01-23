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

use core_badges\local\backpack\ob\exporter_base;
use core_badges\local\backpack\ob\recipient_exporter_interface;

/**
 * Class that represents recipient to be exported to a backpack.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class recipient_exporter extends exporter_base implements recipient_exporter_interface {
    /**
     * Constructs with issued badge unique hash.
     *
     * @param string $email Recipient's email.
     */
    public function __construct(
        /** @var stdClass Recipient's email. */
        private string $email,
    ) {
    }

    #[\Override]
    public function export(
        bool $usesalt = true
    ): array {
        global $CFG;

        $data = [
            'type' => 'email', // Currently the only supported type.
            'hashed' => true, // Recipient is always hashed.
        ];
        if ($usesalt) {
            $data['salt'] = $CFG->badges_badgesalt;
            $data['identity'] = 'sha256$' . hash('sha256', $this->email . $CFG->badges_badgesalt);
        } else {
            $data['identity'] = $this->email;
        }

        return $data;
    }
}
