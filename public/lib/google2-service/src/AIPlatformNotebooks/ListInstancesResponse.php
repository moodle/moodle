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

namespace Google\Service\AIPlatformNotebooks;

class ListInstancesResponse extends \Google\Collection
{
  protected $collection_key = 'unreachable';
  protected $instancesType = Instance::class;
  protected $instancesDataType = 'array';
  /**
   * Page token that can be used to continue listing from the last result in the
   * next list call.
   *
   * @var string
   */
  public $nextPageToken;
  /**
   * Locations that could not be reached. For example, ['us-west1-a', 'us-
   * central1-b']. A ListInstancesResponse will only contain either instances or
   * unreachables,
   *
   * @var string[]
   */
  public $unreachable;

  /**
   * A list of returned instances.
   *
   * @param Instance[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return Instance[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Page token that can be used to continue listing from the last result in the
   * next list call.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
  /**
   * Locations that could not be reached. For example, ['us-west1-a', 'us-
   * central1-b']. A ListInstancesResponse will only contain either instances or
   * unreachables,
   *
   * @param string[] $unreachable
   */
  public function setUnreachable($unreachable)
  {
    $this->unreachable = $unreachable;
  }
  /**
   * @return string[]
   */
  public function getUnreachable()
  {
    return $this->unreachable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListInstancesResponse::class, 'Google_Service_AIPlatformNotebooks_ListInstancesResponse');
