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

namespace local_moodlecheck;

use local_moodlecheck_path;
use local_moodlecheck_registry;

/**
 * Contains unit tests for covering "moodle" PHPDoc rules.
 *
 * @package    local_moodlecheck
 * @category   test
 * @copyright  2018 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class moodlecheck_rules_test extends \advanced_testcase {

    public function setUp(): void {
        global $CFG;
        parent::setUp();
        // Add the moodlecheck machinery.
        require_once($CFG->dirroot . '/local/moodlecheck/locallib.php');
        // Load all files from rules directory.
        if ($dh = opendir($CFG->dirroot. '/local/moodlecheck/rules')) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..') {
                    $pathinfo = pathinfo($file);
                    if (isset($pathinfo['extension']) && $pathinfo['extension'] == 'php') {
                        require_once($CFG->dirroot. '/local/moodlecheck/rules/'. $file);
                    }
                }
            }
            closedir($dh);
        }
        // Load all rules.
        local_moodlecheck_registry::enable_all_rules();
    }

    /**
     * Verify the ::class constant is not reported as phpdoc problem.
     *
     * @covers \local_moodlecheck_file::get_classes
     * @covers \local_moodlecheck_file::previous_nonspace_token
     */
    public function test_constantclass() {
        global $PAGE;
        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/constantclass.php ', null);
        $result = $output->display_path($path, 'xml');

        // Convert results to XML Objext.
        $xmlresult = new \DOMDocument();
        $xmlresult->loadXML($result);

        // Let's verify we have received a xml with file top element and 2 children.
        $xpath = new \DOMXpath($xmlresult);
        $found = $xpath->query("//file/error");
        // TODO: Change to DOMNodeList::count() when php71 support is gone.
        $this->assertSame(2, $found->length);

        // Also verify that contents do not include any problem with line 42 / classesdocumented. Use simple string matching here.
        $this->assertStringContainsString('line="20"', $result);
        $this->assertStringContainsString('packagevalid', $result);
        $this->assertStringNotContainsString('line="42"', $result);
        $this->assertStringNotContainsString('classesdocumented', $result);
    }

    /**
     * Assert that the file block is required for old files, and not for 1-artifact ones.
     *
     * @covers ::local_moodlecheck_filephpdocpresent
     */
    public function test_file_block_required() {
        global $PAGE;

        $output = $PAGE->get_renderer('local_moodlecheck');

        // A file with multiple classes, require the file phpdoc block.
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_file_required_yes1.php', null);
        $result = $output->display_path($path, 'xml');
        $this->assertStringContainsString('File-level phpdocs block is not found', $result);

        // A file without any class (library-like), require the file phpdoc block.
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_file_required_yes2.php', null);
        $result = $output->display_path($path, 'xml');
        $this->assertStringContainsString('File-level phpdocs block is not found', $result);

        // A file with one interface and one trait, require the file phpdoc block.
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_file_required_yes3.php', null);
        $result = $output->display_path($path, 'xml');
        $this->assertStringContainsString('File-level phpdocs block is not found', $result);

        // A file with only one class, do not require the file phpdoc block.
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_file_required_no1.php', null);
        $result = $output->display_path($path, 'xml');
        $this->assertStringNotContainsString('File-level phpdocs block is not found', $result);

        // A file with only one interface, do not require the file phpdoc block.
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_file_required_no2.php', null);
        $result = $output->display_path($path, 'xml');
        $this->assertStringNotContainsString('File-level phpdocs block is not found', $result);

        // A file with only one trait, do not require the file phpdoc block.
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_file_required_no3.php', null);
        $result = $output->display_path($path, 'xml');
        $this->assertStringNotContainsString('File-level phpdocs block is not found', $result);
    }

    /**
     * Assert that classes do not need to have any particular phpdocs tags.
     *
     * @covers ::local_moodlecheck_filehascopyright
     * @covers ::local_moodlecheck_filehaslicense
     */
    public function test_classtags() {
        global $PAGE;

        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/classtags.php ', null);

        $result = $output->display_path($path, 'xml');

        $this->assertStringNotContainsString('classeshavecopyright', $result);
        $this->assertStringNotContainsString('classeshavelicense', $result);
    }

    /**
     * Ensure that token_get_all() does not return PHP Warnings.
     *
     * @covers \local_moodlecheck_file::get_tokens
     */
    public function test_get_tokens() {
        global $PAGE;

        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/unfinished.php ', null);

        $this->expectOutputString('');
        $result = $output->display_path($path, 'xml');
    }

    /**
     * Verify various phpdoc tags in general directories.
     *
     * @covers ::local_moodlecheck_functionarguments
     */
    public function test_phpdoc_tags_general() {
        global $PAGE;
        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_tags_general.php ', null);
        $result = $output->display_path($path, 'xml');

        // Convert results to XML Objext.
        $xmlresult = new \DOMDocument();
        $xmlresult->loadXML($result);

        // Let's verify we have received a xml with file top element and 8 children.
        $xpath = new \DOMXpath($xmlresult);
        $found = $xpath->query("//file/error");
        // TODO: Change to DOMNodeList::count() when php71 support is gone.
        $this->assertSame(19, $found->length);

        // Also verify various bits by content.
        $this->assertStringContainsString('packagevalid', $result);
        $this->assertStringContainsString('incomplete_param_annotation has incomplete parameters list', $result);
        $this->assertStringContainsString('missing_param_defintion has incomplete parameters list', $result);
        $this->assertStringContainsString('missing_param_annotation has incomplete parameters list', $result);
        $this->assertStringContainsString('incomplete_param_definition has incomplete parameters list', $result);
        $this->assertStringContainsString('incomplete_param_annotation1 has incomplete parameters list', $result);
        $this->assertStringContainsString('mismatch_param_types has incomplete parameters list', $result);
        $this->assertStringContainsString('mismatch_param_types1 has incomplete parameters list', $result);
        $this->assertStringContainsString('mismatch_param_types2 has incomplete parameters list', $result);
        $this->assertStringContainsString('mismatch_param_types3 has incomplete parameters list', $result);
        $this->assertStringContainsString('incomplete_return_annotation has incomplete parameters list', $result);
        $this->assertStringContainsString('Invalid phpdocs tag @small', $result);
        $this->assertStringContainsString('Invalid phpdocs tag @zzzing', $result);
        $this->assertStringContainsString('Invalid phpdocs tag @inheritdoc', $result);
        $this->assertStringContainsString('Incorrect path for phpdocs tag @covers', $result);
        $this->assertStringContainsString('Incorrect path for phpdocs tag @dataProvider', $result);
        $this->assertStringContainsString('Incorrect path for phpdocs tag @group', $result);
        $this->assertStringContainsString('@codingStandardsIgnoreLine', $result);
        $this->assertStringNotContainsString('@deprecated', $result);
        $this->assertStringNotContainsString('correct_param_types', $result);
        $this->assertStringNotContainsString('correct_return_type', $result);
    }

    /**
     * Verify various phpdoc tags in tests directories.
     *
     * @covers ::local_moodlecheck_phpdocsinvalidtag
     * @covers ::local_moodlecheck_phpdocsnotrecommendedtag
     * @covers ::local_moodlecheck_phpdocsinvalidpathtag
     */
    public function test_phpdoc_tags_tests() {
        global $PAGE;
        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_tags_test.php ', null);
        $result = $output->display_path($path, 'xml');

        // Convert results to XML Objext.
        $xmlresult = new \DOMDocument();
        $xmlresult->loadXML($result);

        // Let's verify we have received a xml with file top element and 5 children.
        $xpath = new \DOMXpath($xmlresult);
        $found = $xpath->query("//file/error");
        // TODO: Change to DOMNodeList::count() when php71 support is gone.
        $this->assertSame(5, $found->length);

        // Also verify various bits by content.
        $this->assertStringContainsString('packagevalid', $result);
        $this->assertStringContainsString('Invalid phpdocs tag @small', $result);
        $this->assertStringContainsString('Invalid phpdocs tag @zzzing', $result);
        $this->assertStringContainsString('Invalid phpdocs tag @inheritdoc', $result);
        $this->assertStringNotContainsString('Incorrect path for phpdocs tag @covers', $result);
        $this->assertStringNotContainsString('Incorrect path for phpdocs tag @dataProvider', $result);
        $this->assertStringNotContainsString('Incorrect path for phpdocs tag @group', $result);
        $this->assertStringNotContainsString('@deprecated', $result);
    }

    /**
     * Verify various phpdoc tags can be used inline.
     *
     * @covers ::local_moodlecheck_phpdocsinvalidinlinetag
     * @covers ::local_moodlecheck_phpdocsuncurlyinlinetag
     * @covers ::local_moodlecheck_phpdoccontentsinlinetag
     */
    public function test_phpdoc_tags_inline() {
        global $PAGE;
        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path('local/moodlecheck/tests/fixtures/phpdoc_tags_inline.php ', null);
        $result = $output->display_path($path, 'xml');

        // Convert results to XML Objext.
        $xmlresult = new \DOMDocument();
        $xmlresult->loadXML($result);

        // Let's verify we have received a xml with file top element and 8 children.
        $xpath = new \DOMXpath($xmlresult);
        $found = $xpath->query("//file/error");
        // TODO: Change to DOMNodeList::count() when php71 support is gone.
        $this->assertSame(8, $found->length);

        // Also verify various bits by content.
        $this->assertStringContainsString('packagevalid', $result);
        $this->assertStringContainsString('Invalid inline phpdocs tag @param found', $result);
        $this->assertStringContainsString('Invalid inline phpdocs tag @throws found', $result);
        $this->assertStringContainsString('Inline phpdocs tag {@link tags need to have a valid URL} with incorrect', $result);
        $this->assertStringContainsString('Inline phpdocs tag {@see $this-&gt;tagrules[&#039;url&#039;]} with incorrect', $result);
        $this->assertStringContainsString('Inline phpdocs tag not enclosed with curly brackets @see found', $result);
        $this->assertStringContainsString(
            'It must match {@link [valid URL] [description (optional)]} or {@see [valid FQSEN] [description (optional)]}',
            $result
        );
        $this->assertStringNotContainsString('{@link https://moodle.org}', $result);
        $this->assertStringNotContainsString('{@see has_capability}', $result);
        $this->assertStringNotContainsString('ba8by}', $result);
    }

    /**
     * Test that {@see local_moodlecheck_get_categories()} returns the correct list of allowed categories.
     *
     * @covers ::local_moodlecheck_get_categories
     */
    public function test_local_moodlecheck_get_categories() {

        set_user_preference('local_moodlecheck_categoriestime', 0);
        set_user_preference('local_moodlecheck_categoriesvalue', '');

        $allowed = local_moodlecheck_get_categories();

        $expected = ['access', 'dml', 'files', 'form', 'log', 'navigation', 'page', 'output', 'string', 'upgrade',
            'core', 'admin', 'analytics', 'availability', 'backup', 'cache', 'calendar', 'check', 'comment',
            'competency', 'ddl', 'enrol', 'event', 'xapi', 'external', 'lock', 'message', 'media', 'oauth2',
            'preference', 'portfolio', 'privacy', 'rating', 'rss', 'search', 'tag', 'task', 'time', 'test',
            'webservice', 'badges', 'completion', 'grading', 'group', 'grade', 'plagiarism', 'question',
        ];

        foreach ($expected as $category) {
            $this->assertContains($category, $allowed);
        }

        // Also check that the locally cached copy is still up to date.
        $allowed = local_moodlecheck_get_categories(true);

        foreach ($expected as $category) {
            $this->assertContains($category, $allowed);
        }
    }

    /**
     * Verify that anonymous classes do not require phpdoc class blocks.
     *
     * @dataProvider anonymous_class_provider
     * @param   string $path
     * @param   bool $expectclassesdocumentedfail Whether the
     *
     * @covers \local_moodlecheck_file::get_artifacts
     */
    public function test_phpdoc_anonymous_class_docblock(string $path, bool $expectclassesdocumentedfail) {
        global $PAGE;

        $output = $PAGE->get_renderer('local_moodlecheck');
        $checkpath = new local_moodlecheck_path($path, null);
        $result = $output->display_path($checkpath, 'xml');

        if ($expectclassesdocumentedfail) {
            $this->assertStringContainsString('classesdocumented', $result);
        } else {
            $this->assertStringNotContainsString('classesdocumented', $result);
        }
    }

    /**
     * Data provider for anonymous classes tests.
     *
     * @return  array
     */
    public function anonymous_class_provider(): array {
        $rootpath  = 'local/moodlecheck/tests/fixtures/anonymous';
        return [
            'return new class {' => [
                "{$rootpath}/anonymous.php",
                false,
            ],
            'return new class extends parentclass {' => [
                "{$rootpath}/extends.php",
                false,
            ],
            'return new class implements someinterface {' => [
                "{$rootpath}/implements.php",
                false,
            ],
            'return new class extends parentclass implements someinterface {' => [
                "{$rootpath}/extendsandimplements.php",
                false,
            ],
            '$value = new class {' => [
                "{$rootpath}/assigned.php",
                false,
            ],
            'class someclass extends parentclass {' => [
                "{$rootpath}/named.php",
                true,
            ],
        ];
    }

    /**
     * Verify that empty files and files without PHP aren't processed
     *
     * @dataProvider empty_nophp_files_provider
     * @param   string $path
     *
     * @covers \local_moodlecheck_file::validate
     */
    public function test_empty_nophp_files($file) {
        global $PAGE;
        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path($file, null);
        $result = $output->display_path($path, 'xml');

        // Convert results to XML Object.
        $xmlresult = new \DOMDocument();
        $xmlresult->loadXML($result);

        // Let's verify we have received a xml with file top element and 1 children.
        $xpath = new \DOMXpath($xmlresult);
        $found = $xpath->query("//file/error");
        // TODO: Change to DOMNodeList::count() when php71 support is gone.
        $this->assertSame(1, $found->length);

        // Also verify various bits by content.
        $this->assertStringContainsString('The file is empty or doesn&#039;t contain PHP code. Skipped.', $result);
    }

    /**
     * Data provider for test_empty_nophp_files()
     *
     * @return array
     */
    public function empty_nophp_files_provider(): array {
        return [
            'empty' => ['local/moodlecheck/tests/fixtures/empty.php'],
            'nophp' => ['local/moodlecheck/tests/fixtures/nophp.php'],
        ];
    }

    /**
     * Verify that top-level methods without docs are errors but methods in subclasses without docs are warnings.
     *
     * @covers ::local_moodlecheck_functionsdocumented
     */
    public function test_functionsdocumented_method_overrides() {
        $file = __DIR__ . "/fixtures/phpdoc_method_overrides.php";

        global $PAGE;
        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path($file, null);
        $result = $output->display_path($path, 'xml');

        // Convert results to XML Object.
        $xmlresult = new \DOMDocument();
        $xmlresult->loadXML($result);

        $xpath = new \DOMXpath($xmlresult);
        $found = $xpath->query('//file/error[@source="functionsdocumented"]');
        // TODO: Change to DOMNodeList::count() when php71 support is gone.
        $this->assertSame(4, $found->length);

        $severities = array_map(function (\DOMElement $element) {
            return $element->getAttribute("severity");
        }, iterator_to_array($found));

        $this->assertEquals(["error", "warning", "warning", "warning"], $severities);
    }

    /**
     * Verify that `variablesdocumented` correctly detects PHPdoc on different kinds of properties.
     *
     * @covers ::local_moodlecheck_variablesdocumented
     * @covers \local_moodlecheck_file::get_variables
     */
    public function test_variablesdocumented() {
        $file = __DIR__ . "/fixtures/phpdoc_properties.php";

        global $PAGE;
        $output = $PAGE->get_renderer('local_moodlecheck');
        $path = new local_moodlecheck_path($file, null);
        $result = $output->display_path($path, 'xml');

        // Convert results to XML Object.
        $xmlresult = new \DOMDocument();
        $xmlresult->loadXML($result);

        $xpath = new \DOMXpath($xmlresult);

        $found = $xpath->query('//file/error[@source="variablesdocumented"]');
        // TODO: Change to DOMNodeList::count() when php71 support is gone.
        $this->assertSame(4, $found->length);

        // The PHPdocs of the other properties should be detected correctly.
        $this->assertStringContainsString('$undocumented1', $found->item(0)->getAttribute("message"));
        $this->assertStringContainsString('$undocumented2', $found->item(1)->getAttribute("message"));
        $this->assertStringContainsString('$undocumented3', $found->item(2)->getAttribute("message"));
        $this->assertStringContainsString('$undocumented4', $found->item(3)->getAttribute("message"));
    }
}
