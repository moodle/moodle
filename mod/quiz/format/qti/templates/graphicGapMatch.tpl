{if $courselevelexport}<?xml version="1.0" encoding="UTF-8"?>{/if}
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_item_v2p0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_item_v2p0 ../imsqti_item_v2p0.xsd" identifier="{$assessmentitemidentifier}" title="{$assessmentitemtitle}" adaptive="false" timeDependent="false">
	<responseDeclaration identifier="{$questionid}" cardinality="multiple" baseType="directedPair">
		<correctResponse>
   		{section name=item loop=$gapitems}
				<value>{$gapitems[item].id} {$gapitems[item].id}</value>
   		{/section}
		</correctResponse>
		<mapping defaultValue="1">
   		{section name=item loop=$gapitems}
				<mapEntry mapKey="{$gapitems[item].id} {$gapitems[item].id}" mappedValue="1" />
   		{/section}
		</mapping>
	</responseDeclaration>
	<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float"/>
	<itemBody>
		<div class="assesmentItemBody"><p>{$questionText}</p></div>
		<div class="interactive.graphicGapMatch">
			<graphicGapMatchInteraction responseIdentifier="{$questionid}">
				<object type="{$question->mediamimetype}" data="{$question->mediaurl}" width="{$question->mediax}" height="{$question->mediay}"/>
       		{section name=item loop=$gapitems}
				<gapImg identifier="{$gapitems[item].id}" matchMax="1">
					<object type="{$gapitems[item].mediamimetype}" data="{$gapitems[item].media}" width="{$gapitems[item].snaptowidth}" height="{$gapitems[item].snaptoheight}" label="{$gapitems[item].questiontext}"/>
				</gapImg>
			{/section}
       		{section name=item loop=$gapitems}
       		<associableHotspot identifier="{$gapitems[item].id}" matchMax="{$hotspotmaxmatch}" shape="rect" coords="{$gapitems[item].targetx},{$gapitems[item].targety},{$gapitems[item].targetrx},{$gapitems[item].targetby}"/>
			{/section}
			</graphicGapMatchInteraction>
		</div>
	</itemBody>
	<responseProcessing template="http://www.imsglobal.org/xml/imsqti_item_v2p0/rpMapResponse" templateLocation="../RPTemplates/rpMapResponse.xml"/>
</assessmentItem>