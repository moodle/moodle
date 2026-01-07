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

namespace Google\Service\TPU;

class GetGuestAttributesRequest extends \Google\Collection
{
  protected $collection_key = 'workerIds';
  /**
   * The guest attributes path to be queried.
   *
   * @var string
   */
  public $queryPath;
  /**
   * The 0-based worker ID. If it is empty, all workers' GuestAttributes will be
   * returned.
   *
   * @var string[]
   */
  public $workerIds;

  /**
   * The guest attributes path to be queried.
   *
   * @param string $queryPath
   */
  public function setQueryPath($queryPath)
  {
    $this->queryPath = $queryPath;
  }
  /**
   * @return string
   */
  public function getQueryPath()
  {
    return $this->queryPath;
  }
  /**
   * The 0-based worker ID. If it is empty, all workers' GuestAttributes will be
   * returned.
   *
   * @param string[] $workerIds
   */
  public function setWorkerIds($workerIds)
  {
    $this->workerIds = $workerIds;
  }
  /**
   * @return string[]
   */
  public function getWorkerIds()
  {
    return $this->workerIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetGuestAttributesRequest::class, 'Google_Service_TPU_GetGuestAttributesRequest');
