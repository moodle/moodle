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

class GoogleCloudDataplexV1DataQualitySpec extends \Google\Collection
{
  protected $collection_key = 'rules';
  /**
   * Optional. If set, the latest DataScan job result will be published as
   * Dataplex Universal Catalog metadata.
   *
   * @var bool
   */
  public $catalogPublishingEnabled;
  protected $postScanActionsType = GoogleCloudDataplexV1DataQualitySpecPostScanActions::class;
  protected $postScanActionsDataType = '';
  /**
   * Optional. A filter applied to all rows in a single DataScan job. The filter
   * needs to be a valid SQL expression for a WHERE clause in GoogleSQL syntax
   * (https://cloud.google.com/bigquery/docs/reference/standard-sql/query-
   * syntax#where_clause).Example: col1 >= 0 AND col2 < 10
   *
   * @var string
   */
  public $rowFilter;
  protected $rulesType = GoogleCloudDataplexV1DataQualityRule::class;
  protected $rulesDataType = 'array';
  /**
   * Optional. The percentage of the records to be selected from the dataset for
   * DataScan. Value can range between 0.0 and 100.0 with up to 3 significant
   * decimal digits. Sampling is not applied if sampling_percent is not
   * specified, 0 or 100.
   *
   * @var float
   */
  public $samplingPercent;

  /**
   * Optional. If set, the latest DataScan job result will be published as
   * Dataplex Universal Catalog metadata.
   *
   * @param bool $catalogPublishingEnabled
   */
  public function setCatalogPublishingEnabled($catalogPublishingEnabled)
  {
    $this->catalogPublishingEnabled = $catalogPublishingEnabled;
  }
  /**
   * @return bool
   */
  public function getCatalogPublishingEnabled()
  {
    return $this->catalogPublishingEnabled;
  }
  /**
   * Optional. Actions to take upon job completion.
   *
   * @param GoogleCloudDataplexV1DataQualitySpecPostScanActions $postScanActions
   */
  public function setPostScanActions(GoogleCloudDataplexV1DataQualitySpecPostScanActions $postScanActions)
  {
    $this->postScanActions = $postScanActions;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualitySpecPostScanActions
   */
  public function getPostScanActions()
  {
    return $this->postScanActions;
  }
  /**
   * Optional. A filter applied to all rows in a single DataScan job. The filter
   * needs to be a valid SQL expression for a WHERE clause in GoogleSQL syntax
   * (https://cloud.google.com/bigquery/docs/reference/standard-sql/query-
   * syntax#where_clause).Example: col1 >= 0 AND col2 < 10
   *
   * @param string $rowFilter
   */
  public function setRowFilter($rowFilter)
  {
    $this->rowFilter = $rowFilter;
  }
  /**
   * @return string
   */
  public function getRowFilter()
  {
    return $this->rowFilter;
  }
  /**
   * Required. The list of rules to evaluate against a data source. At least one
   * rule is required.
   *
   * @param GoogleCloudDataplexV1DataQualityRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return GoogleCloudDataplexV1DataQualityRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Optional. The percentage of the records to be selected from the dataset for
   * DataScan. Value can range between 0.0 and 100.0 with up to 3 significant
   * decimal digits. Sampling is not applied if sampling_percent is not
   * specified, 0 or 100.
   *
   * @param float $samplingPercent
   */
  public function setSamplingPercent($samplingPercent)
  {
    $this->samplingPercent = $samplingPercent;
  }
  /**
   * @return float
   */
  public function getSamplingPercent()
  {
    return $this->samplingPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataQualitySpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataQualitySpec');
