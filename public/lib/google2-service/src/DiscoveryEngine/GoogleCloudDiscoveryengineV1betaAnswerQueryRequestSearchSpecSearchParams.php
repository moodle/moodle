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

class GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSearchSpecSearchParams extends \Google\Model
{
  protected $boostSpecType = GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  /**
   * @var string
   */
  public $filter;
  /**
   * @var int
   */
  public $maxReturnResults;
  /**
   * @var string
   */
  public $orderBy;

  /**
   * @param GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec
   */
  public function setBoostSpec(GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1betaSearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * @param string
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * @param int
   */
  public function setMaxReturnResults($maxReturnResults)
  {
    $this->maxReturnResults = $maxReturnResults;
  }
  /**
   * @return int
   */
  public function getMaxReturnResults()
  {
    return $this->maxReturnResults;
  }
  /**
   * @param string
   */
  public function setOrderBy($orderBy)
  {
    $this->orderBy = $orderBy;
  }
  /**
   * @return string
   */
  public function getOrderBy()
  {
    return $this->orderBy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSearchSpecSearchParams::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaAnswerQueryRequestSearchSpecSearchParams');
