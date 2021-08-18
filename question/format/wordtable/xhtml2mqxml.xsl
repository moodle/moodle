<xsl:stylesheet version="1.0"
    xmlns:x="http://www.w3.org/1999/xhtml"
    xmlns:mml="http://www.w3.org/1998/Math/MathML"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    exclude-result-prefixes="x"
>
<!--
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

 * XSLT stylesheet to transform XHTML tables derived from Word 2010 files  into Moodle Question XML questions 
 *
 * @package qformat_wordtable
 * @copyright 2010-2015 Eoin Campbell
 * @author Eoin Campbell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
-->

<!-- Settings -->
<xsl:output encoding="UTF-8" method="xml" indent="yes" />

<!-- Top Level Parameters -->
<xsl:param name="debug_flag" select="0" />
<xsl:param name="moodle_release"/>  <!-- The release number of the current Moodle server -->
<xsl:param name="moodle_language"/>  <!-- The current language interface selected by the user -->

<!-- Top Level Variables derived from input -->
<xsl:variable name="metadata" select="//x:html/x:head"/>
<xsl:variable name="courseID" select="$metadata/x:meta[@name='moodleCourseID']/@content" />
<!-- Get the Moodle version as a simple 2-digit number, e.g. 2.6.5 => 26 -->
<xsl:variable name="moodleReleaseNumber" select="substring(translate($moodle_release, '.', ''), 1, 2)"/>

<xsl:variable name="fileLanguage">
    <xsl:variable name="moodleLanguage" select="$metadata/x:meta[@name='moodleLanguage']/@content" />
    <xsl:choose>
    <xsl:when test="$moodleLanguage = ''"><xsl:value-of select="'en'"/></xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodleLanguage"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<xsl:variable name="moodle_labels" select="//moodlelabels" />

<!-- Default column numbers-->
<xsl:variable name="nColumns" select="4"/>
<xsl:variable name="n_tdColumns" select="3"/>
<xsl:variable name="option_colnum" select="2"/>
<xsl:variable name="flag_value_colnum" select="2"/>
<xsl:variable name="specific_feedback_colnum" select="3"/>
<xsl:variable name="match_colnum" select="3"/>
<xsl:variable name="generic_feedback_colnum" select="2"/> <!-- 2 because the label cell is a th -->
<xsl:variable name="hints_colnum" select="2"/> <!-- 2 because the label cell is a th -->
<xsl:variable name="tags_colnum" select="2"/> <!-- 2 because the label cell is a th -->
<xsl:variable name="graderinfo_colnum" select="3"/>
<xsl:variable name="responsetemplate_colnum" select="2"/>

<!-- Preview question: if 0, return all questions, otherwise just a single question -->
<xsl:variable name="moodlePreviewQuestion" select="$metadata/x:meta[@name='moodlePreviewQuestion']/@content" />
<xsl:variable name="moodlePreviewQuestionID" select="$metadata/x:meta[@name='moodlePreviewQuestionID']/@content" />
<xsl:variable name="questionPenalty">
    <xsl:choose>
    <xsl:when test="$metadata/x:meta[@name='moodleDefaultPenalty']">
        <xsl:value-of select="$metadata/x:meta[@name='moodleDefaultPenalty']/@content" />
    </xsl:when>
    <xsl:otherwise>0.1</xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- General Variables -->
<xsl:variable name="ucase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ&#x0a;'"/>
<xsl:variable name="lcase" select="'abcdefghijklmnopqrstuvwxyz'"/>
<xsl:variable name="image_metafolder" select="'@@PLUGINFILE@@'"/>
<xsl:variable name="image_encoding" select="'base64'"/>

<!-- Handle colon usage in French -->
<xsl:variable name="colon_string">
    <xsl:choose>
    <xsl:when test="starts-with($fileLanguage, 'fr')"><xsl:text> :</xsl:text></xsl:when>
    <xsl:otherwise><xsl:text>:</xsl:text></xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- Message to include in output if the current user interface language doesn't match the document language -->
<xsl:variable name="interface_language_mismatch" select="$moodle_labels/data[@name = 'qformat_wordtable_interface_language_mismatch']"/>

<xsl:variable name="categoryname_label" select="$moodle_labels/data[@name = 'moodle_categoryname']"/>
<xsl:variable name="defaultmark_label">
    <xsl:choose>
    <xsl:when test="$moodleReleaseNumber = '1'">
        <xsl:value-of select="$moodle_labels/data[@name = 'quiz_defaultgrade']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="normalize-space($moodle_labels/data[@name = 'question_defaultmark'])"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="grade_label" select="$moodle_labels/data[@name = 'moodle_grade']"/>
<xsl:variable name="no_label" select="normalize-space(translate($moodle_labels/data[@name = 'moodle_no'], $ucase, $lcase))"/>
<xsl:variable name="yes_label" select="normalize-space(translate($moodle_labels/data[@name = 'moodle_yes'], $ucase, $lcase))"/>
<xsl:variable name="item_label" select="$moodle_labels/data[@name = 'grades_item']"/>
<xsl:variable name="penalty_label">
    <xsl:choose>
    <xsl:when test="$moodleReleaseNumber = '1'">
        <xsl:value-of select="$moodle_labels/data[@name = 'quiz_penaltyfactor']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodle_labels/data[@name = 'question_penaltyforeachincorrecttry']"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="hint_clearwrongparts_label" select="$moodle_labels/data[@name = 'question_clearwrongparts']"/>
<xsl:variable name="hint_shownumcorrect_label" select="$moodle_labels/data[@name = 'question_shownumpartscorrect']"/>

<xsl:variable name="question_label" select="$moodle_labels/data[@name = 'moodle_question']"/>
<xsl:variable name="tags_label" select="$moodle_labels/data[@name = 'moodle_tags']"/>
<xsl:variable name="hintn_label" select="$moodle_labels/data[@name = 'question_hintn']"/>

<xsl:variable name="mcq_shuffleanswers_label" select="$moodle_labels/data[@name = 'qtype_multichoice_shuffleanswers']"/>
<xsl:variable name="quiz_shuffle_label" select="$moodle_labels/data[@name = 'quiz_shuffle']"/>
<xsl:variable name="answernumbering_label" select="$moodle_labels/data[@name = 'qtype_multichoice_answernumbering']"/>

<!-- ID Number (Moodle 3.6+) -->
<xsl:variable name="idnumber_label" select="$moodle_labels/data[@name = 'question_idnumber']"/>


<!-- Generic feedback labels -->
<xsl:variable name="correctfeedback_label" select="$moodle_labels/data[@name = 'qtype_multichoice_correctfeedback']"/>
<xsl:variable name="feedback_label" select="$moodle_labels/data[@name = 'moodle_feedback']"/>
<xsl:variable name="generalfeedback_label" select="$moodle_labels/data[@name = 'question_generalfeedback']"/>
<xsl:variable name="incorrectfeedback_label" select="$moodle_labels/data[@name = 'qtype_multichoice_incorrectfeedback']"/>
<xsl:variable name="pcorrectfeedback_label" select="$moodle_labels/data[@name = 'qtype_multichoice_partiallycorrectfeedback']"/>

<!-- Description labels -->
<xsl:variable name="description_instructions" select="$moodle_labels/data[@name = 'qtype_description_pluginnamesummary']"/>

<!-- Essay question labels -->
<xsl:variable name="acceptedfiletypes_label" select="$moodle_labels/data[@name = 'qtype_essay_acceptedfiletypes']"/>
<xsl:variable name="allowattachments_label" select="$moodle_labels/data[@name = 'qtype_essay_allowattachments']"/>
<xsl:variable name="attachmentsrequired_label" select="$moodle_labels/data[@name = 'qtype_essay_attachmentsrequired']"/>
<xsl:variable name="graderinfo_label" select="$moodle_labels/data[@name = 'qtype_essay_graderinfo']"/>

<xsl:variable name="responseformat_label" select="$moodle_labels/data[@name = 'qtype_essay_responseformat']"/>
<xsl:variable name="responserequired_label" select="$moodle_labels/data[@name = 'qtype_essay_responserequired']"/>
<xsl:variable name="responseformateditor_label" select="normalize-space(translate($moodle_labels/data[@name = 'qtype_essay_formateditor'], $ucase, $lcase))"/>
<xsl:variable name="responseformateditorfilepicker_label" select="normalize-space(translate($moodle_labels/data[@name = 'qtype_essay_formateditorfilepicker'], $ucase, $lcase))"/>
<xsl:variable name="responseformatmono_label" select="normalize-space(translate($moodle_labels/data[@name = 'qtype_essay_formatmonospaced'], $ucase, $lcase))"/>
<xsl:variable name="responseformatnoinline_label" select="normalize-space(translate($moodle_labels/data[@name = 'qtype_essay_formatnoinline'], $ucase, $lcase))"/>
<xsl:variable name="responseformatplain_label" select="normalize-space(translate($moodle_labels/data[@name = 'qtype_essay_formatplain'], $ucase, $lcase))"/>

<xsl:variable name="responsetemplate_help_label" select="normalize-space(translate($moodle_labels/data[@name = 'qtype_essay_responsetemplate_help'], $ucase, $lcase))"/>
<xsl:variable name="responsefieldlines_label" select="$moodle_labels/data[@name = 'qtype_essay_responsefieldlines']"/>


<!-- Multichoice/Multi-Answer question labels -->
<xsl:variable name="choice_label">
    <xsl:variable name="choice_text" select="$moodle_labels/data[@name = 'qtype_multichoice_choiceno']"/>
    <xsl:choose>
    <xsl:when test="contains($choice_text, '{')">
        <xsl:value-of select="normalize-space(substring-before($choice_text, '{'))"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$choice_text"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="showNumCorrect_label" select="$moodle_labels/data[@name = 'question_shownumpartscorrectwhenfinished']"/>
<xsl:variable name="multichoice_instructions" select="concat($moodle_labels/data[@name = 'qtype_multichoice_pluginnamesummary'], ' (MC/MA)')"/>

<!-- Multichoice Set (All-or-Nothing Multichoice) question labels -->
<xsl:variable name="multichoiceset_showeachanswerfeedback_label" select="$moodle_labels/data[@name = 'qtype_multichoiceset_showeachanswerfeedback']"/>
<xsl:variable name="multichoiceset_instructions" select="$moodle_labels/data[@name = 'qtype_multichoiceset_pluginnamesummary']"/>

<!-- Short Answer question labels -->
<xsl:variable name="casesensitive_label" select="$moodle_labels/data[@name = 'qtype_shortanswer_casesensitive']"/>
<xsl:variable name="shortanswer_correctanswers_label" select="$moodle_labels/data[@name = 'qtype_shortanswer_correctanswers']"/>
<xsl:variable name="shortanswer_instructions" select="$moodle_labels/data[@name = 'qtype_shortanswer_filloutoneanswer']"/>

<!-- True/False question labels -->
<xsl:variable name="true_label" select="$moodle_labels/data[@name = 'qtype_truefalse_true']"/>

<!-- Gapselect (Select missing word) question labels -->
<xsl:variable name="gapselect_shuffle_label" select="$moodle_labels/data[@name = 'qtype_gapselect_shuffle']"/>
<xsl:variable name="gapselect_instructions" select="$moodle_labels/data[@name = 'qtype_gapselect_pluginnamesummary']"/>

<!-- Drag and Drop question labels -->
<xsl:variable name="ddi_shuffleanswers_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_shuffleimages']"/>
<xsl:variable name="ddi_dropzoneheader_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_dropzoneheader']"/>
<xsl:variable name="ddi_draggableitem_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_draggableitem']"/>
<xsl:variable name="ddi_text_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_text']"/>
<xsl:variable name="ddi_bgimage_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_bgimage']"/>
<xsl:variable name="ddi_coords_label">
    <xsl:value-of select="$moodle_labels/data[@name = 'qtype_ddimageortext_xleft']"/>
    <xsl:text>, </xsl:text>
    <xsl:value-of select="$moodle_labels/data[@name = 'qtype_ddimageortext_ytop']"/>
</xsl:variable>
<xsl:variable name="ddi_infinite_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_infinite']"/>
<xsl:variable name="ddi_instructions" select="$moodle_labels/data[@name = 'qtype_ddimageortext_pluginnamesummary']"/>
<xsl:variable name="ddi_shape_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_shape']"/>

