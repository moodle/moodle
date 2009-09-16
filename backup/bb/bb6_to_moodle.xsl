<?xml version='1.0'?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" encoding="UTF-8" />
<xsl:template match="/">
  <MOODLE_BACKUP>
   <INFO>
    <NAME>backup-from-blackboard.zip</NAME>
    <MOODLE_VERSION>2004083100</MOODLE_VERSION>
    <MOODLE_RELEASE>1.4</MOODLE_RELEASE>
    <BACKUP_VERSION>2004083100</BACKUP_VERSION>
    <BACKUP_RELEASE>1.4</BACKUP_RELEASE>
    <DATE>1094240862</DATE>
    <ORIGINAL_WWWROOT>INSERT URL HERE</ORIGINAL_WWWROOT>
    <DETAILS>
      <MOD>
        <NAME>assignment</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>chat</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>choice</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>forum</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>glossary</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>journal</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>label</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>lesson</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>quiz</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>resource</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>scorm</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>survey</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>wiki</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>workshop</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <USERS>course</USERS>
      <LOGS>false</LOGS>
      <USERFILES>true</USERFILES>
      <COURSEFILES>true</COURSEFILES>
    </DETAILS>
  </INFO>
  <COURSE>
    <!-- Get course specific information -->
    <xsl:apply-templates select="document('res00001.dat')//COURSE"/>

    <xsl:call-template name="modules" />
    
  <SECTIONS>
    <!-- Create a title section -->
    <xsl:for-each select="document('res00001.dat')" >
      <xsl:call-template name="title_section" />
    </xsl:for-each>


    <!-- Create a topic for each top level Bb item and add section modules ONE folder deep -->
    <xsl:for-each select="manifest/organizations/organization/item">
      <xsl:variable name="section_number" select="position()"/>
      <xsl:call-template name="sections">
            <xsl:with-param name="section_number" select="$section_number"/>
            <xsl:with-param name="recurse" >false</xsl:with-param>
          </xsl:call-template>
    </xsl:for-each>

    <!-- Create a topic for each second level Bb item which is a folder, recursively make section modules  -->
    <xsl:for-each select="manifest/organizations/organization/item/item">
      <xsl:sort order="descending" select="document(concat(@identifierref,'.dat'))/CONTENT/FLAGS/ISFOLDER/@value"/>
      <xsl:if test="document(concat(@identifierref,'.dat'))/CONTENT/FLAGS/ISFOLDER/@value = 'true'">
        <xsl:variable name="prev_sections" select="count(/manifest/organizations/tableofcontents/item)"/>
        <xsl:variable name="section_number" select="position()+$prev_sections"/>
        <xsl:call-template name="sections">
              <xsl:with-param name="section_number" select="$section_number"/>
              <xsl:with-param name="recurse" >true</xsl:with-param>
        </xsl:call-template>
      </xsl:if>
    </xsl:for-each>
  </SECTIONS>
    
  </COURSE>
  </MOODLE_BACKUP>
</xsl:template>

<xsl:template match="COURSE">
      <HEADER>
      <ID>2</ID>
      <CATEGORY>
        <ID></ID>
        <NAME><xsl:value-of select="CATEGORIES/CATEGORY/@value"/></NAME>
      </CATEGORY>
      <PASSWORD></PASSWORD>
      <IDNUMBER>4</IDNUMBER>
      <FORMAT>topics</FORMAT>
      <SHOWGRADES>1</SHOWGRADES>
      <BLOCKINFO>participants,activity_modules,search_forums,admin,course_list:news_items,calendar_upcoming,recent_activity</BLOCKINFO>
      <NEWSITEMS>5</NEWSITEMS>
      <TEACHER>Teacher</TEACHER>
      <TEACHERS>Teachers</TEACHERS>
      <STUDENT>Student</STUDENT>
      <STUDENTS>Students</STUDENTS>
      <GUEST>0</GUEST>
      <STARTDATE>1094270400</STARTDATE>
      <ENROLPERIOD>0</ENROLPERIOD>
      <NUMSECTIONS>10</NUMSECTIONS>
      <MAXBYTES>2097152</MAXBYTES>
      <SHOWREPORTS>0</SHOWREPORTS>
      <GROUPMODE>0</GROUPMODE>
      <GROUPMODEFORCE>0</GROUPMODEFORCE>
      <LANG></LANG>
      <COST></COST>
      <MARKER>0</MARKER>
      <VISIBLE>1</VISIBLE>
      <HIDDENSECTIONS>0</HIDDENSECTIONS>
      <TIMECREATED>1094240775</TIMECREATED>
      <TIMEMODIFIED>1094240775</TIMEMODIFIED>
      <SUMMARY><xsl:value-of select="DESCRIPTION"/></SUMMARY>
      <SHORTNAME><xsl:value-of select="COURSEID/@value"/></SHORTNAME>
      <FULLNAME><xsl:value-of select="TITLE/@value"/></FULLNAME>
      </HEADER>
