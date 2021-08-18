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

 * XSLT stylesheet to wrap questions formatted as HTML tables with a Word-compatible wrapper that defines the styles, metadata, etc.
 *
 * @package    qformat_wordtable
 * @copyright  2010-2015 Eoin Campbell
 * @author     Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
-->
<xsl:stylesheet exclude-result-prefixes="htm o w"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:w="urn:schemas-microsoft-com:office:word"
    xmlns:htm="http://www.w3.org/1999/xhtml"
    xmlns="http://www.w3.org/1999/xhtml"
    version="1.0">

<xsl:param name="course_name"/>
<xsl:param name="course_id"/>
<xsl:param name="author_name"/>
<xsl:param name="author_id"/>
<xsl:param name="institution_name"/>
<xsl:param name="moodle_country"/> <!-- Users country -->
<xsl:param name="moodle_language" select="'en'"/> <!-- Interface language for user -->
<xsl:param name="moodle_textdirection"/> <!-- Current text direction ltr or rtl -->
<xsl:param name="moodle_release"/>  <!-- 1.9 or 2.x -->
<xsl:param name="moodle_url"/>      <!-- Location of Moodle site -->
<xsl:param name="moodle_username"/> <!-- Username for login -->
<xsl:param name="debug_flag" select="'0'"/>      <!-- Debugging on or off -->

<xsl:variable name="ucase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />
<xsl:variable name="lcase" select="'abcdefghijklmnopqrstuvwxyz'" />
<xsl:variable name="pluginfiles_string" select="'@@PLUGINFILE@@/'"/>
<xsl:variable name="embeddedimagedata_string" select="'data:image/'"/>
<xsl:variable name="base64data_string" select="';base64,'"/>

<xsl:output method="xml" version="1.0" omit-xml-declaration="yes" encoding="ISO-8859-1" indent="yes" />

<!-- Text labels from translated Moodle files -->
<xsl:variable name="moodle_labels" select="/container/moodlelabels"/>
<!-- Word-compatible XHTML template into which the questions are inserted -->
<xsl:variable name="htmltemplate" select="/container/htmltemplate" />
<!-- Throw away the extra wrapper elements, now we've read them into variables -->
<xsl:template match="/container/moodlelabels"/>
<xsl:template match="/container/htmltemplate"/>

<!-- Read in the input XML into a variable, and handle unusual situation where the inner container element doesn't have an explicit namespace declaration  -->
<xsl:variable name="data" select="/container/*[local-name() = 'container']" />
<xsl:variable name="contains_embedded_images" select="count($data//htm:img[contains(@src, $base64data_string)])"/>

<xsl:variable name="transformationfailed" select="$moodle_labels/data[@name = 'qformat_wordtable_transformationfailed']"/>

<!-- Get the locale if present as part of the language definition (e.g. zh_cn) -->
<xsl:variable name="moodle_language_locale">
    <xsl:if test="contains($moodle_language, '_')">
        <xsl:value-of select="translate(substring-after($moodle_language, '_'), $lcase, $ucase)"/>
    </xsl:if>
</xsl:variable>

<!-- Map Moodle language into a Word-compatible version, removing anything after an underscore and capitalising -->
<xsl:variable name="word_language">
    <xsl:choose>
    <xsl:when test="contains($moodle_language, '_')">
        <xsl:value-of select="translate(substring-before($moodle_language, '_'), $lcase, $ucase)"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="translate($moodle_language, $lcase, $ucase)"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- Guess a suitable Word locale based on the users location, the location of the Moodle server, or the Moodle language locale -->
<xsl:variable name="word_locale">
    <xsl:choose>
    <xsl:when test="$word_language = 'EN' and ($moodle_country = 'AU' or $moodle_country = 'CA' or $moodle_country = 'GB' or $moodle_country = 'IE' or $moodle_country = 'IN' or $moodle_country = 'NZ')">
        <xsl:value-of select="$moodle_country"/>
    </xsl:when>
    <xsl:when test="$word_language = 'EN'">
        <xsl:value-of select="'US'"/>
    </xsl:when>
    <xsl:when test="$word_language = 'FR' and ($moodle_country = 'BE' or $moodle_country = 'CH')">
        <xsl:value-of select="$moodle_country"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$moodle_language_locale"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:variable>

