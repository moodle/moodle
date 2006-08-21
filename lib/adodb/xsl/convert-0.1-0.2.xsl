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
		
		<xsl:element name="schema">
			<xsl:attribute name="version">0.2</xsl:attribute>
			
			<xsl:apply-templates select="schema/table|schema/sql"/>
		</xsl:element>
	</xsl:template>
	
	<!-- Table -->
	<xsl:template match="table">
		<xsl:variable name="table_name" select="@name"/>
		
		<xsl:element name="table">
			<xsl:attribute name="name"><xsl:value-of select="$table_name"/></xsl:attribute>
			
			<xsl:if test="string-length(@platform) > 0">
				<xsl:attribute name="platform"><xsl:value-of select="@platform"/></xsl:attribute>
			</xsl:if>
			
			<xsl:if test="string-length(@version) > 0">
				<xsl:attribute name="version"><xsl:value-of select="@version"/></xsl:attribute>
			</xsl:if>
			
			<xsl:apply-templates select="descr[1]"/>
			
			<xsl:choose>
				<xsl:when test="count(DROP) > 0">
					<xsl:element name="DROP"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="field"/>
				</xsl:otherwise>
			</xsl:choose>
			
			<xsl:apply-templates select="constraint"/>
			
			<xsl:apply-templates select="../index[@table=$table_name]"/>
		</xsl:element>
	</xsl:template>
	
	<!-- Field -->
	<xsl:template match="field">
		<xsl:element name="field">
			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:attribute name="type"><xsl:value-of select="@type"/></xsl:attribute>
			
			<xsl:if test="string-length(@size) > 0">
				<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
			</xsl:if>
			
			<xsl:choose>
				<xsl:when test="count(PRIMARY) > 0">
					<xsl:element name="PRIMARY"/>
				</xsl:when>
				<xsl:when test="count(KEY) > 0">
					<xsl:element name="KEY"/>
				</xsl:when>
				<xsl:when test="count(NOTNULL) > 0">
					<xsl:element name="NOTNULL"/>
				</xsl:when>
			</xsl:choose>
			
			<xsl:choose>
				<xsl:when test="count(AUTO) > 0">
					<xsl:element name="AUTO"/>
				</xsl:when>
				<xsl:when test="count(AUTOINCREMENT) > 0">
					<xsl:element name="AUTOINCREMENT"/>
				</xsl:when>
			</xsl:choose>
			
			<xsl:choose>
				<xsl:when test="count(DEFAULT) > 0">
					<xsl:element name="DEFAULT">
						<xsl:attribute name="value">
							<xsl:value-of select="DEFAULT[1]/@value"/>
						</xsl:attribute>
					</xsl:element>
				</xsl:when>
				<xsl:when test="count(DEFDATE) > 0">
					<xsl:element name="DEFDATE">
						<xsl:attribute name="value">
							<xsl:value-of select="DEFDATE[1]/@value"/>
						</xsl:attribute>
					</xsl:element>
				</xsl:when>
				<xsl:when test="count(DEFTIMESTAMP) > 0">
					<xsl:element name="DEFTIMESTAMP">
						<xsl:attribute name="value">
							<xsl:value-of select="DEFTIMESTAMP[1]/@value"/>
						</xsl:attribute>
					</xsl:element>
				</xsl:when>
			</xsl:choose>
			
			<xsl:if test="count(NOQUOTE) > 0">
				<xsl:element name="NOQUOTE"/>
			</xsl:if>
			
			<xsl:apply-templates select="constraint"/>
		</xsl:element>
	</xsl:template>
	
	<!-- Constraint -->
	<xsl:template match="constraint">
		<xsl:element name="constraint">
			<xsl:value-of select="normalize-space(text())"/>
		</xsl:element>
	</xsl:template>
	
	<!-- Index -->
	<xsl:template match="index">
		<xsl:element name="index">
			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
			
			<xsl:apply-templates select="descr[1]"/>
			
			<xsl:if test="count(CLUSTERED) > 0">
				<xsl:element name="CLUSTERED"/>
			</xsl:if>
			
			<xsl:if test="count(BITMAP) > 0">
				<xsl:element name="BITMAP"/>
			</xsl:if>
			
			<xsl:if test="count(UNIQUE) > 0">
				<xsl:element name="UNIQUE"/>
			</xsl:if>
			
			<xsl:if test="count(FULLTEXT) > 0">
				<xsl:element name="FULLTEXT"/>
			</xsl:if>
			
			<xsl:if test="count(HASH) > 0">
				<xsl:element name="HASH"/>
			</xsl:if>
			
			<xsl:choose>
				<xsl:when test="count(DROP) > 0">
					<xsl:element name="DROP"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates select="col"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:element>
	</xsl:template>
	
	<!-- Index Column -->
	<xsl:template match="col">
		<xsl:element name="col">
			<xsl:value-of select="normalize-space(text())"/>
		</xsl:element>
	</xsl:template>
	
	<!-- SQL QuerySet -->
	<xsl:template match="sql">
		<xsl:element name="sql">
			<xsl:if test="string-length(@platform) > 0">
				<xsl:attribute name="platform"><xsl:value-of select="@platform"/></xsl:attribute>
			</xsl:if>
			
			<xsl:if test="string-length(@key) > 0">
				<xsl:attribute name="key"><xsl:value-of select="@key"/></xsl:attribute>
			</xsl:if>
			
			<xsl:if test="string-length(@prefixmethod) > 0">
				<xsl:attribute name="prefixmethod"><xsl:value-of select="@prefixmethod"/></xsl:attribute>
			</xsl:if>
			
			<xsl:apply-templates select="descr[1]"/>
			<xsl:apply-templates select="query"/>
		</xsl:element>
	</xsl:template>
	
	<!-- Query -->
	<xsl:template match="query">
		<xsl:element name="query">
			<xsl:if test="string-length(@platform) > 0">
				<xsl:attribute name="platform"><xsl:value-of select="@platform"/></xsl:attribute>
			</xsl:if>
			
			<xsl:value-of select="normalize-space(text())"/>
		</xsl:element>
	</xsl:template>
	
	<!-- Description -->
	<xsl:template match="descr">
		<xsl:element name="descr">
			<xsl:value-of select="normalize-space(text())"/>
		</xsl:element>
	</xsl:template>
</xsl:stylesheet>