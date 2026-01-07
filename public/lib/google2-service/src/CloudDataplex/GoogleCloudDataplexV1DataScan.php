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

class GoogleCloudDataplexV1DataScan extends \Google\Model
{
  /**
   * State is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is active, i.e., ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Resource is under creation.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is under deletion.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Resource is active but has unresolved actions.
   */
  public const STATE_ACTION_REQUIRED = 'ACTION_REQUIRED';
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
   * Output only. The time when the scan was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataType = GoogleCloudDataplexV1DataSource::class;
  protected $dataDataType = '';
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
   * Optional. Description of the scan. Must be between 1-1024 characters.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name. Must be between 1-256 characters.
   *
   * @var string
   */
  public $displayName;
  protected $executionSpecType = GoogleCloudDataplexV1DataScanExecutionSpec::class;
  protected $executionSpecDataType = '';
  protected $executionStatusType = GoogleCloudDataplexV1DataScanExecutionStatus::class;
  protected $executionStatusDataType = '';
  /**
   * Optional. User-defined labels for the scan.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The relative resource name of the scan, of the
   * form: projects/{project}/locations/{location_id}/dataScans/{datascan_id},
   * where project refers to a project_id or project_number and location_id
   * refers to a Google Cloud region.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Current state of the DataScan.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The type of DataScan.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. System generated globally unique ID for the scan. This ID will
   * be different if the scan is deleted and re-created with the same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the scan was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the scan was created.
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
   * Required. The data source for DataScan.
   *
   * @param GoogleCloudDataplexV1DataSource $data
   */
  public function setData(GoogleCloudDataplexV1DataSource $data)
  {
    $this->data = $data;
  }
  /**
   * @return GoogleCloudDataplexV1DataSource
   */
  public function getData()
  {
    return $this->data;
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
   * Settings for a data discovery scan.
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
   * Settings for a data documentation scan.
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
   * Settings for a data profile scan.
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
   * Settings for a data quality scan.
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
   * Optional. Description of the scan. Must be between 1-1024 characters.
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
   * Optional. User friendly display name. Must be between 1-256 characters.
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
   * Optional. DataScan execution settings.If not specified, the fields in it
   * will use their default values.
   *
   * @param GoogleCloudDataplexV1DataScanExecutionSpec $executionSpec
   */
  public function setExecutionSpec(GoogleCloudDataplexV1DataScanExecutionSpec $executionSpec)
  {
    $this->executionSpec = $executionSpec;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanExecutionSpec
   */
  public function getExecutionSpec()
  {
    return $this->executionSpec;
  }
  /**
   * Output only. Status of the data scan execution.
   *
   * @param GoogleCloudDataplexV1DataScanExecutionStatus $executionStatus
   */
  public function setExecutionStatus(GoogleCloudDataplexV1DataScanExecutionStatus $executionStatus)
  {
    $this->executionStatus = $executionStatus;
  }
  /**
   * @return GoogleCloudDataplexV1DataScanExecutionStatus
   */
  public function getExecutionStatus()
  {
    return $this->executionStatus;
  }
  /**
   * Optional. User-defined labels for the scan.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Identifier. The relative resource name of the scan, of the
   * form: projects/{project}/locations/{location_id}/dataScans/{datascan_id},
   * where project refers to a project_id or project_number and location_id
   * refers to a Google Cloud region.
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
   * Output only. Current state of the DataScan.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING,
   * ACTION_REQUIRED
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
   * Output only. The type of DataScan.
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
   * Output only. System generated globally unique ID for the scan. This ID will
   * be different if the scan is deleted and re-created with the same name.
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
  /**
   * Output only. The time when the scan was last updated.
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
class_alias(GoogleCloudDataplexV1DataScan::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScan');
