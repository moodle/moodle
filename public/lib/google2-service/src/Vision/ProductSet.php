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

namespace Google\Service\Vision;

class ProductSet extends \Google\Model
{
  /**
   * The user-provided name for this ProductSet. Must not be empty. Must be at
   * most 4096 characters long.
   *
   * @var string
   */
  public $displayName;
  protected $indexErrorType = Status::class;
  protected $indexErrorDataType = '';
  /**
   * Output only. The time at which this ProductSet was last indexed. Query
   * results will reflect all updates before this time. If this ProductSet has
   * never been indexed, this timestamp is the default value
   * "1970-01-01T00:00:00Z". This field is ignored when creating a ProductSet.
   *
   * @var string
   */
  public $indexTime;
  /**
   * The resource name of the ProductSet. Format is:
   * `projects/PROJECT_ID/locations/LOC_ID/productSets/PRODUCT_SET_ID`. This
   * field is ignored when creating a ProductSet.
   *
   * @var string
   */
  public $name;

  /**
   * The user-provided name for this ProductSet. Must not be empty. Must be at
   * most 4096 characters long.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. If there was an error with indexing the product set, the field
   * is populated. This field is ignored when creating a ProductSet.
   *
   * @param Status $indexError
   */
  public function setIndexError(Status $indexError)
  {
    $this->indexError = $indexError;
  }
  /**
   * @return Status
   */
  public function getIndexError()
  {
    return $this->indexError;
  }
  /**
   * Output only. The time at which this ProductSet was last indexed. Query
   * results will reflect all updates before this time. If this ProductSet has
   * never been indexed, this timestamp is the default value
   * "1970-01-01T00:00:00Z". This field is ignored when creating a ProductSet.
   *
   * @param string $indexTime
   */
  public function setIndexTime($indexTime)
  {
    $this->indexTime = $indexTime;
  }
  /**
   * @return string
   */
  public function getIndexTime()
  {
    return $this->indexTime;
  }
  /**
   * The resource name of the ProductSet. Format is:
   * `projects/PROJECT_ID/locations/LOC_ID/productSets/PRODUCT_SET_ID`. This
   * field is ignored when creating a ProductSet.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductSet::class, 'Google_Service_Vision_ProductSet');
