{if $courselevelexport}<?xml version="1.0" encoding="UTF-8"?>{/if}
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ./imsqti_item_v2p0.xsd" identifier="{$assessmentitemidentifier}" title="{$assessmentitemtitle}" adaptive="false" timeDependent="false">
	<responseDeclaration identifier="{$questionid}" cardinality="single" baseType="float">
		<correctResponse>
			<value>{$answer->answer}</value>
		</correctResponse>
		<mapping defaultValue="0">
			<mapEntry mapKey="{$answer->answer}" mappedValue="{$answer->fraction}" />
		</mapping>
	</responseDeclaration>
	<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float"/>
	<itemBody>
		<p>{$questionText}</p>
		<div class="interactive.textEntry">
            <textEntryInteraction responseIdentifier="{$questionid}" expectedLength="10"/>
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
				<and>
				<not>
					<isNull>
						<variable identifier="{$questionid}" />
					</isNull>
				</not>
				<gte>
					<baseValue baseType="float">{$lowerbound}</baseValue>
					<variable identifier="{$questionid}" />
				</gte>
				<lte>
					<baseValue baseType="float">{$upperbound}</baseValue>
					<variable identifier="{$questionid}" />
				</lte>
				</and>
				<setOutcomeValue identifier="SCORE">
					<baseValue baseType="integer">1</baseValue>
				</setOutcomeValue>
			</responseIf>
			<responseElse>
				<setOutcomeValue identifier="SCORE">
					<baseValue baseType="integer">0</baseValue>
				</setOutcomeValue>
			</responseElse>
		</responseCondition>
        <setOutcomeValue identifier="FEEDBACK">
            <variable identifier="{$questionid}"/>
        </setOutcomeValue>		
	</responseProcessing>
{if $answer->feedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK" identifier="{$answer->id}" showHide="show">{$answer->feedback}</modalFeedback>
{/if}
{if $answer->altfeedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK" identifier="{$answer->id}" showHide="hide">{$answer->altfeedback}</modalFeedback>
{/if}
</assessmentItem>
