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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2ConversationalSearchRequestSearchParams extends \Google\Model
{
  protected $boostSpecType = GoogleCloudRetailV2SearchRequestBoostSpec::class;
  protected $boostSpecDataType = '';
  /**
   * Optional. The canonical filter string to restrict search results. The
   * syntax of the canonical filter string is the same as
   * SearchRequest.canonical_filter.
   *
   * @var string
   */
  public $canonicalFilter;
  /**
   * Optional. The filter string to restrict search results. The syntax of the
   * filter string is the same as SearchRequest.filter.
   *
   * @var string
   */
  public $filter;
  /**
   * Optional. The sort string to specify the sorting of search results. The
   * syntax of the sort string is the same as SearchRequest.sort.
   *
   * @var string
   */
  public $sortBy;

  /**
   * Optional. The boost spec to specify the boosting of search results. The
   * syntax of the boost spec is the same as SearchRequest.boost_spec.
   *
   * @param GoogleCloudRetailV2SearchRequestBoostSpec $boostSpec
   */
  public function setBoostSpec(GoogleCloudRetailV2SearchRequestBoostSpec $boostSpec)
  {
    $this->boostSpec = $boostSpec;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestBoostSpec
   */
  public function getBoostSpec()
  {
    return $this->boostSpec;
  }
  /**
   * Optional. The canonical filter string to restrict search results. The
   * syntax of the canonical filter string is the same as
   * SearchRequest.canonical_filter.
   *
   * @param string $canonicalFilter
   */
  public function setCanonicalFilter($canonicalFilter)
  {
    $this->canonicalFilter = $canonicalFilter;
  }
  /**
   * @return string
   */
  public function getCanonicalFilter()
  {
    return $this->canonicalFilter;
  }
  /**
   * Optional. The filter string to restrict search results. The syntax of the
   * filter string is the same as SearchRequest.filter.
   *
   * @param string $filter
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
   * Optional. The sort string to specify the sorting of search results. The
   * syntax of the sort string is the same as SearchRequest.sort.
   *
   * @param string $sortBy
   */
  public function setSortBy($sortBy)
  {
    $this->sortBy = $sortBy;
  }
  /**
   * @return string
   */
  public function getSortBy()
  {
    return $this->sortBy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ConversationalSearchRequestSearchParams::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ConversationalSearchRequestSearchParams');
