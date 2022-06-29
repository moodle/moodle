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

namespace Moodle\BehatExtension\EventDispatcher\Tester;

// phpcs:disable moodle.NamingConventions.ValidFunctionName.LowercaseMethod

use Behat\Behat\EventDispatcher\Event\AfterStepSetup;
use Behat\Behat\EventDispatcher\Event\AfterStepTested;
use Behat\Behat\EventDispatcher\Event\BeforeStepTeardown;
use Behat\Behat\EventDispatcher\Event\BeforeStepTested;
use Behat\Behat\Tester\Result\StepResult;
use Behat\Behat\Tester\StepTester;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Node\StepNode;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\EventDispatcher\TestworkEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Step tester dispatching BEFORE/AFTER events during tests.
 *
 * @package    core
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class MoodleEventDispatchingStepTester implements StepTester {

    /** @var StepTester */
    private $basetester;

    /** @var EventDispatcherInterface */
    private $eventdispatcher;

    /**
     * Initializes tester.
     *
     * @param StepTester               $basetester
     * @param EventDispatcherInterface $eventdispatcher
     */
    public function __construct(StepTester $basetester, EventDispatcherInterface $eventdispatcher) {
        $this->basetester = $basetester;
        $this->eventdispatcher = $eventdispatcher;
    }

    /**
     * Sets up step for a test.
     *
     * @param Environment $env
     * @param FeatureNode $feature
     * @param StepNode    $step
     * @param bool     $skip
     *
     * @return Setup
     */
    public function setUp(Environment $env, FeatureNode $feature, StepNode $step, $skip) {
        $event = new BeforeStepTested($env, $feature, $step);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventdispatcher->dispatch($event, $event::BEFORE);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventdispatcher->dispatch($event::BEFORE, $event);
        }

        $setup = $this->basetester->setUp($env, $feature, $step, $skip);
        $this->basetester->setEventDispatcher($this->eventdispatcher);

        $event = new AfterStepSetup($env, $feature, $step, $setup);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventdispatcher->dispatch($event, $event::AFTER_SETUP);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventdispatcher->dispatch($event::AFTER_SETUP, $event);
        }

        return $setup;
    }

    /**
     * Tests step.
     *
     * @param Environment $env
     * @param FeatureNode $feature
     * @param StepNode    $step
     * @param bool     $skip
     * @return StepResult
     */
    public function test(Environment $env, FeatureNode $feature, StepNode $step, $skip) {
        return $this->basetester->test($env, $feature, $step, $skip);
    }

    /**
     * Tears down step after a test.
     *
     * @param Environment $env
     * @param FeatureNode $feature
     * @param StepNode    $step
     * @param bool     $skip
     * @param StepResult  $result
     * @return Teardown
     */
    public function tearDown(Environment $env, FeatureNode $feature, StepNode $step, $skip, StepResult $result) {
        $event = new BeforeStepTeardown($env, $feature, $step, $result);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventdispatcher->dispatch($event, $event::BEFORE_TEARDOWN);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventdispatcher->dispatch($event::BEFORE_TEARDOWN, $event);
        }

        $teardown = $this->basetester->tearDown($env, $feature, $step, $skip, $result);

        $event = new AfterStepTested($env, $feature, $step, $result, $teardown);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventdispatcher->dispatch($event, $event::AFTER);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventdispatcher->dispatch($event::AFTER, $event);
        }

        return $teardown;
    }
}
