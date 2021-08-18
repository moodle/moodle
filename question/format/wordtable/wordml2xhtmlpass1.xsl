<xsl:stylesheet
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
    xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"
    xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" 
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
    xmlns:rels="http://schemas.openxmlformats.org/package/2006/relationships"
    xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:ve="http://schemas.openxmlformats.org/markup-compatibility/2006"
    xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml"
    xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
    xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint"
    xmlns:w10="urn:schemas-microsoft-com:office:word"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"

    xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
    xmlns:customProps="http://schemas.openxmlformats.org/officeDocument/2006/custom-properties"
    xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:dcterms="http://purl.org/dc/terms/"
    xmlns:dcmitype="http://purl.org/dc/dcmitype/"
    xmlns:mml="http://www.w3.org/1998/Math/MathML"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    exclude-result-prefixes="a cp dc dcterms dcmitype xsi o r v ve w wne wp wx w10 xs rels vt customProps"
    version="1.0">
    
    <!-- This stylesheet is adapted from code by Oleg Tkachenko. The original copyright notice
         is reproduced below -->
    <!--
        Copyright (c) 2004-2005, Oleg Tkachenko
        http://www.xmllab.net
        All rights reserved.

        Redistribution and use in source and binary forms, with or without 
        modification, are permitted provided that the following conditions are 
        met:

        1. Redistributions of source code must retain the above copyright 
             notice, this list of conditions and the following disclaimer. 
        2. Redistributions in binary form must reproduce the above copyright 
             notice, this list of conditions and the following disclaimer in 
             the documentation and/or other materials provided with the 
             distribution. 
        3. Neither the name of Oleg Tkachenko nor the names of its contributors
             may be used to endorse or promote products derived from this software 
             without specific prior written permission. 

        THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 
        "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT 
        LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS 
        FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE 
        COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, 
        INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
        BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS 
        OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
        AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
        OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF 
        THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH 
        DAMAGE.
    -->

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

 * XSLT stylesheet to transform WordProcessingML from Word 2010 files into linear XHTML format
 *
 * @copyright 2004-2005, Oleg Tkachenko
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
-->

    <xsl:include href="omml2mml.xsl"/>

    <xsl:param name="debug_flag" select="'0'"/>
    <xsl:param name="pluginname"/>
    <xsl:param name="moodle_language"/>
    <xsl:param name="moodle_textdirection"/>

    <xsl:output method="xml" encoding="utf-8" indent="no" omit-xml-declaration="yes"/>

    <xsl:variable name="paraStyleID_Default">Normal</xsl:variable>

    <xsl:variable name="charStyleSuffix">-H</xsl:variable>

    <xsl:variable name="paraMarginDefaultTop">0pt</xsl:variable>
    <xsl:variable name="paraMarginDefaultRight">0pt</xsl:variable>
    <xsl:variable name="paraMarginDefaultBottom">.0001pt</xsl:variable>
    <xsl:variable name="paraMarginDefaultLeft">0pt</xsl:variable>


    <xsl:variable name="cxtSpacing_all"></xsl:variable>
    <xsl:variable name="cxtSpacing_top">t</xsl:variable>
    <xsl:variable name="cxtSpacing_bottom">b</xsl:variable>
    <xsl:variable name="cxtSpacing_none">
        <xsl:value-of select="$cxtSpacing_top"/><xsl:value-of select="$cxtSpacing_bottom"/>
    </xsl:variable>


    <xsl:variable name="bdrSide_top">-top</xsl:variable>
    <xsl:variable name="bdrSide_right">-right</xsl:variable>
    <xsl:variable name="bdrSide_bottom">-bottom</xsl:variable>
    <xsl:variable name="bdrSide_left">-left</xsl:variable>
    <xsl:variable name="bdrSide_char"></xsl:variable>


    <xsl:variable name="prrFrame">1</xsl:variable>
    <xsl:variable name="prrDefaultCellpadding">2</xsl:variable>
    <xsl:variable name="prrCellspacing">3</xsl:variable>
    <xsl:variable name="prrBdrPr_top">4</xsl:variable>
    <xsl:variable name="prrBdrPr_right">5</xsl:variable>
    <xsl:variable name="prrBdrPr_bottom">6</xsl:variable>
    <xsl:variable name="prrBdrPr_left">7</xsl:variable>
    <xsl:variable name="prrBdrPr_between">8</xsl:variable>
    <xsl:variable name="prrBdrPr_bar">9</xsl:variable>
    <xsl:variable name="prrBdrPr_insideH">A</xsl:variable>
    <xsl:variable name="prrBdrPr_insideV">B</xsl:variable>
    <xsl:variable name="prrListSuff">C</xsl:variable>
    <xsl:variable name="prrListInd">D</xsl:variable>
    <xsl:variable name="prrApplyRPr">E</xsl:variable>
    <xsl:variable name="prrUpdateRPr">F</xsl:variable>
    <xsl:variable name="prrApplyTcPr">G</xsl:variable>
    <xsl:variable name="prrCustomCellpadding">H</xsl:variable>
    <xsl:variable name="prrCantSplit">I</xsl:variable>
    <xsl:variable name="prrTblInd">J</xsl:variable>
    <xsl:variable name="prrList">K</xsl:variable>
    <xsl:variable name="prrNonList">L</xsl:variable>


    <xsl:variable name="cnfFirstRow">firstRow</xsl:variable>
    <xsl:variable name="cnfLastRow">lastRow</xsl:variable>
    <xsl:variable name="cnfFirstCol">firstCol</xsl:variable>
    <xsl:variable name="cnfLastCol">lastCol</xsl:variable>
    <xsl:variable name="cnfBand1Vert">band1Vert</xsl:variable>
    <xsl:variable name="cnfBand2Vert">band2Vert</xsl:variable>
    <xsl:variable name="cnfBand1Horz">band1Horz</xsl:variable>
    <xsl:variable name="cnfBand2Horz">band2Horz</xsl:variable>
    <xsl:variable name="cnfNECell">neCell</xsl:variable>
    <xsl:variable name="cnfNWCell">nwCell</xsl:variable>
    <xsl:variable name="cnfSECell">seCell</xsl:variable>
    <xsl:variable name="cnfSWCell">swCell</xsl:variable>


    <xsl:variable name="icnfFirstRow">1</xsl:variable>
    <xsl:variable name="icnfLastRow">2</xsl:variable>
    <xsl:variable name="icnfFirstCol">3</xsl:variable>
    <xsl:variable name="icnfLastCol">4</xsl:variable>
    <xsl:variable name="icnfBand1Vert">5</xsl:variable>
    <xsl:variable name="icnfBand2Vert">6</xsl:variable>
    <xsl:variable name="icnfBand1Horz">7</xsl:variable>
    <xsl:variable name="icnfBand2Horz">8</xsl:variable>
    <xsl:variable name="icnfNECell">9</xsl:variable>
    <xsl:variable name="icnfNWCell">10</xsl:variable>
    <xsl:variable name="icnfSECell">11</xsl:variable>
    <xsl:variable name="icnfSWCell">12</xsl:variable>


    <xsl:variable name="off">0</xsl:variable>
    <xsl:variable name="on">1</xsl:variable>
    <xsl:variable name="na">2</xsl:variable>


    <xsl:variable name="sep">/</xsl:variable>
    <xsl:variable name="sep1">|</xsl:variable>
    <xsl:variable name="sep2">,</xsl:variable>


    <xsl:variable name="autoColor_hex">auto</xsl:variable>
    <xsl:variable name="autoColor_text">windowtext</xsl:variable>
    <xsl:variable name="autoColor_bg">transparent</xsl:variable>


    <xsl:variable name="transparentColor_hex">transparent</xsl:variable>
    <xsl:variable name="transparentColor_text">transparent</xsl:variable>
    <xsl:variable name="transparentColor_bg">transparent</xsl:variable>


    <xsl:variable name="prListSuff_space">Space</xsl:variable>
    <xsl:variable name="prListSuff_nothing">Nothing</xsl:variable>


    <xsl:variable name="hyperLinks" select="//documentLinks/rels:Relationships/*[contains(@Type, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink')]"/>
    <xsl:variable name="imageLinks" select="//documentLinks/rels:Relationships/*[contains(@Type, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image')]"/>
    <xsl:variable name="customProps" select="//customProps/*"/>
    <xsl:variable name="dublinCore" select="//dublinCore/cp:coreProperties"/>
    <xsl:variable name="imagesContainer" select="//imagesContainer"/>

    <xsl:variable name="nsStyles" select="//styleMap/w:styles[1]/w:style"/>
    <xsl:variable name="ndLists" select="//wordmlContainer/w:document[1]/w:lists[1]|//w:cfChunk/w:lists"/>
    <xsl:variable name="ndDocPr" select="//wordmlContainer/w:document[1]/w:docPr[1]"/>
    <xsl:variable name="ndDocInfo" select="//wordmlContainer/w:document[1]/w:docInfo[1]"/>
    <xsl:variable name="ndOfficeDocPr" select="//wordmlContainer/w:document[1]/o:DocumentProperties[1]"/>


    <xsl:variable name="lowercase" select="'abcdefghijklmnopqrstuvwxyz'" />
    <xsl:variable name="uppercase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />
    <!-- Output a newline before paras and cells when debugging turned on -->
    <xsl:variable name="debug_newline">
        <xsl:if test="$debug_flag &gt;= '1'">
            <xsl:value-of select="'&#x0a;'"/>
        </xsl:if>
    </xsl:variable>

    <xsl:variable name="pixelsPerInch">
        <xsl:choose>
            <xsl:when test="$ndDocPr/w:pixelsPerInch/@w:val">
                <xsl:value-of select="$ndDocPr/w:pixelsPerInch/@w:val"/>
            </xsl:when>
            <xsl:otherwise>96</xsl:otherwise>
        </xsl:choose>
    </xsl:variable>


    <xsl:variable name="nfcBullet">23</xsl:variable>


    <xsl:variable name="iEmbossImprint">1</xsl:variable>
    <xsl:variable name="iU_Em">2</xsl:variable>
    <xsl:variable name="iStrikeDStrike">3</xsl:variable>
    <xsl:variable name="iSup">4</xsl:variable>
    <xsl:variable name="iSub">5</xsl:variable>
    <xsl:variable name="iVanishWebHidden">6</xsl:variable>
    <xsl:variable name="iBCs">7</xsl:variable>
    <xsl:variable name="iICs">8</xsl:variable>
    <xsl:variable name="ISzCs">9</xsl:variable>


    <xsl:variable name="iTextAutospaceO">1</xsl:variable>
    <xsl:variable name="iTextAutospaceN">2</xsl:variable>
    <xsl:variable name="iInd">3</xsl:variable>


    <xsl:variable name="prsRDefault">
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
        <xsl:value-of select="$na"/>
    </xsl:variable>

    <xsl:variable name="prsPDefault">
        <xsl:value-of select="$na"/><xsl:value-of select="$na"/>
    </xsl:variable>


    <xsl:variable name="footnoteRefLink" select="'ftnref_'"/>
    <xsl:variable name="footnoteLink" select="'ftn_'"/>
    <xsl:variable name="endnoteRefLink" select="'ednref_'"/>
    <xsl:variable name="endnoteLink" select="'edn_'"/>

    <xsl:template name="ConvertHexToDec">
        <xsl:param name="value"/>
        <xsl:param name="i" select="1"/>
        <xsl:param name="s" select="1"/>
        <xsl:variable name="hexDigit" select="substring($value,$i,1)"/>
        <xsl:if test="not($hexDigit = '')">
            <xsl:text> </xsl:text>
            <xsl:choose>
                <xsl:when test="$hexDigit = 'A'">10</xsl:when>
                <xsl:when test="$hexDigit = 'B'">11</xsl:when>
                <xsl:when test="$hexDigit = 'C'">12</xsl:when>
                <xsl:when test="$hexDigit = 'D'">13</xsl:when>
                <xsl:when test="$hexDigit = 'E'">14</xsl:when>
                <xsl:when test="$hexDigit = 'F'">15</xsl:when>
                <xsl:otherwise><xsl:value-of select="$hexDigit"/></xsl:otherwise>
            </xsl:choose>
            <xsl:call-template name="ConvertHexToDec">
                <xsl:with-param name="value" select="$value"/>
                <xsl:with-param name="i" select="$i+$s"/>
                <xsl:with-param name="s" select="$s"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>


    <xsl:template name="ConvBorderStyle">
        <xsl:param name="value"/>
        <xsl:choose>
            <xsl:when test="$value='none' or $value='nil'">none</xsl:when>
            <xsl:when test="$value='single'">solid</xsl:when>
            <xsl:when test="contains($value,'stroke')">solid</xsl:when>
            <xsl:when test="$value='dashed'">dashed</xsl:when>
            <xsl:when test="contains($value,'dash')">dashed</xsl:when>
            <xsl:when test="$value='double'">double</xsl:when>
            <xsl:when test="$value='triple'">double</xsl:when>
            <xsl:when test="contains($value,'double')">double</xsl:when>
            <xsl:when test="contains($value,'gap')">double</xsl:when>
            <xsl:when test="$value='dotted'">dotted</xsl:when>
            <xsl:when test="$value='three-d-emboss'">ridge</xsl:when>
            <xsl:when test="$value='three-d-engrave'">groove</xsl:when>
            <xsl:when test="$value='outset'">outset</xsl:when>
            <xsl:when test="$value='inset'">inset</xsl:when>
            <xsl:otherwise>solid</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="ConvBorderWidth">
        <xsl:param name="value"/>
        <xsl:choose>
            <xsl:when test="$value='none'">0</xsl:when>
            <xsl:when test="$value='solid'">1</xsl:when>
            <xsl:when test="$value='dashed'">1</xsl:when>
            <xsl:when test="$value='double'">3</xsl:when>
            <xsl:when test="$value='dotted'">1</xsl:when>
            <xsl:otherwise>1</xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="EvalTableWidth">
        <xsl:choose>
            <xsl:when test="@w:type = 'auto'">auto</xsl:when>
            <xsl:when test="@w:type = 'pct'"><xsl:value-of select="@w:w div 50"/>%</xsl:when>
            <xsl:otherwise><xsl:value-of select="@w:w div 20"/>pt</xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="ConvColor">
        <xsl:param name="value"/>
        <xsl:choose>
            <xsl:when test="$value='black'">black</xsl:when>
            <xsl:when test="$value='blue'">blue</xsl:when>
            <xsl:when test="$value='cyan'">aqua</xsl:when>
            <xsl:when test="$value='green'">lime</xsl:when>
            <xsl:when test="$value='magenta'">fuchsia</xsl:when>
            <xsl:when test="$value='red'">red</xsl:when>
            <xsl:when test="$value='yellow'">yellow</xsl:when>
            <xsl:when test="$value='white'">white</xsl:when>
            <xsl:when test="$value='dark-blue'">navy</xsl:when>
            <xsl:when test="$value='dark-cyan'">teal</xsl:when>
            <xsl:when test="$value='dark-green'">green</xsl:when>
            <xsl:when test="$value='dark-magenta'">purple</xsl:when>
            <xsl:when test="$value='dark-red'">maroon</xsl:when>
            <xsl:when test="$value='dark-yellow'">olive</xsl:when>
            <xsl:when test="$value='dark-gray'">gray</xsl:when>
            <xsl:when test="$value='light-gray'">silver</xsl:when>
            <xsl:when test="$value='none'">transparent</xsl:when>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="ConvHexColor">
        <xsl:param name="value"/>
        <xsl:param name="autoColor" select="$autoColor_text"/>
        <xsl:param name="transparentColor">transparent</xsl:param>
        <xsl:choose>
            <xsl:when test="$value = $autoColor_hex or $value = ''">
                <xsl:value-of select="$autoColor"/>
            </xsl:when>
            <xsl:when test="$value = $transparentColor_hex">
                <xsl:value-of select="$transparentColor"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="concat('#',$value)"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="EvalBooleanType">
        <xsl:choose>
            <xsl:when test="@w:val = 'off' or @w:val = 'none'  or @w:val = '0'"><xsl:value-of select="$off"/></xsl:when>
            <xsl:otherwise><xsl:value-of select="$on"/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="GetBorderPr">
            <xsl:value-of select="@w:val"/><xsl:value-of select="$sep2"/>
            <xsl:value-of select="@w:color"/><xsl:value-of select="$sep2"/>
            <xsl:text>0</xsl:text>
            <xsl:value-of select="$sep2"/>
            <xsl:value-of select="@w:space"/><xsl:value-of select="$sep2"/>
            <xsl:value-of select="@w:shadow"/>
    </xsl:template>


    <xsl:template name="ApplyBorderPr">
        <xsl:param name="pr.bdr"/>
        <xsl:param name="bdrSide" select="$bdrSide_char"/>
        <xsl:if test="not($pr.bdr='')">
            <xsl:text>border</xsl:text><xsl:value-of select="$bdrSide"/><xsl:text>:</xsl:text>
            <xsl:variable name="borderStyle">
                <xsl:call-template name="ConvBorderStyle">
                    <xsl:with-param name="value" select="substring-before($pr.bdr,$sep2)"/>
                </xsl:call-template>
            </xsl:variable>
            <xsl:value-of select="$borderStyle"/>
            <xsl:variable name="temp" select="substring-after($pr.bdr,$sep2)"/>
            <xsl:text> </xsl:text>
            <xsl:call-template name="ConvHexColor">
                <xsl:with-param name="value" select="substring-before($temp,$sep2)"/>
            </xsl:call-template>
            <xsl:text> </xsl:text>
            <!--<xsl:value-of select="substring-before(substring-after($temp,$sep2),$sep2) div 20"/><xsl:text>pt;</xsl:text>-->
            <xsl:call-template name="ConvBorderWidth">
                <xsl:with-param name="value" select="$borderStyle"/>
            </xsl:call-template>
            <xsl:text>px;</xsl:text>
            <xsl:if test="$bdrSide = $bdrSide_char">padding:0;</xsl:if>
        </xsl:if>
    </xsl:template>



























    <xsl:template name="ApplyTextDirection">
        <xsl:text>layout-flow:</xsl:text>
        <xsl:choose>
            <xsl:when test="@w:val = 'tb-rl-v'">vertical-ideographic</xsl:when>
            <xsl:when test="@w:val = 'lr-tb-v'">horizontal-ideographic</xsl:when>
            <xsl:otherwise>normal</xsl:otherwise>
        </xsl:choose>
        <xsl:text>;</xsl:text>
    </xsl:template>


    <xsl:template name="ApplyCellMar">
        <xsl:choose>
            <xsl:when test="@w:val='none'">none</xsl:when>
            <xsl:otherwise>
                <xsl:text>padding:</xsl:text>
                <xsl:choose><xsl:when test="w:top"><xsl:for-each select="w:top[1]"><xsl:call-template name="EvalTableWidth"/></xsl:for-each></xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose><xsl:text> </xsl:text>
                <xsl:choose><xsl:when test="w:right"><xsl:for-each select="w:right[1]"><xsl:call-template name="EvalTableWidth"/></xsl:for-each></xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose><xsl:text> </xsl:text>
                <xsl:choose><xsl:when test="w:bottom"><xsl:for-each select="w:bottom[1]"><xsl:call-template name="EvalTableWidth"/></xsl:for-each></xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose><xsl:text> </xsl:text>
                <xsl:choose><xsl:when test="w:left"><xsl:for-each select="w:left[1]"><xsl:call-template name="EvalTableWidth"/></xsl:for-each></xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose><xsl:text>;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>




    <xsl:template name="PrsUpdatePPr">
        <xsl:param name="prsP" select="$prsPDefault"/>
        <xsl:param name="ndPrContainer" select="."/>

        <xsl:variable name="prsPTemp">
            <xsl:for-each select="$ndPrContainer">
                <xsl:call-template name="PrsUpdatePPrCore">
                    <xsl:with-param name="prsP" select="$prsP"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$prsPTemp=''">
                <xsl:value-of select="$prsP"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$prsPTemp"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>



    <xsl:template name="FetchBasedOnPropertyBoolean">
            <xsl:param name="match" select="''"/>

            <xsl:choose>
                    <xsl:when test="$match">
                            <xsl:for-each select="$match">
                                    <xsl:call-template name="EvalBooleanType"/>
                            </xsl:for-each>
                    </xsl:when>
                    <xsl:when test="../w:basedOn">
                            <xsl:variable name="sBasedOn">
                                    <xsl:value-of select="../w:basedOn/@w:val"/>
                            </xsl:variable>
                            <xsl:for-each select="$nsStyles[@w:styleId=$sBasedOn]">
                                    <xsl:call-template name="FetchBasedOnPropertyBoolean"><xsl:with-param name="match" select="$match"/></xsl:call-template>
                            </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                            <xsl:value-of select="$na"/>
                    </xsl:otherwise>
            </xsl:choose>
    </xsl:template>

    <xsl:variable name="fbopModeIndentLeft" select="'1'"/>
    <xsl:variable name="fbopModeIndentLeftChars" select="'2'"/>
    <xsl:variable name="fbopModeIndentRight" select="'3'"/>
    <xsl:variable name="fbopModeIndentRightChars" select="'4'"/>
    <xsl:variable name="fbopModeIndentHanging" select="'5'"/>
    <xsl:variable name="fbopModeIndentHangingChars" select="'6'"/>
    <xsl:variable name="fbopModeIndentFirstLine" select="'7'"/>
    <xsl:variable name="fbopModeIndentFirstLineChars" select="'8'"/>

    <xsl:template name="FetchBasedOnProperty">
            <xsl:param name="mode" select="''"/>
            <xsl:param name="sDefault" select="''"/>

            <xsl:variable name="sValue">
                    <xsl:choose>
                            <xsl:when test="$mode=$fbopModeIndentLeft">
                                    <xsl:value-of select="w:ind[1]/@w:left"/>
                            </xsl:when>
                            <xsl:when test="$mode=$fbopModeIndentLeftChars">
                                    <xsl:value-of select="w:ind[1]/@w:left-chars"/>
                            </xsl:when>
                            <xsl:when test="$mode=$fbopModeIndentRight">
                                    <xsl:value-of select="w:ind[1]/@w:right"/>
                            </xsl:when>
                            <xsl:when test="$mode=$fbopModeIndentRightChars">
                                    <xsl:value-of select="w:ind[1]/@w:right-chars"/>
                            </xsl:when>
                            <xsl:when test="$mode=$fbopModeIndentHanging">
                                    <xsl:value-of select="w:ind[1]/@w:hanging"/>
                            </xsl:when>
                            <xsl:when test="$mode=$fbopModeIndentHangingChars">
                                    <xsl:value-of select="w:ind[1]/@w:hanging-chars"/>
                            </xsl:when>
                            <xsl:when test="$mode=$fbopModeIndentFirstLine">
                                    <xsl:value-of select="w:ind[1]/@w:first-line"/>
                            </xsl:when>
                            <xsl:when test="$mode=$fbopModeIndentFirstLineChars">
                                    <xsl:value-of select="w:ind[1]/@w:first-line-chars"/>
                            </xsl:when>
                            <xsl:otherwise>
                                    <xsl:text></xsl:text>
                            </xsl:otherwise>
                    </xsl:choose>
            </xsl:variable>

            <xsl:choose>
                    <xsl:when test="not($sValue='')">
                            <xsl:value-of select="$sValue"/>
                    </xsl:when>
                    <xsl:when test="../w:basedOn">
                            <xsl:variable name="sBasedOn">
                                    <xsl:value-of select="../w:basedOn/@w:val"/>
                            </xsl:variable>
                            <xsl:for-each select="$nsStyles[@w:styleId=$sBasedOn]/w:pPr[1]">
                                    <xsl:call-template name="FetchBasedOnProperty"><xsl:with-param name="mode" select="$mode"/></xsl:call-template>
                            </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                            <xsl:value-of select="$sDefault"/>
                    </xsl:otherwise>
            </xsl:choose>
    </xsl:template>


    <xsl:template name="PrsUpdatePPrCore">
        <xsl:param name="prsP" select="$prsPDefault"/>
        <xsl:for-each select="w:pPr[1]">

            <xsl:variable name="fTextAutospaceO">

                <xsl:for-each select="w:autoSpaceDE[1]">
                    <xsl:call-template name="EvalBooleanType"/>
                </xsl:for-each>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fTextAutospaceO=''">
                    <xsl:value-of select="substring($prsP, $iTextAutospaceO, 1)"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$fTextAutospaceO"/>
                </xsl:otherwise>
            </xsl:choose>


            <xsl:variable name="fTextAutospaceN">
                <xsl:for-each select="w:autoSpaceDN[1]">
                    <xsl:call-template name="EvalBooleanType"/>
                </xsl:for-each>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fTextAutospaceN=''">
                    <xsl:value-of select="substring($prsP, $iTextAutospaceN, 1)"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$fTextAutospaceN"/>
                </xsl:otherwise>
            </xsl:choose>


                    <xsl:variable name="prsDefaultInd" select="substring($prsP, $iInd)"/>
                    <xsl:variable name="sDefLeft" select="substring-before($prsDefaultInd,$sep2)"/><xsl:variable name="temp1" select="substring-after($prsDefaultInd,$sep2)"/>
                    <xsl:variable name="sDefLeftChars" select="substring-before($temp1,$sep2)"/><xsl:variable name="temp2" select="substring-after($temp1,$sep2)"/>
                    <xsl:variable name="sDefRight" select="substring-before($temp2,$sep2)"/><xsl:variable name="temp3" select="substring-after($temp2,$sep2)"/>
                    <xsl:variable name="sDefRightChars" select="substring-before($temp3,$sep2)"/><xsl:variable name="temp4" select="substring-after($temp3,$sep2)"/>
                    <xsl:variable name="sDefHanging" select="substring-before($temp4,$sep2)"/><xsl:variable name="temp5" select="substring-after($temp4,$sep2)"/>
                    <xsl:variable name="sDefHangingChars" select="substring-before($temp5,$sep2)"/><xsl:variable name="temp6" select="substring-after($temp5,$sep2)"/>
                    <xsl:variable name="sDefFirstLine" select="substring-before($temp6,$sep2)"/>
                    <xsl:variable name="sDefFirstLineChars" select="substring-after($temp6,$sep2)"/>

            <xsl:variable name="nInd">

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentLeft"/>
                                    <xsl:with-param name="sDefault" select="$sDefLeft"/>
                            </xsl:call-template>

                            <xsl:value-of select="$sep2"/>

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentLeftChars"/>
                                    <xsl:with-param name="sDefault" select="$sDefLeftChars"/>
                            </xsl:call-template>

                            <xsl:value-of select="$sep2"/>

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentRight"/>
                                    <xsl:with-param name="sDefault" select="$sDefRight"/>
                            </xsl:call-template>

                            <xsl:value-of select="$sep2"/>

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentRightChars"/>
                                    <xsl:with-param name="sDefault" select="$sDefRightChars"/>
                            </xsl:call-template>

                            <xsl:value-of select="$sep2"/>

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentHanging"/>
                                    <xsl:with-param name="sDefault" select="$sDefHanging"/>
                            </xsl:call-template>

                            <xsl:value-of select="$sep2"/>

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentHangingChars"/>
                                    <xsl:with-param name="sDefault" select="$sDefHangingChars"/>
                            </xsl:call-template>

                            <xsl:value-of select="$sep2"/>

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentFirstLine"/>
                                    <xsl:with-param name="sDefault" select="$sDefFirstLine"/>
                            </xsl:call-template>

                            <xsl:value-of select="$sep2"/>

                            <xsl:call-template name="FetchBasedOnProperty">
                                    <xsl:with-param name="mode" select="$fbopModeIndentFirstLineChars"/>
                                    <xsl:with-param name="sDefault" select="$sDefFirstLineChars"/>
                            </xsl:call-template>

            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$nInd=''">
                    <xsl:value-of select="substring($prsP, $iInd)"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$nInd"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>



    <xsl:template name="PrsUpdateRPr">
        <xsl:param name="prsR" select="$prsRDefault"/>
        <xsl:param name="ndPrContainer" select="."/>
        <xsl:variable name="prsRTemp">
            <xsl:for-each select="$ndPrContainer">
                <xsl:call-template name="PrsUpdateRPrCore">
                    <xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$prsRTemp=''">
                <xsl:value-of select="$prsR"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$prsRTemp"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>



    <xsl:template name="PrsUpdateRPrCore">
        <xsl:param name="prsR" select="$prsRDefault"/>
        <xsl:param name="type" select="$prrNonList"/>

        <xsl:for-each select="w:rPr[1]">

            <xsl:variable name="fEmbossImprint">
                <xsl:variable name="condition1"><xsl:for-each select="w:emboss[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:variable name="condition2"><xsl:for-each select="w:imprint[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:choose>
                    <xsl:when test="$condition1 = $on or $condition2 = $on">
                        <xsl:value-of select="$on"/>
                    </xsl:when>
                    <xsl:when test="$condition1 = $off or $condition2 = $off">
                        <xsl:value-of select="$off"/>
                    </xsl:when>
                </xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fEmbossImprint = ''"><xsl:value-of select="substring($prsR,$iEmbossImprint,1)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$fEmbossImprint"/></xsl:otherwise>
            </xsl:choose>


            <xsl:variable name="fU_Em">
                <xsl:variable name="condition1"><xsl:for-each select="w:u[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:variable name="condition2"><xsl:for-each select="w:em[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:choose><xsl:when test="$condition1 = $on or $condition2 = $on"><xsl:value-of select="$on"/></xsl:when><xsl:when test="$condition1 = $off or $condition2 = $off"><xsl:value-of select="$off"/></xsl:when></xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fU_Em = ''">
                    <xsl:choose>
                        <xsl:when test="$type=$prrList">
                            <xsl:value-of select="$off"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="substring($prsR,$iU_Em,1)"/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$fU_Em"/></xsl:otherwise>
            </xsl:choose>

            <xsl:variable name="fStrikeDStrike">
                <xsl:variable name="condition1"><xsl:for-each select="w:strike[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:variable name="condition2"><xsl:for-each select="w:dstrike[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:choose><xsl:when test="$condition1 = $on or $condition2 = $on"><xsl:value-of select="$on"/></xsl:when><xsl:when test="$condition1 = $off or $condition2 = $off"><xsl:value-of select="$off"/></xsl:when></xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fStrikeDStrike = ''"><xsl:value-of select="substring($prsR,$iStrikeDStrike,1)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$fStrikeDStrike"/></xsl:otherwise>
            </xsl:choose>




            <xsl:variable name="fSup">
                <xsl:choose>
                    <xsl:when test="w:vertAlign/@w:val='superscript'"><xsl:value-of select="$on"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$off"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="not(w:vertAlign)"><xsl:value-of select="substring($prsR,$iSup,1)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$fSup"/></xsl:otherwise>
            </xsl:choose>

            <xsl:variable name="fSub">
                <xsl:choose>
                    <xsl:when test="w:vertAlign/@w:val='subscript'"><xsl:value-of select="$on"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$off"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="not(w:vertAlign)"><xsl:value-of select="substring($prsR,$iSub,1)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$fSub"/></xsl:otherwise>
            </xsl:choose>

            <xsl:variable name="fVanishWebHidden">
                <xsl:variable name="condition1"><xsl:for-each select="w:vanish[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:variable name="condition2"><xsl:for-each select="w:webHidden[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each></xsl:variable>
                <xsl:choose><xsl:when test="$condition1 = $on or $condition2 = $on"><xsl:value-of select="$on"/></xsl:when><xsl:when test="$condition1 = $off or $condition2 = $off"><xsl:value-of select="$off"/></xsl:when></xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fVanishWebHidden = ''"><xsl:value-of select="substring($prsR,$iVanishWebHidden,1)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$fVanishWebHidden"/></xsl:otherwise>
            </xsl:choose>

            <xsl:variable name="fBCs">
                <xsl:for-each select="w:bCs[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fBCs = ''"><xsl:value-of select="substring($prsR,$iBCs,1)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$fBCs"/></xsl:otherwise>
            </xsl:choose>

            <xsl:variable name="fICs">
                <xsl:for-each select="w:i-cs[1]"><xsl:call-template name="EvalBooleanType"/></xsl:for-each>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="$fICs = ''"><xsl:value-of select="substring($prsR,$iICs,1)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$fICs"/></xsl:otherwise>
            </xsl:choose>

            <xsl:variable name="nSzCs" select="string(w:sz-cs[1]/@w:val)"/>
            <xsl:choose>
                <xsl:when test="$nSzCs = ''"><xsl:value-of select="substring($prsR,$ISzCs)"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$nSzCs"/></xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="GetSinglePPr">
        <xsl:param name="type"/>
        <xsl:param name="sParaStyleName"/>

        <xsl:variable name="result">
            <xsl:call-template name="GetSinglePPrCore">
                <xsl:with-param name="type" select="$type"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:if test="$result=''">
            <xsl:for-each select="$sParaStyleName">
                <xsl:call-template name="GetSinglePPrCore">
                    <xsl:with-param name="type" select="$type"/>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:if>
        <xsl:value-of select="$result"/>
    </xsl:template>

    <xsl:template name="GetSinglePPrCore">
        <xsl:param name="type"/>
        <xsl:for-each select="w:pPr[1]">
            <xsl:choose>
                <xsl:when test="$type = $prrBdrPr_top">
                    <xsl:for-each select="w:bdr[1]/w:top[1]">
                        <xsl:call-template name="GetBorderPr"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_right">
                    <xsl:for-each select="w:bdr[1]/w:right[1]">
                        <xsl:call-template name="GetBorderPr"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_bottom">
                    <xsl:for-each select="w:bdr[1]/w:bottom[1]">
                        <xsl:call-template name="GetBorderPr"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_left">
                    <xsl:for-each select="w:bdr[1]/w:left[1]">
                        <xsl:call-template name="GetBorderPr"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_between">
                    <xsl:for-each select="w:bdr[1]/w:between[1]">
                        <xsl:call-template name="GetBorderPr"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_bar">
                    <xsl:for-each select="w:bdr[1]/w:bar[1]">
                        <xsl:call-template name="GetBorderPr"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrFrame">
                    <xsl:for-each select="w:framePr[1]">
                        <xsl:value-of select="@w:w"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:h"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:h-rule"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:x-align"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:vspace"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:hspace"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:wrap"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:drop-cap"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:lines"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:x"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:y-align"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:y"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:hanchor"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:vanchor"/><xsl:value-of select="$sep2"/>
                        <xsl:value-of select="@w:anchor-lock"/>
                    </xsl:for-each>
                </xsl:when>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>


    <xsl:template name="GetSingleTblPr">
        <xsl:param name="type"/>
        <xsl:param name="sTblStyleName"/>
        <xsl:variable name="result">
            <xsl:call-template name="GetSingleTblPrCore">
                <xsl:with-param name="type" select="$type"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:if test="$result='' and $sTblStyleName">
            <xsl:for-each select="$sTblStyleName">
                <xsl:call-template name="GetSingleTblPrCore"><xsl:with-param name="type" select="$type"/></xsl:call-template>
            </xsl:for-each>
        </xsl:if>
        <xsl:value-of select="$result"/>
    </xsl:template>

    <xsl:template name="GetSingleTblPrCore">
        <xsl:param name="type"/>
        <xsl:for-each select="w:tblPr[1]">
            <xsl:choose>
                <xsl:when test="$type = $prrBdrPr_top">
                    <xsl:for-each select="w:tblBorders[1]/w:top[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_left">
                    <xsl:for-each select="w:tblBorders[1]/w:left[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_bottom">
                    <xsl:for-each select="w:tblBorders[1]/w:bottom[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_right">
                    <xsl:for-each select="w:tblBorders[1]/w:right[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_insideH">
                    <xsl:for-each select="w:tblBorders[1]/w:insideH[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrBdrPr_insideV">
                    <xsl:for-each select="w:tblBorders[1]/w:insideV[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrDefaultCellpadding">
                    <xsl:for-each select="w:tblCellMar[1]"><xsl:call-template name="ApplyCellMar"/></xsl:for-each>
                </xsl:when>
                <xsl:when test="$type = $prrCellspacing">
                    <xsl:value-of select="w:tblCellSpacing[1]/@w:w"/>
                </xsl:when>
                <xsl:when test="$type = $prrTblInd">
                    <xsl:for-each select="w:tblInd[1]">
                        <xsl:call-template name="EvalTableWidth"/>
                    </xsl:for-each>
                </xsl:when>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>




    <xsl:template name="WrapCnf">
        <xsl:param name="sTblStyleName"/>
        <xsl:param name="cnfCol"/>
        <xsl:param name="cnfRow"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>

        <xsl:choose>

            <xsl:when test="substring($cnfRow,$icnfBand1Horz,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfBand1Horz][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfBand1Horz)}">
                <xsl:call-template name="WrapCnf.a">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:when test="substring($cnfRow,$icnfBand2Horz,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfBand2Horz][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfBand2Horz)}">
                <xsl:call-template name="WrapCnf.a">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:otherwise>

                <xsl:call-template name="WrapCnf.a">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/><xsl:with-param name="prsP" select="$prsP"/><xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template name="WrapCnf.a">
        <xsl:param name="sTblStyleName"/><xsl:param name="cnfCol"/><xsl:param name="cnfRow"/>
        <xsl:param name="prsPAccum"/><xsl:param name="prsP"/><xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="substring($cnfCol,$icnfBand1Vert,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfBand1Vert][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfBand1Vert)}">
                <xsl:call-template name="WrapCnf.b">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:when test="substring($cnfCol,$icnfBand2Vert,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfBand2Vert][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfBand2Vert)}">
                <xsl:call-template name="WrapCnf.b">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:otherwise>

                <xsl:call-template name="WrapCnf.b">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/><xsl:with-param name="prsP" select="$prsP"/><xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template name="WrapCnf.b">
        <xsl:param name="sTblStyleName"/><xsl:param name="cnfCol"/><xsl:param name="cnfRow"/>
        <xsl:param name="prsPAccum"/><xsl:param name="prsP"/><xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="substring($cnfCol,$icnfFirstCol,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfFirstCol][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfFirstCol)}">
                <xsl:call-template name="WrapCnf.c">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:when test="substring($cnfCol,$icnfLastCol,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfLastCol][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfLastCol)}">
                <xsl:call-template name="WrapCnf.c">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:otherwise>

                <xsl:call-template name="WrapCnf.c">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/><xsl:with-param name="prsP" select="$prsP"/><xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template name="WrapCnf.c">
        <xsl:param name="sTblStyleName"/><xsl:param name="cnfCol"/><xsl:param name="cnfRow"/>
        <xsl:param name="prsPAccum"/><xsl:param name="prsP"/><xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="substring($cnfRow,$icnfFirstRow,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfFirstRow][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfFirstRow)}">
                <xsl:call-template name="WrapCnf.d">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:when test="substring($cnfRow,$icnfLastRow,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfLastRow][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfLastRow)}">
                <xsl:call-template name="WrapCnf.d">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/>
                </xsl:call-template>
                </div>
            </xsl:when>

            <xsl:otherwise>

                <xsl:call-template name="WrapCnf.d">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/><xsl:with-param name="prsP" select="$prsP"/><xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template name="WrapCnf.d">
        <xsl:param name="sTblStyleName"/><xsl:param name="cnfCol"/><xsl:param name="cnfRow"/>
        <xsl:param name="prsPAccum"/><xsl:param name="prsP"/><xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="substring($cnfCol,$icnfNECell,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfNECell][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfNECell)}">
                <xsl:call-template name="DisplayBodyContent"><xsl:with-param name="ns.content" select="*"/><xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/></xsl:call-template>
                </div>
            </xsl:when>

            <xsl:when test="substring($cnfCol,$icnfNWCell,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfNWCell][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfNWCell)}">
                <xsl:call-template name="DisplayBodyContent"><xsl:with-param name="ns.content" select="*"/><xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/></xsl:call-template>
                </div>
            </xsl:when>

            <xsl:when test="substring($cnfCol,$icnfSECell,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfSECell][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfSECell)}">
                <xsl:call-template name="DisplayBodyContent"><xsl:with-param name="ns.content" select="*"/><xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/></xsl:call-template>
                </div>
            </xsl:when>

            <xsl:when test="substring($cnfCol,$icnfSWCell,1)=$on">
                <xsl:variable name="p.cnfType" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfSWCell][1]"/>

                <xsl:variable name="prsP.updated">
                    <xsl:call-template name="PrsUpdatePPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsP" select="$prsP"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsR.updated">
                    <xsl:call-template name="PrsUpdateRPr"><xsl:with-param name="ndPrContainer" select="$p.cnfType"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:variable>
                <xsl:variable name="prsPAccum.updated">
                    <xsl:value-of select="$prsPAccum"/><xsl:for-each select="$p.cnfType"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
                </xsl:variable>

                <div class="{concat($sTblStyleName/@w:styleId,'-',$cnfSWCell)}">
                <xsl:call-template name="DisplayBodyContent"><xsl:with-param name="ns.content" select="*"/><xsl:with-param name="prsPAccum" select="$prsPAccum.updated"/><xsl:with-param name="prsP" select="$prsP.updated"/><xsl:with-param name="prsR" select="$prsR.updated"/></xsl:call-template>
                </div>
            </xsl:when>

            <xsl:otherwise>

                <xsl:call-template name="DisplayBodyContent"><xsl:with-param name="ns.content" select="*"/><xsl:with-param name="prsPAccum" select="$prsPAccum"/><xsl:with-param name="prsP" select="$prsP"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="GetCnfPr.all">
        <xsl:param name="type"/><xsl:param name="cnfCol"/><xsl:param name="cnfRow"/>
        <xsl:choose>
            <xsl:when test="substring($cnfRow,$icnfBand1Horz,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand1Horz][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfBand2Horz,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand2Horz][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test="substring($cnfCol,$icnfBand1Vert,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand1Vert][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfBand2Vert,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand2Vert][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test="substring($cnfCol,$icnfFirstCol,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfFirstCol][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfLastCol,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfLastCol][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test="substring($cnfRow,$icnfFirstRow,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfFirstRow][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfLastRow,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfLastRow][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test="substring($cnfCol,$icnfNECell,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfNECell][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfNWCell,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfNWCell][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfSECell,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfSECell][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfSWCell,1)=$on">
                <xsl:for-each select="w:tblStylePr[@w:type=$cnfSWCell][1]">
                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
    </xsl:template>




    <xsl:template name="GetCnfPr.cell">
        <xsl:param name="type"/><xsl:param name="cnfCol"/><xsl:param name="cnfRow"/>
        <xsl:variable name="result1">
            <xsl:choose>
                <xsl:when test="substring($cnfCol,$icnfNECell,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfNECell][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="substring($cnfCol,$icnfNWCell,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfNWCell][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="substring($cnfCol,$icnfSECell,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfSECell][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="substring($cnfCol,$icnfSWCell,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfSWCell][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
            </xsl:choose>
        </xsl:variable>
        <xsl:value-of select="$result1"/>
        <xsl:if test="$result1=''">
            <xsl:variable name="result2">
                <xsl:choose>
                    <xsl:when test="substring($cnfRow,$icnfFirstRow,1)=$on">
                        <xsl:for-each select="w:tblStylePr[@w:type=$cnfFirstRow][1]">
                            <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:when test="substring($cnfRow,$icnfLastRow,1)=$on">
                        <xsl:for-each select="w:tblStylePr[@w:type=$cnfLastRow][1]">
                            <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                        </xsl:for-each>
                    </xsl:when>
                </xsl:choose>
            </xsl:variable>
            <xsl:value-of select="$result2"/>
            <xsl:if test="$result2=''">
                <xsl:variable name="result3">
                    <xsl:choose>
                        <xsl:when test="substring($cnfCol,$icnfFirstCol,1)=$on">
                            <xsl:for-each select="w:tblStylePr[@w:type=$cnfFirstCol][1]">
                                <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                            </xsl:for-each>
                        </xsl:when>
                        <xsl:when test="substring($cnfCol,$icnfLastCol,1)=$on">
                            <xsl:for-each select="w:tblStylePr[@w:type=$cnfLastCol][1]">
                                <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                            </xsl:for-each>
                        </xsl:when>
                    </xsl:choose>
                </xsl:variable>
                <xsl:value-of select="$result3"/>
                <xsl:if test="$result3=''">
                    <xsl:variable name="result4">
                        <xsl:choose>
                            <xsl:when test="substring($cnfCol,$icnfBand1Vert,1)=$on">
                                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand1Vert][1]">
                                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                                </xsl:for-each>
                            </xsl:when>
                            <xsl:when test="substring($cnfCol,$icnfBand2Vert,1)=$on">
                                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand2Vert][1]">
                                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                                </xsl:for-each>
                            </xsl:when>
                        </xsl:choose>
                    </xsl:variable>
                    <xsl:value-of select="$result4"/>
                    <xsl:if test="$result4=''">
                        <xsl:choose>
                            <xsl:when test="substring($cnfRow,$icnfBand1Horz,1)=$on">
                                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand1Horz][1]">
                                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                                </xsl:for-each>
                            </xsl:when>
                            <xsl:when test="substring($cnfRow,$icnfBand2Horz,1)=$on">
                                <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand2Horz][1]">
                                    <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                                </xsl:for-each>
                            </xsl:when>
                        </xsl:choose>
                    </xsl:if>
                </xsl:if>
            </xsl:if>
        </xsl:if>
    </xsl:template>




    <xsl:template name="GetCnfPr.row">
        <xsl:param name="type"/><xsl:param name="cnfRow"/>
        <xsl:variable name="result1">
            <xsl:choose>
                <xsl:when test="substring($cnfRow,$icnfFirstRow,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfFirstRow][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="substring($cnfRow,$icnfLastRow,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfLastRow][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
            </xsl:choose>
        </xsl:variable>
        <xsl:value-of select="$result1"/>
        <xsl:if test="$result1=''">
            <xsl:choose>
                <xsl:when test="substring($cnfRow,$icnfBand1Horz,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand1Horz][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
                <xsl:when test="substring($cnfRow,$icnfBand2Horz,1)=$on">
                    <xsl:for-each select="w:tblStylePr[@w:type=$cnfBand2Horz][1]">
                        <xsl:call-template name="GetCnfPr.a"><xsl:with-param name="type" select="$type"/></xsl:call-template>
                    </xsl:for-each>
                </xsl:when>
            </xsl:choose>
        </xsl:if>
    </xsl:template>


    <xsl:template name="GetCnfPr.a">
        <xsl:param name="type"/>
        <xsl:choose>
            <xsl:when test="$type = $prrApplyTcPr">
                <xsl:call-template name="ApplyTcPr.class"/>
            </xsl:when>
            <xsl:when test="$type = $prrCustomCellpadding">
                <xsl:for-each select="w:tcPr[1]/w:tcMar[1]"><xsl:call-template name="ApplyCellMar"/></xsl:for-each>
            </xsl:when>
            <xsl:when test="$type = $prrDefaultCellpadding">
                <xsl:for-each select="w:tblPr[1]/w:tblCellMar[1]"><xsl:call-template name="ApplyCellMar"/></xsl:for-each>
            </xsl:when>
            <xsl:when test="$type = $prrCantSplit">
                <xsl:for-each select="w:trPr[1]/w:cantSplit[1]">
                    <xsl:choose>
                        <xsl:when test="@w:val = 'off'">page-break-inside:auto;</xsl:when>
                        <xsl:otherwise>page-break-inside:avoid;</xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="GetCnfType">
        <xsl:param name="cnfCol"/><xsl:param name="cnfRow"/>
        <xsl:choose>
            <xsl:when test="substring($cnfCol,$icnfNECell,1)=$on">
                <xsl:value-of select="$cnfNECell"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfNWCell,1)=$on">
                <xsl:value-of select="$cnfNWCell"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfSECell,1)=$on">
                <xsl:value-of select="$cnfSECell"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfSWCell,1)=$on">
                <xsl:value-of select="$cnfSWCell"/>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfFirstRow,1)=$on">
                <xsl:value-of select="$cnfFirstRow"/>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfLastRow,1)=$on">
                <xsl:value-of select="$cnfLastRow"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfFirstCol,1)=$on">
                <xsl:value-of select="$cnfFirstCol"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfLastCol,1)=$on">
                <xsl:value-of select="$cnfLastCol"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfBand1Vert,1)=$on">
                <xsl:value-of select="$cnfBand1Vert"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfBand2Vert,1)=$on">
                <xsl:value-of select="$cnfBand2Vert"/>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfBand1Horz,1)=$on">
                <xsl:value-of select="$cnfBand1Horz"/>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfBand2Horz,1)=$on">
                <xsl:value-of select="$cnfBand2Horz"/>
            </xsl:when>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="GetCnfTypeRow">
        <xsl:param name="cnfRow"/>
        <xsl:choose>
            <xsl:when test="substring($cnfRow,$icnfFirstRow,1)=$on">
                <xsl:value-of select="$cnfFirstRow"/>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfLastRow,1)=$on">
                <xsl:value-of select="$cnfLastRow"/>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfBand1Horz,1)=$on">
                <xsl:value-of select="$cnfBand1Horz"/>
            </xsl:when>
            <xsl:when test="substring($cnfRow,$icnfBand2Horz,1)=$on">
                <xsl:value-of select="$cnfBand2Horz"/>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="GetCnfTypeCol">
        <xsl:param name="cnfCol"/>
        <xsl:choose>
            <xsl:when test="substring($cnfCol,$icnfNECell,1)=$on">
                <xsl:value-of select="$cnfNECell"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfNWCell,1)=$on">
                <xsl:value-of select="$cnfNWCell"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfSECell,1)=$on">
                <xsl:value-of select="$cnfSECell"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfSWCell,1)=$on">
                <xsl:value-of select="$cnfSWCell"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfFirstCol,1)=$on">
                <xsl:value-of select="$cnfFirstCol"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfLastCol,1)=$on">
                <xsl:value-of select="$cnfLastCol"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfBand1Vert,1)=$on">
                <xsl:value-of select="$cnfBand1Vert"/>
            </xsl:when>
            <xsl:when test="substring($cnfCol,$icnfBand2Vert,1)=$on">
                <xsl:value-of select="$cnfBand2Vert"/>
            </xsl:when>
        </xsl:choose>
    </xsl:template>




    <xsl:template name="RecursiveRStyledGetBorderPr">
        <xsl:param name="rStyleId"/>

        <xsl:variable name="myStyle" select="($nsStyles[@w:styleId=$rStyleId])[1]" />

        <xsl:if test="not($rStyleId='')">
            <xsl:choose>
                <xsl:when test="$myStyle/w:rPr[1]/w:bdr[1]">
                    <xsl:for-each select="$myStyle/w:rPr[1]/w:bdr[1]">
                        <xsl:call-template name="GetBorderPr"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:if test="$myStyle/w:basedOn">
                        <xsl:call-template name="RecursiveRStyledGetBorderPr">
                            <xsl:with-param name="rStyleId" select="$myStyle/w:basedOn/@w:val" />
                        </xsl:call-template>
                    </xsl:if>
                </xsl:otherwise>
            </xsl:choose>


        </xsl:if>
    </xsl:template>


    <xsl:template name="DisplayRBorder">
        <xsl:param name="ns.content" select="*"/>
        <xsl:param name="i.range.start" select="1"/>
        <xsl:param name="i.this" select="number($i.range.start)"/>
        <xsl:param name="pr.bdr.prev" select="''"/>
        <xsl:param name="b.bidi"/>
        <xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="($ns.content)[$i.this]">
                <xsl:for-each select="($ns.content)[$i.this]">
                    <xsl:choose>

                        <xsl:when test="name() = 'w:proofErr' or (name() = 'aml:annotation' and not(@w:type = 'Word.Insertion'))">
                            <xsl:call-template name="DisplayRBorder">
                                <xsl:with-param name="ns.content" select="$ns.content"/>
                                <xsl:with-param name="i.range.start" select="$i.range.start"/>
                                <xsl:with-param name="i.this" select="$i.this+1"/>
                                <xsl:with-param name="pr.bdr.prev" select="$pr.bdr.prev"/>
                                <xsl:with-param name="b.bidi" select="$b.bidi"/>
                                <xsl:with-param name="prsR" select="$prsR"/>
                            </xsl:call-template>
                        </xsl:when>
                        <xsl:otherwise>

                            <xsl:variable name="pr.bdr.this">
                                <xsl:choose>

                                    <xsl:when test="name()='aml:annotation'"/>


                                    <xsl:otherwise>

                                        <xsl:for-each select="descendant-or-self::*[name()='w:pPr' or name()='w:r'][1]">
                                            <xsl:choose>
                                                <xsl:when test="w:rPr[1]/w:bdr[1]">
                                                    <xsl:for-each select="w:rPr[1]/w:bdr[1]">
                                                        <xsl:call-template name="GetBorderPr"/>
                                                    </xsl:for-each>
                                                </xsl:when>


                                                <xsl:otherwise>
                                                    <xsl:call-template name="RecursiveRStyledGetBorderPr">
                                                        <xsl:with-param name="rStyleId" select="w:rPr[1]/w:rStyle[1]/@w:val" />
                                                    </xsl:call-template>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:for-each>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>
                            <xsl:choose>

                                <xsl:when test="$pr.bdr.prev = $pr.bdr.this">

                                    <xsl:call-template name="DisplayRBorder">
                                        <xsl:with-param name="ns.content" select="$ns.content"/>
                                        <xsl:with-param name="i.range.start" select="$i.range.start"/>
                                        <xsl:with-param name="i.this" select="$i.this+1"/>
                                        <xsl:with-param name="pr.bdr.prev" select="$pr.bdr.prev"/>
                                        <xsl:with-param name="b.bidi" select="$b.bidi"/>
                                        <xsl:with-param name="prsR" select="$prsR"/>
                                    </xsl:call-template>
                                </xsl:when>

                                <xsl:otherwise>

                                    <xsl:call-template name="WrapRBorder">
                                        <xsl:with-param name="ns.content" select="$ns.content"/>
                                        <xsl:with-param name="i.bdrRange.start" select="$i.range.start"/>
                                        <xsl:with-param name="i.bdrRange.end" select="$i.this"/>
                                        <xsl:with-param name="pr.bdr" select="$pr.bdr.prev"/>
                                        <xsl:with-param name="b.bidi" select="$b.bidi"/>
                                        <xsl:with-param name="prsR" select="$prsR"/>
                                    </xsl:call-template>

                                    <xsl:call-template name="DisplayRBorder">
                                        <xsl:with-param name="ns.content" select="$ns.content"/>
                                        <xsl:with-param name="i.range.start" select="$i.this"/>
                                        <xsl:with-param name="i.this" select="$i.this+1"/>
                                        <xsl:with-param name="pr.bdr.prev" select="$pr.bdr.this"/>
                                        <xsl:with-param name="b.bidi" select="$b.bidi"/>
                                        <xsl:with-param name="prsR" select="$prsR"/>
                                    </xsl:call-template>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:when>

            <xsl:otherwise>

                <xsl:call-template name="WrapRBorder">
                    <xsl:with-param name="ns.content" select="$ns.content"/>
                    <xsl:with-param name="i.bdrRange.start" select="$i.range.start"/>
                    <xsl:with-param name="i.bdrRange.end" select="$i.this"/>
                    <xsl:with-param name="pr.bdr" select="$pr.bdr.prev"/>
                    <xsl:with-param name="b.bidi" select="$b.bidi"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="WrapRBorder">
        <xsl:param name="ns.content"/>
        <xsl:param name="i.bdrRange.start"/>
        <xsl:param name="i.bdrRange.end"/>
        <xsl:param name="pr.bdr"/>
        <xsl:param name="b.bidi"/>
        <xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="$pr.bdr = ''">
                <xsl:apply-templates select="($ns.content)[position() &gt;= $i.bdrRange.start and position() &lt; $i.bdrRange.end]">
                    <xsl:with-param name="b.bidi" select="$b.bidi"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                </xsl:apply-templates>
            </xsl:when>

            <xsl:otherwise>
                <span>
                <xsl:attribute name="style">
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$pr.bdr"/></xsl:call-template>
                </xsl:attribute>
                <xsl:apply-templates select="($ns.content)[position() &gt;= $i.bdrRange.start and position() &lt; $i.bdrRange.end]">
                    <xsl:with-param name="b.bidi" select="$b.bidi"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                </xsl:apply-templates>
                </span>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="DisplayPBorderOld">
        <xsl:param name="pr.frame.prev"/>
        <xsl:param name="pr.bdrTop.prev"/>
        <xsl:param name="pr.bdrLeft.prev"/>
        <xsl:param name="pr.bdrBottom.prev"/>
        <xsl:param name="pr.bdrRight.prev"/>
        <xsl:param name="pr.bdrBetween.prev"/>
        <xsl:param name="pr.bdrBar.prev"/>
        <xsl:param name="ns.content"/>
        <xsl:param name="i.range.start" select="1"/>
        <xsl:param name="i.this" select="number($i.range.start)"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="($ns.content)[$i.this]">
                <xsl:for-each select="($ns.content)[$i.this]">
                    <xsl:variable name="pstyle">
                        <xsl:call-template name="GetPStyleId"/>
                    </xsl:variable>
                    <xsl:variable name="sParaStyleName" select="($nsStyles[@w:styleId=$pstyle])[1]"/>

                    <xsl:variable name="pr.frame.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrFrame"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrTop.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_top"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrLeft.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_left"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrBottom.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_bottom"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrRight.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_right"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrBetween.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_between"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrBar.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_bar"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:choose>

                        <xsl:when test="0 = 1 and $pr.frame.prev = $pr.frame.this and $pr.bdrTop.prev = $pr.bdrTop.this and $pr.bdrLeft.prev = $pr.bdrLeft.this and $pr.bdrBottom.prev = $pr.bdrBottom.this and $pr.bdrRight.prev = $pr.bdrRight.this and $pr.bdrBetween.prev = $pr.bdrBetween.this and $pr.bdrBar.prev = $pr.bdrBar.this">
                            <xsl:call-template name="DisplayPBorder">
                                <xsl:with-param name="ns.content" select="$ns.content"/>
                                <xsl:with-param name="i.range.start" select="$i.range.start"/>
                                <xsl:with-param name="i.this" select="$i.this+1"/>
                                <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                                <xsl:with-param name="prsP" select="$prsP"/>
                                <xsl:with-param name="prsR" select="$prsR"/>
                                <xsl:with-param name="pr.frame.prev" select="$pr.frame.prev"/>
                                <xsl:with-param name="pr.bdrTop.prev" select="$pr.bdrTop.prev"/>
                                <xsl:with-param name="pr.bdrLeft.prev" select="$pr.bdrLeft.prev"/>
                                <xsl:with-param name="pr.bdrBottom.prev" select="$pr.bdrBottom.prev"/>
                                <xsl:with-param name="pr.bdrRight.prev" select="$pr.bdrRight.prev"/>
                                <xsl:with-param name="pr.bdrBetween.prev" select="$pr.bdrBetween.prev"/>
                                <xsl:with-param name="pr.bdrBar.prev" select="$pr.bdrBar.prev"/>
                            </xsl:call-template>
                        </xsl:when>

                        <xsl:otherwise>

                            <xsl:call-template name="wrapFrame">
                                <xsl:with-param name="ns.content" select="$ns.content"/>
                                <xsl:with-param name="i.bdrRange.start" select="$i.range.start"/>
                                <xsl:with-param name="i.bdrRange.end" select="$i.this"/>
                                <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                                <xsl:with-param name="prsP" select="$prsP"/>
                                <xsl:with-param name="prsR" select="$prsR"/>
                                <xsl:with-param name="framePr" select="$pr.frame.prev"/>
                                <xsl:with-param name="pr.bdrTop" select="$pr.bdrTop.prev"/>
                                <xsl:with-param name="pr.bdrLeft" select="$pr.bdrLeft.prev"/>
                                <xsl:with-param name="pr.bdrBottom" select="$pr.bdrBottom.prev"/>
                                <xsl:with-param name="pr.bdrRight" select="$pr.bdrRight.prev"/>
                                <xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween.prev"/>
                                <xsl:with-param name="pr.bdrBar" select="$pr.bdrBar.prev"/>
                            </xsl:call-template>

                            <xsl:call-template name="DisplayPBorder">
                                <xsl:with-param name="ns.content" select="$ns.content"/>
                                <xsl:with-param name="i.range.start" select="$i.this"/>
                                <xsl:with-param name="i.this" select="$i.this+1"/>
                                <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                                <xsl:with-param name="prsP" select="$prsP"/>
                                <xsl:with-param name="prsR" select="$prsR"/>
                                <xsl:with-param name="pr.frame.prev" select="$pr.frame.this"/>
                                <xsl:with-param name="pr.bdrTop.prev" select="$pr.bdrTop.this"/>
                                <xsl:with-param name="pr.bdrLeft.prev" select="$pr.bdrLeft.this"/>
                                <xsl:with-param name="pr.bdrBottom.prev" select="$pr.bdrBottom.this"/>
                                <xsl:with-param name="pr.bdrRight.prev" select="$pr.bdrRight.this"/>
                                <xsl:with-param name="pr.bdrBetween.prev" select="$pr.bdrBetween.this"/>
                                <xsl:with-param name="pr.bdrBar.prev" select="$pr.bdrBar.this"/>
                            </xsl:call-template>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:when>

            <xsl:otherwise>
                <xsl:call-template name="wrapFrame">
                    <xsl:with-param name="ns.content" select="$ns.content"/>
                    <xsl:with-param name="i.bdrRange.start" select="$i.range.start"/>
                    <xsl:with-param name="i.bdrRange.end" select="$i.this"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                    <xsl:with-param name="prsP" select="$prsP"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                    <xsl:with-param name="framePr" select="$pr.frame.prev"/>
                    <xsl:with-param name="pr.bdrTop" select="$pr.bdrTop.prev"/>
                    <xsl:with-param name="pr.bdrLeft" select="$pr.bdrLeft.prev"/>
                    <xsl:with-param name="pr.bdrBottom" select="$pr.bdrBottom.prev"/>
                    <xsl:with-param name="pr.bdrRight" select="$pr.bdrRight.prev"/>
                    <xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween.prev"/>
                    <xsl:with-param name="pr.bdrBar" select="$pr.bdrBar.prev"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="DisplayPBorder">
        <xsl:param name="pr.frame.prev"/>
        <xsl:param name="pr.bdrTop.prev"/>
        <xsl:param name="pr.bdrLeft.prev"/>
        <xsl:param name="pr.bdrBottom.prev"/>
        <xsl:param name="pr.bdrRight.prev"/>
        <xsl:param name="pr.bdrBetween.prev"/>
        <xsl:param name="pr.bdrBar.prev"/>
        <xsl:param name="ns.content"/>
        <xsl:param name="i.range.start" select="1"/>
        <xsl:param name="i.this" select="number($i.range.start)"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="($ns.content)[$i.this]">
                <xsl:for-each select="($ns.content)">

                    <xsl:variable name="pstyle">
                        <xsl:call-template name="GetPStyleId"/>
                    </xsl:variable>
                    <xsl:variable name="sParaStyleName" select="($nsStyles[@w:styleId=$pstyle])[1]"/>

                    <xsl:variable name="pr.frame.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrFrame"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrTop.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_top"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrLeft.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_left"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrBottom.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_bottom"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrRight.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_right"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrBetween.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_between"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>
                    <xsl:variable name="pr.bdrBar.this">
                        <xsl:call-template name="GetSinglePPr"><xsl:with-param name="type" select="$prrBdrPr_bar"/><xsl:with-param name="sParaStyleName" select="$sParaStyleName"/></xsl:call-template>
                    </xsl:variable>

                            <xsl:call-template name="wrapFrame">
                                <xsl:with-param name="ns.content" select="."/>
                                <xsl:with-param name="i.bdrRange.start" select="1"/>
                                <xsl:with-param name="i.bdrRange.end" select="2"/>
                                <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                                <xsl:with-param name="prsP" select="$prsP"/>
                                <xsl:with-param name="prsR" select="$prsR"/>
                                <xsl:with-param name="framePr" select="$pr.frame.prev"/>
                                <xsl:with-param name="pr.bdrTop" select="$pr.bdrTop.prev"/>
                                <xsl:with-param name="pr.bdrLeft" select="$pr.bdrLeft.prev"/>
                                <xsl:with-param name="pr.bdrBottom" select="$pr.bdrBottom.prev"/>
                                <xsl:with-param name="pr.bdrRight" select="$pr.bdrRight.prev"/>
                                <xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween.prev"/>
                                <xsl:with-param name="pr.bdrBar" select="$pr.bdrBar.prev"/>
                            </xsl:call-template>
                </xsl:for-each>

            </xsl:when>

            <xsl:otherwise>
                <xsl:call-template name="wrapFrame">
                    <xsl:with-param name="ns.content" select="$ns.content"/>
                    <xsl:with-param name="i.bdrRange.start" select="$i.range.start"/>
                    <xsl:with-param name="i.bdrRange.end" select="$i.this"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                    <xsl:with-param name="prsP" select="$prsP"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                    <xsl:with-param name="framePr" select="$pr.frame.prev"/>
                    <xsl:with-param name="pr.bdrTop" select="$pr.bdrTop.prev"/>
                    <xsl:with-param name="pr.bdrLeft" select="$pr.bdrLeft.prev"/>
                    <xsl:with-param name="pr.bdrBottom" select="$pr.bdrBottom.prev"/>
                    <xsl:with-param name="pr.bdrRight" select="$pr.bdrRight.prev"/>
                    <xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween.prev"/>
                    <xsl:with-param name="pr.bdrBar" select="$pr.bdrBar.prev"/>
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="wrapFrame">
        <xsl:param name="framePr"/>
        <xsl:param name="pr.bdrTop"/><xsl:param name="pr.bdrLeft"/><xsl:param name="pr.bdrBottom"/><xsl:param name="pr.bdrRight"/><xsl:param name="pr.bdrBetween"/><xsl:param name="pr.bdrBar"/>
        <xsl:param name="ns.content"/>
        <xsl:param name="i.bdrRange.start"/>
        <xsl:param name="i.bdrRange.end"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="$framePr = ''">
                <xsl:call-template name="wrapPBdr">
                    <xsl:with-param name="ns.content" select="$ns.content"/>
                    <xsl:with-param name="i.bdrRange.start" select="$i.bdrRange.start"/><xsl:with-param name="i.bdrRange.end" select="$i.bdrRange.end"/>
                    <xsl:with-param name="pr.bdrTop" select="$pr.bdrTop"/><xsl:with-param name="pr.bdrLeft" select="$pr.bdrLeft"/><xsl:with-param name="pr.bdrBottom" select="$pr.bdrBottom"/><xsl:with-param name="pr.bdrRight" select="$pr.bdrRight"/><xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween"/><xsl:with-param name="pr.bdrBar" select="$pr.bdrBar"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                    <xsl:with-param name="prsP" select="$prsP"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:when>

            <xsl:otherwise>
                <xsl:variable name="width" select="substring-before($framePr,$sep2)"/><xsl:variable name="framePr1" select="substring-after($framePr,$sep2)"/>
                <xsl:variable name="height" select="substring-before($framePr1,$sep2)"/><xsl:variable name="framePr2" select="substring-after($framePr1,$sep2)"/>
                <xsl:variable name="hrule" select="substring-before($framePr2,$sep2)"/><xsl:variable name="framePr3" select="substring-after($framePr2,$sep2)"/>
                <xsl:variable name="xalign" select="substring-before($framePr3,$sep2)"/><xsl:variable name="framePr4" select="substring-after($framePr3,$sep2)"/>
                <xsl:variable name="vspace" select="substring-before($framePr4,$sep2)"/><xsl:variable name="framePr5" select="substring-after($framePr4,$sep2)"/>
                <xsl:variable name="hspace" select="substring-before($framePr5,$sep2)"/><xsl:variable name="framePr6" select="substring-after($framePr5,$sep2)"/>
                <xsl:variable name="wrap" select="substring-before($framePr6,$sep2)"/>

                <table cellspacing="0" cellpadding="0" hspace="0" vspace="0">
                <xsl:if test="not($width = '' and $height='')">
                    <xsl:attribute name="style">
                        <xsl:if test="not($width = '')">width:<xsl:value-of select="number($width) div 20"/>pt;</xsl:if>
                        <xsl:if test="not($height = '')">height:<xsl:value-of select="number($height) div 20"/>pt;</xsl:if>
                    </xsl:attribute>
                </xsl:if>
                <xsl:attribute name="align">
                    <xsl:choose>
                        <xsl:when test="$xalign = 'right' or $xalign = 'outside'">right</xsl:when>
                        <xsl:otherwise>left</xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
                <tr><td valign="top" align="left">
                <xsl:attribute name="style">
                    <xsl:text>padding:</xsl:text>
                    <xsl:choose><xsl:when test="$vspace = ''">0</xsl:when><xsl:otherwise><xsl:value-of select="number($vspace) div 20"/>pt</xsl:otherwise></xsl:choose><xsl:text> </xsl:text>
                    <xsl:choose><xsl:when test="$hspace = ''">0</xsl:when><xsl:otherwise><xsl:value-of select="number($hspace) div 20"/>pt</xsl:otherwise></xsl:choose><xsl:text>;</xsl:text>
                </xsl:attribute>

                <xsl:call-template name="wrapPBdr">
                    <xsl:with-param name="ns.content" select="$ns.content"/>
                    <xsl:with-param name="i.bdrRange.start" select="$i.bdrRange.start"/><xsl:with-param name="i.bdrRange.end" select="$i.bdrRange.end"/>
                    <xsl:with-param name="pr.bdrTop" select="$pr.bdrTop"/><xsl:with-param name="pr.bdrLeft" select="$pr.bdrLeft"/><xsl:with-param name="pr.bdrBottom" select="$pr.bdrBottom"/><xsl:with-param name="pr.bdrRight" select="$pr.bdrRight"/><xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween"/><xsl:with-param name="pr.bdrBar" select="$pr.bdrBar"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                    <xsl:with-param name="prsP" select="$prsP"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
                </td></tr></table>
                <xsl:if test="$wrap = '' or $wrap = 'none' or $wrap = 'not-beside'"><br/></xsl:if>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="wrapPBdr">
        <xsl:param name="pr.bdrTop"/><xsl:param name="pr.bdrLeft"/><xsl:param name="pr.bdrBottom"/><xsl:param name="pr.bdrRight"/><xsl:param name="pr.bdrBetween"/><xsl:param name="pr.bdrBar"/>
        <xsl:param name="ns.content"/>
        <xsl:param name="i.bdrRange.start"/>
        <xsl:param name="i.bdrRange.end"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:choose>

            <xsl:when test="$pr.bdrTop = '' and $pr.bdrLeft = '' and $pr.bdrBottom = '' and $pr.bdrRight = '' and $pr.bdrBar = ''">
                <xsl:apply-templates select="($ns.content)[position() &gt;= $i.bdrRange.start and position() &lt; $i.bdrRange.end]">
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                    <xsl:with-param name="prsP" select="$prsP"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                    <xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween"/>
                </xsl:apply-templates>
            </xsl:when>

            <xsl:otherwise>
                <div>

                <xsl:attribute name="style">
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$pr.bdrBar"/><xsl:with-param name="bdrSide" select="$bdrSide_left"/></xsl:call-template>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$pr.bdrTop"/><xsl:with-param name="bdrSide" select="$bdrSide_top"/></xsl:call-template>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$pr.bdrLeft"/><xsl:with-param name="bdrSide" select="$bdrSide_left"/></xsl:call-template>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$pr.bdrBottom"/><xsl:with-param name="bdrSide" select="$bdrSide_bottom"/></xsl:call-template>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$pr.bdrRight"/><xsl:with-param name="bdrSide" select="$bdrSide_right"/></xsl:call-template>
                    <xsl:text>padding:</xsl:text>
                    <xsl:variable name="topPad" select="substring-before(substring-after(substring-after(substring-after($pr.bdrTop,$sep2),$sep2),$sep2),$sep2)"/>
                    <xsl:variable name="rightPad" select="substring-before(substring-after(substring-after(substring-after($pr.bdrRight,$sep2),$sep2),$sep2),$sep2)"/>
                    <xsl:variable name="bottomPad" select="substring-before(substring-after(substring-after(substring-after($pr.bdrBottom,$sep2),$sep2),$sep2),$sep2)"/>
                    <xsl:variable name="leftPad" select="substring-before(substring-after(substring-after(substring-after($pr.bdrLeft,$sep2),$sep2),$sep2),$sep2)"/>
                    <xsl:choose><xsl:when test="$topPad = ''">0</xsl:when><xsl:otherwise><xsl:value-of select="$topPad"/>pt</xsl:otherwise></xsl:choose><xsl:text> </xsl:text>
                    <xsl:choose><xsl:when test="$rightPad = ''">0</xsl:when><xsl:otherwise><xsl:value-of select="$rightPad"/>pt</xsl:otherwise></xsl:choose><xsl:text> </xsl:text>
                    <xsl:choose><xsl:when test="$bottomPad = ''">0</xsl:when><xsl:otherwise><xsl:value-of select="$bottomPad"/>pt</xsl:otherwise></xsl:choose><xsl:text> </xsl:text>
                    <xsl:choose><xsl:when test="$leftPad = ''">0</xsl:when><xsl:otherwise><xsl:value-of select="$leftPad"/>pt</xsl:otherwise></xsl:choose><xsl:text>;</xsl:text>
                </xsl:attribute>

                <xsl:apply-templates select="($ns.content)[position() &gt;= $i.bdrRange.start and position() &lt; $i.bdrRange.end]">
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                    <xsl:with-param name="prsP" select="$prsP"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                    <xsl:with-param name="pr.bdrBetween" select="$pr.bdrBetween"/>
                </xsl:apply-templates>
                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>




    <xsl:template name="ApplyArgs">
        <xsl:param name="value"/>
        <xsl:variable name="attributeName" select="normalize-space(substring-before($value,'='))"/>
        <xsl:variable name="afterName" select="concat(substring-after($value,'='),' ')"/>
        <xsl:if test="not($attributeName = '')">
            <xsl:attribute name="{$attributeName}"><xsl:value-of select="normalize-space(translate(substring-before($afterName,' '),'&quot;',' '))"/></xsl:attribute>
            <xsl:call-template name="ApplyArgs"><xsl:with-param name="value" select="normalize-space(substring-after($afterName,' '))"/></xsl:call-template>
        </xsl:if>
    </xsl:template>


    <xsl:template match="w:scriptAnchor">
        <script>
        <xsl:apply-templates select="*" mode="scriptAnchor"/>
        </script>
    </xsl:template>
    <xsl:template match="w:args" mode="scriptAnchor">
        <xsl:call-template name="ApplyArgs"><xsl:with-param name="value" select="."/></xsl:call-template>
    </xsl:template>
    <xsl:template match="w:language" mode="scriptAnchor">
        <xsl:attribute name="language"><xsl:value-of select="."/></xsl:attribute>
    </xsl:template>
    <xsl:template match="w:scriptId" mode="scriptAnchor">
        <xsl:attribute name="id"><xsl:value-of select="."/></xsl:attribute>
    </xsl:template>
    <xsl:template match="w:scriptText" mode="scriptAnchor">
        <xsl:value-of disable-output-escaping="yes" select="."/>
    </xsl:template>
    <xsl:template match="*" mode="scriptAnchor"/>


    <xsl:template match="w:applet">
        <applet>
        <xsl:apply-templates select="*" mode="applet"/>
        </applet>
    </xsl:template>
    <xsl:template match="w:appletText" mode="applet">
        <xsl:value-of disable-output-escaping="yes" select="."/>
    </xsl:template>
    <xsl:template match="w:args" mode="applet">
        <xsl:call-template name="ApplyArgs"><xsl:with-param name="value" select="."/></xsl:call-template>
    </xsl:template>
    <xsl:template match="*" mode="applet"/>


    <xsl:template match="w:txbxContent">
        <xsl:call-template name="DisplayBodyContent">
            <xsl:with-param name="ns.content" select="*"/>
        </xsl:call-template>
    </xsl:template>


    <xsl:template match="w:pict">
        <xsl:apply-templates select="*"/>
    </xsl:template>


    <xsl:template match="w:br">
        <br>
        <!-- Adding @clear causes presentation problems on the Download page, and possibly others too -->
        <xsl:attribute name="clear">
            <xsl:choose>
                <xsl:when test="@w:clear"><xsl:value-of select="@w:clear"/></xsl:when>
                <xsl:otherwise>all</xsl:otherwise>
            </xsl:choose>
        </xsl:attribute>
        <xsl:if test="@w:type = 'page'">
            <xsl:attribute name="style">page-break-before:always</xsl:attribute>
        </xsl:if>
        </br>
    </xsl:template>


    <xsl:template match="w:instrText">
    </xsl:template>


    <xsl:template match="w:delText">
        <del>
        <xsl:value-of select="."/>
        </del>
    </xsl:template>


    <xsl:template match="w:t">
        <xsl:value-of select="."/>
    </xsl:template>


    <xsl:template match="w:sym">
        <span><xsl:attribute name="style">font-family:<xsl:value-of select="@w:font"/></xsl:attribute>
            <xsl:choose>
                <xsl:when test="starts-with(@w:char, 'F0')">
                    <xsl:text disable-output-escaping="yes">&amp;</xsl:text>#x<xsl:value-of select="substring-after(@w:char, 'F0')"/><xsl:text>;</xsl:text>
                </xsl:when>
                <xsl:when test="starts-with(@w:char, 'f0')">
                    <xsl:text disable-output-escaping="yes">&amp;</xsl:text>#x<xsl:value-of select="substring-after(@w:char, 'f0')"/><xsl:text>;</xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:text disable-output-escaping="yes">&amp;</xsl:text>#x<xsl:value-of select="@w:char"/><xsl:text>;</xsl:text>
                </xsl:otherwise>
            </xsl:choose></span>
    </xsl:template>

    <xsl:template name="OutputTlcChar">
        <xsl:param name="count" select="0"/>
        <xsl:param name="tlc" select="' '"/>
        <xsl:value-of select="$tlc"/>
        <xsl:if test="$count > 1">
            <xsl:call-template name="OutputTlcChar">
                <xsl:with-param name="count" select="$count - 1"/>
                <xsl:with-param name="tlc" select="$tlc"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>


    <xsl:template match="w:softHyphen">
        <xsl:text>&#xAD;</xsl:text>
    </xsl:template>


    <xsl:template match="w:noBreakHyphen">
        <xsl:text disable-output-escaping="yes">&amp;#8209;</xsl:text>
    </xsl:template>


    <xsl:template name="DisplayRContent">
        <xsl:apply-templates select="*"/>
    </xsl:template>


    <xsl:template name="ApplyRPr.once">
        <xsl:param name="rStyleId"/>
        <xsl:param name="b.bidi"/>
        <xsl:param name="prsR"/>

        <xsl:variable name="b.complexScript">
            <xsl:choose>
                <xsl:when test="w:rPr[1]/w:cs[1] or w:rPr[1]/w:rtl[1]"><xsl:value-of select="$on"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$off"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:if test="$b.complexScript = $on">
            <xsl:variable name="suffix.complexScript">-CS</xsl:variable>
            <xsl:variable name="b.font-weight" select="substring($prsR,$iBCs,1)"/>
            <xsl:variable name="b.font-style" select="substring($prsR,$iICs,1)"/>
            <xsl:variable name="pr.sz" select="substring($prsR,$ISzCs)"/>

            <xsl:choose>
                <xsl:when test="$b.font-style = $on">font-style:italic;</xsl:when>
                <xsl:otherwise>font-style:normal;</xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="$b.font-weight = $on">font-weight:bold;</xsl:when>
                <xsl:otherwise>font-weight:normal;</xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="$pr.sz = ''">font-size:12pt;</xsl:when>
                <xsl:otherwise>
                    <xsl:text>font-size:</xsl:text>
                    <xsl:value-of select="number($pr.sz) div 2"/>
                    <xsl:text>pt;</xsl:text>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>

        <xsl:if test="not($b.bidi = '')">
            <xsl:choose>
                <xsl:when test="$b.bidi = $on and not($b.complexScript = $on)">direction:ltr;</xsl:when>
                <xsl:when test="not($b.bidi = $on) and $b.complexScript = $on">direction:rtl;</xsl:when>
            </xsl:choose>
        </xsl:if>

        <xsl:if test="substring($prsR,$iEmbossImprint,1) = $on">color:gray;</xsl:if>

        <xsl:variable name="b.line-through" select="substring($prsR,$iStrikeDStrike,1)"/>
        <xsl:variable name="b.underline" select="substring($prsR,$iU_Em,1)"/>
        <xsl:choose>
            <xsl:when test="$b.line-through = $off and $b.underline = $off">text-decoration:none;</xsl:when>
            <xsl:when test="$b.line-through = $on and $b.underline = $on">text-decoration:underline;</xsl:when>
            <xsl:when test="$b.line-through = $on">text-decoration:line-through;</xsl:when>
            <xsl:when test="$b.underline = $on">text-decoration:underline;</xsl:when>
        </xsl:choose>

        <xsl:variable name="fSup" select="substring($prsR,$iSup,1)"/>
        <xsl:variable name="fSub" select="substring($prsR,$iSub,1)"/>
        <xsl:choose>
            <xsl:when test="$fSup = $on and $fSub = $on">vertical-align:baseline;</xsl:when>
            <xsl:when test="$fSub = $on">vertical-align:sub;</xsl:when>
            <xsl:when test="$fSup = $on">vertical-align:super;</xsl:when>
        </xsl:choose>

        <xsl:if test="not($rStyleId='CommentReference')">
            <xsl:if test="substring($prsR,$iVanishWebHidden,1) = $on">display:none;</xsl:if>
        </xsl:if>
    </xsl:template>


    <xsl:template name="RecursiveApplyRPr.class">
        <xsl:if test="w:basedOn">
            <xsl:variable name="baseStyleName" select="w:basedOn[1]/@w:val" />
            <xsl:variable name="sParaStyleBase" select="($nsStyles[@w:styleId=$baseStyleName])[1]"/>
            <xsl:for-each select="$sParaStyleBase"><xsl:call-template name="RecursiveApplyRPr.class" /></xsl:for-each>
        </xsl:if>


        <xsl:call-template name="ApplyRPr.class"/>
    </xsl:template>


    <xsl:template name="ApplyRPr.class">
        <xsl:for-each select="w:rPr[1]">
            <xsl:apply-templates select="*" mode="rpr"/>
        </xsl:for-each>
    </xsl:template>


    <xsl:template match="w:highlight" mode="rpr">background:<xsl:call-template name="ConvColor"><xsl:with-param name="value" select="@w:val"/></xsl:call-template>;</xsl:template>


    <xsl:template match="w:color" mode="rpr">color:<xsl:call-template name="ConvHexColor"><xsl:with-param name="value" select="@w:val"/></xsl:call-template>;</xsl:template>

    <xsl:template match="w:smallCaps" mode="rpr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">font-variant:normal;</xsl:when>
            <xsl:otherwise>font-variant:small-caps;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:asianLayout" mode="rpr">
        <xsl:choose>
            <xsl:when test="@w:vert = 'on'">layout-flow:horizontal;</xsl:when>
            <xsl:when test="@w:vert-compress = 'on'">layout-flow:horizontal;</xsl:when>
            <xsl:when test="@w:vert = 'off' or @w:vert-compress = 'off'">layout-flow:normal;</xsl:when>
        </xsl:choose>
        <xsl:if test="@w:combine = 'lines'">text-combine:lines;</xsl:if>
    </xsl:template>

    <xsl:template match="w:spacing" mode="rpr">letter-spacing:<xsl:value-of select="@w:val div 20"/>pt;</xsl:template>

    <xsl:template match="w:position" mode="rpr">
        <xsl:variable name="fDropCap">
             <xsl:value-of select="ancestor::w:p[1]/w:pPr/w:framePr/@w:drop-cap"/>
        </xsl:variable>
        <xsl:if test="$fDropCap=''">
            <xsl:text>position:relative;top:</xsl:text>
            <xsl:value-of select="@w:val div -2"/>
            <xsl:text>pt;</xsl:text>
        </xsl:if>
    </xsl:template>
    <xsl:template match="w:fitText" mode="rpr">text-fit:<xsl:value-of select="@w:val div 20"/>pt;</xsl:template>
    <xsl:template match="w:shadow" mode="rpr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">text-shadow:none;</xsl:when>
            <xsl:otherwise>text-shadow:0.2em 0.2em;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:caps" mode="rpr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">text-transform:none;</xsl:when>
            <xsl:otherwise>text-transform:uppercase;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:sz" mode="rpr">font-size:<xsl:value-of select="@w:val div 2"/>pt;</xsl:template>

    <xsl:template match="w:b" mode="rpr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">font-weight:normal;</xsl:when>
            <xsl:otherwise>font-weight:bold;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:i" mode="rpr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">font-style:normal;</xsl:when>
            <xsl:otherwise>font-style:italic;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template match="*" mode="rpr"/>


    <xsl:template name="RecursivePrsUpdateRPr">
        <xsl:param name="prsR" />
        <xsl:param name="rStyleId" />

        <xsl:variable name="myStyle" select="($nsStyles[@w:styleId=$rStyleId])[1]"/>

        <xsl:variable name="prsR.updated">
            <xsl:choose>
                <xsl:when test="$myStyle/w:basedOn">
                    <xsl:call-template name="RecursivePrsUpdateRPr">
                        <xsl:with-param name="prsR" select="$prsR" />
                        <xsl:with-param name= "rStyleId" select="$myStyle/w:basedOn/@w:val" />
                    </xsl:call-template>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$prsR" /></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>


        <xsl:call-template name="PrsUpdateRPr">
            <xsl:with-param name="ndPrContainer" select="$myStyle"/>
            <xsl:with-param name="prsR" select="$prsR.updated"/>
        </xsl:call-template>
    </xsl:template>



    <xsl:template name="DisplayR">
        <xsl:param name="b.bidi"/>
        <xsl:param name="prsR"/>

        <xsl:variable name="rStyleId" select="string(w:rPr/w:rStyle/@w:val)"/>

        <xsl:variable name="prsR.updated">

            <xsl:variable name="prsR.updated1">
                <xsl:call-template name="RecursivePrsUpdateRPr">
                    <xsl:with-param name="rStyleId" select="$rStyleId"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                </xsl:call-template>
            </xsl:variable>

            <xsl:variable name="prsR.updated2">
                <xsl:call-template name="PrsUpdateRPr">
                    <xsl:with-param name="prsR" select="$prsR.updated1"/>
                </xsl:call-template>
            </xsl:variable>

            <xsl:variable name="prsRTemp3"/>
            <xsl:choose>
                <xsl:when test="$prsRTemp3=''">
                    <xsl:value-of select="$prsR.updated2"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$prsRTemp3"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="pr.listSuff"/>
        <xsl:variable name="styleMod">
            <xsl:call-template name="ApplyRPr.class"/>

            <xsl:variable name="ilfo" select="w:listPr/w:ilfo/@w:val" />
            <xsl:variable name="ilvl" select="w:listPr/w:ilvl/@w:val" />
            <xsl:variable name="ilstDef" select="$ndLists/w:list[@w:ilfo=$ilfo]/w:ilst/@w:val" />
            <xsl:variable name="listDef" select="$ndLists/w:listDef[@w:listDefId=$ilstDef]" />

            <xsl:call-template name="ApplyRPr.once">
                <xsl:with-param name="rStyleId" select="$rStyleId"/>
                <xsl:with-param name="b.bidi" select="$b.bidi"/>
                <xsl:with-param name="prsR" select="$prsR.updated"/>
            </xsl:call-template>


            <xsl:variable name="isBullets">
                <xsl:for-each select="w:listPr[1]"><xsl:call-template name="IsListBullet" /></xsl:for-each>
            </xsl:variable>

            <xsl:if test="$isBullets=$on or ancestor::w:rt">
                <xsl:text>font-style:normal;text-decoration:none;font-weight:normal;</xsl:text>
            </xsl:if>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$rStyleId='' and $styleMod=''">
                <xsl:call-template name="DisplayRContent"/>

                <xsl:if test="$pr.listSuff = $prListSuff_space"><xsl:text> </xsl:text></xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <span>

                <xsl:if test="not($rStyleId='')">
                    <xsl:attribute name="class"><xsl:value-of select="$rStyleId"/><xsl:value-of select="$charStyleSuffix"/></xsl:attribute>
                </xsl:if>

                <xsl:if test="not($styleMod='')">
                        <xsl:attribute name="style"><xsl:value-of select="$styleMod"/></xsl:attribute>
                </xsl:if>



                                <xsl:choose>
                        <xsl:when test="contains($styleMod, 'vertical-align:super') or contains($styleMod, 'vertical-align:sub')">
                                         <span>
                                <xsl:attribute name="style">font-size:smaller;</xsl:attribute>
                                        <xsl:call-template name="DisplayRContent"/>
                                             </span>
                                        </xsl:when>
                                        <xsl:otherwise>
                             <xsl:call-template name="DisplayRContent"/>
                                     </xsl:otherwise>
                                 </xsl:choose>


                </span>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template match="w:r">
        <xsl:param name="b.bidi" select="''"/>
        <xsl:param name="prsR" select="$prsRDefault"/>

        <xsl:if test="not(w:fldChar or w:instrText)">


            <xsl:variable name="instrText" select="preceding-sibling::w:r[w:instrText][1]" />


            <xsl:variable name="nInstrText" select="normalize-space(concat($instrText, ' -'))" />
            <xsl:variable name="instruction" select="substring-before($nInstrText, ' ')" />


            <xsl:choose>
                <xsl:when test="translate($instruction, $lowercase, $uppercase)='HYPERLINK'">
                    <a>
                        <xsl:variable name="href">
                            <xsl:choose>
                                <xsl:when test="contains($nInstrText,'\l')">
                                    <!-- DH: Added this part to preserve file name -->
                                    <xsl:variable name="fieldContent" select="translate(substring-after($nInstrText, concat($instruction, ' ')), '&quot;', '')"/>
                                    <xsl:variable name="linkedFile" select="normalize-space((tokenize(substring-before($fieldContent, '\l '), '(/)|(\\)'))[last()])"/>
                                    <xsl:value-of select="$linkedFile"/>
                                    <!-- End of added part -->
                                    <xsl:text>#</xsl:text>
                                    <xsl:value-of select="translate(substring-before(substring-after($nInstrText, '\l '),' '),'&quot;', '')"/></xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="translate(substring-before(substring-after($nInstrText, concat($instruction, ' ')),' '),'&quot;', '')"/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>

                        <xsl:if test="not($href='')"><xsl:attribute name="href"><xsl:value-of select="$href"/></xsl:attribute></xsl:if>

                        <xsl:if test="contains($nInstrText,'\t') or contains($nInstrText, '\n')">
                            <xsl:attribute name="target">
                                <xsl:choose>
                                    <xsl:when test="contains($nInstrText, '\n')">
                                        <xsl:text>_new</xsl:text>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:value-of select="translate(substring-before(substring-after($nInstrText, '\t '),' '),'&quot;', '')"/>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:attribute>
                        </xsl:if>

                        <xsl:if test="contains($nInstrText,'\o')">
                            <xsl:attribute name="title">
                                <xsl:value-of select="translate(substring-before(substring-after($nInstrText, '\o '),' '),'&quot;', '')"/>
                            </xsl:attribute>
                        </xsl:if>

                        <xsl:call-template name="DisplayR">
                            <xsl:with-param name="b.bidi" select="$b.bidi"/>
                            <xsl:with-param name="prsR" select="$prsR"/>
                        </xsl:call-template>

                    </a>
                </xsl:when>

                <xsl:otherwise>


                    <xsl:call-template name="DisplayR">
                        <xsl:with-param name="b.bidi" select="$b.bidi"/>
                        <xsl:with-param name="prsR" select="$prsR"/>
                    </xsl:call-template>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>


        <xsl:if test="w:instrText">
            <xsl:variable name="refcontent" select="normalize-space(w:instrText)"/>

            <xsl:choose>
                <xsl:when test="starts-with($refcontent, 'REF')">
                    <xsl:variable name="href">
                        <xsl:choose>
                            <xsl:when test="contains($refcontent, '\')">
                                <xsl:value-of select="normalize-space(substring-after(substring-before($refcontent, '\'), 'REF '))"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="normalize-space(substring-after($refcontent, 'REF '))"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:variable>
                    <!-- Ignore cross-references -->
                    <!--<a class="xref" href="{$href}"></a> -->
                </xsl:when>
                <xsl:when test="contains($refcontent, 'XE')">
                    <a class="index_term" href="{normalize-space(substring-after($refcontent, 'XE '))}"></a>
                </xsl:when>
                <xsl:when test="contains($refcontent, 'xe')">
                    <a class="index_term" href="{normalize-space(substring-after($refcontent, 'xe '))}"></a>
                </xsl:when>
                <xsl:when test="starts-with($refcontent, 'TOC')">
                    <a class="toc" href="{normalize-space(substring-after($refcontent, 'TOC '))}"></a>
                </xsl:when>
                <!-- DH: The HYPERLINK bit was commented out but it seems useful to me! I added the '\l' part -->
                <!-- This is probably not useful -->
                <xsl:when test="starts-with($refcontent, 'HYPERLINK')">
                    <xsl:choose>
                        <xsl:when test="contains($refcontent, '\l')">
                            <a href="{concat('#', normalize-space(translate(substring-after($refcontent, '\l '), '&#34;', '')))}">
                                <xsl:apply-templates/>
                            </a>
                        </xsl:when>
                        <xsl:otherwise>
                            <a href="{normalize-space(translate(substring-after($refcontent, 'HYPERLINK '), '&#34;', ''))}">
                                <xsl:apply-templates/>
                            </a>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise>
                    <a href="{$refcontent}"></a>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>


    </xsl:template>

    <xsl:template match="w:r/w:fldChar[@w:fldCharType = 'begin']">
        <span class="field_begin"><xsl:apply-templates/></span>
    </xsl:template>
    <xsl:template match="w:r/w:fldChar[@w:fldCharType = 'end']">
        <span class="field_end"><xsl:apply-templates/></span>
    </xsl:template>

    <xsl:template match="w:r[count(preceding-sibling::w:r[w:fldChar/@w:fldCharType='begin']) = count(preceding-sibling::w:r[w:fldChar/@w:fldCharType='end'])]">
        <xsl:param name="b.bidi" select="''"/>
        <xsl:param name="prsR" select="$prsRDefault"/>
        <xsl:call-template name="DisplayR">
            <xsl:with-param name="b.bidi" select="$b.bidi"/>
            <xsl:with-param name="prsR" select="$prsR"/>
        </xsl:call-template>
    </xsl:template>


    <xsl:template match="w:pPr">
        <xsl:param name="b.bidi" select="''"/>
        <xsl:param name="prsR" select="$prsRDefault"/>
        <xsl:call-template name="DisplayR">
            <xsl:with-param name="b.bidi" select="$b.bidi"/>
            <xsl:with-param name="prsR" select="$prsR"/>
        </xsl:call-template>
    </xsl:template>


    <xsl:template name="DisplayHlink">
        <xsl:param name="b.bidi"/>
        <xsl:param name="prsR"/>

        <!-- Figure out hyperlink targets -->
        <xsl:variable name="current_rId" select="@r:id"/>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('current_rId = ', $current_rId, '; link text = ', current(), '; Relationship/@Id = ', $hyperLinks[@Id = $current_rId]/@Id, '; Target = ', $hyperLinks[@Id = $current_rId]/@Target)"/>
            <xsl:with-param name="inline" select="'true'"/>
            <xsl:with-param name="condition" select="$debug_flag = '2' and @r:id"/>
        </xsl:call-template>

        <a>
            <xsl:variable name="href">
                <xsl:choose>
                    <xsl:when test="@r:id">
                        <xsl:variable name="target">
                            <xsl:choose>
                                <xsl:when test="ancestor::w:footnote">
                                    <xsl:value-of select="//footnoteLinks/rels:Relationships/rels:Relationship[@Id = $current_rId]/@Target"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="$hyperLinks[@Id = $current_rId]/@Target"/>
                                </xsl:otherwise>    
                            </xsl:choose>
                        </xsl:variable>
                        <xsl:choose>
                            <xsl:when test="starts-with($target, 'file:///')">
                                <xsl:value-of select="tokenize($target, '\\')[last()]"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="$target"/>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:if test="@w:anchor">#<xsl:value-of select="@w:anchor"/></xsl:if> <!-- DH: added this line -->
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:for-each select="@w:dest">
                            <xsl:value-of select="."/>
                        </xsl:for-each>
                        <xsl:choose>
                            <xsl:when test="@w:anchor">#<xsl:value-of select="@w:anchor"/></xsl:when>
                            <xsl:when test="@w:arbLocation"># <xsl:value-of select="@w:arbLocation"/></xsl:when>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="not($href='')">
                    <xsl:attribute name="href">
                        <xsl:value-of select="$href"/>
                    </xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                </xsl:otherwise>
            </xsl:choose>
            <!-- Not sure if w:hyperlink/@w:target exists, cf. http://officeopenxml.com/WPhyperlink.php -->
            <xsl:for-each select="@w:target">
                <xsl:attribute name="target">
                    <xsl:value-of select="."/>
                </xsl:attribute>
            </xsl:for-each>
            <!-- Open link in new or named window, cf. http://officeopenxml.com/WPhyperlink.php -->
            <xsl:if test="@w:tgtFrame">
                <xsl:attribute name="target">
                    <xsl:value-of select="@w:tgtFrame"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:for-each select="@w:screenTip">
                <xsl:attribute name="title">
                    <xsl:value-of select="."/>
                </xsl:attribute>
            </xsl:for-each>
            <xsl:call-template name="DisplayPContent">
                <xsl:with-param name="b.bidi" select="$b.bidi"/>
                <xsl:with-param name="prsR" select="$prsR"/>
            </xsl:call-template>
        </a>
    </xsl:template>


    <xsl:template match="w:hyperlink">
        <xsl:param name="b.bidi" select="''"/>
        <xsl:param name="prsR" select="$prsRDefault"/>
        <xsl:call-template name="DisplayHlink">
            <xsl:with-param name="b.bidi" select="$b.bidi"/>
            <xsl:with-param name="prsR" select="$prsR"/>
        </xsl:call-template>
    </xsl:template>


    <xsl:template name="ApplyPPr.once">
        <xsl:param name="i.bdrRange.this"/>
        <xsl:param name="i.bdrRange.last"/>
        <xsl:param name="pr.bdrBetween"/>
        <xsl:param name="prsP"/>
        <xsl:param name="b.bidi"/>

        <xsl:if test="not($i.bdrRange.this = $i.bdrRange.last)">
            <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$pr.bdrBetween"/><xsl:with-param name="bdrSide" select="$bdrSide_bottom"/></xsl:call-template>
        </xsl:if>

        <xsl:if test="not($pr.bdrBetween = '')">
            <xsl:choose>
                <xsl:when test="$i.bdrRange.this = 1">padding:0 0 1pt;</xsl:when>
                <xsl:when test="$i.bdrRange.this = i.bdrRange.last">padding:1pt 0 0;</xsl:when>
                <xsl:otherwise>padding:1pt 0 1pt;</xsl:otherwise>
            </xsl:choose>
        </xsl:if>

        <xsl:choose>
            <xsl:when test="$b.bidi = $off">direction:ltr;unicode-bidi:normal;</xsl:when>
            <xsl:when test="$b.bidi = $on">direction:rtl;unicode-bidi:embed;text-align:right;</xsl:when>
        </xsl:choose>

        <xsl:variable name="nInd" select="substring($prsP,$iInd)"/>
        <xsl:variable name="pr.listInd"/>
        <xsl:if test="not($nInd='' and $pr.listInd='')">

            <xsl:variable name="nInd.left" select="substring-before($nInd,$sep2)"/><xsl:variable name="temp1" select="substring-after($nInd,$sep2)"/>
            <xsl:variable name="nInd.leftChars" select="substring-before($temp1,$sep2)"/><xsl:variable name="temp2" select="substring-after($temp1,$sep2)"/>
            <xsl:variable name="nInd.right" select="substring-before($temp2,$sep2)"/><xsl:variable name="temp3" select="substring-after($temp2,$sep2)"/>
            <xsl:variable name="nInd.rightChars" select="substring-before($temp3,$sep2)"/><xsl:variable name="temp4" select="substring-after($temp3,$sep2)"/>
            <xsl:variable name="nInd.hanging" select="substring-before($temp4,$sep2)"/><xsl:variable name="temp5" select="substring-after($temp4,$sep2)"/>
            <xsl:variable name="nInd.hangingChars" select="substring-before($temp5,$sep2)"/><xsl:variable name="temp6" select="substring-after($temp5,$sep2)"/>
            <xsl:variable name="nInd.firstLine" select="substring-before($temp6,$sep2)"/>
            <xsl:variable name="nInd.firstLineChars" select="substring-after($temp6,$sep2)"/>
            <xsl:variable name="pr.listInd.left" select="substring-before($pr.listInd,$sep2)"/><xsl:variable name="temp1a" select="substring-after($pr.listInd,$sep2)"/>
            <xsl:variable name="pr.listInd.leftChars" select="substring-before($temp1a,$sep2)"/><xsl:variable name="temp2a" select="substring-after($temp1a,$sep2)"/>
            <xsl:variable name="pr.listInd.hanging" select="substring-before($temp2a,$sep2)"/>
            <xsl:variable name="pr.listInd.hangingChars" select="substring-after($temp2a,$sep2)"/>

            <xsl:variable name="marginSide.before">margin-<xsl:choose><xsl:when test="$b.bidi=$on">right</xsl:when><xsl:otherwise>left</xsl:otherwise></xsl:choose>:</xsl:variable>
            <xsl:variable name="marginSide.after">margin-<xsl:choose><xsl:when test="$b.bidi=$on">left</xsl:when><xsl:otherwise>right</xsl:otherwise></xsl:choose>:</xsl:variable>

            <xsl:choose>

                <xsl:when test="not($nInd.left = '')"><xsl:value-of select="$marginSide.before"/><xsl:value-of select="number($nInd.left) div 20"/>pt;</xsl:when>
                <xsl:when test="not($nInd.leftChars = '' and $nInd.hangingChars='')">
                    <xsl:value-of select="$marginSide.before"/>
                    <xsl:variable name="leftchars"><xsl:choose><xsl:when test="$nInd.leftChars=''">0</xsl:when><xsl:otherwise><xsl:value-of select="number($nInd.leftChars) div 100"/></xsl:otherwise></xsl:choose></xsl:variable>
                    <xsl:variable name="hangingchars"><xsl:choose><xsl:when test="$nInd.hangingChars=''">0</xsl:when><xsl:otherwise><xsl:value-of select="number($nInd.hangingChars) div 100"/></xsl:otherwise></xsl:choose></xsl:variable>
                    <xsl:value-of select="$leftchars + $hangingchars"/>
                    <xsl:text>em;</xsl:text>
                </xsl:when>

                <xsl:when test="not($pr.listInd.left = '')"><xsl:value-of select="$marginSide.before"/><xsl:value-of select="number($pr.listInd.left) div 20"/>pt;</xsl:when>
                <xsl:when test="not($pr.listInd.leftChars = '' and $pr.listInd.hangingChars='')">
                    <xsl:value-of select="$marginSide.before"/>
                    <xsl:variable name="leftchars"><xsl:choose><xsl:when test="$pr.listInd.leftChars=''">0</xsl:when><xsl:otherwise><xsl:value-of select="number($pr.listInd.leftChars) div 100 * 12"/></xsl:otherwise></xsl:choose></xsl:variable>
                    <xsl:variable name="hangingchars"><xsl:choose><xsl:when test="$pr.listInd.hangingChars=''">0</xsl:when><xsl:otherwise><xsl:value-of select="number($pr.listInd.hangingChars) div 100 * 12"/></xsl:otherwise></xsl:choose></xsl:variable>
                    <xsl:value-of select="$leftchars + $hangingchars"/>
                    <xsl:text>pt;</xsl:text>
                </xsl:when>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="not($nInd.right = '')"><xsl:value-of select="$marginSide.after"/><xsl:value-of select="number($nInd.right) div 20"/>pt;</xsl:when>
                <xsl:when test="not($nInd.rightChars = '')"><xsl:value-of select="$marginSide.after"/><xsl:value-of select="number($nInd.rightChars) div 100"/>em;</xsl:when>
            </xsl:choose>

            <xsl:choose>
                <xsl:when test="not($nInd.hanging='')">text-indent:<xsl:value-of select="number($nInd.hanging) div -20"/>pt;</xsl:when>
                <xsl:when test="not($nInd.hangingChars='')">text-indent:<xsl:value-of select="number($nInd.hangingChars) div -100"/>em;</xsl:when>
                <xsl:when test="not($nInd.firstLine='')">text-indent:<xsl:value-of select="number($nInd.firstLine) div 20"/>pt;</xsl:when>
                <xsl:when test="not($nInd.firstLineChars='')">text-indent:<xsl:value-of select="number($nInd.firstLineChars) div 100"/>em;</xsl:when>
                <xsl:when test="not($pr.listInd.hanging='')">text-indent:<xsl:value-of select="number($pr.listInd.hanging) div -20"/>pt;</xsl:when>
                <xsl:when test="not($pr.listInd.hangingChars='')">text-indent:<xsl:value-of select="number($pr.listInd.hangingChars) div -100 * 12"/>pt;</xsl:when>
            </xsl:choose>
        </xsl:if>

        <xsl:variable name="fTextAutospaceO" select="substring($prsP,$iTextAutospaceO,1)"/>
        <xsl:variable name="fTextAutospaceN" select="substring($prsP,$iTextAutospaceN,1)"/>
        <xsl:choose>
            <xsl:when test="not($fTextAutospaceN = $off) and $fTextAutospaceO = $off">text-autospace:ideograph-numeric;</xsl:when>
            <xsl:when test="not($fTextAutospaceO = $off) and $fTextAutospaceN = $off">text-autospace:ideograph-other;</xsl:when>
            <xsl:when test="$fTextAutospaceO = $off and $fTextAutospaceN = $off">text-autospace:none;</xsl:when>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="ApplyPPr.many">
        <xsl:param name="cxtSpacing" select="$cxtSpacing_all"/>

        <xsl:variable name="spacing" select="w:pPr[1]/w:spacing[1]"/>
        <xsl:choose>
            <xsl:when test="($spacing/@w:before-autospacing and not($spacing/@w:before-autospacing = 'off')) or $cxtSpacing = $cxtSpacing_none or $cxtSpacing = $cxtSpacing_bottom">

            </xsl:when>
            <xsl:when test="$spacing/@w:before">margin-top:<xsl:value-of select="$spacing/@w:before div 20"/>pt;</xsl:when>
            <xsl:when test="$spacing/@w:before-lines">margin-top:<xsl:value-of select="$spacing/@w:before-lines *.12"/>pt;</xsl:when>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test="($spacing/@w:after-autospacing and not($spacing/@w:after-autospacing = 'off')) or $cxtSpacing = $cxtSpacing_none or $cxtSpacing = $cxtSpacing_top">

            </xsl:when>

            <xsl:when test="$spacing/@w:after">margin-bottom:<xsl:value-of select="$spacing/@w:after div 20"/>pt;</xsl:when>
            <xsl:when test="$spacing/@w:after-lines">margin-bottom:<xsl:value-of select="$spacing/@w:after-lines *.12"/>pt;</xsl:when>
        </xsl:choose>
        <xsl:for-each select="w:pPr[1]">

            <xsl:for-each select="w:snapToGrid[1]">
                <xsl:choose>
                    <xsl:when test="@w:val = 'off'">layout-grid-mode:char;</xsl:when>
                    <xsl:otherwise>layout-grid-mode:both;</xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>

            <xsl:for-each select="w:keepNext[1]">
                <xsl:choose>
                    <xsl:when test="@w:val = 'off'">page-break-after:auto;</xsl:when>
                    <xsl:otherwise>page-break-after:avoid;</xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>

            <xsl:for-each select="w:pageBreakBefore[1]">
                <xsl:choose>
                    <xsl:when test="@w:val = 'off'">page-break-before:auto;</xsl:when>
                    <xsl:otherwise>page-break-before:always;</xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>


    <xsl:template name="RecursiveApplyPPr.class">
        <xsl:if test="w:basedOn">
            <xsl:variable name="baseStyleName" select="w:basedOn[1]/@w:val" />
            <xsl:variable name="sParaStyleBase" select="($nsStyles[@w:styleId=$baseStyleName])[1]"/>
            <xsl:for-each select="$sParaStyleBase"><xsl:call-template name="RecursiveApplyPPr.class" /></xsl:for-each>
        </xsl:if>


        <xsl:call-template name="ApplyPPr.class"/>
    </xsl:template>


    <xsl:template name="ApplyPPr.class">
        <xsl:apply-templates select="w:pPr[1]/*" mode="ppr"/>
    </xsl:template>

    <xsl:template match="w:textDirection" mode="ppr"><xsl:call-template name="ApplyTextDirection"/></xsl:template>

    <xsl:template match="w:spacing[@w:line-rule or @w:line]" mode="ppr">
        <xsl:choose>
            <xsl:when test="not(@w:line-rule) or @w:line-rule = 'auto'">line-height:<xsl:value-of select="@w:line div 240"/>;</xsl:when>
            <xsl:otherwise>line-height:<xsl:value-of select="@w:line div 20"/>pt;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:topLinePunct" mode="ppr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">punctuation-trim:none;</xsl:when>
            <xsl:otherwise>punctuation-trim:leading;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:overflowPunct" mode="ppr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">punctuation-wrap:simple;</xsl:when>
            <xsl:otherwise>punctuation-wrap:hanging;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:jc" mode="ppr">
        <xsl:choose>
            <xsl:when test="@w:val = 'left'">text-align:left;</xsl:when>
            <xsl:when test="@w:val = 'center'">text-align:center;</xsl:when>
            <xsl:when test="@w:val = 'right'">text-align:right;</xsl:when>
            <xsl:when test="@w:val = 'both'">text-align:justify;text-justify:inter-ideograph;</xsl:when>
            <xsl:when test="@w:val = 'distribute'">text-align:justify;text-justify:distribute-all-lines;</xsl:when>
            <xsl:when test="@w:val = 'low-kashida'">text-align:justify;text-justify:kashida;text-kashida:0%;</xsl:when>
            <xsl:when test="@w:val = 'medium-kashida'">text-align:justify;text-justify:kashida;text-kashida:10%;</xsl:when>
            <xsl:when test="@w:val = 'high-kashida'">text-align:justify;text-justify:kashida;text-kashida:20%;</xsl:when>
            <xsl:when test="@w:val = 'thai-distribute'">text-align:justify;text-justify:inter-cluster;</xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:textAlignment" mode="ppr">
        <xsl:choose>
            <xsl:when test="@w:val = 'top'">vertical-align:top;</xsl:when>
            <xsl:when test="@w:val = 'center'">vertical-align:middle;</xsl:when>
            <xsl:when test="@w:val = 'baseline'">vertical-align:baseline;</xsl:when>
            <xsl:when test="@w:val = 'bottom'">vertical-align:bottom;</xsl:when>
            <xsl:when test="@w:val = 'auto'">vertical-align:baseline;</xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:wordWrap" mode="ppr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">word-break:break-all;</xsl:when>
            <xsl:otherwise>word-break:normal;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="*" mode="ppr"/>


    <xsl:template name="DisplayPContent">
        <xsl:param name="b.bidi"/>
        <xsl:param name="prsR"/>
        <xsl:call-template name="DisplayRBorder">
            <xsl:with-param name="b.bidi" select="$b.bidi"/>
            <xsl:with-param name="prsR" select="$prsR"/>
        </xsl:call-template>

        <xsl:if test="count(*[not(name()='w:pPr')])=0"><xsl:text disable-output-escaping="yes">&#160;</xsl:text></xsl:if>
    </xsl:template>

    <xsl:template name="GetPStyleId">
        <xsl:choose>
            <xsl:when test="w:pPr/w:pStyle/@w:val">
                <xsl:value-of select="w:pPr/w:pStyle/@w:val"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="$paraStyleID_Default"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="RecursiveApplyPPr.many">
        <xsl:if test="w:basedOn">
            <xsl:variable name="baseStyleName" select="w:basedOn[1]/@w:val" />
            <xsl:variable name="sParaStyleBase" select="($nsStyles[@w:styleId=$baseStyleName])[1]"/>
            <xsl:for-each select="$sParaStyleBase"><xsl:call-template name="RecursiveApplyPPr.many" /></xsl:for-each>
        </xsl:if>


        <xsl:call-template name="ApplyPPr.many"/>

    </xsl:template>


    <xsl:template match="w:p">
        <xsl:param name="bdrBetween" select="''"/>
        <xsl:param name="prsPAccum" select="''"/>
        <xsl:param name="prsP" select="$prsPDefault"/>
        <xsl:param name="prsR" select="$prsRDefault"/>

        <xsl:if test="not(w:pPr/w:pStyle/@w:val='z-TopofForm') and not(w:pPr/w:pStyle/@w:val='z-BottomofForm')">
            <xsl:value-of select="'&#x0a;'"/>
            <p>

                <xsl:variable name="pStyleId">
                    <xsl:call-template name="GetPStyleId"/>
                </xsl:variable>
                <xsl:variable name="sMappedStyleName" select="translate(($nsStyles[@w:styleId=$pStyleId])[1]/w:name/@w:val, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ ', 'abcdefghijklmnopqrstuvwxyz')"/>
                <xsl:attribute name="class">
                    <xsl:value-of select="$sMappedStyleName"/>
                </xsl:attribute>
                <xsl:variable name="sParaStyleName" select="($nsStyles[@w:styleId=$pStyleId])[1]"/>
                <xsl:variable name="b.bidi">
                    <xsl:for-each select="w:pPr[1]/w:bidi[1]">
                        <xsl:choose>
                            <xsl:when test="@w:val = 'off'">
                                <xsl:value-of select="$off"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="$on"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:variable>


        <xsl:variable name="prsR.updated">
            <xsl:call-template name="PrsUpdateRPr">
                <xsl:with-param name="ndPrContainer" select="$sParaStyleName"/>
                <xsl:with-param name="prsR" select="$prsR"/>
            </xsl:call-template>
        </xsl:variable>


        <xsl:variable name="prsP.updated1">
            <xsl:call-template name="PrsUpdatePPr">
                <xsl:with-param name="ndPrContainer" select="$sParaStyleName"/>
                <xsl:with-param name="prsP" select="$prsP"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="prsP.updated">
            <xsl:call-template name="PrsUpdatePPr">
                <xsl:with-param name="prsP" select="$prsP.updated1"/>
            </xsl:call-template>
        </xsl:variable>


        <xsl:variable name="styleMod">

            <xsl:value-of select="$prsPAccum"/>


            <xsl:for-each select="$sParaStyleName"><xsl:call-template name="RecursiveApplyPPr.many"/></xsl:for-each>


            <xsl:call-template name="ApplyPPr.many">
                <xsl:with-param name="cxtSpacing">
                    <xsl:variable name="cspacing" select="$sParaStyleName/w:pPr[1]/w:contextualSpacing[1]"/>
                    <xsl:if test="$cspacing and not($cspacing/@w:val = 'off')">
                        <xsl:if test="following-sibling::*[1]/w:pPr[1]/w:pStyle[1]/@w:val = $pStyleId"><xsl:value-of select="$cxtSpacing_top"/></xsl:if>
                        <xsl:if test="preceding-sibling::*[1]/w:pPr[1]/w:pStyle[1]/@w:val = $pStyleId"><xsl:value-of select="$cxtSpacing_bottom"/></xsl:if>
                    </xsl:if>
                </xsl:with-param>
            </xsl:call-template>

            <xsl:call-template name="ApplyPPr.class"/>

            <xsl:call-template name="ApplyPPr.once">
                <xsl:with-param name="b.bidi" select="$b.bidi"/>
                <xsl:with-param name="prsP" select="$prsP.updated"/>
                <xsl:with-param name="i.bdrRange.this" select="position()"/>
                <xsl:with-param name="i.bdrRange.last" select="last()"/>
                <xsl:with-param name="pr.bdrBetween" select="$bdrBetween"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:if test="not($styleMod='')"><xsl:attribute name="style"><xsl:value-of select="$styleMod"/></xsl:attribute></xsl:if>

        <!-- Take out redundant span - DH -->
        <!--<span>
            <xsl:attribute name="class">
                <xsl:value-of select="$pStyleId"/>
                <xsl:value-of select="$charStyleSuffix"/>
            </xsl:attribute>-->
            <xsl:call-template name="DisplayPContent">
                <xsl:with-param name="b.bidi" select="$b.bidi"/>
                <xsl:with-param name="prsR" select="$prsR.updated"/>
            </xsl:call-template>
        <!--</span>-->
        </p>
        </xsl:if>
    </xsl:template>


    <xsl:template name="DisplayBodyContent">

        <xsl:param name="ns.content" select="descendant::*[(parent::wx:sect or parent::wx:sub-section) and not(name()='wx:sub-section')]"/>
        <xsl:param name="prsPAccum" select="''"/>
        <xsl:param name="prsP" select="$prsPDefault"/>
        <xsl:param name="prsR" select="$prsRDefault"/>
        <xsl:apply-templates>
            <xsl:with-param name="ns.content" select="$ns.content"/>
            <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
            <xsl:with-param name="prsP" select="$prsP"/>
            <xsl:with-param name="prsR" select="$prsR"/>
        </xsl:apply-templates>

        <xsl:if test="count($ns.content)=0"><xsl:text disable-output-escaping="yes">&#160;</xsl:text></xsl:if>
    </xsl:template>


    <xsl:template name="RecursiveApplyTcPr.class">
        <xsl:if test="w:basedOn">
            <xsl:variable name="baseStyleName" select="w:basedOn[1]/@w:val" />
            <xsl:variable name="sTblStyleBase" select="($nsStyles[@w:styleId=$baseStyleName])[1]"/>
            <xsl:for-each select="$sTblStyleBase"><xsl:call-template name="RecursiveApplyTcPr.class" /></xsl:for-each>
        </xsl:if>


        <xsl:call-template name="ApplyTcPr.class"/>
    </xsl:template>


    <xsl:template name="ApplyTcPr.class">
        <xsl:apply-templates select="w:tcPr[1]/*" mode="tcpr"/>
    </xsl:template>

    <xsl:template match="w:textFlow" mode="tcpr"><xsl:call-template name="ApplyTextDirection"/></xsl:template>

    <xsl:template match="w:tcFitText" mode="tcpr">
        <xsl:if test="not(@w:val = 'off')">text-fit:100%;</xsl:if>
    </xsl:template>

    <xsl:template match="w:vAlign" mode="tcpr">
        <xsl:choose>
            <xsl:when test="@w:val = 'center'">vertical-align:middle;</xsl:when>
            <xsl:when test="@w:val = 'bottom'">vertical-align:bottom;</xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:noWrap" mode="tcpr">
        <xsl:choose>
            <xsl:when test="@w:val = 'off'">white-space:normal;</xsl:when>
            <xsl:otherwise>white-space:nowrap;</xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:tcW" mode="tcpr">width:<xsl:call-template name="EvalTableWidth"/>;</xsl:template>
    <xsl:template match="*" mode="tcpr"/>


    <xsl:template name="ApplyExtraCornerBorders">
        <xsl:param name="cnfType" />
        <xsl:param name="sTblStyleName" />
        <xsl:choose>
            <xsl:when test="$cnfType=$cnfNWCell"><xsl:call-template name="ApplyExtraCornerBordersNW"><xsl:with-param name="sTblStyle" select="$sTblStyleName" /></xsl:call-template></xsl:when>
            <xsl:when test="$cnfType=$cnfNECell"><xsl:call-template name="ApplyExtraCornerBordersNE"><xsl:with-param name="sTblStyle" select="$sTblStyleName" /></xsl:call-template></xsl:when>
            <xsl:when test="$cnfType=$cnfSECell"><xsl:call-template name="ApplyExtraCornerBordersSE"><xsl:with-param name="sTblStyle" select="$sTblStyleName" /></xsl:call-template></xsl:when>
            <xsl:when test="$cnfType=$cnfSWCell"><xsl:call-template name="ApplyExtraCornerBordersSW"><xsl:with-param name="sTblStyle" select="$sTblStyleName" /></xsl:call-template></xsl:when>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="ApplyExtraCornerBordersNW">
        <xsl:param name="sTblStyle" />


        <xsl:variable name="firstColBorders" select="$sTblStyle/w:tblStylePr[@w:type=$cnfFirstCol][1]/w:tcPr[1]/w:tcBorders[1]" />
        <xsl:variable name="firstRowBorders" select="$sTblStyle/w:tblStylePr[@w:type=$cnfFirstRow][1]/w:tcPr[1]/w:tcBorders[1]" />


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="ApplyExtraCornerBordersNE">
        <xsl:param name="sTblStyle" />


        <xsl:variable name="lastColBorders"    select="$sTblStyle/w:tblStylePr[@w:type=$cnfLastCol][1]/w:tcPr[1]/w:tcBorders[1]" />
        <xsl:variable name="firstRowBorders" select="$sTblStyle/w:tblStylePr[@w:type=$cnfFirstRow][1]/w:tcPr[1]/w:tcBorders[1]" />


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstRowBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="ApplyExtraCornerBordersSE">
        <xsl:param name="sTblStyle" />


        <xsl:variable name="lastColBorders"    select="$sTblStyle/w:tblStylePr[@w:type=$cnfLastCol][1]/w:tcPr[1]/w:tcBorders[1]" />
        <xsl:variable name="lastRowBorders" select="$sTblStyle/w:tblStylePr[@w:type=$cnfLastRow][1]/w:tcPr[1]/w:tcBorders[1]" />


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastColBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>

    </xsl:template>

    <xsl:template name="ApplyExtraCornerBordersSW">
        <xsl:param name="sTblStyle" />


        <xsl:variable name="firstColBorders"    select="$sTblStyle/w:tblStylePr[@w:type=$cnfFirstCol][1]/w:tcPr[1]/w:tcBorders[1]" />
        <xsl:variable name="lastRowBorders" select="$sTblStyle/w:tblStylePr[@w:type=$cnfLastRow][1]/w:tcPr[1]/w:tcBorders[1]" />


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:top[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_top"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:left[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_left"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:right[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_right"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$lastRowBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>

        <xsl:call-template name="ApplyBorderPr">
            <xsl:with-param name="pr.bdr"><xsl:for-each select="$firstColBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr" /></xsl:for-each></xsl:with-param>
            <xsl:with-param name="bdrSide" select="$bdrSide_bottom"/>
        </xsl:call-template>
    </xsl:template>


    <xsl:template name="ApplyTcBordersFromCnf">
        <xsl:param name="tcBorders" />
        <xsl:param name="sTblStyleName" />
        <xsl:param name="cnfType" />
        <xsl:param name="thisRow"/>
        <xsl:param name="lastRow"/>
        <xsl:param name="bdr.top"/>
        <xsl:param name="bdr.left"/>
        <xsl:param name="bdr.bottom"/>
        <xsl:param name="bdr.right"/>
        <xsl:param name="bdrSide_right.bidi" />
        <xsl:param name="bdrSide_left.bidi" />



        <xsl:variable name="thisBdr.top">
            <xsl:choose>
                <xsl:when test="$tcBorders/w:top"><xsl:for-each select="$tcBorders/w:top[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                <xsl:when test="not($cnfType='')">
                    <xsl:choose>
                        <xsl:when test="$cnfType=$cnfBand1Vert or $cnfType=$cnfBand2Vert or $cnfType=$cnfFirstCol or $cnfType=$cnfLastCol">
                            <xsl:variable name="p.cnfFirstRow" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfFirstRow][1]"/>
                            <xsl:choose>
                                <xsl:when test="$p.cnfFirstRow and $thisRow=2"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:top[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:when test="not($p.cnfFirstRow) and $thisRow=1"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:top[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:otherwise><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:insideH[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:top[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$bdr.top"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="thisBdr.bottom">
            <xsl:choose>
                <xsl:when test="$tcBorders/w:bottom"><xsl:for-each select="$tcBorders/w:bottom[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                <xsl:when test="not($cnfType='')">
                    <xsl:choose>
                        <xsl:when test="$cnfType=$cnfBand1Vert or $cnfType=$cnfBand2Vert or $cnfType=$cnfFirstCol or $cnfType=$cnfLastCol">
                            <xsl:variable name="p.cnfLastRow" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfLastRow][1]"/>
                            <xsl:choose>
                                <xsl:when test="$p.cnfLastRow and $thisRow=$lastRow - 1"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:bottom[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:when test="not($p.cnfLastRow) and $thisRow=$lastRow"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:bottom[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:otherwise><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:insideH[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:bottom[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$bdr.bottom"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="thisBdr.left">
            <xsl:choose>
                <xsl:when test="$tcBorders/w:left"><xsl:for-each select="$tcBorders/w:left[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                <xsl:when test="not($cnfType='')">
                    <xsl:choose>
                        <xsl:when test="$cnfType=$cnfBand1Horz or $cnfType=$cnfBand2Horz">
                            <xsl:variable name="p.cnfFirstCol" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfFirstCol][1]"/>
                            <xsl:choose>
                                <xsl:when test="$p.cnfFirstCol and position()=2"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:left[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:when test="not($p.cnfFirstCol) and position()=1"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:left[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:otherwise><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:insideV[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:when test="$cnfType=$cnfFirstRow or $cnfType=$cnfLastRow">
                            <xsl:choose>
                                <xsl:when test="position()=1"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:left[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:otherwise><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:insideV[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:left[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$bdr.left"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="thisBdr.right">
            <xsl:choose>
                <xsl:when test="$tcBorders/w:right"><xsl:for-each select="$tcBorders/w:right[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                <xsl:when test="not($cnfType='')">
                    <xsl:choose>
                        <xsl:when test="$cnfType=$cnfBand1Horz or $cnfType=$cnfBand2Horz">
                            <xsl:variable name="p.cnfLastCol" select="$sTblStyleName/w:tblStylePr[@w:type=$cnfLastCol][1]"/>
                            <xsl:choose>
                                <xsl:when test="$p.cnfLastCol and position()=last() - 1"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:right[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:when test="not($p.cnfLastCol) and position()=last()"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:right[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:otherwise><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:insideV[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:when test="$cnfType=$cnfFirstRow or $cnfType=$cnfLastRow">
                            <xsl:choose>
                                <xsl:when test="position()=last()"><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:right[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:when>
                                <xsl:otherwise><xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:insideV[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each></xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="$sTblStyleName/w:tblStylePr[@w:type=$cnfType][1]/w:tcPr[1]/w:tcBorders[1]/w:right[1]"><xsl:call-template name="GetBorderPr"/></xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$bdr.right"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>


        <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$thisBdr.top"/><xsl:with-param name="bdrSide" select="$bdrSide_top"/></xsl:call-template>
        <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$thisBdr.right"/><xsl:with-param name="bdrSide" select="$bdrSide_right.bidi"/></xsl:call-template>
        <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$thisBdr.bottom"/><xsl:with-param name="bdrSide" select="$bdrSide_bottom"/></xsl:call-template>
        <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$thisBdr.left"/><xsl:with-param name="bdrSide" select="$bdrSide_left.bidi"/></xsl:call-template>

    </xsl:template>


    <xsl:template name="ApplyTcPr.once">
        <xsl:param name="cellspacing"/>
        <xsl:param name="cellpadding.default"/>
        <xsl:param name="cellpadding.custom"/>
        <xsl:param name="bdr.top"/>
        <xsl:param name="bdr.left"/>
        <xsl:param name="bdr.bottom"/>
        <xsl:param name="bdr.right"/>
        <xsl:param name="bdr.insideV"/>
        <xsl:param name="thisRow"/>
        <xsl:param name="lastRow"/>
        <xsl:param name="sTblStyleName"/>
        <xsl:param name="cnfRow"/>
        <xsl:param name="cnfCol"/>
        <xsl:param name="b.bidivisual"/>







        <xsl:variable name="cnfType">
            <xsl:if test="not($cnfRow='' and $cnfCol='')">
                <xsl:call-template name="GetCnfType"><xsl:with-param name="cnfRow" select="$cnfRow"/><xsl:with-param name="cnfCol" select="$cnfCol"/>
                </xsl:call-template>
            </xsl:if>
        </xsl:variable>

        <xsl:variable name="cnfTypeRow">
            <xsl:if test="not($cnfRow='')">
                <xsl:call-template name="GetCnfTypeRow"><xsl:with-param name="cnfRow" select="$cnfRow"/></xsl:call-template>
            </xsl:if>
        </xsl:variable>

        <xsl:variable name="cnfTypeCol">
            <xsl:if test="not($cnfCol='')">
                <xsl:call-template name="GetCnfTypeCol"><xsl:with-param name="cnfCol" select="$cnfCol"/></xsl:call-template>
            </xsl:if>
        </xsl:variable>

        <xsl:variable name="tcborders" select="w:tcPr[1]/w:tcBorders[1]"/>

        <xsl:variable name="bdrSide_left.bidi">
            <xsl:choose>
                <xsl:when test="$b.bidivisual = $on"><xsl:value-of select="$bdrSide_right"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$bdrSide_left"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="bdrSide_right.bidi">
            <xsl:choose>
                <xsl:when test="$b.bidivisual = $on"><xsl:value-of select="$bdrSide_left"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$bdrSide_right"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>





        <xsl:for-each select="$sTblStyleName/w:tblPr[1]/w:tblBorders[1]">
            <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.top"/><xsl:with-param name="bdrSide" select="$bdrSide_top"/></xsl:call-template>
            <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.bottom"/><xsl:with-param name="bdrSide" select="$bdrSide_bottom"/></xsl:call-template>
            <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.right"/><xsl:with-param name="bdrSide" select="$bdrSide_right.bidi"/></xsl:call-template>
            <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.left"/><xsl:with-param name="bdrSide" select="$bdrSide_left.bidi"/></xsl:call-template>
        </xsl:for-each>


        <xsl:call-template name="ApplyExtraCornerBorders"><xsl:with-param name="cnfType" select="$cnfType" /><xsl:with-param name="sTblStyleName" select="$sTblStyleName" /></xsl:call-template>


        <xsl:call-template name="ApplyTcBordersFromCnf">
            <xsl:with-param name="cnfType" select="$cnfTypeRow" />
            <xsl:with-param name="sTblStyleName" select="$sTblStyleName" />
            <xsl:with-param name="tcBorders" select="$tcborders" />
            <xsl:with-param name="bdrSide_right.bidi" select="$bdrSide_right.bidi" />
            <xsl:with-param name="bdrSide_left.bidi" select="$bdrSide_left.bidi" />
            <xsl:with-param name="thisRow" select="$thisRow"/>
            <xsl:with-param name="lastRow" select="$lastRow"/>

            <xsl:with-param name="bdr.top" select="$bdr.top"/>
            <xsl:with-param name="bdr.left" select="$bdr.left"/>
            <xsl:with-param name="bdr.right" select="$bdr.right"/>
            <xsl:with-param name="bdr.bottom" select="$bdr.bottom"/>
        </xsl:call-template>


        <xsl:call-template name="ApplyTcBordersFromCnf">
            <xsl:with-param name="cnfType" select="$cnfTypeCol" />
            <xsl:with-param name="sTblStyleName" select="$sTblStyleName" />
            <xsl:with-param name="tcBorders" select="$tcborders" />
            <xsl:with-param name="bdrSide_right.bidi" select="$bdrSide_right.bidi" />
            <xsl:with-param name="bdrSide_left.bidi" select="$bdrSide_left.bidi" />
            <xsl:with-param name="thisRow" select="$thisRow"/>
            <xsl:with-param name="lastRow" select="$lastRow"/>

            <xsl:with-param name="bdr.top" select="$bdr.top"/>
            <xsl:with-param name="bdr.left" select="$bdr.left"/>
            <xsl:with-param name="bdr.right" select="$bdr.right"/>
            <xsl:with-param name="bdr.bottom" select="$bdr.bottom"/>
        </xsl:call-template>






        <xsl:variable name="cellpadding.custom.merged">

            <xsl:variable name="temp.direct">
                <xsl:for-each select="w:tcPr[1]/w:tcMar[1]"><xsl:call-template name="ApplyCellMar"/></xsl:for-each>
            </xsl:variable>
            <xsl:value-of select="$temp.direct"/>
            <xsl:if test="$temp.direct=''">

                <xsl:variable name="temp.cnf">
                    <xsl:for-each select="$sTblStyleName">
                        <xsl:call-template name="GetCnfPr.cell">
                            <xsl:with-param name="type" select="$prrCustomCellpadding"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                        </xsl:call-template>
                    </xsl:for-each>
                </xsl:variable>
                <xsl:value-of select="$temp.cnf"/>
                <xsl:if test="$temp.cnf=''">

                    <xsl:value-of select="$cellpadding.custom"/>
                </xsl:if>
            </xsl:if>
        </xsl:variable>
        <xsl:variable name="cellpadding.default.merged">

            <xsl:variable name="temp.cnf">
                <xsl:for-each select="$sTblStyleName">
                    <xsl:call-template name="GetCnfPr.cell">
                        <xsl:with-param name="type" select="$prrDefaultCellpadding"/><xsl:with-param name="cnfCol" select="$cnfCol"/><xsl:with-param name="cnfRow" select="$cnfRow"/>
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:variable>
            <xsl:value-of select="$temp.cnf"/>
            <xsl:if test="$temp.cnf=''">

                <xsl:value-of select="$cellpadding.default"/>
            </xsl:if>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$cellpadding.custom.merged = 'none' and not($cellpadding.default.merged='')"><xsl:value-of select="$cellpadding.default.merged"/></xsl:when>
            <xsl:when test="not($cellpadding.custom.merged='')"><xsl:value-of select="$cellpadding.custom.merged"/></xsl:when>
            <xsl:when test="not($cellpadding.default.merged='')"><xsl:value-of select="$cellpadding.default.merged"/></xsl:when>
        </xsl:choose>
    </xsl:template>


    <xsl:template match="w:tc">
        <xsl:param name="sTblStyleName"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:param name="cellspacing"/>
        <xsl:param name="cellpadding.default"/>
        <xsl:param name="cellpadding.custom"/>
        <xsl:param name="bdr.top"/>
        <xsl:param name="bdr.left"/>
        <xsl:param name="bdr.bottom"/>
        <xsl:param name="bdr.right"/>
        <xsl:param name="bdr.insideV"/>
        <xsl:param name="bdr.insideH"/>
        <xsl:param name="thisRow"/>
        <xsl:param name="lastRow"/>
        <xsl:param name="cnfRow"/>
        <xsl:param name="b.bidivisual"/>
        <xsl:param name="table_celltype" select="'td'"/>

        <xsl:variable name="cnfCol" select="string(w:tcPr[1]/w:cnfStyle[1]/@w:val)"/>
        <xsl:variable name="vmerge" select="w:tcPr[1]/w:vmerge[1]"/>
        <xsl:variable name="me" select="." />
        <xsl:variable name="tblCount" select="count(ancestor::w:tbl)" />
        <xsl:variable name="meInContext" select="ancestor::w:tr[1]/*[count($me|descendant-or-self::*)=count(descendant-or-self::*)]" />
        <xsl:variable name="before" select="count($meInContext/preceding-sibling::*[descendant-or-self::*[name()='w:tc' and (count(ancestor::w:tbl)=$tblCount)]])" />
        <xsl:variable name="after" select="count($meInContext/following-sibling::*[descendant-or-self::*[name()='w:tc' and (count(ancestor::w:tbl)=$tblCount)]])" />

        <xsl:if test="not($vmerge and not($vmerge/@w:val))">
            <xsl:value-of select="$debug_newline"/>
            <xsl:element name="{$table_celltype}">

        <xsl:if test="$sTblStyleName/@w:styleId != ''">
            <xsl:attribute name="class">
                <xsl:value-of select="$sTblStyleName/@w:styleId"/>
            </xsl:attribute>
        </xsl:if>

            <xsl:for-each select="w:tcPr[1]/w:gridSpan[1]/@w:val">
                <xsl:attribute name="colspan">
                    <xsl:value-of select="."/>
                </xsl:attribute>
            </xsl:for-each>

            <xsl:variable name="rowspan">
                <xsl:choose>
                    <xsl:when test="not($vmerge)">1</xsl:when>
                    <xsl:when test="$vmerge/@wx:rowspan"><xsl:value-of select="$vmerge/@wx:rowspan"/></xsl:when>


                    <xsl:otherwise>
                        <xsl:variable name="myRow" select="ancestor::w:tr[1]" />
                        <xsl:variable name="myRowInContext" select="$myRow/ancestor::w:tbl[1]/*[count($myRow|descendant-or-self::*)=count(descendant-or-self::*)]" />
                        <xsl:variable name="belowMe" select="$myRowInContext/following-sibling::*//w:tc[count(ancestor::w:tbl)=$tblCount][$before + 1]" />
                        <xsl:variable name="NextRestart" select="($belowMe//w:tcPr/w:vmerge[@w:val='restart'])[1]" />
                        <xsl:variable name="NextRestartInContext" select="$NextRestart/ancestor::w:tbl[1]/*[count($NextRestart|descendant-or-self::*)=count(descendant-or-self::*)]" />
                        <xsl:variable name="mergesAboveMe"                                select="count($myRowInContext/preceding-sibling::*[(descendant-or-self::*[name()='w:tc'])[$before + 1][descendant-or-self::*[name()='w:vmerge']]])" />
                        <xsl:variable name="mergesAboveNextRestart" select="count($NextRestartInContext/preceding-sibling::*[(descendant-or-self::*[name()='w:tc'])[$before + 1][descendant-or-self::*[name()='w:vmerge']]])" />

                        <xsl:choose>
                            <xsl:when test="$NextRestart"><xsl:value-of select="$mergesAboveNextRestart - $mergesAboveMe"/></xsl:when>
                            <xsl:when test="$vmerge/@w:val"><xsl:value-of select="count($belowMe[descendant-or-self::*[name()='w:vmerge']]) + 1" /></xsl:when>
                            <xsl:otherwise>1</xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:variable>

            <xsl:if test="$vmerge">
                <xsl:attribute name="rowspan">
                    <xsl:value-of select="$rowspan"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:variable name="lastRow.updated" select="$lastRow - $rowspan + 1"/>

            <xsl:variable name="bdr.bottom.updated">
                <xsl:choose>
                    <xsl:when test="$cellspacing='' and $thisRow=$lastRow.updated"><xsl:value-of select="$bdr.bottom"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$bdr.insideH"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:variable name="bdr.left.updated">
                <xsl:choose>
                    <xsl:when test="$cellspacing='' and $before=0"><xsl:value-of select="$bdr.left"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$bdr.insideV"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <xsl:variable name="bdr.right.updated">
                <xsl:choose>
                    <xsl:when test="$cellspacing='' and $after=0"><xsl:value-of select="$bdr.right"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$bdr.insideV"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>

            <xsl:attribute name="style">

                <xsl:if test="not($cnfRow='' and $cnfCol='')">
                    <xsl:for-each select="$sTblStyleName">
                        <xsl:call-template name="GetCnfPr.all">
                            <xsl:with-param name="type" select="$prrApplyTcPr"/>
                            <xsl:with-param name="cnfRow" select="$cnfRow"/><xsl:with-param name="cnfCol" select="$cnfCol"/>
                        </xsl:call-template>
                    </xsl:for-each>
                </xsl:if>

                <xsl:call-template name="ApplyTcPr.class"/>
                <xsl:call-template name="ApplyTcPr.once">
                    <xsl:with-param name="thisRow" select="$thisRow"/><xsl:with-param name="lastRow" select="$lastRow.updated"/>
                    <xsl:with-param name="cellspacing" select="$cellspacing"/><xsl:with-param name="cellpadding.default" select="$cellpadding.default"/><xsl:with-param name="cellpadding.custom" select="$cellpadding.custom"/>
                    <xsl:with-param name="bdr.top" select="$bdr.top"/><xsl:with-param name="bdr.left" select="$bdr.left.updated"/><xsl:with-param name="bdr.right" select="$bdr.right.updated"/><xsl:with-param name="bdr.bottom" select="$bdr.bottom.updated"/>
                    <xsl:with-param name="bdr.insideV" select="$bdr.insideV"/>
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfRow" select="$cnfRow"/><xsl:with-param name="cnfCol" select="$cnfCol"/>
                    <xsl:with-param name="b.bidivisual" select="$b.bidivisual"/>
                </xsl:call-template>
            </xsl:attribute>
            <xsl:choose>
                <xsl:when test="$cnfRow='' and $cnfCol=''">

                    <xsl:call-template name="DisplayBodyContent"><xsl:with-param name="ns.content" select="*"/><xsl:with-param name="prsPAccum" select="$prsPAccum"/><xsl:with-param name="prsP" select="$prsP"/><xsl:with-param name="prsR" select="$prsR"/></xsl:call-template>
                </xsl:when>
                <xsl:otherwise>

                    <xsl:call-template name="WrapCnf">
                        <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/><xsl:with-param name="cnfRow" select="$cnfRow"/><xsl:with-param name="cnfCol" select="$cnfCol"/>
                        <xsl:with-param name="prsPAccum" select="$prsPAccum"/><xsl:with-param name="prsP" select="$prsP"/><xsl:with-param name="prsR" select="$prsR"/>
                    </xsl:call-template>
                </xsl:otherwise>
            </xsl:choose>
            </xsl:element>
        </xsl:if>
    </xsl:template>


    <xsl:template name="RecursiveApplyTrPr.class">
        <xsl:if test="w:basedOn">
            <xsl:variable name="baseStyleName" select="w:basedOn[1]/@w:val" />
            <xsl:variable name="sTblStyleBase" select="($nsStyles[@w:styleId=$baseStyleName])[1]"/>
            <xsl:for-each select="$sTblStyleBase"><xsl:call-template name="RecursiveApplyTrPr.class" /></xsl:for-each>
        </xsl:if>


        <xsl:call-template name="ApplyTrPr.class"/>
    </xsl:template>


    <xsl:template name="ApplyTrPr.class">
        <xsl:for-each select="w:trPr">

            <xsl:text>height:</xsl:text>
            <xsl:choose><xsl:when test="w:trHeight/@w:val"><xsl:value-of select="w:trHeight[1]/@w:val div 20"/>pt</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>
            <xsl:text>;</xsl:text>

            <xsl:for-each select="w:cantSplit[1]">
                <xsl:choose>
                    <xsl:when test="@w:val = 'off'">page-break-inside:auto;</xsl:when>
                    <xsl:otherwise>page-break-inside:avoid;</xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>


    <xsl:template name="DisplayEmptyCell">
        <xsl:param name="i" select="1"/>
        <xsl:param name="table_celltype"/>

        <xsl:element name="{$table_celltype}">
            <xsl:attribute name="colspan">
                <xsl:value-of select="$i"/>
            </xsl:attribute>
        </xsl:element>
    </xsl:template>


    <xsl:template match="w:tr">
        <xsl:param name="sTblStyleName"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:param name="cellspacing"/>
        <xsl:param name="cellpadding.default"/>
        <xsl:param name="cellpadding.custom"/>
        <xsl:param name="bdr.top"/>
        <xsl:param name="bdr.left"/>
        <xsl:param name="bdr.bottom"/>
        <xsl:param name="bdr.right"/>
        <xsl:param name="bdr.insideH"/>
        <xsl:param name="bdr.insideV"/>
        <xsl:param name="b.bidivisual"/>
        <xsl:param name="table_celltype" select="'td'"/>

        <xsl:value-of select="$debug_newline"/>
        <tr>

        <xsl:if test="$sTblStyleName/@w:styleId != ''">
            <xsl:attribute name="class">
                <xsl:value-of select="$sTblStyleName/@w:styleId"/>
            </xsl:attribute>
        </xsl:if>

        <xsl:variable name="cnfRow" select="string(w:trPr[1]/w:cnfStyle[1]/@w:val)"/>

        <xsl:variable name="styleMod">

            <xsl:if test="not($cnfRow='')">
                <xsl:for-each select="$sTblStyleName">
                    <xsl:call-template name="GetCnfPr.row"><xsl:with-param name="type" select="$prrCantSplit"/><xsl:with-param name="cnfRow" select="$cnfRow"/></xsl:call-template>
                </xsl:for-each>
            </xsl:if>

            <xsl:call-template name="ApplyTrPr.class"/>
        </xsl:variable>
        <xsl:if test="not($styleMod='')">
            <xsl:attribute name="style"><xsl:value-of select="$styleMod"/></xsl:attribute>
        </xsl:if>

        <xsl:variable name="me" select="." />
        <xsl:variable name="tblCount" select="count(ancestor::w:tbl)" />
        <xsl:variable name="meInContext" select="ancestor::w:tbl[1]/*[count($me|descendant-or-self::*)=count(descendant-or-self::*)]" />
        <xsl:variable name="before" select="count($meInContext/preceding-sibling::*[descendant-or-self::*[name()='w:tr' and (count(ancestor::w:tbl)=$tblCount)]])" />
        <xsl:variable name="after" select="count($meInContext/following-sibling::*[descendant-or-self::*[name()='w:tr' and (count(ancestor::w:tbl)=$tblCount)]])" />
        <xsl:variable name="thisRow" select="$before + 1"/>
        <xsl:variable name="lastRow" select="$before + $after + 1"/>

        <xsl:variable name="bdr.top.updated">
            <xsl:choose>
                <xsl:when test="$cellspacing='' and $thisRow=1"><xsl:value-of select="$bdr.top"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="$bdr.insideH"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:for-each select="w:trPr[1]/w:gridBefore[1]/@w:val">
            <xsl:call-template name="DisplayEmptyCell">
                <xsl:with-param name="i"><xsl:value-of select="."/></xsl:with-param>
                <xsl:with-param name="table_celltype"><xsl:value-of select="$table_celltype"/></xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>

        <xsl:apply-templates select="*[not(name()='w:trPr')]">
            <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
            <xsl:with-param name="prsP" select="$prsP"/>
            <xsl:with-param name="prsR" select="$prsR"/>
            <xsl:with-param name="thisRow" select="$thisRow"/><xsl:with-param name="lastRow" select="$lastRow"/>
            <xsl:with-param name="cellspacing" select="$cellspacing"/><xsl:with-param name="cellpadding.default" select="$cellpadding.default"/><xsl:with-param name="cellpadding.custom" select="$cellpadding.custom"/>
            <xsl:with-param name="bdr.top" select="$bdr.top.updated"/><xsl:with-param name="bdr.left" select="$bdr.left"/><xsl:with-param name="bdr.right" select="$bdr.right"/><xsl:with-param name="bdr.bottom" select="$bdr.bottom"/><xsl:with-param name="bdr.insideV" select="$bdr.insideV"/><xsl:with-param name="bdr.insideH" select="$bdr.insideH"/>
            <xsl:with-param name="cnfRow" select="$cnfRow"/>
            <xsl:with-param name="b.bidivisual" select="$b.bidivisual"/>
            <xsl:with-param name="table_celltype" select="$table_celltype"/>
        </xsl:apply-templates>

        <xsl:for-each select="w:trPr[1]/w:gridAfter[1]/@w:val">
            <xsl:call-template name="DisplayEmptyCell">
                <xsl:with-param name="i"><xsl:value-of select="."/></xsl:with-param>
                <xsl:with-param name="table_celltype"><xsl:value-of select="$table_celltype"/></xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
        </tr>
    </xsl:template>


    <xsl:template name="RecursiveApplyTblPr.class">
        <xsl:if test="w:basedOn">
            <xsl:variable name="baseStyleName" select="w:basedOn[1]/@w:val" />
            <xsl:variable name="sTblStyleBase" select="($nsStyles[@w:styleId=$baseStyleName])[1]"/>
            <xsl:for-each select="$sTblStyleBase"><xsl:call-template name="RecursiveApplyTblPr.class" /></xsl:for-each>
        </xsl:if>


        <xsl:call-template name="ApplyTblPr.class"/>
    </xsl:template>


    <xsl:template name="ApplyTblPr.class">
        <xsl:for-each select="w:tblPr[1]">

            <xsl:if test="w:tblpPr/@w:topFromText">margin-top:<xsl:value-of select="w:tblpPr/@w:topFromText[1] div 20"/>pt;</xsl:if>
            <xsl:if test="w:tblpPr/@w:rightFromText">margin-right:<xsl:value-of select="w:tblpPr/@w:rightFromText[1] div 20"/>pt;</xsl:if>
            <xsl:if test="w:tblpPr/@w:bottomFromText">margin-bottom:<xsl:value-of select="w:tblpPr/@w:bottomFromText[1] div 20"/>pt;</xsl:if>
            <xsl:if test="w:tblpPr/@w:leftFromText">margin-left:<xsl:value-of select="w:tblpPr/@w:leftFromText[1] div 20"/>pt;</xsl:if>

            <xsl:for-each select="w:tblW[1]">width:<xsl:call-template name="EvalTableWidth"/>;</xsl:for-each>
        </xsl:for-each>
    </xsl:template>


    <xsl:template name="tblCore">
        <xsl:value-of select="$debug_newline"/>
        <table>

        <xsl:variable name="tStyleId">
            <xsl:value-of select="w:tblPr[1]/w:tblStyle[1]/@w:val"/>
        </xsl:variable>

        <!-- Check if this table is a question meta-table, to distinguish between tables inside the content -->
        <xsl:variable name="tableType">
            <xsl:if test="w:tr[1]/w:tc[2]/w:p[1]/w:pPr[1]/w:pStyle[1]/@w:val = 'QFType'">
                <xsl:value-of select="' moodleQuestion'"/>
            </xsl:if>
        </xsl:variable>

        <xsl:attribute name="class"><xsl:value-of select="concat($tStyleId, $tableType)"/></xsl:attribute>
        <xsl:variable name="sTblStyleName" select="($nsStyles[@w:styleId=$tStyleId])[1]"/>

        <xsl:variable name="cellspacingTEMP">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrCellspacing"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="cellspacing">
            <xsl:choose>

                <xsl:when test="$cellspacingTEMP='0'"></xsl:when>
                <xsl:otherwise><xsl:value-of select="$cellspacingTEMP"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="cellpadding.default">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrDefaultCellpadding"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="cellpadding.custom">
            <xsl:for-each select="$sTblStyleName/w:tcPr[1]/w:tcMar[1]">
                <xsl:call-template name="ApplyCellMar"/>
            </xsl:for-each>
        </xsl:variable>

        <xsl:variable name="tblInd">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrTblInd"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="bdr.top">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrBdrPr_top"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="bdr.left">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrBdrPr_left"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="bdr.bottom">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrBdrPr_bottom"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="bdr.right">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrBdrPr_right"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="bdr.insideH">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrBdrPr_insideH"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:variable name="bdr.insideV">
            <xsl:call-template name="GetSingleTblPr">
                <xsl:with-param name="type" select="$prrBdrPr_insideV"/><xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="b.bidivisual">
            <xsl:for-each select="w:tblPr[1]/w:bidiVisual[1]">
                <xsl:choose>
                    <xsl:when test="@w:val = 'off'"><xsl:value-of select="$off"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$on"/></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:variable>

        <xsl:variable name="align"><xsl:for-each select="w:tblPr[1]/w:tblpPr[1]/@w:tblpXSpec"><xsl:value-of select="."/></xsl:for-each></xsl:variable>
        <xsl:if test="not($align='')"><xsl:attribute name="align"><xsl:choose><xsl:when test="$align = 'right' or $align = 'outside'">right</xsl:when><xsl:otherwise>left</xsl:otherwise></xsl:choose></xsl:attribute></xsl:if>

        <xsl:attribute name="cellspacing">
            <xsl:choose>
                <xsl:when test="$cellspacing=''">0</xsl:when>
                <xsl:otherwise><xsl:value-of select="($cellspacing div 1440) * $pixelsPerInch"/></xsl:otherwise>
            </xsl:choose>
        </xsl:attribute>
        <xsl:if test="$cellspacing=''"><xsl:attribute name="cellspacing">0</xsl:attribute></xsl:if>

        <xsl:variable name="styleMod">
            <xsl:call-template name="ApplyTblPr.class"/>

            <xsl:choose>
                <xsl:when test="$cellspacing=''">border-collapse:collapse;</xsl:when>
                <xsl:otherwise>
                    <xsl:text>border-collapse:separate;</xsl:text>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.top"/><xsl:with-param name="bdrSide" select="$bdrSide_top"/></xsl:call-template>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.left"/><xsl:with-param name="bdrSide" select="$bdrSide_left"/></xsl:call-template>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.bottom"/><xsl:with-param name="bdrSide" select="$bdrSide_bottom"/></xsl:call-template>
                    <xsl:call-template name="ApplyBorderPr"><xsl:with-param name="pr.bdr" select="$bdr.right"/><xsl:with-param name="bdrSide" select="$bdrSide_right"/></xsl:call-template>
                </xsl:otherwise>
            </xsl:choose>

            <xsl:if test="$b.bidivisual=$on">direction:rtl;</xsl:if>

            <xsl:if test="not(w:tblPr/w:tblpPr)">
                <xsl:text>margin-</xsl:text>
                <xsl:choose>
                    <xsl:when test="$b.bidivisual=$on">right</xsl:when>
                    <xsl:otherwise>left</xsl:otherwise>
                </xsl:choose>
                <xsl:text>:</xsl:text>
                <xsl:value-of select="$tblInd"/>
                <xsl:text>;</xsl:text>
            </xsl:if>
        </xsl:variable>
        <xsl:if test="not($styleMod='')">
            <xsl:attribute name="style"><xsl:value-of select="$styleMod"/></xsl:attribute>
        </xsl:if>

        <xsl:variable name="prsPAccum">

            <xsl:for-each select="w:tblPr[1]/w:bidiVisual[1]">
                <xsl:if test="not(@w:val = 'off')">
                    <xsl:value-of select="concat('direction:', $moodle_textdirection, ';')"/>
                </xsl:if>
            </xsl:for-each>

            <xsl:for-each select="$sTblStyleName"><xsl:call-template name="ApplyPPr.many"/></xsl:for-each>
        </xsl:variable>

        <xsl:variable name="prsR">
            <xsl:call-template name="PrsUpdateRPr">
                <xsl:with-param name="ndPrContainer" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>

        <xsl:variable name="prsP">
            <xsl:call-template name="PrsUpdatePPr">
                <xsl:with-param name="ndPrContainer" select="$sTblStyleName"/>
            </xsl:call-template>
        </xsl:variable>

        <!-- Check for Heading Row Repeat flag, and insert thead/tbody to separate them -->
        <xsl:if test="w:tr[1]/w:trPr/w:tblHeader">
            <thead>
                <xsl:apply-templates select="*[not(name()='w:tblPr' or name()='w:tblGrid') and w:trPr/w:tblHeader]">
                    <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
                    <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                    <xsl:with-param name="prsP" select="$prsP"/>
                    <xsl:with-param name="prsR" select="$prsR"/>
                    <xsl:with-param name="cellspacing" select="$cellspacing"/><xsl:with-param name="cellpadding.default" select="$cellpadding.default"/><xsl:with-param name="cellpadding.custom" select="$cellpadding.custom"/>
                    <xsl:with-param name="bdr.top" select="$bdr.top"/><xsl:with-param name="bdr.left" select="$bdr.left"/><xsl:with-param name="bdr.right" select="$bdr.right"/><xsl:with-param name="bdr.bottom" select="$bdr.bottom"/>
                    <xsl:with-param name="bdr.insideH" select="$bdr.insideH"/><xsl:with-param name="bdr.insideV" select="$bdr.insideV"/>
                    <xsl:with-param name="b.bidivisual" select="$b.bidivisual"/>
                    <xsl:with-param name="table_celltype" select="'th'"/>
                </xsl:apply-templates>
            </thead>
        </xsl:if>
        <tbody>
            <xsl:apply-templates select="*[not(name()='w:tblPr' or name()='w:tblGrid') and not(w:trPr/w:tblHeader)]">
                <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
                <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                <xsl:with-param name="prsP" select="$prsP"/>
                <xsl:with-param name="prsR" select="$prsR"/>
                <xsl:with-param name="cellspacing" select="$cellspacing"/><xsl:with-param name="cellpadding.default" select="$cellpadding.default"/><xsl:with-param name="cellpadding.custom" select="$cellpadding.custom"/>
                <xsl:with-param name="bdr.top" select="$bdr.top"/><xsl:with-param name="bdr.left" select="$bdr.left"/><xsl:with-param name="bdr.right" select="$bdr.right"/><xsl:with-param name="bdr.bottom" select="$bdr.bottom"/>
                <xsl:with-param name="bdr.insideH" select="$bdr.insideH"/><xsl:with-param name="bdr.insideV" select="$bdr.insideV"/>
                <xsl:with-param name="b.bidivisual" select="$b.bidivisual"/>
            </xsl:apply-templates>
        </tbody>
        <!--<xsl:for-each select="w:tblGrid[1]">
            <xsl:text disable-output-escaping="yes">&lt;![if !supportMisalignedColumns]&gt;</xsl:text>
            <tr height="0">
            <xsl:for-each select="w:gridCol">
                <xsl:variable name="gridStyle">margin:0;padding:0;border:none;width:<xsl:call-template name="EvalTableWidth"/>;</xsl:variable>
                <td style="{$gridStyle}"/>
            </xsl:for-each>
            </tr>
            <xsl:text disable-output-escaping="yes">&lt;![endif]&gt;</xsl:text>
        </xsl:for-each>-->
        </table>
    </xsl:template>

    <xsl:template match="w:tbl[w:tblPr/w:jc/@w:val]">
        <xsl:variable name="p.Jc" select="w:tblPr/w:jc/@w:val"/>
        <div>
            <xsl:attribute name="align"><xsl:value-of select="$p.Jc"/></xsl:attribute>

            <xsl:call-template name="tblCore"/>
        </div>
    </xsl:template>

    <xsl:template match="w:tbl">
        <xsl:call-template name="tblCore"/>
    </xsl:template>

    <xsl:template name="hrCore">
        <xsl:param name="p.Hr"/>
            <hr>
                <xsl:attribute name="style"><xsl:value-of select="substring-after($p.Hr/@style, ';')"/></xsl:attribute>
                <xsl:attribute name="align"><xsl:value-of select="$p.Hr/@o:hralign"/></xsl:attribute>
                <xsl:if test="$p.Hr/@o:hrnoshade='t'">
                    <xsl:attribute name="noshade">
                        <xsl:text>1</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="color">
                        <xsl:value-of select="$p.Hr/@fillcolor"/>
                    </xsl:attribute>
                </xsl:if>
                <xsl:if test="$p.Hr/@o:hrpct">
                    <xsl:attribute name="width">
                        <xsl:value-of select="$p.Hr/@o:hrpct div 10"/>
                        <xsl:text>%</xsl:text>
                    </xsl:attribute>
                </xsl:if>
            </hr>
    </xsl:template>

    <xsl:template match="w:body">

        <xsl:attribute name="style">
            <xsl:variable name="divBody" select="//wordmlContainer/w:document/w:divs/w:div[w:bodyDiv/@w:val='on']"/>
            <xsl:variable name="dxaBodyLeft">
                <xsl:value-of select="$divBody/w:marLeft/@w:val"/>
            </xsl:variable>
            <xsl:variable name="dxaBodyRight">
                <xsl:value-of select="$divBody/w:marRight/@w:val"/>
            </xsl:variable>
            <xsl:if test="not($dxaBodyLeft='' or $dxaBodyLeft=0)">
                    <xsl:text>margin-left:</xsl:text><xsl:value-of select="$dxaBodyLeft div 20"/><xsl:text>pt;</xsl:text>
            </xsl:if>
            <xsl:if test="not($dxaBodyRight='' or $dxaBodyRight=0)">
                <xsl:text>margin-right:</xsl:text><xsl:value-of select="$dxaBodyRight div 20"/><xsl:text>pt;</xsl:text>
            </xsl:if>
        </xsl:attribute>
        <xsl:apply-templates select="*"/>
    </xsl:template>


    <xsl:template match="w:font">
        <xsl:text>@font-face{font-family:"</xsl:text>
        <xsl:value-of select="@w:name"/>
        <xsl:text>";panose-1:</xsl:text>
        <xsl:variable name="panose1">
            <xsl:call-template name="ConvertHexToDec">
                <xsl:with-param name="value" select="w:panose-1[1]/@w:val"/>
                <xsl:with-param name="i" select="2"/>
                <xsl:with-param name="s" select="2"/>
            </xsl:call-template>
        </xsl:variable>
        <xsl:value-of select="substring($panose1,2)"/>
        <xsl:text>;}</xsl:text>
    </xsl:template>


    <xsl:template name="MakeRStyle">
        <xsl:param name="basetype"/>
        <xsl:text>.</xsl:text><xsl:value-of select="@w:styleId"/><xsl:value-of select="$charStyleSuffix"/>
        <xsl:text>{</xsl:text>
            <xsl:call-template name="MakeRStyleCore"><xsl:with-param name="basetype" select="$basetype"/></xsl:call-template>
        <xsl:text>}
            </xsl:text>
    </xsl:template>

    <xsl:template name="MakeRStyleCore">
        <xsl:param name="basetype"/>

            <xsl:choose>
                    <xsl:when test="w:basedOn/@w:val">
                            <xsl:variable name="sBasedOn">
                                    <xsl:value-of select="w:basedOn/@w:val"/>
                            </xsl:variable>
                            <xsl:for-each select="$nsStyles[@w:styleId=$sBasedOn]">
                                    <xsl:call-template name="MakeRStyleCore"><xsl:with-param name="basetype" select="$basetype"/></xsl:call-template>
                            </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                            <xsl:if test="$basetype='paragraph'">

                                    <xsl:text>font-size: 10pt;</xsl:text>
                            </xsl:if>
                    </xsl:otherwise>
            </xsl:choose>


            <xsl:call-template name="ApplyRPr.class"/>
    </xsl:template>

    <xsl:template name="MakePStyle">

            <xsl:text>.</xsl:text>
            <xsl:value-of select="@w:styleId"/>
            <xsl:text>{
            </xsl:text>
            <xsl:call-template name="MakePStyleCore"/>
            <xsl:text>}
            </xsl:text>

            <xsl:call-template name="MakeRStyle"><xsl:with-param name="basetype" select="'paragraph'"/></xsl:call-template>
    </xsl:template>

    <xsl:template name="MakePStyleCore">
        <xsl:param name="beforeAutospace" select="$off" />
        <xsl:param name="afterAutospace" select="$off" />


        <xsl:variable name="spacing" select="w:pPr[1]/w:spacing[1]"/>
        <xsl:variable name="beforeAutospaceHere">
            <xsl:choose>
                <xsl:when test="$spacing/@w:before-autospacing = 'on'"><xsl:value-of select="$on" /></xsl:when>
                <xsl:otherwise><xsl:value-of select="$beforeAutospace" /></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <xsl:variable name="afterAutospaceHere">
            <xsl:choose>
                <xsl:when test="$spacing/@w:after-autospacing = 'on'"><xsl:value-of select="$on" /></xsl:when>
                <xsl:otherwise><xsl:value-of select="$afterAutospace" /></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

            <xsl:choose>
                    <xsl:when test="w:basedOn/@w:val">
                            <xsl:variable name="sBasedOn">
                                    <xsl:value-of select="w:basedOn/@w:val"/>
                            </xsl:variable>
                            <xsl:for-each select="$nsStyles[@w:styleId=$sBasedOn]">
                                    <xsl:call-template name="MakePStyleCore">
                        <xsl:with-param name="beforeAutospace" select="$beforeAutospaceHere" />
                        <xsl:with-param name="afterAutospace" select="$afterAutospaceHere" />
                    </xsl:call-template>
                            </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                            <xsl:text>margin-left:</xsl:text>
                            <xsl:value-of select="$paraMarginDefaultLeft"/>
                            <xsl:text>;margin-right:</xsl:text>
                            <xsl:value-of select="$paraMarginDefaultRight"/>

                            <xsl:if test="not($beforeAutospace = $on)" >
                    <xsl:if test="(not($spacing/@w:before-autospacing) or $spacing/@w:before-autospacing = 'off')">
                        <xsl:text>;margin-top:</xsl:text><xsl:value-of select="$paraMarginDefaultTop"/>
                    </xsl:if>
                </xsl:if>

                            <xsl:if test="not($afterAutospace = $on)" >
                    <xsl:if test="(not($spacing/@w:after-autospacing) or $spacing/@w:after-autospacing = 'off')">
                        <xsl:text>;margin-bottom:</xsl:text><xsl:value-of select="$paraMarginDefaultBottom"/>
                    </xsl:if>
                </xsl:if>

                            <xsl:text>;font-size:10.0pt;font-family:"Times New Roman";</xsl:text>
                    </xsl:otherwise>
            </xsl:choose>

            <xsl:call-template name="ApplyPPr.class"/>
    </xsl:template>

    <xsl:template name="MakeTblStyle">
            <xsl:variable name="styleId" select="@w:styleId"/>

            <xsl:text>.</xsl:text><xsl:value-of select="$styleId"/>
            <xsl:text>{</xsl:text><xsl:call-template name="RecursiveApplyTblPr.class"/><xsl:text>} </xsl:text>

            <xsl:text>.</xsl:text><xsl:value-of select="$styleId"/>
            <xsl:text>{</xsl:text><xsl:call-template name="RecursiveApplyTrPr.class"/><xsl:text>} </xsl:text>

            <xsl:text>.</xsl:text><xsl:value-of select="$styleId"/>
            <xsl:text>{vertical-align:top;</xsl:text>

            <xsl:call-template name="RecursiveApplyTcPr.class"/>

            <xsl:call-template name="RecursiveApplyPPr.class"/>

            <xsl:call-template name="RecursiveApplyRPr.class"/>
            <xsl:text>} </xsl:text>

            <xsl:for-each select="w:tblStylePr">
                    <xsl:text>.</xsl:text><xsl:value-of select="$styleId"/>-<xsl:value-of select="@w:type"/>
                    <xsl:text>{vertical-align:top;</xsl:text>
                    <xsl:call-template name="ApplyPPr.class"/>
                    <xsl:call-template name="ApplyRPr.class"/>
                    <xsl:text>} </xsl:text>
            </xsl:for-each>
    </xsl:template>


    <xsl:template match="w:style">
        <xsl:choose>

            <xsl:when test="@w:type = 'character'">
                <xsl:call-template name="MakeRStyle"/>
            </xsl:when>

            <xsl:when test="@w:type = 'paragraph'">
                            <xsl:call-template name="MakePStyle"/>
                    </xsl:when>

            <xsl:when test="@w:type = 'table'">
                            <xsl:call-template name="MakeTblStyle"/>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="w:bookmarkStart">
        <a class="bookmarkStart" name="{@w:name}" id="{@w:id}"/>
    </xsl:template>

    <xsl:template match="w:bookmarkEnd">
        <a class="bookmarkEnd" id="{@w:id}"/>
    </xsl:template>

    <xsl:template name="copyElements">
        <xsl:param name="sTblStyleName"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:param name="cellspacing"/>
        <xsl:param name="cellpadding.default"/>
        <xsl:param name="cellpadding.custom"/>
        <xsl:param name="bdr.top"/>
        <xsl:param name="bdr.left"/>
        <xsl:param name="bdr.bottom"/>
        <xsl:param name="bdr.right"/>
        <xsl:param name="bdr.insideV"/>
        <xsl:param name="bdr.insideH"/>
        <xsl:param name="thisRow"/>
        <xsl:param name="lastRow"/>
        <xsl:param name="cnfRow"/>
        <xsl:param name="b.bidivisual"/>
        <xsl:element name="{name()}" namespace="{namespace-uri()}">
            <xsl:for-each select="@*">
                <xsl:attribute name="{name()}" namespace="{namespace-uri()}">
                    <xsl:value-of select="."/>
                </xsl:attribute>
            </xsl:for-each>
            <xsl:apply-templates>
                        <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
                        <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
                        <xsl:with-param name="prsP" select="$prsP"/>
                        <xsl:with-param name="prsR" select="$prsR"/>
                        <xsl:with-param name="cellspacing" select="$cellspacing"/>
                        <xsl:with-param name="cellpadding.default" select="$cellpadding.default"/>
                        <xsl:with-param name="cellpadding.custom" select="$cellpadding.custom"/>
                        <xsl:with-param name="bdr.top" select="$bdr.top"/>
                        <xsl:with-param name="bdr.left" select="$bdr.left"/>
                        <xsl:with-param name="bdr.bottom" select="$bdr.bottom"/>
                        <xsl:with-param name="bdr.right" select="$bdr.right"/>
                        <xsl:with-param name="bdr.insideV" select="$bdr.insideV"/>
                        <xsl:with-param name="bdr.insideH" select="$bdr.insideH"/>
                        <xsl:with-param name="thisRow" select="$thisRow"/>
                        <xsl:with-param name="lastRow" select="$lastRow"/>
                        <xsl:with-param name="cnfRow" select="$cnfRow"/>
                        <xsl:with-param name="b.bidivisual" select="$b.bidivisual"/>
                    </xsl:apply-templates>
        </xsl:element>
    </xsl:template>

    <xsl:template match="*">
        <xsl:param name="sTblStyleName"/>
        <xsl:param name="prsPAccum"/>
        <xsl:param name="prsP"/>
        <xsl:param name="prsR"/>
        <xsl:param name="cellspacing"/>
        <xsl:param name="cellpadding.default"/>
        <xsl:param name="cellpadding.custom"/>
        <xsl:param name="bdr.top"/>
        <xsl:param name="bdr.left"/>
        <xsl:param name="bdr.bottom"/>
        <xsl:param name="bdr.right"/>
        <xsl:param name="bdr.insideV"/>
        <xsl:param name="bdr.insideH"/>
        <xsl:param name="thisRow"/>
        <xsl:param name="lastRow"/>
        <xsl:param name="cnfRow"/>
        <xsl:param name="b.bidivisual"/>
        <xsl:call-template name="copyElements">
            <xsl:with-param name="sTblStyleName" select="$sTblStyleName"/>
            <xsl:with-param name="prsPAccum" select="$prsPAccum"/>
            <xsl:with-param name="prsP" select="$prsP"/>
            <xsl:with-param name="prsR" select="$prsR"/>
            <xsl:with-param name="cellspacing" select="$cellspacing"/>
            <xsl:with-param name="cellpadding.default" select="$cellpadding.default"/>
            <xsl:with-param name="cellpadding.custom" select="$cellpadding.custom"/>
            <xsl:with-param name="bdr.top" select="$bdr.top"/>
            <xsl:with-param name="bdr.left" select="$bdr.left"/>
            <xsl:with-param name="bdr.bottom" select="$bdr.bottom"/>
            <xsl:with-param name="bdr.right" select="$bdr.right"/>
            <xsl:with-param name="bdr.insideV" select="$bdr.insideV"/>
            <xsl:with-param name="bdr.insideH" select="$bdr.insideH"/>
            <xsl:with-param name="thisRow" select="$thisRow"/>
            <xsl:with-param name="lastRow" select="$lastRow"/>
            <xsl:with-param name="cnfRow" select="$cnfRow"/>
            <xsl:with-param name="b.bidivisual" select="$b.bidivisual"/>
            </xsl:call-template>
    </xsl:template>

    <xsl:template match="v:*"/>

    <xsl:template match="w:ruby">
        <ruby>


            <xsl:attribute name="lang">
                <xsl:value-of select="w:rubyPr/w:lid/@w:val" />
            </xsl:attribute>

            <xsl:attribute name="style">


                <xsl:variable name="align" select="w:rubyPr/w:rubyAlign/@w:val" />
                <xsl:text>ruby-align:</xsl:text>
                <xsl:choose>
                    <xsl:when test="$align='rightVertical'"><xsl:text>auto</xsl:text></xsl:when>
                    <xsl:when test="$align='distributeLetter'"><xsl:text>distribute-letter</xsl:text></xsl:when>
                    <xsl:when test="$align='distributeSpace'"><xsl:text>distribute-space</xsl:text></xsl:when>
                    <xsl:otherwise><xsl:value-of select="$align" /></xsl:otherwise>
                </xsl:choose>


            </xsl:attribute>

            <span>
                <xsl:if test="w:rubyPr/w:hpsBaseText">
                    <xsl:attribute name="style">
                        <xsl:text>font-size:</xsl:text>
                        <xsl:value-of select="w:rubyPr/w:hpsBaseText/@w:val" /><xsl:text>pt;</xsl:text>
                    </xsl:attribute>
                </xsl:if>

                <xsl:apply-templates select="w:rubyBase/w:r"/>
            </span>


            <rt>
                <span>
                    <xsl:if test="w:rubyPr/w:hps">
                        <xsl:attribute name="style">
                            <xsl:text>font-size:</xsl:text>
                            <xsl:value-of select="w:rubyPr/w:hps/@w:val div 2" /><xsl:text>pt;</xsl:text>
                        </xsl:attribute>


                        <xsl:apply-templates select="w:rt/w:r/w:t"/>
                    </xsl:if>
                </span>
            </rt>
        </ruby>
    </xsl:template>


    <!-- Footnote handling replaced -->
    <!--<xsl:template match="w:footnote">

        <xsl:variable name="me" select="." />
        <xsl:variable name="meInContext" select="ancestor::w:r[1]/*[count($me|descendant-or-self::*)=count(descendant-or-self::*)]" />
        <xsl:variable name="start">
            <xsl:choose>
                <xsl:when test="$ndDocPr/w:footnotePr/w:numStart">
                    <xsl:value-of select="$ndDocPr/w:footnotePr/w:numStart/@w:val" />
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="1" /></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="position" select="count($meInContext/preceding::*[name()='w:footnote' and ancestor::w:body]) + $start" />

        <sup>
            <a>
                <xsl:attribute name="name"><xsl:value-of select="$footnoteRefLink" /><xsl:value-of select="$position" /></xsl:attribute>
                <xsl:attribute name="href"><xsl:text>#</xsl:text><xsl:value-of select="$footnoteLink" /><xsl:value-of select="$position" /></xsl:attribute>
                <xsl:text>[</xsl:text><xsl:value-of select="$position" /><xsl:text>]</xsl:text>
            </a>
        </sup>
    </xsl:template>-->



    <xsl:template match="w:endnote">

        <xsl:variable name="me" select="." />
        <xsl:variable name="meInContext" select="ancestor::w:r[1]/*[count($me|descendant-or-self::*)=count(descendant-or-self::*)]" />
        <xsl:variable name="start">
            <xsl:choose>
                <xsl:when test="$ndDocPr/w:endnotePr/w:numStart">
                    <xsl:value-of select="$ndDocPr/w:endnotePr/w:numStart/@w:val" />
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="1" /></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="position" select="count($meInContext/preceding::*[name()='w:endnote' and ancestor::w:body]) + $start" />

        <sup>
            <a>
                <xsl:attribute name="name"><xsl:value-of select="$endnoteRefLink" /><xsl:value-of select="$position" /></xsl:attribute>
                <xsl:attribute name="href"><xsl:text>#</xsl:text><xsl:value-of select="$endnoteLink" /><xsl:value-of select="$position" /></xsl:attribute>
                <xsl:text>[</xsl:text><xsl:value-of select="$position" /><xsl:text>]</xsl:text>
            </a>
        </sup>
    </xsl:template>


    <xsl:template name="IsListBullet">



        <xsl:variable name="ilfo" select="w:ilfo/@w:val"/>
        <xsl:variable name="ilvl" select="w:ilvl/@w:val"/>
        <xsl:variable name="list" select="$ndLists/w:list[@w:ilfo=$ilfo][1]"/>

        <xsl:variable name="nfc">


            <xsl:choose>
                <xsl:when test="$ndLists/w:listDef[@w:listDefId=$list/w:ilst/@w:val][1]/w:lvl[@w:ilvl=$ilvl][1]">
                    <xsl:for-each select="$ndLists/w:listDef[@w:listDefId=$list/w:ilst/@w:val][1]/w:lvl[@w:ilvl=$ilvl][1]">
                        <xsl:choose>
                            <xsl:when test="$list/w:lvlOverride[@w:ilvl=$ilvl]/w:nfc">
                                <xsl:value-of select="$list/w:lvlOverride[@w:ilvl=$ilvl]/w:nfc/@w:val" />
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="$ndLists/w:listDef[@w:listDefId=$list/w:ilst/@w:val][1]/w:lvl[@w:ilvl=$ilvl][1]/w:nfc/@w:val" />
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:when>

                <xsl:when test="$list/w:lvlOverride[@w:ilvl=$ilvl]">
                    <xsl:for-each select="$list/w:lvlOverride[@w:ilvl=$ilvl]">
                        <xsl:value-of select="w:nfc/@w:val" />
                    </xsl:for-each>
                </xsl:when>

                <xsl:when test="$ndLists/w:listDef[@w:listDefId=$list/w:ilst/@w:val][1]/w:listStyleLink">
                    <xsl:variable name="linkedStyleId" select="$ndLists/w:listDef[@w:listDefId=$list/w:ilst/@w:val][1]/w:listStyleLink/@w:val" />
                    <xsl:variable name="linkedStyle" select="$nsStyles[@w:styleId=$linkedStyleId]" />
                    <xsl:variable name="linkedList" select="w:list[@w:ilfo=$linkedStyle/w:pPr/w:listPr/w:ilfo/@w:val]" />
                    <xsl:for-each select="$ndLists/w:listDef[@w:listDefId=$linkedList/w:ilst/@w:val][1]/w:lvl[@w:ilvl=$ilvl][1]">
                        <xsl:value-of select="w:nfc/@w:val" />
                    </xsl:for-each>
                </xsl:when>
            </xsl:choose>
        </xsl:variable>

        <xsl:if test="$nfc=$nfcBullet"><xsl:value-of select="$on" /></xsl:if>
    </xsl:template>


    <xsl:template match="w:fldSimple">
        <xsl:apply-templates/>
    </xsl:template>


    <xsl:template match="w:*"/>

    <xsl:template match="wx:*"/>

    <xsl:template match="o:WordFieldCodes"/>

    <xsl:template match="w:cfChunk">
        <xsl:apply-templates />
    </xsl:template>

    <xsl:template match="//wordmlContainer/w:document">
    
        <html>
            <head>
                <!-- Dublin Core properties from file docProps/core.xml-->
                <xsl:for-each select="$dublinCore/*">
                    <xsl:if test="string-length(.) &gt; 0">
                        <xsl:value-of select="$debug_newline"/>
                        <meta name="{concat('dc:', local-name())}" content="{normalize-space(.)}"/>
                    </xsl:if>
                </xsl:for-each>
                <!-- Custom document properties from file docProps/custom.xml-->
                <xsl:for-each select="$customProps/*">
                    <xsl:if test="local-name() = 'property'">
                        <xsl:value-of select="$debug_newline"/>
                        <meta name="{@name}" content="{normalize-space(.)}"/>
                    </xsl:if>
                </xsl:for-each>

                <!-- Image data in Base64, generated from files in word/media folder of .docx file -->
                <xsl:if test="$debug_flag &gt; 1">
                    <xsl:value-of select="$debug_newline"/>
                    <imagesContainer>
                        <xsl:for-each select="$imagesContainer/*">
                            <xsl:value-of select="$debug_newline"/>
                            <file filename="{@filename}" mime-type="{@mime-type}">
                                <xsl:value-of select="substring(normalize-space(.), 1, 100)"/>
                            </file>
                        </xsl:for-each>
                        <xsl:value-of select="$debug_newline"/>
                    </imagesContainer>
                    <xsl:value-of select="$debug_newline"/>
                </xsl:if>
                <!-- Image relationships from file word/_rels/document.xml.rels -->
                <xsl:if test="$debug_flag &gt; 1">
                    <xsl:value-of select="$debug_newline"/>
                    <imageLinks>
                        <xsl:for-each select="$imageLinks">
                            <xsl:value-of select="$debug_newline"/>
                            <Relationship Id="{@Id}" Target="{@Target}" TargetMode="{@TargetMode}"/>
                        </xsl:for-each>
                        <xsl:value-of select="$debug_newline"/>
                    </imageLinks>
                </xsl:if>
                <!-- Style mapping language-specific names to language-independent ids from file word/styles.xml -->
                <xsl:if test="$debug_flag &gt; 1">
                    <xsl:value-of select="$debug_newline"/>
                    <styleMap>
                        <xsl:comment><xsl:value-of select="concat('style count: ', count($nsStyles[name() = 'w:style']))"/></xsl:comment>
                        <xsl:for-each select="$nsStyles">
                            <xsl:value-of select="$debug_newline"/>
                            <style styleId="{@w:styleId}" styleName="{w:name/@w:val}" customStyle="{@w:customStyle}"/>
                        </xsl:for-each>
                        <xsl:value-of select="$debug_newline"/>
                    </styleMap>
                </xsl:if>
                <!-- Hyperlink mapping from file word/_rels/document.xml.rels -->
                <xsl:if test="$debug_flag &gt; 1">
                    <xsl:value-of select="$debug_newline"/>
                    <hyperLinks>
                        <xsl:comment><xsl:value-of select="concat('link count: ', count($hyperLinks))"/></xsl:comment>
                        <xsl:for-each select="$hyperLinks">
                            <xsl:value-of select="$debug_newline"/>
                            <xsl:element name="Relationship">
                                <xsl:attribute name="Id">
                                    <xsl:value-of select="@Id"/>
                                </xsl:attribute>
                                <xsl:attribute name="Target">
                                    <xsl:value-of select="@Target"/>
                                </xsl:attribute>
                                <xsl:attribute name="TargetMode">
                                    <xsl:value-of select="@TargetMode"/>
                                </xsl:attribute>
                            </xsl:element>
                        </xsl:for-each>
                        <xsl:value-of select="$debug_newline"/>
                    </hyperLinks>
                    <xsl:value-of select="$debug_newline"/>
                </xsl:if>
            </head>
            <body>
                <div class="level1">
                    <xsl:apply-templates select="w:body | w:cfChunk"/>

                    <xsl:if test="//w:body//w:endnote">
                        <xsl:variable name="start">
                            <xsl:choose>
                                <xsl:when test="$ndDocPr/w:endnotePr/w:numStart">
                                    <xsl:value-of select="$ndDocPr/w:endnotePr/w:numStart/@w:val" />
                                </xsl:when>
                                <xsl:otherwise><xsl:value-of select="0" /></xsl:otherwise>
                            </xsl:choose>
                        </xsl:variable>
                        <hr align="left" size="1" width="33%" />
                        <xsl:for-each select="//w:body//w:endnote">
                            <a target="self">
                                <xsl:attribute name="href">
                                    <xsl:text>#</xsl:text>
                                    <xsl:value-of select="$endnoteRefLink" />
                                    <xsl:value-of select="position() + $start" />
                                </xsl:attribute>
                                <xsl:attribute name="name">
                                    <xsl:value-of select="$endnoteLink" />
                                    <xsl:value-of select="position() + $start" />
                                </xsl:attribute>
                                <xsl:text>[</xsl:text>
                                <xsl:value-of select="position() + $start" />
                                <xsl:text>]</xsl:text>
                            </a>
                            <xsl:apply-templates select="*" />
                        </xsl:for-each>
                    </xsl:if>
                </div>
            </body>
            <!-- Keep original images data if importing directly into database -->
            <xsl:if test="$pluginname = 'atto_wordimport'">
                <xsl:apply-templates select="//imagesContainer"/>
            </xsl:if>
        </html>
    </xsl:template>
    

    <!-- Handle images -->
    <xsl:template match="w:p/w:r/w:drawing">
        <!-- Embedded images -->
        <xsl:variable name="img_rid" select=".//a:blip/@r:embed"/>
        <xsl:variable name="img_filename" select="$imageLinks[@Id = $img_rid]/@Target"/>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('img_rid = ', $img_rid, '; img_filename = ', $img_filename)"/>
            <xsl:with-param name="inline" select="'true'"/>
            <xsl:with-param name="condition" select="$debug_flag = '2' and $img_rid != ''"/>
        </xsl:call-template>

        <!-- External linked images -->
        <xsl:variable name="img_external_rid" select=".//a:blip/@r:link"/>
        <xsl:variable name="img_external_filename" select="$imageLinks[@Id = $img_external_rid]/@Target"/>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('img_external_rid = ', $img_external_rid, '; img_external_filename = ', $img_external_filename)"/>
            <xsl:with-param name="inline" select="'true'"/>
            <xsl:with-param name="condition" select="$debug_flag = '2' and $img_external_rid != ''"/>
        </xsl:call-template>

        <!-- Hyperlinked images -->
        <xsl:variable name="img_hyperlink_rid" select=".//a:hlinkClick/@r:id"/>
        <xsl:variable name="img_hyperlink" select="$hyperLinks[@Id = $img_hyperlink_rid]/@Target"/>
        <xsl:call-template name="debugComment">
            <xsl:with-param name="comment_text" select="concat('img_hyperlink_rid = ', $img_hyperlink_rid, '; img_hyperlink = ', $img_hyperlink)"/>
            <xsl:with-param name="inline" select="'true'"/>
            <xsl:with-param name="condition" select="$debug_flag = '2' and $img_hyperlink_rid != ''"/>
        </xsl:call-template>

        <!-- Map title field to alt attribute -->
        <xsl:variable name="img_alt" select="wp:inline/wp:docPr/@title"/>
        <!-- The wp:extent/@cx and @cy attributes define the size of the image. They are denominated in 
             EMUs (English Metric Units); 1 inch = 914400, therefore 1 pixel = 914400 / 96 (dpi) = 9525 
             cf. http://polymathprogrammer.com/2009/10/22/english-metric-units-and-open-xml/ -->
        <!-- Map wp:extent/@cx and @cy fields to width/height, and round to integers -->
        <xsl:variable name="img_width" select="substring-before(wp:inline/wp:extent/@cx div 9525, '.')"/>
        <xsl:variable name="img_height" select="substring-before(wp:inline/wp:extent/@cy div 9525, '.')"/>

        <!-- Map description field to longdesc attribute -->
        <xsl:variable name="img_longdesc" select="wp:inline/wp:docPr/@descr"/>
        <!-- Map name field to id attribute: it contains a sequence number for the image, e.g. "Picture 1" -->
        <xsl:variable name="img_id" select="translate(wp:inline/wp:docPr/@name, ' ', '')"/>
        <!-- Store the internal file name in the class attribute, for want of a better place
        <xsl:variable name="img_class" select="$img_filename"/>
        -->
        <!-- Store the image data or URL in the src attribute -->
        <xsl:variable name="img_src">
            <xsl:choose>
            <xsl:when test="$img_rid != '' and $pluginname = 'atto_wordimport'">
                <!-- Dereference the reference ID field to get the file name, and map to the src attribute -->
                <xsl:value-of select="$imagesContainer/file[@filename = $img_filename]"/>
            </xsl:when>
            <xsl:when test="$img_rid != '' and $pluginname = 'booktool_wordimport'">
                <!-- Dereference the reference ID field to get the file name, and map to the src attribute -->
                <xsl:value-of select="substring-after($img_filename, '/')"/>
            </xsl:when>
            <xsl:when test="$img_rid != ''">
                <!-- Dereference the reference ID field to get the file name, and map to the src attribute -->
                <xsl:value-of select="concat('data:', $imagesContainer/file[@filename = $img_filename]/@mime-type, ';base64,', $imagesContainer/file[@filename = $img_filename])"/>
            </xsl:when>
            <xsl:when test="$img_external_rid != ''">
                <!-- External image, so just keep the URL -->
                <xsl:value-of select="$img_external_filename"/>
            </xsl:when>
            </xsl:choose>
        </xsl:variable>

        <!-- Handle case where image might be hyperlinked -->
        <xsl:choose>
        <xsl:when test="$img_hyperlink != ''">
            <!-- The image is linked -->
            <a href="{$img_hyperlink}">
                <img src="{$img_src}" id="{$img_id}" alt="{$img_alt}" longdesc="{$img_longdesc}">
                    <xsl:if test="$img_width != ''">
                        <xsl:attribute name="width">
                            <xsl:value-of select="$img_width"/>
                        </xsl:attribute>
                        <xsl:attribute name="height">
                            <xsl:value-of select="$img_height"/>
                        </xsl:attribute>
                    </xsl:if>
                </img>
            </a>
        </xsl:when>
        <xsl:otherwise>
            <!-- The image is not linked -->
            <img src="{$img_src}" id="{$img_id}" alt="{$img_alt}" longdesc="{$img_longdesc}">
                <xsl:if test="$img_width != ''">
                    <xsl:attribute name="width">
                        <xsl:value-of select="$img_width"/>
                    </xsl:attribute>
                    <xsl:attribute name="height">
                        <xsl:value-of select="$img_height"/>
                    </xsl:attribute>
                </xsl:if>
            </img>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="w:p/w:r/w:pict">
        <xsl:choose>
            <xsl:when test="v:shape/@alt">
                <img src="{v:shape/@alt}"/>
            </xsl:when>
            <xsl:otherwise>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Handle equations by converting them to MathML -->
    <xsl:template match="m:oMathPara">
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="m:oMathParaPr"/>

    <xsl:template match="m:oMath">
        <math xmlns="http://www.w3.org/1998/Math/MathML">
            <xsl:apply-templates />
        </math>
    </xsl:template>

    <!-- Handle w:dir, which Word sometimes wraps around w:r elements in RTL texts like Arabic -->
    <xsl:template match="w:dir">
        <xsl:apply-templates/>
    </xsl:template>

    <!-- Delete the bookmark marking the last cursor position-->
    <xsl:template match="w:bookmarkStart[@w:name = '_GoBack']"/>
    <xsl:template match="w:bookmarkEnd[@w:id = '0']"/>

    <!-- Footnote references: ignore them for the moment -->
    <xsl:template match="w:r[w:rPr/w:rStyle/@w:val = 'FootnoteReference' and w:footnoteReference]"/>
    <!--
    <xsl:template match="w:footnoteReference">
        <sup>
            <a class="fnref" href="{concat('#', $footnoteRefLink, @w:id)}" name="{concat($footnoteLink, @w:id)}"/>
        </sup>
    </xsl:template>
    -->

    <xsl:template match="/">
        <xsl:apply-templates select="//wordmlContainer/w:document"/>
    </xsl:template>

    <!-- Roll up adjacent w:instrText elements to avoid splitting of Word field code 
    <xsl:template match="/">
        <xsl:variable name="instrText">
            <xsl:apply-templates mode="instrText"/>
        </xsl:variable>
        <xsl:apply-templates select="$instrText" mode="continue"/>
    </xsl:template>

    <xsl:template match="/" mode="continue">
        <xsl:apply-templates select="*"/>
    </xsl:template>
-->
    <xsl:template match="w:p" mode="instrText">
        <xsl:copy>
            <xsl:apply-templates select="@*" mode="instrText"/>
            <xsl:apply-templates select="*" mode="instrText"/>
        </xsl:copy>
    </xsl:template>


    <xsl:template match="imagesContainer|customProps|styleMap|imageLinks">
    <!--
        <xsl:comment>
            <xsl:value-of select="concat(name(), ' Container deleted')"/>
            <xsl:value-of select="concat(count(*), ' images')"/>
        </xsl:comment>
        -->
    </xsl:template>
    <xsl:template match="dublinCore|documentLinks|styleMap">
    <!--
        <xsl:comment><xsl:value-of select="concat(name(), ' Container deleted')"/></xsl:comment>
        -->
    </xsl:template>
    <xsl:template match="pass1Container|wordmlContainer">
    <!--
        <xsl:comment><xsl:value-of select="concat(name(), ' Container deleted')"/></xsl:comment>
        -->
        <xsl:apply-templates/>
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
