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

/**
 * Class environment
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class environment {

    /**
     * Get the Composer root install path for the active Composer runtime.
     *
     * @return string|null
     */
    protected static function get_composer_root_install_path(): ?string {
        if (!class_exists(\Composer\InstalledVersions::class)) {
            return null;
        }

        $rootpackage = \Composer\InstalledVersions::getRootPackage();
        if (!is_array($rootpackage) || empty($rootpackage['install_path'])) {
            return null;
        }

        $realpath = realpath($rootpackage['install_path']);
        return $realpath ?: null;
    }

    /**
     * Ensure that Composer dependencies are installed and the necessary files are present.
     *
     * @param \environment_results $result
     * @return \environment_results|null
     */
    public static function check_composer_dependencies_installed(\environment_results $result): ?\environment_results {
        // Check if the composer vendor directory exists.
        if (!class_exists(\Composer\InstalledVersions::class)) {
            $result->setInfo('Composer installed data not found');
            $result->setFeedbackStr('composernotfound');
            return $result;
        }

        return null;
    }

    /**
     * Ensure that Composer developer dependencies are not installed.
     *
     * @param \environment_results $result
     * @return \environment_results|null
     */
    public static function check_composer_developer_dependencies_not_installed(
        \environment_results $result
    ): ?\environment_results {
        if (static::is_developer_mode_enabled()) {
            $result->setInfo('Developer mode is enabled, skipping check for developer dependencies');
            return null; // Skip this check in developer mode.
        }

        if (!class_exists(\Composer\InstalledVersions::class)) {
            return null; // Composer not installed, so no developer dependencies to check.
        }

        $installed = \Composer\InstalledVersions::getAllRawData();
        if (!is_array($installed)) {
            return null;
        }

        // Only consider the installed data set which matches the active Composer root.
        // This reduces the risk of reporting false positives if multiple Composer autoloaders
        // have been included in the same process.
        $rootinstallpath = static::get_composer_root_install_path();
        if ($rootinstallpath === null) {
            return null;
        }

        foreach ($installed as $data) {
            if (!is_array($data) || !array_key_exists('root', $data) || !is_array($data['root'])) {
                continue;
            }

            $installpath = $data['root']['install_path'] ?? null;
            if (empty($installpath) || realpath($installpath) !== $rootinstallpath) {
                continue;
            }

            if (!empty($data['root']['dev'])) {
                $result->setInfo('Composer Developer dependencies are installed');
                $result->setFeedbackStr('composerdeveloperdependenciesinstalled');
                return $result;
            }
        }

        return null;
    }

    /**
     * Ensure that Composer developer dependencies are optimised    .
     *
     * @param \environment_results $result
     * @return \environment_results|null
     * @codeCoverageIgnore
     */
    public static function check_composer_dependencies_optimised(
        \environment_results $result
    ): ?\environment_results {
        if (!class_exists(\Composer\InstalledVersions::class)) {
            return null; // Composer not installed, so no developer dependencies to check.
        }

        $rootpackage = \Composer\InstalledVersions::getRootPackage();
        if (!is_array($rootpackage) || empty($rootpackage['install_path'])) {
            return null;
        }

        $rootpath = $rootpackage['install_path'];
        $rootvendor = realpath("{$rootpath}/vendor");
        if (!$rootvendor) {
            return null;
        }

        $loaders = \Composer\Autoload\ClassLoader::getRegisteredLoaders();
        if (!array_key_exists($rootvendor, $loaders)) {
            return null; // No autoloader for our vendor dir, so nothing to check.
        }

        $autoloader = $loaders[$rootvendor];

        if (static::is_developer_mode_enabled()) {
            if ($autoloader->isClassMapAuthoritative()) {
                $result->setInfo('Composer autoloader is optimised');
                $result->setFeedbackStr('composeroptimisedindevmode');

                return $result;
            }

            $result->setInfo('Developer mode is enabled, optimiser is correctly disabled.');

            return null;
        }

        if ($autoloader->isClassMapAuthoritative()) {
            $result->setInfo('Autoloader is correctly optimised.');

            return null;
        }

        $result->setInfo('Composer autoloader is not optimised');
        $result->setFeedbackStr('composernotoptimised');

        return $result;
    }

    /**
     * Get the path to the Composer vendor directory.
     *
     * @return string
     */
    protected static function get_vendor_path(): string {
        global $CFG;

        // Return the path to the vendor directory.
        return "{$CFG->root}/vendor";
    }

    /**
     * Check if developer mode is enabled.
     *
     * @return bool
     */
    protected static function is_developer_mode_enabled(): bool {
        global $CFG;

        return !empty($CFG->debugdeveloper);
    }

    /**
     * Ensure that the Router is correctly configured.
     *
     * @param \environment_results $result
     * @return \environment_results|null
     */
    public static function check_router_configuration(\environment_results $result): ?\environment_results {
        global $CFG;

        if (empty($CFG->routerconfigured)) {
            // The router has not been marked as configured.
            $result->setInfo('Router not configured');
            $result->setFeedbackStr('routernotconfigured');
            return $result;
        }

        return null;
    }
}
