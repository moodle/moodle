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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV1ExposurePath extends \Google\Collection
{
  protected $collection_key = 'pathNodes';
  protected $edgesType = Edge::class;
  protected $edgesDataType = 'array';
  protected $exposedResourceType = GoogleCloudSecuritycenterV1ExposedResource::class;
  protected $exposedResourceDataType = '';
  /**
   * @var string
   */
  public $name;
  protected $pathNodesType = PathNode::class;
  protected $pathNodesDataType = 'array';

  /**
   * @param Edge[]
   */
  public function setEdges($edges)
  {
    $this->edges = $edges;
  }
  /**
   * @return Edge[]
   */
  public function getEdges()
  {
    return $this->edges;
  }
  /**
   * @param GoogleCloudSecuritycenterV1ExposedResource
   */
  public function setExposedResource(GoogleCloudSecuritycenterV1ExposedResource $exposedResource)
  {
    $this->exposedResource = $exposedResource;
  }
  /**
   * @return GoogleCloudSecuritycenterV1ExposedResource
   */
  public function getExposedResource()
  {
    return $this->exposedResource;
  }
  /**
   * @param string
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
  /**
   * @param PathNode[]
   */
  public function setPathNodes($pathNodes)
  {
    $this->pathNodes = $pathNodes;
  }
  /**
   * @return PathNode[]
   */
  public function getPathNodes()
  {
    return $this->pathNodes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV1ExposurePath::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1ExposurePath');
