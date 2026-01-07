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

namespace Google\Service\RecommendationsAI;

class GoogleCloudRecommendationengineV1beta1PredictResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * True if the dryRun property was set in the request.
   *
   * @var bool
   */
  public $dryRun;
  /**
   * IDs of items in the request that were missing from the catalog.
   *
   * @var string[]
   */
  public $itemsMissingInCatalog;
  /**
   * Additional domain specific prediction response metadata.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * If empty, the list is complete. If nonempty, the token to pass to the next
   * request's PredictRequest.page_token.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * A unique recommendation token. This should be included in the user event
   * logs resulting from this recommendation, which enables accurate attribution
   * of recommendation model performance.
   *
   * @var string
   */
  public $recommendationToken;
  protected $resultsType = GoogleCloudRecommendationengineV1beta1PredictResponsePredictionResult::class;
  protected $resultsDataType = 'array';

  /**
   * True if the dryRun property was set in the request.
   *
   * @param bool $dryRun
   */
  public function setDryRun($dryRun)
  {
    $this->dryRun = $dryRun;
  }
  /**
   * @return bool
   */
  public function getDryRun()
  {
    return $this->dryRun;
  }
  /**
   * IDs of items in the request that were missing from the catalog.
   *
   * @param string[] $itemsMissingInCatalog
   */
  public function setItemsMissingInCatalog($itemsMissingInCatalog)
  {
    $this->itemsMissingInCatalog = $itemsMissingInCatalog;
  }
  /**
   * @return string[]
   */
  public function getItemsMissingInCatalog()
  {
    return $this->itemsMissingInCatalog;
  }
  /**
   * Additional domain specific prediction response metadata.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * If empty, the list is complete. If nonempty, the token to pass to the next
   * request's PredictRequest.page_token.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * A unique recommendation token. This should be included in the user event
   * logs resulting from this recommendation, which enables accurate attribution
   * of recommendation model performance.
   *
   * @param string $recommendationToken
   */
  public function setRecommendationToken($recommendationToken)
  {
    $this->recommendationToken = $recommendationToken;
  }
  /**
   * @return string
   */
  public function getRecommendationToken()
  {
    return $this->recommendationToken;
  }
  /**
   * A list of recommended items. The order represents the ranking (from the
   * most relevant item to the least).
   *
   * @param GoogleCloudRecommendationengineV1beta1PredictResponsePredictionResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudRecommendationengineV1beta1PredictResponsePredictionResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecommendationengineV1beta1PredictResponse::class, 'Google_Service_RecommendationsAI_GoogleCloudRecommendationengineV1beta1PredictResponse');
