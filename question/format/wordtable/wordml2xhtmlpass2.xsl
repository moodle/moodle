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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.    If not, see <http://www.gnu.org/licenses/>.

 * XSLT stylesheet to transform rough XHTML derived from Word 2010 files into a more hierarchical format with divs wrapping each heading and table (question name and item)
 *
 * @package qformat_wordtable
 * @copyright 2010-2016 Eoin Campbell
 * @author Eoin Campbell
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
-->

<xsl:stylesheet
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:x="http://www.w3.org/1999/xhtml"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:mml="http://www.w3.org/1998/Math/MathML"
    xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"
    xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006"
    exclude-result-prefixes="x mc"
    version="1.0">
    <xsl:output method="xml" encoding="UTF-8" indent="no" omit-xml-declaration="yes"/>
    <xsl:preserve-space elements="x:span x:p"/>

    <xsl:param name="debug_flag" select="0"/>
    <xsl:param name="pluginname"/>
    <xsl:param name="course_id"/>
    <xsl:param name="heading1stylelevel"/> <!-- Should be 1 for Books and WordTable, 3 for Atto -->

    <!-- Figure out an offset by which to demote headings e.g. Heading 1  to H2, etc. -->
    <!-- Use a system default, or a document-specific override -->
    <xsl:variable name="moodleHeading1Level" select="/x:html/x:head/x:meta[@name = 'moodleHeading1Level']/@content"/>
    <xsl:variable name="heading_demotion_offset">
        <xsl:choose>
        <xsl:when test="$moodleHeading1Level != ''">
            <xsl:value-of select="$moodleHeading1Level - 1"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="$heading1stylelevel - 1"/>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:variable>

    <!-- Output a newline before paras and cells when debugging turned on -->
    <xsl:variable name="debug_newline">
        <xsl:if test="$debug_flag &gt;= 1">
            <xsl:value-of select="'&#x0a;'"/>
        </xsl:if>
    </xsl:variable>

    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>
    
    <!-- Start: Identity transformation -->
    <xsl:template match="*">
        <xsl:copy>
            <xsl:apply-templates select="@*"/>
            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>

    <xsl:template match="@*|comment()|processing-instruction()">
        <xsl:copy/>
    </xsl:template>
    <!-- End: Identity transformation -->
    
    <xsl:template match="text()">
        <xsl:value-of select="translate(., '&#x2009;', '&#x202f;')"/>
    </xsl:template>

    <!-- Remove empty class attributes -->
    <xsl:template match="@class[.='']"/>
    
    <!-- Omit superfluous MathML markup attributes -->
    <xsl:template match="@mathvariant"/>

    <!-- Remove redundant style information, retaining only borders and widths on table cells, and text direction in paragraphs-->
    <xsl:template match="@style[not(parent::x:table) and not(contains(., 'direction:'))]" priority="1"/>

     <!-- Delete superfluous spans that wrap the complete para content -->
    <xsl:template match="x:span[count(.//node()[self::x:span]) = count(.//node())]" priority="2"/>

    <!-- Out go horizontal bars -->
    <xsl:template match="x:p[@class='horizontalbar']"/>

    <!-- Convert i to em -->
    <xsl:template match="x:em[@class = 'italic']|x:i">
        <em>
            <xsl:apply-templates select="."/>
        </em>
    </xsl:template>

    <!-- Convert b or em/@class=bold to strong -->
    <xsl:template match="x:em[@class = 'bold']|x:b">
        <strong>
            <xsl:apply-templates select="."/>
        </strong>
    </xsl:template>

    <!-- For character level formatting - bold, italic, subscript, superscript - use semantic HTML rather than CSS styling -->
    <!-- Convert style properties inside span element to elements instead -->
    <xsl:template match="x:span[@style]">
        <xsl:apply-templates select="." mode="styleProperty">
            <xsl:with-param name="styleProperty" select="@style"/>
        </xsl:apply-templates>
    </xsl:template>

    <!-- Span elements that contain only the class attribute are usually used for named character styles like Hyperlink, Strong and Emphasis -->
    <xsl:template match="x:span[@class and count(@*) = 1]">
        <xsl:apply-templates select="." mode="styleProperty">
            <xsl:with-param name="styleProperty" select="concat(@class, ';')"/>
        </xsl:apply-templates>
    </xsl:template>

    <!-- Recursive loop to convert style properties inside span element to elements instead -->
    <xsl:template match="x:span" mode="styleProperty">
        <xsl:param name="styleProperty"/>

        <!-- Get the first property in the list -->
        <xsl:variable name="stylePropertyFirst">
            <xsl:choose>
            <xsl:when test="contains($styleProperty, ';')">
                <xsl:value-of select="substring-before($styleProperty, ';')"/>
            </xsl:when>
            <xsl:otherwise>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <!-- Get the remaining properties for passing on in recursive loop-->
        <xsl:variable name="stylePropertyRemainder">
            <xsl:choose>
            <xsl:when test="contains($styleProperty, ';')">
                <xsl:value-of select="substring-after($styleProperty, ';')"/>
            </xsl:when>
            <xsl:otherwise>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('$stylePropertyRemainder = ', $stylePropertyRemainder, '; $stylePropertyFirst = ', $stylePropertyFirst)"/>
            <xsl:with-param name="inline" select="'true'"/>
            <xsl:with-param name="condition" select="contains($styleProperty, '-H') and $debug_flag &gt;= 2"/>
        </xsl:call-template>
        <xsl:choose>
        <xsl:when test="$styleProperty = ''">
            <!-- No styles left, so just process the children in the normal way -->
            <xsl:apply-templates select="node()"/>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'color:#000000'">
            <!-- Omit spans that define text colour to black -->
            <xsl:apply-templates select="." mode="styleProperty">
                <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
            </xsl:apply-templates>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'color:#1155CC' and parent::x:a">
            <!-- Omit explicit text colour definition inside a hyperlink -->
            <xsl:apply-templates select="." mode="styleProperty">
                <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
            </xsl:apply-templates>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'font-weight:bold' or $stylePropertyFirst = 'Strong-H'">
            <!-- Convert bold style to strong element -->
            <strong>
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
            </strong>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'font-style:italic' or $stylePropertyFirst = 'Emphasis-H'">
            <!-- Convert italic style to emphasis element -->
            <em>
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
            </em>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'text-decoration:underline' and (@class = 'Hyperlink-H' or @class = 'hyperlink-h')">
            <!-- Ignore underline style if it is in a hyperlink-->
            <xsl:apply-templates select="." mode="styleProperty">
                <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
            </xsl:apply-templates>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'text-decoration:underline' and parent::x:a and contains(@style, 'color:#1155CC')">
            <!-- Ignore underline style if it is in a hyperlink-->
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'text-decoration:underline'">
            <!-- Convert underline style to u element -->
            <u>
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
            </u>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'vertical-align:super'">
            <!-- Only superscript style present so no need for further x:span processing, and omit x:span element -->
            <sup>
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
            </sup>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'vertical-align:sub'">
            <!-- Only subscript style present so no need for further x:span processing, and omit x:span element -->
            <sub>
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
            </sub>
        </xsl:when>
        <xsl:when test="starts-with($stylePropertyFirst, 'direction:')">
            <!-- Handle inline text direction directive-->
            <xsl:variable name="textDirection" select="substring-after($stylePropertyFirst, 'direction:')"/>
            <span dir="{$textDirection}">
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
            </span>
        </xsl:when>
        <xsl:when test="$stylePropertyFirst = 'font-size:smaller' or $stylePropertyFirst = 'font-size:11pt' or $stylePropertyFirst = 'font-size:12pt' or $stylePropertyFirst = 'font-size:13pt' or $stylePropertyFirst = 'font-style:normal' or $stylePropertyFirst = 'font-weight:normal' or $stylePropertyFirst = 'font-size:1pt' or $stylePropertyFirst = 'unicode-bidi:embed'">
            <!-- Ignore smaller font size style, as it is only in sub and superscripts; ignore some odd styles in Arabic samples -->
            <xsl:apply-templates select="." mode="styleProperty">
                <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
            </xsl:apply-templates>
        </xsl:when>
        <xsl:otherwise>
            <!-- Keep any remaining styles, such as strikethrough or font size changes, using a span element with a style attribute containing only those styles not already handled -->
            <!--<xsl:comment><xsl:value-of select="concat('$stylePropertyRemainder = ', $stylePropertyRemainder, '; $stylePropertyFirst = ', $stylePropertyFirst)"/></xsl:comment>-->
            <span>
                <xsl:for-each select="@*">
                    <xsl:choose>
                    <xsl:when test="name() = 'style'">
                        <xsl:attribute name="style">
                            <xsl:value-of select="$stylePropertyFirst"/>
                        </xsl:attribute>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:attribute name="{name()}">
                            <xsl:value-of select="."/>
                        </xsl:attribute>
                    </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
                <xsl:apply-templates select="." mode="styleProperty">
                    <xsl:with-param name="styleProperty" select="$stylePropertyRemainder"/>
                </xsl:apply-templates>
            </span>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="x:div[@class = 'level0']">
        <xsl:copy>
            <xsl:for-each select="@*[name() != 'style']">
                <xsl:apply-templates select="."/>
            </xsl:for-each>

            <xsl:apply-templates/>
        </xsl:copy>
    </xsl:template>
    
    <!-- Convert the Heading1 style into a <h1> element (i.e. question Category) -->
    <xsl:template match="x:p[@class = 'heading1']" priority="2">
        <div class="level1">
            <h1>
                <xsl:apply-templates select="node()"/>
            </h1>
        </div>
    </xsl:template>

    <!-- Convert the Heading2 style into a <h2> element (i.e. question Name), and wrap it and the following table into a div -->
    <xsl:template match="x:p[@class = 'heading2']" priority="2">
        <div class="level2">
            <h2>
                <xsl:apply-templates select="node()"/>
            </h2>

            <!-- Grab the next table following, and put it inside the same div, introducing a simple hierarchy to group the question name and body-->
            <xsl:apply-templates select="following::x:table[contains(@class, 'moodleQuestion')][1]" mode="moodleQuestion"/>
        </div>
    </xsl:template>

    <!-- Handle question tables in moodleQuestion mode, to wrap them inside a div with the previous heading 2 (question name) -->
    <xsl:template match="x:table[contains(@class, 'moodleQuestion')]" mode="moodleQuestion">
        <xsl:value-of select="$debug_newline"/>
        <table class="moodleQuestion">
            <xsl:apply-templates/>
        </table>
    </xsl:template>

    <!-- Delete question tables in normal processing, as they are grabbed by the previous heading 2 style -->
    <xsl:template match="x:table[contains(@class, 'moodleQuestion')]"/>

