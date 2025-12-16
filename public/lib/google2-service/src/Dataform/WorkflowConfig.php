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

namespace Google\Service\Dataform;

class WorkflowConfig extends \Google\Collection
{
  protected $collection_key = 'recentScheduledExecutionRecords';
  /**
   * Output only. The timestamp of when the WorkflowConfig was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Optional schedule (in cron format) for automatic execution of
   * this workflow config.
   *
   * @var string
   */
  public $cronSchedule;
  /**
   * Optional. Disables automatic creation of workflow invocations.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  protected $invocationConfigType = InvocationConfig::class;
  protected $invocationConfigDataType = '';
  /**
   * Identifier. The workflow config's name.
   *
   * @var string
   */
  public $name;
  protected $recentScheduledExecutionRecordsType = ScheduledExecutionRecord::class;
  protected $recentScheduledExecutionRecordsDataType = 'array';
  /**
   * Required. The name of the release config whose release_compilation_result
   * should be executed. Must be in the format
   * `projects/locations/repositories/releaseConfigs`.
   *
   * @var string
   */
  public $releaseConfig;
  /**
   * Optional. Specifies the time zone to be used when interpreting
   * cron_schedule. Must be a time zone name from the time zone database
   * (https://en.wikipedia.org/wiki/List_of_tz_database_time_zones). If left
   * unspecified, the default is UTC.
   *
   * @var string
   */
  public $timeZone;
  /**
   * Output only. The timestamp of when the WorkflowConfig was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The timestamp of when the WorkflowConfig was created.
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
   * Optional. Optional schedule (in cron format) for automatic execution of
   * this workflow config.
   *
   * @param string $cronSchedule
   */
  public function setCronSchedule($cronSchedule)
  {
    $this->cronSchedule = $cronSchedule;
  }
  /**
   * @return string
   */
  public function getCronSchedule()
  {
    return $this->cronSchedule;
  }
  /**
   * Optional. Disables automatic creation of workflow invocations.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @param string $internalMetadata
   */
  public function setInternalMetadata($internalMetadata)
  {
    $this->internalMetadata = $internalMetadata;
  }
  /**
   * @return string
   */
  public function getInternalMetadata()
  {
    return $this->internalMetadata;
  }
  /**
   * Optional. If left unset, a default InvocationConfig will be used.
   *
   * @param InvocationConfig $invocationConfig
   */
  public function setInvocationConfig(InvocationConfig $invocationConfig)
  {
    $this->invocationConfig = $invocationConfig;
  }
  /**
   * @return InvocationConfig
   */
  public function getInvocationConfig()
  {
    return $this->invocationConfig;
  }
  /**
   * Identifier. The workflow config's name.
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
   * Output only. Records of the 10 most recent scheduled execution attempts,
   * ordered in descending order of `execution_time`. Updated whenever automatic
   * creation of a workflow invocation is triggered by cron_schedule.
   *
   * @param ScheduledExecutionRecord[] $recentScheduledExecutionRecords
   */
  public function setRecentScheduledExecutionRecords($recentScheduledExecutionRecords)
  {
    $this->recentScheduledExecutionRecords = $recentScheduledExecutionRecords;
  }
  /**
   * @return ScheduledExecutionRecord[]
   */
  public function getRecentScheduledExecutionRecords()
  {
    return $this->recentScheduledExecutionRecords;
  }
  /**
   * Required. The name of the release config whose release_compilation_result
   * should be executed. Must be in the format
   * `projects/locations/repositories/releaseConfigs`.
   *
   * @param string $releaseConfig
   */
  public function setReleaseConfig($releaseConfig)
  {
    $this->releaseConfig = $releaseConfig;
  }
  /**
   * @return string
   */
  public function getReleaseConfig()
  {
    return $this->releaseConfig;
  }
  /**
   * Optional. Specifies the time zone to be used when interpreting
   * cron_schedule. Must be a time zone name from the time zone database
   * (https://en.wikipedia.org/wiki/List_of_tz_database_time_zones). If left
   * unspecified, the default is UTC.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * Output only. The timestamp of when the WorkflowConfig was last updated.
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
class_alias(WorkflowConfig::class, 'Google_Service_Dataform_WorkflowConfig');
