{if $courselevelexport}<?xml version="1.0" encoding="UTF-8"?>{/if}
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ./imsqti_item_v2p0.xsd" identifier="{$assessmentitemidentifier}" title="{$assessmentitemtitle}" adaptive="false" timeDependent="false">
	<responseDeclaration identifier="{$questionid}" cardinality="single" baseType="identifier">
		<correctResponse>
			<value>{$correctresponse.id}</value>
		</correctResponse>
		<mapping defaultValue="0">
			<mapEntry mapKey="{$correctresponse.id}" mappedValue="{$correctresponse.fraction}"/>
		</mapping>

	</responseDeclaration>
	<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">
		<defaultValue>
			<value>0</value>
		</defaultValue>
	</outcomeDeclaration>
	<itemBody>
		<p>{$questionText}</p>
		<div class="intreactive.choiceSimple">
			<choiceInteraction responseIdentifier="{$questionid}" shuffle="false" maxChoices="1">
    		{section name=answer loop=$answers}
				<simpleChoice identifier="{$answers[answer].id}">{$answers[answer].answer}                
				{if $answers[answer].feedback != ''}
    				{if $answers[answer].answer != $correctresponse.answer}
	   			    <feedbackInline identifier="{$answers[answer].id}" outcomeIdentifier="FEEDBACK" showHide="hide">{$answers[answer].feedback}</feedbackInline>
                    {/if}
                {/if}
				</simpleChoice>
    		{/section}
			</choiceInteraction>
	{if $question_has_image == 1}
            <div class="media">
	    {if $hassize == 1}
			 <object type="{$question->mediamimetype}" data="{$question->mediaurl}" width="{$question->mediax}" height="{$question->mediay}" />
		{else}
			 <object type="{$question->mediamimetype}" data="{$question->mediaurl}" />     
		{/if}
            </div>
	{/if}
		</div>
	</itemBody>
	<responseProcessing xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ../imsqti_item_v2p0.xsd">
		<responseCondition>
			<responseIf>

				<match>
					<variable identifier="{$questionid}"/>
					<correct identifier="{$questionid}"/>
				</match>
				<setOutcomeValue identifier="SCORE">
					<baseValue baseType="float">1</baseValue>
				</setOutcomeValue>
			</responseIf>

			<responseElse>
				<setOutcomeValue identifier="SCORE">
					<baseValue baseType="float">0</baseValue>
				</setOutcomeValue>
			</responseElse>
		</responseCondition>
        <setOutcomeValue identifier="FEEDBACK">
            <variable identifier="{$questionid}"/>
        </setOutcomeValue>		
	</responseProcessing>
	{section name=answer loop=$answers}
        {if $answers[answer].feedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK" identifier="{$answers[answer].id}" showHide="hide">{$answers[answer].feedback}</modalFeedback>
    	{/if}
	{/section}
</assessmentItem>