<xsl:variable name="ddm_circle_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shape_circle']"/>
<xsl:variable name="ddm_hint_clearwrongparts_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_clearwrongparts']"/>
<xsl:variable name="ddm_coords_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_coords']"/>
<xsl:variable name="ddm_marker_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_marker']"/>
<xsl:variable name="ddm_instructions" select="$moodle_labels/data[@name = 'qtype_ddmarker_pluginnamesummary']"/>
<xsl:variable name="ddm_infinite_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_infinite']"/>
<xsl:variable name="ddm_number_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_number']"/>
<xsl:variable name="ddm_polygon_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shape_polygon']"/>
<xsl:variable name="ddm_rectangle_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shape_rectangle']"/>
<xsl:variable name="ddm_shape_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shape']"/>
<xsl:variable name="ddm_showmisplaced_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_showmisplaced']"/>
<xsl:variable name="ddm_shuffleimages_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shuffleimages']"/>
<xsl:variable name="ddm_hint_stateincorrectlyplaced_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_stateincorrectlyplaced']"/>

<xsl:variable name="ddt_infinite_label" select="$moodle_labels/data[@name = 'qtype_ddwtos_infinite']"/>
<xsl:variable name="ddt_shuffle_label" select="$moodle_labels/data[@name = 'qtype_ddwtos_shuffle']"/>
<xsl:variable name="ddt_instructions" select="$moodle_labels/data[@name = 'qtype_ddwtos_pluginnamesummary']"/>


<!-- Template Matches -->
<xsl:template match="/">
    
    <quiz>
    <!--
    <xsl:for-each select="$moodle_labels">
      <xsl:text>&#x0a;</xsl:text>
      <xsl:comment><xsl:value-of select="concat(@name, '=&quot;', ., '&quot;')"/></xsl:comment>
    </xsl:for-each>
    -->
        <xsl:value-of select="'&#x0a;'"/>
        <xsl:comment>Course ID (Title): <xsl:value-of select="concat($courseID, ' (', //x:html/x:body/x:div/x:p[@class = 'title'], ')')"/></xsl:comment>
        <xsl:value-of select="'&#x0a;'"/>
        <xsl:comment>moodlePreviewQuestion: <xsl:value-of select="$moodlePreviewQuestion"/></xsl:comment>
        <xsl:value-of select="'&#x0a;'"/>
        <xsl:comment>moodlePreviewQuestionID: <xsl:value-of select="$moodlePreviewQuestionID"/></xsl:comment>
        <xsl:value-of select="'&#x0a;'"/>
        <xsl:comment>moodle_language: <xsl:value-of select="$moodle_language"/></xsl:comment>
        <xsl:value-of select="'&#x0a;'"/>
        <xsl:comment>fileLanguage: <xsl:value-of select="$fileLanguage"/></xsl:comment>
        <xsl:value-of select="'&#x0a;'"/>
         <xsl:comment>moodleReleaseNumber: <xsl:value-of select="$moodleReleaseNumber"/></xsl:comment>
        <xsl:value-of select="'&#x0a;'"/>
        <!-- 3 cases to handle: a) 1 preview question; b) language mismatch; c) all questions -->
        <xsl:choose>
        <!-- If preview flag set, return 1 question, and set the category to 'zzPreview' -->
        <xsl:when test="$moodlePreviewQuestion != 0">
            <question type="category">
                <category><text><xsl:value-of select="'$course$/zzPreview'"/></text></category>
            </question>

            <xsl:for-each select="//x:table[@class='moodleQuestion']">
                <!-- <xsl:comment>div position() = <xsl:value-of select="position()"/></xsl:comment> -->

                <xsl:if test="position() = $moodlePreviewQuestion">
                    <xsl:variable name="table_root" select="."/>
                    <xsl:variable name="qtype" select="translate(normalize-space($table_root/x:thead/x:tr[1]/x:th[position() = $flag_value_colnum]), $lcase, $ucase)" />
                    
                    <xsl:comment>preview question[<xsl:value-of select="position()"/>] type = <xsl:value-of select="$qtype"/></xsl:comment>

                    <xsl:call-template name="itemAssessment">
                        <xsl:with-param name="qtype" select="$qtype" />
                        <xsl:with-param name="table_root" select="$table_root" />
                        <xsl:with-param name="category" select="'zzPreview'" />
                    </xsl:call-template>
                </xsl:if>
            </xsl:for-each>
        </xsl:when>
        <xsl:when test="$fileLanguage != $moodle_language">
            <!-- The Moodle user interface language doesn't match the documents template language, so the question labels won't match: report an error in a dummy question that will display on the screen -->
            <xsl:variable name="language_mismatch_error_message" select="concat($interface_language_mismatch, ' &quot;', $fileLanguage, '&quot; != &quot;', $moodle_language, '&quot;')"/>
            <question type="category">
                <category><text><xsl:value-of select="'$course$/zzPreview'"/></text></category>
            </question>
            <question type="description">
                <name><text><xsl:value-of select="$language_mismatch_error_message"/></text></name>
                <questiontext>
                    <text><xsl:value-of select="$language_mismatch_error_message"/></text>
                </questiontext>
                <defaultgrade>0.000000</defaultgrade>
                <penalty>0</penalty>
                <hidden>0</hidden>
                <shuffleanswers>false</shuffleanswers>
            </question>
        </xsl:when>
        <xsl:otherwise>
            <!-- Not a preview, so import all questions -->
            <xsl:for-each select="//x:table[@class='moodleQuestion']">
                <xsl:variable name="qtype" select="translate(normalize-space(./x:thead/x:tr[1]/x:th[position() = $flag_value_colnum]), $lcase, $ucase)" />
                <xsl:variable name="table_root" select="."/>
                <xsl:variable name="id_quest" select="position()"/>

                <!-- Get the category from the closest preceding Heading 1 -->
                <!-- Strip out any numbering before a tab for the category -->
                <xsl:variable name="raw_category">
                    <xsl:choose>
                    <xsl:when test="contains(preceding::x:h1[1], '&#9;')">
                        <xsl:value-of select="normalize-space(substring-after(preceding::x:h1[1], '&#9;'))"/>
                    </xsl:when>
                    <xsl:otherwise><xsl:value-of select="normalize-space(preceding::x:h1[1])"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>

                <xsl:text>&#x0a;</xsl:text>
                <question type="category">
                    <category><text>
                        <!-- Add in the course prefix if not present (it shouldn't be, I think) -->
                        <xsl:choose>
                        <xsl:when test="contains($raw_category, '$course$/')"><xsl:value-of select="$raw_category"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="concat('$course$/', $raw_category)"/></xsl:otherwise>
                        </xsl:choose>
                        </text></category>
                </question>

                <!--<xsl:comment>table#: <xsl:value-of select="$id_quest"/>; nColumns: <xsl:value-of select="$nColumns"/>; qtype: <xsl:value-of select="$qtype"/>; </xsl:comment>-->
                <xsl:call-template name="itemAssessment">
                    <xsl:with-param name="qtype" select="$qtype" />
                    <xsl:with-param name="table_root" select="$table_root" />
                    <xsl:with-param name="id_quest" select="$id_quest" />
                    <xsl:with-param name="category" select="$raw_category" />
                </xsl:call-template>
            </xsl:for-each>
        </xsl:otherwise>
        </xsl:choose>
    </quiz>
</xsl:template>

<!-- Process a full item -->
<xsl:template name="itemAssessment">
    <xsl:param name="qtype"/>
    <xsl:param name="table_root"/>
    <xsl:param name="id_quest"/>
    <xsl:param name="category"/>

    <xsl:text>&#x0a;</xsl:text>
    <question>
        <xsl:choose>
        <xsl:when test="$qtype = 'MC' or $qtype = 'MULTI-CHOICE'">
            <xsl:attribute name="type"><xsl:value-of select="'multichoice'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>

            <!-- Process the key and distractors -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Multi-choice -->
        </xsl:when>
        <xsl:when test="$qtype = 'MA' or $qtype = 'MS' or $qtype = 'MULTI-ANSWER'">
            <xsl:attribute name="type">
                <xsl:value-of select="'multichoice'"/>
                <xsl:if test="$qtype = 'MS'">
                    <xsl:value-of select="'set'"/>
                </xsl:if>
            </xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>

            <!-- Process the key and distractors -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Multi-answer -->
        </xsl:when>
        <xsl:when test="$qtype = 'SA' or $qtype = 'SHORT ANSWER'">
            <xsl:attribute name="type"><xsl:value-of select="'shortanswer'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
                <xsl:with-param name="nColumns" select="$nColumns"/>
            </xsl:call-template>

            <!-- Process the key and distractors -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Multi-answer -->
        </xsl:when>
        <xsl:when test="$qtype = 'TF' or $qtype = 'TRUE-FALSE'">
            <xsl:attribute name="type"><xsl:value-of select="'truefalse'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>

            <!-- Process the key and distractors -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End True/False -->
        </xsl:when>
        <xsl:when test="$qtype = 'MAT' or $qtype = 'MATCHING'">
            <xsl:attribute name="type"><xsl:value-of select="'matching'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>

            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <subquestion>
                    <xsl:if test="$moodleReleaseNumber &gt; '19'">
                        <xsl:attribute name="format"><xsl:text>html</xsl:text></xsl:attribute>
                    </xsl:if>

                    <xsl:call-template name="rich_text_content">
                        <xsl:with-param name="content" select="x:td[position() = $option_colnum]"/>
                    </xsl:call-template>

                    <!-- Only plain text allowed for matching answers -->
                    <xsl:variable name="plain_text_target">
                        <xsl:choose>
                        <xsl:when test="contains(x:td[position() = $match_colnum], '&#x9;')">
                            <xsl:value-of select="substring-after(x:td[position() = $match_colnum], '&#x9;')"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="x:td[position() = $match_colnum]"/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:variable>
                    <answer><text><xsl:value-of select="normalize-space($plain_text_target)"/></text></answer>
                </subquestion>
            </xsl:for-each>
        <!-- End Multi-choice -->
        </xsl:when>
        <xsl:when test="$qtype = 'CL' or $qtype = 'CLOZE'">
            <xsl:attribute name="type"><xsl:value-of select="'cloze'"/></xsl:attribute>

            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>
            <!-- End Cloze -->
        </xsl:when>
        <!-- Numerical not supported yet, really -->
        <xsl:when test="$qtype = 'NU' or $qtype = 'NUM' or $qtype = 'NUMERICAL'">
            <xsl:attribute name="type"><xsl:value-of select="'numerical'"/></xsl:attribute>

            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>

            <!-- Process the key and distractors -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Numerical -->
        </xsl:when>
        <xsl:when test="$qtype = 'ES' or $qtype = 'ESSAY'">
            <xsl:attribute name="type"><xsl:value-of select="'essay'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>
            <!-- End Essay -->
        </xsl:when>
        <xsl:when test="$qtype = 'DE' or $qtype = 'DESCRIPTION'">
            <xsl:attribute name="type"><xsl:value-of select="'description'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
            </xsl:call-template>
            <!-- End Description -->
        </xsl:when>
        <xsl:when test="$qtype = 'MW' or $qtype = 'GAPSELECT'">
            <xsl:attribute name="type"><xsl:value-of select="'gapselect'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
                <xsl:with-param name="nColumns" select="$nColumns"/>
            </xsl:call-template>

            <!-- Process the words and groups -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Missing Word -->
        </xsl:when>
        <xsl:when test="$qtype = 'DDI'">
            <xsl:attribute name="type"><xsl:value-of select="'ddimageortext'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
                <xsl:with-param name="nColumns" select="$nColumns"/>
            </xsl:call-template>

            <!-- Identify the end of the draggables, to avoid treating drop zone items as draggables -->
            <xsl:variable name="dropzone_heading_row" select="$table_root/x:tbody/x:tr[contains(x:td[1], '#')]"/>
            <xsl:variable name="draggable_rows" select="count($dropzone_heading_row/preceding-sibling::x:tr)"/>
            <xsl:comment><xsl:value-of select="concat('draggable_rows: ', $draggable_rows)"/></xsl:comment>

            <!-- Process the draggable items -->
            <xsl:text>&#x0a;</xsl:text>
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns and position() &lt;= $draggable_rows]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <xsl:text>&#x0a;</xsl:text>

            <!-- Process the dropzone items -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns and position() &gt; $draggable_rows + 1]">
                <xsl:call-template name="process_dropzone_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Drag and Drop onto image -->
        </xsl:when>
        <xsl:when test="$qtype = 'DDM'">
            <xsl:attribute name="type"><xsl:value-of select="'ddmarker'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
                <xsl:with-param name="nColumns" select="$nColumns"/>
            </xsl:call-template>

            <!-- Identify the end of the draggables, to avoid treating drop zone items as draggables -->
            <xsl:variable name="dropzone_heading_row" select="$table_root/x:tbody/x:tr[contains(x:td[1], '#')]"/>
            <xsl:variable name="draggable_rows" select="count($dropzone_heading_row/preceding-sibling::x:tr)"/>
            <xsl:comment><xsl:value-of select="concat('draggable_rows: ', $draggable_rows)"/></xsl:comment>

            <!-- Process the draggable items -->
            <xsl:text>&#x0a;</xsl:text>
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns and position() &lt;= $draggable_rows]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <xsl:text>&#x0a;</xsl:text>

            <!-- Process the dropzone items -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns and position() &gt; $draggable_rows + 1]">
                <xsl:call-template name="process_dropzone_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Drag and Drop onto marker -->
        </xsl:when>
        <xsl:when test="$qtype = 'DDT'">
            <xsl:attribute name="type"><xsl:value-of select="'ddwtos'"/></xsl:attribute>
            <xsl:call-template name="itemStem">
                <xsl:with-param name="table_root" select="$table_root"/>
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="category" select="$category"/>
                <xsl:with-param name="nColumns" select="$nColumns"/>
            </xsl:call-template>

            <!-- Process the words and groups -->
            <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $nColumns]">
                <xsl:call-template name="process_row">
                    <xsl:with-param name="table_row" select="$table_root/x:tbody/x:tr"/>
                    <xsl:with-param name="qtype" select="$qtype"/>
                </xsl:call-template>
            </xsl:for-each>
            <!-- End Drag and Drop Words to Sentence -->
        </xsl:when>
        </xsl:choose>

        <!-- Handle any hints that are included, 3 or 4 (MS, DDM) rows for each successive hint -->
        <xsl:if test="$moodleReleaseNumber &gt; '19'">
            <xsl:variable name="hintn_prefix" select="normalize-space(substring-before($hintn_label, '{no}'))"/>
            <xsl:for-each select="$table_root/x:tbody/x:tr[starts-with(normalize-space(x:th), $hintn_prefix)]">
                <xsl:variable name="current_hint_row_num" select="position()"/>
                <xsl:variable name="hint_text_norm" select="normalize-space(x:td[position() = $hints_colnum])"/>
                <xsl:if test="$hint_text_norm != '' and $hint_text_norm != '&#160;' and $hint_text_norm != '_'">
                    <hint format="html">
                        <xsl:call-template name="rich_text_content">
                            <xsl:with-param name="content" select="x:td[position() = $hints_colnum]"/>
                        </xsl:call-template>

                        <!-- Get the 1st row following the hint, check that it has the correct label, defining the handling of the hint (show number of parts correct) -->
                        <xsl:variable name="hint_sncf_cell" select="normalize-space(following-sibling::x:tr[1]/x:th)"/>
                        <xsl:variable name="hint_sncf_cell_lc" select="translate($hint_sncf_cell, $ucase, $lcase)"/>
                        <xsl:variable name="current_hint_shownumcorrect_flag">
                            <xsl:if test="starts-with($hint_sncf_cell_lc, translate($hint_shownumcorrect_label, $ucase, $lcase))">
                                <xsl:value-of select="translate(normalize-space(following-sibling::x:tr[1]/x:td[position() = $hints_colnum]), $ucase, $lcase)"/>
                            </xsl:if>
                            <!--
                            <xsl:if test="starts-with(translate(normalize-space(following-sibling::x:tr[1]/x:td[2]), $ucase, $lcase), translate($hint_shownumcorrect_label, $ucase, $lcase))">
                                <xsl:value-of select="translate(normalize-space(following-sibling::x:tr[1]/x:td[position() = $hints_colnum]), $ucase, $lcase)"/>
                            </xsl:if>
                            -->
                        </xsl:variable>
                        <xsl:if test="contains($current_hint_shownumcorrect_flag, $yes_label)">
                            <shownumcorrect/>
                        </xsl:if>

                        <!-- Get the 2nd row following the hint, check that it has the correct label, defining the handling of the hint (clear wrong parts or move incorrect markers (DDM)) -->
                        <xsl:variable name="clearwrongparts_label">
                            <xsl:choose>
                            <xsl:when test="$qtype = 'DDM'">
                                <xsl:value-of select="$ddm_hint_clearwrongparts_label"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="$hint_clearwrongparts_label"/>
                            </xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>

                        <xsl:variable name="current_hint_clearwrongparts_flag">
                            <xsl:if test="starts-with(translate(normalize-space(following-sibling::x:tr[2]/x:th), $ucase, $lcase), translate($clearwrongparts_label, $ucase, $lcase))">
                                <xsl:value-of select="translate(normalize-space(following-sibling::x:tr[2]/x:td[position() = $hints_colnum]), $ucase, $lcase)"/>
                            </xsl:if>
                        </xsl:variable>
                        <!--
                        <xsl:comment><xsl:value-of select="concat('current_row: ', $current_hint_row_num, '; clearwrongparts: ', $current_hint_clearwrongparts_flag, '; following-sibling::x:tr[2]/x:td[1]: ', following-sibling::x:tr[2]/x:td[1])"/></xsl:comment>
                         -->
                        <xsl:if test="contains($current_hint_clearwrongparts_flag, $yes_label)">
                            <clearwrong/>
                        </xsl:if>

                        <!-- Get the 3rd row following the hint, check if it has a MS/DDM-specific label -->
                        <!--DDM: State which markers are incorrectly placed; MS: Show the feedback for the selected responses-->
                        <xsl:if test="$qtype = 'MS' or $qtype = 'DDM'">
                            <xsl:variable name="current_hint_options_label">
                                <xsl:choose>
                                <xsl:when test="$qtype = 'DDM'">
                                    <xsl:value-of select="$ddm_hint_stateincorrectlyplaced_label"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="$multichoiceset_showeachanswerfeedback_label"/>
                                </xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>
                            <xsl:variable name="current_hint_options_flag">
                                <xsl:if test="starts-with(translate(normalize-space(following-sibling::x:tr[3]/x:th), $ucase, $lcase), translate($current_hint_options_label, $ucase, $lcase))">
                                    <xsl:value-of select="translate(normalize-space(following-sibling::x:tr[3]/x:td[position() = $hints_colnum]), $ucase, $lcase)"/>
                                </xsl:if>
                            </xsl:variable>
                            <xsl:call-template name="debugComment">
                                <xsl:with-param name="comment_text" select="concat('current_hint_options_label: ', $current_hint_options_label, '; current_hint_options_flag: ', $current_hint_options_flag)"/>
                                <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
                            </xsl:call-template>
                            <xsl:if test="contains($current_hint_options_flag, $yes_label)">
                                <options>1</options>
                            </xsl:if>
                        </xsl:if>

                    </hint>
                </xsl:if>
            </xsl:for-each>
        </xsl:if>

        <!-- Handle any tags that are included - all in one cell, comma-separated, no distinction between pre-defined and free tags -->
        <xsl:variable name="tags_row" select="$table_root/x:tbody/x:tr[starts-with(normalize-space(x:th), $tags_label)]/x:td[position() = $tags_colnum]/*"/>
        <xsl:if test="$moodleReleaseNumber &gt; '19' and normalize-space($tags_row) != '' and normalize-space($tags_row) != '&#160;' and normalize-space($tags_row) != '_'">
            <tags>
                <xsl:choose>
                <xsl:when test="contains($tags_row, ',')">
                    <tag><text><xsl:value-of select="normalize-space(substring-before($tags_row, ','))"/></text></tag>
                    <xsl:call-template name="handle_tags_row">
                        <xsl:with-param name="tags_row" select="normalize-space(substring-after($tags_row, ','))"/>
                    </xsl:call-template>
                </xsl:when>
                <xsl:otherwise>
                    <tag><text><xsl:value-of select="$tags_row"/></text></tag>
                </xsl:otherwise>
                </xsl:choose>
            </tags>
        </xsl:if>
    </question>
    <xsl:text>&#x0a;</xsl:text>
