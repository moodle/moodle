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

namespace qformat_blackboard_six;

use qformat_blackboard_six_pool;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format.php');
require_once($CFG->dirroot . '/question/format/blackboard_six/format.php');

/**
 * Unit tests for qformat_blackboard_six formatbase.
 *
 * @package    qformat_blackboard_six
 * @copyright  2026 Adam Olley <adam.olley@openlms.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \qformat_blackboard_six_base
 */
final class formatbase_test extends \advanced_testcase {
    /** @var string Directory acting as the extracted import root. */
    private string $filebase;

    /** @var string A file outside the import root that must never be imported. */
    private string $secretfile;

    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();

        $root = make_request_directory();
        $this->filebase = $root . '/import';
        mkdir($this->filebase . '/res00001', 0777, true);
        file_put_contents($this->filebase . '/res00001/image.png', 'imagedata');
        $this->secretfile = $root . '/secret.php';
        file_put_contents($this->secretfile, 'secretdata');

        // A sibling directory whose name starts with the file base's name. Paths leading
        // here must still be rejected, which only holds if the containment check compares
        // against the file base plus a trailing directory separator.
        mkdir($root . '/importdata');
        file_put_contents($root . '/importdata/secret.png', 'secretdata');
    }

    /**
     * Get the filenames stored in the draft area for a text_field() result.
     *
     * @param array $data result of text_field()
     * @return string[]
     */
    private function get_draft_filenames(array $data): array {
        global $USER;
        if (empty($data['itemid'])) {
            return [];
        }
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            \context_user::instance($USER->id)->id,
            'user',
            'draft',
            $data['itemid'],
            'filename',
            false
        );
        return array_values(array_map(fn($f) => $f->get_filename(), $files));
    }

    /**
     * An image referenced from inside the file base is imported into the draft area.
     */
    public function test_text_field_imports_image_inside_filebase(): void {
        $importer = new qformat_blackboard_six_pool();
        $importer->set_filebase($this->filebase);

        $data = $importer->text_field('<p><img src="res00001/image.png" alt="x"></p>');

        $this->assertEquals(['res00001__image.png'], $this->get_draft_filenames($data));
        $this->assertStringContainsString('@@PLUGINFILE@@/res00001__image.png', $data['text']);
    }

    /**
     * Data provider for test_text_field_rejects_traversal.
     *
     * @return array
     */
    public static function traversal_provider(): array {
        return [
            'relative traversal' => ['../secret.php', false],
            'nested traversal' => ['res00001/../../secret.php', false],
            'entity encoded traversal' => ['&#46;&#46;/secret.php', true],
            'absolute path' => ['{SECRET}', false],
            'prefix sibling directory' => ['../importdata/secret.png', false],
            'empty src' => ['', false],
        ];
    }

    /**
     * Image references resolving outside the import root must not be copied.
     *
     * @dataProvider traversal_provider
     * @param string $src img src value
     * @param bool $cleaned whether to go through cleaned_text_field()
     */
    public function test_text_field_rejects_traversal(string $src, bool $cleaned): void {
        $src = str_replace('{SECRET}', $this->secretfile, $src);
        $importer = new qformat_blackboard_six_pool();
        $importer->set_filebase($this->filebase);

        $text = '<p><img src="' . $src . '" alt="x"></p>';
        $data = $cleaned ? $importer->cleaned_text_field($text) : $importer->text_field($text);

        $this->assertSame([], $this->get_draft_filenames($data));
        $this->assertStringNotContainsString('@@PLUGINFILE@@', $data['text']);
    }

    /**
     * A symlink inside the file base that points outside it must not be followed.
     */
    public function test_text_field_rejects_symlink_escape(): void {
        if (!@symlink($this->secretfile, $this->filebase . '/link.png')) {
            $this->markTestSkipped('Symlinks are not supported on this filesystem.');
        }

        $importer = new qformat_blackboard_six_pool();
        $importer->set_filebase($this->filebase);

        $data = $importer->text_field('<p><img src="link.png" alt="x"></p>');

        $this->assertSame([], $this->get_draft_filenames($data));
        $this->assertStringNotContainsString('@@PLUGINFILE@@', $data['text']);
    }

    /**
     * Dot segments that still resolve inside the file base must be imported.
     */
    public function test_text_field_imports_dot_segments_inside_filebase(): void {
        $importer = new qformat_blackboard_six_pool();
        $importer->set_filebase($this->filebase);

        $data = $importer->text_field('<p><img src="res00001/./image.png" alt="x"></p>');

        $this->assertCount(1, $this->get_draft_filenames($data));
        $this->assertStringContainsString('@@PLUGINFILE@@/', $data['text']);
    }

    /**
     * A rejected reference must not stop later valid images in the same text being imported.
     */
    public function test_text_field_imports_valid_images_alongside_rejected_ones(): void {
        $importer = new qformat_blackboard_six_pool();
        $importer->set_filebase($this->filebase);

        $data = $importer->text_field(
            '<p><img src="../secret.php" alt="a"><img src="res00001/image.png" alt="b"></p>'
        );

        $filenames = $this->get_draft_filenames($data);

        // The traversing reference is rejected, so its src stays in the text verbatim
        // rather than being rewritten to a placeholder.
        $this->assertStringContainsString('../secret.php', $data['text']);

        // The valid reference that follows it is still imported and rewritten. The file
        // list is exact, so it is also what proves nothing was stored for the rejected one.
        $this->assertEquals(['res00001__image.png'], $filenames);
        $this->assertStringContainsString('@@PLUGINFILE@@/res00001__image.png', $data['text']);
        $this->assertSame(1, substr_count($data['text'], '@@PLUGINFILE@@'));
    }

    /**
     * Images are ignored entirely when no file base has been set.
     */
    public function test_text_field_ignores_images_without_filebase(): void {
        $importer = new qformat_blackboard_six_pool();

        $data = $importer->text_field('<p><img src="' . $this->secretfile . '" alt="x"></p>');

        $this->assertSame([], $this->get_draft_filenames($data));
        $this->assertStringNotContainsString('@@PLUGINFILE@@', $data['text']);
    }
}
