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

class EnterpriseTopazSidekickPeopleAnswerRelatedPeopleAnswerCard extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const RELATION_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Direct reports.
   */
  public const RELATION_TYPE_DIRECT_REPORTS = 'DIRECT_REPORTS';
  /**
   * The manager.
   */
  public const RELATION_TYPE_MANAGER = 'MANAGER';
  /**
   * The teammates/peers of the subject.
   */
  public const RELATION_TYPE_PEERS = 'PEERS';
  /**
   * Unknown.
   */
  public const RESPONSE_STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Success.
   */
  public const RESPONSE_STATUS_SUCCESS = 'SUCCESS';
  /**
   * No such person was found in the user's domain.
   */
  public const RESPONSE_STATUS_MISSING_PERSON = 'MISSING_PERSON';
  /**
   * A person was found to match the query, but an answer could not be obtained.
   */
  public const RESPONSE_STATUS_MISSING_DATA = 'MISSING_DATA';
  protected $collection_key = 'relatedPeople';
  protected $disambiguationInfoType = EnterpriseTopazSidekickPeopleAnswerDisambiguationInfo::class;
  protected $disambiguationInfoDataType = '';
  protected $headerType = EnterpriseTopazSidekickPeopleAnswerPeopleAnswerCardHeader::class;
  protected $headerDataType = '';
  protected $relatedPeopleType = EnterpriseTopazSidekickCommonPerson::class;
  protected $relatedPeopleDataType = 'array';
  /**
   * Defines the type of relation the list of people have with the subject of
   * the card.
   *
   * @var string
   */
  public $relationType;
  /**
   * The response status.
   *
   * @var string
   */
  public $responseStatus;
  /**
   * Localized user friendly message to display to the user in the case of
   * missing data or an error.
   *
   * @var string
   */
  public $statusMessage;
  protected $subjectType = EnterpriseTopazSidekickCommonPerson::class;
  protected $subjectDataType = '';

  /**
   * Disambiguation information.
   *
   * @param EnterpriseTopazSidekickPeopleAnswerDisambiguationInfo $disambiguationInfo
   */
  public function setDisambiguationInfo(EnterpriseTopazSidekickPeopleAnswerDisambiguationInfo $disambiguationInfo)
  {
    $this->disambiguationInfo = $disambiguationInfo;
  }
  /**
   * @return EnterpriseTopazSidekickPeopleAnswerDisambiguationInfo
   */
  public function getDisambiguationInfo()
  {
    return $this->disambiguationInfo;
  }
  /**
   * The header to display for the card.
   *
   * @param EnterpriseTopazSidekickPeopleAnswerPeopleAnswerCardHeader $header
   */
  public function setHeader(EnterpriseTopazSidekickPeopleAnswerPeopleAnswerCardHeader $header)
  {
    $this->header = $header;
  }
  /**
   * @return EnterpriseTopazSidekickPeopleAnswerPeopleAnswerCardHeader
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * A list of people that are related to the query subject.
   *
   * @param EnterpriseTopazSidekickCommonPerson[] $relatedPeople
   */
  public function setRelatedPeople($relatedPeople)
  {
    $this->relatedPeople = $relatedPeople;
  }
  /**
   * @return EnterpriseTopazSidekickCommonPerson[]
   */
  public function getRelatedPeople()
  {
    return $this->relatedPeople;
  }
  /**
   * Defines the type of relation the list of people have with the subject of
   * the card.
   *
   * Accepted values: UNKNOWN, DIRECT_REPORTS, MANAGER, PEERS
   *
   * @param self::RELATION_TYPE_* $relationType
   */
  public function setRelationType($relationType)
  {
    $this->relationType = $relationType;
  }
  /**
   * @return self::RELATION_TYPE_*
   */
  public function getRelationType()
  {
    return $this->relationType;
  }
  /**
   * The response status.
   *
   * Accepted values: UNKNOWN, SUCCESS, MISSING_PERSON, MISSING_DATA
   *
   * @param self::RESPONSE_STATUS_* $responseStatus
   */
  public function setResponseStatus($responseStatus)
  {
    $this->responseStatus = $responseStatus;
  }
  /**
   * @return self::RESPONSE_STATUS_*
   */
  public function getResponseStatus()
  {
    return $this->responseStatus;
  }
  /**
   * Localized user friendly message to display to the user in the case of
   * missing data or an error.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * The profile of the person that was the subject of the query.
   *
   * @param EnterpriseTopazSidekickCommonPerson $subject
   */
  public function setSubject(EnterpriseTopazSidekickCommonPerson $subject)
  {
    $this->subject = $subject;
  }
  /**
   * @return EnterpriseTopazSidekickCommonPerson
   */
  public function getSubject()
  {
    return $this->subject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickPeopleAnswerRelatedPeopleAnswerCard::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickPeopleAnswerRelatedPeopleAnswerCard');
