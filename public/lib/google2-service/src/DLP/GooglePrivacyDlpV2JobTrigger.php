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

class GooglePrivacyDlpV2JobTrigger extends \Google\Collection
{
  /**
   * Unused.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Trigger is healthy.
   */
  public const STATUS_HEALTHY = 'HEALTHY';
  /**
   * Trigger is temporarily paused.
   */
  public const STATUS_PAUSED = 'PAUSED';
  /**
   * Trigger is cancelled and can not be resumed.
   */
  public const STATUS_CANCELLED = 'CANCELLED';
  protected $collection_key = 'triggers';
  /**
   * Output only. The creation timestamp of a triggeredJob.
   *
   * @var string
   */
  public $createTime;
  /**
   * User provided description (max 256 chars)
   *
   * @var string
   */
  public $description;
  /**
   * Display name (max 100 chars)
   *
   * @var string
   */
  public $displayName;
  protected $errorsType = GooglePrivacyDlpV2Error::class;
  protected $errorsDataType = 'array';
  protected $inspectJobType = GooglePrivacyDlpV2InspectJobConfig::class;
  protected $inspectJobDataType = '';
  /**
   * Output only. The timestamp of the last time this trigger executed.
   *
   * @var string
   */
  public $lastRunTime;
  /**
   * Unique resource name for the triggeredJob, assigned by the service when the
   * triggeredJob is created, for example `projects/dlp-test-
   * project/jobTriggers/53234423`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. A status for this trigger.
   *
   * @var string
   */
  public $status;
  protected $triggersType = GooglePrivacyDlpV2Trigger::class;
  protected $triggersDataType = 'array';
  /**
   * Output only. The last update timestamp of a triggeredJob.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The creation timestamp of a triggeredJob.
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
   * User provided description (max 256 chars)
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
   * Display name (max 100 chars)
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
   * Output only. A stream of errors encountered when the trigger was activated.
   * Repeated errors may result in the JobTrigger automatically being paused.
   * Will return the last 100 errors. Whenever the JobTrigger is modified this
   * list will be cleared.
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
   * For inspect jobs, a snapshot of the configuration.
   *
   * @param GooglePrivacyDlpV2InspectJobConfig $inspectJob
   */
  public function setInspectJob(GooglePrivacyDlpV2InspectJobConfig $inspectJob)
  {
    $this->inspectJob = $inspectJob;
  }
  /**
   * @return GooglePrivacyDlpV2InspectJobConfig
   */
  public function getInspectJob()
  {
    return $this->inspectJob;
  }
  /**
   * Output only. The timestamp of the last time this trigger executed.
   *
   * @param string $lastRunTime
   */
  public function setLastRunTime($lastRunTime)
  {
    $this->lastRunTime = $lastRunTime;
  }
  /**
   * @return string
   */
  public function getLastRunTime()
  {
    return $this->lastRunTime;
  }
  /**
   * Unique resource name for the triggeredJob, assigned by the service when the
   * triggeredJob is created, for example `projects/dlp-test-
   * project/jobTriggers/53234423`.
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
   * Required. A status for this trigger.
   *
   * Accepted values: STATUS_UNSPECIFIED, HEALTHY, PAUSED, CANCELLED
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
   * A list of triggers which will be OR'ed together. Only one in the list needs
   * to trigger for a job to be started. The list may contain only a single
   * Schedule trigger and must have at least one object.
   *
   * @param GooglePrivacyDlpV2Trigger[] $triggers
   */
  public function setTriggers($triggers)
  {
    $this->triggers = $triggers;
  }
  /**
   * @return GooglePrivacyDlpV2Trigger[]
   */
  public function getTriggers()
  {
    return $this->triggers;
  }
  /**
   * Output only. The last update timestamp of a triggeredJob.
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
class_alias(GooglePrivacyDlpV2JobTrigger::class, 'Google_Service_DLP_GooglePrivacyDlpV2JobTrigger');
