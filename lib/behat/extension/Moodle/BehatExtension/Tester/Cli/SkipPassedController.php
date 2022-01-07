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
 * Caches passed scenarios and skip only them if `--skip-passed` option provided.
 *
 * @copyright  2016 onwards Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace Moodle\BehatExtension\Tester\Cli;

use Behat\Behat\EventDispatcher\Event\AfterFeatureTested;
use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\Cli\Controller;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * Caches passed scenarios and skip only them if `--skip-passed` option provided.
 *
 * @copyright  2016 onwards Rajesh Taneja
 */
final class SkipPassedController implements Controller {
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var null|string
     */
    private $cachePath;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string[]
     */
    private $lines = array();

    /**
     * @var string
     */
    private $basepath;

    /**
     * Initializes controller.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param null|string              $cachePath
     * @param string                   $basepath
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $cachePath, $basepath) {
        $this->eventDispatcher = $eventDispatcher;
        $this->cachePath = null !== $cachePath ? rtrim($cachePath, DIRECTORY_SEPARATOR) : null;
        $this->basepath = $basepath;
    }

    /**
     * Configures command to be executable by the controller.
     *
     * @param Command $command
     */
    public function configure(Command $command) {
        $command->addOption('--skip-passed', null, InputOption::VALUE_NONE,
            'Skip scenarios that passed during last execution.'
        );
    }

    /**
     * Executes controller.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|integer
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        if (!$input->getOption('skip-passed')) {
            // If no skip option is passed then remove any old file which we are saving.
            if (!$this->getFileName()) {
                return;
            }
            if (file_exists($this->getFileName())) {
                unlink($this->getFileName());
            }
            return;
        }

        $this->eventDispatcher->addListener(ScenarioTested::AFTER, array($this, 'collectPassedScenario'), -50);
        $this->eventDispatcher->addListener(ExampleTested::AFTER, array($this, 'collectPassedScenario'), -50);
        $this->eventDispatcher->addListener(ExerciseCompleted::AFTER, array($this, 'writeCache'), -50);
        $this->key = $this->generateKey($input);

        if (!$this->getFileName() || !file_exists($this->getFileName())) {
            return;
        }
        $input->setArgument('paths', $this->getFileName());

        $existing = json_decode(file_get_contents($this->getFileName()), true);
        if (!empty($existing)) {
            $this->lines = array_merge_recursive($existing, $this->lines);
        }
    }

    /**
     * Records scenario if it is passed.
     *
     * @param AfterScenarioTested $event
     */
    public function collectPassedScenario(AfterScenarioTested $event) {
        if (!$this->getFileName()) {
            return;
        }

        $feature = $event->getFeature();
        $suitename = $event->getSuite()->getName();

        if (($event->getTestResult()->getResultCode() !== TestResult::PASSED) &&
            ($event->getTestResult()->getResultCode() !== TestResult::SKIPPED)) {
            unset($this->lines[$suitename][$feature->getFile()]);
            return;
        }

        $this->lines[$suitename][$feature->getFile()] = $feature->getFile();
    }

    /**
     * Writes passed scenarios cache.
     */
    public function writeCache() {
        if (!$this->getFileName()) {
            return;
        }
        if (0 === count($this->lines)) {
            return;
        }
        file_put_contents($this->getFileName(), json_encode($this->lines));
    }

    /**
     * Generates cache key.
     *
     * @param InputInterface $input
     *
     * @return string
     */
    private function generateKey(InputInterface $input) {
        return md5(
            $input->getParameterOption(array('--profile', '-p')) .
            $input->getOption('suite') .
            implode(' ', $input->getOption('name')) .
            implode(' ', $input->getOption('tags')) .
            $input->getOption('role') .
            $input->getArgument('paths') .
            $this->basepath
        );
    }

    /**
     * Returns cache filename (if exists).
     *
     * @return null|string
     */
    private function getFileName() {
        if (null === $this->cachePath || null === $this->key) {
            return null;
        }
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777);
        }
        return $this->cachePath . DIRECTORY_SEPARATOR . $this->key . '.passed';
    }
}
