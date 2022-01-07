<?php

namespace Moodle\BehatExtension\Context\Initializer;

use Moodle\BehatExtension\Context\MoodleContext;
use Behat\Behat\Context\Initializer\ContextInitializer;

use Behat\Behat\Context\Context;

/**
 * MoodleContext initializer
 *
 * @author    David Monllaó <david.monllao@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleAwareInitializer implements ContextInitializer
{
    private $parameters;


    /**
     * Initializes initializer.
     *
     * @param Mink  $mink
     * @param array $parameters
     */
    public function __construct(array $parameters) {
        $this->parameters = $parameters;
    }

    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context) {
        if (method_exists($context, 'setMoodleConfig')) {
            $context->setMoodleConfig($this->parameters);
        }
    }
}
