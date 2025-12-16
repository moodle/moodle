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

class GoogleCloudDiscoveryengineV1SearchRequestQueryExpansionSpec extends \Google\Model
{
  /**
   * Unspecified query expansion condition. In this case, server behavior
   * defaults to Condition.DISABLED.
   */
  public const CONDITION_CONDITION_UNSPECIFIED = 'CONDITION_UNSPECIFIED';
  /**
   * Disabled query expansion. Only the exact search query is used, even if
   * SearchResponse.total_size is zero.
   */
  public const CONDITION_DISABLED = 'DISABLED';
  /**
   * Automatic query expansion built by the Search API.
   */
  public const CONDITION_AUTO = 'AUTO';
  /**
   * The condition under which query expansion should occur. Default to
   * Condition.DISABLED.
   *
   * @var string
   */
  public $condition;
  /**
   * Whether to pin unexpanded results. If this field is set to true, unexpanded
   * products are always at the top of the search results, followed by the
   * expanded results.
   *
   * @var bool
   */
  public $pinUnexpandedResults;

  /**
   * The condition under which query expansion should occur. Default to
   * Condition.DISABLED.
   *
   * Accepted values: CONDITION_UNSPECIFIED, DISABLED, AUTO
   *
   * @param self::CONDITION_* $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return self::CONDITION_*
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Whether to pin unexpanded results. If this field is set to true, unexpanded
   * products are always at the top of the search results, followed by the
   * expanded results.
   *
   * @param bool $pinUnexpandedResults
   */
  public function setPinUnexpandedResults($pinUnexpandedResults)
  {
    $this->pinUnexpandedResults = $pinUnexpandedResults;
  }
  /**
   * @return bool
   */
  public function getPinUnexpandedResults()
  {
    return $this->pinUnexpandedResults;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestQueryExpansionSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestQueryExpansionSpec');
