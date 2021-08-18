<?xml version="1.0" encoding="UTF-8"?>
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

 * XSLT stylesheet to transform Moodle Question XML-formatted questions into Word-compatible HTML tables 
 *
 * @package    qformat_wordtable
 * @copyright  2010-2015 Eoin Campbell
 * @author     Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
-->
<xsl:stylesheet exclude-result-prefixes="htm"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:htm="http://www.w3.org/1999/xhtml"
    xmlns="http://www.w3.org/1999/xhtml"
    version="1.0">

<xsl:param name="course_name"/>
<xsl:param name="course_id"/>
<xsl:param name="author_name"/>
<xsl:param name="author_id"/>
<xsl:param name="institution_name"/>
<xsl:param name="moodle_language" select="'en'"/> <!-- Interface language for user -->
<xsl:param name="moodle_release"/> <!-- 1.9 or 2.x -->
<xsl:param name="moodle_textdirection" select="'ltr'"/> <!-- ltr/rtl, ltr except for Arabic, Hebrew, Urdu, Farsi, Maldivian (who knew?) -->
<xsl:param name="moodle_username"/> <!-- Username for login -->
<xsl:param name="moodle_url"/>      <!-- Location of Moodle site -->
<xsl:param name="debug_flag" select="'0'"/>      <!-- Debugging on or off -->

<xsl:output method="xml" version="1.0" indent="yes" omit-xml-declaration="yes"/>

<!-- Text labels from translated Moodle files - now stored in the input XML file -->
<xsl:variable name="moodle_labels" select="/container/moodlelabels"/>


<xsl:variable name="ucase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />
<xsl:variable name="lcase" select="'abcdefghijklmnopqrstuvwxyz'" />
<xsl:variable name="pluginfiles_string" select="'@@PLUGINFILE@@/'"/>
<!-- Cloze question keywords to watch out for -->
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
<xsl:variable name="cloze_distractor_column_label" select="$moodle_labels/data[@name = 'qformat_wordtable_cloze_distractor_column_label']"/>
<xsl:variable name="cloze_feedback_column_label" select="$moodle_labels/data[@name = 'qformat_wordtable_cloze_feedback_column_label']"/>
<xsl:variable name="cloze_mcformat_label" select="concat($moodle_labels/data[@name = 'qformat_wordtable_cloze_mcformat_label'], $colon_string)"/>

<!-- Moodle release is significant for the format of different questions
    Essay:
        1.9   - no grader info or response template
        2.1-4 - grader info and attachments, but no response template
        2.5+  - grader info, attachments and response template
        2.7+  - number of required attachments field
        2.9+  - response text required/optional field

    Cloze:
        2.1-3 - no per-hint options
        2.4+  - per hint options, i.e. clear wrong responses, show number of correct responses

    Hints and Tags:
        1.9  - no hints or tags
        2.1+ - hints and tags
-->
<!-- Convert Moodle release numbers such as "2.4" to "24" for easier numerical comparisons, to decide what question heading rows to include -->
<xsl:variable name="moodle_release_number" select="translate(substring($moodle_release, 1, 3), '.', '')"/>

<!-- Handle colon usage in French -->
<xsl:variable name="colon_string">
    <xsl:choose>
    <xsl:when test="starts-with($moodle_language, 'fr')"><xsl:text> :</xsl:text></xsl:when>
    <xsl:otherwise><xsl:text>:</xsl:text></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="blank_cell" select="'&#160;'"/>

<!-- Create the list of labels from text strings in Moodle, to maximise familiarity of Word file labels -->
<xsl:variable name="answer_label" select="$moodle_labels/data[@name = 'quiz_answer']"/>
<xsl:variable name="answers_label" select="$moodle_labels/data[@name = 'quiz_answers']"/>
<xsl:variable name="categoryname_label" select="$moodle_labels/data[@name = 'moodle_categoryname']"/>
<xsl:variable name="defaultmark_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="concat($moodle_labels/data[@name = 'quiz_defaultgrade'], $colon_string)"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="concat($moodle_labels/data[@name = 'question_defaultmark'], $colon_string)"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="grade_label" select="$moodle_labels/data[@name = 'moodle_grade']"/>
<xsl:variable name="no_label" select="$moodle_labels/data[@name = 'moodle_no']"/>
<xsl:variable name="yes_label" select="$moodle_labels/data[@name = 'moodle_yes']"/>
<xsl:variable name="item_label" select="$moodle_labels/data[@name = 'grades_item']"/>
<xsl:variable name="penalty_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="concat($moodle_labels/data[@name = 'quiz_penaltyfactor'], $colon_string)"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="concat($moodle_labels/data[@name = 'question_penaltyforeachincorrecttry'], $colon_string)"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="question_label" select="$moodle_labels/data[@name = 'moodle_question']"/>
<xsl:variable name="category_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="$moodle_labels/data[@name = 'question_questioncategory']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodle_labels/data[@name = 'question_category']"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="tags_label" select="concat($moodle_labels/data[@name = 'moodle_tags'], $colon_string)"/>
<xsl:variable name="idnumber_label" select="concat($moodle_labels/data[@name = 'question_idnumber'], $colon_string)"/>

<xsl:variable name="matching_shuffle_label" select="concat($moodle_labels/data[@name = 'quiz_shuffle'], $colon_string)"/>
<xsl:variable name="mcq_shuffleanswers_label" select="$moodle_labels/data[@name = 'qtype_multichoice_shuffleanswers']"/>
<xsl:variable name="gapselect_shuffle_label" select="concat($moodle_labels/data[@name = 'qtype_gapselect_shuffle'], $colon_string)"/>
<xsl:variable name="answernumbering_label" select="$moodle_labels/data[@name = 'qtype_multichoice_answernumbering']"/>

<!-- Per-question feedback labels -->
<xsl:variable name="correctfeedback_label" select="concat($moodle_labels/data[@name = 'qtype_multichoice_correctfeedback'], $colon_string)"/>
<xsl:variable name="feedback_label" select="$moodle_labels/data[@name = 'moodle_feedback']"/>
<xsl:variable name="generalfeedback_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="concat($moodle_labels/data[@name = 'quiz_generalfeedback'], $colon_string)"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="concat($moodle_labels/data[@name = 'question_generalfeedback'], $colon_string)"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<xsl:variable name="incorrectfeedback_label" select="concat($moodle_labels/data[@name = 'qtype_multichoice_incorrectfeedback'], $colon_string)"/>
<xsl:variable name="pcorrectfeedback_label" select="concat($moodle_labels/data[@name = 'qtype_multichoice_partiallycorrectfeedback'], $colon_string)"/>
<xsl:variable name="shownumcorrectfeedback_label" select="concat($moodle_labels/data[@name = 'question_shownumpartscorrectwhenfinished'], $colon_string)"/>

<!-- Default feedback text (2.5+ only) -->
<xsl:variable name="correctfeedback_default">
    <xsl:choose>
    <xsl:when test="$moodle_release_number &gt;= '25'">
        <xsl:value-of select="$moodle_labels/data[@name = 'question_correctfeedbackdefault']"/>
    </xsl:when>
    <xsl:when test="starts-with($moodle_language, 'en')"><xsl:value-of select="'Your answer is correct'"/></xsl:when>
    <xsl:when test="starts-with($moodle_language, 'es')"><xsl:value-of select="'Respuesta correcta'"/></xsl:when>
    <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="incorrectfeedback_default">
    <xsl:choose>
    <xsl:when test="$moodle_release_number &gt;= '25'">
        <xsl:value-of select="$moodle_labels/data[@name = 'question_incorrectfeedbackdefault']"/>
    </xsl:when>
    <xsl:when test="starts-with($moodle_language, 'en')"><xsl:value-of select="'Your answer is incorrect'"/></xsl:when>
    <xsl:when test="starts-with($moodle_language, 'es')"><xsl:value-of select="'Respuesta incorrecta.'"/></xsl:when>
    <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="pcorrectfeedback_default">
    <xsl:choose>
    <xsl:when test="$moodle_release_number &gt;= '25'">
        <xsl:value-of select="$moodle_labels/data[@name = 'question_partiallycorrectfeedbackdefault']"/>
    </xsl:when>
    <xsl:when test="starts-with($moodle_language, 'en')"><xsl:value-of select="'Your answer is partially correct'"/></xsl:when>
    <xsl:when test="starts-with($moodle_language, 'es')"><xsl:value-of select="'Respuesta parcialmente correcta.'"/></xsl:when>
    <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- Hint labels; don't add a colon yet, because we'll suffix a specific hint number when printing -->
<xsl:variable name="hintn_label" select="$moodle_labels/data[@name = 'question_hintn']"/>
<xsl:variable name="hint_shownumpartscorrect_label" select="$moodle_labels/data[@name = 'question_shownumpartscorrect']"/>
<xsl:variable name="hint_clearwrongparts_label" select="$moodle_labels/data[@name = 'question_clearwrongparts']"/>

<!-- Description labels -->
<xsl:variable name="description_instructions">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="$moodle_labels/data[@name = 'qformat_wordtable_description_instructions']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodle_labels/data[@name = 'qtype_description_pluginnamesummary']"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- Essay question labels -->
<xsl:variable name="acceptedfiletypes_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number &gt;= '35'">
        <xsl:value-of select="concat($moodle_labels/data[@name = 'qtype_essay_acceptedfiletypes'], $colon_string)"/>
    </xsl:when>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="allowattachments_label" select="concat($moodle_labels/data[@name = 'qtype_essay_allowattachments'], $colon_string)"/>
<xsl:variable name="attachmentsoptional_label" select="concat($moodle_labels/data[@name = 'qtype_essay_attachmentsoptional'], $colon_string)"/>
<xsl:variable name="attachmentsrequired_label" select="concat($moodle_labels/data[@name = 'qtype_essay_attachmentsrequired'], $colon_string)"/>
<xsl:variable name="graderinfo_label" select="$moodle_labels/data[@name = 'qtype_essay_graderinfo']"/>
<xsl:variable name="responsetemplate_label" select="$moodle_labels/data[@name = 'qtype_essay_responsetemplate']"/>
<xsl:variable name="responsetemplate_help_label" select="$moodle_labels/data[@name = 'qtype_essay_responsetemplate_help']"/>
<xsl:variable name="responsefieldlines_label" select="concat($moodle_labels/data[@name = 'qtype_essay_responsefieldlines'], $colon_string)"/>
<xsl:variable name="responseformat_label" select="concat($moodle_labels/data[@name = 'qtype_essay_responseformat'], $colon_string)"/>
<xsl:variable name="responserequired_label" select="concat($moodle_labels/data[@name = 'qtype_essay_responserequired'], $colon_string)"/>
<xsl:variable name="format_html_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="$moodle_labels/data[@name = 'moodle_formathtml']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodle_labels/data[@name = 'qtype_essay_formateditor']"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="format_plain_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="$moodle_labels/data[@name = 'moodle_formatplain']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodle_labels/data[@name = 'qtype_essay_formatplain']"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="format_noinline_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number &gt;= '27'">
        <xsl:value-of select="$moodle_labels/data[@name = 'qtype_essay_formatnoinline']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodle_labels/data[@name = 'qtype_essay_formatplain']"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<!-- Moodle 2.x only -->
