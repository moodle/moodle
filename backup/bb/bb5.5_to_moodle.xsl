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
        <INCLUDED>false</INCLUDED>
        <USERINFO>false</USERINFO>
      </MOD>
      <MOD>
        <NAME>resource</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <MOD>
        <NAME>scorm</NAME>
        <INCLUDED>false</INCLUDED>
        <USERINFO>false</USERINFO>
      </MOD>
      <MOD>
        <NAME>survey</NAME>
        <INCLUDED>false</INCLUDED>
        <USERINFO>false</USERINFO>
      </MOD>
      <MOD>
        <NAME>wiki</NAME>
        <INCLUDED>false</INCLUDED>
        <USERINFO>false</USERINFO>
      </MOD>
      <MOD>
        <NAME>workshop</NAME>
        <INCLUDED>true</INCLUDED>
        <USERINFO>true</USERINFO>
      </MOD>
      <USERS>course</USERS>
      <LOGS>false</LOGS>
      <USERFILES>false</USERFILES>
      <COURSEFILES>true</COURSEFILES>
    </DETAILS>
  </INFO>
  <COURSE>
    <!-- Get course specific information -->
    <xsl:apply-templates select="document('res00001.dat')//COURSE"/>

    
  <SECTIONS>
    <!-- Create a title section -->
    <xsl:for-each select="document('res00001.dat')" >
      <xsl:call-template name="title_section" />
    </xsl:for-each>


    <!-- Create a topic for each top level Bb item and add section modules ONE folder deep -->
    <xsl:for-each select="manifest/organizations/tableofcontents/item">
      <xsl:variable name="section_number" select="position()"/>
      <xsl:call-template name="sections">
            <xsl:with-param name="section_number" select="$section_number"/>
            <xsl:with-param name="recurse" >false</xsl:with-param>
          </xsl:call-template>
    </xsl:for-each>

    <!-- Create a topic for each second level Bb item which is a folder, recursively make section modules  -->
    <xsl:for-each select="manifest/organizations/tableofcontents/item/item">
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
    
  <MODULES>
    <xsl:call-template name="modules" />
  </MODULES>
    
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
      <GUEST>
        <xsl:choose>
          <xsl:when test="FLAGS/ALLOWGUESTS/@value = 'true' ">1</xsl:when>
          <xsl:when test="FLAGS/ALLOWGUESTS/@value = 'false' ">0</xsl:when>
          <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
      </GUEST>
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
      <VISIBLE>
        <xsl:choose>
          <xsl:when test="FLAGS/ISAVAILABLE/@value = 'true' ">1</xsl:when>
          <xsl:when test="FLAGS/ISAVAILABLE/@value = 'false' ">0</xsl:when>
          <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
      </VISIBLE>
      <HIDDENSECTIONS>0</HIDDENSECTIONS>
      <TIMECREATED>1094240775</TIMECREATED>
      <TIMEMODIFIED>1094240775</TIMEMODIFIED>
      <SUMMARY><xsl:value-of select="DESCRIPTION"/></SUMMARY>
      <SHORTNAME><xsl:value-of select="COURSEID/@value"/></SHORTNAME>
      <FULLNAME><xsl:value-of select="TITLE/@value"/></FULLNAME>
      </HEADER>
</xsl:template>

<!-- ############# Sections ############# -->

<xsl:template name="title_section" match="resource">
    <SECTION>
      <ID>0</ID>
      <NUMBER>0</NUMBER>
      <SUMMARY>&lt;div style="text-align: center;"&gt;&lt;font size="5" style="font-family: arial,helvetica,sans-serif;"&gt;<xsl:value-of select="COURSE/TITLE/@value"/>&lt;/font&gt;&lt;/div&gt;
        <xsl:value-of select="COURSE/DESCRIPTION"/>
      </SUMMARY>
      <VISIBLE>1</VISIBLE>
      <MODS>
          <xsl:call-template name="news_forum_section_mod" >
            <xsl:with-param name="mod_number">1</xsl:with-param>
          </xsl:call-template>
      </MODS>
    </SECTION>
</xsl:template>

