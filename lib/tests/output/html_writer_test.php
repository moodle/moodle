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

namespace core\output;

use core_table\output\html_table;
use core_table\output\html_table_cell;
use core_table\output\html_table_row;

// phpcs:disable moodle.Commenting.DocblockDescription.Missing

/**
 * Unit tests for the html_writer class.
 *
 * @package core
 * @copyright 2010 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core\output\html_writer
 * @coversDefaultClass \\core\output\html_writer
 */
final class html_writer_test extends \basic_testcase {
    /**
     * @covers ::start_tag
     */
    public function test_start_tag(): void {
        $this->assertSame('<div>', html_writer::start_tag('div'));
    }

    /**
     * @covers ::start_tag
     */
    public function test_start_tag_with_attr(): void {
        $this->assertSame(
            '<div class="frog">',
            html_writer::start_tag('div', ['class' => 'frog'])
        );
    }

    /**
     * @covers ::start_tag
     */
    public function test_start_tag_with_attrs(): void {
        $this->assertSame(
            '<div class="frog" id="mydiv">',
            html_writer::start_tag('div', ['class' => 'frog', 'id' => 'mydiv']),
        );
    }

    /**
     * @covers ::end_tag
     */
    public function test_end_tag(): void {
        $this->assertSame('</div>', html_writer::end_tag('div'));
    }

    /**
     * @covers ::empty_Tag
     */
    public function test_empty_tag(): void {
        $this->assertSame('<br />', html_writer::empty_tag('br'));
    }

    /**
     * @covers ::empty_Tag
     */
    public function test_empty_tag_with_attrs(): void {
        $this->assertSame(
            '<input type="submit" value="frog" />',
            html_writer::empty_tag('input', ['type' => 'submit', 'value' => 'frog']),
        );
    }

    /**
     * @covers ::nonempty_tag
     */
    public function test_nonempty_tag_with_content(): void {
        $this->assertSame(
            '<div>Hello world!</div>',
            html_writer::nonempty_tag('div', 'Hello world!'),
        );
    }

    /**
     * @covers ::nonempty_tag
     */
    public function test_nonempty_tag_empty(): void {
        $this->assertSame(
            '',
            html_writer::nonempty_tag('div', ''),
        );
    }

    /**
     * @covers ::nonempty_tag
     */
    public function test_nonempty_tag_null(): void {
        $this->assertSame(
            '',
            html_writer::nonempty_tag('div', null),
        );
    }

    /**
     * @covers ::nonempty_tag
     */
    public function test_nonempty_tag_zero(): void {
        $this->assertSame(
            '<div class="score">0</div>',
            html_writer::nonempty_tag('div', 0, ['class' => 'score'])
        );
    }

    /**
     * @covers ::nonempty_tag
     */
    public function test_nonempty_tag_zero_string(): void {
        $this->assertSame(
            '<div class="score">0</div>',
            html_writer::nonempty_tag('div', '0', ['class' => 'score'])
        );
    }

    /**
     * @covers ::div
     */
    public function test_div(): void {
        // All options.
        $this->assertSame(
            '<div class="frog" id="kermit">ribbit</div>',
            html_writer::div('ribbit', 'frog', ['id' => 'kermit'])
        );
        // Combine class from attributes and $class.
        $this->assertSame(
            '<div class="amphibian frog">ribbit</div>',
            html_writer::div('ribbit', 'frog', ['class' => 'amphibian'])
        );
        // Class only.
        $this->assertSame(
            '<div class="frog">ribbit</div>',
            html_writer::div('ribbit', 'frog')
        );
        // Attributes only.
        $this->assertSame(
            '<div id="kermit">ribbit</div>',
            html_writer::div('ribbit', '', ['id' => 'kermit'])
        );
        // No options.
        $this->assertSame(
            '<div>ribbit</div>',
            html_writer::div('ribbit')
        );
    }

    /**
     * @covers ::start_div
     */
    public function test_start_div(): void {
        // All options.
        $this->assertSame(
            '<div class="frog" id="kermit">',
            html_writer::start_div('frog', ['id' => 'kermit'])
        );
        // Combine class from attributes and $class.
        $this->assertSame(
            '<div class="amphibian frog">',
            html_writer::start_div('frog', ['class' => 'amphibian'])
        );
        // Class only.
        $this->assertSame(
            '<div class="frog">',
            html_writer::start_div('frog')
        );
        // Attributes only.
        $this->assertSame(
            '<div id="kermit">',
            html_writer::start_div('', ['id' => 'kermit'])
        );
        // No options.
        $this->assertSame(
            '<div>',
            html_writer::start_div()
        );
    }