</xsl:template>

<xsl:template name="title_section" match="resource">
    <SECTION>
      <ID>0</ID>
      <NUMBER>0</NUMBER>
      <SUMMARY>&lt;div style="text-align: center;"&gt;&lt;font size="5" style="font-family: arial,helvetica,sans-serif;"&gt;<xsl:value-of select="COURSE/TITLE/@value"/>&lt;/font&gt;&lt;/div&gt;
        <xsl:value-of select="COURSE/DESCRIPTION"/>
      </SUMMARY>
      <VISIBLE>1</VISIBLE>
      <MODS>
      </MODS>
    </SECTION>
</xsl:template>

<xsl:template name="sections" match="resource">
    <xsl:param name="section_number">1. </xsl:param>
    <xsl:param name="recurse"/>
    <SECTION>
      <ID><xsl:value-of select="$section_number"/></ID>
      <NUMBER><xsl:value-of select="$section_number"/></NUMBER>
      <SUMMARY>&lt;span style="font-weight: bold;"&gt;<xsl:value-of select="title"/>&lt;/span&gt;</SUMMARY>
      <VISIBLE>1</VISIBLE>
      <MODS>
        
        <xsl:if test="$recurse = 'true'">
          <xsl:variable name="mod_number" select="substring-after(@identifierref,'res')"/>
          <xsl:call-template name="item_recurse_files" >
                <xsl:with-param name="mod_number" select="$mod_number"/>
                <xsl:with-param name="indent" >0</xsl:with-param>
                <xsl:with-param name="recurse" select="$recurse" />
          </xsl:call-template>
        
        </xsl:if>

        <xsl:if test="$recurse = 'false'">
        <xsl:for-each select="item">
          <xsl:variable name="mod_number" select="substring-after(@identifierref,'res')"/>
          <xsl:call-template name="item" >
                <xsl:with-param name="mod_number" select="$mod_number"/>
                <xsl:with-param name="indent" >0</xsl:with-param>
              </xsl:call-template>
        </xsl:for-each> 
        </xsl:if>
  

      </MODS>
    </SECTION>
  </xsl:template>

<xsl:template name="item_recurse_files">
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="indent">1. </xsl:param>
   <xsl:param name="recurse"/>

   
    <!-- Create one section-mod -->
    <xsl:for-each select="document(concat(@identifierref,'.dat'))">
      <xsl:call-template name="section_mod">
          <xsl:with-param name="mod_number" select="$mod_number"/>
          <xsl:with-param name="indent" select="$indent"/>
      </xsl:call-template>
    </xsl:for-each>
    
    <!-- Depth first recursion to preserve order -->
    <xsl:for-each select="item">
      <xsl:variable name="m_number" select="substring-after(@identifierref,'res')"/>
      <xsl:call-template name="item_recurse_files" >
            <xsl:with-param name="mod_number" select="$m_number"/>
            <xsl:with-param name="indent" select="$indent + 1"/>
      </xsl:call-template>
    </xsl:for-each>
    
</xsl:template>

