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

use core\composer\status;
use core\composer\package_status;

/**
 * Composer runtime status utility class.
 *
 * This class provides runtime checks for composer-managed dependencies.
 *
 * It can detect:
 * - Whether Composer dependencies are installed
 * - Whether installed dependencies are outdated relative to composer.lock
 * - Missing packages from the installed vendor state
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class composer {
    /** @var bool|null Instance cache for the composer installation state. */
    protected ?bool $isinstalled = null;

    /** @var array|null Instance cache for parsed lockfile packages. */
    protected ?array $lockedpackages = null;

    /** @var array|null Instance cache for installed package versions. */
    protected ?array $installedversions = null;

    /**
     * Constructor.
     *
     * @param string $vendordir The path to the vendor directory.
     * @param string $lockfilepath The path to the composer.lock file.
     */
    public function __construct(
        /** @var string The path to the vendor directory. */
        protected readonly string $vendordir,
        /** @var string The path to the composer.lock file. */
        protected readonly string $lockfilepath
    ) {
    }

    /**
     * Determine whether `composer install` has been run.
     *
     * @return bool
     */
    public function is_installed(): bool {
        if ($this->isinstalled !== null) {
            return $this->isinstalled;
        }

        $isinstalled = file_exists($this->vendordir . '/autoload.php') &&
            file_exists($this->vendordir . '/composer/installed.php');

        return $this->isinstalled = $isinstalled;
    }

    /**
     * Get a detailed composer runtime status.
     *
     * @return status The current composer runtime status, including installation state and package statuses.
     */
    public function get_status(): status {
        $iscurrent = true;
        $packages = [];

        $lockedpackages = $this->get_locked_packages();

        // If there are issues with the lockfile, the overall status is not current.
        if ($lockedpackages === null) {
            $iscurrent = false;
        }

        foreach ($lockedpackages as $package => $version) {
            $packagestatus = $this->get_package_status($package);
            $packages[$package] = $packagestatus;
            // If any package is not installed or not up-to-date, the overall status is not current.
            if ($packagestatus->installed === false || $packagestatus->current === false) {
                $iscurrent = false;
            }
        }

        return new status(
            $this->is_installed(),
            $iscurrent,
            $packages
        );
    }

    /**
     * Get the status of a specific composer package.
     *
     * @param string $package The package name.
     * @return package_status The package status, including installation state, version information, and whether it's up-to-date.
     * @throws \InvalidArgumentException If the package is not found in the lockfile.
     */
    public function get_package_status(string $package): package_status {
        $lockedpackages = $this->get_locked_packages();

        if ($lockedpackages === null || !array_key_exists($package, $lockedpackages)) {
            throw new \InvalidArgumentException("Package '{$package}' not found in composer.lock");
        }

        $requiredversion = $lockedpackages[$package];
        $installedversion = $this->get_installed_package_version($package);

        $isinstalled = $installedversion !== null;
        $iscurrent = $isinstalled && $requiredversion === $installedversion;

        return new package_status(
            $isinstalled,
            $iscurrent,
            $requiredversion,
            $installedversion
        );
    }

    /**
     * Get packages defined in composer.lock.
     *
     * @return array|null An array of package names and their required versions from composer.lock, or null if the file
     *                    does not exist or is invalid JSON.
     */
    protected function get_locked_packages(): ?array {
        if ($this->lockedpackages !== null) {
            return $this->lockedpackages;
        }

        if (!file_exists($this->lockfilepath)) {
            return $this->lockedpackages = null;
        }

        $contents = file_get_contents($this->lockfilepath);

        if ($contents === false) {
            return $this->lockedpackages = null;
        }

        $json = json_decode($contents, true);

        if (!is_array($json)) {
            return $this->lockedpackages = null;
        }

        $packages = [];

        foreach ($json['packages'] ?? [] as $package) {
            // Skip invalid composer.lock entries (missing name or version).
            // Lockfile validation is outside the scope of this API; data is processed on a best-effort basis.
            if (empty($package['name']) || empty($package['version'])) {
                continue;
            }

            // Strip leading 'v' from version.
            $packages[$package['name']] = ltrim($package['version'], 'v');
        }

        return $this->lockedpackages = $packages;
    }

    /**
     * Get the installed package version.
     *
     * @param string $package The package name.
     * @return string|null The installed package version or null if not installed.
     */
    protected function get_installed_package_version(string $package): ?string {
        $installedversions = $this->get_installed_versions();

        return $installedversions[$package] ?? null;
    }

    /**
     * Get the installed package versions.
     *
     * @return array The installed package versions, keyed by package name.
     * @throws \Exception If the Moodle root package is not found in the installed metadata.
     */
    private function get_installed_versions(): array {
        if ($this->installedversions !== null) {
            return $this->installedversions;
        }

        // If `composer install` has not been run, there are no installed versions.
        if ($this->is_installed() === false) {
            return $this->installedversions = [];
        }

        $data = \Composer\InstalledVersions::getAllRawData();

        foreach ($data as $package) {
            $packagename = $package['root']['name'] ?? null;

            // Skip if not the Moodle package.
            if ($packagename !== 'moodle/moodle') {
                continue;
            }

            $versions = [];

            foreach (($package['versions'] ?? []) as $name => $versiondata) {
                $version = $versiondata['pretty_version'] ?? null;

                if ($version !== null) {
                    // Strip leading 'v' from the version.
                    $version = ltrim($version, 'v');
                }

                $versions[$name] = $version;
            }

            return $this->installedversions = $versions;
        }

        // If we land here, the Moodle root package was not found in the installed metadata. This likely means that a
        // different Composer installation is active.
        throw new \Exception("The Moodle root package ('moodle/moodle') was not found in the active Composer " .
            "installed package metadata. This may indicate that a different Composer installation is active."
        );
    }
}
