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
 * Class cli_helper
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_uploaduser;

defined('MOODLE_INTERNAL') || die();

use tool_uploaduser\local\cli_progress_tracker;

require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/lib.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->libdir.'/csvlib.class.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/locallib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/uploaduser/user_form.php');
require_once($CFG->libdir . '/clilib.php');

/**
 * Helper method for CLI script to upload users (also has special wrappers for cli* functions for phpunit testing)
 *
 * @package     tool_uploaduser
 * @copyright   2020 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cli_helper {

    /** @var string */
    protected $operation;
    /** @var array */
    protected $clioptions;
    /** @var array */
    protected $unrecognized;
    /** @var string */
    protected $progresstrackerclass;

    /** @var process */
    protected $process;

    /**
     * cli_helper constructor.
     *
     * @param string|null $progresstrackerclass
     */
    public function __construct(?string $progresstrackerclass = null) {
        $this->progresstrackerclass = $progresstrackerclass ?? cli_progress_tracker::class;
        $optionsdefinitions = $this->options_definitions();
        $longoptions = [];
        $shortmapping = [];
        foreach ($optionsdefinitions as $key => $option) {
            $longoptions[$key] = $option['default'];
            if (!empty($option['alias'])) {
                $shortmapping[$option['alias']] = $key;
            }
        }

        list($this->clioptions, $this->unrecognized) = cli_get_params(
            $longoptions,
            $shortmapping
        );
    }

    /**
     * Options used in this CLI script
     *
     * @return array
     */
    protected function options_definitions(): array {
        $options = [
            'help' => [
                'hasvalue' => false,
                'description' => get_string('clihelp', 'tool_uploaduser'),
                'default' => 0,
                'alias' => 'h',
            ],
            'file' => [
                'hasvalue' => 'PATH',
                'description' => get_string('clifile', 'tool_uploaduser'),
                'default' => null,
                'validation' => function($file) {
                    if (!$file) {
                        $this->cli_error(get_string('climissingargument', 'tool_uploaduser', 'file'));
                    }
                    if ($file && (!file_exists($file) || !is_readable($file))) {
                        $this->cli_error(get_string('clifilenotreadable', 'tool_uploaduser', $file));
                    }
                }
            ],
        ];
        $form = new \admin_uploaduser_form1();
        [$elements, $defaults] = $form->get_form_for_cli();
        $options += $this->prepare_form_elements_for_cli($elements, $defaults);
        $form = new \admin_uploaduser_form2(null, ['columns' => ['type1'], 'data' => []]);
        [$elements, $defaults] = $form->get_form_for_cli();
        $options += $this->prepare_form_elements_for_cli($elements, $defaults);
        return $options;
    }

    /**
     * Print help for export
     */
    public function print_help(): void {
        $this->cli_writeln(get_string('clititle', 'tool_uploaduser'));
        $this->cli_writeln('');
        $this->print_help_options($this->options_definitions());
        $this->cli_writeln('');
        $this->cli_writeln('Example:');
        $this->cli_writeln('$sudo -u www-data /usr/bin/php admin/tool/uploaduser/cli/uploaduser.php --file=PATH');
    }

    /**
     * Get CLI option
     *
     * @param string $key
     * @return mixed|null
     */
    public function get_cli_option(string $key) {
        return $this->clioptions[$key] ?? null;
    }

    /**
     * Write a text to the given stream
     *
     * @param string $text text to be written
     */
    protected function cli_write($text): void {
        if (PHPUNIT_TEST) {
            echo $text;
        } else {
            cli_write($text);
        }
    }

    /**
     * Write error notification
     * @param string $text
     * @return void
     */
    protected function cli_problem($text): void {
        if (PHPUNIT_TEST) {
            echo $text;
        } else {
            cli_problem($text);
        }
    }

    /**
     * Write a text followed by an end of line symbol to the given stream
     *
     * @param string $text text to be written
     */
    protected function cli_writeln($text): void {
        $this->cli_write($text . PHP_EOL);
    }

    /**
     * Write to standard error output and exit with the given code
     *
     * @param string $text
     * @param int $errorcode
     * @return void (does not return)
     */
    protected function cli_error($text, $errorcode = 1): void {
        $this->cli_problem($text);
        $this->die($errorcode);
    }

    /**
     * Wrapper for "die()" method so we can unittest it
     *
     * @param mixed $errorcode
     * @throws \moodle_exception
     */
    protected function die($errorcode): void {
        if (!PHPUNIT_TEST) {
            die($errorcode);
        } else {
            throw new \moodle_exception('CLI script finished with error code '.$errorcode);
        }
    }

    /**
     * Display as CLI table
     *
     * @param array $column1
     * @param array $column2
     * @param int $indent
     * @return string
     */
    protected function convert_to_table(array $column1, array $column2, int $indent = 0): string {
        $maxlengthleft = 0;
        $left = [];
        $column1 = array_values($column1);
        $column2 = array_values($column2);
        foreach ($column1 as $i => $l) {
            $left[$i] = str_repeat(' ', $indent) . $l;
            if (strlen('' . $column2[$i])) {
                $maxlengthleft = max($maxlengthleft, strlen($l) + $indent);
            }
        }
        $maxlengthright = 80 - $maxlengthleft - 1;
        $output = '';
        foreach ($column2 as $i => $r) {
            if (!strlen('' . $r)) {
                $output .= $left[$i] . "\n";
                continue;
            }
            $right = wordwrap($r, $maxlengthright, "\n");
            $output .= str_pad($left[$i], $maxlengthleft) . ' ' .
                str_replace("\n", PHP_EOL . str_repeat(' ', $maxlengthleft + 1), $right) . PHP_EOL;
        }
        return $output;
    }

    /**
     * Display available CLI options as a table
     *
     * @param array $options
     */
    protected function print_help_options(array $options): void {
        $left = [];
        $right = [];
        foreach ($options as $key => $option) {
            if ($option['hasvalue'] !== false) {
                $l = "--$key={$option['hasvalue']}";
            } else if (!empty($option['alias'])) {
                $l = "-{$option['alias']}, --$key";
            } else {
                $l = "--$key";
            }
            $left[] = $l;
            $right[] = $option['description'];
        }
        $this->cli_write('Options:' . PHP_EOL . $this->convert_to_table($left, $right));
    }

    /**
     * Process the upload
     */
    public function process(): void {
        // First, validate all arguments.
        $definitions = $this->options_definitions();
        foreach ($this->clioptions as $key => $value) {
            if ($validator = $definitions[$key]['validation'] ?? null) {
                $validator($value);
            }
        }

        // Read the CSV file.
        $iid = \csv_import_reader::get_new_iid('uploaduser');
        $cir = new \csv_import_reader($iid, 'uploaduser');
        $cir->load_csv_content(file_get_contents($this->get_cli_option('file')),
            $this->get_cli_option('encoding'), $this->get_cli_option('delimiter_name'));
        $csvloaderror = $cir->get_error();

        if (!is_null($csvloaderror)) {
            $this->cli_error(get_string('csvloaderror', 'error', $csvloaderror), 1);
        }

        // Start upload user process.
        $this->process = new \tool_uploaduser\process($cir, $this->progresstrackerclass);
        $filecolumns = $this->process->get_file_columns();

        $form = $this->mock_form(['columns' => $filecolumns, 'data' => ['iid' => $iid, 'previewrows' => 1]], $this->clioptions);

        if (!$form->is_validated()) {
            $errors = $form->get_validation_errors();
            $this->cli_error(get_string('clivalidationerror', 'tool_uploaduser') . PHP_EOL .
                $this->convert_to_table(array_keys($errors), array_values($errors), 2));
        }

        $this->process->set_form_data($form->get_data());
        $this->process->process();
    }

    /**
     * Mock form submission
     *
     * @param array $customdata
     * @param array $submitteddata
     * @return \admin_uploaduser_form2
     */
    protected function mock_form(array $customdata, array $submitteddata): \admin_uploaduser_form2 {
        global $USER;
        $submitteddata['description'] = ['text' => $submitteddata['description'], 'format' => FORMAT_HTML];

        // Now mock the form submission.
        $submitteddata['_qf__admin_uploaduser_form2'] = 1;
        $oldignoresesskey = $USER->ignoresesskey ?? null;
        $USER->ignoresesskey = true;
        $form = new \admin_uploaduser_form2(null, $customdata, 'post', '', [], true, $submitteddata);
        $USER->ignoresesskey = $oldignoresesskey;

        $form->set_data($submitteddata);
        return $form;
    }

    /**
     * Prepare form elements for CLI
     *
     * @param \HTML_QuickForm_element[] $elements
     * @param array $defaults
     * @return array
     */
    protected function prepare_form_elements_for_cli(array $elements, array $defaults): array {
        $options = [];
        foreach ($elements as $element) {
            if ($element instanceof \HTML_QuickForm_submit || $element instanceof \HTML_QuickForm_static) {
                continue;
            }
            $type = $element->getType();
            if ($type === 'html' || $type === 'hidden' || $type === 'header') {
                continue;
            }

            $name = $element->getName();
            if ($name === null || preg_match('/^mform_isexpanded_/', $name)
                || preg_match('/^_qf__/', $name)) {
                continue;
            }

            $label = $element->getLabel();
            if (!strlen($label) && method_exists($element, 'getText')) {
                $label = $element->getText();
            }
            $default = $defaults[$element->getName()] ?? null;

            $postfix = '';
            $possiblevalues = null;
            if ($element instanceof \HTML_QuickForm_select) {
                $selectoptions = $element->_options;
                $possiblevalues = [];
                foreach ($selectoptions as $option) {
                    $possiblevalues[] = '' . $option['attr']['value'];
                }
                if (count($selectoptions) < 10) {
                    $postfix .= ':';
                    foreach ($selectoptions as $option) {
                        $postfix .= "\n  ".$option['attr']['value']." - ".$option['text'];
                    }
                }
                if (!array_key_exists($name, $defaults)) {
                    $firstoption = reset($selectoptions);
                    $default = $firstoption['attr']['value'];
                }
            }

            if ($element instanceof \HTML_QuickForm_checkbox) {
                $postfix = ":\n  0|1";
                $possiblevalues = ['0', '1'];
            }

            if ($default !== null & $default !== '') {
                $postfix .= "\n  ".get_string('clidefault', 'tool_uploaduser')." ".$default;
            }
            $options[$name] = [
                'hasvalue' => 'VALUE',
                'description' => $label.$postfix,
                'default' => $default,
            ];
            if ($possiblevalues !== null) {
                $options[$name]['validation'] = function($v) use ($possiblevalues, $name) {
                    if (!in_array('' . $v, $possiblevalues)) {
                        $this->cli_error(get_string('clierrorargument', 'tool_uploaduser',
                            (object)['name' => $name, 'values' => join(', ', $possiblevalues)]));
                    }
                };
            }
        }
        return $options;
    }

    /**
     * Get process statistics.
     *
     * @return array
     */
    public function get_stats(): array {
        return $this->process->get_stats();
    }
}
