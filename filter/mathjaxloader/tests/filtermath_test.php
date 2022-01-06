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
 * Provides the {@link filter_mathjaxloader_filtermath_testcase} class.
 *
 * @package     filter_mathjaxloader
 * @category    test
 * @copyright   2018 Markku Riekkinen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/filter/mathjaxloader/filter.php');
/**
 * Unit tests for the MathJax loader filter.
 *
 * @copyright 2018 Markku Riekkinen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_mathjaxloader_filtermath_testcase extends advanced_testcase {

    /**
     * Test the functionality of {@link filter_mathjaxloader::filter()}.
     *
     * @param string $inputtext The text given by the user.
     * @param string $expected The expected output after filtering.
     *
     * @dataProvider test_math_filtering_inputs
     */
    public function test_math_filtering($inputtext, $expected) {
        $filter = new filter_mathjaxloader(context_system::instance(), []);
        $this->assertEquals($expected, $filter->filter($inputtext));
    }

    /**
     * Data provider for {@link self::test_math_filtering()}.
     *
     * @return array of [inputtext, expectedoutput] tuples.
     */
    public function test_math_filtering_inputs() {
        return [
            // One inline formula.
            ['Some inline math \\( y = x^2 \\).',
            '<span class="filter_mathjaxloader_equation">Some inline math <span class="nolink">\\( y = x^2 \\)</span>.</span>'],

            // One inline and one display.
            ['Some inline math \\( y = x^2 \\) and display formula \\[ S = \\sum_{n=1}^{\\infty} 2^n \\]',
            '<span class="filter_mathjaxloader_equation">Some inline math <span class="nolink">\\( y = x^2 \\)</span> and '
                . 'display formula <span class="nolink">\\[ S = \\sum_{n=1}^{\\infty} 2^n \\]</span></span>'],

            // One display and one inline.
            ['Display formula \\[ S = \\sum_{n=1}^{\\infty} 2^n \\] and some inline math \\( y = x^2 \\).',
            '<span class="filter_mathjaxloader_equation">Display formula <span class="nolink">\\[ S = \\sum_{n=1}^{\\infty} 2^n \\]</span> and '
                . 'some inline math <span class="nolink">\\( y = x^2 \\)</span>.</span>'],

            // One inline and one display (with dollars).
            ['Some inline math \\( y = x^2 \\) and display formula $$ S = \\sum_{n=1}^{\\infty} 2^n $$',
            '<span class="filter_mathjaxloader_equation">Some inline math <span class="nolink">\\( y = x^2 \\)</span> and '
                . 'display formula <span class="nolink">$$ S = \\sum_{n=1}^{\\infty} 2^n $$</span></span>'],

            // One display (with dollars) and one inline.
            ['Display formula $$ S = \\sum_{n=1}^{\\infty} 2^n $$ and some inline math \\( y = x^2 \\).',
            '<span class="filter_mathjaxloader_equation">Display formula <span class="nolink">$$ S = \\sum_{n=1}^{\\infty} 2^n $$</span> and '
                . 'some inline math <span class="nolink">\\( y = x^2 \\)</span>.</span>'],

            // Inline math environment nested inside display environment (using a custom LaTex macro).
            ['\\[ \\newcommand{\\False}{\\mathsf{F}} \\newcommand{\\NullF}{\\fbox{\\(\\False\\)}} \\] '
                . 'Text with inline formula using the custom LaTex macro \\( a = \\NullF \\).',
            '<span class="filter_mathjaxloader_equation"><span class="nolink">'
                . '\\[ \\newcommand{\\False}{\\mathsf{F}} \\newcommand{\\NullF}{\\fbox{\\(\\False\\)}} \\]</span> '
                . 'Text with inline formula using the custom LaTex macro <span class="nolink">\\( a = \\NullF \\)</span>.</span>'],

            // Nested environments and some more content.
            ['\\[ \\newcommand{\\False}{\\mathsf{F}} \\newcommand{\\NullF}{\\fbox{\\(\\False\\)}} \\] '
                . 'Text with inline formula using the custom LaTex macro \\( a = \\NullF \\). Finally, a display formula '
                . '$$ b = \\NullF $$',
            '<span class="filter_mathjaxloader_equation"><span class="nolink">'
                . '\\[ \\newcommand{\\False}{\\mathsf{F}} \\newcommand{\\NullF}{\\fbox{\\(\\False\\)}} \\]</span> '
                . 'Text with inline formula using the custom LaTex macro <span class="nolink">\\( a = \\NullF \\)</span>. '
                . 'Finally, a display formula <span class="nolink">$$ b = \\NullF $$</span></span>'],

            // Broken math: the delimiters ($$) are not closed.
            ['Writing text and starting display math. $$ k = i^3 \\newcommand{\\False}{\\mathsf{F}} \\newcommand{\\NullF}{\\fbox{\\(\\False\\)}} '
                . 'More text and inline math \\( x = \\NullF \\).',
            'Writing text and starting display math. $$ k = i^3 \\newcommand{\\False}{\\mathsf{F}} \\newcommand{\\NullF}{\\fbox{\\(\\False\\)}} '
                . 'More text and inline math \\( x = \\NullF \\).'],
        ];
    }
}
