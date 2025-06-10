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
 * Test helpers for the Wiris Essay question type.
 *
 * @package    qtype_wirisessay
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qtype_essaywiris_test_helper extends question_test_helper {
    public function get_test_questions() {
        return array('scienceessay');
    }

    /**
     * Get the form data that corresponds to saving a Science Essay question.
     *
     * @return stdClass simulated question form data.
     */
    public function get_essaywiris_question_form_data_scienceessay() {
        $form = new stdClass();

        $form->name = 'Essay wiris';
        $form->questiontext = array(
            'text' => 'This is the text #a and #b.',
            'format' => FORMAT_HTML,
        );
        $form->defaultmark = 1.0;
        $form->generalfeedback = array(
            'text' => '',
            'format' => FORMAT_HTML,
        );
        $form->responseformat = 'editor';
        $form->responserequired = 1;
        $form->responsefieldlines = 10;
        $form->attachments = 0;
        $form->attachmentsrequired = 0;
        $form->filetypeslist = '';
        $form->graderinfo = array('text' => '', 'format' => FORMAT_HTML);
        $form->responsetemplate = array('text' => '', 'format' => FORMAT_HTML);

        // Wiris specific information.
        $form->wirisquestion = '<question><wirisCasSession><![CDATA[<wiriscalc version="3.1"><title>
            <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Untitled calc</mtext></math></title><properties>
            <property name="lang">en</property></properties><session lang="en" version="2.0"><library closed="false">
            <mtext style="color:#ffc800" xml:lang="en">variables</mtext><group><command><input>
            <math xmlns="http://www.w3.org/1998/Math/MathML"><mi>a</mi><mo>=</mo><mo>-</mo><mn>10</mn></math></input></command>
            <command><input><math xmlns="http://www.w3.org/1998/Math/MathML"><mi>b</mi><mo>=</mo><mfrac><mn>15</mn><mn>171</mn>
            </mfrac></math></input></command></group></library></session><constructions>
            <construction group="1">{&quot;elements&quot;:[],&quot;constraints&quot;:[],&quot;displays&quot;:[],&quot;
            handwriting&quot;:[]}</construction></constructions></wiriscalc>]]>
            </wirisCasSession><options><option name="precision">4</option><option name="times_operator">·</option>
            <option name="implicit_times_operator">false</option><option name="imaginary_unit">i</option></options><localData>
            <data name="cas">false</data><data name="casSession"><![CDATA[<wiriscalc version="3.1"><title>
            <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Auxiliary computations and notes</mtext></math></title>
            <properties><property name="lang">en</property><property name="precision">4</property>
            <property name="use_degrees">false</property></properties><session version="3.0" lang="en"><task><title>
            <math xmlns="http://www.w3.org/1998/Math/MathML"><mtext>Sheet 1</mtext></math></title><group><command><input>
            <math xmlns="http://www.w3.org/1998/Math/MathML"/></input></command></group></task></session></wiriscalc>]]></data>
            </localData></question>';
        $form->wirislang = 'en';
        $form->wirisessay = '';

        return $form;
    }
}
