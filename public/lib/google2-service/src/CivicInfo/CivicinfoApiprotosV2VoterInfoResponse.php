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

class CivicinfoApiprotosV2VoterInfoResponse extends \Google\Collection
{
  protected $collection_key = 'state';
  protected $contestsType = CivicinfoSchemaV2Contest::class;
  protected $contestsDataType = 'array';
  protected $dropOffLocationsType = CivicinfoSchemaV2PollingLocation::class;
  protected $dropOffLocationsDataType = 'array';
  protected $earlyVoteSitesType = CivicinfoSchemaV2PollingLocation::class;
  protected $earlyVoteSitesDataType = 'array';
  protected $electionType = CivicinfoSchemaV2Election::class;
  protected $electionDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "civicinfo#voterInfoResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * Specifies whether voters in the precinct vote only by mailing their ballots
   * (with the possible option of dropping off their ballots as well).
   *
   * @var bool
   */
  public $mailOnly;
  protected $normalizedInputType = CivicinfoSchemaV2SimpleAddressType::class;
  protected $normalizedInputDataType = '';
  protected $otherElectionsType = CivicinfoSchemaV2Election::class;
  protected $otherElectionsDataType = 'array';
  protected $pollingLocationsType = CivicinfoSchemaV2PollingLocation::class;
  protected $pollingLocationsDataType = 'array';
  /**
   * @var string
   */
  public $precinctId;
  protected $precinctsType = CivicinfoSchemaV2Precinct::class;
  protected $precinctsDataType = 'array';
  protected $stateType = CivicinfoSchemaV2AdministrationRegion::class;
  protected $stateDataType = 'array';

  /**
   * Contests that will appear on the voter's ballot.
   *
   * @param CivicinfoSchemaV2Contest[] $contests
   */
  public function setContests($contests)
  {
    $this->contests = $contests;
  }
  /**
   * @return CivicinfoSchemaV2Contest[]
   */
  public function getContests()
  {
    return $this->contests;
  }
  /**
   * Locations where a voter is eligible to drop off a completed ballot. The
   * voter must have received and completed a ballot prior to arriving at the
   * location. The location may not have ballots available on the premises.
   * These locations could be open on or before election day as indicated in the
   * pollingHours field.
   *
   * @param CivicinfoSchemaV2PollingLocation[] $dropOffLocations
   */
  public function setDropOffLocations($dropOffLocations)
  {
    $this->dropOffLocations = $dropOffLocations;
  }
  /**
   * @return CivicinfoSchemaV2PollingLocation[]
   */
  public function getDropOffLocations()
  {
    return $this->dropOffLocations;
  }
  /**
   * Locations where the voter is eligible to vote early, prior to election day.
   *
   * @param CivicinfoSchemaV2PollingLocation[] $earlyVoteSites
   */
  public function setEarlyVoteSites($earlyVoteSites)
  {
    $this->earlyVoteSites = $earlyVoteSites;
  }
  /**
   * @return CivicinfoSchemaV2PollingLocation[]
   */
  public function getEarlyVoteSites()
  {
    return $this->earlyVoteSites;
  }
  /**
   * The election that was queried.
   *
   * @param CivicinfoSchemaV2Election $election
   */
  public function setElection(CivicinfoSchemaV2Election $election)
  {
    $this->election = $election;
  }
  /**
   * @return CivicinfoSchemaV2Election
   */
  public function getElection()
  {
    return $this->election;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "civicinfo#voterInfoResponse".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Specifies whether voters in the precinct vote only by mailing their ballots
   * (with the possible option of dropping off their ballots as well).
   *
   * @param bool $mailOnly
   */
  public function setMailOnly($mailOnly)
  {
    $this->mailOnly = $mailOnly;
  }
  /**
   * @return bool
   */
  public function getMailOnly()
  {
    return $this->mailOnly;
  }
  /**
   * The normalized version of the requested address
   *
   * @param CivicinfoSchemaV2SimpleAddressType $normalizedInput
   */
  public function setNormalizedInput(CivicinfoSchemaV2SimpleAddressType $normalizedInput)
  {
    $this->normalizedInput = $normalizedInput;
  }
  /**
   * @return CivicinfoSchemaV2SimpleAddressType
   */
  public function getNormalizedInput()
  {
    return $this->normalizedInput;
  }
  /**
   * When there are multiple elections for a voter address, the otherElections
   * field is populated in the API response and there are two possibilities: 1.
   * If the earliest election is not the intended election, specify the election
   * ID of the desired election in a second API request using the electionId
   * field. 2. If these elections occur on the same day, the API doesn?t return
   * any polling location, contest, or election official information to ensure
   * that an additional query is made. For user-facing applications, we
   * recommend displaying these elections to the user to disambiguate. A second
   * API request using the electionId field should be made for the election that
   * is relevant to the user.
   *
   * @param CivicinfoSchemaV2Election[] $otherElections
   */
  public function setOtherElections($otherElections)
  {
    $this->otherElections = $otherElections;
  }
  /**
   * @return CivicinfoSchemaV2Election[]
   */
  public function getOtherElections()
  {
    return $this->otherElections;
  }
  /**
   * Locations where the voter is eligible to vote on election day.
   *
   * @param CivicinfoSchemaV2PollingLocation[] $pollingLocations
   */
  public function setPollingLocations($pollingLocations)
  {
    $this->pollingLocations = $pollingLocations;
  }
  /**
   * @return CivicinfoSchemaV2PollingLocation[]
   */
  public function getPollingLocations()
  {
    return $this->pollingLocations;
  }
  /**
   * @param string $precinctId
   */
  public function setPrecinctId($precinctId)
  {
    $this->precinctId = $precinctId;
  }
  /**
   * @return string
   */
  public function getPrecinctId()
  {
    return $this->precinctId;
  }
  /**
   * The precincts that match this voter's address. Will only be returned for
   * project IDs which have been allowlisted as "partner projects".
   *
   * @param CivicinfoSchemaV2Precinct[] $precincts
   */
  public function setPrecincts($precincts)
  {
    $this->precincts = $precincts;
  }
  /**
   * @return CivicinfoSchemaV2Precinct[]
   */
  public function getPrecincts()
  {
    return $this->precincts;
  }
  /**
   * Local Election Information for the state that the voter votes in. For the
   * US, there will only be one element in this array.
   *
   * @param CivicinfoSchemaV2AdministrationRegion[] $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return CivicinfoSchemaV2AdministrationRegion[]
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoApiprotosV2VoterInfoResponse::class, 'Google_Service_CivicInfo_CivicinfoApiprotosV2VoterInfoResponse');