<!-- Assemble the language-locale combination for Word style template language settings (for spellchecking) -->
<xsl:variable name="word_language_and_locale">
    <xsl:choose>
    <xsl:when test="$word_locale != ''">
        <xsl:value-of select="concat($word_language, '-', $word_locale)"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$word_language"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<!-- Does the language use CSS property 'mso-fareast-language' in Word? -->
<xsl:variable name="word_language_fareast">
    <xsl:if test="$word_language = 'JA' or $word_language = 'KO' or $word_language = 'ZH'">
        <xsl:value-of select="'true'"/>
    </xsl:if>
</xsl:variable>

<!-- Match document root node, and read in and process Word-compatible XHTML template -->
<xsl:template match="/">
<!-- Set the language and text direction -->
    <html lang="{$word_language_and_locale}" dir="{$moodle_textdirection}">
        <xsl:apply-templates select="$htmltemplate/htm:html/*" />
    </html>
</xsl:template>

<!-- Place questions in XHTML template body -->
<xsl:template match="processing-instruction('replace')[.='insert-content']">
    <xsl:comment>Institution: <xsl:value-of select="$institution_name"/></xsl:comment>
    <xsl:comment>Moodle language: <xsl:value-of select="$moodle_language"/></xsl:comment>
    <xsl:comment>Moodle URL: <xsl:value-of select="$moodle_url"/></xsl:comment>
    <xsl:comment>Course name: <xsl:value-of select="$course_name"/></xsl:comment>
    <xsl:comment>Course ID: <xsl:value-of select="$course_id"/></xsl:comment>
    <xsl:comment>Author name: <xsl:value-of select="$author_name"/></xsl:comment>
    <xsl:comment>Author ID: <xsl:value-of select="$author_id"/></xsl:comment>
    <xsl:comment>Author username: <xsl:value-of select="$moodle_username"/></xsl:comment>
    <xsl:comment>Contains embedded images: <xsl:value-of select="$contains_embedded_images"/></xsl:comment>
    
    <!-- Handle the question tables -->
    <xsl:apply-templates select="$data/htm:html/htm:body/*"/>
    <!-- Check that the content has been successfully read in: if the title is empty, include an error message in the Word file rather than leave it blank -->
    <xsl:if test="$data/htm:html/htm:head/htm:title = ''">
        <p class="MsoTitle"><xsl:value-of disable-output-escaping="yes" select="$transformationfailed"/></p>
    </xsl:if>

    <!-- Add a table for images, if present -->
    <xsl:if test="$contains_embedded_images != 0">
        <table border="1" style="display:none;"><thead>
        <tr><td colspan="7"><p class="Cell">&#160;</p></td><td><p class="QFType">Images</p></td></tr>
        <tr><td><p class="TableHead">ID</p></td><td><p class="TableHead">Name</p></td><td><p class="TableHead">Width</p></td><td><p class="TableHead">Height</p></td><td><p class="TableHead">Alt</p></td><td><p class="TableHead">Format</p></td><td><p class="TableHead">Encoding</p></td><td><p class="TableHead">Data</p></td></tr>
        </thead>
        <tbody>
            <!-- Get images exported from Moodle 2.x as file elements -->
            <xsl:for-each select="$data//htm:img[contains(@src, $pluginfiles_string)]">
                <!--<xsl:message><xsl:value-of select="concat('ImageTable:', @src)"/></xsl:message>-->
                <xsl:apply-templates select="." mode="ImageTable"/>
            </xsl:for-each>
            <!-- Get images imported from Word2XML conversion process as embedded base64 images -->
            <xsl:for-each select="$data//htm:img[starts-with(@src, $embeddedimagedata_string)]">
                <xsl:if test="not(ancestor::htm:div/@class = 'ImageFile')">
                    <xsl:apply-templates select="." mode="ImageTable"/>
                </xsl:if>
            </xsl:for-each>
        </tbody>
        </table>
    </xsl:if>
</xsl:template>