</xsl:template>

<xsl:template name="handle_tags_row">
    <xsl:param name="tags_row"/>

    <xsl:choose>
    <xsl:when test="contains($tags_row, ',')">
        <tag><text><xsl:value-of select="normalize-space(substring-before($tags_row, ','))"/></text></tag>
        <xsl:call-template name="handle_tags_row">
            <xsl:with-param name="tags_row" select="normalize-space(substring-after($tags_row, ','))"/>
        </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
        <tag><text><xsl:value-of select="$tags_row"/></text></tag>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Process the item stem and name -->
<xsl:template name="itemStem">
    <xsl:param name="table_root"/>
    <xsl:param name="qtype"/>
    <xsl:param name="category"/>
    <xsl:param name="nColumns"/>

    <!-- First figure out the values of the various possible meta fields for each question -->
    <!-- Default mark / Default question grade -->
    <xsl:variable name="qweight_string">
        <xsl:choose>
        <xsl:when test="$table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $defaultmark_label)]">
            <xsl:value-of select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), 'Default') or starts-with(normalize-space(x:th[1]), $defaultmark_label)]/x:th[position() = $flag_value_colnum])"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="'1'"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="questionWeight">
        <xsl:choose>
        <xsl:when test="$qweight_string = '1'">
            <xsl:text>1.0000000</xsl:text>
        </xsl:when>
        <xsl:when test="$qweight_string = ''">
            <xsl:text>1.0000000</xsl:text>
        </xsl:when>
        <xsl:when test="$qweight_string = '0'">
            <xsl:text>0.0000000</xsl:text>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$qweight_string"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Shuffle (the answers)? -->
    <!-- Get the expected shuffle label for this type of question: MAT: Shuffle; MCQ: Shuffle the answers?' -->
    <xsl:variable name="shuffle_label">
        <xsl:choose>
        <xsl:when test="$qtype = 'MAT'">
            <xsl:value-of select="$quiz_shuffle_label"/>
        </xsl:when>
        <xsl:when test="$qtype = 'MA' or $qtype = 'MC' or $qtype = 'MS'">
            <xsl:value-of select="$mcq_shuffleanswers_label"/>
        </xsl:when>
        <xsl:when test="$qtype = 'MW'">
            <xsl:value-of select="$gapselect_shuffle_label"/>
        </xsl:when>
        <xsl:when test="$qtype = 'DDI' or $qtype = 'DDM'">
            <xsl:value-of select="$ddi_shuffleanswers_label"/>
        </xsl:when>
        <xsl:when test="$qtype = 'DDT'">
            <xsl:value-of select="$quiz_shuffle_label"/>
        </xsl:when>
        <xsl:otherwise> <!-- Make sure the label isn't match if the question type does not contain a shuffle flag -->
            <xsl:value-of select="'NOMATCH'"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Get the text value of the case sensitive flag, to compare it with Yes or No in the required language --> 
    <xsl:variable name="casesensitive_flag" select="normalize-space(translate($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $casesensitive_label)]/x:th[position() = $flag_value_colnum], $ucase, $lcase))"/>
    <xsl:variable name="casesensitive_value">
        <xsl:choose>
        <xsl:when test="starts-with($casesensitive_flag, $yes_label)">
            <xsl:text>1</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Get the text value of the shuffle answers flag, to compare it with Yes or No in the required language --> 
    <xsl:variable name="shuffleAnswers_flag" select="normalize-space(translate($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $shuffle_label)]/x:th[position() = $flag_value_colnum], $ucase, $lcase))"/>
    <!-- Shuffle the answers (MA/MC/MAT): Use the values true/false for Moodle 2.x and 0/1 for Moodle 1.9 -->
    <xsl:variable name="shuffleAnswers_value">
        <xsl:choose>
        <xsl:when test="$moodleReleaseNumber = '1' and starts-with($shuffleAnswers_flag, $yes_label)">
            <xsl:text>1</xsl:text>
        </xsl:when>
        <xsl:when test="$moodleReleaseNumber = '1' and starts-with($shuffleAnswers_flag, $no_label)">
            <xsl:text>0</xsl:text>
        </xsl:when>
        <xsl:when test="starts-with($shuffleAnswers_flag, $yes_label)">
            <xsl:text>true</xsl:text>
        </xsl:when>
        <xsl:when test="starts-with($shuffleAnswers_flag, $no_label)">
            <xsl:text>false</xsl:text>
        </xsl:when>
        <xsl:otherwise>
            <xsl:text>true</xsl:text>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Get the text value of the ID number field  -->
    <xsl:variable name="idnumber_string" select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $idnumber_label)]/x:th[position() = $flag_value_colnum])"/>
    <xsl:variable name="idnumber_value">
        <xsl:if test="$idnumber_string != '' and $idnumber_string != '&#160;'">
            <xsl:value-of select="$idnumber_string"/>
        </xsl:if>
    </xsl:variable>

    <!-- Answer numbering format field -->
    <xsl:variable name="answerNumbering_flag" select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $answernumbering_label)]/x:th[position() = $flag_value_colnum])"/>
    <xsl:variable name="answerNumbering_value">
        <xsl:choose>
        <xsl:when test="$answerNumbering_flag = 'A'">ABCD</xsl:when>
        <xsl:when test="$answerNumbering_flag = 'a'">abc</xsl:when>
        <xsl:when test="$answerNumbering_flag = 'i'">iii</xsl:when>
        <xsl:when test="$answerNumbering_flag = 'I'">IIII</xsl:when>
        <xsl:when test="$answerNumbering_flag = '1'">123</xsl:when>
        <xsl:otherwise>none</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Case sensitivity: used in Short Answer and Cloze (Short Answer subquestions) -->
    <xsl:variable name="cloze_sa_keyword_string">
        <xsl:if test="$qtype = 'CL'">
            <xsl:call-template name="get_cloze_sa_keyword_string">
                <xsl:with-param name="casesensitive_value" select="$casesensitive_value"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:variable>

    <!-- Orientation: used in Cloze Multichoice subquestions for radio button displays -->
    <!-- Capture select style (drop-down or radio button) and orientation (for radio buttons) and pass it on to multiple-choice sub-questions inside the Cloze question -->
    <!-- Default is drop-down -->
    <xsl:variable name="cloze_mc_keyword_string">
        <xsl:if test="$qtype = 'CL'">
            <xsl:call-template name="get_cloze_mc_orientation">
                <xsl:with-param name="cloze_mcorientation_text" select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $cloze_mcorientation_label)]/x:th[position() = $flag_value_colnum])"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:variable>

    <!-- Create a string containing all the separate distractor rows in the table, formatted the Moodle way -->
    <xsl:variable name="cloze_distractor_answer_string">
        <xsl:if test="$qtype = 'CL'">
            <xsl:call-template name="get_cloze_distractor_answer_string">
                <xsl:with-param name="table_root" select="$table_root"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:variable> <!-- cloze_distractor_answer_string -->

    <xsl:call-template name="debugComment">
        <xsl:with-param name="comment_text" select="concat('$cloze_distractor_answer_string:', $cloze_distractor_answer_string)"/>
        <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
    </xsl:call-template>

    <!-- DDM: Show misplaced items? -->
    <xsl:variable name="showmisplaced_flag" select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $ddm_showmisplaced_label)]/x:th[position() = $flag_value_colnum])"/>
    <xsl:if test="translate($showmisplaced_flag, $ucase, $lcase) = $yes_label">
        <shomisplaced/>
    </xsl:if>

    <!-- Multiple try handling -->
    <!-- Penalty factor / Penalty for each incorrect try -->
    <xsl:variable name="questionPenalty_percent" select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $penalty_label)]/x:th[position() = $flag_value_colnum])"/>
    <xsl:variable name="questionPenalty_value">
        <xsl:choose>
        <xsl:when test="$qtype ='DE' or $qtype ='ES'">0.0000000</xsl:when>
        <xsl:when test="$qtype ='TF'">1.0000000</xsl:when>
        <xsl:when test="starts-with($questionPenalty_percent, '100')">1.0</xsl:when>
        <xsl:when test="starts-with($questionPenalty_percent, '50')">0.5</xsl:when>
        <xsl:when test="starts-with($questionPenalty_percent, '33')">0.3333333</xsl:when>
        <xsl:when test="starts-with($questionPenalty_percent, '25')">0.25</xsl:when>
        <xsl:when test="starts-with($questionPenalty_percent, '20')">0.2</xsl:when>
        <xsl:when test="starts-with($questionPenalty_percent, '10')">0.1</xsl:when>
        <xsl:when test="starts-with($questionPenalty_percent, '0')">0.0000000</xsl:when>
        <xsl:otherwise>0.3333333</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Essay question format of box for answer -->
    <xsl:variable name="response_format_flag" select="translate(normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $responseformat_label)]/x:th[position() = $flag_value_colnum]), $ucase, $lcase)"/>
    <xsl:variable name="response_format_value">
        <xsl:choose>
        <xsl:when test="contains($response_format_flag, $responseformateditorfilepicker_label)">
            <xsl:text>editorfilepicker</xsl:text>
        </xsl:when>
        <xsl:when test="contains($response_format_flag, $responseformatmono_label)">
            <xsl:text>monospaced</xsl:text>
        </xsl:when>
        <xsl:when test="contains($response_format_flag, $responseformatplain_label)">
            <xsl:text>plain</xsl:text>
        </xsl:when>
        <xsl:when test="contains($response_format_flag, $responseformatnoinline_label)">
            <xsl:text>noinline</xsl:text>
        </xsl:when>
        <xsl:when test="contains($response_format_flag, $responseformateditor_label)">
            <xsl:text>editor</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>editor</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Essay question format of accepted file types -->
    <xsl:variable name="acceptedfiletypes_flag" select="translate(normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $acceptedfiletypes_label)]/x:th[position() = $flag_value_colnum]), $ucase, $lcase)"/>

    <!-- Get the name of the question, which is in the h2 preceding the table -->
    <xsl:variable name="qseqnum" select="count(preceding::x:table[@class = 'moodleQuestion'])"/>
    <xsl:variable name="raw_qname" select="../x:h2"/>
    <xsl:variable name="qname">
        <xsl:choose>
        <!-- Override the Question name if its a preview question -->
        <xsl:when test="$moodlePreviewQuestion != 0">
            <xsl:value-of select="$moodlePreviewQuestionID"/>
        </xsl:when>
        <xsl:when test="$raw_qname != '' and $raw_qname != ' ' and $raw_qname != '&#160;' and $raw_qname != '_'">
            <xsl:value-of select="$raw_qname"/>
        </xsl:when>
        <!-- if name is empty, assemble one from the category + sequence number -->
        <xsl:otherwise>
            <xsl:value-of select="concat($category, ' ', $qseqnum)"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Question name -->
    <name><text>
        <xsl:value-of select="normalize-space($qname)"/>
    </text></name>
        <!--
        <xsl:comment><xsl:value-of select="concat('raw_qname: ', $raw_qname, '; qseqnum: ', $qseqnum, '; qname: ',  $qname)"/></xsl:comment>
        <xsl:message><xsl:value-of select="concat('itemStem name: ', $qname, '; qtype: ', $qtype)"/></xsl:message> 
        -->

    <xsl:text>&#x0a;</xsl:text>
    <questiontext format="html">
        <xsl:choose>
        <xsl:when test="$qtype = 'CL'">
            <text>
                <xsl:value-of select="'&lt;![CDATA['" disable-output-escaping="yes"/>
                <!-- Cloze questions get text from question, and need special character processing -->
                <xsl:apply-templates select="$table_root/x:thead/x:tr[1]/x:th[1]/*" mode="clozeBlock">
                    <xsl:with-param name="qweight_string" select="$qweight_string"/>
                    <!-- Capture case sensitivity and pass it on to short-answer sub-questions inside the Cloze question -->
                    <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
                    <!-- Pass style/orientation of Multichoice subquestions -->
                    <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
                    <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
                </xsl:apply-templates>
                <xsl:value-of select="']]&gt;'" disable-output-escaping="yes"/>
            </text>
            <xsl:apply-templates select="$table_root/x:thead/x:tr[1]/x:th[1]//x:img" mode="moodle2pluginfile"/>
        </xsl:when>
        <xsl:otherwise>
            <!-- Standard question type, so stem is from heading -->
            <xsl:call-template name="rich_text_content">
                <xsl:with-param name="content" select="$table_root/x:thead/x:tr[1]/x:th[1]"/>
            </xsl:call-template>
        </xsl:otherwise>
        </xsl:choose>
    </questiontext>

    <!-- Handle general feedback for all questions -->
    <xsl:call-template name="debugComment">
        <xsl:with-param name="comment_text" select="concat('$generalfeedback_label:', $generalfeedback_label)"/>
        <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
    </xsl:call-template>
    <generalfeedback format="html">
        <xsl:call-template name="rich_text_content">
            <xsl:with-param name="content" select="$table_root/x:tbody/x:tr[starts-with(normalize-space(x:th), 'General') or contains(normalize-space(x:th), $generalfeedback_label)]/x:td[position() = $generic_feedback_colnum]"/>
        </xsl:call-template>
    </generalfeedback>


    <!-- Set other Moodle XML details, using defaults based on question type -->
    <!-- Default grade set for all qustion types except Cloze -->
    <xsl:if test="$qtype != 'CL'">
        <defaultgrade><xsl:value-of select="$questionWeight"/></defaultgrade>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('defaultmark_label: ', $defaultmark_label, '; Default mark: ', $qweight_string)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>
    </xsl:if>

    <!-- Penalty set for all question types, although it is 0 for some (e.g. DE, ES -->
    <xsl:call-template name="debugComment">
        <xsl:with-param name="comment_text" select="concat('penalty_label: ', $penalty_label, '; Penalty (percent): ', $questionPenalty_percent)"/>
        <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
    </xsl:call-template>
    <penalty><xsl:value-of select="$questionPenalty_value"/></penalty>
    <hidden>0</hidden>
    <xsl:if test="$moodleReleaseNumber &gt;= '36'">
        <idnumber><xsl:value-of select="$idnumber_value"/></idnumber>
    </xsl:if>

    <!-- Specific metadata for each question type -->
    <xsl:choose>
    <!-- If the type is Essay, and it is generated from Moodle 2.x, it might have a response template -->
    <xsl:when test="$qtype = 'ES' and $moodleReleaseNumber = '19'">
    </xsl:when>
    <xsl:when test="$qtype = 'ES' and $moodleReleaseNumber &gt; '19'">
        <xsl:if test="$moodleReleaseNumber &gt;= '25'">
            <responseformat><xsl:value-of select="$response_format_value"/></responseformat>

            <!-- Essays (2.7+): Is text response required? -->
            <xsl:if test="$moodleReleaseNumber &gt;= '27'">
                <xsl:variable name="responseRequired_flag" select="normalize-space(translate($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $responserequired_label)]/x:th[position() = $flag_value_colnum], $ucase, $lcase))"/>
                <!-- 0 = not required, 1 = required -->
                <xsl:variable name="responseRequired_value">
                    <xsl:choose>
                    <xsl:when test="starts-with($responseRequired_flag, $yes_label)">
                        <xsl:text>1</xsl:text>
                    </xsl:when>
                    <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <xsl:call-template name="debugComment">
                    <xsl:with-param name="comment_text" select="concat('$responseRequired_value: ', $responseRequired_value, ', $responserequired_label: ', $responserequired_label, '; $responseRequired_flag: ', $responseRequired_flag)"/>
                    <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
                </xsl:call-template>
                <responserequired><xsl:value-of select="$responseRequired_value"/></responserequired>
            </xsl:if>

            <responsefieldlines><xsl:value-of select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $responsefieldlines_label)]/x:th[position() = $flag_value_colnum])"/></responsefieldlines>
            <attachments><xsl:value-of select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $allowattachments_label)]/x:th[position() = $flag_value_colnum])"/></attachments>
        </xsl:if>

        <!-- Essays: How many attachments required? -->
        <xsl:if test="$moodleReleaseNumber &gt;= '27'">
           <attachmentsrequired><xsl:value-of select="normalize-space($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $attachmentsrequired_label)]/x:th[position() = $flag_value_colnum])"/></attachmentsrequired>
        </xsl:if>
        <graderinfo format="html">
            <xsl:call-template name="rich_text_content">
                <xsl:with-param name="content" select="$table_root/x:tbody/x:tr[1]/x:td[position() = $graderinfo_colnum]"/>
            </xsl:call-template>
        </graderinfo>

        <!-- The response template depends on what the response_format_value flag defines, if plain/mono, don't include markup -->
        <xsl:choose>
        <xsl:when test="$response_format_value = 'plain' or $response_format_value = 'mono'">
            <responsetemplate format="html">
                <text><xsl:value-of select="$table_root/x:tbody/x:tr[1]/x:td[position() = $responsetemplate_colnum]"/></text>
            </responsetemplate>
        </xsl:when>
        <xsl:otherwise>
            <responsetemplate format="html">
                <xsl:call-template name="rich_text_content">
                    <xsl:with-param name="content" select="$table_root/x:tbody/x:tr[1]/x:td[position() = $responsetemplate_colnum]"/>
                </xsl:call-template>
            </responsetemplate>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:when>
    <xsl:when test="$qtype = 'SA'">
        <usecase><xsl:value-of select="$casesensitive_value"/></usecase>
    </xsl:when>
    <xsl:when test="$qtype = 'MA' or $qtype = 'MS'">
        <single>false</single>
        <shuffleanswers><xsl:value-of select="$shuffleAnswers_value"/></shuffleanswers>
        <answernumbering><xsl:value-of select="$answerNumbering_value"/></answernumbering>
    </xsl:when>
    <xsl:when test="$qtype = 'MC'">
        <single>true</single>
        <shuffleanswers><xsl:value-of select="$shuffleAnswers_value"/></shuffleanswers>
        <answernumbering><xsl:value-of select="$answerNumbering_value"/></answernumbering>
    </xsl:when>
    <xsl:when test="$qtype = 'MAT' or $qtype = 'MW'">
        <shuffleanswers><xsl:value-of select="$shuffleAnswers_value"/></shuffleanswers>
    </xsl:when>
    <xsl:when test="($qtype = 'DDI' or $qtype = 'DDM' or $qtype = 'DDT') and $shuffleAnswers_value = 'true'">

        <shuffleanswers/>
    </xsl:when>
    </xsl:choose>

    <!-- Handle the Correct/Incorrect/Partially Correct feedback -->
    <xsl:if test="$qtype = 'MA' or $qtype = 'MC' or $qtype = 'MS' or $qtype = 'DDI' or $qtype = 'DDM' or $qtype = 'DDT' or $qtype = 'MW' or ($qtype = 'MAT' and $moodleReleaseNumber &gt; '19')">
        <xsl:variable name="cfb" select="$table_root/x:tbody/x:tr[starts-with(normalize-space(x:th), 'Correct') or starts-with(normalize-space(x:th), $correctfeedback_label)]/x:td[position() = $generic_feedback_colnum]"/>
        <xsl:variable name="ifb" select="$table_root/x:tbody/x:tr[starts-with(normalize-space(x:th), 'Incorrect') or starts-with(normalize-space(x:th), $incorrectfeedback_label)]/x:td[position() = $generic_feedback_colnum]"/>
        <xsl:variable name="pcfb" select="$table_root/x:tbody/x:tr[starts-with(normalize-space(x:th), 'Partial') or starts-with(normalize-space(x:th), $pcorrectfeedback_label)]/x:td[position() = $generic_feedback_colnum]"/>

        <!-- Show number of correct responses when finished? -->
        <xsl:variable name="showNumCorrect_flag">
            <xsl:choose>
            <xsl:when test="$qtype = 'MS'">
                <xsl:value-of select="normalize-space(translate($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $hint_shownumcorrect_label)]/x:th[position() = $flag_value_colnum], $ucase, $lcase))"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="normalize-space(translate($table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $showNumCorrect_label)]/x:th[position() = $flag_value_colnum], $ucase, $lcase))"/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="showNumCorrect_value">
            <xsl:choose>
            <xsl:when test="($qtype = 'MA' or $qtype = 'MS' or $qtype = 'DDI' or $qtype = 'DDM' or $qtype = 'DDT' or $qtype = 'MW') and starts-with($showNumCorrect_flag, $yes_label)">
                <xsl:text>true</xsl:text>
            </xsl:when>
            <xsl:otherwise><xsl:text>false</xsl:text></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <!--
        <xsl:text>&#x0a;</xsl:text>
        <xsl:comment>
            <xsl:value-of select="concat('showNumCorrect_flag: ', $showNumCorrect_flag, '; showNumCorrect_value: ', $showNumCorrect_value)"/>
            <xsl:value-of select="concat('&#x0a;showNumCorrect_label: ', $showNumCorrect_label, 
                ';&#x0a;flag: ', $table_root/x:thead/x:tr[starts-with(normalize-space(x:th[1]), $showNumCorrect_label)]/x:th[2])"/>
        </xsl:comment>
        <xsl:text>&#x0a;</xsl:text>
        -->

        <correctfeedback format="html">
            <xsl:call-template name="rich_text_content">
                <xsl:with-param name="content" select="$cfb"/>
            </xsl:call-template>
        </correctfeedback>
        <xsl:if test="$qtype != 'MS'">
            <partiallycorrectfeedback format="html">
                <xsl:call-template name="rich_text_content">
                    <xsl:with-param name="content" select="$pcfb"/>
                </xsl:call-template>
            </partiallycorrectfeedback>
        </xsl:if>
        <incorrectfeedback format="html">
            <xsl:call-template name="rich_text_content">
                <xsl:with-param name="content" select="$ifb"/>
            </xsl:call-template>
        </incorrectfeedback>
        <xsl:if test="$showNumCorrect_value = 'true'">
            <shownumcorrect/>
        </xsl:if>

        <xsl:if test="$qtype = 'DDI' or $qtype = 'DDM'">
            <xsl:apply-templates select="$table_root/x:thead/x:tr[2]/x:th" mode="moodle2pluginfile"/>
        </xsl:if>

    </xsl:if>

