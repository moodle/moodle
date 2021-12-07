<?php

namespace Moodle\BehatExtension\Driver;

use Behat\Mink\Session;
use OAndreyev\Mink\Driver\WebDriver as UpstreamDriver;
use WebDriver\Key as key;

/**
 * WebDriver Driver to allow extra selenium capabilities required by Moodle.
 */
class WebDriver extends UpstreamDriver
{

    /**
     * Dirty attribute to get the browser name; $browserName is private
     * @var string
     */
    protected static $browser;

    /**
     * Instantiates the driver.
     *
     * @param string    $browser Browser name
     * @param array     $desiredCapabilities The desired capabilities
     * @param string    $wdHost The WebDriver host
     * @param array     $moodleParameters Moodle parameters including our non-behat-friendly selenium capabilities
     */
    public function __construct($browserName = 'chrome', $desiredCapabilities = null, $wdHost = 'http://localhost:4444/wd/hub', $moodleParameters = array()) {
        parent::__construct($browserName, $desiredCapabilities, $wdHost);

        // This class is instantiated by the dependencies injection system so
        // prior to all of beforeSuite subscribers which will call getBrowser*()
        self::$browser = $browserName;
    }

    /**
     * Returns the browser being used.
     *
     * We need to know it:
     * - To show info about the run.
     * - In case there are differences between browsers in the steps.
     *
     * @static
     * @return string
     */
    public static function getBrowserName() {
        return self::$browser;
    }

    /**
     * Post key on specified xpath.
     *
     * @param string $xpath
     */
    public function post_key($key, $xpath) {
        throw new \Exception('No longer used - please use keyDown and keyUp');
    }
}
