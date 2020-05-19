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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the wiki parser
 *
 * @package   mod_wiki
 * @category  phpunit
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Jordi Piguillem
 * @author Marc Alier
 * @author David Jimenez
 * @author Josep Arus
 * @author Kenneth Riba
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/mod/wiki/parser/parser.php');


class mod_wiki_wikiparser_test extends basic_testcase {

    /**
     * URL inside the clickable text of some link should not be turned into a new link via the url_tag_rule.
     *
     * @dataProvider urls_inside_link_text_provider
     * @param string $markup Markup of the Wiki page the text is part of.
     * @param string $input The input text.
     * @param string $output The expected output HTML as a result of the parsed input text.
     */
    public function test_urls_inside_link_text(string $markup, string $input, string $output) {

        $parsingresult = wiki_parser_proxy::parse($input, $markup, [
            'link_callback' => '/mod/wiki/locallib.php:wiki_parser_link',
            'link_callback_args' => ['swid' => 1],
        ]);

        $this->assertContains($output, $parsingresult['parsed_text']);
    }

    /**
     * Provides data sets for {@see self::test_urls_inside_link_text()}.
     *
     * @return array
     */
    public function urls_inside_link_text_provider() {
        return [
            'creole implicit link' => [
                'markup' => 'creole',
                'input' => 'Visit https://site.url for more information.',
                'output' => 'Visit <a href="https://site.url">https://site.url</a> for more information.',
            ],
            'creole explicit link' => [
                'markup' => 'creole',
                'input' => 'Visit [[https://site.url]] for more information.',
                'output' => 'Visit <a href="https://site.url">https://site.url</a> for more information.',
            ],
            'creole explicit link with text' => [
                'markup' => 'creole',
                'input' => 'Visit [[https://site.url|http://www.site.url]] for more information.',
                'output' => 'Visit <a href="https://site.url">http://www.site.url</a> for more information.',
            ],
            'nwiki implicit link' => [
                'markup' => 'nwiki',
                'input' => 'Visit https://site.url for more information.',
                'output' => 'Visit <a href="https://site.url">https://site.url</a> for more information.',
            ],
            'nwiki explicit link' => [
                'markup' => 'nwiki',
                'input' => 'Visit [https://site.url] for more information.',
                'output' => 'Visit <a href="https://site.url">https://site.url</a> for more information.',
            ],
            'nwiki explicit link with space separated text' => [
                'markup' => 'nwiki',
                'input' => 'Visit [https://site.url http://www.site.url] for more information.',
                'output' => 'Visit <a href="https://site.url">http://www.site.url</a> for more information.',
            ],
            'nwiki explicit link with pipe separated text' => [
                'markup' => 'nwiki',
                'input' => 'Visit [https://site.url|http://www.site.url] for more information.',
                'output' => 'Visit <a href="https://site.url">http://www.site.url</a> for more information.',
            ],
            'html implicit link' => [
                'markup' => 'html',
                'input' => 'Visit https://site.url for more information.',
                'output' => 'Visit <a href="https://site.url">https://site.url</a> for more information.',
            ],
            'html explicit link with text' => [
                'markup' => 'html',
                'input' => 'Visit <a href="https://site.url">http://www.site.url</a> for more information.',
                'output' => 'Visit <a href="https://site.url">http://www.site.url</a> for more information.',
            ],
            'html wiki link to non-existing page' => [
                'markup' => 'html',
                'input' => 'Visit [[Another page]] for more information.',
                'output' => 'Visit <a class="wiki_newentry" ' .
                    'href="https://www.example.com/moodle/mod/wiki/create.php?swid=1&amp;title=Another+page&amp;action=new">' .
                    'Another page</a> for more information.',
            ],
            'html wiki link inside an explicit link' => [
                // The explicit href URL takes precedence here, the [[...]] is not turned into a wiki link.
                'markup' => 'html',
                'input' => 'Visit <a href="https://site.url">[[Another page]]</a> for more information.',
                'output' => 'Visit <a href="https://site.url">[[Another page]]</a> for more information.',
            ],
        ];
    }

    function testCreoleMarkup() {
        $this->assertTestFiles('creole');
    }

    function testNwikiMarkup() {
        $this->assertTestFiles('nwiki');
    }

    function testHtmlMarkup() {
        $this->assertTestFiles('html');
    }

