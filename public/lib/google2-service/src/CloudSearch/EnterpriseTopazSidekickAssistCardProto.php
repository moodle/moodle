<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickAssistCardProto extends \Google\Model
{
  /**
   * The default type, an unknown card type.
   */
  public const CARD_TYPE_UNKNOWN_TYPE = 'UNKNOWN_TYPE';
  /**
   * The user's agenda for the day.
   *
   * @deprecated
   */
  public const CARD_TYPE_AGENDA = 'AGENDA';
  /**
   * Changelists.
   *
   * @deprecated
   */
  public const CARD_TYPE_CHANGELISTS = 'CHANGELISTS';
  /**
   * Any group of meetings for the day that are overlapping.
   *
   * @deprecated
   */
  public const CARD_TYPE_CONFLICTING_MEETINGS = 'CONFLICTING_MEETINGS';
  /**
   * Create notes for a meeting specified in the request.
   *
   * @deprecated
   */
  public const CARD_TYPE_CREATE_NOTES_FOR_MEETING = 'CREATE_NOTES_FOR_MEETING';
  /**
   * Create notes for meeting query.
   *
   * @deprecated
   */
  public const CARD_TYPE_CREATE_NOTES_FOR_MEETING_REQUEST = 'CREATE_NOTES_FOR_MEETING_REQUEST';
  /**
   * News about your SFDC customers.
   *
   * @deprecated
   */
  public const CARD_TYPE_CUSTOMER_NEWS = 'CUSTOMER_NEWS';
  /**
   * Find a time for two people to meet.
   */
  public const CARD_TYPE_FIND_MEETING_TIME = 'FIND_MEETING_TIME';
  /**
   * The user's next non-declined meeting.
   *
   * @deprecated
   */
  public const CARD_TYPE_NEXT_MEETING = 'NEXT_MEETING';
  /**
   * Important documents for you since you have viewed them in the last month
   * and there's some action going on.
   *
   * @deprecated
   */
  public const CARD_TYPE_PERSONALIZED_DOCS = 'PERSONALIZED_DOCS';
  /**
   * Documents that are trending in your company. A TRENDING_DOCS card can be of
   * two types: TRENDING_IN_COLLABORATORS (i.e., Stay in the Loop) and
   * TRENDING_IN_DOMAIN (i.e., Popular Content). Use
   * DOMAIN_TRENDING_DOCS/TEAM_TRENDING_DOCS instead.
   *
   * @deprecated
   */
  public const CARD_TYPE_TRENDING_DOCS = 'TRENDING_DOCS';
  /**
   * An upcoming trip with all trip information along with calendar events in
   * the destination timezone.
   *
   * @deprecated
   */
  public const CARD_TYPE_UPCOMING_TRIP = 'UPCOMING_TRIP';
  /**
   * The Morning/Evening Summary Card for the next working day.
   *
   * @deprecated
   */
  public const CARD_TYPE_SUMMARY = 'SUMMARY';
  /**
   * A meeting. When requesting meetings, the service will return a MEETING card
   * for each meeting the user has in his agenda.
   *
   * @deprecated
   */
  public const CARD_TYPE_MEETINGS = 'MEETINGS';
  /**
   * All cards related to the homepage (agenda, conflicting-meetings, summary,
   * etc...). This type should no longer be used. Use HOMEPAGE_V3 instead.
   *
   * @deprecated
   */
  public const CARD_TYPE_HOMEPAGE = 'HOMEPAGE';
  /**
   * A card to with specifics to share meeting documents with all attendees.
   *
   * @deprecated
   */
  public const CARD_TYPE_SHARE_MEETING_DOCS = 'SHARE_MEETING_DOCS';
  /**
   * Represents a set of users that the requesting user has low affinity with.
   *
   * @deprecated
   */
  public const CARD_TYPE_DISCOVER_PEOPLE = 'DISCOVER_PEOPLE';
  /**
   * All cards related to the homepage-v3 (agenda-group, WIP, etc...)
   *
   * @deprecated
   */
  public const CARD_TYPE_HOMEPAGE_V3 = 'HOMEPAGE_V3';
  /**
   * A group of agenda-events (meeting, conflicts)
   */
  public const CARD_TYPE_AGENDA_GROUP = 'AGENDA_GROUP';
  /**
   * The documents that you were recently working on.
   */
  public const CARD_TYPE_WORK_IN_PROGRESS = 'WORK_IN_PROGRESS';
  /**
   * (v3) The "get and keep ahead" card for today and tomorrow
   */
  public const CARD_TYPE_GET_AND_KEEP_AHEAD = 'GET_AND_KEEP_AHEAD';
  /**
   * Generic answer card.
   */
  public const CARD_TYPE_GENERIC_ANSWER_CARD = 'GENERIC_ANSWER_CARD';
  /**
   * Third party answer card.
   */
  public const CARD_TYPE_THIRD_PARTY_ANSWER_CARD = 'THIRD_PARTY_ANSWER_CARD';
  /**
   * Documents that are trending in your company/domain (i.e., Popular Content).
   *
   * @deprecated
   */
  public const CARD_TYPE_DOMAIN_TRENDING_DOCS = 'DOMAIN_TRENDING_DOCS';
  /**
   * Documents that are trending in your team (i.e., Stay in the Loop).
   *
   * @deprecated
   */
  public const CARD_TYPE_TEAM_TRENDING_DOCS = 'TEAM_TRENDING_DOCS';
  /**
   * Documents that match the user's query (e.g. sheets shared with me).
   */
  public const CARD_TYPE_DOCUMENT_LIST_ANSWER_CARD = 'DOCUMENT_LIST_ANSWER_CARD';
  /**
   * A help card that provides examples of queries the user can ask.
   */
  public const CARD_TYPE_SUGGESTED_QUERY_ANSWER_CARD = 'SUGGESTED_QUERY_ANSWER_CARD';
  /**
   * Answer card for a single person (e.g. what is x's phone number).
   */
  public const CARD_TYPE_PERSON_ANSWER_CARD = 'PERSON_ANSWER_CARD';
  /**
   * Answer card for a list of people related to the person that is the subject
   * of the query (e.g. who reports to x).
   */
  public const CARD_TYPE_RELATED_PEOPLE_ANSWER_CARD = 'RELATED_PEOPLE_ANSWER_CARD';
  /**
   * Knowledge card for a single person and their related people.
   */
  public const CARD_TYPE_PERSON_KNOWLEDGE_CARD = 'PERSON_KNOWLEDGE_CARD';
  /**
   * People Search promotion card.
   */
  public const CARD_TYPE_PEOPLE_SEARCH_PROMOTION_CARD = 'PEOPLE_SEARCH_PROMOTION_CARD';
  protected $agendaGroupCardProtoType = EnterpriseTopazSidekickAgendaGroupCardProto::class;
  protected $agendaGroupCardProtoDataType = '';
  protected $cardMetadataType = EnterpriseTopazSidekickCardMetadata::class;
  protected $cardMetadataDataType = '';
  /**
   * Card type.
   *
   * @var string
   */
  public $cardType;
  protected $conflictingMeetingsCardType = EnterpriseTopazSidekickConflictingEventsCardProto::class;
  protected $conflictingMeetingsCardDataType = '';
  protected $documentListCardType = EnterpriseTopazSidekickDocumentPerCategoryList::class;
  protected $documentListCardDataType = '';
  protected $documentsWithMentionsType = EnterpriseTopazSidekickDocumentPerCategoryList::class;
  protected $documentsWithMentionsDataType = '';
  protected $findMeetingTimeCardType = EnterpriseTopazSidekickFindMeetingTimeCardProto::class;
  protected $findMeetingTimeCardDataType = '';
  protected $genericAnswerCardType = EnterpriseTopazSidekickGenericAnswerCard::class;
  protected $genericAnswerCardDataType = '';
  protected $getAndKeepAheadCardType = EnterpriseTopazSidekickGetAndKeepAheadCardProto::class;
  protected $getAndKeepAheadCardDataType = '';
  protected $meetingType = EnterpriseTopazSidekickAgendaEntry::class;
  protected $meetingDataType = '';
  protected $meetingNotesCardType = EnterpriseTopazSidekickMeetingNotesCardProto::class;
  protected $meetingNotesCardDataType = '';
  protected $meetingNotesCardRequestType = EnterpriseTopazSidekickMeetingNotesCardRequest::class;
  protected $meetingNotesCardRequestDataType = '';
  protected $peopleDisambiguationCardType = EnterpriseTopazSidekickPeopleDisambiguationCard::class;
  protected $peopleDisambiguationCardDataType = '';
  protected $peoplePromotionCardType = PeoplePromotionCard::class;
  protected $peoplePromotionCardDataType = '';
  protected $personAnswerCardType = EnterpriseTopazSidekickPeopleAnswerPersonAnswerCard::class;
  protected $personAnswerCardDataType = '';
  protected $personProfileCardType = EnterpriseTopazSidekickPersonProfileCard::class;
  protected $personProfileCardDataType = '';
  protected $personalizedDocsCardType = EnterpriseTopazSidekickPersonalizedDocsCardProto::class;
  protected $personalizedDocsCardDataType = '';
  protected $relatedPeopleAnswerCardType = EnterpriseTopazSidekickPeopleAnswerRelatedPeopleAnswerCard::class;
  protected $relatedPeopleAnswerCardDataType = '';
  protected $shareMeetingDocsCardType = EnterpriseTopazSidekickShareMeetingDocsCardProto::class;
  protected $shareMeetingDocsCardDataType = '';
  protected $sharedDocumentsType = EnterpriseTopazSidekickDocumentPerCategoryList::class;
  protected $sharedDocumentsDataType = '';
  protected $suggestedQueryAnswerCardType = EnterpriseTopazSidekickAnswerSuggestedQueryAnswerCard::class;
  protected $suggestedQueryAnswerCardDataType = '';
  protected $thirdPartyAnswerCardType = ThirdPartyGenericCard::class;
  protected $thirdPartyAnswerCardDataType = '';
  protected $workInProgressCardProtoType = EnterpriseTopazSidekickRecentDocumentsCardProto::class;
  protected $workInProgressCardProtoDataType = '';

  /**
   * Agenda group card.
   *
   * @param EnterpriseTopazSidekickAgendaGroupCardProto $agendaGroupCardProto
   */
  public function setAgendaGroupCardProto(EnterpriseTopazSidekickAgendaGroupCardProto $agendaGroupCardProto)
  {
    $this->agendaGroupCardProto = $agendaGroupCardProto;
  }
  /**
   * @return EnterpriseTopazSidekickAgendaGroupCardProto
   */
  public function getAgendaGroupCardProto()
  {
    return $this->agendaGroupCardProto;
  }
  /**
   * Card metadata such as chronology and render mode of the card.
   *
   * @param EnterpriseTopazSidekickCardMetadata $cardMetadata
   */
  public function setCardMetadata(EnterpriseTopazSidekickCardMetadata $cardMetadata)
  {
    $this->cardMetadata = $cardMetadata;
  }
  /**
   * @return EnterpriseTopazSidekickCardMetadata
   */
  public function getCardMetadata()
  {
    return $this->cardMetadata;
  }
  /**
   * Card type.
   *
   * Accepted values: UNKNOWN_TYPE, AGENDA, CHANGELISTS, CONFLICTING_MEETINGS,
   * CREATE_NOTES_FOR_MEETING, CREATE_NOTES_FOR_MEETING_REQUEST, CUSTOMER_NEWS,
   * FIND_MEETING_TIME, NEXT_MEETING, PERSONALIZED_DOCS, TRENDING_DOCS,
   * UPCOMING_TRIP, SUMMARY, MEETINGS, HOMEPAGE, SHARE_MEETING_DOCS,
   * DISCOVER_PEOPLE, HOMEPAGE_V3, AGENDA_GROUP, WORK_IN_PROGRESS,
   * GET_AND_KEEP_AHEAD, GENERIC_ANSWER_CARD, THIRD_PARTY_ANSWER_CARD,
   * DOMAIN_TRENDING_DOCS, TEAM_TRENDING_DOCS, DOCUMENT_LIST_ANSWER_CARD,
   * SUGGESTED_QUERY_ANSWER_CARD, PERSON_ANSWER_CARD,
   * RELATED_PEOPLE_ANSWER_CARD, PERSON_KNOWLEDGE_CARD,
   * PEOPLE_SEARCH_PROMOTION_CARD
   *
   * @param self::CARD_TYPE_* $cardType
   */
  public function setCardType($cardType)
  {
    $this->cardType = $cardType;
  }
  /**
   * @return self::CARD_TYPE_*
   */
  public function getCardType()
  {
    return $this->cardType;
  }
  /**
   * Conflicting events card.
   *
   * @deprecated
   * @param EnterpriseTopazSidekickConflictingEventsCardProto $conflictingMeetingsCard
   */
  public function setConflictingMeetingsCard(EnterpriseTopazSidekickConflictingEventsCardProto $conflictingMeetingsCard)
  {
    $this->conflictingMeetingsCard = $conflictingMeetingsCard;
  }
  /**
   * @deprecated
   * @return EnterpriseTopazSidekickConflictingEventsCardProto
   */
  public function getConflictingMeetingsCard()
  {
    return $this->conflictingMeetingsCard;
  }
  /**
   * Answer card for documents that are applicable to the current query.
   *
   * @param EnterpriseTopazSidekickDocumentPerCategoryList $documentListCard
   */
  public function setDocumentListCard(EnterpriseTopazSidekickDocumentPerCategoryList $documentListCard)
  {
    $this->documentListCard = $documentListCard;
  }
  /**
   * @return EnterpriseTopazSidekickDocumentPerCategoryList
   */
  public function getDocumentListCard()
  {
    return $this->documentListCard;
  }
  /**
   * Documents with mentions.
   *
   * @deprecated
   * @param EnterpriseTopazSidekickDocumentPerCategoryList $documentsWithMentions
   */
  public function setDocumentsWithMentions(EnterpriseTopazSidekickDocumentPerCategoryList $documentsWithMentions)
  {
    $this->documentsWithMentions = $documentsWithMentions;
  }
  /**
   * @deprecated
   * @return EnterpriseTopazSidekickDocumentPerCategoryList
   */
  public function getDocumentsWithMentions()
  {
    return $this->documentsWithMentions;
  }
  /**
   * Find meeting time card.
   *
   * @param EnterpriseTopazSidekickFindMeetingTimeCardProto $findMeetingTimeCard
   */
  public function setFindMeetingTimeCard(EnterpriseTopazSidekickFindMeetingTimeCardProto $findMeetingTimeCard)
  {
    $this->findMeetingTimeCard = $findMeetingTimeCard;
  }
  /**
   * @return EnterpriseTopazSidekickFindMeetingTimeCardProto
   */
  public function getFindMeetingTimeCard()
  {
    return $this->findMeetingTimeCard;
  }
  /**
   * Generic answer card.
   *
   * @param EnterpriseTopazSidekickGenericAnswerCard $genericAnswerCard
   */
  public function setGenericAnswerCard(EnterpriseTopazSidekickGenericAnswerCard $genericAnswerCard)
  {
    $this->genericAnswerCard = $genericAnswerCard;
  }
  /**
   * @return EnterpriseTopazSidekickGenericAnswerCard
   */
  public function getGenericAnswerCard()
  {
    return $this->genericAnswerCard;
  }
  /**
   * Get and keep ahead card.
   *
   * @param EnterpriseTopazSidekickGetAndKeepAheadCardProto $getAndKeepAheadCard
   */
  public function setGetAndKeepAheadCard(EnterpriseTopazSidekickGetAndKeepAheadCardProto $getAndKeepAheadCard)
  {
    $this->getAndKeepAheadCard = $getAndKeepAheadCard;
  }
  /**
   * @return EnterpriseTopazSidekickGetAndKeepAheadCardProto
   */
  public function getGetAndKeepAheadCard()
  {
    return $this->getAndKeepAheadCard;
  }
  /**
   * Meeting card.
   *
   * @deprecated
   * @param EnterpriseTopazSidekickAgendaEntry $meeting
   */
  public function setMeeting(EnterpriseTopazSidekickAgendaEntry $meeting)
  {
    $this->meeting = $meeting;
  }
  /**
   * @deprecated
   * @return EnterpriseTopazSidekickAgendaEntry
   */
  public function getMeeting()
  {
    return $this->meeting;
  }
  /**
   * Meeting notes card.
   *
   * @param EnterpriseTopazSidekickMeetingNotesCardProto $meetingNotesCard
   */
  public function setMeetingNotesCard(EnterpriseTopazSidekickMeetingNotesCardProto $meetingNotesCard)
  {
    $this->meetingNotesCard = $meetingNotesCard;
  }
  /**
   * @return EnterpriseTopazSidekickMeetingNotesCardProto
   */
  public function getMeetingNotesCard()
  {
    return $this->meetingNotesCard;
  }
  /**
   * Request for meeting notes card.
   *
   * @param EnterpriseTopazSidekickMeetingNotesCardRequest $meetingNotesCardRequest
   */
  public function setMeetingNotesCardRequest(EnterpriseTopazSidekickMeetingNotesCardRequest $meetingNotesCardRequest)
  {
    $this->meetingNotesCardRequest = $meetingNotesCardRequest;
  }
  /**
   * @return EnterpriseTopazSidekickMeetingNotesCardRequest
   */
  public function getMeetingNotesCardRequest()
  {
    return $this->meetingNotesCardRequest;
  }
  /**
   * The people disambiguation card.
   *
   * @param EnterpriseTopazSidekickPeopleDisambiguationCard $peopleDisambiguationCard
   */
  public function setPeopleDisambiguationCard(EnterpriseTopazSidekickPeopleDisambiguationCard $peopleDisambiguationCard)
  {
    $this->peopleDisambiguationCard = $peopleDisambiguationCard;
  }
  /**
   * @return EnterpriseTopazSidekickPeopleDisambiguationCard
   */
  public function getPeopleDisambiguationCard()
  {
    return $this->peopleDisambiguationCard;
  }
  /**
   * People Search promotion card.
   *
   * @param PeoplePromotionCard $peoplePromotionCard
   */
  public function setPeoplePromotionCard(PeoplePromotionCard $peoplePromotionCard)
  {
    $this->peoplePromotionCard = $peoplePromotionCard;
  }
  /**
   * @return PeoplePromotionCard
   */
  public function getPeoplePromotionCard()
  {
    return $this->peoplePromotionCard;
  }
  /**
   * Answer card that represents a single person.
   *
   * @param EnterpriseTopazSidekickPeopleAnswerPersonAnswerCard $personAnswerCard
   */
  public function setPersonAnswerCard(EnterpriseTopazSidekickPeopleAnswerPersonAnswerCard $personAnswerCard)
  {
    $this->personAnswerCard = $personAnswerCard;
  }
  /**
   * @return EnterpriseTopazSidekickPeopleAnswerPersonAnswerCard
   */
  public function getPersonAnswerCard()
  {
    return $this->personAnswerCard;
  }
  /**
   * Full profile card.
   *
   * @param EnterpriseTopazSidekickPersonProfileCard $personProfileCard
   */
  public function setPersonProfileCard(EnterpriseTopazSidekickPersonProfileCard $personProfileCard)
  {
    $this->personProfileCard = $personProfileCard;
  }
  /**
   * @return EnterpriseTopazSidekickPersonProfileCard
   */
  public function getPersonProfileCard()
  {
    return $this->personProfileCard;
  }
  /**
   * Card with recommended documents for the user.
   *
   * @deprecated
   * @param EnterpriseTopazSidekickPersonalizedDocsCardProto $personalizedDocsCard
   */
  public function setPersonalizedDocsCard(EnterpriseTopazSidekickPersonalizedDocsCardProto $personalizedDocsCard)
  {
    $this->personalizedDocsCard = $personalizedDocsCard;
  }
  /**
   * @deprecated
   * @return EnterpriseTopazSidekickPersonalizedDocsCardProto
   */
  public function getPersonalizedDocsCard()
  {
    return $this->personalizedDocsCard;
  }
  /**
   * Answer card that represents a list of people related to a person.
   *
   * @param EnterpriseTopazSidekickPeopleAnswerRelatedPeopleAnswerCard $relatedPeopleAnswerCard
   */
  public function setRelatedPeopleAnswerCard(EnterpriseTopazSidekickPeopleAnswerRelatedPeopleAnswerCard $relatedPeopleAnswerCard)
  {
    $this->relatedPeopleAnswerCard = $relatedPeopleAnswerCard;
  }
  /**
   * @return EnterpriseTopazSidekickPeopleAnswerRelatedPeopleAnswerCard
   */
  public function getRelatedPeopleAnswerCard()
  {
    return $this->relatedPeopleAnswerCard;
  }
  /**
   * Sahre meeting docs card.
   *
   * @deprecated
   * @param EnterpriseTopazSidekickShareMeetingDocsCardProto $shareMeetingDocsCard
   */
  public function setShareMeetingDocsCard(EnterpriseTopazSidekickShareMeetingDocsCardProto $shareMeetingDocsCard)
  {
    $this->shareMeetingDocsCard = $shareMeetingDocsCard;
  }
  /**
   * @deprecated
   * @return EnterpriseTopazSidekickShareMeetingDocsCardProto
   */
  public function getShareMeetingDocsCard()
  {
    return $this->shareMeetingDocsCard;
  }
  /**
   * Shared documents.
   *
   * @deprecated
   * @param EnterpriseTopazSidekickDocumentPerCategoryList $sharedDocuments
   */
  public function setSharedDocuments(EnterpriseTopazSidekickDocumentPerCategoryList $sharedDocuments)
  {
    $this->sharedDocuments = $sharedDocuments;
  }
  /**
   * @deprecated
   * @return EnterpriseTopazSidekickDocumentPerCategoryList
   */
  public function getSharedDocuments()
  {
    return $this->sharedDocuments;
  }
  /**
   * Answer card for what natural language queries the user can ask.
   *
   * @param EnterpriseTopazSidekickAnswerSuggestedQueryAnswerCard $suggestedQueryAnswerCard
   */
  public function setSuggestedQueryAnswerCard(EnterpriseTopazSidekickAnswerSuggestedQueryAnswerCard $suggestedQueryAnswerCard)
  {
    $this->suggestedQueryAnswerCard = $suggestedQueryAnswerCard;
  }
  /**
   * @return EnterpriseTopazSidekickAnswerSuggestedQueryAnswerCard
   */
  public function getSuggestedQueryAnswerCard()
  {
    return $this->suggestedQueryAnswerCard;
  }
  /**
   * Third party answer cards.
   *
   * @param ThirdPartyGenericCard $thirdPartyAnswerCard
   */
  public function setThirdPartyAnswerCard(ThirdPartyGenericCard $thirdPartyAnswerCard)
  {
    $this->thirdPartyAnswerCard = $thirdPartyAnswerCard;
  }
  /**
   * @return ThirdPartyGenericCard
   */
  public function getThirdPartyAnswerCard()
  {
    return $this->thirdPartyAnswerCard;
  }
  /**
   * Work In Progress card.
   *
   * @param EnterpriseTopazSidekickRecentDocumentsCardProto $workInProgressCardProto
   */
  public function setWorkInProgressCardProto(EnterpriseTopazSidekickRecentDocumentsCardProto $workInProgressCardProto)
  {
    $this->workInProgressCardProto = $workInProgressCardProto;
  }
  /**
   * @return EnterpriseTopazSidekickRecentDocumentsCardProto
   */
  public function getWorkInProgressCardProto()
  {
    return $this->workInProgressCardProto;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickAssistCardProto::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickAssistCardProto');
