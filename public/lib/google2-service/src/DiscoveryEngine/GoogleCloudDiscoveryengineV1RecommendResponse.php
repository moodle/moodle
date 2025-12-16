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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1RecommendResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * A unique attribution token. This should be included in the UserEvent logs
   * resulting from this recommendation, which enables accurate attribution of
   * recommendation model performance.
   *
   * @var string
   */
  public $attributionToken;
  /**
   * IDs of documents in the request that were missing from the default Branch
   * associated with the requested ServingConfig.
   *
   * @var string[]
   */
  public $missingIds;
  protected $resultsType = GoogleCloudDiscoveryengineV1RecommendResponseRecommendationResult::class;
  protected $resultsDataType = 'array';
  /**
   * True if RecommendRequest.validate_only was set.
   *
   * @var bool
   */
  public $validateOnly;

  /**
   * A unique attribution token. This should be included in the UserEvent logs
   * resulting from this recommendation, which enables accurate attribution of
   * recommendation model performance.
   *
   * @param string $attributionToken
   */
  public function setAttributionToken($attributionToken)
  {
    $this->attributionToken = $attributionToken;
  }
  /**
   * @return string
   */
  public function getAttributionToken()
  {
    return $this->attributionToken;
  }
  /**
   * IDs of documents in the request that were missing from the default Branch
   * associated with the requested ServingConfig.
   *
   * @param string[] $missingIds
   */
  public function setMissingIds($missingIds)
  {
    $this->missingIds = $missingIds;
  }
  /**
   * @return string[]
   */
  public function getMissingIds()
  {
    return $this->missingIds;
  }
  /**
   * A list of recommended Documents. The order represents the ranking (from the
   * most relevant Document to the least).
   *
   * @param GoogleCloudDiscoveryengineV1RecommendResponseRecommendationResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1RecommendResponseRecommendationResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * True if RecommendRequest.validate_only was set.
   *
   * @param bool $validateOnly
   */
  public function setValidateOnly($validateOnly)
  {
    $this->validateOnly = $validateOnly;
  }
  /**
   * @return bool
   */
  public function getValidateOnly()
  {
    return $this->validateOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1RecommendResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1RecommendResponse');