<xsl:template name="item">
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="indent">1. </xsl:param>

   <GETHERE></GETHERE> 
   <xsl:if test="document(concat(@identifierref,'.dat'))/CONTENT/FLAGS/ISFOLDER/@value != 'true' or document(concat(@identifierref,'.dat'))/EXTERNALLINK/DESCRIPTION/FLAGS/ISHTML/@value ='true'">
    <!-- Create one section-mod -->
    <xsl:for-each select="document(concat(@identifierref,'.dat'))">
      <xsl:call-template name="section_mod">
            <xsl:with-param name="mod_number" select="$mod_number"/>
            <xsl:with-param name="indent" select="$indent"/>
      </xsl:call-template>
    </xsl:for-each>
   </xsl:if>
   
    
</xsl:template>

<!-- Determines the type of section mod entry and calls the appropriate creation template -->
<xsl:template name="section_mod" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="contenttype" />
   <xsl:param name="indent">1. </xsl:param>

  <!-- Every file will have a label module describing it -->
  <xsl:choose>
      <!-- Detected a file -->
      <xsl:when test="CONTENT/FILE/@id != '' or CONTENT/FILES/FILE/NAME != ''">
        <!-- Create a label -->
        <xsl:call-template name="section_mod_label">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
        </xsl:call-template>

        <!-- Create a resource -->
        <xsl:call-template name="section_mod_resource">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
        </xsl:call-template>

      </xsl:when>

      <!-- Detected a folder -->
      <xsl:when test="CONTENT/FLAGS/ISFOLDER/@value = 'true'">
        <MAKINGLABEL></MAKINGLABEL>
        <!-- Create a label -->
        <xsl:call-template name="section_mod_label">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
            </xsl:call-template>
      </xsl:when>

      <!-- Detected text -->
      <xsl:when test="CONTENT/MAINDATA/FLAGS/ISHTML/@value = 'true' or CONTENT/BODY/TYPE/@value = 'H' ">
        <MAKINGTEXT></MAKINGTEXT>
        <!-- Create a resource -->
        <xsl:call-template name="section_mod_resource">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
        </xsl:call-template>
      </xsl:when>

      <!-- Detected external link -->
      <xsl:when test="EXTERNALLINK/TITLE/@value != '' ">
         <!-- Create a label -->
        <xsl:call-template name="section_mod_label">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
        </xsl:call-template>

        <!-- Create a resource -->
        <xsl:call-template name="section_mod_externallink">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <UNKNOWN>
        </UNKNOWN>
      </xsl:otherwise>

  </xsl:choose>


</xsl:template>

<!-- Creates one section-mod-label -->
<xsl:template name="section_mod_label" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="indent">1. </xsl:param>
  <MOD>
    <ID>1<xsl:value-of select="$mod_number"/>0</ID>
	  <ZIBA_NAME>
      <!-- BB5.5 -->
      <xsl:value-of select="CONTENT/TITLE"/>
      <!-- BB6 -->
      <xsl:value-of select="CONTENT/TITLE/@value"/>
	  </ZIBA_NAME>
    <TYPE>label</TYPE>
    <INSTANCE><xsl:value-of select="$mod_number"/></INSTANCE>
    <ADDED>1094240775</ADDED>
    <DELETED>0</DELETED>
    <SCORE>0</SCORE>
    <INDENT><xsl:value-of select="$indent"/></INDENT>
    <VISIBLE>1</VISIBLE>
    <GROUPMODE>0</GROUPMODE>
  </MOD>
   
</xsl:template>

<!-- Creates one section-mod-resource -->
<xsl:template name="section_mod_resource" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="indent">1. </xsl:param>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/>0</ID>
	  <ZIBA_NAME>
      <!-- BB5.5 -->
      <xsl:value-of select="CONTENT/TITLE"/>
      <!-- BB6 -->
      <xsl:value-of select="CONTENT/TITLE/@value"/>
	  </ZIBA_NAME>
    <TYPE>resource</TYPE>
    <INSTANCE><xsl:value-of select="$mod_number"/></INSTANCE>
    <ADDED>1094240775</ADDED>
    <DELETED>0</DELETED>
    <SCORE>0</SCORE>
    <INDENT><xsl:value-of select="$indent"/></INDENT>
    <VISIBLE>1</VISIBLE>
    <GROUPMODE>0</GROUPMODE>
  </MOD>
   