</xsl:template>

<!-- Answer rows for MC, MA, MS, MW, TF, and SA; DDI, DDM, DDT -->
<xsl:template name="process_row">
    <xsl:param name="table_row"/>
    <xsl:param name="qtype"/>

    <!--<xsl:comment>td[2]: <xsl:value-of select="x:tr[1]/x:td[2]"/>; td[3]: <xsl:value-of select="x:tr[1]/x:td[3]"/>; td[4]: <xsl:value-of select="x:tr[1]/x:td[4]"/></xsl:comment>-->

    <!-- Get plain text option for MW, SA and TF question types 
        TF can contain only 'true' or 'false', while MW and SA anwsers must be matchable strings -->
    <xsl:variable name="plain_text">
        <xsl:choose>
        <xsl:when test="contains(x:td[position() = $option_colnum], '&#x9;')">
            <xsl:value-of select="substring-after(x:td[position() = $option_colnum], '&#x9;')"/>
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="x:td[position() = $option_colnum]"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Get fraction from input, but use 100 for Short Answer questions and 0 for Essays -->
    <xsl:variable name="grade_cell" select="normalize-space(x:td[position() = $nColumns])"/>
    <xsl:variable name="fraction_value">
        <xsl:choose>
        <xsl:when test="$grade_cell = '83.3'"><xsl:text>83.33333</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '66.6'"><xsl:text>66.66667</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '33.3'"><xsl:text>33.33333</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '16.6'"><xsl:text>16.66667</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '14.3'"><xsl:text>14.28571</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '11.1'"><xsl:text>11.11111</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '-83.3'"><xsl:text>-83.33333</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '-66.6'"><xsl:text>-66.66667</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '-33.3'"><xsl:text>-33.33333</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '-16.6'"><xsl:text>-16.66667</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '-14.3'"><xsl:text>-14.28571</xsl:text></xsl:when>
        <xsl:when test="$grade_cell = '-11.1'"><xsl:text>-11.11111</xsl:text></xsl:when>
        <!-- Test for empty or non-breaking space, as happens in original T/F question template -->
        <xsl:when test="$grade_cell = '&#xa0;'"><xsl:text>0</xsl:text></xsl:when>
        <!-- Test for correct and incorrect symbols, but it is never used, I think -->
        <xsl:when test="$grade_cell = '&#x2611;' and ($qtype = 'MA' or $qtype = 'MS')"><xsl:value-of select="'50'"/></xsl:when>
        <xsl:when test="$grade_cell = '&#x2611;' and $qtype = 'MC'"><xsl:value-of select="'100'"/></xsl:when>
        <xsl:when test="$grade_cell = '&#x2612;'"><xsl:value-of select="'0'"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$grade_cell"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:variable name="fraction">
        <xsl:choose>
        <xsl:when test="$qtype = 'ES'"><xsl:value-of select="'0'"/></xsl:when>
        <xsl:when test="$qtype = 'SA'"><xsl:value-of select="'100'"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$fraction_value"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>    

    <xsl:variable name="answer_format">
        <xsl:choose>
        <xsl:when test="$qtype = 'MA' or $qtype = 'MC' or $qtype = 'MS' or $qtype = 'DDI' or $qtype = 'DDT'">
            <xsl:text>html</xsl:text>
        </xsl:when>
        <xsl:when test="$qtype = 'MW' or $qtype = 'SA' or $qtype = 'TF' or $qtype = 'DDM'">
            <xsl:text></xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>moodle_auto_format</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <!--<xsl:comment>option_colnum: <xsl:value-of select="$option_colnum"/>; feedback_colnum: <xsl:value-of select="$generic_feedback_colnum"/>; grade colnum: <xsl:value-of select="$nColumns"/></xsl:comment>-->
    <!--<xsl:comment>option: <xsl:value-of select="$plain_text"/>; feedback: <xsl:value-of select="td[position() = $generic_feedback_colnum]"/>; grade: <xsl:value-of select="$fraction"/></xsl:comment>-->

    <!-- Include an answer for all questions except Description and Cloze -->
    <xsl:choose>
    <xsl:when test="$qtype = 'DE' or $qtype = 'CL'">
        <!-- Do nothing -->
    </xsl:when>
    <xsl:when test="$qtype = 'MW'">
        <selectoption>
            <text><xsl:value-of select="normalize-space($plain_text)"/></text>
            <!-- Fraction value contains the group number in MW questions -->
            <group><xsl:value-of select="$fraction_value"/></group>
        </selectoption>
    </xsl:when>
    <xsl:when test="$qtype = 'DDI'">
        <!-- Drag and Drop image or text onto image -->
        <xsl:variable name="infinite_flag_value">
            <xsl:choose>
            <xsl:when test="contains(x:td[position() = $specific_feedback_colnum], '&#x9;')">
                <xsl:value-of select="substring-after(x:td[position() = $specific_feedback_colnum], '&#x9;')"/>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="x:td[position() = $specific_feedback_colnum]"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <drag>
            <no><xsl:value-of select="position()"/></no>
            <xsl:choose>
            <xsl:when test="x:td[position() = $option_colnum]//x:img">
                <text>
                    <xsl:value-of select="x:td[position() = $option_colnum]//x:img/@longdesc"/>
                </text>
                <xsl:apply-templates select="x:td[position() = $option_colnum]" mode="moodle2pluginfile"/>
            </xsl:when>
            <xsl:otherwise>
                <text><xsl:value-of select="normalize-space($plain_text)"/></text>
            </xsl:otherwise>
            </xsl:choose>
            <!-- Fraction value contains the group number in DDT questions -->
            <draggroup><xsl:value-of select="$fraction_value"/></draggroup>
            <xsl:if test="contains(translate($infinite_flag_value, $ucase, $lcase), $yes_label)">
                <infinite/>
            </xsl:if>
        </drag>
    </xsl:when>
    <xsl:when test="$qtype = 'DDM'">
        <!-- Drag and Drop Markers onto image -->
        <drag>
            <no><xsl:value-of select="position()"/></no>
            <text><xsl:value-of select="normalize-space($plain_text)"/></text>
            <!-- Fraction value contains the allowed number of drags in DDM questions -->
            <xsl:choose>
            <xsl:when test="$fraction_value = '0'">
                <!-- If the number of drags is 0, then it can be used an infinite number of times -->
                <noofdrags>1</noofdrags>
                <infinite/>
            </xsl:when>
            <xsl:otherwise>
                <noofdrags><xsl:value-of select="$fraction_value"/></noofdrags>
            </xsl:otherwise>
            </xsl:choose>
        </drag>
    </xsl:when>
    <xsl:when test="$qtype = 'DDT'">
        <!-- Drag and Drop Word to Sentence -->
        <xsl:variable name="infinite_flag_value">
            <xsl:choose>
            <xsl:when test="contains(x:td[position() = $specific_feedback_colnum], '&#x9;')">
                <xsl:value-of select="substring-after(x:td[position() = $specific_feedback_colnum], '&#x9;')"/>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="x:td[position() = $specific_feedback_colnum]"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <dragbox>
            <text><xsl:value-of select="normalize-space($plain_text)"/></text>
            <!-- Fraction value contains the group number in DDT questions -->
            <group><xsl:value-of select="$fraction_value"/></group>
            <xsl:if test="contains(translate($infinite_flag_value, $ucase, $lcase), $yes_label)">
                <infinite/>
            </xsl:if>
        </dragbox>
    </xsl:when>
    <xsl:otherwise>
        <answer fraction="{$fraction_value}">
            <xsl:if test="$answer_format != ''">
            <xsl:attribute name="format">
                <xsl:value-of select="$answer_format"/>
            </xsl:attribute>
            </xsl:if>
            <xsl:choose>
            <xsl:when test="$qtype = 'TF'">
                <xsl:variable name="truefalse_value">
                    <xsl:choose>
                    <xsl:when test="starts-with(translate(normalize-space($plain_text), $ucase, $lcase), translate($true_label, $ucase, $lcase))">
                        <xsl:value-of select="'true'"/>
                    </xsl:when>
                    <xsl:otherwise><xsl:value-of select="'false'"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <text><xsl:value-of select="$truefalse_value"/></text>
            </xsl:when>
            <xsl:when test="$qtype = 'SA'">
                <text><xsl:value-of select="normalize-space($plain_text)"/></text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:call-template name="rich_text_content">
                    <xsl:with-param name="content" select="x:td[position() = $option_colnum]"/>
                </xsl:call-template>
            </xsl:otherwise>
            </xsl:choose>
            
            <!-- Specific feedback for the current answer -->
            <feedback>
                <xsl:if test="$moodleReleaseNumber &gt; '19'">
                    <xsl:attribute name="format"><xsl:text>html</xsl:text></xsl:attribute>
                </xsl:if>
                <xsl:call-template name="rich_text_content">
                    <xsl:with-param name="content" select="x:td[position() = $specific_feedback_colnum]"/>
                </xsl:call-template>
            </feedback>
        </answer>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Drop Zone rows for DDI and DDM -->