<xsl:variable name="format_editorfilepicker_label" select="$moodle_labels/data[@name = 'qtype_essay_formateditorfilepicker']"/>
<xsl:variable name="format_mono_label" select="$moodle_labels/data[@name = 'qtype_essay_formatmonospaced']"/>
<!-- Moodle 1.9 only -->
<xsl:variable name="format_auto_label" select="$moodle_labels/data[@name = 'moodle_formattext']"/>
<xsl:variable name="format_markdown_label" select="$moodle_labels/data[@name = 'moodle_formatmarkdown']"/>
<xsl:variable name="essay_instructions">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="$moodle_labels/data[@name = 'qformat_wordtable_essay_instructions']"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$moodle_labels/data[@name = 'qtype_essay_pluginnamesummary']"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- Matching question labels -->
<xsl:variable name="matching_instructions" select="$moodle_labels/data[@name = 'qtype_match_filloutthreeqsandtwoas']"/>

<!-- Multichoice/Multi-Answer question labels -->
<xsl:variable name="choice_label">
    <xsl:variable name="choice_text" select="$moodle_labels/data[@name = 'qtype_multichoice_choiceno']"/>
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="$moodle_labels/data[@name = 'quiz_choice']"/>
    </xsl:when>
    <xsl:when test="contains($choice_text, '{')">
        <xsl:value-of select="normalize-space(substring-before($choice_text, '{'))"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$choice_text"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="multichoice_instructions">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="concat($moodle_labels/data[@name = 'qformat_wordtable_multichoice_instructions'], ' (MC/MA)')"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="concat($moodle_labels/data[@name = 'qtype_multichoice_pluginnamesummary'], ' (MC/MA)')"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- Multichoice Set (All-or-Nothing Multichoice) question labels -->
<xsl:variable name="multichoiceset_showeachfeedback_label" select="$moodle_labels/data[@name = 'qtype_multichoiceset_showeachanswerfeedback']"/>
<xsl:variable name="multichoiceset_instructions" select="$moodle_labels/data[@name = 'qtype_multichoiceset_pluginnamesummary']"/>

<!-- Gapselect (Select missing word) question labels -->
<xsl:variable name="missingword_instructions" select="$moodle_labels/data[@name = 'qtype_gapselect_errornoslots']"/>
<xsl:variable name="group_label" select="$moodle_labels/data[@name = 'qtype_gapselect_group']"/>

<!-- Short Answer question labels -->
<xsl:variable name="casesensitive_label">
    <xsl:choose>
    <xsl:when test="$moodle_release_number = '19'">
        <xsl:value-of select="concat($moodle_labels/data[@name = 'quiz_casesensitive'], $colon_string)"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="concat($moodle_labels/data[@name = 'qtype_shortanswer_casesensitive'], $colon_string)"/></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="shortanswer_instructions" select="$moodle_labels/data[@name = 'qtype_shortanswer_filloutoneanswer']"/>

<!-- True/False question labels -->
<xsl:variable name="false_label" select="$moodle_labels/data[@name = 'qtype_truefalse_false']"/>
<xsl:variable name="true_label" select="$moodle_labels/data[@name = 'qtype_truefalse_true']"/>

<!-- Drag and Drop question labels -->
<xsl:variable name="ddi_shuffleimages_label" select="$moodle_labels/data[@name = 'qtype_ddimageortext_shuffleimages']"/>
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
<xsl:variable name="ddm_noofdrags_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_noofdrags']"/>
<xsl:variable name="ddm_polygon_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shape_polygon']"/>
<xsl:variable name="ddm_rectangle_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shape_rectangle']"/>
<xsl:variable name="ddm_shape_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_shape']"/>
<xsl:variable name="ddm_showmisplaced_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_showmisplaced']"/>
<xsl:variable name="ddm_stateincorrectlyplaced_label" select="$moodle_labels/data[@name = 'qtype_ddmarker_stateincorrectlyplaced']"/>

<xsl:variable name="ddt_infinite_label" select="$moodle_labels/data[@name = 'qtype_ddwtos_infinite']"/>
<xsl:variable name="ddt_instructions" select="$moodle_labels/data[@name = 'qtype_ddwtos_pluginnamesummary']"/>

<!-- Wordtable-specific instruction strings -->
<xsl:variable name="cloze_instructions" select="$moodle_labels/data[@name = 'qformat_wordtable_cloze_instructions']"/>
<xsl:variable name="truefalse_instructions" select="$moodle_labels/data[@name = 'qformat_wordtable_truefalse_instructions']"/>
<xsl:variable name="unsupported_instructions" select="$moodle_labels/data[@name = 'qformat_wordtable_unsupported_instructions']"/>


<!-- Column widths -->
<xsl:variable name="col2_width" select="'width: 5.0cm'"/>
<xsl:variable name="col2_2span_width" select="'width: 6.0cm'"/>
<xsl:variable name="col3_width" select="'width: 6.0cm'"/>
<xsl:variable name="col3_2span_width" select="'width: 7.0cm'"/>

<!-- Match document root node, and read in and process Word-compatible XHTML template -->
<xsl:template match="/container/quiz">
    <html>
        <xsl:variable name="category">
            <xsl:variable name="raw_category" select="normalize-space(./question[1]/category)"/>
        
            <xsl:choose>
            <xsl:when test="contains($raw_category, '$course$/')">
                <xsl:value-of select="substring-after($raw_category, '$course$/')"/>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="$raw_category"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
            
        <head>
            <title><xsl:value-of select="concat($course_name, ', ', $category_label, $colon_string, ' ', $category)"/></title>
        </head>
        <body>
            <!--<xsl:comment><xsl:value-of select="concat('Release: ', $moodle_release, '; rel_number: ', $moodle_release_number)"/></xsl:comment>-->
            <p class="MsoTitle"><xsl:value-of select="$course_name"/></p>
            <xsl:apply-templates select="./question"/>
        </body>
    </html>
</xsl:template>

<!-- Throw away extra wrapper elements included in container XML -->
<xsl:template match="/container/moodlelabels"/>

<!-- Omit any Numerical, Random or Calculated questions because we don't want to attempt to import them later 
<xsl:template match="question[@type = 'numerical']"/>
<xsl:template match="question[starts-with(@type, 'calc')]"/>
<xsl:template match="question[starts-with(@type, 'random')]"/>
-->
<!-- Category becomes a Heading 1 style -->
<!-- There can be lots of categories, but they can also be duplicated -->
<xsl:template match="question[@type = 'category']">
    <xsl:variable name="category">
        <xsl:variable name="raw_category" select="normalize-space(category)"/>
    
        <xsl:choose>
        <xsl:when test="contains($raw_category, '$course$/')">
            <xsl:value-of select="substring-after($raw_category, '$course$/')"/>
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="$raw_category"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <h1 class="MsoHeading1"><xsl:value-of select="$category"/></h1>
</xsl:template>

