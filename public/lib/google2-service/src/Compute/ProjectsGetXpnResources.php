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

namespace Google\Service\Compute;

class ProjectsGetXpnResources extends \Google\Collection
{
  protected $collection_key = 'resources';
  /**
   * Output only. [Output Only] Type of resource.
   * Alwayscompute#projectsGetXpnResources for lists of service resources (a.k.a
   * service projects)
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. [Output Only] This token allows you to get the next page of
   * results for list requests. If the number of results is larger
   * thanmaxResults, use the nextPageToken as a value for the query parameter
   * pageToken in the next list request. Subsequent list requests will have
   * their own nextPageToken to continue paging through the results.
   *
   * @var string
   */
  public $nextPageToken;
  protected $resourcesType = XpnResourceId::class;
  protected $resourcesDataType = 'array';

  /**
   * Output only. [Output Only] Type of resource.
   * Alwayscompute#projectsGetXpnResources for lists of service resources (a.k.a
   * service projects)
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. [Output Only] This token allows you to get the next page of
   * results for list requests. If the number of results is larger
   * thanmaxResults, use the nextPageToken as a value for the query parameter
   * pageToken in the next list request. Subsequent list requests will have
   * their own nextPageToken to continue paging through the results.
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
   * Service resources (a.k.a service projects) attached to this project as
   * their shared VPC host.
   *
   * @param XpnResourceId[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return XpnResourceId[]
   */
  public function getResources()
  {
    return $this->resources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsGetXpnResources::class, 'Google_Service_Compute_ProjectsGetXpnResources');
