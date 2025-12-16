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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta3Dataset extends \Google\Model
{
  /**
   * Default unspecified enum, should not be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Dataset has not been initialized.
   */
  public const STATE_UNINITIALIZED = 'UNINITIALIZED';
  /**
   * Dataset is being initialized.
   */
  public const STATE_INITIALIZING = 'INITIALIZING';
  /**
   * Dataset has been initialized.
   */
  public const STATE_INITIALIZED = 'INITIALIZED';
  protected $documentWarehouseConfigType = GoogleCloudDocumentaiV1beta3DatasetDocumentWarehouseConfig::class;
  protected $documentWarehouseConfigDataType = '';
  protected $gcsManagedConfigType = GoogleCloudDocumentaiV1beta3DatasetGCSManagedConfig::class;
  protected $gcsManagedConfigDataType = '';
  /**
   * Dataset resource name. Format:
   * `projects/{project}/locations/{location}/processors/{processor}/dataset`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $spannerIndexingConfigType = GoogleCloudDocumentaiV1beta3DatasetSpannerIndexingConfig::class;
  protected $spannerIndexingConfigDataType = '';
  /**
   * Required. State of the dataset. Ignored when updating dataset.
   *
   * @var string
   */
  public $state;
  protected $unmanagedDatasetConfigType = GoogleCloudDocumentaiV1beta3DatasetUnmanagedDatasetConfig::class;
  protected $unmanagedDatasetConfigDataType = '';

  /**
   * Optional. Deprecated. Warehouse-based dataset configuration is not
   * supported.
   *
   * @deprecated
   * @param GoogleCloudDocumentaiV1beta3DatasetDocumentWarehouseConfig $documentWarehouseConfig
   */
  public function setDocumentWarehouseConfig(GoogleCloudDocumentaiV1beta3DatasetDocumentWarehouseConfig $documentWarehouseConfig)
  {
    $this->documentWarehouseConfig = $documentWarehouseConfig;
  }
  /**
   * @deprecated
   * @return GoogleCloudDocumentaiV1beta3DatasetDocumentWarehouseConfig
   */
  public function getDocumentWarehouseConfig()
  {
    return $this->documentWarehouseConfig;
  }
  /**
   * Optional. User-managed Cloud Storage dataset configuration. Use this
   * configuration if the dataset documents are stored under a user-managed
   * Cloud Storage location.
   *
   * @param GoogleCloudDocumentaiV1beta3DatasetGCSManagedConfig $gcsManagedConfig
   */
  public function setGcsManagedConfig(GoogleCloudDocumentaiV1beta3DatasetGCSManagedConfig $gcsManagedConfig)
  {
    $this->gcsManagedConfig = $gcsManagedConfig;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3DatasetGCSManagedConfig
   */
  public function getGcsManagedConfig()
  {
    return $this->gcsManagedConfig;
  }
  /**
   * Dataset resource name. Format:
   * `projects/{project}/locations/{location}/processors/{processor}/dataset`
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
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Optional. A lightweight indexing source with low latency and high
   * reliability, but lacking advanced features like CMEK and content-based
   * search.
   *
   * @param GoogleCloudDocumentaiV1beta3DatasetSpannerIndexingConfig $spannerIndexingConfig
   */
  public function setSpannerIndexingConfig(GoogleCloudDocumentaiV1beta3DatasetSpannerIndexingConfig $spannerIndexingConfig)
  {
    $this->spannerIndexingConfig = $spannerIndexingConfig;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3DatasetSpannerIndexingConfig
   */
  public function getSpannerIndexingConfig()
  {
    return $this->spannerIndexingConfig;
  }
  /**
   * Required. State of the dataset. Ignored when updating dataset.
   *
   * Accepted values: STATE_UNSPECIFIED, UNINITIALIZED, INITIALIZING,
   * INITIALIZED
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
   * Optional. Unmanaged dataset configuration. Use this configuration if the
   * dataset documents are managed by the document service internally (not user-
   * managed).
   *
   * @param GoogleCloudDocumentaiV1beta3DatasetUnmanagedDatasetConfig $unmanagedDatasetConfig
   */
  public function setUnmanagedDatasetConfig(GoogleCloudDocumentaiV1beta3DatasetUnmanagedDatasetConfig $unmanagedDatasetConfig)
  {
    $this->unmanagedDatasetConfig = $unmanagedDatasetConfig;
  }
  /**
   * @return GoogleCloudDocumentaiV1beta3DatasetUnmanagedDatasetConfig
   */
  public function getUnmanagedDatasetConfig()
  {
    return $this->unmanagedDatasetConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3Dataset::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3Dataset');
