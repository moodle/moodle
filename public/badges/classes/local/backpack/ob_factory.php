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

namespace core_badges\local\backpack;

use core_badges\local\backpack\ob\assertion_exporter_interface;
use core_badges\local\backpack\ob\badge_exporter_interface;
use core_badges\local\backpack\ob\issuer_exporter_interface;

/**
 * Factory class for Open Badges, used to decouple the construction of Open Badges related objects.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class ob_factory {
    /**
     * Create an assertion_exporter object from assertion hash based on the OB API version.
     *
     * @param string $hash Badge unique hash.
     * @param string $apiversion Open Badges API version.
     * @throws \coding_exception
     * @return \core_badges\local\backpack\ob\assertion_exporter_interface The assertion exporter object.
     */
    public static function create_assertion_exporter_from_hash(
        string $hash,
        string $apiversion,
    ): assertion_exporter_interface {

        $classname = helper::assertion_exists($hash) ? 'assertion_exporter' : 'revoked_assertion_exporter';
        $classname = '\\core_badges\\local\\backpack\\ob\\' . $apiversion . '\\' . $classname;
        if (!class_exists($classname)) {
            throw new \coding_exception('Invalid Open Badges API version');
        }

        return new $classname($hash);
    }

    /**
     * Create a badge_exporter object from badge identifier based on the OB API version.
     *
     * @param int $badgeid Badge identifier.
     * @param string $apiversion Open Badges API version.
     * @throws \coding_exception
     * @return \core_badges\local\backpack\ob\badge_exporter_interface The badge exporter object.
     */
    public static function create_badge_exporter_from_id(
        int $badgeid,
        string $apiversion,
    ): badge_exporter_interface {

        $classname = '\\core_badges\\local\\backpack\\ob\\' . $apiversion . '\\badge_exporter';
        if (!class_exists($classname)) {
            throw new \coding_exception('Invalid Open Badges API version');
        }

        return new $classname($badgeid);
    }

    /**
     * Create a badge_exporter object from assertion unique hash based on the OB API version.
     *
     * @param string $hash Badge unique hash.
     * @param string $apiversion Open Badges API version.
     * @throws \coding_exception
     * @return \core_badges\local\backpack\ob\badge_exporter_interface The badge exporter object.
     */
    public static function create_badge_exporter_from_hash(
        string $hash,
        string $apiversion,
    ): badge_exporter_interface {

        $classname = '\\core_badges\\local\\backpack\\ob\\' . $apiversion . '\\badge_exporter';
        if (!class_exists($classname)) {
            throw new \coding_exception('Invalid Open Badges API version');
        }

        $badgeid = helper::get_badgeid_from_hash($hash);
        if ($badgeid === null) {
            throw new \coding_exception('Badge with the given hash does not exist');
        }

        return new $classname($badgeid);
    }

    /**
     * Create an issuer_exporter object from badge identifier based on the OB API version.
     *
     * @param int|null $badgeid Badge identifier, or null to use the default issuer.
     * @param string $apiversion Open Badges API version.
     * @throws \coding_exception
     * @return \core_badges\local\backpack\ob\issuer_exporter_interface The issuer exporter object.
     */
    public static function create_issuer_exporter_from_id(
        ?int $badgeid,
        string $apiversion,
    ): issuer_exporter_interface {

        $classname = '\\core_badges\\local\\backpack\\ob\\' . $apiversion . '\\issuer_exporter';
        if (!class_exists($classname)) {
            throw new \coding_exception('Invalid Open Badges API version');
        }

        return new $classname($badgeid);
    }
}
