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

namespace local_launchpad;

/**
 * Fail-closed checks for whether --apply may write to a Moodle site.
 *
 * Dry-run does not need this. Apply requires a positively verified local target.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class apply_target {

    /**
     * Hosts that are never acceptable for --apply (even with override env).
     *
     * @param string $host Lowercased host from wwwroot.
     */
    public static function is_denied_host(string $host): bool {
        $host = strtolower(trim($host));
        if ($host === '') {
            return true;
        }
        if (str_contains($host, 'toba-tech')) {
            return true;
        }
        // Common production-style hosts.
        if (preg_match('/(^|\.)learn\./', $host)) {
            return true;
        }
        if (preg_match('/(^|\.)prod(\.|$)/', $host)) {
            return true;
        }
        return false;
    }

    /**
     * Loopback hosts treated as candidate local wwwroots.
     */
    public static function is_loopback_host(string $host): bool {
        $host = strtolower(trim($host));
        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }

    /**
     * Positive local DB name signal (not sufficient alone).
     */
    public static function is_local_dbname(string $dbname): bool {
        $dbname = strtolower(trim($dbname));
        if ($dbname === '') {
            return false;
        }
        if ($dbname === 'moodle_phpunit') {
            return true;
        }
        if (str_contains($dbname, 'phpunit')) {
            return true;
        }
        if (str_contains($dbname, '_local') || str_ends_with($dbname, 'local')) {
            return true;
        }
        return false;
    }

    /**
     * Whether --apply is allowed against this target.
     *
     * Fail-closed: returns false unless loopback wwwroot AND a positive local
     * signal (local-ish dbname and/or explicit env). Denied hosts never pass.
     *
     * @param array<string, string|false|null>|null $env Optional env map for tests.
     */
    public static function is_verified_local(string $wwwroot, string $dbname = '', ?array $env = null): bool {
        $wwwroot = trim($wwwroot);
        if ($wwwroot === '') {
            return false;
        }

        $parts = parse_url($wwwroot);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        $scheme = strtolower((string) $parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower((string) $parts['host']);
        if (self::is_denied_host($host)) {
            return false;
        }
        if (!self::is_loopback_host($host)) {
            return false;
        }

        $env = $env ?? self::current_env();
        $docker = !empty($env['MOODLE_DOCKER_PHPUNIT']);
        $allow = isset($env['MOODLE_LAUNCHPAD_ALLOW_APPLY'])
            && (string) $env['MOODLE_LAUNCHPAD_ALLOW_APPLY'] === '1';
        $localdb = self::is_local_dbname($dbname);

        // Require at least one positive local signal beyond "host looks like localhost".
        return $docker || $allow || $localdb;
    }

    /**
     * Human-readable reason when apply must be refused.
     *
     * @param array<string, string|false|null>|null $env
     */
    public static function refusal_reason(string $wwwroot, string $dbname = '', ?array $env = null): string {
        if (self::is_verified_local($wwwroot, $dbname, $env)) {
            return '';
        }

        $parts = parse_url(trim($wwwroot));
        $host = is_array($parts) && !empty($parts['host']) ? strtolower((string) $parts['host']) : '';

        if (trim($wwwroot) === '' || $parts === false || $host === '') {
            return 'wwwroot is missing or not a valid URL';
        }
        if (self::is_denied_host($host)) {
            return 'wwwroot host is denied for --apply (' . $host . ')';
        }
        if (!self::is_loopback_host($host)) {
            return 'wwwroot host is not a verified loopback local host (' . $host . ')';
        }
        return 'local target not positively verified '
            . '(need dbname like moodle_phpunit / *_local, or MOODLE_DOCKER_PHPUNIT, '
            . 'or MOODLE_LAUNCHPAD_ALLOW_APPLY=1)';
    }

    /**
     * @return array<string, string|false|null>
     */
    private static function current_env(): array {
        return [
            'MOODLE_DOCKER_PHPUNIT' => getenv('MOODLE_DOCKER_PHPUNIT'),
            'MOODLE_LAUNCHPAD_ALLOW_APPLY' => getenv('MOODLE_LAUNCHPAD_ALLOW_APPLY'),
        ];
    }
}