<!-- Handle the questions -->
<xsl:template match="question">
    <xsl:variable name="qtype">
        <xsl:choose>
        <xsl:when test="@type = 'calculated'"><xsl:text>CA</xsl:text></xsl:when>
        <xsl:when test="@type = 'calculatedmulti'"><xsl:text>CM</xsl:text></xsl:when>
        <xsl:when test="@type = 'calculatedsimple'"><xsl:text>CS</xsl:text></xsl:when>
        <xsl:when test="@type = 'cloze'"><xsl:text>CL</xsl:text></xsl:when>
        <xsl:when test="@type = 'description'"><xsl:text>DE</xsl:text></xsl:when>
        <xsl:when test="@type = 'essay'"><xsl:text>ES</xsl:text></xsl:when>
        <xsl:when test="@type = 'gapselect'"><xsl:text>MW</xsl:text></xsl:when>
        <xsl:when test="@type = 'matching'"><xsl:text>MAT</xsl:text></xsl:when>
        <xsl:when test="@type = 'multichoice' and single = 'false'"><xsl:text>MA</xsl:text></xsl:when>
        <xsl:when test="@type = 'multichoice' and single = 'true'"><xsl:text>MC</xsl:text></xsl:when>
        <xsl:when test="@type = 'multichoiceset'"><xsl:text>MS</xsl:text></xsl:when>
        <xsl:when test="@type = 'numerical'"><xsl:text>NUM</xsl:text></xsl:when>
        <xsl:when test="@type = 'shortanswer'"><xsl:text>SA</xsl:text></xsl:when>
        <xsl:when test="@type = 'truefalse'"><xsl:text>TF</xsl:text></xsl:when>
        <xsl:when test="@type = 'ddimageortext'"><xsl:text>DDI</xsl:text></xsl:when>
        <xsl:when test="@type = 'ddmarker'"><xsl:text>DDM</xsl:text></xsl:when>
        <xsl:when test="@type = 'ddwtos'"><xsl:text>DDT</xsl:text></xsl:when>
        <xsl:when test="@type = 'truefalse'"><xsl:text>TF</xsl:text></xsl:when>
        <xsl:otherwise><xsl:value-of select="@type"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>


    <!-- Figure out the metadata to be included in table heading rows -->
    <!-- Get Cloze text string if it's a Cloze question containing subquestions -->
    <xsl:variable name="cloze_questiontext_string">
        <xsl:if test="$qtype = 'CL'">
            <xsl:apply-templates select="questiontext/text"/>
        </xsl:if>
    </xsl:variable>
    <!-- Get the default mark from defaultgrade or from Cloze subquestions. If it isn't the same for all Cloze subquestions, set it to 0 -->
    <xsl:variable name="defaultmark_value">
        <xsl:choose>
        <xsl:when test="defaultgrade">
            <xsl:value-of select="number(defaultgrade)"/>
        </xsl:when>
        <xsl:when test="$qtype = 'CL' and contains($cloze_questiontext_string, $cloze_start_delimiter)">
            <xsl:call-template name="get_cloze_defaultmark">
                <xsl:with-param name="cloze_questiontext_string" select="$cloze_questiontext_string"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise><xsl:text>1</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="numbering_flag">
        <xsl:choose>
        <xsl:when test="answernumbering = 'none'">0</xsl:when>
        <xsl:when test="answernumbering"><xsl:value-of select="substring(answernumbering, 1, 1)"/></xsl:when>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="shuffleanswers_flag">
        <xsl:choose>
        <!-- shuffleanswers element might be duplicated in XML, or contain either 'true' or '1', so allow for these possibilities -->
        <xsl:when test="shuffleanswers[1] = 'true' or shuffleanswers[1] = '1'">
            <xsl:value-of select="$yes_label"/> <!-- Explicit true used in MC -->
        </xsl:when>
        <xsl:when test="shuffleanswers[1] = 'false' or shuffleanswers[1] = '0'">
            <xsl:value-of select="$no_label"/> <!-- Explicit false used in MAT -->
        </xsl:when>
        <xsl:when test="shuffleanswers"> <!-- Empty element used in DDM -->
            <xsl:value-of select="$yes_label"/>
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="idnumber_value" select="idnumber"/>

    <xsl:variable name="showmisplaced_flag">
        <xsl:choose>
        <!-- shuffleanswers element might be duplicated in XML, or contain either 'true' or '1', so allow for these possibilities -->
        <xsl:when test="showmisplaced">
            <xsl:value-of select="$yes_label"/> <!-- Explicit true used in MC -->
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Simplify the penalty value to keep it short, to fit it in the 4th column -->
    <xsl:variable name="penalty_value">
        <xsl:choose>
        <xsl:when test="starts-with(penalty, '1')">100</xsl:when>
        <xsl:when test="starts-with(penalty, '0.5')">50</xsl:when>
        <xsl:when test="starts-with(penalty, '0.3333333')">33.3</xsl:when>
        <xsl:when test="starts-with(penalty, '0.25')">25</xsl:when>
        <xsl:when test="starts-with(penalty, '0.2')">20</xsl:when>
        <xsl:when test="starts-with(penalty, '0.1')">10</xsl:when>
        <xsl:otherwise>0</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Column heading 1 is blank if question is not numbered, otherwise it includes a #, and importantly, a list number reset style -->
    <xsl:variable name="colheading1_label">
        <xsl:choose>
        <xsl:when test="$qtype = 'MA' or $qtype = 'MC' or $qtype = 'MAT' or $qtype = 'MS' or $qtype = 'MW' or $qtype = 'NUM' or starts-with($qtype, 'C')"><xsl:text>#</xsl:text></xsl:when>
        <xsl:when test="$qtype = 'DDI' or $qtype = 'DDM' or $qtype = 'DDT'"><xsl:text>#</xsl:text></xsl:when>
        <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:variable name="colheading1_style">
        <xsl:choose>
        <xsl:when test="$qtype = 'MA' or $qtype = 'MC' or $qtype = 'MS' or $qtype = 'NUM' or starts-with($qtype, 'C')"><xsl:value-of select="'QFOptionReset'"/></xsl:when>
        <xsl:when test="$qtype = 'MAT' or $qtype = 'MW'"><xsl:value-of select="'ListNumberReset'"/></xsl:when>
        <xsl:when test="starts-with($qtype, 'DD')"><xsl:value-of select="'ListNumberReset'"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="'Cell'"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Answer/Option column heading for most question types, distractors for Cloze, and the response template for Essays (2.5 and above) -->
    <xsl:variable name="colheading2_label">
        <xsl:choose>
        <xsl:when test="$qtype = 'CL'"><xsl:value-of select="$cloze_distractor_column_label"/></xsl:when>
        <xsl:when test="$qtype = 'DE'"><xsl:value-of select="$blank_cell"/></xsl:when>
        <xsl:when test="$qtype = 'ES' and $moodle_release_number &gt;= '25'"><xsl:value-of select="$responsetemplate_label"/></xsl:when>
        <xsl:when test="$qtype = 'ES'"><xsl:value-of select="$blank_cell"/></xsl:when>
        <xsl:when test="$qtype = 'MAT'"><xsl:value-of select="$question_label"/></xsl:when>
        <xsl:when test="$qtype = 'DDI'"><xsl:value-of select="$ddi_draggableitem_label"/></xsl:when>
        <xsl:when test="$qtype = 'DDM'"><xsl:value-of select="$ddm_marker_label"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$answers_label"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Option feedback and general feedback column heading -->
    <xsl:variable name="colheading3_label">
        <xsl:choose>
        <xsl:when test="$qtype = 'CL'"><xsl:value-of select="$cloze_feedback_column_label"/></xsl:when>
        <xsl:when test="$qtype = 'DE'"><xsl:value-of select="$blank_cell"/></xsl:when>
        <xsl:when test="$qtype = 'ES' and $moodle_release_number = '19'"><xsl:value-of select="$blank_cell"/></xsl:when>
        <xsl:when test="$qtype = 'ES'"><xsl:value-of select="$graderinfo_label"/></xsl:when>
        <xsl:when test="$qtype = 'MAT'"><xsl:value-of select="$answer_label"/></xsl:when>
        <xsl:when test="$qtype = 'CL' or $qtype = 'MA' or $qtype = 'MC' or $qtype = 'SA' or $qtype = 'TF' or $qtype = 'MS' or $qtype = 'NUM' or starts-with($qtype, 'C')">
            <xsl:value-of select="$feedback_label"/>
        </xsl:when>
        <xsl:when test="$qtype = 'DDI'"><xsl:value-of select="$ddi_infinite_label"/></xsl:when>
        <xsl:when test="$qtype = 'DDT'"><xsl:value-of select="$ddt_infinite_label"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Grade column heading, or blank if no grade (CL, DE, ES, MAT) -->
    <xsl:variable name="colheading4_label">
        <xsl:choose>
        <xsl:when test="$qtype = 'MW'"><xsl:value-of select="$group_label"/></xsl:when>
        <xsl:when test="$qtype = 'CL' or $qtype = 'MA' or $qtype = 'MC' or $qtype = 'MS' or $qtype = 'SA' or $qtype = 'TF' or $qtype = 'NUM'">
            <xsl:value-of select="$grade_label"/>
        </xsl:when>
        <xsl:when test="$qtype = 'DDI'"><xsl:value-of select="$group_label"/></xsl:when>
        <xsl:when test="$qtype = 'DDM'"><xsl:value-of select="$ddm_noofdrags_label"/></xsl:when>
        <xsl:when test="$qtype = 'DDT'"><xsl:value-of select="$group_label"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Configure Cloze flags to define whether mixed formatting is used in MC and SA subquestions -->
    <!-- Does :MULTICHOICE: or :MC: occur in a Cloze question? -->
    <xsl:variable name="cloze_mc_formatting_dropdown">
        <xsl:choose>
        <xsl:when test="contains($cloze_questiontext_string, $cloze_mc_keyword1) or contains($cloze_questiontext_string, $cloze_mc_keyword2)">
            <xsl:text>D</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <!-- Does :MULTICHOICE_H: or :MCH: occur in a Cloze question? -->
    <xsl:variable name="cloze_mc_formatting_radio_horizontal">
        <xsl:choose>
        <xsl:when test="contains($cloze_questiontext_string, $cloze_mch_keyword1) or contains($cloze_questiontext_string, $cloze_mch_keyword2)">
            <xsl:text>1</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <!-- Does :MULTICHOICE_V: or :MCV: occur in a Cloze question? -->
    <xsl:variable name="cloze_mc_formatting_radio_vertical">
        <xsl:choose>
        <xsl:when test="contains($cloze_questiontext_string, $cloze_mcv_keyword1) or contains($cloze_questiontext_string, $cloze_mcv_keyword2)">
            <xsl:text>1</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <!-- Does :SHORTANSWER:, :MW: or :SA: occur in a Cloze question? -->
    <xsl:variable name="cloze_sa_formatting_case_insensitive">
        <xsl:choose>
        <xsl:when test="contains($cloze_questiontext_string, $cloze_sa_keyword1) or contains($cloze_questiontext_string, $cloze_sa_keyword2) or contains($cloze_questiontext_string, $cloze_sa_keyword3)">
            <xsl:text>1</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <!-- Does :SHORTANSWER_C:, :MWC: or :SAC: occur in a Cloze question? -->
    <xsl:variable name="cloze_sa_formatting_case_sensitive">
        <xsl:choose>
        <xsl:when test="contains($cloze_questiontext_string, $cloze_sac_keyword1) or contains($cloze_questiontext_string, $cloze_sac_keyword2) or contains($cloze_questiontext_string, $cloze_sac_keyword3)">
            <xsl:text>1</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Now set flags to define the default values for CL/SA case and CL/MC style row indicators -->
    <xsl:variable name="cloze_sa_casesensitive_flag">
        <xsl:choose>
        <xsl:when test="$cloze_sa_formatting_case_sensitive = '1' and $cloze_sa_formatting_case_insensitive = '0'"><xsl:text>1</xsl:text></xsl:when>
        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:variable name="cloze_mc_formatting_flag">
        <xsl:choose>
        <xsl:when test="$cloze_mc_formatting_dropdown = '0' and $cloze_mc_formatting_radio_horizontal = '0' and $cloze_mc_formatting_radio_vertical = '1'"><xsl:text>V</xsl:text></xsl:when>
        <xsl:when test="$cloze_mc_formatting_dropdown = '0' and $cloze_mc_formatting_radio_horizontal = '1' and $cloze_mc_formatting_radio_vertical = '0'"><xsl:text>H</xsl:text></xsl:when>
        <xsl:otherwise><xsl:text>D</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Instruction text for each question type -->
    <xsl:variable name="instruction_text">
        <xsl:choose>
        <xsl:when test="$qtype = 'DDI'"><xsl:value-of select="$ddi_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'DDM'"><xsl:value-of select="$ddm_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'DDT'"><xsl:value-of select="$ddt_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'DE'"><xsl:value-of select="$description_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'ES'"><xsl:value-of select="$essay_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'MA'"><xsl:value-of select="$multichoice_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'MAT'"><xsl:value-of select="$matching_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'MC'"><xsl:value-of select="$multichoice_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'MS'"><xsl:value-of select="$multichoiceset_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'MW'"><xsl:value-of select="$missingword_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'SA'"><xsl:value-of select="$shortanswer_instructions"/></xsl:when>
        <xsl:when test="$qtype = 'TF'"><xsl:value-of select="$truefalse_instructions"/></xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$unsupported_instructions"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>


    <!-- Start generating the HTML output, now that we've finished figuring out all the values -->
    <!-- Put the question name in a heading, so it can be easily viewed in the Word navigation window -->
    <xsl:variable name="qheading">
        <xsl:apply-templates select="name/text"/>
    </xsl:variable>
    <h2 class="MsoHeading2"><xsl:value-of select="normalize-space($qheading)"/></h2>
    <p class="MsoBodyText"> </p>
    
    <!-- Generate the table containing the question stem and the answers -->
    <div class="TableDiv">
    <table border="1" dir="{$moodle_textdirection}">
    <thead>
        <!-- 1st heading row: question stem/description and type cells -->
        <xsl:text>&#x0a;</xsl:text>
        <tr>
            <td colspan="3" style="width: 12.0cm">
                <xsl:choose>
                <xsl:when test="$qtype = 'CL'">
                    <!-- Put Cloze text into the first option table cell, and convert subquestion markup too-->
                    <xsl:apply-templates select="questiontext/text" mode="cloze">
                        <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
                        <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
                        <xsl:with-param name="cloze_defaultmark_value" select="$defaultmark_value"/>
                    </xsl:apply-templates>
                    <xsl:apply-templates select="questiontext/file"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates select="questiontext/*"/>
                </xsl:otherwise>
                </xsl:choose>

                <!-- Handle supplementary image for question text, as implemented in Moodle 1.9 -->
                <xsl:if test="image and image != ''">
                    <xsl:variable name="image_file_suffix">
                        <xsl:value-of select="translate(substring-after(image, '.'), $ucase, $lcase)"/>
                    </xsl:variable>
                    <xsl:variable name="image_format">
                        <xsl:value-of select="concat('data:image/', $image_file_suffix, ';base64,')"/>
                    </xsl:variable>
                    <xsl:variable name="image_id">
                        <xsl:value-of select="'Q'"/>
                        <!-- Count the number of questions -->
                        <xsl:number value="position()" format="0001"/>
                        <xsl:value-of select="'_IID0001'"/>
                    </xsl:variable>
                    <p><img id="{$image_id}" src="{concat($pluginfiles_string, image)}"/></p>

                    <!-- Emit the image in the supplementary format, to be removed later -->
                    <div class="ImageFile"><img id="{$image_id}" title="{image}" src="{concat($image_format, normalize-space(image_base64))}"/></div>
                </xsl:if>
            </td>
            <td style="width: 1.0cm"><p class="QFType"><xsl:value-of select="$qtype" /></p></td>
        </tr>
        <xsl:text>&#x0a;</xsl:text>

        <!-- Handle background image for DDI and DDM questions -->
        <xsl:if test="$qtype = 'DDI' or $qtype = 'DDM'">
            <xsl:variable name="image_id">
                <xsl:value-of select="'Q'"/>
                <xsl:number value="position()" format="0001"/>
                <xsl:value-of select="'_IID0000'"/>
            </xsl:variable>
            <tr>
                <td colspan="4" style="width: 12.0cm">
                    <p class="Cell">
                        <img id="{$image_id}" src="{concat($pluginfiles_string,file/@name)}"/>
                    </p>
                    <xsl:apply-templates select="file">
                        <xsl:with-param name="image_id" select="$image_id"/>
                    </xsl:apply-templates>
                </td>
                <!--<td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>-->
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Handle heading rows for various metadata specific to each question -->
        <!-- 2nd heading row: Default mark / Default grade / Question weighting, i.e. total marks available for question -->
        <xsl:if test="$qtype != 'DE'">
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$defaultmark_label"/></p></td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$defaultmark_value"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>
        <!-- Shuffle the choices? -->
        <xsl:if test="$qtype = 'MAT'">
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$matching_shuffle_label"/></p></td>
                    <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$shuffleanswers_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>
        <xsl:if test="$qtype = 'MA' or $qtype = 'MC' or $qtype = 'MS'">
            <tr>
                <td colspan="3" style="width: 12.0cm">
                    <p class="TableRowHead" style="text-align: right">
                        <xsl:value-of select="$mcq_shuffleanswers_label"/>
                    </p>
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$shuffleanswers_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>
        <xsl:if test="$qtype = 'MW'">
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$gapselect_shuffle_label"/></p></td>
                    <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$shuffleanswers_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>
        <xsl:if test="$qtype = 'DDI' or $qtype = 'DDM'">
            <tr>
                <td colspan="3" style="width: 12.0cm">
                    <p class="TableRowHead" style="text-align: right">
                        <xsl:value-of select="$ddi_shuffleimages_label"/>
                    </p>
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$shuffleanswers_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>
        <xsl:if test="$qtype = 'DDM'">
            <tr>
                <td colspan="3" style="width: 12.0cm">
                    <p class="TableRowHead" style="text-align: right">
                        <xsl:value-of select="$ddm_showmisplaced_label"/>
                    </p>
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$showmisplaced_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Number the choices, and if so, how? May be alphabetic, numeric or roman -->
        <xsl:if test="$qtype = 'MC' or $qtype = 'MA' or $qtype = 'MS'">
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$answernumbering_label"/></p></td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$numbering_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Essay questions in Moodle 2.x have 3 specific fields, for Response field format, Attachments, and Number of lines -->
        <xsl:if test="$qtype = 'ES' and $moodle_release_number &gt;= '20'">
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$responseformat_label"/></p></td>
                <td style="width: 1.0cm">
                    <p class="Cell">
                        <xsl:choose>
                        <xsl:when test="responseformat = 'monospaced'">
                            <xsl:value-of select="$format_mono_label"/>
                        </xsl:when>
                        <xsl:when test="responseformat = 'editorfilepicker'">
                            <xsl:value-of select="$format_editorfilepicker_label"/>
                        </xsl:when>
                        <xsl:when test="responseformat = 'plain'">
                            <xsl:value-of select="$format_plain_label"/>
                        </xsl:when>
                        <xsl:when test="responseformat = 'noinline'">
                            <xsl:value-of select="$format_noinline_label"/>
                        </xsl:when>
                        <xsl:when test="responseformat = 'editor'">
                            <xsl:value-of select="$format_html_label"/>
                        </xsl:when>
                        <xsl:when test="$moodle_release_number = '19' and questiontext/@format = 'markdown'">
                            <xsl:value-of select="$format_markdown_label"/>
                        </xsl:when>
                        <xsl:when test="$moodle_release_number = '19' and questiontext/@format = 'moodle_auto_format'">
                            <xsl:value-of select="$format_auto_label"/>
                        </xsl:when>
                        <xsl:when test="$moodle_release_number = '19' and questiontext/@format = 'plain_text'">
                            <xsl:value-of select="$format_plain_label"/>
                        </xsl:when>
                        <xsl:when test="$moodle_release_number = '19' and questiontext/@format = 'html'">
                            <xsl:value-of select="$format_html_label"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="$format_editorfilepicker_label"/></xsl:otherwise>
                        </xsl:choose>
                    </p>
                </td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>

            <!-- Essays: text input required flag -->
            <xsl:if test="$moodle_release_number &gt;= '27'">
                <xsl:variable name="responserequired_flag">
                    <xsl:choose>
                    <xsl:when test="responserequired = 0"><xsl:value-of select="$no_label"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$yes_label"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <tr>
                    <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$responserequired_label"/></p></td>
                    <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$responserequired_flag"/></p></td>
                </tr>
            </xsl:if>

            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$responsefieldlines_label"/></p></td>
                    <td style="width: 1.0cm">
                    <p class="Cell">
                            <xsl:choose>
                            <xsl:when test="responsefieldlines">
                                <xsl:value-of select="responsefieldlines"/>
                            </xsl:when>
                            <xsl:otherwise><xsl:text>15</xsl:text></xsl:otherwise>
                            </xsl:choose>
                        </p>
                    </td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>

            <!-- Essays: number of attachments field -->
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$allowattachments_label"/></p></td>
                <td style="width: 1.0cm">
                    <p class="Cell">
                        <xsl:choose>
                        <xsl:when test="attachments">
                            <xsl:value-of select="attachments"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
                        </xsl:choose>
                    </p>
                </td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>

            <!-- Essays: number of attachments required field -->
            <xsl:if test="$moodle_release_number &gt;= '27'">
                <xsl:text>&#x0a;</xsl:text>
                <xsl:call-template name="debugComment">
                    <xsl:with-param name="comment_text">
                        <xsl:value-of select="concat('$attachmentsrequired_label: ', $attachmentsrequired_label, '; attachmentsrequired: ', attachmentsrequired, '&#x0a;')"/>
                        <xsl:value-of select="concat('$moodle_release_number: ', $moodle_release_number, '&#x0a;')"/>
                    </xsl:with-param>
                    <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
                </xsl:call-template>
                <tr>
                    <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$attachmentsrequired_label"/></p></td>
                    <td style="width: 1.0cm">
                        <p class="Cell">
                            <xsl:choose>
                            <xsl:when test="attachmentsrequired">
                                <xsl:value-of select="attachmentsrequired"/>
                            </xsl:when>
                            <xsl:otherwise><xsl:text>0</xsl:text></xsl:otherwise>
                            </xsl:choose>
                        </p>
                    </td>
                </tr>
                <xsl:text>&#x0a;</xsl:text>
            </xsl:if>

            <!-- Essays: accepted file types field -->
            <xsl:if test="$moodle_release_number &gt;= '35'">
                <xsl:text>&#x0a;</xsl:text>
                <tr>
                    <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$acceptedfiletypes_label"/></p></td>
                    <td style="width: 1.0cm">
                        <p class="Cell">
                            <xsl:choose>
                            <xsl:when test="acceptedfiletypes">
                                <xsl:value-of select="acceptedfiletypes"/>
                            </xsl:when>
                            <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
                            </xsl:choose>
                        </p>
                    </td>
                </tr>
                <xsl:text>&#x0a;</xsl:text>
            </xsl:if>
        </xsl:if> <!-- 2.x Essay-specific question fields -->

        <!-- Short answers: are they case-sensitive? -->
        <xsl:if test="$qtype = 'SA'">
            <xsl:variable name="casesensitive_flag">
                <xsl:choose>
                <xsl:when test="usecase = 0"><xsl:value-of select="$no_label"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$yes_label"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>

            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$casesensitive_label"/></p></td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$casesensitive_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Cloze question flags: are SA case-sensitive?, are MC drop-down, vertical or horizontal radio? -->
        <xsl:if test="$qtype = 'CL'">
            <xsl:call-template name="debugComment">
                <xsl:with-param name="comment_text">
                    <xsl:value-of select="concat('cloze_sa_casesensitive_flag: ', $cloze_sa_casesensitive_flag, '; cloze_sa_formatting_case_sensitive: ', $cloze_sa_formatting_case_sensitive, '; cloze_sa_formatting_case_insensitive: ', $cloze_sa_formatting_case_insensitive, '&#x0a;')"/>
                    <xsl:value-of select="concat('cloze_mc_formatting_flag: ', $cloze_mc_formatting_flag, '; cloze_mc_formatting_radio_horizontal: ', $cloze_mc_formatting_radio_horizontal, '; cloze_mc_formatting_dropdown: ', $cloze_mc_formatting_dropdown, '; cloze_mc_formatting_radio_vertical: ', $cloze_mc_formatting_radio_vertical, '&#x0a;')"/>
                    <xsl:value-of select="concat('cloze_defaultmark_value: ', $defaultmark_value, '&#x0a;')"/>
                    <xsl:value-of select="concat('cloze_questiontext_string: ', translate($cloze_questiontext_string, '&#x0a;', ' '), '&#x0a;')"/>
                </xsl:with-param>
                <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
            </xsl:call-template>

            <!-- Case sensitivity for SA subquestions -->
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$casesensitive_label"/></p></td>
                <td style="width: 1.0cm"><p class="Cell">
                    <xsl:choose>
                    <xsl:when test="$cloze_sa_casesensitive_flag = '1'">
                        <xsl:value-of select="$yes_label"/>
                    </xsl:when>
                    <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
                    </xsl:choose>
                </p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
            <!-- Style and orientation for MC subquestions: Drop-down menu, vertical or horizontal radio button -->
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$cloze_mcformat_label"/></p></td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$cloze_mc_formatting_flag"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Show number of correct responses when finished (Moodle 2.x only) -->
        <xsl:if test="($qtype = 'MA' or $qtype = 'MAT' or $qtype = 'MS' or $qtype = 'MW' or $qtype = 'DDI' or $qtype = 'DDM' or $qtype = 'DDT') and $moodle_release_number &gt; '19'">
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right">
                    <xsl:choose>
                    <xsl:when test="$qtype = 'MS'">
                        <xsl:value-of select="concat($hint_shownumpartscorrect_label, $colon_string)"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="$shownumcorrectfeedback_label"/>
                    </xsl:otherwise>
                    </xsl:choose>
                </p></td>
                <td style="width: 1.0cm">
                    <p class="Cell">
                        <xsl:choose>
                        <xsl:when test="shownumcorrect">
                            <xsl:value-of select="$yes_label"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
                        </xsl:choose>
                    </p>
                </td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- 2nd last heading row: Penalty for each incorrect try: Don't include for True/False, as it is always 100% in this case -->
        <xsl:if test="$qtype != 'DE' and $qtype != 'ES' and $qtype != 'TF'">
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$penalty_label"/></p></td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$penalty_value"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- New 2nd last heading row: optional ID number -->
        <xsl:if test="$moodle_release_number &gt;= '36'">
            <xsl:text>&#x0a;</xsl:text>
            <tr>
                <td colspan="3" style="width: 12.0cm"><p class="TableRowHead" style="text-align: right"><xsl:value-of select="$idnumber_label"/></p></td>
                <td style="width: 1.0cm">
                    <p class="QFID">
                        <xsl:choose>
                        <xsl:when test="$idnumber_value != ''">
                            <xsl:value-of select="$idnumber_value"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="$blank_cell"/>
                        </xsl:otherwise>
                        </xsl:choose>
                    </p>
                </td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if> <!-- 2.x Essay-specific question fields -->

        <!-- Last heading row: column headings for table body -->
        <tr>
            <td style="width: 1.0cm"><p class="{$colheading1_style}"><xsl:value-of select="$colheading1_label"/></p></td>
            <td style="{$col2_width}"><p class="TableHead"><xsl:value-of select="$colheading2_label"/></p></td>
            <td style="{$col3_width}"><p class="TableHead"><xsl:value-of select="$colheading3_label"/></p></td>
            <td style="width: 1.0cm"><p class="TableHead"><xsl:value-of select="$colheading4_label"/></p></td>
        </tr>
        <xsl:text>&#x0a;</xsl:text>
    </thead>
    <tbody>
        <xsl:text>&#x0a;</xsl:text>

        <!-- Handle the body, containing the options and feedback (for most questions) -->

        <!-- The first body row is the most complicated depending on the question, so do the special cases first -->
        <xsl:choose>
        <xsl:when test="$qtype = 'CL'">
            <!-- Cloze questions should ideally have distractors in the rows, but that's too complicated at the moment, so just include one empty row -->
            <tr>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <td style="{$col2_width}"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
            </tr>
        </xsl:when>
        <xsl:when test="$qtype = 'ES'">
            <!-- Essay questions in Moodle 2.5+ have a response template and information for graders, so put in a row for these -->
            <tr>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <td style="{$col2_width}">
                <!-- Essay questions in Moodle 1.9 to 2.3 have no response template, so leave it out -->
                    <xsl:choose>
                    <xsl:when test="$moodle_release_number &lt; '25'">
                        <p class="Cell"><xsl:value-of select="$blank_cell"/></p>
                    </xsl:when>
                    <xsl:when test="$moodle_release_number &gt; '24' and responsetemplate and normalize-space(responsetemplate) = ''">
                        <p class="Cell"><xsl:value-of select="$responsetemplate_help_label"/></p>
                    </xsl:when>
                    <xsl:when test="responsetemplate and responsetemplate/@format and responsetemplate/@format = 'html'">
                        <xsl:apply-templates select="responsetemplate/*"/>
                    </xsl:when>
                    <xsl:when test="responsetemplate and responsetemplate/@format and responsetemplate/@format != 'html'">
                        <p class="Cell"><xsl:apply-templates select="responsetemplate/*"/></p>
                    </xsl:when>
                    <xsl:otherwise>
                        <!-- No essay response template, so it's probably an older version of Moodle. -->
                        <p class="Cell"><xsl:value-of select="$blank_cell"/></p>
                    </xsl:otherwise>
                    </xsl:choose>
                </td>
                <td style="{$col3_width}">
                    <xsl:choose>
                    <xsl:when test="$moodle_release_number &gt; '19' and graderinfo and graderinfo = ''">
                        <p class="Cell"><xsl:value-of select="$blank_cell"/></p>
                    </xsl:when>
                    <xsl:when test="$moodle_release_number &gt; '19' and graderinfo and graderinfo/@format and graderinfo/@format = 'html'">
                        <xsl:apply-templates select="graderinfo/*"/>
                    </xsl:when>
                    <xsl:when test="$moodle_release_number &gt; '19' and graderinfo and graderinfo/@format and graderinfo/@format != 'html'">
                        <p class="Cell"><xsl:apply-templates select="graderinfo/*"/></p>
                    </xsl:when>
                    <xsl:otherwise>
                        <!-- No information for essay graders, so it's probably an older version of Moodle. -->
                        <p class="Cell"><xsl:value-of select="$blank_cell"/></p>
                    </xsl:otherwise>
                    </xsl:choose>
                </td>
                <!-- No grade info used in essays -->
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
            </tr>
        </xsl:when>
        <xsl:when test="$qtype = 'DDI'">
            <!-- Drag and Drop image questions -->
            <xsl:apply-templates select="drag">
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="numbering_flag" select="$numbering_flag"/>
            </xsl:apply-templates>
            <tr>
                <td style="width: 1.0cm"><p class="{$colheading1_style}" style="page-break-after:avoid">#</p></td>
                <td style="{$col3_width}"><p class="TableHead"><xsl:value-of select="$ddi_dropzoneheader_label"/></p></td>
                <td style="{$col2_width}"><p class="TableHead"><xsl:value-of select="$ddi_coords_label"/></p></td>
                <td style="width: 1.0cm"><p class="TableHead"><xsl:value-of select="$ddi_draggableitem_label"/></p></td>
            </tr>
            <xsl:apply-templates select="drop" mode="ddimageortext">
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="numbering_flag" select="$numbering_flag"/>
            </xsl:apply-templates>
        </xsl:when>
        <xsl:when test="$qtype = 'DDM'">
            <!-- Drag and Drop marker onto image questions -->
            <xsl:apply-templates select="drag" mode="ddmarker">
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="numbering_flag" select="$numbering_flag"/>
            </xsl:apply-templates>
            <tr>
                <td style="width: 1.0cm"><p class="{$colheading1_style}" style="page-break-after:avoid">#</p></td>
                <td style="{$col2_width}"><p class="TableHead"><xsl:value-of select="$ddm_shape_label"/></p></td>
                <td style="{$col3_width}"><p class="TableHead"><xsl:value-of select="$ddm_coords_label"/></p></td>
                <td style="width: 1.0cm"><p class="TableHead"><xsl:value-of select="$ddm_marker_label"/></p></td>
            </tr>
            <xsl:apply-templates select="drop" mode="ddmarker">
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="numbering_flag" select="$numbering_flag"/>
            </xsl:apply-templates>
        </xsl:when>
        <xsl:otherwise>
            <!-- Special cases done, so for other question types, loop through the answers -->
            <xsl:apply-templates select="answer|subquestion|selectoption|dragbox">
                <xsl:with-param name="qtype" select="$qtype"/>
                <xsl:with-param name="numbering_flag" select="$numbering_flag"/>
            </xsl:apply-templates>
        </xsl:otherwise>
        </xsl:choose>
        <xsl:text>&#x0a;</xsl:text>

        <!-- General feedback for all question types except Description -->
        <xsl:if test="$qtype != 'DE'">
            <tr>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="$generalfeedback_label"/></p></th>
                <td style="{$col3_width}">
                    <xsl:choose>
                    <xsl:when test="generalfeedback/text = ''">
                        <p class="Cell"><xsl:value-of select="$blank_cell"/></p>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="generalfeedback/*"/>
                    </xsl:otherwise>
                    </xsl:choose>
                
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
            </tr>
        <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Correct and Incorrect feedback for MA, MAT, MC and MW questions only -->
        <xsl:if test="$qtype = 'MA' or $qtype = 'MC' or $qtype = 'MS' or ($qtype = 'MAT' and $moodle_release_number &gt; '19') or $qtype = 'MW' or starts-with($qtype, 'DD')">
            <tr>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="$correctfeedback_label"/></p></th>
                <td style="{$col3_width}">
                    <xsl:choose>
                    <xsl:when test="normalize-space(correctfeedback/text) = ''">
                        <p class="Cell"><xsl:value-of select="$correctfeedback_default"/></p>
                    </xsl:when>
                    <xsl:otherwise><xsl:apply-templates select="correctfeedback/*"/></xsl:otherwise>
                    </xsl:choose>
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
            <tr>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="$incorrectfeedback_label"/></p></th>
                <td style="{$col3_width}">
                    <xsl:choose>
                    <xsl:when test="normalize-space(incorrectfeedback/text) = ''">
                        <p class="Cell"><xsl:value-of select="$incorrectfeedback_default"/></p>
                    </xsl:when>
                    <xsl:otherwise><xsl:apply-templates select="incorrectfeedback/*"/></xsl:otherwise>
                    </xsl:choose>
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>
        <!-- Partially correct feedback for MA (Multi-answer), MAT(ching) and Missing Word (gapselect) questions only -->
        <xsl:if test="$qtype = 'MA' or ($qtype = 'MAT' and $moodle_release_number &gt; '19') or $qtype = 'MW' or starts-with($qtype, 'DD')">
            <tr>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="$pcorrectfeedback_label"/></p></th>
                <td style="{$col3_width}">
                    <xsl:choose>
                    <xsl:when test="normalize-space(partiallycorrectfeedback/text) = ''">
                        <p class="Cell"><xsl:value-of select="$pcorrectfeedback_default"/></p>
                    </xsl:when>
                    <xsl:otherwise><xsl:apply-templates select="partiallycorrectfeedback/*"/></xsl:otherwise>
                    </xsl:choose>
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Hints rows (added in Moodle 2.x) for CL MA MAT MC SA questions -->
        <xsl:if test="$moodle_release_number &gt; '19'">
            <xsl:for-each select="hint[text != '']">
                <!-- Define a label for the hint text row (row 1 of 3) -->
                <xsl:variable name="hint_number_label" select="concat(substring-before($hintn_label, '{no}'), position())"/>
                <tr>
                    <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                    <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($hint_number_label, $colon_string)"/></p></th>
                    <td style="{$col3_width}">
                        <xsl:apply-templates/>
                    </td>
                    <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                </tr>
                <xsl:text>&#x0a;</xsl:text>
                <!-- Most question types allow for some fields on the behaviour of hints, but SA doesn't, and CL only in 2.4+ -->
                <xsl:if test="($qtype != 'CL') or ($qtype = 'CL' and $moodle_release_number &gt; '23')">
                    <tr>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                        <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($hint_shownumpartscorrect_label, ' (', $hint_number_label, ')', $colon_string)"/></p></th>
                        <td style="{$col3_width}"><p class="Cell">
                            <xsl:choose>
                            <xsl:when test="shownumcorrect">
                                <xsl:value-of select="$yes_label"/>
                            </xsl:when>
                            <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
                            </xsl:choose>
                        </p></td>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                    </tr>
                    <xsl:text>&#x0a;</xsl:text>
                    <tr>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                        <th style="{$col2_width}">
                            <p class="TableRowHead">
                                <xsl:choose>
                                <xsl:when test="$qtype = 'DDM'">
                                    <xsl:value-of select="concat($ddm_hint_clearwrongparts_label, ' (', $hint_number_label, ')', $colon_string)"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="concat($hint_clearwrongparts_label, ' (', $hint_number_label, ')', $colon_string)"/>
                                </xsl:otherwise>
                                </xsl:choose>
                            </p>
                        </th>
                        <td style="{$col3_width}"><p class="Cell">
                            <xsl:choose>
                            <xsl:when test="clearwrong">
                                <xsl:value-of select="$yes_label"/>
                            </xsl:when>
                            <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
                            </xsl:choose>
                        </p></td>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                    </tr>
                    <xsl:text>&#x0a;</xsl:text>
                    <xsl:if test="$qtype = 'MS'">
                        <tr>
                            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                            <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($multichoiceset_showeachfeedback_label, ' (', $hint_number_label, ')', $colon_string)"/></p></th>
                            <td style="{$col3_width}"><p class="Cell">
                                <xsl:choose>
                                <xsl:when test="options and options = '1'">
                                    <xsl:value-of select="$yes_label"/>
                                </xsl:when>
                                <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
                                </xsl:choose>
                            </p></td>
                            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                        </tr>
                        <xsl:text>&#x0a;</xsl:text>
                    </xsl:if>
                    <xsl:text>&#x0a;</xsl:text>
                    <xsl:if test="$qtype = 'DDM'">
                        <tr>
                            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                            <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($ddm_stateincorrectlyplaced_label, ' (', $hint_number_label, ')', $colon_string)"/></p></th>
                            <td style="{$col3_width}"><p class="Cell">
                                <xsl:choose>
                                <xsl:when test="options and options = '1'">
                                    <xsl:value-of select="$yes_label"/>
                                </xsl:when>
                                <xsl:otherwise><xsl:value-of select="$no_label"/></xsl:otherwise>
                                </xsl:choose>
                            </p></td>
                            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                        </tr>
                        <xsl:text>&#x0a;</xsl:text>
                    </xsl:if>
                </xsl:if>
            </xsl:for-each>

            <!-- Include 1 empty hint row even if there are no hints, or if hint elements are present, but only have flags set -->
            <xsl:if test="(not(hint) or hint/text = '') and ($qtype != 'DE' and $qtype != 'ES' and $qtype != 'TF')">
                <!-- Define a label for the hint text row (row 1 of 3) -->
                <xsl:variable name="hint_number_label" select="concat(substring-before($hintn_label, '{no}'), 1)"/>
                <tr>
                    <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                    <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($hint_number_label, $colon_string)"/></p></th>
                    <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                    <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                </tr>
                <xsl:text>&#x0a;</xsl:text>
                <!-- Most question types allow for some fields on the behaviour of hints, but SA doesn't, and CL only in 2.4+ -->
                <xsl:if test="($qtype != 'CL' and $qtype != 'SA') or ($qtype = 'CL' and $moodle_release_number &gt; '23')">
                    <tr>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                        <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($hint_shownumpartscorrect_label, ' (', $hint_number_label, ')', $colon_string)"/></p></th>
                        <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="$no_label"/></p></td>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                    </tr>
                    <xsl:text>&#x0a;</xsl:text>
                    <tr>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                        <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($hint_clearwrongparts_label, ' (', $hint_number_label, ')', $colon_string)"/></p></th>
                        <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="$no_label"/></p></td>
                        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                    </tr>
                    <xsl:if test="$qtype = 'MS'">
                        <xsl:text>&#x0a;</xsl:text>
                        <tr>
                            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                            <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="concat($multichoiceset_showeachfeedback_label, ' (', $hint_number_label, ')', $colon_string)"/></p></th>
                            <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="$no_label"/></p></td>
                            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                        </tr>
                    </xsl:if>
                </xsl:if>
                <xsl:text>&#x0a;</xsl:text>
            </xsl:if> 
            <!-- End Hint processing -->

            <!-- Tags row (added in Moodle 2.x) -->
            <tr>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
                <th style="{$col2_width}"><p class="TableRowHead"><xsl:value-of select="$tags_label"/></p></th>
                <td style="{$col3_width}">
                    <p class="Cell">
                            <xsl:choose>
                            <xsl:when test="tags[tag = '']">
                                <!-- tag element present but empty -->
                                <xsl:value-of select="$blank_cell"/>
                            </xsl:when>
                            <xsl:when test="tags/tag">
                                <!-- tag element present and not empty -->
                                    <xsl:for-each select="tags/tag">
                                        <xsl:value-of select="normalize-space(.)"/>
                                        <xsl:if test="position() != last()">
                                            <xsl:text>, </xsl:text>
                                        </xsl:if>
                                    </xsl:for-each>
                            </xsl:when>
                            <xsl:otherwise><xsl:value-of select="$blank_cell"/></xsl:otherwise>
                            </xsl:choose>
                    </p>
                </td>
                <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
            </tr>
            <xsl:text>&#x0a;</xsl:text>
        </xsl:if>

        <!-- Instructions row for each question type -->
        <tr>
            <td colspan="3" style="width: 12.0cm">
            <xsl:choose>
            <xsl:when test="$qtype = 'CL'">
                <p class="Cell">
                    <xsl:apply-templates select="$moodle_labels/data[@name = 'qformat_wordtable_cloze_instructions']"/>
                </p>
            </xsl:when>
            <xsl:otherwise>
                <p class="Cell"><i><xsl:value-of select="$instruction_text"/></i></p>
            </xsl:otherwise>
            </xsl:choose>
            
            </td>
            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
        </tr>
        <xsl:text>&#x0a;</xsl:text>

    </tbody>
    </table>
    </div>
    <!-- CONTRIB-2847: Insert an empty paragraph after the table so that the "Insert new question" facility works -->
    <p class="MsoNormal"><xsl:value-of select="$blank_cell"/></p>
