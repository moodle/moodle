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

class DynamiteMessagesScoringInfo extends \Google\Model
{
  /**
   * @var string
   */
  public $commonContactCount;
  public $commonCountToContactListCountRatio;
  public $commonCountToMembershipCountRatio;
  /**
   * @var string
   */
  public $creatorGaiaId;
  /**
   * @var bool
   */
  public $creatorInSearcherContactList;
  public $crowdingMultiplier;
  /**
   * @var string
   */
  public $dasContactCount;
  public $finalScore;
  public $freshnessScore;
  public $joinedSpaceAffinityScore;
  public $lastReadTimestampAgeInDays;
  public $messageAgeInDays;
  public $messageSenderAffinityScore;
  /**
   * @var string
   */
  public $spaceId;
  /**
   * @var string
   */
  public $spaceMembershipCount;
  public $topicalityScore;
  public $unjoinedSpaceAffinityScore;

  /**
   * @param string
   */
  public function setCommonContactCount($commonContactCount)
  {
    $this->commonContactCount = $commonContactCount;
  }
  /**
   * @return string
   */
  public function getCommonContactCount()
  {
    return $this->commonContactCount;
  }
  public function setCommonCountToContactListCountRatio($commonCountToContactListCountRatio)
  {
    $this->commonCountToContactListCountRatio = $commonCountToContactListCountRatio;
  }
  public function getCommonCountToContactListCountRatio()
  {
    return $this->commonCountToContactListCountRatio;
  }
  public function setCommonCountToMembershipCountRatio($commonCountToMembershipCountRatio)
  {
    $this->commonCountToMembershipCountRatio = $commonCountToMembershipCountRatio;
  }
  public function getCommonCountToMembershipCountRatio()
  {
    return $this->commonCountToMembershipCountRatio;
  }
  /**
   * @param string
   */
  public function setCreatorGaiaId($creatorGaiaId)
  {
    $this->creatorGaiaId = $creatorGaiaId;
  }
  /**
   * @return string
   */
  public function getCreatorGaiaId()
  {
    return $this->creatorGaiaId;
  }
  /**
   * @param bool
   */
  public function setCreatorInSearcherContactList($creatorInSearcherContactList)
  {
    $this->creatorInSearcherContactList = $creatorInSearcherContactList;
  }
  /**
   * @return bool
   */
  public function getCreatorInSearcherContactList()
  {
    return $this->creatorInSearcherContactList;
  }
  public function setCrowdingMultiplier($crowdingMultiplier)
  {
    $this->crowdingMultiplier = $crowdingMultiplier;
  }
  public function getCrowdingMultiplier()
  {
    return $this->crowdingMultiplier;
  }
  /**
   * @param string
   */
  public function setDasContactCount($dasContactCount)
  {
    $this->dasContactCount = $dasContactCount;
  }
  /**
   * @return string
   */
  public function getDasContactCount()
  {
    return $this->dasContactCount;
  }
  public function setFinalScore($finalScore)
  {
    $this->finalScore = $finalScore;
  }
  public function getFinalScore()
  {
    return $this->finalScore;
  }
  public function setFreshnessScore($freshnessScore)
  {
    $this->freshnessScore = $freshnessScore;
  }
  public function getFreshnessScore()
  {
    return $this->freshnessScore;
  }
  public function setJoinedSpaceAffinityScore($joinedSpaceAffinityScore)
  {
    $this->joinedSpaceAffinityScore = $joinedSpaceAffinityScore;
  }
  public function getJoinedSpaceAffinityScore()
  {
    return $this->joinedSpaceAffinityScore;
  }
  public function setLastReadTimestampAgeInDays($lastReadTimestampAgeInDays)
  {
    $this->lastReadTimestampAgeInDays = $lastReadTimestampAgeInDays;
  }
  public function getLastReadTimestampAgeInDays()
  {
    return $this->lastReadTimestampAgeInDays;
  }
  public function setMessageAgeInDays($messageAgeInDays)
  {
    $this->messageAgeInDays = $messageAgeInDays;
  }
  public function getMessageAgeInDays()
  {
    return $this->messageAgeInDays;
  }
  public function setMessageSenderAffinityScore($messageSenderAffinityScore)
  {
    $this->messageSenderAffinityScore = $messageSenderAffinityScore;
  }
  public function getMessageSenderAffinityScore()
  {
    return $this->messageSenderAffinityScore;
  }
  /**
   * @param string
   */
  public function setSpaceId($spaceId)
  {
    $this->spaceId = $spaceId;
  }
  /**
   * @return string
   */
  public function getSpaceId()
  {
    return $this->spaceId;
  }
  /**
   * @param string
   */
  public function setSpaceMembershipCount($spaceMembershipCount)
  {
    $this->spaceMembershipCount = $spaceMembershipCount;
  }
  /**
   * @return string
   */
  public function getSpaceMembershipCount()
  {
    return $this->spaceMembershipCount;
  }
  public function setTopicalityScore($topicalityScore)
  {
    $this->topicalityScore = $topicalityScore;
  }
  public function getTopicalityScore()
  {
    return $this->topicalityScore;
  }
  public function setUnjoinedSpaceAffinityScore($unjoinedSpaceAffinityScore)
  {
    $this->unjoinedSpaceAffinityScore = $unjoinedSpaceAffinityScore;
  }
  public function getUnjoinedSpaceAffinityScore()
  {
    return $this->unjoinedSpaceAffinityScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamiteMessagesScoringInfo::class, 'Google_Service_CloudSearch_DynamiteMessagesScoringInfo');
