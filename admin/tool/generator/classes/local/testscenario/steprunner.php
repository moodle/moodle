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
use Behat\Gherkin\Node\StepNode;

/**
 * Class to validate and process a scenario step.
 *
 * @package    tool_generator
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class steprunner {
    /** @var behat_data_generators the behat data generator instance. */
    private behat_data_generators $generator;

    /** @var array the valid steps indexed by given expression tag. */
    private array $validsteps;

    /** @var StepNode the step node to process. */
    private StepNode $stepnode;

    /** @var string|null the generator method to call. */
    private ?string $method = null;

    /** @var array the parameters to pass to the generator method. */
    private array $params = [];

    /** @var bool if the step is valid. */
    private bool $isvalid = false;

    /** @var bool if the step has been executed. */
    private bool $executed = false;

    /** @var string the error message if any. */
    private string $error = '';

    /**
     * Constructor.
     * @param behat_data_generators $generator the behat data generator instance.
     * @param array $validsteps the valid steps indexed by given expression tag.
     * @param StepNode $stepnode the step node to process.
     */
    public function __construct(behat_data_generators $generator, array $validsteps, StepNode $stepnode) {
        $this->generator = $generator;
        $this->validsteps = $validsteps;
        $this->stepnode = $stepnode;
        $this->init();
    }

    /**
     * Init the step runner.
     *
     * This method will check if the step is valid and all the needed information
     * in case it is executed.
     */
    private function init() {
        $matches = [];
        $linetext = $this->stepnode->getText();
        foreach ($this->validsteps as $pattern => $method) {
            if (!$this->match_given($pattern, $linetext, $matches)) {
                continue;
            }
            $this->method = $method;
            $this->params = $this->build_method_params($method, $matches);
            $this->isvalid = true;
            return;
        }
        $this->error = get_string('testscenario_invalidstep', 'tool_generator');
    }

    /**
     * Build the method parameters.
     * @param string $methodname the method name.
     * @param array $matches the matches.
     * @return array the method parameters.
     */
    private function build_method_params($methodname, $matches) {
        $method = new \ReflectionMethod($this->generator, $methodname);
        $params = [];
        foreach ($method->getParameters() as $param) {
            $paramname = $param->getName();
            if (isset($matches[$paramname])) {
                $params[] = $matches[$paramname];
                unset($matches[$paramname]);
            } else if (count($matches) > 0) {
                // If the param is not present means the regular expressions does not use
                // proper names. So we will try to find the param by position.
                $params[] = array_pop($matches);
            } else {
                // No more params to match.
                break;
            }
        }
        return array_merge($params, $this->stepnode->getArguments());
    }

    /**
     * Return if the step is valid.
     * @return bool
     */
    public function is_valid(): bool {
        return $this->isvalid;
    }

    /**
     * Return if the step has been executed.
     * @return bool
     */
    public function is_executed(): bool {
        return $this->executed;
    }

    /**
     * Return the step text.
     * @return string
     */
    public function get_text(): string {
        return $this->stepnode->getText();
    }

    /**
     * Return the step error message.
     * @return string
     */
    public function get_error(): string {
        return $this->error;
    }

    /**
     * Return the step arguments as string.
     * @return string
     */
    public function get_arguments_string(): string {
        $result = '';
        foreach ($this->stepnode->getArguments() as $argument) {
            $result .= $argument->getTableAsString();
        }
        return $result;
    }

    /**
     * Match a given expression with a text.
     * @param string $pattern the given expression.
     * @param string $text the text to match.
     * @param array $matches the matches.
     * @return bool if the step matched the generator given expression.
     */
    private function match_given(string $pattern, $text, array &$matches) {
        $internalmatcher = [];
        if (substr($pattern, 0, 1) === '/') {
            // Pattern is a regular expression.
            $result = preg_match($pattern, $text, $matches);
            foreach ($matches as $key => $value) {
                if (is_int($key)) {
                    unset($matches[$key]);
                }
            }
            return $result;
        }

        // Patter is a string with parameters.
        $elementmatches = [];
        preg_match_all('/:([^ ]+)/', $pattern, $elementmatches, PREG_SET_ORDER, 0);

        $pattern = preg_replace('/:([^ ]+)/', '(?P<$1>"[^"]+"|[^" ]+)', $pattern);
        $pattern = '/^' . $pattern . '$/';
        $result = preg_match($pattern, $text, $internalmatcher);
        if (!$result) {
            return false;
        }
        foreach ($elementmatches as $elementmatch) {
            // Remove any possible " at the beggining and end of $internalmatcher[$elementmatch[1]].
            $paramvalue = preg_replace('/^"(.*)"$/', '$1', $internalmatcher[$elementmatch[1]]);
            $matches[$elementmatch[1]] = $paramvalue;
        }
        return true;
    }

    /**
     * Execute the step.
     * @return bool if the step is executed or not.
     */
    public function execute(): bool {
        if (!$this->isvalid) {
            return false;
        }
        $this->executed = true;
        try {
            call_user_func_array(
                [$this->generator, $this->method],
                $this->params
            );
        } catch (\moodle_exception $exception) {
            $this->error = $exception->getMessage();
            $this->isvalid = false;
            return false;
        }
        return true;
    }
}