</xsl:template>

<!-- Omit hint sub-elements, including multichoiceset (All-or-Nothing MCQ) -->
<xsl:template match="hint/clearwrong|hint/shownumcorrect|hint/options"/>

<!-- Handle True/False question rows as a special case, as they only contain 'true' or 'false', which should be translated -->
<xsl:template match="answer[ancestor::question/@type = 'truefalse']" priority="2">
    <tr>
        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
        <td style="{$col2_width}">
            <xsl:choose>
            <xsl:when test="text = 'true'">
                <p class="Cell"><xsl:value-of select="$true_label"/></p>
            </xsl:when>
            <xsl:when test="text = 'false'">
                <p class="Cell"><xsl:value-of select="$false_label"/></p>
            </xsl:when>
            </xsl:choose>
        </td>
        <td style="{$col3_width}"><p class="QFFeedback"><xsl:apply-templates select="feedback/*"/></p></td>
        <td style="width: 1.0cm"><p class="QFGrade"><xsl:value-of select="@fraction"/></p></td>
    </tr>
</xsl:template>

<!-- Handle standard question rows -->
<xsl:template match="answer[not(ancestor::subquestion)]|subquestion">
    <xsl:param name="qtype"/>
    <xsl:param name="numbering_flag"/>

    <!-- The 1st column contains a list item for MA and MC, and is blank for other questions. Use the paragraph style to control the enumeration -->
    <xsl:variable name="numbercolumn_class">
        <xsl:choose>
        <xsl:when test="$qtype = 'MAT'">
            <xsl:text>MsoListNumber</xsl:text>
        </xsl:when>
        <xsl:when test="$qtype = 'SA'">
            <xsl:text>Cell</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:text>QFOption</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Simplify the percentage score value for an answer to keep it short, to fit it in the 4th column -->
    <xsl:variable name="grade_value">
        <xsl:choose>
        <xsl:when test="@fraction = '83.33333'"><xsl:text>83.3</xsl:text></xsl:when>
        <xsl:when test="@fraction = '66.66667'"><xsl:text>66.6</xsl:text></xsl:when>
        <xsl:when test="@fraction = '33.33333'"><xsl:text>33.3</xsl:text></xsl:when>
        <xsl:when test="@fraction = '16.66667'"><xsl:text>16.6</xsl:text></xsl:when>
        <xsl:when test="@fraction = '14.28571'"><xsl:text>14.3</xsl:text></xsl:when>
        <xsl:when test="@fraction = '11.11111'"><xsl:text>11.1</xsl:text></xsl:when>
        <xsl:when test="@fraction = '-83.33333'"><xsl:text>-83.3</xsl:text></xsl:when>
        <xsl:when test="@fraction = '-66.66667'"><xsl:text>-66.6</xsl:text></xsl:when>
        <xsl:when test="@fraction = '-33.33333'"><xsl:text>-33.3</xsl:text></xsl:when>
        <xsl:when test="@fraction = '-16.66667'"><xsl:text>-16.6</xsl:text></xsl:when>
        <xsl:when test="@fraction = '-14.28571'"><xsl:text>-14.3</xsl:text></xsl:when>
        <xsl:when test="@fraction = '-11.11111'"><xsl:text>-11.1</xsl:text></xsl:when>
        <xsl:otherwise><xsl:value-of select="@fraction"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Process body row columns 1 and 2, for MA, MC, MAT and SA -->
    <tr>
        <td style="width: 1.0cm"><p class="{$numbercolumn_class}"><xsl:value-of select="$blank_cell"/></p></td>
        <xsl:choose>
        <xsl:when test="$qtype = 'SA'">
            <td style="{$col2_width}"><p class="Cell"><xsl:value-of select="normalize-space(text)"/></p></td>
        </xsl:when>
        <xsl:otherwise>
            <td style="{$col2_width}"><xsl:apply-templates select="text|file"/></td>
        </xsl:otherwise>
        </xsl:choose>

        <!-- Process body row columns 3 and 4 -->
        <xsl:choose>
        <xsl:when test="$qtype = 'MAT'">
            <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="answer/*"/></p></td>
            <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
        </xsl:when>
        <xsl:otherwise>
            <td style="{$col3_width}"><p class="QFFeedback"><xsl:apply-templates select="feedback/*"/></p></td>
            <td style="width: 1.0cm"><p class="QFGrade"><xsl:value-of select="$grade_value"/></p></td>
        </xsl:otherwise>
        </xsl:choose>
    </tr>
