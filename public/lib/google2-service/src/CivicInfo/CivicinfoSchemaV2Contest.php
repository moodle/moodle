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

class CivicinfoSchemaV2Contest extends \Google\Collection
{
  protected $collection_key = 'sources';
  /**
   * A number specifying the position of this contest on the voter's ballot.
   *
   * @var string
   */
  public $ballotPlacement;
  /**
   * The official title on the ballot for this contest, only where available.
   *
   * @var string
   */
  public $ballotTitle;
  protected $candidatesType = CivicinfoSchemaV2Candidate::class;
  protected $candidatesDataType = 'array';
  protected $districtType = CivicinfoSchemaV2ElectoralDistrict::class;
  protected $districtDataType = '';
  /**
   * A description of any additional eligibility requirements for voting in this
   * contest.
   *
   * @var string
   */
  public $electorateSpecifications;
  /**
   * The levels of government of the office for this contest. There may be more
   * than one in cases where a jurisdiction effectively acts at two different
   * levels of government; for example, the mayor of the District of Columbia
   * acts at "locality" level, but also effectively at both "administrative-
   * area-2" and "administrative-area-1".
   *
   * @var string[]
   */
  public $level;
  /**
   * The number of candidates that will be elected to office in this contest.
   *
   * @var string
   */
  public $numberElected;
  /**
   * The number of candidates that a voter may vote for in this contest.
   *
   * @var string
   */
  public $numberVotingFor;
  /**
   * The name of the office for this contest.
   *
   * @var string
   */
  public $office;
  /**
   * If this is a partisan election, the name of the party/parties it is for.
   *
   * @var string[]
   */
  public $primaryParties;
  /**
   * The set of ballot responses for the referendum. A ballot response
   * represents a line on the ballot. Common examples might include "yes" or
   * "no" for referenda. This field is only populated for contests of type
   * 'Referendum'.
   *
   * @var string[]
   */
  public $referendumBallotResponses;
  /**
   * Specifies a short summary of the referendum that is typically on the ballot
   * below the title but above the text. This field is only populated for
   * contests of type 'Referendum'.
   *
   * @var string
   */
  public $referendumBrief;
  /**
   * A statement in opposition to the referendum. It does not necessarily appear
   * on the ballot. This field is only populated for contests of type
   * 'Referendum'.
   *
   * @var string
   */
  public $referendumConStatement;
  /**
   * Specifies what effect abstaining (not voting) on the proposition will have
   * (i.e. whether abstaining is considered a vote against it). This field is
   * only populated for contests of type 'Referendum'.
   *
   * @var string
   */
  public $referendumEffectOfAbstain;
  /**
   * The threshold of votes that the referendum needs in order to pass, e.g.
   * "two-thirds". This field is only populated for contests of type
   * 'Referendum'.
   *
   * @var string
   */
  public $referendumPassageThreshold;
  /**
   * A statement in favor of the referendum. It does not necessarily appear on
   * the ballot. This field is only populated for contests of type 'Referendum'.
   *
   * @var string
   */
  public $referendumProStatement;
  /**
   * A brief description of the referendum. This field is only populated for
   * contests of type 'Referendum'.
   *
   * @var string
   */
  public $referendumSubtitle;
  /**
   * The full text of the referendum. This field is only populated for contests
   * of type 'Referendum'.
   *
   * @var string
   */
  public $referendumText;
  /**
   * The title of the referendum (e.g. 'Proposition 42'). This field is only
   * populated for contests of type 'Referendum'.
   *
   * @var string
   */
  public $referendumTitle;
  /**
   * A link to the referendum. This field is only populated for contests of type
   * 'Referendum'.
   *
   * @var string
   */
  public $referendumUrl;
  /**
   * The roles which this office fulfills.
   *
   * @var string[]
   */
  public $roles;
  protected $sourcesType = CivicinfoSchemaV2Source::class;
  protected $sourcesDataType = 'array';
  /**
   * "Yes" or "No" depending on whether this a contest being held outside the
   * normal election cycle.
   *
   * @var string
   */
  public $special;
  /**
   * The type of contest. Usually this will be 'General', 'Primary', or 'Run-
   * off' for contests with candidates. For referenda this will be 'Referendum'.
   * For Retention contests this will typically be 'Retention'.
   *
   * @var string
   */
  public $type;

