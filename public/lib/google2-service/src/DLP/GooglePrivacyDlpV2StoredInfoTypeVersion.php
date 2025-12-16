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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2StoredInfoTypeVersion extends \Google\Collection
{
  /**
   * Unused
   */
  public const STATE_STORED_INFO_TYPE_STATE_UNSPECIFIED = 'STORED_INFO_TYPE_STATE_UNSPECIFIED';
  /**
   * StoredInfoType version is being created.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * StoredInfoType version is ready for use.
   */
  public const STATE_READY = 'READY';
  /**
   * StoredInfoType creation failed. All relevant error messages are returned in
   * the `StoredInfoTypeVersion` message.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * StoredInfoType is no longer valid because artifacts stored in user-
   * controlled storage were modified. To fix an invalid StoredInfoType, use the
   * `UpdateStoredInfoType` method to create a new version.
   */
  public const STATE_INVALID = 'INVALID';
  protected $collection_key = 'errors';
  protected $configType = GooglePrivacyDlpV2StoredInfoTypeConfig::class;
  protected $configDataType = '';
  /**
   * Create timestamp of the version. Read-only, determined by the system when
   * the version is created.
   *
   * @var string
   */
  public $createTime;
  protected $errorsType = GooglePrivacyDlpV2Error::class;
  protected $errorsDataType = 'array';
  /**
   * Stored info type version state. Read-only, updated by the system during
   * dictionary creation.
   *
   * @var string
   */
  public $state;
  protected $statsType = GooglePrivacyDlpV2StoredInfoTypeStats::class;
  protected $statsDataType = '';

  /**
   * StoredInfoType configuration.
   *
   * @param GooglePrivacyDlpV2StoredInfoTypeConfig $config
   */
  public function setConfig(GooglePrivacyDlpV2StoredInfoTypeConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GooglePrivacyDlpV2StoredInfoTypeConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Create timestamp of the version. Read-only, determined by the system when
   * the version is created.
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
   * Errors that occurred when creating this storedInfoType version, or
   * anomalies detected in the storedInfoType data that render it unusable. Only
   * the five most recent errors will be displayed, with the most recent error
   * appearing first. For example, some of the data for stored custom
   * dictionaries is put in the user's Cloud Storage bucket, and if this data is
   * modified or deleted by the user or another system, the dictionary becomes
   * invalid. If any errors occur, fix the problem indicated by the error
   * message and use the UpdateStoredInfoType API method to create another
   * version of the storedInfoType to continue using it, reusing the same
   * `config` if it was not the source of the error.
   *
   * @param GooglePrivacyDlpV2Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GooglePrivacyDlpV2Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Stored info type version state. Read-only, updated by the system during
   * dictionary creation.
   *
   * Accepted values: STORED_INFO_TYPE_STATE_UNSPECIFIED, PENDING, READY,
   * FAILED, INVALID
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
   * Statistics about this storedInfoType version.
   *
   * @param GooglePrivacyDlpV2StoredInfoTypeStats $stats
   */
  public function setStats(GooglePrivacyDlpV2StoredInfoTypeStats $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return GooglePrivacyDlpV2StoredInfoTypeStats
   */
  public function getStats()
  {
    return $this->stats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2StoredInfoTypeVersion::class, 'Google_Service_DLP_GooglePrivacyDlpV2StoredInfoTypeVersion');