<!-- Metadata -->
<!-- Set the title property (File->Properties... Summary tab) -->
<xsl:template match="processing-instruction('replace')[.='insert-title']">
    <!-- Place category info and course name into document title -->
    <xsl:value-of select="$data/htm:html/htm:head/htm:title"/>
</xsl:template>

<!-- Set the author property -->
<xsl:template match="processing-instruction('replace')[.='insert-author']">
    <xsl:value-of select="$author_name"/>
</xsl:template>

<xsl:template match="processing-instruction('replace')[.='insert-meta']">
    <!-- Include custom properties used by Moodle2Word Startup Word template and re-import code -->
    <o:DC.Type><xsl:value-of select="'Question'"/></o:DC.Type>
    <o:moodleQuestionSeqNum><xsl:value-of select="count($data//htm:table) + 1"/></o:moodleQuestionSeqNum>
    <o:moodleCourseID><xsl:value-of select="$course_id"/></o:moodleCourseID>
    <o:moodleImages><xsl:value-of select="$contains_embedded_images"/></o:moodleImages>
    <o:moodleLanguage><xsl:value-of select="$moodle_language"/></o:moodleLanguage>
    <o:moodleRelease><xsl:value-of select="$moodle_release"/></o:moodleRelease>
    <o:moodleURL><xsl:value-of select="$moodle_url"/></o:moodleURL>
    <o:moodleUsername><xsl:value-of select="$moodle_username"/></o:moodleUsername>
</xsl:template>

<xsl:template match="processing-instruction('replace')[.='insert-language']">
    <!-- Set the language of each style to be whatever is defined in Moodle, to assist spell-checking -->

    <xsl:choose>
    <!-- For far-eastern languages, use the mso-fareast-language property -->
    <xsl:when test="$word_language_fareast = 'true'">
        <xsl:value-of select="concat('EN-GB;mso-fareast-language:', $word_language_and_locale)"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$word_language_and_locale"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template match="processing-instruction('replace')[.='insert-language-direction']">
    <!-- Set the language and text direction of the Word Normal style -->

    <xsl:choose>
    <!-- For Right-to-Left languages, use the mso-bidi-language property -->
    <xsl:when test="$moodle_textdirection = 'rtl'">
        <xsl:value-of select="concat('EN-GB;&#x0a;mso-bidi-language:', $word_language_and_locale, ';&#x0a;direction:rtl')"/>
    </xsl:when>
    <!-- For far-eastern languages, use the mso-fareast-language property -->
    <xsl:when test="$word_language_fareast = 'true'">
        <xsl:value-of select="concat('EN-GB;mso-fareast-language:', $word_language_and_locale)"/>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$word_language_and_locale"/>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Look for table cells with just text, and wrap them in a Cell paragraph style -->
<xsl:template match="htm:td">
    <xsl:value-of select="'&#x0a;'"/> <!-- Start each cell on a new line to simplify PHPUnit tests -->
    <td>
        <xsl:call-template name="copyAttributes"/>
        <xsl:choose>
        <xsl:when test="count(*) = 0">
            <p class="Cell">
                <xsl:apply-templates/>
            </p>
        </xsl:when>
        <xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
        </xsl:choose>
    </td>
</xsl:template>

<xsl:template match="htm:th">
    <xsl:value-of select="'&#x0a;'"/> <!-- Start each cell on a new line to simplify PHPUnit tests -->
    <th>
        <xsl:call-template name="copyAttributes"/>
        <xsl:apply-templates/>
    </th>
</xsl:template>

<!-- Any paragraphs without an explicit class are set to have the Cell style -->
<xsl:template match="htm:p[not(@class)]">
    <p class="Cell">
        <xsl:call-template name="copyAttributes"/>
        <xsl:apply-templates/>
    </p>
</xsl:template>

