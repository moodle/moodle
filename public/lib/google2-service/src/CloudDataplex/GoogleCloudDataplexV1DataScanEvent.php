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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataScanEvent extends \Google\Model
{
  /**
   * An unspecified scope type.
   */
  public const SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * Data scan runs on all of the data.
   */
  public const SCOPE_FULL = 'FULL';
  /**
   * Data scan runs on incremental data.
   */
  public const SCOPE_INCREMENTAL = 'INCREMENTAL';
  /**
   * Unspecified job state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Data scan job started.
   */
  public const STATE_STARTED = 'STARTED';
  /**
   * Data scan job successfully completed.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Data scan job was unsuccessful.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Data scan job was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Data scan job was created.
   */
  public const STATE_CREATED = 'CREATED';
  /**
   * An unspecified trigger type.
   */
  public const TRIGGER_TRIGGER_UNSPECIFIED = 'TRIGGER_UNSPECIFIED';
  /**
   * Data scan triggers on demand.
   */
  public const TRIGGER_ON_DEMAND = 'ON_DEMAND';
  /**
   * Data scan triggers as per schedule.
   */
  public const TRIGGER_SCHEDULE = 'SCHEDULE';
  /**
   * Data scan is run one time on creation.
   */
  public const TRIGGER_ONE_TIME = 'ONE_TIME';
  /**
   * An unspecified data scan type.
   */
  public const TYPE_SCAN_TYPE_UNSPECIFIED = 'SCAN_TYPE_UNSPECIFIED';
  /**
   * Data scan for data profile.
   */
  public const TYPE_DATA_PROFILE = 'DATA_PROFILE';
  /**
   * Data scan for data quality.
   */
  public const TYPE_DATA_QUALITY = 'DATA_QUALITY';
  /**
   * Data scan for data discovery.
   */
  public const TYPE_DATA_DISCOVERY = 'DATA_DISCOVERY';
  protected $catalogPublishingStatusType = GoogleCloudDataplexV1DataScanCatalogPublishingStatus::class;
  protected $catalogPublishingStatusDataType = '';
  /**
   * The time when the data scan job was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataProfileType = GoogleCloudDataplexV1DataScanEventDataProfileResult::class;
  protected $dataProfileDataType = '';
  protected $dataProfileConfigsType = GoogleCloudDataplexV1DataScanEventDataProfileAppliedConfigs::class;
  protected $dataProfileConfigsDataType = '';
  protected $dataQualityType = GoogleCloudDataplexV1DataScanEventDataQualityResult::class;
  protected $dataQualityDataType = '';
  protected $dataQualityConfigsType = GoogleCloudDataplexV1DataScanEventDataQualityAppliedConfigs::class;
  protected $dataQualityConfigsDataType = '';
  /**
   * The data source of the data scan
   *
   * @var string
   */
  public $dataSource;
  /**
   * The time when the data scan job finished.
   *
   * @var string
   */
  public $endTime;
  /**
   * The identifier of the specific data scan job this log entry is for.
   *
   * @var string
   */
  public $jobId;
  /**
   * The message describing the data scan job event.
   *
   * @var string
   */
  public $message;
  protected $postScanActionsResultType = GoogleCloudDataplexV1DataScanEventPostScanActionsResult::class;
  protected $postScanActionsResultDataType = '';
  /**
   * The scope of the data scan (e.g. full, incremental).
   *
   * @var string
   */
  public $scope;
  /**
   * A version identifier of the spec which was used to execute this job.
   *
   * @var string
   */
  public $specVersion;
  /**
   * The time when the data scan job started to run.
   *
   * @var string
   */
  public $startTime;
  /**
   * The status of the data scan job.
   *
   * @var string
   */
  public $state;
  /**
   * The trigger type of the data scan job.
   *
   * @var string
   */
  public $trigger;
  /**
   * The type of the data scan.
   *
   * @var string
   */
  public $type;

  /**
   * The status of publishing the data scan as Dataplex Universal Catalog
   * metadata.
   *
   * @param GoogleCloudDataplexV1DataScanCatalogPublishingStatus $catalogPublishingStatus
   */
  public function setCatalogPublishingStatus(GoogleCloudDataplexV1DataScanCatalogPublishingStatus $catalogPublishingStatus)
  {
    $this->catalogPublishingStatus = $catalogPublishingStatus;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanCatalogPublishingStatus
   */
  public function getCatalogPublishingStatus()
  {
    return $this->catalogPublishingStatus;
  }
  /**
   * The time when the data scan job was created.
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
   * Data profile result for data profile type data scan.
   *
   * @param GoogleCloudDataplexV1DataScanEventDataProfileResult $dataProfile
   */
  public function setDataProfile(GoogleCloudDataplexV1DataScanEventDataProfileResult $dataProfile)
  {
    $this->dataProfile = $dataProfile;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanEventDataProfileResult
   */
  public function getDataProfile()
  {
    return $this->dataProfile;
  }
  /**
   * Applied configs for data profile type data scan.
   *
   * @param GoogleCloudDataplexV1DataScanEventDataProfileAppliedConfigs $dataProfileConfigs
   */
  public function setDataProfileConfigs(GoogleCloudDataplexV1DataScanEventDataProfileAppliedConfigs $dataProfileConfigs)
  {
    $this->dataProfileConfigs = $dataProfileConfigs;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanEventDataProfileAppliedConfigs
   */
  public function getDataProfileConfigs()
  {
    return $this->dataProfileConfigs;
  }
  /**
   * Data quality result for data quality type data scan.
   *
   * @param GoogleCloudDataplexV1DataScanEventDataQualityResult $dataQuality
   */
  public function setDataQuality(GoogleCloudDataplexV1DataScanEventDataQualityResult $dataQuality)
  {
    $this->dataQuality = $dataQuality;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanEventDataQualityResult
   */
  public function getDataQuality()
  {
    return $this->dataQuality;
  }
  /**
   * Applied configs for data quality type data scan.
   *
   * @param GoogleCloudDataplexV1DataScanEventDataQualityAppliedConfigs $dataQualityConfigs
   */
  public function setDataQualityConfigs(GoogleCloudDataplexV1DataScanEventDataQualityAppliedConfigs $dataQualityConfigs)
  {
    $this->dataQualityConfigs = $dataQualityConfigs;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanEventDataQualityAppliedConfigs
   */
  public function getDataQualityConfigs()
  {
    return $this->dataQualityConfigs;
  }
  /**
   * The data source of the data scan
   *
   * @param string $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return string
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * The time when the data scan job finished.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The identifier of the specific data scan job this log entry is for.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * The message describing the data scan job event.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The result of post scan actions.
   *
   * @param GoogleCloudDataplexV1DataScanEventPostScanActionsResult $postScanActionsResult
   */
  public function setPostScanActionsResult(GoogleCloudDataplexV1DataScanEventPostScanActionsResult $postScanActionsResult)
  {
    $this->postScanActionsResult = $postScanActionsResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanEventPostScanActionsResult
   */
  public function getPostScanActionsResult()
  {
    return $this->postScanActionsResult;
  }
  /**
   * The scope of the data scan (e.g. full, incremental).
   *
   * Accepted values: SCOPE_UNSPECIFIED, FULL, INCREMENTAL
   *
   * @param self::SCOPE_* $scope
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return self::SCOPE_*
   */
  public function getScope()
  {
    return $this->scope;
  }
  /**
   * A version identifier of the spec which was used to execute this job.
   *
   * @param string $specVersion
   */
  public function setSpecVersion($specVersion)
  {
    $this->specVersion = $specVersion;
  }
  /**
   * @return string
   */
  public function getSpecVersion()
  {
    return $this->specVersion;
  }
  /**
   * The time when the data scan job started to run.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The status of the data scan job.
   *
   * Accepted values: STATE_UNSPECIFIED, STARTED, SUCCEEDED, FAILED, CANCELLED,
   * CREATED
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
   * The trigger type of the data scan job.
   *
   * Accepted values: TRIGGER_UNSPECIFIED, ON_DEMAND, SCHEDULE, ONE_TIME
   *
   * @param self::TRIGGER_* $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return self::TRIGGER_*
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
  /**
   * The type of the data scan.
   *
   * Accepted values: SCAN_TYPE_UNSPECIFIED, DATA_PROFILE, DATA_QUALITY,
   * DATA_DISCOVERY
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataScanEvent::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScanEvent');