<xsl:template name="sections" match="resource">
    <xsl:param name="section_number">1. </xsl:param>
    <xsl:param name="recurse"/>
    <SECTION>
      <ID><xsl:value-of select="$section_number"/></ID>
      <NUMBER><xsl:value-of select="$section_number"/></NUMBER>
      <SUMMARY>&lt;span style="font-weight: bold;"&gt;<xsl:value-of select="@title"/>&lt;/span&gt;</SUMMARY>
      <VISIBLE>1</VISIBLE>
      <MODS>
        
      <xsl:choose>
        <xsl:when test="$recurse = 'true'">
          <xsl:variable name="mod_number" select="substring-after(@identifierref,'res')"/>
          <xsl:call-template name="item_recurse_files" >
                <xsl:with-param name="mod_number" select="$mod_number"/>
                <xsl:with-param name="indent" >0</xsl:with-param>
                <xsl:with-param name="recurse" select="$recurse" />
          </xsl:call-template>
        
        </xsl:when>

        <xsl:when test="$recurse = 'false'">
        <xsl:for-each select="item">
          <xsl:variable name="mod_number" select="substring-after(@identifierref,'res')"/>
            <!-- Create one section-mod -->
            <xsl:for-each select="document(concat(@identifierref,'.dat'))">
              <xsl:call-template name="section_mod">
                    <xsl:with-param name="mod_number" select="$mod_number"/>
                    <xsl:with-param name="indent" select="0"/>
              </xsl:call-template>
            </xsl:for-each>
            
       </xsl:for-each> 
        </xsl:when>
      </xsl:choose>

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


<!-- Determines the type of section mod entry and calls the appropriate creation template -->
<xsl:template name="section_mod" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="contenttype" />
   <xsl:param name="indent">1. </xsl:param>

  <!-- Every file will have a label module describing it -->
  <xsl:choose>
      <!-- Detected one or more files -->
      <xsl:when test="CONTENT/FILES/FILEREF/RELFILE/@value != ''">
        <!-- Create a label -->
        <xsl:call-template name="section_mod_generic">
          <xsl:with-param name="mod_number" ><xsl:value-of select="$mod_number"/></xsl:with-param>
          <xsl:with-param name="indent" ><xsl:value-of select="$indent"/></xsl:with-param>
              <xsl:with-param name="type" >label</xsl:with-param>
        </xsl:call-template>
        
        <!-- Create a resource for each file -->
        <xsl:for-each select="CONTENT/FILES/FILEREF">
         <xsl:call-template name="section_mod_generic">
          <xsl:with-param name="mod_number" ><xsl:value-of select="$mod_number"/>0<xsl:value-of select="position()"/></xsl:with-param>
              <xsl:with-param name="indent" select="$indent + 1"/>
              <xsl:with-param name="type" >resource</xsl:with-param>
         </xsl:call-template>
        </xsl:for-each>
        
      </xsl:when>


      <!-- Detected a folder -->
      <xsl:when test="CONTENT/FLAGS/ISFOLDER/@value = 'true'">
        <!-- Create a label -->
        <xsl:call-template name="section_mod_generic">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
              <xsl:with-param name="type" >label</xsl:with-param>
        </xsl:call-template>
      </xsl:when>

      <!-- Detected text -->
      <xsl:when test="CONTENT/MAINDATA/FLAGS/ISHTML/@value = 'true'">
        <!-- Create a resource -->
        <xsl:call-template name="section_mod_generic">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
              <xsl:with-param name="type" >resource</xsl:with-param>
        </xsl:call-template>
      </xsl:when>

      <!-- Detected external link -->
      <xsl:when test="EXTERNALLINK/TITLE/@value != '' ">
         <!-- Create a label -->
        <xsl:call-template name="section_mod_generic">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
              <xsl:with-param name="type" >label</xsl:with-param>
        </xsl:call-template>

        <!-- Create a resource -->
        <xsl:call-template name="section_mod_generic">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
              <xsl:with-param name="type" >resource</xsl:with-param>
        </xsl:call-template>
      </xsl:when>

      <!-- Detected staffinfo -->
      <xsl:when test="STAFFINFO/COURSEID/@value != '' ">
        <!-- Create a resource -->
        <xsl:call-template name="section_mod_generic">
              <xsl:with-param name="mod_number" select="$mod_number"/>
              <xsl:with-param name="indent" select="$indent"/>
              <xsl:with-param name="type" >resource</xsl:with-param>
            </xsl:call-template> -->
      </xsl:when>
      <xsl:otherwise>
      </xsl:otherwise>

  </xsl:choose>


