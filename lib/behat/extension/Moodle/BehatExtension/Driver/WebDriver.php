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

use Behat\Mink\Exception\DriverException;
use OAndreyev\Mink\Driver\WebDriver as UpstreamDriver;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

/**
 * WebDriver Driver to allow extra selenium capabilities required by Moodle.
 *
 * @package core
 * @copyright 2016 onwards Rajesh Taneja
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class WebDriver extends UpstreamDriver {


    /**
     * Dirty attribute to get the browser name; $browserName is private
     * @var string
     */
    protected static $browser;

    /**
     * Instantiates the driver.
     *
     * @param string    $browsername Browser name
     * @param array     $desiredcapabilities The desired capabilities
     * @param string    $wdhost The WebDriver host
     * @param array     $moodleparameters Moodle parameters including our non-behat-friendly selenium capabilities
     */
    public function __construct(
        $browsername = 'chrome',
        $desiredcapabilities = null,
        $wdhost = 'http://localhost:4444/wd/hub',
        $moodleparameters = []
    ) {
        parent::__construct($browsername, $desiredcapabilities, $wdhost);

        // This class is instantiated by the dependencies injection system so prior to all of beforeSuite subscribers
        // which will call getBrowser*().
        self::$browser = $browsername;
    }

    /**
     * Returns the browser being used.
     *
     * We need to know it:
     * - To show info about the run.
     * - In case there are differences between browsers in the steps.
     *
     * @return string
     */
    public static function getBrowserName() {
        return self::$browser;
    }

    /**
     * Post key on specified xpath.
     *
     * @param string $key
     * @param string $xpath
     */
    public function post_key($key, $xpath) {
        throw new \Exception('No longer used - please use keyDown and keyUp');
    }

    #[\Override]
    public function stop(): void {
        try {
            parent::stop();
        } catch (DriverException $e) {
            error_log($e->getMessage());
            $rcp = new \ReflectionProperty(parent::class, 'webDriver');
            $rcp->setValue($this, null);
        }
    }
}
