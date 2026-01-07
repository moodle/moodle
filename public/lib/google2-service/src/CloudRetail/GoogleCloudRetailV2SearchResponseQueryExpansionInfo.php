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

class GoogleCloudRetailV2SearchResponseQueryExpansionInfo extends \Google\Model
{
  /**
   * Bool describing whether query expansion has occurred.
   *
   * @var bool
   */
  public $expandedQuery;
  /**
   * Number of pinned results. This field will only be set when expansion
   * happens and SearchRequest.QueryExpansionSpec.pin_unexpanded_results is set
   * to true.
   *
   * @var string
   */
  public $pinnedResultCount;

  /**
   * Bool describing whether query expansion has occurred.
   *
   * @param bool $expandedQuery
   */
  public function setExpandedQuery($expandedQuery)
  {
    $this->expandedQuery = $expandedQuery;
  }
  /**
   * @return bool
   */
  public function getExpandedQuery()
  {
    return $this->expandedQuery;
  }
  /**
   * Number of pinned results. This field will only be set when expansion
   * happens and SearchRequest.QueryExpansionSpec.pin_unexpanded_results is set
   * to true.
   *
   * @param string $pinnedResultCount
   */
  public function setPinnedResultCount($pinnedResultCount)
  {
    $this->pinnedResultCount = $pinnedResultCount;
  }
  /**
   * @return string
   */
  public function getPinnedResultCount()
  {
    return $this->pinnedResultCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchResponseQueryExpansionInfo::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchResponseQueryExpansionInfo');
