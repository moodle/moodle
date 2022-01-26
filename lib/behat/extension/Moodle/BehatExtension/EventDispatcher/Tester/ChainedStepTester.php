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

/**
 * Override step tester to ensure chained steps gets executed.
 *
 * @package    behat
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moodle\BehatExtension\EventDispatcher\Tester;

use Behat\Behat\Tester\Result\ExecutedStepResult;
use Behat\Behat\Tester\Result\SkippedStepResult;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Behat\Tester\StepTester;
use Behat\Behat\Tester\Result\UndefinedStepResult;
use Moodle\BehatExtension\Context\Step\Given;
use Moodle\BehatExtension\Context\Step\ChainedStep;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Call\CallResult;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\EventDispatcher\TestworkEventDispatcher;
use Behat\Behat\EventDispatcher\Event\AfterStepSetup;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeStepTeardown;
use Behat\Behat\EventDispatcher\Event\BeforeStepTested;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Moodle\BehatExtension\Exception\SkippedException;

/**
 * Override step tester to ensure chained steps gets executed.
 *
 * @package    behat
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ChainedStepTester implements StepTester {
    /**
     * The text of the step to look for exceptions / debugging messages.
     */
    const EXCEPTIONS_STEP_TEXT = 'I look for exceptions';

    /**
     * @var StepTester Base step tester.
     */
    private $singlesteptester;

    /**
     * @var EventDispatcher keep step event dispatcher.
     */
    private $eventDispatcher;

    /**
     * Keep status of chained steps if used.
     * @var bool
     */
    protected static $chainedstepused = false;

    /**
     * Constructor.
     *
     * @param StepTester $steptester single step tester.
     */
    public function __construct(StepTester $steptester) {
        $this->singlesteptester = $steptester;
    }

    /**
     * Set event dispatcher to use for events.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(Environment $env, FeatureNode $feature, StepNode $step, $skip) {
        return $this->singlesteptester->setUp($env, $feature, $step, $skip);
    }

    /**
     * {@inheritdoc}
     */
    public function test(Environment $env, FeatureNode $feature, StepNode $step, $skip) {
        $result = $this->singlesteptester->test($env, $feature, $step, $skip);

        if (!($result instanceof ExecutedStepResult) || !$this->supportsResult($result->getCallResult())) {
            $result = $this->checkSkipResult($result);

            // If undefined step then don't continue chained steps.
            if ($result instanceof UndefinedStepResult) {
                return $result;
            }

            // If exception caught, then don't continue chained steps.
            if (($result instanceof ExecutedStepResult) && $result->hasException()) {
                return $result;
            }

            // If step is skipped, then return. no need to continue chain steps.
            if ($result instanceof SkippedStepResult) {
                return $result;
            }

            // Check for exceptions.
            // Extra step, looking for a moodle exception, a debugging() message or a PHP debug message.
            $checkingStep = new StepNode('Given', self::EXCEPTIONS_STEP_TEXT, array(), $step->getLine());
            $afterExceptionCheckingEvent = $this->singlesteptester->test($env, $feature, $checkingStep, $skip);
            return $this->checkSkipResult($afterExceptionCheckingEvent);
        }

        return $this->runChainedSteps($env, $feature, $result, $skip);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(Environment $env, FeatureNode $feature, StepNode $step, $skip, StepResult $result) {
        return $this->singlesteptester->tearDown($env, $feature, $step, $skip, $result);
    }

    /**
     * Check if results supported.
     *
     * @param CallResult $result
     * @return bool
     */
    private function supportsResult(CallResult $result) {
        $return = $result->getReturn();
        if ($return instanceof ChainedStep) {
            return true;
        }
        if (!is_array($return) || empty($return)) {
            return false;
        }
        foreach ($return as $value) {
            if (!$value instanceof ChainedStep) {
                return false;
            }
        }
        return true;
    }

    /**
     * Run chained steps.
     *
     * @param Environment $env
     * @param FeatureNode $feature
     * @param ExecutedStepResult $result
     * @param $skip
     *
     * @return ExecutedStepResult|StepResult
     */
    private function runChainedSteps(Environment $env, FeatureNode $feature, ExecutedStepResult $result, $skip) {
        // Set chained setp is used, so it can be used by formatter to o/p.
        self::$chainedstepused = true;

        $callResult = $result->getCallResult();
        $steps = $callResult->getReturn();

        if (!is_array($steps)) {
            // Test it, no need to dispatch events for single chain.
            $stepResult = $this->test($env, $feature, $steps, $skip);
            return $this->checkSkipResult($stepResult);
        }

        // Test all steps.
        foreach ($steps as $step) {
            // Setup new step.
            $event = new BeforeStepTested($env, $feature, $step);
            if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
                // Symfony 4.3 and up.
                $this->eventDispatcher->dispatch($event, $event::BEFORE);
            } else {
                // TODO: Remove when our min supported version is >= 4.3.
                $this->eventDispatcher->dispatch($event::BEFORE, $event);
            }

            $setup = $this->setUp($env, $feature, $step, $skip);

            $event = new AfterStepSetup($env, $feature, $step, $setup);
            if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
                // Symfony 4.3 and up.
                $this->eventDispatcher->dispatch($event, $event::AFTER_SETUP);
            } else {
                // TODO: Remove when our min supported version is >= 4.3.
                $this->eventDispatcher->dispatch($event::AFTER_SETUP, $event);
            }

            // Test it.
            $stepResult = $this->test($env, $feature, $step, $skip);

            // Tear down.
            $event = new BeforeStepTeardown($env, $feature, $step, $result);
            if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
                // Symfony 4.3 and up.
                $this->eventDispatcher->dispatch($event, $event::BEFORE_TEARDOWN);
            } else {
                // TODO: Remove when our min supported version is >= 4.3.
                $this->eventDispatcher->dispatch($event::BEFORE_TEARDOWN, $event);
            }

            $teardown = $this->tearDown($env, $feature, $step, $skip, $result);

            $event = new AfterStepTested($env, $feature, $step, $result, $teardown);
            if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
                // Symfony 4.3 and up.
                $this->eventDispatcher->dispatch($event, $event::AFTER);
            } else {
                // TODO: Remove when our min supported version is >= 4.3.
                $this->eventDispatcher->dispatch($event::AFTER, $event);
            }

            //
            if (!$stepResult->isPassed()) {
                return $this->checkSkipResult($stepResult);
            }
        }
        return $this->checkSkipResult($stepResult);
    }

    /**
     * Handle skip exception.
     *
     * @param StepResult $result
     *
     * @return ExecutedStepResult|SkippedStepResult
     */
    private function checkSkipResult(StepResult $result) {
        if ((method_exists($result, 'getException')) && ($result->getException() instanceof SkippedException)) {
            return new SkippedStepResult($result->getSearchResult());
        } else {
            return $result;
        }
    }

    /**
     * Returns if cahined steps are used.
     * @return bool.
     */
    public static function is_chained_step_used() {
        return self::$chainedstepused;
    }
}
