{if $courselevelexport}<?xml version="1.0" encoding="UTF-8"?>{/if}
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ./imsqti_item_v2p0.xsd" identifier="{$assessmentitemidentifier}" title="{$assessmentitemtitle}" adaptive="false" timeDependent="false">
	<responseDeclaration identifier="{$questionid}" cardinality="{$responsedeclarationcardinality}" baseType="identifier">
		<correctResponse>
		{section name=answer loop=$correctresponses}
			<value>{$correctresponses[answer].id}</value>
		{/section}
		</correctResponse>
		<mapping lowerBound="0" upperBound="1" defaultValue="{$defaultvalue}">
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
				<simpleChoice identifier="{$answers[answer].id}" fixed="false"><p>{$answers[answer].choice}
    			{if $answers[answer].media != ''}
    				<object type="{$answers[answer].mediamimetype}" data="{$answers[answer].media}" width="{$answers[answer].mediax}" height="{$answers[answer].mediay}" />
    			{/if}</p>
    			{if $answers[answer].feedback != ''}
	   			    <feedbackInline identifier="{$answers[answer].id}" outcomeIdentifier="FEEDBACK" showHide="show">{$answers[answer].feedback}</feedbackInline>
                {/if}
    			{if $answers[answer].altfeedback != ''}
	   			    <feedbackInline identifier="{$answers[answer].id}" outcomeIdentifier="ALTFEEDBACK" showHide="hide">{$answers[answer].altfeedback}</feedbackInline>
                {/if}
				</simpleChoice>
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
		<responseCondition>
			<responseIf>
				<gte>
					<variable identifier="SCORE"/>
					<baseValue baseType="float">{$question->feedbackfraction}</baseValue>
				</gte>
				<setOutcomeValue identifier="FEEDBACK">
					<variable identifier="feedbackok"/>
				</setOutcomeValue>
			</responseIf>
			<responseElse>
				<setOutcomeValue identifier="FEEDBACK">
					<variable identifier="feedbackmissed"/>
				</setOutcomeValue>
			</responseElse>
		</responseCondition>
	</responseProcessing>
{if $question->feedbackok != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK" identifier="feedbackok" showHide="show">{$question->feedbackok}</modalFeedback>
{/if}
{if $question->feedbackmissed != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK" identifier="feedbackmissed" showHide="hide">{$question->feedbackmissed}</modalFeedback>
{/if}
</assessmentItem>
