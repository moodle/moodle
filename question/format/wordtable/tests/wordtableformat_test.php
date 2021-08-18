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
 * Unit tests for the Moodle WordTable format.
 *
 * @package    qformat_wordtable
 * @copyright  2016 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');
require_once($CFG->dirroot . '/question/format/wordtable/format.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/format/xml/tests/xmlformat_test.php');
require_once($CFG->dirroot . '/tag/lib.php');

/**
 * Unit tests for exporting questions into Word (via XML).
 *
 * Each test has a question in XML format, which is converted to HTML using
 * the qformat_wordtable::presave_process method, and then compared to the expected output.
 * @copyright  2016 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @group qformat_wordtable
 */
class qformat_wordtable_export_test extends question_testcase {

    /**
     * Test if the exported HTML output is the same as the expected HTML (ignoring newlines).
     *
     * @param string $expectedhtml as defined.
     * @param string $html as returned by presave_process.
     * @return mixed Boolean true/false, or some error indicator.
     */
    public function assert_same_html($expectedhtml, $html) {
        // Only test the question content, assuming a single question.
        $html = substr($html, strpos($html, '<h2 '));
        // Remove any non-breaking spaces, which are often used in empty cells.
        $html = str_replace("\xA0", "", $html);
        $expectedhtml = str_replace("\xA0", "", $expectedhtml);
        $html = str_replace("\r\n", "\n", $html);
        $expectedhtml = str_replace("\r\n", "\n", $expectedhtml);
        $this->assertEquals($expectedhtml, $html);
    }

    /**
     * Test if the exported HTML for a Description question matches the expected output.
     */
    public function test_export_description() {
        $descriptionxml = '<question type="description">
    <name>
      <text>A description</text>
    </name>
    <questiontext format="html">
      <text>The question text.</text>
    </questiontext>
    <generalfeedback format="html">
      <text>Here is some general feedback.</text>
    </generalfeedback>
    <defaultgrade>0</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
  </question>';

        $expectedhtml = '<h2 class="MsoHeading2">A description</h2><p class="MsoBodyText"/>' .
            '<div class="TableDiv"><table border="1" dir="ltr"><thead>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="Cell">The question text.</p></td>
<td style="width: 1.0cm"><p class="QFType">DE</p></td></tr>
<tr>
<td style="width: 1.0cm"><p class="Cell"></p></td>
<td style="width: 5.0cm"><p class="TableHead"></p></td>
<td style="width: 6.0cm"><p class="TableHead"></p></td>
<td style="width: 1.0cm"><p class="TableHead"></p></td></tr>
</thead><tbody>

<tr>
<td style="width: 1.0cm"><p class="Cell"></p></td>
<th style="width: 5.0cm"><p class="TableRowHead">Tags:</p></th>
<td style="width: 6.0cm"><p class="Cell"></p></td>
<td style="width: 1.0cm"><p class="Cell"></p></td></tr>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="Cell">' .
        '<i>This is not actually a question. ' .
        'Instead it is a way to add some instructions, rubric or other content to the activity. ' .
        'This is similar to the way that labels can be used to add content to the course page.</i></p></td>
<td style="width: 1.0cm"><p class="Cell"></p></td></tr>
</tbody></table></div><p class="MsoNormal"></p>
  </body>
</html>
';
        $this->resetAfterTest(true);
        $this->setGuestUser();
        $exporter = new qformat_wordtable();
        $html = $exporter->presave_process($descriptionxml);

        $this->assert_same_html($expectedhtml, $html);
    }


    /**
     * Test if the exported HTML for an Essay question matches the expected output.
     */
    public function test_export_essay29() {
        $descriptionxml = '<question type="essay">
    <name><text>Moodle 2.9 Essay Question</text></name>
    <questiontext format="moodle_auto_format">
      <text><![CDATA[<p>Essay question text.</p>]]></text>
    </questiontext>
    <generalfeedback format="moodle_auto_format">
      <text><![CDATA[<p>General Feedback led the charge</p>]]></text>
    </generalfeedback>
    <defaultgrade>1.0000000</defaultgrade>
    <penalty>0.0000000</penalty>
    <hidden>0</hidden>
    <responseformat>editor</responseformat>
    <responsefieldlines>15</responsefieldlines>
    <attachments>1</attachments>
    <graderinfo format="moodle_auto_format">
      <text>Grader information.</text>
    </graderinfo>
    <responsetemplate format="moodle_auto_format">
      <text>Optional response template.</text>
    </responsetemplate>
  </question>';

        $expectedhtml = '<h2 class="MsoHeading2">Moodle 2.9 Essay Question</h2><p class="MsoBodyText"/>' .
        '<div class="TableDiv"><table border="1" dir="ltr"><thead>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="Cell">Essay question text.</p></td>
<td style="width: 1.0cm"><p class="QFType">ES</p></td></tr>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right">Default mark:</p></td>
<td style="width: 1.0cm"><p class="Cell">1</p></td></tr>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right">Response format:</p></td>
<td style="width: 1.0cm"><p class="Cell">HTML editor</p></td></tr>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right">Require text:</p></td>
<td style="width: 1.0cm"><p class="Cell">Yes</p></td></tr><tr>
<td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right">Input box size:</p></td>
<td style="width: 1.0cm"><p class="Cell">15</p></td></tr>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right">Allow attachments:</p></td>
<td style="width: 1.0cm"><p class="Cell">1</p></td></tr>

<tr>
<td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right">Require attachments:</p></td>
<td style="width: 1.0cm"><p class="Cell">0</p></td></tr>
<tr>
<td style="width: 1.0cm"><p class="Cell"></p></td>
<td style="width: 5.0cm"><p class="TableHead">Response template</p></td>
<td style="width: 6.0cm"><p class="TableHead">Information for graders</p></td>
<td style="width: 1.0cm"><p class="TableHead"></p></td></tr>
</thead><tbody>
<tr>
<td style="width: 1.0cm"><p class="Cell"></p></td>
<td style="width: 5.0cm"><p class="Cell">Optional response template.</p></td>
<td style="width: 6.0cm"><p class="Cell">Grader information.</p></td>
<td style="width: 1.0cm"><p class="Cell"></p></td></tr>
<tr>
<td style="width: 1.0cm"><p class="Cell"></p></td>
<th style="width: 5.0cm"><p class="TableRowHead">General feedback:</p></th>
<td style="width: 6.0cm"><p class="Cell">General Feedback led the charge</p></td>
<td style="width: 1.0cm"><p class="Cell"></p></td></tr>
<tr>
<td style="width: 1.0cm"><p class="Cell"></p></td>
<th style="width: 5.0cm"><p class="TableRowHead">Tags:</p></th>
<td style="width: 6.0cm"><p class="Cell"></p></td>
<td style="width: 1.0cm"><p class="Cell"></p></td></tr>
<tr>
<td colspan="3" style="width: 12.0cm"><p class="Cell"><i>Allows a response of a few sentences or paragraphs. ' .
        'This must then be graded manually.</i></p></td>
<td style="width: 1.0cm"><p class="Cell"></p></td></tr>
</tbody></table></div><p class="MsoNormal"></p>
  </body>
</html>
';
        $this->resetAfterTest(true);
        $this->setGuestUser();
        $exporter = new qformat_wordtable();
        $html = $exporter->presave_process($descriptionxml);

        $this->assert_same_html($expectedhtml, $html);
    }
}
