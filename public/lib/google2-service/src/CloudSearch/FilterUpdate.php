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

class FilterUpdate extends \Google\Model
{
  protected $filterCreatedType = FilterCreated::class;
  protected $filterCreatedDataType = '';
  protected $filterDeletedType = FilterDeleted::class;
  protected $filterDeletedDataType = '';
  /**
   * @var string
   */
  public $filterId;

  /**
   * @param FilterCreated
   */
  public function setFilterCreated(FilterCreated $filterCreated)
  {
    $this->filterCreated = $filterCreated;
  }
  /**
   * @return FilterCreated
   */
  public function getFilterCreated()
  {
    return $this->filterCreated;
  }
  /**
   * @param FilterDeleted
   */
  public function setFilterDeleted(FilterDeleted $filterDeleted)
  {
    $this->filterDeleted = $filterDeleted;
  }
  /**
   * @return FilterDeleted
   */
  public function getFilterDeleted()
  {
    return $this->filterDeleted;
  }
  /**
   * @param string
   */
  public function setFilterId($filterId)
  {
    $this->filterId = $filterId;
  }
  /**
   * @return string
   */
  public function getFilterId()
  {
    return $this->filterId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilterUpdate::class, 'Google_Service_CloudSearch_FilterUpdate');