<!-- Handle a hyperlinked img element -->
<xsl:template match="htm:a[htm:img]" priority="3">
    <xsl:call-template name="debugComment">
        <xsl:with-param name="comment_text" select="concat('hyperlinked a/@href = ', @href , '; a/img/@src = ', htm:img/@src)"/>
        <xsl:with-param name="inline" select="'true'"/>
        <xsl:with-param name="condition" select="$debug_flag = '2'"/>
    </xsl:call-template>


    <xsl:choose>
    <xsl:when test="contains(htm:img/@src, $pluginfiles_string) or contains(htm:img/@src, $embeddedimagedata_string)">
        <!-- Place the hyperlink anchor inside the bookmark anchor -->
        <xsl:apply-templates select="htm:img" mode="linkedImage"/>
    </xsl:when>
    <xsl:otherwise>
        <a>
            <xsl:call-template name="copyAttributes"/>
            <xsl:apply-templates/>
        </a>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Handle a hyperlinked img element -->
<xsl:template match="htm:img" mode="linkedImage">
    <!-- Place the hyperlink anchor inside the bookmark anchor -->
    <xsl:variable name="bookmark_name">
        <xsl:value-of select="'MQIMAGE_Q'"/>
        <xsl:number value="count(preceding::htm:div[@class = 'TableDiv']) + 1" format="0001"/>
        <xsl:value-of select="'_IID'"/>
        <xsl:choose>
        <xsl:when test="@id">
            <xsl:value-of select="@id"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:number value="count(preceding::htm:img) + 1" format="0001"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <a name="{$bookmark_name}" style="color:red;"/>
    <a href="{../@href}">
        <span style="{concat('mso-bookmark:', $bookmark_name)}">x</span>
    </a>
</xsl:template>

<!-- Handle the img element within the main component text by replacing it with a bookmark as a placeholder -->
<xsl:template match="htm:img" priority="2">
    <xsl:variable name="bookmark_name">
        <xsl:value-of select="'MQIMAGE_Q'"/>
        <xsl:number value="count(preceding::htm:div[@class = 'TableDiv']) + 1" format="0001"/>
        <xsl:value-of select="'_IID'"/>
        <xsl:choose>
        <xsl:when test="@id">
            <xsl:value-of select="@id"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:number value="count(preceding::htm:img) + 1" format="0001"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:choose>
    <xsl:when test="contains(@src, $pluginfiles_string) or contains(@src, $embeddedimagedata_string)">
        <!-- Generated from Moodle 2.x, so images are handled neatly, using a reference to the data -->
        <!-- If imported from Word2MQXML, images are base64-encoded into the @src attribute -->
        <a name="{$bookmark_name}" style="color:red;">x</a>
    </xsl:when>
    <xsl:otherwise>
        <img>
            <xsl:call-template name="copyAttributes"/>
        </img>
    </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- Create a row in the embedded image table with all image metadata -->
