{if $courselevelexport}<?xml version="1.0" encoding="UTF-8"?>{/if}
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ./imsqti_item_v2p0.xsd" identifier="{$assessmentitemidentifier}" title="{$assessmentitemtitle}" adaptive="false" timeDependent="false">
	<responseDeclaration identifier="{$questionid}" cardinality="{$responsedeclarationcardinality}" baseType="identifier">
		<correctResponse>
		{section name=answer loop=$correctresponses}
			<value>{$correctresponses[answer].id}</value>
		{/section}
		</correctResponse>
		<mapping lowerBound="0" upperBound="1" defaultValue="-1">
		{section name=answer loop=$answers}
		    {if $answers[answer].fraction != 0}
			<mapEntry mapKey="{$answers[answer].id}" mappedValue="{$answers[answer].fraction}" />
			{/if}
		{/section}
		</mapping>
	</responseDeclaration>
	<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" />
	<itemBody>
	   <div class="assesmentItemBody">
		<p>{$questionText}</p>
       </div>
		<div class="interactive.choiceMultiple">
			<choiceInteraction responseIdentifier="{$questionid}" shuffle="false" maxChoices="{$maxChoices}">
    		{section name=answer loop=$answers}
				<simpleChoice identifier="{$answers[answer].id}" fixed="false">{$answers[answer].answer}</simpleChoice>
    		{/section}
			</choiceInteraction>
		</div>
	{if $question_has_image == 1}
		<div class="media">
	    {if $hassize == 1}
			<object type="{$question->mediamimetype}" data="{$question->mediaurl}" width="{$question->mediax}" height="{$question->mediay}" />
		{else}
			<object type="{$question->mediamimetype}" data="{$question->mediaurl}" />     
		{/if}
		</div>
	{/if}
	</itemBody>
	<responseProcessing xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ../imsqti_item_v2p0.xsd">
		<responseCondition>
			<responseIf>
				<isNull>
					<variable identifier="{$questionid}"/>
				</isNull>
				<setOutcomeValue identifier="SCORE">
					<baseValue baseType="float">0</baseValue>
				</setOutcomeValue>
			</responseIf>
			<responseElse>
				<setOutcomeValue identifier="SCORE">
					<mapResponse identifier="{$questionid}"/>
				</setOutcomeValue>
			</responseElse>
		</responseCondition>
        <setOutcomeValue identifier="FEEDBACK">
            <variable identifier="{$questionid}"/>
        </setOutcomeValue>		
	</responseProcessing>
	{section name=answer loop=$answers}
        {if $answers[answer].feedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK" identifier="{$answers[answer].id}" showHide="show">{$answers[answer].feedback}</modalFeedback>
    	{/if}
	{/section}
</assessmentItem>
