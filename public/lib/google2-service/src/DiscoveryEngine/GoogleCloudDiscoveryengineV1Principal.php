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

class GoogleCloudDiscoveryengineV1Principal extends \Google\Model
{
  /**
   * For 3P application identities which are not present in the customer
   * identity provider.
   *
   * @var string
   */
  public $externalEntityId;
  /**
   * Group identifier. For Google Workspace user account, group_id should be the
   * google workspace group email. For non-google identity provider user
   * account, group_id is the mapped group identifier configured during the
   * workforcepool config.
   *
   * @var string
   */
  public $groupId;
  /**
   * User identifier. For Google Workspace user account, user_id should be the
   * google workspace user email. For non-google identity provider user account,
   * user_id is the mapped user identifier configured during the workforcepool
   * config.
   *
   * @var string
   */
  public $userId;

  /**
   * For 3P application identities which are not present in the customer
   * identity provider.
   *
   * @param string $externalEntityId
   */
  public function setExternalEntityId($externalEntityId)
  {
    $this->externalEntityId = $externalEntityId;
  }
  /**
   * @return string
   */
  public function getExternalEntityId()
  {
    return $this->externalEntityId;
  }
  /**
   * Group identifier. For Google Workspace user account, group_id should be the
   * google workspace group email. For non-google identity provider user
   * account, group_id is the mapped group identifier configured during the
   * workforcepool config.
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
   * google workspace user email. For non-google identity provider user account,
   * user_id is the mapped user identifier configured during the workforcepool
   * config.
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
class_alias(GoogleCloudDiscoveryengineV1Principal::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Principal');
