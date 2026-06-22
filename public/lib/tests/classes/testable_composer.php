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

namespace core\tests;

/**
 * Testable composer helper subclass.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_composer extends \core\composer {
    /**
     * Set installed packages with version ['package/name' => 'version'] for the testable composer instance.
     *
     * @param array $installedversions
     * @return void
     */
    public function set_installed_versions(array $installedversions): void {
        $this->installedversions = $installedversions;
    }

    /**
     * Set the composer.lock file contents.
     *
     * @param array $packages Array containing package name and version pairs ['package/name' => 'version'].
     * @return void
     */
    public function set_composer_lock(array $packages): void {
        $contents = [];

        foreach ($packages as $name => $version) {
            $contents['packages'][] = [
                'name' => $name,
                'version' => $version,
            ];
        }

        file_put_contents($this->lockfilepath, json_encode($contents));
    }

    /**
     * Create the necessary composer installed files (autoload.php and composer/installed.php) to simulate an installed state.
     *
     * @return void
     */
    public function create_composer_installed_files(): void {
        mkdir($this->vendordir . '/composer', 0777, true);
        file_put_contents($this->vendordir . '/autoload.php', '');
        file_put_contents($this->vendordir . '/composer/installed.php', '');
    }
}
