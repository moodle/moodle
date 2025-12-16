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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1IdentityMappingEntry extends \Google\Model
{
  /**
   * Required. Identity outside the customer identity provider. The length limit
   * of external identity will be of 100 characters.
   *
   * @var string
   */
  public $externalIdentity;
  /**
   * Optional. The name of the external identity.
   *
   * @var string
   */
  public $externalIdentityName;
  /**
   * Group identifier. For Google Workspace user account, group_id should be the
   * google workspace group email. For non-google identity provider, group_id is
   * the mapped group identifier configured during the workforcepool config.
   *
   * @var string
   */
  public $groupId;
  /**
   * User identifier. For Google Workspace user account, user_id should be the
   * google workspace user email. For non-google identity provider, user_id is
   * the mapped user identifier configured during the workforcepool config.
   *
   * @var string
   */
  public $userId;

  /**
   * Required. Identity outside the customer identity provider. The length limit
   * of external identity will be of 100 characters.
   *
   * @param string $externalIdentity
   */
  public function setExternalIdentity($externalIdentity)
  {
    $this->externalIdentity = $externalIdentity;
  }
  /**
   * @return string
   */
  public function getExternalIdentity()
  {
    return $this->externalIdentity;
  }
  /**
   * Optional. The name of the external identity.
   *
   * @param string $externalIdentityName
   */
  public function setExternalIdentityName($externalIdentityName)
  {
    $this->externalIdentityName = $externalIdentityName;
  }
  /**
   * @return string
   */
  public function getExternalIdentityName()
  {
    return $this->externalIdentityName;
  }
  /**
   * Group identifier. For Google Workspace user account, group_id should be the
   * google workspace group email. For non-google identity provider, group_id is
   * the mapped group identifier configured during the workforcepool config.
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * User identifier. For Google Workspace user account, user_id should be the
   * google workspace user email. For non-google identity provider, user_id is
   * the mapped user identifier configured during the workforcepool config.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1IdentityMappingEntry::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1IdentityMappingEntry');