<xsl:template match="htm:img" mode="ImageTable">
    <xsl:variable name="image_id">
        <xsl:value-of select="'Q'"/>
        <xsl:number value="count(preceding::htm:div[@class = 'TableDiv']) + 1" format="0001"/>
        <xsl:value-of select="'_IID'"/>
        <xsl:choose>
        <xsl:when test="@id and @id != ''">
            <xsl:value-of select="@id"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:number value="count(preceding::htm:img) + 1" format="0001"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <!-- Get image name. If 'PLUGINFILES' not present, the image is embedded in the text, i.e. <img src="data:image/gif;base64,{base64 data}"/> -->
    <xsl:variable name="raw_image_file_name" select="substring-after(@src, $pluginfiles_string)"/>
    <xsl:variable name="image_file_name">
        <xsl:choose>
        <xsl:when test="contains($raw_image_file_name, '%')">
            <xsl:call-template name="url-decode">
                <xsl:with-param name="str" select="$raw_image_file_name"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$raw_image_file_name"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="image_data">
        <xsl:choose>
        <xsl:when test="contains(@src, $pluginfiles_string)">
            <!-- Image exported from Moodle 2.x, i.e. 
                 <img src="@@PLUGINFILE@@/filename.gif"/> <file name="filename.gif" encoding="base64">{base64 data}</file> -->
            <xsl:value-of select="substring-after(ancestor::htm:td//htm:div[@class = 'ImageFile' and htm:img/@title = $image_file_name]/htm:img/@src, ',')"/>
        </xsl:when>
        <xsl:when test="contains(@src, $embeddedimagedata_string)">
            <!-- Image embedded in text as it was imported using Word2MQXML, i.e. <img src="data:image/gif;base64,{base64 data}"/> -->
            <xsl:value-of select="substring-after(@src, $base64data_string)"/>
        </xsl:when>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="image_format">
        <xsl:choose>
        <xsl:when test="contains(@src, $pluginfiles_string)">
            <!-- Image exported from Moodle 2.x, i.e. 
                 <img src="@@PLUGINFILE@@/filename.gif"/> <file name="filename.gif" encoding="base64">{base64 data}</file> -->
            <xsl:value-of select="substring-after(substring-before(ancestor::htm:td//htm:div[@class = 'ImageFile' and htm:img/@title = $image_file_name]/htm:img/@src, ';'), 'data:image/')"/>
        </xsl:when>
        <xsl:when test="contains(@src, $embeddedimagedata_string)">
            <!-- Image embedded in text as it was imported using Word2MQXML, i.e. <img src="data:image/gif;base64,{base64 data}"/> -->
            <xsl:value-of select="substring-before(substring-after(@src, $embeddedimagedata_string), ';')"/>
        </xsl:when>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="image_encoding">
        <xsl:choose>
        <xsl:when test="contains(@src, $pluginfiles_string)">
            <xsl:value-of select="substring-after(substring-before(ancestor::htm:td//htm:div[@class = 'ImageFile' and htm:img/@title = $image_file_name]/htm:img/@src, ','), ';')"/>
        </xsl:when>
        <xsl:otherwise> <!-- Always Base 64 -->
            <xsl:value-of select="'base64'"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:text>&#x0a;</xsl:text>
    <tr>
        <td><p class="Cell"><xsl:value-of select="$image_id"/></p></td>
        <td><p class="Cell"><xsl:value-of select="$image_file_name"/></p></td>
        <td><p class="Cell"><xsl:value-of select="@width"/></p></td>
        <td><p class="Cell"><xsl:value-of select="@height"/></p></td>
        <td><p class="Cell"><xsl:value-of select="@alt"/></p></td>
        <td><p class="Cell"><xsl:value-of select="$image_format"/></p></td>
        <td><p class="Cell"><xsl:value-of select="$image_encoding"/></p></td>
        <td><p class="Cell"><xsl:value-of select="$image_data"/></p></td>
    </tr>
</xsl:template>


<!-- Handle the @src attribute of images in the main component text -->
<xsl:template match="htm:img/@src">
    <xsl:variable name="raw_image_file_name" select="substring-after(., $pluginfiles_string)"/>
    <xsl:variable name="image_file_name">
        <xsl:choose>
        <xsl:when test="contains($raw_image_file_name, '%')">
            <xsl:call-template name="url-decode">
                <xsl:with-param name="str" select="$raw_image_file_name"/>
            </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$raw_image_file_name"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <xsl:variable name="image_name" select="ancestor::htm:td//htm:div[@class = 'ImageFile' and htm:img/@title = $image_file_name]/htm:img/@title"/>
    <xsl:variable name="image_data" select="ancestor::htm:td//htm:div[@class = 'ImageFile']/htm:img/@src"/>
    <xsl:variable name="image_format" select="substring-before(substring-after('data:image/', $image_data), ';')"/>
    <xsl:variable name="image_encoding" select="substring-after(substring-before(',', $image_data), ';')"/>

    <xsl:value-of select="$image_data"/>
</xsl:template>

<!-- Delete the supplementary paragraphs containing images within each question component, as they are no longer needed -->
<xsl:template match="htm:div[@class = 'ImageFile']"/>

