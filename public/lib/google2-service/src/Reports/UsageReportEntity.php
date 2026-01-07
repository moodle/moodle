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

namespace Google\Service\Reports;

class UsageReportEntity extends \Google\Model
{
  /**
   * Output only. The unique identifier of the customer's account.
   *
   * @var string
   */
  public $customerId;
  /**
   * Output only. Object key. Only relevant if entity.type = "OBJECT" Note:
   * external-facing name of report is "Entities" rather than "Objects".
   *
   * @var string
   */
  public $entityId;
  /**
   * Output only. The user's immutable Google Workspace profile identifier.
   *
   * @var string
   */
  public $profileId;
  /**
   * Output only. The type of item. The value is `user`.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The user's email address. Only relevant if entity.type =
   * "USER"
   *
   * @var string
   */
  public $userEmail;

  /**
   * Output only. The unique identifier of the customer's account.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Output only. Object key. Only relevant if entity.type = "OBJECT" Note:
   * external-facing name of report is "Entities" rather than "Objects".
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * Output only. The user's immutable Google Workspace profile identifier.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
  /**
   * Output only. The type of item. The value is `user`.
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
  /**
   * Output only. The user's email address. Only relevant if entity.type =
   * "USER"
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UsageReportEntity::class, 'Google_Service_Reports_UsageReportEntity');
