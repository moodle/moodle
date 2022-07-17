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

namespace tool_pluginskel\local\skel;

/**
 * Represents a file that implements a single external function.
 *
 * @package     tool_pluginskel
 * @subpackage  skel
 * @copyright   2021 David Mudrák <david@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external_function_file extends php_single_file {

    /**
     * Generate the skeleton of the external function implementation.
     *
     * @param array $external Decode recipe data describing one particular external function.
     */
    public function generate_external_function_code(array $external): void {

        if (empty($this->data)) {
            throw new coding_exception('Skeleton data not set');
        }

        $this->set_attribute('has_extra_requirements');

        $this->data['self']['classname'] = $external['name'];
        $this->data['self']['execute_parameters'] = $this->generate_execute_parameters($external);
        $this->data['self']['execute_returns'] = $this->generate_execute_returns($external);

        [
            'phpdocs' => $this->data['self']['execute_phpdoc'],
            'signature' => $this->data['self']['execute_signature'],
        ] = $this->execute_phpdocs_signature($external);

        $this->data['self']['execute_signature'] = join(', ', $this->data['self']['execute_signature']);
        $this->data['self']['execute_args'] = array_keys($this->top_level_parameters($external));
        $this->data['self']['has_execute_args'] = !empty($this->data['self']['execute_args']);
    }

    /**
     * Generate the code to be inserted into the body of the execute_parameters() method of the class.
     *
     * @param array $external
     * @return string
     */
    protected function generate_execute_parameters(array $external): string {

        if (empty($external['parameters'])) {
            return '';
        }

        $code = "\n";

        foreach ($external['parameters'] as $param) {
            $code .= $this->generate_external_description_item($param, 12);
            $code .= ",\n";
        }

        $code .= str_repeat(' ', 8);

        return $code;
    }

    /**
     * Get the list of PHP params of the execute() method of the class.
     *
     * @param array $external
     * @return array
     */
    protected function top_level_parameters(array $external): array {

        if (empty($external['parameters']) || !is_array($external['parameters'])) {
            return [];

        } else {
            $params = [];
            foreach ($external['parameters'] as $param) {
                if (empty($param['name'])) {
                    $this->logger->error('All top level parameters must be named');
                    return [];
                }

                if (isset($param['multiple']) || isset($param['single'])) {
                    $params[$param['name']] = 'array';

                } else {
                    $params[$param['name']] = $param['type'] ?? null;
                }
            }
        }

        return $params;
    }

    /**
     * Get the list of top-level parameters of the execute() method to be put into its phpdoc and signature.
     *
     * @param array $external
     * @return array ['phpdocs' => (array) $phpdocs, 'signature' => (array) $signature]
     */
    protected function execute_phpdocs_signature(array $external): array {

        $phpdocs = [];
        $signature = [];

        foreach ($this->top_level_parameters($external) as $name => $type) {
            switch ($type) {
                case 'array':
                    $phpdocs[] = '     * @param array $' . $name;
                    $signature[] = 'array $' . $name;
                    break;

                case 'PARAM_INT':
                    $phpdocs[] = '     * @param int $' . $name;
                    $signature[] = 'int $' . $name;
                    break;

                case 'PARAM_BOOL':
                    $phpdocs[] = '     * @param bool $' . $name;
                    $signature[] = 'bool $' . $name;
                    break;

                case 'PARAM_RAW':
                case 'PARAM_TEXT':
                case 'PARAM_ALPHA':
                    $phpdocs[] = '     * @param string $' . $name;
                    $signature[] = 'string $' . $name;
                    break;

                default:
                    $phpdocs[] = '     * @param mixed $' . $name;
                    $signature[] = '$' . $name;
            }
        }

        return [
            'phpdocs' => $phpdocs,
            'signature' => $signature,
        ];
    }

    /**
     * Generate the code describing the structure of the external function result.
     *
     * @param array $external
     * @return string
     */
    protected function generate_execute_returns(array $external): string {

        if (empty($external['returns'])) {
            // Null means void result or result is ignored.
            return 'null';
        }

        if (!is_array($external['returns']) || count($external['returns']) > 1) {
            $this->logger->error('External must return just one structure or null');
            return 'false // TODO Unable to generate valid return structure from the recipe.';
        }

        $param = array_shift($external['returns']);

        if (isset($param['name'])) {
            $this->logger->warning('Returned structure should not be named');
            unset($param['name']);
        }

        return trim($this->generate_external_description_item($param, 8));
    }

    /**
     * Helper function to recursively generate tree of items in the execute_parameters() body.
     *
     * @param array $param
     * @param int $indent
     * @return string
     */
    protected function generate_external_description_item(array $param, int $indent): string {

        $code = str_repeat(' ', $indent);

        if (empty($param)) {
            return $code;
        }

        if (isset($param['name'])) {
            $code .= "'" . $param['name'] . "' => ";
        }

        if (!empty($param['multiple']) && is_array($param['multiple'])) {
            if (count($param['multiple']) > 1) {
                $this->logger->error('External multiple structure can specify only one repeating sub-structure');
                return '// TODO Unable to generate valid external_multiple_structure from the recipe.';
            }

            $sub = array_shift($param['multiple']);

            if (isset($sub['name'])) {
                $this->logger->warning('External multiple structure should not contain named sub-structures');
                unset($sub['name']);
            }

            $code .= "new external_multiple_structure(\n";
            $code .= $this->generate_external_description_item($sub, $indent + 4);

            $args = $this->generate_external_description_required_default($param);

            if (!empty($param['desc']) || !empty($args)) {
                $code .= ",\n";
                $code .= str_repeat(' ', $indent + 4);
                $code .= "'" . ($param['desc'] ?? '') . "'" . $args;
            }

            $code .= "\n";
            $code .= str_repeat(' ', $indent);
            $code .= ")";

        } else if (!empty($param['single']) && is_array($param['single'])) {
            $code .= "new external_single_structure([\n";
            foreach ($param['single'] as $sub) {
                $code .= $this->generate_external_description_item($sub, $indent + 4);
                $code .= ",\n";
            }
            $code .= str_repeat(' ', $indent);
            $code .= "]";
            $args = $this->generate_external_description_required_default($param);

            if (!empty($param['desc']) || !empty($args)) {
                $code .= ", '" . ($param['desc'] ?? '') . "'" . $args;
            }

            $code .= ")";

        } else {
            if (empty($param['type'])) {
                $this->logger->error('PARAM type not specified for external value');
                return '// TODO Unable to generate valid external_value from the recipe.';
            }

            if (empty($param['desc'])) {
                $this->logger->warning('External value description not specified');
                $param['desc'] = '';
            }

            $code .= "new external_value(" . $param['type'] . ", '" . $param['desc'] . "'";
            $code .= $this->generate_external_description_required_default($param);
            $code .= ")";
        }

        return $code;
    }

    /**
     * Helper function to generate value's requirement and default arguments.
     *
     * Returns string like ', VALUE_DEFAULT, null' depending on 'required' and 'default' recipe values.  Please note the
     * API itself allows to distinguish between VALUE_DEFAULT and VALUE_OPTIONAL. This generator does not generate
     * VALUE_OPTIONAL values and the 'required' is considered as a boolean flag in the recipe.
     *
     * @param array $param
     * @return string
     */
    protected function generate_external_description_required_default(array $param): string {

        $code = '';

        if (isset($param['required']) && empty($param['required'])) {
            $code .= ", VALUE_DEFAULT";

            if (isset($param['default'])) {
                $code .= ", " . $param['default'];

            } else {
                $code .= ", null";
            }
        }

        return $code;
    }
}
