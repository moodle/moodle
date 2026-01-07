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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickAnswerSuggestedQueryCategory extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const CATEGORY_UNKNOWN = 'UNKNOWN';
  /**
   * Calendar based queries (e.g. "my agenda for tomorrow").
   */
  public const CATEGORY_CALENDAR = 'CALENDAR';
  /**
   * Document based queries (e.g. "files shared with me").
   */
  public const CATEGORY_DOCUMENT = 'DOCUMENT';
  /**
   * People based queries (e.g. "what is x's email address?").
   */
  public const CATEGORY_PEOPLE = 'PEOPLE';
  protected $collection_key = 'query';
  /**
   * The query list category.
   *
   * @var string
   */
  public $category;
  /**
   * Whether this category is enabled.
   *
   * @var bool
   */
  public $isEnabled;
  /**
   * List of suggested queries to show the user.
   *
   * @var string[]
   */
  public $query;

  /**
   * The query list category.
   *
   * Accepted values: UNKNOWN, CALENDAR, DOCUMENT, PEOPLE
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Whether this category is enabled.
   *
   * @param bool $isEnabled
   */
  public function setIsEnabled($isEnabled)
  {
    $this->isEnabled = $isEnabled;
  }
  /**
   * @return bool
   */
  public function getIsEnabled()
  {
    return $this->isEnabled;
  }
  /**
   * List of suggested queries to show the user.
   *
   * @param string[] $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string[]
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickAnswerSuggestedQueryCategory::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickAnswerSuggestedQueryCategory');
