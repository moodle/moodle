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

namespace Google\Service\MigrationCenterAPI;

class AssetFrame extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const COLLECTION_TYPE_SOURCE_TYPE_UNKNOWN = 'SOURCE_TYPE_UNKNOWN';
  /**
   * Manually uploaded file (e.g. CSV)
   */
  public const COLLECTION_TYPE_SOURCE_TYPE_UPLOAD = 'SOURCE_TYPE_UPLOAD';
  /**
   * Guest-level info
   */
  public const COLLECTION_TYPE_SOURCE_TYPE_GUEST_OS_SCAN = 'SOURCE_TYPE_GUEST_OS_SCAN';
  /**
   * Inventory-level scan
   */
  public const COLLECTION_TYPE_SOURCE_TYPE_INVENTORY_SCAN = 'SOURCE_TYPE_INVENTORY_SCAN';
  /**
   * Third-party owned sources.
   */
  public const COLLECTION_TYPE_SOURCE_TYPE_CUSTOM = 'SOURCE_TYPE_CUSTOM';
  /**
   * Discovery clients
   */
  public const COLLECTION_TYPE_SOURCE_TYPE_DISCOVERY_CLIENT = 'SOURCE_TYPE_DISCOVERY_CLIENT';
  protected $collection_key = 'performanceSamples';
  /**
   * Generic asset attributes.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Optional. Frame collection type, if not specified the collection type will
   * be based on the source type of the source the frame was reported on.
   *
   * @var string
   */
  public $collectionType;
  protected $databaseDeploymentDetailsType = DatabaseDeploymentDetails::class;
  protected $databaseDeploymentDetailsDataType = '';
  protected $databaseDetailsType = DatabaseDetails::class;
  protected $databaseDetailsDataType = '';
  /**
   * Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  protected $machineDetailsType = MachineDetails::class;
  protected $machineDetailsDataType = '';
  protected $performanceSamplesType = PerformanceSample::class;
  protected $performanceSamplesDataType = 'array';
  /**
   * The time the data was reported.
   *
   * @var string
   */
  public $reportTime;
  /**
   * Optional. Trace token is optionally provided to assist with debugging and
   * traceability.
   *
   * @var string
   */
  public $traceToken;

  /**
   * Generic asset attributes.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Optional. Frame collection type, if not specified the collection type will
   * be based on the source type of the source the frame was reported on.
   *
   * Accepted values: SOURCE_TYPE_UNKNOWN, SOURCE_TYPE_UPLOAD,
   * SOURCE_TYPE_GUEST_OS_SCAN, SOURCE_TYPE_INVENTORY_SCAN, SOURCE_TYPE_CUSTOM,
   * SOURCE_TYPE_DISCOVERY_CLIENT
   *
   * @param self::COLLECTION_TYPE_* $collectionType
   */
  public function setCollectionType($collectionType)
  {
    $this->collectionType = $collectionType;
  }
  /**
   * @return self::COLLECTION_TYPE_*
   */
  public function getCollectionType()
  {
    return $this->collectionType;
  }
  /**
   * Asset information specific for database deployments.
   *
   * @param DatabaseDeploymentDetails $databaseDeploymentDetails
   */
  public function setDatabaseDeploymentDetails(DatabaseDeploymentDetails $databaseDeploymentDetails)
  {
    $this->databaseDeploymentDetails = $databaseDeploymentDetails;
  }
  /**
   * @return DatabaseDeploymentDetails
   */
  public function getDatabaseDeploymentDetails()
  {
    return $this->databaseDeploymentDetails;
  }
  /**
   * Asset information specific for logical databases.
   *
   * @param DatabaseDetails $databaseDetails
   */
  public function setDatabaseDetails(DatabaseDetails $databaseDetails)
  {
    $this->databaseDetails = $databaseDetails;
  }
  /**
   * @return DatabaseDetails
   */
  public function getDatabaseDetails()
  {
    return $this->databaseDetails;
  }
  /**
   * Labels as key value pairs.
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
   * Asset information specific for virtual machines.
   *
   * @param MachineDetails $machineDetails
   */
  public function setMachineDetails(MachineDetails $machineDetails)
  {
    $this->machineDetails = $machineDetails;
  }
  /**
   * @return MachineDetails
   */
  public function getMachineDetails()
  {
    return $this->machineDetails;
  }
  /**
   * Asset performance data samples. Samples that are from more than 40 days ago
   * or after tomorrow are ignored.
   *
   * @param PerformanceSample[] $performanceSamples
   */
  public function setPerformanceSamples($performanceSamples)
  {
    $this->performanceSamples = $performanceSamples;
  }
  /**
   * @return PerformanceSample[]
   */
  public function getPerformanceSamples()
  {
    return $this->performanceSamples;
  }
  /**
   * The time the data was reported.
   *
   * @param string $reportTime
   */
  public function setReportTime($reportTime)
  {
    $this->reportTime = $reportTime;
  }
  /**
   * @return string
   */
  public function getReportTime()
  {
    return $this->reportTime;
  }
  /**
   * Optional. Trace token is optionally provided to assist with debugging and
   * traceability.
   *
   * @param string $traceToken
   */
  public function setTraceToken($traceToken)
  {
    $this->traceToken = $traceToken;
  }
  /**
   * @return string
   */
  public function getTraceToken()
  {
    return $this->traceToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AssetFrame::class, 'Google_Service_MigrationCenterAPI_AssetFrame');
