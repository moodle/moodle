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

use core\url;
use core_badges\local\backpack\ob_factory;
use core_badges\achievement_credential;
use core_badges\local\backpack\ob\assertion_exporter_interface;
use core_badges\local\backpack\ob\exporter_base;

/**
 * Class that represents badge assertion to be exported to a backpack.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assertion_exporter extends exporter_base implements assertion_exporter_interface {
    /** @var achievement_credential Issued badge */
    private $assertion;

    /**
     * Constructs with issued badge unique hash.
     *
     * @param string $hash Badge unique hash.
     */
    public function __construct(
        /** @var string $hash Badge unique hash. */
        protected string $hash,
    ) {
        $this->assertion = achievement_credential::instance($this->hash);
    }

    #[\Override]
    public function export(
        bool $nested = true,
        bool $usesalt = true,
    ): array {
        // Required fields.
        $assertionurl = $this->get_json_url();
        $recipientclass = $this->get_exporter_class('recipient_exporter');
        $data = [
            '@context' => OPEN_BADGES_V2_CONTEXT,
            'type' => OPEN_BADGES_V2_TYPE_ASSERTION,
            'id' => $assertionurl->out(false),
            'recipient' => (new $recipientclass($this->assertion->get_email()))->export($usesalt),
            'verify' => [
                'type' => 'hosted', // Signed is not implemented yet.
                'url' => $assertionurl->out(false),
            ],
            'issuedOn' => date('c', $this->assertion->get_dateissued()),
        ];

        $badgeexporter = ob_factory::create_badge_exporter_from_id(
            $this->assertion->get_badge_id(),
            $this->get_version_from_namespace(),
        );
        if ($nested) {
            $data['badge'] = $badgeexporter->export($nested);
        } else {
            // When no nested badge information is needed, we just return the URL to the badge JSON.
            $data['badge'] = $badgeexporter->get_json_url()->out(false);
        }

        // Evidence URL.
        $badgeurl = new url('/badges/badge.php', ['hash' => $this->assertion->get_hash()]);
        $data['evidence'] = $badgeurl->out(false);

        // Optional fields.
        if ($this->assertion->get_dateexpire()) {
            $data['expires'] = $this->assertion->get_dateexpire() ? date('c', $this->assertion->get_dateexpire()) : null;
        }

        // Add tags.
        $tags = $this->assertion->get_tags();
        if (is_array($tags) && count($tags) > 0) {
            $data['tags'] = $tags;
        }

        return $data;
    }

    #[\Override]
    public function is_revoked(): bool {
        return false;
    }

    #[\Override]
    public function get_json_url(): url {
        return new url(
            '/badges/json/assertion.php',
            [
                'b' => $this->hash,
                'obversion' => $this->get_version_from_namespace(),
            ]
        );
    }
}
