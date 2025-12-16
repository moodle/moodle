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

namespace Google\Service\Firestore;

class GoogleFirestoreAdminV1UserCreds extends \Google\Model
{
  /**
   * The default value. Should not be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The user creds are enabled.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The user creds are disabled.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Output only. The time the user creds were created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Identifier. The resource name of the UserCreds. Format:
   * `projects/{project}/databases/{database}/userCreds/{user_creds}`
   *
   * @var string
   */
  public $name;
  protected $resourceIdentityType = GoogleFirestoreAdminV1ResourceIdentity::class;
  protected $resourceIdentityDataType = '';
  /**
   * Output only. The plaintext server-generated password for the user creds.
   * Only populated in responses for CreateUserCreds and ResetUserPassword.
   *
   * @var string
   */
  public $securePassword;
  /**
   * Output only. Whether the user creds are enabled or disabled. Defaults to
   * ENABLED on creation.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time the user creds were last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time the user creds were created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Identifier. The resource name of the UserCreds. Format:
   * `projects/{project}/databases/{database}/userCreds/{user_creds}`
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
   * Resource Identity descriptor.
   *
   * @param GoogleFirestoreAdminV1ResourceIdentity $resourceIdentity
   */
  public function setResourceIdentity(GoogleFirestoreAdminV1ResourceIdentity $resourceIdentity)
  {
    $this->resourceIdentity = $resourceIdentity;
  }
  /**
   * @return GoogleFirestoreAdminV1ResourceIdentity
   */
  public function getResourceIdentity()
  {
    return $this->resourceIdentity;
  }
  /**
   * Output only. The plaintext server-generated password for the user creds.
   * Only populated in responses for CreateUserCreds and ResetUserPassword.
   *
   * @param string $securePassword
   */
  public function setSecurePassword($securePassword)
  {
    $this->securePassword = $securePassword;
  }
  /**
   * @return string
   */
  public function getSecurePassword()
  {
    return $this->securePassword;
  }
  /**
   * Output only. Whether the user creds are enabled or disabled. Defaults to
   * ENABLED on creation.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The time the user creds were last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleFirestoreAdminV1UserCreds::class, 'Google_Service_Firestore_GoogleFirestoreAdminV1UserCreds');
