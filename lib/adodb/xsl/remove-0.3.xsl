<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
	<xsl:output method="xml" indent="yes" omit-xml-declaration="no" encoding="UTF-8"/>
	
	<!-- Schema -->
	<xsl:template match="/">
		<xsl:comment>
ADODB XMLSchema
http://adodb-xmlschema.sourceforge.net
</xsl:comment>
		
		<xsl:comment>
Uninstallation Schema
</xsl:comment>
		
		<xsl:element name="schema">
			<xsl:attribute name="version">0.3</xsl:attribute>
			
			<xsl:apply-templates select="schema/table">
				<xsl:sort select="position()" data-type="number" order="descending"/>
			</xsl:apply-templates>
		</xsl:element>
	</xsl:template>
	
	<!-- Table -->
	<xsl:template match="table">
		<xsl:if test="count(DROP) = 0">
			<xsl:element name="table">
				<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
				
				<xsl:if test="string-length(@platform) > 0">
					<xsl:attribute name="platform"><xsl:value-of select="@platform"/></xsl:attribute>
				</xsl:if>
				
				<xsl:if test="string-length(@version) > 0">
					<xsl:attribute name="version"><xsl:value-of select="@version"/></xsl:attribute>
				</xsl:if>
				
				<xsl:apply-templates select="descr[1]"/>
				
				<xsl:element name="DROP"/>
			</xsl:element>
		</xsl:if>
	</xsl:template>
	
	<!-- Description -->
	<xsl:template match="descr">
		<xsl:element name="descr">
			<xsl:value-of select="normalize-space(text())"/>
		</xsl:element>
	</xsl:template>
</xsl:stylesheet>