</xsl:template>

<!-- Creates one section-mod-externallink -->
<xsl:template name="section_mod_externallink" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="indent">1. </xsl:param>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/>0</ID>
	  <ZIBA_NAME>
      <xsl:value-of select="EXTERNALLINK/TITLE/@value"/>
	  </ZIBA_NAME>
    <TYPE>resource</TYPE>
    <INSTANCE><xsl:value-of select="$mod_number"/></INSTANCE>
    <ADDED>1094240775</ADDED>
    <DELETED>0</DELETED>
    <SCORE>0</SCORE>
    <INDENT><xsl:value-of select="$indent"/></INDENT>
    <VISIBLE>1</VISIBLE>
    <GROUPMODE>0</GROUPMODE>
  </MOD>
   
</xsl:template>

<!-- Creates a module-label entry -->
<xsl:template name="module_label" >
   <xsl:param name="mod_number">1. </xsl:param>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
    <MODTYPE>label</MODTYPE>
    <NAME>
      <!-- for CONTENT text -->
      <xsl:value-of select="TITLE"/>
      <!-- for EXTERNALLINK text -->
      <xsl:value-of select="TITLE/@value"/>
    </NAME>
    <CONTENT>
      &lt;span style="font-style: italic;"&gt;
      <!-- for CONTENT text -->
      <xsl:value-of select="TITLE"/>
      <!-- for EXTERNALLINK text -->
      <xsl:value-of select="TITLE/@value"/>
      :&lt;/span&gt;
      <!-- for CONTENT text -->
      <xsl:value-of select="MAINDATA/TEXT"/> 
      <!-- for EXTERNALLINK text -->
      <xsl:value-of select="DESCRIPTION/TEXT"/> 
      <!-- for BB6 text -->
      <xsl:value-of select="BODY/TEXT"/> 
    </CONTENT>
    <TIMEMODIFIED>1094240775</TIMEMODIFIED>
  </MOD>
</xsl:template>
   
<!-- Creates one module-file entry -->
<xsl:template name="module_file" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="identifier"/>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
    <MODTYPE>resource</MODTYPE>
    <NAME>
      <!-- BB5 -->
     <xsl:value-of select="FILES/FILEREF/RELFILE/@value"/>
      <!-- BB6 -->
     <xsl:value-of select="FILES/FILE/NAME"/>
    </NAME>
    <TYPE>file</TYPE>
    <REFERENCE><!-- BB5 --><xsl:value-of select="FILES/FILEREF/CONTENTID/@value"/><!-- BB6 --><xsl:value-of select="$identifier"/>/<!-- BB5 --><xsl:value-of select="FILES/FILEREF/RELFILE/@value"/><!-- BB6 --><xsl:value-of select="FILES/FILE/NAME"/></REFERENCE>
    <SUMMARY>
     <xsl:value-of select="MAINDATA/TEXT"/>
    </SUMMARY>
    <ALLTEXT></ALLTEXT>
    <POPUP></POPUP>
    <OPTIONS></OPTIONS>
    <TIMEMODIFIED>1094240775</TIMEMODIFIED>
  </MOD>
</xsl:template>

<!-- Creates one module-text entry -->
<xsl:template name="module_text" >
   <xsl:param name="mod_number">1. </xsl:param>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
    <MODTYPE>resource</MODTYPE>
    <NAME>
      <!-- BB5.5 -->
      <xsl:value-of select="TITLE"/>
      <!-- BB6 -->
      <xsl:value-of select="TITLE/@value"/>
    </NAME>
    <TYPE>text</TYPE>
    <REFERENCE></REFERENCE>
    <SUMMARY>
      <!-- BB5.5 -->
      <xsl:value-of select="TITLE"/>
      <!-- BB6 -->
      <xsl:value-of select="TITLE/@value"/>
    </SUMMARY>
    <ALLTEXT>
      <!-- BB5.5 -->
      <xsl:value-of select="MAINDATA/TEXT"/>
      <!-- BB6 -->
      <xsl:value-of select="BODY/TEXT"/>
    </ALLTEXT>
    <POPUP></POPUP>
    <OPTIONS></OPTIONS>
    <TIMEMODIFIED>1094240775</TIMEMODIFIED>
  </MOD>
