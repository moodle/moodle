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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ResourceStatus extends \Google\Collection
{
  protected $collection_key = 'revisions';
  /**
   * The resource name. Currently only two resources are supported:
   * EnvironmentGroup - organizations/{org}/envgroups/{envgroup}
   * EnvironmentConfig -
   * organizations/{org}/environments/{environment}/deployedConfig
   *
   * @var string
   */
  public $resource;
  protected $revisionsType = GoogleCloudApigeeV1RevisionStatus::class;
  protected $revisionsDataType = 'array';
  /**
   * The total number of replicas that should have this resource.
   *
   * @var int
   */
  public $totalReplicas;
  /**
   * The uid of the resource. In the unexpected case that the instance has
   * multiple uids for the same name, they should be reported under separate
   * ResourceStatuses.
   *
   * @var string
   */
  public $uid;

  /**
   * The resource name. Currently only two resources are supported:
   * EnvironmentGroup - organizations/{org}/envgroups/{envgroup}
   * EnvironmentConfig -
   * organizations/{org}/environments/{environment}/deployedConfig
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Revisions of the resource currently deployed in the instance.
   *
   * @param GoogleCloudApigeeV1RevisionStatus[] $revisions
   */
  public function setRevisions($revisions)
  {
    $this->revisions = $revisions;
  }
  /**
   * @return GoogleCloudApigeeV1RevisionStatus[]
   */
  public function getRevisions()
  {
    return $this->revisions;
  }
  /**
   * The total number of replicas that should have this resource.
   *
   * @param int $totalReplicas
   */
  public function setTotalReplicas($totalReplicas)
  {
    $this->totalReplicas = $totalReplicas;
  }
  /**
   * @return int
   */
  public function getTotalReplicas()
  {
    return $this->totalReplicas;
  }
  /**
   * The uid of the resource. In the unexpected case that the instance has
   * multiple uids for the same name, they should be reported under separate
   * ResourceStatuses.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ResourceStatus::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ResourceStatus');
