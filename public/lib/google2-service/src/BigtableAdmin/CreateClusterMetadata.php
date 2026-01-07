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

namespace Google\Service\BigtableAdmin;

class CreateClusterMetadata extends \Google\Model
{
  /**
   * The time at which the operation failed or was completed successfully.
   *
   * @var string
   */
  public $finishTime;
  protected $originalRequestType = CreateClusterRequest::class;
  protected $originalRequestDataType = '';
  /**
   * The time at which the original request was received.
   *
   * @var string
   */
  public $requestTime;
  protected $tablesType = TableProgress::class;
  protected $tablesDataType = 'map';

  /**
   * The time at which the operation failed or was completed successfully.
   *
   * @param string $finishTime
   */
  public function setFinishTime($finishTime)
  {
    $this->finishTime = $finishTime;
  }
  /**
   * @return string
   */
  public function getFinishTime()
  {
    return $this->finishTime;
  }
  /**
   * The request that prompted the initiation of this CreateCluster operation.
   *
   * @param CreateClusterRequest $originalRequest
   */
  public function setOriginalRequest(CreateClusterRequest $originalRequest)
  {
    $this->originalRequest = $originalRequest;
  }
  /**
   * @return CreateClusterRequest
   */
  public function getOriginalRequest()
  {
    return $this->originalRequest;
  }
  /**
   * The time at which the original request was received.
   *
   * @param string $requestTime
   */
  public function setRequestTime($requestTime)
  {
    $this->requestTime = $requestTime;
  }
  /**
   * @return string
   */
  public function getRequestTime()
  {
    return $this->requestTime;
  }
  /**
   * Keys: the full `name` of each table that existed in the instance when
   * CreateCluster was first called, i.e. `projects//instances//tables/`. Any
   * table added to the instance by a later API call will be created in the new
   * cluster by that API call, not this one. Values: information on how much of
   * a table's data has been copied to the newly-created cluster so far.
   *
   * @param TableProgress[] $tables
   */
  public function setTables($tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return TableProgress[]
   */
  public function getTables()
  {
    return $this->tables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateClusterMetadata::class, 'Google_Service_BigtableAdmin_CreateClusterMetadata');