</xsl:template>

<!-- ############# Section Modules ############# -->
<!-- Creates one section module entry. 
     Works for types: label, resource (text), resource (externallink)
-->
<xsl:template name="section_mod_generic" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="indent">1. </xsl:param>
   <xsl:param name="type"/>
  
  <MOD>
    <ID><xsl:if test="$type = 'label'">1</xsl:if><xsl:value-of select="$mod_number"/>0</ID>
	  <ZIBA_NAME>
      <xsl:value-of select="CONTENT/TITLE"/>
      <xsl:value-of select="EXTERNALLINK/TITLE/@value"/>
	  </ZIBA_NAME>
    <TYPE><xsl:value-of select="$type"/></TYPE>
    <INSTANCE><xsl:value-of select="$mod_number"/></INSTANCE>
    <ADDED>1094240775</ADDED>
    <DELETED>0</DELETED>
    <SCORE>0</SCORE>
    <INDENT><xsl:value-of select="$indent"/></INDENT>
    <VISIBLE>1</VISIBLE>
    <GROUPMODE>0</GROUPMODE>
  </MOD>
   
</xsl:template>

<!-- ############# Modules ############# -->
<!-- Creates a module-label entry -->
<xsl:template name="module_label" >
   <xsl:param name="mod_number">1. </xsl:param>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
    <LABELFOUND></LABELFOUND>
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
    </CONTENT>
    <TIMEMODIFIED>1094240775</TIMEMODIFIED>
  </MOD>
</xsl:template>
   
<!-- Creates one module-file entry -->
<xsl:template name="module_file" >
   <xsl:param name="mod_number">1. </xsl:param>
   <xsl:param name="summary"/>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
    <MODTYPE>resource</MODTYPE>
    <NAME>
      <!-- <xsl:value-of select="FILES/FILEREF/RELFILE/@value"/> -->
     <xsl:value-of select="RELFILE/@value"/>
    </NAME>
    <TYPE>file</TYPE>
    <REFERENCE>
      <!-- <xsl:value-of select="FILES/FILEREF/CONTENTID/@value"/>/<xsl:value-of select="FILES/FILEREF/RELFILE/@value"/> -->
      <xsl:value-of select="CONTENTID/@value"/>/<xsl:value-of select="RELFILE/@value"/>
    </REFERENCE>
    <SUMMARY>
     <xsl:value-of select="$summary"/>
    </SUMMARY>
    <ALLTEXT></ALLTEXT>
    <POPUP></POPUP>
    <OPTIONS></OPTIONS>
    <TIMEMODIFIED>1094240775</TIMEMODIFIED>
  </MOD>
</xsl:template>

