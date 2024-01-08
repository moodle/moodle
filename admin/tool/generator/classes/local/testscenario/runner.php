<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace tool_generator\local\testscenario;

use behat_data_generators;
use Behat\Gherkin\Parser;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Keywords\ArrayKeywords;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class to process a scenario generator file.
 *
 * @package    tool_generator
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class runner {

    /** @var behat_data_generators the behat data generator instance. */
    private behat_data_generators $generator;

    /** @var array of valid steps indexed by given expression tag. */
    private array $validsteps;

    /**
     * Initi all composer, behat libraries and load the valid steps.
     */
    public function init() {
        $this->include_composer_libraries();
        $this->include_behat_libraries();
        $this->load_generator();
    }

    /**
     * Include composer autload.
     */
    public function include_composer_libraries() {
        global $CFG;
        if (!file_exists($CFG->dirroot . '/vendor/autoload.php')) {
            throw new \moodle_exception('Missing composer.');
        }
        require_once($CFG->dirroot . '/vendor/autoload.php');
        return true;
    }

    /**
     * Include all necessary behat libraries.
     */
    public function include_behat_libraries() {
        global $CFG;
        if (!class_exists('Behat\Gherkin\Lexer')) {
            throw new \moodle_exception('Missing behat classes.');
        }
        // Behat utilities.
        require_once($CFG->libdir . '/behat/classes/util.php');
        require_once($CFG->libdir . '/behat/classes/behat_command.php');
        require_once($CFG->libdir . '/behat/behat_base.php');
        require_once("{$CFG->libdir}/tests/behat/behat_data_generators.php");
        return true;
    }

    /**
     * Load all generators.
     */
    private function load_generator() {
        $this->generator = new behat_data_generators();
        $this->validsteps = $this->scan_generator($this->generator);
    }

    /**
     * Scan a generator to get all valid steps.
     * @param behat_data_generators $generator the generator to scan.
     * @return array the valid steps.
     */
    private function scan_generator(behat_data_generators $generator): array {
        $result = [];
        $class = new ReflectionClass($generator);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $given = $this->get_method_given($method);
            if ($given) {
                $result[$given] = $method->getName();
            }
        }
        return $result;
    }

    /**
     * Get the given expression tag of a method.
     *
     * @param ReflectionMethod $method the method to get the given expression tag.
     * @return string|null the given expression tag or null if not found.
     */
    private function get_method_given(ReflectionMethod $method): ?string {
        $doccomment = $method->getDocComment();
        $doccomment = str_replace("\r\n", "\n", $doccomment);
        $doccomment = str_replace("\r", "\n", $doccomment);
        $doccomment = explode("\n", $doccomment);
        foreach ($doccomment as $line) {
            $matches = [];
            if (preg_match('/.*\@(given|when|then)\s+(.+)$/i', $line, $matches)) {
                return $matches[2];
            }
        }
        return null;
    }

    /**
     * Parse a feature file.
     * @param string $content the feature file content.
     * @return parsedfeature
     */
    public function parse_feature(string $content): parsedfeature {
        $result = new parsedfeature();

        $parser = $this->get_parser();
        $feature = $parser->parse($content);

        // No need for background in testing scenarios because scenarios can only contain generators.
        // In the future the background can be used to define clean up steps (when clean up methods
        // are implemented).
        if ($feature->hasScenarios()) {
            $scenarios = $feature->getScenarios();
            foreach ($scenarios as $scenario) {
                if ($scenario->getNodeType() == 'Outline') {
                    $result->add_scenario($scenario->getNodeType(), $scenario->getTitle());
                    $result->add_error(get_string('testscenario_outline', 'tool_generator'));
                    continue;
                }
                $result->add_scenario($scenario->getNodeType(), $scenario->getTitle());
                $steps = $scenario->getSteps();
                foreach ($steps as $step) {
                    $result->add_step(new steprunner($this->generator, $this->validsteps, $step));
                }
            }
        }
        return $result;
    }

    /**
     * Get the parser.
     * @return Parser
     */
    private function get_parser(): Parser {
        $keywords = new ArrayKeywords([
            'en' => [
                'feature' => 'Feature',
                // If in the future we have clean up steps, background will be renamed to "Clean up".
                'background' => 'Background',
                'scenario' => 'Scenario',
                'scenario_outline' => 'Scenario Outline|Scenario Template',
                'examples' => 'Examples|Scenarios',
                'given' => 'Given',
                'when' => 'When',
                'then' => 'Then',
                'and' => 'And',
                'but' => 'But',
            ],
        ]);
        $lexer = new Lexer($keywords);
        $parser = new Parser($lexer);
        return $parser;
    }

    /**
     * Execute a parsed feature.
     * @param parsedfeature $parsedfeature the parsed feature to execute.
     * @return bool true if all steps were executed successfully.
     */
    public function execute(parsedfeature $parsedfeature): bool {
        if (!$parsedfeature->is_valid()) {
            return false;
        }
        $result = true;
        $steps = $parsedfeature->get_all_steps();
        foreach ($steps as $step) {
            $result = $step->execute() && $result;
        }
        return $result;
    }
}
