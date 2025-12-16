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

class GeneratedRecoveryApk extends \Google\Model
{
  /**
   * RecoveryStatus is unspecified.
   */
  public const RECOVERY_STATUS_RECOVERY_STATUS_UNSPECIFIED = 'RECOVERY_STATUS_UNSPECIFIED';
  /**
   * The app recovery action has not been canceled since it has been created.
   */
  public const RECOVERY_STATUS_RECOVERY_STATUS_ACTIVE = 'RECOVERY_STATUS_ACTIVE';
  /**
   * The recovery action has been canceled. The action cannot be resumed.
   */
  public const RECOVERY_STATUS_RECOVERY_STATUS_CANCELED = 'RECOVERY_STATUS_CANCELED';
  /**
   * The recovery action is in the draft state and has not yet been deployed to
   * users.
   */
  public const RECOVERY_STATUS_RECOVERY_STATUS_DRAFT = 'RECOVERY_STATUS_DRAFT';
  /**
   * The recovery action is generating recovery apks.
   */
  public const RECOVERY_STATUS_RECOVERY_STATUS_GENERATION_IN_PROGRESS = 'RECOVERY_STATUS_GENERATION_IN_PROGRESS';
  /**
   * The app recovery action generation has failed.
   */
  public const RECOVERY_STATUS_RECOVERY_STATUS_GENERATION_FAILED = 'RECOVERY_STATUS_GENERATION_FAILED';
  /**
   * Download ID, which uniquely identifies the APK to download. Should be
   * supplied to `generatedapks.download` method.
   *
   * @var string
   */
  public $downloadId;
  /**
   * Name of the module which recovery apk belongs to.
   *
   * @var string
   */
  public $moduleName;
  /**
   * ID of the recovery action.
   *
   * @var string
   */
  public $recoveryId;
  /**
   * The status of the recovery action corresponding to the recovery apk.
   *
   * @var string
   */
  public $recoveryStatus;

  /**
   * Download ID, which uniquely identifies the APK to download. Should be
   * supplied to `generatedapks.download` method.
   *
   * @param string $downloadId
   */
  public function setDownloadId($downloadId)
  {
    $this->downloadId = $downloadId;
  }
  /**
   * @return string
   */
  public function getDownloadId()
  {
    return $this->downloadId;
  }
  /**
   * Name of the module which recovery apk belongs to.
   *
   * @param string $moduleName
   */
  public function setModuleName($moduleName)
  {
    $this->moduleName = $moduleName;
  }
  /**
   * @return string
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }
  /**
   * ID of the recovery action.
   *
   * @param string $recoveryId
   */
  public function setRecoveryId($recoveryId)
  {
    $this->recoveryId = $recoveryId;
  }
  /**
   * @return string
   */
  public function getRecoveryId()
  {
    return $this->recoveryId;
  }
  /**
   * The status of the recovery action corresponding to the recovery apk.
   *
   * Accepted values: RECOVERY_STATUS_UNSPECIFIED, RECOVERY_STATUS_ACTIVE,
   * RECOVERY_STATUS_CANCELED, RECOVERY_STATUS_DRAFT,
   * RECOVERY_STATUS_GENERATION_IN_PROGRESS, RECOVERY_STATUS_GENERATION_FAILED
   *
   * @param self::RECOVERY_STATUS_* $recoveryStatus
   */
  public function setRecoveryStatus($recoveryStatus)
  {
    $this->recoveryStatus = $recoveryStatus;
  }
  /**
   * @return self::RECOVERY_STATUS_*
   */
  public function getRecoveryStatus()
  {
    return $this->recoveryStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeneratedRecoveryApk::class, 'Google_Service_AndroidPublisher_GeneratedRecoveryApk');
