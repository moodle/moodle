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

use behat_admin;
use behat_data_generators;
use behat_base;
use behat_course;
use behat_general;
use behat_user;
use core\attribute_helper;
use Behat\Gherkin\Parser;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Node\OutlineNode;
use ReflectionClass;
use ReflectionMethod;
use stdClass;

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
        $this->load_cleanup();
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

        // Behat constant.
        if (!defined('BEHAT_TEST')) {
            define('BEHAT_TEST', 1);
        }

        // Behat utilities.
        require_once($CFG->libdir . '/behat/classes/util.php');
        require_once($CFG->libdir . '/behat/classes/behat_command.php');
        require_once($CFG->libdir . '/behat/behat_base.php');
        require_once("{$CFG->libdir}/tests/behat/behat_data_generators.php");
        require_once("{$CFG->dirroot}/admin/tests/behat/behat_admin.php");
        require_once("{$CFG->dirroot}/course/lib.php");
        require_once("{$CFG->dirroot}/course/tests/behat/behat_course.php");
        require_once("{$CFG->dirroot}/lib/tests/behat/behat_general.php");
        require_once("{$CFG->dirroot}/user/tests/behat/behat_user.php");
        return true;
    }

    /**
     * Load all generators.
     */
    private function load_generator() {
        $this->generator = new behat_data_generators();
        $this->validsteps = $this->scan_generator($this->generator);

        // Add some extra steps from other classes.
        $extrasteps = [
            [behat_admin::class, 'the_following_config_values_are_set_as_admin'],
            [behat_general::class, 'i_enable_plugin'],
            [behat_general::class, 'i_disable_plugin'],
        ];
        foreach ($extrasteps as $callable) {
            $classname = $callable[0];
            $method = $callable[1];
            $extra = $this->scan_method(
                new ReflectionMethod($classname, $method),
                new $classname(),
            );
            if ($extra) {
                $this->validsteps[$extra->given] = $extra;
            }
        }
    }

    /**
     * Load all cleanup steps.
     */
    private function load_cleanup() {
        $extra = $this->scan_method(
            new ReflectionMethod(behat_course::class, 'the_course_is_deleted'),
            new behat_course(),
        );
        if ($extra) {
            $this->validsteps[$extra->given] = $extra;
        }

        $extra = $this->scan_method(
            new ReflectionMethod(behat_user::class, 'the_user_is_deleted'),
            new behat_user(),
        );
        if ($extra) {
            $this->validsteps[$extra->given] = $extra;
        }
    }

    /**
     * Get all valid steps.
     * @return array the valid steps.
     */
    public function get_valid_steps(): array {
        return array_values($this->validsteps);
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
            $scan = $this->scan_method($method, $generator);
            if ($scan) {
                $result[$scan->given] = $scan;
            }
        }
        return $result;
    }

    /**
     * Scan a method to get the given expression tag.
     * @param ReflectionMethod $method the method to scan.
     * @param behat_base $behatclass the behat class instance to use.
     * @return stdClass|null the method data (given, name, class).
     */
    private function scan_method(ReflectionMethod $method, behat_base $behatclass): ?stdClass {
        $given = $this->get_method_given($method);
        if (!$given) {
            return null;
        }
        $result = (object)[
            'given' => $given,
            'name' => $method->getName(),
            'generator' => $behatclass,
            'example' => null,
        ];
        $reference = $method->getDeclaringClass()->getName() . '::' . $method->getName();
        if ($attribute = attribute_helper::instance($reference, \core\attribute\example::class)) {
            $result->example = (string) $attribute->example;
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
        return $this->parse_selected_scenarios($content);
    }

    /**
     * Parse all feature file scenarios.
     *
     * Note: if no filter is passed, it will execute only the scenarios that are not tagged.
     *
     * @param string $content the feature file content.
     * @param string $filtertag the tag to filter the scenarios.
     * @return parsedfeature
     */
    private function parse_selected_scenarios(string $content, ?string $filtertag = null): parsedfeature {
        $result = new parsedfeature();

        $parser = $this->get_parser();
        $feature = $parser->parse($content);

        // No need for background in testing scenarios because scenarios can only contain generators.
        // In the future the background can be used to define clean up steps (when clean up methods
        // are implemented).
        if ($feature->hasScenarios()) {
            $scenarios = $feature->getScenarios();
            foreach ($scenarios as $scenario) {
                // By default, we only execute scenaros that are not tagged.
                if (empty($filtertag) && !empty($scenario->getTags())) {
                    continue;
                }
                if ($filtertag && !in_array($filtertag, $scenario->getTags())) {
                    continue;
                }
                if ($scenario->getNodeType() == 'Outline') {
                    $this->parse_scenario_outline($scenario, $result);
                    continue;
                }
                $result->add_scenario($scenario->getNodeType(), $scenario->getTitle());
                $steps = $scenario->getSteps();
                foreach ($steps as $step) {
                    $result->add_step(new steprunner(null, $this->validsteps, $step));
                }
            }
        }
        return $result;
    }

    /**
     * Parse a feature file using only the scenarios with cleanup tag.
     * @param string $content the feature file content.
     * @return parsedfeature
     */
    public function parse_cleanup(string $content): parsedfeature {
        return $this->parse_selected_scenarios($content, 'cleanup');
    }

    /**
     * Parse a scenario outline.
     * @param OutlineNode $scenario the scenario outline to parse.
     * @param parsedfeature $result the parsed feature to add the scenario.
     */
    private function parse_scenario_outline(OutlineNode $scenario, parsedfeature $result) {
        $count = 1;
        foreach ($scenario->getExamples() as $example) {
            $result->add_scenario($example->getNodeType(), $example->getOutlineTitle() . " ($count)");
            $steps = $example->getSteps();
            foreach ($steps as $step) {
                $result->add_step(new steprunner(null, $this->validsteps, $step));
            }
            $count++;
        }
    }

    /**
     * Get the parser.
     * @return Parser
     */
    private function get_parser(): Parser {
        $keywords = new ArrayKeywords([
            'en' => [
                'feature' => 'Feature',
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
