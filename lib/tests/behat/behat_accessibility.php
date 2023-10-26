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
 * Steps definitions to open and close action menus.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Behat\Mink\Exception\{DriverException, ExpectationException};

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

/**
 * Steps definitions to assist with accessibility testing.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_accessibility extends behat_base {

    /**
     * Run the axe-core accessibility tests.
     *
     * There are standard tags to ensure WCAG 2.1 A, WCAG 2.1 AA, and Section 508 compliance.
     * It is also possible to specify any desired optional tags.
     *
     * The list of available tags can be found at
     * https://github.com/dequelabs/axe-core/blob/v3.5.5/doc/rule-descriptions.md.
     *
     * @Then the page should meet accessibility standards
     * @Then the page should meet accessibility standards with :extratags extra tests
     * @Then the page should meet :standardtags accessibility standards
     * @param   string $standardtags Comma-separated list of standard tags to run
     * @param   string $extratags Comma-separated list of tags to run in addition to the standard tags
     */
    public function run_axe_validation_for_tags(string $standardtags = '', string $extratags = ''): void {
        $this->run_axe_for_tags(
            // Turn the comma-separated string into an array of trimmed values, filtering out empty values.
            array_filter(array_map('trim', explode(',', $standardtags))),
            array_filter(array_map('trim', explode(',', $extratags)))
        );
    }

    /**
     * Run the Axe tests.
     *
     * See https://github.com/dequelabs/axe-core/blob/develop/doc/rule-descriptions.md for details of the supported
     * tags.
     *
     * @param   array $standardtags The list of standard tags to run
     * @param   array $extratags The list of tags, in addition to the standard tags, to run
     */
    protected function run_axe_for_tags(array $standardtags = [], array $extratags = []): void {
        if (!behat_config_manager::get_behat_run_config_value('axe')) {
            return;
        }

        if (!$this->has_tag('accessibility')) {
            throw new DriverException(
                'Accessibility tests using Axe must have the @accessibility tag on either the scenario or feature.'
            );
        }

        $this->require_javascript();

        $axeurl = (new \moodle_url('/lib/behat/axe/axe.min.js'))->out(false);
        $axeconfig = $this->get_axe_config_for_tags($standardtags, $extratags);
        $runaxe = <<<EOF
(axeurl => {
    const runTests = () => {
        const axeTag = document.querySelector('script[data-purpose="axe"]');
        axeTag.dataset.results = null;

        axe.run({$axeconfig})
        .then(results => {
            axeTag.dataset.results = JSON.stringify({
                violations: results.violations,
                exception: null,
            });
        })
        .catch(exception => {
            axeTag.dataset.results = JSON.stringify({
                violations: [],
                exception: exception,
            });
        });
    };

    if (document.querySelector('script[data-purpose="axe"]')) {
        runTests();
    } else {
        // Inject the axe content.
        const axeTag = document.createElement('script');
        axeTag.src = axeurl,
        axeTag.dataset.purpose = 'axe';

        axeTag.onload = () => runTests();
        document.head.append(axeTag);
    }
})('{$axeurl}');
EOF;

        $this->execute_script($runaxe);

        $getresults = <<<EOF
return (() => {
    const axeTag = document.querySelector('script[data-purpose="axe"]');
    return axeTag.dataset.results;
})()
EOF;

        for ($i = 0; $i < self::get_extended_timeout() * 10; $i++) {
            $results = json_decode($this->evaluate_script($getresults) ?? '');
            if ($results) {
                break;
            }
        }

        if (empty($results)) {
            throw new \Exception('No data');
        }

        if ($results->exception !== null) {
            throw new ExpectationException($results->exception, $this->session);
        }

        $violations = $results->violations;
        if (!count($violations)) {
            return;
        }

        $violationdata = "Accessibility violations found:\n";
        foreach ($violations as $violation) {
            $nodedata = '';
            foreach ($violation->nodes as $node) {
                $failedchecks = [];
                foreach (array_merge($node->any, $node->all, $node->none) as $check) {
                    $failedchecks[$check->id] = $check->message;
                }

                $nodedata .= sprintf(
                    "    - %s:\n      %s\n\n",
                    implode(', ', $failedchecks),
                    implode("\n      ", $node->target)
                );
            }

            $violationdata .= sprintf(
                "  %.03d violations of '%s' (severity: %s)\n%s\n",
                count($violation->nodes),
                $violation->description,
                $violation->impact,
                $nodedata
            );
        }

        throw new ExpectationException($violationdata, $this->getSession());
    }

    /**
     * Get the configuration to use with Axe.
     *
     * See https://github.com/dequelabs/axe-core/blob/develop/doc/rule-descriptions.md for details of the rules.
     *
     * @param   array|null $standardtags The list of standard tags to run
     * @param   array|null $extratags The list of tags, in addition to the standard tags, to run
     * @return  string The JSON-encoded configuration.
     */
    protected function get_axe_config_for_tags(?array $standardtags = null, ?array $extratags = null): string {
        if (empty($standardtags)) {
            $standardtags = [
                // Meet WCAG 2.1 A requirements.
                'wcag2a',

                // Meet WCAG 2.1 AA requirements.
                'wcag2aa',

                // Meet Section 508 requirements.
                // See https://www.epa.gov/accessibility/what-section-508 for detail.
                'section508',

                // Ensure that ARIA attributes are correctly defined.
                'cat.aria',

                // Requiremetns for sensory and visual cues.
                // These largely related to viewport scale and zoom functionality.
                'cat.sensory-and-visual-cues',

                // Meet WCAG 1.3.4 requirements for orientation.
                // See https://www.w3.org/WAI/WCAG21/Understanding/orientation.html for detail.
                'wcag134',
            ];
        }

        return json_encode([
            'runOnly' => [
                'type' > 'tag',
                'values' => array_merge($standardtags, $extratags),
            ],
        ]);
    }
}