<!-- Creates one module-text-staffinfo entry -->
<!-- TODO staff photo -->
<xsl:template name="module_text_staffinfo" >
   <xsl:param name="mod_number">1. </xsl:param>
  <MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
    <MODTYPE>resource</MODTYPE>
    <NAME>
      <xsl:value-of select="CONTACT/NAME/FORMALTITLE/@value"/><xsl:text> </xsl:text><xsl:value-of select="CONTACT/NAME/GIVEN/@value"/><xsl:text> </xsl:text><xsl:value-of select="CONTACT/NAME/FAMILY/@value"/>
    </NAME>
    <TYPE>text</TYPE>
    <REFERENCE></REFERENCE>
    <SUMMARY>
      <xsl:value-of select="CONTACT/NAME/FORMALTITLE/@value"/><xsl:text> </xsl:text><xsl:value-of select="CONTACT/NAME/GIVEN/@value"/><xsl:text> </xsl:text><xsl:value-of select="CONTACT/NAME/FAMILY/@value"/>
    </SUMMARY>
    <ALLTEXT>
      Title:<xsl:value-of select="CONTACT/NAME/FORMALTITLE/@value"/>
      Given Name:<xsl:value-of select="CONTACT/NAME/GIVEN/@value"/>
      Family Name:<xsl:value-of select="CONTACT/NAME/FAMILY/@value"/>
      Phone:<xsl:value-of select="CONTACT/PHONE"/>
      Office Hours:<xsl:value-of select="CONTACT/OFFICE/HOURS"/>
      Office Address:<xsl:value-of select="CONTACT/OFFICE/ADDRESS"/>
      Homepage:<xsl:value-of select="HOMEPAGE/@value"/>
    </ALLTEXT>
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
      <xsl:value-of select="TITLE"/>
      <!-- For announcements -->
      <xsl:value-of select="TITLE/@value"/>
    </NAME>
    <TYPE>text</TYPE>
    <REFERENCE></REFERENCE>
    <SUMMARY>
      <xsl:value-of select="TITLE"/>
      <!-- For announcements -->
      <xsl:value-of select="TITLE/@value"/>
    </SUMMARY>
    <ALLTEXT>
      <xsl:value-of select="MAINDATA/TEXT"/>
      <!-- For announcements -->
      <xsl:value-of select="DESCRIPTION/TEXT"/>
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

<!-- ############# Modules Decisions ############# -->

<!-- Creates all module entries -->
<xsl:template name="modules" match="resource">
  <!-- Create the News Forum Module -->
       <xsl:call-template name="news_forum_mod">
         <xsl:with-param name="mod_number" >1</xsl:with-param>
       </xsl:call-template>
  <!-- Create all other modules -->
  <xsl:for-each select="//resource">
    <xsl:variable name="mod_number" select="substring-after(@identifier,'res')"/>
    <xsl:for-each select="document(concat('',@file))">
          <xsl:apply-templates select="//FORUM">
            <xsl:with-param name="mod_number" select="$mod_number"/>
          </xsl:apply-templates>
          <xsl:apply-templates select="//CONTENT">
            <xsl:with-param name="mod_number" select="$mod_number"/>
          </xsl:apply-templates>
          <xsl:apply-templates select="//EXTERNALLINK">
            <xsl:with-param name="mod_number" select="$mod_number"/>
          </xsl:apply-templates>
          <xsl:apply-templates select="//STAFFINFO">
            <xsl:with-param name="mod_number" select="$mod_number"/>
          </xsl:apply-templates>
    </xsl:for-each>
  </xsl:for-each>
</xsl:template>
  

<!-- Create an EXTERNALLINK module entry -->
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

<!-- Create a STAFFINFO module entry -->
<xsl:template match="STAFFINFO">
   <xsl:param name="mod_number">1. </xsl:param>
       <!-- Every staffinfo module will have a label module describing it -->
       <xsl:call-template name="module_text_staffinfo">
        <xsl:with-param name="mod_number" select="$mod_number"/>
       </xsl:call-template>

</xsl:template>

<!-- Create a CONTENT module entry -->
<xsl:template match="CONTENT">
   <xsl:param name="mod_number">1. </xsl:param>

  <xsl:choose>
      <!-- Detected a file -->
      <xsl:when test="FILES/FILEREF/RELFILE/@value != ''">

       <!-- Every file module will have a label module describing it -->
       <xsl:call-template name="module_label">
        <xsl:with-param name="mod_number" select="$mod_number"/>
       </xsl:call-template>
       
       <xsl:variable name="summary" select="MAINDATA/TEXT"/>
       
      <xsl:for-each select="FILES/FILEREF">
       <xsl:call-template name="module_file">
        <xsl:with-param name="mod_number" ><xsl:value-of select="$mod_number"/>0<xsl:value-of select="position()"/></xsl:with-param>
        <xsl:with-param name="summary" ><xsl:value-of select="$summary"/></xsl:with-param>
       </xsl:call-template>
      </xsl:for-each>

      </xsl:when>
      
      <!-- Detected a folder -->
      <xsl:when test="FLAGS/ISFOLDER/@value = 'true'">
        
        <xsl:call-template name="module_label">
          <xsl:with-param name="mod_number" select="$mod_number"/>
        </xsl:call-template>
        
      </xsl:when>
      
      <!-- Detected text -->
      <xsl:when test="MAINDATA/FLAGS/ISHTML/@value = 'true'">
        
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