</xsl:template>

<!-- Handle MQXML text elements, which may consist only of a CDATA section -->
<xsl:template match="text">
    <xsl:variable name="text_string">
        <xsl:variable name="raw_text" select="normalize-space(.)"/>
        
        <xsl:choose>
        <!-- If the string is wrapped in <p>...</p>, get rid of it -->
        <xsl:when test="starts-with($raw_text, '&lt;p&gt;') and substring($raw_text, -4) = '&lt;/p&gt;'">
            <!-- 7 = string-length('<p>') + string-length('</p>') </p> -->
            <xsl:value-of select="substring($raw_text, 4, string-length($raw_text) - 7)"/>
        </xsl:when>
        <xsl:when test="starts-with($raw_text, '&lt;table')">
            <!-- Add a blank paragraph before the table, -->
            <xsl:value-of select="concat('&lt;p&gt;', $blank_cell, '&lt;/p&gt;', $raw_text)"/>
        </xsl:when>
        <xsl:when test="$raw_text = ''"><xsl:value-of select="$blank_cell"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$raw_text"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    
    <xsl:value-of select="$text_string" disable-output-escaping="yes"/>
</xsl:template>

<!-- Handle Cloze text by converting it back to nicely formatted bold/italic, instead of ugly Moodle format, if we can -->
<xsl:template match="text" mode="cloze">
    <xsl:param name="cloze_mc_formatting_flag"/>
    <xsl:param name="cloze_sa_casesensitive_flag"/>
    <xsl:param name="cloze_defaultmark_value"/>

    <xsl:variable name="text_string">
        <xsl:variable name="raw_text" select="normalize-space(.)"/>
        
        <xsl:choose>
        <!-- If the string is wrapped in <p>...</p>, get rid of it -->
        <xsl:when test="starts-with($raw_text, '&lt;p&gt;') and substring($raw_text, -4) = '&lt;/p&gt;'">
            <!-- 7 = string-length('<p>') + string-length('</p>') </p> -->
            <xsl:value-of select="substring($raw_text, 4, string-length($raw_text) - 7)"/>
        </xsl:when>
        <xsl:when test="$raw_text = ''"><xsl:value-of select="$blank_cell"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$raw_text"/></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Convert the Cloze string to nice Word format -->
    <xsl:choose>
    <xsl:when test="normalize-space($text_string) != '' and normalize-space($text_string) != '&#160;'">
        <xsl:call-template name="convert_cloze_string">
            <xsl:with-param name="cloze_string" select="$text_string"/>
            <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
            <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
            <xsl:with-param name="cloze_defaultmark_value" select="$cloze_defaultmark_value"/>
        </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$blank_cell"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Convert Cloze text strings into bold or italic, or leave as is if conversion not possible -->