</xsl:template>

<!-- Creates one module-link entry -->
<xsl:template name="module_link" >
   <xsl:param name="mod_number">1. </xsl:param>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
    <MODTYPE>resource</MODTYPE>
    <NAME>
      <xsl:value-of select="URL/@value"/>
    </NAME>
    <TYPE>file</TYPE>
    <REFERENCE>
      <xsl:value-of select="URL/@value"/>
    </REFERENCE>
    <SUMMARY>
      <xsl:value-of select="TITLE/@value"/>&lt;br/&gt;
      <xsl:value-of select="URL/@value"/>
    </SUMMARY>
    <ALLTEXT>
      <xsl:value-of select="DESCRIPTION/TEXT"/>
    </ALLTEXT>
    <POPUP></POPUP>
    <OPTIONS></OPTIONS>
    <TIMEMODIFIED>1094240775</TIMEMODIFIED>
  </MOD>
</xsl:template>

<!-- Create a CONTENT module entries -->
<xsl:template match="EXTERNALLINK">
   <xsl:param name="mod_number">1. </xsl:param>
       <!-- Every link module will have a label module describing it -->
       <xsl:call-template name="module_label">
        <xsl:with-param name="mod_number" select="$mod_number"/>
       </xsl:call-template>
       
       <xsl:call-template name="module_link">
        <xsl:with-param name="mod_number" select="$mod_number"/>
       </xsl:call-template>

</xsl:template>

<!-- Create a CONTENT module entries -->
<xsl:template match="CONTENT">
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="identifier"/>

  <xsl:choose>
    <!-- Detected a file 
    
      <FILEFOUND></FILEFOUND>
    -->
      <xsl:when test="FILES/FILE/@id != ''">

       <!-- Every file module will have a label module describing it -->
       <xsl:call-template name="module_label">
        <xsl:with-param name="mod_number" select="$mod_number"/>
       </xsl:call-template>
       
       <xsl:call-template name="module_file">
        <xsl:with-param name="mod_number" select="$mod_number"/>
        <xsl:with-param name="identifier" select="$identifier"/>
       </xsl:call-template>

      </xsl:when>
      
      <!-- Detected a folder 
     <FOLDERFOUND></FOLDERFOUND>
      -->
      <xsl:when test="FLAGS/ISFOLDER/@value = 'true'">
        
        <xsl:call-template name="module_label">
          <xsl:with-param name="mod_number" select="$mod_number"/>
        </xsl:call-template>
        
      </xsl:when>
      
      <!-- Detected text 
     <TEXTFOUND></TEXTFOUND>
      -->
      <xsl:when test="MAINDATA/FLAGS/ISHTML/@value = 'true' or BODY/TYPE/@value = 'H' ">
        
       <xsl:call-template name="module_text">
        <xsl:with-param name="mod_number" select="$mod_number"/>
       </xsl:call-template>
      
      </xsl:when>
    
      <xsl:otherwise>
    <UNKNOWN>
      <xsl:value-of select="TITLE"/>
    </UNKNOWN>
        </xsl:otherwise>
  </xsl:choose>

</xsl:template>


<!-- Creates all module entries -->
<xsl:template name="modules" match="resource">
  <MODULES>
    <xsl:for-each select="//resource">
    <xsl:variable name="mod_number" select="substring-after(@identifier,'res')"/>
    <xsl:variable name="identifier" select="@identifier"/>
    <xsl:for-each select="document(concat(@identifier,'.dat'))">
          <xsl:apply-templates select="//FORUM">
            <xsl:with-param name="mod_number" select="$mod_number"/>
          </xsl:apply-templates>
          <xsl:apply-templates select="//CONTENT">
            <xsl:with-param name="mod_number" select="$mod_number"/>
            <xsl:with-param name="identifier" select="$identifier"/>
          </xsl:apply-templates>
          <xsl:apply-templates select="//EXTERNALLINK">
            <xsl:with-param name="mod_number" select="$mod_number"/>
          </xsl:apply-templates>
    </xsl:for-each>
  </xsl:for-each>