    /**
     * @covers ::end_div
     */
    public function test_end_div(): void {
        $this->assertSame('</div>', html_writer::end_div());
    }

    /**
     * @covers ::span
     */
    public function test_span(): void {
        // All options.
        $this->assertSame(
            '<span class="frog" id="kermit">ribbit</span>',
            html_writer::span('ribbit', 'frog', ['id' => 'kermit'])
        );
        // Combine class from attributes and $class.
        $this->assertSame(
            '<span class="amphibian frog">ribbit</span>',
            html_writer::span('ribbit', 'frog', ['class' => 'amphibian'])
        );
        // Class only.
        $this->assertSame(
            '<span class="frog">ribbit</span>',
            html_writer::span('ribbit', 'frog')
        );
        // Attributes only.
        $this->assertSame(
            '<span id="kermit">ribbit</span>',
            html_writer::span('ribbit', '', ['id' => 'kermit'])
        );
        // No options.
        $this->assertSame(
            '<span>ribbit</span>',
            html_writer::span('ribbit')
        );
    }

    /**
     * @covers ::start_span
     */
    public function test_start_span(): void {
        // All options.
        $this->assertSame(
            '<span class="frog" id="kermit">',
            html_writer::start_span('frog', ['id' => 'kermit'])
        );
        // Combine class from attributes and $class.
        $this->assertSame(
            '<span class="amphibian frog">',
            html_writer::start_span('frog', ['class' => 'amphibian'])
        );
        // Class only.
        $this->assertSame(
            '<span class="frog">',
            html_writer::start_span('frog')
        );
        // Attributes only.
        $this->assertSame(
            '<span id="kermit">',
            html_writer::start_span('', ['id' => 'kermit'])
        );
        // No options.
        $this->assertSame(
            '<span>',
            html_writer::start_span()
        );
    }

    /**
     * @covers ::end_span
     */
    public function test_end_span(): void {
        $this->assertSame('</span>', html_writer::end_span());
    }

    /**
     * @covers ::table
     * @covers \core_table\output\html_table_row
     * @covers \core_table\output\html_table_cell
     * @covers \core_table\output\html_table
     */
    public function test_table(): void {
        $row = new html_table_row();

        // The attribute will get overwritten by the ID.
        $row->id = 'Bob';
        $row->attributes['id'] = 'will get overwritten';

        // The data-name will be present in the output.
        $row->attributes['data-name'] = 'Fred';

        $cell = new html_table_cell();

        // The attribute will get overwritten by the ID.
        $cell->id = 'Jeremy';
        $cell->attributes['id'] = 'will get overwritten';

        // The data-name will be present in the output.
        $cell->attributes['data-name'] = 'John';

        $row->cells[] = $cell;

        $table = new html_table();
        $table->responsive = false;
        // The attribute will get overwritten by the ID.
        $table->id = 'Jeffrey';
        $table->attributes['id'] = 'will get overwritten';

        // The data-name will be present in the output.
        $table->attributes['data-name'] = 'Colin';
        // The attribute will get overwritten by the ID above.
        $table->data[] = $row;

        // Specify a caption to be output.
        $table->caption = "A table of meaningless data.";

        $output = html_writer::table($table);

        $expected = <<<EOF
<table class="generaltable" id="Jeffrey" data-name="Colin">
<caption>A table of meaningless data.</caption><tbody><tr class="lastrow" id="Bob" data-name="Fred">
<td class="cell c0 lastcol" id="Jeremy" data-name="John" style=""></td>
</tr>
</tbody>
</table>

EOF;
        $this->assertSame($expected, $output);
    }

    /**
     * @covers ::table
     */
    public function test_table_hidden_caption(): void {

        $table = new html_table();
        $table->id = "whodat";
        $table->data = [
            ['fred', 'MDK'],
            ['bob', 'Burgers'],
            ['dave', 'Competitiveness'],
        ];
        $table->caption = "Who even knows?";
        $table->captionhide = true;
        $table->responsive = false;

        $output = html_writer::table($table);
        $expected = <<<EOF
<table class="generaltable" id="whodat">
<caption class="accesshide">Who even knows?</caption><tbody><tr class="">
<td class="cell c0" style="">fred</td>
<td class="cell c1 lastcol" style="">MDK</td>
</tr>
<tr class="">
<td class="cell c0" style="">bob</td>
<td class="cell c1 lastcol" style="">Burgers</td>
</tr>
<tr class="lastrow">
<td class="cell c0" style="">dave</td>
<td class="cell c1 lastcol" style="">Competitiveness</td>
</tr>
</tbody>
</table>

EOF;
        $this->assertSame($expected, $output);
    }
}
