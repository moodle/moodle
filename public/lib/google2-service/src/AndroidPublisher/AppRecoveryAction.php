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

namespace Google\Service\AndroidPublisher;

class AppRecoveryAction extends \Google\Model
{
  /**
   * RecoveryStatus is unspecified.
   */
  public const STATUS_RECOVERY_STATUS_UNSPECIFIED = 'RECOVERY_STATUS_UNSPECIFIED';
  /**
   * The app recovery action has not been canceled since it has been created.
   */
  public const STATUS_RECOVERY_STATUS_ACTIVE = 'RECOVERY_STATUS_ACTIVE';
  /**
   * The recovery action has been canceled. The action cannot be resumed.
   */
  public const STATUS_RECOVERY_STATUS_CANCELED = 'RECOVERY_STATUS_CANCELED';
  /**
   * The recovery action is in the draft state and has not yet been deployed to
   * users.
   */
  public const STATUS_RECOVERY_STATUS_DRAFT = 'RECOVERY_STATUS_DRAFT';
  /**
   * The recovery action is generating recovery apks.
   */
  public const STATUS_RECOVERY_STATUS_GENERATION_IN_PROGRESS = 'RECOVERY_STATUS_GENERATION_IN_PROGRESS';
  /**
   * The app recovery action generation has failed.
   */
  public const STATUS_RECOVERY_STATUS_GENERATION_FAILED = 'RECOVERY_STATUS_GENERATION_FAILED';
  /**
   * ID corresponding to the app recovery action.
   *
   * @var string
   */
  public $appRecoveryId;
  /**
   * Timestamp of when the app recovery action is canceled by the developer.
   * Only set if the recovery action has been canceled.
   *
   * @var string
   */
  public $cancelTime;
  /**
   * Timestamp of when the app recovery action is created by the developer. It
   * is always set after creation of the recovery action.
   *
   * @var string
   */
  public $createTime;
  /**
   * Timestamp of when the app recovery action is deployed to the users. Only
   * set if the recovery action has been deployed.
   *
   * @var string
   */
  public $deployTime;
  /**
   * Timestamp of when the developer last updated recovery action. In case the
   * action is cancelled, it corresponds to cancellation time. It is always set
   * after creation of the recovery action.
   *
   * @var string
   */
  public $lastUpdateTime;
  protected $remoteInAppUpdateDataType = RemoteInAppUpdateData::class;
  protected $remoteInAppUpdateDataDataType = '';
  /**
   * The status of the recovery action.
   *
   * @var string
   */
  public $status;
  protected $targetingType = Targeting::class;
  protected $targetingDataType = '';

  /**
   * ID corresponding to the app recovery action.
   *
   * @param string $appRecoveryId
   */
  public function setAppRecoveryId($appRecoveryId)
  {
    $this->appRecoveryId = $appRecoveryId;
  }
  /**
   * @return string
   */
  public function getAppRecoveryId()
  {
    return $this->appRecoveryId;
  }
  /**
   * Timestamp of when the app recovery action is canceled by the developer.
   * Only set if the recovery action has been canceled.
   *
   * @param string $cancelTime
   */
  public function setCancelTime($cancelTime)
  {
    $this->cancelTime = $cancelTime;
  }
  /**
   * @return string
   */
  public function getCancelTime()
  {
    return $this->cancelTime;
  }
  /**
   * Timestamp of when the app recovery action is created by the developer. It
   * is always set after creation of the recovery action.
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
   * Timestamp of when the app recovery action is deployed to the users. Only
   * set if the recovery action has been deployed.
   *
   * @param string $deployTime
   */
  public function setDeployTime($deployTime)
  {
    $this->deployTime = $deployTime;
  }
  /**
   * @return string
   */
  public function getDeployTime()
  {
    return $this->deployTime;
  }
  /**
   * Timestamp of when the developer last updated recovery action. In case the
   * action is cancelled, it corresponds to cancellation time. It is always set
   * after creation of the recovery action.
   *
   * @param string $lastUpdateTime
   */
  public function setLastUpdateTime($lastUpdateTime)
  {
    $this->lastUpdateTime = $lastUpdateTime;
  }
  /**
   * @return string
   */
  public function getLastUpdateTime()
  {
    return $this->lastUpdateTime;
  }
  /**
   * Data about the remote in-app update action such as such as recovered user
   * base, recoverable user base etc. Set only if the recovery action type is
   * Remote In-App Update.
   *
   * @param RemoteInAppUpdateData $remoteInAppUpdateData
   */
  public function setRemoteInAppUpdateData(RemoteInAppUpdateData $remoteInAppUpdateData)
  {
    $this->remoteInAppUpdateData = $remoteInAppUpdateData;
  }
  /**
   * @return RemoteInAppUpdateData
   */
  public function getRemoteInAppUpdateData()
  {
    return $this->remoteInAppUpdateData;
  }
  /**
   * The status of the recovery action.
   *
   * Accepted values: RECOVERY_STATUS_UNSPECIFIED, RECOVERY_STATUS_ACTIVE,
   * RECOVERY_STATUS_CANCELED, RECOVERY_STATUS_DRAFT,
   * RECOVERY_STATUS_GENERATION_IN_PROGRESS, RECOVERY_STATUS_GENERATION_FAILED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Specifies targeting criteria for the recovery action such as regions,
   * android sdk versions, app versions etc.
   *
   * @param Targeting $targeting
   */
  public function setTargeting(Targeting $targeting)
  {
    $this->targeting = $targeting;
  }
  /**
   * @return Targeting
   */
  public function getTargeting()
  {
    return $this->targeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppRecoveryAction::class, 'Google_Service_AndroidPublisher_AppRecoveryAction');
