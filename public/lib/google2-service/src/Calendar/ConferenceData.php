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

namespace Google\Service\Calendar;

class ConferenceData extends \Google\Collection
{
  protected $collection_key = 'entryPoints';
  /**
   * The ID of the conference. Can be used by developers to keep track of
   * conferences, should not be displayed to users. The ID value is formed
   * differently for each conference solution type:   - eventHangout: ID is not
   * set. (This conference type is deprecated.) - eventNamedHangout: ID is the
   * name of the Hangout. (This conference type is deprecated.) - hangoutsMeet:
   * ID is the 10-letter meeting code, for example aaa-bbbb-ccc. - addOn: ID is
   * defined by the third-party provider.  Optional.
   *
   * @var string
   */
  public $conferenceId;
  protected $conferenceSolutionType = ConferenceSolution::class;
  protected $conferenceSolutionDataType = '';
  protected $createRequestType = CreateConferenceRequest::class;
  protected $createRequestDataType = '';
  protected $entryPointsType = EntryPoint::class;
  protected $entryPointsDataType = 'array';
  /**
   * Additional notes (such as instructions from the domain administrator, legal
   * notices) to display to the user. Can contain HTML. The maximum length is
   * 2048 characters. Optional.
   *
   * @var string
   */
  public $notes;
  protected $parametersType = ConferenceParameters::class;
  protected $parametersDataType = '';
  /**
   * The signature of the conference data. Generated on server side. Unset for a
   * conference with a failed create request. Optional for a conference with a
   * pending create request.
   *
   * @var string
   */
  public $signature;

  /**
   * The ID of the conference. Can be used by developers to keep track of
   * conferences, should not be displayed to users. The ID value is formed
   * differently for each conference solution type:   - eventHangout: ID is not
   * set. (This conference type is deprecated.) - eventNamedHangout: ID is the
   * name of the Hangout. (This conference type is deprecated.) - hangoutsMeet:
   * ID is the 10-letter meeting code, for example aaa-bbbb-ccc. - addOn: ID is
   * defined by the third-party provider.  Optional.
   *
   * @param string $conferenceId
   */
  public function setConferenceId($conferenceId)
  {
    $this->conferenceId = $conferenceId;
  }
  /**
   * @return string
   */
  public function getConferenceId()
  {
    return $this->conferenceId;
  }
  /**
   * The conference solution, such as Google Meet. Unset for a conference with a
   * failed create request. Either conferenceSolution and at least one
   * entryPoint, or createRequest is required.
   *
   * @param ConferenceSolution $conferenceSolution
   */
  public function setConferenceSolution(ConferenceSolution $conferenceSolution)
  {
    $this->conferenceSolution = $conferenceSolution;
  }
  /**
   * @return ConferenceSolution
   */
  public function getConferenceSolution()
  {
    return $this->conferenceSolution;
  }
  /**
   * A request to generate a new conference and attach it to the event. The data
   * is generated asynchronously. To see whether the data is present check the
   * status field. Either conferenceSolution and at least one entryPoint, or
   * createRequest is required.
   *
   * @param CreateConferenceRequest $createRequest
   */
  public function setCreateRequest(CreateConferenceRequest $createRequest)
  {
    $this->createRequest = $createRequest;
  }
  /**
   * @return CreateConferenceRequest
   */
  public function getCreateRequest()
  {
    return $this->createRequest;
  }
  /**
   * Information about individual conference entry points, such as URLs or phone
   * numbers. All of them must belong to the same conference. Either
   * conferenceSolution and at least one entryPoint, or createRequest is
   * required.
   *
   * @param EntryPoint[] $entryPoints
   */
  public function setEntryPoints($entryPoints)
  {
    $this->entryPoints = $entryPoints;
  }
  /**
   * @return EntryPoint[]
   */
  public function getEntryPoints()
  {
    return $this->entryPoints;
  }
  /**
   * Additional notes (such as instructions from the domain administrator, legal
   * notices) to display to the user. Can contain HTML. The maximum length is
   * 2048 characters. Optional.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Additional properties related to a conference. An example would be a
   * solution-specific setting for enabling video streaming.
   *
   * @param ConferenceParameters $parameters
   */
  public function setParameters(ConferenceParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return ConferenceParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * The signature of the conference data. Generated on server side. Unset for a
   * conference with a failed create request. Optional for a conference with a
   * pending create request.
   *
   * @param string $signature
   */
  public function setSignature($signature)
  {
    $this->signature = $signature;
  }
  /**
   * @return string
   */
  public function getSignature()
  {
    return $this->signature;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceData::class, 'Google_Service_Calendar_ConferenceData');
