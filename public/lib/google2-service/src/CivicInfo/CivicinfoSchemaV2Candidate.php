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

class CivicinfoSchemaV2Candidate extends \Google\Collection
{
  protected $collection_key = 'channels';
  /**
   * The URL for the candidate's campaign web site.
   *
   * @var string
   */
  public $candidateUrl;
  protected $channelsType = CivicinfoSchemaV2Channel::class;
  protected $channelsDataType = 'array';
  /**
   * The email address for the candidate's campaign.
   *
   * @var string
   */
  public $email;
  /**
   * The candidate's name. If this is a joint ticket it will indicate the name
   * of the candidate at the top of a ticket followed by a / and that name of
   * candidate at the bottom of the ticket. e.g. "Mitt Romney / Paul Ryan"
   *
   * @var string
   */
  public $name;
  /**
   * The order the candidate appears on the ballot for this contest.
   *
   * @var string
   */
  public $orderOnBallot;
  /**
   * The full name of the party the candidate is a member of.
   *
   * @var string
   */
  public $party;
  /**
   * The voice phone number for the candidate's campaign office.
   *
   * @var string
   */
  public $phone;
  /**
   * A URL for a photo of the candidate.
   *
   * @var string
   */
  public $photoUrl;

  /**
   * The URL for the candidate's campaign web site.
   *
   * @param string $candidateUrl
   */
  public function setCandidateUrl($candidateUrl)
  {
    $this->candidateUrl = $candidateUrl;
  }
  /**
   * @return string
   */
  public function getCandidateUrl()
  {
    return $this->candidateUrl;
  }
  /**
   * A list of known (social) media channels for this candidate.
   *
   * @param CivicinfoSchemaV2Channel[] $channels
   */
  public function setChannels($channels)
  {
    $this->channels = $channels;
  }
  /**
   * @return CivicinfoSchemaV2Channel[]
   */
  public function getChannels()
  {
    return $this->channels;
  }
  /**
   * The email address for the candidate's campaign.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * The candidate's name. If this is a joint ticket it will indicate the name
   * of the candidate at the top of a ticket followed by a / and that name of
   * candidate at the bottom of the ticket. e.g. "Mitt Romney / Paul Ryan"
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
   * The order the candidate appears on the ballot for this contest.
   *
   * @param string $orderOnBallot
   */
  public function setOrderOnBallot($orderOnBallot)
  {
    $this->orderOnBallot = $orderOnBallot;
  }
  /**
   * @return string
   */
  public function getOrderOnBallot()
  {
    return $this->orderOnBallot;
  }
  /**
   * The full name of the party the candidate is a member of.
   *
   * @param string $party
   */
  public function setParty($party)
  {
    $this->party = $party;
  }
  /**
   * @return string
   */
  public function getParty()
  {
    return $this->party;
  }
  /**
   * The voice phone number for the candidate's campaign office.
   *
   * @param string $phone
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;
  }
  /**
   * @return string
   */
  public function getPhone()
  {
    return $this->phone;
  }
  /**
   * A URL for a photo of the candidate.
   *
   * @param string $photoUrl
   */
  public function setPhotoUrl($photoUrl)
  {
    $this->photoUrl = $photoUrl;
  }
  /**
   * @return string
   */
  public function getPhotoUrl()
  {
    return $this->photoUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CivicinfoSchemaV2Candidate::class, 'Google_Service_CivicInfo_CivicinfoSchemaV2Candidate');