<!-- This template calls itself recursively to handle each subquestion in the string -->
<xsl:template name="convert_cloze_string">
    <xsl:param name="cloze_string"/>
    <xsl:param name="cloze_mc_formatting_flag"/> <!-- Default MC format: D, H or V -->
    <xsl:param name="cloze_sa_casesensitive_flag"/> <!-- Default SA case: 0 or 1 -->
    <xsl:param name="cloze_defaultmark_value"/> <!-- Default mark/weight -->

    <!--
    <xsl:message>
        <xsl:value-of select="concat('convert_cloze_string(cloze_string = &quot;', translate(substring($cloze_string, 1, 100), '&#x0a;', ''), '&quot;; default = ', $cloze_defaultmark_value, ')')"/>
    </xsl:message>
    -->
    <xsl:choose>
    <xsl:when test="not(contains($cloze_string, $cloze_start_delimiter))">
        <!-- Simple case: No subquestion left, so return the remaining string -->
        <xsl:value-of select="$cloze_string" disable-output-escaping="yes"/>
    </xsl:when>
    <xsl:otherwise>
        <!-- Have a subquestion, so handle it -->
        <!-- First, copy the text prior to the first subquestion -->
        <xsl:value-of select="substring-before($cloze_string, $cloze_start_delimiter)" disable-output-escaping="yes"/>
        
        <!-- Next, identify the first subquestion, and figure out what is its type and default mark -->
        <xsl:variable name="this_subquestion_string" select="substring-before(substring-after($cloze_string, $cloze_start_delimiter), $cloze_end_delimiter)"/>
        <xsl:variable name="this_subquestion_defaultmark">
            <xsl:call-template name="get_cloze_defaultmark">
                <xsl:with-param name="cloze_questiontext_string" select="concat($cloze_start_delimiter, $this_subquestion_string, $cloze_end_delimiter)"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="this_subquestion_type">
            <xsl:variable name="this_subquestion_type_string" select="concat(':', substring-before(substring-after($this_subquestion_string, $cloze_keyword_delimiter), $cloze_keyword_delimiter), ':')"/>
            <xsl:choose>
            <!-- Numerical -->
            <xsl:when test="$this_subquestion_type_string = $cloze_num_keyword1 or $this_subquestion_type_string = $cloze_num_keyword2">
                <xsl:value-of select="$cloze_num_keyword1"/>
            </xsl:when>
            <!-- Case-insensitive SA -->
            <xsl:when test="$this_subquestion_type_string = $cloze_sa_keyword1 or $this_subquestion_type_string = $cloze_sa_keyword2 or $this_subquestion_type_string = $cloze_sa_keyword3">
                <xsl:value-of select="$cloze_sa_keyword1"/>
            </xsl:when>
            <!-- Case-sensitive SA -->
            <xsl:when test="$this_subquestion_type_string = $cloze_sac_keyword1 or $this_subquestion_type_string = $cloze_sac_keyword2 or $this_subquestion_type_string = $cloze_sac_keyword3">
                <xsl:value-of select="$cloze_sac_keyword1"/>
            </xsl:when>
            <!-- Dropdown MC -->
            <xsl:when test="$this_subquestion_type_string = $cloze_mc_keyword1 or $this_subquestion_type_string = $cloze_mc_keyword2">
                <xsl:value-of select="$cloze_mc_keyword1"/>
            </xsl:when>
            <!-- Vertical radio-button MC -->
            <xsl:when test="$this_subquestion_type_string = $cloze_mcv_keyword1 or $this_subquestion_type_string = $cloze_mcv_keyword2">
                <xsl:value-of select="$cloze_mcv_keyword1"/>
            </xsl:when>
            <!-- Horizontal radio-button MC -->
            <xsl:when test="$this_subquestion_type_string = $cloze_mch_keyword1 or $this_subquestion_type_string = $cloze_mch_keyword2">
                <xsl:value-of select="$cloze_mch_keyword1"/>
            </xsl:when>
            </xsl:choose>
        </xsl:variable>

        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('this_subquestion: type = ', $this_subquestion_type, ', mark = ', normalize-space(translate($this_subquestion_defaultmark, '&#x0a;', '')), '; default: ', $cloze_defaultmark_value)"/>
            <xsl:with-param name="condition" select="$debug_flag &gt; 1"/>
        </xsl:call-template>

        <!-- Compare this subquestions' type and mark against the defaults, to figure out how best to present it -->
        <xsl:choose>
        <xsl:when test="$this_subquestion_defaultmark != $cloze_defaultmark_value">
            <!-- The default mark for this subquestion doesn't match the default for the whole Cloze, so just copy the raw text -->
            <xsl:value-of select="concat($cloze_start_delimiter, $this_subquestion_string, $cloze_end_delimiter)" disable-output-escaping="yes"/>
        </xsl:when>
        <xsl:when test="($this_subquestion_type = $cloze_sa_keyword1 and $cloze_sa_casesensitive_flag = '0') or ($this_subquestion_type = $cloze_sac_keyword1 and $cloze_sa_casesensitive_flag = '1')">
            <!-- An SA subquestion in a consistent case-sensitivity cloze, so convert it to italic -->
            <xsl:call-template name="convert_cloze_subquestion">
                <xsl:with-param name="cloze_subquestion_string" select="$this_subquestion_string"/>
                <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
                <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
            </xsl:call-template>
            </xsl:when>
        <xsl:when test="($this_subquestion_type = $cloze_mc_keyword1 and $cloze_mc_formatting_flag = 'D') or ($this_subquestion_type = $cloze_mch_keyword1 and $cloze_mc_formatting_flag = 'H') or ($this_subquestion_type = $cloze_mcv_keyword1 and $cloze_mc_formatting_flag = 'V')">
            <!-- An MCD, MCH or MCV subquestion in a consistent cloze, so convert it to  bold -->
            <xsl:call-template name="convert_cloze_subquestion">
                <xsl:with-param name="cloze_subquestion_string" select="$this_subquestion_string"/>
                <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
                <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:when test="$this_subquestion_type = $cloze_num_keyword1 or $this_subquestion_type = $cloze_num_keyword2">
            <!-- A NUMERICAL subquestion, so convert it to underline -->
            <xsl:call-template name="convert_cloze_subquestion">
                <xsl:with-param name="cloze_subquestion_string" select="$this_subquestion_string"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <!-- Some other case we don't handle well, so just copy the raw text -->
            <xsl:value-of select="concat($cloze_start_delimiter, $this_subquestion_string, $cloze_end_delimiter)" disable-output-escaping="yes"/>
        </xsl:otherwise>
        </xsl:choose>

        <!-- Finally, recurse through the remaining part of the string to do the remaining subquestions -->
        <xsl:call-template name="convert_cloze_string">
            <xsl:with-param name="cloze_string" select="substring-after($cloze_string, $cloze_end_delimiter)"/>
            <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
            <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
            <xsl:with-param name="cloze_defaultmark_value" select="$cloze_defaultmark_value"/>
        </xsl:call-template>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Convert a single embedded NUMERICAL, SHORTANSWER or MULTICHOICE subquestion into corresponding Word markup -->
