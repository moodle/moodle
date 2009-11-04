{if $courselevelexport}<?xml version="1.0" encoding="UTF-8"?>{/if}
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p0"
				xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
				xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p0 imsqti_v2p0.xsd"
				identifier="{$assessmentitemidentifier}" title="{$assessmentitemtitle}" adaptive="false" timeDependent="false">
	<responseDeclaration identifier="{$questionid}" cardinality="{$responsedeclarationcardinality}" baseType="identifier">
		<correctResponse>
		{section name=answer loop=$correctresponses}
			<value>{$correctresponses[answer].id}</value>
		{/section}
		</correctResponse>
	</responseDeclaration>
	<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">
		<defaultValue>
			<value>0</value>
		</defaultValue>
	</outcomeDeclaration>
	<outcomeDeclaration identifier="FEEDBACK" cardinality="{$responsedeclarationcardinality}" baseType="identifier"/>
	<outcomeDeclaration identifier="FEEDBACK2" cardinality="single" baseType="identifier"/>
	<itemBody>
	   <div class="assesmentItemBody">
		<p>{$questionText}</p>
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
		<div class="interactive.choiceMultiple">
			<choiceInteraction responseIdentifier="{$questionid}" shuffle="{$shuffle}" maxChoices="{$maxChoices}">
    		{section name=answer loop=$answers}
				<simpleChoice identifier="i{$answers[answer].id}">{$answers[answer].answer}
				{if $answers[answer].feedback != ''}
    				{if $answers[answer].answer != $correctresponse.answer}
	   			    <feedbackInline identifier="i{$answers[answer].id}" outcomeIdentifier="FEEDBACK" showHide="show">{$answers[answer].feedback}</feedbackInline>
                    {/if}
                {/if}
				</simpleChoice>
    		{/section}
			</choiceInteraction>
		</div>
	</itemBody>
	<responseProcessing>
		{section name=answer loop=$answers}
		<responseCondition>
			<responseIf>
				<{$operator}>
					<baseValue baseType="identifier">i{$answers[answer].id}</baseValue>
					<variable identifier="{$questionid}"/>
				</{$operator}>
				<setOutcomeValue identifier="SCORE">
					<sum>
						<variable identifier="SCORE"/>
						<baseValue baseType="float">{$answers[answer].fraction}</baseValue>
					</sum>
				</setOutcomeValue>
			</responseIf>
		</responseCondition>
		{/section}
		<responseCondition>
			<responseIf>
				<lte>
					<variable identifier="SCORE"/>
					<baseValue baseType="float">0</baseValue>
				</lte>
				<setOutcomeValue identifier="SCORE">
					<baseValue baseType="float">0</baseValue>
				</setOutcomeValue>
				<setOutcomeValue identifier="FEEDBACK2">
					<baseValue baseType="identifier">INCORRECT</baseValue>
				</setOutcomeValue>
			</responseIf>
			<responseElseIf>
				<gte>
					<variable identifier="SCORE"/>
					<baseValue baseType="float">0.99</baseValue>
				</gte>
				<setOutcomeValue identifier="SCORE">
					<baseValue baseType="float">1</baseValue>
				</setOutcomeValue>
				<setOutcomeValue identifier="FEEDBACK2">
					<baseValue baseType="identifier">CORRECT</baseValue>
				</setOutcomeValue>
			</responseElseIf>
			<responseElse>
				<setOutcomeValue identifier="FEEDBACK2">
					<baseValue baseType="identifier">PARTIAL</baseValue>
				</setOutcomeValue>
			</responseElse>
		</responseCondition>
        <setOutcomeValue identifier="FEEDBACK">
            <variable identifier="{$questionid}"/>
        </setOutcomeValue>		
	</responseProcessing>
    {if $correctfeedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK2" identifier="CORRECT" showHide="show">{$correctfeedback}</modalFeedback>
 	{/if}
    {if $partiallycorrectfeedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK2" identifier="PARTIAL" showHide="show">{$partiallycorrectfeedback}</modalFeedback>
 	{/if}
    {if $incorrectfeedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK2" identifier="INCORRECT" showHide="show">{$incorrectfeedback}</modalFeedback>
 	{/if}
    {if $generalfeedback != ''}
	<modalFeedback outcomeIdentifier="completionStatus" identifier="not_attempted" showHide="hide">{$generalfeedback}</modalFeedback>
 	{/if}
</assessmentItem>