<!-- Handle Bootstrap Alert component divs -->
<xsl:template match="htm:div[starts-with(@class, 'alert')]/htm:p">
    <xsl:variable name="alert_class">
        <xsl:choose>
        <xsl:when test="contains(../@class, 'alert-danger')">
            <xsl:value-of select="'Danger'"/>
        </xsl:when>
        <xsl:when test="contains(../@class, 'alert-info')">
            <xsl:value-of select="'Info'"/>
        </xsl:when>
        <xsl:when test="contains(../@class, 'alert-success')">
            <xsl:value-of select="'Success'"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="'Warning'"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:comment><xsl:value-of select="concat('../@class = ', @class, '; alert_class: ', $alert_class)"/></xsl:comment>
    <p class="{$alert_class}"><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="htm:div[starts-with(@class, 'alert')]">
    <xsl:variable name="alert_class">
        <xsl:choose>
        <xsl:when test="contains(@class, 'alert-danger')">
            <xsl:value-of select="'Danger'"/>
        </xsl:when>
        <xsl:when test="contains(@class, 'alert-info')">
            <xsl:value-of select="'Info'"/>
        </xsl:when>
        <xsl:when test="contains(@class, 'alert-success')">
            <xsl:value-of select="'Success'"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="'Warning'"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>
    <xsl:comment><xsl:value-of select="concat('@class = ', @class, '; alert_class: ', $alert_class)"/></xsl:comment>
    <p class="{$alert_class}"><xsl:apply-templates/></p>
</xsl:template>

<!-- Preserve comments for style definitions -->
<xsl:template match="comment()">
    <xsl:comment><xsl:value-of select="."  /></xsl:comment>
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

<!--
    ISO-8859-1 based URL-encoding demo
    Written by Mike J. Brown, mike@skew.org.
    Updated 2002-05-20.
 
    No license; use freely, but credit me if reproducing in print.
 
    Also see http://skew.org/xml/misc/URI-i18n/ for a discussion of
    non-ASCII characters in URIs.
 
Copied from: https://gist.github.com/nils-werner/721650
-->

<xsl:variable name="hex" select="'0123456789ABCDEF'"/>
<xsl:variable name="ascii"> !"#$%&amp;'()*+,-./0123456789:;&lt;=&gt;?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~</xsl:variable>
<xsl:variable name="safe">!'()*-.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz~</xsl:variable>
<xsl:variable name="latin1">&#160;&#161;&#162;&#163;&#164;&#165;&#166;&#167;&#168;&#169;&#170;&#171;&#172;&#173;&#174;&#175;&#176;&#177;&#178;&#179;&#180;&#181;&#182;&#183;&#184;&#185;&#186;&#187;&#188;&#189;&#190;&#191;&#192;&#193;&#194;&#195;&#196;&#197;&#198;&#199;&#200;&#201;&#202;&#203;&#204;&#205;&#206;&#207;&#208;&#209;&#210;&#211;&#212;&#213;&#214;&#215;&#216;&#217;&#218;&#219;&#220;&#221;&#222;&#223;&#224;&#225;&#226;&#227;&#228;&#229;&#230;&#231;&#232;&#233;&#234;&#235;&#236;&#237;&#238;&#239;&#240;&#241;&#242;&#243;&#244;&#245;&#246;&#247;&#248;&#249;&#250;&#251;&#252;&#253;&#254;&#255;</xsl:variable>

<xsl:template name="url-decode">
    <xsl:param name="str"/>

    <xsl:choose>
    <xsl:when test="contains($str,'%')">
        <xsl:value-of select="substring-before($str,'%')"/>
        <xsl:variable name="hexpair" select="translate(substring(substring-after($str,'%'),1,2),'abcdef','ABCDEF')"/>
        <xsl:variable name="decimal" select="(string-length(substring-before($hex,substring($hexpair,1,1))))*16 + string-length(substring-before($hex,substring($hexpair,2,1)))"/>
        <xsl:choose>
            <xsl:when test="$decimal &lt; 127 and $decimal &gt; 31">
                <xsl:value-of select="substring($ascii,$decimal - 31,1)"/>
            </xsl:when>
            <xsl:when test="$decimal &gt; 159">
                <xsl:value-of select="substring($latin1,$decimal - 159,1)"/>
            </xsl:when>
            <xsl:otherwise>?</xsl:otherwise>
        </xsl:choose>
        <xsl:call-template name="url-decode">
            <xsl:with-param name="str" select="substring(substring-after($str,'%'),3)"/>
        </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="$str"/>
    </xsl:otherwise>
    </xsl:choose>
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