    private function assertTestFile($num, $markup) {
        if(!file_exists(__DIR__."/fixtures/input/$markup/$num") || !file_exists(__DIR__."/fixtures/output/$markup/$num")) {
            return false;
        }
        $input = file_get_contents(__DIR__."/fixtures/input/$markup/$num");
        $output = file_get_contents(__DIR__."/fixtures/output/$markup/$num");

        $result = wiki_parser_proxy::parse($input, $markup, array('pretty_print' => true));

        //removes line breaks to avoid line break encoding causing tests to fail.
        $result['parsed_text'] = preg_replace('~[\r\n]~', '', $result['parsed_text']);
        $output                = preg_replace('~[\r\n]~', '', $output);

        $this->assertEquals($output, $result['parsed_text'], 'Failed asserting that two strings are equal. Markup = '.$markup.", num = $num");
        return true;
    }

    private function assertTestFiles($markup) {
        $i = 1;
        while($this->assertTestFile($i, $markup)) {
            $i++;
        }
    }

    /**
     * Check that headings with special characters work as expected with HTML.
     *
     * - The heading itself is well displayed,
     * - The TOC heading is well display,
     * - The edit link points to the right page,
     * - The links properly works with get_section.
     */
    public function test_special_headings() {

        // First testing HTML markup.

        // Test section name using HTML entities.
        $input = '<h1>Code &amp; Test</h1>';
        $output = '<h3><a name="toc-1"></a>Code &amp; Test <a href="edit.php?pageid=&amp;section=Code+%26amp%3B+Test" '.
            'class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Code &amp; Test <a href="edit.php?pageid=&amp;section=Code+%26amp%3B+'.
            'Test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'html', 'Code &amp; Test');
        $actual = wiki_parser_proxy::parse($input, 'html');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test section name using non-ASCII characters.
        $input = '<h1>Another áéíóúç€ test</h1>';
        $output = '<h3><a name="toc-1"></a>Another áéíóúç€ test <a href="edit.php?pageid=&amp;section=Another+%C'.
            '3%A1%C3%A9%C3%AD%C3%B3%C3%BA%C3%A7%E2%82%AC+test" class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Another áéíóúç€ test <a href="edit.php?pageid=&amp;section=Another+%C'.
            '3%A1%C3%A9%C3%AD%C3%B3%C3%BA%C3%A7%E2%82%AC+test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'html', 'Another áéíóúç€ test');
        $actual = wiki_parser_proxy::parse($input, 'html');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test section name with a URL.
        $input = '<h1>Another http://moodle.org test</h1>';
        $output = '<h3><a name="toc-1"></a>Another <a href="http://moodle.org">http://moodle.org</a> test <a href="edit.php'.
            '?pageid=&amp;section=Another+http%3A%2F%2Fmoodle.org+test" class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Another http://moodle.org test <a href="edit.php?pageid=&amp;section='.
            'Another+http%3A%2F%2Fmoodle.org+test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'html', 'Another http://moodle.org test');
        $actual = wiki_parser_proxy::parse($input, 'html', array(
            'link_callback' => '/mod/wiki/locallib.php:wiki_parser_link'
        ));
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test toc section names being wikilinks.
        $input = '<h1>[[Heading 1]]</h1><h2>[[Heading A]]</h2><h2>Heading D</h2>';
        $regexpoutput = '!<h3><a name="toc-1"></a>' .
            '<a class="wiki_newentry" href.*mod/wiki/create\.php\?.*title=Heading\+1.*action=new.*>Heading 1<.*' .
            '<h4><a name="toc-2"></a>' .
            '<a class="wiki_newentry" href.*mod/wiki/create\.php\?.*title=Heading\+A.*action=new.*>Heading A<.*' .
            '<h4><a name="toc-3"></a>' .
            'Heading D!ms';
        $regexptoc = '!<a href="#toc-1">Heading 1.*<a href="#toc-2">Heading A</a>.*<a href="#toc-3">Heading D</a>!ms';
        $section = wiki_parser_proxy::get_section($input, 'html', 'Another [[wikilinked]] test');
        $actual = wiki_parser_proxy::parse($input, 'html', array(
            'link_callback' => '/mod/wiki/locallib.php:wiki_parser_link',
            'link_callback_args' => array('swid' => 1)
        ));
        $this->assertRegExp($regexpoutput, $actual['parsed_text']);
        $this->assertRegExp($regexptoc, $actual['toc']);

        // Now going to test Creole markup.
        // Note that Creole uses links to the escaped version of the section.

        // Test section name using HTML entities.
        $input = '= Code & Test =';
        $output = '<h3><a name="toc-1"></a>Code &amp; Test <a href="edit.php?pageid=&amp;section=Code+%26amp%3B+Test" '.
            'class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Code &amp; Test <a href="edit.php?pageid=&amp;section=Code+%26amp%3B+'.
            'Test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'creole', 'Code &amp; Test');
        $actual = wiki_parser_proxy::parse($input, 'creole');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test section name using non-ASCII characters.
        $input = '= Another áéíóúç€ test =';
        $output = '<h3><a name="toc-1"></a>Another áéíóúç€ test <a href="edit.php?pageid=&amp;section=Another+%C'.
            '3%A1%C3%A9%C3%AD%C3%B3%C3%BA%C3%A7%E2%82%AC+test" class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Another áéíóúç€ test <a href="edit.php?pageid=&amp;section=Another+%C'.
            '3%A1%C3%A9%C3%AD%C3%B3%C3%BA%C3%A7%E2%82%AC+test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'creole', 'Another áéíóúç€ test');
        $actual = wiki_parser_proxy::parse($input, 'creole');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test section name with a URL, creole does not support linking links in a heading.
        $input = '= Another http://moodle.org test =';
        $output = '<h3><a name="toc-1"></a>Another http://moodle.org test <a href="edit.php'.
            '?pageid=&amp;section=Another+http%3A%2F%2Fmoodle.org+test" class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Another http://moodle.org test <a href="edit.php?pageid=&amp;section='.
            'Another+http%3A%2F%2Fmoodle.org+test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'creole', 'Another http://moodle.org test');
        $actual = wiki_parser_proxy::parse($input, 'creole');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Now going to test NWiki markup.
        // Note that Creole uses links to the escaped version of the section.

        // Test section name using HTML entities.
        $input = '= Code & Test =';
        $output = '<h3><a name="toc-1"></a>Code & Test <a href="edit.php?pageid=&amp;section=Code+%26+Test" '.
            'class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Code & Test <a href="edit.php?pageid=&amp;section=Code+%26+'.
            'Test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'nwiki', 'Code & Test');
        $actual = wiki_parser_proxy::parse($input, 'nwiki');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test section name using non-ASCII characters.
        $input = '= Another áéíóúç€ test =';
        $output = '<h3><a name="toc-1"></a>Another áéíóúç€ test <a href="edit.php?pageid=&amp;section=Another+%C'.
            '3%A1%C3%A9%C3%AD%C3%B3%C3%BA%C3%A7%E2%82%AC+test" class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Another áéíóúç€ test <a href="edit.php?pageid=&amp;section=Another+%C'.
            '3%A1%C3%A9%C3%AD%C3%B3%C3%BA%C3%A7%E2%82%AC+test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'nwiki', 'Another áéíóúç€ test');
        $actual = wiki_parser_proxy::parse($input, 'nwiki');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test section name with a URL, nwiki does not support linking links in a heading.
        $input = '= Another http://moodle.org test =';
        $output = '<h3><a name="toc-1"></a>Another http://moodle.org test <a href="edit.php'.
            '?pageid=&amp;section=Another+http%3A%2F%2Fmoodle.org+test" class="wiki_edit_section">[edit]</a></h3>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Another http://moodle.org test <a href="edit.php?pageid=&amp;section='.
            'Another+http%3A%2F%2Fmoodle.org+test" class="wiki_edit_section">[edit]</a></a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'nwiki', 'Another http://moodle.org test');
        $actual = wiki_parser_proxy::parse($input, 'nwiki');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);

        // Test section names when headings start with level 3.
        $input = '<h3>Heading test</h3><h4>Subsection</h4>';
        $output = '<h3><a name="toc-1"></a>Heading test <a href="edit.php?pageid=&amp;section=Heading+test" '.
            'class="wiki_edit_section">[edit]</a></h3>'. "\n" . '<h4><a name="toc-2"></a>Subsection</h4>' . "\n";
        $toc = '<div class="wiki-toc"><p class="wiki-toc-title">Table of contents</p><p class="wiki-toc-section-1 '.
            'wiki-toc-section">1. <a href="#toc-1">Heading test <a href="edit.php?pageid=&amp;section=Heading+'.
            'test" class="wiki_edit_section">[edit]</a></a></p><p class="wiki-toc-section-2 wiki-toc-section">'.
            '1.1. <a href="#toc-2">Subsection</a></p></div>';
        $section = wiki_parser_proxy::get_section($input, 'html', 'Heading test');
        $actual = wiki_parser_proxy::parse($input, 'html');
        $this->assertEquals($output, $actual['parsed_text']);
        $this->assertEquals($toc, $actual['toc']);
        $this->assertNotEquals(false, $section);
    }

}
