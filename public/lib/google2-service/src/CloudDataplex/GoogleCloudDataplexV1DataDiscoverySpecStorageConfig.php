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

class GoogleCloudDataplexV1DataDiscoverySpecStorageConfig extends \Google\Collection
{
  protected $collection_key = 'includePatterns';
  protected $csvOptionsType = GoogleCloudDataplexV1DataDiscoverySpecStorageConfigCsvOptions::class;
  protected $csvOptionsDataType = '';
  /**
   * Optional. Defines the data to exclude during discovery. Provide a list of
   * patterns that identify the data to exclude. For Cloud Storage bucket
   * assets, these patterns are interpreted as glob patterns used to match
   * object names. For BigQuery dataset assets, these patterns are interpreted
   * as patterns to match table names.
   *
   * @var string[]
   */
  public $excludePatterns;
  /**
   * Optional. Defines the data to include during discovery when only a subset
   * of the data should be considered. Provide a list of patterns that identify
   * the data to include. For Cloud Storage bucket assets, these patterns are
   * interpreted as glob patterns used to match object names. For BigQuery
   * dataset assets, these patterns are interpreted as patterns to match table
   * names.
   *
   * @var string[]
   */
  public $includePatterns;
  protected $jsonOptionsType = GoogleCloudDataplexV1DataDiscoverySpecStorageConfigJsonOptions::class;
  protected $jsonOptionsDataType = '';

  /**
   * Optional. Configuration for CSV data.
   *
   * @param GoogleCloudDataplexV1DataDiscoverySpecStorageConfigCsvOptions $csvOptions
   */
  public function setCsvOptions(GoogleCloudDataplexV1DataDiscoverySpecStorageConfigCsvOptions $csvOptions)
  {
    $this->csvOptions = $csvOptions;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoverySpecStorageConfigCsvOptions
   */
  public function getCsvOptions()
  {
    return $this->csvOptions;
  }
  /**
   * Optional. Defines the data to exclude during discovery. Provide a list of
   * patterns that identify the data to exclude. For Cloud Storage bucket
   * assets, these patterns are interpreted as glob patterns used to match
   * object names. For BigQuery dataset assets, these patterns are interpreted
   * as patterns to match table names.
   *
   * @param string[] $excludePatterns
   */
  public function setExcludePatterns($excludePatterns)
  {
    $this->excludePatterns = $excludePatterns;
  }
  /**
   * @return string[]
   */
  public function getExcludePatterns()
  {
    return $this->excludePatterns;
  }
  /**
   * Optional. Defines the data to include during discovery when only a subset
   * of the data should be considered. Provide a list of patterns that identify
   * the data to include. For Cloud Storage bucket assets, these patterns are
   * interpreted as glob patterns used to match object names. For BigQuery
   * dataset assets, these patterns are interpreted as patterns to match table
   * names.
   *
   * @param string[] $includePatterns
   */
  public function setIncludePatterns($includePatterns)
  {
    $this->includePatterns = $includePatterns;
  }
  /**
   * @return string[]
   */
  public function getIncludePatterns()
  {
    return $this->includePatterns;
  }
  /**
   * Optional. Configuration for JSON data.
   *
   * @param GoogleCloudDataplexV1DataDiscoverySpecStorageConfigJsonOptions $jsonOptions
   */
  public function setJsonOptions(GoogleCloudDataplexV1DataDiscoverySpecStorageConfigJsonOptions $jsonOptions)
  {
    $this->jsonOptions = $jsonOptions;
  }
  /**
   * @return GoogleCloudDataplexV1DataDiscoverySpecStorageConfigJsonOptions
   */
  public function getJsonOptions()
  {
    return $this->jsonOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataDiscoverySpecStorageConfig::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataDiscoverySpecStorageConfig');
