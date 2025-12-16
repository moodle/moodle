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

namespace Google\Service\Vault;

class CountArtifactsRequest extends \Google\Model
{
  /**
   * Default. Same as **TOTAL_COUNT**.
   */
  public const VIEW_COUNT_RESULT_VIEW_UNSPECIFIED = 'COUNT_RESULT_VIEW_UNSPECIFIED';
  /**
   * Response includes counts of the total accounts, queried accounts, matching
   * accounts, non-queryable accounts, and queried account errors.
   */
  public const VIEW_TOTAL_COUNT = 'TOTAL_COUNT';
  /**
   * Response includes the same details as **TOTAL_COUNT**, plus additional
   * account breakdown.
   */
  public const VIEW_ALL = 'ALL';
  protected $queryType = Query::class;
  protected $queryDataType = '';
  /**
   * Sets the granularity of the count results.
   *
   * @var string
   */
  public $view;

  /**
   * The search query.
   *
   * @param Query $query
   */
  public function setQuery(Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Sets the granularity of the count results.
   *
   * Accepted values: COUNT_RESULT_VIEW_UNSPECIFIED, TOTAL_COUNT, ALL
   *
   * @param self::VIEW_* $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return self::VIEW_*
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CountArtifactsRequest::class, 'Google_Service_Vault_CountArtifactsRequest');