  /**
   * A number specifying the position of this contest on the voter's ballot.
   *
   * @param string $ballotPlacement
   */
  public function setBallotPlacement($ballotPlacement)
  {
    $this->ballotPlacement = $ballotPlacement;
  }
  /**
   * @return string
   */
  public function getBallotPlacement()
  {
    return $this->ballotPlacement;
  }
  /**
   * The official title on the ballot for this contest, only where available.
   *
   * @param string $ballotTitle
   */
  public function setBallotTitle($ballotTitle)
  {
    $this->ballotTitle = $ballotTitle;
  }
  /**
   * @return string
   */
  public function getBallotTitle()
  {
    return $this->ballotTitle;
  }
  /**
   * The candidate choices for this contest.
   *
   * @param CivicinfoSchemaV2Candidate[] $candidates
   */
  public function setCandidates($candidates)
  {
    $this->candidates = $candidates;
  }
  /**
   * @return CivicinfoSchemaV2Candidate[]
   */
  public function getCandidates()
  {
    return $this->candidates;
  }
  /**
   * Information about the electoral district that this contest is in.
   *
   * @param CivicinfoSchemaV2ElectoralDistrict $district
   */
  public function setDistrict(CivicinfoSchemaV2ElectoralDistrict $district)
  {
    $this->district = $district;
  }
  /**
   * @return CivicinfoSchemaV2ElectoralDistrict
   */
  public function getDistrict()
  {
    return $this->district;
  }
  /**
   * A description of any additional eligibility requirements for voting in this
   * contest.
   *
   * @param string $electorateSpecifications
   */
  public function setElectorateSpecifications($electorateSpecifications)
  {
    $this->electorateSpecifications = $electorateSpecifications;
  }
  /**
   * @return string
   */
  public function getElectorateSpecifications()
  {
    return $this->electorateSpecifications;
  }
  /**
   * The levels of government of the office for this contest. There may be more
   * than one in cases where a jurisdiction effectively acts at two different
   * levels of government; for example, the mayor of the District of Columbia
   * acts at "locality" level, but also effectively at both "administrative-
   * area-2" and "administrative-area-1".
   *
   * @param string[] $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }
  /**
   * @return string[]
   */
  public function getLevel()
  {
    return $this->level;
  }
  /**
   * The number of candidates that will be elected to office in this contest.
   *
   * @param string $numberElected
   */
  public function setNumberElected($numberElected)
  {
    $this->numberElected = $numberElected;
  }
  /**
   * @return string
   */
  public function getNumberElected()
  {
    return $this->numberElected;
  }
  /**
   * The number of candidates that a voter may vote for in this contest.
   *
   * @param string $numberVotingFor
   */
  public function setNumberVotingFor($numberVotingFor)
  {
    $this->numberVotingFor = $numberVotingFor;
  }
  /**
   * @return string
   */
  public function getNumberVotingFor()
  {
    return $this->numberVotingFor;
  }
  /**
   * The name of the office for this contest.
   *
   * @param string $office
   */
  public function setOffice($office)
  {
    $this->office = $office;
  }
  /**
   * @return string
   */
  public function getOffice()
  {
    return $this->office;
  }
  /**
   * If this is a partisan election, the name of the party/parties it is for.
   *
   * @param string[] $primaryParties
   */
  public function setPrimaryParties($primaryParties)
  {
    $this->primaryParties = $primaryParties;
  }
  /**
   * @return string[]
   */
  public function getPrimaryParties()
  {
    return $this->primaryParties;
  }
  /**
   * The set of ballot responses for the referendum. A ballot response
   * represents a line on the ballot. Common examples might include "yes" or
   * "no" for referenda. This field is only populated for contests of type
   * 'Referendum'.
   *
   * @param string[] $referendumBallotResponses
   */
  public function setReferendumBallotResponses($referendumBallotResponses)
  {
    $this->referendumBallotResponses = $referendumBallotResponses;
  }
  /**
   * @return string[]
   */
  public function getReferendumBallotResponses()
  {
    return $this->referendumBallotResponses;
  }
  /**
   * Specifies a short summary of the referendum that is typically on the ballot
   * below the title but above the text. This field is only populated for
   * contests of type 'Referendum'.
   *
   * @param string $referendumBrief
   */
  public function setReferendumBrief($referendumBrief)
  {
    $this->referendumBrief = $referendumBrief;
  }
  /**
   * @return string
   */
  public function getReferendumBrief()
  {
    return $this->referendumBrief;
  }
  /**
   * A statement in opposition to the referendum. It does not necessarily appear
   * on the ballot. This field is only populated for contests of type
   * 'Referendum'.
   *
   * @param string $referendumConStatement
   */
  public function setReferendumConStatement($referendumConStatement)
  {
    $this->referendumConStatement = $referendumConStatement;
  }
  /**
   * @return string
   */
  public function getReferendumConStatement()
  {
    return $this->referendumConStatement;
  }
  /**
   * Specifies what effect abstaining (not voting) on the proposition will have
   * (i.e. whether abstaining is considered a vote against it). This field is
   * only populated for contests of type 'Referendum'.
   *
   * @param string $referendumEffectOfAbstain
   */
  public function setReferendumEffectOfAbstain($referendumEffectOfAbstain)
  {
    $this->referendumEffectOfAbstain = $referendumEffectOfAbstain;
  }
  /**
   * @return string
   */
  public function getReferendumEffectOfAbstain()
  {
    return $this->referendumEffectOfAbstain;
  }
  /**
   * The threshold of votes that the referendum needs in order to pass, e.g.
   * "two-thirds". This field is only populated for contests of type
   * 'Referendum'.
   *
   * @param string $referendumPassageThreshold
   */
  public function setReferendumPassageThreshold($referendumPassageThreshold)
  {
    $this->referendumPassageThreshold = $referendumPassageThreshold;
  }
  /**
   * @return string
   */
  public function getReferendumPassageThreshold()
  {
    return $this->referendumPassageThreshold;
  }
  /**
   * A statement in favor of the referendum. It does not necessarily appear on
   * the ballot. This field is only populated for contests of type 'Referendum'.
   *
   * @param string $referendumProStatement
   */
  public function setReferendumProStatement($referendumProStatement)
  {
    $this->referendumProStatement = $referendumProStatement;
  }
  /**
   * @return string
   */
  public function getReferendumProStatement()
  {
    return $this->referendumProStatement;
  }
  /**
   * A brief description of the referendum. This field is only populated for
   * contests of type 'Referendum'.
   *
   * @param string $referendumSubtitle
   */
  public function setReferendumSubtitle($referendumSubtitle)
  {
    $this->referendumSubtitle = $referendumSubtitle;
  }
  /**
   * @return string
   */
  public function getReferendumSubtitle()
  {
    return $this->referendumSubtitle;
  }
  /**
   * The full text of the referendum. This field is only populated for contests
   * of type 'Referendum'.
   *
   * @param string $referendumText
   */
  public function setReferendumText($referendumText)
  {
    $this->referendumText = $referendumText;
  }
  /**
   * @return string
   */
  public function getReferendumText()
  {
    return $this->referendumText;
  }
  /**
   * The title of the referendum (e.g. 'Proposition 42'). This field is only
   * populated for contests of type 'Referendum'.
   *
   * @param string $referendumTitle
   */
  public function setReferendumTitle($referendumTitle)
  {
    $this->referendumTitle = $referendumTitle;
  }
  /**
   * @return string
   */
  public function getReferendumTitle()
  {
    return $this->referendumTitle;
  }
  /**
   * A link to the referendum. This field is only populated for contests of type
   * 'Referendum'.
   *
   * @param string $referendumUrl
   */
  public function setReferendumUrl($referendumUrl)
  {
    $this->referendumUrl = $referendumUrl;
  }
  /**
   * @return string
   */
  public function getReferendumUrl()
  {
    return $this->referendumUrl;
  }
  /**
   * The roles which this office fulfills.
   *
   * @param string[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return string[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
  /**
   * A list of sources for this contest. If multiple sources are listed, the
   * data has been aggregated from those sources.
   *
   * @param CivicinfoSchemaV2Source[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return CivicinfoSchemaV2Source[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * "Yes" or "No" depending on whether this a contest being held outside the
   * normal election cycle.
   *
   * @param string $special
   */
  public function setSpecial($special)
  {
    $this->special = $special;
  }
  /**
   * @return string
   */
  public function getSpecial()
  {
    return $this->special;
  }
  /**
   * The type of contest. Usually this will be 'General', 'Primary', or 'Run-
   * off' for contests with candidates. For referenda this will be 'Referendum'.
   * For Retention contests this will typically be 'Retention'.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2Contest::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2Contest');