<xsl:template name="convert_cloze_subquestion">
    <xsl:param name="cloze_subquestion_string"/>
    <xsl:param name="cloze_mc_formatting_flag"/>
    <xsl:param name="cloze_sa_casesensitive_flag"/>

    <!-- Get the remainder of the string, after the 2nd colon, which occurs as the keyword end delimiter, e.g. ':SA:' -->
    <xsl:variable name="cloze_remainder_string" select="substring-after(substring-after($cloze_subquestion_string, $cloze_keyword_delimiter), $cloze_keyword_delimiter)"/>

    <xsl:choose>
    <xsl:when test="contains($cloze_subquestion_string, $cloze_num_keyword1) or contains($cloze_subquestion_string, $cloze_num_keyword2)">
        <u>
            <xsl:call-template name="format_cloze_subquestion">
                <xsl:with-param name="cloze_subquestion_string" select="$cloze_remainder_string"/>
                <xsl:with-param name="first_answer_item" select="'true'"/>
                <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
                <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
            </xsl:call-template>
        </u>
    </xsl:when>
    <xsl:when test="contains($cloze_subquestion_string, $cloze_sa_keyword1) or contains($cloze_subquestion_string, $cloze_sa_keyword2) or contains($cloze_subquestion_string, $cloze_sa_keyword3) or contains($cloze_subquestion_string, $cloze_sac_keyword1) or contains($cloze_subquestion_string, $cloze_sac_keyword2) or contains($cloze_subquestion_string, $cloze_sac_keyword3)">
        <i>
            <xsl:call-template name="format_cloze_subquestion">
                <xsl:with-param name="cloze_subquestion_string" select="$cloze_remainder_string"/>
                <xsl:with-param name="first_answer_item" select="'true'"/>
                <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
                <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
            </xsl:call-template>
        </i>
    </xsl:when>
    <xsl:when test="contains($cloze_subquestion_string, $cloze_mc_keyword1) or contains($cloze_subquestion_string, $cloze_mc_keyword2) or contains($cloze_subquestion_string, $cloze_mch_keyword1) or contains($cloze_subquestion_string, $cloze_mch_keyword2) or contains($cloze_subquestion_string, $cloze_mcv_keyword1) or contains($cloze_subquestion_string, $cloze_mcv_keyword2)">
        <b>
            <xsl:call-template name="format_cloze_subquestion">
                <xsl:with-param name="cloze_subquestion_string" select="$cloze_remainder_string"/>
                <xsl:with-param name="first_answer_item" select="'true'"/>
                <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
                <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
            </xsl:call-template>
        </b>
    </xsl:when>
    </xsl:choose>
</xsl:template>

<!-- Clean up Cloze subquestion items by removing '%100%' or '=', empty feedback, etc. -->
<xsl:template name="format_cloze_subquestion">
    <xsl:param name="cloze_subquestion_string"/>
    <xsl:param name="first_answer_item" select="'false'"/>
    <xsl:param name="cloze_mc_formatting_flag"/>
    <xsl:param name="cloze_sa_casesensitive_flag"/>

    <xsl:if test="$first_answer_item = 'false'">
        <xsl:value-of select="$cloze_answer_delimiter"/>
    </xsl:if>

    <xsl:choose>
    <!-- Does the answer contain multiple components separated by tilda delimiter (~)? -->
    <xsl:when test="contains($cloze_subquestion_string, $cloze_answer_delimiter)">
        <!-- Format the answer before the ~ delimiter -->
        <xsl:call-template name="format_cloze_answer">
            <xsl:with-param name="cloze_subquestion_string" select="substring-before($cloze_subquestion_string, $cloze_answer_delimiter)"/>
            <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
            <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
        </xsl:call-template>

        <!-- Recurse to format the answers after the ~ delimiter -->
        <xsl:call-template name="format_cloze_subquestion">
            <xsl:with-param name="cloze_subquestion_string" select="substring-after($cloze_subquestion_string, $cloze_answer_delimiter)"/>
            <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
            <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
        </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
        <!-- Format the only answer -->
        <xsl:call-template name="format_cloze_answer">
            <xsl:with-param name="cloze_subquestion_string" select="$cloze_subquestion_string"/>
            <xsl:with-param name="cloze_sa_casesensitive_flag" select="$cloze_sa_casesensitive_flag"/>
            <xsl:with-param name="cloze_mc_formatting_flag" select="$cloze_mc_formatting_flag"/>
        </xsl:call-template>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Clean up answer string Cloze inside subquestion -->
