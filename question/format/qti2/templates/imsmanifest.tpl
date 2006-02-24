<?xml version="1.0" encoding="UTF-8"?>
<manifest xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_v1p2" xmlns:imsqti="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" identifier="{$manifestidentifier}" xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd   http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p2.xsd  http://www.imsglobal.org/xsd/imsqti_item_v2p0 ./imsqti_item_v2p0.xsd">
	<metadata>
		<schema>ADL SCORM</schema>
		<schemaversion>1.2</schemaversion>
		<lom xmlns="http://www.imsglobal.org/xsd/imsmd_v1p2" 
     			xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
     			xsi:schemaLocation="http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p2.xsd">
     		<general>
				<title><langstring xml:lang="{$lang}">{$quiztitle}</langstring></title>
				<description><langstring xml:lang="{$lang}">{$quizinfo}</langstring></description>
				<keyword><langstring xml:lang="{$lang}">{$quizkeywords}</langstring></keyword>
			</general>
		</lom>
   		{if $quiz_level_export == 1}
    		<imsqti:var id="submiturl">{$submiturl}</imsqti:var>
    		<imsqti:var id="userid">{$userid}</imsqti:var>
    		<imsqti:var id="username">{$username}</imsqti:var>
    		<imsqti:var id="id">{$quiz->id}</imsqti:var>
    		<imsqti:var id="course">{$quiz->course}</imsqti:var>
    		<imsqti:var id="timeopen">{$quiztimeopen}</imsqti:var>
    		<imsqti:var id="timeclose">{$quiztimeclose}</imsqti:var>
    		<imsqti:var id="timelimit">{$quiz->timelimit}</imsqti:var>
    		<imsqti:var id="shufflequestions">{$quiz->shufflequestions}</imsqti:var>
    		<imsqti:var id="shuffleanswers">{$quiz->shuffleanswers}</imsqti:var>
    		<imsqti:var id="attempts">{$quiz->attempts}</imsqti:var>
    		<imsqti:var id="attemptbuildsonlast">{$quiz->attemptonlast}</imsqti:var>
    		<imsqti:var id="grademethod">{$grademethod}</imsqti:var>
    		<imsqti:var id="feedback">{$quiz->feedback}</imsqti:var>
    		<imsqti:var id="feedbackcorrectanswers">{$quiz->correctanswers}</imsqti:var>
    		<imsqti:var id="maxgrade">{$quiz->grade}</imsqti:var>
    		<imsqti:var id="rawpointspossible">{$quiz->sumgrades}</imsqti:var>
    		<imsqti:var id="password">{$quiz->password}</imsqti:var>
    		<imsqti:var id="subnet">{$quiz->subnet}</imsqti:var>
    		<imsqti:var id="coursefullname">{$course->fullname}</imsqti:var>
    		<imsqti:var id="courseshortname">{$course->shortname}</imsqti:var>
		{/if}
	</metadata>
	<organizations/>
	<resources>
    	{section name=question loop=$questions}
		<resource identifier="category{$questions[question].category}-question{$questions[question].id}" type="imsqti_item_xmlv2p0" {if $externalfiles == 1}href="./category{$questions[question].category}-question{$questions[question].id}.xml"{/if}>
			<metadata>
				<schema>IMS QTI Item</schema>
				<schemaversion>2.0</schemaversion>
				<imsmd:lom>
					<imsmd:general>
						<imsmd:identifier>category{$questions[question].category}-question{$questions[question].id}</imsmd:identifier>
						<imsmd:title>
							<imsmd:langstring xml:lang="{$lang}">{$questions[question].name}</imsmd:langstring>
						</imsmd:title>
						<imsmd:description>
							<imsmd:langstring xml:lang="en">Question {$questions[question].id} from category {$questions[question].category}</imsmd:langstring>
						</imsmd:description>
					</imsmd:general>
					<imsmd:lifecycle>
						<imsmd:version>
							<imsmd:langstring xml:lang="en">1.0</imsmd:langstring>
						</imsmd:version>
						<imsmd:status>
							<imsmd:source>
								<imsmd:langstring xml:lang="en">LOMv1.0</imsmd:langstring>
							</imsmd:source>
							<imsmd:value>
								<imsmd:langstring xml:lang="en">Draft</imsmd:langstring>
							</imsmd:value>
						</imsmd:status>
					</imsmd:lifecycle>
				</imsmd:lom>
				<imsqti:qtiMetadata>
					<imsqti:timeDependent>false</imsqti:timeDependent>
					<imsqti:interactionType>{$questions[question].qtiinteractiontype}</imsqti:interactionType>
					<imsqti:canComputerScore>{$questions[question].qtiscoreable}</imsqti:canComputerScore>
					<imsqti:feedbackType>nonadaptive</imsqti:feedbackType>
					<imsqti:solutionAvailable>{$questions[question].qtisolutionavailable}</imsqti:solutionAvailable>
				</imsqti:qtiMetadata>
			</metadata>
			{if $questions[question].image != ''}
			<file href="{$questions[question].mediaurl}" />
			{/if}
			{$questions[question].exporttext}
		</resource>
		{/section}
	</resources>
</manifest>