<xsl:template name="news_forum_section_mod" >
   <xsl:param name="mod_number">1. </xsl:param>
  <MOD>
    <ID>1</ID>
	  <ZIBA_NAME>
      News forum
	  </ZIBA_NAME>
    <TYPE>news</TYPE>
    <INSTANCE>1</INSTANCE>
    <ADDED>1094240775</ADDED>
    <DELETED>0</DELETED>
    <SCORE>0</SCORE>
    <INDENT>0</INDENT>
    <VISIBLE>1</VISIBLE>
    <GROUPMODE>0</GROUPMODE>
  </MOD>
</xsl:template>

<xsl:template name="news_forum_mod" >
   <xsl:param name="mod_number">1. </xsl:param>
	<MOD>
    <ID><xsl:value-of select="$mod_number"/></ID>
		<MODTYPE>forum</MODTYPE>
		<TYPE>news</TYPE>
	  <NAME>News forum</NAME>
	  <INTRO>General news and announcements</INTRO>
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
    <DISCUSSIONS>
      <xsl:for-each select="//resource">
        <xsl:variable name="m_number" select="substring-after(@identifier,'res')"/>
        <xsl:variable name="discussion_id" select="position()"/>
        <xsl:for-each select="document(concat('',@file))">
         <xsl:if test="//ANNOUNCEMENT/TITLE/@value != ''">
          <xsl:call-template name="ANNOUNCEMENT">
            <xsl:with-param name="discussion_id" select="$discussion_id"/>
          </xsl:call-template>
        </xsl:if>
         </xsl:for-each>
       </xsl:for-each>

     </DISCUSSIONS>
	</MOD>
</xsl:template>

<!-- Create an ANNOUNCEMENT forum entry -->
<xsl:template name="ANNOUNCEMENT" >
  <xsl:param name="discussion_id">1. </xsl:param>
      <DISCUSSION>
        <ID>
          <xsl:value-of select="$discussion_id"/>
        </ID>
        <NAME><xsl:value-of select="//ANNOUNCEMENT/TITLE/@value"/></NAME>
        <FIRSTPOST><xsl:value-of select="$discussion_id"/></FIRSTPOST>
        <USERID>1</USERID>
        <GROUPID>-1</GROUPID>
        <ASSESSED>1</ASSESSED>
        <TIMEMODIFIED>1094748430</TIMEMODIFIED>
        <USERMODIFIED>1</USERMODIFIED>
        <POSTS>
          <POST>
            <ID><xsl:value-of select="$discussion_id"/></ID>
            <PARENT>0</PARENT>
            <USERID>1</USERID>
            <CREATED>1094748430</CREATED>
            <MODIFIED>1094748430</MODIFIED>
            <MAILED>1</MAILED>
            <SUBJECT><xsl:value-of select="//ANNOUNCEMENT/TITLE/@value"/></SUBJECT>
            <MESSAGE><xsl:value-of select="//ANNOUNCEMENT/DESCRIPTION/TEXT"/></MESSAGE>
            <FORMAT>1</FORMAT>
            <ATTACHMENT></ATTACHMENT>
            <TOTALSCORE>0</TOTALSCORE>
          </POST>
        </POSTS>
      </DISCUSSION>

          <!--
          <xsl:call-template name="MSG">
            <xsl:with-param name="parent" select="0"/>
            <xsl:with-param name="post_id">
            <xsl:value-of select="$mod_number"/>0<xsl:value-of select="$discussion_id"/>0<xsl:value-of select="position()"/>
            </xsl:with-param>
          </xsl:call-template>
          -->
</xsl:template>


</xsl:stylesheet>

