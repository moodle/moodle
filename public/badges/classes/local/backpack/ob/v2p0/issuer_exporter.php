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
use core_badges\badge;
use core_badges\local\backpack\ob\exporter_base;
use core_badges\local\backpack\ob\issuer_exporter_interface;

/**
 * Class that represents issuer to be exported to a backpack.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class issuer_exporter extends exporter_base implements issuer_exporter_interface {
    /** @var string The issuer name */
    private $name;

    /** @var string The issuer url */
    private $url;

    /** @var string The issuer email */
    private $email;

    /**
     * Constructs with badge identifier.
     *
     * @param int|null $badgeid Badge identifier. If null, the default issuer for the site will be used.
     */
    public function __construct(
        /** @var int|null Badge identifier. */
        private ?int $badgeid,
    ) {
        global $CFG, $SITE;

        if (empty($this->badgeid)) {
            // Get the default issuer for this site.
            $sitebackpack = badges_get_site_primary_backpack();
            $this->name = $CFG->badges_defaultissuername ?: ($SITE->fullname ? $SITE->fullname : $SITE->shortname);
            $this->url = (new url('/'))->out(false);
            $this->email = $sitebackpack->backpackemail ?: $CFG->badges_defaultissuercontact;
        } else {
            $badge = new badge($this->badgeid);
            $this->name = $badge->issuername;
            $this->url = $badge->issuerurl;
            $this->email = $badge->issuercontact;
        }
    }

    #[\Override]
    public function export(): array {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'email' => $this->email,
            '@context' => OPEN_BADGES_V2_CONTEXT,
            'id' => $this->get_json_url()->out(false),
            'type' => OPEN_BADGES_V2_TYPE_ISSUER,
        ];
    }

    #[\Override]
    public function get_json_url(): url {
        $params = [
            'obversion' => $this->get_version_from_namespace(),
        ];
        if ($this->badgeid) {
            $params['badgeid'] = $this->badgeid;
        }
        return new url(
            '/badges/json/issuer.php',
            $params,
        );
    }
}
