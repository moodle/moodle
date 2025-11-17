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

namespace tool_mobile\local\hooks;

/**
 * Allow adjustment of ios smart app banner fields.
 *
 * @package    tool_mobile
 * @copyright  2025 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allow adjustment of ios smart app banner fields')]
#[\core\attribute\tags('mobile')]
final class before_extend_ios_app_banner {
    /**
     * Create a new instance of the hook.
     *
     * @param string $appid The app id for the ios smart banner
     * @param string $appargument The app argument for the ios smart banner
     */
    public function __construct(
        /** @var string $appid The app id for the ios smart banner */
        private string $appid,
        /** @var string $appargument The app argument for the ios smart banner */
        private string $appargument,
    ) {
    }

    /**
     * Get the appid.
     *
     * @return string
     */
    public function get_appid(): string {
        return $this->appid;
    }

    /**
     * Set the appid.
     *
     * @param string $appid
     */
    public function set_appid(string $appid): void {
        $this->appid = $appid;
    }

    /**
     * Get the appargument.
     *
     * @return string
     */
    public function get_appargument(): string {
        return $this->appargument;
    }

    /**
     * Set the appargument.
     *
     * @param string $appargument
     */
    public function set_appargument(string $appargument): void {
        $this->appargument = $appargument;
    }
}
