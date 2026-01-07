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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p2beta1ProductSearchResults extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * Timestamp of the index which provided these results. Products added to the
   * product set and products removed from the product set after this time are
   * not reflected in the current results.
   *
   * @var string
   */
  public $indexTime;
  protected $productGroupedResultsType = GoogleCloudVisionV1p2beta1ProductSearchResultsGroupedResult::class;
  protected $productGroupedResultsDataType = 'array';
  protected $resultsType = GoogleCloudVisionV1p2beta1ProductSearchResultsResult::class;
  protected $resultsDataType = 'array';

  /**
   * Timestamp of the index which provided these results. Products added to the
   * product set and products removed from the product set after this time are
   * not reflected in the current results.
   *
   * @param string $indexTime
   */
  public function setIndexTime($indexTime)
  {
    $this->indexTime = $indexTime;
  }
  /**
   * @return string
   */
  public function getIndexTime()
  {
    return $this->indexTime;
  }
  /**
   * List of results grouped by products detected in the query image. Each entry
   * corresponds to one bounding polygon in the query image, and contains the
   * matching products specific to that region. There may be duplicate product
   * matches in the union of all the per-product results.
   *
   * @param GoogleCloudVisionV1p2beta1ProductSearchResultsGroupedResult[] $productGroupedResults
   */
  public function setProductGroupedResults($productGroupedResults)
  {
    $this->productGroupedResults = $productGroupedResults;
  }
  /**
   * @return GoogleCloudVisionV1p2beta1ProductSearchResultsGroupedResult[]
   */
  public function getProductGroupedResults()
  {
    return $this->productGroupedResults;
  }
  /**
   * List of results, one for each product match.
   *
   * @param GoogleCloudVisionV1p2beta1ProductSearchResultsResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudVisionV1p2beta1ProductSearchResultsResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p2beta1ProductSearchResults::class, 'Google_Service_Vision_GoogleCloudVisionV1p2beta1ProductSearchResults');
