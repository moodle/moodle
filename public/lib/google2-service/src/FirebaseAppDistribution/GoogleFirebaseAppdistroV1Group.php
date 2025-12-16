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

namespace Google\Service\FirebaseAppDistribution;

class GoogleFirebaseAppdistroV1Group extends \Google\Model
{
  /**
   * Required. The display name of the group.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The number of invite links for this group.
   *
   * @var int
   */
  public $inviteLinkCount;
  /**
   * The name of the group resource. Format:
   * `projects/{project_number}/groups/{group_alias}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The number of releases this group is permitted to access.
   *
   * @var int
   */
  public $releaseCount;
  /**
   * Output only. The number of testers who are members of this group.
   *
   * @var int
   */
  public $testerCount;

  /**
   * Required. The display name of the group.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The number of invite links for this group.
   *
   * @param int $inviteLinkCount
   */
  public function setInviteLinkCount($inviteLinkCount)
  {
    $this->inviteLinkCount = $inviteLinkCount;
  }
  /**
   * @return int
   */
  public function getInviteLinkCount()
  {
    return $this->inviteLinkCount;
  }
  /**
   * The name of the group resource. Format:
   * `projects/{project_number}/groups/{group_alias}`
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
   * Output only. The number of releases this group is permitted to access.
   *
   * @param int $releaseCount
   */
  public function setReleaseCount($releaseCount)
  {
    $this->releaseCount = $releaseCount;
  }
  /**
   * @return int
   */
  public function getReleaseCount()
  {
    return $this->releaseCount;
  }
  /**
   * Output only. The number of testers who are members of this group.
   *
   * @param int $testerCount
   */
  public function setTesterCount($testerCount)
  {
    $this->testerCount = $testerCount;
  }
  /**
   * @return int
   */
  public function getTesterCount()
  {
    return $this->testerCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirebaseAppdistroV1Group::class, 'Google_Service_FirebaseAppDistribution_GoogleFirebaseAppdistroV1Group');