<xsl:template name="process_dropzone_row">
    <xsl:param name="table_row"/>
    <xsl:param name="qtype"/>

    <!-- Get 2nd column containing shape (DDM) or zone label (DDI) -->
    <xsl:variable name="plain_text">
        <xsl:choose>
        <xsl:when test="contains(x:td[position() = $option_colnum], '&#x9;')">
            <xsl:value-of select="substring-after(x:td[position() = $option_colnum], '&#x9;')"/>
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="x:td[position() = $option_colnum]"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Get 3rd column containing coordinates -->
    <xsl:variable name="coordinates" select="x:td[position() = $specific_feedback_colnum]"/>

    <!-- Get 4th column containing reference to draggable item -->
    <xsl:variable name="drag_item_ref" select="normalize-space(x:td[position() = $nColumns])"/>

    <xsl:choose>
    <xsl:when test="$qtype = 'DDI'">
        <drop>
            <text><xsl:value-of select="normalize-space($plain_text)"/></text>
            <no><xsl:value-of select="position()"/></no>
            <choice><xsl:value-of select="$drag_item_ref"/></choice>
            <xleft><xsl:value-of select="substring-before($coordinates, ',')"/></xleft>
            <ytop><xsl:value-of select="substring-after($coordinates, ', ')"/></ytop>
        </drop>
    </xsl:when>
    <xsl:when test="$qtype = 'DDM'">
        <drop>
            <no><xsl:value-of select="position()"/></no>
            <shape>
                <xsl:choose>
                <xsl:when test="translate($plain_text, $ucase, $lcase) = translate($ddm_circle_label, $ucase, $lcase)">
                    <xsl:text>circle</xsl:text>
                </xsl:when>
                <xsl:when test="translate($plain_text, $ucase, $lcase) = translate($ddm_polygon_label, $ucase, $lcase)">
                    <xsl:text>polygon</xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:text>rectangle</xsl:text>
                </xsl:otherwise>
                </xsl:choose>
            </shape>
            <coords><xsl:value-of select="$coordinates"/></coords>
            <choice><xsl:value-of select="$drag_item_ref"/></choice>
        </drop>
    </xsl:when>
    </xsl:choose>
