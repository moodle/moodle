{if $courselevelexport}<?xml version="1.0" encoding="UTF-8"?>{/if}
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ./imsqti_item_v2p0.xsd" identifier="{$assessmentitemidentifier}" title="{$assessmentitemtitle}" adaptive="false" timeDependent="false">
	{section name=aid loop=$answers}
	{if $answers[aid].answertype == 3}
    	<responseDeclaration identifier="{$questionid}{$answers[aid].id}" cardinality="single" baseType="identifier">
    		<correctResponse>
    	    {section name=subanswer loop=$answers[aid].subanswers}
    	    {if $answers[aid].subanswers[subanswer].fraction > 0}
    			<value>{$answers[aid].subanswers[subanswer].id}</value>
    		{/if}
    		{/section}
    		</correctResponse>
    		<mapping defaultValue="0">
    	    {section name=subanswer loop=$answers[aid].subanswers}
    	    {if $answers[aid].subanswers[subanswer].fraction != 0}
    			<mapEntry mapKey="{$answers[aid].subanswers[subanswer].id}" mappedValue="{$answers[aid].subanswers[subanswer].fraction}"/>
    		{/if}
    		{/section}
    		</mapping>
    	</responseDeclaration>
    {elseif $answers[aid].answertype == 1}
    	<responseDeclaration identifier="{$questionid}{$answers[aid].id}" cardinality="single" baseType="string">
    		<correctResponse>
    		{section name=subanswer loop=$answers[aid].subanswers}
    		{if $answers[aid].subanswers[subanswer].fraction > 0}
    			<value>{$answers[aid].subanswers[subanswer].answer}</value>
    		{/if}
    		{/section}
    		</correctResponse>
    		<mapping lowerBound="0" upperBound="1" defaultValue="0">
    		{section name=subanswer loop=$answers[aid].subanswers}
    		    {if $answers[aid].subanswers[subanswer].fraction != 0}
    			<mapEntry mapKey="{$answers[aid].subanswers[subanswer].answer}" mappedValue="{$answers[aid].subanswers[subanswer].fraction}" />
    			{/if}
    		{/section}
    		</mapping>
    	</responseDeclaration>
	{/if}
	{/section}
	<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float"/>
	<itemBody>
	{if $questionText != ''}
	    <div class="assesmentItemBody">
    		<p>{$questionText}</p>
		</div>
	{/if}
	<div class="interactive.cloze"><p>
	{section name=qid loop=$questions.question}
	    {$questions.text[qid]}
	    {if $questions.question[qid].id != $cloze_trailing_text_id}
    		{if $questions.question[qid].answertype == 3}
		        <inlineChoiceInteraction responseIdentifier="{$questionid}{$questions.question[qid].id}" shuffle="false">
                    {section name=aid loop=$questions.question[qid].subanswers}
					   <inlineChoice identifier="{$questions.question[qid].subanswers[aid].id}">{$questions.question[qid].subanswers[aid].answer}</inlineChoice>
					{/section}
				</inlineChoiceInteraction>
        	{elseif $questions.question[qid].answertype == 1}
                <textEntryInteraction responseIdentifier="{$questionid}{$questions.question[qid].id}" expectedLength="15"/>
           	{/if}
    	{/if}
	{/section}</p></div>
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
{section name=answer loop=$answers}{if $answers[answer].answertype == 1 || $answers[answer].answertype == 3}
		<responseCondition>
			<responseIf>
				<isNull>
					<variable identifier="{$questionid}{$answers[answer].id}"/>
				</isNull>
				<setOutcomeValue identifier="SCORE{$questionid}{$answers[answer].id}">
					<baseValue baseType="float">0</baseValue>
				</setOutcomeValue>
			</responseIf>
			<responseElse>
				<setOutcomeValue identifier="SCORE{$questionid}{$answers[answer].id}">
					<mapResponse identifier="{$questionid}{$answers[answer].id}"/>
				</setOutcomeValue>
			</responseElse>
		</responseCondition>
        <setOutcomeValue identifier="FEEDBACK">
            <variable identifier="{$questionid}{$answers[answer].id}"/>
        </setOutcomeValue>		
{/if}{/section}
	</responseProcessing>
{section name=answer loop=$answers}{if $answers[answer].answertype == 1 || $answers[answer].answertype == 3}
	   {section name=subanswer loop=$answers[answer].subanswers}
       {if $answers[answer].subanswers[subanswer].feedback != ''}
	<modalFeedback outcomeIdentifier="FEEDBACK" identifier="{$answers[answer].subanswers[subanswer].id}" showHide="show">{$answers[answer].subanswers[subanswer].feedback}</modalFeedback>
{/if}{/section}
    {/if}   
	{/section}
</assessmentItem>
