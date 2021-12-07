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
 * @package    behat
 * @copyright  2016 Rajesh Taneja <rajesh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class MoodleEventDispatchingStepTester implements StepTester
{
    /**
     * @var StepTester
     */
    private $baseTester;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Initializes tester.
     *
     * @param StepTester               $baseTester
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(StepTester $baseTester, EventDispatcherInterface $eventDispatcher) {
        $this->baseTester = $baseTester;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(Environment $env, FeatureNode $feature, StepNode $step, $skip) {
        $event = new BeforeStepTested($env, $feature, $step);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventDispatcher->dispatch($event, $event::BEFORE);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventDispatcher->dispatch($event::BEFORE, $event);
        }

        $setup = $this->baseTester->setUp($env, $feature, $step, $skip);
        $this->baseTester->setEventDispatcher($this->eventDispatcher);

        $event = new AfterStepSetup($env, $feature, $step, $setup);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventDispatcher->dispatch($event, $event::AFTER_SETUP);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventDispatcher->dispatch($event::AFTER_SETUP, $event);
        }

        return $setup;
    }

    /**
     * {@inheritdoc}
     */
    public function test(Environment $env, FeatureNode $feature, StepNode $step, $skip) {
        return $this->baseTester->test($env, $feature, $step, $skip);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(Environment $env, FeatureNode $feature, StepNode $step, $skip, StepResult $result) {
        $event = new BeforeStepTeardown($env, $feature, $step, $result);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventDispatcher->dispatch($event, $event::BEFORE_TEARDOWN);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventDispatcher->dispatch($event::BEFORE_TEARDOWN, $event);
        }

        $teardown = $this->baseTester->tearDown($env, $feature, $step, $skip, $result);

        $event = new AfterStepTested($env, $feature, $step, $result, $teardown);
        if (TestworkEventDispatcher::DISPATCHER_VERSION === 2) {
            // Symfony 4.3 and up.
            $this->eventDispatcher->dispatch($event, $event::AFTER);
        } else {
            // TODO: Remove when our min supported version is >= 4.3.
            $this->eventDispatcher->dispatch($event::AFTER, $event);
        }

        return $teardown;
    }
}