</xsl:template>

<!-- Omit language-only span elements (e.g. <span @lang="en-ie">) to keep things tidy -->
<xsl:template match="x:span[@lang and count(@*) = 1] | x:a[starts-with(@name, 'Heading')]" priority="2">
    <xsl:apply-templates/>
</xsl:template>


<!-- Omit classes beginning with a QF style -->
<xsl:template match="@class">
    <xsl:choose>
    <xsl:when test="starts-with(., 'QF')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Body')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Normal')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Cell')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Question')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Instructions')"><!-- Omit class --></xsl:when>
    <xsl:otherwise>
        <xsl:attribute name="class">
            <xsl:value-of select="."/>
        </xsl:attribute>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>


<!-- Text: check if numbering should be removed -->
<xsl:template match="text()">
    <xsl:call-template name="convertUnicode">
        <xsl:with-param name="txt" select="."/>
    </xsl:call-template>
</xsl:template>


<!-- Copy elements as is -->
<xsl:template match="*">
    <xsl:element name="{translate(name(), $ucase, $lcase)}">
        <xsl:apply-templates select="@*"/>
        <xsl:apply-templates />
    </xsl:element>
</xsl:template>

<!-- copy attributes as is -->
<xsl:template match="@*">
    <xsl:attribute name="{translate(name(), $ucase, $lcase)}">
        <xsl:value-of select="."/>
    </xsl:attribute>
</xsl:template>


<!-- Handle text, removing text before tabs, deleting non-significant newlines between elements, etc. -->
<xsl:template name="convertUnicode">
    <xsl:param name="txt"/>
    
    <xsl:variable name="cloze_answer_sep_nl" select="concat($cloze_cloze_answer_delimiter, '&#x0a;')"/>
    <xsl:choose>
        <!-- If empty (or newline), do nothing: needed to stop newlines between block elements being turned into br elements -->
        <xsl:when test="normalize-space($txt) = ''">
        </xsl:when>
        <!-- If tab, include only the text after it -->
        <xsl:when test="contains($txt, '&#x9;')">
            <xsl:call-template name="convertUnicode">
                <xsl:with-param name="txt" select="substring-after($txt, '&#x9;')"/>
            </xsl:call-template>
        </xsl:when>
        <!-- If a | followed by newline, remove the newline -->
        <xsl:when test="contains($txt, $cloze_answer_sep_nl)">
            <xsl:value-of select="concat(substring-before($txt, $cloze_answer_sep_nl), $cloze_answer_delimiter)"/>
            
            <xsl:call-template name="convertUnicode">
                <xsl:with-param name="txt" select="substring-after($txt, $cloze_answer_sep_nl)"/>
            </xsl:call-template>
        </xsl:when>
        <!-- If a newline, insert a br element instead -->
        <xsl:when test="contains($txt, '&#x0a;')">
            <xsl:value-of select="substring-before($txt, '&#x0a;')"/>
            <br/>
            <xsl:call-template name="convertUnicode">
                <xsl:with-param name="txt" select="substring-after($txt, '&#x0a;')"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$txt"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Handle rich text content fields in a generic way -->
<xsl:template name="rich_text_content">
    <xsl:param name="content"/>

    <!-- Check if the cell contains non-blank text or an image -->
    <xsl:variable name="content_norm">
        <xsl:variable name="content_text">
            <xsl:value-of select="$content"/>
        </xsl:variable>
        <xsl:value-of select="normalize-space($content_text)"/>
    </xsl:variable>
    <xsl:variable name="contains_image" select="count($content//x:img)"/>

    <text>
        <xsl:if test="($content_norm != '' and $content_norm != '&#160;' and $content_norm != '_') or $contains_image != 0">
            <xsl:value-of select="'&lt;![CDATA['" disable-output-escaping="yes"/>
            <xsl:apply-templates select="$content/*" mode="rich_text"/>
            <xsl:value-of select="']]&gt;'" disable-output-escaping="yes"/>
        </xsl:if>
    </text>
    <!-- Handle embedded images: do nothing in Moodle 1.9, and move to file element in Moodle 2.x -->
    <xsl:if test="$moodleReleaseNumber &gt;= '20'">
        <xsl:apply-templates select="$content//x:img" mode="moodle2pluginfile"/>
    </xsl:if>
</xsl:template>

<!-- Copy elements as is -->
<xsl:template match="*" mode="rich_text">
    <xsl:element name="{translate(name(), $ucase, $lcase)}">
        <xsl:apply-templates select="@*" mode="rich_text"/>
        <!--
        <xsl:for-each select="attribute()">
            <xsl:comment><xsl:value-of select="concat(name(), '=&quot;', .)"/></xsl:comment>
        </xsl:for-each>
        -->
        <xsl:apply-templates mode="rich_text"/>
    </xsl:element>
</xsl:template>

<!-- Change em to italic-->
<xsl:template match="x:em[@class = 'italic']" mode="rich_text">
    <em>
        <xsl:apply-templates mode="rich_text"/>
    </em>
</xsl:template>

<!-- Change em/bold to strong-->
<xsl:template match="x:em[@class = 'bold']" mode="rich_text">
    <strong>
        <xsl:apply-templates mode="rich_text"/>
    </strong>
</xsl:template>
<!-- copy attributes as is -->
<xsl:template match="@*" mode="rich_text">
    <xsl:attribute name="{translate(name(), $ucase, $lcase)}">
        <xsl:value-of select="."/>
    </xsl:attribute>
</xsl:template>

<xsl:template match="@class" mode="rich_text">
    <xsl:choose>
    <xsl:when test="starts-with(., 'QF')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Body')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Normal')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Cell')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Question')"><!-- Omit class --></xsl:when>
    <xsl:when test="starts-with(., 'Instructions')"><!-- Omit class --></xsl:when>
    <xsl:otherwise>
        <xsl:attribute name="class">
            <xsl:value-of select="."/>
        </xsl:attribute>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="substring-after-last">
    <xsl:param name="text_string"/>
    <xsl:param name="delimiter_string"/>

    <xsl:choose>
    <xsl:when test="contains($text_string, $delimiter_string)">
        <!-- get everything after the first delimiter -->
        <xsl:variable name="text_remainder" select="substring-after($text_string, $delimiter_string)"/>

        <xsl:choose>
            <xsl:when test="contains($text_remainder, $delimiter_string)">
                <xsl:value-of select="$delimiter_string"/>
                <xsl:call-template name="substring-after-last">
                    <!-- store anything left in another variable -->
                    <xsl:with-param name="text_string" select="substring-after($text_string, $delimiter_string)"/>
                    <xsl:with-param name="delimiter_string" select="$delimiter_string"/>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$text_remainder"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$text_string"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>



<!-- Cloze Questions: handle special character formatting -->
<!-- Cloze question label -->
<xsl:variable name="cloze_mcorientation_label" select="'Orientation'"/>

<!-- Cloze question symbols -->
<xsl:variable name="cloze_mc_keyword1" select="':MULTICHOICE:'"/>
<xsl:variable name="cloze_mc_keyword2" select="':MC:'"/>
<xsl:variable name="cloze_mch_keyword1" select="':MCH:'"/>
<xsl:variable name="cloze_mch_keyword2" select="':MULTICHOICE_H:'"/>
<xsl:variable name="cloze_mcv_keyword1" select="':MULTICHOICE_V:'"/>
<xsl:variable name="cloze_mcv_keyword2" select="':MCV:'"/>
<xsl:variable name="cloze_sa_keyword1" select="':SHORTANSWER:'"/>
<xsl:variable name="cloze_sa_keyword2" select="':SA:'"/>
<xsl:variable name="cloze_sa_keyword3" select="':MW:'"/>
<xsl:variable name="cloze_sac_keyword1" select="':SHORTANSWER_C:'"/>
<xsl:variable name="cloze_sac_keyword2" select="':SAC:'"/>
<xsl:variable name="cloze_sac_keyword3" select="':MWC:'"/>
<xsl:variable name="cloze_num_keyword1" select="':NUMERICAL:'"/>
<xsl:variable name="cloze_num_keyword2" select="':NM:'"/>
<xsl:variable name="cloze_correct_prefix1" select="'%100%'"/>
<xsl:variable name="cloze_correct_prefix2" select="'='"/>
<xsl:variable name="cloze_incorrect_prefix" select="'%0%'"/>
<xsl:variable name="cloze_start_delimiter" select="'{'"/>
<xsl:variable name="cloze_end_delimiter" select="'}'"/>
<xsl:variable name="cloze_keyword_delimiter" select="':'"/>
<xsl:variable name="cloze_answer_delimiter" select="'~'"/>
<xsl:variable name="cloze_cloze_answer_delimiter" select="'|'"/>
<xsl:variable name="cloze_feedback_separator" select="'#'"/>
<xsl:variable name="cloze_wildcard_wrong" select="'*'"/>
<xsl:variable name="cloze_wildcard_indicator" select="concat($cloze_answer_delimiter, $cloze_incorrect_prefix, $cloze_wildcard_wrong, $cloze_feedback_separator)"/>
<xsl:variable name="cloze_percent" select="'%'"/>
<xsl:variable name="cloze_distractor_columns" select="4"/>
<xsl:variable name="cloze_distractor_colnum" select="2"/>
<xsl:variable name="cloze_distractor_feedback_colnum" select="3"/>
<xsl:variable name="cloze_distractor_grade_colnum" select="4"/>

