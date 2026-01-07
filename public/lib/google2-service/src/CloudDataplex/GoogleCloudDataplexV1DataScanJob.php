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

class GoogleCloudDataplexV1DataScanJob extends \Google\Model
{
  /**
   * The DataScanJob state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The DataScanJob is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The DataScanJob is canceling.
   */
  public const STATE_CANCELING = 'CANCELING';
  /**
   * The DataScanJob cancellation was successful.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The DataScanJob completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The DataScanJob is no longer running due to an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The DataScanJob has been created but not started to run yet.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The data scan type is unspecified.
   */
  public const TYPE_DATA_SCAN_TYPE_UNSPECIFIED = 'DATA_SCAN_TYPE_UNSPECIFIED';
  /**
   * Data quality scan.
   */
  public const TYPE_DATA_QUALITY = 'DATA_QUALITY';
  /**
   * Data profile scan.
   */
  public const TYPE_DATA_PROFILE = 'DATA_PROFILE';
  /**
   * Data discovery scan.
   */
  public const TYPE_DATA_DISCOVERY = 'DATA_DISCOVERY';
  /**
   * Data documentation scan.
   */
  public const TYPE_DATA_DOCUMENTATION = 'DATA_DOCUMENTATION';
  /**
   * Output only. The time when the DataScanJob was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataDiscoveryResultType = GoogleCloudDataplexV1DataDiscoveryResult::class;
  protected $dataDiscoveryResultDataType = '';
  protected $dataDiscoverySpecType = GoogleCloudDataplexV1DataDiscoverySpec::class;
  protected $dataDiscoverySpecDataType = '';
  protected $dataDocumentationResultType = GoogleCloudDataplexV1DataDocumentationResult::class;
  protected $dataDocumentationResultDataType = '';
  protected $dataDocumentationSpecType = GoogleCloudDataplexV1DataDocumentationSpec::class;
  protected $dataDocumentationSpecDataType = '';
  protected $dataProfileResultType = GoogleCloudDataplexV1DataProfileResult::class;
  protected $dataProfileResultDataType = '';
  protected $dataProfileSpecType = GoogleCloudDataplexV1DataProfileSpec::class;
  protected $dataProfileSpecDataType = '';
  protected $dataQualityResultType = GoogleCloudDataplexV1DataQualityResult::class;
  protected $dataQualityResultDataType = '';
  protected $dataQualitySpecType = GoogleCloudDataplexV1DataQualitySpec::class;
  protected $dataQualitySpecDataType = '';
  /**
   * Output only. The time when the DataScanJob ended.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. Additional information about the current state.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. Identifier. The relative resource name of the DataScanJob, of
   * the form: projects/{project}/locations/{location_id}/dataScans/{datascan_id
   * }/jobs/{job_id}, where project refers to a project_id or project_number and
   * location_id refers to a Google Cloud region.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the DataScanJob was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. Execution state for the DataScanJob.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The type of the parent DataScan.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. System generated globally unique ID for the DataScanJob.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. The time when the DataScanJob was created.
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
   * Output only. The result of a data discovery scan.
   *
   * @param GoogleCloudDataplexV1DataDiscoveryResult $dataDiscoveryResult
   */
  public function setDataDiscoveryResult(GoogleCloudDataplexV1DataDiscoveryResult $dataDiscoveryResult)
  {
    $this->dataDiscoveryResult = $dataDiscoveryResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoveryResult
   */
  public function getDataDiscoveryResult()
  {
    return $this->dataDiscoveryResult;
  }
  /**
   * Output only. Settings for a data discovery scan.
   *
   * @param GoogleCloudDataplexV1DataDiscoverySpec $dataDiscoverySpec
   */
  public function setDataDiscoverySpec(GoogleCloudDataplexV1DataDiscoverySpec $dataDiscoverySpec)
  {
    $this->dataDiscoverySpec = $dataDiscoverySpec;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoverySpec
   */
  public function getDataDiscoverySpec()
  {
    return $this->dataDiscoverySpec;
  }
  /**
   * Output only. The result of a data documentation scan.
   *
   * @param GoogleCloudDataplexV1DataDocumentationResult $dataDocumentationResult
   */
  public function setDataDocumentationResult(GoogleCloudDataplexV1DataDocumentationResult $dataDocumentationResult)
  {
    $this->dataDocumentationResult = $dataDocumentationResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataDocumentationResult
   */
  public function getDataDocumentationResult()
  {
    return $this->dataDocumentationResult;
  }
  /**
   * Output only. Settings for a data documentation scan.
   *
   * @param GoogleCloudDataplexV1DataDocumentationSpec $dataDocumentationSpec
   */
  public function setDataDocumentationSpec(GoogleCloudDataplexV1DataDocumentationSpec $dataDocumentationSpec)
  {
    $this->dataDocumentationSpec = $dataDocumentationSpec;
  }
  /**
   * @return GoogleCloudDataplexV1DataDocumentationSpec
   */
  public function getDataDocumentationSpec()
  {
    return $this->dataDocumentationSpec;
  }
  /**
   * Output only. The result of a data profile scan.
   *
   * @param GoogleCloudDataplexV1DataProfileResult $dataProfileResult
   */
  public function setDataProfileResult(GoogleCloudDataplexV1DataProfileResult $dataProfileResult)
  {
    $this->dataProfileResult = $dataProfileResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileResult
   */
  public function getDataProfileResult()
  {
    return $this->dataProfileResult;
  }
  /**
   * Output only. Settings for a data profile scan.
   *
   * @param GoogleCloudDataplexV1DataProfileSpec $dataProfileSpec
   */
  public function setDataProfileSpec(GoogleCloudDataplexV1DataProfileSpec $dataProfileSpec)
  {
    $this->dataProfileSpec = $dataProfileSpec;
  }
  /**
   * @return GoogleCloudDataplexV1DataProfileSpec
   */
  public function getDataProfileSpec()
  {
    return $this->dataProfileSpec;
  }
  /**
   * Output only. The result of a data quality scan.
   *
   * @param GoogleCloudDataplexV1DataQualityResult $dataQualityResult
   */
  public function setDataQualityResult(GoogleCloudDataplexV1DataQualityResult $dataQualityResult)
  {
    $this->dataQualityResult = $dataQualityResult;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityResult
   */
  public function getDataQualityResult()
  {
    return $this->dataQualityResult;
  }
  /**
   * Output only. Settings for a data quality scan.
   *
   * @param GoogleCloudDataplexV1DataQualitySpec $dataQualitySpec
   */
  public function setDataQualitySpec(GoogleCloudDataplexV1DataQualitySpec $dataQualitySpec)
  {
    $this->dataQualitySpec = $dataQualitySpec;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualitySpec
   */
  public function getDataQualitySpec()
  {
    return $this->dataQualitySpec;
  }
  /**
   * Output only. The time when the DataScanJob ended.
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
   * Output only. Additional information about the current state.
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
   * Output only. Identifier. The relative resource name of the DataScanJob, of
   * the form: projects/{project}/locations/{location_id}/dataScans/{datascan_id
   * }/jobs/{job_id}, where project refers to a project_id or project_number and
   * location_id refers to a Google Cloud region.
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
   * Output only. The time when the DataScanJob was started.
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
   * Output only. Execution state for the DataScanJob.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, CANCELING, CANCELLED,
   * SUCCEEDED, FAILED, PENDING
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
   * Output only. The type of the parent DataScan.
   *
   * Accepted values: DATA_SCAN_TYPE_UNSPECIFIED, DATA_QUALITY, DATA_PROFILE,
   * DATA_DISCOVERY, DATA_DOCUMENTATION
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
  /**
   * Output only. System generated globally unique ID for the DataScanJob.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataScanJob::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScanJob');
