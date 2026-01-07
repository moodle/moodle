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

namespace Google\Service\Logging;

class LogBucket extends \Google\Collection
{
  /**
   * Unspecified state. This is only used/useful for distinguishing unset
   * values.
   */
  public const LIFECYCLE_STATE_LIFECYCLE_STATE_UNSPECIFIED = 'LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * The normal and active state.
   */
  public const LIFECYCLE_STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource has been marked for deletion by the user. For some resources
   * (e.g. buckets), this can be reversed by an un-delete operation.
   */
  public const LIFECYCLE_STATE_DELETE_REQUESTED = 'DELETE_REQUESTED';
  /**
   * The resource has been marked for an update by the user. It will remain in
   * this state until the update is complete.
   */
  public const LIFECYCLE_STATE_UPDATING = 'UPDATING';
  /**
   * The resource has been marked for creation by the user. It will remain in
   * this state until the creation is complete.
   */
  public const LIFECYCLE_STATE_CREATING = 'CREATING';
  /**
   * The resource is in an INTERNAL error state.
   */
  public const LIFECYCLE_STATE_FAILED = 'FAILED';
  protected $collection_key = 'restrictedFields';
  /**
   * Optional. Whether log analytics is enabled for this bucket.Once enabled,
   * log analytics features cannot be disabled.
   *
   * @var bool
   */
  public $analyticsEnabled;
  protected $cmekSettingsType = CmekSettings::class;
  protected $cmekSettingsDataType = '';
  /**
   * Output only. The creation timestamp of the bucket. This is not set for any
   * of the default buckets.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Describes this bucket.
   *
   * @var string
   */
  public $description;
  protected $indexConfigsType = IndexConfig::class;
  protected $indexConfigsDataType = 'array';
  /**
   * Output only. The bucket lifecycle state.
   *
   * @var string
   */
  public $lifecycleState;
  /**
   * Optional. Whether the bucket is locked.The retention period on a locked
   * bucket cannot be changed. Locked buckets may only be deleted if they are
   * empty.
   *
   * @var bool
   */
  public $locked;
  /**
   * Output only. The resource name of the bucket.For example:projects/my-
   * project/locations/global/buckets/my-bucketFor a list of supported
   * locations, see Supported Regions
   * (https://cloud.google.com/logging/docs/region-support)For the location of
   * global it is unspecified where log entries are actually stored.After a
   * bucket has been created, the location cannot be changed.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Log entry field paths that are denied access in this bucket.The
   * following fields and their children are eligible: textPayload, jsonPayload,
   * protoPayload, httpRequest, labels, sourceLocation.Restricting a repeated
   * field will restrict all values. Adding a parent will block all child
   * fields. (e.g. foo.bar will block foo.bar.baz)
   *
   * @var string[]
   */
  public $restrictedFields;
  /**
   * Optional. Logs will be retained by default for this amount of time, after
   * which they will automatically be deleted. The minimum retention period is 1
   * day. If this value is set to zero at bucket creation time, the default time
   * of 30 days will be used.
   *
   * @var int
   */
  public $retentionDays;
  /**
   * Output only. The last update timestamp of the bucket.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Whether log analytics is enabled for this bucket.Once enabled,
   * log analytics features cannot be disabled.
   *
   * @param bool $analyticsEnabled
   */
  public function setAnalyticsEnabled($analyticsEnabled)
  {
    $this->analyticsEnabled = $analyticsEnabled;
  }
  /**
   * @return bool
   */
  public function getAnalyticsEnabled()
  {
    return $this->analyticsEnabled;
  }
  /**
   * Optional. The CMEK settings of the log bucket. If present, new log entries
   * written to this log bucket are encrypted using the CMEK key provided in
   * this configuration. If a log bucket has CMEK settings, the CMEK settings
   * cannot be disabled later by updating the log bucket. Changing the KMS key
   * is allowed.
   *
   * @param CmekSettings $cmekSettings
   */
  public function setCmekSettings(CmekSettings $cmekSettings)
  {
    $this->cmekSettings = $cmekSettings;
  }
  /**
   * @return CmekSettings
   */
  public function getCmekSettings()
  {
    return $this->cmekSettings;
  }
  /**
   * Output only. The creation timestamp of the bucket. This is not set for any
   * of the default buckets.
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
   * Optional. Describes this bucket.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. A list of indexed fields and related configuration data.
   *
   * @param IndexConfig[] $indexConfigs
   */
  public function setIndexConfigs($indexConfigs)
  {
    $this->indexConfigs = $indexConfigs;
  }
  /**
   * @return IndexConfig[]
   */
  public function getIndexConfigs()
  {
    return $this->indexConfigs;
  }
  /**
   * Output only. The bucket lifecycle state.
   *
   * Accepted values: LIFECYCLE_STATE_UNSPECIFIED, ACTIVE, DELETE_REQUESTED,
   * UPDATING, CREATING, FAILED
   *
   * @param self::LIFECYCLE_STATE_* $lifecycleState
   */
  public function setLifecycleState($lifecycleState)
  {
    $this->lifecycleState = $lifecycleState;
  }
  /**
   * @return self::LIFECYCLE_STATE_*
   */
  public function getLifecycleState()
  {
    return $this->lifecycleState;
  }
  /**
   * Optional. Whether the bucket is locked.The retention period on a locked
   * bucket cannot be changed. Locked buckets may only be deleted if they are
   * empty.
   *
   * @param bool $locked
   */
  public function setLocked($locked)
  {
    $this->locked = $locked;
  }
  /**
   * @return bool
   */
  public function getLocked()
  {
    return $this->locked;
  }
  /**
   * Output only. The resource name of the bucket.For example:projects/my-
   * project/locations/global/buckets/my-bucketFor a list of supported
   * locations, see Supported Regions
   * (https://cloud.google.com/logging/docs/region-support)For the location of
   * global it is unspecified where log entries are actually stored.After a
   * bucket has been created, the location cannot be changed.
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
   * Optional. Log entry field paths that are denied access in this bucket.The
   * following fields and their children are eligible: textPayload, jsonPayload,
   * protoPayload, httpRequest, labels, sourceLocation.Restricting a repeated
   * field will restrict all values. Adding a parent will block all child
   * fields. (e.g. foo.bar will block foo.bar.baz)
   *
   * @param string[] $restrictedFields
   */
  public function setRestrictedFields($restrictedFields)
  {
    $this->restrictedFields = $restrictedFields;
  }
  /**
   * @return string[]
   */
  public function getRestrictedFields()
  {
    return $this->restrictedFields;
  }
  /**
   * Optional. Logs will be retained by default for this amount of time, after
   * which they will automatically be deleted. The minimum retention period is 1
   * day. If this value is set to zero at bucket creation time, the default time
   * of 30 days will be used.
   *
   * @param int $retentionDays
   */
  public function setRetentionDays($retentionDays)
  {
    $this->retentionDays = $retentionDays;
  }
  /**
   * @return int
   */
  public function getRetentionDays()
  {
    return $this->retentionDays;
  }
  /**
   * Output only. The last update timestamp of the bucket.
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
class_alias(LogBucket::class, 'Google_Service_Logging_LogBucket');