<!-- Handle block elements in the Cloze question such as p and ul/li -->
<xsl:template match="*" mode="clozeBlock">
    <xsl:param name="qweight_string" select="'1'"/>
    <xsl:param name="cloze_sa_keyword_string"/>
    <xsl:param name="cloze_mc_keyword_string"/>
    <xsl:param name="cloze_distractor_answer_string"/>

    <xsl:call-template name="debugComment">
        <xsl:with-param name="comment_text" select="concat('clozeBlock: ', translate(name(), $ucase, $lcase))"/>
        <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
    </xsl:call-template>

    <!-- Duplicate the block element, including its attributes -->
    <xsl:element name="{translate(name(), $ucase, $lcase)}">
        <xsl:apply-templates select="@*"/>

        <xsl:choose>
        <xsl:when test="text() or x:strong or x:em or x:u or x:img or x:span or x:sub or x:sup">
            <!-- Process the inline elements -->
            <xsl:call-template name="debugComment">
                <xsl:with-param name="comment_text" select="concat('clozeBlock: ', 'inline content')"/>
                <xsl:with-param name="inline" select="'true'"/>
                <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
            </xsl:call-template>
            <xsl:call-template name="clozeInline">
                <xsl:with-param name="qweight_string" select="$qweight_string"/>
                <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
                <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
                <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <!-- Process subblock elements -->
            <xsl:call-template name="debugComment">
                <xsl:with-param name="comment_text" select="concat('clozeBlock: ', 'block content')"/>
                <xsl:with-param name="inline" select="'true'"/>
                <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
            </xsl:call-template>
            <xsl:apply-templates select="node()" mode="clozeBlock">
                <xsl:with-param name="qweight_string" select="$qweight_string"/>
                <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
                <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
                <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
            </xsl:apply-templates>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:element>
</xsl:template>

<!-- Merge adjacent elements with the same name inside Cloze text, and convert to internal Moodle format -->
<xsl:template name="clozeInline">
    <xsl:param name="qweight_string" select="'1'"/>
    <xsl:param name="cloze_sa_keyword_string"/>
    <xsl:param name="cloze_mc_keyword_string"/>
    <xsl:param name="cloze_distractor_answer_string"/>

    <xsl:call-template name="debugComment">
        <xsl:with-param name="comment_text" select="concat('clozeInline: ', substring(., 1, 30))"/>
        <xsl:with-param name="inline" select="'true'"/>
        <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
    </xsl:call-template>

    <!-- Process the inline nodes whether text or elements -->
    <xsl:for-each select="node()">
        <xsl:variable name="elname" select="local-name()"/>
        <xsl:variable name="firstElement">
            <xsl:choose>
            <xsl:when test="not(self::text())">
                <xsl:value-of select="local-name(preceding-sibling::node()[1]) != $elname"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="'false'"/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('clozeInline: elname = ', $elname, '; firstElement = ', $firstElement)"/>
            <xsl:with-param name="inline" select="'true'"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>
         <xsl:variable name="text_string">
            <xsl:value-of select="."/>
            <!-- Merge in following siblings if it has the same element name -->
            <xsl:apply-templates select="following-sibling::node()[1][local-name() = $elname]" mode="clozeMergeAdjacent"/>
         </xsl:variable>

        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('clozeInline: text_string = ', $text_string)"/>
            <xsl:with-param name="inline" select="'true'"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>

        <xsl:choose>
        <xsl:when test="self::text()">
            <!-- Simple text, so just copy it -->
            <xsl:value-of select="."/>
        </xsl:when>
        <xsl:when test="$elname = 'strong' and $firstElement">
             <!-- Bold text, so convert it to MultiChoice -->
             <xsl:call-template name="clozeMultiChoice">
                <xsl:with-param name="mctext_string" select="$text_string"/>
                <xsl:with-param name="qweight_string" select="$qweight_string"/>
                <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
                <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
                <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:when test="$elname = 'em' and $firstElement">
            <!-- Italic text, so convert it to Short Answer -->
             <xsl:call-template name="clozeShortAnswer">
                <xsl:with-param name="satext_string" select="$text_string"/>
                <xsl:with-param name="qweight_string" select="$qweight_string"/>
                <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
                <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
                <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:when test="$elname = 'u' and $firstElement">
            <!-- Underlined text, so convert it to Numerical -->
             <xsl:call-template name="clozeNumerical">
                <xsl:with-param name="numtext_string" select="$text_string"/>
                <xsl:with-param name="qweight_string" select="$qweight_string"/>
                <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
                <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
                <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
            </xsl:call-template>
        </xsl:when>
        <!-- Ignore subsequent bold/italic elements -->
        <xsl:when test="($elname = 'em' or $elname = 'strong' or $elname = 'u') and not($firstElement)"/>
        <xsl:when test="$elname = 'img'">
            <!-- Convert images to a reference to the file element -->
            <xsl:call-template name="debugComment">
                <xsl:with-param name="comment_text" select="concat('clozeInline: img element = ', $elname)"/>
                <xsl:with-param name="inline" select="'true'"/>
                <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
            </xsl:call-template>
            <xsl:apply-templates select="." mode="rich_text"/>
        </xsl:when>
        <xsl:otherwise>
            <!-- Handle any other inline markup like images, subscript, etc. -->
            <xsl:call-template name="debugComment">
                <xsl:with-param name="comment_text" select="concat('clozeInline: other element = ', $elname)"/>
                <xsl:with-param name="inline" select="'true'"/>
                <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
            </xsl:call-template>

            <xsl:apply-templates select="." mode="cloze"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:for-each>
</xsl:template>

<!-- Recursive template used to match the next sibling if it is an element with the same name -->
<xsl:template match="*" mode="clozeMergeAdjacent">
  <xsl:variable name="elname" select="local-name()"/>

  <xsl:apply-templates />
  <xsl:apply-templates select="following-sibling::node()[1][local-name() = $elname]" mode="clozeMergeAdjacent"/>
</xsl:template>

<!-- Copy Cloze elements except for Bold and Italic -->
<xsl:template match="*" mode="cloze">
    <xsl:param name="qweight_string" select="'1'"/>
    <xsl:param name="cloze_sa_keyword_string"/>
    <xsl:param name="cloze_mc_keyword_string"/>
    <xsl:param name="cloze_distractor_answer_string"/>

    <xsl:element name="{translate(name(), $ucase, $lcase)}">
        <xsl:apply-templates select="@*"/>

        <xsl:apply-templates mode="cloze">
            <xsl:with-param name="qweight_string" select="$qweight_string"/>
            <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
            <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
            <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
        </xsl:apply-templates>
    </xsl:element>
</xsl:template>

