<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- Top level: disclaimer/intro -->
<xsl:template match="/">
  <p>This documentation is generated automatically from the XMLDB database
    definition. It is available only in English.</p>
  <p><em>Note: This documentation currently does not show full details of field types.</em></p>
  <xsl:apply-templates/>
</xsl:template>

<!-- Tables: heading, comment -->
<xsl:template match="TABLE">
  <h3 style="margin-top:3em"><xsl:value-of select="@NAME"/></h3>
  <xsl:call-template name="display-comment"><xsl:with-param name="PARA">y</xsl:with-param></xsl:call-template>
  <xsl:apply-templates/>
</xsl:template>

<!-- Fields (if any): table with field, type, comment -->
<xsl:template match="FIELDS[FIELD]">
  <table class="generaltable boxaligncenter" style="margin:1em 0" cellspacing="1" cellpadding="5" width="100%"> 
    <tr>
      <th class="header c0" scope="col">Field</th>
      <th class="header c1" scope="col">Type</th>
      <th class="header c2 lastcol" scope="col">Description</th>
    </tr>
    <xsl:apply-templates/>
  </table>
</xsl:template>

<!-- Each individual field -->
<xsl:template match="FIELD">
  <xsl:variable name="COUNT" select="count(preceding-sibling::*)"/>
  <tr class="r{$COUNT}">
    <td class="cell c0"><xsl:value-of select="@NAME"/></td>
    <td class="cell c1"><xsl:value-of select="@TYPE"/> (<xsl:value-of select="@LENGTH"/>)</td>
    <td class="cell c2 lastcol"><xsl:call-template name="display-comment"/></td>
  </tr>
</xsl:template>

<!-- Keys (if any): table with key, type, field(s), reference, and comment --> 
<xsl:template match="KEYS[KEY]">
  <h4>Keys</h4>
  <table class="generaltable boxaligncenter" cellspacing="1" cellpadding="5" width="100%"> 
    <tr>      
      <th class="header c0" scope="col">Name</th>
      <th class="header c1" scope="col">Type</th>
      <th class="header c2" scope="col">Field(s)</th>
      <th class="header c3" scope="col">Reference</th>
      <!-- If no keys have comments (which is usually sensible since it's 
         completely obvious what they are) then the comment column is not 
         included -->      
      <xsl:if test="*[normalize-space(@COMMENT)!='']">
        <th class="header c4 lastcol" scope="col">Description</th>
      </xsl:if>
    </tr>
    <xsl:apply-templates/>
  </table>
</xsl:template>

<!-- Individual key -->
<xsl:template match="KEY">
  <xsl:variable name="COUNT" select="count(preceding-sibling::*)"/>
  <tr class="r{$COUNT}">
    <td class="cell c0"><xsl:value-of select="@NAME"/></td>
    <td class="cell c1"><xsl:value-of select="@TYPE"/></td>
    <td class="cell c2"><xsl:value-of select="@FIELDS"/></td>
    <td class="cell c3">
      <xsl:if test="@REFTABLE">
        <xsl:value-of select="@REFTABLE"/> (<xsl:value-of select="@REFFIELDS"/>)
      </xsl:if>
    </td>
    <xsl:if test="../*[normalize-space(@COMMENT)!='']">
      <td class="cell c4 lastcol"><xsl:call-template name="display-comment"/></td>
    </xsl:if>    
  </tr>
</xsl:template>

<!-- Indexes -->
<xsl:template match="INDEXES[INDEX]">
  <h4>Indexes</h4>
  <table class="generaltable boxaligncenter" cellspacing="1" cellpadding="5" width="100%"> 
    <tr>
      <th class="header c0" scope="col">Name</th>
      <th class="header c1" scope="col">Type</th>
      <th class="header c2" scope="col">Field(s)</th>
      <xsl:if test="*[normalize-space(@COMMENT)!='']">
        <th class="header c4 lastcol" scope="col">Description</th>
      </xsl:if>
    </tr>
    <xsl:apply-templates/>
  </table>
</xsl:template>

<!-- Individual index -->
<xsl:template match="INDEX">
  <xsl:variable name="COUNT" select="count(preceding-sibling::*)"/>
  <tr class="r{$COUNT}">
    <td class="cell c0"><xsl:value-of select="@NAME"/></td>
    <td class="cell c1">
      <xsl:choose>
        <xsl:when test="@UNIQUE='true'">Unique</xsl:when>
        <xsl:otherwise>Not unique</xsl:otherwise>
      </xsl:choose>
    </td>
    <td class="cell c2"><xsl:value-of select="@FIELDS"/></td>
    <xsl:if test="../*[normalize-space(@COMMENT)!='']">
      <td class="cell c4 lastcol"><xsl:call-template name="display-comment"/></td>
    </xsl:if>    
  </tr>
</xsl:template>

<xsl:template name="display-comment">
  <xsl:param name="PARA"/>
  <xsl:if test="normalize-space(@COMMENT)!=''">
    <xsl:choose>
      <xsl:when test="$PARA">
        <p class="xmldb_comment"><xsl:value-of select="@COMMENT"/></p>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="@COMMENT"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:if> 
</xsl:template>

</xsl:stylesheet>
