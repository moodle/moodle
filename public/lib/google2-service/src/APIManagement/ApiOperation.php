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

namespace Google\Service\APIManagement;

class ApiOperation extends \Google\Model
{
  /**
   * The number of occurrences of this API Operation.
   *
   * @var string
   */
  public $count;
  /**
   * First seen time stamp
   *
   * @var string
   */
  public $firstSeenTime;
  protected $httpOperationType = HttpOperation::class;
  protected $httpOperationDataType = '';
  /**
   * Last seen time stamp
   *
   * @var string
   */
  public $lastSeenTime;
  /**
   * Identifier. Name of resource
   *
   * @var string
   */
  public $name;

  /**
   * The number of occurrences of this API Operation.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * First seen time stamp
   *
   * @param string $firstSeenTime
   */
  public function setFirstSeenTime($firstSeenTime)
  {
    $this->firstSeenTime = $firstSeenTime;
  }
  /**
   * @return string
   */
  public function getFirstSeenTime()
  {
    return $this->firstSeenTime;
  }
  /**
   * An HTTP Operation.
   *
   * @param HttpOperation $httpOperation
   */
  public function setHttpOperation(HttpOperation $httpOperation)
  {
    $this->httpOperation = $httpOperation;
  }
  /**
   * @return HttpOperation
   */
  public function getHttpOperation()
  {
    return $this->httpOperation;
  }
  /**
   * Last seen time stamp
   *
   * @param string $lastSeenTime
   */
  public function setLastSeenTime($lastSeenTime)
  {
    $this->lastSeenTime = $lastSeenTime;
  }
  /**
   * @return string
   */
  public function getLastSeenTime()
  {
    return $this->lastSeenTime;
  }
  /**
   * Identifier. Name of resource
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
class_alias(ApiOperation::class, 'Google_Service_APIManagement_ApiOperation');
