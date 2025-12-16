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

class ListInstancesResponse extends \Google\Collection
{
  protected $collection_key = 'instances';
  /**
   * Locations from which Instance information could not be retrieved, due to an
   * outage or some other transient condition. Instances whose Clusters are all
   * in one of the failed locations may be missing from `instances`, and
   * Instances with at least one Cluster in a failed location may only have
   * partial information returned. Values are of the form `projects//locations/`
   *
   * @var string[]
   */
  public $failedLocations;
  protected $instancesType = Instance::class;
  protected $instancesDataType = 'array';
  /**
   * DEPRECATED: This field is unused and ignored.
   *
   * @deprecated
   * @var string
   */
  public $nextPageToken;

  /**
   * Locations from which Instance information could not be retrieved, due to an
   * outage or some other transient condition. Instances whose Clusters are all
   * in one of the failed locations may be missing from `instances`, and
   * Instances with at least one Cluster in a failed location may only have
   * partial information returned. Values are of the form `projects//locations/`
   *
   * @param string[] $failedLocations
   */
  public function setFailedLocations($failedLocations)
  {
    $this->failedLocations = $failedLocations;
  }
  /**
   * @return string[]
   */
  public function getFailedLocations()
  {
    return $this->failedLocations;
  }
  /**
   * The list of requested instances.
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
   * DEPRECATED: This field is unused and ignored.
   *
   * @deprecated
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListInstancesResponse::class, 'Google_Service_BigtableAdmin_ListInstancesResponse');
