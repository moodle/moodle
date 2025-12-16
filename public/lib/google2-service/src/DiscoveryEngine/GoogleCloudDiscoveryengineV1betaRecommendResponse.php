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

class GoogleCloudDiscoveryengineV1betaRecommendResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * @var string
   */
  public $attributionToken;
  /**
   * @var string[]
   */
  public $missingIds;
  protected $resultsType = GoogleCloudDiscoveryengineV1betaRecommendResponseRecommendationResult::class;
  protected $resultsDataType = 'array';
  /**
   * @var bool
   */
  public $validateOnly;

  /**
   * @param string
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
   * @param string[]
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
   * @param GoogleCloudDiscoveryengineV1betaRecommendResponseRecommendationResult[]
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaRecommendResponseRecommendationResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * @param bool
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
class_alias(GoogleCloudDiscoveryengineV1betaRecommendResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaRecommendResponse');