</MODULES>
</xsl:template>
  
<!-- ############# Forum conversion ################# -->

<xsl:template match="FORUM">
   <xsl:param name="mod_number">1. </xsl:param>
	<MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
		<MODTYPE>forum</MODTYPE>
		<TYPE>general</TYPE>
	  <NAME>
      <xsl:value-of select="TITLE/@value"/>
	  </NAME>
	  <INTRO>
      <xsl:value-of select="DESCRIPTION/TEXT"/> 
	  </INTRO>
	  <OPEN>2</OPEN>
	  <ASSESSED>0</ASSESSED>
	  <ASSESSPUBLIC>0</ASSESSPUBLIC>
	  <ASSESSTIMESTART>0</ASSESSTIMESTART>
	  <ASSESSTIMEFINISH>0</ASSESSTIMEFINISH>
	  <MAXBYTES>0</MAXBYTES>
	  <SCALE>0</SCALE>
	  <FORCESUBSCRIBE>0</FORCESUBSCRIBE>
	  <RSSTYPE>0</RSSTYPE>
	  <RSSARTICLES>0</RSSARTICLES>
	  <TIMEMODIFIED></TIMEMODIFIED>
    <!--
    <DISCUSSIONS>
      <xsl:for-each select="MESSAGETHREADS/MSG">
      <xsl:variable name="discussion_id" select="position()"/>
      <DISCUSSION>
        <ID>
          <xsl:value-of select="$mod_number"/>0<xsl:value-of select="$discussion_id"/>
        </ID>
        <NAME>
          <xsl:value-of select="TITLE/@value"/> 
        </NAME>
        <FIRSTPOST>2</FIRSTPOST>
        <USERID>1</USERID>
        <GROUPID>-1</GROUPID>
        <ASSESSED>1</ASSESSED>
        <TIMEMODIFIED>1094748430</TIMEMODIFIED>
        <USERMODIFIED>1</USERMODIFIED>
        <POSTS>
          <xsl:call-template name="MSG">
            <xsl:with-param name="parent" select="0"/>
            <xsl:with-param name="post_id">
            <xsl:value-of select="$mod_number"/>0<xsl:value-of select="$discussion_id"/>0<xsl:value-of select="position()"/>
            </xsl:with-param>
          </xsl:call-template>
        </POSTS>
      </DISCUSSION>
      </xsl:for-each>
    </DISCUSSIONS>
      -->
	</MOD>
</xsl:template>

<xsl:template name="MSG" match="MSG">
    <xsl:param name="parent" select="1"/>
    <xsl:param name="post_id" select="1"/>
    <POST>
      <ID><xsl:value-of select="$post_id"/></ID>
      <PARENT> <xsl:value-of select="$parent"/></PARENT>
      <USERID>1</USERID>
      <CREATED>1094748430</CREATED>
      <MODIFIED>1094748430</MODIFIED>
      <MAILED>1</MAILED>
      <SUBJECT><xsl:value-of select="TITLE/@value"/></SUBJECT>
      <MESSAGE><xsl:value-of select="MESSAGETEXT"/></MESSAGE>
      <FORMAT>1</FORMAT>
      <ATTACHMENT></ATTACHMENT>
      <TOTALSCORE>0</TOTALSCORE>
    </POST>

      <xsl:for-each select="MSG">
          <xsl:call-template name="MSG">
            <xsl:with-param name="parent" select="$post_id"/>
            <xsl:with-param name="post_id">
            <xsl:value-of select="$post_id"/>0<xsl:value-of select="position()"/>
            </xsl:with-param>
          </xsl:call-template>
      </xsl:for-each>
</xsl:template>
</xsl:stylesheet>