<!-- Convert bold into Moodle Cloze Multichoice format: e.g. {1:MULTICHOICE:=California#OK~Arizona#Wrong} -->
<xsl:template name="clozeMultiChoice">
    <xsl:param name="mctext_string"/>
    <xsl:param name="qweight_string" select="'1'"/>
    <xsl:param name="cloze_sa_keyword_string"/>
    <xsl:param name="cloze_mc_keyword_string"/>
    <xsl:param name="cloze_distractor_answer_string"/>

    <!-- The 3 components at the start of the string are something like: "{" "1" ":MULTICHOICE:", i.e. "{1:MULTICHOICE:" -->
    <xsl:variable name="cloze_mc_prefix" select="concat($cloze_start_delimiter, $qweight_string, $cloze_mc_keyword_string)"/>

    <!-- Process the Cloze bold item if it doesn't contain ':MC' or ':MULTICHOICE' already -->
    <xsl:variable name="correct_option" select="normalize-space(.)"/>
    <xsl:choose>
    <xsl:when test="contains($mctext_string, $cloze_mc_keyword1) or 
            contains($mctext_string, $cloze_mc_keyword2) or 
            contains($mctext_string, $cloze_mch_keyword1) or 
            contains($mctext_string, $cloze_mch_keyword2) or 
            contains($mctext_string, $cloze_mcv_keyword1) or 
            contains($mctext_string, $cloze_mcv_keyword2)">
        <!-- MC subquestion contains Moodle keywords, so no need to process it further -->
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('No Cloze processing required: ', $mctext_string)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>
        <xsl:value-of select="$mctext_string"/>
    </xsl:when>
    <xsl:when test="starts-with($mctext_string, $cloze_percent) or 
            starts-with($mctext_string, $cloze_correct_prefix2) or 
            contains($mctext_string, $cloze_answer_delimiter)">
        <!-- Text starts with grade indicator (percent or '='), so just wrap the content with the keyword prefix and suffix, omitting distractors -->
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('Minimum Cloze processing required: ', $mctext_string)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>
        <xsl:value-of select="concat($cloze_mc_prefix, $mctext_string, $cloze_end_delimiter)"/>
    </xsl:when>
    <xsl:when test="contains($mctext_string, $cloze_answer_delimiter) and 
            (not(starts-with($mctext_string, $cloze_percent)) or 
             not(starts-with($mctext_string, $cloze_correct_prefix2)))">
        <!-- Text doesn't starts with grade indicator ('%' or '='), but does contain distractors delimited by ~, so prefix the first entry with a correct indicator -->
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('Minor Cloze processing required (prefix correct answer): ', $mctext_string)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>
        <xsl:value-of select="concat($cloze_mc_prefix, $cloze_correct_prefix2, $mctext_string, $cloze_end_delimiter)"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('Full Cloze processing required: ', $mctext_string)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>
        <!-- Standard MC case, so process the correct answer, add other answers as distractors, and add generic distractors (if any) too-->

        <!-- Gather the other MC answers as distractors, grouping any multi-choice items according to the para or list they are in -->
        <xsl:variable name="other_mc_answers">
            <xsl:for-each select="ancestor::x:p//x:strong|ancestor::x:ul//x:strong|ancestor::x:ol//x:strong">
                <!-- Get the current option, which may be this MC subquestion or one of the other ones -->
                <xsl:variable name="this_mc_option" select="normalize-space(.)"/>
                <!-- If it isn't the current MC subquestion, include it as a distractor, marking it incorrect -->
                <xsl:if test="$this_mc_option != $mctext_string">
                    <xsl:value-of select="concat($cloze_answer_delimiter, $cloze_incorrect_prefix, $this_mc_option)"/>
                </xsl:if>
            </xsl:for-each>
        </xsl:variable>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('$other_mc_answers: ', $other_mc_answers)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>

        <!-- Get the common distractor list, but remove the SA wildcard distractor '*', if present -->
        <xsl:variable name="other_distractor_answers_string">
            <xsl:choose>
            <xsl:when test="contains($cloze_distractor_answer_string, $cloze_wildcard_indicator)">
                <xsl:value-of select="substring-before($cloze_distractor_answer_string, $cloze_wildcard_indicator)"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$cloze_distractor_answer_string"/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('$other_distractor_answers_string: ', $other_distractor_answers_string)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>

        <!-- Assemble the complete string, consisting of the current subquestion, other subquestions as distractors, and the generic distractors -->
        <xsl:value-of select="$cloze_mc_prefix"/>
        <!-- Format the current MC subquestion text -->
        <xsl:call-template name="format_cloze_answer">
            <xsl:with-param name="answer_string" select="$mctext_string"/>
            <xsl:with-param name="cloze_type" select="'MC'"/>
        </xsl:call-template>
        <!-- Other MC subquestions and generic distractors, plus the end-delimiter -->
        <xsl:value-of select="concat($other_mc_answers, $other_distractor_answers_string, $cloze_end_delimiter)"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Convert italic into Moodle Cloze Short Answer format: e.g. {1:SHORTANSWER:%100%Answer1#Correct~%50%Answer2#Half-right} -->
<xsl:template name="clozeShortAnswer">
    <xsl:param name="satext_string"/>
    <xsl:param name="qweight_string" select="'1'"/>
    <xsl:param name="cloze_sa_keyword_string"/>
    <xsl:param name="cloze_distractor_answer_string"/>

    <xsl:variable name="cloze_sa_prefix" select="concat($cloze_start_delimiter, $qweight_string, $cloze_sa_keyword_string)"/>

    <!-- Process the Cloze italic item if it doesn't contain ':SHORTANSWER:', ':SA:', ':SHORTANSWER_C:' or ':SAC:' already -->
    <xsl:choose>
    <xsl:when test="contains($satext_string, $cloze_sa_keyword1) or contains($satext_string, $cloze_sa_keyword2) or contains($satext_string, $cloze_sac_keyword1) or contains($satext_string, $cloze_sac_keyword2)">
        <xsl:value-of select="$satext_string"/>
    </xsl:when>
    <xsl:when test="starts-with($satext_string, $cloze_percent)">
        <!-- Text starts with percent grade, so assume it just needs to be wrapped in SHORTANSWER keyword, don't add common distractors -->
        <xsl:value-of select="concat($cloze_sa_prefix, $satext_string, $cloze_end_delimiter)"/>
    </xsl:when>
    <xsl:otherwise>
        <!-- Plain text, so output the SA keyword prefix, then the formatted answer, and finally append the common distractors -->
        <xsl:value-of select="$cloze_sa_prefix"/>
        <xsl:call-template name="split_cloze_answer">
            <xsl:with-param name="answer_string" select="$satext_string"/>
            <xsl:with-param name="first" select="'1'"/>
            <xsl:with-param name="cloze_type" select="'SA'"/>
        </xsl:call-template>
        <xsl:value-of select="concat($cloze_distractor_answer_string, $cloze_end_delimiter)"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Convert underline into Moodle Cloze Numerical format: e.g. {1:NUMERICAL:%100%3.0:0.1#feedback 1~%50%36.7:0.2#feedback 2} -->
<xsl:template name="clozeNumerical">
    <xsl:param name="numtext_string"/>
    <xsl:param name="qweight_string" select="'1'"/>
    <xsl:param name="cloze_sa_keyword_string"/>
    <xsl:param name="cloze_distractor_answer_string"/>

    <xsl:variable name="cloze_num_prefix" select="concat($cloze_start_delimiter, $qweight_string, $cloze_num_keyword1)"/>

    <!-- Process the Cloze underlined item if it doesn't contain 'NUMERICAL' already -->
    <xsl:choose>
    <xsl:when test="contains($numtext_string, $cloze_num_prefix)">
        <xsl:value-of select="$numtext_string"/>
    </xsl:when>
    <xsl:when test="contains($numtext_string, $cloze_percent)">
        <xsl:value-of select="concat($cloze_num_prefix, $numtext_string, $cloze_end_delimiter)"/>
    </xsl:when>
    <xsl:when test="contains($numtext_string, 'NUMERICAL')">
        <xsl:value-of select="$numtext_string"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$cloze_num_prefix"/>
        <xsl:call-template name="split_cloze_answer">
            <xsl:with-param name="answer_string" select="$numtext_string"/>
            <xsl:with-param name="first" select="'1'"/>
            <xsl:with-param name="cloze_type" select="'NUM'"/>
        </xsl:call-template>
        <xsl:value-of select="$cloze_end_delimiter"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Omit language-only span elements (e.g. <span @lang="en-ie">) to keep things tidy -->
<xsl:template match="x:span[@lang and count(@*) = 1] | x:a[starts-with(@name, 'Heading')]" mode="cloze">
    <xsl:param name="qweight_string" select="'1'"/>
    <xsl:param name="cloze_sa_keyword_string"/>
    <xsl:param name="cloze_mc_keyword_string"/>
    <xsl:param name="cloze_distractor_answer_string"/>

    <xsl:apply-templates mode="cloze">
        <xsl:with-param name="qweight_string" select="$qweight_string"/>
        <xsl:with-param name="cloze_sa_keyword_string" select="$cloze_sa_keyword_string"/>
        <xsl:with-param name="cloze_mc_keyword_string" select="$cloze_mc_keyword_string"/>
        <xsl:with-param name="cloze_distractor_answer_string" select="$cloze_distractor_answer_string"/>
    </xsl:apply-templates>
</xsl:template>

<!-- Handle images in Moodle 2.x to use PLUGINFILE--> 
<xsl:template match="x:img" mode="cloze">

    <xsl:apply-templates select="."/>
</xsl:template>

<!-- Handle multiple answers separated by | for Cloze questions -->
<xsl:template name="split_cloze_answer">
    <xsl:param name="answer_string"/>
    <xsl:param name="first" select="'0'"/>
    <xsl:param name="cloze_type" select="'SA'"/>
    
    <!-- If its not the first item, insert a separator before the next answer-->
    <xsl:if test="$first != '1'">
        <xsl:text>~</xsl:text>
    </xsl:if>

    <xsl:choose>
    <xsl:when test="contains($answer_string, $cloze_answer_delimiter)">
        <!-- More than one answer, so split out the first one  -->
        <xsl:variable name="first_answer" select="normalize-space(substring-before($answer_string, $cloze_answer_delimiter))"/>
        
        <xsl:call-template name="format_cloze_answer">
            <xsl:with-param name="answer_string" select="$first_answer"/>
            <xsl:with-param name="cloze_type" select="$cloze_type"/>
        </xsl:call-template>

        <!-- Recurse, passing on the remainder of the string -->
        <xsl:call-template name="split_cloze_answer">
            <xsl:with-param name="answer_string" select="normalize-space(substring-after($answer_string, $cloze_answer_delimiter))"/>
            <xsl:with-param name="cloze_type" select="$cloze_type"/>
        </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
        <!-- Just one answer (remaining) -->
        <xsl:call-template name="format_cloze_answer">
            <xsl:with-param name="answer_string" select="normalize-space($answer_string)"/>
            <xsl:with-param name="cloze_type" select="$cloze_type"/>
        </xsl:call-template>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>


<!-- Return complete answer, including percentage and/or feedback, if not supplied -->
<xsl:template name="format_cloze_answer">
    <xsl:param name="answer_string"/>
    <xsl:param name="cloze_type" select="'SA'"/>

    <!-- If no explicit grade value is set, use default -->
    <xsl:if test="not(starts-with($answer_string, $cloze_percent)) and not(starts-with($answer_string, $cloze_wildcard_wrong))">
        <xsl:value-of select="$cloze_correct_prefix1"/>
    </xsl:if>
    <!-- The answer -->
    <xsl:value-of select="$answer_string"/>
</xsl:template>


    <!-- Case sensitivity: used in Short Answer and Cloze (Short Answer subquestions) -->
    <xsl:template name="get_cloze_sa_keyword_string">
        <xsl:param name="casesensitive_value"/>

        <xsl:choose>
        <xsl:when test="$casesensitive_value = '1'">
            <xsl:value-of select="$cloze_sac_keyword1"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$cloze_sa_keyword1"/>
        </xsl:otherwise>
        </xsl:choose>
</xsl:template>

<!-- Orientation: used in Cloze Multichoice subquestions for radio button displays -->
<xsl:template name="get_cloze_mc_orientation">
    <xsl:param name="cloze_mcorientation_text"/>

  <xsl:choose>
  <xsl:when test="starts-with(translate($cloze_mcorientation_text, $lcase, $ucase), 'V')">
    <xsl:value-of select="$cloze_mcv_keyword1"/>
  </xsl:when>
  <xsl:when test="starts-with(translate($cloze_mcorientation_text, $lcase, $ucase), 'H')">
    <xsl:value-of select="$cloze_mch_keyword1"/>
  </xsl:when>
  <xsl:otherwise>
    <xsl:value-of select="$cloze_mc_keyword1"/>
  </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- Create a string containing all the separate distractor rows in the table, formatted the Moodle way -->
<xsl:template name="get_cloze_distractor_answer_string">
    <xsl:param name="table_root"/>

    <xsl:for-each select="$table_root/x:tbody/x:tr[count(x:td) = $cloze_distractor_columns]">
        <!-- Distractor row column 2 is the text, 3 is feedback, and 4 is a grade -->
        <xsl:variable name="distractor_text_raw" select="normalize-space(x:td[position() = $cloze_distractor_colnum])"/>
        <xsl:variable name="distractor_feedback_text_raw" select="normalize-space(x:td[position() = $cloze_distractor_feedback_colnum])"/>
        <xsl:variable name="distractor_grade_raw" select="normalize-space(x:td[position() = $cloze_distractor_grade_colnum])"/>
        <xsl:variable name="distractor_text">
            <xsl:if test="$distractor_text_raw != '&#160;' and $distractor_text_raw != ''">
                <xsl:value-of select="$distractor_text_raw"/>
            </xsl:if>
        </xsl:variable>
        <xsl:variable name="distractor_feedback">
            <xsl:if test="$distractor_feedback_text_raw != '&#160;' and $distractor_feedback_text_raw != ''">
                <xsl:value-of select="$distractor_feedback_text_raw"/>
            </xsl:if>
        </xsl:variable>
        <!-- Distractor row column 4 is a grade, generally blank or 0 (%) -->
        <xsl:variable name="distractor_grade">
            <xsl:choose>
            <xsl:when test="$distractor_grade_raw != '' and $distractor_grade_raw != '&#160;'">
                <xsl:value-of select="concat($cloze_percent, $distractor_grade_raw, $cloze_percent)"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$cloze_incorrect_prefix"/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        
        <!-- Compose a full item string for this answer option, containing the ~ delimiter, the grade and the distractor -->
        <xsl:if test="$distractor_text != ''">
            <xsl:value-of select="concat($cloze_answer_delimiter, $distractor_grade, $distractor_text)"/>
            <!-- Add any feedback -->
            <xsl:if test="$distractor_feedback != ''">
                <xsl:value-of select="concat($cloze_feedback_separator, $distractor_feedback)"/>
            </xsl:if>
        </xsl:if>
    </xsl:for-each>
</xsl:template>


<!-- Images -->

<!-- Handle images by replacing the @src attribute with a reference to the base64-encoded data in the file element -->
<xsl:template match="x:img" mode="rich_text">
    <xsl:variable name="image_format">
        <xsl:if test="contains(@src, $image_encoding)">
            <xsl:value-of select="substring-after(substring-before(@src, concat(';', $image_encoding)), '/')"/>
        </xsl:if>
    </xsl:variable>

    <xsl:variable name="real_image_format">
        <xsl:choose>
        <xsl:when test="$image_format != ''">
                <xsl:value-of select="$image_format"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="substring-after-last">
                <xsl:with-param name="text_string" select="@src"/>
                <xsl:with-param name="delimiter_string" select="'.'"/>
            </xsl:call-template>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:if test="$moodleReleaseNumber &gt;= '20'">
        <!-- Moodle 2 images have the data component moved to the file element -->

        <xsl:variable name="alt_text">
            <xsl:choose>
            <xsl:when test="@alt">
                <xsl:value-of select="@alt"/>
            </xsl:when>
            <xsl:when test="@longdesc">
                <xsl:value-of select="@longdesc"/>
            </xsl:when>
            <xsl:when test="@title">
                <xsl:value-of select="@title"/>
            </xsl:when>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="image_src_attr">
            <xsl:choose>
            <xsl:when test="$image_format != ''">
                <!-- Image was embedded in Word file, so embed the data the way Question XML wants it  -->
                <xsl:value-of select="concat($image_metafolder, '/', @id, '.', $real_image_format)"/>
            </xsl:when>
            <xsl:otherwise>
                <!-- Image was linked to (e.g. <img src="image.gif"...) rather than embedded in the Word file, so keep the path -->
                <xsl:value-of select="@src"/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <img src="{$image_src_attr}" alt="{$alt_text}">
        <!--
            <xsl:if test="@class">
                <xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute>
            </xsl:if>
            -->
            <xsl:if test="@title and (@alt or @longdesc)">
                <xsl:attribute name="title"><xsl:value-of select="@title"/></xsl:attribute>
            </xsl:if>
            <!-- Set the width and height if present -->
            <xsl:if test="@width">
                <xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute>
            </xsl:if>
            <xsl:if test="@height">
                <xsl:attribute name="height"><xsl:value-of select="@height"/></xsl:attribute>
            </xsl:if>
        </img>
    </xsl:if>
    
</xsl:template>


<xsl:template match="x:img" mode="moodle2pluginfile">
    <xsl:variable name="image_format" select="substring-after(substring-before(@src, ';'), '/')"/>

    <xsl:variable name="real_image_format">
        <xsl:choose>
        <xsl:when test="$image_format != ''">
                <xsl:value-of select="$image_format"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:call-template name="substring-after-last">
                <xsl:with-param name="text_string" select="@src"/>
                <xsl:with-param name="delimiter_string" select="'.'"/>
            </xsl:call-template>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <file name="{concat(@id, '.', $real_image_format)}" encoding="base64">
        <xsl:value-of select="substring-after(@src, 'base64,')"/>
    </file>
</xsl:template>

<!-- Include debugging information in the output -->
<xsl:template name="debugComment">
  <xsl:param name="comment_text"/>
  <xsl:param name="inline" select="'false'"/>
  <xsl:param name="condition" select="'true'"/>

  <xsl:if test="boolean($condition) and $debug_flag != '0'">
    <xsl:if test="$inline = 'false'"><xsl:text>&#x0a;</xsl:text></xsl:if>
    <xsl:comment><xsl:value-of select="concat('Debug: ', $comment_text)"/></xsl:comment>
    <xsl:if test="$inline = 'false'"><xsl:text>&#x0a;</xsl:text></xsl:if>
  </xsl:if>
</xsl:template>
</xsl:stylesheet>
