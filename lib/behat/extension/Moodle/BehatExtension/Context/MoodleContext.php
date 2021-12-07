<?php

namespace Moodle\BehatExtension\Context;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Moodle contexts loader
 *
 * It gathers all the available steps definitions reading the
 * Moodle configuration file
 *
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleContext extends RawMinkContext {

    /**
     * Moodle features and steps definitions list
     * @var array
     */
    protected $moodleConfig;

    /**
     * Includes all the specified Moodle subcontexts
     * @param array $parameters
     */
    public function setMoodleConfig($parameters) {
        $this->moodleConfig = $parameters;
    }
}
