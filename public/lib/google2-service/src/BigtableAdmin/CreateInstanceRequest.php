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

class CreateInstanceRequest extends \Google\Model
{
  protected $clustersType = Cluster::class;
  protected $clustersDataType = 'map';
  protected $instanceType = Instance::class;
  protected $instanceDataType = '';
  /**
   * Required. The ID to be used when referring to the new instance within its
   * project, e.g., just `myinstance` rather than
   * `projects/myproject/instances/myinstance`.
   *
   * @var string
   */
  public $instanceId;
  /**
   * Required. The unique name of the project in which to create the new
   * instance. Values are of the form `projects/{project}`.
   *
   * @var string
   */
  public $parent;

  /**
   * Required. The clusters to be created within the instance, mapped by desired
   * cluster ID, e.g., just `mycluster` rather than
   * `projects/myproject/instances/myinstance/clusters/mycluster`. Fields marked
   * `OutputOnly` must be left blank.
   *
   * @param Cluster[] $clusters
   */
  public function setClusters($clusters)
  {
    $this->clusters = $clusters;
  }
  /**
   * @return Cluster[]
   */
  public function getClusters()
  {
    return $this->clusters;
  }
  /**
   * Required. The instance to create. Fields marked `OutputOnly` must be left
   * blank.
   *
   * @param Instance $instance
   */
  public function setInstance(Instance $instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return Instance
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Required. The ID to be used when referring to the new instance within its
   * project, e.g., just `myinstance` rather than
   * `projects/myproject/instances/myinstance`.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * Required. The unique name of the project in which to create the new
   * instance. Values are of the form `projects/{project}`.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateInstanceRequest::class, 'Google_Service_BigtableAdmin_CreateInstanceRequest');
