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

namespace Google\Service\CivicInfo;

class CivicinfoSchemaV2AdministrativeBody extends \Google\Collection
{
  protected $collection_key = 'voter_services';
  protected $internal_gapi_mappings = [
        "voterServices" => "voter_services",
  ];
  /**
   * A URL provided by this administrative body for information on absentee
   * voting.
   *
   * @var string
   */
  public $absenteeVotingInfoUrl;
  /**
   * A URL provided by this administrative body to give contest information to
   * the voter.
   *
   * @var string
   */
  public $ballotInfoUrl;
  protected $correspondenceAddressType = CivicinfoSchemaV2SimpleAddressType::class;
  protected $correspondenceAddressDataType = '';
  /**
   * A URL provided by this administrative body for looking up general election
   * information.
   *
   * @var string
   */
  public $electionInfoUrl;
  /**
   * A last minute or emergency notification text provided by this
   * administrative body.
   *
   * @var string
   */
  public $electionNoticeText;
  /**
   * A URL provided by this administrative body for additional information
   * related to the last minute or emergency notification.
   *
   * @var string
   */
  public $electionNoticeUrl;
  protected $electionOfficialsType = CivicinfoSchemaV2ElectionOfficial::class;
  protected $electionOfficialsDataType = 'array';
  /**
   * A URL provided by this administrative body for confirming that the voter is
   * registered to vote.
   *
   * @var string
   */
  public $electionRegistrationConfirmationUrl;
  /**
   * A URL provided by this administrative body for looking up how to register
   * to vote.
   *
   * @var string
   */
  public $electionRegistrationUrl;
  /**
   * A URL provided by this administrative body describing election rules to the
   * voter.
   *
   * @var string
   */
  public $electionRulesUrl;
  /**
   * A description of the hours of operation for this administrative body.
   *
   * @var string
   */
  public $hoursOfOperation;
  /**
   * The name of this election administrative body.
   *
   * @var string
   */
  public $name;
  protected $physicalAddressType = CivicinfoSchemaV2SimpleAddressType::class;
  protected $physicalAddressDataType = '';
  /**
   * A description of the services this administrative body may provide.
   *
   * @var string[]
   */
  public $voterServices;
  /**
   * A URL provided by this administrative body for looking up where to vote.
   *
   * @var string
   */
  public $votingLocationFinderUrl;

