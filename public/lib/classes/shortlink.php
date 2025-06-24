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

namespace core;

use core\exception\coding_exception;

/**
 * Shortlink manager for Moodle.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shortlink {
    /** @var int The minimum shortcode length */
    private const SHORTCODE_MIN_LENGTH = 2;

    /** @var int The maximum shortcode length */
    private const SHORTCODE_MAX_LENGTH = 8;

    /** @var string The list of possible characters */
    private const SHORTCODE_CHARS = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789_-';

    /** @var int The number of shortcodes to generate at once */
    private const GENERATE_COUNT = 30;

    /**
     * Constructor for the shortlink manager.
     *
     * @param \moodle_database $db
     */
    public function __construct(
        /** @var \moodle_database The DB handler */
        private \moodle_database $db,
    ) {
    }

    /**
     * Fetch the URL for the specified shortcode.
     *
     * @param bool $isuserspecific
     * @param string $shortcode
     * @throws coding_exception
     * @return url
     */
    public function fetch_url_for_shortcode(
        bool $isuserspecific,
        string $shortcode,
    ): url {
        global $USER;

        $result = $this->db->get_record('shortlink', [
            'shortcode' => $shortcode,
            'userid' => $isuserspecific ? $USER->id : 0,
        ]);

        if ($result === false) {
            throw new coding_exception("Shortlink not found for shortcode {$shortcode}");
        }

        $component = $result->component;
        $linktype = $result->linktype;
        $identifier = $result->identifier;

        $handler = $this->get_shortlink_handler($component);
        $this->validate_linktype($component, $handler, $linktype);

        $link = $handler->process_shortlink($linktype, $identifier);
        if ($link === null) {
            throw new coding_exception("No URL found for shortcode {$shortcode}");
        }

        return $link;
    }

    /**
     * Create a shortlink URL for the specified user.
     *
     * To create a public shortlink, a userid of 0 can be used.
     *
     * @param string $component
     * @param string $linktype
     * @param int|string $identifier
     * @param int $userid
     * @param int $minlength
     * @param int $maxlength
     * @throws coding_exception
     * @return url
     */
    public function create_shortlink(
        string $component,
        string $linktype,
        int|string $identifier,
        int $userid,
        int $minlength = self::SHORTCODE_MIN_LENGTH,
        int $maxlength = self::SHORTCODE_MAX_LENGTH,
    ): url {
        if ($userid === 0) {
            return $this->create_public_shortlink(
                component: $component,
                linktype: $linktype,
                identifier: $identifier,
                minlength: $minlength,
                maxlength: $maxlength,
            );
        }
        $shortcode = $this->generate_and_store_shortcode(
            userids: $userid,
            component: $component,
            linktype: $linktype,
            identifier: $identifier,
            minlength: $minlength,
            maxlength: $maxlength,
        );

        $url = \core\router\util::get_path_for_callable(
            callable: [\core\route\shortlink::class, 'handle_shortlink'],
            params: ['shortcode' => $shortcode],
        );

        return $url;
    }

    /**
     * Create a public shortlink URL.
     *
     * @param string $component
     * @param string $linktype
     * @param int|string $identifier
     * @param int $minlength
     * @param int $maxlength
     * @throws coding_exception
     * @return url
     */
    public function create_public_shortlink(
        string $component,
        string $linktype,
        int|string $identifier,
        int $minlength = self::SHORTCODE_MIN_LENGTH,
        int $maxlength = self::SHORTCODE_MAX_LENGTH,
    ): url {
        $shortcode = $this->generate_and_store_shortcode(
            userids: 0,
            component: $component,
            linktype: $linktype,
            identifier: $identifier,
            minlength: $minlength,
            maxlength: $maxlength,
        );

        $url = \core\router\util::get_path_for_callable(
            callable: [\core\route\shortlink::class, 'handle_public_shortlink'],
            params: ['shortcode' => $shortcode],
        );

        return $url;
    }

    /**
     * Create a shortlink for a set of users.
     *
     * The shortlink returned will be the same for all specified users.
     *
     * @param string $component
     * @param string $linktype
     * @param string $identifier
     * @param array $userids
     * @param int $minlength
     * @param int $maxlength
     * @throws coding_exception
     * @return url
     */
    public function create_shortlink_for_users(
        string $component,
        string $linktype,
        int|string $identifier,
        array $userids,
        int $minlength = self::SHORTCODE_MIN_LENGTH,
        int $maxlength = self::SHORTCODE_MAX_LENGTH,
    ): url {
        // Ensure none of the user IDs are 0.
        if (in_array(0, $userids, true)) {
            throw new coding_exception('User-specific short links cannot be created for user ID 0.');
        }

        $shortcode = $this->generate_and_store_shortcode(
            userids: $userids,
            component: $component,
            linktype: $linktype,
            identifier: $identifier,
            minlength: $minlength,
            maxlength: $maxlength,
        );

        $url = \core\router\util::get_path_for_callable(
            callable: [\core\route\shortlink::class, 'handle_shortlink'],
            params: ['shortcode' => $shortcode],
        );

        return $url;
    }

    /**
     * Generate and store a shortcode.
     *
     * @param string $component
     * @param string $linktype
     * @param int|string $identifier
     * @param int|array $userids
     * @param int $minlength
     * @param int $maxlength
     * @return string
     */
    private function generate_and_store_shortcode(
        string $component,
        string $linktype,
        int|string $identifier,
        int|array $userids,
        int $minlength,
        int $maxlength,
    ): string {
        $handler = $this->get_shortlink_handler($component);
        $this->validate_linktype($component, $handler, $linktype);

        $transaction = $this->db->start_delegated_transaction();

        $userid = is_int($userids) ? $userids : 0;
        $userids = is_int($userids) ? [$userids] : $userids;
        $shortcode = $this->get_unused_shortcode(
            userid: $userid,
            minlength: $minlength,
            maxlength: $maxlength,
        );
        $linkdata = [
            'component' => $component,
            'linktype' => $linktype,
            'identifier' => $identifier,
            'shortcode' => $shortcode,
            'timecreated' => time(),
        ];
        // Add in user ids.
        $linkdata = array_map(
            fn (int $userid) => (object) array_merge($linkdata, ['userid' => $userid]),
            $userids,
        );

        $this->db->insert_records('shortlink', $linkdata);
        $transaction->allow_commit();

        return $shortcode;
    }

    /**
     * Get an unused shortcode suitable for this user.
     *
     * @param int $userid
     * @param int $minlength
     * @param int $maxlength
     * @return string
     */
    private function get_unused_shortcode(
        int $userid,
        int $minlength = self::SHORTCODE_MIN_LENGTH,
        int $maxlength = self::SHORTCODE_MAX_LENGTH,
    ): string {
        do {
            $shortcodes = array_map(
                fn() => $this->generate_shortcode(
                    minlength: $minlength,
                    maxlength: $maxlength,
                ),
                array_fill(0, self::GENERATE_COUNT, null),
            );

            [$sql, $params] = $this->db->get_in_or_equal($shortcodes, SQL_PARAMS_NAMED);

            if ($userid === 0) {
                // Global shortcodes must be entirely unique.
                $existing = $this->db->get_records_select_menu(
                    table: 'shortlink',
                    select: "shortcode {$sql}",
                    params: $params,
                    fields: 'id, shortcode',
                );
            } else {
                // User-specific shortcodes may be re-used by other users, but not by the same user, and not globally.
                $params['userid'] = $userid;
                $existing = $this->db->get_records_select_menu(
                    table: 'shortlink',
                    select: "(userid = 0 OR userid = :userid) AND shortcode $sql",
                    params: $params,
                    fields: 'id, shortcode',
                );
            }

            $unused = array_diff($shortcodes, $existing);
            if (count($unused) === 0) {
                // If we didn't find any unused shortcodes, increase the max length before trying again.
                $maxlength++;
            }
        } while (count($unused) === 0);

        return array_shift($unused);
    }

    /**
     * Generate a shortcode.
     *
     * @param int $minlength
     * @param int $maxlength
     * @return string
     */
    private function generate_shortcode(
        int $minlength = self::SHORTCODE_MIN_LENGTH,
        int $maxlength = self::SHORTCODE_MAX_LENGTH,
    ): string {
        if ($minlength < 1 || $maxlength < 1 || $minlength > $maxlength) {
            throw new coding_exception('Invalid min/max length for shortcode generation');
        }

        if ($minlength === $maxlength) {
            $data = array_fill(0, $minlength, null);
        } else {
            $data = array_fill(0, rand($minlength, $maxlength), null);
        }

        return implode('', array_map(
            fn (): string => self::SHORTCODE_CHARS[rand(0, strlen(self::SHORTCODE_CHARS) - 1)],
            $data,
        ));
    }

    /**
     * Get the shortlink handler for the specified component.
     *
     * @param string $component
     * @throws coding_exception
     * @return shortlink_handler_interface
     */
    private function get_shortlink_handler(string $component): shortlink_handler_interface {
        try {
            $handler = \core\di::get("{$component}\\shortlink_handler");
        } catch (\DI\NotFoundException $e) {
            throw new coding_exception("No shortlink handler found for component {$component}");
        }

        if (!$handler instanceof shortlink_handler_interface) {
            throw new coding_exception("Shortlink handler for component {$component} must implement shortlink_handler_interface");
        }

        return $handler;
    }

    /**
     * Validate a link type for the specified handler.
     *
     * @param string $component
     * @param shortlink_handler_interface $handler
     * @param string $linktype
     * @throws coding_exception
     */
    private function validate_linktype(
        string $component,
        shortlink_handler_interface $handler,
        string $linktype,
    ): void {
        if (!in_array($linktype, $handler->get_valid_linktypes(), true)) {
            throw new coding_exception("Invalid link type {$linktype} for component {$component}");
        }
    }
}