<!-- Handle simple unnested lists, as long as they use the explicit "List Number" or "List Bullet" styles -->

    <!-- Assemble numbered lists -->
    <xsl:template match="x:p[starts-with(@class, 'listnumber')]" priority="2">
        <xsl:if test="not(starts-with(preceding-sibling::x:p[1]/@class, 'listnumber'))">
            <!-- First item in a list, so wrap it in a ol, and drag in the rest of the items -->
            <ol>
                <li>
                    <xsl:apply-templates/>
                </li>

                <!-- Recursively process following paragraphs until we hit one that isn't a list item -->
                <xsl:apply-templates select="following-sibling::x:p[1]" mode="listItem">
                    <xsl:with-param name="listType" select="'listnumber'"/>
                </xsl:apply-templates>
            </ol>
        </xsl:if>
        <!-- Silently ignore the item if it is not the first -->
    </xsl:template>

    <!-- Assemble bullet lists -->
    <xsl:template match="x:p[starts-with(@class, 'listbullet')]" priority="2">
        <xsl:if test="not(starts-with(preceding-sibling::x:p[1]/@class, 'listbullet'))">
            <!-- First item in a list, so wrap it in a ul, and drag in the rest of the items -->
            <xsl:value-of select="$debug_newline"/>
            <ul>
                <xsl:value-of select="$debug_newline"/>
                <li>
                    <xsl:apply-templates/>
                </li>

                <!-- Recursively process following paragraphs until we hit one that isn't a list item -->
                <xsl:apply-templates select="following-sibling::x:p[1]" mode="listItem">
                    <xsl:with-param name="listType" select="'listbullet'"/>
                </xsl:apply-templates>
            </ul>
        </xsl:if>
        <!-- Silently ignore the item if it is not the first -->
    </xsl:template>

    <!-- Output a list item only if it has the right class -->
    <xsl:template match="x:p" mode="listItem">
        <xsl:param name="listType"/>

        <xsl:choose>
        <xsl:when test="starts-with(@class, $listType)">
            <xsl:value-of select="$debug_newline"/>
            <li>
                <xsl:apply-templates/>
            </li>
                <!-- Recursively process following paragraphs until we hit one that isn't a list item -->
                <xsl:apply-templates select="following-sibling::x:p[1]" mode="listItem">
                    <xsl:with-param name="listType" select="$listType"/>
                </xsl:apply-templates>
        </xsl:when>
        </xsl:choose>
    </xsl:template>

    <!-- Paragraphs -->
    <xsl:template match="x:p">
        <p>
            <!-- Keep text direction if specified -->
            <xsl:if test="contains(@style, 'direction:')">
                <xsl:attribute name="dir">
                    <xsl:value-of select="substring-before(substring-after(@style, 'direction:'), ';')"/>
                </xsl:attribute>
            </xsl:if>
            <!-- Keep text alignment if specified -->
            <xsl:if test="contains(@style, 'text-align:')">
                <xsl:attribute name="style">
                    <xsl:value-of select="concat('text-align:', substring-before(substring-after(@style, 'text-align:'), ';'))"/>
                </xsl:attribute>
            </xsl:if>

            <xsl:apply-templates select="node()"/>
        </p>
    </xsl:template>

    <!-- Preformatted text -->
    <xsl:template match="x:p[starts-with(@class, 'macro') or starts-with(@class, 'htmlpreformatted')]" priority="2">
        <xsl:variable name="paraClass" select="@class"/>
        <xsl:if test="not(starts-with(preceding-sibling::x:p[1]/@class, $paraClass))">
            <!-- First item in a sequence of preformatted text, so start a '<pre>', and pull in succeeding lines -->
            <xsl:value-of select="$debug_newline"/>
            <pre>
                <xsl:apply-templates/>
                <!-- Recursively process following paragraphs until we hit one that isn't a list item -->
                <xsl:apply-templates select="following-sibling::x:p[1]" mode="preformatted"/>
            </pre>
        </xsl:if>
        <!-- Silently ignore the item if it is not the first -->
    </xsl:template>

    <!-- Output another preformatted line only if it has the right class -->
    <xsl:template match="x:p" mode="preformatted">

        <xsl:choose>
        <xsl:when test="starts-with(@class, 'macro') or starts-with(@class, 'htmlpreformatted')">
            <xsl:value-of select="'&#x0a;'"/>
                <xsl:apply-templates/>
                <!-- Recursively process following paragraphs until we hit one that isn't a pre -->
                <xsl:apply-templates select="following-sibling::x:p[1]"  mode="preformatted"/>
        </xsl:when>
        </xsl:choose>
    </xsl:template>

    <!-- Delete any temporary ToC Ids to enable differences to be checked more easily, reduce clutter -->
    <xsl:template match="x:a[starts-with(translate(@name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '_toc') and @class = 'bookmarkStart' and count(@*) =3 and not(node())]" priority="4"/>
    <xsl:template match="x:a[@class = 'bookmarkStart' and count(@*) = 3 and not(node())]" priority="4"/>
    <!-- Delete any spurious OLE_LINK bookmarks that Word inserts -->
    <xsl:template match="x:a[starts-with(translate(@name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), 'ole_link') and @class = 'bookmarkStart']" priority="4"/>
    <xsl:template match="x:a[starts-with(translate(@name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '_goback') and @class = 'bookmarkStart']" priority="4"/>
    <xsl:template match="x:a[@class='bookmarkEnd' and not(node())]" priority="2"/>
    <xsl:template match="x:a[@href='\* MERGEFORMAT']" priority="2"/>

    <!-- Handle tables differently depending on the context (booktool, qformat) -->
    <xsl:template match="x:table">
        <!-- If in booktool and a table contains a h4 in the first heading cell, then it's a Case Study (for Kimmage DSC) -->
        <xsl:choose>
        <xsl:when test="x:thead/x:tr[1]/x:th[1]/x:p[1]/@class = 'heading4' and ($pluginname = 'booktool_wordimport' or $pluginname = 'atto_wordimport')">
            <div class="casestudy">
                <xsl:apply-templates select="x:thead/x:tr[1]/x:th[1]/x:p[@class = 'heading4']"/>
                <div class="whitebox">
                    <xsl:apply-templates select="x:tbody/x:tr[1]/x:td[1]/node()"/>
                </div>
            </div>
        </xsl:when>
        <xsl:otherwise>
            <table>
                <xsl:apply-templates select="@*"/>

                <!-- Check if a table has a title in the previous paragraph-->
                <xsl:if test="preceding-sibling::x:p[1]/@class = 'tabletitle'">
                    <caption>
                        <xsl:apply-templates select="preceding-sibling::x:p[1]" mode="tablecaption"/>
                    </caption>
                </xsl:if>

                <xsl:apply-templates/>
            </table>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Omit table titles, since they are included in the table itself-->
    <xsl:template match="x:p[@class = 'tabletitle']"/>
    <!-- Process the table titles as a caption-->
    <xsl:template match="x:p[@class = 'tabletitle']" mode="tablecaption">
        <xsl:apply-templates/>
    </xsl:template>

    <!-- Clean up table style so that border is either on or off -->
    <xsl:template match="x:table/@style" priority="2">
        <!-- Get the style of the 1st body cell in the 1st row of the table -->
        <xsl:variable name="tdStyle" select="../x:tbody/x:tr/x:td/@style"/>
        <!-- Get the style of the top border only -->
        <xsl:variable name="tdStyleBorder" select="substring-before(substring-after($tdStyle, 'border-top:'), ';')"/>
        <xsl:variable name="tdStyleBorderWidth" select="substring-after(substring-after($tdStyleBorder, ' '), ' ')"/>
        <xsl:variable name="tdStyleBorderType" select="substring-before($tdStyleBorder, ' ')"/>
        <!-- Get the 2nd item of the top border style settings, which is the color value -->
        <xsl:variable name="tdStyleBorderColor" select="substring-before(substring-after($tdStyleBorder, ' '), ' ')"/>

        <xsl:variable name="tableBorderStyleKeep">
            <xsl:choose>
            <!-- Remove negative indent on tables, so that the first column is not partially hidden-->
            <xsl:when test="contains(., 'margin-left:-')">
                    <xsl:value-of select="substring-before(., 'margin-left:-')"/>
            </xsl:when>
            <xsl:otherwise>
                    <xsl:value-of select="."/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="tableBorderColor">
            <xsl:choose>
            <!-- Replace windowtext with black-->
            <xsl:when test="$tdStyleBorderColor = 'windowtext'">
                    <xsl:value-of select="'black'"/>
            </xsl:when>
            <xsl:otherwise>
                    <xsl:value-of select="$tdStyleBorderColor"/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:attribute name="style">
            <xsl:value-of select="concat('cellpadding:1pt; border:', $tdStyleBorderType, ' ', $tableBorderColor, ' ', $tdStyleBorderWidth, '; ', $tableBorderStyleKeep)"/>
        </xsl:attribute>
    </xsl:template>

    <!-- Clean up cell styles to reduce verbosity -->
    <xsl:template match="x:td/@style|x:th/@style" priority="1"/>

    <!-- Handle table body explicitly, so that rows can be marked odd or even -->
    <xsl:template match="x:tbody">
        <tbody>
            <xsl:apply-templates select="x:tr"/>
        </tbody>
    </xsl:template>

    <!-- Mark table rows odd or even -->
    <xsl:template match="x:tbody/x:tr">
        <xsl:variable name="row_class">
            <xsl:choose>
            <xsl:when test="position() mod 2 = 1">
                <xsl:value-of select="'r0'"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="'r1'"/>
            </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <tr class="{$row_class}" style="vertical-align: text-top">
            <xsl:apply-templates/>
        </tr>
    </xsl:template>

    <!-- Convert table body cells containing headings into th's -->
    <xsl:template match="x:td[contains(x:p[1]/@class, 'tablerowhead')]">
        <xsl:value-of select="$debug_newline"/>
        <th>
            <xsl:apply-templates/>
        </th>
    </xsl:template>

    <!-- Process Figure captions, so that they can be explicitly styled -->
    <xsl:template match="x:p[@class = 'caption' or @class = 'MsoCaption']">
        <p class="figure-caption"><xsl:apply-templates/></p>
    </xsl:template>

    <!-- Process Bootstrap Alert components -->
    <xsl:template match="x:p[@class = 'danger' or @class = 'info' or @class = 'success' or @class = 'warning']">
        <div class="{concat('alert alert-', @class)}"><p><xsl:apply-templates/></p></div>
    </xsl:template>

    <!-- Strip out VML/drawingML markup from Word 2010 files (cf. http://officeopenxml.com/drwOverview.php)-->
    <xsl:template match="mc:AlternateContent|m:ctrlPr"/>

    <!-- Delete unused image, hyperlink and style info -->
    <xsl:template match="x:imageLinks|x:imagesContainer|x:styleMap|x:hyperLinks"/>

    <xsl:template match="@name[parent::x:a]">
        <xsl:attribute name="name">
            <xsl:value-of select="translate(., 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')"/>
        </xsl:attribute>
    </xsl:template>

    <!-- Include debugging information in the output -->
    <xsl:template name="debugComment">
        <xsl:param name="comment_text"/>
        <xsl:param name="inline" select="'false'"/>
        <xsl:param name="condition" select="'true'"/>

        <xsl:if test="boolean($condition) and $debug_flag &gt;= 1">
            <xsl:if test="$inline = 'false'"><xsl:text>&#x0a;</xsl:text></xsl:if>
            <xsl:comment><xsl:value-of select="concat('Debug: ', $comment_text)"/></xsl:comment>
            <xsl:if test="$inline = 'false'"><xsl:text>&#x0a;</xsl:text></xsl:if>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>