<xsl:template name="format_cloze_answer">
    <xsl:param name="cloze_subquestion_string"/>
    <xsl:param name="cloze_mc_formatting_flag"/>
    <xsl:param name="cloze_sa_casesensitive_flag"/>

    <xsl:choose>
    <!-- If an answer starts with '%100%', leave it out -->
    <xsl:when test="starts-with($cloze_subquestion_string, $cloze_correct_prefix1)">
        <xsl:value-of select="substring-after($cloze_subquestion_string, $cloze_correct_prefix1)" disable-output-escaping="yes"/>
    </xsl:when>
    <!-- If an answer starts with '=', leave it out -->
    <xsl:when test="starts-with($cloze_subquestion_string, $cloze_correct_prefix2)">
        <xsl:value-of select="substring-after($cloze_subquestion_string, $cloze_correct_prefix2)" disable-output-escaping="yes"/>
    </xsl:when>
    <!-- If an answer has an incorrect prefix ('%0%'), just copy it -->
    <xsl:when test="starts-with($cloze_subquestion_string, $cloze_incorrect_prefix)">
        <xsl:value-of select="$cloze_subquestion_string" disable-output-escaping="yes"/>
    </xsl:when>
    <!-- If an answer has a partially correct prefix (i.e. starts with '%', but not '%0%' or '%100%', just copy it -->
    <xsl:when test="starts-with($cloze_subquestion_string, $cloze_incorrect_prefix)">
        <xsl:value-of select="$cloze_subquestion_string" disable-output-escaping="yes"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:if test="contains(., '&amp;')">
            <xsl:message>
                <xsl:value-of select="concat('String: ', .)"/>
            </xsl:message>
        </xsl:if>

        <!-- Add explicit incorrect marker '%0%' if it is not present -->
        <xsl:value-of select="concat($cloze_incorrect_prefix, $cloze_subquestion_string)" disable-output-escaping="yes"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>


<!-- Get the default mark in Cloze subquestions; it must be the same for all subquestions or we cannot optimise the Word output -->
<xsl:template name="get_cloze_defaultmark">
    <xsl:param name="cloze_questiontext_string"/>
    <xsl:param name="current_defaultmark" select="'0'"/>

    <!-- This template assumes there is always be a subquestion to process -->
    <!--
    <xsl:if test="$current_defaultmark = '0' and $cloze_questiontext_string != ''">
        <xsl:message>
            <xsl:value-of select="concat('get_cloze_defaultmark: starting string = &quot;', translate(substring(normalize-space($cloze_questiontext_string), 1, 100), '&#x0a;', ''), '&quot;')"/>
        </xsl:message>
    </xsl:if>
    -->

    <!-- Get the default mark for the first subquestion -->
    <xsl:variable name="defaultmark_string" select="substring-before(substring-after($cloze_questiontext_string, $cloze_start_delimiter), $cloze_keyword_delimiter)"/>
    <xsl:variable name="this_defaultmark">
        <xsl:choose>
        <xsl:when test="$defaultmark_string != ''">
            <xsl:value-of select="$defaultmark_string"/>
        </xsl:when>
        <!-- Default mark not explicitly specified, so use Moodle-defined default of 1 -->
        <xsl:otherwise><xsl:text>1</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!--
    <xsl:message>
        <xsl:value-of select="concat('get_cloze_defaultmark: this_defaultmark = ', $this_defaultmark, ', defaultmark_string = ', $defaultmark_string, '; cloze_questiontext_string: &quot;', translate(substring(normalize-space(substring-after($cloze_questiontext_string, $cloze_start_delimiter)), 1, 30), '&#x0a;', ''), '&quot;')"/>
    </xsl:message>
    -->

    <!-- Are there more subquestions? Check the remainder of the string after the end of the first subquestion to find out -->
    <xsl:variable name="cloze_questiontext_remainder" select="substring-after($cloze_questiontext_string, $cloze_end_delimiter)"/>
    <xsl:choose>
    <xsl:when test="contains($cloze_questiontext_remainder, $cloze_start_delimiter)">
        <!-- Yes, more subquestions, so recurse to the next one, passing this defaultmark value through as the current default -->
        <xsl:call-template name="get_cloze_defaultmark">
            <xsl:with-param name="cloze_questiontext_string" select="$cloze_questiontext_remainder"/>
            <xsl:with-param name="current_defaultmark" select="$this_defaultmark"/>
        </xsl:call-template>
    </xsl:when>
    <xsl:when test="$current_defaultmark = 0 or $current_defaultmark = $this_defaultmark">
        <!-- No more subquestions, so check the default mark for this subquestion against the current value to decide what to return -->
        <!-- If current default mark is 0, just return this default mark, since this is the first recursive call -->
        <xsl:value-of select="$this_defaultmark"/>
    </xsl:when>
    <xsl:when test="$current_defaultmark != $this_defaultmark">
        <!-- If current and this default marks are not the same, return 0 to indicate that different subquestions have different marks, because it means we cannot optimise Word output -->
        <xsl:value-of select="'0'"/>
    </xsl:when>
    </xsl:choose>
</xsl:template>

<!-- Handle Missing Words-specific question elements -->
<xsl:template match="selectoption">
    <tr>
        <td style="width: 1.0cm"><p class="MsoListNumber"><xsl:value-of select="$blank_cell"/></p></td>
        <td style="{$col2_width}"><p class="Cell"><xsl:value-of select="normalize-space(text)"/></p></td>
        <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
        <td style="width: 1.0cm"><p class="QFGrade"><xsl:value-of select="group"/></p></td>
    </tr>
</xsl:template>

<!-- Handle Drag and Drop text question elements -->
<xsl:template match="dragbox">
    <tr>
        <td style="width: 1.0cm"><p class="MsoListNumber"><xsl:value-of select="$blank_cell"/></p></td>
        <td style="{$col2_width}"><p class="Cell"><xsl:value-of select="normalize-space(text)"/></p></td>
        <td style="{$col3_width}">
            <p class="Cell">
                <xsl:choose>
                <xsl:when test="infinite">
                    <xsl:value-of select="$yes_label"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$no_label"/>
                </xsl:otherwise>
                </xsl:choose>
            </p>
        </td>
        <td style="width: 1.0cm"><p class="QFGrade"><xsl:value-of select="group"/></p></td>
    </tr>
</xsl:template>

<!-- Handle Drag and Drop onto image question elements -->
<xsl:template match="drag">
    <tr>
        <td style="width: 1.0cm"><p class="MsoListNumber"><xsl:value-of select="$blank_cell"/></p></td>
        <td style="{$col2_width}">
            <p class="Cell">
                <!-- May be dealing with either an image or a text label -->
                <xsl:choose>
                <xsl:when test="file">
                    <xsl:variable name="image_id">
                        <xsl:value-of select="'Q'"/>
                        <xsl:number value="count(preceding::question) + 1" format="0001"/>
                        <xsl:value-of select="'_IID'"/>
                        <xsl:number value="position()" format="0001"/>
                    </xsl:variable>
                    <img id="{$image_id}" src="{concat($pluginfiles_string,file/@name)}" alt="{normalize-space(text)}"/>
                    <xsl:apply-templates select="file">
                        <xsl:with-param name="image_id" select="$image_id"/>
                    </xsl:apply-templates>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="normalize-space(text)"/>
                </xsl:otherwise>
                </xsl:choose>
            </p>
        </td>
        <td style="{$col3_width}">
            <p class="Cell">
                <xsl:choose>
                <xsl:when test="infinite">
                    <xsl:value-of select="$yes_label"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$no_label"/>
                </xsl:otherwise>
                </xsl:choose>
            </p>
        </td>
        <td style="width: 1.0cm"><p class="QFGrade"><xsl:value-of select="draggroup"/></p></td>
    </tr>
</xsl:template>

<!-- Markers area of Drag and Drop marker -->
<xsl:template match="drag" mode="ddmarker">
    <tr>
        <td style="width: 1.0cm"><p class="MsoListNumber"><xsl:value-of select="$blank_cell"/></p></td>
        <td style="{$col2_width}"><p class="Cell"><xsl:value-of select="normalize-space(text)"/></p></td>
        <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="$blank_cell"/></p></td>
        <td style="width: 1.0cm">
            <p class="Cell">
                <xsl:choose>
                <xsl:when test="infinite"><xsl:value-of select="'0'"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="noofdrags"/></xsl:otherwise>
                </xsl:choose>
            </p>
        </td>
    </tr>
</xsl:template>

<xsl:template match="drop" mode="ddmarker">
    <tr>
        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="no"/></p></td>
        <td style="{$col2_width}">
            <p class="Cell">
                <xsl:variable name="this_shape">
                    <xsl:choose>
                    <xsl:when test="shape = 'circle'">
                        <xsl:value-of select="$ddm_circle_label"/>
                    </xsl:when>
                    <xsl:when test="shape = 'rectangle'">
                        <xsl:value-of select="$ddm_rectangle_label"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="$ddm_polygon_label"/>
                    </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <xsl:value-of select="$this_shape"/>
            </p>
        </td>
        <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="coords"/></p></td>
        <td style="width: 1.0cm"><p class="QFGrade"><xsl:value-of select="choice"/></p></td>
    </tr>
</xsl:template>

<xsl:template match="drop" mode="ddimageortext">
    <tr>
        <td style="width: 1.0cm"><p class="Cell"><xsl:value-of select="no"/></p></td>
        <td style="{$col2_width}"><p class="Cell"><xsl:value-of select="normalize-space(text)"/></p></td>
        <td style="{$col3_width}"><p class="Cell"><xsl:value-of select="concat(xleft, ', ', ytop)"/></p></td>
        <td style="width: 1.0cm"><p class="QFGrade"><xsl:value-of select="choice"/></p></td>
    </tr>
</xsl:template>

<!-- Handle images associated with '@@PLUGINFILE@@' keyword by including them in temporary supplementary paragraphs in whatever component they occur in -->
<xsl:template match="file">
    <xsl:param name="image_id"/>

    <xsl:variable name="image_file_suffix">
        <xsl:value-of select="translate(substring-after(@name, '.'), $ucase, $lcase)"/>
    </xsl:variable>
    <xsl:variable name="image_format">
        <xsl:value-of select="concat('data:image/', $image_file_suffix, ';', @encoding, ',')"/>
    </xsl:variable>
    <xsl:variable name="alt_text">
        <xsl:value-of select="concat('data:image/', $image_file_suffix, ';', @encoding, ',')"/>
    </xsl:variable>

    <div class="ImageFile">
        <img title="{@name}" src="{concat($image_format, .)}">
            <xsl:if test="$image_id != ''">
                <xsl:attribute name="id"><xsl:value-of select="$image_id"/></xsl:attribute>
            </xsl:if>
        </img>
    </div>
</xsl:template>

<!-- got to preserve comments for style definitions -->
<xsl:template match="comment()">
    <xsl:comment><xsl:value-of select="."/></xsl:comment>
</xsl:template>

<!-- Identity transformations -->
<xsl:template match="*">
    <xsl:element name="{name()}">
        <xsl:call-template name="copyAttributes" />
        <xsl:apply-templates select="node()"/>
    </xsl:element>
</xsl:template>

<xsl:template name="copyAttributes">
    <xsl:for-each select="@*">
        <xsl:attribute name="{name()}"><xsl:value-of select="."/></xsl:attribute>
    </xsl:for-each>
</xsl:template>

<!-- Include debugging information in the output -->
<xsl:template name="debugComment">
    <xsl:param name="comment_text"/>
    <xsl:param name="inline" select="'false'"/>
    <xsl:param name="condition" select="'true'"/>

    <xsl:if test="boolean($condition) and $debug_flag != 0">
        <xsl:if test="$inline = 'false'"><xsl:text>&#x0a;</xsl:text></xsl:if>
        <xsl:comment><xsl:value-of select="concat('Debug: ', $comment_text)"/></xsl:comment>
        <xsl:if test="$inline = 'false'"><xsl:text>&#x0a;</xsl:text></xsl:if>
    </xsl:if>
</xsl:template>
</xsl:stylesheet>
