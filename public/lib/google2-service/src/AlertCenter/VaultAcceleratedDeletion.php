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

namespace Google\Service\AlertCenter;

class VaultAcceleratedDeletion extends \Google\Model
{
  /**
   * Unspecified action type
   */
  public const ACTION_TYPE_VAULT_ACCELERATED_DELETION_ACTION_TYPE_UNSPECIFIED = 'VAULT_ACCELERATED_DELETION_ACTION_TYPE_UNSPECIFIED';
  /**
   * AD Create action type
   */
  public const ACTION_TYPE_VAULT_ACCELERATED_DELETION_ACTION_TYPE_CREATE = 'VAULT_ACCELERATED_DELETION_ACTION_TYPE_CREATE';
  /**
   * AD Cancel action type
   */
  public const ACTION_TYPE_VAULT_ACCELERATED_DELETION_ACTION_TYPE_CANCEL = 'VAULT_ACCELERATED_DELETION_ACTION_TYPE_CANCEL';
  /**
   * Unspecified app type
   */
  public const APP_TYPE_VAULT_ACCELERATED_DELETION_APP_TYPE_UNSPECIFIED = 'VAULT_ACCELERATED_DELETION_APP_TYPE_UNSPECIFIED';
  /**
   * Gmail app type
   */
  public const APP_TYPE_VAULT_ACCELERATED_DELETION_APP_TYPE_GMAIL = 'VAULT_ACCELERATED_DELETION_APP_TYPE_GMAIL';
  /**
   * The action can be one of create and cancel
   *
   * @var string
   */
  public $actionType;
  /**
   * Currentlty only Gmail is supported as app type
   *
   * @var string
   */
  public $appType;
  /**
   * The UTC timestamp of when the AD request was created
   *
   * @var string
   */
  public $createTime;
  /**
   * Accelerated deletion request ID intended to be used to construct the Vault
   * UI link to the AD request
   *
   * @var string
   */
  public $deletionRequestId;
  /**
   * Matter ID of the accelerated deletion request intended to be used to
   * construct the Vault UI link to the AD request
   *
   * @var string
   */
  public $matterId;

  /**
   * The action can be one of create and cancel
   *
   * Accepted values: VAULT_ACCELERATED_DELETION_ACTION_TYPE_UNSPECIFIED,
   * VAULT_ACCELERATED_DELETION_ACTION_TYPE_CREATE,
   * VAULT_ACCELERATED_DELETION_ACTION_TYPE_CANCEL
   *
   * @param self::ACTION_TYPE_* $actionType
   */
  public function setActionType($actionType)
  {
    $this->actionType = $actionType;
  }
  /**
   * @return self::ACTION_TYPE_*
   */
  public function getActionType()
  {
    return $this->actionType;
  }
  /**
   * Currentlty only Gmail is supported as app type
   *
   * Accepted values: VAULT_ACCELERATED_DELETION_APP_TYPE_UNSPECIFIED,
   * VAULT_ACCELERATED_DELETION_APP_TYPE_GMAIL
   *
   * @param self::APP_TYPE_* $appType
   */
  public function setAppType($appType)
  {
    $this->appType = $appType;
  }
  /**
   * @return self::APP_TYPE_*
   */
  public function getAppType()
  {
    return $this->appType;
  }
  /**
   * The UTC timestamp of when the AD request was created
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
   * Accelerated deletion request ID intended to be used to construct the Vault
   * UI link to the AD request
   *
   * @param string $deletionRequestId
   */
  public function setDeletionRequestId($deletionRequestId)
  {
    $this->deletionRequestId = $deletionRequestId;
  }
  /**
   * @return string
   */
  public function getDeletionRequestId()
  {
    return $this->deletionRequestId;
  }
  /**
   * Matter ID of the accelerated deletion request intended to be used to
   * construct the Vault UI link to the AD request
   *
   * @param string $matterId
   */
  public function setMatterId($matterId)
  {
    $this->matterId = $matterId;
  }
  /**
   * @return string
   */
  public function getMatterId()
  {
    return $this->matterId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VaultAcceleratedDeletion::class, 'Google_Service_AlertCenter_VaultAcceleratedDeletion');