  /**
   * A URL provided by this administrative body for information on absentee
   * voting.
   *
   * @param string $absenteeVotingInfoUrl
   */
  public function setAbsenteeVotingInfoUrl($absenteeVotingInfoUrl)
  {
    $this->absenteeVotingInfoUrl = $absenteeVotingInfoUrl;
  }
  /**
   * @return string
   */
  public function getAbsenteeVotingInfoUrl()
  {
    return $this->absenteeVotingInfoUrl;
  }
  /**
   * A URL provided by this administrative body to give contest information to
   * the voter.
   *
   * @param string $ballotInfoUrl
   */
  public function setBallotInfoUrl($ballotInfoUrl)
  {
    $this->ballotInfoUrl = $ballotInfoUrl;
  }
  /**
   * @return string
   */
  public function getBallotInfoUrl()
  {
    return $this->ballotInfoUrl;
  }
  /**
   * The mailing address of this administrative body.
   *
   * @param CivicinfoSchemaV2SimpleAddressType $correspondenceAddress
   */
  public function setCorrespondenceAddress(CivicinfoSchemaV2SimpleAddressType $correspondenceAddress)
  {
    $this->correspondenceAddress = $correspondenceAddress;
  }
  /**
   * @return CivicinfoSchemaV2SimpleAddressType
   */
  public function getCorrespondenceAddress()
  {
    return $this->correspondenceAddress;
  }
  /**
   * A URL provided by this administrative body for looking up general election
   * information.
   *
   * @param string $electionInfoUrl
   */
  public function setElectionInfoUrl($electionInfoUrl)
  {
    $this->electionInfoUrl = $electionInfoUrl;
  }
  /**
   * @return string
   */
  public function getElectionInfoUrl()
  {
    return $this->electionInfoUrl;
  }
  /**
   * A last minute or emergency notification text provided by this
   * administrative body.
   *
   * @param string $electionNoticeText
   */
  public function setElectionNoticeText($electionNoticeText)
  {
    $this->electionNoticeText = $electionNoticeText;
  }
  /**
   * @return string
   */
  public function getElectionNoticeText()
  {
    return $this->electionNoticeText;
  }
  /**
   * A URL provided by this administrative body for additional information
   * related to the last minute or emergency notification.
   *
   * @param string $electionNoticeUrl
   */
  public function setElectionNoticeUrl($electionNoticeUrl)
  {
    $this->electionNoticeUrl = $electionNoticeUrl;
  }
  /**
   * @return string
   */
  public function getElectionNoticeUrl()
  {
    return $this->electionNoticeUrl;
  }
  /**
   * The election officials for this election administrative body.
   *
   * @param CivicinfoSchemaV2ElectionOfficial[] $electionOfficials
   */
  public function setElectionOfficials($electionOfficials)
  {
    $this->electionOfficials = $electionOfficials;
  }
  /**
   * @return CivicinfoSchemaV2ElectionOfficial[]
   */
  public function getElectionOfficials()
  {
    return $this->electionOfficials;
  }
  /**
   * A URL provided by this administrative body for confirming that the voter is
   * registered to vote.
   *
   * @param string $electionRegistrationConfirmationUrl
   */
  public function setElectionRegistrationConfirmationUrl($electionRegistrationConfirmationUrl)
  {
    $this->electionRegistrationConfirmationUrl = $electionRegistrationConfirmationUrl;
  }
  /**
   * @return string
   */
  public function getElectionRegistrationConfirmationUrl()
  {
    return $this->electionRegistrationConfirmationUrl;
  }
  /**
   * A URL provided by this administrative body for looking up how to register
   * to vote.
   *
   * @param string $electionRegistrationUrl
   */
  public function setElectionRegistrationUrl($electionRegistrationUrl)
  {
    $this->electionRegistrationUrl = $electionRegistrationUrl;
  }
  /**
   * @return string
   */
  public function getElectionRegistrationUrl()
  {
    return $this->electionRegistrationUrl;
  }
  /**
   * A URL provided by this administrative body describing election rules to the
   * voter.
   *
   * @param string $electionRulesUrl
   */
  public function setElectionRulesUrl($electionRulesUrl)
  {
    $this->electionRulesUrl = $electionRulesUrl;
  }
  /**
   * @return string
   */
  public function getElectionRulesUrl()
  {
    return $this->electionRulesUrl;
  }
  /**
   * A description of the hours of operation for this administrative body.
   *
   * @param string $hoursOfOperation
   */
  public function setHoursOfOperation($hoursOfOperation)
  {
    $this->hoursOfOperation = $hoursOfOperation;
  }
  /**
   * @return string
   */
  public function getHoursOfOperation()
  {
    return $this->hoursOfOperation;
  }
  /**
   * The name of this election administrative body.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The physical address of this administrative body.
   *
   * @param CivicinfoSchemaV2SimpleAddressType $physicalAddress
   */
  public function setPhysicalAddress(CivicinfoSchemaV2SimpleAddressType $physicalAddress)
  {
    $this->physicalAddress = $physicalAddress;
  }
  /**
   * @return CivicinfoSchemaV2SimpleAddressType
   */
  public function getPhysicalAddress()
  {
    return $this->physicalAddress;
  }
  /**
   * A description of the services this administrative body may provide.
   *
   * @param string[] $voterServices
   */
  public function setVoterServices($voterServices)
  {
    $this->voterServices = $voterServices;
  }
  /**
   * @return string[]
   */
  public function getVoterServices()
  {
    return $this->voterServices;
  }
  /**
   * A URL provided by this administrative body for looking up where to vote.
   *
   * @param string $votingLocationFinderUrl
   */
  public function setVotingLocationFinderUrl($votingLocationFinderUrl)
  {
    $this->votingLocationFinderUrl = $votingLocationFinderUrl;
  }
  /**
   * @return string
   */
  public function getVotingLocationFinderUrl()
  {
    return $this->votingLocationFinderUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2AdministrativeBody::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2AdministrativeBody');
