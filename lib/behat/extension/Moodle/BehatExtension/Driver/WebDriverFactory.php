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

namespace Moodle\BehatExtension\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use OAndreyev\Mink\Driver\WebDriverFactory as UpstreamFactory;
use Symfony\Component\DependencyInjection\Definition;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * Driver factory for the Moodle WebDriver.
 *
 * @package    core
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class WebDriverFactory extends UpstreamFactory implements DriverFactory {
    /**
     * Builds the service definition for the driver.
     *
     * @param array $config
     * @return Definition
     */
    public function buildDriver(array $config) {
        // Merge capabilities.
        $extracapabilities = $config['capabilities']['extra_capabilities'];
        unset($config['capabilities']['extra_capabilities']);

        // Normalise the Edge browser name.
        if ($config['browser'] === 'edge') {
            $config['browser'] = 'MicrosoftEdge';
        }

        // Ensure that the capabilites.browserName is set correctly.
        $config['capabilities']['browserName'] = $config['browser'];

        $capabilities = array_replace($extracapabilities, $config['capabilities']);

        // Incorrect top level capabilities lead to invalid Selenium browser selection.
        // See https://github.com/SeleniumHQ/selenium/issues/10410 for more information.
        // If any of these settings are mentioned then additional empty Capability options are created and a random
        // browser is chosen.
        $filteredcapabilities = [
            'tags',
            'ignoreZoomSetting',
            'marionette',
            'browser',
            'name',
        ];

        foreach ($filteredcapabilities as $capabilityname) {
            unset($capabilities[$capabilityname]);
        }

        // Build driver definition.
        return new Definition(WebDriver::class, [
            $config['browser'],
            $capabilities,
            $config['wd_host'],
        ]);
    }

    /**
     * Get the CapabilitiesNode.
     *
     * @return Node
     */
    protected function getCapabilitiesNode() {
        $node = parent::getCapabilitiesNode();

        // Specify chrome as the default browser.
        $node->find('browser')->defaultValue('chrome');

        return $node;
    }
}
